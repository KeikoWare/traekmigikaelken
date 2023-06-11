<?php
error_reporting (0);
ob_start();
$autostart = ini_get('session.auto_start');
if (!$autostart) {
	session_name("PHPWEBMAILSESSID");
	session_start();
}
require './settings_path.php';
require './inc_functions.php';
require './class_dom_xml.php';
require './inc_settings.php';

disable_magic_quotes_gpc();

$ErrorDesc = '';
$config = RestoreSettings($ErrorDesc);
if ($config['intDisableErrorHandling'] == 1)
	error_reporting (E_ALL ^ E_NOTICE);
require './language.php';
require './class_message.php';

$Action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'list';
$Page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
if (!is_numeric($Page) || $Page < 0)
	$Page = 0;
$Mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
$MessageId = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'error';
$FwdAttach = isset($_REQUEST['att']) ? $_REQUEST['att'] :  'yes';
$Charset = $config['txtDefaultCharset'];
if ($Charset != '')
	header('Content-type: text/html; charset='.$Charset);
else
	header('Content-type: text/html');
$isSessionError = false;
$IsAddAttError = false;
$IsSendAttachError = false;

if (($Action == 'view' || $Action == 'reply' || $Action == 'replyall' || $Action == 'forward' ||
($Action == 'new' && $Mode == 'redirect')) && !is_numeric($MessageId))
	header('Location: ./actions.php?action=list&page='.$Page);

if ($Action == 'login') {
	require('class_pop3.php');
	$pop3 = new POP3();
	$pop3->Password = $_REQUEST['password'];
	if ($Mode == 2) {
		$email = $_REQUEST['email'];
		$pop3->PortNumber = $_REQUEST['inc_port'];
		$pop3->ServerName = $_REQUEST['inc_server'];
		$pop3->UserName = $_REQUEST['login'];
	} else {
		if ($config['intHideLoginMode'] >= 20)
			$email = $_REQUEST['login'].'@'.$config['txtDefaultDomainOptional'];
		else $email = $_REQUEST['email'];
		if ($config['intHideLoginMode'] == 10) $login = $_REQUEST['email'];
		elseif ($config['intHideLoginMode'] == 11){
			$tmpLogin = explode('@', $_REQUEST['email']);
			$login = $tmpLogin[0];}
		elseif ($config['intHideLoginMode'] == 22 || $config['intHideLoginMode'] == 23)
			$login = $_REQUEST['login'].'@'.$config['txtDefaultDomainOptional'];
		else $login = $_REQUEST['login'];
		$pop3->PortNumber = $config['intIncomingMailPort'];
		$pop3->ServerName = $config['txtIncomingMailServer'];
		$pop3->UserName = $login;
	}
	if (empty($config['txtImapFlags']) || $config['txtImapFlags'] == 'no_flags')
		$pop3->ImapFlags = '/pop3';
	else
		$pop3->ImapFlags = $config['txtImapFlags'];
	$pop3->LogFilePath = $config['txtDefaultLogPath'];
	$pop3->EnableLogging = $config['intEnableLogging'];

	$tmpLogin = explode('@', $email);
	$tmpLogin[1] = $email;
	$UserName = $pop3->UserName;
	$pop3Error = '';
	$i = 0;
	do {
		if ($i == 1) {
			if ($pop3->ImapFlags == '/pop3') $pop3->ImapFlags = '/pop3/novalidate-cert';
			else $pop3->ImapFlags = '/pop3';
			$config['txtImapFlags'] = 'no_flags';
		}
		$pop3->UserName = $UserName;
		$pop3->Connect();
		if ($pop3Error == '' && $pop3->IsError && !eregi('Certificate', $pop3->ErrorDesc))
			$pop3Error = $pop3->ErrorDesc;
		if ($pop3->IsError && $config['intAutomaticCorrectLoginSettings'] && 
			($UserName != $tmpLogin[0] || $UserName != $tmpLogin[1])) {
			if ($UserName != $tmpLogin[0]) {
				$pop3->UserName = $tmpLogin[0];
				$pop3->Connect();
			}
			if ($pop3->IsError && $UserName != $tmpLogin[1]) {
				$pop3->UserName = $tmpLogin[1];
				$pop3->Connect();
			}
		}
		$i++;
	} while ($pop3->IsError && $i < 2);

	if ($pop3->IsError) {
		if ($pop3Error != '')
			$ErrorDesc = $pop3Error;
		else
			$ErrorDesc = $pop3->ErrorDesc;
	} else {
		if (empty($config['txtImapFlags']) || $config['txtImapFlags'] == 'no_flags') {
			$config['txtImapFlags'] = $pop3->ImapFlags;
			$Error = SaveSettings($config);
		}
		$_SESSION['wm_email'] = $email;
		$_SESSION['wm_login'] = $pop3->UserName;
		$_SESSION['wm_inc_server'] = $pop3->ServerName;
		$_SESSION['wm_inc_server_port'] = $pop3->PortNumber;
		$_SESSION['wm_password'] = $pop3->Password;
		if ($Mode == 2) {
			$_SESSION['wm_out_server'] = $_REQUEST['out_server'];
			$_SESSION['wm_out_server_port'] = $_REQUEST['out_port'];
			if ($_REQUEST['out_auth'] == 1)
				$_SESSION['wm_out_server_auth'] = 1;
			else
				$_SESSION['wm_out_server_auth'] = 0;
		} else {
			$_SESSION['wm_out_server'] = $config['txtOutgoingMailServer'];
			$_SESSION['wm_out_server_port'] = $config['intOutgoingMailPort'];
			if ($config['intReqSmtpAuth'] == 1)
				$_SESSION['wm_out_server_auth'] = 1;
			else
				$_SESSION['wm_out_server_auth'] = 0;
		}
		if (is_dir($config['txtDefaultTempDir'].$_SESSION['wm_email'])){
			if (file_exists($config['txtDefaultTempDir'])) {
				if ($config['intDisableErrorHandling']) {
					$FolderHandle = opendir($config['txtDefaultTempDir']);
				} else {
					$FolderHandle = @opendir($config['txtDefaultTempDir']);
				}
				if ($FolderHandle) {
					while (($FileName = readdir($FolderHandle)) !== false) {
						if ($FileName != '.' && $FileName != '..') {
							$FileTime = filemtime($config['txtDefaultTempDir'].$FileName);
							$CurrTime = time();
							if (is_file($config['txtDefaultTempDir'].$FileName)) {
								if ($config['intDisableErrorHandling'])
									unlink($config['txtDefaultTempDir'].$FileName);
								else
									@unlink($config['txtDefaultTempDir'].$FileName);
							}
						}
					}
					closedir($FolderHandle);
				}
			}
			if ($FileHandle = opendir($config['txtDefaultTempDir'].$_SESSION['wm_email'])) {
				while (($FileName = readdir($FileHandle)) !== false) {
					if ($FileName != '.' && $FileName != '..') {
						$FileTime = filemtime($config['txtDefaultTempDir'].$_SESSION['wm_email'].'/'.$FileName);
						$CurrTime = time();
						if (($CurrTime-$FileTime)/3600 > 2) {
							if ($config['intDisableErrorHandling'])
								unlink($config['txtDefaultTempDir'].$_SESSION['wm_email'].'/'.$FileName);
							else
								@unlink($config['txtDefaultTempDir'].$_SESSION['wm_email'].'/'.$FileName);
						}
					}
				}
				closedir($FileHandle); 
			}
		} elseif (is_dir($config['txtDefaultTempDir'])) {
			if ($config['intDisableErrorHandling']) {
				mkdir($config['txtDefaultTempDir'].$_SESSION['wm_email']);
			} else {
				@mkdir($config['txtDefaultTempDir'].$_SESSION['wm_email']);
			}
		}
	}
} else {
	if (!isset($_SESSION['wm_email']) || !isset($_SESSION['wm_login']) || !isset($_SESSION['wm_password']) ||
		!isset($_SESSION['wm_inc_server']) || !isset($_SESSION['wm_inc_server_port']) ||
		!isset($_SESSION['wm_out_server']) || !isset($_SESSION['wm_out_server_port']) || !isset($_SESSION['wm_out_server_auth']))
	{
		$isSessionError = true;
	}
}

if (Empty($ErrorDesc) && $isSessionError == false) {
$Email = $_SESSION['wm_email'];
if ($Action == 'delete' || $Action == 'list' || $Action == 'view' ||
$Action == 'reply' || $Action == 'replyall' || $Action == 'forward' ||
$Action == 'login' || $Action == 'new' && $Mode == 'redirect') {
	if ($Action != 'login') {
		require './class_pop3.php';
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
	}
	$pop3->TimeOffset = $config['txtDefaultTimeOffset'];
	if ($pop3->IsError)
		$ErrorDesc = $pop3->ErrorDesc;
	elseif ($Action == 'delete') {
		if (isset($_REQUEST['ids']) && !empty($_REQUEST['ids']))
			//deleting messages from pop3-server
			$pop3->DeleteMessages($_REQUEST['ids']);
	} elseif ($Action == 'list' || $Action == 'login') {
		$MailsPerPage = $config['intMailsPerPage'];
		$LastMsg = $pop3->MessageCount - $Page * $MailsPerPage;
		while ($LastMsg <= 0 && $Page != 0)
		{
			$Page--;
			$LastMsg = $pop3->MessageCount - $Page * $MailsPerPage;
		}
		$FirstMsg = $LastMsg - $MailsPerPage;
		$FirstMsg = (0 < $FirstMsg) ? $FirstMsg + 1 : 1;
		if ($pop3->MessageCount == 0)
			$Messages = Array();
		else
			//getting headers of messages from pop3-server
			$Messages = $pop3->RetrieveHeaders($FirstMsg, $LastMsg);
		for ($i=0, $c = count($Messages); $i<$c; $i++)
		{
			$Messages[$i]->Charset = $Charset;
			$Messages[$i]->FromAddr = $Messages[$i]->DecodeHeader($Messages[$i]->RawFromAddr);
			$Messages[$i]->FromFriendlyName = $Messages[$i]->GetFriendlyName($Messages[$i]->FromAddr);
			$Messages[$i]->Subject = $Messages[$i]->DecodeHeader($Messages[$i]->RawSubject);
			if ($Messages[$i]->Subject == '') $Messages[$i]->Subject = '['.iconv($InCharset, $Charset, $strNoSubject).']';
			if (strlen($Messages[$i]->Subject) > 60) {
				$SubjectArray = explode(' ', $Messages[$i]->Subject);
				$Subject = $SubjectArray[0];
				$j = 1;
				while (strlen($Subject) < 60) {
					$Subject .= ' '.$SubjectArray[$j];
					$j++;
				}
				$Messages[$i]->Subject = $Subject.' ...';
			}
		}
	} elseif ($Action == 'view' || $Action == 'reply' || $Action == 'replyall' || $Action =='forward' ||
				$Action == 'new' && $Mode == 'redirect') {
		if ($MessageId > $pop3->MessageCount)
			$ErrorDesc = iconv($InCharset, $Charset, $DelMsgError);
		else {
			//getting message from pop3-server
			$Message = $pop3->GetMessage($MessageId, $Charset);
			if ($pop3->IsError)
				$ErrorDesc = $pop3->ErrorDesc;
			else {
				$Message->Charset = $Charset;
				$Message->FromAddr = $Message->DecodeHeader($Message->RawFromAddr);
				$Message->ToAddr = $Message->DecodeHeader($Message->RawToAddr);
				$Message->CCAddr = $Message->DecodeHeader($Message->RawCCAddr);
				$Message->Subject = $Message->DecodeHeader($Message->RawSubject);
				switch ($Action){
				case 'new':
					if ($Message->AttachmentsInfo) {
						foreach ($Message->AttachmentsInfo as $AttachInfo) {
							$pop3->ClientRequest = date('H:i:s ').'[Getting part of message: message number '.$MessageId.', part number '.$AttachInfo['id'].']';
							$AttachmentBody = imap_fetchbody($pop3->ConnectionHandle, $MessageId, $AttachInfo['id']);
							$pop3->ServerResponse = date('H:i:s ').'[Got part of message]';
							if ($pop3->EnableLogging) $pop3->LogToFile();
							if ($AttachInfo['encoding'] == 3) $AttachmentBody = imap_base64($AttachmentBody);
							elseif ($AttachInfo['encoding'] == 4) $AttachmentBody = imap_qprint($AttachmentBody);
							$Message->AddAttachmentFromPop3($AttachmentBody, $AttachInfo['type'].'/'.$AttachInfo['subtype'], $AttachInfo['name'], $AttachInfo['cid']);
						}
					}
					break;
				case 'view':
					if ($Mode == '' || $Mode != 0 && $Mode != 1 && $Mode != 2 && $Mode != 10 && $Mode != 11 && $Mode != 12){
						if ($Message->HasTextBody && $Message->HasHtmlBody)
							$Mode = 2;
						else $Mode = 0;
					}
					for ($i=0, $c=count($Message->AttachmentsInfo); $i<$c; $i++)
						$Message->AttachmentsInfo[$i]['name'] = $Message->DecodeHeader($Message->AttachmentsInfo[$i]['name']);
					if ($Message->HasHtmlBody) {
						foreach ($Message->AttachmentsInfo as $AttachInfo)
							if (!empty($AttachInfo['cid'])) {
								$AttachCid = str_replace('>','',str_replace('<','',$AttachInfo['cid']));
								if (strpos($Message->HtmlBody, $AttachCid)){
									$Replacement = 'picture.php?msg_id='.$MessageId.'&part_id='.
									$AttachInfo['id'].'&encoding='.$AttachInfo['encoding'].'&mime='.
									$AttachInfo['type'].'-'.$AttachInfo['subtype'];
									$PatternArray = Array('cid:'.$AttachCid, 'CID:'.$AttachCid, 'Cid:'.$AttachCid);
									$Message->HtmlBody = str_replace($PatternArray, $Replacement, $Message->HtmlBody);
								}
							}
						$Message->HtmlBody = $Message->GetCensoredHtmlBody();
					}
					if ($Message->HasTextBody)
						$Message->TextBody = $Message->GetCensoredTextBody();
					break;
				case 'reply':
				case 'replyall':
				case 'forward':
					$_SESSION['attachments'] = Array();
					$From = $Email;
					switch ($Action) {
						case 'reply':
							$To = $Message->FromAddr;
							$Subject = iconv($InCharset, $Charset, $strReSubject);
							break;
						case 'replyall':
							$To = $Message->GetReplyAddr($Email);
							if ($To == '') $To = $Email;
							$To = str_replace('"', '', $To);
							$Subject = iconv($InCharset, $Charset, $strReSubject);
							break;
						case 'forward':
							$To = '';
							$Subject = iconv($InCharset, $Charset, $strFWDSubject);
							break;
					}
					$CC = '';
					$BCC = '';
					$Subject .= $Message->DecodeHeader($Message->RawSubject);
					if ($Message->HasTextBody)
						$MessageBody = $Message->TextBody;
					else
						$MessageBody = $Message->GetPlainFromHtml($Message->HtmlBody);
					if ($Action == 'forward') {
						$MessageBody = iconv($InCharset, $Charset, $strFWDText)."\r\n\r\n".
							iconv($InCharset, $Charset, $strFrom).': '.EncodeHtml($Message->FromAddr)."\r\n".
							iconv($InCharset, $Charset, $strTo).': '.EncodeHtml($Message->ToAddr)."\r\n".
							iconv($InCharset, $Charset, $strDate).': '.$Message->Date."\r\n".
							iconv($InCharset, $Charset, $strSubject).': '.EncodeHtml($Message->Subject)."\r\n\r\n".
							iconv($InCharset, $Charset, $strFWDQuoteBegin)."\r\n\r\n".
							$MessageBody."\r\n\r\n".
							iconv($InCharset, $Charset, $strFWDQuoteEnd)."\r\n\r\n";
						$_SESSION['attachments'] = Array();
						if ($FwdAttach == 'yes') {
							if (is_dir($config['txtDefaultTempDir'])) {
								$TempDir = $config['txtDefaultTempDir'].$Email;
								if (@is_dir($TempDir)) { 
									foreach ($Message->AttachmentsInfo as $AttachInfo) {
										$tmpName = $pop3->RecordTempAttachment($TempDir, $MessageId, $AttachInfo['id'], $AttachInfo['encoding']);
										if ($tmpName) {
											$tmp = Array(
												'name' => $Message->DecodeHeader($AttachInfo['name']),
												'tmp_name' => $tmpName,
												'size' => $AttachInfo['size'],
												'type' => $AttachInfo['type'].'/'.$AttachInfo['subtype']
											);
											$_SESSION['attachments'][] = $tmp;
										} else {
											$OkAction = 'document.location=\'actions.php?action='.$Action.'&page='.$Page.'&id='.$MessageId.'&att=noattachment\';';
											$ErrorDesc = 'The message cannot be forwarded due to insufficient permissions on the user\'s attachment folder (read and write must be allowed).<br/><br/>The message will be sent without the attachment(s)';
										}
									}
								} else {
									$OkAction = 'document.location=\'actions.php?action='.$Action.'&page='.$Page.'&id='.$MessageId.'&att=noattachment\';';
									$ErrorDesc = 'Folder '.$TempDir.' not found.<br/><br/>The message will be sent without the attachment(s).';
								}
							}
						}
					} else
						$MessageBody = '>'.str_replace("\n", "\r\n>", str_replace("\r\n", "\n", $MessageBody));
					break;
				}//end switch
			}//end else "not error"
		}//end else "message exists"
	}
	//disconnection from pop3-server
	$pop3->Disconnect();
}
if ($Action == 'new') {
	$From = isset($_REQUEST['from']) ? $_REQUEST['from'] : $Email;
	$To = isset($_REQUEST['to']) ? $_REQUEST['to'] : '';
	$CC = isset($_REQUEST['cc']) ? $_REQUEST['cc'] : '';
	$BCC = isset($_REQUEST['bcc']) ? $_REQUEST['bcc'] : '';
	$Subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : '';
	$MessageBody = isset($_REQUEST['message']) ? $_REQUEST['message'] : '';
	switch ($Mode)
	{
		case 'redirect':
		case 'send':
			require './class_smtp.php';
			$smtp = new SMTP();
			$smtp->AuthMethod = $_SESSION['wm_out_server_auth'];
			$smtp->Password = $_SESSION['wm_password'];
			$smtp->PortNumber = $_SESSION['wm_out_server_port'];
			$smtp->ServerName = $_SESSION['wm_out_server'];
			$smtp->UserName = $_SESSION['wm_login'];
			$smtp->LogFilePath = $config['txtDefaultLogPath'];
			$smtp->EnableLogging = $config['intEnableLogging'];
			$smtp->Connect();
			if ($smtp->IsError)
				$ErrorDesc = $smtp->ErrorDesc;
			else {
				switch ($Mode) {
				case 'send':
					$Message = new Message();
					$Message->crlf = ($config['txtCrlf'] == '\n') ? "\n" : "\r\n";
					$Message->RawFromAddr = $Message->EncodeHeaderText('From: ', $From, $Charset, 4);
					$Message->RawToAddr = $Message->EncodeHeaderText('To: ', $To, $Charset, 4);
					if (!empty($CC))
						$Message->RawCCAddr = $Message->EncodeHeaderText('CC: ', $CC, $Charset, 4);
					if(!empty($BCC))
						$Message->RawBCCAddr = $Message->EncodeHeaderText('BCC: ', $BCC, $Charset, 4);
					$Message->RawSubject = $Message->EncodeHeaderText('Subject: ', $Subject, $Charset, 4);
					$Message->HasTextBody = true;
					$Message->HasHtmlBody = false;
					$Message->RawTextBody = $Message->EncodeBodyText($MessageBody, $Charset, 4, 'plain');
					$TempDir = $config['txtDefaultTempDir'].$Email.'/';
					if (!empty($_SESSION['attachments'])) {
						if (is_dir($TempDir)) {
							foreach ($_SESSION['attachments'] as $key => $Attachment) {
								$FileName = (empty($Attachment['name'])) ? '' : $Message->EncodeHeaderText("name=\"", $Attachment['name'], $Charset, 4).'"';
								if (!$Message->AddAttachment($TempDir.$Attachment['tmp_name'], $Attachment['type'], $FileName)) {					
									$IsAddAttError = true;
								}
							}
							if ($IsAddAttError) {
								$ErrorDesc = 'The attachment cannot be attached because the attachment file is write only or doesn\'t exist. The message cannot be sent.';
								$IsSendAttachError = true;
							}
						} else {
							$ErrorDesc = 'The user\'s attachments folder is write only or doesn\'t exist.';
							$IsSendAttachError = true;
						}
					}
					$FromArray = $Message->GetArrayAddr($From);
					$smtp->From = $FromArray[0];
					$smtp->ToArray = $Message->GetArrayAddr($To.', '.$CC.', '.$BCC);
					break;
				case 'redirect':
					$Message->crlf = ($config['txtCrlf'] == '\n') ? "\n" : "\r\n";
					$Message->RawFromAddr = 'From: '.$Message->RawFromAddr;
					$Message->RawToAddr = 'To: '.$Message->RawToAddr;
					$Message->RawSubject = 'Subject: '.$Message->RawSubject;
					if (!empty($CC))
						$Message->RawCCAddr = $Message->EncodeHeaderText('CC: ', $CC, $Charset, 4);
					if(!empty($BCC))
						$Message->RawBCCAddr = $Message->EncodeHeaderText('BCC: ', $BCC, $Charset, 4);
					$Message->RawResentFrom = $Message->EncodeHeaderText('Resent-from: ', $Email, $Charset, 4);
					if ($Message->HasTextBody)
						$Message->RawTextBody = $Message->EncodeBodyText($Message->TextBody, $Charset, 4, 'plain');
					if ($Message->HasHtmlBody)
						$Message->RawHtmlBody = $Message->EncodeBodyText($Message->HtmlBody, $Charset, 4, 'html');
					$smtp->From = $Email;
					$smtp->ToArray = $Message->GetArrayAddr($_REQUEST['toemail']);
					break;
				}
				$smtp->Message = $Message->GetMessageText();
				if ($IsSendAttachError)
					$OkAction = 'history.back();';
				else 
					$smtp->Send();
				if ($smtp->IsError)
					$ErrorDesc = $smtp->ErrorDesc;
				else {
					$smtp->Disconnect();
					if ($smtp->IsError)
						$ErrorDesc = $smtp->ErrorDesc;
				}
			}
			break;
		case 'deattach':
			if (isset($_REQUEST['key']))
			{
				$key = $_REQUEST['key'];
				if (isset($_SESSION['attachments'][$key]))
				{
					if (is_dir($config['txtDefaultTempDir']))
						if (is_dir($config['txtDefaultTempDir'].$Email))
							if (is_file($config['txtDefaultTempDir'].$Email.'/'.$_SESSION['attachments'][$key]['tmp_name']))
								unlink($config['txtDefaultTempDir'].$Email.'/'.$_SESSION['attachments'][$key]['tmp_name']);
					unset($_SESSION['attachments'][$key]);
				}
			}
			break;
		case 'attach':
			if (is_dir($config['txtDefaultTempDir']))
			{
				require './class_upload.php';
				$Upload = new AttachmentUpload();
				$Upload->TempDir = $config['txtDefaultTempDir'].$Email;
				if (@!is_dir($Upload->TempDir)) {
					if ($config['intDisableErrorHandling']) {
						mkdir($Upload->TempDir);
					} else {
						@mkdir($Upload->TempDir);
					}
				}
				$Upload->SizeLimit = $config['intAttachmentSizeLimit'];
				$Upload->Upload();
				if ($Upload->IsError)
					$ErrorDesc = $Upload->ErrorDesc;
			} else
				$ErrorDesc = 'Folder '.$config['txtDefaultTempDir'].' not found';
			break;
		default:
			$_SESSION['attachments'] = Array();
	}
}
}
if (!Empty($ErrorDesc) || $isSessionError == true) {
	if ($isSessionError)
		header('Location: ./login.php');
	else {
		$OkAction = ($OkAction != '') ? $OkAction : 'history.back();';
		include('inc_html_error.php');
	}
} elseif ($Action == 'delete' || $Action == 'new' && ($Mode =='send' || $Mode == 'redirect')) {
	header('Location: ./actions.php?action=list&page='.$Page);
} else {
	if ($Action == 'list' || $Action == 'login')
		include('inc_html_list.php');
	elseif ($Action == 'view')
		include('inc_html_view.php');
	elseif ($Action =='new' || $Action == 'reply' || $Action == 'replyall' || $Action =='forward')
		include('inc_html_new.php');
	elseif ($Action == 'redirect')
		include('inc_html_redirect.php');
}
ob_end_flush();
?>