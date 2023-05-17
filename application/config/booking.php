<?php

defined('BASEPATH') OR exit('No direct script access allowed');

//BOOKING
$config['booking_default']['grace_period'] = 7; //days that user will be able to continue booking while membership is expired pending/waiting for renovation (payment).
$config['booking_default']['cancel_period'] = 45; //days after membership last expiring date w/o any renovation. If reached membership will automatically be cancelled.

// MEMBERSHIP
$config['membership_default']['grace_period'] = 7; //days that user will be able to continue booking while membership is expired pending/waiting for renovation (payment).
$config['membership_default']['cancel_period'] = 45; //days after membership last expiring date w/o any renovation. If reached membership will automatically be cancelled.
$config['membership_default']['autorenovations_default_status'] = 1; //every payment registered as IBAN autorenovation will be automatically marked as received = 1 or not received = 0.

//CALENDAR
$config['calendar_default']['weekly'] = TRUE; //vista mensual (FALSE) o semanal (TRUE)
$config['calendar_default']['only_this_week'] = TRUE; //solo aplicable en vista mensual
$config['calendar_default']['past_events'] = TRUE; // mostrar los servicios y eventos que ya han pasado
$config['calendar_default']['mark_past'] = TRUE;
$config['calendar_default']['free_spots'] = FALSE; // mostrar espacios libres (TRUE) u ocupados (FALSE)
$config['calendar_default']['max_spots'] = TRUE;
$config['calendar_default']['allow_public'] = TRUE; //permitir que el calendario sea visto por alguin no logueado
$config['calendar_default']['use_popup'] = FALSE;
$config['calendar_default']['start_day'] = '1'; // 1 para Lunes y 7 para Domingo

//PAYMENTS
$config['payment']['methods'] = array('card','cash','iban');
$config['payment']['types'] = array('online','offline');

$config['payment']['available_gateways'] = array(
											array('name' => 'card', 		'demo' => 0, 'type' => 'offline', 'is_recurring' => 0, 'active' => 1, 'default' => 1, 'methods' => array('card')),
											array('name' => 'cash', 		'demo' => 0, 'type' => 'offline', 'is_recurring' => 0, 'active' => 1, 'default' => 1, 'methods' => array('cash')),
											array('name' => 'iban', 		'demo' => 0, 'type' => 'offline', 'is_recurring' => 1, 'active' => 0, 'default' => 1, 'methods' => array('iban')),
											array('name' => 'paypal', 		'demo' => 0, 'type' => 'online', 'is_recurring' => 0, 'active' => 0, 'default' => 0, 'methods' => array('card'), 'public_key' => '', 'private_key' => '', 'info' => 'https://www.paypal.com'),
											array('name' => 'paypal_demo', 	'demo' => 1, 'type' => 'online', 'is_recurring' => 0, 'active' => 0, 'default' => 0, 'methods' => array('card'), 'public_key' => '', 'private_key' => '', 'info' => 'https://www.paypal.com'),
											array('name' => 'stripe', 		'demo' => 0, 'type' => 'online', 'is_recurring' => 0, 'active' => 0, 'default' => 0, 'methods' => array('card'), 'public_key' => '', 'private_key' => '', 'info' => 'https://www.stripe.com'),
											array('name' => 'stripe_demo', 	'demo' => 1, 'type' => 'online', 'is_recurring' => 0, 'active' => 0, 'default' => 0, 'methods' => array('card'), 'public_key' => '', 'private_key' => '', 'info' => 'https://www.stripe.com')
										);

$config['payment_default']['currency'] = 'EUR';

										

?>
