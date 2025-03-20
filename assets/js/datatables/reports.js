$(document).ready(function() {
    let start = moment().subtract(29, 'days');
    let end = moment();

    function cb(start, end) {
        // Display date in MMMM D, YYYY format while keeping the value in YYYY-MM-DD format
        $('#dateRange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#dateRange').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
    }
    
    $('#dateRange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

    $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
        // Set the value in YYYY-MM-DD format but keep displaying it in MMMM D, YYYY
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        $('#dateRange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
    });

    $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    // Initialize DataTables with deferred loading
    var table = $('#logsTable').DataTable({
        responsive: true,
        serverSide: true,
        dom: '<"row"<"col-sm-4"l><"col-sm-4 text-center"B><"col-sm-4"f>>rtip',
        deferLoading: 0, // Prevent initial data load
        ajax: {
            url: site_url + 'admin/reports/entry_logs/filter',  // Replace with your actual endpoint
            type: 'GET',
            data: function(d) {
                // Append form data to the DataTables AJAX request
                return $.extend({}, d, {
                    dateRange: $('#dateRange').val(),
                    userType: $('#userType').val(),
                    college: $('#college').val(),
                    department: $('#department').val(),
                    program: $('#program').val(),
                    office: $('#office').val(),
                    type: $('#type').val(),
                    location: $('#location').val()
                });
            },
            dataType: 'json',
            dataSrc: function(json) {
                // Check if the data returned is empty and prevent the table from rendering
                if (!json.data || json.data.length === 0) {
                    danger('No records found for the selected options!', "fa fa-exclamation-triangle")
                    // $('#logsTable tbody').empty(); // Clear any previous data
                    return []; // Return an empty array to prevent DataTables from rendering
                }
                
                return json.data;
            }
        },
        autoWidth: false,
        pageLength: 10,
        aaSorting: [
            [0, "desc"]
        ],
        columns: [
            { 
                title: 'Date',
                data: 'date',
                class: 'text-center', 
            },
            { 
                title: 'Time',
                data: 'time',
                class: 'text-center', 
            },
            {
                title: 'RFID',
                data: 'rfid',
                class: 'text-center'
            },
            { 
                title: 'Name',
                data: 'fullname',
                class: 'text-center'
            },
            { 
                title: 'College',
                data: 'college',
                class: 'text-center',
            },         
            { 
                title: 'Department/Office',
                data: 'department',
                class: 'text-center',
            },      
            { 
                title: 'Program',
                data: 'program',
                class: 'text-center',
            },         
            { 
                title: 'Type',
                data: 'type',
                class: 'text-center', 
            },
            { 
                title: 'Location',
                data: 'building',
                class: 'text-center', 
            },
            { 
                title: 'Gate',
                data: 'gate',
                class: 'text-center', 
            },
        ],
        buttons: [
            {
                extend: 'csvHtml5',
                className: "text-dark bg-white border-0 mt-3 mr-2",
                text: "<i class=\"fa fa-file-csv\"></i> CSV",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    rows: ':visible',
                },
                title: 'Logs-CSV-' + getFormattedDate() 
            },
            {
                extend: 'excelHtml5',
                className: "text-dark bg-white border-0 mr-2",
                text: "<i class=\"fa fa-file-excel\"></i> Excel",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    rows: ':visible',
                },
                title: 'Logs-Excel-' + getFormattedDate()
            },
            {
                extend: 'print',
                className: "text-dark bg-white border-0 mr-2",
                text: "<i class=\"fa fa-print\"></i> Print",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                },
                customize: function (win) {
                    
                    $(win.document.body).prepend('<div style="text-align: center; display: flex; justify-content: center;">' +
                        '<div style="margin-right: 10px;"><img src="' + site_url + 'assets/images/msu.png" alt="School Logo" style="max-width: 100px; max-height: 100px;"></div>' +
                        '<div>' + 
                        '<h2 style="margin-top: 20px; color: black; font-family: Times New Roman; font-weight: 600;">Mindanao State University</h2>' +
                        '<p style="margin-top: -10px;">General Santos City, South Cotabato</p>' +
                        '</div>' +
                        '</div>');
                }
            }                
        ],
    });

    table.button('.buttons-print').action(function (e, dt, button, config) {
        let totalData = table.rows().count(); // Get the total number of records
        let studentData = table.rows({ filter: 'applied' }).nodes().to$().find('td:eq(7)').filter(':contains("Student")').length;
        let staffData = table.rows({ filter: 'applied' }).nodes().to$().find('td:eq(7)').filter(':contains("Staff")').length;
        let facultyData = table.rows({ filter: 'applied' }).nodes().to$().find('td:eq(7)').filter(':contains("Faculty")').length;
        let visitingOfficerData = table.rows({ filter: 'applied' }).nodes().to$().find('td:eq(7)').filter(':contains("Visiting officer")').length;
        let contractorData = table.rows({ filter: 'applied' }).nodes().to$().find('td:eq(7)').filter(':contains("Contractor")').length;
        let researcherData = table.rows({ filter: 'applied' }).nodes().to$().find('td:eq(7)').filter(':contains("Researcher")').length;
        let deliveryServiceData = table.rows({ filter: 'applied' }).nodes().to$().find('td:eq(7)').filter(':contains("Delivery service")').length;
        let otherData = table.rows({ filter: 'applied' }).nodes().to$().find('td:eq(7)').filter(':contains("Other")').length;
        let unregisteredData = table.rows({ filter: 'applied' }).nodes().to$().find('td:eq(7)').filter(':contains("Unknown")').length;

        let selectedType = $('#type').val();
        let dateRange = $('#dateRange').val();
        let dates = dateRange.split(' - ');

        var startDate = moment(dates[0]).format('MMMM D, YYYY');
        var endDate = moment(dates[1]).format('MMMM D, YYYY');

        var formattedDateRange = startDate + ' - ' + endDate;
    
        // Set the title based on the selected type
        let reportTitle = selectedType === 'exit_logs' ? 'Exit' : 'Entry';
        config.title = '';
        config.messageTop = '<div class="text-center">' + reportTitle + ' Logs Report</div> Date Range: ' + formattedDateRange;
        
        config.messageBottom = '<div class="mt-4 table">' +
            '<p class="h6 text-dark">Total ' + reportTitle +  ' Data</p>' +
            '<table class="table table-bordered">' +
            '<thead>' +
            '<tr>' +
            '<th scope="col">User Type</th>' +
            '<th scope="col">Count</th>' +
            '</tr>' +
            '</thead>' +
            '<tbody>' +
            '<tr>' +
            '<td>Students</td>' +
            '<td><span class="text-info mr-2">' + studentData + '</span></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Faculty</td>' +
            '<td><span class="text-info mr-2">' + facultyData + '</span></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Staff</td>' +
            '<td><span class="text-info mr-2">' + staffData + '</span></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Visiting Officers</td>' +
            '<td><span class="text-info mr-2">' + visitingOfficerData + '</span></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Contractors</td>' +
            '<td><span class="text-info mr-2">' + contractorData + '</span></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Researchers</td>' +
            '<td><span class="text-info mr-2">' + researcherData + '</span></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Delivery Services</td>' +
            '<td><span class="text-info mr-2">' + deliveryServiceData + '</span></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Others</td>' +
            '<td><span class="text-info mr-2">' + otherData + '</span></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Total '+ reportTitle + '</td>' +
            '<td><span class="text-info">' + totalData + '</span></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Total Blocked Users</td>' +
            '<td><span class="text-danger">' + unregisteredData + '</span></td>' +
            '</tr>' +
            '</tbody>' +
            '</table>' +
            '</div>';
        

        $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
    });

    // Re-fetch data on form submission
    $('#logFilterForm').on('submit', function(e) {
        $('#tableParent').css('display', 'block');
        e.preventDefault();
        table.ajax.reload();  // Reload DataTable data using new filter values
    });
});
