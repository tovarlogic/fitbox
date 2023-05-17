<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Select extends MY_Controller {

    function __construct() 
    {
        parent::__construct();
        $this->load->database();

        $this->load->library(array('ion_auth','form_validation','encryption'));

        $this->load->helper(array('language'));

        $this->load->model('box_model', 'box');

        $this->output->enable_profiler(FALSE);

        if ($this->ion_auth->logged_in() && $this->ion_auth->in_group('athlete'))
        {
            $this->box->set_box();
        }
        
    }


    function index() 
    {
        $staff = array('sadmin', 'admin', 'coach', 'finance', 'rrhh', 'comercial', 'marketing');

        if ( $this->ion_auth->in_group('athlete') AND $this->ion_auth->in_group($staff) )
        {
            //$data['user_data'] = $this->ath->getUserData();
            $data['user'] = $this->box->getUser($this->session->userdata('user_id'));

            $this->show_view($data, 'select', 'dashboard');
        }
        else
        {
             redirect(base_url());
        }
    }



}
