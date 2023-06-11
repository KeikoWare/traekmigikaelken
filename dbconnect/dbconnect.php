<?PHP
// Klan side for de ni, som ikke kan finde et navn til deres klan
// Copyright KeikoWare 2006.
// Der oprettes forbindelse til databasen
/*
 $link = mysql_connect("localhost:3306", "traekmigikaelken_dk", "9PJiKkmmwDBLnWSBAWwd") or die("Could not connect: " . mysql_error());
		mysql_select_db("traekmigikaelken_dk");
		if(mysql_errno() <> 0){
			echo "FEJL : " . mysql_error() . "<br>";
		}
*/
date_default_timezone_set('Europe/Copenhagen');
$link = new mysqli("localhost", "traekmigikaelken_dk", "9PJiKkmmwDBLnWSBAWwd", "traekmigikaelken_dk");
if ($link->connect_errno) {
    echo "Failed to connect to MySQL: " . $link->connect_error;
    exit();
}
if (!$link->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $link->error);
    exit();
}
	

function mysql_query($sql_statement){
	global $link;
	return $link->query($sql_statement);
	/*if ($result = $link->query($sql_statement)) {
	  return $result;
	  // Free result set
	  $result->free_result();
	} 
	*/
}	
function  mysql_num_rows($recordset){
	if($recordset !== false){
		return $recordset->num_rows;
	} else {
		return 0;
	}
}

function mysql_fetch_array($recordset){
	if($recordset !== false){
		return $recordset->fetch_array(MYSQLI_BOTH);
	} else {
		return false;
	}
}

function mysql_fetch_row($recordset){
	if($recordset !== false){
		return $recordset->fetch_array(MYSQLI_BOTH);
	} else {
		return false;
	}
}

function mysql_error(){
	global $link;
	return $link->error;
}
function mysql_close(){
	return true;
}

?>