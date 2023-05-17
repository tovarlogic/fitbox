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
                              <span>Editar servicio </span>
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
                        <?php echo form_open("staff/conf/membership/edit", array('id' => 'Form')); ?>

                          <?php 
                          $i = 0;
                          foreach ($membership as $key => $value):
                          ?> 
                              <?php 
                              if($key == 'grace_period') $label = 'Periodo de gracia'; 
                              else if($key == 'cancel_period') $label = 'Periodo de cancelación'; 
                              ?>
                              <?php  if($i == 0) echo '<div class ="row">'; ?>
                              <div class="form-group col-md-3"><label><?php echo $label; ?></label> 
                                  <?php echo form_input($key);?>

                              </div>
                              <?php $i++; if($i == 4) { echo '</div>'; $i = 0;} ?>
                          <?php endforeach ?>
                          <?php if ($i<3) { echo '</div>'; $i = 0;} ?>

                            <div class ="row">
                                <?php echo form_submit('submit', 'Guardar', 'class="btn btn-xs btn-primary m-t-n-xs"');?></p>
                            </div>
                        <?php echo form_close();?>
                    </div>
                </div>
            </div>  

<script>
$('form').submit(function(e) {
    var form = $(this);
    var url2 = "<?php echo base_url("staff/conf/"); ?>";
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

        error: function() { 
          alert("Error posting."); 
        }
   });
    return false;
});


</script>

                 













      
