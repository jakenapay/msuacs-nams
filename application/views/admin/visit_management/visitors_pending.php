
<!-- Begin Page Content -->
<div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
        <div class="col-lg">
            <h1 class="h3 mb-2 text-center"><?= $title; ?></h1>
        </div>
        </div>

        <div class="col-lg-5 offset-lg-4">
            <?= $this->session->flashdata('message'); ?>
        </div>

        <div class="row">
            <div class="col-sm-12 dashboard-btn" id="add-row">
                        <a href="<?= base_url('admin'); ?>" class="btn btn-secondary btn-sm btn-icon-split mb-4 float-right shadow-sm">
                                <span class="icon text-white">
                                <i class="fas fa-chevron-left"></i>
                                </span>
                                <span class="text">Back</span>
                        </a>
                </div>

            <div class="col-sm-12 col-md-12 col-lg-9">

            </div>
        </div>
        <!-- End of row show -->

        
        <div class="shadow mb-4 mt-2">
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table class="table table-striped" id="visitorsPendingTable" width="100%" cellspacing="0"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Export exel and PDF -->
<div id="modal-edit" class="iziModal"></div>
<div id="modal-delete" class="iziModal"></div>
