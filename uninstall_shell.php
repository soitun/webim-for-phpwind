<?php
if(php_sapi_name() != 'cli') exit("Run it with commmand `php -f shell.php`");
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'include.php');
$msg = "";
if(!empty($unwritable_paths)){
	$msg = unwritable_log($unwritable_paths, $subpathlen);
}elseif(!is_db_connectable($db_config)){
	$msg = "\n不能连接数据库\n\n";
}else{
	$logs = uninstall_config($_IMC, $im_config_file, $product_config_file);
	$logs = array_merge($logs, uninstall_template($templates, $template_file));
	//$logs = array_merge($logs, uninstall_db($db_config, $un_db_file));
	$logs = array_merge($logs, unclean_cache($cache_dir));
	$msg = log_install($logs, $subpathlen, false, true);
}
echo $msg;

?>
