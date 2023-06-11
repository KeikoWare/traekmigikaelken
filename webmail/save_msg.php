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
	header('Location: ./index.php');
}
require './class_pop3.php';
require './class_message.php';
require './language.php';

$MessageId = $_REQUEST['id'];
$File = '';
$Name = '';
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
if ($pop3->Connected) {
	$Message->Charset = $Charset;
	$From = $Message->DecodeHeader($Message->RawFromAddr);
	$To = $Message->DecodeHeader($Message->RawToAddr);
	$Date = $Message->Date;
	$Subject = $Message->DecodeHeader($Message->RawSubject);
	if ($Message->HasTextBody)
		$MessageBody = $Message->TextBody;
	else
		$MessageBody = $Message->GetPlainFromHtml();
	$Name = str_replace(':','',$Subject);
	$Name = str_replace('\\','',$Name);
	$Name = str_replace('/','',$Name);
	$Name = str_replace('*','',$Name);
	$Name = str_replace('?','',$Name);
	$Name = str_replace('<','',$Name);
	$Name = str_replace('>','',$Name);
	$Name = str_replace('|','',$Name);
	if ($Name == '' || $Name == ' ' || $Name == '_') $Name = 'untitled';
	$Name .= '.txt';
	$File = iconv($InCharset, $Charset, $strFrom).": ".$From."\r\n".
	iconv($InCharset, $Charset, $strDate).": ".$Date."\r\n".
	iconv($InCharset, $Charset, $strTo).": ".$To."\r\n".
	iconv($InCharset, $Charset, $strSubject).": ".$Subject."\r\n\r\n".$MessageBody;
	//disconnection from pop3-server
	$pop3->Disconnect();
}
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-type: text/plain; charset='.$Charset);
header('Content-Disposition: attachment; filename="'.$Name.'"');
header('Content-Length: '.strlen($File));
echo ($File);
?>
