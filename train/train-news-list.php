<?php
 /*
 * 74cms  ��ѵ��Ѷ
*/
define('IN_QISHI', true);
$alias="QS_train_news";
require_once(dirname(__FILE__).'/../include/common.inc.php');
if ($_PLUG['train']['p_install']==1)
{
	$link[0]['text'] = "������ҳ";
	$link[0]['href'] = $_CFG['site_dir'];
	showmsg("����Ա�ѹرմ�ģ��!",1,$link);
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
$mypage['tpl']=get_tpl("train_profile",$_GET['id']).$mypage['tpl'];
$smarty->display($mypage['tpl'],$cached_id);
$db->close();
}
else
{
$mypage['tpl']=get_tpl("train_profile",$_GET['id']).$mypage['tpl'];
$smarty->display($mypage['tpl'],$cached_id);
}
unset($smarty);
?>