<?PHP
header("Expires Wed, 6 Sep 2000 11:11:11 GMT");
header("Last-Modified: ".gmdate("D, d m y H:i:s")." GMT");
header("Cache-control: no-cache, must-revalidate"); // HTTP ver 1.1
header("Pragma: no-chache"); // HTTP ver 1.2

$forumID = $HTTP_GET_VARS["forumID"];
$page = $HTTP_GET_VARS["page"];

?>
<html>

<head>

<?PHP
function ReMessage($Layer,$ID,$VarPage,$VarForum){
	// Layer: how many spaces to insert in
	$rs2 = mysql_query("SELECT * FROM forum WHERE fldReply=".$ID." AND fldForumID=". $VarForum ." ORDER BY fldID DESC");
		While($record2 = mysql_fetch_row($rs2)){
			Echo "<FORM class='line'>";
			For($i=0;$i<$Layer;$i++){
				Echo "<img src='../images/blank.gif' width='12' height='12'>";
			}
			Echo "<img src='../images/re.gif' width='36' height='12'>&nbsp;<B><FONT color=darkblue>$record2[4]</FONT></B> af <FONT color=orange>$record2[3]</FONT> <I class='forum'>$record2[6], $record2[7]</I>\n";
			Echo "&nbsp;<INPUT type=button class='delete' value='SLET' onClick=\"if(confirm('Vil du slette post\\n # $record2[4] #?')){parent.top.document.location.href='master_edit.php?ID=$record2[0]&event=delete&page=$VarPage&forumID=$VarForum'};\">&nbsp;";
			Echo "<INPUT type=button class='edit' value='REDIGERS' onClick=\"parent.top.document.location.href='master_edit.php?ID=$record2[0]&event=edit&page=$VarPage&forumID=$VarForum';\"></FORM>\n";
			ReMessage($Layer+1,$record2[0],$Varpage,$VarForum);		
		}
}


function fFormat($sString){

	If($sString != ""){
		If(strpos($sString,"'") != 0){ 
			str_replace($sString,"'","''");
		}
	}
}
?>
<LINK REL=STYLESHEET TYPE="text/css" HREF="admin.css">
<title>Langekær beboer portal forum admin</title>
</head>
<body>

<?PHP
//Her bestemmer du om indlæggets hovedtekst skal vises eller ej
$ShowBody = FALSE;
// Herunder sættes det antal indlæg der skal vises pr side. husk at der til hvert indlæg bliver vist alle svar uanset antallet.
$iPageSize = 5;
// Det aktuelle forum-nummer afgøres
If($forumID<> ""){
$iCurrentForum = strval($forumID);
}
else
{
$iCurrentForum = 1;
}
// Det aktuelle side-nummer afgøres
If($page<>""){
$iCurrentPage = strval($page);
}
else
{
$iCurrentPage = 0;
}
// Der oprettes forbindelse til databasen
$dbConn = mysql_connect("localhost","eurole_dk","669Xhlgw");
mysql_select_db("eurole_dk",$dbConn);
$rs = mysql_query("SELECT * FROM forum WHERE fldReply=0 AND fldForumID=". $iCurrentForum ." ORDER BY fldID DESC");

$iMaxPage = ceil(Mysql_num_rows($rs) / $iPageSize);
If($iCurrentPage>=$iMaxPage){
	$iCurrentPage=$iMaxPage-1;
}

// Den aktuelle sides data hentes fra databasen
$rs = mysql_query("SELECT * FROM forum WHERE fldReply=0 AND fldForumID=". $iCurrentForum ." ORDER BY fldID DESC LIMIT ".($iCurrentPage*$iPageSize).",".$iPageSize);

?>
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
        <Table width="100%"><TR><TD Align="left" width="25%"><B>ADMIN PAGE FOR FORUM <?echo $iCurrentForum?></B></TD><TD Width="50%" align="center"><FORM>
<?PHP
If($iCurrentPage > 0){
	echo "<input type=button class='but' value='Forrige side' onClick=\"parent.top.document.location.href='master_index.php?page=";
	echo ($iCurrentPage-1)."&forumID=".$iCurrentForum."';\">";
}
Else
{
	echo "<input type=button class='butoff' value='Forrige side'>";
}
?>&nbsp;<B>Side <?echo $iCurrentPage+1?> af <?echo $iMaxPage?></B>&nbsp;<?PHP
If($iCurrentPage < ($iMaxPage-1)){
	echo "<input type=button class='but' value='Næste side' onClick=\"parent.top.document.location.href='master_index.php?page=";
	echo ($iCurrentPage+1)."&forumID=".$iCurrentForum."';\">";
}
Else
{
	echo "<input type=button class='butoff' value='Næste side'>";
}
?>        
</FORM></TD><TD Width="25%" Align="right"><FORM><B class="datetime"><?echo Date("d-m-Y h:i:s")?></B></FORM></TD></TR></TABLE>
<!-- DATA START -->
<?PHP
While($record = mysql_fetch_row($rs)){
	Echo "<FORM class='line'><img src='../images/envelopes.gif' width='18' height='21'>&nbsp;<B><FONT color=darkblue>$record[4]</FONT></B> af <FONT color=orange>$record[3]</FONT> <I class='forum'>$record[6], $record[7]</I>\n";
	Echo "&nbsp;<INPUT type=button class='delete' value='SLET' onClick=\"if(confirm('Vil du slette post\\n # $record[4] #?')){parent.top.document.location.href='master_edit.php?ID=$record[0]&event=delete&page=$iCurrentPage&forumID=$iCurrentForum'};\">&nbsp;";
	Echo "<INPUT type=button class='edit' value='REDIGERS' onClick=\"parent.top.document.location.href='master_edit.php?ID=$record[0]&event=edit&page=$iCurrentPage&forumID=$iCurrentForum';\"></FORM>\n";
	ReMessage(1,$record[0],$iCurrentPage,$iCurrentForum);
}
?>

<br>
Tryk på indlæggenes <font color=white>hvide</font> overskrift for at svare. Tryk på svarets <font color=white>hvide</font> overskrift for at læse HELE svaret og for at få muligheden for at svare igen.&nbsp;</td>
      </tr>
    </table>
    </td>
  </tr>
</table>

</body>

</html>