	<table class="wm_toolbar">
	  <tr>
		<td>
<?php
	for ($i=0, $c = count($Titles); $i<$c; $i++) {
		echo '			<span class="wm_toolbar_item"';
		if ($Clicks[$i] != '') {
			echo ' onmouseover="this.className=\'wm_toolbar_item_over\'" 
			onmouseout="this.className=\'wm_toolbar_item\'" 
			onclick="'.$Clicks[$i].'"';
		}
		echo '>
				<img class="wm_icon" src="skins/'.$config['txtDefaultSkin'].'/menu/'.$Icons[$i].'" ';
		if ($Alts[$i] != '')
			echo 'alt="'.$Alts[$i].'"';
		if ($config['intShowTextLabels'] && $Titles[$i] != '')
			echo ' />&nbsp;'.$Titles[$i];
		else
			echo ' />&nbsp;';
		echo '
			</span>
';
	}
?>
		</td>
	  </tr>
	</table>
