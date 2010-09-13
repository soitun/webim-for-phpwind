<?php
require_once('lib/webim.class.php');
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'common.php');
require_once("lib/json.php");

$is_login = false;
if(empty($_SGLOBAL['supe_uid'])) {
	$is_login = false;
} else {
	$is_login = true;
	$space = getspace($_SGLOBAL['supe_uid']);
}
if(!$is_login)exit('Login at first.');
$user->uid = $space['uid'];
$user->id = to_utf8($space['username']);
$user->nick = to_utf8($space['username']);
$user->pic_url = avatar($user->uid,"small",true);
$user->default_pic_url=UC_API.'/images/noavatar_small.gif';
$user->show = gp('show') ? gp('show') : "available";
$user->url = "space.php?uid=".$user->uid;

$_SGLOBAL['db']->query("SET NAMES utf8");
$groups = getfriendgroup();
foreach($groups as $k => $v){
	$groups[$k] = to_utf8($v);
}

#connect ucenter db.
include_once(S_ROOT.'./source/class_mysql.php');
$ucdb = new dbstuff;
$ucdb->charset = UC_DBCHARSET;
$ucdb->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME);
$ucdb->query("SET NAMES utf8");


function nick($sp) {
	global $_IMC;
	return (!$_IMC['show_realname']||empty($sp['name'])) ? $sp['username'] : $sp['name'];
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

function im_tname($name) {
	//     return "`webim_".$name."`";
	return UC_DBTABLEPRE."webim_".$name;
}


function online_buddy(){
	global $groups, $user, $_SGLOBAL;
	$list = array();
	$query = $_SGLOBAL['db']->query("SELECT m.uid, m.username, m.name, f.gid 
		FROM ".tname('friend')." f, ".tname('session')." s, ".tname('space')." m
		WHERE f.uid='$user->uid' AND f.fuid = s.uid AND m.uid = s.uid
		ORDER BY f.num DESC, f.dateline DESC");
	while ($value = $_SGLOBAL['db']->fetch_array($query)){
		$list[] = (object)array(
			"uid" => $value['uid'],
			"id" => $value['username'],
			"nick" => nick($value),
			"group" => $groups[$value['gid']],
			"url" => "home.php?mod=space&uid=".$value['uid'],
			'default_pic_url' => UC_API.'/images/noavatar_small.gif',
			"pic_url" => avatar($value['uid'], 'small', true),
		);
	}
	return $list;
}


function complete_status($members){
	global $_SGLOBAL;
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

//$names="licangcai,qiukh"
function buddy($names, $uids = null) {
	global $_SGLOBAL,$user, $groups;
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
	$query = $_SGLOBAL['db']-> query($q="SELECT m.uid, m.username, m.name, f.gid, f.fuid 
		FROM " .tname('space')." m 
		LEFT OUTER JOIN ".tname('friend')." f ON f.uid = '$user->uid' AND m.uid = f.fuid 
		WHERE m.uid <> $user->uid AND $where_sql");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if(empty($value['fuid'])) {
			$group = "stranger";
		}else {
			$gid = $value['gid'];
			$group = (empty($gid) || empty($groups[$gid])) ? "friend" : $groups[$gid];
		}
		$buddies[]=(object)array(
			'uid'=>$value['uid'],
			'id'=> $value['username'],
			'nick'=> nick($value),
			'pic_url' =>avatar($value['uid'],"small",true),
			'status'=>'' ,
			'status_time'=>'',
			'url'=>'space.php?uid='.$value['uid'],
			'group'=> $group,
			'default_pic_url' => UC_API.'/images/noavatar_small.gif');
	}
	return $buddies;
}

function rooms() {
	global $_SGLOBAL,$user;
	$rooms = array();
	$query = $_SGLOBAL['db']->query("SELECT t.tagid, t.membernum, t.tagname, t.pic
		FROM ".tname('tagspace')." main
		LEFT JOIN ".tname('mtag')." t ON t.tagid = main.tagid
		WHERE main.uid = '$user->uid'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$tagid = $value['tagid'];
		$id = $tagid;
		$tagname = $value['tagname'];
		$pic = empty($value['pic']) ? 'image/nologo.jpg' : $value['pic'];
		$rooms[$id]=(object)array('id'=>$id,
			'nick'=> $tagname,
			'pic_url'=>$pic,
			'status'=>'',
			'status_time'=>'',
			'all_count' => $value['membernum'],
			'url'=>'space.php?do=mtag&tagid='.$tagid,
			'count'=>"");
	}
	return $rooms;
}

function new_message_to_histroy() {
	global $user, $ucdb;
	$id = $user->id;
	$ucdb->query("UPDATE ".im_tname('histories')." SET send = 1 WHERE `to`='$id' AND send = 0");
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
	global $user, $ucdb;
	$user_id = $user->id;
	$list = array();
	if($type == "unicast"){
		$query = $ucdb->query("SELECT * FROM ".im_tname('histories')." 
			WHERE `type` = 'unicast' 
			AND ((`to`='$id' AND `from`='$user_id' AND `fromdel` != 1) 
			OR (`send` = 1 AND `from`='$id' AND `to`='$user_id' AND `todel` != 1))  
			ORDER BY timestamp DESC LIMIT 30");
		while ($value = $ucdb->fetch_array($query)){
			array_unshift($list, log_item($value));
		}
	}elseif($type == "multicast"){
		$query = $ucdb->query("SELECT * FROM ".im_tname('histories')." 
			WHERE `to`='$id' AND `type`='multicast' AND send = 1 
			ORDER BY timestamp DESC LIMIT 30");
		while ($value = $ucdb->fetch_array($query)){
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
	global $user, $ucdb;
	$id = $user->id;
	$list = array();
	$query = $ucdb->query("SELECT * FROM ".im_tname('histories')." 
		WHERE `to`='$id' and send = 0 
		ORDER BY timestamp DESC LIMIT 100");
	while ($value = $ucdb->fetch_array($query)){
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
	global $_SGLOBAL,$user, $ucdb;
	if(!empty($_SGLOBAL['supe_uid'])) {
		$setting  = $ucdb->fetch_array($ucdb->query("SELECT * FROM ".im_tname('settings')." WHERE uid='$_SGLOBAL[supe_uid]'"));
		if(empty($setting)) {
			$setting = array('uid'=> $user->uid,'web'=>"");
			$ucdb->query("INSERT INTO ".im_tname('settings')." (uid,web) VALUES ($_SGLOBAL[supe_uid],'')");
		}
		$setting = $setting["web"];
	}
	return json_decode(empty($setting) ? "{}" : $setting);
}

function to_utf8($s) {
	global $_SC;
	if($_SC['charset'] == 'utf-8') {
		return $s;
	} else {
		return  _iconv($_SC['charset'],'utf-8',$s);
	}
}

function from_utf8($s) {
	global $_SC;
	if($_SC['charset'] == 'utf-8') {
		return $s;
	} else {
		return  _iconv('utf-8',$_SC['charset'],$s);
	}
}

?>
