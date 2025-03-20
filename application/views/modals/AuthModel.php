<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AuthModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    // Update a record
    public function update($id, $data) {
        return $this->db->update('admin', $data, ['id' => $id]);
    }

    // Delete a record
    public function delete($id) {
        return $this->db->delete('admin', ['id' => $id]);
    }

    public function getAdmin($username)
    {
        $account = $this->db->get_where('admin', ['username' => $username])->row_array();
    
        $query = "SELECT  admin.id AS `id`,
                            admin.username AS `username`,
                            admin.image AS `image`
                    FROM  admin
                    WHERE  username = '$username'";
        return $this->db->query($query)->row_array();
    }

    // Get a single record by ID
    public function get_by_id($id) {
        return $this->db->get_where('admin', ['id' => $id])->row();
    }

    public function verify_admin($username, $password) {
        $this->db->where('username', $username);
        $user = $this->db->get('admin')->row();

        if ($user && password_verify($password, $user->password)) {
            return $user;
        } else {
            return false;
        }
    }

    public function verify_admin_id($id){
        $this->db->where('id', $id);
        $user = $this->db->get('admin')->row();

        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    public function has_role($admin_id, $required_role_id) {
        $this->db->select('1');
        $this->db->from('admin_roles');
        $this->db->where('admin_id', $admin_id);
        $this->db->where('role_id', $required_role_id);
        $query = $this->db->get();
        
        return $query->num_rows() > 0;
    }
    
    public function get_admin_roles($admin_id) {
        $this->db->select('role_id');
        $this->db->from('admin_roles');
        $this->db->where('admin_id', $admin_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_roles() {
        return $this->db->get('roles')->result();
    }

    public function getPassword($username)
    {
        return  $this->db->get_where('admin', ['username' => $username])->row()->password;
    }

    public function getImage($id)
    {
        return  $this->db->get_where('admin', ['id' => $id])->row()->image;
    }

    //For Security Logs
    public function insertSecurity($aid, $action, $name)
    {
        date_default_timezone_set("Asia/Manila");
        $currentTime = date("Y-m-d h:i:s A");
        $this->db->insert("logs_security", [
            "aid" => $aid,
            "action" => $action,
            "name" => $name,
            "date" => $currentTime,
        ]);
    }
}
