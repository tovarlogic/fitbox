
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
                              <span>Peso</span>
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
                        <?php echo ($action == 'add') ? form_open("athlete/weight/add/") : form_open("athlete/weight/edit/".$id); ?>
                            <div class ="row">
                              <div class="form-group col-md-4"><label>Fecha</label> <?php echo form_input($date);?></div>
                            </div>
                            <div class ="row">
                              <div class="form-group col-md-4"><label>Peso</label> <?php echo form_input($weight);?></div>
                              <div class="form-group col-md-4"><label>Grasa</label> <?php echo form_input($fat);?></div>
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

  $("#weight").TouchSpin({
      step: 0.1,
      decimals: 2,
      boostat: 5,
      maxboostedstep: 10,
      postfix: 'kg'
  });

  $("#fat").TouchSpin({
      min: 2,
      max: 80,
      step: 1,
      boostat: 5,
      maxboostedstep: 10,
      postfix: '%'
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






      
