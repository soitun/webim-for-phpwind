<?php

require_once( dirname( __FILE__ ) . '/' . 'admin_common.php' );
require_once( 'markdown.php' );

$text = file_get_contents( '../CHANGELOG.md' );
$html =  Markdown( $text );
$html = preg_replace( '/<h1.*?h1>/i', "", $html );

echo webim_header( '更新日志' );

echo webim_menu( 'changelog' );

echo '<div id="content">';
echo $html;
echo '</div>';

echo webim_footer();

?>

