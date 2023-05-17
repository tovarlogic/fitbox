<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// https://github.com/ccschmitz/codeIgniter-base-model

class MY_Model1 extends CI_Model {
    
    protected $table = '';
    protected $fields = array();
    protected $required_fields = array();
    protected $primary_key = 'id';
    
    function __construct()
    {
        parent::__construct();
    }
    
    function insert($options = array())
    {
        if ( ! $this->_required($this->required_fields, $options))
        {
            return FALSE;
        }
        
        $this->_set_editable_fields($this->table);
        
        $this->_validate_options_exist($options);
        $default = array(
            'created_at' => date($this->config->item('log_date_format')),
            'modified_at' => date($this->config->item('log_date_format'))
        );
        $options = $this->_default($default, $options);
        
        // qualification (make sure that we're not allowing the site to insert data that it shouldn't)
        foreach ($this->fields as $field) 
        {
            if (isset($options[$field]))
            {
                $this->db->set($field, $options[$field]);
            }
        }
        
        $query = $this->db->insert($this->table);
        if ($query)
        {
            return TRUE;
        }
    }
    
    function get($options = array())
    {
        // set an array for field querys and values
        // This allows gets with operators
        // $options = array('status >' => 5)
        $option_fields = array();
        foreach($options as $key => $value)
        {
            $parts = explode(' ', $key, 2);
            $field = isset($parts[0]) ? $parts[0] : '';
            $operator = isset($parts[1]) ? $parts[1] : '';
            $option_fields[$field]['query'] = $key;
            $option_fields[$field]['value'] = $value;
        }
        $defaults = array(
            'sort_direction' => 'asc'
        );
        $options = $this->_default($defaults, $options);
        
        $this->_set_editable_fields($this->table);
        
        foreach ($this->fields as $field)
        {
            if (isset($option_fields[$field]))
            {
                $this->db->where($option_fields[$field]['query'], $option_fields[$field]['value']);
            }
        }
        if (isset($options['limit']) && isset($options['offset']))
        {
            $this->db->limit($options['limit'], $options['offset']);
        }
        else
        {
            if (isset($options['limit']))
            {
                $this->db->limit($options['limit']);
            }
        }
        if (isset($options['sort_by']))
        {
            $this->db->order_by($options['sort_by'], $options['sort_direction']);
        }
        
        $query = $this->db->get($this->table);
        
        // if an id was specified we know you only are retrieving a single record so we return the object
        if (isset($options[$this->primary_key]))
        {
            return $query->row();
        }
        else
        {
            return $query;
        }
    }
    
    function update($options = array())
    {
        $required = array($this->primary_key);
        if ( ! $this->_required($required, $options))
        {
            return FALSE;
        }
        
        $this->_set_editable_fields($this->table);
        
        $this->_validate_options_exist($options);
        $default = array(
            'date_modified' => date($this->config->item('log_date_format'))
        );
        $options = $this->_default($default, $options);
        
        // qualification (make sure that we're not allowing the site to insert data that it shouldn't)
        foreach ($this->fields as $field) 
        {
            if (isset($options[$field]))
            {
                $this->db->set($field, $options[$field]);
            }
        }
                
        $this->db->where($this->primary_key, $options[$this->primary_key]);
        $this->db->update($this->table);
        return $this->db->affected_rows();
    }
    
    function delete($options = array())
    {
        $required = array($this->primary_key);
        if ( ! $this->_required($required, $options))
        {
            return FALSE;
        }
        
        $this->db->where($this->primary_key, $options[$this->primary_key]);
        return $this->db->delete($this->table);
    }
    
    
    /**
     * set editable fields in the table, if no fields are specified in the model, fields will be pulled dynamically from the table
     *
     * @return void
     */
    function _set_editable_fields()
    {
        if (empty($this->fields))
        {
            // pull the fields dynamically from the database
            $this->db->cache_on();
            $this->fields = $this->db->list_fields($this->table);
            $this->db->cache_off();
        }
    }
    
    /**
     * _required method returns false if the $data array does not contain all of the keys assigned by the $required array.
     *
     * @param array $required
     * @param array $data
     * @return bool
     */
    function _required($required, $data)
    {
        foreach ($required as $field)
        {
            if ( ! isset($data[$field]))
            {
                return FALSE;
            }
        }
        return TRUE;
    }
    
    /**
     * _default method combines the options array with a set of defaults giving the values in the options array priority.
     *
     * @param array $defaults
     * @param array $options
     * @return array
     */
    function _default($defaults, $options)
    {
        return array_merge($defaults, $options);
    }
}