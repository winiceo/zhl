<?php
/*
 * 74cms ��ҵ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"jobfair");
if ($act=='mybooth')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY e.id DESC";
	$wheresql= " WHERE e.uid={$_SESSION['uid']} ";
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND e.eaddtime>".$settr_val;
	}
	$joinsql=" LEFT JOIN  ".table('jobfair')." AS j ON  e.jobfairid=j.id ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('jobfair_exhibitors')." as e ".$joinsql.$wheresql;
	$perpage=10;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title','��Ԥ����չλ - ��Ƹ�� - ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('list',get_jobfair_exhibitors($offset,$perpage,$joinsql.$wheresql));
	if ($total_val>$perpage)$smarty->assign('page',$page->show(3));
	$smarty->display('member_company/company_jobfair_exhibitors.htm');
}
unset($smarty);
?>