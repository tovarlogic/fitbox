<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payment_model extends MY_Model
{
    //DATABASE TABLES
    private $bTable = 'boxes';
    private $sTable = 'settings';

    private $uTable = 'auth_users';
    private $ugTable = 'auth_users_groups';

    private $cTable = 'bs_coupons';

    private $mTable = 'ms_memberships';
    private $muTable = 'ms_memberships_users';
    private $msTable = 'ms_memberships_services';

    private $mssTable = 'ms_settings';
    private $tTable = 'ms_transactions';
    private $gTable = 'ms_gateways';
    private $geTable = 'ms_gateways_events';
    private $gcTable = 'ms_gateways_customers';
    private $gmTable = 'ms_gateways_mandates';
    private $gsTable = 'ms_gateways_subscriptions';
    private $gpTable = 'ms_gateways_payments';
    private $gpoTable = 'ms_gateways_payouts';
    private $grTable = 'ms_gateways_refunds';
    private $gbaTable = 'ms_gateways_bank_accounts';
    private $goTable = 'ms_gateways_oauth';
    private $gmcTable = 'ms_gateways_match_conflict';
    private $iuTable = 'ms_iban_users';

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
        "cancel"=>"c", // cancelado/de baja
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

    public $box_id = null;

    function __construct()
    {
        parent::__construct();

        //$this->config->load('stripe');
    }

    /**
     * Function: set_box
     */
    function set_box($box_id = null) //pendiente: comprobar que box existe
    {
        $result = false;

        if($box_id !== null)//inicialización manual EJ: Calendario
        {
            $this->box_id = $box_id;
            $result = true;
        }
        else
        {
          if( $result = $this->db->select('box_id')->get_where($this->ugTable, array('user_id' => $this->session->userdata('user_id')))->row() ) //pendiente: este metodo no permite que un usuarioo sté en más de un box.
          {
            $this->session->set_userdata("box_id", $result->box_id);
            $this->box_id = $result->box_id;
          }
        }

        return ($result) ? $result : false;
    }  


////////////////////////////////////
//// Section:  TRANSACTIONS/PAYMENTS
////////////////////////////////////

    /**
     * Function: getPaymentMethod
     * 
     * Paramaters:
     * int              $id [description]
     * 
     * Returns: 
     * object           or FALSE in case of no results
     */
    function getPaymentMethod($id)
    {
        $this->db->from($this->gTable)->where('box_id =', $this->box_id)->where('id', $id);

        $result = $this->db->get()->row();
        return ($result) ? $result : false;
    }

    /**
     * Function: getPaymentMethods
     * 
     * Paramaters:
     * [type] $params [description]
     * 
     * Returns: 
     * [type] [description]
     */
    function getPaymentMethods($params = null)
    {
        $result = $this->getRows($this->gTable, 'id, name, default', $params);

        $list = array();
        foreach ($result as $res) {

          $list[$res->id] = array('name' => $this->lang->line($res->name.'_gateway_name'), 'default' => $res->default);
        }

        return ($result) ? $list : false;
    }

    /**
     * Function: changePaymentMethod
     * 
     * Paramaters:
     * [type]              $id             [description]
     * [type]              $payment_method [description]
     * 
     * Returns: 
     * [type]              [description]
     */
    function changePaymentMethod($id = null, $payment_method = null)
    {
        if($this->db->from($this->gTable)->where('box_id =', $this->box_id)->where('id', $payment_method))
        {
            if($this->db->where('id', $id)->where('box_id', $this->box_id)->update($this->muTable, array('payment_method' => $payment_method)))
            {
              return true;
            }
        }
        
        return false;
    }

    /**
     * Function: deletePayment
     *
     * @param  [type] $id [description]
     *
     * @return [type] [description]
     */
    function deletePayment($id)
    {
        $params = array('status' => 'deleted');
        if($this->updateTransaction($id, $params))
        {
            return true;
        }
        return false;
    }

    /**
     * Function: updateTransaction
     *
     * @param  [type] $id [description]
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function updateTransaction($id, $params)
    {
        if($this->db->where('id', $id)->where('box_id', $this->box_id)->update($this->pTable, $params))
        {
            return true;
        }
        return false;
    }

    /**
     * Function: registerTransaction
     *
     * @param  [type] $transaction_data [description]
     *
     * @return [type] [description]
     */
    function registerTransaction($transaction_data)
    {
        if($this->insert($this->tTable, $transaction_data))
            return true;

        return false;
    }
    /**
     * Function: registerPayment
     *
     * @param  [type] $payment_data [description]
     * @param  [type] $transaction_data [description]
     *
     * @return [type] [description]
     */
    function registerPayment($payment_data, $transaction_data)
    {   
        $this->db->trans_start();
        if($pay_id = $this->insert($this->gpTable, $payment_data))
        {
            $transaction_data['payment_id'] = $pay_id;
            if($this->insert($this->tTable, $transaction_data))
            {
                if($transaction_data['coupon_id'] != 0)
                {
                    //pendiente revisar funcionamiento
                    //$this->booking->registerCouponUse($transaction_data['coupon_id'], $payment_data['mu_id']);
                }
            }
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return FALSE;
        }
        else
        {
            return true;
        }
    }

    function registerRefund()
    {
        //pendiente
    }

    /**
     * Function: getTransactionByTXN_ID
     *
     * @param  [type] $id [description]
     *
     * @return [type] [description]
     */
    function getTransactionByTXN_ID($id)
    {
        $this->db->from($this->gpTable)->where('txn_id', $id);

        $result = $this->db->get()->row();
        return ($result) ? $result : false;
    }

    /**
     * Function: getTransaction
     *
     * @param  [type] $id [description]
     *
     * @return [type] [description]
     */
    function getTransaction($id)
    {
        $this->db->from($this->pTable)->where('box_id =', $this->box_id)->where('id', $id);

        $result = $this->db->get()->row();
        return ($result) ? $result : false;
    }

    /**
     * Function: getTransactionsByParams
     *
     * @param  [type] $params [description]
     * @param  [type] $order_by [description]
     *
     * @return [type] [description]
     */
    function getTransactionsByParams($params, $order_by = null)
    {
        if($order_by == null) $order_by = array('date', 'DESC');

        $joins = array($this->gpTable => array( 'ref_table' => $this->tTable, 
                                                'ref_field' => 'id', 
                                                'new_field' => 'payment_id',
                                                'join_type' => 'inner'));

        return $this->getRows($this->tTable, '*', $params, $order_by, null, null, $joins);
    }
    
    /**
     * Function: getUserPayments
     * returns all apyments of the requested user
     *
     * Parameters:
     * $user int
     * $demo - bool
     * $status - accepts either a single one (string) or many (array)
     *
     * @return [type] [description]
     */
    function getUserPayments($user, $demo = null, $status = null, $box = null)
    {
        $this->db->select('ms_transactions.*,  ms_gateways_payments.*,
                            u1.first_name AS user_name, u1.last_name AS user_last_name, 
                            u2.first_name AS staff_name, u2.last_name AS staff_last_name, ms_gateways.name AS pp, ms_memberships_users.id, ms_memberships.title AS membership, 
                            boxes.name AS box_name')
                ->from($this->tTable)
                ->join($this->gpTable, 'ms_gateways_payments.id = ms_transactions.payment_id')
                ->join('auth_users as u1', 'u1.id = ms_gateways_payments.user_id') 
                ->join('auth_users as u2', 'u2.id = ms_gateways_payments.staff_id') 
                ->join($this->gTable, 'ms_gateways.id = ms_gateways_payments.gateway') 
                ->join($this->muTable, 'ms_memberships_users.id = ms_gateways_payments.mu_id')
                ->join($this->mTable, 'ms_memberships.id = ms_memberships_users.membership_id')
                ->join($this->bTable, 'boxes.id = ms_gateways_payments.box_id')
                ->where('ms_gateways_payments.user_id =', $user);

        //by default only shows real payments
        if($demo != 'all')
        {
           if($demo == 'demo')
                $this->db->where('ms_gateways_payments.demo =', 1);
            else
                $this->db->where('ms_gateways_payments.demo =', 0);
        }
        

        //by default shows all payments regardless of status
        if(!is_null($status))
        {
            if(is_string($status))
                $this->db->where('ms_gateways_payments.status =', $status);
            else if(is_array($status))
            {
                $this->db->group_start();
                foreach ($status as $key => $value) {
                    $this->db->or_where('ms_gateways_payments.status =', $value);
                }
                $this->db->group_end();
            }
        }

        if(!is_null($box))
            $this->db->where('ms_gateways_payments.box_id =', $box);

        $this->db->order_by("ms_gateways_payments.updated_on", "DESC"); 

        $result = $this->db->get()->result();

        return ($result) ? $result : false;
    }
    /**
     * Function: listUserTransactions
     *
     * @param  [type] $user [description]
     * @param  [type] $status [description]
     *
     * @return [type] [description]
     */
    function listUserTransactions($user, $status = null)
    {
       $this->db->select(' ms_gateways_payments.*, ms_transactions.id as trans_id, ms_transactions.box_id, ms_transactions.user_id, ms_transactions.mu_id, ms_transactions.from_membership_id, ms_transactions.to_membership_id, ms_transactions.staff_id, ms_transactions.from, ms_transactions.to, ms_transactions.type, ms_transactions.notes, ms_transactions.rate_amount, ms_transactions.tax, ms_transactions.discount, ms_transactions.total, ms_transactions.currency, ms_transactions.pp, ms_transactions.date, ms_transactions.status, ms_transactions.email_not_received_sent, u1.first_name AS user_name, u1.last_name AS user_last_name, u2.first_name AS staff_name, u2.last_name AS staff_last_name, ms_gateways.name AS pp, ms_memberships_users.id, ms_memberships.title AS membership, bs_coupons.title, boxes.name AS box_name')
                ->from($this->tTable)
                ->join($this->gpTable, 'ms_gateways_payments.id = ms_transactions.payment_id')
                ->join('auth_users as u1', 'u1.id = ms_gateways_payments.user_id') 
                ->join('auth_users as u2', 'u2.id = ms_gateways_payments.staff_id') 
                ->join($this->gTable, 'ms_gateways.id = ms_gateways_payments.gateway') 
                ->join($this->muTable, 'ms_memberships_users.id = ms_gateways_payments.mu_id')
                ->join($this->mTable, 'ms_memberships.id = ms_transactions.to_membership_id')
                ->join($this->cTable, 'bs_coupons.id = ms_transactions.coupon_id')
                ->join($this->bTable, 'boxes.id = ms_gateways_payments.box_id')
                ->where('ms_gateways_payments.box_id =', $this->box_id)
                ->where('ms_gateways_payments.user_id =', $user);
        
        if($status == TRUE)
        {
            $this->db->group_start()
                ->or_where('ms_gateways_payments.status =', 'canceled')
                ->or_where('ms_gateways_payments.status =', 'succeeded')
                ->or_where('ms_gateways_payments.status =', 'confirmed')
                ->or_where('ms_gateways_payments.status =', 'pending')
                ->or_where('ms_gateways_payments.status =', 'refunded')
                ->or_where('ms_gateways_payments.status =', 'captured')
                ->group_end();
        }

                
        $this->db->order_by("ms_gateways_payments.updated_on", "DESC"); 

        $result = $this->db->get()->result();

        return ($result) ? $result : false;
    }
    
    /**
     * Function: getTransactions
     *
     * @param  [type] $year [description]
     * @param  [type] $month [description]
     * @param  [type] $user [description]
     * @param  [type] $box [description]
     *
     * @return [type] [description]
     */
    function getTransactions($year = null, $month = null, $user = null, $box = null)
    {
        $this->db->select('ms_transactions.*, ms_gateways_payments.*, u1.first_name AS user_name, u1.last_name AS user_last_name, u2.first_name AS staff_name, u2.last_name AS staff_last_name, ms_gateways.name AS gateway_name, ms_memberships_users.id, ms_memberships.title AS membership, bs_coupons.title, boxes.name AS box_name')
                ->from($this->tTable)
                ->join($this->gpTable, 'ms_gateways_payments.id = ms_transactions.payment_id') 
                ->join('auth_users as u1', 'u1.id = ms_gateways_payments.user_id') 
                ->join('auth_users as u2', 'u2.id = ms_gateways_payments.staff_id') 
                ->join($this->gTable, 'ms_gateways.id = ms_gateways_payments.gateway') 
                ->join($this->muTable, 'ms_memberships_users.id = ms_gateways_payments.mu_id')
                ->join($this->mTable, 'ms_memberships.id = ms_transactions.to_membership_id')
                ->join($this->cTable, 'bs_coupons.id = ms_transactions.coupon_id', 'left')
                ->join($this->bTable, 'boxes.id = ms_gateways_payments.box_id')
                ->where('ms_gateways_payments.status !=', 'deleted')
                ->where('ms_gateways_payments.status !=', 'canceled')
                ->order_by("ms_gateways_payments.updated_on", "DESC");

        if($user == "") $user = null;
        else if($user != null && $user != 'all') $this->db->where('ms_gateways_payments.user_id =', $user);

        if($year == null) $year = $year2 = date("Y"); 
        else if($year == 'all') { $year = '2018'; $year2 = date("Y"); }
        else if($year != null && $year != 'all') { $year2 = $year; }

        if($month == null)  $month = $month2 = date("m");  
        else if($month == 'all') {  $month = '01'; $month2 = '12'; } 
        else if($month != null && $month != 'all') { $month2 = $month; } 

        if($box == null) $this->db->where('ms_gateways_payments.box_id =', $this->box_id);
        else if($box != 'all') $this->db->where('ms_gateways_payments.box_id =', $box);

        // $last_day = DateTime::createFromFormat('Y-m-d', $year."-".$month."-01");
        // $last_day->modify( 'last day of this month');     

        $this->db->where('ms_gateways_payments.created_on >=', $year.'-'.$month.'-01 00:00:00');
        $this->db->where('ms_gateways_payments.created_on <=', $year2.'-'.$month2.'-31 23:59:59');

        $result = $this->db->get()->result();

        return ($result) ? $result : false;
    }

    /**
     * Function: UUID
     *
     */
    function UUID()
    {
        return $this->db->select('UUID() as uuid;')->get()->row()->uuid;
    }

///////////////////////////////////////////
/// Section: GATEWAYS
///   ////////////////////////////////////
  
    /**
     * Function: getGateways
     *
     * @param  [type] $params [description]
     * @param  [type] $order_by [description]
     *
     * @return [type] [description]
     */
    public function getGateways($params = null, $order_by = null)
    { 
        if($order_by == null)  $order_by = array('name','ASC');
        
        return $this->getRows($this->gTable, '*', $params, $order_by);
    } 

    /**
     * Function: getGateway
     *
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    public function getGateway($params)
    { 
        return $this->getRow($this->gTable, '*', $params);
    }

    /**
     * Function: getGatewaySettings
     *
     * @param  [type] $name [description]
     *
     * @return [type] [description]
     */
    function getGatewaySettings($name, $oauth = FALSE)
    {
        $settings = array();
        
        $result = $this->getGateway( array('name' => $name, 'box_id' => $this->box_id) );
        if($result !== FALSE)
        {
            $settings['box_id'] = $this->box_id;
            $settings['demo'] = $result->demo;
            $settings['active'] = $result->active;
            $settings['pp'] = $result->id;
            $settings['plan'] = $result->plan;
            $settings['currency'] = $result->currency;
            $settings['webhook_secret'] = ($result->demo == 1)? $result->demo_webhook_secret : $result->webhook_secret;

            if($oauth === TRUE)
            {
                $params = array('gateway' => $settings['pp'], 'box_id' => $this->box_id, 'demo' => $settings['demo']);

                $oauth = $this->getOauthOrg( $params );
                if($oauth !== FALSE)
                {
                    $settings['private_key'] = $oauth->access_token;
                    $settings['organisation_id'] = $oauth->organisation_id; 
                }
                else
                {
                    //needed when creating a new oauth connection
                    if($result->demo == 1)
                    {
                        $settings['public_key'] = $result->demo_public_key;
                        $settings['private_key'] = $result->demo_private_key;
                    }
                    else
                    {
                        $settings['public_key'] = $result->public_key;
                        $settings['private_key'] = $result->private_key;
                    } 
                }
            }
            else
            {
                if($result->demo == 1)
                {
                    $settings['public_key'] = $result->demo_public_key;
                    $settings['private_key'] = $result->demo_private_key;
                }
                else
                {
                    $settings['public_key'] = $result->public_key;
                    $settings['private_key'] = $result->private_key;
                }
            }

            return $settings;
        }

        return false;
    }

    /**
     * Function: getGatewayEvent
     *
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function getGatewayEvent($params)
    {
        return $this->getRow($this->geTable, '*', $params, array('id', 'DESC'));
    }

    /**
     * Function: registerGatewaysEvent
     *
     * @param  [type] $id [description]
     * @param  [type] $type [description]
     * @param  [type] $demo [description]
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function registerGatewaysEvent($id, $type, $demo, $params)
    {
        $dataDB = array(  
                'demo' => $demo,
                'type' => $type,
                'txn_id' => $id,
                'status' => $params['status']                            
        ); 

        $transaction = null;
        if($type != 'redirect_flow')
            $transaction = $this->getGatewayTransactions($type, array('txn_id' => $id), TRUE);

        $dataDB['box_id'] = (is_null($transaction))? $params['box_id']: $transaction->box_id;
        $dataDB['user_id'] = (is_null($transaction))? $params['user_id']: $transaction->user_id;

        if($type == 'redirect_flow' OR $type == 'subscriptions')
            $dataDB['mu_id'] = (!empty($params['mu_id']))? $params['mu_id'] : $transaction->mu_id;
        
        $dataDB['gateway'] = (!empty($params['gateway']))? $params['gateway'] : $transaction->gateway;
        $dataDB['action'] =  (!empty($params['action']))? $params['action'] : $transaction->status;
        $dataDB['cause'] =  (!empty($params['cause']))? $params['cause'] : $transaction->status;

        $this->registerGatewaysEvent2($dataDB);
    }

    function registerGatewaysEvent2($dataDB)
    {
        $this->db->insert($this->geTable, $dataDB);

        $error = $this->db->error();
        if (empty($error['code'])) 
            return true;
        
        return false;
    }

    /**
     * Function: updateGatewayEvent
     *
     * @param  [type] $id [description]
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function updateGatewayEvent($id, $params)
    {
        $this->db->where('txn_id', $id)->where('box_id', $this->box_id)->update($this->geTable, $params);
        
        $error = $this->db->error();
        if (empty($error['code'])) 
            return true;
        
        return false;
    }

    /**
     * Function: _getGatewaysDBTable
     *
     * @param  [type] $type [description]
     *
     * @return [type] [description]
     */
    private function _getGatewaysDBTable($type)
    {
        $db_table = null;
        switch ($type) 
        {
            case 'mandates':
                $db_table = $this->gmTable;
                break;
            
            case 'subscriptions':
                $db_table = $this->gsTable;
                break;

            case 'customers':
                $db_table = $this->gcTable;
                break;

            case 'bank_accounts':
                $db_table = $this->gbaTable;
                break;

            case 'oauth':
                $db_table = $this->goTable;
                break;

            case 'payments':
                $db_table = $this->gpTable;
                break;

            case 'refunds':
                $db_table = $this->grTable;
                break;
        }

        return $db_table;
    }

    /**
     * Function: getGatewayTransactions
     *
     * @param  [type] $type [description]
     * @param  [type] $params [description]
     * @param  [type] $row [description]
     * @param  [type] $order_by [description]
     *
     * @return [type] [description]
     */
    function getGatewayTransactions($type, $params, $row = null, $order_by = null)
    {
        $db_table = $this->_getGatewaysDBTable($type);
        if($order_by == null)  $order_by = array('created_on','ASC');

        if($row == TRUE)
            return $this->getRow($db_table, '*', $params);
        else
            return $this->getRows($db_table, '*', $params, $order_by);
    }

    /**
     * Function: addGatewayTransaction
     *
     * @param  [type] $type [description]
     * @param  [type] $params [description]
     */
    function addGatewayTransaction($type, $params)
    {
        $db_table = $this->_getGatewaysDBTable($type);
        $result = $this->insert($db_table, $params);
        
        return $result;
    }

    /**
     * Function: updateGatewayTransaction
     *
     * Parameters:
     * $id [description]
     * $type string
     * $demo int - 1 or 0
     * $params array 
     *
     * Returns:
     * bool
     */
    function updateGatewayTransaction($id, $type, $params)
    {
        $db_table = $this->_getGatewaysDBTable($type);
        $result = $this->update(array('txn_id' => $id), $db_table, $params);
        
        return $result;
    }

    /**
     * Function: cancelMandate
     *
     * @param  [type] $id [description]
     * @param  [type] $demo [description]
     *
     * @return [type] [description]
     */
    function cancelMandate($id, $demo)
    {
        $subscriptions = $this->getGatewayTransactions('subscriptions', array('mandate_id' => $id, 'demo' => $demo));
        if($subscritions != FALSE)
        {
            foreach ($subscriptions as $sub) 
            {
                if($this->cancelSubscription($sub->txn_id, $demo) === false)
                {
                    //register error
                    $dataDB = array(
                                'demo' => $demo,
                                'type' => 'subscriptions',
                                'txn_id' => $sub->txn_id,
                                'action' => 'cancelled', 
                                'cause' => 'mandate_cancelled', 
                                'status' => 'error'
                                );

                    $this->registerGatewaysEvent2($dataDB);

                }
            }
        }

        return $this->updateGatewayTransaction($id, 'mandates', $demo, array('status' => 'cancelled'));
    }

    /**
     * Function: cancelSubscription
     *
     * @param  [type] $id [description]
     * @param  [type] $demo [description]
     *
     * @return [type] [description]
     */
    function cancelSubscription($id, $demo = null)
    {
        return $this->updateGatewayTransaction($id, 'subscriptions', array('status' => 'cancelled')); 
    }
    
    ///////////////////////////////////////////
    ///  Section: OAUTH
    ///  /////////////////////////////////////
    ///  
    /**
     * Function: addOauth
     *
     * @param  [type] $params [description]
     */
    function addOauth($params)
    {
        $this->insert($this->goTable, $params);

        $error = $this->db->error();
        if (empty($error['code'])) 
            return true;
        
        return false;
    }

    /**
     * Function: getOauthOrg
     *
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function getOauthOrg($params)
    {
        return $this->getRow($this->goTable, '*', $params);
    }

    /**
     * Function: delete_oauth
     *
     * @param  [type] $id [description]
     * @param  [type] $demo [description]
     *
     * @return [type] [description]
     */
    function delete_oauth($id, $demo)
    {
        $this->db->delete($this->goTable, array('organisation_id' => $id, 'demo' => $demo));

        $error = $this->db->error();
        if (empty($error['code'])) 
            return true;
        
        return false;
    }

    /**
     * Function: updateOauth
     *
     * @param  [type] $organisation_id [description]
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function updateOauth($organisation_id, $params)
    {
        $db_table = $this->_getGatewaysDBTable('oauth');
        $this->db->where('organisation_id', $organisation_id)->update($db_table, $params);

        $error = $this->db->error();
        if (empty($error['code'])) 
            return true;
        
        return false;
    }

    
    ////////////////////////////////////////////////
    // SECTION: ONLINE GATEWAYS COMMOM MANAGEMENT //
    ////////////////////////////////////////////////
    
    /**
     * Function: is_user_conflicted
     *
     * @param  [type] $user_id [description]
     * @param  [type] $gateway [description]
     * @param  [type] $demo [description]
     *
     * @return bool [description]
     */
    function is_user_conflicted($user_id, $gateway, $demo)
    {
        $this->db->select('*')
                ->from($this->gmcTable)
                ->where('gateway', $gateway)
                ->where('demo', $demo);
        $this->db->group_start();
          $this->db->where('user_id', $user_id);
          $this->db->or_where('gateway_fitbox_id', $user_id);
        $this->db->group_end();

        $result = $this->db->get()->row();
        return ($result) ? $result : false;
    }

    /**
     * Function: get_match_conflict
     *
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function get_match_conflict($params)
    {
        return $this->getRow($this->gmcTable, '*', $params);
    }

    /**
     * Function: register_match_conflict
     *
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function register_match_conflict($params)
    {
        $this->db->insert($this->gmcTable, $params);

        $error = $this->db->error();
        if (empty($error['code'])) 
            return true;
        
        return false;
    }
}

?>
