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
                  <div class="pull-right">
                    <a href='<?php echo base_url(); ?>staff/membership/add' class="btn btn-info btn-primary html5history"><i class="fa fa-plus"></i></a>
                </div>
                  Planes y Tarifas del Box
              </div>
              <div class="panel-body">
                
                  <div class="row">
                    <div class="col-sm-12">
                      <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Búsqueda">
                      <?php $this->load->view('backend/messages'); ?>
                      <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="20" data-filter=#filter>
                        <thead>
                          <tr role="row">
                            <th data-toggle="true">Título</th>
                            <th data-hide="phone">Servicios</th>
                            <th >Precio</th>
                            <th >Caducidad</th>
                            <th data-hide="phone,tablet">Clientes</th>
                            <th data-hide="phone">Disponibilidad</th>
                            <th data-hide="phone,tablet">Recurrente</th>
                            <th data-hide="phone,tablet">Privado</th>
                            <th data-hide="phone,tablet">Validez</th>
                            <th >Estado</th>
                            <th data-hide="phone,tablet">Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $rows = 0;
                          if(!empty($memberships)):
                          foreach($memberships as $membership): 
                            $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                          <tr class=<?php echo $class; ?> role="row">
                              <td ><?php echo $membership['title'];?></td>
                              <td >
                                <?php 
                                if ($membership['services']) { 
                                  foreach ($membership['services'] as $ser) {
                                      if($ser['name']) echo $ser['name'].", ";
                                      else echo "";
                                  }
                                }
                                ?>
                              </td>
                              <td ><?php echo $membership['price']." €";?></td>
                              <td ><?php 
                                  echo $membership['days']; 
                                  if($membership['period'] == 'M'){echo " meses";}
                                  elseif($membership['period'] == 'D'){ echo " dias"; }
                                  elseif($membership['period'] == 'Y'){ echo " año"; } ?>
                              </td>
                              <td>
                                <?php
                                 if(isset($subscriptions[$membership['id']]))
                                 {
                                    echo "<i class='text-info'>".$subscriptions[$membership['id']]."</i>"; 
                                 } 
                                 else
                                {
                                  echo "<i class='text-danger'> 0</i>"; 
                                }
                                ?>
                              </td>
                              <td>
                                <?php 
                                  $highlight = 0;
                                  if($membership['available_from'] != '0000' OR $membership['available_to'] != '2359') $highlight = 1;
                                  $available_from = str_split($membership['available_from'], 2);
                                  $available_to = str_split($membership['available_to'], 2);
                                  echo ($highlight == 0)? 
                                    $available_from[0].":".$available_from[1]." a ".$available_to[0].":".$available_to[1]
                                    :
                                    "<i class='text-info'>".$available_from[0].":".$available_from[1]." a ".$available_to[0].":".$available_to[1]."</i>"; 
                                    ?>
                              </td>
                              <td><?php echo ($membership['recurring'] == '1') ? "Si" : "";?></td>
                              <td><?php echo ($membership['private'] == '1') ? "Si" : "";?></td>
                              <td><?php echo ($membership['deprecated'] == '1') ? "En desuso" : "";?></td>
                              <td><?php 
                                if ($membership['active'] == '1'){ echo "<i class='fa success fa-check'> Activo </i>";}
                                else{ echo "<i class='fa fa-minus'> Inactivo</i>";} ?>
                              </td>
                              <td>
                                <a class='btn btn-warning btn-xs html5history' href='<?php echo base_url(); ?>staff/membership/edit/<?php echo $membership['id'];?>'><i class='fa fa-pencil'></i></a>
                                <a href='<?php echo base_url(); ?>staff/deleteMembership/<?php echo $membership['id']; ?>' type="button" class="btn btn-danger btn-xs html5history_warning"><i class='fa fa-trash-o'></i></a>
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

 jQuery('.footable').footable();

 jQuery('.footable').trigger('footable_resize');


</script>
