<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Tbl_visiting_officer_table extends CI_Migration {

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
            'rfid' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'image' => array(
                'type' => 'LONGTEXT'
            ),
            'is_banned' => array(
                'type' => 'BOOLEAN',
                'default' => 0
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
        $this->dbforge->create_table('visiting_officer');
    }

    public function down()
    {
        $this->dbforge->drop_table('visiting_officer');
    }
}