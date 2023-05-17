<?php
/////////////////////////////////////
////use when CREATING a new table
//////////////////////////////////////

//  = array(
//         'blog_id' => array(
//                 'type' => 'INT',
//                 'constraint' => 5,
//                 'unsigned' => TRUE,
//                 'auto_increment' => TRUE
//         ),
//         'blog_title' => array(
//                 'type' => 'VARCHAR',
//                 'constraint' => '100',
//                 'unique' => TRUE,
//         ),
//         'blog_author' => array(
//                 'type' =>'VARCHAR',
//                 'constraint' => '100',
//                 'default' => 'King of Town',
//         ),
//         'blog_description' => array(
//                 'type' => 'TEXT',
//                 'null' => TRUE,
//         ),
// );
// ->dbforge->add_field();
//->dbforge->add_field('id');
// gives id INT(9) NOT NULL AUTO_INCREMENT

//KEYS
//
//->dbforge->add_key('blog_id', TRUE);
// gives PRIMARY KEY `blog_id` (`blog_id`)
// ->dbforge->add_key(array('blog_name', 'blog_label'));
// gives KEY `blog_name_blog_label` (`blog_name`, `blog_label`)
//->add_field('CONSTRAINT FOREIGN KEY (id) REFERENCES table(id)');

//CREATE TABLE
//->dbforge->create_table('table_name');
// gives CREATE TABLE table_name
//->dbforge->create_table('table_name', TRUE);
// gives CREATE TABLE IF NOT EXISTS table_name
//  = array('ENGINE' => 'InnoDB');
//->dbforge->create_table('table_name', FALSE, );
// produces: CREATE TABLE `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

//DROP TABLE
////->dbforge->drop_table('table_name');
// Produces: DROP TABLE table_name
//->dbforge->drop_table('table_name',TRUE);
// Produces: DROP TABLE IF EXISTS table_name



/////////////////////////////////////
//use when EDITING an existing table
/////////////////////////////////////

///RENAME TABLE
///->dbforge->rename_table('old_table_name', 'new_table_name');
// gives ALTER TABLE old_table_name RENAME TO new_table_name

//  = array(
//         'preferences' => array('type' => 'TEXT')
// );
//->dbforge->add_column('table_name', );
// Executes: ALTER TABLE table_name ADD preferences TEXT

// Will place the new column after the `another_field` column:
//  = array(
//         'preferences' => array('type' => 'TEXT', 'after' => 'another_field')
// );

// Will place the new column at the start of the table definition:
//  = array(
//         'preferences' => array('type' => 'TEXT', 'first' => TRUE)
// );

//->add_column('table',['CONSTRAINT fk_id FOREIGN KEY(id) REFERENCES table(id)',]);

//->dbforge->drop_column('table_name', 'column_to_drop');

//  = array(
//         'old_name' => array(
//                 'name' => 'new_name',
//                 'type' => 'TEXT',
//         ),
// );
//->dbforge->modify_column('table_name', );
// gives ALTER TABLE table_name CHANGE old_name new_name TEXT

//FOREIGN KEYS
//->db->query('ALTER TABLE `pelayanan` ADD FOREIGN KEY(`ID_AREA`) REFERENCES 'the_other_table_name'(`ID_AREA`) ON DELETE CASCADE ON UPDATE CASCADE;');

class Migration_transactions extends CI_Migration 
{

    public function up() 
    {
		$fields = array(
		        'uuid' => array(
		                'type' => 'VARCHAR',
		                'constraint' => '50',
		                'default' => '1',
		                'first' => TRUE,
		        ),
		);

		$this->dbforge->add_column('ms_payments', $fields);

    }

    public function down() 
    {
        $this->dbforge->drop_column('ms_payments', 'uuid');
    }

}