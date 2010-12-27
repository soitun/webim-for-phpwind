<?php

require_once( dirname( __FILE__ ) . '/' . 'admin_common.php' );

webim_only_for_admin();

$unwritable_paths = webim_select_unwritable_path( false );

$msg = "";
$success = false;

if ( !empty($unwritable_paths ) ){

	$msg = webim_unwritable_log( $unwritable_paths );
	include_once( '_error.php' );
	exit();

}

if( isset( $_GET['theme'] ) ){
	webim_update_config();
	header("Location: themes.php?success");
}else{
	if ( isset( $_GET['success'] ) ) {
		$success = true;
		$notice = "<p id='notice'>更新成功。</p>";
	} else {
	}
}

$theme = $_IMC['theme'];
$path = WEBIM_PATH.DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."themes";
$files = webim_scan_subdir($path);
$html = '<h3 id="header-title">主题选择</h3><ul id="themes">';
foreach ($files as $k => $v){
	$t_path = $path.DIRECTORY_SEPARATOR.$v;
	if(is_dir($t_path) && is_file($t_path.DIRECTORY_SEPARATOR."jquery.ui.theme.css")){
		$cur = $v == $theme ? " class='current'" : "";
		$url = '?theme='.$v.'#'.$v;
		$html .= "<li$cur id='$v'><h4><a href='$url'>$v</a></h4><p><a href='$url'><img width=100 height=134 src='../static/themes/images/$v.png' alt='$v' title='$v'/></a></p></li>";
	}
}
$html .= '</ul>';

echo webim_header( '主题选择' );
echo webim_menu( 'themes' );

echo '<div id="content">';
echo $notice;
echo $html;
echo '</div>';
echo webim_footer();
?>
