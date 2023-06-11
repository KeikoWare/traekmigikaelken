<?php
function SetDefaultSettings()
{
	global $strIniDir;
	$strIniDir = CorrectPath($strIniDir);

	$Settings = Array();
	$Settings['txtAdminPassword'] = '12345';
	$Settings['intAllowAdvancedLogin'] = 1;
	$Settings['intAllowAjax'] = 1;
	$Settings['intAttachmentSizeLimit'] = 1000200;
	$Settings['intAutomaticCorrectLoginSettings'] = 0;
	$Settings['txtCrlf'] = '\r\n';
	$Settings['txtDefaultCharset'] = 'utf-8';
	$Settings['txtDefaultDomainOptional'] = '';
	$Settings['txtDefaultLogPath'] = $strIniDir.'/log.txt';
	$Settings['txtDefaultSkin'] = 'Hotmail_Style';
	$Settings['txtDefaultTempDir'] = $strIniDir.'/';
	$Settings['txtDefaultTimeOffset'] = '';
	$Settings['intDisableErrorHandling'] = 0;
	$Settings['intEnableLogging'] = 1;
	$Settings['intHideLoginMode'] = 0;
	$Settings['txtImapFlags'] = 'no_flags';
	$Settings['txtIncomingMailServer'] = 'localhost';
	$Settings['intIncomingMailPort'] = 110;
	$Settings['intMailsPerPage'] = 10;
	$Settings['txtOutgoingMailServer'] = 'localhost';
	$Settings['intOutgoingMailPort'] = 25;
	$Settings['intReqSmtpAuth'] = 0;
	$Settings['intShowTextLabels'] = 1;
	$Settings['txtWindowTitle'] = 'WebMail';

	return $Settings;
}

function RestoreSettings(&$ErrorDesc)
{
	global $strIniDir;
	$FileName = $strIniDir.'/settings/settings.xml';

	$Settings = SetDefaultSettings();

	$Dom = new wm_DomXml('1.0','utf-8');
	$Dom->wm_Load($FileName);
	if ($Dom->isLoaded == true) {
		$childNodes = $Dom->GetChildNodes();
		$intCount = $Dom->GetLength($childNodes);
		for ($intCntr=0; $intCntr<$intCount; $intCntr++) {
			$objNode = $Dom->GetNode($childNodes, $intCntr);
			$Name = $Dom->GetName($objNode);
			$Data = $Dom->GetData($objNode);
			switch ($Name) {
				case "AdminPassword":
					$Settings['txtAdminPassword'] = $Data;
					break;
				case "AllowAdvancedLogin":
					if (settype($Data, 'integer') == true) $Settings['intAllowAdvancedLogin'] = $Data;
					break;
				case "AllowAjax":
					if (settype($Data, 'integer') == true) $Settings['intAllowAjax'] = $Data;
					break;
				case "AttachmentSizeLimit":
					if (settype($Data, 'integer') == true) $Settings['intAttachmentSizeLimit'] = $Data;
					break;
				case "AutomaticCorrectLoginSettings":
					if (settype($Data, 'integer') == true) $Settings['intAutomaticCorrectLoginSettings'] = $Data;
					break;
				case 'Crlf':
					$Settings['txtCrlf'] = $Data;
					break;
				case "DefaultCharset":
					$Settings['txtDefaultCharset'] = $Data;
					break;
				case "DefaultDomainOptional":
					$Settings['txtDefaultDomainOptional'] = $Data;
					break;
				case "DefaultLogPath":
					$Dir = CorrectPath(dirname($Data));
					$File = basename($Data);
					if ($File == '') $File = 'log.txt';
					if (strpos($File, '.') == false) $File = $File.'.txt';
					$Parts = explode('.', $File);
					if ($Parts[0] == '') $Parts[0] = 'log';
					if ($Parts[1] == '') $Parts[1] = 'txt';
					$File = $Parts[0].'.'.$Parts[1];
					if (is_dir($Dir))
						$Settings['txtDefaultLogPath'] = $Dir.'/'.$File;
					elseif (is_dir($strIniDir.'/logging/'))
						$Settings['txtDefaultLogPath'] = $strIniDir.'/logging/'.$File;
					else
						$Settings['txtDefaultLogPath'] = $strIniDir.'/'.$File;
					break;
				case "DefaultSkin":
					$Settings['txtDefaultSkin'] = $Data;
					break;
				case "DefaultTempDir":
					if (is_dir($Data))
						$Settings['txtDefaultTempDir'] = CorrectPath($Data).'/';
					elseif (is_dir($strIniDir.'/attachments/'))
						$Settings['txtDefaultTempDir'] = $strIniDir.'/attachments/';
					else
						$Settings['txtDefaultTempDir'] = $strIniDir.'/';
					break;
				case "DefaultTimeOffset":
					$Settings['txtDefaultTimeOffset'] = $Data;
					break;
				case "DisableErrorHandling":
					if (settype($Data, 'integer') == true) $Settings['intDisableErrorHandling'] = $Data;
					break;
				case "EnableLogging":
					if (settype($Data, 'integer') == true) $Settings['intEnableLogging'] = $Data;
					break;
				case "HideLoginMode":
					if (settype($Data, 'integer') == true) $Settings['intHideLoginMode'] = $Data;
					break;
				case 'ImapFlags':
					$Settings['txtImapFlags'] = $Data;
					break;
				case "IncomingMailServer":
					$Settings['txtIncomingMailServer'] = $Data;
					break;
				case "IncomingMailPort":
					if (settype($Data, 'integer') == true) $Settings['intIncomingMailPort'] = $Data;
					break;
				case "MailsPerPage":
					if (settype($Data, 'integer') == true) $Settings['intMailsPerPage'] = $Data;
					break;
				case "OutgoingMailServer":
					$Settings['txtOutgoingMailServer'] = $Data;
					break;
				case "OutgoingMailPort":
					if (settype($Data, 'integer') == true) $Settings['intOutgoingMailPort'] = $Data;
					break;
				case "ReqSmtpAuth":
					if (settype($Data, 'integer') == true) $Settings['intReqSmtpAuth'] = $Data;
					break;
				case "ShowTextLabels":
					if (settype($Data, 'integer') == true) $Settings['intShowTextLabels'] = $Data;
					break;
				case "WindowTitle":
					$Settings['txtWindowTitle'] = $Data;
					break;
			}
		}
	} else {
		$ErrorDesc = 'An error occurred while parsing the settings file <br />or <br />The web-server has no permission to read the settings file. <br/><br/>To learn how to grant the appropriate permission, please refer to WebMail documentation: <br/><br/><a href="help/installation_instructions_win.html">Installation Instructions for Windows</a> <br/><a href="help/installation_instructions_unix.html">Installation Instructions for Unix</a>';
	}
	return $Settings;
}

function SaveSettings($Settings){
	global $strIniDir;
	$dom = new wm_DomXml('1.0', 'utf-8');
	$data = $dom->CreateDomElement('Settings');
	$dom->CreateElementWithData($data, 'AdminPassword', $Settings['txtAdminPassword']);
	$dom->CreateElementWithData($data, 'AllowAjax', $Settings['intAllowAjax']);
	$dom->CreateElementWithData($data, 'AllowAdvancedLogin', $Settings['intAllowAdvancedLogin']);
	$dom->CreateElementWithData($data, 'AttachmentSizeLimit', $Settings['intAttachmentSizeLimit']);
	$dom->CreateElementWithData($data, 'AutomaticCorrectLoginSettings', $Settings['intAutomaticCorrectLoginSettings']);
	$dom->CreateElementWithData($data, 'Crlf', $Settings['txtCrlf']);
	$dom->CreateElementWithData($data, 'DefaultCharset', $Settings['txtDefaultCharset']);
	$dom->CreateElementWithData($data, 'DefaultDomainOptional', $Settings['txtDefaultDomainOptional']);
	$dom->CreateElementWithData($data, 'DefaultLogPath', $Settings['txtDefaultLogPath']);
	$dom->CreateElementWithData($data, 'DefaultSkin', $Settings['txtDefaultSkin']);
	$dom->CreateElementWithData($data, 'DefaultTempDir', $Settings['txtDefaultTempDir']);
	$dom->CreateElementWithData($data, 'DefaultTimeOffset', $Settings['txtDefaultTimeOffset']);
	$dom->CreateElementWithData($data, 'DisableErrorHandling', $Settings['intDisableErrorHandling']);
	$dom->CreateElementWithData($data, 'EnableLogging', $Settings['intEnableLogging']);
	$dom->CreateElementWithData($data, 'ImapFlags', $Settings['txtImapFlags']);
	$dom->CreateElementWithData($data, 'HideLoginMode', $Settings['intHideLoginMode']);
	$dom->CreateElementWithData($data, 'IncomingMailServer', $Settings['txtIncomingMailServer']);
	$dom->CreateElementWithData($data, 'IncomingMailPort', $Settings['intIncomingMailPort']);
	$dom->CreateElementWithData($data, 'MailsPerPage', $Settings['intMailsPerPage']);
	$dom->CreateElementWithData($data, 'OutgoingMailServer', $Settings['txtOutgoingMailServer']);
	$dom->CreateElementWithData($data, 'OutgoingMailPort', $Settings['intOutgoingMailPort']);
	$dom->CreateElementWithData($data, 'ReqSmtpAuth', $Settings['intReqSmtpAuth']);
	$dom->CreateElementWithData($data, 'ShowTextLabels', $Settings['intShowTextLabels']);
	$dom->CreateElementWithData($data, 'WindowTitle', $Settings['txtWindowTitle']);
	$dom->AppendDomElement($data);
	$FileContent = $dom->SaveDomXml();
	$FileName = $strIniDir.'/settings/settings.xml';
	if ($Settings['intDisableErrorHandling']) {
		$FileHandler = fopen($FileName, 'w');
	} else {
		$FileHandler = @fopen($FileName, 'w');
	}
	if ($FileHandler) {
		fwrite($FileHandler, $FileContent);
		fclose($FileHandler);
	} else {
		return 'The web-server has no permission to write into the settings file<br/>or<br/>settings file not exists<br/>'.$FileName.'<br/><br/>To learn how to grant the appropriate permission, please refer to WebMail documentation:<br/><br/><a href=\'help/installation_instructions_win.html\'>Installation Instructions for Windows</a><br/><a href=\'help/installation_instructions_unix.html\'>Installation Instructions for Unix</a>';
	}
	return '';
}
?>
