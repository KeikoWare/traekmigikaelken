<?PHP
// Klan side for de ni, som ikke kan finde et navn til deres klan
// Copyright KeikoWare 2006.
// put.php Version 1

// sessionen startes
session_start();

// Der oprettes forbindelse til databasen
include( "../dbconnect/dbconnect.php" ); 


If($HTTP_POST_VARS["frmNavn"] <> ""){
	$frmForum = $HTTP_POST_VARS["frmForum"];
	$frmReply = $HTTP_POST_VARS["frmReply"];
	$frmNavn = $HTTP_POST_VARS["frmNavn"];
	$frmEmail = $HTTP_POST_VARS["frmEmail"];
	$frmTitel = $HTTP_POST_VARS["frmTitel"];
	$frmTekst = $HTTP_POST_VARS["frmTekst"];
	setcookie ("PHPFORUM[Navn]", "$frmNavn");
	setcookie ("PHPFORUM[Mail]", "$frmEmail");
	$frmIP = $_SERVER["REMOTE_ADDR"];
	$result = mysql_query("INSERT INTO forum_indhold (ID, Forum, Reply, Navn, Titel, Tekst, Tid, Dato, Email, IP) VALUES ('','$frmForum', '$frmReply', '$frmNavn', '$frmTitel', '$frmTekst', CURTIME(), CURDATE(), '$frmEmail', '$frmIP')");
	if(mysql_errno() <> 0 ) echo mysql_error();
	$log_text = "Ny forum besked: " . $_SESSION['navn'] . " -> " . $_SERVER['REMOTE_ADDR' ];
	mysql_query("INSERT INTO `log` ( `id` , `userID` , `tid` , `event` ) VALUES ('', '" . $_SESSION['userID'] . "', NOW( ) , '$log_text');");	
	header ("Location: ../?menu=111&forumnummer=".$frmForum);
	exit;
}
?>