<?php
 /*
 * 74cms ajax หัห๗ฬ๘ืช
*/
 
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'QS_jobslist';
if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
{
$_GET['category']=utf8_to_gbk($_GET['category']);
$_GET['district']=utf8_to_gbk($_GET['district']);
}
unset($_GET['act']);
$_GET=array_map("rawurlencode",$_GET);
$url=url_rewrite($act,$_GET);
unset($_GET);
exit($url);
?>