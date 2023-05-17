<div class="row form-group col-md-12">
	<div class="form-group col-md-6">
		<?php echo form_dropdown("food_id[]", $food_list, '', 'class="form-control food" id="food_id"');?> 
	</div>
	<div class="form-group col-md-3"> 
		<?php echo form_input("qtty[]","", 'class="form-control qtty"');?>	
	</div>
	<!-- <div class="form-group col-md-4">
		<?php echo form_dropdown("serving[]", $serving_list, '', 'class="form-control serving" id="serving"');?> 
	</div> -->
	<div class="form-group col-md-2">
		<a class="btn btn-default remove_field" href="JavaScript:void(0);"><i class="fa fa-minus"></i></a>
	</div>
</div>

