<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Tbl_logs_table extends CI_Migration {

    public function up() {
        // Create entry_logs table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'rfid' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'fullname' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'id_number' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'college' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ],
            'department' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ],
            'program' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ],
            'building' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'time' => [
                'type' => 'TIME',
            ],
            'gate' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('entry_logs');

        // Create exit_logs table (identical structure to entry_logs)
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'rfid' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'fullname' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'id_number' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'college' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ],
            'department' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ],
            'program' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ],
            'building' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'time' => [
                'type' => 'TIME',
            ],
            'gate' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('exit_logs');
    }

    public function down() {
        $this->dbforge->drop_table('entry_logs');
        $this->dbforge->drop_table('exit_logs');
    }
}