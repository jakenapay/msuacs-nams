<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Tbl_departments_table extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'college_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
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
        $this->dbforge->create_table('departments');
        // Add foreign key
        $this->db->query('ALTER TABLE `departments` ADD CONSTRAINT `fk_departments_college` FOREIGN KEY (`college_id`) REFERENCES `colleges`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down() {
        $this->dbforge->drop_table('departments');
    }
}