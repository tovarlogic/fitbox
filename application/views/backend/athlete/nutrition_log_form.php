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
                  <li class="active"> <span>Registro Alimentos </span> </li>
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

<div class="content">

<div class="row">
    <div class="col-lg-8">
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
                <?php echo ($action == 'add') ? form_open("athlete/foods/add/") : form_open("athlete/foods/edit/".$id.'/'.$id); ?>
                    <div class ="row">
                      <div class="form-group col-md-6">
                        <label>Fecha</label> 
                        <?php echo form_input($date);?></div>
                      <div class="form-group col-md-6">
                        <label>Comida</label> 
                        <?php echo form_dropdown('meal', $meal_list, ($this->input->post('meal'))? $this->input->post('meal') : ($meal_status)? $meal_status: "<-- Selecciona -->", 'class="form-control" id="meal"');?> </div>
                    </div>
                    <div class ="row form-group col-md-12">
                      <div class="form-group col-md-6">
                        <label>Alimento</label> 
                        <?php echo form_dropdown('food_id[]', $food_list, ($this->input->post('food_id'))? $this->input->post('food_id') : $food_status, 'class="form-control food" id="food_id"');?> </div>
                      <div class="form-group col-md-3">
                        <label>Cantidad</label> 
                        <?php echo form_input('qtty[]', ($action == 'edit')? $qtty['value'] : "", 'class="form-control qtty"');                 log_message('debug',print_r($qtty,TRUE));?>

                      </div>
                      <!-- <div class="form-group col-md-4" ><label>Unidades</label> <?php echo form_dropdown('serving[]', $servings_list, ($this->input->post('serving'))? $this->input->post('serving') : "<-- Selecciona -->", 'class="form-control" id="serving"');?> </div> -->
                    </div>
                    <div class ="row input_fields_wrap">
                        
                    </div>                        

                    <div class ="row">
                      <?php if($action == 'add') { echo '<div class="form-group col-md-1"><a class="btn btn-default" id="btn-add" href="JavaScript:void(0);"><i class="fa fa-plus"></i></a></div>'; } ?>
                      <div class="form-group col-md-1">
                        <?php echo form_submit('submit', 'Guardar', 'class="btn btn-xs btn-primary m-t-n-xs"');?>
                      </div>
                    </div>
                <?php echo form_close();?>

            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-tools">
                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    <a class="closebox"><i class="fa fa-times"></i></a>
                </div>
                Alimentos frecuentes (últimos 3 meses)
            </div>
            <div class="panel-body">                

            </div>
        </div>
    </div>

    <div class="pipo"></div>

<script>

  $(".qtty").TouchSpin({
    min: 0,
    max: 9999,
    step: 1,
    boostat: 5,
    maxboostedstep: 10
  });

  $("select.food").select2({
    placeholder: "-- Seleccionar --",
    allowClear: true,
    minimumInputLength: 3
  });

  var wrapper         = $(".input_fields_wrap"); //Fields wrapper
  var excercises    = $('.excercise_wrapper'); // Excercise wrapper    

  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); 
        $(this).parent('div').parent('div').remove(); x--;
  })

 $('#btn-add').click(function(){
  $.ajax({
    url : '<?php echo base_url()."athlete/getNutritionPartialform/"; ?>',

    success : function(data){
        $('#aditional_form').empty;
        $(wrapper).append(data);  
    },

    complete : function(){

      $(".qtty").TouchSpin({
        min: 0,
        max: 9999,
        step: 1,
        boostat: 5,
        maxboostedstep: 10
      });

      $("select.food").select2({
        placeholder: "-- Seleccionar --",
        allowClear: true,
        minimumInputLength: 3
      });
    }

  });
 });

  $('#food_id').select(function(){
        x++;
         $.ajax({
            url : '<?php echo base_url()."athlete/getServings/"; ?>',
            type : 'GET'  ,
            data : {food_id: x},
            success : function(data){
                $('#aditional_form').empty;
                $(wrapper).append(data);  
            }
         });
 });


$('form').submit(function(e) {
    var form = $(this);
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: form.attr("action"),
        data: form.serialize(),
        dataType: "html",

        success: function(data){
            history.pushState(null, null, url);
            $('.content').empty();
            $('.content').html(data);
        },

        error: function() { alert("Error posting feed."); }
   });

});

</script>









      
