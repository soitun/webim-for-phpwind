<?php
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'global.php');
require_once('lib/webim.class.php');

/** is user login */
$is_login = $GLOBALS['groupid'] != 'guest' && $GLOBALS['groupid'] != '';
if ( !$is_login ) {
	exit('"Please login at first"');
}

/** is admin */
$is_admin = CkInArray($windid, $manager) || $SYSTEM['allowadmincp'];

/** database charset */
$charset = $charset;

/** database table prefix*/
$dbpre = $PW;

/** database connection*/
$db = $db;

/** userinfo*/
$user = (object)array(
	'uid' => $winddb['uid'],
	'id' => to_utf8( $winddb['username'] ),
	'nick' => to_utf8( $winddb['username'] ),
	'pic_url' => avatar( $winddb['icon'] ),
	'show' => gp('show') ? gp('show') : "available",
	'url' => profile_url( $winddb['uid'] ),
);

$db->query("SET NAMES utf8");

function nick($sp) {
	return $sp['username'];
}

function ids_array($ids) {
	return ($ids===NULL || $ids==="") ? array() : (is_array($ids) ? array_unique($ids) : array_unique(split(",", $ids)));
}
function ids_except($id, $ids) {
	if(in_array($id, $ids)) {
		array_splice($ids, array_search($id, $ids), 1);
	}
	return $ids;
}

function tname($name) {
	global $dbpre;
	return $dbpre.$name;
}

function im_tname($name) {
	global $dbpre;
	return $dbpre."webim_".$name;
}


function online_buddy(){
	global $groups, $user, $db;
	$list = array();
	$query = $db->query("SELECT m.uid, m.username, m.icon
		FROM ".tname('friends')." f, ".tname('members')." m
		WHERE f.uid='$user->uid' AND f.friendid = m.uid");
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
	return $list;
}


function complete_status($members){
	return $members;
	global $db;
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
		$query = $db-> query($q="SELECT t.uid, t.message FROM " . tname("doing") . " t 
			INNER JOIN (SELECT max(doid) doid 
			FROM " . tname("doing") . "  
			WHERE uid IN ($ids)
			GROUP BY uid) t2 
			ON t2.doid = t.doid;");
		while ($value = $db->fetch_array($query)) {
			$ob[$value['uid']]->status = $value['message'];
		}
	}
	return $members;
}

//$names="licangcai,qiukh"
function buddy($names, $uids = null) {
	global $db,$user, $groups;
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
	$buddies = array();
	$query = $db-> query($q="SELECT m.uid, m.username, m.icon, f.friendid fuid 
		FROM " .tname('members')." m 
		LEFT OUTER JOIN ".tname('friends')." f ON f.uid = '$user->uid' AND m.uid = f.friendid 
		WHERE m.uid <> $user->uid AND $where_sql");
	while ($value = $db->fetch_array($query)) {
		if(empty($value['fuid'])) {
			$group = "stranger";
		}else {
			$group = "friend";
		}
		$buddies[]=(object)array(
			"uid" => $value['uid'],
			"id" => $value['username'],
			"nick" => nick($value),
			"group" => 'friend',
			"url" => profile_url($value['uid']),
			"pic_url" => avatar($value['icon']),
		);
	}
	return $buddies;
}

function room() {
	global $db, $user;
	$rooms = array();
	$query = $db->query("SELECT t.id, t.members, t.cname, t.cnimg
		FROM ".tname('cmembers')." main
		LEFT JOIN ".tname('colonys')." t ON t.id = main.colonyid
		WHERE main.uid = '$user->uid'");
	while ($value = $db->fetch_array($query)) {
		$pic = empty($value['cnimg']) ? 'webim/static/images/group.gif' : "attachment/cn_img/".$value['cnimg'];
		$rooms[$id]=(object)array(
			'id'=>$value['id'],
			'nick'=> $value['cname'],
			'pic_url'=>$pic,
			'all_count' => $value['members'],
			'url'=>"mode.php?m=o&q=group&cyid=$value[id]",
			'count'=>"");
	}
	return $rooms;
}

function new_message_to_histroy() {
	global $user, $db;
	$id = $user->id;
	$db->query("UPDATE ".im_tname('histories')." SET send = 1 WHERE `to`='$id' AND send = 0");
}

/**
 * Get history message
 *
 * @param string $type unicast or multicast
 * @param string $id
 *
 * Example:
 * 	history('unicast', 'webim');
 * 	history('multicast', '36');
 *
 */

function history($type, $id){
	global $user, $db;
	$user_id = $user->id;
	$list = array();
	if($type == "unicast"){
		$query = $db->query("SELECT * FROM ".im_tname('histories')." 
			WHERE `type` = 'unicast' 
			AND ((`to`='$id' AND `from`='$user_id' AND `fromdel` != 1) 
			OR (`send` = 1 AND `from`='$id' AND `to`='$user_id' AND `todel` != 1))  
			ORDER BY timestamp DESC LIMIT 30");
		while ($value = $db->fetch_array($query)){
			array_unshift($list, log_item($value));
		}
	}elseif($type == "multicast"){
		$query = $db->query("SELECT * FROM ".im_tname('histories')." 
			WHERE `to`='$id' AND `type`='multicast' AND send = 1 
			ORDER BY timestamp DESC LIMIT 30");
		while ($value = $db->fetch_array($query)){
			array_unshift($list, log_item($value));
		}
	}else{
	}
	return $list;
}

/**
 * Get new message
 *
 */

function new_message() {
	global $user, $db;
	$id = $user->id;
	$list = array();
	$query = $db->query("SELECT * FROM ".im_tname('histories')." 
		WHERE `to`='$id' and send = 0 
		ORDER BY timestamp DESC LIMIT 100");
	while ($value = $db->fetch_array($query)){
		array_unshift($list, log_item($value));
	}
	return $list;
}

function log_item($value){
	return (object)array(
		'to' => $value['to'],
		'nick' => $value['nick'],
		'from' => $value['from'],
		'style' => $value['style'],
		'body' => $value['body'],
		'type' => $value['type'],
		'timestamp' => $value['timestamp']
	);
}

function setting() {
	global $user, $db;
	if(!empty($user->uid)) {
		$setting  = $db->fetch_array($db->query("SELECT * FROM ".im_tname('settings')." WHERE uid='$user->uid'"));
		if(empty($setting)) {
			$setting = array('uid'=> $user->uid,'web'=>"");
			$db->query("INSERT INTO ".im_tname('settings')." (uid,web) VALUES ($user->uid,'')");
		}
		$setting = $setting["web"];
	}
	return json_decode(empty($setting) ? "{}" : $setting);
}

function to_utf8( $s ) {
	global $charset;
	if($charset == 'utf-8') {
		return $s;
	} else {
		return  _iconv($charset,'utf-8',$s);
	}
}

function from_utf8( $s ) {
	global $charset;
	if($charset == 'utf-8') {
		return $s;
	} else {
		return  _iconv('utf-8',$charset,$s);
	}
}

function avatar ($icon) {
	require_once(R_P.'require/showimg.php');
	$pic_url = showfacedesign($icon, true);
	return $pic_url[0];
}

function profile_url( $uid ) {
	return "u.php?action=show&uid=$uid";
}

function firend_group( $id ) {

	return 'friend';
}

?>
