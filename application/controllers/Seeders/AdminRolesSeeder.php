<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminRolesSeeder extends CI_Controller {

    public function index()
    {
        // Load database
        $this->load->database();

        $data = [
            ['role_name' => 'Admin Management'],
            ['role_name' => 'Logs'],
            ['role_name' => 'User Management'],
            ['role_name' => 'Visit Management'],
            ['role_name' => 'Security'],
            ['role_name' => 'Reports'],
            ['role_name' => 'Configurations'],
        ];

        $this->db->insert_batch('roles', $data);

        echo "Generated records for roles table successful.";
    }
}