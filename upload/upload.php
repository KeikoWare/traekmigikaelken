<?PHP
// Grundejerforeningen Langekærs beboer portal
// Copyright KeikoWare 2005.
// upload.php Version 0.1

// sessionen startes
session_start();

// Der oprettes forbindelse til databasen
include( "../dbconnect/dbconnect.php" ); 

function thumbnail($image_path,$thumb_path,$image_name,$thumb_width) 
{ 
    $src_img = imagecreatefromjpeg($image_path.$image_name); 
    $origw=imagesx($src_img); 
    $origh=imagesy($src_img); 
    $new_w = $thumb_width; 
    $diff=$origw/$new_w; 
    $new_h=$origh/$diff; 
    $dst_img = imagecreate($new_w,$new_h); 
    imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img)); 
		return true;
}


$konfiguration["upload_bibliotek"] = "uploads/";
$konfiguration["thumb_bibliotek"] = "uploads/thumbs/";
$konfiguration["max_stoerrelse"] = "10000";

/* Hvor flytter vi fra og til */
$fra = $_FILES["upfil"]["tmp_name"];
$til = $konfiguration["upload_bibliotek"] . $_FILES["upfil"]["name"];

/* Accepterer vi filens stoerrelse? */
$fil_stoerrelse = filesize($fra)/1024;
if($fil_stoerrelse > $konfiguration["max_stoerrelse"]) {
  die("Desværre - filen er for stor. Jeg accepterer kun " . 
       $konfiguration["max_stoerrelse"] . "kb, og din fil fylder " . 
       ceil($fil_stoerrelse, 1) . " kb");
}

/* Saa koerer vi */
if(move_uploaded_file($fra, $til)){
	if (strstr($_FILES["upfil"]["type"],"jpeg")){
			$succes = thumbnail($konfiguration["upload_bibliotek"],$konfiguration["thumb_bibliotek"],$_FILES['upfil']['name'],'100');
	}
	mysql_query("INSERT INTO `filer` ( filnavn, side, dato, tekst, type, timestamp, sti, user) VALUES ( '" . $_FILES['upfil']['name'] . "', '" . $_POST["type"] . "', '" . $_POST["dato"] . "','" . $_POST["beskrivelse"] . "', '" . $_FILES['upfil']['type'] . "', NOW(), 'upload/uploads/', " . $_SESSION['userID'] . ")");
	mysql_query("INSERT INTO `log` ( `id`, `userID`, `tid`, `event` ) VALUES ('', '" . $_SESSION['userID'] . "',  NOW( ) , 'Fil oploaded : " . $_FILES['upfil']['name'] . "')");
	if(mysql_errno() != 0) echo mysql_error();
	if(@$_POST["menu"]){
		header ("Location: ../?menu=".$_POST["menu"]."&res=succes");
	} else {
		header ("Location: ../?res=succes");
	}
	mysql_close();
	
} else {
	if(@$_POST["menu"]){
		header ("Location: ../?menu=".$_POST["menu"]."&res=failure");
	} else {
		header ("Location: ../?res=failure");
	}
}

?>
