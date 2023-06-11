<?php
session_start();
echo "<code>" .$_REQUEST["adresser"] . $_REQUEST["flereadresser"] . "<br><br>";
echo $_REQUEST["titel"] . "<br>";
echo nl2br($_REQUEST["tekst"]) . "</code>";
	if(@$_SESSION["godkendt"]){ 
		/* recipients */
		$to  = $_REQUEST["adresser"] . $_REQUEST["flereadresser"];
		
		/* subject */
		$subject = $_REQUEST["titel"];
		
		/* message */
		$message = nl2br($_REQUEST["tekst"]);
		
		/* To send HTML mail, you can set the Content-type header. */
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "From: klanlisten@keikoware.dk";
		
		/* and now mail it */
		mail($to, $subject, $message, $headers);
		echo "<br><b>Din mail er afsendt succesfuldt</b>";
	}else{
		echo "Du skal være logget ind for at bruge denne funktion";
	}
?>
<br>
<a href="http://www.traekmigikaelken.dk/?menu=110">Tilbage</a>
