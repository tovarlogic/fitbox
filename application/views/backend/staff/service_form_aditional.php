<div class="form-group col-md-8">
	<div class="input-group">
        <input 
			class="schedule input-sm col-md-3 form-control" 
			type="number" min="0" max="23" 
			name="week_from_h_<?php echo $week ?>[]" 
			value="" 
			class="adjStartEnd adj_hrs_0" 
		/>
        <span class="input-group-addon">:</span>
        <input  
        	class="schedule input-sm col-md-3 form-control" 
        	type="number" min="0" max="59" 
        	name="week_from_m_<?php echo $week ?>[]" 
        	value="" 
        	class="adjStartEnd adj_mins_0" 
        />
    </div>
    <div class="input-group"> 
        <input  
            class="schedule input-sm col-md-3 form-control" 
            type="text" size="25"
            name="coach_<?php echo $week ?>[]" 
            placeholder="entrenador"
            value="<?php if(isset($week["{$i}"]["{$j}"]['coach'])){ echo $week["{$i}"]["{$j}"]['coach'];}else{ echo '' ;} ?>"
        />
      </div>
    <a href="javascript:;" class="adj_SE_remove">eliminar</a>

</div>