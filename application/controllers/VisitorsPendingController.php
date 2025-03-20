<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VisitorsPendingController extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('VisitorsPendingModel');
        $this->load->model('VisitorsApproveModel');
        $this->load->model('VisitorsDeclineModel');
        $this->load->model('AuthModel');
        $this->load->model('StudentsModel');
        $this->load->model('FacultyModel');
        $this->load->model('StaffsModel');
        $this->load->model('ResidentsModel');
        $this->load->model('GuestsModel');
        $this->load->model('CollegesModel');
        $this->load->model('LocationsModel');
        $this->load->model('DepartmentsModel');
        $this->load->model('ProgramsModel');
        $this->load->helper('file'); // For file operations
        $this->load->library('email');


        //Check first if there is admin session data
        if ($this->session->userdata('admin') == null) {
            return redirect('admin/login');
        }
        else{
            //If session data exists, get admin id from session data and verify it with database
            $admin_id = $this->session->userdata('admin')['id'];
            $doesAdminExist = $this->AuthModel->verify_admin_id($admin_id);
            if (!$doesAdminExist) {
                $this->session->sess_destroy();
                return redirect('admin/login');
            }         
        }  
    }

    protected function checkPermission(){
        // Check if the admin has the required role before proceeding
        $required_role_id = 4; // ID of the required role
        if (!require_role($required_role_id)) {
            return; // Stop further execution if unauthorized
        }
    }


/*****************************************VISITORS PENDING SECTION**************************************************** */    

    public function index(){
        $this->checkPermission();
        $d['title'] = 'Pre-Registered Visitors';
        $d['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'visitors_pending.js';
        
        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $d);
        $this->load->view('admin/visit_management/visitors_pending', $d);
        $this->load->view('templates/dashboard_footer', $data);
    }

    public function visitorsPendingList(){
        
        //Visitors Pending Records for Datatables
        $draw = $this->input->get('draw');
        $start = $this->input->get('start');
        $length = $this->input->get('length');
        $search = $this->input->get('search')['value'];
        $orderColumn = $this->input->get('order')[0]['column'] + 1;
        $orderDir = $this->input->get('order')[0]['dir'];

        $data['locations'] = $this->LocationsModel->get_all();

         // Specify only the required columns from the faculty table
         $columns = [
            'id',
            'first_name',
            'last_name',
            'visit_purpose',
            'visit_date',
            'visit_time',
            'phone_number',
            'email',
            'status',
            'emergency_contact_person',
            'emergency_contact_number',
        ];

        // Your custom logic to fetch data based on parameters
        $this->db->select(implode(',', $columns))
            ->from('visitors_pending')
            ->group_start()
            ->like('id', $search)
            ->or_like('first_name', $search)
            ->or_like('last_name', $search)
            ->or_like('email', $search)     
            ->or_like('phone_number', $search) 
            ->or_like('visitor_image', $search) 
            ->or_like('visit_purpose', $search) 
            ->or_like('visit_date', $search) 
            ->or_like('visit_time', $search)
            ->or_like('status', $search)
            ->or_like('emergency_contact_person', $search)
            ->or_like('emergency_contact_number', $search)
            ->group_end()
            ->order_by($orderColumn, $orderDir)
            ->limit($length, $start);

        // Initial sorting by the first column (ID) in descending order
        if ($draw === '1') {
            $this->db->order_by('id', 'desc');
        }

        $data = $this->db->get()->result_array();

        // Count total records without filtering
        $totalRecords = $this->db->count_all_results('visitors_pending');

        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function addVisitorView(){
        $departments = $this->DepartmentsModel->get_all(); // Fetch departments data
        $this->load->view('modals/add_visitors_pending_form', ['departments' => $departments]);
    }

    public function addVisitor(){
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|max_length[50]|min_length[3]');
        // $this->form_validation->set_rules('middle_name', 'Middle Name', 'max_length[50]|min_length[3]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]');
        // $this->form_validation->set_rules('suffix', 'Suffix', 'max_length[4]|trim');
        // $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|is_unique[visitors_pending.email]|max_length[50]',
        // array(
        //     'is_unique'     => 'This %s is already used in a pending visit request.'
        // ));
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required|trim|is_unique[visitors_pending.phone_number]|callback_valid_philippine_phone', array(
            'is_unique' => 'This %s is already used in a pending visit request.'
        ));
        // $this->form_validation->set_rules('company', 'Company', 'max_length[50]|trim|min_length[3]');
        // $this->form_validation->set_rules('id_type', 'ID Type', 'required|max_length[50]|trim');
        
        // $this->form_validation->set_rules('id_number', 'ID Number', 'required|max_length[50]|trim|min_length[6]');
        // $this->form_validation->set_rules('id_front_base64', 'Photo of ID (front)', 'required|trim');
        // $this->form_validation->set_rules('id_back_base64', 'Photo of ID (back)', 'required|trim');

        $this->form_validation->set_rules('visit_purpose', 'Purpose', 'required|trim');
        $this->form_validation->set_rules('visit_date', 'Date of Visit', 'required|trim|callback_validate_date_min_today');
        $this->form_validation->set_rules('visit_time', 'Time of Visit', 'required|trim');
        // $this->form_validation->set_rules('visit_duration', 'Visit Duration', 'required|trim');
        // $this->form_validation->set_rules('contact_department', 'Contact Department', 'required|trim');
        // $this->form_validation->set_rules('contact_person', 'Contact Person Name', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');

        if (empty($_FILES['image']['name']) && empty($_FILES['uploadInput']['name']))
        {
            $this->form_validation->set_rules('image', 'User Image', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo (json_encode([
                'status' => 400, 
                'message' => $this->form_validation->error_array()
            ]));
        }
        else{

            $data = [
                'first_name' => $this->input->post('first_name'),
                // 'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'), 
                // 'suffix' => $this->input->post('suffix'), 
                // 'email' => $this->input->post('email'), 
                'phone_number' => $this->input->post('phone_number'), 
                // 'company' => $this->input->post('company'), 
                // 'id_type' => $this->input->post('id_type'), 
                // 'id_number' => $this->input->post('id_number'), 
                // 'id_front' => $this->input->post('id_front_base64'), 
                // 'id_back' => $this->input->post('id_back_base64'), 
                'visit_purpose' => $this->input->post('visit_purpose'), 
                'visit_date' => $this->input->post('visit_date'), 
                'visit_time' => $this->input->post('visit_time'), 
                // 'visit_duration' => $this->input->post('visit_duration'), 
                // 'contact_department' => $this->input->post('contact_department'), 
                // 'contact_person' => $this->input->post('contact_person'), 
                'emergency_contact_person' => $this->input->post('emergency_contact_person'), 
                'emergency_contact_number' => $this->input->post('emergency_contact_number'), 
                'rfid' => $this->input->post('rfid'), 
                'status' => 2, 
            ];

            // Configure upload settings
            $config['upload_path'] = 'assets/images/uploads/visitors/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048; // 2MB max
            $config['file_name'] = uniqid(); // Unique filename

            $this->load->library('upload', $config);

            if (!empty($_FILES['image']['name'])) {
                $uploadField = 'image';
            } elseif (!empty($_FILES['uploadInput']['name'])) {
                $uploadField = 'uploadInput';
            } else {
                $uploadField = null; // No file uploaded
            }

            if ($uploadField) {
                if (!$this->upload->do_upload($uploadField)) {
                    echo json_encode([
                        'status' => 400,
                        'message' => [$this->upload->display_errors()]
                    ]);
                    return;
                } else {
                    // Get upload data and set new image filename
                    $upload_data = $this->upload->data();
                    $data['image'] = 'assets/images/uploads/visitors/' . $upload_data['file_name'];
                }
            }

            if ($this->VisitorsApproveModel->insert($data)) {

                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Added new visitor record with RFID# " . $this->input->post('rfid'), 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Active Visitor added successfully!']);
            }
            else{
                echo json_encode([
                    'status' => 500, 
                    'message' => 'Failed to visitor record.']);
            }

        }
    }

    public function editVisitorView($id) {
        // Ensure the id is sanitized and valid
        $id = intval($id);
        $visitor = $this->db->get_where('visitors_pending', ['id' => $id])->row_array();
        $departments = $this->DepartmentsModel->get_all(); // Fetch departments data
    
        // Check if visitor data was retrieved
        if ($visitor) {
            $this->load->view('modals/edit_visitors_pending_form', ['visitor' => $visitor, 'departments' => $departments]);
        } else {
            // Handle the case where visitor data is not found
            echo "Visitor not found.";
        }
    }
    

    public function updateVisitor($id) {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|max_length[50]|min_length[3]|regex_match[/^[a-zA-Z -]+$/]', array(
            'regex_match' => 'The {field} can only contain letters, spaces and minus symbol'
        ));
        // $this->form_validation->set_rules('middle_name', 'Middle Name', 'max_length[50]|min_length[3]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]|regex_match[/^[a-zA-Z -]+$/]', array( 
            'regex_match' => 'The {field} can only contain letters, spaces and minus symbol'
        ));
        // $this->form_validation->set_rules('suffix', 'Suffix', 'max_length[4]|trim');
        // $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|max_length[50]',
        // array(
        //     'is_unique'     => 'This %s is already used in a pending visit request.'
        // ));

        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required|trim|callback_valid_philippine_phone', array(
            'is_unique' => 'This %s is already used in a pending visit request.'
        ));
        // $this->form_validation->set_rules('company', 'Company', 'max_length[50]|trim|min_length[3]');
        // $this->form_validation->set_rules('id_type', 'ID Type', 'required|max_length[50]|trim');

        $this->form_validation->set_rules('visit_purpose', 'Purpose', 'required|trim');
        // $this->form_validation->set_rules('visit_date', 'Date of Visit', 'required|trim');
        // $this->form_validation->set_rules('visit_time', 'Time of Visit', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim|regex_match[/^[a-zA-Z -]+$/]', array(
            'regex_match' => 'The {field} can only contain letters, spaces and minus symbol'
        ));
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone', array(
            'is_unique' => 'This %s is already used in a pending visit request.'
        ));

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400, 
                'message' => $this->form_validation->error_array()
            ]);   

        }
        else {
            $data = [
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'phone_number' => $this->input->post('phone_number'),
                'visit_purpose' => $this->input->post('visit_purpose'),
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
                // 'visit_date' => $this->input->post('visit_date'),
                // 'visit_time' => $this->input->post('visit_time'), 

            ];

            if ($this->VisitorsPendingModel->update($id, $data)) {

                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Edited Pending visitor request information with ID# " . $id, 
                    $this->session->userdata('admin')['username'], 
                );
                
                echo json_encode([
                    'status' => 200, 
                    'message' => 'Form updated successfully!']);
            }
            else{
                echo json_encode([
                    'status' => 500, 
                    'message' => 'Failed to update form.']);
            }
        }    
    }

    public function deleteVisitor($id) {
        $id = intval($id);
        $admin_username = $this->session->userdata('admin')['username'];
        $password = $this->input->post('password');
            
        // Verify the admin's password
        $is_verified = $this->AuthModel->verify_admin($admin_username, $password);
        
        if ($is_verified) {
            // Delete the visitor record
            if($this->VisitorsPendingModel->delete($id)){
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'], 
                    "Deleted Pending visitor request information with ID# " . $id, 
                    $this->session->userdata('admin')['username'], 
                );

                echo json_encode(['status' => 200, 'message' => 'Visitor deleted successfully!']);
            }
            else{
                echo json_encode(['status' => 500, 'message' => 'Failed to delete visitor.']);
            }

        } else {
            echo json_encode(['status' => 400, 'message' => 'Invalid password!']);
        }
    }
    
    public function approveVisitor($id) {
        $id = intval($id);
        $admin_username = $this->session->userdata('admin')['username'];
        $rfid = $this->input->post('rfid');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400, 
                'message' => $this->form_validation->error_array()
            ]);
        }
        else{
            $visitor_data = $this->VisitorsPendingModel->get_by_id($id);
            
            // Commented out the email functions regarding with the approval of visitors (currently/W.I.P.)
            // $email_sent = $this->sendEmailWithQR(
            //     'testingntek@gmail.com', 
            //     $visitor_data->first_name,
            //     $visitor_data->visit_purpose,
            //     $visitor_data->visit_date,
            //     $visitor_data->visit_time);
    
            // Send Email with QR Code
            // if($email_sent){
                // Convert visitor_data object to an associative array
                $visitor_array = (array) $visitor_data;
    
                // Remove the id from the array
                unset($visitor_array['id']);
    
                // Set the status to declined (assuming 3 represents declined)
                $visitor_array['status'] = 2;
                $visitor_array['rfid'] = $rfid;
                $visitor_array['updated_at'] = date('Y-m-d H:i:s');
                // $visitor_array['image'] = $this->uploadVisitorImage($visitor_array['visitor_image']);
                unset($visitor_array['visitor_image']);

                $this->VisitorsApproveModel->insert($visitor_array);
                $this->VisitorsPendingModel->delete($id);
                echo json_encode(['status' => 200, 'message' => 'Approved successfully!']);
            // }
    
            // else{
            //     echo json_encode(['status' => 500, 'message' => 'Failed to send email']);
            // }
        }

    }
    
    private function sendEmailWithQR($email, $visitorName, $visitPurpose, $visitDate, $visitTime) {
        $this->email->from('testingntek@gmail.com','MSU - NAMS');
        $this->email->to($email);
        
        // Create the email message with the embedded QR code image
        $subject = "Your Visitor Request has been Approved";
        $this->email->subject($subject);
        $message = "
            <p>Dear $visitorName,</p>
            <p>We are pleased to inform you that your request to visit MSU - NAMS has been approved. Below are the details of your visit:</p>
            <ul>
                <li><strong>Visit Purpose:</strong> $visitPurpose</li>
                <li><strong>Visit Date:</strong> $visitDate</li>
                <li><strong>Visit Time:</strong> $visitTime</li>
            </ul>
            <p>If you have any questions or need further assistance, please do not hesitate to contact us.</p>
            <p>Thank you and we look forward to your visit!</p>
            <p>Best regards,</p>
            <p><strong>MSU - NAMS</strong></p>
        ";
        
        $this->email->message($message);
    
        if ($this->email->send()) {
            return true;
        } else {
            // You might want to log the error here
            log_message('error', 'Email sending failed: ' . $this->email->print_debugger());
            return false;
        }
    }

    public function declineVisitor($id){
        $id = intval($id);
        $admin_username = $this->session->userdata('admin')['username'];
        $password = $this->input->post('password');
        $reason = $this->input->post('reason');
        $visitor_data = $this->VisitorsPendingModel->get_by_id($id);

            
        // Verify the admin's password
        $is_verified = $this->AuthModel->verify_admin($admin_username, $password);
        
        if ($is_verified) {

            $email_sent = true;

            // Send Email with QR Code
            if($email_sent){
                // Convert visitor_data object to an associative array
                $visitor_array = (array) $visitor_data;

                // Remove the id from the array
                unset($visitor_array['rfid']); // Remove RFID from the data array
                unset($visitor_array['id']);    
                $visitor_array['updated_at'] = date('Y-m-d H:i:s');
                $visitor_array['status'] = 3;
                $visitor_array['decline_reason'] = $reason;
                $visitor_array['image'] = $this->uploadVisitorImage($visitor_array['visitor_image']);
                unset($visitor_array['visitor_image']);
                
                $this->VisitorsDeclineModel->insert($visitor_array);
                $this->VisitorsPendingModel->delete($id);
                echo json_encode(['status' => 200, 'message' => 'Visitor request declined successfully!']);
            }

            else{
                echo json_encode(['status' => 500, 'message' => 'Server Error, Failed to decline request.']);
            }

        }
    }

    private function send_decline_email($email, $visitorName, $visitPurpose, $visitDate, $visitTime) {
        $subject = "Your Visitor Request has been Declined";
        $message = "
            <p>Dear $visitorName,</p>
            <p>We regret to inform you that your request to visit MSU - NAMS has been declined. Below are the details of your requested visit:</p>
            <ul>
                <li><strong>Visit Purpose:</strong> $visitPurpose</li>
                <li><strong>Visit Date:</strong> $visitDate</li>
                <li><strong>Visit Time:</strong> $visitTime</li>
            </ul>
            <p>We apologize for any inconvenience this may cause. If you have any questions or need further assistance, please do not hesitate to contact us.</p>
            <p>Thank you for your understanding.</p>
            <p>Best regards,</p>
            <p>MSU - NAMS</p>
        ";
    
        // Use CodeIgniter's email library to send the email
        $this->email->from('your-email-here', 'MSU - NAMS');
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message);
    
        if ($this->email->send()) {
            return true;
        } else {
            // You might want to log the error here
            log_message('error', 'Email sending failed: ' . $this->email->print_debugger());
            return false;
        }
    }
    

    public function contact(){
        $name = 'Yoru';
        $email = 'test@gmail.com';
        $contact_num = '09121212';
        $company = 'NTEK';
        $message = 'This is a test email';
        // Send email
        $this->load->library('email');
        $this->email->from('your-email-here');
        $this->email->to('your-email-here');
        $this->email->subject('Contact Us Form Submission');
        $this->email->message(
            "<p><strong>Name:</strong> $name</p>" .
            "<p><strong>Email:</strong> $email</p>" .
            "<p><strong>Contact Number:</strong> $contact_num</p>" .
            "<p><strong>Company:</strong> $company</p>" .
            "<p><strong>Message:</strong>" . 
            "<br><br>".
            "$message</p>"
        );

        if ($this->email->send()) {
            // Email sent successfully
            echo 'Success';
         } else {
            // Email sending failed
               // Email sending failed, catch the error
             $error_message = $this->email->print_debugger(); // Get the error message
          echo ('Email sending failed: ' . $error_message); // Log the error message
         }
    }


/*****************************************VISITORS DECLINED SECTION**************************************************** */    

    public function uploadVisitorImage($visitorImage)
    {      
        // If the image string contains 'base64,' split it, otherwise just decode directly
        if (strpos($visitorImage, 'base64,') !== false) {
            $image_parts = explode(";base64,", $visitorImage);
            $image_base64 = base64_decode($image_parts[1]);
        } else {
            $image_base64 = base64_decode($visitorImage); // Decode the base64 image
        }
    
        // Set the default file type (e.g., jpg) or extract it if needed
        $file_type = 'jpg';
    
        // Generate a unique name for the image
        $fileName = uniqid() . '.' . $file_type;
    
        // Define the path to save the image
        $filePath = 'assets/images/uploads/visitors/' . $fileName;
    
        // Save the image to the specified path
        if (file_put_contents($filePath, $image_base64)) {
            // Return the path of the uploaded image
            return $filePath;
        } else {
            return false; // Handle the error if the file saving fails
        }
    }



    // Custom validation function
    public function valid_philippine_phone($phone) {
        // Check for 9xxxxxxxxxx format (11 digits)
        if (preg_match('/^9\d{9}$/', $phone)) {
            return TRUE;
        }
    
        $this->form_validation->set_message('valid_philippine_phone', 'The {field} field must contain a valid Philippine phone number.');
            return FALSE;
    
    }
    
        private function checkPreviousSteps($steps) {
            foreach ($steps as $step => $redirect_url) {
                if ($this->session->userdata($step . '_data') == null) {
                    $this->session->set_flashdata('danger', 'You must complete the previous step first.');
                    redirect($redirect_url);
                }
            }
    }

    public function validate_date_min_today($date) {
        $input_date = strtotime($date);
        $current_date = strtotime(date('Y-m-d'));
        $today = date('m-d-Y');

        if ($input_date < $current_date) {
            $this->form_validation->set_message('validate_date_min_today', 'The {field} value must be today\'s date or later.');
            return FALSE;
        }
        return TRUE;
    }

        /**
     * Custom callback function to validate RFID uniqueness across multiple tables
     *
     * @param string $rfid
     * @return bool
     */
    public function _validate_rfid($rfid)
    {        
        $user_id = $this->input->post('id');
        // Check if RFID is unique in the students table
        if ($this->StudentsModel->is_rfid_exists('students', $rfid, $user_id))
        {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a student.');
            return false;
        }
        
        // Check if RFID is unique in the faculty table
        if ($this->FacultyModel->is_rfid_exists('faculty', $rfid, $user_id))
        {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a faculty member.');
            return false;
        }
        
        // Check if RFID is unique in the staff table
        if ($this->StaffsModel->is_rfid_exists('staff', $rfid, $user_id))
        {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a staff member.');
            return false;
        }

        // Check if RFID is unique in the deivery services table
        if ($this->ResidentsModel->is_rfid_exists('residents', $rfid, $user_id))
        {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a residents member.');
            return false;
        }

        // Check if RFID is unique in the others table
        if ($this->GuestsModel->is_rfid_exists('guests', $rfid, $user_id))
        {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a guest member.');
            return false;
        }

        // Check if RFID is unique in the others table
        if ($this->VisitorsApproveModel->is_rfid_exists('visitors_active', $rfid, $user_id))
        {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for an active visitor.');
            return false;
        }
        
        // RFID is unique across all tables
        return true;
    }    
}