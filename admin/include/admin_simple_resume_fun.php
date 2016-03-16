<?php
 /*
 * 74cms ΢��Ƹ
*/
if(!defined('IN_QISHI'))
{
 die('Access Denied!');
}
function get_simple_list($offset, $perpage, $sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT {$offset},{$perpage} ";
	$result = $db->query("SELECT * FROM ".table('simple')." {$sql} {$limit}");
	while($row = $db->fetch_array($result))
	{
	$row_arr[] = $row;
	}
	return $row_arr;
}
//΢�����б�
function get_simple_resume_list($offset, $perpage, $sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT {$offset},{$perpage} ";
	$result = $db->query("SELECT * FROM ".table('simple_resume')." {$sql} {$limit}");
	while($row = $db->fetch_array($result))
	{
	$row_arr[] = $row;
	}
	return $row_arr;
}
function simple_del($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('simple_resume')." WHERE id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
		//��д����Ա��־
		write_log("��̨ɾ��idΪ".$sqlin."��΢���� , ��ɾ��".$return."��", $_SESSION['admin_name'],3);
	}
	return $return;
}
function simple_refresh($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('simple_resume')." SET refreshtime='".time()."'  WHERE id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
		//��д����Ա��־
		write_log("��̨ˢ��idΪ".$sqlin."��΢���� , ��ˢ��".$return."��", $_SESSION['admin_name'],3);
	}
	return $return;
}
//΢�������
function simple_resume_audit($id,$audit)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$return=0;
	$sqlin=implode(",",$id);	
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('simple_resume')." SET audit='".intval($audit)."'  WHERE id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
		//��д����Ա��־
		write_log("��̨���idΪ".$sqlin."��΢���� , �����".$return."��", $_SESSION['admin_name'],3);
	}
	return $return;
}
//����΢�����ö�
function simple_resume_hot($id,$is_hot)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$return=0;
	$sqlin=implode(",",$id);	
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('simple_resume')." SET is_hot='".intval($is_hot)."'  WHERE id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
		//��д����Ա��־
		write_log("��̨�ö�idΪ".$sqlin."��΢���� , ���ö�".$return."��", $_SESSION['admin_name'],3);
	}
	return $return;
}
//����΢��������ְλ
function add_simple_resume_jobs($pid,$str)
{
	global $db;
	$db->query("Delete from ".table('simple_resume_jobs')." WHERE pid='".intval($pid)."'");
	$str=trim($str);
	$arr=explode("-",$str);
	if (is_array($arr) && !empty($arr))
	{
		foreach($arr as $a)
		{
		$a=explode(".",$a);
		$setsqlarr['pid']=intval($pid);
		$setsqlarr['category']=intval($a[0]);
		$setsqlarr['subclass']=intval($a[1]);
			if (!$db->inserttable(table('simple_resume_jobs'),$setsqlarr))return false;
		}
		//��д����Ա��־
		write_log("��̨����΢��������ְλ", $_SESSION['admin_name'],3);
	}
	return true;
}
//����΢����������ҵ
function add_simple_resume_trade($pid,$str)
{
	global $db;
	$db->query("Delete from ".table('simple_resume_trade')." WHERE pid='".intval($pid)."'");
	$str=trim($str);
	$arr=explode(",",$str);
	if (is_array($arr) && !empty($arr))
	{
		foreach($arr as $a)
		{
		$setsqlarr['pid']=intval($pid);
		$setsqlarr['trade']=intval($a);
			if (!$db->inserttable(table('simple_resume_trade'),$setsqlarr))return false;
		}
		//��д����Ա��־
		write_log("��̨����΢����������ҵ", $_SESSION['admin_name'],3);
	}
	return true;
}
?>