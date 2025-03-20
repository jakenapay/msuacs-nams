<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VisitorsPendingModel extends CI_Model {

    protected $table = 'visitors_pending';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Insert a new record
    public function insert($data) {
        print_r($data);
        return $this->db->insert($this->table, $data);
    }

    // Get all records
    public function get_all() {
        return $this->db->get($this->table)->result();
    }

    // Get a single record by ID
    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    // Get a single record by ID
    public function get_by_transaction_number($transactionNumber) {
        return $this->db->get_where($this->table, ['transaction_number' => $transactionNumber])->row();
    }

    // Update a record
    public function update($id, $data) {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    // Delete a record
    public function delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    // Delete a record using transaction number
    public function delete_by_transaction_number($transaction_number) {
        return $this->db->delete($this->table, ['transaction_number' => $transaction_number]);
    }

    public function is_unique_email($email, $id) {
        $this->db->where('email', $email);
        $this->db->where('id !=', $id);
        return $this->db->get($this->table)->num_rows() === 0;
    }
    
    public function is_unique_phone($phone_number, $id) {
        $this->db->where('phone_number', $phone_number);
        $this->db->where('id !=', $id);
        return $this->db->get($this->table)->num_rows() === 0;
    }

    public function is_unique_email_pending($email, $id = null) {
        $this->db->where('email', $email);
        if ($id) {
            $this->db->where('id !=', $id); // Exclude the record with the same id if updating
        }
        return $this->db->get($this->table)->num_rows() === 0;
    }
    
    public function is_unique_phone_pending($phone_number, $id = null) {
        $this->db->where('phone_number', $phone_number);
        if ($id) {
            $this->db->where('id !=', $id); // Exclude the record with the same id if updating
        }
        return $this->db->get($this->table)->num_rows() === 0;
    }
    

    //Check if there is an existeing transaction number
    public function transactionNumberExists($transaction_number) {
        $this->db->where('transaction_number', $transaction_number);
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    public function get_contact_persons_by_department($department) {
        // First query for faculty
        $faculty_query = $this->db->select('first_name, last_name')
                                    ->from('faculty')
                                    ->where('department', $department)
                                    ->get_compiled_select();
    
        // Second query for staff, using UNION
        $staff_query = $this->db->select('first_name, last_name')
                                ->from('staff')
                                ->where('office', $department)
                                ->get_compiled_select();
    
        // Combine both queries with UNION
        $union_query = "($faculty_query) UNION ($staff_query)";
    
        // Execute the union query
        $query = $this->db->query($union_query);
    
        return $query->result();
    }
    
    
}