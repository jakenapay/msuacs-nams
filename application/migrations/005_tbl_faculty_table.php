<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Tbl_faculty_table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'BIGINT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'first_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255'
            ),
            'middle_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ),
            'last_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255'
            ),
            'id_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '255'
            ),
            'college_id' => array(
                'type' => 'BIGINT',
                'unsigned' => TRUE,
            ),
            'department_id' => array(
                'type' => 'BIGINT',
                'unsigned' => TRUE,
            ),
            'rfid' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'is_banned' => array(
                'type' => 'BOOLEAN',
                'default' => 0
            ),
            'image' => array(
                'type' => 'LONGTEXT'
            ),
            'created_at' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ),
            'updated_at' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE
            )
        ));
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('faculty');
    }

    public function down()
    {
        $this->dbforge->drop_table('faculty');
    }
}