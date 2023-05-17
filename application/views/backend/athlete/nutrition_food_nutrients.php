<div class="col-lg-12" style="">

            <div class="text-center m-b-xl font-uppercase">
                <h3><?php echo $food->Shrt_Desc; ?> (100 gramos)</h3>
            </div>

            <div class="row">
                <div class="col-lg-3" style="">
                    <div class="hpanel stats">
                        <div class="panel-heading hbuilt text-center">
                            <h4 class="font-bold">Macros (kCal)</h4>
                        </div>
                        <div class="panel-body h-200">
                            <div class="stats-icon pull-right">
                                
                            </div>
                            <div class="clearfix"></div>
                            <div class="flot-chart">
                                <div class="flot-chart-content" id="flot-pie-chart" style="height: 112px; padding: 0px; position: relative;">
                                    <canvas class="flot-base" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 346px; height: 112px;" width="346" height="112"></canvas>
                                    <canvas class="flot-overlay" style="direction: ltr; position: absolute; left: 0px; top: 0px; width: 346px; height: 112px;" width="346" height="112"></canvas>
                                    <div class="legend">
                                      <div style="position: absolute; width: 47px; height: 62px; top: 5px; right: 5px; background-color: rgb(255, 255, 255); opacity: 0.85;"> 
                                      </div>
                                      <table style="position:absolute;top:5px;right:5px;;font-size:smaller;color:#545454">
                                        <tbody>
                                          <tr>
                                            <td class="legendColorBox">
                                              <div style="border:1px solid #ccc;padding:1px">
                                                <div style="width:4px;height:0;border:5px solid #62cb31;overflow:hidden">
                                                  
                                                </div>
                                              </div>
                                            </td>
                                            <td class="legendLabel">Carb</td>
                                          </tr>
                                          <tr>
                                            <td class="legendColorBox"><div style="border:1px solid #ccc;padding:1px">
                                              <div style="width:4px;height:0;border:5px solid #A4E585;overflow:hidden">
                                                
                                              </div>
                                            </div>
                                          </td>
                                          <td class="legendLabel">Prot</td></tr><tr><td class="legendColorBox">
                                            <div style="border:1px solid #ccc;padding:1px">
                                              <div style="width:4px;height:0;border:5px solid #368410;overflow:hidden">
                                                
                                              </div>
                                            </div>
                                          </td>
                                          <td class="legendLabel">Grasa</td>
                                        </tr>
                                        <tr>
                                          <td class="legendColorBox">
                                            <div style="border:1px solid #ccc;padding:1px">
                                              <div style="width:4px;height:0;border:5px solid #8DE563;overflow:hidden">
                                                
                                              </div>
                                            </div>
                                          </td>
                                          <td class="legendLabel">Data 4</td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer contact-footer">
                            <div class="row">
                                <div class="col-md-4 border-right" style=""> <div class="contact-stat"><span>Carb </span> <strong><?php echo $carbs; ?> %</strong></div> </div>
                                <div class="col-md-4 border-right" style=""> <div class="contact-stat"><span>Prot </span> <strong><?php echo $proteins; ?> %</strong></div> </div>
                                <div class="col-md-4" style=""> <div class="contact-stat"><span>Grasa </span> <strong><?php echo $fats; ?> %</strong></div> </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3" style="">
                    <div class="hpanel plan-box hblue active">
                        <div class="panel-heading hbuilt text-center">
                            <h4 class="font-bold">Carbohidratos</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td>
                                        <b>Total</b> <span class="text-right"><?php echo round($food->Carbohydrt_g,1); ?> gr + (fibra) <?php echo round($food->Fiber_TD_g,1); ?> gr </span>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        Azucares <span class="text-right"><?php echo round($food->Sugar_Tot_g,1); ?> gr </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i></i> Almidones
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i></i> <b>Fibra</b> <?php echo round($food->Fiber_TD_g,1); ?> gr
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3" style="">
                    <div class="hpanel plan-box hred active">
                        <div class="panel-heading hbuilt text-center">
                            <h4 class="font-bold">Proteinas</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td>
                                        <b>Total</b> <?php echo round($food->Protein_g,1); ?> gr
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3" style="">
                    <div class="hpanel plan-box hyellow active">
                        <div class="panel-heading hbuilt text-center">
                            <h4 class="font-bold">Grasas</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td>
                                        <b>Total</b> <?php echo round($food->Lipid_Tot_g,1); ?> gr
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        Saturadas <?php echo round($food->FA_Sat_g,1); ?> gr
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Monoinsaturadas <?php echo round($food->FA_Mono_g,1); ?> gr
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Polyinsaturadas <?php echo round($food->FA_Poly_g,1); ?> gr 
                                        <br>
                                            &nbsp;&nbsp;&nbsp; &omega; 3 <br>
                                            &nbsp;&nbsp;&nbsp; &omega; 6 
                                    </td>
                                </tr>
                                </tbody>
                            </table>
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
                        <div class="col-lg-4">
                          <div class="list-item-container">
                              <div class="no-margins list-item">
                                  <small><b>Calcio</b> <?php echo round($nutrient_stats['Calcium_mg'],1); ?>mg </small> <div class="pull-right font-bold"> <small>(<?php echo round($DV['Calcium_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Calcium_mg']<=25) $color = 'danger'; else if($DV['Calcium_mg']<=50) $color = 'warning'; else if($DV['Calcium_mg']<=75) $color = 'info'; else if($DV['Calcium_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Calcium_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Hierro</b> <?php echo round($nutrient_stats['Iron_mg'],1); ?>mg</small>  <div class="pull-right font-bold"> <small>(<?php echo round($DV['Iron_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Iron_mg']<=25) $color = 'danger'; else if($DV['Iron_mg']<=50) $color = 'warning'; else if($DV['Iron_mg']<=75) $color = 'info'; else if($DV['Iron_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Iron_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Magnesio</b> <?php echo round($nutrient_stats['Magnesium_mg'],1); ?>mg</small>   <div class="pull-right font-bold"> <small>(<?php echo round($DV['Magnesium_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Magnesium_mg']<=25) $color = 'danger'; else if($DV['Magnesium_mg']<=50) $color = 'warning'; else if($DV['Magnesium_mg']<=75) $color = 'info'; else if($DV['Magnesium_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Magnesium_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Fósforo</b> <?php echo round($nutrient_stats['Phosphorus_mg'],1); ?>mg</small>   <div class="pull-right font-bold"> <small>(<?php echo round($DV['Phosphorus_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
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
                                  <small><b>Potasio</b> <?php echo round($nutrient_stats['Potassium_mg'],1); ?>mg</small>  <div class="pull-right font-bold"> <small>(<?php echo round($DV['Potassium_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Potassium_mg']<=25) $color = 'danger'; else if($DV['Potassium_mg']<=50) $color = 'warning'; else if($DV['Potassium_mg']<=75) $color = 'info'; else if($DV['Potassium_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Potassium_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Sodio</b> <?php echo round($nutrient_stats['Sodium_mg'],1); ?>mg</small>  <div class="pull-right font-bold"> <small>(<?php echo round($DV['Sodium_mg']); ?>% CMDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Sodium_mg']<=25) $color = 'success'; else if($DV['Sodium_mg']<=50) $color = 'info'; else if($DV['Sodium_mg']<=75) $color = 'warning'; else if($DV['Sodium_mg']>75) $color = 'danger'; ?>
                                    <div style="width: <?php echo round($DV['Sodium_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Zinc</b>  <?php echo round($nutrient_stats['Zinc_mg'],1); ?>mg</small>  <div class="pull-right font-bold"> <small>(<?php echo round($DV['Zinc_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
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
                                  <small><b>Cobre</b>  <?php echo round($nutrient_stats['Copper_mg'],1); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Copper_mg'],1); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Copper_mg']<=25) $color = 'danger'; else if($DV['Copper_mg']<=50) $color = 'warning'; else if($DV['Copper_mg']<=75) $color = 'info'; else if($DV['Copper_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Copper_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Manganeso</b> <?php echo round($nutrient_stats['Manganese_mg'],1); ?>mg</small>   <div class="pull-right font-bold"> <small>(<?php echo round($DV['Manganese_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Manganese_mg']<=25) $color = 'danger'; else if($DV['Manganese_mg']<=50) $color = 'warning'; else if($DV['Manganese_mg']<=75) $color = 'info'; else if($DV['Manganese_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Manganese_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Selenio</b>  <?php echo round($nutrient_stats['Selenium_ug'],1); ?>ug</small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Selenium_ug']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
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
                        <div class="col-lg-4">
                          <div class="list-item-container">
                              <div class="no-margins list-item">
                                  <small><b>Vit A</b> <?php echo round($nutrient_stats['Vit_A_RAE'],1); ?>ug </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_A_RAE']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                    <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_A_RAE']<=25) $color = 'danger'; else if($DV['Vit_A_RAE']<=50) $color = 'warning'; else if($DV['Vit_A_RAE']<=75) $color = 'info'; else if($DV['Vit_A_RAE']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_A_RAE']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Vit C</b>  <?php echo round($nutrient_stats['Vit_C_mg'],1); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_C_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_C_mg']<=25) $color = 'danger'; else if($DV['Vit_C_mg']<=50) $color = 'warning'; else if($DV['Vit_C_mg']<=75) $color = 'info'; else if($DV['Vit_C_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_C_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Vit D</b> <?php echo round($nutrient_stats['Vit_D_ug'],1); ?>ug </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_D_ug']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_D_ug']<=25) $color = 'danger'; else if($DV['Vit_D_ug']<=50) $color = 'warning'; else if($DV['Vit_D_ug']<=75) $color = 'info'; else if($DV['Vit_D_ug']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_D_ug']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Vit E</b> <?php echo round($nutrient_stats['Vit_E_mg'],1); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_E_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_E_mg']<=25) $color = 'danger'; else if($DV['Vit_E_mg']<=50) $color = 'warning'; else if($DV['Vit_E_mg']<=75) $color = 'info'; else if($DV['Vit_E_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_E_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>

                              <div class="no-margins list-item">
                                  <small><b>Acid. Pantoténico (B5)</b>  <?php echo round($nutrient_stats['Panto_Acid_mg'],1); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Panto_Acid_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
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
                                  <small><b>Vit K</b>  <?php echo round($nutrient_stats['Vit_K_ug'],1); ?>ug </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_K_ug']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_K_ug']<=25) $color = 'danger'; else if($DV['Vit_K_ug']<=50) $color = 'warning'; else if($DV['Vit_K_ug']<=75) $color = 'info'; else if($DV['Vit_K_ug']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_K_ug']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Vit B6</b>  <?php echo round($nutrient_stats['Vit_B6_mg'],1); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_B6_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Vit_B6_mg']<=25) $color = 'danger'; else if($DV['Vit_B6_mg']<=50) $color = 'warning'; else if($DV['Vit_B6_mg']<=75) $color = 'info'; else if($DV['Vit_B6_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Vit_B6_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Tiamina (B1)</b> <?php echo round($nutrient_stats['Thiamin_mg'],1); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Thiamin_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
                                  <div class="progress m-t-xs full progress-small">
                                  <?php 
                                    $color = 'danger'; 
                                    if($DV['Thiamin_mg']<=25) $color = 'danger'; else if($DV['Thiamin_mg']<=50) $color = 'warning'; else if($DV['Thiamin_mg']<=75) $color = 'info'; else if($DV['Thiamin_mg']>75) $color = 'success'; ?>
                                    <div style="width: <?php echo round($DV['Thiamin_mg']); ?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class=" progress-bar progress-bar-<?php echo $color;?>"></div>
                                  </div>
                              </div>
                              <div class="no-margins list-item">
                                  <small><b>Riboflavina (B2)</b>  <?php echo round($nutrient_stats['Riboflavin_mg'],1); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Riboflavin_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
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
                                  <small><b>Niacina (B3)</b>  <?php echo round($nutrient_stats['Niacin_mg'],1); ?>mg </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Niacin_mg']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
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
                                  <small><b>Vit B12</b> <?php echo round($nutrient_stats['Vit_B12_ug'],1); ?>ug </small><div class="pull-right font-bold"> <small>(<?php echo round($DV['Vit_B12_ug']); ?>% CDR)</small><i class="fa fa-level-up text-success"></i></div>
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

<script>
  $(function () {
      /**
         * Pie Chart Data
         */
        var pieChartData = [
            { label: "Carb", data: <?php echo $carbs; ?>, color: "#3498db", },
            { label: "Prot", data: <?php echo $proteins; ?>, color: "#e74c3c", },
            { label: "Grasa", data: <?php echo $fats; ?>, color: "#ffb606", }
        ];

        /**
         * Pie Chart Options
         */
        var pieChartOptions = {
            series: {
                pie: {
                    show: true
                }
            },
            grid: {
                hoverable: true
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false
            }
        };

        $.plot($("#flot-pie-chart"), pieChartData, pieChartOptions);

  });
</script>