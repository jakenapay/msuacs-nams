<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_tbl_security_logs_table extends CI_Migration {

    public function up()
    {
        // Defineields
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'aid' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ),
            'action' => array(
                'type' => 'TINYTEXT',
                'null' => FALSE
            ),
            'name' => array(
                'type' => 'TINYTEXT',
                'null' => FALSE
            ),
            'date' => array(
                'type' => 'VARCHAR',
                'constraint' => '55',
                'null' => FALSE,
                'default' => 'CURRENT_TIMESTAMP()'
            )
        );

        // Add the fields
        $this->dbforge->add_field($fields);

        // Add primary key
        $this->dbforge->add_key('id', TRUE);

        // Create the table
        $this->dbforge->create_table('logs_security');
    }

    public function down()
    {
        // Drop the table if it exists
        $this->dbforge->drop_table('logs_security');
    }
}
