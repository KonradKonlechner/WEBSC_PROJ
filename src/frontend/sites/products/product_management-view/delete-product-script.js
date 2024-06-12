import {closeAllProductInfos} from "./product_management-view.js";

function removeProductFields(product) {
    $("#"+product.id).remove();
}

export function deleteProduct(product) {
    const userShouldBeUpdated = confirm(
        "Sie sind dabei das Produkt "
        + product.name
        + " dauerhaft und unwiderruflich zu löschen.\nMöchten Sie fortfahren?"
    );

    if (!userShouldBeUpdated) {
        return;
    }

    const productId = {"productId": product.id};
    $.ajax({
        type: "DELETE",
        url: "../../../../backend/product/controller/ProductController.php",
        cache: false,
        data: {method: "deleteProduct", param: productId},
        dataType: "json"
    }).done(function (response) {
        if (response) {
            alert("Das Produkt " + product.name + " wurde erfolgreich gelöscht!");
            removeProductFields(product);
            closeAllProductInfos();
        } else {
            alert("Hoppala! Es kam beim Löschen zu einem Fehler! Bitte refreshen Sie die Seite und versuchen Sie es später nocheinmal.")
        }
    }).fail(function () {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}