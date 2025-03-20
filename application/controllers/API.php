<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AccessModel');
        $this->load->model('CollegesModel');
        $this->load->model('DepartmentsModel');
        $this->load->model('DevicesModel');
        $this->load->model('ProgramsModel');
        $this->load->database();
    }

    public function index()
    {
        $data['title'] = 'MSUACS API';
        echo $data['title'];
    }


    //API FOR ENTRY ACCESS
    public function access()
    {
        // Validate input parameters
        $value = $this->input->get('value');
        $device_id = $this->input->get('device_id');
        $ip = $this->input->get('ip');

        if (!$value || !$device_id || !$ip) {
            return $this->sendResponse(500, 'Invalid access!', 'danger', $device_id, $ip);
        }

        // Check device_id validity
        $isDeviceValid = $this->AccessModel->isDeviceValid($device_id, $ip);
        if (!$isDeviceValid) {
            return $this->sendResponse(500, 'Invalid device IP and ID!', 'danger', $device_id, $ip);
        }

        // get current date and time
        $date = date("Y-m-d");
        $time = date("H:i:s");

        // Check user in all tables at once
        $tables = ['students', 'faculty', 'staff', 'residents', 'guests', 'visitors_active'];
        $user = null;
        $user_type = null;

        foreach ($tables as $table) {
            $user = $this->db->get_where($table, ['rfid' => $value])->row_array();
            if ($user) {
                $user_type = $table === 'visitors_active' ? 'Visitor' : str_replace('_', ' ', ucfirst($table));
                $user_table = $table;
                break;
            }
        }

        if (!$user) {
            return $this->handleUnregisteredUser($value, $date, $time, $device_id, $ip);
        }

        // Check if the turnstile is in production mode:
        $deviceMode = $this->AccessModel->getDeviceMode();
        if ($deviceMode == 1) {
            // Check if the user can enter
            $canEnter = $this->checkLastAction($value);
            if (!$canEnter) {
                return $this->sendResponse(501, 'Entry denied. You must exit before entering again.', 'warning', $device_id, $ip);
            }
        }

        // Check if user is banned
        if ($user['is_banned'] == true) {
            $device_details = $this->DevicesModel->get($device_id);
            $location_id = $device_details->location_id;
            
            if (in_array($location_id, explode(',', $user['banned_location']))) {
                // TESTING:
                // return $this->sendResponse(503, 'Access Denied! User is banned in this location' . ' ban_loc: ' . $user['banned_location'] . ' loc_id: ' . $location_id, 'danger', $device_id, $ip);
                return $this->sendResponse(503, 'Access Denied! User is banned', 'danger', $device_id, $ip);
            }
            
        }

        // Prepare response and record
        $response = $this->prepareUserResponse($user, $user_type, $device_id, $ip);
        // Get location ID from device details
        $location_id = $this->AccessModel->getDeviceDetails($device_id, 'location_id');
        // Get location name by location ID
        $location_name = $this->AccessModel->getLocationNameById($location_id);
        //Updates the location of the User and Indicate to Entry 
        $this->AccessModel->updateLocationAndIndicator($user_table, $value, 'Entry', $location_name);
        //Entry Record
        $record = $this->prepareEntryRecord($user, $user_type, $value, $time, $date, $device_id);

        // Insert entry record
        $this->db->insert('entry_logs', $record);

        return $this->sendResponse(200, $response);
    }   
    public function GetDeviceInfo()
    {
         $ip = $this->input->get('ip');
         echo $ip; 
    }
    private function sendResponse($status_code, $data, $type = null, $device_id = null, $ip = null)
    {
        $response = is_array($data) ? $data : ['text' => $data, 'type' => $type, 'device_id' => $device_id, 'ip' => $ip];
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($status_code)
            ->set_output(json_encode($response));
    }


    private function handleUnregisteredUser($value, $date, $time, $device_id, $ip)
    {
        // Get location ID from device details
        $location_id = $this->AccessModel->getDeviceDetails($device_id, 'location_id');
        // Get location name by location ID
        $location_name = $this->AccessModel->getLocationNameById($location_id);

        $record = [
            'rfid' =>  $value,
            'fullname' => 'Unregistered', 
            'id_number' => '000-000',
            'building' => $location_name,
            'type'    => 'Unknown',                     
            'date' => $date,
            'time' => $time,
            'gate' => $device_id,
        ];
        
        $this->db->insert('entry_logs', $record);
        return $this->sendResponse(502, 'User not found', 'danger', $device_id, $ip);
    }

    private function prepareUserResponse($user, $user_type, $device_id, $ip)
    {
        $response = [
            'rfid' =>  $user['rfid'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'image' => $user['image'],
            'type' => rtrim($user_type, 's'),
            'device_id' => $device_id,
            'ip' => $ip,
        ];

        return $response;
    }

    private function prepareEntryRecord($user, $user_type, $value, $time, $date, $device_id)
    {
        // Get location ID from device details
        $location_id = $this->AccessModel->getDeviceDetails($device_id, 'location_id');
        // Get location name by location ID
        $location_name = $this->AccessModel->getLocationNameById($location_id);
        return [
            'rfid' =>  $value,
            'fullname' => $user['first_name'] . ' ' . $user['last_name'], 
            'id_number' => isset($user['id_number']) ? $user['id_number'] : 'None',
            'building' => $location_name,
            'type'    => ucfirst(rtrim($user_type, 's')), // Remove 's' from plural and capitalize
            'college' => isset($user['college']) ? $user['college'] : null  ,
            'department' => isset($user['department']) ? $user['department'] : ($user_type == 'Staff' ? $user['office'] : null),
            'program' => isset($user['program']) ? $user['program'] : null,
            'time' => $time,
            'date' => $date,
            'gate' => $device_id,
        ];
    }

    private function checkLastAction($rfid, $isExit = false)
    {
        // Get the last entry
        $this->db->select('*');
        $this->db->from('entry_logs');
        $this->db->where('rfid', $rfid);
        $this->db->order_by('date DESC, time DESC');
        $this->db->limit(1);
        $lastEntry = $this->db->get()->row_array();
    
        // Get the last exit
        $this->db->select('*');
        $this->db->from('exit_logs');
        $this->db->where('RFID', $rfid);
        $this->db->order_by('date DESC, time DESC');
        $this->db->limit(1);
        $lastExit = $this->db->get()->row_array();
    
        if (!$lastEntry && !$lastExit) {
            // No previous records, allow entry but not exit
            return !$isExit;
        }
    
        if (!$lastEntry) {
            // Only exit record exists, allow entry
            return !$isExit;
        }
    
        if (!$lastExit) {
            // Only entry record exists, allow exit
            return $isExit;
        }
    
        // Both entry and exit records exist, compare their timestamps
        $entryTimestamp = strtotime($lastEntry['date'] . ' ' . $lastEntry['time']);
        $exitTimestamp = strtotime($lastExit['date'] . ' ' . $lastExit['time']);
    
        if ($entryTimestamp === $exitTimestamp) {
            // If timestamps are the same, prioritize the most recent action
            if ($isExit) {
                // If the action is an exit, and the last action was an entry
                return true;
            } else {
                // If the action is an entry, and the last action was an exit
                return false;
            }
        }
    
        if ($isExit) {
            // For exit: allow if last entry is more recent than last exit
            return $entryTimestamp > $exitTimestamp;
        } else {
            // For entry: allow if last exit is more recent than last entry
            return $exitTimestamp > $entryTimestamp;
        }
    }
    


/************************************************API FOR EXIT ACCESS***************************************************************/    

    public function exit()
    {
        // Validate input parameters
        $value = $this->input->get('value');
        $device_id = $this->input->get('device_id');
        $ip = $this->input->get('ip');

        if (!$value || !$device_id || !$ip) {
            return $this->sendResponse(500, 'Invalid exit!', 'danger', $device_id, $ip);
        }

        // Check device_id validity
        $isDeviceValid = $this->AccessModel->isDeviceValid($device_id, $ip);
        if (!$isDeviceValid) {
            return $this->sendResponse(500, 'Invalid device IP and ID!', 'danger', $device_id, $ip);
        }

        // Sget current date and time
        $date = date("Y-m-d");
        $time = date("H:i:s");

        // Check user in all tables at once
        $tables = ['students', 'faculty', 'staff', 'visitors_active', 'guests', 'residents'];
        $user = null;
        $user_type = null;

        foreach ($tables as $table) {
            $user = $this->db->get_where($table, ['rfid' => $value])->row_array();
            if ($user) {
                $user_type = $table === 'visitors_active' ? 'Visitor' : str_replace('_', ' ', ucfirst($table));
                $user_table = $table;
                break;
            }
        }

        if (!$user) {
            return $this->handleUnregisteredExit($value, $date, $time, $device_id);
        }

        // Check if the turnstile is in production mode:
        $deviceMode = $this->AccessModel->getDeviceMode();
        if ($deviceMode == 1) {
            // Check if the user can exit
            $canExit = $this->checkLastAction($value, true);
                if (!$canExit) {
                    return $this->sendResponse(501, 'Exit denied. No entry record!', 'warning', $device_id, $ip);
                }
        }
        

        // Check if user is banned
        if ($user['is_banned'] == true) {
            return $this->sendResponse(503, 'Exit Denied! User is banned', 'danger', $device_id, $ip);
        }

        // Prepare response and record
        $response = $this->prepareUserResponse($user, $user_type, $device_id, $ip);
        // Get location ID from device details
        $location_id = $this->AccessModel->getDeviceDetails($device_id, 'location_id');
        // Get location name by location ID
        $location_name = $this->AccessModel->getLocationNameById($location_id);
        //Updates the location of the User and Indicate to Entry 
        $this->AccessModel->updateLocationAndIndicator($user_table, $value, 'Exit', $location_name);
        $record = $this->prepareExitRecord($user, $user_type, $value, $time, $date, $device_id, $ip);

        // Insert exit record
        $this->db->insert('exit_logs', $record);

        return $this->sendResponse(200, $response);
    }

    private function handleUnregisteredExit($value, $date, $time, $device_id)
    {
        // Get location ID from device details
        $location_id = $this->AccessModel->getDeviceDetails($device_id, 'location_id');
        // Get location name by location ID
        $location_name = $this->AccessModel->getLocationNameById($location_id);
        // Record exit for unregistered user 
        $record = [
            'RFID' =>  $value,
            'fullname' => 'Unregistered', 
            'id_number' => '000-000',
            'building' => $location_name,
            'type'    => 'Unknown',                     
            'date' => $date,
            'time' => $time,
            'gate' => $device_id,
        ];
        
        $this->db->insert('exit_logs', $record);
        return $this->sendResponse(502, 'User not found', 'danger');
    }

    private function prepareExitRecord($user, $user_type, $value, $time, $date, $device_id)
    {
        // Get location ID from device details
        $location_id = $this->AccessModel->getDeviceDetails($device_id, 'location_id');
        // Get location name by location ID
        $location_name = $this->AccessModel->getLocationNameById($location_id);
        return [
            'RFID' =>  $value,
            'fullname' => $user['first_name'] . ' ' . $user['last_name'], 
            'id_number' => isset($user['id_number']) ? $user['id_number'] : 'None',
            'building' => $location_name,
            'type'    => ucfirst(rtrim($user_type, 's')), // Remove 's' from plural and capitalize
            'college' => isset($user['college']) ? $user['college'] : null,
            'department' => isset($user['department']) ? $user['department'] : ($user_type == 'Staff' ? $user['office'] : null),
            'program' => isset($user['program']) ? $user['program'] : null,
            'time' => $time,
            'date' => $date,
            'gate' => $device_id,
        ];
    }
}