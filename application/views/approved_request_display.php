<div class="step2-container">
    <div class="form-container">
        <div class="return-btn w-100 align-self-start ml-5 mt-5">
            <a href="<?= site_url('form/status'); ?>" class="btn btn-icon-split">
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

        <h1 class="">Reservation Details</h1>
        <div class="">
            <!-- <img src="" alt="Visitor image"> -->
        </div>
        <?= form_open_multipart('form/reservation/update/' . $visitor->id, array(
            'id' => 'updateForm',
            'class' => 'px-5',
            )) ?>
        <div class="text-center review-title">
            <p>Reservation Status: <?= $visitor->status == 1 ? '<span class="bg-success p-2 rounded text-white">Pending</span>' : '<span class="bg-success p-2 rounded text-white">Approved</span>'  ?></p>
        </div>

        <div class="text-center review-title mt-4">
            <p>Personal Details</p>
            <hr class="px-5">
        </div>
        <div class="row w-100 px-5 mb-5 mt-4">
            <div class="col-lg-12 col-xl-6">
                <label for="first_name">First Name <span class="text-danger">*</span></label>
                <input class="form-control <?php echo (form_error('first_name') != '') ? 'is-invalid' : '' ?>" name="first_name" id="first_name" type="text" value="<?= $visitor->first_name ?>" readonly>
                <div class="invalid-feedback"><?php echo form_error('first_name'); ?></div>
            </div>
            <div class="col-lg-12 col-xl-6"> 
                <label for="middle_name">Middle Name (Optional)</label>
                <input class="form-control <?php echo (form_error('middle_name') != '') ? 'is-invalid' : '' ?>" name="middle_name" id="middle_name" type="text" value="<?= $visitor->middle_name ?>" readonly>
                <div class="invalid-feedback"><?php echo form_error('middle_name'); ?></div>
            </div>
            <div class="col-lg-12 col-xl-6"> 
                <label for="suffix">Suffix (Optional)</label>
                <input class="form-control <?php echo (form_error('suffix') != '') ? 'is-invalid' : '' ?>" name="suffix" id="suffix" type="text" value="<?= $visitor->suffix ?>" readonly>
                <div class="invalid-feedback"><?php echo form_error('suffix'); ?></div>
            </div>
            <div class="col-lg-12 col-xl-6">
                <label for="last_name">Last Name <span class="text-danger">*</span></label>
                <input class="form-control <?php echo (form_error('last_name') != '') ? 'is-invalid' : '' ?>" name="last_name" id="last_name" type="text" value="<?= $visitor->last_name ?>" readonly>
                <div class="invalid-feedback"><?php echo form_error('last_name'); ?></div>
            </div>
            <div class="col-lg-12 col-xl-6">
                <label for="phone_number">Phone Number <span class="text-danger">*</span></label>
                <div class="input-group">       
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">+63</span>
                    </div>
                    <input placeholder="" type="text" name="phone_number" class="form-control <?php echo (form_error('phone_number') != '' || $this->session->flashdata('phone_number_error')) ? 'is-invalid' : '' ?>" id="phone_number" value="<?= $visitor->phone_number ?>" required readonly>
                    <div class="invalid-feedback"><?php echo form_error('phone_number'); ?></div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-6">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input class="form-control <?php echo (form_error('email') != '') ? 'is-invalid' : '' ?>" name="email" id="email" type="text" value="<?= $visitor->email ?>" readonly>
                <div class="invalid-feedback"><?php echo form_error('email'); ?></div>
            </div>
        </div>
        <div class="text-center review-title">
            <p>Credentials</p>
            <hr class="px-5">
        </div>
        <div class="row w-100 px-5 mb-5 mt-4">
            <div class="col-lg-12 col-xl-6">
                <label for="company">Company (Optional)</label>
                <input type="text" name="company" class="form-control <?= (form_error('company')) ? 'is-invalid' : '' ?>" placeholder="Company (Optional)" value="<?= $visitor->company ?>" readonly>
                <div class="invalid-feedback"><?php echo form_error('company'); ?></div>

                <label for="id_type">ID Type <span class="text-danger">*</span></label>
                    <select name="id_type" id="id_type" class="form-control" disabled>
                        <option value="" selected>Select ID Type</option>
                        <option value="None" <?= set_select('id_type', 'None', isset($visitor->id_type) && $visitor->id_type == 'None'); ?>>None</option>
                        <option value="National ID" <?= set_select('id_type', 'National ID', isset($visitor->id_type) && $visitor->id_type == 'National ID'); ?>>National ID</option>
                        <option value="ePhilID" <?= set_select('id_type', 'ePhilID', isset($visitor->id_type) && $visitor->id_type == 'ePhilID'); ?>>Philippine Identification (PhilID / ePhilID)</option>
                        <option value="SSS" <?= set_select('id_type', 'SSS', isset($visitor->id_type) && $visitor->id_type == 'SSS'); ?>>SSS / UMID</option>
                        <option value="Passport" <?= set_select('id_type', 'Passport', isset($visitor->id_type) && $visitor->id_type == 'Passport'); ?>>Passport</option>
                        <option value="Driver's License" <?= set_select('id_type', 'Driver\'s License', isset($visitor->id_type) && $visitor->id_type == 'Driver\'s License'); ?>>Driver's License</option>
                        <option value="PWD ID" <?= set_select('id_type', 'PWD ID', isset($visitor->id_type) && $visitor->id_type == 'PWD ID'); ?>>PWD ID</option>
                        <option value="Barangay ID" <?= set_select('id_type', 'Barangay ID', isset($visitor->id_type) && $visitor->id_type == 'Barangay ID'); ?>>Barangay ID</option>
                        <option value="Phil-health ID" <?= set_select('id_type', 'Phil-health ID', isset($visitor->id_type) && $visitor->id_type == 'Phil-health ID'); ?>>Phil-health ID</option>
                    </select>
                    <div class="invalid-feedback"><?php echo form_error('id_type'); ?></div>
            </div>
            <div class="col-lg-12 col-xl-6 align-self-end id-fields">
                <label for="id_number">ID Number <span class="text-danger">*</span></label>
                <input type="text" name="id_number" class="form-control <?= (form_error('id_number')) ? 'is-invalid' : '' ?>" placeholder="ID Number" value="<?= $visitor->id_number ?>" readonly>
                <div class="invalid-feedback"><?php echo form_error('id_number'); ?></div>
            </div>
            <div class="col-lg-12 col-xl-6 id-fields">
                <label for="id_front">ID (Front) <span class="text-danger">*</span></label>
                    <?php if (form_error('id_back_base64')): ?>
                        <span class="text-danger"><?= form_error('id_front_base64') ?></span>
                    <?php endif; ?>
                    <div class="form-file id-back">
                        <label for="id_front" class="id-label <?= (form_error('id_front_base64')) ? 'is-invalid' : '' ?>">Photo of Front ID
                            <span class="id-upload-button">Choose File</span>
                            <input type="file" name="id_front" class="id_input" id="id_front" accept="image/*" onchange="encodeImageFileAsURL(this, 'id_front_base64', 'id_front_preview')" readonly>
                            <input type="hidden" name="id_front_base64" id="id_front_base64" value="<?= $visitor->id_front ?>" readonly>
                        </label>
                        <img id="id_front_preview" class="preview-image" src="<?= $visitor->id_front ?>">
                        <div class="invalid-feedback"><?php echo form_error('id_front_base64'); ?></div>
                    </div>
            </div>
            <div class="col-lg-12 col-xl-6 id-fields">
                <label for="id_back">ID (Back) <span class="text-danger">*</span></label>
                        <?php if (form_error('id_back_base64')): ?>
                            <span class="text-danger"><?= form_error('id_back_base64') ?></span>
                        <?php endif; ?>
                        <div class="form-file id-back">
                        <label for="id_back" class="id-label <?= (form_error('id_back_base64')) ? 'is-invalid' : '' ?>">Photo of ID (back)
                            <span class="id-upload-button">Choose File</span>
                            <input type="file" class="id_input" name="id_back" id="id_back" accept="image/*" onchange="encodeImageFileAsURL(this, 'id_back_base64', 'id_back_preview')" readonly>
                            <input type="hidden" name="id_back_base64" id="id_back_base64" value="<?= $visitor->id_back ?>" readonly>
                        </label>
                        <img id="id_back_preview" class="preview-image" src="<?= $visitor->id_back ?>">
                        </div>
            </div>
        </div>
        <div class="text-center review-title">
            <p>Visit Details</p>
            <hr class="px-5">
        </div>
        <div class="row w-100 px-5 mt-4">
            <div class="col-lg-12 col-xl-6">
                <label for="visit_purpose">Purpose of Visit <span class="text-danger">*</span></label>
                <input type="text" name="visit_purpose" class="form-control <?= (form_error('visit_purpose')) ? 'is-invalid' : '' ?>" placeholder="" value="<?= $visitor->visit_purpose ?>" readonly>
                <?php if (form_error('visit_purpose')): ?>
                    <span class="text-danger"><?= form_error('visit_purpose') ?></span>
                <?php endif; ?>

                <label for="visit_date">Date of Visit <span class="text-danger">*</span></label>
                <input readonly type="date"
                    name="visit_date" 
                    class="form-control <?= (form_error('visit_date')) ? 'is-invalid' : '' ?>"
                    value="<?= $visitor->visit_date ?>"
                    min="<?= date('Y-m-d') ?>"> <!-- Sets the minimum date to today -->
                <?php if (form_error('visit_date')): ?>
                    <span class="text-danger"><?= form_error('visit_date') ?></span>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-12 col-xl-6">
                <label for="visit_time">Time of Visit <span class="text-danger">*</span></label>
                    <select name="visit_time" id="visit_time" class="form-control <?= (form_error('visit_time')) ? 'is-invalid' : '' ?>" disabled>
                        <?php 
                            $selected_time = isset($visitor->visit_time) ? $visitor->visit_time : set_value('visit_time');
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


                    <label for="visit_duration">Estimated duration of visit <span class="text-danger">*</span></label>
                    <select name="visit_duration" id="visit_duration" class="form-control <?= (form_error('visit_duration')) ? 'is-invalid' : '' ?>" disabled>
                        <option value="" selected>Select estimated duration</option>
                        <option value="Less than an hour" <?= set_select('visit_duration', 'Less than an hour', isset($visitor->visit_duration) && $visitor->visit_duration == 'Less than an hour'); ?>>Less than an hour</option>
                        <option value="1 - 2 hours" <?= set_select('visit_duration', '1 - 2 hours', isset($visitor->visit_duration) && $visitor->visit_duration == '1 - 2 hours'); ?>>1 - 2 hours</option>
                        <option value="3 - 4 hours" <?= set_select('visit_duration', '3 - 4 hours', isset($visitor->visit_duration) && $visitor->visit_duration == '3 - 4 hours'); ?>>3 - 4 hours</option>
                        <option value="Half Day" <?= set_select('visit_duration', 'Half Day', isset($visitor->visit_duration) && $visitor->visit_duration == 'Half Day'); ?>>Half Day</option>
                        <option value="Full Day" <?= set_select('visit_duration', 'Full Day', isset($visitor->visit_duration) && $visitor->visit_duration == 'Full Day'); ?>>Full Day</option>
                    </select>
                    <div class="invalid-feedback"><?php echo form_error('visit_duration'); ?></div>
            </div>

            <div class="col-lg-12 col-xl-6">
                    <label for="contact_position">Contact Person Position <span class="text-danger">*</span></label>
                    <select name="contact_position" id="contact_position" class="form-control <?= (form_error('contact_position')) ? 'is-invalid' : '' ?>" disabled>
                        <option value="" selected>Select position of contact person</option>
                        <option value="Administrator" <?= set_select('contact_position', 'Administrator', isset($visitor->contact_position) && $visitor->contact_position == 'Administrator'); ?>>Administrator</option>
                        <option value="Principal" <?= set_select('contact_position', 'Principal', isset($visitor->contact_position) && $visitor->contact_position == 'Principal'); ?>>Principal</option>
                        <option value="Teacher" <?= set_select('contact_position', 'Teacher', isset($visitor->contact_position) && $visitor->contact_position == 'Teacher'); ?>>Teacher</option>
                        <option value="Department Head" <?= set_select('contact_position', 'Department Head', isset($visitor->contact_position) && $visitor->contact_position == 'Department Head'); ?>>Department Head</option>
                        <option value="Counselor" <?= set_select('contact_position', 'Counselor', isset($visitor->contact_position) && $visitor->contact_position == 'Counselor'); ?>>Counselor</option>
                        <option value="Staff Member" <?= set_select('contact_position', 'Staff Member', isset($visitor->contact_position) && $visitor->contact_position == 'Staff Member'); ?>>Staff Member</option>
                        <option value="Other" <?= set_select('contact_position', 'Other', isset($visitor->contact_position) && $visitor->contact_position == 'Other'); ?>>Other (Please Specify)</option>
                    </select>
                    <div class="invalid-feedback"><?php echo form_error('contact_position'); ?></div>
                    <input type="text" name="contact_position_other" id="contact_position_input" class="form-control mt-2 <?= (form_error('contact_position_other')) ? 'is-invalid' : '' ?>" style="display: none;" placeholder="Please specify contact person's position" value="<?= $visitor->contact_position ?>">
                    <div class="invalid-feedback"><?php echo form_error('contact_position_other'); ?></div>

                    <label for="contact_person">Contact Person Name <span class="text-danger">*</span></label>
                    <select name="contact_person" id="contact_person" class="form-control <?= (form_error('contact_person')) ? 'is-invalid' : '' ?>" disabled>
                    <option value="" <?= set_select('contact_person', '', !isset($visitor->contact_person) || empty($visitor->contact_person)); ?>>Select contact person name</option>
                        <?php if (isset($visitor->contact_person)): ?>
                                <option selected value="<?=$visitor->contact_person ?>" <?= set_select('contact_person', $visitor->contact_person); ?>>
                                    <?= $visitor->contact_person ?>
                                </option>
                        <?php endif; ?>
                        <option value="Other" <?= set_select('contact_person', 'Other', isset($visitor->contact_person) && $visitor->contact_person == 'Other'); ?>>Other (Please Specify)</option>
                    </select>
                    <div class="invalid-feedback"><?php echo form_error('contact_person'); ?></div>
                    <input type="text" name="contact_person_other" id="contact_person_input" class="form-control mt-2 <?= (form_error('contact_person_other')) ? 'is-invalid' : '' ?>" style="display: none;" placeholder="Please specify contact person's name" value="<?= set_value('contact_person_other', isset($this->session->userdata('step4_data')['contact_person_other']) ? $this->session->userdata('step4_data')['contact_person_other'] : ''); ?>">
                    <div class="invalid-feedback"><?php echo form_error('contact_person_other'); ?></div>
            </div>


            <div class="col-lg-12 col-xl-6 mb-5">
                <label for="parking_requirement">Parking requirement <span class="text-danger">*</span></label>
                <select name="parking_requirement" id="parking_requirement" class="form-control <?= (form_error('parking_requirement')) ? 'is-invalid' : '' ?>" disabled>
                    <option value="" selected>Select parking requirement</option>
                    <option value="No parking needed" <?= set_select('parking_requirement', 'No parking needed', isset($visitor->parking_requirement) && $visitor->parking_requirement == 'No parking needed'); ?>>No parking needed</option>
                    <option value="Car parking" <?= set_select('parking_requirement', 'Car parking', isset($visitor->parking_requirement) && $visitor->parking_requirement == 'Car parking'); ?>>Car parking</option>
                    <option value="Motorcycle parking" <?= set_select('parking_requirement', 'Motorcycle parking', isset($visitor->parking_requirement) && $visitor->parking_requirement == 'Motorcycle parking'); ?>>Motorcycle parking</option>
                    <option value="Oversized vehicle parking" <?= set_select('parking_requirement', 'Oversized vehicle parking', isset($visitor->parking_requirement) && $visitor->parking_requirement == 'Oversized vehicle parking'); ?>>Oversized vehicle parking</option>
                </select>
                <div class="invalid-feedback"><?php echo form_error('parking_requirement'); ?></div>

                <label for="accomodations" class="form-label">Special accommodations needed (Optional)</label>
                <textarea readonly class="form-control <?= (form_error('accomodations')) ? 'is-invalid' : '' ?>" name="accomodations" id="accomodations" min-rows="3"><?= $visitor->accomodations ?>
                </textarea>
                <div class="invalid-feedback"><?php echo form_error('accomodations'); ?></div>
            </div>

        </div>
        <div class="d-flex justify-content-around">
                <button type="reset" id="cancel-btn" class="mt-5" onclick="displayLoader2();" data-toggle="modal" data-target="#logoutModal">
                    <span id="loader2" class="spinner-border spinner-border-sm" style="display: none;"></span> Cancel Reservation</button>
        </div>
        <?= form_close() ?>
    </div>

    <div class="image-container">

    </div>

    <!------Reservation Cancellation Modal --->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Cancel Visit Reservation?</h5>
              <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">Select "Yes" below if you are ready to cancel your current reservation.</div>
            <div class="modal-footer">
              <button class="btn" id="cancel-btn" type="button" data-dismiss="modal">Cancel</button>
              <a class="btn btn-success text-white" onclick="cancelReservation('<?= $visitor->transaction_number; ?>');">Yes</a>
            </div>
          </div>
        </div>
</div>
<script>
        document.addEventListener('DOMContentLoaded', function () {
        const idType = document.getElementById('id_type');
        const idFields = document.querySelectorAll('.id-fields');
        const idBackPreview = document.getElementById('id_back_preview');
        const idFrontPreview = document.getElementById('id_front_preview');
        const formContainer = document.getElementById('form-container');

        function toggleIdFields() {
            if (idType.value === 'None') {
                idFields.forEach(field => field.style.display = 'none');
            } else {
                idFields.forEach(field => field.style.display = 'block');
                // formContainer.style.height = 'auto';
                // idBackPreview.style.display = 'block';
                // idFrontPreview.style.display = 'block';
            }
        }

        idType.addEventListener('change', toggleIdFields);
        toggleIdFields(); // Initial check on page load
    });

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

    function cancelReservation(transactionNumber) {
        let form = document.getElementById('updateForm');
        form.action = site_url + 'form/reservation/cancel/' + transactionNumber; // Change this to your desired URL for cancellation
        form.submit();
    }
</script>
<script src="<?= base_url('assets/js/form.js'); ?>"></script>