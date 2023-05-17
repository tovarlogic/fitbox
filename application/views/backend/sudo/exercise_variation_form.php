        

        <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='<?php echo base_url(); ?>sudo' class='html5history'>Inicio</a></li>
                          <li>
                              <span><a href='<?php echo base_url(); ?>sudo/exercises' class='html5history'>Ejercicios</a></span>
                          </li>
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

                        <?php echo ($action == 'add') ? form_open("sudo/exercise_variation/add/") : form_open("sudo/exercise_variation/edit/".$id); ?>
                        <fieldset>
                            <legend> Datos básicos</legend>
                            <div class ="row">
                              <div class="form-group col-md-4">
                                <label>Ejercicios base</label>
                                <?php echo form_multiselect('basic[]',$basics_options, ($this->input->post('basic'))? $this->input->post('basic') : $basics, 'class="form-control"');?>
                              </div>
                              <div class="form-group col-md-4"><label>Nombre</label> <?php echo form_input($name);?></div>
                              <div class="form-group col-md-4"><label>Nombre corto</label> <?php echo form_input($short_name);?></div>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend> Registro de resultados</legend>
                            <div class ="row">
                              <div class="form-group col-md-1">
                                <label>Reps</label> 
                                <?php echo form_dropdown('reps',$reps_options, ($this->input->post('reps'))? $this->input->post('reps') : $reps_status, 'class="form-control"');?>
                              </div>
                              <div class="form-group col-md-1">
                                <label>Load</label> 
                                <?php echo form_dropdown('load',$load_options, ($this->input->post('load'))? $this->input->post('load') : $load_status, 'class="form-control"');?>
                              </div>
                              <div class="form-group col-md-1">
                                <label>Distance</label> 
                                <?php echo form_dropdown('distance',$distance_options, ($this->input->post('distance'))? $this->input->post('distance') : $distance_status, 'class="form-control"');?>
                              </div>
                              <div class="form-group col-md-1">
                                <label>Height</label> 
                                <?php echo form_dropdown('height',$height_options, ($this->input->post('height'))? $this->input->post('height') : $height_status, 'class="form-control"');?>
                              </div>
                              <div class="form-group col-md-1">
                                <label>Time</label> 
                                <?php echo form_dropdown('time',$time_options, ($this->input->post('time'))? $this->input->post('time') : $time_status, 'class="form-control"');?>
                              </div>
                              <div class="form-group col-md-1">
                                <label>Energy</label> 
                                <?php echo form_dropdown('energy',$energy_options, ($this->input->post('energy'))? $this->input->post('energy') : $energy_status, 'class="form-control"');?>
                              </div>
                              <div class="form-group col-md-1">
                                <label>Tons</label> 
                                <?php echo form_dropdown('tons',$tons_options, ($this->input->post('tons'))? $this->input->post('tons') : $tons_status, 'class="form-control"');?>
                              </div>
                              <div class="form-group col-md-1">
                                <label>Work</label> 
                                <?php echo form_dropdown('work',$work_options, ($this->input->post('work'))? $this->input->post('work') : $work_status, 'class="form-control"');?>
                              </div>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend> Relaciones</legend>
                            <div class ="row">
                              <div class="form-group col-md-4">
                                <label>Types</label> 
                                <?php echo form_multiselect('type[]',$types_options, ($this->input->post('type'))? $this->input->post('type') : $types, 'class="form-control" style="height:150px"');?>
                              </div>
                              <div class="form-group col-md-4">
                                <label>Mechanics</label> 
                                <?php echo form_multiselect('mechanic[]',$mechanics_options, ($this->input->post('mechanic'))? $this->input->post('mechanic') : $mechanics, 'class="form-control"');?>
                              </div>
                              <div class="form-group col-md-4">
                                <label>Material</label> 
                                <?php echo form_multiselect('material[]',$materials_options, ($this->input->post('material'))? $this->input->post('material') : $materials, 'class="form-control" style="height: 150px"');?>
                              </div>
                            </div>

                            <div class ="row">
                              <div class="form-group col-md-4">
                                <label>Muscles primary</label> 
                                <?php echo form_multiselect('muscles_primary[]',$muscles_options, ($this->input->post('muscles_primary'))? $this->input->post('muscles_primary') : $muscles_primarys, 'class="form-control" style="height: 200px"');?>
                              </div>
                              <div class="form-group col-md-4">
                                <label>Muscles secundary</label> 
                                <?php echo form_multiselect('muscles_secondary[]',$muscles_options, ($this->input->post('muscles_secondary'))? $this->input->post('muscles_secondary') : $muscles_secondarys, 'class="form-control" style="height: 200px"');?>
                              </div>
                              
                            </div>

                            <div class ="row">
                              <div class="form-group col-md-12">
                                <label>Targets</label> 
                                <?php echo form_multiselect('target[]',$targets_options, ($this->input->post('target'))? $this->input->post('target') : $targets, 'class="form-control" style="height: 250px"');?>
                              </div>
                              <div class="form-group col-md-12">
                                <label>Movement</label> 
                                <?php echo form_multiselect('movement[]',$movements_options, ($this->input->post('movement'))? $this->input->post('movement') : $movements, 'class="form-control" style="height: 280px"');?>
                              </div>
                              <div class="form-group col-md-12">
                                <label>Contraction</label> 
                                <?php echo form_multiselect('contraction[]',$contractions_options, ($this->input->post('contraction'))? $this->input->post('contraction') : $contractions, 'class="form-control" style="height: 200px"');?>
                              </div>
                            </div>                            
                        </fieldset>

                            <div>
                                <?php echo form_submit('submit', 'Guardar', 'class="btn btn-xs btn-primary m-t-n-xs"');?> 
                            </div>
                        <?php echo form_close();?>
                    </div>
                </div>
            </div>  

<script>

  $('form').submit(function(e) {
    var form = $(this);
    var url2 = "<?php echo base_url("sudo/exercise_variations"); ?>";
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