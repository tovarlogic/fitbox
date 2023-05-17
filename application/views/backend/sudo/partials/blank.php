<?php $this->load->view('backend/partials/header'); ?>

<!-- Simple splash screen-->
<div class="splash"> 
	<div class="color-line"></div>
	<div class="splash-title"><h1>FitBox - Gestión Online del Fitness</h1><p></p><img src="<?php echo base_url(); ?>assets/images/loading-bars.svg" width="64" height="64" /> </div> 
</div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!-- Header -->
<?php $this->load->view('backend/sudo/partials/header_bar'); ?>

<!-- Navigation -->
<?php 
$data['user'] = $user;
$this->load->view('backend/sudo/partials/nav_bar', $data); 
//$this->load->view('backend/staff/partials/nav_bar'); 
?>
<!-- Main Wrapper -->
<div id="wrapper">
	<div class="splash2"> 
		<div class="color-line"></div>
		<div class="splash-title">
			<img src="<?php echo base_url(); ?>assets/images/loading-bars.svg" width="64" height="64" /> 
		</div> 
	</div>
    <div class="content animate-panel">
