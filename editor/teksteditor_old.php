<?PHP

session_start();

$sidenavn= "";
@$sidenavn = $_GET["sidenavn"];

if( strstr(@$_SESSION["redaktoer"],$sidenavn) OR strstr(@$_SESSION["redaktoer"],"*") ){
	
	include("../dbconnect/dbconnect.php");
	
	?>
	<html><head><title>TRÆK MIG I KÆLKEN . DK</title>
	<link rel=stylesheet type="text/css" href="../css/index.css">
	
	<script language="Javascript1.2"><!-- // load htmlarea
	_editor_url = "";                     // URL to htmlarea files
	var win_ie_ver = parseFloat(navigator.appVersion.split("MSIE")[1]);
	if (navigator.userAgent.indexOf('Mac')        >= 0) { win_ie_ver = 0; }
	if (navigator.userAgent.indexOf('Windows CE') >= 0) { win_ie_ver = 0; }
	if (navigator.userAgent.indexOf('Opera')      >= 0) { win_ie_ver = 0; }
	if (win_ie_ver >= 5.5) {
	  document.write('<scr' + 'ipt src="' +_editor_url+ 'editor.js"');
	  document.write(' language="Javascript1.2"></scr' + 'ipt>');  
	} else { document.write('<scr'+'ipt>function editor_generate() { return false; }</scr'+'ipt>'); }
	// --></script>
	</head>
	<body>
	<center>
	<a name="top"></a>
	<h1>TRÆK MIG KÆLKEN . DK<br><? echo $sidenavn; ?></h1>
	
	<table><tr><td>
	<?PHP
	$sql = mysql_query( "SELECT * FROM side WHERE navn='$sidenavn' ORDER BY version DESC"); 
	$tekst = mysql_fetch_row($sql);
	?>
	<form method=POST action="new.php">
	<input type=hidden name=sidenavn value="<?=$sidenavn;?>">
	<input type=hidden name=menunavn value="<?=$tekst[12];?>">
	<input type=hidden name=menulink value="<?=$tekst[13];?>">
	<input type=hidden name=version value="<?=$tekst[3];?>">
	
	<textarea name="sideindhold" style="width:800; height:150">
	<?PHP
	print($tekst[2]);
	?>
	</textarea><br>
	
	<script language="javascript1.2">
	var config = new Object();    // create new config object
	
	config.width = "680px";
	config.height = "460px";
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
	<input type=submit value=opdater> (dette er version <?=$tekst[3];?> oprettet <?=$tekst[7];?>)
	</form>
	</td></tr></table>
	
	</center>
	</body>
	</html>
<?PHP
} else {
	echo "<h2><font color=red>INGEN ADGANG</font></h2>";
//	header ("Location: ../index.php");
	exit;
	
}