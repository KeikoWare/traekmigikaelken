<?PHP
// sessionen startes
session_start();

// Der oprettes forbindelse til databasen
include( "../dbconnect/dbconnect.php" ); 

if($_POST["pwd"]!= "" ){
	$sql = "SELECT * FROM bruger WHERE LCASE(email) = LCASE('" . addslashes($_POST['usr']) . "') AND adgangskode = '" . addslashes($_POST['pwd']). "'";
	//echo $sql . "<br>";
	$rs = mysql_query($sql);
	//echo mysql_error();
	
	if(@mysql_num_rows($rs) == 1){
		$record = mysql_fetch_row($rs);
		$_SESSION['godkendt'] = 1;
		$_SESSION['navn'] = $record[3];
		$_SESSION['email'] = $record[1];
		$_SESSION['userID'] = $record[0];
		$_SESSION['note'] = $record[5];
		$_SESSION['admin'] = $record[7];
		$_SESSION['redaktoer'] = $record[6];
		$log_text = "Succesfuld Log IND: " . $_SESSION['navn'] . " -> " . $_SERVER['REMOTE_ADDR' ];
		mysql_query("INSERT INTO `log` ( `id` , `userID` , `tid` , `event` ) VALUES ('', '" . $_SESSION['userID'] . "', NOW( ) , '$log_text');");
		mysql_close();
		header ("Location: ../?menu=100&res=succes");
		exit;
	}
}
	mysql_close();
	header ("Location: ../?menu=100&res=failure");
	exit;
?>