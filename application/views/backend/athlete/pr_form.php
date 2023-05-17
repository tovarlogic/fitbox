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
                              <span><a href='<?php echo base_url(); ?>athlete/PRs' class='html5history'>Gestión de Records Personales</a></span>
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
                        <?php echo form_open("athlete/pr/add/") ?>
                            <div class ="row">
                              <div class="form-group col-md-3"><label>Ejercicio</label><?php echo form_dropdown('excercise_id', $excercise_list, ($this->input->post('excercise_id'))? $this->input->post('excercise_id') : $excercise_status, 'class="form-control" id="excercise_id" onChange="updateForm();"');?></div>
                            </div>
                            <div id="aditional_form"></div>
                            <div class ="row">
                                <?php echo form_submit('submit', 'Guardar', 'class="btn btn-xs btn-primary m-t-n-xs"');?></p>
                            </div>
                        <?php echo form_close();?>

                    </div>
                </div>
            </div>



<script>
$("#excercise_id").select2();

function updateForm(){
    var excercise_id = $('#excercise_id').val();
         $.ajax({
            url : '<?php echo base_url()."athlete/setExcerciseForm"; ?>',
            type : 'POST'  ,
            data : 'excercise_id='+excercise_id,
            success : function(data){
                $('#aditional_form').empty;
                $('#aditional_form').html(data);  
            },
            complete : function(){
                $("#excercise_id").select2();

                $(".spin").TouchSpin({
                    verticalbuttons: true,
                    max: 999
                });
            }
         });
 }

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
       











      
