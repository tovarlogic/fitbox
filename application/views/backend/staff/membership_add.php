
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
                              <span><a href='<?php echo base_url(); ?>staff/users' class='html5history'>Gestión de usuarios</a></span>
                          </li>
                          <li class="active">
                              <span>Alta en plan </span>
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
                        <?php echo ($action == 'add') ? form_open("staff/user/add/") : form_open("staff/user/edit/".$user_id); ?>
                            <div class ="row">
                              <div class="form-group col-md-4"><label>Nombre</label> <?php echo form_input($first_name);?></div>
                              <div class="form-group col-md-4"><label>Apellido</label> <?php echo form_input($last_name);?></div>
                              <div class="form-group col-md-4"><label>DNI</label> <?php echo form_input($DNI);?></div>
                            </div>
                            <div class ="row">
                              <div class="form-group col-md-4"><label>Email</label> <?php echo form_input($email);?></div>
                              <div class="form-group col-md-4"><label>Telefono</label> <?php echo form_input($phone);?></div>
                              <div class="form-group col-md-4"><label>Alias</label> <?php echo form_input($username);?></div>
                            </div>

                            <div class ="row">
                              <div class="form-group col-md-2">
                                <label>Fecha de Nacimiento</label>
                                  <?php echo form_dropdown('day', $days, ($this->input->post('day'))? $this->input->post('day') : $date[2], 'class="form-control"');?>
                                  <?php echo form_dropdown('month',$months,($this->input->post('month'))? $this->input->post('month') : $date[1], 'class="form-control"');?>
                                  <?php echo form_dropdown('year',$years,($this->input->post('year'))? $this->input->post('year') : $date[0], 'class="form-control"');?>
                              </div>
                            </div>

                            <div class ="row">
                              <div class="form-group col-md-4">
                                <label>Sexo</label> 
                                <?php echo form_dropdown('gender',$genders, ($this->input->post('gender'))? $this->input->post('gender') : $sex, 'class="form-control"');?>
                              </div>
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
                                <?php echo form_submit('submit', lang('edit_user_submit_btn'), 'class="btn btn-xs btn-primary m-t-n-xs"');?></p>
                            </div>
                        <?php echo form_close();?>

                    </div>
                </div>
            </div>   
                 













      
