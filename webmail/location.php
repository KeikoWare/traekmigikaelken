<?php
error_reporting (E_ALL ^ E_NOTICE);
Header("Content-type: text/html; charset=utf-8");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="Thu, 01 Jan 1900 00:00:00 GMT">
	<title></title>
</head>
<body>
	<script language="JavaScript" type="text/javascript">
		parent.onLocationChanged('<?php echo $_REQUEST['historyLocation'];?>');
	</script>
</body>
</html>
