<?php
error_reporting (E_ALL);
$autostart = ini_get('session.auto_start');
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
require './settings_path.php';
$StartError = '';
include './checkready.php';
require './class_dom_xml.php';
require './inc_settings.php';
$ErrorDesc = '';
if ($StartError != '') {
	$config = SetDefaultSettings();
} else {
	$config = RestoreSettings($ErrorDesc);
	if (empty($StartError)) $StartError = $ErrorDesc ;
}
if ($StartError == '' && $BadSessionPath)
	$StartError = 'Path for saving sessions (specified in session.save_path variable in php.ini file) doesn\'t exist or there is no permission to write into that location.<br /><br />WebMail can\'t work properly because it\'s impossible to create new sessions.';
if ($config['intDisableErrorHandling'] == 1)
	error_reporting (E_ALL ^ E_NOTICE);
require './language.php';
//$Mode == 1 - standard panel
//$Mode == 2 - advanced panel
$Mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 1;
$Charset = ($config['txtDefaultCharset'] == '') ? 'utf-8' : $config['txtDefaultCharset'];
header('Content-type: text/html; charset='.$Charset);
//$StandardMode == 0 - show email and login fields
//$StandardMode == 10 - hide login field and use email as login
//$StandardMode == 11 - hide login field and account-name as login
//$StandardMode == 20 - hide email field
//$StandardMode == 21 - hide email field and display domain after login field
//$StandardMode == 22 - hide email field and use login as concatenation of "Login" field + "@" + domain
//$StandardMode == 23 - hide email field and display domain after login field and use login as
						//concatenation of "Login" field + "@" + domain
$StandardMode = $config['intHideLoginMode'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<title><?php echo $config['txtWindowTitle'];?></title>
	<link rel="stylesheet" href="./skins/<?php echo $config['txtDefaultSkin']?>/styles.css" type="text/css" />
	<script language="JavaScript" type="text/javascript" src="./inc_html_functions.js"></script>
	<script language="JavaScript" type="text/javascript">
		function EmailFocus()
		{
			document.form_login.email.select();
		}

		function LoginFocus()
		{
<?php
if ($StandardMode < 20 || $Mode == 2) {
?>
			if( document.form_login.login.value.length == 0 )
				document.form_login.login.value = document.form_login.email.value;
			document.form_login.login.select();
<?php
}
?>
		}

		function PasswordFocus()
		{
			document.form_login.password.select();
		}

		function CheckLoginForm()
		{
<?php
if (empty($StartError)) {?>
			var login_ok = 1;
<?php
if ($StandardMode < 20 || $Mode == 2) {
?>
			if (document.form_login.email.value.length == 0)
			{
				alert('<?php echo iconv($InCharset, $Charset, $JsEmptyEmailField);?>');
				login_ok = 0;
			}
<?php
}
if ($StandardMode == 0 || $StandardMode >= 20 || $Mode == 2) {
?>
			if (login_ok == 1 && document.form_login.login.value.length == 0)
			{
				alert('<?php echo iconv($InCharset, $Charset, $JsEmptyLoginField);?>');
				login_ok = 0;
			}
<?php
}
if ($Mode == 2) {
?>
			if (login_ok == 1 && (document.form_login.inc_server.value.length == 0 ||
			document.form_login.inc_port.value.length == 0 || document.form_login.out_server.value.length == 0 ||
			document.form_login.out_port.value.length == 0))
			{
				alert('<?php echo iconv($InCharset, $Charset, $JsEmptyHostField);?>');
				login_ok = 0;
			}
<?php
}
?>
			if (login_ok == 1)
			{
				document.form_login.submit();
			}
<?php } ?>
		}
	</script>
</head>
<body>
  <div align="center">
<?php
include './inc_header.php';
?>
	  <form name="form_login" action="./actions.php?action=login&mode=<?php echo $Mode;?>" method="post">
		<table class="wm_dialog">
		  <tr>
			<td class="wm_dialog_login_header" colspan="4"><?php echo iconv($InCharset, $Charset, $strTitleLogin);?></td>
		  </tr>
<?php
if ($StandardMode < 20 || $Mode == 2) {
?>
		  <tr>
			<td class="wm_dialog_login_field"><?php echo iconv($InCharset, $Charset, $strEmail);?>: </td>
			<td class="wm_dialog_edit" colspan="3" width="160px">
				<input type="text" class="wm_login_input" name="email" onfocus="EmailFocus();"/>
			</td>
		  </tr>
<?php
}
if ($StandardMode == 0 || $StandardMode >= 20 || $Mode == 2) {
?>
		  <tr>
			<td class="wm_dialog_login_field"><?php echo iconv($InCharset, $Charset, $strLogin);?>: </td>
<?php
	if ($StandardMode == 21 || $StandardMode == 23) {
?>
			<td class="wm_dialog_edit" colspan="2" width="130px">
				<input type="text" class="wm_login_input" name="login" onfocus="LoginFocus();"/>
			</td>
			<td class="wm_dialog_edit">
				@<?php echo $config['txtDefaultDomainOptional'];?>
			</td>
<?php
	} else {
?>
			<td class="wm_dialog_edit" colspan="3" width="160px">
				<input type="text" class="wm_login_input" name="login" onfocus="LoginFocus();"/>
			</td>
<?php
	}
?>
		  </tr>
<?php
}
?>
		  <tr>
			<td class="wm_dialog_login_field"><?php echo iconv($InCharset, $Charset, $strPassword);?>: </td>
			<td class="wm_dialog_edit" colspan="3" width="160px">
				<input type="password" class="wm_login_input" name="password" onfocus="PasswordFocus();"/>
			</td>
		  </tr>
<?php
if ($Mode == 2) {
?>
		  <tr>
			<td class="wm_dialog_login_field"><?php echo iconv($InCharset, $Charset, $strAcctIncomingServer);?>: </td>
			<td class="wm_dialog_edit">
				<input type="text" class="wm_input" name="inc_server" size="10" value="<?php echo $config['txtIncomingMailServer'];?>"/>
			</td>
			<td class="wm_dialog_login_field"><?php echo iconv($InCharset, $Charset, $strAcctPort);?>: </td>
			<td class="wm_dialog_edit">
				<input type="text" class="wm_input" name="inc_port" size="2" value="<?php echo $config['intIncomingMailPort'];?>" />
			</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_login_field"><?php echo iconv($InCharset, $Charset, $strAcctSMTPServer);?>: </td>
			<td class="wm_dialog_edit">
				<input type="text" class="wm_input" name="out_server" size="10" value="<?php echo $config['txtOutgoingMailServer'];?>" />
			</td>
			<td class="wm_dialog_login_field"><?php echo iconv($InCharset, $Charset, $strAcctPort);?>: </td>
			<td class="wm_dialog_edit">
				<input type="text" class="wm_input" name="out_port" size="2" value="<?php echo $config['intOutgoingMailPort'];?>" />
			</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_login_field" colspan="4">
				<input type="checkbox" style="vertical-align: middle;" name="out_auth" id="out_auth" <?php if ($config['intReqSmtpAuth']) echo 'checked="checked"';?>/>
				<label for="out_auth"><?php echo iconv($InCharset, $Charset, $strUseSMTPauthLogin);?></label>
			</td>
		  </tr>
<?php
}
?>
		  <tr>
			<td class="wm_dialog_login_field" colspan="4">
<?php
if ($config['intAllowAdvancedLogin']) {
	if ($Mode == 1) {
?>
				<span class="wm_dialog_login_switcher">
					<a href="./login.php?mode=2" class="wm_reg"><?php echo iconv($InCharset, $Charset, $strAdvancedLogin);?></a>
				</span>
<?php
	} else {
?>
				<span class="wm_dialog_login_switcher">
					<a href="./login.php?mode=1" class="wm_reg"><?php echo iconv($InCharset, $Charset, $strStandardLogin);?></a>
				</span>
<?php
	}
} else {
	echo '&nbsp;';
}
?>
				<span class="wm_dialog_login_button">
					<input type="submit" class="wm_button" name="enter" value="<?php echo iconv($InCharset, $Charset, $strEnterButton);?>" onclick="CheckLoginForm(); return false;"/>
				</span>
			</td>
		  </tr>
		</table>
	  </form>
<?php
if (!empty($StartError)) {?>
		<div class="wm_error_div" align="center" valign="middle">
			<div class="wm_error_header">Error(s) detected</div>
			<div class="wm_error_text"><?php echo $StartError;?></div>
		</div>
<?php
}
include './inc_footer.php';
?>
  </div>
</body>
</html>
