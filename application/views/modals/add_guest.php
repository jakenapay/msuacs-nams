<style>
    body {}

    form {
        padding: 20px;
    }

    form label {
        font-size: .9rem;
    }

    input,
    .input-group-text,
    select {
        font-size: .9rem !important
    }

    img#user-image {
        border-radius: 100%;
        height: 200px;
        width: 200px;
    }

    .user-id img {
        width: 15rem;
        height: 12rem;

    }
</style>

<?= form_open('admin/user_management/guests/add/', ['id' => 'addGuestForm']) ?>
<div class="row mb-2">
    <div class="col-sm-12 text-center">
        <p>User Image</p>

    </div>
    <div class="col-sm-12 mb-2">
        <div class="video-wrapper d-flex justify-content-center">
            <video id="video" class="<?= form_error('image') ? 'form-error' : '' ?>" autoplay></video>
        </div>
        <div id="upload_image_preview" style="display: none; text-align: center; margin-y: 10px">
            <img id="upload_preview" class="rounded m-3" width="250" height="250">
        </div>
        <canvas id="canvas" style="display: none;"></canvas>
        <input type="file" name="image" id="imageInput" style="display: none;" accept="image/*">
        <div class="capture-buttons d-flex justify-content-center">
            <button type="button" id="capture-button" class="btn btn-sm btn-primary">Take Snapshot</button>
            <input type="file" class="form-control-file" name="uploadInput" id="uploadImage" style="display: none;"
                accept="image/*">
            <label for="uploadImage" title="Upload Image" id="uploadBtn"
                class="btn btn-sm btn-primary col-form-label ml-2">
                <span class="icon text-white-600">
                    <i class="fas fa-upload d-sm-none"></i></span>
                <span class="text d-none d-sm-block">Upload Image</span></label>
            <button type="button" id="reset-button" class="btn btn-sm btn-danger" style="display: none;">Reset</button>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="first_name">First Name <span class="text-danger">*</span></label>
        <input class="form-control" name="first_name" id="first_name" type="text">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="middle_name">Middle Name (Optional)</label>
        <input class="form-control" name="middle_name" id="middle_name" type="text">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="last_name">Last Name <span class="text-danger">*</span></label>
        <input class="form-control" name="last_name" id="last_name" type="text">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="phone_number">Phone Number <span class="text-danger">*</span></label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">+63</span>
            </div>
            <input type="text" name="phone_number" class="form-control" id="phone_number">
        </div>
    </div>
</div>
<!-- 
<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="company">Company <span class="text-danger">*</span></label>
        <input type="text" name="company" class="form-control">
    </div>
    <div class="col-md-6 mb-2">
        <label for="id_type">ID Type</label>
        <select name="id_type" id="id_type" class="form-control">
            <option value="">Select ID Type</option>
            <option value="National ID">National ID</option>
            <option value="ePhilID">Philippine Identification (PhilID / ePhilID)</option>
            <option value="SSS" >SSS / UMID</option>
            <option value="Passport" >Passport</option>
            <option value="Driver's License">Driver's License</option>
            <option value="PWD ID">PWD ID</option>
            <option value="Barangay ID">Barangay ID</option>
            <option value="Phil-health ID">Phil-health ID</option>
        </select>
    </div>
    <div class="col-md-6 mb-2">
        <label for="id_number">ID Number</label>
        <input type="text" name="id_number" class="form-control">
    </div>
</div> -->

<!-- 
<div class="row mb-2 user-id">
    <div class="col-sm-6 mb-2 text-center">
        <label for="id_front">ID (Front) <span class="text-danger">*</span></label>
        <div class="form-file id-back">
            <label for="id_front" class="id-label">Photo of Front ID
                <span class="id-upload-button">Choose File</span>
                <input type="file" name="id_front" class="id_input" id="id_front" accept="image/*">
                <input type="hidden" name="id_front_base64" id="id_front_base64" value="">
            </label>
            <img id="id_front_preview" class="preview-image id-fields" src="<?= base_url('assets/images/id-card-color-icon.png'); ?>">
        </div>
    </div>

    <div class="col-sm-6 mb-2 text-center">
        <label for="id_back">ID (Back) <span class="text-danger">*</span></label>
        <div class="form-file id-back">
        <label for="id_back" class="id-label">Photo of ID (back)
            <span class="id-upload-button">Choose File</span>
            <input type="file" class="id_input" name="id_back" id="id_back" accept="image/*">
            <input type="hidden" name="id_back_base64" id="id_back_base64" value="">
        </label>
        <img id="id_back_preview" class="preview-image id-fields" src="<?= base_url('assets/images/id-card-color-icon-back.png'); ?>">
        </div>
    </div>    
</div> 
-->

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="check_in_date">Check-In Date & Time <span class="text-danger">*</span></label>
        <input type="datetime-local" name="check_in_date" class="form-control">
    </div>
    <div class="col-md-6 mb-2">
        <label for="check_out_date">Estimated Check-Out Date <span class="text-danger">*</span></label>
        <input type="date" name="check_out_date" class="form-control">
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="stay_purpose">Purpose of Stay <span class="text-danger">*</span></label>
        <input type="text" name="stay_purpose" class="form-control">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="assigned_dormitory">Dormitory/Building Name <span class="text-danger">*</span></label>
        <input class="form-control" name="assigned_dormitory" id="assigned_dormitory" type="text">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="room_number">Room Number <span class="text-danger">*</span></label>
        <input class="form-control" name="room_number" id="room_number" type="text">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="emergency_contact_number">Emergency Phone Number <span class="text-danger">*</span></label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">+63</span>
            </div>
            <input type="text" name="emergency_contact_number" class="form-control" id="emergency_contact_number">
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="emergency_contact_person">Emergency Contact Person <span class="text-danger">*</span></label>
        <input class="form-control" name="emergency_contact_person" id="emergency_contact_person" type="text">
    </div>
    <div class="col-md-6 mb-2">
        <label for="rfid">RFID <span class="text-danger">*</span></label>
        <input class="form-control" name="rfid" id="rfid" type="text">
    </div>
</div>

<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
</div>
<?= form_close() ?>