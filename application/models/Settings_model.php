<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Settings_model extends CI_Model
{
    
    //DATABASE
    private $ugTable = 'auth_users_groups';
    private $sTable = 'settings';


    //VAR
    public $box_id = null;

    function __construct()
    {
        parent::__construct();
    }


////////////////////////////////////////////////////////////////////
// Sección SETTINGS
////////////////////////////////////////////////////////////////////
///
    function getSettingTables($module)
    {

    }
    function getSettings($module, $subset, $box_id)
    {
        $result = $this->db->where('box_id', $box_id)->from($this->setting_tables[$subset])->get();

        if($result !== FALSE && $result->num_rows() > 0)
        {
            $result = $result->row_array();
            unset($result['id'], $result['box_id']);
            if($subset == 'calendar')
            {
                unset($result['cal_code']);
                return $result;
            }
            else
            {
                return $result;
            }
        }

        $result = $this->config->item($subset.'_default', 'booking_lib');
        return $result;
    }

    function getSettingItem($module, $subset, $field, $box_id = null)
    {
        $box_id = ($box_id == null)? $this->box_id : $box_id;
        
        $result = $this->db->where('box_id', $box_id)->from($this->setting_tables[$subset])->get();
                
        if($result !== FALSE && $result->num_rows() > 0)
        {
            $result = $result->row_array();
            if(isset($result[$field])) 
                    return $result[$field];
        }
        
        $default_settings = $this->config->item($subset.'_default', 'booking_lib');
        if(isset($default_settings[$field]))
        {
            return $default_settings[$field];
        }
        return FALSE;
    }

}

?>