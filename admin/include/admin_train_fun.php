<?php
 /*
 * 74cms �������� ��ѵ�û���غ���
*/
 if(!defined('IN_QISHI'))
 {
die('Access Denied!');
 }
 function get_points_rule()
{
	global $db;
	$sql = "select * from ".table('members_points_rule')." WHERE utype='4' order BY id asc";
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
	$sql = "select * from ".table('train_setmeal').$where."  order BY display desc,show_order desc,id asc";
	return $db->getall($sql);
}
 function get_setmeal_one($id)
{
	global $db;
	$sql = "select * from ".table('train_setmeal')."  WHERE id=".intval($id)."";
	return $db->getone($sql);
}
 function del_setmeal_one($id)
{
	global $db;
	if (!$db->query("Delete from ".table('train_setmeal')." WHERE id=".intval($id)." ")) return false;
	//��д����Ա��־
	write_log("��̨�ɹ�ɾ���ײ�", $_SESSION['admin_name'],3);
	return true;
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
			if ($gift=='trainauth')
			{
				$com=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$vuid}' AND htype='{$gift}'  LIMIT 1");
				if(empty($com))
				{
				report_deal($vuid,$ptype,$points);
				$user=get_user($vuid);
				$mypoints=get_user_points($vuid);
				write_memberslog($vuid,4,9101,$user['username']," ��Ϊ����֤��ѵ����({$operator}{$points})��(ʣ��:{$mypoints})");
				$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$vuid}', '{$gift}','{$time}')");			
				}
			}			
		}
	 }
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
 function get_user($uid)
{
	global $db;
	$sql = "select * from ".table('members')." where uid=".intval($uid)." LIMIT 1";
	return $db->getone($sql);
}
 function get_user_points($uid)
{
	global $db;
	$sql = "select * from ".table('members_points')." where uid = ".intval($uid)."  LIMIT 1 ";
	$points=$db->getone($sql);
	return $points['points'];
}
 function get_member_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT m.*,t.trainname,t.id,t.addtime FROM ".table('members')." as m ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
		$row['train_url']=url_rewrite('QS_train_agencyshow',array('id'=>$row['id']));
		$address = $db->getone("select log_address,log_id,log_uid from ".table("members_log")." where log_type = '1000' and log_uid = ".$row['uid']." order by log_id asc limit 1");
		$row['ipAddress'] = $address['log_address'];
		$row_arr[] = $row;
	}
	return $row_arr;
}
 function get_train_img($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->getall("SELECT i.*,t.trainname,t.telephone,t.email  FROM ".table('train_img')." AS i ".$get_sql.$limit);
	return $result;
}
 function del_train_img($id)
{
	global $db;
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
	
	$result = $db->query("SELECT * FROM ".table('train_img')." WHERE id IN ({$sqlin})");
	while($list = $db->fetch_array($result))
	{
		@unlink("../data/train_img/original/".$list['img']);//ɾ��ԭͼ
		@unlink("../data/train_img/thumb/".$list['img']);//ɾ������ͼ
	}
	if (!$db->query("Delete from ".table('train_img')." WHERE id IN ({$sqlin})")) return false;
	$num=$db->affected_rows();
	write_log("ɾ��ͼƬidΪ".$sqlin."����ѵͼƬ,��ɾ��".$num."��", $_SESSION['admin_name'],3);
	return $db->affected_rows();
}
 function edit_img_audit($id,$audit,$reason,$pms_notice='1')
{
	global $db;
	$audit=intval($audit);
	$reason=trim($reason);
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('train_img')." SET audit={$audit}  WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		write_log("��ͼƬidΪ".$sqlin."����ѵͼƬ�����״̬����Ϊ".$audit.",������".$return."��", $_SESSION['admin_name'],3);
		if($audit=='1') {$note='�ɹ�ͨ����վ����Ա���!';}elseif($audit=='2'){$note='���������!';}else{$note='δͨ����վ����Ա��ˣ�';}
		$reason=$reason==''?'��':$reason;
		//����վ����
		if ($pms_notice=='1')
		{
				$result = $db->query("SELECT uid,title,img FROM ".table('train_img')." WHERE id IN ({$sqlin}) ");
				while($list = $db->fetch_array($result))
				{
					$list['title']=$list['title']==''?$list['img']:$list['title'];
					$user_info=get_user($list['uid']);
					$setsqlarr['message']="���ϴ���ͼƬ������Ϊ��{$list['title']},".$note." ����˵����".$reason;
					$setsqlarr['msgtype']=1;
					$setsqlarr['msgtouid']=$user_info['uid'];
					$setsqlarr['msgtoname']=$user_info['username'];
					$setsqlarr['dateline']=time();
					$setsqlarr['replytime']=time();
					$setsqlarr['new']=1;
					$db->inserttable(table('pms'),$setsqlarr);
				 }
		}
		return $return;
	}
}
function get_train_news($offset, $perpage, $sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT n.*,t.trainname,t.telephone,t.email  FROM ".table('train_news')." AS n ".$sql.$limit);
	while($row = $db->fetch_array($result)){
	$row['news_url'] = url_rewrite('QS_train_agency_news',array('id'=>$row['id']));
	$row_arr[] = $row;
	}
	return $row_arr;
}
function edit_news_audit($id,$audit,$reason,$pms_notice='1')
{
	global $db;
	$audit=intval($audit);
	$reason=trim($reason);
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('train_news')." SET audit={$audit}  WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		write_log("������idΪ".$sqlin."����ѵ���ŵ����״̬����Ϊ".$audit.",������".$return."��", $_SESSION['admin_name'],3);
		if($audit=='1') {$note='�ɹ�ͨ����վ����Ա���!';}elseif($audit=='2'){$note='���������!';}else{$note='δͨ����վ����Ա��ˣ�';}
	    $reason=$reason==''?'��':$reason;
		//����վ����
		if ($pms_notice=='1')
		{
				$result = $db->query("SELECT uid,title FROM ".table('train_news')." WHERE id IN ({$sqlin}) ");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					$setsqlarr['message']="����ӵ����ţ�����Ϊ��{$list['title']},".$note." ����˵����".$reason;
					$setsqlarr['msgtype']=1;
					$setsqlarr['msgtouid']=$user_info['uid'];
					$setsqlarr['msgtoname']=$user_info['username'];
					$setsqlarr['dateline']=time();
					$setsqlarr['replytime']=time();
					$setsqlarr['new']=1;
					$db->inserttable(table('pms'),$setsqlarr);
				 }
		}
		return $return;
	}
}
function del_train_news($id)
{
	global $db;
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
	if (!$db->query("Delete from ".table('train_news')." WHERE id IN ({$sqlin})")) return false;
	$num=$db->affected_rows();
	write_log("ɾ������idΪ".$sqlin."����ѵ����,��ɾ��".$num."��", $_SESSION['admin_name'],3);
	return $db->affected_rows();
}
function get_news_one($id){
	global $db;
	$sql = "select * from ".table('train_news')."  WHERE id='".intval($id)."' LIMIT 1";
	return $db->getone($sql);
}
function get_train($offset,$perpage,$get_sql= '',$mode=1)
{
	global $db;
	$colum=$mode==1?'p.points':'p.setmeal_name';
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT t.*,m.username,m.mobile,m.email as memail,{$colum} FROM ".table('train_profile')." AS t ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['train_url']=url_rewrite('QS_train_agencyshow',array('id'=>$row['id']));
	$row_arr[] = $row;
	}
	return $row_arr;
}
function edit_train_audit($uid,$audit,$reason,$pms_notice)
{
	global $db,$_CFG;	
	$audit=intval($audit);
	$pms_notice=intval($pms_notice);
	$reason=trim($reason);
	if (!is_array($uid)) $uid=array($uid);
	$sqlin=implode(",",$uid);	
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('train_profile')." SET audit='{$audit}'  WHERE uid IN ({$sqlin})")) return false;
		write_log("����ԱuidΪ".$sqlin."����ѵ���������״̬����Ϊ".$audit, $_SESSION['admin_name'],3);
		//����վ����
		if ($pms_notice=='1')
		{
			$reason=$reason==''?'��':$reason;
			if($audit=='1') {$note='�ɹ�ͨ����վ����Ա���!';}elseif($audit=='2'){$note='���������!';}else{$note='δͨ����վ����Ա��ˣ�';}
			$result = $db->query("SELECT trainname,uid FROM ".table('train_profile')." WHERE uid IN ({$sqlin})");
			while($list = $db->fetch_array($result))
			{
				$user_info=get_user($list['uid']);
				$setsqlarr['message']="���Ļ�����{$list['trainname']},".$note.'����˵����'.$reason;
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
			if($_CFG['operation_train_mode']=='1'){
				$points_rule=get_cache('points_rule');
				if ($points_rule['train_auth']['value']>0)//�����������֤���ͺ�«��
				{
					gift_points($sqlin,'trainauth',$points_rule['train_auth']['type'],$points_rule['train_auth']['value']);
				}
			}
		}
		$mailconfig=get_cache('mailconfig');
		$sms=get_cache('sms_config');
		if ($audit=="1" && $mailconfig['set_licenseallow']=='1')//��֤ͨ��
		{
			$result = $db->query("SELECT uid FROM ".table('train_profile')." WHERE uid IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['email_audit']=="1")
					{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid={$list['uid']}&key=".asyn_userkey($list['uid'])."&act=set_licenseallow");
					}
				}
		}
		if ($audit=="3" && $mailconfig['set_licensenotallow']=="1")//��֤δͨ��
		{
			$result = $db->query("SELECT uid FROM ".table('train_profile')." WHERE uid IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['email_audit']=="1")
					{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid={$list['uid']}&key=".asyn_userkey($list['uid'])."&act=set_licensenotallow");
					}
				}
		}
		//sms		
		if ($audit=="1" && $sms['open']=="1" && $sms['set_licenseallow']=="1" )
		{
			$result = $db->query("SELECT uid FROM ".table('train_profile')." WHERE uid IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['mobile_audit']=="1")
					{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid={$list['uid']}&key=".asyn_userkey($list['uid'])."&act=set_licenseallow");
					}
				}
		}
		//sms
		if ($audit=="3" && $sms['open']=="1" && $sms['set_licensenotallow']=="1" )//��֤δͨ��
		{
			$result = $db->query("SELECT uid FROM ".table('train_profile')." WHERE uid IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['mobile_audit']=="1")
					{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid={$list['uid']}&key=".asyn_userkey($list['uid'])."&act=set_licensenotallow");
					}
				}
		}
	return true;
	}
	return false;
}
function get_user_setmeal($uid)
{
	global $db;
	$sql = "select * from ".table('members_train_setmeal')."  WHERE uid=".intval($uid)." AND  effective=1 LIMIT 1";
	return $db->getone($sql);
}
function refresh_train($uid,$refrescou=false)
{
	global $db;
	$return=0;
	if (!is_array($uid))$uid=array($uid);
	$sqlin=implode(",",$uid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('train_profile')." SET refreshtime='".time()."'  WHERE uid IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		if ($refrescou)
		{
		if (!$db->query("update  ".table('course')." SET refreshtime='".time()."'  WHERE uid IN ({$sqlin})")) return false;
		//$return=$return+$db->affected_rows();
		}
	}
	write_log("ˢ�»�ԱuidΪ".$sqlin."����ѵ����,��ˢ��".$return."��", $_SESSION['admin_name'],3);
	return $return;
}
function del_train($uid)
{
	global $db,$certificate_train_dir;
	if (!is_array($uid))$uid=array($uid);
	$sqlin=implode(",",$uid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$result = $db->query("SELECT certificate_img FROM ".table('train_profile')." WHERE uid IN ({$sqlin})");
		while($row = $db->fetch_array($result))
		{
		@unlink($certificate_train_dir.$row['certificate_img']);
		}
		if (!$db->query("Delete from ".table('train_profile')." WHERE uid IN ({$sqlin})")) return false;
		write_log("ɾ����ԱuidΪ".$sqlin."����ѵ����", $_SESSION['admin_name'],3);
	return true;
	}
	return false;
}
function del_train_allteacher($uid)
{
	global $db;
	if (!is_array($uid))$uid=array($uid);
	$sqlin=implode(",",$uid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$db->query("Delete from ".table('train_teachers')." WHERE uid IN ({$sqlin})");
		write_log("ɾ����ԱuidΪ".$sqlin."�Ľ�ʦ", $_SESSION['admin_name'],3);
		return true;
	}
	return false;
}
function del_train_allcourse($uid)
{
	global $db;
	if (!is_array($uid))$uid=array($uid);
	$sqlin=implode(",",$uid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$result = $db->query("SELECT id FROM ".table('course')." WHERE uid IN ({$sqlin}) ");
		while($row = $db->fetch_array($result))
		{
		$db->query("Delete from ".table('course_contact')." WHERE pid IN ({$row['id']})");	
		}
		$db->query("Delete from ".table('course')." WHERE uid IN ({$sqlin})");
		write_log("ɾ����ԱuidΪ".$sqlin."�Ŀγ�", $_SESSION['admin_name'],3);
		return true;
	}
	return false;
}
//����idɾ����ʦ
function del_train_idteacher($id)
{
	global $db;
	if (!is_array($id))$uid=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$db->query("Delete from ".table('train_teachers')." WHERE id IN ({$sqlin})");
		write_log("ɾ����ʦidΪ".$sqlin."�Ľ�ʦ", $_SESSION['admin_name'],3);
		return true;
	}
	return false;
}
//���ݽ�ʦidɾ���γ�
function del_train_teacourse($teacher_id)
{
	global $db;
	if (!is_array($teacher_id))$teacher_id=array($teacher_id);
	$sqlin=implode(",",$teacher_id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$result = $db->query("SELECT id FROM ".table('course')." WHERE teacher_id IN ({$sqlin}) ");
		while($row = $db->fetch_array($result))
		{
		$db->query("Delete from ".table('course_contact')." WHERE pid IN ({$row['id']})");	
		}
		$db->query("Delete from ".table('course')." WHERE teacher_id IN ({$sqlin})");
		write_log("ɾ����ʦidΪ".$sqlin."�Ŀγ�", $_SESSION['admin_name'],3);
		return true;
	}
	return false;
}

function get_train_one_id($id)
{
	global $db;
	$id=intval($id);
	$sql = "select * from ".table('train_profile')." where id='{$id}'";
	$val=$db->getone($sql);
	$val['user']=get_user($val['uid']);
	return $val;
}
function get_meal_members_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT a.*,b.*,c.trainname FROM ".table('members_train_setmeal')." as a ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row_arr[] = $row;
	}
	return $row_arr;
}
function get_train_one_uid($uid)
{
	global $db;
	$uid=intval($uid);
	$sql = "select * from ".table('train_profile')." where uid={$uid}";
	$val=$db->getone($sql);
	return $val;
}
function set_members_setmeal($uid,$setmealid)
{
	global $db,$timestamp;
	$setmeal=$db->getone("select * from ".table('train_setmeal')." WHERE id = ".intval($setmealid)." AND display=1 LIMIT 1");
	if (empty($setmeal)) return false;
	$setsqlarr['effective']=1;
	$setsqlarr['setmeal_id']=$setmeal['id'];
	$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
	$setsqlarr['days']=$setmeal['days'];
	$setsqlarr['refresh_course_space']=$setmeal['refresh_course_space'];
	$setsqlarr['refresh_course_time']=$setmeal['refresh_course_time'];
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
	$setsqlarr['teachers_num']=$setmeal['teachers_num'];
	$setsqlarr['course_num']=$setmeal['course_num'];
	$setsqlarr['down_apply']=$setmeal['down_apply'];
	$setsqlarr['change_templates']=$setmeal['change_templates'];
	$setsqlarr['map_open']=$setmeal['map_open'];
	$setsqlarr['added']=$setmeal['added'];
	if (!$db->updatetable(table('members_train_setmeal'),$setsqlarr," uid='{$uid}'")) return false;
	$setmeal_course['setmeal_deadline']=$setsqlarr['endtime'];
	$setmeal_course['setmeal_id']=$setsqlarr['setmeal_id'];
	$setmeal_course['setmeal_name']=$setsqlarr['setmeal_name'];
	if (!$db->updatetable(table('course'),$setmeal_course," uid='{$uid}' AND add_mode='2' ")) return false;
	return true;
}
function get_order_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT o.*,m.username,m.email,c.trainname FROM ".table('order')." as o ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['payment_name']=get_payment_info($row['payment_name'],true);
	$row_arr[] = $row;
	}
	return $row_arr;
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
//ȡ������
function del_order($id)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('order')." WHERE id IN (".$sqlin.")  AND is_paid=1 ")) return false;
		write_log("ɾ������idΪ".$sqlin."�Ķ���", $_SESSION['admin_name'],3);	
		return true;
	}
	return false;
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
					write_memberslog($order['uid'],4,9101,$user['username'],$notes);
					write_setmeallog($order['uid'],$user['username'],$notes,4,$order['amount'],$ismoney,1,4);
			}
			if ($order['setmeal']>0)
			{
					set_members_setmeal($order['uid'],$order['setmeal']);
					$setmeal=get_setmeal_one($order['setmeal']);
					$notes="�����ˣ�{$_SESSION['admin_name']},˵����ȷ���տ�տ��{$order['amount']} ��".date('Y-m-d H:i',time())."ͨ����".get_payment_info($order['payment_name'],true)." �ɹ���ֵ ".$order['amount']."Ԫ����ͨ{$setmeal['setmeal_name']}";
					write_memberslog($order['uid'],4,9102,$user['username'],$notes);
					write_setmeallog($order['uid'],$user['username'],$notes,4,$order['amount'],$ismoney,2,4);
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
		write_log("��������Ϊ".$v_oid."�Ķ�������Ϊȷ���տ�", $_SESSION['admin_name'],3);
		return true;
	}
return true;
}
function edit_setmeal_notes($setarr,$setmeal){
	$diff_arr= array_diff_assoc($setarr,$setmeal);
	if($diff_arr){
		foreach($diff_arr as $key=>$value){
			if($key=='teachers_num'){
				$str.="��ӽ�ʦ����{$setmeal['teachers_num']}-{$setarr['teachers_num']}";
			}elseif($key=='course_num'){
				$str.=",�����γ�����{$setmeal['course_num']}-{$setarr['course_num']}";
			}elseif($key=='down_apply'){
				$str.=",�����������룺{$setmeal['down_apply']}-{$setarr['down_apply']}";
			}elseif($key=='change_templates'){
					$flag=$setmeal['change_templates']=='1'?'����':'������';
					$flag1=$setarr['change_templates']=='1'?'����':'������';
				$str.=",�����л�ģ�壺{$flag}-{$flag1}";
			}elseif($key=='map_open'){
					$flag=$setmeal['map_open']=='1'?'����':'������';
					$flag1=$setarr['map_open']=='1'?'����':'������';
				$str.=",���ӵ�ͼ��{$flag}-{$flag1}";
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
	$result = $db->query("SELECT a.*,{$colum},c.trainname FROM ".table('members_charge_log')." as a ".$get_sql.$limit);
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
	write_log("ɾ���ײͱ����¼idΪ".$sqlin."�Ĳͱ����¼,��ɾ��".$num."��", $_SESSION['admin_name'],3);
	return $db->affected_rows();
}
function get_course($offset,$perpage,$get_sql= '')
{
	global $db,$timestamp;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query($get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['course_name']=cut_str($row['course_name'],12,0,"...");
	$row['trainname']=cut_str($row['trainname'],18,0,"...");
	$row['train_url']=url_rewrite('QS_train_agencyshow',array('id'=>$row['train_id']));
	$row['course_url']=url_rewrite('QS_train_curriculumshow',array('id'=>$row['id']),1,$row['subsite_id']);
	$row_arr[] = $row;
	}
	return $row_arr;
}
function get_teacher($offset,$perpage,$get_sql= '')
{
	global $db,$timestamp;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query($get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['teachername']=cut_str($row['teachername'],12,0,"...");
	$row['trainname']=cut_str($row['trainname'],18,0,"...");
	$row['teacher_url']=url_rewrite('QS_train_lecturershow',array('id'=>$row['id']));
	$row['train_url']=url_rewrite('QS_train_agencyshow',array('id'=>$row['train_id']));
	$row_arr[] = $row;
	}
	return $row_arr;
}
function get_teacher_one($id)
{
	global $db,$timestamp;
	$data=$db->getone("select * from ".table('train_teachers')." WHERE id={$id} limit 1");
	$data['teacher_url']=url_rewrite('QS_train_lecturershow',array('id'=>$data['id']));
	$data['train_url']=url_rewrite('QS_train_agencyshow',array('id'=>$data['train_id']));
	$data['user']=get_user($data['uid']);
	return $data;
}
function get_course_one($id)
{
	global $db;
	$id=intval($id);
	$val=$db->getone("select * from ".table('course')." where id='{$id}' LIMIT 1");
	$val['course_url']=url_rewrite('QS_train_curriculumshow',array('id'=>$val['id']),1,$val['subsite_id']);
	$val['train_url']=url_rewrite('QS_train_agencyshow',array('id'=>$val['train_id']));
	$val['user']=get_user($val['uid']);
	$val['contact']=$db->getone("select * from ".table('course_contact')." where pid='{$id}' LIMIT 1");
	return $val;
}
function get_audit_teachers($train_id)
{
	global $db,$timestamp;
	$row_arr = array();
	$data=$db->getall('select id,teachername from '.table('train_teachers').' WHERE train_id='.intval($train_id).' AND audit=1 ');
	return $data;
}
function del_course($id)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('course')." WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		if (!$db->query("Delete from ".table('course_contact')." WHERE pid IN ({$sqlin})")) return false;
		write_log("ɾ���γ�idΪ".$sqlin."�Ŀγ�,��ɾ��".$return."��", $_SESSION['admin_name'],3);
		return $return;
	}
	else
	{
	return false;
	}
}
function edit_course_audit($id,$audit,$reason,$pms_notice='1')
{
	global $db,$_CFG;
	$audit=intval($audit);
	$reason=trim($reason);
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('course')." SET audit={$audit}  WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		write_log("���γ�idΪ".$sqlin."�Ŀγ̵����״̬����Ϊ".$audit."������".$return."��", $_SESSION['admin_name'],3);
			//����վ����
			if ($pms_notice=='1')
			{
					$result = $db->query("SELECT course_name,uid FROM ".table('course')." WHERE id IN ({$sqlin})");
					$reason=$reason==''?'ԭ��δ֪':'ԭ��'.$reason;
					while($list = $db->fetch_array($result))
					{
						$user_info=get_user($list['uid']);
						$setsqlarr['message']=$audit=='1'?"�������Ŀγ̣�{$list['course_name']},�ɹ�ͨ����վ����Ա��ˣ�":"�������Ŀγ̣�{$list['course_name']},δͨ����վ����Ա���,{$reason}";
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
			if ($audit=="1" && $mailconfig['set_couallow']=="1")//���ͨ��
			{
					$result = $db->query("SELECT course_name,uid FROM ".table('course')." WHERE id IN ({$sqlin})");
					while($list = $db->fetch_array($result))
					{
						$user_info=get_user($list['uid']);
						if ($user_info['email_audit']=="1")
						{				
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&coursename=".$list['course_name']."&act=set_couallow");
						}
					}
			}
			if ($audit=="3" && $mailconfig['set_counotallow']=="1")//���δͨ��
			{
					$result = $db->query("SELECT course_name,uid FROM ".table('course')." WHERE id IN ({$sqlin})");
					while($list = $db->fetch_array($result))
					{
						$user_info=get_user($list['uid']);
						if ($user_info['email_audit']=="1")
						{
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&coursename=".$list['course_name']."&act=set_counotallow");
						}
					}
			}
			//sms		
			if ($audit=="1" && $sms['open']=="1" && $sms['set_couallow']=="1" )
			{
				$mobilearray = array();
				$result = $db->query("SELECT course_name,uid FROM ".table('course')." WHERE id IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['mobile_audit']=="1")
					{
						//��ͬһ��Ա�Ŀγ̷ŵ������� ����ȷ���û�ԱΨһ��
						$mobilearray[$list['uid']] = empty($mobilearray[$list['uid']]) ? $list['course_name'] : $mobilearray[$list['uid']].' , '.$list['course_name'];
					}
				}
				foreach ($mobilearray as $key => $value) 
				{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$key."&key=".asyn_userkey($key)."&coursename=".$value."&act=set_couallow");
				}
			}
			//sms
			if ($audit=="3" && $sms['open']=="1" && $sms['set_counotallow']=="1" )//��֤δͨ��
			{
				$mobilearray = array();
				$result = $db->query("SELECT course_name,uid FROM ".table('course')." WHERE id IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['mobile_audit']=="1")
					{
						//��ͬһ��Ա�Ŀγ̷ŵ������� ����ȷ���û�ԱΨһ��
						$mobilearray[$list['uid']] = empty($mobilearray[$list['uid']]) ? $list['course_name'] : $mobilearray[$list['uid']].' , '.$list['course_name'];
					}
				}
				foreach ($mobilearray as $key => $value) 
				{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$key."&key=".asyn_userkey($key)."&coursename=".$value."&act=set_counotallow");
				}
			}
			//sms
		return $return;
	}
	else
	{
	return $return;
	}
}
function refresh_course($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$time=time();
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('course')." SET refreshtime='{$time}'  WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		write_log("ˢ�¿γ�idΪ".$sqlin."�Ŀγ�,��ˢ��".$return."��", $_SESSION['admin_name'],3);
	}
	return $return;
}
function refresh_teacher($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$time=time();
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('train_teachers')." SET refreshtime='{$time}'  WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		write_log("ˢ�½�ʦidΪ".$sqlin."�Ľ�ʦ,������".$return."��", $_SESSION['admin_name'],3);
	}
	return $return;
}
function recom_course($id,$recommend,$pms_notice,$notice)
{
	global $db;
	$return=0;
	$recommend=intval($recommend);
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$time=time();
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('course')." SET recommend='{$recommend}'  WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		write_log("���γ�idΪ".$sqlin."�Ŀγ̵��ƹ�״̬����Ϊ".$recommend.",������".$return."��", $_SESSION['admin_name'],3);
		//����վ����
		if ($pms_notice=='1')
		{
				$result = $db->query("SELECT course_name,uid FROM ".table('course')." WHERE id IN ({$sqlin})");
				$notice=$notice==''?'˵������':'˵����'.$notice;
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					$setsqlarr['message']=$recommend=='1'?"����ӵĿγ̣�{$list['course_name']},������Ա����Ϊ(��Ʒ�γ�)��{$notice}":"����ӵĿγ̣�{$list['course_name']},������Աȡ��(��Ʒ�γ�)��{$notice}";
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
function recom_teacher($id,$recommend,$pms_notice,$notice)
{
	global $db;
	$return=0;
	$recommend=intval($recommend);
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$time=time();
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('train_teachers')." SET recommend='{$recommend}'  WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		write_log("����ʦidΪ".$sqlin."�Ľ�ʦ,�ƹ�״̬��Ϊ".$recommend, $_SESSION['admin_name'],3);
		//����վ����
		if ($pms_notice=='1')
		{
				$result = $db->query("SELECT teachername,uid FROM ".table('train_teachers')." WHERE id IN ({$sqlin})");
				$notice=$notice==''?'˵������':'˵����'.$notice;
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					$setsqlarr['message']=$recommend=='1'?"����ӵĽ�ʦ��{$list['teachername']},������Ա����Ϊ(�Ƽ���ʦ)��{$notice}":"����ӵĽ�ʦ��{$list['teachername']},������Աȡ��(�Ƽ���ʦ)��{$notice}";
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
function recom_train($id,$recommend,$pms_notice,$notice)
{
	global $db;
	$return=0;
	$recommend=intval($recommend);
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$time=time();
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('train_profile')." SET recommend='{$recommend}'  WHERE uid IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		write_log("����ԱuidΪ".$sqlin."����ѵ�������ƹ�״̬����Ϊ".$recommend.",��ɾ��".$return."��", $_SESSION['admin_name'],3);
		//����վ����
		if ($pms_notice=='1')
		{
				$result = $db->query("SELECT trainname,uid FROM ".table('train_profile')." WHERE uid IN ({$sqlin})");
				$notice=$notice==''?'˵������':'˵����'.$notice;
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					$setsqlarr['message']=$recommend=='1'?"����ӵ���ѵ������{$list['trainname']},������Ա����Ϊ(�Ƽ�����)��{$notice}":"����ӵ���ѵ������{$list['trainname']},������Աȡ��(�Ƽ�����)��{$notice}";
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
function delay_course($id,$days)
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
		$result = $db->query("SELECT id,deadline FROM ".table('course')." WHERE id IN ({$sqlin}) ");
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
			if (!$db->query("update  ".table('course')." SET deadline='{$deadline}'  WHERE id='{$row['id']}'  LIMIT 1")) return false;
			$return=$return+$db->affected_rows();
			write_log("���γ�idΪ".$sqlin."�Ŀγ̣�����,������".$return."��", $_SESSION['admin_name'],3);
		}
	}
	return $return;
	
}
function edit_teachers_audit($id,$audit,$reason,$pms_notice='1')
{
	global $db,$_CFG;
	$audit=intval($audit);
	$reason=trim($reason);
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('train_teachers')." SET audit={$audit}  WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		write_log("����ʦidΪ".$sqlin."�Ľ�ʦ,���״̬����Ϊ".$audit."������".$return."��", $_SESSION['admin_name'],3);
			//����վ����
			if ($pms_notice=='1')
			{
					$result = $db->query("SELECT uid,teachername FROM ".table('train_teachers')." WHERE id IN ({$sqlin})");
					$reason=$reason==''?'ԭ��δ֪':'ԭ��'.$reason;
					while($list = $db->fetch_array($result))
					{
						$user_info=get_user($list['uid']);
						$setsqlarr['message']=$audit=='1'?"����ӵĽ�ʦ��{$list['teachername']},�ɹ�ͨ����վ����Ա��ˣ�":"����ӵĽ�ʦ��{$list['teachername']},δͨ����վ����Ա���,{$reason}";
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
			if ($audit=="1" && $mailconfig['set_teaallow']=="1")//���ͨ��
			{
					$result = $db->query("SELECT teachername,uid  FROM ".table('train_teachers')." WHERE id IN ({$sqlin})");
					while($list = $db->fetch_array($result))
					{
						$user_info=get_user($list['uid']);
						if ($user_info['email_audit']=="1")
						{				
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&teachername=".$list['teachername']."&act=set_teaallow");
						}
					}
			}
			if ($audit=="3" && $mailconfig['set_teanotallow']=="1")//���δͨ��
			{
					$result = $db->query("SELECT teachername,uid  FROM ".table('train_teachers')." WHERE id IN ({$sqlin})");
					while($list = $db->fetch_array($result))
					{
						$user_info=get_user($list['uid']);
						if ($user_info['email_audit']=="1")
						{
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&teachername=".$list['teachername']."&act=set_teanotallow");
						}
					}
			}
			//sms		
			if ($audit=="1" && $sms['open']=="1" && $sms['set_teaallow']=="1" )
			{
				$mobilearray = array();
				$result = $db->query("SELECT teachername,uid  FROM ".table('train_teachers')." WHERE id IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['mobile_audit']=="1")
					{
						//��ͬһ��Ա�Ŀγ̷ŵ������� ����ȷ���û�ԱΨһ��
						$mobilearray[$list['uid']] = empty($mobilearray[$list['uid']]) ? $list['teachername'] : $mobilearray[$list['uid']].' , '.$list['teachername'];
					}
				}
				foreach ($mobilearray as $key => $value) 
				{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$key."&key=".asyn_userkey($key)."&teachername=".$value."&act=set_teaallow");
				}
			}
			//sms
			if ($audit=="3" && $sms['open']=="1" && $sms['set_teanotallow']=="1" )//��֤δͨ��
			{
				$mobilearray = array();
				$result = $db->query("SELECT teachername,uid  FROM ".table('train_teachers')." WHERE id IN ({$sqlin})");
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					if ($user_info['mobile_audit']=="1")
					{
						//��ͬһ��Ա�Ŀγ̷ŵ������� ����ȷ���û�ԱΨһ��
						$mobilearray[$list['uid']] = empty($mobilearray[$list['uid']]) ? $list['teachername'] : $mobilearray[$list['uid']].' , '.$list['teachername'];
					}
				}
				foreach ($mobilearray as $key => $value) 
				{
					dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$key."&key=".asyn_userkey($key)."&teachername=".$value."&act=set_teanotallow");
				}
			}
			//sms
		return $return;
	}
	else
	{
	return $return;
	}
}
function delete_train_user($uid)
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
		if (!$db->query("Delete from ".table('members_train_setmeal')." WHERE uid IN (".$sqlin.")")) return false; 
		return true;
		write_log("ɾ����ԱuidΪ".$sqlin."����ѵ������Ա", $_SESSION['admin_name'],3);
	}
	return false;
}

?>