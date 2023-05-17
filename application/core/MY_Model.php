<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
 
class MY_Model extends CI_Model
{ 
    function __construct()
    {
        parent::__construct();
    }

    function insert($table, $params)
    {
        $this->db->insert($table, $params);

        $error = $this->db->error();
        if (empty($error['code'])) 
            return $this->db->insert_id();
        
        return false;
    }

    function update($ref, $table, $params)
    {
        if(!is_array($ref))
            return false;

        foreach ($ref as $key => $value) 
        {
            $this->db->where($key, $value);
        }
        
        $this->db->update($table, $params);

        $error = $this->db->error();
        if (empty($error['code'])) 
            return true;
        
        return false;
    }

    
    function delete($id, $table)
    {
        $this->db->delete($table, array('id' => $id));

        $error = $this->db->error();
        if (empty($error['code'])) 
            return true;
        
        return false;
    }

    function getRow($table, $fields = '*', $params = null, $order_by = null, $result_type = null, $join = null)
    {
        return $this->getRows($table, $fields, $params, $order_by, $result_type, TRUE, $join);
    }

    /**
     * Function: getRows
     *
     * $table - name of table to get from
     * $fields - array('field1','field2') or (string) 'field1, field2' or or in case of a join then array('table1' => (array) $fields)
     * $params - array('status' => array('y','p')) or array('status' => 'p') or in case of a join then array('table1' => (array) $params)
     * @param  [type] $order_by [description]
     * @param  [type] $result_type - by default (null) is database object. If true then array
     * @param  [type] $row [description]
     * $join - array('auth_users_groups' => array( 'ref_table' => 'auth_users', 'ref_field' => 'id', 'new_field' => 'user_id'));
     *
     * @return [type] [description]
     *
     * @todo  change to getRows($table, $fields = '*', $join = null, $params = null, $order_by = null, $result_type = null, $row = null)
     */
    function getRows($table, $fields = '*', $params = null, $order_by = null, $result_type = null, $row = null, $join = null)
    {   
        if(is_array($fields))
        {
            $str = '';
            $i == 0;
            foreach ($fields as $key => $value) 
            {
                if(is_array($join))
                {
                    foreach ($value as $k => $v) 
                    {
                        if($i>0) $str .= ', ';
                        $str .= $key.'.'.$v;
                        $i++; 
                    }
                }
                else
                {
                    if($i>0) $str .= ', ';
                    $str .= $value;
                    $i++; 
                }
                
            }
            $this->db->select($str);
        }
        else
        {
            $this->db->select($fields);
        }
        $this->db->from($table);

        if(is_array($join))
        {              
            foreach ($join as $key => $value) 
            {
                if(isset($value['join_type']))
                    $this->db->join($key, $value['ref_table'].".".$value['ref_field']." = ".$key.".".$value['new_field'], $value['join_type']);
                else
                    $this->db->join($key, $value['ref_table'].".".$value['ref_field']." = ".$key.".".$value['new_field']);
            }
        }


        if($params != null && is_array($params))
        {
            foreach ($params as $key => $value) 
            {
                if(is_array($value))
                {
                    //if array('table1' => array('status' => array('y','p')))
                    if(is_array($join))
                    {
                        foreach ($value as $k => $v) 
                        {
                            if(is_array($v))
                            {
                                $this->db->group_start();
                                    $i = 0;
                                    foreach ($v as $p ) 
                                    {
                                        if($i == 0) $this->db->where($key.".".$k, $p); 
                                        else $this->db->or_where($key.".".$k, $p); 
                                        $i++;
                                    }
                                $this->db->group_end(); 
                            }
                            else
                            {
                                $this->db->where($key.".".$k, $v);
                            }
                        }
                    }
                    else
                    {
                        //if array('status' => array('y','p'))
                        $this->db->group_start();
                        $i = 0;
                        foreach ($value as $p ) 
                        {
                            if($i == 0) $this->db->where($table.".".$key, $p); 
                            else $this->db->or_where($table.".".$key, $p); 
                            $i++;
                        }
                        $this->db->group_end(); 
                    }
                    
                }
                else
                {
                    // if array('status' => 'y')
                    $this->db->where($table.".".$key, $value);
                }
            }
            if($order_by != null AND is_array($order_by))
                $this->db->order_by($order_by[0], $order_by[1]);
        }
        else if($params != null) 
                $this->db->where($params);

        $query  = $this->db->get(); 


        if($query->num_rows() > 0)
        {
            if($row === TRUE)
                $result = ($result_type == null)? $query->row() : $query->row_array();
            else
                $result = ($result_type == null)? $query->result() : $query->result_array();

            return $result;
        }
     
        return FALSE;
    }   
     
}

?>
