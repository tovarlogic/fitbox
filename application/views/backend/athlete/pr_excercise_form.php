
<div class ="row"> 
    <div class="form-group col-md-3" id="date2"><label>Fecha</label> <?php echo form_input($date);?></div>
</div>  


<div class ="row"> 
<?php if($excercise->distance == '1' OR $excercise->distance == '2'): ?> 
    <div class="form-group col-md-2" id="distance1"><label>Distancia</label> <?php echo form_input($distance);?></div>
    <div class="form-group col-md-1" id="distance2"><label>Distancia</label>
        <?php echo form_dropdown('distance_unit', $distance_list, ($this->input->post('distance_unit'))? $this->input->post('distance_unit') : 'm', 'class="form-control"');?>
    </div>
<?php endif; ?>
<?php if($excercise->time == '1' OR $excercise->time == '2'): ?> 
    <div class="form-group col-md-2" id="time1"><label>Horas</label> <?php echo form_input($hour);?></div>
    <div class="form-group col-md-2" id="time2"><label>Minutos</label> <?php echo form_input($min);?></div>
    <div class="form-group col-md-2" id="time3"><label>Segundos</label> <?php echo form_input($secs);?></div>
<?php endif; ?>
</div>  

<div class ="row"> 
<?php if($excercise->height == '1' OR $excercise->height == '2'): ?>    
    <div class="form-group col-md-2" id="height1"><label>Altura (cm)</label> <?php echo form_input($height);?></div>
<?php endif; ?>
<?php if($excercise->reps == '1' OR $excercise->reps == '2'): ?>   
    <div class="form-group col-md-2" id="reps1"><label>Repeticiones</label> <?php echo form_input($reps);?></div>
<?php endif; ?>
<?php if($excercise->load == '1' OR $excercise->load == '2'): ?>       
    <div class="form-group col-md-2" id="load1"><label>Carga/peso (kg)</label> <?php echo form_input($load);?></div>
<?php endif; ?>
</div>

<script>

$("#hour").TouchSpin({
    min: 0,
    max: 10,
    step: 1,
    boostat: 5,
    maxboostedstep: 1
  });

$("#min").TouchSpin({
    min: 0,
    max: 59,
    step: 1,
    boostat:15,
    maxboostedstep: 5
  });

$("#secs").TouchSpin({
    min: 0,
    max: 59,
    step: 1,
    boostat: 15,
    maxboostedstep: 5
  });

$("#height").TouchSpin({
    min: 0,
    max: 250,
    step: 1,
    boostat: 5,
    maxboostedstep: 10
  });

$("#reps").TouchSpin({
    min: 0,
    max: 999,
    step: 1,
    boostat: 15,
    maxboostedstep: 5
  });

$("#load").TouchSpin({
    min: 0,
    max: 999,
    step: 1,
    boostat: 15,
    maxboostedstep: 10
  });

</script>