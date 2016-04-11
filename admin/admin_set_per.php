<?php
 /*
 * 74cms ��������
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_personal_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set';
$smarty->assign('act',$act);
$smarty->assign('pageheader',"��������");
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"set_per");	
if($act == 'set')
{
	get_token();	
	$smarty->assign('navlabel',"set");
	$smarty->assign('config',$_CFG);
	$smarty->assign('text',get_cache('text'));
	$smarty->display('set_per/admin_set_per.htm');
}
elseif($act == 'set_save')
{
	check_token();
	//��д����Ա��־
	write_log("��̨��������", $_SESSION['admin_name'],3);
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
	adminmsg("����ɹ���",2);
}
elseif($act == 'set_points')
{
	get_token();
	$smarty->assign('config',$_CFG);
	$smarty->assign('points',get_points_rule());
	$smarty->assign('navlabel',"set_points");
	$smarty->display('set_per/admin_mode_points.htm');
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

?>