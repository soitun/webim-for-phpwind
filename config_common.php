<?php

include_once('common.php');
if(!ckfounder($user->uid)){
	//is not admin
	exit('Please login as admin.');
}
if(!isset($_IMC)){
	header("Location: install.php");
	exit();
}
define('IM_ROOT', dirname(__FILE__));
include_once(IM_ROOT.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'install.php');
$_IMC_OLD = $_IMC;
include_once(IM_ROOT.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'config.php');
$_IMC = array_merge($_IMC, $_IMC_OLD);
define('PRODUCT_ROOT', dirname(IM_ROOT));
$im_config_file = IM_ROOT.DIRECTORY_SEPARATOR.'config.php';
$unwritable_paths = select_unwritable_path(array($im_config_file));
$subpathlen = strlen(dirname(PRODUCT_ROOT)) + 1;
if(!empty($unwritable_paths)){
	$msg = unwritable_log($unwritable_paths, $subpathlen, true);
	include_once('config_error.php');
	exit();
}

$is_edit = false;
$notice = "";
foreach($_IMC as $k => $v){
	if($k != 'version' && $k != 'enable'){
		$nv = gp($k);
		if(!is_null($nv)){
			$_IMC[$k] = $nv;
			$is_edit = true;
			$notice = "<p id='notice'>更新成功。</p>";
		}
	}
}
if($is_edit){
	$markup = "<?php\n\$_IMC = ".var_export($_IMC, true).";\n";
	file_put_contents($im_config_file, $markup);
}

