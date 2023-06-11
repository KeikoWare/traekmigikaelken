<?php
error_reporting (0);
$autostart = ini_get('session.auto_start');
if (!$autostart) {
	session_name("PHPWEBMAILSESSID");
	session_start();
}
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
require './class_pop3.php';
require './class_message.php';
require './language.php';

$MessageId = $_REQUEST['id'];
if ($config['txtDefaultCharset'] == '') $Charset = 'utf-8';
else $Charset = $config['txtDefaultCharset'];

$pop3 = new POP3();
$pop3->Password = $_SESSION['wm_password'];
$pop3->PortNumber = $_SESSION['wm_inc_server_port'];
$pop3->ServerName = $_SESSION['wm_inc_server'];
$pop3->UserName = $_SESSION['wm_login'];
$pop3->ImapFlags = $config['txtImapFlags'];
$pop3->LogFilePath = $config['txtDefaultLogPath'];
$pop3->EnableLogging = $config['intEnableLogging'];
$pop3->TimeOffset = $config['txtDefaultTimeOffset'];
//connection at pop3-server
$pop3->Connect();
$Message = $pop3->GetMessage($MessageId, $Charset);
if ($pop3->IsError) {
	$OkAction = 'window.close();';
	$ErrorDesc = $Message->ErrorDesc;
	include './inc_html_error.php';
} else {
	$Message->Charset = $Charset;
	$From = $Message->DecodeHeader($Message->RawFromAddr);
	$To = $Message->DecodeHeader($Message->RawToAddr);
	$Date = $Message->Date;
	$Subject = $Message->DecodeHeader($Message->RawSubject);
	if ($Message->HasHtmlBody) {
		foreach ($Message->AttachmentsInfo as $AttachInfo)
			if (!empty($AttachInfo['cid'])) {
				$AttachCid = str_replace('>','',str_replace('<','',$AttachInfo['cid']));
				if (strpos($Message->HtmlBody, $AttachCid)){
					$Replacement = './picture.php?msg_id='.$MessageId.'&part_id='.
					$AttachInfo['id'].'&encoding='.$AttachInfo['encoding'].'&mime='.
					$AttachInfo['type'].'-'.$AttachInfo['subtype'];
					$PatternArray = Array('cid:'.$AttachCid, 'CID:'.$AttachCid, 'Cid:'.$AttachCid);
					$Message->HtmlBody = str_replace($PatternArray, $Replacement, $Message->HtmlBody);
				}
			}
		$MessageBody = $Message->GetCensoredHtmlBody();
	} else
		$MessageBody = $Message->GetCensoredTextBody();

	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Content-type: text/html; charset='.$Charset);
}
//disconnection from pop3-server
$pop3->Disconnect();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<link rel="stylesheet" href="./skins/<?php echo $config['txtDefaultSkin'];?>/styles.css" type="text/css" />
	<title><?php echo $config['txtWindowTitle'];?></title>
</head>

<body class="wm_body">
  <div align="center" class="wm_space_before">
	<table class="wm_print">
	  <tr>
		<td class="wm_print_content" style="border-width: 0px 1px 1px 0px" width="60px"><?php echo iconv($InCharset, $Charset, $strFrom);?>: </td>
		<td class="wm_print_content" style="border-width: 0px 0px 1px 1px"><?php echo EncodeHTML($From);?></td>
	  </tr>
	  <tr>
		<td class="wm_print_content" style="border-width: 0px 1px 1px 0px" width="60px"><?php echo iconv($InCharset, $Charset, $strTo);?>: </td>
		<td class="wm_print_content" style="border-width: 0px 0px 1px 1px"><?php echo EncodeHTML($To);?></td>
	  </tr>
	  <tr>
		<td class="wm_print_content" style="border-width: 0px 1px 1px 0px" width="60px"><?php echo iconv($InCharset, $Charset, $strDate);?>: </td>
		<td class="wm_print_content" style="border-width: 0px 0px 1px 1px"><?php echo $Date;?></td>
	  </tr>
	  <tr>
		<td class="wm_print_content" style="border-width: 0px 1px 1px 0px" width="60px"><?php echo iconv($InCharset, $Charset, $strSubject);?>: </td>
		<td class="wm_print_content" style="border-width: 0px 0px 1px 1px"><?php echo EncodeHTML($Subject);?></td>
	  </tr>
	  <tr>
		<td colspan="2" class="wm_print_content" style="border-width: 1px 0px 0px 0px">
			<div class="wm_space_before">
				<?php echo $MessageBody;?>
			</div>
		</td>
	  </tr>
	</table>
  </div>
</body>
</html>

