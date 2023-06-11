<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<link rel="stylesheet" href="skins/<?php echo $config['txtDefaultSkin'];?>/styles.css" type="text/css" />
	<title><?php echo $config['txtWindowTitle'];?> - Error</title>
</head>

<body>
	<div align="center" style="margin-top:40px">
		<div class="wm_error_div" align="center" valign="middle">
			<div class="wm_error_header">Error(s) detected</div>
			<div class="wm_error_text"><?php echo $ErrorDesc;?></div>
<?php
	if (!empty($OkAction)) {
?>
		<div class="wm_error_button">
				<a href="" class="wm_reg" onclick="<?php echo $OkAction;?> return false;">OK</a>
		</div>
<?php
	} ?> 
		</div>
	</div>
</body>
</html>