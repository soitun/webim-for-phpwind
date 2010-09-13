<?php
include('config_common.php');

$theme = $_IMC['theme'];
$path = dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."themes";
$files = scan_subdir($path);
$html = '<h3 id="header-title">主题选择</h3><ul id="themes">';
foreach ($files as $k => $v){
	$t_path = $path.DIRECTORY_SEPARATOR.$v;
	if(is_dir($t_path) && is_file($t_path.DIRECTORY_SEPARATOR."jquery.ui.theme.css")){
		$cur = $v == $theme ? " class='current'" : "";
		$url = '?theme='.$v.'#'.$v;
		$html .= "<li$cur id='$v'><h4><a href='$url'>$v</a></h4><p><a href='$url'><img width=100 height=134 src='static/themes/images/$v.png' alt='$v' title='$v'/></a></p></li>";
	}
}
$html .= '</ul>';

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
					<li><a href="index.php">基本配置</a></li>
					<li class="current"><a href="themes.php">主题选择</a></li>
				</ul>
			</div>
			<div id="content">
				<?php echo $notice; ?>
				<?php echo $html;?>
			</div>
		</div>
		<div id="footer"><p> <a href="uninstall.php">卸载</a> | <a href="http://www.webim20.cn">© 2010 NextIM</a></p></div>
	</body>
</html>
