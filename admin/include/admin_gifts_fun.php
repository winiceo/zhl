<?php
 /*
 * 74cms ÀñÆ·¿¨
*/
 if(!defined('IN_QISHI'))
{
 	die('Access Denied!');
}
function get_gifts_category()
{
	global $db;
	$sql = "select * from ".table('gifts_type')."  order BY t_id DESC";
	return $db->getall($sql);
}
function get_gifts_category_one($id)
{
	global $db;
	$sql = "select * from ".table('gifts_type')." where t_id=".intval($id)." LIMIT 1";
	return $db->getone($sql);
}
function del_gifts_category($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	$info=$db->getone("select t_id from ".table('gifts')." WHERE t_id IN (".$sqlin.")  LIMIT 1");
	if (!empty($info))
	{
	return -1;
	}
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('gifts_type')." WHERE t_id IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
function get_gifts($offset, $perpage, $sql= '')
{
	global $db,$_CFG;
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT * FROM ".table('gifts')." AS g ".$sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row_arr[] = $row;
	}
	return $row_arr;
}
function del_gifts($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('gifts')." WHERE id IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
function del_gifts_tid($tid)
{
	global $db;
	if(!is_array($tid)) $tid=array($tid);
	$return=0;
	$sqlin=implode(",",$tid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('gifts')." WHERE t_id IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
function get_members_gifts($offset, $perpage, $sql= '')
{
	global $db,$_CFG;
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT * FROM ".table('members_gifts')." AS g ".$sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row_arr[] = $row;
	}
	return $row_arr;
}
?>