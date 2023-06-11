<?PHP
// Klan side for de ni, som ikke kan finde et navn til deres klan
// Copyright KeikoWare 2006. 

// FORSIDE.PHP

// Seneste krudsedulle på tavlen
			try {

$OldDate  = date ("Y-m-d", mktime (0,0,0,date("m")  ,date("d")-3,date("Y")));
$datediff_sql = "SELECT * FROM forum_indhold WHERE Dato > \"" . date("Y-m-d",mktime (0,0,0,date("m")  ,date("d")-14,date("Y"))) . "\" ORDER BY ID DESC";
echo "<div id=krudseduller><h3>Seneste 14 dages krudseduller på tavlen</h3>\n";
$rs = mysql_query($datediff_sql);
while($record = mysql_fetch_row($rs)){
	echo "<table border=0 width=100%><tr><td><div class=indryk>\n";
	If($record[2]>0){
		echo"&nbsp;<img src=\"../images/re.gif\" width=\"36\" height=\"12\" alt=\"Svar på et andet indlæg\">";
	} else{
		echo "<img src=\"../images/konvolut.gif\">";
	}
	If($record[7]>$OldDate){
		echo"&nbsp;<img src=\"../images/star.gif\" width=\"12\" height=\"12\" alt=\"Indlæg oprettet inden for de sidste 2 dage\">";
	}
	echo "&nbsp;<B><A class=\"reply\"";
	echo " href=\"?menu=111\"";
	echo ">$record[4]</A></B> af $record[3] <I class=\"forum\">$record[7]</I>\n";

	$text = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $record[5]);
	
	echo "<BR>".nl2br($text);
	
	echo "\n";
	echo "</div></td></tr></table>\n\n";
}
echo "</div>";
// echo "<object width=\"464\" height=\"348\" type=\"application/x-shockwave-flash\" data=\"http://iloapp.keikoware.dk/gallery/swf/embedFlashGallery.swf?albumId=0&galleryLocation=tmik&domainName=keikoware.dk" name ="embedFlashGallery"><param name="movie" value="http://iloapp.keikoware.dk/gallery/swf/embedFlashGallery.swf?albumId=0&galleryLocation=tmik&domainName=keikoware.dk\"/><param name=\"quality\" value=\"high\"/><param name=\"bgcolor\" value=\"#000000\"/><param name=\"allowScriptAccess\" value=\"always\"/><param name=\"allowFullScreen\" value=\"true\"/><a href=\"http://tmik.keikoware.dk/#0\">http://tmik.keikoware.dk/#0</a></object>";

//De fire sidst uploadede billede - ændret til senest opdaterede gallerier
	echo "<div id=galleri><br><table border=0 width=100%><tr><td><h3>Senest opdaterede gallerier</h3>\n";
				$sql  = "SELECT billeder_billed.* , billeder_galleri.* ,min(billeder_billed.id) FROM billeder_billed , billeder_galleri WHERE billeder_billed.galleri = billeder_galleri.id AND billeder_galleri.slettet =\"nej\" GROUP BY billeder_billed.galleri ORDER BY billeder_billed.oprettet desc LIMIT 0,4";
				$rs = mysql_query($sql);
				$cc = 0;
				echo "<table align=center><tr>";
				while($data = mysql_fetch_row($rs)){
						$gal = "";
						$sql2 = "SELECT * FROM billeder_galleri WHERE id = ".$data[1]." LIMIT 1";
						$rs2 = mysql_query($sql2);
						$data2 = mysql_fetch_row($rs2);
						$gal = "<b>Galleri:<br><a href=\"?menu=102&galleri=".$data2[0]."&action=1\"><font color=#0000ff>".$data2[1]."</font></b><br>";
					$cc++;
					if($cc>4) $cc = 1;
					echo "<td width=145 align=venter valign=top>".$gal."<img border=0 src=\"galleri/thumbs/".$data[4]."\"><br>".$data[6]."</a></td>";
					if($cc == 4) echo "</tr><tr>\n";
				}
				for(;$cc<4;$cc++){
					echo "<td width=145></td>";
				}
				echo "</tr></table>";
	echo "</td></tr></table>\n\n";
echo "</div>";

// OPDATEREDE SIDER
	$sql  = "SELECT s.menunavn, s.menulink , b.navn , MAX(s.oprettet) as dato FROM side as s, bruger as b WHERE b.id = s.useropret AND s.oprettet > \"2009-01-01\" GROUP BY s.menunavn, s.menulink, b.navn ORDER BY dato DESC;";
	$result = mysql_query($sql); 
	echo "<div id=opdateringer><h3>Sidst opdaterede sider</h3>\n";
	while($data = mysql_fetch_row($result)){
		echo "<a href=\"?menu=" . $data[1] ."\">" . $data[0] . "</a> blev opdateret " . $data[3] . " af " . $data[2] . "<br>\n";
	}
  echo "</div>";
  
// Kalender
echo "<div id=kalender>";
$intMonth = date("n",time()); 
$intYear = date("Y",time()); 
$intDay = date("j",time());

function month_name($M){
	$M_N="ERROR";
	switch($M){
	case "1" : 		$M_N="Januar";break;
	case "2" : 		$M_N= "Februar";break;
	case "3" : 		$M_N= "Marts";break;
	case "4" : 		$M_N= "April";break;
	case "5" : 		$M_N= "Maj";break;
	case "6" : 		$M_N= "Juni";break;
	case "7" : 		$M_N= "Juli";break;
	case "8" : 		$M_N= "August";break;
	case "9" : 		$M_N= "September";break;
	case "10" : 		$M_N= "Oktober";break;
	case "11" : 		$M_N= "November";break;
	case "12" :  		$M_N= "December";break;
	}
	return $M_N;
}
echo "<table border=0 width=100%><tr><td><h3>Dagens begivenheder</h3>\n";

print "<b><u>".$intDay.".&nbsp;".month_name($intMonth)."&nbsp;".$intYear."</u></b><br><br>";
$sSQL1 = "SELECT * FROM kalender WHERE dato=\"".date("Y-m-d",mktime(0,0,0,$intMonth,$intDay,$intYear))."\" AND gentag!=\"on\" ORDER BY tidsstempel DESC";
$sSQL2 = "SELECT * FROM kalender WHERE DAYOFMONTH(dato)=".date("d",mktime(0,0,0,$intMonth,$intDay,$intYear))." AND MONTH(dato)=".date("m",mktime(0,0,0,$intMonth,$intDay,$intYear))." AND gentag=\"on\" ORDER BY tidsstempel DESC";
$sSQL3 = "SELECT * FROM kalender WHERE YEAR(dato)=".date("Y",mktime(0,0,0,$intMonth,$intDay,$intYear))." AND MONTH(dato)=".date("m",mktime(0,0,0,$intMonth,$intDay,$intYear))." ORDER BY dato, tidsstempel DESC";
$rsKalender1 = mysql_query($sSQL1);
$rsKalender2 = mysql_query($sSQL2);
$rsKalender3 = mysql_query($sSQL3);

if((mysql_num_rows($rsKalender2)+mysql_num_rows($rsKalender1))==0) print "<font color=red>Der er ingen begivenheder denne dag</font><br><br>";
While($record = mysql_fetch_row($rsKalender1)){
//	print "<img src=\"images/star.gif\" alt=\"Begivenhed\"> <font color=yellow>".$record[2]."</font> <font color=orange>indsat af ".$record[4]."</font>";
	print "<i>" . $record[2] . "</i><br>\n" . $record[3] . "<br>\n";
	if( strstr(@$_SESSION["redaktoer"],"kalender") AND $_SESSION["UserID"] = $record[5] ){    	
		echo " <input type=button class=but name=btnEdit value=\"Slet\" onclick=\"if(confirm(\"Vil du slette denne forekomst?\")) location.href=\"kalender/slet.php?id=$record[0]&dag=$intDay&maaned=$intMonth&aar=$intYear\";\">";
	}		
	print "<br><br>\n";
}

While($record = mysql_fetch_row($rsKalender2)){
	print "<img src=\"images/star.gif\" alt=\"Begivenhed\"> <font color=yellow>".$record[2]."</font> <font color=orange>indsat af ".$record[4]."</font>";
	if( strstr(@$_SESSION["redaktoer"],"kalender") OR strstr(@$_SESSION["redaktoer"],"*") ){    	
		echo " <input type=button class=but name=btnEdit value=\"Slet\" onclick=\"location.href=\"kalender/slet.php?id=$record[0]&dag=$intDay&maaned=$intMonth&aar=$intYear\";\">";
	}		
	print "<br><br>\n";
}
	echo "</td></tr></table>\n\n<br><br>";
echo "<a name=\"tempskema\"></a><div align=center><a href=\"http://temp.k31k0.dk/image.php?history=10\" target=\"_blank\"><img src=\"http://temp.k31k0.dk/image.php?history=3\" alt=\"Serverens temperatur\" border=0></a></div><br>";
echo "Klik på billedet og få de sidste 10 dages statistik :)<br><br>Termometret aflæses hvert 10 minut og temperaturen gemmes i en database. Dog skal du opdatere siden for at få de aktuelle tal. <br>Jeg har selv lavet PHP scriptet der genererer den smukke graf :)<br>Termometerteret er et industritermometer af typen:<br> Web-IO Thermometer, 10/100BT, 12-24V#57601. Produceret af <a href=\"http://www.wut.de\" target=_blank>W&T</a><br><br><br><br><br>Copyright KeikoWare 2000<br><br>";
echo "</div>";
			} catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}

?>