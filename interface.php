<?php

/** 
 * Custom interface 
 *
 * Provide 
 *
 * define WEBIM_PRODUCT_NAME
 * array $_IMC
 * boolean $im_is_admin
 * boolean $im_is_login
 * object $imuser require when $im_is_login
 * function webim_get_menu() require when !$_IMC['disable_menu']
 * function webim_get_buddies()
 * function webim_get_online_buddies()
 * function webim_get_rooms()
 * function webim_get_notifications()
 * function webim_login()
 *
 */

define( 'WEBIM_PRODUCT_NAME', 'phpwind' );

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'global.php');
include(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'data/sql_config.php');

//Find and insert data with utf8 client.
@$db->query("SET NAMES utf8");

@include_once( 'config.php' );

/**
 *
 * Provide the webim database config.
 *
 * $_IMC['dbuser'] MySQL database user
 * $_IMC['dbpassword'] MySQL database password
 * $_IMC['dbname'] MySQL database name
 * $_IMC['dbhost'] MySQL database host
 * $_IMC['dbtable_prefix'] MySQL database table prefix
 * $_IMC['dbcharset'] MySQL database charset
 *
 */

/** uchome db */
$_IMC['dbuser'] = $dbuser;
$_IMC['dbpassword'] = $dbpw;
$_IMC['dbname'] = $dbname;
$_IMC['dbhost'] = $dbhost;
$_IMC['dbtable_prefix'] = $PW;
$_IMC['dbcharset'] = $charset;

//$_SC['charset'] is utf-8

/**
 * Init im user.
 * 	-uid:
 * 	-id:
 * 	-nick:
 * 	-pic_url:
 * 	-show:
 *
 */

#$site_url = dirname( webim_urlpath() ) . "/";
$site_url = "";

if($GLOBALS['groupid'] == 'guest' || $GLOBALS['groupid'] == '') {
	$im_is_login = false;
} else {
	$im_is_login = true;
	webim_set_user();
}

function profile_url( $id ) {
	global $site_url;
	return $site_url . "u.php?action=show&uid=" . $id;
}

function avatar ($icon) {
	require_once(R_P.'require/showimg.php');
	$pic_url = showfacedesign($icon, true);
	return $pic_url[0];
}

function tname($name) {
	global $PW;
	return $PW.$name;
}

function webim_set_user() {
	global $winddb, $imuser, $im_is_admin, $SYSTEM, $windid, $manager;
	$imuser->uid = $winddb['uid'];
	$imuser->id = to_utf8($winddb['username']);
	$imuser->nick = to_utf8($winddb['username']);
	$imuser->pic_url = avatar( $winddb['icon'] );
	# $imuser->default_pic_url = UC_API.'/images/noavatar_small.gif';
	$imuser->show = webim_gp('show') ? webim_gp('show') : "available";
	$imuser->url = profile_url( $imuser->uid );
	complete_status( array( $imuser ) );
	if( CkInArray($windid, $manager) || $SYSTEM['allowadmincp'] ){
		$im_is_admin = true;
	} else {
		$im_is_admin = false;
	}
}

function webim_login( $username, $password, $question = "", $answer = "" ) {
	return false;
	global $imuser, $_SGLOBAL, $im_is_login;
	$username = from_utf8( $username );
	include_once(S_ROOT.'./source/function_cp.php');
	$cookietime = intval($_POST['cookietime']);
	$cookiecheck = $cookietime?' checked':'';
	$membername = $username;

	if(empty($username)) {
		return false;
	}

	//同步获取用户源
	if(!$passport = getpassport($username, $password)) {
		return false;
	}

	$setarr = array(
		'uid' => $passport['uid'],
		'username' => addslashes($passport['username']),
		'password' => md5("$passport[uid]|$_SGLOBAL[timestamp]")//本地密码随机生成
	);

	include_once(S_ROOT.'./source/function_space.php');
	//开通空间
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('space')." WHERE uid='$setarr[uid]'");
	if(!$space = $_SGLOBAL['db']->fetch_array($query)) {
		$space = space_open($setarr['uid'], $setarr['username'], 0, $passport['email']);
	}

	$_SGLOBAL['member'] = $space;

	//实名
	realname_set($space['uid'], $space['username'], $space['name'], $space['namestatus']);

	//检索当前用户
	$query = $_SGLOBAL['db']->query("SELECT password FROM ".tname('member')." WHERE uid='$setarr[uid]'");
	if($value = $_SGLOBAL['db']->fetch_array($query)) {
		$setarr['password'] = addslashes($value['password']);
	} else {
		//更新本地用户库
		inserttable('member', $setarr, 0, true);
	}

	//清理在线session
	insertsession($setarr);

	//设置cookie
	ssetcookie('auth', authcode("$setarr[password]\t$setarr[uid]", 'ENCODE'), $cookietime);
	ssetcookie('loginuser', $passport['username'], 31536000);
	ssetcookie('_refer', '');

	//同步登录
	if($_SCONFIG['uc_status']) {
		include_once S_ROOT.'./uc_client/client.php';
		$ucsynlogin = uc_user_synlogin($setarr['uid']);
	} else {
		$ucsynlogin = '';
	}
	realname_get();
	$im_is_login = true;
	webim_set_user();
	return true;
}

//Cache friend_groups;
$friend_groups = array();
foreach($friend_groups as $k => $v){
	$friend_groups[$k] = to_utf8($v);
}

/**
 * Online buddy list.
 *
 */
function webim_get_online_buddies() {
	global $friend_groups, $imuser, $db;
	$list = array();
	$query = $db->query("SELECT m.uid, m.username, m.icon
		FROM ".tname('friends')." f, ".tname('members')." m
		WHERE f.uid='$imuser->uid' AND f.friendid = m.uid");
	while ($value = $db->fetch_array($query)){
		$list[] = (object)array(
			"uid" => $value['uid'],
			"id" => $value['username'],
			"nick" => nick($value),
			"group" => 'friend',
			"url" => profile_url($value['uid']),
			"pic_url" => avatar($value['icon']),
		);
	}
	complete_status( $list );
	return $list;
}

/**
 * Get buddy list from given ids
 * $ids:
 *
 * Example:
 * 	buddy('admin,webim,test');
 *
 */

function webim_get_buddies( $names, $uids = null ) {
	global $db, $imuser, $friend_groups;
	$where_name = "";
	$where_uid = "";
	if(!$names and !$uids)return array();
	if($names){
		$names = "'".implode("','", explode(",", $names))."'";
		$where_name = "m.username IN ($names)";
	}
	if($uids){
		$where_uid = "m.uid IN ($uids)";
	}
	$where_sql = $where_name && $where_uid ? "($where_name OR $where_uid)" : ($where_name ? $where_name : $where_uid);
	$list = array();
	$query = $db-> query($q="SELECT m.uid, m.username, m.icon, f.friendid fuid 
		FROM " .tname('members')." m 
		LEFT OUTER JOIN ".tname('friends')." f ON f.uid = '$imuser->uid' AND m.uid = f.friendid 
		WHERE m.uid <> $imuser->uid AND $where_sql");
	while ($value = $db->fetch_array($query)) {
		if(empty($value['fuid'])) {
			$group = "stranger";
		}else {
			$group = "friend";
		}
		$list[]=(object)array(
			"uid" => $value['uid'],
			"id" => $value['username'],
			"nick" => nick($value),
			"group" => 'friend',
			"url" => profile_url($value['uid']),
			"pic_url" => avatar($value['icon']),
		);
	}
	complete_status( $list );
	return $list;
}

/**
 * Get room list
 * $ids: Get all imuser rooms if not given.
 *
 */

function webim_get_rooms($ids=null) {
	global $db,$imuser, $site_url;
	$rooms = array();
	$query = $db->query("SELECT t.id, t.members, t.cname, t.cnimg
		FROM ".tname('cmembers')." main
		LEFT JOIN ".tname('colonys')." t ON t.id = main.colonyid
		WHERE main.uid = '$imuser->uid'");
	while ($value = $db->fetch_array($query)) {
		$pic = empty($value['cnimg']) ? 'webim/static/images/group.gif' : "attachment/cn_img/".$value['cnimg'];
		$rooms[$id]=(object)array(
			'id'=>$value['id'],
			'nick'=> $value['cname'],
			'pic_url'=>$pic,
			'all_count' => $value['members'],
			'url'=> $site_url."mode.php?m=o&q=group&cyid=$value[id]",
			'count'=>"");
	}
	return $rooms;
}

function webim_get_notifications(){
	global $_SGLOBAL, $site_url;
	return array();
}

function webim_get_menu() {
	global $_SCONFIG, $_SGLOBAL, $site_url;
	$menu = array(
		#array("title" => 'doing',"icon" =>$site_url . "image/app/doing.gif","link" => $site_url . "space.php?do=doing"),
	);
	return $menu;
}

/**
 * Add status to member info.
 *
 * @param array $members the member list
 * @return 
 *
 */
function complete_status( $members ) {
	global $_SGLOBAL;
	return $members;
	if(!empty($members)){                
		$num = count($members);                
		$ids = array();
		$ob = array();
		for($i = 0; $i < $num; $i++){
			$m = $members[$i];
			$id = $m->uid;
			if ( $id ) {
				$ids[] = $id;
				$ob[$id] = $m;
			}
		}
		$ids = implode(",", $ids);
		$query = $_SGLOBAL['db']-> query($q="SELECT t.uid, t.message FROM " . tname("doing") . " t 
			INNER JOIN (SELECT max(doid) doid 
			FROM " . tname("doing") . "  
			WHERE uid IN ($ids)
			GROUP BY uid) t2 
			ON t2.doid = t.doid;");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$ob[$value['uid']]->status = $value['message'];
		}
	}
	return $members;
}

function nick( $sp ) {
	global $_IMC;
	return $sp['username'];
	#return (!$_IMC['show_realname']||empty($sp['name'])) ? $sp['username'] : $sp['name'];
}

function to_utf8( $s ) {
	global $charset;
	if( strtoupper( $charset ) == 'UTF-8' ) {
		return $s;
	} else {
		if ( function_exists( 'iconv' ) ) {
			return iconv( $charset, 'utf-8', $s );
		} else {
			require_once 'class_chinese.php';
			$chs = new Chinese( $charset, 'utf-8' );
			return $chs->Convert( $s );
		}
	}
}

function from_utf8( $s ) {
	global $charset;
	if( strtoupper( $charset ) == 'UTF-8' ) {
		return $s;
	} else {
		if ( function_exists( 'iconv' ) ) {
			return iconv( 'utf-8', $charset, $s );
		} else {
			require_once 'class_chinese.php';
			$chs = new Chinese( 'utf-8', $charset );
			return $chs->Convert( $s );
		}
	}
}

