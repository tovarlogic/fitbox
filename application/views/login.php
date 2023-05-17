<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title>FitBox | Login</title>

<?php $this->load->view('frontend/partials/top_includes'); ?>

</head>
<body class="blank">
<!-- This is needed when you send requests via Ajax -->
        <script type="text/javascript">
            var baseurl = 'http://kintec.es/fitbox/';
        </script>
<!-- Simple splash screen-->
<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1>FitBox - Gestión Online del Fitness</h1><p></p><img src="<?php echo base_url(); ?>assets/images/loading-bars.svg" width="64" height="64" /> </div> </div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<div class="color-line"></div>

<div class="back-link">
    <a onclick="window.history.back()" class="btn btn-primary">Volver</a>
</div>

<div class="login-container ajax-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <h3>FITBOX | ACCESO WEB</h3>
                <small>Gestión online del fitness y fitness business</small>
            </div>
            <div class="hpanel">
                <div class="panel-body">
                        <form method="post" action="#" id="form_login">
                            <div class="form-group">
                                <label class="control-label" for="username">Email</label>
                                <input type="text" placeholder="email" title="Por favor, introduzca su email" required="" value="" name="email" id="email" class="form-control">
                                
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">Password</label>
                                <input type="password" title="Por favor, introducza su contraseña" placeholder="******" required="" value="" name="password" id="password" class="form-control">
                                
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" class="i-checks" checked>
                                     Recordar
                                <p class="help-block small">(si es un dispositivo privado)</p>
                            </div>
                            <button class="btn btn-success btn-block">Acceder</button>
                            <a class="btn btn-default btn-block" href="#ajax/register">Registro nueva cuenta</a>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-md-12 text-center">
            <strong>FitBox</strong> - Gestión Online del Fitness <br/> 2015 Copyright Fitbox.es
        </div>
    </div>

<?php $this->load->view('frontend/partials/bottom_includes'); ?>

</body>
</html>