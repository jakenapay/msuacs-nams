<div class="step1-container">
    <div class="image-section">
        <!-- Placeholder for future image preview -->
    </div>

    <div class="camera-section">        
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success mx-5 mb-5" role="alert">
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php elseif($this->session->flashdata('danger')): ?>
            <div class="alert alert-danger mx-5 mb-5" role="alert">
                <?php echo $this->session->flashdata('danger'); ?>
            </div>            
        <?php endif; ?>
            
        <h1>Take a picture to proceed</h1>
        <hr>    
        <form method="POST" action="<?= site_url('visitors_pending/process_step1') ?>">
            <?= form_error('visitor_image', '<small class="text-danger pl-3">', '</small>') ?>
            <div class="video-wrapper">
                <video id="video" class="<?= form_error('visitor_image') ? 'form-error' : '' ?>" style="<?= $this->session->userdata('step1_data') ? 'display: none;' : '' ?>" autoplay></video>
            </div>
            <canvas id="canvas" style="display: none;"></canvas>
            <input type="text" name="visitor_image" id="imageInput" style="display: none;" value="<?= $this->session->userdata('step1_data') ?>">
            <div class="capture-buttons">
                <button type="button" id="capture-button" onclick="takeSnapshot()" class="btn btn-primary" style="<?= $this->session->userdata('step1_data') ? 'display: none;' : '' ?>">Take Snapshot</button>
                <button type="button" id="reset-button" onclick="removeSnapshot()" class="btn btn-danger ml-2" style="<?= $this->session->userdata('step1_data') ? '' : 'display: none;' ?>">Reset</button>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" id="submit" class="mt-5 mb-5" onclick="displayLoader()"><span id="loader" class="spinner-border spinner-border-sm" style="display: none;"></span> Next</button>
            </div>
        </form>
        <?php $this->load->view('templates/progressbar', array('progress' => $progress)); ?>
    </div>
</div>
<script src="<?= base_url('assets/js/camera.js') ?>"></script>



