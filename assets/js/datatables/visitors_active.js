$(document).ready(function () {
    //DATATABLES
    $('#visitorsActiveTable').DataTable({
        responsive: true,
        serverSide: true, // server-side processing
        dom: '<"row"<"col-sm-4"l><"col-sm-4 text-center"B><"col-sm-4"f>>rtip',
        ajax: {
            url: site_url + 'admin/visit_management/visitors_active/list',
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
                render: function (data, type, row, meta) {
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
                render: function (data, type, row, meta) {
                    let phoneNumber = '+63 ' + data;

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
                title: 'Status',
                data: 'status',
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    let status = data === '1' ? `<span class="badge badge-warning">Pending</span>` : data === '2' ? `<span class="badge badge-success">Active</span>` : `<span class="badge badge-warning">Banned</span>`;
                    return status;
                }
            },
            {
                title: "Options",
                data: "id",
                className: "text-center",
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    let editLink = `<div class="btn-group dashboard-btn"><button title="Edit" id="edit-btn" class="btn btn-sm btn-primary edit-btn" data-id="${data}"><i class="fas fa-eye"></i> </button>`;
                    let approveLink = `<button title="Conclude" id="approve-btn" class="btn btn-sm btn-success approve-btn" data-id="${data}"><i class="fas fa-check"></i> </button>`;
                    let banLink = `<button title="Ban" id="ban-btn" class="btn btn-sm btn-warning ban-btn" data-id="${data}"><i class="fas fa-ban"></i> </button>`;
                    let unbanLink = `<button title="Unban" id="unban-btn" class="btn btn-sm btn-success unban-btn" data-id="${data}"><i class="fas fa-unlock"></i> </button>`;
                    let deleteLink = `<button title="Delete" id="delete-btn" class="btn btn-sm btn-danger delete-btn" data-id="${data}"><i class="fas fa-trash"></i> </button></div>`;

                    if (row.is_banned == true) {
                        return editLink + approveLink + unbanLink + deleteLink;
                    }
                    else {
                        return editLink + approveLink + banLink + deleteLink;
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
                title: 'VisitorsActive_CSV-' + getFormattedDate()  // Add a custom file name for CSV
            },
            {
                extend: 'excelHtml5',
                className: "text-dark bg-white border-0 mt-3",
                text: "<i class=\"fa fa-file-excel\"></i> Excel",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    rows: ':visible',
                },
                title: 'VisitorsActive_Excel-' + getFormattedDate()  // Add a custom file name for Excel
            },
        ],
        createdRow: function (row, data, index) {
            if (data.is_banned == true)
                $(row).addClass("bg-danger text-white");
        }
    });

    /****************************IZIMODAL**************************************** */


    // Handle edit button click
    $('#visitorsActiveTable').on('click', '.edit-btn', function () {
        var id = $(this).data('id');
        //Edit Modal Configurations
        $("#modal-edit").iziModal({
            title: 'Active Visitor Information',
            icon: "fas fa-fw fa-user",
            subtitle: 'View Visitor Information',
            transitionIn: 'fadeInUp',
            transitionOut: 'fadeOutDown',
            width: 700,
            headerColor: "linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(94,4,4,1) 0%, rgba(94,4,4,0.6979166666666667) 96%)",
            fullscreen: true,
            onClosed: function () {
                $("izimodal").iziModal("destroy");
                $("izimodal").remove();
            }
        });
        $.ajax({
            url: site_url + 'admin/visit_management/visitors_active/view/' + id,
            type: 'GET',
            success: function (response) {
                $("#modal-edit").iziModal('setContent', response);
                $("#modal-edit").iziModal('open');
                // Initialize your script here
                initializeEditForm();
                initializePhoneNumberFormatting();
            },
            error: function () {
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
            emergencyPhoneInput.addEventListener('input', function () {
                formatPhoneNumber(this);
            });
        }
    }

    function formatPhoneNumber(input) {
        let phoneNumber = input.value.trim();
        if (phoneNumber && !phoneNumber.startsWith('+')) {
            // Assuming the user is from the Philippines and didn't include the country code
            phoneNumber = phoneNumber.replace(/^63+/, ''); // Remove leading zeros
            if (phoneNumber.startsWith('0')) {
                phoneNumber = phoneNumber.replace(/^0+/, ''); // Remove leading zeros
            }
        }
        input.value = phoneNumber;
    }

    function initializeEditForm() {
        $('#contact_department').on('change', function () {
            let department = $(this).val();
            let contactPersonSelect = $('#contact_person');
            contactPersonSelect.empty(); // Clear previous options

            if (department) {
                // Assuming you have a backend endpoint to get the contact persons based on position
                $.ajax({
                    url: site_url + 'visitors_pending/get/contact_person',
                    method: 'POST',
                    data: { department: department },
                    success: function (response) {
                        let contactPersons = JSON.parse(response);
                        if (contactPersons.length > 0) {
                            contactPersons.forEach(function (person) {
                                let fullName = person.first_name + ' ' + person.last_name;
                                contactPersonSelect.append(new Option(fullName, fullName));
                            });
                        } else {
                            contactPersonSelect.append(new Option('No contacts found', ''));
                        }
                    },
                    error: function () {
                        contactPersonSelect.append(new Option('Error fetching contacts', ''));
                    }
                });
            } else {
                contactPersonSelect.append(new Option('Select contact person name', ''));
            }
        });
    }

    // Edit Button Handle form submission via AJAX
    $(document).on('submit', '#editVisitorForm', function (e) {
        e.preventDefault(); // Prevent default form submission

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function (response) {
                const serverResponse = JSON.parse(response)

                if (serverResponse.status == 200) {
                    success(serverResponse.message, "fa fa-check-circle")
                    // success(response.message, "fa fa-check-circle");
                    $('#modal-edit').iziModal('close');
                    $('#visitorsActiveTable').DataTable().ajax.reload();
                }
                else {
                    const errors = serverResponse.message;
                    for (let key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            danger(errors[key], "fa fa-exclamation-triangle")

                        }
                    }
                }
            },
            error: function () {
                iziToast.error({
                    title: 'Error',
                    message: 'An error occurred while submitting the form.',
                });
            }
        });
    });

    /****************************DELETE FUNCTION**************************************** */

    // Handle delete button click
    $('#visitorsActiveTable').on('click', '.delete-btn', function (e) {
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
                ["<button class=\"text-white\">Confirm</button>", function (instance, toast, button, e, inputs) {
                    $.ajax({
                        url: site_url + "admin/visit_management/visitors_active/delete/" + id,
                        type: "POST",
                        data: "id=" + id + "&password=" + inputs[0].value,
                        beforeSend: function () {
                            instance.hide({
                                transitionOut: "fadeOut"
                            }, toast, "button");
                            $("button").attr("disabled", "");
                        },
                        success: function (result) {
                            $("button").removeAttr("disabled");
                            const response = JSON.parse(result);
                            if (response.status == 200) {
                                $('#visitorsActiveTable').DataTable().ajax.reload();
                                success(response.message, "fa fa-check-circle");
                            } else {
                                danger(response.message, "fa fa-exclamation-triangle");
                            }
                        }
                    });
                }, true],
                ["<button class=\"text-white\">Cancel</button>", function (instance, toast, button, e) {
                    instance.hide({
                        transitionOut: "fadeOut"
                    }, toast, "button");
                }]
            ]
        });
    });

    /****************************APPROVE FUNCTION**************************************** */

    // Handle delete button click
    $('#visitorsActiveTable').on('click', '.approve-btn', function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        iziToast.question({
            title: 'Conclude Visit',
            message: "Are you confirming that the visitor has completed their visit and is done using the RFID?",
            backgroundColor: "linear-gradient(30deg, rgb(74, 117, 16), rgb(51, 199, 55))",
            position: "center",
            icon: "fa fa-id-card",
            drag: false,
            timeout: false,
            close: false,
            overlay: true,
            zindex: 1031,
            buttons: [
                ["<button class=\"text-white\">Confirm</button>", function (instance, toast, button, e, inputs) {
                    $.ajax({
                        url: site_url + "admin/visit_management/visitors_active/approve/" + id,
                        type: "POST",
                        data: "id=" + id,
                        beforeSend: function () {
                            instance.hide({
                                transitionOut: "fadeOut"
                            }, toast, "button");
                            $("button").attr("disabled", "");
                        },
                        success: function (result) {
                            $("button").removeAttr("disabled");
                            console.log(result);
                            const response = JSON.parse(result);
                            if (response.status == 200) {
                                $('#visitorsActiveTable').DataTable().ajax.reload();
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
                        error: function (xhr, status, error) {
                            console.error("AJAX error:", status, error);
                            console.log("Response text:", xhr.responseText);
                            $("button").prop("disabled", false);
                            danger("An error occurred: " + error, "fa fa-exclamation-triangle");
                        }
                    });
                }, true],
                ["<button class=\"text-white\">Cancel</button>", function (instance, toast, button, e) {
                    instance.hide({
                        transitionOut: "fadeOut"
                    }, toast, "button");
                }]
            ]
        });
    });


    /****************************BAN FUNCTION**************************************** */

    // Handle delete button click
    $('#visitorsActiveTable').on('click', '.ban-btn', function (e) {
        e.preventDefault();
        var id = $(this).data("id");

        $.ajax({
            url: site_url + 'admin/configurations/locations/list',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status === 200) {
                    var locations = response.data;
                    iziToast.question({
                        message: "Ban visitor?",
                        backgroundColor: "linear-gradient(30deg, rgb(179, 103, 24), rgb(243, 168, 32))",
                        position: "center",
                        icon: "fa fa-ban",
                        drag: false,
                        timeout: false,
                        close: false,
                        overlay: true,
                        zindex: 1031,
                        inputs: [

                            ["<input type=\"password\" id=\"dashboard-input\" placeholder=\"Enter password\" class=\"form-control text-white\">"],
                            [
                                `<select id="dashboard-reason" class="form-control" style="color: white;" required>
                                    <option style="color: black;" value="" disabled selected hidden>Select reason</option>
                                    <option style="color: black;" value="Bullying">Bullying</option>
                                    <option style="color: black;" value="Disrespecting teachers or staff">Disrespecting teachers or staff</option>
                                    <option style="color: black;" value="Misbehavior">Misbehavior</option>
                                    <option style="color: black;" value="Threatening or intimidating">Threatening or intimidating</option>
                                    <option style="color: black;" value="Using drugs or alcohol">Using drugs or alcohol</option>
                                    <option style="color: black;" value="Violating school rules">Violating school rules</option>
                                    <option style="color: black;" value="Other">Other</option>
                                </select>`
                            ],
                            [
                                `<select id="dashboard-locations" class="form-control" multiple style="color: white;">
                                    <option value="" style="color: white;" disabled selected>Select ban locations</option>
                                    <option value="all" style="color: white;">All</option>
                                    ${locations.map(location => `<option value="${location.id}" style="color: white;">${location.name}</option>`).join('')}
                                </select>`
                            ]
                        ],
                        buttons: [
                            ["<button class=\"text-white\">Confirm</button>", function (instance, toast, button, e, inputs) {
                                var reason = inputs[1].value;
                                var selectedLocations = $("#dashboard-locations").val();
                                if (selectedLocations.includes("all")) {
                                    selectedLocations = locations.map(location => location.id.toString());
                                }
                                var selectedLocationNames = $("#dashboard-locations option:selected").map(function () {
                                    return $(this).text();
                                }).get().join(', ');

                                if (reason === "Other") {
                                    reason = prompt("Please specify the reason:");
                                    if (!reason) {
                                        danger("Reason must be specified.", "fa fa-exclamation-triangle");
                                        return;
                                    }
                                }

                                $.ajax({
                                    url: site_url + "admin/visit_management/visitors_active/ban/" + id,
                                    type: "POST",
                                    data: {
                                        id: id,
                                        password: inputs[0].value,
                                        reason: reason,
                                        locations: selectedLocations.join(', '),
                                        locationNames: selectedLocationNames
                                    },
                                    beforeSend: function () {
                                        instance.hide({
                                            transitionOut: "fadeOut"
                                        }, toast, "button");
                                        $("button").attr("disabled", "");
                                    },
                                    success: function (result) {
                                        $("button").removeAttr("disabled");
                                        console.log(result);
                                        const response = JSON.parse(result);
                                        if (response.status == 200) {
                                            $('#visitorsActiveTable').DataTable().ajax.reload();
                                            success(response.message, "fa fa-check-circle");
                                        } else {
                                            danger(response.message, "fa fa-exclamation-triangle");
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error("AJAX error:", status, error);
                                        console.log("Response text:", xhr.responseText);
                                        $("button").prop("disabled", false);
                                        danger("An error occurred: " + error, "fa fa-exclamation-triangle");
                                    }
                                });
                            }, true],
                            ["<button class=\"text-white\">Cancel</button>", function (instance, toast, button, e) {
                                instance.hide({
                                    transitionOut: "fadeOut"
                                }, toast, "button");
                            }]
                        ]
                    });
                } else {
                    console.error("Error fetching locations:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    });

    // Handle delete button click
    $('#visitorsActiveTable').on('click', '.unban-btn', function (e) {
        e.preventDefault();
        var id = $(this).data("id");

        $.ajax({
            url: site_url + 'admin/configurations/locations/list',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status === 200) {
                    var locations = response.data;
                    iziToast.question({
                        message: "Unban visitor?",
                        backgroundColor: "linear-gradient(30deg, rgb(74, 117, 16), rgb(51, 199, 55))",
                        position: "center",
                        icon: "fa fa-unlock",
                        drag: false,
                        timeout: false,
                        close: false,
                        overlay: true,
                        zindex: 1031,
                        inputs: [
                            ["<input type=\"password\" id=\"dashboard-input\" placeholder=\"Enter password\" class=\"form-control text-white\">"],
                            [`<select id="unban-reason" class="form-control text-white">
                                <option style="color: black;" value="" selected disabled hidden>Select reason</option>
                                <option style="color: black;" value="Appeal Approved">Appeal Approved</option>
                                <option style="color: black;" value="Mistake">Mistake</option>
                                <option style="color: black;" value="Other">Other</option>
                            </select>`],
                            [
                                `<select id="unban-locations" class="form-control text-white" multiple>
                                    ${(() => {
                                        let options = '<option value="" style="color: white;" disabled selected>Select unban locations</option><option value="all" style="color: white;">All</option>';
                                        $.ajax({
                                            url: site_url + 'admin/visit_management/visitors_active/getBannedLocations/' + id,
                                            type: 'GET',
                                            async: false,
                                            dataType: 'json',
                                            success: function(response) {
                                                if (response.status === 200) {
                                                    options += response.data.map(location => `<option value="${location.id}" style="color: white;">${location.name}</option>`).join('');
                                                }
                                            },
                                            error: function(xhr, status, error) {
                                                console.error("AJAX Error:", error);
                                            }
                                        });
                                        return options;
                                    })()}
                                </select>`
                            ]
                        ],
                        buttons: [
                            ["<button class=\"text-white\">Confirm</button>", function (instance, toast, button, e, inputs) {
                                var reason = $("#unban-reason").val();
                                var selectedLocations = $("#unban-locations").val();
                                var selectedLocationNames;

                                if (selectedLocations.includes("all")) {
                                    selectedLocations = [0];
                                    selectedLocationNames = "All";
                                } else {
                                    selectedLocations = $("#unban-locations option:not(:selected)").map(function() {
                                        return $(this).val();
                                    }).get();
                                    // Remove "all" from the array if it exists
                                    const index = selectedLocations.indexOf("all");
                                    if (index > -1) {
                                        selectedLocations.splice(index, 1);
                                    }
                                    selectedLocationNames = $("#unban-locations option:selected").map(function() {
                                        // Exclude the "Select unban location" option
                                        if ($(this).val() !== "") {
                                            return $(this).text();
                                        }
                                    }).get().join(', ');
                                }
                                
                                if (reason == "Other") {
                                    reason = prompt("Please specify the reason:");
                                    if (!reason) {
                                        danger("Reason must be specified.", "fa fa-exclamation-triangle");
                                        return;
                                    }
                                }
                                $.ajax({
                                    url: site_url + "admin/visit_management/visitors_active/unban/" + id,
                                    type: "POST",
                                    data: {
                                        id: id,
                                        password: inputs[0].value,
                                        reason: reason,
                                        locations: selectedLocations.join(', ').replace(/^,/, ''),
                                        locationNames: selectedLocationNames,
                                    },
                                    beforeSend: function () {
                                        instance.hide({
                                            transitionOut: "fadeOut"
                                        }, toast, "button");
                                        $("button").attr("disabled", "");
                                    },
                                    success: function (result) {
                                        $("button").removeAttr("disabled");
                                        console.log(result);
                                        const response = JSON.parse(result);
                                        if (response.status == 200) {
                                            $('#visitorsActiveTable').DataTable().ajax.reload();
                                            success(response.message, "fa fa-check-circle");
                                        } else {
                                            danger(response.message, "fa fa-exclamation-triangle");
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error("AJAX error:", status, error);
                                        console.log("Response text:", xhr.responseText);
                                        $("button").prop("disabled", false);
                                        danger("An error occurred: " + error, "fa fa-exclamation-triangle");
                                    }
                                });
                            }, true],
                            ["<button class=\"text-white\">Cancel</button>", function (instance, toast, button, e) {
                                instance.hide({
                                    transitionOut: "fadeOut"
                                }, toast, "button");
                            }]
                        ]
                    });
                } else {
                    console.error("Error fetching locations:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    });


});
