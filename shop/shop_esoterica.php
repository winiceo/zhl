<?php
 /*
 * 74cms ��«���̳���ҳ
*/
define('IN_QISHI', true);
require_once('shop_common.php');
$smarty->assign("shop_nav","esoterica");
$tpl = "../tpl_shop/default/shop_esoterica.htm";
$smarty->assign('title',"��«���̳�-��«���ؼ�");
$smarty->display($tpl);
unset($smarty);
?>