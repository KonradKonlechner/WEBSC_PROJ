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
        activateAdminMode(userId);
    }
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
            class: "container text-center"
        }).append(
            $( "<h3/>", {
                text: name
            }),
            $( "<div/>", {
                class: "row align-items-center"
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

function activateAdminMode(userId) {
    // change backwards button
    $("#back-button-text").text("Zurück zu den Bestellungen");
    $("#userorder-view-button").click(function () {location.href="../../orders/userorders-view/userorder-view.html?userId="+userId})
    // display remove button
    $(".removeButton").attr("hidden", false);
    // enable quantity input
    $(".quantityInput").attr("disabled", false);
    // enable total order price --> not sure if this is a real use case
    $("#totalPrice").attr("disabled", false);
}

function removeItem(orderId, positionId) {
    deletePositionFromOrder(orderId);
    $("#positionListItem"+positionId).remove();
}

function deletePositionFromOrder(orderId) {
    // ToDO:
}

function updatePositionQuantityForOrder(positionId, orderId, positionQty) {
    // ToDo
}
