<?php
 /*
 * 74cms ���˻�Ա����
*/
if(!defined('IN_QISHI'))
{
 die('Access Denied!');
}
//��ȡ����ѵ�����б�
function get_resume_training($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_training')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
	return $db->getall($sql);
}
//��ȡ ���� ��ѵ����
function get_resume_training_one($uid,$pid,$id)
{
	global $db;
	$sql = "select * from ".table('resume_training')." where id='".intval($id)."' AND uid='".intval($uid)."'  AND pid='".intval($pid)."'  LIMIT 1 ";
	return $db->getone($sql);
}

//��ȡ�����������б�
function get_resume_language($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_language')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
	return $db->getall($sql);
}
//��ȡ ���� ��������
function get_resume_language_one($uid,$pid,$id)
{
	global $db;
	$sql = "select * from ".table('resume_language')." where id='".intval($id)."' AND uid='".intval($uid)."'  AND pid='".intval($pid)."'  LIMIT 1 ";
	return $db->getone($sql);
}
//��ȡ��֤���б�
function get_resume_credent($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_credent')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
	return $db->getall($sql);
}
//��ȡ ���� ֤������
function get_resume_credent_one($uid,$pid,$id)
{
	global $db;
	$sql = "select * from ".table('resume_credent')." where id='".intval($id)."' AND uid='".intval($uid)."'  AND pid='".intval($pid)."'  LIMIT 1 ";
	return $db->getone($sql);
}


// ��ȡ��������ͼƬ
function get_resume_img($uid,$pid)
{
	global $db;
	$sql = "SELECT * FROM ".table('resume_img')." WHERE  resume_id='".intval($pid)."' AND uid='".intval($uid)."' ";
	return $db->getall($sql);
}


//��ȡ����ְλ
function get_resume_jobs($pid)
{
	global $db;
	$pid=intval($pid);
	$sql = "select * from ".table('resume_jobs')." where pid='{$pid}'  LIMIT 20" ;
	return $db->getall($sql);
}

?>