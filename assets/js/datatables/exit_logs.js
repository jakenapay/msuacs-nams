$(document).ready(function() {
    let table;

    function initializeDataTable(isFiltered = false) {
        if (table) {
            table.destroy();
        }

        let ajaxConfig = {
            url: isFiltered ? site_url + 'admin/logs/exit/location' : site_url + 'admin/logs/exit/list',
            type: isFiltered ? 'POST' : 'GET',
            data: function(d) {
                if (isFiltered) {
                    d.location = $('#location').val();
                }
                return d;
            }
        };

        table = $('#exitLogsTable').DataTable({
            responsive: true,
            serverSide: true,
            stateSave: false,
            ajax: ajaxConfig,
            autoWidth: false,
            pageLength: 10,
            aaSorting: [[0, "desc"]],
            columns: [
                {
                    title: 'ID',
                    data: 'id',
                    class: 'd-none'
                },
                { 
                    title: 'Date',
                    data: 'date',
                    class: 'text-center'
                },
                {
                    title: 'Time',
                    data: 'time',
                    class: 'text-center',
                    render: function(data, type, row, meta) {
                        let timeParts = data.split(':');
                        let hours = parseInt(timeParts[0], 10);
                        let minutes = timeParts[1];
                        let ampm = hours >= 12 ? 'PM' : 'AM';
                        hours = hours % 12;
                        hours = hours ? hours : 12;
                        return `${hours}:${minutes} ${ampm}`;
                    }
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
                    title: 'Type',
                    data: 'type',
                    class: 'text-center'
                },
                { 
                    title: 'College',
                    data: 'college',
                    class: 'text-center'
                },
                { 
                    title: 'Department/Office',
                    data: 'department',
                    class: 'text-center'
                },
                { 
                    title: 'Program',
                    data: 'program',
                    class: 'text-center'
                },
                { 
                    title: 'Building',
                    data: 'building',
                    class: 'text-center'
                },
                { 
                    title: 'Gate',
                    data: 'gate',
                    class: 'text-center'
                },
            ],
            language: {
                search: "<i class=\"fa fa-search\"></i> Search",
                searchPlaceholder: "Name, RFID, College, Program",
            },
            scrollX: "30rem",
            createdRow: function(row, data, index) {
                if (data.username == "Unregistered")
                    $(row).addClass("bg-danger text-white");
            }
        });
    }

    function resetToDefaults() {
        $('#location').val('all');
        initializeDataTable(false);
    }

    initializeDataTable(false);

    $('#location').on('change', function (e) {
        e.preventDefault();
        let location = $(this).val();


        if (location === 'all') {
            resetToDefaults();
            return;
        }

        initializeDataTable(true);
    });

    $('#resetTable').on('click', resetToDefaults);

    // Reload the table every 5 seconds when not filtered
    setInterval(function() {
        // if ($('#location').val() === 'all') {
            table.ajax.reload(null, false);
        // }
    }, 5000);
});