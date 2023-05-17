        

        <div class="small-header">
          <div class="hpanel">
              <div class="panel-body">
                  <div id="hbreadcrumb" class="pull-right">
                      <ol class="hbreadcrumb breadcrumb">
                          <li><a href='<?php echo base_url(); ?>sudo' class='html5history'>Inicio</a></li>
                          <li>Ejercicios</li>
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

                        <?php echo ($action == 'add') ? form_open("sudo/sport/add/") : form_open("sudo/sport/edit/".$id); ?>
                            <div class ="row">
                              <div class="form-group col-md-4"><label>Nombre</label> <?php echo form_input($name);?></div>
                            </div>

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
    var url2 = "<?php echo base_url("sudo/sports"); ?>";
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