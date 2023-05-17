<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->


<div class="back-link">
    <a  onclick="window.history.back()"  class="btn btn-primary">Volver</a>
</div>
<div class="error-container">
    <i class="pe-7s-way text-success big-icon"></i>
    <h1><?php echo $heading; ?></h1>
    <strong>Página no existe</strong>
    <p><?php echo $message; ?></p>
    <a onclick="window.history.back()" class="btn btn-xs btn-success">Volver</a>
</div>


