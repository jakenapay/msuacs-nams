<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CollegesModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all() {
        return $this->db->get('colleges')->result();
    }

    public function get($id) {
        return $this->db->get_where('colleges', ['id' => $id])->row();
    }

    public function getCollegeName($id){
        $this->db->select('name');
        $this->db->from('colleges');
        $this->db->where('id', $id);
        return $this->db->get()->row()->name;
    }


    public function getCollegeCode($id){
        return $this->db->get_where('colleges', ['name' => $id])->row()->college_code;
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('colleges', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('colleges', $data);
    }

    public function delete($id) {
        return $this->db->delete('colleges', ['id' => $id]);
    }

    public function get_departments($college_id) {
        $this->db->where('college_id', $college_id);
        return $this->db->get('departments')->result();
    }

    public function is_code_exists($table_name, $code, $exclude_id = null)
    {
        $this->db->where('college_code', $code);
        
        if ($exclude_id !== null)
        {
            $this->db->where('id !=', $exclude_id);
        }
        
        $query = $this->db->get($table_name);
        return $query->num_rows() > 0;
    }

    public function is_name_exists($table_name, $name, $exclude_id = null)
    {
        $this->db->where('name', $name);
        
        if ($exclude_id !== null)
        {
            $this->db->where('id !=', $exclude_id);
        }
        
        $query = $this->db->get($table_name);
        return $query->num_rows() > 0;
    }
}