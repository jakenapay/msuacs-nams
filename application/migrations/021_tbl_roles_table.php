<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Tbl_roles_table extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'role_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
            ),
        ));

        $this->dbforge->add_key('id', TRUE); // Primary Key
        $this->dbforge->create_table('roles');
    }

    public function down() {
        $this->dbforge->drop_table('roles');
    }
}
