document.addEventListener("DOMContentLoaded", function() {
    const visitorTrendsChartCtx = document.getElementById('visitorTrendsChart').getContext('2d');
    const exitTrendsChartCtx = document.getElementById('exitTrendsChart').getContext('2d');
    const userTypeDistributionCtx = document.getElementById('userTypeDistributionChart').getContext('2d');
    const peakHoursChartCtx = document.getElementById('peakHoursChart').getContext('2d');
    const trendsContainer = document.getElementById('trendsContainer');
    const userTypeDistributionChartContainter = document.getElementById('chartContainer');

    function setUserTypeDistributionChartHeight() {
        if (trendsContainer && userTypeDistributionChartContainter) {
        const trendsHeight = trendsContainer.offsetHeight;
        userTypeDistributionChartContainter.style.height = trendsHeight + 'px';
        }
    }

    setUserTypeDistributionChartHeight();
    window.addEventListener('resize', setUserTypeDistributionChartHeight);

    let visitorTrendsChart, exitTrendsChart, userTypeDistributionChart, peakHoursChart;

    // Initialize start and end dates
    let start = moment().subtract(29, 'days');
    let end = moment();

    function formatHour(hour) {
        const hours = parseInt(hour, 10);
        const period = hours >= 12 ? 'PM' : 'AM';
        const formattedHour = hours % 12 || 12; // Converts 0 to 12
        return `${formattedHour} ${period}`;
    }

    // Plugin to add text in the center of the doughnut chart
    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw: function(chart) {
            const ctx = chart.ctx;
            const width = chart.width;
            const height = chart.height;
            const fontSize = (height / 250).toFixed(2);
            ctx.font = fontSize + "em sans-serif";
            ctx.textBaseline = "middle";

            // Ensure the data is numeric
            const data = chart.data.datasets[0].data.map(Number);
            const total = data.reduce((a, b) => a + b, 0);
            const text = 'Total: ' + total.toString(); // Convert total to string
            const text2 = total.toString(); // Convert total to string
            const textX = Math.round((width - ctx.measureText(text).width) / 2);
            const textY = height / 2;

            ctx.save(); // Save the current context state
            ctx.clearRect(0, 0, width, height); // Clear the canvas before rendering the new chart
            ctx.fillText(text, textX, textY);
            ctx.restore(); // Restore the context state
            }
    };
    

    function cb(start, end) {
        // Update date range display
        $('#dateRange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#dateRange').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        
        // Update all charts with the new date range
        updateAllCharts(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
    }

    // Initialize Date Range Picker with predefined ranges
    $('#dateRange').daterangepicker({
        startDate: start,
        endDate: end,
        opens: "left",
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    // Initial call to display the current date range and load the charts
    cb(end, end);

    // Handle applying a new date range
    $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        $('#dateRange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
    });

    // Handle canceling the date range selection
    $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    function updateAllCharts(startDate, endDate) {
        updateVisitorTrendsChart(startDate, endDate);
        updateExitTrendsChart(startDate, endDate);
        updateUserTypeDistributionChart(startDate, endDate);
        updatePeakHoursChart(startDate, endDate);
        updateTotalExitEntry(startDate, endDate);
    }

    function updateTotalExitEntry(startDate, endDate){
        $.ajax({
            url: site_url + 'admin/dashboard/filter_total_entry_exit',
            type: 'GET',
            data: { start_date: startDate, end_date: endDate },
            dataType: 'json',
            success: function(response) {
                const totalEntry = response.total_entries;
                const totalExit = response.total_exits;
                
                $('#totalEntry').html(totalEntry);
                $('#totalExit').html(totalExit);
            },
            error: function() {
                danger('Failed to load total entry and exit data.', "fa fa-exclamation-triangle");

            }
        })
    }

    function updateVisitorTrendsChart(startDate, endDate) {
        $.ajax({
            url: site_url + 'admin/dashboard/filter_visitor_trends',
            type: 'GET',
            data: { start_date: startDate, end_date: endDate },
            dataType: 'json',
            success: function(response) {
                const labels = response.map(item => item.date);
                const data = response.map(item => item.count);

                if (visitorTrendsChart) {
                    visitorTrendsChart.destroy();
                }

                visitorTrendsChart = new Chart(visitorTrendsChartCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Entries',
                            data: data,
                            borderColor: 'rgb(28, 200, 138)',
                            backgroundColor: 'rgb(28, 200, 138, 0.2)',
                            borderWidth: 1,
                            fill: false
                        }]
                    },
                    options: {
                        animation: {
                            onComplete: function() {
                                setUserTypeDistributionChartHeight();
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            },
                            
                        }
                    },
                });
            },
            error: function() {
                danger('Failed to load visitor trends data.', "fa fa-exclamation-triangle");

            }
        });
    }

    function updateExitTrendsChart(startDate, endDate) {
        $.ajax({
            url: site_url + 'admin/dashboard/filter_exit_trends',
            type: 'GET',
            data: { start_date: startDate, end_date: endDate },
            dataType: 'json',
            success: function(response) {
                const labels = response.map(item => item.date);
                const data = response.map(item => item.count);

                if (exitTrendsChart) {
                    exitTrendsChart.destroy();
                }

                exitTrendsChart = new Chart(exitTrendsChartCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Exits',
                            data: data,
                            borderColor: 'rgb(231, 74, 59)',
                            backgroundColor: 'rgb(231, 74, 59, 0.2)',
                            borderWidth: 1,
                            fill: false
                        }]
                    }
                });
            },
            error: function() {
                danger('Failed to load exit trends data.', "fa fa-exclamation-triangle");
            }                

        });
    }

    function updateUserTypeDistributionChart(startDate, endDate) {
        $.ajax({
            url: site_url + 'admin/dashboard/filter_user_type_distribution',
            type: 'GET',
            data: { start_date: startDate, end_date: endDate },
            dataType: 'json',
            success: function(response) {
                const labels = response.map(item => item.type);
                const data = response.map(item => item.count);

                if (userTypeDistributionChart) {
                    userTypeDistributionChart.destroy();
                }

                userTypeDistributionChart = new Chart(userTypeDistributionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: [
                                'rgb(52, 49, 49)',
                                'rgb(160, 71, 71)',
                                'rgb(128, 0, 0)',
                                'rgb(238, 223, 122)',
                                'rgb(224, 167, 94)',
                                'rgb(249, 214, 137)',
                                'rgb(198, 60, 81)',
                                'rgb(255, 138, 8)'
                            ]
                        }]
                    },
                    options: {
                        cutout: '70%',
                        plugins: {
                            centerText: true
                        }
                    },
                    plugins: [centerTextPlugin]
                });
            },
            error: function() {
                danger('Failed to load user type distribution data.', "fa fa-exclamation-triangle");
            }
        });
    }

    function updatePeakHoursChart(startDate, endDate) {
        $.ajax({
            url: site_url + 'admin/dashboard/get_peak_hours_data',
            type: 'GET',
            data: { start_date: startDate, end_date: endDate },
            dataType: 'json',
            success: function(response) {
                const allHours = [
                    ...response.entries.map(item => parseInt(item.hour, 10)),
                    ...response.exits.map(item => parseInt(item.hour, 10))
                ];

                const minHour = Math.min(...allHours);
                const maxHour = Math.max(...allHours);

                const hoursRange = Array.from({ length: maxHour - minHour + 1 }, (_, i) => i + minHour);
                const formattedHours = hoursRange.map(hour => formatHour(hour));

                const entryTotals = Array(hoursRange.length).fill(0);
                const exitTotals = Array(hoursRange.length).fill(0);

                response.entries.forEach(item => {
                    const hour = parseInt(item.hour, 10);
                    const index = hoursRange.indexOf(hour);
                    if (index !== -1) {
                        entryTotals[index] = Math.max(0, item.total);
                    }
                });

                response.exits.forEach(item => {
                    const hour = parseInt(item.hour, 10);
                    const index = hoursRange.indexOf(hour);
                    if (index !== -1) {
                        exitTotals[index] = Math.max(0, item.total);
                    }
                });

                if (peakHoursChart) {
                    peakHoursChart.destroy();
                }

                peakHoursChart = new Chart(peakHoursChartCtx, {
                    type: 'line',
                    data: {
                        labels: formattedHours,
                        datasets: [{
                            label: 'Entries',
                            data: entryTotals,
                            borderColor: 'rgb(28, 200, 138)',
                            backgroundColor: 'rgb(28, 200, 138, 0.2)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.1
                        },
                        {
                            label: 'Exits',
                            data: exitTotals,
                            borderColor: 'rgb(231, 74, 59)',
                            backgroundColor: 'rgb(231, 74, 59, 0.2)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Peak Hours - Entries and Exits'
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Hour of the Day'
                                },
                                ticks: {
                                    stepSize: 1
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Count'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            },
            error: function() {
                danger('Failed to load peak hours data.', "fa fa-exclamation-triangle");
            }
        });
    }

    // Initial load with default date range (today)
    const today = new Date().toISOString().split('T')[0];
    updateAllCharts(today, today);
});
