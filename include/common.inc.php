<?php
 /*
 * 74cms ���������ļ�
*/
if(!defined('IN_QISHI')) exit('Access Denied!');
define('QISHI_ROOT_PATH',dirname(dirname(__FILE__)).'/');
error_reporting(E_ERROR);
//error_reporting(-1);
ini_set('session.save_handler', 'files');
session_save_path(QISHI_ROOT_PATH.'data/sessions');
session_start();
require_once(QISHI_ROOT_PATH.'data/config.php');
header("Content-Type:text/html;charset=".QISHI_CHARSET);
require_once(QISHI_ROOT_PATH.'include/help.class.php');
require_once(QISHI_ROOT_PATH.'include/common.fun.php');
require_once(QISHI_ROOT_PATH.'include/74cms_version.php');

$QSstarttime=exectime();
if (!empty($_GET))
{
$_GET  = help::addslashes_deep($_GET);
}
if (!empty($_POST))
{
$_POST = help::addslashes_deep($_POST);
}
$_COOKIE   = help::addslashes_deep($_COOKIE);
$_REQUEST  = help::addslashes_deep($_REQUEST);
date_default_timezone_set("PRC");
$timestamp = time();
$online_ip=getip();
$ip_address=convertip($online_ip);
$_NAV=get_cache('nav');
$_PAGE=get_cache('page');
$_CFG=get_cache('config');
$_SUBSITE=get_cache('subsite');
$_M_SUBSITE=get_cache('m_subsite');
$_CFG['statistics'] = htmlspecialchars_decode($_CFG['statistics']);
$_PLUG=get_cache('plug');
if($_CFG['uc_open']=="1"){
	require_once(QISHI_ROOT_PATH.'data/cache_uc_config.php');
}
$QS_cookiedomain = get_cookiedomain();

$_CFG['main_domain']=$_CFG['site_domain'].$_CFG['site_dir'];
$_CFG['wap_domain'] = $_CFG['wap_domain']==""?$_CFG['site_domain'].$_CFG['site_dir']."m":$_CFG['wap_domain'];
$_CFG['m_main_domain'] = $_CFG['wap_domain'];
$_CFG['version']=QISHI_VERSION;
$_CFG['web_logo']=$_CFG['web_logo']?$_CFG['web_logo']:'logo.gif';
$_CFG['upfiles_dir']=$_CFG['site_dir']."data/".$_CFG['updir_images']."/";
$_CFG['thumb_dir']=$_CFG['site_dir']."data/".$_CFG['updir_thumb']."/";
$_CFG['_resume_photo_dir']= $_CFG['resume_photo_dir'];
$_CFG['resume_photo_dir']=$_CFG['site_dir']."data/".$_CFG['resume_photo_dir']."/";
$_CFG['_resume_photo_dir_thumb']= $_CFG['resume_photo_dir_thumb'];
$_CFG['resume_photo_dir_thumb']=$_CFG['site_dir']."data/".$_CFG['resume_photo_dir_thumb']."/";
$_CFG['teacher_photo_dir']=$_CFG['site_dir']."data/train_teachers/";
$_CFG['teacher_photo_dir_thumb']=$_CFG['site_dir']."data/train_teachers/thumb/";
$_CFG['train_logo_dir']=$_CFG['site_dir']."data/train_logo/";
$_CFG['train_logo_dir_thumb']=$_CFG['site_dir']."data/train_logo/thumb/";
$_CFG['hunter_photo_dir']=$_CFG['site_dir']."data/hunter/";
$_CFG['hunter_photo_dir_thumb']=$_CFG['site_dir']."data/hunter/thumb/";
$_CFG['site_template']=$_CFG['site_dir'].'templates/'.$_CFG['template_dir'];
$_CFG['site_shop_template']=$_CFG['site_dir'].'templates/tpl_shop/default/';
$_CFG['site_campus_template']=$_CFG['site_dir'].'templates/tpl_campus/default/';
$_CFG['site_evaluation_template']=$_CFG['site_dir'].'templates/tpl_evaluation/default/';
$_CFG['site_hunter_template']=$_CFG['site_dir'].'templates/tpl_hunter/default/';
$_CFG['subsite_id']=0;
subsiteinfo($_CFG);
if(defined('REQUEST_MOBILE')){
	mobile_subsiteinfo($_CFG);
}
// $_CFG['m_dir'] = strstr($_CFG['wap_domain'],'/m')===false?$_CFG['site_dir'].'/m';
$_CFG['site_template']=$_CFG['site_dir'].'templates/'.$_CFG['template_dir'];
$mypage=$_PAGE[$alias];
$mypage['tag']?$page_select=$mypage['tag']:'';
require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
	if ($_CFG['isclose'])
	{
				$smarty->assign('info',$_CFG['close_reason']=$_CFG['close_reason']?$_CFG['close_reason']:'վ����ʱ�ر�...');
				$smarty->display('warning.htm');
				exit();
	}
	if ($_CFG['filter_ip'] && check_word($_CFG['filter_ip'],$online_ip))
	{
			$smarty->assign('info',$_CFG['filter_ip_tips']);
			$smarty->display('warning.htm');
			exit();
	}
?>