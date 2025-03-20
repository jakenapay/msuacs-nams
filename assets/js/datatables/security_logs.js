$(document).ready(function() {
    function fetchData(startDate, endDate, order) {
        return $.ajax({
            url: site_url + 'admin/security/security_logs_list_filter',
            type: 'POST',
            dataType: 'json',
            data: {
                start: startDate,
                end: endDate,
            },
        });
    }

    let table = $('#securityLogsTable').DataTable({
        responsive: true,
        dom: '<"row"<"col-sm-4"l><"col-sm-4 text-center"B><"col-sm-4"f>>rtip',
        ajax: {
            url: site_url + 'admin/security/security_logs_list', // Server-side data endpoint
            type: 'GET',
            dataType: 'json',
            dataSrc: 'data', // 'data' key in the JSON response
        },
        autoWidth: false,
        pageLength: 10,
        aaSorting: [
            [0, "desc"] // Sort by the 'Out Time' column in descending order initially
        ],
        liveAjax: {
            interval: 2000
        },
        sPaginationType: "full_numbers",
        columns: [
            { 
                title: 'ID',
                data: 'id',
                class: 'd-none',
            },
            {
                title: 'Name',
                data: 'name',
                class: 'text-center'
            },
            { 
                title: 'Action',
                data: 'action',
                class: 'text-center'
            },
            { 
                title: 'Date',
                data: 'date',
                class: 'text-center', 
            },
        ],
        language: {
            search: "<i class=\"fa fa-search\"></i> Search",
            searchPlaceholder: "First Name, Course, School ID Number, Gender",
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
                title: 'SecurityLogs-CSV-' + getFormattedDate() 
            },
            {
                extend: 'excelHtml5',
                className: "text-dark bg-white border-0 mr-2",
                text: "<i class=\"fa fa-file-excel\"></i> Excel",
                exportOptions: {
                    columns: [1, 2, 3],
                    rows: ':visible',
                },
                title: 'SecurityLogs-Excel-' + getFormattedDate()
            },
                {
                extend: 'print',
                className: "text-dark bg-white border-0 mr-2",
                text: "<i class=\"fa fa-print\"></i> Print",
                exportOptions: {
                    columns: [1, 2, 3]
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
            },
        ],
        createdRow: function(row, data, index) {
            if (data.username == "Unregistered")
                $(row).addClass("bg-danger text-white");
        }
    });

    table.button('.buttons-print').action(function (e, dt, button, config) {
        // let selectedType = $('#type').val();
        // let dateRange = $('#dateRange').val();
        // let dates = dateRange.split(' - ');

        let startDate = $('input[name="start"]').val();
        let endDate = $('input[name="end"]').val();

        // var startDate = moment(dates[0]).format('MMMM D, YYYY');
        // var endDate = moment(dates[1]).format('MMMM D, YYYY');

        var formattedDateRange = startDate + ' to ' + endDate;
    
        // Set the title based on the selected type
        config.title = '';
        config.messageTop = '<div class="text-center">Security Logs Report</div> Date Range: ' + formattedDateRange;
        

        $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
    });

    function resetToDefaults() {
        table.ajax.reload();
        table.order([[0, 'desc']]).draw()
    }

    $('#security_filter').on('submit', function (e) {
        e.preventDefault();
        let startDate = $('input[name="start"]').val();
        let endDate = $('input[name="end"]').val();

        fetchData(startDate, endDate).done(function (data) {
            // Clear the existing DataTable data
            table.clear();
            
            // Add the new data to the DataTable
            table.rows.add(data.data).draw();
            table.order([[0, 'asc']]).draw();

            // Calculate the number of data
            let numRecords = data.data.length;
            
            // Set the page length based on the number of data
            table.page.len(numRecords).draw();
        });
    });

    $('#resetTable').on('click', resetToDefaults);

});

