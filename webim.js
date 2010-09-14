//custom
(function(webim){
	var path = "";

	var menu = webim.JSON.decode('{}');
	webim.extend(webim.setting.defaults.data, webim.JSON.decode(_webim_setting));
	var webim = window.webim;
	webim.defaults.urls = {
		online:path + "webim/online.php",
		offline:path + "webim/offline.php",
		message:path + "webim/message.php",
		presence:path + "webim/presence.php",
		refresh:path + "webim/refresh.php",
		status:path + "webim/status.php"
	};
	webim.setting.defaults.url = path + "webim/setting.php";
	webim.history.defaults.urls = {
		load: path + "webim/history.php",
		clear: path + "webim/clear_history.php"
	};
	webim.room.defaults.urls = {
		member: path + "webim/members.php",
		join: path + "webim/join.php",
		leave: path + "webim/leave.php"
	};
	webim.buddy.defaults.url = path + "webim/buddies.php";
	webim.notification.defaults.url = path + "webim/notifications.php";


	webim.ui.emot.init({"dir": path + "webim/static/images/emot/default"});
	var soundUrls = {
		lib: path + "webim/static/assets/sound.swf",
		msg: path + "webim/static/assets/sound/msg.mp3"
	};
	var ui = new webim.ui(document.body, {
		soundUrls: soundUrls
	}), im = ui.im;
	ui.addApp("menu", {"data": menu});
	//rm shortcut in uchome
	if(!_webim_enable_shortcut)ui.layout.addShortcut(menu);
	ui.addApp("buddy");
	ui.addApp("room");
	ui.addApp("notification");
	ui.addApp("setting", {"data": webim.setting.defaults.data});
	if(!_webim_disable_chatlink)ui.addApp("chatlink", {
		link_href: [/u.php\?action=show&uid=(\d+)$/i, /u.php\?uid=(\d+)$/i, /mode.php\?m=o&space=1&q=user&u=(\d+)/, /mode.php\?m=o&q=user&uid=(\d+)/i, /mode.php\?m=o&q=user&u=(\d+)/i],
		space_href: [],
		off_link_class: /gray/
	});
	ui.render();
	im.autoOnline() && im.online();

})(webim);
