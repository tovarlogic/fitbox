<?php

if (!defined('BASEPATH')) 
    exit('No direct script access allowed');

class Logs_model extends CI_Model
{

	private $luTable = 'log_users';
    private $lmTable = 'log_members';
    private $ltcTable = 'log_total_clients';

    function __construct()
    {
        parent::__construct();
    }

    function update_log($log, $action, $box_id, $user_id = null)
    {
     	$table = 'log_'.$log;
      	$data = array(
        	'user_id' => $user_id,
        	'box_id' => $box_id,
        	'action' => $action);

      	$this->db->insert($table, $data);
      	return ($this->db->affected_rows() > 0 )? TRUE : FALSE;
    }

   /*
   * get_users_log
   *
   * counts the number of each user type of current box
   *
   * @param $action (string) accepted values ['new,'cancel', 'banned']
   * @param $months (string) accepted values []
   * @return (array)
   */
    function get_users_log($box_id, $action, $months = null){
      	$to = date('Y-m-t 23:59:59');

	      if ($months == null) //get count of current month
	      {
	        $from = date('Y-m-01 00:00:00'); 
	        

	        return $this->db->from($this->luTable)
	                      ->where('box_id', $box_id)
	                      ->where('action', $action)
	                      ->where('date >', $from)
	                      ->where('date <', $to)
	                      ->count_all_results();
	      }
	      else //get log of every month
	      {
	          $i = 1;
	          while ($i <= $months) {
	            $from =  new DateTime();
	            $op = $months-$i;
	            $from -> modify('- '.$op.' months');  
	            $from = $from -> format('Y-m-01 00:00:00'); 


	            $to =  new DateTime();
	            $to -> modify('-'.$op.' months'); 
	            $to = $to -> format('Y-m-t 23:59:59'); 

	            $count[$op] = $this->db->from($this->luTable)
	                        ->where('box_id', $box_id)
	                        ->where('action', $action)
	                        ->where('date >', $from)
	                        ->where('date <', $to)
	                        ->count_all_results();
	            $i++;
	          }
	          return $count;
	      }
    }

    function get_members_log($box_id, $action, $months = null){
	      if ($months == null)  //get log of current month
	      {
	        $from = date('Y-m-01 00:00:00'); 
	        $to = date('Y-m-t 23:59:59');

	        $this->db->from($this->lmTable)->where('box_id', $box_id);

	            if(!is_array($action))
	                $this->db->where('action', $action);
	            else
	            	$this->db->where($action);

	            $this->db->where('date >=', $from)->where('date <=', $to);

	        return $this->db->count_all_results();
	      }
	      else //get log of every month
	      {
	          	$from =  new DateTime();
	      		$from->modify('-'.$months.' months'); 
	      		$to =  new DateTime();
	      		$to->modify('-'.$months.' months');

				for ($i = 12; $i > 0 ; $i--) 
				{
					$this->db->from($this->lmTable)->where('box_id', $box_id);

					if(!is_array($action))
					    $this->db->where('action', $action);
					else
						$this->db->where($action);

					$this->db->where('date >=', $from->format('Y-m-01 00:00:00'));
	            	$this->db->where('date <=', $to->format('Y-m-t 23:59:59'));

					$count[$from->format('n')-1] = $this->db->count_all_results();

					$from->modify('+1 months');  
					$to->modify('+1 months'); 
				}
				return $count;
	      }
    }

    function get_total_clients_log($box_id, $params, $months = null){
	      if ($months == null)  //get log of current month
	      {
	        $from = date('Y-m-01 00:00:00'); 
	        $to = date('Y-m-t 23:59:59');

	        $this->db->from($this->ltcTable)->where('box_id', $box_id);

	            if(is_array($params))
	            {
	            	$x = 0;
	            	
	            	foreach ($params as $key => $param ) 
	            	{
	            		$this->db->group_start();
	            		foreach ($param as $value) 
	            		{
		            		if($x == 0) $this->db->where($key, $value);
		            		else $this->db->or_where($key, $value);
		            		$x++;
		            	}
	            		$this->db->group_end();
	            	}
	            	
	            }

	            $this->db->where('date >=', $from)->where('date <=', $to);

	        $result = $this->db->get();
	        return ($result !== FALSE && $result->num_rows() > 0)? $result->row()->total : 0;
	      }
	      else //get log of every month
	      {
	      		$from =  new DateTime();
	      		$from->modify('-'.$months.' months'); 
	      		$to =  new DateTime();
	      		$to->modify('-'.$months.' months'); 

				for ($i = 12; $i > 0 ; $i--) 
				{
					$this->db->from($this->ltcTable)->where('box_id', $box_id);

					if(is_array($params))
					{
						$x = 0;
						
						foreach ($params as $key => $param ) 
						{
							$this->db->group_start();
							foreach ($param as $value) 
							{
					    		if($x == 0) $this->db->where($key, $value);
					    		else $this->db->or_where($key, $value);
					    		$x++;
					    	}
							$this->db->group_end();
						}
						
					}
		            
		            $this->db->where('date >=', $from->format('Y-m-01 00:00:00'));
		            $this->db->where('date <=', $to->format('Y-m-t 23:59:59'));

		            $result = $this->db->get();
		            $count[$from->format('n')-1] = ($result !== FALSE && $result->num_rows() > 0)? $result->row()->total : 0;

		            $from->modify('+1 months');  
					$to->modify('+1 months'); 
	          }
	          return $count;
	      }
    }

    function set_users_log($user_id, $box_id, $action)
    {
      	$this->db->insert($this->luTable, array('user_id' => $user_id, 
                                              	'box_id' => $box_id, 
                                              	'action' => $action));
    }

    function set_members_log($user_id, $box_id, $membership_id, $action)
    {
      	$this->db->insert($this->lmTable, array('user_id' => $user_id, 
                                              	'box_id' => $box_id, 
                                              	'membership_id' => $membership_id, 
                                              	'action' => $action));
    }

    function set_total_clients_log($box_id, $type, $total)
    {
      	$this->db->insert($this->ltcTable, array('box_id' => $box_id, 
                                              	'type' => $type,
                                              	'total' => $total));
    }
}