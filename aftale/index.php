<?PHP
// Copyright KeikoWare 2009
// Aftale forslag og beslutnings modul
// index.php Version 0.1

// sessionen startes
session_start();

// Der oprettes forbindelse til databasen
$aftaleID=@$_GET["aftale"];
$mulighedID=@$_GET["mulighed"];
$svarID=@$_GET["svar"];

?>
<h1>Aftale</h1>
<?php
if($action == "new"){
	echo "<form name=nyaftale action=\"functionz.php\" method=post>";
	echo "Dit navn: <input type=test name=navn size=16><br>";
	echo "Din mail: <input type=test name=mail size=16><br>";
	echo "Aftalens emne / overskrift<br> <input type=text name=overskkrift size=36><br>";
	echo "<input type=submit >";
	
	echo "</form>";
}

?>
	</body>
</html>