<?PHP
// Grundejerforeningen Langekærs beboer portal
// Copyright KeikoWare 2005.
// remove_dead_files.php Version 0.1

// sessionen startes
session_start();

// Der oprettes forbindelse til databasen
include( "../dbconnect/dbconnect.php" ); 

//Script der rydder op i de filer der er slettet i databasen, men ikke på serveren.
//først flyttes alle filer i et temp bib, dernæst flyttes de tilbage der er i databasen.
$filedir = "uploads/";
$trashdir = "trash/";

if ($handle = opendir($filedir)) {
    echo "Directory handle: $handle\n";
    echo "Files:\n";

    while (false !== ($file = readdir($handle))) { 
        $rs = mysql_query("SELECT id FROM `filer` WHERE `filnavn` = '" . $file  . "';");
        if(mysql_num_rows($rs)>0){
        	rename ($filedir . $file, $trashdir . $file)or die ("Could not move file"); 
        	echo "<FONT color=#cccccc>$file</font><br>\n";
        } else {
	        echo "$file<br>\n";
        }

    }


    closedir($handle); 
}
?>