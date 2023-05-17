       <?php 
$html_default = $this->config->item('html_default', 'settings');
if( $html_default['show_header'] === TRUE): 
?>
      <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href="#" onclick="goTo('athlete','index');">Inicio</a></li>
                          <li>
                              <span>Log book </span>
                          </li>
                          <li class="active">
                              <span>Nutrición </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE NUTRICIÓN
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>
      <?php endif ?>
      
       <div class="row">
            <div class="col-md-3" style="">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="stats-title pull-left">
                            <h4>Fecha del diario</h4>
                        </div>
                        <div class="stats-icon pull-right">
                            <i class="pe-7s-date fa-2x"></i>
                        </div>
                        <div class="m-t-xl">
                            <?php echo form_open("athlete/nutrition/log"); ?>
                            <form action="<?php echo base_url();?>fitbox/athlete/nutrition/log" method="post" accept-charset="utf-8">
                            <input name="date" value="<?php echo $date; ?>" id="date" class="form-control" data-date-format="yyyy-mm-dd" required="required" type="date">
                            </form>
                            
                            <small>
                                Selecciona la fecha de tu diario que quieras revisar. Puedes consultar desde tu primera entrada, no importa hace cuanto.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" style="">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="stats-title pull-left">
                            <h4>Registro alimentos</h4>
                        </div>
                        <div class="stats-icon pull-right">
                            <i class="pe-7s-note fa-2x"></i>
                        </div>
                        <div class="m-t-xl">
                            <div class="row list">
                                  <div class="col-md-4" style="">
                                      <a href='<?php echo base_url(); ?>athlete/nutrition/food' type="button" class="btn btn-block btn-outline btn-info html5history">Alimento</a>
                                  </div>
                                  <div class="col-md-4" style="">
                                      <a href='<?php echo base_url(); ?>athlete/nutrition/meal' type="button" class="btn btn-block btn-outline btn-info html5history">Plato</a>
                                  </div>
                                  <div class="col-md-4" style="">
                                    <a href='<?php echo base_url(); ?>athlete/nutrition/addMeal' type="button" class="btn btn-block btn-outline btn-info html5history">Crear plato</a>
                                  </div>
                            </div>
                            <small>
                                Elige entre la opción de registrar alimentos por separados o platos completos a partir de recetas predefinidas.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" style="">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="stats-title pull-left">
                            <h4>Consulta de nutrientes</h4>
                        </div>
                        <div class="stats-icon pull-right">
                            <i class="pe-7s-display2 fa-2x"></i>
                        </div>
                        <div class="m-t-xl">
                            <div class="row list">
                                  <div class="col-md-12" style="">
                                      <a href='<?php echo base_url(); ?>athlete/nutrition/search' type="button" class="btn btn-block btn-outline btn-info html5history">Consultar</a>
                                  </div>
                            </div>
                            <small>
                                Consulta la base de alimentos completa, <strong> ve en detalle </strong> los macro nutrientes, minerales, oligoelementos y vitaminas que contienen.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" style="">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="stats-title pull-left">
                            <h4>Configuración y opciones</h4>
                        </div>
                        <div class="stats-icon pull-right">
                            <i class="pe-7s-edit fa-2x"></i>
                        </div>
                        <div class="m-t-xl">
                            <div class="row list">
                                  <div class="col-md-6" style="">
                                      <a href='<?php echo base_url(); ?>athlete/nutrition/config' type="button" class="btn btn-block btn-outline btn-info html5history">Configuración</a>
                                  </div>
                                  <div class="col-md-6" style="">
                                      <a href='<?php echo base_url(); ?>athlete/food/add' type="button" class="btn btn-block btn-outline btn-info html5history">Nuevo alimento</a>
                                  </div>
                            </div>
                            <small>
                                Configura tus objetivos nutricionales o propón que añadamos a la base de datos algún alimento que no encuentres.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view('backend/messages'); ?>
        <div class="row">
            <div class="col-lg-2">
                <div class="hpanel">
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        Calorías por Macros
                    </div>
                    <div class="panel-body list">

                        <div class="text-center">
                            <a href="#" class="btn btn-xs btn-default">Diario</a>
                            <a href="#" class="btn btn-xs btn-default">Mensual</a>
                            <a href="#" class="btn btn-xs btn-default">Anual</a>
                        </div>

                        <div class="list-item-container">
                            <div class="list-item">
                                <h4 class="no-margins font-extra-bold text-success"><?php echo round($nutrient_stats['energy']); ?> kCal</h4>
                                <small>Calorias totales ingeridas</small>
                                <div class="pull-right font-bold">100% <i class="fa fa-level-up text-success"></i></div>
                            </div>
                            <div class="list-item">
                                <h5 class="no-margins font-extra-bold text-info"><?php echo round($nutrient_stats['carbs']);?> kCal</h5>
                                <small>Carbohidratos</small>
                                <div class="pull-right font-bold"><?php echo ($nutrient_stats['energy'] > 0) ? round($nutrient_stats['carbs']*100/$nutrient_stats['energy']) : "0"; ?>% <i class="fa fa-level-down text-color3"></i></div>
                            </div>
                            <div class="list-item">
                                <h5 class="no-margins font-extra-bold text-danger"><?php echo round($nutrient_stats['proteins']);?> kCal</h5>
                                <small>Proteinas</small>
                                <div class="pull-right font-bold"><?php echo ($nutrient_stats['energy'] > 0) ? round($nutrient_stats['proteins']*100/$nutrient_stats['energy']) : "0"; ?>% <i class="fa fa-bolt text-color3"></i></div>
                            </div>
                            <div class="list-item">
                                <h5 class="no-margins font-extra-bold text-warning"><?php echo round($nutrient_stats['fats']);?> kCal</h5>
                                <small>Grasas</small>
                                <div class="pull-right font-bold"><?php echo ($nutrient_stats['energy'] > 0) ? round($nutrient_stats['fats']*100/$nutrient_stats['energy']) : "0"; ?>% <i class="fa fa-level-up text-success"></i></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="hpanel">
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        Cantidades por Macros
                    </div>
                    <div class="panel-body list">
                        <div class="text-center">
                            <a href="#" class="btn btn-xs btn-default">Diario</a>
                            <a href="#" class="btn btn-xs btn-default">Mensual</a>
                            <a href="#" class="btn btn-xs btn-default">Anual</a>
                        </div>
                        <div class="list-item-container">
                            <div class="list-item">
                                <h4 class="no-margins font-extra-bold text-success"><?php echo round($nutrient_stats['qtty']); ?> gr </h6>
                                <small>gramos totales consumidos</small>
                                <div class="pull-right font-bold">100% <i class="fa fa-level-up text-success"></i></div>
                            </div>
                            <div class="list-item">
                                <h5 class="no-margins font-extra-bold text-info"><?php echo round($nutrient_stats['gr_carbs']);?> gr + <small class="text-info"> (Fibra <?php echo round($nutrient_stats['gr_fiber']);?> gr)</small></h5>
                                <small>Carbohidratos</small>
                                <div class="pull-right font-bold"><?php echo ($nutrient_stats['qtty'] > 0) ? round($nutrient_stats['gr_carbs']*100/$nutrient_stats['qtty']) : "0"; ?>% <i class="fa fa-level-down text-color3"></i></div>
                            </div>
                            <div class="list-item">
                                <h5 class="no-margins font-extra-bold text-danger"><?php echo round($nutrient_stats['gr_proteins']);?> gr</h5>
                                <small>Proteinas</small>
                                <div class="pull-right font-bold"><?php echo ($nutrient_stats['qtty'] > 0) ? round($nutrient_stats['gr_proteins']*100/$nutrient_stats['qtty']) : "0"; ?>% <i class="fa fa-bolt text-color3"></i></div>
                            </div>
                            <div class="list-item">
                                <h5 class="no-margins font-extra-bold text-warning"><?php echo round($nutrient_stats['gr_fats']);?> gr</h5>
                                <small>Grasas</small>
                                <div class="pull-right font-bold"><?php echo ($nutrient_stats['qtty'] > 0) ? round($nutrient_stats['gr_fats']*100/$nutrient_stats['qtty']) : "0"; ?>% <i class="fa fa-level-up text-success"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="hpanel">
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        Bloques por Macros
                    </div>
                    <div class="panel-body list">
                        <div class="text-center">
                            <a href="#" class="btn btn-xs btn-default">Diario</a>
                            <a href="#" class="btn btn-xs btn-default">Mensual</a>
                            <a href="#" class="btn btn-xs btn-default">Anual</a>
                        </div>
                        <div class="list-item-container">
                            <div class="list-item">
                                <h4 class="no-margins font-extra-bold text-success"><?php echo round($nutrient_stats['total_blocks']); ?> bloques</h4>
                                <small>Bloques totales consumidos</small>
                                <div class="pull-right font-bold">100% <i class="fa fa-level-up text-success"></i></div>
                            </div>
                            <div class="list-item">
                                <h5 class="no-margins font-extra-bold text-info"><?php echo round($nutrient_stats['carb_blocks']);?> bloques</h5>
                                <small>Carbohidratos</small>
                                <div class="pull-right font-bold"><?php echo ($nutrient_stats['total_blocks'] > 0) ? round($nutrient_stats['carb_blocks']*100/$nutrient_stats['total_blocks']) : "0"; ?>% <i class="fa fa-level-down text-color3"></i></div>
                            </div>
                            <div class="list-item">
                                <h5 class="no-margins font-extra-bold text-danger"><?php echo round($nutrient_stats['protein_blocks']);?> bloques</h5>
                                <small>Proteinas</small>
                                <div class="pull-right font-bold"><?php echo ($nutrient_stats['total_blocks'] > 0) ? round($nutrient_stats['protein_blocks']*100/$nutrient_stats['total_blocks']) : "0"; ?>% <i class="fa fa-bolt text-color3"></i></div>
                            </div>
                            <div class="list-item">
                                <h5 class="no-margins font-extra-bold text-warning"><?php echo round($nutrient_stats['fat_blocks']);?> bloques</h5>
                                <small>Grasas</small>
                                <div class="pull-right font-bold"><?php echo ($nutrient_stats['total_blocks'] > 0) ? round($nutrient_stats['fat_blocks']*100/$nutrient_stats['total_blocks']) : "0"; ?>% <i class="fa fa-level-up text-success"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hpanel">
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        Resumen de gráfico
                    </div>
                    <div class="panel-body list">
                        <div class="flot-chart" style="height: 240px">
                          <div class="flot-chart-content" id="flot-line-chart"></div>
                      </div>

                    </div>
                </div>
            </div>
      </div>

      <div class="row">
            <div class="col-lg-6">
                <div class="hpanel">
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        Minerales y oligoelementos
                    </div>
                    <div class="panel-body list">

                        <div class="text-center">
                            <a href="#" class="btn btn-xs btn-default">Diario</a>
                            <a href="#" class="btn btn-xs btn-default">Mensual</a>
                            <a href="#" class="btn btn-xs btn-default">Anual</a>
                        </div>

                        <div class="col-lg-4">
                          <div class="list-item-container">
                              <div class="no-margins list-item">
                                  <small><b>Calcio</b> <?php echo round($nutrient_stats['Calcium_mg']); ?>mg </small> <div class="pull-right font-bold"> <small>(<?php echo round($DV['Calcium_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Calcium_mg']<=25) $color = 'danger'; else if($DV['Calcium_mg']<=50) $color = 'warning'; else if($DV['Calcium_mg']<=75) $color = 'info'; else if($DV['Calcium_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Calcium_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Hierro</b> <?php echo round($nutrient_stats['Iron_mg']); ?>mg</small>  <div class="pull-right font-bold"> <small>(<?php echo round($DV['Iron_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Iron_mg']<=25) $color = 'danger'; else if($DV['Iron_mg']<=50) $color = 'warning'; else if($DV['Iron_mg']<=75) $color = 'info'; else if($DV['Iron_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Iron_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Magnesio</b> <?php echo round($nutrient_stats['Magnesium_mg']); ?>mg</small>   <div class="pull-right font-bold"> <small>(<?php echo round($DV['Magnesium_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Magnesium_mg']<=25) $color = 'danger'; else if($DV['Magnesium_mg']<=50) $color = 'warning'; else if($DV['Magnesium_mg']<=75) $color = 'info'; else if($DV['Magnesium_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Magnesium_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Fósforo</b> <?php echo round($nutrient_stats['Phosphorus_mg']); ?>mg</small>   <div class="pull-right font-bold"> <small>(<?php echo round($DV['Phosphorus_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Phosphorus_mg']<=25) $color = 'danger'; else if($DV['Phosphorus_mg']<=50) $color = 'warning'; else if($DV['Phosphorus_mg']<=75) $color = 'info'; else if($DV['Phosphorus_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Phosphorus_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                          </div>
                        </div>

                        <div class="col-lg-4">
                          <div class="list-item-container">
                              <div class="no-margins list-item">
                                  <small><b>Potasio</b> <?php echo round($nutrient_stats['Potassium_mg']); ?>mg</small>  <div class="pull-right font-bold"> <small>(<?php echo round($DV['Potassium_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Potassium_mg']<=25) $color = 'danger'; else if($DV['Potassium_mg']<=50) $color = 'warning'; else if($DV['Potassium_mg']<=75) $color = 'info'; else if($DV['Potassium_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Potassium_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Sodio</b> <?php echo round($nutrient_stats['Sodium_mg']); ?>mg</small>  <div class="pull-right font-bold"> <small>(<?php echo round($DV['Sodium_mg']); ?>% CMDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Sodium_mg']<=25) $color = 'success'; else if($DV['Sodium_mg']<=50) $color = 'info'; else if($DV['Sodium_mg']<=75) $color = 'warning'; else if($DV['Sodium_mg']>75) $color = 'danger'; ?>
                                    <div style="width: <?php echo round($DV['Sodium_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Zinc</b>  <?php echo round($nutrient_stats['Zinc_mg']); ?>mg</small>  <div class="pull-right font-bold"> <small>(<?php echo round($DV['Zinc_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Zinc_mg']<=25) $color = 'danger'; else if($DV['Zinc_mg']<=50) $color = 'warning'; else if($DV['Zinc_mg']<=75) $color = 'info'; else if($DV['Zinc_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Zinc_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                          </div>
                        </div>

                        <div class="col-lg-4">
                          <div class="list-item-container">
                            <div class="no-margins list-item">
                                  <small><b>Cobre</b>  <?php echo round($nutrient_stats['Copper_mg']); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Copper_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Copper_mg']<=25) $color = 'danger'; else if($DV['Copper_mg']<=50) $color = 'warning'; else if($DV['Copper_mg']<=75) $color = 'info'; else if($DV['Copper_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Copper_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Manganeso</b> <?php echo round($nutrient_stats['Manganese_mg']); ?>mg</small>   <div class="pull-right font-bold"> <small>(<?php echo round($DV['Manganese_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Manganese_mg']<=25) $color = 'danger'; else if($DV['Manganese_mg']<=50) $color = 'warning'; else if($DV['Manganese_mg']<=75) $color = 'info'; else if($DV['Manganese_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Manganese_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Selenio</b>  <?php echo round($nutrient_stats['Selenium_ug']); ?>ug</small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Selenium_ug']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Selenium_ug']<=25) $color = 'danger'; else if($DV['Selenium_ug']<=50) $color = 'warning'; else if($DV['Selenium_ug']<=75) $color = 'info'; else if($DV['Selenium_ug']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Selenium_ug']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                          </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="hpanel">
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        Vitaminas
                    </div>
                    <div class="panel-body list">

                        <div class="text-center">
                            <a href="#" class="btn btn-xs btn-default">Diario</a>
                            <a href="#" class="btn btn-xs btn-default">Mensual</a>
                            <a href="#" class="btn btn-xs btn-default">Anual</a>
                        </div>

                        <div class="col-lg-4">
                          <div class="list-item-container">
                              <div class="no-margins list-item">
                                  <small><b>Vit A</b> <?php echo round($nutrient_stats['Vit_A_RAE']); ?>ug </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_A_RAE']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                    <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_A_RAE']<=25) $color = 'danger'; else if($DV['Vit_A_RAE']<=50) $color = 'warning'; else if($DV['Vit_A_RAE']<=75) $color = 'info'; else if($DV['Vit_A_RAE']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_A_RAE']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Vit C</b>  <?php echo round($nutrient_stats['Vit_C_mg']); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_C_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_C_mg']<=25) $color = 'danger'; else if($DV['Vit_C_mg']<=50) $color = 'warning'; else if($DV['Vit_C_mg']<=75) $color = 'info'; else if($DV['Vit_C_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_C_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Vit D</b> <?php echo round($nutrient_stats['Vit_D_ug']); ?>ug </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_D_ug']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_D_ug']<=25) $color = 'danger'; else if($DV['Vit_D_ug']<=50) $color = 'warning'; else if($DV['Vit_D_ug']<=75) $color = 'info'; else if($DV['Vit_D_ug']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_D_ug']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Vit E</b> <?php echo round($nutrient_stats['Vit_E_mg']); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_E_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_E_mg']<=25) $color = 'danger'; else if($DV['Vit_E_mg']<=50) $color = 'warning'; else if($DV['Vit_E_mg']<=75) $color = 'info'; else if($DV['Vit_E_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_E_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>

                              <div class="no-margins list-item">
                                  <small><b>Acid. Pantoténico (B5)</b>  <?php echo round($nutrient_stats['Panto_Acid_mg']); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Panto_Acid_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Panto_Acid_mg']<=25) $color = 'danger'; else if($DV['Panto_Acid_mg']<=50) $color = 'warning'; else if($DV['Panto_Acid_mg']<=75) $color = 'info'; else if($DV['Panto_Acid_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Panto_Acid_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>

                          </div>
                        </div>

                        <div class="col-lg-4">
                          <div class="list-item-container">
                              <div class="no-margins list-item">
                                  <small><b>Vit K</b>  <?php echo round($nutrient_stats['Vit_K_ug']); ?>ug </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_K_ug']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_K_ug']<=25) $color = 'danger'; else if($DV['Vit_K_ug']<=50) $color = 'warning'; else if($DV['Vit_K_ug']<=75) $color = 'info'; else if($DV['Vit_K_ug']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_K_ug']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Vit B6</b>  <?php echo round($nutrient_stats['Vit_B6_mg']); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_B6_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_B6_mg']<=25) $color = 'danger'; else if($DV['Vit_B6_mg']<=50) $color = 'warning'; else if($DV['Vit_B6_mg']<=75) $color = 'info'; else if($DV['Vit_B6_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_B6_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Tiamina (B1)</b> <?php echo round($nutrient_stats['Thiamin_mg']); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Thiamin_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Thiamin_mg']<=25) $color = 'danger'; else if($DV['Thiamin_mg']<=50) $color = 'warning'; else if($DV['Thiamin_mg']<=75) $color = 'info'; else if($DV['Thiamin_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Thiamin_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Riboflavina (B2)</b>  <?php echo round($nutrient_stats['Riboflavin_mg']); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Riboflavin_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Riboflavin_mg']<=25) $color = 'danger'; else if($DV['Riboflavin_mg']<=50) $color = 'warning'; else if($DV['Riboflavin_mg']<=75) $color = 'info'; else if($DV['Riboflavin_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Riboflavin_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>

                              <div class="no-margins list-item">
                                  <small><b>Cholina</b>  </small><div class="pull-right font-bold"> <small>(100% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_A_RAE']<=25) $color = 'danger'; else if($DV['Vit_A_RAE']<=50) $color = 'warning'; else if($DV['Vit_A_RAE']<=75) $color = 'info'; else if($DV['Vit_A_RAE']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_A_RAE']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                          </div>
                        </div>

                        <div class="col-lg-4">
                          <div class="list-item-container">
                              <div class="no-margins list-item">
                                  <small><b>Niacina (B3)</b>  <?php echo round($nutrient_stats['Niacin_mg']); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Niacin_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Niacin_mg']<=25) $color = 'danger'; else if($DV['Niacin_mg']<=50) $color = 'warning'; else if($DV['Niacin_mg']<=75) $color = 'info'; else if($DV['Niacin_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Niacin_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Folato (B9)</b>  </small><div class="pull-right font-bold"><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_A_RAE']<=25) $color = 'danger'; else if($DV['Vit_A_RAE']<=50) $color = 'warning'; else if($DV['Vit_A_RAE']<=75) $color = 'info'; else if($DV['Vit_A_RAE']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_A_RAE']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Vit B12</b> <?php echo round($nutrient_stats['Vit_B12_ug']); ?>ug </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_B12_ug']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_B12_ug']<=25) $color = 'danger'; else if($DV['Vit_B12_ug']<=50) $color = 'warning'; else if($DV['Vit_B12_ug']<=75) $color = 'info'; else if($DV['Vit_B12_ug']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_B12_ug']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>

                              <div class="no-margins list-item">
                                  <small><b>Betaina</b>  </small><div class="pull-right font-bold"> <small>(100% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_A_RAE']<=25) $color = 'danger'; else if($DV['Vit_A_RAE']<=50) $color = 'warning'; else if($DV['Vit_A_RAE']<=75) $color = 'info'; else if($DV['Vit_A_RAE']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_A_RAE']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
      </div>      

    <div class="row nutrients_container" id="Nutrients">

    </div>

      <div class="row">
        <div style="" class="col-lg-12">
            <div class="hpanel">
              <div class="panel-heading hbuilt">
                  <div class="panel-tools">
                      <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                      <a class="closebox"><i class="fa fa-times"></i></a>
                  </div>
                  Histórico alimentación
              </div>
              <div class="panel-body">


                  <div class="row">
                    <div class="col-sm-12">
                      <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Search in table">
                      <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="20" data-filter=#filter>
                        <thead>
                          <tr role="row">
                            <th data-toggle="true">Fecha</th>
                            <th data-hide="phone">Comida</th>
                            <th data-hide="phone">Grupo</th>
                            <th>Alimento</th>
                            <th>Energía (kcal)</th>
                            <th data-hide="phone">Total (g)</th>
                            <th data-hide="phone,tablet" class="text-info">Hcarbono (g)</th>
                            <th data-hide="phone,tablet">Fibra (g)</th>
                            <th data-hide="phone,tablet" class="text-danger">Proteinas (g)</th>
                            <th data-hide="phone,tablet" class="text-warning">Grasas (g)</th>
                            <th data-breakpoints="xs sm" data-hide="all">Bloques Proteinas</th>
                            <th data-hide="all">Bloques Carbohidratos</th>
                            <th data-hide="all">Bloques Grasas</th>
                            <th data-hide="phone"> Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $rows = 0;
                          foreach($foods as $food): 
                            $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?> 
                          <tr class=<?php echo $class; ?> role="row">
                              <td><?php echo $food->date;?></td>
                              <td><?php echo $food->meal;?></td>
                              <td><?php echo $food->group;?></td>
                              <td><?php echo ($food->brand ==null)? $food->food : $food->food." (".$food->brand.")";?></td>
                              <td><?php echo $food->energy;?></td>
                              <td><?php echo $food->qtty;?></td>
                              <td><?php echo $food->carb;?></td>
                              <td><?php echo $food->fiber;?></td>
                              <td><?php echo $food->protein;?></td>
                              <td><?php echo $food->fat;?></td>
                              <td><?php echo $food->protein_blocks;?></td>
                              <td><?php echo $food->carb_blocks;?></td>
                              <td><?php echo $food->fat_blocks;?></td>
                              <td>
                                <a href='<?php echo base_url(); ?>athlete/foods/view/<?php echo $food->food_id; ?>' class="btn btn-success btn-xs html5history"><i class='fa fa-eye'></i></a>
                                <a href='<?php echo base_url(); ?>athlete/foods/edit/<?php echo $food->id; ?>' class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
                                <a href="#" onclick="DWC(<?php echo $food->id; ?>);" class='btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a>
                              </td>
                          </tr>
                          <?php endforeach ?>
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



<!-- Vendor scripts -->
<script src="<?php echo base_url(); ?>assets/vendor/fooTable/dist/footable.all.min.js"></script>


  <script>

          function DWC(id) {
            swal.fire({
                        title: "Seguro que quieres borrar este registro?",
                        text: "Una vez borrado los cambios no se podrán deshacer!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        cancelButtonText: "Cancelar",
                        confirmButtonText: "Borrar"
                    },
                    function () {
                        goTo('athlete','deleteFoodLog',id)
                        swal("Booyah!");
                    });
        };

</script>

<script>

  var table = $('.footable').footable();


 $('.nav-tabs a').click(function (e) {
       e.preventDefault(); //prevents re-size from happening before tab shown
       $(this).tab('show'); //show tab panel 
       table.trigger('footable_resize'); //fire re-size of footable
  });

$('#date').change(function(e){
    var form = $(this);
    var date = $('#date').val()
    var url = '<?php echo base_url()."athlete/nutrition/log/"; ?>'+ date;

    e.preventDefault();

    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(),
        dataType: "html",

        success: function(data){
            history.pushState(null, null, url);
            $('.content').empty();
            $('.content').html(data);
        },

        error: function() { alert("Error posting feed."); }
   });
});

</script>

<script>

    $(function () {

        /**
         * Flot charts data and options
         */

        var dataSeries = [
            {
                data: <?php echo $energy; ?>, 
                label: "kCal totales",
                color: "#62cb31",
                points: { show: true },
                splines: {
                    show: true,
                    tension: 0.15,
                    lineWidth: 1.5,
                    fill: 0.3
                },
            },
            {
                data: <?php echo $carbs; ?>, 
                label: "kcal carbs",
                color: "#3498db",
                points: { show: true },
                splines: {
                    show: true,
                    tension: 0.15,
                    lineWidth: 1,
                    fill: 0
                },
            },
            {
                data: <?php echo $proteins; ?>, 
                label: "kcal prot",
                color: "#e74c3c",
                points: { show: true },
                splines: {
                    show: true,
                    tension: 0.15,
                    lineWidth: 1,
                    fill: 0
                },
            },
            {
                data: <?php echo $fats; ?>, 
                label: "kcal grasa",
                color: "#ffb606",
                points: { show: true },
                splines: {
                    show: true,
                    tension: 0.15,
                    lineWidth: 1,
                    fill: 0
                },
            }

        ];

        var chartUsersOptions = { 
            grid: {
                tickColor: "#f0f0f0",
                borderWidth: 1,
                borderColor: 'f0f0f0',
                color: '#6a6c6f',
                hoverable: true,
                clickable: true
            },

            xaxis: { mode: "time", timeformat: "%d/%m/%Y" },
        };

        $.plot($("#flot-line-chart"), dataSeries, chartUsersOptions);

    });

</script>
