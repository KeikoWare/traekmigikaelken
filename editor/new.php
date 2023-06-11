<?PHP
include("../dbconnect/dbconnect.php");
session_start();
@$version=$_POST["version"];
if($version==""){
	$version=0;
}
@$sideindhold=$_POST["sideindhold"];
@$menunavn=$_POST["menunavn"];
@$menulink=$_POST["menulink"];

if($_POST["sidenavn"]!= "" ){
	$rs = mysql_query("INSERT INTO side ( navn, tekst, version, useropret, oprettet, menunavn, menulink) VALUES ('".$_POST["sidenavn"]."','".$sideindhold."', ".$version." + 1, " . $_SESSION['userID'] . ",NOW() ,'".$menunavn."' ,'".$menulink."' )");
	if(mysql_errno() <> 0){ 
		$log_text = "FEJL i opdatering af indhold: ".$_POST["sidenavn"]." af " . $_SESSION['navn'] . " -> MySQL server fejl";
		mysql_query("INSERT INTO `log` ( `id` , `userID` , `tid` , `event` ) VALUES ('', '" . $_SESSION['userID'] . "', NOW( ) , '$log_text');");
		echo mysql_error();
	}
	$log_text = "Succesfuld Opdatering af indhold: ".$_POST["sidenavn"]." af " . $_SESSION['navn'];
	mysql_query("INSERT INTO `log` ( `id` , `userID` , `tid` , `event` ) VALUES ('', '" . $_SESSION['userID'] . "', NOW( ) , '$log_text');");
	header ("Location: ".$_SERVER['HTTP_REFERER']);
	exit;	
}
	header ("Location: ".$_SERVER['HTTP_REFERER']);
	exit;	
?>
