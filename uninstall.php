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
	if(isset($_POST['uninstall'])){
		$logs = uninstall_config($_IMC, $im_config_file, $product_config_file);
		$logs = array_merge($logs, uninstall_template($templates, $template_file));
		//$logs = array_merge($logs, uninstall_db($db_config, $db_file));
		$logs = array_merge($logs, clean_cache($cache_dir));
		$msg = log_install($logs, $subpathlen, true, true);
		$success = true;
	}else{
		$msg = uninstall_html($_IMC);
	}
}

function uninstall_html($config, $errors = array()){
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
		<h3>卸载WebIM</h3>
		<div class="box-c">
			<p class="box-desc">真的要卸载吗？</p>
			$err
			<form action="" method="post" class="form">
				<p><input class="text" type="hidden" id="apikey" value="1" name="uninstall"/></p>
				<p class="actions"><input type="submit" class="submit" value="确认" /></p>
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
		<title>WebIM For UChome卸载</title>
		<link href="base.css" media="all" type="text/css" rel="stylesheet" />
	</head>
	<body>
		<h1>WebIM For UChome卸载</h1>
		<div id="wrap">
			<?php echo $msg; ?>
		</div>
		<div id="footer"><p><a href="http://www.webim20.cn">© 2010 NextIM</a></p></div>
		<?php if($success): ?>
		<script type="text/javascript">
			//setTimeout(function(){window.location.href = "../index.php";}, 2000);
		</script>
		<?php endif; ?>
	</body>
</html>
