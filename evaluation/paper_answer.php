<?php
 /*
 * 74cms ��������ҳ��
*/
define('IN_QISHI', true);
$alias="QS_paper_answer";
error_reporting(E_ERROR);
require_once(dirname(__FILE__).'/../include/common.inc.php');
if($mypage['caching']>0){
	$smarty->cache =true;
	$smarty->cache_lifetime=$mypage['caching'];
}else{
	$smarty->cache = false;
}
$cached_id=$alias.(isset($_REQUEST['id'])?"|".(intval($_REQUEST['id'])%100).'|'.intval($_REQUEST['id']):'').(isset($_REQUEST['page'])?"|p".intval($_REQUEST['page'])%100:'');


require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$id=$_REQUEST['id']?intval($_REQUEST['id']):0;
if(empty($_SESSION['utype']) || $_SESSION['utype']!=2)
{
	$link[0]['text'] = "���ز�����ҳ";
	$link[0]['href'] = url_rewrite('QS_evaluation_index');
	showmsg('ֻ�и��˻�Ա���ܽ��в�����',1,$link);
}
if(empty($_SESSION['uid']))
{
	$uid =0;
}
else
{
	$uid = intval($_SESSION['uid']);
}
//�ж�һ�¸û�Ա�Ƿ����˾�
$check_sql = "SELECT * FROM ".table('evaluation_record')." WHERE paper_id=".$id.' AND uid='.$uid;
$check_info = $db->getone($check_sql);
if($check_info)
{
	$link[0]['text'] = "�����ҵĲ���";
	$link[0]['href'] = url_rewrite('QS_my_evaluation');
	showmsg('���Ѿ�����˾�',1,$link);
}
//�Ծ���Ϣ
$peper_sql = "SELECT * FROM ".table('evaluation_paper')." WHERE id=".$id;
$paper_info = $db->getone($peper_sql);
$smarty->assign('paper_info',$paper_info);
//������Ϣ
$perpage = 10;
$page=$_REQUEST['page']?intval($_REQUEST['page']):1;
$total_sql="SELECT COUNT(*) AS num from ".table('evaluation_question')." WHERE paper_id=".$id;
$total_val=$db->get_total($total_sql);
$page_num = ceil($total_val/$perpage);
if($page<$page_num)
{
	$smarty->assign('next_page',$page+1);
}
if($page != 1)
{
	$smarty->assign('pre_page',$page-1);
}
$smarty->assign('page',$page);
$smarty->assign('page_num',$page_num);
$smarty->assign('perpage',$perpage);
$smarty->assign('total_val',$total_val);
$offset=($page-1)*$perpage;
$limit = " LIMIT ".intval($offset).','.intval($perpage);
$question_sql = "SELECT * FROM ".table('evaluation_question')." WHERE paper_id=".$id." ORDER BY id asc ".$limit;
$question_list = $db->getall($question_sql);
foreach ($question_list as $key => $value) 
{
	$option_sql = "SELECT * FROM ".table('evaluation_option')." WHERE question_id=".$value['id']." ORDER BY id asc ";
	$option_list = $db->getall($option_sql);
	$question_list2[intval($offset)+intval($key)+1] = $value;
	$question_list2[intval($offset)+intval($key)+1]['key'] = intval($offset)+intval($key)+1;
	$question_list2[intval($offset)+intval($key)+1]['option'] = $option_list;
}
$smarty->assign('question_list',$question_list2);
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