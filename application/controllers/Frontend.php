<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Frontend extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('frontend/layouts/landing');
		//redirect(base_url() . 'auth/login', 'refresh');
	}

	public function login()
	{
		if ($this->session->userdata('sudo_login') == 1)
            redirect(base_url() . 'index.php/sudo', 'refresh');
        
        else if ($this->session->userdata('staff_login') == 1)
            redirect(base_url() . 'index.php/staff', 'refresh');
        
        else if ($this->session->userdata('athlete_login') == 1)
            redirect(base_url() . 'index.php/athlete', 'refresh');
        
		$this->load->view('frontend/layouts/login');
	}

	public function register()
	{
		$this->load->view('frontend/layouts/register');
	}
}
