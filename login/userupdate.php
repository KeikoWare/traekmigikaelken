<?PHP
// sessionen startes
session_start();

// Der oprettes forbindelse til databasen
include( "../dbconnect/dbconnect.php" ); 

$redaktor = "";
	If($_SESSION["admin"] == "ja"){
		$redaktor = "', `redaktoer`='";
		$sSQL = mysql_query("SELECT navn , MAX(version) FROM side GROUP BY navn");
		$pagenr = 0;
		While($red = mysql_fetch_row($sSQL)){
			$pagenr++;
			if(@$_POST["redaktor_$pagenr"] == "on") $redaktor.= " ".$red[0];
		}
	}
$sql = "UPDATE `bruger` SET `email`='".$_POST["email"]."', `mobil`='".$_POST["mobil"]."', `fastnet`='".$_POST["fastnet"]."', `postnummer`='".$_POST["postnummer"]."', `bynavn`='".$_POST["bynavn"]."', `adgangskode`='".$_POST["adgangskode"]."' , `navn`='".$_POST["navn"]."' , `adresse`='".$_POST["adresse"]."' , `info`='".$_POST["info"].$redaktor."', `admin`='";
if(@$_POST["admin"] == "on"){
	$sql.= "ja' ";
} else {
	$sql.="' ";
}
if(isset($_POST["klanmedlem"])){
	$sql .= ", `klanmedlem`=";
	if($_POST["klanmedlem"] == "on"){
		$sql.= " 1";
	} else {
		$sql.=" 0";
	}
}
$sql.=" WHERE `id` = ".$_POST["id"];
mysql_query($sql);
// echo $sql . "<br>";
// echo mysql_error();
		$log_text = "Bruger opdateret: " . $_SESSION['navn'] . " -> " . $_SERVER['REMOTE_ADDR' ];
		mysql_query("INSERT INTO `log` ( `id` , `userID` , `tid` , `event` ) VALUES ('', '" . $_SESSION['userID'] . "', NOW( ) , '$log_text');");
mysql_close();
header ("Location: ../?menu=120&action=11&id=".$_POST["id"]);
exit;
?>