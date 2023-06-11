<?PHP
// Klan side for de ni, som ikke kan finde et navn til deres klan
// Copyright KeikoWare 2006.
// forum.php Version 1


//Her bestemmer du om indlæggets hovedtekst skal vises eller ej
$ShowBody = TRUE;
$ShowEmail = FALSE;

if(isset($_SESSION['godkendt'])) $ShowEmail = TRUE;
// Herunder sættes det antal indlæg der skal vises pr side. husk at der til hvert indlæg bliver vist alle svar uanset antallet.
$iPageSize = 10;

// Det aktuelle forum-nummer afgøres
$forumnummer = @$_GET["forumnumber"];
if($forumnummer == "") $forumnummer = 1;
If($forumnummer<> ""){$iCurrentForum = strval($forumnummer);}else{$iCurrentForum = 1;}

// Det aktuelle side-nummer afgøres
$forumpage = @$_GET["forumside"];
if($forumpage == "") $forumpage = 0;
If($forumpage<>""){ $iCurrentPage = strval($forumpage);} else {$iCurrentPage = 0;}

$OldDate  = date ("Y-m-d", mktime (0,0,0,date("m")  ,date("d")-3,date("Y")));

function ReMessage($Layer,$ID,$VarPage,$VarForum){
Global $ShowBody;
Global $ShowEmail;
	$OldDate  = date ("Y-m-d", mktime (0,0,0,date("m")  ,date("d")-2,date("Y")));

	$rs2 = mysql_query("SELECT * FROM forum_indhold WHERE Reply=".$ID." AND Forum=". $VarForum ." ORDER BY ID ASC");
		While($record2 = mysql_fetch_row($rs2)){
			Echo "<div class=indryk>";
			Echo "<img src='../images/re.gif'>";
			If($record2[7]>$OldDate){
				echo"&nbsp;<img src='../images/star.gif' width='12' height='12' alt='Indlæg oprettet inden for de sidste 2 dage'>";
			}
			echo "&nbsp;<B><A class='reply'";
			if(@$_SESSION["godkendt"] == 1) echo " href='?menu=111&action=20&forumside=$VarPage&reply=$record2[0]&forumnummer=$VarForum'";
			echo ">$record2[4]</A></B> af ";
			if($ShowEmail) echo "<A class='forum' href='mailto:$record2[8]'>";
			echo $record2[3];
			if($ShowEmail) echo "</A>";
			echo " <I class='forum'>$record2[7]</I>\n";
			If($ShowBody){
				$text = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $record2[5]);
				Echo "<BR>".nl2br($text)."";
			}
			ReMessage($Layer+1,$record2[0],$VarPage,$VarForum);	
			echo "</div>\n\n";
		}
}

function fFormat($sString){

	If($sString != ""){
		If(strpos($sString,"'") != 0){ 
			str_replace($sString,"'","''");
		}
	}
}


$rs = mysql_query("SELECT * FROM forum_indhold WHERE Reply=0 AND Forum=". $iCurrentForum ." ORDER BY ID DESC");

$iMaxPage = ceil(Mysql_num_rows($rs) / $iPageSize);
If($iCurrentPage>=$iMaxPage){
	$iCurrentPage=$iMaxPage-1;
}

// Den aktuelle sides data hentes fra databasen
$rs = mysql_query("SELECT * FROM forum_indhold WHERE Reply=0 AND Forum=". $iCurrentForum ." ORDER BY ID DESC LIMIT ".($iCurrentPage*$iPageSize).",".$iPageSize);


?>
<table width="100%">
  <tr>
    <td width="100%" class=indhold>
     <Table width="100%"><TR><TD Align="left" width="25%" class=indhold><FORM>
<?PHP
	if(@$_SESSION["godkendt"] == 1) echo "<INPUT type=button class=\"but\" Value=\"Nyt Indlæg\" onclick=\"location.href='?menu=111&action=10&reply=0&forumnummer=$iCurrentForum';\">";
	echo "</form></TD><TD Width=\"50%\" align=\"center\" class=indhold><FORM>";
If($iCurrentPage > 0){
	echo "<input type=button class='but' value='Forrige side' onClick=\"parent.top.document.location.href='?menu=111&forumside=";
	echo ($iCurrentPage-1)."&forum=".$iCurrentForum."';\">";
}
Else
{
	echo "<input type=button class='b_off' value='Forrige side'>";
}
?>&nbsp;<B>Side <?echo $iCurrentPage+1?> af <?echo $iMaxPage?></B>&nbsp;<?PHP
If($iCurrentPage < ($iMaxPage-1)){
	echo "<input type=button class='but' value='Næste side' onClick=\"parent.top.document.location.href='?menu=111&forumside=";
	echo ($iCurrentPage+1)."&forum=".$iCurrentForum."';\">";
}
Else
{
	echo "<input type=button class='b_off' value='Næste side'>";
}
?>        
</FORM></TD><TD Width="25%" Align="right" class=indhold><FORM><B class="datetime"><?echo Date("d-m-Y h:i:s")?></B></FORM></TD></TR></TABLE>


<?PHP
While($record = mysql_fetch_row($rs)){
	echo "<div class=indryk>\n";
	echo "<img src='../images/konvolut.gif'>";
	If($record[7]>$OldDate){
		echo"&nbsp;<img src='../images/star.gif' width='12' height='12' alt='Indlæg oprettet inden for de sidste 2 dage'>";
	}
	echo "&nbsp;<B><A class='reply'";
	if(@$_SESSION["godkendt"] == 1) echo " href=\"?menu=111&action=20&forumside=$iCurrentPage&reply=$record[0]&forumnummer=$iCurrentForum\"";
			echo ">$record[4]</A></B> af ";
			if($ShowEmail) echo "<A class='forum' href='mailto:$record[8]'>";
			echo $record[3];
			if($ShowEmail) echo "</A>";
			echo " <I class='forum'>$record[7]</I>\n";
	If($ShowBody){
		$text = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $record[5]);

		Echo "<BR>".nl2br($text)."";
	}
	echo "<br>\n";
	ReMessage(1,$record[0],$iCurrentPage,$iCurrentForum);
	echo "</div><br>\n\n";
}
?>
<a href="forum/printer.php" target="_blank">printer venlig</a><br><br>
<font color=blue>Tryk på indlæggenes overskrift for at svare direkte til den person der har oprettet emnet.</font><br>
</td>

    </td>
  </tr>
</table>