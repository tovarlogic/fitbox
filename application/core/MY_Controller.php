<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
 
class MY_Controller extends CI_Controller
{ 
    function __construct()
    {
        parent::__construct();
    }

    function show_view($data, $controller, $page)
    {
        if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
        {  
            $this->load->view('backend/'.$controller.'/'.$page, $data);
        }
        else
        {
            $data2['box'] =  $this->box->getBox();
            $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
            $data2['many_profiles'] = FALSE;
            $staff = array('sadmin', 'admin', 'coach', 'finance', 'rrhh', 'comercial', 'marketing');
            if ( $this->ion_auth->in_group('athlete') AND $this->ion_auth->in_group($staff) )
            {
                $data2['many_profiles'] = TRUE;
            }
            $this->load->view('backend/'.$controller.'/partials/blank', $data2);
            $this->load->view('backend/'.$controller.'/'.$page, $data);
            $this->load->view('backend/partials/footer');
        }
    }  
     
}

?>
