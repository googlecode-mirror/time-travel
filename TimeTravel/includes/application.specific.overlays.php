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

<div id="gmailFolderList" style="display: none;">
	<table width="100%" border="0" style="font-size: 1.0em">
		<thead id="gmailFolderList-head">
			<td width="5%" class="ui-widget-header"><input type="checkbox"/> </td>
			<td class="ui-widget-header">
				Folder
			</td>
		</thead>
	</table>
	<div class="ui-state-default" style="width: 544px; padding: 5px;">
		Please sync my emails starting from 
			<select id="syncStartMonth">
				<option value="01">January</option>
				<option value="02">February</option>
				<option value="03">March</option>
				<option value="04">April</option>
				<option value="05">May</option>
				<option value="06">June</option>
				<option value="07">July</option>
				<option value="08">August</option>
				<option value="09">September</option>
				<option value="10">October</option>
				<option value="11">November</option>
				<option value="12">December</option>
			</select>
			
			<select id="syncStartYear">
				<option value="2012">2012</option>
				<option value="2011">2011</option>
				<option value="2010">2010</option>
				<option value="2009">2009</option>
				<option value="2008">2008</option>
				<option value="2007">2007</option>
			</select>
	</div>		
</div>