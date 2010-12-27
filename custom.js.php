<?php

include_once('common.php');

header("Content-type: application/javascript");
/** set no cache in IE */
header("Cache-Control: no-cache");

$webim_jsonp = isset( $_GET['remote'] ) || webim_is_remote();
$webim_path = webim_urlpath();

if ( !$im_is_login && !$_IMC['enable_login'] ) {
	exit('"Please login at first."');
}


if ( $im_is_login ) {
	$setting = json_encode( webim_get_settings() );
	$imuser->show = 'unavailable';
	$imuser = json_encode( $imuser );
} else {
	$setting = "";
	$imuser = "";
}
if ( !$_IMC['disable_menu'] )
	$menu = json_encode( webim_get_menu() );

?>
var _IMC = {
production_name: '<?php echo WEBIM_PRODUCT_NAME ?>',
version: '<?php echo $_IMC['version']; ?>',
path: '<?php echo $webim_path; ?>',
is_login: '<?php echo $im_is_login ? "1" : "" ?>',
login_options: <?php echo json_encode( array("notice" => "使用phpwind帐号登录", "questions" => null) ); ?>,
user: <?php echo $imuser ? $imuser : '""'; ?>,
setting: <?php echo $setting ? $setting : '""'; ?>,
menu: <?php echo !$_IMC['disable_menu'] ? $menu : '""'; ?>,
disable_chatlink: '<?php echo $_IMC['disable_chatlink'] ? "1" : "" ?>',
enable_shortcut: '<?php echo $_IMC['enable_shortcut'] ? "1" : "" ?>',
disable_menu: '<?php echo $_IMC['disable_menu'] ? "1" : "" ?>',
theme: '<?php echo $_IMC['theme']; ?>',
local: '<?php echo $_IMC['local']; ?>',
jsonp: '<?php echo $webim_jsonp ? "1" : "" ?>',
min: window.location.href.indexOf("webim_debug") != -1 ? "" : ".min"
};
_IMC.script = window.webim ? '' : ('<link href="' + _IMC.path + 'static/webim.' + _IMC.production_name + _IMC.min + '.css?' + _IMC.version + '" media="all" type="text/css" rel="stylesheet"/><link href="' + _IMC.path + 'static/themes/' + _IMC.theme + '/jquery.ui.theme.css?' + _IMC.version + '" media="all" type="text/css" rel="stylesheet"/><script src="' + _IMC.path + 'static/webim.' + _IMC.production_name + _IMC.min + '.js?' + _IMC.version + '" type="text/javascript"></script><script src="' + _IMC.path + 'static/i18n/webim-' + _IMC.local + '.js?' + _IMC.version + '" type="text/javascript"></script>');
_IMC.script += '<script src="' + _IMC.path + 'webim.js?' + _IMC.version + '" type="text/javascript"></script>';
document.write( _IMC.script );
