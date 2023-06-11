<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<title><?php echo $config['txtWindowTitle'];?></title>
	<link rel="stylesheet" href="./skins/<?php echo $config['txtDefaultSkin'];?>/styles.css" type="text/css" />
	<script language="JavaScript" type="text/javascript" src="./inc_html_functions.js"></script>
</head>
<body>
  <div align="center">
<?php
	include './inc_header.php';
	include './inc_html_accountslist.php';
	$Titles = $Alts = Array(
		iconv($InCharset, $Charset, $strList)
	);
	$Clicks = Array(
		'document.location = \'./actions.php?action=list&page='.$Page.'\';'
	);
	$Icons = Array(
		'back_to_list.gif'
	);
	include './inc_html_toolbar.php';
?>
	<form name="message_form" action="actions.php" method="post">
		<input type="hidden" name="action" value="new" />
		<input type="hidden" name="page" value="<?php echo $Page;?>" />
		<input type="hidden" name="mode" value="redirect" />
		<input type="hidden" name="id" value="<?php echo $MessageId;?>" />
	<table class="wm_dialog" style="margin: 50px">
	  <tr>
		<td class="wm_dialog_redirect_header" colspan="2"><?php echo iconv($InCharset, $Charset, $strRedirect);?></td>
	  </tr>
	  <tr>
		<td class="wm_dialog_redirect_field" width="90"><?php echo iconv($InCharset, $Charset, $strEmail);?>: </td>
		<td class="wm_dialog_edit">
			<input type="text" class="wm_input" name="toemail" size="90" />
		</td>
	  </tr>
	  <tr>
		<td class="wm_dialog_redirect_field"></td>
		<td class="wm_dialog_button_edit">
			<input type="submit" class="wm_button" name="redirect" value="<?php echo iconv($InCharset, $Charset, $strRedirectButton);?>" />
		</td>
	  </tr>
	</table>
	</form>
<?php
	include './inc_html_toolbar.php';
	include './inc_footer.php';
?>
  </div>
</body>
</html>
