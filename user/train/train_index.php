<?php
/*
 * 74cms 培训机构会员中心
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/train_common.php');
$smarty->assign('leftmenu',"index");
if ($act=='index')
{
	$uid=intval($_SESSION['uid']);
	$smarty->assign('title','培训会员中心 - '.$_CFG['site_name']);
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$smarty->assign('loginlog',get_loginlog_one($uid,'1001'));
	$smarty->assign('user',$user);
	$smarty->assign('points',get_user_points($uid));
	$smarty->assign('train',$train_profile);
	if ($_CFG['operation_train_mode']=='2')
	{
		$setmeal=get_user_setmeal($uid);
		$smarty->assign('setmeal',$setmeal);
		if ($setmeal['endtime']>0)
		{
					$setmeal_endtime=sub_day($setmeal['endtime'],time());
					if (empty($setmeal_endtime))
					{
						$setmeal_endtime_days="已到期,请您重新申请";
					}
					 else
					 {
						 $setmeal_endtime_days="还有".$setmeal_endtime."到期";
					 }
					$smarty->assign('setmeal_endtime_days',$setmeal_endtime_days);
					if (time()>$setmeal['endtime'])
					{
						$smarty->assign('meal_min_remind',"已经到期");
					}
					else
					{
						$smarty->assign('meal_min_remind',$setmeal_endtime);
					}
		
		}else{
			$smarty->assign('meal_min_remind',"无限期");
		}
	}
	$smarty->assign('msg_total1',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1' AND `replyuid`<>'{$uid}' AND msgtype=1"));
	$smarty->assign('msg_total2',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1' AND `replyuid`<>'{$uid}' AND msgtype=2"));
	$smarty->assign('total_noaudit_course',$db->get_total("SELECT COUNT(*) AS num FROM ".table('course')." WHERE uid=".$uid." AND audit=2"));
	$smarty->assign('total_audit_course',$db->get_total("SELECT COUNT(*) AS num FROM ".table('course')." WHERE uid=".$uid." AND audit=1"));
	$smarty->assign('total_teacher',$db->get_total("SELECT COUNT(*) AS num FROM ".table('train_teachers')." WHERE uid=".$uid));
	$smarty->assign('total_nolook_apply',$db->get_total("SELECT COUNT(*) AS num FROM ".table('personal_course_apply')." WHERE train_uid=".$uid." AND personal_look=1"));
	$smarty->display('member_train/train_index.htm');
}
unset($smarty);
?>