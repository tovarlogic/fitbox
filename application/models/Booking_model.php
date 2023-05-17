<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Booking_model extends CI_Model
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
    private $cstTable = 'bs_calendar_settings';
    private $svstTable = 'bs_service_settings';

    private $mssTable = 'ms_settings';
    private $msTable = 'ms_memberships_services';
    private $muTable = 'ms_memberships_users';
    private $mucTable = 'ms_memberships_users_coupons';
    private $msgTable = 'ms_gateways';

    //VARs
    private $box_id = null;

    private $setting_tables = array('booking' => 'bs_settings', 'membership' => 'ms_settings', 'calendar' => 'bs_calendar_settings', 'service' => 'bs_service_settings');

    function __construct()
    {
        parent::__construct();
        
    }

    /* Function: set_box 
        set the box id in the private variable $box_id

        Parameters:
            $box_id(int) - the ID of the box
            
    */
    function set_box($box_id)
    {
        $this->box_id = $box_id;
    }

////////////////////////////////////////////////////////////////////
// Section: Cupones
////////////////////////////////////////////////////////////////////
   
    /**
     * function: getCoupons 
     * set the box id in the private variable $box_id
     *
     * Parameters:
     * @param  int $status null to fetch results with any status. If else value will return results only matching the provided status. Possible values as: 1 for active coupons and 0 for inactive
     * 
     * Returns:
     * @
     * @return array          empty if no results
     * @return object         empty array or ['id','box_id','title', dateFrom, dateTo, value, type, services, code, counter, status]
     *
     */
    function getCoupons($status = null)
    {      
        $this->db->where('box_id', $this->box_id);

        if($status != null) $this->db->where('status', $status);

        $this->db->order_by('dateFrom', 'ASC');
        $this->db->from($this->cTable);
        
        $result = $this->db->get()->result();

        return ($result) ? $result : array();
    }

    /* Function: getCoupon 

    */
    function getCoupon($id)
    {
        $result = $this->db->get_where($this->cTable, array('id' => $id, 'box_id' => $this->box_id))->row();

        return ($result) ? $result : false;
    }

    /* Function: create_coupon 

    */
    function create_coupon($params)
    {

        return $this->db->insert($this->cTable, $params);
    }

    /* Function: edit_coupon 

    */
    function edit_coupon($coupon_id, $params)
    {

        return $this->db->where('id', $coupon_id)->update($this->cTable, $params);
    }

    /* Function: delete_coupon 

    */
    function delete_coupon($coupon_id)
    {
        return $this->db->delete($this->cTable, array('id' => $coupon_id));
    }

    /* Function: checkCouponCode 

    */
    function checkCouponCode($code)
    {
        $this->db->from('bs_coupons')->where('code', $code);

        if($this->db->count_all_results() > 0)
            return true;
        else
            return false;
    }

    /* Function: checkCoupon 

    */
    function checkCoupon($couponCode, $serviceID)
    {
        $responce = array();

        $this->db->from('bs_coupons')->where('code', $couponCode);

        if($this->db->count_all_results() > 0)
        {
            $row = $this->db->get()->row();

            if($row->dateFrom <= date("Y-m-d") && $row->dateTo >= date("Y-m-d"))
            {
                $services = explode(",", $row->services);
                if(in_array($serviceID, $services))
                    $response = array("responce" => true, "value"=>$row->value,"type"=>$row->type );
                else
                   $response = array("responce" => false, "message"=>"This coupon not accepted fo this service" );
            }
            else
            {
                 $response = array("responce" =>false, "message"=>"This coupon out of date" );
            }
        }
        else
        {
            $response = array("responce" =>false, "message"=>"Coupon not found" );
        }
        
        return $response;
    }

    /* Function: getCouponByCode 

    */
    function getCouponByCode($code)
    {
       $result = $this->db->select('bs_coupons.id, bs_coupons.title, bs_coupons.dateFrom, bs_coupons.dateTo, bs_coupons.value, bs_coupons.type, bs_coupons.limit, bs_coupons.counter, bs_coupons.status, bs_coupons.code, bs_coupons.services, bs_coupons.box_id') 
        ->where('bs_coupons.box_id =', $this->box_id)
        ->where('bs_coupons.status =', 1)
        ->where('bs_coupons.code =', $code)
        ->get()->row();


        if ($result != NULL)
            return $result;
        else
        {
            return false;
        }

    }

    /* Function: getAvailableCoupons 
        returns active coupons with-in the timeframe that havent been consumed yet and available for a certain membership-user 
    */
    function getAvailableCoupons($mem_user_id)
    {
        $now = date ("Y-m-d H:i:s");

        $this->db->select('bs_coupons.id, bs_coupons.box_id, bs_coupons.status, bs_coupons.dateFrom, bs_coupons.dateTo, bs_coupons.limit, bs_coupons.counter, bs_coupons.value, bs_coupons.type, bs_coupons.title, bs_coupons.services')
        ->from($this->cTable)
        ->join($this->mucTable, 'ms_memberships_users_coupons.coupon_id = bs_coupons.id', 'left outer') 
        ->where("(`bs_coupons`.`box_id` = $this->box_id AND `bs_coupons`.`status` = 1 AND `bs_coupons`.`dateFrom` <= '".$now."' AND `bs_coupons`.`dateTo` >= '".$now."') AND (`bs_coupons`.`counter` < `bs_coupons`.`limit` OR `bs_coupons`.`limit` = 0 ) AND `bs_coupons`.`id` NOT IN (select `coupon_id` from `ms_memberships_users_coupons` where `ms_memberships_users_coupons`.`mu_id` = $mem_user_id)");

        $result = $this->db->get();
        if($result !== FALSE && $result->num_rows() > 0)
        {
         return $result->result();
        }
        else
        {
        return array();
        }
    }


    // checks if a certain coupon have already been used by a membership-user
    function isCouponUsed($coupon_id, $mem_user_id)
    {
       $params = array(
            'coupon_id' => $coupon_id,
            'mu_id' => $mem_user_id,
            'box_id' => $this->box_id
        );

       if($this->db->get_where($this->mucTable, $params)->row())
            return true;
        else
            return false;     
    }

    function registerCouponUse($coupon_id, $mem_user_id)
    {
        $params = array(
            'coupon_id' => $coupon_id,
            'mu_id' => $mem_user_id,
            'box_id' => $this->box_id
        );
        if ($this->db->insert($this->mucTable, $params))
        {
            $coupon = $this->getCoupon($coupon_id);
            if($this->edit_coupon($coupon_id, array('counter' => $coupon->counter+1))) 
                return true;
        }

        return false;
    }

    function deleteCouponUse($coupon_id, $mem_user_id)
    {
        $params = array(
            'coupon_id' => $coupon_id,
            'mu_id' => $mem_user_id,
            'box_id' => $this->box_id
        );

        return $this->db->delete($this->mucTable, $params);
    }


////////////////////////////////////////////////////////////////////
// Sección Servicios
////////////////////////////////////////////////////////////////////

    function getBoxServices($box_id) 
    // returns all the services (scheduled or not)
    {
        $boxServices = array();

        $this->db->select('id, name')
                ->from($this->sTable) 
                ->where('box_id =', $this->box_id);

        $result = $this->db->get()->result_array();

        $i=0;
        foreach ($result as $row)
        {
            $boxServices[$i]=$row['id'];
            $i++;
        }

        return $boxServices;
    }
    

    function get_services($active = null)
    {
      $this->db->select('id, name')
                ->from($this->sTable) 
                ->where('box_id =', $this->box_id);

        if($active != null) 
            $this->db->where('active', $active);
      
      $result = $this->db->get()->result_array();

      return ($result) ? $result : false;
    }

    function getServices($active = null)
    {       
        //$this->db->select('id, name, type, spots, interval, status');
        $this->db->where('box_id', $this->box_id);
        if($active != null) $this->db->where('active', $active);
        $this->db->order_by('active', 'DESC');
        $this->db->from($this->sTable);

        $result = $this->db->get()->result();

        return ($result) ? $result : false;
    }

    function getService($service_id, $field = null)
    {
        $result = $this->db->get_where($this->sTable, array('id' => $service_id, 'box_id' => $this->box_id))->row();

        if($result)
        {
            if ($field == null) {
                return $result;
            } else {
                return $result->$field;
            } 
        }
        
        return false;
    }

    function getDefaultService()
    {
        $result = $this->db->get_where($this->sTable, array('default' => 'y'))->row();

        if($result) 
            return $result['id'];
        else
        {
            $result = $this->db->select('id')->from($this->sTable)->order_by('id', 'ASC')->get()->row();
            return $result['id'];
        }
    }

    function createService($service_params, $schedule_params)
    {
        $result = $this->db->insert($this->sTable, $service_params); //insert service
        
        if($result)
        {
            $id = $this->db->insert_id();
            foreach ($schedule_params as $days) 
            {
                foreach ($days as $sch) 
                {
                    $sch['idService'] = $id;
                    $result = $this->db->insert($this->shTable, $sch); //insert service schedule
                }
            }
        }

        return $result;
    }

    function deleteService($service_id)
    {
        //check if there aren't reservations and transactios relative to the service
        $this->db->select('serviceID, boxID')->from($this->rTable)->where(array('serviceID' => $service_id, 'boxID' => $this->box_id));
        $result = $this->db->get()->num_rows();
        if($result !== FALSE && $result == 0)
        {
            //check there aren't coupons relative to the service
            $this->db->select('services, box_id')->from($this->cTable)->where(array('services' => $service_id, 'box_id' => $this->box_id));
            $result = $this->db->get()->num_rows();
            if($result !== FALSE && $result == 0)
            {
                //check there aren't memberships relative to the service
                $this->db->select('service_id, box_id')->from($this->msTable)->where(array('service_id' => $service_id, 'box_id' => $this->box_id));
                $result = $this->db->get()->num_rows();
                if($result !== FALSE && $result == 0)
                {
                    //delete service setttings
                    //if($this->db->delete($this->svstTable, array('serviceId' => $service_id, 'box_id' => $this->box_id)))
                    //{
                        //delete service scheadule
                        if($this->db->delete($this->shTable, array('idService' => $service_id, 'box_id' => $this->box_id)))
                        {
                            //delete service
                           if($this->db->delete($this->sTable, array('id' => $service_id, 'box_id' => $this->box_id))) 
                                return true;
                        }
                    //}
                }
            }
        }
        return false;
    }

    function getSchedule($service_id = null)
    {
        $this->db->where('box_id', $this->box_id);

        if ($service_id != null)
            $this->db->where('idService', $service_id);

        $this->db->order_by('startTime', 'ASC')
            ->from($this->shTable);

        $result = $this->db->get()->result_array();

        return ($result) ? $result : false;
    }

    function getScheduleData($service_id, $params, $field = null)
    {
        $this->db->where('box_id', $this->box_id)
            ->where('idService', $service_id)
            ->where($params)
            ->from($this->shTable);

        $result = $this->db->get()->row();

        if ($field == null) {
            return $result;
        } else {
            return $result->$field;
        }
    }

   function editService($service_id, $service_params, $schedule_params, $changes)
   {    
        $result = $this->db->update($this->sTable, $service_params, array('id' => $service_id, 'box_id' => $this->box_id)); //update service info
        //if(!$result) $this->db->error();

        if($changes == true)
        {
            $this->db->trans_start();
            $result = $this->db->delete($this->shTable, array('idService' => $service_id, 'box_id' => $this->box_id)); //delete current service schedule
            if($result)
            {
                foreach ($schedule_params as $days)
                {
                    foreach ($days as $sch) 
                    {
                        $result = $this->db->insert($this->shTable, $sch); //insert new service schedule
                    }
                }
            }
            $this->db->trans_complete();
        }
        
        return $result;
    }

    function getLastAvailable($date = null)
    //returns the DateTime of last available service in a particular weekday
    {
        $week_num =  date('w', strtotime($date));
        //if sunday
        if($week_num == 0) $week_num = 7;
        //services
        $this->db->where('box_id', $this->booking->box_id)->where('week_num', $week_num);
        $this->db->order_by('startTime', 'DESC');
        $this->db->from($this->shTable);

        $service = $this->db->get()->row();

        if($service)
        {
            $service = explode(":", $service->startTime); 
            $service = date("Y-m-d H:i", strtotime($date." ".$service[0].":".$service[1]));  
        }
        else
        {
            $service = FALSE;
        }
        //events pendiente

        return $service;
    }

    function getTimeRestrictions($serviceID = null, $userID = null)
    {
        if( $userID == null) $userID = $this->session->userdata('user_id');

        $this->db->select('ms.box_id , ms.service_id, m.available_from, m.available_to')
                    ->from('ms_memberships_services ms')
                    ->join('ms_memberships_users mu', 'ms.membership_id = mu.membership_id')
                    ->join('ms_memberships m', 'ms.membership_id = m.id')
                    ->where('mu.user_id', $userID)
                    ->where('ms.box_id', $this->box_id)
                    ->where("(mu.status = 'y' OR mu.status = 'g')");

        if($serviceID != null) $this->db->where('ms.service_id', $serviceID);

        $result = $this->db->get();

        if($result !== FALSE && $result->num_rows() > 0)
        {
            return $result->result();
        } 

        return FALSE;

    }

    function getSubscribedServices($userID = null)
    {
        if( $userID == null) $userID = $this->session->userdata('user_id');

        $this->db->select('ms.box_id , ms.service_id, mu.status')
                    ->from('ms_memberships_services ms')
                    ->join('ms_memberships_users mu', 'ms.membership_id = mu.membership_id')
                    ->where('mu.user_id', $userID)
                    ->where('ms.box_id', $this->box_id)
                    ->where("(mu.status = 'y' OR mu.status = 'g')");

        $result = $this->db->get()->result();
        $array = array();

        if($result)
        {
            foreach ($result as $res) {
                $array[] = $res->service_id;
            }
            return $array;
        }
        else
        {
            return FALSE;
        }
    }

    function isSubscribed($serviceID, $user_id = null)
    // checks if user is subscribed to a particular service
    {
        if($user_id == null) $user_id = $this->session->userdata('user_id');

        $services = $this->getSubscribedServices($user_id);

        if($services !== FALSE AND is_array($services))
        {
            foreach ($services as $key => $value) {
                if($value == $serviceID) 
                    return TRUE;
            }
        }

        return FALSE;
    }


    function getMembershipsService($userID, $serviceID)
    {
        $this->db->select('mu.box_id, mu.user_id, mss.service_id, m.period, mu.mem_expire, mu.status')
                    ->from('ms_memberships_users mu')
                    ->join('ms_memberships_services mss', 'mss.membership_id = mu.membership_id')
                    ->join('ms_memberships m', 'm.id = mu.membership_id')
                    ->where('mu.user_id', $userID)
                    ->where('mu.box_id', $this->box_id)
                    ->where('mss.service_id', $serviceID)
                    ->where("(mu.status = 'y' OR mu.status = 'g')");

        $result = $this->db->get();

        if($result !== FALSE && $result->num_rows() > 0)
        {
            return $result->result();
        } 

        return FALSE;
    }

////////////////////////////////////////////////////////////////////
// Sección Reservas
////////////////////////////////////////////////////////////////////

    function checkForAvailability($from, $to, $serviceID){
        $from = '2000-'.  substr($from, 5);
        $to = '2000-'.  substr($to, 5);
        $sSQL = "SELECT * FROM bs_schedule_days WHERE idService='{$serviceID}' AND (
            dateFrom <= '{$from}' AND dateTo >= '{$to}')";
        //print $sSQL;
        $res = mysqli_query($GLOBALS['link'],$sSQL);
        if(mysqli_num_rows($res)>0){
            return 1;
        }else{
            return 0;
        }
    }

        function getBoxSchedule2 ($box_id, $week_num, $serviceID = null) 
    // returns all the schedule (events and services) for a particular day of the week
    {
        $schedule = array();
        if ($week_num == 0) $week_num = 7;
        
        $this->db->where('box_id', $this->booking->box_id)->where('week_num', $week_num);
        if($serviceID != null) 
            $this->db->where('idService', $serviceID);
        $this->db->order_by('startTime', 'ASC');
        $this->db->from($this->shTable);

        $result = $this->db->get()->result();

        foreach ($result as $res) 
        {
            $res->startTime = explode(':', $res->startTime);            
            $m_from = $res->startTime[1];
            $h_from = $res->startTime[0];

            $res->endTime = explode(':', $res->endTime);            
            $m_to = $res->endTime[1];
            $h_to = $res->endTime[0];

            $schedule[] = array("id" => $res->idService, "startH" => $h_from, "startM" => $m_from, "endH" => $h_to, "endM" => $m_to);
        }

        return $schedule;
    }

    function getWebBookingsList($dateTimeToCheck, $box_id, $serviceID)
    //returns a list of ATHLETE names with reservations
    {
        $bookings = 0;

        $this->db->select('br.*, users.*')
                    ->from('bs_reservations br')
                    ->join('auth_users users', 'br.userID = users.id')
                    ->where('br.serviceID', $serviceID)
                    ->where('br.boxID', $this->box_id)
                    ->where('br.reserveDateFrom', $dateTimeToCheck)
                    ->where("(br.status = '1' OR br.status = '4')")
                    ->order_by('br.reserveDateFrom', 'ASC');

        $result = $this->db->get()->result();

        return $result;
    }

    function getWebBookings($dateTimeToCheck, $box_id, $serviceID)
    //returns the nº of bookings made by ATHLETEs
    {
        $bookings = 0;

        $this->db->select('br.*')
                    ->from('bs_reservations br')
                    ->where('br.serviceID', $serviceID)
                    ->where('br.boxID', $this->box_id)
                    ->where('br.reserveDateFrom', $dateTimeToCheck)
                    ->where("(br.status = '1' OR br.status = '4')")
                    ->order_by('br.reserveDateFrom', 'ASC');

        $result = $this->db->get()->result();

        foreach ($result as $row)
        {
            $bookings += $row->qty;
        }

        return $bookings;
    }


    function getManualBookings($dateTimeToCheck, $box_id, $serviceID)
    //returns the nº of manual bookings made by STAFF
    {
        $bookings = 0;

        $this->db->select('*')->from('bs_reserved_time')->where('serviceID', $serviceID)->where('reserveDateFrom', $dateTimeToCheck);
        $result = $this->db->get()->result();

        foreach ($result as $row)
        {
            $bookings += $row->qty;
        }

        return $bookings;
    }

    function getBooking($id, $field = null) 
    // returns reservation details by ID
    {        
        $this->db->select('br.*, bs.name AS sname')
                ->from('bs_reservations br')
                ->join('bs_services bs', 'bs.id = br.serviceID')
                ->where('br.serviceID', $id)
                ->where('reserveDateFrom', $dateTimeToCheck);
        
        $result = $this->db->get();

        if($result !== FALSE && $result->num_rows() > 0)
        {
            return $result;
        } 
        else 
        {
            return $result->row()->$field;
        }

    }

    function getBookingID($dateTime, $box_id, $serviceID, $user_id = null)
    //returns reservationID by dateTime and ServiceID
    {
        if($user_id == null) $user_id = $this->session->userdata('user_id');

        $this->db->select('br.serviceID, br.userID, br.boxID, br.reserveDateFrom, br.id')
                ->from('bs_reservations br')
                ->where('br.serviceID', $serviceID)
                ->where('br.userID', $user_id)
                ->where('reserveDateFrom', $dateTime);

        $result = $this->db->get();

        if($result !== FALSE && $result->num_rows() > 0)
            return $result->row()->id;
        else
            return FALSE;        
    }

    function isReservedByUser($dateTime, $box_id, $serviceID, $user_id = null)
    //checks if a ServiceID is already reserved by a User at a particular DateTime
    {
        if($user_id == null) $user_id = $this->session->userdata('user_id');

        $this->db->select('br.serviceID, br.muID, br.userID, br.boxID, br.reserveDateFrom')
                ->from('bs_reservations br')
                ->where('br.serviceID', $serviceID)
                ->where('br.userID', $user_id)
                ->where('reserveDateFrom', $dateTime);

        $result = $this->db->get();

        if($result !== FALSE && $result->num_rows() > 0)
            return TRUE;
        else
            return FALSE;
    }

    function setWebBooking($dateTime, $box_id, $serviceID, $user_id = null, $mu_id, $qtty = 1)
    {
        $duration = $this->getService($serviceID, 'interval');
        $reserveDateTo = date("Y-m-d h:i:s" , strtotime($dateTime." +".$duration." minutes"));
        if($user_id == null) $user_id = $this->session->userdata('user_id');

        $params = array(
            "serviceID" => $serviceID,
            "userID" => $user_id,
            "boxID" => $this->box_id,
            "muID" => $mu_id,
            "reserveDateFrom" => $dateTime,
            "reserveDateTo" => $reserveDateTo,
            "status" => '1',
            "qty" => $qtty,
            "reminder_sent" => 'n'
        );

        $this->db->insert($this->rTable, $params);

        if($this->db->affected_rows() > 0)
        {
            return TRUE;        
        }
        else
        {
             $err_msg = 'Error al tratar de insertar en Base de Datos.';
        }
        return $err_msg;
    }

    function setGuestBooking($dateTime, $box_id, $serviceID, $user_id = null, $mu_id, $qtty = 1)
    {
        $duration = $this->getService($serviceID, 'interval');
        $reserveDateTo = date("Y-m-d h:i:s" , strtotime($dateTime." +".$duration." minutes"));
        if($user_id == null) $user_id = $this->session->userdata('user_id');

        $params = array(
            "serviceID" => $serviceID,
            "userID" => $user_id,
            "boxID" => $this->box_id,
            "muID" => $mu_id,
            "reserveDateFrom" => $dateTime,
            "reserveDateTo" => $reserveDateTo,
            "status" => '1',
            "qty" => $qtty,
            "trial" => '1',
            "reminder_sent" => 'n'
        );

        $this->db->insert($this->rTable, $params);

        if($this->db->affected_rows() > 0)
        {
            return TRUE;        
        }
        else
        {
             $err_msg = 'Error al tratar de insertar en Base de Datos.';
             return $err_msg;
        }
    }

    function delWebBooking($dateTime, $box_id, $serviceID, $user_id = null)
    {
        if($user_id == null) $user_id = $this->session->userdata('user_id');

        $params = array(
            "serviceID" => $serviceID,
            "userID" => $user_id,
            "boxID" => $this->box_id,
            "reserveDateFrom" => $dateTime
        );

        $this->db->delete($this->rTable, $params);

        if($this->db->affected_rows() > 0)
        {
            return TRUE;        
        }
        else
        {
             $err_msg['error'][] = 'Error al tratar de borrar en Base de Datos.';
        }

        return $err_msg;
    }


    function endBonusMembership($mu_id)
    {
        $this->db->set('status', 'e')
                ->where('id', $mu_id)
                ->update('ms_memberships_users');
    }

    function getBoxEventsByDate($datetocheck, $box_id, $serviceID = null)
    // returns the events for a particular date
    {    
        $events = array();

        $this->db->from('bs_events')
                ->where('box_id', $box_id)
                ->where('eventDate <=', $datetocheck . " 23:59")
                ->where('recurringEndDate >=', $datetocheck)
                ->where('recurring', '1');

        if(!empty($serviceID)) $this->db->where('serviceID', $serviceID);

        $this->db->order_by('eventDate', 'ASC');
        
        if($this->db->count_all_results() > 0)
        {
            $result = $this->db->get()->result_array();

            foreach ($result as $row)
            {
               $startDate = date("Y-m-d", strtotime($row['eventDate'])); 
               $startTime = date("H:i",strtotime($row['eventDate']));
               $endDate = date("Y-m-d", strtotime($row['eventDateEnd']));
               $endTime = date("H:i",strtotime($row['eventDateEnd']));

               $interval = strtotime($row['eventDateEnd']) - strtotime($row['eventDate']);

               $st = $startDate;
               $j = 0;
               
                for ($i = $st; $i <= $row['recurringEndDate'] . " 23:59:59"; $i = date("Y-m-d", strtotime($i . " +{$row['repeate_interval']} {$row['repeate']}"))) {
                    //print $i;
                    $reserveDateFrom = $i;
                    $reserveDateTo = date("Y-m-d" , strtotime("$i +$interval seconds"));
                    
                   if (strtotime($datetocheck)<=strtotime($reserveDateTo) && strtotime($datetocheck) >= strtotime($reserveDateFrom)) {
                        $row->eventDate = "$reserveDateFrom $startTime";
                        $row->eventDateEnd = "$reserveDateTo $endTime";
                        $events[] = array("event" => $row, "qty" => $this->getSpotsLeftForEvent($row['id'], $reserveDateFrom));
                    }
                    //$i=$b;
                    $j++;
                    if($j>1000){
                        $message = "error to match iterations 'function getEventsByDate 
                             from=$datetocheck
                             serviceID=$serviceID'
                             startDate=$startDate";
                        _error_log($message);
                        break;
                    }
                } 
            }  
        }

        $this->db->from('bs_events')
                ->where('box_id', $box_id)
                ->where('eventDate <=', $datetocheck . " 23:59")
                ->where('eventDateEnd >=', $datetocheck . " 00:00")
                ->where('recurring', '0');

        if(!empty($serviceID)) $this->db->where('serviceID', $serviceID);

        $this->db->order_by('eventDate', 'ASC');

        $result = $this->db->get();
        if($result !== FALSE && $result->num_rows() > 0)
        {
            $result = $result->result_array();

            foreach ($result as $row)
            {
               $events[] = array("event"=>$row, "qty"=>$this->getSpotsLeftForEvent($row['id']));
            }
        }
        return $events;
    }

    function getEventRecurringSpots($eventID, $spacesAvl)
    {
        $text = "";
        $spaces = array();

        $this->db->from('bs_reservations')->where('eventID', $eventID)->order_by('date', 'DESC');
        $result = $this->db->get()->result();

        foreach ($result as $row)
        {
            isset($spaces[$row->date]) ? $spaces[$row->date] = $spaces[$row->date] + $row->qty : $spaces[$row->date] = $row->qty;
        }

        foreach($spaces as $k=>$v)
        {
            $text.= getDateFormat($k). ": <b>".($spacesAvl-$v)."</b> ".SYL_LEFT." <b>".$spacesAvl."</b> ".SYL_TOTAL."<br/>";
        }

        return $text;
    }

    function getSpotsLeftForEvent($id, $date=null) 
    {
        $this->db->select('payment_required, spaces')->from('bs_events')->where('id', $id);
        $result = $this->db->get()->row();

        $space = $result->spaces;
        //if($rr["payment_required"]=="1"){ $status = "4";} else { $status = "1"; }

        $this->db->select_sum('qty', 'num')->from('bs_reservations')->where('eventID', $id)->where("(status = '1' OR status = '4')");

        if(!empty($date)) $this->db->where('date', date("Y-m-d", strtotime($date)));

        $result = $this->db->get()->row();
       
        return $space - $result->num;
    }

    function getMaxBooking($serviceID) 
    {
        $this->db->select('qtty')->from('bs_services')->where('id', $serviceID);
        $result = $this->db->get()->row();

        return $result->allow_times;
    }

    function getMinBooking($serviceID) 
    {
        $this->db->select('allow_times_min')->from('bs_services')->where('id', $serviceID);
        $result = $this->db->get()->row();

        return $result->allow_times_min;
    }


    function getInfoByReservID($reservID) 
    {
        $t = array();
        
        $this->db->from('bs_reservations')->where('id', $serviceID);
        $result = $this->db->get()->row();

        $t[0] = $result->name;
        $t[1] = $result->email;
        $t[2] = $result->qty;
        $t[3] = $result->serviceID;
        $t[4] = $result->date;
        
        return $t;
    }

    /* Function: getUserMemberships 
        get memberships of a single user.

        Parameters:
            $active(bool) - null to return all memberships (present and past). if TRUE will only return active memberships
            $serviceID(int) - null to return all memberships if else will return memberships containing that particular service.

        Returns: 
            FALSE - if no memberships or
            $memberships - array['user_id'(int), 
                                 'mu_id'(int) => array['user_id'(int), 
                                                        'membership_id'(int), 
                                                        'mu_id'(int), 
                                                        'payment_method'(int), 
                                                        'status'(char), 
                                                        'created_on'(dateTime), 
                                                        'mem_expire'(date), 
                                                        'period'(char), 
                                                        'active'(int), 
                                                        'available_from', 
                                                        'available_to', 
                                                        'max_reservations', 
                                                        'services_quota' => array['service_id' => (int)]
                                                    ]
                                ]
    */
    function getUserMemberships($userID, $box_id, $active = null, $serviceID = null)
    {
        $this->db->select('mu.user_id, mu.membership_id, mu.id as mu_id, mu.payment_method, mu.status, mu.created_on, mu.mem_expire, m.period, m.active, m.available_from, m.available_to, m.max_reservations')
                ->from('ms_memberships_users mu') 
                ->join('ms_memberships_services ms', 'ms.membership_id = mu.membership_id') 
                ->join('ms_memberships m', 'm.id = mu.membership_id')
                ->where('mu.user_id =', $userID)
                ->where('mu.box_id =', $box_id)
                ->group_by('mu.membership_id');

        if($active === TRUE)
            $this->db->where("(mu.status = 'y' OR mu.status = 'g')");

        if($serviceID != null)
            $this->db->where('ms.service_id', $serviceID);

        $result = $this->db->get();

        if($result != FALSE AND $result->num_rows() > 0)
        {
            $memberships = array();
            $result = $result->result_array();

            foreach ($result as $rst) {
                $memberships['user_id'] = $rst['user_id'];
                $memberships[$rst['mu_id']] = $rst;

                $this->db->select('service_id, qtty')
                        ->from('ms_memberships_services ms') 
                        ->where('ms.membership_id =', $rst['membership_id']);

                $result2 = $this->db->get();

                if($result2 != FALSE AND $result2->num_rows() > 0)
                {
                    $result2 = $result2->result_array();

                    foreach ($result2 as $rst2) 
                    {
                        $memberships[$rst['mu_id']]['services_quota'][$rst2['service_id']] = $rst2['qtty'];
                    }
                }
            }

            return $memberships;
        }

        return FALSE;
    }


    function getUserBonusQuotas($userID, $date = null)
    //get max quota for every service that user is suscribed on BONUS memberships (period = D)
    {
        if( $date == null) $date = date("Y-m-d");
        
        $quotas = array();

        $this->db->select('m.period, mu.user_id, mu.status, mu.box_id, mu.membership_id, ms.service_id, ms.qtty')
                ->from('ms_memberships_users mu') 
                ->join('ms_memberships_services ms', 'ms.membership_id = mu.membership_id') 
                ->join('ms_memberships m', 'm.id = mu.membership_id') 
                ->where('m.period =', 'D')
                ->where('mu.user_id =', $userID)
                ->where('mu.box_id =', $this->box_id)
                ->where('mu.status =', 'y')
                ->where('mu.mem_expire >= ', $date);

        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $quotas[$row->membership_id][$row->service_id] = ($row->service_quota_left == 0)? 99 : $row->service_quota_left;
                $quotas[$row->membership_id]['max'] = ($row->max_quota_left == 0)? 99 : $row->max_quota_left;
            }
        }

        return $quotas;
    }

    function getUserQuotas($userID, $date = null)
    //get weekly quota for every service that user is suscribed
    {
        $grace_period = $this->getSettingItem('membership', 'grace_period');
        if( $date == null) $date = date("Y-m-d");

        $max_date = date("Y-m-d", strtotime($date." -".$grace_period. "days"));
        
        $quotas = array();

        $this->db->select('m.period, mu.user_id, mu.status, mu.box_id, mu.membership_id, ms.service_id, ms.qtty')
                ->from('ms_memberships_users mu') 
                ->join('ms_memberships_services ms', 'ms.membership_id = mu.membership_id') 
                ->join('ms_memberships m', 'm.id = mu.membership_id') 
                ->where('m.period !=', 'D')
                ->where('mu.user_id =', $userID)
                ->where('mu.box_id =', $this->box_id);
                $this->db->group_start();
                $this->db->where('mu.status =', 'y');
                $this->db->or_where('mu.status =', 'g');
                $this->db->group_end();
                $this->db->where('mu.mem_expire >= ', $max_date);

        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $quotas[$row->membership_id][$row->service_id] = ($row->qtty == 0)? 99 : $row->qtty;
                $max_reservations = $this->db->select('max_reservations')->from('ms_memberships')->where('id =', $row->membership_id)->get()->row()->max_reservations; 
                $quotas[$row->membership_id]['combined'] = ($max_reservations == 0)? 99 : $max_reservations;
            }
        }

        return $quotas;
    }

    function getUserBookings($from, $to, $box_id, $userID = null)
    //returns the nº of bookings of a certain user to a particular service within a time frame
    {
        if( $userID == null) $userID = $this->session->userdata('user_id');
        
        $from = $from." 00:00:00";
        $to = $to." 23:59:59";

        $bookings = array();

        //web bookings
        $this->db->select('br.qty, br.serviceID, br.muID, br.reserveDateFrom')
                    ->from('bs_reservations br')
                    ->where('br.userID', $userID)
                    ->where('br.boxID', $box_id)
                    ->where('br.reserveDateFrom >=', $from)
                    ->where('br.reserveDateFrom <=', $to)
                    ->where("(br.status = '1' OR br.status = '4')");

        $result = $this->db->get()->result();

        foreach ($result as $row)
        {
            $bookings[$row->muID][$row->serviceID] += $row->qty;
            $bookings[$row->muID]['total'] += $row->qty;
            $bookings[$row->muID]['reservations'][] = $row->reserveDateFrom;
        }

        //staff bookings (not implemented yet, and probably wont 4ever)
        // $this->db->select('*')
        //         ->from('bs_reserved_time')
        //         ->where('serviceID', $serviceID)
        //         ->where('userID', $userID)
        //         ->where('reserveDateFrom >=', $from)
        //         ->where('reserveDateFrom <=', $to);

        // $result = $this->db->get()->result();

        // foreach ($result as $row)
        // {
        //     $bookings[$row->serviceID] += $row->qty;
        // }
        
        return $bookings;
    }

    function getMembershipUserBookings($muID, $from, $to)
    {        
        $from = $from." 00:00:00";
        $to = $to." 23:59:59";

        $bookings = array();

        //web bookings
        $this->db->select('br.qty, br.serviceID, br.muID, br.reserveDateFrom')
                    ->from('bs_reservations br')
                    ->where('br.muID', $muID)
                    ->where('br.reserveDateFrom >=', $from)
                    ->where('br.reserveDateFrom <=', $to)
                    ->where("(br.status = '1' OR br.status = '4')");

        $result = $this->db->get()->result();

        foreach ($result as $row)
        {
            $bookings[$row->muID][$row->serviceID] += $row->qty;
            $bookings[$row->muID]['total'] += $row->qty;
            $bookings[$row->muID]['reservations'][] = $row->reserveDateFrom;
        }

        //staff bookings (not implemented yet, and probably wont 4ever)
        // $this->db->select('*')
        //         ->from('bs_reserved_time')
        //         ->where('serviceID', $serviceID)
        //         ->where('userID', $userID)
        //         ->where('reserveDateFrom >=', $from)
        //         ->where('reserveDateFrom <=', $to);

        // $result = $this->db->get()->result();

        // foreach ($result as $row)
        // {
        //     $bookings[$row->serviceID] += $row->qty;
        // }
        
        return $bookings;
    }

    function checkForUserReserv($from, $to, $serviceID) 
    {
        $qty = 0;
        $sSQL = "SELECT br.* FROM `bs_reservations br`
                    WHERE br.serviceID='{$serviceID}' AND (
                    (br.reserveDateFrom < '{$to}' AND br.reserveDateTo >= '{$to}') OR
                    (br.reserveDateTo > '{$from}' AND br.reserveDateFrom <= '{$from}') OR
                    (br.reserveDateFrom <= '{$from}' AND br.reserveDateTo >= '{$to}') OR
                    (br.reserveDateFrom >= '{$from}' AND br.reserveDateTo <= '{$to}'))
                    AND (br.status='1' OR br.status='4')  
                    ORDER BY br.reserveDateFrom ASC";
        $query = $this->db->query($sSQL);

        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $qty += $row->qty;
            }
            return $qty;
        }
        else 
            return false; 
    }

    function checkSpotsForTimeInterval($serviceID, $from, $to, $qty, $id)
    {
        $spots = $this->getService($serviceID,'spaces_available');

        $this->db->select_sum('qty', 'num')
                ->from('bs_reservations')
                ->where('id <>', $id)
                ->where('reserveDateFrom', $from)
                ->where('reserveDateTo', $to);

        $result = $this->db->get()->row();

        return $spots - $result->num;
    }

    function getPricePerSpot($serviceID) 
    {
        $this->db->select('spot_price')->from('bs_services')->where('id', $serviceID);
        $result = $this->db->get()->row();

        return $result->spot_price;
    }

    function getMaxQtyEvent($id) 
    {
        $this->db->select('max_qty')->from('bs_events')->where('id', $id);
        $result = $this->db->get()->row();

        return $result->max_qty;
    }

    function getActiveSuscribedClients($date, $time, $serviceID)
    //returns list of clients able to reserve a particular service on a particular date
    {
        $grace_period = $this->getSettingItem('membership', 'grace_period');
        $max_date = date("Y-m-d", strtotime($date." -".$grace_period. "days"));

        $this->db->select('auth_users.id, CONCAT(auth_users.first_name, " ", auth_users.last_name) AS name')
                ->group_by('auth_users.id') //to avoid duplicate values
                ->from('auth_users') 
                ->join('auth_users_groups', 'auth_users.id = auth_users_groups.user_id') 
                ->join('ms_memberships_users mu', 'auth_users.id = mu.user_id') 
                ->join('ms_memberships_services ms', 'mu.membership_id = ms.membership_id')
                ->where('auth_users_groups.box_id =', $this->box_id)
                ->where('ms.service_id =', $serviceID);
                $this->db->group_start();
                $this->db->where('mu.status =', 'y');
                $this->db->or_where('mu.status =', 'g');
                $this->db->group_end();
                $this->db->where('mu.mem_expire >= ', $max_date);


        return $this->db->get()->result_array();
    }

////////////////////////////////////////////////////////////////////
// Sección SETTINGS
////////////////////////////////////////////////////////////////////
    function getSettings($subset)
    {
        $result = $this->db->where('box_id', $this->box_id)->from($this->setting_tables[$subset])->get();

        if($result !== FALSE && $result->num_rows() > 0)
        {
            $result = $result->row_array();
            unset($result['id'], $result['box_id']);
            if($subset == 'calendar')
            {
                unset($result['cal_code']);
            }
        }
        else
        {
            $result = $this->config->item($subset.'_default', 'booking_lib');
        }
        
        return $result;
    }

    function getSettingItem($subset, $field, $box_id = null)
    {
        $box_id = ($box_id == null)? $this->box_id : $box_id;
        
        $result = $this->db->where('box_id', $box_id)->from($this->setting_tables[$subset])->get();
                
        if($result !== FALSE && $result->num_rows() > 0)
        {
            $result = $result->row_array();
            if(isset($result[$field])) 
                return $result[$field];

        }

        $result = $this->config->item($subset.'_default', 'booking');
        if(isset($result[$field]))
        {
            return $result[$field];
        }

        return FALSE;
    }

    function editSettings($subset, $box_id, $params)
    {      
        $db_table = null;

        //1º comparar con settings default
        //si igual ver si hay settings en la DB y borrarlos
        //si diferentes ver si hay settings en la DB
        //si si editar
        //si no insertar
        //
        $default_settings = $this->config->item($subset.'_default', 'booking_lib');

        $changes = FALSE;
        foreach ($default_settings as $key => $value) 
        {
            if($params[$key] != $value) $changes = TRUE;
        }

        $custom_settings = FALSE;
        $result = $this->db->get_where($this->setting_tables[$subset], array('box_id' => $this->box_id));
        if($result !== FALSE && $result->num_rows() > 0)
        {
            $custom_settings = TRUE;
        }

        if($custom_settings === TRUE AND $changes === FALSE)
        {
            $result = $this->db->delete($this->setting_tables[$subset], array('box_id' => $this->box_id)); //delete config info
        }
        else if($custom_settings === TRUE AND $changes === TRUE)
        {
            $result = $this->db->update($this->setting_tables[$subset], $params, array('box_id' => $this->box_id)); //update config info
        }
        else if($custom_settings === FALSE AND $changes === TRUE)
        {
            $params['box_id'] = $this->box_id;
            $result = $this->db->insert($this->setting_tables[$subset], $params); //insert config info
        }

        return ($result) ? TRUE : FALSE;
    }

/////////////////////////////////////////
////  Sección SETTINGS (DEPRECATED)
/////////////////////////////////////////

    function getOption($option) 
    {
        $option = trim($option);

        if (empty($option)) return false;

        $option = addslashes($option);
        
        $this->db->where('option_name', $option);
        $this->db->from($this->stTable);
        
        $result = $this->db->get()->row()->option_value;

        return ($result) ? $result : false;
    }

    function setOption($option_name, $option_value) {

        $option_name = trim($option_name);

        if ($this->getOption($option_name) !== false)
            return false;

        if (is_string($option_value))
            $option_value = trim($option_value);
        if (is_array($option_value))
            $option_value = serialize($option_value);

        $sql = "INSERT INTO  bs_settings (option_name,option_value) VALUES ('{$option_name}','{$option_value}')";
        $res = mysqli_query($this->link,$sql);

        return true;
    }

    function updateOption($option_name, $option_value) {

        $option_name = trim($option_name);

        if ($this->getOption($option_name) === false) {
            if (setOption($option_name, $option_value))
                return true;
        }

        if (is_string($option_value))
            $option_value = trim($option_value);
        if (is_array($option_value))
            $option_value = serialize($option_value);

        $sql = "UPDATE bs_settings SET option_value='{$option_value}' WHERE  option_name='{$option_name}'";
        $res = mysqli_query($this->link,$sql);

        return true;
    }

    function deleteOption($option_name) {

        $option_name = trim($option_name);

        if ($this->getOption($option_name) === false) {
            return false;
        }

        if (!checkCoreOptions($option_name)) {
            $sql = "DELETE FROM bs_settings WHERE option_name='{$option_name}'";
            $res = mysqli_query($this->link,$sql);
            return true;
        } else {
            return false;
        }
    }


}   
?>
