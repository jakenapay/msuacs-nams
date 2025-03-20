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

<?= form_open('admin/visit_management/visitors_pending/add/', ['id' => 'addVisitorForm']) ?>
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
        <input class="form-control" name="first_name" id="first_name" type="text">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="last_name">Last Name</label>
        <input class="form-control" name="last_name" id="last_name" type="text">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="phone_number">Phone Number</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">+63</span>
            </div>
            <input type="text" name="phone_number" class="form-control" id="phone_number">
        </div>
    </div>
    <div class="col-md-6 mb-2">
        <label for="visit_purpose">Purpose of Visit <span class="text-danger">*</span></label>
        <input type="text" name="visit_purpose" class="form-control">
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="visit_date">Date of Visit <span class="text-danger">*</span></label>
        <input type="date"
            name="visit_date" 
            class="form-control <?= (form_error('visit_date')) ? 'is-invalid' : '' ?>"
            min="<?= date('Y-m-d') ?>"> <!-- Sets the minimum date to today -->
        <?php if (form_error('visit_date')): ?>
            <span class="text-danger"><?= form_error('visit_date') ?></span>
        <?php endif; ?>
    </div>
    <div class="col-md-6 mb-2">
        <label for="visit_time">Time of Visit <span class="text-danger">*</span></label>
        <select name="visit_time" id="visit_time" class="form-control">
            <?php 
                $selected_time = set_value('visit_time');
                $times = [];

                for ($i = 8 ; $i < 18; $i++) {
                    $hour24 = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                    $hour12 = $i == 0 ? 12 : ($i > 12 ? $i - 12 : $i);
                    $period = $i < 12 ? 'AM' : 'PM';
                    $formatted_hour12 = str_pad($hour12, 2, '0', STR_PAD_LEFT) . ':00 ' . $period;
                    $times[$hour24] = $formatted_hour12;
                }

                foreach ($times as $hour24 => $hour12) {
                    $selected = $selected_time === $hour24 ? 'selected' : '';
                    echo "<option value=\"$hour24\" $selected>$hour12</option>";
                }
            ?>
        </select>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="emergency_contact_person">Emergency Contact Person <span class="text-danger">*</span></label>
        <input class="form-control <?php echo (form_error('emergency_contact_person') != '') ? 'is-invalid' : '' ?>" name="emergency_contact_person" id="emergency_contact_person" type="text">
    </div>

    <div class="col-sm-6 mb-2">
        <label for="emergency_contact_number">Emergency Phone Number</label>
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
        <label for="rfid">RFID <span class="text-danger">*</span></label>
        <input class="form-control" name="rfid" id="rfid" type="text">
    </div>
</div>

<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
</div>
<?= form_close() ?>
