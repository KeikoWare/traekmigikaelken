<?
//----  Settings Start ---\\
// Image Information
$imageWidth=100; 
$imageHeight=-1;
$imageText = 1; //1 = true

// Table Layout
$rows=4;
$cols=4;
$cellwidth = 150;

//Image path
$libery="galleri/thumbs/"; // remember to finish with /
$orgpic="galleri/billeder/";
// Settings Stop----------//

$viewNum=$cols*$rows;
if(!$firstImage){ // so it works if you just enter default.php and show the pictures without having to define the variables in the path
		$firstImage=1;
}
function imagetext($imagename){
	$SQL = "SELECT * FROM galleri WHERE billed='".$imagename."'";
	$result = mysql_query($SQL);
	if(mysql_num_rows($result) == 0){
		return "<i>Ingen tekst</i>";
	}else{
		$record = mysql_fetch_row($result);
		return $record[2];
	}
}

function getImages($firstImage){
	global $viewNum, $imageWidth, $imageHeight, $cols, $libery, $orgpic, $imageText, $cellwidth;  //insures that the function gets the variables 
	$handle=opendir($libery); //opens directory
	while($file=readdir($handle)){ // reads the directory
		$extension=strtolower(substr($file,-4)); // gets the extension of the files
		if(($extension == ".jpg") || ($extension == ".gif") || ($extension == ".png") ){ // checks for image files
			$all[]=$file;  //adds positive results to the archive
		}
	}
	$colscounter=0; // variable that controls the tablebuilding system below
	$firstImage--; // variable that insures that the first image is being showed. Case: It looks best if the links begins with 1 but the array begins with 0
	if(isset($all)){  // if array is set build the table
		print "\n<table>";
		for($i=$firstImage; $i < $firstImage+$viewNum; $i++){
			if($all[$i]){ // checks for running dry of imges
				if($i==$firstImage + $colscounter*$cols){ // if true: new row
					$colscounter++; 
					print "\n<tr>";
				} // add the image
				print "\n<td valign='bottom' class=indhold width='$cellwidth'><br><a href='".$orgpic.$all[$i]."' target='_blank'><img src='".$libery."".$all[$i]."' border='0' width='$imageWidth' alt='Click to Enlarge'></a>";
				if($imageText==1) print "<br>".imagetext($all[$i]);
				print "</td>";
			}
			else{ // stop if no more images
				break;
			}
		}
		print "\n</table><br>";
		$num=count($all);  //number of pictures in actual libery
		makeList($num); // function to make the links list
	}
	else{
		print "Der var desværre ingen billeder i dette bibliotek<br><a href='javascript:history.back()'>Go Back</a>";
	}
}
function makeList($num){
	global $libery, $firstImage, $viewNum; //insures that the function gets the variables
	$numLinks = $num / $viewNum; // finds the number og links to be maked
	for($a=0; $a < $numLinks;$a++){
			$start=$a*$viewNum+1;  //adds 1 because then the links begins with ex. number 1-10 and 11-20 instead of 0-10 and 10-20
			$finish=$a*$viewNum+$viewNum; 
			if($finish>$num){ // insures that the final link returns the right amount of pictures instead of to much
				$finish=$num;
			}
		if(($firstImage >= $start) && ($firstImage < $a * $viewNum+$viewNum)){ // if true: makes no link because you already are on the page
			print "$start - $finish</a>&nbsp;&nbsp;";
		}
		else{ // makes the link
			print "<a href='index.php?menu=galleri&firstImage=$start'>$start - $finish</a>&nbsp;&nbsp;";
		}
	}
}
?>