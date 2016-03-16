<?php
 /*
 * 74cms 自定义页面
*/
define('IN_QISHI', true);
$alias="QS_talents";
error_reporting(E_ERROR);
require_once(dirname(__FILE__).'/../include/common.inc.php');
if($mypage['caching']>0){
	$smarty->cache =true;
	$smarty->cache_lifetime=$mypage['caching'];
}else{
	$smarty->cache = false;
}
$cached_id=$alias.(isset($_GET['id'])?"|".(intval($_GET['id'])%100).'|'.intval($_GET['id']):'').(isset($_GET['page'])?"|p".intval($_GET['page'])%100:'');
if(!$smarty->is_cached($mypage['tpl'],$cached_id))
{
	require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	$mypage['tpl']='../tpl_evaluation/default/'.$mypage['tpl'];
	$smarty->display($mypage['tpl'],$cached_id);
	$db->close();
}
else
{
	$smarty->display($mypage['tpl'],$cached_id);
}
unset($smarty);
?>