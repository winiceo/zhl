<?php
 /*
 * 74cms 管理中心 广告广利函数
*/
 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
 //获取广告列表
function get_ad_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	$info = $db->getall("SELECT a.*,c.categoryname FROM ".table('ad_app')." AS a ".$get_sql." order BY a.show_order DESC,a.id DESC ".$limit);
	return $info;
}
//获取广告位
function get_ad_app_category($type=NULL)
{
	global $db;
	if ($type) $wheresql=" where  type_id=".intval($type).""; 
	$sql = "select * from ".table('ad_app_category').$wheresql." order BY id asc";
	$info = $db->getall($sql);
	return $info;
}
//获取广告(单个)
function get_ad_one($val)
{
	global $db;
	$sql = "select * from ".table('ad_app')." where id=".intval($val). " LIMIT 1";
	$arr=$db->getone($sql);
	$arr['starttime']=$arr['starttime']=="0"?'':convert_datefm($arr['starttime'],1);
	$arr['deadline']=$arr['deadline']=="0"?'':convert_datefm($arr['deadline'],1);
	return $arr;
}
//删除广告
function del_ad($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('ad_app')." WHERE id IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
		//填写管理员日志
		write_log("后台删除APP广告成功", $_SESSION['admin_name'],3);
	}
	return $return;
}

?>