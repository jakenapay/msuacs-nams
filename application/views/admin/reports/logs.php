
<!-- Begin Page Content -->
<div class="container">
        <!-- Page Heading -->
        <div class="row">
        <div class="col-lg">
            <!-- <h1 class="h3 mb-2 text-center"><?= $title; ?></h1> -->
        </div>
        </div>

        <div class="col-lg-5 offset-lg-4">
            <?= $this->session->flashdata('message'); ?>
        </div>

        <div class="row">
            <div class="col-lg-2 mt-2">
                <a href="<?= base_url('admin'); ?>" class="btn btn-sm btn-secondary btn-icon-split shadow-sm mb-4 float-right float-sm-left">
                    <span class="icon text-white"><i class="fas fa-chevron-left"></i></span>
                    <span class="text">Back</span>
                </a>    
            </div>

            <div class="col-sm-12 col-md-12 col-lg-9">

            </div>
        </div>
        <!-- End of row show -->

        
        <div class="shadow mb-4 mt-2">
            <div class="card-header pt-4 bg-white text-center border-0">
                <h5>Search Logs History</h5>
            </div>
            <div class="card-body mx-5">
                <form id="logFilterForm">
                    <div class="input-group reportInput justify-content-center">
                        <!-- Date Range -->
                        <div class="mb-3">
                            <div id="dateRange">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                        </div>

                        <!-- User Type -->
                        <div class="mb-3 inputs">
                            <select class="form-control" id="userType" name="userType">
                                <option value="all">All Groups</option>
                                <option value="Student">Student</option>
                                <option value="Faculty">Faculty</option>
                                <option value="Staff">Staff</option>
                                <option value="Resident">Resident</option>
                                <option value="Guest">Guest</option>
                                <option value="Visitor">Visitor</option>
                            </select>
                        </div>

                        <!-- Location -->
                        <div class="mb-3 inputs">
                            <select class="form-control" id="location" name="location">
                                <option value="all">All Locations</option>
                                <?php foreach($locations as $location): ?>
                                    <option value="<?= $location->name ?>"><?= $location->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- College -->
                        <div class="mb-3 inputs">
                            <select class="form-control" id="college" name="college">
                                <option value="">All Colleges</option>
                                <?php foreach($colleges as $college): ?>
                                    <option value="<?= $college->name ?>"><?= $college->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Department -->
                        <div class="mb-3 inputs">
                            <select class="form-control" id="department" name="department">
                                <option value="">All Departments</option>
                            </select>
                        </div>

                        <!-- Programs -->
                        <div class="mb-3 inputs">
                            <select class="form-control" id="program" name="program">
                                <option value="">All Programs</option>
                            </select>
                        </div>

                        <!-- Offices -->
                        <div class="mb-3 inputs">
                            <select class="form-control" id="office" name="offce">
                                <option value="all">All Offices</option>
                                <?php foreach ($offices as $office): ?>
                                    <option value="<?= $office->name ?>"> <?= $office->name ?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        
                        <!-- Type -->
                        <div class="mb-3 inputs">
                            <select class="form-control" id="type" name="type">
                                <option value="entry_logs" selected>Entry</option>
                                <option value="exit_logs">Exit</option>
                            </select>
                        </div>

                        <!-- Entry/Exit -->
                        <div class="mb-3 align-self-end">
                            <button type="submit" class="btn btn-primary" id="view-btn">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- DataTable -->
        <div class="shadow mb-4 mt-2" id="tableParent" style="display: none;">
            <div class="card-header pt-4 bg-white border-0">
                <h5>Search Results</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive-sm logsTable">
                    <table class="table table-striped" id="logsTable" width="100%" cellspacing="0"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Export exel and PDF -->
<div id="modal-edit" class="iziModal"></div>
<div id="modal-delete" class="iziModal"></div>
<!-- End of Main Content -->
<script>
    $(document).on('change', '#userType', function() {
        var userType = $(this).val();
        if(userType == 'Visitor' || userType == 'Resident' || userType == 'Guest'){
            $('#college').hide();
            $('#college').val('');
            $('#department').hide();
            $('#department').val('');
            $('#program').hide();
            $('#program').val('');
            $('#office').hide();
            $('#office').val('');
        }
        else if(userType == 'Staff'){
            $('#college').hide();
            $('#college').val('');
            $('#department').hide();
            $('#department').val('');
            $('#program').hide();
            $('#program').val('');
            $('#office').show('');
            $('#office').val('all');
        }
        else if(userType == 'Faculty'){
            $('#college').show();
            $('#department').show();
            $('#program').hide();
            $('#program').val('');
            $('#office').hide();
            $('#office').val('');
        }
        else if(userType == 'Student' || userType == 'all'){
            $('#college').show();
            $('#department').show();
            $('#program').show();
            // $('#college').val('all');
            // $('#department').val('all');
            // $('#program').val('all');
        }
    });

    $(document).on('change', '#college', function() {
        var collegeId = $(this).val();
        if(collegeId) {
            $.ajax({
                url: site_url + 'admin/user_management/get/department_by_college/' + collegeId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#department').empty();
                    $('#department').append('<option value="">All Departments</option>');
                    $.each(data, function(key, value) {
                        $('#department').append('<option value="'+ value.name +'">'+ value.name +'</option>');
                        
                    });
                    $('#program').empty();
                    $('#program').append('<option value="">Select Programs</option>');
                }
            });
        } else {
            $('#department').empty();
            $('#department').append('<option value="">Select Departments</option>');
            $('#program').empty();
            $('#program').append('<option value="">Select Program</option>');
        }
    });
    $(document).on('change', '#department', function() {
        var departmentId = $(this).val();
        if(departmentId) {
            $.ajax({
                url: site_url + 'admin/user_management/get/programs_by_department/' + departmentId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#program').empty();
                    $('#program').append('<option value="">All Program</option>');
                    $.each(data, function(key, value) {
                        $('#program').append('<option value="'+ value.name +'">'+ value.name +' </option>');
                    });
                }
            });
        } else {
            $('#program').empty();
            $('#program').append('<option value="">Select Program</option>');
        }
    });
</script>