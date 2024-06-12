import {appendProduct, getProductCategoryTranslation} from "./product_management-view.js";
import {updateProductData} from "./update-product-script.js";

export function appendEmptyProduct() {
    let product = {};
    product.id = "";
    product.name = "";
    product.description = "";
    product.category = "";
    product.price = 0;
    product.thumbnailPath = "";
    product.imagePath = "";

    appendProduct(product);
}

export function createProduct(product) {
    console.log("creating new product " + product.name);

    const createProduct = {
        id: product.id,
        name: product.name,
        description: product.description,
        category: product.category,
        price: product.price,
        imagePath: product.imagePath,
        thumbnailPath: product.thumbnailPath
    }

    $.ajax({
        type: "POST",
        url: "../../../../backend/product/controller/ProductController.php",
        cache: false,
        data: {method: "createProduct", param: createProduct},
        dataType: "json"
    }).done(function (response) {
        alert("Das Produkt " + response.name + " wurde erfolgreich angelegt!");
        product = response;
        setFieldsToUpdatedValues(product)
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}

function setFieldsToUpdatedValues(product) {
    $("#productNameHeader")
        .attr("id", "productNameHeader" + product.id)
        .text(product.name);
    $("#productCategoryHeader")
        .attr("id", "productCategoryHeader" + product.id)
        .text(getProductCategoryTranslation(product));
    $("#name")
        .attr("id", "name" + product.id);
    $("#description")
        .attr("id", "description" + product.id);
    $("#category")
        .attr("id", "category" + product.id);
    $("#price")
        .attr("id", "price" + product.id);

    $("#submit")
        .attr("id", "submit" + product.id)
        .off("click")
        .click(function () {
            product.name = $("#name" + product.id).val();
            product.description = $("#description" + product.id).val();
            product.category = $("#category" + product.id).val();
            product.price = $("#price" + product.id).val();
            updateProductData(product);
        })
        .text("Ändern");
}