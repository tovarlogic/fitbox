<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sudo extends CI_Controller {

    function __construct() 
    {
        parent::__construct();
        $this->load->database();

        $this->load->library(array('ion_auth','form_validation','booking_lib','iban','encryption','toolbox_lib'));

        $this->load->helper(array('language'));

        $this->load->model('fitbox_model', 'fbx');
        $this->load->model('box_model', 'box');
        $this->load->model('exercise_model', 'exercise');
        $this->load->model('logs_model', 'logs');
        $this->load->model('booking_model', 'booking');
        $this->load->model('ion_auth_model', 'ion');

        $this->lang->load(array('auth','fitbox','booking'));

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->output->enable_profiler(FALSE);
        
        if ($this->ion_auth->logged_in() && $this->ion_auth->in_group('sudo'))
        {
            $this->box->set_box();
            $this->booking->set_box($this->box->box_id);
        }   
    }

    function index() 
    {
        if ($this->ion_auth->check_login('sudo'))
        {
            $data['box'] = $this->box->getBox();

            if ($this->input->post('ajax') OR $this->input->is_ajax_request()) 
            {  
                $this->load->view('backend/sudo/dashboard', $data);
            }
            else
            {
                $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                $this->load->view('backend/sudo/partials/blank', $data2);
                $this->load->view('backend/sudo/dashboard', $data);
            }
        }

    }

    function boxes($ajax = false)
    {
        if ($this->ion_auth->check_login('sudo') )
        {
            $data['page_title'] = "Boxes";
            $data['boxes'] = $this->fbx->getBoxes();

            if ($this->input->post('ajax') OR $this->input->is_ajax_request() OR $ajax == true)  
            {  
                $this->load->view('backend/sudo/boxes', $data);
            }else{
                $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                $this->load->view('backend/sudo/partials/blank', $data2);
                $this->load->view('backend/sudo/boxes', $data);
                $this->load->view('backend/partials/footer');
            }

        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function exercises($ajax = false)
    {
        if ($this->ion_auth->check_login('sudo') )
        {
            $data['page_title'] = "Ejercicios básicos";
            $data['exercises'] = $this->exercise->getBasicExercises();

            if ($this->input->post('ajax') OR $this->input->is_ajax_request() OR $ajax == true)  
            {  
                $this->load->view('backend/sudo/exercises', $data);
            }else{
                $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                $this->load->view('backend/sudo/partials/blank', $data2);
                $this->load->view('backend/sudo/exercises', $data);
                $this->load->view('backend/partials/footer');
            }

        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function exercise($action, $id = null)
    {
        if ($this->ion_auth->check_login('sudo'))
        {

            $data['action'] = $action;
            if($action == 'add')
            {
                $data['page_title'] = "Añadir ejercicio básico";
            }
            elseif($action == 'edit')
            {
                $data['page_title'] = "Editar ejercicio básico";
                $exercise = $this->exercise->getBasicExercise($id);
            }
            

            // validate form input
            $is_unique = ($this->input->post('name') != $exercise->name) ? 'required|is_unique[exercise_basic.name]' : 'required';
            $this->form_validation->set_rules('name', 'name', $is_unique);

            
            if($this->input->post('short_name') != $exercise->short_name AND $this->input->post('short_name') != null)
            {
                $is_unique = 'is_unique[exercise_basic.short_name]';      
            }
            else
            {
                $is_unique = '';
            }

            $this->form_validation->set_rules('short_name', 'short_name', $is_unique);

            if ($this->form_validation->run() == true)
            {
                $additional_data = array(
                    'name'      => $this->input->post('name'),
                    'short_name'      => ($this->input->post('short_name') == null)? NULL : $this->input->post('short_name')
                );
                
                if($action == 'add')
                {
                    if($this->exercise->setBasicExercise($additional_data))
                        $this->session->set_flashdata('success', 'Nuevo ejercicio básico creado');
                    else
                        $this->session->set_flashdata('error', 'No se pudo crear el nuevo ejercicio básico.');
                }
                elseif($action == 'edit')
                {
                    if($this->exercise->editBasicExercise($id, $additional_data))
                        $this->session->set_flashdata('success', 'Ejercicio básico editado.');
                    else
                        $this->session->set_flashdata('error', 'No se pudo editar el ejercicio básico.');
                }

                
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->exercises();
            }
            else
            {
                // display the create user form
                // set the flash data error message if there is one
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $data['id'] = $id;

                if (!empty(form_error('name'))) $class = "error"; else $class = "valid";
                $data['name'] = array(
                    'name'  => 'name',
                    'id'    => 'name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $exercise->name,
                );

                if (!empty(form_error('short_name'))) $class = "error"; else $class = "valid";
                $data['short_name'] = array(
                    'name'  => 'short_name',
                    'id'    => 'short_name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'value' => $exercise->short_name,
                );


                if($this->input->post('ajax') OR $this->input->is_ajax_request())
                {
                    $this->load->view('backend/sudo/exercise_basic_form', $data);
                }
                else
                {
                    $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                    $this->load->view('backend/sudo/partials/blank', $data2);
                    $this->load->view('backend/sudo/exercise_basic_form', $data);
                    $this->load->view('backend/partials/footer');
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function deleteExercise($id) 
    {
        if ($this->ion_auth->check_login('sudo'))
        {
            if($this->exercise->getBasicExercise($id))
                if($this->exercise->deleteBasicExercise($id))
                        $this->session->set_flashdata('success', 'Ejercicio básico eliminado.');
                    else
                        $this->session->set_flashdata('error', 'No se pudo eliminar el ejercicio básico.');  
            else
                $this->session->set_flashdata('info', 'No existe el ejercicio indicado.');

           $this->exercises();         
        }
        else
        {
            $this->load->view('backend/no_session');
        }     
    }
    
    function exercise_materials($ajax = false)
    {
        if ($this->ion_auth->check_login('sudo') )
        {
            $data['page_title'] = "Materiales para ejercicios";
            $data['materials'] = $this->exercise->getMaterials();

            if ($this->input->post('ajax') OR $this->input->is_ajax_request() OR $ajax == true)  
            {  
                $this->load->view('backend/sudo/exercise_materials', $data);
            }else{
                $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                $this->load->view('backend/sudo/partials/blank', $data2);
                $this->load->view('backend/sudo/exercise_materials', $data);
                $this->load->view('backend/partials/footer');
            }

        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function exercise_material($action, $id = null)
    {
        if ($this->ion_auth->check_login('sudo'))
        {

            $data['action'] = $action;
            if($action == 'add')
            {
                $data['page_title'] = "Añadir material";
            }
            elseif($action == 'edit')
            {
                $data['page_title'] = "Editar material";
                $material = $this->exercise->getMaterial($id);
            }
            

            // validate form input
            $is_unique = ($this->input->post('name') != $material->name) ? 'required|is_unique[exercise_materials.name]' : 'required';
            $this->form_validation->set_rules('name', 'name', $is_unique);

            if($this->input->post('short_name') != $material->short_name AND $this->input->post('short_name') != null)
            {
                $is_unique = 'is_unique[exercise_materials.short_name]';      
            }
            else
            {
                $is_unique = '';
            }
            $this->form_validation->set_rules('short_name', 'short_name', $is_unique);

            if ($this->form_validation->run() == true)
            {
                $additional_data = array(
                    'name'      => $this->input->post('name'),
                    'short_name'      => ($this->input->post('short_name') == null)? NULL : $this->input->post('short_name')
                );
                
                if($action == 'add')
                {
                    if($this->exercise->setMaterial($additional_data))
                        $this->session->set_flashdata('success', 'Nuevo material creado');
                    else
                        $this->session->set_flashdata('error', 'No se pudo crear el nuevo material.');
                }
                elseif($action == 'edit')
                {
                    if($this->exercise->editMaterial($id, $additional_data))
                        $this->session->set_flashdata('success', 'Material editado.');
                    else
                        $this->session->set_flashdata('error', 'No se pudo editar el material.');
                }

                
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->exercise_materials();
            }
            else
            {
                // display the create user form
                // set the flash data error message if there is one
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $data['id'] = $id;

                if (!empty(form_error('name'))) $class = "error"; else $class = "valid";
                $data['name'] = array(
                    'name'  => 'name',
                    'id'    => 'name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $material->name,
                );

                if (!empty(form_error('short_name'))) $class = "error"; else $class = "valid";
                $data['short_name'] = array(
                    'name'  => 'short_name',
                    'id'    => 'short_name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'value' => $material->short_name,
                );


                if($this->input->post('ajax') OR $this->input->is_ajax_request())
                {
                    $this->load->view('backend/sudo/exercise_material_form', $data);
                }
                else
                {
                    $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                    $this->load->view('backend/sudo/partials/blank', $data2);
                    $this->load->view('backend/sudo/exercise_material_form', $data);
                    $this->load->view('backend/partials/footer');
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function deleteExerciseMaterial($id) 
    {
        if ($this->ion_auth->check_login('sudo'))
        {
            if($this->exercise->getMaterial($id))
                if($this->exercise->deleteMaterial($id))
                    $this->session->set_flashdata('success', 'Material eliminado.');
                else
                    $this->session->set_flashdata('error', 'No se pudo eliminar el material.');  
            else
                $this->session->set_flashdata('info', 'No existe el material indicado.');

           $this->materials();         
        }
        else
        {
            $this->load->view('backend/no_session');
        }     
    }

    function exercise_types($ajax = false)
    {
        if ($this->ion_auth->check_login('sudo') )
        {
            $data['page_title'] = "Tipos de ejercicios";
            $data['types'] = $this->exercise->getTypes();

            if ($this->input->post('ajax') OR $this->input->is_ajax_request() OR $ajax == true)  
            {  
                $this->load->view('backend/sudo/exercise_types', $data);
            }else{
                $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                $this->load->view('backend/sudo/partials/blank', $data2);
                $this->load->view('backend/sudo/exercise_types', $data);
                $this->load->view('backend/partials/footer');
            }

        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function exercise_type($action, $id = null)
    {
        if ($this->ion_auth->check_login('sudo'))
        {

            $data['action'] = $action;
            if($action == 'add')
            {
                $data['page_title'] = "Añadir tipo de ejercicio";
            }
            elseif($action == 'edit')
            {
                $data['page_title'] = "Editar tipo de ejercicio";
                $type = $this->exercise->getType($id);
            }
            

            // validate form input
            $is_unique = ($this->input->post('name') != $type->name) ? 'required|is_unique[exercise_types.name]' : 'required';
            $this->form_validation->set_rules('name', 'name', $is_unique);

            if($this->input->post('short_name') != $type->short_name AND $this->input->post('short_name') != null)
            {
                $is_unique = 'is_unique[exercise_types.short_name]';      
            }
            else
            {
                $is_unique = '';
            }
            $this->form_validation->set_rules('short_name', 'short_name', $is_unique);

            if ($this->form_validation->run() == true)
            {
                $additional_data = array(
                    'name'      => $this->input->post('name'),
                    'short_name'      => ($this->input->post('short_name') == null)? NULL : $this->input->post('short_name')
                );
                
                if($action == 'add')
                {
                    if($this->exercise->setType($additional_data))
                        $this->session->set_flashdata('success', 'Nuevo tipo de ejercicio creado');
                    else
                        $this->session->set_flashdata('error', 'No se pudo crear el nuevo tipo de ejercicio.');
                }
                elseif($action == 'edit')
                {
                    if($this->exercise->editType($id, $additional_data))
                        $this->session->set_flashdata('success', 'Tipo de ejercicio editado.');
                    else
                        $this->session->set_flashdata('error', 'No se pudo editar el tipo de ejercici.');
                }

                
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->exercise_types();
            }
            else
            {
                // display the create user form
                // set the flash data error message if there is one
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $data['id'] = $id;

                if (!empty(form_error('name'))) $class = "error"; else $class = "valid";
                $data['name'] = array(
                    'name'  => 'name',
                    'id'    => 'name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $type->name,
                );

                if (!empty(form_error('short_name'))) $class = "error"; else $class = "valid";
                $data['short_name'] = array(
                    'name'  => 'short_name',
                    'id'    => 'short_name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'value' => $type->short_name,
                );


                if($this->input->post('ajax') OR $this->input->is_ajax_request())
                {
                    $this->load->view('backend/sudo/exercise_type_form', $data);
                }
                else
                {
                    $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                    $this->load->view('backend/sudo/partials/blank', $data2);
                    $this->load->view('backend/sudo/exercise_type_form', $data);
                    $this->load->view('backend/partials/footer');
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function deleteExerciseType($id) 
    {
        if ($this->ion_auth->check_login('sudo'))
        {
            if($this->exercise->getType($id))
                if($this->exercise->deleteType($id))
                    $this->session->set_flashdata('success', 'Tipo de ejercicio eliminado.');
                else
                    $this->session->set_flashdata('error', 'No se pudo eliminar el tipo de ejercicio.');  
            else
                $this->session->set_flashdata('info', 'No existe el tipo de ejercicio indicado.');

           $this->types();         
        }
        else
        {
            $this->load->view('backend/no_session');
        }     
    }

    function sports($ajax = false)
    {
        if ($this->ion_auth->check_login('sudo') )
        {
            $data['page_title'] = "Tipos de ejercicios";
            $data['sports'] = $this->exercise->getSports();

            if ($this->input->post('ajax') OR $this->input->is_ajax_request() OR $ajax == true)  
            {  
                $this->load->view('backend/sudo/sports', $data);
            }else{
                $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                $this->load->view('backend/sudo/partials/blank', $data2);
                $this->load->view('backend/sudo/sports', $data);
                $this->load->view('backend/partials/footer');
            }

        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function sport($action, $id = null)
    {
        if ($this->ion_auth->check_login('sudo'))
        {

            $data['action'] = $action;
            if($action == 'add')
            {
                $data['page_title'] = "Añadir deporte";
            }
            elseif($action == 'edit')
            {
                $data['page_title'] = "Editar deporte";
                $sport = $this->exercise->getSport($id);
            }
            

            // validate form input
            $is_unique = ($this->input->post('name') != $sport->name) ? 'required|is_unique[exercise_sports.name]' : 'required';
            $this->form_validation->set_rules('name', 'name', $is_unique);

            if ($this->form_validation->run() == true)
            {
                $additional_data = array(
                    'name'      => $this->input->post('name')
                );
                
                if($action == 'add')
                {
                    if($this->exercise->setSport($additional_data))
                        $this->session->set_flashdata('success', 'Deporte creado');
                    else
                        $this->session->set_flashdata('error', 'No se pudo crear el nuevo deporte.');
                }
                elseif($action == 'edit')
                {
                    if($this->exercise->editSport($id, $additional_data))
                        $this->session->set_flashdata('success', 'Deporte editado.');
                    else
                        $this->session->set_flashdata('error', 'No se pudo editar el deporte.');
                }

                
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->sports();
            }
            else
            {
                // display the create user form
                // set the flash data error message if there is one
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $data['id'] = $id;

                if (!empty(form_error('name'))) $class = "error"; else $class = "valid";
                $data['name'] = array(
                    'name'  => 'name',
                    'id'    => 'name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $sport->name,
                );


                if($this->input->post('ajax') OR $this->input->is_ajax_request())
                {
                    $this->load->view('backend/sudo/sport_form', $data);
                }
                else
                {
                    $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                    $this->load->view('backend/sudo/partials/blank', $data2);
                    $this->load->view('backend/sudo/sport_form', $data);
                    $this->load->view('backend/partials/footer');
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function deleteSport($id) 
    {
        if ($this->ion_auth->check_login('sudo'))
        {
            if($this->exercise->getSport($id))
                if($this->exercise->deleteSport($id))
                    $this->session->set_flashdata('success', 'Deporte eliminado.');
                else
                    $this->session->set_flashdata('error', 'No se pudo eliminar el deporte.');  
            else
                $this->session->set_flashdata('info', 'No existe el deporte indicado.');

           $this->sports();         
        }
        else
        {
            $this->load->view('backend/no_session');
        }     
    }

    function exercise_variations($ajax = false)
    {
        if ($this->ion_auth->check_login('sudo') )
        {
            $data['page_title'] = "Ejercicios";
            $data['variations'] = $this->exercise->getVariations();


            if ($this->input->post('ajax') OR $this->input->is_ajax_request() OR $ajax == true)  
            {  
                $this->load->view('backend/sudo/exercise_variations', $data);
            }else{
                $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                $this->load->view('backend/sudo/partials/blank', $data2);
                $this->load->view('backend/sudo/exercise_variations', $data);
                $this->load->view('backend/partials/footer');
            }

        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function exercise_variation($action, $id = null)
    {
        if ($this->ion_auth->check_login('sudo'))
        {

            $data['action'] = $action;
            $relations = $this->exercise->getRelations();
            $parameters = $this->exercise->getParameters();

            if($action == 'add')
            {
                $data['page_title'] = "Añadir ejercicio";
                $variation = (object)[
                    'name'      => '',
                    'short_name'      => '',
                    'reps'      => '',
                    'load'      => '',
                    'distance'      => '',
                    'height'      => '',
                    'time'      => '',
                    'energy'      => '',
                    'tons'      => '',
                    'work'      => ''
                ];
            }
            elseif($action == 'edit')
            {
                $data['page_title'] = "Editar ejercicio";

                if($this->exercise->getVariation($id) !== FALSE)
                {
                    $result = $this->exercise->getVariation($id, TRUE);
                    $variation = $result['variation'];
                    
                    foreach ($relations as $key => $value) 
                    {
                        if(!empty($result[$value]) AND sizeof($result[$value]) > 0)
                        {
                            $$value = $result[$value];
                        }
                        else
                            $$value = array();
                    }
                }
                else
                   $this->exercise_variations(); 
            }

            // validate form input
            $is_unique = ($this->input->post('name') != $variation->name) ? 'required|is_unique[exercise_variations.name]' : 'required';
            $this->form_validation->set_rules('name', 'name', $is_unique);

            
            if($this->input->post('short_name') != $variation->short_name AND $this->input->post('short_name') != null)
            {
                $is_unique = 'is_unique[exercise_variations.short_name]';      
            }
            else
            {
                $is_unique = '';
            }
            $this->form_validation->set_rules('short_name', 'short_name', $is_unique);

            //$this->form_validation->set_rules('basic', 'Basico/s', 'callback_multiple_select[basic]');
            //$this->form_validation->set_rules('muscles_primary', 'Músculo/s primario/s', 'callback_multiple_select[muscles_primary]');

            $logs = array('reps', 'load', 'distance', 'height', 'time', 'energy', 'tons', 'work');

            if ($this->form_validation->run() == true)
            {
                $additional_data = array(
                    'name'      => $this->input->post('name'),
                    'short_name'      => ($this->input->post('short_name') == null)? NULL : $this->input->post('short_name'),
                    'reps' => $this->input->post('reps'),
                    'load' => $this->input->post('load'),
                    'distance' => $this->input->post('distance'),
                    'height' => $this->input->post('height'),
                    'time' => $this->input->post('time'),
                    'energy' => $this->input->post('energy'),
                    'tons' => $this->input->post('tons'),
                    'work' => $this->input->post('work')
                );

                $basics = $this->input->post('basic[]');
                $muscles_primary = $this->input->post('muscles_primary[]');
                $muscles_secondary = $this->input->post('muscles_secondary[]');
                $targets = $this->input->post('target[]');
                $types = $this->input->post('type[]');
                $contractions = $this->input->post('contraction[]');
                $materials = $this->input->post('material[]');
                $mechanics = $this->input->post('mechanic[]');
                $movements = $this->input->post('movement[]');
                
                if($action == 'add')
                {
                    if($this->exercise->setVariation($additional_data, $basics, $muscles_primary, $muscles_secondary, $targets, $types, $mechanics, $materials, $movements, $contractions) !== FALSE)
                        $this->session->set_flashdata('success', 'Nuevo ejercicio creado');
                    else
                        $this->session->set_flashdata('error', 'No se pudo crear el ejercicio.');
                }
                elseif($action == 'edit')
                {
                    if($this->exercise->editVariation($id, $additional_data, $basics, $muscles_primary, $muscles_secondary, $targets, $types, $mechanics, $materials, $movements, $contractions) !== FALSE)
                        $this->session->set_flashdata('success', 'Nuevo ejercicio editado');
                    else
                        $this->session->set_flashdata('error', 'No se pudo editar el ejercicio.');
                }

                
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                $this->exercise_variations();
            }
            else
            {

                // display the create user form
                // set the flash data error message if there is one
                $data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                $data['id'] = $id;

                if (!empty(form_error('name'))) $class = "error"; else $class = "valid";
                $data['name'] = array(
                    'name'  => 'name',
                    'id'    => 'name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'required' => '',
                    'value' => $this->form_validation->set_value('name', $variation->name),
                );

                if (!empty(form_error('short_name'))) $class = "error"; else $class = "valid";
                $data['short_name'] = array(
                    'name'  => 'short_name',
                    'id'    => 'short_name',
                    'class' => 'form-control '.$class,
                    'type'  => 'text',
                    'value' => $this->form_validation->set_value('short_name', $variation->short_name),
                );

                foreach ($logs as $key => $value) 
                {
                    $data[$value.'_status'] = $variation->$value;
                    $data[$value] = array(
                        'name'  => $value,
                        'id'    => $value,
                        'class' => 'form-control '.$class,
                        'type'  => 'text',
                        'value' => $this->form_validation->set_value('active', $variation->$value), 
                    );
                    $data[$value.'_options'] = array('0'=> 'No', '1' =>'Si');
                }

                
                foreach ($parameters as $key => $value) 
                {
                    $options = $this->exercise->getParameter($value);
                    foreach ($options as $k => $v) 
                    {
                        if($value == 'types' OR $value == 'basics' OR $value == 'materials')
                            $data[$value.'_options'][$options[$k]['id']] =  $options[$k]['name'].' ('.$options[$k]['short_name'].')';
                        else if($value == 'contractions' OR $value == 'mechanics' OR $value == 'movements' OR $value == 'targets')
                            $data[$value.'_options'][$options[$k]['id']] =  $options[$k]['name'].': '.$options[$k]['description'];
                        else if ($value == 'muscles')
                            $data[$value.'_options'][$options[$k]['id']] =  $options[$k]['muscle_group'].': '.$options[$k]['name'];
                    }
                }

                

                foreach ($relations as $key => $value) 
                {
                    if($action == 'edit')
                    {
                        foreach ($$value as $k => $v) 
                        {
                            if($value == 'muscles_primary' OR $value == 'muscles_secondary') $x = 'id_muscle';
                            else $x = 'id_'.$value;
                            $data[$value.'s'][] =  $$value[$k][$x];                            
                        } 
                    }
                    elseif($action == 'add')
                    {
                        $data[$value] = '';
                    }

                    if (!empty(form_error($value))) $class = "error"; else $class = "valid";
                    $data[$value] = array(
                        'name'  => $value,
                        'id'    => $value,
                        'class' => 'form-control '.$class,
                        'type'  => 'text',
                        'required' => '',
                        'value' => $this->form_validation->set_value($value, $data[$value.'s']), //pendiente que muestre lo enviado en post previo si algun error de validación....
                    );
                }

                if($this->input->post('ajax') OR $this->input->is_ajax_request())
                {
                    $this->load->view('backend/sudo/exercise_variation_form', $data);
                }
                else
                {
                    $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                    $this->load->view('backend/sudo/partials/blank', $data2);
                    $this->load->view('backend/sudo/exercise_variation_form', $data);
                    $this->load->view('backend/partials/footer');
                }
            }
        }
        else
        {
            $this->load->view('backend/no_session');
        }
    }

    function deleteExerciseVariation($id) 
    {
        if ($this->ion_auth->check_login('sudo'))
        {
            if($this->exercise->getVariation($id) !== FALSE)
                if($this->exercise->deleteVariation($id))
                        $this->session->set_flashdata('success', 'Ejercicio eliminado.');
                    else
                        $this->session->set_flashdata('error', 'No se pudo eliminar el ejercicioo.');  
            else
                $this->session->set_flashdata('info', 'No existe el ejercicio indicado.');

           $this->exercise_variations();         
        }
        else
        {
            $this->load->view('backend/no_session');
        }     
    }

    public function multiple_select($rel)
    {
         $arr = $this->input->post($rel.'[]');
         if(!empty($arr) AND sizeof($arr) > 0)
         {
            return TRUE;
         }
         
        if($rel == 'basic')
            $this->form_validation->set_message('multiple_select','Selecciona al menos un ejercicio.');
        else if($rel == 'muscles_primary') 
            $this->form_validation->set_message('multiple_select','Selecciona al menos un músculo primario.');
        else
            $this->form_validation->set_message('multiple_select','Selecciona al menos un valor.');
         return FALSE;
    }
}