<?php
 /*
 * 触屏版关于我们
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
//网站简介（说明页） 
$site_detail = $db->getone("SELECT * FROM ".table('explain')." WHERE id=2 ");
$smarty->assign('site_detail',htmlspecialchars_decode($site_detail["content"],ENT_QUOTES));
$smarty->assign('goback',$_SERVER["HTTP_REFERER"]);
$smarty->display("m/about.html");
?>