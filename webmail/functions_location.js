var isDOM = document.getElementById ? true : false;
var isOpera = window.opera && isDOM;

function changeLocation(newLocation) {
	if (!isOpera){
		aCachePageElements[HISTORY_LOCATION].value = newLocation;
		aCachePageElements[HISTORY_FORM].action = location_url + '?param=' + Math.random();
		aCachePageElements[HISTORY_FORM].submit();
	} else {
		onLocationChanged(newLocation);
	}
}

function onLocationChanged(location_str) {
	if (aCurrentState[CURRENT_LOCATION_CHANGE] == 1){
		var params = location_str.split('|%|');
		switch(params[1]*1)
		{
			case SCREEN_VIEW_MESSAGE:
				ViewMessage(params[2]*1);
			break;
			case SCREEN_COMPOSE_MESSAGE:
				NewMessage(params[2]);
			break;
			case SCREEN_MESSAGES_LIST:
				if (params[2]*1 == aCurrentState[CURRENT_PAGE] && params[0] != 'r') {
					ShowScreen(SCREEN_MESSAGES_LIST);
				} else {
					MakeRequest(processing_script_url + '?action=get&request=messages&page=' + params[2], SET_FOLDER_HANDLER);
				}
			break;
			case SCREEN_REDIRECT_MESSAGE:
				RedirectMessage(params[2], params[3]);
			break;
		}
	}
	aCurrentState[CURRENT_LOCATION_CHANGE] = 1;
}
