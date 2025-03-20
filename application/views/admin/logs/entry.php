
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

    <div class="input-group justify-content-end">      
        <div class="mt-3 mb-2 shadow dashboard-card">
            <div id="dateRange" class="location-selector border-0 px-3 py-2" style="margin-right: 0;">
                <select name="location" id="location" class="border-0">
                    <option value="all" selected>All Locations</option>
                    <?php foreach($locations as $location):?>
                        <option value="<?php echo $location->name?>"><?php echo $location->name?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
    </div>

    <!-- End of row show -->

        <!-- Export exel and PDF -->

            <div class="shadow mb-4 mt-2">
            <div class="card-body">
                <div class="table-responsive-sm">
                <table class="table table-striped" id="entryLogsTable" width="100%" cellspacing="0"></table>
                </div>
            </div>
            </div>
    </div>
    <!-- /.container-fluid -->
    </div>
<!-- End of Main Content -->