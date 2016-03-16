<?php
 /*
 * 74cms ajax 查看完整地图
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'company_map';
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if ($act=="company_map")
{	
	$map_x = trim($_GET['map_x']);
	$map_y = trim($_GET['map_y']);
	$map_zoom = trim($_GET['map_zoom']);
	$companyname = trim($_GET['companyname']);
	$companyname =  iconv("UTF-8","GBK",$companyname);
	$address = trim($_GET['address']);
	$address =  iconv("UTF-8","GBK",$address);
	$tpl=QISHI_ROOT_PATH.'templates/'.$_CFG['template_dir']."ajax_map.htm";
	$contents=file_get_contents($tpl);
	$contents=str_replace('{#$company.map_x#}',$map_x,$contents);
	$contents=str_replace('{#$company.map_y#}',$map_y,$contents);
	$contents=str_replace('{#$company.map_zoom#}',$map_zoom,$contents);
	$contents=str_replace('{#$company.companyname#}',$companyname,$contents);
	$contents=str_replace('{#$company.address#}',$address,$contents);
	exit($contents);
}
?>