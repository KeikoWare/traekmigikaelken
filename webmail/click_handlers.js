function ToolbarClick() {}

function RefreshClick()
{
	changeLocation('r|%|' + SCREEN_MESSAGES_LIST + '|%|' + aCurrentState[CURRENT_PAGE]);
}

function NewMessageClick()
{
	changeLocation('|%|' + SCREEN_COMPOSE_MESSAGE + '|%|n');
}

function NewMessage(mode)
{
	if (mode == 'n'){
		if (allow_dhtml)
		{
			var plain_html = 1;
		} else {
			var plain_html = 0;
		}
		var aMessage = new Array();
		aMessage['from'] = global_email;
		aMessage['to'] = '';
		aMessage['cc'] = '';
		aMessage['bcc'] = '';
		aMessage['subject'] = '';
		aMessage['message'] = '';
		aMessage['plain_html'] = plain_html;
		aCacheData[CACHE_COMPOSE_MESSAGE_ATTACHMENTS].removeAll();
		ComposeMessage(aMessage);
		this.className = 'wm_toolbar_item';
	}
	else 
	{
		var id_message = aCurrentState[CURRENT_MESSAGE_ID];
		if (http_request)
		{
			switch (mode){
				case 'r':
					MakeRequest(processing_script_url + '?action=get&request=view_original&id=' + id_message,GET_MESSAGE_FOR_REPLY_HANDLER);
				break;
				case 'a':
					MakeRequest(processing_script_url + '?action=get&request=view_original&id=' + id_message,GET_MESSAGE_FOR_REPLY_ALL_HANDLER);
				break;
				case 'f':
					MakeRequest(processing_script_url + '?action=get&request=view_original_fwd&id=' + id_message + '&temp=' + document.getElementById("temp").value,GET_MESSAGE_FOR_FORWARD_HANDLER);
				break;
			}
		}
	}
}

function SendMessageClick()
{
	SendMessage();
}

function SaveMessageClick()
{
	var id_message = aCurrentState[CURRENT_MESSAGE_ID];
	document.location.href = save_message_url + "?id=" + id_message;
}

function BackToListClick()
{
	changeLocation('|%|' + SCREEN_MESSAGES_LIST + '|%|' + aCurrentState[CURRENT_PAGE]);
}

function UpMessageClick()
{
	if (aCurrentState[CURRENT_UP_MESSAGE])
	{
		changeLocation('|%|' + SCREEN_VIEW_MESSAGE + '|%|' + aCurrentState[CURRENT_UP_MESSAGE]);
	}
}

function DownMessageClick()
{
	if (aCurrentState[CURRENT_DOWN_MESSAGE] > 0)
	{
		changeLocation('|%|' + SCREEN_VIEW_MESSAGE + '|%|' + aCurrentState[CURRENT_DOWN_MESSAGE]);
	}
}

function DeleteSelected()
{
	DoGroupOperation('delete');
}

function DeleteMessageClick()
{
	var ar_post_data = new Array();
	ar_post_data.push(['action','delete']);
	ar_post_data.push(['request','messages']);
	ar_post_data.push(['id_message',aCurrentState[CURRENT_MESSAGE_ID]]);
	ar_post_data.push(['page',aCurrentState[CURRENT_PAGE]]);
	if (confirm(strConfirmation))
	{
		aCurrentState[CURRENT_LOCATION_CHANGE] = 0;
		changeLocation('r|%|' + SCREEN_MESSAGES_LIST + '|%|' + aCurrentState[CURRENT_PAGE]);
		aCurrentState[CURRENT_LOCATION_CHANGE] = 1;
		MakeRequest(processing_script_url,DELETE_MESSAGES_HANDLER,ar_post_data);
	}
}

function RedirectClick()
{
	if (aCurrentState[CURRENT_SCREEN] == SCREEN_VIEW_MESSAGE)
	{
		if (typeof(aCurrentState[CURRENT_MESSAGE_ID]) != 'undefined')
		{
			changeLocation('|%|' + SCREEN_REDIRECT_MESSAGE + '|%|' + aCurrentState[CURRENT_MESSAGE_ID] + '|%|v');
		}
	}
	if (aCurrentState[CURRENT_SCREEN] == SCREEN_MESSAGES_LIST)
	{
		var arMessageIds = GetCheckedIds();
		if (arMessageIds.length > 0 )
		{
			var MessageIds = arMessageIds.join(', ');
			changeLocation('|%|' + SCREEN_REDIRECT_MESSAGE + '|%|' + MessageIds + '|%|l');
		}
	}
}

function RedirectMessage(message_id, mode)
{
	switch (mode){
		case 'v':
			ShowRedirectScreen([message_id*1],'redirect');
		break;
		case 'l':
			var ar_message_id = message_id.split(',');
			ShowRedirectScreen(ar_message_id,'redirect');
		break;
	}
}

function ForwardClick()
{
	changeLocation('|%|' + SCREEN_COMPOSE_MESSAGE + '|%|f');
}

function ReplyAllClick()
{
	changeLocation('|%|' + SCREEN_COMPOSE_MESSAGE + '|%|a');
}

function ReplyClick()
{
	changeLocation('|%|' + SCREEN_COMPOSE_MESSAGE + '|%|r');
}

function PrintMessageClick()
{
	var id_message = aCurrentState[CURRENT_MESSAGE_ID];
	var popup_url = "";
	popup_url = print_message_url + "?id=" + id_message;
	setPopUpSize(760,480);
	_scrollbars='yes';
	_status='no';
	_resizable='yes';
	_toolbar='yes';
	PopUpEx(popup_url,"PrintVersionWin");
}

// PopUp windows
var shown='';
var defWinCaption='Popup';
var windowwidth,windowheight,windowleft,windowtop;
var _toolbar,_location,_directories,_directories,_status,_scrollbars,_resizable,_copyhistory;

function setPopUpSize(w,h,l,t)
{
	if (w) windowwidth=w;
	if (h) windowheight=h;
	if (l) {windowleft=l} else if (window.screen) windowleft=(screen.width-windowwidth)/2;
	if (t) {windowtop=t;} else if (window.screen) windowtop=(screen.height-windowheight)/2;
}

function PopUpEx(url,wName)
{
	if (!wName || wName.length==0) wName = defWinCaption;
	shown=window.open(url, wName, 'left='+windowleft+',top='+windowtop+
		',toolbar='+_toolbar+',location='+_location+',directories='+_directories+
		',status='+_status+',scrollbars='+_scrollbars+',resizable='+_resizable+
		',copyhistory='+_copyhistory+',width='+windowwidth+',height='+windowheight);
	shown.focus();
	return false;
}
