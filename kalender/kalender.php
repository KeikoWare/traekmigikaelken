<?PHP
// Klan side for de ni, som ikke kan finde et navn til deres klan
// Copyright KeikoWare 2006.

// KALENDER.PHP

$maaned = @$_GET["maaned"];
$aar = @$_GET["aar"];
$dag = @$_GET["dag"];

if(isset($maaned)){
	$intMonth = $maaned;
}else{
	$intMonth = date('n',time());
}

if(isset($aar)){
	$intYear = $aar;
}else{
	$intYear = date('Y',time());
}
if(isset($dag)){
	$intDay = $dag;
}else{
	$intDay = date('j',time());
}
function month_name($M){
	$M_N="ERROR";
	switch($M){
	case "1" :
		$M_N="Januar";break;
	case "2" :
		$M_N= "Februar";break;
	case "3" :
		$M_N= "Marts";break;
	case "4" :
		$M_N= "April";break;
	case "5" :
		$M_N= "Maj";break;
	case "6" :
		$M_N= "Juni";break;
	case "7" :
		$M_N= "Juli";break;
	case "8" :
		$M_N= "August";break;
	case "9" :
		$M_N= "September";break;
	case "10" :
		$M_N= "Oktober";break;
	case "11" :
		$M_N= "November";break;
	case "12" :
		$M_N= "December";break;
	}
	return $M_N;
}
function build_month($month,$year){
	print "<table cellspacing=0 cellpadding=0 class=calender><tr>\n";
	print "<td colspan=7 align=center class=calmonth>";
	print month_name($month);
	print " ".$year."</td></tr>\n<tr><td class=cal>Man</td><td class=cal>Tir</td><td class=cal>Ons</td><td class=cal>Tor</td><td class=cal>Fre</td><td class=cal>Lør</td><td class=cal>Søn</td></tr>\n";
	$nrDay = date('w', mktime(0,0,0,$month,1,$year));
	$DaysInMonth = date('t',mktime(0,0,0,$month,1,$year));
	if($nrDay==0) $nrDay=7;
	$nrDay = -$nrDay + 2;
	for($col=1;$col<7;$col++){
		print "<tr>";
		for($row=1;$row<8;$row++){
			print "<td align=center";
			if(($nrDay >0) && ($nrDay <= $DaysInMonth)){
				if(($GLOBALS["intDay"]==$nrDay) && ($GLOBALS["intMonth"]==$month) && ($GLOBALS["intYear"]==$year)) print " style=\"background-color: gold;\" ";
				print " class=calday onclick=\"location.href='?menu=109&dag=".$nrDay."&maaned=".$month."&aar=".$year."';\">";
				$rs1 = mysql_query("SELECT * FROM kalender WHERE dato='".date('Y-m-d',mktime(0,0,0,$month,$nrDay,$year))."'");
				$rs2 = mysql_query("SELECT * FROM kalender WHERE DAYOFMONTH(dato)=".date('d',mktime(0,0,0,$month,$nrDay,$year))." AND MONTH(dato)=".date('m',mktime(0,0,0,$month,$nrDay,$year))." AND gentag='on'");
				if((mysql_num_rows($rs1) + mysql_num_rows($rs2))==0){
					print $nrDay;
				}else{
					print "<font color=red><b>".$nrDay."</b></font>";
				}
					
			}else{
				print " class=cal>&nbsp;";
			}
			print "</td>";
			$nrDay++;
		}
		print "</tr>\n";
	}
	print "</table>\n";
//	print $DaysInMonth." * ".$month." * ".$year;
}

print "<table border=0 width=100% cellspacing=0 ><tr><td width=250 align=left valign=top>";

print "<table border=0 cellspacing=0 align=center>";

print "<tr><td align=center colspan=3>";
build_month($intMonth,$intYear);
print "</td></tr>";
print "<tr><td align=left>";
print "<input type=button value='<<' class=b_on onclick=\"location.href='?menu=109&dag=1&maaned=".date('n',mktime(0,0,0,$intMonth-1,1,$intYear))."&aar=".date('Y',mktime(0,0,0,$intMonth-1,1,$intYear))."';\">";
print "</td><td align=center>";
print "<input type=button value='I dag' class=b_on onclick=\"location.href='?menu=109&dag=".date('d',time())."&maaned=".date('n',time())."&aar=".date('Y',time())."';\">";
print "</td><td align=right>";
print "<input type=button value='>>' class=b_on onclick=\"location.href='?menu=109&dag=1&maaned=".date('n',mktime(0,0,0,$intMonth+1,1,$intYear))."&aar=".date('Y',mktime(0,0,0,$intMonth+1,1,$intYear))."';\">";
print "</td></tr>";
print "</table>";
print "</td><td valign=top>";


$sSQL1 = "SELECT * FROM kalender WHERE dato='".date('Y-m-d',mktime(0,0,0,$intMonth,$intDay,$intYear))."' AND gentag!='on' ORDER BY tidsstempel DESC";
$sSQL2 = "SELECT * FROM kalender WHERE DAYOFMONTH(dato)=".date('d',mktime(0,0,0,$intMonth,$intDay,$intYear))." AND MONTH(dato)=".date('m',mktime(0,0,0,$intMonth,$intDay,$intYear))." AND gentag='on' ORDER BY tidsstempel DESC";
$sSQL3 = "SELECT * FROM kalender WHERE YEAR(dato)=".date('Y',mktime(0,0,0,$intMonth,$intDay,$intYear))." AND MONTH(dato)=".date('m',mktime(0,0,0,$intMonth,$intDay,$intYear))." ORDER BY dato, tidsstempel DESC";
$rsKalender1 = mysql_query($sSQL1);
$rsKalender2 = mysql_query($sSQL2);
$rsKalender3 = mysql_query($sSQL3);
$antalbegivenheder =  mysql_num_rows($rsKalender3);

print "<table width=100% class=calender cellspacing=0><tr><td class=calmonth>".month_name($intMonth)."&nbsp;".$intYear."</td></tr></table><br>";
print "<p style=\"margin-left:1cm\">";
if($antalbegivenheder==0){
	print "<font color=#a0a0a0>Der er ingen begivenheder denne måned</font><br>";
} else {
	if($antalbegivenheder==1){
		print "<font color=Blue>Der er 1 begivenhed denne måned</font><br>";
	} else {
		print "<font color=Blue>Der er ". $antalbegivenheder ." begivenheder denne måned</font><br>";
	}
}
print "<br><b>".$intDay.".&nbsp;".month_name($intMonth)."&nbsp;".$intYear."</b><br><br>";

$antalbegivenheder =  mysql_num_rows($rsKalender1)+mysql_num_rows($rsKalender2);
if($antalbegivenheder==0) print "<font color=#a0a0a0>Der er ingen begivenheder denne dag</font><br>";

 
While($record = mysql_fetch_row($rsKalender1)){
	print "<i>" . $record[4] . "</i><br>\n" . nl2br($record[5]) . "<br>\n";
	if( strstr(@$_SESSION["redaktoer"],"kalender") AND $_SESSION["UserID"] = $record[7] ){    	
		echo " <input type=button class=but name=btnEdit value='Slet' onclick=\"if(confirm('Vil du slette denne forekomst?')) location.href='kalender/slet.php?id=$record[0]&dag=$intDay&maaned=$intMonth&aar=$intYear';\">";
	}		
	print "<br><br>\n";
}

While($record = mysql_fetch_row($rsKalender2)){
	print "<i>" . $record[4] . "</i><br>\n" . $record[5] . "<br>\n";
	if( strstr(@$_SESSION["redaktoer"],"kalender") AND $_SESSION["UserID"] = $record[7] ){    	
		echo " <input type=button class=but name=btnEdit value='Slet' onclick=\"location.href='kalender/slet.php?id=$record[0]&dag=$intDay&maaned=$intMonth&aar=$intYear';\">";
	}		
	print "<br><br>\n";
}
print "</p>";


			if( strstr(@$_SESSION["redaktoer"],"kalender") OR strstr(@$_SESSION["redaktoer"],"*") ){    	
print "<br><table width=100% cellspacing=0 class=calender><tr><td class=calmonth>Ny aftale</td></tr></table>";
?>
<p style="margin-left:1cm">
<form name="KalenderForm" action="kalender/put.php" method=post>
<input type=hidden value=<?=$_SESSION["userID"];?> name=User>
<b>Titel</b><br>
<input class=txt type=text name="Titel" size=40><br>
<b>Tekst</b><br>
<textarea name="Tekst" cols=40 rows=4></textarea><br>
Invitation? <input type=checkbox name="invitation" onchange="javascript:toggle();"><br><div id=invitation style="display: none;">
	
	<table>
		<tr>
			<td>Mulige deltagere<br><select  name="available" style="width:150;" size="10" ondblclick="moveOver();">";
<option value="1">Navn[1]</option>
<option value="2">Navn[2]</option>
<option value="3">Navn[3]</option>
<option value="4">Navn[4]</option>
<option value="5">Navn[5]</option>
<option value="6">Navn[6]</option>
</select></td>
			<td><input type=button name="->" value="->" onClick="javascript:addpeople();"><br><br><input type=button name="<-" value="<-" onClick="javascript:removepeople();"></td>
			<td>Valgte deltagere<br><select  name="available" style="width:150;" size="10" ondblclick="moveOver();">";

</select></td>
		</tr>
	</table>
	
	
</div>
<b>Kategori</b>&nbsp;
<select name="Kategori">
<option value=1>Begivenhed
<option value=2>Fødselsdag
<option value=3>Klansamling
<option value=4>Andet
</select>&nbsp;&nbsp;
Årlig?&nbsp;<input name="Gentag" type=checkbox>
&nbsp;&nbsp;
<input class=txt type=hidden name="Dato" size=12 value='<?=date('Y-m-d',mktime(0,0,0,$intMonth,$intDay,$intYear));?>'>
<input type=submit class=but name=btnSubmit value="Indsæt" >
</form>
</p>
<?PHP
}
Print "</td></tr></table>";
?>
