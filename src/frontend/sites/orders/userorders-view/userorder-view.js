import {translateState} from "../util/order-state-translator.js";
import {userIsAdmin} from "../util/user-state.js";

$(document).ready(function () {
    const orders = getUserOrders();

    displayOrders(orders);
});


function getUserOrders() {
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('userId');

    let orders = [];

    if (userIsAdmin() && userId != null) {
        const user = getUserInfo(userId);
        if (user == null || user.id == null) {
            $("h1").text("Benutzer mit der Id "+ userId + " existiert nicht.");
            return;
        }
        $("h1").text("Bestellungen von "+ user.name)
        $.ajax({
            type: "GET",
            url: "../../../../backend/order/controller/OrderController.php",
            cache: false,
            async: false,
            data: {method: "getOrdersForUserId", param: {userId: userId}},
            dataType: "json"
        }).done(function (response) {
            if(response != "no orders available") {
                orders = response;
            }
        }).fail(function () {
            console.log("Request failed!");
            alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
                "Bitte probieren Sie es später erneut.");
        });

    } else {
        $.ajax({
            type: "GET",
            url: "../../../../backend/order/controller/OrderController.php",
            cache: false,
            async: false,
            data: {method: "getOrdersForUser", param: null},
            dataType: "json"
        }).done(function (response) {
            if(response != "no orders available") {
                orders = response;
            }
        }).fail(function () {
            console.log("Request failed!");
            alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
                "Bitte probieren Sie es später erneut.");
        });
    }

    return orders;
}

function getUserInfo(userId) {
    let user;
    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        async: false,
        data: {method: "getUserDataById", param: {userId: userId}},
        dataType: "json"
    }).done(function (response) {
        user = response;
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
    return user;
}

function displayOrders(orders) {
    if (orders == null || orders.length <=0) {
        $("<h2>", {
          text: "Es wurden noch keine Bestellungen bei uns getätigt."
        }).appendTo("#productContainer")
    }

    orders.forEach(order => {
        getOrderAppendable(order)
            .appendTo("#orders")
    })
}

function getOrderAppendable(order) {
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('userId');

    return $("<a>", {
        id: "order"+order.orderId,
        class: order,
        href: "../../orders/orderdetail-view/orderdetail-view.html?orderId="+order.orderId + (userId == null ? "" : ("&userId="+userId))
    })
        .append(
            $("<div>", {
                id: "orderTimeStamp"+order.orderId,
                class: "orderData orderTimeStamp",
                text: "Bestellung vom "+order.createdAt
            }),
            $("<div>", {
                id: "orderRefNo"+order.orderId,
                class: "orderData orderRefNo",
                text: "Bestellnummer: "+order.orderId // Hint: in a real world application, this would never be actual id, but for simplification we used it here
            }),
            $("<div>", {
                id: "orderState"+order.orderId,
                class: "orderData orderState",
                text: "Status: "+translateState(order.state)
            }),
            $("<div>", {
                id: "orderPrice"+order.orderId,
                class: "orderData orderTimeStamp",
                text: "Bestellwert: "+order.totalPrice + "€"
            }),
        )
}