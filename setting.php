<?php

include_once('common.php');
$data = p('data');
if(empty($data)){
	header("HTTP/1.0 400 Bad Request");
	echo 'Empty post $data';
}else{
        $ucdb->query("UPDATE ".im_tname('settings')." SET web='$data' WHERE uid=$user->uid");
	echo 'ok';
}
