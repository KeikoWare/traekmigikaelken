<?PHP
// TraekMigIKaelken.dk
// Copyright KeikoWare 2006.

// RSS feed til Traek Mig I Kaelken . DK

// Der oprettes forbindelse til databasen
    $link = mysql_connect("localhost", "traekmigikaelken", "qwerty123456") or die("Could not connect: " . mysql_error());
		mysql_select_db("traekmigikaelken_dk");
		if(mysql_errno() <> 0){
			echo "FEJL : " . mysql_error() . "<br>";
		}
		
		$feed =  "<?xml version=\"1.0\"?>
<rss version=\"2.0\">
<channel>

";
// In here goes the channel header 
		$header = "<title>Traek Mig I Kaelken.DK</title>
<description>RSS feed fra TraekMigIKaelken.DK<br /><br /></description>
<link>http://www.traekmigikaelken.dk</link>

";

// in here goes all the items

// Logged in users
$sql = "SELECT log.userid, bruger.navn, MAX(log.tid)
FROM `log` left join `bruger` on bruger.id = log.userid
WHERE log.event LIKE '%Succesfuld Log IND%'
GROUP BY log.userid, bruger.navn
ORDER BY log.tid DESC
LIMIT 0 , 1";
$rs = mysql_query($sql);
$txt = "";
while($data = mysql_fetch_array($rs)){
	$txt .= " &#10;<br /><a href='http://www.traekmigikaelken.dk/?menu=112'>[" . $data[2] ."] " .$data[1] ."</a> ";
}
		$item1 = "<item>
<title>Klan aktivitet</title>
<description>Her kan du se hvilke klan medlemmer &#10;
der har brugt siden de seneste 14 dage! $txt <br /><br /></description>
<link>http://www.traekmigikaelken.dk</link>
</item>

";

// Added galleries
		$item2 = "<item>
<title>Nye gallerier og billeder</title>
<description>Disse personer har bidraget i galleriet indefor de sidste 14 dage<br /><br /></description>
<link>http://www.traekmigikaelken.dk</link>
</item>

";


// Added comments in the Forum
		$item3 = "<item>
<title>Nye svinere</title>
<description>Her proklameres nye svinere<br /><br /></description>
<link>http://www.traekmigikaelken.dk</link>
</item>

";


		$feed .= $header . $item1 . $item2 . $item3 . "

</channel>
</rss>
";

header ("content-type: text/xml");

echo $feed;

/*

<?xml version="1.0"?>
<rss version="2.0">
<channel>

<title>The Channel Title Goes Here</title>
<description>The explanation of how the items are related goes here</description>
<link>http://www.directoryoflinksgohere</link>

<item>
<title>The Title Goes Here</title>
<description>The description goes here</description>
<link>http://www.linkgoeshere.com</link>
</item>

<item>
<title>Another Title Goes Here</title>
<description>Another description goes here</description>
<link>http://www.anotherlinkgoeshere.com</link>
</item>

</channel>
</rss>

*/

?>