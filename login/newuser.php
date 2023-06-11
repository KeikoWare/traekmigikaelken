<h2>Bruger administration</h2><br>

<?PHP
$sql = "SELECT * FROM bruger";
$rs = mysql_query($sql);
?>

<H2>Ny bruger</H2><br><br><br>

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
<table>
<tr>
	<td>Dit fulde navn</td>
	<td> -> <input type=text name="navn" size=40></td>
</tr>
<tr>
	<td>Din email</td>
	<td> -> <input type=text name="email" size=40></td>
</tr>
<tr>
	<td>Din adresse</td>
	<td> -> <input type=text name="adresse" size=40></td>
</tr>
<tr>
	<td>Rettigheder<br>Admin<br>Redaktør</td>
	<td><br> -> <input type=text name="admin" size=40><br> -> <input type=text name="redaktoer" size=40></td>
</tr>
<tr>
	<td></td>
	<td><input class=but type=submit name="btnsubmit" value="Opret"></td>
</tr>
</table>
</form>
Koden til dit brugernavn sendes til den indtastede email-adresse. ALLE felter SKAL udfyldes!