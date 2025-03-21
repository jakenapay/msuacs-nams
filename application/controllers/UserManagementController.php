<?php
defined('BASEPATH') or exit('No direct script access allowed');


class UserManagementController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('AuthModel');
        $this->load->model('StudentsModel');
        $this->load->model('FacultyModel');
        $this->load->model('StaffsModel');
        $this->load->model('GuestsModel');
        $this->load->model('ResidentsModel');
        $this->load->model('CollegesModel');
        $this->load->model('DepartmentsModel');
        $this->load->model('ProgramsModel');
        $this->load->model('OfficesModel');
        $this->load->model('LocationsModel');
        $this->load->model('AccessModel');
        $this->load->library('form_validation');

        //Check first if there is admin session data
        if ($this->session->userdata('admin') == null) {
            return redirect('admin/login');
        } else {
            //If session data exists, get admin id from session data and verify it with database
            $admin_id = $this->session->userdata('admin')['id'];
            $doesAdminExist = $this->AuthModel->verify_admin_id($admin_id);
            if (!$doesAdminExist) {
                $this->session->sess_destroy();
                return redirect('admin/login');
            }
        }
    }

    protected function checkPermission()
    {
        // Check if the admin has the required role before proceeding
        $required_role_id = 3; // ID of the required role
        if (!require_role($required_role_id)) {
            return; // Stop further execution if unauthorized
        }
    }


    public function index()
    {
        $this->checkPermission();
        $data['title'] = 'Students';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'students.js';

        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/user_management/students');
        $this->load->view('templates/dashboard_footer');
    }

    public function studentsList()
    {
        $draw = $this->input->get('draw');
        $start = $this->input->get('start');
        $length = $this->input->get('length');
        $search = $this->input->get('search')['value'];
        $orderColumn = $this->input->get('order')[0]['column'] + 1;
        $orderDir = $this->input->get('order')[0]['dir'];

        $data['locations'] = $this->LocationsModel->get_all();

        // Specify the columns to select, including the names from joined tables
        $columns = [
            'students.id',
            'students.image',
            'students.rfid',
            'students.id_number',
            'students.first_name',
            'students.last_name',
            'students.is_banned',
            'colleges.name as college_name',
            'departments.name as department_name',
            'programs.name as program_name',
        ];

        // Your custom logic to fetch data based on parameters
        $this->db->select(implode(',', $columns))
            ->from('students')
            ->join('colleges', 'colleges.name = students.college', 'left')
            ->join('departments', 'departments.name = students.department', 'left')
            ->join('programs', 'programs.name = students.program', 'left')
            ->group_start()
            ->like('students.id', $search)
            ->or_like('students.rfid', $search)
            ->or_like('students.id_number', $search)
            ->or_like('students.first_name', $search)
            ->or_like('students.last_name', $search)
            ->or_like('colleges.name', $search)
            ->or_like('departments.name', $search)
            ->or_like('programs.name', $search)
            ->group_end()
            ->order_by($orderColumn, $orderDir)
            ->limit($length, $start);

        // Initial sorting by the first column (ID) in descending order
        if ($draw === '1') {
            $this->db->order_by('students.id', 'desc');
        }

        $data = $this->db->get()->result_array();

        // Count total records without filtering
        $totalRecords = $this->db->count_all_results('students');

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

    public function addStudentView()
    {
        $this->checkPermission();
        $data['colleges'] = $this->CollegesModel->get_all();
        $this->load->view('modals/add_student', $data);
    }

    public function getDepartmentsByCollege($college_id)
    {
        $decodedCollegeName = urldecode($college_id);
        $departments = $this->DepartmentsModel->getByCollegeName($decodedCollegeName);
        echo json_encode($departments);
    }

    public function getProgramsByDepartment($department_id)
    {
        $decodedDepartmentName = urldecode($department_id);
        $programs = $this->ProgramsModel->getByDepartmentId($decodedDepartmentName);
        echo json_encode($programs);
    }

    public function addStudent()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]|min_length[2]|max_length[50]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'trim|regex_match[/^[a-zA-Z\s]*$/]|min_length[2]|max_length[50]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('id_number', 'ID Number', 'required|is_unique[students.id_number]');
        $this->form_validation->set_rules('college_id', 'College', 'required|trim');
        $this->form_validation->set_rules('department_id', 'Department', 'required|trim');
        $this->form_validation->set_rules('program_id', 'Program', 'required|trim');
        $this->form_validation->set_rules('enrollment_status', 'Enrollment Status', 'required|trim');
        $this->form_validation->set_rules('resident_status', 'Resident Status', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');

        if (empty($_FILES['image']['name']) && empty($_FILES['uploadInput']['name'])) {
            $this->form_validation->set_rules('image', 'image', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        } else {
            $id_number = $this->input->post('id_number');
            $college_id = trim($this->input->post('college_id'));
            $department_id = trim($this->input->post('department_id'));
            $program_id = trim($this->input->post('program_id'));

            $data = [
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'id_number' => $id_number,
                'college' => $college_id,
                'department' => $department_id,
                'program' => $program_id,
                'enrollment_status' => $this->input->post('enrollment_status'),
                'resident_status' => $this->input->post('resident_status'),
                'assigned_dormitory' => $this->input->post('assigned_dormitory') !== null ? $this->input->post('assigned_dormitory') : '',
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
                'rfid' => $this->input->post('rfid'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $collegeCode = $this->CollegesModel->getCollegeCode($college_id);
            $departmentCode = $this->DepartmentsModel->getDepartmentCode($department_id);
            $programCode = $this->ProgramsModel->getProgramCode($program_id);

            $uploadPath = setStudentUploadPath($collegeCode, $departmentCode, $programCode);

            // Configure upload settings
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048; // 2MB max
            $config['file_name'] = $id_number; // Unique filename

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
                    $data['image'] = $uploadPath . $upload_data['file_name'];
                }
            }


            if ($this->StudentsModel->insert($data)) {

                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'],
                    "Added new student record with RFID# " . $this->input->post('rfid'),
                    $this->session->userdata('admin')['username'],
                );

                echo json_encode([
                    'status' => 200,
                    'message' => 'Student added successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => 'Failed to add student record.'
                ]);
            }
        }
    }

    public function editStudentView($id)
    {
        $this->checkPermission();
        $data['student'] = $this->StudentsModel->get_by_id($id);
        $data['colleges'] = $this->CollegesModel->get_all();
        $data['departments'] = $this->DepartmentsModel->get_all();
        $data['programs'] = $this->ProgramsModel->get_all();
        $this->load->view('modals/edit_student', $data);
    }

    public function editStudent($id)
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'trim|regex_match[/^[a-zA-Z\s]*$/]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('id_number', 'ID Number', 'required|trim');
        $this->form_validation->set_rules('college_id', 'College', 'required|trim');
        $this->form_validation->set_rules('department_id', 'Department', 'required|trim');
        $this->form_validation->set_rules('program_id', 'Program', 'required|trim');
        $this->form_validation->set_rules('enrollment_status', 'Enrollment Status', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('resident_status', 'Resident Status', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        } else {
            $id_number = $this->input->post('id_number');
            $college_id = trim($this->input->post('college_id'));
            $department_id = trim($this->input->post('department_id'));
            $program_id = trim($this->input->post('program_id'));

            $data = [
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'id_number' => $id_number,
                'college' => $college_id,
                'department' => $department_id,
                'program' => $this->input->post('program_id'),
                'enrollment_status' => $this->input->post('enrollment_status'),
                'resident_status' => $this->input->post('resident_status'),
                'assigned_dormitory' => $this->input->post('assigned_dormitory') !== null ? $this->input->post('assigned_dormitory') : '',
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
                'rfid' => $this->input->post('rfid'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $collegeCode = $this->CollegesModel->getCollegeCode($college_id);
            $departmentCode = $this->DepartmentsModel->getDepartmentCode($department_id);
            $programCode = $this->ProgramsModel->getProgramCode($program_id);

            $uploadPath = setStudentUploadPath($collegeCode, $departmentCode, $programCode);

            // Configure upload settings
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048; // 2MB max
            $config['file_name'] = $id_number; // Unique filename

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
                    // Delete old image if exists
                    $old_image = $this->StudentsModel->getImage($id);
                    if ($old_image && file_exists('./' . $old_image)) {
                        unlink('./' . $old_image);
                    }

                    // Get upload data and set new image filename
                    $upload_data = $this->upload->data();
                    $data['image'] = $uploadPath . $upload_data['file_name'];
                }
            }

            $this->StudentsModel->update($id, $data);
            $rows = $this->db->affected_rows();

            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'],
                    "Edited student record with RFID# " . $this->input->post('rfid'),
                    $this->session->userdata('admin')['username'],
                );

                echo json_encode([
                    'status' => 200,
                    'message' => 'Student updated successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => ['Failed to update student record.']
                ]);
            }
        }
    }

    public function verifyBanStudent($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $adminReason = trim($this->input->post('reason'));
        $adminLocations = trim($this->input->post('locations'));
        $adminLocationNames = trim($this->input->post('locationNames'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        if (empty($adminReason)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please provide a reason for banning the student.'
            ]);
            return;
        } elseif (empty($adminLocations)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please select at least one location to ban the student.'
            ]);
            return;
        } elseif ($isPasswordCorrect) {
            $this->banStudent($id, $adminReason, $adminLocations, $adminLocationNames);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password. Please try again.'
            ]);
        }
    }

    private function banStudent($id, $reason = null, $locations = null, $locationNames = null)
    {
        $this->StudentsModel->banStudent($id, $reason, $locations);
        $rfid = $this->StudentsModel->getRFID($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Banned a student with RFID# " . $rfid . ($reason ? " for reason: " . $reason : "") . ($locationNames ? " in locations: " . $locationNames : ""),
                $this->session->userdata('admin')['username'],
            );
            echo json_encode([
                'status' => 200,
                'message' => 'Student successfully banned!'
            ]);
        } else {
            echo json_encode([
                'status' => 200,
                'message' => 'Failed to ban student!'
            ]);
        }
    }


    public function verifyUnbanStudent($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $adminReason = trim($this->input->post('reason'));
        $adminLocations = trim($this->input->post('locations'));
        $adminLocationNames = trim($this->input->post('locationNames'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        if (empty($adminReason)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please provide a reason for unbanning the student.'
            ]);
        } elseif ($isPasswordCorrect) {
            $this->unbanStudent($id, $adminReason, $adminLocations, $adminLocationNames);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password. Please try again.'
            ]);
        }
    }

    private function unbanStudent($id, $reason = null, $locations = null, $locationNames = null)
    {
        $this->StudentsModel->unbanStudent($id, $reason, $locations, $locationNames);
        $rfid = $this->StudentsModel->getRFID($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Unbanned a student with RFID# " . $rfid . ($reason ? " for reason: " . $reason : "") . ($locationNames ? " in locations: " . $locationNames : ""),
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Student successfully unbanned!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to ban student!'
            ]);
        }
    }

    public function getBannedLocationsStudents($id)
    {
        $response = $this->StudentsModel->getBannedLocations($id);

        if ($response) {
            // Check if response is an object
            $bannedLocations = array_map(function ($loc) {
                return [
                    'id' => $loc['id'],
                    'name' => $loc['name']
                ];
            }, $response);

            echo json_encode([
                'status' => 200,
                'data' => $bannedLocations // Output banned location's id and name as an array of objects
            ]);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Failed to retrieve student banned locations!'
            ]);
        }
    }

    public function getBannedLocationsFaculty($id)
    {
        $response = $this->FacultyModel->getBannedLocations($id);

        if ($response) {
            // Check if response is an object
            $bannedLocations = array_map(function ($loc) {
                return [
                    'id' => $loc['id'],
                    'name' => $loc['name']
                ];
            }, $response);

            echo json_encode([
                'status' => 200,
                'data' => $bannedLocations // Output banned location's id and name as an array of objects
            ]);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Failed to retrieve student banned locations!'
            ]);
        }
    }

    public function getBannedLocationsStaff($id)
    {
        $response = $this->StaffsModel->getBannedLocations($id);

        if ($response) {
            // Check if response is an object
            $bannedLocations = array_map(function ($loc) {
                return [
                    'id' => $loc['id'],
                    'name' => $loc['name']
                ];
            }, $response);

            echo json_encode([
                'status' => 200,
                'data' => $bannedLocations // Output banned location's id and name as an array of objects
            ]);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Failed to retrieve student banned locations!'
            ]);
        }
    }

    public function getBannedLocationsResidents($id)
    {
        $response = $this->ResidentsModel->getBannedLocations($id);

        if ($response) {
            // Check if response is an object
            $bannedLocations = array_map(function ($loc) {
                return [
                    'id' => $loc['id'],
                    'name' => $loc['name']
                ];
            }, $response);

            echo json_encode([
                'status' => 200,
                'data' => $bannedLocations // Output banned location's id and name as an array of objects
            ]);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Failed to retrieve student banned locations!'
            ]);
        }
    }



    public function verifyDeleteStudent($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        $response = $isPasswordCorrect;
        if ($response === true) {
            $this->deleteStudent($id);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password'
            ]);
        }
    }

    private function deleteStudent($id)
    {
        // Delete old image if exists
        $old_image = $this->StudentsModel->getImage($id);
        if ($old_image && file_exists('./' . $old_image)) {
            unlink('./' . $old_image);
        }

        $rfid = $this->StudentsModel->getRFID($id);
        $this->StudentsModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Deleted a student with RFID# " . $rfid,
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Student successfully deleted!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to delete student!'
            ]);
        }
    }

    public function faculty()
    {
        $this->checkPermission();
        $data['title'] = 'Faculty';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'faculty.js';

        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/user_management/faculty');
        $this->load->view('templates/dashboard_footer');
    }

    public function facultyList()
    {
        $draw = $this->input->get('draw');
        $start = $this->input->get('start');
        $length = $this->input->get('length');
        $search = $this->input->get('search')['value'];
        $orderColumn = $this->input->get('order')[0]['column'] + 1;
        $orderDir = $this->input->get('order')[0]['dir'];

        $data['locations'] = $this->LocationsModel->get_all();

        // Specify the columns to select, including the names from joined tables
        $columns = [
            'faculty.id',
            'faculty.image',
            'faculty.rfid',
            'faculty.id_number',
            'faculty.first_name',
            'faculty.last_name',
            'faculty.is_banned',
            'faculty.position',
            'faculty.college',
            'faculty.department'
        ];

        // Your custom logic to fetch data based on parameters
        $this->db->select(implode(',', $columns))
            ->from('faculty')
            ->join('colleges', 'colleges.name = faculty.college', 'left')
            ->join('departments', 'departments.name = faculty.department', 'left')
            ->group_start()
            ->like('faculty.id', $search)
            ->or_like('faculty.rfid', $search)
            ->or_like('faculty.id_number', $search)
            ->or_like('faculty.first_name', $search)
            ->or_like('faculty.last_name', $search)
            ->or_like('faculty.position', $search)
            ->or_like('faculty.college', $search)
            ->or_like('faculty.department', $search)
            ->group_end()
            ->order_by($orderColumn, $orderDir)
            ->limit($length, $start);

        // Initial sorting by the first column (ID) in descending order
        if ($draw === '1') {
            $this->db->order_by('faculty.id', 'desc');
        }

        $data = $this->db->get()->result_array();

        // Count total records without filtering
        $totalRecords = $this->db->count_all_results('faculty');

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

    public function addFacultyView()
    {
        $this->checkPermission();
        $data['colleges'] = $this->CollegesModel->get_all();
        $this->load->view('modals/add_faculty', $data);
    }

    // Assuming this function is within the same controller or accessible as a class method
    public function setFacultyUploadPath($collegeCode, $departmentCode)
    {
        // Base path for uploads
        $basePath = 'assets/images/uploads/';

        // Replace spaces with underscores and convert to proper case for directory names
        $collegeName = ucfirst(str_replace(' ', '_', $collegeCode));
        $departmentName = ucfirst(str_replace(' ', '_', $departmentCode));

        // Create the directory path: assets/images/uploads/collegeCode/departmentCode/
        $directoryPath = $basePath . $collegeName . '/' . $departmentName . '/';

        // Check if the directory exists, if not, create it with proper permissions
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        // Return the path where the directory was created
        return $directoryPath;
    }

    public function addFaculty()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]|min_length[2]|max_length[50]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'trim|regex_match[/^[a-zA-Z\s]*$/]|min_length[2]|max_length[50]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]|min_length[2]|max_length[50]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('id_number', 'ID Number', 'required|is_unique[students.id_number]');
        $this->form_validation->set_rules('college_id', 'College', 'required|trim');
        $this->form_validation->set_rules('department_id', 'Department', 'required|trim');
        $this->form_validation->set_rules('position', 'Position', 'required|trim');
        $this->form_validation->set_rules('employment_status', 'Employment Status', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('resident_status', 'Resident Status', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|max_length[10]');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');

        if (empty($_FILES['image']['name']) && empty($_FILES['uploadInput']['name'])) {
            $this->form_validation->set_rules('image', 'image', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        } else {
            $id_number = $this->input->post('id_number');
            $college_id = trim($this->input->post('college_id'));
            $department_id = trim($this->input->post('department_id'));

            $data = [
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'id_number' => $id_number,
                'college' => $college_id,
                'department' => $department_id,
                'position' => $this->input->post('position'),
                'employment_status' => $this->input->post('employment_status'),
                'resident_status' => $this->input->post('resident_status'),
                'assigned_dormitory' => $this->input->post('assigned_dormitory'),
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
                'rfid' => $this->input->post('rfid'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $collegeCode = $this->CollegesModel->getCollegeCode($college_id);
            $departmentCode = $this->DepartmentsModel->getDepartmentCode($department_id);

            $uploadPath = $this->setFacultyUploadPath($collegeCode, $departmentCode);

            // Configure upload settings
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048; // 2MB max
            $config['file_name'] = $id_number; // Unique filename

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
                    $data['image'] = $uploadPath . $upload_data['file_name'];
                }
            }


            if ($this->FacultyModel->insert($data)) {

                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'],
                    "Added new faculty record with RFID# " . $this->input->post('rfid'),
                    $this->session->userdata('admin')['username'],
                );

                echo json_encode([
                    'status' => 200,
                    'message' => 'Faculty added successfully!'
                ]);
            } else {

                echo json_encode([
                    'status' => 500,
                    'message' => 'Failed to add student faculty.',
                    'error_details' => $error, // Include error details for debugging
                ]);
            }
        }
    }

    public function editFacultyView($id)
    {
        $this->checkPermission();
        $data['faculty'] = $this->FacultyModel->get_by_id($id);
        $data['colleges'] = $this->CollegesModel->get_all();
        $data['departments'] = $this->DepartmentsModel->get_all();
        $this->load->view('modals/edit_faculty', $data);
    }

    public function editFaculty($id)
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'trim|regex_match[/^[a-zA-Z\s]*$/]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]|min_length[2]|max_length[50]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('id_number', 'ID Number', 'required|trim');
        $this->form_validation->set_rules('college_id', 'College', 'required|trim');
        $this->form_validation->set_rules('department_id', 'Department', 'required|trim');
        $this->form_validation->set_rules('position', 'Position', 'required|trim');
        $this->form_validation->set_rules('employment_status', 'Employment Status', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]', array('regex_match' => 'The %s field can only contain letters and spaces')); 
        $this->form_validation->set_rules('resident_status', 'Resident Status', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        } else {
            $id_number = $this->input->post('id_number');
            $college_id = trim($this->input->post('college_id'));
            $department_id = trim($this->input->post('department_id'));

            $data = [
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'id_number' => $id_number,
                'college' => $college_id,
                'department' => $department_id,
                'position' => $this->input->post('position'),
                'employment_status' => $this->input->post('employment_status'),
                'resident_status' => $this->input->post('resident_status'),
                'assigned_dormitory' => $this->input->post('assigned_dormitory'),
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
                'rfid' => $this->input->post('rfid'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $collegeCode = $this->CollegesModel->getCollegeCode($college_id);
            $departmentCode = $this->DepartmentsModel->getDepartmentCode($department_id);

            $uploadPath = $this->setFacultyUploadPath($collegeCode, $departmentCode);

            // Configure upload settings
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048; // 2MB max
            $config['file_name'] = $id_number; // Unique filename

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
                    // Delete old image if exists
                    $old_image = $this->FacultyModel->getImage($id);
                    if ($old_image && file_exists('./' . $old_image)) {
                        unlink('./' . $old_image);
                    }

                    // Get upload data and set new image filename
                    $upload_data = $this->upload->data();
                    $data['image'] = $uploadPath . $upload_data['file_name'];
                }
            }

            $this->FacultyModel->update($id, $data);
            $rows = $this->db->affected_rows();

            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'],
                    "Edited faculty record with RFID# " . $this->input->post('rfid'),
                    $this->session->userdata('admin')['username'],
                );

                echo json_encode([
                    'status' => 200,
                    'message' => 'Faculty updated successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => ['Failed to update faculty record.']
                ]);
            }
        }
    }

    public function verifyBanFaculty($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $adminReason = trim($this->input->post('reason'));
        $adminLocations = trim($this->input->post('locations'));
        $adminLocationNames = trim($this->input->post('locationNames'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        if (empty($adminReason)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please provide a reason for banning this faculty member.'
            ]);
        } elseif (empty($adminLocations)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please select at least one location to ban the faculty.'
            ]);
            return;
        } else if ($isPasswordCorrect) {
            $this->banFaculty($id, $adminReason, $adminLocations, $adminLocationNames);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password. Please try again'
            ]);
        }
    }

    private function banFaculty($id, $reason = null, $locations = null, $locationNames = null)
    {
        $this->FacultyModel->banFaculty($id, $reason, $locations);
        $rfid = $this->FacultyModel->getRFID($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Banned a faculty with RFID# " . $rfid . ($reason ? " for reason: " . $reason : "") . ($locationNames ? " in locations: " . $locationNames : ""),
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Faculty successfully banned!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to ban faculty!'
            ]);
        }
    }

    public function verifyUnbanFaculty($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $adminReason = trim($this->input->post('reason'));
        $adminLocations = trim($this->input->post('locations'));
        $adminLocationNames = trim($this->input->post('locationNames'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        if (empty($adminReason)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please provide a reason for unbanning this faculty member.'
            ]);
        } else if ($isPasswordCorrect) {
            $this->unbanFaculty($id, $adminReason, $adminLocations, $adminLocationNames);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password Please try again.'
            ]);
        }
    }

    private function unbanFaculty($id, $reason = null, $locations = null, $locationNames = null)
    {
        $this->FacultyModel->unbanFaculty($id, $reason, $locations, $locationNames);
        $rfid = $this->FacultyModel->getRFID($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Unbanned a faculty with RFID# " . $rfid . ($reason ? " for reason: " . $reason : "") . ($locationNames ? " in locations: " . $locationNames : ""),
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Faculty successfully unbanned!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to ban faculty!'
            ]);
        }
    }

    public function verifyDeleteFaculty($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        $response = $isPasswordCorrect;
        if ($response === true) {
            $this->deleteFaculty($id);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password'
            ]);
        }
    }

    private function deleteFaculty($id)
    {
        // Delete old image if exists
        $old_image = $this->FacultyModel->getImage($id);
        if ($old_image && file_exists('./' . $old_image)) {
            unlink('./' . $old_image);
        }

        $rfid = $this->FacultyModel->getRFID($id);
        $this->FacultyModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Deleted a faculty with RFID# " . $rfid,
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Faculty successfully deleted!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to delete faculty!'
            ]);
        }
    }

    public function staff()
    {
        $this->checkPermission();
        $data['title'] = 'Staff';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'staff.js';

        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/user_management/staff');
        $this->load->view('templates/dashboard_footer');
    }

    public function staffList()
    {
        $draw = $this->input->get('draw');
        $start = $this->input->get('start');
        $length = $this->input->get('length');
        $search = $this->input->get('search')['value'];
        $orderColumn = $this->input->get('order')[0]['column'] + 1;
        $orderDir = $this->input->get('order')[0]['dir'];

        $data['locations'] = $this->LocationsModel->get_all();

        // Specify the columns to select, including the names from joined tables
        $columns = [
            'staff.id',
            'staff.image',
            'staff.rfid',
            'staff.id_number',
            'staff.first_name',
            'staff.last_name',
            'staff.office',
            'staff.position',
            'staff.is_banned',
        ];

        // Your custom logic to fetch data based on parameters
        $this->db->select(implode(',', $columns))
            ->from('staff')
            ->group_start()
            ->like('staff.id', $search)
            ->or_like('staff.rfid', $search)
            ->or_like('staff.id_number', $search)
            ->or_like('staff.first_name', $search)
            ->or_like('staff.last_name', $search)
            ->or_like('staff.office', $search)
            ->or_like('staff.position', $search)
            ->group_end()
            ->order_by($orderColumn, $orderDir)
            ->limit($length, $start);

        // Initial sorting by the first column (ID) in descending order
        if ($draw === '1') {
            $this->db->order_by('staff.id', 'desc');
        }

        $data = $this->db->get()->result_array();

        // Count total records without filtering
        $totalRecords = $this->db->count_all_results('staff');

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

    public function addStaffView()
    {
        $this->checkPermission();
        $data['offices'] = $this->OfficesModel->get_all();
        $this->load->view('modals/add_staff', $data);
    }

    public function addStaff()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]|min_length[2]|max_length[50]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'trim|regex_match[/^[a-zA-Z\s]*$/]|min_length[2]|max_length[50]', array('regex_match' => 'The %s field can only contain letters and spaces')); 
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]|min_length[2]|max_length[50]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('id_number', 'ID Number', 'required|is_unique[staff.id_number]');
        $this->form_validation->set_rules('office', 'Office', 'required|trim');
        $this->form_validation->set_rules('position', 'Position', 'required|trim');
        $this->form_validation->set_rules('employment_status', 'Employment Status', 'required|trim');
        $this->form_validation->set_rules('working_hours', 'Shift/Working Hours', 'required|trim');
        $this->form_validation->set_rules('resident_status', 'Resident Status', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');

        if (empty($_FILES['image']['name']) && empty($_FILES['uploadInput']['name'])) {
            $this->form_validation->set_rules('image', 'image', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        } else {
            $id_number = $this->input->post('id_number');

            $data = [
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'id_number' => $id_number,
                'office' => $this->input->post('office'),
                'rfid' => $this->input->post('rfid'),
                'position' => $this->input->post('position'),
                'employment_status' => $this->input->post('employment_status'),
                'working_hours' => $this->input->post('working_hours'),
                'resident_status' => $this->input->post('resident_status'),
                'assigned_dormitory' => $this->input->post('assigned_dormitory'),
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Configure upload settings
            $config['upload_path'] = 'assets/images/uploads/staff/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048; // 2MB max
            $config['file_name'] = $id_number; // Unique filename

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
                    $data['image'] = 'assets/images/uploads/staff/' . $upload_data['file_name'];
                }
            }


            if ($this->StaffsModel->insert($data)) {

                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'],
                    "Added new staff record with RFID# " . $this->input->post('rfid'),
                    $this->session->userdata('admin')['username'],
                );

                echo json_encode([
                    'status' => 200,
                    'message' => 'Staff added successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => 'Failed to add staff.'
                ]);
            }
        }
    }

    public function editStaffView($id)
    {
        $this->checkPermission();
        $data['staff'] = $this->StaffsModel->get_by_id($id);
        $data['offices'] = $this->OfficesModel->get_all();
        $this->load->view('modals/edit_staff', $data);
    }

    public function editStaff($id)
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'required|trim');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('id_number', 'ID Number', 'required|trim');
        $this->form_validation->set_rules('office', 'Office', 'required|trim');
        $this->form_validation->set_rules('position', 'Position', 'required|trim');
        $this->form_validation->set_rules('employment_status', 'Employment Status', 'required|trim');
        $this->form_validation->set_rules('working_hours', 'Shift/Working Hours', 'required|trim');
        $this->form_validation->set_rules('resident_status', 'Resident Status', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        } else {
            $id_number = $this->input->post('id_number');

            $data = [
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'id_number' => $id_number,
                'office' => $this->input->post('office'),
                'rfid' => $this->input->post('rfid'),
                'position' => $this->input->post('position'),
                'employment_status' => $this->input->post('employment_status'),
                'working_hours' => $this->input->post('working_hours'),
                'resident_status' => $this->input->post('resident_status'),
                'assigned_dormitory' => $this->input->post('assigned_dormitory'),
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Configure upload settings
            $config['upload_path'] = 'assets/images/uploads/staff/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048; // 2MB max
            $config['file_name'] = $id_number; // Unique filename

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
                    // Delete old image if exists
                    $old_image = $this->StaffsModel->getImage($id);
                    if ($old_image && file_exists('./' . $old_image)) {
                        unlink('./' . $old_image);
                    }

                    // Get upload data and set new image filename
                    $upload_data = $this->upload->data();
                    $data['image'] = 'assets/images/uploads/staff/' . $upload_data['file_name'];
                }
            }

            $this->StaffsModel->update($id, $data);
            $rows = $this->db->affected_rows();

            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'],
                    "Edited staff record with RFID# " . $this->input->post('rfid'),
                    $this->session->userdata('admin')['username'],
                );

                echo json_encode([
                    'status' => 200,
                    'message' => 'Staff updated successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => ['Failed to update staff record.']
                ]);
            }
        }
    }

    public function verifyBanStaff($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $adminReason = trim($this->input->post('reason'));
        $adminLocations = trim($this->input->post('locations'));
        $adminLocationNames = trim($this->input->post('locationNames'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        if (empty($adminPassword)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please provide a reason for banning this staff member.'
            ]);
        } elseif (empty($adminLocations)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please select at least one location to ban the staff member.'
            ]);
            return;
        } else if ($isPasswordCorrect) {
            $this->banStaff($id, $adminReason, $adminLocations, $adminLocationNames);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password. Please try again.'
            ]);
        }
    }

    private function banStaff($id, $reason = null, $locations = null, $locationNames = null)
    {
        $this->StaffsModel->banStaff($id, $reason, $locations);
        $rfid = $this->StaffsModel->getRFID($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Banned a staff with RFID# " . $rfid . ($reason ? " for reason: " . $reason : "") . ($locationNames ? " in locations: " . $locationNames : ""),
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Staff successfully banned!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to ban staff!'
            ]);
        }
    }

    public function verifyUnbanStaff($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $adminReason = trim($this->input->post('reason'));
        $adminLocations = trim($this->input->post('locations'));
        $adminLocationNames = trim($this->input->post('locationNames'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        if (empty($adminReason)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please provide a reason for unbanning this staff member.'
            ]);
        } else if ($isPasswordCorrect) {
            $this->unbanStaff($id, $adminReason, $adminLocations, $adminLocationNames);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password'
            ]);
        }
    }

    private function unbanStaff($id, $reason = null, $locations = null, $locationNames = null)
    {
        $this->StaffsModel->unbanStaff($id, $reason, $locations, $locationNames);
        $rfid = $this->StaffsModel->getRFID($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Unbanned a staff with RFID# " . $rfid . ($reason ? " for reason: " . $reason : "") . ($locationNames ? " in locations: " . $locationNames : ""),
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Staff successfully banned!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to ban staff!'
            ]);
        }
    }

    public function verifyDeleteStaff($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        $response = $isPasswordCorrect;
        if ($response === true) {
            $this->deleteStaff($id);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password'
            ]);
        }
    }

    private function deleteStaff($id)
    {
        // Delete old image if exists
        $old_image = $this->StaffsModel->getImage($id);
        if ($old_image && file_exists('./' . $old_image)) {
            unlink('./' . $old_image);
        }

        $rfid = $this->StaffsModel->getRFID($id);
        $this->StaffsModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Deleted a staff with RFID# " . $rfid,
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Staff successfully deleted!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to delete staff!'
            ]);
        }
    }

    public function guests()
    {
        $this->checkPermission();
        $data['title'] = 'Guests';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'guest.js';

        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/user_management/guests');
        $this->load->view('templates/dashboard_footer');
    }

    public function guestsList()
    {
        $draw = $this->input->get('draw');
        $start = $this->input->get('start');
        $length = $this->input->get('length');
        $search = $this->input->get('search')['value'];
        $orderColumn = $this->input->get('order')[0]['column'] + 1;
        $orderDir = $this->input->get('order')[0]['dir'];

        // Specify the columns to select, including the names from joined tables
        $columns = [
            'guests.id',
            'guests.image',
            'guests.rfid',
            'guests.first_name',
            'guests.last_name',
            'guests.phone_number',
            'guests.assigned_dormitory',
            'guests.room_number',
            'guests.stay_purpose',
            'guests.check_in_date',
            'guests.check_out_date',
            'guests.is_banned',
        ];

        // Your custom logic to fetch data based on parameters
        $this->db->select(implode(',', $columns))
            ->from('guests')
            ->group_start()
            ->like('guests.id', $search)
            ->or_like('guests.rfid', $search)
            ->or_like('guests.first_name', $search)
            ->or_like('guests.last_name', $search)
            ->or_like('guests.phone_number', $search)
            ->or_like('guests.assigned_dormitory', $search)
            ->or_like('guests.room_number', $search)
            ->or_like('guests.stay_purpose', $search)
            ->or_like('guests.check_in_date', $search)
            ->or_like('guests.check_out_date', $search)
            ->group_end()
            ->order_by($orderColumn, $orderDir)
            ->limit($length, $start);

        // Initial sorting by the first column (ID) in descending order
        if ($draw === '1') {
            $this->db->order_by('guests.id', 'desc');
        }

        $data = $this->db->get()->result_array();

        // Count total records without filtering
        $totalRecords = $this->db->count_all_results('guests');

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

    public function addGuestView()
    {
        $this->checkPermission();
        $this->load->view('modals/add_guest');
    }

    public function addGuest()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]|max_length[50]|min_length[2]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'trim|regex_match[/^[a-zA-Z\s]*$/]|max_length[50]|min_length[2]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]|min_length[2]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required|trim|is_unique[visitors_pending.phone_number]|callback_valid_philippine_phone', array(
            'is_unique' => 'This %s is already used in a pending visit request.'
        ));
        // $this->form_validation->set_rules('company', 'Company', 'max_length[50]|trim|min_length[3]');
        // $this->form_validation->set_rules('id_type', 'ID Type', 'required|max_length[50]|trim');

        // $this->form_validation->set_rules('id_number', 'ID Number', 'required|max_length[50]|trim|min_length[6]');
        // $this->form_validation->set_rules('id_front_base64', 'Photo of ID (front)', 'required|trim');
        // $this->form_validation->set_rules('id_back_base64', 'Photo of ID (back)', 'required|trim');

        $this->form_validation->set_rules('stay_purpose', 'Purpose of Stay', 'required|trim');
        $this->form_validation->set_rules('check_in_date', 'Check-In Date & Time', 'required|trim');
        $this->form_validation->set_rules('check_out_date', 'Estimated Check-Out Date', 'required|trim');
        $this->form_validation->set_rules('assigned_dormitory', 'Dorm/Building Name', 'required|trim');
        $this->form_validation->set_rules('room_number', 'Room Number', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');

        if (empty($_FILES['image']['name']) && empty($_FILES['uploadInput']['name'])) {
            $this->form_validation->set_rules('image', 'image', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        } else {

            $data = [
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'phone_number' => $this->input->post('phone_number'),
                // 'company' => $this->input->post('company'),
                // 'id_type' => $this->input->post('id_type'),
                // 'id_number' => $this->input->post('id_number'),
                // 'id_front' => $this->input->post('id_front_base64'),
                // 'id_back' => $this->input->post('id_back_base64'),
                'stay_purpose' => $this->input->post('stay_purpose'),
                'check_in_date' => $this->input->post('check_in_date'),
                'check_out_date' => $this->input->post('check_out_date'),
                'assigned_dormitory' => $this->input->post('assigned_dormitory'),
                'room_number' => $this->input->post('room_number'),
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
                'rfid' => $this->input->post('rfid'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Configure upload settings
            $config['upload_path'] = 'assets/images/uploads/guests/';
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
                    $data['image'] = 'assets/images/uploads/guests/' . $upload_data['file_name'];
                }
            }


            if ($this->GuestsModel->insert($data)) {

                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'],
                    "Added new guest record with RFID# " . $this->input->post('rfid'),
                    $this->session->userdata('admin')['username'],
                );

                echo json_encode([
                    'status' => 200,
                    'message' => 'Guest added successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => 'Failed to add guest.'
                ]);
            }
        }
    }

    public function editGuestView($id)
    {
        // Ensure the id is sanitized and valid
        $id = intval($id);
        $guest = $this->db->get_where('guests', ['id' => $id])->row_array();

        // Check if visitor data was retrieved
        if ($guest) {
            $this->load->view('modals/edit_guest', ['guest' => $guest]);
        } else {
            // Handle the case where visitor data is not found
            echo "Guest not found.";
        }
    }

    public function editGuest($id)
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|max_length[50]|min_length[2]');
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'max_length[50]|min_length[2]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required|trim|is_unique[visitors_pending.phone_number]|callback_valid_philippine_phone', array(
            'is_unique' => 'This %s is already used in a pending visit request.'
        ));
        // $this->form_validation->set_rules('company', 'Company', 'max_length[50]|trim|min_length[3]');
        // $this->form_validation->set_rules('id_type', 'ID Type', 'required|max_length[50]|trim');

        // $this->form_validation->set_rules('id_number', 'ID Number', 'required|max_length[50]|trim|min_length[6]');

        $this->form_validation->set_rules('stay_purpose', 'Purpose of Stay', 'required|trim');
        $this->form_validation->set_rules('check_in_date', 'Check-In Date & Time', 'required|trim');
        $this->form_validation->set_rules('check_out_date', 'Estimated Check-Out Date', 'required|trim');
        $this->form_validation->set_rules('assigned_dormitory', 'Dorm/Building Name', 'required|trim');
        $this->form_validation->set_rules('room_number', 'Room Number', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        } else {

            $data = [
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'phone_number' => $this->input->post('phone_number'),
                // 'company' => $this->input->post('company'),
                // 'id_type' => $this->input->post('id_type'),
                // 'id_number' => $this->input->post('id_number'),
                'stay_purpose' => $this->input->post('stay_purpose'),
                'check_in_date' => $this->input->post('check_in_date'),
                'check_out_date' => $this->input->post('check_out_date'),
                'assigned_dormitory' => $this->input->post('assigned_dormitory'),
                'room_number' => $this->input->post('room_number'),
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
                'rfid' => $this->input->post('rfid'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Configure upload settings
            $config['upload_path'] = 'assets/images/uploads/guests/';
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
                    // Delete old image if exists
                    $old_image = $this->GuestsModel->getImage($id);
                    if ($old_image && file_exists('./' . $old_image)) {
                        unlink('./' . $old_image);
                    }

                    // Get upload data and set new image filename
                    $upload_data = $this->upload->data();
                    $data['image'] = 'assets/images/uploads/guests/' . $upload_data['file_name'];
                }
            }

            $this->GuestsModel->update($id, $data);
            $rows = $this->db->affected_rows();

            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'],
                    "Edited guest record with RFID# " . $this->input->post('rfid'),
                    $this->session->userdata('admin')['username'],
                );

                echo json_encode([
                    'status' => 200,
                    'message' => 'Guest updated successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => ['Failed to update guest record.']
                ]);
            }
        }
    }

    public function verifyBanGuest($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        $response = $isPasswordCorrect;
        if ($response === true) {
            $this->banGuest($id);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password'
            ]);
        }
    }

    private function banGuest($id)
    {
        $this->GuestsModel->banGuest($id);
        $rfid = $this->GuestsModel->getRFID($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Banned a guest with RFID# " . $rfid,
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Guest successfully banned!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to ban guest!'
            ]);
        }
    }

    public function verifyUnbanGuest($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        $response = $isPasswordCorrect;

        if ($response === true) {
            $this->unbanGuest($id);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password'
            ]);
        }
    }

    private function unbanGuest($id)
    {
        $this->GuestsModel->unbanGuest($id);
        $rfid = $this->GuestsModel->getRFID($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Unbanned a guest with RFID# " . $rfid,
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Guest successfully banned!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to ban guest!'
            ]);
        }
    }

    public function verifyDeleteGuest($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        $response = $isPasswordCorrect;
        if ($response === true) {
            $this->deleteGuest($id);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password'
            ]);
        }
    }

    private function deleteGuest($id)
    {
        // Delete old image if exists
        $old_image = $this->GuestsModel->getImage($id);
        if ($old_image && file_exists('./' . $old_image)) {
            unlink('./' . $old_image);
        }

        $rfid = $this->GuestsModel->getRFID($id);
        $this->GuestsModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Deleted a guest with RFID# " . $rfid,
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Guest successfully deleted!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to delete guest!'
            ]);
        }
    }

    public function residents()
    {
        $this->checkPermission();
        $data['title'] = 'Residents';
        $data['account'] = $this->AuthModel->getAdmin($this->session->userdata('admin')['username']);
        $data['js'] = 'resident.js';

        $this->load->view('templates/dashboard_header');
        $this->load->view('templates/dashboard_sidebar');
        $this->load->view('templates/dashboard_topbar', $data);
        $this->load->view('admin/user_management/residents');
        $this->load->view('templates/dashboard_footer');
    }

    public function residentsList()
    {
        $draw = $this->input->get('draw');
        $start = $this->input->get('start');
        $length = $this->input->get('length');
        $search = $this->input->get('search')['value'];
        $orderColumn = $this->input->get('order')[0]['column'] + 1;
        $orderDir = $this->input->get('order')[0]['dir'];

        $data['locations'] = $this->LocationsModel->get_all();

        // Specify the columns to select, including the names from joined tables
        $columns = [
            'residents.id',
            'residents.image',
            'residents.rfid',
            'residents.first_name',
            'residents.last_name',
            'residents.dormitory',
            'residents.move_in_date',
            'residents.is_banned',
        ];

        // Your custom logic to fetch data based on parameters
        $this->db->select(implode(',', $columns))
            ->from('residents')
            ->group_start()
            ->like('residents.id', $search)
            ->or_like('residents.rfid', $search)
            ->or_like('residents.first_name', $search)
            ->or_like('residents.last_name', $search)
            ->or_like('residents.dormitory', $search)
            ->or_like('residents.move_in_date', $search)
            ->group_end()
            ->order_by($orderColumn, $orderDir)
            ->limit($length, $start);

        // Initial sorting by the first column (ID) in descending order
        if ($draw === '1') {
            $this->db->order_by('residents.id', 'desc');
        }

        $data = $this->db->get()->result_array();

        // Count total records without filtering
        $totalRecords = $this->db->count_all_results('residents');

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

    public function addResidentView()
    {
        $this->checkPermission();
        $this->load->view('modals/add_resident');
    }

    public function addResident()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]|max_length[50]|min_length[3]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'trim|regex_match[/^[a-zA-Z\s]*$/]|max_length[50]|min_length[3]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|regex_match[/^[a-zA-Z\s]+$/]|min_length[2]', array('regex_match' => 'The %s field can only contain letters and spaces'));
        $this->form_validation->set_rules('dormitory', 'Dormitory', 'required|trim');
        $this->form_validation->set_rules('move_in_date', 'Move In Date', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');

        if (empty($_FILES['image']['name']) && empty($_FILES['uploadInput']['name'])) {
            $this->form_validation->set_rules('image', 'image', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        } else {

            $data = [
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'move_in_date' => $this->input->post('move_in_date'),
                'dormitory' => $this->input->post('dormitory'),
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
                'rfid' => $this->input->post('rfid'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Configure upload settings
            $config['upload_path'] = 'assets/images/uploads/residents/';
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
                    $data['image'] = 'assets/images/uploads/residents/' . $upload_data['file_name'];
                }
            }


            if ($this->ResidentsModel->insert($data)) {

                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'],
                    "Added new resident record with RFID# " . $this->input->post('rfid'),
                    $this->session->userdata('admin')['username'],
                );

                echo json_encode([
                    'status' => 200,
                    'message' => 'Resident added successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => 'Failed to add resident.'
                ]);
            }
        }
    }

    public function editResidentView($id)
    {
        // Ensure the id is sanitized and valid
        $id = intval($id);
        $resident = $this->db->get_where('residents', ['id' => $id])->row_array();

        // Check if visitor data was retrieved
        if ($resident) {
            $this->load->view('modals/edit_resident', ['resident' => $resident]);
        } else {
            // Handle the case where visitor data is not found
            echo "Resident not found.";
        }
    }

    public function editResident($id)
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|max_length[50]|min_length[3]');
        $this->form_validation->set_rules('middle_name', 'Middle Name', 'max_length[50]|min_length[3]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]');
        $this->form_validation->set_rules('dormitory', 'Dormitory', 'required|trim');
        $this->form_validation->set_rules('move_in_date', 'Move In Date', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_person', 'Emergency Contact Person', 'required|trim');
        $this->form_validation->set_rules('emergency_contact_number', 'Emergency Contact Number', 'required|trim|callback_valid_philippine_phone|max_length[10]');
        $this->form_validation->set_rules('rfid', 'RFID', 'required|integer|callback__validate_rfid');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 400,
                'message' => $this->form_validation->error_array()
            ]);
        } else {

            $data = [
                'first_name' => $this->input->post('first_name'),
                'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'move_in_date' => $this->input->post('move_in_date'),
                'dormitory' => $this->input->post('dormitory'),
                'emergency_contact_person' => $this->input->post('emergency_contact_person'),
                'emergency_contact_number' => $this->input->post('emergency_contact_number'),
                'rfid' => $this->input->post('rfid'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Configure upload settings
            $config['upload_path'] = 'assets/images/uploads/residents/';
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
                    // Delete old image if exists
                    $old_image = $this->ResidentsModel->getImage($id);
                    if ($old_image && file_exists('./' . $old_image)) {
                        unlink('./' . $old_image);
                    }

                    // Get upload data and set new image filename
                    $upload_data = $this->upload->data();
                    $data['image'] = 'assets/images/uploads/residents/' . $upload_data['file_name'];
                }
            }

            $this->ResidentsModel->update($id, $data);
            $rows = $this->db->affected_rows();

            if ($rows > 0) {
                $this->AuthModel->insertSecurity(
                    $this->session->userdata('admin')['id'],
                    "Edited resident record with RFID# " . $this->input->post('rfid'),
                    $this->session->userdata('admin')['username'],
                );

                echo json_encode([
                    'status' => 200,
                    'message' => 'Resident updated successfully!'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => ['Failed to update resident record.']
                ]);
            }
        }
    }

    public function verifyBanResident($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $adminReason = trim($this->input->post('reason'));
        $adminLocations = trim($this->input->post('locations'));
        $adminLocationNames = trim($this->input->post('locationNames'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        if (empty($adminReason)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please provide a reason for banning this resident.'
            ]);
        } else if (empty($adminLocations)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please select at least one location to ban the resident.'
            ]);
        } else if ($isPasswordCorrect) {
            $this->banResident($id, $adminReason, $adminLocations, $adminLocationNames);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password'
            ]);
        }
    }

    private function banResident($id, $reason = null, $locations = null, $locationNames = null)
    {
        $this->ResidentsModel->banResident($id, $reason, $locations);
        $rfid = $this->ResidentsModel->getRFID($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Banned a resident with RFID# " . $rfid . ($reason ? " for reason: " . $reason : "") . ($locationNames ? " in locations: " . $locationNames : ""),
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Resident successfully banned!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to ban resident!'
            ]);
        }
    }

    public function verifyUnbanResident($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $adminReason = trim($this->input->post('reason'));
        $adminLocations = trim($this->input->post('locations'));
        $adminLocationNames = trim($this->input->post('locationNames'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);


        if (empty($adminReason)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Please provide a reason.'
            ]);
        } else if ($isPasswordCorrect) {
            $this->unbanResident($id, $adminReason, $adminLocations, $adminLocationNames);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password'
            ]);
        }
    }

    private function unbanResident($id, $reason = null, $locations = null, $locationNames = null)
    {
        $this->ResidentsModel->unbanResident($id, $reason, $locations, $locationNames);
        $rfid = $this->ResidentsModel->getRFID($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Unbanned a residents with RFID# " . $rfid . ($reason ? " for reason: " . $reason : "") . ($locationNames ? " in locations: " . $locationNames : ""),
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Residents successfully banned!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to ban resident!'
            ]);
        }
    }

    public function verifyDeleteResident($id)
    {
        $adminPassword = trim($this->input->post('password'));
        $old_pass = $this->AuthModel->getPassword($this->session->userdata('admin')['username']);

        // Verify the password
        $isPasswordCorrect = password_verify($adminPassword, $old_pass);

        $response = $isPasswordCorrect;
        if ($response === true) {
            $this->deleteResident($id);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Invalid password'
            ]);
        }
    }

    private function deleteResident($id)
    {
        // Delete old image if exists
        $old_image = $this->ResidentsModel->getImage($id);
        if ($old_image && file_exists('./' . $old_image)) {
            unlink('./' . $old_image);
        }

        $rfid = $this->ResidentsModel->getRFID($id);
        $this->ResidentsModel->delete($id);
        $rows = $this->db->affected_rows();

        if ($rows > 0) {
            $this->AuthModel->insertSecurity(
                $this->session->userdata('admin')['id'],
                "Deleted a resident with RFID# " . $rfid,
                $this->session->userdata('admin')['username'],
            );

            echo json_encode([
                'status' => 200,
                'message' => 'Resident successfully deleted!'
            ]);
        } else {

            echo json_encode([
                'status' => 200,
                'message' => 'Failed to delete resident!'
            ]);
        }
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
        if ($this->StudentsModel->is_rfid_exists('students', $rfid, $user_id)) {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a student.');
            return false;
        }

        // Check if RFID is unique in the faculty table
        if ($this->FacultyModel->is_rfid_exists('faculty', $rfid, $user_id)) {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a faculty member.');
            return false;
        }

        // Check if RFID is unique in the staff table
        if ($this->StaffsModel->is_rfid_exists('staff', $rfid, $user_id)) {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a staff member.');
            return false;
        }

        // Check if RFID is unique in the Visiting Officer table
        if ($this->ResidentsModel->is_rfid_exists('residents', $rfid, $user_id)) {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a resident member.');
            return false;
        }

        // Check if RFID is unique in the contractor table
        if ($this->GuestsModel->is_rfid_exists('guests', $rfid, $user_id)) {
            $this->form_validation->set_message('_validate_rfid', 'The RFID is already registered for a guest member.');
            return false;
        }

        // RFID is unique across all tables
        return true;
    }
}
