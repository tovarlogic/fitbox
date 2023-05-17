      <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='<?php echo base_url(); ?>sudo' class='html5history'>Inicio</a></li>
                          <li>Ejercicios</li>
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
                    <a href='<?php echo base_url(); ?>sudo/exercise_type/add' class="btn btn-info btn-primary html5history"><i class="fa fa-plus"></i></a>
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
                            <th data-hide="phone,tablet">nombre corto</th>
                            <th data-hide="phone">Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $rows = 0;
                          if(sizeof($types) > 0):
                            foreach($types as $typ): 
                              $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                            <tr class=<?php echo $class; ?> role="row">
                                <td class=""><?php echo $typ->name;?></td>
                                <td class=""><?php echo $typ->short_name;?></td>
                                <td>
                                  <a href='<?php echo base_url(); ?>sudo/exercise_type/edit/<?php echo $typ->id; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
                                  <a href='<?php echo base_url(); ?>sudo/deleteExerciseType/<?php echo $typ->id; ?>' type="button" class="btn btn-danger btn-xs html5history_warning"><i class='fa fa-trash-o'></i></a>
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