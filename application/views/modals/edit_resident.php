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
                    <?= (new DateTime($resident['created_at']))->format('M j, Y g:iA') ?>
                </span>
            </p>
            <p class="m-2"><span class="small">Last Updated:</span> 
                <span class="badge badge-primary text-white">
                    <?= (new DateTime($resident['updated_at']))->format('M j, Y g:iA') ?>
                </span>
            </p>
        </div>
    </div>
</div>
<?= form_open('admin/user_management/residents/edit/' . $resident['id'], ['id' => 'editResidentForm']) ?>
<div class="row mb-2">
<div class="col-sm-6 mb-2">
        <input class="form-control" name="id" id="id" type="number" value="<?= isset($resident['id']) ? $resident['id'] : '' ?>" hidden>
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
            <img id="currentImage" class="rounded" src="<?= isset($resident['image']) ? base_url($resident['image']) : '' ?>" alt="Resident Image" style="max-width: 250px; max-height: 250px;">
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
        <input class="form-control" name="first_name" id="first_name" type="text" value="<?= $resident['first_name'] ?>">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="middle_name">Middle Name (Optional)</label>
        <input class="form-control" name="middle_name" id="middle_name" type="text" value="<?= $resident['middle_name'] ?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="last_name">Last Name <span class="text-danger">*</span></label>
        <input class="form-control" name="last_name" id="last_name" type="text" value="<?= $resident['last_name'] ?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="dormitory">Dormitory <span class="text-danger">*</span></label>
        <input class="form-control" name="dormitory" id="dormitory" type="text" value="<?= $resident['dormitory'] ?>">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="move_in_date">Move-In Date <span class="text-danger">*</span></label>
        <input class="form-control" name="move_in_date" id="move_in_date" type="text" value="<?= $resident['move_in_date'] ?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="emergency_contact_person">Emergency Contact Person <span class="text-danger">*</span></label>
        <input class="form-control" name="emergency_contact_person" id="emergency_contact_person" type="text" value="<?= isset($resident['emergency_contact_person']) ? $resident['emergency_contact_person'] : '' ?>">
    </div>

    <div class="col-sm-6 mb-2">
        <label for="emergency_contact_number">Emergency Contact Number <span class="text-danger">*</span></label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">+63</span>
            </div>
            <input type="text" name="emergency_contact_number" class="form-control" id="emergency_contact_number" value="<?= $resident['emergency_contact_number'] ?>">
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="rfid">RFID <span class="text-danger">*</span></label>
        <input class="form-control" name="rfid" id="rfid" type="text" value="<?= isset($resident['rfid']) ? $resident['rfid'] : '' ?>">
    </div>
</div>

<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
    <input class="form-control" name="old_rfid" id="old_rfid" type="hidden" value="<?= $resident['rfid']; ?>">
</div>
<?= form_close() ?>
