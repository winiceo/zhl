<?php
 /*
 * 74cms 微招聘
*/
define('IN_QISHI', true);
$alias="QS_simplelist";
require_once(dirname(__FILE__).'/../include/common.inc.php');
if ($_PLUG['simple']['p_install']==1)
{
	$link[0]['text'] = "返回首页";
	$link[0]['href'] = $_CFG['site_dir'];
	showmsg("管理员已关闭此模块!",1,$link);
}
if(browser()=="mobile" && $_SESSION['iswap']==""){
	header("location:".$_CFG['wap_domain'].'/simple-list.php');
}
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
$smarty->display($mypage['tpl'],$cached_id);
$db->close();
}
else
{
$smarty->display($mypage['tpl'],$cached_id);
}
unset($smarty);
?>