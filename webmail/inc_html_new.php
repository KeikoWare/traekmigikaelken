<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<title><?php echo $config['txtWindowTitle'];?></title>
	<link rel="stylesheet" href="skins/<?php echo $config['txtDefaultSkin'];?>/styles.css" type="text/css" />
	<script language="JavaScript" type="text/javascript" src="inc_html_functions.js"></script>
</head>
<body>
  <div align="center">
<?php
	include './inc_header.php';
	include './inc_html_accountslist.php';
	$Titles = $Alts = Array(
		iconv($InCharset, $Charset, $strList),
		iconv($InCharset, $Charset, $strBtnSend)
	);
	$Clicks = Array(
		'document.location = \'./actions.php?action=list&page='.$Page.'\';',
		'Send(\''.iconv($InCharset, $Charset, $JsEmptyToField).'\', \''.iconv($InCharset, $Charset, $JsEmptySubjectConfirmation).'\');'
	);
	$Icons = Array(
		'back_to_list.gif',
		'send.gif'
	);
	include './inc_html_toolbar.php';
?>
	<form id="message_form" method="post" enctype="multipart/form-data" action="actions.php?action=new&page=<?php echo $Page;?>" style="padding:0px; margin:0px; border:0px;">
		<input type="hidden" name="mode" id="mode" value="attach" />
		<input type="hidden" name="key" id="key" />
	<div class="wm_new_message">
	<table class="wm_message">
	  <col width="90px" />
	  <col />
	  <col />
	  <tr>
		<td class="wm_new_message_data"><?php echo iconv($InCharset, $Charset, $strFrom);?>: </td>
		<td class="wm_message_value" colspan="2">
		  <input type="text" class="wm_input" name="from" size="93" value="<?php echo EncodeHTMLquotes($From);?>" />
		</td>
	  </tr>
	  <tr>
		<td class="wm_new_message_data"><?php echo iconv($InCharset, $Charset, $strTo);?>: </td>
		<td class="wm_message_value" colspan="2">
		  <input type="text" class="wm_input" name="to" id="to" size="93" value="<?php echo EncodeHTMLquotes($To);?>" />
		</td>
	  </tr>
	  <tr>
		<td class="wm_new_message_data"><?php echo iconv($InCharset, $Charset, $strCC);?>: </td>
		<td class="wm_message_value" colspan="2">
		  <input type="text" class="wm_input" name="cc" size="93" value="<?php echo EncodeHTMLquotes($CC);?>" />
		</td>
	  </tr>
	  <tr>
		<td class="wm_new_message_data"><?php echo iconv($InCharset, $Charset, $strBCC);?>: </td>
		<td class="wm_message_value" colspan="2">
		  <input type="text" class="wm_input" name="bcc" size="93" value="<?php echo EncodeHTMLquotes($BCC);?>" />
		</td>
	  </tr>
	  <tr>
		<td class="wm_new_message_data"><?php echo iconv($InCharset, $Charset, $strSubject);?>: </td>
		<td class="wm_message_value" colspan="2">
		  <input type="text" class="wm_input" name="subject" id="subject" size="93" value="<?php echo EncodeHTMLquotes($Subject);?>" />
		</td>
	  </tr>
	  <tr>
		<td class="wm_new_message_data" valign="top"><?php echo iconv($InCharset, $Charset, $strMessage);?>: </td>
		<td class="wm_message_value" colspan="2">
		  <textarea class="wm_input" name="message" rows="16" cols="94"><?php echo $MessageBody;?></textarea>
		</td>
	  </tr>
<?php
	if (!empty($_SESSION['attachments']))
	{
		$Number = 0;
		foreach ($_SESSION['attachments'] as $key => $Attachment)
		{
			$Number++;
			$Extention = explode('.', $Attachment['name']);
			$Extention = $Extention[count($Extention) - 1];
			$Icon = GetIconNameByExtension($Extention);
?>
	  <tr>
		<td class="wm_new_attach_data"><?php echo iconv($InCharset, $Charset, $strFile);?> #<?php echo $Number;?>: </td>
		<td class="wm_attach_value_icon">
			<img src="images/icons/<?php echo $Icon;?>" width="32" height="32">
		</td>
		<td class="wm_attach_value_text">
			<?php echo $Attachment['name'];?> (<?php echo ceil($Attachment['size']/1024);?> K)&nbsp;&nbsp;&nbsp;
			<a href="" class="wm_reg" onClick="DeAttachFile(<?php echo $key;?>, '<?php echo iconv($InCharset, $Charset, $JsConfirmation);?>'); return false;">Delete</a>
		</td>
	  </tr>
<?php
		}
	}
?>
	  <tr>
		<td class="wm_new_message_data"><?php echo iconv($InCharset, $Charset, $strAttachFile);?>:</td>
		<td class="wm_message_value" colspan="2">
			<input id="fileAttach" type="file" class="wm_file" name="fileAttach"/>&nbsp;
			<input type="submit" class="wm_button" value="<?php echo iconv($InCharset, $Charset, $strBtnAttachFile);?>"/>
		</td>
	  </tr>
	</table>
	</div>
	</form>
<?php
	include './inc_html_toolbar.php';
	include './inc_footer.php';
?>
  </div>
</body>
</html>
