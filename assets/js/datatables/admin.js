$(document).ready(function() {    
    $('#adminsTable').DataTable({
        responsive: true,
        dom: '<"row"<"col-sm-4"l><"col-sm-4 text-center"B><"col-sm-4"f>>rtip',
        ajax: {
            url: site_url + 'admin/admin_management/accounts/list', // Server-side data endpoint
            type: 'GET',
            dataType: 'json',
            dataSrc: 'data', // 'data' key in the JSON response
        },
        autoWidth: false,
        pageLength: 10,
        sPaginationType: "full_numbers",
        columns: [
            { 
                title: 'ID',
                data: 'id',
                class: 'text-center',
            },
            { 
                title: 'Name',
                data: 'username',
                class: 'text-center',
            },
            { 
                title: 'Email',
                data: 'email',
                class: 'text-center',
            },
            // { 
            //     title: 'Permissions',
            //     data: 'roles',
            //     class: 'text-center',
            //     render: function(data, type, row, meta){
            //         if (data) {
            //             return data.replace(/,/g, ', '); // Add space after commas
            //         } else {
            //             return ''; // No roles assigned
            //         }
            //     }
            // },
            {
                title: "Options",
                data: "id",
                className: "text-center",
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    let editLink = `<div class="btn-group dashboard-btn"><button title="Edit" id="edit-btn" class="btn btn-sm btn-primary edit-btn" data-id="${data}"><i class="fas fa-eye"></i> </button>`;
                    let viewLink = `<div class="btn-group dashboard-btn"><button title="View" id="view-btn" class="btn btn-sm btn-primary view-btn" data-id="${data}"><i class="fas fa-eye"></i> </button>`;
                    let deleteLink = `<button title="Delete" id="delete-btn" class="btn btn-sm btn-danger delete-btn" data-id="${data}"><i class="fas fa-trash"></i> </button></div>`;
                    
                    // if (data != currentAdminId) {
                    //     return viewLink;
                    // } else {
                        return editLink + deleteLink; // No buttons if the admin ID does not match
                    // }
                }
            }
        ],
        language: {
            search: "<i class=\"fa fa-search\"></i> Search",
            searchPlaceholder: "Name, ID",
        },
        scrollX: "30rem",
        buttons: [
            {
                extend: 'csvHtml5',
                className: "text-dark bg-white border-0 mt-3 mr-2",
                text: "<i class=\"fa fa-file-csv\"></i> CSV",
                exportOptions: {
                    columns: [1, 2, 3],
                    rows: ':visible',
                },
                title: 'Admin-CSV-' + getFormattedDate() 
            },
            {
                extend: 'excelHtml5',
                className: "text-dark bg-white border-0 mr-2",
                text: "<i class=\"fa fa-file-excel\"></i> Excel",
                exportOptions: {
                    columns: [1, 2, 3],
                    rows: ':visible',
                },
                title: 'Admin-Excel-' + getFormattedDate()
            },
        ],
    });

    /****************************IZIMODAL**************************************** */    

    // Handle edit button click
    $('#adminsTable').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
           //Edit Modal Configurations
        $("#modal-edit").iziModal({
            title: 'Admin Information',
            icon: "fas fa-fw fa-user-shield",
            subtitle: 'Edit Admin Information',
            transitionIn: 'fadeInUp',
            transitionOut: 'fadeOutDown',
            width: 700,
            headerColor: "linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(94,4,4,1) 0%, rgba(94,4,4,0.6979166666666667) 96%)",
            fullscreen: true,
            onClosed: function() {
                $("izimodal").iziModal("destroy");
                $("izimodal").remove();
            }
        });
        $.ajax({
            url: site_url + 'admin/admin_management/accounts/edit/view/' + id,
            type: 'GET',
            success: function(response) {
                $("#modal-edit").iziModal('setContent', response);
                $("#modal-edit").iziModal('open');
                // Initialize your script here
            },
            error: function() {
                iziToast.error({
                    title: 'Error',
                    message: 'Failed to load the modal content.'
                });
            }
        });
    });

    $(document).on('click', '#edit-capture-button', editTakeSnapshot);
    $(document).on('click', '#edit-reset-button', editRemoveSnapshot);
    $(document).on('change', '#edit-uploadImage', editImagePreview);


    // Edit Button Handle form submission via AJAX
    $(document).on('submit', '#editAdminForm', function(e) {
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
                    // success(response.message, "fa fa-check-circle");
                    $('#modal-edit').iziModal('close');
                    $('#adminsTable').DataTable().ajax.reload();
                }
                else{
                    const errors = serverResponse.message;
                    for (let key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            danger(errors[key], "fa fa-exclamation-triangle")

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

/****************************VIEW FUNCTION**************************************** */    
    // Handle edit button click
    $('#adminsTable').on('click', '.view-btn', function() {
        var id = $(this).data('id');
           //Edit Modal Configurations
        $("#modal-view").iziModal({
            title: 'Admin Informatiosn',
            icon: "fas fa-fw fa-user-shield",
            subtitle: 'View Admin Information',
            transitionIn: 'fadeInUp',
            transitionOut: 'fadeOutDown',
            width: 700,
            headerColor: "linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(94,4,4,1) 0%, rgba(94,4,4,0.6979166666666667) 96%)",
            fullscreen: true,
            onClosed: function() {
                $("izimodal").iziModal("destroy");
                $("izimodal").remove();
            }
        });
        $.ajax({
            url: site_url + 'admin/admin_management/accounts/view/' + id,
            type: 'GET',
            success: function(response) {
                $("#modal-view").iziModal('setContent', response);
                $("#modal-view").iziModal('open');
                // Initialize your script here
            },
            error: function() {
                iziToast.error({
                    title: 'Error',
                    message: 'Failed to load the modal content.'
                });
            }
        });
    });
/****************************DELETE FUNCTION**************************************** */    


    // Handle delete button click
    $('#adminsTable').on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        iziToast.question({
            message: "Delete Item?",
            backgroundColor: "linear-gradient(30deg, rgb(197, 23, 23), rgb(255, 125, 33))",
            position: "center",
            icon: "fa fa-trash",
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
                        url: site_url + "admin/admin_management/accounts/delete/" + id,
                        type: "POST",
                        data: "id=" + id + "&password=" + inputs[0].value,
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
                                $('#adminsTable').DataTable().ajax.reload();
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

