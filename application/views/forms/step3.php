<div class="step2-container">
    <div class="form-container" id="form-container">
        <div class="return-btn w-100 align-self-start ml-5 mt-5">
            <a href="<?= site_url('visitors_pending/form/step2'); ?>" class="btn btn-icon-split">
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
        <?= form_open_multipart('visitors_pending/process_step3', array(
            'class' => 'px-5',
        )) ?>
            <div class="col w-100">
                <div class="row-lg-12">
                    <label for="company">Company (Optional)</label>
                    <input type="text" name="company" class="form-control <?= (form_error('company')) ? 'is-invalid' : '' ?>" placeholder="" value="<?= isset($this->session->userdata('step3_data')['company']) ? $this->session->userdata('step3_data')['company'] : set_value('company'); ?>">
                    <div class="invalid-feedback"><?php echo form_error('company'); ?></div>
                </div>
                <div class="row-lg-12">
                    <label for="id_type">ID Type <span class="text-danger">*</span></label>
                    <select name="id_type" id="id_type" class="form-control">
                        <option value="" selected>Select ID Type</option>
                        <option value="National ID" <?= set_select('id_type', 'National ID', isset($this->session->userdata('step3_data')['id_type']) && $this->session->userdata('step3_data')['id_type'] == 'National ID'); ?>>National ID</option>
                        <option value="ePhilID" <?= set_select('id_type', 'ePhilID', isset($this->session->userdata('step3_data')['id_type']) && $this->session->userdata('step3_data')['id_type'] == 'ePhilID'); ?>>Philippine Identification (PhilID / ePhilID)</option>
                        <option value="SSS" <?= set_select('id_type', 'SSS', isset($this->session->userdata('step3_data')['id_type']) && $this->session->userdata('step3_data')['id_type'] == 'SSS'); ?>>SSS / UMID</option>
                        <option value="Passport" <?= set_select('id_type', 'Passport', isset($this->session->userdata('step3_data')['id_type']) && $this->session->userdata('step3_data')['id_type'] == 'Passport'); ?>>Passport</option>
                        <option value="Driver's License" <?= set_select('id_type', 'Driver\'s License', isset($this->session->userdata('step3_data')['id_type']) && $this->session->userdata('step3_data')['id_type'] == 'Driver\'s License'); ?>>Driver's License</option>
                        <option value="PWD ID" <?= set_select('id_type', 'PWD ID', isset($this->session->userdata('step3_data')['id_type']) && $this->session->userdata('step3_data')['id_type'] == 'PWD ID'); ?>>PWD ID</option>
                        <option value="Barangay ID" <?= set_select('id_type', 'Barangay ID', isset($this->session->userdata('step3_data')['id_type']) && $this->session->userdata('step3_data')['id_type'] == 'Barangay ID'); ?>>Barangay ID</option>
                        <option value="Phil-health ID" <?= set_select('id_type', 'Phil-health ID', isset($this->session->userdata('step3_data')['id_type']) && $this->session->userdata('step3_data')['id_type'] == 'Phil-health ID'); ?>>Phil-health ID</option>
                    </select>
                    <div class="invalid-feedback"><?php echo form_error('id_type'); ?></div>
                </div>

                <div class="row-lg-12 id-fields">
                    <label for="id_number">ID Number <span class="text-danger">*</span></label>
                    <input type="text" name="id_number" class="form-control <?= (form_error('id_number')) ? 'is-invalid' : '' ?>" placeholder="" value="<?= isset($this->session->userdata('step3_data')['id_number']) ? $this->session->userdata('step3_data')['id_number'] : set_value('id_number'); ?>">
                    <div class="invalid-feedback"><?php echo form_error('id_number'); ?></div>
                </div> 

                <!-- div class="row-lg-12 id-fields">
                    <label for="id_front">ID (Front) <span class="text-danger">*</span></label>
                    <?php if (form_error('id_front_base64')): ?>
                        <span class="text-danger"><?= form_error('id_front_base64') ?></span>
                    <?php endif; ?>
                    <div class="form-file id-back">
                        <label for="id_front" class="id-label <?= (form_error('id_front_base64')) ? 'is-invalid' : '' ?>">Photo of Front ID
                            <span class="id-upload-button">Choose File</span>
                            <input type="file" name="id_front" class="id_input" id="id_front" accept="image/*" onchange="encodeImageFileAsURL(this, 'id_front_base64', 'id_front_preview')">
                            <input type="hidden" name="id_front_base64" id="id_front_base64" value="<?= isset($this->session->userdata('step3_data')['id_front_base64']) ? $this->session->userdata('step3_data')['id_front_base64'] : '' ?>">
                        </label>
                        <img id="id_front_preview" class="preview-image id-fields" src="<?= isset($this->session->userdata('step3_data')['id_front_base64']) ? $this->session->userdata('step3_data')['id_front_base64'] : base_url('assets/images/id-card-color-icon.png'); ?>">
                        <div class="invalid-feedback"><?php echo form_error('id_front_base64'); ?></div>
                    </div>
                </div -->

                <!-- div class="row-lg-12 id-fields">
                    <label for="id_back">ID (Back) <span class="text-danger">*</span></label>
                    <?php if (form_error('id_back_base64')): ?>
                        <span class="text-danger"><?= form_error('id_back_base64') ?></span>
                    <?php endif; ?>
                    <div class="form-file id-back">
                    <label for="id_back" class="id-label <?= (form_error('id_back_base64')) ? 'is-invalid' : '' ?>">Photo of ID (back)
                        <span class="id-upload-button">Choose File</span>
                        <input type="file" class="id_input" name="id_back" id="id_back" accept="image/*" onchange="encodeImageFileAsURL(this, 'id_back_base64', 'id_back_preview')">
                        <input type="hidden" name="id_back_base64" id="id_back_base64" value="<?= isset($this->session->userdata('step3_data')['id_back_base64']) ? $this->session->userdata('step3_data')['id_back_base64'] : '' ?>">
                    </label>
                    <img id="id_back_preview" class="preview-image id-fields" src="<?= isset($this->session->userdata('step3_data')['id_back_base64']) ? $this->session->userdata('step3_data')['id_back_base64'] : base_url('assets/images/id-card-color-icon-back.png') ?>">
                    </div>
                </div -->
            </div>
            <div class="d-flex justify-content-center test">
                <button type="submit" id="submit" class="mt-5" onclick="displayLoader()"><span id="loader" class="spinner-border spinner-border-sm" style="display: none;"></span> Next</button>
            </div>
        <?= form_close() ?>
        <?php $this->load->view('templates/progressbar', array('progress' => $progress)); ?>
    </div>
<div class="image-container" id="image-container">

</div>
<script src="<?= base_url('assets/js/form.js'); ?>"></script>
