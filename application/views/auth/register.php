<?php
$this->load->view('frontend/partials/header');
$this->load->view('frontend/partials/access_header');
?>

<div class="register-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <h3>FITBOX | REGISTRO NUEVO USUARIO</h3>
                <small>Gestión online del fitness y fitness business</small>
            </div>
            <div class="hpanel">
                <div id="infoMessage"><?php echo $message;?></div>
                <div class="panel-body">
                    <form action="register" id="loginForm" method="post">
                        <div class="row">
                        <?php if($identity_column!=='email'): ?>
                            <div class="form-group col-lg-12">
                                <label>Usuario </label>
                                <?php echo form_input($identity);?>
                            </div>
                        <?php endif; ?>
                        <div class="form-group col-lg-6">
                            <label>Email </label>
                            <?php echo form_input($email);?>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Repita Email</label>
                           <?php echo form_input($email_confirm);?>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Password</label>
                            <?php echo form_input($password);?>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Repita Password</label>
                            <?php echo form_input($password_confirm);?>
                        </div>
                        <div class="checkbox col-lg-12">
                            <input type="checkbox" class="i-checks" checked>
                            Recibir boletín de noticias
                        </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-success btn-block">Registrarse</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('frontend/partials/access_footer'); ?>