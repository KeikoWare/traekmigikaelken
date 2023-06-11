<?php
error_reporting (E_ALL ^ E_NOTICE);
ob_start();
$Charset = 'utf-8';
header('Content-type: text/html; charset='.$Charset);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<title></title>
</head>
<body>
<?php
require './settings_path.php';
require './inc_functions.php';
require './class_dom_xml.php';
require './inc_settings.php';
$ErrorDesc = '';
$config = RestoreSettings($ErrorDesc);
include './language.php';
if (empty($ErrorDesc)) {
	if (isset($_FILES['fileAttach']) && ($_FILES['fileAttach']['size'] <= $config['intAttachmentSizeLimit']))
	{
		if (isset($_REQUEST['temp'])) {
			$TempDir = CorrectPath($_REQUEST['temp']);
			if (!is_dir($TempDir))
				$ErrorDesc = 'Error: folder '.$TempDir.' not found';
		} elseif (is_dir($config['txtDefaultTempDir'])) {
			$TempDir = CorrectPath($config['txtDefaultTempDir'].'/'.$_SESSION['wm_email']);
		} else $ErrorDesc = 'Error: folder '.$config['txtDefaultTempDir'].' not found';
		if ($ErrorDesc == '')
		{
			if (!is_dir($TempDir)) mkdir($TempDir);
			$FilePath = tempnam($TempDir, 'tmp');
			$FileName = basename($FilePath);
			if (!move_uploaded_file($_FILES['fileAttach']['tmp_name'], $TempDir.'/'.$FileName)){
				switch ($_FILES['fileAttach']['error']) {
					case 1: $ErrorDesc = iconv($InCharset, $Charset, $strTooBigFile); break;
					case 2: $ErrorDesc = iconv($InCharset, $Charset, $strTooBigFile); break;
					case 3: $ErrorDesc = iconv($InCharset, $Charset, $strFailedUpload3); break;
					case 4: $ErrorDesc = iconv($InCharset, $Charset, $strFailedUpload4); break;
					case 6: $ErrorDesc = iconv($InCharset, $Charset, $strFailedUpload6); break;
					default: $ErrorDesc = iconv($InCharset, $Charset, $strFailedUpload);
				}
			} else {
				$filesize = @filesize($TempDir.'/'.$FileName);
				if ($filesize == false)
					$ErrorDesc = iconv($InCharset, $Charset, $strFailedAlertSend);
			}
		}
	} else $ErrorDesc = iconv($InCharset, $Charset, $strTooBigFile);
}
if ($ErrorDesc != '') {
?>
	<script language="JavaScript" type="text/javascript">
		alert('<?php echo addslashes(strip_tags($ErrorDesc));?>');
	</script>
<?php
} else {
?>
	<script language="JavaScript" type="text/javascript">
		parent.AttachmentLoaded('<?php echo $FileName;?>','<?php echo $_FILES['fileAttach']['name'];?>','<?php echo $_FILES['fileAttach']['size'];?>','<?php echo $_FILES['fileAttach']['type'];?>');
	</script>
<?php
}
?>
</body>
</html>
<?php
ob_end_flush();
?>