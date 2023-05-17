<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
 
/** 
 * Stripe Library for CodeIgniter 3.x 
 * 
 * Library for Stripe payment gateway. It helps to integrate Stripe payment gateway 
 * in CodeIgniter application. 
 * 
 * This library requires the Stripe PHP bindings and it should be placed in the third_party folder. 
 * It also requires Stripe API configuration file and it should be placed in the config directory. 
 * 
 * @package     CodeIgniter 
 * @category    Libraries 
 * @version     3.0 
 */ 
 
class Stripe_lib
{ 
    var $api_error; 
    
    var $settings;
     
    function __construct()
    { 
        $this->api_error = ''; 
        
        $this->settings = array(
            'demo' => 1,
            'public_key' => '',
            'private_key' => '',
            'currency' => 'EUR',
            'webhook_secret' => ''
        );

        // Include the Stripe PHP bindings library 
        require APPPATH .'third_party/stripe-php/init.php'; 
          
    } 
 
    function setSettings($params)
    {
        $this->settings = $params;

        // Set API key 
        \Stripe\Stripe::setApiKey($this->settings['private_key']);
    }

    function createIntent($amount, $description, $capture = 'automatic')
    {
        try { 
            $intent = \Stripe\PaymentIntent::create([
                'amount' => $amount*100,
                'currency' => $this->settings['currency'],
                'description' => $description,
                'capture_method' => $capture,
            ]); 

            return $intent; 
        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    function retreiveIntent($id)
    {
        try { 
            $intent = \Stripe\PaymentIntent::retrieve($id); 
            return $intent; 
        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    //pendiente de testar si funciona... tengo dudas sobre si acepta $params tal cual o he de pasarlo a string
    ////params format
        //array('amount' => value, 'metadata' => array('order_id' => value) ....)
        //has to be --> ['amount' => ]
        //
    //Depending on which properties you update, you may need to confirm the PaymentIntent again. 
    //For example, updating the payment_method will always require you to confirm the PaymentIntent again. 
    //If you prefer to update and confirm at the same time, we recommend updating properties via the CONFIRM instead.
    function updateIntent($id, $params)
    {   
        $intent = $this->retreiveIntent($id);
        try { 
            $intent->update($id, $params); 
            return $intent;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
        
    }

    //Authorized payments can only be captured once. If you partially capture a payment, you cannot perform another capture for the difference.
    function caprureIntent($id, $amount = null)
    {
        $intent = $this->retreiveIntent($id);
        try { 
            if($amount == null)
                $intent->capture(); 
            else
                $intent->capture(['amount_to_capture' => $amount]); 
            return $intent;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
    }

    function canBeCancelled($id)
    {
        $intent = $this->retreiveIntent($id);
        if($intent['status'] == 'requires_payment_method' OR $intent['status'] == 'requires_capture' OR $intent['status'] == 'requires_confirmation' OR $intent['status'] == 'requires_action')
                return TRUE;
            else
                return FALSE;
    }

    function cancelIntent($id, $reason)
    {
        $intent = $this->retreiveIntent($id);
        try { 
            //posible reasons: duplicate, fraudulent, requested_by_customer, or abandoned
            if(!empty($reason))
                $intent->cancel($reason);
            else
                $intent->cancel();
            return $intent;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }
        
    }

    
    function listIntents($params)
    {   
    //possible parameters:
        // ['customer' => 3] is a customer id
        // ['limit' => 3] is a limit on the number of objects to be returned. default = 10
        // ['ending_before' => id] 
        // ['starting_after' => id]
        //
        try {  
            if(empty($params))
                $intents_list = \Stripe\PaymentIntent::all();
            else
                $intents_list = \Stripe\PaymentIntent::all($params);
            return $intents_list;

        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        }   
    }

    function createWebhookEvent($input, $signature)
    {

        $event = null;

        try {
            // Make sure the event is coming from Stripe by checking the signature header
            $event = \Stripe\Webhook::constructEvent($input, $signature, $this->settings['webhook_secret']);
            return $event;
        }
        catch (Exception $e) {
            $this->api_error = $e->getMessage(); 
            return false;
        }
    }

    /////////////////////////////////
    /// DEPRECATED: no SCA support
    /// ////////////////////////////
    
    public function process_payment($order_string = null, $item = null, $description = null)
    {
        //check whether stripe token is not empty
        if(!empty($_POST['stripeToken']))
        {
            //get token, card and user info from the form
            $token  = $_POST['stripeToken'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            
            //add customer to stripe
            $customer = $this->addCustomer($email, $token);
            if($customer === FALSE)
            {
                return array(FALSE, null, $this->api_error);
            }
            //item information
            $itemName = $description;
            $itemNumber = $item;
            $itemPrice = $_POST['rate_amount']; 
            $orderID = date('Y').date('m').date('d').'_'.$order_string;
            
            //charge a credit or a debit card AND retrieve charge details
            $chargeJson = $this->createCharge($customer->id, $itemName, $itemPrice, $orderID);

            //check whether the charge is successful
            if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1)
            {
                return array(TRUE, $chargeJson, null);
            }
            else
            {
                 return array(FALSE, $chargeJson, $this->api_error);
            }
        }
    } 
     
    function createCharge($customerId, $itemName, $itemPrice, $orderID)
    { 
        // Convert price to cents 
        $itemPriceCents = ($itemPrice*100); 
        $currency = $this->settings['currency'];
         
        try { 
            // Charge a credit or a debit card 
            $charge = \Stripe\Charge::create(array( 
                'customer' => $customerId, 
                'amount'   => $itemPriceCents, 
                'currency' => $currency, 
                'description' => $itemName, 
                'metadata' => array( 
                    'order_id' => $orderID 
                ) 
            )); 
             
            // Retrieve charge details 
            $chargeJson = $charge->jsonSerialize(); 
            return $chargeJson; 
        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    } 

    function addCustomer($email, $token)
    { 
        try { 
            // Add customer to stripe 
            $customer = \Stripe\Customer::create(array( 
                'email' => $email, 
                'source'  => $token 
            )); 
            return $customer; 
        }catch(Exception $e) { 
            $this->api_error = $e->getMessage(); 
            return false; 
        } 
    }

    


}

?>
