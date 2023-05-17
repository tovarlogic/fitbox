<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/// MODULES: genertal settings
$config['modules']['athlete']['production'] = array('booking','profile','settings','log_book');
$config['modules']['athlete']['development'] = array('booking','profile','settings','log_book', 'sports','nutrition','communications');

$config['modules']['staff']['production'] = array('users', 'booking','profile','settings','gateways');
$config['modules']['staff']['development'] = array('users', 'booking','profile','settings', 'sports','nutrition','communications', 'settings', 'gateways');

$config['modules']['inactive'] = array('blog','shop','finance','stats');

$config['module']['booking']['setting_tables'] = array('booking' => 'bs_settings', 'membership' => 'ms_settings', 'calendar' => 'bs_calendar_settings', 'service' => 'bs_service_settings');
$config['module']['sports']['setting_tables'] = array();
$config['module']['nutrition']['setting_tables'] = array();


/// UI HTML: default settings
$config['html_default']['show_header'] = FALSE;

$config['model']['box']['db_tables'] = array(	'bTable' => 'boxes',
												'sTable' => 'settings',
												'uTable' => 'auth_users',
												'ugTable' => 'auth_users_groups',
												'mTable' => 'ms_memberships',
												'muTable' => 'ms_memberships_users',
												'sTable' => 'ms_memberships_services',
												'ssTable' => 'ms_settings',
												'pTable' => 'ms_payments',
												'pdTable' => 'ms_payments_deleted',
												'gTable' => 'ms_gateways',
												'tTable' => 'ms_transactions',
												'iuTable' => 'ms_iban_users',
												'cTable ' => 'bs_coupons',
												);


?>
