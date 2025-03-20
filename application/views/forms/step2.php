<div class="step2-container">
        <div class="form-container">
            <div class="return-btn w-100 align-self-start ml-5 mt-5">
                <a href="<?= site_url('visitors_pending/form/step1'); ?>" class="btn btn-icon-split">
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
            <h1 class="">Kindly complete the form:</h1>
            <hr>
            <?php echo form_open('visitors_pending/process_step2', array(
                'class' => 'px-5'
            )); ?>
            <div class="col w-100 px-5 mb-5">
                <div class="row-sm-12 w-100">
                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                    <input placeholder="" type="text" name="first_name" class="form-control <?php echo (form_error('first_name') != '') ? 'is-invalid' : '' ?>" id="first_name" value="<?= isset($this->session->userdata('step2_data')['first_name']) ? $this->session->userdata('step2_data')['first_name'] : set_value('first_name'); ?>" required>
                    <div class="invalid-feedback"><?php echo form_error('first_name'); ?></div>
                </div>
                <div class="row-lg-12">
                    <label for="middle_name">Middle Name (Optional)</label>
                    <input placeholder="" type="text" name="middle_name" class="form-control <?php echo (form_error('middle_name') != '') ? 'is-invalid' : '' ?>" id="middle_name" value="<?= isset($this->session->userdata('step2_data')['middle_name']) ? $this->session->userdata('step2_data')['middle_name'] : set_value('middle_name'); ?>">
                    <div class="invalid-feedback"><?php echo form_error('middle_name'); ?></div>
                </div>
                <div class="row-lg-12">
                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                    <input placeholder="Last Name" type="text" name="last_name" class="form-control <?php echo (form_error('last_name') != '') ? 'is-invalid' : '' ?>" id="last_name" value="<?= isset($this->session->userdata('step2_data')['last_name']) ? $this->session->userdata('step2_data')['last_name'] : set_value('last_name'); ?>" required>
                    <div class="invalid-feedback"><?php echo form_error('last_name'); ?></div>
                </div>
                <div class="row-lg-12">
                    <label for="suffix">Suffix (Optional)</label>
                    <input placeholder="" type="text" name="suffix" class="form-control <?php echo (form_error('suffix') != '') ? 'is-invalid' : '' ?>" id="suffix" value="<?= isset($this->session->userdata('step2_data')['suffix']) ? $this->session->userdata('step2_data')['suffix'] : set_value('suffix'); ?>">
                    <div class="invalid-feedback"><?php echo form_error('suffix'); ?></div>
                </div>
                <div class="row-lg-12">
                    <label for="suffix">Email Address <span class="text-danger">*</span></label>
                    <input placeholder="Email Address" type="email" name="email" class="form-control <?php echo (form_error('email') != '') ? 'is-invalid' : '' ?>" id="email" value="<?= isset($this->session->userdata('step2_data')['email']) ? $this->session->userdata('step2_data')['email'] : set_value('email'); ?>" required>
                    <div class="invalid-feedback"><?php echo form_error('email'); ?></div>
                </div>
                <div class="row-lg-12">
                    <label for="suffix">Phone Number <span class="text-danger">*</span></label>
                    <div class="input-group">       
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">+63</span>
                        </div>
                        <input placeholder="" type="text" name="phone_number" class="form-control <?php echo (form_error('phone_number') != '' || $this->session->flashdata('phone_number_error')) ? 'is-invalid' : '' ?>" id="phone_number" value="<?= isset($this->session->userdata('step2_data')['phone_number']) ? $this->session->userdata('step2_data')['phone_number'] : set_value('phone_number'); ?>" required onchange="formatPhoneNumber(this)">
                        <div class="invalid-feedback"><?php echo form_error('phone_number'); ?></div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" id="submit" class="mt-5" onclick="displayLoader()"><span id="loader" class="spinner-border spinner-border-sm" style="display: none;"></span> Next</button>
            </div>
            <?php echo form_close(); ?>
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
</script>

