<?php
function ParseHeader($header)
{
	$header = str_replace("\r\n\t", "",$header);
	$header = str_replace("\r\n ", " ",$header);
	$header_array = explode("\r\n", $header);
	$tmp = Array(
		'x-msmail-priority' => 3,
		'importance' => 3,
		'x-priority' => 3,
		'type' => '',
		'subtype' => '',
		'name' => '',
		'charset' => '',
		'boundary' => '',
		'from' => '',
		'to' => '',
		'cc' => '',
		'bcc' => '',
		'subject' => '',
		'date' => '',
		'content-transfer-encoding' => '',
		'content-length' => 0,
		'mime-version' => 0,
		'disposition' => '',
		'filename' => '',
		'content-id' => '0',
		'content-location' => ''
	);
	foreach ($header_array as $SingleHeader)
		if ($SingleHeader!=''){
			$cut_header = explode(":", $SingleHeader, 2);
			if (count($cut_header)>1){
				$name = strtolower(trim($cut_header[0]));
				switch ($name){
				case 'type': break;
				case 'subtype': break;
				case 'content-type':
					$cut_ct = explode(';', trim($cut_header[1]));
					$ct = explode('/', $cut_ct[0]);
					$tmp['type'] = strtolower(trim($ct[0]));
					$tmp['subtype'] = strtolower(trim($ct[1]));
					$c=count($cut_ct);
					for ($k=1; $k<$c; $k++){
						$params = explode('=', $cut_ct[$k], 2);
						$name = strtolower(trim($params[0]));
						if ($name != 'type' && $name != 'subtype')
							$tmp["$name"] = str_replace('"','',trim($params[1]));
					}
					break;
				case 'content-disposition':
					$cut_cd = explode(';', trim($cut_header[1]));
					$tmp['disposition'] = strtolower(trim($cut_cd[0]));
					$c=count($cut_cd);
					for ($k=1; $k<$c; $k++){
						$params = explode('=', $cut_cd[$k], 2);
						$tmp[strtolower(trim($params[0]))] = str_replace('"','',trim($params[1]));
					}
					break;
				case 'content-transfer-encoding':
					$tmp[$name] = strtolower(trim($cut_header[1]));
					break;
				default: $tmp[$name] = trim($cut_header[1]);
				}
			}
		}
	$importance = 3;
	if ($tmp['x-msmail-priority'] == 'High') $importance = 1;
	if ($tmp['x-msmail-priority'] == 'Low') $importance = 5;
	if ($tmp['importance'] == 'High') $importance = 1;
	if ($tmp['importance'] == 'Low') $importance = 5;
	if ($tmp['x-priority'] == '1 (Highest)' || $tmp['x-priority'] == '2 (High)') $importance = 1;
	if ($tmp['x-priority'] == '5 (Lowest)' || $tmp['x-priority'] == '4 (Low)') $importance = 5;
	if ($tmp['x-priority'] == '1' || $tmp['x-priority'] == '2') $importance = 1;
	if ($tmp['x-priority'] == '5' || $tmp['x-priority'] == '4') $importance = 5;
	$tmp['importance'] = $importance;
	return $tmp;
}

function GetCorrectDate($time, $offset)
{
	$time = substr($time,0,31);
	$timestamp = strtotime(substr($time,0,25));
	$sign = substr($time,-5,1);
	$hour = substr($time,-4,2);
	$min = substr($time,-2,2);
	$difference = $hour * 3600 + $min * 60;
	if ($sign == '+') {
		$timestamp -= $difference;
	} elseif ($sign == '-') {
		$timestamp += $difference;
	}
	$def = false;
	$timestamp += TranslateTimeOffset($offset, $def);
	if (!$def) {
		$time_ar = localtime($timestamp, 1);
		$timestamp += $time_ar['tm_isdst']*3600;
	}
	return strftime('%d.%m.%Y %H:%M', $timestamp);
}

function TranslateTimeOffset($str_time_offset, &$def){
	switch($str_time_offset){
		case "cdoEniwetok": $seconds = -43200; break;
		case "cdoMidwayIsland": $seconds = -39600; break;
		case "cdoHawaii": $seconds = -36000; break;
		case "cdoAlaska": $seconds = -32400; break;
		case "cdoPacific": $seconds = -28800; break;
		case "cdoArizona": $seconds = -25200; break;
		case "cdoMountain": $seconds = -25200; break;
		case "cdoCentralAmerica": $seconds = -21600; break;
		case "cdoCentral": $seconds = -21600; break;
		case "cdoMexicoCity": $seconds = -21600; break;
		case "cdoSaskatchewan": $seconds = -21600; break;
		case "cdoIndiana": $seconds = -18000; break;
		case "cdoEastern": $seconds = -18000; break;
		case "cdoBogota": $seconds = -18000; break;
		case "cdoSantiago": $seconds = -14400; break;
		case "cdoCaracas": $seconds = -14400; break;
		case "cdoAtlanticCanada": $seconds = -14400; break;
		case "cdoNewfoundland": $seconds = -12600; break;
		case "cdoGreenland": $seconds = -10800; break;
		case "cdoBuenosAires": $seconds = -10800; break;
		case "cdoBrasilia": $seconds = -10800; break;
		case "cdoMidAtlantic": $seconds = -7200; break;
		case "cdoCapeVerde": $seconds = -3600; break;
		case "cdoAzores": $seconds = -3600; break;
		case "cdoMonrovia": $seconds = 000; break;
		case "cdoGMT": $seconds = 0; break;
		case "cdoBerlin": $seconds = 3600; break;
		case "cdoPrague": $seconds = 3600; break;
		case "cdoParis": $seconds = 3600; break;
		case "cdoWestCentralAfrica": $seconds = 3600; break;
		case "cdoSarajevo": $seconds = 3600; break;
		case "cdoAthens": $seconds = 7200; break;
		case "cdoEasternEurope": $seconds = 7200; break;
		case "cdoCairo": $seconds = 7200; break;
		case "cdoHarare": $seconds = 7200; break;
		case "cdoHelsinki": $seconds = 7200; break;
		case "cdoIsrael": $seconds = 7200; break;
		case "cdoBaghdad": $seconds = 10800; break;
		case "cdoArab": $seconds = 10800; break;
		case "cdoMoscow": $seconds = 10800; break;
		case "cdoEastAfrica": $seconds = 10800; break;
		case "cdoTehran": $seconds = 12600; break;
		case "cdoAbuDhabi": $seconds = 14400; break;
		case "cdoCaucasus": $seconds = 14400; break;
		case "cdoKabul": $seconds = 16200; break;
		case "cdoEkaterinburg": $seconds = 18000; break;
		case "cdoIslamabad": $seconds = 18000; break;
		case "cdoBombay": $seconds = 19800; break;
		case "cdoNepal": $seconds = 20700; break;
		case "cdoAlmaty": $seconds = 21600; break;
		case "cdoDhaka": $seconds = 21600; break;
		case "cdoSriLanka": $seconds = 21600; break;
		case "cdoRangoon": $seconds = 23400; break;
		case "cdoBangkok": $seconds = 25200; break;
		case "cdoKrasnoyarsk": $seconds = 25200; break;
		case "cdoBeijing": $seconds = 28800; break;
		case "cdoIrkutsk": $seconds = 28800; break;
		case "cdoSingapore": $seconds = 28800; break;
		case "cdoPerth": $seconds = 28800; break;
		case "cdoTaipei": $seconds = 28800; break;
		case "cdoTokyo": $seconds = 32400; break;
		case "cdoSeoul": $seconds = 32400; break;
		case "cdoYakutsk": $seconds = 32400; break;
		case "cdoAdelaide": $seconds = 34200; break;
		case "cdoDarwin": $seconds = 34200; break;
		case "cdoBrisbane": $seconds = 36000; break;
		case "cdoSydney": $seconds = 36000; break;
		case "cdoGuam": $seconds = 36000; break;
		case "cdoHobart": $seconds = 36000; break;
		case "cdoVladivostock": $seconds = 36000; break;
		case "cdoMagadan": $seconds = 39600; break;
		case "cdoWellington": $seconds = 43200; break;
		case "cdoFiji": $seconds = 43200; break;
		case "cdoTonga": $seconds = 46800; break;
		default: $seconds = date("Z"); $def = true;
	}
	return $seconds;
}

function GetExtention($ThisPart)
{
	$SubType = (isset($ThisPart->ifsubtype)) ? strtolower($ThisPart->subtype) : '';
	if ($ThisPart->type == 5) {
		if (strpos($SubType, 'gif') !== false) return '.gif';
		if (strpos($SubType, 'jpeg') !== false) return '.jpeg';
	}
	if (strpos($SubType, 'zip') !== false) return '.zip';
	if (strpos($SubType, 'excel') !== false) return '.xls';
	return '.att';
}

function GetMime($TypeId){
	switch ($TypeId){
		case 0: $Type = 'text'; break;
		case 1: $Type = 'multipart'; break;
		case 2: $Type = 'message'; break;
		case 3: $Type = 'application'; break;
		case 4: $Type = 'audio'; break;
		case 5: $Type = 'image'; break;
		case 6: $Type = 'video'; break;
		default: $Type = 'unknown';
	}
	return $Type;
}

function GetParameter($Parameters, $Name)
{
	$Parameter = '';
	while ($Obj = array_pop($Parameters))
		if (strtolower($Obj->attribute) == $Name){
			$Parameter = $Obj->value;
			break;
		}
	return $Parameter;
}

?>
