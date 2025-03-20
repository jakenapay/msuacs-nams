<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DepartmentsModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all() {
        return $this->db->get('departments')->result();
    }

    public function get($id) {
        return $this->db->get_where('departments', ['id' => $id])->row();
    }

    public function getDepartmentName($id){
        $this->db->select('name');
        $this->db->from('departments');
        $this->db->where('id', $id);
        return $this->db->get()->row()->name;
    }

    public function getDepartmentCode($id){
        $this->db->select('code');
        $this->db->from('departments');
        $this->db->where('name', $id);
        return $this->db->get()->row()->code;
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('departments', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('departments', $data);
    }

    public function delete($id) {
        return $this->db->delete('departments', ['id' => $id]);
    }

    public function get_programs($department_id) {
        $this->db->where('department_id', $department_id);
        return $this->db->get('programs')->result();
    }

    public function get_college($department_id) {
        $department = $this->get($department_id);
        $this->db->where('id', $department->college_id);
        return $this->db->get('colleges')->row();
    }

    public function getByCollegeId($college_id) {
        $this->db->where('college_id', $college_id);
        $query = $this->db->get('departments');
        return $query->result();
    }

    public function getByCollegeName($college_id) {
        $this->db->where('college_name', $college_id);
        $query = $this->db->get('departments');
        return $query->result();
    }

    public function is_code_exists($table_name, $code, $exclude_id = null)
    {
        $this->db->where('code', $code);
        
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