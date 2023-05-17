<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_base_fitbox extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'MEDIUMINT',
                'constraint' => '8',
                'unsigned' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            'description' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table auth_groups
        $this->dbforge->create_table("auth_groups", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
            ),
            'login' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'time' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table auth_login_attempts
        $this->dbforge->create_table("auth_login_attempts", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'VARCHAR',
                'constraint' => '128',
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
            ),
            'timestamp' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'default' => '0',
            ),
            'data' => array(
                'type' => 'BLOB',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("timestamp");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table auth_sessions
        $this->dbforge->create_table("auth_sessions", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'active' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'last_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'first_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
                'unique' => TRUE,
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'unique' => TRUE,
            ),
            'company' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'DNI' => array(
                'type' => 'VARCHAR',
                'constraint' => '11',
                'null' => TRUE,
                'unique' => TRUE,
            ),
            'gender' => array(
                'type' => 'ENUM',
                'null' => TRUE,
            ),
            'phone' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'birth_date' => array(
                'type' => 'DATE',
                'null' => TRUE,
            ),
            'password' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'salt' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'activation_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => TRUE,
            ),
            'forgotten_password_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => TRUE,
            ),
            'forgotten_password_time' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'remember_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => TRUE,
            ),
            'last_login' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
            'updated_on' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table auth_users
        $this->dbforge->create_table("auth_users", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'group_id' => array(
                'type' => 'MEDIUMINT',
                'constraint' => '8',
                'unsigned' => TRUE,
                'default' => '12',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("user_id");
        $this->dbforge->add_key("group_id");
        $this->dbforge->add_key("box_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table auth_users_groups
        $this->dbforge->create_table("auth_users_groups", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `auth_users_groups` ADD FOREIGN KEY(`box_id`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `auth_users_groups` ADD FOREIGN KEY(`group_id`) REFERENCES auth_groups(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `auth_users_groups` ADD FOREIGN KEY(`user_id`) REFERENCES auth_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'systolic' => array(
                'type' => 'TINYINT',
                'constraint' => '3',
                'null' => TRUE,
            ),
            'diastolic' => array(
                'type' => 'TINYINT',
                'constraint' => '3',
                'null' => TRUE,
            ),
            'pulse' => array(
                'type' => 'TINYINT',
                'constraint' => '3',
                'null' => TRUE,
            ),
            'timestamp' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("user_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table biometrics_bp
        $this->dbforge->create_table("biometrics_bp", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'height' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'date' => array(
                'type' => 'DATE',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("user_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table biometrics_height
        $this->dbforge->create_table("biometrics_height", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'weight' => array(
                'type' => 'DECIMAL',
                'constraint' => '4,1',
                'unsigned' => TRUE,
            ),
            'fat' => array(
                'type' => 'DECIMAL',
                'constraint' => '3,1',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'date' => array(
                'type' => 'DATE',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("user_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table biometrics_weight
        $this->dbforge->create_table("biometrics_weight", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'unique' => TRUE,
            ),
            'website' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
            'status' => array(
                'type' => 'CHAR',
                'constraint' => '1',
                'null' => TRUE,
            ),
            'slug' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
                'unique' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table boxes
        $this->dbforge->create_table("boxes", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'cal_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'weekly' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '1',
            ),
            'only_this_week' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '1',
            ),
            'past_events' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'mark_past' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'free_spots' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'max_spots' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '1',
            ),
            'allow_public' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '1',
            ),
            'use_popup' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'start_day' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '1',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("box_id");
        $this->dbforge->add_key("cal_code");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table bs_calendar_settings
        $this->dbforge->create_table("bs_calendar_settings", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `bs_calendar_settings` ADD FOREIGN KEY(`box_id`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'title' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
            ),
            'dateFrom' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'dateTo' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'value' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'type' => array(
                'type' => 'ENUM',
            ),
            'services' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'unique' => TRUE,
            ),
            'limit' => array(
                'type' => 'SMALLINT',
                'constraint' => '6',
                'null' => TRUE,
            ),
            'counter' => array(
                'type' => 'SMALLINT',
                'constraint' => '6',
                'default' => '0',
            ),
            'status' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("box_id");
        $this->dbforge->add_key("services");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table bs_coupons
        $this->dbforge->create_table("bs_coupons", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `bs_coupons` ADD FOREIGN KEY(`box_id`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '20',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'eventDate' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'eventDateEnd' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'serviceID' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'eventTime' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'spaces' => array(
                'type' => 'INT',
                'constraint' => '20',
                'default' => '10',
                'null' => TRUE,
            ),
            'title' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'entryFee' => array(
                'type' => 'DOUBLE',
                'default' => '0',
            ),
            'payment_method' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => 'invoice',
            ),
            'payment_required' => array(
                'type' => 'TINYINT',
                'constraint' => '5',
                'default' => '2',
            ),
            'description' => array(
                'type' => 'LONGTEXT',
                'null' => TRUE,
            ),
            'max_qty' => array(
                'type' => 'INT',
                'constraint' => '20',
                'default' => '1',
            ),
            'allow_multiple' => array(
                'type' => 'INT',
                'constraint' => '20',
                'default' => '2',
            ),
            'path' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'repeate' => array(
                'type' => 'ENUM',
            ),
            'repeate_interval' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'recurring' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => '0',
            ),
            'recurringEndDate' => array(
                'type' => 'DATE',
            ),
            'coupon' => array(
                'type' => 'SMALLINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'location' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => TRUE,
            ),
            'map_link' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => TRUE,
            ),
            'color' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            'deposit' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table bs_events
        $this->dbforge->create_table("bs_events", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '20',
            ),
            'boxID' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'userID' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'muID' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'trial' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => '0',
            ),
            'serviceID' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'reserveDateFrom' => array(
                'type' => 'DATETIME',
            ),
            'reserveDateTo' => array(
                'type' => 'DATETIME',
            ),
            'status' => array(
                'type' => 'TINYINT',
                'constraint' => '5',
                'default' => '2',
            ),
            'qty' => array(
                'type' => 'INT',
                'constraint' => '20',
                'default' => '1',
            ),
            'reminder_sent' => array(
                'type' => 'ENUM',
                'default' => 'n',
            ),
            'dateCreated' => array(
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("boxID");
        $this->dbforge->add_key("userID");
        $this->dbforge->add_key("muID");
        $this->dbforge->add_key("serviceID");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
            'COMMENT' => 'reservas online',
        );

        // Create Table bs_reservations
        $this->dbforge->create_table("bs_reservations", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `bs_reservations` ADD FOREIGN KEY(`boxID`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `bs_reservations` ADD FOREIGN KEY(`muID`) REFERENCES ms_memberships_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `bs_reservations` ADD FOREIGN KEY(`serviceID`) REFERENCES bs_services(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `bs_reservations` ADD FOREIGN KEY(`userID`) REFERENCES auth_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '20',
            ),
            'serviceID' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
            ),
            'userID' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
            ),
            'reason' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'dateCreated' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'reserveDateFrom' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'reserveDateTo' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
                'null' => TRUE,
            ),
            'interval' => array(
                'type' => 'INT',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'qty' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
            ),
            'repeate' => array(
                'type' => 'ENUM',
                'null' => TRUE,
            ),
            'repeate_interval' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'recurring' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => '0',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
            'COMMENT' => 'reservas manuales staff',
        );

        // Create Table bs_reserved_time
        $this->dbforge->create_table("bs_reserved_time", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '20',
            ),
            'dateCreated' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'reservedID' => array(
                'type' => 'INT',
                'constraint' => '20',
            ),
            'dateFrom' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'dateTo' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'tinterval' => array(
                'type' => 'INT',
                'constraint' => '20',
            ),
            'qty' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table bs_reserved_time_items
        $this->dbforge->create_table("bs_reserved_time_items", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'idItem' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'idService' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'week_num' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'startTime' => array(
                'type' => 'TIME',
            ),
            'endTime' => array(
                'type' => 'TIME',
            ),
            'coach' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
                'default' => 'jose sola',
                'null' => TRUE,
            ),
            'text1' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
                'null' => TRUE,
            ),
            'text2' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("idItem", TRUE);
        $this->dbforge->add_key("box_id");
        $this->dbforge->add_key("idService");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table bs_schedule
        $this->dbforge->create_table("bs_schedule", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `bs_schedule` ADD FOREIGN KEY(`box_id`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `bs_schedule` ADD FOREIGN KEY(`idService`) REFERENCES bs_services(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'idItem' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'idService' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'dateFrom' => array(
                'type' => 'DATE',
            ),
            'dateTo' => array(
                'type' => 'DATE',
            ),
            'price' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("idItem", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table bs_schedule_days
        $this->dbforge->create_table("bs_schedule_days", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'idService' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'spots' => array(
                'type' => 'INT',
                'constraint' => '1',
            ),
            'description' => array(
                'type' => 'TEXT',
            ),
            'img' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
            ),
            'maxDays' => array(
                'type' => 'INT',
                'constraint' => '10',
            ),
            'minDays' => array(
                'type' => 'INT',
                'constraint' => '10',
            ),
            'daysBefore' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'startDay' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'comment' => '0- sunday, 1 - monday',
            ),
            'payment_method' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => 'invoice',
            ),
            'coupon' => array(
                'type' => 'SMALLINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'showPrice' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("idService", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table bs_service_days_settings
        $this->dbforge->create_table("bs_service_days_settings", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'serviceId' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'payment_method' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'invoice',
            ),
            'allow_times' => array(
                'type' => 'INT',
                'constraint' => '20',
                'default' => '1',
            ),
            'allow_times_min' => array(
                'type' => 'INT',
                'constraint' => '20',
                'default' => '1',
            ),
            'interval' => array(
                'type' => 'INT',
                'constraint' => '20',
                'default' => '60',
            ),
            'spot_price' => array(
                'type' => 'DOUBLE',
                'default' => '0',
            ),
            'spot_invoice' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => '0',
            ),
            'startDay' => array(
                'type' => 'TINYINT',
                'constraint' => '5',
                'default' => '0',
                'comment' => '0- sunday, 1 - monday',
            ),
            'spaces_available' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '1',
                'comment' => 'spaces available per each REGULAR timed slot',
            ),
            'show_spaces_left' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'comment' => '1-show,0-not show',
            ),
            'show_multiple_spaces' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'comment' => '1-show,0-not show',
            ),
            'use_popup' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'comment' => '1-show,0-not show',
            ),
            'coupon' => array(
                'type' => 'SMALLINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'time_before' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table bs_service_settings
        $this->dbforge->create_table("bs_service_settings", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
            ),
            'type' => array(
                'type' => 'ENUM',
                'default' => 't',
                'null' => TRUE,
            ),
            'autoconfirm' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => '0',
                'null' => TRUE,
            ),
            'fromName' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'default' => 'Name',
                'null' => TRUE,
            ),
            'fromEmail' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'default' => 'noreply@email.com',
                'null' => TRUE,
            ),
            'show_event_titles' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'null' => TRUE,
            ),
            'show_event_image' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'null' => TRUE,
            ),
            'show_available_seats' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'null' => TRUE,
            ),
            'default' => array(
                'type' => 'ENUM',
                'default' => 'n',
                'null' => TRUE,
            ),
            'deposit' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '100.00',
                'null' => TRUE,
            ),
            'delBookings' => array(
                'type' => 'ENUM',
                'default' => 'n',
                'null' => TRUE,
            ),
            'payment_method' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => 'invoice',
                'null' => TRUE,
            ),
            'allow_times' => array(
                'type' => 'INT',
                'constraint' => '20',
                'default' => '1',
                'null' => TRUE,
            ),
            'allow_times_min' => array(
                'type' => 'INT',
                'constraint' => '20',
                'default' => '1',
                'null' => TRUE,
            ),
            'interval' => array(
                'type' => 'INT',
                'constraint' => '20',
                'default' => '60',
                'null' => TRUE,
            ),
            'spot_price' => array(
                'type' => 'DOUBLE',
                'default' => '0',
                'null' => TRUE,
            ),
            'spot_invoice' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => '0',
                'null' => TRUE,
            ),
            'startDay' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'null' => TRUE,
            ),
            'spaces_available' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '1',
                'null' => TRUE,
            ),
            'show_spaces_left' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'null' => TRUE,
            ),
            'show_multiple_spaces' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'null' => TRUE,
            ),
            'use_popup' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'null' => TRUE,
            ),
            'time_before' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => TRUE,
            ),
            'spots' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
                'null' => TRUE,
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'img' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => TRUE,
            ),
            'maxDays' => array(
                'type' => 'INT',
                'constraint' => '10',
                'null' => TRUE,
            ),
            'minDays' => array(
                'type' => 'INT',
                'constraint' => '10',
                'null' => TRUE,
            ),
            'daysBefore' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'coupon' => array(
                'type' => 'SMALLINT',
                'constraint' => '1',
                'default' => '0',
                'null' => TRUE,
            ),
            'showPrice' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'null' => TRUE,
            ),
            'color_bg' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => '#000000',
                'null' => TRUE,
            ),
            'color_hover' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => '#000000',
                'null' => TRUE,
            ),
            'active' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'null' => TRUE,
            ),
            'date_created' => array(
                'type' => 'DATE',
                'null' => TRUE,
            ),
            'updated_on' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("box_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table bs_services
        $this->dbforge->create_table("bs_services", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'grace_period' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
            ),
            'cancel_period' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("box_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table bs_settings
        $this->dbforge->create_table("bs_settings", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `bs_settings` ADD FOREIGN KEY(`box_id`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'uid' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'mid' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'cid' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'tax' => array(
                'type' => 'FLOAT',
                'constraint' => '4,2',
                'default' => '0.00',
            ),
            'totaltax' => array(
                'type' => 'FLOAT',
                'constraint' => '4,2',
                'default' => '0.00',
            ),
            'coupon' => array(
                'type' => 'FLOAT',
                'constraint' => '4,2',
                'default' => '0.00',
            ),
            'total' => array(
                'type' => 'FLOAT',
                'constraint' => '4,2',
                'default' => '0.00',
            ),
            'originalprice' => array(
                'type' => 'FLOAT',
                'constraint' => '4,2',
                'default' => '0.00',
            ),
            'totalprice' => array(
                'type' => 'FLOAT',
                'constraint' => '4,2',
                'default' => '0.00',
            ),
            'created' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("uid", TRUE);
        $this->dbforge->add_key("mid");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table cart
        $this->dbforge->create_table("cart", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'title' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'tooltip' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '55',
                'null' => TRUE,
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'req' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'active' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '1',
            ),
            'sorting' => array(
                'type' => 'INT',
                'constraint' => '4',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table custom_fields
        $this->dbforge->create_table("custom_fields", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '5',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
            ),
            'subject' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'help' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'body' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table email_templates
        $this->dbforge->create_table("email_templates", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '0',
                'unique' => TRUE,
            ),
            'short_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
                'null' => TRUE,
                'unique' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_basics
        $this->dbforge->create_table("exercise_basics", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => '0',
            ),
            'description' => array(
                'type' => 'MEDIUMTEXT',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_contractions
        $this->dbforge->create_table("exercise_contractions", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'short_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_materials
        $this->dbforge->create_table("exercise_materials", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '0',
            ),
            'description' => array(
                'type' => 'MEDIUMTEXT',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_mechanics
        $this->dbforge->create_table("exercise_mechanics", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '0',
            ),
            'description' => array(
                'type' => 'MEDIUMTEXT',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_movements
        $this->dbforge->create_table("exercise_movements", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'muscle_group' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => '0',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '0',
                'unique' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("muscle_group");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_muscles
        $this->dbforge->create_table("exercise_muscles", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_sports
        $this->dbforge->create_table("exercise_sports", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '0',
            ),
            'description' => array(
                'type' => 'MEDIUMTEXT',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_targets
        $this->dbforge->create_table("exercise_targets", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'short_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
                'default' => '0',
                'null' => TRUE,
                'unique' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_types
        $this->dbforge->create_table("exercise_types", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_variation' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'comment' => 'variation excercise',
            ),
            'reps' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'load' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'distance' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'height' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'time' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'energy' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'tons' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'work' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_variation");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_variation_log
        $this->dbforge->create_table("exercise_variation_log", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '0',
                'unique' => TRUE,
            ),
            'short_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => TRUE,
                'unique' => TRUE,
            ),
            'reps' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'load' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'distance' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'height' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'time' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'energy' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'tons' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'work' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_variations
        $this->dbforge->create_table("exercise_variations", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_variation' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_basic' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_variation");
        $this->dbforge->add_key("id_basic");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_variations_basics
        $this->dbforge->create_table("exercise_variations_basics", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `exercise_variations_basics` ADD FOREIGN KEY(`id_basic`) REFERENCES exercise_basics(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `exercise_variations_basics` ADD FOREIGN KEY(`id_variation`) REFERENCES exercise_variations(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_variation' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_contraction' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_variation");
        $this->dbforge->add_key("id_contraction");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_variations_contractions
        $this->dbforge->create_table("exercise_variations_contractions", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `exercise_variations_contractions` ADD FOREIGN KEY(`id_contraction`) REFERENCES exercise_contractions(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `exercise_variations_contractions` ADD FOREIGN KEY(`id_variation`) REFERENCES exercise_variations(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_variation' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_material' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_variation");
        $this->dbforge->add_key("id_material");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_variations_materials
        $this->dbforge->create_table("exercise_variations_materials", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `exercise_variations_materials` ADD FOREIGN KEY(`id_material`) REFERENCES exercise_materials(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `exercise_variations_materials` ADD FOREIGN KEY(`id_variation`) REFERENCES exercise_variations(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_variation' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_mechanic' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_variation");
        $this->dbforge->add_key("id_mechanic");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_variations_mechanics
        $this->dbforge->create_table("exercise_variations_mechanics", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `exercise_variations_mechanics` ADD FOREIGN KEY(`id_mechanic`) REFERENCES exercise_mechanics(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `exercise_variations_mechanics` ADD FOREIGN KEY(`id_variation`) REFERENCES exercise_variations(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_variation' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_movement' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_variation");
        $this->dbforge->add_key("id_movement");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_variations_movements
        $this->dbforge->create_table("exercise_variations_movements", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `exercise_variations_movements` ADD FOREIGN KEY(`id_movement`) REFERENCES exercise_movements(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `exercise_variations_movements` ADD FOREIGN KEY(`id_variation`) REFERENCES exercise_variations(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_variation' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'id_muscle' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'EMG' => array(
                'type' => 'TINYINT',
                'constraint' => '3',
                'unsigned' => TRUE,
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_variation");
        $this->dbforge->add_key("id_muscle");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_variations_muscles_primary
        $this->dbforge->create_table("exercise_variations_muscles_primary", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `exercise_variations_muscles_primary` ADD FOREIGN KEY(`id_muscle`) REFERENCES exercise_muscles(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `exercise_variations_muscles_primary` ADD FOREIGN KEY(`id_variation`) REFERENCES exercise_variations(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_variation' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_muscle' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_variation");
        $this->dbforge->add_key("id_muscle");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_variations_muscles_secondary
        $this->dbforge->create_table("exercise_variations_muscles_secondary", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `exercise_variations_muscles_secondary` ADD FOREIGN KEY(`id_muscle`) REFERENCES exercise_muscles(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `exercise_variations_muscles_secondary` ADD FOREIGN KEY(`id_variation`) REFERENCES exercise_variations(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_variation' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_target' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_variation");
        $this->dbforge->add_key("id_target");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_variations_targets
        $this->dbforge->create_table("exercise_variations_targets", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `exercise_variations_targets` ADD FOREIGN KEY(`id_target`) REFERENCES exercise_targets(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `exercise_variations_targets` ADD FOREIGN KEY(`id_variation`) REFERENCES exercise_variations(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_variation' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_type' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_variation");
        $this->dbforge->add_key("id_type");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table exercise_variations_types
        $this->dbforge->create_table("exercise_variations_types", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `exercise_variations_types` ADD FOREIGN KEY(`id_type`) REFERENCES exercise_types(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `exercise_variations_types` ADD FOREIGN KEY(`id_variation`) REFERENCES exercise_variations(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
            ),
            'capital_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'slug' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'unique' => TRUE,
            ),
            'comunidad' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table geo_comunidades
        $this->dbforge->create_table("geo_comunidades", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'SMALLINT',
                'constraint' => '6',
            ),
            'abbr' => array(
                'type' => 'VARCHAR',
                'constraint' => '2',
                'unique' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '70',
            ),
            'active' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '1',
            ),
            'home' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'vat' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => '0.00',
            ),
            'sorting' => array(
                'type' => 'SMALLINT',
                'constraint' => '6',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table geo_countries
        $this->dbforge->create_table("geo_countries", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'provincia_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
            ),
            'id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
            ),
            'municipio' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'slug' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => TRUE,
            ),
            'latitud' => array(
                'type' => 'DOUBLE',
                'null' => TRUE,
            ),
            'longitud' => array(
                'type' => 'DOUBLE',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("provincia_id");
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table geo_municipios
        $this->dbforge->create_table("geo_municipios", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
            ),
            'comunidad_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
            ),
            'capital_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '-1',
            ),
            'slug' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'provincia' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("comunidad_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table geo_provincias
        $this->dbforge->create_table("geo_provincias", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'action' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'date' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
        ));

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table log_boxes
        $this->dbforge->create_table("log_boxes", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'membership_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'action' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'date' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
        ));

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table log_members
        $this->dbforge->create_table("log_members", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'total' => array(
                'type' => 'SMALLINT',
                'constraint' => '6',
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => TRUE,
            ),
            'date' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
        ));

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table log_total_clients
        $this->dbforge->create_table("log_total_clients", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'action' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'date' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
        ));

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table log_users
        $this->dbforge->create_table("log_users", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
            ),
            'code' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => TRUE,
            ),
            'default' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => '0',
                'null' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'methods' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '0',
                'null' => TRUE,
            ),
            'is_recurring' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => 'offline',
            ),
            'demo' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '1',
            ),
            'active' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'private_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'public_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'demo_private_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'demo_public_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'info' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'webhook_secret' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("box_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table ms_gateways
        $this->dbforge->create_table("ms_gateways", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'IBAN' => array(
                'type' => 'TEXT',
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
            'updated_on' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("user_id");
        $this->dbforge->add_key("box_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table ms_iban_users
        $this->dbforge->create_table("ms_iban_users", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `ms_iban_users` ADD FOREIGN KEY(`box_id`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_iban_users` ADD FOREIGN KEY(`user_id`) REFERENCES auth_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
            ),
            'title' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'description' => array(
                'type' => 'TEXT',
            ),
            'price' => array(
                'type' => 'FLOAT',
                'constraint' => '10,2',
                'default' => '0.00',
            ),
            'days' => array(
                'type' => 'INT',
                'constraint' => '5',
                'default' => '0',
            ),
            'period' => array(
                'type' => 'ENUM',
                'default' => 'M',
            ),
            'trial' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'recurring' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'private' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'active' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '1',
            ),
            'compatibility' => array(
                'type' => 'ENUM',
                'default' => 'U',
                'comment' => 'Unico (incompatible con el resto), Primario (compatible con complementarios pero no con otro primario), Complementario (compatible con pripario y otros complementarios)',
            ),
            'type' => array(
                'type' => 'ENUM',
                'default' => 'Ath',
            ),
            'max_reservations' => array(
                'type' => 'TINYINT',
                'constraint' => '2',
                'default' => '1',
            ),
            'available_from' => array(
                'type' => 'CHAR',
                'constraint' => '4',
                'default' => '0000',
            ),
            'available_to' => array(
                'type' => 'CHAR',
                'constraint' => '4',
                'default' => '2359',
            ),
            'deprecated' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("box_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table ms_memberships
        $this->dbforge->create_table("ms_memberships", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'membership_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'service_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'qtty' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'comment' => 'max quota reservas semanales',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("box_id");
        $this->dbforge->add_key("membership_id");
        $this->dbforge->add_key("service_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table ms_memberships_services
        $this->dbforge->create_table("ms_memberships_services", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `ms_memberships_services` ADD FOREIGN KEY(`box_id`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_memberships_services` ADD FOREIGN KEY(`membership_id`) REFERENCES ms_memberships(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_memberships_services` ADD FOREIGN KEY(`service_id`) REFERENCES bs_services(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'membership_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'payment_method' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'trial_used' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'unsigned' => TRUE,
                'default' => '0',
                'null' => TRUE,
            ),
            'status' => array(
                'type' => 'ENUM',
                'default' => 'n',
                'null' => TRUE,
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
            'mem_expire' => array(
                'type' => 'DATE',
            ),
            'updated_on' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("user_id");
        $this->dbforge->add_key("box_id");
        $this->dbforge->add_key("membership_id");
        $this->dbforge->add_key("payment_method");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table ms_memberships_users
        $this->dbforge->create_table("ms_memberships_users", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `ms_memberships_users` ADD FOREIGN KEY(`box_id`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_memberships_users` ADD FOREIGN KEY(`payment_method`) REFERENCES ms_gateways(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_memberships_users` ADD FOREIGN KEY(`user_id`) REFERENCES auth_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'mu_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'default' => '0',
            ),
            'coupon_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'date' => array(
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("mu_id");
        $this->dbforge->add_key("coupon_id");
        $this->dbforge->add_key("box_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table ms_memberships_users_coupons
        $this->dbforge->create_table("ms_memberships_users_coupons", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `ms_memberships_users_coupons` ADD FOREIGN KEY(`box_id`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_memberships_users_coupons` ADD FOREIGN KEY(`coupon_id`) REFERENCES bs_coupons(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_memberships_users_coupons` ADD FOREIGN KEY(`mu_id`) REFERENCES ms_memberships_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'staff_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
            ),
            'mu_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'from_membership_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'to_membership_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'txn_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'coupon_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => TRUE,
            ),
            'notes' => array(
                'type' => 'TINYTEXT',
                'null' => TRUE,
            ),
            'to' => array(
                'type' => 'DATE',
            ),
            'from' => array(
                'type' => 'DATE',
            ),
            'rate_amount' => array(
                'type' => 'FLOAT',
                'constraint' => '7,2',
            ),
            'tax' => array(
                'type' => 'FLOAT',
                'constraint' => '7,2',
                'default' => '0.00',
            ),
            'discount' => array(
                'type' => 'FLOAT',
                'constraint' => '7,2',
                'default' => '0.00',
            ),
            'total' => array(
                'type' => 'FLOAT',
                'constraint' => '7,2',
                'default' => '0.00',
            ),
            'gateway_comision' => array(
                'type' => 'FLOAT',
                'constraint' => '7,2',
                'default' => '0.00',
            ),
            'total_to_receive' => array(
                'type' => 'FLOAT',
                'constraint' => '7,2',
                'default' => '0.00',
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '4',
                'default' => 'EUR',
                'null' => TRUE,
            ),
            'pp' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            'email_not_received_sent' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => '0',
            ),
            'date' => array(
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ),
            'updated_on' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("box_id");
        $this->dbforge->add_key("user_id");
        $this->dbforge->add_key("staff_id");
        $this->dbforge->add_key("mu_id");
        $this->dbforge->add_key("from_membership_id");
        $this->dbforge->add_key("to_membership_id");
        $this->dbforge->add_key("coupon_id");
        $this->dbforge->add_key("pp");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table ms_payments
        $this->dbforge->create_table("ms_payments", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `ms_payments` ADD FOREIGN KEY(`box_id`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments` ADD FOREIGN KEY(`coupon_id`) REFERENCES bs_coupons(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments` ADD FOREIGN KEY(`from_membership_id`) REFERENCES ms_memberships(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments` ADD FOREIGN KEY(`mu_id`) REFERENCES ms_memberships_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments` ADD FOREIGN KEY(`pp`) REFERENCES ms_gateways(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments` ADD FOREIGN KEY(`staff_id`) REFERENCES auth_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments` ADD FOREIGN KEY(`to_membership_id`) REFERENCES ms_memberships(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments` ADD FOREIGN KEY(`user_id`) REFERENCES auth_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'staff_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
            ),
            'mu_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'from_membership_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'to_membership_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'txn_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'coupon_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => TRUE,
            ),
            'notes' => array(
                'type' => 'TINYTEXT',
                'null' => TRUE,
            ),
            'to' => array(
                'type' => 'DATE',
            ),
            'from' => array(
                'type' => 'DATE',
            ),
            'rate_amount' => array(
                'type' => 'FLOAT',
                'constraint' => '7,2',
            ),
            'tax' => array(
                'type' => 'FLOAT',
                'constraint' => '7,2',
                'default' => '0.00',
            ),
            'discount' => array(
                'type' => 'FLOAT',
                'constraint' => '7,2',
                'default' => '0.00',
            ),
            'total' => array(
                'type' => 'FLOAT',
                'constraint' => '7,2',
                'default' => '0.00',
            ),
            'gateway_comision' => array(
                'type' => 'FLOAT',
                'constraint' => '7,2',
                'default' => '0.00',
            ),
            'total_to_receive' => array(
                'type' => 'FLOAT',
                'constraint' => '7,2',
                'default' => '0.00',
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '4',
                'default' => 'EUR',
                'null' => TRUE,
            ),
            'pp' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            'email_not_received_sent' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => '0',
            ),
            'date' => array(
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ),
            'updated_on' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("box_id");
        $this->dbforge->add_key("user_id");
        $this->dbforge->add_key("staff_id");
        $this->dbforge->add_key("mu_id");
        $this->dbforge->add_key("from_membership_id");
        $this->dbforge->add_key("to_membership_id");
        $this->dbforge->add_key("coupon_id");
        $this->dbforge->add_key("pp");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table ms_payments_deleted
        $this->dbforge->create_table("ms_payments_deleted", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `ms_payments_deleted` ADD FOREIGN KEY(`box_id`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments_deleted` ADD FOREIGN KEY(`coupon_id`) REFERENCES bs_coupons(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments_deleted` ADD FOREIGN KEY(`from_membership_id`) REFERENCES ms_memberships(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments_deleted` ADD FOREIGN KEY(`mu_id`) REFERENCES ms_memberships_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments_deleted` ADD FOREIGN KEY(`pp`) REFERENCES ms_gateways(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments_deleted` ADD FOREIGN KEY(`staff_id`) REFERENCES auth_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments_deleted` ADD FOREIGN KEY(`to_membership_id`) REFERENCES ms_memberships(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `ms_payments_deleted` ADD FOREIGN KEY(`user_id`) REFERENCES auth_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'grace_period' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => '0',
            ),
            'cancel_period' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("box_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table ms_settings
        $this->dbforge->create_table("ms_settings", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `ms_settings` ADD FOREIGN KEY(`box_id`) REFERENCES boxes(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'title' => array(
                'type' => 'VARCHAR',
                'constraint' => '55',
            ),
            'body' => array(
                'type' => 'TEXT',
            ),
            'author' => array(
                'type' => 'VARCHAR',
                'constraint' => '55',
            ),
            'created' => array(
                'type' => 'DATE',
                'default' => '0000-00-00',
            ),
            'active' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table news
        $this->dbforge->create_table("news", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '60',
            ),
            'name_ESP' => array(
                'type' => 'VARCHAR',
                'constraint' => '60',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table nutrition_food_groups
        $this->dbforge->create_table("nutrition_food_groups", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'group_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'brand' => array(
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => TRUE,
            ),
            'from' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => 'custom',
            ),
            'NBD_No' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Shrt_Desc' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
            ),
            'Shrt_Desc_ESP' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
            ),
            'Water_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
            ),
            'Energ_Kcal' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'Protein_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
            ),
            'Lipid_Tot_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
            ),
            'Ash_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
            ),
            'Carbohydrt_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
            ),
            'Fiber_TD_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '4,1',
                'null' => TRUE,
            ),
            'Sugar_Tot_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
            ),
            'Calcium_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '8,3',
                'null' => TRUE,
            ),
            'Iron_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
            ),
            'Magnesium_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '8,3',
                'null' => TRUE,
            ),
            'Phosphorus_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '8,3',
                'null' => TRUE,
            ),
            'Potassium_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '8,3',
                'null' => TRUE,
            ),
            'Sodium_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,1',
                'null' => TRUE,
            ),
            'Zinc_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Copper_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Manganese_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Selenium_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,1',
                'null' => TRUE,
            ),
            'Vit_C_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,1',
                'null' => TRUE,
            ),
            'Thiamin_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Riboflavin_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Niacin_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Panto_Acid_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Vit_B6_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Folate_Tot_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,0',
                'null' => TRUE,
            ),
            'Folic_Acid_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,0',
                'null' => TRUE,
            ),
            'Food_Folate_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,0',
                'null' => TRUE,
            ),
            'Folate_DFE_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,0',
                'null' => TRUE,
            ),
            'Choline_Tot_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,1',
                'null' => TRUE,
            ),
            'Vit_B12_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
            ),
            'Vit_A_IU' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Vit_A_RAE' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Retinol_ug' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Alpha_Carot_ug' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Beta_Carot_ug' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Beta_Crypt_ug' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Lycopene_ug' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'LutZea_ug' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Vit_E_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,2',
                'null' => TRUE,
            ),
            'Vit_D_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '4,1',
                'null' => TRUE,
            ),
            'Vit_D_IU' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Vit_K_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,1',
                'null' => TRUE,
            ),
            'FA_Sat_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,3',
                'null' => TRUE,
            ),
            'FA_Mono_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,3',
                'null' => TRUE,
            ),
            'FA_Poly_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,3',
                'null' => TRUE,
            ),
            'Cholestrl_mg' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'GmWt_1' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => TRUE,
            ),
            'GmWt_Desc1' => array(
                'type' => 'VARCHAR',
                'constraint' => '75',
                'null' => TRUE,
            ),
            'GmWt_2' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => TRUE,
            ),
            'GmWt_Desc2' => array(
                'type' => 'VARCHAR',
                'constraint' => '78',
                'null' => TRUE,
            ),
            'Refuse_Pct' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'date' => array(
                'type' => 'DATETIME',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("group_id");
        $this->dbforge->add_key("NBD_No");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table nutrition_foods
        $this->dbforge->create_table("nutrition_foods", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `nutrition_foods` ADD FOREIGN KEY(`group_id`) REFERENCES nutrition_food_groups(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'condition' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '0',
            ),
            'from' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => 'custom',
            ),
            'Water_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
            ),
            'Energ_Kcal' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'Protein_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
            ),
            'Lipid_Tot_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
            ),
            'Ash_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
            ),
            'Carbohydrt_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
            ),
            'Fiber_TD_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '4,1',
                'null' => TRUE,
            ),
            'Sugar_Tot_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
            ),
            'Calcium_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Iron_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
            ),
            'Magnesium_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Phosphorus_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Potassium_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Sodium_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,1',
                'null' => TRUE,
            ),
            'Zinc_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Copper_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Manganese_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Selenium_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,1',
                'null' => TRUE,
            ),
            'Vit_C_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,1',
                'null' => TRUE,
            ),
            'Thiamin_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Riboflavin_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Niacin_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Panto_Acid_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Vit_B6_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,3',
                'null' => TRUE,
            ),
            'Folate_Tot_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,0',
                'null' => TRUE,
            ),
            'Folic_Acid_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,0',
                'null' => TRUE,
            ),
            'Food_Folate_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,0',
                'null' => TRUE,
            ),
            'Folate_DFE_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,0',
                'null' => TRUE,
            ),
            'Choline_Tot_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,1',
                'null' => TRUE,
            ),
            'Vit_B12_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => TRUE,
            ),
            'Vit_A_IU' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Vit_A_RAE' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Retinol_ug' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Alpha_Carot_ug' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Beta_Carot_ug' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Beta_Crypt_ug' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Lycopene_ug' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'LutZea_ug' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Vit_E_mg' => array(
                'type' => 'DECIMAL',
                'constraint' => '7,2',
                'null' => TRUE,
            ),
            'Vit_D_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '4,1',
                'null' => TRUE,
            ),
            'Vit_D_IU' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'Vit_K_ug' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,1',
                'null' => TRUE,
            ),
            'FA_Sat_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,3',
                'null' => TRUE,
            ),
            'FA_Mono_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,3',
                'null' => TRUE,
            ),
            'FA_Poly_g' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,3',
                'null' => TRUE,
            ),
            'Cholestrl_mg' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'date' => array(
                'type' => 'DATETIME',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table nutrition_foods_requirements
        $this->dbforge->create_table("nutrition_foods_requirements", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'food_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'group_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'qtty' => array(
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => TRUE,
            ),
            'date' => array(
                'type' => 'DATE',
                'null' => TRUE,
            ),
            'hour' => array(
                'type' => 'TIME',
                'null' => TRUE,
            ),
            'meal' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("user_id");
        $this->dbforge->add_key("food_id");
        $this->dbforge->add_key("group_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table nutrition_log
        $this->dbforge->create_table("nutrition_log", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `nutrition_log` ADD FOREIGN KEY(`food_id`) REFERENCES nutrition_foods(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `nutrition_log` ADD FOREIGN KEY(`group_id`) REFERENCES nutrition_food_groups(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `nutrition_log` ADD FOREIGN KEY(`user_id`) REFERENCES auth_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'card_num' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
            ),
            'card_cvc' => array(
                'type' => 'INT',
                'constraint' => '5',
            ),
            'card_exp_month' => array(
                'type' => 'VARCHAR',
                'constraint' => '2',
            ),
            'card_exp_year' => array(
                'type' => 'VARCHAR',
                'constraint' => '5',
            ),
            'item_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'item_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'item_price' => array(
                'type' => 'FLOAT',
                'constraint' => '10,2',
            ),
            'item_price_currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),
            'paid_amount' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),
            'paid_amount_currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),
            'txn_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'payment_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'created' => array(
                'type' => 'DATETIME',
            ),
            'modified' => array(
                'type' => 'DATETIME',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table orders
        $this->dbforge->create_table("orders", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'box_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'site_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'site_email' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => TRUE,
            ),
            'site_url' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => TRUE,
            ),
            'site_dir' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'reg_allowed' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '1',
            ),
            'user_limit' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'reg_verify' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'notify_admin' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'auto_verify' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'perpage' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '12',
            ),
            'thumb_w' => array(
                'type' => 'VARCHAR',
                'constraint' => '4',
            ),
            'thumb_h' => array(
                'type' => 'VARCHAR',
                'constraint' => '4',
            ),
            'backup' => array(
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => TRUE,
            ),
            'logo' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => TRUE,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'cur_symbol' => array(
                'type' => 'VARCHAR',
                'constraint' => '8',
                'null' => TRUE,
            ),
            'dsep' => array(
                'type' => 'VARCHAR',
                'constraint' => '2',
                'default' => ',',
                'null' => TRUE,
            ),
            'tsep' => array(
                'type' => 'VARCHAR',
                'constraint' => '2',
                'default' => '.',
                'null' => TRUE,
            ),
            'enable_tax' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'long_date' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'short_date' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'inv_info' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'inv_note' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'mailer' => array(
                'type' => 'ENUM',
                'default' => 'PHP',
            ),
            'smtp_host' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'smtp_user' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'smtp_pass' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'smtp_port' => array(
                'type' => 'VARCHAR',
                'constraint' => '6',
                'null' => TRUE,
            ),
            'is_ssl' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'sendmail' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => TRUE,
            ),
            'version' => array(
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("box_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table settings
        $this->dbforge->create_table("settings", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'unique' => TRUE,
            ),
            'at_country_id' => array(
                'type' => 'SMALLINT',
                'constraint' => '6',
                'null' => TRUE,
            ),
            'at_state_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'at_province_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'at_locality_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'from_country_id' => array(
                'type' => 'SMALLINT',
                'constraint' => '6',
                'null' => TRUE,
            ),
            'from_state_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'from_province_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'from_locality_id' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("at_country_id");
        $this->dbforge->add_key("at_state_id");
        $this->dbforge->add_key("at_province_id");
        $this->dbforge->add_key("at_locality_id");
        $this->dbforge->add_key("from_country_id");
        $this->dbforge->add_key("from_state_id");
        $this->dbforge->add_key("from_province_id");
        $this->dbforge->add_key("from_locality_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table users_geo
        $this->dbforge->create_table("users_geo", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `users_geo` ADD FOREIGN KEY(`at_country_id`) REFERENCES geo_countries(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `users_geo` ADD FOREIGN KEY(`at_locality_id`) REFERENCES geo_municipios(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `users_geo` ADD FOREIGN KEY(`at_province_id`) REFERENCES geo_provincias(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `users_geo` ADD FOREIGN KEY(`at_state_id`) REFERENCES geo_comunidades(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `users_geo` ADD FOREIGN KEY(`from_country_id`) REFERENCES geo_countries(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `users_geo` ADD FOREIGN KEY(`from_locality_id`) REFERENCES geo_municipios(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `users_geo` ADD FOREIGN KEY(`from_province_id`) REFERENCES geo_provincias(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `users_geo` ADD FOREIGN KEY(`from_state_id`) REFERENCES geo_comunidades(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `users_geo` ADD FOREIGN KEY(`user_id`) REFERENCES auth_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'unique' => TRUE,
            ),
            'facebook' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => TRUE,
            ),
            'twitter' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => TRUE,
            ),
            'instagram' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => TRUE,
            ),
            'youtube' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => TRUE,
            ),
            'website' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table users_social_networks
        $this->dbforge->create_table("users_social_networks", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `users_social_networks` ADD FOREIGN KEY(`user_id`) REFERENCES auth_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'Name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '0',
            ),
            'custom' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_categories
        $this->dbforge->create_table("wod_categories", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_variation' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '0',
                'unique' => TRUE,
            ),
            'short_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => TRUE,
                'unique' => TRUE,
            ),
            'location' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_variation");
        $this->dbforge->add_key("location");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_exercise_variations_images
        $this->dbforge->create_table("wod_exercise_variations_images", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '0',
                'unique' => TRUE,
            ),
            'short_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => TRUE,
            ),
            'id_type' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'id_target' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'id_contraction' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'id_mechanics' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'reps' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'load' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'distance' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'height' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'time' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'energy' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'tons' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'work' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_type");
        $this->dbforge->add_key("id_target");
        $this->dbforge->add_key("id_contraction");
        $this->dbforge->add_key("id_mechanics");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_exercises
        $this->dbforge->create_table("wod_exercises", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'id user' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'id workout' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'id routine' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'reps' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'time' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'distance' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'load' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'height' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
        ));

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_log
        $this->dbforge->create_table("wod_log", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
            ),
            'excercise_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'time' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'load' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'RM' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'distance' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'height' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'reps' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'power' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'tons' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,3',
                'null' => TRUE,
            ),
            'date' => array(
                'type' => 'DATE',
            ),
            'manual' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("user_id");
        $this->dbforge->add_key("excercise_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_personal_records
        $this->dbforge->create_table("wod_personal_records", TRUE, $attributes);

        // Add foreign Key.
        $this->db->query("ALTER TABLE `wod_personal_records` ADD FOREIGN KEY(`excercise_id`) REFERENCES exercise_variation_log(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `wod_personal_records` ADD FOREIGN KEY(`user_id`) REFERENCES auth_users(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_phases
        $this->dbforge->create_table("wod_phases", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_plan_macrocycle
        $this->dbforge->create_table("wod_plan_macrocycle", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_plan_mesocycle
        $this->dbforge->create_table("wod_plan_mesocycle", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_plan_microcycle
        $this->dbforge->create_table("wod_plan_microcycle", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => '0',
            ),
            'ton' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
            'toff' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
            'rounds' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
            'time' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
            'MTPR' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
            'TPR' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
            'RPR' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
            'MTPE' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
            'TPE' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
            'RPE' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
            'RBS' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
            'RAF' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_routine_types
        $this->dbforge->create_table("wod_routine_types", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_box' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '-1',
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'default' => '0',
            ),
            'id_phase' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_category' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_type' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_sport' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'description' => array(
                'type' => 'MEDIUMTEXT',
                'null' => TRUE,
            ),
            'rounds' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'time' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'max time' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'ton' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'toff' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'MTPR' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'TPR' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'RPR' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'MTPE' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'TPE' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'RPE' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'RBS' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'RAF' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'public' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
            'created_at' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
            'updated_at' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_box");
        $this->dbforge->add_key("user_id");
        $this->dbforge->add_key("id_phase");
        $this->dbforge->add_key("id_category");
        $this->dbforge->add_key("id_type");
        $this->dbforge->add_key("id_sport");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_routines
        $this->dbforge->create_table("wod_routines", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id routine' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'sequence' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'id excercise' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'reps' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'load' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
            'load unit' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => TRUE,
            ),
            'TPS' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'RPS' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'distance' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'energy' => array(
                'type' => 'SMALLINT',
                'constraint' => '6',
                'null' => TRUE,
            ),
            'time' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'height' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id routine");
        $this->dbforge->add_key("id excercise");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_routines_excercises
        $this->dbforge->create_table("wod_routines_excercises", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id_box' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '0',
            ),
            'description' => array(
                'type' => 'MEDIUMTEXT',
            ),
            'created_at' => array(
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ),
            'updated_at' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id_box");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_workouts
        $this->dbforge->create_table("wod_workouts", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id box' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id workout' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'id routine' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'sequence' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->add_key("id box");
        $this->dbforge->add_key("id workout");
        $this->dbforge->add_key("id routine");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wod_workouts_routines
        $this->dbforge->create_table("wod_workouts_routines", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'event_hours_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => TRUE,
            ),
            'event_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
            ),
            'weekday_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
            ),
            'start' => array(
                'type' => 'TIME',
            ),
            'end' => array(
                'type' => 'TIME',
            ),
            'tooltip' => array(
                'type' => 'TEXT',
            ),
            'before_hour_text' => array(
                'type' => 'TEXT',
            ),
            'after_hour_text' => array(
                'type' => 'TEXT',
            ),
            'category' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'available_places' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '10',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("event_hours_id", TRUE);
        $this->dbforge->add_key("event_id");
        $this->dbforge->add_key("weekday_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table wp_event_hours
        $this->dbforge->create_table("wp_event_hours", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'booking_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => TRUE,
            ),
            'event_hours_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => TRUE,
            ),
            'user_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => TRUE,
            ),
            'booking_datetime' => array(
                'type' => 'DATETIME',
            ),
            'validation_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("booking_id", TRUE);
        $this->dbforge->add_key("event_hours_id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table wp_event_hours_booking
        $this->dbforge->create_table("wp_event_hours_booking", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'option_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => TRUE,
            ),
            'option_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '191',
                'unique' => TRUE,
            ),
            'option_value' => array(
                'type' => 'LONGTEXT',
            ),
            'autoload' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => 'yes',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("option_id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wp_options
        $this->dbforge->create_table("wp_options", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'meta_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => TRUE,
            ),
            'post_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => TRUE,
                'default' => '0',
            ),
            'meta_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'meta_value' => array(
                'type' => 'LONGTEXT',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("meta_id", TRUE);
        $this->dbforge->add_key("post_id");
        $this->dbforge->add_key("meta_key");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wp_postmeta
        $this->dbforge->create_table("wp_postmeta", TRUE, $attributes);

        // Add Fields.
        $this->dbforge->add_field(array(
            'ID' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => TRUE,
            ),
            'post_author' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => TRUE,
                'default' => '0',
            ),
            'post_date' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'post_date_gmt' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'post_content' => array(
                'type' => 'LONGTEXT',
            ),
            'post_title' => array(
                'type' => 'TEXT',
            ),
            'post_excerpt' => array(
                'type' => 'TEXT',
            ),
            'post_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => 'publish',
            ),
            'comment_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => 'open',
            ),
            'ping_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => 'open',
            ),
            'post_password' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'post_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
            ),
            'to_ping' => array(
                'type' => 'TEXT',
            ),
            'pinged' => array(
                'type' => 'TEXT',
            ),
            'post_modified' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'post_modified_gmt' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ),
            'post_content_filtered' => array(
                'type' => 'LONGTEXT',
            ),
            'post_parent' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
                'unsigned' => TRUE,
                'default' => '0',
            ),
            'guid' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'menu_order' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'post_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => 'post',
            ),
            'post_mime_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'comment_count' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("ID", TRUE);
        $this->dbforge->add_key("post_author");
        $this->dbforge->add_key("post_name");
        $this->dbforge->add_key("post_parent");
        $this->dbforge->add_key("post_type");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table wp_posts
        $this->dbforge->create_table("wp_posts", TRUE, $attributes);

    }

    /**
     * down (drop tables)
     *
     * @return void
     */
    public function down()
    {
        // Drop table auth_groups
        $this->dbforge->drop_table("auth_groups", TRUE);
        // Drop table auth_login_attempts
        $this->dbforge->drop_table("auth_login_attempts", TRUE);
        // Drop table auth_sessions
        $this->dbforge->drop_table("auth_sessions", TRUE);
        // Drop table auth_users
        $this->dbforge->drop_table("auth_users", TRUE);
        // Drop table auth_users_groups
        $this->dbforge->drop_table("auth_users_groups", TRUE);
        // Drop table biometrics_bp
        $this->dbforge->drop_table("biometrics_bp", TRUE);
        // Drop table biometrics_height
        $this->dbforge->drop_table("biometrics_height", TRUE);
        // Drop table biometrics_weight
        $this->dbforge->drop_table("biometrics_weight", TRUE);
        // Drop table boxes
        $this->dbforge->drop_table("boxes", TRUE);
        // Drop table bs_calendar_settings
        $this->dbforge->drop_table("bs_calendar_settings", TRUE);
        // Drop table bs_coupons
        $this->dbforge->drop_table("bs_coupons", TRUE);
        // Drop table bs_events
        $this->dbforge->drop_table("bs_events", TRUE);
        // Drop table bs_reservations
        $this->dbforge->drop_table("bs_reservations", TRUE);
        // Drop table bs_reserved_time
        $this->dbforge->drop_table("bs_reserved_time", TRUE);
        // Drop table bs_reserved_time_items
        $this->dbforge->drop_table("bs_reserved_time_items", TRUE);
        // Drop table bs_schedule
        $this->dbforge->drop_table("bs_schedule", TRUE);
        // Drop table bs_schedule_days
        $this->dbforge->drop_table("bs_schedule_days", TRUE);
        // Drop table bs_service_days_settings
        $this->dbforge->drop_table("bs_service_days_settings", TRUE);
        // Drop table bs_service_settings
        $this->dbforge->drop_table("bs_service_settings", TRUE);
        // Drop table bs_services
        $this->dbforge->drop_table("bs_services", TRUE);
        // Drop table bs_settings
        $this->dbforge->drop_table("bs_settings", TRUE);
        // Drop table cart
        $this->dbforge->drop_table("cart", TRUE);
        // Drop table custom_fields
        $this->dbforge->drop_table("custom_fields", TRUE);
        // Drop table email_templates
        $this->dbforge->drop_table("email_templates", TRUE);
        // Drop table exercise_basics
        $this->dbforge->drop_table("exercise_basics", TRUE);
        // Drop table exercise_contractions
        $this->dbforge->drop_table("exercise_contractions", TRUE);
        // Drop table exercise_materials
        $this->dbforge->drop_table("exercise_materials", TRUE);
        // Drop table exercise_mechanics
        $this->dbforge->drop_table("exercise_mechanics", TRUE);
        // Drop table exercise_movements
        $this->dbforge->drop_table("exercise_movements", TRUE);
        // Drop table exercise_muscles
        $this->dbforge->drop_table("exercise_muscles", TRUE);
        // Drop table exercise_sports
        $this->dbforge->drop_table("exercise_sports", TRUE);
        // Drop table exercise_targets
        $this->dbforge->drop_table("exercise_targets", TRUE);
        // Drop table exercise_types
        $this->dbforge->drop_table("exercise_types", TRUE);
        // Drop table exercise_variation_log
        $this->dbforge->drop_table("exercise_variation_log", TRUE);
        // Drop table exercise_variations
        $this->dbforge->drop_table("exercise_variations", TRUE);
        // Drop table exercise_variations_basics
        $this->dbforge->drop_table("exercise_variations_basics", TRUE);
        // Drop table exercise_variations_contractions
        $this->dbforge->drop_table("exercise_variations_contractions", TRUE);
        // Drop table exercise_variations_materials
        $this->dbforge->drop_table("exercise_variations_materials", TRUE);
        // Drop table exercise_variations_mechanics
        $this->dbforge->drop_table("exercise_variations_mechanics", TRUE);
        // Drop table exercise_variations_movements
        $this->dbforge->drop_table("exercise_variations_movements", TRUE);
        // Drop table exercise_variations_muscles_primary
        $this->dbforge->drop_table("exercise_variations_muscles_primary", TRUE);
        // Drop table exercise_variations_muscles_secondary
        $this->dbforge->drop_table("exercise_variations_muscles_secondary", TRUE);
        // Drop table exercise_variations_targets
        $this->dbforge->drop_table("exercise_variations_targets", TRUE);
        // Drop table exercise_variations_types
        $this->dbforge->drop_table("exercise_variations_types", TRUE);
        // Drop table geo_comunidades
        $this->dbforge->drop_table("geo_comunidades", TRUE);
        // Drop table geo_countries
        $this->dbforge->drop_table("geo_countries", TRUE);
        // Drop table geo_municipios
        $this->dbforge->drop_table("geo_municipios", TRUE);
        // Drop table geo_provincias
        $this->dbforge->drop_table("geo_provincias", TRUE);
        // Drop table log_boxes
        $this->dbforge->drop_table("log_boxes", TRUE);
        // Drop table log_members
        $this->dbforge->drop_table("log_members", TRUE);
        // Drop table log_total_clients
        $this->dbforge->drop_table("log_total_clients", TRUE);
        // Drop table log_users
        $this->dbforge->drop_table("log_users", TRUE);
        // Drop table ms_gateways
        $this->dbforge->drop_table("ms_gateways", TRUE);
        // Drop table ms_iban_users
        $this->dbforge->drop_table("ms_iban_users", TRUE);
        // Drop table ms_memberships
        $this->dbforge->drop_table("ms_memberships", TRUE);
        // Drop table ms_memberships_services
        $this->dbforge->drop_table("ms_memberships_services", TRUE);
        // Drop table ms_memberships_users
        $this->dbforge->drop_table("ms_memberships_users", TRUE);
        // Drop table ms_memberships_users_coupons
        $this->dbforge->drop_table("ms_memberships_users_coupons", TRUE);
        // Drop table ms_payments
        $this->dbforge->drop_table("ms_payments", TRUE);
        // Drop table ms_payments_deleted
        $this->dbforge->drop_table("ms_payments_deleted", TRUE);
        // Drop table ms_settings
        $this->dbforge->drop_table("ms_settings", TRUE);
        // Drop table news
        $this->dbforge->drop_table("news", TRUE);
        // Drop table nutrition_food_groups
        $this->dbforge->drop_table("nutrition_food_groups", TRUE);
        // Drop table nutrition_foods
        $this->dbforge->drop_table("nutrition_foods", TRUE);
        // Drop table nutrition_foods_requirements
        $this->dbforge->drop_table("nutrition_foods_requirements", TRUE);
        // Drop table nutrition_log
        $this->dbforge->drop_table("nutrition_log", TRUE);
        // Drop table orders
        $this->dbforge->drop_table("orders", TRUE);
        // Drop table settings
        $this->dbforge->drop_table("settings", TRUE);
        // Drop table users_geo
        $this->dbforge->drop_table("users_geo", TRUE);
        // Drop table users_social_networks
        $this->dbforge->drop_table("users_social_networks", TRUE);
        // Drop table wod_categories
        $this->dbforge->drop_table("wod_categories", TRUE);
        // Drop table wod_exercise_variations_images
        $this->dbforge->drop_table("wod_exercise_variations_images", TRUE);
        // Drop table wod_exercises
        $this->dbforge->drop_table("wod_exercises", TRUE);
        // Drop table wod_log
        $this->dbforge->drop_table("wod_log", TRUE);
        // Drop table wod_personal_records
        $this->dbforge->drop_table("wod_personal_records", TRUE);
        // Drop table wod_phases
        $this->dbforge->drop_table("wod_phases", TRUE);
        // Drop table wod_plan_macrocycle
        $this->dbforge->drop_table("wod_plan_macrocycle", TRUE);
        // Drop table wod_plan_mesocycle
        $this->dbforge->drop_table("wod_plan_mesocycle", TRUE);
        // Drop table wod_plan_microcycle
        $this->dbforge->drop_table("wod_plan_microcycle", TRUE);
        // Drop table wod_routine_types
        $this->dbforge->drop_table("wod_routine_types", TRUE);
        // Drop table wod_routines
        $this->dbforge->drop_table("wod_routines", TRUE);
        // Drop table wod_routines_excercises
        $this->dbforge->drop_table("wod_routines_excercises", TRUE);
        // Drop table wod_workouts
        $this->dbforge->drop_table("wod_workouts", TRUE);
        // Drop table wod_workouts_routines
        $this->dbforge->drop_table("wod_workouts_routines", TRUE);
        // Drop table wp_event_hours
        $this->dbforge->drop_table("wp_event_hours", TRUE);
        // Drop table wp_event_hours_booking
        $this->dbforge->drop_table("wp_event_hours_booking", TRUE);
        // Drop table wp_options
        $this->dbforge->drop_table("wp_options", TRUE);
        // Drop table wp_postmeta
        $this->dbforge->drop_table("wp_postmeta", TRUE);
        // Drop table wp_posts
        $this->dbforge->drop_table("wp_posts", TRUE);
    }

}
