<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Guest extends CI_Controller {

    function __construct() 
    {
        parent::__construct();
        $this->load->database();

        $this->load->library(array('ion_auth','form_validation'));

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
        $allowed_groups = array('guest');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            //$data['user_data'] = $this->ath->getUserData();
            $data['user'] = $this->box->getUser($this->session->userdata('user_id'));

            if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
            {  
                $this->load->view('backend/guest/dashboard', $data);
            }
            else
            {
                $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                $this->load->view('backend/guest/partials/blank', $data2);
                $this->load->view('backend/guest/dashboard', $data);
            }
        }
        else
        {
            redirect('auth', 'refresh');
        }
    }



}