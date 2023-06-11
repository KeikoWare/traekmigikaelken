function CheckForm01()
{
	var intSubmitOk = 1;
	if( isNaN(document.wm_settings.intIncomingMailPort.value))
	{
		alert("Incoming mail port is not a number.");
		intSubmitOk = 0;
	}
	if( isNaN(document.wm_settings.intOutgoingMailPort.value) && intSubmitOk == 1 )
	{
		alert("Outgoing mail port is not a number.");
		intSubmitOk = 0;
	}
	if( intSubmitOk == 1 )
		document.wm_settings.submit();
}

function CheckForm02()
{
	document.wm_settings.submit();
}

function CheckForm03()
{
	document.wm_settings.submit();
}

function CheckForm04()
{
	if( document.wm_settings.hideLoginRadionButton[0].checked )
	{
		document.wm_settings.intHideLogin.value = 0;
	}
	if( document.wm_settings.hideLoginRadionButton[1].checked )
	{
		if( document.wm_settings.hideLoginSelect.selectedIndex == 0 )
		{
			document.wm_settings.intHideLogin.value = 10;
		} else {
			document.wm_settings.intHideLogin.value = 11;
		}
	}
	if( document.wm_settings.hideLoginRadionButton[2].checked )
	{
		if( !document.wm_settings.intDisplayDomainAfterLoginField.checked && !document.wm_settings.intLoginAsConcatination.checked )
		{
			document.wm_settings.intHideLogin.value = 20;
		} else {
			if( document.wm_settings.intDisplayDomainAfterLoginField.checked && !document.wm_settings.intLoginAsConcatination.checked )
				document.wm_settings.intHideLogin.value = 21;
			if( !document.wm_settings.intDisplayDomainAfterLoginField.checked && document.wm_settings.intLoginAsConcatination.checked )
				document.wm_settings.intHideLogin.value = 22;
			if( document.wm_settings.intDisplayDomainAfterLoginField.checked && document.wm_settings.intLoginAsConcatination.checked )
				document.wm_settings.intHideLogin.value = 23;
		}
	}
	document.wm_settings.submit();
}

function CheckForm05()
{
	document.wm_settings.submit();
}

function SetDomain(intDomainNum)
{
	switch (intDomainNum)
	{
		case 0 :
			document.wm_settings.hideLoginSelect.disabled = true;
			document.wm_settings.txtUseDomain.disabled = true;
			document.wm_settings.intDisplayDomainAfterLoginField.disabled = true;
			document.wm_settings.intLoginAsConcatination.disabled = true;
			break;
		case 1 :
			document.wm_settings.hideLoginSelect.disabled = false;
			document.wm_settings.txtUseDomain.disabled = true;
			document.wm_settings.intDisplayDomainAfterLoginField.disabled = true;
			document.wm_settings.intLoginAsConcatination.disabled = true;
			break;
		case 2 :
			document.wm_settings.hideLoginSelect.disabled = true;
			document.wm_settings.txtUseDomain.disabled = false;
			document.wm_settings.intDisplayDomainAfterLoginField.disabled = false;
			document.wm_settings.intLoginAsConcatination.disabled = false;
			break;
	}
}

function DeleteDomain(id_domain)
{
	if( document.wm_settings.hideLoginRadionButton[0].checked )
	{
		document.wm_settings.intHideLogin.value = 0;
	}
	if( document.wm_settings.hideLoginRadionButton[1].checked )
	{
		if( document.wm_settings.hideLoginSelect.selectedIndex == 0 )
		{
			document.wm_settings.intHideLogin.value = 10;
		} else {
			document.wm_settings.intHideLogin.value = 11;
		}
	}
	if( document.wm_settings.hideLoginRadionButton[2].checked )
	{
		if( !document.wm_settings.intDisplayDomainAfterLoginField.checked && !document.wm_settings.intLoginAsConcatination.checked )
		{
			document.wm_settings.intHideLogin.value = 20;
		} else {
			if( document.wm_settings.intDisplayDomainAfterLoginField.checked && !document.wm_settings.intLoginAsConcatination.checked )
				document.wm_settings.intHideLogin.value = 21;
			if( !document.wm_settings.intDisplayDomainAfterLoginField.checked && document.wm_settings.intLoginAsConcatination.checked )
				document.wm_settings.intHideLogin.value = 22;
			if( document.wm_settings.intDisplayDomainAfterLoginField.checked && document.wm_settings.intLoginAsConcatination.checked )
				document.wm_settings.intHideLogin.value = 23;
		}
	}
	if( confirm("Are you sure?") )
	{
		document.wm_settings.mode.value = "delete_domain";
		document.wm_settings.id_user_delete.value = id_domain;
		document.wm_settings.submit();
	}
}

