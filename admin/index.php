<?php

require_once( dirname( __FILE__ ) . '/' . 'admin_common.php' );

webim_only_for_admin();

/** Check install */
if ( ! isset( $_IMC['version'] ) ) {
	header("Location: in.php");
	exit();
}

/** Check update */
if ( version_compare( $_IMC['version'], $im_version, "<" ) ) {
	header("Location: upgrade.php");
	exit();
}

$unwritable_paths = webim_select_unwritable_path( false );

$msg = "";
$success = false;

if ( !empty($unwritable_paths ) ){

	$msg = webim_unwritable_log( $unwritable_paths );
	include_once( '_error.php' );
	exit();

}

if( isset( $_POST['host'] ) && isset( $_POST['domain'] ) && isset( $_POST['apikey'] ) ){
	webim_update_config();
	header("Location: index.php?success");
}else{
	if ( isset( $_GET['success'] ) ) {
		$success = true;
		$notice = "<p id='notice'>更新成功。</p>";
	} else {
	}
}

echo webim_header( '配置' );
echo webim_menu( 'index' );
?>
<div id="content">
				<?php echo $notice ?>
				<div class="box">
				<h3>更新基本配置</h3>
				<div class="box-c">
				<p class="box-desc">apikey需要到<a href="http://www.webim20.cn" target="_blank">webim20.cn</a>注册</p>
					<form action="" method="post" class="form">
						<p class="clearfix"><label for="host">服务器地址：</label><input class="text" type="text" id="host" value="<?php echo $_IMC['host']; ?>" name="host"/><span class="help">IM服务器地址，国内im.webim20.cn，国外im.webim.us，独立版用户使用自己的服务器地址</span></p>
						<p class="clearfix"><label for="port">服务器端口：</label><input class="text" type="text" id="port" value="<?php echo $_IMC['port']; ?>" name="port"/></p>
						<p class="clearfix"><label for="domain">注册域名：</label><input class="text" type="text" id="domain" value="<?php echo $_IMC['domain']; ?>" name="domain"/><span class="help">网站注册域名</span></p>
						<p class="clearfix"><label for="apikey">注册apikey：</label><input class="text" type="text" id="apikey" value="<?php echo $_IMC['apikey']; ?>" name="apikey"/></p>
						<p class="clearfix"><label for="local">本地语言：</label><select class="select" id="local" name="local">
						<option value="zh-CN" <?php echo $_IMC['local'] == 'zh-CN' ? 'selected="selected"' : '' ?>>简体中文</option>
						<option value="zh-TW" <?php echo $_IMC['local'] == 'zh-TW' ? 'selected="selected"' : '' ?>>繁体中文</option>
						<option value="en" <?php echo $_IMC['local'] == 'en' ? 'selected="selected"' : '' ?>>English</option>
						</select>
						</p>
<?php echo webim_check_tag( "disable_room", $_IMC['disable_room'], "关闭群组聊天："); ?>
<?php echo webim_check_tag( "disable_chatlink", $_IMC['disable_chatlink'], "关闭陌生人聊天："); ?>
<?php #echo webim_check_tag( "disable_menu", $_IMC['disable_menu'], "隐藏工具栏："); ?>
<?php #echo webim_check_tag( "enable_shortcut", $_IMC['enable_shortcut'], "开启快捷工具栏："); ?>
<?php #echo webim_check_tag( "show_realname", $_IMC['show_realname'], "显示真实姓名："); ?>
<?php #echo webim_check_tag( "enable_login", $_IMC['enable_login'], "支持从IM登录："); ?>
						<p class="actions clearfix"><input type="submit" class="submit" value="提交" /></p>
					</form>
				</div>
				</div>

			</div>

<?php
echo webim_footer();

function webim_check_tag( $name, $value, $text ) {
	return '<p class="clearfix"><label for="' . $name . '">' . $text . '</label><input type="radio" value="1" name="' . $name . '" class="radio" id="' . $name . '_yes" ' . ( $value ? 'checked="checked"' : '' ) . '>是 &nbsp;<input type="radio" value="" name="' . $name . '" class="radio" id="' . $name . '_no" ' . ( $value ? '' : 'checked="checked"' ) . '>否</p>';
}
?>
