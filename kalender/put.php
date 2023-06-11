<?PHP
// Grundejerforeningen Langekærs beboer portal
// Copyright KeikoWare 2005.
// upload.php Version 0.1

// sessionen startes
session_start();

// Der oprettes forbindelse til databasen
include( "../dbconnect/dbconnect.php" ); 

$Dato = addslashes($_POST["Dato"]);
$Titel = addslashes($_POST["Titel"]);
$Tekst = addslashes($_POST["Tekst"]);
$Kategori = addslashes($_POST["Kategori"]);
$User = addslashes($_POST["User"]);
$Gentag = addslashes(@$_POST["Gentag"]);
$SQL = "INSERT INTO kalender ( dato, titel, tekst, kategori, user, gentag, tidsstempel) VALUES ('$Dato', '$Titel', '$Tekst', '$Kategori', '$User', '$Gentag', NOW() )";
	$result = mysql_query($SQL);
	mysql_query("INSERT INTO `log` ( `id` , `userID`, `navn`, `tid` , `event` ) VALUES ('', '" . $_SESSION['userID'] . "', '" . $_SESSION['navn'] . "', NOW( ) , 'Kalender aftale oprettet : " . $Tekst . "');");
// echo $SQL ."<br>";
// echo mysql_error();
	header ("Location: ../?menu=109&dag=".substr($Dato,-2)."&maaned=".substr($Dato,5,2)."&aar=".substr($Dato,0,4));
	exit;
?>