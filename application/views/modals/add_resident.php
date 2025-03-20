<style>
    body{
    }
    
    #addResidentForm{
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

<?= form_open('admin/user_management/residents/add', ['id' => 'addResidentForm', 'enctype' => 'multipart/form-data']) ?>
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
            <input type="file" class="form-control-file" name="uploadInput" id="uploadImage" style="display: none;" accept="image/*">
            <label for="uploadImage" title="Upload Image" id="uploadBtn" class="btn btn-sm btn-primary col-form-label ml-2">
            <span class="icon text-white-600">
            <i class="fas fa-upload d-sm-none"></i></span>
            <span class="text d-none d-sm-block">Upload Image</span></label>
            <button type="button" id="reset-button" class="btn btn-sm btn-danger" style="display: none;">Reset</button>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="first_name">First Name</label>
        <input class="form-control <?= form_error('first_name') ? 'form-error' : '' ?>" name="first_name" id="first_name" type="text" value="<?= set_value('first_name') ?>">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="middle_name">Middle Name</label>
        <input class="form-control <?= form_error('middle_name') ? 'form-error' : '' ?>" name="middle_name" id="middle_name" type="text" value="<?= set_value('middle_name')?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="last_name">Last Name</label>
        <input class="form-control <?= form_error('last_name') ? 'form-error' : '' ?>" name="last_name" id="last_name" type="text" value="<?= set_value('last_name') ?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="dormitory">Dormitory</label>
        <input class="form-control" name="dormitory" id="dormitory" type="text">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="move_in_date">Move-In Date</label>
        <input class="form-control" name="move_in_date" id="move_in_date" type="date">
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="emergency_contact_person">Emergency Contact Person</label>
        <input class="form-control" name="emergency_contact_person" id="emergency_contact_person" type="text">
    </div>

    <div class="col-sm-6 mb-2">
        <label for="emergency_contact_number">Emergency Contact Number</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">+63</span>
            </div>
            <input type="text" name="emergency_contact_number" class="form-control" id="emergency_contact_number">
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="rfid">RFID</label>
        <input type="number" name="rfid" class="form-control" id="rfid">
    </div>
</div>


<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
</div>
<?= form_close() ?>

