
function getImage(imagePath) {
    // TODo
}

export function saveImage(image) {
    var formData = new FormData();
    formData.append('image', image);
    formData.append('method', 'upload');
    formData.append('param', 'image');

    $.ajax({
        type: "POST",
        url: "../../../../backend/product/controller/ImageController.php",
        cache: false,
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json"
    }).done(function(response) {
        $('#result').text('Image uploaded successfully');
    }).fail(function() {
        $('#result').text('Image upload failed');
        console.log("Request failed!");
    });
}