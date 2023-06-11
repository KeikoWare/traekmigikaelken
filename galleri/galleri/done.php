<?
function thumbnail($image_path,$thumb_path,$image_name,$thumb_width) 
{ 
    $src_img = imagecreatefromjpeg("$image_path/$image_name"); 
    $origw=imagesx($src_img); 
    $origh=imagesy($src_img); 
    $new_w = $thumb_width; 
    $diff=$origw/$new_w; 
    $new_h=$origh/$diff; 
    $dst_img = imagecreate($new_w,$new_h); 
    imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img)); 

    imagejpeg($dst_img, "$thumb_path/$image_name"); 
    return true; 
} 

$files = $HTTP_POST_FILES["userfile"];

if(copy($files['tmp_name'], "billeder/".$files['name']))
{
	if(thumbnail('billeder','thumbs',$files['name'],'100') ) {
		
		$dbConn = mysql_connect("localhost","eurole_dk","669Xhlgw");
		mysql_select_db("eurole_dk",$dbConn);
		$result = mysql_query("INSERT INTO galleri VALUES (0,'".$files['name']."', '$Tekst', '$User', UNIX_TIMESTAMP())");
		
		header ("Location: ../?menu=galleri&side=galleri&res=succes");
	}else{	
		header ("Location: ../?menu=galleri&side=galleri&res=failure");
	}
	exit;
}
header ("Location: ../?menu=galleri&side=galleri&res=failure");
exit;

?>
