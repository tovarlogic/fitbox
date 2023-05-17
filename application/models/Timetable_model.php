<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Timetable_model extends CI_Model
{
    //DATABASE
    private $eTable = 'bs_events';
    private $tTable = 'bs_transactions';
    private $cTable = 'bs_coupons';
    private $sTable = 'bs_services';
    private $rTable = 'bs_reservations';
    private $rtTable = 'bs_reserved_time';
    private $rtiTable = 'bs_reserved_time_items';
    private $shTable = 'bs_schedule';
    private $shdTable = 'bs_schedule_days';
    private $stTable = 'bs_settings';


    //VARs
    private $box_id = 0;
    private $options = null;

////////////////////////////////////////////////////
    function __construct()
    {
        parent::__construct();
    }

    function set_box($box_id)
    {
        $this->box_id = $box_id;
    }

    function set_options() {
        $this->options = $this->db->where('box_id =', $this->box_id)->get('timetable_config')->result_array();
    }

    function get_option( $option, $default = false ) {
        if($options == null)
            $this->set_options();
        else
        {
            if(isset($options[$option]))
                return $options[$option];
            else
                return $default;
        }
    }

    function get_services()
    {
        $this->db->select('name')
            ->from($this->sTable) 
            ->where('box_id =', $this->box_id);

        if($result = $this->db->get()->result())
        {
            $serv = '';
            foreach ($result as $res) {
                $serv .= $res->name.',';
            }
            $serv = rtrim($serv, ","); 

            return $serv;
        }

        return false;
    }

    function get_activities($event = null)
    {
        $this->db->select('t1.week_num as menu_order, t1.tooltip, t1.text1 as before_hour_text, t1.text2 as after_hour_text, t2.name as event_title, t2.color_bg, t2.color_hover')
                ->select("TIME_FORMAT(t1.startTime, '%H.%i') as start, TIME_FORMAT(t1.endTime, '%H.%i') as end", FALSE)
                ->select('0 as available_places, 0 as booking_count, 0 as current_user_booking_id, 0 as event_hours_id, 0 as event_id, 0 as post_name')
                ->from($this->shTable.' as t1')
                ->join($this->sTable.' as t2', 't2.id = t1.idService') 
                ->where('t1.box_id =', $this->box_id)
                ->where('t2.active', 1);

        if($event!=null) $this->db->where_in('t2.name', $event);

        $this->db->order_by('t1.week_num ASC, t1.startTime ASC, t1.endTime ASC');

        $result = $this->db->get()->result();


        return $result;
    }


    function get_events($event = null, $type = null)
    {
        $result = false;
        if ($event != null && $type != null)
        {
            $query = "SELECT ID, post_name, post_parent, post_type FROM wp_posts WHERE post_name IN ". $event ." AND post_type IN ". $type;
            $query = "SELECT ID, post_name, post_parent, post_type FROM wp_posts";

            $result = $this->db->query($query)->result_array();
        }
        return ($result) ? $result : false; 
    }

    function get_posts($params)
    {

        return $this->db->get_where('wp_posts',$params)->result_array();
    }

    function get_min_max_hours()
    {
        $query = "SELECT min(TIME_FORMAT(t1.startTime, '%H.%i')) AS min, max(REPLACE(TIME_FORMAT(t1.endTime, '%H.%i'), '00.00', '24.00')) AS max FROM bs_schedule AS t1";

        return $this->db->query($query)->row();
    }

    function get_weekdays()
    {   
        
        $day1 = new stdClass();
        $day2 = new stdClass();
        $day3 = new stdClass();
        $day4 = new stdClass();
        $day5 = new stdClass();
        $day6 = new stdClass();
        $day7 = new stdClass();

        $day1->title = 'Lunes';
        $day1->menu_order = 1;

        $day2->title = 'Martes';
        $day2->menu_order = 2;

        $day3->title = 'Miércoles';
        $day3->menu_order = 3;

        $day4->title = 'Jueves';
        $day4->menu_order = 4;

        $day5->title = 'Viernes';
        $day5->menu_order = 5;

        $day6->title = 'Sabado';
        $day6->menu_order = 6;

        $day7->title = 'Domingo';
        $day7->menu_order = 7;

        $weekdays = array($day1, $day2, $day3, $day4, $day5, $day6, $day7);

        return $weekdays;

    }

    function get_post_meta($id, $var, $bool)
    {
        return $this->db->get_where('wp_postmeta',array('post_id' => $id, 'meta_key' => $var))->row()->meta_value;
    }


}   
?>