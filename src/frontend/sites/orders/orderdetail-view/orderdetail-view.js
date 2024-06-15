$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('orderId');

    const order = getOrder(orderId);

    updateHead(order);
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
    });
    return order;
}

function updateHead(order) {
    // Hint: in a real world application, the id should never be used to display in the GUI, however for simplification reasons
    // we decided to use the id as a reference number
    $("h1").text("Bestellung mit Referenznummer: " + order.orderId);
}
