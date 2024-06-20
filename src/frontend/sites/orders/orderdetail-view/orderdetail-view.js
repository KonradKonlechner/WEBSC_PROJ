import {translateState} from "../util/order-state-translator.js";
import {userIsAdmin} from "../util/user-state.js";

function init() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('orderId');
    const userId = urlParams.get('userId');

    const order = getOrder(orderId);
    if (order == null) {
        return;
    }

    updateHead(order);
    updateOrderInformation(order);
    if (userIsAdmin() && userId != null && userId !== "") {
        activateAdminMode(userId, order);
    }

    $("#showInvoiceBtn").on("click", function () {
        showInvoice(order);
    })
}

$(document).ready(function () {
    init();
});

function getOrder(orderId) {
    let order;

    const param = {orderId: orderId};

    $.ajax({
        type: "GET",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        async: false,
        data: {method: "getOrderByOrderId", param: param},
        dataType: "json"
    }).done(function (response) {
        order = response;
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
        return null;
    });
    return order;
}

function updateHead(order) {
    // Hint: in a real world application, the id should never be used to display in the GUI, however for simplification reasons
    // we decided to use the id as a reference number
    $("h1").text("Bestellung mit Bestellnummer: " + order.orderId);
}

function appendPosition(position, orderId) {
    const product = position.product;
    $( "<li/>", {
        id: "positionListItem"+position.positionId,
        class: "list-group-item"
    }).append(
        $( "<div/>", {
            class: "container text-center product-container"
        }).append(
            $( "<h3/>", {
                text: product.name
            }),
            $( "<div/>", {
                class: "row align-items-space-between"
            }).append(
                $( "<div/>", {
                    class: "col-sm"
                }).append(
                    $( "<img/>", {
                        src: "../../../../pictures/" + product.thumbnailPath
                    })
                ),
                $( "<div/>", {
                    class: "col-sm"
                }).append(
                    $( "<div/>", {
                        text: product.description
                    })
                ),
                $( "<div/>", {
                    class: "col-sm",

                }).append(
                    $( "<p/>", {
                        text: "Preis pro Stück: " + product.price + " €"
                    }),
                    $("<label/>", {
                        htmlFor: "productPrice" + position.positionId,
                        class: "form-label",
                        text: "Anzahl"
                    }),
                    $("<input/>", {
                        type: "number",
                        id: "positionQuantity" + position.positionId,
                        class: "form-control quantityInput",
                        name: "positionQuantity",
                        value: position.quantity,
                        min: 1,
                        max: 1000,
                        disabled: true,
                        "data-positionId": position.quantity
                    }).change(function () {updatePosition(position.positionId, product.price, orderId)}),
                    $( "<p/>", {
                        id: "productTotalPrice"+position.positionId,
                        class: "productPrice",
                        text: "Preis gesamt: " + (product.price * position.quantity)  + " €",
                        val: (product.price * position.quantity)
                    }),
                    $( "<button/>", {
                        class: "btn btn-danger removeButton",
                        id: "removeButton"+position.positionId,
                        text: "entfernen",
                        hidden: true
                    }).click(function () {removeItem(orderId, position.positionId)})
                )
            )
        )
    ).appendTo("#products");
}

function updateOrderInformation(order) {
    $("#orderStateTag")
        .text(translateState(order.state))
        .addClass(order.state)
    $("#orderTimeStamp")
        .val(order.createdAt)
    $("#totalPrice")
        .val(order.totalPrice)

    order.positions.forEach(position => {
        appendPosition(position, order.orderId);
    })
}

function updatePosition(positionId, price, orderId) {
    const positionQty = $("#positionQuantity" + positionId).val();

    // update position
    updatePositionQuantityForOrder(positionId, orderId, positionQty)

    // Update position price
    let totalPosPrice = (positionQty * price);
    $("#productTotalPrice"+positionId)
        .text(
            "Preis gesamt: " +
            totalPosPrice  +
            " €")
        .val(totalPosPrice)
    // update total price
    let totalPPP = 0;
    $(".productPrice").each(function() {
        totalPPP += Number($(this).val());
    })
    $("#totalPrice").val(
        totalPPP
    );
}

function activateAdminMode(userId, order) {
    // change backwards button
    $("#back-button-text").text("Zurück zu den Bestellungen");
    $("#userorder-view-button").click(function () {location.href="../../orders/userorders-view/userorder-view.html?userId="+userId})
    // display remove button
    $(".removeButton").attr("hidden", false);
    // enable quantity input
    $(".quantityInput").attr("disabled", false);
    // enable total order price --> not sure if this is a real use case
    $("#totalPrice").attr("disabled", false);
    // display state selection
    $("#orderStateSelector")
        .attr("hidden", false)
        .attr("disabled", false);
    $("option").each(function (option) {
        if (this.value === order.state) {
            this.selected = true;
        }
    })
    $("#orderStateTag")
        .attr("hidden", true);
    $("#orderState").change(function() {
        updateState();
    })
}

function updateState() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('orderId');
    const state = $("#orderState").val();

    const param = {
        state: state,
        orderId: orderId
    }

    $.ajax({
        type: "PUT",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        data: {method: "updateOrderState", param: param},
        dataType: "json"
    }).done(function () {
        console.log("Updating order state was successful");
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
        return null;
    });
}

function removeItem(orderId, positionId) {
    deletePositionFromOrder(orderId, positionId);
    $("#positionListItem"+positionId).remove();
}

function deletePositionFromOrder(orderId, positionId) {
    const param = {
        positionId: positionId,
        orderId: orderId
    }

    $.ajax({
        type: "DELETE",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        data: {method: "deleteOrderPosition", param: param},
        dataType: "json"
    }).done(function () {
        console.log("deleting order position was successful");
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
        return null;
    });
}

function showInvoice(order) {

    const orderId = order.orderId;
    const invoiceId = getInvoiceIdByOrderId(orderId);
    const invoiceDate = new Date().toLocaleDateString();
    const userId = order.userId;
    const user = getUserById(userId);
    const userSex = user.sex;
    const userName = user.name;
    const userLastname = user.lastname;
    const userAddress = user.address;
    const userCity = user.city;
    const positions = order.positions;
    const orderTotalPrice = order.totalPrice.toString().replace(".", ",");

    let invoiceWindow = window.open();
    invoiceWindow.document.title = "Rechnung";

    const header = document.createElement("div");
    header.appendChild(document.createTextNode("PawsomeMart GmbH, Höchstädtplatz 6, 1200 Wien | Tel.: 01 33340770 | E-Mail: office@pawsomemart.com"));
    header.setAttribute("style", "border-bottom: 2px solid black;")
    invoiceWindow.document.body.appendChild(header);

    const invoiceTitle = document.createElement("h1");
    invoiceTitle.appendChild(document.createTextNode("RECHNUNG Nr.: " + invoiceId));

    invoiceWindow.document.body.appendChild(invoiceTitle);

    const customer = document.createElement("div");
    const customerName = document.createElement("p");
    customerName.appendChild(document.createTextNode(userSex + " " + userName + " " + userLastname));
    const customerAddress = document.createElement("p");
    customerAddress.appendChild(document.createTextNode(userAddress + ", " + userCity));

    customer.appendChild(customerName);
    customer.appendChild(customerAddress);

    invoiceWindow.document.body.appendChild(customer);

    const invoiceDateInfo = document.createElement("div");
    invoiceDateInfo.appendChild(document.createTextNode("Rechnungsdatum: " + invoiceDate));
    invoiceDateInfo.setAttribute("style", "border-top: 1px solid black; border-bottom: 1px solid black;")
    invoiceWindow.document.body.appendChild(invoiceDateInfo);

    const invoiceOrderInfo = document.createElement("p");
    invoiceOrderInfo.appendChild(document.createTextNode("Bestellnr.: " + orderId));
    invoiceWindow.document.body.appendChild(invoiceOrderInfo);

    const invoiceInfoText = document.createElement("p");
    invoiceInfoText.appendChild(document.createTextNode("Vielen Dank für Ihre Bestellung! Gemäß Ihres Auftrags berechnen wir Ihnen folgende Artikel:"));
    invoiceWindow.document.body.appendChild(invoiceInfoText);

    const orderItemsTable = document.createElement("table");

    const columnHeaderRow = document.createElement("tr");
    const columnheader1 = document.createElement("th");
    columnheader1.appendChild(document.createTextNode("Bezeichung"));
    columnHeaderRow.appendChild(columnheader1);
    const columnheader2 = document.createElement("th");
    columnheader2.appendChild(document.createTextNode("Artikel-Nr."));
    columnHeaderRow.appendChild(columnheader2);
    const columnheader3 = document.createElement("th");
    columnheader3.appendChild(document.createTextNode("Menge"));
    columnHeaderRow.appendChild(columnheader3);
    const columnheader4 = document.createElement("th");
    columnheader4.appendChild(document.createTextNode("Einzelpreis"));
    columnHeaderRow.appendChild(columnheader4);
    const columnheader5 = document.createElement("th");
    columnheader5.appendChild(document.createTextNode("Gesamtpreis"));
    columnHeaderRow.appendChild(columnheader5);
    columnHeaderRow.setAttribute("style", "border-bottom: 1px solid black;");
    orderItemsTable.appendChild(columnHeaderRow);

    $.each( positions, function( key, val ) {

        const productName = val["product"]["name"];
        const productId = val["product"]["id"];
        const productPrice_num = val["product"]["price"];
        const productPrice = productPrice_num.toString().replace(".", ",");
        const quantity = val["quantity"];
        const positionPrice_num = productPrice_num * quantity;
        const positionPrice = positionPrice_num.toFixed(2).toString().replace(".", ",");

        const positionRow = document.createElement("tr");
        const positionProductName = document.createElement("td");
        positionProductName.appendChild(document.createTextNode(productName));
        positionRow.appendChild(positionProductName);
        const positionProductId = document.createElement("td");
        positionProductId.appendChild(document.createTextNode(productId));
        positionRow.appendChild(positionProductId);
        const positionQuantity = document.createElement("td");
        positionQuantity.appendChild(document.createTextNode(quantity));
        positionRow.appendChild(positionQuantity);
        const positionSinglePrice = document.createElement("td");
        positionSinglePrice.appendChild(document.createTextNode(productPrice + " EUR"));
        positionRow.appendChild(positionSinglePrice);
        const positionTotalPrice = document.createElement("td");
        positionTotalPrice.appendChild(document.createTextNode(positionPrice + " EUR"));
        positionRow.appendChild(positionTotalPrice);
        orderItemsTable.appendChild(positionRow);

    });

    const positionRow = document.createElement("tr");
    positionRow.appendChild(document.createElement("td"));
    positionRow.appendChild(document.createElement("td"));
    positionRow.appendChild(document.createElement("td"));
    positionRow.appendChild(document.createElement("td"));

    const overallTotalPrice = document.createElement("td");
    const overallTotalPriceLabel = document.createElement("div");
    overallTotalPriceLabel.appendChild(document.createTextNode("Rechnungsendbetrag"));
    overallTotalPriceLabel.setAttribute("style", "font-weight: bold;");
    overallTotalPrice.appendChild(overallTotalPriceLabel);
    const overallTotalPriceValue = document.createElement("div")
    overallTotalPriceValue.appendChild(document.createTextNode(orderTotalPrice + " EUR"));
    overallTotalPriceValue.setAttribute("style", "font-size: 30px;");
    overallTotalPrice.appendChild(overallTotalPriceValue);
    positionRow.appendChild(overallTotalPrice);
    orderItemsTable.appendChild(positionRow);

    invoiceWindow.document.body.appendChild(orderItemsTable);

    const paymentInfo = document.createElement("p");
    paymentInfo.appendChild(document.createTextNode("Wir bitten um Zahlung des Rechnungsbetrages per Banküberweisung innerhalb von 14 Tagen ab Erhalt der Ware."));
    invoiceWindow.document.body.appendChild(paymentInfo);

    const footer = document.createElement("div");
    footer.appendChild(document.createTextNode("Geschäftsführer: Le Chef Big-Boss | UID-Nummer: ATU123456789 | Bankverbindung: Erste Bank Wien, BIC: SPSPAT11XXX, IBAN: AT01 1111 2222 3333 4567"));
    footer.setAttribute("style", "border-top: 2px solid black;")
    invoiceWindow.document.body.appendChild(footer);

}

function getUserById(userId) {
    let user = null;

    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        async: false,
        data: {method: "getUserDataById", param: userId},
        dataType: "json"
    }).done(function (response) {
        user = response;
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
        return null;
    });

    return user;
}

function getInvoiceIdByOrderId(orderId) {
    console.log("getting invoice id by order id...")
    let invoiceId = null;

    $.ajax({
        type: "POST",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        async: false,
        data: {method: "getInvoiceIdByOrderId", param: orderId},
        dataType: "json"
    }).done(function (response) {
        invoiceId = response;
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
        return null;
    });

    return invoiceId;
}


function updatePositionQuantityForOrder(positionId, orderId, positionQty) {
    const param = {
        positionId: positionId,
        orderId: orderId,
        positionQty: positionQty
    }

    $.ajax({
        type: "PUT",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        data: {method: "updateOrderQty", param: param},
        dataType: "json"
    }).done(function (response) {
        console.log("Updating order was successful");
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
        return null;
    });
}
