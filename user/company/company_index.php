<?php
/*
 * 74cms ��ҵ��Ա����
*/
define('IN_QISHI', true);

require_once(dirname(__FILE__).'/company_common.php');

$smarty->assign('leftmenu',"index");

require_once(QISHI_ROOT_PATH . 'genv/func_company.php');


if ($act=='index')
{
	$uid=intval($_SESSION['uid']);
	$smarty->assign('title','��ҵ��Ա���� - '.$_CFG['site_name']);
	//��ҳ������ʾ��Ϣ(�ײͻ��ߺ�«����ʧЧ���ʧЧʱ����)
	$message = array();
	if ($_CFG['operation_mode']=='1' || $_CFG['operation_mode']=='3')
	{
		$my_points = get_user_points($uid);
		if($my_points < $_CFG['points_min_remind'] && intval($my_points) > 0 && !empty($_CFG['points_min_remind']))
		{
			$message[] = '���ѣ����ĺ�«�Ҳ��㣬Ϊ������ɲ���Ҫ���鷳����<a href="company_service.php?act=order_add">������ֵ</a>';
		}
		elseif(intval($my_points) <= 0 && !empty($_CFG['points_min_remind']))
		{
			$message[] = '���ѣ����ĺ�«����Ϊ0��Ϊ������ɲ���Ҫ���鷳����<a href="company_service.php?act=order_add">������ֵ</a>';
		}
		$smarty->assign('points',$my_points);
	}
	if($_CFG['operation_mode']=='2' || $_CFG['operation_mode']=='3')
	{
		$my_setmeal = get_user_setmeal($uid);
		if(time()>$my_setmeal['endtime'] && $my_setmeal['endtime'] > 0 && !empty($_CFG['meal_min_remind'])){
			$message[] = '���ѣ������ײ��ѵ��ڣ�Ϊ������ɲ���Ҫ���鷳����<a href="company_service.php?act=setmeal_list" target="_blank">�����ײ�</a>';
		}
		elseif(($my_setmeal['endtime']-time())/86400 <=$_CFG['meal_min_remind']  && $my_setmeal['endtime'] > 0 && !empty($_CFG['meal_min_remind']))
		{
			$message[] = '���ѣ������ײͿ쵽�ڣ�Ϊ������ɲ���Ҫ���鷳����<a href="company_service.php?act=setmeal_list" target="_blank">�����ײ�</a>';
		}
		$smarty->assign('setmeal',$my_setmeal);
	}
	$smarty->assign('message',$message);
	$smarty->assign('company',$company_profile);
	//��¼ʱ��
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	//�ж��Ƿ����Լ���¼�Ļ��Ǻ�̨����Ա��¼��
	if($_SESSION['no_self'])
	{
		$smarty->assign('loginlog',get_loginlog_two($uid,'1001'));
	}
	else
	{
		$smarty->assign('loginlog',get_loginlog_one($uid,'1001'));
	}
	$smarty->assign('user',$user);
	//ͳ��ְλ��
	$smarty->assign('total_audit_jobs',$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs')." WHERE uid=".$uid));
	$smarty->assign('total_noaudit_jobs',$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE uid=".$uid." AND audit=2"));
	$smarty->assign('total_nolook_resume',$db->get_total("SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply')." WHERE company_uid=".$uid." AND personal_look=1"));
	$smarty->assign('total_down_resume',$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_down_resume')." WHERE company_uid=".$uid));
	$smarty->assign('total_view_resume',$db->get_total("SELECT COUNT(*) AS num FROM ".table('view_resume')." WHERE uid=".$uid));
	$smarty->assign('total_favorites_resume',$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_favorites')." WHERE company_uid=".$uid));
	//�Ƽ�����
	$smarty->assign('concern_id',get_concern_id($uid));
	//��Ϣ����
	$smarty->assign('msg_total1',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='2' AND `replyuid`<>'{$uid}'"));
	$smarty->assign('msg_total2',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1' AND `replyuid`<>'{$uid}'"));
	$smarty->display('member_company/company_index.htm');
}
unset($smarty);
?>