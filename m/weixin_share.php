<?php
 /*
 * 74cms WAP
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(QISHI_ROOT_PATH.'include/jssdk.php');
$jssdk = new JSSDK($_CFG['weixin_appid'], $_CFG['weixin_appsecret'],get_access_token());
$signPackage = $jssdk->GetSignPackage();
$smarty->assign("signPackage",$signPackage);
?>