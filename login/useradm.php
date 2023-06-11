<h2>Bruger administration</h2><br>
<b>Eksisterende brugere</b><br>
<?PHP
$sql = "SELECT * FROM bruger";
$rs = mysql_query($sql);

while($data = mysql_fetch_row($rs)){
	print "ID: $data[0] -> <a href='?menu=120&action=11&id=$data[0]'>$data[3], ($data[1])</a><br>";
}

mysql_free_result($rs);
?><br><br>
<SCRIPT LANGUAGE = "JavaScript">
<!-- 
function checkForm (form) {

if (form.navn.value == ""){
alert("Alle felter skal udfyldes!");form.navn.focus();return false;}

if (form.email.value == ""){
alert("Alle felter skal udfyldes!");form.email.focus();return false;}

if (form.adresse.value == ""){
alert("Alle felter skal udfyldes!");form.user.focus();return false;}

else{return true;}}
// --></script>

<form name="frmnewuser" method=post action="login/putuser.php" onSubmit="return checkForm (this)">
<b>Ny bruger</b>
<table>
<tr>
	<td width=100>Fulde navn</td>
	<td> -> <input type=text name="navn" size=50></td>
</tr>
<tr>
	<td>Email</td>
	<td> -> <input type=text name="email" size=50></td>
</tr>
<tr>
	<td>Adresse</td>
	<td> -> <input type=text name="adresse" size=50></td>
</tr>
<tr>
	<td></td>
	<td><input class=but type=submit name="btnsubmit" value="Opret"></td>
</tr>
</table>
</form>
ALLE felter SKAL udfyldes!<br>
Koden sendes til den indtastede email-adresse.<br>
Rettigheder tildeles når brugeren er oprettet.<br>