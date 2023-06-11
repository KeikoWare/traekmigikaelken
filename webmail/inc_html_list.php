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
		iconv($InCharset, $Charset, $strNewMessage),
		iconv($InCharset, $Charset, $strRefresh),
		iconv($InCharset, $Charset, $strDelete)
	);
	$Clicks = Array(
		'document.location = \'./actions.php?action=new&page='.$Page.'\';',
		'document.location = \'./actions.php?action=list&page='.$Page.'\';',
		'DeleteSelected(\''.iconv($InCharset, $Charset, $JsSelectCheckbox).'\', \''.iconv($InCharset, $Charset, $JsConfirmation).'\');'
	);
	$Icons = Array(
		'new_mail.gif',
		'refresh.gif',
		'delete.gif'
	);
	include './inc_html_toolbar.php';
?>
	<table class="wm_list">
			<col width="24" />
			<col width="24" />
			<col width="20" />
			<col width="20" />
			<col />
			<col width="120" />
			<col width="50" />
			<col width="350" />
	  <tr>
		<td class="wm_list_center_header">N</td>
<?php
	$FirstLineNum = $Page * $MailsPerPage + 1;
	$LastLineNum = ($Page + 1) * $MailsPerPage;
	if ($LastLineNum > $pop3->MessageCount)
		$LastLineNum = $pop3->MessageCount;
?>
		<td class="wm_list_center_header"><input type="checkbox" id="check" onclick="SelectAllCheckbox(<?php echo $FirstLineNum;?>, <?php echo $LastLineNum;?>)"/></td>
		<td class="wm_list_center_header"><img src="images/attachment.gif" /></td>
		<td class="wm_list_center_header"><img src="skins/<?php echo $config['txtDefaultSkin'];?>/menu/priority_high.gif" /></td>
		<td class="wm_list_left_header"><?php echo iconv($InCharset, $Charset, $strFrom);?></td>
		<td class="wm_list_center_header"><?php echo iconv($InCharset, $Charset, $strDate);?></td>
		<td class="wm_list_center_header"><?php echo iconv($InCharset, $Charset, $strSize);?></td>
		<td class="wm_list_left_header"><?php echo iconv($InCharset, $Charset, $strSubject);?></td>
	  </tr>
<?php
	if ($pop3->MessageCount == 0)
	{
?>
	  <tr><td colspan="8">
		<div align="center">
		<table width="200px" height="40px" class="wm_info">
			<tr><td align="center" class="wm_info_id">
				<?php echo iconv($InCharset, $Charset, $strEmptyMailbox);?>
			</td></tr>
		</table>
		</div>
	  </td></tr>
	  <tr><td class="wm_lowtoolbar" colspan="8">
		<span class="wm_lowtoolbar_item">
			<?php echo '0&nbsp;'.iconv($InCharset, $Charset, $strMessagesInMailbox);?>
		</span>	
	  </td></tr>
	</table>
<?php
	} else {
		$MessageId = $Page * $MailsPerPage;
		foreach ($Messages as $Message)
		{
			$MessageId++;
?>
	  <tr class="wm_list_readitem" id="line_<?php echo $MessageId;?>">
		<td class="wm_list_center_cell"><?php echo $MessageId;?></td>
		<td class="wm_list_center_cell">
			<input type="checkbox" id="check_<?php echo $MessageId;?>" value="<?php echo $Message->MessageId;?>" onclick="SelectLine(<?php echo $MessageId;?>)"/>
		</td>
		<td class="wm_list_center_cell">
<?php
			if ($Message->HasAttachments == 1)
				echo '<img src="./images/attachment.gif" class="wm_attachment_gif" />';
			else
				echo '&nbsp;'
?>
		</td>
		<td class="wm_list_center_cell">
<?php
			if ($Message->Importance == 1)
				echo '<img src="./skins/'.$config['txtDefaultSkin'].'/menu/priority_high.gif" />';
			elseif ($Message->Importance == 5)
				echo '<img src="./skins/'.$config['txtDefaultSkin'].'/menu/priority_low.gif" />';
			else
				echo '&nbsp;'
?>
		</td>
		<td class="wm_list_left_cell">
			<a href="./actions.php?action=view&page=<?php echo $Page;?>&id=<?php echo $Message->MessageId;?>" class="wm_list_item_link"><?php echo EncodeHTML($Message->FromFriendlyName);?></a>
		</td>
		<td class="wm_list_center_cell"><?php echo $Message->Date;?></td>
		<td class="wm_list_center_cell"><?php echo round($Message->Size/1024);?>&nbsp;K</td>
		<td class="wm_list_left_cell"><div class="wm_list_subject">
			<a href="./actions.php?action=view&page=<?php echo $Page;?>&id=<?php echo $Message->MessageId;?>" class="wm_list_item_link"><?php echo EncodeHTML($Message->Subject);?></a></div>
		</td>
	  </tr>
<?php
		}
?>
	  <tr>
		<td class="wm_lowtoolbar" colspan="8">
			<span class="wm_lowtoolbar_item">
<?php
		echo $pop3->MessageCount;
		if ($pop3->MessageCount == 1)
			echo '&nbsp;'.iconv($InCharset, $Charset, $strMessageInMailbox);
		elseif ($pop3->MessageCount > 1)
			echo '&nbsp;'.iconv($InCharset, $Charset, $strMessagesInMailbox);
?>
			</span>	
			<span class="wm_lowtoolbar_headers">
<?php
		$Count = ceil($pop3->MessageCount / $MailsPerPage);
		if ($Count > 4)
		{
			$FirstPage = $Page - 2;
			if ($FirstPage < 0) $FirstPage = 0;
			$LastPage = $FirstPage + 4;
			if ($LastPage >= $Count)
			{
				$LastPage = $Count-1;
				$FirstPage = $LastPage - 4;
			}
		}
		else
		{
			$FirstPage = 0;
			$LastPage = $Count-1;
		}
		if ($FirstPage != $LastPage){
			if ($FirstPage > 0)
				echo '<a style="padding-left: 6px;" href="./actions.php?action=list&page='.($FirstPage-1).'" class="wm_reg">&lt;&lt;</a>';
			for ($i=$FirstPage; $i<=$LastPage; $i++)
			{
				$First = $i*$MailsPerPage + 1;
				$Last = ($i + 1)*$MailsPerPage;
				if ($Last > $pop3->MessageCount)
					$Last = $pop3->MessageCount;
				$Text = $First.'...'.$Last;
				if (($Page*$MailsPerPage + 1) == $First)
					echo '<font style="padding-left: 6px;" class="wm_lowtoolbar_page_selected">'.$Text.'</font>';
				else
					echo '<a style="padding-left: 6px;" href="./actions.php?action=list&page='.$i.'" class="wm_reg">'.$Text.'</a>';
			}
			if ($Count > $i)
				echo '<a style="padding-left: 6px;" href="./actions.php?action=list&page='.$i.'" class="wm_reg">&gt;&gt;</a>';
		}
?>
			</span>	
		</td>
	  </tr>
	</table>
	<form id="delete" action="./actions.php" method="post" style="display: none;">
		<input type="hidden" id="action" name="action" value="delete" />
		<input type="hidden" id="page" name="page" value="<?php echo $Page;?>" />
		<input type="hidden" id="ids" name="ids" />
	</form>
<?php
	}
	include './inc_html_toolbar.php';
	include './inc_footer.php';
?>
  </div>
</body>
</html>
