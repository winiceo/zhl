<?php
 /*
 * 74cms ��ѵ����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_train_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set';
$smarty->assign('pageheader',"��ѵ��������");
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"set_train");
if($act == 'set')
{	
	get_token();
	$smarty->assign('navlabel',"set");
	$smarty->assign('config',$_CFG);
	$smarty->assign('text',get_cache('text'));
	$smarty->display('set_train/admin_set_train.htm');
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
	write_log("��̨�ɹ���������", $_SESSION['admin_name'],3);	
	adminmsg("����ɹ���",2);
}
elseif($act == 'modeselect')
{
	get_token();
	$smarty->assign('navlabel',"modeselect");
	$smarty->display('set_train/admin_mode.htm');
}
elseif($act == 'modeselect_save')
{
 	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k' LIMIT 1")?adminmsg('����ʧ��', 1):"";
	}
	//��д����Ա��־
	write_log("��̨�ɹ���������", $_SESSION['admin_name'],3);	
	refresh_cache('config');
	adminmsg("����ɹ���",2);
}
elseif($act == 'set_points')
{
	get_token();
	$smarty->assign('config',$_CFG);
	$smarty->assign('points',get_points_rule());
	$smarty->assign('navlabel',"set_points");
	$smarty->display('set_train/admin_mode_points.htm');
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
	//��д����Ա��־
	write_log("��̨�ɹ�������ѵ��Ա���ֹ���", $_SESSION['admin_name'],3);	
	refresh_points_rule_cache();
	adminmsg("�������óɹ���",2);
}
elseif($act == 'set_points_config_save')
{
	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k' LIMIT 1")?adminmsg('����ʧ��', 1):"";
	}
	//��д����Ա��־
	write_log("��̨�ɹ���������", $_SESSION['admin_name'],3);
	refresh_cache('config');
	adminmsg("����ɹ���",2);
}
elseif($act == 'set_meal')
{
	get_token();
	$smarty->assign('setmeal',get_setmeal());
	$smarty->assign('givesetmeal',get_setmeal(false));
	$smarty->assign('navlabel',"set_meal");
	$smarty->display('set_train/admin_mode_meal.htm');
}
elseif($act == 'set_meal_add')
{
	get_token();
	$smarty->assign('setmeal',get_setmeal());
	$smarty->assign('navlabel',"set_meal");
	$smarty->display('set_train/admin_mode_meal_add.htm');
}
elseif($act == 'set_meal_add_save')
{
	check_token();
	$setsqlarr['setmeal_name']=trim($_POST['setmeal_name'])?trim($_POST['setmeal_name']):adminmsg('�ײ����Ʋ���Ϊ�գ�',1);
	$setsqlarr['days']=intval($_POST['days']);
	$setsqlarr['expense']=intval($_POST['expense']);
	$setsqlarr['course_num']=intval($_POST['course_num']);
	$setsqlarr['teachers_num']=intval($_POST['teachers_num']);
	$setsqlarr['down_apply']=intval($_POST['down_apply']);
	$setsqlarr['show_order']=intval($_POST['show_order']);
	$setsqlarr['change_templates']=intval($_POST['change_templates']);
	$setsqlarr['map_open']=intval($_POST['map_open']);
	$setsqlarr['display']=intval($_POST['display']);
	$setsqlarr['apply']=intval($_POST['apply']);
	$setsqlarr['added']=trim($_POST['added']);
	$setsqlarr['refresh_course_space']=trim($_POST['refresh_course_space']);
	$setsqlarr['refresh_course_time']=trim($_POST['refresh_course_time']);
	if ($db->inserttable(table('train_setmeal'),$setsqlarr))
		{
			//��д����Ա��־
			write_log("��̨�ɹ�����ײ�", $_SESSION['admin_name'],3);
			$link[0]['text'] = "�����ײ�����";
			$link[0]['href'] ="?act=set_meal";
			adminmsg("��ӳɹ���",2,$link);
		}
		else
		{
		adminmsg("���ʧ�ܣ�",0);
		}
}
elseif($act == 'set_meal_edit')
{
	get_token();
	$smarty->assign('show',get_setmeal_one(intval($_GET['id'])));
	$smarty->assign('navlabel',"set_meal");
	$smarty->display('set_train/admin_mode_meal_edit.htm');
}
elseif($act == 'set_meal_edit_save')
{
	check_token();
	$setsqlarr['setmeal_name']=trim($_POST['setmeal_name'])?trim($_POST['setmeal_name']):adminmsg('�ײ����Ʋ���Ϊ�գ�',1);
	$setsqlarr['days']=intval($_POST['days']);
	$setsqlarr['expense']=intval($_POST['expense']);
	$setsqlarr['course_num']=intval($_POST['course_num']);
	$setsqlarr['teachers_num']=intval($_POST['teachers_num']);
	$setsqlarr['down_apply']=intval($_POST['down_apply']);
	$setsqlarr['show_order']=intval($_POST['show_order']);
	$setsqlarr['change_templates']=intval($_POST['change_templates']);
	$setsqlarr['map_open']=intval($_POST['map_open']);
	$setsqlarr['display']=intval($_POST['display']);
	$setsqlarr['added']=trim($_POST['added']);
	$setsqlarr['refresh_course_space']=trim($_POST['refresh_course_space']);
	$setsqlarr['refresh_course_time']=trim($_POST['refresh_course_time']);
	if ($db->updatetable(table('train_setmeal'),$setsqlarr," id=".intval($_POST['id'])))
		{
			//��д����Ա��־
			write_log("��̨�ɹ��޸��ײ�", $_SESSION['admin_name'],3);
			$link[0]['text'] = "�����ײ�����";
			$link[0]['href'] ="?act=set_meal";
			adminmsg("���óɹ���",2,$link);
		}
		else
		{
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
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k' LIMIT 1")?adminmsg('����ʧ��', 1):"";
	}
	refresh_cache('config');
	//��д����Ա��־
	write_log("��̨�ɹ���������", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2);
	exit();
}
?>