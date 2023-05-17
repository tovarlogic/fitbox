<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Dates_lib
{

	public function __construct()
	{
		//$this->booking = & get_instance();
		
		//$this->booking->lang->load('booking');
	}

	public function birth_to_age($date)
	{
		$birthday = new DateTime($date);
	    $now = new DateTime();

	    $years = $now->diff($birthday);

	    return $years->y;
	}


}

?>