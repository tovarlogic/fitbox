<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class PRs_model extends CI_Model
{
	//DATABASE
    private $prTable = 'wod_personal_records';

    //VAR
    public $user_id = null;


    function __construct()
    {
        parent::__construct();
    }
    // INITIALIZATION

    function setUser($user_id)
    {
        $this->user_id=$user_id;
    }

    function registerPR($params)
    {
        return $this->db->insert($this->prTable, $params);
    }

    function getPR($id = null)
    {
        $this->db->from($this->prTable);
        if($id != null) $this->db->where('id =', $id);
        return $this->db->where('user_id =', $this->user_id)->get()->row();
    }    


    function updatePR($id, $params)
    {

        if(getPR($id)->manual == 1) return $this->db->where('id', $id)->update($this->prTable, $params);
    }

    function deletePR($id)
    {
        $this->db->delete($this->prTable, array('id' => $id));
    }

}

?>