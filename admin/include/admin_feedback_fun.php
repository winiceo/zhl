<?php
 /*
 * 74cms Ͷ���뽨����غ���
*/
 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
function get_feedback_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	$sql = "select * from ".table('feedback')." ".$get_sql.$limit;
	$val=$db->getall($sql);
	return $val;
}
function get_feedback_one($id)
{
	global $db;
	$sql = "select * from ".table('feedback')." where id=".intval($id);
	$val=$db->getone($sql);
	return $val;
}
function del_feedback($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("Delete from ".table('feedback')." WHERE id IN (".$sqlin.")")) return false;
	$return=$return+$db->affected_rows();
	}
	return $return;
}
function get_report_list($offset,$perpage,$get_sql= '',$type)
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	if($type==1){
		$result = $db->query("SELECT r.*,m.username FROM ".table('report')." AS r ".$get_sql.$limit);
		while($row = $db->fetch_array($result))
		{
		$row['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$row['jobs_id']));
		$row_arr[] = $row;
		}
	}else{
		$result = $db->query("SELECT r.*,m.username FROM ".table('report_resume')." AS r ".$get_sql.$limit);
		while($row = $db->fetch_array($result))
		{
		$row['resume_url']=url_rewrite('QS_resumeshow',array('id'=>$row['resume_id']));
		$row_arr[] = $row;
		}
	}
	
	return $row_arr;
}
//�������
function report_audit($id,$audit,$type,$rid)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$return=0;
	$sqlin=implode(",",$id);	
	$sqlrin=implode(",",$rid);
	$rule=get_cache('points_rule');
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{	
		if($type==1) {
			if (!$db->query("update  ".table('report')." SET audit='".intval($audit)."'  WHERE id IN (".$sqlin.")")) return false;
		} else {
			if (!$db->query("update  ".table('report_resume')." SET audit='".intval($audit)."'  WHERE id IN (".$sqlin.")")) return false;
		}
		$return=$return+$db->affected_rows();
		//����վ����
		if($type==1) {
			$result = $db->query("SELECT * FROM ".table('report')." WHERE id IN ({$sqlin})");
		} else {
			$result = $db->query("SELECT * FROM ".table('report_resume')." WHERE id IN ({$sqlin})");
		}
		while($list = $db->fetch_array($result))
		{
			$user_info=get_user($list['uid']);
			$timestring = date("Y��m��d��",time());
			// $type->1  ���˾ٱ�ְλ
			if($type==1) 
			{
				// ְλ��Ϣ ��ҵ��Ա��Ϣ
				$jobsurl=url_rewrite('QS_jobsshow',array('id'=>$list['jobs_id']));
				$jobsinfo = $db->getone("SELECT * FROM ".table('jobs')." WHERE id=".intval($list['jobs_id'])." UNION ALL SELECT * FROM ".table('jobs_tmp')." WHERE id=".$list['jobs_id']);
				if(!$jobsinfo)
				{
					continue;
				}
				$user_info_com=get_user($jobsinfo['uid']);
				// ����ʵ
				if($audit==2)
				{
					//���˻��ֲ���
					report_deal($user_info['uid'],$rule['report_jobs']['type'],$rule['report_jobs']['value']);
					$user_points=get_user_points($user_info['uid']);
					$operator=$rule['report_jobs']['type']=="1"?"+":"-";
					write_memberslog($user_info['uid'],2,9001,$user_info_com['username']," �ٱ�ְλ���ͨ����{$_CFG['points_byname']}({$operator}{$rule['report_jobs']['value']})��(ʣ��:{$user_points})",2,7003,"�ٱ�ְλ���ͨ��","{$operator}{$rule['report_jobs']['value']}","{$user_points}");

					$msg_p = "������".$rule['report_jobs']['value']."���֣���л����".$_CFG['site_name']."��֧�֣�";
					$setsqlarr_p['message']="����".$timestring."�ٱ���ҵ��".$jobsinfo['companyname']."��������ְλ����<a href=\"{$jobsurl}\" target=\"_blank\">{$list['jobs_name']}</a>��,��ƽ̨��ʵ�����ʵ".$msg_p;
					$setsqlarr_p['msgtype']=1;
					$setsqlarr_p['msgtouid']=$user_info['uid'];
					$setsqlarr_p['msgtoname']=$user_info['username'];
					$setsqlarr_p['dateline']=time();
					$setsqlarr_p['replytime']=time();
					$setsqlarr_p['new']=1;
					$db->inserttable(table('pms'),$setsqlarr_p);

					//�۳���ҵ���ֲ���
					report_deal($jobsinfo['uid'],$rule['company_jobs_report_points']['type'],$rule['company_jobs_report_points']['value']);
					$user_points=get_user_points($jobsinfo['uid']);
					$operator=$rule['company_jobs_report_points']['type']=="1"?"+":"-";
					write_memberslog($jobsinfo['uid'],1,9001,$user_info_com['username']," ��ҵ����ְλ���ٱ���{$_CFG['points_byname']}({$operator}{$rule['company_jobs_report_points']['value']})��(ʣ��:{$user_points})",1,1016,"��ҵ����ְλ���ٱ�","{$operator}{$rule['company_jobs_report_points']['value']}","{$user_points}");

					$msg_c = "���۳�".$rule['company_jobs_report_points']['value']."���֣������д����������������Ŵ���";
					$setsqlarr_c['message']="��������ְλ����<a href=\"{$jobsurl}\" target=\"_blank\">{$list['jobs_name']}</a>����".$timestring."���ٱ�����ƽ̨��ʵ�����ʵ".$msg_c;
					$setsqlarr_c['msgtype']=1;
					$setsqlarr_c['msgtouid']=$user_info_com['uid'];
					$setsqlarr_c['msgtoname']=$user_info_com['username'];
					$setsqlarr_c['dateline']=time();
					$setsqlarr_c['replytime']=time();
					$setsqlarr_c['new']=1;
					$db->inserttable(table('pms'),$setsqlarr_c);
				}
				else
				{
					$setsqlarr['message']="����".$timestring."�ٱ���ҵ��".$jobsinfo['companyname']."��������ְλ����<a href=\"{$jobsurl}\" target=\"_blank\">{$list['jobs_name']}</a>��,��ƽ̨��ʵ�������ʵ";
					$setsqlarr['msgtype']=1;
					$setsqlarr['msgtouid']=$user_info['uid'];
					$setsqlarr['msgtoname']=$user_info['username'];
					$setsqlarr['dateline']=time();
					$setsqlarr['replytime']=time();
					$setsqlarr['new']=1;
					$db->inserttable(table('pms'),$setsqlarr);
				}
			} 
			// ��ҵ�ٱ�����
			else 
			{
				// ������Ϣ  ��Ա��Ϣ
				$resumeurl=url_rewrite('QS_resumeshow',array('id'=>$list['resume_id']));
				$resumeinfo = $db->getone("SELECT * FROM ".table('resume')." WHERE id=".intval($list['resume_id']));
				if(!$resumeinfo)
				{
					continue;
				}
				$user_info_per=get_user($resumeinfo['uid']);
				// ����ʵ
				if ($audit==2)
				{
					// ��ҵ�ٱ����� ��û���
					report_deal($user_info['uid'],$rule['company_report_resume_points']['type'],$rule['company_report_resume_points']['value']);
					$user_points=get_user_points($user_info['uid']);
					$operator=$rule['company_report_resume_points']['type']=="1"?"+":"-";
					write_memberslog($user_info['uid'],1,9001,$user_info['username']," ��ҵ�ٱ�������{$_CFG['points_byname']}({$operator}{$rule['company_report_resume_points']['value']})��(ʣ��:{$user_points})",1,7003,"��ҵ�ٱ�����","{$operator}{$rule['company_report_resume_points']['value']}","{$user_points}");
					$msg = "������".$rule['company_report_resume_points']['value']."���֣���л����".$_CFG['site_name']."��֧�֣�";
					$setsqlarr_c['message']="����".$timestring."�ٱ��ļ�������<a href=\"{$resumeurl}\" target=\"_blank\">{$list['title']}</a>������ƽ̨��ʵ�����ʵ".$msg;
					$setsqlarr_c['msgtype']=1;
					$setsqlarr_c['msgtouid']=$user_info['uid'];
					$setsqlarr_c['msgtoname']=$user_info['username'];
					$setsqlarr_c['dateline']=time();
					$setsqlarr_c['replytime']=time();
					$setsqlarr_c['new']=1;
					$db->inserttable(table('pms'),$setsqlarr_c);

					//���˿۳����ֲ���
					report_deal($user_info_per['uid'],$rule['resume_report']['type'],$rule['resume_report']['value']);
					$user_points=get_user_points($user_info_per['uid']);
					$operator=$rule['resume_report']['type']=="1"?"+":"-";
					write_memberslog($user_info_per['uid'],2,9001,$user_info_per['username']," �������ٱ����ͨ����{$_CFG['points_byname']}({$operator}{$rule['resume_report']['value']})��(ʣ��:{$user_points})",2,7003,"�������ٱ����ͨ��","{$operator}{$rule['resume_report']['value']}","{$user_points}");

					$msg_p = "���۳�".$rule['resume_report']['value']."���֣������д����������������Ŵ���";
					$setsqlarr_p['message']="�������ļ�����".$resumeinfo['title']."����".$timestring."���ٱ�����ƽ̨��ʵ�����ʵ".$msg_p;
					$setsqlarr_p['msgtype']=1;
					$setsqlarr_p['msgtouid']=$user_info_per['uid'];
					$setsqlarr_p['msgtoname']=$user_info_per['username'];
					$setsqlarr_p['dateline']=time();
					$setsqlarr_p['replytime']=time();
					$setsqlarr_p['new']=1;
					$db->inserttable(table('pms'),$setsqlarr_p);
				}
				else
				{
					$setsqlarr['message']="����".$timestring."�ٱ��ļ�������<a href=\"{$resumeurl}\" target=\"_blank\">{$list['title']}</a>������ƽ̨��ʵ�������ʵ";
					$setsqlarr['msgtype']=1;
					$setsqlarr['msgtouid']=$user_info['uid'];
					$setsqlarr['msgtoname']=$user_info['username'];
					$setsqlarr['dateline']=time();
					$setsqlarr['replytime']=time();
					$setsqlarr['new']=1;
					$db->inserttable(table('pms'),$setsqlarr);
				}
			}
		 }
	}
	return $return;
}
function get_user($uid)
{
	global $db;
	$uid=intval($uid);
	$sql = "select * from ".table('members')." where uid = '{$uid}' LIMIT 1";
	return $db->getone($sql);
}
function del_report($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$del_id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("Delete from ".table('report')." WHERE id IN (".$sqlin.")")) return false;
	$return=$return+$db->affected_rows();
	}
	return $return;
}
function del_report_resume($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$del_id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("Delete from ".table('report_resume')." WHERE id IN (".$sqlin.")")) return false;
	$return=$return+$db->affected_rows();
	}
	return $return;
}
function report_deal($uid,$i_type=1,$points=0)
{
		global $db,$timestamp;
		$points_val=get_user_points($uid);
		if ($i_type==1)
		{
		$points_val=$points_val+$points;
		}
		if ($i_type==2)
		{
		$points_val=$points_val-$points;
		$points_val=$points_val<0?0:$points_val;
		}
		$sql = "UPDATE ".table('members_points')." SET points= '{$points_val}' WHERE uid='{$uid}'  LIMIT 1 ";
		return $db->query($sql);
}
function get_user_points($uid)
{
	global $db;
	$sql = "select * from ".table('members_points')." where uid = ".intval($uid)."  LIMIT 1 ";
	$points=$db->getone($sql);
	return $points['points'];
}
?>