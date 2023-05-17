<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
 
/** 
 * Gocardless Library for CodeIgniter 3.x 
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
 * @version     0.2 2020-03
 * @author kinsay <kinsay@gmail.com>
 *
 * @requires Gocardless-pro-php (placed in the third_party folder)
 * @requires Guzzle (placed in the third_party folder)
 * @requires php-oauth2 (placed in the third_party folder)
 * 
 * @requires gocardless config file (placed in the config directory)
 */ 

/**
 * Class: Gocardless_lib
 * Used to integrate the Gocardless-pro-php library in a CodeIgniter application.
 * 
 * Is it possible to use two different integrations ['partner' (OAuth2) or 'standard'] depending on the config file. Is it also possible to use it either with 'standard' or 'pro' plans. Enterprise has not been implemented.
 *
 * 
 */


// Include the GOCARDLESS PHP bindings library 
require_once(APPPATH .'third_party/guzzle/autoloader.php'); 
require_once(APPPATH .'third_party/gocardless-pro-php/lib/loader.php');

require_once(APPPATH .'third_party/php-oauth2/src/OAuth2/Client.php');  
require_once(APPPATH .'third_party/php-oauth2/src/OAuth2/GrantType/IGrantType.php'); 
require_once(APPPATH .'third_party/php-oauth2/src/OAuth2/GrantType/AuthorizationCode.php'); 

class Gocardless_lib
{ 
    private $settings = null;
    public $integration = null;
    public $enviroment = null;

    public $gc_config = null;
    private $client = null;
     
    public $oauth_config = null;
    private $oauth_client = null;

    public $api_error = null; 



    /**
     * Function: __construct
     * initializes the library by loading the config file and required external libraries
     */
    function __construct()
    { 
        $this->CI =& get_instance();
        $this->CI->config->load('gocardless', TRUE);
        $this->gc_config = $this->CI->config->item('gocardless');
        $this->integration = $this->gc_config['integration'];        

        if($this->integration == 'partner')
        {
            //include OAuth bindings
            
        }        
    } 

    /////////////////////
    //Section: General //
    /////////////////////

    /**
     * Function: is_Sandbox_request
     * Checks if a received request is from a sandbox enviroment or live instead
     * 
     * Parameters: 
     * $http_origin string - the http_origin of a request received 
     *
     * @return bool
     */
    function is_Sandbox_request($http_origin)
    {        
        foreach ($this->gc_config['sandbox']['http_origins'] as $url) {
            if($http_origin == $url)
                return true;
        }        
        return false;
    }
    
    /**
     * Function: setSettings
     */
    function setSettings($params)
    {
        $this->settings = $params;
        $this->enviroment = ($this->settings['demo'] == 0)? 'live' : 'sandbox';

        try { 
            $this->client = new \GoCardlessPro\Client([
                'access_token' => $this->settings['private_key'],
                'environment' => $this->enviroment
            ]);

            return true;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
        
    }

    private function getSettings()
    {
        return $this->settings;
    }
    

    //////////////////////////////
    //Section: HTTP_response // //
    //////////////////////////////

    
    /**
     * Function: _getType
     */
    function _getType($response, $type)
    {
        if(is_object($response) AND $response->api_response->status_code == 200)
        {          
            return $response->api_response->body->$type;
        }
        
        return FALSE;
    }

    /**
     * Function: _getBody
     */
    function _getBody($response)
    {
       if(is_object($response))
        {          
            return $response->api_response->body;
        }
        
        return FALSE;
    }

    /**
     * Function: _getStatus
     */
    function _getStatus($response)
    {
        if(is_object($response))
        {          
            return $response->api_response->status_code;
        }
        
        return FALSE;
    }

    //////////////////////////////////////////
    // section: Gocardless pro only
    // /////////////////////////////////////////
    
    
    /**
     * Function: createCustomer
     */
    function createCustomer($customer_data)
    {
        if($this->plan != 'standard')
        {
            try { 
            
                $result = $this->client->customers()->create([
                    "params" => $customer_data
                ]);

                return $result;

            }catch(Exception $e) { 
                $this->api_error = $e->getMessage(); 
                return false; 
            }
        }
        
        $this->api_error = 'Procedimiento restringido a planes de pago (Gocardless Pro o Enterprise).';
        
        return false;
    }

    /**
     * Function: createMandate
     */
    function createMandate($contract_id, $bank_account_id)
    {
        if($this->plan != 'standard')
        {
            try { 
            
                $result = $this->client->mandates()->create([
                    "params" => ["scheme" => "bacs",
                               "metadata" => ["contract" => $contract_id],
                               "links" => ["customer_bank_account" => $bank_account_id]]
                ]);

                return $result;

            }catch(Exception $e) { 
                $this->api_error = $e->getMessage(); 
                return false; 
            }
        }
        
        $this->api_error = 'Procedimiento restringido a planes de pago (Gocardless Pro o Enterprise).';
        
        return false;
    }

    ///////////////////////////////////////////
    /// section: Gocardless standard and pro
    /// ////////////////////////////////////////
 
    
    /**
     * Function: initiateFlow
     * used to create customer and mandate for GOCARDLESS STANDARD
     *
     */
    function initiateFlow($customer_data, $product_name, $token, $metadata = null)
    { 
        try { 
            $params = array( 
                "params" => array(
                    // This will be shown on the payment pages
                    "description" => $product_name,
                    // Not the access token
                    "session_token" => $token,
                    "success_redirect_url" => base_url()."athlete/membership/flow_completed/",
                    // Optionally, prefill customer details on the payment page
                    "prefilled_customer" => $customer_data
                )
            );
            if($metadata != null)
                $params['params']['metadata'] = $metadata;

            $result = $this->client->redirectFlows()->create($params);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }

    /**
     * Function: completeFlow
     *
     */
    function completeFlow($id, $token)
    {   
        try { 
            
            $redirectFlow = $this->client->redirectFlows()->complete(
                    $id, //The redirect flow ID from above.
                    ["params" => ["session_token" => $token]]
                );

            return $redirectFlow;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: getRedirectFlow
     *
     */
    function getRedirectFlow($id)
    {
        try { 
            
            $redirectFlow = $this->client->redirectFlows()->get($id);

            return $redirectFlow;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: getCustomer
     *
     */
    function getCustomer($id)
    {
        try { 
            
            $result = $this->client->customers()->get($id)->api_response->body->customers;

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: getCustomers
     *
     */
    function getCustomers()
    {
        try { 
            
            $clients = $this->client->customers()->list()->records;

        return $clients;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }  
    }

    function updateCustomer($id, $data)
    {
        try { 
            
            $result = $this->client->customers()->update($id, ["params" => $data]);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: getMandates
     *
     */
    function getMandates($params = null)
    {
        try { 
            
            if($params == null)
                $result = $this->client->mandates()->list();
            else
                $result = $this->client->mandates()->list(["params" => $params]);

            return $result->api_response->body->mandates;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: getMandate
     *
     */
    function getMandate($id)
    {
        try { 
            
            $result = $this->client->mandates()->get($id);
            $result = $this->_getType($result, 'mandates');

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: updateMandate
     *
     */
    function updateMandate($id, $params)
    {
        try { 
            
            $result = $this->client->mandates()->update($id, ["params" => $params]);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: cancelMandate
     *
     */
    function cancelMandate($id)
    {
        try { 
            
            $result = $this->client->mandates()->cancel($id);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: reinstateMandate
     *
     */
    function reinstateMandate($id)
    {
        try { 
            
            $result = $this->client->mandates()->reinstate($id);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: createSubscription
     *
     */
    function createSubscription($params)
    {
        try { 
            if(empty($params['retry_if_possible'])) $params['retry_if_possible'] = true;

            $result = $this->client->subscriptions()->create(["params" => $params]);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }

    /**
     * Function: pauseSubscription
     *
     */
    function pauseSubscription()
    {
        try { 
            
            $result = $this->client->subscriptions()->pause($id);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }

    /**
     * Function: cancelSubscription
     *
     */
    function cancelSubscription($id)
    {
        try { 
            
            $result = $this->client->subscriptions()->cancel($id);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }

    /**
     * Function: updateSubscription
     *
     */
    function updateSubscription($id, $params)
    {
        try { 
            $result = $this->client->subscriptions()->update($id, ["params" => $params]);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }

    /**
     * Function: getSubscription
     *
     */
    function getSubscription($id)
    {
        try { 
            
            $result = $this->client->subscriptions()->get($id)->api_response->body->subscriptions;

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }

    /**
     * Function: getSubscriptions
     *
     */
    function getSubscriptions($params = null)
    {
        try { 
            if($params == null)
                $result = $this->client->subscriptions()->list();
            else
                $result = $this->client->subscriptions()->list(["params" => $params]);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }

    
    /**
     * Function: getCreditors
     * returns the creditors list acording to the params included in the request
     * in partner integration will always be only one creditor
     */
    function getCreditors($params = null)
    {
        try { 
            if($params == null)
                $result = $this->client->creditors()->list();
            else
                $result = $this->client->creditors()->list(["params" => $params]);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: getPayment
     *
     * @param  [type] $id [description]
     *
     * @return [type] [description]
     */
    function getPayment($id)
    {
        try { 
            
            $result = $this->client->payments()->get($id)->api_response->body->payments;

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }

    /**
     * Function: getPayments
     *
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function getPayments($params = null)
    {
        try { 
            if($params == null)
                $result = $this->client->payments()->list();
            else
                $result = $this->client->payments()->list(["params" => $params]);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    function createPayment($params)
    {
        try { 
            if(empty($params['retry_if_possible'])) $params['retry_if_possible'] = true;
            
            $result = $this->client->payments()->create(["params" => $params]);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }
    /**
     * Function: createRefund
     * This function is dasabled by default, may be enabled if requested to GoCardless
     * 
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function createRefund($params)
    {
        try { 
            
            $result = $this->client->refunds()->create(["params" => $params]);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }

    /**
     * Function: getRefunds
     *
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function getRefunds($params = null)
    {
        try { 
            if($params == null)
                $result = $this->client->refunds()->list();
            else
                $result = $this->client->refunds()->list(["params" => $params]);

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    /**
     * Function: getRefund
     *
     * @param  [type] $id [description]
     *
     * @return [type] [description]
     */
    function getRefund($id)
    {
        try { 
            
            $result = $this->client->refunds()->get($id)->api_response->body->refunds;

            return $result;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }

    /**
     * Function: createMandatePDF
     *
     * @param  [type] $params [description]
     *
     * @return [type] [description]
     */
    function createMandatePDF($params)
    {
        try { 
            
            $url = $this->client->mandatePdfs()->create(["params" => $params]);

            return $url;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }
    /////////////////////////////////////////////////////
    //Section: OAuth specific (partner integration) // //
    /////////////////////////////////////////////////////
       /**
     * Function: getConfigItem
     * Returns a config item. This item is dependent of current enviroment of the gateway (if it is demo or not). 
     */
    function getOauthConfig()
    {
        return $this->gc_config[$this->enviroment]['oauth'];
    }

    /**
     * Function: oauth_setup
     * creates the OAuth2 client object with public and private keys
     * 
     * @return bool
     */
    function oauth_setup()
    {
        $this->oauth_config = $this->getOauthConfig();

        try { 

            $this->oauth_client = new OAuth2\Client(
                $this->oauth_config['client_id'], //pendiente -> fitbox id -> to be stored in database or .env instead of config file.
                $this->oauth_config['client_private_key']); //pendiente ->  fitbox pk -> to be stored in databsae
            return true;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); //pendiente ->  revisar formato errores de la API
            return false; 
        }
    }

    /**
     * Function: oauth_flow
     * starts the proccess to connect a new client (organization)
     *
     * Parameters:
     * $customer_data array - client basic data to prefill the form
     * $scope string - accepts 'read_write' or 'read_only'
     * $view string - accepts 'login' or 'signup'
     *
     * @return [type] [description]
     */
    function oauth_flow($customer_data, $scope = 'read_write', $view = 'signup')
    {
        try { 

            $authorizeUrl = $this->oauth_client->getAuthenticationUrl(
                $this->oauth_config['authorize_url'],
                $this->oauth_config['redirect_url'],
                ['scope' => $scope, 'initial_view' => $view, 'prefill' => $customer_data]
            );

            return $authorizeUrl;
            
        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); //pte revisar formato errores de la API
            return false; 
        }        
    }

    /**
     * Function: oauth_callback
     * completes the flow in the proccess to connect a new client
     *
     * Returns:
     * assoc_array - containing organisation_id and access_token
     */
    function oauth_callback()
    {
        try { 

            $response = $this->oauth_client->getAccessToken(
                $this->oauth_config['get-token_url'],
                'authorization_code',
                ['code' => $_GET['code'], 'redirect_uri' => $this->oauth_config['redirect_url']]
            );

            if($response['code'] == 200)
            {
                return array('organisation_id' => $response['result']['organisation_id'], 
                             'access_token' => $response['result']['access_token']);
            }
            else
            {
                $this->api_error = $response['error'];
                return false;
            }
            
        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); //pte revisar formato errores de la API
            return false; 
        } 
    }


}

?>
