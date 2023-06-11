<?PHP
session_start();
$sidenavn= "";
@$sidenavn = $_GET["sidenavn"];
if(@$_SESSION["admin"] == "ja" ){
	
	include("../dbconnect/dbconnect.php");
	
	?>
<html>
<head>
	<title>TRÆK MIG I KÆLKEN . DK</title>
	<link rel=stylesheet type="text/css" href="../css/index.css">
	<script src="editor.js" ></script>
</head>
<body>
<center>
<a name="top"></a>
<h1>TRÆK MIG I KÆLKEN . DK<br>
<font color=blue>&nbsp;<? echo $sidenavn; ?>&nbsp;</font></h1>
<table><tr><td>
<b>MENU</b><br>
	<?PHP
	$rs = mysql_query("SELECT navn , MAX(version) FROM side GROUP BY navn");
	While($txt = mysql_fetch_row($rs)){
		print("<a href='teksteditor2.php?sidenavn=");
		print($txt[0]);
		print("'>");
		print($txt[0]." (v. ".$txt[1].")");
		print("</a><br>");
	}
	$rs = mysql_query( "SELECT * FROM side WHERE navn='$sidenavn' ORDER BY version DESC"); 
	$tekst = mysql_fetch_row($rs);
	?>
	<br><br><b>NY SIDE</b><form method=post action="new.php">
	<input type=text name=sidenavn size=16><br><input type=submit value="Opret ny side">
	
	</form>
	
	</td><td>
	<form method=POST action="new.php">
	<input type=hidden name=sidenavn value="<?=$sidenavn;?>">
	<input type=hidden name=version value="<?=$tekst[3];?>">
	<input type=hidden name=menunavn value="<?=$tekst[12];?>">
	<input type=hidden name=menulink value="<?=$tekst[13];?>">
	
	<textarea name="sideindhold" style="width:1000px; height:450px">
	<?PHP
	print($tekst[2]);
	?>
	</textarea><br>
	
	<script language="javascript1.2">
	var _editor_url = "";
	var config = new Object();    // create new config object
	
	config.width = "1000px";
	config.height = "450px";
	config.bodyStyle = "background :  #FFFFFF; font-family : verdana; font-size : 12px; color : #000000;";
	config.debug = 0;
	
	// NOTE:  You can remove any of these blocks and use the default config!
	
	config.toolbar = [
	//  ['fontname'],
	    ['fontsize'],
	//  ['fontstyle'],
	    ['linebreak'],
	    ['bold','italic','underline','separator'],
	    ['strikethrough','subscript','superscript','separator'],
	    ['justifyleft','justifycenter','justifyright','separator'],
	    ['OrderedList','UnOrderedList','Outdent','Indent','separator'],
	//  ['forecolor','backcolor','separator'],
	    ['HorizontalRule','Createlink','InsertImage','htmlmode','separator'],
	//  ['about','help','popupeditor'],
	];
	
	config.fontnames = {
	    "Arial":           "arial, helvetica, sans-serif",
	    "Courier New":     "courier new, courier, mono",
	    "Georgia":         "Georgia, Times New Roman, Times, Serif",
	    "Tahoma":          "Tahoma, Arial, Helvetica, sans-serif",
	    "Times New Roman": "times new roman, times, serif",
	    "Verdana":         "Verdana, Arial, Helvetica, sans-serif",
	    "impact":          "impact",
	    "WingDings":       "WingDings"
	};
	config.fontsizes = {
	    "1 (8 pt)":  "1",
	    "2 (10 pt)": "2",
	    "3 (12 pt)": "3",
	    "4 (14 pt)": "4",
	    "5 (18 pt)": "5",
	    "6 (24 pt)": "6",
	    "7 (36 pt)": "7"
	  };
	
	config.stylesheet = "http://www.traekmigikaelken.dk/css/index.css";
	  
	config.fontstyles = [   // make sure classNames are defined in the page the content is being display as well in or they won't work!
	  { name: "headline",     className: "headline",  classStyle: "font-family: arial black, arial; font-size: 28px; letter-spacing: -2px;" },
	  { name: "arial red",    className: "headline2", classStyle: "font-family: arial black, arial; font-size: 12px; letter-spacing: -2px; color:red" },
	  { name: "verdana blue", className: "headline4", classStyle: "font-family: verdana; font-size: 18px; letter-spacing: -2px; color:blue" }
	
	// leave classStyle blank if it's defined in config.stylesheet (above), like this:
	//  { name: "verdana blue", className: "headline4", classStyle: "" }  
	];
	editor_generate('sideindhold',config);
	</script>
	<input type=submit value=opdater>  (dette er version <?=$tekst[3];?> oprettet <?=$tekst[7];?>)
	</form>
	</td></tr></table>
	
	</center>
	<script> 
	// function editor_generate() { return false; }
	</script>
	</body>
	</html>
<?php
} else {
	echo "<h2><font color=red>INGEN ADGANG</font></h2>";
//	header ("Location: ../index.php");
	exit;
}
?>