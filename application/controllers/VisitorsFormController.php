<?php
defined('BASEPATH') or exit('No direct script access allowed');

class VisitorsFormController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('VisitorsPendingModel');
        $this->load->model('DepartmentsModel');
    }

    public function welcome_page()
    {
        $css = base_url('assets/css/intro.css');
        $this->load->view('templates/form_header', ['css' => $css]);
        $this->load->view('welcome_message');
        $this->load->view('templates/form_footer');
    }
    // public function regiform()
    // {
    //     // $css = base_url('assets/css/intro.css');
    //     // $this->load->view('templates/form_header', ['css' => $css]);
    //     $this->load->view('forms/rform');
    //     // $this->load->view('welcome_message');
    //     // $this->load->view('templates/form_footer');    
    // }

    public function visitRequestForm()
    {
        $this->load->view('forms/visitRequest');
    }
    public function registore2()
    {
        $raw_input = file_get_contents('php://input');
        echo $raw_input;
    }

    public function registore()
    {
        // Set the response header for JSON output
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');

        // Read the incoming request's raw JSON body
        $raw_input = file_get_contents('php://input');
        $post_data = json_decode($raw_input, true);

        // Ensure the data is valid before proceeding
        if (empty($post_data)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or empty input data received.'
            ]);
            return;
        }

        // Validate email and phone number before processing
        if (!$this->VisitorsPendingModel->is_unique_email_pending($post_data['email'])) {
            echo json_encode(['success' => false, 'message' => 'Email already registered.']);
            return;
        }

        if (!$this->VisitorsPendingModel->is_unique_phone_pending($post_data['phone'])) {
            echo json_encode(['success' => false, 'message' => 'Phone number already registered.']);
            return;
        }

        // Generate transaction number
        $transaction = $this->generateTransactionNumber();

        // Prepare visitor data
        $visitor_data = [
            'first_name' => $post_data['firstName'] ?? '',
            'middle_name' => '',
            'last_name' => $post_data['lastName'] ?? '',
            'suffix' => $post_data['suffix'] ?? '',
            'email' => $post_data['email'] ?? '',
            'phone_number' => $post_data['phone'] ?? '',
            'company' => $post_data['company'] ?? '',
            'id_type' => $post_data['idType'] ?? '',
            'id_number' => $post_data['idNumber'] ?? '',
            'id_front' => '',
            'id_back' => '',
            'visitor_image' => '',
            'visit_purpose' => $post_data['purpose'] ?? '',
            'visit_date' => $post_data['visitDate'] ?? '',
            'visit_time' => $post_data['visitTime'] ?? '',
            'visit_duration' => $post_data['visitDuration'] ?? '',
            'contact_department' => $post_data['hostDepartment'] ?? '',
            'contact_person' => $post_data['hostName'] ?? '',
            'transaction_number' => $transaction,
            'emergency_contact_person' => ' ',
            'emergency_contact_number' => ' ',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        try {
            // Attempt to insert the visitor data into the database
            $inserted = $this->VisitorsPendingModel->insert($visitor_data);

            if ($inserted) {
                // On success, return the visitor ID (transaction number)
                $response = [
                    'success' => true,
                    'visitorId' => $visitor_data['transaction_number'],
                    'message' => 'Registration successful!'
                ];

                // Log success
                log_message('info', 'Visitor data inserted successfully: ' . json_encode($visitor_data));

                echo json_encode($response);
            } else {
                // Log the error for debugging and throw an exception
                log_message('error', 'Failed to insert visitor data: ' . json_encode($visitor_data));
                throw new Exception('Failed to insert visitor request.');
            }
        } catch (Exception $e) {
            // Catch any exception and return an error message
            log_message('error', 'Error in registore function: ' . $e->getMessage());

            // Return the error as JSON
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function insert($data)
    {
        return $this->db->insert('visitors_pending', $data);
    }

    public function pending()
{
    // Set validation rules
    $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z ]+$/]');
    $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|regex_match[/^[a-zA-Z ]+$/]');
    $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|numeric|max_length[11]');
    $this->form_validation->set_rules('visit_purpose', 'Visit Purpose', 'trim|required|regex_match[/^[a-zA-Z ]+$/]');
    $this->form_validation->set_rules('visit_date', 'Visit Date', 'trim|required');
    $this->form_validation->set_rules('visit_time', 'Visit Time', 'trim|required');
    $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'trim|required|regex_match[/^[a-zA-Z ]+$/]');
    $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'trim|required|numeric|max_length[11]');
    
    if ($this->form_validation->run() == FALSE) {
        // Validation failed, reload the form with errors
        $this->load->view('forms/visitRequest');
    } else {
        // Validation passed, collect the data
        $data = [
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'phone_number' => $this->input->post('phone_number'),
            'visit_purpose' => $this->input->post('visit_purpose'),
            'visit_date' => $this->input->post('visit_date'),
            'visit_time' => $this->input->post('visit_time'),
            'transaction_number' => $this->generateTransactionNumber(),
            'status' => '1',
            'emergency_contact_person' => $this->input->post('emergency_contact_person'),
            'emergency_contact_number' => $this->input->post('emergency_contact_number'),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        try {
            // Send data via cURL
            $response = $this->send_post_request('http://13.209.12.129/msuacs/receiveData', $data);
            
            if ($response && strpos($response, 'success') !== false) {
                // Success message
                $this->session->set_flashdata('success', 'Visit requested successfully');
                redirect('/form2');
            } else {
                // If response doesn't contain 'success'
                $this->session->set_flashdata('error', 'Error occurred: ' . $response);
                redirect('/form2');
            }
        } catch (Exception $e) {
            // If cURL or other error occurs
            $this->session->set_flashdata('error', 'Error occurred: ' . $e->getMessage());
            redirect('/form2');
        }
    }
}

    
    // Function to send data via cURL
    private function send_post_request($url, $data)
    {
        // Initialize cURL session
        $ch = curl_init();
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url); // URL of the remote server
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as string
        curl_setopt($ch, CURLOPT_POST, true); // Use POST method
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Attach form data
    
        // Set headers if needed
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded', // Content type for form data
        ]);
    
        // Execute cURL request and capture the response
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get the HTTP status code
    
        // Check for errors
        if ($response === false) {
            log_message('error', 'cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }
    
        // Log the response and status code
        log_message('debug', 'Response: ' . $response . ' | Status code: ' . $status_code);
    
        // Close cURL session
        curl_close($ch);
    
        // Return the response
        return $response;
    }




    public function status_check()
    {
        $css = base_url('assets/css/step2.css');
        $this->load->view('templates/form_header', ['css' => $css]);
        $this->load->view('status_input');
        $this->load->view('templates/form_footer');
    }

    public function verify_status()
    {
        $transaction_number = $this->input->post('transaction_number');
        $visitor = $this->VisitorsPendingModel->get_by_transaction_number($transaction_number);
        $this->session->set_userdata('visitor_transaction_number', $transaction_number);

        if ($visitor !== null) {
            if ($visitor->status < 3) {
                $visitorPhoneNumber = $visitor->phone_number;
                $this->sendOTPForStatus($visitorPhoneNumber);
            } else {
                $this->session->set_flashdata('danger', 'Transaction number is not active.');
                $this->status_check();
            }
        } else {
            $this->session->set_flashdata('danger', 'Invalid transaction number.');
            $this->status_check();
        }
    }


    //Vonage API Viber Sandbox (For Testing SMS OTP using Viber)
    private function sendOTPForStatus($visitorPhoneNumber)
    {
        $phoneNumber = $this->formatPhoneNumber($visitorPhoneNumber);
        $otp = rand(100000, 999999); // Generate a 6-digit OTP
        $this->session->set_userdata('reservation_otp', $otp); // Store OTP in session for later verification
        $this->session->set_userdata('visitor_phone_number', $phoneNumber);

        // Prepare the data for the POST request
        $data = [
            'from' => '22353',
            'to' => $phoneNumber,
            'message_type' => 'text',
            'text' => "Your OTP is: $otp",
            'channel' => 'viber_service'
        ];

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, 'https://messages-sandbox.nexmo.com/v1/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, 'your-email-here');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute the cURL request and capture the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            $this->session->set_flashdata('danger', 'Failed to send OTP: ' . $error_msg);
            redirect('form/reservation/confirm');
            return;
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the response
        $response_data = json_decode($response, true);

        // Check if the message was sent successfully
        if (isset($response_data['message_uuid'])) {
            $this->session->set_flashdata('success', 'OTP successfully sent to your Phone Number.');
            redirect('form/reservation/confirm');
        } else {
            $otp_message = "Failed to send OTP to $phoneNumber";
            if (isset($response_data['error_text'])) {
                $otp_message .= ' Error: ' . $response_data['error_text'];
            }
            $this->session->set_flashdata('danger', $otp_message);
            redirect('form/reservation/confirm');
        }
    }

    public function otpConfirmationView()
    {
        $css = base_url('assets/css/step2.css');
        $this->load->view('templates/form_header', ['css' => $css]);
        $this->load->view('otp_confirmation');
        $this->load->view('templates/form_footer');
    }

    // Controller method to resend OTP
    public function resendReservationOTP()
    {
        $phoneNumber = $this->formatPhoneNumber($this->session->userdata('visitor_phone_number'));
        $otp = rand(100000, 999999); // Generate a new 6-digit OTP
        $this->session->set_userdata('reservation_otp', $otp); // Store OTP in session for later verification

        // Prepare the data for the POST request
        $data = [
            'from' => '22353',
            'to' => $phoneNumber,
            'message_type' => 'text',
            'text' => "Your OTP is: $otp",
            'channel' => 'viber_service'
        ];

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, 'https://messages-sandbox.nexmo.com/v1/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, 'your-email-here');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute the cURL request and capture the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            $this->session->set_flashdata('danger', 'Failed to resend OTP: ' . $error_msg);
            redirect('form/reservation/confirm');
            return;
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the response
        $response_data = json_decode($response, true);

        // Check if the message was sent successfully
        if (isset($response_data['message_uuid'])) {
            $this->session->set_flashdata('success', 'OTP successfully resent to your Phone Number.');
            redirect('form/reservation/confirm');
        } else {
            $otp_message = "Failed to resend OTP.";
            if (isset($response_data['error_text'])) {
                $otp_message .= ' Error: ' . $response_data['error_text'];
            }
            $this->session->set_flashdata('danger', $otp_message);
            redirect('form/reservation/confirm');
        }
    }


    // Controller method to verify OTP
    public function verifyReservationOTP()
    {
        $enteredOtp = $this->input->post('otp_number');
        $sessionOtp = $this->session->userdata('reservation_otp');
        $visitor = $this->VisitorsPendingModel->get_by_transaction_number($this->session->userdata('visitor_transaction_number'));

        if ($enteredOtp == $sessionOtp) {

            $message = "OTP verified successfully.";
            // Proceed to the next step
            $this->session->unset_userdata('reservation_otp');
            $this->session->set_flashdata('success', $message);

            $data['visitor'] = $visitor;
            $data['departments'] = $this->DepartmentsModel->get_all(); // Fetch departments data


            if ($visitor->status == 1) {
                $css = base_url('assets/css/step3.css');
                $this->load->view('templates/form_header', ['css' => $css]);
                $this->load->view('existing_request_display', $data);
                $this->load->view('templates/form_footer');
            } elseif ($visitor->status == 2) {
                $css = base_url('assets/css/step3.css');
                $this->load->view('templates/form_header', ['css' => $css]);
                $this->load->view('approved_request_display', $data);
                $this->load->view('templates/form_footer');
            }
        } else {
            $this->session->set_flashdata('danger', 'Invalid OTP. Please try again.');
            redirect('form/reservation/confirm');
        }
    }

    public function reservation_update($id)
    {
        $visitor = $this->VisitorsPendingModel->get_by_id($id);

        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|max_length[50]|min_length[3]');
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'max_length[50]|min_length[3]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]');
        $this->form_validation->set_rules('suffix', 'Suffix', 'max_length[4]|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|max_length[50]|callback_check_unique_email[' . $id . ']');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required|trim|callback_check_unique_phone[' . $id . ']|callback_valid_philippine_phone');
        $this->form_validation->set_rules('company', 'Company', 'max_length[50]|trim|min_length[3]');
        $this->form_validation->set_rules('id_type', 'ID Type', 'required|max_length[50]|trim');
        $this->form_validation->set_rules('visit_purpose', 'Purpose', 'required|trim');
        $this->form_validation->set_rules('visit_date', 'Date of Visit', 'required|trim|callback_validate_date_min_today');
        $this->form_validation->set_rules('visit_time', 'Time of Visit', 'required|trim');
        $this->form_validation->set_rules('visit_duration', 'Visit Duration', 'required|trim');
        $this->form_validation->set_rules('contact_department', 'Contact Department', 'required|trim');
        $this->form_validation->set_rules('contact_person', 'Contact Person Name', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');


        if ($this->form_validation->run() == FALSE) {
            $data['visitor'] = $visitor;
            $data['departments'] = $this->DepartmentsModel->get_all(); // Fetch departments data
            $css = base_url('assets/css/step3.css');
            $this->load->view('templates/form_header', ['css' => $css]);
            $this->load->view('existing_request_display', $data);
            $this->load->view('templates/form_footer');
        } else {


            $data = array(
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'suffix' => $this->input->post('suffix'),
                'email' => $this->input->post('email'),
                'phone_number' => $this->input->post('phone_number'),
                'company' => $this->input->post('company'),
                'id_type' => $this->input->post('id_type'),
                'id_number' => $this->input->post('id_number'),
                'id_front' => $this->input->post('id_front_base64'),
                'id_back' => $this->input->post('id_back_base64'),
                'visit_purpose' => $this->input->post('visit_purpose'),
                'visit_date' => $this->input->post('visit_date'),
                'visit_time' => $this->input->post('visit_time'),
                'visit_duration' => $this->input->post('visit_duration'),
                'contact_department' => $this->input->post('contact_department'),
                'contact_person' => $this->input->post('contact_person'),
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
                'updated_at' => date('Y-m-d H:i:s'),
            );

            $this->VisitorsPendingModel->update($id, $data);
            $rows = $this->db->affected_rows();

            if ($rows > 0) {
                $this->session->set_flashdata('success', 'Visit request successfully updated.');
                // echo json_encode('success');

            } else {
                $this->session->set_flashdata('danger', 'Error! Unable to update reservation.');
                // echo json_encode('error');
            }

            redirect('form/status');
        }
    }

    public function reservation_cancel($transaction_number)
    {
        $this->VisitorsPendingModel->delete_by_transaction_number($transaction_number);
        $rows = $this->db->affected_rows();


        if ($rows > 0) {
            $this->session->set_flashdata('success', 'Visit request successfully deleted.');
        } else {
            $this->session->set_flashdata('danger', 'Error! Unable to delete reservation.');
        }
        // Redirect to the status check page
        redirect('form/status');
    }

    // Step 1: Display form for taking a picture
    public function step11()
    {
        $data['progress'] = 15;
        $css = base_url('assets/css/step1.css');
        $this->load->view('templates/form_header', ['css' => $css]);
        $this->load->view('forms/step1', $data);
        $this->load->view('templates/form_footer');
    }
    public function step1()
    {
        $data['progress'] = 30;
        $css = base_url('assets/css/step2.css');
        $this->load->view('templates/form_header', ['css' => $css]);
        $this->load->view('forms/step2', $data);
        $this->load->view('templates/form_footer');
    }

    // Step 1: Process form submission for taking a picture
    public function process_step1()
    {
        $this->form_validation->set_rules('visitor_image', 'Visitor Image', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->step1();
        } else {
            $visitor_image = $this->input->post('visitor_image');
            $this->session->set_userdata('step1_data', $visitor_image);
            redirect('visitors_pending/form/step2');
        }
    }

    // Step 2: Display form for personal details
    public function step2()
    {
        $this->checkPreviousSteps(['step1' => 'visitors_pending/form/step1']);
        $data['progress'] = 30;
        $css = base_url('assets/css/step2.css');
        $this->load->view('templates/form_header', ['css' => $css]);
        $this->load->view('forms/step2', $data);
        $this->load->view('templates/form_footer');
    }

    // Step 2: Process form submission for personal details
    public function process_step2()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|min_length[3]|max_length[50]');
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'max_length[50]|min_length[3]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|max_length[50]|min_length[2]');
        $this->form_validation->set_rules('suffix', 'Suffix', 'max_length[4]|trim');
        $this->form_validation->set_rules(
            'email',
            'Email',
            'required|valid_email|trim|is_unique[visitors_pending.email]|max_length[50]',
            array(
                'is_unique' => 'This %s is already used in a pending visit request.'
            )
        );
        $this->form_validation->set_rules(
            'phone_number',
            'Phone Number',
            'required|trim|callback_valid_philippine_phone|max_length[10]'
        );

        if ($this->form_validation->run() == FALSE) {
            $this->step2();
        } else {
            $step2_data = array(
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'suffix' => $this->input->post('suffix'),
                'email' => $this->input->post('email'),
                'phone_number' => $this->input->post('phone_number')
            );
            $this->session->set_userdata('step2_data', $step2_data);
            redirect('visitors_pending/form/step3');
        }
    }

    // Step 3: Display form for additional details
    public function step3()
    {
        /*
        $this->checkPreviousSteps([
            'step1' => 'visitors_pending/form/step1',
            'step2' => 'visitors_pending/form/step2'
        ]);
        */
        $data['progress'] = 45;
        $css = base_url('assets/css/step3.css');
        $this->load->view('templates/form_header', ['css' => $css]);
        $this->load->view('forms/step3', $data);
        $this->load->view('templates/form_footer');
    }

    public function process_step3()
    {
        $this->form_validation->set_rules('company', 'Company', 'max_length[50]|trim|min_length[3]');
        $this->form_validation->set_rules('id_type', 'ID Type', 'required|max_length[50]|trim');

        /*
                if($this->input->post('id_type') != "None"){
                    $this->form_validation->set_rules('id_number', 'ID Number', 'required|max_length[50]|trim|min_length[6]');
                    $this->form_validation->set_rules('id_front_base64', 'Photo of ID (front)', 'required|trim');
                    $this->form_validation->set_rules('id_back_base64', 'Photo of ID (back)', 'required|trim');
                }
         */
        if ($this->form_validation->run() == FALSE) {
            $this->step3();
        } else {
            $step3_data = array(
                'company' => $this->input->post('company'),
                'id_type' => $this->input->post('id_type'),
                'id_number' => $this->input->post('id_number'),
                //  'id_front_base64' => $this->input->post('id_front_base64'),
                //  'id_back_base64' => $this->input->post('id_back_base64')
            );
            $this->session->set_userdata('step3_data', $step3_data);
            redirect('visitors_pending/form/step4');  // Update to the next step
        }
    }

    // Step 3: Display form for additional details
    public function step4()
    {
        /*
        $this->checkPreviousSteps([
            'step1' => 'visitors_pending/form/step1',
            'step2' => 'visitors_pending/form/step2',
            'step3' => 'visitors_pending/form/step3'
        ]);
        */
        $data['departments'] = $this->DepartmentsModel->get_all(); // Fetch departments data
        $data['progress'] = 60;
        $css = base_url('assets/css/step2.css');
        $this->load->view('templates/form_header', ['css' => $css]);
        $this->load->view('forms/step4', $data);
        $this->load->view('templates/form_footer');
    }

    public function process_step4()
    {
        $this->form_validation->set_rules('visit_purpose', 'Purpose', 'required|trim|max_length[155]');
        $this->form_validation->set_rules('visit_date', 'Date of Visit', 'required|trim|callback_validate_date_min_today');
        $this->form_validation->set_rules('visit_time', 'Time of Visit', 'required|trim');
        $this->form_validation->set_rules('visit_duration', 'Visit Duration', 'required|trim');
        $this->form_validation->set_rules('contact_department', 'Contact Department', 'required|trim');
        $this->form_validation->set_rules('contact_person', 'Contact Person Name', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');


        if ($this->form_validation->run() == FALSE) {
            $this->step4();  // Reload the view with errors
        } else {

            $step4_data = array(
                'visit_purpose' => $this->input->post('visit_purpose'),
                'visit_date' => $this->input->post('visit_date'),
                'visit_time' => $this->input->post('visit_time'),
                'visit_duration' => $this->input->post('visit_duration'),
                'contact_department' => $this->input->post('contact_department'),
                'contact_person' => $this->input->post('contact_person'),
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
            );
            $this->session->set_userdata('step4_data', $step4_data);
            $this->show_review(); // Redirect to the next step
        }
    }



    public function get_contact_persons_by_position()
    {
        $department = $this->input->post('department');

        $contactPersons = $this->VisitorsPendingModel->get_contact_persons_by_department($department);

        // Return the data as JSON
        echo json_encode($contactPersons);
    }


    public function show_review()
    {
        /*
        $this->checkPreviousSteps([
            'step1' => 'visitors_pending/form/step1',
            'step2' => 'visitors_pending/form/step2',
            'step3' => 'visitors_pending/form/step3',
            'step4' => 'visitors_pending/form/step4'
        ]);
        */
        $data['departments'] = $this->DepartmentsModel->get_all(); // Fetch departments data
        $data['progress'] = 75;
        $css = base_url('assets/css/step3.css');
        $this->load->view('templates/form_header', ['css' => $css]);
        $this->load->view('forms/review_input', $data);
        $this->load->view('templates/form_footer');
    }

    public function process_final()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|max_length[50]|min_length[3]');
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'max_length[50]|min_length[3]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]');
        $this->form_validation->set_rules('suffix', 'Suffix', 'max_length[4]|trim');
        $this->form_validation->set_rules(
            'email',
            'Email',
            'required|valid_email|trim|is_unique[visitors_pending.email]|max_length[50]',
            array(
                'is_unique' => 'This %s is already used in a pending visit request.'
            )
        );
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required|trim|is_unique[visitors_pending.phone_number]|callback_valid_philippine_phone', array(
            'is_unique' => 'This %s is already used in a pending visit request.'
        ));
        $this->form_validation->set_rules('company', 'Company', 'max_length[50]|trim|min_length[3]');
        $this->form_validation->set_rules('id_type', 'ID Type', 'required|max_length[50]|trim');
        /*
        if($this->input->post('id_type') !== "None"){
            $this->form_validation->set_rules('id_number', 'ID Number', 'required|max_length[50]|trim|min_length[6]');
            $this->form_validation->set_rules('id_front_base64', 'Photo of ID (front)', 'required|trim');
            $this->form_validation->set_rules('id_back_base64', 'Photo of ID (back)', 'required|trim');
        }
        */
        $this->form_validation->set_rules('visit_purpose', 'Purpose', 'required|trim');
        $this->form_validation->set_rules('visit_date', 'Date of Visit', 'required|trim|callback_validate_date_min_today');
        $this->form_validation->set_rules('visit_time', 'Time of Visit', 'required|trim');
        $this->form_validation->set_rules('visit_duration', 'Visit Duration', 'required|trim');
        $this->form_validation->set_rules('contact_department', 'Contact Department', 'required|trim');
        $this->form_validation->set_rules('contact_person', 'Contact Person Name', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');

        if ($this->form_validation->run() == FALSE) {
            $this->show_review();  // Reload the view with errors
        } else {
            $step2_data = array(
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'suffix' => $this->input->post('suffix'),
                'email' => $this->input->post('email'),
                'phone_number' => $this->input->post('phone_number'),
            );

            $step3_data = array(
                'company' => $this->input->post('company'),
                'id_type' => $this->input->post('id_type'),
                'id_number' => $this->input->post('id_number'),
                //  'id_front_base64' => $this->input->post('id_front_base64'),
                //  'id_back_base64' => $this->input->post('id_back_base64'),
            );

            $step4_data = array(
                'visit_purpose' => $this->input->post('visit_purpose'),
                'visit_date' => $this->input->post('visit_date'),
                'visit_time' => $this->input->post('visit_time'),
                'visit_duration' => $this->input->post('visit_duration'),
                'contact_department' => $this->input->post('contact_department'),
                'contact_person' => $this->input->post('contact_person'),
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
            );

            //SET THE FINAL REVIEWED DATA INTO THE SESSION
            $this->session->set_userdata('step2_data', $step2_data);
            $this->session->set_userdata('step3_data', $step3_data);
            $this->session->set_userdata('step4_data', $step4_data);

            $step2_url = 'visitors_pending/form/step2';
            $step5_url = 'visitors_pending/form/step5';
            $this->sendOTP();
        }
    }

    public function step5()
    {
        /*
        $this->checkPreviousSteps([
            'step1' => 'visitors_pending/form/step1',
            'step2' => 'visitors_pending/form/step2',
            'step3' => 'visitors_pending/form/step3',
            'step4' => 'visitors_pending/form/step4'
        ]);  
        */
        $data['progress'] = 90;
        $css = base_url('assets/css/step2.css');
        $this->load->view('templates/form_header', ['css' => $css]);
        $this->load->view('forms/step5', $data);
        $this->load->view('templates/form_footer');
    }

    // Controller method to send OTP
    // private function sendOTP() {
    //     $step2_data = $this->session->userdata('step2_data');
    //     if (!isset($step2_data['phone_number'])) {
    //         // Handle the error if the phone number is not available
    //         $this->session->set_flashdata('phone_number_error', 'Phone Number is not available.');
    //         redirect('visitors_pending/form/step2'); // Redirect to step 2 to show the message
    //         return;
    //     }

    //     $phoneNumber = $this->formatPhoneNumber($step2_data['phone_number']);
    //     $otp = rand(100000, 999999); // Generate a 6-digit OTP
    //     $this->session->set_userdata('otp', $otp); // Store OTP in session for later verification

    //     $basic  = new \Vonage\Client\Credentials\Basic("", "");
    //     $client = new \Vonage\Client($basic);

    //     $response = $client->sms()->send(
    //         new \Vonage\SMS\Message\SMS($phoneNumber, 'MSU - NAMS', "Your OTP is: $otp")
    //     );

    //     $message = $response->current();

    //     if ($message->getStatus() == 0) {
    //         $this->session->set_flashdata('success', 'OTP successfully sent to your Phone Number.');
    //         redirect('visitors_pending/form/step5');

    //     } else {
    //         $otp_message = "Failed to send OTP: " . $message->getStatus();;
    //         $this->session->set_flashdata('danger', $otp_message);
    //         redirect('visitors_pending/form/step5');
    //     }
    // }

    //Vonage API Viber Sandbox (For Testing SMS OTP using Viber)
    private function sendOTP()
    {
        $step2_data = $this->session->userdata('step2_data');
        if (!isset($step2_data['phone_number'])) {
            // Handle the error if the phone number is not available
            $this->session->set_flashdata('phone_number_error', 'Phone Number is not available.');
            redirect('visitors_pending/form/step2'); // Redirect to step 2 to show the message
            return;
        }

        $phoneNumber = $this->formatPhoneNumber($step2_data['phone_number']);
        $otp = rand(100000, 999999); // Generate a 6-digit OTP
        $this->session->set_userdata('otp', $otp); // Store OTP in session for later verification

        // Prepare the data for the POST request
        $data = [
            'from' => '22353',
            'to' => $phoneNumber,
            'message_type' => 'text',
            'text' => "Your OTP is: $otp",
            'channel' => 'viber_service'
        ];

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, 'https://messages-sandbox.nexmo.com/v1/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, 'your-vonage-api-here');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute the cURL request and capture the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            $this->session->set_flashdata('danger', 'Failed to send OTP: ' . $error_msg);
            redirect('visitors_pending/form/step5');
            return;
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the response
        $response_data = json_decode($response, true);

        // Check if the message was sent successfully
        if (isset($response_data['message_uuid'])) {
            $this->session->set_flashdata('success', 'OTP successfully sent to your Phone Number.');
            redirect('visitors_pending/form/step5');
        } else {
            $otp_message = "Failed to send OTP to $phoneNumber";
            if (isset($response_data['error_text'])) {
                $otp_message .= ' Error: ' . $response_data['error_text'];
            }
            $this->session->set_flashdata('danger', $otp_message);
            redirect('visitors_pending/form/step5');
        }
    }

    // Controller method to resend OTP
    public function resendOTP()
    {
        $step2_data = $this->session->userdata('step2_data');
        if (!isset($step2_data['phone_number'])) {
            // Handle the error if the phone number is not available
            $this->session->set_flashdata('phone_number_error', 'Phone Number is not available.');
            redirect('visitors_pending/form/step2'); // Redirect to step 2 to show the message
            return;
        }

        $phoneNumber = $this->formatPhoneNumber($step2_data['phone_number']);
        $otp = rand(100000, 999999); // Generate a new 6-digit OTP
        $this->session->set_userdata('otp', $otp); // Store OTP in session for later verification

        // Prepare the data for the POST request
        $data = [
            'from' => '22353',
            'to' => $phoneNumber,
            'message_type' => 'text',
            'text' => "Your OTP is: $otp",
            'channel' => 'viber_service'
        ];

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, 'https://messages-sandbox.nexmo.com/v1/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, 'your-email-here');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute the cURL request and capture the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            $this->session->set_flashdata('danger', 'Failed to resend OTP: ' . $error_msg);
            redirect('visitors_pending/form/step5');
            return;
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the response
        $response_data = json_decode($response, true);

        // Check if the message was sent successfully
        if (isset($response_data['message_uuid'])) {
            $this->session->set_flashdata('success', 'OTP successfully resent to your Phone Number.');
            redirect('visitors_pending/form/step5');
        } else {
            $otp_message = "Failed to resend OTP.";
            if (isset($response_data['error_text'])) {
                $otp_message .= ' Error: ' . $response_data['error_text'];
            }
            $this->session->set_flashdata('danger', $otp_message);
            redirect('visitors_pending/form/step5');
        }
    }



    // Controller method to verify OTP
    public function verifyOTP()
    {
        $enteredOtp = $this->input->post('otp_number');
        $sessionOtp = $this->session->userdata('otp');

        if ($enteredOtp == $sessionOtp) {
            $message = "OTP verified successfully.";
            // Proceed to the next step
            $this->session->unset_userdata('otp');
            $this->session->set_flashdata('success', $message);
            $this->storeVisitorRequest();
        } else {
            $this->session->set_flashdata('danger', 'Invalid OTP. Please try again.');
            redirect('visitors_pending/form/step5');
        }
    }


    private function success($transaction_number)
    {
        $data['transaction_number'] = $transaction_number;
        $css = base_url('assets/css/intro.css');
        $this->load->view('templates/form_header', ['css' => $css]);
        $this->load->view('forms/success', $data);
        $this->load->view('templates/form_footer');
    }

    private function storeVisitorRequest()
    {
        $step1_data = $this->session->userdata('step1_data');
        $step2_data = $this->session->userdata('step2_data');
        $step3_data = $this->session->userdata('step3_data');
        $step4_data = $this->session->userdata('step4_data');

        // Validate that all necessary session data exists
        if (!$step1_data || !$step2_data || !$step3_data || !$step4_data) {
            // Redirect or show an error if any step data is missing
            $this->session->set_flashdata('danger', 'Some required data is missing. Please complete the form again.');
            redirect('visitors_pending/form/step1');
            return;
        }

        $transaction_number = $this->generateTransactionNumber();

        $visitor_data = array(
            'first_name' => $step2_data['first_name'],
            'middle_name' => $step2_data['middle_name'],
            'last_name' => $step2_data['last_name'],
            'suffix' => $step2_data['suffix'],
            'email' => $step2_data['email'],
            'phone_number' => $step2_data['phone_number'],
            'company' => $step3_data['company'],
            'id_type' => $step3_data['id_type'],
            'id_number' => $step3_data['id_number'],
            'id_front' => $step3_data['id_front_base64'],
            'id_back' => $step3_data['id_back_base64'],
            'visitor_image' => $step1_data,
            'visit_purpose' => $step4_data['visit_purpose'],
            'visit_date' => $step4_data['visit_date'],
            'visit_time' => $step4_data['visit_time'],
            'visit_duration' => $step4_data['visit_duration'],
            'contact_department' => $step4_data['contact_department'],
            'contact_person' => $step4_data['contact_person'],
            'emergency_contact_person' => $step4_data['emergency_contact_person'],
            'emergency_contact_number' => $step4_data['emergency_contact_number'],
            'transaction_number' => $transaction_number,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        );

        try {
            if ($this->VisitorsPendingModel->insert($visitor_data)) {
                // Clear all session data
                $this->session->unset_userdata('step1_data');
                $this->session->unset_userdata('step2_data');
                $this->session->unset_userdata('step3_data');
                $this->session->unset_userdata('step4_data');

                // Redirect to success page
                $this->session->set_flashdata('success', 'Visitor request submitted successfully.');
                $this->success($transaction_number);
            } else {
                // Handle insertion failure
                $this->session->set_flashdata('danger', 'Failed to submit visitor request. Please try again.');
                redirect('visitors_pending/form/step5');
            }
        } catch (Exception $e) {
            // Handle any exceptions or database errors
            $this->session->set_flashdata('danger', 'An error occurred: ' . $e->getMessage());
            redirect('visitors_pending/form/step5');
        }
    }


    //Function to Generate the Transaction Number and avoid generating existing transaction number
    /**
     * QR CODE FORMAT:
     * YY - Year
     * MM - month
     * DD -day
     * HH - hrs (military)
     * MM - minutes
     * SS - seconds
     * NNNNNNNN - unique number (x8)
     * 
     * 
     * format:
     * YYMMDDHHMMSSNNNNNNNN
     * 
     * 
     */
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


    /**********************************CUSTOM CALLBACKS FOR FORM VALIDATION**************************************************/

    public function validate_date_min_today($date)
    {
        $input_date = strtotime($date);
        $current_date = strtotime(date('Y-m-d'));
        $today = date('m-d-Y');

        if ($input_date < $current_date) {
            $this->form_validation->set_message('validate_date_min_today', 'The {field} value must be today\'s date or later.');
            return FALSE;
        }
        return TRUE;
    }


    public function check_unique_email($email, $id)
    {
        $this->load->model('VisitorsPendingModel');
        $visitor = $this->VisitorsPendingModel->get_by_id($id);
        if ($visitor->email === $email) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_unique_email', 'The {field} field must contain a unique value.');
            return $this->VisitorsPendingModel->is_unique_email($email, $id);
        }
    }

    public function check_unique_phone($phone_number, $id)
    {
        $this->load->model('VisitorsPendingModel');
        $visitor = $this->VisitorsPendingModel->get_by_id($id);
        if ($visitor->phone_number === $phone_number) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_unique_phone', 'The {field} field must contain a unique value.');
            return $this->VisitorsPendingModel->is_unique_phone($phone_number, $id);
        }
    }

    private function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Check if the phone number starts with a country code
        if (substr($phoneNumber, 0, 2) !== '63') {
            // Assuming the user is from the Philippines and didn't include the country code
            $phoneNumber = '63' . ltrim($phoneNumber, '0'); // Remove leading zeros
        }

        return $phoneNumber;
    }

    // Custom validation function
    public function valid_philippine_phone($phone)
    {
        // Check for 9xxxxxxxxxx format (11 digits)
        if (preg_match('/^9\d{9}$/', $phone)) {
            return TRUE;
        }

        $this->form_validation->set_message('valid_philippine_phone', 'The {field} field must contain a valid Philippine phone number.');
        return FALSE;
    }

    private function checkPreviousSteps($steps)
    {
        foreach ($steps as $step => $redirect_url) {
            if ($this->session->userdata($step . '_data') == null) {
                $this->session->set_flashdata('danger', 'You must complete the previous step first.');
                redirect($redirect_url);
            }
        }
    }
}
