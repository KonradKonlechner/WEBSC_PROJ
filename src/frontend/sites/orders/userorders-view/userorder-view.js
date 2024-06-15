import {translateState} from "../util/order-state-translator.js";

$(document).ready(function () {
    const orders = getUserOrders();

    const urlParams = new URLSearchParams(window.location.search);
    const myParam = urlParams.get('myParam');
    console.log("########################"+myParam);

    displayOrders(orders);
});


function getUserOrders() {
    let orders = [];

    $.ajax({
        type: "GET",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        async: false,
        data: {method: "getOrdersForUser", param: null},
        dataType: "json"
    }).done(function (response) {
        orders = response;
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
    return orders;
}

function displayOrders(orders) {
    if (orders == null || orders.length <=0) {
        $("<h2>", {
          text: "Sie haben noch keine Bestellungen bei uns getätigt."
        }).appendTo("#productContainer")
    }

    orders.forEach(order => {
        getOrderAppendable(order)
            .appendTo("#orders")
    })
}

function getOrderAppendable(order) {
    return $("<a>", {
        id: "order"+order.orderId,
        class: order,
        href: "../../orders/orderdetail-view/orderdetail-view.html?orderId="+order.orderId
    })
        .append(
            $("<div>", {
                id: "orderTimeStamp"+order.orderId,
                class: "orderTimeStamp",
                text: "Bestellund vom: "+order.createdAt
            }),
            $("<div>", {
                id: "orderState"+order.orderId,
                class: "orderState",
                text: "Status: "+translateState(order.state)
            }),
            $("<div>", {
                id: "orderPrice"+order.orderId,
                class: "orderTimeStamp",
                text: "Bestellwert: "+order.totalPrice + "€"
            }),
        )
}