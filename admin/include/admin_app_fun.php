<?php
 /*
 * 74cms �������� ����������
*/
 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
 //��ȡ����б�
function get_ad_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	$info = $db->getall("SELECT a.*,c.categoryname FROM ".table('ad_app')." AS a ".$get_sql." order BY a.show_order DESC,a.id DESC ".$limit);
	return $info;
}
//��ȡ���λ
function get_ad_app_category($type=NULL)
{
	global $db;
	if ($type) $wheresql=" where  type_id=".intval($type).""; 
	$sql = "select * from ".table('ad_app_category').$wheresql." order BY id asc";
	$info = $db->getall($sql);
	return $info;
}
//��ȡ���(����)
function get_ad_one($val)
{
	global $db;
	$sql = "select * from ".table('ad_app')." where id=".intval($val). " LIMIT 1";
	$arr=$db->getone($sql);
	$arr['starttime']=$arr['starttime']=="0"?'':convert_datefm($arr['starttime'],1);
	$arr['deadline']=$arr['deadline']=="0"?'':convert_datefm($arr['deadline'],1);
	return $arr;
}
//ɾ�����
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
		//��д����Ա��־
		write_log("��̨ɾ��APP���ɹ�", $_SESSION['admin_name'],3);
	}
	return $return;
}

?>