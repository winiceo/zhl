<?php
 /*
 * 74cms 个人会员函数
*/
if(!defined('IN_QISHI'))
{
 die('Access Denied!');
}
//获取：培训经历列表
function get_resume_training($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_training')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
	return $db->getall($sql);
}
//获取 单条 培训经历
function get_resume_training_one($uid,$pid,$id)
{
	global $db;
	$sql = "select * from ".table('resume_training')." where id='".intval($id)."' AND uid='".intval($uid)."'  AND pid='".intval($pid)."'  LIMIT 1 ";
	return $db->getone($sql);
}

//获取：语言能力列表
function get_resume_language($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_language')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
	return $db->getall($sql);
}
//获取 单条 语言能力
function get_resume_language_one($uid,$pid,$id)
{
	global $db;
	$sql = "select * from ".table('resume_language')." where id='".intval($id)."' AND uid='".intval($uid)."'  AND pid='".intval($pid)."'  LIMIT 1 ";
	return $db->getone($sql);
}
//获取：证书列表
function get_resume_credent($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_credent')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
	return $db->getall($sql);
}
//获取 单条 证书能力
function get_resume_credent_one($uid,$pid,$id)
{
	global $db;
	$sql = "select * from ".table('resume_credent')." where id='".intval($id)."' AND uid='".intval($uid)."'  AND pid='".intval($pid)."'  LIMIT 1 ";
	return $db->getone($sql);
}


// 获取简历附件图片
function get_resume_img($uid,$pid)
{
	global $db;
	$sql = "SELECT * FROM ".table('resume_img')." WHERE  resume_id='".intval($pid)."' AND uid='".intval($uid)."' ";
	return $db->getall($sql);
}


//获取意向职位
function get_resume_jobs($pid)
{
	global $db;
	$pid=intval($pid);
	$sql = "select * from ".table('resume_jobs')." where pid='{$pid}'  LIMIT 20" ;
	return $db->getall($sql);
}

?>