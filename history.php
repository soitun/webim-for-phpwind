<?php 
include_once('common.php');

$id = gp("id");
$type = gp("type");
if(empty($id) || empty($type)){
	header("HTTP/1.0 400 Bad Request");
	echo 'Empty get $id or $type';
}else{
	echo json_encode(history($type, $id));
}
