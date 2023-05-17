<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="-1" />

    <!-- Page title -->
    <title>FITBOX | GESTOR DE FITNESS ONLINE</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/bootstrap/dist/css/bootstrap.css" />
    <!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/sweetalert/lib/sweet-alert.css" /> -->
    <!-- para formularios -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/xeditable/bootstrap3-editable/css/bootstrap-editable.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/select2-3.5.2/select2.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/select2-bootstrap/select2-bootstrap.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/bootstrap-datepicker-master/dist/css/bootstrap-datepicker3.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" />

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/summernote/dist/summernote.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/summernote/dist/summernote-bs3.css" />
    
    <!-- Tablas -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/fooTable/css/footable.core.min.css" />
    
    <!-- App styles -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/styles/style.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/styles/stripe.css">
    

    <!-- Vendor scripts -->
    <script src="<?php echo base_url(); ?>assets/vendor/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/slimScroll/jquery.slimscroll.min.js"></script>
    
    <script src="<?php echo base_url(); ?>assets/vendor/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- FLOT -->
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.canvaswrapper.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.colorhelpers.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.saturated.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.browser.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.drawSeries.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.uiConstants.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.navigate.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.touchNavigate.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.categories.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.time.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.hover.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.touch.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.legend.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.selection.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.errorbars.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot-3.2.9/source/jquery.flot.axislabels.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/flot.curvedlines/curvedLines.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/jquery.flot.spline/index.js"></script>


    <script src="<?php echo base_url(); ?>assets/vendor/metisMenu/dist/metisMenu.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/iCheck/icheck.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/peity/jquery.peity.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/sparkline/index.js"></script>
    <!-- <script src="<?php echo base_url(); ?>assets/vendor/sweetalert/lib/sweet-alert.min.js"></script> -->
       <!--  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script> -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@9/dist/sweetalert2.min.css" id="theme-styles">

    <script src="<?php echo base_url(); ?>assets/vendor/select2-3.5.2/select2.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/bootstrap-datepicker-master/dist/locales/bootstrap-datepicker.es.min.js" charset="UTF-8"></script>
    <script src="<?php echo base_url(); ?>assets/vendor/fooTable/dist/footable.all.min.js"></script>

    <script src="<?php echo base_url(); ?>assets/vendor/summernote/dist/summernote.min.js"></script>

    <!-- App scripts -->
    <script src="<?php echo base_url(); ?>assets/scripts/homer.js"></script>
    <script src="<?php echo base_url(); ?>assets/scripts/charts.js"></script>
    <script src="<?php echo base_url(); ?>assets/scripts/application.js"></script>
    
</head>
<body class="fixed-sidebar fixed-navbar">
