<?php
 /*
 * 74cms н��ͳ��ģ��
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/./../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'salary';
$smarty->cache = false;

$filename = urlencode($_GET['district'].'_'.$_GET['category']).'_all_salary.cache';
$result = check_cache($filename,'salary',7);
if(!$result){
	$result = dfopen("http://www.zhaohulu.com/salary/get_salary_data_all_salary.php?district=".$_GET['district']."&category=".$_GET['category']."&certification=".$_SERVER['SERVER_NAME']);
	write_cache($filename,$result,'salary');
}
if($result!="-1"){
	$result = json_decode($result,true);
}else{
	$result="error";
}
$str1 = $_GET['district']!=''?$_GET['district']:'ȫ��';
$str2 = $_GET['category']!=''?$_GET['category']:'ְ��';
$str3 = $_GET['category']!=''?$_GET['category']:'��ҵ';
$smarty->assign('str',$str1.$str2);
$smarty->assign('str2',$str1.$str3);
$smarty->assign('all_salary',$result);

$filename = urlencode($_GET['district'].'_'.$_GET['category']).'_salary_barchart.cache';
$result = check_cache($filename,'salary',7);
if(!$result){
	$result = dfopen("http://www.zhaohulu.com/salary/get_salary_data_salary_barchart.php?district=".$_GET['district']."&category=".$_GET['category']."&certification=".$_SERVER['SERVER_NAME']);
	write_cache($filename,$result,'salary');
}
$smarty->assign('salary_barchart_experience',$result);
$smarty->display("m/m-salary.html");
?>