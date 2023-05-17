<?php
$this->load->view('frontend/partials/header');
$this->load->view('frontend/partials/access_header');
?>

<div class="login-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <h3>FITBOX | RECORDAR CONTRASEÑA</h3>
                <small>Gestión online del fitness y fitness business</small>
            </div>
            <div class="hpanel">
                <div id="infoMessage"><?php echo $message;?></div>
                <div class="panel-body">
                    <div id="infoMessage"></div>
                    <?php echo form_open("auth/forgot_password");?>
                        <div class="form-group">
                        	<label for="identity"><?php echo (($type=='email') ? sprintf(lang('forgot_password_email_label'), $identity_label) : sprintf(lang('forgot_password_identity_label'), $identity_label));?></label>
                            <?php echo form_input($identity);?>
                        </div>
                        
                        <button  class="btn btn-success btn-block">Enviar</button>
                    <?php echo form_close();?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('frontend/partials/access_footer'); ?>
