<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AccessModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_device_count() {
        return $this->db->count_all('devices');
    }

    public function does_attendance_exist($rfid, $date) {
        return $this->db->where(['RFID' => $rfid, 'date' => $date])
                        ->get('entry_logs')
                        ->num_rows() > 0;
    }

    public function get_user($rfid) {
        $tables = ['students', 'faculty', 'staff', 'visiting_officer', 'contractors', 'researchers', 'delivery_services', 'others', 'visitors_active'];
        foreach ($tables as $table) {
            $user = $this->db->get_where($table, ['rfid' => $rfid])->row_array();
            if ($user) {
                return ['user' => $user, 'type' => $table === 'visitor_active' ? 'visitor' : $table];
            }
        }
        return null;
    }

    public function insert_attendance($record) {
        $this->db->insert('entry_logs', $record);
        return $this->db->affected_rows() > 0;
    }

    public function is_user_banned($user, $user_type) {
        return $this->db->get_where($user_type, ['rfid' => $user['rfid'], 'status' => 'Banned'])
                        ->num_rows() > 0;
    }

    public function isDeviceValid($id, $ip){
        return $this->db->get_where('devices', ['device_id' => $id, 'ip' => $ip])
                        ->num_rows() > 0;
    }

    public function getDeviceDetails($device_id, $column) {
        $this->db->select($column);
        $this->db->from('devices');
        $this->db->where('device_id', $device_id);
        $result = $this->db->get()->row_array();
        return $result ? $result[$column] : null;
    }
    
    public function getLocationNameById($location_id) {
        $this->db->select('name');
        $this->db->from('locations');
        $this->db->where('id', $location_id);
        $result = $this->db->get()->row_array();
        return $result ? $result['name'] : null;
    }

    public function getDeviceMode(){
        return $this->db->get_where('turnstile_mode', ['id' => 1])->row()->mode;
    }
    
    public function updateLocationAndIndicator($user_table, $rfid, $type, $location_name){
        $data = [
            'indicator' => $type,
            'location' => $location_name
        ];

        $this->db->where('rfid', $rfid);
        return $this->db->update($user_table, $data);
    }

    public function getEntryByLocation($location_name){
        $this->db->select('*');
        $this->db->from('entry_logs');

        if($location_name !== 'all'){
            $this->db->where('building', $location_name);
        }

        $this->db->order_by('id', 'DESC');
        return $this->db->get()->result_array();
    }
}