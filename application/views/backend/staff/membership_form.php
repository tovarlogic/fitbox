
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
                          <li>
                              <span><a href='<?php echo base_url(); ?>staff/memberships' type="button" class="html5history">Gestión de tarifas</a>
                          </li>
                          <li class="active">
                              <span>Crear tarifa </span>
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
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        <?php echo $page_title;?>
                    </div>
                    <div class="panel-body">
                      <?php $this->load->view('backend/messages'); ?>
                        <?php echo ($action == 'add') ? form_open("staff/membership/add/", array('id' => 'Form')) : form_open("staff/membership/edit/".$membership_id, array('id' => 'Form')); ?>
                            <div class ="row">
                              <div class="form-group col-md-2"><label>Título</label> <?php echo form_input($title);?></div>
                              <div class="form-group col-md-2"><label>Precio</label> <?php echo form_input($price);?></div>
                              <div class="form-group col-md-2"><label>Caducidad</label> <?php echo form_input($days);?></div>
                              <div class="form-group col-md-2"><label>Unidad</label> <?php echo form_dropdown('period', $period_list, ($this->input->post('period'))? $this->input->post('period') : $period_status, $period);?></div>
                              <div class="form-group col-md-2"><label>Recurrente</label> <?php echo form_radio($recurring1); echo " Si "; echo form_radio($recurring2); echo " No "?></div>
                            </div>                             
                             
                            <fieldset>
                              <legend> Servicios y limites de reserva</legend>
                              <div class="row">
                                <div class="form-group col-md-2"><label>Reservas máximas (combinada)</label> <?php echo form_input($max_reservations);?></div>
                              </div>
                              <?php $rows = 0; $qtty = null;
                              foreach($service_options as $key => $value): ?>
                                <?php if($services) { foreach($services as $srv) { if($srv['service_id'] == $key) { $qtty = $srv['qtty'] ; break; } else $qtty = null; } } ?>
                              <div class="row">
                                <div class="form-group">
                                  <label class="col-md-2 control-label"><?php echo $value; ?></label>
                                  <div class="col-md-8">
                                     <input type="hidden" name="service[<?php echo $key; ?>][id]" value="<?php echo $key; ?>" />
                                     <div class="col-md-4" style=""><input type="checkbox" name="service[<?php echo $key; ?>][include]" <?php if($qtty != null) echo "checked=''"; ?> ></div>
                                     <div class="col-md-4" style=""><input type="text" name="service[<?php echo $key; ?>][qtty]" value="<?php if($qtty != null) echo $qtty; ?>" placeholder="max reservas (0 = ilimitado)" <?php if($qtty != null) echo "value='".$qtty."'"; ?> class="form-control"></div>
                                  </div>
                              </div>
                            </div>
                            <?php endforeach; ?>
                            </fieldset>

                            <fieldset>
                              <legend> Restricción horaria </legend>
                              <div class ="row">
                                <div class="form-group col-md-2"><label>Disponible desde</label> <?php echo form_input($available_from);?></div>
                                <div class="form-group col-md-2"><label>Disponible hasta</label> <?php echo form_input($available_to);?></div>
                              </div>
                            </fieldset>

                            <fieldset>
                              <legend> Otros datos </legend>
                              <div class ="row">
                                <div class="form-group col-md-2"><label>Permite probar sin pagar</label> <?php echo form_radio($trial1); echo " Si "; echo form_radio($trial2); echo " No ";?></div>
                                
                                <div class="form-group col-md-2"><label>Plan privado</label> <?php echo form_radio($private1); echo " Si "; echo form_radio($private2); echo " No "?></div>
                                <div class="form-group col-md-2"><label>Validez</label> <?php echo form_radio($deprecated2); echo " Valido "; echo form_radio($deprecated1); echo " Desuso (antiguo) ";?></div>
                                <div class="form-group col-md-2"><label>Activo</label> <?php echo form_radio($active1); echo " Si "; echo form_radio($active2); echo " No "?></div>
                              </div>
                              <div class ="row">
                              <div class="form-group col-md-12"><label>Comentarios</label> <?php echo form_textarea($description);?></div>
                            </div>
                            </fieldset>
                                                     

                            <div class ="row">
                                <?php echo form_submit('submit', 'Guardar plan', 'class="btn btn-xs btn-primary m-t-n-xs"');?></p>
                            </div>
                        <?php echo form_close();?>
                    </div>
                </div>
            </div>


        

<script>
  $('#date_from').datepicker();
  $('#date_to').datepicker();
  $("#limit").TouchSpin({
      verticalbuttons: true
  });
  $("#value").TouchSpin({
      verticalbuttons: true
  });

  $('form').submit(function(e) {
    var form = $(this);
    var url2 = "<?php echo base_url("staff/memberships"); ?>";
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: form.attr("action"),
        data: form.serialize(),
        dataType: "html",
        cache: false,

        success: function(data){
          $('.content').empty();
          $('.content').html(data);
          history.pushState(null, null, url2);
        },

        error: function() { alert("Error posting."); }
   });
    return false;
  });

</script>











      
