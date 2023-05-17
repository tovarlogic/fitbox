<?php 
$html_default = $this->config->item('html_default', 'settings');
if( $html_default['show_header'] === TRUE): 
?>
      <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='<?php echo base_url(); ?>staff' class='html5history'>Inicio</a></li>
                          <li class="active">
                              <span>Planes y Tarifas </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE PLANES Y TARIFAS
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>
<?php endif ?>

<!-- Shortcuts -->
<?php $this->load->view('backend/staff/partials/shortcuts'); ?>

      <div class="row">
        <div style="" class="col-lg-12">
            <div class="hpanel">
              <div class="panel-heading">
                  Histórico de transacciones
              </div>
              <div class="panel-body">
                  <div class="row">
                    <div class="col-sm-12">
                      <?php form_open("staff/transactions/list/"); ?>
                      <form action="<?php echo base_url(); ?>staff/transactions/list" id="html5form" method="post" accept-charset="utf-8">
                      <div class="form-group col-md-2"><label>Año</label><?php echo form_dropdown('years', $years_list, ($month)? $year : date('Y'), 'class="form-control" id="year"');?>
                      </div>
                      <div class="form-group col-md-2"><label>Mes</label><?php echo form_dropdown('months', $months_list, ($month)? $month : date('m'), 'class="form-control" id="month"');?>
                      </div>
                      <div class="form-group col-md-3"><label>Cliente</label><?php echo form_dropdown('users', $users_list, ($user)? $user : 'all', 'class="form-control select2" id="user"');?>
                      </div>
                       <div class="form-group col-md-3">
                          <?php echo form_submit('submit', 'Buscar', 'class="btn btn-xs btn-primary m-t-n-xs"');?>
                      </div>
                      <?php echo form_close();?>
                      </div>
                    </div>
                    
                    <?php $this->load->view('backend/messages'); ?>

                  <div class="row">
                    <div class="col-sm-12">
                      <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="20" data-filter=#filter>
                        <thead>
                          <tr role="row">
                            <th data-toggle="true">Cliente</th>
                            <th>Tipo</th>
                            <th>Concepto</th>
                            <th data-hide="all">Precio</th>
                            <th data-hide="all">Cupón</th>
                            <th data-hide="all">Descuento</th>
                            <th data-hide="all">Impuestos</th>
                            <th>Total</th>
                            <th data-hide="phone">Método</th>
                            <th data-hide="all">Staff</th>
                            <th data-hide="all">Notas</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php                           
                          if ($transactions): ?>
                          <?php 
                          $rows = 0;
                          $revert_option = array();
                          foreach ($transactions as $trans): 
                            $rows++; 
                            if(!isset($revert_option[$trans->user_id][$trans->from_mu_id])) $revert_option[$trans->user_id][$trans->from_mu_id] = 0;
                            if(!isset($revert_option[$trans->user_id][$trans->to_mu_id])) $revert_option[$trans->user_id][$trans->to_mu_id] = 0;

                            if ($rows % 2 == 0) $class ='even'; else $class ='odd'; ?>
                          <tr class=<?php echo $class; ?> role="row">
                              <td ><?php echo $trans->user_name." ".$trans->user_last_name;?></td>
                              <td >
                                  <?php 
                                  if($trans->type == 'change')
                                    echo "Cambio de plan";
                                  else if($trans->type == 'renew')
                                    echo "Renovación";
                                  else if ($trans->type == 'new')
                                    echo "Alta";
                                  else
                                    echo "";
                                  ?>
                              </td>
                              <td >
                                <?php echo ($trans->notes == 'plan change')? "Cambio de plan a " : ""; 
                              echo $trans->membership." de ".date("d-m-Y", strtotime($trans->from))." a ".date("d-m-Y", strtotime($trans->to));?></td>
                              <td ><?php echo $trans->amount/100; echo " ".$trans->currency;?></td>
                              <td ><?php echo $trans->title;?></td>
                              <td ><?php echo $trans->discount." ".$trans->currency;?></td>
                              <td ><?php echo $trans->tax." ".$trans->currency;?></td>
                              <td ><?php echo $trans->amount/100; echo " ".$trans->currency;?></td>
                              <td ><?php echo $trans->gateway_name;?></td>
                              <td ><?php echo $trans->staff_name." ".$trans->staff_last_name;?></td>
                              <td ><?php echo $trans->notes;?></td>
                              <td ><?php echo date("d-m-Y H:i", strtotime($trans->created_on));?></td>
                              <td>
                              <?php if($trans->gateway_name == 'Domiciliación' AND $year == date('Y') AND $month == date('m')): ?>
                                <?php if($trans->status == '0'): ?>
                                  <a href='<?php echo base_url(); ?>staff/transactions/confirm/<?php echo $year;?>/<?php echo $month;?>/<?php echo $user;?>/<?php echo $trans->trans_id;?>' type="button" class="btn btn-primary2 btn-xs html5history"><i class='fa fa-exclamation'></i></a>
                                  <?php if($trans->email_not_received_sent == '0'): ?>
                                    <a href='<?php echo base_url(); ?>staff/transactions/email/<?php echo $year;?>/<?php echo $month;?>/<?php echo $user;?>/<?php echo $trans->trans_id;?>' type="button" class="btn btn-info btn-xs html5history"><i class='fa fa-envelope-o'></i></a>
                                  <?php endif;?>
                                <?php endif;?>
                                <?php if($trans->status == '1'): ?>
                                  <a href='<?php echo base_url(); ?>staff/transactions/retry/<?php echo $year;?>/<?php echo $month;?>/<?php echo $user;?>/<?php echo $trans->trans_id;?>' type="button" class="btn btn-success btn-xs html5history"><i class='fa fa-check'></i></a>
                                <?php endif;?>
                              <?php endif;?>
                              <?php $date = DateTime::createFromFormat('Y-m-d H:i:s', $trans->created_on)->format('Y-m-d');
                              if($date >= date('d-m-Y', strtotime('-15 days')) AND $revert_option[$trans->user_id][$trans->from_mu_id] == 0 AND  $revert_option[$trans->user_id][$trans->to_mu_id] == 0): 
                                $revert_option[$trans->user_id][$trans->from_mu_id] = 1;
                                $revert_option[$trans->user_id][$trans->to_mu_id] = 1; ?>
                                <a href='<?php echo base_url(); ?>staff/transactions/revertPayment/<?php echo $year;?>/<?php echo $month;?>/<?php echo $user;?>/<?php echo $trans->trans_id;?>' type="button" class="btn btn-danger btn-xs html5history_warning"><i class='fa fa-trash-o'></i></a>
                              <?php endif;?>
                              </td>
                          </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
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

var table = jQuery('.footable').footable();

 $('.nav-tabs').on( 'shown.bs.tab', function () {
    $('.footable').trigger('footable_resize');
});

    $('.select2').select2({

            placeholder: "-- Buscar clientes --",
            allowClear: false,

    });

</script>
