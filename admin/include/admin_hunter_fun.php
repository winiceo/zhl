<?php
 /*
 * 74cms �������� ��ͷ�����û���غ���
*/
 if(!defined('IN_QISHI'))
 {
die('Access Denied!');
 }
 function get_points_rule()
{
	global $db;
	$sql = "select * from ".table('members_points_rule')." WHERE utype='3' order BY id asc";
	$list=$db->getall($sql);
	return $list;
}
function get_setmeal($apply=true)
{
	global $db;
	if ($apply==true)
	{
	$where="";
	}
	else
	{
	$where=" WHERE display=1 ";
	} 
	$sql = "select * from ".table('hunter_setmeal').$where."  order BY display desc,show_order desc,id asc";
	return $db->getall($sql);
}
function get_setmeal_one($id)
{
	global $db;
	$sql = "select * from ".table('hunter_setmeal')."  WHERE id=".intval($id)."";
	return $db->getone($sql);
}
function del_setmeal_one($id)
{
	global $db;
	if (!$db->query("Delete from ".table('hunter_setmeal')." WHERE id=".intval($id)." ")) return false;
	//��д����Ա��־
	write_log("��̨�ɹ�ɾ����ͷ�ײ�", $_SESSION['admin_name'],3);
	return true;
}
function get_hunterjobs($offset,$perpage,$get_sql= '')
{
	global $db,$timestamp;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query($get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
		$row['companyname']=cut_str($row['companyname'],18,0,"...");
		if($row['utype']=='1'){
			$company=$db->getone('select id from '.table('company_profile').' where uid = '.$row['uid'].' limit 1 ');
			$row['company_url']=url_rewrite('QS_companyshow',array('id'=>$company['id']));
		}
		$row['jobs_url']=url_rewrite('QS_hunter_jobsshow',array('id'=>$row['id']),1,$row['subsite_id']);
		$row_arr[] = $row;
	}
	return $row_arr;
}
function del_hunterjobs($del_id)
{
	global $db;
	$return=0;
	if (!is_array($del_id)) $del_id=array($del_id);
	$sqlin=implode(",",$del_id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("Delete from ".table('hunter_jobs')." WHERE id IN ({$sqlin}) ")) return false;
	$return=$return+$db->affected_rows();
	}
	write_log("ɾ����ͷְλidΪ".$sqlin."��ְλ,��ɾ��".$return."��", $_SESSION['admin_name'],3);
	return $return;
}
function edit_jobs_audit($id,$audit,$reason,$pms_notice='1')
{
	global $db,$_CFG;
	$audit=intval($audit);
	$reason=trim($reason);
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('hunter_jobs')." SET audit={$audit}  WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
			//����վ����
			if ($pms_notice=='1')
			{
					$result = $db->query("SELECT uid,jobs_name FROM ".table('hunter_jobs')." WHERE id IN ({$sqlin})");
					$reason=$reason==''?'ԭ��δ֪':'ԭ��'.$reason;
					while($list = $db->fetch_array($result))
					{
						$user_info=get_user($list['uid']);
						$setsqlarr['message']=$audit=='1'?"����������ͷְλ��{$list['jobs_name']},�ɹ�ͨ����վ����Ա��ˣ�":"����������ͷְλ��{$list['jobs_name']},δͨ����վ����Ա���,{$reason}";
						$setsqlarr['msgtype']=1;
						$setsqlarr['msgtouid']=$user_info['uid'];
						$setsqlarr['msgtoname']=$user_info['username'];
						$setsqlarr['dateline']=time();
						$setsqlarr['replytime']=time();
						$setsqlarr['new']=1;
						$db->inserttable(table('pms'),$setsqlarr);
					 }
			}
			//�����ʼ�
			$mailconfig=get_cache('mailconfig');
			$sms=get_cache('sms_config');
			if ($audit=="1" && $mailconfig['set_hunjobsallow']=="1")//���ͨ��
			{
					$result = $db->query("SELECT uid,jobs_name FROM ".table('hunter_jobs')." WHERE id IN ({$sqlin})");
					while($list = $db->fetch_array($result))
					{
						$user_info=get_user($list['uid']);
						if ($user_info['email_audit']=="1")
						{				
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&jobs_name=".$list['jobs_name']."&act=set_hunjobsallow");
						}
					}
			}
			if ($audit=="3" && $mailconfig['set_hunjobsnotallow']=="1")//���δͨ��
			{
					$result = $db->query("SELECT uid,jobs_name FROM ".table('hunter_jobs')." WHERE id IN ({$sqlin})");
					while($list = $db->fetch_array($result))
					{
						$user_info=get_user($list['uid']);
						if ($user_info['email_audit']=="1")
						{
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&jobs_name=".$list['jobs_name']."&act=set_hunjobsnotallow");
						}
					}
			}
			//sms		
			if ($audit=="1" && $sms['open']=="1" && $sms['set_hunjobsallow']=="1" )
			{
				$mobilearray = array();
				$result = $db->query("SELECT uid,jobs_name FROM ".table('hunter_jobs')." WHERE id IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['mobile_audit']=="1")
					{
						//��ͬһ��Ա��ְλ�ŵ������� ����ȷ���û�ԱΨһ��
						$mobilearray[$list['uid']] = empty($mobilearray[$list['uid']]) ? $list['jobs_name'] : $mobilearray[$list['uid']].' , '.$list['jobs_name'];
					}
				}
				foreach ($mobilearray as $key => $value) 
				{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$key."&key=".asyn_userkey($key)."&jobs_name=".$value."&act=set_hunjobsallow");
				}
			}
			//sms
			if ($audit=="3" && $sms['open']=="1" && $sms['set_hunjobsnotallow']=="1" )//��֤δͨ��
			{
				$mobilearray = array();
				$result = $db->query("SELECT uid,jobs_name FROM ".table('hunter_jobs')." WHERE id IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['mobile_audit']=="1")
					{
						//��ͬһ��Ա��ְλ�ŵ������� ����ȷ���û�ԱΨһ��
						$mobilearray[$list['uid']] = empty($mobilearray[$list['uid']]) ? $list['jobs_name'] : $mobilearray[$list['uid']].' , '.$list['jobs_name'];
					}
				}
				foreach ($mobilearray as $key => $value) 
				{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$key."&key=".asyn_userkey($key)."&jobs_name=".$value."&act=set_hunjobsnotallow");
				}
			}
			//sms
			write_log("����ͷְλidΪ".$sqlin."��ְλ,���״̬����Ϊ".$audit."������".$return."��", $_SESSION['admin_name'],3);
		return $return;

	}
	else
	{
	return $return;
	}
}
function refresh_jobs($id)
{
	global $db;
	$return=0;
	if (!is_array($id)) $id=array($id);
	$time=time();
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('hunter_jobs')."  SET refreshtime='{$time}' WHERE id IN ({$sqlin})  ")) return false;
		$return=$return+$db->affected_rows();
		write_log("ˢ����ͷְλidΪ".$sqlin."��ְλ,��ˢ��".$return."��", $_SESSION['admin_name'],3);
	}
	return $return;
}
function delay_jobs($id,$days)
{
	global $db;
	$days=intval($days);
	$return=0;
	if (empty($days)) return false;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$time=time();
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$result = $db->query("SELECT id,deadline FROM ".table('hunter_jobs')." WHERE id IN ({$sqlin}) ");
		while($row = $db->fetch_array($result))
		{
			if ($row['deadline']>$time)
			{
			$deadline=strtotime("+{$days} day",$row['deadline']);
			}
			else
			{
			$deadline=strtotime("+{$days} day");
			}
			if (!$db->query("update  ".table('hunter_jobs')." SET deadline='{$deadline}'  WHERE id='{$row['id']}'  LIMIT 1")) return false;
			$return=$return+$db->affected_rows();
			write_log("������ͷְλidΪ".$sqlin."��ְλ,������".$return."��", $_SESSION['admin_name'],3);
		}
	}
	return $return;
}
function get_jobs_one($id,$uid='')
{
	global $db,$timestamp;
	$id=intval($id);
	if (!empty($uid)) $wheresql=" AND uid=".intval($uid);
	$val=$db->getone("select * from ".table('hunter_jobs')." where id='{$id}' {$wheresql} LIMIT 1");
	if (empty($val)) return false;
	$val['user']=get_user($val['uid']);
	$val['jobs_url']=url_rewrite('QS_hunter_jobsshow',array('id'=>$val['id']),1,$val['subsite_id']);
	if($val['utype']=='1'){
		$company=$db->getone('select id from '.table('company_profile').' where uid = '.$val['uid'].' limit 1 ');
		$val['company_url']=url_rewrite('QS_companyshow',array('id'=>$company['id']));
	}
	$val['deadline_days']=($val['deadline']-$timestamp)>0?"�ൽ��ʱ�仹��<strong style=\"color:#FF0000\">".sub_day($val['deadline'],$timestamp)."</strong>":"<span style=\"color:#FF6600\">Ŀǰ�ѹ���</span>";
	return $val;
}
function get_hunter($offset,$perpage,$get_sql= '',$mode=1)
{
	global $db;
	$colum=$mode==1?'p.points':'p.setmeal_name';
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT c.*,m.username,m.mobile,m.email as memail,{$colum} FROM ".table('hunter_profile')." AS c ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
		$row['hunter_url']=url_rewrite('QS_huntershow',array('id'=>$row['id']));
		$address = $db->getone("select log_address,log_id,log_uid from ".table("members_log")." where log_type = '1000' and log_uid = ".$row['uid']." order by log_id asc limit 1");
		$row['ipAddress'] = $address['log_address']; 
		$row_arr[] = $row;
	}
	return $row_arr;
}
function del_hunter($uid)
{
	global $db,$hunter_dir;
	if (!is_array($uid))$uid=array($uid);
	$sqlin=implode(",",$uid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$result = $db->query("SELECT photo_img FROM ".table('hunter_profile')." WHERE uid IN ({$sqlin})");
		while($row = $db->fetch_array($result))
		{
		@unlink($hunter_dir.$row['photo_img']);
		@unlink($hunter_dir.'thumb/'.$row['photo_img']);
		}
		if (!$db->query("Delete from ".table('hunter_profile')." WHERE uid IN ({$sqlin})")) return false;
		write_log("ɾ����ͷuidΪ".$sqlin."����ͷ��Ϣ", $_SESSION['admin_name'],3);
	return true;
	}
	return false;
}
function del_hunter_alljobs($uid)
{
	global $db;
	if (!is_array($uid))$uid=array($uid);
	$sqlin=implode(",",$uid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$db->query("Delete from ".table('hunter_jobs')." WHERE uid IN ({$sqlin})");	
		write_log("ɾ����ͷuidΪ".$sqlin."��ְλ", $_SESSION['admin_name'],3);	
		return true;
	}
	return false;
}
function edit_hunter_audit($uid,$audit,$reason,$pms_notice)
{
	global $db,$_CFG;	
	$audit=intval($audit);
	$pms_notice=intval($pms_notice);
	$reason=trim($reason);
	if (!is_array($uid)) $uid=array($uid);
	$sqlin=implode(",",$uid);	
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('hunter_profile')." SET audit='{$audit}'  WHERE uid IN ({$sqlin})")) return false;
		//����վ����
		if ($pms_notice=='1')
		{
			$reason=$reason==''?'��':$reason;
			if($audit=='1') {$note='�ɹ�ͨ����վ����Ա���!';}elseif($audit=='2'){$note='���������!';}else{$note='δͨ����վ����Ա��ˣ�';}
			$result = $db->query("SELECT huntername,uid FROM ".table('hunter_profile')." WHERE uid IN ({$sqlin})");
			while($list = $db->fetch_array($result))
			{
				$user_info=get_user($list['uid']);
				$setsqlarr['message']="��ע�����ͷ���ʣ�{$list['huntername']},".$note.'����˵����'.$reason;
				$setsqlarr['msgtype']=1;
				$setsqlarr['msgtouid']=$user_info['uid'];
				$setsqlarr['msgtoname']=$user_info['username'];
				$setsqlarr['dateline']=time();
				$setsqlarr['replytime']=time();
				$setsqlarr['new']=1;
				$db->inserttable(table('pms'),$setsqlarr);
			 }
		}
		if ($audit=='1') 
		{
		//3.4�����޸�ע��,ֻ�л���ģʽ��������
			if($_CFG['operation_hunter_mode']=='1'){
				$points_rule=get_cache('points_rule');
				if ($points_rule['hunter_auth']['value']>0)//�����������֤���ͻ���
				{
					gift_points($sqlin,'hunterauth',$points_rule['hunter_auth']['type'],$points_rule['hunter_auth']['value']);
				}
			}
		}
		$mailconfig=get_cache('mailconfig');
		$sms=get_cache('sms_config');
		if ($audit=="1" && $mailconfig['set_hunallow']=="1")//��֤ͨ��
		{
			$result = $db->query("SELECT huntername,uid FROM ".table('hunter_profile')." WHERE uid IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['email_audit']=="1")
					{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid={$list['uid']}&huntername={$list['huntername']}&key=".asyn_userkey($list['uid'])."&act=set_hunallow");
					}
				}
		}
		if ($audit=="3" && $mailconfig['set_hunnotallow']=="1")//��֤δͨ��
		{
			$result = $db->query("SELECT huntername,uid FROM ".table('hunter_profile')." WHERE uid IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['email_audit']=="1")
					{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid={$list['uid']}&huntername={$list['huntername']}&key=".asyn_userkey($list['uid'])."&act=set_hunnotallow");
					}
				}
		}
		//sms		
		if ($audit=="1" && $sms['open']=="1" && $sms['set_hunallow']=="1" )
		{
			$result = $db->query("SELECT huntername,uid FROM ".table('hunter_profile')." WHERE uid IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['mobile_audit']=="1")
					{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid={$list['uid']}&huntername={$list['huntername']}&key=".asyn_userkey($list['uid'])."&act=set_hunallow");
					}
				}
		}
		//sms
		if ($audit=="3" && $sms['open']=="1" && $sms['set_hunnotallow']=="1" )//��֤δͨ��
		{
			$result = $db->query("SELECT huntername,uid FROM ".table('hunter_profile')." WHERE uid IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['mobile_audit']=="1")
					{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid={$list['uid']}&huntername={$list['huntername']}&key=".asyn_userkey($list['uid'])."&act=set_hunnotallow");
					}
				}
		}
		write_log("����ͷuidΪ".$sqlin."��ͷ���״̬����Ϊ".$audit, $_SESSION['admin_name'],3);
	return true;
	}
	return false;
}
function gift_points($uid,$gift,$ptype,$points)
{
	 global $db;
	 $operator=$ptype=="1"?"+":"-";
	 $time=time();
	 if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$uid))
	 {
		$uid=explode(',',$uid);
	 }
	 if (!is_array($uid))$uid=array($uid);
	 if (!empty($uid) && is_array($uid))
	 {
	 	foreach($uid as $vuid)
		{
			$vuid=intval($vuid);
			if ($gift=='companyauth')
			{
				$com=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$vuid}' AND htype='{$gift}'  LIMIT 1");
				if(empty($com))
				{
				report_deal($vuid,$ptype,$points);
				$user=get_user($vuid);
				$mypoints=get_user_points($vuid);
				write_memberslog($vuid,3,9201,$user['username']," ��Ϊ����֤��ͷ����({$operator}{$points})��(ʣ��:{$mypoints})");
				$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$vuid}', '{$gift}','{$time}')");			
				}
			}			
		}
	 }
}
function refresh_hunter($uid,$refresjobs=false)
{
	global $db;
	$return=0;
	if (!is_array($uid))$uid=array($uid);
	$sqlin=implode(",",$uid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('hunter_profile')." SET refreshtime='".time()."'  WHERE uid IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		write_log("ˢ����ͷuidΪ".$sqlin."����ͷ��Ϣ��������".$return."��", $_SESSION['admin_name'],3);
		if ($refresjobs)
		{
		if (!$db->query("update  ".table('hunter_jobs')." SET refreshtime='".time()."'  WHERE uid IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		write_log("ˢ����ͷuidΪ".$sqlin."��ְλ��Ϣ��������".$return."��", $_SESSION['admin_name'],3);
		}
	}
	return $return;
}
function get_hunter_one_id($id)
{
	global $db;
	$id=intval($id);
	$sql = "select * from ".table('hunter_profile')." where id='{$id}'";
	$val=$db->getone($sql);
	$val['user']=get_user($val['uid']);
	if($val){
	$arr=explode('-',$val['companytelephone']);
	$val['code']=$arr[0];
	$val['companytelephone']=$arr[1];
	$val['workyears']=date('Y')-$val['worktime_start'];
	}
	return $val;
}
 function get_member_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT m.*,c.huntername,c.id,c.addtime FROM ".table('members')." as m ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['hunter_url']=url_rewrite('QS_huntershow',array('id'=>$row['id']));
	$row_arr[] = $row;
	}
	return $row_arr;
}
function delete_hunter_user($uid)
{
	global $db;
	if (!is_array($uid))$uid=array($uid);
	$sqlin=implode(",",$uid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if(defined('UC_API'))
		{
			include_once(QISHI_ROOT_PATH.'uc_client/client.php');
			foreach($uid as $tuid)
			{
			$userinfo=get_user($tuid);
			$uc_user=uc_get_user($userinfo['username']);
			$uc_uid_arr[]=$uc_user[0];
			}
			uc_user_delete($uc_uid_arr);
		}
		if (!$db->query("Delete from ".table('members')." WHERE uid IN (".$sqlin.")")) return false;
		if (!$db->query("Delete from ".table('members_info')." WHERE uid IN (".$sqlin.")")) return false;
		if (!$db->query("Delete from ".table('members_log')." WHERE log_uid IN (".$sqlin.")")) return false;
		if (!$db->query("Delete from ".table('members_points')." WHERE uid IN (".$sqlin.")")) return false;
		if (!$db->query("Delete from ".table('order')." WHERE uid IN (".$sqlin.")")) return false;
		if (!$db->query("Delete from ".table('members_hunter_setmeal')." WHERE uid IN (".$sqlin.")")) return false; 
		write_log("ɾ����ԱuidΪ".$sqlin."�Ļ�Ա", $_SESSION['admin_name'],3);
		return true;		
	}
	return false;
}
function set_members_setmeal($uid,$setmealid)
{
	global $db,$timestamp;
	$setmeal=$db->getone("select * from ".table('hunter_setmeal')." WHERE id = ".intval($setmealid)." AND display=1 LIMIT 1");
	if (empty($setmeal)) return false;
	$setsqlarr['effective']=1;
	$setsqlarr['setmeal_id']=$setmeal['id'];
	$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
	$setsqlarr['days']=$setmeal['days'];
	$setsqlarr['starttime']=$timestamp;
		if ($setmeal['days']>0)
		{
		$setsqlarr['endtime']=strtotime("".$setmeal['days']." days");
		}
		else
		{
		$setsqlarr['endtime']="0";	
		}
	$setsqlarr['expense']=$setmeal['expense'];
	$setsqlarr['jobs_add']=$setmeal['jobs_add'];
	$setsqlarr['download_resume_senior']=$setmeal['download_resume_senior'];
	$setsqlarr['download_resume_ordinary']=$setmeal['download_resume_ordinary'];
	$setsqlarr['interview_senior']=$setmeal['interview_senior'];
	$setsqlarr['interview_ordinary']=$setmeal['interview_ordinary'];
	$setsqlarr['added']=$setmeal['added'];
	$setsqlarr['hunter_refresh_jobs_space']=$setmeal['hunter_refresh_jobs_space'];
	$setsqlarr['hunter_refresh_jobs_time']=$setmeal['hunter_refresh_jobs_time'];
	$setmeal_jobs['setmeal_deadline']=$setsqlarr['endtime'];
	$setmeal_jobs['setmeal_id']=$setsqlarr['setmeal_id'];
	$setmeal_jobs['setmeal_name']=$setsqlarr['setmeal_name'];
	if (!$db->updatetable(table('members_hunter_setmeal'),$setsqlarr," uid='{$uid}'")) return false;
	if (!$db->updatetable(table('hunter_jobs'),$setmeal_jobs," uid=".intval($uid)." AND add_mode='2' ")) return false;
	return true;
}
function get_hunter_one_uid($uid)
{
	global $db;
	$uid=intval($uid);
	$sql = "select * from ".table('hunter_profile')." where uid={$uid}";
	$val=$db->getone($sql);
	return $val;
}
function get_user_points($uid)
{
	global $db;
	$sql = "select * from ".table('members_points')." where uid = ".intval($uid)."  LIMIT 1 ";
	$points=$db->getone($sql);
	return $points['points'];
}
function get_user_setmeal($uid)
{
	global $db;
	$sql = "select * from ".table('members_hunter_setmeal')."  WHERE uid=".intval($uid)." AND  effective=1 LIMIT 1";
	return $db->getone($sql);
}
function get_user($uid)
{
	global $db;
	$sql = "select * from ".table('members')." where uid=".intval($uid)." LIMIT 1";
	return $db->getone($sql);
}
function edit_setmeal_notes($setarr,$setmeal){
	$diff_arr= array_diff_assoc($setarr,$setmeal);
	if($diff_arr){
		foreach($diff_arr as $key=>$value){
			if($key=='jobs_add'){
				$str.="����ְλ��{$setmeal['jobs_add']}-{$setarr['jobs_add']}";
			}elseif($key=='download_resume_manager'){
				$str.=",���ؾ����˲ż�����{$setmeal['download_resume_manager']}-{$setarr['download_resume_manager']}";
			}elseif($key=='interview_manager'){
				$str.=",���뾭���˲���������{$setmeal['interview_manager']}-{$setarr['interview_manager']}";
			}elseif($key=='endtime'){
				if($setarr['endtime']=='1970-01-01') $setarr['endtime']='������';
				$str.=",�޸��ײ͵���ʱ�䣺{$setmeal['endtime']}~{$setarr['endtime']}";
			}elseif($key=='log_amount' && $value){
				$str.=",��ȡ�ײͽ�{$value} Ԫ";
			}
		}
		$strend=$str?"�����ˣ�{$_SESSION['admin_name']}��˵����".$str:'';
		return $strend;
	}else{
		return '';
	}
}
function get_meal_members_log($offset,$perpage,$get_sql= '',$mode='1')
{
	global $db;
	$colum=$mode==1?'b.points':'b.setmeal_name';
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT a.*,{$colum},c.huntername FROM ".table('members_charge_log')." as a ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['log_value_']=$row['log_value'];
	$row['log_value']=cut_str($row['log_value'],20 ,0,"...");
	$row_arr[] = $row;
	}
	return $row_arr;
}
function del_meal_log($id)
{
	global $db;
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
	if (!$db->query("Delete from ".table('members_charge_log')." WHERE log_id IN ({$sqlin})")) return false;
	$num=$db->affected_rows();
	write_log("ɾ���ײͼ�¼idΪ".$sqlin."���ײͼ�¼,��ɾ��".$num."��", $_SESSION['admin_name'],3);
	return $db->affected_rows();
}
function get_meal_members_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT a.*,b.*,c.huntername FROM ".table('members_hunter_setmeal')." as a ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row_arr[] = $row;
	}
	return $row_arr;
}
 function delay_meal($id,$days)
{
	global $db;
	$days=intval($days);
	$return=0;
	if (empty($days)) return false;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$time=time();
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$result = $db->query("SELECT id,uid,endtime FROM ".table('members_hunter_setmeal')." WHERE uid IN ({$sqlin})");
		while($row = $db->fetch_array($result))
		{
			if($row['endtime']=="0")
			{
			continue;
			}
			else
			{
				if ($row['endtime']>$time)
				{
				$deadline=strtotime("{$days} day",$row['endtime']);
				}
				else
				{
				$deadline=strtotime("{$days} day");
				}
				if (!$db->query("update  ".table('members_hunter_setmeal')." SET endtime='{$deadline}'  WHERE id='{$row['id']}'  LIMIT 1")) return false;
				$return=$return+$db->affected_rows();
				if (!$db->query("update  ".table('hunter_jobs')." SET setmeal_deadline='{$deadline}'  WHERE uid='{$row['uid']}'  LIMIT 1")) return false;
 			}
		}
	}
	write_log("���ڻ�ԱuidΪ".$sqlin."�Ļ�Ա�ײ�,��caoz".$return."��", $_SESSION['admin_name'],3);
	return $return;
	
}
function get_order_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT o.*,m.username,m.email,c.huntername FROM ".table('order')." as o ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['payment_name']=get_payment_info($row['payment_name'],true);
	$row_arr[] = $row;
	}
	return $row_arr;
}
function get_payment_info($typename,$name=false)
{
	global $db;
	$sql = "select * from ".table('payment')." where typename ='".$typename."'";
	$val=$db->getone($sql);
	if ($name)
	{
	return $val['byname'];
	}
	else
	{
	return $val;
	}
}
function get_order_one($id=0)
{
	global $db;
	$sql = "select * from ".table('order')." where id=".intval($id)." LIMIT 1";
	$val=$db->getone($sql);
	$val['payment_name']=get_payment_info($val['payment_name'],true);
	$val['payment_username']=get_user($val['uid']);
	return $val;
}
function del_order($id)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('order')." WHERE id IN (".$sqlin.")  AND is_paid=1 ")) return false;		
		return true;
		write_log("ɾ������idΪ".$sqlin."�Ķ���", $_SESSION['admin_name'],3);
	}
	return false;
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
 function order_paid($v_oid)
{
	global $db,$timestamp,$_CFG;
	$order=$db->getone("select * from ".table('order')." WHERE oid ='{$v_oid}' AND is_paid= '1' LIMIT 1 ");
	if ($order)
	{
		$user=get_user($order['uid']);
		$sql = "UPDATE ".table('order')." SET is_paid= '2',payment_time='{$timestamp}' WHERE oid='{$v_oid}' LIMIT 1 ";
			if (!$db->query($sql)) return false;
			if($order['amount']=='0.00'){
				$ismoney=1;
			}else{
				$ismoney=2;
			}
			if ($order['points']>0)
			{
					report_deal($order['uid'],1,$order['points']);				
					$user_points=get_user_points($order['uid']);
					$notes="�����ˣ�{$_SESSION['admin_name']},˵����ȷ���տ�տ��{$order['amount']} ��".date('Y-m-d H:i',time())."ͨ����".get_payment_info($order['payment_name'],true)." �ɹ���ֵ ".$order['amount']."Ԫ��(+{$order['points']})��(ʣ��:{$user_points}),����:{$v_oid}";					
					write_memberslog($order['uid'],3,9201,$user['username'],$notes);
					//��Ա�ײͱ����¼������Ա��̨���û�Ա��������ɹ���4��ʾ������Ա��̨��ͨ
					write_setmeallog($order['uid'],$user['username'],$notes,4,$order['amount'],$ismoney,1,3);
			}
			if ($order['setmeal']>0)
			{
					set_members_setmeal($order['uid'],$order['setmeal']);
					$setmeal=get_setmeal_one($order['setmeal']);
					$notes="�����ˣ�{$_SESSION['admin_name']},˵����ȷ���տ�տ��{$order['amount']} ��".date('Y-m-d H:i',time())."ͨ����".get_payment_info($order['payment_name'],true)." �ɹ���ֵ ".$order['amount']."Ԫ����ͨ{$setmeal['setmeal_name']}";
					write_memberslog($order['uid'],3,9202,$user['username'],$notes);
					//��Ա�ײͱ����¼������Ա��̨���û�Ա��������ɹ���4��ʾ������Ա��̨��ͨ
					write_setmeallog($order['uid'],$user['username'],$notes,4,$order['amount'],$ismoney,2,3);
			
			}
		//�����ʼ�
		$mailconfig=get_cache('mailconfig');
		if ($mailconfig['set_payment']=="1" && $user['email_audit']=="1")
		{
		dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$order['uid']."&key=".asyn_userkey($order['uid'])."&act=set_payment");
		}
		//�����ʼ����
		//sms
		$sms=get_cache('sms_config');
		if ($sms['open']=="1" && $sms['set_payment']=="1"  && $user['mobile_audit']=="1")
		{
		dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$order['uid']."&key=".asyn_userkey($order['uid'])."&act=set_payment");
		}
		//sms
		write_log("��������Ϊ".$v_oid."�Ķ�����Ϊȷ���տ�", $_SESSION['admin_name'],3);
		return true;
	}
return true;
}
function recom_hunter_jobs($id,$recommend,$pms_notice,$notice)
{
	global $db;
	$return=0;
	$recommend=intval($recommend);
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$time=time();
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('hunter_jobs')." SET recommend='{$recommend}'  WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		write_log("����ͷְλidΪ".$sqlin."��ְλ�ƹ�״̬����Ϊ".$recommend."������".$return."��", $_SESSION['admin_name'],3);
		//����վ����
		if ($pms_notice=='1')
		{
				$result = $db->query("SELECT jobs_name,uid FROM ".table('hunter_jobs')." WHERE id IN ({$sqlin})");
				$notice=$notice==''?'˵������':'˵����'.$notice;
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					$setsqlarr['message']=$recommend=='1'?"��������ְλ��{$list['jobs_name']},������Ա����Ϊ(�Ƽ�ְλ)��{$notice}":"��������ְλ��{$list['jobs_name']},������Աȡ��(�Ƽ�ְλ)��{$notice}";
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
	return $return;
}

  ?>