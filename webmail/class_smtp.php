<?php
class SMTP
{
	var $AuthMethod;
	var $ClientRequest;
	var $Connected;
	var $ConnectionHandle;
	var $EnableLogging;
	var $ErrorDesc;
	var $ErrorDescLog;
	var $From;
	var $IsError;
	var $LogFilePath;
	var $Message;
	var $Password;
	var $PortNumber;
	var $ServerName;
	var $ServerResponse;
	var $UserName;
	var $ToArray;

	function CheckErrors()
	{
		$Line = fgets($this->ConnectionHandle, 1024);
		$Line = str_replace("\n", '', str_replace("\r\n", '', $Line));
		$this->ServerResponse = date('H:i:s ').'[Rcvd: '.$Line.']';
		if (substr($Line, 0, 1) != '2' && substr($Line, 0, 1) != '3') {
			$this->IsError = true;
			$this->ErrorDesc = 'Rcvd: '.$Line;
			$this->ErrorDescLog = date('H:i:s ').'['.$this->ErrorDesc.']';
		}
		while (substr($Line,3,1) == '-') {
			$Line = fgets($this->ConnectionHandle, 1024);
			$Line = str_replace("\n", '', str_replace("\r\n", '', $Line));
			$this->ServerResponse = date('H:i:s ').'[Rcvd: '.$Line.']';
			if (substr($Line, 0, 1) != '2' && substr($Line, 0, 1) != '3') {
				$this->IsError = true;
				$this->ErrorDesc = 'Rcvd: '.$Line;
				$this->ErrorDescLog = date('H:i:s ').'['.$this->ErrorDesc.']';
			}
		}
		if ($this->EnableLogging) $this->LogToFile();
	}

	function Connect()
	{
		$this->Connected = false;
		$this->IsError = false;
		$this->ClientRequest = date('H:i:s ').'[Connecting to server '.$this->ServerName.' on port '.$this->PortNumber.']';
		$this->ConnectionHandle = @fsockopen($this->ServerName, $this->PortNumber, $errno, $errstr);;
		if(!$this->ConnectionHandle)
		{
			$this->IsError = true;
			$this->ErrorDesc = date('H:i:s ').'[Error: '.$errstr.']';
		} else {
			$this->Connected = true;
			$this->CheckErrors();
		}
	}

	function ExecuteCommand($Command)
	{
		fputs($this->ConnectionHandle, $Command."\r\n");
		$this->CheckErrors();
	}

	function Disconnect()
	{
		$this->IsError = false;
		$this->ClientRequest = date('H:i:s ').'[Send: QUIT]';
		$this->ExecuteCommand('QUIT');
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

	function Send()
	{
		$this->ClientRequest = date('H:i:s ').'[Send: EHLO '. $this->ServerName.']';
		$this->ExecuteCommand('EHLO '. $this->ServerName);
		if ($this->IsError) {
			$this->IsError = false;
			$this->ClientRequest = date('H:i:s ').'[Send: RSET '. $this->ServerName.']';
			$this->ExecuteCommand('RSET '. $this->ServerName);
			if (!$this->IsError) {
				$this->ClientRequest = date('H:i:s ').'[Send: HELO '. $this->ServerName.']';
				$this->ExecuteCommand('HELO '. $this->ServerName);
			}
		}
		if (!$this->IsError && $this->AuthMethod == 1) {
			$this->ClientRequest = date('H:i:s ').'[Send: AUTH LOGIN]';
			$this->ExecuteCommand('AUTH LOGIN');
			if (!$this->IsError) {
				$this->ClientRequest = date('H:i:s ').'[Sending encoded login]';
				$this->ExecuteCommand(base64_encode($this->UserName));
			}
			if (!$this->IsError) {
				$this->ClientRequest = date('H:i:s ').'[Sending encoded password]';
				$this->ExecuteCommand(base64_encode($this->Password));
			}
		}
		if (!$this->IsError) {
			$this->ClientRequest = date('H:i:s ').'[Send: MAIL FROM:<'. $this->From.'>]';
			$this->ExecuteCommand('MAIL FROM:<'. $this->From.'>');
		}
		if (!$this->IsError) {
			foreach ($this->ToArray as $Recipient)
				if($Recipient != '' || $Recipient != '<>') {
					$this->ClientRequest = date('H:i:s ').'[Send: RCPT TO:<'. $Recipient.'>]';
					$this->ExecuteCommand('RCPT TO:<'. $Recipient.'>');
					if ($this->IsError)
						break;
				}
		}
		if (!$this->IsError) {
			$this->ClientRequest = date('H:i:s ').'[Send: DATA]';
			$this->ExecuteCommand('DATA');
		}
		if (!$this->IsError) {
			$this->ClientRequest = date('H:i:s ')."[Data sending]";
			$this->ExecuteCommand($this->Message);
		}
	}
}
?>