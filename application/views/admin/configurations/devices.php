
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
                        <span class="text text-white d-none d-sm-block">Add New Device</span>
                    </a>
                    <button type="button" class="btn text-white btn-sm btn-icon-split mb-4 shadow-sm" id="toggle-mode-btn">
                        <span class="icon text-white-600 sm-bg-primary">
                            <i class="fas fa-laptop"></i>
                        </span>
                        <span class="text d-none d-sm-block">Toggle Device Mode</span>
                    </button>
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
                    <table class="table table-striped" id="devicesTable" width="100%" cellspacing="0"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Export exel and PDF -->
<div id="modal-edit" class="iziModal"></div>
<div id="modal-add" class="iziModal"></div>
<div id="modal-delete" class="iziModal"></div>
<div id="modal-toggle-mode" class="iziModal"></div>
<!-- End of Main Content -->

<script>
$(document).ready(function(){
    $("#modal-add").iziModal({
        title: 'Register Device',
        icon: "fas fa-fw fa-laptop",
        subtitle: 'Add New Device',
        transitionIn: 'fadeInUp',
        transitionOut: 'fadeOutDown',
        width: 700,
        headerColor: "linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(94,4,4,1) 0%, rgba(94,4,4,0.6979166666666667) 96%)",
        fullscreen: false,
        onClosed: function() {
            $("izimodal").iziModal("destroy");
            $("izimodal").remove();
        }
    });

    // Handle add button click
    $('#add-row').on('click', '#add-btn', function() {
        $.ajax({
            url: site_url + 'admin/configurations/devices/add/view',
            type: 'GET',
            success: function(response) {
                $("#modal-add").iziModal('setContent', response);
                $("#modal-add").iziModal('open');
            },
            error: function() {
                iziToast.error({
                    title: 'Error',
                    message: 'Failed to load the modal content.'
                });
            }
        });
    });

    // Add Button Handle form submission via AJAX
    $(document).on('submit', '#addDeviceForm', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                const serverResponse = JSON.parse(response)

                if(serverResponse.status == 200){
                    success(serverResponse.message, "fa fa-check-circle")                        
                    $('#modal-add').iziModal('close');
                    $('#devicesTable').DataTable().ajax.reload();
                }
                else{
                    const errors = serverResponse.message;
                    console.log(errors);
                    
                    for (let key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            danger(errors[key], "fa fa-exclamation-triangle");
                        }
                        else{
                            danger(errors, "fa fa-exclamation-triangle")
                        }
                    }
                }
            },
            error: function() {
                iziToast.error({
                    title: 'Error',
                    message: 'An error occurred while submitting the form.',
                });
            }
        });
    });

    $("#modal-toggle-mode").iziModal({
        title: 'Toggle Device Mode',
        subtitle: 'Set the Devices mode to Testing or Production Mode.',
        icon: "fas fa-laptop",
        headerColor: "linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(94,4,4,1) 0%, rgba(94,4,4,0.6979166666666667) 96%)",
        width: 800,
        fullscreen: true,
        padding: 20,
        onOpening: function(modal){
            modal.startLoading();
            $.get(site_url + 'admin/configurations/devices/toggle_mode', function(data) {
                $("#modal-toggle-mode").iziModal('setContent', data);
                modal.stopLoading();
            }).fail(function() {
                modal.stopLoading();
                iziToast.error({
                    title: 'Error',
                    message: 'Failed to load the toggle modal.'
                });
            });
        }
    });

    $('#toggle-mode-btn').on('click', function() {
        $("#modal-toggle-mode").iziModal('open');
    });

    // Handle form submission
    $(document).on('submit', '#toggleForm', function(e) {
        e.preventDefault();
        var formData = $('#toggleForm').serialize()

        iziToast.question({
            message: "Password confirmation",
            backgroundColor: "linear-gradient(30deg, rgb(16, 107, 181), rgb(79, 193, 238))",
            position: "center",
            icon: "fa fa-user-shield",
            drag: false,
            timeout: false,
            close: false,
            overlay: true,
            zindex: 1031,
            inputs: [
                ["<input type=\"password\" placeholder=\"Enter password\" class=\"form-control\">"]
            ],
            buttons: [
                ["<button class=\"text-white\">Confirm</button>", function(instance, toast, button, e, inputs) {
                    $.ajax({
                        url: site_url + "admin/configurations/devices/toggle_mode/set",
                        type: "POST",
                        data: formData + "&password=" + inputs[0].value,
                        beforeSend: function() {
                            instance.hide({
                                transitionOut: "fadeOut"
                            }, toast, "button");
                            $("button").attr("disabled", "");
                        },
                        success: function(result) {
                            $("button").removeAttr("disabled");
                            const response = JSON.parse(result);
                            if (response.status == 200) {
                                $("#modal-toggle-mode").iziModal('close');
                                $('#departmentsTable').DataTable().ajax.reload();
                                success(response.message, "fa fa-check-circle");
                            } else {
                                danger(response.message, "fa fa-exclamation-triangle");
                            }
                        }
                    });
                }, true],
                ["<button class=\"text-white\">Cancel</button>", function(instance, toast, button, e) {
                    instance.hide({
                        transitionOut: "fadeOut"
                    }, toast, "button");
                }]
            ]
        });
    });

});
</script>