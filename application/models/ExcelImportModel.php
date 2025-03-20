<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ExcelImportModel extends CI_Model
{
	public function __construct()
	{
        parent::__construct();
        $this->load->database();
	}
	
    public function importData($data) {
        $this->db->db_debug = FALSE; // Disable default error handling
        $res = $this->db->insert_batch('students', $data);
        if (!$res) {
            $error = $this->db->error(); // Get the last error
            log_message('error', 'Database error: ' . $error['message']);
            return FALSE;
        }
        return TRUE;
    }

	public function importFacultyData($data) {
        $this->db->db_debug = FALSE; // Disable default error handling
        $res = $this->db->insert_batch('faculty', $data);
        if (!$res) {
            $error = $this->db->error(); // Get the last error
            log_message('error', 'Database error: ' . $error['message']);
            return FALSE;
        }
        return TRUE;
    }

	public function importStaffData($data) {
        $this->db->db_debug = FALSE; // Disable default error handling
        $res = $this->db->insert_batch('staff', $data);
        if (!$res) {
            $error = $this->db->error(); // Get the last error
            log_message('error', 'Database error: ' . $error['message']);
            return FALSE;
        }
        return TRUE;
    }

	public function importResidentData($data) {
        $this->db->db_debug = FALSE; // Disable default error handling
        $res = $this->db->insert_batch('residents', $data);
        if (!$res) {
            $error = $this->db->error(); // Get the last error
            log_message('error', 'Database error: ' . $error['message']);
            return FALSE;
        }
        return TRUE;
    }

	public function importGuestData($data) {
        $this->db->db_debug = FALSE; // Disable default error handling
        $res = $this->db->insert_batch('guests', $data);
        if (!$res) {
            $error = $this->db->error(); // Get the last error
            log_message('error', 'Database error: ' . $error['message']);
            return FALSE;
        }
        return TRUE;
    }

}

