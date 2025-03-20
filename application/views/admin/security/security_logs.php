
<!-- Begin Page Content -->
<div class="container">
    <!-- Page Heading -->
    <div class="row">
      <div class="col-lg">
        <h1 class="h3 mb-2 text-center"><?= $title; ?></h1>
      </div>
    </div>

    <div class="col-lg-5 offset-lg-4">
        <?= $this->session->flashdata('message'); ?>
    </div>

    <div class="col">


      <div class="col-sm-12 col-md-12 col-lg-12">
          <?= form_open_multipart('admin/security_logs_filter', array('id' => 'security_filter')); ?>
              <div class="row">
                  <?php $date = date("Y-m-d", strtotime("today"));  ?> 
                    <div class="col-sm-4 col-md-4 col-lg-4 form-check-inline mr-0 mt-2">
                      <label for="start" class="pr-1">From:</label>
                      <input type="date" id="startDate" name="start" placeholder="<?php echo $date ?>" max="<?php echo $date ?>" class="form-control form-control-sm shadow mb-2" required>            
                      <?= form_error('start', '<small class="text-danger pl-3">', '</small>') ?>
                    </div>

                    <div class="col-sm-4 col-md-4 col-lg-4 form-check-inline mr-0 mt-2">
                        <label for="start" class="pr-1">To:</label>
                        <input type="date" id="endDate" name="end" placeholder="<?php echo $date ?>" max="<?php echo $date ?>" class="form-control form-control-sm shadow mb-2" required>  
                        <?= form_error('end', '<small class="text-danger pl-3">', '</small>') ?>
                    </div>

                    <div class="col-sm-2 col-md-2 col-lg-2 mt-2">
                        <button type="submit" id="approve-btn" name="submit" value="Show" class="btn btn-sm btn-success btn-block shadow-sm">Show</button>            
                    </div>
          <?= form_close(); ?>
              <div class="col-sm-2 col-md-2 col-lg-2 mt-2">
                  <button type="reset" id="resetTable" class="btn btn-sm btn-primary btn-block shadow-sm">Reset</button>            
              </div>
        </div>

      </div>
  </div>
    <!-- End of row show -->

        <!-- Export exel and PDF -->

        <div class="shadow mb-4 mt-2">
          <div class="card-body">
            <div class="table-responsive-sm">
              <table class="table table-bordered" id="securityLogsTable" width="100%" cellspacing="0"></table>
            </div>
          </div>
        </div>
  </div>
  <!-- /.container-fluid -->
</div>
<!-- End of Main Content -->