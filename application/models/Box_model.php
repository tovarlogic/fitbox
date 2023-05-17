<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Box_model extends MY_Model
{
    //DATABASE
    private $bTable = 'boxes';
    private $sTable = 'settings';
    private $uTable = 'auth_users';
    private $ugTable = 'auth_users_groups';
    private $mTable = 'ms_memberships';
    private $muTable = 'ms_memberships_users';
    private $msTable = 'ms_memberships_services';
    private $mssTable = 'ms_settings';
    private $pTable = 'ms_payments';
    private $pdTable = 'ms_payments_deleted';
    private $gTable = 'ms_gateways';
    private $tTable = 'ms_transactions';
    private $iuTable = 'ms_iban_users';
    private $cTable = 'bs_coupons';

    //VAR
    public $box_id = null;

    private $user_groups = array( 
        "sudo"=>1, 
        "sadmin"=>2, 
        "admin"=>3, 
        "rrhh"=>4, 
        "finance"=>5, 
        "fcoach"=>6, 
        "coach"=>7, 
        "comercial"=>8, 
        "marketing"=>9, 
        "board"=>10, 
        "athlete"=>11, 
        "guest"=>12
    ); //pendiente crear funcion en auth "getGroups"

    public $ath_status = array(
        "active"=>"y", // pagado y valido
        "inactive"=>"n", // caducado
        "pending"=>"p", // pendiente primer pago
        "grace"=>"g", // en periodo de gracia (pendiente de renovación)
        "banned"=>"b", // non grato (baneado)
        "cancel"=>"c", // cancelado/de baja
        "ended"=>"e" // terminado por consumición
    );

    public $compatibility = array(
        "unico"=>"u", // incompatible con el resto
        "primario"=>"p", // incompatible con otros primarios
        "complementario"=>"c" // compatible con primarios y otros complementarios
    );
    public $athletes = array( 
        "pending"=>0, 
        "banned"=>0, 
        "active"=>0, 
        "member"=>0, 
        "cancel"=>0, 
        "inactive"=>0);

    public $memberships = null;

    function __construct()
    {
        parent::__construct();
    }
    // INITIALIZATION

    /**
     * Function: set_box
     *
     * @param  [type] $box_id [description]
     */
    function set_box($box_id = null) //pendiente: comprobar que box existe
    {
        $result = false;

        if($box_id !== null)//inicialización manual EJ: Calendario
        {
            $this->box_id = $box_id;
            $result = true;
        }
        else
        {
          if( $result = $this->db->select('box_id')->get_where($this->ugTable, array('user_id' => $this->session->userdata('user_id')))->row() ) //pendiente: este metodo no permite que un usuarioo sté en más de un box.
          {
            $this->session->set_userdata("box_id", $result->box_id);
            $this->box_id = $result->box_id;
          }
        }

        return ($result) ? $result : false;
    }

////////////////////////////////////
/// Section: USERS
/// /////////////////////////////////
/// 
    /**
     * Function: create_user
     */
    function create_user($email)
    {
        // get user_id
        $user_id = $result = $this->db->select('id')
                                      ->get_where($this->uTable, array('email' => $email))
                                      ->row()->id;
        // link user to box
        $this->db->set('box_id', $this->box_id)
                  ->where('user_id', $user_id) //pendiente: este metodo no permite que un mismo usuario esté en varios boxes...
                  ->update($this->ugTable);
        // update new_user log
        $this->logs->update_log('users', 'new', $this->box_id, $user_id);
        return $user_id;
    }

    /**
     * Function: editUser
     */
    function editUser($user_id, $params)
    {
        $iban = $params['IBAN'];
        //print("<pre>".print_r($iban,true)."</pre>"); 
        $params = array_diff_key($params,array_flip(['IBAN']));

        if($this->db->get_where($this->ugTable, array('user_id' => $user_id, 'box_id' => $this->box_id))->row())
        {
          if($this->db->where('id', $user_id)->update($this->uTable, $params))
          {
              if($iban != null OR $iban != '')
              {
                  if($this->db->select('IBAN')->get_where($this->iuTable, array('user_id' => $user_id))->row())
                  {
                      if($this->db->where('user_id', $user_id)->where('box_id', $this->box_id)->update($this->iuTable, array('IBAN' => $this->encryption->encrypt($iban))))
                          return true;
                  }
                  else
                  {
                      if($this->db->insert($this->iuTable, array('user_id' => $user_id, 'box_id' => $this->box_id, 'IBAN' => $this->encryption->encrypt($iban))))
                          return true;
                  }
              }
              else
              {
                  if($this->db->select('IBAN')->get_where($this->iuTable, array('user_id' => $user_id))->row())
                  {
                    if($this->db->delete($this->iuTable, array('user_id' => $user_id)))
                      return true;
                  }
                  else
                  {
                    return true;
                  }
              } 
          }
        }
        return false;

    }

    /**
     * Function: deleteUserData
     */
    function deleteUserData($user_id)
    {
      //delete IBAN info
      if($this->db->delete($this->iuTable, array('user_id' => $user_id)))
      {
          //delete memberships info
          if($this->db->delete($this->muTable, array('user_id' => $user_id)))
          {
            $this->logs->update_log('users', 'del', $this->box_id, $user_id);
            return true;
          }
      }
  
      return false;
    }


    /**
     * Function: editGroups
     * updates the groups for a particular user.
     * pendiente que no añada grupos de rango inferior EJ: si sadmin no añadir admin
     */
    function editGroups($user_id, $groups) 
    {
        $errors = 0;
        if($this->db->get_where($this->ugTable, array('user_id' => $user_id, 'box_id' => $this->box_id))->row())
        {
          $result = $this->db->select('group_id')
                              ->get_where($this->ugTable, array('user_id' => $user_id, 
                                                                'box_id' => $this->box_id))
                              ->result();
            foreach ($result as $res) {
              $groups_db[] = $res->group_id;
            }
            
            $add = array_diff($groups, $groups_db); //new groups to insert
            foreach ($add as $key => $value) {
                if(!$this->db->insert($this->ugTable, array('user_id' => $user_id, 
                                                        'box_id' => $this->box_id,
                                                        'group_id' => $value)))
                  $errors++;
            }

            $del = array_diff($groups_db, $groups); //old groups to delete
            foreach ($del as $key => $value) {
                if(!$this->db->delete($this->ugTable, array('user_id' => $user_id, 
                                                        'box_id' => $this->box_id,
                                                        'group_id' => $value)))
                  $errors++;
            }
        }
        if($errors == 0) return true;
        else return false;
    }

    /**
     * Function: addMembershipService
     */
    function addMembershipService($membership_id, $services)
    {
      foreach ($services as $srv) {
          if($srv['include'] == 'on')
          {
              $this->db->insert($this->msTable, array('membership_id' => $membership_id, 
                                                      'box_id' => $this->box_id,
                                                      'service_id' => $srv['id'],
                                                      'qtty' => $srv['qtty']));
          }
        }
      return ($this->db->affected_rows() > 0 )? TRUE : FALSE;
    }


    /**
     * Function: editServices
     * updates the groups for a particular membership plan
     */
    function editServices($membership_id, $services) 
    {
        $this->db->delete($this->msTable, array('membership_id' => $membership_id));

        foreach ($services as $srv) {
          if($srv['include'] == 'on')
          {
              $this->db->insert($this->msTable, array('membership_id' => $membership_id, 
                                                      'box_id' => $this->box_id,
                                                      'service_id' => $srv['id'],
                                                      'qtty' => $srv['qtty']));
          }
        }
    }

    /**
     * Function: count_members
     */
    function count_members($type = 'all', $since = null)
    {
      $this->db->from($this->muTable)->where('box_id', $this->box_id);

      if($type != 'all'){
        if($type == 'member') 
          $this->db->where('membership_id !=', 0);
        else 
          $this->db->where('status =', $this->ath_status[$type]);
      }
      if($since == 'week')
      {
        $this->db->where('updated_on >', strtotime('-7 day', strtotime('today UTC 00:00')));
      }
      elseif($since == 'month')
      {
        $this->db->where('updated_on >', strtotime('-1 month', strtotime('today UTC 00:00')));
      }
      elseif($since == 'year')
      {
        $this->db->where('updated_on >', strtotime('-1 year', strtotime('today UTC 00:00')));
      }

      return $this->db->count_all_results();
    }

    /**
     * Function: getBox
     */
    function getBox()
    { 
      return $this->db->get_where($this->bTable, array('id' => $this->box_id))->row();
    }

    /**
     * Function: getBoxes
     */
    function getBoxes($params)
    { 
      return $this->db->get_where($this->bTable, $params)->result();
    }

    /**
     * Function: checkUserBox
     */
    function checkUserBox($user_id)
    { 
      return ($this->db->get_where($this->ugTable, array('user_id' => $user_id,'box_id' => $this->box_id))->num_rows() == 0)? false : true ;
    }

    /**
     * Function: getUserBoxes
     */
    function getUserBoxes($user_id)
    {
        $this->db->distinct()->select('box_id')->from($this->ugTable)->where('user_id =', $user_id);

         $result = $this->db->get();


        if($result !== FALSE && $result->num_rows() > 0)
           return $result->result_array();
        else
          return FALSE;
    }

    /**
     * Function: getTotalClients
     */
    function getTotalClients($box_id, $groups, $params = null)
    {
        $this->db->select('aug.user_id')
                  ->from('auth_users_groups aug')
                  ->join('ms_memberships_users mu', 'aug.user_id = mu.user_id')
                  ->group_by('mu.user_id')
                  ->where('mu.box_id =', $box_id)
                  ->where_in('aug.group_id', $groups);

        if($params != null && is_array($params))
        {
            foreach ($params as $key => $value) 
            {
                if(is_array($value))
                {
                    $i = 0;
                    $this->db->group_start();
                    foreach ($value as $p ) 
                    {
                        // para array('status' => array('y','p')) -> key = status; p = y, p = p
                        if($i == 0) $this->db->where('`mu`.'.$key, $p); 
                        else $this->db->or_where('`mu`.'.$key, $p); 
                        $i++;
                    }
                    $this->db->group_end();
                }
                else
                {
                    // para array('status' => 'y')
                    $this->db->where('`mu`.'.$key, $value); 
                }

            }
        }

        $result = $this->db->get();


        if($result !== FALSE && $result->num_rows() > 0)
        {
           return $result->num_rows();
        }

        return 0;
    }

    /**
     * Function: getActiveClientsIDs
     */
    function getActiveClientsIDs($groups = null, $box_id = null)
    {
       $this->db->distinct()->select('mu.user_id, mu.box_id')
                ->from('ms_memberships_users mu')
                ->join('auth_users_groups aug', 'aug.user_id = mu.user_id')
                ->where("(mu.status = 'y' OR mu.status = 'g')");

        if($box_id !== null) 
          $this->db->where('mu.box_id =', $box_id);

        if($groups != null)
          $this->db->where_in('aug.group_id', $groups);

        $result = $this->db->get();

        if($result->num_rows() > 0)
        {
            return $result->result_array();
        }
        
        return FALSE;
    }

    /**
     * Function: getClients
     * retrieves personal clients info such as personal info, IBAN and memberships info, if the user is part of 
     * the group/s requested and any of its menberships complies with the params indicated.
     *
     * Parameters:
     *     groups - default null. an array of integers indicating the type of users.
      *     params - default null. can be either an array of a single user membership variable like array('status' => 'y') or many like array('status' => array('y','p'), 'var_name' => array('y','p')).
      *
      * Returns: 
      *   multidim array containing clients info or FALSE (boolean) in case of no clients found
     */
    function getClients($groups = null, $params = null)
    {
       $users = $this->db->distinct()->select('user_id')->where('box_id =', $this->box_id)->where_in('group_id', $groups)->get('auth_users_groups')->result_array();
      
        $this->db->select('
        auth_users_groups.group_id, auth_users_groups.user_id, auth_users_groups.box_id, 
        auth_users.username, auth_users.email, auth_users.phone, auth_users.first_name, auth_users.last_name, auth_users.active, auth_users.created_on, auth_groups.description, auth_users.id') 
                ->from('ms_memberships_users')  
                ->group_by('`ms_memberships_users`.`user_id`')
                ->join('auth_users', '`ms_memberships_users`.`user_id` = `auth_users`.`id`')
                ->join('auth_users_groups', '`ms_memberships_users`.`user_id` = `auth_users_groups`.`user_id`')
                ->join('auth_groups', '`auth_users_groups`.`group_id` = `auth_groups`.`id`')
                ->join('ms_memberships', '`ms_memberships_users`.`membership_id` = `ms_memberships`.`id`')
                ->where_in('`auth_users_groups`.`group_id`', $groups);
              
        if($this->box_id != 0) 
            $this->db->where('`ms_memberships_users`.`box_id` =', $this->box_id);

        if($params != null && is_array($params))
        {
            foreach ($params as $key => $value) 
            {
                if(is_array($value))
                {
                    $i = 0;
                    $this->db->group_start();
                    foreach ($value as $p ) 
                    {
                        // para array('status' => array('y','p')) -> key = status; p = y, p = p
                        if($i == 0) $this->db->where('`ms_memberships_users`.'.$key, $p); 
                        else $this->db->or_where('`ms_memberships_users`.'.$key, $p); 
                        $i++;
                    }
                    $this->db->group_end();
                }
                else
                {
                    // para array('status' => 'y')
                    $this->db->where('`ms_memberships_users`.'.$key, $value); 
                }

            }
        }

      //return $this->unique_multidim_array($this->db->get()->result_array(), 'user_id'); //pendiente que se muestren todos los grupos en un solo resultado
      $result = $this->db->get();

      if($result !== FALSE && $result->num_rows() > 0)
      {
         $result = $result->result_array();

        $i = 0;
        foreach ($result as $res) 
        {
          $result[$i]['IBAN'] = $this->getUserIBAN($res['user_id']);
          $result[$i]['memberships'] = $this->getUserMemberships($res['user_id'], $params);
          $i++;
        }
        return $result;
      }
      return false;
    }


    /**
     * Function: getNoPlanClients
     * retrieves personal clients info such as personal info, IBAN and memberships info, if the user is part of the group/s requested and doesnt or ever had a membership plan.
     *
     * Parameters:
     * groups - default null. an array of integers indicating the type of users.
     * params - default null. can be either an array of a single user membership variable like array('status' => 'y') or many like array('status' => array('y','p'), 'var_name' => array('y','p')).
     *
     * Returns: 
     *  multidim array containing clients info or FALSE (boolean) in case of no clients found
     */
    function getNoPlanClients($box_id = null, $groups = null, $params = null)
    {
      if($box_id == null) $box_id = $this->box_id;
      $clients = $this->db->distinct()->select('ms_memberships_users.user_id')
                                                         ->where('ms_memberships_users.box_id =', $this->box_id)
                                                         ->where_not_in('ms_memberships_users.status', 'y')
                                                         ->where_not_in('ms_memberships_users.status', 'g')
                                                         ->where_not_in('ms_memberships_users.status', 'n')
                                                         ->where_not_in('ms_memberships_users.status', 'c')
                                                         ->where_not_in('ms_memberships_users.status', 'e')
                                                         ->from('ms_memberships_users') 
                                                         ->join('auth_users_groups', '`auth_users_groups`.`user_id` = `ms_memberships_users`.`user_id`')
                                                         ->where_in('group_id', $groups)
                                                         ->join('auth_users', '`auth_users_groups`.`user_id` = `auth_users`.`id`')
                                                         ->get();
      
      $client_array = array();
      if($clients !== FALSE && $clients->num_rows() > 0)
      {
          $clients = $clients->result_array();
          foreach ($clients as $cli ) {
              $client_array[] = $cli['user_id'];
          }
      }

      $this->db->select('
        `auth_users_groups`.`group_id`, `auth_users_groups`.`user_id`, `auth_users_groups`.`box_id`, `auth_groups`.`description`, 
        `auth_users`.`username`, `auth_users`.`email`, `auth_users`.`phone`, `auth_users`.`first_name`, `auth_users`.`last_name`, `auth_users`.`active`, `auth_users`.`id`,`auth_users`.`created_on`') 
                ->from('auth_users_groups')   
                ->join('auth_users', '`auth_users_groups`.`user_id` = `auth_users`.`id`')
                ->join('auth_groups', '`auth_users_groups`.`group_id` = `auth_groups`.`id`');

      if(sizeof($client_array) > 0)
      {
        $this->db->where_not_in('`auth_users_groups`.`user_id`', $client_array);
      }
                
      if($this->box_id != 0)
      {
        $this->db->where('`auth_users_groups`.`box_id` =', $this->box_id);
      }

      if($groups != null)
      {
        $this->db->where_in('group_id', $groups);
      }

      if($params != null)
      {
        foreach ($params as $key => $value) 
        {
          $this->db->where($key, $value);
        }
      }

      $result = $this->db->get();
      if($result !== FALSE && $result->num_rows() > 0)
      {
          $result = $result->result_array();

        $i = 0;
        foreach ($result as $res) 
        {
          $result[$i]['IBAN'] = $this->getUserIBAN($res['user_id']);
          $i++;
        }


          return $this->unique_multidim_array($result, 'user_id'); //pendiente que se muestren todos los grupos en un solo resultado
      }
      return false;
    } 

    /**
     * Function: get_users
     */
    function get_users($box_id = null, $groups = null, $params = null)
    {
      if($box_id == null) $box_id = $this->box_id;
      
      $users = $this->db->distinct()->select('user_id')->where('box_id =', $box_id)->where_in('group_id', $groups)->get('auth_users_groups')->result_array();
 
      $this->db->select('
        `auth_users_groups`.`group_id`, `auth_users_groups`.`user_id`, `auth_users_groups`.`box_id`, `auth_groups`.`description`, 
        `auth_users`.`username`, `auth_users`.`email`, `auth_users`.`phone`, `auth_users`.`first_name`, `auth_users`.`last_name`, `auth_users`.`active`, `auth_users`.`id`,`auth_users`.`created_on`') 
                ->from('auth_users_groups')   
                ->join('auth_users', '`auth_users_groups`.`user_id` = `auth_users`.`id`')
                ->join('auth_groups', '`auth_users_groups`.`group_id` = `auth_groups`.`id`');
              
      if($this->box_id != 0)
      {
        $this->db->where('`auth_users_groups`.`box_id` =', $box_id);
      }

      if($groups != null)
      {
        $this->db->where_in('group_id', $groups);
      }

      if($params != null)
      {
        foreach ($params as $key => $value) 
        {
          $this->db->where($key, $value);
        }
      }

      $result = $this->db->get();
      if($result !== FALSE && $result->num_rows() > 0)
      {
          $result = $result->result_array();
          return $this->unique_multidim_array($result, 'user_id'); //pendiente que se muestren todos los grupos en un solo resultado
      }
      return false;
    }

    /**
     * Function: getUsers
     */
    function getUsers($groups = null, $show_list = TRUE)
    {
      $this->db->distinct()->select('auth_users_groups.user_id, CONCAT(auth_users.first_name, " ", auth_users.last_name, " (", auth_users.phone, ") ") as name')
                ->from('auth_users_groups')
                ->join('auth_users', '`auth_users`.`id` = `auth_users_groups`.`user_id`')  
                ->where('box_id =', $this->box_id)
                ->order_by('auth_users.first_name', 'ASC')
                ->order_by('auth_users.last_name', 'ASC');

      if($groups != null)
      {
        $this->db->where_in('group_id', $groups);
      }
      
      

      if($show_list === TRUE)
      {
        $list = array('all' => 'Todos');
        $result =  $this->db->get()->result();
        foreach ($result as $res) 
        {
            $list[$res->user_id] = $res->name;
        }

          return ($list) ? $list : false;
      }
      else
      {
        $list = array();
        return $this->db->get()->result_array();
      }
      
    }

    /**
     * Function: getUser
     */
    function getUser($user_id, $iban = true)
    {
       $this->db->select('`auth_users`.`id`, username, email, active, first_name, last_name, phone, DNI, gender, auth_users.created_on, birth_date')
                ->from('auth_users') 
                ->join('auth_users_groups', '`auth_users`.`id` = `auth_users_groups`.`user_id`')  
                ->where('`auth_users_groups`.`box_id` =', $this->box_id)
                ->where('`auth_users`.`id` =',$user_id);
      
      $result = $this->db->get()->row();

      if($result AND $iban)
          $result->IBAN = $this->getUserIBAN($user_id);   

      return ($result) ? $result : false;
    }

     /**
     * Function: getUserByEmail
     */
    function getUserByEmail($email)
    {
       $this->db->select('`auth_users`.`id`, username, email, active, first_name, last_name, phone, DNI, gender, auth_users.created_on, birth_date')
                ->from('auth_users') 
                ->join('auth_users_groups', '`auth_users`.`id` = `auth_users_groups`.`user_id`')  
                ->where('`auth_users_groups`.`box_id` =', $this->box_id)
                ->where('`auth_users`.`email` =',$email);
      
      $result = $this->db->get()->row();
 
      return ($result) ? $result : false;
    }
    /**
     * Function: get_user_group
     * returns the HIGHEST GROUP RANK of any user
     */
    function get_user_group($user_id)
    {
        return $this->db->select('group_id')->from($this->ugTable)->where(array('user_id' => $user_id, 'box_id' => $this->box_id))->order_by('group_id', 'ASC')->get()->row()->group_id;
    }

    /**
     * Function: getUserIBAN
     */
    function getUserIBAN($user_id)
    {
          $this->db->select('IBAN')
                ->from('ms_iban_users') 
                ->where('box_id =', $this->box_id)
                ->where('user_id =',$user_id);

          $IBAN = $this->db->get()->row()->IBAN;

          if($IBAN !== false && $IBAN != null)
          {
              return $this->encryption->decrypt($IBAN);
          }
          return null;
    }

////////////////////////////////////////////////////////////////////
// Section: Tarifas
////////////////////////////////////////////////////////////////////

    /**
     * Function: getMembershipServices
     */
    function getMembershipServices($id = null)
    {
      $this->db->select('ms_memberships_services.membership_id, ms_memberships_services.service_id, ms_memberships_services.qtty, bs_services.name')
        ->from('ms_memberships_services')
        ->join('bs_services', 'bs_services.id = ms_memberships_services.service_id'); 
        
        $this->db->where('ms_memberships_services.box_id', $this->box_id);
        $this->db->where('ms_memberships_services.membership_id', $id);

        $this->db->order_by('bs_services.name', 'ASC');

        $result = $this->db->get()->result_array();

        return ($result) ? $result : false;
    }

    /**
     * Function: getServices
     */
    function getServices($active = null)
    {       
        //$this->db->select('id, name, type, spots, interval, status');
        $this->db->where('box_id', $this->box_id);
        if($active != null) $this->db->where('active', $active);
        $this->db->order_by('name', 'ASC');
        $this->db->from('bs_services');

        $result = $this->db->get()->result_array();

        return ($result) ? $result : false;
    }

    /**
     * Function: getMembership
     */
    function getMembership($id)
    {
        $result = $this->db->get_where($this->mTable, array('id' => $id, 'box_id' => $this->box_id))->row();
        $result->{'services'} = $this->getMembershipServices($id);

        return ($result) ? $result : false;
    }

    /**
     * Function: getMemberships
     */
    function getMemberships($active = null, $deprecated = null)
    {               
        $this->db->where('ms_memberships.box_id', $this->box_id);

        if($active != null) $this->db->where('ms_memberships.active', $active);
        if($deprecated != null) $this->db->where('ms_memberships.deprecated', $deprecated);

        $this->db->order_by('ms_memberships.title', 'ASC');
        $this->db->order_by('ms_memberships.active', 'ASC');
        $this->db->order_by('ms_memberships.private', 'DESC');
        $this->db->from('ms_memberships');
        
        $result = $this->db->get()->result_array();

        $i = 0;
        foreach ($result as $res) 
        {
          $result[$i]['services'] = $this->getMembershipServices($res['id']);
          $i++;
        }

        return ($result) ? $result : false;
    }

    /**
     * Function: getMembershipsUsers
     * generic function to get any data from memberships_users
     *
     * Parameters:
     * @param  [type] $params [description]
     * @param  [type] $row [description]
     * @param  [type] $order_by [description]
     * @param  [type] $result_type [description]
     * @param  [type] $join [description]
     *
     * See Also:
     *  <getRow>
     *  <getRows>
     *
     * @return [type] [description]
     */
    function getMembershipsUsers($params, $row = null, $order_by = null, $result_type = null, $join = null)
    {
        if($order_by == null)  $order_by = array('created_on','ASC');

        if($row == TRUE)
            return $this->getRow('ms_memberships_users', '*', $params, $order_by, $result_type, $join);
        else
            return $this->getRows('ms_memberships_users', '*', $params, $order_by, $result_type, $join);

    }

    /**
     * Function: getMembershipSubscriptions
     */
    function getMembershipSubscriptions($membership_id = null)
    {

        $this->db->select('membership_id, COUNT(membership_id) as count')
                ->group_by('membership_id')
                ->from('ms_memberships_users')
                ->where('box_id', $this->box_id);
        $this->db->group_start();
          $this->db->where('status', 'y');
          $this->db->or_where('status', 'g');
        $this->db->group_end();

        if($membership_id != null) $this->db->where('membership_id', $membership_id);

        $result = $this->db->get();

        if($result !== FALSE && $result->num_rows() > 0)
        {
            $arr = array();
            $result = $result->result_array();
            foreach ($result as $row)
            {
               $arr[$row['membership_id']] = $row['count'];
            }

            return $arr;
        } 

        return FALSE;
    }

    /**
     * Function: getUserMembership
     */
    function getUserMembership($id)
    {
       $result = $this->db->get_where($this->muTable, array('id' => $id, 'box_id' => $this->box_id))->row();

        return ($result) ? $result : false;
    }

    /**
     * Function: getUserMemberships
     */
    function getUserMemberships($user_id, $params = null)
    {
        $this->db->select('ms_memberships_users.status, ms_memberships_users.created_on, ms_memberships_users.updated_on, ms_memberships_users.mem_expire, , ms_memberships_users.id, ms_memberships_users.membership_id, 
          ms_memberships.title, ms_memberships.price, ms_memberships.days, ms_memberships.period, ms_memberships.compatibility, ms_memberships.active')
        ->from($this->muTable)
        ->join('ms_memberships', 'ms_memberships.id = ms_memberships_users.membership_id')  
        ->where('ms_memberships_users.box_id =', $this->box_id)
        ->where('ms_memberships_users.user_id =',$user_id);

        if($params != null && is_array($params))
        {
            foreach ($params as $key => $value) 
            {
                if(is_array($value))
                {
                    $this->db->group_start();
                    $i = 0;
                    foreach ($value as $p ) 
                    {
                        // para array('status' => array('y','p')) -> key = status; p = y, p = p
                        if($i == 0) $this->db->where('`ms_memberships_users`.'.$key, $p); 
                        else $this->db->or_where('`ms_memberships_users`.'.$key, $p); 
                        $i++;
                    }
                    $this->db->group_end(); 
                }
                else
                {
                    // para array('status' => 'y')
                    $this->db->where('`ms_memberships_users`.'.$key, $value); 
                }

            }
        }

      $result = $this->db->get()->result_array();
      return ($result) ? $result : false;
    }

    /**
     * Function: getUserAvailableMemberships
     * returns active memberships that the user is not subscribed to
     */
    function getUserAvailableMemberships($user_id, $params = null)
    {
      $this->db->select('ms.title, ms.price, ms.days, ms.period, ms.deprecated, ms.active, ms.id')
        ->from('ms_memberships ms')
        ->join('ms_memberships_users mu', 'mu.membership_id = ms.id', 'left outer') 
        ->where('ms.box_id =', $this->box_id)
        ->where('ms.active =', 1)
        ->where('ms.deprecated =', 0);

        if($params != null) $this->db->where($params);
        
        $this->db->where("ms.id NOT IN (select `membership_id` from `ms_memberships_users` where `ms_memberships_users`.`status` IN('y', 'g', 'p', 'b') AND `ms_memberships_users`.`user_id` = $user_id)"); // que no esten ya activos o de gracia, pendientes de pago inicial, de renovación, o baneados


      $result = $this->db->get();

      if($result !== FALSE && $result->num_rows() > 0)
      {
         return $result->result_array();
      }
      else
      {
        return false;
      }
    }

    /**
     * Function: setUserMembership
     *
     * @param  [type] $membership [description]
     */
    function setUserMembership($membership)
    { 
        $mem = $this->getMembership($membership['membership_id']);

        $this->db->trans_start();
          if($this->db->insert($this->muTable, $membership))
          {
              $id = $this->db->insert_id(); 
          }
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE)
        {
            return FALSE;
        }

        return $id;
    }

    /**
     * Function: changePlan
     */
    function changePlan($original_mu, $new_membership_id)
    {
        $today = date('Y-m-d');
        if($original_mu->mem_expire > $today AND $original_mu->status == 'y')
        {
          $original_membership = $this->getMembership($original_mu->membership_id);
          $new_membership = $this->getMembership($new_membership_id);
          
          $credit = $this->booking_lib->calculateAmount($original_membership, $today, $original_mu->mem_expire);

          $new_expiring_date = $this->booking_lib->calculateCreditExpiration($credit, $new_membership, $today);
          
          if($new_expiring_date >= $today)
          {
            $params = array(
              'membership_id' => $new_membership_id,
              'mem_expire' => $new_expiring_date
            );

            $result = $this->db->where('id', $original_mu->id)->where('box_id', $this->box_id)->update($this->muTable, $params);
            if($this->db->affected_rows() != 0)
            {
              $params = array(
                    'mu' => $original_mu->id,
                    'from_membership_id' => $original_mu->membership_id, 
                    'to_membership_id' => $new_membership_id, 
                    'from' => $today,
                    'to' => $new_expiring_date,
                    'staff' => $this->session->userdata('user_id'),
                    'type' => 'change',
                    'notes' => 'plan change',
                  );

              $this->logs->set_members_log($original_mu->id, $this->box->box_id, $new_membership_id, 'change');
            }

            return ($result) ? $params : false;
          }
        }
      return false;
    }

    /**
     * Function: create_membership
     */
    function create_membership($params)
    {
        $this->db->insert($this->mTable, $params);
        return $this->db->insert_id();
    }

    /**
     * Function: edit_membership
     */
    function edit_membership($membership_id, $params)
    {

        return $this->db->where('id', $membership_id)->update($this->mTable, $params);
    }

    /**
     * Function: delete_membership
     */
    function delete_membership($membership_id)
    {
        if($this->db->delete($this->msTable, array('membership_id' => $membership_id)))
        {
          if($this->db->delete($this->mTable, array('id' => $membership_id)))
          {
            return true;
          }
        }
        return false;
    }

    /**
     * Function: cancel_user_membership
     */
    function cancel_user_membership($membership_id)
    {
        $this->db->set('status', 'c', TRUE);
                $this->db->where('id', $membership_id);
                $this->db->update($this->muTable);
        //pendiente registrar en log la baja.

        if($this->db->affected_rows() > 0)
          return true;
        else
          return false;
    }

    /**
     * Function: edit_user_membership
     */
    function edit_user_membership($id, $params)
    {

        $this->db->where('id', $id)->where('box_id', $this->box_id)->update($this->muTable, $params);

        if($this->db->affected_rows() > 0)
          return true;
        else
          return false;
    }

    /**
     * Function: delete_user_membership
     *
     */
    function delete_user_membership($membership_id)
    {
        $this->db->delete($this->muTable, array('id' => $membership_id));
        //pendiente registrar en log la baja.

        if($this->db->affected_rows() > 0)
          return true;
        else
          return false;
    }

    /**
     * Function: isUserDeletable
     *
     */
    function isUserDeletable($user_id)
    {

        $this->db->select('id')->from($this->pTable)->where(array('user_id' => $user_id));

        $result = $this->db->get()->num_rows();
        
        //si no tiene pagos
        if($result == 0)
        {

            return true;

        }
        return false;

    }


    /////////////////////////////////////
    /////  Section: FUNCIONES
    /////////////////////////////////////

   /**
    * Function: genericGet
    * customizable select from database
    *
    * @deprecated now use function getRow ingerited in every MY_Model
    * 
    * Parameters:
    *   $what(string) - items to select separated by comma.
    *   $where(array) - items to condition result
    *   $from(string) - table name
    *   $order_by(string) - dafault null. sigle item to order results.
    *   $order(string) - default 'ASC'
    *   $box(bool) - defautl TRUE to limit results to current BOX. FALSE for every BOX.
    *
    * Returns: 
    *   Database result(object) or FALSE(boolean)
    */
    function genericGet($what, $where, $from, $order_by = null, $order = 'ASC', $box = TRUE)
    {
      $this->db->select($what)->from($from);

      foreach ($where as $key => $value) { $this->db->where($key, $value); }

      if ($box === TRUE) $this->db->where('box_id =', $this->box_id);
      if($order_by != null)  $this->db->order_by($order_by, $order);
      
      $result = $this->db->get()->result();

      return ($result) ? $result : false;
    }

    /**
     * Function: convert_to_time_chart
     *
     */
    function convert_to_time_chart($result)
    {
      $converted = '[';

      $i = 0;
      foreach ($result as $key => $value) {
        if($i>0) $coma = ','; else $coma = '';
        $converted .=$coma.'["'.$key.'",'.$value.']';
        $i++;
      }
      return $converted.']';
    }

    /**
     * Function: convert_to_chart
     *
     */
    function convert_to_chart($result)
    {
      $converted = '[';

      $i = 0;
      $months = array("Ene","feb","Mar","Abr","May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
      foreach ($result as $key => $value) {
        if($i>0) $coma = ','; else $coma = '';
        $converted .=$coma.'["'.$months[$key].'",'.$value.']';
        $i++;
      }
      return $converted.']';
    }

    /**
     * Function: unique_multidim_array
     *
     */
    function unique_multidim_array($array, $key) {
      $temp_array = array();
      $i = 0;
      $key_array = array();
     
      foreach($array as $val) {
          if (!in_array($val[$key], $key_array)) {
              $key_array[$i] = $val[$key];
              $temp_array[$i] = $val;
          }
          $i++;
      }
      return $temp_array;
    } 

    /**
     * Function: unique_multidim_array2
     *
     */
    function unique_multidim_array2($array, $key)
    {

      $temp_array = array();
      $i = 0;
      $key_array = array();
     
      foreach($array as $val) {
          if (!in_array($val[$key], $key_array)) {
              $key_array[$i] = $val[$key];
              $temp_array[$i] = $val;
          }
          $i++;
      }
      return $temp_array;

    }
    
}

?>
