<?php
defined ('BASEPATH') or exit ('No direct scripts access allowed.');


class ConfigurationsController extends CI_Controller {

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
        $this->load->model('OfficesModel');

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
        $required_role_id = 7; // ID of the required role
        if (!require_role($required_role_id)) {
            return; // Stop further execution if unauthorized
        }
    }

    public function index() {
        $this->checkPermission();
        $data['title'] = 'Locations';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'locations.js';
    
        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/configurations/locations');
        $this->load->view('templates/dashboard_footer', $data);
    }

    public function locationList(){
        $data = $this->db->select('*')->from('locations')->get()->result_array();
        
        $response = [
            'status' => 200,
            'data' => $data,
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($response['status'])
            ->set_output(json_encode($response));
    }

    public function addLocationView(){
        $this->load->view('modals/add_location');
    }

    public function addLocation(){
        $this->form_validation->set_rules('name', 'Name', 'required|trim', array(
            'required'      => 'The Location %s field is required.',
        ));

        if($this->form_validation->run() == FALSE){
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }

        else{
            $data = [
                'name' => $this->input->post('name'),
            ];

            $this->LocationsModel->create($data);
            $rows = $this->db->affected_rows();
    
            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Added a new Location with Name: " . $this->input->post('name'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Location successfully added!']);
            } else {
    
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Failed to add location!']);
            }
        }
    }

    public function editLocationView($id){
        $this->checkPermission();
        $data['location'] = $this->LocationsModel->get($id);
        $this->load->view('modals/edit_location', $data);
    }

    public function editLocation($id){
        $this->form_validation->set_rules('name', 'Name', 'required|trim', array(
            'required'      => 'The Location %s field is required.',
        ));

        if($this->form_validation->run() == FALSE){
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }
        else{
            $data = [
                'name' => $this->input->post('name'),
            ];

            $this->LocationsModel->update($id, $data);
            $rows = $this->db->affected_rows();

            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Updated Location with ID: " . $id . " and Name: " . $this->input->post('name'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Location successfully updated!']);
            } else {
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Failed to update location!']);
            }
        }
    }

    public function verifyDeleteLocation($id){
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);
    
        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);
    
        $response = $isPasswordCorrect;
        if($response === true){
            $this->deleteLocation($id);
        }
        
        else {
            echo json_encode([
                'status' => 500, 
                'message' => 'Invalid password']);
        }
    }

    public function deleteLocation($id){
        $this->LocationsModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'], 
                "Deleted Location with ID: " . $id, 
                $this->session->userdata('admin')['username'], 
            );
            
            echo json_encode([
                'status' => 200, 
                'message' => 'Location successfully deleted!']);
        } else {
            echo json_encode([
                'status' => 200, 
                'message' => 'Failed to delete location!']);
        }
    }

    public function devices() {
        $this->checkPermission();
        $data['title'] = 'Devices';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'devices.js';

        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/configurations/devices');
        $this->load->view('templates/dashboard_footer', $data);
    }

    public function deviceList() {
        $this->db->select('devices.*, locations.name as location_name');
        $this->db->from('devices');
        $this->db->join('locations', 'devices.location_id = locations.id', 'left');
        $data = $this->db->get()->result_array();
        
        $response = [
            'status' => 200,
            'data' => $data,
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($response['status'])
            ->set_output(json_encode($response));
    }

    public function addDeviceView(){
        $this->checkPermission();
        $data['locations'] = $this->LocationsModel->get_all();
        $this->load->view('modals/add_device', $data);
    }

    public function addDevice(){
        $this->form_validation->set_rules('device_id', 'Device ID', 'required|trim|is_unique[devices.device_id]');
        $this->form_validation->set_rules('location_id', 'Location', 'required|trim', array(
            'required'      => 'The Location %s field is required.',
        ));
        $this->form_validation->set_rules('name', 'Device Name', 'required|trim|is_unique[devices.name]', array(
            'required'      => 'The Device Name %s field is required.',
            'is_unique'     => 'This Device Name already exists.'
        ));
        $this->form_validation->set_rules('ip', 'IP Address', 'required|trim');
        $this->form_validation->set_rules('type', 'Device Type', 'required|trim');

        
        if($this->form_validation->run() == FALSE){
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }
        else{
            $data = [
                'device_id' => $this->input->post('device_id'),
                'location_id' => $this->input->post('location_id'),
                'name' => $this->input->post('name'),
                'ip' => $this->input->post('ip'),
                'type' => $this->input->post('type'),
            ];

            $this->DevicesModel->create($data);
            $rows = $this->db->affected_rows();
            
            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Added a new Device with Name: " . $this->input->post('name'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Device successfully added!']);
            } else {
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Failed to add device!']);
            }
        }
    }

    public function editDeviceView($id){
        $this->checkPermission();
        $data['device'] = $this->DevicesModel->get($id);
        $data['locations'] = $this->LocationsModel->get_all();
        $this->load->view('modals/edit_device', $data);
    }

    public function editDevice($id){
        $this->form_validation->set_rules('device_id', 'Device ID', 'required|trim|callback_check_unique_device_id[' . $id . ']');
        $this->form_validation->set_rules('location_id', 'Location', 'required|trim', array(
            'required'      => 'The Location %s field is required.',
        ));
        $this->form_validation->set_rules('name', 'Device Name', 'required|trim|callback_check_unique_device_name[' . $id . ']', array(
            'required'      => 'The Device Name %s field is required.',
        ));
        $this->form_validation->set_rules('ip', 'IP Address', 'required|trim');
        $this->form_validation->set_rules('type', 'Device Type', 'required|trim');

        if($this->form_validation->run() == FALSE){
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }
        else{
            
            $data = [
                'device_id' => $this->input->post('device_id'),
                'location_id' => $this->input->post('location_id'),
                'name' => $this->input->post('name'),
                'ip' => $this->input->post('ip'),
                'type' => $this->input->post('type'),
            ];

            $this->DevicesModel->update($id, $data);
            $rows = $this->db->affected_rows();

            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Updated device with ID: " . $id . " and Name: " . $this->input->post('name'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Device successfully updated!']);
            } else {
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Failed to update device!']);
            }
        }
    }

    public function toggleModeView(){
        $this->checkPermission();
        $data['mode'] = $this->DevicesModel->getCurrentMode();
        $this->load->view('modals/toggle_device_mode', $data);
    }

    public function toggleMode(){
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);
        $modeInput = trim($this->input->post('mode'));
    
        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);
    
        $response = $isPasswordCorrect;
        if($response === true){
            $this->toogleModeConfirmed($modeInput);
        }
        
        else {
            echo json_encode([
                'status' => 500, 
                'message' => 'Invalid password']);
        }
    }

    public function toogleModeConfirmed($modeInput){
        $data = [
            'mode' => $modeInput
        ];

        $this->DevicesModel->updateMode($data);
        $rows = $this->db->affected_rows();
        
        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'], 
                $modeInput == 1 ? 'Toggled device mode to: Production Mode' : 'Toggled device mode to: Testing Mode', 
                $this->session->userdata('admin')['username'], 
            );
            
            echo json_encode([
                'status' => 200, 
                'message' => 'Device mode successfully toggled!']);
        } else {
            echo json_encode([
                'status' => 200, 
                'message' => 'Failed to toggle device mode!']);
        }
    }

    public function verifyDeleteDevice($id){
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);
    
        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);
    
        $response = $isPasswordCorrect;
        if($response === true){
            $this->deleteDevice($id);
        }
        
        else {
            echo json_encode([
                'status' => 500, 
                'message' => 'Invalid password']);
        }
    }

    public function deleteDevice($id){
        $this->DevicesModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'], 
                "Deleted Device with ID: " . $id, 
                $this->session->userdata('admin')['username'], 
            );
            
            echo json_encode([
                'status' => 200, 
                'message' => 'Device successfully deleted!']);
        } else {
            echo json_encode([
                'status' => 200, 
                'message' => 'Failed to delete device!']);
        }
    }

    public function colleges() {
        $this->checkPermission();
        $data['title'] = 'Colleges';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'colleges.js';

        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/configurations/colleges');
        $this->load->view('templates/dashboard_footer', $data);
    }

    public function collegeList() {
        $data = $this->db->select('*')->from('colleges')->get()->result_array();
        
        $response = [
            'status' => 200,
            'data' => $data,
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($response['status'])
            ->set_output(json_encode($response));
    }

    public function addCollegeView(){
        $this->checkPermission();
        $this->load->view('modals/add_college');
    }

    public function addCollege(){
        $this->form_validation->set_rules('college_code', 'College Code', 'required|trim|callback__validate_code');
        $this->form_validation->set_rules('name', 'College Name', 'required|trim|callback__validate_name');
        
        if($this->form_validation->run() == FALSE){
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }
        else{
            $data = [
                'college_code' => strtoupper($this->input->post('college_code')),
                'name' => $this->input->post('name'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->CollegesModel->create($data);
            $rows = $this->db->affected_rows();
            
            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Added a new College: " . $this->input->post('college_code'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'College successfully added!']);
            } else {
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Failed to add college!']);
            }
        }
    }

    public function editCollegeView($id){
        $this->checkPermission();
        $data['college'] = $this->CollegesModel->get($id);
        $this->load->view('modals/edit_college', $data);
    }

    public function editCollege($id){
        $this->form_validation->set_rules('college_code', 'College Code', 'required|trim|callback__validate_code');
        $this->form_validation->set_rules('name', 'College Name', 'required|trim|callback__validate_name');

        if($this->form_validation->run() == FALSE){
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }
        else{
            $data = [
                'college_code' => strtoupper($this->input->post('college_code')),
                'name' => $this->input->post('name'),
                'updated_at' => date('Y-m-d H:i:s'),

            ];

            $this->CollegesModel->update($id, $data);
            $rows = $this->db->affected_rows();

            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Updated College information with ID: " . $id . " and Name: " . $this->input->post('name'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'College successfully updated!']);
            } else {
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Failed to update college!']);
            }
        }
    }

    public function verifyDeleteCollege($id){
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);
    
        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);
    
        $response = $isPasswordCorrect;
        if($response === true){
            $this->deleteCollege($id);
        }
        
        else {
            echo json_encode([
                'status' => 500, 
                'message' => 'Invalid password']);
        }
    }

    public function deleteCollege($id){
        $this->CollegesModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'], 
                "Deleted College with ID: " . $id, 
                $this->session->userdata('admin')['username'], 
            );
            
            echo json_encode([
                'status' => 200, 
                'message' => 'College successfully deleted!']);
        } else {
            echo json_encode([
                'status' => 200, 
                'message' => 'Failed to delete college!']);
        }
    }

    public function departments() {
        $this->checkPermission();
        $data['title'] = 'Departments';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'departments.js';

        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/configurations/departments');
        $this->load->view('templates/dashboard_footer', $data);
    }

    public function departmentList() {
        $this->db->select('departments.*, colleges.name as college_name');
        $this->db->from('departments');
        $this->db->join('colleges', 'departments.college_id = colleges.id', 'left');
        $data = $this->db->get()->result_array();
        
        $response = [
            'status' => 200,
            'data' => $data,
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($response['status'])
            ->set_output(json_encode($response));
    }

    public function addDepartmentView(){
        $this->checkPermission();
        $data['colleges'] = $this->CollegesModel->get_all();
        $this->load->view('modals/add_department', $data);
    }

    public function addDepartment(){
        $this->form_validation->set_rules('college_id', 'College', 'required|trim');
        $this->form_validation->set_rules('code', 'Department Code', 'required|trim|is_unique[departments.code]');
        $this->form_validation->set_rules('name', 'Department Name', 'required|trim|is_unique[departments.name]');
        
        if($this->form_validation->run() == FALSE){
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }
        else{
            $collegeName = $this->CollegesModel->getCollegeName($this->input->post('college_id'));
            $data = [
                'college_id' => $this->input->post('college_id'),
                'college_name' => $collegeName,
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->DepartmentsModel->create($data);
            $rows = $this->db->affected_rows();
            
            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Added a new Department: " . $this->input->post('name'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Department successfully added!']);
            } else {
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Failed to add department!']);
            }
        }
    }

    public function editDepartmentView($id){
        $this->checkPermission();
        $data['colleges'] = $this->CollegesModel->get_all();
        $data['department'] = $this->DepartmentsModel->get($id);
        $this->load->view('modals/edit_department', $data);
    }

    public function editDepartment($id){
        $this->form_validation->set_rules('college_id', 'College', 'required|trim');
        $this->form_validation->set_rules('code', 'Department Code', 'required|trim|callback__validate_code');
        $this->form_validation->set_rules('name', 'Department Name', 'required|trim|callback__validate_name');

        if($this->form_validation->run() == FALSE){
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }
        else{
            $collegeName = $this->CollegesModel->getCollegeName($this->input->post('college_id'));

            $data = [
                'college_id' => $this->input->post('college_id'),
                'college_name' => $collegeName,
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $this->DepartmentsModel->update($id, $data);
            $rows = $this->db->affected_rows();

            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Updated Department information with ID: " . $id . " and Name: " . $this->input->post('name'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Department successfully updated!']);
            } else {
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Failed to update department!']);
            }
        }
    }

    public function verifyDeleteDepartment($id){
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);
    
        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);
    
        $response = $isPasswordCorrect;
        if($response === true){
            $this->deleteDepartment($id);
        }
        
        else {
            echo json_encode([
                'status' => 500, 
                'message' => 'Invalid password']);
        }
    }

    public function deleteDepartment($id){
        $this->DepartmentsModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'], 
                "Deleted Department with ID: " . $id, 
                $this->session->userdata('admin')['username'], 
            );
            
            echo json_encode([
                'status' => 200, 
                'message' => 'Department successfully deleted!']);
        } else {
            echo json_encode([
                'status' => 200, 
                'message' => 'Failed to delete department!']);
        }
    }    

    public function programs() {
        $this->checkPermission();
        $data['title'] = 'Programs';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'programs.js';
        $data['colleges'] = $this->CollegesModel->get_all();
        $data['department'] = $this->DepartmentsModel->get_all();

        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/configurations/programs', $data);
        $this->load->view('templates/dashboard_footer', $data);
    }

    public function programList() {
        $this->db->select('programs.*, departments.name as department_name, colleges.name as college_name');
        $this->db->from('programs');
        $this->db->join('departments', 'programs.department_id = departments.id', 'left');
        $this->db->join('colleges', 'departments.college_id = colleges.id', 'left');
        $data = $this->db->get()->result_array();
        
        $response = [
            'status' => 200,
            'data' => $data,
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($response['status'])
            ->set_output(json_encode($response));
    }

    public function getDepartmentsByCollege($college_id) {
        $departments = $this->DepartmentsModel->getByCollegeId($college_id);
    
        // Return the departments as JSON
        echo json_encode($departments);
    }

    public function addProgramView(){
        $this->checkPermission();
        $data['colleges'] = $this->CollegesModel->get_all();
        $this->load->view('modals/add_program', $data);
    }

    public function addProgram(){
        $this->form_validation->set_rules('code', 'Program Code', 'required|trim|callback__validate_code');
        $this->form_validation->set_rules('name', 'Program Name', 'required|trim|callback__validate_name');
        $this->form_validation->set_rules('department_id', 'Department', 'required|trim');
        
        if($this->form_validation->run() == FALSE){
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }
        else{
            $departmentName = $this->DepartmentsModel->getDepartmentName($this->input->post('department_id'));
            $data = [
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'department_id' => $this->input->post('department_id'),
                'department_name' => $departmentName,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->ProgramsModel->create($data);
            $rows = $this->db->affected_rows();
            
            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Added a new Program: " . $this->input->post('name'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Program successfully added!']);
            } else {
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Failed to add program!']);
            }
        }
    }

    public function editProgramView($id) {
        $this->checkPermission();
        $data['colleges'] = $this->CollegesModel->get_all();
        $data['departments'] = $this->DepartmentsModel->get_all();
        $data['program'] = $this->ProgramsModel->get($id);
    
        $this->load->view('modals/edit_program', $data);
    }
    
    public function editProgram($id){
        $this->form_validation->set_rules('code', 'Program Code', 'required|trim|callback_check_unique_program_code[' . $id . ']');
        $this->form_validation->set_rules('name', 'Program Name', 'required|trim|callback_check_unique_program_name[' . $id . ']');
        $this->form_validation->set_rules('department_id', 'Department', 'required|trim');
        $this->form_validation->set_rules('college_id', 'College', 'required|trim');

        if($this->form_validation->run() == FALSE){
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }
        else{
            $departmentName = $this->DepartmentsModel->getDepartmentName($this->input->post('department_id'));
            $data = [
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'department_id' => $this->input->post('department_id'),
                'department_name' => $departmentName,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->ProgramsModel->update($id, $data);
            $rows = $this->db->affected_rows();

            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Updated Program information with ID: " . $id . " and Name: " . $this->input->post('name'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Program successfully updated!']);
            } else {
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Failed to update program!']);
            }
        }
    }

    public function verifyDeleteProgram($id){
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);
    
        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);
    
        $response = $isPasswordCorrect;
        if($response === true){
            $this->deleteProgram($id);
        }
        
        else {
            echo json_encode([
                'status' => 500, 
                'message' => 'Invalid password']);
        }
    }

    public function deleteProgram($id){
        $this->DepartmentsModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'], 
                "Deleted Program with ID: " . $id, 
                $this->session->userdata('admin')['username'], 
            );
            
            echo json_encode([
                'status' => 200, 
                'message' => 'Program successfully deleted!']);
        } else {
            echo json_encode([
                'status' => 200, 
                'message' => 'Failed to delete program!']);
        }
    }        

    public function offices() {
        $this->checkPermission();
        $data['title'] = 'Offices';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'offices.js';
        $data['colleges'] = $this->CollegesModel->get_all();

        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/configurations/offices');
        $this->load->view('templates/dashboard_footer', $data);
    }

    public function officeList() {
        $this->db->select('*');
        $this->db->from('offices');
        $data = $this->db->get()->result_array();
        
        $response = [
            'status' => 200,
            'data' => $data,
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($response['status'])
            ->set_output(json_encode($response));
    }

    public function addOfficeView(){
        $this->checkPermission();
        $this->load->view('modals/add_office');
    }

    public function addOffice(){
        $this->form_validation->set_rules('name', 'Office Name', 'required|trim|callback__validate_name');
        
        if($this->form_validation->run() == FALSE){
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }
        else{
            $data = [
                'name' => $this->input->post('name'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->OfficesModel->create($data);
            $rows = $this->db->affected_rows();
            
            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Added a new Office: " . $this->input->post('name'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Office successfully added!']);
            } else {
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Failed to add office!']);
            }
        }
    }

    public function editOfficeView($id) {
        $this->checkPermission();
        $data['office'] = $this->OfficesModel->get($id);
    
        $this->load->view('modals/edit_office', $data);
    }
    
    public function editOffice($id){
        $this->form_validation->set_rules('name', 'Program Name', 'required|trim|callback__validate_name');

        if($this->form_validation->run() == FALSE){
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        }
        else{
            $data = [
                'name' => $this->input->post('name'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->OfficesModel->update($id, $data);
            $rows = $this->db->affected_rows();

            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Updated Office information with ID: " . $id . " and Name: " . $this->input->post('name'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Office successfully updated!']);
            } else {
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Failed to update program!']);
            }
        }
    }

    public function verifyDeleteOffice($id){
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);
    
        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);
    
        $response = $isPasswordCorrect;
        if($response === true){
            $this->deleteOffice($id);
        }
        
        else {
            echo json_encode([
                'status' => 500, 
                'message' => 'Invalid password']);
        }
    }

    public function deleteOffice($id){
        $this->OfficesModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'], 
                "Deleted Office with ID: " . $id, 
                $this->session->userdata('admin')['username'], 
            );
            
            echo json_encode([
                'status' => 200, 
                'message' => 'Office successfully deleted!']);
        } else {
            echo json_encode([
                'status' => 200, 
                'message' => 'Failed to delete office!']);
        }
    }     

    /* CUSTOM FORM VALIDATION CALLBACKS */
    public function check_unique_device_name($name, $id) {
        $device = $this->DevicesModel->get($id);
        if ($device->name === $name) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_unique_device_name', 'The {field} field must contain a unique value.');
            return $this->DevicesModel->is_unique_device_name($name, $id);        
        }
    }
    public function check_unique_device_id($device_id, $id) {
        $device = $this->DevicesModel->get($id);
        if ($device->device_id === $device_id) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_unique_device_id', 'The {field} field must contain a unique value.');
            return $this->DevicesModel->is_unique_device_id($device_id, $id);        
        }
    }

    public function check_unique_program_code($code, $id) {
        $program = $this->ProgramsModel->get($id);
        if ($program->code === $code) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_unique_program_code', 'The {field} field must contain a unique value.');
            return $this->ProgramsModel->is_unique_program_code($code, $id);    
        }
    }

    public function check_unique_program_name($name, $id) {
        $program = $this->ProgramsModel->get($id);
        if ($program->name === $name) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_unique_program_name', 'The {field} field must contain a unique value.');
            return $this->ProgramsModel->is_unique_program_name($name, $id);    
        }
    }

    public function _validate_code($code)
    {        
        $user_id = $this->input->post('id');
        // Check if RFID is unique in the students table
        if ($this->DepartmentsModel->is_code_exists('departments', $code, $user_id))
        {
            $this->form_validation->set_message('_validate_code', 'The Department Code is already registered.');
            return false;
        }

        if ($this->ProgramsModel->is_code_exists('programs', $code, $user_id))
        {
            $this->form_validation->set_message('_validate_code', 'The Program Code is already registered.');
            return false;
        }

        if ($this->CollegesModel->is_code_exists('colleges', $code, $user_id))
        {
            $this->form_validation->set_message('_validate_code', 'The College Code is already registered.');
            return false;
        }
        
        // RFID is unique across all tables
        return true;
    }

    public function _validate_name($code)
    {        
        $user_id = $this->input->post('id');
        // Check if RFID is unique in the students table
        if ($this->DepartmentsModel->is_code_exists('departments', $code, $user_id))
        {
            $this->form_validation->set_message('_validate_name', 'The Department Name is already registered.');
            return false;
        }

        if ($this->ProgramsModel->is_code_exists('programs', $code, $user_id))
        {
            $this->form_validation->set_message('_validate_name', 'The Program Name is already registered.');
            return false;
        }

        if ($this->CollegesModel->is_code_exists('colleges', $code, $user_id))
        {
            $this->form_validation->set_message('_validate_name', 'The College Name is already registered.');
            return false;
        }

        if ($this->OfficesModel->is_name_exists('offices', $code, $user_id))
        {
            $this->form_validation->set_message('_validate_name', 'The Office Name is already registered.');
            return false;
        }
        
        // RFID is unique across all tables
        return true;
    }
}

