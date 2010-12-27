<?php

require_once( dirname( __FILE__ ) . '/' . 'admin_common.php' );
$html =  "<h3>IM Config</h3>";
$html .= "<p>domain: '" . $_IMC['domain'] . "'</p>";
$html .= "<p>host: '" . $_IMC['host'] . "'</p>";
$html .= "<p>port: '" . $_IMC['port'] . "'</p>";

$html .= "<h3>Allow url fopen</h3>";
$html .= ini_get('allow_url_fopen') ? "On" : "Off";

$html .= "<h3>IM check online</h3>";
$data = $imclient->check_connect();
$html .= $data->success ? "Success" : ( "Faild: " . $data->error_msg );

$html .= "<h3>Test for port 80</h3>";
$content = file_get_contents("http://www.google.com");
$html .= empty( $content ) ? "Faild" : "Success";

require_once( dirname( __FILE__ ) . '/' . 'admin_common.php' );

echo webim_header( '' );

echo webim_menu( 'check' );

echo '<div id="content">';
echo $html;
echo '</div>';

echo webim_footer();

