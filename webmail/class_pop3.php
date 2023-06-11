<?php
require './inc_functions_pop3.php';

class POP3
{
	var $ClientRequest;
	var $Connected;
	var $ConnectionHandle;
	var $EnableLogging;
	var $ErrorDesc;
	var $ErrorDescLog;
	var $ImapFlags;
	var $IsError;
	var $LogFilePath;
	var $MessageCount;
	var $Password;
	var $PortNumber;
	var $ServerName;
	var $ServerResponse;
	var $TimeOffset;
	var $UserName;

	function Connect()
	{
		$this->Connected = false;
		$this->IsError = false;
		$this->ClientRequest = date('H:i:s ').'[Connecting to server '.$this->ServerName.' on port '.$this->PortNumber.$this->ImapFlags.' (login '.$this->UserName.')]';
		$this->ConnectionHandle = @imap_open('{'.$this->ServerName.':'.$this->PortNumber.$this->ImapFlags.'/notls}INBOX', $this->UserName, $this->Password, 0);
		if(!$this->ConnectionHandle)
		{
			$this->IsError = true;
			$error = imap_last_error();
			if ($error == 'Too many login failures' || $error == '' || eregi('too many failed logins', $error)) {
				$this->ErrorDesc = 'Error: Wrong Email, Login and or Password. Authentication failed';
				$this->ErrorDescLog = date('H:i:s ').'['.$this->ErrorDesc.']';
			} else {
				$this->ErrorDesc = 'Error: '.$error;
				$this->ErrorDescLog = date('H:i:s ').'['.$this->ErrorDesc.']';
			}
		} else {
			$this->Connected = true;
			$this->ServerResponse = date('H:i:s ').'[Connected to the server]';
			if ($this->EnableLogging) $this->LogToFile();
			$this->ClientRequest = date('H:i:s ').'[Getting number of messages in mailbox]';
			$this->MessageCount = imap_num_msg($this->ConnectionHandle);
			$this->ServerResponse = date('H:i:s ').'[Got number of messages]';
		}
		if ($this->EnableLogging) $this->LogToFile();
	}

	function Disconnect()
	{
		$this->ClientRequest = date('H:i:s ').'[Disconnecting from the server]';
		if (!@imap_close($this->ConnectionHandle, CL_EXPUNGE))
		{
			$this->IsError = true;
			$this->ErrorDesc = 'Error: '.imap_last_error();
			$this->ErrorDescLog = date('H:i:s ').'['.$this->ErrorDesc.']';
		} else {
			$this->Connected = false;
			$this->ServerResponse = date('H:i:s ').'[Connection closed]';
		}
		if ($this->EnableLogging) $this->LogToFile();
	}

	function LogToFile()
	{
		if (is_file($this->LogFilePath)) {
			@error_log($this->ClientRequest."\r\n", 3, $this->LogFilePath);
			if ($this->IsError)
				@error_log($this->ErrorDescLog."\r\n", 3, $this->LogFilePath);
			else
				@error_log($this->ServerResponse."\r\n", 3, $this->LogFilePath);
		}
	}

	function RetrieveHeaders($FirstMsg, $LastMsg)
	{
		$Messages = Array();
		if ($FirstMsg == $LastMsg) $MsgNum = $FirstMsg;
		else $MsgNum = $FirstMsg.':'.$LastMsg;
		$this->ClientRequest = date('H:i:s ').'[Getting overview of messages: messages numbers '.$MsgNum.']';
		$OverView = imap_fetch_overview($this->ConnectionHandle, $MsgNum);
		$this->ServerResponse = date('H:i:s ').'[Got overview of messages]';
		if ($this->EnableLogging) $this->LogToFile();
		for ($i = $LastMsg; $i >= $FirstMsg; $i--)
		{
			$Message = new Message();

			$HeadView = $OverView[$i - $FirstMsg];
			$Message->MessageId = $HeadView->msgno;
			if (isset($HeadView->from))
				$Message->RawFromAddr = $HeadView->from;
			else $Message->RawFromAddr = '';
			if (isset($HeadView->subject))
				$Message->RawSubject = $HeadView->subject;
			else $Message->RawSubject = '';
			$Message->Size = $HeadView->size;
			$Message->Date = GetCorrectDate($HeadView->date, $this->TimeOffset);

			$this->ClientRequest = date('H:i:s ').'[Sending fetchheader command: message number '.$i.']';
			$Header = imap_fetchheader($this->ConnectionHandle, $i);
			$this->ServerResponse = date('H:i:s ').'[Message header received]';
			if ($this->EnableLogging) $this->LogToFile();
			$Headers = ParseHeader($Header);
			$Message->Importance = $Headers['importance'];
			$Message->HasTextBody = true;
			$Message->TextBodyCharset = $Headers['charset'];
			if ($Headers['type'] == 'multipart' && $Headers['subtype'] != 'alternative')
				$Message->HasAttachments = 1;
			else $Message->HasAttachments = 0;
			$Messages[] = $Message;
		}
		return $Messages;
	}

	function DeleteMessages($Indexes)
	{
		$IndexesArray = explode(', ', $Indexes);
		for ($i=0, $count=count($IndexesArray); $i<$count; $i++)
		{
			$this->ClientRequest = date('H:i:s ').'[Marking message as deleted: message number '.$IndexesArray[$i].']';
			$x = imap_delete($this->ConnectionHandle, $IndexesArray[$i], 0);
			$this->ServerResponse = date('H:i:s ').'[Message marked]';
			if ($this->EnableLogging) $this->LogToFile();
		}
		$this->ClientRequest = date('H:i:s ').'[Expunging messages]';
		$x = imap_expunge($this->ConnectionHandle);
		$this->ServerResponse = date('H:i:s ').'[Messages expunged]';
		if ($this->EnableLogging) $this->LogToFile();
		$this->MessageCount = imap_num_msg($this->ConnectionHandle);
	}

	function GetPart(&$AttachTab, &$Num, &$ThisPart, $PartNum) {
		$AttachmentName = "unknown";
		if ($ThisPart->ifdescription) $AttachmentName = $ThisPart->description;
		if ($ThisPart->ifparameters) $AttachmentName = GetParameter($ThisPart->parameters, 'name');
		if (empty($AttachmentName) || eregi("unknown", $AttachmentName))
			$AttachmentName = $Num.GetExtention($ThisPart);
		if ($ThisPart->type == 1)
			foreach ($ThisPart->parts as $key => $CurPart) {
				if ($PartNum != NULL) $NextNum = $PartNum . '.';
				else $NextNum = '';
				$this->GetPart($AttachTab, $Num, $CurPart, $NextNum . ($key + 1));
			}
		else {
			$tmp = Array(
				'id' => ($PartNum != NULL ? $PartNum : 1),
				'name' => $AttachmentName,
				'size' => isset($ThisPart->bytes) ? $ThisPart->bytes : 0,
				'charset' => $ThisPart->ifparameters ? strtolower(GetParameter($ThisPart->parameters, 'charset')) : 'us-ascii',
				'encoding' => $ThisPart->encoding,
				'type' => $ThisPart->type,
				'subtype' => strtolower($ThisPart->subtype),
				'cid' => ($ThisPart->ifid) ? $ThisPart->id : '',
				'disposition' => ($ThisPart->ifdisposition) ? strtolower($ThisPart->disposition) : ''
			);
			if ($tmp['cid'] != '' || $tmp['disposition'] == 'attachment') $Num++;
			$AttachTab[] = $tmp;
		}
	}

	function GetMessage($MessageId, $Charset)
	{
		$Message = new Message();
		$Message->MessageId = $MessageId;
		$this->ClientRequest = date('H:i:s ').'[Sending fetchheader command: message number '.$MessageId.']';
		$Message->Headers = imap_fetchheader($this->ConnectionHandle, $MessageId);
		$x = iconv('', $Charset, $Message->Headers);
		if ($x)
			$Message->Headers = $x;
		$this->ServerResponse = date('H:i:s ').'[Message header received]';
		if ($this->EnableLogging) $this->LogToFile();
		$Headers = ParseHeader($Message->Headers);
		$Message->Importance = $Headers['importance'];
		$Message->HtmlBodyCharset = $Headers['charset'];
		$Message->TextBodyCharset = $Headers['charset'];
		$this->ClientRequest = date('H:i:s ').'[Sending fetchstructure command: message number '.$MessageId.']';
		$MsgStructure = imap_fetchstructure($this->ConnectionHandle, $MessageId);
		if (!is_object($MsgStructure)) {
			$this->IsError = true;
			$this->ErrorDesc = 'imap_fetchstructure did not return an object: '.imap_last_error();
			$this->ErrorDescLog = date('H:i:s ').'['.$this->ErrorDesc.']';
			if ($this->EnableLogging) $this->LogToFile();
			return;
		} else
			$this->ServerResponse = date('H:i:s ').'[Message structure received]';
		if ($this->EnableLogging) $this->LogToFile();
		$Message->HasHtmlBody = false;
		$Message->HtmlBody = '';
		$Message->HasTextBody = false;
		$Message->TextBody = '';
		if ($MsgStructure->type == 3 || (isset($MsgStructure->parts) && (sizeof($MsgStructure->parts) > 0))){
			$MessageParts = Array();
			$Num = 1;
			$this->GetPart($MessageParts, $Num, $MsgStructure, NULL);
			if (!empty($MessageParts)) {
				foreach ($MessageParts as $Part)
					if ($Part['cid'] == '' && $Part['disposition'] != 'attachment' && $Part['type'] == 0) {
						$this->ClientRequest = date('H:i:s ').'[Getting part of message: message number '.$MessageId.', part number 1]';
						$Body = imap_fetchbody($this->ConnectionHandle, $MessageId, $Part['id']);
						$this->ServerResponse = date('H:i:s ').'[Got part of message]';
						if ($this->EnableLogging) $this->LogToFile();
						if ($Headers['mime-version'] == 0) {
							if ($Headers['content-transfer-encoding'] == 'quoted-printable') $Body = imap_qprint($Body);
							if ($Headers['content-transfer-encoding'] == 'base64') $Body = imap_base64($Body);
						} else {
							if ($Part['encoding'] == 4) $Body = imap_qprint($Body);
							if ($Part['encoding'] == 3) $Body = imap_base64($Body);
						}
						if ($Part['subtype'] == 'html') {
							$Message->HasHtmlBody = true;
							if ($Headers['mime-version'] != 0)
								$Message->HtmlBodyCharset = $Part['charset'];
							if ($Message->HtmlBodyCharset != 'us-ascii') {
								$x = iconv($Message->HtmlBodyCharset, $Charset, $Body);
								if ($x)
									$Message->HtmlBody .= $x;
							} else {
								$x = iconv('', $Charset, $Body);
								if ($x) {
									$Message->HtmlBody .= $x;
								} else {
									$Message->HtmlBody .= $Body;
								}
							}
						} else {
							$Message->HasTextBody = true;
							if ($Headers['mime-version'] != 0)
								$Message->TextBodyCharset = $Part['charset'];
							if ($Message->TextBodyCharset != 'us-ascii') {
								$x = iconv($Message->TextBodyCharset, $Charset, $Body);
								if ($x)
									$Message->TextBody .= $x;
							} else {
								$x = iconv('', $Charset, $Body);
								if ($x) {
									$Message->TextBody .= $x;
								} else {
									$Message->TextBody .= $Body;
								}
							}
						}
					} else {
						$AttachInfo = Array (
							'id' => $Part['id'],
							'name' => $Part['name'],
							'size' => $Part['size'],
							'encoding' => $Part['encoding'],
							'type' => GetMime($Part['type']),
							'subtype' => $Part['subtype'],
							'cid' => $Part['cid']
						);
						$Message->AttachmentsInfo[] = $AttachInfo;
					}
				$Message->AttachmentsCount = count($Message->AttachmentsInfo);
			} else {
				$Message->HasTextBody = true;
				$Message->AttachmentsCount = 0;
			}
		} else {
			$Message->AttachmentsCount = 0;
			$this->ClientRequest = date('H:i:s ').'[Getting part of message: message number '.$MessageId.', part number 1]';
			$Body = imap_fetchbody($this->ConnectionHandle, $MessageId, 1);
			$this->ServerResponse = date('H:i:s ').'[Got part of message]';
			if ($this->EnableLogging) $this->LogToFile();
			$BodyCharset = $MsgStructure->ifparameters ? strtolower(GetParameter($MsgStructure->parameters, 'charset')) : 'us-ascii';
			if ($Headers['mime-version'] == 0) {
				if ($Headers['content-transfer-encoding'] == 'quoted-printable') $Body = imap_qprint($Body);
				if ($Headers['content-transfer-encoding'] == 'base64') $Body = imap_base64($Body);
				$BodyCharset = $Headers['charset'];
			} else {
				if ($MsgStructure->encoding == 4) $Body = imap_qprint($Body);
				if ($MsgStructure->encoding == 3) $Body = imap_base64($Body);
			}
			if ($BodyCharset != 'us-ascii') {
				$x = iconv($BodyCharset, $Charset, $Body);
				if ($x)
					$Body = $x;
			}
			if ($Headers['subtype'] == 'html') {
				$Message->HasHtmlBody = true;
				$Message->HtmlBody = $Body;
				$Message->HtmlBodyCharset = $BodyCharset;
			} else {
				$Message->HasTextBody = true;
				$Message->TextBody = $Body;
				$Message->TextBodyCharset = $BodyCharset;
			}
		}
		$this->ClientRequest = date('H:i:s ').'[Sending header command: message number '.$MessageId.']';
		$HeaderInfo = imap_headerinfo($this->ConnectionHandle, $MessageId);
		if (!is_object($HeaderInfo)) {
			$this->IsError = true;
			$this->ErrorDesc = 'Could not get header info: '.imap_last_error();
			$this->ErrorDescLog = date('H:i:s ').'['.$this->ErrorDesc.']';
			if ($this->EnableLogging) $this->LogToFile();
			return;
		} else
			$this->ServerResponse = date('H:i:s ').'[Message header info received]';
		if ($this->EnableLogging) $this->LogToFile();
		if (isset($HeaderInfo->fromaddress))
			$Message->RawFromAddr = $HeaderInfo->fromaddress;
		else $Message->RawFromAddr = '';
		if (isset($HeaderInfo->toaddress))
			$Message->RawToAddr = $HeaderInfo->toaddress;
		else $Message->RawToAddr = '';
		if (isset($HeaderInfo->ccaddress))
			$Message->RawCCAddr = $HeaderInfo->ccaddress;
		else $Message->RawCCAddr = '';
		if (isset($HeaderInfo->subject))
			$Message->RawSubject = $HeaderInfo->subject;
		else $Message->RawSubject = '';
		$Message->RawDate = $HeaderInfo->date;
		$Message->Date = GetCorrectDate($HeaderInfo->date, $this->TimeOffset);
		return $Message;
	}

	function RecordTempAttachment($TempDir, $MessageId, $PartId, $Encoding)
	{
		$TempFilePath = tempnam($TempDir, 'tmp');
		$TempFileName = basename($TempFilePath);
		$FileHandle = @fopen($TempDir.'/'.$TempFileName, 'r');
		if ($FileHandle) {
			fclose ($FileHandle);
			$FileHandle = @fopen($TempDir.'/'.$TempFileName, 'w');
		}
		if ($FileHandle) {
			$this->ClientRequest = date('H:i:s ').'[Getting part of message: message number '.$MessageId.', part number 1]';
			$AttachBody = imap_fetchbody($this->ConnectionHandle, $MessageId, $PartId);
			$this->ServerResponse = date('H:i:s ').'[Got part of message]';
			if ($this->EnableLogging) $this->LogToFile();
			if ($Encoding == 3) $AttachBody = imap_base64($AttachBody);
			elseif ($Encoding == 4) $AttachBody = imap_qprint($AttachBody);
			fwrite($FileHandle, $AttachBody);
			fclose($FileHandle);
			return $TempFileName;
		} else
			return false;
	}
}
?>