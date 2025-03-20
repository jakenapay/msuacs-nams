<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Tbl_devices_table extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'device_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'ip' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'location_id' => [
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
        $this->dbforge->create_table('devices');
        // Add foreign key
        $this->db->query('ALTER TABLE `devices` ADD CONSTRAINT `fk_devices_location` FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down() {
        $this->dbforge->drop_table('devices');
    }
}