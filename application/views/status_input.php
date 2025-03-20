<div class="step2-container">
    <div class="form-container">
        <div class="return-btn w-100 align-self-start ml-5 mt-5">
            <a href="<?= site_url('/'); ?>" class="btn btn-icon-split">
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
        <h1>Status Update</h1>
        <hr>
        <?= form_open('form/verify_status', array(
            'class' => 'px-5'
        )) ?>
        <div class="col w-100 px-5 mb-5">
            <div class="row-lg-12">
                <input type="text" name="transaction_number" class="form-control text-center <?= (form_error('transaction_number')) ? 'is-invalid' : '' ?>" placeholder="Enter your request Transaction Number">
                <?php if (form_error('transaction_number')): ?>
                    <span class="text-danger"><?= form_error('transaction_number') ?></span>
                <?php endif; ?>
            </div>
        </div>
            <div class="d-flex justify-content-center">
                <button type="submit" id="submit" class="take-photo-btn btn btn-primary mt-5" onclick="displayLoader()"><span id="loader" class="spinner-border spinner-border-sm" style="display: none;"></span> Next</button>
            </div>  
        <?= form_close() ?>
        <!-- Resend OTP Button -->
    </div>

    <div class="image-container">
        <!-- Placeholder for image or other content -->
    </div>
</div>