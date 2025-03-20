<?php

defined('BASEPATH') or exit('No direct scripts allowed');

class SecurityController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('AuthModel');

        //Check first if there is admin session data
        if ($this->session->userdata('admin') == null) {
            return redirect('admin/login');
        } else {
            //If session data exists, get admin id from session data and verify it with database
            $admin_id = $this->session->userdata('admin')['id'];
            $doesAdminExist = $this->AuthModel->verify_admin_id($admin_id);
            if (!$doesAdminExist) {
                $this->session->sess_destroy();
                return redirect('admin/login');
            }
        }
    }

    protected function checkPermission()
    {
        // Check if the admin has the required role before proceeding
        $required_role_id = 5; // ID of the required role
        if (!require_role($required_role_id)) {
            return; // Stop further execution if unauthorized
        }
    }

    public function securityLogsView()
    {
        $this->checkPermission();
        $d['title'] = 'Security';
        $d['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $d['start'] = $this->input->get('start');
        $d['end'] = $this->input->get('end');
        $data['js'] = 'security_logs.js';

        $type = $this->input->get('submit');


        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $d);
        $this->load->view('admin/security/security_logs', $d);
        $this->load->view('templates/dashboard_footer', $data);
    }
    public function idLogsView()
    {
        $this->checkPermission();
        $d['title'] = 'ID Logs';
        $d['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $d['start'] = $this->input->get('start');
        $d['end'] = $this->input->get('end');
        $data['js'] = 'id_logs.js';

        $type = $this->input->get('submit');


        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $d);
        $this->load->view('admin/security/id_logs', $d);
        $this->load->view('templates/dashboard_footer', $data);
    }

    public function securityLogsList()
    {
        $data = $this->db->select('*')->from('logs_security')->get()->result_array();

        $response = [
            'status' => 200,
            'data' => $data,
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($response['status'])
            ->set_output(json_encode($response));
    }

    public function idLogsList()
    {
        $data = $this->db->select('*')->from('security_id_log')->get()->result_array();

        $response = [
            'status' => 200,
            'data' => $data,
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($response['status'])
            ->set_output(json_encode($response));
    }

    public function securityLogsListFilter()
    {
        $startDate = $this->input->post('start');
        $endDate = $this->input->post('end');

        // Use $startDate and $endDate to filter data from the database
        $data = $this->db
            ->select('*')
            ->from('logs_security')
            ->where('DATE(date) >=', $startDate)
            ->where('DATE(date) <=', $endDate)
            ->get()
            ->result_array();

        $response = [
            'status' => 200,
            'data' => $data,
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($response['status'])
            ->set_output(json_encode($response));
    }

    public function idLogsListFilter()
    {
        $startDate = $this->input->post('start');
        $endDate = $this->input->post('end');

        // Use $startDate and $endDate to filter data from the database
        $data = $this->db
            ->select('*')
            ->from('security_id_log')
            ->where('DATE(date) >=', $startDate)
            ->where('DATE(date) <=', $endDate)
            ->get()
            ->result_array();

        $response = [
            'status' => 200,
            'data' => $data,
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($response['status'])
            ->set_output(json_encode($response));
    }

    public function idLogsAddModal()
    {
        $this->checkPermission();
        $this->load->view('modals/add_id_log');
    }

    public function addIdLog()
    {
        // Set timezone
        date_default_timezone_set('Asia/Manila');

        // Validation rules
        $this->form_validation->set_rules('id_number', 'ID Number', 'required|is_unique[students.id_number]');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|trim', [
            'required' => 'The RFID is required.'
        ]);
        $this->form_validation->set_rules('status', 'Status', 'required|trim', [
            'required' => 'The Status is required.',
        ]);
        $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim|min_length[3]|max_length[255]', [
            'required' => 'The Remarks field is required.',
            'min_length' => 'Remarks must be at least 3 characters.',
            'max_length' => 'Remarks cannot exceed 255 characters.'
        ]);
        $this->form_validation->set_rules('reason', 'Reason', 'required|trim|min_length[3]|max_length[500]', [
            'required' => 'The Reason is required.',
            'min_length' => 'Reason must be at least 3 characters.',
            'max_length' => 'Reason cannot exceed 500 characters.'
        ]);


        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }

        // Collect form data
        $data = [
            'id_number' => $this->input->post('id_number', true),
            'rfid' => $this->input->post('rfid', true),
            'status' => $this->input->post('status', true),
            'reason' => $this->input->post('reason', true),
            'remarks' => $this->input->post('remarks', true),
            'date' => date('Y-m-d h:i:s A')
        ];

        // Insert ID log and log action
        if ($this->db->insert('security_id_log', $data)) {
            $this->db->insert('logs_security', [
                'aid' => $this->session->userdata('admin')['id'],
                'action' => "Added new ID log with RFID# {$data['rfid']}",
                'name' => $this->session->userdata('admin')['username'],
                'date' => date('Y-m-d h:i:s A')
            ]);

            echo json_encode(['status' => 200, 'message' => 'ID log added successfully!']);
        } else {
            echo json_encode(['status' => 500, 'message' => 'Failed to add ID log.']);
        }
    }
}