<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**  
 * 
 * @package     CodeIgniter 
 * @category    Libraries 
 * @version     3.0 
 *
 * @author kinsay <kinsay@gmail.com>
 * @since 0.2 2020-03
 *
 * @requires php-oauth2 (placed in the third_party folder)
 * @requires Guzzle (placed in the third_party folder)
 * 
 * @requires gocardless config file (placed in the config directory)
 */ 

// Class: Oauth
// used to implement Oauth2
class Oauth extends CI_Controller {

    function __construct() 
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') 
        {
            header('HTTP/1.1 204 No Content');
        }
     
        parent::__construct();

        //INIT
        $this->load->library(array('ion_auth'));
        $this->load->model('box_model', 'box');
        $this->box->set_box();

        $client = null; //oauth client
        $gateway = null;
        $enviroment = null;
        
        //include OAuth bindings
        require APPPATH .'third_party/php-oauth2/src/OAuth2/Client.php';  
        require APPPATH .'third_party/php-oauth2/src/OAuth2/GrantType/IGrantType.php'; 
        require APPPATH .'third_party/php-oauth2/src/OAuth2/GrantType/AuthorizationCode.php'; 
        
    }

    //function: index
    function index() 
    {
        redirect('/staff/gateways','refresh');
    }

    /////////////////////////////////////
    // Section: GOCARDLESS GATEWAY
    ///////////////////////////////////

    //function: gocardless
    // Prepares the OAuth client to be used with gocardless and calls the "gc_methods" depending on action received
    function gocardless($action = null, $params = null)
    {
        $allowed_groups = array('sadmin', 'admin');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $this->load->library('gocardless');
            if($this->gocardless->gc_config['integration'] == 'partner' AND $this->gc_setUp())
            {
                if($this->gocardless->is_configured() AND $this->gocardless->is_active())
                {   
                    switch ($action) {
                        case 'connect':
                            if(!$this->gocardless->is_connected())
                            {
                                $customer_data = $this->getCustomerData();
                                $authorizeUrl = $this->gocardless->gc_oauth_flow($customer_data);
                                if($authorizeUrl !== FALSE) 
                                {                                    
                                    echo "<script>window.location.href='". $authorizeUrl."';</script>";
                                    //header("Location: " . $authorizeUrl);
                                }
                                else
                                {
                                    $this->session->set_flashdata('error', 'Operación no válida.');
                                    redirect('/staff/gateways');
                                }
                            }
                            else
                            {
                               $this->session->set_flashdata('error', $this->gocardless->api_error); 
                               redirect('/staff/gateways');
                            }
                            break;

                        case 'callback':
                            if(!$this->gocardless->is_ready())
                            {
                                $verifyUrl = $this->gocardless->gc_oauth_callback();

                                if($verifyUrl === TRUE)
                                {
                                    $status = $this->gocardless->get_oauth('status');
                                    $this->onboard_complete($status);
                                }
                                else if($verifyUrl == FALSE)
                                {
                                    $this->session->set_flashdata('error', $this->gocardless->api_error); 
                                    //redirect('/staff/gateways'); 
                                }
                                else
                                {
                                    echo "<script>window.location.href='". $verifyUrl."';</script>";
                                }
                            }
                            else
                            {
                               $this->session->set_flashdata('error', $this->gocardless->api_error); 
                               redirect('/staff/gateways'); 
                            }
                            
                            break;

                        case 'verify':
                            echo "<script>window.location.href='". $this->gocardless->oauth_config['verify_url']."';</script>";
                            break;

                        case 'update':
                            $status = $this->gocardless->get_oauth('status');
                            $this->gocardless->update_oauth_status(true);
                            redirect('/staff/gateways'); 
                            break;

                        case 'onboard_complete':
                            $status = $this->gocardless->get_oauth('status');
                            $this->gocardless->update_oauth_status(true);
                            $this->onboard_complete($status);
                            break;

                        case 'revoke':
                            if($this->gocardless->is_ready())
                            {
                                $id = $this->gocardless->get_oauth('id');
                                $demo = $this->gocardless->get_oauth('demo');
                                $this->gocardless->revoke($id, $demo);
                            }
                            else
                               $this->session->set_flashdata('error', $this->gocardless->api_error);
                                
                            redirect('/staff/gateways','refresh');
                            break;
                        
                        default:
                            redirect('/staff/gateways','refresh');
                            break;
                    }
                }
                else
                {
                    echo $this->gocardless->api_error;
                }
            }
            else
            {
                echo 'error al inicilizar la pasarela';
            }   
        } 
        else
        {
            if(!$this->ion_auth->logged_in())
                echo 'no logged';

            if(!$this->ion_auth->in_group(array('sadmin', 'admin')))
                echo 'no in group';
        }    
    }

    function getCustomerData()
    {
        // gather customer data
        $result = $this->box->genericGet('first_name, last_name, email, phone', array('id' => $this->session->userdata('user_id')), 'auth_users', null, null, false);

        $customer_data = array("given_name" => $result[0]->first_name,
                                "family_name" => $result[0]->last_name,
                                "email" => $result[0]->email,
                                "country_code" => "ES",
                                "language" => "ES"
                            );

        $result = $this->box->genericGet('name', array('id' => $this->session->userdata('box_id')), 'boxes', null, null, false);
        $customer_data['organisation_name'] = $result[0]->name;

        return $customer_data;
    }


    /**
     * function: gc_setUp
     */
    private function gc_setUp($box_id = null)
    {
        if($box_id !== null) 
            $this->box->set_box($box_id);
        else
            $this->box->set_box();

        return $this->gocardless->set_up($this->box->box_id);
    }


    function onboard_complete($status = null)
    {
        $demo = '';
        if(isset($this->gocardless->enviroment))
            $demo = ($this->gocardless->enviroment == 'sandbox')? '[modo pruebas]' : ''; 

        switch ($status) {
            case 'in_review':
                $this->session->set_flashdata('success', 'Pasarela para Domiciliaciones bancarias (GoCardless) '.$demo.' conectada con éxito. No obstante, gocardless.com está en proceso de verificación de su cuenta. Hasta que no sea verificada por ellos no podrá recibir cobros. Este proceso suele ser rápido.');
                break;

            case 'successful':
                $this->session->set_flashdata('success', 'Pasarela para Domiciliaciones bancarias (GoCardless '.$demo.') conectada y su cuenta de esta pasarela está verificada.');
                break;

            case 'demo':
                $demo = '[modo pruebas]';
                $this->session->set_flashdata('success', 'Pasarela para Domiciliaciones bancarias (GoCardless '.$demo.') conectada. Tenga en cuenta que su cuenta de GoCardless debe ser verificada por sus responsables para poder operar con ella.');
                break;
        }

        redirect('/staff/gateways'); 
    }

    

   

}
