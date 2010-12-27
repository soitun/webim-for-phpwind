<?php

require_once( dirname( __FILE__ ) . '/' . 'admin_common.php' );

webim_only_for_admin();

$unwritable_paths = webim_select_unwritable_path( true );
$msg = "";
$success = false;

if ( !empty($unwritable_paths ) ){

	$msg = webim_unwritable_log( $unwritable_paths );
	include_once( '_error.php' );
	exit();

} elseif ( !$imdb->ready ) {

	$msg = '<div class="box"><div class="box-c">不能连接数据库。</div></div>';
	include_once( '_error.php' );
	exit();

}
if( isset( $_POST['uninstall'] ) ){
	webim_remove_template();
	webim_clean_cache();
	header("Location: uninstall.php?success");
}else{
	if ( isset( $_GET['success'] ) ) {
		$msg = <<<EOF
	<div class="box">
		<h3>WebIM卸载完成</h3>
		<div class="box-c">
			<p class="box-desc">你可以</p>
			<p>
				<a href="in.php">重新安装WebIM</a>
			</p>
		</div>
	</div>
EOF;
	} else {
		$msg = <<<EOF
		<div class="box">
			<h3>卸载WebIM</h3>
			<div class="box-c">
			<p class="box-desc">卸载WebIM会自动从模板文件中删除webim代码, 但不会删除数据库中的数据<br /><br /> 真的要卸载吗？</p>
			<form action="" method="post" class="form">
			<p><input class="text" type="hidden" value="1" name="uninstall"/></p>
			<p class="actions"><input type="submit" class="submit" value="卸载" /></p>
			</form>
			</div>
			</div>
EOF;
	}
}

echo webim_header( '卸载' );

echo webim_menu( 'uninstall' );

echo '<div id="content">' . $msg . '</div>';

echo webim_footer();

?>
