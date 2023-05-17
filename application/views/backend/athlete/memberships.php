       <?php 
$html_default = $this->config->item('html_default', 'settings');
if( $html_default['show_header'] === TRUE): 
?>
      <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='<?php echo base_url(); ?>athlete' class='html5history'>Inicio</a></li>
                          <li class="active">
                              <span>Planes </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      PLANES CONTRATADOS
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>
      <?php endif ?>

      <div class="row">
          <div class="col-lg-12">
            <div class="hpanel">
                <a href='<?php echo base_url(); ?>athlete/userMembership/add/<?php echo $this->session->userdata('user_id'); ?>' type="button" class="btn btn-block btn-outline btn-info html5history">Dar de alta en plan</a>
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
                  Planes contratados
              </div>
              <div class="panel-body"> 
                  <div class="row">
                    <div class="col-sm-12">
                      <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Search in table">
                      <?php $this->load->view('backend/messages'); ?>
                      <table id="table1" class="footable table table-stripped toggle-arrow-tiny table-hover" data-page-size="12" data-filter=#filter>
                        <thead>
                          <tr role="row">
                            <th data-toggle="true">Box</th>
                            <th data-toggle="true">Título</th>
                            <th >Precio</th>
                            <th >Periodicidad</th>
                            <th >Estado</th>
                            <th >Fecha Alta</th>
                            <th >Fecha caducidad</th>
                            <th >Fecha baja</th>
                            <th data-hide="phone">Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $rows = 0;
                          if(isset($memberships) && sizeof($memberships) > 0):
                            foreach($memberships as $membership): 
                              $rows++; if ($rows % 2 == 0) {$class ='even';}else{$class ='odd';}?>
                              <tr class=<?php echo $class; ?> role="row">
                                  <td ><?php echo $membership->name;?></td>
                                  <td ><?php echo $membership->title;?></td>
                                  <td ><?php echo $membership->price." €";?></td>
                                  <td >
                                    <?php  
                                      if($membership->period == 'M'){ 
                                        if($membership->days == 1) echo 'mensual';
                                        else if($membership->days == 2) echo 'bimensual';
                                        else if($membership->days == 3) echo 'trimestral';
                                        else if($membership->days == 4) echo 'cuatrimestral';
                                        else if($membership->days == 6) echo 'semestral';
                                        else { echo $membership->days." "; echo ($membership->days == 1)? $this->lang->line('date_month') : $this->lang->line('date_months'); }
                                      }
                                      elseif($membership->period == 'D'){ 
                                        if($membership->days == 1) echo 'diaria';
                                        else echo $membership->days." ".$this->lang->line('date_days');
                                      }
                                      elseif($membership->period == 'Y'){ 
                                        if($membership->days == 1) echo "anual";
                                        else  echo $membership->days." ".$this->lang->line('date_years');
                                      }
                                      ?>
                                  </td>
                                  <td><?php 
                                    if ($membership->status == 'y'){ echo "<i class='fa text-success fa-check'> Activo </i>";}
                                    else if ($membership->status == 'p') { echo "<i class='fa fa-question text-primary-2'> Pendiente pago</i>";}
                                    else if ($membership->status == 'n') { echo "<i class='fa text-warning fa-minus'> Caducado</i>";} 
                                    else if ($membership->status == 'c') { echo "<i class='fa text-danger fa-minus'> De baja</i>";} 
                                    ?>
                                  </td>
                                  <td> <?php echo $membership->created_on; ?></td>
                                  <td> <?php echo $membership->mem_expire; ?></td>
                                  <td> <?php 
                                    if ($membership->status == 'c') echo $membership->created_on; 
                                    else echo "-"; 
                                    ?>
                                  </td>
                                  
                                  <td>
                                    <?php if ($membership->status == 'y' OR $membership->status == 'p' OR $membership->status == 'n') : ?>
                                    <a href='<?php echo base_url(); ?>athlete/membershipPayment/add/<?php echo $membership->id; ?>' type="button" class="btn btn-success btn-xs html5history"><i class='fa fa-euro'></i> Renovar/Pagar</a>

                                    <?php if ($membership->status == 'y' OR $membership->status == 'n') : ?>
                                    <a class='btn btn-warning btn-xs' href="#" onclick="goTo('athlete','userMembership/change',<?php echo $membership->id;?>);"><i class='fa fa-pencil'> Cambiar</i></a>
                                    <?php endif; ?>
                                    <a href='<?php echo base_url(); ?>athlete/userMembership/cancel/<?php echo $this->session->userdata('user_id'); ?>' type="button" class="btn btn-danger btn-xs html5history"><i class='fa fa-times'> 
                                    Baja</i></a>
                                    <?php endif; ?>

                                  </td>
                              </tr>
                          <?php endforeach ?>
                          <?php endif?>
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
<script src="<?php echo base_url(); ?>assets/vendor/jquery/dist/jquery.min.js"></script>
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
                        goTo('athlete','deleteMembership',id)
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
</script>
