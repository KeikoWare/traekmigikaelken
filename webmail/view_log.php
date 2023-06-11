<?php
error_reporting (0);
require './settings_path.php';
require './inc_functions.php';
require './class_dom_xml.php';
require './inc_settings.php';
$ErrorDesc = '';
$config = RestoreSettings($ErrorDesc);
if (!empty($ErrorDesc)) {
	$OkAction = 'window.close();';
	include('inc_html_error.php');
	exit;
}
if ($config['intDisableErrorHandling'] == 1)
	error_reporting (E_ALL ^ E_NOTICE);
if (!$config['intDisableErrorHandling'] || is_file($config['txtDefaultLogPath'])) {
	if ($config['intDisableErrorHandling']) {
		$f = fopen($config['txtDefaultLogPath'], "r");
	} else {
		$f = @fopen($config['txtDefaultLogPath'], "r");
	}
	if ($f) {
		if ($config['intDisableErrorHandling']) {
			$filesize = filesize($config['txtDefaultLogPath']);
		} else {
			$filesize = @filesize($config['txtDefaultLogPath']);
		}
		if ($filesize > 0) {
			$contents = fread ($f, $filesize);
			$contents = str_replace('<', '&lt;', $contents);
			$contents = str_replace('>', '&gt;', $contents);
			$contents = str_replace("]\r\n", "]<br>", $contents);
			$contents = str_replace("\r\n", "]<br>", $contents);
			$contents = str_replace("]\n", "]<br>", $contents);
			$contents = str_replace("\n", "]<br>", $contents);
		} else
			$contents = '';
		fclose($f);
	} else {
		$ErrorDesc = 'The web-server has no permission to read the log file<br />or<br />log file not exists';
		$OkAction = 'window.close();';
		include('inc_html_error.php');
		exit;
	}
	
} else {
	$ErrorDesc = 'Error: file '.$config['txtDefaultLogPath'].' not found<br>';
	$OkAction = 'window.close();';
	include('inc_html_error.php');
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
	<head>
		<title><?php echo $config['txtWindowTitle'];?> - View log-file</title>
		<link rel="stylesheet" href="./skins/<?php echo $config['txtDefaultSkin'];?>/styles.css" type="text/css">
	</head>
	<body class="wm_body">
		<?php echo $contents;?>
	</body>
</html>
