<?PHP
// Grundejerforeningen Langekærs beboer portal
// Copyright KeikoWare 2005.
// slet.php Version 0.1

// sessionen startes
session_start();

// Der oprettes forbindelse til databasen
include( "../dbconnect/dbconnect.php" ); 

$id = @$_GET["id"];
if($id){
	$sql = "DELETE FROM `filer` WHERE `id`='" . $id . "' LIMIT 1";
	mysql_query($sql);
	mysql_query("INSERT INTO `log` ( `id`, `userID`, `navn`, `tid`, `event` ) VALUES ('', '" . $_SESSION['userID'] . "', '" . $_SESSION['navn'] . "', NOW( ) , 'Fil slettet : " . $id . "')");
	mysql_close();
}
	if(@$_GET["menu"]){
		header ("Location: ../?menu=".$_GET["menu"]);
	} else {
		header ("Location: ../?");
	}
?>