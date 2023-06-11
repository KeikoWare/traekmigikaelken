<?php
error_reporting(E_ALL ^ E_NOTICE);
require './settings_path.php';
require './inc_functions.php';
require './class_dom_xml.php';
require './inc_settings.php';

disable_magic_quotes_gpc();

$ErrorDesc = '';
$isCriticalError = false;

$config = RestoreSettings($ErrorDesc);
if (!empty($ErrorDesc)) {
	$isCriticalError = true;
}

require './class_message.php';
require './language.php';
$autostart = ini_get('session.auto_start');
if (!$autostart) {
	session_name("PHPWEBMAILSESSID");
	session_start();
}
set_time_limit(120);

$Action = $_REQUEST['action'];
$Request = isset($_REQUEST['request']) ? $_REQUEST['request'] : '';
$MessageId = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
$Charset = 'utf-8';

if ($Request == 'login') {
	require './class_pop3.php';
	$pop3 = new POP3();
	$pop3->Password = $_REQUEST['password'];
	$Mode = $_REQUEST['login_mode'];
	if ($Mode == 'advanced') {
		$email = $_REQUEST['email'];
		$pop3->PortNumber = $_REQUEST['adv_inc_server_port'];
		$pop3->ServerName = $_REQUEST['adv_inc_server'];
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
		if ($Mode == 'advanced') {
			$_SESSION['wm_out_server'] = $_REQUEST['adv_out_server'];
			$_SESSION['wm_out_server_port'] = $_REQUEST['adv_out_server_port'];
			if ($_REQUEST['adv_out_server_auth'] == 1)
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
			if ($config['intDisableErrorHandling']) {
				$FileHandle = opendir($config['txtDefaultTempDir'].$_SESSION['wm_email']);
			} else {
				$FileHandle = @opendir($config['txtDefaultTempDir'].$_SESSION['wm_email']);
			}
			if ($FileHandle) {
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
		} elseif (is_dir($config['txtDefaultTempDir'])){
			if ($config['intDisableErrorHandling']) 
				mkdir($config['txtDefaultTempDir'].$_SESSION['wm_email']);
			else
				@mkdir($config['txtDefaultTempDir'].$_SESSION['wm_email']);
		}
	}
}elseif (!isset($_SESSION['wm_login']) || !isset($_SESSION['wm_password']) ||
	!isset($_SESSION['wm_inc_server']) || !isset($_SESSION['wm_inc_server_port']) ||
	!isset($_SESSION['wm_out_server']) || !isset($_SESSION['wm_out_server_port']) ||
	!isset($_SESSION['wm_out_server_auth']))
{
	$ErrorDesc = 'session is empty';
}
if ($ErrorDesc == '') {
	$Email = $_SESSION['wm_email'];
	if ($Action == 'delete' || $Request == 'messages' || $Request == 'view' ||
	$Request == 'view_original' || $Request == 'view_original_fwd' ||
	$Request == 'login' || $Action == 'redirect') {
		if ($Request != 'login') {
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
		if (!$pop3->IsError && $Action == 'delete') {
			if (isset($_REQUEST['id_message']) && !empty($_REQUEST['id_message']))
				//deleting messages from pop3-server
				$pop3->DeleteMessages($_REQUEST['id_message']);
		}
		if ($pop3->IsError)
			$ErrorDesc = $pop3->ErrorDesc;
		elseif ($Request == 'messages') {
			$Page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
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
				$Messages[$i]->FromFriendlyName = preg_replace("/[\\x00-\\x08\\x0B-\\x0C\\x0E-\\x1F]/", ' ', $Messages[$i]->FromFriendlyName);
				$Messages[$i]->Subject = preg_replace("/[\\x00-\\x08\\x0B-\\x0C\\x0E-\\x1F]/", ' ', $Messages[$i]->Subject);
				$Messages[$i]->FromFriendlyName = iconv($Charset, $Charset.'//IGNORE', $Messages[$i]->FromFriendlyName);
				$Messages[$i]->Subject = iconv($Charset, $Charset.'//IGNORE', $Messages[$i]->Subject);
			}
		} elseif ($Request == 'view' || $Request == 'view_original' || $Request == 'view_original_fwd' ||
				$Action == 'redirect') {
			$MessageId = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
			if ($MessageId > $pop3->MessageCount || $MessageId <= 0)
				$ErrorDesc = 'This message has already been deleted from the mail server';
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
					if ($Action == 'redirect') {
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
					} elseif ($Request == 'view') {
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
						if ($Message->HasTextBody) {
							$Message->TextBody = $Message->GetCensoredTextBody();
						}
					} elseif ($Request == 'view_original' || $Request == 'view_original_fwd') {
						if (!$Message->HasTextBody) {
							$Message->HasTextBody = true;
							$Message->TextBody = $Message->GetPlainFromHtml($Message->HtmlBody);
						}
						if ($Request == 'view_original_fwd' && is_dir($config['txtDefaultTempDir'])) {
							$TempDir = $config['txtDefaultTempDir'].$_SESSION['wm_email'];
							if (is_dir($TempDir)) {
								$isAttError = false;
								for ($i=0, $c=count($Message->AttachmentsInfo); $i<$c; $i++) {
									$Message->AttachmentsInfo[$i]['tmp_name'] = $pop3->RecordTempAttachment($TempDir, $Message->MessageId, $Message->AttachmentsInfo[$i]['id'], $Message->AttachmentsInfo[$i]['encoding']);
									if ($Message->AttachmentsInfo[$i]['tmp_name'] == false)
										$isAttError = true;
								}
								if ($isAttError == true) {
									$ErrorDesc = 'The message cannot be forwarded due to insufficient permissions on the user\'s attachment folder (read and write must be allowed).<br/><br/>The message will be sent without the attachment(s).';
									$Message->AttachmentsInfo = array(); 
								}
							} else {
								$ErrorDesc = 'Folder '.$TempDir.' not found<br/><br/>The message will be sent without the attachment(s).';
								$Message->AttachmentsInfo = array(); 
							}
						}// end if
					}//end if
				}//end else "not error"
			}//end else "message exists"
		}
		//disconnection from pop3-server
		$pop3->Disconnect();
	}
	if ($Action == 'send' || $Action == 'redirect') {
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
			switch ($Action) {
			case 'send':
				$SendCharset = $config['txtDefaultCharset'];
				$Message = new Message();
				$Message->crlf = ($config['txtCrlf'] == '\n') ? "\n" : "\r\n";
				$Message->RawFromAddr = $Message->EncodeHeaderText('From: ', iconv($Charset, $SendCharset, $_REQUEST['str_from']), $SendCharset, 4);
				$Message->RawToAddr = $Message->EncodeHeaderText('To: ', iconv($Charset, $SendCharset, $_REQUEST['str_to']), $SendCharset, 4);
				if (!empty($_REQUEST['str_cc']))
					$Message->RawCCAddr = $Message->EncodeHeaderText('CC: ', iconv($Charset, $SendCharset, $_REQUEST['str_cc']), $SendCharset, 4);
				if (!empty($_REQUEST['str_bcc']))
					$Message->RawBCCAddr = $Message->EncodeHeaderText('BCC: ', iconv($Charset, $SendCharset, $_REQUEST['str_bcc']), $SendCharset, 4);
				$Message->RawSubject = $Message->EncodeHeaderText('Subject: ', iconv($Charset, $SendCharset, $_REQUEST['str_subject']), $SendCharset, 4);
				$Message->HasTextBody = true;
				$Message->HasHtmlBody = false;
				$Message->RawTextBody = $Message->EncodeBodyText(iconv($Charset, $SendCharset, $_REQUEST['message']), $SendCharset, 4, 'plain');
				if (isset($_REQUEST['temp'])) $TempDir = $_REQUEST['temp'];
				else $TempDir = '';
				if (!is_dir($TempDir)) {
					$ErrorDesc = 'Folder '.$TempDir.' not found';
					$isCriticalError = true;
				} else {
					$TempNames = explode('|%|', $_REQUEST['atts_temp_names']);
					$RealNames = explode('|%|', $_REQUEST['atts_real_names']);
					$Types = explode('|%|', $_REQUEST['atts_types']);
					for ($i=0, $c=count($TempNames); $i<$c; $i++) {
						$FileName = (empty($RealNames[$i])) ? '' : $Message->EncodeHeaderText("name=\"", iconv($Charset, $SendCharset, $RealNames[$i]), $SendCharset, 4).'"';
						if (!empty($TempNames[$i])) {
							if(!$Message->AddAttachment($TempDir.'/'.$TempNames[$i], $Types[$i], $FileName)) {
								$isCriticalError = true;
							}
						}
					}
					if ($isCriticalError){
						$ErrorDesc = 'The attachment cannot be attached because the attachment file is write only or doesn\'t exist. The message cannot be sent.';
					}
				}
				$FromArray = $Message->GetArrayAddr($_REQUEST['str_from']);
				$smtp->From = $FromArray[0];
				$smtp->ToArray = $Message->GetArrayAddr($_REQUEST['str_to'].', '.$_REQUEST['str_cc'].', '.$_REQUEST['str_bcc']);
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
				$smtp->ToArray = $Message->GetArrayAddr($_REQUEST['str_to']);
				break;
			}
			$smtp->Message = $Message->GetMessageText();
			if (!$isCriticalError)
				$smtp->Send();
			if ($smtp->IsError)
				$ErrorDesc = $smtp->ErrorDesc;
			else {
				$smtp->Disconnect();
				if ($smtp->IsError)
					$ErrorDesc = $smtp->ErrorDesc;
			}
		}
	}
}

$Dom = new wm_DomXml('1.0', $Charset);
if ($ErrorDesc == 'session is empty') {
	$Data = $Dom->CreateDomElement('webmail_empty');
} elseif ($isCriticalError){
	$Data = $Dom->CreateDomElement('webmail_data');
	if ($ErrorDesc != '')
		$Dom->AddError($Data, $ErrorDesc);
} elseif ($ErrorDesc != '') {
	$Data = $Dom->CreateDomElement('webmail_data');
	$Dom->AddError($Data, $ErrorDesc);
} else {
	$Data = $Dom->CreateDomElement('webmail_data');
	if ($Request == 'messages') {
		$Messes = $Dom->CreateNewElement('messages');
		$Dom->AddAttributes($Messes, Array('page'=>$Page, 'limit'=>0, 'count'=>$pop3->MessageCount, 'inbox_size'=>0));
		foreach ($Messages as $Message) {
			$Mess = $Dom->CreateNewElement('message');
			$Dom->AddAttributes($Mess, Array('id'=>$Message->MessageId, 'has_attachments'=>$Message->HasAttachments, 'importance'=>$Message->Importance));
			$Dom->CreateElementWithCDATA($Mess, 'from', EncodeHTML($Message->FromFriendlyName));
			$Dom->CreateElementWithCDATA($Mess, 'size', $Message->Size);
			$Dom->CreateElementWithCDATA($Mess, 'subject', EncodeHTML($Message->Subject));
			$Dom->CreateElementWithCDATA($Mess, 'date', $Message->Date);
			$Dom->AppendElement($Messes, $Mess);
		}
		$Dom->AppendElement($Data, $Messes);
	}
	if ($Request == 'view' || $Request == 'view_original' || $Request == 'view_original_fwd') {
		if (!empty($Message->Headers)) {
			$Headers = preg_replace("/[\\x00-\\x08\\x0B-\\x0C\\x0E-\\x1F]/", ' ', str_replace("\n", '<br/>', str_replace("\r\n", '<br/>', EncodeHtml($Message->Headers))));
			$Headers = iconv($Charset, $Charset.'//IGNORE', $Headers);
		} else $Headers = ' ';
		if (!empty($Message->FromAddr)) {
			$From = preg_replace("/[\\x00-\\x08\\x0B-\\x0C\\x0E-\\x1F]/", ' ', EncodeHtml($Message->FromAddr));
			$From = iconv($Charset, $Charset.'//IGNORE', $From);
		} else $From = ' ';
		if (!empty($Message->ToAddr)) {
			$To = preg_replace("/[\\x00-\\x08\\x0B-\\x0C\\x0E-\\x1F]/", ' ', EncodeHtml($Message->ToAddr));
			$To = iconv($Charset, $Charset.'//IGNORE', $To);
		} else $To = ' ';
		if (!empty($Message->CCAddr)) {
			$CC = preg_replace("/[\\x00-\\x08\\x0B-\\x0C\\x0E-\\x1F]/", ' ', EncodeHtml($Message->CCAddr));
			$CC = iconv($Charset, $Charset.'//IGNORE', $CC);
		} else $CC = ' ';
		if (!empty($Message->Subject)) {
			$Subject = preg_replace("/[\\x00-\\x08\\x0B-\\x0C\\x0E-\\x1F]/", ' ', EncodeHtml($Message->Subject));
			$Subject = iconv($Charset, $Charset.'//IGNORE', $Subject);
		} else $Subject = ' ';
		if ($Message->HasHtmlBody && !empty($Message->HtmlBody)) {
			$HtmlBody = preg_replace("/[\\x00-\\x08\\x0B-\\x0C\\x0E-\\x1F]/", ' ', $Message->HtmlBody);
			$HtmlBody = iconv($Charset, $Charset.'//IGNORE', $HtmlBody);
		} else $HtmlBody = ' ';
		if ($Message->HasTextBody && !empty($Message->TextBody)) {
			$TextBody = preg_replace("/[\\x00-\\x08\\x0B-\\x0C\\x0E-\\x1F]/", ' ', $Message->TextBody);
			$TextBody = iconv($Charset, $Charset.'//IGNORE', $TextBody);
		} else $TextBody = ' ';
		$Html = ($Message->HasHtmlBody) ? 1 : 0;
		$Text = ($Message->HasTextBody) ? 1 : 0;
		if (is_dir($config['txtDefaultTempDir'])) {
			$TempDir = $config['txtDefaultTempDir'].$_SESSION['wm_email'];
			if (@!is_dir($TempDir))
				@mkdir($TempDir);
			$AttachCount = count($Message->AttachmentsInfo);
		} else
			$AttachCount = 0;

		$Mess = $Dom->CreateNewElement('message');
		$Dom->AddAttributes($Mess, Array('id'=>$Message->MessageId, 'html'=>$Html, 'txt'=>$Text, 'number_attachments'=>$AttachCount, 'importance'=>$Message->Importance, 'count'=>$pop3->MessageCount));
		$Dom->CreateElementWithCDATA($Mess, 'from', $From);
		$Dom->CreateElementWithCDATA($Mess, 'to', $To);
		$Dom->CreateElementWithCDATA($Mess, 'cc', $CC);
		$Dom->CreateElementWithCDATA($Mess, 'subject', $Subject);
		$Dom->CreateElementWithCDATA($Mess, 'date', EncodeHtml($Message->Date));
		$Dom->CreateElementWithCDATA($Mess, 'headers', $Headers);
		if ($Html) $Dom->CreateElementWithCDATA($Mess, 'html_message', $HtmlBody);
		if ($Text) $Dom->CreateElementWithCDATA($Mess, 'txt_message', $TextBody);
		if ($AttachCount > 0){
			$Element = $Dom->CreateNewElement('attachments');
			$Dom->AddAttribute($Element, 'count', $AttachCount);
			foreach ($Message->AttachmentsInfo as $AttachInfo) {
				$Name = $Message->DecodeHeader($AttachInfo['name']);
				$Attach = $Dom->CreateNewElement('attachment');
				$Dom->AddAttributes($Attach, Array('id'=>$AttachInfo['id'], 'size'=>$AttachInfo['size'], 'filename'=>trim($Name)));
				if ($Request == 'view_original_fwd')
					$Dom->AddAttributes($Attach, Array('filename_temp'=>$AttachInfo['tmp_name'], 'type'=>$AttachInfo['type'].'/'.$AttachInfo['subtype']));
				if ($Request == 'view') {
					$Href = 'open_img.php?msg_id='.$Message->MessageId.'&part_id='.$AttachInfo['id'].'&name='.
						urlencode($Name).'&encoding='.$AttachInfo['encoding'].'&mime='.
						$AttachInfo['type'].'-'.$AttachInfo['subtype'];
					$Dom->CreateElementWithCDATA($Attach, 'view', $Href);
					$Href = 'download.php?msg_id='.$Message->MessageId.'&part_id='.$AttachInfo['id'].'&name='.
						urlencode($Name).'&encoding='.$AttachInfo['encoding'].'&mime='.
						$AttachInfo['type'].'-'.$AttachInfo['subtype'];
					$Dom->CreateElementWithCDATA($Attach, 'download', $Href);
				}
				$Dom->AppendElement($Element, $Attach);
			}
			$Dom->AppendElement($Mess, $Element);
		}
		$Dom->AppendElement($Data, $Mess);
	}
}
$Dom->AppendDomElement($Data);
$File = $Dom->SaveDomXml();

header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="01.xml"');
header('Content-Length: '.strlen($File));
echo $File;
?>