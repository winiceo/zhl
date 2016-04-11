<?php
 /*
 * 74cms ��ҵ����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_company_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set';
$smarty->assign('pageheader',"��ҵ����");
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"set_com");
if($act == 'set')
{	
	get_token();
	$smarty->assign('navlabel',"set");
	$smarty->assign('config',$_CFG);
	$smarty->assign('text',get_cache('text'));
	$smarty->display('set_com/admin_set_com.htm');
}
elseif($act == 'set_save')
{
	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k'")?adminmsg('��������ʧ��', 1):"";
	}
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('text')." SET value='$v' WHERE name='$k'")?adminmsg('��������ʧ��', 1):"";
	}
	refresh_cache('config');
	refresh_cache('text');	
	//��д����Ա��־
	write_log("��̨�ɹ�������������", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2);
}
elseif($act == 'modeselect')
{
	get_token();
	$smarty->assign('navlabel',"modeselect");
	$smarty->display('set_com/admin_mode.htm');
}
elseif($act == 'modeselect_save')
{
 	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k' LIMIT 1")?adminmsg('����ʧ��', 1):"";
	}
	refresh_cache('config');
	//��д����Ա��־
	write_log("��̨�ɹ���������", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2);
}
elseif($act == 'set_points')
{
	get_token();
	$smarty->assign('config',$_CFG);
	$smarty->assign('points',get_points_rule());
	$smarty->assign('navlabel',"set_points");
	$smarty->display('set_com/admin_mode_points.htm');
}
elseif($act == 'set_points_save')
{
	check_token();
	$ids=$_POST['id'];
	$operation=$_POST['operation'];
	$value=$_POST['value'];
	foreach($ids as $k =>  $id)
	{
	$id=intval($id);
	!$db->query("UPDATE ".table('members_points_rule')." SET value='{$value[$k]}', operation='{$operation[$k]}' WHERE id='{$id}' LIMIT 1")?adminmsg('����ʧ��', 1):"";
	}
	refresh_points_rule_cache();
	//��д����Ա��־
	write_log("��̨�ɹ����º�«�ҹ���", $_SESSION['admin_name'],3);
	adminmsg("�������óɹ���",2);
}
elseif($act == 'set_points_config_save')
{
	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k' LIMIT 1")?adminmsg('����ʧ��', 1):"";
	}
	refresh_cache('config');
	adminmsg("����ɹ���",2);
}
elseif($act == 'set_meal')
{
	get_token();
	$smarty->assign('setmeal',get_setmeal());
	$smarty->assign('givesetmeal',get_setmeal(false));
	$smarty->assign('navlabel',"set_meal");
	$smarty->display('set_com/admin_mode_meal.htm');
}
elseif($act == 'set_meal_add')
{
	get_token();
	$smarty->assign('setmeal',get_setmeal());
	$smarty->assign('navlabel',"set_meal");
	$smarty->display('set_com/admin_mode_meal_add.htm');
}
elseif($act == 'set_meal_add_save')
{
	check_token();
	$setsqlarr['setmeal_name']=trim($_POST['setmeal_name'])?trim($_POST['setmeal_name']):adminmsg('�ײ����Ʋ���Ϊ�գ�',1);
	$setsqlarr['days']=intval($_POST['days']);
	$setsqlarr['original_price']=intval($_POST['original_price']);
	$setsqlarr['expense']=intval($_POST['expense']);
	$setsqlarr['jobs_ordinary']=intval($_POST['jobs_ordinary']);
	$setsqlarr['download_resume_ordinary']=intval($_POST['download_resume_ordinary']);
	$setsqlarr['download_resume_senior']=intval($_POST['download_resume_senior']);
	$setsqlarr['interview_ordinary']=intval($_POST['interview_ordinary']);
	$setsqlarr['interview_senior']=intval($_POST['interview_senior']);
	$setsqlarr['talent_pool']=intval($_POST['talent_pool']);

	$setsqlarr['recommend_num']=intval($_POST['recommend_num']);
	$setsqlarr['recommend_days']=intval($_POST['recommend_days']);
	$setsqlarr['stick_num']=intval($_POST['stick_num']);
	$setsqlarr['stick_days']=intval($_POST['stick_days']);
	$setsqlarr['emergency_num']=intval($_POST['emergency_num']);
	$setsqlarr['emergency_days']=intval($_POST['emergency_days']);
	$setsqlarr['highlight_num']=intval($_POST['highlight_num']);
	$setsqlarr['highlight_days']=intval($_POST['highlight_days']);
	$setsqlarr['change_templates']=intval($_POST['change_templates']);
	$setsqlarr['jobsfair_num']=intval($_POST['jobsfair_num']);
	$setsqlarr['map_open']=intval($_POST['map_open']);

	$setsqlarr['show_order']=intval($_POST['show_order']);
	$setsqlarr['display']=intval($_POST['display']);
	$setsqlarr['apply']=intval($_POST['apply']);
	$setsqlarr['added']=trim($_POST['added']);
	/**
	 * 2014-01-26����start
	 */
	$setsqlarr['refresh_jobs_space']=intval($_POST['refresh_jobs_space']);
	$setsqlarr['refresh_jobs_time']=intval($_POST['refresh_jobs_time']);
	//2015-01-09н�ʶ������� set_sms 
	$setsqlarr['set_sms'] = intval($_POST['set_sms']);
	$id = $db->inserttable(table('setmeal'),$setsqlarr,true);
	if ($id)
		{
			if($_FILES['setmeal_img']['name'])
			{
				require_once(ADMIN_ROOT_PATH.'include/upload.php');
				$dir= '../data/setmealimg/';
				$setmeal_img=_asUpFiles($dir, "setmeal_img", 10, 'gif',$id);
				$db->updatetable(table('setmeal'),array('setmeal_img'=>$setmeal_img),array('id'=>$id));
			}
			
			//��д����Ա��־
			write_log("��̨�ɹ�����ײ�", $_SESSION['admin_name'],3);
			$link[0]['text'] = "�����ײ�����";
			$link[0]['href'] ="?act=set_meal";
			adminmsg("��ӳɹ���",2,$link);
		}
		else
		{
			//��д����Ա��־
			write_log("��̨����ײ�ʧ��", $_SESSION['admin_name'],3);
			adminmsg("���ʧ�ܣ�",0);
		}
}
elseif($act == 'set_meal_edit')
{
	get_token();
	$smarty->assign('show',get_setmeal_one(intval($_GET['id'])));
	$smarty->assign('navlabel',"set_meal");
	$smarty->display('set_com/admin_mode_meal_edit.htm');
}
elseif($act == 'set_meal_edit_save')
{
	check_token();
	$setsqlarr['setmeal_name']=trim($_POST['setmeal_name'])?trim($_POST['setmeal_name']):adminmsg('�ײ����Ʋ���Ϊ�գ�',1);
	$setsqlarr['days']=intval($_POST['days']);
	$setsqlarr['original_price']=intval($_POST['original_price']);
	$setsqlarr['expense']=intval($_POST['expense']);
	$setsqlarr['jobs_ordinary']=intval($_POST['jobs_ordinary']);
	$setsqlarr['download_resume_ordinary']=intval($_POST['download_resume_ordinary']);
	$setsqlarr['download_resume_senior']=intval($_POST['download_resume_senior']);
	$setsqlarr['interview_ordinary']=intval($_POST['interview_ordinary']);
	$setsqlarr['interview_senior']=intval($_POST['interview_senior']);
	$setsqlarr['talent_pool']=intval($_POST['talent_pool']);
	$setsqlarr['recommend_num']=intval($_POST['recommend_num']);
	$setsqlarr['recommend_days']=intval($_POST['recommend_days']);
	$setsqlarr['stick_num']=intval($_POST['stick_num']);
	$setsqlarr['stick_days']=intval($_POST['stick_days']);
	$setsqlarr['emergency_num']=intval($_POST['emergency_num']);
	$setsqlarr['emergency_days']=intval($_POST['emergency_days']);
	$setsqlarr['highlight_num']=intval($_POST['highlight_num']);
	$setsqlarr['highlight_days']=intval($_POST['highlight_days']);
	$setsqlarr['change_templates']=intval($_POST['change_templates']);
	$setsqlarr['jobsfair_num']=intval($_POST['jobsfair_num']);
	$setsqlarr['map_open']=intval($_POST['map_open']);
	$setsqlarr['show_order']=intval($_POST['show_order']);
	$setsqlarr['display']=intval($_POST['display']);
	$setsqlarr['apply']=intval($_POST['apply']);
	$setsqlarr['added']=trim($_POST['added']);
	/**
	 * 2014-01-26����start
	 */
	$setsqlarr['refresh_jobs_space']=intval($_POST['refresh_jobs_space']);
	$setsqlarr['refresh_jobs_time']=intval($_POST['refresh_jobs_time']);
	//2015-01-09н�ʶ������� set_sms 
	$setsqlarr['set_sms'] = intval($_POST['set_sms']);
	/*
	�����ϴ��ײ�ͼ��
	*/	

	if($_FILES['setmeal_img']['name'])
	{
		require_once(ADMIN_ROOT_PATH.'include/upload.php');
		$dir= '../data/setmealimg/';
		$oldimg=$db->getone("select setmeal_img from ".table('setmeal')." where id=".intval($_POST['id'])." ");
		@unlink($dir.$oldimg['setmeal_img']);
		$setsqlarr['setmeal_img']=_asUpFiles($dir, "setmeal_img", 10, 'gif',$_POST['id']);
	}
	if ($db->updatetable(table('setmeal'),$setsqlarr," id=".intval($_POST['id'])))
		{
			//��д����Ա��־
			write_log("��̨�ɹ��޸��ײ�", $_SESSION['admin_name'],3);
			$link[0]['text'] = "�����ײ�����";
			$link[0]['href'] ="?act=set_meal";
			adminmsg("���óɹ���",2,$link);
		}
		else
		{
			//��д����Ա��־
			write_log("��̨�޸��ײ�ʧ��", $_SESSION['admin_name'],3);
			adminmsg("����ʧ�ܣ�",0);
		}
}
elseif($act == 'set_meal_del')
{
	check_token();
		if (del_setmeal_one(intval($_GET['id'])))
		{
		adminmsg("ɾ���ɹ���",2);
		}
		else
		{
		adminmsg("ɾ��ʧ�ܣ�",0);
		}
}
elseif($act == 'reg_service_save')
{
	check_token();
	//��д����Ա��־
	write_log("��̨���������ļ�", $_SESSION['admin_name'],3);
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k' LIMIT 1")?adminmsg('����ʧ��', 1):"";
	}
	refresh_cache('config');
	adminmsg("����ɹ���",2);
	exit();
}
elseif($act == "set_com_reg")
{
	get_token();
	$smarty->assign('navlabel',"set_com_reg");
	$reg_com_config=explode(',', $_CFG['reg_com_set']);
	$smarty->assign('config',$reg_com_config);
	$smarty->assign('text',get_cache('text'));
	$smarty->display('set_com/admin_set_com_reg.htm');
}
//companyname:0,nature:0,trade:0,scale:0,district:0,contact:0,telephone:0,email:0,address:0,contents:0
elseif($act=="set_com_reg_save")
{
	check_token();
	if(empty($_POST['reg_set']))
	{
		$str="";
	}
	else
	{
		foreach ($_POST['reg_set'] as $value) {
			$str.=",".$value;
		}
	}
	$str=ltrim($str,",");
	!$db->query("UPDATE ".table('config')." SET value='$str' WHERE name='reg_com_set'")?adminmsg('��������ʧ��', 1):"";
	!$db->query("UPDATE ".table('text')." SET value='$str' WHERE name='reg_com_set'")?adminmsg('��������ʧ��', 1):"";
	refresh_cache('config');
	refresh_cache('text');	
	//��д����Ա��־
	write_log("��̨�ɹ�������������", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2);
}
?>