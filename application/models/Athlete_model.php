<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Athlete_model extends CI_Model
{
    //DATABASE
    private $bTable = 'boxes';
    private $augTable = 'auth_users_groups';
    private $mTable = 'ms_memberships';
    private $muTable = 'ms_memberships_users';
    private $msTable = 'ms_memberships_services';
    private $pTable = 'ms_payments';
    private $gTable = 'ms_gateways';
    private $tTable = 'ms_transactions';

    //VAR
    public $user_data = null;
    public $memberships_user = array();
    public $boxes = array();

    public $user_id = null;

    function __construct($id = null)
    {
        parent::__construct();
        $this->user_id = $id;
        if(!is_null($this->user_id)) 
        {
            $this->set_boxes();
            $this->set_user_data();
            $this->set_memberships();
        }
    }
    // INITIALIZATION

    function get_id()
    {
        return $this->boxes;
    }

    function set_boxes()
    {
        $this->db->select('auth_users_groups.user_id, auth_users_groups.group_id, auth_users_groups.box_id, boxes.name')
        ->from($this->augTable)
        ->join('boxes', 'boxes.id = auth_users_groups.box_id') 
        ->where('auth_users_groups.user_id =', $this->user_id);

        $result = $this->db->get();

        if($result !== FALSE && $result->num_rows() > 0)
        {
            $result = $result->result();
            foreach ($result as $res) {
                if(empty($this->boxes[$res->box_id]))
                {
                    $box = new stdClass();
                    $box->id = $res->box_id;
                    $box->name = $res->name;
                    $box->groups = array($res->group_id => $res->group_id);
                    $this->boxes[$res->box_id] = $box; 
                }
                else
                   $this->boxes[$res->box_id]->groups[$res->group_id] = $res->group_id;
            }
        }
        
    }

    function get_boxes()
    {
        return $this->boxes;
    }

    function set_user_data()
    {
        $this->db->select('`auth_users`.`id`, username, email, active, first_name, last_name, phone, DNI, gender, auth_users.created_on, birth_date')
                ->from('auth_users') 
                ->where('`auth_users`.`id` =', $this->user_id);

        $result = $this->db->get();

        if($result !== FALSE && $result->num_rows() > 0)
        {
            $this->user_data = $result->row();
        }
    }

    function get_user_data($item = null)
    {
        if(!is_null($item))
            return $this->user_data->item;
        else
            return $this->user_data;
    }

    function get_iban($box_id)
    {
        $this->db->select('IBAN')->from('ms_iban_users')->where('box_id =', $box_id)->where('user_id =', $this->user_id);

        $IBAN = $this->db->get()->row()->IBAN;
        if($IBAN !== false && $IBAN != null)
        {
            return $this->encryption->decrypt($IBAN);
        }
        return null;
    }

    function set_memberships()
    {
        $this->db->select('ms_memberships_users.*, ms_memberships.title, ms_memberships.price, ms_memberships.days, ms_memberships.period, ms_memberships.active, boxes.name')
        ->from($this->muTable)
        ->join('ms_memberships', 'ms_memberships.id = ms_memberships_users.membership_id') 
        ->join('boxes', 'boxes.id = ms_memberships_users.box_id') 
        ->where('ms_memberships_users.user_id =', $this->user_id);

        $result = $this->db->get();

        if($result !== FALSE && $result->num_rows() > 0)
        {
            $result = $result->result_array();
            foreach ($result as $res) 
            {
                $mu = new stdClass();
                foreach ($res as $key => $value) 
                {
                    $mu->$key = $value;
                } 
                $this->memberships_user[$mu->box_id][$mu->id] = $mu;                
            }
        }
    }

    function get_memberships($box_id = null)
    {
        if(!is_null($box_id))
            return $this->memberships_user[$box_id];
        else
            return $this->memberships_user;
    }

    function getSocialNetworks()
    {

    }

    function setSocialNetworks()
    {

    }

    function updateSocialNetworks()
    {

    }

    function deleteSocialNetworks()
    {

    }

    function getGeo()
    {

    }

    function setGeo()
    {

    }

    function updateGeo()
    {

    }

    function deleteGeo()
    {

    }

}   
?>
