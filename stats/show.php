<?PHP
// Grundejerforeningen Langekærs beboer portal
// Copyright KeikoWare 2006.
//
//
// show.stats
// version 0.1 (2006-02-12)
//
// SELECT date,count(DISTINCT `sessionid`) as unikke,count(`sessionid`) as total FROM `stats_daily` GROUP BY date ORDER BY date DESC  LIMIT 0 , 30;
//  
// SELECT ip FROM `stats_daily` WHERE date = '20060210' GROUP BY ip;
//  
// SELECT date, time, ip, ref FROM `stats_daily` WHERE date = '20060210' AND ip = '130.227.16.15' ORDER BY time ASC  LIMIT 0 , 30 

	if( @$_SESSION["redaktoer"] != ""){    	
		
		$showstats = @$_GET["showstats"];
		if( $showstats == "" ) $showstats = 1;

		echo "<br><a href=\"?menu=$menu&showstats=1\">Statisitk forside</a><br><br>";
		
		switch($showstats){
			case 1:
				echo "<b>STATS de senseste 30 dage</b><br><table><tr><td> - = DATO = - </td><td>T</td><td> - = UNIKKE = - </td><td>T</td><td> - = TOTAL = - </td></tr>\n";
				$recordset= mysql_query("SELECT date,count(DISTINCT `sessionid`) as unikke,count(`sessionid`) as total FROM `stats_daily` GROUP BY date ORDER BY date DESC  LIMIT 0 , 30;");
				while($data = mysql_fetch_row($recordset)){
					echo "<tr><td align=center><a href=\"?menu=$menu&showstats=2&statsdato=".$data[0]."\">".$data[0]."</a></td><td align=center>|</td><td align=center>".$data[1]."</td><td align=center>|</td><td align=center>".$data[2]."</td></tr>\n";
				}
				echo "</table>\n";
				break;
			
			case 2:
				$statsdato = @$_GET["statsdato"];
				if($statsdato == "") break;
				echo "<br><b>STATS de senseste 30 dage</b><br><table><tr><td> - = DATO = - </td><td>T</td><td> - = IP = - </td></tr>\n";
				$recordset= mysql_query("SELECT date,ip FROM `stats_daily` WHERE date = '$statsdato' GROUP BY ip");
				while($data = mysql_fetch_row($recordset)){
					echo "<tr><td align=center>".$data[0]."</td><td align=center>|</td><td align=center><a href=\"?menu=$menu&showstats=3&statsdato=$statsdato&statsip=".$data[1]."\">".$data[1]."</a></td></tr>\n";
				}
				echo "</table>\n";
				break;
			
			case 3:
				$statsdato = @$_GET["statsdato"];
				if($statsdato == "") break;
				$statsip = @$_GET["statsip"];
				if($statsip == "") break;
				
				echo "<br><b>STATS for $statsip fra den $statsdato</b><br><table><tr><td> - = DATO = - </td><td>T</td><td> - = TIME = - </td><td>T</td><td> - = REF = - </td></tr>\n";
				$recordset= mysql_query("SELECT date, time, ip, ref FROM `stats_daily` WHERE date = '$statsdato' AND ip = '$statsip' ORDER BY time ASC");
				while($data = mysql_fetch_row($recordset)){
					echo "<tr><td align=center>".$data[0]."</td><td align=center>|</td><td align=center>".$data[1]."</a></td><td align=center>|</td><td align=center>".$data[3]."</a></td></tr>\n";
				}
				echo "</table>\n";
				break;
			}
		
		
	}
?>