<?php

//defined('BASEPATH') OR exit('No direct script access allowed');

// Cron setup on ubuntu:
// crontab -e
// 00 00 * * * php /var/www/fitbox.es/public_html/fitbox/index.php cron
// 30 02 * * * kinsay mysqldump -u fitbox --password='eNgj`z(Y5!=wP;?F' fitbox | gzip > /var/www/fitbox.es/backups/fitbox_$(date -d "today" +"%Y-%m-%d").sql.gz
// find /var/www/fitbox.es/backups/ -type f -mtime +7 -name '*.gz' -execdir rm -- '{}' \;
// /etc/init.d/cron restart
// cron log can be found by default at /etc/rsyslog.d/50-default.conf
// cron log has been moved to /var/log/cron.log


class Webhooks extends CI_Controller 
{
    function __construct() 
    {
        parent::__construct();

        // if(!$this->input->is_cli_request())
        // {
        //     log_message('debug',print_r('intento ejecución de WEBHOOKS desde cliente', TRUE));
        //     show_404();
        //      return;
        // }
        

        $this->load->database();
        $this->config->load('settings', TRUE);
        $this->load->model('box_model', 'box');
    }

    
    //////////////////////////
    //  Section: GOCARDLESS //
    //////////////////////////

    /**
     * Function: gocardless
     * manages how to preocess gocardless events.
     *
     * Parameters:
     * $box_id int -   If the integration is "PARTNERS" no BOX_ID has to be supplied.
     *                 Otherwise a is required in order to get the public and private keys of the box.           
     */
    function gocardless($box_id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || json_last_error() !== JSON_ERROR_NONE) 
        {
            //error 400
            $this->webhook_error(400, 'mensaje incorrecto');
        }

         //INIT 
        $this->load->model('payment_model', 'pay'); //pendiente -> eventually remove "pay"
        $this->load->library(array('gocardless'));

        //depending on the origin we will manage request in the right context (demo or live)
        $demo = $this->gocardless->is_Sandbox_request($_SERVER['HTTP_ORIGIN']);

        //check the headers and get the events
        $events = $this->gocardless->gc_get_events($demo);
        if($events === false)
            $this->webhook_error(498, "HTTP/1.1 498 Invalid Token");

        //set_up gocardless library and process events
        if($events !== FALSE)
        {
            foreach ($events as $event) 
            {
                //setUp gocardless library
                $box_id = $this->gocardless->get_box($event->links->organisation);
                if($box_id !== FALSE AND $this->gocardless->set_up($box_id) AND $this->gocardless->oauth_setup())
                {   
                    //check if event is new
                    if($this->gocardless->gc_is_event_processed($event, $demo) === FALSE)
                    {
                        // register event
                        $this->gocardless->gc_register_event($event, $demo);

                        //process the event
                        switch ($event->resource_type) 
                        {
                            case "mandates":
                                $this->gc_process_mandate_event($event, $demo);
                                http_response_code(200);
                                break;

                            case "subscriptions":
                                $this->gc_process_subscription_event($event, $demo);
                                http_response_code(200);
                                break;

                            case "payments":
                                $this->gc_process_payments_event($event, $demo);
                                break;

                            case "refunds":
                                //$this->gc_process_payments_event($event, $demo);
                                http_response_code(200);
                                break;

                            case "organisations":
                                $this->gc_process_organisations_event($event, $demo);
                                http_response_code(200);
                                break;

                            case "creditors":
                                $this->gc_process_creditors_event($event, $demo);
                                http_response_code(200);
                                break;

                            case "customers":
                                $this->gc_process_customers_event($event, $demo);
                                http_response_code(200);
                                break;

                            default:
                                $this->webhook_error(501, $event->resource_type);
                                break;
                        }
                    }
                    else
                    {
                        // duplicated request -> do nothing
                        http_response_code(208);
                    }
                }
            }
            
        }
    }

    /**
     * Function: gc_process_mandate_event
     *
     */
    function gc_process_mandate_event($event, $demo)
    {   
        try { 
            $error = false;

            $gateway = $this->gocardless->get_gateway();
            $params = array('txn_id' => $event->links->mandate, 
                            'demo' => $demo,
                            'gateway' => $gateway['pp'],
                            'box_id' => $gateway['box_id']
                        );

            switch ($event->action) 
            {
                case "created":
                    if(!$this->gocardless->gc_register_transaction('mandate', $params))
                        $error = true;
                    break;

                case "expired":
                case "failed":
                case "cancelled":
                    if(!$this->gocardless->gc_cancel_mandate($params))
                        $error = true;
                
                case "transferred":
                    //The mandate has been transferred to a different bank account. 
                    // do nothing
                    break;
                
                case "replaced":
                    if(!$this->gocardless->gc_replace_mandate($event->links->new_mandate, $params))
                        $error = true;
                    break;

                case "reinstated":
                case "submited":
                case "active":
                case "customer_approval_granted":
                case "customer_approval_skipped":
                case "submission_requested":
                case "resubmission_requested":
                     if(!$this->gocardless->gc_update_transaction('mandate', $params))
                        $error = true;
                    break;

            }

            return $this->gocardless->gc_register_event_result('mandates', $event, $error);

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
        
    }

    /**
     * Function: gc_process_subscription_event
     *
     */
    function gc_process_subscription_event($event, $demo)
    {
        try { 
            $error = false;

            $gateway = $this->gocardless->get_gateway();

            $params = array('txn_id' => $event->links->subscription, 
                            'demo' => $demo,
                            'gateway' => $gateway['pp'],
                            'box_id' => $gateway['box_id']);

            

            switch ($event->action) 
            {
                case "created":
                    if(!$this->gocardless->gc_register_transaction('subscription', $params))
                        $error = true;
                    break;

                case "customer_approval_granted":
                case "customer_approval_denied":
                case "paused":
                case "resumed":
                case "cancelled":
                case "finished":   
                    if(!$this->gocardless->gc_update_transaction('subscription', $params))
                        $error = true;
                    break;

                case "payment_created":
                case "amended":
                    // do nothing
                    break;
            }

            return $this->gocardless->gc_register_event_result('subscriptions', $event, $error);

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: gc_process_payment_event
     *
     */
    function gc_process_payments_event($event, $demo)
    {
        try { 
            $error = false;

            $gateway = $this->gocardless->get_gateway();
            $params = array('txn_id' => $event->links->payment, 
                            'demo' => $demo,
                            'gateway' => $gateway['pp'],
                            'box_id' => $gateway['box_id']
                        );

            switch ($event->action) 
            {
                case 'created':
                    if(!$this->gocardless->gc_register_transaction('payment', $params))
                        $error = true;
                    break;

                case 'customer_approval_granted':
                    // do nothing
                    break;

                case 'customer_approval_denied':
                    //nada por el momento
                    break;

                case 'submitted':
                case 'confirmed':
                case 'paid_out':
                case 'resubmission_requested':
                case 'late_failure_settled':
                case 'chargeback_settled':
                case 'cancelled':
                case 'charged_back':
                    if(!$this->gocardless->gc_update_transaction('payment', $params))
                        $error = true;
                    break;


                case 'surcharge_fee_credited':
                    break;

                case 'failed':
                    if(!$this->gocardless->gc_failed_payment($params))
                        $error = true;
                    break;


            }

            return $this->gocardless->gc_register_event_result('payments', $event, $error);

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: gc_process_refund_event
     *
     * @param  [type] $event [description]
     * @param  [type] $demo [description]
     *
     * @return [type] [description]
     */
    function gc_process_refund_event($event, $demo)
    {
        try { 
            $error = false;

            $gateway = $this->gocardless->get_gateway();
            $params = array('txn_id' => $event->links->refund, 
                            'demo' => $demo,
                            'gateway' => $gateway['pp'],
                            'box_id' => $gateway['box_id']
                        );

            switch ($event->action) 
            {
                case 'created':
                    if(!$this->gocardless->gc_register_transaction('refund', $params))
                        $error = true;
                    break;

                case 'failed':
                    if(!$this->gocardless->gc_failed_refund($params))
                        $error = true;
                    break;

                case 'paid':
                    if(!$this->gocardless->gc_update_transaction('refund', $params))
                        $error = true;
                    break;

                case 'refund_settled':
                    break;

                case 'funds_returned':
                    break;
            }

            return $this->gocardless->gc_register_event_result('refunds', $event, $error);

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: gc_process_organisations_event
     *
     * @param  [type] $event [description]
     * @param  [type] $demo [description]
     *
     * @return [type] [description]
     */
    function gc_process_organisations_event($event, $demo)
    {
        try {
            $error = false;

            $gateway = $this->gocardless->get_gateway();

            switch ($event->action) {
                case "disconnected":
                    $this->gocardless->gc_revoke($event->links->organisation, $demo);
                    break;

                default:
                    $this->webhook_error(403, 'Unexpected event action '.$event->action);
                    break;
            }
            
        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }

        return $this->gocardless->gc_register_event_result('organizations', $event, $error);
    }

    /**
     * Function: gc_process_creditors_event
     *
     * @param  [type] $event [description]
     * @param  [type] $demo [description]
     *
     * @return [type] [description]
     */
    function gc_process_creditors_event($event, $demo)
    {
        try {
            $error = false;

            switch ($event->action) {
                case "updated":
                    $creditor = $this->gocardless->getCreditors()->records[0];
                    $this->pay->updateOauth($creditor->id, array('status' => $creditor->verification_status));
                    break;

                default:
                    $this->webhook_error(403, 'Unexpected event action '.$event->action);
                    break;
            }
            
        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }

        return $this->gocardless->gc_register_event_result('creditors', $event, $error);
    }

    /**
     * Function: gc_process_customers_event
     *
     * @param  [type] $event [description]
     * @param  [type] $demo [description]
     *
     * @return [type] [description]
     */
    function gc_process_customers_event($event, $demo)
    {
        try { 
            $error = false;

            $mandate = $this->gocardless->gc_get_transaction('mandates', $params, TRUE);
            $gateway = $this->gocardless->get_gateway();

            $params = array('txn_id' => $event->links->mandate, 
                            'demo' => $demo,
                            'gateway' => $gateway['pp'],
                            'box_id' => $gateway['box_id']
                        );

            switch ($event->action) 
            {
                case "created":
                    // if does not exist in database
                    if($mandate === false)
                    {
                        if($this->gocardless->gc_register_transaction('mandate', $params) === false)
                            $error = true;
                    }
                    break;

                

            }

            return $this->gocardless->gc_register_event_result('custumers', $event, $error);

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    

    //////////////////////
    //  Section: STRIPE //
    //////////////////////
 
    /**
     * Function: stripe
     * manages stripe events
     */
    function stripe()
    {
        //INIT
        $this->load->model('box_model', 'box');
        $this->load->model('payment_model', 'pay');

        $this->load->library(array('stripe_lib','booking_lib'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || json_last_error() !== JSON_ERROR_NONE) 
        {
            //error 400
            $this->webhook_error(400, 'mensaje incorrecto');
        }

        $input = file_get_contents('php://input');
        $body = json_decode($input);

        if($body->data->object->object == 'payment_intent')
        {
            $transaction = $this->pay->getTransactionByTXN_ID($body->data->object->id);
        }
        else if($body->data->object->object == 'charge')
        {
            $transaction = $this->pay->getTransactionByTXN_ID($body->data->object->payment_intent);
        }
        
        if($transaction === FALSE)
        {
            $this->webhook_error(400, 'No existe la transaccion indicada.'.$body->data->object->id);
        }

        $this->pay->set_box($transaction->box_id);
        $this->box->set_box($transaction->box_id);

        $gateway = $this->pay->getGatewaySettings('stripe');
        if($gateway !== FALSE)
        {
            /// STRIPE initialization
            $this->stripe_lib->setSettings($gateway);

            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
            $endpoint_secret = $gateway['webhook_secret'];
            $event = null;

            try {
                $event = \Stripe\Webhook::constructEvent(
                    $input, $sig_header, $endpoint_secret
                );
            } catch(\UnexpectedValueException $e) {
                // Invalid input
                $this->webhook_error(400, $e->getMessage());
                exit();
            } catch(\Stripe\Exception\SignatureVerificationException $e) {
                // Invalid signature
                $this->webhook_error(400, $e->getMessage());
                exit();
            }
            
            if($event !== FALSE)
            {
                $details = '';

                switch ($event->type) 
                {   
                    
                    // Customer’s payment is authorized and ready for capture
                    case 'payment_intent.amount_capturable_updated':  
                        // renew membership accordingly
                        if($gateway['demo'] == 1)
                        {   
                            $trans = $this->pay->getTransaction($transaction->id);
                            if($trans->status == 'pending')
                            {
                                $result = $this->box->edit_user_membership($transaction->mu_id, array('status' => 'y', 'mem_expire' => $transaction->to));

                                if($result === FALSE)
                                {
                                    $this->webhook_error(403, 'No se pudo realizar la renovación');
                                }
                                else
                                {
                                    $params = array('status' => 'processed');
                                    $result = $this->pay->updateTransaction($transaction->id, $params);

                                    $intent = $this->stripe_lib->caprureIntent($body->data->object->id);
                                    if($intent === FALSE)
                                    {
                                        $this->webhook_error(400, $this->stripe_lib->api_error);
                                    }
                                }
                            }

                            

                        }

                    break;

                    //Customer’s payment succeeded
                    case 'payment_intent.succeeded':
                         $trans = $this->pay->getTransaction($transaction->id);
                        if($trans->status == 'processed' or $trans->status == 'pending')
                        {
                            // register payment as captured
                            $params = array('status' => 'succeeded');
                            $result = $this->pay->updateTransaction($transaction->id, $params);
                        }
                                           

                    break;
                    
                    //Customer’s payment was declined by card network or otherwise expired
                    case 'payment_intent.payment_failed':
                        // register payment as failed
                        $params = array('status' => 'failed');
                        $result = $this->pay->updateTransaction($transaction->id, $params);

                    break;

                    case 'payment_intent.canceled':
                        if($gateway['demo'] == 0)
                        {
                            $result = $this->pay->getTransaction($transaction->id);
                            if($result !== FALSE)
                            {
                                if($result->status == 'pending')
                                {
                                    // register payment as canceled
                                    $params = array('status' => 'canceled');
                                    if($gateway['demo'] == 0)
                                        $result = $this->pay->updateTransaction($transaction->id, $params);
                                }
                                else if ($result->status == 'processed')
                                {
                                    $result = $this->box->edit_user_membership($transaction->mu_id, array('mem_expire' => $transaction->from, 'membership_id' => $transaction->from_membership_id));
                                    if($result !== FALSE)
                                    {
                                        $params = array('status' => 'rolled_back');
                                        $this->pay->updateTransaction($transaction->id, $params); 
                                    }
                                    else
                                    {
                                        $this->webhook_error(403, 'No se pudo deshacer la renovación.');
                                    }
                                }
                            }
                            else
                            {
                                $this->webhook_error(403, 'No se pudo obtener el registro.');
                            }
                        }
                            

                    break;

                    case 'charge.captured':
                    case 'charge.succeeded':
                    case 'payment_intent.created':

                    break;

                    default:
                        // Unexpected event type
                        $this->webhook_error(403, 'Unexpected event type '.$event->type);
                    break;

                }

                http_response_code(200);
            }
            else
            {
                $this->webhook_error(400, $this->stripe_lib->api_error);
            }  
        }
        else
        {
            $this->webhook_error(500);
        }
    }


    /**
     * Function: webhook_error
     * manages errors by logging them and replying the request with an error
     */
    function webhook_error($code, $message = null)
    {
        if($code == 400)
        {
            if($message == null ) $message = 'invalid request'; 
            log_message('DEBUG',print_r('WEBHOOK DEBUG: error_'.$code.'->'.$message, TRUE));
            echo json_encode([ 'error' => $message ]);
        }
        else if($code == 403)
        {
            if($message == null ) $message = 'Internal server error'; 
            echo json_encode([ 'error' => $message ]);
            log_message('DEBUG',print_r('WEBHOOK DEBUG: error_'.$code.'->'.$message, TRUE));
        }
        else if($code == 501)
        {
            if($message == null ) $message = 'Internal server error'; 
            log_message('DEBUG',print_r('WEBHOOK DEBUG: error_'.$code.'->'.$message, TRUE));
            echo json_encode([ 'error' => $message ]);
        }    
        
        http_response_code($code);
    }


}
