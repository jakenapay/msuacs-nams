
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
                        <span class="text text-white d-none d-sm-block">Add New Visitor Card</span>
                    </a>
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
                <div class="table-responsive">
                    <table class="table table-striped" id="collegesTable" width="100%" cellspacing="0"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Export exel and PDF -->
<div id="modal-edit" class="iziModal"></div>
<div id="modal-add" class="iziModal"></div>
<div id="modal-delete" class="iziModal"></div>
<!-- End of Main Content -->

<script>
$(document).ready(function(){
    $("#modal-add").iziModal({
        title: 'Register College',
        icon: "fas fa-fw fa-university",
        subtitle: 'Add New College',
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
            url: site_url + 'admin/configurations/colleges/add/view',
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
    $(document).on('submit', '#addCollegeForm', function(e) {
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
                    $('#collegesTable').DataTable().ajax.reload();
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
});
</script>