<P><strong><font size="5">Log ind</font></strong></P>
<?PHP
	if(@$_SESSION['godkendt']==1){
		
		echo "<br>Hej ".$_SESSION['navn'].", du er nu logget ind :)<br><br>";
		echo $_SESSION['note'];
		if($_SESSION['admin']){
			echo "<br><br>Du er logget ind som <b>ADMIN</b> og kan derfor rette i indholdet her på hjemmesiden.";
		}
		echo "<br><br><a href='login/logud.php'>LOG UD</a>";
	} else {
?>
<form name="frmlogin" method=post action="login/godkend.php">
Brugernavn:<br>
<input type=text class=txt name="usr" size=40><br>
Adgangskode:<br>
<input type=password class=txt name="pwd" size=40><br>
<input class=but type=submit name="btnsubmit" value="Log Ind">
</form>
<script language="Javascript">
document.frmlogin.usr.focus();
</script>
<?PHP } ?>