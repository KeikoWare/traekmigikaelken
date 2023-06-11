<?php
error_reporting (E_ALL ^ E_NOTICE);
require './settings_path.php';
$StartError = '';
include './checkready.php';
$BadSessionPath = false;
$session_path = ini_get('session.save_path');
if (strlen($session_path) > 0 && !is_dir($session_path))
	$BadSessionPath = true;
if (!$BadSessionPath) {
	$autostart = ini_get('session.auto_start');
	if ($autostart) {
		session_unset();
	} else {
		session_name('PHPWEBMAILSESSID');
		session_start();
		session_unset();
		session_destroy();
		session_name('PHPWEBMAILSESSID');
		session_start();
	}
}
require './class_dom_xml.php';
require './inc_settings.php';
if ($StartError != '') {
	$config = SetDefaultSettings();
} else {
	$RestoreError = '';
	$config = RestoreSettings($RestoreError);
	if (empty($StartError)) $StartError = $RestoreError;
}
if ($StartError == '' && $BadSessionPath)
	$StartError = 'Path for saving sessions (specified in session.save_path variable in php.ini file) doesn\'t exist or there is no permission to write into that location.<br /><br />WebMail can\'t work properly because it\'s impossible to create new sessions.';
if ($config['intDisableErrorHandling'] == 1)
	error_reporting (E_ALL ^ E_NOTICE);
include './language.php';
$Charset = 'utf-8';
header('Content-type: text/html; charset='.$Charset);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<link rel="stylesheet" href="./skins/<?php echo $config['txtDefaultSkin'];?>/styles.css" type="text/css" />
	<title><?php echo $config['txtWindowTitle'];?></title>
	<script language="JavaScript" type="text/javascript" src="./functions_login.js"></script>
	<script language="JavaScript" type="text/javascript">
		var ajax = <?php if ($config['intAllowAjax'] == 1) echo 'true'; else echo 'false';?>;
		if (SafariDetect())
			ajax = false;
		var http_request = false;
		if (window.XMLHttpRequest) // Mozilla, Safari,...
		{
			http_request = new XMLHttpRequest();
		} else if (window.ActiveXObject) { // IE
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				try {
					http_request = new ActiveXObject("Msxml2.XMLHTTP");
				} catch (e) {
					ajax = false;
				}
			}
		}
		if (ajax == false)
			document.location = './login.php';
	</script>
	<script language="JavaScript" type="text/javascript" src="./language.js"></script>
	<script language="JavaScript" type="text/javascript">
		var user_encoding = 'utf-8';
		var processing_script_url = '<?php echo './processing.php';?>';
		var allow_advanced_login = <?php echo $config['intAllowAdvancedLogin'];?>;
		var hide_login = <?php echo $config['intHideLoginMode'];?>;
		var login_mode = 'standard';
		var page_script_url = '<?php echo './page.php';?>';

		var is_ff = FireFoxDetect();
		var is_opera = OperaDetect();
		// list of all values
		var POP3_SERVER = 0; var POP3_PORT = 1; var SMTP_SERVER = 2;
		var SMTP_PORT = 3; var USE_SMTP_AUTH = 4; var USE_DOMAIN = 5;
		var aValues = [
		'<?php echo $config['txtIncomingMailServer'];?>', '<?php echo $config['intIncomingMailPort'];?>',
		'<?php echo $config['txtOutgoingMailServer'];?>', '<?php echo $config['intOutgoingMailPort'];?>',
		'<?php echo $config['intReqSmtpAuth'];?>', '<?php echo $config['txtDefaultDomainOptional'];?>'];
		// list of all titles
		var LOGIN_INFORMATION = 5; var EMAIL = 6; var LOGIN = 7; var PASSWORD = 8;
		var STANDARD_LOGIN = 9; var ADVANCED_LOGIN = 10; var ENTER = 11;
		var EMPTY_EMAIL = 12; var EMPTY_LOGIN = 13; var EMPTY_SERVERS = 14; var ERROR_HEADER = 15;
		var aTitles = [
		strIncServer, strPort, strOutServer, strPort, strUseAuth,
		strLoginInfo, strEmail, strLogin, strPassword, strStandardLogin, strAdvancedLogin, strEnter,
		strEmptyEmailField, strEmptyLoginField,
		strEmptyServersFields, strErrorsDetected];
		var CONTENT_FORM = 5; var ERROR_DIV = 9; var LOAD_DIV = 10;
		var aCachePageElements = new Array();
	</script>
</head>
<body>
	<div align="center">
<?php include './inc_header.php';?>
		<form name="form_login" id="content" action="./index.php" onsubmit="HideErrorBar(); CheckLoginForm(); return false;">
		</form>
		<div id="error" style="padding:0; margin:0;">
		</div>
<?php include './inc_footer.php';?>
	</div>
	<script language="JavaScript" type="text/javascript">
		CreateErrorBar();
		Processing();
		CreateLoadingBar();
		HideLoadingBar();
		var start_error = '<?php echo addslashes($StartError);?>';
		if (start_error != '') {
			ShowErrorBar(start_error);
		}
	</script>
</body>
</html>
