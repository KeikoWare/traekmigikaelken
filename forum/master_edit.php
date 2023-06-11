<?PHP

$ID = $HTTP_GET_VARS["ID"];
$forumID = $HTTP_GET_VARS["forumID"];
$page = $HTTP_GET_VARS["page"];
$event = $HTTP_GET_VARS["event"];

// Der oprettes forbindelse til databasen
$dbConn = mysql_connect("localhost","eurole_dk","669Xhlgw");
mysql_select_db("eurole_dk",$dbConn);

function deleterow($VarID,$VarForum){
		$rs2 = mysql_query("SELECT * FROM forum WHERE fldReply=".$VarID." AND fldForumID=". $VarForum );
		While($record2 = mysql_fetch_row($rs2)){
			deleterow($record2[0],$VarForum);		
		}
			$result = mysql_query("DELETE FROM forum WHERE fldID='".$VarID."'");
}

If($event == "delete"){
  deleterow($ID,$forumID);
	header ("Location: master_index.php?page=".$page."&event=DoneDelete&affected=".$result);
	exit;
}

If($HTTP_POST_VARS["Submit"] == "Opdater"){
	$forum = $HTTP_POST_VARS["frmForumID"];
	$page = $HTTP_POST_VARS["frmPage"];
	$frmID = $HTTP_POST_VARS["frmID"];
	$frmNavn = $HTTP_POST_VARS["frmNavn"];
	$frmEmail = $HTTP_POST_VARS["frmEmail"];
	$frmTitel = $HTTP_POST_VARS["frmTitel"];
	$frmTekst = $HTTP_POST_VARS["frmTekst"];
	$result = mysql_query("UPDATE forum SET fldNavn='$frmNavn', fldEmail='$frmEmail', fldTitel='$frmTitel', fldTekst='$frmTekst' WHERE fldID=$frmID");
	header ("Location: master_index.php?page=".$page."&forumID=".$forum."&event=DoneUpdate&affected=".$result);
	exit;
}
else
{
header("Expires Wed, 6 Sep 2000 11:11:11 GMT");
header("Last-Modified: ".gmdate("D, d m y H:i:s")." GMT");
header("Cache-control: no-cache, must-revalidate"); // HTTP ver 1.1
header("Pragma: no-chache"); // HTTP ver 1.2

$rs = mysql_query("SELECT * FROM forum WHERE fldID=".$ID);
$record = mysql_fetch_row($rs)
?>
<html>
<head>
<LINK REL=STYLESHEET TYPE="text/css" HREF="admin.css">
<title>Keikoware forum</title>
</head>
<body>
<!--Some simple form validation below-->
<SCRIPT LANGUAGE = "JavaScript">
<!-- THIS SCRIPT WAS WRITTEN BY CHRIS WHYTE, WWW.WHYTEHOUSE.COM
function checkForm (form) {

if (form.frmNavn.value == ""){
alert("Skriv venligst dit navn.");form.frmNavn.focus();return false;}

if (form.frmTitel.value == ""){
alert("Skriv venligst en overskrift til dit indlæg.");form.frmTitel.focus();return false;}

if (form.frmTekst.value == ""){
alert("Skriv venligst en kommentar, et spørgsmål, en besked etc.");form.Body.focus();return false;}

else{return true;}}
// --></script>
<table width="100%" cellspacing="20" cellpadding="0">
  <tr>
    <td width="100%">
    <table border="0" cellspacing="0" width="100%" cellpadding="0">
      <tr>
        <td class="head" width="100%" >
        <p align="center"><IMG src="../images/eurole.gif"></td>
      </tr>
    </table>
    </td>
  </tr>
</table>
<table width="100%" cellspacing="0" cellpadding="20">
  <tr>
    <td width="100%" valign="top">
    <table width="100%">
      <tr>
        <td class="forum" width="100%">
        <Table width="100%"><TR><TD Width="25%">
        	<form><input type=button class="but" value="Tilbage" onClick="history.go(-1)"></form>
        </TD><TD Align=center  Width="50%">
					<FORM><B>Rediger indlæg</B></FORM>
				</TD><TD align=right  Width="25%"><FORM><B class="datetime"><?echo Date("d-m-Y h:i:s")?></B></FORM></TD></TR></TABLE>
<center>
<form action="master_edit.php" method="post" onSubmit = "return checkForm (this)">
<input type=hidden name="frmForumID" value="<?echo $forumID?>">
<input type=hidden name="frmID" value="<?echo $ID?>">
<input type=hidden name="frmPage" value="<?echo $page?>">
<table border=3 cellspacing=2 cellpadding=2 bgcolor=efefef>
<TR><TD colspan="2"><U><B>Indlæggets data :</B></U><BR>
<TABLE width="100%">
<?PHP
echo "<TR><TD align=right width='25%' style=\"padding-left:20;padding-right: 10;\">fldID</TD><TD style=\"padding-right: 10;\">$record[0]</TD></TR>";
echo "<TR><TD align=right width='25%' style=\"padding-left:20;padding-right: 10;\">fldForumID</TD><TD style=\"padding-right: 10;\">$record[1]</TD></TR>";
echo "<TR><TD align=right width='25%' style=\"padding-left:20;padding-right: 10;\">fldReply</TD><TD style=\"padding-right: 10;\">$record[2]</TD></TR>";
echo "<TR><TD align=right width='25%' style=\"padding-left:20;padding-right: 10;\">fldTid</TD><TD style=\"padding-right: 10;\">$record[6]</TD></TR>";
echo "<TR><TD align=right width='25%' style=\"padding-left:20;padding-right: 10;\">fldDato</TD><TD style=\"padding-right: 10;\">$record[7]</TD></TR>";
echo "<TR><TD align=right width='25%' style=\"padding-left:20;padding-right: 10;\">fldIP</TD><TD style=\"padding-right: 10;\">$record[9]</TD></TR>";
echo "<TR><TD align=right width='25%' style=\"padding-left:20;padding-right: 10;\">fldNavn</TD><TD style=\"padding-right: 10;\"><input type='Text' class='txt' name='frmNavn' size='30' value='$record[3]' MAXLENGTH='50'> </TD></TR>";
echo "<TR><TD align=right width='25%' style=\"padding-left:20;padding-right: 10;\">fldEmail</TD><TD style=\"padding-right: 10;\"><input type='Text' class='txt' name='frmEmail' size='30' value='$record[8]' MAXLENGTH='50'> </TD></TR>";
echo "<TR><TD align=right width='25%' style=\"padding-left:20;padding-right: 10;\">fldTitel</TD><TD style=\"padding-right: 10;\"><input type='Text' class='txt' name='frmTitel' value='$record[4]' size='30' MAXLENGTH='100'> </TD></TR>";
echo "<TR><TD align=right width='25%' style=\"padding-left:20;padding-right: 10;\">fldTekst</TD><TD style=\"padding-right: 10;\"><textarea name='frmTekst' cols='40' rows='5' wrap='virtual'>$record[5]</textarea></TD></TR>";
?>
</TABLE>
</TD></TR>
<tr><td align=center width=50%><input class="but" type="reset" name="Reset" value="Ryd alt"></td><td align=center width=50%><input class="but" type="submit" name="Submit" value="Opdater"></td></tr>
</table></form>
</TD></TR></TABLE>
<br>
&nbsp;</td>
      </tr>
    </table>
    </td>
  </tr>
</table>

</body>
</html>
<?PHP
}
?>