<?PHP
// sessionen startes
session_start();

// Der oprettes forbindelse til databasen
include( "../dbconnect/dbconnect.php" ); 

$sql = "DELETE FROM `bruger` WHERE id = " . $_GET["id"];
$t = mysql_query($sql);
mysql_query("INSERT INTO `log` ( `id` , `userID` , `tid` , `event` ) VALUES ('', '" . $_SESSION['userID'] . "', NOW( ) , 'Bruger slettet id:" . $_GET["id"] . "')");
mysql_close();
header ("Location: ../?menu=120");
exit;
?>