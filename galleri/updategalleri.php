<?php
//session
session_start();

include('../dbconnect/dbconnect.php');

$action = @$_REQUEST["action"];     
//echo "<br>---<br>" . $action . "\n<br>";
$galleri = @$_REQUEST["galleri"];
//echo $galleri . "\n<br>---<br>";
//if($galleri == "") die;

		$log_text = "Galleri: " . $_SESSION['navn'] . " -> " . $_SERVER['REMOTE_ADDR' ] . " | Action: $action | Galleri: $galleri";
		mysql_query("INSERT INTO `log` ( `id` , `userID` , `tid` , `event` ) VALUES ('', '" . $_SESSION['userID'] . "', NOW( ) , '$log_text');");

switch($action){
	case 1: // opret galleri
			$sql = "INSERT INTO `billeder_galleri` ( `navn` , `beskrivelse` , `opretter` , `oprettet` , `billed` , `lukket` , `slettet` ) VALUES ( '".addslashes($_POST["navn"])."', '".addslashes($_POST["beskrivelse"])."', '".$_SESSION["userID"]."', NOW( ) , '', 'nej', 'nej');";
			$result = mysql_query($sql);
			$galleri = mysql_insert_id();
			$billed = "";
			break;
	case 2: // upload billed
			function thumbnail($image_path,$thumb_path,$image_name,$thumb_name,$thumb_width,$type,$org_name) 
			{ 
			    $src_img = false;
			    if($type == 'image/pjpeg' OR $type == 'image/jpeg') {
			    	$src_img = imagecreatefromjpeg("$image_path/$image_name"); 
			    } elseif ($type == 'image/gif') {
			    	$src_img = imagecreatefromgif("$image_path/$image_name"); 
			    }
			    if (!$src_img) { /* See if it failed */
			        $src_img = imagecreatetruecolor (140, 110); /* Create a blank image */
			        $bgc = imagecolorallocate ($src_img, 204, 204, 204);
			        $tc = imagecolorallocate ($src_img, 0, 0, 0);
			        imagefilledrectangle ($src_img, 0, 0, 140,110, $bgc);
			        /* Output an errmsg */
			        imagestring ($src_img, 1, 15, 25, "NO THUMBNAIL FOR:", $tc);
			        imagestring ($src_img, 1, 15, 45, "$org_name", $tc);
			    }
			    
			    $origw=imagesx($src_img); 
			    $origh=imagesy($src_img); 
			    $new_w = $thumb_width; 
			    $diff=$origw/$new_w; 
			    $new_h=$origh/$diff; 
			    $dst_img = imagecreatetruecolor($new_w,$new_h); 
			    imagecopyresampled($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img)); 
			
			    imagejpeg($dst_img, "$thumb_path/$thumb_name"); 
			    return true; 
			} 
			$result = mysql_query("SELECT COUNT(*) FROM `billeder_billed`;");
			$data = mysql_fetch_row($result);
			$count = ($data[0] + 20000);

			$galleri = @$_POST["galleri"];
			$user = @$_SESSION["userID"];
			$uploadresult = "resultat";
			foreach ($_FILES["userfile"]["error"] as $key => $error) {
		    if ($error == UPLOAD_ERR_OK) {

	        $tmp_name = $_FILES["userfile"]["tmp_name"][$key];
	        $name = $_FILES["userfile"]["name"][$key];

					$org_name = $_FILES["userfile"]["name"][$key];
					$ext = strtolower(substr(strrchr($org_name, '.'), 1));
					$org_size = @$_FILES["userfile"]["size"][$key];
					$pic_name = "pic_".($count+$key).".".$ext;
					$tmb_name = "tmb_".($count+$key).".jpg";
					$type = $_FILES["userfile"]["type"][$key];
					if(copy($tmp_name, "billeder/".$pic_name))
					{
						thumbnail('billeder','thumbs',$pic_name,$tmb_name,'140',$type,$org_name);
						$result = mysql_query("INSERT INTO billeder_billed VALUES ('',$galleri,'".$org_name."','".$pic_name."','".$tmb_name."', '$user', '' , now(),'nej','nej','".$org_size."')");
						if(mysql_errno() == 0){
							$result = mysql_query("UPDATE `billeder_galleri` SET `billed` = '$tmb_name' WHERE id = $galleri LIMIT 1;");
							if(mysql_errno() != 0) echo mysql_error();
						}
						$uploadresult += "_$key:succes";
			    }	else{	
						$uploadresult += "_$key:CpYeRRor";
					} // IF copy
		    }	else{	
					$uploadresult += "_$key:UpeRRor";
				} // IF upload_error_ok
			} // FOREACH file
			header ("Location: ../?menu=102&action=1&galleri=$galleri&res=".urlencode($uploadresult));
			break;
	case 3: // tilføj kommentar
			$billed =  @$_POST["billed"];
			$tekst = addslashes(@$_POST["tekst"]);
			$sql= "UPDATE `billeder_billed` SET `kommentar` = '$tekst' WHERE `id` = $billed;";
			$result = mysql_query($sql);
			// echo $result . "<br>" . $sql;
			header ("Location: ../?menu=102&action=1&galleri=$galleri&res=kommentsucces");
			break;
	case 4: // slet billed
			$billed =  @$_GET["billed"];
			$result = mysql_query("UPDATE `billeder_billed` SET `slettet` = 'ja' WHERE `id` = $billed;");
			header ("Location: ../?menu=102&action=1&galleri=$galleri&res=succes");
			break;
	case 5: // luk galleri
			$galleri = @$_GET["galleri"];
			$result = mysql_query("UPDATE `billeder_galleri` SET `lukket` = 'ja' WHERE `id` = $galleri;");
			header ("Location: ../?menu=102&return=luk&mysql=".mysql_errno());
			break;
	case 6: // åbn galleri
			$galleri = @$_GET["galleri"];
			$result = mysql_query("UPDATE `billeder_galleri` SET `lukket` = 'nej' WHERE `id` = $galleri;");
			header ("Location: ../?menu=102&return=aabn&mysql=".mysql_errno());
			break;
	case 7: // slet galleri
			$galleri = @$_GET["galleri"];
			$result = mysql_query("UPDATE `billeder_galleri` SET `slettet` = 'ja' WHERE `id` = $galleri ;");			
			header ("Location: ../?menu=102&return=slet&mysql=".urlencode(mysql_error()));
			break;
	case 8: // gendan galleri
			$galleri = @$_GET["galleri"];
			$result = mysql_query("UPDATE `billeder_galleri` SET `slettet` = 'nej' WHERE `id` = $galleri;");			
			header ("Location: ../?menu=102&return=gendan&mysql=".mysql_errno());
			break;
	case 9: // behandl uploadede billeder i tmp dir
			function thumbnail($image_path,$thumb_path,$image_name,$thumb_name,$thumb_width,$type,$org_name) 
			{ 
			    $src_img = false;
			    if($type == 'image/pjpeg' OR $type == 'image/jpeg' OR $type == 'jpg') {
			    	$src_img = imagecreatefromjpeg("$image_path/$image_name"); 
			    } elseif ($type == 'image/gif' OR $type == 'gif') {
			    	$src_img = imagecreatefromgif("$image_path/$image_name"); 
			    }
			    if (!$src_img) { /* See if it failed */
			        $src_img = imagecreatetruecolor (140, 110); /* Create a blank image */
			        $bgc = imagecolorallocate ($src_img, 204, 204, 204);
			        $tc = imagecolorallocate ($src_img, 0, 0, 0);
			        imagefilledrectangle ($src_img, 0, 0, 140,110, $bgc);
			        /* Output an errmsg */
			        imagestring ($src_img, 1, 15, 25, "NO THUMBNAIL FOR:", $tc);
			        imagestring ($src_img, 1, 15, 45, "$org_name", $tc);
			    }
			    
			    $origw=imagesx($src_img); 
			    $origh=imagesy($src_img); 
			    $new_w = $thumb_width; 
			    $diff=$origw/$new_w; 
			    $new_h=$origh/$diff; 
			    $dst_img = imagecreatetruecolor($new_w,$new_h); 
			    imagecopyresampled($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img)); 
			
			    imagejpeg($dst_img, "$thumb_path/$thumb_name"); 
			    return true; 
			} 
			$result = mysql_query("SELECT COUNT(*) FROM `billeder_billed`;");
			$data = mysql_fetch_row($result);
			$count = ($data[0] + 19999);

			$user = @$_SESSION["userID"];
			$uploadresult = "resultat";

			if ($handle = opendir('tmp')) {
			    while (false !== ($file = readdir($handle))) { 
			        if ($file != "." && $file != "..") { 
			        		$count++;
									echo "<code><br><br>$file\n";
					        $tmp_name = $file;
					        $name = $file;
				
									$org_name = $file;
									$ext = strtolower(substr(strrchr($org_name, '.'), 1));
									$org_size = filesize("tmp/" . $file);
									$pic_name = "pic_".($count).".".$ext;
									$tmb_name = "tmb_".($count).".jpg";
									$type = $ext;
									echo "<br>...............<br>ext: $ext<br>siz: $org_size<br>pic: $pic_name<br>tmb: $tmb_name<br>typ: $type<br>\n";
									if(copy("tmp/" . $file, "billeder/".$pic_name))
									{
										echo "<br> Copy succes ...\n";
										thumbnail('billeder','thumbs',$pic_name,$tmb_name,'140',$type,$org_name);
									
										$result = mysql_query("INSERT INTO billeder_billed VALUES ('',$galleri,'".$org_name."','".$pic_name."','".$tmb_name."', '$user', '' , now(),'nej','nej','".$org_size."')");
										if(mysql_errno() == 0){
											$result = mysql_query("UPDATE `billeder_galleri` SET `billed` = '$tmb_name' WHERE id = $galleri LIMIT 1;");
											if(mysql_errno() != 0) echo mysql_error();
										} else {
											echo mysql_error();
										}
										
										$uploadresult += "_$file:succes";
							    }	else{	
										$uploadresult += "_$file:CpYeRRor";
									} // IF copy
									unlink("tmp/".$file);
			        }
			    }
			    closedir($handle); 
			}

			header ("Location: ../?menu=102&action=1&galleri=$galleri&res=".urlencode($uploadresult));
			break;
	Case 10: // skift forsidebillede på galleriet
		$tmb_name = @$_REQUEST["thumb"];
		$result = mysql_query("UPDATE `billeder_galleri` SET `billed` = '$tmb_name' WHERE id = $galleri LIMIT 1;");
		header ("Location: ../?menu=102&action=1&galleri=$galleri&res=nytGalleriBillede");
}

header ("Location: ../?menu=102&action=1&galleri=$galleri");
?>