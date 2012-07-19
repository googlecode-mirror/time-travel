<span id="timeSelector">
	<select class="selectInput hourTempl" style="font-size: 1.1em; color: #C0C0C0;">
		<?  for ($i=0; $i<24; $i++) {?>
			<option value="<?= (($i<10) ? ("0".$i) : $i) ?>"><?= (($i<10) ? ("0".$i) : $i) ?></option>
		<?  }?>
		
	</select>
	
	
	<select class="selectInput minuteTempl" style="font-size: 1.1em; color: #C0C0C0;">
		<?  for ($i=0; $i<60; $i++) {?>
			<option value="<?= (($i<10) ? ("0".$i) : $i) ?>"><?= (($i<10) ? ("0".$i) : $i) ?></option>
		<?  }?>
	</select>
</span>