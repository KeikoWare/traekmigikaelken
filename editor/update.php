<?PHP
include("../dbconnect/dbconnect.php");

if($_POST["sidenavn"]!= "" ){
	$rs = mysql_query("UPDATE sider SET sideindhold='".$_POST["sideindhold"]."', opdateret=UNIX_TIMESTAMP() WHERE sidenavn='".$_POST["sidenavn"]."'");
	mysql_query("INSERT INTO `log` ( `id` , `userID` , `tid` , `event` ) VALUES ('', '" . $_SESSION['userID'] . "', NOW( ) , 'Tekstindhold opdateret : " .$_POST["sidenavn"]. "')");

	header ("Location: teksteditor.php?sidenavn=".$_POST["sidenavn"]);
	exit;	
}
	header ("Location: teksteditor.php");
	exit;	
?>
