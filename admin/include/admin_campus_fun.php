<?php
 /*
 * 74cms �������� ����������
*/
 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
//��ȡ����ԺУ�б�
function get_cooperate_campus_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT * FROM ".table('cooperate_campus').$get_sql." order BY addtime DESC,id DESC ".$limit);
	while($row = $db->fetch_array($result))
	{
		//ͳ��ע���ҵ����
		$sql = "SELECT COUNT(distinct(pid)) AS num FROM ".table('resume_education')." as e INNER JOIN ".table('resume')." as r on r.id=e.pid WHERE e.school='".trim($row['campusname']). "'  AND r.display=1 ";
		$graduate_num = $db->get_total($sql);
		$row['graduate'] = intval($graduate_num);
		$row_arr[] = $row;
	}
	return $row_arr;
}
//��ȡԺУ��Ϣ(����)
function get_campus_one($val)
{
	global $db;
	$sql = "select * from ".table('cooperate_campus')." where id=".intval($val). " LIMIT 1";
	$arr=$db->getone($sql);
	return $arr;
}
//��ȡԺУ���ͼƬ
function get_campus_img($val)
{
	global $db;
	$sql = "select * from ".table('cooperate_campus_img')." where campus_id=".intval($val). " ";
	$arr=$db->getall($sql);
	return $arr;
}
//ͳ��ԺУ���ͼƬ����
function count_campus_img($campus_id)
{
	global $db;
	$sql = "SELECT COUNT(*) AS num FROM ".table('cooperate_campus_img')." WHERE campus_id=".intval($campus_id). " ";
	$num=$db->get_total($sql);
	return $num;
}
//ɾ��ԺУ���ͼƬ
function del_campus_img($id,$campus_id)
{
	global $db;
	if (!$db->query("Delete from ".table('cooperate_campus_img')." WHERE id = {$id} AND campus_id = {$campus_id}")) return false;
	return true;
}
//ɾ��ԺУ
function del_campus($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('cooperate_campus')." WHERE id IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
		//ɾ�����ͼƬ
		if (!$db->query("Delete from ".table('cooperate_campus_img')." WHERE campus_id IN ({$sqlin})")) return false;
		//��д����Ա��־
		write_log("��̨�ɹ�ɾ������ԺУ", $_SESSION['admin_name'],3);
		return $return;
	}
	else
	{
		return false;
	}
}

?>