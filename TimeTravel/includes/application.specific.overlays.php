<span id="timeSelector">
	<select class="selectInput hourTempl" style="font-size: 1.1em; color: #C0C0C0;">
		<?php  for ($i=0; $i<24; $i++) {?>
			<option value="<?php echo (($i<10) ? ("0".$i) : $i) ?>"><?php echo (($i<10) ? ("0".$i) : $i) ?></option>
		<?php  }?>
		
	</select>
	
	
	<select class="selectInput minuteTempl" style="font-size: 1.1em; color: #C0C0C0;">
		<?php  for ($i=0; $i<60; $i++) {?>
			<option value="<?php echo (($i<10) ? ("0".$i) : $i) ?>"><?php echo (($i<10) ? ("0".$i) : $i) ?></option>
		<?php  }?>
	</select>
</span>