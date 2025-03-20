$(document).ready(function() {
let table =  $('#liveAccessTable').DataTable({
        responsive: false,
        dom: '<"row"<"col-sm-4"l><"col-sm-4 text-center"><"col-sm-4"f>>rtip',
        ajax: {
            url: site_url + 'admin/live_devices', // Server-side data endpoint
            type: 'GET',
            dataType: 'json',
            dataSrc: 'data', // 'data' key in the JSON response
        },
        interval: 1000,
        columns: [
            { 
                title: 'Status',
                data: 'status', 
                render: function(data, type, row, meta) {
                    if(data == 'Online'){
                        let a = `       
                        <p><i class='fa fa-satellite-dish text-success'></i> Online</p>
                    `;
                        return a;
                    }
                    else{
                    let a = `       
                    <p><i class='fa fa-satellite-dish text-danger'></i> Offline</p>
                    `;
                    return a;
                    }
                },
                class: 'text-center',
            },
            { 
                title: 'Device ID',
                data: 'device_id',
                class: 'text-center', 
            },
            { 
                title: 'Location',
                data: 'location',
                class: 'text-center', 
            },
            { 
                title: 'Name',
                data: 'name',
                class: 'text-center'
            },
            {
                title: 'IP Address', 
                data: 'ip_address',
                class: 'text-center', 
            },
            { 
                title: 'Response Time',
                data: 'response_time',
                class: 'text-center', 
            },
            
        ],
        scrollX: "30rem",
        createdRow: function(row, data, index) {
            if (data.status == "Online")
                $(data.status).addClass("fa fa-satellite-dish");    
        },
        language: {
            search: "<i class=\"fa fa-search\"></i> Search",
            searchPlaceholder: "Device Name, Location, Status",
        },
    });

    setInterval( function () {
        table.ajax.reload();
    }, 2000 );
});