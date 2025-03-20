<?php
defined('BASEPATH') or exit('No direct script access allowed');

function test(){
    return 'API.php';
}
require test();

class AuthController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AuthModel');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    public function index() {
        //IF THERE IS ALREADY SESSION AND ADMIN ID IS EXISTING, REDIRECT TO DASHBOARD
        if ($this->session->userdata('admin') !== null) {
            $admin_id = $this->session->userdata('admin')['id'];
            $doesAdminExist = $this->AuthModel->verify_admin_id($admin_id);

            if ($doesAdminExist) {
                return redirect('admin/dashboard');
            } else {
                $this->session->sess_destroy();
                return redirect('admin');
            }
        }

        $this->load->view('templates/auth_header');
        $this->load->view('auth/login');
        $this->load->view('templates/auth_footer');
    }

    public function login() {
        //USER NAME AND PASSWORD VALIDATION
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        //IF VALIDATION FAILS, RETURN JSON RESPONSE
        if ($this->form_validation->run() == FALSE) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 400, 
                    'message' => "Error"
                )));
        }
        
        $username = trim($this->input->post('username'));
        $password = trim($this->input->post('password'));

        // VERIFY IF ADMIN CREDENTIALS ARE CORRECT
        $user = $this->AuthModel->verify_admin($username, $password);

        if ($user) {
            // Retrieve roles from the admin_roles table
            $roles = $this->AuthModel->get_admin_roles($user->id);

            // Convert the roles to an array of role_ids
            $role_ids = array_map(function($role) {
                return $role->role_id;
            }, $roles);

            // Prepare session data
            $admin = [
                'id' => $user->id,
                'username' => $user->username,
                'role_ids' => $role_ids, // Store the array of role_ids in session
            ];

            $this->session->set_userdata('admin', $admin);

            $this->AuthModel->insertSecurity(
                $user->id, 
                "Logged in to system", 
                $user->username
            );

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 200, 
                    'message' => 'Login successful'
                )));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 400, 
                    'message' => 'Invalid credentials'
                )));
        }
    }

    public function logout()
    {
        $adminUsername = $this->session->userdata('admin')['username'];
        $admin = $this->AuthModel->getAdmin($adminUsername);
        $this->AuthModel->insertSecurity(
            $admin['id'], 
            "Logged out of system", 
            $this->session->userdata('admin')['username']
        );
        $this->session->unset_userdata('admin');
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Logged Out!</div>');
        redirect('admin/login');
    }

    public function blocked(){
        $data['title'] = 'Blocked';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'admin.js';
    
        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('errors/blocked');
        $this->load->view('templates/dashboard_footer', $data);
    }
}
