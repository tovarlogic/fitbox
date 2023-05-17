<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Staff extends MY_Controller {
    
    public $is_admin = FALSE;

    function __construct() 
    {
        parent::__construct();
        $this->load->database();

        $this->load->library(array('ion_auth','form_validation','booking_lib','iban','encryption','toolbox_lib'));

        $this->load->helper(array('language'));

        $this->load->model('box_model', 'box');
        $this->load->model('logs_model', 'logs');
        $this->load->model('booking_model', 'booking');
        $this->load->model('ion_auth_model', 'ion');
        $this->load->model('payment_model', 'pay');

        $this->lang->load(array('auth','fitbox','booking'));

        $this->config->load('settings', TRUE);

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->output->enable_profiler(FALSE);
        
        $staff = array('sadmin', 'admin', 'fcoach', 'coach', 'finance', 'rrhh', 'comercial', 'marketing');
        if ($this->ion_auth->logged_in() && $this->ion_auth->in_group($staff))
        {
            $this->box->set_box();
            $this->pay->set_box($this->box->box_id);
            $this->booking->set_box($this->box->box_id);
        }   

        if($this->ion_auth->is_admin()) $this->is_admin = TRUE;
    }

    /**
     * Function: index
     * Shows the staff dashboard
     *
     */
    function index() 
    {
        $allowed_groups = array('sadmin', 'admin', 'rrhh', 'finance', 'fcoach', 'coach', 'comercial', 'marketing');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $users = array();
            $users['new'] = $this->logs->get_users_log($this->box->box_id, 'new');           

            $members = array();
            $members['cancelled'] = $this->logs->get_members_log($this->box->box_id, 'canceled') + $this->logs->get_members_log($this->box->box_id, 'auto cancelled');
            $members['new'] = $this->logs->get_members_log($this->box->box_id, 'new');
            $members['renew'] = $this->logs->get_members_log($this->box->box_id, 'renew');
            $members['expired'] = $this->logs->get_members_log($this->box->box_id, 'expired');

            $groups = array(11);
            $params = array('status'=>'y');
            $members['active'] = $this->box->getTotalClients($this->box->box_id,$groups, $params);

            $params = array('status'=>'g');
            $members['grace'] = $this->box->getTotalClients($this->box->box_id,$groups, $params);

            $params = array('status'=>'p');
            $members['pending'] = $this->box->getTotalClients($this->box->box_id,$groups, $params);

            $result = $this->box->getNoPlanClients($this->box->box_id, $groups);
            $members['no_plan'] = ($result !== FALSE)? sizeof($result) : 0;

            $groups = array(12);
            $result = $this->box->get_users($this->box->box_id,$groups);
            $members['guests'] = ($result !== FALSE)? sizeof($result) : 0;

            

            //log_members
            $members['new_history'] = $this->box->convert_to_chart($this->logs->get_members_log($this->box->box_id, 'new', 12));
            $members['renew_history'] = $this->box->convert_to_chart($this->logs->get_members_log($this->box->box_id, 'renew', 12));
            $members['expired_history'] = $this->box->convert_to_chart($this->logs->get_members_log($this->box->box_id, 'expired', 12));
            $members['cancelled_history'] = $this->box->convert_to_chart($this->logs->get_members_log($this->box->box_id, array('action' => 'canceled','action' => 'auto cancelled'), 12));

            //log_totales
            $params = array('type' => array('active', 'grace'));
            $members['active_history'] = $this->box->convert_to_chart($this->logs->get_total_clients_log($this->box->box_id, $params, 12));
            $params = array('type' => array('pending'));
            $members['pending_history'] = $this->box->convert_to_chart($this->logs->get_total_clients_log($this->box->box_id, $type, 12));
            $params = array('type' => array('guests'));
            $members['guests_history'] = $this->box->convert_to_chart($this->logs->get_total_clients_log($this->box->box_id,  $type, 12));

            $data['users'] = $users;
            $data['members'] = $members;

            $data['box'] = $this->box->getBox();

            $this->show_view($data, 'staff', 'dashboard');

        }
        else
        {
            redirect('auth', 'refresh');
        }

    }
   


////////////////////
// Section: Users //
////////////////////

    /**
     * Function: users
     *
     * @param  [type] $action [description]
     * @param  [type] $id [description]
     *
     * @return [type] [description]
     */
    function users($action = null, $id = null)
    {
        $allowed_groups = array('sadmin', 'admin', 'fcoach', 'comercial');

        if ($this->ion_auth->check_login($allowed_groups) )
        {   
            switch ($action) {
                case 'edit':
                    if(!is_null($id))
                    {
                        $user = $this->box->getUser($id);
                        if($user !== false)
                        {
                            $this->edit_user($user);
                            break;
                        }         
                    }

                    $this->show_users();
                    break;
                
                case 'add':
                    $this->create_user();
                    break;

                case 'delete':
                    if(!is_null($id))
                    {
                        $user = $this->box->getUser($id);
                        if($user !== false)
                        {
                            $this->delete_user($user);
                            break;
                        }         
                    }

                    $this->show_users();
                    break;

                default:
                    $this->show_users();
                    break;
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }


    /**
     * Function: show_users
     *
     * @return [type] [description]
     */
    private function show_users()
    {
        $groups = array(2,3,4,5,6,7,8,9,10);
        $data['staff'] = $this->box->get_users($this->box->box_id, $groups);

        $groups = array(12);
        $data['guests'] = $this->box->get_users($this->box->box_id, $groups);

        $groups = array(11);
        $params = array('status' => array('y','p','g'));
        $data['clients'] = $this->box->getClients($groups, $params);

        $params = array('status' => array('n','e','c'));
        $data['clients_inactive'] = $this->box->getClients($groups, $params);

        $groups = array(11);
        $data['pending'] = $this->box->getNoPlanClients($this->box->box_id, $groups);

        $data['login_group'] = $this->box->get_user_group($this->session->userdata('user_id'));

        $this->show_view($data, 'staff', 'users');
    }

    /**
     * Function: edit_user
     *
     * @return [type] [description]
     */
    private function edit_user($user)
    {
        $login_group = $this->box->get_user_group($this->session->userdata('user_id'));
        $user_group = $this->box->get_user_group($user->id);
        $user->user_group = $user_group;

        $tables = $this->config->item('tables','ion_auth');
        $identity_column = $this->config->item('identity','ion_auth');

        $this->user_form_set_validation($user, $tables, $identity_column);

        if ($this->form_validation->run() == true)
        {
            $additional_data = $this->user_form_get_posted_data();

            if($login_group < $user_group)
            {
                $this->db->trans_start();
                    if($this->box->editUser($user->id, $additional_data))
                    {
                        if($this->box->editGroups($user->id, $this->input->post('group')))
                            $this->session->set_flashdata('success', 'Datos de usuario actualizados.');
                        else
                            $this->session->set_flashdata('error', 'Error al actualizar el/los grupo/s del usuario');  
                    }
                    else
                        $this->session->set_flashdata('error', 'No se pudo editar el usuario.');
                $this->db->trans_complete();

                $this->session->set_flashdata('message', $this->ion_auth->messages());
            }

            $this->show_users();
        }
        else
        {
            $data = $this->user_form_create($user);
            $data['action'] = 'edit';
            $data['page_title'] = "Editar usuario";

            $groups = $this->ion_auth->get_users_groups($user->id)->result_array();
            foreach ($groups as $key => $value) {
                $data['groups'][] =  $groups[$key]['id'];
            }

            if (!empty(form_error('group'))) $class = "error"; else $class = "valid";
            $data['group'] = array(
                'name'  => 'group',
                'id'    => 'group',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'required' => '',
                'aria-required' => "true",
                'value' => $this->form_validation->set_value('group', $data['groups']), //pendiente que muestre los grupos enviados originalmente en casio de error de validación
            );

            $this->show_view($data, 'staff', 'user_form');
        }
    }

    /**
     * Function: create_user
     *
     * @return [type] [description]
     */
    private function create_user()
    {
        $user = (object)[
            'box_id'    => $this->box->box_id,
            'email'      => '',
            'username'      => '',
            'first_name'      => '',
            'last_name'      => '',
            'DNI'      => '',
            'IBAN'      => '',
            'gender'      => '',
            'active'      => '',
            'phone'      => '',
            'private'      => '',
            'birth_date'      => '',
            'year'      => '',
            'month'      => '',
            'day'      => ''
        ];

        $tables = $this->config->item('tables','ion_auth');
        $identity_column = $this->config->item('identity','ion_auth');

        $this->user_form_set_validation($user, $tables, $identity_column);

        if ($this->form_validation->run() == true)
        {
            $additional_data = $this->user_form_get_posted_data();

            $email    = strtolower($this->input->post('email'));
            $identity = ($identity_column==='email') ? $email : $this->input->post('identity');
            $password = $this->generate_password(8);
            $id = null;
            $error = false;

            $this->db->trans_start();
                if($this->ion_auth->register($identity, $password, $email, $additional_data, $this->input->post('group')))
                {
                    $id = $this->box->create_user($email);
                    if($id)
                    {
                        $this->session->set_flashdata('success', 'Nuevo usuario creado.');
                    }
                    else
                    { 
                        $error = true;
                        $this->session->set_flashdata('error', 'No se pudo crear el nuevo usuario.');
                    }
                }
                else
                {
                    $error = true;
                    $this->session->set_flashdata('error', 'No se pudo registrar el nuevo usuario.');
                }
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE OR $error === true)
            {
                $this->show_users();
            }
            else
            {
                $data2 = $this->prepare_data_to_addMembership_form($id);
                $this->load->view('backend/staff/membership_user_form', $data2);
            }   
        }
        else
        {
            $data = $this->user_form_create($user);
            $data['action'] = 'add';
            $data['page_title'] = "Crear Usuario";

            if (!empty(form_error('group'))) $class = "error"; else $class = "valid";
            $data['group'] = array(
                'name'  => 'group',
                'id'    => 'group',
                'class' => 'form-control '.$class,
                'type'  => 'text',
                'required' => '',
                'aria-required' => "true",
                'value' => $this->form_validation->set_value('group', $data['groups']), //pendiente que muestre los grupos enviados originalmente en casio de error de validación
            );

            $this->show_view($data, 'staff', 'user_form');    
        }
    }

    /**
     * Function: user_form_create
     *
     * @param  [type] $user [description]
     *
     * @return [type] [description]
     */
    private function user_form_create($user)
    {
        // display the create user form
        // set the flash data error message if there is one
        $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        if($user->email != '') $data['user_id'] = $user->id;

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

        if (!empty(form_error('email'))) $class = "error"; else $class = "valid";
        $data['email'] = array(
            'name'  => 'email',
            'id'    => 'email',
            'class' => 'form-control '.$class,
            'type'  => 'email',
            'required' => '',
            'aria-required' => "true",
            'value' => $this->form_validation->set_value('email', $user->email),
        );

        if (!empty(form_error('DNI'))) $class = "error"; else $class = "valid";
        $data['DNI'] = array(
            'name'  => 'DNI',
            'id'    => 'DNI',
            'class' => 'form-control '.$class,
            'type'  => 'text',
            'value' => strtoupper($this->form_validation->set_value('DNI', $user->DNI)),
        );

        if (!empty(form_error('IBAN'))) $class = "error"; else $class = "valid";
        $data['IBAN'] = array(
            'name'  => 'IBAN',
            'id'    => 'IBAN',
            'class' => 'form-control '.$class,
            'type'  => 'text',
            'value' => strtoupper($this->form_validation->set_value('IBAN', $user->IBAN)),
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

        $data['active_status'] = $user->active;
        $data['active_options'] = array('1' =>'Activo', '0'=> 'Inactivo');
        $data['sex'] = $user->gender;
        $data['date'] = $date;
        $data['genders'] = array('' =>'-- Seleccione --', 'M' =>'Masculino', 'F'=> 'Femenino');
        $data['days'] = $this->toolbox_lib->generate_list(1,31);
        $data['months'] = $this->toolbox_lib->generate_list(1,12);
        $data['years'] = $this->toolbox_lib->generate_list(date('Y')-80,date('Y'));

        $data['active'] = array(
            'name'  => 'active',
            'id'    => 'active',
            'class' => 'form-control '.$class,
            'type'  => 'text',
            'value' => $this->form_validation->set_value('active', $user->active), 
        );
        $data['active_options'] = array('1' =>'Activo', '0'=> 'Inactivo');

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

        $data['groups'] = array();

        $options = $this->ion_auth->groups()->result_array();
        foreach ($options as $key => $value) {
            if($options[$key]['id'] > 1)
            {
                if( ($this->ion_auth->in_group('sadmin') && $options[$key]['id'] > 2) OR
                    ($this->ion_auth->in_group('admin') && $options[$key]['id'] > 3) OR
                    ($this->ion_auth->in_group('comercial') && $options[$key]['id'] == 11) )
                {
                    $data['group_options'][$options[$key]['id']] =  $options[$key]['description'];
                }

            }
        }
        

        return $data;
    }

    /**
     * Function: user_form_get_posted_data
     *
     * @return [type] [description]
     */
    private function user_form_get_posted_data()
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
            'IBAN'    => $this->input->post('IBAN'),
            'DNI'    => $this->input->post('DNI'),
            'gender'      => $this->input->post('gender'),
            'active'      => $this->input->post('active'),
            'phone'      => $this->input->post('phone'),
            'birth_date' => $birth_date,

        );

        if($this->input->post('DNI') == null) $additional_data['DNI'] = NULL;

        return $additional_data;
    }

    /**
     * Function: user_form_set_validation
     *
     * @param  [type] $user [description]
     * @param  [type] $tables [description]
     * @param  [type] $identity_column [description]
     *
     * @return [type] [description]
     */
    private function user_form_set_validation($user, $tables, $identity_column)
    {
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
                }
                
            }

            if($this->input->post('DNI') != $user->DNI && $this->input->post('DNI') != null)
            {
                $this->form_validation->set_rules('DNI', 'DNI', 'is_unique[' . $tables['users'] . '.DNI]|callback_valid_dni');
            }

            if($this->input->post('IBAN') != $user->IBAN && $this->input->post('IBAN') != null)
            {
                $this->form_validation->set_rules('IBAN', 'IBAN', 'callback_valid_IBAN');
            }

            $this->form_validation->set_rules('group', 'Grupo/s', 'callback_multiple_select');
            $this->form_validation->set_rules('gender', 'Sexo', 'required');
            $this->form_validation->set_rules('phone', 'Telefono', 'required|regex_match[/^[0-9]{9}$/]'); //{9} for 9 digits number
    }

    /**
     * Function: delete_user
     *
     * @param  [type] $user_id [description]
     *
     * @return [type] [description]
     */
    private function delete_user($user) 
    {
        //if the request cames from an allowed staf user AND user is registered in this box AND is not deleting itself
        if ($user->id != $this->session->userdata('user_id'))
        {
            $user_boxes = $this->box->getUserBoxes($user->id);
            //If user is Only registered in this box
            if($user_boxes !== FALSE AND sizeof($user_boxes) == 1)
            {
                if($this->box->isUserDeletable($user->id))
                {
                    $this->db->trans_start();
                        if($this->box->deleteUserData($user->id) )
                        {
                            if($this->ion_auth->delete_user($user->id))
                            {
                                $this->session->set_flashdata('success', 'Usuario borrado'); 
                            }
                            else
                                $this->session->set_flashdata('info', 'No se pudo eliminar el usuario.');  
                        }     
                        else 
                            $this->session->set_flashdata('error', 'No se pudieron borrar los datos asociados al usuario.'); 
                    $this->db->trans_complete();
                }
                else
                {
                    $this->session->set_flashdata('info', 'No se puede borrar usuarios que ya cuentan con transacciones. Sólo puedes darle de baja y desactivarlo.');     
                }
                
            }
            else
            {
                $this->session->set_flashdata('info', 'No se puede borrar al usuario porque también está registrado en otros boxes. Sólo puedes darle de baja y desactivarlo.'); 
            } 

            $this->show_users();
        }
        else
        {
            $this->load->view('backend/no_session');
        }

               
    }


//////////////////////
// Section: Coupons //
//////////////////////

    function coupons($ajax = false)
    {

        $allowed_groups = array('sadmin', 'admin', 'comercial');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $data['coupons'] = $this->booking->getCoupons();
            $this->show_view($data, 'staff', 'coupons');
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function coupon($action, $coupon_id = null)
    {
        
        $allowed_groups = array('sadmin', 'admin', 'comercial', 'Coach + Comercial');

        if ($this->ion_auth->check_login($allowed_groups))
        {

            $data['action'] = $action;
            if($action == 'add')
            {
                $data['page_title'] = "Create Coupon";
                $coupon = (object) [
                    'box_id'    => $this->box->box_id,
                    'title'      => '',
                    'code'      => '',
                    'value'      => '',
                    'type'      => '',
                    'dateFrom'      => '',
                    'dateTo'      => '',
                    'limit'      => '',
                    'status'      => '',
                    'services'      => ''
                ];
            }
            elseif($action == 'edit')
            {
                $data['page_title'] = "Edit Coupon";
                $coupon = $this->booking->getCoupon($coupon_id);
            }
            

            // validate form input
            $is_unique = ($this->input->post('title') != $coupon->title) ? 'required|is_unique[bs_coupons.title]' : 'required';
            $this->form_validation->set_rules('title', 'title', $is_unique);
            $is_unique = ($this->input->post('code') != $coupon->code) ? 'required|is_unique[bs_coupons.code]' : 'required';
            $this->form_validation->set_rules('code', 'code', $is_unique);
            $this->form_validation->set_rules('value', 'value', 'required|integer');
            $this->form_validation->set_rules('type', 'type', 'required');
            $this->form_validation->set_rules('date_from', 'date_from', 'required');
            $this->form_validation->set_rules('date_to', 'date_to', 'required');
            $this->form_validation->set_rules('limit', 'limit', 'required|integer');
            $this->form_validation->set_rules('status', 'status', 'required');
            $this->form_validation->set_rules('services', 'services', 'required');

            if ($this->form_validation->run() == true)
            {
                $additional_data = array(
                    'box_id'    => $this->box->box_id,
                    'title'      => $this->input->post('title'),
                    'code'      => $this->input->post('code'),
                    'value'      => $this->input->post('value'),
                    'type'      => $this->input->post('type'),
                    'dateFrom'      => $this->input->post('date_from').' 00:00:00',
                    'dateTo'      => $this->input->post('date_to').' 23:59:59',
                    'limit'      => $this->input->post('limit'),
                    'status'      => $this->input->post('status'),
                    'services'      => $this->input->post('services')
                );
            }
            if ($this->form_validation->run() == true)
            {
                
                if($action == 'add')
                {
                    if($this->booking->create_coupon($additional_data))
                        $this->session->set_flashdata('success', 'Nuevo cupón de descuento creado');
                    else
                        $this->session->set_flashdata('error', 'No se pudo crear el nuevo cupón de descuento.');
                }
                elseif($action == 'edit')
                {
                    if($this->booking->edit_coupon($coupon_id, $additional_data))
                        $this->session->set_flashdata('success', 'Cupón de descuento editado.');
                    else
                        $this->session->set_flashdata('error', 'No se pudo editar el cupón de descuento.');
                }

                
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->coupons();
            }
            else
            {
                // display the create user form
                // set the flash data error message if there is one
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $data['coupon_id'] = $coupon_id;

                if (!empty(form_error('title'))) $class = "error"; else $class = "valid";
                $data['title'] = array(
                    'name'  => 'title',
                    'id'    => 'title',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $coupon->title,
                );

                if (!empty(form_error('code'))) $class = "error"; else $class = "valid";
                $data['code'] = array(
                    'name'  => 'code',
                    'id'    => 'code',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $coupon->code,
                );

                if (!empty(form_error('value'))) $class = "error"; else $class = "valid";
                $data['value'] = array(
                    'name'  => 'value',
                    'id'    => 'value',
                    'class' => 'form-control '.$class,
                    'type'  => 'number',
                    'min'  => '1',
                    'max'  => '100',
                    'required' => '',
                    'value' => $coupon->value,
                );

                if (!empty(form_error('type'))) $class = "error"; else $class = "valid";
                $data['type_status'] = $coupon->type;
                $data['type'] = array(
                    'name'  => 'type',
                    'id'    => 'type',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $coupon->type,
                );

                if (!empty(form_error('date_from'))) $class = "error"; else $class = "valid";
                $date=explode(' ', $coupon->dateFrom);
                $data['date_from'] = array(
                    'name'  => 'date_from',
                    'id'    => 'date_from',
                    'class' => 'form-control '.$class,
                    'type'  => 'date',
                    'data-date-format' => 'yyyy-mm-dd',
                    'required' => '',
                    'value' => $date[0],
                );

                if (!empty(form_error('date_to'))) $class = "error"; else $class = "valid";
                $date=explode(' ', $coupon->dateTo);
                $data['date_to'] = array(
                    'name'  => 'date_to',
                    'id'    => 'date_to',
                    'class' => 'form-control '.$class,
                    'type'  => 'date',
                    'data-date-format' => 'yyyy-mm-dd',
                    'required' => '',
                    'value' => $date[0],
                );

                if (!empty(form_error('limit'))) $class = "error"; else $class = "valid";
                $data['limit'] = array(
                    'name'  => 'limit',
                    'id'    => 'limit',
                    'class' => 'form-control '.$class,
                    'type'  => 'number',
                    'required' => '',
                   'value' => $coupon->limit,
                );

                if (!empty(form_error('status'))) $class = "error"; else $class = "valid";
                $data['status_status'] = $coupon->status;
                $data['status'] = array(
                    'name'  => 'status',
                    'id'    => 'status',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $coupon->status,
                );

                if (!empty(form_error('services'))) $class = "error"; else $class = "valid";
                $data['services_status'] = $coupon->services;
                $data['services'] = array(
                    'name'  => 'services',
                    'id'    => 'services',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                   'value' => $coupon->services,
                );

                $data['status_list'] = array('' =>'-- Seleccione --', '1' =>'Activo', '0'=> 'Inactivo');
                $data['type_list'] = array('' =>'-- Seleccione --', 'abs' =>'Absoluto (€)', 'rel'=> 'Porcentaje (%)');

                $options = $this->booking->get_services();

                foreach ($options as $key => $value) {
                    $data['services_list'][$options[$key]['id']] =  $options[$key]['name'];
                }

                $this->show_view($data, 'staff', 'coupon_form');
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function deleteCoupon($coupon_id) 
    {
        $allowed_groups = array('sadmin', 'admin', 'fcoach');

        if ($this->ion_auth->check_login($allowed_groups))
        {
            if($this->booking->getCoupon($coupon_id))
                if($this->booking->delete_coupon($coupon_id))
                        $this->session->set_flashdata('success', 'Cupón de descuento eliminado.');
                    else
                        $this->session->set_flashdata('error', 'No se pudo eliminar el cupón de descuento.');  
            else
                $this->session->set_flashdata('info', 'No existe el cupón de descuento indicado.');

           $this->coupons();         
        }
        else
        {
            $this->load->view('backend/no_session');
        }     
    }

//////////////////////////////////////////////////////////////////////////
// Sección Tarifas
//////////////////////////////////////////////////////////////////////////

    function memberships($ajax = false)
    {
        $allowed_groups = array('sadmin', 'admin', 'comercial');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $data['memberships'] = $this->box->getmemberships();
            $data['subscriptions'] = $this->box->getMembershipSubscriptions();

            $this->show_view($data, 'staff', 'memberships');
        }
        else
        {
            $this->load->view('backend/no_session');
        } 
    }

    function membership($action, $membership_id = null)
    {
        
        $allowed_groups = array('sadmin', 'admin', 'Finance');

        if ($this->ion_auth->check_login($allowed_groups))
        {
            $data['action'] = $action;

            // validate form input
            $this->form_validation->set_rules('title', 'title', 'required');
            $this->form_validation->set_rules('price', 'price', 'required|numeric');
            $this->form_validation->set_rules('service[]', 'service', 'required');
            if($action == 'add')
            {
                $this->form_validation->set_rules('days', 'days', 'required|integer');
                $this->form_validation->set_rules('period', 'period', 'required');  
            }

            if ($this->form_validation->run() == true)
            {
                $type = ($this->box->box_id == 0) ? 'Box' : 'Ath' ;
                if($this->input->post('period') == 'D')
                    $recurring = 0;
                else
                    $recurring = $this->input->post('recurring');

                if($action == 'add')
                    $deprecated = 0;
                else
                    $deprecated = $this->input->post('deprecated');

                $additional_data = array(
                    'box_id'    => $this->box->box_id,
                    'title'      => $this->input->post('title'),
                    'description'      => $this->input->post('description'),
                    'price'      => $this->input->post('price'),
                    'days'      => $this->input->post('days'),
                    'max_reservations'      => $this->input->post('max_reservations'),
                    'period'      => $this->input->post('period'),
                    'trial'      => $this->input->post('trial'),
                    'recurring'      => $recurring,
                    'private'      => $this->input->post('private'),
                    'active'      => $this->input->post('active'),
                    'available_from'      => $this->input->post('available_from'),
                    'available_to'      => $this->input->post('available_to'),
                    'type'      => $type,
                    'deprecated' => $deprecated
                );
                
                if($action == 'add')
                {
                    $this->db->trans_start();
                        if($mem_id = $this->box->create_membership($additional_data))
                        {
                            if($this->box->addMembershipService($mem_id, $this->input->post('service')))
                                $this->session->set_flashdata('success', 'Nuevo plan de membresía creado.');
                            else
                                $this->session->set_flashdata('error', 'No se pudo asignar los servicios al nuevo plan de membresía.');
                        }
                        else
                            $this->session->set_flashdata('error', 'No se pudo crear el nuevo plan de membresía.');
                    $this->db->trans_complete();
                }
                elseif($action == 'edit')
                {
                    if($recurring != 0)
                    {
                        //to be consistent with subscriptions, if recurrent not allowed to edit 
                        unset($additional_data['days']);
                        unset($additional_data['period']);
                    }
                    $this->db->trans_start();
                        if($this->box->edit_membership($membership_id, $additional_data))
                        {                    
                            $this->box->editServices($membership_id, $this->input->post('service'));
                                
                            $this->session->set_flashdata('success', 'Plan de membresía editado.');
                        }
                        else
                            $this->session->set_flashdata('error', 'No se pudo editar el plan de membresía.');
                    $this->db->trans_complete();

                    //Update info in online gateways
                    $this->load->library(array('gocardless'));
                    $this->gocardless->set_up($this->box->box_id);
                    $gateway = $this->gocardless->get_gateway();

                    $params = array('gateway' => $gateway['pp'],
                                    'box_id' => $gateway['box_id'],
                                    'demo' => $gateway['demo']);

                    $mus = $this->box->getMembershipsUsers(array('membership_id' => $membership_id));
                    if(!empty($mus))
                    {
                        foreach ($mus as $mu) 
                        {
                            $params['mu_id'] = $mu->id;
                            $subscription = $this->pay->getGatewayTransactions('subscriptions', $params);
                            if($subscription !== false)
                            {
                                $this->pay->updateGatewayTransaction($subscription[0]->txn_id, 'subscriptions', array('amount' => $additional_data['price']*100));

                                $params2 = array(
                                'name' => $additional_data['title'],
                                'amount' => (int)$additional_data['price']*100);

                                // GC no permite cambiar el intervalo. Si este cambiase habría que crear un nuevo plan..
                                $result = $this->gocardless->updateSubscription($subscription[0]->txn_id, $params2);
                                if($result == false)
                                    $this->session->set_flashdata('error', 'No se han podido actualizar las subscripciones a este plan en GoCardless.');
                                else
                                    $this->session->set_flashdata('success', 'Plan de membresía editado y subscripciones en Gocardless actualizadas. En caso de existir cobros previamente emitidos, estos no sufrirán cambios.');

                            }
                        }
                    }

                }

                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->memberships();
            }
            else 
            {
                
                if($action == 'add')
                {
                    $data['page_title'] = "Create Membership";
                    $membership = (object) [
                        'box_id'    => $this->box->box_id,
                        'title'      => '',
                        'description'      => '',
                        'price'      => '',
                        'days'      => '',
                        'max_reservations' => '',
                        'period'      => '',
                        'trial'      => '',
                        'recurring'      => '',
                        'private'      => '',
                        'available_from'      => '0000',
                        'available_to'      => '2359',
                        'active'      => ''
                    ];

                    $data['services'] = '';
                }
                elseif($action == 'edit')
                {
                    $data['page_title'] = "Modificación de planes";
                    $membership = $this->box->getMembership($membership_id);

                    $data['services'] = $this->box->getMembershipServices($membership_id);
                }


                // display the create user form
                // set the flash data error message if there is one
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $data['membership_id'] = $membership_id;

                if (!empty(form_error('title'))) $class = "error"; else $class = "valid";
                $data['title'] = array(
                    'name'  => 'title',
                    'id'    => 'title',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => ($this->input->post('title'))? $this->form_validation->set_value('title', $this->input->post('title')): $membership->title,
                );

                if (!empty(form_error('description'))) $class = "error"; else $class = "valid";
                $data['description'] = array(
                    'name'  => 'description',
                    'id'    => 'description',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'value' => ($this->input->post('description'))? $this->form_validation->set_value('description', $this->input->post('description')): $membership->description,
                );

                if (!empty(form_error('price'))) $class = "error"; else $class = "valid";
                $data['price'] = array(
                    'name'  => 'price',
                    'id'    => 'price',
                    'class' => 'form-control '.$class,
                    'type'  => 'number',
                    'required' => '',
                    'value' => ($this->input->post('price'))? $this->form_validation->set_value('price', $this->input->post('price')): $membership->price,
                );

                if (!empty(form_error('days'))) $class = "error"; else $class = "valid";
                $data['days'] = array(
                    'name'  => 'days',
                    'id'    => 'days',
                    'class' => 'form-control '.$class,
                    'type'  => 'number',
                    'required' => '',
                    'value' => ($this->input->post('days'))? $this->form_validation->set_value('days', $this->input->post('days')): $membership->days,
                );
                if($action == 'edit' AND $membership->recurring != 0)
                {
                    $data['days']['disabled'] = '';
                    unset($data['days']['required']);
                }

                if (!empty(form_error('max_reservations'))) $class = "error"; else $class = "valid";
                $data['max_reservations'] = array(
                    'name'  => 'max_reservations',
                    'id'    => 'max_reservations',
                    'class' => 'form-control '.$class,
                    'type'  => 'number',
                    'required' => '',
                    'value' => ($this->input->post('max_reservations'))? $this->form_validation->set_value('max_reservations', $this->input->post('max_reservations')): $membership->max_reservations,
                );

                if (!empty(form_error('available_from'))) $class = "error"; else $class = "valid";
                $data['available_from'] = array(
                    'name'  => 'available_from',
                    'id'    => 'available_from',
                    'class' => 'form-control '.$class,
                    'type'  => 'number',
                    'required' => '',
                    'value' => ($this->input->post('available_from'))? $this->form_validation->set_value('available_from', $this->input->post('available_from')): $membership->available_from,
                );

                if (!empty(form_error('available_to'))) $class = "error"; else $class = "valid";
                $data['available_to'] = array(
                    'name'  => 'available_to',
                    'id'    => 'available_to',
                    'class' => 'form-control '.$class,
                    'type'  => 'number',
                    'required' => '',
                    'value' => ($this->input->post('available_to'))? $this->form_validation->set_value('available_to', $this->input->post('available_to')): $membership->available_to,
                );

                if (!empty(form_error('period'))) $class = "error"; else $class = "valid";
                $data['period_status'] = $membership->period;

                $data['period'] = array(
                    'name'  => 'period',
                    'id'    => 'period',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => ($this->input->post('period'))? $this->form_validation->set_value('period', $this->input->post('period')): $membership->period,
                );
                if($action == 'edit' AND $membership->recurring != 0)
                {
                    unset($data['period']['required']);
                    $data['period']['disabled'] = '';
                }

                $data['period_list'] = array('' =>'-- Seleccione --', 'Y' =>'Año', 'M'=> 'Mes', 'D'=> 'Día');

                $chk_status = array();
                $retVal = ($this->input->post('trial'))? $this->form_validation->set_value('trial', $this->input->post('trial')): $membership->trial;
                if ($retVal == 0) {
                    $data['trial1'] = array('name' => 'trial', 'value' => '1' );
                    $data['trial2'] = array('name' => 'trial', 'value' => '0', 'checked' => TRUE);
                } else {
                    $data['trial1'] = array('name' => 'trial', 'value' => '1', 'checked' => TRUE );
                    $data['trial2'] = array('name' => 'trial', 'value' => '0' );
                }
                
                $chk_status = array();
                $retVal = ($this->input->post('recurring'))? $this->form_validation->set_value('recurring', $this->input->post('recurring')): $membership->recurring;
                if ($retVal == 0) {
                    $data['recurring1'] = array('name' => 'recurring', 'value' => '1');
                    $data['recurring2'] = array('name' => 'recurring', 'value' => '0', 'checked' => TRUE );
                } else {
                    $data['recurring1'] = array('name' => 'recurring', 'value' => '1', 'checked' => TRUE );
                    $data['recurring2'] = array('name' => 'recurring', 'value' => '0');
                }

                $chk_status = array();
                $retVal = ($this->input->post('private'))? $this->form_validation->set_value('private', $this->input->post('private')): $membership->private;
                if ($retVal == 0) {
                    $data['private1'] = array('name' => 'private', 'value' => '1' );
                    $data['private2'] = array('name' => 'private', 'value' => '0', 'checked' => TRUE);
                } else {
                    $data['private1'] = array('name' => 'private', 'value' => '1', 'checked' => TRUE );
                    $data['private2'] = array('name' => 'private', 'value' => '0' );
                }

                $chk_status = array();
                $retVal = ($this->input->post('active'))? $this->form_validation->set_value('active', $this->input->post('active')): $membership->active;
                if ($retVal == 0) {
                    $data['active1'] = array('name' => 'active', 'value' => '1' );
                    $data['active2'] = array('name' => 'active', 'value' => '0', 'checked' => TRUE);
                } else {
                    $data['active1'] = array('name' => 'active', 'value' => '1', 'checked' => TRUE );
                    $data['active2'] = array('name' => 'active', 'value' => '0' );
                }

                $chk_status = array();
                $retVal = ($this->input->post('deprecated'))? $this->form_validation->set_value('deprecated', $this->input->post('deprecated')): $membership->deprecated;
                if ($retVal == 0) {
                    $data['deprecated1'] = array('name' => 'deprecated', 'value' => '1' );
                    $data['deprecated2'] = array('name' => 'deprecated', 'value' => '0', 'checked' => TRUE);
                } else {
                    $data['deprecated1'] = array('name' => 'deprecated', 'value' => '1', 'checked' => TRUE );
                    $data['deprecated2'] = array('name' => 'deprecated', 'value' => '0' );
                }

                if (!empty(form_error('trial'))) $class = "error"; else $class = "valid";
                $data['trial'] = array(
                    'name'  => 'trial',
                    'id'    => 'trial',
                    'class' => 'form-control '.$class,
                    'type'  => 'checkbox',
                    'required' => '',
                    'value' => ($this->input->post('trial'))? $this->form_validation->set_value('trial', $this->input->post('trial')): $membership->trial,
                );

                if (!empty(form_error('recurring'))) $class = "error"; else $class = "valid";
                $data['recurring'] = array(
                    'name'  => 'recurring',
                    'id'    => 'recurring',
                    'class' => 'form-control '.$class,
                    'type'  => 'checkbox',
                    'required' => '',
                    'value' => ($this->input->post('recurring'))? $this->form_validation->set_value('recurring', $this->input->post('recurring')): $membership->recurring,
                );

                if (!empty(form_error('private'))) $class = "error"; else $class = "valid";
                $data['private'] = array(
                    'name'  => 'private',
                    'id'    => 'private',
                    'class' => 'form-control '.$class,
                    'type'  => 'checkbox',
                    'required' => '',
                    'value' => ($this->input->post('private'))? $this->form_validation->set_value('private', $this->input->post('private')): $membership->private,
                );

                if (!empty(form_error('deprecated'))) $class = "error"; else $class = "valid";
                $data['deprecated'] = array(
                    'name'  => 'deprecated',
                    'id'    => 'deprecated',
                    'class' => 'form-control '.$class,
                    'type'  => 'checkbox',
                    'required' => '',
                    'value' => ($this->input->post('deprecated'))? $this->form_validation->set_value('deprecated', $this->input->post('deprecated')): $membership->deprecated,
                );

                if (!empty(form_error('active'))) $class = "error"; else $class = "valid";
                $data['active'] = array(
                    'name'  => 'active',
                    'id'    => 'active',
                    'class' => 'form-control '.$class,
                    'type'  => 'checkbox',
                    'required' => '',
                    'value' => ($this->input->post('active'))? $this->form_validation->set_value('active', $this->input->post('active')): $membership->active,
                );
                
                $options = $this->box->getServices(1); //get active services...
                foreach ($options as $key => $value) {
                    $data['service_options'][$options[$key]['id']] =  $options[$key]['name'];
                }

                if (!empty(form_error('service'))) $class = "error"; else $class = "valid";
                $data['service'] = array(
                    'name'  => 'service',
                    'id'    => 'service',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('service', $data['services']),
                );

                $this->show_view($data, 'staff', 'membership_form');
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        } 
    }

    function deleteMembership($id)
    {
        $allowed_groups = array('sadmin', 'admin', 'fcoach', 'comercial');
        if ($this->ion_auth->check_login($allowed_groups) )
        {
            if($this->box->getMembership($id))
            {
                if($this->box->delete_membership($id))
                    $this->session->set_flashdata('success', 'Plan de membresía eliminado.');
                else
                    $this->session->set_flashdata('error', 'No se pudo eliminar el plan de membresía.');
            }
            else
                $this->session->set_flashdata('error', 'No existe el plan de membresía indicado.');

            $this->memberships();
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    /**
     * Function: userMembership
     *
     * @param  [type] $action [description]
     * @param  [type] $user_id [description]
     * @param  [type] $membership_id [description]
     * @param  bool $ajax [description]
     *
     * @return [type] [description]
     */
    function userMembership($action = null, $user_id = null, $membership_id = null, $ajax = false)
    {
        $allowed_groups = array('sadmin', 'admin', 'fcoach', 'comercial');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            switch ($action) {              
                case 'add':
                    $this->process_user_membership_add($user_id);
                    break;

                case 'list':
                    $this->process_user_membership_list($user_id);
                    break;

                case 'cancel':
                    $this->process_user_membership_cancel($membership_id);
                    break;

                //deprecated -> to be deleted
                case 'changePaymentMethod':
                    $this->process_user_membership_method($user_id, $membership_id); 
                    break;

                case 'changePlan':
                    $this->process_user_membership_change($membership_id);
                    break;
                    
                default:
                    $this->users();
                    break;
            }
        }
    }

    /**
     * Function: process_user_membership_add
     *
     * @param  [type] $user_id [description]
     *
     * @return [type] [description]
     */
    private function process_user_membership_add($user_id)
    {
        $data['user'] = $this->box->getUser($user_id);
        $now = new DateTime('now');

        $membership = array(
                    'user_id'    => $user_id,
                    'box_id'    => $this->box->box_id,
                    'membership_id'    => '',
                    'mem_expire'    => $now->format("Y-m-d"),
                    'status'      => 'p'
                );

        // validate form input
        $this->form_validation->set_rules('membership', 'membership', 'required');

        if ($this->form_validation->run() == true)
        {
            $membership['membership_id'] = $this->input->post('membership');

            $um_id = $this->box->setUserMembership($membership);
            if($um_id)
            {
                $this->session->set_flashdata('success', 'Se ha dado de alta al usuario en el plan de membresía.');
                if($this->ion_auth->in_group(12, $user_id))
                {
                    $this->ion->add_to_group(11, $user_id);
                    $this->ion->remove_from_group(12, $user_id);
                }
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                //////// prepare data 
                 $data2 = $this->prepare_data_to_payment_form($um_id);
                 $data2['action'] = 'add'; 
                 
                 $this->load->view('backend/staff/membership_pay_form', $data2);

            }
            else
            {
                $this->session->set_flashdata('error', 'No se pudo dar de alta al usuario en el plan de membresía.');
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->userMembership('list', $user_id, '', 'ajax');
            }    
        }
        else
        {
            $data = $this->prepare_data_to_addMembership_form($user_id);

            $this->show_view($data, 'staff', 'membership_user_form');

        }
    }

    /**
     * Function: process_user_membership_list
     *
     * @param  [type] $user_id [description]
     *
     * @return [type] [description]
     */
    private function process_user_membership_list($user_id)
    {
        $data['user'] = $this->box->getUser($user_id);
        $data['memberships'] = $this->box->getUserMemberships($user_id);
        
        if(!empty($data['memberships']))
        {
            foreach ($data['memberships'] as $key => $mem) 
            {
                $params = array('box_id' => $this->box->box_id,
                                    'mu_id' => $mem['id'],
                                    'user_id' => $user_id,
                                    'status' => 'active');
                $subscriptions = $this->pay->getGatewayTransactions('subscriptions', $params);

                // Set subscriptions
                if($subscriptions !== FALSE)
                {
                    $data['memberships'][$key]['subscribed'] = true;
                }

            }
        }

        $this->show_view($data, 'staff', 'memberships_user');
    }

    /**
     * Function: process_user_membership_cancel
     *
     * @param  [type] $membership_id [description]
     *
     * @return [type] [description]
     */
    private function process_user_membership_cancel($membership_id)
    {
        $membership = $this->box->getMembership($membership_id);
        if($membership)
        {
            // $this->form_validation->set_rules('option', 'option', 'required');
            // if ($this->form_validation->run() == true)
            // {
            //     if($this->input->post('option') == 'confirm')
            //        
                
            //     $this->userMembership('list', $user_id);   
            // }
            // else
            // {
            //     $data['url'] = uri_string();
            //     if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
            //     {  
            //         $this->load->view('backend/partials/delete_warning', $data);
            //     }
            //     else
            //     {
            //         $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
            //         $this->load->view('backend/staff/partials/blank', $data2);
            //         $this->load->view('backend/partials/delete_warning', $data);
            //         $this->load->view('backend/partials/footer');
            //     }
            // }  
            if($this->box->cancel_user_membership($membership_id))
                $this->session->set_flashdata('success', 'Se ha dado de baja.');
            else
                $this->session->set_flashdata('error', 'No se ha podido dar de baja.');

        }
        $this->users();
    }

    /**
     * Function: process_user_membership_change
     *
     * @param  [type] $membership_id [description]
     *
     * @return [type] [description]
     */
    private function process_user_membership_change($membership_id)
    {
        $original = $this->box->getUserMembership($membership_id);
        
        $user_id = $original->user_id;
        if($original)
        {
            $membership = array(
                'user_id'    => $user_id,
                'box_id'    => $this->box->box_id,
                'membership_id'    => '',
                'payment_method'    => '',
                'status'      => 'p'
            );

            // validate form input
            $this->form_validation->set_rules('membership_new', 'membership_new', 'required');

            if ($this->form_validation->run() == true)
            {
                if($transaction_data = $this->box->changePlan($original, $this->input->post('membership_new')))
                {
                    $this->pay->registerTransaction($transaction_data); //pendiente -> añaridr argumento para transaccion

                    $this->session->set_flashdata('success', 'Se ha cambiado a nuevo plan de membresía.');
                }
                else
                    $this->session->set_flashdata('error', 'No se ha podido cambiaa a nuevo plan de membresía.');

                $this->userMembership('list', $user_id);
            }
            else
            {
                $data['page_title'] = "Suscribir/Alta en un plan";

                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

                if (!empty(form_error('membership'))) $class = "error"; else $class = "valid";
                $data['membership'] = array(
                    'name'  => 'membership',
                    'id'    => 'membership',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => ($this->input->post('membership'))? $this->form_validation->set_value('membership', $this->input->post('membership')): '',
                );

                $data['membership_id'] = $membership_id;

                $params = "period!='D'";
                $memberships = $this->box->getUserAvailableMemberships($user_id, $params);

                $data['membership_list'] = array('' =>'-- Seleccione --');
                foreach ($memberships as $mem) 
                {
                    $data['membership_list'][$mem['id']] = $mem['title'];
                }

                $this->show_view($data, 'staff', 'membership_user_change_form');
            }      
        }
        else
        {
            $this->userMembership('list', $user_id);
        }
    }

    private function process_user_membership_method($user_id, $membership_id)
    {
        $user_membership = $this->box->getUserMembership($membership_id);
        if($user_membership)
        {
             // validate form input
            $this->form_validation->set_rules('payment_method', 'payment_method', 'required');
            if ($this->form_validation->run() == true)
            {
                $method = $this->input->post('payment_method');
                $pay_method = $this->pay->getPaymentMethod($method);
                if($pay_method->default == 1 AND $pay_method->name == 'iban' AND $data['user']->IBAN == null)
                {
                     $this->session->set_flashdata('error', 'Error: El usuario no tiene cuenta bancaria registrada. Antes de volver a intentar cambiar el método de pago, registre una cuenta bancaria del usuario.');
                }
                else
                {
                    if($this->pay->changePaymentMethod($membership_id, $method))
                        $this->session->set_flashdata('message', 'Método de pago actualizado correctamente.');
                    else
                        $this->session->set_flashdata('error', 'Error al actualizar, por favor, inténtelo de nuevo.');
                }
                   
                $this->userMembership('list', $user_id);
                
            }
            else
            {
                $data['page_title'] = "Cambio de plan";
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message'))); 

                $data['payment_method'] = array(
                    'name'  => 'payment_method',
                    'id'    => 'payment_method',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => ($this->input->post('payment_method'))? $this->form_validation->set_value('payment_method', $this->input->post('payment_method')): $membership_id,
                );
                $data['membership_id'] = $membership_id;

                $pay_methods = $this->pay->getPaymentMethods();
                if($data['user']->IBAN != null)
                // si usuario no tiene iban registrado y existe metodo de pabo por domiciliación, eliminar esta opcion
                {
                    foreach ($pay_methods as $key => $value) 
                    {
                        if($value['name'] == 'IBAN' AND $value['default'] == 1) unset($pay_methods[$key]);
                    }
                    
                }
                $data['pay_method_list'] = array(''=> '-- Seleccione --');
                foreach ($pay_methods as $key => $value) 
                {
                    if($value['default'] == 1)
                        $data['pay_method_list'][$key] = $this->lang->line($value['name'].'_name');
                    else
                        $data['pay_method_list'][$key] = $value['name'];
                }

                $this->show_view($data, 'staff', 'membership_pay_method_form');
            }
            
        }
        else
        {
            $this->userMembership('list', $user_id);
        }
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

    function prepare_data_to_addMembership_form($user_id)
    {
        $data['page_title'] = "Suscribir/Alta en un plan";

        $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

        if (!empty(form_error('membership'))) $class = "error"; else $class = "valid";
        $data['membership'] = array(
            'name'  => 'membership',
            'id'    => 'membership',
            'class' => 'form-control '.$class,
            'type'  => 'text',
            'required' => '',
            'value' => ($this->input->post('membership'))? $this->form_validation->set_value('membership', $this->input->post('membership')): '',
        );

        if (!empty(form_error('payment_method'))) $class = "error"; else $class = "valid";
        $data['payment_method'] = array(
            'name'  => 'payment_method',
            'id'    => 'payment_method',
            'class' => 'form-control '.$class,
            'type'  => 'text',
            'required' => '',
            'value' => ($this->input->post('payment_method'))? $this->form_validation->set_value('payment_method', $this->input->post('payment_method')): '',
        );
        $data['user'] = $this->box->getUser($user_id);

        $data['memberships'] = $this->box->getUserMemberships($user_id);


        $memberships = $this->box->getUserAvailableMemberships($user_id);
        $data['membership_list'] = array('' =>'-- Seleccione --');
        if(sizeof($memberships) > 1)
        {
            foreach ($memberships as $mem) 
            {
                $data['membership_list'][$mem['id']] = $mem['title'];
            }  
        }
        

        $pay_methods = $this->pay->getPaymentMethods(array('active' => '1'));
        if(!$data['user']->IBAN)
        // si usuario no tiene iban registrado y existe metodo de pabo por domiciliación, eliminar esta opcion
        {
            foreach ($pay_methods as $key => $value) 
            {
                if($value['name'] == 'IBAN') unset($pay_methods[$key]);
            }
            
        }
        $data['pay_method_list'] = array(''=> '-- Seleccione --');
        if(!empty($pay_methods))
        {
            foreach ($pay_methods as $key => $value) 
            {
                $data['pay_method_list'][$key] = $this->lang->line($value['name'].'_gateway');
            }
        }
        return $data;
    }

    function prepare_data_to_payment_form($id, $type = null)
    {
        $data['page_title'] = "Registrar pago manual";
        $payment = (object) [
            'box_id' => $this->box->box_id, 
            'mu_id' => '',
            'from_membership_id' => '', 
            'to_membership_id' => '', 
            'user_id' => '',
            'rate_amount' => '',
            'coupon_id' => 0,
            'discount' => '',
            'tax' => '',
            'total' => '',
            'date' => '',
            'pp' => '',
            'status' => '',
            'staff_id' => $this->session->userdata('user_id'),
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

        if($data['membership']->status == 'y' || $data['membership']->status == 'g') $payment->from = $this->booking_lib->calculateFrom($data['membership']->mem_expire, 1);
        else $payment->from = $this->booking_lib->calculateFrom();
       
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

        $coupons = $this->booking->getAvailableCoupons($id);
        //print("<pre>".print_r($coupons,true)."</pre>");
        $data['coupons_list'] = array('0' =>'-- Seleccione --');
        foreach ($coupons as $cup) 
        {
            $type = ($cup->type == 'abs')? "€" : "%"; 
            $data['coupons_list'][$cup->id] = $cup->title." (".$cup->value." ".$type.")";
        }
        if (!empty(form_error('coupon'))) $class = "error"; else $class = "valid";
        $data['coupon_status'] = $payment->coupon_id;
        $data['coupon'] = array(
            'name'  => 'coupon',
            'id'    => 'coupon',
            'class' => 'form-control '.$class,
            'required' => 'required',
            'value' => $payment->coupon_id,
        );

        $params = array('box_id' => $this->box->box_id, 
                        'methods' => array('card', 'cash'), 
                        'type' => 'offline');
        $pay_methods = $this->pay->getPaymentMethods($params);

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

    /**
     * Function: membershipPayment
     *
     * @param  [type] $action [description]
     * @param  [type] $id [description]
     *
     * @return [type] [description]
     */
    function membershipPayment($action = null, $id = null)
    {
        $allowed_groups = array('sadmin', 'admin', 'fcoach', 'comercial');
        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $payment = array(
                    'gateway' => '',
                    'box_id' => $this->box->box_id, 
                    'user_id' => '',
                    'mu_id' => '',
                    'staff_id' => $this->session->userdata('user_id'),
                    'demo' => '',
                    'txn_id' => '',
                    'amount' => '', 
                    'charge_date' => '',
                    'refounded' => 0,
                    'retry' => 0,
                    'status' => '');

            $transaction_data = array(  'type' => '',
                                        'from_membership_id' => '', 
                                        'to_membership_id' => '',
                                        'from' => '',
                                        'to' => '',
                                        'coupon_id' => '',
                                        'notes' => 'Manual staff payment');

            if($action == 'add')
            {
                $data['action'] = $action;
                $data['page_title'] = "Registrar pago manual";
                $transaction_data['type'] = 'new';
                

            }
            else if($action == 'renew')
            {
                $data['action'] = $action;
                $data['page_title'] = "Registrar pago manual";
                $transaction_data['type'] = 'renew';

            }

            // validate form input
            $this->form_validation->set_rules('from', 'from', 'required');
            $this->form_validation->set_rules('to', 'to', 'required');
            $this->form_validation->set_rules('times', 'times', 'required');
            $this->form_validation->set_rules('pp', 'pp', 'required');
            $this->form_validation->set_rules('user_id', 'user_id', 'required');
            $this->form_validation->set_rules('rate_amount', 'rate_amount', 'required');

            if ($this->form_validation->run() == true)
            {
                $now = new DateTime('now');

                $discount = 0;
                $total = $this->input->post('rate_amount');
                if($this->input->post('coupon') != 0)
                {
                    $coupon = $this->booking->getCoupon($this->input->post('coupon')); 

                    if($coupon->type == 'abs')
                    {
                        $discount = $coupon->value;
                    }
                    else
                    {
                        $discount = $total * $coupon->value / 100;
                    }
                }

                $gateway = $this->pay->getPaymentMethod($this->input->post('pp'));

                $payment['gateway'] = $this->input->post('pp');
                $payment['user_id'] = $this->input->post('user_id');
                $payment['mu_id'] = $this->input->post('memberships_user_id');
                $payment['amount'] = ($total + $discount)*100;
                $payment['charge_date'] = $now->format("Y-m-d");
                $payment['txn_id'] = 'FBX'.date("U").'-'.$payment['box_id'].'-'.$payment['mu_id'];
                $payment['status'] = 'confirmed';
                $payment['demo'] = $gateway->demo;

                $transaction_data['from_membership_id'] = $this->input->post('membership_id');
                $transaction_data['to_membership_id'] = $this->input->post('membership_id');
                $transaction_data['notes'] = $transaction_data['notes'].' '.$this->input->post('notes');
                $transaction_data['to'] = $this->input->post('to');
                $transaction_data['from'] = $this->input->post('from');
                $transaction_data['coupon_id'] = $this->input->post('coupon');

                if($action == 'add' OR $action == 'renew')
                {
                    $result = $this->pay->registerPayment($payment, $transaction_data);

                    if( $result != false)
                    {
                        if($this->box->edit_user_membership($payment['mu_id'], array('status' => 'y', 'mem_expire' => $transaction_data['to'])))
                        {
                            $this->session->set_flashdata('success', 'El Pago/renovación registrado correctamente y la membresía renovada.');
                        }
                        else
                        {
                            $this->session->set_flashdata('error', 'No se ha podido renovar la membresía, a pesar de haber recibido el pago correctamente. Contante con los administradores del box.'); 
                        }
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Error al registrar el pago');
                    }

                }
                elseif($action == 'edit')
                {
                    if($this->box->editManualPayment($id, $payment))
                        $this->session->set_flashdata('success', 'Pago/renovación editado correctamente');
                    else
                        $this->session->set_flashdata('error', 'Error al editar el pago');
                }

                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->userMembership('list', $this->input->post('user_id'), null, true);
            }
            else
            {
                $data = $this->prepare_data_to_payment_form($id, 'manual');
                $data['action'] = $action;  

                $this->show_view($data, 'staff', 'membership_pay_form');
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function transactions($action = null, $year = null, $month = null, $user = null, $trans_id = null)
    {
        $allowed_groups = array('sadmin', 'admin', 'fcoach', 'comercial');
        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $data['action'] = $action;

            //get users
            //get date (year) of first transaction
            
            if($action == 'list')
            {

                $data['users_list'] = $this->box->getUsers(array(11));
                $data['users'] = array(
                    'name'  => 'user',
                    'id'    => 'user',
                    'class' => 'form-control '.$class,
                    'required' => '',
                    'value' => $user,
                );

                $result = $this->box->genericGet('created_on', array('id' => $this->box->box_id), 'boxes', 'created_on','DESC', false);
                
                if($result !== FALSE) 
                    $first_year = explode('-', $result[0]->created_on)[0];
                else 
                    $first_year = date('Y');
                
                $last_year = date('Y');
                $data['years_list'] = array();
                while ($last_year >= $first_year)
                {
                    $data['years_list'][$last_year] = $last_year;
                    $last_year--;
                }
                $data['years'] = array(
                    'name'  => 'year',
                    'id'    => 'year',
                    'class' => 'form-control '.$class,
                    'required' => '',
                    'value' => $year,
                );

                $data['months_list'] = array(
                    'all' => 'Todos', 
                    '1' => 'Ene', 
                    '2' => 'Feb', 
                    '3' => 'Mar', 
                    '4' => 'Abr', 
                    '5' => 'May', 
                    '6' => 'Jun', 
                    '7' => 'Jul', 
                    '8' => 'Ago', 
                    '9' => 'Sep', 
                    '10' => 'Oct', 
                    '11' => 'Nov', 
                    '12' => 'Dic',);

                $data['months'] = array(
                    'name'  => 'month',
                    'id'    => 'month',
                    'class' => 'form-control '.$class,
                    'required' => '',
                    'value' => $month,
                );

                $data['transactions'] = $this->pay->getTransactions($year, $month, $user);

                $data['year'] = $year;
                $data['month'] = $month;
                $data['user'] = $user;

                $this->show_view($data, 'staff', 'transactions');
            }
            else if($action == 'retry')
            {
                $params = array(
                    'status'  => '0'
                );

               if($this->box->updateTransaction($trans_id, $params))
               {
                    $this->session->set_flashdata('success', 'Transacción actualizada.');
               } 
               else
                {
                    $this->session->set_flashdata('error', 'No se pudo actualizar la transacción.');
                }
               $this->transactions('list', $year, $month, $user );
            }
            else if($action == 'confirm')
            {
                $params = array(
                    'status'  => '1'
                );

                if($this->box->updateTransaction($trans_id, $params))
               {
                    $this->session->set_flashdata('success', 'Transacción actualizada correctamente.');
               } 
               else
                {
                    $this->session->set_flashdata('error', 'Error: No se pudo actualizar la transacción.');
                }
                $this->transactions('list', $year, $month, $user);
            }
            else if($action == 'email')
            {
                
                $trans_data = $this->box->getTransaction($trans_id);
                $user_data = $this->box->getUser($trans_data->user_id);
                $box_data = $this->box->getBox($trans_data->box_id);

                if ($user_data->first_name != null)  
                    $name = $user_data->first_name;
                else 
                    $name = ($user_data->username != null) ? $user_data->username : $user_data->email;

                $data2 = array(
                            'box_name' =>  $box_data->name,
                            'user' => $name,
                            'grace_period' => $this->booking->getSettingItem('membership', 'grace_period')
                        );


                $this->config->load('communications_system', TRUE);
                $email_config = $this->config->item('email_default','communications_system');
                $this->load->library('email', $email_config['settings']);

                $this->email->set_newline("\r\n");
                $this->email->from("info@fitbox.es", "FitBox");
                $this->email->to($user_data->email);
                $this->email->subject(
                    $data2['box_name'].": Aviso de domiciliación bancaria no procesada."
                );
                $this->email->message(
                    $this->load->view('/emails/transactions/transaction_IBAN_notReceived.tpl.php', $data2, TRUE)
                );
                if($email_config['IBAN_attempt_failed'] === TRUE)
                {
                    if ($this->email->send() === TRUE) 
                    {
                        $params = array(
                            'email_not_received_sent'  => '1'
                        );
                        if($this->box->updateTransaction($trans_id, $params))
                        {
                            $this->session->set_flashdata('success', 'Email enviado correctamente.');
                        } 
                        else
                        {
                            $this->session->set_flashdata('error', 'Error: No se pudo actualizar la transacción.');
                        }
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Email no se ha podido enviar.');
                    }
                }
                
                $this->transactions('list', $year, $month, $user );
            }
            else if($action == 'revertPayment')
            {
                $transactions = $this->box->getTransactions($year, $month, $user);
                $trans = $this->box->getTransaction($trans_id);
                //print("<pre>".print_r($transactions,true)."</pre>");
                //print("<pre>".print_r($trans,true)."</pre>");
               if($this->booking_lib->isPaymentReversible($transactions, $trans))
                {
                    $this->db->trans_start();
                       if($this->box->edit_user_membership($trans->mu_id, array('mem_expire' => $from, 'membership_id' => $trans->from_membership_id)))
                       {
                            $from = date("Y-m-d", strtotime($trans->from . "-1 days"));

                            if($this->box->deletePayment($trans))
                                $this->session->set_flashdata('success', 'Transacción eliminada correctamente.');
                            else
                                $this->session->set_flashdata('error', 'Transacción revertida pero no eliminada.');
                       }
                       else
                       {
                            $this->session->set_flashdata('error', 'No revertir la transacción.');
                       } 
                   $this->db->trans_complete();
                }
                else
                {
                    $this->session->set_flashdata('info', 'La transacción no es reversible.');
                }
                $this->transactions('list', $year, $month, $user );
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }


////////////////////////////////////////////////////////////////
//  SERVICES
/////////////////////////////////////////////////////////////////

    function calendar()
    {
        $allowed_groups = array('sadmin', 'admin', 'comercial', 'fcoach');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $this->show_view($data, 'staff', 'calendar');
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function getBookingSchedule($date)
    {
        $allowed_groups = array('sadmin', 'admin', 'comercial', 'fcoach');
        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $dayOfWeek = date("w", strtotime($date));
            $schedule = $this->booking->getBoxSchedule2($this->box->box_id, $dayOfWeek);
            $cont = 0;

            foreach ($schedule as $sch) {
                $dateTimeToCheck = $date." ".$sch['startH'].":".$sch['startM'].":00";
                $schedule[$cont]['reservations'] = $this->booking->getWebBookings($dateTimeToCheck, $this->box->box_id, $sch['id']);
                if(!$services[$sch['id']])
                {
                    $services[$sch['id']] = $this->booking->getService($sch['id']);
                } 
                $schedule[$cont]['color'] =  $services[$sch['id']]->color_bg; 
                $schedule[$cont]['name'] =  $services[$sch['id']]->name; 
                $schedule[$cont]['space'] =  $services[$sch['id']]->spaces_available; 
                $cont ++;
            }
        }

        return $schedule;
    }

    function bookings($date = null, $time = null, $service_id = null, $user_id = null)
    {
         $allowed_groups = array('sadmin', 'admin', 'comercial', 'fcoach');

        if($action == null) $action = 'show';

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            if($date == null) $date = date("Y-m-d");
            $data['date'] = $date;

            $data['schedule'] = $this->getBookingSchedule($date);   

            if($service_id != null AND $time != null)  
            {
                if(!is_array($time)) $time = str_split($time, 2);
                $dateTimeToCheck = $date." ".$time[0].":".$time[1].":00";
                $data['time'] = $time[0].$time[1];
                $data['id'] = $service_id;

                $data['athletes'] = $this->booking->getWebBookingsList($dateTimeToCheck, $this->box->box_id, $service_id);
                $data['reserved'] = $this->booking->isReservedByUser($dateTimeToCheck, $this->box->box_id, $service_id);
                $data['options'] = $this->booking_lib->getElegibleClients($date, $time, $this->box->box_id, $service_id);
                $data['guests'] = $this->box->getUsers($groups = 12, $show_list = FALSE);
            }       

            $this->show_view($data, 'staff', 'bookings');
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function deleteBooking($date, $time, $service_id, $user_id)
    {
        $allowed_groups = array('sadmin', 'admin', 'comercial', 'fcoach');
        if ($this->ion_auth->check_login($allowed_groups) )
        {

            $data['date'] = $date;
            $data['time'] = $time;
            $data['id'] = $service_id;

            $time = str_split($time, 2);
            $dateTimeToCheck = $date." ".$time[0].":".$time[1].":00";

            if ($this->booking->isReservedByUser($dateTimeToCheck, $this->box->box_id, $service_id, $user_id))
            {
                if($this->booking->delWebBooking($dateTimeToCheck, $this->box->box_id, $service_id, $user_id))
                {
                    $this->session->set_flashdata('success', 'Reserva eliminada.');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Error al intentar borrar la reserva.');
                }
            }
            else
            {
                $this->session->set_flashdata('error', 'No se ha borrado la reserva porque no constan reservas del usuario indicado.');
            }
        }

        if($this->input->post('ajax') OR $this->input->is_ajax_request())
        { 
            $this->bookings($date, $time, $service_id);
        }
    }

    function addBooking($date, $time, $service_id, $user_id = null, $qtty = 1)
    {
        $allowed_groups = array('sadmin', 'admin', 'comercial', 'fcoach');
        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $data['date'] = $date;
            $data['time'] = $time;
            $data['id'] = $service_id;
            if($user_id == null) $user_id = $this->input->post('clientes');

            $time = str_split($time, 2);
            $dateTimeToCheck = $date." ".$time[0].":".$time[1].":00";

            $spots = $this->booking_lib->getAvailableSpots($dateTimeToCheck, $this->box->box_id, $service_id);

            $err_msg = $this->booking_lib->addBooking($dateTimeToCheck, $this->box->box_id, $service_id, $user_id, $qtty);
            
            if($err_msg === TRUE)
            {
                if($spots > 0)
                {
                    $this->session->set_flashdata('success', 'Reserva añadida.');
                }
                else
                {
                    $this->session->set_flashdata('info', 'Reserva añadida, sobrepasando el límite de aforo.');
                }
            }
            else
            {
                foreach ($err_msg as $err => $er) 
                {
                    foreach ($er as $e) 
                    {
                        $this->session->set_flashdata($err, $e);
                    }
                }
            }
        }

        if($this->input->post('ajax') OR $this->input->is_ajax_request())
        { 
            $this->bookings($date, $time, $service_id);
        }
    }

    /* Function: addGuestBooking 
        sets the trial reservation/s regardless any condition.            
    */
    function addGuestBooking($date, $time, $service_id, $user_id = null, $qtty = 1)
    {
        $data['date'] = $date;
        $data['time'] = $time;
        $data['id'] = $service_id;
        if($user_id == null) $user_id = $this->input->post('invitados');
        $qtty = $this->input->post('qtty');

        $time = str_split($time, 2);
        $dateTimeToCheck = $date." ".$time[0].":".$time[1].":00";

        $spots = $this->booking_lib->getAvailableSpots($dateTimeToCheck, $this->box->box_id, $service_id);

        $result = $this->booking->setGuestBooking($dateTimeToCheck, $this->box->box_id, $service_id, $user_id, null, $qtty);
        
        if($result === TRUE)
        {
            if($spots - $qtty >= 0)
            {
                $this->session->set_flashdata('success', 'Reserva añadida.');
            }
            else
            {
                $this->session->set_flashdata('info', 'Reserva añadida, sobrepasando el límite de aforo.');
            }
        }
        else
        {
            $this->session->set_flashdata('error', $result);
        }

        if($this->input->post('ajax') OR $this->input->is_ajax_request())
        { 
            $this->bookings($date, $time, $service_id);
        }
    }

    /* Function: addGuestAndBooking 
        creates a new guest user and sets the trial reservation/s regardless any condition.            
    */
    function addGuestAndBooking($date, $time, $service_id)
    {
        $allowed_groups = array('sadmin', 'admin', 'comercial', 'fcoach');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $data['page_title'] = "Crear invitado";

            $tables = $this->config->item('tables','ion_auth');
            $identity_column = $this->config->item('identity','ion_auth');

            // validate form input
            $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|alpha');
            $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required|alpha');
            $this->form_validation->set_rules('gender', 'Sexo', 'required');
            $this->form_validation->set_rules('phone', 'Telefono', 'required|regex_match[/^[0-9]{9}$/]'); //{9} for 9 
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
                }
                
            }

            if ($this->form_validation->run() == true)
            {
                $guest_data = array(
                    'box_id'    => $this->box->box_id,
                    'username'  => strtolower($this->input->post('username')),
                    'first_name' => strtolower($this->input->post('first_name')),
                    'last_name'  => strtolower($this->input->post('last_name')),
                    'email'    => $this->input->post('email'),
                    'gender'      => $this->input->post('gender'),
                    'active'      => 1,
                    'phone'      => $this->input->post('phone')
                );

                $qtty = $this->input->post('qtty');

                $email    = strtolower($this->input->post('email'));
                $identity = ($identity_column === 'email') ? $email : $this->input->post('identity');
                $password = $this->generate_password(8);
                $id = null;
                $error = 0;
                $this->db->trans_start();
                    if($this->ion_auth->register($identity, $password, $email, $guest_data, array(12)))
                    {
                        $id = $this->box->create_user($email);
                        if($id)
                        {
                            $this->session->set_flashdata('success', 'Nuevo usuario creado.');
                        }
                        else
                        { 
                            $error = 1;
                            $this->session->set_flashdata('error', 'No se pudo crear el nuevo usuario.');
                        }
                    }
                    else
                    {
                        $error = 1;
                        $this->session->set_flashdata('error', 'No se pudo registrar el nuevo usuario.');
                    }
                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE OR $error == 1)
                {
                    if($this->input->post('ajax') OR $this->input->is_ajax_request())
                    { 
                        $this->bookings($date, $time, $service_id);
                    }
                }
                else
                {
                    $time = str_split($time, 2);
                    $dateTime = $date." ".$time[0].":".$time[1].":00";

                    $spots = $this->booking_lib->getAvailableSpots($dateTime, $box_id, $service_id);
                    $result = $this->booking->setWebBooking($dateTime, $box_id, $service_id, $id, null, $qtty);

                    if($result === TRUE)
                    {
                        if($spots - $qtty >= 0)
                        {
                            $this->session->set_flashdata('success', 'Reserva añadida.');
                        }
                        else
                        {
                            $this->session->set_flashdata('info', 'Reserva añadida, sobrepasando el límite de aforo.');
                        }
                    }
                    else
                    {
                        $this->session->set_flashdata('error', $result);
                    }

                    $this->bookings($date, $time, $service_id);
                }   
            }
            else
            {
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

                $data['user_id'] = $user_id;

                if (!empty(form_error('first_name'))) $class = "error"; else $class = "valid";
                $data['first_name'] = array(
                    'name'  => 'first_name',
                    'id'    => 'first_name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('first_name', $user->first_name),
                );
                if (!empty(form_error('last_name'))) $class = "error"; else $class = "valid";
                $data['last_name'] = array(
                    'name'  => 'last_name',
                    'id'    => 'last_name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('last_name', $user->last_name),
                );

                if (!empty(form_error('email'))) $class = "error"; else $class = "valid";
                $data['email'] = array(
                    'name'  => 'email',
                    'id'    => 'email',
                    'class' => 'form-control '.$class,
                    'type'  => 'email',
                    'required' => '',
                    'value' => $this->form_validation->set_value('email', $user->email),
                );

                if (!empty(form_error('gender'))) $class = "error"; else $class = "valid";
                $data['genders'] = array('' =>'-- Seleccione --', 'M' =>'Masculino', 'F'=> 'Femenino');
                $data['gender'] = array(
                    'name'  => 'gender',
                    'id'    => 'gender',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('gender', $user->gender), 
                );

                if (!empty(form_error('phone'))) $class = "error"; else $class = "valid";
                $data['phone'] = array(
                    'name'  => 'phone',
                    'id'    => 'phone',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('phone', $user->phone), 
                );

                if (!empty(form_error('qtty'))) $class = "error"; else $class = "valid";
                $data['qttys'] = array('1' =>'1', '2' =>'2', '3' =>'3', '4' =>'4', '5' =>'5');
                $data['qtty'] = array(
                    'name'  => 'qtty',
                    'id'    => 'qtty',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('qtty', $user->gender), 
                );

                $data['date'] = $date;
                $data['time'] = $time;
                $data['service_id'] = $service_id;

                $this->show_view($data, 'staff', 'guest_reservation_form');
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }
    ///////////////////////
    // Section: Gateways //
    ///////////////////////
    
    /**
     * Function: gateways
     *
     * Paramaters:
     * $action [description]
     * $id [description]
     *
     * Returns:
     * view
     */
    function gateways($action = null, $id = null)
    {
        $allowed_groups = array('sadmin', 'admin', 'comercial', 'fcoach');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            switch ($action) {
                case 'edit':
                    if($id != null)
                    {
                        $gateway = $this->pay->getGateway(array('box_id' => $this->box->box_id, 'id' => $id));
                        if($gateway !== false)
                        {
                            $this->edit_gateway($gateway);
                            break;
                        }                            
                    }

                    $this->show_gateways();
                    break;
                
                default:
                    $this->show_gateways();
                    break;
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }

    }

    /**
     * Function: show_gateways
     *
     * @return [type] [description]
     */
    private function show_gateways()
    {
        $params = array('box_id' => $this->box->box_id);
        $data['gateways'] = $this->pay->getGateways($params);
        $data['show_gateway'] = array();

        //the gateways wanted to show connect panel
        $gateways = array('gocardless');
        foreach ($gateways as $key => $value) {
            $gateway = $this->pay->getGateway(array('box_id' => $this->box->box_id, 'name' => $value));
            $oauth = $this->pay->getOauthOrg(array('box_id' => $this->box->box_id, 'gateway' => $gateway->id, 'demo' => $gateway->demo));
            if($oauth !== FALSE)
            {
                $data['show_gateways'][$value] = $oauth->status;
            } 
        }
        if(!empty($data['gateways']))
        {
            foreach ($data['gateways'] as $gateway) 
            {
                if($gateway->demo == 1 AND  $gateway->active == 1)
                {
                    $this->session->set_flashdata('info', 'Alguna de sus pasarelas activas está en modo "DEMO". Tenga en cuenta que esto implica que los cobros realizados a través de esta/s pasarela/s serán ficticios.');
                    break;
                }
            
            }  
        }
        

        $this->show_view($data, 'staff', 'gateways');

    }

    /**
     * Function: edit_gateway
     *
     * @param  [type] $gateway [description]
     *
     * @return [type] [description]
     */
    private function edit_gateway($gateway)
    {
        $data['page_title'] = "Configuración de pasarela";
    }

///////////////////////
// Section: Services //
///////////////////////
    
    /**
     * Function: services
     *
     * @param  bool $ajax [description]
     *
     * @return [type] [description]
     */
    function services($action = null, $id = null)
    {
        $allowed_groups = array('sadmin', 'admin', 'comercial', 'fcoach');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            switch ($action) {
                case 'edit':
                    if(!is_null($id))
                    {
                        $service = $this->booking->getService($id);
                        if($service !== false)
                        {
                            $this->edit_service($service);
                            break;
                        }
                    }
                    
                    $this->show_services();
                    break;

                case 'add':
                    $this->create_service();
                    break;

                case 'delete':
                    if(!is_null($id))
                    {
                        $service = $this->booking->getService($id, 'id');
                        if($service !== false)
                        {
                            $this->delete_service($service);
                            break;
                        }
                    }
                    
                    $this->show_services();
                    break;
                
                default:
                    $this->show_services();
                    break;
            }
            
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    /**
     * Function: show_services
     *
     * @return [type] [description]
     */
    private function show_services()
    {
        $data['services'] = $this->booking->getServices();

        $this->show_view($data, 'staff', 'services');
    }

    /**
     * Function: edit_service
     *
     * @param  [type] $service [description]
     *
     * @return [type] [description]
     */
    private function edit_service($service)
    {   
        $this->service_form_set_validation();

        if ($this->form_validation->run() == true)
        {
            list($schedule_data, $service) = $this->service_form_get_posted_data($service);
            $changes = $this->input->post('changes');

            if($this->booking->editService($service->id, $service, $schedule_data, $changes))
                $this->session->set_flashdata('success', 'Servicio editado.');
            else
                $this->session->set_flashdata('error', 'No se pudo editar el servicio.');

            $this->show_services();
        }
        else
        {
            $data = $this->service_form_create($service);

            $schedule = $this->booking->getSchedule($service->id);
            $data['week'] = $this->booking_lib->formatSchedule($schedule);
            $data['page_title'] = "Editar servicio";
            $data['action'] = 'edit';

            $this->show_view($data, 'staff', 'service_form');
        }
    }

    /**
     * Function: create_service
     *
     * @return [type] [description]
     */
    private function create_service()
    {
        $service = (object) [
                'box_id'    => $this->box->box_id,
                'name'      => '',
                'spot_price'      => '1',
                'spot_invoice'      => '',
                'payment_method'      => 'both',
                'interval'      => '60',
                'allow_times'      => '0',
                'allow_times_min'      => '0',
                'startDay'      => '1',
                'time_before'      => '96',
                'spaces_available'      => '15',
                'show_spaces_left'      => '1',
                'show_event_titles'      => '1',
                'show_event_image'      => '',
                'show_multiple_spaces'      => '0',
                'show_available_seats'      => '0',
                'coupon'      =>  '0',
                'deposit'      => '100',
                'delBookings'      => '1',
                'autoconfirm'      => '1',
                'fromName'      => '',
                'fromEmail'      => '',
                'color_bg'      => '#e74c3c',
                'color_hover' => '#5A6F84',
                'active'      => '0',
                'date_created'  => date('Y-m-d')
            ];

        $this->service_form_set_validation();

        if ($this->form_validation->run() == true)
        {
            list($schedule_data, $service) = $this->service_form_get_posted_data($service);

            if($this->booking->createService($service, $schedule_data))
                $this->session->set_flashdata('success', 'Nuevo servicio creado.');
            else
                $this->session->set_flashdata('error', 'No se pudo crear el nuevo servicio.');

            $this->show_services();
        }
        else
        {
            $data = $this->service_form_create($service);

            $data['page_title'] = "Crear servicio";
            $data['action'] = 'add';

            $this->show_view($data, 'staff', 'service_form');
        }
    }   

    /**
     * Function: delete_service
     *
     * @param  [type] $id [description]
     *
     * @return [type] [description]
     */
    private function delete_service($service_id)
    {
        if($this->booking->getService($service_id))
        {
            if($this->booking->deleteService($service_id))
                $this->session->set_flashdata('success', 'Servicio eliminado.');
            else
                $this->session->set_flashdata('error', 'No se pudo eliminar el servicio. Comprueba que no existen cupones o membresias vinculadas a este servicio. Si el servicio ya ha sido reservado con anterioridad no podrá borrarse, solo desactivarse.');    
        }
        else
            $this->session->set_flashdata('info', 'No existe el servicio indicado.');   

        $this->show_services();
    }

    /**
     * Function: service_form_create
     *
     * @param  [type] $service [description]
     *
     * @return [type] [description]
     */
    private function service_form_create($service)
    {
        // display the create user form
        // set the flash data error message if there is one
        $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        $data['service_id'] = $service->id;

        $data['box_id'] = array(
        'box_id' => 'box_id',
        'id' => 'box_id',
        'class' => 'form-control',
        'type' => 'text',
        'required' => '',
        'value' => $this->box->box_id,
        );

        if (!empty(form_error('name'))) $class = "error"; else $class = "valid";
        $data['name'] = array(
        'name' => 'name',
        'id' => 'name',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('name'))? $this->input->post('name'): $service->name,
        );

        if (!empty(form_error('spot_price'))) $class = "error"; else $class = "valid";
        $data['spot_price'] = array(
        'name' => 'spot_price',
        'id' => 'spot_price',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => $service->spot_price,
        );

        if (!empty(form_error('spot_invoice'))) $class = "error"; else $class = "valid";
        $data['spot_invoice'] = array(
        'name' => 'spot_invoice',
        'id' => 'spot_invoice',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => $service->spot_invoice,
        );

        if (!empty(form_error('color_bg'))) $class = "error"; else $class = "valid";
        $data['color_list'] = $this->booking_lib->getListOfColours();
        $data['color_bg'] = array(
        'name' => 'color_bg',
        'id' => 'color_bg',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('color_bg'))? $this->form_validation->set_value('color_bg', $this->input->post('color_bg')): $service->color_bg,
        );

        if (!empty(form_error('payment_method'))) $class = "error"; else $class = "valid";
        $data['payment_list'] = array('' =>'-- Seleccione --', 'paypal' =>'Online', 'invoice'=> 'Offline', 'both'=> 'Online y Offline');
        $data['payment_method'] = array(
        'name' => 'payment_method',
        'id' => 'payment_method',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('payment_method'))? $this->form_validation->set_value('payment_method', $this->input->post('payment_method')): $service->payment_method,
        );              

        if (!empty(form_error('interval'))) $class = "error"; else $class = "valid";
        $data['interval_list'] = $this->booking_lib->getIntervalList(15,720);
        $data['interval'] = array(
        'name' => 'interval',
        'id' => 'interval',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('interval'))? $this->form_validation->set_value('interval', $this->input->post('interval')): $service->interval,
        );

        if (!empty(form_error('allow_times'))) $class = "error"; else $class = "valid";
        $data['allow_times'] = array(
        'name' => 'allow_times',
        'id' => 'allow_times',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('allow_times'))? $this->form_validation->set_value('allow_times', $this->input->post('allow_times')): $service->allow_times,
        );

        if (!empty(form_error('allow_times_min'))) $class = "error"; else $class = "valid";
        $data['allow_times_min'] = array(
        'name' => 'allow_times_min',
        'id' => 'allow_times_min',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('allow_times_min'))? $this->form_validation->set_value('allow_times_min', $this->input->post('allow_times_min')): $service->allow_times_min,
        );

        if (!empty(form_error('startDay'))) $class = "error"; else $class = "valid";
        $data['startDay'] = array(
        'name' => 'startDay',
        'id' => 'startDay',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('startDay'))? $this->form_validation->set_value('startDay', $this->input->post('startDay')): $service->startDay,
        );

        if (!empty(form_error('time_before'))) $class = "error"; else $class = "valid";
        $data['time_before'] = array(
        'name' => 'time_before',
        'id' => 'time_before',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('time_before'))? $this->form_validation->set_value('time_before', $this->input->post('time_before')): $service->time_before,
        );

        if (!empty(form_error('spaces_available'))) $class = "error"; else $class = "valid";
        $data['spaces_available'] = array(
        'name' => 'spaces_available',
        'id' => 'spaces_available',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('spaces_available'))? $this->form_validation->set_value('spaces_available', $this->input->post('spaces_available')): $service->spaces_available,
        );

        if (!empty(form_error('show_event_titles'))) $class = "error"; else $class = "valid";
        $data['show_event_titles'] = array(
        'name' => 'show_event_titles',
        'id' => 'show_event_titles',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('show_event_titles'))? $this->form_validation->set_value('show_event_titles', $this->input->post('show_event_titles')): $service->show_event_titles,
        );

        if (!empty(form_error('show_event_image'))) $class = "error"; else $class = "valid";
        $data['show_event_image'] = array(
        'name' => 'show_event_image',
        'id' => 'show_event_image',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('show_event_image'))? $this->form_validation->set_value('show_event_image', $this->input->post('show_event_image')): $service->show_event_image,
        );

        if (!empty(form_error('show_multiple_spaces'))) $class = "error"; else $class = "valid";
        $data['show_multiple_spaces'] = array(
        'name' => 'show_multiple_spaces',
        'id' => 'show_multiple_spaces',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('show_multiple_spaces'))? $this->form_validation->set_value('show_multiple_spaces', $this->input->post('show_multiple_spaces')): $service->show_multiple_spaces,
        );

        if (!empty(form_error('deposit'))) $class = "error"; else $class = "valid";
        $data['deposit'] = array(
        'name' => 'deposit',
        'id' => 'deposit',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('deposit'))? $this->form_validation->set_value('deposit', $this->input->post('deposit')): $service->deposit,
        );

        $data['delBookings'] = ($this->input->post('delBookings'))? $this->form_validation->set_value('delBookings', $this->input->post('delBookings')): $service->delBookings;

        $data['autoconfirm'] = ($this->input->post('autoconfirm'))? $this->form_validation->set_value('autoconfirm', $this->input->post('autoconfirm')): $service->autoconfirm;

        $data['show_available_seats'] = ($this->input->post('show_available_seats'))? $this->form_validation->set_value('show_available_seats', $this->input->post('show_available_seats')): $service->show_available_seats;

        $data['show_spaces_left'] = ($this->input->post('show_spaces_left'))? $this->form_validation->set_value('show_spaces_left', $this->input->post('show_spaces_left')): $service->show_spaces_left;

        $data['coupon'] = ($this->input->post('coupon'))? $this->form_validation->set_value('coupon', $this->input->post('coupon')): $service->coupon;

        if (!empty(form_error('fromName'))) $class = "error"; else $class = "valid";
        $data['fromName'] = array(
        'name' => 'fromName',
        'id' => 'fromName',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('fromName'))? $this->form_validation->set_value('fromName', $this->input->post('fromName')): $service->fromName,
        );

        if (!empty(form_error('fromEmail'))) $class = "error"; else $class = "valid";
        $data['fromEmail'] = array(
        'name' => 'fromEmail',
        'id' => 'fromEmail',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('fromEmail'))? $this->form_validation->set_value('fromEmail', $this->input->post('fromEmail')): $service->fromEmail,
        );

        if (!empty(form_error('active'))) $class = "error"; else $class = "valid";
        $data['active_list'] = array('1' =>'Activo', '0'=> 'Inactivo');
        $data['active'] = array(
        'name' => 'active',
        'id' => 'active',
        'class' => 'form-control '.$class,
        'type' => 'text',
        'required' => '',
        'value' => ($this->input->post('active'))? $this->form_validation->set_value('active', $this->input->post('active')): $service->active,
        );

        $week = array();
        $week_populated = FALSE;
        for ($i = 1; $i < 8; $i++)
        {
            $j = 0;
            $hora = $this->input->post('week_from_h_'.$i);
            $coach = $this->input->post('coach_'.$i);

            if(sizeof($hora) > 0)
            {
                $week_populated = TRUE;
                foreach ($hora as $slot) 
                {
                    if ($slot != null) {
                        $min = $this->input->post('week_from_m_'.$i);

                        $week[$i][$j]['startHH'] = $hora[$j];
                        $week[$i][$j]['startMM'] = $min[$j];
                        $week[$i][$j]['coach'] = $coach[$j];
                    }
                    $j++; 
                }
            }
        }
        if($week_populated === TRUE) $data['week'] = $week;


        return $data;
    }

    /**
     * Function: service_form_get_posted_data
     *
     * @param  [type] $service [description]
     *
     * @return [type] [description]
     */
    private function service_form_get_posted_data($service)
    {
        $service->name = $this->input->post('name');
        $service->spot_price = $this->input->post('spot_price');
        $service->spot_invoice = $this->input->post('spot_invoice');
        $service->payment_method = $this->input->post('payment_method');
        $service->interval = $this->input->post('interval');
        $service->allow_times = $this->input->post('allow_times');
        $service->allow_times_min = $this->input->post('allow_times_min');
        $service->startDay = $this->input->post('startDay');
        $service->spaces_available = $this->input->post('spaces_available');
        $service->show_spaces_left = $this->input->post('show_spaces_left');
        $service->show_event_titles = $this->input->post('show_event_titles');
        $service->show_event_image = $this->input->post('show_event_image');
        $service->show_multiple_spaces = $this->input->post('show_multiple_spaces');
        $service->show_available_seats = $this->input->post('show_available_seats');
        $service->coupon = $this->input->post('coupon');
        $service->delBookings = $this->input->post('delBookings');
        $service->autoconfirm = $this->input->post('autoconfirm');
        $service->color_bg = $this->input->post('color_bg');
        $service->coupon = $this->input->post('coupon');
        $service->active = $this->input->post('active');

        $schedule_data = array();

        for ($i = 1; $i < 8; $i++)
        {
            $j = 0;
            $hora = $this->input->post('week_from_h_'.$i);
            $coach = $this->input->post('coach_'.$i);

            if(sizeof($hora) > 0)
            {
                foreach ($hora as $slot) 
                {
                    if ($slot != null) {
                        $min = $this->input->post('week_from_m_'.$i);
                        $time_input = $hora[$j].":".$min[$j];
                        $date = DateTime::createFromFormat( 'H:i', $time_input);
                        
                        $schedule_data[$i][$j]['box_id'] = $this->box->box_id;
                        $schedule_data[$i][$j]['idService'] = $service->id;
                        $schedule_data[$i][$j]['week_num'] = $i;
                        $schedule_data[$i][$j]['startTime'] = $date->format( 'H:i:s'); 
                        $schedule_data[$i][$j]['endTime'] = date('H:i:s', strtotime("+".(int)$this->input->post('interval')." minutes", strtotime($schedule_data[$i][$j]['startTime'])));  
                        $schedule_data[$i][$j]['coach'] = $coach[$j];
                    }
                    $j++; 
                }
            }
        }

        return array($schedule_data, $service);
    }

    /**
     * Function: service_form_set_validation
     *
     * @return [type] [description]
     */
    private function service_form_set_validation()
    {
        // validate form input
        $this->form_validation->set_rules('name', 'name', 'required');
        //$this->form_validation->set_rules('interval', 'interval', 'required');
        //$this->form_validation->set_rules('active', 'active', 'required');
    }

    /**
     * Function: getServicePartialform
     *
     * @return [type] [description]
     */
    function getServicePartialform()
    {   
        $data['week'] = $this->input->post('week');
        $this->load->view('backend/staff/service_form_aditional', $data);
    }


    function schemes($action = null, $id = null)
    {
        $allowed_groups = array('sadmin', 'admin', 'comercial', 'fcoach');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            switch ($action) {
                case 'add':
                    $this->create_scheme();
                    break;


                case 'edit':
                    if(!is_null($id))
                    {
                        $scheme = $this->booking->getScheme($id);
                        if($schedule !== false)
                        {
                            $this->edit_scheme($schedule);
                            break;
                        }
                    }
                    
                    $this->show_schedules();
                    break;
                
                case 'delete':
                    break;

                default:
                    $this->show_schemes();
                    break;
            }
            
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

//////////////////////////
// Section: Memberships //
//////////////////////////

    function new_member()
    {
        $allowed_groups = array('sadmin', 'admin', 'comercial');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            
            $data['title'] = "Create User";
            $tables = $this->config->item('tables','ion_auth');
            $identity_column = $this->config->item('identity','ion_auth');
            $data['identity_column'] = $identity_column;

            // validate form input
            $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required');
            $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required');
            if($identity_column!=='email')
            {
                $this->form_validation->set_rules('identity',$this->lang->line('create_user_validation_identity_label'),'required|is_unique['.$tables['users'].'.'.$identity_column.']');
                $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
            }
            else
            {
                $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique[' . $tables['users'] . '.email]');
            }
            $this->form_validation->set_rules('DNI', 'DNI', 'required|is_unique[' . $tables['users'] . '.DNI]');
            //$this->form_validation->set_rules('DNI', 'DNI', 'required|is_unique[' . $tables['users'] . '.DNI]|regex_match[(([X-Z]{1})([-]?)(\d{7})([-]?)([A-Z]{1}))|((\d{8})([-]?)([A-Z]{1}))]');$this->form_validation->set_rules('gender', 'Sexo', 'required');
            $this->form_validation->set_rules('day', 'Día', 'required');
            $this->form_validation->set_rules('month', 'Mes', 'required');
            $this->form_validation->set_rules('year', 'Año', 'required');
            $this->form_validation->set_rules('gender', 'Sexo', 'required');

            if ($this->form_validation->run() == true)
            {
                $email    = strtolower($this->input->post('email'));
                $identity = ($identity_column==='email') ? $email : $this->input->post('identity');
                $password = $this->generate_password(8);

                $additional_data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name'  => $this->input->post('last_name'),
                    'DNI'    => $this->input->post('DNI'),
                    'gender'      => $this->input->post('gender'),
                    'birth_date' => $this->input->post('year').$this->input->post('month').$this->input->post('day')
                );
            }
            if ($this->form_validation->run() == true && $this->ion_auth->register($identity, $password, $email, $additional_data))
            {
                if($user_id = $this->box->create_user($email))
                    $this->session->set_flashdata('success', 'Usuario creado.');
                else
                    $this->session->set_flashdata('success', 'No se pudo crear nuevo usuario.');

                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->load->view('backend/staff/renew_member/$user_id');

            }
            else
            {
                // display the create user form
                // set the flash data error message if there is one
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

                if (!empty(form_error('first_name'))) $class = "error"; else $class = "valid";
                $data['first_name'] = array(
                    'name'  => 'first_name',
                    'id'    => 'first_name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('first_name'),
                );

                if (!empty(form_error('last_name'))) $class = "error"; else $class = "valid";
                $data['last_name'] = array(
                    'name'  => 'last_name',
                    'id'    => 'last_name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('last_name'),
                );

                if (!empty(form_error('identity'))) $class = "error"; else $class = "valid";
                $data['identity'] = array(
                    'name'  => 'identity',
                    'id'    => 'identity',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('identity'),
                );

                if (!empty(form_error('email'))) $class = "error"; else $class = "valid";
                $data['email'] = array(
                    'name'  => 'email',
                    'id'    => 'email',
                    'class' => 'form-control '.$class,
                    'type'  => 'email',
                    'required' => '',
                    'value' => $this->form_validation->set_value('email'),
                );

                if (!empty(form_error('password'))) $class = "error"; else $class = "valid";
                $data['password'] = array(
                    'name'  => 'password',
                    'id'    => 'password',
                    'type'  => 'password',
                    'class' => 'form-control '.$class,
                    'disabled' => '',
                    'value' => $this->form_validation->set_value('password'),
                );

                if (!empty(form_error('DNI'))) $class = "error"; else $class = "valid";
                $data['DNI'] = array(
                    'name'  => 'DNI',
                    'id'    => 'DNI',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('DNI'),
                );

                if (!empty(form_error('gender'))) $class = "error"; else $class = "valid";
                $data['gender'] = array(
                    'name'  => 'gender',
                    'id'    => 'gender',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('gender'),
                );

                if (!empty(form_error('year'))) $class = "error"; else $class = "valid";
                $data['year'] = array(
                    'name'  => 'year',
                    'id'    => 'year',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('year'),
                );

                if (!empty(form_error('month'))) $class = "error"; else $class = "valid";
                $data['month'] = array(
                    'name'  => 'month',
                    'id'    => 'month',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('month'),
                );

                if (!empty(form_error('day'))) $class = "error"; else $class = "valid";
                $data['day'] = array(
                    'name'  => 'day',
                    'id'    => 'day',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('day'),
                );

                if (!empty(form_error('group'))) $class = "error"; else $class = "valid";
                $data['group'] = array(
                    'name'  => 'group',
                    'id'    => 'group',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('day'),
                );
                $data['genders'] = array('' =>'-- Seleccione --', 'M' =>'Masculino', 'F'=> 'Femenino');
                $data['days'] = $this->toolbox_lib->generate_list(1,31);
                $data['months'] = $this->toolbox_lib->generate_list(1,12);
                $data['years'] = $this->toolbox_lib->generate_list(date('Y')-85,date('Y'));

                $this->show_view($data, 'staff', 'create_user');
            }
        }


    } 

////////////////////////////
/// Comunicaciones
/// ///////////////////////

    function emails()
    {
        $allowed_groups = array('sadmin', 'admin', 'fcoach', 'comercial');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $data = array();

            $this->show_view($data, 'staff', 'emails');
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

////////////////////////////
/// Configuraciones
/// ///////////////////////

    function conf($module = null, $action = null)
    {
        $allowed_groups = array('sadmin', 'admin');

        if ($this->ion_auth->check_login($allowed_groups) )
        {
            $this->load->model('Settings_model', 'conf');
            $data = array();

            switch ($module) 
            {
                case 'calendar':
                    $data['page_title'] = "Configuración calendario";
                    $data['calendar'] = $this->booking->getSettings('calendar');  

                    foreach ($data['calendar'] as $key => $value) 
                    {                  
                        if($key == 'weekly'){ $key2 = 'Vista'; }
                        else if($key == 'only_this_week'){ $key2 = 'Límite';}
                        else if($key == 'past_events'){ $key2 = 'Ver actividades pasadas';}
                        else if($key == 'mark_past'){ $key2 = 'Diferenciar actividades pasadas';}
                        else if($key == 'free_spots'){ $key2 = 'Plazas';}
                        else if($key == 'max_spots'){ $key2 = 'Mostrar Aforo máx.'; }
                        else if($key == 'allow_public'){ $key2 = 'Acceso';}
                        else if($key == 'use_popup'){ $key2 = 'Pop-ups'; }
                        else if($key == 'start_day'){ $key2 = '1er día semana';  }
                        $this->form_validation->set_rules($key, $key2, 'required');
                    }

                    if ($action == 'edit' AND $this->form_validation->run() == true)
                    {
                        foreach ($data['calendar'] as $key => $value) 
                        {
                            $data['calendar'][$key] = $this->input->post($key);
                        }

                        if($this->booking->editSettings('calendar', $this->box->box_id, $data['calendar']))
                            $this->session->set_flashdata('success', 'Configuración de calendario modificada.');
                        else
                            $this->session->set_flashdata('error', 'No se pudo modificar la configuración del calendario.');

                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        $this->conf();
                    }
                    else
                    {
                        $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                        
                        foreach ($data['calendar'] as $key => $value) 
                        {
                            if($key == 'weekly'){ $data[$key.'_list'] = array('1' =>'Semanal', '0' => 'Mensual'); }
                            else if($key == 'only_this_week'){ $data[$key.'_list'] = array('1' =>'Sólo actual', '0' =>'Actual y anteriores'); }
                            else if($key == 'past_events'){ $data[$key.'_list'] = array('1' =>'Si', '0' => 'No');  }
                            else if($key == 'mark_past'){ $data[$key.'_list'] = array('1' =>'Si', '0' => 'No');  }
                            else if($key == 'free_spots'){ $data[$key.'_list'] = array('1' =>'Huecos libres', '0' => 'Plazas reservadas');  }
                            else if($key == 'max_spots'){ $data[$key.'_list'] = array('1' =>'Si', '0' => 'No');  }
                            else if($key == 'allow_public'){ $data[$key.'_list'] = array('1' =>'Público', '0' => 'Restringido');  }
                            else if($key == 'use_popup'){ $data[$key.'_list'] = array('1' =>'Si', '0' => 'No');  }
                            else if($key == 'start_day'){ $data[$key.'_list'] = array('1' =>'Lunes', '0' => 'Domingo'); }

                            $data[$key] = array(
                                                $key  => $key,
                                                'id'    => $key,
                                                'class' => 'form-control '.$class,
                                                'type'  => 'text',
                                                'required' => '',
                                                'value' => $this->form_validation->set_value($key),
                                            );
                        }

                        $this->show_view($data, 'staff', 'calendar_conf_form');
                    }
                    break;


                case 'booking':
                    $data['page_title'] = "Configuración Reservas";
                    $data['booking'] = $this->booking->getSettings('booking'); 

                break; 

                case 'membership':
                    $data['page_title'] = "Configuración Tarifas";
                    $data['membership'] = $this->booking->getSettings('membership'); 

                    foreach ($data['membership'] as $key => $value) 
                    {                  
                        if($key == 'grace_period'){ $key2 = 'Periodo de gracia'; }
                        else if($key == 'cancel_period'){ $key2 = 'Periodo de cancelación';}
                        
                        $this->form_validation->set_rules($key, $key2, 'required');
                    }

                    if ($action == 'edit' AND $this->form_validation->run() == true)
                    {
                        foreach ($data['membership'] as $key => $value) 
                        {
                            $data['membership'][$key] = $this->input->post($key);
                        }

                        if($this->booking->editSettings('membership', $this->box->box_id, $data['membership']))
                            $this->session->set_flashdata('success', 'Configuración de planes/subscripciones modificada.');
                        else
                            $this->session->set_flashdata('error', 'No se pudo modificar la configuración del planes/subscripciones.');

                        $this->session->set_flashdata('message', $this->ion_auth->messages());
                        $this->conf();
                    }
                    else
                    {
                        $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                        
                        foreach ($data['membership'] as $key => $value) 
                        {
                            if (!empty(form_error($key))) $class = "error"; else $class = "valid";
                            $data[$key] = array(
                                                $key  => $key,
                                                'id'    => $key,
                                                'class' => 'form-control '.$class,
                                                'type'  => 'text',
                                                'required' => '',
                                                'value' => $this->form_validation->set_value($key)
                                            );
                        }

                        $this->show_view($data, 'staff', 'membership_conf_form');
                    }

                break;


                case null:
                default:

                    $data['calendar'] = $this->booking->getSettings('calendar');
                    $data['booking'] = $this->booking->getSettings('booking'); 
                    $data['membership'] = $this->booking->getSettings('membership'); 
                    
                    $this->show_view($data, 'staff', 'conf');

                    break;
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

////////////////////
    // FUNCIONES
///////////////////

     function generate_password($length = 20){
      $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
                'abcdefghijklmnopqrstuvwxyz'.
                '0123456789'.
                '-=~!@#$%^&*()_+,./<>?;:[]{}\|';
      $str = '';
      $max = strlen($chars) - 1;

      for ($i=0; $i < $length; $i++)
        $str .= $chars[mt_rand(0, $max)];

      return $str;
    }

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

    public function valid_IBAN($str)
    {
        // validate the IBAN
        if ($this->iban->validate($str, $error) != true) 
        {
            $this->form_validation->set_message('valid_IBAN', $error);
            return false;
        }
        else
            return true;    
    }

    public function multiple_select()
    {
         $arr = $this->input->post('group[]');
         if(!empty($arr) AND sizeof($arr) > 0)
         {
            return TRUE;
         }
         
         $this->form_validation->set_message('multiple_select','Selecciona al menos un grupo.');
         return FALSE;
    }
    
}
