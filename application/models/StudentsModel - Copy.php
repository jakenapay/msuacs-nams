<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentsModel extends CI_Model {

    protected $table = 'students';

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

    public function banStudent($id) {
        return $this->db->update($this->table, ['is_banned' => 1], ['id' => $id]);
    }

    public function unbanStudent($id) {
        return $this->db->update($this->table, ['is_banned' => 0], ['id' => $id]);
    }

    // Delete a record
    public function delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function getRFID($id)
    {
        return $this->db->get_where('students', ['id' => $id])->row()->rfid;
    }

    public function getImage($id)
    {
        return $this->db->get_where('students', ['id' => $id])->row()->image;
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