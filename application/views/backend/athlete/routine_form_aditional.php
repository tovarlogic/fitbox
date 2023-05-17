                                <div class ="row"> 
                                <?php if($type->ton == '1' OR $type->ton == '2'): ?> 
                                    <div class="form-group col-md-2" id="distance1"><label>Time On</label> <?php echo form_input($ton);?></div>
                                <?php endif; ?>
                                <?php if($type->toff == '1' OR $type->toff == '2'): ?> 
                                    <div class="form-group col-md-2" id="time1"><label>Time Off</label> <?php echo form_input($toff);?></div>
                                <?php endif; ?>
                                </div>  

                                <div class ="row"> 
                                <?php if($type->rounds == '1' OR $type->rounds == '2'): ?>    
                                    <div class="form-group col-md-2" id="height1"><label>Rounds</label> <?php echo form_input($rounds);?></div>
                                <?php endif; ?>
                                <?php if($type->time == '1' OR $type->time == '2'): ?>   
                                    <div class="form-group col-md-2" id="reps1"><label>Time</label> <?php echo form_input($time);?></div>
                                <?php endif; ?>
                                </div>