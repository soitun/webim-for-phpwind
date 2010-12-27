<?php

require_once( dirname( __FILE__ ) . '/' . 'admin_common.php' );

webim_only_for_admin();

$unwritable_paths = webim_select_unwritable_path( false );

$msg = "";
$success = false;

if ( !$imdb->ready ) {

	$msg = '<div class="box"><div class="box-c">不能连接数据库。</div></div>';
	include_once( '_error.php' );
	exit();

}

if( isset( $_POST['clear'] ) ){
	switch ( $_POST['period'] ) {
	case 'weekago':
		$ago = 7*24*60*60;break;
	case 'monthago':
		$ago = 30*24*60*60;break;
	case '3monthago':
		$ago = 3*30*24*60*60;break;
	default:
		$ago = 0;
	}
	$ago = ( time() - $ago ) * 1000;
	$imdb->query( $imdb->prepare( "DELETE FROM $imdb->webim_histories WHERE `timestamp` < %s", $ago ) );
	header("Location: histories.php?success");
	exit();
}else{
	if ( isset( $_GET['success'] ) ) {
		$success = true;
		$notice = "<p id='notice'>清理成功。</p>";
	} else {
	}
}

$count = $imdb->get_var( $imdb->prepare( "SELECT count(*) FROM $imdb->webim_histories" ) );

echo webim_header( '聊天记录清理' );
echo webim_menu( 'histories' );
?>
<div id="content">
<?php echo $notice ?>
		<div class="box">
			<h3>聊天记录清理</h3>
			<div class="box-c">
			<p class="box-desc">网站目前有聊天记录<?php echo $count ?>条</p>
			<form action="" method="post" class="form">
			<p><input class="text" type="hidden" value="1" name="clear"/></p>
			<p class="clearfix"><label></label>
<select name="period">
<option value="all">清理所有记录</option>
<option value="weekago">清理一周前的记录</option>
<option value="monthago">清理一个月前的记录</option>
<option value="3monthago">清理三个月前的记录</option>
</select>
</p>
			<p class="actions"><input type="submit" class="submit" value="确认" /></p>
			</form>
			</div>
			</div>
</div>
<?php
echo webim_footer();
?>
