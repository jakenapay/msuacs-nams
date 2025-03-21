<style>
    body {}

    #editVisitorForm {
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

<?= form_open('admin/visit_management/visitors_pending/update/' . $visitor['id'], ['id' => 'editVisitorForm']) ?>
<!-- <div class="row mb-2">
    <div class="col-sm-12 mb-2 text-center">
        <p>User Image</p>
        <img id="user-image" src="data:image/jpeg;base64,<?= $visitor['visitor_image']; ?>" alt="Visitor Image"
            class="rounded-full">
    </div>
</div> -->

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="first_name">First Name</label>
        <input class="form-control" name="first_name" id="first_name" type="text" value="<?= $visitor['first_name'] ?>"
            >
    </div>
    <!-- <div class="col-sm-6 mb-2">
        <label for="middle_name">Middle Name (Optional)</label>
        <input class="form-control" name="middle_name" id="middle_name" type="text" value="<?= $visitor['middle_name'] ?>" readonly>
    </div> -->
    <div class="col-sm-6 mb-2">
        <label for="last_name">Last Name</label>
        <input class="form-control" name="last_name" id="last_name" type="text" value="<?= $visitor['last_name'] ?>"
            >
    </div>
</div>

<!-- <div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="suffix">Suffix (Optional)</label>
        <input class="form-control" name="suffix" id="suffix" type="text" value="<?= $visitor['suffix'] ?>" readonly>
    </div>
</div> -->

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="phone_number">Phone Number</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">+63</span>
            </div>
            <input type="text" name="phone_number" 
                class="form-control <?php echo (form_error('phone_number') != '' || $this->session->flashdata('phone_number_error')) ? 'is-invalid' : '' ?>" 
                id="phone_number" 
                value="<?= isset($visitor['phone_number']) ? $visitor['phone_number'] : '' ?>">
        </div>
    </div>
    <div class="col-md-6 mb-2">
    <label for="visit_purpose">Purpose of Visit <span class="text-danger">*</span></label>
    <?php
    $visit_purposes = [
        'After-School Activity',
        'Attending School Event', 
        'Collecting Documents',
        'Meeting with Staff',
        'Parent Conference',
        'School Inspection',
        'Student Pick-Up/Drop-Off',
        'Vendor Delivery',
        'Volunteering',
        'Other'
    ];
    ?>
    <select name="visit_purpose" id="visit_purpose" class="form-control">
        <option value="">Select Purpose of Visit</option>
        <?php foreach($visit_purposes as $purpose): ?>
            <option value="<?= $purpose ?>" <?php 
                if(isset($visitor['visit_purpose'])) {
                    if($visitor['visit_purpose'] == $purpose) {
                        echo 'selected';
                    } elseif(!in_array($visitor['visit_purpose'], $visit_purposes) && $purpose == 'Other') {
                        echo 'selected';
                    }
                }
            ?>>
                <?= $purpose ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

    <!-- <div class="col-sm-6 mb-2">
        <label for="email">Email</label>
        <input class="form-control" name="email" id="email" type="email" value="<?= $visitor['email'] ?>" readonly>
    </div> -->
</div>

<!-- <div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="company">Company</label>
        <input type="text" name="company" class="form-control" value="<?= $visitor['company'] ?>" readonly>
    </div>
    <div class="col-md-6 mb-2">
        <label for="id_type">ID Type</label>
        <select name="id_type" id="id_type" class="form-control" disabled>
            <option value="">Select ID Type</option>
            <option value="None" <?= $visitor['id_type'] == 'None' ? 'selected' : '' ?>>None</option>
            <option value="National ID" <?= $visitor['id_type'] == 'National ID' ? 'selected' : '' ?>>National ID</option>
            <option value="ePhilID" <?= $visitor['id_type'] == 'ePhilID' ? 'selected' : '' ?>>Philippine Identification
                (PhilID / ePhilID)</option>
            <option value="SSS" <?= $visitor['id_type'] == 'SSS' ? 'selected' : '' ?>>SSS / UMID</option>
            <option value="Passport" <?= $visitor['id_type'] == 'Passport' ? 'selected' : '' ?>>Passport</option>
            <option value="Driver's License" <?= $visitor['id_type'] == 'Driver\'s License' ? 'selected' : '' ?>>Driver's
                License</option>
            <option value="PWD ID" <?= $visitor['id_type'] == 'PWD ID' ? 'selected' : '' ?>>PWD ID</option>
            <option value="Barangay ID" <?= $visitor['id_type'] == 'Barangay ID' ? 'selected' : '' ?>>Barangay ID</option>
            <option value="Phil-health ID" <?= $visitor['id_type'] == 'Phil-health ID' ? 'selected' : '' ?>>Phil-health ID
            </option>
        </select>
    </div>
    <div class="col-md-6 mb-2">
        <label for="id_number">ID Number</label>
        <input type="text" name="id_number" class="form-control" value="<?= $visitor['id_number'] ?>" readonly>
    </div>
</div> -->

<!-- 
<div class="row mb-2 user-id">
    <div class="col-sm-6 mb-2 text-center">
        <label for="id_front">Photo of ID (Front)</label>
        <img src="<?= empty($visitor['id_front']) ? base_url('assets/images/id-card-color-icon.png') : $visitor['id_front'] ?>"
            alt="Photo of front ID" class="img-fluid">
    </div>
    <div class="col-sm-6 mb-2 text-center">
        <label for="id_back">Photo of ID (Back)</label>
        <img src="<?= empty($visitor['id_back']) ? base_url('assets/images/id-card-color-icon-back.png') : $visitor['id_back'] ?>"
            alt="Photo of front ID" class="img-fluid">
    </div>
</div> -->

<div class="row mb-2">
    
    <div class="col-md-6 mb-2">
        <label for="visit_date">Date of Visit <span class="text-danger">*</span></label>
        <input readonly type="date" name="visit_date" class="form-control <?= (form_error('visit_date')) ? 'is-invalid' : '' ?>"
            value="<?= isset($visitor['visit_date']) ? $visitor['visit_date'] : set_value('visit_date') ?>"
            min="<?= date('Y-m-d') ?>"> <!-- Sets the minimum date to today -->
        <?php if (form_error('visit_date')): ?>
            <span class="text-danger"><?= form_error('visit_date') ?></span>
        <?php endif; ?>
    </div>
    <div class="col-md-6 mb-2">
        <label for="visit_time">Time of Visit <span class="text-danger">*</span></label>
        <input readonly type="time" name="visit_time" class="form-control <?= (form_error('visit_time')) ? 'is-invalid' : '' ?>"
            value="<?= isset($visitor['visit_time']) ? $visitor['visit_time'] : set_value('visit_time') ?>">
        <!-- Time picker -->
        <?php if (form_error('visit_time')): ?>
            <span class="text-danger"><?= form_error('visit_time') ?></span>
        <?php endif; ?>
    </div>

    <!-- <div class="col-md-6 mb-2">
        <label for="visit_time">Time of Visit <span class="text-danger">*</span></label>
        <select name="visit_time" id="visit_time"
            class="form-control <?= (form_error('visit_time')) ? 'is-invalid' : '' ?>">
            <?php
            $selected_time = isset($visitor['visit_time']) ? $visitor['visit_time'] : set_value('visit_time');
            $times = [];

            for ($i = 8; $i < 18; $i++) {
                $hour24 = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00:00';
                $hour12 = $i == 0 ? 12 : ($i > 12 ? $i - 12 : $i);
                $period = $i < 12 ? 'AM' : 'PM';
                $formatted_hour12 = str_pad($hour12, 2, '0', STR_PAD_LEFT) . ':00' . $period;
                $times[$hour24] = $formatted_hour12;
            }

            foreach ($times as $hour24 => $hour12) {
                $selected = $selected_time === $hour24 ? 'selected' : '';
                echo "<option value=\"$hour24\" $selected>$hour12</option>";
            }
            ?>
        </select>
        <?php if (form_error('visit_time')): ?>
            <span class="text-danger"><?= form_error('visit_time') ?></span>
        <?php endif; ?>
    </div> -->


    <!-- <div class="col-md-6 mb-2">
        <label for="visit_duration">Estimated duration of visit <span class="text-danger">*</span></label>
        <select readonly name="visit_duration" id="visit_duration"
            class="form-control <?= (form_error('visit_duration')) ? 'is-invalid' : '' ?>">
            <option value="" <?= set_select('visit_duration', '', empty($visitor['visit_duration'])); ?>>Select estimated
                duration</option>
            <option value="Less than an hour" <?= set_select('visit_duration', 'Less than an hour', isset($visitor['visit_duration']) && $visitor['visit_duration'] == 'Less than an hour'); ?>>Less than an
                hour</option>
            <option value="1 - 2 hours" <?= set_select('visit_duration', '1 - 2 hours', isset($visitor['visit_duration']) && $visitor['visit_duration'] == '1 - 2 hours'); ?>>1 - 2 hours</option>
            <option value="3 - 4 hours" <?= set_select('visit_duration', '3 - 4 hours', isset($visitor['visit_duration']) && $visitor['visit_duration'] == '3 - 4 hours'); ?>>3 - 4 hours</option>
            <option value="Half Day" <?= set_select('visit_duration', 'Half Day', isset($visitor['visit_duration']) && $visitor['visit_duration'] == 'Half Day'); ?>>Half Day</option>
            <option value="Full Day" <?= set_select('visit_duration', 'Full Day', isset($visitor['visit_duration']) && $visitor['visit_duration'] == 'Full Day'); ?>>Full Day</option>
        </select>
        <div class="invalid-feedback"><?php echo form_error('visit_duration'); ?></div>
    </div> -->

</div>

<!-- Old department and person meeting code -->
<!-- <div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="contact_department">Department of Person meeting with<span class="text-danger">*</span></label>
        <select class="form-control" id="contact_department" name="contact_department">
            <option value="">Select Department</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?= $department->id ?>" <?= set_select('contact_department', $department->id, isset($visitor['contact_department']) && $visitor['contact_department'] == $department->id); ?>>
                    <?= $department->name ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6 mb-2">
        <label for="contact_person">Name of Person meeting with<span class="text-danger">*</span></label>
        <select name="contact_person" id="contact_person"
            class="form-control <?= (form_error('contact_person')) ? 'is-invalid' : '' ?>">
            <option value="" <?= set_select('contact_person', '', !isset($visitor['contact_person']) || empty($visitor['contact_person'])); ?>>Select contact person name</option>
            <?php if (isset($visitor['contact_person'])): ?>
                <option selected value="<?= $visitor['contact_person'] ?>" <?= set_select('contact_person', $visitor['contact_person']); ?>>
                    <?= $visitor['contact_person'] ?>
                </option>
            <?php endif; ?>
        </select>
    </div>
</div> -->
<!-- 
<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="contact_department">Department of Person meeting with<span class="text-danger">*</span></label>
        <select readonly name="contact_department" id="contact_department"
            class="form-control <?= (form_error('contact_department')) ? 'is-invalid' : '' ?>">
            <option value="" <?= set_select('contact_department', '', !isset($visitor['contact_department']) || empty($visitor['contact_department'])); ?>>Select department name</option>
            <?php if (isset($visitor['contact_department'])): ?>
                <option selected value="<?= $visitor['contact_department'] ?>" <?= set_select('contact_department', $visitor['contact_department']); ?>>
                    <?= $visitor['contact_department'] ?>
                </option>
            <?php endif; ?>
            <?php foreach ($departments as $department): ?>
                <option value="<?= $department->id ?>" <?= set_select('contact_department', $department->id, isset($visitor['contact_department']) && $visitor['contact_department'] == $department->id); ?>>
                    <?= $department->name ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6 mb-2">
        <label for="contact_person">Name of Person meeting with<span class="text-danger">*</span></label>
        <select readonly name="contact_person" id="contact_person"
            class="form-control <?= (form_error('contact_person')) ? 'is-invalid' : '' ?>">
            <option value="" <?= set_select('contact_person', '', !isset($visitor['contact_person']) || empty($visitor['contact_person'])); ?>>Select contact person name</option>
            <?php if (isset($visitor['contact_person'])): ?>
                <option selected value="<?= $visitor['contact_person'] ?>" <?= set_select('contact_person', $visitor['contact_person']); ?>>
                    <?= $visitor['contact_person'] ?>
                </option>
            <?php endif; ?>
        </select>
    </div>

</div> -->

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="emergency_contact_person">Emergency Contact Person <span class="text-danger">*</span></label>
        <input class="form-control <?php echo (form_error('emergency_contact_person') != '') ? 'is-invalid' : '' ?>"
            name="emergency_contact_person" id="emergency_contact_person" type="text"
            value="<?= isset($visitor['emergency_contact_person']) ? $visitor['emergency_contact_person'] : '' ?>">
    </div>

    <div class="col-md-6 mb-2">
        <label for="emergency_contact_number">Emergency Contact Number <span class="text-danger">*</span></label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">+63</span>
            </div>
            <input placeholder="" type="text" name="emergency_contact_number"
                class="form-control <?php echo (form_error('emergency_contact_number') != '' || $this->session->flashdata('phone_number_error')) ? 'is-invalid' : '' ?>"
                id="emergency_contact_number"
                value="<?= isset($visitor['emergency_contact_number']) ? $visitor['emergency_contact_number'] : set_value('emergency_contact_number'); ?>"
                required onchange="formatPhoneNumber(this)"
                value="<?= isset($visitor['emergency_contact_number']) ? $visitor['emergency_contact_number'] : '' ?>">
        </div>
        <div>
        </div>

        <div class="d-flex mt-5">
            <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
        </div>
        <?= form_close() ?>