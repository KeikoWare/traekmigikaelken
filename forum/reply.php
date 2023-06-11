<?PHP
// Klan side for de ni, som ikke kan finde et navn til deres klan
// Copyright KeikoWare 2006.
// reply.php Version 1

// Det aktuelle forum-nummer afgøres
$forumnummer = @$_GET["forumnumber"];
if($forumnummer == "") $forumnummer = 1;
If($forumnummer<> ""){$iCurrentForum = strval($forumnummer);}else{$iCurrentForum = 1;}

// Det aktuelle side-nummer afgøres
$forumpage = @$_GET["forumside"];
if($forumpage == "") $forumpage = 0;
If($forumpage<>""){ $iCurrentPage = strval($forumpage);} else {$iCurrentPage = 0;}

// Det aktuelle reply nummer afgøres
$reply = @$_GET["reply"];
if($reply == "") 	header ("Location: ../?menu=111&forumnummer=".$forumnummer);

?>
<!--Some simple form validation below-->
<SCRIPT LANGUAGE = "JavaScript">
<!-- 
function checkForm (form) {

if (form.frmNavn.value == ""){
alert("Skriv venligst dit navn.");form.frmNavn.focus();return false;}

if (form.frmTitel.value == ""){
alert("Skriv venligst en overskrift til dit indlæg.");form.frmTitel.focus();return false;}

if (form.frmTekst.value == ""){
alert("Skriv venligst en besked i dit indlæg.");form.frmTekst.focus();return false;}

else{return true;}}
// --></script>
    <table width="100%">
      <tr>
        <td class="indhold" width="100%">
        <Table width="100%"><TR><TD Width="25%" class="indhold">
        	<form><input type=button class="but" value="Tilbage" onClick="history.go(-1)"></form>
        </TD><TD Align=center  Width="50%" class="indhold">
					<FORM><B>Svar på et indlæg</B></FORM>
				</TD><TD align=right  Width="25%" class="indhold"><FORM><B class="datetime"><?echo Date("d-m-Y h:i:s")?></B></FORM></TD></TR></TABLE>
<?PHP

$motherID = 0;
$childID = $reply;
$hopcount = 0;
$output = "";

While( $motherID == 0 ){
	$rs = mysql_query("SELECT * FROM forum_indhold WHERE ID=".$childID." AND Forum=".$forumnummer);
	$record = mysql_fetch_row($rs);
	if ($record[2] == 0){
		$motherID = $record[0];
		$pic = "<img src='../images/konvolut.gif'>";
	} else {
		$childID = $record[2];
		$pic = "<img src='../images/re.gif' height='12'>";
	}
	$txt = "<div class=indryk>\n";
	$txt .= $pic;
	$txt .= "&nbsp;<B><A class='reply' href='?menu=111&action=20&forumside=$forumpage&reply=$record[0]&forumnummer=$forumnummer'>$record[4]</A></B> af <A class='forum' href='mailto:$record[8]'>$record[3]</A> <I class='forum'> $record[6], $record[7]</I>";
	$txt .= "<BR>$record[5]";
	$output =  $txt . $output;
	$hopcount++;
}
for($i =0;$i < $hopcount;$i++) $output .= "</div>\n";
echo $output;

	$varN = @$HTTP_COOKIE_VARS["PHPFORUM[Navn]"];
	$varE = @$HTTP_COOKIE_VARS["PHPFORUM[Mail]"];
?>
<center>
<center>
<form action="forum/put.php" method="post" onSubmit = "return checkForm (this)">
<input type=hidden name="frmForum" value="<?echo $forumnummer?>">
<input type=hidden name="frmReply" value="<?echo $reply?>">
<table border=3 cellspacing=2 cellpadding=2 bgcolor=efefef>
<tr><td align=center width=50%><font face="arial" size="2" color="#004080"><b>Navn:</b></font></td><td align=center width=50%><?PHP echo $_SESSION["navn"]; ?><input type="hidden" class="txt" name="frmNavn" size="30" value="<?PHP echo $_SESSION["navn"]; ?>" MAXLENGTH="50"></td></tr>
<tr><td align=center width=50%><font face="arial" size="2" color="#004080"><b>Email:</b></font></td><td align=center width=50%><?PHP echo $_SESSION["email"]; ?><input type="hidden" class="txt" name="frmEmail" size="30" value="<?PHP echo $_SESSION["email"]; ?>" MAXLENGTH="50"></td></tr>
<tr><td align=center width=50%><font face="arial" size="2" color="#004080"><b>Overskrift:</b></font></td><td align=center width=50%><input type="Text" class="txt" name="frmTitel" size="30" MAXLENGTH="100"></td></tr>
<tr><td align=center colspan="2"><font face="arial" size="3" color="#004080"><b>Tekst:</b></font></td></tr>
<tr><td align=center colspan="2"><textarea name="frmTekst" cols="55" rows="4" wrap="virtual"></textarea></td></tr>
<tr><td align=center width=50%><input class="but" type="reset" name="Reset" value="Ryd alt"></td><td align=center width=50%><input class="but" type="submit" name="Submit" value="Send"></td></tr>
</table></form>
</TD></TR></TABLE>
<br>
&nbsp;</td>
      </tr>
    </table>