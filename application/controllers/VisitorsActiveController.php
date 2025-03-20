<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VisitorsActiveController extends CI_Controller {
    
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
        $this->load->model('GuestsModel');
        $this->load->model('LocationsModel');
        $this->load->model('ResidentsModel');
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
        $d['title'] = 'Active Visitors';
        $d['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'visitors_active.js';
        
        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $d);
        $this->load->view('admin/visit_management/visitors_active', $d);
        $this->load->view('templates/dashboard_footer', $data);
    }

    public function visitorsActiveList(){
        
        //Visitors Pending Records for Datatables
        $draw = $this->input->get('draw');
        $start = $this->input->get('start');
        $length = $this->input->get('length');
        $search = $this->input->get('search')['value'];
        $orderColumn = $this->input->get('order')[0]['column'] + 1;
        $orderDir = $this->input->get('order')[0]['dir'];

        $data['locations'] = $this->LocationsModel->get_all();
        // Specify only the required columns from the faculty table
        $columns = [
            'id',
            'rfid',
            'first_name',
            'last_name',
            'visit_purpose',
            'visit_date',
            'visit_time',
            'phone_number',
            'email',
            'status',
            'is_banned'
        ];

        // Your custom logic to fetch data based on parameters
        $this->db->select(implode(',', $columns))
            ->from('visitors_active')
            ->group_start()
            ->like('id', $search)
            ->or_like('first_name', $search)
            ->or_like('rfid', $search)
            ->or_like('last_name', $search)
            ->or_like('email', $search)     
            ->or_like('phone_number', $search) 
            ->or_like('image', $search) 
            ->or_like('visit_purpose', $search) 
            ->or_like('visit_date', $search) 
            ->or_like('visit_time', $search)
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
        $totalRecords = $this->db->count_all_results('visitors_active');

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

    public function editActiveVisitorView($id) {
        // Ensure the id is sanitized and valid
        $id = intval($id);
        $visitor = $this->db->get_where('visitors_active', ['id' => $id])->row_array();
        $departments = $this->DepartmentsModel->get_all(); // Fetch departments data
    
        // Check if visitor data was retrieved
        if ($visitor) {
            $this->load->view('modals/edit_visitors_active', ['visitor' => $visitor, 'departments' => $departments]);
        } else {
            // Handle the case where visitor data is not found
            echo "Visitor not found.";
        }
    }

    public function editActiveVisitor($id){
        if ($this->input->post('rfid') !== $this->input->post('old_rfid')) {
            
            $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');
    
            if ($this->form_validation->run() == FALSE) {
                echo json_encode([
                    'status' => 400, 
                    'message' => $this->form_validation->error_array()
                ]);   
    
            }
            else {
                $data = [
                    'rfid' => $this->input->post('rfid'),
                ];
    
                if ($this->VisitorsApproveModel->update($id, $data)) {
    
                    $this->AuthModel->insertSecurity(
                        $this->session->userdata('admin')['id'], 
                        "Edited Active Visitor information with ID# " . $id, 
                        $this->session->userdata('admin')['username'], 
                    );
                    
                    echo json_encode([
                        'status' => 200, 
                        'message' => 'Form updated successfully!']);
                }
                else{
                    echo json_encode([
                        'status' => 500, 
                        'message' => 'Failed to update form.']);
                }
            }    
        }
        else {
            echo json_encode([
                'status' => 200, 
                'message' => 'Form saved successfully!']);
        }
    }

    public function verifyBanVisitor($id)
    {   
        $adminPassword = trim($this->input->post('password'));
        $adminReason = trim($this->input->post('reason'));
        $adminLocations = trim($this->input->post('locations'));
        $adminLocationNames = trim($this->input->post('locationNames'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);
    
        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);
        
        if (empty($adminReason)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please provide a reason for banning the visitor.'
            ]);
            return;
        } else if($isPasswordCorrect) {
            $this->banVisitor($id, $adminReason, $adminLocations, $adminLocationNames);
        } elseif (empty($adminLocations)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please select at least one location to ban the visitor.'
            ]);
            return;
        }
        else {
            echo json_encode([
                'status' => 500, 
                'message' => 'Invalid password']);
        }
    }

    private function banVisitor($id, $reason = null, $locations = null, $locationNames = null){
        $this->VisitorsApproveModel->banVisitor($id, $reason, $locations, $locationNames);
        $rfid = $this->VisitorsApproveModel->getRFID($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'], 
                "Banned a active visitor with RFID# " . $rfid . ($reason ? " for reason: " . $reason : "") . ($locationNames ? " in locations: " . $locationNames : ""),
                $this->session->userdata('admin')['username'], 
            );
            
            echo json_encode([
                'status' => 200, 
                'message' => 'Active Visitor successfully banned!']);
        } else {

            echo json_encode([
                'status' => 200, 
                'message' => 'Failed to ban active visitor!']);
        }
    }

    public function verifyUnbanVisitor($id)
    {   
        $adminPassword = trim($this->input->post('password'));
        $adminReason = trim($this->input->post('reason'));
        $adminLocations = trim($this->input->post('locations'));
        $adminLocationNames = trim($this->input->post('locationNames'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);
    
        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);
    
        if (empty($adminReason)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please provide a reason for unbanning the visitor.'
            ]);
        }
        else if($isPasswordCorrect){
            $this->unbanVisitor($id, $adminReason, $adminLocations, $adminLocationNames);
        }
        else {
            echo json_encode([
                'status' => 500, 
                'message' => 'Invalid password']);
        }
    }

    private function unbanVisitor($id, $reason = null, $locations = null, $locationNames = null){
        $this->VisitorsApproveModel->unbanVisitor($id, $reason, $locations, $locationNames);
        $rfid = $this->VisitorsApproveModel->getRFID($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'], 
                "Unbanned a Visitor with RFID# " . $rfid . ($reason ? " for reason: " . $reason : "") . ($locationNames ? " in locations: " . $locationNames : ""), 
                $this->session->userdata('admin')['username'], 
            );
            
            echo json_encode([
                'status' => 200, 
                'message' => 'Active Visitor successfully banned!']);
        } else {

            echo json_encode([
                'status' => 200, 
                'message' => 'Failed to ban active visitor!']);
        }
    }

    public function getBannedLocationsVisitors($id)
    {
        $response = $this->VisitorsApproveModel->getBannedLocations($id);

        if ($response) {
            // Check if response is an object
            $bannedLocations = array_map(function ($loc) {
                return [
                    'id' => $loc['id'],
                    'name' => $loc['name']
                ];
            }, $response);

            echo json_encode([
                'status' => 200,
                'data' => $bannedLocations // Output banned location's id and name as an array of objects
            ]);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Failed to retrieve visitor banned locations!'
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
        $old_image = $this->VisitorsApproveModel->getImage($id);
        if ($old_image && file_exists('./'. $old_image)) {
            unlink('./'. $old_image);
        }

        $rfid = $this->VisitorsApproveModel->getRFID($id);
        $this->VisitorsApproveModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'], 
                "Deleted an Active Visitor with RFID# " . $rfid, 
                $this->session->userdata('admin')['username'], 
            );
            
            echo json_encode([
                'status' => 200, 
                'message' => 'Active Visitor successfully deleted!']);
        } else {

            echo json_encode([
                'status' => 200, 
                'message' => 'Failed to delete active visitor!']);
        }
    }

    public function concludeVisitor($id){
        $visitor = $this->VisitorsApproveModel->get_by_id($id);
        unset($visitor->id);
        $visitor->status = 4;
        unset($visitor->location);
        unset($visitor->indicator);
        if (isset($visitor->ban_reason)) {
            unset($visitor->ban_reason);
        }
        if (isset($visitor->banned_location)) {
            unset($visitor->banned_location);
        }
        $this->VisitorsCompletedModel->insert($visitor);
        $this->VisitorsApproveModel->delete($id);
        $rows = $this->db->affected_rows();

        if($rows > 0){
            echo json_encode([
                'status' => 200, 
                'message' => 'Visitor successfully concluded!']);
        }

        else{
            echo json_encode([
                'status' => 500, 
                'message' => ['Failed to conclude visitor.']]);
        }
    }

        /**
     * Custom callback function to validate RFID uniqueness across multiple tables
     *
     * @param string $rfid
     * @return bool
     */
    public function _validate_rfid($rfid)
    {        
        $user_id = $this->input->post('id');
        // Check if RFID is unique in the students table
        if ($this->StudentsModel->is_rfid_exists('students', $rfid, $user_id))
        {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a student.');
            return false;
        }
        
        // Check if RFID is unique in the faculty table
        if ($this->FacultyModel->is_rfid_exists('faculty', $rfid, $user_id))
        {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a faculty member.');
            return false;
        }
        
        // Check if RFID is unique in the staff table
        if ($this->StaffsModel->is_rfid_exists('staff', $rfid, $user_id))
        {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a staff member.');
            return false;
        }

        // Check if RFID is unique in the deivery services table
        if ($this->ResidentsModel->is_rfid_exists('residents', $rfid, $user_id))
        {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a resident member.');
            return false;
        }

        // Check if RFID is unique in the others table
        if ($this->GuestsModel->is_rfid_exists('guests', $rfid, $user_id))
        {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a guest member.');
            return false;
        }

        // Check if RFID is unique in the others table
        if ($this->VisitorsApproveModel->is_rfid_exists('visitors_active', $rfid, $user_id))
        {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for an active visitor.');
            return false;
        }


        
        // RFID is unique across all tables
        return true;
    }   
}    