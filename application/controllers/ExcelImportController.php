<?php

defined ('BASEPATH') or exit ('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('ExcelImportModel');
        $this->load->library('form_validation');
    }

    public function index() 
    {
        redirect(base_url());
    }

    public function studentExcelImportForm(){
        $this->load->view('modals/excel_import_form');
    }

    public function importStudent() {
        $config['upload_path'] = 'assets/file_uploads/student_files/';
        $config['allowed_types'] = 'xlsx|xls|csv';
        $config['remove_spaces'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('uploadFile')) {
            $response = [
                'status' => 400,
                'message' => $this->upload->display_errors()
            ];
        } else {
            $data = $this->upload->data();
            $inputFileName = $config['upload_path'] . $data['file_name'];

            try {
                $spreadsheet = IOFactory::load($inputFileName);
                $worksheet = $spreadsheet->getActiveSheet();
                $allDataInSheet = $worksheet->toArray(null, true, true, true);

            // Validate columns
            $requiredColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
            $columnNames = [
                'A' => 'First Name', 'B' => 'Middle Name', 'C' => 'Last Name',
                'D' => 'ID Number', 'E' => 'College', 'F' => 'Department',
                'G' => 'Program', 'H' => 'RFID'
            ];

            // Get the columns present in the file
            $presentColumns = array_keys($allDataInSheet[1]);

            // Check for missing required columns
            $missingColumns = array_diff($requiredColumns, $presentColumns);

            if (!empty($missingColumns)) {
                $missingColumnsNames = array_map(function ($col) use ($columnNames) {
                    return $columnNames[$col];
                }, $missingColumns);

                $response = [
                    'status' => 400,
                    'message' => 'Import error! Missing columns: ' . implode(', ', $missingColumnsNames)
                ];
            } else {
                // All required columns are present, proceed with import
                $inserdata = [];
                $flag = true;
                foreach ($allDataInSheet as $value) {
                    if ($flag) {
                        $flag = false;
                        continue;
                    }

                    $notEmptyValues = array_filter($value, function($v) {
                        return $v !== null && $v !== '';
                    });
                    
                    if (!empty($notEmptyValues)) {
                        $inserdata[] = [
                            'first_name' => $value['A'],
                            'middle_name' => $value['B'],
                            'last_name' => $value['C'],
                            'id_number' => $value['D'],
                            'college' => $value['E'],
                            'department' => $value['F'],
                            'program' => $value['G'],
                            'rfid' => $value['H']
                        ];
                    }
                }

                $result = $this->ExcelImportModel->importData($inserdata);

                if ($result) {
                    $response = [
                        'status' => 200,
                        'message' => 'Student File Uploaded Successfully!'
                    ];
                } else {
                    $response = [
                        'status' => 400,
                        'message' => 'Student File Upload Failed!'
                    ];
                }
            }

            } catch (Exception $e) {
                $response = [
                    'status' => 500,
                    'message' => 'Error loading file: ' . $e->getMessage()
                ];
            }

            // Delete the uploaded file after processing
            unlink($inputFileName);
        }

        echo json_encode($response);
    }

    public function facultyExcelImportForm(){
        $this->load->view('modals/excel_import_faculty');
    }


    public function importFaculty(){
        $config['upload_path'] = 'assets/file_uploads/faculty_files/';
        $config['allowed_types'] = 'xlsx|xls|csv';
        $config['remove_spaces'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('uploadFile')) {
            $response = [
                'status' => 400,
                'message' => $this->upload->display_errors()
            ];
        } else {
            $data = $this->upload->data();
            $inputFileName = $config['upload_path'] . $data['file_name'];

            try {
                $spreadsheet = IOFactory::load($inputFileName);
                $worksheet = $spreadsheet->getActiveSheet();
                $allDataInSheet = $worksheet->toArray(null, true, true, true);

            // Validate columns
            $requiredColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
            $columnNames = [
                'A' => 'First Name', 'B' => 'Middle Name', 'C' => 'Last Name',
                'D' => 'ID Number', 'E' => 'Position', 'F' => 'College','G' => 'Department', 'H' => 'RFID'
            ];

            // Get the columns present in the file
            $presentColumns = array_keys($allDataInSheet[1]);

            // Check for missing required columns
            $missingColumns = array_diff($requiredColumns, $presentColumns);

            if (!empty($missingColumns)) {
                $missingColumnsNames = array_map(function ($col) use ($columnNames) {
                    return $columnNames[$col];
                }, $missingColumns);

                $response = [
                    'status' => 400,
                    'message' => 'Import error! Missing columns: ' . implode(', ', $missingColumnsNames)
                ];
            } else {
                // All required columns are present, proceed with import
                $inserdata = [];
                $flag = true;
                foreach ($allDataInSheet as $value) {
                    if ($flag) {
                        $flag = false;
                        continue;
                    }

                    $notEmptyValues = array_filter($value, function($v) {
                        return $v !== null && $v !== '';
                    });
                    
                    if (!empty($notEmptyValues)) {
                        $inserdata[] = [
                            'first_name' => $value['A'],
                            'middle_name' => $value['B'],
                            'last_name' => $value['C'],
                            'id_number' => $value['D'],
                            'position' => $value['E'],
                            'college' => $value['F'],
                            'department' => $value['G'],
                            'rfid' => $value['H'],
                        ];
                    }
                }

                $result = $this->ExcelImportModel->importFacultyData($inserdata);

                if ($result) {
                    $response = [
                        'status' => 200,
                        'message' => 'Faculty File Uploaded Successfully!'
                    ];
                } else {
                    $response = [
                        'status' => 400,
                        'message' => 'Faculty File Upload Failed!'
                    ];
                }
            }

            } catch (Exception $e) {
                $response = [
                    'status' => 500,
                    'message' => 'Error loading file: ' . $e->getMessage()
                ];
            }

            // Delete the uploaded file after processing
            unlink($inputFileName);
        }

        echo json_encode($response);    
    }

    public function staffExcelImportForm(){
        $this->load->view('modals/excel_import_staff');
    }

    public function importStaff(){
        $config['upload_path'] = 'assets/file_uploads/staff_files/';
        $config['allowed_types'] = 'xlsx|xls|csv';
        $config['remove_spaces'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('uploadFile')) {
            $response = [
                'status' => 400,
                'message' => $this->upload->display_errors()
            ];
        } else {
            $data = $this->upload->data();
            $inputFileName = $config['upload_path'] . $data['file_name'];

            try {
                $spreadsheet = IOFactory::load($inputFileName);
                $worksheet = $spreadsheet->getActiveSheet();
                $allDataInSheet = $worksheet->toArray(null, true, true, true);

            // Validate columns
            $requiredColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
            $columnNames = [
                'A' => 'First Name', 'B' => 'Middle Name', 'C' => 'Last Name',
                'D' => 'ID Number', 'E' => 'Position', 'F' => 'Office', 
                'G' => 'RFID'
            ];

            // Get the columns present in the file
            $presentColumns = array_keys($allDataInSheet[1]);

            // Check for missing required columns
            $missingColumns = array_diff($requiredColumns, $presentColumns);

            if (!empty($missingColumns)) {
                $missingColumnsNames = array_map(function ($col) use ($columnNames) {
                    return $columnNames[$col];
                }, $missingColumns);

                $response = [
                    'status' => 400,
                    'message' => 'Import error! Missing columns: ' . implode(', ', $missingColumnsNames)
                ];
            } else {
                // All required columns are present, proceed with import
                $inserdata = [];
                $flag = true;
                foreach ($allDataInSheet as $value) {
                    if ($flag) {
                        $flag = false;
                        continue;
                    }

                    $notEmptyValues = array_filter($value, function($v) {
                        return $v !== null && $v !== '';
                    });
                    
                    if (!empty($notEmptyValues)) {
                        $inserdata[] = [
                            'first_name' => $value['A'],
                            'middle_name' => $value['B'],
                            'last_name' => $value['C'],
                            'id_number' => $value['D'],
                            'position' => $value['E'],
                            'office' => $value['F'],
                            'rfid' => $value['G']
                        ];
                    }
                }

                $result = $this->ExcelImportModel->importStaffData($inserdata);

                if ($result) {
                    $response = [
                        'status' => 200,
                        'message' => 'Staff File Uploaded Successfully!'
                    ];
                } else {
                    $response = [
                        'status' => 400,
                        'message' => 'Staff File Upload Failed!'
                    ];
                }
            }

            } catch (Exception $e) {
                $response = [
                    'status' => 500,
                    'message' => 'Error loading file: ' . $e->getMessage()
                ];
            }

            // Delete the uploaded file after processing
            unlink($inputFileName);
        }

        echo json_encode($response);    
    }

    public function residentExcelImportForm(){
        $this->load->view('modals/excel_import_resident');
    }

    public function importResident(){
        $config['upload_path'] = 'assets/file_uploads/residents_files/';
        $config['allowed_types'] = 'xlsx|xls|csv';
        $config['remove_spaces'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('uploadFile')) {
            $response = [
                'status' => 400,
                'message' => $this->upload->display_errors()
            ];
        } else {
            $data = $this->upload->data();
            $inputFileName = $config['upload_path'] . $data['file_name'];

            try {
                $spreadsheet = IOFactory::load($inputFileName);
                $worksheet = $spreadsheet->getActiveSheet();
                $allDataInSheet = $worksheet->toArray(null, true, true, true);

            // Validate columns
            $requiredColumns = ['A', 'B', 'C', 'D', 'E', 'F'];
            $columnNames = [
                'A' => 'First Name', 'B' => 'Middle Name', 'C' => 'Last Name',
                'D' => 'Dormitory/Residence Name', 'E' => 'Move-In Date', 'F' => 'RFID',
            ];

            // Get the columns present in the file
            $presentColumns = array_keys($allDataInSheet[1]);

            // Check for missing required columns
            $missingColumns = array_diff($requiredColumns, $presentColumns);

            if (!empty($missingColumns)) {
                $missingColumnsNames = array_map(function ($col) use ($columnNames) {
                    return $columnNames[$col];
                }, $missingColumns);

                $response = [
                    'status' => 400,
                    'message' => 'Import error! Missing columns: ' . implode(', ', $missingColumnsNames)
                ];
            } else {
                // All required columns are present, proceed with import
                $inserdata = [];
                $flag = true;
                foreach ($allDataInSheet as $value) {
                    if ($flag) {
                        $flag = false;
                        continue;
                    }

                    $notEmptyValues = array_filter($value, function($v) {
                        return $v !== null && $v !== '';
                    });
                    
                    if (!empty($notEmptyValues)) {
                        $inserdata[] = [
                            'first_name' => $value['A'],
                            'middle_name' => $value['B'],
                            'last_name' => $value['C'],
                            'dormitory' => $value['D'],
                            'move_in_date' => $value['E'],
                            'rfid' => $value['F'],
                        ];
                    }
                }

                $result = $this->ExcelImportModel->importResidentData($inserdata);

                if ($result) {
                    $response = [
                        'status' => 200,
                        'message' => 'Resident File Uploaded Successfully!'
                    ];
                } else {
                    $response = [
                        'status' => 400,
                        'message' => 'Resident File Upload Failed!'
                    ];
                }
            }

            } catch (Exception $e) {
                $response = [
                    'status' => 500,
                    'message' => 'Error loading file: ' . $e->getMessage()
                ];
            }

            // Delete the uploaded file after processing
            unlink($inputFileName);
        }

        echo json_encode($response);    
    }

    public function guestExcelImportForm(){
        $this->load->view('modals/excel_import_guest');
    }

    public function importGuest(){
        $config['upload_path'] = 'assets/file_uploads/guests_files/';
        $config['allowed_types'] = 'xlsx|xls|csv';
        $config['remove_spaces'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('uploadFile')) {
            $response = [
                'status' => 400,
                'message' => $this->upload->display_errors()
            ];
        } else {
            $data = $this->upload->data();
            $inputFileName = $config['upload_path'] . $data['file_name'];

            try {
                $spreadsheet = IOFactory::load($inputFileName);
                $worksheet = $spreadsheet->getActiveSheet();
                $allDataInSheet = $worksheet->toArray(null, true, true, true);

            // Validate columns
            $requiredColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
            $columnNames = [
                'A' => 'First Name', 'B' => 'Middle Name', 'C' => 'Last Name',
                'D' => 'ID Number', 'E' => 'Position', 'F' => 'Office', 
                'G' => 'RFID'
            ];

            // Get the columns present in the file
            $presentColumns = array_keys($allDataInSheet[1]);

            // Check for missing required columns
            $missingColumns = array_diff($requiredColumns, $presentColumns);

            if (!empty($missingColumns)) {
                $missingColumnsNames = array_map(function ($col) use ($columnNames) {
                    return $columnNames[$col];
                }, $missingColumns);

                $response = [
                    'status' => 400,
                    'message' => 'Import error! Missing columns: ' . implode(', ', $missingColumnsNames)
                ];
            } else {
                // All required columns are present, proceed with import
                $inserdata = [];
                $flag = true;
                foreach ($allDataInSheet as $value) {
                    if ($flag) {
                        $flag = false;
                        continue;
                    }

                    $notEmptyValues = array_filter($value, function($v) {
                        return $v !== null && $v !== '';
                    });
                    
                    if (!empty($notEmptyValues)) {
                        $inserdata[] = [
                            'first_name' => $value['A'],
                            'middle_name' => $value['B'],
                            'last_name' => $value['C'],
                            'id_number' => $value['D'],
                            'position' => $value['E'],
                            'office' => $value['F'],
                            'rfid' => $value['G']
                        ];
                    }
                }

                $result = $this->ExcelImportModel->importStaffData($inserdata);

                if ($result) {
                    $response = [
                        'status' => 200,
                        'message' => 'Staff File Uploaded Successfully!'
                    ];
                } else {
                    $response = [
                        'status' => 400,
                        'message' => 'Staff File Upload Failed!'
                    ];
                }
            }

            } catch (Exception $e) {
                $response = [
                    'status' => 500,
                    'message' => 'Error loading file: ' . $e->getMessage()
                ];
            }

            // Delete the uploaded file after processing
            unlink($inputFileName);
        }

        echo json_encode($response);    
    }

}