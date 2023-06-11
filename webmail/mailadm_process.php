<?php
error_reporting (E_ALL ^ E_NOTICE);
require './settings_path.php';
require './inc_functions.php';
require './inc_functions_mailadm.php';
require './class_dom_xml.php';
require './inc_settings.php';
$PassMode = '';
$RestoreError = '';
$Settings = RestoreSettings($RestoreError);
echo $RestoreError;
if (!empty($RestoreError)) {
	header('Location: ./mailadm.php');
}
$Mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

switch($Mode)
{
	case 'wm_login':
		if($_REQUEST['login'] == 'mailadm' && $_REQUEST['password'] == $Settings['txtAdminPassword'])
		{
			$autostart = ini_get('session.auto_start');
			if ($autostart) {
				session_unset();
			} else {
				session_name('PHPWEBMAILADMINSESSID');
				session_start();
				session_unset();
				session_destroy();
				session_name('PHPWEBMAILADMINSESSID');
				session_start();
			}
			$_SESSION['mailadm_password'] = $_REQUEST['password'];
		} else {
			$PassMode = '?error_mode=error';
		}
		break;
	case 'wm_update_form01':
		$tmpDefaultTempDir = CorrectPath((isset($_REQUEST["txtPathForUpload"]) ? $_REQUEST["txtPathForUpload"] : '')).'/';
		$tmpPassword1 = isset($_REQUEST["txtPassword1"]) ? $_REQUEST["txtPassword1"] : '';
		$tmpPassword2 = isset($_REQUEST["txtPassword2"]) ? $_REQUEST["txtPassword2"] : '';
		if(!is_dir($tmpDefaultTempDir)) {
			$ErrorDesc = 'Folder '.$tmpDefaultTempDir.' not found';
			$OkAction = 'document.location=\'mailadm.php\';';
			$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
			include('inc_html_error.php');
			exit;
		} else {
			if ($Settings['intDisableErrorHandling'])
				$FolderHandler = opendir($tmpDefaultTempDir);
			else
				$FolderHandler = @opendir($tmpDefaultTempDir);
			if ($FolderHandler) {
				$tmpInSys = tempnam($tmpDefaultTempDir, 'tmp');
				$tmpName = basename($tmpInSys);
				$tmpFileInFolder = $tmpDefaultTempDir.$tmpName;
				if ($Settings['intDisableErrorHandling'])
					$FileHandler = fopen($tmpFileInFolder, "a+");
				else
					$FileHandler = @fopen($tmpFileInFolder, "a+");
				if ($FileHandler) {
					fclose($FileHandler);
					@unlink($tmpFileInFolder);
				} else {
					$ErrorDesc = 'User\'s attachments folder can\'t be created because web-service has no permission to perform this action<br/><br/>To learn how to grant the appropriate permission, please refer to WebMail documentation:<br/><br/><a href=\'help/installation_instructions_win.html\'>Installation Instructions for Windows</a><br/><a href=\'help/installation_instructions_unix.html\'>Installation Instructions for Unix</a>';
					$OkAction = 'history.back();';
					$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
					include('inc_html_error.php');
					exit;
				}
			} else {
				$ErrorDesc = 'Attachments folder can\'t be open because web-service has no permission to perform this action<br/><br/>To learn how to grant the appropriate permission, please refer to WebMail documentation:<br/><br/><a href=\'help/installation_instructions_win.html\'>Installation Instructions for Windows</a><br/><a href=\'help/installation_instructions_unix.html\'>Installation Instructions for Unix</a>';
				$OkAction = 'history.back();';
				$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
				include('inc_html_error.php');
				exit;
			}
			$Settings['txtDefaultTempDir'] = $tmpDefaultTempDir;
		}
		if ($tmpPassword1 != '*********' && $tmpPassword1 != $tmpPassword2) {
			$ErrorDesc = 'The password you typed do not match. Type the new password in both text boxes';
			$OkAction = 'document.location=\'mailadm.php\';';
			$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
			include('inc_html_error.php');
			exit;
		} elseif ($tmpPassword1 != '*********') {
			$Settings['txtAdminPassword'] = $tmpPassword1;
			$PassMode = "?pass_mode=new";
		}
		$Settings['txtWindowTitle'] = isset($_REQUEST["txtSiteName"]) ? $_REQUEST["txtSiteName"] : '';
		$Settings['txtIncomingMailServer'] = isset($_REQUEST["txtIncomingMail"]) ? $_REQUEST["txtIncomingMail"] : '';
		$Settings['intIncomingMailPort'] = EncodeDataNumeric(isset($_REQUEST["intIncomingMailPort"]) ? $_REQUEST["intIncomingMailPort"] : 0);
		$Settings['txtOutgoingMailServer'] = isset($_REQUEST["txtOutgoingMail"]) ? $_REQUEST["txtOutgoingMail"] : '';
		$Settings['intOutgoingMailPort'] = EncodeDataNumeric(isset($_REQUEST["intOutgoingMailPort"]) ? $_REQUEST["intOutgoingMailPort"] : 0);
		$Settings['intReqSmtpAuth'] = EncodeDataNumeric(isset($_REQUEST["intReqSmtpAuthentication"]) ? $_REQUEST["intReqSmtpAuthentication"] : 0);
		$Settings['intAttachmentSizeLimit'] = EncodeDataNumeric(isset($_REQUEST["intAttachmentSizeLimit"]) ? $_REQUEST["intAttachmentSizeLimit"] : 0);
		$Settings['txtDefaultCharset'] = isset($_REQUEST["txtDefaultUserCharset"]) ? $_REQUEST["txtDefaultUserCharset"] : '';
		$Settings['txtDefaultTimeOffset'] = isset($_REQUEST["txtDefaultTimeOffset"]) ? $_REQUEST["txtDefaultTimeOffset"] : '';
		$ErrorDesc = SaveSettings($Settings);
		if (!empty($ErrorDesc)) {
			$OkAction = false;
			$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
			include('inc_html_error.php');
			exit;
		}
		break;
	case 'wm_update_form02':
		$Settings['intMailsPerPage'] = EncodeDataNumeric(isset($_REQUEST["intMailsPerPage"]) ? $_REQUEST["intMailsPerPage"] : 0);
		$Settings['txtDefaultSkin'] = isset($_REQUEST["txtDefaultSkin"]) ? $_REQUEST["txtDefaultSkin"] : '';
		$Settings['intShowTextLabels'] = EncodeDataNumeric(isset($_REQUEST["intShowTextLabels"]) ? $_REQUEST["intShowTextLabels"] : 0);
		$Settings['intAllowAjax'] = EncodeDataNumeric(isset($_REQUEST["intAllowAjax"]) ? $_REQUEST["intAllowAjax"] : 0);
		$ErrorDesc = SaveSettings($Settings);
		if (!empty($ErrorDesc)) {
			$OkAction = false;
			$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
			include('inc_html_error.php');
			exit;
		}
		break;
	case 'wm_update_form04':
		$Settings['intAllowAdvancedLogin'] = EncodeDataNumeric(isset($_REQUEST["intAllowAdvancedLogin"]) ? $_REQUEST["intAllowAdvancedLogin"] : 0);
		$Settings['intHideLoginMode'] = EncodeDataNumeric(isset($_REQUEST["intHideLogin"]) ? $_REQUEST["intHideLogin"] : 0);
		$Settings['txtDefaultDomainOptional'] = isset($_REQUEST["txtUseDomain"]) ? $_REQUEST["txtUseDomain"] : '';
		$Settings['intAutomaticCorrectLoginSettings'] = EncodeDataNumeric(isset($_REQUEST["intAutomaticHideLogin"]) ? $_REQUEST["intAutomaticHideLogin"] : 0);
		$ErrorDesc = SaveSettings($Settings);
		if (!empty($ErrorDesc)) {
			$OkAction = false;
			$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
			include('inc_html_error.php');
			exit;
		}
		break;
	case 'wm_update_form05':
		$Settings['intEnableLogging'] = EncodeDataNumeric(isset($_REQUEST["intEnableLogging"]) ? $_REQUEST["intEnableLogging"] : 0);
	 	$Path = CorrectPath(isset($_REQUEST["txtPathForLog"]) ? $_REQUEST["txtPathForLog"] : '');
		$Dir = dirname($Path);
		if($Settings['intEnableLogging'] && !is_dir($Dir)) {
			$ErrorDesc = 'Folder '.$Dir.' not found.';
			$OkAction = 'document.location=\'mailadm.php\';';
			$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
			include('inc_html_error.php');
			exit;
		} elseif ($Settings['intEnableLogging'] && is_dir($Dir)) {
			if ($Settings['intDisableErrorHandling']) {
				$FileHandle = fopen($Path, "a+");
			} else {
				$FileHandle = @fopen($Path, "a+");
			}
			if ($FileHandle) {
				if ($Settings['intDisableErrorHandling'])
					fclose($FileHandle);
				else
					@fclose($FileHandle);
			} else {
				$ErrorDesc = 'The web-server has no permission to write into the log file<br/>or<br/>log file not exists<br />'.$Path.'<br/><br/>To learn how to grant the appropriate permission, please refer to WebMail documentation:<br/><br/><a href=\'help/installation_instructions_win.html\'>Installation Instructions for Windows</a><br/><a href=\'help/installation_instructions_unix.html\'>Installation Instructions for Unix</a>';
				$OkAction = 'history.back();';
				$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
				include('inc_html_error.php');
				exit;
			}
			$Settings['txtDefaultLogPath'] = $Path;
		}
		$Settings['intDisableErrorHandling'] = EncodeDataNumeric(isset($_REQUEST["intDisableErrorHandling"]) ? $_REQUEST["intDisableErrorHandling"] : 0);
		$ErrorDesc = SaveSettings($Settings);
		if (!empty($ErrorDesc)) {
			$OkAction = false;
			$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
			include('inc_html_error.php');
			exit;
		}
		break;
}
header('Location: ./mailadm.php'.$PassMode);
?>
