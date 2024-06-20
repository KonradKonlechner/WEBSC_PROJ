import {getProductCategoryTranslation} from "./product_management-view.js";

export function updateProductData(product) {
    const userShouldBeUpdated = confirm(
        "Sie sind dabei die Daten des Produkts "
        + product.name
        + " zu ändern.\nMöchten Sie fortfahren?"
    );

    if (!userShouldBeUpdated) {
        return;
    }

    console.log("Attempting to update product " + product.name);

    const updateProduct = {
        id: product.id,
        name: product.name,
        description: product.description,
        category: product.category,
        price: product.price,
        imagePath: product.imagePath,
        thumbnailPath: product.thumbnailPath
    }

    $.ajax({
        type: "PUT",
        url: "../../../../backend/product/controller/ProductController.php",
        cache: false,
        data: {method: "updateProduct", param: updateProduct},
        dataType: "json",
        async: false
    }).done(function (response) {
        alert("Das Produkt " + response.name + " wurde erfolgreich geändert!");
        setFieldsToUpdatedValues(response)
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}

function setFieldsToUpdatedValues(product) {
    $("#productNameHeader"+ product.id)
        .text(product.name);
    $("#productCategoryHeader"+ product.id)
        .text(getProductCategoryTranslation(product));
}