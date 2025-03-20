<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ApiForm extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Load necessary models for database interactions
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('VisitorsPendingModel');
        $this->load->model('DepartmentsModel');
    }

    private function generateTransactionNumber()
    {
        $date = new DateTime();
        $year = $date->format('y');
        $month = $date->format('m');
        $day = $date->format('d');
        $hour = $date->format('H');
        $minute = $date->format('i');
        $second = $date->format('s');

        // Generate a random 8-digit number
        $random = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);

        $transaction_number = $year . $month . $day . $hour . $minute . $second . $random;

        // Check if this number already exists in the database
        while ($this->VisitorsPendingModel->transactionNumberExists($transaction_number)) {
            // If it exists, generate a new random part and try again
            $random = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $transaction_number = $year . $month . $day . $hour . $minute . $second . $random;
        }

        return $transaction_number;
    }

    public function receiveData()
    {
        // Allow CORS
        header('Access-Control-Allow-Origin: *'); // Allows requests from any origin
        header('Access-Control-Allow-Methods: POST, OPTIONS'); // Allow POST and OPTIONS methods
        header('Access-Control-Allow-Headers: Content-Type'); // Allow Content-Type in headers

        // Handle OPTIONS request for CORS preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Collect incoming data from POST request
        $data = [
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            // 'email' => $this->input->post('email'),
            'phone_number' => $this->input->post('phone_number'),
            // 'id_type' => $this->input->post('id_type'),
            // 'id_number' => $this->input->post('id_number'),
            'visit_purpose' => $this->input->post('visit_purpose'),
            'visit_date' => $this->input->post('visit_date'),
            'visit_time' => $this->input->post('visit_time'),
            'transaction_number' => $this->generateTransactionNumber(),
            'status' => '1',
            // 'visit_duration' => $this->input->post('visit_duration'),
            // 'contact_department' => $this->input->post('contact_department'),
            // 'contact_person' => $this->input->post('contact_person'),
            'emergency_contact_person' => $this->input->post('emergency_contact_person'),
            'emergency_contact_number' => $this->input->post('emergency_contact_number'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Check if all required data is present
        if (empty($data['first_name']) || empty($data['last_name'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            http_response_code(400); // Bad request
            return;
        }

        // Insert data into the visitors_pending table
        $inserted = $this->VisitorsPendingModel->insert($data);

        // Respond with JSON message
        if ($inserted) {
            echo json_encode(['status' => 'success', 'message' => 'Data received successfully']);
            http_response_code(200); // OK
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data insertion failed']);
            http_response_code(500); // Internal server error
        }
    }
}