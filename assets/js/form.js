function encodeImageFileAsURL(element, hiddenInputId, previewId) {
    var file = element.files[0];
    if (file && file.type.match('image.*')) {
        var reader = new FileReader();
        reader.onloadend = function() {
            document.getElementById(hiddenInputId).value = reader.result;
            document.getElementById(previewId).src = reader.result;
        }
        reader.readAsDataURL(file);
    } else {
        alert("Only image files are allowed!");
        element.value = ""; // Clear the input if not an image
        document.getElementById(hiddenInputId).value = "";
        document.getElementById(previewId).src = "";
    }
}

// Populate the image previews if there are already base64 values
window.onload = function() {
    var idFrontBase64 = document.getElementById('id_front_base64').value;
    if (idFrontBase64) {
        document.getElementById('id_front_preview').src = idFrontBase64;
    }
    var idBackBase64 = document.getElementById('id_back_base64').value;
    if (idBackBase64) {
        document.getElementById('id_back_preview').src = idBackBase64;
    }
}
