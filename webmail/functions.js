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

function attachment(id,filename,size,type)
{
	this.id = id;
	this.filename = filename;
	this.size = size;
	this.type = type;
}

function DoGroupOperation(operation)
{
	var ar_ids = GetCheckedIds();
	if (ar_ids.length > 0)
	{
		var str_ids = ar_ids.join(', ');
		var ar_post_data = new Array();
		ar_post_data.push(['action',operation]);
		ar_post_data.push(['request','messages']);
		ar_post_data.push(['id_message',str_ids]);
		ar_post_data.push(['page',aCurrentState[CURRENT_PAGE]]);
		var handler;
		switch(operation)
		{
			case "delete":
				handler = DELETE_MESSAGES_HANDLER;
				break;
			default:
				handler = MARK_READ_HANDLER;
				break;
		}
		if (operation == "delete")
		{
			if (confirm(strConfirmation))
			{
				aCurrentState[CURRENT_LOCATION_CHANGE] = 0;
				changeLocation('r|%|' + SCREEN_MESSAGES_LIST + '|%|' + aCurrentState[CURRENT_PAGE]);
				MakeRequest(processing_script_url,handler,ar_post_data);
			}
		} else {
			MakeRequest(processing_script_url,handler,ar_post_data);
		}
	} else {
		alert(strMarkOneItem);
	}
}

function ComposeMessage(aMessage)
{
	ShowScreen(SCREEN_COMPOSE_MESSAGE);
	aCachePageElements[COMPOSE_MESSAGE_FROM].value = HTMLdecode(aMessage['from']);
	aCachePageElements[COMPOSE_MESSAGE_TO].value = HTMLdecode(aMessage['to']);
	aCachePageElements[COMPOSE_MESSAGE_CC].value = HTMLdecode(aMessage['cc']);
	aCachePageElements[COMPOSE_MESSAGE_BCC].value = HTMLdecode(aMessage['bcc']);
	aCachePageElements[COMPOSE_MESSAGE_SUBJECT].value = HTMLdecode(aMessage['subject']);
	aCachePageElements[COMPOSE_MESSAGE_TEXT].value = HTMLspacedecode(HTMLdecode(aMessage['message']));
	RefreshAttachmentsRows();
}

function ProcessAttachmentRow(row, att, key, ind)
{
	var td1 = row.insertCell(-1);
	td1.className = 'wm_new_attach_data';
	td1.width = '90px';
	td1.innerHTML = 'File&nbsp;#' + (ind + 1);
	var td2 = row.insertCell(-1);
	td2.className = 'wm_attach_value_icon';
	var td3 = row.insertCell(-1);
	td3.className = 'wm_attach_value_text';

	var attach_filename = '';
	var attach_size = '';
	attach_filename = att.filename;
	attach_size = att.size;
	var icon_name;
	var ext = '';
	var dotpos = attach_filename.lastIndexOf('.');
	if (dotpos > -1)
	{
		ext = attach_filename.substr(dotpos + 1).toLowerCase();
	}
	td2.innerHTML = '<img src="images/icons/' + GetIconNameByExtension(ext) + '" width="32" height="32" border="0">';
	td3.innerHTML = attach_filename + '&nbsp;' + GetStrSize(attach_size) + '&nbsp;<a class="wm_reg" href="" onclick="javascript:DeleteAttachment(\'' + key + '\'); return false;">'+strDelete+'</a>';
}

function DeleteAttachment(id)
{
	var ats = aCacheData[CACHE_COMPOSE_MESSAGE_ATTACHMENTS];
	if (ats.exists(id))
	{
		ats.remove(id);
		RefreshAttachmentsRows();
	}
}

function RefreshAttachmentsRows()
{
	var da = aCacheData[CACHE_COMPOSE_MESSAGE_ATTACHMENTS];
	var at = aCachePageElements[COMPOSE_MESSAGE_ATTACHMENTS_TABLE];
	while (at.rows.length > 7)
	{
		at.deleteRow(at.rows.length - 1);
	}
	var keys = da.keys();
	for (var i = 0; i < keys.length; i++)
	{
		var key = keys[i];
		var a = da.getVal(key);
		var ntr = at.insertRow(-1);
		ProcessAttachmentRow(ntr, a, key, i);
	}
	var div = aCachePageElements[COMPOSE_MESSAGE_ATTACHMENTS_DIV];
	while (div.hasChildNodes()) div.removeChild(div.lastChild);
	div.innerHTML = strAttachFile+'<input id="fileAttach" type="file" runat="server" name="fileAttach"> &nbsp;  <input type="submit" value="'+strBtnAttachFile+'" class="wm_button"/>'		
}

function HTMLspacedecode(str)
{
	return str.replace(/&nbsp;/g,' ');
}

function HTMLdecode(str)
{
	if (typeof(str) == 'string')
	{
		if (str.length > 0)
		{
			return str.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"').replace(/&amp;/g,'&');
		} else {
			return '';
		}
	} else {
		return '';
	}
}

function trim(str)
{
	return str.replace(/^\s+/, '').replace(/\s+$/, '');
}

function AttachmentLoaded(id_attach,filename,size,type)
{
	var a = aCacheData[CACHE_COMPOSE_MESSAGE_ATTACHMENTS];
	if (a)
	{
		var at = new attachment(id_attach,filename,size,type);
		a.add(id_attach, at);
		var frm = aCachePageElements[COMPOSE_MESSAGE_ATTACHMENTS_FORM];
		var div = aCachePageElements[COMPOSE_MESSAGE_ATTACHMENTS_DIV];
		while (div.hasChildNodes()) div.removeChild(div.lastChild);
		div.innerHTML = strAttachFile+'<input id="fileAttach" type="file" runat="server" name="fileAttach"> &nbsp;  <input type="submit" value="'+strBtnAttachFile+'" class="wm_button"/>'		
		RefreshAttachmentsRows();
	}
}

function SendMessage()
{
	var listc = [COMPOSE_MESSAGE_FROM,COMPOSE_MESSAGE_TO,COMPOSE_MESSAGE_CC,COMPOSE_MESSAGE_BCC,COMPOSE_MESSAGE_SUBJECT];
	var listn = ['str_from','str_to','str_cc','str_bcc','str_subject'];
	var ar_post_data = new Array();
	ar_post_data.push(['action','send']);
	for (var i = 0; i < listc.length; i++)
	{
		ar_post_data.push([listn[i], aCachePageElements[listc[i]].value]);
	}

	ar_post_data.push(['message', aCachePageElements[COMPOSE_MESSAGE_TEXT].value]);

	atts = aCacheData[CACHE_COMPOSE_MESSAGE_ATTACHMENTS];

	var keys = atts.keys();
	var tmpNames = "";
	var realNames = "";
	var types = "";
	for (var i = 0; i < keys.length; i++)
	{
		var key = keys[i];
		var a = atts.getVal(key);
		tmpNames += "|%|" + a.id;
		realNames += "|%|" + a.filename;
		types += "|%|" + a.type;
	}
	ar_post_data.push(['atts_temp_names',tmpNames]);
	ar_post_data.push(['atts_real_names',realNames]);
	ar_post_data.push(['atts_types',types]);
	if (document.getElementById("temp") && (trim(aCachePageElements[listc[1]].value).length > 0
		 || trim(aCachePageElements[listc[2]].value).length > 0 || trim(aCachePageElements[listc[3]].value).length > 0))
	{
		aCurrentState[CURRENT_LOCATION_CHANGE] = 0;
		changeLocation('|%|' + SCREEN_MESSAGES_LIST + '|%|' + aCurrentState[CURRENT_PAGE]);
		aCurrentState[CURRENT_LOCATION_CHANGE] = 1;
		ar_post_data.push(['temp',document.getElementById("temp").value]);
		MakeRequest(processing_script_url,SEND_MESSAGE_HANDLER,ar_post_data);
	} else {
		alert(strEmptyToField);
	}
}

function GetCheckedIds()
{
	var ar_ids = new Array();
	var mt_rows = aCachePageElements[MESSAGES_TABLE].rows;
	for (var i = 1; i < mt_rows.length; i++)
	{
		if (mt_rows[i].cells[1].childNodes[0].checked)
		{
			ar_ids.push(mt_rows[i].cells[1].childNodes[0].value);
		}
	}
	return ar_ids;
}

function UncheckAllRows()
{
	if (aCachePageElements[CHECK_ALL_CHECKBOX])
	{
		aCachePageElements[CHECK_ALL_CHECKBOX].checked = false;
	}
	CheckAllRows(false,MESSAGES_TABLE);
}

function DoRedirectMessage()
{
	var arIds = aCurrentState[CURRENT_REDIRECT_MESSAGES_IDS];
	var to = aCachePageElements[REDIRECT_MESSAGE_TO].value;
	var ar_post_data = new Array();
	if (trim(to).length == 0)
	{
		alert(strTypeEmailAdr);
	} else {
		ar_post_data.push(['action','redirect']);
		ar_post_data.push(['str_to',to]);
		ar_post_data.push(['id',arIds.join(',')]);
		MakeRequest(processing_script_url,SEND_MESSAGE_HANDLER,ar_post_data);
	}
}

function ShowRedirectScreen(arIds,mode)
{
	// mode: redirect or forward
	aCurrentState[CURRENT_REDIRECT_MESSAGES_IDS] = arIds;
	ShowScreen(SCREEN_REDIRECT_MESSAGE);
	var input_to = aCachePageElements[REDIRECT_MESSAGE_TO];
	input_to.select();
}

function GetReplyAddr(str_from, str_to, str_cc)
{
	var to_ar = str_to.split(',');
	var cc_ar = str_cc.split(',');
	var rec_ar = new Array();
	var next = 0;
	if (str_from != global_email) {rec_ar[next] = str_from; next++;}
	var trimmed, flag;
	for (var i = 0; i < to_ar.length; i++)
	{
		trimmed = trim(to_ar[i]);
		flag = true;
		if (rec_ar.length == 0 && trimmed == global_email) flag = false;
		for (var j = 0; j < rec_ar.length; j++)
		{
			if (trimmed == rec_ar[j] || trimmed == '' || trimmed == global_email) flag = false;
		}
		if (flag)
		{
			rec_ar[next] = trimmed;
			next++;
		}
	}
	for (var i = 0; i < cc_ar.length; i++)
	{
		trimmed = trim(cc_ar[i]);
		flag = true;
		if (rec_ar.length == 0 && trimmed == global_email) flag = false;
		for (var j = 0; j < rec_ar.length; j++)
		{
			if ((trimmed == rec_ar[j]) || (trimmed == '') || (trimmed == global_email)) flag = false;
		}
		if (flag)
		{
			rec_ar[next] = trimmed;
			next++;
		}
	}
	var result = trim(rec_ar.join(', '));
	if (result == '') result = global_email;
	return result;
}

//input data: xml object message, int mode for processing
//modes:
//PM_REPLY = 0,
//PM_REPLY_ALL = 1,
//PM_FORWARD = 2
//output: array with texts
function ProcessingMessage(xml_message, mode)
{
	var PM_REPLY = 0, PM_REPLY_ALL = 1, PM_FORWARD = 2, aMessage = [];
	switch(mode)
	{
		case PM_REPLY:
			aCacheData[CACHE_COMPOSE_MESSAGE_ATTACHMENTS].removeAll();
			aMessage['from'] = global_email;
			aMessage['to'] = xml_message.getElementsByTagName('from')[0].childNodes[0].nodeValue;
			aMessage['cc'] = '';
			aMessage['bcc'] = '';
			aMessage['subject'] = strReSubjectPref + xml_message.getElementsByTagName('subject')[0].childNodes[0].nodeValue;
			// plain text message in plain text editor
			var reply_message = xml_message.getElementsByTagName('txt_message')[0].childNodes[0].nodeValue;
			reply_message = reply_message.replace(/^/g,'>').replace(/\n/g,'\n>');
			reply_message = '\n\n' + reply_message;
			aMessage['message'] = reply_message;
			aMessage['plain_html'] = 0;
		break;

		case PM_REPLY_ALL:
			var str_reply_all = GetReplyAddr(xml_message.getElementsByTagName('from')[0].childNodes[0].nodeValue, xml_message.getElementsByTagName('to')[0].childNodes[0].nodeValue, xml_message.getElementsByTagName('cc')[0].childNodes[0].nodeValue);
			aMessage['from'] = global_email;
			aMessage['to'] = str_reply_all;
			aMessage['cc'] = '';
			aMessage['bcc'] = '';
			aMessage['subject'] = strReSubjectPref + xml_message.getElementsByTagName('subject')[0].childNodes[0].nodeValue;
			aCacheData[CACHE_COMPOSE_MESSAGE_ATTACHMENTS].removeAll();
			// plain text message in plain text editor
			var reply_message = xml_message.getElementsByTagName('txt_message')[0].childNodes[0].nodeValue;
			reply_message = reply_message.replace(/^/g,'>').replace(/\n/g,'\n>');
			reply_message = '\n\n' + reply_message;
			aMessage['message'] = reply_message;
			aMessage['plain_html'] = 0;
		break;

		case PM_FORWARD:
			aMessage['from'] = global_email;
			aMessage['to'] = '';
			aMessage['cc'] = '';
			aMessage['bcc'] = '';
			aMessage['subject'] =  strFWDSubjectPref+ xml_message.getElementsByTagName('subject')[0].childNodes[0].nodeValue;
			// plain text message in plain text editor
			//var reply_message = xml_message.getElementsByTagName('unmodified_txt_message')[0].childNodes[0].nodeValue;
			var reply_message = strFWDText + '\n' + strFWDFrom + ': ' + xml_message.getElementsByTagName('from')[0].childNodes[0].nodeValue;
			reply_message += '\n' + strFWDTo + ': ' + xml_message.getElementsByTagName('to')[0].childNodes[0].nodeValue;
			reply_message += '\n' + strFWDSent + ': ' + xml_message.getElementsByTagName('date')[0].childNodes[0].nodeValue;
			reply_message += '\n' + strFWDSubject + ': ' + xml_message.getElementsByTagName('subject')[0].childNodes[0].nodeValue;
			reply_message += '\n\n' + strFWDQuoteBegin + '\n\n';
			reply_message += xml_message.getElementsByTagName('txt_message')[0].childNodes[0].nodeValue;
			reply_message += '\n\n' + strFWDQuoteEnd;
			reply_message = '\n\n' + reply_message;
			aMessage['message'] = reply_message;
			aMessage['plain_html'] = 0;
		break;
	}
	return aMessage;
}

function ToolbarMouseOver(oEvent)
{
	if (this.className != 'wm_toolbar_item_selected')
	{
		this.className = 'wm_toolbar_item_over';
	}
}

function ToolbarMouseOut(oEvent)
{
	if (this.className != 'wm_toolbar_item_selected')
	{
		this.className = 'wm_toolbar_item';
	}
}

function drop_down(identifier, mode, ar_items, str_caption)
{
	this.identifier = identifier;
	this.mode = mode;
	if (this.mode == 'first_unclickable')
	{
		this.caption = str_caption;
	}
	this.items = new Array();
	for (var i = 0; i < ar_items.length; i++)
	{
		this.items[i] = ar_items[i];
	}
}

function CreateErrorBar()
{
	var error_div = CreateChildWithAttrs(document.body,'div',[['class','wm_error_div'],['align','center']]);
	aCachePageElements[ERROR_DIV] = error_div;
	error_div.style.position = 'absolute';
	error_div.style.right = '100px';
	var header_container = CreateChild(error_div,'div');
	var error_container = CreateChild(error_div,'div');
	var button_container = CreateChild(error_div,'div');
	error_container.className= 'wm_error_text';
	header_container. className= 'wm_error_header ';
	button_container.innerHTML = '<div class="wm_error_button"><a href="" class="wm_reg" onclick="javascript:HideErrorBar(); return false;">'+strOK+'</a></div>';
}

function ShowErrorBar(text)
{
	var a = aCachePageElements[ERROR_DIV];
	if (a)
	{
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
		a.style.top = scrollY+'px';
		a.style.visibility = 'visible';
		a.childNodes[1].innerHTML = text;
	}
}

function HideErrorBar()
{
	if (aCachePageElements[ERROR_DIV])
	{
		aCachePageElements[ERROR_DIV].style.visibility = 'hidden';
	}
	aCachePageElements[ERROR_DIV].childNodes[0].innerHTML = '';
}

function CreateLoadingBar()
{
	var info_div = CreateChild(document.body,'div');
	aCachePageElements[INFO_DIV] = info_div;
	info_div.className = 'wm_loading_div';
	info_div.innerHTML = '<img src="images/wait.gif" align="middle">' + strLoading;
}

function ShowLoadingBar()
{
	if (aCachePageElements[INFO_DIV])
	{
		var info_div = aCachePageElements[INFO_DIV];
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
		info_div.style.top = scrollY+'px';
		info_div.style.visibility = 'visible';
	}
}

function HideLoadingBar()
{
	if (aCachePageElements[INFO_DIV])
	{
		aCachePageElements[INFO_DIV].style.visibility = 'hidden';
	}
}

function CreateChild(oParentNode,sTag)
{
	var oNode = document.createElement(sTag);
	oParentNode.appendChild(oNode);
	return oNode;
}

function NewAttachClick()
{
	aCachePageElements[COMPOSE_MESSAGE_ATTACHMENTS_FORM].submit();
}

function BuildScreen(iScreen)
{
	var my_div = CreateChild(aCachePageElements[COMMON_CONTAINER],'div');
	my_div.align = 'center';
	aScreens[iScreen] = my_div;
	if (aToolbar[iScreen])
	{
		var toolbar = BuildToolbar(iScreen,0);
		aCachePageElements[TOOLBAR_TABLES][iScreen] = [];
		aCachePageElements[TOOLBAR_TABLES][iScreen][0] = toolbar;
		my_div.appendChild(toolbar);
	}
	if (aToolbar[iScreen])
	{
		BuildDropDowns(iScreen,0);
	}
	switch (iScreen)
	{
		// Screen 'Messages List'
		case SCREEN_MESSAGES_LIST:
			var main_container = CreateChild(my_div,'table');
			main_container.className = 'wm_messages';
			var main_container_tr = main_container.insertRow(-1);
			var messages_td = main_container_tr.insertCell(-1);
			messages_td.height = '100%';
			messages_td.vAlign = 'top';
			messages_td.id = 'messages_td';
			var div_messages = CreateChild(messages_td,'div');
			div_messages.align = 'center';

			var table_messages = CreateChild(div_messages,'table');
			table_messages.className = 'wm_list';
			aCachePageElements[MESSAGES_TABLE] = table_messages;

			var ttr = table_messages.insertRow(-1);
			for (var j = 0; j < aMessagesListHeaders[iScreen].length; j++)
			{
				var ttd = ttr.insertCell(-1);
				ttd.className = 'wm_list_left_header';
				if (aMessagesListHeaders[iScreen][j].length > 0)
				{
					var rr = aMessagesListHeadersWidth[iScreen][j];
					ttd.width = rr;
				}
				ttd.innerHTML = aMessagesListHeaders[iScreen][j];
			}
			aCachePageElements[CHECK_ALL_CHECKBOX] = ttr.cells[1].childNodes[0];

			var empty_div = CreateChild(div_messages,'div');
			empty_div.align = 'center';
			var empty_folder = CreateChild(empty_div,'table');
			aCachePageElements[EMPTY_FOLDER] = empty_folder;
			empty_folder.width = '200px';
			empty_folder.height = '40px';
			empty_folder.className = 'wm_info';
			empty_folder.style.display = 'none';
			var tr = empty_folder.insertRow(-1);
			var td = tr.insertCell(-1);
			td.align = 'center';
			td.className = 'wm_info_id';
			td.innerHTML = strEmptyMailbox;

			// LowToolbar
			var table_lowtoolbar = CreateChild(div_messages,'table');
			table_lowtoolbar.className = 'wm_list';
			var tr_lowtoolbar = table_lowtoolbar.insertRow(-1);
			var td_lowtoolbar = tr_lowtoolbar.insertCell(-1);
			td_lowtoolbar.className = 'wm_lowtoolbar';

			var span_msg_text = CreateChild(td_lowtoolbar,'span');
			span_msg_text.className = 'wm_lowtoolbar_item';
			var span_pages_text = CreateChild(td_lowtoolbar,'span');
			span_pages_text.className = 'wm_lowtoolbar_headers';

			aCachePageElements[LOWTOOLBAR_SPAN] = span_msg_text;
			aCachePageElements[LOWTOOLBAR_SPAN2] = span_pages_text;
			break;

		// Screen 'View Message'
		case SCREEN_VIEW_MESSAGE:
			var main_container = CreateChild(my_div,'table');
			main_container.className = 'wm_message';
			var main_container_tr = main_container.insertRow(-1);
			var main_container_td2 = main_container_tr.insertCell(-1);
			var headers = [strFrom,strTo,strCC,strDate,strSubject];
			for (var i = 0; i < headers.length; i++)
			{
				var tr = main_container.insertRow(-1);
				var td1 = tr.insertCell(-1);
				td1.className = 'wm_view_message_data';
				td1.width = '15%';
				td1.innerHTML = headers[i] + ':';
				var td2 = tr.insertCell(-1);
				var current_cache_element;
				switch (i)
				{
					case 0:
						current_cache_element = VIEW_MESSAGE_FROM;
					break;
					case 1:
						current_cache_element = VIEW_MESSAGE_TO;
					break;
					case 2:
						current_cache_element = VIEW_MESSAGE_CC;
					break;
					case 3:
						current_cache_element = VIEW_MESSAGE_DATE;
					break;
					case 4:
						current_cache_element = VIEW_MESSAGE_SUBJECT;
					break;
				}
				aCachePageElements[current_cache_element] = td2;
				td2.className = 'wm_message_value';
				if (i != 0)
				{
					td2.colSpan = 2;
				} else {
					//td2.width = '68%';
				}
				if (i == 0)
				{
					var td3 = tr.insertCell(-1);
					td3.className = 'wm_message_importance';
					td3.innerHTML = strNormalImportance;
					aCachePageElements[VIEW_MESSSAGE_IMPORTANCE] = td3;
				}
			}
			var tr = main_container.insertRow(-1);
			var td = tr.insertCell(-1);
			td.className = 'wm_message_body_';
			td.colSpan = 3;
			var tt = CreateChild(td,'table');
			tt.width = '100%';
			var tt_tr = tt.insertRow(-1);
			var tt_td = tt_tr.insertCell(-1);
			tt_td.className = 'wm_message_body';
			// headers container
			var headers_table = CreateChild(tt_td,'table');
			aCachePageElements[VIEW_MESSAGE_HEADERS] = headers_table;
			with (headers_table)
			{
				width = '100%';
				cellPadding = '6px;'
				border = 0;
				style.display = 'none';
			}
			var h_tr = headers_table.insertRow(-1);
			var h_td = h_tr.insertCell(-1);
			h_td.className = 'wm_message_rfc822';

			// body container
			var tt_div = CreateChild(tt_td,'div');
			aCachePageElements[VIEW_MESSAGE_TEXT] = tt_div;

			var html_plain_switcher_container_tr = main_container.insertRow(-1);
			var html_plain_switcher_container_td2 = html_plain_switcher_container_tr.insertCell(-1);
			html_plain_switcher_container_tr.className = 'wm_lowtoolbar';
			html_plain_switcher_container_td2.colSpan = 3;

			var span_plain_text = CreateChild(html_plain_switcher_container_td2, 'span');
			span_plain_text.className = 'wm_lowtoolbar_item';
			var span_html = CreateChild(html_plain_switcher_container_td2, 'span');
			span_html.className = 'wm_lowtoolbar_item_selected';
			var span_headers = CreateChild(html_plain_switcher_container_td2, 'span');
			span_headers.className = 'wm_lowtoolbar_headers';

			aCachePageElements[VIEW_MESSAGES_HTML_SWITCHER] = span_html;
			aCachePageElements[VIEW_MESSAGES_PLAIN_SWITCHER] = span_plain_text;

			var a = CreateChild(span_headers,'A');
			aCachePageElements[VIEW_MESSAGE_A_SHOW_HIDE_HEADERS] = a;
			a.href = '';
			a.className = 'wm_reg';
			a.onclick = ViewMessage_FullHeaders;
			a.appendChild(document.createTextNode(strAllHeader));

			// attaches table
			var tr_attach = main_container.insertRow(-1);
			var td_attach = tr_attach.insertCell(-1);
			td_attach.colSpan = 3;
			var attach_container = CreateChild(td_attach,'table');
			aCachePageElements[VIEW_MESSAGE_ATTACH_TABLE] = attach_container;
			attach_container.className = 'wm_attach';
		break;

		// Screen 'Compose Message'
		case SCREEN_COMPOSE_MESSAGE:
			var m_div = CreateChild(my_div,'div');
			m_div.className = 'wm_new_message';
			var m_table = CreateChild(m_div,'table');
			m_table.className = 'wm_message';

			aCachePageElements[COMPOSE_MESSAGE_TABLE] = m_table;

			var list = [strFrom,strTo,strCC,strBCC,strSubject];
			var listc = [COMPOSE_MESSAGE_FROM,COMPOSE_MESSAGE_TO,COMPOSE_MESSAGE_CC,COMPOSE_MESSAGE_BCC,COMPOSE_MESSAGE_SUBJECT];
			for (var i = 0; i < list.length; i++)
			{
				var mm_tr = m_table.insertRow(-1);
				var mm_td1 = mm_tr.insertCell(-1);
				mm_td1.className = 'wm_new_message_data';
				mm_td1.width = '10px';
				mm_td1.innerHTML = list[i] + ':';
				var mm_td2 = mm_tr.insertCell(-1);
				mm_td2.className = 'wm_message_value';
				mm_td2.colSpan = 2;
				mm_td2.width = '100%';
				var textfield = CreateChild(mm_td2,'input');
				textfield.type = 'text';
				textfield.className = 'wm_input';
				textfield.size = '93';
				textfield.tabIndex = new Number(i + 1);
				textfield.maxLength = 2000;
				aCachePageElements[listc[i]] = textfield;
			}

			var mm_tr = m_table.insertRow(-1);
			var mm_td1 = mm_tr.insertCell(-1);
			mm_td1.className = 'wm_new_message_data';
			mm_td1.innerHTML = strMessage;
			mm_td1.vAlign = 'top';
			var mm_td2 = mm_tr.insertCell(-1);
			mm_td2.className = 'wm_message_value';
			mm_td2.colSpan = 2;
			mm_td2.innerHTML += '<textarea tabindex="7" rows="16" cols="94" name="message" id="pagecontent" type="text" automaticMessages="true" class="wm_input"></textarea>';
			aCachePageElements[COMPOSE_MESSAGE_TEXT] = document.getElementById('pagecontent');

			// attachments
			var a_tr = m_table.insertRow(-1);
			var a_td = a_tr.insertCell(-1);
			a_td.className = 'wm_message_value';
			a_td.align = 'left';
			a_td.colSpan = 2;

			aCachePageElements[COMPOSE_MESSAGE_ATTACHMENTS_TABLE] = m_table;

			var att_table = CreateChild(m_div,'table');
			att_table.className = 'wm_message';
			var a_tr2 = att_table.insertRow(-1);
			var a_td2 = a_tr2.insertCell(-1);
			a_td2.className = 'wm_message_value';
			a_td2.align = 'left';

			var frm = document.getElementById('attach_form');
			aCachePageElements[COMPOSE_MESSAGE_ATTACHMENTS_FORM] = frm;
			frm.style.display = '';
			var div = document.getElementById('attach_div');
			aCachePageElements[COMPOSE_MESSAGE_ATTACHMENTS_DIV] = div;
			a_td2.appendChild(frm);
		break;

		case SCREEN_REDIRECT_MESSAGE:
			var main_container = CreateChild(my_div,'div');
			main_container.align = 'center';

			var main_container = CreateChild(main_container,'table');
			aCachePageElements[REDIRECT_MESSAGE_TABLE] = main_container;
			main_container.className = 'wm_dialog';
			var m_tr = main_container.insertRow(-1);
			var m_td_header = m_tr.insertCell(-1);
			m_td_header.className = 'wm_dialog_redirect_header';
			m_td_header.colSpan = 2;
			m_td_header.innerHTML = strRedirect;

			var m_tr = main_container.insertRow(-1);
			var m_td1 = m_tr.insertCell(-1);
			m_td1.className = 'wm_dialog_redirect_field';
			m_td1.width = 90;
			m_td1.innerHTML = strTo + ':';
			var m_td2 = m_tr.insertCell(-1);
			m_td2.className = 'wm_dialog_edit';
			m_td2.innerHTML = '';
			var textfield = CreateChild(m_td2,'input');
			aCachePageElements[REDIRECT_MESSAGE_TO] = textfield;
			textfield.className = 'wm_input';
			textfield.size = '90';

			var m_tr = main_container.insertRow(-1);
			var m_td1 = m_tr.insertCell(-1);
			m_td1.className = 'wm_dialog_redirect_field';
			m_td1.innerHTML = '&nbsp;';
			var m_td2 = m_tr.insertCell(-1);
			m_td2.className = 'wm_dialog_button_edit';
			m_td2.innerHTML = '';

			var a_button = CreateChildWithAttrs(m_td2,'input',[['type','button']]);
			aCachePageElements[REDIRECT_MESSAGE_BUTTON] = a_button;
			a_button.className = 'wm_button';
			a_button.value = strRedirect;
			a_button.onclick = DoRedirectMessage;
		break;
	}
	if (aToolbar[iScreen])
	{
		var toolbar = BuildToolbar(iScreen,1);
		aCachePageElements[TOOLBAR_TABLES][iScreen][1] = toolbar;
		my_div.appendChild(toolbar);
	}
	if (aToolbar[iScreen])
	{
		BuildDropDowns(iScreen,1);
	}
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

function EditMessage(id_message)
{
	if (http_request)
	{
		MakeRequest(processing_script_url + '?action=view&request=message&id_message=' + id_message,EDIT_MESSAGE_HANDLER);
	}
}

function ViewMessage(id_message)
{
	if (http_request)
	{
		MakeRequest(processing_script_url + '?action=get&request=view&id=' + id_message,VIEW_MESSAGE_HANDLER);
	}
}

function FolderList()
{
	if (http_request)
		MakeRequest(processing_script_url + '?action=get&request=folders',FOLDERS_LIST_HANDLER);
}

function CheckAllRows(state, table)
{
	// check all table rows
	var my_rows = aCachePageElements[table].rows;
	for (var i=messages_list_header_row_index + 1; i<my_rows.length; i++)
	{
		if (state)
		{
			my_rows[i].className = 'wm_list_readitem_selected';
		} else {
			my_rows[i].className = 'wm_list_readitem';
		}
		my_rows[i].cells[1].childNodes[0].checked = state;
	}
}

function ColorizeRow(table_row, state)
{
	if (state)
	{
		table_row.className = 'wm_list_readitem_selected';
	} else {
		table_row.className = 'wm_list_readitem';
	}
}

function ViewMessage_FullHeaders()
{
	var headers_table = aCachePageElements[VIEW_MESSAGE_HEADERS];
	if (headers_table)
	{
		aCurrentState[CURRENT_SHOW_HIDE_HEADERS] = (aCurrentState[CURRENT_SHOW_HIDE_HEADERS] == 1 ? 0 : 1);
		if (aCurrentState[CURRENT_SHOW_HIDE_HEADERS] == 1)
		{
			headers_table.style.display = '';
			aCachePageElements[VIEW_MESSAGE_A_SHOW_HIDE_HEADERS].childNodes[0].nodeValue = strStandardHeader;
		} else {
			headers_table.style.display = 'none';
			aCachePageElements[VIEW_MESSAGE_A_SHOW_HIDE_HEADERS].childNodes[0].nodeValue = strAllHeader;
		}
	}
	return false;
}

function ViewMessage_ChangeViewMode()
{
	var cur = aCurrentState[CURRENT_PLAIN_HTML_VIEW_MODE];
	if (typeof(cur) != 'undefined')
	{
		if (cur == 1)
		{
			// from html to plain
			var tag_name = 'txt_message';
			var new_state = 0;
		} else {
			// from plain to html
			var tag_name = 'html_message';
			var new_state = 1;
		}
		var dict_messages = aCacheData[CACHE_DATA_MESSAGES];
		if (dict_messages)
		{
			var current_message_id = aCurrentState[CURRENT_MESSAGE_ID];
			if (dict_messages.exists(current_message_id))
			{
				var xml_message = dict_messages.getVal(current_message_id);
				if (xml_message)
				{
					var text_node = xml_message.getElementsByTagName(tag_name)[0];
					aCurrentState[CURRENT_PLAIN_HTML_VIEW_MODE] = new_state;
				}
			}
		}

		if (text_node.childNodes[0])
		{
			m_txt_message = text_node.childNodes[0].nodeValue;
		} else {
			m_txt_message = '&nbsp;';
		}
		aCachePageElements[VIEW_MESSAGE_TEXT].innerHTML = m_txt_message;

		var switcherHtml = aCachePageElements[VIEW_MESSAGES_HTML_SWITCHER];
		var switcherPlain = aCachePageElements[VIEW_MESSAGES_PLAIN_SWITCHER];

		if (new_state == 1)
		{
			switcherPlain.className = 'wm_lowtoolbar_item';
			switcherPlain.innerHTML = '<a class="wm_reg" href="" onclick="javascript:ViewMessage_ChangeViewMode(); return false;">'+strPlainText+'</a>';;
			switcherHtml.className = 'wm_lowtoolbar_item_selected';
			switcherHtml.innerHTML = strHTML;
		} else {
			switcherHtml.className = 'wm_lowtoolbar_item';
			switcherHtml.innerHTML = '<a class="wm_reg" href="" onclick="javascript:ViewMessage_ChangeViewMode(); return false;">'+strHTML+'</a>';;
			switcherPlain.className = 'wm_lowtoolbar_item_selected';
			switcherPlain.innerHTML = strPlainText;
		}
	}
}

function RefreshViewMessage()
{
	var xml_message = aCacheData[CACHE_CURRENT_MESSAGE];
	if (xml_message)
	{
		var xml_message_nodes = xml_message.childNodes;
		if (xml_message.getAttribute('html'))
		{
			var is_html = xml_message.getAttribute('html');
			var is_txt = xml_message.getAttribute('txt');
			var switcherHtml = aCachePageElements[VIEW_MESSAGES_HTML_SWITCHER];
			var switcherPlain = aCachePageElements[VIEW_MESSAGES_PLAIN_SWITCHER];
			if (is_html == 1 && is_txt == 1)
			{
				with (switcherPlain)
				{
					className = 'wm_lowtoolbar_item';
					innerHTML = '<a class="wm_reg" href="" onclick="javascript:ViewMessage_ChangeViewMode(); return false;">'+strPlainText+'</a>';
				}
				switcherHtml.className = 'wm_lowtoolbar_item_selected';
				switcherHtml.innerHTML = strHTML;

				aCurrentState[CURRENT_PLAIN_HTML_VIEW_MODE] = 1;
			} else {
				aCurrentState[CURRENT_PLAIN_HTML_VIEW_MODE] = 0;
				switcherHtml.innerHTML = '';
				switcherPlain.innerHTML = '';
			}
		}
		// Importance field
		if (xml_message.getAttribute('importance'))
		{
			var importance = new Number(xml_message.getAttribute('importance'));
			var importance_td = aCachePageElements[VIEW_MESSSAGE_IMPORTANCE];
			if (importance == 1)
				importance_td.innerHTML = strHighImportance;
			if (importance == 3)
				importance_td.innerHTML = strNormalImportance;
			if (importance == 5)
				importance_td.innerHTML = strLowImportance;
		}

		var down_arrow = VIEW_MESSAGE_DOWN_ARROW;
		var up_arrow = VIEW_MESSAGE_UP_ARROW;

		var az = [0,1];
		var message_id = new Number(xml_message.getAttribute('id'));
		var messages_count = new Number(xml_message.getAttribute('count'));

		if (message_id > 1)
		{
			aCurrentState[CURRENT_DOWN_MESSAGE] = message_id - 1;
			for (var k = 0; k < az.length; k++)
			{
				var y = az[k];
				aCachePageElements[down_arrow][y].onclick = DownMessageClick;
				aCachePageElements[down_arrow][y].onmouseover = ToolbarMouseOver;
				aCachePageElements[down_arrow][y].omouseout = ToolbarMouseOut;
				aCachePageElements[down_arrow][y].childNodes[0].src = 'skins/' + aSettings[SETTING_SKIN] + '/menu/message_down.gif';
			}
		} else {
			aCurrentState[CURRENT_DOWN_MESSAGE] = 0;
			for (var k = 0; k < az.length; k++)
			{
				var y = az[k];
				aCachePageElements[down_arrow][y].onclick = '';
				aCachePageElements[down_arrow][y].onmouseover = '';
				aCachePageElements[down_arrow][y].omouseout = '';
				aCachePageElements[down_arrow][y].childNodes[0].src = 'skins/' + aSettings[SETTING_SKIN] + '/menu/message_down_inactive.gif';
			}
		}
		if (message_id < messages_count)
		{
			aCurrentState[CURRENT_UP_MESSAGE] = message_id + 1;
			for (var k = 0; k < az.length; k++)
			{
				var y = az[k];
				aCachePageElements[up_arrow][y].onclick = UpMessageClick;
				aCachePageElements[up_arrow][y].onmouseover = ToolbarMouseOver;
				aCachePageElements[up_arrow][y].omouseout = ToolbarMouseOut;
				aCachePageElements[up_arrow][y].childNodes[0].src = 'skins/' + aSettings[SETTING_SKIN] + '/menu/message_up.gif';
			}
		} else {
			aCurrentState[CURRENT_UP_MESSAGE] = 0;
			for (var k = 0; k < az.length; k++)
			{
				var y = az[k];
				aCachePageElements[up_arrow][y].onclick = '';
				aCachePageElements[up_arrow][y].onmouseover = '';
				aCachePageElements[up_arrow][y].omouseout = '';
				aCachePageElements[up_arrow][y].childNodes[0].src = 'skins/' + aSettings[SETTING_SKIN] + '/menu/message_up_inactive.gif';
			}
		}

		if (xml_message.getAttribute('number_attachments'))
		{
			var number_attachments = xml_message.getAttribute('number_attachments');
			if (number_attachments > 0)
			{
				var xml_attachments = xml_message.getElementsByTagName('attachment');
				if (xml_attachments)
				{
					RefreshViewMessageAttachments(xml_attachments);
				}
			} else {
				aCachePageElements[VIEW_MESSAGE_ATTACH_TABLE].style.display = 'none';
			}
		}

		for (var i = 0; i < xml_message_nodes.length; i++){
			var t_node = xml_message_nodes[i];
			var m_from = '', m_to = '', m_cc = '', m_subject = '', m_date = '', m_headers = '';
			var prop;
			switch(t_node.tagName){
				case "headers":
					if(t_node.childNodes[0]){
						m_headers = t_node.childNodes[0].nodeValue;
					} else {
						m_headers = '&nbsp;';
					}
					aCachePageElements[VIEW_MESSAGE_HEADERS].rows[0].cells[0].innerHTML = m_headers;
					break;
				case "from":
					if(t_node.childNodes[0]){
						m_from = t_node.childNodes[0].nodeValue;
					} else {
						m_from = '&nbsp;';
					}
					aCachePageElements[VIEW_MESSAGE_FROM].innerHTML = m_from;
				break;
				
				case "to":
					if(t_node.childNodes[0]){
						m_to = t_node.childNodes[0].nodeValue;
					} else {
						m_to = '&nbsp;';
					}
					aCachePageElements[VIEW_MESSAGE_TO].innerHTML = m_to;
				break;
				
				case "cc":
					if(t_node.childNodes[0]){
						m_cc = trim(t_node.childNodes[0].nodeValue);
					}
					if(m_cc.length > 0){
						aCachePageElements[VIEW_MESSAGE_CC].innerHTML = m_cc;
						var cc_tr = aCachePageElements[VIEW_MESSAGE_CC].parentNode;
						cc_tr.style.display = '';
					} else {
						var cc_tr = aCachePageElements[VIEW_MESSAGE_CC].parentNode;
						cc_tr.style.display = 'none';
					}
				break;
				
				case "date":
					if(t_node.childNodes[0]){
						m_date = t_node.childNodes[0].nodeValue;
					} else {
						m_date = '&nbsp;';
					}
					aCachePageElements[VIEW_MESSAGE_DATE].innerHTML = m_date;
				break;
				
				case "subject":
					if(t_node.childNodes[0]){
						m_subject = t_node.childNodes[0].nodeValue;
					} else {
						m_subject = '&nbsp;';
					}
					aCachePageElements[VIEW_MESSAGE_SUBJECT].innerHTML = m_subject;
				break;
				
				case "txt_message":
					if(is_html == 0){
						if(t_node.childNodes[0]){
							m_txt_message = t_node.childNodes[0].nodeValue;
						} else {
							m_txt_message = '';
						}
						aCachePageElements[VIEW_MESSAGE_TEXT].innerHTML = m_txt_message;
					}
				break;
				
				case "html_message":
					if(t_node.childNodes[0]){
						m_html_message = t_node.childNodes[0].nodeValue;
					} else {
						m_html_message = '&nbsp;';
					}
					aCachePageElements[VIEW_MESSAGE_TEXT].innerHTML = m_html_message;
				break;
			}
		}
	}
}

function RefreshViewMessageAttachments(xml_attachments)
{
	if (xml_attachments)
	{
		var attach_container = aCachePageElements[VIEW_MESSAGE_ATTACH_TABLE];
		if (xml_attachments.length == 0)
		{
			attach_container.style.display = 'none';
		} else {
			attach_container.style.display = '';
			for (var i = 0; i < xml_attachments.length; i++)
			{
				if (attach_container.rows[i])
				{
					var ftr = attach_container.rows[i];
				} else {
					var ftr = attach_container.insertRow(-1);
				}
				if (ftr)
				{
					var xml_attachment = xml_attachments[i];
					if (ftr.cells[0])
					{
						var ftd1 = ftr.cells[0];
					} else {
						var ftd1 = ftr.insertCell(-1);
					}
					ftd1.className = 'wm_attach_data';
					ftd1.innerHTML = strFile + new Number(i + 1) + ':';
					if (ftr.cells[1])
					{
						var ftd2 = ftr.cells[1];
					} else {
						var ftd2 = ftr.insertCell(-1);
					}
					if (ftr.cells[2])
					{
						var ftd3 = ftr.cells[2];
					} else {
						var ftd3 = ftr.insertCell(-1);
					}
					ftd2.className = 'wm_attach_value_icon';
					ftd3.className = 'wm_attach_value_text';
					var attach_filename = '';
					var attach_size = '';
					attach_filename = xml_attachment.getAttribute('filename');
					attach_size = xml_attachment.getAttribute('size');
					var icon_name;
					var ext = '';
					var dotpos = attach_filename.lastIndexOf('.');
					if (dotpos > -1)
					{
						ext = attach_filename.substr(dotpos + 1).toLowerCase();
					}
					ftd2.innerHTML = '<img src="images/icons/' + GetIconNameByExtension(ext) + '" width="32" height="32" border="0">';
					ftd3.innerHTML = attach_filename + '&nbsp;' + GetStrSize(attach_size);
					if (ext == 'gif' || ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'bmp')
					{
						var attachment_view = xml_attachment.getElementsByTagName('view');
						if (attachment_view.length > 0)
						{
							ftd3.innerHTML += '&nbsp;&nbsp;<a target="_blank" class="wm_reg" href="' + attachment_view[0].childNodes[0].nodeValue + '">'+strDownloadView+'</a>';
						}
					}
					var attachment_download = xml_attachment.getElementsByTagName('download');
					if (attachment_download)
					{
						ftd3.innerHTML += '&nbsp;&nbsp;<a class="wm_reg" href="' + attachment_download[0].childNodes[0].nodeValue + '">'+strDownload+'</a>';
					}
				}
			}
			if (attach_container.rows.length > xml_attachments.length)
			{
				while (attach_container.rows.length > xml_attachments.length)
				{
					attach_container.deleteRow(attach_container.rows.length - 1);
				}
			}
		}
	}
}

function GetIconNameByExtension(str_extension)
{
	switch (str_extension)
	{
		case 'txt':
			return 'text_plain.gif';
		break;
		case 'bat':
			return 'executable.gif';
		break;
		case 'exe':
			return 'executable.gif';
		break;
		case 'com':
			return 'executable.gif';
		break;
		case 'asp':
			return 'application_asp.gif';
		break;
		case 'asa':
			return 'application_asp.gif';
		break;
		case 'inc':
			return 'application_asp.gif';
		break;
		case 'css':
			return 'application_css.gif'
		break;
		case 'doc':
			return 'application_doc.gif'
		break;
		case 'html':
			return 'application_html.gif'
		break;
		case 'shtml':
			return 'application_html.gif'
		break;
		case 'phtml':
			return 'application_html.gif'
		break;
		case 'htm':
			return 'application_html.gif'
		break;
		case 'pdf':
			return 'application_pdf.gif'
		break;
		case 'xls':
			return 'application_xls.gif';
		break;
		case 'bmp':
			return 'image_bmp.gif'
		break;
		case 'gif':
			return 'image_gif.gif';
		break;
		case 'jpg':
			return 'image_jpeg.gif';
		break;
		case 'jpeg':
			return 'image_jpeg.gif';
		break;
		case 'psd':
			return 'image_psd.gif';
		break;
		case 'tiff':
			return 'image_tiff.gif';
		break;
		case 'tif':
			return 'image_tiff.gif';
		break;
		default:
			return 'attach.gif'
		break;
	}
}

function SetFolder(page)
{
	// we need to refresh messages list
	if (aCurrentState[CURRENT_SCREEN] != SCREEN_MESSAGES_LIST)
	{
		ShowScreen(SCREEN_MESSAGES_LIST);
	} else {
		if (http_request)
		{
			changeLocation('r|%|' + SCREEN_MESSAGES_LIST + '|%|' + page);
		}
	}
}

function ProcessingMessageRow(table_row,xml_message,folder_type,int_counter)
{
	var msg_id = xml_message.getAttribute('id');

	table_row.className = 'wm_list_readitem';

	// number
	table_row.cells[0].innerHTML = '&nbsp;' + int_counter;

	// checkbox
	table_row.cells[1].innerHTML = '<input type="checkbox" onclick="javascript:ColorizeRow(aCachePageElements[MESSAGES_TABLE].rows[' + table_row.rowIndex +
	'], this.checked);" name="cb" value="' + msg_id + '">';

	// attachments
	if (xml_message.getAttribute('has_attachments') == 1)
	{
		table_row.cells[2].innerHTML = '<img src="images/attachment.gif">';
	} else {
		table_row.cells[2].innerHTML = '&nbsp;';
	}

	// importance
	var im = new Number(xml_message.getAttribute('importance'));
	if ((im == 1 || im == 3 || im == 5) == false) { im = 3; }
	if (im == 1) { table_row.cells[3].innerHTML = '<img src="skins/' + aSettings[SETTING_SKIN] + '/menu/priority_high.gif" border="0">'; }
	if (im == 3) { table_row.cells[3].innerHTML = '&nbsp;'; }
	if (im == 5) { table_row.cells[3].innerHTML = '<img src="skins/' + aSettings[SETTING_SKIN] + '/menu/priority_low.gif" border="0">'; }

	// from/to
	if(xml_message.getElementsByTagName('from')[0].childNodes[0])
	{
		table_row.cells[4].innerHTML = '<div class="wm_list_subject"><a class="wm_list_item_link" href="" onclick="javascript:aCachePageElements[MESSAGES_TABLE].rows[' +
		table_row.rowIndex + '].className = \'wm_list_readitem\'; changeLocation(\'|%|' + SCREEN_VIEW_MESSAGE + '|%|' + msg_id + '\'); return false;">' +
//		ViewMessage(\'' + msg_id + '\'); return false;">' +
		xml_message.getElementsByTagName('from')[0].childNodes[0].nodeValue + '</a></div>';
	} else {
		table_row.cells[4].innerHTML = '&nbsp;';
	}

	//date
	if(xml_message.getElementsByTagName('date')[0].childNodes[0]){
		table_row.cells[5].innerHTML = xml_message.getElementsByTagName('date')[0].childNodes[0].nodeValue;
	} else {
		table_row.cells[5].innerHTML = '&nbsp;';
	}

	//size
	if(xml_message.getElementsByTagName('size')[0].childNodes[0]){
		table_row.cells[6].innerHTML = GetStrSize(xml_message.getElementsByTagName('size')[0].childNodes[0].nodeValue);
	} else {
		table_row.cells[6].innerHTML = '&nbsp;';
	}

	// subject
	if(xml_message.getElementsByTagName('subject')[0].childNodes[0])
	{
		table_row.cells[7].innerHTML = '<div class="wm_list_subject"><a class="wm_list_item_link" href="" onclick="javascript:aCachePageElements[MESSAGES_TABLE].rows[' +
		table_row.rowIndex + '].className = \'wm_list_readitem\';  changeLocation(\'|%|' + SCREEN_VIEW_MESSAGE + '|%|' + msg_id + '\'); return false;">' +
		//</a>ViewMessage(\'' + msg_id + '\'); return false;">' +
		xml_message.getElementsByTagName('subject')[0].childNodes[0].nodeValue + '</a></div>';
	} else {
		table_row.cells[7].innerHTML = '&nbsp;';
	}
}

function RefreshMessagesList(messages_table, xml_messages)
{
	var num_messages = xml_messages.length;
	if (num_messages > 0)
	{
			messages_table.rows[0].style.display = '';
	} else {
			messages_table.rows[0].style.display = 'none';
	}

	for (var i = 0; i < num_messages; i++)
	{
		var msg = xml_messages[i];
		var curRowIndex = Number(messages_list_header_row_index + 1 + i);
		if (messages_table.rows[curRowIndex])
		{
			var mtr = messages_table.rows[curRowIndex];
		} else {
			var mtr = messages_table.insertRow(-1);
		}
		while (mtr.cells.length > 0)
		{
			mtr.deleteCell(mtr.cells.length - 1);
		}
		for (var j = 0; j < aMessagesListHeaders[aCurrentState[CURRENT_SCREEN]].length; j++)
		{
			var ttd = mtr.insertCell(-1);
			ttd.className = 'wm_list_left_cell';
		}
		ProcessingMessageRow(mtr,msg,0,Number(aCurrentState[CURRENT_PAGE] * aSettings[SETTING_MAILS_PER_PAGE] + i + 1));
	}

	var lastRowIndex = num_messages + messages_list_header_row_index + 1;
	if (messages_table.rows.length > lastRowIndex)
	{
		while (messages_table.rows.length > lastRowIndex)
		{
			messages_table.deleteRow(messages_table.rows.length - 1)
		}
	}

	var empty_folder = aCachePageElements[EMPTY_FOLDER];
	if (num_messages == 0)
	{
		empty_folder.style.display = 'block';
	} else {
		empty_folder.style.display = 'none';
	}
}

function GetStrSize(size)
{
	var a = Math.round(size / 1024);
	if (a == 0)
	{
		a = (size / 1024);
		if (a < (1/10) && a > 0)
		{
			a = 1/10;
		}
		a = a.toString();
		a = a.substr(0, 3);
	}
	return a + strSizeK;
}

function MessagesListRefresh()
{
	var messages_td = document.getElementById('messages_td');
}

function FindPositionY(obj)
{
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	} else if (obj.y)
		curtop += obj.y;
	return curtop;
}

function FindPositionX(obj)
{
	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	} else if (obj.x)
		curleft += obj.x;
	return curleft;
}

function BuildDropDowns(iScreen,iWhere)
{
	var toolbar_items = aToolbar[iScreen];
	if (iScreen == SCREEN_MESSAGES_LIST)
	{
		aCachePageElements[DROP_DOWN_MENUS][iWhere] = [];
	}
	for (var i = 0; i < toolbar_items.length; i++)
	{
		var t_i = toolbar_items[i];
		if (typeof(t_i) == 'object')
		{
			var table_drop_down = CreateChild(aScreens[SCREEN_MESSAGES_LIST], 'TABLE');
			aCachePageElements[DROP_DOWN_MENUS][iWhere][t_i.identifier] = table_drop_down;
			table_drop_down.cellSpacing = 0;
			with (table_drop_down.style)
			{
				position = 'absolute';
				visibility = 'hidden';
				backgroundColor = '#E9F2F8';
				border = '1px solid #7FB7E1';
				fontFamily = 'Tahoma';
				fontSize = '11px';
				
			}
			if (t_i.mode == 'first_clickable') { var f = 1; }
			else { var f = 0; }
			for (var j = f; j < toolbar_items[i].items.length; j++)
			{
				var trt = table_drop_down.insertRow(-1);
				trt.onmouseover = DropDownMenu_ListItemOver;
				trt.onmouseout = DropDownMenu_ListItemOver;
				trt.onclick = aToolbarClicks[t_i.items[j]];
				var t_icon = aToolbarIcons[t_i.items[j]];
				var t_text = aToolbarTitles[t_i.items[j]];
				var td_1 = trt.insertCell(-1);
				td_1.className = 'wm_calendar_listmenu_item';
				td_1.innerHTML = '&nbsp; <img class="wm_icon" src="skins/' + aSettings[SETTING_SKIN] + '/' + t_icon + '">&nbsp;';
				var td_2 = trt.insertCell(-1);
				td_2.className = 'wm_calendar_listmenu_item';
				td_2.innerHTML = t_text + '&nbsp; &nbsp; ';
			}
		}
	}
}

function BuildToolbar(iScreen,iWhere)
{
	var toolbar = document.createElement('TABLE');
	toolbar.className = 'wm_toolbar';
	toolbar.cellSpacing = 1;
	var toolbar_tr = toolbar.insertRow(-1);
	var toolbar_items = aToolbar[iScreen];
	var toolbar_td = toolbar_tr.insertCell(-1);

	for(var i = 0; i < toolbar_items.length; i++)
	{
		var span_item = CreateChild(toolbar_td, 'span');
		var t_text = aToolbarTitles[toolbar_items[i]];
		var t_icon = aToolbarIcons[toolbar_items[i]];
		var t_click_handler = aToolbarClicks[toolbar_items[i]];
		if (t_text.length > 0 && show_text_labels == 1)
		{
			span_item.innerHTML = '<img class="wm_icon" src="skins/' + aSettings[SETTING_SKIN] + '/' + t_icon + '">&nbsp;' + t_text + '';
		} else {
			span_item.innerHTML = '<img class="wm_icon" src="skins/' + aSettings[SETTING_SKIN] + '/' + t_icon + '">';
		}
		span_item.className = 'wm_toolbar_item';
		span_item.onclick = t_click_handler;
		span_item.onmouseover = ToolbarMouseOver;
		span_item.onmouseout = ToolbarMouseOut;
		if(toolbar_items[i] == 18){
			if(iScreen == SCREEN_VIEW_MESSAGE){
				aCachePageElements[VIEW_MESSAGE_UP_ARROW][iWhere] = span_item;
			}
		}
		if(toolbar_items[i] == 19){
			if(iScreen == SCREEN_VIEW_MESSAGE){
				aCachePageElements[VIEW_MESSAGE_DOWN_ARROW][iWhere] = span_item;
			}
		}
	}

	//  infobar ("You are using...")
	if(iScreen == SCREEN_MESSAGES_LIST && iWhere == 0 && enable_mailbox_size_limit == 1 && mailbox_size_limit > 0)
	{
		var span_item = CreateChild(toolbar_td, 'span');
		span_item.className = 'wm_email_space_indicator';

		var infobar_table = CreateChild(span_item,'TABLE');
		aCachePageElements[INFOBAR_TABLE] = infobar_table;
		var infobar_text = CreateChild(toolbar_td,'span');
		aCachePageElements[INFOBAR_TEXT] = infobar_text;

		infobar_table.className = 'wm_space_amount';
		var infobar_tr = infobar_table.insertRow(-1);
		var infobar_td1 = infobar_tr.insertCell(-1);
		infobar_td1.width = '1';
		infobar_td1.className = 'wm_space_used';
		var infobar_td2 = infobar_tr.insertCell(-1);
	}
	return toolbar;
}

function ShowScreen(iScreen)
{
	if (typeof(aScreens[iScreen]) == 'undefined')
	{
		BuildScreen(iScreen);
	}
	HideAllScreens();
	aScreens[iScreen].style.display = '';
	aCurrentState[CURRENT_SCREEN] = iScreen;
}

function HideAllScreens()
{
	for (var i = 0; i < aScreens.length; i++)
	{
		if (typeof(aScreens[i]) != 'undefined')
		{
			aScreens[i].style.display = 'none';
		}
	}
}

function HideScreen(iScreen)
{
	aScreens[iScreen].style.left = '-10000px';
}

function MakeRequest(url, handler, ar_post_data){
	// ar_post_data - array with variables for POST request
	ShowLoadingBar();

	// i was not found better place in code to uncheck the 'Check All' checkbox
	if (aCachePageElements[CHECK_ALL_CHECKBOX])
	{
		aCachePageElements[CHECK_ALL_CHECKBOX].checked = false;
	}

	var post_data = null;
	var request_method = 'GET';
	if (ar_post_data)
	{
		request_method = 'POST';
		if (typeof(ar_post_data) == 'object')
		{
			if (ar_post_data.length > 0)
			{
				var ar_post_data_new = new Array();
				for (var i = 0; i < ar_post_data.length; i++)
				{
					var t = ar_post_data[i];
					var t_str = '';
					if (typeof(t) == 'object')
					{
						if (t.length == 2)
						{
							if (typeof(t[1]) != 'undefined')
							{
								var t_str = t[0] + '=' + encodeURIComponent(t[1]);
							}
						}
					}
					if (t_str.length > 3)
					{
						ar_post_data_new.push(t_str);
					}
				}
				post_data = ar_post_data_new.join('&');
			}
		}
	}
	if (request_method == 'GET')
	{
		var rnd = new Date().getTime();
		url = url + '&rnd=' + rnd;
	}
	var http_request = new obj_http_request();

	http_request.onreadystatechange = function()
	{
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
						var webmail_redirect = webmail_data.getElementsByTagName('webmail_redirect')[0];
						if (webmail_redirect)
						{
							var url = webmail_redirect.getAttribute('url');
							if (trim(url).length > 0)
							{
								location.href = url;
							}
						} else {
							var webmail_error = webmail_data.getElementsByTagName('webmail_error')[0];
							if (webmail_error)
							{
								var err_desc = webmail_error.getAttribute('description');
								if (err_desc)
								{
									if (err_desc.length > 0)
									{
										ShowErrorBar(err_desc);
									} else {
										ShowErrorBar(strEmptyError);
									}
								} else {
									ShowErrorBar(strWithoutDescError);
								}
							}
						}
						eval(aCacheHandlers[handler](webmail_data));
					} else {
						if (webmail_data.tagName == 'webmail_empty')
						{
							document.location.href = default_page;
						} else {
							eval(ShowErrorBar(strTagError));
						}
					}
				} else {
					ShowErrorBar(http_request.responseText);
				}
			} else {
				ShowErrorBar(strRequestError + http_request.status + '\n\n' + http_request.responseText);
			}
			http_request = null;
		}
	}
	http_request.open(request_method, url, true);
	if (request_method == 'POST')
	{
		var ct = 'application/x-www-form-urlencoded';
		http_request.setRequestHeader('Content-Type', ct);
	}
	http_request.send(post_data);
}

function CreateHttpRequestObject()
{
	var http_request = false;
	if (window.XMLHttpRequest) // Mozilla, Safari,...
	{
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType)
		{
    		http_request.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try {
				http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}
	return http_request;
}

function obj_http_request()
{
	return CreateHttpRequestObject();
}

function Dictionary()
{
	this.count = 0;
	this.Obj = new Object();
	this.exists = Dictionary_exists;
	this.add = Dictionary_add;
	this.remove = Dictionary_remove;
	this.removeAll = Dictionary_removeAll;
	this.values = Dictionary_values;
	this.keys = Dictionary_keys;
	this.items = Dictionary_items;
	this.getVal = Dictionary_getVal;
	this.setVal = Dictionary_setVal;
	this.setKey = Dictionary_setKey;
}

function Dictionary_exists(sKey)
{
	return (this.Obj[sKey])?true:false;
}

function Dictionary_add(sKey,aVal)
{
	var K = String(sKey);
	if (this.exists(K)) return false;
	this.Obj[K] = aVal;
	this.count++;
	return true;
}

function Dictionary_remove(sKey)
{
	var K = String(sKey);
	if (!this.exists(K)) return false;
	delete this.Obj[K];
	this.count--;
	return true;
}

function Dictionary_removeAll()
{
	for(var key in this.Obj) delete this.Obj[key];
	this.count = 0;
}

function Dictionary_values()
{
	var Arr = new Array();
	for (var key in this.Obj) Arr[Arr.length] = this.Obj[key];
	return Arr;
}

function Dictionary_keys()
{
	var Arr = new Array();
	for (var key in this.Obj) Arr[Arr.length] = key;
	return Arr;
}

function Dictionary_items()
{
	var Arr = new Array();
	for (var key in this.Obj)
	{
		var A = new Array(key,this.Obj[key]);
		Arr[Arr.length] = A;
	}
	return Arr;
}

function Dictionary_getVal(sKey)
{
	var K = String(sKey);
	return this.Obj[K];
}

function Dictionary_setVal(sKey,aVal)
{
	var K = String(sKey);
	if (this.exists(K))
		this.Obj[K] = aVal;
	else
		this.add(K,aVal);
}

function Dictionary_setKey(sKey,sNewKey)
{
	var K = String(sKey);
	var Nk = String(sNewKey);
	if (this.exists(K))
	{
		if (!this.exists(Nk))
		{
			this.add(Nk,this.getVal(K));
			this.remove(K);
		}
	}
	else if(!this.exists(Nk)) this.add(Nk,null);
}

function tab(title, handler, is_strong)
{
	this.title = title;
	this.handler = handler;
	this.is_strong = is_strong;
}

function IndexOfValue(obj,compareText,compareType) // returns index of the text (cmpTxt) of a select objest (obj)
{
	var i = 0
	for (var li = 0; li < obj.length; li++)
	{
		if (compareType == 'value')
		{
			if (obj.options[li].value == compareText)
			{
				i = li;
			}
		} else {
			if (obj.options[li].text == compareText)
			{
				i = li;
			}
		}
	}
	return i;
}

function RefreshInfoBar(sum_size, max_size)
{
	var infobar_table = aCachePageElements[INFOBAR_TABLE];
	max_size_mb = Math.round(max_size * 10 / 1048576) / 10;
	if (sum_size >= max_size){
		var procent1 = 100;
		var procent2 = 0;
	} else {
		var procent1 = Math.round(sum_size * 100 / max_size);
		var procent2 = 100 - procent1;
	}
	procent = max_size / sum_size;
	procent = Math.floor(procent * 100);
	infobar_table.rows[0].cells[0].className = 'wm_space_used';
	if (procent1 == 0)
		infobar_table.rows[0].cells[0].width = 1;
	else
		infobar_table.rows[0].cells[0].width = procent1;
	infobar_table.rows[0].cells[0].innerHTML = '<img src="images/1x1.gif" />';
	if (procent2 == 0)
		infobar_table.rows[0].cells[1].width = 1;
	else
		infobar_table.rows[0].cells[1].width = procent2;
	infobar_table.rows[0].cells[0].innerHTML = '<img src="images/1x1.gif" />';

	var infobar_text = aCachePageElements[INFOBAR_TEXT];
	infobar_text.className = 'wm_email_space';
	infobar_text.innerHTML = '<nobr>'+strPer1 + procent1 + strPer2 + max_size_mb + strPer3+'</nobr>';
}

function CheckScroll()
{
	window.scrollTo(0,0);
}


