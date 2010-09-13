<?php 
include_once('common.php');

$ids = gp("ids");
if(empty($ids)){
	header("HTTP/1.0 400 Bad Request");
	echo 'Empty get $ids';
}else{
	$buddies = buddy($ids);
	complete_status($buddies);
	echo json_encode($buddies);
}
?>
