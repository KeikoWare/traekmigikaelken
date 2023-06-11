<?php
require './inc_functions.php';
$strIniDir = CorrectPath($strIniDir);
$ErrorsArray = Array();

if ((floor(phpversion()) >= 5) && (!extension_loaded('dom')))
	$ErrorsArray[] = 'The DOM module does not seem to be installed on this PHP setup.';

if ((floor(phpversion()) < 5) && (!extension_loaded('domxml'))) {
	$ErrorsArray[] = 'The DOM XML module does not seem to be installed on this PHP setup.';
} else {
	if (floor(phpversion()) < 5 && !function_exists('domxml_new_doc')) {
		$ErrorsArray[] = 'The DOM XML module does not seem to be installed on this PHP setup.';
	}
}

if (!extension_loaded('iconv')) {
	$ErrorsArray[] = 'The ICONV module does not seem to be installed on this PHP setup.';
	function iconv($in, $out, $str)
	{
		return $str;
	}
}

if (!extension_loaded('imap'))
	$ErrorsArray[] = 'The IMAP module does not seem to be installed on this PHP setup.';

if (!is_dir($strIniDir))
	$ErrorsArray[] = 'Path to the data folder is incorrect. WebMail cannot run. Check "settings_path.php" file.';

if (floor(phpversion()) < 4) {
	$ErrorsArray = Array();
	$ErrorsArray[] = 'Too old version of  PHP installed on your server. WebMail requires PHP 4.3.0 or higher.';
} else {
	$version = explode('.', phpversion());
	if ($version[0] == 4 && $version[1] < 3) {
		$ErrorsArray = Array();
		$ErrorsArray[] = 'Too old version of  PHP installed on your server. WebMail requires PHP 4.3.0 or higher.';
	}
}

$ErrorsCount = count($ErrorsArray);
if ($ErrorsCount > 1) {
	for ($i=($ErrorsCount-1); $i>=0; $i--) {
		$StartError .= 'Error #'.($ErrorsCount-$i).' '.$ErrorsArray[$i].'<br />';
	}
	$StartError .= '<br />Please make sure you\'ve followed all the instructions in WebMail documentation:<br /><br /><a href="help/installation_instructions_win.html">Installation Instructions for Windows</a><br /><a href="help/installation_instructions_unix.html">Installation Instructions for Unix</a>';
} elseif ($ErrorsCount == 1) {
	$StartError = $ErrorsArray[0].'<br />';
	$StartError .= '<br />Please make sure you\'ve followed all the instructions in WebMail documentation:<br /><br /><a href="help/installation_instructions_win.html">Installation Instructions for Windows</a><br /><a href="help/installation_instructions_unix.html">Installation Instructions for Unix</a>';
}
?>
