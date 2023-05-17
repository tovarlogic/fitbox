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
                        <li><span>Log book </span></li>
                        <li><a href='<?php echo base_url(); ?>athlete/nutrition' class='html5history'>Nutrición</a></li>
                        <li class="active"> <span>Consulta nutrientes </span> </li>
                    </ol>
                </div>
                <h2 class="font-light m-b-xs">
                    GESTIÓN DE NUTRICIÓN
                </h2>
                <small>Dando el mejor servicio posible</small>
            </div>
        </div>
      </div>
      <?php endif ?>

        <div class="row">
          <!-- ALIMENTOS -->
            <div class="col-lg-6">
                <div class="hpanel">

                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        Consulta por alimentos
                    </div>

                    <div class="panel-body" id="form1">
                        <form action="<?php echo base_url(); ?>athlete/nutrients/food/" method="post" id="form1" name="form1"> 
                            <div class ="row">
                              <div class="form-group col-md-8"><label>Alimento</label> 
                                 <select name="food_id" id="food_id" class="form-control food" type="text" required="required">
                                  <?php 
                                  foreach($food_list as $key => $value)
                                  {
                                      echo "<option value='".$key."'>".$value."</option>";
                                  }
                                  ?>
                                </select> 
                              </div>
                              <div class="form-group col-md-4"><label><p><br><br><br></p></label> <?php echo form_submit('submit', 'Mostrar', 'class="btn btn-xs btn-primary m-t-n-xs"');?></div>
                            </div> 
                        </form>
                    </div>
                </div>
            </div>
            <!-- NUTRIENTES -->
            <div class="col-lg-6">
                <div class="hpanel">

                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <a class="closebox"><i class="fa fa-times"></i></a>
                        </div>
                        Consulta por nutrientes
                    </div>

                    <div class="panel-body" id="form2">
                      <form action="<?php echo base_url(); ?>athlete/nutrients/nutrient/" method="post" id="form2" name="form2"> 
                            <div class ="row">
                              <div class="form-group col-md-4"><label>Nutriente</label> 
                                 <select name="nutrient" id="nutrient" class="form-control nutrient1" type="text" required="required">
                                  <optgroup label="Minerales">
                                      <?php 
                                      foreach($minerals_list as $key => $value)
                                      {
                                          echo "<option value='".$key."'>".$value."</option>";
                                      }
                                      ?>
                                  </optgroup>

                                  <optgroup label="Vitaminas">
                                      <?php
                                      foreach($vitamins_list as $key => $value)
                                      {
                                          echo "<option value='".$key."'>".$value."</option>";
                                      }
                                      ?>
                                  </optgroup>
                                </select> 
                              </div>
                              <div class="form-group col-md-4"><label>Tipo alimento</label> 
                                 <select name="group_id" id="group_id" class="form-control nutrient2" type="text">
                                  <?php 
                                  foreach($group_list as $key => $value)
                                  {
                                      echo "<option value='".$key."'>".$value."</option>";
                                  }
                                  ?>
                                </select> 
                              </div>
                              <div class="form-group col-md-4"><label><p><br><br><br></p></label> <?php echo form_submit('submit', 'Mostrar', 'class="btn btn-xs btn-primary m-t-n-xs"');?></div>
                            </div> 
                        </form>
                        
                    </div>
                </div>
            </div>  
        </div>
        <!-- RESULTADOS CONSULTA -->
        <div class="row" id="nutrients_container">
          
        </div>
        <!-- RESULTADOS CONSULTA -->
        <div class="row" id="search_results">
          
        </div>

<script>

  $("select.food").select2({
    placeholder: "-- Seleccionar --",
    allowClear: true,
    minimumInputLength: 3
  });

  $("select.nutrient1").select2({
    placeholder: "-- Seleccionar --"
  });

  $("select.nutrient2").select2({
    placeholder: "-- Todos --",
    allowClear: true
  });

  $('form').submit(function(e) {
    var form = $(this);
    $.ajax({
        type: "POST",
        url: form.attr('action'),
        data: form.serialize(),
        dataType: "html",

        success: function(data){
          $( "#nutrients_container" ).empty();
          $( "#search_results" ).empty();
          
          if(form.attr('id') == 'form1') 
            $( "#nutrients_container" ).html( data );
          else 
            $( "#search_results" ).html( data );
        },

        error: function() { alert("Error posting feed."); }
   });
    e.preventDefault();
    return false;
});




</script>