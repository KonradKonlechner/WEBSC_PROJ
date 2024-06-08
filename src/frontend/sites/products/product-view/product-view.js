
$(document).ready(function () {
    setProductList();

    $( "#productCategory" ).on( "change", function() {
        setProductList();
    } );
});

function setProductList() {
    console.log("getting products by category...");

    var productCategory = $("#productCategory").val();
    console.log("selected product category: " + productCategory);

    switch(productCategory) {
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
        $( "li" ).remove();
    });
}

function insertProductsIntoList(products) {

    $( "li" ).remove();

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
            $( "<h3/>", {
                text: name
            }),
            $( "<div/>", {
                class: "row align-items-center"
            }).append(
                $( "<div/>", {
                    class: "col-2"
                }).append(
                    $( "<img/>", {
                        src: "../../../../backend/product/pictures/" + imgPath
                    })
                ),
                $( "<div/>", {
                    class: "col-5"
                }).append(
                    $( "<div/>", {
                        text: description
                    })
                ),
                $( "<div/>", {
                    class: "col-2",

                }).append(
                    $( "<p/>", {
                        class: "productPrice",
                        id: prodId,
                        text: price + " €"
                    }),
                    $( "<a/>", {
                        class: "addToShoppingCartLink",
                        id: prodId,
                        text: "In den Warenkorb legen"
                    })
                )
            )
        ).appendTo("#productList" );
    })
}