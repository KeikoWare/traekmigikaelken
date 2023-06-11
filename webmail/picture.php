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
require './class_pop3.php';

$MessageId = $_REQUEST['msg_id'];
$PartId = $_REQUEST['part_id'];
$Encoding = $_REQUEST['encoding'];
$Mime = $_REQUEST['mime'];

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

$pop3->ClientRequest = date('H:i:s ').'[Getting part of message: message number '.$MessageId.', part number 1]';
$Img = imap_fetchbody($pop3->ConnectionHandle, $MessageId, $PartId);
$pop3->ServerResponse = date('H:i:s ').'[Got part of message]';
if ($pop3->EnableLogging) $pop3->LogToFile();

if ($Encoding == 3) $Img = imap_base64($Img);
elseif ($Encoding == 4) $Img = imap_qprint($Img);

//disconnection from pop3-server
$pop3->Disconnect();

header('Content-type: '.$Mime);
echo $Img;
?>
