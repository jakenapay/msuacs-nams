<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VisitorsCompletedModel extends CI_Model {

    protected $table = 'visitors_completed';

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

    public function banVisitor($id) {
        return $this->db->update($this->table, ['is_banned' => 1, 'status' => 4], ['id' => $id]);
    }

    public function unbanVisitor($id) {
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
}