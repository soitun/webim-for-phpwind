<?php
define('IM_ROOT', dirname(dirname(__FILE__)));
include_once(IM_ROOT.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'install.php');
include_once(IM_ROOT.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'util.php');
define('PRODUCT_ROOT', dirname(IM_ROOT));
$im_config_file = IM_ROOT.DIRECTORY_SEPARATOR.'config.php';
$product_config_file = PRODUCT_ROOT.DIRECTORY_SEPARATOR.'config.php';
$template_file = IM_ROOT.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'webim_uchome.htm';
$db_file = IM_ROOT.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'install.sql';
$un_db_file = IM_ROOT.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'uninstall.sql';
$cache_dir = PRODUCT_ROOT.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'tpl_cache';

//添加需监测可写权限的文件
$need_check_paths = array();
$need_check_paths[] = $product_config_file;
$need_check_paths[] = $cache_dir;
$need_check_paths[] = $db_file;
$need_check_paths[] = $un_db_file;
$need_check_paths[] = $template_file;

include_once($product_config_file);

$db_config = array('host' => UC_DBHOST, 'username' => UC_DBUSER, 'password' => UC_DBPW, 'db_name' => UC_DBNAME, 'charset' => UC_DBCHARSET, 'db_prefix' => UC_DBTABLEPRE);

if(file_exists($im_config_file)){
	include_once($im_config_file);
	$need_check_paths[] = $im_config_file;
}else{
	$need_check_paths[] = IM_ROOT;
}
$template_dir = PRODUCT_ROOT.DIRECTORY_SEPARATOR.'template';
$templates = array();
$tmp_name = basename($template_file);
foreach(scan_subdir($template_dir) as $k => $v){
	$d = $template_dir.DIRECTORY_SEPARATOR.$v;
	$f = $d.DIRECTORY_SEPARATOR.'footer.htm';
	$t = $d.DIRECTORY_SEPARATOR.$tmp_name;
	if(file_exists($f)){
		$templates[] = $d;
		$need_check_paths[] = $d;
		$need_check_paths[] = $f;
		if(file_exists($t)){
			$need_check_paths[] = $t;
		}
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
		$markup .= "@include_once('webim/config.php');";
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
		$tmp = $v.DIRECTORY_SEPARATOR.basename($file);
		$logs[] = array(true, (file_exists($tmp) ? "更新" : "写入")."模版", $tmp);
		file_put_contents($tmp, $markup);
		$inc = $v.DIRECTORY_SEPARATOR.'footer.htm';
		$name = basename($file, ".htm");
		$html = file_get_contents($inc);
		$html = preg_replace('/<\!--\{template\swebim[^>]+>/i', "", $html);
		list($html, $foot) = explode("</body>", $html);
		$logs[] = array(true, "加载模版", $inc);
		$inc_markup = "<!--{template ".$name."}-->";
		$html .= $inc_markup."</body>".$foot;
		file_put_contents($inc, $html);
	}
	return $logs;
}

function uninstall_template($templates, $file){
	$logs = array();
	foreach($templates as $k => $v) {
		$tmp = $v.DIRECTORY_SEPARATOR.basename($file);
		if(file_exists($tmp)){
			$logs[] = array(true, "删除模版", $tmp);
			unlink($tmp);
		}
		$inc = $v.DIRECTORY_SEPARATOR.'footer.htm';
		$name = basename($file, ".htm");
		$html = file_get_contents($inc);
		$html = preg_replace('/<\!--\{template\swebim[^>]+>/i', "", $html);
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
		$markup = preg_replace('/\@?include_once\([\'"]webim\/config\.php[\'"]\);?/i', "", $markup);
		file_put_contents($product_file, $markup);
		$logs[] = array(true, "卸载配置", $product_file);
	}
	return $logs;
}

?>
