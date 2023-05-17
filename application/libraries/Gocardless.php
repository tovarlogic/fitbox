<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
 
/** 
 * Gocardless Library for CodeIgniter 3.x 
 * 
 * Library for Gocardless payment gateway. 
 * It helps Gocardless_lib to be able to interact with payment_model with-out the need of custom functions in a controller. 
 * 
 * @package     CodeIgniter 
 * @version     3.0 
 *
 * @package     Fitbox
 * @category    Libraries 
 * @version     0.1 2020-04
 * @author kinsay <kinsay@gmail.com>
 *
 * @requires gocardless config file (placed in the config directory)
 * @requires payment_model
 */ 

/**
 * Class: Gocardless
 * Extention of Gocardless_lib to interact with the 'payment_model'. This way Gocardless_lib can be as generic and custom-poject agnostic as possible to mantain its modularity and re-usability.
 * 
 */
require_once (APPPATH.'/libraries/Gocardless_lib.php');
class Gocardless extends Gocardless_lib
{ 
    private $oauth = null;
    private $gateway = null;
    private $fitbox_gateway = null;

    public $conflict = null;
    public $mail = false;

    public $possible_status = array('mandates' => array('valid' => array(   'pending_submission', 
                                                                            'submitted',
                                                                            'active'),

                                                        'invalid' => array( 'pending_customer_approval',
                                                                            'failed',
                                                                            'cancelled',
                                                                            'expired')),
                                    

                                    'subscriptions' => array('valid' => array(  'pending_customer_approval',
                                                                                'active'),

                                                            'invalid' => array( 'customer_approval_denied',
                                                                                'finished',
                                                                                'cancelled',
                                                                                'paused')),

                                    'creditors' => array(   'invalid' => array( 'action_required',
                                                                                'in_review'),

                                                            'valid' => array (  'successfull'))
                                    );


    
    /**
     * Function: __construct
     * initializes the library by loading the model and config file
     */
    function __construct()
    { 
        $this->CI =& get_instance();

        $this->CI->load->model('payment_model', 'pay');

        $this->CI->config->load('gocardless', TRUE);
        $this->gc_config = $this->CI->config->item('gocardless');   
         
    } 

    /**
     * Function: get_oauth
     *
     * @param  [type] $field [description]
     *
     * @return [type] [description]
     */
    function get_oauth($field = null)
    {
        if($field === null AND !empty($this->oauth))
            return $this->oauth;
        else if ($field !== null AND !empty($this->oauth))
            return $this->oauth->$field;

        return false;
    }

    /**
     * Function: get_gateway
     *
     * @param  [type] $field [description]
     *
     * @return [type] [description]
     */
    function get_gateway($field = null)
    {
        if($field === null AND !empty($this->gateway))
            return $this->gateway;
        else if ($field !== null AND !empty($this->gateway))
            return $this->gateway[$field];

        return false;
    }


    /**
     * Function: set_up
     * Sets-up the library variables and both gocardless and oauth2 clients (the last one, only if integration == partner)
     *
     * Parameters:
     * $box_id int
     *
     * @return bool
     */
    function set_up($box_id)
    {
        $this->CI->pay->set_box($box_id);
        $this->CI->box->set_box($box_id);

        $this->gateway = $this->CI->pay->getGatewaySettings('gocardless', true);
        if($this->gateway !== FALSE)
        {
            /// Gocardless initialization
            if($this->setSettings($this->gateway))
            {
                if($this->gc_config['integration'] == 'partner')
                {
                    if($this->oauth_setup())
                    {
                        $params = array('box_id' => $this->CI->pay->box_id, 
                                    'gateway' => $this->gateway['pp'],
                                    'demo' => $this->gateway['demo']);

                        $this->oauth = $this->CI->pay->getOauthOrg($params);

                        return true;
                    }
                }
                else
                    return true;     
            }
        }

        return false; 
    }

    /**
     * Function: set_fitbox_gateway
     *
     * @param  [type] $demo [description]
     */
    function set_fitbox_gateway($demo)
    {
        $this->fitbox_gateway = $this->CI->pay->getGateway(array('box_id' => 0, 'name' => 'gocardless', 'demo' => $demo));
        if($this->fitbox_gateway !== FALSE)
            return $this->fitbox_gateway;

        return false;
    }

    /**
     * Function: get_fitbox_gateway
     *
     * @return [type] [description]
     */
    function get_fitbox_gateway()
    {
        return $this->fitbox_gateway;
    }

    /**
     * Function: get_box
     *
     * @param  [type] $organisation_id [description]
     *
     * @return [type] [description]
     */
    function get_box($organisation_id)
    {
        return $this->CI->pay->getOauthOrg(array('organisation_id' => $organisation_id))->box_id;
    }
    
    ///////////////////////////////
    //Section: Oauth //
    ///////////////////////////////
    
    /**
     * Function: is_ready
     * returns TRUE if the gocardless gateway is fully configured, active, organization is connected and verified
     * 
     * @return bool 
     */
    function is_ready()
    {
        $this->api_error = null;

        if($this->is_configured())
        {
            if($this->is_active())
            {
                if($this->gc_config['integration'] == 'partner')
                {
                   if($this->is_connected())
                    {
                        if($this->is_verified())
                        {
                            return true;
                        }
                    } 
                }
                else if($this->gc_config['integration'] == 'standard')
                    return true;
            }
        }        

        return false;
    }

    /**
     * Function: is_configured
     *
     * @return bool [description]
     */
    function is_configured()
    {
        if($this->gc_config['integration'] == 'standard')
        {
            if($this->gateway->private_key != null AND $this->gateway->public_key != null AND $this->gateway->demo_webhook_secret != null)
            {
                return true;
            }
            $this->api_error = 'gc_error_check_4';
        }
        else
            return true;

        return false;
    }

    /**
     * Function: is_active
     *
     * @return bool [description]
     */
    function is_active()
    {
        $this->api_error = null;
        
        //check if it is active in the client box
        if($this->gateway['active'] == 1)
        {
            if($this->gc_config['integration'] == 'partner')
            {
                //check if it is active in FITBOX
                $params = array('box_id' => '0', 'name' => 'gocardless');
                $gateway = $this->CI->pay->getGateway($params);
                if($gateway->active == 1)
                    return true;
                else
                    $this->api_error = 'gc_error_check_3';
            }
            else if($this->gc_config['integration'] == 'standard')
                return true;
        }

        $this->api_error = 'gc_error_check_5';

        return false;
    }

    /**
     * Function: is_connected
     * returns TRUE if the gocardless gateway is fully configured, active and organization is connected
     * 
     * @return bool
     */
    function is_connected()
    {
        $this->api_error = null;

        //check if it is connected
        if(!empty($this->oauth))
        {
            return true;
        }
        
        $this->api_error = 'gc_error_check_2';
        return false;
    }

    /**
     * Function: is_verified
     * returns TRUE if the client (gocardless creditor conection) is verified
     * 
     * @return bool [description]
     */
    function is_verified()
    {
        if($this->oauth->status == 'verified')
            return true;

        $this->api_error = 'gc_error_check_1';
        return false;
    }
   


    /**
     * Function: update_oauth_status
     * Updates the oauth_status variable and database to the latest real value through the API
     * 
     * @return [type] [description]
     */
    function update_oauth_status($re_init = false)
    {
        if($re_init)
            $this->set_up($this->CI->pay->box_id);

        $creditor = $this->getCreditors()->records[0];

        if($creditor->verification_status != false)
        {
            if($creditor->verification_status != $this->oauth->status)
            {
                $this->oauth->status = $creditor->verification_status;
                $this->CI->pay->updateOauth($this->oauth->organisation_id, array('status' => $creditor->verification_status));  
            }
            return true;
        }
        return false;        
    }

    /**
     * Function: gc_oauth_flow
     * Initiates the oauth_flow to connect a new client
     *
     * Parameters:
     * $customer_data array - to prefill the sing-up form
     *      
     * @return [type] [description]
     */
    function gc_oauth_flow($customer_data)
    {
        // if connection has not been created yet, do so.
        if(!$this->is_connected())
        {
            //register event
            $dataDB['gateway'] = $this->gateway['pp'];
            $dataDB['box_id'] = $this->CI->pay->box_id;
            $dataDB['demo'] = $this->gateway['demo'];

            $dataDB['type'] = 'organisations';
            $dataDB['action'] = 'connection_flow';
            $dataDB['status'] = 'api_request';

            $this->CI->pay->registerGatewaysEvent2($dataDB);

            //initiate connection flow
            $authorizeUrl = $this->oauth_flow($customer_data);

            return $authorizeUrl;
        }
  
        return false;
    }

    /**
     * Function: gc_oauth_callback
     * Completes the oauth flow and saves the connection data into the database
     * 
     * @return bool
     */
    function gc_oauth_callback()
    {
        $data = $this->oauth_callback();

        if($data !== FALSE)
        {
            $data['box_id'] = $this->CI->pay->box_id;
            $data['gateway'] = $this->gateway['pp'];

            $token = explode("_", $data['access_token']);
            $data['demo'] = ($token[0] == 'sandbox')? 1 : 0;
            

            //if organization in not registered yet...
            if(!$this->is_connected())
            {
                $data['status'] = 'created';
                $this->CI->pay->addOauth($data);

                //update settings to include the new connection
                
                $this->settings['private_key'] = $data['access_token'];
                $this->settings['organisation_id'] = $data['organisation_id'];
            }
            
            //check status
            if($this->update_oauth_status(true))
            {
                if($this->oauth->status == 'action_required')
                    return $this->oauth_config['verify_url'];
                else
                    return true;
            }
        }
        
        return false;
    }

    /**
     * Function: gc_revoke
     * disconnects an organisation and delete it from database
     *
     * Parameters
     * $org_id varchar - el identificador único del box cliente en gocardless
     * $demo bool - indica si es en un contexto de pruebas o no
     * 
     * @todo  revisar si $demo es necesario
     *
     */
    function gc_revoke($org_id = null, $demo = null)
    {
        return $this->CI->pay->delete_oauth($org_id, $demo);
    }

    ////////////////////////////
    //Section: Webhook Events //
    ////////////////////////////
    
    /**
     * Function: gc_get_events
     * Checks if the message is legit and in that case returns the events received
     * 
     * @param  [type] $webhook_secret [description]
     *
     * @return [type] [description]
     */
    function gc_get_events($demo)
    {
        if($this->set_fitbox_gateway($demo))
        {
            $webhook_secret = ($demo)?  $this->fitbox_gateway->demo_webhook_secret : 
                                        $this->fitbox_gateway->webhook_secret;

            $request_body = file_get_contents('php://input');
            $headers = getallheaders();
            $signature_header = $headers["Webhook-Signature"];

            try {
                $events = \GoCardlessPro\Webhook::parse($request_body,
                                                        $signature_header,
                                                        $webhook_secret);        
                return $events;
            } catch(\GoCardlessPro\Core\Exception\InvalidSignatureException $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Function: gc_register_event
     *
     */
    function gc_register_event($event, $demo)
    {
        $gateway = $this->get_gateway();
        //data to registerc
            $dataDB = array('gateway' => $gateway['pp'],
                            'box_id' => $gateway['box_id'], 
                            'demo' => $demo,
                            'event_id' => $event->id,
                            'type' => $event->resource_type,
                            'action' => $event->action,
                            'cause' => $event->details->cause,
                            'status' => 'wh_request'
            );

            //select the link (txn_id)
            $link = null;
            switch ($event->resource_type) {
                case 'creditors':
                case 'organisations':
                    $link = 'organisation';
                    break;

                case 'mandates':
                    $link = 'mandate';
                    break;

                default:
                    $link = $event->resource_type;
                    break;

            }
            $dataDB['txn_id'] = ($event->links->$link)? $event->links->$link : $event->links->organisation;

            //register event
            return $this->CI->pay->registerGatewaysEvent2($dataDB);
    }

    /**
     * Function: gc_is_event_processed
     *
     * Parameters: 
     * $event gocardless object
     * $demo 1 or 0
     *
     * Returns:
     * bool 
     */
    function gc_is_event_processed($event, $demo)
    {
        if(is_null($event->id))
        {
            return false;
        }
        else
        {
            //check if event already registered
           $params = array('event_id' => $event->id, 
                            'gateway' => $this->gateway['pp'], 
                            'demo' => $demo);
            $db_event = $this->CI->pay->getGatewayEvent($params);

            if($db_event !== FALSE)
            {
                if($db_event->status == 'processed')
                    return true;
            }

            return false;
        }
    }

    /**
     * Function: gc_register_event_result
     *
     * @param  [type] $error [description]
     *
     * @return [type] [description]
     */
    function gc_register_event_result($type, $event, $error)
    {
        $name = substr($type, 0, -1); //to remove the last 'S'
        $dataDB = array('gateway' => $this->gateway['pp'],
                        'box_id' => $this->gateway['box_id'],
                            'demo' => $this->gateway['demo'],
                            'event_id' => $event->id,
                            'type' => $type,
                            'txn_id' => $event->links->$name,
                            'action' => $event->action, 
                            'cause' => $event->details->cause, 
                            );
        if(!empty($event->metadata->fitbox_plan_id)) $dataDB['mu_id'] = $event->metadata->fitbox_plan_id;
        if(!empty($event->metadata->fitbox_user_id)) $dataDB['user_id'] = $event->metadata->fitbox_user_id;

        if($error === TRUE) 
        {
            $dataDB['status'] = 'error';
            $this->CI->pay->registerGatewaysEvent2($dataDB);

            return false;
        } 
        else if($error === FALSE)
        {
            $dataDB['status'] = 'processed';
            $this->CI->pay->registerGatewaysEvent2($dataDB);

            return true;
        }
    }


    //////////////////////
    //Section: Mandates //
    //////////////////////
    
    /**
     * Function: gc_cancel_mandate
     *
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function gc_cancel_mandate($params)
    {
        //get it from our database
        $mandate = $this->CI->pay->getGatewayTransactions('mandates', array('txn_id' => $params['txn_id']), TRUE);

        //if does not exist create it
        if($mandate === false)
        {
            if(!$this->gc_register_transaction('mandate', $params))
                return false;
        }
        
        //get it from GC database
        $mandate = $this->getmandate($params['txn_id']);
        if($mandate !== false)
        {
            $subscriptions =  $this->CI->pay->getGatewayTransactions('subscriptions', array('mandate_id' => $params['txn_id']), FALSE);
            if($subscriptions !== false)
            {
                foreach ($subscriptions as $subscription) {
                    if($subscription->status !== 'cancelled')
                        $this->gc_cancel_subscription(array('txn_id' => $subscription->txn_id));
                }
            }
            
            $params['status'] = $mandate->status;
            return $this->CI->pay->updateGatewayTransaction($params['txn_id'], 'mandates', $params);
        }

        return false; 
    }

    /**
     * Function: gc_replace_mandate
     *
     * @param  [type] $new_mandate [description]
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function gc_replace_mandate($new_mandate, $params)
    {   
        $old_mandate = $params['txn_id'];

        //update or create the new mandate
        $params['txn_id'] = $new_mandate;
        if(!$this->gc_update_transaction('mandate', $params))
            return false;

        // move old_mandate's subscriptions to the new one
        $subscriptions =  $this->CI->pay->getGatewayTransactions('subscriptions', array('mandate_id' => $old_mandate), FALSE);
        if($subscriptions !== false)
        {
            foreach ($subscriptions as $subscription) 
            {
                if($subscription->status !== 'cancelled')
                {
                    $this->gc_update_transaction('subscription', array('txn_id' => $subscription->txn_id), array('mandate_id' => $new_mandate));
                }
            }
        }

        //cancel old_mandate
        $params['txn_id'] = $old_mandate;
        if($this->gc_update_transaction('mandate', $params, array('status' => 'cancelled')))
            return true;

        return false;
    }


    ////////////////////////
    // Section: Customers //
    ////////////////////////

    /**
     * Function: gc_match_users
     *
     * @param  [type] $customer [description]
     * @param  [type] $box_id [description]
     *
     * @return [type] [description]
     */
    function gc_match_users($customer, $box_id)
    {
        $this->CI->load->model('box_model', 'box');
        $this->CI->box->set_box($box_id);

        $user_id = 0;
        $match = false; //if no exact match we shoud ask the box admins to match manually

        // arrange possible matching options to check
        $options = array();

        if(isset($customer->metadata->fitbox_user_id)) $options['metadata'] = $customer->metadata->fitbox_user_id;
        
        $user = $this->CI->box->getUserByEmail($customer->email);
        if($user !== false) $options['email'] = $user;

        ///por nombre y apellidos
        // pendiente -> no critico para una version beta
        // 
        // pendiente tb comprobar si un fitbox_user_id ya tiene otro GC customer asignado -> en tal caso q hacer¿?

        //check possible options
        foreach ($options as $key => $usr) 
        {
            if($key == 'metadata')
                $usr = $this->CI->box->getUser($usr, false);

            //if user exists in the box
            if($usr !== false)
            {
                if($usr->email == $customer->email AND $usr->first_name == $customer->given_name)
                {
                    $user_id = $usr->id;
                    $match = true;
                    break;
                }
            }
        }


        //if exact match (name and email) manage it
        if($match === true)
        {
            //check for conflicts
            if(isset($customer->metadata->fitbox_user_id) AND $customer->metadata->fitbox_user_id != $user_id)
            {
                //conflict -> possible human error filling up fitbox_id in GC
                $this->conflict = true; 

                // check if conflict has been discarded manually:
                $params = array('customer_id' => $customer->id,
                                'user_id' => $user_id,
                                'gateway' => $this->gateway['pp']);

                $prev_match = $this->CI->pay->get_match_conflict($params);
                if($prev_match !== false)
                {
                    if($prev_match->status == 'discarded' AND $prev_match->user_id == $user_id) $this->conflict = false;
                }
                
                //if conflict remains then register it
                if($this->conflict == true)
                {
                    $data = array(  'customer_id' => $customer->id,
                                    'user_id' => $user_id,
                                    'gateway_fitbox_id' => $customer->metadata->fitbox_user_id,
                                    'gateway' => $this->gateway['pp'],
                                    'demo' => $this->gateway['demo'],
                                    'status' => 'pending');

                    $user_id = 0;
                    $this->CI->pay->register_match_conflict($data);
                }
            }

            if($this->conflict === false AND !isset($customer->metadata->fitbox_user_id))
            {
                $data = array('metadata' => array('fitbox_user_id' => $user_id));
                $result = $this->updateCustomer($customer->id, $data);
            }
        }

        return $user_id;
    }

    /**
     * Function: gc_create_customer
     *
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function gc_create_customer($params)
    {
        //get it from gocardless database
        $customer = $this->getCustomer($params['txn_id']);

        //match GC and FITBOX users
        $params['user_id'] = $this->gc_match_users($customer, $params['box_id']);

        //in case of conflict report via mail to box admins
        if($this->conflict === true)
        {
            $emails = array();
            $groups = array(2, 3, 6, 7);
            $recipients = $this->CI->box->get_users($gateway['box_id'], $groups, array('active' => 1));
            foreach ($recipients as $recip) 
            {
                //$emails[] = array('email' => $recip['email'], 'name' => $recip['first_name']);
                $emails[] = $recip['email'];
            }
            $data['customer_id'] = $mandate->links->customer;
            $data['customer_mail'] = $customer->email;
            $data['customer_name'] = $customer->given_name.' '.$customer->family_name;
            $data['reference'] = $mandate->reference;

            $this->send_mail($gateway['box_id'], $emails, $data, 'gc_customer_conflict');
        }

        $params['status'] = 'created';

        return $this->CI->pay->addGatewayTransaction('customers', $params);
    }

    ///////////////////////////
    //Section: Subscriptions //
    ///////////////////////////

    
    /**
     * Function: gc_cancel_subscription
     *
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function gc_cancel_subscription($params)
    {
        $params['status'] = 'cancelled';

        return $this->gc_update_transaction('subscription', $params);
    }

    function gc_create_subscription($mu, $customer_data, $product_name)
    {
        log_message('DEBUG',print_r('gc_create_subscription',true));
        //check for customer and mandate
        // if exists create subscription
        // if not initiate_flow
        $params = array('gateway' => $this->gateway['pp'],
                        'box_id' => $this->gateway['box_id'],
                        'demo' => $this->gateway['demo'],
                        'user_id' => $mu->user_id,
                        'status' => 'created');
        //check if there is already either a confirmed customer id or a conflicted one. 
        ////If not, create customer, mandate and subscription.
        ////If so, then check for active subscriptions and mandates to avoid duplication
        $gw_customer = $this->CI->pay->getGatewayTransactions('customers', $params, TRUE);
        if($gw_customer != FALSE)
        {
            $gw_params = array('customer' => $gw_customer->txn_id);
            $customer = $this->getCustomer($gw_customer->txn_id);

            if($customer != FALSE)
            {
                $gw_mandates = $this->getMandates($gw_param);

                $subscription = FALSE;
                $mandate = FALSE;

                if($gw_mandates != FALSE)
                {
                    //check if any active mandate
                    foreach ($gw_mandates as $gw_mandate)
                    {
                        if(in_array($gw_mandate->status, $this->possible_status['mandates']['valid']))
                        {
                            $mandate = $gw_mandate->id;
                            break;
                        }
                    }

                    if($mandate !== false)
                    {
                        $params['mu_id'] = $mu->id;
                        $gw_subscriptions = $this->CI->pay->getGatewayTransactions('subscriptions', $params, TRUE);
                        if($gw_subscriptions !== false)
                        {
                             //check if any active subscription
                            foreach ($gw_subscriptions as $gw_subscription)
                            {
                                if(in_array($gw_subscription->status, $this->possible_status['subscriptions']['valid']))
                                {
                                    $subscription = $gw_subscription->id;
                                    break;
                                }
                            }

                            if($subscription !== false)
                            {
                                //$this->CI->session->set_flashdata('info', 'No se ha realizado una nueva subscripción porque ya contaba con una subscripción activa.');
                                return false;
                            }
                        }
                        else
                        { 
                            //create subscription
                            $params = array(
                                'name' => $mu->title,
                                'amount' => (int)$mu->price*100,
                                'currency' => $this->gateway['currency'],
                                'interval' => $mu->days,
                                'retry_if_possible' => TRUE,
                                'links' => array('mandate' => $mandate),
                                'metadata' => array('fitbox_plan_id' => $mu->id));

                            switch ($mu->period) 
                            {
                                case 'W':
                                    $params['interval_unit'] = 'weekly';
                                    break;

                                case 'M':
                                    $params['interval_unit'] = 'monthly';
                                    $params['day_of_month'] = 3;
                                    break;
                                
                                case 'Y':
                                    $params['interval_unit'] = 'yearly';
                                    $params['day_of_month'] = 3;
                                    break;
                            }

                            $subscription = $this->createSubscription($params)->api_response;
                            if($subscription)
                            {
                                if($subscription->status_code == "201")
                                {
                                    $params = array('mu_id' => $mu->id, 
                                                    'user_id' => $mu->user_id,
                                                    'gateway' => $this->gateway['pp'],
                                                    'box_id' => $this->gateway['box_id'],
                                                    'txn_id' => $subscription->body->subscriptions->id);

                                    $this->gc_register_transaction('subscription', $params);
                                    return true;
                                }
                                else
                                {
                                    // pendiente error
                                }
                            }
                            else
                            {

                            }
                        }
                    }
                    else
                    {
                        $this->CI->session->set_flashdata('error', 'No tienes mandatos activos en "'.strtoupper($gtw_name).'". Por favor, consulte con el administrador de su box para que lo solucione.');
                        $this->profile();
                        // pendiente -> por el momento no puedo re-instate pq GC lo restringe 
                        //check if any cancelled mandate to reinstate
                        // $reinstated = false;
                        // foreach ($gw_mandates as $gw_mandate)
                        // {
                        //     if(in_array($gw_mandate->status, array('cancelled')))
                        //     {
                        //         $result = $this->reinstateMandate($gw_mandate->id);
                        //         if($result)
                        //         {
                        //             break;
                        //         }
                        //         else
                        //             $reinstated = $gw_mandate->id;
                        //     }
                        // }

                        // if($reinstated !== false)
                        // {
                        //     $subscription = $this->gc_createSubscription($reinstated, $mu->id, $gateway);
                        // }
                    }
                }
                else
                {
                    // pendiente -> Que hacer¿? crear mandato directamente en GC o crear un nuevo flujo¿?
                }
            }
            else
            {
                //pendiente ->update info y reiniciar proceso
            }                                                                     
        }
        else //no customer registered in GC
        {
            $user_id = $this->CI->session->userdata('user_id');

            $conflicted_customer = $this->CI->pay->is_user_conflicted($user_id, $this->gateway['pp'], $this->gateway['demo']);
            if($conflicted_customer === true)
            {
                $this->CI->session->set_flashdata('error', 'Existe un conflicto de datos en la pasarela. Por favor, consulte con el administrador de su box para que lo solucione.');
                $this->profile();
            }
            else
            {
                $this->CI->session->sess_regenerate(TRUE);
                $box_name = $this->CI->box->getBox()->name;
                $metadata = array('fitbox_box_id' => $this->gateway['box_id']);

                $redirectFlow = $this->initiateFlow($customer_data, $box_name, session_id(), $metadata);
                if($redirectFlow !== FALSE)
                {
                    /// Register event
                    $dataDB = array(
                            'gateway' => $this->gateway['pp'],
                            'box_id' => $this->CI->box->box_id, 
                            'user_id' => $this->CI->session->userdata('user_id'),
                            'mu_id' => $mu->id,
                            'action' => 'initiate',
                            'status' => 'created'                            
                        );

                    $result = $this->CI->pay->registerGatewaysEvent($redirectFlow->id, 'redirect_flow', $this->gateway['demo'], $dataDB);
                    
                    $data['redirect_url']= $redirectFlow->redirect_url;

                    //this was the only way I could do a redirect without any CORS error
                    $this->CI->load->view('backend/athlete/redirect', $data);
                    //header('location: '.$redirectFlow->redirect_url);

                }
            }
        }
        return false;
    }

    ///////////////////////
    // Section: Payments //
    ///////////////////////

    /**
     * Function: gc_failed_payment
     *
     * @param  [type] $params [description]
     * @param  bool $retry [description]
     *
     * @return [type] [description]
     */
    function gc_failed_payment($params, $retry = false)
    {
        if($this->gc_update_transaction('payment', $params))
        {
            //get it from our database
            $payment = $this->CI->pay->getGatewayTransactions('payments', array('txn_id' => $params['txn_id']), TRUE);
            
            $emails = array();
            $user = $this->CI->box->getUser($payment->user_id);
            $emails[] = $user->email;

            $data['customer_name'] = $user->first_name;
            $data['box_name'] = $this->CI->box->getBox()->name;
            $mem_id = ($payment->mu_id != 0)? $this->CI->box->getUserMembership($payment->mu_id)->membership_id : 0;
            $data['mem_name'] = ($mem_id != 0)? $this->CI->box->getMembership($mem_id)->name : 'tu plan';
            $data['amount'] = $payment->amount/100;

            $template = ($payment->retry === true)? 'gc_payment_failed_retry' : 'gc_payment_failed';
            if($this->send_mail($params['box_id'], $emails, $data, $template))
                return true;
        }

        return false;
    }

    /////////////////////
    //Section: Refunds //
    /////////////////////


      ////////////////////////////////
     // Section: Generic functions //
    ////////////////////////////////

    function gc_create_transaction($type, $params)
    {
        //for every transaction to be created must exist a superior one
        $superior_name = $this->gc_get_superior($type);
        //if there is a superior hierarchy check if there is any superior_transaction
        // with current gocardless_pro API version is not possible to create a mandate directly, its mandatory to initiate_flow
        if($superior_name)
        {        
            //get superior from our database
            $superior_transaction = $this->CI->pay->getGatewayTransactions($superior_name.'s', array('txn_id' => $params['links'][$superior_name]), TRUE);
            if($superior_transaction !== false)
            {
                $function_name = 'create'.ucfirst($type);

                if($result = $this->$function_name($params))
                    return $result;
            }
            else
            {
                return $this->gc_create_transaction($superior_name, $params);
            }
        }
        //if there is no superior hierarchy then initiate_flow (for customer and mandate creation)
        else
        {
            $params2 = array('box_id' => $this->gateway['box_id'], 
                             'gateway' => $this->gateway['pp'],
                             'demo' => $this->gateway['demo'],
                            'user_id' => $this->CI->session->userdata('user_id') );
            $db_customer = $this->CI->pay->getGatewayTransactions('customers', $params2, TRUE);
            //$customer = $this->getCustomer();
        } 

        return false; 
    }

    /**
     * Function: gc_register_transaction
     * Generic function to create a transaction valid for all transactions except for Oauth/creditors/customers
     * 
     * @param  [type] $type [description]
     * @param  [type] $params [description]
     * 
     * @return [type] [description]
     */
    function gc_register_transaction($type, $params)
    {
        //get it from our database
        $transaction = $this->CI->pay->getGatewayTransactions($type.'s', array('txn_id' => $params['txn_id']), TRUE);
        //if does not exists create it, otherwise do nothing. 
        if($transaction === FALSE)
        {
            // create loop, until reaching the top (customer), to register higher hierarchies if they dont exist in database either.
            $superior_name = $this->gc_get_superior($type);
            //get it from gocardless database
            $function_name = 'get'.ucfirst($type);
            $transaction = $this->$function_name($params['txn_id']);

            if($type != 'customer' AND !empty($transaction->links->$superior_name))
            {                
                $params_next = $params;
                $params_next['txn_id'] = $transaction->links->$superior_name;
                if($type == 'subscription') unset($params_next['mu_id']);
                if(!$this->gc_register_transaction($superior_name, $params_next))
                    return false;
            }

            if($transaction !== FALSE)
            {
                //gestión de conflictos
                if($type == 'customer' AND empty($transaction->metadata->fitbox_user_id))
                {
                    //pendiente
                }
                else if($type == 'subscription' AND empty($transaction->metadata->fitbox_plan_id))
                {
                    //pendiente
                }

                //prepare data
                $dataDB = $params;
                $dataDB ['status'] = $transaction->status;
                
                if(!empty($transaction->retry_if_possible)) $dataDB['retry'] = $transaction->retry_if_possible;
                if(!empty($transaction->amount)) $dataDB['amount'] = $transaction->amount;

                

                if($type == 'customer')
                {
                    $dataDB['user_id'] = $transaction->metadata->fitbox_user_id;
                    if(is_null($dataDB ['status'])) $dataDB ['status'] = 'cancelled';                   
                }
                else
                {
                    $superior = $this->CI->pay->getGatewayTransactions($superior_name.'s', $params_next);

                    $dataDB['box_id'] = $superior[0]->box_id;
                    $dataDB['user_id'] = $superior[0]->user_id;
                    $dataDB[$superior_name.'_id'] = $transaction->links->$superior_name;
                }

                if($type == 'subscription')
                {
                    $dataDB['mu_id'] = $transaction->metadata->fitbox_plan_id;
                }
                else if($type != 'customer' AND $type != 'mandate')
                {
                    $dataDB['mu_id'] = $superior[0]->mu_id; 
                }

                //particular case of payments and refunds
                if(!empty($transaction->charge_date)) $dataDB['charge_date'] = $transaction->charge_date;
                if(!empty($transaction->amount)) $dataDB['amount'] = $transaction->amount;
                if(!empty($transaction->amount_refunded)) $dataDB['refounded'] = $transaction->amount_refunded;
                if(!empty($transaction->retry_if_possible)) $dataDB['retry'] = $transaction->retry_if_possible;
                
                $this->CI->load->library('booking_lib');
                $mu = $this->CI->box->getUserMembership($dataDB['mu_id']);
                $mem = $this->CI->box->getMembership($mu->membership_id);

                if($mu->status == 'y' || $mu->status == 'g')
                {
                    $from = $this->CI->booking_lib->calculateFrom($mu->mem_expire, 1);
                    $to = $this->CI->booking_lib->calculateExpiration($mem->days, $mem->period, $from);
                }
                else 
                {
                    $from = $this->CI->booking_lib->calculateFrom();
                    $to = $this->CI->booking_lib->calculateExpiration($mem->days, $mem->period);
                }

                $now = new DateTime('now');

                $dataDB2 = array(   'type' => 'renew',
                                    'from_membership_id' => $mu->membership_id, 
                                    'to_membership_id' => $mu->membership_id,
                                    'from' => $from,
                                    'to' => $to,
                                    'coupon_id' => 0,
                                    'notes' => 'Automatic GoCardless payment');

                $result = $this->CI->pay->registerPayment($dataDB, $dataDB2);

                if($result !== FALSE)
                {
                    // renew membership
                    $dataDB2 = array('user_id' => $dataDB['user_id'],
                                    'box_id' => $dataDB['box_id'],
                                    'mem_expire' => $to,
                                    'status' => 'y');

                    $this->CI->box->edit_user_membership($dataDB['mu_id'], $dataDB2);
                }

                return $result;
            }
        }
        else
        {
            return true;
        }

        return false;
    }

    /**
     * Function: gc_update_transaction
     *
     * @param  [type] $type [description]
     * @param  [type] $params [description]
     * @param  [type] $override [description]
     *
     * @return [type] [description]
     */
    function gc_update_transaction($type, $params, $override = null)
    {
        //create if does not exist
        if(!$this->gc_register_transaction($type, $params))
        {
            return false;
        }
        
        //get it from GC database
        $function_name = 'get'.$type;
        $transaction = $this->$function_name($params['txn_id']);
        if($transaction !== false)
        {
            $params['status'] = $transaction->status;

            //particular case of payments and refunds
            //$params['txn_id'] = $payment->id;
            if(!empty($transaction->charge_date)) $params['charge_date'] = $transaction->charge_date;
            if(!empty($transaction->amount)) $params['amount'] = $transaction->amount;
            if(!empty($transaction->amount_refunded)) $params['refounded'] = $transaction->amount_refunded;
            if(!empty($transaction->retry_if_possible)) $params['retry'] = $transaction->retry_if_possible;

            if(!empty($override))
            {
                foreach ($override as $key => $value) {
                    $params[$key] = $value;
                }
            }
            //Register in database
            return $this->CI->pay->updateGatewayTransaction($params['txn_id'], $type.'s', $params);
        }

        return false;
    }

    /**
     * Function: gc_get_superior
     *
     * @param  [type] $type [description]
     *
     * @return [type] [description]
     */
    private function gc_get_superior($type)
    {
        $name = null;

        switch ($type) 
        {
            case 'refund':
                $name = 'payment';
                break;

            case 'payment':
                $name = 'subscription';
                break;

            case 'subscription':
                $name = 'mandate';
                break;

            case 'mandate':
                $name = 'customer';
                break;

            default:
                $name = false;
                break;
        }

        return $name;
    }

    /**
     * Function: gc_get_subordinate
     *
     * @param  [type] $type [description]
     *
     * @return [type] [description]
     */
    private function gc_get_subordinate($type)
    {
        $name = null;

        switch ($type) 
        {
            case 'customer':
                $name = 'mandate';
                break;

            case 'mandate':
                $name = 'subscription';
                break;

            case 'subscription':
                $name = 'payment';
                break;
            
            default:
                $name = false;
                break;
        }

        return $name;
    }

    ////////////////////
    // SECTION: EMAIL //
    ////////////////////
    
    /**
     * Function: setup_mail
     *
     * @return [type] [description]
     */
    function setup_mail()
    {
        $this->CI->config->load('communications_system', TRUE);
        $email_config = $this->CI->config->item('email_default','communications_system');

        $this->CI->load->library('email', $email_config['settings']);

        $this->mail = true;
    }

    /**
     * Function: send_mail
     *
     * @param  [type] $box_id [description]
     * @param  [type] $emails [description]
     * @param  [type] $data [description]
     * @param  [type] $template [description]
     *
     * @return [type] [description]
     */
    function send_mail($box_id, $emails, $data, $template)
    {
        if($this->mail === false)
            $this->setup_mail();
        log_message('debug',$template);
        $this->CI->email->set_newline("\r\n");
        $this->CI->email->to($emails);
        $this->CI->email->from('info@fitbox.es', "FitBox");
        //$this->email->bcc("kinsay.spam@gmail.com");

        $sandbox_subject = ($this->enviroment == 'sandbox')? '(modo pruebas)' : '';
        $data['sandbox_body'] = ($this->enviroment == 'sandbox')? '<p style="color:red">Este correo ha sido enviado en "modo pruebas" y, por tanto, no tiene efecto real.</p>' : '';
        if($template == 'gc_mandate_conflict')
        {
            $subject = "Urgente: Conflicto en nuevo mandato de GoCardless. ".$sandbox_subject;
            $message = $this->CI->load->view('/emails/gateways/gc_mandate_conflict.tpl.php', $data, TRUE);
        }
        else if($template == 'gc_customer_conflict')
        {
            $subject = "Urgente: Conflicto en nuevo cliente de GoCardless. ".$sandbox_subject;
            $message = $this->CI->load->view('/emails/gateways/gc_customer_conflict.tpl.php', $data, TRUE);
        }
        else if($template == 'gc_subscription_conflict')
        {
            $subject = "Urgente: Conflicto en nuevo cliente de GoCardless. ".$sandbox_subject;
            $message = $this->CI->load->view('/emails/gateways/gc_subscription_conflict.tpl.php', $data, TRUE);
        }
        else if($template == 'gc_payment_failed' OR $template == 'gc_payment_failed_retry')
        {
            $subject = "Importante: No se ha podido realizar el cobro del plan ".$data['mem_name']." en ".$data['box_name'].". ".$sandbox_subject;
            $message = $this->CI->load->view('/emails/gateways/'.$template.'.tpl.php', $data, TRUE);
        }

        $this->CI->email->subject($subject);
        $this->CI->email->message($message);

        if($this->CI->email->send(false))
            return true;

        return false;
    }
}

?>
