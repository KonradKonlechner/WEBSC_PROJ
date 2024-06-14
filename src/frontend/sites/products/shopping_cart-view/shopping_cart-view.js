import {setTopNavBarShoppingCartCount} from "../../navigation/navbar/topNavBar_logic.js";

$(document).ready(function () {
    showShoppingCart();
});

function showShoppingCart() {

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

    $(".quantityInput").on( "change", function() {
        const positionId = $(this).attr("data-positionId");
        const newQuantity = $(this).val();
        console.log("PositionId: " + positionId + ", Quantity: " + newQuantity);

        updateShoppingCart(positionId, newQuantity);
        setTopNavBarShoppingCartCount();
    });

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


    $("#orderBtn").on( "click", function() {
        console.log("send order from shopping cart button clicked");

        orderIfUserIsLoggedIn()
    });
}

function updateShoppingCart(positionId, newQuantity) {

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

    $.ajax({
        type: "GET",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        data: {method: "orderFromShoppingCart", param: null},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! Order From Shopping Cart - Response: " + response);
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}



