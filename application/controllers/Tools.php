<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Tools extends CI_Controller {

    function __construct() 
    {
        parent::__construct();
        // can only be called from the command line in a testing or development enviroment    
    }

    function check()
    {
        if(ENVIRONMENT == 'production' AND !$this->input->is_cli_request())
        {
            log_message('debug',print_r('WARNING: intento ejecución indebida de TOOLS.', TRUE));
            show_404();
            exit;
        }
    }

    public function help() 
    {
        $this->check();
        if($this->input->is_cli_request())
        {
            $result = "The following are the available command line interface commands (php index.php tools)\n\n";
            $result .= "SQL2migration [\"table_name\"]          to Create new migration file/s (no foreign keys). The table_name is optional.\n";
            $result .= "list_migrations                         to list available migrations\n";
            $result .= "migrate [\"version_number\"]            to Run all migrations. The version number is optional.\n";
            $result .= "create_empty_file [\"table_name\"]    to Create an empty migration files.\n>";
            $result .= "seeder \"file_name\"                    to Create a new seed file.\n";
            $result .= "seed \"file_name\"                      to Run the specified seed file.\n";

            echo $result . PHP_EOL;
        }
        else
        {
            $result = "The following are the available command line interface commands (php index.php tools)<br>";
            $result .= "<b>SQL2migration</b> [\"table_name\"]          to Create new migration file/s (no foreign keys). The table_name is optional.<br>";
            $result .= "<b>list_migrations </b>                        to list available migrations<br>";
            $result .= "<b>migrate</b> [\"version_number\"]            to Run all migrations. The version number is optional.<br>";
            $result .= "<b>create_empty_file</b> [\"table_name\"]    to Create an empty migration files.<br>";
            $result .= "<b>seeder</b> \"file_name\"                    to Create a new seed file.<br>";
            $result .= "<b>seed</b> \"file_name\"                      to Run the specified seed file.<br>";

            echo $result . PHP_EOL;
        }

        
    }

    //for testing porpuses
    function test($param = null)
    {
        $this->load->model('box_model', 'box');
        $this->box->set_box(1);

        $fields = array('auth_users' => array('email', 'first_name'),
                        'auth_users_groups' => array('box_id','group_id'));

        $params = array('auth_users' => array('id' => 6));

        $join = array('auth_users_groups' => array( 'ref_table' => 'auth_users', 
                                                    'ref_field' => 'id', 
                                                    'new_field' => 'user_id'));

        $result = $this->box->getRows('auth_users', $fields, $params, null, null, true, $join);
        echo '<pre>'; print_r($result); echo '</pre>';
        echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';
    }



    function viewlog() 
    {
        $this->check();
        $this->load->library('CILogViewer', NULL, 'logViewer'); 

        echo $this->logViewer->showLogs();        
        return;
    }

    function SQL2migration($param = null)
    {
        $this->check();
        $this->load->library('Migration_lib');

        if($param == 'all')
            $this->migration_lib->generate();
        else if($param != null)
            $this->migration_lib->generate($param);
        else
            echo 'Error: $param needed in request url, such as: <br>"all" for whole database or <br>"table_name" for a single table';
    }

    function migrate($version = null)
    {
        $this->check();
        $this->load->library('migration');

        if ($this->migration->current() === FALSE)
        {
                show_error($this->migration->error_string());
        }

        if ($version != null) 
        {
            if ($this->migration->version($version) === FALSE) 
            {
                show_error($this->migration->error_string());
            } else {
                echo "Migrations run successfully" . PHP_EOL;
                return TRUE;
            }
        }
        else if ($this->migration->latest() === FALSE) 
        {
            show_error($this->migration->error_string());
        } 
        else 
        {
            echo "Migrations run successfully" . PHP_EOL;
            return TRUE;
        }

        return FALSE;

    }

    function list_migrations()
    {
        $this->check();
        $this->load->library('migration');

        $migrations = $this->migration->find_migrations();
        if( empty($migrations))
        {
            echo "No migrations available" . PHP_EOL;
            return FALSE;
        }
        else
        {
            echo '<pre>';
                print_r($migrations);
            echo '</pre>';
            return TRUE;
        }
    }

    function create_empty_file($name) 
    {
        $this->check();
        $this->load->library('Migration_lib');

        $this->migration_lib->create_empty_file($name);
    }

    

}
