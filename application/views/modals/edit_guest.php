<style>
    body{
    }
    
    form{
        padding: 20px;
    }

    form label{
        font-size: .9rem;
    }
    
    input, .input-group-text, select{
        font-size: .9rem !important
    }

    img#user-image{
        border-radius: 100%;
        height: 200px;
        width: 200px;
    }

    .user-id img{
        width: 15rem;
        height: 12rem;
        
    }

</style>
<div class="row">
    <div class="col-sm-12">
        <div class="d-flex justify-content-between">
            <p class="m-2"><span class="small">Created at:</span> 
                <span class="badge badge-success text-white">
                    <?= (new DateTime($guest['created_at']))->format('M j, Y g:iA') ?>
                </span>
            </p>
            <p class="m-2"><span class="small">Last Updated:</span> 
                <span class="badge badge-primary text-white">
                    <?= (new DateTime($guest['updated_at']))->format('M j, Y g:iA') ?>
                </span>
            </p>
        </div>
    </div>
</div>
<?= form_open('admin/user_management/guests/edit/' . $guest['id'], ['id' => 'editGuestForm']) ?>
<div class="row mb-2">
<div class="col-sm-6 mb-2">
        <input class="form-control" name="id" id="id" type="number" value="<?= isset($guest['id']) ? $guest['id'] : '' ?>" hidden>
    </div>
    <div class="col-sm-12 mb-2 text-center">
        <p>User Image</p>
    </div>
    <div class="col-sm-12 mb-2">
        <div class="video-wrapper d-flex justify-content-center">
            <video id="edit-video" autoplay style="display: none;"></video>
        </div>
        <div id="edit-upload_image_preview" style="display: none; text-align: center; margin-y: 10px">
            <img id="edit-upload_preview" class="rounded" width="250" height="250">
        </div>
        <div class="d-flex justify-content-center">
            <canvas id="edit-canvas" style="display: none;"></canvas>
            <img id="currentImage" class="rounded" src="<?= isset($guest['image']) ? base_url($guest['image']) : '' ?>" alt="Guest Image" style="max-width: 250px; max-height: 250px;">
            <input type="file" name="image" id="edit-imageInput" style="display: none;" accept="image/*">
        </div>
        <div class="capture-buttons d-flex justify-content-center mt-2">
            <button type="button" id="edit-capture-button" class="btn btn-sm btn-primary" style="display: none;">Take Snapshot</button>
            <input type="file" class="form-control-file" name="uploadInput" id="edit-uploadImage" style="display: none;" accept="image/*">
            <label for="edit-uploadImage" title="Upload Image" id="edit-uploadBtn" class="btn btn-sm btn-primary col-form-label ml-2" style="display: none;">
                <span class="icon text-white-600">
                    <i class="fas fa-upload d-sm-none"></i></span>
                <span class="text d-none d-sm-block">Upload Image</span>
            </label>
            <button type="button" id="edit-reset-button" class="btn btn-sm btn-danger">Reset</button>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="first_name">First Name <span class="text-danger">*</span></label>
        <input class="form-control" name="first_name" id="first_name" type="text" value="<?= $guest['first_name'] ?>">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="middle_name">Middle Name (Optional)</label>
        <input class="form-control" name="middle_name" id="middle_name" type="text" value="<?= $guest['middle_name'] ?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="last_name">Last Name <span class="text-danger">*</span></label>
        <input class="form-control" name="last_name" id="last_name" type="text" value="<?= $guest['last_name'] ?>">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="phone_number">Phone Number <span class="text-danger">*</span></label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">+63</span>
            </div>
            <input type="text" name="phone_number" class="form-control" id="phone_number" value="<?= $guest['phone_number'] ?>">
        </div>
    </div>
</div>

<!-- 
<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="company">Company <span class="text-danger">*</span></label>
        <input type="text" name="company" class="form-control" value="<?= $guest['company'] ?>">
    </div>
    <div class="col-md-6 mb-2">
        <label for="id_type">ID Type <span class="text-danger">*</span></label>
        <select name="id_type" id="id_type" class="form-control">
            <option value="">Select ID Type</option>
            <option value="None" <?= $guest['id_type'] == 'None' ? 'selected' : '' ?>>None</option>
            <option value="National ID" <?= $guest['id_type'] == 'National ID' ? 'selected' : '' ?>>National ID</option>
            <option value="ePhilID" <?= $guest['id_type'] == 'ePhilID' ? 'selected' : '' ?>>Philippine Identification (PhilID / ePhilID)</option>
            <option value="SSS" <?= $guest['id_type'] == 'SSS' ? 'selected' : '' ?>>SSS / UMID</option>
            <option value="Passport" <?= $guest['id_type'] == 'Passport' ? 'selected' : '' ?>>Passport</option>
            <option value="Driver's License" <?= $guest['id_type'] == 'Driver\'s License' ? 'selected' : '' ?>>Driver's License</option>
            <option value="PWD ID" <?= $guest['id_type'] == 'PWD ID' ? 'selected' : '' ?>>PWD ID</option>
            <option value="Barangay ID" <?= $guest['id_type'] == 'Barangay ID' ? 'selected' : '' ?>>Barangay ID</option>
            <option value="Phil-health ID" <?= $guest['id_type'] == 'Phil-health ID' ? 'selected' : '' ?>>Phil-health ID</option>
        </select>
    </div>
    <div class="col-md-6 mb-2">
        <label for="id_number">ID Number <span class="text-danger">*</span></label>
        <input type="text" name="id_number" class="form-control" value="<?= $guest['id_number'] ?>">
    </div>
</div> -->


<!-- <div class="row mb-2 user-id">
    <div class="col-sm-6 mb-2 text-center">
        <label for="id_front">Photo of ID (Front)</label>
        <img src="<?= empty($guest['id_front']) ? base_url('assets/images/id-card-color-icon.png') : $guest['id_front'] ?>" alt="Photo of front ID" class="img-fluid">
    </div>
    <div class="col-sm-6 mb-2 text-center">
        <label for="id_back">Photo of ID (Back)</label>
        <img src="<?= empty($guest['id_back']) ? base_url('assets/images/id-card-color-icon-back.png') : $guest['id_back'] ?>" alt="Photo of front ID" class="img-fluid">
    </div>
</div> -->

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="check_in_date">Check-In Date & Time <span class="text-danger">*</span></label>
        <input type="datetime-local"name="check_in_date" class="form-control" value="<?= $guest['check_in_date'] ?>">
    </div>
    <div class="col-md-6 mb-2">
        <label for="check_out_date">Estimated Check-Out Date <span class="text-danger">*</span></label>
        <input type="date"name="check_out_date" class="form-control" value="<?= $guest['check_out_date'] ?>">
    </div>
    <div class="col-md-6 mb-2">
        <label for="stay_purpose">Purpose of Stay <span class="text-danger">*</span></label>
        <input type="text" name="stay_purpose" class="form-control" value="<?= $guest['stay_purpose'] ?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="assigned_dormitory">Dormitory/Building Name <span class="text-danger">*</span></label>
        <input class="form-control" name="assigned_dormitory" id="assigned_dormitory" type="text" value="<?= $guest['assigned_dormitory'] ?>">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="room_number">Room Number <span class="text-danger">*</span></label>
        <input class="form-control" name="room_number" id="room_number" type="text" value="<?= $guest['room_number'] ?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="emergency_contact_person">Emergency Contact Person <span class="text-danger">*</span></label>
        <input class="form-control" name="emergency_contact_person" id="emergency_contact_person" type="text" value="<?= isset($guest['emergency_contact_person']) ? $guest['emergency_contact_person'] : '' ?>">
    </div>

    <div class="col-sm-6 mb-2">
        <label for="emergency_contact_number">Emergency Contact Number <span class="text-danger">*</span></label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">+63</span>
            </div>
            <input type="text" name="emergency_contact_number" class="form-control" id="emergency_contact_number" value="<?= $guest['emergency_contact_number'] ?>">
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="rfid">RFID <span class="text-danger">*</span></label>
        <input class="form-control" name="rfid" id="rfid" type="text" value="<?= isset($guest['rfid']) ? $guest['rfid'] : '' ?>">
    </div>
</div>

<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
    <input class="form-control" name="old_rfid" id="old_rfid" type="hidden" value="<?= $guest['rfid']; ?>">
</div>
<?= form_close() ?>
