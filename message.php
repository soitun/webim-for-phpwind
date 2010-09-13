<?php 
include_once('common.php');
$ticket = p("ticket");
$type = p("type");
$offline = p("offline");
$to = p("to");
$body = p("body");
$style = p("style");
$time=microtime(true)*1000;
if(empty($ticket) || empty($type) || empty($to) || empty($body)) {
	header("HTTP/1.0 400 Bad Request");
	echo 'Empty post $ticket or $type or $to or $body';
}else {
	$send = $offline == "true" || $offline == "1" ? 0 : 1;
	$columns = "`send`,`to`,`from`,`nick`,`style`,`body`,`timestamp`,`type`";
	$values_from = "'$send','$to','$user->id','$user->nick','$style','".$body."','$time','$type'";
	$ucdb->query("INSERT INTO ".im_tname('histories')." ($columns) VALUES ($values_from)");
	if($send == 1) {
		require 'config.php';
		$im = new WebIM($user, $ticket, $_IMC['domain'], $_IMC['apikey'], $_IMC['host'], $_IMC['port']);
		$im->message($type, $to, $body, $style);
	}
	echo "ok";

}
