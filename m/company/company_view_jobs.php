<?php
 /*
 * ������˭�����ҵ�ְλ�б�
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
if ($_SESSION['uid']=='' || $_SESSION['username']=='' || intval($_SESSION['utype'])==2)
{
	header("Location: ../login.php");
}
elseif ($act == 'index')
{
	$smarty->cache = false;
	//ɸѡ ְλ
	if(intval($_GET['jobsid'])>0)
	{
		$wheresql=" WHERE `jobsid`=".intval($_GET['jobsid'])." ";
	}
	else
	{
		$my_jobs = get_my_jobs(intval($_SESSION['uid']));
		if(empty($my_jobs)){
			$wheresql=" WHERE 0";
		}
		else{
			$wheresql=" WHERE `jobsid` in(".$my_jobs.") ";
		}
		
	}
	//ɸѡ �鿴ʱ��
	$settr = intval($_GET['settr']);
	if($settr>0)
	{
		if(empty($wheresql))
		{
			$settr_val=strtotime("-".$settr." day");
			$wheresql="WHERE addtime>".$settr_val;
		}
		else
		{
			$settr_val=strtotime("-".$settr." day");
			$wheresql.=" AND addtime>".$settr_val;
		}
	}
	$perpage = 100;
	$count  = 0;
	$page = empty($_GET['page'])?1:intval($_GET['page']);
	if($page<1) $page = 1;
	$start = ($page-1)*$perpage;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('view_jobs')." {$wheresql} ";
	$total_val=$db->get_total($total_sql);
	$view_jobs = wap_get_view_users($start,$perpage,$wheresql);
	$smarty->assign('view_jobs',$view_jobs);
	$smarty->assign('jobs',get_my_jobs($_SESSION['uid'],true));	
	$smarty->display("m/company/m-view-jobs.html");	
}
?>