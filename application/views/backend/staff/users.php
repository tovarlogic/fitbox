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
                              <span>Users </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE USUARIOS
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>
<?php endif ?>

      <!-- Shortcuts -->
<?php $this->load->view('backend/staff/partials/shortcuts'); ?>
<br>     

      <div class="text-danger" id="infoMessage"><?php echo $this->session->flashdata('message')?></div>

      <div class="row">
        <div style="" class="col-lg-12">
            <div class="hpanel">
              <div class="panel-heading">
                <div class="pull-right">
                    <a href='<?php echo base_url(); ?>staff/users/add' class="btn btn-info btn-primary html5history"><i class="fa fa-plus"></i></a>
                </div>
                  Usuarios del Box
              </div>
              <ul class="nav nav-tabs">
                  <li class="active"><a data-toggle="tab" href="#tab-1">Activos <span class="label label-success "><?php echo ($clients != FALSE)? sizeof($clients) : 0; ?></span> </a> </li>
                  <li><a data-toggle="tab" href="#tab-5">Inactivos <span class="label label-danger "><?php echo ($clients_inactive != FALSE)? sizeof($clients_inactive) : 0; ?></span> </a> </li>
                  <li ><a data-toggle="tab" href="#tab-2">Sin plan <span class="label label-info "><?php echo ($pending != FALSE)? sizeof($pending) : 0; ?></span> </a> </li>
                  <li ><a data-toggle="tab" href="#tab-4">Invitados <span class="label label-warning "><?php echo ($guests != FALSE)? sizeof($guests) : 0; ?></span> </a> </li>
                  <li ><a data-toggle="tab" href="#tab-3">Staff <span class="label label-default "><?php echo ($staff != FALSE)? sizeof($staff) : 0; ?></span> </a> </li>
              </ul>

              <div class="tab-content">
                <div id="tab-1" class="tab-pane active">
                  <div class="panel-body"> 
                      <div class="row">
                        <div class="col-sm-12">
                          <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Búsqueda">
                          <?php $this->load->view('backend/messages'); ?>
                          <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="20" data-filter=#filter>
                            <thead>
                              <tr role="row">
                                <th data-toggle="true">Nombre</th>
                                <th data-hide="all">Usuario</th>
                                <th data-hide="all">e-mail</th>
                                <th data-hide="all">Teléfono</th>
                                <?php 
                                $group = $this->session->userdata('group');
                                if($group == 'sadmin' OR $group == 'admin' OR $group == 'fcoach' OR $group == 'comercial' OR $group == 'finance'):?>
                                <th data-hide="all">Cuenta bancaria</th>
                                <?php endif; ?>
                                <!--<th >Tipo</th>-->
                                <th data-hide="phone">Primer alta</th>
                                <th >Planes</th>
                                <th data-hide="phone">Caducidad</th>
                                <th data-hide="phone">Estado</th>
                                <th data-hide="phone">Acciones</th>
                              </tr>
                            </thead>
                            <tbody>
                              
                              <?php $rows = 0;
                              if($clients):
                                  foreach($clients as $user): 
                                    $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                                  <tr class=<?php echo $class; ?> role="row">
                                      <td><?php echo ucfirst($user['first_name'])." ".ucfirst($user['last_name']);?></td>
                                      <td><?php echo $user['username'];?></td>
                                      <td><?php echo $user['email'];?></td>
                                      <td><?php echo $user['phone'];?></td>
                                      <?php 
                                      if($group == 'sadmin' OR $group == 'admin' OR $group == 'fcoach' OR $group == 'comercial' OR $group == 'finance'):?>
                                      <td><?php echo (isset($user['IBAN']))? $user['IBAN']:"no disponible";?></td>
                                      <?php endif; ?>
                                      <!--<td><?php echo $user['description'];?></td>-->
                                      <td><?php echo date("d-m-Y", strtotime($user['created_on']));?></td>
                                      <td>
                                        <?php 
                                        $size = sizeof($user['memberships']);
                                        $i = 0;
                                        foreach ($user['memberships'] as $mem) {
                                          $i++;
                                          if ($mem['status'] == 'y'){ echo " <i class='fa fa-check text-success'> ".$mem['title']."</i>"; 
                                          }elseif($mem['status'] == 'n' OR $mem['status'] == 'e'){ echo " <i class='fa fa-minus text-warning'> ".$mem['title']." (Caducado)</i>"; 
                                          }elseif($mem['status'] == 'g'){ echo " <i class='fa fa-clock-o text-primary-2'> ".$mem['title']." (periodo gracia)</i>";
                                          }elseif($mem['status'] == 'b'){ echo " <i class='fa fa-ban text-danger'> Baneado</i>";
                                          }elseif($mem['status'] == 'p'){  echo " <i class='fa fa-question text-primary-2'> ".$mem['title']." (Pendiente pago inicial)</i>"; 
                                          }elseif($mem['status'] == 'c'){ echo "<i class='fa fa-times text-danger'> ".$mem['title']." (De baja)</i>"; }
                                          if ($i < $size && $i > 0) echo "<br>";
                                        }
                                        ?>
                                      </td>
                                      <td><?php 
                                          $i = 0;
                                          foreach ($user['memberships'] as $mem) 
                                          { 
                                            $i++;
                                            echo date("d-m-Y", strtotime($mem['mem_expire']));
                                            if ($i < $size && $i > 0) echo "<br>";
                                          }
                                        ?>
                                      </td>
                                      <td><?php 
                                        if ($user['active'] == '0'){ echo "Inactivo";
                                          }elseif($user['active'] == '1'){ echo "Activo"; 
                                          }else{ echo "Indefinido"; }
                                        ?>                                
                                      </td>
                                      <td>
                                        <a href='<?php echo base_url(); ?>staff/userMembership/list/<?php echo $user['id']; ?>' type="button" class="btn btn-info btn-xs html5history"><i class='fa fa-list-ul'></i> Planes</a>
                                        <a href='<?php echo base_url(); ?>staff/users/edit/<?php echo $user['id']; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i> Editar</a>
                                        <a href='<?php echo base_url(); ?>staff/users/delete/<?php echo $user['id']; ?>' type="button" class="btn btn-danger btn-xs html5history_warning_nourl"><i class='fa fa-trash-o'></i> Borrar</a>
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

                <div id="tab-5" class="tab-pane">
                  <div class="panel-body">
                      <div class="row">
                        <div class="col-sm-12">
                          <input type="text" class="form-control input-sm m-b-md" id="filter-5" placeholder="Búsqueda">
                          <?php $this->load->view('backend/messages'); ?>
                          <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="20" data-filter=#filter-5>
                            <thead>
                              <tr role="row">
                                <th data-toggle="true">Nombre</th>
                                <th data-hide="all">Usuario</th>
                                <th data-hide="all">e-mail</th>
                                <th data-hide="all">Teléfono</th>
                                <?php 
                                $group = $this->session->userdata('group');
                                if($group == 'sadmin' OR $group == 'admin' OR $group == 'fcoach' OR $group == 'comercial' OR $group == 'finance'):?>
                                <th data-hide="all">Cuenta bancaria</th>
                                <?php endif; ?>
                                <!--<th >Tipo</th>-->
                                <th data-hide="phone">Primer alta</th>
                                <th >Planes</th>
                                <th data-hide="phone">Caducidad</th>
                                <th data-hide="phone">Estado</th>
                                <th data-hide="phone">Acciones</th>
                              </tr>
                            </thead>
                            <tbody>
                              
                              <?php $rows = 0;
                              if($clients_inactive):
                                  foreach($clients_inactive as $user): 
                                    $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                                  <tr class=<?php echo $class; ?> role="row">
                                      <td><?php echo ucfirst($user['first_name'])." ".ucfirst($user['last_name']);?></td>
                                      <td><?php echo $user['username'];?></td>
                                      <td><?php echo $user['email'];?></td>
                                      <td><?php echo $user['phone'];?></td>
                                      <?php 
                                      if($group == 'sadmin' OR $group == 'admin' OR $group == 'fcoach' OR $group == 'comercial' OR $group == 'finance'):?>
                                      <td><?php echo (isset($user['IBAN']))? $user['IBAN']:"no disponible";?></td>
                                      <?php endif; ?>
                                      <!--<td><?php echo $user['description'];?></td>-->
                                      <td><?php echo date("d-m-Y", strtotime($user['created_on']));?></td>
                                      <td>
                                        <?php 
                                        $size = sizeof($user['memberships']);
                                        $i = 0;
                                        foreach ($user['memberships'] as $mem) {
                                          $i++;
                                          if ($mem['status'] == 'y'){ echo " <i class='fa fa-check text-success'> ".$mem['title']."</i>"; 
                                          }elseif($mem['status'] == 'n' OR $mem['status'] == 'e'){ echo " <i class='fa fa-minus text-warning'> ".$mem['title']." (Caducado)</i>"; 
                                          }elseif($mem['status'] == 'g'){ echo " <i class='fa fa-clock-o text-primary-2'> ".$mem['title']." (periodo gracia)</i>";
                                          }elseif($mem['status'] == 'b'){ echo " <i class='fa fa-ban text-danger'> Baneado</i>";
                                          }elseif($mem['status'] == 'p'){  echo " <i class='fa fa-question text-primary-2'> ".$mem['title']." (Pendiente pago inicial)</i>"; 
                                          }elseif($mem['status'] == 'c'){ echo "<i class='fa fa-times text-danger'> ".$mem['title']." (De baja)</i>"; }
                                          if ($i < $size && $i > 0) echo "<br>";
                                        }
                                        ?>
                                      </td>
                                      <td><?php 
                                          $i = 0;
                                          foreach ($user['memberships'] as $mem) 
                                          { 
                                            $i++;
                                            echo date("d-m-Y", strtotime($mem['mem_expire']));
                                            if ($i < $size && $i > 0) echo "<br>";
                                          }
                                        ?>
                                      </td>
                                      <td><?php 
                                        if ($user['active'] == '0'){ echo "Inactivo";
                                          }elseif($user['active'] == '1'){ echo "Activo"; 
                                          }else{ echo "Indefinido"; }
                                        ?>                                
                                      </td>
                                      <td>
                                        <a href='<?php echo base_url(); ?>staff/userMembership/list/<?php echo $user['id']; ?>' type="button" class="btn btn-info btn-xs html5history"><i class='fa fa-list-ul'></i></a>
                                        <a href='<?php echo base_url(); ?>staff/users/edit/<?php echo $user['id']; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
                                        <a href='<?php echo base_url(); ?>staff/users/delete/<?php echo $user['id']; ?>' type="button" class="btn btn-danger btn-xs html5history_warning_nourl"><i class='fa fa-trash-o'></i></a>
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

                <div id="tab-4" class="tab-pane">
                  <div class="panel-body">    
                    
                      <div class="row">
                        <div class="col-sm-12">
                          <input type="text" class="form-control input-sm m-b-md" id="filter-4" placeholder="Búsqueda">
                          <table id="table2" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="20" data-filter=#filter-4>
                            <thead>
                              <tr role="row">
                                <th data-toggle="true">Nombre</th>
                                <th data-hide="all">Usuario</th>
                                <th data-hide="all">e-mail</th>
                                <th data-hide="all">Teléfono</th>
                                <th >Tipo</th>
                                <th data-hide="phone">Desde</th>
                                <th data-hide="phone">Estado</th>
                                <th data-hide="phone">Acciones</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $rows = 0;
                              if($guests):
                                foreach($guests as $user): 
                                  $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                                <tr class=<?php echo $class; ?> role="row">
                                    <td ><?php echo $user['first_name']." ".$user['last_name'];?></td>
                                    <td ><?php echo $user['username'];?></td>
                                    <td><?php echo $user['email'];?></td>
                                    <td><?php echo $user['phone'];?></td>
                                    <td><?php echo $user['description'];?></td>
                                    <td><?php echo date("d-m-Y", strtotime($user['created_on']));?></td>
                                    <td><?php echo ($user['active'] == 1) ? "Activo" : "Inactivo";?></td>
                                    <td>
                                      <a href='<?php echo base_url(); ?>staff/userMembership/add/<?php echo $user['id']; ?>' type="button" class="btn btn-success btn-xs html5history"><i class='fa fa-euro'></i> Alta plan</a>
                                      <a href='<?php echo base_url(); ?>staff/users/edit/<?php echo $user['id']; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
                                      <a href='<?php echo base_url(); ?>staff/users/delete/<?php echo $user['id']; ?>' type="button" class="btn btn-danger btn-xs html5history_warning_nourl"><i class='fa fa-trash-o'></i></a>
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

                <div id="tab-3" class="tab-pane">
                  <div class="panel-body"> 

                      <div class="row">
                        <div class="col-sm-12">
                          <input type="text" class="form-control input-sm m-b-md" id="filter-3" placeholder="Búsqueda">
                          <table id="table3" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="20" data-filter=#filter-3>
                            <thead>
                              <tr role="row">
                                <th data-toggle="true">Nombre</th>
                                <th data-hide="phone">Usuario</th>
                                <th data-hide="all">e-mail</th>
                                <th data-hide="all">Teléfono</th>
                                <th >Tipo</th>
                                <th data-hide="phone">Estado</th>
                                <th data-hide="phone">Acciones</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $rows = 0;
                              if($staff):
                              foreach($staff as $user): 
                                $rows++; 
                                if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                              <tr class=<?php echo $class; ?> role="row">
                                  <td ><?php echo $user['first_name']." ".$user['last_name'];?></td>
                                  <td ><?php echo $user['username'];?></td>
                                  <td><?php echo $user['email'];?></td>
                                  <td><?php echo $user['phone'];?></td>
                                  <td><?php echo $user['description'];?></td>
                                  <td><?php echo ($user['active'] == 1) ? "Activo" : "Inactivo";?></td>
                                  <td>
                                    <?php
                                    if($login_group < $user['group_id']):
                                    ?>
                                    <a href='<?php echo base_url(); ?>staff/users/edit/<?php echo $user['id']; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
                                    <a href='<?php echo base_url(); ?>staff/users/delete/<?php echo $user['id']; ?>' type="button" class="btn btn-danger btn-xs html5history_warning_nourl"><i class='fa fa-trash-o'></i></a>
                                    <?php endif ?>
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

                <div id="tab-2" class="tab-pane">
                  <div class="panel-body"> 
                      <div class="row">
                        <div class="col-sm-12">
                          <input type="text" class="form-control input-sm m-b-md" id="filter-2" placeholder="Búsqueda">
                          <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="20" data-filter=#filter-2>
                            <thead>
                              <tr role="row">
                                <th data-toggle="true">Nombre</th>
                                <th data-hide="all">Usuario</th>
                                <th data-hide="all">e-mail</th>
                                <th data-hide="all">Teléfono</th>
                                <?php 
                                $group = $this->session->userdata('group');
                                if($group == 'sadmin' OR $group == 'admin' OR $group == 'fcoach' OR $group == 'comercial' OR $group == 'finance'):?>
                                <th data-hide="all">Cuenta bancaria</th>
                                <?php endif; ?>
                                <!--<th >Tipo</th>-->
                                <th data-hide="phone">Primer alta</th>
                                <th data-hide="phone">Estado</th>
                                <th data-hide="phone">Acciones</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $rows = 0;
                              if($pending):
                              foreach($pending as $user): 
                                $rows++; if ($rows % 2 == 0) {$class ='even'; }else{ $class ='odd'; }?>
                              <tr class=<?php echo $class; ?> role="row">
                                  <td><?php echo $user['first_name']." ".$user['last_name'];?></td>
                                  <td><?php echo $user['username'];?></td>
                                  <td><?php echo $user['email'];?></td>
                                  <td><?php echo $user['phone'];?></td>
                                  <?php 
                                  if($group == 'sadmin' OR $group == 'admin' OR $group == 'fcoach' OR $group == 'comercial' OR $group == 'finance'):?>
                                  <td><?php echo (isset($user['IBAN']))? $user['IBAN']:"no disponible";?></td>
                                  <?php endif; ?>
                                  <!--<td><?php echo $user['description'];?></td>-->
                                  <td><?php echo date("d-m-Y", strtotime($user['created_on']));?></td>
                                  <td><?php 
                                    if ($user['active'] == '0'){ echo "Inactivo";
                                      }elseif($user['active'] == '1'){ echo "Activo"; 
                                      }else{ echo "Indefinido"; }
                                    ?>                                
                                  </td>
                                  <td>
                                     <a href='<?php echo base_url(); ?>staff/userMembership/add/<?php echo $user['id']; ?>' type="button" class="btn btn-success btn-xs html5history"><i class='fa fa-euro'></i> Alta plan</a>
                                    <a href='<?php echo base_url(); ?>staff/users/edit/<?php echo $user['id']; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
                                    <a href='<?php echo base_url(); ?>staff/users/delete/<?php echo $user['id']; ?>' type="button" class="btn btn-danger btn-xs html5history_warning_nourl"><i class='fa fa-trash-o'></i></a>
                                  </td>
                              </tr>
                              <?php endforeach; ?>
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
          </div>
        </div>

<script>


  var table = jQuery('.footable').footable();

 $('.nav-tabs').on( 'shown.bs.tab', function () {
    $('.footable').trigger('footable_resize');
});

</script>
