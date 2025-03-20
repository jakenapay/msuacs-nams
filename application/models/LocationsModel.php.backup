<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LocationsModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all() {
        return $this->db->get('locations')->result();
    }

    public function get($id) {
        return $this->db->get_where('locations', ['id' => $id])->row();
    }

    public function getLocationName($id)
    {
        return $this->db->get_where('locations', ['id' => $id])->row()->name;
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('locations', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('locations', $data);
    }

    public function delete($id) {
        return $this->db->delete('locations', ['id' => $id]);
    }

    public function get_devices($location_id) {
        $this->db->where('id', $location_id);
        return $this->db->get('devices')->result();
    }
}