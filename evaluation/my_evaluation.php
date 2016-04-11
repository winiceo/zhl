<?php
 /*
 * 74cms �ҵĲ���ҳ��
*/
define('IN_QISHI', true);
$alias="QS_my_evaluation";
error_reporting(E_ERROR);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(dirname(__FILE__).'/../user/personal/personal_common.php');

if($mypage['caching']>0){
	$smarty->cache =true;
	$smarty->cache_lifetime=$mypage['caching'];
}else{
	$smarty->cache = false;
}
$cached_id=$alias.(isset($_GET['id'])?"|".(intval($_GET['id'])%100).'|'.intval($_GET['id']):'').(isset($_GET['page'])?"|p".intval($_GET['page'])%100:'');
//���˻�Ա��Ϣ
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$user_info = get_user_inid($_SESSION['uid']);
if(!empty($user_info['avatars']))
{
	$user_info['avatars'] = $_CFG['site_dir']."data/avatar/100/".$user_info['avatars'];
}
else
{
	$user_info['avatars'] = $_CFG['site_template']."images/06.jpg";
}
//ͳ�ƴ����
$count_sql = "SELECT count(*) as num,type_id FROM ".table('evaluation_record')." WHERE uid=".$_SESSION['uid']." GROUP BY type_id ORDER BY type_id ";
$result = $db->query($count_sql);
while($row = $db->fetch_array($result))
{
	$count[$row['type_id']] = intval($row['num']);
}
for ($x=1; $x<=4; $x++) 
{
	if(empty($count[$x]))
	{
		$count[$x] = 0;
	}
}
//��ҳ
require_once(QISHI_ROOT_PATH.'include/page.class.php');
$perpage = 10;
$total_sql="SELECT COUNT(*) AS num from ".table('evaluation_record')." WHERE uid=".$_SESSION['uid'];
$total_val=$db->get_total($total_sql);
$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
$currenpage=$page->nowindex;
$offset=($currenpage-1)*$perpage;
$limit=" LIMIT ".intval($offset).','.intval($perpage);
//�����Ϣ
$paper_sql = "SELECT r.*,name FROM ".table('evaluation_record')." AS r  LEFT JOIN  ".table('evaluation_type')." AS t  ON r.type_id=t.id  WHERE uid=".$_SESSION['uid']."  ORDER BY addtime desc ".$limit;
$record_list=$db->getall($paper_sql);
foreach ($record_list as $key => $value) 
{
	switch ($value['type_id']) {
		case '1':
			$value['url'] = url_rewrite('QS_selfcognition');
			break;
		case '2':
			$value['url'] = url_rewrite('QS_occupation');
			break;
		case '3':
			$value['url'] = url_rewrite('QS_talents');
			break;
		default:
			$value['url'] = url_rewrite('QS_management');
			break;
	}
	$record_list[$key] = $value;
}
$smarty->assign('user_info',$user_info);
$smarty->assign('count',$count);
$smarty->assign('record_list',$record_list);
if($total_val>$perpage)
{
	$smarty->assign('page',$page->show(8));
}
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