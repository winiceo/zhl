<?php
 /*
 * 74cms 管理中心首页
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_flash_statement_fun.php');
$act=!empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
$smarty->assign('admin_plug_hunter',$_PLUG["hunter"]['p_install']);
$smarty->assign('admin_plug_train',$_PLUG["train"]['p_install']);
$smarty->assign('admin_plug_simple',$_PLUG["simple"]['p_install']);
$smarty->assign('admin_plug_jobfair',$_PLUG["jobfair"]['p_install']);
if($act=='')
{
	$smarty->display('sys/admin_index.htm');
}
elseif($act=='top')
{
	$admininfo=get_admin_one($_SESSION['admin_name']);
	$smarty->assign('admin_rank', $admininfo['rank']);
	$smarty->assign('admin_name', $_SESSION['admin_name']);
	$smarty->display('sys/admin_top.htm');
}
elseif($act=='left')
{
	$smarty->display('sys/admin_left.htm');
}
elseif($act == 'main')
{
	get_userreg_30_days();
	$install_warning=file_exists('../install')?"您还没有删除 install 文件夹，出于安全的考虑，我们建议您删除 install 文件夹。":null;
	$update_warning=file_exists('../update')?"您还没有删除 update 文件夹，出于安全的考虑，我们建议您删除 update 文件夹。":null;
	$admindir_warning=substr(ADMIN_ROOT_PATH,-7)=='/admin/'?"您的网站管理中心目录为 ./admin ，出于安全的考虑，我们建议您修改目录名。":null;
	$admin_register_globals=ini_get('register_globals')?'您的php.ini中register_globals为On，强烈建议你设为Off，否则将会出现严重的安全隐患和数据错乱！':null;
	$system_info = array();
	$system_info['version'] = QISHI_VERSION;
	$system_info['release'] = QISHI_RELEASE;
	$system_info['os'] = PHP_OS;
	$system_info['web_server'] = $_SERVER['SERVER_SOFTWARE'];
	$system_info['php_ver'] = PHP_VERSION;
	$system_info['mysql_ver'] = $db->dbversion();
	$system_info['max_filesize'] = ini_get('upload_max_filesize');
	$smarty->assign('site_domain',$_SERVER['SERVER_NAME']);
	$smarty->assign('system_info',$system_info);
	$smarty->assign('random',mt_rand());
	$smarty->assign('install_warning',$install_warning);
	$smarty->assign('update_warning',$update_warning);
	$smarty->assign('admindir_warning',$admindir_warning);
	$smarty->assign('admin_register_globals',$admin_register_globals);

	$subsite = $db->getone('select 1 from '.table('subsite').' where s_effective=1');
	$admindir_warning = empty($subsite)?"您还没有添加有效的分站，为了不影响您的正常运营，请先 <a href='admin_subsite.php?act=add' style='color:red;'>添加分站</a> ！":null;
	$smarty->assign('admindir_warning',$admindir_warning);
	$smarty->assign('pageheader',"管理中心 - 后台管理首页");
	$smarty->display('sys/admin_main.htm');
}
?>