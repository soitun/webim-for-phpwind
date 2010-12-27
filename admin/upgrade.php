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

if ( isset( $_GET['success'] ) ) {
	$success = true;
	$msg = <<<EOF
	<div class="box">
		<h3>WebIM更新完成</h3>
		<div class="box-c">
			<p class="box-desc">你可以</p>
			<p>
				<a href="index.php">进入WebIM管理首页</a>
			</p>
			<p>
				<a href="changelog.php">查看WebIM更新说明</a>
			</p>
		</div>
	</div>
EOF;
} else {
	webim_update_config();
	webim_insert_template();
	webim_update_db();
	webim_clean_cache();
	header("Location: upgrade.php?success");
	exit();
}

echo webim_header( '升级' );

echo $success ? webim_menu( '' ) . "<div id=content>$msg</div>" : $msg;

echo webim_footer();

?>
