<?php
function disable_magic_quotes_gpc()
{
	if (@get_magic_quotes_gpc() == 1) {
		$_REQUEST = array_map ('stripslashes' , $_REQUEST);
	}
}

function CorrectPath($Path)
{
	$NewPath = str_replace('\\', '/', $Path);
	$PathArray = explode('/', $NewPath);
	$NewPath = '';
	foreach ($PathArray as $PathPart) {
		if (!empty($PathPart)) {
			if (!empty($NewPath)) $NewPath .= '/';
			$NewPath .= $PathPart;
		}
	}
	if (substr($Path, 0, 1) == '/' || substr($Path, 0, 1) == '\\') $NewPath = '/'.$NewPath;
	return $NewPath;
}

function isImage($strFileName)
{
	$strFileExt = preg_replace("/.*\.(\w{2,4})/", "$1", $strFileName);
	switch (strtolower($strFileExt)) {
		case 'jpeg':
		case 'jpg':
		case 'gif':
		case 'bmp':
			return true;
			break;
		default:
			return false;
			break;
	}
}

/*encode to html-entities for correct display $from and $subject*/
function EncodeHTML($Src)
{
	$Src = str_replace('&', '&amp;', $Src);
	$Src = str_replace('<', '&lt;', $Src);
	$Src = str_replace('>', '&gt;', $Src);
	return $Src;
}

function EncodeHTMLquotes($Src)
{
	$Src = str_replace('"', '&quot;', $Src);
	return $Src;
}

function GetIconNameByExtension($str_extension)
{
	switch (strtolower($str_extension))
	{
		case 'txt': return 'text_plain.gif'; break;
		case 'bat': return 'executable.gif'; break;
		case 'exe': return 'executable.gif'; break;
		case 'com': return 'executable.gif'; break;
		case 'asp': return 'application_asp.gif'; break;
		case 'asa': return 'application_asp.gif'; break;
		case 'inc': return 'application_asp.gif'; break;
		case 'css': return 'application_css.gif'; break;
		case 'doc': return 'application_doc.gif'; break;
		case 'html': return 'application_html.gif'; break;
		case 'shtml': return 'application_html.gif'; break;
		case 'phtml': return 'application_html.gif'; break;
		case 'htm': return 'application_html.gif'; break;
		case 'pdf': return 'application_pdf.gif'; break;
		case 'xls': return 'application_xls.gif'; break;
		case 'bmp': return 'image_bmp.gif'; break;
		case 'gif': return 'image_gif.gif'; break;
		case 'jpg': return 'image_jpeg.gif'; break;
		case 'jpeg': return 'image_jpeg.gif'; break;
		case 'psd': return 'image_psd.gif'; break;
		case 'tiff': return 'image_tiff.gif'; break;
		case 'tif': return 'image_tiff.gif'; break;
		default: return 'attach.gif'; break;
	}
}

?>