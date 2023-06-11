<?PHP
// Klan side for de ni, som ikke kan finde et navn til deres klan
// Copyright KeikoWare 2006.
// index.php Version 0.1

// sessionen startes
session_start();

// Der oprettes forbindelse til databasen
include( "dbconnect/dbconnect.php" ); 

// statistik
include( "stats/stats.php" ); 

// Statistikken startes
// include( "stats/stats.php" ); 

// Variabel defineres så includes ikke kan loades uden main page!
$keiko="keiko";


@$menu = $_GET["menu"];
if($menu < 100 || $menu > 122) $menu = 101;

@$action = $_GET["action"];
?>
<html>
<head>
<meta name="robots" content="index,nofollow" />
<title>TRAEK MIG I KAELKEN . DK</title>
<link rel=stylesheet type="text/css" href="css/index.css">
<style type="text/css">
	#banner {
			background-image: url('images/black_ribbon.gif');
			width : 76;
			height : 102;
			position : absolute;
			top : -11;
			left : 150;
			visibility : hidden;
	}
	#shadow {
			background-image: url('images/black_ribbon.gif');
			width : 76;
			height : 102;
			position : absolute;
			top : -11;
			left : 147;
			/* for IE */
			filter			: alpha(opacity=30);
			/* CSS3 standard */
			opacity			: 0.3;
			visibility : hidden;
	}
	#header {
			position : relative;
			top : 0;
			left : 0;
	}
table	{mso-displayed-decimal-separator:"\,";
	mso-displayed-thousand-separator:"\.";}
.xl1529576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6329576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:"dd\/mmm";
	text-align:center;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6429576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6529576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6629576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6729576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6829576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6929576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7029576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7129576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7229576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7329576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7429576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7529576
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
</style>
</head>

<body>
<table align=center width=800 height=100% cellspacing=0 cellpadding=0>
	<tr>
		<td width=120 valign=top>
			<table cellspacing=0 cellpadding=0 width=120>
				<tr>
					<td height=128 width=120 class=logo>
					</td>
				</tr>
				<tr>
					<td height=50 class=menu><br><div align=center><b>Menu</b></div><br>
<?php
	$pic100 = "'images\space.gif'"; // Log ind
	$pic101 = "'images\space.gif'"; // Forside
	$pic102 = "'images\space.gif'"; // galleri
	$pic103 = "'images\space.gif'"; // wium
	$pic104 = "'images\space.gif'"; // skitur
	$pic105 = "'images\space.gif'"; // DHB
 	$pic106 = "'images\space.gif'"; // rynkeby
	$pic107 = "'images\space.gif'"; // aftale
	$pic108 = "'images\space.gif'"; 
	$pic109 = "'images\space.gif'"; // Kalender
	$pic110 = "'images\space.gif'"; // Klanlisten
	$pic111 = "'images\space.gif'"; // Forum
	$pic112 = "'images\space.gif'"; // historie
	$pic113 = "'images\space.gif'"; 	// nyt galleri
	$pic114 = "'images\space.gif'"; 
	$pic115 = "'images\space.gif'"; 	
	$pic116 = "'images\space.gif'"; 
	$pic117 = "'images\space.gif'"; 	
	$pic118 = "'images\space.gif'"; 
	$pic119 = "'images\space.gif'"; // fil upload til internt brug	
	$pic120 = "'images\space.gif'"; // bruger styring
	$pic121 = "'images\space.gif'"; // profil
	$pic122 = "'images\space.gif'"; // statistik
	eval("\$pic$menu = \"'images\dot.gif'\";");
?>
					<img src=<?PHP echo $pic101 ;?> alt="Klik på teksten"><a class=menu href="?menu=101">Forside</a><br>
					<br>
					<img src=<?PHP echo $pic102 ;?> alt="Klik på teksten"><a class=menu href="?menu=102">Billeder</a><br>
					<img src=<?PHP echo $pic113 ;?> alt="Klik på teksten"><a class=menu href="?menu=113">Det nye galleri</a><br>
					<!-- <img src=<?PHP echo $pic111 ;?> alt="Klik på teksten"><a class=menu href="?menu=111">Tavle</a><br> -->
					<img src=<?PHP echo $pic109 ;?> alt="Klik på teksten"><a class=menu href="?menu=109">Kalender</a><br>
					<img src=<?PHP echo $pic107 ;?> alt="Klik på teksten"><a class=menu href="?menu=107">Afstemning</a><br>
					<br>
					<img src=<?PHP echo $pic104 ;?> alt="Klik på teksten"><a class=menu href="?menu=104">Skitur</a><br>
					<img src=<?PHP echo $pic105 ;?> alt="Klik på teksten"><a class=menu href="?menu=105">DHB</a><br>
					<br>
					<img src=<?PHP echo $pic106 ;?> alt="Klik på teksten"><a class=menu href="?menu=106">Rynkeby</a><br>
					<img src=<?PHP echo $pic103 ;?> alt="Klik på teksten"><a class=menu href="?menu=103">Talermanden</a><br>
					<br>
					<img src=<?PHP echo $pic112 ;?> alt="Klik på teksten"><a class=menu href="?menu=112">Klanen</a><br>
					<img src=<?PHP echo $pic110 ;?> alt="Klik på teksten"><a class=menu href="?menu=110">Klanlisten</a><br>
					<br>
					<br>
					<img src=<?PHP echo $pic100 ;?> alt="Klik på teksten"><a class=menu href="?menu=100"><?PHP 
						if(@$_SESSION["godkendt"]){ 
							echo "[Log ud]</a>";
								echo "<br><br>";
								echo "<img src=$pic121 alt=\"Klik på teksten\"><a class=menu href=\"?menu=121&id=".$_SESSION["userID"]."\">Din profil</a><br>";
								echo "<img src=$pic119 alt=\"Klik på teksten\"><a class=menu href=\"?menu=119\">Fil upload</a><br><br>";
								echo "<img src=$pic122 alt=\"Klik på teksten\"><a class=menu href=\"?menu=122\">Statistik</a>";
							
							if(@$_SESSION["admin"]=="ja"){
								echo "<br><br>";
								echo "<img src=$pic120 alt=\"Klik på teksten\"><a class=menu href=\"?menu=120\">Brugere</a><br>";
								echo "<img src='images\space.gif' alt=\"Klik på teksten\"><a class=menu href=\"editor/teksteditor2.php\" target=_blank>Indhold</a>";
								}
						} else { 
							echo "[Log ind]</a>"; 
						} ?>


					</td>
				</tr>
				<tr>
					<td height=15 style="background : url('images/klan_07.gif');font-size:6px;"></td>
				</tr>
				<tr>
					<td height=300 style="background : url('images/klan_08.gif');font-size:6px;"></td>
				</tr>
				<tr>
					<td height=17 style="background : url('images/klan_10.gif');font-size:6px;">&nbsp;</td>
				</tr>
			</table>
		</td>
		<td width=680 valign=top>
			<table cellspacing=0 cellpadding=0 width=680 height=100%>
				<tr>
					<td height=82 class=top ><div id=header><div id=banner name=banner></div><div id=shadow name=shadow></div><img src="images/titel.gif" alt="traekmigikaleken.dk" border=0></div>
					</td>
				</tr>
				<tr>
					<td height=25 style="background : #003399;">&nbsp;
					</td>
				</tr>
				<tr>
					<td valign=top style="padding-left: 15px;">

					<?PHP
switch ($menu) {
    case 100: // Login
    	include("login/login.php");
    	break;
    case 101: // forside
    	$page="forside";
			$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
			echo "<font color=Black>Skitur 2012 - Galleri</font><br>";
			echo "<object width=\"464\" height=\"348\" type=\"application/x-shockwave-flash\" data=\"http://iloapp.keikoware.dk/gallery/swf/embedFlashGallery.swf?albumId=0&galleryLocation=tmik&domainName=keikoware.dk\" name =\"embedFlashGallery\"><param name=\"movie\" value=\"http://iloapp.keikoware.dk/gallery/swf/embedFlashGallery.swf?albumId=0&galleryLocation=tmik&domainName=keikoware.dk\"/><param name=\"quality\" value=\"high\"/><param name=\"bgcolor\" value=\"#000000\"/><param name=\"allowScriptAccess\" value=\"always\"/><param name=\"allowFullScreen\" value=\"true\"/><a href=\"http://tmik.keikoware.dk/#0\">http://tmik.keikoware.dk/#0</a></object>";			
			try {
				include('forside/forside.php');
			} catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			
			if( strstr(@$_SESSION["redaktoer"],$page) OR strstr(@$_SESSION["redaktoer"],"*") ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    case 102: // Billeder
    	$page="billeder";
			$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
			include("galleri/galleri.php");
			if( strstr(@$_SESSION["redaktoer"],$page) OR strstr(@$_SESSION["redaktoer"],"*") ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    case 103: // wium
    	$page="wium";
			$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
			if( strstr(@$_SESSION["redaktoer"],$page) ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    case 104: // skitur
    	$page="skitur";
			$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
			if( strstr(@$_SESSION["redaktoer"],$page) ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    case 105: // DHB
    	$page="dhb";
			$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
			if( strstr(@$_SESSION["redaktoer"],$page) ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    case 106: // rynkeby
    	$page="rynkeby";
			$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
			if( strstr(@$_SESSION["redaktoer"],$page) ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    case 107: // Afstemning
    	$page="afstemning";
			$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
			include("afstemning/afstemning.php");
			if( strstr(@$_SESSION["redaktoer"],$page) OR strstr(@$_SESSION["redaktoer"],"*") ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    case 109: // Kalender
    	$page="kalender";
			$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
			include("kalender/kalender.php");
			if( strstr(@$_SESSION["redaktoer"],$page) OR strstr(@$_SESSION["redaktoer"],"*") ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    case 110: // Klan Listen
    	include("login/klanlisten.php");
    	break;
    case 111: // Forum
    	$page="forum";
			$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
    	switch($action){
    		case 10 : // new
    			include("forum/new.php");
    		
    			break;
    		case 20 : // reply
    			include("forum/reply.php");
    		
    			break;
    		case 30 : // 
    			break;
    		default:
    			include("forum/forum.php");
    	}
			if( strstr(@$_SESSION["redaktoer"],$page) OR strstr(@$_SESSION["redaktoer"],"*") ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    case 112: // Historie
    	$page="historie";
			$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
			if( strstr(@$_SESSION["redaktoer"],$page) OR strstr(@$_SESSION["redaktoer"],"*") ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    case 113: // Nyt Galleri
		$page="Galleri";
			/*$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
			*/
			include("gallery/index.php");
			if( strstr(@$_SESSION["redaktoer"],$page) OR strstr(@$_SESSION["redaktoer"],"*") ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    case 119: // Fil upload
    	$page="filer";
			$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
			//vis sidens filer
			include("upload/filer.php");
			if( strstr(@$_SESSION["redaktoer"],$page) OR strstr(@$_SESSION["redaktoer"],"*") ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    case 120: // Bruger styring
    	switch($action){
    		case 10 : //users
    		include("login/useradm.php");
    		break;
    		case 11 : //users
    		include("login/profil.php");
    		break;
    		default: //users
    		include("login/useradm.php");
    	}
    	break;
    case 121: // Profil
    	include("login/profil.php");
    	break;
    case 122: // Statistik
    	$page="statistik";
			$rs = mysql_query("SELECT * FROM side WHERE navn='$page' ORDER BY version DESC"); 
			if($rs!=""){
				$tekst = mysql_fetch_row($rs);
				print($tekst[2]);
			} else {
				echo mysql_error();
			}
			include("stats/show.php");
			if( strstr(@$_SESSION["redaktoer"],$page) OR strstr(@$_SESSION["redaktoer"],"*") ){    	
				echo "<br><br><a href='editor/teksteditor.php?sidenavn=$page' target=_blank><font color=red><b>RET TEKST</b></font></a>";
			}
    	break;
    	
}
?>
					</td>
				<tr>
			</table>
		<td>	
	</tr>
</table>

</body>
</html>