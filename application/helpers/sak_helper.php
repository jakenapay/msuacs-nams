<?php

//Geerlingguy Ping Package
function ping($host) 
{
    // Load the JJG\Ping package
    $ping = new \JJG\Ping($host);
    $responseTime = $ping->ping();

    // Check if the ping was successful
    if ($responseTime === false) {
        return false;
    }

    return $responseTime;
}
// Check if admin has the required role
function require_role($required_role_id) {
    $ci = get_instance();
    $admin_id = $ci->session->userdata('admin')['id'];
    if (!$ci->AuthModel->has_role($admin_id, $required_role_id)) {
        // Handle unauthorized access
        $ci->session->set_flashdata('error', 'You do not have permission to access this page.');
        redirect('errors/blocked'); // or any other appropriate page
        return false; // Optional, if you want to prevent further execution
    }
    return true; // Optional, in case you want to use it as a conditional check
}

//Function to set the image path of the Contractors in Database and in the server
function setAdminsUploadPath(){
    $ci = get_instance();

    //App name
    $app_name = $ci->config->item('app_name'); //msuacs

    //Default values
    $parentFolder = $_SERVER['DOCUMENT_ROOT'] . '/' . $app_name . '/assets/images/uploads/admin/';
      // Construct the upload path
    $dbImagePath = 'assets/images/uploads/admin/';

    // Check if the folder exists, if not, create it
    if (!file_exists($parentFolder)) {
        mkdir($parentFolder, 0777, true); // You might want to adjust the permissions

        // Apply Linux-specific ownership settings (if needed)
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            // Linux-specific permission settings (if required)
            chown($subfolderPath, 'www-data'); // Adjust 'www-data' as per your server configuration
            chmod($subfolderPath, 0777);       // Ensure write permissions for web server user
        }

        return $dbImagePath;
    } else {
        return $dbImagePath;
    }
}   

//Function to set the image path of the student in Database and in the server
function setStudentUploadPath($collegeName, $departmentName, $programName){
    $ci = get_instance();

    //App name
    $app_name = $ci->config->item('app_name'); //msuacs

    //Default values
    $parentFolder = $_SERVER['DOCUMENT_ROOT'] . '/' . $app_name . '/assets/images/uploads/students/';
    $college = $collegeName;
    $department = $departmentName;
    $program = $programName;

      // Construct the upload path
    $uploadPath = $college . '/' . $department . '/' . $program . '/';
    $subfolderPath = $parentFolder . $uploadPath;
    $dbImagePath = 'assets/images/uploads/students/' . $uploadPath;

    // Check if the folder exists, if not, create it
    if (!file_exists($subfolderPath)) {
        mkdir($subfolderPath, 0777, true); // You might want to adjust the permissions

        // Apply Linux-specific ownership settings (if needed)
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            // Linux-specific permission settings (if required)
            chown($subfolderPath, 'www-data'); // Adjust 'www-data' as per your server configuration
            chmod($subfolderPath, 0777);       // Ensure write permissions for web server user
        }
        
        return $dbImagePath;
    } else {
        return $dbImagePath;
    }
}   

