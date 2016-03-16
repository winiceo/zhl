<?php
 /*
 * 74cms ��Ƹ��
*/
 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
function get_jobfair($offset, $perpage, $sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT * FROM ".table('jobfair')." ".$sql." ".$limit);
	while($row = $db->fetch_array($result))
	{
	$row_arr[] = $row;
	}
	return $row_arr;
}
function get_jobfair_display()
{
	global $db;
	$info = $db->getall("SELECT * FROM ".table('jobfair')." WHERE display=1  order BY `order` DESC,id DESC");
	return $info;
}
function get_jobfair_audit()
{
	global $db;
	$info = $db->getall("SELECT * FROM ".table('jobfair')." WHERE display=1 AND predetermined_status=1 AND holddate_start>".time()." order BY `order` DESC,id DESC");
	return $info;
}
function del_jobfair($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('jobfair')." WHERE id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
		write_log("ɾ����Ƹ��idΪ".$sqlin."����Ƹ��,��ɾ��".$return."��", $_SESSION['admin_name'],3);
	}
	return $return;
}
function get_jobfair_exhibitors($offset, $perpage, $sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT * FROM ".table('jobfair_exhibitors')." ".$sql." ".$limit);
	while($row = $db->fetch_array($result))
	{
	if ($row['uid']>0 && $row['company_id']>0)
	{
	$row['company_url']=url_rewrite('QS_companyshow',array('id'=>$row['company_id']));
	}
	else
	{
	$row['company_url']="";
	}
	$row['jobfair_url']=url_rewrite('QS_jobfairshow',array('id'=>$row['jobfairid']),1,$row['subsite_id']);
	$row_arr[] = $row;
	}
	return $row_arr;
}
function del_exhibitors($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('jobfair_exhibitors')." WHERE id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
		write_log("ɾ��idΪ".$sqlin."�Ĳλ���ҵ,��ɾ��".$return."��", $_SESSION['admin_name'],3);
	}
	return $return;
}
function edit_exhibitors_audit($id,$audit)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$return=0;
	$sqlin=implode(",",$id);	
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('jobfair_exhibitors')." SET audit='".intval($audit)."'  WHERE id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
		write_log("��idΪ".$sqlin."�Ĳλ���ҵ�����״̬Ϊ".intval($audit).",��ɾ��".$return."��", $_SESSION['admin_name'],3);
	}
	return $return;
}
/*
	ר����Ƹ��
*/
// �б�
function get_jobfair_section($offset, $perpage, $sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT * FROM ".table('jobfair_section')." ".$sql." ".$limit);
	while($row = $db->fetch_array($result))
	{
	if($row['is_singleness']==1)
	{
		$row['jobfair_url']=url_rewrite('QS_industry_jobfair',array('id'=>$row['id']),1,$row['subsite_id']);
	}
	else
	{
		$row['jobfair_url']=url_rewrite('QS_industrys_jobfair',array('id'=>$row['id']),1,$row['subsite_id']);
	}
	$row_arr[] = $row;
	}
	return $row_arr;
}
// ר����Ƹ�� ��ϸ
function get_jobfair_section_one($id)
{
	global $db;
	$id= intval($id);
	$row = $db->getone("SELECT * from ".table("jobfair_section")." where id=$id limit 1");
	if($row['is_singleness']==1)
	{
		$row['jobfair_url']=url_rewrite('QS_industry_jobfair',array('id'=>$row['id']),1,$row['subsite_id']);
	}
	else
	{
		$row['jobfair_url']=url_rewrite('QS_industrys_jobfair',array('id'=>$row['id']),1,$row['subsite_id']);
	}
	return $row;
}
// ר����Ƹ�� �λ���ҵ�б�
function get_jobfair_section_company($offset, $perpage, $sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT j.*,c.companyname,c.trade_cn FROM ".table('jobfair_section_company')." as j ".$sql." ".$limit);
	while($row = $db->fetch_array($result))
	{
	$row_arr[] = $row;
	}
	return $row_arr;
}
//ɾ��ר����Ƹ�� 
function del_jobfair_section($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('jobfair_section')." WHERE id IN (".$sqlin.")")) return false;
		if (!$db->query("Delete from ".table('jobfair_section_company')." WHERE jobfair_id IN (".$sqlin.")")) return false;	
		$return=$return+$db->affected_rows();
	}
	return $return;
}
// ɾ��ר����Ƹ�� �λ���ҵ
function del_jobfair_section_company($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('jobfair_section_company')." WHERE id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
?>