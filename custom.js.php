<?php
header("Content-type: application/javascript");
include_once('common.php');
/*
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
*/
$setting = json_encode(setting());

?>
_webim_min = window.location.href.indexOf("webim_debug") != -1 ? "" : ".min";
_webim_setting = '<?php echo $setting; ?>';
_webim_disable_chatlink = <?php echo $_IMC['disable_chatlink'] ? "true" : "false" ?>;
_webim_enable_shortcut = <?php echo $_IMC['enable_shortcut'] ? "true" : "false" ?>;
document.write('<link href="webim/static/webim.phpwind'+_webim_min+'.css" media="all" type="text/css" rel="stylesheet"/><link href="webim/static/themes/<?php echo $_IMC['theme']; ?>/jquery.ui.theme.css" media="all" type="text/css" rel="stylesheet"/><script src="webim/static/webim.phpwind'+_webim_min+'.js" type="text/javascript"></script><script src="webim/static/i18n/webim-<?php echo $_IMC['local']; ?>.js" type="text/javascript"></script><script src="webim/webim.js" type="text/javascript"></script>');

