<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VisitorsDeclinedController extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('VisitorsPendingModel');
        $this->load->model('VisitorsApproveModel');
        $this->load->model('VisitorsDeclineModel');
        $this->load->model('VisitorsCompletedModel');
        $this->load->model('AuthModel');
        $this->load->model('StudentsModel');
        $this->load->model('FacultyModel');
        $this->load->model('StaffsModel');
        $this->load->model('CollegesModel');
        $this->load->model('DepartmentsModel');
        $this->load->model('ProgramsModel');
        $this->load->helper('file'); // For file operations
        $this->load->library('email');


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
        $required_role_id = 4; // ID of the required role
        if (!require_role($required_role_id)) {
            return; // Stop further execution if unauthorized
        }
    }
    
    public function index(){
        $this->checkPermission();
        $d['title'] = 'Declined Visitors';
        $d['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'visitors_declined.js';
        
        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $d);
        $this->load->view('admin/visit_management/visitors_decline', $d);
        $this->load->view('templates/dashboard_footer', $data);
    }

    public function declinedList(){
        //Visitors Completed Records for Datatables
        $draw = $this->input->get('draw');
        $start = $this->input->get('start');
        $length = $this->input->get('length');
        $search = $this->input->get('search')['value'];
        $orderColumn = $this->input->get('order')[0]['column'] + 1;
        $orderDir = $this->input->get('order')[0]['dir'];

        // Specify only the required columns from the faculty table
        $columns = [
            'id',
            'first_name',
            'last_name',
            'visit_purpose',
            'visit_date',
            'visit_time',
            'decline_reason',
            'DATE(updated_at) as date',
            'status',
        ];

        // Your custom logic to fetch data based on parameters
        $this->db->select(implode(',', $columns))
            ->from('visitors_declined')
            ->group_start()
            ->like('id', $search)
            ->or_like('first_name', $search)
            ->or_like('last_name', $search)
            ->or_like('visit_purpose', $search) 
            ->or_like('visit_date', $search) 
            ->or_like('visit_time', $search)
            ->or_like('decline_reason', $search)
            ->or_like('DATE(updated_at)', $search)  
            ->or_like('status', $search)
            ->group_end()
            ->order_by($orderColumn, $orderDir)
            ->limit($length, $start);

        // Initial sorting by the first column (ID) in descending order
        if ($draw === '1') {
            $this->db->order_by('id', 'desc');
        }

        $data = $this->db->get()->result_array();

        // Count total records without filtering
        $totalRecords = $this->db->count_all_results('visitors_declined');

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

    public function viewVisitor($id) {
        $visitor = $this->db->get_where('visitors_declined', ['id' => $id])->row_array();
        if ($visitor) {
            $this->load->view('modals/view_declined_visitor', ['visitor' => $visitor]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => 'Visitor not found.'
            ]);
        }
    }   
    
    public function verifyDeleteVisitor($id)
    {   
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);
    
        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);
    
        $response = $isPasswordCorrect;
        if($response === true){
            $this->deleteVisitor($id);
        }
        else {
            echo json_encode([
                'status' => 500, 
                'message' => 'Invalid password']);
        }
    }

    private function deleteVisitor($id){
        // Delete old image if exists
        $old_image = $this->VisitorsDeclineModel->getImage($id);
        if ($old_image && file_exists('./'. $old_image)) {
            unlink('./'. $old_image);
        }

        $this->VisitorsDeclineModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'], 
                "Deleted an Declined Visitor with ID: " . $id, 
                $this->session->userdata('admin')['username'], 
            );
            
            echo json_encode([
                'status' => 200, 
                'message' => 'Visitor Data successfully deleted!']);
        } else {

            echo json_encode([
                'status' => 200, 
                'message' => 'Failed to delete visitor data!']);
        }
    }


}    