<?php
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'include.php');
include_once(IM_ROOT.DIRECTORY_SEPARATOR.'common.php');
if(!ckfounder($user->uid)){
	//is not admin
	exit('Please login as admin.');
}
$msg = "";
$success = false;
if(!empty($unwritable_paths)){
	$msg = unwritable_log($unwritable_paths, $subpathlen, true);
}elseif(!is_db_connectable($db_config)){
	$msg = '<div class="box"><div class="box-c">不能连接数据库。</div></div>';
}else{
	$PRE_IMC = isset($_IMC) ? $_IMC : null;
	include_once(IM_ROOT.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'config.php');
	$_IMC = merge_config($_IMC, $PRE_IMC);
	if(isset($_POST['host']) && isset($_POST['domain']) && isset($_POST['apikey'])){
		$_IMC['host'] = $_POST['host'];
		$_IMC['domain'] = $_POST['domain'];
		$_IMC['apikey'] = $_POST['apikey'];
		$logs = install_config($_IMC, $im_config_file, $product_config_file);
		$logs = array_merge($logs, install_template($templates, $template_file));
		$logs = array_merge($logs, install_db($db_config, $db_file));
		$logs = array_merge($logs, clean_cache($cache_dir));
		$msg = log_install($logs, $subpathlen, true);
		$success = true;
	}else{
		$msg = config_html($_IMC);
	}
}

function config_html($config, $errors = array()){
	$host = $config['host'];
	$domain = $config['domain'];
	$apikey = $config['apikey'];
	$err = "";
	$err_c = "";
	if(!empty($errors)){
		$err_c = " box-error";
		$err = "<ul class=\"error\"><li>".implode($errors, "</li><li>")."</li></ul>";
	}
	return <<<EOF
		<div class="box$err_c">
		<h3>设置安装信息</h3>
		<div class="box-c">
			<p class="box-desc">请先到<a href="http://www.webim20.cn" target="_blank">webim20.cn</a>注册apikey网站注册</p>
			$err
			<form action="" method="post" class="form">
				<p class="clearfix"><label for="host">服务器地址：</label><input class="text" type="text" id="host" value="$host" name="host"/><span class="help">IM服务器地址</span></p>
				<p class="clearfix"><label for="domain">注册域名：</label><input class="text" type="text" id="domain" value="$domain" name="domain"/><span class="help">网站注册域名</span></p>
				<p class="clearfix"><label for="apikey">注册apikey：</label><input class="text" type="text" id="apikey" value="$apikey" name="apikey"/></p>
				<p class="actions clearfix"><input type="submit" class="submit" value="提交" /></p>
			</form>
		</div>
	</div>
EOF;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>WebIM For UChome安装</title>
		<link href="base.css" media="all" type="text/css" rel="stylesheet" />
	</head>
	<body>
		<h1>WebIM For UChome安装</h1>
		<div id="wrap">
			<?php echo $msg; ?>
		</div>
		<div id="footer"><p><a href="http://www.webim20.cn">© 2010 NextIM</a></p></div>
		<?php if($success): ?>
		<script type="text/javascript">
			setTimeout(function(){window.location.href = "index.php";}, 2000);
		</script>
		<?php endif; ?>
	</body>
</html>
