<?php
 /*
 * 74cms ��ͷ�ɼ�������
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$locoyspider=get_cache('locoyspider');
require_once(ADMIN_ROOT_PATH.'include/admin_locoyspider_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set';
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"locoyspider");
	require_once(ADMIN_ROOT_PATH.'include/admin_article_fun.php');
$smarty->assign('show',$locoyspider);
$smarty->assign('pageheader',"��ͷ�ɼ�");
if($act=="set")
{	
	get_token();
	$smarty->assign('navlabel',"set");
	$smarty->display('locoyspider/admin_locoyspider.htm');
}
elseif($act=="set_news")
{	
	get_token();
	$smarty->assign('navlabel',"set_news");
	$smarty->display('locoyspider/admin_locoyspider_news.htm');
}
elseif($act=="set_company")
{	
	get_token();
	$smarty->assign('navlabel',"set_company");
	$smarty->display('locoyspider/admin_locoyspider_company.htm');
}
elseif($act=="set_jobs")
{	
	get_token();
	$smarty->assign('navlabel',"set_jobs");
	$smarty->display('locoyspider/admin_locoyspider_jobs.htm');
}
elseif($act=="set_user")
{	
	get_token();
	$smarty->assign('navlabel',"set_user");
	$smarty->display('locoyspider/admin_locoyspider_user.htm');
}
elseif($act == 'set_save')
{
	check_token();
	if (intval($_POST['search_threshold'])>100 || intval($_POST['search_threshold'])==0) unset($_POST['search_threshold']);
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('locoyspider')." SET value='$v' WHERE name='$k' LIMIT 1")?adminmsg('����ʧ��', 1):"";
	}
	refresh_cache('locoyspider');
	write_log("���û�ͷ����", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2);
}
?>