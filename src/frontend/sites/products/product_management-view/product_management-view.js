$(document).ready(function () {
    validateAdminAuthorization()
    getAllProducts();
});

function validateAdminAuthorization() {
    $.ajax({
        type: "GET",
        url: "../../../../backend/user/controller/UserController.php",
        cache: false,
        data: {method: "checkUserIsAdmin", param: null},
        dataType: "json"
    }).done(function (response) {
        console.log("Request succeeded! IsAdmin - Response: " + response);
        if (response !== '1') {
            // redirect to welcome page
            window.location.href = "../../welcome/welcome-view/welcome-view.html"
        }
    }).fail(function () {
        console.log("Request failed!");
        alert("Adminauthentifizierung konnte nicht durchgeführt werden.\nBitte versuchen Sie es später noch einmal")
        // redirect to welcome page
        window.location.href = "../../welcome/welcome-view/welcome-view.html"
    });
}

function getAllProducts() {
    $.ajax({
        type: "GET",
        url: "../../../../backend/product/controller/ProductController.php",
        cache: false,
        data: {method: "getAllProducts", param: null},
        dataType: "json"
    }).done(function (response) {
        appendProductsToList(response);
    }).fail(function () {
        console.log("Request failed!");
    });
}

function appendProductsToList(response) {
    response.forEach(function (product) {
        appendProduct(product)
    });
    closeAllProductInfos();
}

function appendProduct(product) {
    $("#products")
        .append(
            $("<button/>", {
                id: product.id,
                class: "productClickBox",
                type: "product"
            })
                .click(function (e) {
                    openProductInfo(product)
                })
                .append(
                    $("<div/>", {
                        name: product.name,
                        text: product.name,
                        class: "productNameHeader"
                    }),
                    $("<img/>", { // <img src="../../../res/images/paw_print.png" alt="paw print">
                        id: "thumbnail" + product.id,
                        src: "../../../../backend/product/pictures/" + product.thumbnailPath,
                        alt: product.name + " thumbnail",
                        class: "thumbnail"
                    })
                ),
            $("<div/>", {
                class: "productInfo",
                id: "productInfo" + product.id,
            }).append(getAppendableObjectsFor(product))
        );
    setUpdateEventListener(product)
}

function openProductInfo(product) {
    const isAlreadyOpen = $("#productInfo" + product.id).hasClass("closed");
    closeAllProductInfos();
    if (isAlreadyOpen) {
        $("#productInfo" + product.id)
            .removeClass("closed")
            .css("display", "block");
        $("#"+product.id)
            .addClass("active");
    }
}

function closeAllProductInfos() {
    $(".productInfo")
        .addClass("closed")
        .css("display", "none");
    $(".productClickBox")
        .removeClass("active");
}

function getAppendableObjectsFor(product) {
    console.log("setting up appendages for " + product.id);
}
function setUpdateEventListener(product) {
    console.log("setting up event listener for " + product.id);
}