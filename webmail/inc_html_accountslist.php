	<table class="wm_accountslist">
	  <tr>
		<td>
			<span class="wm_accountslist_item_selected">
				<a href="actions.php?action=list&page=<?php echo $Page;?>" class="wm_accountslist_item_link"><?php echo $Email;?></a>
			</span>
			<span class="wm_accountslist_item">
				<a href="login.php" class="wm_accountslist_item_link"><?php echo iconv($InCharset, $Charset, $strLogout);?></a>
			</span>
		</td>
	  </tr>
	</table>
