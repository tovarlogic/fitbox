<?php 
$html_default = $this->config->item('html_default', 'settings');
if( $html_default['show_header'] === TRUE): 
?>
<div class="small-header">
    <div class="hpanel">
        <div class="panel-body">
            <div id="hbreadcrumb" class="pull-right">
                <ol class="hbreadcrumb breadcrumb">
                    <li><a href='<?php echo base_url(); ?>athlete' class='html5history'>Inicio</a></li>
                    <li class="active">
                        <span>Mi cuenta </span>
                    </li>
                </ol>
            </div>
            <h2 class="font-light m-b-xs">
                MI CUENTA
            </h2>
            <small>Dando el mejor servicio posible</small>
        </div>
    </div>
</div>
<?php endif ?>
<?php $this->load->view('backend/messages'); ?>
<div class="row">
    <div class="col-lg-4">
        <div class="hpanel hgreen">
            <div class="panel-heading">
                  <div class="panel-tools">
                      <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                      <a class="closebox"><i class="fa fa-times"></i></a>
                  </div>
                  Datos personales
            </div>
            <div class="panel-body">
              <div class="pull-right text-right">
                    <div class="btn-group">
                        <a href="<?php echo base_url(); ?>athlete/profile/edit" type="button" class="btn btn-xs btn-outline btn-warning2 html5history" >Editar datos</a>
                    </div>
                </div>
                <?php $image = 'profile.jpg';
                if($user->gender == 'M') 
                    $image = 'male2.png'; 
                else 
                    $image = 'female2.png';  
                ?>
                <img alt="face" class="img-circle m-b m-t-md" src="<?php echo base_url().'assets/images/'.$image; ?>">
                               
                <h3><?php echo ucfirst($user->first_name)." ".ucfirst($user->last_name).' ('.$user->username.')'; ?></h3>
                <div class="text-muted font-bold m-b-xs"><?php echo $user->email;?></div>
                <!-- <div class="text-muted font-bold m-b-xs">Las Palmas de GC, Las palmas</div> -->
                <div class="pull-left text-left">
                    <!-- <div class="btn-group">
                        <i class="fa fa-envelope-o btn btn-default btn-xs"></i>
                        <i class="fa fa-phone btn btn-default btn-xs"></i>
                        <i class="fa fa-facebook btn btn-default btn-xs"></i>
                        <i class="fa fa-twitter btn btn-default btn-xs"></i>
                        <i class="fa fa-instagram btn btn-default btn-xs"></i>
                        <i class="fa fa-youtube btn btn-default btn-xs"></i>
                    </div> -->
                </div>
                <p><br></p>
                <div class="row">
                    <div class="col-xs-3" style="">
                        <div class="project-label"><b>EDAD</b></div>
                        <small><?php echo ($user->age != 0)? $user->age : 'N/A'; ?></small>
                    </div>
                    <div class="col-xs-3" style="">
                        <div class="project-label"><b>PESO</b></div>
                        <small><?php echo ($user->weight != null)? $user->weight.' kg' : 'N/A'; ?></small>
                    </div>
                    <div class="col-xs-3" style="">
                        <div class="project-label"><b>ALTURA</b></div>
                        <small><?php echo ($user->height != null)? $user->height.' cm' : 'N/A'; ?></small>
                    </div>
                    <div class="col-xs-3" style="">
                        <div class="project-label"><b>Sexo</b></div>
                         <small><?php echo ($user->gender == 'M')? 'Hombre' : 'Mujer'; ?></small>
                    </div>
                </div>
            </div>
            <!-- <div class="panel-footer contact-footer">
                <div class="row">
                    <div class="col-xs-6 col-md-3 border-right">
                        <div class="contact-stat"><span>Fitness index</span> <strong></strong></div>
                    </div>
                    <div class="col-xs-6 col-md-3">
                        <div class="contact-stat"><span>FitBox Ranking #</span> <strong></strong></div>
                    </div>
                    <div class="col-xs-6  col-md-3 border-right">
                        <div class="contact-stat"><span>Box Ranking #</span> <strong></strong></div>
                    </div>
                    <div class="col-xs-6 col-md-3 border-right">
                        <div class="contact-stat"><span>Wods Realizados</span> <strong></strong></div>
                    </div>
                </div>
            </div> -->

        </div>
    </div>

    <div class="col-lg-8">
      <div class="hpanel hblue">
              <div class="panel-heading">
                  <div class="panel-tools">
                      <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                      <a class="closebox"><i class="fa fa-times"></i></a>
                  </div>
                  Histórico planes contratados
              </div>
              <div class="panel-body"> 
                  <div class="row">
                    <div class="col-sm-12">
                      <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Search in table">
                      <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="12" data-filter=#filter>
                        <thead>
                          <tr role="row">
                            <th data-toggle="true">Box</th>
                            <th data-toggle="true">Título</th>
                            <th data-hide="phone">Precio</th>
                            <th data-hide="phone,tablet">Periodicidad</th>
                            <th data-hide="all">Fecha Alta</th>
                            <th data-hide="phone">Fecha caducidad</th>
                            <th data-hide="all">Fecha baja</th>
                            <th >Estado</th>
                            <th data-hide="phone">Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $rows = 0;
                          if(is_array($user_memberships) && sizeof($user_memberships) > 0):
                            foreach($user_memberships as $membership): 
                              $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                              <tr class=<?php echo $class; ?> role="row">
                                  <td ><?php echo $membership->name;?></td>
                                  <td ><?php echo $membership->title;?></td>
                                  <td ><?php echo $membership->price." €";?></td>
                                  <td ><?php  
                                      if($membership->period == 'M'){ 
                                        if($membership->days == 1) echo 'mensual';
                                        else if($membership->days == 2) echo 'bimensual';
                                        else if($membership->days == 3) echo 'trimestral';
                                        else if($membership->days == 4) echo 'cuatrimestral';
                                        else if($membership->days == 6) echo 'semestral';
                                        else echo $membership->days." ".$this->lang->line('date_months'); 
                                      }
                                      elseif($membership->period == 'D'){ 
                                        if($membership->days == 1) echo 'diaria';
                                        else echo $membership->days." ".$this->lang->line('date_days');
                                      }
                                      elseif($membership->period == 'Y'){ 
                                        if($membership->days == 1) echo "anual";
                                        else  echo $membership->days." ".$this->lang->line('date_years');
                                      }
                                      ?>
                                  </td>
                                  
                                  <td> <?php echo $membership->created_on; ?></td>
                                  <td> <?php echo date("d-m-Y", strtotime($membership->mem_expire)); ?></td>
                                  <td> <?php 
                                    if ($membership->status == 'c') echo $membership->created_on; 
                                    else echo "-"; 
                                    ?>
                                  </td>

                                  <td><?php 
                                    if ($membership->status == 'y') 
                                      echo "<i class='fa fa-check text-success'> ";
                                    else if ($membership->status == 'p') 
                                      echo "<i class='fa fa-question text-primary-2'> ";
                                    else if ($membership->status == 'n')
                                      echo "<i class='fa fa-ban text-warning'> ";
                                    else if ($membership->status == 'c')
                                      echo "<i class='fa fa-ban text-danger'> ";
                                    else if ($membership->status == 'g')
                                      echo "<i class='fa fa-calendar text-primary-2'> ";
                                    else if ($membership->status == 'e')
                                      echo "<i class='fa fa-minus text-danger'> ";

                                    echo $this->lang->line('mem_status_'.$membership->status)."</i>";
                                    ?>
                                  </td>
                                  <td>
                                      <!-- si subscrito -->
                                      <?php 
                                      if (!empty($membership->subscription))
                                      {

                                        echo "<a href='".base_url().">athlete/membership/cancel_subscription/".$membership->subscription['subscription_id']."' type='button' class='btn btn-danger btn-xs html5history'>
                                          <i class='fa fa-bank'></i> ".$this->lang->line('cancel_iban')."</a>";
                                      }
                                      else
                                      {
                                      //si no subscrito
                                        //Si es un bono pendiente -> pagar -->
                                        if ($membership->period == 'D' AND $membership->status == 'p' )
                                        {
                                          echo "<a href='".base_url()."athlete/membershipPayment/add/".$membership->id."' type='button' class='btn btn-success btn-xs html5history'>
                                            <i class='fa fa-euro'></i> ".$this->lang->line('pay')."</a>";
                                        }
                                        else
                                        {
                                        //si no es un bono y con metodos de pago disponibles-->
                                          if ($membership->period != 'D' AND !empty($membership->gateways))
                                          {
                                            foreach ($membership->gateways as $gtw)
                                            {
                                              $url = base_url().'athlete/';
                                              if($gateways[$gtw]->methods == 'card')
                                              {
                                                //Si aun pendiente de pago inicial
                                                if($membership->status == 'p')
                                                {
                                                    $text = $this->lang->line('pay');
                                                    $url .= 'membership/initial/'.$membership->id.'/'.$gateways[$gtw]->id;
                                                }
                                                else
                                                {
                                                    $text = $this->lang->line('renew');
                                                    $url .= 'membership/renew/'.$membership->id.'/'.$gateways[$gtw]->id; 
                                                }
                                                $color = 'success';
                                                $icon = 'credit-card';

                                                echo '<a href="'.$url.'" type="button" class="btn btn-'.$color.' btn-xs html5history"><i class="fa fa-'.$icon.'"></i> '.$text.'</a>';
                                              }
                                              else if($gateways[$gtw]->methods == 'iban')
                                              {
                                                if($membership->status == 'y')
                                                {
                                                    $text = $this->lang->line('manage_iban');
                                                  $url .= 'membership/subscribe/'.$membership->id.'/'.$gateways[$gtw]->id;
                                                  $color = 'info';
                                                  $icon = 'bank';

                                                  if((isset($gateways[$gtw]->status) AND $gateways[$gtw]->status == 'successful' ))
                                                    echo '<a href="'.$url.'" type="button" class="btn btn-'.$color.' btn-xs html5history"><i class="fa fa-'.$icon.'"></i> '.$text.'</a>';
                                                }
                                              }
                                            }
                                          }
                                        }
                                      }
                                       ?>
                                  </td>
                              </tr>
                          <?php endforeach ?>
                          <?php endif?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="12">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                
              </div>
            </div>
    </div>
</div>

<div class="row">
  <div class="col-lg-4">
  </div>
  <div class="col-lg-8">
      <div class="hpanel hred">
              <div class="panel-heading">
                  <div class="panel-tools">
                      <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                      <a class="closebox"><i class="fa fa-times"></i></a>
                  </div>
                  Registro de pagos
              </div>
              <div class="panel-body"> 
                  <div class="row">
                    <div class="col-sm-12">
                      <input type="text" class="form-control input-sm m-b-md" id="filter2" placeholder="Search in table">
                      <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="12" data-filter=#filter2>
                        <thead>
                          <tr role="row">
                            <th data-toggle="true">Box</th>
                            <th data-hide="phone">Tipo</th>
                            <th data-toggle="true">Concepto</th>
                            <th data-hide="phone">Total</th>
                            <th data-hide="phone">Método</th>
                            <th data-hide="phone">fecha</th>
                            <th data-toggle="true">Estado</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $rows = 0;
                          if(is_array($transactions) && sizeof($transactions) > 0):
                            foreach($transactions as $trans): 
                              $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                              <tr class=<?php echo $class; ?> role="row">
                                  <td ><?php echo $trans->box_name;?></td>
                                  <td >
                                      <?php 
                                      switch ($trans->type) {
                                        case 'change':
                                          echo $this->lang->line('plan_change'); 
                                          break;
                                        
                                        case 'renew':
                                          echo $this->lang->line('renewal'); 
                                          break;

                                        case 'demo renew':
                                          echo $this->lang->line('demo_renew'); 
                                          break;

                                        case 'new':
                                          echo $this->lang->line('first_payment'); 
                                          break;

                                        case 'mandate':
                                          echo $this->lang->line('mandate_payment'); 
                                          break;

                                        case 'demo mandate':
                                          echo $this->lang->line('demo_mandate_payment'); 
                                          break;

                                        default:
                                          echo "";
                                          break;
                                      }
                                      ?>
                                  </td>
                                  <td >
                                    <?php 
                                    if($trans->type == 'mandate' || $trans->type == 'demo mandate')
                                    {
                                      $from = $this->lang->line('recurrent_payment');
                                      $to = $this->lang->line('undefined');
                                    } 
                                    else 
                                    {
                                      $to = date("d-m-Y", strtotime($trans->to));
                                      $from = date("d-m-Y", strtotime($trans->from))." a";
                                    }
                                    echo ($trans->notes == 'plan change')? "Cambio de plan a " : ""; 
                                    echo $trans->membership." ".$from." ".$to; ?></td>
                                  <td ><?php echo $trans->amount/100; echo " ".$trans->currency;?></td>
                                  <td ><?php
                                    switch ($trans->pp) {
                                      case 'gocardless':
                                        echo $this->lang->line('gocardless_gateway_name'); 
                                        break;

                                      case 'stripe':
                                        echo $this->lang->line('stripe_gateway_name'); 
                                        break;

                                      case 'iban':
                                        echo $this->lang->line('iban_gateway_name'); 
                                        break;

                                      case 'card':
                                        echo $this->lang->line('card_gateway_name'); 
                                        break;

                                      case 'cash':
                                        echo $this->lang->line('cash_gateway_name'); 
                                        break;
                                      
                                    }
     
                                  ?></td>
                                  <td ><?php echo date("d-m-Y H:i", strtotime($trans->created_on));?></td>
                                  <td > <?php
                                    switch ($trans->status) {
                                      case 'succeeded':
                                      case 'confirmed':
                                        echo "<i class='fa text-success fa-check'> ";
                                        break;

                                      case 'pending':
                                        echo "<i class='fa fa-spinner text-info'> "; 
                                        break;

                                      case 'refunded':
                                        echo "<i class='fa fa-reply text-primary-2'> ";
                                        break;

                                      case 'canceled':
                                        echo "<i class='fa text-warning fa-minus'> "; 
                                        break;

                                      case 'failed':
                                        echo "<i class='fa text-danger fa-times'> ";
                                        break;
                                      
                                    }   
                                    $demo = ($trans->demo == true)? '(demo)': '';                                 
                                    echo $this->lang->line('payment_'.$trans->status).' '.$demo."</i>";

                                    ?>
                                  </td>
                              </tr>
                          <?php endforeach ?>
                          <?php endif?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="12">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                
              </div>
            </div>
    </div>

</div>

<script>

  jQuery.support.cors = true;
  var table = jQuery('.footable').footable();

 $('.nav-tabs').on( 'shown.bs.tab', function () {
    $('.footable').trigger('footable_resize');
});

</script>
