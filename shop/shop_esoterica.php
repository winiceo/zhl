<?php
 /*
 * 74cms 葫芦币商城首页
*/
define('IN_QISHI', true);
require_once('shop_common.php');
$smarty->assign("shop_nav","esoterica");
$tpl = "../tpl_shop/default/shop_esoterica.htm";
$smarty->assign('title',"葫芦币商城-葫芦币秘籍");
$smarty->display($tpl);
unset($smarty);
?>