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
                      GESTIÓN DE DESCUENTOS
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>
<?php endif ?> 
 
      <div class="row">
            <div class="col-lg-12">
              <div class="hpanel">
                <a href='<?php echo base_url(); ?>staff/coupon/add' type="button" class='btn btn-block btn-outline btn-info html5history'>Nuevo cupón de descuento</a>

              </div>
            </div>
        </div>

      <div class="row">
        <div style="" class="col-lg-12">
            <div class="hpanel">
              <div class="panel-heading">
                  <div class="panel-tools">
                      <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                      <a class="closebox"><i class="fa fa-times"></i></a>
                  </div>
                  Descuentos del Box
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
                            <th data-hide="phone,tablet">Código</th>
                            <th >Descuento</th>
                            <th >Servicio</th>
                            <th data-hide="phone,tablet">Inicio validez</th>
                            <th data-hide="phone,tablet">Fin validez</th>
                            <th data-hide="phone">Max usos</th>
                            <th data-hide="phone">Usados</th>
                            <th >Estado</th>
                            <th data-hide="phone">Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $rows = 0;
                          foreach($coupons as $coupon): 
                            $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                          <tr class=<?php echo $class; ?> role="row">
                              <td class=""><?php echo $coupon->title;?></td>
                              <td class=""><?php echo $coupon->code;?></td>
                              <td class="sorting_1"><?php 
                                  echo $coupon->value; echo ($coupon->type == 'rel') ? "% " : "€";?>
                              </td>
                              <td><?php echo $coupon->services;?></td>
                              <td><?php echo $coupon->dateFrom;?></td>
                              <td><?php echo $coupon->dateTo;?></td>
                              <td><?php echo ($coupon->limit == '0') ? "Ilimitado" : $coupon->limit;?></td>
                              <td><?php echo $coupon->counter;?></td>
                              <td><?php 
                                if ($coupon->status == '1'){ echo "<i class='fa success fa-check'> Activo </i>";}
                                else{ echo "<i class='fa fa-minus'> Inactivo</i>";} ?>
                              </td>
                              <td>
                                <a href='<?php echo base_url(); ?>staff/coupon/edit/<?php echo $coupon->id; ?>' type="button" class="btn btn-warning btn-xs html5history"><i class='fa fa-pencil'></i></a>
                                <a href='<?php echo base_url(); ?>staff/deleteCoupon/<?php echo $coupon->id; ?>' type="button" class="btn btn-danger btn-xs html5history_warning"><i class='fa fa-trash-o'></i></a>
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

<script>

  var table = $('.footable').footable();

 $('.nav-tabs').on( 'shown.bs.tab', function () {
    $('.footable').trigger('footable_resize');
});

</script>