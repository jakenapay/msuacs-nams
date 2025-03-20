<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VisitorsDeclineModel extends CI_Model {

    protected $table = 'visitors_declined';
    
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

    // Delete a record
    public function delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function getImage($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row()->image;
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
}