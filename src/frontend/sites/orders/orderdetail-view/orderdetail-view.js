$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('orderId');

    const order = getOrder(orderId);

    // FixMe: remove this after implementation, also in html
    $("h1").text("Bestellung mit der Id: " + orderId);
});

function getOrder(orderId) {
    return {};

}
