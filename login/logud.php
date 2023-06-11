<?PHP
// sessionen startes
session_start();

// Der oprettes forbindelse til databasen
include( "../dbconnect/dbconnect.php" ); 

$log_text = "Succesfuld Log UD: " . $_SESSION['navn'] . " -> " . $_SERVER['REMOTE_ADDR' ];
mysql_query("INSERT INTO `log` ( `id` , `userID` , `tid` , `event` ) VALUES ('', '" . $_SESSION['userID'] . "', NOW( ) , '$log_text');");
mysql_close();

session_unset();
header ("Location: ../?menu=login&res=loggedout");
exit;
?>