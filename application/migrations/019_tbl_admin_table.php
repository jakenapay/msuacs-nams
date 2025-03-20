<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Tbl_admin_table extends CI_Migration {

    public function up() {
        // Drop the existing admin table if it exists
        $this->dbforge->drop_table('admin', TRUE);

        // Create the new admin table
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
            ),
            'password' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
            ),
            'image' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
            ),
            'created_at' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('admin');

        // Add unique index on username
        $this->db->query('CREATE UNIQUE INDEX idx_username ON admin(username)');
    }

    public function down() {
        $this->dbforge->drop_table('admin');
    }
}