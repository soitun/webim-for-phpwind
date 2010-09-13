<?php
//$action = !empty($action) ? $action : (isset($uid) || !empty($pmid) ? 'view' : '');
//$systemnewpm = $pmstatus['newpm'] - $pmstatus['newprivatepm'];
//$multipage = multi($ucdata['count'], 10, 1, 'pm.php?filter='.$filter);
//$_COOKIE['checkpm'] && setcookie('checkpm', '', -86400 * 365);
////		$pm['date'] = gmdate($dateformat, $pm['dateline'] + $timeoffset * 3600);
//		$pm['time'] = gmdate($timeformat, $pm['dateline'] + $timeoffset * 3600);
//Array
//(
//    [pmid] => 1
//    [msgfrom] => orez88
//    [msgfromid] => 10
//    [msgtoid] => 1
//    [new] => 1
//    [subject] => kddkdk
//    [dateline] => 1283489424
//    [message] => kddkdk
//    [delstatus] => 0
//    [related] => 0
//    [fromappid] => 1
//    [daterange] => 1
//    [touid] => 10
//)
include_once('common.php');
include_once S_ROOT.'./uc_client/client.php';
$pmlist = array();
$member = $_SGLOBAL['member'];
if($member['notenum']){
$pmlist[]=array("text"=>('<img src="image/icon/notice.gif" width="16" alt="" /><strong>'.$member["notenum"].'</strong> 个新通知'),"link"=>"space.php?do=notice");
}
if($member['pokenum']){
$pmlist[]=array("text"=>('<img src="image/icon/poke.gif" width="16" alt="" /><strong>'.$member["pokenum"].'</strong> 个新招呼'),"link"=>"cp.php?ac=poke");
}
if($member['addfriendnum']){
$pmlist[]=array("text"=>('<img src="image/icon/friend.gif" width="16" alt="" /><strong>'.$member["addfriendnum"].'</strong> 个好友请求'),"link"=>"cp.php?ac=friend&op=request");
}
if($member['mtaginvitenum']){
$pmlist[]=array("text"=>('<img src="image/icon/mtag.gif" width="16" alt="" /><strong>'.$member["mtaginvitenum"].'</strong> 个群组邀请'),"link"=>"cp.php?ac=mtag&op=mtaginvite");
}
if($member['eventinvitenum']){
$pmlist[]=array("text"=>('<img src="image/icon/event.gif" width="16" alt="" /><strong>'.$member["eventinvitenum"].'</strong> 个活动邀请'),"link"=>"cp.php?ac=event&op=eventinvite");
}
if($member['myinvitenum']){
$pmlist[]=array("text"=>('<img src="image/icon/userapp.gif" width="16" alt="" /><strong>'.$member["myinvitenum"].'</strong> 个应用消息'),"link"=>"space.php?do=notice&view=userapp");
}

$pmstatus = uc_pm_checknew($user->uid, 0);
$filter =  'newpm';
$ucdata = uc_pm_list($user->uid, 1, 20, "inbox", "newpm", 150);

foreach($ucdata['data'] as $pm) {
    if ($pm['msgfromid'] > 0) {
        $from=to_utf8($pm['msgfrom']);
        $text=to_utf8($pm['msgfrom']." 给你发了一条消息！");
        $link= 'space.php?do=pm&filter=newpm&uid='.$pm['touid'].'&filter=newpm&daterange='.$pm['daterange'];
    }else {
        $from='';
        $text=to_utf8("系统信息：".$pm['subject']);
        $link= 'space.php?do=pm&filter=newpm?pmid='.$pm['pmid'].'&filter=systempm';
    }
        $pmlist[]= array('from'=>$from,'text'=>$text,'link'=>$link,'time'=>$pm['dateline']);
}



die(json_encode($pmlist));
?>
