<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class: Athlete
 */
class Athlete extends MY_Controller {

    
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
        $this->load->database();

        $this->load->library(array('ion_auth','form_validation','iban','encryption','dates_lib','toolbox_lib', 'biometrics_lib','booking_lib'));


        $this->load->helper(array('language'));

        $this->load->model('athlete_model', 'ath');
        $this->load->model('box_model', 'box');
        $this->load->model('biometric_model', 'bio');
        $this->load->model('wod_model', 'wod');
        $this->load->model('nutrition_model', 'food');
        $this->load->model('payment_model', 'pay');

        $this->config->load('settings', TRUE);

        $this->lang->load(array('auth','fitbox','booking','date_lang','calendar_lang'));

        $this->output->enable_profiler(FALSE);

        if ($this->ion_auth->logged_in() && $this->ion_auth->in_group('athlete'))
        {
            $this->box->set_box();

            $this->ath = new Athlete_model($this->session->userdata('user_id'));
            $this->bio->setUser($this->ath->user_id);
            $this->wod->setUser($this->ath->user_id);
            $this->food->setUser($this->ath->user_id);
            $this->pay->set_box($this->box->box_id);
        }

        

    }


    function index() 
    {
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups) )
        {   
            $this->show_view($data, 'athlete', 'calendar');
        }
        else
        {
            redirect('auth', 'refresh');
        }

    }
/////////////////////////////////////////////////////////////////////////
//  SECTION: CUENTA
/////////////////////////////////////////////////////////////////////////

    /**
     * Function: profile
     *
     * @param  [type] $action [description]
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function profile($action = null, $params = null)
    {
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            switch ($action) {              
                case null:
                    $this->process_profile();
                    break;

                case 'edit':
                    $this->process_profile_edit();
                    break;

                default:
                    $this->profile();
                    break;
            }
        }
    }

    /**
     * Function: process_profile
     *
     * @return [type] [description]
     */
    private function process_profile()
    {
        $user_id = $this->session->userdata('user_id');
        $data['user'] = $this->box->getUser($user_id);
        $data['user']->age = $this->dates_lib->birth_to_age($data['user']->birth_date);
        $data['user']->weight = $this->bio->getLastWeight()->weight;
        $data['user']->height = $this->bio->getLastHeight()->height;
        $data['user_memberships'] = $this->ath->get_memberships($this->box->box_id);
        $data['transactions'] = $this->pay->getUserPayments($user_id, 'all');
        //get active gateways
        $params = array('box_id' => $this->box->box_id,
                        'type' => 'online',
                        'active' => 1);
        $gateways = $this->pay->getGateways($params); 
        $gateways2 = array();

        foreach ($gateways as $key => $gtw ) 
        {
            if($gtw->name == 'gocardless')
            {
                $params = array('gateway' => $gtw->id, 'box_id' => $this->box->box_id, 'demo' => $gtw->demo);

                $oauth = $this->pay->getOauthOrg($params);
                $gateways[$key]->status = $oauth->status;
            }

            $gateways2[$gtw->id] = $gateways[$key];
            $gateways[$gtw->name] = $gateways[$key];
            unset($gateways[$key]);
        }

        $data['gateways'] = $gateways2;

        // check available payment methods for each membership
        if(!empty($data['user_memberships']))
        {
            foreach ($data['user_memberships'] as $key => $mem) 
            {
                if($mem->status == 'p' OR $mem->status == 'y')
                {   
                    // Set all online available gateways
                    foreach ($gateways2 as $key2 => $gtw) 
                    {
                        if($gtw->type == 'online')
                        {
                            $data['user_memberships'][$key]->{'gateways'}['gateway_id'] = $key2;
                        }
                    }

                    $params = array('box_id' => $this->box->box_id,
                                    'mu_id' => $mem->id,
                                    'user_id' => $user_id,
                                    'status' => 'active');
                    $subscriptions = $this->pay->getGatewayTransactions('subscriptions', $params);

                    // Set subscriptions
                    if($subscriptions !== FALSE)
                    {
                        foreach ($subscriptions as $subs) 
                        {
                            if($subs->demo == $gateways2[$subs->gateway]->demo)
                            {
                                $data['user_memberships'][$key]->{'subscription'}['gateway_id'] = $subs->gateway; 
                                $data['user_memberships'][$key]->{'subscription'}['subscription_id'] = $subs->id;  
                                break; break;
                            }
                        }
                    }
                }
            }
        }

        // //get any kind of transaction, not just stripe´s, for the membership that may be in conflict with a new renovation intent
        
        // if(isset($gateways['stripe']))
        // {
        //     $status = array('pending'); 
        //     $pending_trans = $this->pay->getUserPayments($user_id, $gateways['stripe']->demo, $status, $this->box->box_id);
        //     log_message('debug',print_r($pending_trans,true));
        //     //get any kind of iban mandates that may be in conflict with a new renovation intent
        //     $data['subscriptions'] = array();
        //     $params = array('user_id' => $user_id,
        //                     'box_id' => $this->box->box_id,
        //                     'status' => array('created', 'active') ); 
        //     $subs = $this->pay->getGatewayTransactions('subscriptions', $params);
        //     if(!empty($subs))
        //     {
        //        foreach ($subs as $key => $value) 
        //         {
        //             $data['subscriptions'][$value->mu_id] = $value;
        //         } 
        //     }
        //     $data['mu_card_blocked'] = array();

        //     if(!empty($pending_trans))
        //     {
        //         foreach ($pending_trans as $trans ) 
        //         {
        //             if($gateways[$trans->pp]->demo == $trans->demo)
        //                 $data['mu_card_blocked'][$trans->mu_id] = true;
        //         }
        //     }
        // }
        

        if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
        {  
            $this->load->view('backend/athlete/profile', $data);
        }
        else
        {
            $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

            $this->load->view('backend/athlete/partials/blank', $data2);
            $this->load->view('backend/athlete/profile', $data);
            $this->load->view('backend/partials/footer');
        }
    }

    /**
     * Function: process_profile_edit
     *
     * @return [type] [description]
     */
    private function process_profile_edit()
    {
        $user = $this->box->getUser($this->ath->user_id);

        $tables = $this->config->item('tables','ion_auth');
        $identity_column = $this->config->item('identity','ion_auth');

        // validate form input
        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|callback_alpha_space');
        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required|callback_alpha_space');
        if($identity_column!=='email')
        {
            $this->form_validation->set_rules('identity',$this->lang->line('create_user_validation_identity_label'),'required|is_unique['.$tables['users'].'.'.$identity_column.']');
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
        }
        else
        {
            if($this->input->post('email') == $user->email)
            {
                $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
            }else{
                $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique[' . $tables['users'] . '.email]');
                $this->form_validation->set_rules('email2', $this->lang->line('create_user_validation_email2_label'), 'required|valid_email|matches[email]');
            }
            
            $this->form_validation->set_rules('username', $this->lang->line('create_user_validation_lname_label'), 'required|callback_alpha_space');
        }
        
        if($this->input->post('DNI') != $user->DNI && $this->input->post('DNI') != null)
        {
            $this->form_validation->set_rules('DNI', 'DNI', 'is_unique[' . $tables['users'] . '.DNI]|callback_valid_dni');
        } 

        $this->form_validation->set_rules('gender', 'Sexo', 'required');
        $this->form_validation->set_rules('phone', 'Telefono', 'required|regex_match[/^[0-9]{9}$/]'); //{9} for 9 digits number

        if ($this->form_validation->run() == true)
        {
            if($this->input->post('year') != null AND $this->input->post('month') != null AND $this->input->post('day') != null)
                $birth_date = $this->input->post('year')."-".$this->input->post('month')."-".$this->input->post('day');
            else
                $birth_date = NULL;     

            $additional_data = array(
                'username'  => strtolower($this->input->post('username')),
                'first_name' => strtolower($this->input->post('first_name')),
                'last_name'  => strtolower($this->input->post('last_name')),
                'email'    => $this->input->post('email'),
                'DNI'    => $this->input->post('DNI'),
                'gender'      => $this->input->post('gender'),
                'phone'      => $this->input->post('phone'),
                'birth_date' => $birth_date,

            );

            if($this->input->post('DNI') == null) $additional_data['DNI'] = NULL;

            $this->db->trans_start();
                if($this->box->editUser($this->ath->user_id, $additional_data))
                {
                    $this->session->set_flashdata('success', 'Datos de usuario actualizados.'); 
                }
                else
                    $this->session->set_flashdata('error', 'No se pudo editar el usuario.');
            $this->db->trans_complete();

            $this->session->set_flashdata('message', $this->ion_auth->messages());

            $this->profile();
        }
        else
        {

            $data['page_title'] = "Editar datos";

            $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
            $data['user_id'] = $this->ath->user_id;

            if (!empty(form_error('first_name'))) $class = "error"; else $class = "valid";

            $data['first_name'] = array(
                'name'  => 'first_name',
                'id'    => 'first_name',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'required' => '',
                'aria-required' => "true",
                'value' => $this->form_validation->set_value('first_name', $user->first_name),
            );

            if (!empty(form_error('last_name'))) $class = "error"; else $class = "valid";

            $data['last_name'] = array(
                'name'  => 'last_name',
                'id'    => 'last_name',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'required' => '',
                'aria-required' => "true",
                'value' => $this->form_validation->set_value('last_name', $user->last_name),
            );

            if (!empty(form_error('username'))) $class = "error"; else $class = "valid";

            $data['username'] = array(
                'name'  => 'username',
                'id'    => 'username',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'value' => $this->form_validation->set_value('username', $user->username),
            );

            if (!empty(form_error('email')) OR !empty(form_error('email2'))) $class = "error"; else $class = "valid";

            $data['email'] = array(
                'name'  => 'email',
                'id'    => 'email',
                'class' => 'form-control '.$class,
                'type'  => 'email',
                'required' => '',
                'aria-required' => "true",
                'value' => $this->form_validation->set_value('email', $user->email),
            );

            if (!empty(form_error('email')) OR !empty(form_error('email2'))) $class = "error"; else $class = "valid";

            $data['email2'] = array(
                'name'  => 'email2',
                'id'    => 'email2',
                'class' => 'form-control '.$class,
                'type'  => 'email',
                'required' => '',
                'aria-required' => "true",
                'value' => $this->form_validation->set_value('email2', $user->email),
                'equalTo' => 'email',
            );

            if (!empty(form_error('DNI'))) $class = "error"; else $class = "valid";

            $data['DNI'] = array(
                'name'  => 'DNI',
                'id'    => 'DNI',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'value' => $this->form_validation->set_value('DNI', $user->DNI),
            );

            if (!empty(form_error('gender'))) $class = "error"; else $class = "valid";

            $data['gender'] = array(
                'name'  => 'gender',
                'id'    => 'gender',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'required' => '',
                'aria-required' => "true",
                'value' => $this->form_validation->set_value('gender', $user->gender), 
            );

            $date = ($user->birth_date != null)? explode('-',$user->birth_date,3) : null;
            $data['year_status'] = ($date != null)? $date[0]: null;
            $data['month_status'] = ($date != null)? $date[1]: null;
            $data['day_status'] = ($date != null)? $date[2]: null;

            if (!empty(form_error('year'))) $class = "error"; else $class = "valid";

            $data['year'] = array(
                'name'  => 'year',
                'id'    => 'year',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'value' => $this->form_validation->set_value('year',$date[0]), 
            );

            if (!empty(form_error('month'))) $class = "error"; else $class = "valid";

            $data['month'] = array(
                'name'  => 'month',
                'id'    => 'month',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'value' => $this->form_validation->set_value('month',$date[1]), 
            );

            if (!empty(form_error('day'))) $class = "error"; else $class = "valid";

            $data['day'] = array(
                'name'  => 'day',
                'id'    => 'day',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'value' => $this->form_validation->set_value('day', $date[2]), 
            );

            $data['sex'] = $user->gender;
            $data['date'] = $date;
            $data['genders'] = array('' =>'-- Seleccione --', 'M' =>'Masculino', 'F'=> 'Femenino');
            $data['days'] = $this->toolbox_lib->generate_list(1,31, TRUE);
            $data['months'] = $this->toolbox_lib->generate_list(1,12, TRUE);
            $data['years'] = $this->toolbox_lib->generate_list(date('Y')-80,date('Y'), TRUE);

            if (!empty(form_error('phone'))) $class = "error"; else $class = "valid";

            $data['phone'] = array(
                'name'  => 'phone',
                'id'    => 'phone',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'required' => '',
                'aria-required' => "true",
                'value' => $this->form_validation->set_value('phone', $user->phone), 
            );


            if($this->input->post('ajax') OR $this->input->is_ajax_request())
            {
                $this->load->view('backend/athlete/profile_form', $data);
            }
            else
            {
                $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                $data2['many_profiles'] = FALSE;
                $staff = array('sadmin', 'admin', 'coach', 'finance', 'rrhh', 'comercial', 'marketing');
                if ( $this->ion_auth->in_group('athlete') AND $this->ion_auth->in_group($staff) )
                {
                    $data2['many_profiles'] = TRUE;
                }
                $this->load->view('backend/athlete/partials/blank', $data2);
                $this->load->view('backend/athlete/profile_form', $data);
                $this->load->view('backend/partials/footer');   
            }
        }
    }



    function membership($action = null, $id = null, $gateway = null)
    {
        $allowed_groups = array('athlete');
        $this->pay->set_box($this->box->box_id);

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            // validate form input
            $this->form_validation->set_rules('from', 'from', 'required');
            $this->form_validation->set_rules('to', 'to', 'required');
            $this->form_validation->set_rules('times', 'times', 'required');
            //$this->form_validation->set_rules('pp', 'pp', 'required');
            $this->form_validation->set_rules('rate_amount', 'rate_amount', 'required');

            switch ($action) {
                case 'initial':
                case 'renew':
                    $this->session->sess_regenerate(TRUE); //necessary to avoid problems with stripe callback
                    $this->process_membership_renew($id, $gateway);
                    break;
                
                case 'payment':
                    $this->process_membership_payment($id);
                    break;

                case 'subscribe':
                    $this->process_membership_subscribe($id, $gateway);
                    break;

                case 'flow_completed':
                    $this->process_membership_flow_completed();
                    break; 

                case 'cancel_subscription':
                    $this->process_membership_cancel_subscription($id);
                    break;

                default:
                    $this->profile();
                    break;
            }
        }
    }

    private function process_membership_renew($id)
    {
        $mu = $this->box->getUserMembership($id);
        // check if membership exists AND if user is the owner of that membership
        if($mu !== FALSE AND $mu->user_id == $this->session->userdata('user_id'))
        {
            // check if that membership can be renewed, that is if hasn´t been ended, banned or canceled
            if($mu->status != 'e' AND $mu->status != 'b' AND $mu->status != 'c')
            {
                //check if CARD gateways are active
                $params = array('box_id' => $this->box->box_id,
                                    'type' => 'online',
                                    'methods' => 'card',
                                    'active' => 1);

                $gateways = $this->pay->getGateways($params); 
                
                if(!empty($gateways))
                {
                    // for the moment only STRIPE has been implemented
                    $gateway = $this->pay->getGatewaySettings('stripe');  
                    //log_message('DEBUG', print_r($gateway, TRUE));
                    if($gateway !== FALSE)
                    {
                        /// STRIPE initialization
                        $this->load->library('stripe_lib');
                        $this->stripe_lib->setSettings($gateway);
                        //get any kind of transaction, not just stripe´s, for the membership that may be in conflict with a new renovation intent
                        $status = array('pending','failed'); 
                        $pending_trans = $this->pay->getUserPayments($mu->user_id, 'all', $status, $this->box->box_id);

                        $blocked = false;
                        if(!empty($pending_trans))
                        {
                            foreach ($pending_trans as $key => $trans) 
                            {
                                if($trans->mu_id == $id)
                                    $blocked = true;

                            }
                        }
                        if($blocked === false)
                        {
                            $data = $this->prepare_data_to_payment_form($id, 'manual');
                            $data['public_key'] = $gateway['public_key'];
                            $data['currency'] = $gateway['currency'];

                            /// Renovation Details
                            $result = $this->box->genericGet('first_name, last_name, email, phone', array('id' => $this->session->userdata('user_id')), 'auth_users', null, null, false);
                            $data['client_name'] = $result[0]->first_name." ".$result[0]->last_name;
                            $data['client_email'] = $result[0]->email;
                            $data['client_phone'] = $result[0]->phone;
                            
                            if($this->input->post('ajax') OR $this->input->is_ajax_request())
                            {
                                $this->load->view('backend/athlete/membership_pay_form', $data);
                            }
                            else
                            {
                                $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                                $this->load->view('backend/athlete/partials/blank', $data2);
                                $this->load->view('backend/athlete/membership_pay_form', $data);
                                $this->load->view('backend/partials/footer');
                            }
                        }
                        else
                        {
                            $this->session->set_flashdata('error', 'Este plan no se puede renovar porque aún tiene transacciones de renovación en proceso.');
                            $this->profile();
                        }
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Pasarela de pago "Stripe" no activa. Por favor, consulte con el administrador de su box.');
                        $this->profile();
                    }                       
                }
                else
                {
                    $this->session->set_flashdata('error', 'No hay pasarelas de pago activas para pago online con tarjeta. Por favor, consulte con el administrador de su box.');
                    $this->profile();
                }
            }
            else
            {
                $this->session->set_flashdata('error', 'Este plan no se puede renovar porque ya está de baja.');
                $this->profile();
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'URL incorrecta');
            $this->profile();
        }
    }

    private function process_membership_subscribe($id, $gateway = null)
    {
        $mu = $this->ath->get_memberships($this->box->box_id)[$id];
        
        // check if membership exists AND if user is the owner of that membership
        if($mu !== FALSE AND $mu->user_id == $this->session->userdata('user_id'))
        {
            // check if that membership can be renewed, that is if hasn´t been ended, banned or canceled
            //if is not 'expired', 'banned' or cancelled
            if($mu->status != 'e' AND $mu->status != 'b' AND $mu->status != 'c')
            {
                $params = array('box_id' => $this->box->box_id,
                                'is_recurring' => 1,
                                'active' => 1);
                
                if(!is_null($gateway))
                {
                    // use the selected gateway
                    $params['id'] = $gateway;
                    $gateway = $this->pay->getGateway($params);

                    //if gateway exists and is active
                    if($gateway !== false)
                    {
                        if($gateway->type == 'offline')
                        {
                            //if selected gateway is offline will require a IBAN. Lets check if exists
                            $iban = $this->box->getUserIBAN($this->session->userdata('user_id'));
                            if($iban !== false)
                            {
                                //pendiente
                                // crear subscripcion por remesas
                            }
                            else
                            {
                                //pendiente
                                //enviar a formulario para añadir un iban
                            }
                        }
                        else
                        {
                            $gtw_name = $gateway->name;
                            
                            $gateway_settings = $this->pay->getGatewaySettings($gtw_name); 

                            $this->load->library($gtw_name);

                            if($this->$gtw_name->set_up($this->box->box_id))
                            {
                                //get relevant info for subscription
                                $result = $this->box->genericGet('first_name, last_name, email, phone', 
                                                            array('id' => $this->session->userdata('user_id')), 
                                                            'auth_users', null, null, false);
                                
                                $customer_data = array("given_name" => $result[0]->first_name,
                                                        "family_name" => $result[0]->last_name,
                                                        "email" => $result[0]->email,
                                                        "country_code" => "ES"
                                                    );

                                $box_name = strtoupper($this->box->getBox($this->box->box_id)->name);
                                
                                $membership_name = $this->box->getMembership($mu->membership_id)->title;
                                $product_name = $box_name.' - '.$membership_name;

                                //create subscription
                                if($this->gocardless->gc_create_subscription($mu, $customer_data, $product_name))
                                {
                                    $this->session->set_flashdata('success', 'Subscripción de '.$membership_name.' creada con éxito. A partir de ahora, durante los primerios días de mes cobraremos la cuota periódica a través de tu cuenta bancaria.');
                                    $this->profile();
                                }
                                else
                                {
                                    if($this->gocardless->api_error == null)
                                        $this->session->set_flashdata('error', 'No se ha creado al subscripción solicitada.');
                                    else
                                        $this->session->set_flashdata('error', 'Error de GoCardless: '.$this->gocardless->api_error);

                                    $this->profile();
                                }
                            }
                            else
                            {
                                $this->session->set_flashdata('error', 'No se ha podido inicializar la pasarela de pago "'.strtoupper($gtw_name).'". Por favor, consulte con el administrador de su box.');
                                $this->profile();
                            }
                        }
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Pasarela de pago no activa. Por favor, consulte con el administrador de su box.');
                        $this->profile();
                    }
                }
                else
                {
                    // show subscribe options in order to select one
                    $data['gateways'] = $this->pay->getGateways($params); 
                    $this->show_view($data, 'athlete', 'subscribe_options'); //pendiente
                }
            }
            else
            {
                $this->session->set_flashdata('error', 'Este plan no se puede renovar porque ya está de baja.');
                $this->profile();
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'URL incorrecta');
            $this->profile();
        }
    }
   
    private function process_membership_flow_completed()
    {
        $this->load->library('gocardless');
        $this->gocardless->set_up($this->box->box_id);

        $redirect_flow_id = $this->input->get('redirect_flow_id');
        $redirectFlow = $this->gocardless->completeFlow($redirect_flow_id, session_id());
        if($redirectFlow !== FALSE)
        {
            /// Register events
            $result = $this->pay->updateGatewayEvent($redirect_flow_id, array('status' => 'completed'));

            $gateway = $this->gocardless->get_gateway();
            $dataDB = array(
                    'gateway' => $gateway['pp'],
                    'box_id' => $this->box->box_id, 
                    'user_id' => $this->session->userdata('user_id'),
                    'demo' => $gateway['demo'],
                    'txn_id' => $redirectFlow->links->customer,
                    'status' => 'created');
            $result = $this->pay->addGatewayTransaction('customers', $dataDB);

            $dataDB['txn_id'] = $redirectFlow->links->mandate;
            $dataDB['customer_id'] = $redirectFlow->links->customer;

            $result = $this->gocardless->updateCustomer($dataDB['customer_id'], array('metadata' => array('fitbox_user_id' => $dataDB['user_id'])));

            $result = $this->pay->addGatewayTransaction('mandates', $dataDB);

            $event = $this->pay->getGatewayEvent(array('txn_id' => $redirect_flow_id));

            $result = $this->gc_createSubscription($redirectFlow->links->mandate, $event->mu_id, $gateway);
            if($result !== FALSE)
            {
                $dataDB['mandate_id'] = $redirectFlow->links->mandate;
                $dataDB['mu_id'] = $event->mu_id;
                $dataDB['txn_id'] = $result->api_response->body->subscriptions->id;
                unset($dataDB['customer_id']);
                $result = $this->pay->addGatewayTransaction('subscriptions', $dataDB);

                $this->session->set_flashdata('success', 'Se ha creado la orden de domiciliación periódica.');
                
            }
            else
            {
                $this->session->set_flashdata('info', 'Se ha creado la orden de domiciliación periódica.');
            }                  
        } 
        else
        {
            $this->session->set_flashdata('error', 'GOCARDLESS ERROR: '.$this->gocardless->api_error);                   
        }   

        $this->profile();
    }

    private function process_membership_cancel_subscription($id)
    {
        $this->load->library('gocardless');
        $this->gocardless->set_up($this->box->box_id);

        $params = array('user_id' => $this->session->userdata('user_id'),
                        'box_id' => $this->box->box_id,
                        'id' => $id,
                        'status' => array('created', 'active') ); 
        $subscription = $this->pay->getGatewayTransactions('subscriptions', $params, true);

        // check if membership exists AND if user is the owner of that membership
        if($subscription !== FALSE)
        {
            $result = $this->gocardless->cancelSubscription($subscription->txn_id);
            if($result !== FALSE)
            {
                $this->gocardless->gc_cancel_subscription(array('txn_id' => $subscription->txn_id));

                $this->session->set_flashdata('success', 'Domiciliación bancaria periódica cancelada.');
            }
            else
            {
                $this->session->set_flashdata('error', 'Error de GOCARDLESS: '.$this->gocardless->api_error);
            }
        } 
        else
        {
            $this->session->set_flashdata('error', 'Url incorrecta.');
           
        }  
        $this->profile(); 
    }

    private function process_membership_payment($id)
    {
        if ($this->form_validation->run() == true)
        {
            $mu = $this->box->getUserMembership($id);
            // check if membership exists AND if user is the owner of that membership
            if($mu !== FALSE AND $mu->user_id == $this->session->userdata('user_id'))
            {
                // check if that membership can be renewed, that is if hasn´t been ended, banned or canceled
                if($mu->status != 'e' AND $mu->status != 'b' AND $mu->status != 'c')
                {
                    //check if CARD gateways are active
                    $params = array('box_id' => $this->box->box_id,
                                        'type' => 'online',
                                        'methods' => 'card',
                                        'active' => 1);

                    $gateways = $this->pay->getGateways($params); 
                    
                    if(!empty($gateways))
                    {
                        // for the moment only STRIPE has been implemented
                        $this->load->library('stripe_lib');
                        $gateway = $this->pay->getGatewaySettings('stripe');  

                        if($gateway !== FALSE)
                        {
                            /// STRIPE initialization
                            $this->stripe_lib->setSettings($gateway);

                            //get any kind of transaction, not just stripe´s, for the membership that may be in conflict with a new renovation intent
                            $status = array('pending','failed'); 
                            $pending_trans = $this->pay->getUserPayments($mu->user_id, 'all', $status, $this->box->box_id);

                            $blocked = false;
                            if(!empty($pending_trans))
                            {
                                foreach ($pending_trans as $key => $trans) 
                                {
                                    if($trans->mu_id == $id)
                                        $blocked = true;

                                }
                            }

                            if($blocked === false)
                            {
                                $times = $this->input->post('times');

                                $mem = $this->box->getMembership($mu->membership_id);
                                if($mu->status == 'y' || $mu->status == 'g') 
                                {
                                    $from = $this->booking_lib->calculateFrom($mu->mem_expire, 1);
                                    $to = $this->booking_lib->calculateExpiration($times, $mem->period, $from);
                                }
                                else if($mu->status == 'p' || $mu->status == 'n')
                                {
                                    $from = $this->booking_lib->calculateFrom();
                                    $to = $this->booking_lib->calculateExpiration($times, $mem->period);
                                }

                                $price = $this->booking_lib->calculateAmount($mem, $from, $to);
                                $description = "Renovación de ".$mem->title." desde ".$from." hasta ".$to;
                                $capture = 'manual'; // manual -> will only capture the amount, so another step is needed; automatic -> will complete the payment in only this (1) step

                                /// Renovation Details
                                $result = $this->box->genericGet('first_name, last_name, email, phone', array('id' => $this->session->userdata('user_id')), 'auth_users', null, null, false);
                                $data['client_name'] = $result[0]->first_name." ".$result[0]->last_name;
                                $data['client_email'] = $result[0]->email;
                                $data['client_phone'] = $result[0]->phone;

                                $intent = $this->stripe_lib->createIntent($price, $description, $capture);
                                if($intent !== FALSE)
                                {

                                    $data['client_secret'] = $intent['client_secret'];
                                    /// Register intent in Database
                                    $now = new DateTime('now');
                                    $payment_data = array(
                                            'gateway' => $gateway['pp'],
                                            'box_id' => $this->box->box_id, 
                                            'user_id' => $this->session->userdata('user_id'),
                                            'mu_id' => $id,
                                            'demo' => $gateway['demo'],
                                            'txn_id' => $intent['id'],
                                            'amount' => $price*100,
                                            'charge_date' => $now->format("Y-m-d"),
                                            'refounded' => 0,
                                            'retry' => 0,
                                            'status' => 'pending'                               
                                        );

                                    $transaction_data = array(
                                                        'type' => 'renew',
                                                        'from_membership_id' => $mu->membership_id, 
                                                        'to_membership_id' => $mu->membership_id,
                                                        'from' => $from,
                                                        'to' => $to,
                                                        'notes' => 'Manual client Stripe payment');

                                    $result = $this->pay->registerPayment($payment_data, $transaction_data);

                                    if($result !== FALSE)
                                    {
                                        $this->session->set_flashdata('success', 'El Pago ha sido recibido y registrado correctamente, en breves segundos, tu membresía será renovada.');
                                        $data = json_encode($data);
                                        echo $data;
                                    }                                 
                                    else
                                    {
                                        $this->session->set_flashdata('error', 'Pago recibido correctamente, pero no se ha podido registrar correctamente. Por favor, contacte con el administrador de su box. La referencia del pago es: '.$intent['id']);
                                        $this->profile();

                                    }
                                }
                                else
                                {
                                    $this->session->set_flashdata('error', 'Error de Stripe: '.$this->stripe_lib->api_error);
                                    $this->profile();
                                }
                            }
                            else
                            {
                                $this->session->set_flashdata('error', 'Este plan no se puede renovar porque aún tiene transacciones de renovación en proceso.');
                                $this->profile();
                            }
                        }
                        else
                        {
                            $this->session->set_flashdata('error', 'Pasarela de pago "Stripe" no activa. Por favor, consulte con el administrador de su box.');
                            $this->profile();
                        }                       
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'No hay pasarelas de pago activas para pago online con tarjeta. Por favor, consulte con el administrador de su box.');
                        $this->profile();
                    }
                }
                else
                {
                    $this->session->set_flashdata('error', 'Este plan no se puede renovar porque ya está de baja.');
                    $this->profile();
                }
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'URL incorrecta');
            $this->profile();
        }
    }
           

    function gc_createSubscription($mandate_id, $mu_id, $gateway)
    {
        $mem = $this->box->getUserMemberships($this->session->userdata('user_id'), array('id' => $mu_id))[0];

        $params = array(
                'name' => $mem['title'],
                'amount' => (int)$mem['price']*100,
                'currency' => $gateway['currency'], 
                'interval' => $mem['days'],
                'retry_if_possible' => TRUE,
                'links' => array('mandate' => $mandate_id),
                'metadata' => array('fitbox_plan_id' => $mu_id));

        switch ($mem['period']) {
            case 'W':
                $params['interval_unit'] = 'weekly';
                break;

            case 'M':
                $params['interval_unit'] = 'monthly';
                $params['day_of_month'] = 3;
                break;
            
            case 'Y':
                $params['interval_unit'] = 'yearly';
                $params['day_of_month'] = 3;
                break;
        }
        
        return $this->gocardless->gc_create_transaction('subscription', $params);
    }

    function membershipPaymentRecalc($id)
    {
        $mem = $this->box->getMembership($id);
        $from = $this->input->post('from');
        $times = $this->input->post('times');
        $coupon_id = $this->input->post('coupon');

        $result = $this->booking_lib->membershipPaymentRecalc($mem, $from, $times, $coupon_id);

        echo json_encode($result);
    }

    function prepare_data_to_payment_form($id, $type = null)
    {
        $data['page_title'] = "Registrar pago manual";
                $payment = (object) [
                    'box_id' => $this->box->box_id, 
                    'mu_id' => $id,
                    'from_membership_id' => '', 
                    'to_membership_id' => '', 
                    'user_id' => $this->session->userdata('user_id'),
                    'rate_amount' => '',
                    'coupon_id' => 0,
                    'discount' => '',
                    'tax' => '',
                    'total' => '',
                    'date' => '',
                    'pp' => '',
                    'status' => '',
                    'staff_id' => '',
                    'notes' => '',
                    'times' => '',
                    'type' => '',
                    'to' => '',
                    'from' => ''
                ];
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

                $data['membership'] = $this->box->getUserMembership($id);
                $result = $this->box->genericGet('first_name, last_name', array('id' => $data['membership']->user_id), 'auth_users', null, null, false);
                $data['user'] = $result[0]->first_name." ".$result[0]->last_name;
                $mem = $this->box->getMembership($data['membership']->membership_id);
                $data['mem'] = $mem;

                if($data['membership']->status == 'y' || $data['membership']->status == 'g') 
                    $payment->from = $this->booking_lib->calculateFrom($data['membership']->mem_expire, 1);
                else 
                    $payment->from = $this->booking_lib->calculateFrom();
               
                if (!empty(form_error('from'))) $class = "error"; else $class = "valid";
                $data['from'] = array(
                    'name'  => 'from',
                    'id'    => 'from',
                    'class' => 'form-control '.$class,
                    'type'  => 'date',
                    'data-date-format' => 'yyyy-mm-dd',
                    'required' => 'required',
                    'value' => $payment->from,
                );

                if($data['membership']->status == 'y' || $data['membership']->status == 'g') 
                    $payment->to = $this->booking_lib->calculateExpiration($mem->days, $mem->period, $payment->from);
                else 
                    $payment->to = $this->booking_lib->calculateExpiration($mem->days, $mem->period);

                $payment->rate_amount = $this->booking_lib->calculateAmount($mem, $payment->from, $payment->to);
                $data['price'] = $payment->rate_amount;
                
                $data['times_list'] = array(1 => 1, 2 => 2, 3 => 3, 4 =>4, 5 => 5, 6 => 6);
                $data['times_status'] = $payment->times;
                if (!empty(form_error('times'))) $class = "error"; else $class = "valid";
                $data['times'] = array(
                    'name'  => 'times',
                    'id'    => 'times',
                    'class' => 'form-control input '.$class,
                    'required' => 'required',
                    'value' => '',
                );

                // $coupons = $this->booking->getAvailableCoupons($id);
                // //print("<pre>".print_r($coupons,true)."</pre>");
                // $data['coupons_list'] = array('0' =>'-- Seleccione --');
                // foreach ($coupons as $cup) 
                // {
                //     $type = ($cup->type == 'abs')? "€" : "%"; 
                //     $data['coupons_list'][$cup->id] = $cup->title." (".$cup->value." ".$type.")";
                // }
                // if (!empty(form_error('coupon'))) $class = "error"; else $class = "valid";
                // $data['coupon_status'] = $payment->coupon_id;
                // $data['coupon'] = array(
                //     'name'  => 'coupon',
                //     'id'    => 'coupon',
                //     'class' => 'form-control '.$class,
                //     'required' => 'required',
                //     'value' => $payment->coupon_id,
                // );

                
                $pay_methods = $this->pay->getPaymentMethods();
                if(!$data['user']->IBAN OR $type = 'manual')
                // si usuario no tiene iban registrado y existe metodo de pabo por domiciliación, eliminar esta opcion
                {
                    foreach ($pay_methods as $key => $value) 
                    {
                        if($value['name'] == 'IBAN' AND $value['default'] == 1) unset($pay_methods[$key]);
                    }
                    
                }
                $data['pp_list'] = array(''=> '-- Seleccione --');
                foreach ($pay_methods as $key => $value) 
                {
                    if($value['default'] == 1)
                        $data['pp_list'][$key] = $this->lang->line($value['name'].'_name');
                    else
                        $data['pp_list'][$key] = $value['name'];
                }

                if (!empty(form_error('pp'))) $class = "error"; else $class = "valid";

                $data['pp_status'] = $payment->pp;
                $data['pp'] = array(
                    'name'  => 'pp',
                    'id'    => 'pp',
                    'class' => 'form-control '.$class,
                    'required' => 'required',
                    'value' => $payment->pp,
                );

                if (!empty(form_error('notes'))) $class = "error"; else $class = "valid";
                $data['notes'] = array(
                    'name'  => 'notes',
                    'id'    => 'notes',
                    'class' => 'form-control '.$class,
                    'value' => '',
                );

                if (!empty(form_error('to'))) $class = "error"; else $class = "valid";
                $data['to'] = array(
                    'name'  => 'to',
                    'id'    => 'to',
                    'class' => 'form-control '.$class,
                    'type'  => 'date',
                    'data-date-format' => 'yyyy-mm-dd',
                    'required' => '',
                    'value' => $payment->to,
                );

                if (!empty(form_error('rate_amount'))) $class = "error"; else $class = "valid";
                $data['rate_amount'] = array(
                    'name'  => 'rate_amount',
                    'id'    => 'rate_amount',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $payment->rate_amount,
                );

        return $data;

    }
    

/////////////////////////////////////////////////////////////////////////
//  SECTION: NUTRITION
/////////////////////////////////////////////////////////////////////////

    function nutrition($action = null, $params = null)
    {
        $allowed_groups = array('athlete');
        $date = null;
        $ajax = null;

        if ($params != null) 
        {
            if (is_array($params)) extract($params); 
            else log_message('debug', print_r("€RROR @nutrition #1: ".$params,TRUE));
        }

        if ($this->ion_auth->check_login($allowed_groups) )
        {        
            if($action == 'log' OR $action == null)
            // default, show main view
            {   

                if($params == null) 
                    $date = ($this->input->post('date')) ? $this->input->post('date') : date('Y-m-d'); 
                else  
                    $date = $params;

                $data['date'] = $date;

                //foods registry
                $data['foods'] = $this->food->getLog($date, 1); //1 day history

                //STATS
                $data['nutrient_stats'] = $this->food->getNutrientStats($date, 'day');
                $data['nutrient_req'] = $this->food->getNutrientReq();
                $data['DV'] = $this->food->calcDV($data['nutrient_req'], $data['nutrient_stats']);

                //PLOT
                $data['energy'] = $this->food->convert_to_chart($this->food->getTimeSeriesMacroStats($date, 'month'), 'energy');
                $data['carbs'] = $this->food->convert_to_chart($this->food->getTimeSeriesMacroStats($date, 'month'), 'carbs');
                $data['proteins'] = $this->food->convert_to_chart($this->food->getTimeSeriesMacroStats($date, 'month'), 'proteins');
                $data['fats'] = $this->food->convert_to_chart($this->food->getTimeSeriesMacroStats($date, 'month'), 'fats');

                if ($this->input->post('ajax') OR $ajax == true OR $this->input->is_ajax_request()) 
                {  
                    $this->load->view('backend/athlete/nutrition', $data);
                }else{
                     $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                    $this->load->view('backend/athlete/partials/blank', $data2);
                    $this->load->view('backend/athlete/nutrition', $data);
                    $this->load->view('backend/partials/footer');
                }
            }
            else if ($action == 'food')
            // show register form view
            { 
                $this->foods('add', $date);
            }
            else if ($action == 'meal')
            // whow register form view
            {
                //$this->meals('add');
            }
            else if($action == 'search')
            // show nutrients search view
            {
                $this->nutrients('search');
            }
            else if($action == 'view_food')
            // show nutrients search view
            {
                $this->foods('view');
            }
            else if($action == 'config')
            // swhow config view
            {
                if ($this->input->post('ajax')) 
                {  
                    $this->load->view('backend/athlete/nutrition_config', $data);
                }else{
                     $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                    $this->load->view('backend/athlete/partials/blank', $data2);
                    $this->load->view('backend/athlete/nutrition_config', $data);
                    $this->load->view('backend/partials/footer');
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function nutrients($action, $params = null)
    {
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            
            $food = (object) [
                'food_id'    => ''
            ];

            $nutrient = (object) [
                'nutrient_id'    => '',
                'category_id_id'    => ''
            ];
            
            if($action == 'food')
            //show food nutrients
            // params == food_id
            {
                $food_id = $this->input->post('food_id')? $this->input->post('food_id') : $params;
                $data['food'] = $this->food->getFood($food_id);
                $data['nutrient_stats'] = $this->food->getFoodNutrientStats($data['food']);
                $data['nutrient_req'] = $this->food->getNutrientReq();
                $data['DV'] = $this->food->calcDV($data['nutrient_req'], $data['nutrient_stats']);

                list($data['carbs'], $data['proteins'], $data['fats']) = $this->food->calcMacrosKcal($data['food']->Carbohydrt_g, $data['food']->Protein_g, $data['food']->Lipid_Tot_g);
                $this->load->view('backend/athlete/nutrition_food_nutrients', $data);
            }
            else if($action == 'nutrient')
            //search foods high in nutrient
            {
                $data['nutrient'] = $this->input->post('nutrient');
                $unit = explode("_", $data['nutrient']);
                $data['unit'] = array_pop($unit);
                $data['name'] = implode(" ",$unit);
                $data['foods'] = $this->food->getFoodsByNutrient($data['nutrient'], $this->input->post('group_id'));
                $this->load->view('backend/athlete/nutrition_foods_by_nutrient', $data);
            }
            else
            {

                //food form
                $data['food_list'] = $this->food->getFoods();
                $data['food_id'] = array(
                    'name'  => 'food_id',
                    'id'    => 'food_id',
                    'class' => 'form-control',
                    'type'  => 'text',
                    'required' => 'required',
                    'value' => '',
                );

                //nutrient form
                $data['vitamins_list'] = $this->food->getNutrientsList('vitamins');
                $data['minerals_list'] = $this->food->getNutrientsList('minerals');
                //$data['other_list'] = $this->food->getNutrientsList('other');
                $data['group_list'] = $this->food->getCategoryList();

                // show main view
                if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
                {  
                    $this->load->view('backend/athlete/nutrition_search', $data);
                }else{
                     $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                    $this->load->view('backend/athlete/partials/blank', $data2);
                    $this->load->view('backend/athlete/nutrition_search', $data);
                    $this->load->view('backend/partials/footer');
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function foods($action, $param = null)
    //si EDIT param1 == $id
    //si ADD param1 == $date
    {
        
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups))
        {
            $data['action'] = $action;
            if($action == 'add')
            {
                $data['page_title'] = "Register food log";
                if($param == null) $date = ($this->input->post('date')) ? $this->input->post('date') : date('Y-m-d');
                else $date = $param;

                $food = (object) [
                    'user_id'    => '',
                    'food_id'    => '',
                    'date'    => '',
                    'meal'    => '',
                    'qtty'    => '',
                    // 'serving'    => ''
                ];
            }
            elseif($action == 'edit')
            {
                $data['id'] = $param;
                $data['page_title'] = "Edit food log";
                $food = $this->food->getLogRegistry($param);
            }

            // validate form input

            $this->form_validation->set_rules('date[]', 'date', 'required');
            $this->form_validation->set_rules('qtty[]', 'qtty', 'required|integer');
            // $this->form_validation->set_rules('serving[]', 'serving', 'required');
            $this->form_validation->set_rules('food_id[]', 'food_id', 'required|integer');
            $this->form_validation->set_rules('meal[]', 'meal', 'required');

            if ($this->form_validation->run() == true)
            {
                $additional_data = array(
                    'user_id'    => $this->ath->user_id,
                    'date'      => $this->input->post('date'),
                    'meal'      => $this->input->post('meal'),
                    'qtty'      => $this->input->post('qtty'),
                    // 'serving'      => $this->input->post('serving'),
                    'food_id'      => $this->input->post('food_id')
                );
  
                if($action == 'add')
                {
                    $this->food->addLog($additional_data);
                }
                elseif($action == 'edit')
                {
                    $this->food->updateLog($param, $additional_data);
                }
   
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $params = array(
                    'date' => $additional_data['date'], 
                    'ajax' => true
                );
                $this->nutrition('log', $params);
            }
            else if($action == 'view')
            {                

                if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
                {
                    $this->nutrients('food', $param);
                }
                else
                {
                     $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                    $this->load->view('backend/athlete/partials/blank', $data2);
                    $this->nutrients('food', $param);
                    $this->load->view('backend/partials/footer');
                }
            }
            else
            // display the form
            {
                if (!$this->input->post('ajax')) 
                //since goTo uses POST, by default there are validation errors, even though the form hasn´t been submited yet.
                    $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                    // set the flash data error message if there is one
                else
                    $data['message'] = "";

                //$food_id = ($food_id == null) ? $this->food->getFood($this->input->post('food_id')) : $this->food->getFood($food->food_id); 

                $data['meal_list'] = $this->food->getMealList();
                $data['food_list'] = $this->food->getFoods();

                $data['food_status'] = $food->food_id;
                $data['meal_status'] = $food->meal;

                $data['food_id'] = array(
                    'name'  => 'food_id[]',
                    'id'    => 'food_id[]',
                    'class' => 'form-control',
                    'type'  => 'text',
                    'required' => 'required',
                    'value' => $food->food_id,
                );

                if (!empty(form_error('qtty'))) $class = "error"; else $class = "valid";
                $data['qtty'] = array(
                    'name'  => 'qtty[]',
                    'id'    => 'qtty[]',
                    'class' => 'form-control '.$class,
                    'required' => 'required',
                    'value' => $food->qtty,
                );

                // $data['serving'] = array(
                //     'name'  => 'servings[]',
                //     'id'    => 'servings[]',
                //     'class' => 'form-control '.$class,
                //     'required' => 'required',
                //     'value' => $food->serving,
                // );

                if (!empty(form_error('date'))) $class = "error"; else $class = "valid";
                $data['date'] = array(
                    'name'  => 'date',
                    'id'    => 'date',
                    'class' => 'form-control '.$class,
                    'type'  => 'date',
                    'data-date-format' => 'yyyy-mm-dd',
                    'required' => 'required',
                    'value' => ($food->date) ? $food->date : $date,
                );

                $data['meal'] = array(
                    'name'  => 'meal[]',
                    'id'    => 'meal[]',
                    'class' => 'form-control ',
                    'type'  => 'text',
                    'required' => 'required',
                    'value' => $food->meal,
                );

                if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
                {
                    $this->load->view('backend/athlete/nutrition_log_form', $data);
                }
                else
                {
                     $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                    $this->load->view('backend/athlete/partials/blank', $data2);
                    $this->load->view('backend/athlete/nutrition_log_form', $data);
                    $this->load->view('backend/partials/footer');
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function deleteFoodLog($id) 
    {
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups))
        {
            if($this->food->checkLogUser($id))
                $this->food->deleteFoodLog($id); 

            $params = array('ajax' => true);
            $this->nutrition('log', $params);         
        }
        else
        {
            $this->load->view('backend/no_session');
        }
                
    }

    function getNutritionPartialform()
    {   
        $data['count'] = $this->input->post('count');
        // $food = (object) [
        //             'food_id'.$data['count']    => '',
        //             'date'.$data['count']    => ''
        // ];

        $data['food_list'] = $this->food->getFoods();
        $data['food_id[]'] = array(
            'name'  => 'food_id[]',
            'id'    => 'food_id[]',
            'class' => 'form-control dropdown',
            'type'  => 'text',
            'required' => 'required',
            'value' => '',
        );

        $data['qtty[]'] = array(
            'name'  => 'qtty[]',
            'id'    => 'qtty[]',
            'class' => 'form-control input',
            'required' => 'required',
            'value' => '',
        );

        // $data['serving[]'] = array(
        //     'name'  => 'serving[]',
        //     'id'    => 'serving[]',
        //     'class' => 'form-control input',
        //     'required' => 'required',
        //     'value' => '',
        // );

        $this->load->view('backend/athlete/nutrition_log_form_aditional', $data);
    }

    function getServings($food_id)
    {
        return $this->food->getServings($food_id);
    }

//////////////////////////////////////////////////////////////////////////
//  SECCION: BIOMETRICS
/////////////////////////////////////////////////////////////////////////

    function biometrics($ajax = false)
    {
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups) )
        {        
            $data['weights'] = $this->bio->getWeight();
            $data['heights'] = $this->bio->getHeight();
            $data['BPs'] = $this->bio->getBP();
            $data['weight_history'] = $this->bio->getWeightHistory();
            $data['bp_history'] = $this->bio->getBPHistory();

            if ($this->input->post('ajax') OR $this->input->is_ajax_request() OR $ajax == TRUE) 
            {  
                $this->load->view('backend/athlete/biometrics', $data);
            }else{
                 $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                $this->load->view('backend/athlete/partials/blank', $data2);
                $this->load->view('backend/athlete/biometrics', $data);
                $this->load->view('backend/partials/footer');
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function deleteWeight($id) 
    {
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups))
        {
           if($this->bio->getWeightById($id))
             $this->bio->deleteWeight($id);  

            $this->biometrics();         
        }
        else
        {
            $this->load->view('backend/no_session');
        }
               
    }

    function deleteHeight($id) 
    {
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups))
        {
           if($this->bio->getHeightById($id))
            $this->bio->deleteHeight($id);      

            $this->biometrics();    
        }
        else
        {
            $this->load->view('backend/no_session');
        }       
    }

    function deleteBP($id) 
    {
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups))
        {
           if($this->bio->getBPById($id))
            $this->bio->deleteBP($id); 

           $this->biometrics();            
        }
        else
        {
            $this->load->view('backend/no_session');
        }
             
    }

    function deletePR($id) 
    {
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups))
        {
           if($this->wod->getPR($id))
           $this->wod->deletePR($id);  

           $this->PRs();        
        }
        else
        {
            $this->load->view('backend/no_session');
        }
                
    }

    function weight($action, $id = null)
    {
        
        $allowed_groups = array('athlete');
        $today = date('Y-m-d');

        if ($this->ion_auth->check_login($allowed_groups))
        {

            $data['action'] = $action;
            
            if($action == 'add')
            {
                $data['page_title'] = "Register Weight";
                $last_weight = $this->bio->getLastWeight();
                $last_fat = '';

                $weight = (object) [
                    'user_id'    => '',
                    'date'    => $today,
                    'weight'      => $last_weight->weight,
                    'fat'      => $last_weight->fat
                ];
            }
            elseif($action == 'edit')
            {
                $data['page_title'] = "Edit Weight";
                $weight = $this->bio->getWeightById($id);
            }
            
            //Security check, to avoid users edit other users info
            if($action == 'edit' AND $weight == null)
            {
                $this->biometrics();
            }
            else
            {
                // validate form input
                $this->form_validation->set_rules('date', 'date', 'required');
                $this->form_validation->set_rules('weight', 'weight', 'required');
                $this->form_validation->set_rules('fat', 'fat', 'required');
                

                if ($this->form_validation->run() == true)
                {
                    $additional_data = array(
                        'user_id'    => $this->ath->user_id,
                        'date'      => $this->input->post('date'),
                        'weight'      => $this->input->post('weight'),
                        'fat'      => $this->input->post('fat')
                    );
                }
                if ($this->form_validation->run() == true)
                {
                    
                    if($action == 'add')
                    {
                        $this->bio->registerWeight($additional_data);
                    }
                    elseif($action == 'edit')
                    {
                        $this->bio->updateWeight($id, $additional_data);
                    }

                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    $this->biometrics();
                }
                else
                {
                    // display the create user form
                    // set the flash data error message if there is one
                    $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                    $data['id'] = $id;

                    if (!empty(form_error('weight'))) $class = "error"; else $class = "valid";
                    $data['weight'] = array(
                        'name'  => 'weight',
                        'id'    => 'weight',
                        'class' => 'form-control '.$class,
                        'type'  => 'decimal',
                        'required' => '',
                        'value' => $weight->weight,
                    );

                    if (!empty(form_error('fat'))) $class = "error"; else $class = "valid";
                    $data['fat'] = array(
                        'name'  => 'fat',
                        'id'    => 'fat',
                        'class' => 'form-control '.$class,
                        'type'  => 'decimal',
                        'required' => '',
                        'value' => $weight->fat,
                    );

                    if (!empty(form_error('date'))) $class = "error"; else $class = "valid";
                    $data['date'] = array(
                        'name'  => 'date',
                        'id'    => 'date',
                        'class' => 'form-control '.$class,
                        'type'  => 'date',
                        'data-date-format' => 'yyyy-mm-dd',
                        'required' => '',
                        'value' => $weight->date,
                    );

                    if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
                    {
                        $this->load->view('backend/athlete/biometric_weight_form', $data);
                    }
                    else
                    {
                         $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                    $this->load->view('backend/athlete/partials/blank', $data2);
                        $this->load->view('backend/athlete/biometric_weight_form', $data);
                        $this->load->view('backend/partials/footer');
                    }
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function height($action, $id = null)
    {
        
        $allowed_groups = array('athlete');
        $today = date('Y-m-d');

        if ($this->ion_auth->check_login($allowed_groups))
        {

            $data['action'] = $action;
            
            if($action == 'add')
            {
                $data['page_title'] = "Registrar altura";
                $last_height = $this->bio->getLastHeight();

                $height = (object) [
                    'user_id'    => '',
                    'date'    => date('Y-m-d'),
                    'height'      => $last_height->height
                ];
            }
            elseif($action == 'edit')
            {
                $data['page_title'] = "Editar altura";
                $height = $this->bio->getHeightById($id);
            }
            
            //Security check, to avoid users edit other users info
            if($action == 'edit' AND $height == null)
            {
                $this->biometrics();
            }
            else
            {
                // validate form input
                $this->form_validation->set_rules('date', 'date', 'required');
                $this->form_validation->set_rules('height', 'height', 'required');            

                if ($this->form_validation->run() == true)
                {
                    $additional_data = array(
                        'user_id'    => $this->ath->user_id,
                        'date'      => $this->input->post('date'),
                        'height'      => $this->input->post('height')
                    );
                    
                    if($action == 'add')
                    {
                        if($this->bio->registerHeight($additional_data))
                            $this->session->set_flashdata('success', 'Altura actualizada');
                        else
                            $this->session->set_flashdata('error', 'No se pudo actualizar la altura.');
                    }
                    elseif($action == 'edit')
                    {
                        if($this->bio->updateHeight($id, $additional_data))
                            $this->session->set_flashdata('success', 'Altura actualizada');
                        else
                            $this->session->set_flashdata('error', 'No se pudo actualizar la altura.');
                        
                    }

                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    $this->biometrics();
                }
                else
                {
                    // display the create user form
                    // set the flash data error message if there is one
                    $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                    $data['id'] = $id;

                    if (!empty(form_error('height'))) $class = "error"; else $class = "valid";
                    $data['height'] = array(
                        'name'  => 'height',
                        'id'    => 'height',
                        'class' => 'form-control '.$class,
                        'type'  => 'decimal',
                        'required' => '',
                        'value' => $height->height,
                    );

                    if (!empty(form_error('date'))) $class = "error"; else $class = "valid";
                    $data['date'] = array(
                        'name'  => 'date',
                        'id'    => 'date',
                        'class' => 'form-control '.$class,
                        'type'  => 'date',
                        'data-date-format' => 'yyyy-mm-dd',
                        'required' => '',
                        'value' => $height->date,
                    );

                    if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
                    {
                        $this->load->view('backend/athlete/biometric_height_form', $data);
                    }
                    else
                    {
                         $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                    $this->load->view('backend/athlete/partials/blank', $data2);
                        $this->load->view('backend/athlete/biometric_height_form', $data);
                        $this->load->view('backend/partials/footer');
                    }
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function bp($action, $id = null)
    {
        
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups))
        {
            $data['action'] = $action;
            if($action == 'add')
            {
                $data['page_title'] = "Register blood pressure";
                $bp = (object) [
                    'user_id'    => '',
                    'date'    => date('Y-m-d'),
                    'hour'    => date("H:i:s"),
                    'timestamp'    => '',
                    'systolic'    => '120',
                    'diastolic'      => '80',
                    'pulse'      => '60'
                ];
            }
            elseif($action == 'edit')
            {
                $data['page_title'] = "Edit blood pressure";
                $bp = $this->bio->getBPById($id);
            }
            
            //Security check, to avoid users edit other users info
            if($action == 'edit' AND $bp == null)
            {
                $this->biometrics();
            }
            else
            {

                // validate form input
                $this->form_validation->set_rules('date', 'date', 'required');
                $this->form_validation->set_rules('hour', 'hour', 'required');
                $this->form_validation->set_rules('systolic', 'systolic', 'required|integer');
                $this->form_validation->set_rules('diastolic', 'diastolic', 'required|integer');
                $this->form_validation->set_rules('pulse', 'pulse', 'required|integer');
                

                if ($this->form_validation->run() == true)
                {
                    $additional_data = array(
                        'user_id'    => $this->ath->user_id,
                        'timestamp'      => $this->input->post('date').' '.$this->input->post('hour').':00',
                        'systolic'      => $this->input->post('systolic'),
                        'diastolic'      => $this->input->post('diastolic'),
                        'pulse'      => $this->input->post('pulse')
                    );
                }
                if ($this->form_validation->run() == true)
                {
                    
                    if($action == 'add')
                    {
                        $this->bio->registerBP($additional_data);
                    }
                    elseif($action == 'edit')
                    {
                        $this->bio->updateBP($id, $additional_data);
                    }

                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    $this->biometrics();
                }
                else
                {
                    // display the create user form
                    // set the flash data error message if there is one
                    $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                    $data['id'] = $id;

                    if (!empty(form_error('date'))) $class = "error"; else $class = "valid";
                    $data['date'] = array(
                        'name'  => 'date',
                        'id'    => 'date',
                        'class' => 'form-control '.$class,
                        'type'  => 'date',
                        'data-date-format' => 'yyyy-mm-dd',
                        'required' => '',
                        'value' => $bp->date,
                    );

                    if (!empty(form_error('hour'))) $class = "error"; else $class = "valid";
                    $data['hour'] = array(
                        'name'  => 'hour',
                        'id'    => 'hour',
                        'class' => 'form-control '.$class,
                        'type'  => 'text',
                        'required' => '',
                        'value' => $bp->hour,
                    );

                    if (!empty(form_error('systolic'))) $class = "error"; else $class = "valid";
                    $data['systolic'] = array(
                        'name'  => 'systolic',
                        'id'    => 'systolic',
                        'class' => 'form-control spin',
                        'type'  => 'decimal',
                        'required' => '',
                        'value' => $bp->systolic,
                    );

                    if (!empty(form_error('diastolic'))) $class = "error"; else $class = "valid";
                    $data['diastolic'] = array(
                        'name'  => 'diastolic',
                        'id'    => 'diastolic',
                        'class' => 'form-control spin',
                        'type'  => 'decimal',
                        'required' => '',
                        'value' => $bp->diastolic,
                    );

                    if (!empty(form_error('pulse'))) $class = "error"; else $class = "valid";
                    $data['pulse'] = array(
                        'name'  => 'pulse',
                        'id'    => 'pulse',
                        'class' => 'form-control '.$class,
                        'type'  => 'decimal',
                        'required' => '',
                        'value' => $bp->pulse,
                    );


                    if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
                    {
                        $this->load->view('backend/athlete/biometric_bp_form', $data);
                    }
                    else
                    {
                         $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                    $this->load->view('backend/athlete/partials/blank', $data2);
                        $this->load->view('backend/athlete/biometric_bp_form', $data);
                        $this->load->view('backend/partials/footer');
                    }
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

/////////////////////////////////////////////////////////
//  SECCION: PERSONAL RECORDS
/////////////////////////////////////////////////////////

    function PRs($ajax = false)
    {
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups) )
        {        
            $data['PR'] = $this->wod->getPR();
            $data['strength'] = $this->wod->getPRs(1);
            $data['flexibility'] = $this->wod->getPRs(3);
            $data['power'] = $this->wod->getPRs(4);
            $data['endurance'] = $this->wod->getPRs(5);
            $data['speed'] = $this->wod->getPRs(7);
            
            if ($this->input->post('ajax') OR $ajax = true) 
            {  
                $this->load->view('backend/athlete/PRs', $data);
            }else{
                 $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                $this->load->view('backend/athlete/partials/blank', $data2);
                $this->load->view('backend/athlete/PRs', $data);
                $this->load->view('backend/partials/footer');
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function setExcerciseForm($action = null, $pr_id = null)
    {
        if ($action == null)
        {
            $pr = (object) [
                'user_id'    => '',
                'excercise_id'    => '',
                'date'    => '',
                'hour'    => '',
                'min'    => '',
                'secs'    => '',
                'load'      => '',
                'distance'      => '',
                'distance_unit'      => '',
                'height'      => '',
                'reps'      => ''
            ];
        }
        else
        {
           $pr = ($pr_id == null) ? $this->wod->getPR($this->input->post('pr_id')) : $this->wod->getPR($pr_id); 
        }


        $excercise = ($pr_id == null) ? $this->wod->getExcercise($this->input->post('excercise_id')) : $this->wod->getExcercise($pr->excercise_id); 

        $data['excercise_status'] = $excercise->id;
        $data['excercise_id'] = array(
            'name'  => 'excercise_id',
            'id'    => 'excercise_id',
            'class' => 'form-control ',
            'type'  => 'text',
            'required' => '',
            'value' => $excercise->id,
        );

        if (!empty(form_error('date'))) $class = "error"; else $class = "valid";
        $data['date'] = array(
            'name'  => 'date',
            'id'    => 'date',
            'class' => 'form-control date'.$class,
            'type'  => 'date',
            'data-date-format' => 'yyyy-mm-dd',
            'required' => 'required',
            'value' => $pr->date,
        );

        if($excercise->time == 1 OR $excercise->time == 2)
        {
            if (!empty(form_error('hour'))) $class = "error"; else $class = "valid";
            $data['hour'] = array(
                'name'  => 'hour',
                'id'    => 'hour',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'required' => ($excercise->time == 1) ? "required" : "",
                'value' => $pr->hour,
            );

            if (!empty(form_error('min'))) $class = "error"; else $class = "valid";
            $data['min'] = array(
                'name'  => 'min',
                'id'    => 'min',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'required' => ($excercise->time == 1) ? "required" : "",
                'value' => $pr->min,
            );

            if (!empty(form_error('secs'))) $class = "error"; else $class = "valid";
            $data['secs'] = array(
                'name'  => 'secs',
                'id'    => 'secs',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'required' => ($excercise->time == 1) ? "required" : "",
                'value' => $pr->secs,
            );
        }
        if($excercise->load == '1' OR $excercise->load == '2')
        {
            if (!empty(form_error('load'))) $class = "error"; else $class = "valid";
            $data['load'] = array(
                'name'  => 'load',
                'id'    => 'load',
                'class' => 'form-control spin'.$class,
                'type'  => 'text',
                'required' => ($excercise->load == 1) ? "required" : "",
                'value' => $pr->load,
            );
        }
        if($excercise->distance == '1' OR $excercise->distance == '2')
        {
            if (!empty(form_error('distance'))) $class = "error"; else $class = "valid";
            $data['distance'] = array(
                'name'  => 'distance',
                'id'    => 'distance',
                'class' => 'form-control spin'.$class,
                'type'  => 'text',
                'required' => ($excercise->distance == 1) ? "required" : "",
                'value' => $pr->distance,
            );
            $data['distance_list'] = array('' =>'-- Seleccione --', 'm' =>'metros', 'k'=> 'kilometros');

            if (!empty(form_error('distance_unit'))) $class = "error"; else $class = "valid";
            $data['distance_unit'] = array(
                'name'  => 'distance_unit',
                'id'    => 'distance_unit',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'required' => '',
                'value' => $pr->distance_unit,
            );
        }

        if($excercise->height == '1' OR $excercise->height == '2')
        {
            if (!empty(form_error('height'))) $class = "error"; else $class = "valid";
            $data['height'] = array(
                'name'  => 'height',
                'id'    => 'height',
                'class' => 'form-control spin'.$class,
                'type'  => 'text',
                'required' => ($excercise->height == 1) ? "required" : "",
                'value' => $pr->height,
            );
        }

        if($excercise->reps == '1' OR $excercise->reps == '2')
        {
            if (!empty(form_error('reps'))) $class = "error"; else $class = "valid";
            $data['reps'] = array(
                'name'  => 'reps',
                'id'    => 'reps',
                'class' => 'form-control spin'.$class,
                'type'  => 'text',
                'required' => ($excercise->reps == 1) ? "required" : "",
                'value' => $pr->reps
            );                 
        }

        $data['excercise'] = $excercise;

        if($action == null OR $action == 'update') $this->load->view('backend/athlete/pr_excercise_form',$data);  else return $data;
    }

    function pr($action, $id = null)
    {
        
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups))
        {

            $data['action'] = $action;
            if($action == 'add')
            {
                
                $data['page_title'] = "Register personal records";
                $pr = (object) [
                    'user_id'    => '',
                    'excercise_id'    => '',
                    'date'    => '',
                    'hour'    => '',
                    'min'    => '',
                    'secs'    => '',
                    'load'      => '',
                    'distance'      => '',
                    'distance_unit'      => '',
                    'height'      => '',
                    'reps'      => ''
                ];
            }
            elseif($action == 'edit')
            {
                $data['page_title'] = "Edit personal record";
                $pr = $this->wod->getPR($id);
            }
            

            // validate form input
            $this->form_validation->set_rules('date', 'date', 'required');
            

            if ($this->form_validation->run() == true)
            {
                
                if ($this->input->post('distance_unit') == 'k') 
                {
                    $distance = $this->input->post('distance') * 1000;
                }
                else
                {
                    $distance = $this->input->post('distance');
                }
                $time = $this->input->post('secs') + $this->input->post('min')*60 + $this->input->post('hour')*60^2; 
                $RM = $this->wod->calcMaxRep($this->input->post('reps'), $this->input->post('load'));

                $additional_data = array(
                    'user_id'    => $this->ath->user_id,
                    'date'      => $this->input->post('date'),
                    'excercise_id'      => $this->input->post('excercise_id'),
                    'time'      => $time,
                    'RM'       => $RM[1],
                    'load'      => $this->input->post('load'),
                    'distance'      => $distance,
                    'height'      => $this->input->post('height'),
                    'reps'      => $this->input->post('reps'),
                    'tons'      => $this->input->post('reps')*$this->input->post('load')/1000,
                    'manual' => 1
                );
                
                if($action == 'add')
                {
                    $this->wod->registerPR($additional_data);
                }
                elseif($action == 'edit')
                {
                    $this->wod->updatePR($id, $additional_data);
                }

                
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->PRs(true);
            }
            else
            {
                // display the create user form
                // set the flash data error message if there is one
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $data['id'] = $id;

                $data['excercise_status'] = $pr->excercise_id;
                $data['excercise_id'] = array(
                    'name'  => 'excercise_id',
                    'id'    => 'excercise_id',
                    'class' => 'form-control ',
                    'type'  => 'text',
                    'required' => '',
                    'value' => $pr->excercise_id,
                );
                $data['excercise_list'] = $this->wod->getExcercises();      

                if ($action == 'edit') 
                {
                    $data2 = $this->setExcerciseForm('edit', $pr->id);

                    foreach ($data2 as $key => $value) {
                        $data[$key] = $value;
                    }
                }

                if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
                {
                    if($action == 'add') { $this->load->view('backend/athlete/pr_form', $data); }
                    elseif($action == 'edit') { $this->load->view('backend/athlete/pr_form_edit', $data); }
                }
                else
                {
                     $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                $this->load->view('backend/athlete/partials/blank', $data2);

                    if($action == 'add') { $this->load->view('backend/athlete/pr_form', $data); }
                    elseif($action == 'edit') {  $this->load->view('backend/athlete/pr_form_edit', $data); }

                    $this->load->view('backend/partials/footer');
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

/////////////////////////////////////////////////////////
//  SECCION: RUTINAS
/////////////////////////////////////////////////////////

    function routines($ajax = false)
    {
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups) )
        {        
            $data['my_routines'] = $this->wod->getRoutines('user');
            $data['fitbox_routines'] = $this->wod->getRoutines('fitbox');
            $data['public_routines'] = $this->wod->getRoutines('public');
            
            if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
            {  
                $this->load->view('backend/athlete/routines', $data);
            }else{
                 $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                $this->load->view('backend/athlete/partials/blank', $data2);
                $this->load->view('backend/athlete/routines', $data);
                $this->load->view('backend/partials/footer');
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

function routine($action, $id = null)
    {
        
        $allowed_groups = array('athlete');

        if ($this->ion_auth->check_login($allowed_groups))
        {         
             // validate form input
            $this->form_validation->set_rules('id_sport', 'id_sport', 'required');
            $this->form_validation->set_rules('id_phase', 'id_phase', 'required');
            $this->form_validation->set_rules('id_category', 'id_category', 'required');
            $this->form_validation->set_rules('id_type', 'id_type', 'required');
            $this->form_validation->set_rules('name', 'name', 'required');
            $this->form_validation->set_rules('description', 'description', 'required');

            if ($this->form_validation->run() == true)
            {
                
                if ($this->input->post('distance_unit') == 'k') 
                    $distance = $this->input->post('distance') * 1000;
                else if($this->input->post('distance_unit'))
                    $distance = $this->input->post('distance');
                else
                    $distance = null;  

                $time = $this->input->post('secs') + $this->input->post('min')*60 + $this->input->post('hour')*60^2; 
                $RM = 0;
                $RM = $this->input->post('reps')? $this->input->post('load')? $this->wod->calcMaxRep($this->input->post('reps'), $this->input->post('load')) : 0 : 0;

                $additional_data = array(
                    // 'date'      => $this->input->post('date'),
                    'user_id'      => $this->ath->user_id,
                    'id_sport'     => $this->input->post('id_sport'),
                    'id_phase'     => $this->input->post('id_phase'),
                    'id_category'  => $this->input->post('id_category'),
                    'id_type'      => $this->input->post('id_type'),
                    'name'         => $this->input->post('name'),
                    'description'  => $this->input->post('description'),

                    // 'excercise_id' => $this->input->post('excercise_id'),

                    'time'      => $this->input->post('time')? $this->input->post('time'): null,
                    'RM'        => is_array($RM)? $RM[1] : null,
                    'load'      => $this->input->post('load')? $this->input->post('load') : null,
                    'reps'      => $this->input->post('reps')? $this->input->post('reps') : null,
                    'tons'      => $this->input->post('reps')? $this->input->post('load')? $this->input->post('reps')*$this->input->post('load') : 0 : 0,
                    'distance'  => $distance,
                    'height'    => $this->input->post('height')? $this->input->post('height') : 0,
                    'manual'    => 1
                );
                
                if($action == 'add' OR $action == 'adapt')
                {
                    $this->wod->registerRoutine($additional_data);
                }
                elseif($action == 'edit')
                {
                    $this->wod->updateRoutine($id, $additional_data);
                }

                
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->routines(true);
            }
            else
            {
                $data['action'] = $action;
                // set the flash data error message if there is one
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $data['id'] = $id; 

                if($action == 'add')
                { 
                    $data['page_title'] = "Register custom routine";
                    $data2 = $this->setRoutineForm($action);
                }
                elseif ($action == 'edit' OR $action == 'adapt') 
                {
                    $data['page_title'] = "Edit routine";
                    $data2 = $this->setRoutineForm($action, $id);
                }

                foreach ($data2 as $key => $value) {
                    $data[$key] = $value;
                }

                if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
                {
                    $this->load->view('backend/athlete/routine_form', $data); 
                    // $this->load->view('backend/athlete/routine_form_open', $data); 
                    // if($action == 'edit' OR $action == 'adapt') { $this->load->view('backend/athlete/routine_form_aditional', $data); }
                    // $this->load->view('backend/athlete/routine_form_close');

                }
                else
                {
                     $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));

                $this->load->view('backend/athlete/partials/blank', $data2);
                    $this->load->view('backend/athlete/routine_form', $data); 
                    // $this->load->view('backend/athlete/routine_form_open', $data); 
                    // if($action == 'edit' OR $action == 'adapt') {  $this->load->view('backend/athlete/routine_form_aditional', $data); }
                    // $this->load->view('backend/athlete/routine_form_close');
                    $this->load->view('backend/partials/footer');
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function setRoutineForm($action = null, $id = null)
    {
        if ($id == null)
        {
            $routine = (object) [
                    'id' => '',
                    'id_box' => '',
                    'user_id' => '',
                    'id_phase' => '',
                    'id_category' => '',
                    'id_type' => '',
                    'id_sport' => '',
                    'name' => '',
                    'description' => '',
                    'rounds' => '',
                    'time' => '',
                    'max time' => '',
                    'ton' => '', 
                    'toff' => '',
                    'MTPR' => '', //Max Time per Round
                    'TPR' => '', //Time per Round
                    'RPR' => '', //Rest per Round
                    'MTPE' => '', //Max Time per Excercise
                    'TPE' => '', //Time per Excercise
                    'RPE' => '', //Rest per excercise
                    'RBS' => '', //Rest Before Start
                    'RAF' => '' //Rest After Finished
                ];
        }
        else
        {
            $routine = $this->wod->getRoutine($id);
        }

        $data['excercise_list'] = $this->wod->getExcercises(); 
        $data['phase_list'] = $this->wod->getPhases();
        $data['type_list'] = $this->wod->getTypes();
        $data['category_list'] = $this->wod->getCategories();
        $data['sport_list'] = $this->wod->getSports();

        $data['phase_status'] = $routine->id_phase;
        $data['id_phase'] = array(
            'name'  => 'id_phase',
            'id'    => 'id_phase',
            'class' => 'form-control ',
            'type'  => 'text',
            'required' => 'required',
            'value' => $routine->id_phase,
        );

        $data['category_status'] = $routine->id_category;
        $data['id_category'] = array(
            'name'  => 'id_category',
            'id'    => 'id_category',
            'class' => 'form-control ',
            'type'  => 'text',
            'required' => 'required',
            'value' => $routine->id_category,
        );

        $data['phase_status'] = $routine->id_type;
        $data['id_type'] = array(
            'name'  => 'id_type',
            'id'    => 'id_type',
            'class' => 'form-control ',
            'type'  => 'text',
            'required' => 'required',
            'value' => $routine->id_type,
        );

        $data['sport_status'] = $routine->id_sport;
        $data['id_sport'] = array(
            'name'  => 'id_sport',
            'id'    => 'id_sport',
            'class' => 'form-control select2',
            'type'  => 'text',
            'required' => 'required',
            'value' => $routine->id_sport,
        ); 

        $data['name_status'] = $routine->name;
        $data['name'] = array(
            'name'  => 'name',
            'id'    => 'name',
            'class' => 'form-control ',
            'type'  => 'text',
            'required' => 'required',
            'value' => $routine->name,
        );       

        $data['description_status'] = $routine->description;
        $data['description'] = array(
            'name'  => 'description',
            'id'    => 'description',
            'class' => 'form-control ',
            'type'  => 'text',
            'required' => 'required',
            'value' => $routine->description,
        );        


        if($action == 'edit') $type = $this->wod->getType($routine->id_type);
        if($action == 'update')  $type = $this->wod->getType($this->input->post('id_type')); 

        if($action == 'edit' OR $action == 'adapt' OR $action == 'update')
        {
            $data['type'] = $type;

            foreach ($type as $key => $value) 
            {
                if($key !='id' && $key !='type' && ($value == 1 OR $value == 2))
                {
                    $data[$key] = array(
                        'name'  => $key,
                        'id'    => $key,
                        'class' => 'form-control spin',
                        'type'  => 'text',
                        'required' => ($value == 1) ? "required" : "",
                        'value' => $routine->$key,
                    );
                }
            }
        }

        if($action == 'update')  $this->load->view('backend/athlete/routine_form_aditional', $data); else return $data;
    }

////////////////////////
////////// FUNCIONES
///////////////////////
///
///
function alpha_space($fullname)
    {
        if (! preg_match('/^[a-zA-ZñÑáÁéÉíÍóÓúÚ\s]+$/', $fullname)) {
            $this->form_validation->set_message('alpha_dash_space', 'El campo %s solo permite caracteres alfabéticos y espacios.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function valid_dni($str)
    {
        $str = trim($str);  
        $str = str_replace("-","",$str);  
        $str = str_ireplace(" ","",$str);

        if ( !preg_match("/^[0-9]{7,8}[a-zA-Z]{1}$/" , $str) )
        {
            $this->form_validation->set_message('valid_dni', 'El formato del {field} es incorrecto.');
            return FALSE;
        }
        else
        {
            $n = substr($str, 0 , -1);      
            $letter = substr($str,-1);
            $letter2 = substr ("TRWAGMYFPDXBNJZSQVHLCKE", $n%23, 1); 
            if(strtolower($letter) != strtolower($letter2))
            {
                $this->form_validation->set_message('valid_dni', 'La letra del {field} no se corresponde con la numeración.');
                return FALSE;
            }
        }
        return TRUE;
    }
}
