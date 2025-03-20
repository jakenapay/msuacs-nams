<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RolesSeeder extends CI_Controller {

    public function index()
    {
        // Load database
        $this->load->database();

        // Fetch the admin ID (assuming it's the first record)
        $admin = $this->db->get_where('admin', ['username' => 'admin'])->row();
        if ($admin) {
            $admin_id = $admin->id;

            // Assign roles (assuming the role IDs are predefined and Super Admin has role_id = 1)
            $roles = [1, 2, 3, 4, 5, 6, 7]; // Add more role IDs as needed

            foreach ($roles as $role_id) {
                $this->db->insert('admin_roles', [
                    'admin_id' => $admin_id,
                    'role_id' => $role_id
                ]);
            }
        }

        echo "Generate records for admin roles successful.";
    }
}