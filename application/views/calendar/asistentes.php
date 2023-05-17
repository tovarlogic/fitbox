<font color="black">
<?php if($coach): ?>
<p><b>Entrenador:</b> <?php echo $coach; ?></p>
<?php endif; ?>
<p><b>Asistentes:</b> 
	<?php 
	$i = $x = 0;
	if(!empty($asistentes)){
		foreach ($asistentes as $ath) 
		{ 
			$i++;
			if( ($x == 0 && $i > 2) OR ($x > 0 && $i > 3) ) { echo "<br>"; $i = 0; $x++;}
			if($i > 1) echo ", "; 
			echo $ath->first_name." ".$ath->last_name;
		} 
		echo ".";
	}
	?>
</p>
</font>

<?php if($this->ion_auth->in_group('athlete')): ?>

<!-- BOTON CANCELAR -->
    <?php 
    if($reserved === TRUE && $open === TRUE): ?>
    <p><button type="button" class="btn w-xs btn-danger" onclick="cancel('<?= $id ?>','<?= $dateTime ?>','1','<?= $serviceID ?>')">Cancelar reserva</button></p>
    <?php endif; ?>

    <?php if($reserved === TRUE && $open === FALSE): ?>
    <p><button type="button" class="btn w-xs btn-danger disabled">Cancelar reserva</button></p>
    <?php endif; ?>

<!-- BOTON RESERVAR -->
    <?php if($reserved === FALSE && $available === TRUE && in_array($serviceID, $subscribed_services) && is_array($subscribed_services)): ?>
        <?php if(in_array($serviceID, $subscribed_services)): ?>
            <p><button type="button" class="btn w-xs btn-success" onclick="book('<?= $id ?>','<?= $dateTime ?>','1','<?= $serviceID ?>')">Reservar</button></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php if($reserved === FALSE && $available === FALSE && is_array($subscribed_services)): ?>
        <?php if(in_array($serviceID, $subscribed_services)): ?>
            <p><button type="button" class="btn w-xs btn-success disabled">Reservar</button></p>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<script>

// $(document).on("click", ".btn", function (e) {
//   $('[data-toggle="popover"]').trigger('click');
//   $('[data-toggle="popover"]').trigger('manual');
// });

function book(id, time, box, service) 
{
    var response = "";
    $.ajax(
    {
        url: '<?php echo base_url()."calendar/book"; ?>',
        method: 'POST',
        dataType: 'html',
        data:{ time : time, service : service },
        success: function(data){
        		location.reload();
        }
    }
    );

}

function cancel(id, time, box, service) 
{
    var response = "";
    $.ajax(
    {
        url: '<?php echo base_url()."calendar/cancel"; ?>',
        method: 'POST',
        dataType: 'html',
        data:{ time : time, service : service },
        success: function(data){
        		location.reload();
        }
    }
    );

}


</script>
