<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** 
 * Booking Library for CodeIgniter 3.x 
 * 
 * Library for Gocardless payment gateway. 
 * It helps to integrate gocardless_pro (the oficial library for PHP) 
 * in a CodeIgniter application. 
 * 
 * @package     CodeIgniter  
 * @version     3.0 
 *
 * @package     Fitbox
 * @category    Libraries 
 * @version     0.2 2019-08
 * @author kinsay <kinsay@gmail.com>
 *
 * Library based on BOOKINGWIZZ 5.5
 * @link
 *
 * @todo  04-2020 -> initialize library with set_box and remove box_id from function args
 * @todo  04-2020 -> MULTI_DAY BOOKING
 */ 

/**
 * Class: booking_lib
 * used to manage membership and single events bookings, depending on the client services purchased or active subscriptions (memberships)
 */
class booking_lib 
{
	public $weekdays = Array('','Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');
	public $weekdays_short = Array('','Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', );
	public $months = Array('January','February','March','April','May','June','July','August','September','October','November','December');
	public $months_short = Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

    private $link = null;

    private $error = array();

	public function __construct()
	{
		$this->CI =& get_instance();
        $this->CI->load->model('booking_model', 'booking');
        $this->CI->lang->load('booking');
        $this->CI->config->load('booking', TRUE);

        if(ENVIRONMENT == 'development')
        {
            $password = "";
            $username = "root";
        }
        else
        {
            $password = 'eNgj`z(Y5!=wP;?F';
            $username = 'fitbox';
        }

        $db_host = 'localhost'; //hostname
        $db_user = $username; // username
        $db_password = $password; // password
        $db_name = 'fitbox'; //database name

        define("January","Enero");
        define("February","Febrero");
        define("March","Marzo");
        define("April","Abril");
        define("May","Mayo");
        define("June","Junio");
        define("July","Julio");
        define("August","Agosto");
        define("September","Septiembre");
        define("October","Octubre");
        define("November","Noviembre");
        define("December","Diciembre");

        define("Jan","Ene");
        define("Feb","Feb");
        define("Mar","Mar");
        define("Apr","Abr");
        define("Jun","Jun");
        define("Jul","Jul");
        define("Aug","Ago");
        define("Sep","Sep");
        define("Oct","Oct");
        define("Nov","Nov");
        define("Dec","Dic");

        define("Mon","Lun");
        define("Tue","Mar");
        define("Wed","Mi&eacute;");
        define("Thu","Jue");
        define("Fri","Vie");
        define("Sat","S&aacute;b");
        define("Sun","Dom");
        
        define("Monday","Lunes");
        define("Tuesday","Martes");
        define("Wednesday","Mi&eacute;rcoles");
        define("Thursday","Jueves");
        define("Friday","Viernes");
        define("Saturday","S&aacute;bado");
        define("Sunday","Domingo");

        $this->link = mysqli_connect($db_host, $db_user, $db_password, $db_name);
	}

    /**
     * Function: _set_error
     * sets the global variable designed to store the error events of the library
     *
     * Parameters:
     * $message string
     * $code int
     * $this->error array
     */
    private function _set_error($message, $code = null)
    {
        $this->error[]['msg'] = $message;
        $this->error[]['code'] = $code;
    }

    /**
     * Function: get_error
     * returns the array of errors stored.
     */
    public function get_error()
    {
        return $this->error;
    }

    /**
     * Function: set_box
     *
     */
    function set_box($box_id)
    {
        $this->box_id = $box_id;
    }
    
    /**
     * Function: checkForBoxEvents
     * returns TRUE if there are any events active during the time frame requested.
     *
     * Parameters:
     * $from date
     * date $to date 
     * $box_id 
     * $serviceID int - is used to limit the search. if null will check ALL services.
     *
     * @return bool
     */
    function checkForBoxEvents($from, $to, $box_id, $serviceID = null)  
    {
        $date = date("Y-m-d",strtotime($from));
        $eventsList = $this->$this->CI->booking->getBoxEventsByDate($date,$box_id, $serviceID);
        $to = strtotime($to.":00");
        $from = strtotime($from.":00");

        if (count($eventsList) > 0) {
            foreach($eventsList as $event) {

                $event['eventDate'] = strtotime($event['event']['eventDate']);
                $event['eventDateEnd'] = strtotime($event['event']['eventDateEnd']);

                if (($event['eventDate'] < $to AND $event['eventDateEnd'] >= $to) OR
                        ($event['eventDateEnd'] > $from AND $event['eventDate'] <= $from) OR
                        ($event['eventDate'] <= $from AND $event['eventDateEnd'] >= $to) OR
                        ($event['eventDate'] >= $from AND $event['eventDateEnd'] <= $to)) {
                    return true;
                }                
            }
        } 
        return false;
    }

    /**
     * Function: getBoxServicesByDate
     * returns the services available for a particular date
     *
     * Parameters:
     * $datetocheck date
     * $box_id 
     * $serviceID - optional
     *
     * @return [type] [description]
     */
    function getBoxServicesByDate($datetocheck, $box_id, $serviceID = null)
    // 
    {
        $services = array();

        if ($serviceID == null) 
            $boxServices = $this->CI->booking->getBoxServices($box_id); 
        else 
            $boxServices[0]['id'] = $serviceID;

        $numServices = count ($boxServices);

        for($x=0; $x<=$numServices-1; $x++){
            if($this->CI->booking->getService($boxServices[$x]['id'], 'type')=='t'){
                ######################  HOURLY BOOKING  #########################################
                $cur_spots = checkSpotsLeft($datetocheck, $box_id, $boxServices[$x]['id']);
                if ($cur_spots>0) { 
                    $services[]['id'] = $boxServices[$x]['id']; 
                }
            }else{
                ######################  MULTI_DAY BOOKING  #########################################

            }
        }
        return $services;
    }

    /**
     * Function: checkIfReservedByUser
     * returns TRUE if the user has already reserved that service at that dateTime
     *
     * @todo  to be implemented. check if still necessary
     */
    function checkIfReservedByUser($dateTime, $box_id, $serviceID)
    //
    {
        $userID = $this->CI->session->userdata('user_id');
        
        return false;
    }


    
    /////////////////////////////////////////////
    //// SECTION: booking wizz functions
    ////////////////////////////////////////////

    function getEventStartEndDate($id,$date,$type='text')
    {
        $text = "";
        $q = "SELECT * FROM bs_events WHERE id='" . $id . "'";
        $res = mysqli_query($this->link,$q);
        if (mysqli_num_rows($res) < 1)
            return false;
        $rr = mysqli_fetch_assoc($res);
        $date = empty($date)?$rr["eventDate"]:$date;
        $startTime = date("H:i", strtotime($rr["eventDate"]));
        $startDate = date("Y-m-d", strtotime($date));
        $endTime = date("H:i", strtotime($rr["eventDateEnd"]));
        $endDate = date("Y-m-d", strtotime($date));
        if (date("d-m-Y", strtotime($rr["eventDate"])) == date("d-m-Y", strtotime($rr["eventDateEnd"]))) {
            $text  .= getDateFormat($date) . " " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDate"])) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDateEnd"]));
            
        } else {
            $interval = strtotime(_date($rr["eventDateEnd"]))-strtotime(_date($rr["eventDate"]));
            $start = getDateFormat(date("Y-m-d",strtotime($date)))." ".date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDate"]));
            
            
            $end = getDateFormat(date("Y-m-d",strtotime($date ." ".date("H:i", strtotime($rr["eventDate"]))." +$interval seconds")))." ".date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDateEnd"]));
            
            $endDate = date("Y-m-d",strtotime("$date  +$interval seconds"));
            $text = "From: $start  To: $end";
        }
        if($type=='text'){
            return $text;
        }else{
            return array("from"=>"$startDate $startTime","to"=>"$endDate $endTime",'fromDate'=>$startDate,'toDate'=>$endDate);
        }
    }

    function getEventInfo($id) 
    {
        $t = array();
        $q = "SELECT * FROM bs_events WHERE id='" . $id . "'";
        $res = mysqli_query($this->link,$q);
        if (mysqli_num_rows($res) < 1)
            return false;
        $rr = mysqli_fetch_assoc($res);
        
        $t = $rr;
        $t[0] = $rr["title"];
        $t[1] = $rr["description"];
        if (date("d-m-Y", strtotime($rr["eventDate"])) == date("d-m-Y", strtotime($rr["eventDateEnd"]))) {
            $t[2] = getDateFormat($rr["eventDate"]) . " " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDate"])) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDateEnd"]));
        } else {
            $t[2] = "from: " . getDateFormat($rr["eventDate"]) . " " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDate"]));
            $t[2].=" to: " . getDateFormat($rr["eventDateEnd"]) . " " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDateEnd"]));
        }
        $t[3] = $rr["payment_required"];
        $t[4] = $rr["entryFee"];
        $t[5] = $rr["payment_method"];
        $t[6] = $rr["serviceID"];
        $t[7] = date("Y-m-d", strtotime($rr["eventDate"]));

        return $t;
    }

    function getOrderSummery($orderId, $date = null) 
    {
        $info = '';

        bw_apply_filter("pre_order_summery", $info, $orderId);

        $info .="<div class='orderSummery'>";

        $currency = $this->CI->booking->getOption('currency');
        $currencyPos = $this->CI->booking->getOption('currency_position');

        $paid = false;
        $deposit = 1;

        $orderInfo = $this->CI->booking->getBooking($orderId);

        $serviceSettings = $this->CI->booking->getService($orderInfo['serviceID']);
        
        if (!empty($orderInfo['eventID'])) {
            $eventInfo = getEventInfo($orderInfo['eventID']);
            $eventDates =  getEventStartEndDate($orderInfo['eventID'] ,$date,"array");
            $info .="<h2>".ORDER_EVENT_INF."</h2>";
            $info .="<ul class='summery'>";
            $info .="<li><label>".EVENT_TTL.":</label>{$eventInfo['title']}</li>";
            $info .="<li><label>".EVENT_DISCRP.":</label><span>".nl2br($eventInfo['description'])."</span></li>";
            $info .="<li><label>".TXT_EVENT_START.":</label>" . getDateFormat($eventDates['from']) . " "._time($eventDates['from'])."</li>";
            $info .="<li><label>".TXT_EVENT_ENDS.":</label>" . getDateFormat($eventDates['to']) . " "._time($eventDates['to'])."</li>";
            $info .="<li><label>".ORDER_BOOKING_QTY.":</label>{$orderInfo['qty']}</li>";
            $info .="</ul><div style='clear:both'></div>";
            if ($eventInfo['payment_required'] == 1&& $eventInfo['entryFee']>0) {
                $paid = true;
            }
            $deposit = $eventInfo['deposit'];
        } else {

            if($serviceSettings['type']=='t'){
                $info .="<h2>".ORDER_BOOKING_INF."</h2>";
                $info .="<ul class='summery'>";
                $booking_times = '';
                $booking_date = '';
                $bookint_times_count = 0;
                $sSQL = "SELECT * FROM bs_reservations_items WHERE reservationID='" . $orderId . "' ORDER BY reserveDateFrom ASC";
                $result = mysqli_query($this->link,$sSQL) or die("err: " . mysqli_error() . $sSQL);
                while ($row = mysqli_fetch_assoc($result)) {

                    $booking_times .=_time($row["reserveDateFrom"]) . " - " .
                                    _time($row["reserveDateTo"]) . "<br/>";
                    $booking_date = getDateFormat($row["reserveDateTo"]);
                    $bookint_times_count++;
                }
                $info .="<li><label>".ORDER_BOOKING_DATE.":</label>{$booking_date}</li>";
                $info .="<li><label>".ORDER_BOOKING_TIME.":</label><span>{$booking_times}</span></li>";
                $info .="<li><label>".ORDER_BOOKING_QTY.":</label>{$orderInfo['qty']}</li>";
                $price = $this->CI->booking->getService($orderInfo['serviceID'], 'spot_price');
                $deposit = $this->CI->booking->getService($orderInfo['serviceID'],'deposit');
                if($price > 0 ){

                    $price = number_format($price,2);
                    $paid = true;
                    $info .="<li><label>".PRICE.":</label>".($currencyPos=='b'?$currency:"")." {$price} ".($currencyPos=='a'?$currency:"")."</li>";

                }
                $info .="</ul><div style='clear:both'></div>";
            }else{
                $info .="<h2>".ORDER_BOOKING_INF."</h2>";
                $info .="<ul class='summery'>";
                /*$booking_times = '';
                $booking_date = '';
                $bookint_times_count = 0;*/
                $sSQL = "SELECT * FROM bs_reservations_items WHERE reservationID='" . $orderId . "' ORDER BY reserveDateFrom ASC";
                $result = mysqli_query($this->link,$sSQL) or die("err: " . mysqli_error() . $sSQL);
                $bookInfo = mysqli_fetch_assoc($result);
                $bookingSummery = _checkForAvailability($bookInfo['reserveDateFrom'],$bookInfo['reserveDateTo'],$orderInfo['serviceID']);
                $deposit = $this->CI->booking->getService($orderInfo['serviceID'],'deposit');
                $paid = $bookingSummery['totalPrice']>0?true:false;
                foreach($bookingSummery['info'] as $k=>$v){
                    $info .="<li><label>".ORDER_DATE_FROM.":</label>".  getDateFormat($v['from'])."</li>";
                    $info .="<li><label>".ORDER_DATE_TO.":</label>".  getDateFormat($v['to'])."</li>";
                    $info .="<li><label>".ORDER_DAYS.":</label>".(getDaysInterval($v['from'], $v['to']))."</li>";
                    $price = number_format($v['_price'],2);

                    $info .="<li><label>".PRICE.":</label>".($currencyPos=='b'?"$currency ":"")."{$price} ".($currencyPos=='a'?$currency:"")."</li>";
                    $info .="<li>&nbsp;</li>";
                }
                $info .="</ul><div style='clear:both'></div>";
            }
        }
        if ($paid) {
            $orderPymentInfo = get_payment_info($orderId);
            $amount = number_format($orderPymentInfo['amount'], 2);
            $subTotal = number_format($orderPymentInfo['subAmount'], 2);
            $_subTotal = number_format($orderPymentInfo['_subAmount'], 2);
            $tax = number_format($orderPymentInfo['tax'], 2);
            $taxRate = $orderPymentInfo['taxRate'];
            $discount = $orderPymentInfo['discount'];

            $info .="<h2>".ORDER_SUMMERY."</h2>";
            $info .="<ul class='summery'>";

            if (!empty($tax) && $tax>0) {
                $info.="<li><label>".ORDER_SUBTOTAL.":</label>".getCurrencyText( $subTotal )." ".(!empty($discount)?"(<del>".getCurrencyText($_subTotal)."</del>)":"")."</li>";
                if(!empty($discount)){
                    
                    $info.="<li><label>".ORDER_DISCOUNT.":</label>".($discount)." </li>";
                }
                $info.="<li><label>".ORDER_TAX.":</label>".getCurrencyText( $tax )." ( $taxRate % )</li>";
                $info.="<li class='total'><label>".ORDER_TOTAL.":</label>".getCurrencyText( $amount )."</li>";
                if($deposit<1){
                    $info.="<li class='_total'><label>".ORDER_TO_PAY.":</label>".getCurrencyText(number_format($orderPymentInfo['amount']*$deposit,2) )." <small>( ".($deposit*100)."% )</small> </li>";
                }
            } else {
                if(!empty($discount)){
                    $info.="<li><label>".ORDER_SUBTOTAL.":</label>". getCurrencyText($_subTotal)." </li>";
                    $info.="<li><label>".ORDER_DISCOUNT.":</label>".($discount)." </li>";
                }
                $info.="<li class='total'><label>".ORDER_TOTAL.":</label>".getCurrencyText($amount )."</li>";
                if($deposit<1){
                    $info.="<li class='_total'><label>".ORDER_TO_PAY.":</label>".getCurrencyText(number_format($orderPymentInfo['amount']*$deposit,2) )." <small>( ".($deposit*100)."% )</small> </li>";
                }
            }
            $info .="</ul>";
        }


        $info.="<div style='clear:both'></div></div>";
        //print $info;
        return bw_apply_filter("order_summery", $info, $orderId);
    }

    function getAdminMail() {
        return $this->CI->booking->getOption("email");
    }

    function getTimeMode() {

        return $this->CI->booking->getOption("time_mode");
    }

    function getAdminPaypal() {

        $tt = array();
        $tt[0] = $this->CI->booking->getOption("pemail");
        $tt[1] = $this->CI->booking->getOption("pcurrency");
        return $tt;
    }


    function checkSpotsLeft($date, $box_id, $serviceID) {
        $spots = 0;
        $serviceSettings = $this->CI->booking->getService($boxServices[0]['id']); //assumes that all services have same settings
        $show_multiple_spaces = $serviceSettings['show_multiple_spaces']; //check option for multiple timeBooking
        $availebleSpaces = $show_multiple_spaces ? $serviceSettings['spaces_available'] : 1;
        $timeBefore = $serviceSettings['time_before']; 

        ##########################################################################################################################
        ##########################################################################################################################
        # PREPARE AVAILABILITY ARRAY 
        $schedule = $this->getBoxSchedule($box_id, $date, $serviceID);
        $availabilityArr = $schedule['availability'];
        $events = $schedule['events'];
        $admins = $schedule['admins'];
        $users = $schedule['users'];
        $n = $schedule['countItems'];
        $currTime = strtotime(date("Y-m-d H:i"));
        

        foreach ($availabilityArr as $k => $v) { //$v= date  (  2010-10-05 )
            foreach ($v as $kk => $vv) { //$vv = time slot in above date 
                //echo $vv;
                $spotTimeStart = strtotime(date("Y-m-d", strtotime($k)) . " $vv:00 -5 minutes"); //5-minutes befo select interval in past
                
                $spotTimeStart = $timeBefore > 0?strtotime(_date($date) . " $vv:00  -{$timeBefore} hours"):$spotTimeStart;
               
                if (isset($events[$k]) && in_array($vv, $events[$k])) {
                    
                } elseif (isset($admins[$k]) && array_key_exists($vv, $admins[$k])) {

                    //current timestamp
                    $spacesBooked = $admins[$k][$vv];
                    $spacesAllowed = $availebleSpaces - $spacesBooked;

                    //timestamp on start time interval

                    if (isset($users[$k]) && array_key_exists($vv, $users[$k])) {

                        //current timestamp
                        $spacesBooked = $users[$k][$vv];
                        $spacesAllowed = $spacesAllowed - $spacesBooked;

                        //timestamp on start time interval
                    }
                    if ($spotTimeStart <= $currTime) {
                        //this interval passed already.
                    } elseif ($spacesAllowed >= 1) {
                        $spots+=$spacesAllowed;
                    }
                }elseif (isset($users[$k]) && array_key_exists($vv, $users[$k])) {

                    //current timestamp
                    $spacesBooked = $users[$k][$vv];
                    $spacesAllowed = $availebleSpaces - $spacesBooked;
                    
                    //timestamp on start time interval
                   
                    if ($spotTimeStart < $currTime) {
                        //this interval passed already.
                    } elseif ($spacesAllowed >= 1) {
                        $spots+=$spacesAllowed;
                    }
                } else {
                    if($spotTimeStart > $currTime){
                        $spots+=$availebleSpaces;
                    }
                }
            }
        }

        return $spots;
    }

    function checkForEvents($from, $to, $serviceID) {
        
        $date = date("Y-m-d",strtotime($from));
        $eventsList = getEventsByDate($date,$serviceID);
        $to = strtotime($to.":00");
        $from = strtotime($from.":00");
        //print "$from $to<br>";
        //bw_dump($event);
        if (count($eventsList) > 0) {
            foreach($eventsList as $event) {

                $event['eventDate'] = strtotime($event['event']['eventDate']);
                $event['eventDateEnd'] = strtotime($event['event']['eventDateEnd']);

                if (($event['eventDate'] < $to AND $event['eventDateEnd'] >= $to) OR
                        ($event['eventDateEnd'] > $from AND $event['eventDate'] <= $from) OR
                        ($event['eventDate'] <= $from AND $event['eventDateEnd'] >= $to) OR
                        ($event['eventDate'] >= $from AND $event['eventDateEnd'] <= $to)) {
                    return true;
                }
                    
                
            }
        } 
        return false;

    }

    function getAdminReserveData($from, $to, $serviceID){
        $qty = 0;
        $qtyTmp = 0;
        $data = array();
        $recurring = array();
        $date = date("Y-m-d", strtotime($from)); //print $date;
        $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=1 AND reserveDateTo>='{$to}'"; //print $sSQL;
        $res = mysqli_query($this->link,$sSQL);
        if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {

                $startDate = date("Y-m-d", strtotime($row['reserveDateFrom']));
                $endDate = date("Y-m-d", strtotime($row['reserveDateTo']));
                $startTime = date("H:i", strtotime($row['reserveDateFrom']));
                $endTime = date("H:i", strtotime($row['reserveDateTo']));
                $st = $startDate;
                $en = $endDate;
                $j = 0;
                for ($i = $st; $i <= $date . " 23:59:59"; $i = date("Y-m-d", strtotime($i . " +{$row['repeate_interval']} {$row['repeate']}"))) {
                    //print $i;
                    $reserveDateFrom = $date . " " . $startTime;
                    $reserveDateTo = $date . " " . $endTime;
                    if ($date == date("Y-m-d", strtotime($i))) {

                        if (($reserveDateFrom < $to AND $reserveDateTo >= $to) OR
                                ($reserveDateTo > $from AND $reserveDateFrom <= $from) OR
                                ($reserveDateFrom <= $from AND $reserveDateTo >= $to) OR
                                ($reserveDateFrom > $from AND $reserveDateTo <= $to)) {
                            $recurring[$row['qty']] = array("start" => $reserveDateFrom, "end" => $reserveDateTo);
                            $qtyTmp+=intval($row['qty']);
                            $data [$row['id']]['reason']=$row['reason'];
                            $data [$row['id']]['qty']=$qtyTmp;
                        }
                    }

                    //$i=$b;
                    $j++;
                    if($j>1000){
                        $message = "error to match iterations 'function checkForAdminReserv 
                             from=$from
                             to=$to
                             serviceID=$serviceID'";
                        _error_log($message);
                        break;
                    }
                }
            }
        }
        //bw_dump($recurring);
        //print $qtyTmp."-";
        $qty = $qtyTmp;
        $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=0 AND(
                    (reserveDateFrom < '{$to}' AND reserveDateTo >= '{$to}') OR
                    (reserveDateTo > '{$from}' AND reserveDateFrom <= '{$from}') OR
                    (reserveDateFrom <= '{$from}' AND reserveDateTo >= '{$to}') OR
                    (reserveDateFrom >= '{$from}' AND reserveDateTo <= '{$to}'))";
        //print $sSQL;
        $res = mysqli_query($this->link,$sSQL);
        if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                $qty =$row['qty'];
                $data [$row['id']]['reason']=$row['reason'];
                $data [$row['id']]['qty']=$qty;
            }
        } else {
            //return false;
        }

        return $data;
    }
    
    function checkForAdminReserv($from, $to, $serviceID) 
    {
        //print $from." - ".$to."<br>";
        $qty = 0;
        $qtyTmp = 0;
        $recurring = array();
        $date = date("Y-m-d", strtotime($from)); //print $date;
        $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=1 AND reserveDateTo>='{$to}'"; //print $sSQL;
        $res = mysqli_query($this->link,$sSQL);
        if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {

                $startDate = date("Y-m-d", strtotime($row['reserveDateFrom']));
                $endDate = date("Y-m-d", strtotime($row['reserveDateTo']));
                $startTime = date("H:i", strtotime($row['reserveDateFrom']));
                $endTime = date("H:i", strtotime($row['reserveDateTo']));
                $st = $startDate;
                $en = $endDate;
                $j = 0;
                for ($i = $st; $i <= $date . " 23:59:59"; $i = date("Y-m-d", strtotime($i . " +{$row['repeate_interval']} {$row['repeate']}"))) {
                    //print $i;
                    $reserveDateFrom = $date . " " . $startTime;
                    $reserveDateTo = $date . " " . $endTime;
                    if ($date == date("Y-m-d", strtotime($i))) {

                        if (($reserveDateFrom < $to AND $reserveDateTo >= $to) OR
                                ($reserveDateTo > $from AND $reserveDateFrom <= $from) OR
                                ($reserveDateFrom <= $from AND $reserveDateTo >= $to) OR
                                ($reserveDateFrom > $from AND $reserveDateTo <= $to)) {
                            $recurring[$row['qty']] = array("start" => $reserveDateFrom, "end" => $reserveDateTo);
                            $qtyTmp+=intval($row['qty']);
                        }
                    }

                    //$i=$b;
                    $j++;
                    if($j>1000){
                        $message = "error to match iterations 'function checkForAdminReserv 
                             from=$from
                             to=$to
                             serviceID=$serviceID'";
                        _error_log($message);
                        break;
                    }
                }
            }
        }
        //bw_dump($recurring);
        //print $qtyTmp."-";
        $qty = $qtyTmp;
        $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=0 AND(
                    (reserveDateFrom < '{$to}' AND reserveDateTo >= '{$to}') OR
                    (reserveDateTo > '{$from}' AND reserveDateFrom <= '{$from}') OR
                    (reserveDateFrom <= '{$from}' AND reserveDateTo >= '{$to}') OR
                    (reserveDateFrom >= '{$from}' AND reserveDateTo <= '{$to}'))";
        //print $sSQL;
        $res = mysqli_query($this->link,$sSQL);
        if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                $qty +=$row['qty'];
            }
        } else {
            //return false;
        }

        return $qty;
    }

    function getEventsByDate($datetocheck,$serviceID=null){
        $where = "";
        if(!empty($serviceID)){
            $where = " AND serviceID='{$serviceID}'";
        }
        $query = "SELECT * FROM bs_events WHERE eventDate <= '" . $datetocheck . " 23:59' AND recurringEndDate>={$datetocheck} $where AND recurring=1 ORDER BY eventDate ASC "; 
        $result = mysqli_query($this->link,$query);
        $events = array();
        if(mysqli_num_rows($result)>0){
            while($row = mysqli_fetch_assoc($result)){
               $startDate = date("Y-m-d", strtotime($row['eventDate'])); 
               $startTime = date("H:i",strtotime($row['eventDate']));
               $endDate = date("Y-m-d", strtotime($row['eventDateEnd']));
               $endTime = date("H:i",strtotime($row['eventDateEnd']));
               $interval = strtotime($row['eventDateEnd'])-strtotime($row['eventDate']);
               $st = $startDate;
               $j = 0;
               
                for ($i = $st; $i <= $row['recurringEndDate'] . " 23:59:59"; $i = date("Y-m-d", strtotime($i . " +{$row['repeate_interval']} {$row['repeate']}"))) {
                    //print $i;
                    $reserveDateFrom = $i;
                    $reserveDateTo = date("Y-m-d" , strtotime("$i +$interval seconds"));
                    
                   
                    if (strtotime($datetocheck)<=strtotime($reserveDateTo) && strtotime($datetocheck) >= strtotime($reserveDateFrom)) {
                        $row['eventDate']="$reserveDateFrom $startTime";
                        $row['eventDateEnd']="$reserveDateTo $endTime";
                        $events[]=array("event"=>$row,"qty"=>$this->CI->booking->getSpotsLeftForEvent($row['id'],$reserveDateFrom));
                        
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
        $query = "SELECT * FROM bs_events WHERE eventDate <= '" . $datetocheck . " 23:59' AND eventDateEnd >= '" . $datetocheck . " 00:00' $where  AND recurring=0 ORDER BY eventDate ASC ";
        $result = mysqli_query($this->link,$query);
        if(mysqli_num_rows($result)>0){
            while($row = mysqli_fetch_assoc($result)){
               $events[]=array("event"=>$row,"qty"=>$this->CI->booking->getSpotsLeftForEvent($row['id']));
            }
        }
        return $events;
    }

    function checkTimesIntervals($serviceID,$date,$from,$to){
        $responce = array("res" => true, "message" => "");
        $message = "";
        $availability = getScheduleService($serviceID, _date($date));
        //bw_dump($availability);
        $availableTimesFrom = $availability['availability'][_date($date)];
        if (is_array($availableTimesFrom)) {
            $_availableTimesFrom = array_flip($availableTimesFrom);
        } else {
            $message.="Start time '$from' out of availability. Check your input";
            $responce['res'] = false;
            $responce['message'] = $message;
            return $responce;
        }
        $availableTimesTo = array();
        $interval = $this->CI->booking->getService($serviceID,'interval');
        foreach ($availableTimesFrom as $k => $v) {
            $availableTimesTo[$k] = date("H:i", strtotime("2000-01-01 $v +$interval minutes"));
        }
        
        //bw_dump($availableTimesFrom);
        //bw_dump($availableTimesTo);
        if(!in_array($from, $availableTimesFrom)){
            $message.="Start time '$from' out of availability. Check your input";
            $responce['res']=false;
        }elseif($to!=$availableTimesTo[$_availableTimesFrom[$from]]){
            $message.="Incorrect end time for start time '$from', should be a '{$availableTimesTo[$_availableTimesFrom[$from]]}'. Check your input.";
            $responce['res']=false;
        }
        $responce['message'] = $message;
            return $responce;
    }

    function getBoxSchedule($box_id, $date, $serviceID = null) {
        $availabilityArr = array();
        $events = array();
        $admins = array();
        $users = array();

        if ($serviceID != null){
            $where ="AND idService='{$serviceID}'";
        }

        $dayOfWeek = date("w", strtotime($date));
        $sql = "SELECT * FROM bs_schedule
                WHERE box_id='{$box_id}' $where AND week_num='{$dayOfWeek}' ORDER BY startTime ASC";
        $res = mysqli_query($this->link,$sql) or die(mysqli_error() . "<br>" . $sql);
        $n = 0;
        while ($row = mysqli_fetch_assoc($res)) {
            //$schedule[]=array("start"=>$row['startTime'],"end"=>$row['endTime']);

            $st = date("Y-m-d H:i", strtotime($date . " +" . $row['startTime'] . " minutes"));
            //TODO 
            //for afternight bookings
            //$row['endTime'] = ($row['startTime']<$row['endTime'])?$row['endTime']+720:$row['endTime'];
            $et = date("Y-m-d H:i", strtotime($date . " +" . $row['endTime'] . " minutes"));
            $a = $st;

            //layout counter
            $int = $this->CI->booking->getService($row['idService'],'interval');
            $b = date("Y-m-d H:i", strtotime($a . " +" . $int . " minutes")); //default value for B is start time.
            $j = 0;
            for ($a = $st; $b <= $et; $b = date("Y-m-d H:i", strtotime($a . " +" . $int . " minutes"))) {
                //echo "a: ".$a." // "."b: ".$b."<br />";
                if (checkForBoxEvents($a, $b, $box_id, $idService)) {
                    $events[date("Y-m-d", strtotime($a))][] = date("H:i", strtotime($a));
                }
                $qtyAdminReservation = checkForAdminReserv($a, $b, $idService); //print "<br>".$qtyAdminReservation."<br>";
                if ($qtyAdminReservation > 0) {
                    $admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] = isset($admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]) ? $admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]+= $qtyAdminReservation : $admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]+= $qtyAdminReservation;
                }
                $qtyUserReservation = $this->CI->booking->checkForUserReserv($a, $b, $idService);
                if ($qtyUserReservation !== FALSE) {
                    //$users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] = $qtyUserReservation;
                    $users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] = isset($users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]) ? $users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]+=$qtyUserReservation : $users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]+= $qtyUserReservation;
                }
                $availabilityArr[date("Y-m-d", strtotime($a))][] = date("H:i", strtotime($a));
                $a = $b;
                $n++;
                $j++;
                if($j>1000){
                    $message = "error to match iterations 'function getScheduleService 
                             date=$date
                             idService=$idService'
                             st=$st";
                        _error_log($message);
                        break;
                }
            }
        }
        return array("availability" => $availabilityArr, "events" => $events, "admins" => $admins, "users" => $users, "countItems" => $n);
    }

    function getScheduleService($idService, $date) {
        $availabilityArr = array();
        $events = array();
        $admins = array();
        $users = array();
        $int = $this->CI->booking->getService($idService,'interval');

        $dayOfWeek = date("w", strtotime($date));
        $sql = "SELECT * FROM bs_schedule
                WHERE idService='{$idService}' AND week_num='{$dayOfWeek}' ORDER BY startTime ASC"; 
        $res = mysqli_query($this->link,$sql) or die(mysqli_error() . "<br>" . $sql);
        $n = 0;
        while ($row = mysqli_fetch_assoc($res)) {
            //$schedule[]=array("start"=>$row['startTime'],"end"=>$row['endTime']);

            $st = date("Y-m-d H:i", strtotime($date . " +" . $row['startTime'] . " minutes"));
            //TODO 
            //for afternight bookings
            //$row['endTime'] = ($row['startTime']<$row['endTime'])?$row['endTime']+720:$row['endTime'];
            $et = date("Y-m-d H:i", strtotime($date . " +" . $row['endTime'] . " minutes"));
            $a = $st;

            //layout counter
            $b = date("Y-m-d H:i", strtotime($a . " +" . $int . " minutes")); //default value for B is start time.
            $j = 0;
            for ($a = $st; $b <= $et; $b = date("Y-m-d H:i", strtotime($a . " +" . $int . " minutes"))) {
                //echo "a: ".$a." // "."b: ".$b."<br />";
                if (checkForEvents($a, $b, $idService)) {
                    $events[date("Y-m-d", strtotime($a))][] = date("H:i", strtotime($a));
                }
                $qtyAdminReservation = checkForAdminReserv($a, $b, $idService); //print "<br>".$qtyAdminReservation."<br>";
                if ($qtyAdminReservation > 0) {
                    $admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] = isset($admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]) ? $admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]+= $qtyAdminReservation : $admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]+= $qtyAdminReservation;
                }
                $qtyUserReservation = $this->CI->booking->checkForUserReserv($a, $b, $idService);
                if ($qtyUserReservation !== FALSE) {
                    //$users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] = $qtyUserReservation;
                    $users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] = isset($users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]) ? $users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]+=$qtyUserReservation : $users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]+= $qtyUserReservation;
                }
                $availabilityArr[date("Y-m-d", strtotime($a))][] = date("H:i", strtotime($a));
                $a = $b;
                $n++;
                $j++;
                if($j>1000){
                    $message = "error to match iterations 'function getScheduleService 
                             date=$date
                             idService=$idService'
                             st=$st";
                        _error_log($message);
                        break;
                }
            }
        }
        return array("availability" => $availabilityArr, "events" => $events, "admins" => $admins, "users" => $users, "countItems" => $n);
    }


    function getAvailableBookingsTable($date, $serviceID=1, $time=null, $qty=1,$couponCode='') {
        ####################################### PREPARE AVAILABILITY TABLE ##############################################
        $int = $this->CI->booking->getService($serviceID,'interval'); //interval in minutes.
        $serviceSettings = $this->CI->booking->getService($serviceID);
        $coupons = $serviceSettings['coupon'];
        $couponCode = urldecode($couponCode);
        $timeBefore = $serviceSettings['time_before'];
        $show_multiple_spaces = $serviceSettings['show_multiple_spaces']; //check option for multiple timeBooking
        $availebleSpaces = $show_multiple_spaces ? $serviceSettings['spaces_available'] : 1;
        $spot_price = $serviceSettings['spot_price'];
        $seconds = 0;
        $availability = "";

        ##########################################################################################################################
        # PREPARE AVAILABILITY ARRAY 

        $schedule = getScheduleService($serviceID, $date);
        $availabilityArr = $schedule['availability'];
        $events = $schedule['events'];
        $admins = $schedule['admins'];
        $users = $schedule['users'];
        $n = $schedule['countItems'];
        //print bw_dump($availabilityArr);
        //bw_dump($admins);
        //bw_dump($users);
        //bw_dump($events);
        //print $n;
        
        $availability .= "<div class='timeEvCont'><table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\"><tr><td valign='top' width='270' style='text-align:center;'>";

        $n = round($n/2);
        $count = 0;
        //current timestamp
        $currTime = strtotime(date("Y-m-d H:i"));
        
        foreach ($availabilityArr as $k => $v) { //$v= date  (  2010-10-05 )
            //var_dump($availabilityArr);
            foreach ($v as $kk => $vv) { //$vv = time slot in above date 
                if ($time == null) {
                    $time = array();
                }
                
                //timestamp on start time interval
                $spotTimeStart = strtotime(date("Y-m-d", strtotime($k)) . " $vv:00 -5 minutes"); //5-minutes befo select interval in past
                
                $spotTimeStart = $timeBefore > 0?strtotime(_date($date) . " $vv:00  -{$timeBefore} hours"):$spotTimeStart;
                
                if ($count == $n) {
                    $availability .= "</td><td align='center' valign='top' width='270'>";
                    $count = 0;
                }
                $availability .="<div class='timeItem'>";
                //select intervat to past
                if (isset($events[$k]) && in_array($vv, $events[$k])) {
                    $availability .= date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . " - ".TXT_EVENT.".<br />";
                } elseif ($spotTimeStart <= $currTime) {
                    $availability .= date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . " - ".TXT_PAST.".<br />";
                } elseif ((isset($admins[$k]) && array_key_exists($vv, $admins[$k]))) {
                    $spacesBookedUser = isset($users[$k][$vv])?$users[$k][$vv]:0;
                    $spacesBooked = $admins[$k][$vv];
                    $spacesAllowed = $availebleSpaces - $spacesBooked-$spacesBookedUser;
                    if ($spacesAllowed >= 1) {
                        $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                        $txt = $show_multiple_spaces ? "&nbsp;-&nbsp;<span class='spaces'>({$spacesAllowed} ".SPACES.")</span>" : "";
                        $availability .="<input type=\"checkbox\"" . (in_array($msm, $time) ? "checked" : "") . " value=\"" . $msm . "\" name=\"time[]\" rel=\"$spacesAllowed\"> - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "{$txt}<br />";
                    } else {
                        $txt = $show_multiple_spaces ? '&nbsp;-&nbsp;<span class="spaces">('.ZERO_SPACES2.')</span>' : "";
                        $availability .="<input type='checkbox' disabled><span style='color:#ccc'> - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "{$txt}</span><br />";
                    }
                 } elseif ((isset($users[$k]) && array_key_exists($vv, $users[$k]))) {

                    $spacesBooked = $users[$k][$vv];
                    
                    $spacesAllowed = $availebleSpaces - $spacesBooked;
                    
                    if ($spacesAllowed >= 1) {
                        $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                        $txt = $show_multiple_spaces ? "&nbsp;-&nbsp;<span class='spaces'>({$spacesAllowed} ".SPACES.")</span>" : "";
                        $availability .="<input type=\"checkbox\"" . (in_array($msm, $time) ? "checked" : "") . " value=\"" . $msm . "\" name=\"time[]\" rel=\"$availebleSpaces\"> - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "{$txt}<br />";
                    } else {
                        $txt = $show_multiple_spaces ? '&nbsp;-&nbsp;<span class="spaces">('.ZERO_SPACES2.')</span>' : "";
                        $availability .="<input type='checkbox' disabled><span style='color:#ccc'> - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "{$txt}</span><br />";
                    }

                } else {
                    $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                    $txt = $show_multiple_spaces ? "&nbsp;-&nbsp;<span class='spaces'>(".$availebleSpaces.SPACES.")</span>" : "";
                    $availability .="<input type=\"checkbox\"" . (in_array($msm, $time) ? "checked" : "") . " value=\"" . $msm . "\" name=\"time[]\" rel=\"$availebleSpaces\"> - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "{$txt}<br />";
                }
                $availability .="</div>";
                $count++;
            }
        };
        $currencyPos = $this->CI->booking->getOption('currency_position');
        $cuurency = $this->CI->booking->getOption('currency');
        $availability .="</td></tr></table><div class='qtyCont'>";
        $availability .=$show_multiple_spaces ?"<span>".TXT_QTY." <span class='spinner'><input type='text' name='qty' id='qty' value='$qty' style='width:40px'></span></span>" : "";
        $availability .=$spot_price ? "&nbsp;<span id='feeValue'>".$cuurency . "&nbsp;</span>&nbsp;<del id='feeValueOld'></del>" : "";
        $availability .="</div>";
        if($coupons && $spot_price>0){
            $availability .="<div id='coupon_conteiner'>";
            $availability .="<label>". TXT_COUPON_CODE .":</label><input type='text' name='couponCode' id='couponCode' value='{$couponCode}' class='small'>&nbsp;<span id='discountDetails'></span>";
            $availability .="</div>";
        }
        $availability .="</div>";
        ##########################################################################################################################

        return $availability;
    }

    function checkQtyForTimeBooking($serviceID, $time, $date, $interval, $qty) {
        //print "$serviceID<br>$date<br>$interval<br>$qty";

        $availebleSpaces = $this->CI->booking->getService($serviceID, 'spaces_available');
        $error = false;

        if($date < date("Y-m-d") || !is_array($time)) return true;

        $sumQty = 0;
        foreach ($time as $k => $v) {
            $qtyTmp = 0;
            $from = date("Y-m-d H:i:s", strtotime($date . " +" . $v . " minutes"));
            $to = date("Y-m-d H:i:s", strtotime($from . " +" . $interval . " minutes"));
            $adminQTY = checkForAdminReserv($from, $to, $serviceID);
            //print gettype($qtyTmp)."<br>";
            $sumQty = $adminQTY;

            $sSQL = "SELECT bri.* FROM `bs_reservations_items` bri
                INNER JOIN bs_reservations br on bri.reservationID = br.id
                    WHERE br.serviceID='{$serviceID}' AND (
                    (bri.reserveDateFrom < '{$to}' AND bri.reserveDateTo >= '{$to}') OR
                    (bri.reserveDateTo > '{$from}' AND bri.reserveDateFrom <= '{$from}') OR
                    (bri.reserveDateFrom <= '{$from}' AND bri.reserveDateTo >= '{$to}') OR
                    (bri.reserveDateFrom >= '{$from}' AND bri.reserveDateTo <= '{$to}'))
                    AND (br.status='1' OR br.status='4')  
                    ORDER BY bri.reserveDateFrom ASC";

            $result = mysqli_query($this->link,$sSQL);
            if (mysqli_num_rows($result) > 0) {
                if (mysqli_num_rows($result) > 1) {

                    while ($row = mysqli_fetch_assoc($result)) {
                        $qtyTmp+=$row['qty'];
                    }
                    $sumQty+=$qtyTmp + $qty;
                    if ($sumQty > $availebleSpaces) {
                        $error = true;
                    }
                } else {
                    $qtyTmp = mysqli_fetch_assoc($result);
                    $sumQty+=$qtyTmp['qty'] + $qty;
                    if ($sumQty > $availebleSpaces) {
                        $error = true;
                    }
                }
            }
        }

        return $error;
    }

    function getManualBookingsByDate($dateFrom,$serviceID,$dateTo=null){
     //print $from." - ".$to."<br>";
        $dateTo = empty($dateTo)?$dateFrom:$dateTo;
        
        $bookings = array();
        //$date = date("Y-m-d", strtotime($from)); //print $date;
        $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=1 AND reserveDateTo>='{$dateTo}'"; //print $sSQL;
        $res = mysqli_query($this->link,$sSQL);
        if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {

                $startDate = date("Y-m-d", strtotime($row['reserveDateFrom']));
                $endDate = date("Y-m-d", strtotime($row['reserveDateTo']));
                $startTime = date("H:i", strtotime($row['reserveDateFrom']));
                $endTime = date("H:i", strtotime($row['reserveDateTo']));
                $st = $startDate;
                $en = $endDate;
                $j = 0;
                for ($i = $st; $i <= $dateTo . " 23:59:59"; $i = date("Y-m-d", strtotime($i . " +{$row['repeate_interval']} {$row['repeate']}"))) {
                    //print $i;
                    /*$reserveDateFrom = $date . " " . $startTime;
                    $reserveDateTo = $date . " " . $endTime;*/
                    if ($dateFrom <= date("Y-m-d", strtotime($i)) || $dateTo > date("Y-m-d", strtotime($i))) {

                        $bookings[$row['id']]=$row;
                    }

                    //$i=$b;
                    $j++;
                    if($j>1000){
                        $message = "error to match iterations 'function checkForAdminReserv 
                             from=$from
                             to=$to
                             serviceID=$serviceID'";
                        _error_log($message);
                        break;
                    }
                }
            }
        }
        //bw_dump($recurring);
        //print $qtyTmp."-";
        $to = "$dateTo 23:59:00";
        $from = "$dateFrom 00:00:00";
        $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=0 AND(
                    (reserveDateFrom < '{$to}' AND reserveDateTo >= '{$to}') OR
                    (reserveDateTo > '{$from}' AND reserveDateFrom <= '{$from}') OR
                    (reserveDateFrom <= '{$from}' AND reserveDateTo >= '{$to}') OR
                    (reserveDateFrom >= '{$from}' AND reserveDateTo <= '{$to}'))";
        //print $sSQL;
        $res = mysqli_query($this->link,$sSQL);
        if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                $bookings[$row['id']]=$row;
            }
        } else {
            //return false;
        }

        return $bookings;    
    }

    function getScheduleEventsTable($date, $serviceID=1) {
        $availability = "";
        $availability .= "<table  border=\"0\" class=\"dataTable schedule\" align=\"left\" cellpadding=\"0\" cellspacing=\"0\">";
        $availability .= "<thead>
                                <tr class=\"topRow\">
                                <th>Event</th>
                                <th>Spots</th>
                                <th>Time From</th>
                                <th>Time To</th>
                                <th>Entry Fee</th>

                                </tr></thead>";

        $eventsList = getEventsByDate($date,$serviceID);
        //bw_dump($eventsList);
        $i=0;
        foreach($eventsList as $event){
            $i=$i?0:1;
            $class=$i?"odd":"even";
            $_event = $event['event'];
            $timeFrom =getDateFormat($_event['eventDate'])." ". _time($_event['eventDate']);
            $timeTo= getDateFormat($_event['eventDateEnd'])." ". _time($_event['eventDateEnd']);
            $spaces = $event['qty']." out of ".$_event['spaces'];
            $fee = $_event['entryFee']>0?getCurrencyText($_event['entryFee']):"Free";

            $hasBookings = $_event['spaces']!=$event['qty']?true:false;
            if($hasBookings){
                $spaces = "<a href='javascript:;' data-event=\"{$_event['id']}\" class=\"bookingsList\">{$spaces}</a>";
            }
            $availability.="
            <tr class=\"{$class}\">
                <td><a href=\"bs-events-add.php?id={$_event['id']}\">{$_event['title']}</a></td>
                <td><span class=\"space\">{$spaces}</span></td>
                <td>{$timeFrom}</td>
                <td>{$timeTo}</td>
                <td>{$fee}</td>


            </tr>
            ";
            if($hasBookings){
                //$availability.="<tr><td>&nbsp;</td><td colspan=\"4\">";
                /*$availability.="<table  border=\"0\" class=\"dataTable bookings\" align=\"left\" cellpadding=\"0\" cellspacing=\"0\">
                                <thead>
                                <tr class=\"topRow\">
                                    <th>Spots</th>
                                    <th>Customer Name</th>
                                    <th>Customer Phone</th>
                                    <th>Customer Email</th>
                                </tr>
                                </thead>";*/
                $availability.="
                                <tr class=\"topRow header\" data-row=\"{$_event['id']}\">
                                    <td>&nbsp;</td>
                                    <td>Spots</td>
                                    <td>Customer Name</td>
                                    <td>Customer Phone</td>
                                    <td>Customer Email</td>
                                </tr>
                                ";
                $sql = "SELECT * FROM bs_reservations WHERE eventID='{$_event['id']}' AND status IN ('1','4')";
                $res = mysqli_query($this->link,$sql);
                $j=0;$_class = '';
                while($row = mysqli_fetch_assoc($res)){
                    //bw_dump($row);
                    if($j==0){$j=1;$_class="odd";}else{$j=0;$_class="even";}
                    $availability.="<tr class=\"{$_class} bookings\" data-row=\"{$_event['id']}\">
                                    <td>&nbsp;</td>
                                    <td>{$row['qty']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['phone']}</td>
                                    <td><a href=\"bs-bookings_event-edit.php?id={$row['id']}\">{$row['email']}</a></td>
                                    </tr>";
                }
                //$availability.="</table></td></tr>";
            }
        }
        $availability .="</table>";
        return $availability;
    }

    function getScheduleTable($date, $serviceID=1) {
        global $baseDir;
        ####################################### PREPARE AVAILABILITY TABLE ##############################################


        $adminReserveData = "";
        $seconds = 0;
        $availability = "";
        $availability .= "<table  border=\"0\" class=\"dataTable schedule\" align=\"left\" cellpadding=\"0\" cellspacing=\"0\">";
        $availability .= "<thead><tr class=\"topRow\"><th>Time</th><th>Spots Left</th><th>Customer Name</th><th>Customer Phone</th><th  class=\"noBorderRight\">Customer Email</th><th></th>&nbsp;</tr></thead>";



        $manualBookings = $this->getManualBookingsByDate($date,$serviceID);

            $reservedArray = array();
            $reservationData = array();
            $int = $this->CI->booking->getService($serviceID,'interval'); //interval in minutes.

            $availebleSpaces = $this->CI->booking->getService($serviceID, 'spaces_available');
            $show_multiple_spaces = $this->CI->booking->getService($serviceID, 'show_multiple_spaces');
            //bw_dump($manualBookings);
    $J=1;
            //ACTUAL CUSTOMER BOOKINGS

            $query = "SELECT bs_reservations_items.*,bs_reservations.email,bs_reservations.name,bs_reservations.phone,bs_reservations.id as rid FROM `bs_reservations_items`
        INNER JOIN bs_reservations on bs_reservations_items.reservationID = bs_reservations.id
        WHERE (bs_reservations.status='1' OR bs_reservations.status='4') AND
        bs_reservations_items.reserveDateFrom LIKE '" . $date . "%' AND
        bs_reservations.serviceID={$serviceID} ORDER BY bs_reservations_items.reserveDateFrom ASC ";
            $result = mysqli_query($this->link,$query);
            if (mysqli_num_rows($result) > 0) {

                while ($rr = mysqli_fetch_assoc($result)) {

                    $tFrom = date("H:i", strtotime($rr["reserveDateFrom"]));
                    $dFrom = date("Y-m-d", strtotime($rr["reserveDateFrom"]));
                    if (isset($reservedArray[$dFrom][$tFrom])) {
                        $reservedArray[$dFrom][$tFrom] = $rr["qty"] + $reservedArray[$dFrom][$tFrom];
                    } else {
                        $reservedArray[$dFrom][$tFrom] = $rr["qty"];
                    }
                    //$reservationInfo = "<div><a href='bs-bookings-edit.php?id=" . $rr["rid"] . "'>" . $rr["name"] . "&nbsp; (phone:" . $rr["phone"] . "; qty=" . $rr['qty'] . ")</a></div>";
                    $classRow = $classRow=='even1'?"odd1":"even1";
                    $reservationInfo ="<tr class='{$classRow} rr_{$tFrom}'><td></td><td>{$rr['qty']}</td><td>{$rr["name"]}</td><td>{$rr["phone"]}</td><td><a 'bs-bookings-edit.php?id=" . $rr["rid"] . "'>{$rr['email']}</a></td><td></td></tr>";

                    $reservationInfoArray = array("qty"=>$rr['qty'],'name'=>$rr['name'],'phone'=>$rr['phone'],'email'=>$rr['email'],"rid"=>$rr['rid']);

                    if (isset($reservationData[$dFrom][$tFrom])) {
                        $reservationData[$dFrom][$tFrom] = $reservationData[$dFrom][$tFrom] . $reservationInfo;
                    } else {
                        $reservationData[$dFrom][$tFrom] = $reservationInfo;
                    }
                    $reservationData1[$dFrom][$tFrom][]=$reservationInfoArray;
                }
            }
            //bw_dump($reservationData1);
            //bw_dump($reservedArray);
            ##########################################################################################################################
            ##########################################################################################################################
            # PREPARE AVAILABILITY ARRAY
            $schedule = getScheduleService($serviceID, $date);
            $availabilityArr = $schedule['availability'];
            $events = $schedule['events'];
            $n = $schedule['countItems'];
            $admins = $schedule['admins'];
            $users = $schedule['users'];


            //bw_dump($events);
            //$ww= date("w",strtotime($date));
            //$tt = getStartEndTime($ww,$serviceID);
            if (!count($availabilityArr)) {
                //$availability .= ADM_NONWORKING;
            } else {

                $n = ($n - ($n % 2)) / 2;
                $count = 0;
                $i=0;

                foreach ($availabilityArr as $k => $v) { //$v= date  (  2010-10-05 )
                    foreach ($v as $kk => $vv) { //$vv = time slot in above date
                        $i=$i?0:1;
                        $class=$i?"odd":"even";

                        $time = _time($vv)."-"._time(date("Y-m-d H:i:m", strtotime($vv . " +" . $int . " minutes")));
                        $bookLink = "<a class='greedButton' href='bs-reserve.php?serviceID={$serviceID}&reserveDateFrom={$date}&reserveDateTo={$date}&1_from_h=".date("H", strtotime($vv))."&1_from_m=".date("i", strtotime($vv))."&2_from_h=".date("H", strtotime($vv. " +" . $int . " minutes"))."&2_from_m=".date("i", strtotime($vv. " +" . $int . " minutes"))."' >Book</a>";
                        if (isset($events[$k]) && in_array($vv, $events[$k])) {
                            $availability .="<tr class=\"$class\"><td><span class=\"time\">" . $time . "</span></td><td colspan=\"4\" class=\"noBorderRight\">Event<td></tr>";
                        } elseif (isset($admins[$k]) && array_key_exists($vv, $admins[$k])) {
                            $classRow = "odd1";

                            $adminData = getAdminReserveData("$k $vv",date("Y-m-d H:i",strtotime("$k $vv +$int minutes")),$serviceID);

                            $spacesBookedUser = isset($users[$k][$vv])?$users[$k][$vv]:0;
                            $spacesBooked = $admins[$k][$vv];
                            $adminReserveData = "";
                            foreach($adminData as $key=>$data){
                                $classRow = $classRow=='even1'?"odd1":"even1";
                                $viewLink = "<a href=\"bs-reserve.php?id={$key}\" class=\"greedButton grey\">Edit</a>";
                                //$adminReserveData .= "<br><a href='bs-reserve.php?id={$key}'>Manual Reservation<br/> (Reason: {$data['reason']}; Quantity: {$data['qty']})</a>";
                                $adminReserveData .= "<tr class='{$classRow} rr_{$J} hide'><td></td><td>{$data['qty']}</td><td>Manual Booking <img src='images/info_small.png' border=\"0\" class=\" tipTip imgCenter\"  title=\"{$data['reason']} \"/></td><td></td><td  class=\"noBorderRight\"></td><td>{$viewLink}</td></tr>";

                            }
                            $spacesAllowed = $availebleSpaces - $spacesBooked-$spacesBookedUser;
                            $userBookings = "";
                            if(($availebleSpaces - $spacesBooked)>0){

                                if(isset($reservationData1[$k][$vv])) {
                                    foreach($reservationData1[$k][$vv] as $rr) {
                                        $classRow = $classRow=='even1'?"odd1":"even1";
                                        $viewLink = "<a href=\"bs-bookings-edit.php?id=" . $rr["rid"] . "\" class=\"greedButton grey\">Edit</a>";
                                        $userBookings.="<tr class='{$classRow} rr_{$J} hide'><td></td><td>{$rr['qty']}</td><td>{$rr["name"]}</td><td>{$rr["phone"]}</td><td class=\"noBorderRight\"><a 'bs-bookings-edit.php?id=" . $rr["rid"] . "'>{$rr['email']}</a></td><td>{$viewLink}</td></tr>";
                                    }
                                }


                            }else{
                                $spacesAllowed = 0;
                            }
                            if($show_multiple_spaces){
                                $availability .="<tr  class=\"$class\"><td><span class=\"time\">" . $time . "</span></td><td><span class='space'>{$spacesAllowed} out of {$availebleSpaces}</span></td><td><a href='javascript:;' onclick='collapseRows(this,\"rr_{$J}\")'>Expand to view all</a></td><td>&nbsp;</td><td class=\"noBorderRight\">&nbsp;</td><td>{$bookLink}</td></tr>";
                            }else{
                                $availability .="<tr  class=\"$class\"><td><span class=\"time\">" . $time . "</span></td><td><span class='space'>0 out of 1</span></td><td><a href='javascript:;' onclick='collapseRows(this,\"rr_{$J}\")'>Expand to view all</a></td><td>&nbsp;</td><td class=\"noBorderRight\">&nbsp;</td><td>{$bookLink}</td></tr>";
                            }

                            $availability.=$adminReserveData.$userBookings;
                        } elseif (isset($users[$k][$vv])/* || (isset($users[$k]) && array_key_exists($vv, $users[$k]))*/) {
                            $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                            //$availebleSpaces;
                            $spacesBooked = $users[$k][$vv];
                            $spacesAllowed = $availebleSpaces - $spacesBooked;
                            $userBookings='';
                            if($show_multiple_spaces){

                                if(isset($reservationData1[$k][$vv])) {

                                    foreach($reservationData1[$k][$vv] as $rr) {
                                        $classRow = $classRow=='even1'?"odd1":"even1";
                                        $viewLink = "<a href=\"bs-bookings-edit.php?id=" . $rr["rid"] . "\" class=\"greedButton grey\">Edit</a>";
                                        $userBookings.="<tr class='{$classRow} rr_{$J} hide'><td></td><td>{$rr['qty']}</td><td>{$rr["name"]}</td><td>{$rr["phone"]}</td><td class=\"noBorderRight\"><a 'bs-bookings-edit.php?id=" . $rr["rid"] . "'>{$rr['email']}</a></td><td>{$viewLink}</td></tr>";
                                    }
                                }
                                $bookLink = $spacesAllowed>0?$bookLink:"";
                                $availability .="<tr  class=\"$class\"><td><span class=\"time\">" . $time . "</span></td><td><span class='space'>{$spacesAllowed} out of {$availebleSpaces}</span></td><td><a href='javascript:;' onclick='collapseRows(this,\"rr_{$J}\")'>Expand to view all</a></td><td>&nbsp;</td><td class=\"noBorderRight\">&nbsp;</td><td>{$bookLink}</td></tr>";
                                $availability.=$userBookings;
                                //$availability .="<tr class='schedule_av  class=\"$class\"".($spacesAllowed==0?"empty":"")."'><td width='100' valign='top' class='time'><div>" . $time . "</div></td><td valign='top'><span class='space'>{$spacesAllowed}</span>".($spacesAllowed?$bookLink:"") .SPC_LEFT . $reservationData[$k][$vv] . "</td></tr>";
                            }else{
                                $rr = $reservationData1[$k][$vv][0];

                                $viewLink = "<a href=\"bs-bookings-edit.php?id=" . $rr["rid"] . "\" class=\"greedButton grey\">Edit</a>";
                                $availability.="<tr  class=\"$class\"><td><span class=\"time\">" . $time . "</span></td><td><span class='space'>0 out of 1</span></td><td>{$rr["name"]}</td><td>{$rr["phone"]}</td><td class=\"noBorderRight\"><a 'bs-bookings-edit.php?id=" . $rr["rid"] . "'>{$rr['email']}</a></td><td>{$viewLink}</td></tr>";
                            }
                        } else {
                            $availebleSpaces = $show_multiple_spaces ? $availebleSpaces : 1;
                            //$availability .= "<tr class='schedule_av'><td width='100' valign='top' class='time'><div>" . $time . "</div></td><td valign='top'><span class='space'>{$availebleSpaces}</span>". SPC_LEFT. $reservationData[$k][$vv] . "{$bookLink}</td></tr>";
                            $availability .="<tr  class=\"$class\"><td><span class=\"time\">" . $time . "</span></td><td><span class='space'>{$availebleSpaces} out of {$availebleSpaces}</span></td><td>N/A</td><td>N/A</td><td class=\"noBorderRight\">N/A</td><td>{$bookLink}</td></tr>";
                        }


                        $count++;
                        $J++;
                    }
                }


            }

        $availability .="</table>";
        ##########################################################################################################################

        return $availability;
    }

    function _getScheduleTable($date, $serviceID=1) {
        global $baseDir;
        ####################################### PREPARE AVAILABILITY TABLE ##############################################
        $int = $this->CI->booking->getService($serviceID,'interval'); //interval in minutes.
        $reservedArray = array();
        $reservationData = array();
        $adminReserveData = "";
        $seconds = 0;
        $availability = "";
        $availebleSpaces = $this->CI->booking->getService($serviceID, 'spaces_available');
        $show_multiple_spaces = $this->CI->booking->getService($serviceID, 'show_multiple_spaces');
        
        
        $manualBookings = $this->getManualBookingsByDate($date,$serviceID);
        //bw_dump($manualBookings);

        //ACTUAL CUSTOMER BOOKINGS

        $query = "SELECT bs_reservations_items.*,bs_reservations.name,bs_reservations.phone,bs_reservations.id as rid FROM `bs_reservations_items` 
        INNER JOIN bs_reservations on bs_reservations_items.reservationID = bs_reservations.id 
        WHERE (bs_reservations.status='1' OR bs_reservations.status='4') AND 
        bs_reservations_items.reserveDateFrom LIKE '" . $date . "%' AND 
        bs_reservations.serviceID={$serviceID} ORDER BY bs_reservations_items.reserveDateFrom ASC ";
        $result = mysqli_query($this->link,$query);
        if (mysqli_num_rows($result) > 0) {
            while ($rr = mysqli_fetch_assoc($result)) {
                if (isset($reservedArray[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))])) {
                    $reservedArray[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))] = $rr["qty"] + $reservedArray[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))];
                } else {
                    $reservedArray[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))] = $rr["qty"];
                }
                $reservationInfo = "<div><a href='bs-bookings-edit.php?id=" . $rr["rid"] . "'>" . $rr["name"] . "&nbsp; (phone:" . $rr["phone"] . "; qty=" . $rr['qty'] . ")</a></div>";
                if (isset($reservationData[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))])) {
                    $reservationData[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))] =
                            $reservationData[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))] . $reservationInfo;
                } else {
                    $reservationData[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))] = $reservationInfo;
                }
            }
        }
        //bw_dump($reservationData);
        //bw_dump($reservedArray);
        ##########################################################################################################################
        ##########################################################################################################################
        # PREPARE AVAILABILITY ARRAY 
        $schedule = getScheduleService($serviceID, $date);
        $availabilityArr = $schedule['availability'];
        $events = $schedule['events'];
        $n = $schedule['countItems'];
        $admins = $schedule['admins'];
        $users = $schedule['users'];
       

        if (!count($availabilityArr)) {
            $availability .= ADM_NONWORKING;
        } else {
            $availability .= "<table width=\"500\" border=\"0\" align=\"left\" cellpadding=\"0\" cellspacing=\"0\">";
            $n = ($n - ($n % 2)) / 2;
            $count = 0;
            
            $availability .="<tr><td valign='top'>";
            foreach ($availabilityArr as $k => $v) { //$v= date  (  2010-10-05 )
                foreach ($v as $kk => $vv) { //$vv = time slot in above date 
                    if ($count == $n) {
                        $availability .= "</td><td align='left' valign='top'>";
                        $count = 0;
                    }
                    $bookLink = "<a class='book' href='bs-reserve.php?serviceID={$serviceID}&reserveDateFrom={$date}&reserveDateTo={$date}&1_from_h=".date("H", strtotime($vv))."&1_from_m=".date("i", strtotime($vv))."&2_from_h=".date("H", strtotime($vv. " +" . $int . " minutes"))."&2_from_m=".date("i", strtotime($vv. " +" . $int . " minutes"))."' ></a>";
                    if (isset($events[$k]) && in_array($vv, $events[$k])) {
                        $availability .="<tr class='schedule_na'><td width='100' valign='top' class='time'><div>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "</div></td><td valign='top'>".TXT_EVENT2."</td></tr>";
                    } elseif (isset($admins[$k]) && array_key_exists($vv, $admins[$k])) {
                        $adminData = getAdminReserveData("$k $vv",date("Y-m-d H:i",strtotime("$k $vv +$int minutes")),$serviceID);
                        $spacesBookedUser = isset($users[$k][$vv])?$users[$k][$vv]:0;
                        $spacesBooked = $admins[$k][$vv];
                        $adminReserveData = "";
                        foreach($adminData as $key=>$data){
                            $adminReserveData .= "<br><a href='bs-reserve.php?id={$key}'>Manual Reservation<br/> (Reason: {$data['reason']}; Quantity: {$data['qty']})</a>";
                        
                        }
                        $spacesAllowed = $availebleSpaces - $spacesBooked-$spacesBookedUser;
                        if ($spacesAllowed >= 1) {
                            $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                            $spacesAllowed = $show_multiple_spaces ? $spacesAllowed : 1;
                            $availability .="<tr class='schedule_av'><td width='100' valign='top' class='time'><div>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "</div></td><td valign='top'><span class='space'>{$spacesAllowed}</span> {$bookLink}". SPC_LEFT. $adminReserveData .(isset($reservationData[$k][$vv])?$reservationData[$k][$vv]:""). "</td></tr>";
                        } else {

                            $availability .="<tr class='schedule_av empty'><td width='100' valign='top' class='time'><div>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "</div></td><td valign='top'><span class='space'>{$spacesAllowed}</span>". SPC_LEFT. $adminReserveData . (isset($reservationData[$k][$vv])?$reservationData[$k][$vv]:"")."</td></tr>";
                        }
                    } elseif (isset($users[$k]) || (isset($users[$k]) && !array_key_exists($vv, $users[$k]))) {
                        $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                        //$availebleSpaces;
                        $spacesBooked = $users[$k][$vv];
                        $spacesAllowed = $availebleSpaces - $spacesBooked;
                        $availability .="<tr class='schedule_av ".($spacesAllowed==0?"empty":"")."'><td width='100' valign='top' class='time'><div>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "</div></td><td valign='top'><span class='space'>{$spacesAllowed}</span>".($spacesAllowed?$bookLink:"") .SPC_LEFT . $reservationData[$k][$vv] . "</td></tr>";
                    } else {
                        $availebleSpaces = $show_multiple_spaces ? $availebleSpaces : 1;
                        $availability .= "<tr class='schedule_av'><td width='100' valign='top' class='time'><div>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "</div></td><td valign='top'><span class='space'>{$availebleSpaces}</span>". SPC_LEFT. $reservationData[$k][$vv] . "{$bookLink}</td></tr>";
                    }


                    $count++;
                }
            }
            if (count($manualBookings)){
                $availability .="<tr><td colspan=2><h3>Manual Bookings For Current Day</h3></td></tr>";
                foreach($manualBookings as $mbook){
                    $availability .="<tr><td colspan=2><div class='mBooking'><a class=\"naw\" href=\"javascript:;\">▾</a>";
                    $availability .="Manual Booking: <a href='bs-reserve.php?id={$mbook['id']}'>{$mbook['reason']}</a> ".($mbook['recurring']?"( <b>recurring</b> )":"")."<br>";
                    $availability .="<div class='info'><label>From</label>: <b>".getDateFormat($mbook['reserveDateFrom'])."</b> "._time($mbook['reserveDateFrom'])."<br>";
                    $availability .="<label>To</label>: <b>".getDateFormat($mbook['reserveDateTo'])."</b> "._time($mbook['reserveDateTo'])."<br>";
                    if($mbook['recurring']){
                        $availability .="<label>Interval</label>: every {$mbook['repeate_interval']} <b>{$mbook['repeate']}</b> <br>";
                        
                    }
                    $availability .="<label>Qty</label>: {$mbook['qty']}<br>";
                    $availability .="</div></div></td></tr>";

                }
                $availability .="<tr><td colspan=2>&nbsp;</td></tr>";
            }
            $availability .="</td></tr></table>";
        }
        ##########################################################################################################################

        return $availability;
    }

    function getBookingText($serviceID=1) {
        $tt = array();
        $maximumBookings = $this->CI->booking->getMaxBooking($serviceID);
        $inter = $this->CI->booking->getService($serviceID,'interval');
        $intervalConverted = $inter * $maximumBookings;
        //interval 15*X / 30*X / 45*X / 60*X /
        // example with 2 maximum bookings - 30 / 60 / 90 / 120
        if ($intervalConverted < 60) {
            //minutes
            if ($maximumBookings != 0 && $maximumBookings != 99) {
                $tt[0] = $intervalConverted . TXT_MINUTES_MAX;
            } else {
                $tt[0] = "";
            }
        } else {
            //hours
            $fullHours = ($intervalConverted - $intervalConverted % 60) / 60;
            $fullMinutes = $intervalConverted - ($fullHours * 60);
            if ($maximumBookings != 0 && $maximumBookings != 99) {
                $tt[0] = $fullHours . TXT_HOURSS . ($fullMinutes > 0 ? TXT_AND . $fullMinutes . TXT_MINUTES : "") . TXT_MAX;
            } else {
                $tt[0] = "";
            }
        }


        $minimumBookings = $this->CI->booking->getMinBooking($serviceID);
        $intervalConverted = $inter * $minimumBookings;
        if ($intervalConverted < 60) {
            //minutes
            if ($minimumBookings != 0 && $minimumBookings != 99) {
                $tt[1] = $intervalConverted . TXT_MINUTES_MIN;
            } else {
                $tt[1] = "";
            }
        } else {
            //hours
            $fullHours = ($intervalConverted - $intervalConverted % 60) / 60;
            $fullMinutes = $intervalConverted - ($fullHours * 60);
            if ($minimumBookings != 0 && $minimumBookings != 99) {
                $tt[1] = $fullHours . TXT_HOURSS . ($fullMinutes > 0 ? TXT_AND . $fullMinutes . TXT_MINUTES : "") . TXT_MIN ;
            } else {
                $tt[1] = "";
            }
        }
        return $tt;
    }


    function uploadFile($inputFile, $sFolderPictures) {
        $image_path = $inputFile['tmp_name'];
        $photoFileNametmp = $inputFile['name'];
        $fileNamePartstmp = explode(".", $photoFileNametmp);
        $fileExtensiontmp = strtolower(end($fileNamePartstmp)); // part behind last dot
        $allowedExtentions = array("jpeg", "jpg", "png", "gif");
        $allowedMime = array("image/jpeg", "image/jpg", "image/png", "image/gif");
        $fileInfo = getimagesize($image_path);
        $err = false;

        if ($inputFile['size'] > 20971520) {
            $ssize = sprintf("%01.2f", $inputFile['size'] / 1048576);
            $err = "Your file is " . $ssize . ". Max file size is 20 MB.<br>";
        }
        if (!in_array(strtolower($fileExtensiontmp), $allowedExtentions)) {
            $err.= "Picture's extension should be ." . join(" ,.", $allowedExtentions) . "<br />";
        }elseif (!in_array($fileInfo['mime'], $allowedMime)) {
            $err.= "Picture's type should be ." . join(" ,.", $allowedMime) . "<br />";
        }

        if (empty($err)) {
            // $newFile=$_SERVER['DOCUMENT_ROOT'].$sFolderPictures;//print $newFile;
            $newFile = $sFolderPictures; //print $newFile;
            $ret = move_uploaded_file($inputFile['tmp_name'], $newFile);
            if (!$ret) {
                $err.="Upload failed. No file received. Check your installation directory in dbconnect.php";
            } else {
                $imgPath = $sFolderPictures;
            }
        }
        if (file_exists($inputFile['tmp_name'])) {
            @unlink($inputFile['tmp_name']);
        }
        return array("error"=>$err,'imgPath'=>$imgPath);
    }

    function getEventList($eventID=null, $qty=null,$date,$couponCode = '') {
        $availability = ""; //print "dd".$qty;
        
            
        $query = "SELECT * FROM bs_events WHERE id={$eventID} ORDER BY eventDate ASC ";
        
        $currencyPos = $this->CI->booking->getOption('currency_position');
        $currency = $this->CI->booking->getOption('currency');


        $result = mysqli_query($this->link,$query);
        if (mysqli_num_rows($result) > 0) {
           
            $availability .= "<div class='eventWrapper'>";
            //we have events for this day!
            $event_num = mysqli_num_rows($result);
            //we need to check if at least one event has spaces. if yes then { $bgClass="cal_reg_on";  } else { $bgClass="cal_reg_off"; }
            $event_available = false;
            $event_count = 0;
            $text = "";
            $curr = getAdminPaypal();
            $startEnd = getEventStartEndDate($eventID,$date,'array');
           
            
            while ($row = mysqli_fetch_assoc($result)) {
                $coupons = $row['coupon'];
                $spaces_left = $row["recurring"]==1? $this->CI->booking->getSpotsLeftForEvent($row["id"],$date): $this->CI->booking->getSpotsLeftForEvent($row["id"]);
                $availability .="<div class='eventContainer'>";

                $availability .="<div class='eventCheckbox'>";
                if ($spaces_left > 0) {

                    $availability .="<input type='hidden' name='eventID' value='" . $row["id"] . "' >";
                } else {
                    $availability .= "&nbsp;";
                }
                $availability .="</div>";
                $availability .="<div class='eventTitle'><h1 itemprop=\"name\">" . $row["title"] . "</h1></div>";
                $availability .= "<table class='evntCont' width='100%'><tr><td width='80%' valign='center'><div class='eventDescr'>";

                $availability .=TXT_EVENT_START." <span>" . getDateFormat($startEnd['from']) . "&nbsp;&nbsp;" . date((getTimeMode()) ? " g:i a" : " H:i", strtotime($startEnd['from'])) . "</span><br>
                                        ".TXT_EVENT_ENDS." <span>" . getDateFormat($startEnd['to'])  . "&nbsp;&nbsp;". date((getTimeMode()) ? " g:i a" : "H:i", strtotime($startEnd['to'])) . "</span>
                        <br />";
                
                if(!empty($row['location'])){
                    if(!empty($row['map_link'])){
                        $availability .=LOCATION."<a href='{$row['map_link']}' target='_blank'>{$row['location']}</a><br/>";
                    }else{
                       $availability .=LOCATION."{$row['location']}<br/>";
                    }
                
                }
                if ($row["path"] != "") {
                    $availability .="<div class='eventImage'><img src='" . $row["path"] . "' alt='" . $row["title"] . "' /></div>";
                }
                $availability .="<p itemprop=\"description\">".nl2br($row["description"])."</p>";
                
                $availability .= "</div><td>";
                $availability .="<td class='brd_l'><div class='spots'><span class='spot'>" . $spaces_left . "</span><span class='spot1'>".TXT_SPOTS_LEFT."</span></div></td>";
                if ($row["allow_multiple"] == "1") {
                    $qty_max = ($this->CI->booking->getMaxQtyEvent($row["id"]) > $spaces_left) ? $spaces_left : $this->CI->booking->getMaxQtyEvent($row["id"]);
                    $availability .= "<td class='brd_l'><div class='tickets'>
                        <select name='qty_" . $row["id"] . "' id='qty'>";
                    $availability .="<option value='1'>".TXT_FUNC_QTY."</option>";
                    for ($i = 1; $i <= $qty_max; $i++) {
                        $availability .= "<option value='" . $i . "' " . (!empty($qty) && $i == $qty && $row["id"] == $eventID ? "selected='selected'" : "") . ">" . $i . "</option>";
                    }
                    $availability .= "</select></div></td>";
                }
                if ($row["entryFee"]> 0) {

                    $price = $row["entryFee"];
                    if ($this->CI->booking->getOption('enable_tax')) {
                        $price = $price + ($price * $this->CI->booking->getOption('tax') / 100);
                    }
                    $availability .= "<td class='brd_l'><div class='fee'><b> ".($currencyPos=='b'?$currency:"")." <span  id='price'>" . number_format($price, 2) . "</span> ".($currencyPos=='a'?$currency:"")."<del id='feeValueOld'></del></div></td>";
                } else {
                    $availability .= "<td class='brd_l'><div class='fee'><span style='color:#0FA1D2'>".TXT_FUNC_FREE."</span></div></td>";
                }
                $availability .="</tr>";
                if($coupons && $row["entryFee"]>0){
                   $availability .="<tr><td colspan='5' align='center'><label>". TXT_COUPON_CODE .":</label><input type='text' name='couponCode' id='couponCode' value='{$couponCode}' class='small'>&nbsp;<span id='discountDetails'></span></td></tr>";
                }
                $availability .="</table>";
                $availability .="<br clear='all'><div class='social'>" . getSocial($row["id"]) . "</div>";
                $availability .="</div>";
            }
            if ($event_count == 1) {
                
            } else if ($event_count > 1) {
                $text = "<p>".TXT_PLSSELECT."</p>";
            } else {
                $text = "";
            }

            $availability .="</div>";
        }

        return $availability;
    }

    function getEventsList($date, $serviceID=1, $eventID=null, $selEvent=null, $qty=null) {
        $availability = ""; //print "dd".$qty;

        if (!empty($eventID)) {
            $query = "SELECT * FROM bs_events WHERE id={$eventID} ORDER BY eventDate ASC ";
        } else {
            $query = "SELECT * FROM bs_events WHERE eventDate LIKE '%" . $date . "%' AND serviceID={$serviceID} ORDER BY eventDate ASC ";
        }

        $result = mysqli_query($this->link,$query);
        if (mysqli_num_rows($result) > 0) {
            $availability .= "<div class='eventWrapper'>";
            //we have events for this day!
            $event_num = mysqli_num_rows($result);
            //we need to check if at least one event has spaces. if yes then { $bgClass="cal_reg_on";  } else { $bgClass="cal_reg_off"; }
            $event_available = false;
            $event_count = 0;
            $text = "";
            $curr = getAdminPaypal();
            while ($row = mysqli_fetch_assoc($result)) {
                $spaces_left = $row["recurring"]==1? $this->CI->booking->getSpotsLeftForEvent($row["id"],$date): $this->CI->booking->getSpotsLeftForEvent($row["id"]);
                $availability .="<div class='eventContainer'>";

                $availability .="<div class='eventCheckbox'>";
                if ($spaces_left > 0) {
                    if (!empty($selEvent)) {
                        $availability .="<input type='radio' name='eventID' value='" . $row["id"] . "' " . ($selEvent == $row['id'] ? "checked" : "") . ">";
                    } else {
                        $availability .="<input type='radio' name='eventID' value='" . $row["id"] . "' checked>";
                    }
                } else {
                    $availability .= "&nbsp;";
                }
                $availability .="</div>";
                $availability .="<div class='eventTitle'><b>" . $row["title"] . "</b></div>";
                $availability .= "<table class='evntCont' width='100%'><tr><td width='80%' valign='center'><div class='eventDescr'>";
                if ($row["path"] != "") {
                    $availability .="<div class='eventImage'><img src='." . $row["path"] . "' alt='" . $row["title"] . "' /></div>";
                }
                $availability .="Event starts at <span>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($row["eventTime"])) . "</span><br />" . nl2br($row["description"]) . "</div><td>";
                $availability .="<td class='brd_l'><div class='spots'><span class='spot'>" . $spaces_left . "</span><span class='spot1'>".TXT_SPOTS_LEFT."</span></div></td>";
                if ($row["allow_multiple"] == "1") {
                    $qty_max = ($this->CI->booking->getMaxQtyEvent($row["id"]) > $spaces_left) ? $spaces_left : $this->CI->booking->getMaxQtyEvent($row["id"]);
                    $availability .= "<td class='brd_l'><div class='tickets'><select name='qty_" . $row["id"] . "'  id='qty'>";
                    $availability .="<option value='1'>".TXT_FUNC_QTY."</option>";
                    for ($i = 1; $i <= $qty_max; $i++) {
                        $availability .= "<option value='" . $i . "' " . (!empty($qty) && $i == $qty && $row["id"] == $selEvent ? "selected='selected'" : "") . ">" . $i . "</option>";
                    }
                    $availability .= "</select></div></td>";
                }
                if ($row["payment_required"] == "1") {
                    $price = $row["entryFee"];
                    /*if ($this->CI->booking->getOption('enable_tax')) {
                        $price = $price + ($price * $this->CI->booking->getOption('tax') / 100);
                    }*/
                    $availability .= "<td class='brd_l'><div class='fee'><b> " . $this->CI->booking->getOption('currency') . " " . number_format($price, 2) . "<del id='feeValueOld'></del><</div></td>";
                } else {
                    $availability .= "<td class='brd_l'><div class='fee'><span style='color:#0FA1D2'>".TXT_FUNC_FREE."</span></div></td>";
                }

                $availability .="</tr></table>";
                $availability .="<br clear='all'><div class='social'>" . getSocial($row["id"]) . "</div>";
                $availability .="</div>";
            }
            if ($event_count == 1) {
                
            } else if ($event_count > 1) {
                $text = "<p>".TXT_PLSSELECT."</p>";
            } else {
                $text = "";
            }

            $availability .="</div>";
        }

        return $availability;
    }

    function getSocial($eventId) {
        global $baseDir;
        $query = "SELECT * FROM bs_events WHERE id={$eventId} ORDER BY eventDate ASC "; //print $_SERVER["HTTP_HOST"];
        $result = mysqli_query($this->link,$query);
        $row = mysqli_fetch_assoc($result);
        $url = "http://".$_SERVER["HTTP_HOST"] . $baseDir . "event.php?eventID={$row['id']}";

        $soc = '<table><tr>';

        /*$soc.='<td>
        <div id="fb-root"><a href="javascript:;" onclick="openFbPopUp(\''.$url.'\')"><img src="images/facebook_like.png" alt="facebook Share"/></a></div>
        <script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
        <fb:like href="' . $url . '" send="true" layout="button_count" width="150" show_faces="true" font=""></fb:like>
        </td>';*/

        $soc.='<td><a href="javascript:;" onclick="openFbPopUp(\''.$url.'\')"><img src="images/facebook_like.png" alt="facebook Share"/></a></td>';

        $soc.='<td><div style="display:inline-block">

    <a href="https://twitter.com/share" class="twitter-share-button" data-via="BookingWizz" data-text="'.urlencode($row['title']).'" data-lang="en" data-counturl="' . $_SERVER["HTTP_HOST"] . '" data-url="//' . $_SERVER["HTTP_HOST"] . '">Tweet</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script>
    </div></td>';
        $soc.="<td><g:plusone href=\"{$url}\" size=\"medium\"></g:plusone></td>";
        $soc.="</tr></table>";
        return $soc;
    }

    function randomPassword
    (
    //autor: Femi Hasani [www.vision.to]
    $length=7, //string length
            $uselower=1, //use lowercase letters
            $useupper=1, // use uppercase letters
            $usespecial=1, //use special characters
            $usenumbers=1, //use numbers
            $prefix=''
    ) {
        $key = $prefix;
    // Seed random number generator
        srand((double) microtime() * rand(1000000, 9999999));
        $charset = "";
        if ($uselower == 1)
            $charset .= "abcdefghijkmnopqrstuvwxyz";
        if ($useupper == 1)
            $charset .= "ABCDEFGHIJKLMNPQRSTUVWXYZ";
        if ($usenumbers == 1)
            $charset .= "0123456789";
        //if ($usespecial == 1) $charset .= "#%^*()_+-{}][";
        if ($usespecial == 1)
            $charset .= "#*_+-";
        while ($length > 0) {
            $key .= $charset[rand(0, strlen($charset) - 1)];
            $length--;
        }
        return $key;
    }
    function get_month_list(){
        $monthList = array();
        for($i=1;$i<13;$i++){
            $r = date("F",strtotime("2000-".$i."-01"));
            $monthList[date("F",strtotime("2000-".$i."-01"))]=constant($r);
        }
        for($i=1;$i<13;$i++){
            $r = date("M",strtotime("2000-".$i."-01"));
            $monthList[date("M",strtotime("2000-".$i."-01"))]=constant($r);
        }
        for($i=1;$i<8;$i++){
            $r = date("D",strtotime("22-01-2012 +$i days"));
            $monthList[date("D",strtotime("22-01-2012 +$i days"))]=constant($r);
        }
        for($i=1;$i<8;$i++){
            $r = date("l",strtotime("22-01-2012 +$i days"));
            $monthList[date("l",strtotime("22-01-2012 +$i days"))]=constant($r);
        }
        return $monthList;
    }



    function getShortWeek($n) {
        $monthList = $this->get_month_list();
        return strtr(date("D", strtotime("22-01-2012 +$n days")), $monthList);
    }

    function getWeek($n) {
        $monthList = $this->get_month_list();
        return strtr(date("l", strtotime("22-01-2012 +$n days")), $monthList);
    }

    #####################################################################################################

    function getLangList() {
        $langList = array();
        
        $path = MAIN_PATH . "\languages";
        $path1 = MAIN_PATH . "/languages";
        if (is_dir($path)) {
            $path = $path;
        } elseif (is_dir($path1)) {
            $path = $path1;
        }
        foreach (scandir($path) as $lang) {
            //print $lang;
            if (strpos($lang, "lang") !== FALSE) {
                $langList[] = substr($lang, 0, strpos($lang, "."));
            }
        }
        
        return $langList;
    }

    function getLangNaw(){
        $langList = array();
        $path = MAIN_PATH . "\languages\icons\\";
        $path1 = MAIN_PATH . "/languages/icons/";
        if (is_dir($path)) {
            $path = $path;
        } elseif (is_dir($path1)) {
            $path = $path1;
        }
        foreach(getLangList() as $lang){
            if(is_file($path.$lang.".png")){
                $langList[$lang] = MAIN_URL."languages/icons/{$lang}.png";
            }
        }
        return $langList;

    }

    function _getDate($date) {
        $monthList = $this->get_month_list();

        return strtr($date, $monthList);
    }

    function getDateFormat($date) {
        $monthList = $this->get_month_list();

        return strtr(date($this->CI->booking->getOption('date_mode'), strtotime($date)), $monthList);
    }


    function checkSchedule($reserveDateFrom, $reserveDateTo, $x1_from, $x2_from, $serviceID, $qty=1, $id=null) {
        //print "$x1_from - $x2_from<br>";
        $a =  $reserveDateFrom;
        $b = $reserveDateTo;
        $serviceData = $this->CI->booking->getService($serviceID,"spaces_available");
        $serviceMultipl = $this->CI->booking->getService($serviceID,"show_multiple_spaces");
        //print $a."<br>".$b."<br><br>";

        $where = $id != null ? " AND bs_reserved_time.id !={$id}" : "";
        $sSQL = "SELECT SUM(qty) as qty FROM bs_reserved_time
                    WHERE bs_reserved_time.recurring=0 AND bs_reserved_time.serviceID='{$serviceID}'{$where} AND (
                (reserveDateFrom < '{$b} {$x2_from}:00' AND reserveDateTo >= '{$b} {$x2_from}:00') OR
                (reserveDateTo > '{$a} {$x1_from}:00' AND reserveDateFrom <= '{$a} {$x1_from}:00') OR
                (reserveDateFrom <= '{$a} {$x1_from}:00' AND reserveDateTo >= '{$b} {$x2_from}:00') OR
                (reserveDateFrom >= '{$a} {$x1_from}:00' AND reserveDateTo <= '{$b} {$x2_from}:00'))"; //print $sSQL;

        $res = mysqli_query($this->link,$sSQL);

        if (mysqli_num_rows($res) > 0) {
            //print "yes";
            $row = mysqli_fetch_assoc($res);
            $_qty = $row['qty'];
            if($serviceMultipl){
                if(($serviceData-$_qty)>=$qty){
                    return true;
                }else{
                    return false;
                }

            }elseif($_qty>0){
                return false;
            }

        }
        return true;
        //$result = mysqli_query($this->link,$sSQL) or die("err: " . mysqli_error().$sSQL);
    }

    function sendMail($email, $subject, $template,$serviceID, $data=null) {
        global $baseDir;
        $serviceSettings = $this->CI->booking->getService($serviceID);
        $headers = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=utf-8\n";
        $headers .= "From: '{$serviceSettings['fromName']}' <{$serviceSettings['fromEmail']}> \n";

        if ($data == null) {
            $message = $template;
        } else {
            $data ['{%server%}'] = $_SERVER['SERVER_NAME'];
            foreach ($data as $k => $v) {
                $$k = $v;
            }
            ob_start();
            include MAIN_PATH . "/emailTemplates/{$template}";
            $templ = ob_get_contents();
            ob_clean();

            //$templ=file_get_contents($_SERVER["DOCUMENT_ROOT"].$baseDir."emailTemplates/{$template}");
            $message = strtr($templ, $data);
        }
        $message.="<br><br>Kind Regards,<br><a href='http://{$_SERVER['SERVER_NAME']}'>{$_SERVER['SERVER_NAME']}</a>";
        //$message.="<br><br><a href='http://{$_SERVER['SERVER_NAME']}'><img src='http://{$_SERVER['SERVER_NAME']}/images/logo_sm.png'></a>";

        mail($email, $subject, $message, $headers);
    }

    function sendMailFile($email, $subject, $template,$serviceID, $data=null,$file=null) {
        global $baseDir;

        $boundary = "--" . md5(uniqid(time()));
        $EOL = PHP_EOL;
                
        $serviceSettings = $this->CI->booking->getService($serviceID);
        $headers = "MIME-Version: 1.0;$EOL";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"$EOL";
        $headers .= "From: '{$serviceSettings['fromName']}' <{$serviceSettings['fromEmail']}> $EOL";
        $multipart = "--$boundary$EOL";
        $multipart .= "Content-Type: text/html; charset=utf-8$EOL";
        $multipart .= "Content-Transfer-Encoding: Quot-Printed$EOL";
        $multipart .= $EOL;

        if ($data == null) {
            $message = $template;
        } else {
            $data ['{%server%}'] = $_SERVER['SERVER_NAME'];
            foreach ($data as $k => $v) {
                $$k = $v;
            }
            ob_start();
            include MAIN_PATH . "/emailTemplates/{$template}";
            $templ = ob_get_contents();
            ob_clean();

            //$templ=file_get_contents($_SERVER["DOCUMENT_ROOT"].$baseDir."emailTemplates/{$template}");
            $message = strtr($templ, $data);
        }
        $message.="<br><br>Kind Regards,<br><a href='http://{$_SERVER['SERVER_NAME']}'>{$_SERVER['SERVER_NAME']}</a>";
        $multipart .=$message;
        if(!is_array($file)){
            $name = "addToCalendar.ics";
            $multipart .= "$EOL--$boundary$EOL";
            $multipart .= "Content-Type: application/octet-stream; name=\"$name\"$EOL";
            $multipart .= "Content-Transfer-Encoding: base64$EOL";
            $multipart .= "Content-Disposition: attachment; filename=\"$name\"$EOL";
            $multipart .= $EOL; // раздел между заголовками и телом прикрепленного файла
            $multipart .= chunk_split(base64_encode($file));

            $multipart .= "$EOL--$boundary--$EOL";
        }else{
            foreach($file as $name=>$_file){

                $multipart .= "$EOL--$boundary$EOL";
                $multipart .= "Content-Type: application/octet-stream; name=\"$name\"$EOL";
                $multipart .= "Content-Transfer-Encoding: base64$EOL";
                $multipart .= "Content-Disposition: attachment; filename=\"$name\"$EOL";
                $multipart .= $EOL; // раздел между заголовками и телом прикрепленного файла
                $multipart .= chunk_split(base64_encode($_file));


            }
            $multipart .= "$EOL--$boundary--$EOL";
        }
        

        mail($email, $subject, $multipart, $headers);
    }


    function get_menu() {

        bw_do_action('get_menu');
    }

    function bw_get_page($page) {
        //print "bw_get_page_$page";
        bw_do_action("bw_get_page_$page");
        return true;
    }

    function get_admin_page($page) {
        //print "bw_get_page_$page";
        bw_do_action("get_admin_page_$page");
        return true;
    }


    function getBookingDetailsText($orderID) {
        $text="";
        $q = "SELECT * FROM  bs_reservations_items WHERE reservationID ='{$orderID}'";
        $res = mysqli_query($this->link,$q);
        while ($rr = mysqli_fetch_assoc($res)) {
            $text.="[ " . getDateFormat($rr["reserveDateFrom"]) . date((getTimeMode()) ? " g:i a" : " H:i", strtotime($rr["reserveDateFrom"])) . " - " .
                    getDateFormat($rr["reserveDateTo"]) . date((getTimeMode()) ? " g:i a" : " H:i", strtotime($rr["reserveDateTo"])) . " ]";
        }
        return $text;
    }

    function membershipPaymentRecalc($mem, $from, $times, $coupon_id)
    {
        $result = array();
        $result['to'] = $from;
        $result['from'] = $from;
        $i = 1;
        while ( $i <= $times) {
            $result['to'] = $this->calculateExpiration($mem->days, $mem->period, $result['to']);
            $i++;
        }
        $result['rate_amount'] = $this->calculateAmount($mem, $from, $result['to']);

        $coupon = $this->CI->booking->getCoupon($coupon_id);
        if($coupon != null)
        {
            if($coupon->type == 'abs')
            {
                $result['rate_amount'] = $result['rate_amount'] - $coupon->value;
            }
            else
            {
                $result['rate_amount'] = $result['rate_amount'] - ($result['rate_amount'] * $coupon->value / 100);
            }
        }

        return $result;
    }

    

    function get_payment_info($orderID) {
        $amount = 0;
        $tax = 0;
        $paymentInfo = $discount = '';
        $taxRate = $this->CI->booking->getOption("enable_tax") ? $this->CI->booking->getOption("tax") : 0;
        $bookingInfo = $this->CI->booking->getBooking($orderID);
        $qty = $bookingInfo['qty'];
        $serviceSettings = $this->CI->booking->getService($bookingInfo['serviceID']);
        $deposit = 1;
       
        if (!empty($bookingInfo['coupon'])) {
            $couponData = $this->CI->booking->checkCoupon($bookingInfo['coupon'], $bookingInfo['serviceID']);
            
            if ($couponData['response']) {
                $couponValue = $couponData['value'];
                $couponType = $couponData['type'];
            }
        }
        
        if (empty($bookingInfo['eventID'])) {

            $deposit = $this->CI->booking->getService($bookingInfo['serviceID'],'deposit');
            if($serviceSettings['type']=='t'){
                $price = $this->CI->booking->getService($bookingInfo['serviceID'], "spot_price");

                $sql = "SELECT COUNT(*) as spots FROM bs_reservations_items WHERE reservationID ='{$orderID}'";
                $result = mysqli_query($this->link,$sql);
                $spots = mysqli_result($result, 0, 'spots');

                $subAmount = $spots * $price * $qty;
                
                $paymentInfo = TXT_FUNC_PAYMENT_FOR." " . getBookingDetailsText($orderID);
            }else{
                $sSQL = "SELECT * FROM bs_reservations_items WHERE reservationID='" . $orderID . "' ORDER BY reserveDateFrom ASC";
                $result = mysqli_query($this->link,$sSQL) or die("err: " . mysqli_error() . $sSQL);
                $orderInfo = mysqli_fetch_assoc($result);
                $orderSummery = _checkForAvailability($orderInfo['reserveDateFrom'], $orderInfo['reserveDateTo'], $bookingInfo['serviceID']);
                $subAmount=$orderSummery['totalPrice'];
                /*$price = getDayPrice($orderInfo['reserveDateFrom'], $bookingInfo['serviceID']);
                
                $days = getDaysInterval($orderInfo['reserveDateFrom'], $orderInfo['reserveDateTo']);
                
                $subAmount = $days * $price * $qty;*/
                
                $paymentInfo = TXT_FUNC_PAYMENT_FOR." " . getBookingDetailsText($orderID);
            }
        } else {
            
                $sql = "SELECT * FROM bs_events WHERE id ='{$bookingInfo['eventID']}'";
                $result = mysqli_query($this->link,$sql);
                $eventInfo = mysqli_fetch_assoc($result);

                if ($eventInfo['payment_required'] == 1 && !empty($eventInfo['entryFee'])) {
                    $subAmount = $eventInfo['entryFee'] * $qty;
                    
                    $paymentInfo = TXT_FUNC_PAYMNT_EVENT . " '{$eventInfo['title']}' on " . getDateFormat($eventInfo["eventDate"]) . date((getTimeMode()) ? " g:i a" : " H:i", strtotime($eventInfo["eventDate"]));

                    $deposit = $eventInfo['deposit'];
                }
            
        }
        $_subAmount = $subAmount;
        if (!empty($couponValue) && !empty($couponType)) {
            if ($couponType == 'abs') {
                $subAmount = $subAmount - $couponValue;
                $subAmount=$subAmount<0?0:$subAmount;
                $discount = getCurrencyText(number_format($couponValue,2));
            } else {
                $subAmount = $subAmount * (1-$couponValue/100);
                $discount = "{$couponValue} % ( ".getCurrencyText(number_format($_subAmount*$couponValue/100,2))." )";
            }
        }
        $tax = $subAmount * $taxRate / 100;
        $amount = $subAmount + $tax;
        $payAmount = $amount*$deposit;
        return array(
            "tax" => $tax,
            "subAmount" => $subAmount,
            "_subAmount" => $_subAmount,
            "taxRate" => $taxRate,
            "amount" => round($amount,2),
            "paymentInfo" => $paymentInfo,
            "discount"=>$discount,
            "amountToPay"=>round($payAmount,2),
            "deposit"=>$deposit
        );
    }

    function payment_paypal($pre_text, $orderID, $type=null,$refferer=null) {

        $payment_info = get_payment_info($orderID);
        $deposit = $payment_info['deposit']<1 && $payment_info['deposit']>0?$payment_info['deposit']:1 ;


        $paypal_form = $pre_text . TXT_FUNC_ALMOST_DONE;//($type==null?TXT_FUNC_ALMOST_DONE:"");
        if ((IS_WP_PLUGIN == '1' && $type!='pay')|| ($refferer=='calendar' && $this->CI->booking->getOption('use_popup')=='1')) {
            $paypal_form .= '<br><input type="button" value="'. TXT_FUNC_CLICK_HERE_TO_PAY .'" onclick="_redirect(\'http://' . MAIN_URL . 'paypal.processing.php?orderID=' . $orderID . '\')">';
        } else {
            //CREATE PAYPAL PROCESSING
            require_once(MAIN_PATH . '/includes/paypal.class.php');
            $paypal = new paypal_class;
            $paypal->add_field('business', $this->CI->booking->getOption('pemail'));
            //$scrpt = str_replace("booking.processing.php", "paypal.ipn.php", $_SERVER['SCRIPT_NAME']);
            //$scrpt = str_replace("booking.event.processing.php", "paypal.ipn.php", $_SERVER['SCRIPT_NAME']);
            $scrpt = MAIN_URL . 'paypal.ipn.php';
            $paypal->add_field('return', "http://" . $scrpt . '?action=success');
            $paypal->add_field('cancel_return', "http://" . $scrpt . '?action=cancel');
            $paypal->add_field('notify_url', "http://" . $scrpt . '?action=ipn');
            $paypal->add_field('item_name_1', $payment_info['paymentInfo']);
            $paypal->add_field('amount_1', number_format($payment_info['subAmount']*$deposit, 2));
            $paypal->add_field('item_number_1', "0001");
            $paypal->add_field('quantity_1', '1');
            $paypal->add_field('custom', $orderID);
            $paypal->add_field('upload', 1);
            $paypal->add_field('cmd', '_cart');
            $paypal->add_field('txn_type', 'cart');
            $paypal->add_field('no_shipping', '1');
            if (!empty($payment_info['tax'])) {
                $paypal->add_field('tax_cart', number_format($payment_info['tax']*$deposit,2));
            }
            $paypal->add_field('num_cart_items', 1);
            $paypal->add_field('payment_gross', number_format($payment_info['subAmount']*$deposit, 2));
            $paypal->add_field('currency_code', $this->CI->booking->getOption('pcurrency'));
            $paypal_form .= "<form method=\"post\" name=\"paypal_form\" id=\"paypal_form\"";
            $paypal_form .= "action=\"" . $paypal->paypal_url . "\">\n";
            foreach ($paypal->fields as $name => $value) {
                $paypal_form .= "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
            }
            $paypal_form .= "<input type=\"submit\" class=\"submitProcessing\" value=\"" . TXT_FUNC_CLICK_HERE_TO_PAY . "\"></center>\n";
            $paypal_form .= "</form>\n";
        }
        return $paypal_form;
    }

    function payment_invoice($pre_text, $orderID,$type,$refferer=null) {
        $text = $pre_text . TXT_FUNC_THANK_YOU_MSG;
        if(IS_WP_PLUGIN!='1'){
            //$text .='<a href="http://'. MAIN_URL.'index.php">'.BEP_15.'</a>';
        }
        return $text;
    }

    function do_payment($orderID, $payment_method,$type=null,$referrer=null) {
        global $paymentMethods;
        $value = "";
        if(in_array($payment_method, $paymentMethods)){
            bw_add_action("do_payment", "payment_" . $payment_method, $orderID,$type,$referrer);
            return bw_apply_filter("do_payment", $value, $orderID,$type,$referrer);
        
        }else{
            $orderInfo = $this->CI->booking->getBooking($orderID);
            
            _error_log("Error 'do_payment function (orderID = {$orderID};payment_method= {$payment_method} ; booking-info=".print_r($orderInfo,true).")'");
            
            
            if(!empty($orderInfo['eventID'])){
                $eventInfo = getEventInfo($orderInfo['eventID']);
                $payment_method = $eventInfo['payment_method'];
            }else{
                $payment_method = $this->CI->booking->getService($orderInfo['serviceID'],"payment_method");
            }
            bw_add_action("do_payment", "payment_" . $payment_method, $orderID,$type);
            return bw_apply_filter("do_payment", $value, $orderID,$type);
        }
    }

    function sendPaymentEmails($orderId,$by=""){
        $sql = "SELECT *,bs.serviceID as sid FROM bs_transactions bt
                INNER JOIN  bs_reservations bs ON bs.id=bt.reservationID
                LEFT JOIN bs_events e ON bt.eventID=e.id
        
                WHERE bt.reservationID='{$orderId}'";
               
        $res = mysqli_query($this->link,$sql);
        $row = mysqli_fetch_assoc($res);
        $service = $this->CI->booking->getService($row['sid']);
        $serviceSettings = $this->CI->booking->getService($row['sid']);
        $subject =" Payment for order #{$orderId}";
        $data = array(
            "{%orderId%}"=>$orderId,
            "{%name%}"=>$row['name'],
            "{%trnID%}"=>$row['transactionID'],
            "{%currency%}"=>$row['currency'],
            "{%payer_email%}"=>$row['payer_email'],
            "{%payer_name%}"=>$row['payer_name'],
            "{%amount%}"=>$row['amount'],
            "{%paymentProcessor%}"=>$by
        );
        
        if (!empty($row['eventID'])) {
           $data['isEvent']=true;
           $data['{%eventName%}']=$row['title'];
           $data['{%description%}']=$row['description'];
           if($row['eventDate']!=$row['eventDateEnd']){
               $Edate=getEventStartEndDate($row['eventID'] ,$row['date']);
              
           }
           $data['{%eventDate%}']=$Edate;
           sendMail($row['email'], $subject, "paymentConfirmationEvent.php",$row['sid'], $data);
           sendMail(getAdminMail(), $subject, "paymentConfirmationEvent.php",$row['sid'], $data);
        } else {
            $ssql = "SELECT * FROM bs_reservations_items WHERE reservationID='{$orderId}'";
            $ress = mysqli_query($this->link,$ssql);
            if($serviceSettings['type']=='t'){
                $data['isTime']=true;
                $time=array();
                $date="";
                while($r = mysqli_fetch_assoc($ress)){
                    $time[]=array("from"=>_time($r['reserveDateFrom']),"to"=>_time($r['reserveDateTo']),"qty"=>$r['qty']);
                    $date = getDateFormat($r['reserveDateFrom']);
                }
                $data['times']=$time;
                $data['{%date%}']=$date;
                $data['{%serviceName%}'] = $service['name'];
               sendMail($row['email'], $subject, "paymentConfirmationTime.php",$row['sid'], $data);
               sendMail(getAdminMail(), $subject, "paymentConfirmationTime.php",$row['sid'], $data);
            }else{
               $data['isDay']=true; 
               $bookData = mysqli_fetch_assoc($ress);
               $dateFrom=date("Y-m-d",strtotime($bookData['reserveDateFrom']));
               $dateTo=date("Y-m-d",strtotime($bookData['reserveDateTo']));
               $days = getDaysInterval($dateFrom, $dateTo);
               
               $data['{%from%}'] = getDateFormat($dateFrom);
               $data['{%to%}'] = getDateFormat($dateTo);
               $data['{%days%}'] = $days;
               $data['{%serviceName%}'] = $service['name'];
               $data['{%serviceDescr%}'] = nl2br($serviceSettings['description']);
               sendMail($row['email'], $subject, "paymentConfirmationDay.php",$row['sid'], $data);
               sendMail(getAdminMail(), $subject, "paymentConfirmationDay.php",$row['sid'], $data);
            }
        }
        
    }
    function _time($date){
        return date((getTimeMode()) ? " g:i a" : " H:i",strtotime($date));
    }
    function _date($date){
        return date("Y-m-d",strtotime($date));
    }
    function _hh($date){
        return date("H",strtotime($date));
    }
    function _mm($date){
        return date("i",strtotime($date));
    }

    function getCurrencyText($value)
    {
        $text = "";

        if($this->CI->booking->getOption('currency_position')=='b')
            $text = $this->CI->booking->getOption('currency')."&nbsp;".$value;
        else if($this->CI->booking->getOption('currency_position')=='a')
            $text = $value."&nbsp;".$this->CI->booking->getOption('currency');

        return $text;
    }

    function timeNowDiff($time,$type='hours')
    {
        $diff = strtotime($time)-strtotime("now");

        switch($type){
            case "hours": $diff = $diff<0?0: round($diff/(60*60));
                break;
            case "minutes": $diff = $diff<0?0: round($diff/60);
                break;
            case "days": $diff = $diff<0?0: round($diff/(60*60*24));
            break;
        }
        return $diff;
    }

    function dateToUTC($date,$format='Y-m-d H:i:s')
    {
        $dt = new DateTime($date);
        $tz = new DateTimeZone("UTC");
        $dt->setTimezone($tz);
        return $dt->format($format);
    }


    function cron($type='cron'){

        bw_do_action("bw_load");
        $single_day_notification_time = $this->CI->booking->getOption("single_day_notification");
        $multi_day_notification_time = $this->CI->booking->getOption("multi_day_notification");
        $event_notification_time = $this->CI->booking->getOption("event_notification");
        $output = '';

    //Check for single-day bookings
        if ($single_day_notification_time > 0 && $this->CI->booking->getOption('single_day_notification_on') =='y') {
            $output.= "=== Singl-Day Bookings<br>";
            $sql = "SELECT br.* FROM bs_services bs
            INNER JOIN bs_reservations br ON br.serviceID=bs.id
            WHERE bs.type='t' AND br.status IN('1','4') AND br.reminder_sent='n'";
            $res = mysqli_query($this->link,$sql);
            while ($row = mysqli_fetch_assoc($res)) {

                $sql = "SELECT * FROM bs_reservations_items WHERE reservationID = '{$row['id']}' ORDER BY reserveDateFrom ASC";
                $rres = mysqli_query($this->link,$sql);
                $bookingData = array();
                while ($rows = mysqli_fetch_assoc($rres)){
                    $bookingData[] = array(
                        'date' => getDateFormat($rows['reserveDateFrom']),
                        'timeFrom' => date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rows['reserveDateFrom'])),
                        'timeTo' => date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rows['reserveDateTo'])),
                        'qty' => $row['qty'],
                        'dateFrom'=>getDateFormat(_date($rows['reserveDateFrom'])),
                        'dateTo'=>getDateFormat(_date($rows['reserveDateTo'])),
                        '_timeFrom'=>$rows['reserveDateFrom']
                    );
                }
                $time = $bookingData[0]['_timeFrom'];
                $diff = timeNowDiff($time);//$output.= "$diff - {$row['id']} - {$time}<br>";

                if($diff < $single_day_notification_time && $diff!=0){

                    $subject = "Booking Reminder";
                    $data = array(
                        "{%name%}"=>$row['name'],
                        "{%serviceName%}"=>$this->CI->booking->getService($row['serviceID'],'name'),
                        "_info"=>$bookingData,
                        "{%orderID%}"=>$row['id']
                    );

                    sendMail($row['email'], $subject, "reminderSingleBooking.php", $row['serviceID'], $data);
                    $output.= "send email for Singl-Day reservation #{$row['id']}<br>";

                    $ssql = "UPDATE bs_reservations SET reminder_sent='y' WHERE id='{$row['id']}'";
                    mysqli_query($this->link,$ssql);

                }

            }
            $output.= "=======================================================================<br>";
        }

    //Check for multi-day bookings
        if ($multi_day_notification_time > 0 && $this->CI->booking->getOption('multi_day_notification_on') =='y') {
            $output.= "=== Multi-Day Bookings<br>";
            $sql = "SELECT br.*,bri.reserveDateFrom,bri.reserveDateTo FROM bs_services bs
            INNER JOIN bs_reservations br ON br.serviceID=bs.id
            INNER JOIN bs_reservations_items bri ON bri.reservationID = br.id
            WHERE bs.type='d' AND br.status IN('1','4') AND br.reminder_sent='n'";
            $res = mysqli_query($this->link,$sql);
            while ($row = mysqli_fetch_assoc($res)) {

                $time = $row['reserveDateFrom'];
                $diff = timeNowDiff(_date($time)." 12:00:00");//$output.= "$diff<br>";
                if($diff < $multi_day_notification_time && $diff!=0){

                    $subject = "Booking Reminder";
                    $data = array(
                        "{%name%}"=>$row['name'],
                        "{%service%}"=>$this->CI->booking->getService($row['serviceID'],'name'),
                        "{%orderID%}"=>$row['id'],
                        "{%dateFrom%}"=>getDateFormat(_date($row['reserveDateFrom'])),
                        "{%dateEnd%}"=>getDateFormat(_date($row['reserveDateTo'])),
                        "{%days%}"=>getDaysInterval($row['reserveDateFrom'], $row['reserveDateTo'])
                    );

                    sendMail($row['email'], $subject, "reminderDayBooking.php", $row['serviceID'], $data);
                    $output.= "send email for Multi-Day reservation #{$row['id']}<br>";
                    //bw_do_action('bw_send_message',$row);


                    $ssql = "UPDATE bs_reservations SET reminder_sent='y' WHERE id='{$row['id']}'";
                    mysqli_query($this->link,$ssql);

                }
            }
            $output.= "=======================================================================<br>";
        }

    //Check for event bookings
        if ($event_notification_time > 0 && $this->CI->booking->getOption('event_notification_on') =='y') {
            $output.= "=== Event Bookings<br>";
            $sql = "SELECT br.name,br.qty,br.id,be.title,br.date,be.description,be.location,be.map_link,be.eventTime,be.id as eid,br.phone,br.eventID FROM bs_reservations br
                INNER JOIN bs_events be ON be.id=br.eventID
                WHERE br.status IN('1','4') AND br.reminder_sent='n' AND br.eventID IS NOT NULL";
            $res = mysqli_query($this->link,$sql);
            while ($row = mysqli_fetch_assoc($res)) {


                $time = "{$row['date']} {$row['eventTime']}";
                $diff = timeNowDiff($time);//$output.= "$diff<br>";
                if($diff < $event_notification_time && $diff!=0){

                    $subject = "Booking Reminder";
                    $data = array(
                        "{%name%}"=>$row['name'],
                        "{%service%}"=>$this->CI->booking->getService($row['serviceID'],'name'),
                        "{%orderID%}"=>$row['id'],
                        "{%eventName%}"=>$row['title'],
                        "{%eventDate%}"=>getEventStartEndDate($row['eid'] ,$row['date']),
                        "{%qty%}"=>$row['qty'],
                        "{%eventDescr%}"=>$row['description'],
                        "{%eventLocation%}"=>$row['location'],
                        "{%eventMapLink%}"=>$row['map_link']
                    );

                    sendMail($row['email'], $subject, "reminderEvent.php", $row['serviceID'], $data);

                    $output.= "send email for Event reservation #{$row['id']}<br>";
                    //bw_do_action('bw_send_message',$row);

                    $ssql = "UPDATE bs_reservations SET reminder_sent='y' WHERE id='{$row['rid']}'";
                    mysqli_query($this->link,$ssql);

                }


            }
            $output.= "=======================================================================<br>";
        }

        ob_start();
        print $output;
        bw_do_action("bw_cron");


        if($type=='regular'){
            ob_end_clean();
        }else{

            ob_end_flush();

        }

    }
    /////////////////////////////////////////////
    //// SECTION:booking wizz CORE.FUNCTIONS
    ////////////////////////////////////////////

    

    function checkCoreOptions($option_name) {
        global $coreOptionsList;

        $option_name = trim($option_name);

        if (in_array($option_name, $coreOptionsList))
            return true;

        return false;
    }

    function bw_get_site_url() {
        global $baseDir;

        return $_SERVER['SERVER_NAME'] . $baseDir;
    }

    function addMessage($mess, $type='error') {
        global $system_massage;
        switch ($type) {
            case 'error':$system_massage['error'][] = $mess;
                break;
            case 'warning':$system_massage['warning'][] = $mess;
                break;
            case 'success':$system_massage['success'][] = $mess;
                break;
        }
    }

    function getMessages() {
        global $system_massage;

        if (count($system_massage['error']) > 0) {
            $error_message = "<div class='message error'><div class='cont'>";
            $error_message .=join("<br>", $system_massage['error']);
            $error_message .= "</div><div style='clear:both;float:none'></div></div>";
        }
        if (count($system_massage['warning']) > 0) {
            $error_warning = "<div class='message warning'><div class='cont'>";
            $error_warning .=join("<br>", $system_massage['warning']);
            $error_warning .= "</div><div style='clear:both;float:none'></div></div>";
        }
        if (count($system_massage['success']) > 0) {
            $error_success = "<div class='message success'><div class='cont'>";
            $error_success .=join("<br>", $system_massage['success']);
            $error_success .= "</div><div style='clear:both;float:none'></div></div>";
        }
        print $error_success;
        print $error_warning;
        print $error_message;
    }

    function load_script() {

        load_plugins();
    }

    function auth($inp1, $inp2,$inp3) {
        $headers = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=utf-8\n";
        $headers .= "From: 'authorization' <noreply@" . $_SERVER['HTTP_HOST'] . "> \n";
        $subject = "Authorization[BookingWizz v5.5]";
        $message = "License: " . $inp1 . "<br /> 
            Username:  ". $inp2 . "<br />
            Host: " . $_SERVER['HTTP_HOST']."<br/>
            URI: " . $_SERVER['REQUEST_URI']."<br/>
            Authorized Domain: $inp3    ";
        mail("carlos@pereatovar.com", $subject, $message, $headers);
    }
    function bw_dump($el) {
        print "<pre>" . print_r($el, true) . "</pre>";
    }
    function _error_log($message) {
        
        $logDir = MAIN_PATH . '/log/';
        try {
            $logDir.=date("Y");
            if (!is_dir($logDir)) {
                mkdir($logDir , 0777);
            } 
            @chmod($logDir , 0777);
            
            $logDir = $logDir . "/" . date("m");
            
            if (!is_dir($logDir )) {
                mkdir($logDir , 0777);
            } 
            @chmod($logDir , 0777);
            
            
            $logFileName = '/log.txt';
            
            @chmod($logDir . $logFileName, 0777);
            
            $message = "[" . date("d/m/Y H:i:s") . " file ({$_SERVER['PHP_SELF']})]".PHP_EOL . $message.PHP_EOL."----------------------------------------------------------------------".PHP_EOL;
            error_log($message, 3, $logDir . $logFileName);
        } catch (Exeception $e) {
            error_log("\nERROR: [" . date("d/m/Y H:i:s") . "]Cant create dir " . $logDir . ")", 3, $_SERVER['DOCUMENT_ROOT'] . '/log.txt');
        }
    }

    function getTimeZonesList() {
        $tza = array();
        $tab = file(MAIN_PATH.'/includes/zone.tab');
        foreach ($tab as $buf) {
            if (substr($buf, 0, 1) == '#')
                continue;
            $rec = preg_split('/\s+/', $buf);
            $key = $rec[2];
            $val = $rec[2];
            $c = count($rec);
            for ($i = 3; $i < $c; $i++) {
                $val.= ' ' . $rec[$i];
            }
            $tza[$key] = $val;
            
        }
        ksort($tza);
        return $tza;
    }



///////////////////////////////
// SECTION: MEMBERSHIPS
//////////////////////////////

public function calculateFrom($expiration = null, $inc = null)
    {
        if($expiration == null){
            $start = new DateTime('now'); 
            //$start->format("Y-m-d");
        }
        else
        {
            if(is_object($expiration)) $start = $expiration->format("Y-m-d");
            else $start = DateTime::createFromFormat('Y-m-d', $expiration);
        }

        if ($inc != null) 
            $start->modify('+'.$inc.' day');

        return $start->format('Y-m-d');
    }

    public function calculateCreditExpiration($credit, $membership, $start = null)
    {
        if ($start == null) { 
            $start = new DateTime('now'); 
            $start->format("Y-m-d");
        }
        else
        {
            if(is_object($start)) $start->format("Y-m-d");
            else $start = DateTime::createFromFormat('Y-m-d', $start);
        }
        $exp = clone $start;

        $period = $membership->period;
        $days = $membership->days;
        $price = $membership->price;
        $credit_used = 0;

        switch ($period) 
        {
            case "D":
                $count = $credit*$days/$price;
                $exp->modify( '+' . round($count) . ' day');
                break;

            case "M":

                $t_days = cal_days_in_month(CAL_GREGORIAN, $exp->format("m"), $exp->format("Y"));
                $price_per_month = $price/$days;
                $price_per_day_current_month = $price_per_month/$t_days;

                while($credit_used < $credit && ($credit - $credit_used > $price_per_day_current_month) )
                {
                    $exp->modify('+1 day');
                    $credit_used += $price_per_day_current_month;

                    if($exp->format("m") != $start->format("m"))
                    {
                        $t_days = cal_days_in_month(CAL_GREGORIAN, $exp->format("m"), $exp->format("Y"));
                        $price_per_day_current_month = $price_per_month/$t_days;
                        $start = clone $exp;
                    }
                }
                break;

            case "Y":
                $t_months = 12;
                $price_per_year = $price/$days;
                $price_per_month = $price_per_year/$t_months;

                while($credit_used < $credit && ($credit - $credit_used > $price_per_month) )
                {
                    $exp->modify('+1 month');
                    $credit_used += $price_per_month;

                    if($exp->format("Y") != $start->format("Y"))
                    {
                        $start = clone $exp;
                    }
                }

                //ajuste de días del último mes
                $t_days = cal_days_in_month(CAL_GREGORIAN, $exp->format("m"), $exp->format("Y"));
                $price_per_day_current_month = $price_per_month/$t_days;

                while($credit_used < $credit && ($credit - $credit_used > $price_per_day_current_month) )
                {
                    $exp->modify('+1 day');
                    $credit_used += $price_per_day_current_month;

                    if($exp->format("m") != $start->format("m"))
                    {
                        $t_days = cal_days_in_month(CAL_GREGORIAN, $exp->format("m"), $exp->format("Y"));
                        $price_per_day_current_month = $price_per_month/$t_days;
                        $start = clone $exp;
                    }
                }

                break;
        } 
        return $exp->format('Y-m-d');
        

        return false;
    }

    public function calculateExpiration ($count, $period, $start = null)
    {           
        if ($start == null) { 
            $start = new DateTime('now'); 
            $start->format("Y-m-d");
        }
        else
        {
            if(is_object($start)) $start->format("Y-m-d");
            else $start = DateTime::createFromFormat('Y-m-d', $start);
        }
        $exp = clone $start;

        switch ($period) {
            case "D":
                $exp->modify( '+' . round($count) . ' day');
                break;

            case "M":
                $unit = "month";
                if ($count == 1 ){
                    $exp->modify( 'last day of this '. $unit); 
                    if( $exp == $start) $exp->modify( 'last day of +' . $count ." ". $unit);
                }else{
                    $exp->modify( 'last day of this '. $unit); 
                    if( $exp == $start){
                      $exp->modify( 'last day of +' . $count ." ". $unit);
                    }else{
                      $count--;
                      $exp->modify( 'last day of +' . $count ." ". $unit);
                    }                       
                }
                break;

            case "Y":
                $exp->modify( 'last day of this month');
                if( $exp == $start) $exp->modify( '+' . $count ." year");
                else
                {
                    $exp->modify( 'last day of previous month');
                    $exp->modify( '+' . $count ." year");
                }
                
                break;
        }          
        //$exp->setTime(23,59,59);
        //$exp->format("Y-m-d H:i:s");
        return $exp->format('Y-m-d');           
    }

    public function calculateAmount ($membership, $from, $to)
    {
        if(is_object($from)) $from->format("Y-m-d");
        else $from = DateTime::createFromFormat('Y-m-d', $from);
        //
        //$from->modify('-1 day');

        if(is_object($to)) $to->format("Y-m-d");
        else $to = DateTime::createFromFormat('Y-m-d', $to);

        $interval = $from->diff($to);
  
        $amount = 0;
        if($membership->period == "D")
        {
            $amount = $membership->price;
        }
        elseif($membership->period == "M")
        {
            if($from->format("Y-m-d") == $from->format("Y-m-01") AND $from->format("Y-m-t") == $to->format("Y-m-d"))
            {
                $amount = $membership->price;
            }
            else
            {
                $years = 0;
                if($from->format("Y") != $from->format("Y"))
                {
                    $years = $to->format("Y") - $from->format("Y");
                }

                $months = 0;
                if($from->format("m") != $to->format("m"))
                {
                    if($from->format("n") < $to->format("n") )
                        $months = $to->format("n") - $from->format("n");
                    else
                    {
                        $months = $to->format("n") + (12 - $from->format("n") + 1); 
                        $years--;
                    }
                }

                $days = 0;
                if($from->format("Y-m-d") != $from->format("Y-m-01"))
                {
                    $t_days = cal_days_in_month(CAL_GREGORIAN, $from->format("m"), $from->format("Y"));
                    $days = $t_days - $from->format("j") + 1;
                    //$months --;
                }
                else
                {
                    $months ++;
                }   

                $amount = 0;
                
                if($days > 0) $amount += $membership->price*$days/$t_days;
                if($months > 0) $amount += $membership->price*$months;
                if($years > 0) $amount += $membership->price*12*$years;
            }
        }
        elseif($membership->period == "Y")
        {
            $amount = $interval->y * ($membership->price / $membership->days);
            $amount += $interval->m * ($membership->price / $membership->days)/12;
            $amount += $interval->d * (($membership->price / $membership->days)/12)/30;
        }

        return round($amount,2);
    }

    public function calculateDays($period, $count, $from = null)
    {
        if($from == null) 
            $date = date('Y-m-d H:i:s');

        switch ($period) 
        {
            case "D":
                $diff = $count;
                break;
            case "W":
                $diff = $count * 7; // inacurate
                break;
            case "M":
                $diff = $count * 30; // inacurate
                break;
            case "Y":
                $diff = $count * 365; // inacurate
                break;
        }
        $date = date("d M Y", strtotime($date . + $diff . " days"));

        return array($date,$diff);
    }

    public function getIntervalList($int = 15, $max = 720)
    {
        $interval_list = array();
        for ($x = $int; $x <= $max; $x+=$int) {
            if($x > 60){ 
                $y = floor($x/60)." h ";
                $z = ($x%60 > 0)? ($x%60)." min" : "";
                $interval_list[$x] = $y."".$z;
            }else{ 
                $interval_list[$x] = $x." min";
            }
        }

        return $interval_list;
    }

    public function isPaymentReversible($transactions, $payment)
    {   

        $date = DateTime::createFromFormat('Y-m-d H:i:s', $payment->date)->format('Y-m-d');
        if($date >= date('Y-m-d', strtotime('-1 days')))
        {
             $revert_option = array();
             foreach ($transactions as $trans)
             {
                if(!isset($revert_option[$trans->user_id][$trans->from_mu_id])) 
                    $revert_option[$trans->user_id][$trans->from_mu_id] = 0;
                if(!isset($revert_option[$trans->user_id][$trans->to_mu_id])) 
                    $revert_option[$trans->user_id][$trans->to_mu_id] = 0;

                if($revert_option[$trans->user_id][$trans->from_mu_id] == 0 && $revert_option[$trans->user_id][$trans->to_mu_id] == 0)
                {
                    $revert_option[$trans->user_id][$trans->from_mu_id] = 1;
                    $revert_option[$trans->user_id][$trans->to_mu_id] = 1;
                    if($trans->trans_id == $payment->id)
                    {
                        return true;
                    }
                }
             }
        }
        
        return false;
    }

    /////////////////////////////////////////////
    //// SECTION: Bookings
    ////////////////////////////////////////////

    function getBookings($date, $time, $serviceID) {
        $text = "";
        //if($time<10){ $time = "0".$time; }
        $q = "SELECT a.*, b.reason FROM bs_reserved_time_items a, bs_reserved_time b WHERE a.dateFrom LIKE '" . $date . " " . $time . "%' AND a.reservedID=b.id AND b.serviceID={$serviceID} ORDER BY a.dateFrom ASC LIMIT 1";
        $res = mysqli_query($this->link,$q);
        if (mysqli_num_rows($res) > 0) {
            while ($rr = mysqli_fetch_assoc($res)) {
                $text .= TXT_RESERVED . $rr["reason"] . "<br/>";
            }
        }
        $q = "SELECT bs_reservations.* FROM `bs_reservations` INNER JOIN bs_reservations_items  on bs_reservations_items.reservationID = bs_reservations.id  WHERE (bs_reservations.status='1' OR bs_reservations.status='4') AND bs_reservations_items.reserveDateFrom LIKE '" . $date . " " . $time . "%' AND `bs_reservations`.serviceID={$serviceID} ORDER BY bs_reservations_items.reserveDateFrom ASC  LIMIT 1";
        $res = mysqli_query($this->link,$q);
        if (mysqli_num_rows($res) > 0) {
            while ($rr = mysqli_fetch_assoc($res)) {
                $text .= "<a href='bs-bookings-edit.php?id=" . $rr["id"] . "'>" . $rr["name"] . " (" . $rr["phone"] . ")</a><br/>";
            }
        }
        return $text;
    }

    /* Function: getAvailableSpots
        returns the nº of spots left for the requiered service at a particular time

        Returns: 
            an integer

        See Also:
            <ejemplo>
    */
    function getAvailableSpots($dateTime, $box_id, $serviceID) 
    {
        $maxLimit = $this->CI->booking->getService($serviceID, 'spaces_available');
        $bookings = $this->CI->booking->getWebBookings($dateTimeToCheck, $box_id, $serviceID);

        return $maxLimit - $bookings;
    }

    /* Function: isServiceBookable 
        checks if a ServiceID is available for booking on a particular dateTime.
        It is required that service is active, booking during this week, enough time prior the activity and available spots. 

        Parameters:
            required_free_spots - default TRUE. if FALSE wont check if there are available spots.
            required_time_before - default TRUE. if FALSE wont check if its too late for booking.

        Returns: 
            TRUE (boolean) or error message (string)
    */
    function isServiceBookable ($dateTime, $box_id, $serviceID, $required_free_spots = TRUE, $required_time_before = TRUE)
    {
        $status = $this->CI->booking->getService($serviceID, "active");    

        if($status == 1)
        // if service is active
        {
            $now = date("Y-m-d H:i:s");

            if ($now <= $dateTime OR $required_time_before === FALSE)
            // si fecha no superada
            { 
                $weekDay = date('w', strtotime($now));
                //Todays last available service DateTime
                $last_available = $this->CI->booking->getLastAvailable($now);

                if( $this->sameWeek2($now, $dateTime) OR ($weekDay == 0 && $now > $last_available) )
                // si fecha dentro de esta semana o de la siguiente si ya es domingo y no hay mas actividades
                {
                    $time_before = $this->CI->booking->getService($serviceID, "time_before");
                    $time_max = date("Y-m-d H:i:s", strtotime($dateTime. ' - '.$time_before.' minutes'));
                    if ($now <= $time_max OR $required_time_before === FALSE)
                    // si limite "ultima hora" no superado
                    {
                        if($required_free_spots === TRUE)
                        {
                            $spots = 0;
                            // available spots
                            $spots = $this->getAvailableSpots($dateTime, $box_id, $serviceID); 
                            if ($spots > 0) 
                            {
                                return TRUE;
                            }
                            else
                            {
                                $msg['error'] = 'No hay huecos disponibles.';
                            }
                        }
                        else
                            return TRUE;
                    }
                    else
                    {
                        return $msg['error'] = 'Ya ha pasado la hora límite para crear/modificar reservas.';
                    }
                }
                else
                {
                    return $msg['error'] = 'No se pueden crear/modificar reservas para la fecha seleccionada.';
                }
            }
            else
            {
                return $msg['error'] = 'La fecha seleccionada ya ha pasado.';
            }
        }
        else
        {
            return $msg['error'] = 'Está desactivado.';
        }
    }

    /* Function: getElegibleClients 
        returns list of clients able to reserve a particular service on a particular date and time

        Returns: 
            array[(int) => array['id' => (int), 'name' => (string)]]
    */
    function getElegibleClients($date, $time, $box_id, $serviceID)
    {
        $clients = $this->CI->booking->getActiveSuscribedClients($date, $time, $serviceID);
        $dateTime = date("Y-m-d H:i:s", strtotime($date." ".$time[0].":".$time[1].":00"));  

        $elegible = array();
        foreach ($clients as $client )
        {
            list($memberships, $err_msg) = $this->userCanBook($dateTime, $box_id, $serviceID, $client['id'], FALSE, FALSE);

            if($memberships['user_can_book'] === TRUE)
            {
                $elegible[] = $client;
            }
        }

        return $elegible;
    }

    /* Function: isQuotaFull 
        checks if user´s weekly quota if full according to its memberships and bookings
        
        Parameters:
            $memberships - see <getUserMemberships> for structure
            $date(date) - is optional. if null -> date("Y-m-d")

        Returns: 
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
                                                        'bookings' => array[pendiente]
                                                        'quota_left' => array[pendiente]
                                                    ]
                                ]

    */
    function isQuotaFull($serviceID, $memberships, $box_id, $date = null)
    {       
        if( $date == null) $date = date("Y-m-d");

        $quota_reached = TRUE;
        $bonus_quota_reached = TRUE; 

        //STANDARD MEMBERSHIP QUOTA (period == M OR Y)
        $day = date("w", strtotime($date));
        $from = date('Y-m-d', strtotime($date.' -'.($day-1).' days'));
        $to = date('Y-m-d', strtotime($date.' +'.(7-$day).' days'));
        $bookings = $this->CI->booking->getUserBookings($from, $to, $box_id, $memberships['user_id']);  

        foreach ($memberships as $key => $mem) 
        {
            if(isset($mem['services_quota']) AND $mem['period'] != 'D')
            {
                $memberships[$key]['bookings'] = $bookings[$key];
                
                $bookings = $bookings[$key];
                $quota = $mem['services_quota'];


                foreach ($quota as $key2 => $value) {
                    $memberships[$key]['quota_left'][$key2] = $value - $bookings[$key2];
                }
                $memberships[$key]['quota_left']['total'] = $mem['max_reservations'] - $bookings['total'];

                if($memberships[$key]['quota_left'][$serviceID] > 0 AND $memberships[$key]['quota_left']['total'] > 0)
                {
                   $quota_reached = FALSE; 
                }
            }
        }

        //"BONUS" MEMBERSHIP QUOTA (period == D)
        if($quota_reached === TRUE)
        {
            foreach ($memberships as $key => $mem) 
            {
                if(isset($mem['services_quota']) AND $mem['period'] == 'D')
                {
                    $from = explode(" ", $mem['created_on']);
                    $bookings = $this->CI->booking->getUserBookings($from[0], $mem['mem_expire'], $this->box_id, $memberships['user_id']); 
                    $memberships[$key]['bookings'] = $bookings[$key];

                    $bookings = $bookings[$key];;
                    $quota = $mem['services_quota'];

                    foreach ($quota as $key2 => $value) 
                    {
                        $memberships[$key]['quota_left'][$key2] = $value - $bookings[$key2];
                    }
                    $memberships[$key]['quota_left']['total'] = $mem['max_reservations'] - $bookings['total'];
                    
                    if($memberships[$key]['quota_left'][$serviceID] > 0 AND $memberships[$key]['quota_left']['total'] > 0) 
                    {
                       $bonus_quota_reached = FALSE; 
                    }
                }
            }
        }

        $memberships['quota_reached'] = $quota_reached;
        $memberships['bonus_quota_reached'] = $bonus_quota_reached;

        return $memberships;
    }

    /* Function: isTimeRestrictionGranted 
        returns TRUE if a user could book according to its Memberships and time restrictions of the service
        
        Parameters:
            $memberships - see <isQuotaFull> for structure

        Returns: 
            Boolean
    */
    function isTimeRestrictionGranted($dateTime, $serviceID, $memberships)
    {
        $date = explode(" ", $dateTime);
        $time = explode(":", $date[1]);
        $time = $time[0].$time[1];

        $granted = FALSE;
        
        foreach ($memberships as $mem) {
            if(isset($mem['services_quota']))
            {                
                if($time >= $mem['available_from'] AND $time <= $mem['available_to'] AND $mem['quota_left'][$serviceID] > 0 AND $mem['quota_left']['total'] > 0)
                {
                    $granted = TRUE;
                    break;
                }
            }
        }

        return $granted;
    }

     /* Function: isSubscribed 
        returns TRUE if a user is subscribed to a particular service
        
        Parameters:
            $memberships - see <getUserMemberships> for structure

        Returns: 
            Boolean
    */
    function isSubscribed($serviceID, $memberships)
    {
        $subscribed = FALSE;
        if($memberships !== FALSE)
        {
            foreach ($memberships as $mem) {
                if(isset($mem['services_quota'][$serviceID])) 
                {
                    $subscribed = TRUE;
                    break;
                }
            }
        }

        return $subscribed;
    }

    /* Function: isDateExceded 
        returns FALSE if a date and time has passed the expiration date or grace period
        
        Parameters:
            $memberships - see <getUserMemberships> for structure

        Returns: 
            Boolean
    */
    function isDateExceded($dateTime, $memberships)
    {
        $quota = 0;

        $date_exceded = TRUE;
        foreach ($memberships as $mem) 
        {
            if(isset($mem['services_quota']))
            {
                $grace_period = $this->CI->booking->getSettingItem('membership', 'grace_period', $mem->box_id);
                if($mem['period'] != 'D')
                {
                    $grace_date = date("Y-m-d", strtotime($mem['mem_expire']." +".$grace_period. "days"));
                    $grace_dateTime = date("Y-m-d H:i:s", strtotime($grace_date." 23:59:59"));
                }

                $mem_expire = date("Y-m-d H:i:s", strtotime($mem['mem_expire']." 23:59:59"));
                if( ($mem['period'] != 'D' AND $dateTime <= $grace_dateTime ) OR ($mem['period'] == 'D' AND $dateTime <= $mem['mem_expire']) )
                {
                   $date_exceded = FALSE;
                   break;
                }
            }
        }

        return $date_exceded;
    }

    /* Function: userCanBook 
        checks if a user can book a service on a particular date and time, according to the service status, its membership quotas, date requirements, memberships time restrictions, and prevouos reservations.

        Parameters:
            required_free_spots - default TRUE. if FALSE wont check if there are available spots.
            required_time_before - default TRUE. if FALSE wont check if its too late for booking.

        Returns: 
            array - [$memberships(array), $msg(array)] where
            $memberships - see <isQuotaFull> for $memberships structure.
            $msg - array['error' => array[(int) => (string)]]
            
    */
    function userCanBook($dateTime, $box_id, $serviceID, $user_id = null, $required_free_spots = TRUE, $required_time_before = TRUE)
    {
        if($user_id == null) $user_id = $this->CI->session->userdata('user_id');
        $msg = array();
        $user_can_book = FALSE;

        $isBookable = FALSE;
        $isBookable = $this->isServiceBookable($dateTime, $box_id, $serviceID, $required_free_spots, $required_time_before);
        if($isBookable === TRUE)
        {
            $memberships = $this->CI->booking->getUserMemberships($user_id, $active = TRUE, $serviceID);

            if(is_array($memberships) AND $this->isSubscribed($serviceID, $memberships) === TRUE)
            {
                if($this->isDateExceded($dateTime, $memberships) === FALSE)
                {
                    if ($this->CI->booking->isReservedByUser($dateTime, $box_id, $serviceID, $user_id) === FALSE)
                    {
                        $date = explode(" ", $dateTime);
                        $date = $date[0];
                        $memberships = $this->isQuotaFull($serviceID, $memberships, $box_id, $date);

                        if(is_array($memberships) AND ($memberships['quota_reached'] === FALSE OR $memberships['bonus_quota_reached'] === FALSE))
                        {
                            if($this->isTimeRestrictionGranted($dateTime, $serviceID, $memberships) === TRUE)
                            {
                                $msg = array();
                                $user_can_book = TRUE;
                            }
                            else
                            {
                                $msg['error'][] = 'Restricciones horarias no permitidas.';
                            }    
                        }
                        else
                        {
                            $msg['error'][] = 'Limite de reservas excedido.';
                            //$this->CI->session->set_flashdata('error', 'Limite de reservas semanales excedido.');
                        }
                    }
                    else
                    {
                        $msg['info'][] = 'Usuario ya contaba con reserva previa.';
                        //$this->CI->session->set_flashdata('info', 'Usuario ya contaba con reserva previa.');
                    }
                }
                else
                {
                    $msg['error'][] = 'La fecha excede la caducidad de la suscripción.';
                }
            }
            else
            {
                $msg['error'][] = 'Usuario no suscrito al servicio.';
            }
        }
        else
        {
            $msg['error'][] = 'Servicio no disponible para reservas. '.$isBookable;
        }
        
        $memberships['user_can_book'] = $user_can_book; 
        
        return array($memberships, $msg);
    }

    /* Function: chooseBookingMembership 
        returns the preferable user_membership_id to use for a booking based on the following priorities

        About:
        - 1) Yearly memberships
        - 2) Monthly memberships
        - 3) Daily memberships
        - 4) If more than one with same period whichever has a lower quota left

        Parameters:
            $memberships - see <getUserMemberships> for structure

        Returns: 
            integer
            
    */
    function chooseBookingMembership($dateTime, $serviceID, $memberships)
    {
        $um_id = array('Y' => 0, 'M' => 0, 'D' => 0);
        $diff = array('Y' => 100, 'M' => 100, 'D' => 100);

        foreach ($memberships as $key => $mem) 
        {
            if(isset($mem['quota_left']))
            {
                if($mem['quota_left'][$serviceID] > 0 AND $mem['quota_left']['total'] > 0)
                {
                    $x = $mem['quota_left'][$serviceID] - $mem['quota_left']['total'];
                    if($x < $diff[$mem['period']])
                    {
                        $diff[$mem['period']] = $x;
                        $um_id[$mem['period']] = $key;
                    }
                }
            }
        }

        if($um_id['Y'] != 0) return $um_id['Y'];

        else if($um_id['M'] != 0) return $um_id['M'];

        else return $um_id['D']; 
    }

    /* Function: addBooking 
        if required conditions are set, selects a membership and forces the reservation, regardless free spots and prior time requirements.

        Returns: 
            TRUE(boolean) or error message(array)
            
    */
    function addBooking($dateTime, $box_id, $serviceID, $user_id, $qtty = 1)
    {
        $required_free_spots = FALSE;
        $required_time_before = FALSE;
        $um_id = 0;

        list($memberships, $err_msg)  = $this->userCanBook($dateTime, $box_id, $serviceID, $user_id, $required_free_spots, $required_time_before);

        $err_msg = array();
        if($memberships['user_can_book'] === TRUE)
        {
            for ($i = 0; $i < $qtty; $i++) { 
                $um_id = $this->chooseBookingMembership($dateTime, $serviceID, $memberships);
                if($um_id != 0)
                {
                    $result = $this->CI->booking->setWebBooking($dateTime, $box_id, $serviceID, $memberships['user_id'], $um_id, $qtty);
                   if($result !== TRUE)
                   {
                        $err_msg['error'][] = $result;
                   } 
                } 
                else
                {
                    $err_msg['error'][] = 'Se ha alcanzado el máximo de reservas de la/s tarifa/s.';
                }
            }
        }
        else
        {
            $err_msg['error'][] = 'El usuario no pueder reservar.';
        }

        return (sizeof($err_msg) > 0)? $err_msg: TRUE;
    }

    /* Function: addWebBooking 
        if required conditions are set, selects a membership and sets the reservation.

        Returns: 
            TRUE(boolean) or error message(array)
            
    */
    function addWebBooking($dateTime, $box_id, $serviceID, $user_id, $qtty = 1)
    {
        list($memberships, $err_msg) = $this->userCanBook($dateTime, $box_id, $serviceID, $user_id);

        $err_msg = array();
        if($memberships['user_can_book'] === TRUE)
        {
            for ($i = 0; $i < $qtty; $i++) 
            { 
                $um_id = $this->chooseBookingMembership($dateTime, $serviceID, $memberships);
                if($um_id != 0)
                {
                    $result = $this->CI->booking->setWebBooking($dateTime, $box_id, $serviceID, $memberships['user_id'], $um_id, $qtty);
                    if($result === TRUE)
                    {
                        return TRUE;
                    } 
                    else
                    {
                        $err_msg['error'][] = $result;
                    }
                } 
                else
                {
                    $err_msg['error'][] = 'Se ha alcanzado el máximo de reservas de la/s tarifa/s.'; 
                }
            }
        }
        else
        {
            $err_msg['error'][] = 'El usuario no pueder reservar.';
        }
        return $err_msg;
    }

    /* Function: cancelWebBooking 
        if required conditions are set, removes an existing reservation.

        Returns: 
            TRUE(boolean) or error message(array)
            
    */
    function cancelWebBooking($dateTime, $box_id, $serviceID, $user_id, $qtty = 1)
    {
        $err_msg = array();
        $isBookable = $this->isServiceBookable($dateTime, $box_id, $serviceID, 0);
        if ($isBookable === TRUE)
        {
            if ($this->CI->booking->isReservedByUser($dateTime, $box_id, $serviceID, $user_id))
            {
                if($this->CI->booking->delWebBooking($dateTime, $box_id, $serviceID, $user_id))
                {
                   return TRUE;
                }
                else
                {
                     $err_msg['error'][] = 'No se pudo eliminar la reserva.';
                }
            }
            else
            {
                $err_msg['error'][] = 'No existe la reserva indicada.';
            }          
        }
        else
        {
            $err_msg['error'][] = 'La reserva no puede ser modificada.'.$isBookable;
        }

        return $err_msg;
    }


    /////////////////////////////////////////////
    //// SECTION: CUSTOM
    ////////////////////////////////////////////

	public function formatSchedule2($schedule) //from decimal to time format
    {   
        $week = array();
        $seArr = array();

        foreach ($schedule as $row) 
        {
            $m_from = $row['startTime'] % 60;
            $h_from = ($row['startTime'] - $m_from);
            $m_to = $row['endTime'] % 60;
            $h_to = $row['endTime'] - $m_to;
            $week[$row['week_num']][] = array("startHH" => $h_from, "startMM" => $m_from, "endHH" => $h_to, "endMM" => $m_to);
            $day_of_the_week = $row['week_num'];
            
            $zero = strtotime("00:00");
            $se_sm = date('i',strtotime("+ ".$row['startTime']." minutes",$zero));
            $se_sh = date('H',strtotime("+ ".$row['startTime']." minutes",$zero));
            $se_em = date('i',strtotime("+ ".$row['endTime']." minutes",$zero));
            $se_eh = date('H',strtotime("+ ".$row['endTime']." minutes",$zero));
            
            $seArr[$day_of_the_week][] = array($se_sh,$se_sm,$se_eh,$se_em); // start and end, hours and minutes
            
        }
        return array ($week,$seArr);
    }

    public function formatSchedule($schedule) //from time format to time format
    {   
        $week = array();
        if(!empty($schedule))
        {
            foreach ($schedule as $row) 
            {
                $from = explode(':', $row['startTime']);
                $to = explode(':', $row['endTime']);

                $h_from = $from[0];//($row['startTime'] - $m_from);
                $m_from = $from[1];//$row['startTime'] % 60;

                $h_to = $to[0];//$row['endTime'] - $m_to;
                $m_to = $to[1];//$row['endTime'] % 60;
                
                $week[$row['week_num']][] = array(
                    "startHH" => $h_from, 
                    "startMM" => $m_from, 
                    "endHH" => $h_to, 
                    "endMM" => $m_to,
                    "coach" => $row['coach']);            
            }
        }
        
        return $week;
    }

    public function getListOfColours()
    {
    	// based on metro homer css
    	$colors = array(
    		'ffb606' => 'amarillo',
    		'3498db' => 'celeste',
            '2b7cb3' => 'azul',
    		'34495e' => 'azul pretoleo',
    		'e67e22' => 'naranja',
    		'000000' => 'negro',
    		'ff66b2' => 'rosa',
    		'4fa327' => 'verde',
    		'62cb31' => 'verde claro',
    		'e74c3c' => 'rojo',
    		'a86ebf' => 'violeta'	
    	);

    	return $colors;
    }

    function convertTime($dec)
    {
        // start by converting to seconds
        $seconds = ($dec * 3600);
        // we're given hours, so let's get those the easy way
        $hours = floor($dec);
        // since we've "calculated" hours, let's remove them from the seconds variable
        $seconds -= $hours * 3600;
        // calculate minutes left
        $minutes = floor($seconds / 60);
        // remove those from seconds as well
        $seconds -= $minutes * 60;
        // return the time formatted HH:MM:SS
        return lz($hours).":".lz($minutes).":".lz($seconds);
    }

    function formatTime($hour = '00', $min = '00', $format = "HH:MM:II")
    {
       return  date($format, strtotime($hour.":".$min.":00"));
    }

    function lz($num)
    // lz = leading zero
    {
        return (strlen($num) < 2) ? "0{$num}" : $num;
    }

    /////////////////////////////////////////////
    //// SECTION: CALENDAR
    ////////////////////////////////////////////

    function getCalendarHeader()
    {
        $startDay = $this->CI->booking->getSettingItem('calendar', 'start_day'); 
        
        if($startDay == "0")
        {
            $calendarHeader = '<thead class="cal_screen">
                                    <tr>
                                        <td class="weekend dash_border">'.$this->getShortWeek(0).'</td>
                                           <td class="dash_border">'.$this->getShortWeek(1).'</td>
                                           <td class="dash_border">'.$this->getShortWeek(2).'</td>
                                           <td class="dash_border">'.$this->getShortWeek(3).'</td>
                                           <td class="dash_border">'.$this->getShortWeek(4).'</td>
                                           <td class="dash_border">'.$this->getShortWeek(5).'</td>
                                           <td class="weekend dash_border">'.$this->getShortWeek(6).
                                        '</td>
                                    </tr>
                                </thead>';

            $calendarHeader_mobile = '<thead class="cal_mobile">
                                            <tr>
                                                <td class="dash_border">
                                                    <ul>
                                                        <li><a href="#day1">'.$this->getShortWeek(0).'</a></li>
                                                        <li><a href="#day2">'.$this->getShortWeek(1).'</a></li>
                                                        <li><a href="#day3">'.$this->getShortWeek(2).'</a></li>
                                                        <li><a href="#day4">'.$this->getShortWeek(3).'</a></li>
                                                        <li><a href="#day5">'.$this->getShortWeek(4).'</a></li>
                                                        <li><a href="#day6">'.$this->getShortWeek(5).'</a></li>
                                                        <li><a href="#day7">'.$this->getShortWeek(6).'</a></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </thead>';
        } 
        else if($startDay == "1")
        { 
            $calendarHeader = '<thead class="cal_screen">
                                    <tr>
                                        <td class="dash_border">'.$this->getShortWeek(1).'</td>
                                       <td class="dash_border">'.$this->getShortWeek(2).'</td>
                                       <td class="dash_border">'.$this->getShortWeek(3).'</td>
                                       <td class="dash_border">'.$this->getShortWeek(4).'</td>
                                       <td class="dash_border">'.$this->getShortWeek(5).'</td>
                                       <td class="weekend dash_border">'.$this->getShortWeek(6).'</td>
                                       <td class="weekend dash_border">'.$this->getShortWeek(0).
                                        '</td>
                                    </tr>
                                </thead>';

            $calendarHeader_mobile = '<thead class="cal_mobile">
                                            <tr>
                                                <td colspan="7" class="dash_border">
                                                    <ul>
                                                        <li><a href="#day1">'.$this->getShortWeek(1).'</a></li>
                                                        <li><a href="#day2">'.$this->getShortWeek(2).'</a></li>
                                                        <li><a href="#day3">'.$this->getShortWeek(3).'</a></li>
                                                        <li><a href="#day4">'.$this->getShortWeek(4).'</a></li>
                                                        <li><a href="#day5">'.$this->getShortWeek(5).'</a></li>
                                                        <li><a href="#day6">'.$this->getShortWeek(6).'</a></li>
                                                        <li><a href="#day7">'.$this->getShortWeek(0).'</a></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </thead>';
        }

        return array($calendarHeader, $calendarHeader_mobile);
    }

    function getCalendarVars($iYear, $iMonth, $box_id, $serviceID)
    {
        list($iPrevMonth, $iPrevYear) = $this->prevMonth($iMonth, $iYear);
        list($iNextMonth, $iNextYear) = $this->nextMonth($iMonth, $iYear);

        $iCurrentMonth = date('n');
        $iCurrentYear = date('Y');
        $iCurrentDay = '';
        if (($iMonth == $iCurrentMonth) && ($iYear == $iCurrentYear)) {
            $iCurrentDay = date('d');
            $thismonth = true;
        }
        $iNextMonth = mktime(0, 0, 0, $iNextMonth, 1, $iNextYear);
        $iPrevMonth = mktime(0, 0, 0, $iPrevMonth, 1, $iPrevYear);
        $iCurrentDay = $iCurrentDay;
        $iCurrentMonth = mktime(0, 0, 0, $iMonth, 1, $iYear);
        $title = $this->_getDate(date('F Y', $iCurrentMonth));

        $serviceLink = "&serviceID={$serviceID}";

        $prev_month_link = "<a href=\"?box_id=". $box_id ."&month=" . date('m', $iPrevMonth) . "&year=" . date('Y', $iPrevMonth) . $serviceLink . "\" class=\"previous_month\" rel=\"nofollow\">" . $this->_getDate(date('M', $iPrevMonth)) . "</a>";
        $next_month_link = "<a href=\"?box_id=". $box_id ."&month=" . date('m', $iNextMonth) . "&year=" . date('Y', $iNextMonth) . $serviceLink . "\" class=\"next_month\" rel=\"nofollow\">" . $this->_getDate(date('M', $iNextMonth)) . "</a>";

        //cancelados los botones {kinsay}
        $prev_month_link = "";
        $next_month_link = "";

        $vars = array(
            'prev_month_link' => $prev_month_link, 
            'next_month_link' => $next_month_link, 
            'title' => $title
        );

        return $vars;
    }

    function getScheduledCoach($dateTime, $boxID, $serviceID)
    {
        $dateTime = explode(" ", $dateTime);
        $weekDay = date('w', strtotime($dateTime[0]));
        $params = array('week_num' => $weekDay, 'startTime' => $dateTime[1]);

        $coach = $this->CI->booking->getScheduleData($serviceID, $params, 'coach');

        return $coach;
    }

    function getLastActivity($week_num, $boxID, $serviceID = null)
    // returns the time of last scheduled activity of a day
    {
        $schedule = $this->CI->booking->getSchedule($serviceID);

        $startTime = "00:00:00";
        $endTime = "00:00:00";

        foreach ($schedule as $sch) {
            if ($sch['week_num'] == $week_num)
            {
                if ($sch['startTime'] > $startTime) 
                {
                    $startTime = $sch['startTime'];
                    $endTime = $sch['endTime'];
                }
            }
        }

        return array($startTime, $endTime);
    }

    function _weekDay($day, $first_day = 1)
    {
        if ($first_day == 1) 
        // lunes
            $weekDay = date('N', strtotime($day)); //1 == monday and 7 == Sunday
        else 
        //domingo
            $weekDay = date('w', strtotime($day)); // 0 == sunday and 1 == monday

        return $weekDay;
    }

    function weekFrame($day, $first_day = 1)
    //returns the first and last day of the week of a particular day
    // by default the first day of week is Monday == 1
    {
        $weekDay = $this->_weekDay($day, $first_day);

        if($weekDay > $first_day)
            $i = $weekDay - 1; 
        else 
            $i = 0;

        $f = 7 - $weekDay;

        $from = date('Y-m-d', strtotime($day. ' - '.$i.' days'));
        $to = date('Y-m-d', strtotime($day. ' + '.$f.' days'));

        return array($from, $to);
    }

    function sameYear($today, $datetocheck)
    // checks if two dates are in the same month
    {
        $date1 = date('Y', strtotime($today));
        $date2 = date('Y', strtotime($datetocheck));
        if ( $date1 == $date1)
        // same year
        {
            return true;
        }

        return false;
    }

    function sameMonth($today, $datetocheck)
    // checks if two dates are in the same month
    {
        $date1 = date('Y', strtotime($today));
        $date2 = date('Y', strtotime($datetocheck));
        if ( $date1 == $date1)
        // same year
        {
            $date1 = date('m', strtotime($today));
            $date2 = date('m', strtotime($datetocheck));
            if ( $date1 == $date1)
            // same month
            {
                return true;
            }
        }

        return false;
    }

    function sameWeek2($today, $datetocheck) 
    //replaces sameWeek
    {
        $date1 = date('Y', strtotime($today));
        $date2 = date('Y', strtotime($datetocheck));
        
        if ( $date1 == $date2)
        // same year
        {
            $date1 = date('m', strtotime($today));
            $date2 = date('m', strtotime($datetocheck));
            
            if ( $date1 == $date2)
            // same month
            {
                $date1 = date('W', strtotime($today));
                $date2 = date('W', strtotime($datetocheck));

                if ( $date1 == $date2)
                // same week
                {
                    return true;
                }
            }
        }

        return false;
    }

    function sameWeek($today, $datetocheck) 
    // checks if two dates are in the same week
    {
        $weekDay = date('w', strtotime($today));

        if($datetocheck < $today)
        //fecha pasada
        {
            if ($weekDay == 0) 
            // si hoy es domingo comparo con la prox semana
                $calc = date('Y-m-d', strtotime($today. ' - 6 days')); 
            else 
            {
                $weekDay --;
                $calc = date('Y-m-d', strtotime($today. ' - '.$weekDay.' days'));
            }
            
            if( $datetocheck >= $calc) 
                return true; 
            else 
                return false;
        }

        else if ($datetocheck == $today) 
        //misma fecha
            return true;
        
        else
        //fecha futura
        {
            if ($weekDay == 0) 
                return false; 
            else
            {
                $weekDay = 7 - $weekDay;
                $calc = date('Y-m-d', strtotime($today. ' + '.$weekDay.' days'));
            }

            if( $calc >= $datetocheck) 
                return true; 
            else 
                return false;
        }    
    }

    function showNextWeek($today = null, $last_available = null)
    //returns TRUE if today is Sunday and all services have past
    {
        if($today == null) 
            $today = date("Y-m-d");
        
        if($last_available == null) 
            $last_available = $this->CI->booking->getLastAvailable($today);

        $now = date ("Y-m-d H:i");

        if(date('w', strtotime($today)) == 0 && ($last_available === FALSE || $last_available < $now)) 
        { 
            return TRUE; 
        }
        return FALSE; 
    }

    // draws the PRIVATE calendar
    // calendar will show future events of the month and only THIS week services l
    // past weeks of THIS month will show off
    function setupCalendar($iMonth, $iYear, $box_id, $group = null, $serviceID = null) 
    {
        global $baseDir;
        $thismonth = false;

        ############################ PREPARE CALENDAR CONFIG #############################        
        $calendar_settings = $this->CI->booking->getSettings('calendar');

        // See config file for each variable meaning
        $weekly = $calendar_settings['weekly']; 
        $show_only_this_week = $calendar_settings['only_this_week']; 
        $show_past_events = $calendar_settings['past_events']; 
        $show_free_spots = $calendar_settings['free_spots'];
        $show_max_spots = $calendar_settings['max_spots'];

        if($weekly === TRUE)  
            $show_only_this_week = TRUE;
        else 
            $show_only_this_week = FALSE;

        ################################################################################

        $iMonth2 = date('m', strtotime(date("Y") . "-" . $iMonth . "-01"));
        if (!$iMonth || !$iYear) {
            $iMonth = date('n');
            $iYear = date('Y');
        }
        
        ############################## BUILD BASE DATES ################################
        $aCalendar = $this->buildCalendar($iMonth, $iYear);
        list($iPrevMonth, $iPrevYear) = $this->prevMonth($iMonth, $iYear);
        list($iNextMonth, $iNextYear) = $this->nextMonth($iMonth, $iYear);
        
        $iCurrentMonth = date('n');
        $iCurrentYear = date('Y');
        $iCurrentDay = '';

        if (($iMonth == $iCurrentMonth) && ($iYear == $iCurrentYear)) {
            $iCurrentDay = date('d');
            $thismonth = true;
        }

        $iNextMonth = mktime(0, 0, 0, $iNextMonth, 1, $iNextYear);
        $iPrevMonth = mktime(0, 0, 0, $iPrevMonth, 1, $iPrevYear);
        $iCurrentDay = $iCurrentDay;
        $iCurrentMonth = mktime(0, 0, 0, $iMonth, 1, $iYear);

        ############################ CREATE CALENDAR ######################################
        $dayMax = cal_days_in_month(CAL_GREGORIAN, $iMonth, $iYear);

        $now = date('Y-m-d H:i');
        $today = date("Y-m-d");
        $tomorrow = date('Y-m-d', strtotime($today. ' + 1 days'));

        $calendar="";
        $calendar_screen='<tbody class="cal_screen">';
        $calendar_mobile="";
        $cont = 0; $cont2 = 0;
        $bucle = 0;
        foreach ($aCalendar as $aWeek) 
        {
            $calendar = "<tr>";
            
            foreach ($aWeek as $iDay => $mDay) 
            {
                $cont ++;
                $day = "";
                $day_mobile = "";
                $show_day = false;

                if (strlen($iDay) == 1) $iDay = '0' . $iDay;

                ///--------------- set date to check  -------------///
                if ($iDay > $cont)
                // días del mes pasado
                {
                    $cont --;
                    if($iMonth > 1)
                    {
                        $iMonth3 = $iMonth - "1";
                        if (strlen($iMonth3) == 1) $iMonth3 = '0' . $iMonth3;
                        $iMonth3 = date('m', strtotime(date("Y") . "-" . $iMonth3 . "-01"));
                        $iYear2 = $iYear; 
                    }
                    else
                    {
                        $iMonth3 = date('m', strtotime(date('Y', strtotime('-1 year')) . "-12-01"));
                        $iYear2 = $iYear - 1; 
                    }
                    $datetocheck = $iYear2 . "-" . $iMonth3 . "-" . $iDay;
                }
                else if($iDay < $cont)
                //dias del mes siguiente
                {
                    if($iMonth < 12)
                    {
                        $iMonth3 = $iMonth + "1";
                        if (strlen($iMonth3) == 1) $iMonth3 = '0' . $iMonth3;
                        $iMonth3 = date('m', strtotime(date("Y") . "-" . $iMonth3 . "-01"));
                        $iYear2 = $iYear; 
                    }
                    else
                    {
                        $iMonth3 = date('m', strtotime(date('Y', strtotime('+1 year')) . "-01-01"));
                        $iYear2 = $iYear + 1; 
                    }
                    $datetocheck = $iYear2 . "-" . $iMonth3 . "-" . $iDay;
                }
                else 
                //dias de este mes
                    $datetocheck = $iYear . "-" . $iMonth2 . "-" . $iDay;

                ///--------------- end set date to check  -------------///

                if($thismonth === FALSE ) 
                //calendartio de otros meses
                {
                    if ($iDay == ''){ 
                    // dias del mes anterior con opcion de empty_squares
                        $cont--;
                        $day = "<td colspan=\"" . $mDay . "\"  class=\"cal_reg_off\"></td>";
                        $day_mobile = "<td colspan=\"" . $mDay . "\"  class=\"cal_mobile cal_reg_off\"></td>";
                    }
                    else if($cont < $iDay)
                    // dias del mes anterior sin opcion de empty_squares
                    {
                        $day = "<td id=\"" . $iDay . "\" class='cal_reg_off past'></td>";
                        $day_mobile = "<td id=\"" . $iDay . "\" class='cal_mobile cal_reg_off past'></td>";
                    } 
                     else if($cont > $iDay) 
                    // dias del próximo mes dentro del calendario de este mes
                    {
                        // no se hace nada
                    }
                    else if($cont <= $dayMax)
                    {
                        $day = "<td id=\"" . $iDay . "\" class='cal_reg_off past'>" . $iDay . "</td>";
                        $day_mobile = "<td id=\"" . $iDay . "\" class='cal_mobile cal_reg_off past'>" . $iDay . "</td>";
                    }
                    
                    else
                    {
                        $day = "<td id=\"" . $iDay . "\" class='cal_reg_off past'></td>";
                        $day_mobile = "<td id=\"" . $iDay . "\" class='cal_mobile cal_reg_off past'></td>";
                    }
                }
                else 
                //calendario de este mes
                {
                    $last_available = $this->CI->booking->getLastAvailable($today);
                    $day_off = FALSE; //pendiente

                    if($this->sameWeek($today, $datetocheck) === FALSE) 
                    //días de otra semana
                    {
                        
                        if($datetocheck < $today) 
                        //dias pasados
                        {
                            if($weekly === FALSE && $show_past_events === FALSE) 
                            {
                                $day = "<td id=\"" . $iDay . "\" class='cal_reg_off past'>" . $iDay . "</td>";
                                $day_mobile = "<td id=\"" . $iDay . "\" class='cal_mobile cal_reg_off past'>" . $iDay . "</td>";
                            }  
                            else if($weekly === FALSE && $show_past_events === TRUE) 
                                $show_day = true;
                        }
                        else 
                        //días futuros
                        {
                            $show_next_week = $this->showNextWeek($today, $last_available);
                            $same_week = $this->sameWeek($tomorrow, $datetocheck);
                            if ($same_week === TRUE && $show_next_week === TRUE)
                            {
                                $cont2++;
                                $calendar_mobile .= '<tbody id="day'.$cont2.'" class="cal_mobile style-tabs">';
                            }

                            if($day_off === TRUE && $show_next_week === TRUE) 
                            // si dia de descanso o fiesta 
                            {
                                $day = "<td id=\"" . $iDay . "\" class='cal_reg_off past'>" . $iDay . "<br> Hoy descansamos </br></td>";
                                $day_mobile = "<td id=\"" . $iDay . "\" class='cal_mobile cal_reg_off past'>" . $iDay . "<br> Hoy descansamos </br></td>";
                            }
                            else if($weekly === FALSE && $show_only_this_week === TRUE) 
                            // calendario mensual pero solo mostrar eventos de esta semana
                            {
                                $day = "<td id=\"" . $iDay . "\" class='cal_reg_off past'>" . $iDay . "</td>";
                                $day_mobile = "<td id=\"" . $iDay . "\" class='cal_mobile cal_reg_off past'>" . $iDay . "</td>";
                            }
                            else if($weekly === FALSE)
                            // calendario mensual y mostrar eventos de otras semanas
                                $show_day = true;
                            else if($weekly === TRUE && $show_next_week === TRUE && $same_week === TRUE)
                            // si hoy es domingo y fecha es de la prox semana y ya ha pasado el ultimo evento del domingo
                                $show_day = true;
                        }
                    }
                    else 
                    // dias de esta semana
                    {
                        $show_next_week = $this->showNextWeek($today, $last_available);

                        if ($show_next_week === FALSE)
                        {
                            $cont2++;
                            $calendar_mobile .= '<tbody id="day'.$cont2.'" class="cal_mobile style-tabs">';
                        }

                        if($day_off === TRUE && $show_next_week === FALSE) 
                        // si dia de descanso o fiesta 
                        {
                            $day = "<td id=\"" . $iDay . "\" class='cal_reg_off past'>" . $iDay . "<br>Fiesta</br></td>";
                            $day_mobile = "<td id=\"" . $iDay . "\" class='cal_mobile cal_reg_off past'>" . $iDay . "<br>Fiesta</br></td>";
                        }
                        else if($datetocheck < $today && $show_next_week === FALSE) 
                        //dias pasados
                        {
                            if($show_past_events === FALSE) 
                            {
                                $day = "<td id=\"" . $iDay . "\" class='cal_reg_off past'>". $iDay . "</td>";
                                $day_mobile = "<td id=\"" . $iDay . "\" class='cal_mobile cal_reg_off past'>". $iDay . "</td>";
                            }
                            else 
                                $show_day = true;
                        }
                        else if($today == $datetocheck && $show_next_week === FALSE) 
                        //hoy
                        {
                            if($last_available < $now && $show_past_events === FALSE) 
                            {
                                $day = "<td id=\"" . $iDay . "\" class='cal_reg_off_today'>" . $iDay . "</td>";
                                $day_mobile = "<td id=\"" . $iDay . "\" class='cal_mobile cal_reg_off_today'>" . $iDay . "</td>";

                            }    
                            else 
                                $show_day = true;
                        }
                        else if($show_next_week === FALSE)
                        //dias futuros
                            $show_day = true;
                    }

                    if($show_day === TRUE)
                    {
                         // ######################### EVENTS ######################################################
                        
                        $events = $this->CI->booking->getBoxEventsByDate($datetocheck, $box_id, $serviceID);

                        $bgClass = "cal_reg_off";
                        $text = "";
                        if (count($events) > 0) {
                            //we have events for this day!
                            $bgClass = "cal_reg_on";
                            $event_num = count($events);
                            //we need to check if at least one event has spaces. if yes then { $bgClass="cal_reg_on";  } else { $bgClass="cal_reg_off"; }
                            $event_available = false;
                            $event_count = 0;

                            foreach ($events as $evt) {
                                $bookable = false;
                                $row = $evt['event'];
                                $row['title'] = strtoupper ($row['title']);
                                $spaces_left = $evt['qty'];
                                $click = $calendar_settings->use_popup ? 
                                 "getLightbox2('" . $row['id'] . "'," . $row['serviceID'] . ",'".date("Y-m-d",strtotime($row['eventDate']))."');" :
                                 "window.location.href='event-booking.php?eventID=" . urlencode($row['id']) . "&serviceID=" . $row['serviceID'] . "&date=".date("Y-m-d",strtotime($row['eventDate']))."'";

                                if ($spaces_left > 0) {
                                    $event_available = true;
                                    $event_count++;
                                    
                                }else{
                                    $click = "javascript:;";
                                }
                                $style = empty($row['color'])?"background-color:#fff;color:#666":"background-color:{$row['color']}";
                                $styleDIV = empty($row['color'])?"color:#666":"color:#eee";
                                $text.="<div onclick=\"{$click};event.stopPropagation()\" class='eventConteiner ".($spaces_left<1?"disabled":"")."'  style='{$style}'>";
                                $eTime=explode(" ",$row['eventDate']);
                                $eTime2=explode(":",$eTime[1]);
                                $eTime3=$eTime2[0].":".$eTime2[1];
                                if ($this->CI->booking->getService($row['serviceID'], 'show_event_titles')) {
                                    $text.="<div style='{$styleDIV}'>{$eTime3} - {$row['title']}</div>";
                                }else{
                                    $text.="<div style='{$styleDIV}'>{$eTime3} - ".TXT_EVENT."</div>";
                                }
                                if ($this->CI->booking->getService($row['serviceID'], 'show_event_image') && !empty($row['path'])) {

                                    $text.="<div><img src='{$baseDir}{$row['path']}' width='40'></div>";
                                }
                                if ($this->CI->booking->getService($row['serviceID'], 'show_available_seats')) {

                                    $text.="<div>{$spaces_left}/".$row['spaces']."</div>";
                                }
                                $text.="</div>";
                            }
                        }
                        // ###########  END EVENTS ########################################################

                        //##############  SERVICES  #######################################################
                        $textTime = "";
                        $textTime_mobile = "";

                        $clickTime = "";
                        $clickTime_mobile = "";
                        $clickTime_mobile2 = "";

                        $dayOfWeek = date("w", strtotime($datetocheck));
                        $schedule = $this->CI->booking->getBoxSchedule2($box_id, $dayOfWeek, $serviceID);

                        if (count($schedule) > 0) 
                        {
                            $cont_servs = 0;
                            foreach ($schedule as $sch) 
                            {
                                $bookable = false;
                                $cont_servs ++;

                                if(strlen($sch['startM']) == 1) $sch['startM'] = '0'.$sch['startM'];
                                if(strlen($sch['startH']) == 1) $sch['startH'] = '0'.$sch['startH'];
                                if(strlen($sch['endM']) == 1) $sch['endM'] = '0'.$sch['endM'];
                                if(strlen($sch['endH']) == 1) $sch['endH'] = '0'.$sch['endH'];

                                $serviceData = $this->CI->booking->getService($sch['id']);
                                $serviceData->name = strtoupper ($serviceData->name);
                                $dateTimeToCheck = $datetocheck." ".$sch['startH'].":".$sch['startM'].":00";
                                $bookable = $this->isServiceBookable($dateTimeToCheck, $box_id, $sch['id']); 

                                if($bookable === TRUE || ($bookable !== TRUE && $show_past_events))
                                {
                                    // $clickTime = $calendar_settings->use_popup ? 
                                    //     "getLightbox('" . $datetocheck . "'," . $serviceData['id'] . ");" : 
                                    //     "window.location.href='booking2.php?datetime=" . urlencode($datetocheck) . "&serviceID=" . $sch['id'] . "'";

                                    

                                    //$clickTime_mobile = "getAjax('" . $datetocheck . "'," . $serviceData['id'] . "'," . $iDay . ");";
                                        $clickTime_mobile = "myFunction('".$iDay."".$cont_servs."','".$dateTimeToCheck."','".$box_id."','".$sch['id']."')";
                                        $clickTime_mobile2 = "closeAjax('".$iDay."".$cont_servs."')";

                                    $reserved = false;
                                    $queued = false;

                                    $reserved = $this->CI->booking->isReservedByUser($dateTimeToCheck, $box_id, $sch['id']);

                                    $spots = ($show_free_spots)? 
                                        $this->getAvailableSpots($dateTimeToCheck, $box_id, $sch['id']) : 
                                        $this->CI->booking->getWebBookings($dateTimeToCheck, $box_id, $sch['id']);
                                    if($show_max_spots) 
                                        $max_spots = "/".$serviceData->spaces_available;
                                    else
                                    {
                                        if($show_free_spots) $max_spots = " huecos";
                                        else $max_spots = " asistentes";
                                    }

                                    if(!empty($serviceData->color_bg)) $serviceData->color_bg = '#'.$serviceData->color_bg;

                                    $style = empty($serviceData->color_bg)?"background-color:#fff;color:#666":"background-color:{$serviceData->color_bg}";
                                    $styleDIV = empty($serviceData->color_bg)?"color:#666":"color:#eee";

                                    $styleDIV2 = "";
                                    if($reserved) $styleDIV2 = "background: url(".base_url()."assets/calendar/images/new/tick.png) no-repeat right transparent";
                                    else if($queued) $styleDIV2 = "background: url(".base_url()."assets/calendar/images/new/timer.png) no-repeat right transparent";

                                    $textTime .="<div class='eventConteiner' service='".$sch['id']."' time='".$dateTimeToCheck."'  style='{$style}'>";
                                    $textTime .="<div data-toggle='popover' title='Detalles'  service='".$sch['id']."' time='".$dateTimeToCheck."'>";
                                    $textTime .="<div style='{$styleDIV}'><b>{$sch['startH']}:{$sch['startM']} - {$sch['endH']}:{$sch['endM']}</b></div>";
                                    $textTime .="<div style='{$styleDIV}'><b>{$serviceData->name}</b></div>";
                                    $textTime.="<div style='{$styleDIV2}'><b>{$spots}{$max_spots}</b></div>";
                                    $textTime .="</div></div>";

                                    $textTime_mobile .="<div onclick=\"{$clickTime_mobile};event.stopPropagation()\" class='eventConteiner' style='{$style}'>";
                                    $textTime_mobile .="<div style='{$styleDIV}'><b>{$sch['startH']}:{$sch['startM']} - {$sch['endH']}:{$sch['endM']} {$serviceData->name}</b></div>";
                                    $textTime_mobile .="<div style='{$styleDIV2}'><b>{$spots}{$max_spots}</b></div>";
                                    $textTime_mobile .="</div>";

                                    $textTime_mobile .="<div id=\"ajax" . $iDay . "" . $cont_servs. "\" onclick=\"{$clickTime_mobile2};event.stopPropagation()\" class='ajaxContainer eventConteiner'  >";
                                    $textTime_mobile .="</div>";
                                }

                                
                            }
                            $bgClass = "cal_reg_on";
                            //pendiente: incluir multiday services
                        } 
                        else 
                        {
                            $spotsText = "";//($showSpaces) ? "<span class='hide-me-for-nojs'><br/>" . $cur_spots . SPC_AVAIL."</span>" : "";
                            //$text .="<div class='eventConteiner' onclick=\"{$clickTime}\">{$spotsText}</div>";
                            $clickTime = '';
                            $clickTime_mobile = '';

                        }

                        $day = "<td id=\"" . $iDay . "\"";
                        $day_mobile = "<td id=\"" . $iDay . "\"  onclick=\"" . $clickTime_mobile . "\"";
                        if ($iCurrentDay != $iDay) 
                        {
                            $var = "";
                        } else {
                            $var = "_today";
                        }

                        if ($iCurrentDay != $iDay && $bgClass != "cal_reg_off") 
                        {
                            $day .= "onmouseover=\"getElementById('" . $iDay . "').className='mainmenu5';\" onmouseout=\"getElementById('" . $iDay . "').className='" . $bgClass . "';\" ";
                            $day_mobile .= "onmouseover=\"getElementById('" . $iDay . "').className='mainmenu5';\" onmouseout=\"getElementById('" . $iDay . "').className='" . $bgClass . "';\" ";
                        } 
                        else if ($iCurrentDay == $iDay && $bgClass != "cal_reg_off") 
                        {
                            $day .= "onmouseover=\"getElementById('" . $iDay . "').className='mainmenu5';\" onmouseout=\"getElementById('" . $iDay . "').className='" . $bgClass . $var . "' \"";
                            $day_mobile .= "onmouseover=\"getElementById('" . $iDay . "').className='mainmenu5';\" onmouseout=\"getElementById('" . $iDay . "').className='" . $bgClass . $var . "' \"";
                        }
                        $day .= "class=\"" . $bgClass . $var . "\">" . $iDay;
                        $day_mobile .= "class=\"cal_mobile " . $bgClass . $var . "\">" . $iDay;

                        $day .=$textTime . $text;
                        $day_mobile .=$textTime_mobile . $text;

                        $day .= "</td>";
                        $day_mobile .= "</td>";
                        //##############  END SERVICES  #######################################################
                        
                        
                    }
                    $calendar .= $day;
                    $calendar_mobile .= $day_mobile. "</tbody>";
                }
            }
            $calendar .= "</tr>";
            $calendar_screen .= $calendar;
            
        } //end foreach
        $calendar_screen .= "</tbody>";
        return array($calendar_screen, $calendar_mobile);
    }

    function setupSmallCalendar($iMonth, $iYear, $serviceID=1,$baseDir='/') {

        $thismonth = false;

        $iMonth2 = date('m', strtotime(date("Y") . "-" . $iMonth . "-01"));
        if (!$iMonth || !$iYear) {
            $iMonth = date('n');
            $iYear = date('Y');
        }

        ############################## BUILD BASE CALENDAR ################################
        $aCalendar = $this->buildCalendar($iMonth, $iYear);
        list($iPrevMonth, $iPrevYear) = $this->prevMonth($iMonth, $iYear);
        list($iNextMonth, $iNextYear) = $this->nextMonth($iMonth, $iYear);
        $iCurrentMonth = date('n');
        $iCurrentYear = date('Y');
        $iCurrentDay = '';
        if (($iMonth == $iCurrentMonth) && ($iYear == $iCurrentYear)) {
            $iCurrentDay = date('d');
            $thismonth = true;
        }
        $iNextMonth = mktime(0, 0, 0, $iNextMonth, 1, $iNextYear);
        $iPrevMonth = mktime(0, 0, 0, $iPrevMonth, 1, $iPrevYear);
        $iCurrentDay = $iCurrentDay;
        $iCurrentMonth = mktime(0, 0, 0, $iMonth, 1, $iYear);
        $calendar = "";
        ############################ PREPARE CALENDAR DATA #############################
        foreach ($aCalendar as $aWeek) {
            $calendar .= "<tr>";
            foreach ($aWeek as $iDay => $mDay) {
                if ($iDay == '') {
                    $calendar .= "<td colspan=\"" . $mDay . "\"  class=\"cal_reg_off\">&nbsp;</td>";
                } else {
                    if (strlen($iDay) == 1) {
                        $iDay = '0' . $iDay;
                    }
                    $datetocheck = $iYear . "-" . $iMonth2 . "-" . $iDay;



                    if ($datetocheck < date("Y-m-d")) {
                        $calendar .= "<td id=\"" . $iDay . "\" class='cal_reg_off past'><div class='day_number'>" . $iDay . "</div></td>";
                    } else {


                        //we need to check reserved time by admin, in case this day is booked off by him.
                        ######################### EVENT CHECKER ###################################################################################################
                        $events = $this->getEventsByDate($datetocheck,$serviceID);
                        $bgClass = "cal_reg_off";
                        $text = "";
                        $textTime = "";
                        if (count($events) > 0) {
                            //we have events for this day!


                            $bgClass = "cal_reg_on";
                            $event_num = count($events);
                            //we need to check if at least one event has spaces. if yes then { $bgClass="cal_reg_on";  } else { $bgClass="cal_reg_off"; }
                            $event_available = false;
                            $event_count = 0;

                            foreach ($events as $evt) {
                                $row = $evt['event'];
                                $spaces_left = $evt['qty'];
                                $click = $calendar_settings->use_popup ? "getLightbox2('" . $row['id'] . "'," . $serviceID . ",'".date("Y-m-d",strtotime($row['eventDate']))."');" : "location.href='{$baseDir}event-booking.php?eventID=" . urlencode($row['id']) . "&serviceID=" . $serviceID . "&date=".date("Y-m-d",strtotime($row['eventDate']))."'";
                                if ($spaces_left > 0) {
                                    $event_available = true;
                                    $event_count++;
                                }
                                $style = empty($row['color'])?"background-color:#fff;color:#666":"background-color:{$row['color']}";
                                $styleDIV = empty($row['color'])?"color:#666":"color:#eee";
                                $text.="<div onclick=\"{$click};return false;\" class='eventConteiner'  style='{$style}'>";
                                if ($this->CI->booking->getService($serviceID, 'show_event_titles')) {
                                    $text.="<div style='{$styleDIV}'>{$row['title']}</div>";
                                }else{
                                    $text.="<div style='{$styleDIV}'>".TXT_EVENT2."</div>";
                                }
                                if ($this->CI->booking->getService($serviceID, 'show_event_image') && !empty($row['path'])) {

                                    $text.="<div><img src='{$baseDir}{$row['path']}' width='40'></div>";
                                }
                                if ($this->CI->booking->getService($serviceID, 'show_available_seats')) {

                                    $text.="<div>{$spaces_left} ".SEATS_AVAIL."</div>";
                                }
                                $text.="</div>";
                            }
                        }
                        //we dont have events for this day, lets check bookings.
                         // end EVENT CHECKER.
                        ########################################################################################################################################
                        if($this->CI->booking->getService($serviceID, 'type')=='t'){
                        
                            $cur_spots = $this->checkSpotsLeft($datetocheck, $box_id, $serviceID);
                            
                            $showSpaces = $this->CI->booking->getService($serviceID, 'show_spaces_left');
                        
                       
                            if ($cur_spots > 0) {
                                $bgClass = "cal_reg_on";
                                $clickTime = $calendar_settings->use_popup ? "getLightbox('" . $datetocheck . "'," . $serviceID . ");" : "location.href='{$baseDir}booking.php?date=" . urlencode($datetocheck) . "&serviceID=" . $serviceID . "'";
                                $spotsText = ($showSpaces) ?  $cur_spots .SPC_AVAIL : BOOK_NOW;
                                $spotsText = '<div class="cal_text hide-me-for-nojs" onclick="' . $clickTime . '">' . $spotsText . "</div>";
                                $textTime .=$spotsText;
                            } else {
                                $spotsText = "";
                                $textTime .=$spotsText;
                                $clickTime = '';
                                $clickTime_mobile = '';
                            }



                            $calendar .= "<td id=\"" . $iDay . "\"";
                            if ($iCurrentDay != $iDay) {
                                $var = "";
                            } else {
                                $var = "_today";
                            }

                            if ($iCurrentDay != $iDay && $bgClass != "cal_reg_off") {
                                $calendar .= "onmouseover=\"this.className='mainmenu5';\" onmouseout=\"this.className='" . $bgClass . "';\" ";
                            } else if ($iCurrentDay == $iDay && $bgClass != "cal_reg_off") {
                                $calendar .= "onmouseover=\"this.className='mainmenu5';\" onmouseout=\"this.className='" . $bgClass . $var . "' \"";
                            }
                            $calendar .= "class=\"" . $bgClass . $var . "\"><div class='day_number'>" . $iDay;
                            if (!empty($textTime) || !empty($text)) {
                                $calendar .="<div class='showInfo'>" . $textTime . $text . "<b></b></div>";
                            }
                            //check if this day available for booking or not.
                            /* if(Empty($text)){
                              $calendar .= "<span class='hide-me-for-nojs'><br/>0 spaces available</span><noscript><br/>0 spaces available</noscript>";
                              } else {
                              $calendar .= "<div class='cal_text hide-me-for-nojs'>".$text."</div><noscript><br/><a href='event-booking-nojs.php?date=".$datetocheck."'>".$text."</a></noscript>";
                              } */
                            $calendar .= "</div></td>";
                        }else{
                            $availability = $this->checkAvailableDay($datetocheck, $serviceID);
                            if($availability['res']){
                                if ($this->checkSpotsForDay($datetocheck,$serviceID) === TRUE) {
                                    $bgClass = "cal_reg_on";
                                    //$clickTime = "getLightboxDays('" . $datetocheck . "'," . $serviceID . ");";
                                    $clickTime = $calendar_settings->use_popup ? "getLightboxDays('" . $datetocheck . "'," . $serviceID . ");" : "location.href='{$baseDir}booking-days.php?dateFrom=" . urlencode($datetocheck) . "&serviceID=" . $serviceID . "'";
                                    $spotsText = '<div class="cal_text hide-me-for-nojs"  onclick="' . $clickTime . '">' . DAY_AVAIL ."</div>";
                                    $textTime .=$spotsText;
                                } else {
                                    $spotsText = "<span class='hide-me-for-nojs'><br/>"  . DAY_BOOKED."</span>" ;
                                    $textTime .=$spotsText;
                                    $clickTime = '';
                                    $clickTime_mobile = '';
                                }
                            }
                            $calendar .= "<td id=\"" . $iDay . "\"";
                            if ($iCurrentDay != $iDay) {
                                $var = "";
                            } else {
                                $var = "_today";
                            }
                            
                            if ($iCurrentDay != $iDay && $bgClass != "cal_reg_off") {
                                $calendar .= "onmouseover=\"this.className='mainmenu5';\" onmouseout=\"this.className='" . $bgClass . "';\" ";
                            } else if ($iCurrentDay == $iDay && $bgClass != "cal_reg_off") {
                                $calendar .= "onmouseover=\"this.className='mainmenu5';\" onmouseout=\"this.className='" . $bgClass . $var . "' \"";
                            }
                            $calendar .= "class=\"" . $bgClass . $var . "\"><div class='day_number'>" . $iDay;
                            if (!empty($textTime) || !empty($text)) {
                                $calendar .="<div class='showInfo'>" . $textTime . $text . "<b></b></div>";
                            }
                            //check if this day available for booking or not.

                            $calendar .= "</td>";
                        }
                    } //end if iDay
                }
            }
            $calendar .= "</tr>";
        } //end foreach 
        ############################## END PREPARE CALENDAR DATA ################################

        return $calendar;
    }

    function buildCalendar($iMonth, $iYear) 
    {
        $myFirstDay = $this->CI->booking->getSettingItem('calendar', 'start_day');

        $iFirstDayTimeStamp = mktime(0, 0, 0, $iMonth, 1, $iYear);
        $iFirstDayNum = date('w', $iFirstDayTimeStamp);
        $iFirstDayNum++;
        $iDayCount = date('t', $iFirstDayTimeStamp);
        $aCalendar = array();
        $empty_squares = false;

        if($iMonth > 1) 
            $lastDay_previous_month = cal_days_in_month(CAL_GREGORIAN, $iMonth-1, $iYear);
        else 
            $lastDay_previous_month = cal_days_in_month(CAL_GREGORIAN, '12', $iYear-1);

        if ($myFirstDay == "0") {
            //SUNDAY
            if ($iFirstDayNum > 1) {
                if($empty_squares) 
                    $aCalendar[1][''] = $iFirstDayNum - 1; // how many empty squares before actual day 1.
                else
                {
                    for ($k = $iFirstDayNum - 1; $k == 0; $k--) { 
                        $aCalendar[1][$lastDay_previous_month - $k] = $lastDay_previous_month - $k;
                    }
                }

            }
            $i = 1;
            $j = 1;

            while ($j <= $iDayCount) {
                $aCalendar[$i][$j] = $j;
                if (floor(($j + $iFirstDayNum - 1) / 7) >= $i) {
                    $i++;
                }
                $j++;
            }
            if ((isset($aCalendar[$i])) && ($iM = count($aCalendar[$i])) < 7) {
                if($empty_squares) 
                    $aCalendar[$i][''] = 7 - $iM;
                else
                {
                    for ($k=0; $k < 7 - $iM; $k++) { 
                        $aCalendar[$i][$k+1] = $k+1;
                    }
                }
            }
        } else if ($myFirstDay == "1") {
            //MONDAY
            $iFirstDayNum--;
            if ($iFirstDayNum > 1 && $iFirstDayNum < 6) {
                //echo "off1";
                $tmp = 1;

                if($empty_squares) 
                    $aCalendar[1][''] = $iFirstDayNum - $tmp; // how many empty squares before actual day 1.
                else
                {   
                    for ($k = $iFirstDayNum - $tmp; $k > 0; $k--) { 
                        $aCalendar[1][$lastDay_previous_month - $k + 1] = $lastDay_previous_month - $k + 1;
                    }
                }

                $i = 1;
                $j = 1;

                while ($j <= $iDayCount) {
                    $aCalendar[$i][$j] = $j;
                    if (floor(($j + $iFirstDayNum - $tmp) / 7) >= $i) {
                        $i++;
                    }
                    $j++;
                }
                if ((isset($aCalendar[$i])) && ($iM = count($aCalendar[$i])) < 7) {
                    if($empty_squares) 
                        $aCalendar[$i][''] = 7 - $iM; //last row - how many empty squares.
                    else
                    {
                       for ($k=0; $k < 7 - $iM; $k++) { 
                            $aCalendar[$i][$k+1] = $k+1;
                        } 
                    }
                    
                }
            } else if ($iFirstDayNum == 0) {

                //echo "off2";
                $tmp = 1;
                if($empty_squares) {
                    $aCalendar[1][''] = 6;
                }
                else
                {
                    for ($k = 6; $k > 0; $k--) { 
                        $aCalendar[1][$lastDay_previous_month - $k + 1] = $lastDay_previous_month - $k + 1;
                    }
                }

                $i = 1;
                $j = 1;

                while ($j <= $iDayCount) {
                    $aCalendar[$i][$j] = $j;
                    if (floor(($j + $iFirstDayNum + 6) / 7) >= $i) {
                        $i++;
                    }
                    $j++;
                }
                if ((isset($aCalendar[$i])) && ($iM = count($aCalendar[$i])) < 7) {
                    if($empty_squares)
                        $aCalendar[$i][''] = 7 - $iM; //last row - how many empty squares.
                    else
                    {
                       for ($k=0; $k < 7 - $iM; $k++) { 
                            $aCalendar[$i][$k+1] = $k+1;
                        } 
                    }  
                }
            } else if ($iFirstDayNum == 6) {

                //echo "off2";
                $tmp = 1;
                if($empty_squares) 
                    $aCalendar[1][''] = 5;
                else
                {
                    for ($k = $iFirstDayNum - $tmp; $k > 0; $k--) { 
                        $aCalendar[1][$lastDay_previous_month - $k + 1] = $lastDay_previous_month - $k + 1;
                    }
                }

                $i = 1;
                $j = 1;

                while ($j <= $iDayCount) {
                    $aCalendar[$i][$j] = $j;
                    if (floor(($j + $iFirstDayNum - 1) / 7) >= $i) {
                        $i++;
                    }
                    $j++;
                }
                if ((isset($aCalendar[$i])) && ($iM = count($aCalendar[$i])) < 7) {
                    if($empty_squares) 
                        $aCalendar[$i][''] = 7 - $iM; //last row - how many empty squares.
                    else
                    {
                        for ($k=0; $k < 7 - $iM; $k++) { 
                            $aCalendar[$i][$k+1] = $k+1;
                        }  
                    }    
                }
            } else {
                //echo "off3";
                $tmp = 1;
                $i = 1;
                $j = 1;


                while ($j <= $iDayCount) {
                    $aCalendar[$i][$j] = $j;
                    if (floor(($j + $iFirstDayNum - $tmp) / 7) >= $i) {
                        $i++;
                    }
                    $j++;
                }
                if ((isset($aCalendar[$i])) && ($iM = count($aCalendar[$i])) < 7) {
                    if($empty_squares)
                        $aCalendar[$i][''] = 7 - $iM; //last row - how many empty squares.
                    else
                    {
                       for ($k=0; $k < 7 - $iM; $k++) { 
                            $aCalendar[$i][$k+1] = $k+1;
                        } 
                    }
                    
                }
            }
        }
        //print("<pre>".print_r($aCalendar,true)."</pre>");
        return $aCalendar;
    }

    function nextMonth($iMonth, $iYear) {
        if ($iMonth == 12) {
            $iMonth = 1;
            $iYear++;
        } else {
            $iMonth++;
        }
        return array($iMonth, $iYear);
    }

    function nextDay($iDay, $iMonth, $iYear) {
        $iDayTimestamp = mktime(0, 0, 0, $iMonth, $iDay, $iYear);
        $iNextDayTimestamp = strtotime('+1 day', $iDayTimestamp);
        return $iNextDayTimestamp;
    }

    function prevDay($iDay, $iMonth, $iYear) {
        $iDayTimestamp = mktime(0, 0, 0, $iMonth, $iDay, $iYear);
        $iPrevDayTimestamp = strtotime('-1 day', $iDayTimestamp);
        return $iPrevDayTimestamp;
    }

    function prevMonth($iMonth, $iYear) {
        if ($iMonth == 1) {
            $iMonth = 12;
            $iYear--;
        } else {
            $iMonth--;
        }
        return array($iMonth, $iYear);
    }

    function getMaxSecondsForThisDay($day) {
        $tt = 0;
        $q = "SELECT * FROM bs_settings WHERE id='1'";
        $res = mysqli_query($this->link,$q);
        $rr = mysqli_fetch_assoc($res);
        /* if(!empty($rr[$day."_from"]) && $rr[$day."_from"]!="N/A"){ $from = explode(":",$rr[$day."_from"]); } else { $from[0]=0; }
          if(!empty($rr[$day."_to"]) && $rr[$day."_to"]!="N/A"){ $to = explode(":",$rr[$day."_to"]);} else { $to[0]=0; } */ //LEFTOVERS FROM V2
        if (!empty($rr[$day . "_from"]) && $rr[$day . "_from"] != "N/A" && $rr[$day . "_from"] != "0") {
            $from = $rr[$day . "_from"] / 60;
        } else {
            $from = 0;
        }
        if (!empty($rr[$day . "_to"]) && $rr[$day . "_to"] != "N/A" && $rr[$day . "_to"] != "0") {
            $to = $rr[$day . "_to"] / 60;
        } else {
            $to = 0;
        }
        $tt = (($to - $from) * 60) * 60;
        return $tt;
    }

    function getStartEndTime($day, $serviceID=1) {
        $tt = array();
        $tt[0] = 0;
        $tt[1] = 0;
        $q = "SELECT * FROM bs_services WHERE id='{$serviceID}'";
        $res = mysqli_query($this->link,$q);
        $rr = mysqli_fetch_assoc($res);

        if (!empty($rr[$day . "_from"]) && $rr[$day . "_from"] != "N/A" && $rr[$day . "_from"] != "0") {
            $from = $rr[$day . "_from"];
        } else {
            $from = 0;
        }
        if (!empty($rr[$day . "_to"]) && $rr[$day . "_to"] != "N/A" && $rr[$day . "_to"] != "0") {
            $to = $rr[$day . "_to"];
        } else {
            $to = 0;
        }

        $tt[0] = ($from - ($from % 60)) / 60;
        $tt[1] = ($to - ($to % 60)) / 60;
        $tt[2] = $from;
        $tt[3] = $to;
        //print var_dump($tt);
        return $tt;
    } 
}

?>
