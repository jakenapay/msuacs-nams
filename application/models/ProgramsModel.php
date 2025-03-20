<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProgramsModel extends CI_Model {
    protected $table = 'programs';
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getAllPrograms() {
        return $this->db->get('programs')->result(); // Fetch all programs from the table
    }

    public function get_all() {
        return $this->db->get('programs')->result();
    }

    public function get($id) {
        return $this->db->get_where('programs', ['id' => $id])->row();
    }

    public function getProgramName($id){
        $program = $this->get($id);
        return $program->name;
    }

    public function getProgramCode($id){
        $this->db->select('code');
        $this->db->from($this->table);
        $this->db->where('name', $id);
        return $this->db->get()->row()->code;
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('programs', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('programs', $data);
    }

    public function delete($id) {
        return $this->db->delete('programs', ['id' => $id]);
    }

    public function get_department($program_id) {
        $program = $this->get($program_id);
        $this->db->where('id', $program->department_id);
        return $this->db->get('departments')->row();
    }

    public function get_college($program_id) {
        $program = $this->get($program_id);
        $department = $this->get_department($program_id);
        $this->db->where('id', $department->college_id);
        return $this->db->get('colleges')->row();
    }

    public function getByDepartmentId($department_id) {
        $this->db->where('department_name', $department_id);
        $query = $this->db->get('programs');
        return $query->result();
    }

    public function is_unique_program_code($code, $id) {
        $this->db->where('code', $code);
        $this->db->where('id !=', $id);
        return $this->db->get($this->table)->num_rows() === 0;
    }

    public function is_unique_program_name($name, $id) {
        $this->db->where('name', $name);
        $this->db->where('id !=', $id);
        return $this->db->get($this->table)->num_rows() === 0;
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