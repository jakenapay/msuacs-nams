<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Tbl_admin_roles_table extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'admin_id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
            ),
            'role_id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
            )
        ));

        $this->dbforge->add_key(['admin_id', 'role_id'], TRUE); // Primary Key
        
        $this->dbforge->create_table('admin_roles');

        // Add foreign keys after table creation
        $this->db->query('ALTER TABLE `admin_roles` ADD CONSTRAINT `fk_admin_roles_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // Check if roles table exists before adding the foreign key
        if ($this->db->table_exists('roles')) {
            $this->db->query('ALTER TABLE `admin_roles` ADD CONSzTRAINT `fk_admin_roles_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        }
    }

    public function down() {
        $this->dbforge->drop_table('admin_roles');
    }
}