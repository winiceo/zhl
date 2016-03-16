<?php
 /*
 * 74cms 职位详细页
*/
define('IN_QISHI', true);
$alias="QS_hunter_jobsshow";
require_once(dirname(__FILE__).'/../include/common.inc.php');
if ($_PLUG['hunter']['p_install']==1)
{
	$link[0]['text'] = "返回首页";
	$link[0]['href'] = $_CFG['site_dir'];
	showmsg("管理员已关闭此模块!",1,$link);
}
if($mypage['caching']>0){
        $smarty->cache =true;
		$smarty->cache_lifetime=$mypage['caching'];
	}else{
		$smarty->cache = false;
	}
$cached_id=$alias.(isset($_GET['id'])?"|".(intval($_GET['id'])%100).'|'.intval($_GET['id']):'').(isset($_GET['page'])?"|p".intval($_GET['page'])%100:'');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$mypage['tpl']='../tpl_hunter/default/'.$mypage['tpl'];
$smarty->assign('user_tpl','../tpl_hunter/');
$smarty->display($mypage['tpl'],$cached_id);
$db->close();
unset($smarty);
?>