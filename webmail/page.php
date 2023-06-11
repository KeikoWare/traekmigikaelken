<?php
error_reporting (0);
require './settings_path.php';
require './inc_functions.php';
require './class_dom_xml.php';
require './inc_settings.php';
$RestoreError = '';
$config = RestoreSettings($RestoreError);
if (!empty($RestoreError)) {
	header('Location: ./index.php');
}
if ($config['intDisableErrorHandling'] == 1)
	error_reporting (E_ALL ^ E_NOTICE);
include './language.php';

$autostart = ini_get('session.auto_start');
if (!$autostart) {
	session_name("PHPWEBMAILSESSID");
	session_start();
}
if (@is_dir($config['txtDefaultTempDir']) && @!is_dir($config['txtDefaultTempDir'].$_SESSION['wm_email'])) {
	if ($config['intDisableErrorHandling']) 
		mkdir($config['txtDefaultTempDir'].$_SESSION['wm_email']);
	else
		@mkdir($config['txtDefaultTempDir'].$_SESSION['wm_email']);
}
$Charset = 'utf-8';
header('Content-type: text/html; charset='.$Charset);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<link rel="stylesheet" href="./skins/<?php echo $config['txtDefaultSkin'];?>/styles.css">
	<title><?php echo $config['txtWindowTitle'];?></title>
	<script language="JavaScript" type="text/javascript" src="./language.js"></script>
	<script language="JavaScript" type="text/javascript" src="./functions_location.js"></script>
	<script language="JavaScript" type="text/javascript" src="./functions.js"></script>
	<script language="JavaScript" type="text/javascript">
		var global_email = '<?php echo $_SESSION['wm_email'];?>';
		var processing_script_url = './processing.php';
		var save_message_url = './save_msg.php';
		var print_message_url = './print.php';
		var mails_per_page = <?php echo $config['intMailsPerPage'];?>;
		var global_skin = '<?php echo $config['txtDefaultSkin'];?>';
		var show_text_labels = <?php echo $config['intShowTextLabels'];?>;
		var enable_mailbox_size_limit = 0;
		var mailbox_size_limit = 0;
		var user_encoding = 'utf-8';
		var default_page = './index.php';
		var location_url = './location.php';
	</script>
	<script language="JavaScript" type="text/javascript" src="./click_handlers.js"></script>
	<script language="JavaScript" type="text/javascript" src="./functions_handlers.js"></script>
</head>
<body id="main_body">
<?php include './inc_header.php';?>
<div align="center">
<table class="wm_accountslist">
	<tr>
	<td>
		<span class="wm_accountslist_item_selected">
		<a href="" onclick="BackToListClick(); return false;" class="wm_accountslist_item_link"><?php echo $_SESSION['wm_email'];?></a>
		</span>
		<span class="wm_accountslist_item">
		<a href="index.php" class="wm_accountslist_item_link"><?php echo iconv($inCharset, $Charset, $strLogout);?></a>
		</span>
	</td>
	</tr>
</table>
</div>

<iframe name="attach_iframe" id="attach_iframe" src="attach.html" style="width:1px; height:1px; border:0px; display:none;"></iframe>
<iframe name="session_saver" id="session_saver" src="session_saver.php" style="width:1px; height:1px; border:0px; display:none;"></iframe>
<form action="./attach.php" method="post" enctype="multipart/form-data" id="attach_form" target="attach_iframe" style="display:none;">
	<input type="hidden" name="temp" id="temp" value="<?php echo $config['txtDefaultTempDir'].$_SESSION['wm_email'];?>"/>
	<div id="attach_div"><?php echo iconv($inCharset, $Charset, $strAttachFile);?> : 
		<input id="fileAttach" type="file" runat="server" name="fileAttach"/>&nbsp;
		<input type="submit" value="Attach" class="wm_button"/>
	</div>
</form>
<iframe name="historyFrame" id="historyFrame" src="location.html" style="width:1px; height:1px; border:0px; display:none;"></iframe>
<form action="./location.php" method="post" enctype="multipart/form-data" id="historyForm" target="historyFrame" style="display:none;">
	<input type="hidden" name="historyLocation" id="historyLocation" value="">
	<input type="submit"/>
</form>

<script language="JavaScript" type="text/javascript" src="./processing.js"></script>

<?php include './inc_footer.php';?>
</body>
</html>
