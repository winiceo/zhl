<?php
 /*
 * 74cms ´¥ÆÁ°æÎ¢ÕÐÆ¸ÏêÇéÄ£¿é
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
require_once(dirname(__FILE__).'/weixin_share.php');
wap_weixin_logon($_GET['from']);
$row=simple_jobs_one($_GET['id']);
$smarty->assign('show',$row);
$smarty->assign('goback',$_SERVER["HTTP_REFERER"]);
$smarty->display("m/simple-job-show.html");
?>