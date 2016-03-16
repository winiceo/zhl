<?php
 /*
 * 74cms uc整合
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set_uc';
if($_CFG['subsite_id']>0){
	adminmsg('您没有管理权限！',0);
}
check_permissions($_SESSION['admin_purview'],"set_uc");
$smarty->assign('pageheader',"uc整合");
if($act == 'set_uc')
{
	get_token();
	$smarty->assign('uc',get_uc_config());
	$smarty->assign('navlabel','set');
	$smarty->display('uc/admin_uc_set.htm');
}
elseif($act == 'set_save')
{
	check_token();
	header("Cache-control: private");
	foreach($_POST as $k => $v){
	!$db->query("UPDATE ".table('uc_config')." SET value='$v' WHERE name='$k'")?adminmsg('更新站点设置失败', 1):"";
	}
	refresh_uc_cache('uc_config');
	write_log("设置uc整合配置", $_SESSION['admin_name'],3);
	adminmsg("保存成功！",2);
}
elseif($act == 'set_open')
{
	header("Cache-control: private");
	$open = intval($_POST['uc_open']);
	!$db->query("UPDATE ".table('config')." SET value='".$open."' WHERE name='uc_open'")?adminmsg('保存失败', 1):"";
	refresh_cache('config');
	write_log("设置uc开启选项", $_SESSION['admin_name'],3);
	adminmsg("保存成功！",2);
}
?>