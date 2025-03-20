<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ResidentsModel extends CI_Model {

    protected $table = 'residents';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Insert a new record
    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    // Get all records
    public function get_all() {
        return $this->db->get($this->table)->result();
    }

    // Get a single record by ID
    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    // Update a record
    public function update($id, $data) {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function banResident($id) {
        return $this->db->update($this->table, ['is_banned' => 1, 'status' => 4], ['id' => $id]);
    }

    public function unbanResident($id) {
        return $this->db->update($this->table, ['is_banned' => 0, 'status' => 2], ['id' => $id]);
    }

    public function getRFID($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row()->rfid;
    }

    public function getImage($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row()->image;
    }

    // Delete a record
    public function delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function is_unique_email($email, $id) {
        $this->db->where('email', $email);
        $this->db->where('id !=', $id);
        return $this->db->get($this->table)->num_rows() === 0;
    }
    
    public function is_unique_phone($phone_number, $id) {
        $this->db->where('phone_number', $phone_number);
        $this->db->where('id !=', $id);
        return $this->db->get($this->table)->num_rows() === 0;
    }

    public function is_rfid_exists($table_name, $rfid, $exclude_id = null)
    {
        $this->db->where('rfid', $rfid);
        
        if ($exclude_id !== null)
        {
            $this->db->where('id !=', $exclude_id);
        }
        
        $query = $this->db->get($table_name);
        return $query->num_rows() > 0;
    }
}