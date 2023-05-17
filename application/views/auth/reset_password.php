
<?php
$this->load->view('frontend/partials/header');
$this->load->view('frontend/partials/access_header');
?>

<div class="login-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <h3>FITBOX | CAMBIO CONTRASEÑA</h3>
                <small>Gestión online del fitness y fitness business</small>
            </div>
            <div class="hpanel">
                <div id="infoMessage"><?php echo $message;?></div>
                <div class="panel-body">
                    <?php echo form_open('auth/reset_password/' . $code);?>
                        <div class="form-group">
                        	<label for="new_password"><?php echo sprintf(lang('reset_password_new_password_label'), $min_password_length);?></label>
                            <?php echo form_input($new_password);?>
                         </div>
                        <div class="form-group">
                        	<label>repita nueva contraseña </label>
                            <?php echo form_input($new_password_confirm);?>
                        </div>
             
                        <?php echo form_input($user_id);?>
						<?php echo form_hidden($csrf); ?>
                        <button  class="btn btn-success btn-block">Enviar</button>

                    <?php echo form_close();?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('frontend/partials/access_footer'); ?>