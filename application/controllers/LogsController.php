<?php
defined ('BASEPATH') or exit ('No direct script access allowed');


class LogsController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('AuthModel');
        $this->load->model('StudentsModel');
        $this->load->model('CollegesModel');
        $this->load->model('DepartmentsModel');
        $this->load->model('ProgramsModel');
        $this->load->model('LocationsModel');
        $this->load->model('AccessModel');
        $this->load->library('form_validation');

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

    protected function checkPermission(){
        // Check if the admin has the required role before proceeding
        $required_role_id = 2; // ID of the required role
        if (!require_role($required_role_id)) {
            return; // Stop further execution if unauthorized
        }
    }

    public function index() {
        $this->checkPermission();
        $data['title'] = 'Entry Logs';
        $data['locations'] = $this->LocationsModel->get_all();
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'entry_logs.js';
    
        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/logs/entry');
        $this->load->view('templates/dashboard_footer');
    }
    
    public function entryLogsList(){
        $draw = $this->input->get('draw');
        $start = $this->input->get('start');
        $length = $this->input->get('length');
        $search = $this->input->get('search')['value'];
        $orderColumn = $this->input->get('order')[0]['column'] + 1;
        $orderDir = $this->input->get('order')[0]['dir'];
    
        // Specify the columns to select, including the names from joined tables
        $columns = [
            'entry_logs.id',
            'entry_logs.date',
            'entry_logs.time',
            'entry_logs.rfid',
            'entry_logs.fullname',
            'entry_logs.type',
            'entry_logs.college',
            'entry_logs.department',
            'entry_logs.program',
            'entry_logs.building',
            'entry_logs.gate',
        ];
    
        // Your custom logic to fetch data based on parameters
        $this->db->select(implode(',', $columns))
            ->from('entry_logs')
            ->group_start()
            ->like('entry_logs.id', $search)
            ->or_like('entry_logs.date', $search)
            ->or_like('entry_logs.time', $search)
            ->or_like('entry_logs.rfid', $search)
            ->or_like('entry_logs.fullname', $search)
            ->or_like('entry_logs.type', $search)
            ->or_like('entry_logs.college', $search)
            ->or_like('entry_logs.department', $search)     
            ->or_like('entry_logs.program', $search)     
            ->or_like('entry_logs.building', $search)
            ->or_like('entry_logs.gate', $search)
            ->group_end()
            ->order_by($orderColumn, $orderDir)
            ->limit($length, $start);
    
        // Initial sorting by the first column (ID) in descending order
        if ($draw === '1') {
            $this->db->order_by('entry_logs.id', 'desc');
        }

        $data = $this->db->get()->result_array();
    
        // Count total records without filtering
        $totalRecords = $this->db->count_all_results('entry_logs');
    
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function filterEntryLocation()
    {
        $location = $this->input->post('location');
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'];
        $orderColumn = $this->input->post('order')[0]['column'] + 1;
        $orderDir = $this->input->post('order')[0]['dir'];
    
        // Specify the columns to select
        $columns = [
            'entry_logs.id',
            'entry_logs.date',
            'entry_logs.time',
            'entry_logs.rfid',
            'entry_logs.fullname',
            'entry_logs.type',
            'entry_logs.college',
            'entry_logs.department',
            'entry_logs.program',
            'entry_logs.building',
            'entry_logs.gate',
        ];
    
        // Build the query
        $this->db->select(implode(',', $columns))
            ->from('entry_logs')
            ->where('entry_logs.building', $location) // Filter by location
            ->group_start()
            ->like('entry_logs.id', $search)
            ->or_like('entry_logs.date', $search)
            ->or_like('entry_logs.time', $search)
            ->or_like('entry_logs.rfid', $search)
            ->or_like('entry_logs.fullname', $search)
            ->or_like('entry_logs.type', $search)
            ->or_like('entry_logs.college', $search)
            ->or_like('entry_logs.department', $search)     
            ->or_like('entry_logs.program', $search)     
            ->or_like('entry_logs.building', $search)
            ->or_like('entry_logs.gate', $search)
            ->group_end()
            ->order_by($orderColumn, $orderDir)
            ->limit($length, $start);
    
        // Initial sorting by the first column (ID) in descending order
        if ($draw === '1') {
            $this->db->order_by('entry_logs.id', 'desc');
        }
    
        $data = $this->db->get()->result_array();
    
        // Count total records without filtering
        $this->db->where('entry_logs.building', $location);
        $totalRecords = $this->db->count_all_results('entry_logs');
    
        // Count filtered records
        $this->db->where('entry_logs.building', $location);
        $this->db->from('entry_logs');
        $this->db->group_start()
            ->like('entry_logs.id', $search)
            ->or_like('entry_logs.date', $search)
            ->or_like('entry_logs.time', $search)
            ->or_like('entry_logs.rfid', $search)
            ->or_like('entry_logs.fullname', $search)
            ->or_like('entry_logs.type', $search)
            ->or_like('entry_logs.college', $search)
            ->or_like('entry_logs.department', $search)     
            ->or_like('entry_logs.program', $search)     
            ->or_like('entry_logs.building', $search)
            ->or_like('entry_logs.gate', $search)
            ->group_end();
        $filteredRecords = $this->db->count_all_results();
    
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    /***********************EXIT LOGS**********************************/

    public function exit() {
        $this->checkPermission();
        $data['title'] = 'Exit Logs';
        $data['locations'] = $this->LocationsModel->get_all();
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'exit_logs.js';
    
        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/logs/exit');
        $this->load->view('templates/dashboard_footer');
    }
    
    public function exitLogsList(){
        $draw = $this->input->get('draw');
        $start = $this->input->get('start');
        $length = $this->input->get('length');
        $search = $this->input->get('search')['value'];
        $orderColumn = $this->input->get('order')[0]['column'] + 1;
        $orderDir = $this->input->get('order')[0]['dir'];
    
        // Specify the columns to select, including the names from joined tables
        $columns = [
            'exit_logs.id',
            'exit_logs.date',
            'exit_logs.time',
            'exit_logs.rfid',
            'exit_logs.fullname',
            'exit_logs.building',
            'exit_logs.type',
            'exit_logs.college',
            'exit_logs.department',
            'exit_logs.program',
            'exit_logs.gate',
        ];
    
        // Your custom logic to fetch data based on parameters
        $this->db->select(implode(',', $columns))
            ->from('exit_logs')
            ->group_start()
            ->like('exit_logs.id', $search)
            ->or_like('exit_logs.date', $search)
            ->or_like('exit_logs.time', $search)
            ->or_like('exit_logs.rfid', $search)
            ->or_like('exit_logs.fullname', $search)
            ->or_like('exit_logs.type', $search)
            ->or_like('exit_logs.college', $search)
            ->or_like('exit_logs.department', $search)     
            ->or_like('exit_logs.program', $search)  
            ->or_like('exit_logs.building', $search)
            ->or_like('exit_logs.gate', $search)
            ->group_end()
            ->order_by($orderColumn, $orderDir)
            ->limit($length, $start);
    
        // Initial sorting by the first column (ID) in descending order
        if ($draw === '1') {
            $this->db->order_by('exit_logs.id', 'desc');
        }

        $data = $this->db->get()->result_array();
    
        // Count total records without filtering
        $totalRecords = $this->db->count_all_results('exit_logs');
    
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function filterExitLocation()
    {
        $location = $this->input->post('location');
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'];
        $orderColumn = $this->input->post('order')[0]['column'] + 1;
        $orderDir = $this->input->post('order')[0]['dir'];
    
        // Specify the columns to select
        $columns = [
            'exit_logs.id',
            'exit_logs.date',
            'exit_logs.time',
            'exit_logs.rfid',
            'exit_logs.fullname',
            'exit_logs.type',
            'exit_logs.college',
            'exit_logs.department',
            'exit_logs.program',
            'exit_logs.building',
            'exit_logs.gate',
        ];
    
        // Build the query
        $this->db->select(implode(',', $columns))
            ->from('exit_logs')
            ->where('exit_logs.building', $location) // Filter by location
            ->group_start()
            ->like('exit_logs.id', $search)
            ->or_like('exit_logs.date', $search)
            ->or_like('exit_logs.time', $search)
            ->or_like('exit_logs.rfid', $search)
            ->or_like('exit_logs.fullname', $search)
            ->or_like('exit_logs.type', $search)
            ->or_like('exit_logs.college', $search)
            ->or_like('exit_logs.department', $search)     
            ->or_like('exit_logs.program', $search)     
            ->or_like('exit_logs.building', $search)
            ->or_like('exit_logs.gate', $search)
            ->group_end()
            ->order_by($orderColumn, $orderDir)
            ->limit($length, $start);
    
        // Initial sorting by the first column (ID) in descending order
        if ($draw === '1') {
            $this->db->order_by('exit_logs.id', 'desc');
        }
    
        $data = $this->db->get()->result_array();
    
        // Count total records without filtering
        $this->db->where('exit_logs.building', $location);
        $totalRecords = $this->db->count_all_results('exit_logs');
    
        // Count filtered records
        $this->db->where('exit_logs.building', $location);
        $this->db->from('exit_logs');
        $this->db->group_start()
            ->like('exit_logs.id', $search)
            ->or_like('exit_logs.date', $search)
            ->or_like('exit_logs.time', $search)
            ->or_like('exit_logs.rfid', $search)
            ->or_like('exit_logs.fullname', $search)
            ->or_like('exit_logs.type', $search)
            ->or_like('exit_logs.college', $search)
            ->or_like('exit_logs.department', $search)     
            ->or_like('exit_logs.program', $search)     
            ->or_like('exit_logs.building', $search)
            ->or_like('exit_logs.gate', $search)
            ->group_end();
        $filteredRecords = $this->db->count_all_results();
    
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }
}    