var email_value = '';
var login_value = '';

function SafariDetect()
{
	return navigator.userAgent.indexOf("Safari") >= 0;
}

function FireFoxDetect()
{
	return navigator.userAgent.indexOf("Gecko") >= 0;
}

function OperaDetect()
{
	return navigator.userAgent.indexOf("Opera") >= 0;
}

function CreateErrorBar()
{
	if (document.getElementById("error"))
	{
		aCachePageElements[ERROR_DIV] = document.getElementById("error");
	}
}

function HideErrorBar()
{
	var error = aCachePageElements[ERROR_DIV];
	while (error.hasChildNodes()) error.removeChild(error.lastChild);
}

function ShowErrorBar(text)
{
	var error = aCachePageElements[ERROR_DIV];
	while (error.hasChildNodes()) error.removeChild(error.lastChild);
	var error_div = CreateChildWithAttrs(error,'div',[['class','wm_error_div'],['align','center']]);
	var header_container = CreateChild(error_div,'div');
	var error_container = CreateChild(error_div,'div');
	var button_container = CreateChild(error_div,'div');
	header_container.className = 'wm_error_header';
	header_container.innerHTML = aTitles[ERROR_HEADER];
	error_container.className= 'wm_error_text';
	error_container.innerHTML = text;
	button_container.innerHTML = '<div class="wm_error_button"><a href="" class="wm_reg" onclick="javascript:HideErrorBar(); return false;">' + strOK + '</a></div>';
	if (aCachePageElements[EMAIL]) {
		aCachePageElements[EMAIL].focus();
	} else {
		if (aCachePageElements[LOGIN])
			aCachePageElements[LOGIN].focus();
	}
}

function CreateLoadingBar()
{
	var load_div = CreateChild(document.body,'div');
	aCachePageElements[LOAD_DIV] = load_div;
	load_div.className = 'wm_loading_div';
	load_div.innerHTML = '<img src="images/wait.gif" align="middle">' + strLoading;
}

function ShowLoadingBar()
{
	if (aCachePageElements[LOAD_DIV])
	{
		var load_div = aCachePageElements[LOAD_DIV];
		var scrollY = 0;
		if (document.body && typeof document.body.scrollTop != "undefined")
		{
			scrollY += document.body.scrollTop;
			if (scrollY == 0 && document.body.parentNode && typeof document.body.parentNode != "undefined")
			{
				scrollY += document.body.parentNode.scrollTop;
			}
		} else if (typeof window.pageXOffset != "undefined")  {
			scrollY += window.pageYOffset;
		}
		load_div.style.top = scrollY+'px';
		load_div.style.visibility = 'visible';
	}
}

function HideLoadingBar()
{
	if (aCachePageElements[LOAD_DIV]) aCachePageElements[LOAD_DIV].style.visibility = 'hidden';
}

function CreateHttpRequestObject()
{
	var http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) http_request.overrideMimeType('text/xml');
	} else if (window.ActiveXObject) { // IE
		try {http_request = new ActiveXObject("Msxml2.XMLHTTP");}
		catch (e) {
		try {http_request = new ActiveXObject("Microsoft.XMLHTTP");}
		catch (e) {}
		}
	}
	return http_request;
}

function MakeRequest(url){
	var http_request = CreateHttpRequestObject();
	http_request.onreadystatechange = function(){
		if (http_request.readyState == 4)
		{
			HideLoadingBar();
			if (http_request.status == 200)
			{
				var xmldoc = http_request.responseXML;
				var webmail_data = xmldoc.documentElement;
				if (webmail_data)
				{
					if (webmail_data.tagName == 'webmail_data')
					{
						var webmail_error = webmail_data.getElementsByTagName('webmail_error')[0];
						if (webmail_error)
						{
							var err_desc = webmail_error.getAttribute('description');
							if (err_desc)
							{
								if(err_desc.length > 0) ShowErrorBar(err_desc);
								else ShowErrorBar(strEmptyError);
							} else ShowErrorBar(strWithoutDescError);
						} else {
							document.location = page_script_url;
						}
					} else {
						ShowErrorBar(strTagError);
					}
				} else {
					ShowErrorBar(strXMLError + http_request.responseText);
				}
			} else {
				ShowErrorBar(strRequestError + http_request.status + '\n\n' + http_request.responseText);
			}
		}
	}
	http_request.open('POST', url, true);
	http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

	var post_data = '';
	if (login_mode == 'advanced' || hide_login < 20) post_data += 'email='+aCachePageElements[EMAIL].value+'&';
	if (login_mode == 'advanced' || hide_login != 10 && hide_login != 11) post_data += 'login='+aCachePageElements[LOGIN].value+'&';
	post_data += 'password='+aCachePageElements[PASSWORD].value;
	if (login_mode == 'advanced')
	{
		post_data += '&adv_inc_server='+aCachePageElements[POP3_SERVER].value;
		post_data += '&adv_inc_server_port='+aCachePageElements[POP3_PORT].value;
		post_data += '&adv_out_server='+aCachePageElements[SMTP_SERVER].value;
		post_data += '&adv_out_server_port='+aCachePageElements[SMTP_PORT].value;
		post_data += '&adv_out_server_auth=';
		if (aCachePageElements[USE_SMTP_AUTH].checked) {
			post_data += '1';
		} else {
			post_data += '0';
		}
	}
	http_request.send(post_data);
}

function CreateChild(oParentNode,sTag)
{
	var oNode = document.createElement(sTag);
	oParentNode.appendChild(oNode);
	return oNode;
}

function CreateChildWithAttrs(oParentNode, sTag, aAttrs)
{
	if (is_ff || is_opera)
	{
		var oNode = document.createElement(sTag);
		for (var i = 0; i < aAttrs.length; i++)
		{
			var t = aAttrs[i];
			var key = t[0];
			var val = t[1];
			oNode.setAttribute(key,val);
		}
	} else {
		var sAdd = '';
		for (var i = 0; i < aAttrs.length; i++)
		{
			var t = aAttrs[i];
			sAdd += ' ' + t[0] + '="'+ t[1] + '"';
		}
		sTag = '<' + sTag + sAdd + '>';
		var oNode = document.createElement(sTag);
	}
	oParentNode.appendChild(oNode);
	return oNode;
}

function AddServersPart(main_table, SERVER, PORT_)
{
	var tr = main_table.insertRow(-1);
	var td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_login_field']]);
	td.innerHTML = aTitles[SERVER];
	td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_edit']]);
	aCachePageElements[SERVER] = CreateChildWithAttrs(td,'input',[['type','text'],['name','adv_pop3'],['class','wm_input'],['size','10'],['value',aValues[SERVER]]]);
	td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_login_field']]);
	td.innerHTML = aTitles[PORT_];
	td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_edit']]);
	aCachePageElements[PORT_] = CreateChildWithAttrs(td,'input',[['type','text'],['name','adv_pop3_port'],['class','wm_input'],['size','2'],['value',aValues[PORT_]]]);
}

function SetValues()
{
	if (aCachePageElements[EMAIL]) email_value = aCachePageElements[EMAIL].value;
	if (aCachePageElements[LOGIN]) login_value = aCachePageElements[LOGIN].value;
}

function Processing()
{
	HideErrorBar();
	var main_form = document.getElementById("content");
	aCachePageElements[CONTENT_FORM] = main_form;
	while (main_form.hasChildNodes()) main_form.removeChild(main_form.lastChild);
	var main_table = CreateChild(main_form,'table');
	main_table.className = 'wm_dialog';
	var tr = main_table.insertRow(-1);
	var	td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_login_header'],['colspan','4']]);
	td.innerHTML = aTitles[LOGIN_INFORMATION];

	if (login_mode == 'advanced' || hide_login < 20)
	{
		tr = main_table.insertRow(-1);
		td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_login_field']]);
		td.innerHTML = aTitles[EMAIL];
		td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_edit'],['colspan','3'],['width','170']]);
		aCachePageElements[EMAIL] = CreateChildWithAttrs(td,'input',[['type','text'],['name','email'],['class','wm_login_input'],['onfocus','EmailFocus();'],['value',email_value]]);
	}

	if (login_mode == 'advanced' || hide_login != 10 && hide_login != 11)
	{
		tr = main_table.insertRow(-1);
		td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_login_field']]);
		td.innerHTML = aTitles[LOGIN];
		if (login_mode != 'advanced' && (hide_login == 21 || hide_login == 23))
		{
			td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_edit'],['colspan','2'],['width','130']]);
			aCachePageElements[LOGIN] = CreateChildWithAttrs(td,'input',[['type','text'],['name','login'],['class','wm_login_input'],['onfocus','LoginFocus();'],['value',login_value]]);
			td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_edit']]);
			td.innerHTML = '@'+aValues[USE_DOMAIN];
		} else {
			td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_edit'],['colspan','3'],['width','170']]);
			aCachePageElements[LOGIN] = CreateChildWithAttrs(td,'input',[['type','text'],['name','login'],['class','wm_login_input'],['onfocus','LoginFocus();'],['value',login_value]]);
		}
	}

	tr = main_table.insertRow(-1);
	td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_login_field']]);
	td.innerHTML = aTitles[PASSWORD];
	td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_edit'],['colspan','3'],['width','170']]);
	aCachePageElements[PASSWORD] = CreateChildWithAttrs(td,'input',[['type','password'],['name','password'],['class','wm_login_input'],['onfocus','PasswordFocus();']]);

	if (login_mode == 'advanced')
	{
		AddServersPart(main_table, POP3_SERVER, POP3_PORT);
		AddServersPart(main_table, SMTP_SERVER, SMTP_PORT);
		tr = main_table.insertRow(-1);
		td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_login_field'],['colspan','4']]);
		var smtp_auth = CreateChildWithAttrs(td,'input',[['type','checkbox'],['style','vertical-align: middle;'],['id','adv_smtp_auth'],['name','adv_smtp_auth']]);
		if (aValues[USE_SMTP_AUTH] == 1) smtp_auth.checked = 'checked';
		aCachePageElements[USE_SMTP_AUTH] = smtp_auth;
		var label_smtp_auth = CreateChildWithAttrs(td,'label',[['for','adv_smtp_auth']]);
		label_smtp_auth.innerHTML = aTitles[USE_SMTP_AUTH];
	}

	tr = main_table.insertRow(-1);
	td = CreateChildWithAttrs(tr,'td',[['class','wm_dialog_button_field'],['colspan','4']]);
	if (allow_advanced_login == 1)
	{
		span = CreateChildWithAttrs(td,'span',[['class','wm_dialog_login_switcher']]);
		if (login_mode == 'advanced')
			span.innerHTML = '<a class="wm_reg" href="" onclick="login_mode=\'standard\'; SetValues(); Processing(); return false;">'+aTitles[STANDARD_LOGIN]+'</a>';
		else
			span.innerHTML = '<a class="wm_reg" href="" onclick="login_mode=\'advanced\'; SetValues(); Processing(); return false;">'+aTitles[ADVANCED_LOGIN]+'</a>';
	}
	span = CreateChildWithAttrs(td,'span',[['class','wm_dialog_login_button']]);
	enter_button = CreateChildWithAttrs(span,'input',[['type','submit'],['name','enter_button'],['onclick','HideErrorBar();CheckLoginForm(); return false;'],['class','wm_button'],['value',aTitles[ENTER]]]);

	if (aCachePageElements[EMAIL]) aCachePageElements[EMAIL].focus();
	else aCachePageElements[LOGIN].focus();
}

function EmailFocus()
{
	aCachePageElements[EMAIL].select();
}

function LoginFocus()
{
	if ((login_mode == 'advanced' || hide_login < 20) && aCachePageElements[LOGIN].value.length == 0)
	{
		aCachePageElements[LOGIN].value = aCachePageElements[EMAIL].value;
	}
	aCachePageElements[LOGIN].select();
}

function PasswordFocus()
{
	aCachePageElements[PASSWORD].select();
}

function CheckLoginForm()
{
	if (start_error != '') {
		ShowErrorBar(start_error);
	} else {
		var login_ok = 1;
		if ((login_mode == 'advanced' || hide_login < 20) && aCachePageElements[EMAIL].value.length == 0)
		{
			alert(aTitles[EMPTY_EMAIL]);
			login_ok = 0;
			aCachePageElements[EMAIL].focus();
		}
		if (login_ok == 1 && (login_mode == 'advanced' || hide_login != 10 && hide_login != 11) &&
			aCachePageElements[LOGIN].value.length == 0)
		{
			alert(aTitles[EMPTY_LOGIN]);
			login_ok = 0;
			aCachePageElements[LOGIN].focus();
		}
		if (login_mode == 'advanced' && login_ok == 1 && (aCachePageElements[POP3_SERVER].value.length == 0 ||
		aCachePageElements[POP3_PORT].value.length == 0 || aCachePageElements[SMTP_SERVER].value.length == 0 ||
		aCachePageElements[SMTP_PORT].value.length == 0))
		{
			alert(aTitles[EMPTY_SERVERS]);
			login_ok = 0;
			aCachePageElements[POP3_SERVER].focus();
		}
		if (login_ok == 1)
		{
			ShowLoadingBar();
			MakeRequest(processing_script_url+'?action=get&request=login&login_mode='+login_mode);
		}
	}
}
