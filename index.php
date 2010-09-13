<?php
include('config_common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>WebIM For UChome</title>
		<link href="base.css" media="all" type="text/css" rel="stylesheet" />
	</head>
	<body>
		<h1>WebIM For UChome</h1>
		<div id="wrap">
			<div id="menu">
				<ul>
					<li class="current"><a href="index.php">基本配置</a></li>
					<li><a href="themes.php">主题选择</a></li>
				</ul>
			</div>
			<div id="content">
				<?php echo $notice ?>
				<div class="box">
				<h3>更新基本配置</h3>
				<div class="box-c">
				<p class="box-desc">apikey需要到<a href="http://www.webim20.cn" target="_blank">webim20.cn</a>注册</p>
					<form action="" method="post" class="form">
						<p class="clearfix"><label for="host">服务器地址：</label><input class="text" type="text" id="host" value="<?php echo $_IMC['host']; ?>" name="host"/><span class="help">IM服务器地址</span></p>
						<p class="clearfix"><label for="port">服务器端口：</label><input class="text" type="text" id="port" value="<?php echo $_IMC['port']; ?>" name="port"/></p>
						<p class="clearfix"><label for="domain">注册域名：</label><input class="text" type="text" id="domain" value="<?php echo $_IMC['domain']; ?>" name="domain"/><span class="help">网站注册域名</span></p>
						<p class="clearfix"><label for="apikey">注册apikey：</label><input class="text" type="text" id="apikey" value="<?php echo $_IMC['apikey']; ?>" name="apikey"/></p>
						<p class="clearfix"><label for="local">本地语言：</label><select class="select" id="local" name="local">
						<option value="zh-CN" <?php echo $_IMC['local'] == 'zh-CN' ? 'selected="selected"' : '' ?>>简体中文</option>
						<option value="zh-TW" <?php echo $_IMC['local'] == 'zh-TW' ? 'selected="selected"' : '' ?>>繁体中文</option>
						<option value="en" <?php echo $_IMC['local'] == 'en' ? 'selected="selected"' : '' ?>>English</option>
						</select>
						</p>
						<p class="clearfix"><label for="disable_room">关闭群组聊天：</label><input type="radio" value="1" name="disable_room" class="radio" id="disable_room_yes" <?php echo $_IMC['disable_room'] ? 'checked="checked"' : ''; ?>>是 &nbsp;<input type="radio" value="" name="disable_room" class="radio" id="disable_room_no" <?php echo $_IMC['disable_room'] ? '0' : 'checked="checked"'; ?>>否</p>
						<p class="clearfix"><label for="disable_chatlink">关闭陌生人聊天：</label><input type="radio" value="1" name="disable_chatlink" class="radio" id="disable_chatlink_yes" <?php echo $_IMC['disable_chatlink'] ? 'checked="checked"' : ''; ?>>是 &nbsp;<input type="radio" value="" name="disable_chatlink" class="radio" id="disable_chatlink_no" <?php echo $_IMC['disable_chatlink'] ? '0' : 'checked="checked"'; ?>>否</p>
						<p class="clearfix"><label for="enable_shortcut">开启快捷工具栏：</label><input type="radio" value="1" name="enable_shortcut" class="radio" id="enable_shortcut_yes" <?php echo $_IMC['enable_shortcut'] ? 'checked="checked"' : ''; ?>>是 &nbsp;<input type="radio" value="" name="enable_shortcut" class="radio" id="enable_shortcut_no" <?php echo $_IMC['enable_shortcut'] ? '0' : 'checked="checked"'; ?>>否</p>
						<p class="clearfix"><label for="show_realname">显示真实姓名：</label><input type="radio" value="1" name="show_realname" class="radio" id="enable_shortcut_yes" <?php echo $_IMC['show_realname'] ? 'checked="checked"' : ''; ?>>是 &nbsp;<input type="radio" value="" name="show_realname" class="radio" id="enable_shortcut_no" <?php echo $_IMC['show_realname'] ? '0' : 'checked="checked"'; ?>>否</p>
						<p class="actions clearfix"><input type="submit" class="submit" value="提交" /></p>
					</form>
				</div>
				</div>

			</div>
		</div>
		<div id="footer"><p> <a href="uninstall.php">卸载</a> | <a href="http://www.webim20.cn">© 2010 NextIM</a></p></div>
	</body>
</html>

