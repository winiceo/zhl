<?php
 /*
 * 74cms ���Խ������ҳ��
*/
define('IN_QISHI', true);
$alias="QS_paper_result";
error_reporting(E_ERROR);
require_once(dirname(__FILE__).'/../include/common.inc.php');
if($mypage['caching']>0){
	$smarty->cache =true;
	$smarty->cache_lifetime=$mypage['caching'];
}else{
	$smarty->cache = false;
}
$cached_id=$alias.(isset($_GET['id'])?"|".(intval($_GET['id'])%100).'|'.intval($_GET['id']):'').(isset($_GET['page'])?"|p".intval($_GET['page'])%100:'');
//���������
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$id=$_GET['id']?intval($_GET['id']):0;
$paper_sql = "SELECT r.score,p.* FROM ".table('evaluation_record')." AS r LEFT JOIN  ".table('evaluation_paper')." AS p  ON r.paper_id=p.id  WHERE uid=".$_SESSION['uid']."  and r.id = {$id}  ";
$paper_info = $db->getone($paper_sql);
$smarty->assign('paper_info',$paper_info);
//�����Ƽ�
$paper_list_sql = "SELECT * FROM ".table('evaluation_paper')." ORDER BY join_num desc LIMIT 3 ";
$paper_list = $db->getall($paper_list_sql);
$smarty->assign('paper_list',$paper_list);
if(!$smarty->is_cached($mypage['tpl'],$cached_id))
{
	$mypage['tpl']='../tpl_evaluation/default/'.$mypage['tpl'];
	$smarty->display($mypage['tpl'],$cached_id);
	$db->close();
}
else
{
	$smarty->display($mypage['tpl'],$cached_id);
}
unset($smarty);
?>