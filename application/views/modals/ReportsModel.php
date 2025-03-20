<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportsModel extends CI_Model {

    protected $table = 'entry_logs';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getTotalLogsCount() {
        return $this->db->count_all('entry_logs');
    }

    public function getFilteredLogs($dateRange, $userType, $college, $department, $program, $table, $location, $office) {
        $dates = explode(' - ', $dateRange);
        $startDate = $dates[0];
        $endDate = $dates[1];
    
        // Select fields from entry_logs and the corresponding names from the joined tables
        $this->db->select($table . '.*, 
            colleges.name as college_name, 
            departments.name as department_name, 
            programs.name as program_name
        ');
        $this->db->from($table);
    
        // Join with colleges, departments, and programs tables to get their names
        $this->db->join('colleges', 'colleges.id = ' . $table . '.college', 'left');
        $this->db->join('departments', 'departments.id = ' . $table . '.department', 'left');
        $this->db->join('programs', 'programs.id = ' . $table . '.program', 'left');
    
        // Apply filters based on the user input
        if ($userType != 'all') {
            $this->db->where($table . '.type', $userType);
        }

        if($location != 'all') {
            $this->db->where($table . '.building', $location);
        }
    
        if (!empty($college) && $college != 'all') {
            $this->db->where($table . '.college', $college);
        }
    
        if (!empty($department) && $department != 'all') {
            $this->db->where($table . '.department', $department);
        }
    
        if (!empty($program) && $program != 'all') {
            $this->db->where($table . '.program', $program);
        }

        if (!empty($office) && $office != 'all') {
            $this->db->where($table . '.department', $office);
        }
    
        // Filter by date range
        $this->db->where($table . '.date >=', $startDate);
        $this->db->where($table . '.date <=', $endDate);
    
        // Execute the query and return the results
        $query = $this->db->get();
        return $query->result_array();
    }
    
    
    public function getFilteredLogsCount($dateRange, $userType, $college, $department, $program, $table, $location, $office) {
        $dates = explode(' - ', $dateRange);
        $startDate = $dates[0];
        $endDate = $dates[1];
    
        // Select fields from entry_logs and the corresponding names from the joined tables
        $this->db->select($table . '.id');
        $this->db->from($table);
    
        // Join with colleges, departments, and programs tables to get their names
        $this->db->join('colleges', 'colleges.id = ' . $table . '.college', 'left');
        $this->db->join('departments', 'departments.id = ' . $table . '.department', 'left');
        $this->db->join('programs', 'programs.id = ' . $table . '.program', 'left');
    
        // Apply filters based on the user input
        if ($userType != 'all') {
            $this->db->where($table . '.type', $userType);
        }
    
        if($location != 'all') {
            $this->db->where($table . '.building', $location);
        }
        
        if (!empty($college)) {
            $this->db->where($table . '.college', $college);
        }
    
        if (!empty($department)) {
            $this->db->where($table . '.department', $department);
        }

        if(!empty($office)) {
            $this->db->where($table . '.department', $office);
        }
    
        if (!empty($program)) {
            $this->db->where($table . '.program', $program);
        }
    
        // Filter by date range
        $this->db->where($table . '.date >=', $startDate);
        $this->db->where($table . '.date <=', $endDate);
    
        // Return the count of filtered results
        return $this->db->count_all_results();
    }
    

}    