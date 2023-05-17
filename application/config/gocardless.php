<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 

// THESE ARE DEFAULT KEYS => ONLY USE DEMO KEYS
$config['api_key']         = 'sk_test_'; 
$config['publishable_key'] = 'pk_test_'; 
$config['webhook_key'] 	   = 'whsec_';
$config['currency']        = 'eur';	

// THERE ARE VARIABLES OF THE TYPE OF INTEGRATION
$config['integration'] = 'partner';	// possible values are 'standard' or 'partner'
					

//SANDBOX VARIABLES
$config['sandbox']['http_origins'][] = 'https://api-sandbox.gocardless.com';
$config['sandbox']['http_origins'][] = 'https://connect-sandbox.gocardless.com';

$config['sandbox']['oauth']['client_id'] = '';
$config['sandbox']['oauth']['client_private_key'] = '';

$config['sandbox']['oauth']['verify_url'] = 'https://verify-sandbox.gocardless.com';
$config['sandbox']['oauth']['authorize_url'] = 'https://connect-sandbox.gocardless.com/oauth/authorize';
$config['sandbox']['oauth']['get-token_url'] = 'https://connect-sandbox.gocardless.com/oauth/access_token';

$config['sandbox']['oauth']['redirect_url'] = base_url().'oauth/gocardless/callback';
$config['sandbox']['oauth']['post-onboard_url'] = base_url().'oauth/gocardless/onboard_complete/demo';

//LIVE VARIABLES
$config['live']['oauth']['client_id'] = '';
$config['live']['oauth']['client_private_key'] = '';

$config['live']['oauth']['verify_url'] = 'https://verify.gocardless.com';
$config['live']['oauth']['authorize_url'] = 'https://connect.gocardless.com/oauth/authorize';
$config['live']['oauth']['get-token_url'] = 'https://connect.gocardless.com/oauth/access_token';

$config['live']['oauth']['redirect_url'] = base_url().'oauth/gocardless/callback';
$config['live']['oauth']['post-onboard_url'] = base_url().'oauth/gocardless/onboard_complete';
?>
