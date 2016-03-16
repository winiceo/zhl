<?php
 /*
 * 74cms 积分商城首页
*/
define('IN_QISHI', true);
$alias="QS_shop_list";
require_once('shop_common.php');
//$shop_nav=trim($_GET['shop_nav']);
$smarty->assign("shop_nav","all");
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
$mypage['tpl']=get_tpl("shop",$_GET['id']).$mypage['tpl'];
$smarty->display($mypage['tpl'],$cached_id);
$db->close();
}
else
{
$smarty->display($mypage['tpl'],$cached_id);
}
unset($smarty);
?>