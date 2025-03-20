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
            <a id="add-btn" class="btn btn-sm btn-icon-split mb-4 shadow-sm">
                <span class="icon text-white">
                    <i class="fas fa-plus-circle"></i>
                </span>
                <span class="text text-white d-none d-sm-block">Add New</span>
            </a>
            <!-- <button type="button" class="btn text-white btn-sm btn-icon-split mb-4 shadow-sm" id="excel-import-btn"
                disabled>
                <span class="icon text-white-600 sm-bg-primary">
                    <i class="fas fa-file-import"></i>
                </span>
                <span class="text d-none d-sm-block">Excel Import</span>
            </button>
            <button type="button" class="btn text-white btn-sm btn-icon-split mb-4 shadow-sm" id="data-transfer-btn"
                disabled>
                <span class="icon text-white-600 sm-bg-primary">
                    <i class="fas fa-solid fa-database"></i>
                </span>
                <span class="text d-none d-sm-block">Import Data</span>
            </button>
            <a href="<?= base_url('admin'); ?>"
                class="btn btn-secondary btn-sm btn-icon-split mb-4 float-right shadow-sm">
                <span class="icon text-white">
                    <i class="fas fa-chevron-left"></i>
                </span>
                <span class="text">Back</span>
            </a> -->
        </div>

        <div class="col-sm-12 col-md-12 col-lg-9">

        </div>
    </div>
    <!-- End of row show -->


    <div class="shadow mb-4 mt-2">
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table table-striped" id="idTable" width="100%" cellspacing="0"></table>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Export exel and PDF -->
<div id="modal-edit" class="iziModal"></div>
<div id="modal-add" class="iziModal"></div>
<div id="modal-excel-import" class="iziModal"></div>
<div id="modal-delete" class="iziModal"></div>
<div id="modal-data-import" class="iziModal"></div>
<!-- End of Main Content -->

<script>

    $(document).ready(function () {
        $("#modal-add").iziModal({
            title: 'Add New Log',
            icon: "fas fa-fw fa-user-graduate",
            subtitle: 'Add New Log',
            transitionIn: 'fadeInUp',
            transitionOut: 'fadeOutDown',
            width: 700,
            headerColor: "linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(94,4,4,1) 0%, rgba(94,4,4,0.6979166666666667) 96%)",
            fullscreen: false,
            onClosed: function () {
                $("izimodal").iziModal("destroy");
                $("izimodal").remove();
            }
        });

        // Handle "Other" selection immediately on change
        $('#remarks').on('change', function () {
            if ($(this).val() === 'other') {
                const customRemark = prompt('Please enter specific remarks:');

                if (customRemark && customRemark.trim() !== '') {
                    // Add the custom remark dynamically
                    if ($('#remarks option[value="' + customRemark + '"]').length === 0) {
                        $('#remarks').append(`<option value="${customRemark}" selected>${customRemark}</option>`);
                    }
                    $('#remarks').val(customRemark);
                } else {
                    // Reset to default if no input is provided
                    $(this).val('');
                    iziToast.warning({
                        title: 'Warning',
                        message: 'You must provide a valid remark!',
                        position: 'topRight'
                    });
                }
            }
        });

        // Handle add button click
        $('#add-row').on('click', '#add-btn', function () {
            $.ajax({
                url: site_url + 'admin/security/add_new_id_log',
                type: 'GET',
                success: function (response) {
                    $("#modal-add").iziModal('setContent', response);
                    $("#modal-add").iziModal('open');
                },
                error: function () {
                    iziToast.error({
                        title: 'Error',
                        message: 'Failed to load the modal content.'
                    });
                }
            });
        });


        // Add Button Handle form submission via AJAX
        // $(document).on('submit', '#addIdLog', function (e) {
        //     e.preventDefault(); // Prevent default form submission

        //     var formData = new FormData(this);

        //     $.ajax({
        //         type: 'POST',
        //         url: $(this).attr('action'),
        //         data: formData,
        //         cache: false,
        //         contentType: false,
        //         processData: false,
        //         success: function (response) {
        //             const serverResponse = JSON.parse(response)

        //             if (serverResponse.status == 200) {
        //                 success(serverResponse.message, "fa fa-check-circle")
        //                 $('#modal-add').iziModal('close');
        //                 $('#idTable').DataTable().ajax.reload();
        //             }
        //             else {
        //                 const errors = serverResponse.message;
        //                 console.log(errors);

        //                 for (let key in errors) {
        //                     if (errors.hasOwnProperty(key)) {
        //                         danger(errors[key], "fa fa-exclamation-triangle");
        //                     }
        //                     else {
        //                         danger(errors, "fa fa-exclamation-triangle")
        //                     }
        //                 }
        //             }
        //         },
        //         error: function () {
        //             iziToast.error({
        //                 title: 'Error',
        //                 message: 'An error occurred while submitting the form.',
        //             });
        //         }
        //     });
        // });

        // ✅ Handle form submission with AJAX and "Other" validation
        $(document).on('submit', '#addIdLog', function (e) {
            e.preventDefault();

            const remarks = $('#remarks').val();

            // If remarks is "other", prompt for specific remarks before AJAX submission
            if (remarks === 'other') {
                const customRemark = prompt('Please enter specific remarks:');

                if (!customRemark || customRemark.trim() === '') {
                    iziToast.warning({
                        title: 'Warning',
                        message: 'You must provide a valid remark!',
                        position: 'topRight'
                    });
                    return;  // Stop form submission if no valid remark is provided
                }

                // Add the custom remark dynamically
                if ($('#remarks option[value="' + customRemark + '"]').length === 0) {
                    $('#remarks').append(`<option value="${customRemark}" selected>${customRemark}</option>`);
                }
                $('#remarks').val(customRemark);
            }

            // Proceed with AJAX submission
            const formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    try {
                        const serverResponse = JSON.parse(response);

                        if (serverResponse.status === 200) {
                            iziToast.success({
                                title: 'Success',
                                message: serverResponse.message,
                                position: 'topRight',
                                backgroundColor: '#4CAF50',     // Green background for success
                                color: '#fff',                  // White text color
                                timeout: 5000,
                                zindex: 999999,                 // Ensure it's on top
                            });

                            // Close the modal
                            $('#modal-add').iziModal('close');

                            // Reload the DataTable
                            $('#idTable').DataTable().ajax.reload();
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: serverResponse.message || 'Failed to add log',
                                position: 'topRight'
                            });
                        }
                    } catch (error) {
                        console.error("Parsing error:", error);
                        iziToast.error({
                            title: 'Error',
                            message: 'Invalid server response',
                            position: 'topRight'
                        });
                    }
                },
                error: function (xhr) {
                    console.error("AJAX error:", xhr.responseText);
                    iziToast.error({
                        title: 'Error',
                        message: 'An error occurred while submitting the form.',
                        position: 'topRight'
                    });
                }
            });
        });

        $("#modal-excel-import").iziModal({
            title: 'Import New Student with Excel',
            subtitle: 'Add New Students Record via Import',
            icon: "fas fa-file-excel",
            headerColor: "linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(94,4,4,1) 0%, rgba(94,4,4,0.6979166666666667) 96%)",
            width: 1200,
            fullscreen: true,
            padding: 20,
            onOpening: function (modal) {
                modal.startLoading();
                $.get(site_url + 'admin/user_management/students/excel_import_form', function (data) {
                    $("#modal-excel-import").iziModal('setContent', data);
                    modal.stopLoading();
                }).fail(function () {
                    modal.stopLoading();
                    iziToast.error({
                        title: 'Error',
                        message: 'Failed to load the Excel import form.'
                    });
                });
            }
        });

        $('#excel-import-btn').on('click', function () {
            $("#modal-excel-import").iziModal('open');
        });

        // Handle form submission
        $(document).on('submit', '#excelImportForm', function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    const serverResponse = JSON.parse(response);
                    if (serverResponse.status == 200) {
                        success(serverResponse.message, "fa fa-check-circle");
                        $('#modal-excel-import').iziModal('close');
                        $('#studentsTable').DataTable().ajax.reload();
                    } else {
                        danger(serverResponse.message, "fa fa-exclamation-triangle");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", status, error);
                    console.log("Response text:", xhr.responseText);
                    iziToast.error({
                        title: 'Error',
                        message: 'An error occurred while uploading the file.',
                    });
                }
            });
        });

    });
</script>