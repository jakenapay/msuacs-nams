<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FormValidationHook {

    public function checkFormProgress() {
        $CI =& get_instance();
        $CI->load->library('session');
        $CI->load->helper('url');

        // Get current controller and method
        $controller = $CI->router->fetch_class();
        $method = $CI->router->fetch_method();

        // Define the required session keys for each step
        $required_steps = [
            'step2' => 'step1_data',
            'step3' => 'step2_data',
            'step4' => 'step3_data',
            'step5' => 'step4_data'
        ];

        // Check if the current method is in the form steps array
        if (array_key_exists($method, $required_steps)) {
            $required_session_key = $required_steps[$method];

            // Check if the required session data exists
            if (!$CI->session->userdata($required_session_key)) {
                // Redirect to the required step if session data is missing
                $CI->session->set_flashdata('error', 'Please complete the previous steps first.');
                redirect('visitors_pending/form/step1');
                exit; // Ensure no further code is executed
            }
        }
    }
}
?>
