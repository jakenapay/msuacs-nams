<div class="step2-container">
    <div class="form-container">
        <div class="return-btn w-100 align-self-start ml-5 mt-5">
            <a href="<?= site_url('visitors_pending/form/review'); ?>" class="btn btn-icon-split">
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
        <h1>OTP Verification</h1>
        <hr>
        <!-- <p><?= $this->session->userdata('otp'); ?></p> -->
        <?= form_open('visitors_pending/form/verify_otp', array(
            'class' => 'px-5'
        )) ?>
        <div class="col w-100 px-5 mb-5">
            <div class="row-lg-12">
                <input type="text" name="otp_number" class="form-control text-center <?= (form_error('otp_number')) ? 'is-invalid' : '' ?>" placeholder="Enter your OTP number" value="<?= set_value('otp_number') ?>">
                <?php if (form_error('otp_number')): ?>
                    <span class="text-danger"><?= form_error('otp_number') ?></span>
                <?php endif; ?>
            </div>
        </div>
            <div class="d-flex justify-content-center">
                <button type="submit" id="submit" class="take-photo-btn btn btn-primary mt-5" onclick="displayLoader()"><span id="loader" class="spinner-border spinner-border-sm" style="display: none;"></span> Next</button>
            </div>  
        <?= form_close() ?>
        <?php $this->load->view('templates/progressbar', array('progress' => $progress)); ?>
        <!-- Resend OTP Button -->
        <div class="mt-3">
            <a href="<?= site_url('visitors_pending/form/resend_otp') ?>" class="btn btn-secondary w-100">Resend OTP</a>
        </div>
    </div>

    <div class="image-container">
        <!-- Placeholder for image or other content -->
    </div>
</div>