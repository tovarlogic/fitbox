<?php 
$this->load->view('frontend/partials/header');
$this->load->view('frontend/partials/access_header');
?>

<div class="login-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <h3>FITBOX | <?php echo $box_name; ?></h3>
                <small>Gestión online del fitness y fitness business</small>
            </div>
            <div class="hpanel">
                <div id="infoMessage" class="text-danger"><?php echo $message;?></div>
                <div class="panel-body">
                    <div id="infoMessage"></div>
                    <form method="post" id="form_login" action="login">
                        <div class="form-group">
                            <?php echo form_input($identity);?>
                            
                        </div>
                        <div class="form-group">
                            <?php echo form_input($password);?>
                        </div>
                        <div class="checkbox">
                            <input type="checkbox" name="remember" id="remember" class="i-checks" checked>
                                 Recordar
                            <p class="help-block small">(si es un dispositivo privado)</p>
                        </div>
                        <button  class="btn btn-success btn-block">Acceder</button>
                        <a class="btn btn-default btn-block" href="register">Registro nueva cuenta</a>
                        <a class="btn btn-default btn-block" href="forgot_password">No recuerdo contraseña</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('frontend/partials/access_footer'); ?>
