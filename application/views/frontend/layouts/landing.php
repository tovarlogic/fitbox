<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title>FitBox | Gestión Online del Fitness</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/bootstrap/dist/css/bootstrap.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/styles/style.css">

</head>
<body class="landing-page">

<!-- Simple splash screen-->
<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1>FitBox - Gestión Online del Fitness</h1><p>Web App con la que registrar tus entrenos y hacer seguimiento de tu forma física o si eres un entrenador personal, la de tus clientes.</p><img src="<?php echo base_url(); ?>assets/images/loading-bars.svg" width="64" height="64" /> </div> </div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="#" class="navbar-brand">FitBox</a>
            <div class="brand-desc">
                Gestión Online del Fitness
            </div>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a class="page-scroll" href="#page-top">Inicio</a></li>
                <li><a class="page-scroll" page-scroll href="#features">Características</a></li>
                <li><a class="page-scroll" page-scroll href="#pricing">Precios </a></li>
                <li><a class="page-scroll" page-scroll href="#clients">Clientes </a></li>
                <li><a class="page-scroll" page-scroll href="#contact">Contacto</a></li>
                <li><a class="navbar-brand" page-scroll href="<?php echo base_url(); ?>auth/login">Acceder</a></li>
            </ul>
        </div>
    </div>
</nav>

<header id="page-top">
    <div class="container">
        <div class="heading">
            <h1>
                Bienvenido a FitBox.es
            </h1>
            <span>Web App intuitiva para registrar tus entramientos y hacer seguimiento a tu estado de forma física.<br/> 
                Incluye funciones específicas de gestión de negocio para entrenadores personales y gimnasios.
            </span>
            <p class="small">
                Independientemente del deporte que realices, <b>Triatlon, Crossfit, Yoga, Entrenamiento funcional, HIIT, etc..</b> y de tus objetivos (Adelgazar, Hipertrofia, Fuerza, Flexibilidad, etc...) pretendemos aportarte las herramientas para <b class="text-success">evaluar tu forma física y estado de salud con datos objetivos</b> a partir de tus sesiones de entrenamiento, biometrías, comidas, etc...
            </p>
            <a href="#" class="btn btn-success btn-xs">Más detalles</a>
        </div>
        <div class="heading-image animate-panel" data-child="img-animate" data-effect="fadeInRight">
            <p class="small"></p>
            <img class="img-animate" src="<?php echo base_url(); ?>assets/images/landing/c1.jpg">
            <img class="img-animate" src="<?php echo base_url(); ?>assets/images/landing/c2.jpg">
            <img class="img-animate" src="<?php echo base_url(); ?>assets/images/landing/c3.jpg">
            <img class="img-animate" src="<?php echo base_url(); ?>assets/images/landing/c4.jpg">
            <br/>
            <img class="img-animate" src="<?php echo base_url(); ?>assets/images/landing/c5.jpg">
            <img class="img-animate" src="<?php echo base_url(); ?>assets/images/landing/c6.jpg">
            <img class="img-animate" src="<?php echo base_url(); ?>assets/images/landing/c7.jpg">
            <img class="img-animate" src="<?php echo base_url(); ?>assets/images/landing/c8.jpg">
        </div>
    </div>
</header>
<section>
    <div class="container">
    <!-- <?php phpinfo(); ?> -->
    <div class="row">
        <div class="col-md-4">
            <h4>Gestión de Entrenamientos</h4>
            <p>Crea tus propias rutinas y programa de entreno, o sigue los creados por nosotros, tu entrenador u amigos.</p>
            <p>Registra tu progreso o permite que tu entrenador sea el que realiza el seguimiento.</p>
            <p><a class="navy-link btn btn-xs" href="#" role="button">Más detalles</a></p>
        </div>
        <div class="col-md-4">
            <h4>Gestión de tu salud</h4>
            <p>Podrás registrar y analizar la evolución de tus biometrías como peso o presión arterial, tus lesiones, alimentos ingeridos y mucho más. </p>
            <p>Tu entrenador personal, podrá prescribir tus entrenamientos de acuerdo a tus necesiadades personales.</p>
            <p><a class="navy-link btn btn-xs" href="#" role="button">Más detalles</a></p>
        </div>
        <div class="col-md-4">
            <h4>Gestión económica</h4>
            <p>Realiza el pago o domiciliaciones bancaras de tu gimnasio, box o centro deportivo.</p>
            <p>Si eres administrador de un gimnasio, Fitbox te aporta todas las herramientas para gestionarlo comodamente.</p>
            <p><a class="navy-link btn btn-xs" href="#" role="button">Más detalles</a></p>
        </div>
    </div>
    </div>
</section>


<section id="contact" class="bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-6 col-md-offset-3">
                <h2>Queremos escucharte, ponte en<span class="text-success"> contacto </span> con nosotros</h2>
                <p>
                    Si tienes cualquier comentario, duda o sugerencia, queremos conocerla.
                </p>
            </div>
        </div>

        <div class="row text-center m-t-lg">
            <div class="col-md-4 col-md-offset-3">

                <form class="form-horizontal" role="form" method="post" action="#">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Nombre</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Tu nombre" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">Email</label>

                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="email" name="email" placeholder="tu@correo.com" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message" class="col-sm-2 control-label">Mensaje</label>

                        <div class="col-sm-10">
                            <textarea class="form-control" rows="3" name="message"  placeholder="Tus comentarios...."></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input id="submit" name="submit" type="submit" value="Enviar mensaje" class="btn btn-success">
                        </div>
                    </div>
                </form>

            </div>
            <div class="col-md-3 text-left">
                <address>
                    <strong><span class="navy">Fitbox.es</span></strong><br/>
                    ...<br/>
                    Las Palmas de GC, España<br/>
                    <abbr title="Phone">Tlf:</abbr> (+34) XXX XXX XXX
                </address>
                <p class="text-color">
                    Fitbox.es aún se encuentra en fase BETA y en continuo desarrollo. Escuchamos tus necesidades y problemas para poderles dar solución.
                </p>
            </div>
        </div>


    </div>
</section>

<!-- Vendor scripts -->
<script src="<?php echo base_url(); ?>assets/vendor/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/vendor/jquery-ui/jquery-ui.min.js"></script>
<script src="<?php echo base_url(); ?>assets/vendor/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>assets/vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/vendor/metisMenu/dist/metisMenu.min.js"></script>
<script src="<?php echo base_url(); ?>assets/vendor/iCheck/icheck.min.js"></script>
<script src="<?php echo base_url(); ?>assets/vendor/sparkline/index.js"></script>

<!-- App scripts -->
<script src="<?php echo base_url(); ?>assets/scripts/homer.js"></script>

<!-- Local script for menu handle -->
<!-- It can be also directive -->
<script>
    $(document).ready(function () {

        // Page scrolling feature
        $('a.page-scroll').bind('click', function(event) {
            var link = $(this);
            $('html, body').stop().animate({
                scrollTop: $(link.attr('href')).offset().top - 50
            }, 500);
            event.preventDefault();
        });

        $('body').scrollspy({
            target: '.navbar-fixed-top',
            offset: 80
        });

    });
</script>

</body>
</html>