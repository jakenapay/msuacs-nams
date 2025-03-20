<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller {
    public function index() {
        $this->load->library('migration');
        
        if ($this->migration->current() === FALSE) {
            echo $this->migration->error_string();
        } else {
            echo 'Migration completed successfully.';
        }
        
        echo " Last migration version: " . $this->db->get('migrations')->row()->version;
    }
}