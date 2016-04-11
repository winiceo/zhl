<?php
 /*
 * 74cms 葫芦币商城首页
*/
if(!defined('IN_QISHI')) die('Access Denied!');
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_shop.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if($_CFG['operation_mode']==2)
{
	$link[0]['text'] = "网站首页";
	$link[0]['href'] = url_rewrite('QS_index');
	showmsg('套餐模式不能使用葫芦币商城',1,$link);
}
if($_SESSION['utype']=='1')
{
	$smarty->assign("com_point",get_user_points($_SESSION['uid']));
	$smarty->assign("com_info",get_company($_SESSION['uid']));

}
elseif($_SESSION['utype']=='2')
{
	$smarty->assign("com_point",get_user_points($_SESSION['uid']));
}
elseif ($_SESSION['utype']!='' && $_SESSION['utype']!='1') 
{
	$link[0]['text'] = "网站首页";
	$link[0]['href'] = url_rewrite('QS_index');
	showmsg('葫芦币商城仅对企业开放！',1,$link);
}
// 葫芦币规则
$smarty->assign("points_rule",get_cache("points_rule"));
// 热门关键字 
$smarty->assign("hotword",get_shop_hotword(6));
// 最新兑换记录 
$smarty->assign("exchange_list",get_exchange_index(4));
?>