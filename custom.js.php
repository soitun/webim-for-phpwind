<?php
header("Content-type: application/javascript");
include_once('common.php');
$menu = array(
	array("title" => 'doing',"icon" =>"image/app/doing.gif","link" => "space.php?do=doing"),
	array("title" => 'album',"icon" =>"image/app/album.gif","link" => "space.php?do=album"),
	array("title" => 'blog',"icon" =>"image/app/blog.gif","link" => "space.php?do=blog"),
	array("title" => 'thread',"icon" =>"image/app/mtag.gif","link" => "space.php?do=thread"),
	array("title" => 'share',"icon" =>"image/app/share.gif","link" => "space.php?do=share")
);

if($_SCONFIG['my_status']) {
	if(is_array($_SGLOBAL['userapp'])) { 
		foreach($_SGLOBAL['userapp'] as $value) { 
			$menu[] = array("title" => to_utf8($value['appname']), "icon" =>"http://appicon.manyou.com/icons/".$value['appid'],"link" => "userapp.php?id=".$value['appid']);
		}
	}
	if(is_array($_SGLOBAL['my_menu'])) { 
		foreach($_SGLOBAL['my_menu'] as $value) { 
			$menu[] = array("title" => to_utf8($value['appname']), "icon" =>"http://appicon.manyou.com/icons/".$value['appid'],"link" => "userapp.php?id=".$value['appid']);
		}
	}
}
$setting = json_encode(setting());

?>
//custom
(function(webim){
    var path = "";

    var menu = webim.JSON.decode('<?php echo json_encode($menu) ?>');
	webim.extend(webim.setting.defaults.data, webim.JSON.decode('<?php echo $setting ?>'));
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
	if(<?php var_export(!!$_IMC['enable_shortcut']); ?>)ui.layout.addShortcut(menu);
	ui.addApp("buddy");
	ui.addApp("room");
	ui.addApp("notification");
	ui.addApp("setting", {"data": webim.setting.defaults.data});
	if(<?php echo var_export(!$_IMC['disable_chatlink']) ?>)ui.addApp("chatlink", {
		off_link_class: /r_option|spacelink/i
	});
	ui.render();
        im.autoOnline() && im.online();

})(webim);
