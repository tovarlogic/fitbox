<div class="text-danger" id="Message"><?php echo $message;?></div>
<?php 
if($this->session->flashdata('success') != null) 
	echo "<div class='alert alert-success' id='successMessage'>".$this->session->flashdata('success')."</div>"; unset($_SESSION['success']);

if($this->session->flashdata('error') != null) 
	echo "<div class='alert alert-danger' id='errorMessage'>".$this->session->flashdata('error')."</div>"; unset($_SESSION['error']);

if($this->session->flashdata('info') != null) 
	echo "<div class='alert alert-info' id='infoMessage'>".$this->session->flashdata('info')."</div>";  
		unset($_SESSION['info']); 
?>
