<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Tbl_turnstile_mode_table extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'mode' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ]
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('turnstile_mode');
        $this->db->query('INSERT INTO `turnstile_mode` (`id`, `mode`) VALUES (1, 1)');
    }

    public function down() {
        $this->dbforge->drop_table('turnstile_mode');
    }
}