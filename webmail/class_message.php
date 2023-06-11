<?php
function GetStrReplacement($Str, &$Rep)
{
	static $Count = 0;
	$Rep[$Count] = stripslashes($Str);
	return "##string_replacement{".($Count++)."}##";
}

class Message
{
	var $Attachments = Array();
	var $AttachmentsCount;
	var $AttachmentsInfo = Array();
	var $Charset;
	var $Crlf = "\r\n";
	var $Date;
	var $FromAddr;
	var $FromFriendlyName;
	var $HasAttachments;
	var $HasHtmlBody;
	var $HasTextBody;
	var $HtmlBody;
	var $HtmlBodyCharset;
	var $Importance;
	var $LineLength = 76;
	var $MessageId;
	var $RawBCCAddr = '';
	var $RawCCAddr = '';
	var $RawDate;
	var $RawFromAddr;
	var $RawHtmlBody;
	var $RawResentFrom = '';
	var $RawSubject = '';
	var $RawTextBody;
	var $RawToAddr;
	var $Size;
	var $TextBody;
	var $TextBodyCharset;
	var $Subject;

	function GetFriendlyName($FromAddress)
	{
		$From = str_replace('"', '', trim(preg_replace("/<.+?>/", "", $FromAddress)));
		if ($From == '')
			$From = str_replace('<', '', str_replace('>', '', $FromAddress));
		return $From;
	}

	function DecodeHeader($Header)
	{
		$HeaderAr = imap_mime_header_decode($Header);
		$NewHeader = '';
		if ($this->HasTextBody) $BodyCharset = $this->TextBodyCharset;
		elseif ($this->HasHtmlBody) $BodyCharset = $this->HtmlBodyCharset;
		else $BodyCharset = '';
		for ($j = 0, $c = count($HeaderAr); $j<$c; $j++ ){
			if ($HeaderAr[$j]->charset != 'default') {
				$x = iconv($HeaderAr[$j]->charset, $this->Charset.'//IGNORE', $HeaderAr[$j]->text);
				if ($x) $HeaderAr[$j]->text = $x;
			} elseif ($BodyCharset != '' && $BodyCharset != 'us-ascii' && $BodyCharset != 'default') {
				$x = iconv($BodyCharset, $this->Charset.'//IGNORE', $HeaderAr[$j]->text);
				if ($x) $HeaderAr[$j]->text = $x;
			} else {
				$x = iconv('', $this->Charset.'//IGNORE', $HeaderAr[$j]->text);
				if ($x) $HeaderAr[$j]->text = $x;
			}
			$NewHeader .= $HeaderAr[$j]->text;
		}
		return $NewHeader;
	}

	function EncodeHeaderText($HeaderName, $HeaderText, $Charset, $Encoding)
	{
		$CharsetText = ($Encoding == 3) ? '=?'.$Charset.'?B?' : '=?'.$Charset.'?Q?';
		$IsAddr = ($HeaderName == 'From: ' || $HeaderName == 'To: ' || $HeaderName == 'CC: ' || $HeaderName == 'BCC: ') ? true : false;
		if ($IsAddr) {
			$HeaderArray = Array();
			$wasQuote = false;
			$firstLetter = 0;
			for($i = 0; $i < strlen($HeaderText); $i++) {
				$HeaderChar = substr($HeaderText, $i, 1);
				if (!$wasQuote && ($HeaderChar == ',' || $HeaderChar == ';')) {
					$HeaderArray[] = substr($HeaderText, $firstLetter, $i - $firstLetter);
					$firstLetter = $i+1;
				}
				if ($HeaderChar == '"')
					$wasQuote = $wasQuote ? false : true;
			}
			$HeaderArray[] = substr($HeaderText, $firstLetter, $i - $firstLetter);
		} else
			$HeaderArray = Array($HeaderText);
		$Result = $HeaderName;
		$Len = strlen($Result);
		$CharsetLen = strlen($CharsetText);
		$HeadersCount = count($HeaderArray);
		foreach ($HeaderArray as $Key => $Header) {
			if (preg_match("<.+@.+>", $Header, $a) > 0 || !$IsAddr)
			{
				if ($IsAddr) {
					$Pos = strpos($Header, '<');
					$Addr = trim(substr($Header, $Pos));
					if (substr($Addr, 0, 1) != '<')
						$Addr = '<'.$Addr;
					if ($p = strpos($Addr, '>')) {
						$Addr = substr($Addr, 0, $p + 1);
					} else {
						if (substr($Addr, strlen($Addr), 1) != '>')
							$Addr = $Addr.'>';
					}
					$Name = trim(substr($Header, 0, $Pos));
				} else {
					$Addr = '';
					$Name = trim($Header);
				}
				$NameArray = preg_split('//', $Name, -1, PREG_SPLIT_NO_EMPTY);
				$isRoman = true;
				foreach ($NameArray as $NameChar){
					if (ord($NameChar) > 127)
						$isRoman = false;
				}
				if ($Charset == '') $isRoman = true;
				while (strlen($Name)>0) {
					if ($isRoman){
						$TmpResult = substr($Name, 0, ($this->LineLength - $Len - 3));
						$Name = substr($Name, ($this->LineLength - $Len - 3));
						if (strlen($Name) == 0) {
							$Len = $Len + strlen($TmpResult);
							$Result .= $TmpResult;
						} else {
							$Len = 1;
							$Result .= $TmpResult.$this->Crlf.' ';
						}
					} else {
						$Result .= $CharsetText;
						$NameLen = floor(($this->LineLength - $Len - $CharsetLen - 3)/3);
						if ($Charset == 'utf-8'){
							$Offset = 6;
							$Kod = ord(substr($Name, $NameLen, 1)) >> $Offset;
							while ($Kod == 2){
								$NameLen--;
								$Kod = ord(substr($Name, $NameLen, 1)) >> $Offset;
							}
						}
						$TmpName = substr($Name, 0, $NameLen);
						$Name = substr($Name, $NameLen);
						if ($Encoding == 3)
							$TmpResult = imap_binary($TmpName).'?= ';
						else
							$TmpResult = str_replace('?', '=3F', imap_8bit($TmpName)).'?= ';
						if (strlen($Name) == 0) {
							$Len = $Len + $CharsetLen + strlen($TmpResult);
							$Result .= $TmpResult;
						} else {
							$Len = 1;
							$Result .= $TmpResult.$this->Crlf.' ';
						}
					}
				}
			} else {
				$Name = '';
				if (strlen(trim($Header)) > 0) {
					$Addr = trim($Header);
					if (substr($Addr, 0, 1) != '<')
						$Addr = '<'.$Addr;
					if (substr($Addr, strlen($Addr) - 1, 1) != '>')
						$Addr = $Addr.'>';
				} else {
					$Addr = trim($Header);
				}
			}
			if (($Len + strlen($Addr) + 1) < $this->LineLength) {
				$Result .= ' '.$Addr;
				$Len = $Len + 1 + strlen($Addr);
			} else {
				$Result .= $this->Crlf.' '.$Addr;
				$Len = strlen($Addr) + 1;
			}
			if ($HeadersCount > $Key + 1) {
				$Result .= ', ';
				$Len += 2;
			}
		}
		return trim($Result);
	}

	function GetArrayAddr($FullAddr)
	{
		$FullAddr = ' '.$FullAddr.' ';
		preg_match_all('|[ <]([\.a-zA-z\d_+-]+@[\.a-zA-z\d_-]+)[,; >]|', $FullAddr, $Result);
		$Key = count($Result) - 1;
		if ($Key > -1) return $Result[$Key];
		else return Array();
	}

	function EncodeBodyText($BodyText, $Charset, $Encoding, $Subtype)
	{
		$BodyArray = preg_split('//', $BodyText, -1, PREG_SPLIT_NO_EMPTY);
		$isRoman = true;
		foreach ($BodyArray as $BodyChar){
			if (ord($BodyChar) > 127)
				$isRoman = false;
		}
		if ($isRoman || $Charset == '') {
			$Result = 'Content-Type: text/'.$Subtype.$this->Crlf.$this->Crlf;
			$Result .= $BodyText.$this->Crlf;
		} else {
			$Result = 'Content-Type: text/'.$Subtype.'; charset='.$Charset.$this->Crlf;
			if ($Encoding == 3){
				$Result .= 'Content-Transfer-Encoding: base64'.$this->Crlf.$this->Crlf;
				$Result .= imap_binary($BodyText).$this->Crlf;
			} else {
				$Result .= 'Content-Transfer-Encoding: quoted-printable'.$this->Crlf.$this->Crlf;
				$Result .= imap_8bit($BodyText).$this->Crlf;
			}
		}
		return $Result;
	}

	function AddAttachment($FilePath, $ContentType, $FileName)
	{
		if (is_file($FilePath)) {
			$NewAttachment = '';
			if (!empty($ContentType)) {
				$NewAttachment .= 'Content-Type: '.$ContentType;
				if (!empty($FileName))
					$NewAttachment .= ';'.$this->Crlf."\t".$FileName;
			}
			$NewAttachment .= $this->Crlf.'Content-Disposition: attachment'.$this->Crlf;
			$NewAttachment .= 'Content-Transfer-Encoding: base64'.$this->Crlf.$this->Crlf;
			$FileHandle = @fopen($FilePath, "r");
			if ($FileHandle) {
				$filesize = @filesize($FilePath);
				if ($filesize > 0) {
					$AttachmentBody = fread($FileHandle, $filesize);
					fclose($FileHandle);
				} else {
					$AttachmentBody = '';
				}
				@unlink($FilePath);
				$NewAttachment .= imap_binary($AttachmentBody).$this->Crlf;
				$this->Attachments[] = $NewAttachment;
				return true;
			} else 
				return false;
		}
	}

	function AddAttachmentFromPop3($AttachmentBody, $ContentType, $FileName, $AttachCid)
	{
		$NewAttachment = '';
		if (!empty($ContentType)) {
			$NewAttachment .= 'Content-Type: '.$ContentType;
			if (!empty($FileName))
				$NewAttachment .= ';'.$this->Crlf."\tname=\"".$FileName.'"';
		}
		if ($AttachCid == '')
			$NewAttachment .= $this->Crlf.'Content-Disposition: attachment'.$this->Crlf;
		else {
			$NewAttachment .= $this->Crlf.'Content-Disposition: inline'.$this->Crlf;
			$NewAttachment .= 'Content-ID: '.$AttachCid.$this->Crlf;
		}
		$NewAttachment .= 'Content-Transfer-Encoding: base64'.$this->Crlf.$this->Crlf;
		$NewAttachment .= imap_binary($AttachmentBody).$this->Crlf;
		$this->Attachments[] = $NewAttachment;
	}

	function GetBodies()
	{
		$Bodies = '';
		if ($this->HasHtmlBody && $this->HasTextBody) {
			$BodyBoundary = '--=_NextBodyPart_'.md5(uniqid(time()));
			$Bodies .= 'Content-Type: multipart/alternative;' . $this->Crlf . "\tboundary=\"$BodyBoundary\"".$this->Crlf.$this->Crlf;
			$Bodies .= '--'.$BodyBoundary.$this->Crlf;
			$Bodies .= $this->RawTextBody.$this->Crlf;
			$Bodies .= '--'.$BodyBoundary.$this->Crlf;
			$Bodies .= $this->RawHtmlBody.$this->Crlf;
			$Bodies .= '--'.$BodyBoundary.'--'.$this->Crlf;
		} elseif ($this->HasHtmlBody)
			$Bodies .= $this->RawHtmlBody.$this->Crlf;
		else
			$Bodies .= $this->RawTextBody.$this->Crlf;
		return $Bodies;
	}

	function GetMessageText()
	{
		$Message = '';
		$Offset = date("Z");
		$Offset = (int) $Offset/36;
		if ($Offset>=0)
			$Sign = '+';
		else
			$Sign = '-';
		if (strlen(abs($Offset)) == 1)
			$Add = '000';
		elseif (strlen(abs($Offset)) == 2)
			$Add = '00';
		elseif (strlen(abs($Offset)) == 3)
			$Add = '0';
		else
			$Add = '';
		$Offset = $Sign.$Add.abs($Offset);
		if (!empty($this->RawResentFrom)) {
			$Message .= $this->RawResentFrom.$this->Crlf;
			$Message .= 'Resent-date: '.date("D, j M Y H:i:s ").$Offset.$this->Crlf;
			$Message .= 'Date: '.$this->RawDate.$this->Crlf;
		} else {
			$Message .= 'Date: '.date("D, j M Y H:i:s ").$Offset.$this->Crlf;
		}
		$Message .= 'X-Priority: 3 (Normal)'.$this->Crlf;
		$Message .= 'MIME-Version: 1.0'.$this->Crlf;
		$Message .= 'X-Mailer: MailBee WebMail Lite 4'.$this->Crlf;
		$Message .= 'Message-ID: <'.session_id().'.'.md5(time()).'@'.$_SERVER['SERVER_NAME'].'>'.$this->Crlf;
		$Message .= $this->RawFromAddr.$this->Crlf;
		$Message .= $this->RawToAddr.$this->Crlf;
		if (!empty($this->RawCCAddr))
			$Message .= $this->RawCCAddr.$this->Crlf;
		if (!empty($this->RawBCCAddr))
			$Message .= $this->RawBCCAddr.$this->Crlf;
		if (!empty($this->RawSubject))
			$Message .= $this->RawSubject.$this->Crlf;
		if (count($this->Attachments) > 0) {
			$Boundary = '--=_NextPart_'.md5(uniqid(time()));
			$Message .= 'Content-Type: multipart/mixed;' . $this->Crlf . "\tboundary=\"$Boundary\"".$this->Crlf.$this->Crlf;
			$Message .= '--'.$Boundary.$this->Crlf;
			$Message .= $this->GetBodies();
			foreach ($this->Attachments as $Attachment) {
				$Message .= '--'.$Boundary.$this->Crlf;
				$Message .= $Attachment;
			}
			$Message .= '--'.$Boundary.'--'.$this->Crlf;
		} else
			$Message .= $this->GetBodies();
		$Message = str_replace($this->Crlf.'.', $this->Crlf.'..', $Message);
		$Message = $Message.'.';
		return $Message;
	}

	function GetCensoredHtmlBody()
	{
		$Body = $this->HtmlBody;
		$ToRemoveArray = array (
			"'<html[^>]*>'si",
			"'</html>'si",
			"'<body[^>]*>'si",
			"'</body>'si",
			"'<base[^>]*>'si",
			"'<title[^>]*>.*?</title>'si",
			"'<style[^>]*>.*?</style>'si",
			"'<script[^>]*>.*?</script>'si",
			"'<object[^>]*>.*?</object>'si",
			"'<embed[^>]*>.*?</embed>'si",
			"'<applet[^>]*>.*?</applet>'si",
			"'<mocha[^>]*>.*?</mocha>'si",
			"'<meta[^>]*>'si",
		);
		$Body = preg_replace($ToRemoveArray, '', $Body);
		$Body = preg_replace("|href=\"(.*)script:|i", 'href="php_mail_removed_script:', $Body);
		$Body = preg_replace("|<([^>]*)java|i", '<php_mail_removed_java_tag', $Body);
		$Body = preg_replace("|<([^>]*)&{.*}([^>]*)>|i", "<&{;}\\3>", $Body);
		$Body = preg_replace("/\x0D\x0A\t+/", "\x0D\x0A", $Body);
		return $Body;
	}

	function GetCensoredTextBody()
	{
		$Body = $this->TextBody;
		$Body = str_replace("\n", "\r\n", $Body);
		$ReplaceStrings = array();
		$Pattern = "/(http|https|ftp|telnet|gopher|news|file|wais):\/\/([a-zA-Z0-9+-=%&@:_\.~?]+[#a-zA-Z0-9+]*)/ie";
		$Replace = "GetStrReplacement('<a href=\"\\1://\\2\" target=\"_blank\">\\1://\\2</a>', \$ReplaceStrings)";
		$Body = preg_replace($Pattern, $Replace, $Body);
		$Body = htmlspecialchars($Body);
		$Body = preg_replace("/([0-9a-zA-Z]([-+_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,})/", "<a href=\"mailto:\\1\" target=\"_blank\">\\1</a>", $Body); 
		for($i=0, $c=count($ReplaceStrings); $i<$c; $i++)
			$Body = str_replace('##string_replacement{'.$i.'}##', $ReplaceStrings[$i], $Body);
		$BodyArray = explode("\r\n", $Body);
		$Body = '';
		foreach ($BodyArray as $BodyPart)
		{
			if (ereg("^&gt;", $BodyPart))
				$BodyPart = "<font class=\"wm_message_body_quotation\">".$BodyPart."</font>";
			$Body .= $BodyPart.'<br/>';
		}
		$Body = str_replace("  ", "&nbsp;&nbsp;", $Body);
		return $Body;
	}

	function GetPlainFromHtml()
	{
		$Body = $this->HtmlBody;
		$ToRemoveArray = array (
			"'<style[^>]*>.*?</style>'si",
			"'<script[^>]*>.*?</script>'si"
		);
		$Body = preg_replace($ToRemoveArray, '', $Body);
		$Body = trim(eregi_replace("<[^>]*>","",$Body));
		$Body = trim(eregi_replace("<[^>]*>","",$Body));
		$Body = preg_replace("/(\x0D\x0A)+/","\x0D\x0A",$Body);
		$Body = str_replace('&ndash;', '_', $Body);
		$Body = str_replace('&ldquo;', '"', $Body);
		$Body = str_replace('&rdquo;', '"', $Body);
		$Body = str_replace('&nbsp;', ' ', $Body);
		$Body = str_replace('&quot;', '"', $Body);
		$Body = str_replace('&lt;', '<', $Body);
		$Body = str_replace('&gt;', '>', $Body);
		$Body = str_replace('&amp;', '&', $Body);
		if (function_exists(html_entity_decode)) $Body = html_entity_decode($Body);
		return $Body;
	}

	function GetReplyAddr($Email)
	{
		$Addresses = $this->FromAddr.','.$this->ToAddr.','.$this->CCAddr;
		$Addresses = str_replace(';', ',', $Addresses);
		$AddrArray = explode(',', $Addresses);
		$CheckedAddrArray = Array();
		for ($i=0, $c=count($AddrArray); $i<$c; $i++) {
			$PureAddr = $this->GetArrayAddr($AddrArray[$i]);
			if (!empty($PureAddr)) {
				$PureAddr = trim($PureAddr[0]);
				$flag = false;
				if ($PureAddr == $Email) $flag = true;
				foreach ($CheckedAddrArray as $CheckedAddr)
					if ($PureAddr == $CheckedAddr['pure']) $flag = true;
				if ($flag == false) {
					$tmp = Array(
						'pure' => $PureAddr,
						'full' => $AddrArray[$i]
					);
					$CheckedAddrArray[] = $tmp;
				}
			}
		}
		$ResultAddr = '';
		foreach ($CheckedAddrArray as $CheckedAddr) {
			if ($ResultAddr != '')
				$ResultAddr .= ', ';
			$ResultAddr .= $CheckedAddr['full'];
		}
		return $ResultAddr;
	}
}
?>