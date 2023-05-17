
    </div>


<?php $this->load->view('backend/partials/footer'); ?>
<script>
	$(window).load(function(){
		swal.fire({
			title: "Bienvenido",
			  text: "Tu usuario cuenta con varios perfiles. Por favor, elige como quieres acceder.",
			  confirmButtonText: "Atleta",
			  showCancelButton: true,
			  cancelButtonText: "Box staff",
			  confirmButtonColor: '#3085d6',
  			  cancelButtonColor: '#d33',
  			  reverseButtons: true
			}).then((result) => {
				if (result.value) {
					window.location.href = "athlete";
				} 
				/* Read more about handling dismissals below */
				else if (result.dismiss === Swal.DismissReason.cancel) 
				{
					window.location.href = "staff";
				}
				else
				{
					window.location.href = "select";
				}
			})
	});
</script>
