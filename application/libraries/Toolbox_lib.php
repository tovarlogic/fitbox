<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Toolbox_lib
{

	public function __construct()
	{
		//$this->booking = & get_instance();
		
		//$this->booking->lang->load('booking');
	}

	public function generate_list($from, $to, $ini = FALSE)
    {
        if ($ini === FALSE) 
        	$array = array();
        else
        	$array = array('' => '-');

        for($i=$from; $i<$to+1; $i++)
        { 
            $array[$i] =  $i;
        }
        return $array;
    }

}

?>