function GetMessageForForward_Handler(root_element)
{
	if (root_element)
	{
		if (root_element.tagName == 'webmail_data')
		{
			var xml_message = root_element.getElementsByTagName('message')[0];
			if (xml_message)
			{
				aCacheData[CACHE_CURRENT_MESSAGE] = xml_message;
				aCurrentState[CURRENT_MESSAGE_ID] = xml_message.getAttribute('id');

				aCacheData[CACHE_COMPOSE_MESSAGE_ATTACHMENTS].removeAll();
				var atts = xml_message.getElementsByTagName('attachment');
				for (var j = 0; j < atts.length; j++)
				{
					aCacheData[CACHE_COMPOSE_MESSAGE_ATTACHMENTS].add(atts[j].getAttribute('id'), new attachment(atts[j].getAttribute('filename_temp'),atts[j].getAttribute('filename'),atts[j].getAttribute('size'),atts[j].getAttribute('type')));
				}

				ComposeMessage(ProcessingMessage(xml_message,2));
			}
		}
	}
	CheckScroll();
}

function GetMessageForReplyAll_Handler(root_element)
{
	if (root_element)
	{
		if (root_element.tagName == 'webmail_data')
		{
			var xml_message = root_element.getElementsByTagName('message')[0];
			if (xml_message)
			{
				aCacheData[CACHE_CURRENT_MESSAGE] = xml_message;
				aCurrentState[CURRENT_MESSAGE_ID] = xml_message.getAttribute('id');
				ComposeMessage(ProcessingMessage(xml_message,1));
			}
		}
	}
	CheckScroll();
}

function GetMessageForReply_Handler(root_element)
{
	if (root_element)
	{
		if (root_element.tagName == 'webmail_data')
		{
			var xml_message = root_element.getElementsByTagName('message')[0];
			if (xml_message)
			{
				aCacheData[CACHE_CURRENT_MESSAGE] = xml_message;
				aCurrentState[CURRENT_MESSAGE_ID] = xml_message.getAttribute('id');
				ComposeMessage(ProcessingMessage(xml_message,0));
			}
		}
	}
	CheckScroll();
}

function ViewMessage_Handler(root_element)
{
	if (root_element)
	{
		if (root_element.nodeName == 'webmail_data')
		{
			var xml_message = root_element.getElementsByTagName('message')[0];
			if (xml_message)
			{
				if (aCurrentState[CURRENT_SCREEN] != SCREEN_VIEW_MESSAGE) { ShowScreen(SCREEN_VIEW_MESSAGE); }

				// save the xml message to cache
				var dict_messages = aCacheData[CACHE_DATA_MESSAGES];
				if (dict_messages)
				{
					if (xml_message.getAttribute('id'))
					{
						var message_id = xml_message.getAttribute('id');
						aCurrentState[CURRENT_MESSAGE_ID] = message_id;
						if (dict_messages.exists(message_id) == false)
						{
							dict_messages.add(message_id,xml_message);
						}
					}
				}
				aCacheData[CACHE_CURRENT_MESSAGE] = xml_message;
				RefreshViewMessage();
/*				var doc = document.getElementById("main_body");
				doc.className = "wm_body_main";*/
			}
		}
	}
	CheckScroll();
}

function SendMessage_Handler(root_element)
{
	ShowScreen(SCREEN_MESSAGES_LIST);
	CheckScroll();
}

function SetFolder_Handler(root_element)
{
	if(root_element)
	{
		if(root_element.nodeName == 'webmail_data')
		{
			var e_messages = root_element.getElementsByTagName('messages')[0];
			if(e_messages)
			{
				var folder_type = e_messages.getAttribute('folder_type');
				if(aCurrentState[CURRENT_SCREEN] != SCREEN_MESSAGES_LIST ){
					ShowScreen(SCREEN_MESSAGES_LIST);
				}
				var xml_messages = root_element.getElementsByTagName('message');

				var mailbox_limit = new Number(e_messages.getAttribute('limit'));
				var inbox_size = new Number(e_messages.getAttribute('inbox_size'));
				if (mailbox_limit && inbox_size)
				{
					if (mailbox_limit > 0 && enable_mailbox_size_limit == 1)
					{
						RefreshInfoBar(inbox_size, mailbox_limit);
					}
				}

				var num_messages = new Number(e_messages.getAttribute('count'));
				if (!num_messages)
					num_messages = 0;

				if (num_messages > 0)
				{
					aCachePageElements[MESSAGES_TABLE].style.display = '';
				} else {
					aCachePageElements[MESSAGES_TABLE].style.display = 'none';
				}

				var page = new Number(e_messages.getAttribute('page')) + 1;
				if (page)
				{
					aCurrentState[CURRENT_PAGE] = page - 1;
				}

				var span_msg_text = aCachePageElements[LOWTOOLBAR_SPAN];
				span_msg_text.innerHTML = num_messages + strMessagesInbox;
				var td_pages = aCachePageElements[LOWTOOLBAR_SPAN2];

				// refresh pages switcher
				if (td_pages)
				{
					var str_pages = '';
					var num_pages = Math.ceil(num_messages / aSettings[SETTING_MAILS_PER_PAGE]);
					if (num_pages > 4)
					{
						var first_page = page - 3;
						if (first_page < 0) first_page = 0;
						var last_page = first_page + 4;
						if (last_page >= num_pages)
						{
							last_page = num_pages-1;
							first_page = last_page - 4;
						}
					} else {
						var first_page = 0;
						var last_page = num_pages-1;
					}
					if (first_page == last_page) {
						td_pages.innerHTML = '';
					} else {
						if (first_page > 0)
							str_pages += ' <a class="wm_reg" href="" onclick="javascript:SetFolder(' + (first_page-1) + '); return false;">&lt;&lt;</a> ';
						var first_msg, last_msg, text;
						for (var i = first_page; i <= last_page; i++)
						{
							first_msg = i * aSettings[SETTING_MAILS_PER_PAGE] + 1;
							last_msg = (i + 1) * aSettings[SETTING_MAILS_PER_PAGE];
							if (last_msg > num_messages)
								last_msg = num_messages;
							text = first_msg + '&#133;' + last_msg;
							if (((page - 1) * aSettings[SETTING_MAILS_PER_PAGE] + 1) == first_msg)
								str_pages += ' <font class="wm_lowtoolbar_page_selected">' + text + '</font> ';
							else
								str_pages += ' <a class="wm_reg" href="" onclick="javascript:SetFolder(' + i + '); return false;">' + text + '</a> ';
						}
						if (num_pages > i)
								str_pages += ' <a class="wm_reg" href="" onclick="javascript:SetFolder(' + i + '); return false;">&gt;&gt;</a> ';
						td_pages.innerHTML = str_pages;
					}
				}
				RefreshMessagesList(aCachePageElements[MESSAGES_TABLE], xml_messages);
			}
		}
	}
	CheckScroll();
}
