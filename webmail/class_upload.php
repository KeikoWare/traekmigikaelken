<?php
class AttachmentUpload
{
	var $SizeLimit;
	var $TempDir;
	var $IsError;
	var $ErrorDesc;

	function Upload()
	{
		include './language.php';
		global $Charset;
		$this->IsError = false;
		if (!isset($_FILES['fileAttach']))
		{
			$this->IsError = true;
			$this->ErrorDesc = iconv($InCharset, $Charset, $strFailedUpload);
		} else {
			if ($_FILES['fileAttach']['size'] > $this->SizeLimit)
			{
				$this->IsError = true;
				$this->ErrorDesc = iconv($InCharset, $Charset, $strTooBigFile);
			} else {
				$IsUploaded = false;
				if (!empty($_SESSION['attachments']))
					foreach ($_SESSION['attachments'] as $Attachment)
						if ($Attachment['size'] == $_FILES['fileAttach']['size'] &&
						$Attachment['name'] == $_FILES['fileAttach']['name'])
							$IsUploaded = true;
				if (!$IsUploaded)
				{
					$FilePath = tempnam($this->TempDir, 'tmp');
					$FileName = basename($FilePath);
					if (!@move_uploaded_file($_FILES['fileAttach']['tmp_name'], $this->TempDir.'/'.$FileName))
					{
						$this->IsError = true;
						switch ($_FILES['fileAttach']['error']) {
							case 1: $this->ErrorDesc = iconv($InCharset, $Charset, $strTooBigFile); break;
							case 2: $this->ErrorDesc = iconv($InCharset, $Charset, $strTooBigFile); break;
							case 3: $this->ErrorDesc = iconv($InCharset, $Charset, $strFailedUpload3); break;
							case 4: $this->ErrorDesc = iconv($InCharset, $Charset, $strFailedUpload4); break;
							case 6: $this->ErrorDesc = iconv($InCharset, $Charset, $strFailedUpload6); break;
							default: $this->ErrorDesc = iconv($InCharset, $Charset, $strFailedUpload);
						}
					} else {
						$filesize = @filesize($this->TempDir.'/'.$FileName);
						if ($filesize == false) {
							$this->IsError = true;
							$this->ErrorDesc = iconv($InCharset, $Charset, $strFailedSend);
						}
						$Attachment = Array(
							'name' => $_FILES['fileAttach']['name'],
							'tmp_name' => $FileName,
							'size' => $_FILES['fileAttach']['size'],
							'type' => $_FILES['fileAttach']['type']
						);
						$_SESSION['attachments'][] = $Attachment;
					}
				}
			}
		}
	}
}
?>