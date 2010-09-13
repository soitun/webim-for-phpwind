<?php

include_once('common.php');

$ticket = p("ticket");
$show = p("show");
$to = p("to");
if(empty($ticket) || empty($show) || empty($to)){
	header("HTTP/1.0 400 Bad Request");
	echo 'Empty post $ticket or $show or $to';
}else{
	require 'config.php';
	$im = new WebIM($user, $ticket, $_IMC['domain'], $_IMC['apikey'], $_IMC['host'], $_IMC['port']);
	$re = $im->status($to, $show);
	if($re != "ok"){
		header("HTTP/1.0 404 Not Found");
	}
	echo $re;
}