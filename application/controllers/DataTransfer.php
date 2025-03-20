<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DataTransfer extends CI_Controller {

    private $AUTH_USERNAME_KEY;
    private $AUTH_SECRET_KEY;
    private $GET_TOKEN;
    private $GET_ALL_STUDENTS;
    private $GET_ALL_FACULTY;
    private $GET_ALL_STAFF;
    private $GET_ALL_OFFICE;
    private $GET_ALL_PROGRAMS;
    private $GET_ALL_DEPARTMENTS;
    private $GET_ALL_COLLEGE;


    public function __construct() {
        parent::__construct();
        // Load necessary models and helpers
        $this->load->helper('form');
        $this->load->helper('env_helper');
        $this->GET_TOKEN = env('GET_TOKEN');
        $this->AUTH_USERNAME_KEY = env('AUTH_USERNAME_KEY');
        $this->AUTH_SECRET_KEY = env('AUTH_SECRET_KEY');
        $this->GET_ALL_STUDENTS = env('GET_ALL_STUDENTS');
        $this->GET_ALL_FACULTY = env('GET_ALL_FACULTY');
        $this->GET_ALL_STAFF = env('GET_ALL_STAFF');
        $this->GET_ALL_OFFICE = env('GET_ALL_OFFICE');
        $this->GET_ALL_PROGRAMS = env('GET_ALL_PROGRAMS');
        $this->GET_ALL_DEPARTMENTS = env('GET_ALL_DEPARTMENTS');
        $this->GET_ALL_COLLEGE = env('GET_ALL_COLLEGE');
        $this->load->library(['form_validation', 'session']);
        $this->load->model('StudentsModel');
        $this->load->database();
    }
    private function handleImageByUserType($imageData, $userType, $college = null, $department = null, $program = null) {
        // Define base paths
        $pathMap = [
            'staff' => 'assets/images/uploads/staff/',
            'faculty' => 'assets/images/uploads/faculty/',
            'students' => 'assets/images/uploads/students/',
            'default' => 'assets/images/uploads/'
        ];
        
        if ($userType === 'students' && $college) {
            // Construct student-specific path
            $college = 'TEST';
            $department = 'TEST';
            $program = 'TEST';
            $studentPath = "assets/images/uploads/students/" . 
                ($college ? $this->sanitizeDirectoryName($college) . '/' : '') .
                ($department ? $this->sanitizeDirectoryName($department) . '/' : '') .
                ($program ? $this->sanitizeDirectoryName($program) . '/' : '');
        } else {
            $studentPath = $pathMap[$userType] ?? $pathMap['default'];
        }
        
        $uploadPath = FCPATH . $studentPath;
    
        // Create directory if needed
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
    
        // Generate filename
        $filename = time() . '.jpg';
        
        try {
            // Handle base64
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageData = base64_decode($imageData);
                file_put_contents($uploadPath . $filename, $imageData);
                return $studentPath . $filename;
            }
            
            // Handle URL/file
            if (filter_var($imageData, FILTER_VALIDATE_URL) || file_exists($imageData)) {
                $content = file_get_contents($imageData);
                file_put_contents($uploadPath . $filename, $content);
                return $studentPath . $filename;
            }
        } catch (Exception $e) {
            log_message('error', 'Image upload failed: ' . $e->getMessage());
            return "default.jpg";
        }
        
        return "default.jpg";
    }
    
    private function sanitizeDirectoryName($name) {
        return preg_replace('/[^a-zA-Z0-9_-]/', '_', strtoupper(trim($name)));
    }
    

    // Requesting token to the SAIS API
    public function requestToken() {
        
        // Prepare the request payload
        $payload = json_encode([
            'username' => $this->AUTH_USERNAME_KEY,
            'secret' => $this->AUTH_SECRET_KEY
        ]);
    
        // Initialize cURL session
        $ch = curl_init();
    
        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->GET_TOKEN,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Accept: application/json"
            ]
        ]);
    
        // Execute cURL request
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        // Close cURL session
        curl_close($ch);
    
        // Handle cURL errors
        if ($err) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'code' => 500,
                    'message' => 'Failed to request token: ' . $err,
                    'data' => null
                ]));
        }
    
        // Decode JSON response
        $result = json_decode($response, true);
        
        // Check if JSON is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'code' => 500,
                    'message' => 'Invalid JSON response from server',
                    'data' => null
                ]));
        }
    
        // Handle different response codes
        if ($httpCode !== 200) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header($httpCode)
                ->set_output(json_encode([
                    'code' => $httpCode,
                    'message' => isset($result['message']) ? $result['message'] : 'Server error occurred',
                    'data' => null
                ]));
        }
    
        // Check if token exists in response
        if (!isset($result['data']['token'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'code' => 500,
                    'message' => 'Token not found in response',
                    'data' => null
                ]));
        }
    
        // Success response
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'code' => 200,
                'message' => isset($result['message']) ? $result['message'] : 'Login Successful',
                'data' => [
                    'token' => $result['data']['token']
                ]
            ]));
    }

    // =======================
    // Function to process and insert data into the database
    private function processAllStudents($dataRows, $param) {
        $inserted = 0;
        $failed = 0;

        foreach ($dataRows as $row) {
            // Validate and insert college if it doesn't exist
            if (isset($row['college']) && !empty($row['college'])) {
                $college_exists = $this->db
                    ->where('name', $row['college'])
                    ->get('colleges')
                    ->num_rows();

                if (!$college_exists) {
                    $college_data = [
                        'name' => $row['college'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('colleges', $college_data);
                }
            }

            // Validate and insert department if it doesn't exist
            if (isset($row['department']) && !empty($row['department'])) {
                $department_exists = $this->db
                    ->where('name', $row['department'])
                    ->get('departments')
                    ->num_rows();

                if (!$department_exists) {
                    $department_data = [
                        'code' => $row['department'],
                        'college_id' => $this->db
                            ->select('id')
                            ->where('name', $row['college'])
                            ->get('colleges')
                            ->row()
                            ->id,
                        'name' => $row['department'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('departments', $department_data);
                }
            }

            // Format data for insertion
            $formattedData = [
                'first_name' => isset($row['first_name']) ? $row['first_name'] : null,
                'last_name' => isset($row['last_name']) ? $row['last_name'] : null,
                'middle_name' => isset($row['middle_name']) ? $row['middle_name'] : null,
                'image' => isset($row['image']) ? $this->handleImageByUserType($row['image'], $param,$row['college'], 'TEST', "TEST") : "assets/images/default.jpg",
                'id_number' => isset($row['id_number']) ? $row['id_number'] : null,
                'rfid' => isset($row['rfid']) ? $row['rfid'] : null,
                'enrollment_status' => isset($row['enrollment_status']) ? $row['enrollment_status'] : 0,
                'resident_status' => isset($row['resident_status']) ? $row['resident_status'] : 0,
                'emergency_contact_person' => isset($row['emergency_contact_person']) ? $row['emergency_contact_person'] : "test",
                'emergency_contact_number' => isset($row['emergency_contact_number']) ? $row['emergency_contact_number'] : "9999999999",
                'college' => isset($row['college']) ? $row['college'] : null,
                'department' => isset($row['department']) ? $row['department'] : "test",
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Check if record exists
            $exists = $this->db
                ->where('id_number', $row['id_number'])
                ->get($param)
                ->num_rows();

            if ($exists) {
                // Update
                $result = $this->db
                    ->where('id_number', $row['id_number'])
                    ->update($param, $formattedData);
            } else {
                // Insert
                $formattedData['created_at'] = date('Y-m-d H:i:s');
                $result = $this->db->insert($param, $formattedData);
            }

            $result ? $inserted++ : $failed++;
        }

        return [
            'inserted' => $inserted,
            'failed' => $failed
        ];
    }

    // Function to process and insert faculty data into the database
    private function processAllFaculty($dataRows, $param) {
        $inserted = 0;
        $failed = 0;

        foreach ($dataRows as $row) {
             // Validate and insert college if it doesn't exist
             if (isset($row['college']) && !empty($row['college'])) {
                $college_exists = $this->db
                    ->where('name', $row['college'])
                    ->get('colleges')
                    ->num_rows();

                if (!$college_exists) {
                    $college_data = [
                        'name' => $row['college'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('colleges', $college_data);
                }
            }

            // Validate and insert department if it doesn't exist
            if (isset($row['department']) && !empty($row['department'])) {
                $department_exists = $this->db
                    ->where('name', $row['department'])
                    ->get('departments')
                    ->num_rows();

                if (!$department_exists) {
                    $department_data = [
                        'code' => $row['department'],
                        'college_id' => $this->db
                            ->select('id')
                            ->where('name', $row['college'])
                            ->get('colleges')
                            ->row()
                            ->id,
                        'name' => $row['department'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('departments', $department_data);
                }
            }

            // Format data for insertion
            $formattedData = [
                'first_name' => isset($row['first_name']) ? $row['first_name'] : "test",
                'middle_name' => isset($row['middle_name']) ? $row['middle_name'] : "test",
                'last_name' => isset($row['last_name']) ? $row['last_name'] : "test",
                'id_number' => isset($row['id_number']) ? $row['id_number'] : random_int(100000, 999999),
                'position' => isset($row['position']) ? $row['position'] : "test",
                'college' => isset($row['college']) ? $row['college'] : null,
                'department' => isset($row['department']) ? $row['department'] : null,
                'employment_status' => isset($row['employment_status']) ? $row['employment_status'] : "test",
                'rfid' => isset($row['rfid']) ? $row['rfid'] : null,
                'resident_status' => isset($row['resident_status']) ? $row['resident_status'] : 0,
                'assigned_dormitory' => isset($row['assigned_dormitory']) ? $row['assigned_dormitory'] : null,
                'image' => isset($row['image']) ? $this->handleImageByUserType($row['image'], $param) : "default.jpg",
                'emergency_contact_person' => isset($row['emergency_contact_person']) ? $row['emergency_contact_person'] : "test",
                'emergency_contact_number' => isset($row['emergency_contact_number']) ? $row['emergency_contact_number'] : "9999999999",
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Check if record exists
            $exists = $this->db
                ->where('id_number', $row['id_number'])
                ->get($param)
                ->num_rows();

            if ($exists) {
                // Update
                $result = $this->db
                    ->where('id_number', $row['id_number'])
                    ->update($param, $formattedData);
            } else {
                // Insert
                $formattedData['created_at'] = date('Y-m-d H:i:s');
                $result = $this->db->insert($param, $formattedData);
            }

            $result ? $inserted++ : $failed++;
        }

        return [
            'inserted' => $inserted,
            'failed' => $failed
        ];
    }

    private function processAllStaff($dataRows, $param) {
        $inserted = 0;
        $failed = 0;

        foreach ($dataRows as $row) {
             // Validate and insert college if it doesn't exist
             if (isset($row['office']) && !empty($row['office'])) {
                $office_exists = $this->db
                    ->where('name', $row['office'])
                    ->get('offices')
                    ->num_rows();

                if (!$office_exists) {
                    $office_data = [
                        'name' => $row['office'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('offices', $office_data);
                }
            }

            // Format data for insertion
            $formattedData = [
                'first_name' => isset($row['first_name']) ? $row['first_name'] : "test",
                'middle_name' => isset($row['middle_name']) ? $row['middle_name'] : "test",
                'last_name' => isset($row['last_name']) ? $row['last_name'] : "test",
                'id_number' => isset($row['id_number']) ? $row['id_number'] : random_int(100000, 999999),
                'position' => isset($row['position']) ? $row['position'] : "test",
                'employment_status' => isset($row['employment_status']) ? $row['employment_status'] : "test",
                'working_hours' => isset($row['working_hours']) ? $row['working_hours'] : "test",
                'office' => isset($row['office']) ? $row['office'] : "test",
                'rfid' => isset($row['rfid']) ? $row['rfid'] : null,
                'resident_status' => isset($row['resident_status']) ? $row['resident_status'] : 0,
                'assigned_dormitory' => isset($row['assigned_dormitory']) ? $row['assigned_dormitory'] : null,
                // 'image' => isset($row['image']) ? $row['image'] : "test",
                'image' => isset($row['image']) ? $this->handleImageByUserType($row['image'], $param) : "default.jpg",
                'emergency_contact_person' => isset($row['emergency_contact_person']) ? $row['emergency_contact_person'] : "test",
                'emergency_contact_number' => isset($row['emergency_contact_number']) ? $row['emergency_contact_number'] : "9999999999",
                'updated_at' => date('Y-m-d H:i:s')
            ];

            

            // Check if record exists
            $exists = $this->db
                ->where('id_number', $row['id_number'])
                ->get($param)
                ->num_rows();

            if ($exists) {
                // Update
                $result = $this->db
                    ->where('id_number', $row['id_number'])
                    ->update($param, $formattedData);
            } else {
                // Insert
                $formattedData['created_at'] = date('Y-m-d H:i:s');
                $result = $this->db->insert($param, $formattedData);
            }

            $result ? $inserted++ : $failed++;
        }

        return [
            'inserted' => $inserted,
            'failed' => $failed
        ];
    }

    // Updated main function
    public function getAllData($param) {
        // First get the token
        $tokenResponse = $this->requestToken();
        $tokenData = json_decode($tokenResponse->final_output, true);

        if (!isset($tokenData['data']['token'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode([
                    'code' => 401,
                    'message' => 'Failed to obtain token',
                    'data' => null
                ]));
        }

        $token = $tokenData['data']['token'];

        // Define valid endpoints and their corresponding config items
        $validEndpoints = [
            'students' => 'GET_ALL_STUDENTS',
            'faculty' => 'GET_ALL_FACULTY',
            'staff' => 'GET_ALL_STAFF',
            'offices' => 'GET_ALL_OFFICE',
            'programs' => 'GET_ALL_PROGRAMS',
            'departments' => 'GET_ALL_DEPARTMENTS',
            'colleges' => 'GET_ALL_COLLEGE'
        ];

        // Validate parameter and get API URL
        if (!$param || !array_key_exists($param, $validEndpoints)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'code' => 400,
                    'message' => 'Invalid endpoint parameter provided',
                    'data' => null
                ]));
        }

        $property = $validEndpoints[$param];
        if (property_exists($this, $property)) {
            $api_url = $this->$property;  // Correct access
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'code' => 500,
                    'message' => 'Invalid API endpoint',
                    'data' => null
                ]));
        }

        $api_url = $this->$property;  

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Authorization: Bearer " . $token
            ]
        ]);

        // Execute cURL request
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close cURL session
        curl_close($ch);

        if ($err) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode([
                    'code' => 500,
                    'message' => 'Failed to fetch data: ' . $err,
                    'data' => null
                ]));
        }

        // Decode and validate response
        $data = json_decode($response, true);

        if (!isset($data['data']['rows']) || !is_array($data['data']['rows'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'code' => 400,
                    'message' => 'Invalid or empty data structure',
                    'data' => null
                ]));
        }

        // Process data using the new function
        if ($param === 'students') {
            $result = $this->processAllStudents($data['data']['rows'], $param);
        } else if ($param === 'faculty') {
            $result = $this->processAllFaculty($data['data']['rows'], $param);
        } else if ($param === 'staff') {
            $result = $this->processAllStaff($data['data']['rows'], $param);
        }

        // Return response
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                'code' => 200,
                'message' => sprintf(
                    'Processed %d records: %d succeeded, %d failed',
                    count($data['data']['rows']),
                    $result['inserted'],
                    $result['failed']
                ),
                'data' => $result
            ]));
    }




}