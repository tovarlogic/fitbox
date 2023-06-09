<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/* 
 * Generated by CRUDigniter v1.3 Beta 
 * www.crudigniter.com
 */
 
class MY_Model2 extends CI_Model
{
    
	protected $table;
    protected $fields = array();

    function __construct()
    {
        parent::__construct();
        set_fields();
    }
    
    function set_fields()
    {
        $this->db->cache_on();
        $this->fields = $this->db->list_fields($this->table);
        $this->db->cache_off();
    }

    function get($id)
    {
        return $this->db->get_where($table,array('id'=>$id))->row_array();
    }
    
    function get_all()
    {
        return $this->db->get($table)->result_array();
    }
    
    function insert($params)
    {
        $this->db->insert($table,$params);

        return ($this->db->affected_rows() != 1) ? false : $this->db->insert_id();
    }
    
    function update($id,$params)
    {
        $this->db->where('id',$id);
        $this->db->update($table,$params);

        return ($this->db->affected_rows() != 1) ? false : true;
    }
    
    function delete($id)
    {
        $this->db->delete($table,array('id'=>$id));

        return ($this->db->affected_rows() != 1) ? false : true;
    }
}
