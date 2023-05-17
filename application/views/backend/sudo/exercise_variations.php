      <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='<?php echo base_url(); ?>sudo' class='html5history'>Inicio</a></li>
                          <li class="active">
                              <span><?php echo $page_title;?></span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE EJERCICIOS
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>

      <div class="row">
        <div style="" class="col-lg-12">
            <div class="hpanel">
              <div class="panel-heading">
                  <div class="pull-right">
                    <a href='<?php echo base_url(); ?>sudo/exercise_variation/add' class="btn btn-info btn-primary html5history"><i class="fa fa-plus"></i></a>
                </div>
                  <?php echo $page_title;?>
              </div>
              <div class="panel-body">
                
                  <div class="row">
                    <div class="col-sm-12">
                      <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Búsqueda">
                      <?php $this->load->view('backend/messages'); ?>
                      <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="20" data-filter=#filter>
                        <thead>
                          <tr role="row">
                            <th data-toggle="true">Nombre</th>
                            <th >Basado en</th>
                            <th data-hide="phone">Adaptación</th>
                            <th data-hide="phone">Músculos primarios</th>
                            <th data-hide="phone">Músculos secundarios</th>
                            <th data-hide="phone,tablet">Movimiento</th>
                            <th data-hide="phone,tablet">Contracción</th>
                            <th data-hide="phone,tablet">Mecánica</th>
                            <th data-hide="phone,tablet">Tipo</th>
                            <th data-hide="phone,tablet">Material</th>
                            <th data-hide="phone,tablet">Registro</th>
                            <th data-hide="phone">Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $rows = 0;
                          if(is_array($variations) AND sizeof($variations) > 0):
                            foreach($variations as $var): 
                              $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                            <tr class=<?php echo $class; ?> role="row">
                                <td class=""><?php echo $var['name']; echo ($var['short_name'] != '')? ' ('.$var['short_name'].')' : '';?></td>
                                <td class=""><?php 
                                  $i = 0;
                                  foreach ($var['basic'] as $rel) {
                                    $i++;
                                    echo ($i > 1)? ', ' : '';
                                    echo $rel['name'];
                                    echo ($rel['short_name'] != '')? ' ('.$rel['short_name'].')' : '';
                                    
                                  }
                                ?></td>
                                <td class=""><?php 
                                  $i = 0;
                                  if(is_array($var['target']) AND sizeof($var['target']) > 0)
                                  {
                                    foreach ($var['target'] as $rel) {
                                      $i++;
                                      echo ($i > 1)? ', ' : '';
                                      echo $rel['name']; 
                                    }
                                  }
                                ?></td>
                                <td class=""><?php 
                                  $i = 0;
                                  if(is_array($var['muscles_primary']) AND sizeof($var['muscles_primary']) > 0)
                                  {
                                    foreach ($var['muscles_primary'] as $rel) {
                                      $i++;
                                      echo ($i > 1)? ', ' : '';
                                      echo $rel['name']; 
                                    }
                                  }
                                ?></td>
                                <td class=""><?php 
                                  $i = 0;
                                  if(is_array($var['muscles_secondary']) AND sizeof($var['muscles_secondary']) > 0)
                                  {
                                    foreach ($var['muscles_secondary'] as $rel) {
                                      $i++;
                                      echo ($i > 1)? ', ' : '';
                                      echo $rel['name']; 
                                    }
                                  }
                                ?></td>
                                <td class=""><?php 
                                  $i = 0;
                                  if(is_array($var['movement']) AND sizeof($var['movement']) > 0)
                                  {
                                    foreach ($var['movement'] as $rel) {
                                      $i++;
                                      echo ($i > 1)? ', ' : '';
                                      echo $rel['name']; 
                                    }
                                  }
                                ?></td>
                                <td class=""><?php 
                                  $i = 0;
                                  if(is_array($var['contraction']) AND sizeof($var['contraction']) > 0)
                                  {
                                    foreach ($var['contraction'] as $rel) {
                                      $i++;
                                      echo ($i > 1)? ', ' : '';
                                      echo $rel['name']; 
                                    }
                                  }
                                ?></td>
                                <td class=""><?php 
                                  $i = 0;
                                  if(is_array($var['mechanic']) AND sizeof($var['mechanic']) > 0)
                                  {
                                    foreach ($var['mechanic'] as $rel) {
                                      $i++;
                                      echo ($i > 1)? ', ' : '';
                                      echo $rel['name']; 
                                    }
                                  }
                                ?></td>
                                <td class=""><?php 
                                  $i = 0;
                                  if(is_array($var['type']) AND sizeof($var['type']) > 0)
                                  {
                                    foreach ($var['type'] as $rel) {
                                      $i++;
                                      echo ($i > 1)? ', ' : '';
                                      echo $rel['name']; 
                                      echo ($rel['short_name'] != '')? ' ('.$rel['short_name'].')' : '';
                                    }
                                  }
                                ?></td>
                                <td class=""><?php 
                                  $i = 0;
                                  if(is_array($var['material']) AND sizeof($var['material']) > 0)
                                  {
                                    foreach ($var['material'] as $rel) {
                                      $i++;
                                      echo ($i > 1)? ', ' : '';
                                      echo $rel['name']; 
                                      echo ($rel['short_name'] != '')? ' ('.$rel['short_name'].')' : '';
                                    }
                                  }
                                ?></td>
                                <td class=""><?php
                                  $i = 0;
                                  if($var['reps'] != 0 ) { echo 'Reps'; $i++; }
                                  if($var['load'] != 0 ) { if($i>0) { echo ', ';} echo 'Load'; }
                                  if($var['distance'] != 0 ) { if($i>0) { echo ', ';} echo 'Dist.'; }
                                  if($var['height'] != 0 ) { if($i>0) { echo ', ';} echo 'Height'; }
                                  if($var['time'] != 0 ) { if($i>0) { echo ', ';} echo 'Time'; }
                                  if($rvarel['energy'] != 0 ) { if($i>0) { echo ', ';} echo 'Energy.'; }
                                  if($var['tons'] != 0 ) { if($i>0) { echo ', ';} echo 'Ton'; }
                                  if($var['work'] != 0 ) { if($i>0) { echo ', ';} echo 'Work'; }
                                ?></td>
                                <td>
                                  <a href='<?php echo base_url(); ?>sudo/exercise_variation/edit/<?php echo $var['id']; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
                                  <a href='<?php echo base_url(); ?>sudo/deleteExerciseVariation/<?php echo $var['id']; ?>' type="button" class="btn btn-danger btn-xs html5history_warning"><i class='fa fa-trash-o'></i></a>
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

  var table = $('.footable').footable();

 $('.nav-tabs').on( 'shown.bs.tab', function () {
    $('.footable').trigger('footable_resize');
});

</script>