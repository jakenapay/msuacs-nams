<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DevicesModel extends CI_Model {

    protected $table = 'devices';
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all() {
        return $this->db->get('devices')->result();
    }

    public function get($id) {
        return $this->db->get_where('devices', ['id' => $id])->row();
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('devices', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('devices', $data);
    }

    public function delete($id) {
        return $this->db->delete('devices', ['id' => $id]);
    }

    public function get_location($device_id) {
        $this->db->where('id', $device_id);
        return $this->db->get('locations')->result();
    }

    public function is_unique_device_id($device_id, $id) {
        $this->db->where('device_id', $device_id);
        $this->db->where('id !=', $id);
        return $this->db->get($this->table)->num_rows() === 0;
    }

    public function is_unique_device_name($name, $id) {
        $this->db->where('name', $name);
        $this->db->where('id !=', $id);
        return $this->db->get($this->table)->num_rows() === 0;
    }

    public function getCurrentMode(){
        $this->db->select('mode');
        $this->db->from('turnstile_mode');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row('mode');
    }

    public function updateMode($data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', 1);
        return $this->db->update('turnstile_mode', $data);
    }
}