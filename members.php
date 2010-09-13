<?php
include_once('common.php');

$ticket = g("ticket");
$id = g("id");
if(empty($ticket) || empty($id)){
	header("HTTP/1.0 400 Bad Request");
	echo 'Empty get $ticket or $id';
}else{
	require 'config.php';
	$im = new WebIM($user, $ticket, $_IMC['domain'], $_IMC['apikey'], $_IMC['host'], $_IMC['port']);
	$re = $im->members($id);
	if($re){
		echo json_encode($re);
	}else{
		header("HTTP/1.0 404 Not Found");
		echo "Not Found";
	}
}