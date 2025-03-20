$(document).ready(function() {
    //DATATABLES
    $('#visitorsPendingTable').DataTable({
        responsive: true,
        serverSide: true, // server-side processing
        dom: '<"row"<"col-sm-4"l><"col-sm-4 text-center"B><"col-sm-4"f>>rtip',
        ajax: {
            url: site_url + 'admin/visit_management/visitors_pending_list',
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
                title: 'Purpose of Visit',
                data: 'visit_purpose', 
                class: 'text-center',
            },
            { 
                title: 'Date of Visit',
                data: 'visit_date', 
                class: 'text-center',
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
                title: 'Phone Number',
                data: 'phone_number',
                class: 'text-center',
                orderable: false,
                render: function(data, type, row, meta){
                    // let phoneNumber = '+63 ' + data;
                    let phoneNumber = data;
                    return phoneNumber;
                }
            },
            // { 
            //     title: 'Email',
            //     data: 'email',
            //     class: 'text-center',
            //     orderable: false,
            // },
            { 
                title: 'Emergency Person',
                data: 'emergency_contact_person',
                class: 'text-center',
                orderable: false,
            },
            { 
                title: 'Emergency Number',
                data: 'emergency_contact_number',
                class: 'text-center',
                orderable: false,
            },
            // { 
            //     data: 'status', 
            //     class: 'text-center',
            //     orderable: false,
            //     render: function(data, type, row, meta) {
            //         let status = data === '1'? `<span class="badge badge-warning">Pending</span>` : `<span class="badge badge-success">Approved</span>`;
            //         return status;
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
                    let approveLink = `<button title="Approve" id="approve-btn" class="btn btn-sm btn-success approve-btn" data-id="${data}"><i class="fas fa-thumbs-up"></i> </button>`;
                    let declineLink = `<button title="Decline" id="decline-btn" class="btn btn-sm btn-warning decline-btn" data-id="${data}"><i class="fas fa-ban"></i> </button>`;
                    let deleteLink = `<button title="Delete" id="delete-btn" class="btn btn-sm btn-danger delete-btn" data-id="${data}"><i class="fas fa-trash"></i> </button></div>`;
                    return editLink + approveLink + declineLink + deleteLink;
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
                className: "text-dark bg-white border-0",
                text: "<i class=\"fa fa-file-csv\"></i> CSV",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7],
                    rows: ':visible',
                },
                title: 'VisitorsPending_CSV-' + getFormattedDate()  // Add a custom file name for CSV
            },
            {
                extend: 'excelHtml5',
                className: "text-dark bg-white border-0",
                text: "<i class=\"fa fa-file-excel\"></i> Excel",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7],
                    rows: ':visible',
                },
                title: 'VisitorsPending_Excel-' + getFormattedDate()  // Add a custom file name for Excel
            },
        ],
    });
    
/****************************IZIMODAL**************************************** */    


    // Handle edit button click
    $('#visitorsPendingTable').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
           //Edit Modal Configurations
        $("#modal-edit").iziModal({
            title: 'Visitor Request Information',
            icon: "fas fa-fw fa-user",
            subtitle: 'Edit Visitor Information',
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
            url: site_url + 'admin/visit_management/visitors_pending/view/' + id,
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

    function initializePhoneNumberFormatting() {
        const emergencyPhoneInput = document.getElementById('emergency_contact_number');
        if (emergencyPhoneInput) {
            emergencyPhoneInput.addEventListener('input', function() {
                formatPhoneNumber(this);
            });
        }
    }

    function formatPhoneNumber(input) {
        let phoneNumber = input.value.trim();
        if (phoneNumber && !phoneNumber.startsWith('+')) {
            // Assuming the user is from the Philippines and didn't include the country code
            phoneNumber = phoneNumber.replace(/^63+/, ''); // Remove leading zeros
            if (phoneNumber.startsWith('0')){
                phoneNumber = phoneNumber.replace(/^0+/, ''); // Remove leading zeros
            }
        }
        input.value = phoneNumber;
    }

    function initializeEditForm() {
        $('#contact_department').on('change', function() {
            let department = $(this).val();
            let contactPersonSelect = $('#contact_person');
            contactPersonSelect.empty(); // Clear previous options

            if (department) {
                // Assuming you have a backend endpoint to get the contact persons based on position
                $.ajax({
                    url: site_url + 'visitors_pending/get/contact_person',
                    method: 'POST',
                    data: {department: department},
                    success: function(response) {
                    let contactPersons = JSON.parse(response);
                    if (contactPersons.length > 0) {
                        contactPersons.forEach(function(person) {
                            let fullName = person.first_name + ' ' + person.last_name;
                            contactPersonSelect.append(new Option(fullName, fullName));
                        });
                    } else {
                        contactPersonSelect.append(new Option('No contacts found', ''));
                    }
                },
                    error: function() {
                        contactPersonSelect.append(new Option('Error fetching contacts', ''));
                    }
                });
            } else {
                contactPersonSelect.append(new Option('Select contact person name', ''));
            }       
        });
    }

    // Edit Button Handle form submission via AJAX
    $(document).on('submit', '#editVisitorForm', function(e) {
        e.preventDefault(); // Prevent default form submission

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function(response) {
                const serverResponse = JSON.parse(response)

                if(serverResponse.status == 200){
                    success(serverResponse.message, "fa fa-check-circle")                        
                    // success(response.message, "fa fa-check-circle");
                    $('#modal-edit').iziModal('close');
                    $('#visitorsPendingTable').DataTable().ajax.reload();
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
    $('#visitorsPendingTable').on('click', '.delete-btn', function(e) {
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
                        url: site_url + "admin/visit_management/visitors_pending/delete/" + id,
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
                                $('#visitorsPendingTable').DataTable().ajax.reload();
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

/****************************APPROVE FUNCTION**************************************** */    

    // Handle delete button click
    $('#visitorsPendingTable').on('click', '.approve-btn', function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        iziToast.question({
            message: "Enter RFID Number to be used by the Visitor",
            backgroundColor: "linear-gradient(30deg, rgb(74, 117, 16), rgb(51, 199, 55))",
            position: "center",
            icon: "fa fa-id-card",
            drag: false,
            timeout: false,
            close: false,
            overlay: true,
            zindex: 1031,
            inputs: [
                ["<input type=\"text\" placeholder=\"Enter RFID value\" class=\"form-control text-white\">"]
            ],
            buttons: [
                ["<button class=\"text-white\">Confirm</button>", function(instance, toast, button, e, inputs) {
                    $.ajax({
                        url: site_url + "admin/visit_management/visitors_pending/approve/" + id,
                        type: "POST",
                        data: "id=" + id + "&rfid=" + inputs[0].value,
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
                                $('#visitorsPendingTable').DataTable().ajax.reload();
                                success(response.message, "fa fa-check-circle");
                            } else {
                                const errors = response.message;
                                for (let key in errors) {
                                    if (errors.hasOwnProperty(key)) {
                                        danger(errors[key], "fa fa-exclamation-triangle")
            
                                    }
                                }
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

/****************************DECLINE FUNCTION**************************************** */    

    // Handle delete button click
    $("#visitorsPendingTable").on("click", ".decline-btn", function (e) {
		e.preventDefault();
		var id = $(this).data("id");
		iziToast.question({
			message: "Decline Request?",
			backgroundColor:
				"linear-gradient(30deg, rgb(179, 103, 24), rgb(243, 168, 32))",
			position: "center",
			icon: "fa fa-ban",
			drag: false,
			timeout: false,
			close: false,
			overlay: true,
			zindex: 1031,
			inputs: [
				[
					'<input type="text" id="dashboard-input" placeholder="Enter reason of decline" class="form-control text-white">',
				],
				[
					'<input type="password" id="dashboard-input" placeholder="Enter password" class="form-control text-white">',
				],
			],
			buttons: [
				[
					'<button class="text-white">Confirm</button>',
					function (instance, toast, button, e, inputs) {
						// console.log('Entered Password:', inputs[1].value); // This will log the entered password for debugging
						$.ajax({
							url:
								site_url +
								"admin/visit_management/visitors_pending/decline/" +
								id,
							type: "POST",
							data: {
								id: id,
								reason: inputs[0].value,
								password: inputs[1].value,
							},
							beforeSend: function () {
								instance.hide(
									{
										transitionOut: "fadeOut",
									},
									toast,
									"button"
								);
								$("button").attr("disabled", "disabled");
							},
							success: function (result) {
								$("button").removeAttr("disabled");
								try {
									const response = JSON.parse(result);
									if (response.status == 200) {
										$("#visitorsPendingTable").DataTable().ajax.reload();
										success(response.message, "fa fa-check-circle");
									} else {
										danger(response.message, "fa fa-exclamation-triangle");
									}
								} catch (e) {
									console.error("JSON parsing error:", e.message);
									console.log("Raw response:", result);
									danger(
										"Invalid server response. Please try again.",
										"fa fa-exclamation-triangle"
									);
								}
							},
							error: function (xhr, status, error) {
								console.error("AJAX error:", status, error);
								console.log("Response text:", xhr.responseText);
								$("button").prop("disabled", false);
								danger(
									"An error occurred: " + error,
									"fa fa-exclamation-triangle"
								);
							},
						});
					},
					true,
				],
				[
					'<button class="text-white">Cancel</button>',
					function (instance, toast, button, e) {
						instance.hide(
							{
								transitionOut: "fadeOut",
							},
							toast,
							"button"
						);
					},
				],
			],
		});
	});


});
