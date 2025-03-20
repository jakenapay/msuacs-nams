<div class="row">
    <div class="col-lg">
        <h1 class="h3 mb-5 text-center"><?= $title; ?></h1>
    </div>
</div>        

<div class="container">
    
        <div class="input-group justify-content-end">      
            <div class="mb-3">
            </div>  
            <div class="mb-3 shadow dashboard-card">
                <div id="dateRange" class="border-0 px-3 py-2" style="margin-right: 0;">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>
            </div>
        </div>
        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="shadow rounded dashboard-card">
                    <div class="card-body">
                        <h5>Total Visitors Today</h5>
                        <p class="h3"><?php echo $total_visitors_today; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="shadow rounded dashboard-card">
                    <div class="card-body">
                        <h5>Total Entries</h5>
                        <p class="text-success h3" id="totalEntry">0</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="shadow rounded dashboard-card">
                    <div class="card-body">
                        <h5>Total Exits</h5>
                        <p class="text-danger h3" id="totalExit">0</p>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-3">
                <div class="shadow rounded dashboard-card">
                    <div class="card-body">
                        <h5>Pending Approvals</h5>
                      <p class="h3"><?php echo $pending_approvals; ?></p>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- Charts -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="shadow rounded dashboard-card" id="trendsContainer">
                    <div class="card-body">
                        <h5>Entry Trends</h5>
                        <canvas id="visitorTrendsChart"></canvas>
                    </div>
                    <div class="card-body">
                        <h5>Exit Trends</h5>
                        <canvas id="exitTrendsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="shadow rounded dashboard-card" id="chartContainer" style="height: 494px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5>Entries by Type</h5>
                        </div>
                        <canvas id="userTypeDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


                <!-- Peak Hours Chart -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="shadow rounded dashboard-card">
                    <div class="card-body">
                        <h5>Peak Hours</h5>
                        <canvas id="peakHoursChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Live Device Status -->
            <div class="col-lg-12">
                <div class="shadow rounded dashboard-card">
                    <div class="card-header bg-white border-0">
                        <h5>Live Device Status</h5>
                    </div>
                    <!--div class="card-body">
                        <table id="liveAccessTable" class="table table-bordered nowrap" style="width:100%"></table>                        
                    </div -->
                </div>
            </div>
        </div>

        <!-- Recent Logs -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="shadow rounded dashboard-card">
                    <div class="card-body">
                        <h5>Recent Logs</h5>
                        <table class="table table-responsive-lg text-center" id="recentLogsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Name</th>
                                    <th>User Type</th>
                                    <th>Location</th>
                                    <th></th> <!-- New column to indicate Entry/Exit -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_logs as $log): ?>
                                    <tr>
                                        <td><?php echo $log['date']; ?></td>
                                        <td><?php echo date("h:i:s A", strtotime($log['time'])); ?></td>
                                        <td><?php echo $log['fullname']; ?></td>
                                        <td ><?php echo $log['type']; ?></td>
                                        <td><?php echo $log['building']; ?></td>
                                        <td><?php echo $log['log_type'] == 'entry' ? '<span class="badge badge-success">Entry</span>' : '<span class="badge badge-danger">Exit</span>'  ?></td> <!-- Displays Entry/Exit -->
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    <!-- Add JavaScript to render the charts -->
    <script src="<?= base_url('assets/js/libs/chartjs.js'); ?>"></script>
    <script src="<?= base_url('assets/js/dashboard.js'); ?>"></script>
    <script>
    </script>