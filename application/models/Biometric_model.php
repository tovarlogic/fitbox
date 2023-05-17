<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Biometric_model extends CI_Model
{
	//DATABASE
    private $bpTable = 'biometrics_bp';
    private $hTable = 'biometrics_height';
    private $wTable = 'biometrics_weight';
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

    function registerBP($params)
    {
        return $this->db->insert($this->bpTable, $params);
    }

    function registerHeight($params)
    {
        return $this->db->insert($this->hTable, $params);
    }

    function registerWeight($params)
    {
        return $this->db->insert($this->wTable, $params);
    }

    function getBP($months = null)
    {
      	$this->db->select('id, systolic, diastolic, pulse, timestamp')->from($this->bpTable)->where('user_id =', $this->user_id);
      	if($months != null)
	    {
	        $fromDate = date("Y-m-d 00:00:00", strtotime("-".$months." months"));
	        $this->db->where('timestamp >=', $fromDate);

	    }
        return $this->db->order_by('timestamp', 'DESC')->get()->result();
    }

    function getBPHistory($months = null)
    {
        $result = $this->getBP($months);

        $bp_history = '[';
        $i = 0;
        foreach ($result as $res) {
            if($i > 0) $coma = ','; else $coma = '';
            $mean = ($res->systolic - $res->diastolic)/2;
            $med = $res->systolic - $mean;
            $time_ms = strtotime($res->timestamp) * 1000;
            $bp_history .= $coma.'['.$time_ms.','.$med.','.$mean.']';
            $i++;
        }
        $bp_history .= ']';

        return $bp_history;
    }

    function getHeight($months = null)
    {
      	$this->db->select('id, height, date')->from($this->hTable)->where('user_id =', $this->user_id);
      	if($months != null)
	    {
	        $fromDate = date("Y-m-d", strtotime("-".$months." months"));
	        $this->db->where('date >=', $fromDate);

	    }
        return $this->db->order_by('date', 'DESC')->get()->result();
    }

    function getWeight($months = null)
    {
      	$this->db->select('id, weight, fat, date')->from($this->wTable)->where('user_id =', $this->user_id);
      	if($months != null)
	    {
	        $fromDate = date("Y-m-d", strtotime("-".$months." months"));
	        $this->db->where('date >=', $fromDate);

	    }
        return $this->db->order_by('date', 'DESC')->get()->result();
    }

    function getWeightHistory($months = null)
    {
        $result = $this->getWeight($months);

        $weight_history = array();
        $weight_history['weight'] = '[';
        $weight_history['fat'] = '[';
        $i = 0;
        foreach ($result as $res) {
            if($i > 0) $coma = ','; else $coma = '';
            $time_ms = strtotime($res->date) * 1000;
            $weight_history['weight'] .= $coma.'['.$time_ms.','.$res->weight.']';
            $weight_history['fat'] .= $coma.'['.$time_ms.','.$res->fat.']';
            $i++;
        }
        $weight_history['weight'] .= ']';
        $weight_history['fat'] .= ']';

        return $weight_history;
    }

    function getLastWeight()
    {
        $this->db->select('weight, fat')->from($this->wTable)->where('user_id =', $this->user_id);
        return $this->db->order_by('date', 'ASC')->get()->row(1);
    }

    function getLastHeight()
    {
        $this->db->select('height')->from($this->hTable)->where('user_id =', $this->user_id);
        return $this->db->order_by('date', 'ASC')->get()->row(1);
    }

    function getBPById($id)
    {
        return $this->db->select('id, systolic, diastolic, pulse, timestamp')->from($this->bpTable)->where('id =', $id)->where('user_id =', $this->user_id)->get()->row();
    }    

    function getWeightById($id)
    {
        return $this->db->select('id, weight, fat, date')->from($this->wTable)->where('id =', $id)->where('user_id =', $this->user_id)->get()->row();
    }

    function getHeightById($id)
    {
        return $this->db->select('id, height, date')->from($this->hTable)->where('id =', $id)->where('user_id =', $this->user_id)->get()->row();
    }

    function updateBP($biometric_id, $params)
    {

        return $this->db->where('id', $biometric_id)->update($this->bpTable, $params);
    }

    function updateHeight($biometric_id, $params)
    {

        return $this->db->where('id', $biometric_id)->update($this->hTable, $params);
    }

    function updateWeight($biometric_id, $params)
    {

        return $this->db->where('id', $biometric_id)->update($this->wTable, $params);
    }

    function deleteWeight($id)
    {
        $this->db->delete($this->wTable, array('id' => $id));
    }

    function deleteHeight($id)
    {
        $this->db->delete($this->hTable, array('id' => $id));
    }

    function deleteBP($id)
    {
        $this->db->delete($this->bpTable, array('id' => $id));
    }

}

?>