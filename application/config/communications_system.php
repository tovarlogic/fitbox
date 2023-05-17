<?php

defined('BASEPATH') OR exit('No direct script access allowed');

//EMAILS
//
$config['email_default']['settings'] = Array(
						             'protocol' => 'smtp',
						             'smtp_host' => 'ssl://mail.smtp2go.com',
						             'smtp_port' => 465,
						             'smtp_user' => 'info@fitbox.es',
						             'smtp_pass' => 'pipopipo', 
						             'mailtype' => 'html',
						             'charset' => 'utf-8',
						             'wordwrap' => TRUE
						          ); 

$config['email_default']['expiring_reminder'] = FALSE; 
$config['email_default']['expired_notification_no_grace'] = FALSE; 
$config['email_default']['expired_notification'] = FALSE; 
$config['email_default']['grace_expired_notification'] = FALSE; 
$config['email_default']['IBAN_attempt_failed'] = FALSE; 



?>