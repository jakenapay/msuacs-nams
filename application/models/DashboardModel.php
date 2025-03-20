<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardModel extends CI_Model {

    public function getTotalVisitorsToday() {
        // Query to get the total visitors for today
        $this->db->where('DATE(date)', date('Y-m-d'));
        return $this->db->count_all_results('entry_logs');
    }

    public function getTotalEntries($start_date, $end_date) {
        // Query to get the total number of entries
        $this->db->select('*');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        return $this->db->count_all_results('entry_logs');
    }
    
    public function getTotalExits($start_date, $end_date) {
        // Query to get the total number of exits
        $this->db->select('*');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        return $this->db->count_all_results('exit_logs');
    }
    
    public function getPendingApprovals() {
        // Query to get the number of pending approvals
        $this->db->select('*');
        return $this->db->count_all_results('visitors_pending');
    }

    public function getVisitorTrends($start_date, $end_date) {
        $this->db->select('DATE(date) as date, COUNT(*) as count');
        $this->db->from('entry_logs');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->group_by('DATE(date)');
        $this->db->order_by('date', 'ASC');
        return $this->db->get()->result_array();
    }

    public function getExitTrends($start_date, $end_date) {
        $this->db->select('DATE(date) as date, COUNT(*) as count');
        $this->db->from('exit_logs');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->group_by('DATE(date)');
        $this->db->order_by('date', 'ASC');
        return $this->db->get()->result_array();
    }

    public function getUserTypeDistribution($start_date, $end_date) {
        $this->db->select('type, COUNT(*) as count');
        $this->db->from('entry_logs');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->group_by('type');
        return $this->db->get()->result_array();
    }

    public function getPeakHours($start_date, $end_date) {
        $this->db->select('HOUR(time) as hour, COUNT(id) as total');
        $this->db->from('entry_logs');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->group_by('HOUR(time)');
        $this->db->order_by('hour');
        return $this->db->get()->result_array();
    }

    public function getPeakHoursExit($start_date, $end_date) {
        $this->db->select('HOUR(time) as hour, COUNT(id) as total');
        $this->db->from('exit_logs');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->group_by('HOUR(time)');
        $this->db->order_by('hour');
        return $this->db->get()->result_array();
    }

    public function getRecentLogs($start_date, $end_date) {
        $entry_logs = $this->db->select('date, time, fullname, type, building, "entry" as log_type')
                                ->from('entry_logs')
                                ->where('date >=', $start_date)
                                ->where('date <=', $end_date)
                                ->get()
                                ->result_array();
    
        $exit_logs = $this->db->select('date, time, fullname, type, building, "exit" as log_type')
                                ->from('exit_logs')
                                ->where('date >=', $start_date)
                                ->where('date <=', $end_date)
                                ->get()
                                ->result_array();
    
        $logs = array_merge($entry_logs, $exit_logs);
    
        usort($logs, function($a, $b) {
            $datetimeA = strtotime($a['date'] . ' ' . $a['time']);
            $datetimeB = strtotime($b['date'] . ' ' . $b['time']);
            return $datetimeB - $datetimeA;
        });
    
        return array_slice($logs, 0, 10);
    }

    public function filterUserTypeDistribution($start_date, $end_date) {
        $this->db->select('type, COUNT(*) as count');
        $this->db->from('entry_logs');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->group_by('type');
        return $this->db->get()->result_array();
    }

    
    

}
