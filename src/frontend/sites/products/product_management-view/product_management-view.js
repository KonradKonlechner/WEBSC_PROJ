import {saveImage} from "../util/image-handler.js";
import {updateProductData} from "./update-product-script.js";
import {deleteProduct} from "./delete-product-script.js";
import {appendEmptyProduct, createProduct} from "./create-product-script.js";

const imageStore = "../../../../pictures/"; // path to image storage

export function closeAllProductInfos() {
    $(".productInfo")
        .addClass("closed")
        .css("display", "none");
    $(".productClickBox")
        .removeClass("active");
}

export function appendProduct(product) {
    $("#products")
        .append(
            $("<button/>", {
                id: product.id,
                class: "productClickBox",
                type: "product"
            })
                .click(function () {
                    openProductInfo(product)
                })
                .append(
                    $("<button/>", {
                        id: "delete" + product.id,
                        class: "fa fa-trash deleteButton"
                    }),
                    $("<div/>", {
                        class: "headerText"
                    })
                        .append(
                            $("<div/>", {
                                name: product.name,
                                text: product.name,
                                class: "productNameHeader",
                                id: "productNameHeader" + product.id
                            }),
                            $("<div/>", {
                                name: "productCategory",
                                text: getProductCategoryTranslation(product),
                                class: "productCategoryHeader",
                                id: "productCategoryHeader" + product.id
                            })
                        ),
                    $("<img/>", {
                        id: "thumbnail" + product.id,
                        src: imageStore + product.thumbnailPath,
                        alt: product.name + " thumbnail",
                        class: "thumbnail"
                    })
                ),
            $("<div/>", {
                class: "productInfo",
                id: "productInfo" + product.id,
            }).append(getAppendableObjectsFor(product))
        );
    setImageHandler(product);
    setUpdateEventListener(product)
    setDeleteEventListener(product)
}

export function getProductCategoryTranslation(product) {
    switch (product.category) {
        case "food":
            return "Tiernahrung";
        case "toys":
            return "Spielzeug";
        case "accessories":
            return "Zubehör";
        default:
            return "";
    }
}

$(document).ready(function () {
    validateAdminAuthorization()
    getAllProducts();
    setAddButtonListener()
});


function setAddButtonListener() {
    $("#addProductButton").click(function () {
        closeAllProductInfos();
        appendEmptyProduct();
    })
}

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
            // redirect to welcome page if not admin
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

function setDeleteEventListener(product) {
    $("#delete" + product.id)
        .click(function () {
            deleteProduct(product)
        });
}

function setImageHandler(product) {
    $("#upload" + product.id)
        .click(function (event) {
            event.preventDefault();
            var fileInput = document.getElementById('fileInput' + product.id);
            var file = fileInput.files[0];
            var savedFileResponse = saveImage(file);

            const thumbnail = savedFileResponse.thumbnail;
            $("#thumbnail" + product.id).attr("src", imageStore + thumbnail);
            product.thumbnailPath = thumbnail;

            const image = savedFileResponse.image;
            $("#image" + product.id).attr("src", imageStore + image);
            product.imagePath = image;
        });
}

function openProductInfo(product) {
    const isAlreadyOpen = $("#productInfo" + product.id).hasClass("closed");
    closeAllProductInfos();
    if (isAlreadyOpen) {
        $("#productInfo" + product.id)
            .removeClass("closed")
            .css("display", "block");
        $("#" + product.id)
            .addClass("active");
    }
}

function setUpdateEventListener(product) {
    $("#submit" + product.id).click(function () {
        product.name = $("#name" + product.id).val();
        product.description = $("#description" + product.id).val();
        product.category = $("#category" + product.id).val();
        product.price = $("#price" + product.id).val();
        if (product.id === "") {
            console.log(product)
            createProduct(product);
        } else {
            updateProductData(product);
        }
    });
}


function getAppendableObjectsFor(product) {
    // Note: yes, this is ugly but its easy to write and gets the job done.
    return "<div class=\"mb-3\">\n" +
        "  <label for=\"name\" class=\"form-label\">Bezeichnung</label>\n" +
        "  <input type=\"text\" class=\"form-control\" name=\"name\" id=\"name" + product.id + "\" value=\"" + product.name + "\" required >\n" +
        "</div>" +

        "<div class=\"inlineProductInfo\">\n" +
        "  <div class=\"mb-3\">\n" +
        "    <label for=\"category\" class=\"form-label\">Kategorie</label>\n" +
        "    <select name=\"category\" id=\"category" + product.id + "\" class=\"form-select\">\n" +
        "      <option " + (product.category === "food" ? "selected" : "") + " value=\"food\">Tiernahrung</option>\n" +
        "      <option " + (product.category === "toys" ? "selected" : "") + " value=\"toys\">Spielzeug</option>\n" +
        "      <option " + (product.category === "accessories" ? "selected" : "") + " value=\"accessories\">Zubehör</option>\n" +
        "    </select>\n" +
        "  </div>\n" +
        "  <div class=\"mb-3\">\n" +
        "    <label for=\"price\" class=\"form-label\">Preis</label>\n" +
        "    <input type=\"number\" class=\"form-control\" name=\"price\" id=\"price" + product.id + "\" value=\"" + product.price + "\" required >\n" +
        "  </div>\n" +
        "</div>\n" +

        "<div class=\"mb-3\">\n" +
        "  <label for=\"description\" class=\"form-label\">Beschreibung</label>\n" +
        "  <textarea type=\"text\" class=\"form-control\" name=\"description\" id=\"description" + product.id + "\" required >" +
        product.description +
        "</textarea>\n" +
        "</div>\n" +

        "<div class=\"mb-3\">\n" +
        "  <label class=\"form-label\">Bild</label>\n" +
        "  <div>" +
        "    <img src=\"" + imageStore + product.imagePath + "\" class=\"productImage\" id=\"image" + product.id + "\" alt=\"Image of " + product.name + "\" >" +
        "    <input type=\"file\" id=\"fileInput" + product.id + "\" accept=\"image/*\">" +
        "    <input type=\"submit\" id=\"upload" + product.id + "\" value=\"Hochladen\" class =\"btn btn-primary\">" +
        "  </div>\n" +
        "</div>\n" +

        "<button class=\"btn btn-success\" type=\"submit\" name=\"submit\" id=\"submit" + product.id + "\">" +
        (product.id != null && product.id !== "" ? "Ändern" : "Anlegen") +
        "</button>\n"

}
