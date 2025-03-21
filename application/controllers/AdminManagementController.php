<?php

defined ('BASEPATH') or exit ('No direct scripts access allowed');

class AdminManagementController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->model('AuthModel');

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
        $required_role_id = 1; // ID of the required role
        if (!require_role($required_role_id)) {
            return; // Stop further execution if unauthorized
        }
    }

    public function index() {
        $this->checkPermission();
        $data['title'] = 'Admin Management';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'admin.js';
    
        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/admin_management/admin');
        $this->load->view('templates/dashboard_footer', $data);
    }

    public function adminList(){
        $data = $this->db->select('*')->from('admin')->get()->result_array();
        
        $response = [
            'status' => 200,
            'data' => $data,
        ];
    
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($response['status'])
            ->set_output(json_encode($response));
    }

    // public function adminList() {
    //     $this->db->select('admin.*, GROUP_CONCAT(roles.role_name) as roles');
    //     $this->db->from('admin');
    //     $this->db->join('admin_roles', 'admin.id = admin_roles.admin_id', 'left');
    //     $this->db->join('roles', 'roles.id = admin_roles.role_id', 'left');
    //     $this->db->group_by('admin.id');
    //     $data = $this->db->get()->result_array();
        
    //     $response = [
    //         'status' => 200,
    //         'data' => $data,
    //     ];
    
    //     return $this->output
    //         ->set_content_type('application/json')
    //         ->set_status_header($response['status'])
    //         ->set_output(json_encode($response));
    // }  

    function setAdminsUploadPath() {
        return 'assets/images/uploads/admin/'; // Adjust according to your structure
    }

    public function addAdminView(){
        $this->checkPermission();
        $data['roles'] = $this->AuthModel->get_roles();
        $this->load->view('modals/add_admin', $data);
    }

    public function addAdmin() {
        // Set form validation rules
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[3]|max_length[25]|is_unique[admin.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[8]|max_length[25]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|trim|matches[password]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|is_unique[admin.email]|max_length[50]');
    
        // Check if any file was uploaded
        if (empty($_FILES['image']['name']) && empty($_FILES['uploadInput']['name'])) {
            $this->form_validation->set_rules('image', 'Image', 'required');
        }
    
        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array(),
            ]);
            return;
        }
    
        // Get form data
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        $roles = $this->input->post('roles_id');
    
        // File upload configuration
        $uploadPath = setAdminsUploadPath(); // Ensure this returns correct server path
        $config['upload_path'] = $uploadPath;
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 2048;
        $config['file_name'] = $username . '_' . time(); // Add timestamp for uniqueness
    
        $this->load->library('upload', $config);
        $imagePath = '';
    
        // Determine which file input to use
        $uploadField = null;
        if (!empty($_FILES['image']['name'])) {
            $uploadField = 'image';
        } elseif (!empty($_FILES['uploadInput']['name'])) {
            $uploadField = 'uploadInput';
        }
    
        if ($uploadField) {
            if (!$this->upload->do_upload($uploadField)) {
                echo json_encode([
                    'status' => 400,
                    'message' => [$this->upload->display_errors()]
                ]);
                return;
            } else {
                $upload_data = $this->upload->data(); // Get upload data
                // Construct relative path for web access
                $imagePath = 'assets/images/uploads/admin/' . $upload_data['file_name'];
            }
        }
    
        // Prepare admin data
        $admin_data = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'image' => $imagePath,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    
        // Insert admin data
        $this->db->insert('admin', $admin_data);
        $admin_id = $this->db->insert_id();
    
        // Insert roles if any
        if (!empty($roles)) {
            $admin_roles_data = [];
            foreach ($roles as $role_id) {
                $admin_roles_data[] = [
                    'admin_id' => $admin_id,
                    'role_id' => $role_id,
                ];
            }
            $this->db->insert_batch('admin_roles', $admin_roles_data);
        }
    
        echo json_encode([
            'status' => 200,
            'message' => 'Admin added successfully',
        ]);
    }

    public function editAdminView($id){
        $this->checkPermission();
        $data['admin'] = $this->AuthModel->get_by_id($id);
        $data['roles'] = $this->AuthModel->get_roles();
        $data['admin_roles'] = $this->AuthModel->get_admin_roles($id);
        $this->load->view('modals/edit_admin', $data);
    }

    public function adminView($id){
        $data['admin'] = $this->AuthModel->get_by_id($id);
        $data['roles'] = $this->AuthModel->get_roles();
        $data['admin_roles'] = $this->AuthModel->get_admin_roles($id);
        $this->load->view('modals/view_admin', $data);
    }

    public function editAdmin($id){
        // Set form validation rules
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[3]|max_length[25]|callback_check_unique_username[' . $id . ']');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|callback_check_unique_email[' . $id . ']|max_length[50]');

        if(!empty($this->input->post('password')) || $this->input->post('password') != null){
            $this->form_validation->set_rules('password', 'Password', 'min_length[8]|max_length[25]|trim');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'matches[password]|trim');
        }
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array(),
            ]);
        } else {
            // Get form data
            $username = $this->input->post('username');
            $email = $this->input->post('email');
            $roles = $this->input->post('roles_id'); // This will be an array of role IDs
            if(!empty($this->input->post('password')) || $this->input->post('password') != null){
                $password = $this->input->post('password')? password_hash($this->input->post('password'), PASSWORD_DEFAULT) : null;
                // Prepare data for 'admin' table
                $admin_data = [
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            else{
                // Prepare data for 'admin' table
                $admin_data = [
                    'username' => $username,
                    'email' => $email,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }

            $uploadPath = setAdminsUploadPath();

            // Configure upload settings
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048; // 2MB max
            $config['file_name'] = $username; // Unique filename

            $this->load->library('upload', $config);

            if (!empty($_FILES['image']['name'])) {
                $uploadField = 'image';
            } elseif (!empty($_FILES['uploadInput']['name'])) {
                $uploadField = 'uploadInput';
            } else {
                $uploadField = null; // No file uploaded
            }

            if ($uploadField) {
                if (!$this->upload->do_upload($uploadField)) {
                    echo json_encode([
                        'status' => 400,
                        'message' => [$this->upload->display_errors()]
                    ]);
                    return;
                } else {
                    // Delete old image if exists
                    $old_image = $this->AuthModel->getImage($id);
                    if ($old_image && file_exists('./' . $old_image)) {
                        unlink('./' . $old_image);
                    }

                    // Get upload data and set new image filename
                    $upload_data = $this->upload->data();
                    $admin_data['image'] = $uploadPath . $upload_data['file_name'];
                }
            }

            
            $this->AuthModel->update($id, $admin_data);
            
            // Delete existing roles for the admin
            $this->db->delete('admin_roles', ['admin_id' => $id]);

            // Insert new roles for the admin
            if (!empty($roles)) {
                foreach ($roles as $role_id) {
                    $this->db->insert('admin_roles', [
                        'admin_id' => $id,
                        'role_id' => $role_id,
                    ]);
                }
            }

            $rows = $this->db->affected_rows();
            
            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Edited admin record with ID# " . $id, 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Admin record updated successfully!']);
            }
            else{
                echo json_encode([
                    'status' => 500, 
                    'message' => ['Failed to update admin record.']]);
            }
        }
    }

    public function verifyDeleteAdmin($id){
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);
    
        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);
    
        $response = $isPasswordCorrect;
        if($response === true){
            $this->deleteAdmin($id);
        }
        
        else {
            echo json_encode([
                'status' => 500, 
                'message' => 'Invalid password']);
        }
    }

    public function deleteAdmin($id){
        // Delete old image if exists
        $old_image = $this->AuthModel->getImage($id);
        if ($old_image && file_exists('./'. $old_image)) {
            unlink('./'. $old_image);
        }

        $this->AuthModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'], 
                "Deleted an admin with ID# " . $id, 
                $this->session->userdata('admin')['username'], 
            );
            
            echo json_encode([
                'status' => 200, 
                'message' => 'Admin successfully deleted!']);
        } else {

            echo json_encode([
                'status' => 200, 
                'message' => 'Failed to admin staff!']);
        }
    }

    // Custom callback function for username validation
    public function check_unique_username($username, $id) {
        // Check if username is unique, ignoring the current user's record
        $this->db->where('username', $username);
        if ($id) {
            $this->db->where('id !=', $id);
        }
        $query = $this->db->get('admin');

        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_unique_username', 'The {field} is already in use.');
            return false;
        }
        return true;
    }

    // Custom callback function for email validation
    public function check_unique_email($email, $id) {
        // Check if email is unique, ignoring the current user's record
        $this->db->where('email', $email);
        if ($id) {
            $this->db->where('id !=', $id);
        }
        $query = $this->db->get('admin');

        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_unique_email', 'The {field} is already in use.');
            return false;
        }
        return true;
    }
    
}