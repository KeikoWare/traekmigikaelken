function SelectLine(LineNum)
{
	if(document.getElementById('check_' + LineNum).checked)
	{
		document.getElementById('line_' + LineNum).className = 'wm_list_readitem_selected';
	} else {
		document.getElementById('line_' + LineNum).className = 'wm_list_readitem';
	}
}

function SelectAllCheckbox(FirstLineNum, LastLineNum)
{
	if( document.getElementById("check").checked )
	{
		for(var i = FirstLineNum; i <= LastLineNum; i++)
		{
			document.getElementById('check_' + i).checked = true;
			document.getElementById('line_' + i).className = 'wm_list_readitem_selected';
		}
	} else {
		for(var i = FirstLineNum; i <= LastLineNum; i++)
		{
			document.getElementById('check_' + i).checked = false;
			document.getElementById('line_' + i).className = 'wm_list_readitem';
		}
	}
}

function DeleteSelected(selectcheckbox, confirmation)
{
	var doc = document.body;
	var CheckBoxes = doc.getElementsByTagName('input');
	var MesIdArray = Array();
	for (var i=0; i<CheckBoxes.length; i++)
	{
		if (CheckBoxes[i].type == 'checkbox' && CheckBoxes[i].checked && CheckBoxes[i].id != 'check')
		{
			MesIdArray.unshift(CheckBoxes[i].value);
		}
	}
	if (MesIdArray.length == 0) 
		alert(selectcheckbox);
	else
		if (confirm(confirmation)) {
			var MesIds = MesIdArray.join(', ');
			var InputIds = document.getElementById('ids');
			InputIds.value = MesIds;
			var FormDelete = document.getElementById('delete');
			FormDelete.submit()
		}
}

function DeAttachFile(key, confirmation)
{
	if (confirm(confirmation))
	{
		var ModeElem = document.getElementById('mode');
		ModeElem.value = 'deattach';
		var KeyElem = document.getElementById('key');
		KeyElem.value = key;
		var FormElem = document.getElementById('message_form');
		FormElem.submit();
	}
}

function Send(EmptyToField, EmptySubjectConfirmation)
{
	var ToElem = document.getElementById('to');
	if (ToElem.value == '')
		alert (EmptyToField);
	else {
		var SubjectElem = document.getElementById('subject');
		var sendOk = 0;
		if (SubjectElem.value.length > 0) sendOk = 1;
		if (SubjectElem.value.length == 0) {
			if (confirm(EmptySubjectConfirmation)) sendOk = 1;
		}
		if (sendOk == 1) {
			var ModeElem = document.getElementById('mode');
			ModeElem.value = 'send';
			var FormElem = document.getElementById('message_form');
			FormElem.submit();
		}
	}
}

function Delete(Page, Ids, confirmation)
{
	if (confirm(confirmation))
		document.location='actions.php?action=delete&page='+Page+'&ids='+Ids;
}
