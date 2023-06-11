<?php
error_reporting (E_ALL ^ E_NOTICE);

$BadSessionPath = false;
$session_path = ini_get('session.save_path');
if (strlen($session_path) > 0 && !is_dir($session_path))
	$BadSessionPath = true;
$autostart = ini_get('session.auto_start');
if (!$autostart && !$BadSessionPath) {
	session_name("PHPWEBMAILADMINSESSID");
	session_start();
}
require './settings_path.php';
$StartError = '';
include './checkready.php';
require './inc_functions_mailadm.php';
require './class_dom_xml.php';
require './inc_settings.php';
$RestoreError = '';
$Settings = RestoreSettings($RestoreError);
if (empty($StartError)) $StartError = $RestoreError;
if (!empty($StartError)) {
	$ErrorDesc = $StartError;
	$OkAction = false;
	$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
	include('inc_html_error.php');
	exit;
}
$PassMode = isset($_REQUEST['pass_mode']) ? $_REQUEST['pass_mode'] : '';
$ErrorMode = isset($_REQUEST['error_mode']) ? $_REQUEST['error_mode'] : '';
if ($PassMode == 'new') $_SESSION['mailadm_password'] = $Settings['txtAdminPassword'];
$Mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
if ($Mode == 'exit') $_SESSION['mailadm_password'] = '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<link rel="stylesheet" href="./skins/<?php echo $Settings['txtDefaultSkin'];?>/styles.css" type="text/css" />
	<title><?php echo $Settings['txtWindowTitle'];?> - Administration</title>
	<script language="JavaScript" type="text/javascript" src="./mailadm.js"></script> 
</head>

<body>
	<div align="center">
<?php
include './inc_header.php';
if(!isset($_SESSION['mailadm_password']) || $_SESSION['mailadm_password'] != $Settings['txtAdminPassword'])
{
	$ErrorDesc = SaveSettings($Settings);
	if (!empty($ErrorDesc)) {
		$OkAction = false;
		$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
		include('inc_html_error.php');
		exit;
	}
?>
	<form name="form_login" action="mailadm_process.php" method="post">
	<input type="hidden" name="mode" value="wm_login">
	  <table class="wm_mailadm_dialog">
		<tr>
		  <td colspan="2" class="wm_mailadm_dialog_header">Administration Login</td>
		</tr>
		<tr>
		  <td class="wm_dialog_login_field">Login: </td>
		  <td class="wm_dialog_edit"><input type="text" class="wm_input" name="login" /></td>
		</tr>
		<tr>
		  <td class="wm_dialog_login_field">Password: </td>
		  <td class="wm_dialog_edit"><input type="password" class="wm_input" name="password" /></td>
		</tr>
		<tr>
		  <td colspan="2" class="wm_dialog_button_field">
			<input type="submit" class="wm_button" name="submit2" value="LogIn" />
		  </td>
		</tr>
	  </table>
	</form>
<?php
if ($ErrorMode == 'error' || $BadSessionPath){
$ErrorDesc = 'Wrong Login and or Password';
if ($BadSessionPath) $ErrorDesc = 'Path for saving sessions (specified in session.save_path variable in php.ini file) doesn\'t exist or there is no permission to write into that location.<br /><br />WebMail can\'t work properly because it\'s impossible to create new sessions.';
?>
	<div align="center" style="margin-top:40px">
		<div class="wm_error_div" align="center" valign="middle">
			<div class="wm_error_header">Error(s) detected</div>
			<div class="wm_error_text"><?php echo $ErrorDesc;?></div>
		</div>
	</div>
<?php
}
} else {
	switch($Mode)
	{
		case 'wm_settings':
?>
	<form name="wm_settings" action="mailadm_process.php" method="post">
	<input type="hidden" name="mode" value="wm_update_form01">
		<table class="wm_mailadm_dialog" width="430">
		  <tr>
			<td class="wm_mailadm_dialog_settings_header" colspan="4">WebMail Settings</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_field" width="130">Site Name: </td>
			<td class="wm_dialog_edit" colspan="3">
				<input type="text" class="wm_input" name="txtSiteName" size="44" maxlength="100" value="<?php echo htmlspecialchars($Settings['txtWindowTitle']);?>" />
			</td>
		  </tr>
		  <tr>
			<td class="wm_mailadm_dialog_settings_header" colspan="4">Default Mail Servers</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_field">Incoming Mail: </td>
			<td width="100" class="wm_dialog_edit">
				<input type="text" class="wm_input" name="txtIncomingMail" maxlength="100" value="<?php echo htmlspecialchars($Settings['txtIncomingMailServer']);?>" />
			</td>
			<td class="wm_dialog_edit" colspan="2">Port:&nbsp;
				<input type="text" class="wm_input" name="intIncomingMailPort" size="3" maxlength="4" value="<?php echo $Settings['intIncomingMailPort'];?>" />&nbsp;&nbsp;POP3
			</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_field">Outgoing Mail: </td>
			<td class="wm_dialog_edit">
				<input type="text" class="wm_input" name="txtOutgoingMail" maxlength="100" value="<?php echo htmlspecialchars($Settings['txtOutgoingMailServer']);?>" />
			</td>
			<td class="wm_dialog_edit" colspan="2">Port:&nbsp;
				<input type="text" class="wm_input" name="intOutgoingMailPort" size="3" maxlength="4" value="<?php echo $Settings['intOutgoingMailPort'];?>" />
			</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_field">&nbsp;</td>
			<td class="wm_dialog_edit" colspan="3">
				<input type="checkbox" class="wm_input" id="intReqSmtpAuthentication" name="intReqSmtpAuthentication" value="1" <?php if($Settings['intReqSmtpAuth'] == 1) echo 'checked="checked"';?>>&nbsp;
				<label for="intReqSmtpAuthentication">Requires SMTP Authentication</label>
			</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_field">Attachment Size Limit: </td>
			<td class="wm_dialog_edit" colspan="3">
				<input type="text" class="wm_input" name="intAttachmentSizeLimit" size="8" maxlength="100" value="<?php echo $Settings['intAttachmentSizeLimit'];?>" /> bytes
			</td>
		  </tr>
		  <tr>
			<td class="wm_mailadm_dialog_settings_header" colspan="4">Internationalization Support</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_field">Default User Charset </td>
			<td class="wm_dialog_edit" colspan="3">
			<select class="wm_input" name="txtDefaultUserCharset">
			<option<?php If ($Settings['txtDefaultCharset'] == "") echo ' selected="selected"';?> value=""> Not specified</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "big5") echo ' selected="selected"';?> value="big5"> Chinese Traditional</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "euc-kr") echo ' selected="selected"';?> value="euc-kr"> Korean (EUC)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "iso-2022-kr") echo ' selected="selected"';?> value="iso-2022-kr"> Korean (ISO)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "iso-2022-jp") echo ' selected="selected"';?> value="iso-2022-jp"> Japanese</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "shift-jis") echo ' selected="selected"';?> value="shift-jis"> Japanese (Shift-JIS)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "iso-8859-1") echo ' selected="selected"';?> value="iso-8859-1"> Western Alphabet (ISO)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "iso-8859-2") echo ' selected="selected"';?> value="iso-8859-2"> Central European Alphabet (ISO)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "iso-8859-3") echo ' selected="selected"';?> value="iso-8859-3"> Latin 3 Alphabet (ISO)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "iso-8859-4") echo ' selected="selected"';?> value="iso-8859-4"> Baltic Alphabet (ISO)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "iso-8859-5") echo ' selected="selected"';?> value="iso-8859-5"> Cyrillic Alphabet (ISO)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "iso-8859-6") echo ' selected="selected"';?> value="iso-8859-6"> Arabic Alphabet (ISO)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "iso-8859-7") echo ' selected="selected"';?> value="iso-8859-7"> Greek Alphabet (ISO)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "iso-8859-8") echo ' selected="selected"';?> value="iso-8859-8"> Hebrew Alphabet (ISO)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "koi8-r") echo ' selected="selected"';?> value="koi8-r"> Cyrillic Alphabet (KOI8-R)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "utf-8") echo ' selected="selected"';?> value="utf-8"> Universal Alphabet (UTF-8)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "windows-1250") echo ' selected="selected"';?> value="windows-1250"> Central European Alphabet (Windows)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "windows-1251") echo ' selected="selected"';?> value="windows-1251"> Cyrillic Alphabet (Windows)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "windows-1252") echo ' selected="selected"';?> value="windows-1252"> Western Alphabet (Windows)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "windows-1253") echo ' selected="selected"';?> value="windows-1253"> Greek Alphabet (Windows)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "windows-1254") echo ' selected="selected"';?> value="windows-1254"> Turkish Alphabet</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "windows-1255") echo ' selected="selected"';?> value="windows-1255"> Hebrew Alphabet (Windows)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "windows-1256") echo ' selected="selected"';?> value="windows-1256"> Arabic Alphabet (Windows)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "windows-1257") echo ' selected="selected"';?> value="windows-1257"> Baltic Alphabet (Windows)</option>
			<option<?php If ($Settings['txtDefaultCharset'] == "windows-1258") echo ' selected="selected"';?> value="windows-1258"> Vietnamese Alphabet (Windows)</option>
			</select>
			</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_field">Default User Time Offset</td>
			<td class="wm_dialog_edit" colspan="3">
			<select class="wm_input" name="txtDefaultTimeOffset" style="width:280px">
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "") echo ' selected="selected"';?> value=""> Default</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoEniwetok") echo ' selected="selected"';?> value="cdoEniwetok"> (GMT -12:00) Eniwetok, Kwajalein, Dateline Time</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoMidwayIsland") echo ' selected="selected"';?> value="cdoMidwayIsland"> (GMT -11:00) Midway Island, Samoa</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoHawaii") echo ' selected="selected"';?> value="cdoHawaii"> (GMT -10:00) Hawaii</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoAlaska") echo ' selected="selected"';?> value="cdoAlaska"> (GMT -09:00) Alaska</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoPacific") echo ' selected="selected"';?> value="cdoPacific"> (GMT -08:00) Pacific Time (US & Canada); Tijuana</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoArizona") echo ' selected="selected"';?> value="cdoArizona"> (GMT -07:00) Arizona</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoMountain") echo ' selected="selected"';?> value="cdoMountain"> (GMT -07:00) Mountain Time (US & Canada)</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoCentralAmerica") echo ' selected="selected"';?> value="cdoCentralAmerica"> (GMT -06:00) Central America</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoCentral") echo ' selected="selected"';?> value="cdoCentral"> (GMT -06:00) Central Time (US & Canada)</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoMexicoCity") echo ' selected="selected"';?> value="cdoMexicoCity"> (GMT -06:00) Mexico City, Tegucigalpa</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoSaskatchewan") echo ' selected="selected"';?> value="cdoSaskatchewan"> (GMT -06:00) Saskatchewan</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoIndiana") echo ' selected="selected"';?> value="cdoIndiana"> (GMT -05:00) Indiana (East)</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoEastern") echo ' selected="selected"';?> value="cdoEastern"> (GMT -05:00) Eastern Time (US & Canada)</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoBogota") echo ' selected="selected"';?> value="cdoBogota"> (GMT -05:00) Bogota, Lima, Quito</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoSantiago") echo ' selected="selected"';?> value="cdoSantiago"> (GMT -04:00) Santiago</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoCaracas") echo ' selected="selected"';?> value="cdoCaracas"> (GMT -04:00) Caracas, La Paz</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoAtlanticCanada") echo ' selected="selected"';?> value="cdoAtlanticCanada"> (GMT -04:00) Atlantic Time (Canada)</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoNewfoundland") echo ' selected="selected"';?> value="cdoNewfoundland"> (GMT -03:30) Newfoundland</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoGreenland") echo ' selected="selected"';?> value="cdoGreenland"> (GMT -03:00) Greenland</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoBuenosAires") echo ' selected="selected"';?> value="cdoBuenosAires"> (GMT -03:00) Buenos Aires, Georgetown</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoBrasilia") echo ' selected="selected"';?> value="cdoBrasilia"> (GMT -03:00) Brasilia</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoMidAtlantic") echo ' selected="selected"';?> value="cdoMidAtlantic"> (GMT -02:00) Mid-Atlantic</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoCapeVerde") echo ' selected="selected"';?> value="cdoCapeVerde"> (GMT -01:00) Cape Verde Is.</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoAzores") echo ' selected="selected"';?> value="cdoAzores"> (GMT -01:00) Azores</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoMonrovia") echo ' selected="selected"';?> value="cdoMonrovia"> (GMT) Casablanca, Monrovia</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoGMT") echo ' selected="selected"';?> value="cdoGMT"> (GMT) Dublin, Edinburgh, Lisbon, London</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoBerlin") echo ' selected="selected"';?> value="cdoBerlin"> (GMT +01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoPrague") echo ' selected="selected"';?> value="cdoPrague"> (GMT +01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoParis") echo ' selected="selected"';?> value="cdoParis"> (GMT +01:00) Brussels, Copenhagen, Madrid, Paris</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoSarajevo") echo ' selected="selected"';?> value="cdoSarajevo"> (GMT +01:00) Sarajevo, Skopje, Sofija, Vilnius, Warsaw, Zagreb</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoWestCentralAfrica") echo ' selected="selected"';?> value="cdoWestCentralAfrica"> (GMT +01:00) West Central Africa</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoAthens") echo ' selected="selected"';?> value="cdoAthens"> (GMT +02:00) Athens, Istanbul, Minsk</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoEasternEurope") echo ' selected="selected"';?> value="cdoEasternEurope"> (GMT +02:00) Bucharest</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoCairo") echo ' selected="selected"';?> value="cdoCairo"> (GMT +02:00) Cairo</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoHarare") echo ' selected="selected"';?> value="cdoHarare"> (GMT +02:00) Harare, Pretoria</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoHelsinki") echo ' selected="selected"';?> value="cdoHelsinki"> (GMT +02:00) Helsinki, Riga, Tallinn</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoIsrael") echo ' selected="selected"';?> value="cdoIsrael"> (GMT +02:00) Israel, Jerusalem Standard Time</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoBaghdad") echo ' selected="selected"';?> value="cdoBaghdad"> (GMT +03:00) Baghdad</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoArab") echo ' selected="selected"';?> value="cdoArab"> (GMT +03:00) Arab, Kuwait, Riyadh</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoMoscow") echo ' selected="selected"';?> value="cdoMoscow"> (GMT +03:00) Moscow, St. Petersburg, Volgograd</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoEastAfrica") echo ' selected="selected"';?> value="cdoEastAfrica"> (GMT +03:00) East Africa, Nairobi</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoTehran") echo ' selected="selected"';?> value="cdoTehran"> (GMT +03:30) Tehran</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoAbuDhabi") echo ' selected="selected"';?> value="cdoAbuDhabi"> (GMT +04:00) Abu Dhabi, Muscat</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoCaucasus") echo ' selected="selected"';?> value="cdoCaucasus"> (GMT +04:00) Baku, Tbilisi, Yerevan</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoKabul") echo ' selected="selected"';?> value="cdoKabul"> (GMT +04:30) Kabul</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoEkaterinburg") echo ' selected="selected"';?> value="cdoEkaterinburg"> (GMT +05:00) Ekaterinburg</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoIslamabad") echo ' selected="selected"';?> value="cdoIslamabad"> (GMT +05:00) Islamabad, Karachi, Sverdlovsk, Tashkent</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoBombay") echo ' selected="selected"';?> value="cdoBombay"> (GMT +05:30) Calcutta, Chennai, Mumbai, New Delhi, India Standard Time</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoNepal") echo ' selected="selected"';?> value="cdoNepal"> (GMT +05:45) Kathmandu, Nepal</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoAlmaty") echo ' selected="selected"';?> value="cdoAlmaty"> (GMT +06:00) Almaty, Novosibirsk, North Central Asia</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoDhaka") echo ' selected="selected"';?> value="cdoDhaka"> (GMT +06:00) Astana, Dhaka</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoSriLanka") echo ' selected="selected"';?> value="cdoSriLanka"> (GMT +06:00) Sri Jayawardenepura, Sri Lanka</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoRangoon") echo ' selected="selected"';?> value="cdoRangoon"> (GMT +06:30) Rangoon</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoBangkok") echo ' selected="selected"';?> value="cdoBangkok"> (GMT +07:00) Bangkok, Hanoi, Jakarta</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoKrasnoyarsk") echo ' selected="selected"';?> value="cdoKrasnoyarsk"> (GMT +07:00) Krasnoyarsk</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoBeijing") echo ' selected="selected"';?> value="cdoBeijing"> (GMT +08:00) Beijing, Chongqing, Hong Kong SAR, Urumqi</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoIrkutsk") echo ' selected="selected"';?> value="cdoIrkutsk"> (GMT +08:00) Irkutsk, Ulaan Bataar</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoSingapore") echo ' selected="selected"';?> value="cdoSingapore"> (GMT +08:00) Kuala Lumpur, Singapore</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoPerth") echo ' selected="selected"';?> value="cdoPerth"> (GMT +08:00) Perth, Western Australia</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoTaipei") echo ' selected="selected"';?> value="cdoTaipei"> (GMT +08:00) Taipei</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoTokyo") echo ' selected="selected"';?> value="cdoTokyo"> (GMT +09:00) Osaka, Sapporo, Tokyo</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoSeoul") echo ' selected="selected"';?> value="cdoSeoul"> (GMT +09:00) Seoul, Korea Standard time</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoYakutsk") echo ' selected="selected"';?> value="cdoYakutsk"> (GMT +09:00) Yakutsk</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoAdelaide") echo ' selected="selected"';?> value="cdoAdelaide"> (GMT +09:30) Adelaide, Central Australia</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoDarwin") echo ' selected="selected"';?> value="cdoDarwin"> (GMT +09:30) Darwin</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoBrisbane") echo ' selected="selected"';?> value="cdoBrisbane"> (GMT +10:00) Brisbane, East Australia</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoSydney") echo ' selected="selected"';?> value="cdoSydney"> (GMT +10:00) Canberra, Melbourne, Sydney, Hobart</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoGuam") echo ' selected="selected"';?> value="cdoGuam"> (GMT +10:00) Guam, Port Moresby</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoHobart") echo ' selected="selected"';?> value="cdoHobart"> (GMT +10:00) Hobart, Tasmania</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoVladivostock") echo ' selected="selected"';?> value="cdoVladivostock"> (GMT +10:00) Vladivostok</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoMagadan") echo ' selected="selected"';?> value="cdoMagadan"> (GMT +11:00) Magadan, Solomon Is., New Caledonia</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoWellington") echo ' selected="selected"';?> value="cdoWellington"> (GMT +12:00) Auckland, Wellington</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoFiji") echo ' selected="selected"';?> value="cdoFiji"> (GMT +12:00) Fiji Islands, Kamchatka, Marshall Is.</option>
			<option<?php If ($Settings['txtDefaultTimeOffset'] == "cdoTonga") echo ' selected="selected"';?> value="cdoTonga"> (GMT +13:00) Nuku\'alofa, Tonga</option>
			</select>
			</td>
		  </tr>
		  <tr>
			<td class="wm_mailadm_dialog_settings_header" colspan="4">Attachment Settings</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_field">Path to WebMail PHP Attachment Folder:</td>
			<td class="wm_dialog_edit" colspan="3">
				<input type="text" class="wm_input" name="txtPathForUpload" value="<?php echo $Settings['txtDefaultTempDir'];?>" size="44" maxlength="255" />
			</td>
		  </tr>
		  <tr>
			<td class="wm_mailadm_dialog_settings_header" colspan="4">Password</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_field">New Password: </td>
			<td class="wm_dialog_edit" colspan="3">
				<input type="password" class="wm_input" name="txtPassword1" value="*********" maxlength="100" />
			</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_field">Confirm Password: </td>
			<td class="wm_dialog_edit" colspan="3">
				<input type="password" class="wm_input" name="txtPassword2" value="*********" maxlength="100" />
			</td>
		  </tr>
		  <tr>
			<td class="wm_dialog_button_edit" colspan="4">
				<input type="button" name="save" value="Save" class="wm_button" onClick="CheckForm01()" />
				<input type="button" name="cancel" value="Cancel" class="wm_button" onClick="document.location.replace('mailadm.php')" />
			</td>
		  </tr>
		</table>
	</form>
<?php
		break;
		case 'wm_interface':
?>
	<form name="wm_settings" action="mailadm_process.php" method="post">
	<input type="hidden" name="mode" value="wm_update_form02">
	  <table class="wm_mailadm_dialog" width="430">
		<tr>
		  <td class="wm_mailadm_dialog_settings_header" colspan="4">Interface Settings</td>
		</tr>
		<tr>
		  <td class="wm_dialog_field" width="130">Mails Per Page: </td>
		  <td class="wm_dialog_edit" colspan="3">
			<input type="text" class="wm_input" name="intMailsPerPage" size="4" value="<?php echo $Settings['intMailsPerPage']?>" maxlength="4" />
		  </td>
		</tr>
		<tr>
		  <td class="wm_dialog_field">Default Skin: </td>
		  <td class="wm_dialog_edit" colspan="3">
			<select class="wm_input" name="txtDefaultSkin">
<?php
			$SubFolders = ScanDirC('skins');
			for ($i=0; $i<count($SubFolders); $i++)
				if (is_dir('skins/'.$SubFolders[$i]) && !strstr($SubFolders[$i],'.'))
				{
					echo '<option value="'.$SubFolders[$i].'"';
					if ($SubFolders[$i] == $Settings['txtDefaultSkin'])
						echo ' selected="selected"';
					echo '>'.$SubFolders[$i].'</option>';
				}
?>
			</select>
		  </td>
		</tr>
		<tr>
		  <td class="wm_dialog_field"><img src="images/1x1.gif" /></td>
		  <td class="wm_dialog_edit" colspan="3">
			<input type="checkbox" class="wm_input" name="intShowTextLabels" id="intShowTextLabels" value="1" <?php If ($Settings['intShowTextLabels'] == 1) echo ' checked="checked"';?> />
			<label for="intShowTextLabels">Show Text Labels</label>
		  </td>
		</tr>
		<tr>
		  <td class="wm_dialog_field"><img src="images/1x1.gif" /></td>
		  <td class="wm_dialog_edit" colspan="3">
			<input type="checkbox" class="wm_input" name="intAllowAjax" id="intAllowAjax" value="1" <?php If ($Settings['intAllowAjax'] == 1) echo ' checked="checked"';?> />
			<label for="intAllowAjax">Allow AJAX Version</label>
		  </td>
		</tr>
		<tr>
		  <td class="wm_dialog_button_edit" colspan="4">
			<input type="button" class="wm_button" name="save" value="Save" onClick="CheckForm02()" />
			<input type="button" class="wm_button" name="cancel" value="Cancel" onClick="document.location.replace('mailadm.php')" />
		  </td>
		</tr>
	  </table>
	</form>
<?php
		break;
		case 'wm_domain':
?>
	<form name="wm_settings" action="mailadm_process.php" method="post" onSubmit="CheckForm04()">
	<input type="hidden" name="mode" value="wm_update_form04">
	<input type="hidden" name="intHideLogin" value="'.$Setting['globalIntHideLogin'].'">
	<input type="hidden" name="id_user_delete" value="0">
	  <table class="wm_mailadm_dialog" width="400">
		<tr>
		  <td class="wm_mailadm_dialog_settings_header" colspan="2">Login Settings</td>
		</tr>
		<tr>
		  <td class="wm_dialog_hide_login_field" width="60">
			<input type="radio" name="hideLoginRadionButton" id="hideLoginRadionButton1"<?php If ($Settings['intHideLoginMode'] == 0) echo ' checked="checked"';?> onclick="SetDomain(0)">
		  </td>
		  <td class="wm_dialog_edit">
			<label for="hideLoginRadionButton1">Standard Login Panel</label>
		  </td>
		</tr>
		<tr>
		  <td class="wm_dialog_hide_login_field">
			<input type="radio" name="hideLoginRadionButton" id="hideLoginRadionButton2"<?php If ($Settings['intHideLoginMode'] >= 10 && $Settings['intHideLoginMode'] < 20) echo ' checked="checked"';?> onclick="SetDomain(1)">
		  </td>
		  <td class="wm_dialog_edit">
			<label for="hideLoginRadionButton2">Hide Login Field</label><br/><br/>
			<select class="wm_input" name="hideLoginSelect" <?php If (!($Settings['intHideLoginMode'] >= 10) || !($Settings['intHideLoginMode'] < 20)) echo ' disabled="disabled"';?>>
				<option value="1"<?php If ($Settings['intHideLoginMode'] == 10) echo ' selected="selected"';?>> Use Email as Login</option>
				<option value="2"<?php If ($Settings['intHideLoginMode'] == 11) echo ' selected="selected"';?>> Use Account-name as Login</option>
			</select>
		  </td>
		</tr>
		<tr>
		  <td class="wm_dialog_hide_login_field">
			<input type="radio" name="hideLoginRadionButton" id="hideLoginRadionButton3"<?php If ($Settings['intHideLoginMode'] >= 20) echo ' checked="checked"';?> onclick="SetDomain(2)" />
		  </td>
		  <td class="wm_dialog_edit">
			<label for="hideLoginRadionButton3">Hide Email Field</label><br /><br />
			<input type="text" class="wm_input" name="txtUseDomain" value="<?php echo $Settings['txtDefaultDomainOptional'];?>" size="20"<?php If ($Settings['intHideLoginMode'] < 20) echo ' disabled="disabled"';?> /> domain to use<br /><br />
			<input type="checkbox" name="intDisplayDomainAfterLoginField" id="intDisplayDomainAfterLoginField" <?php If ($Settings['intHideLoginMode'] == 21 || $Settings['intHideLoginMode'] == 23) echo ' checked="checked"'; If ($Settings['intHideLoginMode'] < 20) echo ' disabled="disabled"';?> />
			<label for="intDisplayDomainAfterLoginField"> Display Domain After Login Field</label><br /><br />
			<input type="checkbox" name="intLoginAsConcatination" id="intLoginAsConcatination"<?php If ($Settings['intHideLoginMode'] >= 22) echo ' checked="checked"'; If ($Settings['intHideLoginMode'] < 20) echo ' disabled="disabled"';?> />
			<label for="intLoginAsConcatination"> Login as Concatenation of "Login" field + "@" + domain</label><br /><br />
		 </td>
		</tr>
		<tr>
		  <td class="wm_dialog_hide_login_field">
			<input type="checkbox" value="1" id="intAllowAdvancedLogin" name="intAllowAdvancedLogin" <?php if($Settings['intAllowAdvancedLogin'] == 1) echo ' checked="checked"';?> />
		  </td>
		  <td class="wm_dialog_edit">
			<label for="intAllowAdvancedLogin">Allow Advanced Login</label>
		  </td>
		</tr>
		<tr>
		  <td class="wm_dialog_hide_login_field">
			<input type="checkbox" value="1" name="intAutomaticHideLogin" id="intAutomaticHideLogin"<?php If ($Settings['intAutomaticCorrectLoginSettings'] == 1) echo ' checked="checked"';?> />
		  </td>
		  <td class="wm_dialog_edit">
			<label for="intAutomaticHideLogin">Automatically detect and correct if user inputs e-mail instead of account-name</label>
		  </td>
		</tr>
		<tr>
		  <td class="wm_dialog_button_edit" colspan="2">
			<input type="button" class="wm_button" name="submit2" value="Save" onClick="CheckForm04()" />
			<input type="button" class="wm_button" name="cancel" value="Cancel" onClick="document.location.replace('mailadm.php')" />
		  </td>
		</tr>
	  </table>
	</form>
<?php
		break;
		case 'clear_log':
		if ($Settings['intDisableErrorHandling']) 
			$f = fopen($Settings['txtDefaultLogPath'], "w");
		else 
			$f = @fopen($Settings['txtDefaultLogPath'], "w");
		if ($f)	
			fclose($f);
		else {
			$ErrorDesc = 'The web-server has no permission to write into the log file:<br/><br/>'.$Settings['txtDefaultLogPath'].'<br/><br/>To learn how to grant the appropriate permission, please refer to WebMail documentation:<br/><br/><a href=\'help/installation_instructions_win.html\'>Installation Instructions for Windows</a><br/><a href=\'help/installation_instructions_unix.html\'>Installation Instructions for Unix</a>';
			$OkAction = 'history.back();';
			$config['txtDefaultSkin'] = $Settings['txtDefaultSkin'];
			include('inc_html_error.php');
			exit;
		}
		case 'wm_debug':
?>
	<form name="wm_settings" action="mailadm_process.php" method="post">
	<input type="hidden" name="mode" value="wm_update_form05">
	  <table class="wm_mailadm_dialog" nowrap width="400">
		<tr>
		  <td class="wm_mailadm_dialog_settings_header" colspan="2">Debug Settings</td>
		</tr>
		<tr>
		  <td class="wm_dialog_field" width="60">
			<input type="checkbox" name="intEnableLogging" id="intEnableLogging" <?php If ($Settings['intEnableLogging'] == 1) echo ' checked="checked"';?> value="1" />
		  </td>
		  <td class="wm_dialog_edit">
			<label for="intEnableLogging">Enable Logging</label>
		  </td>
		</tr>
		<tr>
		  <td class="wm_dialog_field">&nbsp;</td>
		  <td class="wm_dialog_edit">
			<div>
				Path For Log&nbsp;
				<input type="text" class="wm_input" name="txtPathForLog" value="<?php echo $Settings['txtDefaultLogPath'];?>" size="34" />
			</div>
			<div class="wm_log_file">
				<a href="" class="wm_reg" title="View log-file" onclick = "window.open('view_log.php','','toolbar=no,scrollbars=yes,resizable=yes,width=640,height=380'); return false;">View Log</a>
				<a href="mailadm.php?mode=clear_log" class="wm_reg" title="Clearlog-file">Clear Log</a>
			</div>
		  </td>
		</tr>
		<tr>
		  <td class="wm_dialog_field">
			<input type="checkbox" name="intDisableErrorHandling" id="intDisableErrorHandling" value="1"<?php If ($Settings['intDisableErrorHandling'] == 1) echo ' checked="checked"';?> />
		  </td>
		  <td class="wm_dialog_edit">
			<label for="intDisableErrorHandling">Disable Error Handling</label>
		  </td>
		</tr>
		<tr>
		  <td class="wm_dialog_button_edit" colspan="2">
			<input type="button" class="wm_button" name="submit2" value="Save" onClick="CheckForm05()" />
			<input type="button" class="wm_button" name="cancel" value="Cancel" onClick="document.location.replace('mailadm.php')" />
		  </td>
		</tr>
	  </table>
	</form>
<?php
		break;
		default:
?>
	<table class="wm_mailadm_console_dialog" width="200">
	  <tr>
		<td class="wm_mailadm_dialog_settings_header">Administration Console</td>
	  </tr>
	  <tr>
		<td class="wm_dialog_edit">
			<a href="./mailadm.php?mode=wm_settings" class="wm_list_item_link">WebMail Settings</a>
		</td>
	  </tr>
	  <tr>
		<td class="wm_dialog_edit">
			<a href="./mailadm.php?mode=wm_interface" class="wm_list_item_link">Interface Settings</a>
		</td>
	  </tr>
	  <tr>
		<td class="wm_dialog_edit">
			<a href="./mailadm.php?mode=wm_domain" class="wm_list_item_link">Login Settings</a>
		</td>
	  </tr>
	  <tr>
		<td class="wm_dialog_edit">
			<a href="./mailadm.php?mode=wm_debug" class="wm_list_item_link">Debug Settings</a>
		</td>
	  </tr>
	  <tr>
		<td class="wm_dialog_edit">
			<a href="help/default.htm" class="wm_list_item_link" target="_blank">Help</a>
		</td>
	  </tr>
	  <tr>
		<td class="wm_dialog_edit">
			<a href="./mailadm.php?mode=exit" class="wm_list_item_link">Exit</a>
		</td>
	  </tr>
	</table>
<?php
	}
}
include './inc_footer.php';
?>
	</div>
</body>
</html>
