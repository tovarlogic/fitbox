
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
                              <span><a href='<?php echo base_url(); ?>staff/users' class='html5history'>Usuarios</a></span>
                          </li>
                          <li class="active">
                              <span>Editar usuario </span>
                          </li>
                      </ol>
                  </div>
                  <h2 class="font-light m-b-xs">
                      GESTIÓN DE USUARIOS
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
                    <div class="panel-body">
                        <?php $this->load->view('backend/messages'); ?>

                        <?php echo ($action == 'add') ? form_open("staff/users/add/") : form_open("staff/users/edit/".$user_id); ?>
                            <div class ="row">
                              <div class="form-group col-md-4"><label>Nombre</label> <?php echo form_input($first_name);?></div>
                              <div class="form-group col-md-4"><label>Apellido</label> <?php echo form_input($last_name);?></div>
                              <div class="form-group col-md-4"><label>Alias</label> <?php echo form_input($username);?></div>
                            </div>
                            <div class ="row">
                              <div class="form-group col-md-4"><label>Email</label> <?php echo form_input($email);?></div>
                              <div class="form-group col-md-4"><label>Telefono</label> <?php echo form_input($phone);?></div>
                              <div class="form-group col-md-4"><label>DNI</label> <?php echo form_input($DNI);?></div>
                            </div>

                            <div class ="row">
                              <div class="form-group col-md-6">
                                  <label>Fecha de Nacimiento</label>
                                  <div class ="row">
                                      <div class="form-group col-md-2">
                                          <?php echo form_dropdown('day', $days, ($this->input->post('day'))? $this->input->post('day') : $day_status, 'class="form-control"');?>
                                      </div>
                                      <div class="form-group col-md-2">
                                          <?php echo form_dropdown('month', $months,($this->input->post('month'))? $this->input->post('month') : $month_status, 'class="form-control"');?>
                                      </div>
                                      <div class="form-group col-md-4">
                                          <?php echo form_dropdown('year', $years, ($this->input->post('year'))? $this->input->post('year') : $year_status, 'class="form-control"');?>
                                      </div>
                                  </div>
                              </div>
                              <div class="form-group col-md-2">
                                
                              </div>
                              <div class="form-group col-md-4">
                                <label>Sexo</label> 
                                <?php echo form_dropdown('gender',$genders, ($this->input->post('gender'))? $this->input->post('gender') : $sex, 'class="form-control"');?>
                              </div>
                            </div>

                            <div class ="row">
                              <div class="form-group col-md-6">
                                  <label>Cuenta Bancaria (IBAN)</label> <?php echo form_input($IBAN);?>
                              </div>
                            </div>

                            <div class ="row">
                              
                              <div class="form-group col-md-4">
                                <label>Estado</label> 
                                <?php echo form_dropdown('active',$active_options, ($this->input->post('active'))? $this->input->post('active') : $active_status, 'class="form-control"');?>
                              </div>
                              <div class="form-group col-md-4">
                                <label>Grupos</label> 
                                <?php echo form_multiselect('group[]',$group_options, ($this->input->post('group'))? $this->input->post('group') : $groups, 'class="form-control"');?>
                              </div>
                            </div>
                            <div>
                              <?php echo form_submit('submit', $page_title, 'class="btn btn-xs btn-primary m-t-n-xs"');?> 
                            </div>
                        <?php echo form_close();?>
                    </div>
                </div>
            </div>  
<script>

  $('form').submit(function(e) {
    var form = $(this);
    var url2 = "<?php echo base_url("staff/users"); ?>";
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
                 













      
