<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Tbl_programs_table extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'department_id' => [
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
        $this->dbforge->create_table('programs');

        // Add foreign key
        $this->db->query('ALTER TABLE `programs` ADD CONSTRAINT `fk_programs_department` FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');        
    }

    public function down() {
        $this->dbforge->drop_table('programs');
    }
}