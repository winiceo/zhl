<?php
/*
 * 74cms ��ѵ������Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/train_common.php');
$smarty->assign('leftmenu',"index");
if ($act=='index')
{
	$uid=intval($_SESSION['uid']);
	$smarty->assign('title','��ѵ��Ա���� - '.$_CFG['site_name']);
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
						$setmeal_endtime_days="�ѵ���,������������";
					}
					 else
					 {
						 $setmeal_endtime_days="����".$setmeal_endtime."����";
					 }
					$smarty->assign('setmeal_endtime_days',$setmeal_endtime_days);
					if (time()>$setmeal['endtime'])
					{
						$smarty->assign('meal_min_remind',"�Ѿ�����");
					}
					else
					{
						$smarty->assign('meal_min_remind',$setmeal_endtime);
					}
		
		}else{
			$smarty->assign('meal_min_remind',"������");
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