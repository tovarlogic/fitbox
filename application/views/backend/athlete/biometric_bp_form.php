
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
                          <li>Log book</li>
                          <li>Biometrías</li>
                          <li class="active">
                              <span>Presión arterial</span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE BIOMETRÍAS
                  </h2>
                  <small>Dando el mejor servicio posible</small>
              </div>
          </div>
      </div>
      <?php endif ?>

      <div class="text-danger" id="infoMessage"><?php echo $this->session->flashdata('message')?></div>

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
                        <?php echo ($action == 'add') ? form_open("athlete/bp/add/") : form_open("athlete/bp/edit/".$id); ?>
                            <div class ="row">
                              <div class="form-group col-md-4"><label>Fecha</label> <?php echo form_input($date);?></div>
                              <div class="form-group col-md-4"><label>Hora</label> <?php echo form_input($hour);?></div>
                            </div>
                            <div class="row">
                              <div class="form-group col-md-4"><label>Sistólica</label> <?php echo form_input($systolic);?></div>
                              <div class="form-group col-md-4"><label>Diastólica</label> <?php echo form_input($diastolic);?></div>
                              <div class="form-group col-md-4"><label>Pulso</label> <?php echo form_input($pulse);?></div>
                            </div>                        

                            <div class ="row">
                                <?php echo form_submit('submit', 'Guardar', 'class="btn btn-xs btn-primary m-t-n-xs"');?></p>
                            </div>
                        <?php echo form_close();?>

                    </div>
                </div>
            </div>


        

<script>

  $('#date').datepicker();
  $('#hour').timepicker();

  $("#pulse").TouchSpin({
    min: 20,
    max: 250,
    step: 1,
    boostat: 5,
    maxboostedstep: 10
  });

  $("#diastolic").TouchSpin({
    min: 20,
    max: 250,
    step: 1,
    boostat: 5,
    maxboostedstep: 10
  });

  $("#systolic").TouchSpin({
      min: 20,
    max: 250,
    step: 1,
    boostat: 5,
    maxboostedstep: 10
  });


$('form').submit(function(e) {
    var form = $(this);
    var url2 = "<?php echo base_url("athlete/biometrics"); ?>";
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











      
