<?php
define('IM_ROOT', dirname(dirname(__FILE__)));
include_once(IM_ROOT.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'install.php');
include_once(IM_ROOT.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'util.php');
define('PRODUCT_ROOT', dirname(IM_ROOT));
$im_config_file = IM_ROOT.DIRECTORY_SEPARATOR.'config.php';
$product_config_file = PRODUCT_ROOT.DIRECTORY_SEPARATOR.'data/sql_config.php';
$template_file = IM_ROOT.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'webim_phpwind.htm';
$db_file = IM_ROOT.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'install.sql';
$un_db_file = IM_ROOT.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'uninstall.sql';
$cache_dir = PRODUCT_ROOT.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'tplcache';
//添加需监测可写权限的文件
$need_check_paths = array();
$need_check_paths[] = $product_config_file;
$need_check_paths[] = $cache_dir;
$need_check_paths[] = $db_file;
$need_check_paths[] = $un_db_file;
//$need_check_paths[] = $template_file;

include($product_config_file);

$db_config = array('host' => $dbhost, 'username' => $dbuser, 'password' => $dbpw, 'db_name' => $dbname, 'charset' => $charset, 'db_prefix' => $PW);

if(file_exists($im_config_file)){
	include_once($im_config_file);
	$need_check_paths[] = $im_config_file;
}else{
	$need_check_paths[] = IM_ROOT;
}

$templates = array();
$template_dir = PRODUCT_ROOT.DIRECTORY_SEPARATOR.'template';
//$tmp_name = basename($template_file);
foreach(scan_subdir($template_dir) as $k => $v){
	$d = $template_dir.DIRECTORY_SEPARATOR.$v;
	$f = $d.DIRECTORY_SEPARATOR.'footer.htm';
	if(file_exists($f)){
		$templates[] = $d;
		$need_check_paths[] = $f;
	}
}
$template_dir = PRODUCT_ROOT.DIRECTORY_SEPARATOR.'mode/o/template';
if ( is_dir( $template_dir ) ){
	$f = $template_dir.DIRECTORY_SEPARATOR.'footer.htm';
	if(file_exists($f)){
		$templates[] = $template_dir;
		$need_check_paths[] = $f;
	}
}

/*pw 7.0*/
$template_dir = PRODUCT_ROOT.DIRECTORY_SEPARATOR.'mode/area/template';
if ( is_dir( $template_dir ) ){
	$f = $template_dir.DIRECTORY_SEPARATOR.'footer.htm';
	if(file_exists($f)){
		$templates[] = $template_dir;
		$need_check_paths[] = $f;
	}
}


$unwritable_paths = select_unwritable_path($need_check_paths);
$subpathlen = strlen(dirname(PRODUCT_ROOT)) + 1;

function install_config($config, $file, $product_file){
	$logs = array();
	$markup = "<?php\n\$_IMC = ".var_export($config, true).";\n";
	$logs[] = array(true, (file_exists($file) ? "更新" : "写入")."配置", $file);
	file_put_contents($file, $markup);
	$markup = file_get_contents($product_file);
	if(strpos($markup, 'webim/config.php') === false) {
		$markup = trim($markup);
		$markup = substr($markup, -2) == '?>' ? substr($markup, 0, -2) : $markup;
		$markup .= "@include_once(dirname(dirname(__FILE__)).'/webim/config.php');";
		file_put_contents($product_file, $markup);
		$logs[] = array(true, "加载配置", $product_file);
	}else{
		$logs[] = array(true, "检查加载", $product_file);
	}
	return $logs;
}

function install_template($templates, $file){
	$logs = array();
	$markup = file_get_contents($file);
	foreach($templates as $k => $v) {
		//$tmp = $v.DIRECTORY_SEPARATOR.basename($file);
		//$logs[] = array(true, (file_exists($tmp) ? "更新" : "写入")."模版", $tmp);
		//file_put_contents($tmp, $markup);
		$inc = $v.DIRECTORY_SEPARATOR.'footer.htm';
		$name = basename($file, ".htm");
		$html = file_get_contents($inc);
		$html = preg_replace('/<script[^w>]+webim[^>]+><\/script>/i', "", $html);
		list($html, $foot) = explode("</body>", $html);
		$logs[] = array(true, "加载模版", $inc);
		$inc_markup = '<script type="text/javascript" src="webim/custom.js.php"></script>';
		$html .= $inc_markup."</body>".$foot;
		file_put_contents($inc, $html);
	}
	return $logs;
}

function uninstall_template($templates, $file){
	$logs = array();
	foreach($templates as $k => $v) {
		//$tmp = $v.DIRECTORY_SEPARATOR.basename($file);
		//if(file_exists($tmp)){
		//	$logs[] = array(true, "删除模版", $tmp);
		//	unlink($tmp);
		//}
		$inc = $v.DIRECTORY_SEPARATOR.'footer.htm';
		$name = basename($file, ".htm");
		$html = file_get_contents($inc);
		$html = preg_replace('/<script[^w>]+webim[^>]+><\/script>/i', "", $html);
		$logs[] = array(true, "卸载模版", $inc);
		//list($html, $foot) = explode("</body>", $html);
		//$inc_markup = "<!--{template ".$name."}-->";
		//$html .= $inc_markup."</body>".$foot;
		file_put_contents($inc, $html);
	}
	return $logs;
}

function uninstall_config($config, $file, $product_file){
	$logs = array();
	$markup = file_get_contents($product_file);
	if(strpos($markup, 'webim/config.php')) {
		$markup = preg_replace('/\@?include_once\([^\/]+\/webim\/config\.php[\'"]\);?/i', "", $markup);
		file_put_contents($product_file, $markup);
		$logs[] = array(true, "卸载配置", $product_file);
	}
	return $logs;
}

?>
