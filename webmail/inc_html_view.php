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
		iconv($InCharset, $Charset, $strList),
		iconv($InCharset, $Charset, $strNewMessage),
		iconv($InCharset, $Charset, $strReply),
		iconv($InCharset, $Charset, $strReplyAll),
		iconv($InCharset, $Charset, $strForward),
		iconv($InCharset, $Charset, $strRedirect),
		iconv($InCharset, $Charset, $strPrint),
		iconv($InCharset, $Charset, $strSaveMessage),
		iconv($InCharset, $Charset, $strDelete)
	);
	$Alts[] = iconv($InCharset, $Charset, $strMessageUp);
	$Alts[] = iconv($InCharset, $Charset, $strMessageDown);
	$Titles[] = '';
	$Titles[] = '';
	$Clicks = Array(
		'document.location = \'./actions.php?action=list&page='.$Page.'\';',
		'document.location = \'./actions.php?action=new&page='.$Page.'\';',
		'document.location = \'./actions.php?action=reply&page='.$Page.'&id='.$MessageId.'\';',
		'document.location = \'./actions.php?action=replyall&page='.$Page.'&id='.$MessageId.'\';',
		'document.location = \'./actions.php?action=forward&page='.$Page.'&id='.$MessageId.'\';',
		'document.location = \'./actions.php?action=redirect&page='.$Page.'&id='.$MessageId.'\';',
		'window.open(\'./print.php?id='.$MessageId.'\',\'\',\'toolbar=yes,scrollbars=yes,resizable=yes,width=640,height=380\');',
		'document.location = \'./save_msg.php?id='.$MessageId.'\';',
		'Delete('.$Page.', '.$MessageId.', \''.iconv($InCharset, $Charset, $JsConfirmation).'\');',
		($MessageId == $pop3->MessageCount) ? '' : 'document.location = \'./actions.php?action=view&page='.$Page.'&id='.($MessageId+1).'\';',
		($MessageId == 1) ? '' : 'document.location = \'./actions.php?action=view&page='.$Page.'&id='.($MessageId-1).'\';'
	);
	$Icons = Array(
		'back_to_list.gif',
		'new_mail.gif',
		'reply.gif',
		'replyall.gif',
		'forward.gif',
		'redirect.gif',
		'print.gif',
		'save.gif',
		'delete.gif',
		($MessageId == $pop3->MessageCount) ? 'message_up_inactive.gif' : 'message_up.gif',
		($MessageId == 1) ? 'message_down_inactive.gif' : 'message_down.gif'
	);
	include './inc_html_toolbar.php';
?>
	<table class="wm_message">
	  <tr>
		<td class="wm_view_message_data" width="15%"><?php echo iconv($InCharset, $Charset, $strFrom);?>: </td>
		<td class="wm_message_value" colspan="2" width="85%"><?php echo EncodeHtml($Message->FromAddr);?></td>
	  </tr>
	  <tr>
		<td class="wm_view_message_data"><?php echo iconv($InCharset, $Charset, $strTo);?>: </td>
		<td class="wm_message_value" colspan="2"><?php echo EncodeHtml($Message->ToAddr);?></td>
	  </tr>
<?php
	if (!empty($Message->CCAddr)) {
?>
	  <tr>
		<td class="wm_view_message_data"><?php echo iconv($InCharset, $Charset, $strCC);?>: </td>
		<td class="wm_message_value" colspan="2"><?php echo EncodeHtml($Message->CCAddr);?></td>
	  </tr>
<?php
	}
?>
	  <tr>
		<td class="wm_view_message_data"><?php echo iconv($InCharset, $Charset, $strDate);?>: </td>
		<td class="wm_message_value" colspan="2"><?php echo EncodeHtml($Message->Date);?></td>
	  </tr>
	  <tr>
		<td class="wm_view_message_data"><?php echo iconv($InCharset, $Charset, $strSubject);?>: </td>
		<td class="wm_message_value" width="70%"><?php echo EncodeHtml($Message->Subject);?></td>
		<td class="wm_message_importance">
<?php
	if ($Message->Importance == 1)
		echo iconv($InCharset, $Charset, $strHighImportance);
	elseif ($Message->Importance == 5)
		echo iconv($InCharset, $Charset, $strLowImportance);
	else
		echo iconv($InCharset, $Charset, $strNormalImportance);
?>
		</td>	
	  </tr>
	  <tr>
		<td class="wm_lowtoolbar" colspan="3">
		  <div align="center">
			<div class="wm_message_body">
<?php
	if ($Mode >= 10) {
?>
 				<div class="wm_message_rfc822">
					<?php echo str_replace("\n", '<br/>', str_replace("\r\n", '<br/>', EncodeHtml($Message->Headers)));?>
				</div>
<?php
	}
//$Mode = 0 - by default, hide headers
//$Mode = 1 - text plain, hide headers
//$Mode = 2 - html, hide headers
//$Mode = 10 - by default, show headers
//$Mode = 11 - text plain, show headers
//$Mode = 12 - html, show headers
	switch ($Mode) {
		case 0:
		case 10:
			if ($Message->HasHtmlBody) echo $Message->HtmlBody;
			else echo $Message->TextBody;
			break;
		case 1:
		case 11:
			echo $Message->TextBody;
			break;
		case 2:
		case 12:
			echo $Message->HtmlBody;
			break;
	}
		?>
			</div>
		  </div>
		</td>
	  </tr>
	  <tr class="wm_lowtoolbar">
		<td colspan="3">
<?php
	switch ($Mode) {
		case 0:
		case 10:
			break;
		case 1:
		case 11:
?>
			<span class="wm_lowtoolbar_item_selected"><?php echo iconv($InCharset, $Charset, $strPlainText);?></span>	
			<span class="wm_lowtoolbar_item"><a href="actions.php?action=view&page=<?php echo $Page;?>&id=<?php echo $MessageId;?>&mode=<?php echo $Mode+1;?>" class="wm_reg"><?php echo iconv($InCharset, $Charset, $strHTML);?></a></span>	
<?php
			break;
		case 2:
		case 12:
?>
			<span class="wm_lowtoolbar_item"><a href="actions.php?action=view&page=<?php echo $Page;?>&id=<?php echo $MessageId;?>&mode=<?php echo $Mode-1;?>" class="wm_reg"><?php echo iconv($InCharset, $Charset, $strPlainText);?></a></span>	
			<span class="wm_lowtoolbar_item_selected"><?php echo iconv($InCharset, $Charset, $strHTML);?></span>	
<?php
			break;
	}
	switch ($Mode) {
		case 0:
		case 1:
		case 2:
?>
			<span class="wm_lowtoolbar_headers"><a href="actions.php?action=view&page=<?php echo $Page;?>&id=<?php echo $MessageId;?>&mode=<?php echo $Mode+10;?>" class="wm_list_item_link"><?php echo iconv($InCharset, $Charset, $strAllHeader);?></a></span>
<?php
			break;
		case 10:
		case 11:
		case 12:
?>
			<span class="wm_lowtoolbar_headers"><a href="actions.php?action=view&page=<?php echo $Page;?>&id=<?php echo $MessageId;?>&mode=<?php echo $Mode-10;?>" class="wm_list_item_link"><?php echo iconv($InCharset, $Charset, $strStandardHeader);?></a></span>
<?php
			break;
	}
?>
		</td>
	  </tr>
<?php
	if (!empty($Message->AttachmentsInfo)) {
?>
	  <tr>
		<td colspan="3">
			<table class="wm_attach">
<?php
		$key = 0;
		foreach ($Message->AttachmentsInfo as $AttachInfo) {
			$key++;
			$Extension = explode('.', $AttachInfo['name']);
			$Extension = $Extension[count($Extension) - 1]
?>
				<tr><td class="wm_attach_data">
					<?php echo iconv($InCharset, $Charset, $strFile).'&nbsp;#'.$key.':';?>
					</td><td class="wm_attach_value_icon">
					<img src="images/icons/<?php echo GetIconNameByExtension($Extension);?>" width="32" height="32" />
					</td><td class="wm_attach_value_text">
<?php
			echo $AttachInfo['name'].' ('.ceil($AttachInfo['size']/1024).' K)';
			if (isImage($AttachInfo['name']) == true) {
				$href = './open_img.php?msg_id='.$MessageId.'&part_id='.$AttachInfo['id'].'&name='.
					urlencode($AttachInfo['name']).'&encoding='.$AttachInfo['encoding'].'&mime='.
					$AttachInfo['type'].'-'.$AttachInfo['subtype'];
?>
					<a href="<?php echo $href;?>" class="wm_reg" target="_blank"><?php echo iconv($InCharset, $Charset, $strDownloadView);?></a>&nbsp;
<?php
			}
			$href = './download.php?msg_id='.$MessageId.'&part_id='.$AttachInfo['id'].'&name='.
				urlencode($AttachInfo['name']).'&encoding='.$AttachInfo['encoding'].'&mime='.
				$AttachInfo['type'].'-'.$AttachInfo['subtype'];
?>
					<a href="<?php echo $href;?>" class="wm_reg"><?php echo iconv($InCharset, $Charset, $strDownload);?></a>
				</td></tr>
<?php
		}
?>
			</table>
		</td>
	  </tr>
<?php
	}
?>
	</table>
<?php
	include './inc_html_toolbar.php';
	include './inc_footer.php';
?>
  </div>
</body>
</html>
