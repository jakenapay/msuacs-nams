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


        // ✅ Handle form submission with AJAX and "Other" validation
        $(document).on('submit', '#addIdLog', function (e) {
            e.preventDefault();

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

    });
</script>