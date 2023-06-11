
<P><FONT size=5><STRONG>Klanlisten</STRONG></FONT></P>
<P><FONT size=2>Her kan du se klanlisten, med adresser og det hele.</FONT></P>
<?php
	if(@$_SESSION["godkendt"]){ 
		$sql= "SELECT * FROM bruger WHERE klanmedlem;";
		$adresse="";
		$recordset = mysql_query($sql);
		echo "<table>";
		$i=0;
		while($data = mysql_fetch_row($recordset)){
			if($i % 3 == 0) echo "<tr>";
			$i++;
			echo "\n<td style=\"width:215px;border:1 solid #cccccc;\"  valign=top><b>" .$data[5] ."</b><br>\n";
			echo "<p style=\"margin : 0 0 0 15px;\">";
			echo (($data[3] != "" ? $data[3] : " ---------------") . "<br>");
			echo (($data[4] != "" ? $data[4] : " ---------------") . "<br>");
			echo (($data[10] != "" ? $data[10] : "----") . "&nbsp;");
			echo (($data[11] != "" ? $data[11] : " ---------------") . "<br>");
			echo (($data[13] != "" ? $data[13] : "---- ----") . " / ");
			echo (($data[12] != "" ? $data[12] : "---- ----") . "<br>");
			echo (($data[1] != "" ? $data[1] : " n/a"));
			echo "</p>";
			echo "<br></td>";
			$adresse = $adresse . $data[1] . ",";
			if ($i % 3 == 0) echo "</tr>";
		}
		// $adresse = "keiko@keikoware.dk,kim@ortvald.dk,";
		echo "</table>";
		echo "<p style=\"margin : 0 0 0 75px;\">";
		echo "<br>";
		echo "<form name=klanmail action='login/mail.php' method=post>";
		echo "<input type=hidden name=adresser value='$adresse'>KLANMAIL<br>Andre modtagere ud over klanen (adskilles med semikolon):<br>";
		echo "<input type=text size=80 name=flereadresser><br>Mail titel:<br>";
		echo "<input type=text size=80 name=titel><br>Mail tekst:<br>";
		echo "<textarea name=tekst cols=70 rows=10></textarea><br><input type=submit value='Send mail'></form></p>";
		
	} else {
		echo "<b>Du skal være logget ind for at se medlemmernes info!!!</b>";
	}
	
?>
