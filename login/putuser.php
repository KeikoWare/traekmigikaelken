<?PHP
// sessionen startes
session_start();
	@$navn = $_REQUEST["navn"];
	@$email = $_REQUEST["email"];
	@$adresse = $_REQUEST["adresse"];
	
// Der oprettes forbindelse til databasen
include( "../dbconnect/dbconnect.php" ); 

if(($navn=="") OR ($email=="")){
	header ("Location: ../?menu=newuser&result=fejl");
	exit;
}else{
	$pass = crypt($email,date('\D\e\t \e\r \u\g\e W \d\a\g z \i \å\r\e\t Y, \k\l. h:i:s'));
	$sql = "INSERT INTO bruger ( email, adgangskode, navn , adresse) VALUES ( '".$email."', '".$pass."', '".$navn."', '".$adresse."')";
	mysql_query($sql);
		$log_text = "Bruger oprettet af: " . $_SESSION['navn'] . " -> " . $_SERVER['REMOTE_ADDR' ] . " | Ny bruger: " .$navn;
		mysql_query("INSERT INTO `log` ( `id` , `userID` , `tid` , `event` ) VALUES ('', '" . $_SESSION['userID'] . "', NOW( ) , '$log_text');");

	if(mysql_errno()<>0){
		echo mysql_errno() . " : " . mysql_error() . "<br>" . $sql;
	}

	/* recipients */
	$to  = $email;
	
	/* subject */
	$subject = "Brugenavn og kode til www.trakmigikaelken.dk";
	
	/* message */
	$message = '
	Du er nu blevet oprettet på <a href="http://www.traekmigikaelken.dk">TRÆK MIG I KÆLKEN dot DK</a>.
	
	Her er dit brugernavn og adgangskode:
	Brugernavn : '.$email.'<br>
	Adgangskode : '.$pass.'<br>
	
	Du kan ændre din adgangskode når du er logget ind, under menupunktet [profil].
	';
	
	/* To send HTML mail, you can set the Content-type header. */
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	
	/* and now mail it */
	// mail($to, $subject, $message, $headers);
	mysql_close();
	header ("Location: ../?menu=120");
	exit;
}
?>