<?php

// --------------------------------------------------------------------

if ( ! function_exists('uuid'))
{
	function uuid($prefix = null, $extra = null)
	{
		return time_uid($prefix).psrandom_uid($extra);
	}
}


if ( ! function_exists('time_uid'))
{
	function time_uid($prefix = null)
	{
		echo $id = $prefix.uniqid();
		
	}
}

if ( ! function_exists('psrandom_uid'))
{
	function psrandom_uid($lenght = 13) 
	{
	    // uniqid gives 13 chars, but you could adjust it to your needs.
	    if (function_exists("random_bytes")) {
	        $bytes = random_bytes(ceil($lenght / 2));
	    } elseif (function_exists("openssl_random_pseudo_bytes")) {
	        
	    } else {
	        throw new Exception("no cryptographically secure random function available");
	    }
	    return substr(bin2hex($bytes), 0, $lenght);
	}
}

if ( ! function_exists('mysql_uuid'))
{
	function mysql_uuid()
	{
		return $this->db->select('UUID() as uuid')->get()->row()->uuid;
	}
}
