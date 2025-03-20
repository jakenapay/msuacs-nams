$(document).ready(function() {
    //DATATABLES
    $('#guestsTable').DataTable({
        responsive: true,
        serverSide: true, // server-side processing
        dom: '<"row"<"col-sm-4"l><"col-sm-4 text-center"B><"col-sm-4"f>>rtip',
        ajax: {
            url: site_url + 'admin/user_management/guests/list',
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
                class: 'text-center',
                type: 'numeric'
            },
            {
                title: "Guest Image",
                data: "image",
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    let timestamp = new Date().getTime();
                    if (data !== null) {
                        let imgSrc = `${site_url}${data}?time=${timestamp}`;
                        let imgAlt = 'User Image';
                        let imgStyle = 'border-radius:5%;';
                        let imgWidth = '60';
                        let imgHeight = '60';
                
                        return `<img src="${imgSrc}" alt="${imgAlt}" style="${imgStyle}" width="${imgWidth}" height="${imgHeight}" onerror="this.onerror=null;this.src='${site_url}/assets/images/default.png';" />`;
                    } else {
                        let imgSrc = `${site_url}/assets/images/default.png`;
                        let imgAlt = 'Default Image';
                        let imgStyle = 'border-radius:5%;';
                        let imgWidth = '60';
                        let imgHeight = '60';
                
                        return `<img src="${imgSrc}" alt="${imgAlt}" style="${imgStyle}" width="${imgWidth}" height="${imgHeight}" />`;
                    }
                },
                class: "text-center"
            },
            {
                title: 'RFID',
                data: 'rfid',
                class: 'text-center',
            },
            { 
                title: 'First Name',
                data: 'first_name',
                class: 'text-center',
            },
            { 
                title: 'Last Name',
                data: 'last_name',
                class: 'text-center',
            },
            { 
                title: 'Phone Number',
                data: 'phone_number',
                class: 'text-center',
                orderable: false,
                render: function(data, type, row, meta){
                    let phoneNumber = '+63 ' + data;

                    return phoneNumber;
                }
            },
            { 
                title: 'Dormitory/Building Name',
                data: 'assigned_dormitory', 
                class: 'text-center',
            },
            { 
                title: 'Room Number',
                data: 'room_number', 
                class: 'text-center',
            },
            {
                title: 'Check-In Date & Time',
                data: 'check_in_date',
                class: 'text-center',
                render: function (data, type, row) {
                    if (data) {
                        let date = new Date(data);
                        let options = { year: 'numeric', month: 'short', day: 'numeric' }; // Aug 14, 2024
                        let formattedDate = date.toLocaleDateString('en-US', options);
                        let formattedTime = date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }); // 1:00 PM
                        return formattedDate + ' ' + formattedTime;
                    }
                    return '';
                }
            },
            { 
                title: 'Expected Check-Out Date',
                data: 'check_out_date', 
                class: 'text-center',
                render: function (data, type, row) {
                    if (data) {
                        let date = new Date(data);
                        let options = { year: 'numeric', month: 'short', day: 'numeric' }; // Aug 14, 2024
                        let formattedDate = date.toLocaleDateString('en-US', options);
                        return formattedDate;
                    }
                    return '';
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
                    let banLink = `<button title="Ban" id="ban-btn" class="btn btn-sm btn-warning ban-btn" data-id="${data}"><i class="fas fa-ban"></i> </button>`;
                    let unbanLink = `<button title="Unban" id="unban-btn" class="btn btn-sm btn-success unban-btn" data-id="${data}"><i class="fas fa-unlock"></i> </button>`;
                    let deleteLink = `<button title="Delete" id="delete-btn" class="btn btn-sm btn-danger delete-btn" data-id="${data}"><i class="fas fa-trash"></i> </button></div>`;
                    
                    if(row.is_banned == true){
                        return editLink + unbanLink + deleteLink;
                    }
                    else{
                        return editLink + banLink + deleteLink;
                    }
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
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    rows: ':visible',
                },
                title: 'Guests_CSV-' + getFormattedDate()  // Add a custom file name for CSV
            },
            {
                extend: 'excelHtml5',
                className: "text-dark bg-white border-0 mt-3",
                text: "<i class=\"fa fa-file-excel\"></i> Excel",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    rows: ':visible',
                },
                title: 'Guests_Excel-' + getFormattedDate()  // Add a custom file name for Excel
            },
        ],
        createdRow: function(row, data, index) {
            if (data.is_banned == true)
                $(row).addClass("bg-danger text-white");
        }
    });
    
/****************************IZIMODAL**************************************** */    


    // Handle edit button click
    $('#guestsTable').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
           //Edit Modal Configurations
        $("#modal-edit").iziModal({
            title: 'Guest Information',
            icon: "fas fa-fw fa-user-tag",
            subtitle: 'Edit Guest Information',
            transitionIn: 'fadeInUp',
            transitionOut: 'fadeOutDown',
            width: 700,
            headerColor: "linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(94,4,4,1) 0%, rgba(94,4,4,0.6979166666666667) 96%)",
            fullscreen: true,
            onClosed: function() {
                const imageResult = document.getElementById('edit-image-result');
                if (imageResult) {
                    imageResult.remove();
                }
                editUnloadWebCamera();
                $("izimodal").iziModal("destroy");
                $("izimodal").remove();
            }
        });
        $.ajax({
            url: site_url + 'admin/user_management/guests/view/' + id,
            type: 'GET',
            success: function(response) {
                $("#modal-edit").iziModal('setContent', response);
                $("#modal-edit").iziModal('open');
                // Initialize your script here
                editLoadWebCamera();

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
    $(document).on('submit', '#editGuestForm', function(e) {
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
                    $('#guestsTable').DataTable().ajax.reload();
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

/****************************DELETE FUNCTION**************************************** */    

    // Handle delete button click
    $('#guestsTable').on('click', '.delete-btn', function(e) {
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
                        url: site_url + "admin/user_management/guests/delete/" + id,
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
                                $('#guestsTable').DataTable().ajax.reload();
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


/****************************BAN FUNCTION**************************************** */    

    // Handle delete button click
    $('#guestsTable').on('click', '.ban-btn', function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        iziToast.question({
            message: "Ban guest?",
            backgroundColor: "linear-gradient(30deg, rgb(179, 103, 24), rgb(243, 168, 32))",
            position: "center",
            icon: "fa fa-ban",
            drag: false,
            timeout: false,
            close: false,
            overlay: true,
            zindex: 1031,
            inputs: [
                // ["<input type=\"text\" id=\"dashboard-input\" placeholder=\"Reason of ban\" class=\"form-control text-white\">"],
                ["<input type=\"password\" id=\"dashboard-input\" placeholder=\"Enter password\" class=\"form-control text-white\">"]
            ],
            buttons: [
                ["<button class=\"text-white\">Confirm</button>", function(instance, toast, button, e, inputs) {
                    $.ajax({
                        url: site_url + "admin/user_management/guests/ban/" + id,
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
                            console.log(result);
                            const response = JSON.parse(result);
                            if (response.status == 200) {
                                $('#guestsTable').DataTable().ajax.reload();
                                success(response.message, "fa fa-check-circle");
                            } else {
                                danger(response.message, "fa fa-exclamation-triangle");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX error:", status, error);
                            console.log("Response text:", xhr.responseText);
                            $("button").prop("disabled", false);
                            danger("An error occurred: " + error, "fa fa-exclamation-triangle");
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

    // Handle delete button click
    $('#guestsTable').on('click', '.unban-btn', function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        iziToast.question({
            message: "Unban guest?",
            backgroundColor: "linear-gradient(30deg, rgb(74, 117, 16), rgb(51, 199, 55))",
            position: "center",
            icon: "fa fa-unlock",
            drag: false,
            timeout: false,
            close: false,
            overlay: true,
            zindex: 1031,
            inputs: [
                ["<input type=\"password\" id=\"dashboard-input\" placeholder=\"Enter password\" class=\"form-control text-white\">"]
            ],
            buttons: [
                ["<button class=\"text-white\">Confirm</button>", function(instance, toast, button, e, inputs) {
                    $.ajax({
                        url: site_url + "admin/user_management/guests/unban/" + id,
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
                            console.log(result);
                            const response = JSON.parse(result);
                            if (response.status == 200) {
                                $('#guestsTable').DataTable().ajax.reload();
                                success(response.message, "fa fa-check-circle");
                            } else {
                                danger(response.message, "fa fa-exclamation-triangle");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX error:", status, error);
                            console.log("Response text:", xhr.responseText);
                            $("button").prop("disabled", false);
                            danger("An error occurred: " + error, "fa fa-exclamation-triangle");
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
