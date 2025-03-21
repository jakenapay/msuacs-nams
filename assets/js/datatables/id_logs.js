$(document).ready(function() {

    //DATATABLES
    $('#idTable').DataTable({
        responsive: true,
        // serverSide: true, // server-side processing
        dom: '<"row"<"col-sm-4"l><"col-sm-4 text-center"B><"col-sm-4"f>>rtip',
        ajax: {
            url: site_url + 'admin/security/id_logs_list',
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
                class: 'text-center d-none',
                type: 'numeric'
            },   
            { 
                title: 'ID',
                data: 'id_number',
                class: 'text-center',
                type: 'numeric'
            },   
            { 
                title: 'RFID',
                data: 'rfid',
                class: 'text-center',
            },         
            {
                title: 'Status',
                data: 'status',
                class: 'text-center',
                render: function (data, type, row) {
                    let bgClass = '';
                    if (data === 'loss') {
                        bgClass = 'text-capitalize bg-warning text-dark';
                    } else if (data === 'issued') {
                        bgClass = 'text-capitalize bg-success text-white';
                    } else {
                        bgClass = 'text-capitalize bg-secondary text-white'; // Default style for other statuses
                    }
                    return `<span class="badge ${bgClass} p-2">${data}</span>`;
                }
            },     
            { 
                title: 'Reason',
                data: 'reason',
                class: 'text-center text-capitalize',
            },         
            { 
                title: 'Date',
                data: 'date',
                class: 'text-center',
            },     
        ],
        language: {
            search: "<i class=\"fa fa-search\"></i> Search",
            searchPlaceholder: "ID, RFID, Status, Reason, Date, Remarks",
        },
        scrollX: "30rem",
        buttons: [
            {
                extend: 'csvHtml5',
                className: "btn btn-sm btn-outline-dark mt-3 mr-2",
                text: "<i class='fa fa-file-csv'></i> CSV",
                exportOptions: {
                    columns: ':not(.d-none)'  // Export all columns, including hidden ones
                },
                filename: `ID_Logs_CSV_${getFormattedDate()}`
            },
            {
                extend: 'excelHtml5',
                className: "btn btn-sm btn-outline-success mt-3",
                text: "<i class='fa fa-file-excel'></i> Excel",
                exportOptions: {
                    columns: ':not(.d-none)'  // Export all columns, including hidden ones
                },
                filename: `ID_Logs_Excel_${getFormattedDate()}`
            }
        ]
    });

});
