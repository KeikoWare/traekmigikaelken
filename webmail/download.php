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
$config = RestoreSettings();
$ErrorDesc = '';
$config = RestoreSettings($ErrorDesc);
if (!empty($ErrorDesc)) {
	header('Location: ./index.php');
}
require './class_pop3.php';

$MessageId = $_REQUEST['msg_id'];
$PartId = $_REQUEST['part_id'];
$Name = urldecode($_REQUEST['name']);
$Encoding = $_REQUEST['encoding'];
$Mime = $_REQUEST['mime'];
$File = '';
$pop3 = new POP3();
$pop3->Password = $_SESSION['wm_password'];
$pop3->PortNumber = $_SESSION['wm_inc_server_port'];
$pop3->ServerName = $_SESSION['wm_inc_server'];
$pop3->UserName = $_SESSION['wm_login'];
$pop3->ImapFlags = $config['txtImapFlags'];
$pop3->LogFilePath = $config['txtDefaultLogPath'];
$pop3->EnableLogging = $config['intEnableLogging'];
//connection at pop3-server
$pop3->Connect();
if ($pop3->Connected) {
	$pop3->ClientRequest = date('H:i:s ').'[Getting part of message: message number '.$MessageId.', part number 1]';
	$File = imap_fetchbody($pop3->ConnectionHandle, $MessageId, $PartId);
	$pop3->ServerResponse = date('H:i:s ').'[Got part of message]';
	if ($pop3->EnableLogging) $pop3->LogToFile();
	
	if ($Encoding == 3) $File = imap_base64($File);
	elseif ($Encoding == 4) $File = imap_qprint($File);
	//disconnection from pop3-server
	$pop3->Disconnect();
}
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-type: '.$Mime);
header('Content-Disposition: attachment; filename="'.$Name.'"');
header('Content-Length: '.strlen($File));
echo ($File);
?>
