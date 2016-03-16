<?php
 /*
 * 74cms 计划任务 自动刷新简历
*/
if(!defined('IN_QISHI'))
{
die('Access Denied!');
}
	global $_CFG;
	$time=time();
	$db->getall("Delete from ".table('resume_auto_refresh')." where deadline_time<$time");
	$row=$db->getall("SELECT resume_id FROM ".table('resume_auto_refresh')." where deadline_time>$time");
	foreach ($row as $key => $value)
	{
		$id_str.=",".$value['resume_id'];
	}
	$id_str=ltrim($id_str,",");
	refresh_resume($id_str);
	function refresh_resume($pid)
	{
		global $db;
		$time=time();
		if (!$db->query("update  ".table('resume')."  SET refreshtime='{$time}'  WHERE id='{$pid}' ")) return false;
		if (!$db->query("update  ".table('resume_search_rtime')."  SET refreshtime='{$time}'  WHERE id='{$pid}' ")) return false;
		if (!$db->query("update  ".table('resume_search_key')."  SET refreshtime='{$time}'  WHERE id='{$pid}' ")) return false;
		return true;
	}

	//更新任务时间表
	if ($crons['weekday']>=0)
	{
	$weekday=array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$nextrun=strtotime("Next ".$weekday[$crons['weekday']]);
	}
	elseif ($crons['day']>0)
	{
	$nextrun=strtotime('+1 months'); 
	$nextrun=mktime(0,0,0,date("m",$nextrun),$crons['day'],date("Y",$nextrun));
	}
	else
	{
	$nextrun=time();
	}
	if ($crons['hour']>=0)
	{
	$nextrun=strtotime('+1 days',$nextrun); 
	$nextrun=mktime($crons['hour'],0,0,date("m",$nextrun),date("d",$nextrun),date("Y",$nextrun));
	}
	if (intval($crons['minute'])>0)
	{
	$nextrun=strtotime('+1 hours',$nextrun); 
	$nextrun=mktime(date("H",$nextrun),$crons['minute'],0,date("m",$nextrun),date("d",$nextrun),date("Y",$nextrun));
	}
	$setsqlarr['nextrun']=$nextrun;
	$setsqlarr['lastrun']=time();
	$db->updatetable(table('crons'), $setsqlarr," cronid ='".intval($crons['cronid'])."'");
?>