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
                          <li>
                              <span><a href='<?php echo base_url(); ?>athlete/PRs' class='html5history'>Gestión marcas personales</a></span>
                          </li>
                          <li class="active">
                              <span>Registro de PRs </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE RECORDS PERSONALES
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>
      <?php endif ?>

      <div class="content">

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
                        <?php echo form_open("athlete/pr/edit/".$id); ?>
                            <div class ="row">
                              <div class="form-group col-md-3"><label>Ejercicio</label><?php echo form_dropdown('excercise_id', $excercise_list, ($this->input->post('excercise_id'))? $this->input->post('excercise_id') : $excercise_status, 'class="form-control" id="excercise_id" onChange="updateForm();"');?></div>
                            </div>
                            <div id="aditional_form">
                                <div class ="row"> 
                                    <div class="form-group col-md-3" id="date2"><label>Fecha</label> <?php echo form_input($date);?></div>
                                </div>  


                                <div class ="row"> 
                                <?php if($excercise->distance == '1' OR $excercise->distance == '2'): ?> 
                                    <div class="form-group col-md-2" id="distance1"><label>Distancia</label> <?php echo form_input($distance);?></div>
                                    <div class="form-group col-md-1" id="distance2"><label>Distancia</label>
                                        <?php echo form_dropdown('distance_unit', $distance_list, ($this->input->post('distance_unit'))? $this->input->post('distance_unit') : 'm', 'class="form-control"');?>
                                    </div>
                                <?php endif; ?>
                                <?php if($excercise->time == '1' OR $excercise->time == '2'): ?> 
                                    <div class="form-group col-md-1" id="time1"><label>Horas</label> <?php echo form_input($hour);?></div>
                                    <div class="form-group col-md-1" id="time2"><label>Minutos</label> <?php echo form_input($min);?></div>
                                    <div class="form-group col-md-1" id="time3"><label>Segundos</label> <?php echo form_input($secs);?></div>
                                <?php endif; ?>
                                </div>  

                                <div class ="row"> 
                                <?php if($excercise->height == '1' OR $excercise->height == '2'): ?>    
                                    <div class="form-group col-md-1" id="height1"><label>Altura (cm)</label> <?php echo form_input($height);?></div>
                                <?php endif; ?>
                                <?php if($excercise->reps == '1' OR $excercise->reps == '2'): ?>   
                                    <div class="form-group col-md-1" id="reps1"><label>Repeticiones</label> <?php echo form_input($reps);?></div>
                                <?php endif; ?>
                                <?php if($excercise->load == '1' OR $excercise->load == '2'): ?>       
                                    <div class="form-group col-md-1" id="load1"><label>Carga/peso (kg)</label> <?php echo form_input($load);?></div>
                                <?php endif; ?>
                                </div>
                            </div>
                            <div class ="row">
                                <?php echo form_submit('submit', 'Guardar', 'class="btn btn-xs btn-primary m-t-n-xs"');?></p>
                            </div>
                        <?php echo form_close();?>

                    </div>
                </div>
            </div>


<script>

$("#hour").TouchSpin({
    min: 0,
    max: 10,
    step: 1,
    boostat: 5,
    maxboostedstep: 1
  });

$("#min").TouchSpin({
    min: 0,
    max: 59,
    step: 1,
    boostat:15,
    maxboostedstep: 5
  });

$("#secs").TouchSpin({
    min: 0,
    max: 59,
    step: 1,
    boostat: 15,
    maxboostedstep: 5
  });

$("#height").TouchSpin({
    min: 0,
    max: 250,
    step: 1,
    boostat: 5,
    maxboostedstep: 10
  });

$("#reps").TouchSpin({
    min: 0,
    max: 999,
    step: 1,
    boostat: 15,
    maxboostedstep: 5
  });

$("#load").TouchSpin({
    min: 0,
    max: 999,
    step: 1,
    boostat: 15,
    maxboostedstep: 10
  });

function updateForm(){
    var excercise_id = $('#excercise_id').val();
    var pr_id = <?php echo $id; ?>;
         $.ajax({
            url : '<?php echo base_url()."athlete/setExcerciseForm/update"; ?>',
            type : 'POST'  ,
            data : {excercise_id: excercise_id, pr_id: pr_id},
            success : function(data){
                $('#aditional_form').empty;
                $('#aditional_form').html(data);  
            },
            complete : function(){
                $("#excercise_id").select2();
                $('.date').datepicker({
                  autoclose: true
                });
                $(".spin").TouchSpin({
                    verticalbuttons: true,
                    max: 999
                });
            }
         });
 }




</script>

       











      
