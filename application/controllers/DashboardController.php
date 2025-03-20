<?php

defined('BASEPATH') or exit ('No direct scripts allowed');

class DashboardController extends CI_Controller{

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->model('AuthModel');
        $this->load->model('LocationsModel');
        $this->load->model('DevicesModel');
        $this->load->model('CollegesModel');
        $this->load->model('DepartmentsModel');
        $this->load->model('ProgramsModel');
        $this->load->model('DashboardModel');

        //Check first if there is admin session data
        if ($this->session->userdata('admin') == null) {
            return redirect('admin/login');
        }
        else{
            //If session data exists, get admin id from session data and verify it with database
            $admin_id = $this->session->userdata('admin')['id'];
            $doesAdminExist = $this->AuthModel->verify_admin_id($admin_id);
            if (!$doesAdminExist) {
                $this->session->sess_destroy();
                return redirect('admin/login');
            }         
        }                   
    }

    public function index() {
        $data['title'] = 'Dashboard';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        
        // Fetch the selected date range or default to the last 30 days
        $start_date = $this->input->get('start_date', TRUE) ?: date('Y-m-d', strtotime('-29 days'));
        $end_date = $this->input->get('end_date', TRUE) ?: date('Y-m-d');

        // Fetching data for the dashboard with date range
        $data['total_visitors_today'] = $this->DashboardModel->getTotalVisitorsToday($start_date, $end_date);
        $data['pending_approvals'] = $this->DashboardModel->getPendingApprovals();
        $data['recent_logs'] = $this->DashboardModel->getRecentLogs($start_date, $end_date);
        $d['js'] = 'device_status.js';

        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/dashboard/dashboard');
        $this->load->view('templates/dashboard_footer', $d);
    }

    public function liveDevices(){
        $devices = $this->DevicesModel->get_all();
        // Initialize the data array
        $data['data'] = [];

        foreach ($devices as $device) {
            // Ping the device IP address
            $responseTime = ping($device->ip);

            // Determine the status based on the ping response
            $status = $responseTime !== false ? 'Online' : 'Offline';

            // Add the device data to the array
            $data['data'][] = [
                'id' => $device->id,
                'device_id' => $device->device_id,
                'status' => $status,
                'location' => $this->LocationsModel->getLocationName($device->location_id),
                'type' => $device->type,
                'name' => $device->name,
                'ip_address' => $device->ip,
                'response_time' => $responseTime !== false ? $responseTime . 'ms' : 'N/A',
            ];
        }

        // Return or process the $data array as needed
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($data));
    }

    public function filterEntryTrends(){
        // Fetch the selected date range or default to the last 30 days
        $start_date = $this->input->get('start_date', TRUE) ?: date('Y-m-d', strtotime('-29 days'));
        $end_date = $this->input->get('end_date', TRUE) ?: date('Y-m-d');

        $data = $this->DashboardModel->getVisitorTrends($start_date, $end_date);
        echo json_encode($data);

    }

    public function filterExitTrends() {
        $start_date = $this->input->get('start_date', TRUE);
        $end_date = $this->input->get('end_date', TRUE);

        $data = $this->DashboardModel->getExitTrends($start_date, $end_date);
        echo json_encode($data);
    }
    
    public function filterUserTypeDistribution() {
        $start_date = $this->input->get('start_date', TRUE);
        $end_date = $this->input->get('end_date', TRUE);

        $data = $this->DashboardModel->filterUserTypeDistribution($start_date, $end_date);
        echo json_encode($data);
    }

    public function getPeakHoursData() {
        $start_date = $this->input->get('start_date', TRUE);
        $end_date = $this->input->get('end_date', TRUE);

        $peak_hours_entries = $this->DashboardModel->getPeakHours($start_date, $end_date);
        $peak_hours_exits = $this->DashboardModel->getPeakHoursExit($start_date, $end_date);
        
        $data = [
            'entries' => $peak_hours_entries,
            'exits' => $peak_hours_exits
        ];
        
        echo json_encode($data);
    }

    public function filterTotalEntryExit(){
        $start_date = $this->input->get('start_date', TRUE);
        $end_date = $this->input->get('end_date', TRUE);

        $total_entries = $this->DashboardModel->getTotalEntries($start_date, $end_date);
        $total_exits = $this->DashboardModel->getTotalExits($start_date, $end_date);

        $data = [
            'total_entries' => $total_entries,
            'total_exits' => $total_exits
        ];

        echo json_encode($data);
    }
    

}