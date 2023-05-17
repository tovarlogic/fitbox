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
                      <span>Configuración </span>
                  </li>
              </ol>
          </div>
          <h2 class="font-light m-b-xs">
              CONFIGURACIONES DE FITBOX
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
            Opciones de configuración
        </div>
        <div class="panel-body">
          <?php $this->load->view('backend/messages'); ?>
          <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <!-- Collapse -->
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading1">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="false" aria-controls="collapse1" class="collapsed">
                            <i class="fa fa-envelope-o"></i>
                            Comunicaciones
                        </a>
                    </h4>
                </div>
                <div id="collapse1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading1" aria-expanded="false" style="height: 0px;">
                    <div class="panel-body">

                    </div>
                </div>
            </div>
            <!-- Collapse -->
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading1">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="false" aria-controls="collapse2" class="collapsed">
                            <i class="fa fa-credit-card"></i>
                            Métodos de pago
                        </a>
                    </h4>
                </div>
                <div id="collapse2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading2" aria-expanded="false" style="height: 0px;">
                    <div class="panel-body">

                    </div>
                </div>
            </div>
            <!-- Collapse -->
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading1">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="false" aria-controls="collapse3" class="collapsed">
                            <i class="fa fa-clock-o"></i>
                            Facturación
                        </a>
                    </h4>
                </div>
                <div id="collapse3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading3" aria-expanded="false" style="height: 0px;">
                    <div class="panel-body">

                    </div>
                </div>
            </div>
            <!-- Collapse -->
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading4">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse4" aria-expanded="false" aria-controls="collapse4" class="collapsed">
                            <i class="fa fa-calendar"></i>
                            Calendario
                         </a>
                    </h4>
                </div>
                <div id="collapse4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading4" aria-expanded="false" style="height: 0px;">
                    <div class="panel-body">
                        <div class="row">
                          <a href="<?php echo base_url(); ?>staff/conf/calendar" class="label label-warning pull-right html5history"></i>Editar</a>
                        </div>
                        <div class="row">
                          <?php foreach ($calendar as $key => $value): ?>
                            <?php 
                            if($key == 'weekly'){ $key = 'Vista'; $value = ($value == 1)? 'Semanal' : 'Mensual'; }
                            else if($key == 'only_this_week'){ $key = 'Límite'; $value = ($value == 1)? 'Sólo actual' : 'Actual y anteriores'; }
                            else if($key == 'past_events'){ $key = 'Ver actividades pasadas'; $value = ($value == 1)? 'Si' : 'No'; }
                            else if($key == 'mark_past'){ $key = 'Diferenciar actividades pasadas'; $value = ($value == 1)? 'Si' : 'No'; }
                            else if($key == 'free_spots'){ $key = 'Plazas'; $value = ($value == 1)? 'Huecos libres' : 'Plazas reservadas'; }
                            else if($key == 'max_spots'){ $key = 'Mostrar Aforo máx.'; $value = ($value == 1)? 'Si' : 'No'; }
                            else if($key == 'allow_public'){ $key = 'Acceso'; $value = ($value == 1)? 'Público' : 'Restringido'; }
                            else if($key == 'use_popup'){ $key = 'Pop-ups'; $value = ($value == 1)? 'Si' : 'No'; }
                            else if($key == 'start_day'){ $key = '1er día semana'; $value = ($value == 1)? 'Lunes' : 'Domingo'; }
                            ?>
                            <div class="col-sm-3" style="">
                                <div class="project-label">
                                  <b><?php echo $key; ?></b>
                                </div>
                                <small>
                                  <?php echo $value; ?>
                                </small>
                            </div>
                          <?php endforeach ?>
                      </div>
                    </div>
                </div>
            </div>

            <!-- Collapse -->
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading6">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse6" aria-expanded="false" aria-controls="collapse6" class="collapsed">
                            <i class="fa fa-sliders"></i>
                            Planes
                        </a>
                    </h4>
                </div>
                <div id="collapse6" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading6" aria-expanded="false" style="height: 0px;">
                    <div class="panel-body">
                      <div class="row">
                          <a href="<?php echo base_url(); ?>staff/conf/membership" class="label label-warning pull-right html5history"></i>Editar</a>
                        </div>
                        <div class="row">
                          <?php foreach ($membership as $key => $value): ?>
                            <?php 
                            if($key == 'grace_period'){ $key = 'Periodo de gracia'; $value = ($value > 1)? $value.' días' : $value.' dia'; }
                            else if($key == 'cancel_period'){ $key = 'Límite para cancelación'; $value = ($value > 1)? $value.' días' : $value.' dia'; }
                            ?>
                            <div class="col-sm-3" style="">
                                <div class="project-label">
                                  <b><?php echo $key; ?></b>
                                </div>
                                <small>
                                  <?php echo $value; ?>
                                </small>
                            </div>
                          <?php endforeach ?>
                      </div>
                    </div>
                </div>
            </div>


        </div>
      </div>
  </div>
</div>


