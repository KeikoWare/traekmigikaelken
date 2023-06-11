<h1>Afstemning</h1>
<br>
Her på siden kommer en afstmening a la doodle...
<br>
<br>
Hav tålmodighed
<br><br>
<?php
	$action = @$_REQUEST["action"];
	if($action == "") $action = 1;
	$pollid = @$_REQUEST["pollid"];

	switch ($action) {
    case 1: // Show all active polls
    	$sql = "SELECT poll_hoved.*, bruger.navn, bruger.info FROM poll_hoved, bruger WHERE bruger.id = poll_hoved.userid AND start_date <= NOW() AND end_date >= NOW() AND deleted = 0;";
    	$rsHead = mysql_query($sql);
    	if(mysql_num_rows($rsHead) == 0){
    		echo "Der er ingen aktive afstemninger!";
    	} else {
    		echo "<b><u>Aktive afstemninger!</u></b><br>";
	    	While($dataHead = mysql_fetch_row($rsHead)) {
	    		echo "<a href='?menu=$menu&action=2&pollid=". $dataHead[0] . "'>" . $dataHead[2] . "</a> af " . $dataHead[12] . " (". $dataHead[9] . " -> " . $dataHead[10] .")<br>";
	    	}
	    }
    	break;
    case 2: // Show all active polls
    	$sql = "SELECT poll_hoved.*, bruger.navn, bruger.info FROM poll_hoved, bruger WHERE bruger.id = poll_hoved.userid AND poll_hoved.id = $pollid;";
    	$rsHead = mysql_query($sql);
    	$dataHead = mysql_fetch_row($rsHead);
    	echo "<h3>" . $dataHead[2] . "</h3>" . nl2br($dataHead[3]) . "<br>";
    	echo "<font style=\"font-size:10px;color:#6c6c6c;\">Oprettet af " . $dataHead[12] . "</font><br>"; 
    	$sql = "SELECT poll_valg.* FROM poll_valg WHERE poll_valg.pollid = $pollid;";
    	$rsChoice = mysql_query($sql);
    	$i = 0;
    	while($dataChoice = mysql_fetch_row($rsChoice)){
    		// $sql = "SELECT * FROM poll_svar WHERE userid = " . $_session["UserID"] . " AND choiceid = " . $dataChoice[0] . ";";
    		// $rsAnswers = mysql_query($sql);
    		echo " Valg nummer " . $i ." -> " . $dataChoice[2] . "<br>";
    		$i++;
    	}
    	break;
	}


?>