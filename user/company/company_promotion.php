<?php
/*
 * 74cms ��ҵ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"promotion");

if ($act=='tpl')
{
	if (!$cominfo_flge)
	{
	$link[0]['text'] = "��д��ҵ����";
	$link[0]['href'] = 'company_info.php?act=company_profile';
	showmsg("������д������ҵ���ϣ�",1,$link);
	}
	$smarty->assign('title','ѡ��ģ�� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('comtpl',get_comtpl());
	if ($company_profile['tpl']=="")
	{
	$company_profile['tpl']=$_CFG['tpl_company'];
	}
	if($_CFG['operation_mode']=='2' || $_CFG['operation_mode']=='3'){
		$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
		$smarty->assign('setmeal',$setmeal);
	}
	$smarty->assign('user_points',$user_points);
	$smarty->assign('mytpl',$company_profile['tpl']);
	$smarty->assign('com_info',$company_profile['id']);
	$smarty->display('member_company/company_tpl.htm'); 
}
elseif ($act=='tpl_save')
{
	$seltpl=trim($_POST['tpl']);
	if ($company_profile['tpl']=="")
	{
	$company_profile['tpl']=$_CFG['tpl_company'];
	}
	if ($company_profile['tpl']==$seltpl)
	{
	showmsg("���óɹ���",2);
	}
	$comtpl=get_comtpl_one($seltpl);
	if (empty($comtpl))
	{
		showmsg("ģ��ѡ�����",0);
	}
	if($_CFG['operation_mode']=='1'){
		$user_points=get_user_points($_SESSION['uid']);
		if ($comtpl['tpl_val']>$user_points)
		{
			$link[0]['text'] = "������һҳ";
			$link[0]['href'] = 'javascript:history.go(-1)';
			$link[1]['text'] = "��ֵ��«��";
			$link[1]['href'] = 'company_service.php?act=order_add';
			showmsg("���".$_CFG['points_byname']."�������д˴β��������ȳ�ֵ��",1,$link);
		}
	}elseif($_CFG['operation_mode']=='2'||$_CFG['operation_mode']=='3'){
		$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
		$link[0]['text'] = "������һҳ";
		$link[0]['href'] = 'javascript:history.go(-1)';
		$link[1]['text'] = "���¿�ͨ����";
		$link[1]['href'] = 'company_service.php?act=setmeal_list';
		if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{					
			showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
		}
		if ($setmeal['change_templates']=='0')
		{
			showmsg("����ײ�{$setmeal['setmeal_name']},û�������л�ģ���Ȩ�ޣ��뾡�쿪ͨ���ײ�",1,$link);
		}
	}
	$setsqlarr['tpl']=$seltpl;
	$db->updatetable(table('company_profile'),$setsqlarr," uid='{$_SESSION['uid']}'");
	$db->updatetable(table('jobs'),$setsqlarr," uid='{$_SESSION['uid']}'");
	$db->updatetable(table('jobs_tmp'),$setsqlarr," uid='{$_SESSION['uid']}'");
	
 	if($_CFG['operation_mode']=='1'){
		if ($comtpl['tpl_val']>0)
		{
		report_deal($_SESSION['uid'],2,$comtpl['tpl_val']);
		$user_points=get_user_points($_SESSION['uid']);
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"������ҵģ�棺{$comtpl['tpl_name']}��(-{$comtpl['tpl_val']})��(ʣ��:{$user_points})",1,1022,"ѡ��ģ��","-{$comtpl['tpl_val']}","{$user_points}");
		}
	}elseif($_CFG['operation_mode']=='2'||$_CFG['operation_mode']=='3'){
		write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"�ײͣ�{$setmeal['setmeal_name']}���������л�ģ�壬������ҵģ�棺{$comtpl['tpl_name']}",2,1022,"ѡ��ģ��","0","0");
	}
	write_memberslog($_SESSION['uid'],1,8007,$_SESSION['username'],"������ҵģ�棺{$comtpl['tpl_name']}");
	showmsg("���óɹ���",2);
}
unset($smarty);
?>