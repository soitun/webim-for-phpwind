<?php

require_once( dirname( __FILE__ ) . '/' . 'admin_common.php' );
webim_only_for_admin();

webim_update_config( false );

$unwritable_paths = webim_select_unwritable_path( true );
$msg = "";
$success = false;

if ( !empty( $unwritable_paths ) ){

	$msg = webim_unwritable_log( $unwritable_paths );
	include_once( '_error.php' );
	exit();

} elseif ( !$imdb->ready ) {

	$msg = '<div class="box"><div class="box-c">不能连接数据库。</div></div>';
	include_once( '_error.php' );
	exit();

}
if( isset( $_POST['host'] ) && isset( $_POST['domain'] ) && isset( $_POST['apikey'] ) ){
	webim_update_config();
	webim_insert_template();
	webim_update_db();
	webim_clean_cache();
	header("Location: in.php?success");
}else{
	if ( isset( $_GET['success'] ) ) {
		$success = true;
		$msg = <<<EOF
	<div class="box">
		<h3>WebIM安装完成</h3>
		<div class="box-c">
			<p class="box-desc">你可以</p>
			<p>
				<a href="index.php">进入WebIM管理首页</a>
			</p>
			<p>
				<a href="faq.php#install">查看WebIM安装说明</a>
			</p>
		</div>
	</div>
EOF;
	} else {
		$host = $_IMC['host'];
		$domain = $_IMC['domain'];
		$apikey = $_IMC['apikey'];
		$errors = array();
		$err = "";
		$err_c = "";
		if(!empty($errors)){
			$err_c = " box-error";
			$err = "<ul class=\"error\"><li>".implode($errors, "</li><li>")."</li></ul>";
		}
		$msg = <<<EOF
		<div class="box$err_c">
		<h3>设置安装信息</h3>
		<div class="box-c">
			<p class="box-desc">请先到<a href="http://www.webim20.cn" target="_blank">webim20.cn</a>注册apikey网站注册</p>
			$err
			<form action="" method="post" class="form">
				<p class="clearfix"><label for="host">服务器地址：</label><input class="text" type="text" id="host" value="$host" name="host"/><span class="help">IM服务器地址，国内im.webim20.cn，国外im.webim.us，独立版用户使用自己的服务器地址</span></p>
				<p class="clearfix"><label for="domain">注册域名：</label><input class="text" type="text" id="domain" value="$domain" name="domain"/><span class="help">网站注册域名</span></p>
				<p class="clearfix"><label for="apikey">注册apikey：</label><input class="text" type="text" id="apikey" value="$apikey" name="apikey"/></p>
				<p class="actions clearfix"><input type="submit" class="submit" value="提交" /></p>
			</form>
		</div>
	</div>
EOF;
	}
}

echo webim_header( '安装' );

echo $success ? webim_menu( '' ) . "<div id=content>$msg</div>" : $msg;

echo webim_footer();

?>
