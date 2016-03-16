<?php
 /*
 * 74cms WAP
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
require_once(dirname(__FILE__).'/weixin_share.php');
$show = notice_one($_GET['id']);
check_m_url($show['subsite_id'],$smarty,$_CFG['m_notice_url']);
if($show)
{
	$show['addtime'] = daterange(time(),$show['addtime'],'Y-m-d',"#FF3300");
	$smarty->assign('show',$show);
}
$smarty->assign('goback',$_SERVER["HTTP_REFERER"]);
$smarty->display("m/notice-show.html");
?>