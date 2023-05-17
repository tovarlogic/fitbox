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
                      GESTIÓN DE SERVICIOS
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
                    <a href='<?php echo base_url(); ?>staff/services/add' class="btn btn-info btn-primary html5history"><i class="fa fa-plus"></i></a>
                </div>
                  Servicios del Box
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
                            <th data-hide="phone,tablet">Tipo</th>
                            <th >Capacidad</th>
                            <th >Duración</th>
                            <th >Estado</th>
                            <th data-hide="phone,tablet">Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $rows = 0;
                          if(!empty($services)):
                          foreach($services as $service): 
                            $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                          <tr class=<?php echo $class; ?> role="row">
                              <td ><?php echo $service->name;?></td>
                              <td ><?php echo ($service->type == 't') ? "Por horas " : "Dias";?></td>
                              <td ><?php echo ($service->spaces_available == 0) ? "Ilimitados" : $service->spaces_available;?></td>
                              <td><?php echo $service->interval." min";?></td>
                              <td><?php 
                                if ($service->active == '1'){ echo "<i class='fa success fa-check'> Activo </i>";}
                                else{ echo "<i class='fa fa-minus'> Inactivo</i>";} ?>
                              </td>
                              <td>
                                <a href='<?php echo base_url(); ?>staff/services/edit/<?php echo $service->id; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
                                <a href='<?php echo base_url(); ?>staff/services/delete/<?php echo $service->id; ?>' type="button" class="btn btn-danger btn-xs html5history_warning_nourl"><i class='fa fa-trash-o'></i></a>

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

 $('.nav-tabs').on( 'shown.bs.tab', function () {
    $('.footable').trigger('footable_resize');
});

</script>
