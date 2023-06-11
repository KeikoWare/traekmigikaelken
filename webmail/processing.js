var aSections = new Array('Common');

var aSettings = new Array();
var SETTING_MAILS_PER_PAGE = 1;
var SETTING_SKIN = 2;
var SETTING_LANGUAGE = 3;
var SETTING_CHARSET = 4;
var SETTING_TIME_OFFSET = 5;
aSettings[SETTING_MAILS_PER_PAGE] = mails_per_page;
aSettings[SETTING_SKIN] = global_skin;

// identifiers for functions-handlers
var aCacheHandlers = new Array();
var SET_FOLDER_HANDLER = 1;
var DELETE_MESSAGES_HANDLER = 2;
var VIEW_MESSAGE_HANDLER = 3;
var SEND_MESSAGE_HANDLER = 4;
var GET_MESSAGE_FOR_REPLY_HANDLER = 5;
var GET_MESSAGE_FOR_REPLY_ALL_HANDLER = 6;
var GET_MESSAGE_FOR_FORWARD_HANDLER = 7;

aCacheHandlers[SET_FOLDER_HANDLER] = SetFolder_Handler;
aCacheHandlers[DELETE_MESSAGES_HANDLER] = SetFolder_Handler;
aCacheHandlers[VIEW_MESSAGE_HANDLER] = ViewMessage_Handler;
aCacheHandlers[SEND_MESSAGE_HANDLER] = SendMessage_Handler;
aCacheHandlers[GET_MESSAGE_FOR_REPLY_HANDLER] = GetMessageForReply_Handler;
aCacheHandlers[GET_MESSAGE_FOR_REPLY_ALL_HANDLER] = GetMessageForReplyAll_Handler;
aCacheHandlers[GET_MESSAGE_FOR_FORWARD_HANDLER] = GetMessageForForward_Handler;

var arNumMessages = new Array();

// detecting browsers
var is_ff = FireFoxDetect();
var is_opera = OperaDetect();

if (is_opera || is_ff)
{
	var allow_dhtml = false;
} else {
	var allow_dhtml = true;
}

//aCurrentState - array for remember current settings

var aCurrentState = new Array();
var CURRENT_PAGE = 1;
var CURRENT_SCREEN = 2;
var CURRENT_UP_MESSAGE = 3;
var CURRENT_DOWN_MESSAGE = 4;
var CURRENT_SHOW_HIDE_HEADERS = 5;
var CURRENT_PLAIN_HTML_VIEW_MODE = 6;
var CURRENT_MESSAGE_ID = 7;
var CURRENT_REDIRECT_MESSAGES_IDS = 8;
var CURRENT_LOCATION_CHANGE = 9;
var CURRENT_LOCATION_MESSAGES = 10;

//array for access to 'screens'
//ex. aScreens[SCREEN_MESSAGES_LIST]

var aScreens = new Array();
var SCREEN_MESSAGES_LIST = 0;
var SCREEN_VIEW_MESSAGE = 1;
var SCREEN_COMPOSE_MESSAGE = 2;
var SCREEN_REDIRECT_MESSAGE = 3;
var SCREEN_MANAGE_FOLDERS = 4;
var SCREEN_SETTINGS = 5;

var dMailFolders = new Dictionary();

//aCacheData - remember some data

var aCacheData = new Array();
var CACHE_DATA_MESSAGES = 0;
var CACHE_CURRENT_MESSAGE = 1;
var dCacheDataMessages = new Dictionary();
var CACHE_COMPOSE_MESSAGE_ATTACHMENTS = 4;
var aCacheDataEditMessage = new Array();

aCacheData[CACHE_DATA_MESSAGES] = dCacheDataMessages;
aCacheData[CACHE_COMPOSE_MESSAGE_ATTACHMENTS] = new Dictionary();

// define variables for some page elements
var MESSAGES_TABLE = 1;
var LOWTOOLBAR_SPAN = 2;
var LOWTOOLBAR_SPAN2 = 3;
var INFO_DIV = 4;
var CHECK_ALL_CHECKBOX = 5;
var DROP_DOWN_MENUS = 6;
var TOOLBAR_TABLES = 7;
var VIEW_MESSAGE_FROM = 8;
var VIEW_MESSAGE_TO = 9;
var VIEW_MESSAGE_CC = 10;
var VIEW_MESSAGE_DATE = 11;
var VIEW_MESSAGE_SUBJECT = 12;
var VIEW_MESSAGE_HEADERS = 13;
var VIEW_MESSAGE_ATTACH_TABLE = 14;
var COMMON_CONTAINER = 15;
var VIEW_MESSAGE_TEXT = 16;
var VIEW_MESSAGE_DOWN_ARROW = 17;
var VIEW_MESSAGE_UP_ARROW = 18;
var VIEW_MESSAGES_HTML_SWITCHER = 19;
var VIEW_MESSAGE_HEADERS = 20;
var VIEW_MESSAGE_A_SHOW_HIDE_HEADERS = 21;
var COMPOSE_MESSAGE_FROM = 22;
var COMPOSE_MESSAGE_TO = 23;
var COMPOSE_MESSAGE_CC = 24;
var COMPOSE_MESSAGE_BCC = 25
var COMPOSE_MESSAGE_SUBJECT = 26;
var COMPOSE_MESSAGE_TEXT = 27;
var COMPOSE_MESSAGE_TABLE = 29;
var REDIRECT_MESSAGE_TABLE = 30;
var REDIRECT_MESSAGE_BUTTON = 31;
var REDIRECT_MESSAGE_TEXT = 32;
var VIEW_MESSAGES_PLAIN_SWITCHER = 33;
var COMPOSE_MESSAGE_ATTACHMENTS_FORM = 39;
var COMPOSE_MESSAGE_ATTACHMENTS_TABLE = 40;
var VIEW_MESSSAGE_IMPORTANCE = 44;
var EMPTY_FOLDER = 46;
var REDIRECT_MESSAGE_TO = 53;
var ERROR_DIV = 54;
var INFOBAR_TABLE = 55;
var INFOBAR_TEXT = 56;
var COMPOSE_MESSAGE_ATTACHMENTS_DIV = 57;
var HISTORY_LOCATION = 58;
var HISTORY_FORM = 59;

var aCachePageElements = new Array();
aCachePageElements[VIEW_MESSAGE_DOWN_ARROW] = [];
aCachePageElements[VIEW_MESSAGE_UP_ARROW] = [];

aCachePageElements[HISTORY_LOCATION] = document.getElementById("historyLocation");
aCachePageElements[HISTORY_FORM] = document.getElementById("historyForm");

// create main div-container
var common_div = CreateChild(document.body,'div');
aCachePageElements[COMMON_CONTAINER] = common_div;

aCachePageElements[DROP_DOWN_MENUS] = new Array();
aCachePageElements[TOOLBAR_TABLES] = [];

var http_request = true;

// define tabs
var aTabs = new Array( new tab('Mail','Mail_Click()',true),undefined, new tab(strLogout,'Logout_Click()',false));

// array where save descriptions for toolbars for screens
var aToolbar = new Array();

// list of all titles for toolbar
var aToolbarTitles = [
strRefresh,strFind,strDelete,strReply,strReplyAll,strForward,strRedirect,strMarkReadSelected,strMarkUnReadSelected,strFlagSelected,strUnFlagSelected,strNewMessage,
strList,strIsSpam,strPrint,strSaveMessage,strDelete,strSaveAddress,'','',
strBtnSend,strBtnSend,strNewFolder,strRenameFolder,strDelete,
strRelease,strIsSpam,strNotSpam,strDelete
];

// list of all icons for toolbar
var aToolbarIcons = [
'menu/refresh.gif',
'menu/find.gif',
'menu/delete.gif',
'menu/reply.gif',
'menu/replyall.gif',
'menu/forward.gif',
'menu/redirect.gif',
'menu/mark_as_read.gif',
'menu/mark_as_unread.gif',
'menu/flag.gif',
'menu/unflag_menu.gif',
'menu/new_mail.gif',
'menu/back_to_list.gif',
'menu/isspam.gif',
'menu/print.gif',
'menu/save.gif',
'menu/delete.gif',
'menu/contacts_new_contact.gif',
'menu/message_up.gif',
'menu/message_down.gif',
'menu/send.gif',
'menu/save.gif',
'menu/folder.gif',
'menu/folder.gif',
'menu/delete.gif',
'menu/release.gif',
'menu/isspam.gif',
'menu/notspam.gif',
'menu/delete.gif'
];
// list of all functions-handlers for clicking items in toolbar
var aToolbarClicks = [
RefreshClick,
'FindClick',
DeleteSelected,
ReplyClick,
ReplyAllClick,
ForwardClick,
RedirectClick,
'MarkReadClick',
'MarkUnreadClick',
'MarkFlagClick',
'MarkUnflagClick',
NewMessageClick,
BackToListClick,
'IsSpamClick',
PrintMessageClick,
SaveMessageClick,
DeleteMessageClick,
'SaveAddressClick',
UpMessageClick,
DownMessageClick,
SendMessageClick,
'SaveMessageClickFromComposeScreen',
'NewFolderClick',
'RenameFolderClick',
'DeleteFolderClick',
'ReleaseSuspectMessageClick',
'IsSpamClick',
'NotSpamClick',
'DeleteSuspectMessageClick'
];

// defining toolbars
aToolbar[SCREEN_MESSAGES_LIST] = [11,0,2];
aToolbar[SCREEN_VIEW_MESSAGE] = [12,11,3,4,5,6,14,15,16,18,19];
aToolbar[SCREEN_COMPOSE_MESSAGE] = [12,20];
aToolbar[SCREEN_REDIRECT_MESSAGE] = [12];
aToolbar[SCREEN_MANAGE_FOLDERS] = [12,22,23,24];

var aMessagesListHeaders = new Array();
var aMessagesListHeadersWidth = new Array();
aMessagesListHeaders[SCREEN_MESSAGES_LIST] = ['&nbsp;N', '<input type="checkbox" onclick="javascript:CheckAllRows(this.checked,' + MESSAGES_TABLE + ');">', '<img src="images/attachment.gif">', '<img src="skins/' + aSettings[SETTING_SKIN] + '/menu/priority_high.gif">', strFrom, strDate, strSize, strSubject];
aMessagesListHeadersWidth[SCREEN_MESSAGES_LIST] = ['24','24','20','20','','140','50','400'];

CreateLoadingBar();
HideLoadingBar();

CreateErrorBar();
HideErrorBar();

// define 
var messages_list_header_row_index = 0;

// define default screen
var default_screen = SCREEN_MESSAGES_LIST;

// Go!
aCurrentState[CURRENT_LOCATION_CHANGE] = 1;
aCurrentState[CURRENT_LOCATION_MESSAGES] = 1;
ShowScreen(default_screen);
SetFolder(0);
