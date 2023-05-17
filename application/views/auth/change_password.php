<?php
if (! $this->input->is_ajax_request()) 
{ 
      $this->load->view('frontend/partials/header');
      $this->load->view('frontend/partials/access_header');
}
?>
<div class="login-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <h3>FITBOX | CAMBIO DE CONTRASEÑA</h3>
                <small>Gestión online del fitness y fitness business</small>
            </div>
            <div class="hpanel">
                <div id="infoMessage"><?php echo $message;?></div>
                <div class="panel-body">
                    <div id="infoMessage"></div>
                    <?php echo form_open("auth/change_password");?>
                        <div class="form-group">
                            <?php echo lang('change_password_old_password_label', 'old_password');?> <br />
                              <?php echo form_input($old_password);?>
                            <label for="new_password"><?php echo sprintf(lang('change_password_new_password_label'), $min_password_length);?></label> <br />
                              <?php echo form_input($new_password);?>
                              <br />
                              <?php echo lang('change_password_new_password_confirm_label', 'new_password_confirm');?> <br />
                              <?php echo form_input($new_password_confirm);?>
                              <?php echo form_input($user_id);?>
                        </div>
                        
                        <button  class="btn btn-success btn-block">Enviar</button>
                    <?php echo form_close();?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if (! $this->input->is_ajax_request()) 
{ 
      $this->load->view('frontend/partials/access_footer'); 
}
?>