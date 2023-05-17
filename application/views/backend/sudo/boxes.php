      <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='<?php echo base_url(); ?>sudo' class='html5history'>Inicio</a></li>
                          <li>Boxes</li>
                          <li class="active">
                              <span><?php echo $page_title;?></span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE BOXES
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
                    <a href='<?php echo base_url(); ?>sudo/box/add' class="btn btn-info btn-primary html5history"><i class="fa fa-plus"></i></a>
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
                            <th>Estado</th>
                            <th>Desde</th>
                            <th data-hide="phone">Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $rows = 0;
                          if(sizeof($boxes) > 0):
                            foreach($boxes as $bx): 
                              $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                            <tr class=<?php echo $class; ?> role="row">
                                <td class=""><?php echo $bx->name.' ('.$bx->slug.')';?></td>
                                <td>
                                  <?php   
                                  if ($bx->status == '0'){ echo "<i class='fa fa-minus text-warning'>Inactivo</i>";
                                    }elseif($bx->status == '1'){ echo "<i class='fa fa-check text-success'> Activo</i>"; 
                                    }else{ echo "<i class='fa fa-clock-o text-primary-2'>Indefinido</i>"; }
                                  ?>                              
                                </td>
                                <td>
                                  <?php echo date('Y-m-d', strtotime($bx->created_on)); ?>
                                </td>
                                <td>
                                  <a href='<?php echo base_url(); ?>sudo/box/edit/<?php echo $bx->id; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
                                  <a href='<?php echo base_url(); ?>sudo/deleteBox/<?php echo $bx->id; ?>' type="button" class="btn btn-danger btn-xs html5history_warning"><i class='fa fa-trash-o'></i></a>
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