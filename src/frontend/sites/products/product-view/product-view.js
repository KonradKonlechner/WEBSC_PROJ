import {setTopNavBarShoppingCartCount} from "../../navigation/navbar/topNavBar_logic.js";

$(document).ready(function () {

    // show filtered product list based on selection of product category and search term input
    filterProductList();

    // event handlers to trigger filtering of product list
    $("#productCategory").on( "change", function() {
        filterProductList();
    });

    $("#productSearch").on( "keyup", function() {
        filterProductList();
    });
});

function filterProductList() {
    if($("#productSearch").val() === "") {
        filterProductListByCategory();
    } else {
        filterProductListBySearchInput();
    }
}

function filterProductListByCategory() {
    console.log("getting products by category...");

    var productCategory = $("#productCategory").val();
    console.log("selected product category: " + productCategory);

    switch(productCategory) {
        case "Alle":
            insertAllProducts();
            return;
        case "Tiernahrung":
            productCategory = "food";
            break;
        case "Spielzeug":
            productCategory = "toys";
            break;
        case "Zubehör":
            productCategory = "accessories";
            break;
        default:
            productCategory = null;
    }

    insertAllProductsOfCategory(productCategory)
}

function insertAllProductsOfCategory(productCategory) {

    // send request to backend to get filtered list of products from database based on selection of product category

    $.ajax({
        type: "GET",
        url: "../../../../backend/product/controller/ProductController.php",
        cache: false,
        data: {method: "getAllProductsOfCategory", param: productCategory},
        dataType: "json"
    }).done(function(response) {
        //console.log("Request succeeded! Response: " + response);
        insertProductsIntoList(response)
    }).fail(function() {
        console.log("Request failed!");
        $( "#productList > li" ).remove();
    });
}

function insertAllProducts() {

    // get list of all products if no filter is set

    $.ajax({
        type: "GET",
        url: "../../../../backend/product/controller/ProductController.php",
        cache: false,
        data: {method: "getAllProducts", param: null},
        dataType: "json"
    }).done(function(response) {
        //console.log("Request succeeded! Response: " + response);
        insertProductsIntoList(response)
    }).fail(function() {
        console.log("Request failed!");
        $( "#productList > li" ).remove();
    });
}

function filterProductListBySearchInput() {
    console.log("getting products filtered by search input...");

    var searchTerm = $("#productSearch").val();
    console.log("searching for products by term: " + searchTerm);

    var productCategory = $("#productCategory").val();

    switch(productCategory) {
        case "Alle":
            productCategory = "all";
            break;
        case "Tiernahrung":
            productCategory = "food";
            break;
        case "Spielzeug":
            productCategory = "toys";
            break;
        case "Zubehör":
            productCategory = "accessories";
            break;
        default:
            productCategory = null;
    }

    const searchParameter = {
        searchTerm: searchTerm,
        productCategory: productCategory
    }

    if(searchTerm !== "") {
        $.ajax({
            type: "GET",
            url: "../../../../backend/product/controller/ProductController.php",
            cache: false,
            data: {method: "getAllProductsFilteredBySearchTermAndCategory", param: searchParameter},
            dataType: "json"
        }).done(function (response) {
            //console.log("Request succeeded! Response: " + response);
            insertProductsIntoList(response)
        }).fail(function () {
            console.log("Request failed!");
            $("#productList > li").remove();
        });
    } else {
        filterProductListByCategory();
    }
}

function insertProductsIntoList(products) {

    $( "#productList > " ).remove();

    $.each(products, function( key, val ) {

        const prodId = val["id"];
        const name = val["name"];
        const description = val["description"];
        const price = val["price"].toString().replace(".", ",");
        const imgPath = val["thumbnailPath"];

        //console.log(name);

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
                            class: "productPrice",
                            text: price + " €"
                        }),
                        $( "<a/>", {
                            class: "addToShoppingCartLink",
                            id: prodId,
                            text: "In den Warenkorb legen"
                        })
                    )
                )
            )
        ).appendTo("#productList" );
    })

    $(".addToShoppingCartLink").on( "click", function() {
        console.log("adding to shopping cart link clicked");

        addProductToShoppingCart(this.id);
    });
}

function addProductToShoppingCart(productId) {

    console.log("adding to shopping cart product with id=" + productId);

    $.ajax({
        type: "POST",
        url: "../../../../backend/order/controller/OrderController.php",
        cache: false,
        data: {method: "addProductToShoppingCart", param: productId},
        dataType: "json"
    }).done(function(response) {
        console.log("Request succeeded! Response: " + response);
        setTopNavBarShoppingCartCount()
    }).fail(function() {
        console.log("Request failed!");
        alert("Es tut uns Leid, auf unserer Seite scheint es zu einem Fehler gekommen zu sein. " +
            "Bitte probieren Sie es später erneut.");
    });
}