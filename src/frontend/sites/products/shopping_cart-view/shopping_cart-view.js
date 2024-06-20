import {setTopNavBarShoppingCartCount} from "../../navigation/navbar/topNavBar_logic.js";

$(document).ready(function () {
    showShoppingCart();
});

function showShoppingCart() {
    // send request to backend to get current content of shopping cart and add items to the site's list
    $.ajax({
        type: "GET",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        data: {method: "getSessionShoppingCart", param: null},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! ShoppingCart - Response: " + response);
        if (response !== "no shoppingCart is set") {
            const positions = response["positions"];
            const totalPrice = response["totalPrice"];
            insertPositionsIntoList(positions);
            insertTotalPriceAndOrderButton(totalPrice);
        } else {
            $("#shoppingCartList > ").remove();
            $("#totalPrice").remove();
            $("#orderBtnContainer").remove();
            setTopNavBarShoppingCartCount();
        }
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}

function insertPositionsIntoList(positions) {

    $("#shoppingCartList > ").remove();

    $.each(positions, function( key, val ) {

        const positionId = key;
        const quantity = val["quantity"];
        const product = val["product"]

        const name = product["name"];
        const description = product["description"];
        const price_num = product["price"];
        const price= price_num.toString().replace(".", ",");
        const posPrice_num = price_num * quantity;
        const posPrice= posPrice_num.toFixed(2).toString().replace(".", ",");
        const imgPath = product["thumbnailPath"];

        $( "<li/>", {
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
                            src: "../../../../pictures/" + imgPath
                        })
                    ),
                    $( "<div/>", {
                        class: "col-sm"
                    }).append(
                        $( "<div/>", {
                            text: description
                        })
                    ),
                    $( "<div/>", {
                        class: "col-sm",

                    }).append(
                        $( "<p/>", {
                            text: "Preis pro Stück: " + price + " €"
                        }),
                        $("<label/>", {
                            htmlFor: "quantity" + positionId,
                            class: "form-label",
                            text: "Anzahl"
                        }),
                        $("<input/>", {
                            type: "number",
                            id: "quantity_" + positionId,
                            class: "form-control quantityInput",
                            name: "positionQuantity",
                            value: quantity,
                            min: 1,
                            max: 1000,
                            "data-positionId": positionId
                        }),
                        $( "<p/>", {
                            class: "productPrice",
                            text: "Preis gesamt: " + posPrice + " €"
                        }),
                        $( "<button/>", {
                            class: "btn btn-danger removeFromShoppingCart",
                            id: positionId,
                            text: "entfernen"
                        })
                    )
                )
            )
        ).appendTo("#shoppingCartList");
    })

    // event handler on change of quantity of any shopping cart position
    $(".quantityInput").on( "change", function() {
        const positionId = $(this).attr("data-positionId");
        const newQuantity = $(this).val();
        console.log("PositionId: " + positionId + ", Quantity: " + newQuantity);

        updateShoppingCart(positionId, newQuantity);
        setTopNavBarShoppingCartCount();
    });

    // event handler on clicking button for removing a shopping cart position
    $(".removeFromShoppingCart").on( "click", function() {
        console.log("adding to shopping cart link clicked");

        removePositionFromShoppingCart(this.id);
    });
}

function insertTotalPriceAndOrderButton(totalPrice) {

    $("#totalPrice").remove();

    totalPrice = totalPrice.toString().replace(".", ",");

    $( "<p/>", {
        class: "productPrice totalPrice text-end",
        id: "totalPrice",
        text: "Preis gesamt (TOTAL): " + totalPrice + " €"
    }).appendTo("#shoppingCartContainer");

    $("#orderBtnContainer").remove();

    $( "<div/>", {
        id: "orderBtnContainer",
        class: "d-flex align-items-end flex-column"
    }).append(
        $( "<button/>", {
            class: "btn btn-success orderButton mb-2",
            id: "orderBtn",
            "aria-describedby": "orderBtnHelp",
            text: "Bestellung absenden"
        }),
        $("<div/>", {
            id: "orderBtnHelp",
            class: "text-danger mb-3"
        })
    ).appendTo("#shoppingCartContainer");

    // event handler on clicking button for send order from shopping cart
    $("#orderBtn").on( "click", function() {
        console.log("send order from shopping cart button clicked");
        // check if an user is logged and if so send request to backend for saving the order to database
        orderIfUserIsLoggedIn()
    });
}

function updateShoppingCart(positionId, newQuantity) {

    // send request to backend to update the quantity of a shopping cart's position

    const parameter = {
        positionId: positionId,
        newQuantity: newQuantity
    }

    $.ajax({
        type: "PUT",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        data: {method: "updateShoppingCart", param: parameter},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! ShoppingCart - Response: " + response);
        if (response !== "no shoppingCart is set") {
            const positions = response["positions"];
            const totalPrice = response["totalPrice"];
            insertPositionsIntoList(positions);
            insertTotalPriceAndOrderButton(totalPrice);
            setTopNavBarShoppingCartCount();
        }
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}

function removePositionFromShoppingCart(positionId) {

    // send request to backend to delete a shopping cart's position

    $.ajax({
        type: "DELETE",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        data: {method: "removePositionFromShoppingCart", param: positionId},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! RemovePositionFromShoppingCart - Response: " + response);
        if (response !== "no shoppingCart is set") {
            const positions = response["positions"];
            const totalPrice = response["totalPrice"];
            insertPositionsIntoList(positions);
            insertTotalPriceAndOrderButton(totalPrice);
            setTopNavBarShoppingCartCount();
        }
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}

function orderIfUserIsLoggedIn() {

    // if no user is logged in then a message should be displayed that an order could only be sent if the user is logged in

    console.log("check if user is logged in");

    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "checkUserSession", param: null},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! UserSessionIsSet - Response: " + response);
        if (response === '1') {
            console.log("a user is already logged in!");
            orderFromShoppingCart()
        } else {
            $("#orderBtnHelp").html("ACHTUNG! Sie müssen eingeloggt sein, um eine Bestellung abzusenden!")
        }
    }).fail(function () {
        console.log("Request failed!");
    });
}

function orderFromShoppingCart() {

    // send request to backend to save shopping cart's order to database

    $.ajax({
        type: "GET",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        data: {method: "orderFromShoppingCart", param: null},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! Order From Shopping Cart - Response: " + response);
        showShoppingCart()
        showOrderConfirmation(response)
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}

function showOrderConfirmation(orderId) {

    // display a message of confirmation after the user clicked the order button in the shopping cart and the order has been saved to database

    $("#shoppingCartTitle").html("Ihr Auftrag mit der Bestellnummer " + orderId + " wurde erfolgreich abgesendet!");
    $("#shoppingCartTitle").attr("class", "text-danger orderConfirmationText");
}