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
                          <li>
                              <span><a href='<?php echo base_url(); ?>staff/users' class='html5history'>Usuarios</a></span>
                          </li>
                          <li class="active">
                              <span>Planes </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      PLANES CONTRATADOS POR: <?php echo $user->first_name." ".$user->last_name; ?>
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
                  <div class="pull-right">
                    <a href='<?php echo base_url(); ?>staff/userMembership/add/<?php echo $user->id; ?>' class="btn btn-info btn-primary html5history"><i class="fa fa-plus"></i></a>
                </div>
                  Planes contratados
              </div>
              <div class="panel-body">
                  <div class="row">
                    <div class="col-sm-12">
                      <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Search in table">
                      <?php $this->load->view('backend/messages'); ?>
                      <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="12" data-filter=#filter>
                        <thead>
                          <tr role="row">
                            <th data-toggle="true">Título</th>
                            <th data-hide="phone">Precio</th>
                            <th data-hide="phone">Periodicidad</th>
                            <!--<th >Compatibilidad</th>-->
                            <!-- <th data-hide="phone">Método de Pago</th> -->
                            <th data-hide="all">Fecha Alta</th>
                            <th data-hide="phone">Fecha caducidad</th>
                            <th data-hide="phone">Fecha baja</th>
                            <th >Estado</th>
                            <th data-hide="phone">Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $rows = 0;
                          if(!empty($memberships)):
                          foreach($memberships as $membership): 
                            $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                          <tr class=<?php echo $class; ?> role="row">
                              <td ><?php echo $membership['title'];?></td>
                              <td ><?php echo $membership['price']." €";?></td>
                              <td >
                                <?php  
                                  if($membership['period'] == 'M'){ 
                                    if($membership['days'] == 1) echo 'mensual';
                                    else if($membership['days'] == 2) echo 'bimensual';
                                    else if($membership['days'] == 3) echo 'trimestral';
                                    else if($membership['days'] == 4) echo 'cuatrimestral';
                                    else if($membership['days'] == 6) echo 'semestral';
                                    else { echo $membership['days']." "; echo ($membership['days'] == 1)? $this->lang->line('date_month') : $this->lang->line('date_months'); }
                                  }
                                  elseif($membership['period'] == 'D'){ 
                                    if($membership['days'] == 1) echo 'diaria';
                                    else echo $membership['days']." ".$this->lang->line('date_days');
                                  }
                                  elseif($membership['period'] == 'Y'){ 
                                    if($membership['days'] == 1) echo "anual";
                                    else  echo $membership['days']." ".$this->lang->line('date_years');
                                  }
                                ?>
                              </td>
                              <td> <?php echo date("d-m-Y", strtotime($membership['created_on'])); ?></td>
                              <td> <?php echo date("d-m-Y", strtotime($membership['mem_expire'])); ?></td>
                              <td> <?php 
                                if ($membership['status'] == 'c') echo date("d-m-Y H:i", strtotime($membership['updated_on'])); 
                                else echo "-"; 
                                ?>
                              </td>
                              <td><?php 
                                if ($membership['status'] == 'y'){ echo "<i class='fa text-success fa-check'> Activo </i>";}
                                else if ($membership['status'] == 'g') { echo "<i class='fa fa-clock-o text-primary-2'> periodo gracia</i>";}
                                else if ($membership['status'] == 'p') { echo "<i class='fa fa-question text-primary-2'> Pendiente pago</i>";}
                                else if ($membership['status'] == 'n' OR $membership['status'] == 'g') { echo "<i class='fa text-warning fa-minus'> Caducado</i>";} 
                                else if ($membership['status'] == 'c') { echo "<i class='fa text-danger fa-minus'> De baja</i>";} 
                                else if ($membership['status'] == 'e') { echo "<i class='fa text-danger fa-minus'> Consumido</i>";} 
                                ?>
                              </td>
                              <td>
                                <?php

                                ?>
                                  <?php if (($membership['period'] != 'D' AND ($membership['status'] == 'p' OR $membership['status'] == 'y') ) OR ($membership['period'] == 'D' AND $membership['status'] == 'p' )) : ?>
                                    <?php if($membership['status'] == 'p'): ?>
                                      <a href='<?php echo base_url(); ?>staff/membershipPayment/add/<?php echo $membership['id']; ?>' type="button" class="btn btn-success btn-xs html5history"><i class='fa fa-euro'></i> Pagar</a>
                                    <?php else : ?>
                                      <?php if(empty($membership['subscribed'])): ?>
                                        <a href='<?php echo base_url(); ?>staff/membershipPayment/renew/<?php echo $membership['id']; ?>' type="button" class="btn btn-success btn-xs html5history"><i class='fa fa-euro'></i> Renovar</a>
                                      <?php endif; ?>
                                    <?php endif; ?>
                                  <?php endif; ?>
                                  <?php if ($membership['period'] != 'D' AND $membership['status'] == 'y' AND $membership['mem_expire'] > date('Y-m-d')) : ?>
                                  <a href='<?php echo base_url(); ?>staff/userMembership/changePlan/<?php echo $user->id; ?>/<?php echo $membership['id']; ?>' type="button" class="btn btn-info btn-xs html5history"><i class='fa fa-exchange'></i> Cambiar plan</a>
                                  <?php endif; ?>
                                  <?php if ($membership['period'] != 'D' AND $membership['status'] != 'c' ) : ?>
                                  <a href='<?php echo base_url(); ?>staff/userMembership/cancel/<?php echo $user->id; ?>/<?php echo $membership['id']; ?>' type="button" class="btn btn-danger btn-xs html5history_warning"><i class='fa fa-times'> 
                                  Baja</i></a>
                                  <?php endif; ?>
                                
                              </td>
                          </tr>
                          <?php endforeach ?>
                        <?php endif ?>
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

</script>
