export function saveImage(image) {
    var formData = new FormData();
    formData.append('image', image);
    formData.append('method', 'upload');
    formData.append('param', 'image');

    var result;
    $.ajax({
        type: "POST",
        url: "../../../../backend/product/controller/ImageController.php",
        cache: false,
        data: formData,
        async: false,
        processData: false,
        contentType: false,
        dataType: "json"
    }).done(function(response) {
        result = response;
        alert("Bild \""+image.name+"\" erfolgreich hochgeladen!");
    }).fail(function() {
        $('#result').text('Image upload failed');
        console.log("Request failed!");
    });
    return result;
}