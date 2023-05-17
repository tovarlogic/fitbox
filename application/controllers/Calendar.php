<?php

class Calendar extends CI_Controller {

    function __construct() 
    {
        parent::__construct();
        $this->load->database();

        $this->load->library(array('ion_auth','form_validation','booking_lib'));

        $this->load->helper(array('language'));

        $this->load->model('box_model', 'box');
        $this->load->model('logs_model', 'logs');
        $this->load->model('booking_model', 'booking');
        $this->load->model('ion_auth_model', 'ion');

        $this->lang->load(array('auth','fitbox','booking'));

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $groups = array('sadmin', 'admin', 'fcoach', 'coach', 'finance', 'rrhh', 'comercial', 'marketing', 'athlete');

        if($this->box->set_box()) $this->set_box($this->box->box_id);
              
    }

    private function set_box($box_id)
    {
        $this->booking->set_box($box_id); 
    }

    function index($cal_code = null) 
    {
        $logged = false;

        if($cal_code == null)
        //if no calendar code
        {
            $allowed_groups = array('athlete', 'sadmin', 'admin', 'rrhh', 'finance', 'fcoach', 'coach', 'comercial', 'marketing');

            if($this->ion_auth->check_login($allowed_groups))
            //if user is logged in
            {
                //set box
                $box_id = $this->box->box_id;
                $this->set_box($box_id);
                $logged = true;
            } 
            else redirect('auth/login', 'refresh');

        }
        else
        {
            //get calendar settings
        }

        

        $group = null;
        if ($this->ion_auth->logged_in()) 
            $group = $this->ion->get_user_group();
        else if(!$allow_public) 
            redirect('auth/login', 'refresh');

        if(!$this->box->set_box() && $box_id != null)
        {
            if($allow_public)
            {
                if(!$this->box->set_box($box_id)) die();
                $this->set_box($box_id);
            }
            else
            {
                //pendiente: show login
                redirect('auth/login', 'refresh');
            }
        }

        $data['page_title'] = "Fitbox | Calendar";
        $data['services'] = $this->booking->get_services(1);
        $data['box_id'] = $box_id;
        $data['logged'] = $logged;
        $data['group'] = $group;
        $data['next_week'] = $this->booking_lib->showNextWeek();      

        $data['serviceID'] = $this->input->get('serviceID', TRUE); //XSS filtered
        if ($data['serviceID'] == -1) $data['serviceID'] = null;

        $iMonth = ($this->input->get('month', TRUE))? $this->input->get('month', TRUE) : date('n'); //XSS filtered
        $iYear = ($this->input->get('year', TRUE))? $this->input->get('year', TRUE) : date('Y'); //XSS filtered


        list($data['calendar'], $data['calendar_mobile']) = $this->booking_lib->setupCalendar($iMonth, $iYear, $this->box->box_id, $group, $data['serviceID']);

        $data['calendar_vars'] = $this->booking_lib->getCalendarVars($iYear, $iMonth, $this->box->box_id, $data['serviceID']);

        list ($data['calendarHeader'], $data['calendarHeader_mobile']) = $this->booking_lib->getCalendarHeader();

        if($this->input->post('ajax'))
        {  
            $this->load->view('calendar/calendar', $data);
        }
        else
        {
            $this->load->view('calendar/calendar', $data);
        }
    
    }

    function details()
    {
        $allowed_groups = array('athlete', 'sadmin', 'admin', 'rrhh', 'finance', 'fcoach', 'coach', 'comercial', 'marketing');

        if ($this->ion_auth->check_login($allowed_groups, false) )
        {
            $data['id'] = $this->input->post('id', TRUE);
            $data['serviceID'] = $this->input->post('service', TRUE);
            $data['dateTime'] = $this->input->post('time', TRUE);
            $data['group'] = $this->input->post('group', TRUE);
            $box = $this->box->box_id;

            $data['coach'] = $this->booking_lib->getScheduledCoach($data['dateTime'], $box, $data['serviceID']);
            $data['subscribed_services'] = $this->booking->getSubscribedServices();
            $data['asistentes'] = $this->booking->getWebBookingsList($data['dateTime'], $box, $data['serviceID']);
            
            list($memberships, $err_msg) = $this->booking_lib->userCanBook($data['dateTime'], $box, $data['serviceID']);

            if($memberships['user_can_book'] === TRUE)
            {
                $data['available'] = TRUE; 
                $data['open'] = TRUE;
                $data['reserved'] = FALSE;
            }
            else
            {
                $data['available'] = FALSE; 
                $data['open'] = $this->booking_lib->isServiceBookable($data['dateTime'], $box, $data['serviceID']);
                $data['reserved'] = $this->booking->isReservedByUser($data['dateTime'], $box, $data['serviceID']);
            }

            $this->load->view('calendar/asistentes', $data);
        }
        else
        {
            echo '<font color="black"><b>Error:</b> Sesión caducada. Refresque la página.</font>';
        }
    }

    function book()
    {
        $allowed_groups = array('athlete', 'staff', 'admin');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $serviceID = $this->input->post('service', TRUE);
            $dateTime = $this->input->post('time', TRUE);
            $box = $this->box->box_id;
            $user = $this->session->userdata('user_id');

            if($this->booking_lib->addWebBooking($dateTime, $box, $serviceID, $user, 1))
            {
                return json_encode(array('message' => 'success'));
            }
            else
            {
                return json_encode(array('message' => 'error'));
            }
        }
    }

    function cancel()
    {
        $allowed_groups = array('athlete', 'staff', 'admin');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $serviceID = $this->input->post('service', TRUE);
            $dateTime = $this->input->post('time', TRUE);
            $box = $this->box->box_id;
            $user = $this->session->userdata('user_id');

            if($this->booking_lib->cancelWebBooking($dateTime, $box, $serviceID, $user, 1))
            {
                return json_encode(array('message' => 'success'));
            }
            else
            {
                return json_encode(array('message' => 'error'));
            }
        }
    }

}


?>