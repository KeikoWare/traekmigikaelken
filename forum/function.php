<?PHP
Function ReMessage($Layer,$ID,$VarPage){
	// Layer: how many spaces to insert in
	$rs2 = mysql_query("SELECT * FROM forum WHERE fldReply=".$ID." AND fldForumID=". $iCurrentForum ." ORDER BY fldID DESC");

	While($record2 = mysql_fetch_row($rs2)){
		For(i=0;i<Layer;i++){
			Echo "<img src='billeder/blank.gif' width='12' height='12'>"
		}
		Echo "<img src='billeder/re.gif' width='36' height='12'>&nbsp;<B><A class='reply' href='?menu=gaestebog&side=reply.php&page=$VarPage&ID=$record2[0]'>$record2[4]</A></B> af <A class='forum' href='mailto:$record2[8]'>$record2[3]</A> <I class='forum'> $record2[6], $record2[7]</I>";
		If($record2[6]>(Date-2)){
			echo"&nbsp;<img src='billeder/new.gif' width='34' height='12'>";
		}
		echo "<BR>\n";
		ReMessage($Layer+1,$record2[0],$Varpage);		
	}
}


Function fFormat($sString){

	If($sString != ""){
		If(strpos($sString,"'") != 0){ 
			str_replace($sString,"'","''")
		}
	}
}
?>