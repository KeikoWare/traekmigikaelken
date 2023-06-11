<?php 

$dato = date( "Ymd" );
$tid = date("H:m:s");
$REMOTE_ADDR = @$_SERVER["REMOTE_ADDR"];
$HTTP_REFERER = @$_SERVER["HTTP_REFERER"];

// Daily_Hits = stats_daily
$daily1 = "INSERT INTO stats_daily (date, time, ip, ref, sessionid) VALUES ('$dato', '$tid', '$REMOTE_ADDR', '$HTTP_REFERER', '".session_id()."') "; 
$re = mysql_query( $daily1 ); 

// Counter = stats_counter
$main_counter = mysql_query( "SELECT counter FROM stats_counter " ); 
$gy = mysql_fetch_array( $main_counter ); 
$new_count = $gy['counter'] + 1; 
$up = mysql_query( "UPDATE stats_counter SET counter='$new_count'" ); 

//usersonline = stats_online                    
$sql = mysql_query( "SELECT * FROM stats_online" ); 
$users = mysql_num_rows( $sql ); 

$sql = mysql_query( "SELECT * FROM stats_daily WHERE `date`='$dato'" ); 
$daily = mysql_num_rows( $sql ); 

$sql = mysql_query( "SELECT DISTINCT sessionid FROM stats_daily WHERE `date`='$dato'" ); 
$daily_uniqe = mysql_num_rows( $sql ); 

$sql = mysql_query( "SELECT DISTINCT sessionid FROM stats_daily" ); 
$total_uniqe = mysql_num_rows( $sql ); 

$sql = mysql_query("SELECT counter FROM stats_counter"); 
$count = mysql_fetch_array($sql); 

$timeoutseconds = 30; 
$timestamp = time(); 
$timeout = $timestamp-$timeoutseconds; 
$page = $_SERVER["PHP_SELF"];

$tmpSql = "INSERT INTO stats_online VALUES ( 0,'$timestamp','$REMOTE_ADDR','$page' ); ";
$insert = mysql_query( $tmpSql ); 
// echo "$tmpSql<br>"; 
if ( !( $insert ) ) 
{ 
print "Useronline Insert Failed >" . mysql_error();
} 
$delete = mysql_query( "DELETE FROM stats_online WHERE timestamp<$timeout" ); 

if (!( $delete ) ) 
{ 
print "Useronline Delete Failed > "; 
} 
$result = mysql_query("SELECT DISTINCT ip FROM stats_online" ); 
if ( !( $result ) ) 
{ 
print "Useronline Select Error > "; 
} 
$user = mysql_num_rows( $result ); 
if ( $user == '1' ) 
{ 
$online = "$user"; 
} else {
$online = "$user";
}

?> 
