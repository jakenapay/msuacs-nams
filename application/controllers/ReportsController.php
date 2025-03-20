<?php

defined ('BASEPATH') or exit ('No direct scripts access allowed');

class ReportsController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->model('AuthModel');
        $this->load->model('ReportsModel');
        $this->load->model('CollegesModel');
        $this->load->model('OfficesModel');
        $this->load->model('LocationsModel');

        // Check if there is admin session data
        if ($this->session->userdata('admin') == null) {
            redirect('admin/login');
        } else {
            // If session data exists, get admin id from session data and verify it with database
            $admin_id = $this->session->userdata('admin')['id'];
            $doesAdminExist = $this->AuthModel->verify_admin_id($admin_id);
            if (!$doesAdminExist) {
                $this->session->sess_destroy();
                redirect('admin/login');
            }
        }
    }
    
    protected function checkPermission(){
        // Check if the admin has the required role before proceeding
        $required_role_id = 6; // ID of the required role
        if (!require_role($required_role_id)) {
            return; // Stop further execution if unauthorized
        }
    }

    public function index() {
        $this->checkPermission();
        $data['colleges'] = $this->CollegesModel->get_all();
        $data['offices'] = $this->OfficesModel->get_all();
        $data['locations'] = $this->LocationsModel->get_all();
        $data['title'] = 'Entry Logs Report';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'reports.js';
    
        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/reports/logs');
        $this->load->view('templates/dashboard_footer', $data);
    }

    public function filter() {
        // Get parameters from the request
        $dateRange = $this->input->get('dateRange');
        $userType = $this->input->get('userType');
        $college = $this->input->get('college');
        $department = $this->input->get('department');
        $program = $this->input->get('program');
        $office = $this->input->get('office');
        $type = $this->input->get('type');
        $location = $this->input->get('location');
    
        // Get the parameters for DataTables
        $start = $this->input->get('start');
        $length = $this->input->get('length');
        $draw = $this->input->get('draw');
    
        // Initialize variables for DataTables response
        $response = array();
        
        // Get the total number of records (before any filtering)
        $totalRecords = $this->ReportsModel->getTotalLogsCount();
    
        // Get the filtered data
        $filteredData = $this->ReportsModel->getFilteredLogs($dateRange, $userType, $college, $department, $program, $type, $location, $office);
        
        // Get the number of records after filtering
        $filteredRecords = $this->ReportsModel->getFilteredLogsCount($dateRange, $userType, $college, $department, $program, $type, $location, $office);
    
        // Format the response
        $response['draw'] = intval($draw);
        $response['recordsTotal'] = intval($totalRecords);
        $response['recordsFiltered'] = intval($filteredRecords);
        $response['data'] = $filteredData;
    
        // Output the JSON response
        echo json_encode($response);

        // echo json_encode([
        //     'Date Range' => $dateRange, 
        //     'User Type' => $userType, 
        //     'College' => $college, 
        //     'Department' => $department,
        //     'Program' => $program,
        //     'Type' => $type,
        //     'Office' => $office,
        // ]);
    }
    
}