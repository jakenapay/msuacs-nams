
<!-- Begin Page Content -->
<div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
        <div class="col-lg">
            <h1 class="h3 mb-2 text-center"><?= $title; ?></h1>
        </div>
        </div>

        <div class="col-lg-5 offset-lg-4">
            <?= $this->session->flashdata('message'); ?>
        </div>

        <div class="row">
        <div class="col-sm-12 dashboard-btn" id="add-row">
                        <a id="add-btn" class="btn btn-sm btn-icon-split mb-4 shadow-sm">
                            <span class="icon text-white">
                            <i class="fas fa-plus-circle"></i>
                            </span>
                            <span class="text text-white d-none d-sm-block">Register New Guest</span>
                        </a>

                        <a href="<?= base_url('admin'); ?>" class="btn btn-secondary btn-sm btn-icon-split mb-4 float-right shadow-sm">
                                <span class="icon text-white">
                                <i class="fas fa-chevron-left"></i>
                                </span>
                                <span class="text">Back</span>
                        </a>
                </div>


            <div class="col-sm-12 col-md-12 col-lg-9">

            </div>
        </div>
        <!-- End of row show -->

        
        <div class="shadow mb-4 mt-2">
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table class="table table-striped" id="guestsTable" width="100%" cellspacing="0"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Export exel and PDF -->
<div id="modal-edit" class="iziModal"></div>
<div id="modal-delete" class="iziModal"></div>
<div id="modal-add" class="iziModal"></div>
<div id="modal-excel-import" class="iziModal"></div>

<!-- End of Main Content -->
<script>
    function loadWebCamera(){
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const constraints = { video: true };
        navigator.mediaDevices.getUserMedia(constraints)
            .then((stream) => {
                video.srcObject = stream;
                video.onloadedmetadata = () => {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                };
            })
            .catch((err) => {
                alert('Can\'t access camera');
                console.error('Error accessing the camera: ', err);
            });
    }

    function unloadWebCamera() {
        const video = document.getElementById('video');
        
        // Check if video.srcObject is set (i.e., the camera is currently loaded)
        if (video.srcObject) {
            // Get the media stream from the video element
            const stream = video.srcObject;
            
            // Stop all tracks of the stream
            const tracks = stream.getTracks();
            tracks.forEach(track => track.stop());
            
            // Clear the video source
            video.srcObject = null;
        }
    }


    function takeSnapshot() {
        document.getElementById('uploadBtn').style.display = 'none';
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const imageInput = document.getElementById('imageInput');
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(function(blob) {
            // Create a File object from the blob
            const imageFile = new File([blob], 'student_image.jpg', { type: 'image/jpeg' });
            
            // Create a DataTransfer object and add the file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(imageFile);
            
            // Set the files of the input element
            imageInput.files = dataTransfer.files;

            // Display the captured image
            const img = document.createElement('img');
            img.src = URL.createObjectURL(blob);
            img.id = 'image-result';
            img.style.width = '320px';
            img.style.height = '320px';
            img.style.borderRadius = '100%';
            img.style.margin = '10px';

            video.style.display = 'none';
            canvas.style.display = 'none';
            document.getElementById('capture-button').style.display = 'none';
            document.getElementById('reset-button').style.display = 'inline-block';

            video.parentNode.insertBefore(img, video);
        }, 'image/jpeg');
    }

    function removeSnapshot() {
        const currentUserImage = document.getElementById('currentImage');
        const video = document.getElementById('video');
        const imageResult = document.getElementById('image-result');
        const imageInput = document.getElementById('imageInput');
        const uploadedImage = document.getElementById('uploadImage');
        const uploadedImagePreview = document.getElementById('upload_image_preview');

        if (imageResult) {
            imageResult.remove();
        }
        if (currentUserImage) {
            currentUserImage.remove();
            currentUserImage.value = '';
            currentUserImage.style.display = 'none';
        }
        uploadedImage.value = '';
        uploadedImagePreview.style.display = 'none';
        video.style.display = 'block';
        document.getElementById('capture-button').style.display = 'inline-block';
        document.getElementById('uploadBtn').style.display = 'inline-block';
        document.getElementById('reset-button').style.display = 'none';
        imageInput.value = '';
    }

    function imagePreview() {
        const video = document.getElementById('video');
        const uploadButton = document.getElementById('uploadBtn');
        video.style.display = 'none';
        uploadButton.style.display = 'none';
        document.getElementById('capture-button').style.display = 'none';
        document.getElementById('reset-button').style.display = 'inline-block';

        // Get the selected file
        let input = this;
        let file = input.files[0];

        if (file) {
            // Read the file as a data URL
            let reader = new FileReader();
            reader.onload = function (e) {
                // Display the image preview
                document.getElementById('upload_preview').src = e.target.result;
                document.getElementById('upload_image_preview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }
/****************************FUNCTIONS FOR EDIT MODAL*********************************************** */

    function editLoadWebCamera(){
            const video = document.getElementById('edit-video');
            const canvas = document.getElementById('edit-canvas');
            const constraints = { video: true };
            navigator.mediaDevices.getUserMedia(constraints)
                .then((stream) => {
                    video.srcObject = stream;
                    video.onloadedmetadata = () => {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                    };
                })
                .catch((err) => {
                    alert('Can\'t access camera');
                    console.error('Error accessing the camera: ', err);
                });
        }


    function editUnloadWebCamera() {
            const video = document.getElementById('edit-video');
            
            // Check if video.srcObject is set (i.e., the camera is currently loaded)
            if (video.srcObject) {
                // Get the media stream from the video element
                const stream = video.srcObject;
                
                // Stop all tracks of the stream
                const tracks = stream.getTracks();
                tracks.forEach(track => track.stop());
                
                // Clear the video source
                video.srcObject = null;
            }
    }

    function editTakeSnapshot() {
        document.getElementById('edit-uploadBtn').style.display = 'none';
        const video = document.getElementById('edit-video');
        const canvas = document.getElementById('edit-canvas');
        const imageInput = document.getElementById('edit-imageInput');
        const editContext = canvas.getContext('2d');
        editContext.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(function(blob) {
            // Create a File object from the blob
            const editImageFile = new File([blob], 'student_image.jpg', { type: 'image/jpeg' });
            
            // Create a DataTransfer object and add the file
            const editDataTransfer = new DataTransfer();
            editDataTransfer.items.add(editImageFile);
            
            // Set the files of the input element
            imageInput.files = editDataTransfer.files;

            // Display the captured image
            const editImg = document.createElement('img');
            editImg.src = URL.createObjectURL(blob);
            editImg.id = 'edit-image-result';
            editImg.style.width = '320px';
            editImg.style.height = '320px';
            editImg.style.borderRadius = '100%';
            editImg.style.margin = '10px';

            video.style.display = 'none';
            canvas.style.display = 'none';
            document.getElementById('edit-capture-button').style.display = 'none';
            document.getElementById('edit-reset-button').style.display = 'inline-block';

            video.parentNode.insertBefore(editImg, video);
        }, 'image/jpeg');
    }

    function editRemoveSnapshot() {
        const currentUserImage = document.getElementById('currentImage');
        const video = document.getElementById('edit-video');
        const imageResult = document.getElementById('edit-image-result');
        const imageInput = document.getElementById('edit-imageInput');
        const uploadedImage = document.getElementById('edit-uploadImage');
        const uploadedImagePreview = document.getElementById('edit-upload_image_preview');

        if (imageResult) {
            imageResult.remove();
        }
        if (currentUserImage) {
            currentUserImage.remove();
            currentUserImage.value = '';
            currentUserImage.style.display = 'none';
        }
        uploadedImage.value = '';
        uploadedImagePreview.style.display = 'none';
        video.style.display = 'block';
        document.getElementById('edit-capture-button').style.display = 'inline-block';
        document.getElementById('edit-uploadBtn').style.display = 'inline-block';
        document.getElementById('edit-reset-button').style.display = 'none';
        imageInput.value = '';
    }

    function editImagePreview() {
        const video = document.getElementById('edit-video');
        const uploadButton = document.getElementById('edit-uploadBtn');
        video.style.display = 'none';
        uploadButton.style.display = 'none';
        document.getElementById('edit-capture-button').style.display = 'none';
        document.getElementById('edit-reset-button').style.display = 'inline-block';

        // Get the selected file
        let input2 = this;
        let file2 = input2.files[0];

        if (file2) {
            // Read the file as a data URL
            let reader2 = new FileReader();
            reader2.onload = function (e) {
                // Display the image preview
                document.getElementById('edit-upload_preview').src = e.target.result;
                document.getElementById('edit-upload_image_preview').style.display = 'block';
            };
            reader2.readAsDataURL(file2);
        }
    }

    function populateImagePreview(){
        let idFrontBase64 = document.getElementById('id_front_base64').value;
        if (idFrontBase64) {
            document.getElementById('id_front_preview').src = idFrontBase64;
        }
        let idBackBase64 = document.getElementById('id_back_base64').value;
        if (idBackBase64) {
            document.getElementById('id_back_preview').src = idBackBase64;
        }
    }

    // Function to handle the front image file preview and base64 encoding
    function encodeImageFileAsURLFront() {
        const element = document.getElementById('id_front');
        const hiddenInput = document.getElementById('id_front_base64');
        const preview = document.getElementById('id_front_preview');

        let file = element.files[0];
        if (file && file.type.match('image.*')) {
            let reader = new FileReader();
            reader.onloadend = function() {
                hiddenInput.value = reader.result;
                preview.src = reader.result;
            }
            reader.readAsDataURL(file);
        } else {
            alert("Only image files are allowed!");
            element.value = ""; // Clear the input if not an image
            hiddenInput.value = "";
            preview.src = "";
        }
    }

    // Function to handle the back image file preview and base64 encoding
    function encodeImageFileAsURLBack() {
        const element = document.getElementById('id_back');
        const hiddenInput = document.getElementById('id_back_base64');
        const preview = document.getElementById('id_back_preview');

        let file = element.files[0];
        if (file && file.type.match('image.*')) {
            let reader = new FileReader();
            reader.onloadend = function() {
                hiddenInput.value = reader.result;
                preview.src = reader.result;
            }
            reader.readAsDataURL(file);
        } else {
            alert("Only image files are allowed!");
            element.value = ""; // Clear the input if not an image
            hiddenInput.value = "";
            preview.src = "";
        }
    }

    function formatPhoneNumber() {
        let input = document.getElementById('phone_number');
        let phoneNumber = input.value.trim();
        // Remove any non-numeric characters except the plus sign
        phoneNumber = phoneNumber.replace(/[^0-9]/g, '');
        
        // If the phone number starts with '0', remove the leading zero
        if (phoneNumber.startsWith('0')) {
            phoneNumber = phoneNumber.substring(1);
        }

        // Set the formatted number back to the input value
        input.value = phoneNumber;
    }

    function formatEmergencyPhoneNumber() {
        let input = document.getElementById('emergency_contact_number');
        let phoneNumber = input.value.trim();
        // Remove any non-numeric characters except the plus sign
        phoneNumber = phoneNumber.replace(/[^0-9]/g, '');
        
        // If the phone number starts with '0', remove the leading zero
        if (phoneNumber.startsWith('0')) {
            phoneNumber = phoneNumber.substring(1);
        }

        // Set the formatted number back to the input value
        input.value = phoneNumber;
    }

$(document).ready(function(){
    $("#modal-add").iziModal({
        title: 'Register Guest',
        icon: "fas fa-fw fa-user",
        subtitle: 'Add New Guest',
        transitionIn: 'fadeInUp',
        transitionOut: 'fadeOutDown',
        width: 700,
        headerColor: "linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(94,4,4,1) 0%, rgba(94,4,4,0.6979166666666667) 96%)",
        fullscreen: false,
        onClosed: function() {
            unloadWebCamera(); 
            $("izimodal").iziModal("destroy");
            $("izimodal").remove();
        }
    });

    // Handle add button click
    $('#add-row').on('click', '#add-btn', function() {
        $.ajax({
            url: site_url + 'admin/user_management/guests/add/view',
            type: 'GET',
            success: function(response) {
                $("#modal-add").iziModal('setContent', response);
                $("#modal-add").iziModal('open');
                loadWebCamera();
                populateImagePreview();
            },
            error: function() {
                iziToast.error({
                    title: 'Error',
                    message: 'Failed to load the modal content.'
                });
            }
        });
    });

    $(document).on('click', '#capture-button', takeSnapshot);
    $(document).on('click', '#reset-button', removeSnapshot);
    $(document).on('change', '#uploadImage', imagePreview);
    // Handle file input change for front ID image
    $(document).on('change', '#id_front', encodeImageFileAsURLFront);
    $(document).on('change', '#id_back', encodeImageFileAsURLBack); // For back image 
    $(document).on('change', '#phone_number', formatPhoneNumber); 
    $(document).on('change', '#emergency_contact_number', formatEmergencyPhoneNumber);  

    // Add Button Handle form submission via AJAX
    $(document).on('submit', '#addGuestForm', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                const serverResponse = JSON.parse(response)

                if(serverResponse.status == 200){
                    success(serverResponse.message, "fa fa-check-circle")                        
                    $('#modal-add').iziModal('close');
                    $('#guestsTable').DataTable().ajax.reload();
                }
                else{
                    const errors = serverResponse.message;
                    console.log(errors);
                    
                    for (let key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            danger(errors[key], "fa fa-exclamation-triangle");
                        }
                        else{
                            danger(errors, "fa fa-exclamation-triangle")
                        }
                    }
                }
            },
            error: function() {
                iziToast.error({
                    title: 'Error',
                    message: 'An error occurred while submitting the form.',
                });
            }
        });
    });
});

$("#modal-excel-import").iziModal({
        title: 'Import New Others with Excel',
        subtitle: 'Add New Others Record via Import',
        icon: "fas fa-file-excel",
        headerColor: "linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(94,4,4,1) 0%, rgba(94,4,4,0.6979166666666667) 96%)",
        width: 1200,
        fullscreen: true,
        padding: 20,
        onOpening: function(modal){
            modal.startLoading();
            $.get(site_url + 'admin/user_management/others/excel_import_form', function(data) {
                $("#modal-excel-import").iziModal('setContent', data);
                modal.stopLoading();
            }).fail(function() {
                modal.stopLoading();
                iziToast.error({
                    title: 'Error',
                    message: 'Failed to load the Excel import form.'
                });
            });
        }
    });

    $('#excel-import-btn').on('click', function() {
        $("#modal-excel-import").iziModal('open');
    });

    // Handle form submission
    $(document).on('submit', '#excelImportForm', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const serverResponse = JSON.parse(response);
                if(serverResponse.status == 200){
                    success(serverResponse.message, "fa fa-check-circle");
                    $('#modal-excel-import').iziModal('close');
                    $('#contractorsTable').DataTable().ajax.reload();
                } else {
                    danger(serverResponse.message, "fa fa-exclamation-triangle");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                console.log("Response text:", xhr.responseText);
                iziToast.error({
                    title: 'Error',
                    message: 'An error occurred while uploading the file.',
                });
            }
        });
});
</script>