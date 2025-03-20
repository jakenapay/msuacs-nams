<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminSeeder extends CI_Controller {

    public function index()
    {
        // Load database
        $this->load->database();

        $adminData = [
            'username' => 'admin',
            'password' => password_hash('password', PASSWORD_BCRYPT),
            'email' => 'superadmin@example.com',
            'image' => 'default.png',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('admin', $adminData);

        echo "Generate records for admin table successful.";
    }
}