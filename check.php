<?php
header("Content-type: application/javascript");

include_once('common.php');
$im = new WebIM($user, null, $_IMC['domain'], $_IMC['apikey'], $_IMC['host'], $_IMC['port']);

echo "\n\nIM Config \n";
echo "'" . $_IMC['domain'] . "'\n";
echo "'" . $_IMC['host'] . "'\n";
echo "'" . $_IMC['port'] . "'\n";

echo "\n\n\nIM check online \n";
$client = new HttpClient(trim($_IMC['host']), trim($_IMC['port']));
print_r($im->check_connect());

echo "Test for port 80\n";
echo file_get_contents("http://www.webim20.cn");

