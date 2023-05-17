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
                              <span><a href='<?php echo base_url(); ?>staff/services' class='html5history'>Servicios</a></span>
                          </li>
                          <li class="active">
                              <span><?php echo $page_title;?></span>
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
                    <div class="panel-body" id="form">
                        <?php $this->load->view('backend/messages'); ?>
                        <?php echo ($action == 'add') ? form_open("staff/services/add/", array('id' => 'Form')) : form_open("staff/services/edit/".$service_id, array('id' => 'Form')); ?>
                            <input type="hidden" name="changes" value="false" id="changes">
                            <fieldset>
                              <legend> Configuración </legend>
                                <div class ="row">
                                  <div class="form-group col-md-3"><label>Nombre</label> <?php echo form_input($name);?></div>
                                </div>
                                <div class ="row">
                                  <div class="form-group col-md-3"><label>Duración (min)</label> 
                                  <?php echo form_dropdown('interval', $interval_list, $interval['value'], 'class="form-control"');?>
                                  </div>
                                  <div class="form-group col-md-3"><label>Capacidad</label> <?php echo form_input($spaces_available);?> </div>
                                  <div class="form-group col-md-3"><label>Reserva adelantada (minutos)</label> <?php echo form_input($time_before);?> </div>
                                  <div class="form-group col-md-3"><label>Color en calendario</label> 
                                  <?php echo form_dropdown('color_bg', $color_list, $color_bg['value'], 'class="form-control"');?></div>
                                </div>
                                <div class ="row">
                                  <div class="form-group col-md-3"><label>¿Permitir cancelar?</label> 
                                  <?php echo form_checkbox('delBookings', $delBookings, ($delBookings == 'y')? TRUE : FALSE);?></div>
                                  <div class="form-group col-md-3"><label>¿Autoconfirmar?</label> 
                                  <?php echo form_checkbox('autoconfirm', $autoconfirm, ($autoconfirm == 1)? TRUE : FALSE);?></div>
                                  <div class="form-group col-md-3"><label>¿Permitir descuentos?</label>
                                  <?php echo form_checkbox('coupon', $coupon, ($coupon == 1)? TRUE : FALSE);?></div>
                                  <div class="form-group col-md-3"><label>¿Mostrar plazas libres en calendario?</label> 
                                  <?php echo form_checkbox('show_spaces_left', $show_spaces_left, ($show_spaces_left == 1)? TRUE : FALSE);?></div>
                                </div>

                                <div class ="row">
                                    <div class="form-group col-md-4" ><label>Estado</label> 
                                    <?php echo form_dropdown('active',$active_list, $active['value'], 'class="form-control"');?></div>
                                </div>
                            <fieldset>
                            <!-- SCHEDULE -->
                            <fieldset>
                              <legend> Horarios </legend>
                            <div class="row">
                              <div class="col-lg-12" style="">
                                  <div class="hpanel">
                                    <div class="tabs-left">
                                      <ul class="nav nav-tabs">
                                          <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true"><b>Lunes</b></a></li>
                                          <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false"><b>Martes</b></a></li>
                                          <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false"><b>Miércoles</b></a></li>
                                          <li class=""><a data-toggle="tab" href="#tab-4" aria-expanded="false"><b>Jueves</b></a></li>
                                          <li class=""><a data-toggle="tab" href="#tab-5" aria-expanded="false"><b>Viernes</b></a></li>
                                          <li class=""><a data-toggle="tab" href="#tab-6" aria-expanded="false"><b>Sábado</b></a></li>
                                          <li class=""><a data-toggle="tab" href="#tab-7" aria-expanded="false"><b>Domingo</b></a></li>
                                      </ul>

                                      <div class="tab-content ">
                                        <?php
                                          $weekdays = $this->booking_lib->weekdays;
                                          for ($i = 1; $i < 8; $i++) {
                                              $step = $j = 0; $step = 15;
                                              $items = (isset($week[$i]) && (count($week[$i]) > 0)) ? count($week[$i]) : 1;
                                              $active = ($i == 1)? "active" : "";
                                        ?>
                                          <div id="tab-<?php echo $i;?>" class="tab-pane <?php echo $active;?>">
                                              <div class="panel-body">
                                                  <p><strong>Inicio de la actividad (hora : minutos)</strong><p>
                                                  <?php for ($j = 0; $j < $items; $j++) { ?>
                                                  <div class="form-group col-md-8">

                                                        <div class="input-group">
                                                          <input 
                                                            class="schedule input-sm col-md-3 form-control" 
                                                            type="number" min="0" max="23" 
                                                            name="week_from_h_<?php echo $i ?>[]" 
                                                            value="<?php if(isset($week["{$i}"]["{$j}"]['startHH'])){ echo $week["{$i}"]["{$j}"]['startHH'];}else{ echo '' ;} ?>" 
                                                            class="adjStartEnd adj_hrs_0" 
                                                          />
                                                          <span class="input-group-addon">:</span>
                                                          <input  
                                                            class="schedule input-sm col-md-3 form-control" 
                                                            type="number" min="0" max="59" 
                                                            name="week_from_m_<?php echo $i ?>[]" 
                                                            value="<?php if(isset($week["{$i}"]["{$j}"]['startMM'])){ echo $week["{$i}"]["{$j}"]['startMM'];}else{ echo '' ;} ?>" 
                                                            class="adjStartEnd adj_mins_0" 
                                                          />

                                                        </div>
                                                        <div class="input-group"> 
                                                        <input  
                                                            class="schedule input-sm col-md-3 form-control" 
                                                            type="text" size="25"
                                                            name="coach_<?php echo $i ?>[]" 
                                                            placeholder="entrenador"
                                                            value="<?php if(isset($week["{$i}"]["{$j}"]['coach'])){ echo $week["{$i}"]["{$j}"]['coach'];}else{ echo '' ;} ?>"
                                                        />
                                                      </div>
                                                      <?php if($j > 0): ?>
                                                        <a href="javascript:;" class="adj_SE_remove">eliminar</a>
                                                      <?php else: ?>
                                                          <a href="javascript:;" class="adj_SE_clear">vaciar</a>
                                                          <?php endif;?>                                         
                                                  
                                                 </div> 
                                                <?php } ?>
                                                <a href="javascript:;" onclick="addTime(<?php echo $i ?>,this)" class="buttonAddSmall"><span>añadir</span></a>
                                              </div>
                                          </div>
                                        <?php } ?>  
                                      </div>
                                    </div>
                                  </div>
                              </div>
                            </div>
                          </fieldset>

                            <div class ="row">
                                <?php echo form_submit('submit', 'Guardar servicio', 'class="btn btn-xs btn-primary m-t-n-xs"');?></p>
                            </div>
                        <?php echo form_close();?>
                        <script>

                        $('form').submit(function(e) {
                            var form = $(this);
                            var url2 = "<?php echo base_url("staff/services"); ?>";
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

function addTime(week_number,el){
     $.ajax({
        url : '<?php echo base_url()."staff/getServicePartialform/"; ?>',
        type : 'POST'  ,
        data : {week: week_number},
        success : function(data){
            $(el).before(data);
            removeAndClear();
            changeDetect();
        }
     });
};

function removeAndClear(){
  $('.adj_SE_remove').click(function(){
      $(this).parent().remove();
      return false;
  });

  $('.adj_SE_clear').click(function(){
    $(this).parent().find("input").each(function(){$(this).val(""); });
    return false;
  });
};

function changeDetect(){
  $('.schedule').on('input', function() { 
    $("#changes").val(true);
      console.log("change input");
      return false;
  });
};

  removeAndClear();
  changeDetect();

</script>

                 













      
