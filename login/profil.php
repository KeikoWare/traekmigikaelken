<?PHP
	$sql= "SELECT * FROM bruger WHERE id=".$_GET["id"];
	$recordset = mysql_query($sql);
	$rs = mysql_fetch_row($recordset);
	
	if($_SESSION['admin'] == "ja" or $_SESSION['userID'] == $_GET["id"]){
?>
<H2>Bruger profil</H2><br>

<form name="frmUpdateUser" method=post action="login/userupdate.php">
<input type=hidden name=id value="<?PHP echo $_GET["id"]; ?>">
<b>Bruger oplysninger</b>
<table><tr><td width=100>Email </td><td><input type=text name="email" size=50 value="<?PHP echo $rs[1]; ?>"></td></tr>
<tr><td>Adgangskode</td><td><input type=text name="adgangskode" size=50 value="<?PHP echo $rs[2]; ?>"></td></tr>
<tr><td>Dit fulde navn</td><td><input type=text name="navn" size=50 value="<?PHP echo $rs[3]; ?>"></td></tr>
<tr><td>Din adresse</td><td><input type=text name="adresse" size=50 value="<?PHP echo $rs[4]; ?>"></td></tr>
<tr><td>Post nr og by</td><td><input type=text name="postnummer" size=5 value="<?PHP echo $rs[10]; ?>"> <input type=text name="bynavn" size=40 value="<?PHP echo $rs[11]; ?>"></td></tr>
<tr><td>Fastnet tlf.</td><td><input type=text name="fastnet" size=50 value="<?PHP echo $rs[12]; ?>"></td></tr>
<tr><td>Mobil tlf.</td><td><input type=text name="mobil" size=50 value="<?PHP echo $rs[13]; ?>"></td></tr>
<tr><td>Øgenavn</td><td><input type=text name="info" size=50 value="<?PHP echo $rs[5]; ?>"></td></tr>
<tr><td>Medlem</td><td><input type=checkbox name="klanmedlem" <?PHP if($_SESSION["admin"] != "ja") echo "disabled "; if($rs[14]) echo "checked "; ?>></td></tr>
<tr><td>Admin</td><td><input type=checkbox name="admin" <?PHP if($_SESSION["admin"] != "ja") echo "disabled "; if($rs[7] == "ja") echo "checked "; ?>></td></tr>
<tr><td valign=top>Redaktør</td><td>
<?PHP 
	$sSQL = mysql_query("SELECT navn , MAX(version) FROM side GROUP BY navn ORDER BY navn ASC");
	$pagenr = 0;
	While($red = mysql_fetch_row($sSQL)){
		$pagenr++;
		print("<input type=checkbox name=redaktor_$pagenr ");
		if(strstr($rs[6],$red[0]) OR strstr($rs[6],"*")) print("checked ");
		If($_SESSION["admin"] != "ja") echo "disabled";
		print(">".$red[0]."<br>\n");
	}
	echo "</td></tr> 
<tr><td colspan=2 ><input class=but type=submit name='btnsubmit' value='Opdater'></td></tr>
</table></form>";
	If($_SESSION["admin"] == "ja"){
		$txt = "<table width=100%><tr><td width=50%><input type=button onclick=\"location='?menu=120';\" name=cancel value='Retur'></td>";
		$txt.= "<td width=50%><input type=button onclick=\"if(confirm('Vil du slette denne bruger?')) location = '/login/userdelete.php?id=" . $_GET["id"] . "';\" name=delbut value='Slet bruger'> ";
		$txt.= "</td></tr></table>\n<br>";
		echo $txt;
	}
} else{
	echo "Du har ikke adgang til denne funktion!";
	}
	?>