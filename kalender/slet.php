<?PHP
// Grundejerforeningen Langekærs beboer portal
// Copyright KeikoWare 2005.
// upload.php Version 0.1

// sessionen startes
session_start();

// Der oprettes forbindelse til databasen
include( "../dbconnect/dbconnect.php" ); 

$id = @$_GET["id"];
$dag = @$_GET["dag"];
$maaned = @$_GET["maaned"];
$aar = @$_GET["aar"];

	$result = mysql_query("DELETE FROM kalender WHERE ID=$id");
	mysql_query("INSERT INTO `log` ( `id`, `userID`, `navn`, `tid`, `event`) VALUES ('', '" . $_SESSION['userID'] . "', '" . $_SESSION['navn'] . "', NOW( ) , 'Kalender begivenhed slettet : " . $id . "');");
	header ("Location: ../?menu=109&dag=$dag&maaned=$maaned&aar=$aar");
	exit;
?>