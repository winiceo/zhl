<?php
 /*
 * 74cms ������ϸҳ��
*/
define('IN_QISHI', true);
$alias="QS_resumeshow";
require_once(dirname(__FILE__).'/../include/common.inc.php');
if(browser()=="mobile" && $_SESSION['iswap']==""){
	header("location:".$_CFG['wap_domain'].'/resume-show.php?id='.intval($_GET['id']));
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
$mypage['tpl']=get_tpl("resume",$_GET['id']).$mypage['tpl'];
$smarty->display($mypage['tpl'],$cached_id);
$db->close();
unset($smarty);
?>