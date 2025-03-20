<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Tbl_visitors_pending_table extends CI_Migration {

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
            'suffix' => array(
                'type' => 'VARCHAR',
                'constraint' => '4',
                'null' => TRUE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => TRUE
            ),
            'phone_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => TRUE
            ),
            'company' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ),
            'id_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '255'
            ),
            'id_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '255'
            ),
            'id_front' => array(
                'type' => 'LONGTEXT'
            ),
            'id_back' => array(
                'type' => 'LONGTEXT'
            ),
            'visitor_image' => array(
                'type' => 'LONGTEXT'
            ),
            'visit_purpose' => array(
                'type' => 'VARCHAR',
                'constraint' => '255'
            ),
            'visit_date' => array(
                'type' => 'DATE'
            ),
            'visit_time' => array(
                'type' => 'TIME'
            ),
            'visit_duration' => array(
                'type' => 'VARCHAR',
                'constraint' => '55',
            ),
            'contact_position' => array(
                'type' => 'VARCHAR',
                'constraint' => '55',
            ),
            'contact_person' => array(
                'type' => 'VARCHAR',
                'constraint' => '55',
            ),
            'accomodations' => array(
                'type' => 'VARCHAR',
                'constraint' => '155',
                'null' => TRUE
            ),
            'parking_requirement' => array(
                'type' => 'VARCHAR',
                'constraint' => '55',
            ),
            'transaction_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '155',
            ),
            'status' => array(
                'type' => 'INT',
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
        $this->dbforge->create_table('visitors_pending');
    }

    public function down()
    {
        $this->dbforge->drop_table('visitors_pending');
    }
}