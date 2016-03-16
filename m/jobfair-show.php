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
$show = jobfair_one($_GET['id']);
check_m_url($show['subsite_id'],$smarty,$_CFG['m_jobfair_url']);
$time = time();
if($show)
{
	//状态
	if($show['predetermined_status']=="1" && $show['predetermined_start']>$time)
	{
		$show['predetermined_ok'] = 1; // 未开始
	}
	else if ($show['predetermined_status']=="1" && $show['holddate_start']>$time && ($show['predetermined_end']=="0" || $show['predetermined_end']>$time) && ($show['predetermined_web']=="1" || $show['predetermined_tel']=="1"))
	{
		$show['predetermined_ok'] = 2; // 预定中
	}
	else
	{
		$show['predetermined_ok'] = 0; // 已结束
	}
	//参会行业
	$jobfair_trade = explode(",", $show['trade_cn']);
	$show["trade_cn"] = $jobfair_trade;
	$smarty->assign('show',$show);
	//参会企业
	$exhibitors = $db->getall("SELECT * FROM ".table('jobfair_exhibitors')." WHERE jobfairid=".$show["id"]);      
	$smarty->assign('exhibitors',$exhibitors);
}
$smarty->assign('goback',$_SERVER["HTTP_REFERER"]);
$smarty->display("m/jobfair-show.html");
?>