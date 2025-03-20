$(document).ready( function(){
    //Datatables for the records of Declined Visit Requests
    $('#visitorsDeclinedTable').DataTable({
        responsive: true,
        serverSide: true,
        dom: '<"row"<"col-sm-4"l><"col-sm-4 text-center"B><"col-sm-4"f>>rtip',
        ajax: {
            url: site_url + 'admin/visit_management/visitors_declined/list',
            type: 'GET',
            dataType: 'json',
        },
        autoWidth: false,
        pageLength: 10,
        aaSorting: [
            [0, "desc"]
        ],
        columns: [
            {
                title: 'ID',
                data: 'id',
                class: 'text-center'
            },
            {
                title: 'First Name',
                data: 'first_name',
                class: 'text-center'
            },
            {
                title: 'Last Name',
                data: 'last_name',
                class: 'text-center'
            },
            {
                title: 'Purpose of Visit',
                data: 'visit_purpose',
                class: 'text-center'
            },
            {
                title: 'Date of Visit',
                data: 'visit_date',
                class: 'text-center',
                render: function(data, type, row, meta) {
                    let date = new Date(data); // Convert to Date object
                    let formattedDate = date.toLocaleDateString('en-US', {
                        month: 'short',    // Aug
                        day: 'numeric',    // 24
                        year: 'numeric'    // 2022
                    });
                    return formattedDate;
                }
            },
            {
                title: 'Time of Visit',
                data: 'visit_time',
                class: 'text-center',
                render: function(data, type, row, meta) {
                    // Split the time string into components
                    let timeParts = data.split(':');
                    let hours = parseInt(timeParts[0], 10);
                    let minutes = timeParts[1];
                    let seconds = timeParts[2];
                    
                    // Determine AM/PM
                    let ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
            
                    // Return formatted time
                    return `${hours}:${minutes} ${ampm}`;
                }
            }, 
            {
                title: 'Reason of Decline',
                data: 'decline_reason',
                class: 'text-center'
            },
            {
                title: 'Date Declined',
                data: 'date',
                class: 'text-center',
                render: function(data, type, row, meta) {
                    let date = new Date(data); // Convert to Date object
                    let formattedDate = date.toLocaleDateString('en-US', {
                        month: 'short',    // Aug
                        day: 'numeric',    // 24
                        year: 'numeric'    // 2022
                    });
                    return formattedDate;
                }
            },            
            {
                title: 'Status',
                data: 'status',
                class: 'text-center',
                render: function(data, type, row, meta) {
                    let status = '<span class="badge badge-warning">Declined<span>';
                    return status;
                }
            },
            {
                title: "Options",
                data: "id",
                className: "text-center",
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    let editLink = `<div class="btn-group dashboard-btn"><button title="Edit" id="edit-btn" class="btn btn-sm btn-primary edit-btn" data-id="${data}"><i class="fas fa-eye"></i> </button>`;
                    let deleteLink = `<button title="Delete" id="delete-btn" class="btn btn-sm btn-danger delete-btn" data-id="${data}"><i class="fas fa-trash"></i> </button></div>`;
                        
                    return editLink + deleteLink;
                }
            }
        ],
        language: {
            search: "<i class=\"fa fa-search\"></i> Search",
            searchPlaceholder: "First Name, Last Name, Email, Phone Number",
        },
        scrollX: "30rem",
        buttons: [
            {
                extend: 'csvHtml5',
                className: "text-dark bg-white border-0 mt-3 mr-2",
                text: "<i class=\"fa fa-file-csv\"></i> CSV",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7],
                    rows: ':visible',
                },
                title: 'VisitorsDeclined_CSV-' + getFormattedDate()  // Add a custom file name for CSV
            },
            {
                extend: 'excelHtml5',
                className: "text-dark bg-white border-0 mt-3",
                text: "<i class=\"fa fa-file-excel\"></i> Excel",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7],
                    rows: ':visible',
                },
                title: 'VisitorsDeclined_Excel-' + getFormattedDate()  // Add a custom file name for Excel
            },
        ],
        createdRow: function(row, data, index) {
            if (data.is_banned == true)
                $(row).addClass("bg-danger text-white");
        }
    });

    /****************************IZIMODAL**************************************** */    
    // Handle edit button click
    $('#visitorsDeclinedTable').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
           //Edit Modal Configurations
        $("#modal-edit").iziModal({
            title: 'Declined Visitor Information',
            icon: "fas fa-fw fa-user",
            subtitle: 'View Visitor Information',
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
            url: site_url + 'admin/visit_management/visitors_declined/view/' + id,
            type: 'GET',
            success: function(response) {
                $("#modal-edit").iziModal('setContent', response);
                $("#modal-edit").iziModal('open');
                // Initialize your script here
                initializeEditForm();
                initializePhoneNumberFormatting();
            },
            error: function() {
                iziToast.error({
                    title: 'Error',
                    message: 'Failed to load the modal content.'
                });
            }
        });
    });

    // Edit Button Handle form submission via AJAX
    $(document).on('submit', '#editVisitorForm', function(e) {
        e.preventDefault(); // Prevent default form submission
        $('#modal-edit').iziModal('close');
        $('#visitorsActiveTable').DataTable().ajax.reload();
    });

/****************************DELETE FUNCTION**************************************** */    

    // Handle delete button click
    $('#visitorsDeclinedTable').on('click', '.delete-btn', function(e) {
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
                        url: site_url + "admin/visit_management/visitors_declined/delete/" + id,
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
                                $('#visitorsDeclinedTable').DataTable().ajax.reload();
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