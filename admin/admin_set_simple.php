<?php
 /*
 * 74cms 微招聘
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set';
$smarty->assign('act',$act);
$smarty->assign('pageheader',"微商圈");
if($_CFG['subsite_id']>0){
	adminmsg('您没有管理权限！',0);
}
check_permissions($_SESSION['admin_purview'],"set_simple");	
if($act == 'set')
{
	get_token();	
	$smarty->assign('config',$_CFG);
	$smarty->assign('text',get_cache('text'));
	$smarty->display('set_simple/admin_set_simple.htm');
}
elseif($act == 'set_save')
{
	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k'")?adminmsg('更新设置失败', 1):"";
	}
	refresh_cache('config');
	adminmsg("保存成功！",2);
}
?>