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
	error_reporting(E_ALL ^ E_NOTICE);

$MessageId = $_REQUEST['msg_id'];
$PartId = $_REQUEST['part_id'];
$Name = $_REQUEST['name'];
$Encoding = $_REQUEST['encoding'];
$Mime = $_REQUEST['mime'];

$Charset = ($config['txtDefaultCharset'] == '') ? 'utf-8' : $config['txtDefaultCharset'];
Header("Content-type: text/html; charset=".$Charset);
?>
<html>
	<head>
		<title><?php echo $config['txtWindowTitle'];?></title>
	</head>
	<body>
		<img src="./picture.php?msg_id=<?php echo $MessageId;?>&part_id=<?php echo $PartId;?>&encoding=<?php echo $Encoding;?>&mime=<?php echo $Mime;?>">
	</body>
</html>