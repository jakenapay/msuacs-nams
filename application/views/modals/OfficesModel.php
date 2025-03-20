<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OfficesModel extends CI_Model {

    protected $table = 'offices';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all() {
        return $this->db->get($this->table)->result();
    }

    public function get($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
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