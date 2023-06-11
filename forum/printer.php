<?PHP
// Klan side for de ni, som ikke kan finde et navn til deres klan
// Copyright KeikoWare 2006.
// printer.php Version 0.1

// sessionen startes
session_start();


// Der oprettes forbindelse til databasen
include( "../dbconnect/dbconnect.php" ); 

?>
<html>
<head>
<meta name="robots" content="index,nofollow" />
<title>TRAEK MIG I KAELKEN . DK</title>
<link rel=stylesheet type="text/css" href="../css/index.css">
</head>
<?PHP
// forum
include( "forum.php" ); 
?>
</body></html>

