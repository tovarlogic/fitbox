<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Cron_model extends CI_Model
{
    //DATABASE
    private $bTable = 'boxes';
    private $sTable = 'settings';
    private $uTable = 'auth_users';
    private $ugTable = 'auth_users_groups';
    private $mTable = 'ms_memberships';
    private $muTable = 'ms_memberships_users';
    private $msTable = 'ms_memberships_services';
    private $mssTable = 'ms_settings';
    private $pTable = 'ms_payments';
    private $pdTable = 'ms_payments_deleted';
    private $gTable = 'ms_gateways';
    private $tTable = 'ms_transactions';
    private $iuTable = 'ms_iban_users';
    private $cTable = 'bs_coupons';

    //VAR
    public $box_id = null;

    private $user_groups = array( 
        "sudo"=>1, 
        "sadmin"=>2, 
        "admin"=>3, 
        "rrhh"=>4, 
        "finance"=>5, 
        "fcoach"=>6, 
        "coach"=>7, 
        "comercial"=>8, 
        "marketing"=>9, 
        "board"=>10, 
        "athlete"=>11, 
        "guest"=>12
    ); //pendiente crear funcion en auth "getGroups"

    public $ath_status = array(
        "active"=>"y", // pagado y valido
        "inactive"=>"n", // caducado
        "pending"=>"p", // pendiente primer pago
        "grace"=>"g", // en periodo de gracia (pendiente de renovación)
        "banned"=>"b", // non grato (baneado)
        "cancel"=>"c", // de baja o cancelado
        "ended"=>"e" // terminado por consumición
    );

    public $compatibility = array(
        "unico"=>"u", // incompatible con el resto
        "primario"=>"p", // incompatible con otros primarios
        "complementario"=>"c" // compatible con primarios y otros complementarios
    );
    public $athletes = array( 
        "pending"=>0, 
        "banned"=>0, 
        "active"=>0, 
        "member"=>0, 
        "cancel"=>0, 
        "inactive"=>0);
    
    public $memberships = null;

    function __construct()
    {
        parent::__construct();
        $this->load->model('booking_model', 'booking');
        $this->load->model('settings_model', 'booking');
    }

    function renewMembership($mem, $to)
    {
        $result = $this->db->set('status', 'y', TRUE)->set('mem_expire', $to, TRUE)->where('id', $mem->id)->update($this->muTable);
        $this->logs->set_members_log($mem->user_id, $mem->box_id, $mem->id, 'renew');

        return $result;
    }

    function updateMembership($mem, $status)
    {
        if($status == 'c')
          $log = 'canceled';
        else if($status == 'n')
          $log = 'expired';
        else if($status == 'g')
          $log = 'grace';

        $this->db->set('status', $status, TRUE)->where('id', $mem->id)->update($this->muTable);

        $this->logs->set_members_log($mem->user_id, $mem->box_id, $mem->id, $log);
    }

    /**
     * Function: getAllExpiringMemberships
     * returns every membership (apart from bonuses) that expire today or before and which status is not 'cancelled' or 'expired'
     * @return [type] [description]
     */
    function getAllExpiringMemberships()
    {
        $condition = 'mem_expire <=';
        $exp_date = date("Y-m-d", strtotime("-1 day"));

        $result = $this->db->select('mu.id, mu.user_id, mu.box_id, mu.membership_id, mu.payment_method, mu.status, mu.mem_expire, ms.title, ms.price, ms.days, ms.period, ms.recurring, auth_users.email, auth_users.username, auth_users.first_name, boxes.name as box_name')
                            ->from('ms_memberships_users mu')
                            ->join('ms_memberships ms', 'ms.id = mu.membership_id')
                            ->join('auth_users', 'auth_users.id = mu.user_id')
                            ->join('boxes', 'boxes.id = mu.box_id')
                            ->where($condition, $exp_date)
                            ->where('ms.period !=', 'D')
                            ->where("(mu.status != 'c' AND mu.status != 'e')")
                            ->get();

         return ($result !== FALSE && $result->num_rows() > 0)? $result->result() : FALSE;
    }  

    function autoCancelExpriredMemberships($days = null)
    {      
      $result = $this->db->select('ms_memberships_users.id, ms_memberships_users.user_id, ms_memberships_users.box_id, ms_memberships_users.membership_id, ms_memberships_users.mem_expire, ms_memberships.title, ms_memberships.recurring, auth_users.email, auth_users.username, auth_users.first_name, boxes.name as box_name')
                            ->from($this->muTable)
                            ->join('ms_memberships', 'ms_memberships.id = ms_memberships_users.membership_id')
                            ->join('auth_users', 'auth_users.id = ms_memberships_users.user_id')
                            ->join('boxes', 'boxes.id = ms_memberships_users.box_id')
                            ->where('ms_memberships_users.status', 'n')
                            ->get()
                            ->result(); 

        $today = date("Y-m-d");
        foreach ($result as $membership ) 
        {
            if ($days == null)
                $days = $this->booking->getSettingItem('membership', 'cancel_period', $membership->box_id);

            
            $cancel_date = date("Y-m-d", strtotime($today." +".$days." day"));
            //set the memberships and notificate
            if($membership->mem_expire <= $cancel_date)
            {

                $this->db->set('status', 'c', TRUE);
                $this->db->where('id', $membership->id);
                $this->db->update($this->muTable);

                $this->logs->set_members_log($membership->user_id, $membership->box_id, $membership->id, 'cancel');
              

              //Send notification e-mail

                $this->email->set_newline("\r\n");
                $this->email->to($membership->email);
                $this->email->from("info@fitbox.es", "FitBox");
                $this->email->bcc("kinsay@gmail.com");

                if ($membership->first_name != null) 
                {
                    $user = $membership->first_name;
                }
                else
                {
                    $user = ($membership->username != null) ? $membership->username : $membership->email;
                }

                $data = array(
                            'box_name' => $membership->box_name,
                            'title' => $membership->title,
                            'user' => $user,
                            'cancel_period' => $days
                    );
                    
                    $subject = "Recordatorio ".$membership->title." en ".$membership->box_name." ha caducado";
                    $message = $this->load->view('/emails/cron/membership_expired2.tpl.php', $data, TRUE);
            }
        }
    }

}

?>
