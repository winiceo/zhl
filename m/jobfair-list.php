<?php
 /*
 * 74cms ��������Ƹ��ģ��
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
require_once(dirname(__FILE__).'/weixin_share.php');
$page = empty($_GET['page'])?1:intval($_GET['page']);
$jobstable=table('jobfair');
$orderbysql=" ORDER BY `addtime` desc";
$perpage = 5;
$count  = 0;
$page = empty($_GET['page'])?1:intval($_GET['page']);
if($page<1) $page = 1;
$start = ($page-1)*$perpage;
$wheresql = '';
if ($_CFG['subsite_id']>0)
{
	$wheresql.=" WHERE `subsite_id` = ".intval($_CFG['subsite_id']).' ';
}
$total_sql="SELECT COUNT(*) AS num FROM {$jobstable} ".$wheresql.$orderbysql;
$count=$db->get_total($total_sql);
$limit=" LIMIT {$start},{$perpage}";
$idresult = $db->query("SELECT * FROM {$jobstable} ".$wheresql.$orderbysql.$limit);
$jobfairs=array();
$time=time();
while($row = $db->fetch_array($idresult))
{
	$row['url'] = wap_url_rewrite("jobfair-show",array("id"=>$row['id']),1,$row['subsite_id']);
	//״̬
	if($row['predetermined_status']=="1" && $row['predetermined_start']>$time)
	{
		$row['predetermined_ok'] = 1; // δ��ʼ
	}
	else if ($row['predetermined_status']=="1" && $row['holddate_start']>$time && ($row['predetermined_end']=="0" || $row['predetermined_end']>$time) && ($row['predetermined_web']=="1" || $row['predetermined_tel']=="1"))
	{
		$row['predetermined_ok'] = 2; // Ԥ����
	}
	else
	{
		$row['predetermined_ok'] = 0; // �ѽ���
	}
	$jobfairs[] = $row;
}
$smarty->assign('jobfairs',$jobfairs);
$smarty->display("m/jobfair-list.html");
?>