<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Wod_model extends CI_Model
{
    //DATABASE
    private $bTable = 'boxes';

    private $prTable = 'wod_personal_records';
    private $wcTable = 'wod_categories';
    private $weTable = 'wod_excercises';
    private $wpTable = 'wod_phases';
    private $wrTable = 'wod_routines';
    private $wrtTable = 'wod_routine_types';
    private $wreTable = 'wod_routines_excercises';
    private $wsTable = 'wod_sport';

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

//////////////////////////////////////////////////////
//  SECTION: ROUTINES
/////////////////////////////////////////////////////
    function registerRoutine($params)
    {
        return $this->db->insert($this->wrTable, $params);
    }

    function updateRoutine($id, $params)
    {

    }

    function getRoutines($from = null)
    {
        $this->db->select('wod_routines.*, wod_phases.name as phase, wod_categories.name as category, wod_routine_types.type');
        $this->db->from($this->wrTable);
        if($from != 'user') {
            $this->db->where('user_id =', $this->user_id);
        }
        elseif($from != 'fitbox')
        {
            $this->db->where('id_box =', 0)->where('user_id !=', $this->user_id);
        }
        elseif($from != 'public')
        {
            $this->db->where('public =', 1)->where('user_id !=', $this->user_id)->where('id_box !=', 0);
        }
        $this->db->join($this->wpTable, 'wod_routines.id_phase = wod_phases.id');
        $this->db->join($this->wcTable, 'wod_routines.id_category = wod_categories.id');
        $this->db->join($this->wrtTable, 'wod_routines.id_type = wod_routine_types.id');

        return $this->db->get()->result();
    }  

    function getRoutine($id)
    {
        $this->db->select('wod_routines.*, wod_phases.id as phase_id, wod_phases.name as phase, wod_categories.id as category_id, wod_categories.name as category, wod_routine_types.id, wod_routine_types.type');
        $this->db->from($this->wrTable);
        $this->db->where('wod_routines.id =', $id);
        $this->db->join($this->wpTable, 'wod_routines.id_phase = wod_phases.id');
        $this->db->join($this->wcTable, 'wod_routines.id_category = wod_categories.id');
        $this->db->join($this->wrtTable, 'wod_routines.id_type = wod_routine_types.id');

        return $this->db->where("user_id =".$this->user_id." OR id_box = 0")->get()->row();
    }

    function getPhases()
    {
        $ph_list = $this->db->select('id, name')->from($this->wpTable)->get()->result();

        $phase_list = array('' =>'-- Seleccione --');
        foreach ($ph_list as $list) 
        {
          $phase_list[$list->id] = $list->name;
        }
        return $phase_list;
    }

    function getTypes()
    {
        $tp_list = $this->db->select('id, type')->order_by('type', 'ASC')->from($this->wrtTable)->get()->result();

        $type_list = array('' =>'-- Seleccione --');
        foreach ($tp_list as $list) 
        {
          $type_list[$list->id] = $list->type;
        }
        return $type_list;
    }

    function getType($id)
    {
        return $this->db->from($this->wrtTable)->where('id', $id)->get()->row();
    }

    function getCategories()
    {
        $this->db->select('id, name')->order_by('name', 'ASC')->from($this->wcTable);
        if($this->user_id != 0) $this->db->where('custom = 1');
        $ct_list = $this->db->get()->result();

        $category_list = array('' =>'-- Seleccione --');
        foreach ($ct_list as $list) 
        {
          $category_list[$list->id] = $list->name;
        }
        return $category_list;
    }  

    function getSports()
    {
        $this->db->select('id, name')->order_by('name', 'ASC')->from($this->wsTable);
        $sp_list = $this->db->get()->result();

        $sport_list = array('' =>'-- Seleccione --');
        foreach ($sp_list as $list) 
        {
          $sport_list[$list->id] = $list->name;
        }
        return $sport_list;
    }    

///////////////////////////////////////////////////////////
//  SECTION: PERSONAL RECORDS
///////////////////////////////////////////////////////////

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

    function getPRs($target)
    {   
         
         //$query = $this->db->select('wod_personal_records.id, wod_personal_records.user_id, wod_personal_records.time, wod_personal_records.load, wod_personal_records.distance, wod_personal_records.height, wod_personal_records.reps, wod_personal_records.power, wod_personal_records.tons, wod_personal_records.date, wod_personal_records.manual, wod_excercises.id_target, wod_excercises.name, wod_excercises.short_name')
          //              ->from($this->prTable)
          //              ->join($this->weTable, 'wod_personal_records.excercise_id = wod_excercises.id')
          //              ->where('user_id =', $this->user_id)->where('wod_excercises.id_target =', $target)
          //              ->order_by('date', 'DESC')
          //              ->get()->result();

        return $this->db->query("SELECT ex.id_target, ex.name, ex.short_name, pr1.id, pr1.user_id, pr1.time, pr1.load, pr1.RM, pr1.reps, pr1.distance, pr1.height, pr1.power, pr1.tons, pr1.date, pr1.manual
                                FROM wod_excercises ex
                                JOIN wod_personal_records pr1 ON (ex.id = pr1.excercise_id)
                                LEFT OUTER JOIN wod_personal_records pr2 ON (ex.id = pr2.excercise_id AND (pr1.date < pr2.date OR pr1.date = pr2.date AND pr1.id < pr2.id))
                                WHERE pr2.id IS NULL AND pr1.user_id=$this->user_id AND ex.id_target=$target")->result();

    }

    function updatePR($id, $params)
    {
        $pr = $this->getPR($id);
        if($pr->manual == 1) 
        {

            return $this->db->where('id', $id)->update($this->prTable, $params);
        }
    }

    function deletePR($id)
    {
        $this->db->delete($this->prTable, array('id' => $id));
    }


///////////////////////////////////////////////////
//  SECTION: EXCERCISES
//////////////////////////////////////////////////

    function getExcercise($id = null)
    {
        $this->db->from($this->weTable);
        if($id != null) $this->db->where('id =', $id);
        return $this->db->get()->row();
    }

    function getExcercises($cat = null)
    {
        $exc_list = $this->db->select('id, name')->order_by('name', 'ASC')->from($this->weTable)->get()->result();

        $excercise_list = array('' =>'-- Seleccione --');
        foreach ($exc_list as $list) 
        {
          $excercise_list[$list->id] = $list->name;
        }
        return $excercise_list;
    }

    function calcMaxRep($reps, $load)
    {
        if($reps <=10) 
        {
            $RM[1] = ($reps == 1) ? $load : $load*($reps/30+1); //Epley Formula => only reliable if reps < 10
            $RM[5] = $RM[1]*0.87;
            $RM[10] = $RM[1]*0.75;
            $RM[15] = $RM[1]*0.63;
            return $RM;
        }
        else
        {
            return null;
        }
    }


}   
?>