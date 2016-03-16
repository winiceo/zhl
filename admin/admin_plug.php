<?php
 /*
 * 74cms ģ�����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_plug_fun.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"site_plug");
$smarty->assign('pageheader',"ģ�����");
if($act == 'list')
{	
	get_token();
	$smarty->assign('plug',get_plug());
	$smarty->display('plug/admin_plug_list.htm');
}
elseif($act == 'uninstall_plug')
{
	check_token();
	if(uninstall_plug($_GET['id'])){
		refresh_plug_cache();
		adminmsg('�رճɹ�', 2);
	}else{
		adminmsg('�ر�ʧ��', 1);
	}

			
}
elseif($act == 'install_plug')
{
	check_token();
	if(install_plug($_GET['id'])){
		refresh_plug_cache();
		adminmsg('�����ɹ�', 2);
	}else{
		adminmsg('����ʧ��', 1);
	}
}
?>