       <?php 
$html_default = $this->config->item('html_default', 'settings');
if( $html_default['show_header'] === TRUE): 
?>
      <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='/fitbox/athlete' class='html5history'>Inicio</a></li>
                          <li class="active">
                              <span><a href='<?php echo base_url(); ?>athlete/routines' class='html5history'>Gestión de rutinas de entrenamiento</a></span>
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
      
       <div class="row">
            <div class="col-lg-12">
              <div class="hpanel">
                <div class="row show-grid">
                  <div class="col-md-12" style="">
                    <a href='<?php echo base_url(); ?>athlete/routine/add' type="button" class="btn btn-block btn-outline btn-info html5history" >Crear Rutina Personalizada</a>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <?php $this->load->view('backend/messages'); ?>
     <div class="row">
        <div class="col-lg-12" style="">
          <div class="hpanel">
                  <ul class="nav nav-tabs">
                      <li class="active"><a data-toggle="tab" href="#tab-1">Fitbox</a></li>
                      <li ><a data-toggle="tab" href="#tab-2">Personalizadas</a></li>
                      <li ><a data-toggle="tab" href="#tab-3">Publicas</a></li>
                      
                  </ul>
                  <div class="tab-content ">
                      <div id="tab-1" class="tab-pane active">
                          <div class="panel-body">
                              
                                <div class="row">
                                  <div class="col-sm-12">
                                    <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Search in table">
                                    <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="8" data-filter=#filter>
                                      <thead>
                                        <tr role="row">
                                          <th data-toggle="true">Ejercicio</th>
                                          <th >Fecha</th>
                                          <th >Peso (kg)</th>
                                          <th data-hide="phone">Reps</th>
                                          <th data-hide="phone">Toneladas</th>
                                          <th >1RM</th>
                                          <th data-hide="phone">Acciones</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $rows = 0;
                                        foreach($fitbox_routines as $fb): 
                                          $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                                        <tr class=<?php echo $class; ?> role="row">
                                            <td ><?php echo $str->name;?></td>
                                            <td ><?php echo $str->date;?></td>
                                            <td ><?php echo $str->load;?></td>
                                            <td ><?php echo $str->reps;?></td>
                                            <td ><?php echo $str->tons;?></td>
                                            <td ><?php echo $str->RM;?></td>
                                            <td>
                                              <a href='<?php echo base_url(); ?>athlete/routine/adapt/<?php echo $str->id; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
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


                      <div id="tab-2" class="tab-pane">
                          <div class="panel-body">
                              
                                <div class="row">
                                  <div class="col-sm-12">
                                    <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Search in table">
                                    <table id="table2" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="8" data-filter=#filter>
                                      <thead>
                                        <tr role="row">
                                          <th data-toggle="true">Fase</th>
                                          <th >Categoría</th>
                                          <th >Tipo</th>
                                          <th >Nombre</th>
                                          <th data-hide="phone">Desc.</th>
                                          <th data-hide="phone">Acciones</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $rows = 0;
                                        foreach($my_routines as $my): 
                                          $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                                        <tr class=<?php echo $class; ?> role="row">
                                            <td ><?php echo $my->phase;?></td>
                                            <td ><?php echo $my->category;?></td>
                                            <td ><?php echo $my->type;?></td>
                                            <td ><?php echo $my->name;?></td>
                                            <td ><?php echo $my->description;?></td>
                                            <td>
                                              <a href='<?php echo base_url(); ?>athlete/routine/edit/<?php echo $my->id; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
                                              <a class='btn btn-danger btn-xs' href="#" onclick="DWC(<?php echo $my->id; ?>);"><i class='fa fa-trash-o'></i></a>
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

                      <div id="tab-3" class="tab-pane">
                          <div class="panel-body">
                              
                                <div class="row">
                                  <div class="col-sm-12">
                                    <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Search in table">
                                    <table id="table3" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="8" data-filter=#filter>
                                      <thead>
                                        <tr role="row">
                                          <th data-toggle="true">Ejercicio</th>
                                          <th >Fecha</th>
                                          <th >Peso (kg)</th>
                                          <th data-hide="phone">Reps</th>
                                          <th data-hide="phone">Toneladas</th>
                                          <th data-hide="phone">Acciones</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php $rows = 0;
                                        foreach($public_routines as $pb): 
                                          $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                                        <tr class=<?php echo $class; ?> role="row">
                                            <td ><?php echo $spd->name;?></td>
                                            <td ><?php echo $spd->date;?></td>
                                            <td ><?php echo $spd->load;?></td>
                                            <td ><?php echo $spd->reps;?></td>
                                            <td ><?php echo $spd->tons;?></td>
                                            <td>
                                              <a href='<?php echo base_url(); ?>athlete/routine/adapt/<?php echo $spd->id; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
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
      </div>
     </div>


<!-- Vendor scripts -->
<script src="<?php echo base_url(); ?>assets/vendor/fooTable/dist/footable.all.min.js"></script>


  <script>

  var table = $('.footable').footable();


 $('.nav-tabs a').click(function (e) {
       e.preventDefault(); //prevents re-size from happening before tab shown
       $(this).tab('show'); //show tab panel 
       table.trigger('footable_resize'); //fire re-size of footable
  });
</script>

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
                        goTo('athlete','deleteRoutine',id)
                        swal.fire("Booyah!");
                    });
        };

</script>
