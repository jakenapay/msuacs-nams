<div class="step2-container">
    <div class="form-container">
        <div class="return-btn w-100 align-self-start ml-5 mt-5">
            <a href="<?= site_url('visitors_pending/form/step3'); ?>" class="btn btn-icon-split">
                <span class="icon"><i class="fas fa-chevron-left"></i></span>
            </a>
        </div>
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success mx-5 mb-5" role="alert">
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php elseif($this->session->flashdata('danger')): ?>
            <div class="alert alert-danger mx-5 mb-5" role="alert">
                <?php echo $this->session->flashdata('danger'); ?>
            </div>            
        <?php endif; ?>
        <h1>Kindly complete the form:</h1>
        <hr>
        <?= form_open('visitors_pending/process_step4', array(
            'class' => 'px-5'
        )) ?>
        <div class="col w-100">

            <div class="row-lg-12">
                <label for="visit_purpose">Purpose of Visit <span class="text-danger">*</span></label>
                <input type="text" name="visit_purpose" class="form-control <?= (form_error('visit_purpose')) ? 'is-invalid' : '' ?>" placeholder="" value="<?= isset($this->session->userdata('step4_data')['visit_purpose']) ? $this->session->userdata('step4_data')['visit_purpose'] : set_value('visit_purpose') ?>">
                <?php if (form_error('visit_purpose')): ?>
                    <span class="text-danger"><?= form_error('visit_purpose') ?></span>
                <?php endif; ?>
            </div>

            <div class="row-lg-12">
                <label for="visit_date">Date of Visit <span class="text-danger">*</span></label>
                <input type="date"
                    name="visit_date" 
                    class="form-control <?= (form_error('visit_date')) ? 'is-invalid' : '' ?>"
                    value="<?= isset($this->session->userdata('step4_data')['visit_date']) ? $this->session->userdata('step4_data')['visit_date'] : set_value('visit_date') ?>"
                    min="<?= date('Y-m-d') ?>"> <!-- Sets the minimum date to today -->
                <?php if (form_error('visit_date')): ?>
                    <span class="text-danger"><?= form_error('visit_date') ?></span>
                <?php endif; ?>
            </div>

            <div class="row-lg-12">
                <label for="visit_time">Time of Visit <span class="text-danger">*</span></label>
                <select name="visit_time" id="visit_time" class="form-control <?= (form_error('visit_time')) ? 'is-invalid' : '' ?>">
                    <?php 
                        $selected_time = isset($this->session->userdata('step4_data')['visit_time']) ? $this->session->userdata('step4_data')['visit_time'] : set_value('visit_time');
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
                <?php if (form_error('visit_time')): ?>
                    <span class="text-danger"><?= form_error('visit_time') ?></span>
                <?php endif; ?>
            </div>

            <div class="row-lg-12">
                    <label for="visit_duration">Estimated duration of visit <span class="text-danger">*</span></label>
                    <select name="visit_duration" id="visit_duration" class="form-control <?= (form_error('visit_duration')) ? 'is-invalid' : '' ?>">
                        <option value="" selected>Select estimated duration</option>
                        <option value="Less than an hour" <?= set_select('visit_duration', 'Less than an hour', isset($this->session->userdata('step4_data')['visit_duration']) && $this->session->userdata('step4_data')['visit_duration'] == 'Less than an hour'); ?>>Less than an hour</option>
                        <option value="1 - 2 hours" <?= set_select('visit_duration', '1 - 2 hours', isset($this->session->userdata('step4_data')['visit_duration']) && $this->session->userdata('step4_data')['visit_duration'] == '1 - 2 hours'); ?>>1 - 2 hours</option>
                        <option value="3 - 4 hours" <?= set_select('visit_duration', '3 - 4 hours', isset($this->session->userdata('step4_data')['visit_duration']) && $this->session->userdata('step4_data')['visit_duration'] == '3 - 4 hours'); ?>>3 - 4 hours</option>
                        <option value="Half Day" <?= set_select('visit_duration', 'Half Day', isset($this->session->userdata('step4_data')['visit_duration']) && $this->session->userdata('step4_data')['visit_duration'] == 'Half Day'); ?>>Half Day</option>
                        <option value="Full Day" <?= set_select('visit_duration', 'Full Day', isset($this->session->userdata('step4_data')['visit_duration']) && $this->session->userdata('step4_data')['visit_duration'] == 'Full Day'); ?>>Full Day</option>
                    </select>
                    <div class="invalid-feedback"><?php echo form_error('visit_duration'); ?></div>
            </div>

            <div class="row-lg-12">
                    <label for="contact_department">Department of Person meeting with<span class="text-danger">*</span></label>
                    <select class="form-control" id="contact_department" name="contact_department">
                                <option value="">Select Department</option>
                                <?php foreach($departments as $department): ?>
                                    <option value="<?= $department->name ?>"><?= $department->name ?></option>
                                <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"><?php echo form_error('visit_duration'); ?></div>
            </div>

            <div class="row-lg-12">
                    <label for="contact_person">Name of Person meeting with<span class="text-danger">*</span></label>
                    <select name="contact_person" id="contact_person" class="form-control <?= (form_error('contact_person')) ? 'is-invalid' : '' ?>">
                    <option value="" <?= set_select('contact_person', '', !isset($this->session->userdata('step4_data')['contact_person']) || empty($this->session->userdata('step4_data')['contact_person'])); ?>>Select contact person name</option>
                        <?php if (isset($this->session->userdata('step4_data')['contact_person'])): ?>
                                <option selected value="<?=$this->session->userdata('step4_data')['contact_person'] ?>" <?= set_select('contact_person', $this->session->userdata('step4_data')['contact_person']); ?>>
                                    <?= $this->session->userdata('step4_data')['contact_person'] ?>
                                </option>
                        <?php endif; ?>
                    </select>
                    <div class="invalid-feedback"><?php echo form_error('contact_person'); ?></div>
            </div>

            <div class="row-lg-12 mt-5">
                <p>In case of Emergency:</p>
                <label for="emergency_contact_person">Emergency Contact Person <span class="text-danger">*</span></label>
                <input type="text" name="emergency_contact_person" class="form-control <?= (form_error('emergency_contact_person')) ? 'is-invalid' : '' ?>" placeholder="" value="<?= isset($this->session->userdata('step4_data')['emergency_contact_person']) ? $this->session->userdata('step4_data')['emergency_contact_person'] : set_value('emergency_contact_person') ?>">
                <?php if (form_error('emergency_contact_person')): ?>
                    <span class="text-danger"><?= form_error('emergency_contact_person') ?></span>
                <?php endif; ?>
            </div>
            <div class="row-lg-12">
                    <label for="suffix">Emergency Contact Phone Number <span class="text-danger">*</span></label>
                    <div class="input-group">       
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">+63</span>
                        </div>
                        <input placeholder="" type="text" name="emergency_contact_number" class="form-control <?php echo (form_error('emergency_contact_number') != '' || $this->session->flashdata('phone_number_error')) ? 'is-invalid' : '' ?>" id="emergency_contact_number" value="<?= isset($this->session->userdata('step2_data')['emergency_contact_number']) ? $this->session->userdata('step2_data')['emergency_contact_number'] : set_value('emergency_contact_number'); ?>" required onchange="formatPhoneNumber(this)">
                        <div class="invalid-feedback"><?php echo form_error('emergency_contact_number'); ?></div>
                    </div>
            </div>

        </div>
            <div class="d-flex justify-content-center">
                <button type="submit" id="submit" class="mt-5" onclick="displayLoader()"><span id="loader" class="spinner-border spinner-border-sm" style="display: none;"></span> Next</button>
            </div>
        <?= form_close() ?>
        <?php $this->load->view('templates/progressbar', array('progress' => $progress)); ?>

    </div>

    <div class="image-container">
        <!-- Placeholder for image or other content -->
    </div>
</div>
<script>
    function formatPhoneNumber(input) {
        let phoneNumber = input.value.trim();
        if (phoneNumber && !phoneNumber.startsWith('+')) {
            // Assuming the user is from the Philippines and didn't include the country code
            phoneNumber = phoneNumber.replace(/^63+/,   ''); // Remove leading zeros
            if (phoneNumber.startsWith('0')){
                phoneNumber = phoneNumber.replace(/^0+/, ''); // Remove leading zeros
            }
        }
        input.value = phoneNumber;
    }

    $(document).ready(function() {
        $('#contact_department').on('change', function() {
            let department = $(this).val();
            let contactPersonSelect = $('#contact_person');
            contactPersonSelect.empty(); // Clear previous options

            if (department) {
                // Assuming you have a backend endpoint to get the contact persons based on position
                $.ajax({
                    url: '<?= base_url("visitors_pending/get/contact_person") ?>',
                    method: 'POST',
                    data: {department: department},
                    success: function(response) {
                    let contactPersons = JSON.parse(response);
                    if (contactPersons.length > 0) {
                        contactPersons.forEach(function(person) {
                            let fullName = person.first_name + ' ' + person.last_name;
                            contactPersonSelect.append(new Option(fullName, fullName));
                        });
                    } else {
                        contactPersonSelect.append(new Option('No contacts found', ''));
                    }
                },
                    error: function() {
                        contactPersonSelect.append(new Option('Error fetching contacts', ''));
                    }
                });
            } else {
                contactPersonSelect.append(new Option('Select contact person name', ''));
            }
        });
    });
</script>