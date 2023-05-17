
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
                              <span><a href='<?php echo base_url(); ?>staff/coupons' class='html5history'>Gestión de descuentos</a></span>
                          </li>
                          <li class="active">
                              <span>Editar cupón </span>
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
                        <?php echo ($action == 'add') ? form_open("staff/coupon/add/", array('id' => 'Form')) : form_open("staff/coupon/edit/".$coupon_id, array('id' => 'Form')); ?>
                            <div class ="row">
                              <div class="form-group col-md-6"><label>Título</label> <?php echo form_input($title);?></div>
                              <div class="form-group col-md-6"><label>Código</label> <?php echo form_input($code);?></div>
                            </div>
                            <div class ="row">
                              <div class="form-group col-md-6"><label>Cantidad descuento</label> <?php echo form_input($value);?></div>
                              <div class="form-group col-md-6"><label>Tipo descuento</label> 
                              <?php echo form_dropdown('type', $type_list, ($this->input->post('type'))? $this->input->post('type') : $type_status, 'class="form-control"');?></div>
                            </div>
                            <div class ="row">
                              <div class="form-group col-md-6"><label>Validez desde (00:00 horas)</label> <?php echo form_input($date_from);?></div>
                              <div class="form-group col-md-6"><label>Validez hasta (23:59 horas)</label> <?php echo form_input($date_to);?></div>
                            </div>
                            <div class ="row">
                              <div class="form-group col-md-6"><label>Límite (0 = Ilimitado)</label> <?php echo form_input($limit);?></div>
                              <div class="form-group col-md-6"><label>Servicio</label> 
                                <?php echo form_dropdown('services', $services_list, ($this->input->post('services'))? $this->input->post('services') : $services_status, 'class="form-control"');?></div>
                            </div>
                            
                            <div class ="row">
                                <div class="form-group col-md-4" ><label>Estado</label> 
                                <?php echo form_dropdown('status',$status_list, ($this->input->post('status'))? $this->input->post('status') : $status_status, 'class="form-control"');?></div>
                            </div>
                            

                            <div class ="row">
                                <?php echo form_submit('submit', 'Guardar cupón', 'class="btn btn-xs btn-primary m-t-n-xs"');?></p>
                            </div>
                        <?php echo form_close();?>
                        <script type="text/javascript">
                          $('form').submit(function(e) {
                            var form = $(this);
                            var url2 = "<?php echo base_url("staff/coupons"); ?>";
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


</script>

                 













      
