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

class Cron extends CI_Controller {

    function index() 
    {
        if(!$this->input->is_cli_request())
        {
            log_message('debug',print_r('WARNING: intento ejecución del CRON desde cliente', TRUE));
            show_404();
             return;
        }

        $this->config->load('communications_system', TRUE);
        $email_config = $this->config->item('email_default','communications_system');

        $this->load->library('email', $email_config['settings']);
        $this->load->library('booking_lib');

        $this->load->model('box_model', 'box');
        $this->load->model('payment_model', 'pay');
        $this->load->model('cron_model', 'cron');
        $this->load->model('booking_model', 'booking');
        $this->load->model('logs_model', 'logs');

        log_message('debug',print_r('CRON: INICIO', TRUE));
        //automatic renovation on plans based on IBAN payments
        // pendiente: por el momento la comproibación de que el cobro se haya hecho efectivo se hace manualmente.

        // Gestion Tarifas
        $this->manageExpiringMemberships();
        $this->finishConsumedBonuses();

        //Gestión estadísticas
        $this->registerTotalClients();

        //Gestión Base de datos
        //not neccesary, it is currently done in server
        //$this->manageDatabaseBackups(); 

        log_message('debug',print_r('CRON: FIN', TRUE));
    }

    /**
     * Function: sendEmail
     *
     * @param  [type] $data [description]
     * @param  [type] $template [description]
     *
     * @return [type] [description]
     */
    function sendEmail($data, $template)
    {
        if($email_config[$template] === TRUE)
        {
            $this->email->set_newline("\r\n");
            $this->email->to($data->email);
            $this->email->from($email_config['settings']['smtp_user'], "FitBox");
            $this->email->bcc("kinsay.spam@gmail.com");

            if ($data->first_name != null)  
                $user = $data->first_name;
            else 
                $user = ($data->username != null) ? $data->username : $data->email;

            $data2 = array(
                        'box_name' => $data->box_name,
                        'title' => $data->title,
                        'user' => $user,
                        'days' => date("d-m-Y", strtotime($data->mem_expire)),
                        'cancel_date' => date("Y-m-d", strtotime($data->mem_expire." +".$data->cancel_period." day")),
                        'cancel_period' => $data->cancel_period,
                        'grace_period' => $data->grace_period
                );

        

            if($template == 'grace_expired_notification')
            {
                $subject = $data->box_name.": Recordatorio ".$data->title." ha caducado";
                $message = $this->load->view('/emails/cron/membership_expired2.tpl.php', $data2, TRUE);
            }
            else if($template == 'expired_notification')
            {
                $subject = $data->box_name.": Plan ".$data->title." ha caducado";
                $message = $this->load->view('/emails/cron/membership_expired.tpl.php', $data2, TRUE);
            }
            else if($template == 'expired_notification_no_grace')
            {
                $subject = $data->box_name.": Plan ".$data->title." ha caducado";
                $message = $this->load->view('/emails/cron/membership_expired_no_grace.tpl.php', $data2, TRUE);
            }
            else if($template == 'expiring_reminder')
            {
                if($data->recurring == 1 && $data->payment_method == 7) // DOMICILIACION BANCARIA
                {
                    $subject = $data->box_name.": Próximo cobro por domicialiación bancaria para renovación de ".$data->title.".";
                    $message = $this->load->view('/emails/cron/membership_renovation_notice.tpl.php', $data2, TRUE);             
                }
                else
                {
                    $subject = $data->box_name.": Plan ".$data->title." caducará próximamente.";
                    $message = $this->load->view('/emails/cron/membership_expiration_reminder.tpl.php', $data2, TRUE);
                }
            }
            else if($template == 'auto_canceled_notification')
            {
                $subject = $data->box_name.": Baja en el plan ".$data->title.".";
                $message = $this->load->view('/emails/cron/membership_auto_canceled.tpl.php', $data2, TRUE);
            }

        
            $this->email->subject($subject);
            $this->email->message($message);
            $result = $this->email->send();

        }
    }

    function renovate($mem)
    {
        $to = $this->booking_lib->calculateExpiration($mem->days, $mem->period, $mem->mem_expire);
        return $this->cron->renewMembership($mem, $to);
    }

    /**
     * Function: autoRenovate
     * auto renovate recurrent memberships based on IBAN (manual) payment method.
     *
     * @param  [type] $mem [description]
     *
     * @return [type] [description]
     */
    function autoRenovate($mem)
    {
        if($this->renovate($mem))
        {
            $rate_amount = $this->booking_lib->calculateAmount($mem, $mem->mem_expire, $to);
            $payment = array(
                'gateway' => $mem->payment_method,
                'box_id' => $mem->box_id,  
                'user_id' => $mem->user_id,
                'mu_id' => $mem->id,
                'staff_id' => '0',
                'amount' => $rate_amount,
                'txn_id' => 'FBX'.date("U").'-'.$mem->box_id.'-'.$mem->id,
                'demo' => 0,
                'retry' => 0,
                'refounded' => 0,
                'charge_date' => date("Y-m-d"),
                'status' => $this->booking->getSettingItem('membership', 'autorenovations_default_status', $mem->box_id)
              );

            $transaction = array(
                'type' => 'renew',
                'from_membership_id' => $mem->membership_id,
                'to_membership_id' => $mem->membership_id,
                'from' => $mem->mem_expire,
                'to' => $to,
                'coupon_id' => 0,                
                'notes' => 'automatic IBAN renew',
              );
            $this->pay->registerPayment($payment, $transaction);
        }

    }

    /**
     * Function: manageExpiringMemberships
     *
     * @return [type] [description]
     */
    function manageExpiringMemberships()
    {
        //get all memberships
        $memberships = $this->cron->getAllExpiringMemberships();

        $grace_period = array();
        $cancel_period = array();
        $payment_method = array();

        log_message('DEBUG','CRON_DEBUG: All Memberships-> '.print_r($memberships,TRUE));
        if($memberships)
        {
            $yesterday = date("Y-m-d", strtotime( '-1 day' ) );

            foreach ($memberships as $mem) 
            {
                if(empty($grace_period[$mem->box_id])) 
                    $grace_period[$mem->box_id] = $this->booking->getSettingItem('membership', 'grace_period', $mem->box_id);
                
                if(empty($cancel_period[$mem->box_id])) 
                    $cancel_period[$mem->box_id] = $this->booking->getSettingItem('membership', 'cancel_period', $mem->box_id);

                if(empty($payment_method[$mem->payment_method])) 
                {
                    $params = array('box_id' => $mem->box_id, 
                                    'id' => $mem->payment_method);

                    $payment_method[$mem->payment_method] = $this->pay->getGateway($params);
                }

                $mem->{'grace_period'} = $grace_period[$mem->box_id];
                $mem->{'cancel_period'} = $cancel_period[$mem->box_id];

                $last_grace_date = date("Y-m-d", strtotime($mem->mem_expire." +".$mem->grace_period." days"));
                $last_cancel_date = date("Y-m-d", strtotime($mem->mem_expire." +".$mem->cancel_period." days"));

                // periods info
                // <----valid(y)---|---grace(g)---|---outdated(n)---|---cancelled(c)---->
                
                // valid period
                if($yesterday <= $mem->mem_expire) 
                {
                    // nothing to do yet
                }
                // grace period
                else if($yesterday <= $last_grace_date AND $mem->status != 'g') 
                {
                    if($mem->recurring == '1')
                    {
                        // caso especial de IBAN manual
                        if($payment_method[$mem->payment_method]->name == 'iban')  
                        {
                            $this->autoRenovate($mem);
                        }
                        else
                        {
                            // resto de casos: comprobar si cobrado antes de renovar
                            $params = array('box_id' => $mem->box_id, 
                                            'mu_id' => $mem->id,
                                            'status' => array('confirmed', 'paid_out'));

                            $payment = $this->pay->getGatewayTransaction('payment', $params);
                            if(!empty($payments))
                            {
                                //pendiente -> comprobar que la cantidad recibida se ajusta al precio y coupons
                                $pay_date = date("Y-m-d", strtotime($payment->updated_on));
                                if($pay_date <= $last_grace_date)
                                    $this->renovate($mem);
                                else
                                {   
                                    //pendiente -> que hacer en este caso?
                                    log_message('DEBUG','Pago de renovacion recibido fuera de plazo');
                                    log_message('DEBUG',print_r($mem,true));
                                    log_message('DEBUG',print_r($payment,true));
                                }
                            }
                            else
                            {   
                                if($mem->grace_period > 0)
                                {
                                    $this->cron->updateMembership($mem, 'g');
                                    //$this->sendEmail($mem, 'expired_notification');
                                }
                                else if($mem->status != 'n') 
                                {
                                    $this->cron->updateMembership($mem, 'n');
                                    $this->sendEmail($mem, 'expired_notification_no_grace');
                                }                                
                            } 
                        }                      
                    }
                    else if($mem->status != 'n') 
                    {
                        $this->cron->updateMembership($mem, 'n');
                    }
                }
                // outdated period
                else if($yesterday <= $last_cancel_date)
                {
                    if($mem->status != 'n')
                    {   
                        $this->cron->updateMembership($mem, 'n');
                        if($mem->grace_period > 0)
                            $this->sendEmail($mem, 'grace_expired_notification');

                    }
                }
                //cancelled - de baja
                else 
                {
                    if($mem->status != 'c')
                    {
                        $this->cron->updateMembership($mem, 'c');
                        $this->sendEmail($mem, 'auto_canceled_notification'); 
                    }
                }
            }
        }
    }

    function registerTotalClients()
    {
        if(date("Y-m-d") == date("Y-m-t"))
        {
            $boxes = $this->box->getBoxes(array('status' => 1));
            foreach ($boxes as $bx) {

                $groups = array(11);
                $params = array('status'=>'y');
                $active = $this->box->getTotalClients($bx->id, $groups, $params);
                $this->logs->set_total_clients_log($bx->id, 'active', $active);

                $params = array('status'=>'g');
                $grace = $this->box->getTotalClients($bx->id, $groups, $params);
                $this->logs->set_total_clients_log($bx->id, 'grace', $grace);

                $params = array('status'=>'p');
                $pending = $this->box->getTotalClients($bx->id, $groups, $params);
                $no_plan = sizeof($this->box->getNoPlanClients($bx->id, $groups));
                $this->logs->set_total_clients_log($bx->id, 'pending', $pending + $no_plan);

                $groups = array(12);
                $guests = sizeof($this->box->get_users($bx->id,$groups));
                $this->logs->set_total_clients_log($bx->id, 'guests', $guests); 
            }
            
        }
    }

    function finishConsumedBonuses()
    {
        $now = date("Y-m-d H:i:s");

        $clients = $this->box->getActiveClientsIDs(11);

        if($clients !== FALSE AND sizeof($clients) > 0)
        {
            foreach ($clients as $client) 
            {
                $memberships = $this->box->getUserMemberships($client['user_id'], array('status' => array('y')));
                if($memberships !== FALSE AND sizeof($memberships) > 0)
                {
                    foreach ($memberships as $key => $mem) 
                    {
                        if(isset($mem['services_quota']) AND $mem['period'] == 'D')
                        {
                            $from = explode(" ", $mem['created_on']);
                            $bookings = $this->booking->getUserBookings($from[0], $mem['mem_expire'], $client['box_id'], $client['user_id']); 
                            
                            $memberships[$key]['bookings'] = $bookings[$key];

                            $bookings = $bookings[$key];
                            $quota = $mem['services_quota'];

                            foreach ($quota as $key2 => $value) 
                            {
                                $memberships[$key]['quota_left'][$key2] = $value - $bookings[$key2];
                            }
                            $memberships[$key]['quota_left']['total'] = $mem['max_reservations'] - $bookings['total'];
                            
                            if(($memberships[$key]['quota_left'][$serviceID] == 0 OR $memberships[$key]['quota_left']['total'] == 0))
                            {
                                $older = TRUE;
                                if($bookings !== FALSE AND sizeof($bookings['reservations']) > 0)
                                {
                                    foreach ($bookings['reservations'] as $bk){
                                        if($bk > $now) $older = FALSE;
                                    }
                                    if($older === TRUE) $this->booking->endBonusMembership($key);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function manageDatabaseBackups()
    {
        $this->load->helper('file');
        //create backup
        $this->autoBackup();
        //remove backups older than $days
        $this->removeOldBackups(15);
    }

    function autoBackup() 
    {
        $this->load->dbutil();
        $filename = "db-" . date("Y-m-d_H-i-s") . ".sql";
        $prefs = array(
            'ignore' => array(),
            'format' => 'txt',
            'filename' => $filename,
            'add_drop' => TRUE,
            'add_insert' => TRUE,
            'newline' => "\n"
        );
        $backup = $this->dbutil->backup($prefs);
        if(write_file('./backups/database/' . $filename, $backup))
        {
            log_message('DEBUG','CRON_DEBUG: Database backup done!');
        }
        else
        {
            log_message('ERROR','CRON_DEBUG: Database backup error!');
        }
        
    }

    function removeOldBackups($days = 31)
    {
        //pendiente
    }


}
