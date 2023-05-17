<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fitbox_model extends MY_Model 
{	
	private $bTable = 'boxes';
    private $sTable = 'settings';

	function __construct()
    {
        parent::__construct();
    }

    function getBoxes($params = null)
    { 
      return $this->db->get_where($this->bTable, $params)->result();
    }


}

?>