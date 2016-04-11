<?php
 /*
 * �������� �����û���غ���
*/
 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
  function get_user_info($uid)
{
	global $db;
	$sql = "select * from ".table('members')." where uid = ".intval($uid)." LIMIT 1";
	return $db->getone($sql);
}
 //���ü����Ƿ�Ͷ�ݹ���ְλ
 function check_jobs_apply($jobs_id,$resume_id,$p_uid)
{
	global $db;
	$sql = "select did from ".table('personal_jobs_apply')." WHERE personal_uid = '".intval($p_uid)."' AND jobs_id='".intval($jobs_id)."'  AND resume_id='".intval($resume_id)."'";
	return $db->getall($sql);
}
 //��ȡְλ��Ϣ�б�
function get_jobs($offset,$perpage,$get_sql= '')
{
	global $db,$timestamp;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query($get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['jobs_name']=cut_str($row['jobs_name'],12,0,"...");
	if (!empty($row['highlight']))
	{
	$row['jobs_name']="<span style=\"color:{$row['highlight']}\">{$row['jobs_name']}</span>";
	}
	$row['companyname']=cut_str($row['companyname'],18,0,"...");
	$row['company_url']=url_rewrite('QS_companyshow',array('id'=>$row['company_id']));
	$row['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$row['id']),1,$row['subsite_id']);
	$get_resume_nolook = $db->getone("select count(*) from ".table('personal_jobs_apply')." where personal_look=1 and jobs_id=".$row['id']);
	$get_resume_all = $db->getone("select count(*) from ".table('personal_jobs_apply')." where jobs_id=".$row['id']);
	$row['get_resume'] = "( ".$get_resume_nolook['count(*)']." / ".$get_resume_all['count(*)']." )";
	$row_arr[] = $row;
	}
	return $row_arr;
}
 //******************************��������**********************************
function get_resume_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query($get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['resume_url']=url_rewrite('QS_resumeshow',array('id'=>$row['id']),1,$row['subsite_id']);
	$row_arr[] = $row;
	}
	return $row_arr;
}
function del_resume($id)
{
	global $db;
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("Delete from ".table('resume')." WHERE id IN ({$sqlin})")) return false;
	$return=$return+$db->affected_rows();
	if (!$db->query("Delete from ".table('resume_jobs')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_trade')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_tag')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_education')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_training')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_work')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_search_rtime')." WHERE id IN ({$sqlin})")) return false;
	if (!$db->query("Delete from ".table('resume_search_key')." WHERE id IN ({$sqlin})")) return false;
	if (!$db->query("Delete from ".table('view_resume')." WHERE resumeid IN ({$sqlin})")) return false;
	$db->query("delete from ".table('resume_entrust')." where id IN (".$sqlin.")");
	//��д����Ա��־
	write_log("ɾ������idΪ".$id."�ļ��� , ��ɾ��".$return."��", $_SESSION['admin_name'],3);
	return $return;
	}
	return $return;
}
function del_resume_for_uid($uid)
{
	global $db;
	if (!is_array($uid)) $uid=array($uid);
	$sqlin=implode(",",$uid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$result = $db->query("SELECT id FROM ".table('resume')." WHERE uid IN (".$sqlin.")");
		while($row = $db->fetch_array($result))
		{
		$rid[]=$row['id'];
		}
		if (empty($rid))
		{
		return true;
		}
		else
		{
		return del_resume($rid);
		}		
	}
}
function edit_resume_audit($id,$audit,$reason,$pms_notice)
{
	global $db,$_CFG;
	$audit=intval($audit);
	if (!is_array($id))  $id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('resume')." SET audit='{$audit}'  WHERE id IN ({$sqlin}) ")) return false;
		if (!$db->query("update  ".table('resume_search_key')." SET audit='{$audit}'  WHERE id IN ({$sqlin}) ")) return false;
		if (!$db->query("update  ".table('resume_search_rtime')." SET audit='{$audit}'  WHERE id IN ({$sqlin}) ")) return false;
		foreach ($id as $key => $value) {
			set_resume_entrust($value);
		}
		// distribution_resume($id);
		//��д����Ա��־
		write_log("�޸ļ���idΪ".$sqlin."�����״̬Ϊ".$audit, $_SESSION['admin_name'],3);
		//����վ����
		if ($pms_notice=='1')
		{
				$result = $db->query("SELECT  fullname,title,uid  FROM ".table('resume')." WHERE id IN ({$sqlin})");
				$reason=$reason==''?'ԭ��δ֪':'ԭ��'.$reason;
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					$setsqlarr['message']=$audit=='1'?"�������ļ�����{$list['title']},��ʵ������{$list['fullname']},�ɹ�ͨ����վ����Ա��ˣ�":"�������ļ�����{$list['title']},��ʵ������{$list['fullname']},δͨ����վ����Ա���,{$reason}";
					$setsqlarr['msgtype']=1;
					$setsqlarr['msgtouid']=$user_info['uid'];
					$setsqlarr['msgtoname']=$user_info['username'];
					$setsqlarr['dateline']=time();
					$setsqlarr['replytime']=time();
					$setsqlarr['new']=1;
					$db->inserttable(table('pms'),$setsqlarr);
				 }
		}
		//���δͨ������ԭ��
		if($audit=='3'){
			foreach($id as $list){
				$auditsqlarr['resume_id']=$list;
				$auditsqlarr['reason']=$reason;
				$auditsqlarr['addtime']=time();
				$db->inserttable(table('audit_reason'),$auditsqlarr);
			}
		}
			
			//�����ʼ�
				$mailconfig=get_cache('mailconfig');//��ȡ�ʼ�����
				$sms=get_cache('sms_config');
				if ($audit=="1" && $mailconfig['set_resumeallow']=="1")//���ͨ��
				{
						$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin}) ");
						while($list = $db->fetch_array($result))
						{
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&act=set_resumeallow");
						}
				}
				if ($audit=="3" && $mailconfig['set_resumenotallow']=="1")//���δͨ��
				{
					$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin}) ");
						while($list = $db->fetch_array($result))
						{
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&act=set_resumenotallow");
						}
				}
				//sms		
				if ($audit=="1" && $sms['open']=="1" && $sms['set_resumeallow']=="1" )
				{
					$mobilearray = array();
					$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin}) ");
					while($list = $db->fetch_array($result))
					{
						$user_info=get_user($list['uid']);
						if ($user_info['mobile_audit']=="1" && !is_array($list['uid'],$mobilearray))
						{
							//��ͬһ��Ա�ŵ������� ����ȷ���û�ԱΨһ��
							$mobilearray[] = $list['uid'];
						}
					}
					foreach ($mobilearray as $key => $value) 
					{
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$value."&key=".asyn_userkey($value)."&act=set_resumeallow");
					}
				}
				//sms
				if ($audit=="3" && $sms['open']=="1" && $sms['set_resumenotallow']=="1" )//��֤δͨ��
				{
					$mobilearray = array();
					$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin}) ");
					while($list = $db->fetch_array($result))
					{
						$user_info=get_user($list['uid']);
						if ($user_info['mobile_audit']=="1" && !is_array($list['uid'],$mobilearray))
						{
							//��ͬһ��Ա�ŵ������� ����ȷ���û�ԱΨһ��
							$mobilearray[] = $list['uid'];
						}
					}
					foreach ($mobilearray as $key => $value) 
					{
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$value."&key=".asyn_userkey($value)."&act=set_resumenotallow");
					}
				}
				//΢��֪ͨ
				$weixinconfig=get_cache('weixin_config');
				if($audit=="1")
				{
					$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin}) ");
					while($list = $db->fetch_array($result))
					{
						set_resumeallow($list['uid'],$weixinconfig['set_resumeallow'],$list['title'],"ͨ��",$reason);
					}
				}
				if($audit=="3")
				{
					$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin}) ");
					while($list = $db->fetch_array($result))
					{
						set_resumeallow($list['uid'],$weixinconfig['set_resumenotallow'],$list['title'],"δͨ��",$reason);
					}
				}
	return true;
	}
	return false;
}
//�޸���Ƭ���״̬
function edit_resume_photoaudit($id,$audit,$is_del_img)
{
	global $db,$_CFG;
	$audit=intval($audit);
	$is_del_img=intval($is_del_img);
	if (!is_array($id)) $id=array($id);
	if (!empty($id))
	{
		foreach($id as $i)
		{
			$i=intval($i);
			$tb1=$db->getone("select photo_img,photo_audit,photo_display from ".table('resume')." WHERE id='{$i}' LIMIT  1");
			if (!empty($tb1))
			{
				if($is_del_img==1 && $audit==3)
				{
					$photo=0;
					@unlink(QISHI_ROOT_PATH.'data/photo/'.$tb1['photo_img']);
					@unlink(QISHI_ROOT_PATH.'data/photo/thumb/'.$tb1['photo_img']);
					$db->query("update  ".table('resume')." SET photo_img='',photo_audit='{$audit}',photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");
					$db->query("update  ".table('resume_search_rtime')." SET photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");
					$db->query("update  ".table('resume_search_key')." SET photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");
				}
				else
				{
					if ($tb1['photo_img'] && $audit=="1" && $tb1['photo_display']=="1")
					{
					$photo=1;
					}
					else
					{
					$photo=0;
					}	
					$db->query("update  ".table('resume')." SET photo_audit='{$audit}',photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");
					$db->query("update  ".table('resume_search_rtime')." SET photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");
					$db->query("update  ".table('resume_search_key')." SET photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");
				}
			}
		}
		//��д����Ա��־
		write_log("�޸ļ���idΪ".$id."����Ƭ���״̬Ϊ".$audit, $_SESSION['admin_name'],3);
	}
	return true;
}
//�޸��˲ŵȼ�
function edit_resume_talent($id,$talent)
{
	global $db;
	$talent=intval($talent);
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('resume')." SET talent={$talent}  WHERE id IN ({$sqlin})")) return false;
		if (!$db->query("update  ".table('resume_search_rtime')." SET talent={$talent}  WHERE id IN ({$sqlin})")) return false;
		if (!$db->query("update  ".table('resume_search_key')." SET talent={$talent}  WHERE id IN ({$sqlin})")) return false;
		//��д����Ա��־
		write_log("�޸ļ���idΪ".$sqlin."���˲ŵȼ�Ϊ".$talent, $_SESSION['admin_name'],3);
		return true;
	}
	return false;
}
//��UID��ȡ���м���
function get_resume_uid($uid)
{
	global $db;
	$uid=intval($uid);
	$result = $db->query("select * FROM ".table('resume')." where uid='{$uid}'");
	while($row = $db->fetch_array($result))
	{ 
	$row['resume_url']=url_rewrite('QS_resumeshow',array('id'=>$row['id']),1,$row['subsite_id']);
	$row_arr[] = $row;
	}
	return $row_arr;	
}
function refresh_resume($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('resume')." SET refreshtime='".time()."'  WHERE id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
		if (!$db->query("update  ".table('resume_search_rtime')." SET refreshtime='".time()."'  WHERE id IN (".$sqlin.")")) return false;
		if (!$db->query("update  ".table('resume_search_key')." SET refreshtime='".time()."'  WHERE id IN (".$sqlin.")")) return false;
	}
	//��д����Ա��־
	write_log("ˢ�¼���idΪ".$sqlin."�ļ��� , ��ˢ��".$return."��", $_SESSION['admin_name'],3);
	return $return;
}
//**************************���˻�Ա�б�
function get_member_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;	
	$result = $db->query("SELECT * FROM ".table('members')." as m ".$get_sql.$limit);
		while($row = $db->fetch_array($result))
		{
			$address = $db->getone("select log_address,log_id,log_uid from ".table("members_log")." where log_type = '1000' and log_uid = ".$row['uid']." order by log_id asc limit 1");
			$row['ipAddress'] = $address['log_address'];
			$row_arr[] = $row;
		}
	return $row_arr;
}
function delete_member($uid)
{
	global $db;
	if (!is_array($uid)) $uid=array($uid);
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
		//��д����Ա��־
		write_log("ɾ��uidΪ".$sqlin."�Ļ�Ա", $_SESSION['admin_name'],3);
		return true;
		}
	return false;
}
function get_member_one($memberuid)
{
	global $db;
	$sql = "select * from ".table('members')." where uid=".intval($memberuid)." LIMIT 1";
	$val=$db->getone($sql);
	return $val;
}
function get_user($uid)
{
	global $db;
	$uid=intval($uid);
	$sql = "select * from ".table('members')." where uid = '{$uid}' LIMIT 1";
	return $db->getone($sql);
}
//��ȡ�����������־
function get_resumeaudit_one($resume_id){
	global $db;
	$sql = "select * from ".table('audit_reason')."  WHERE resume_id='".intval($resume_id)."' ORDER BY id DESC";
	return $db->getall($sql);
}
//��ȡ����������Ϣ
function get_resume_basic($uid,$id)
{
	global $db;
	$id=intval($id);
	$uid=intval($uid);
	$info=$db->getone("select * from ".table('resume')." where id='{$id}'  AND uid='{$uid}' LIMIT 1 ");
	
	if (empty($info))
	{
	return false;
	}
	else
	{
	$info['age']=date("Y")-$info['birthdate'];
	$info['number']="N".str_pad($info['id'],7,"0",STR_PAD_LEFT);
	$info['lastname']=$info['fullname'];
	return $info;
	}
}
//��ȡ���������б�
function get_resume_education($uid,$pid)
{
	global $db;
	if (intval($uid)!=$uid) return false;
	$sql = "SELECT * FROM ".table('resume_education')." WHERE  pid='".intval($pid)."' AND uid='".intval($uid)."' ";
	return $db->getall($sql);
}
//��ȡ����������
function get_resume_work($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_work')." where pid='".$pid."' AND uid=".intval($uid)."" ;
	return $db->getall($sql);
}
//��ȡ����ѵ�����б�
function get_resume_training($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_training')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
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
function reasonaudit_del($id)
{
	global $db;
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
	if (!$db->query("Delete from ".table('audit_reason')." WHERE id IN ({$sqlin})")) return false;
	//��д����Ա��־
	write_log("��̨ɾ����־idΪ".$sqlin."����־", $_SESSION['admin_name'],3);
	return $db->affected_rows();
}
function export_resume($yid){
	global $db;
	$yid_str = implode(",", $yid);
	$oederbysql=" order BY refreshtime desc ";
	$wheresql = empty($wheresql)?" id in ({$yid_str}) ":" and id in ({$yid_str}) ";
	if (!empty($wheresql))
	{
	$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
	}
	$data = $db->getall("select * from ".table('resume').$wheresql);
	
	if(!empty($data)){
		$result = $data;
	}
	if(!empty($result)){
		foreach ($result as $key => $value) {
			$arr[$key]['num'] = $key;
			$arr[$key]['title'] = $value['title'];
			$arr[$key]['fullname'] = $value['fullname'];
			$arr[$key]['sex_cn'] = $value['sex_cn'];
			$arr[$key]['birthdate'] = $value['birthdate'];
			$arr[$key]['height'] = $value['height'];
			$arr[$key]['householdaddress'] = $value['householdaddress'];
			$arr[$key]['marriage_cn'] = $value['marriage_cn'];
			$arr[$key]['experience_cn'] = $value['experience_cn'];
			$arr[$key]['education_cn'] = $value['education_cn'];
			$arr[$key]['natrue_cn'] = $value['natrue_cn'];
			$arr[$key]['trade_cn'] = $value['trade_cn'];
			$arr[$key]['district_cn'] = $value['district_cn'];
			$arr[$key]['wage_cn'] = $value['wage_cn'];
			$arr[$key]['tag']=$value['tag_cn'];
			/*$arr[$key]['tag']=preg_replace("/\d+/", '',$value['tag']);
			$arr[$key]['tag']=preg_replace('/\,/','',$arr[$key]['tag']);
			$arr[$key]['tag']=preg_replace('/\|/','&nbsp;&nbsp;&nbsp;',$arr[$key]['tag']);*/
			$arr[$key]['school'] = "";
			$school = $db->getall("select * from ".table('resume_education')." where pid=".$value['id']." order by id desc");
			if(!empty($school)){
				foreach ($school as $key1 => $value1) {
					if(intval($value1['todate']) == 1)
					{
						$endtime = "����";
					}
					else
					{
						$endtime = $value1['endyear']."��".$value1['endmonth']."��";
					}
					$arr[$key]['school'] .= $value1['startyear']."��".$value1['startmonth']."��"."-".$endtime."�Ͷ���".$value1['school'].",��ѧרҵ��".$value1['speciality'].",ѧ����".$value1['education_cn'].";&nbsp;";
				}
			}
			$arr[$key]['work'] = "";
			$work = $db->getall("select * from ".table('resume_work')." where pid=".$value['id']." order by id desc");
			if(!empty($work)){
				foreach ($work as $key1 => $value1) {
					if(intval($value1['todate']) == 1)
					{
						$endtime = "����";
					}
					else
					{
						$endtime = $value1['endyear']."��".$value1['endmonth']."��";
					}
					$arr[$key]['work'] .= $value1['startyear']."��".$value1['startmonth']."��"."-".$endtime."��ְ��".$value1['companyname'].",��ְ��".$value1['jobs'].";&nbsp;";
				}
			}
			$arr[$key]['train'] = "";
			$train = $db->getall("select * from ".table('resume_training')." where pid=".$value['id']." order by id desc");
			if(!empty($train)){
				foreach ($train as $key1 => $value1) {
					if(intval($value1['todate']) == 1)
					{
						$endtime = "����";
					}
					else
					{
						$endtime = $value1['endyear']."��".$value1['endmonth']."��";
					}
					$arr[$key]['train'] .= $value1['startyear']."��".$value1['startmonth']."��"."-".$endtime."��".$value1['agency']."��ѵ".$value1['course']."�γ�;&nbsp;";
				}
			}
			$arr[$key]['telephone'] = $value['telephone'];
			$arr[$key]['email'] = $value['email'];
			$arr[$key]['qq'] = $value['qq'];
			$arr[$key]['address'] = $value['address'];
			$arr[$key]['website'] = $value['website'];
			$arr[$key]['recentjobs'] = $value['recentjobs'];
			$arr[$key]['intention_jobs'] = $value['intention_jobs'];
			$arr[$key]['specialty'] = str_replace("\n","",str_replace("\r", "", $value['specialty']));
			$arr[$key]['addtime'] = date("Y-m-d",$value['addtime']);
			$arr[$key]['refreshtime'] = date("Y-m-d",$value['refreshtime']);
			$arr[$key]['talent'] = $value['talent']==1?"��ͨ":"�߼�";
			$arr[$key]['complete_percent'] = $value['complete_percent'];
		}
		$top_str = "���\t��������\t����\t�Ա�\t��������\t���\t�������ڵ�\t����״��\t��������\tѧ��\t����ְλ����\t������ҵ\t����������\t����н��\t��ǩ\t��������\t��������\t��ѵ����\t�ֻ�\t����\tQQ\t��ַ\t������ҳ\t������¹���\t����ְλ\t�����س�\t���ʱ��\tˢ��ʱ��\t�����ȼ�\t������\t\n";
		create_excel($top_str,$arr);
		//��д����Ա��־
		write_log("��������idΪ".$yid_str."�ļ���", $_SESSION['admin_name'],3);
		return true;
	}else{
		return false;
	}
	
}
function set_resume_entrust($resume_id){
	global $db;
	$resume = $db->getone("select audit,uid,fullname,addtime,entrust from ".table('resume')." where id=".$resume_id);
	if($resume["audit"]=="1" && $resume["entrust"]=="1"){
		$has = $db->getone("select 1 from ".table('resume_entrust')." where id=".$resume_id);
		if(!$has){
			$setsqlarr['id'] = $resume_id;
			$setsqlarr['uid'] = $resume['uid'];
			$setsqlarr['fullname'] = $resume['fullname'];
			$setsqlarr['resume_addtime'] = $resume['addtime'];
			$db->inserttable(table('resume_entrust'),$setsqlarr);
			$db->updatetable(table('resume'),array("entrust"=>"0")," id=".$resume_id." ");
		}
	}
	else
	{
		$db->query("delete from ".table('resume_entrust')." where id=".$resume_id);
	}
	return true;
}
//�޸��û�״̬
function set_user_status($status,$uid)
{
	global $db;
	$status=intval($status);
	$uid=intval($uid);
	if (!$db->query("UPDATE ".table('members')." SET status= {$status} WHERE uid={$uid} LIMIT 1")) return false;
	//��д����Ա��־
	write_log("��̨��uidΪ".$uid."��Ա���û�״̬�޸�Ϊ".$status, $_SESSION['admin_name'],3);
	// if (!$db->query("UPDATE ".table('company_profile')." SET user_status= {$status} WHERE uid={$uid} ")) return false;
	// if (!$db->query("UPDATE ".table('jobs')." SET user_status= {$status} WHERE uid={$uid} ")) return false;
	// if (!$db->query("UPDATE ".table('jobs_tmp')." SET user_status= {$status} WHERE uid={$uid} ")) return false; 
	return true;
}
//�������ʱ��ע���Ա
function import_user_register($username,$password,$member_type=0,$email,$mobile,$uc_reg=true)
{
	global $db,$timestamp,$_CFG,$online_ip,$QS_pwdhash;
	$member_type=intval($member_type);
	$ck_username=get_user_inusername_import($username);
	$ck_email=get_user_inemail_import($email);
	$ck_mobile=get_user_inmobile_import($mobile);
	if ($member_type==0) 
	{
	return -1;
	}
	elseif (!empty($ck_username))
	{
	return $ck_username['uid'];
	}
	elseif ($email!=""&&!empty($ck_email))
	{
	return $ck_email['uid'];
	}
	elseif ($mobile!=""&&!empty($ck_mobile))
	{
	return $ck_mobile['uid'];
	}
	$pwd_hash=randstr_import();
	$password_hash=md5(md5($password).$pwd_hash.$QS_pwdhash);
	$setsqlarr['username']=$username;
	$setsqlarr['password']=$password_hash;
	$setsqlarr['pwd_hash']=$pwd_hash;
	$setsqlarr['email']=$email;
	$setsqlarr['mobile']=$mobile;
	$setsqlarr['utype']=intval($member_type);
	$setsqlarr['reg_time']=$timestamp;
	$setsqlarr['reg_ip']=$online_ip;
	$insert_id=$db->inserttable(table('members'),$setsqlarr,true);
	if(defined('UC_API') && $uc_reg)
	{
		include_once(QISHI_ROOT_PATH.'uc_client/client.php');
		$uc_reg_uid=uc_user_register($username,$password,$email);
	}
	return $insert_id;
}
function get_user_inemail_import($email)
{
	global $db;
	return $db->getone("select * from ".table('members')." where email = '{$email}' LIMIT 1");
}
function get_user_inusername_import($username)
{
	global $db;
	$sql = "select * from ".table('members')." where username = '{$username}' LIMIT 1";
	return $db->getone($sql);
}
function get_user_inid($uid)
{
	global $db;
	$uid=intval($uid);
	$sql = "select * from ".table('members')." where uid = '{$uid}' LIMIT 1";
	return $db->getone($sql);
}
function get_user_inmobile_import($mobile)
{
	global $db;
	$sql = "select * from ".table('members')." where mobile = '{$mobile}' LIMIT 1";
	return $db->getone($sql);
}
//��ȡ����ַ���
function randstr_import($length=6)
{
$hash='';
$chars= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz@#!~?:-='; 
$max=strlen($chars)-1;   
mt_srand((double)microtime()*1000000);   
for($i=0;$i<$length;$i++)   {   
$hash.=$chars[mt_rand(0,$max)];   
}   
return $hash;   
}
//ƥ��Ҫ��ѧ��
function resume_education($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select c_id,c_name from ".table('category')." where c_alias='QS_education'  and c_id=".intval($locoyspider['jobs_education'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')."  where c_alias='QS_education'";
		$info=$db->getall($sql);
		$return=resume_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return $default;
		}
	}
}
//ƥ��Ҫ��������
function resume_experience($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select c_id,c_name from ".table('category')." where c_alias='QS_experience'  and c_id=".intval($locoyspider['jobs_experience'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')."  where c_alias='QS_experience'";
		$info=$db->getall($sql);
		$return=resume_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return $default;
		}
	}
}
//ƥ�����������ҵ
function resume_trade($str=NULL)
{	
	global $db,$locoyspider;
	$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_trade' AND  c_id=".intval($locoyspider['company_trade'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_trade'";
		$info=$db->getall($sql);
		$return=resume_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return $default;
		}
	}
}
//�޸ĺ��ƥ��н�ʴ���
function resume_wage($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select  c_id,c_name from ".table('category')." where  c_alias='QS_wage' and c_id=".intval($locoyspider['jobs_wage'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$str = trim($str);
		$wage_array = explode('~', $str);
		$wage_min = intval($wage_array[0]);
		if($wage_min>=10000){
			return array('id'=>61,'cn'=>'һ������/��');
		}elseif($wage_min>=5000){
			return array('id'=>60,'cn'=>'5000~10000Ԫ/��');
		}elseif($wage_min>=3000){
			return array('id'=>59,'cn'=>'3000~5000Ԫ/��');
		}elseif($wage_min>=2000){
			return array('id'=>58,'cn'=>'2000~3000Ԫ/��');
		}elseif($wage_min>=1500){
			return array('id'=>57,'cn'=>'1500~2000Ԫ/��');
		}elseif($wage_min>=1000){
			return array('id'=>56,'cn'=>'1000~1500Ԫ/��');
		}else{
			return array('id'=>$info['c_id'],'cn'=>$info['c_name']);
		}
	}
}
//ƥ��Ҫ��ѧ��
function resume_work_education($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select c_id,c_name from ".table('category')." where c_alias='QS_education'  and c_id=".intval($locoyspider['jobs_education'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')."  where c_alias='QS_education'";
		$info=$db->getall($sql);
		$return=resume_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return $default;
		}
	}
}
//ģ������
function resume_search_str($arr,$str,$arrinname)
{
	global $locoyspider;

	foreach ($arr as $key =>$list)
	{
		similar_text($list[$arrinname],$str,$percent);
		$od[$percent]=$key;
	}
	krsort($od);
	foreach ($od as $key =>$li)
	{
		if ($key>=$locoyspider['search_threshold'])
		{
		return $arr[$li];
		}
		else
		{
		return false;
		}
	}	
}
//��ȡ��«�ҹ���
function get_points_rule()
{
	global $db;
	$sql = "select * from ".table('members_points_rule')." WHERE utype='2' order BY operation asc,value asc";
	$list=$db->getall($sql);
	return $list;
}
//��ȡ��Ա��Ϣ�������û����������Ϣ
function get_user_points($uid)
{
	global $db;
	$sql = "select * from ".table('members_points')." where uid = ".intval($uid)."  LIMIT 1 ";
	$points=$db->getone($sql);
	return $points['points'];
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
//��ȡ��ֵ֧����ʽ����
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
function get_order_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT o.*,m.username,m.email,c.realname FROM ".table('order')." as o ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
		if($row['payment_name'] == 'points'){
			$row['payment_name']='��«��';
		}else{
			$row['payment_name']=get_payment_info($row['payment_name'],true);
		} 
		$row_arr[] = $row;
	}
	return $row_arr;
}
//��ȡ����
function get_order_one($id=0)
{
	global $db;
	$sql = "select * from ".table('order')." where id=".intval($id)." LIMIT 1";
	$val=$db->getone($sql);
	$val['payment_name']=get_payment_info($val['payment_name'],true);
	$val['payment_username']=get_user($val['uid']);
	return $val;
}
//�����ͨ
function order_paid($v_oid)
{
	global $db,$timestamp,$_CFG;
	$order=$db->getone("select * from ".table('order')." WHERE oid ='{$v_oid}' AND is_paid= '1' LIMIT 1 ");
	if($order['pay_type'] == '1'  || $order['pay_type'] == '4')			//�ײͺ�«��֧��
	{
		$order_name = "�ײͺ�«�Ҷ���";
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
				write_memberslog($order['uid'],1,9001,$user['username'],$notes);
				//��Ա�ײͱ����¼������Ա��̨���û�Ա��������ɹ���4��ʾ������Ա��̨��ͨ
				write_setmeallog($order['uid'],$user['username'],$notes,4,$order['amount'],$ismoney,1,1);
		}
		if ($order['setmeal']>0)
		{
				set_members_setmeal($order['uid'],$order['setmeal']);
				$setmeal=get_setmeal_one($order['setmeal']);
				$notes="�����ˣ�{$_SESSION['admin_name']},˵����ȷ���տ�տ��{$order['amount']} ��".date('Y-m-d H:i',time())."ͨ����".get_payment_info($order['payment_name'],true)." �ɹ���ֵ ".$order['amount']."Ԫ����ͨ{$setmeal['setmeal_name']}";
				write_memberslog($order['uid'],1,9002,$user['username'],$notes);
				//��Ա�ײͱ����¼������Ա��̨���û�Ա��������ɹ���4��ʾ������Ա��̨��ͨ
				write_setmeallog($order['uid'],$user['username'],$notes,4,$order['amount'],$ismoney,2,1);
		
		}
	}
	elseif($order['pay_type'] == '2')		//���λ֧��
	{	
		$order_name = "���λ����"; 
		$sql = "UPDATE ".table('order')." SET is_paid= '2',payment_time='{$timestamp}' WHERE oid='{$v_oid}' LIMIT 1 ";	//is_paid =2 Ϊȷ��֧��
		if (!$db->query($sql)) return false; 
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"������λ��<strong>{$order['description']}</strong>��(���ѣ� {$order['amount']})��",1,1020,"������λ","-{$order['amount']}","{$user_points}"); 
	}
	elseif($order['pay_type'] == '3')		//����֧��
	{	
		$order_name = "���Ŷ���"; 
		$user=get_user($order['uid']);
		$sql = "UPDATE ".table('order')." SET is_paid= '2',payment_time='{$timestamp}' WHERE oid='{$v_oid}' LIMIT 1 ";
		if (!$db->query($sql)) return false;
		if($order['setmeal'] > 0){	//�鿴�����ײ�
			set_members_sms($order['uid'],intval($order['setmeal']));	//֧���ɹ������û����Ӷ�������
			$user_points = get_user_setmeal($order['uid']);
			write_memberslog($_SESSION['uid'],1,9003,$_SESSION['username'],"���ų�ֵ�ײͣ�<strong>{$order['description']}</strong>��(- {$order['amount']})��(ʣ��:{$user_points['set_sms']})",1,1020,"������λ","- {$order['amount']}","{$user_points['set_sms']}");
		}
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
	//΢��֪ͨ
	set_payment($order['uid'],$order_name,$order['oid'],$order['amount']);
	write_log("��������Ϊ".$v_oid."�Ķ�������Ϊȷ���տ�", $_SESSION['admin_name'],3);
	return true;
}
//ȡ������
function del_order($id)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('order')." WHERE id IN (".$sqlin.")  AND is_paid=1 ")) return false;
		write_log("ȡ������idΪ".$sqlin."�Ķ���", $_SESSION['admin_name'],3);	
		return true;
	}
	return false;
}

?>