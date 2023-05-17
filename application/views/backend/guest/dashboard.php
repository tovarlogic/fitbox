
    </div>

    <!-- Right sidebar -->
	<?php $this->load->view('backend/sudo/partials/right_bar'); ?>   

<?php $this->load->view('backend/partials/footer'); ?>
<script>
	$(window).load(function(){
		swal.fire({
			timer: 3000,
	        title: "Bienvenido",
	        text: "Por el momento tu cuenta es de Invitado. Es necesario que te des de alta en un plan del box y realizar el primer pago para poder comenzar a usar las funcionalidades de FitBox."
	    });
    });
</script>
