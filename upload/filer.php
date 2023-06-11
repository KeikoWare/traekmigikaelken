<?PHP
// Grundejerforeningen Langekærs beboer portal
// Copyright KeikoWare 2005.
// filer.php Version 0.1
$page = "filer";

if($page){
echo "<b>Interne filer til brug for links og billeder</b>";
echo "<table border=1 width=90%>";
$rs = mysql_query( "SELECT * FROM filer WHERE side='".$page."' ORDER BY dato desc, id DESC");
$t = 0;
While((@$data = mysql_fetch_array($rs)) AND ($t <10)){
	echo "<tr>";
	if( strstr(@$_SESSION["redaktoer"],$page) OR strstr(@$_SESSION["redaktoer"],"*") ) echo "<td width=50 align=center><input type=button value='Slet' onClick=\"if(confirm('Vil du slette denne fil?')) location = 'upload/slet.php?menu=" . $menu. "&id=" . $data[0] . "';\"></td>";
	echo "<td width=100 align=center>";
	echo $data["dato"];
	echo "</td><td align=left>";
	echo $data["tekst"];
	echo "</td><td width=20 align=center><a href='".$data["sti"].$data["filnavn"]."' target='_blank'>";
	switch( $data["type"]){
		case "application/msword":
			echo "<img border=0 src='images/docs/word.gif'>";
			break;
		case "application/pdf":
			echo "<img border=0 src='images/docs/acrobat.gif'>";
			break;
		case "application/vnd.ms-powerpoint":
			echo "<img border=0 src='images/docs/powerpoint.gif'>";
			break;
		case "application/vnd.ms-excel":
			echo "<img border=0 src='images/docs/excel.gif'>";
			break;
		case "application/vnd.ms-publisher":
			echo "<img border=0 src='images/docs/publisher.gif'>";
			break;
		case "text/plain":
			echo "<img border=0 src='images/docs/tekst.gif'>";
			break;
		case "application/msaccess":
			echo "<img border=0 src='images/docs/access.gif'>";
			break;
		default:
			echo "<img border=0 src='images/docs/unknown.gif'>";
	}
	echo "</a></td></tr>";
	$t++;
}
echo "</table>\n";

if( strstr(@$_SESSION["redaktoer"],$page) OR strstr(@$_SESSION["redaktoer"],"*") ){    	
?>
<SCRIPT LANGUAGE="JavaScript">
function checkdate(objName) {
	var datefield = objName;
	if (chkdate(objName) == false) {
		datefield.select();
		alert("Datoen er forkert/eksisterer ikke. Prøv igen. Formatet skal være => '2005-06-01' for d. 1. juni 2005.");
		datefield.focus();
		return false;
	}
	else {
		return true;
  }
}

function chkdate(objName) {
	var strDatestyle = "EU";  //European date style
	var strDate;
	var strDateArray;
	var strDay;
	var strMonth;
	var strYear;
	var intday;
	var intMonth;
	var intYear;
	var booFound = false;
	var datefield = objName;
	var strSeparatorArray = new Array("-"," ","/",".");
	var intElementNr;
	var err = 0;
	var strMonthArray = new Array(12);
	strMonthArray[0] = "Jan";
	strMonthArray[1] = "Feb";
	strMonthArray[2] = "Mar";
	strMonthArray[3] = "Apr";
	strMonthArray[4] = "May";
	strMonthArray[5] = "Jun";
	strMonthArray[6] = "Jul";
	strMonthArray[7] = "Aug";
	strMonthArray[8] = "Sep";
	strMonthArray[9] = "Oct";
	strMonthArray[10] = "Nov";
	strMonthArray[11] = "Dec";
	strDate = datefield.value;

	if (strDate.length < 1) {
		return true;
	}

	for (intElementNr = 0; intElementNr < strSeparatorArray.length; intElementNr++) {
		if (strDate.indexOf(strSeparatorArray[intElementNr]) != -1) {
			strDateArray = strDate.split(strSeparatorArray[intElementNr]);
			if (strDateArray.length != 3) {
				err = 1;
				return false;
			}
			else {
				strDay = strDateArray[2];
				strMonth = strDateArray[1];
				strYear = strDateArray[0];
			}
			booFound = true;
	  }
	}
	if (booFound == false) {
		if (strDate.length>5) {
			strDay = strDate.substr(0, 2);
			strMonth = strDate.substr(2, 2);
			strYear = strDate.substr(4);
	  }
	}
	if (strYear.length == 2) {
		strYear = '20' + strYear;
	}
	
	intday = parseInt(strDay, 10);
	if (isNaN(intday)) {
		err = 2;
		return false;
	}
	intMonth = parseInt(strMonth, 10);
	if (isNaN(intMonth)) {
		for (i = 0;i<12;i++) {
			if (strMonth.toUpperCase() == strMonthArray[i].toUpperCase()) {
				intMonth = i+1;
				strMonth = strMonthArray[i];
				i = 12;
			}
		}
		if (isNaN(intMonth)) {
			err = 3;
			return false;
	  }
	}
	intYear = parseInt(strYear, 10);
	if (isNaN(intYear)) {
		err = 4;
		return false;
	}
	if (intMonth>12 || intMonth<1) {
		err = 5;
		return false;
	}
	if ((intMonth == 1 || intMonth == 3 || intMonth == 5 || intMonth == 7 || intMonth == 8 || intMonth == 10 || intMonth == 12) && (intday > 31 || intday < 1)) {
		err = 6;
		return false;
	}
	if ((intMonth == 4 || intMonth == 6 || intMonth == 9 || intMonth == 11) && (intday > 30 || intday < 1)) {
		err = 7;
		return false;
	}
	if (intMonth == 2) {
		if (intday < 1) {
			err = 8;
			return false;
		}
		if (LeapYear(intYear) == true) {
			if (intday > 29) {
				err = 9;
				return false;
			}
		}
		else {
			if (intday > 28) {
				err = 10;
				return false;
			}
		}
	}
	strMonth = "0" + intMonth
	strDay = "0" + intday
	datefield.value = strYear + "-" + strMonth.substring(strMonth.length-2) + "-" + strDay.substring(strDay.length-2);
	
	return true;
}
function LeapYear(intYear) {
	if (intYear % 100 == 0) {
		if (intYear % 400 == 0) { return true; }
	}
	else {
		if ((intYear % 4) == 0) { return true; }
	}
	return false;
}

</script>
<form action="upload/upload.php" method="post" enctype="multipart/form-data">
	<input type=hidden name="type" value=<? echo $page; ?>>
	<input type=hidden name="menu" value=<? echo $menu; ?>>
  <b>Fil til upload:</b><br>
  <input type="file" name="upfil" size=25 /><br>
  <input type=hidden name="dato" onBlur="checkdate(this)" value='<? echo date("Y-m-d");?>' /><br>  
  <b>Beskrivelse af filen</b><br>
  <textarea name="beskrivelse" cols=30 /></textarea><br>
  <input type="submit" value="upload" />
</form>
<?
}
}
?>
