<?PHP
	$galleri = @$_GET["galleri"];
	$orderbyfilename = @$_GET["orderbyfilename"];
	$user = @$_SESSION["userID"];

	$arr_users = array();
	$sql = "SELECT * FROM bruger ORDER by id ASC;";
	$rs = mysql_query($sql);
	while($data = mysql_fetch_row($rs)){
		$arr_users[$data[0]] = array("navn" => $data[3], "nickname" => $data[5]);
	}	
?>

<div name=commentdialog id=commentinput style="visibility:hidden;position:absolute;background:white;width:200;padding: 5px 5px 5px 5px;border:1 solid black;"></div>
<SCRIPT LANGUAGE="JScript">
function display_dialog(oCaller, id, kommentar){

	var ol=oCaller.offsetLeft;
  var ot=oCaller.offsetTop;
	while((oCaller=oCaller.offsetParent) != null) { ot += oCaller.offsetTop; ol += oCaller.offsetLeft; }
	document.getElementById('commentinput').innerHTML = "<a href='javascript:hide_dialog();'>[luk]</a><br>Kommentar til billede " + id + "<form action='galleri/updategalleri.php' method='post'><textarea name=tekst cols=25 rows=4>" + kommentar + "</textarea><input type=hidden name=billed value='" + id + "'><input type=hidden name=action value=3><input type=hidden name=galleri value=<?php echo $galleri; ?>><input type=submit value='opdater kommentar'></form>";
	document.getElementById('commentinput').style.left = ol;
	document.getElementById('commentinput').style.top = ot;
	document.getElementById('commentinput').style.visibility = "visible";
	
}
function hide_dialog(){
	document.getElementById('commentinput').style.visibility = "hidden";
}	
</SCRIPT>

	

<?PHP
	switch($action){
		case 1 : // vis galleri
				$sql = "SELECT * FROM billeder_galleri WHERE id = $galleri AND slettet = 'nej' ORDER BY id ASC";
				$rs = mysql_query($sql);
				$data = mysql_fetch_row($rs);
				echo "<div style=\"float:right;\"><input type=button class=\"but\" Value=\"Galleri oversigten\" onclick=\"location.href='?menu=102';\"></div>";
				echo "<h3>Galleri : <font color=#0000ff>".$data[1]."</font><br><span style=\"color : #999999;\">Oprettet af ".$arr_users[$data[3]]["nickname"] ."</span></h3>";
				$lukket = $data[6];
				$owner = $data[3];
				$sql  = "SELECT * FROM billeder_billed WHERE galleri = $galleri AND slettet = 'nej' ORDER BY `id` DESC";
				if($orderbyfilename) $sql = "SELECT * FROM billeder_billed WHERE galleri = $galleri AND slettet = 'nej' ORDER BY org_navn ASC";
				$rs = mysql_query($sql);
				$cc = 0;
				echo "<table align=center><tr>";
				while($data = mysql_fetch_row($rs)){
					$cc++;
					if($cc>4) $cc = 1;
					echo "\n<td width=145 valign=top style=\"border: 1 solid #999999;\"><div style=\"text-align:center;\"><a href='galleri/billeder/".$data[3]."' target=_blank>\n<img border=0 alt=\"".$data[2]."\" src='galleri/thumbs/";
					if( file_exists("galleri/thumbs/".$data[4])) {
						echo $data[4];
					}else {
						echo "nothumb.gif";
					}
					echo "'></a></div>\n".nl2br(stripslashes($data[6]));
					if($user != "") echo "&nbsp;<a href=\"javascript:void(0);\" onclick=\"display_dialog(this," . $data[0] . ",'" . str_replace(chr(13).chr(10),"<br>",$data[6]) . "');\">@</a>";
					if(@$_SESSION["admin"] == 'ja' || $user == $owner){
						echo  "&nbsp;<a href=\"javascript:void(0);\" onclick=\"if(confirm('Vil du slette dette billed?')) location.href='galleri/updategalleri.php?galleri=$galleri&billed=".$data[0]."&action=4'\">x</a>";
						echo  "&nbsp;<a href=\"javascript:void(0);\" onclick=\"if(confirm('Vil du bruge dette billede som forside billede til galleriet?')) location.href='galleri/updategalleri.php?galleri=$galleri&thumb=".$data[4]."&action=10'\">f</a>";
					}
					echo "</td>";
					if($cc == 4) echo "\n</tr><tr>\n";
				}
				for(;$cc<4;$cc++){
					echo "<td width=145></td>";
				}
				echo "</tr></table>";
				
				if(mysql_num_rows($rs) == 0) echo "<font color=#ff0000><h3>Der er desværre ingen billeder i dette galleri</h3></font><br>";
				$txt	=  "<br>Billederne vises i omvendt rækkefølge af hvordan de er uploaded, efter princippet:<br> [<i>sidst ind vises først</i>].<br>Det sikrer at man altid ser de nyeste billeder forrest i galleriet og ikke skal bladre
				helt ned i bunden. Hvis du husker på dette kan du selv styre den rækkefølge du vil have vist billederne hvis du uploader mange billeder. Upload dem bagfra (i omvendt rækkefølge af hvordan du vil have dem vist) så vises de korrekt, som standard.<br>
				<br>Filstørrelse samlet max 100 mb.\n
				<form enctype=\"multipart/form-data\" accept=\"image/jpeg\" action=\"galleri/updategalleri.php\" method=\"post\" >\n
				<input type=\"hidden\" name=\"max_file_size\" value=\"100000000\">\n
				<input type=\"hidden\" name=\"galleri\" value='$galleri'>\n
				<input type=\"hidden\" name=\"action\" value='2'>\n
				Billeder/videoer:<br>
				<input class=\"txt\" name=\"userfile[]\" type=\"file\" size=70 ><br>
				<input class=\"txt\" name=\"userfile[]\" type=\"file\" size=70 ><br>
				<input class=\"txt\" name=\"userfile[]\" type=\"file\" size=70 ><br>
				<input class=\"txt\" name=\"userfile[]\" type=\"file\" size=70 ><br>
				<input class=\"txt\" name=\"userfile[]\" type=\"file\" size=70 ><br>
				<input class=\"txt\" name=\"userfile[]\" type=\"file\" size=70 ><br>
				<input class=\"txt\" name=\"userfile[]\" type=\"file\" size=70 ><br>
				<input class=\"txt\" name=\"userfile[]\" type=\"file\" size=70 ><br>
				<input class=\"txt\" name=\"userfile[]\" type=\"file\" size=70 ><br>
				<input class=\"txt\" name=\"userfile[]\" type=\"file\" size=70 ><br>				
				<br><br>\n
				<input class=\"but\" type=\"submit\" value=\"Upload fil(er)\" ><br>\n</form>";
				
				
				if( strstr(@$_SESSION["redaktoer"],$page) && $lukket == 'nej' ) echo $txt;
				if(@$_SESSION["admin"] == 'ja') echo "<br><br><br><input type=button class=but name=btnClose value='Hent uploadede filer' onclick=\"if(confirm('Vil du hente uploadede filer ind i dette galleri?')) location.href='galleri/updategalleri.php?galleri=$galleri&action=9'\">";
				
			break;
		case 2 : // Vis kommentarer til billede - UDGÅR er klaret på anden vis
			break;
		case 3 : // kommentar til billede - UDGÅR er klaret på anden vis
				break;
		case 4 : // nyt galleri
				$txt	=  "<h3><font color=#0000ff>Opret et nyt galleri</h3><form action=\"galleri/updategalleri.php\" ";
				$txt .=  "method=\"post\" ><input type=\"hidden\" name=\"action\" value='1'>\nNavn: <br><input class=\"txt\" name=\"navn\" type=\"text\" size=40><br><br>\n";
				$txt .=  "Beskrivelse : <br><textarea name=\"beskrivelse\" rows=4 cols=25></textarea><br><br>\n<input class=\"but\" type=\"submit\" value=\"Opret Galleri\" ><br>\n</form>";
				echo $txt;
				break;
		default : // vis galleri oversigt
				if(@$_SESSION["admin"] == 'ja'){
					$sql = "SELECT * FROM billeder_galleri ORDER BY slettet DESC, oprettet DESC";
				} else {
					$sql = "SELECT * FROM billeder_galleri WHERE slettet = 'nej' ORDER BY oprettet DESC";
				}					
				$rs = mysql_query($sql);
				
				if(mysql_num_rows($rs) != 0) echo "<table>";
				
				While($data = mysql_fetch_row($rs)){
					echo "<tr><td style=\"border: 1 solid #666666;\">";
					if($data[5] != "") {
						echo "<div style=\"FLOAT: right; POSITION: relative\"><a href=\"?menu=102&action=1&galleri=".$data[0]."\"><img src=\"galleri/thumbs/".$data[5]."\" border=0 alt=\"".$data[1]."\"></a></div>\n";
					}

					echo "<a href=\"?menu=102&action=1&galleri=".$data[0]."\">".$data[1]."</a><span style=\"color : #999999;\"> oprettet af ".$arr_users[$data[3]]["nickname"] ."</span><br><br>\n".$data[2]."<br><br>";
					if($data[6] == 'ja'){
						echo "<div style=\"color : #999999;\">Lukket for nye billeder.</div>";
					} else {
						echo "<div style=\"color : #999999;\">Åben for tilgang af nye billeder.</div>";
					}
					// echo "<br>".$data[3]." | " . $user . "<br>";
					if(($data[3] == trim($user)) || (@$_SESSION["admin"] == 'ja')){
						if($data[6] == 'ja'){
							echo "<input type=button class=but name=btnClose value='Åbn for tilgang af nye billeder' onclick=\"if(confirm('Vil du åbne dette galleri for uvedkommende uploads?')) location.href='galleri/updategalleri.php?galleri=".$data[0]."&action=6'\">";
						} else {
							echo "<input type=button class=but name=btnClose value='Luk for tilgang af nye billeder' onclick=\"if(confirm('Vil du låse dette galleri for uvedkommende uploads?')) location.href='galleri/updategalleri.php?galleri=".$data[0]."&action=5'\">";
						}
						echo "<br><br>";
						if($data[7] == 'ja'){
							echo "<br><input type=button class=but name=btnClose value='Gendan galleri så alle kan se det igen.' onclick=\"if(confirm('Vil du dendanne dette galleri?')) location.href='galleri/updategalleri.php?galleri=".$data[0]."&action=8'\">";
						} else {
							echo "<br><input type=button class=but name=btnClose value='Slet galleri så ingen kan se det' onclick=\"if(confirm('Vil du slette dette galleri?')) location.href='galleri/updategalleri.php?galleri=".$data[0]."&action=7'\">";
						}
					}
					echo "</td></tr>\n";
				}
				if(mysql_num_rows($rs) != 0) echo "</table>";
				if( strstr(@$_SESSION["redaktoer"],$page)) echo "<br><br><a href=\"?menu=102&action=4\">Opret nyt galleri</a>";
			break;
	}


?>
	
	
	