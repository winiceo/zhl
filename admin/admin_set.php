<?php
 /*
 * 74cms ϵͳ����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set';
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"site_set");
$smarty->assign('pageheader',"��վ����");
if($act == 'set')
{	
	get_token();
	$smarty->assign('rand',rand(1,100));
	$smarty->assign('upfiles_dir',$upfiles_dir);	
	$smarty->assign('config',get_cache('config'));
	$smarty->assign('navlabel',"set");	
	$smarty->display('set/admin_set_config.htm');
}
elseif($act == 'site_setsave')
{
	check_token();
	require_once(ADMIN_ROOT_PATH.'include/upload.php');
		if($_FILES['web_logo']['name'])
		{
			$web_logo=_asUpFiles($upfiles_dir, "web_logo", 1024*2, 'jpg/gif/png',"logo");
			if(!$db->query("UPDATE ".table('config')." SET value='$web_logo' WHERE name='web_logo'"))
			{
				//��д����Ա��־
				write_log("��̨������վLOGOʧ��", $_SESSION['admin_name'],3);
				adminmsg('����վ������ʧ��', 1);
			}
			//��д����Ա��־
			write_log("��̨�ɹ�������վLOGO", $_SESSION['admin_name'],3);
		}

		if($_FILES['body_bgimg']['name'])
		{
			$body_bgimg=_asUpFiles($upfiles_dir, "body_bgimg", 1024*2, 'jpg/gif/png',"body_bg_img");
			if(!$db->query("UPDATE ".table('config')." SET value='$body_bgimg' WHERE name='body_bgimg'"))
			{
				//��д����Ա��־
				write_log("��̨������վ����ʧ��", $_SESSION['admin_name'],3);
				adminmsg('����վ������ʧ��', 1);
			}
			//��д����Ա��־
			write_log("��̨�ɹ�������վ����", $_SESSION['admin_name'],3);
		}
		if($_POST['set_body_bgimg_defaule']==1)
		{
			@unlink($upfiles_dir.$_CFG["body_bgimg"]);
			if(!$db->query("UPDATE ".table('config')." SET value='' WHERE name='body_bgimg'"))
			{
				//��д����Ա��־
				write_log("��̨������վĬ�ϱ���ʧ��", $_SESSION['admin_name'],3);
				adminmsg('����վ������ʧ��', 1);
			}
			//��д����Ա��־
			write_log("��̨�ɹ�������վĬ�ϱ���", $_SESSION['admin_name'],3);
		}
		foreach($_POST as $k => $v)
		{
		!$db->query("UPDATE ".table('config')." SET value='{$v}' WHERE name='{$k}'")?adminmsg('����վ������ʧ��', 1):"";
		}
		refresh_cache('config');
		//��д����Ա��־
		write_log("��̨�ɹ�������վ����", $_SESSION['admin_name'],3);
		adminmsg("����ɹ���",2);
}
elseif($act == 'map')
{
	get_token();
	$smarty->assign('config',$_CFG);
	$smarty->assign('navlabel',"map");	
	$smarty->display('set/admin_set_map.htm');
}
elseif($act == 'agreement')
{
	get_token();
	$smarty->assign('config',$_CFG);
	$smarty->assign('text',get_cache('text'));
	$smarty->assign('navlabel',"agreement");	
	$smarty->display('set/admin_set_agreement.htm');
}
elseif($act == 'set_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"mb_set");
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='{$v}' WHERE name='{$k}'")?adminmsg('��������ʧ��', 1):"";
	}
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('text')." SET value='{$v}' WHERE name='{$k}'")?adminmsg('��������ʧ��', 1):"";
	}
	refresh_cache('config');
	refresh_cache('text');
	//��д����Ա��־
	write_log("��̨�ɹ���������", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2);
}
elseif($act == 'search')
{
	get_token();
	$smarty->assign('pageheader',"��������");
	$smarty->assign('config',$_CFG);
	$smarty->display('set/admin_set_search.htm');
}
elseif($act == 'search_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"set_search");
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='{$v}' WHERE name='{$k}'")?adminmsg('��������ʧ��', 1):"";
	}
	//��д����Ա��־
	write_log("��̨�ɹ�������������", $_SESSION['admin_name'],3);
	refresh_cache('config');
	write_log("������������", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2);
}
?>