<style>
    body{
    }
    
    #editStudentForm{
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
                    <?= (new DateTime($student->created_at))->format('M j, Y g:iA') ?>
                </span>
            </p>
            <p class="m-2"><span class="small">Last Updated:</span> 
                <span class="badge badge-primary text-white">
                    <?= (new DateTime($student->updated_at))->format('M j, Y g:iA') ?>
                </span>
            </p>
        </div>
    </div>
</div>
<?= form_open('admin/user_management/students/edit/' . $student->id, ['id' => 'editStudentForm', 'enctype' => 'multipart/form-data']) ?>
<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <input class="form-control" name="id" id="id" type="number" value="<?= isset($student->id) ? $student->id : '' ?>" hidden>
    </div>
    <div class="col-sm-12 mb-2 text-center">
        <p>User Image</p>
    </div>
    <div class="col-sm-12 mb-2">
        <div class="video-wrapper d-flex justify-content-center">
            <video id="edit-video" class="<?= form_error('image') ? 'form-error' : '' ?>" autoplay style="display: none;"></video>
        </div>
        <div id="edit-upload_image_preview" style="display: none; text-align: center; margin-y: 10px">
            <img id="edit-upload_preview" class="rounded" width="250" height="250">
        </div>
        <div class="d-flex justify-content-center">
            <canvas id="edit-canvas" style="display: none;"></canvas>
            <img id="currentImage" class="rounded" src="<?= isset($student->image) ? base_url($student->image) : '' ?>" alt="Student Image" style="max-width: 250px; max-height: 250px;">
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
        <label for="first_name">First Name</label>
        <input class="form-control" name="first_name" id="first_name" type="text" value="<?= isset($student->first_name) ? $student->first_name : set_value('first_name') ?>">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="middle_name">Middle Name</label>
        <input class="form-control" name="middle_name" id="middle_name" type="text" value="<?= isset($student->middle_name) ? $student->middle_name : set_value('middle_name')?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="last_name">Last Name</label>
        <input class="form-control" name="last_name" id="last_name" type="text" value="<?= isset($student->last_name) ? $student->last_name : set_value('last_name') ?>">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="id_number">ID Number</label>
        <input type="text" name="id_number" class="form-control" id="id_number" value="<?= isset($student->id_number) ? $student->id_number : set_value('id_number')?>" >
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="college">College</label>
        <select class="form-control" name="college_id" id="college">
            <?php if($student->college): ?>
                <option value="<?= $student->college ?>" selected><?= $student->college ?></option>
            <?php endif; ?>
            <option value="">Select College</option>
            <?php foreach($colleges as $college): ?>
                <?php if($college->name !== $student->college): ?>
                    <option value="<?= $college->name ?>"><?= $college->name ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-sm-6 mb-2">
        <label for="department">Department</label>
        <select class="form-control" name="department_id" id="department">
            <?php if($student->department): ?>
                <option value="<?= $student->department ?>" selected><?= $student->department ?></option>
            <?php endif; ?>
            <option value="">Select Department</option>
            <?php foreach($departments as $department): ?>
                <?php if($department->name !== $student->department): ?>
                    <option value="<?= $department->name ?>"><?= $department->name ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="program">Program</label>
        <select class="form-control" name="program_id" id="program">
            <?php if($student->program): ?>
                <option value="<?= $student->program ?>" selected><?= $student->program ?></option>
            <?php endif; ?>
            <option value="">Select Program</option>
            <?php foreach($programs as $program): ?>
                <?php if($program->name !== $student->program): ?>
                    <option value="<?= $program->name ?>"><?= $program->name ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-sm-6 mb-2">
        <label for="enrollment_status">Enrollment Status</label>
        <input type="text" name="enrollment_status" class="form-control" id="enrollment_status" value="<?= isset($student->enrollment_status) ? $student->enrollment_status : set_value('enrollment_status')?>"  >
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="resident_status">Resident Status</label>
        <select class="form-control" name="resident_status" id="resident_status">
            <option value="">Select Resident Status</option>
            <option value="1" <?= ($student->resident_status == 1) ? 'selected' : '' ?>>Yes</option>
            <option value="0" <?= ($student->resident_status == 0) ? 'selected' : '' ?>>No</option>
        </select>
    </div>
    <div class="col-sm-6 mb-2">
        <label for="assigned_dormitory">Assigned Dormitory (If Applicable)</label>
        <input type="text" name="assigned_dormitory" class="form-control" id="assigned_dormitory" value="<?= isset($student->assigned_dormitory) ? $student->assigned_dormitory : set_value('assigned_dormitory')?>" >
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="emergency_contact_person">Emergency Contact Person</label>
        <input class="form-control" name="emergency_contact_person" id="emergency_contact_person" type="text" value="<?= isset($student->emergency_contact_person) ? $student->emergency_contact_person : set_value('emergency_contact_person')?>" >
    </div>

    <div class="col-sm-6 mb-2">
        <label for="emergency_contact_number">Phone Number</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">+63</span>
            </div> 
            <input type="text" name="emergency_contact_number" class="form-control" id="emergency_contact_number" value="<?= isset($student->emergency_contact_number) ? $student->emergency_contact_number : set_value('emergency_contact_number')?>">
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="rfid">RFID</label>
        <input type="number" name="rfid" class="form-control" id="rfid" value="<?= isset($student->rfid) ? $student->rfid : set_value('rfid')?>" >
    </div>
</div>

<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
</div>
<?= form_close() ?>