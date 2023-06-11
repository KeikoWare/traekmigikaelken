<?php
function SafeStrip($Src)
{
	if (get_magic_quotes_gpc())
		$Src = stripslashes($Src);
	return $Src;
}

function EncodeDataNumeric($Src)
{
	$Res = SafeStrip($Src);
	if ($Res == "" or (!preg_match( "/^([0-9]+)$/", $Res)))
		$Res = 0;
	return $Res;
}

function ScanDirC($Dir = './')
{
	if (!($OpenDir = @ opendir($Dir)))
		return false;
	while (($DirContent = readdir($OpenDir)) !== false)
		$Files[] = $DirContent;
	sort($Files, SORT_STRING);
	return $Files;
}

function ReplaceTrash($Src){
	$Src = substr(strstr($Src, '='), 1, strlen($Src));
	$Src = str_replace("'", "", $Src);
	$Src = str_replace(";", "", $Src);
	$Src = trim($Src);
	return $Src; 
}
?>
