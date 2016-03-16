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
$id = intval($_GET['id']);
$row=resume_one($id);
check_m_url($row['subsite_id'],$smarty,$_CFG['m_resume_url']);
wap_weixin_logon($_GET['from']);
if(intval($_SESSION["uid"])>0){
	$sql="select * from ".table("company_down_resume")." where company_uid=$_SESSION[uid] and resume_id=".$id;
	$down_resume=$db->getone($sql);
	$smarty->assign('down_resume',$down_resume);
	$time=time();
	$jobs_sql="select * from ".table("jobs")." where uid=$_SESSION[uid] and display=1 and deadline>$time ";
	$jobs_row=$db->getall($jobs_sql);
	$smarty->assign('jobs_row',$jobs_row);
}
$smarty->assign('show',$row);
$smarty->assign('goback',$_SERVER["HTTP_REFERER"]);
$smarty->display("m/m-resume-show.html");
?>