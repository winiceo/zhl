<?php
/*
 * 74cms ���˻�Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');
$smarty->assign('leftmenu',"user");
if ($act=='binding')
{
	$smarty->assign('user',$user);
	$smarty->assign('title','�˺Ű� - ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_binding.htm');
}
elseif ($act=='userprofile')
{
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$_SESSION['send_email_key']=mt_rand(100000, 999999);
	$uid = intval($_SESSION['uid']);
	$smarty->assign('total',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1'"));
	$smarty->assign('send_mobile_key',$_SESSION['send_mobile_key']);
	$smarty->assign('send_email_key',$_SESSION['send_email_key']);
	$smarty->assign('user',$user);
	$smarty->assign('title','�������� - ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('userprofile',get_userprofile($_SESSION['uid']));
	// ��ע���Ա �����ȡע������
	$smarty->assign('user',$user);
	$smarty->display('member_personal/personal_userprofile.htm');
}
elseif ($act=='userprofile_save')
{
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['email']=trim($_POST['email'])?trim($_POST['email']):showmsg('����д���䣡',1);
	if($user['email_audit']!="1")
	{
		$members['email']=$setsqlarr['email'];
		$resume['email']=$setsqlarr['email'];
		$db->updatetable(table("members"),$members,array("uid"=>intval($_SESSION['uid'])));
		$db->updatetable(table("resume"),$resume,array("uid"=>intval($_SESSION['uid'])));
		unset($members['email'],$resume['email']);
	}
	$setsqlarr['phone']=trim($_POST['mobile'])?trim($_POST['mobile']):showmsg('����д�ֻ��ţ�',1);
	if($user['mobile_audit']!="1")
	{
		$members['mobile']=$setsqlarr['phone'];
		$resume['telephone']=$setsqlarr['phone'];
		$db->updatetable(table("members"),$members,array("uid"=>intval($_SESSION['uid'])));
		$db->updatetable(table("resume"),$resume,array("uid"=>intval($_SESSION['uid'])));
		unset($members['mobile'],$resume['telephone']);
	}
	$setsqlarr['realname']=trim($_POST['realname'])?trim($_POST['realname']):showmsg('����д��ʵ������',1);
	$setsqlarr['sex']=intval($_POST['sex'])?intval($_POST['sex']):showmsg('��ѡ���Ա�',1);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['birthday']=intval($_POST['birthday'])?intval($_POST['birthday']):showmsg('��ѡ��������',1);
	$setsqlarr['residence']=trim($_POST['residence'])?trim($_POST['residence']):showmsg('����д�־�ס�أ�',1);
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):showmsg('��ѡ��ѧ��',1);
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['major']=intval($_POST['major'])?intval($_POST['major']):showmsg('��ѡ��רҵ',1);
	$setsqlarr['major_cn']=trim($_POST['major_cn']);
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):showmsg('��ѡ��������',1);
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['height']=intval($_POST['height']);
	$setsqlarr['householdaddress']=trim($_POST['householdaddress']);
	$setsqlarr['marriage']=intval($_POST['marriage']);
	$setsqlarr['marriage_cn']=trim($_POST['marriage_cn']);
	if (get_userprofile($_SESSION['uid']))
	{
	$wheresql=" uid='".intval($_SESSION['uid'])."'";
	write_memberslog($_SESSION['uid'],2,1005,$_SESSION['username'],"�޸��˸�������");
	!$db->updatetable(table('members_info'),$setsqlarr,$wheresql)?showmsg("�޸�ʧ�ܣ�",0):showmsg("�޸ĳɹ���",2);
	}
	else
	{
	$setsqlarr['uid']=intval($_SESSION['uid']);
	write_memberslog($_SESSION['uid'],2,1005,$_SESSION['username'],"�޸��˸�������");
	!$db->inserttable(table('members_info'),$setsqlarr)?showmsg("�޸�ʧ�ܣ�",0):showmsg("�޸ĳɹ���",2);
	}
}
//ͷ��
elseif ($act=='avatars')
{
	$uid = intval($_SESSION['uid']);
	$smarty->assign('total',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1'"));
	$smarty->assign('title','����ͷ�� - ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('user',$user);
	$smarty->assign('rand',rand(1,100));
	$smarty->display('member_personal/personal_avatars.htm');
}
elseif ($act=='avatars_ready')
{
	require_once(QISHI_ROOT_PATH.'include/cut_upload.php');
	!$_FILES['avatars']['name']?showmsg('���ϴ�ͼƬ��',1):"";
	$up_dir_original="../../data/avatar/original/";
	$up_dir_100="../../data/avatar/100/";
	$up_dir_48="../../data/avatar/48/";
	$up_dir_thumb="../../data/avatar/thumb/";
	make_dir($up_dir_original.date("Y/m/d/"));
	make_dir($up_dir_100.date("Y/m/d/"));
	make_dir($up_dir_48.date("Y/m/d/"));
	make_dir($up_dir_thumb.date("Y/m/d/"));
	$setsqlarr['avatars']=_asUpFiles($up_dir_original.date("Y/m/d/"), "avatars",500,'gif/jpg/bmp/png',true);
	$setsqlarr['avatars']=date("Y/m/d/").$setsqlarr['avatars'];
	if ($setsqlarr['avatars'])
	{
		
	makethumb($up_dir_original.$setsqlarr['avatars'],$up_dir_thumb.date("Y/m/d/"),445,300);
	// makethumb($up_dir_original.$setsqlarr['avatars'],$up_dir_100.date("Y/m/d/"),100,100);
	// makethumb($up_dir_original.$setsqlarr['avatars'],$up_dir_48.date("Y/m/d/"),48,48);
	$wheresql=" uid='".$_SESSION['uid']."'";
	write_memberslog($_SESSION['uid'],2,1006 ,$_SESSION['username'],"�޸��˸���ͷ��");
	$db->updatetable(table('members'),$setsqlarr,$wheresql)?exit($setsqlarr['avatars']):showmsg('����ʧ�ܣ�',1);
	}
	else
	{
	showmsg('����ʧ�ܣ�',1);
	}
}
elseif ($act=='avatars_save')
{	
	$savePath = "../../data/avatar/100/";  //ͼƬ�洢·��
	$savePathThumb = "../../data/avatar/48/";  //ͼƬ�洢·��
	$savePicName = time();//ͼƬ�洢����
	$file_src = $savePath.$savePicName."_src.jpg";
	$filename150 = $savePath.$savePicName.".jpg"; 
	$filename50 = $savePathThumb.$savePicName.".jpg"; 
	$src=base64_decode($_POST['pic']);
	$pic1=base64_decode($_POST['pic1']);   
	$pic2=base64_decode($_POST['pic2']);
	if($src) {
		file_put_contents($file_src,$src);
	}
	file_put_contents($filename150,$pic1);
	if($pic2)file_put_contents($filename50,$pic2);
	$rs['status'] = 1;
	$rs['picUrl'] = $savePicName.".jpg";
	$setsqlarr['avatars']=$savePicName.".jpg";
	$wheresql=" uid='".$_SESSION['uid']."'";
	$db->updatetable(table('members'),$setsqlarr,$wheresql)?print json_encode($rs):showmsg('����ʧ�ܣ�',1);
	write_memberslog($_SESSION['uid'],2,1006 ,$_SESSION['username'],"�޸��˸���ͷ��");
}
//�޸�����
elseif ($act=='password_edit')
{
	$uid = intval($_SESSION['uid']);
	$smarty->assign('total',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1'"));
	$smarty->assign('title','�޸����� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_password.htm');
}
//�����޸�����
elseif ($act=='save_password')
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$arr['username']=$_SESSION['username'];
	$arr['oldpassword']=trim($_POST['oldpassword'])?trim($_POST['oldpassword']):showmsg('����������룡',1);
	$arr['password']=trim($_POST['password'])?trim($_POST['password']):showmsg('�����������룡',1);
	if ($arr['password']!=trim($_POST['password1'])) showmsg('�����������벻��ͬ�����������룡',1);
	$info=edit_password($arr);
	if ($info==-1) showmsg('����������������������룡',1);
	if ($info==$_SESSION['username']){
			//�����ʼ�
			$mailconfig=get_cache('mailconfig');
			if ($mailconfig['set_editpwd']=="1" && $user['email_audit']=="1")
			{
			dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&act=set_editpwd&newpassword=".$arr['password']);
			}
			//�ʼ��������
			//sms
			$sms=get_cache('sms_config');
			if ($sms['open']=="1" && $sms['set_editpwd']=="1"  && $user['mobile_audit']=="1")
			{
			dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&act=set_editpwd&newpassword=".$arr['password']);
			}
			//sms
			if(defined('UC_API'))
			{
			include_once(QISHI_ROOT_PATH.'uc_client/client.php');
			uc_user_edit($arr['username'],$arr['oldpassword'], $arr['password']);
			}
			//΢������
			set_editpwd($_SESSION['uid'],$_SESSION['username'],$arr['password']);
	 write_memberslog($_SESSION['uid'],2,1004 ,$_SESSION['username'],"�޸�����");
	 showmsg('�����޸ĳɹ���',2);
	 }
}
//�����޸��û���
elseif ($act=='save_username')
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$arr['uid']=$_SESSION['uid'];
	$_POST['newusername'] = utf8_to_gbk($_POST['newusername']);
	$arr['newusername']=trim($_POST['newusername'])?trim($_POST['newusername']):showmsg('���û�����',1);
	$row_newname = $db->getone("SELECT * FROM ".table('members')." WHERE username='{$arr['newusername']}' LIMIT 1");
	if($row_newname)
	{
		exit("-1");
	}
	$info=edit_username($arr);
	if ($info==-1) exit("-2");
	if (!$info) exit("-3");
	exit("1");
}
elseif ($act=='authenticate')
{
	$uid = intval($_SESSION['uid']);
	$smarty->assign('total',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1'"));
	$smarty->assign('user',$user);
	$smarty->assign('re_audit',$_GET['re_audit']);
	$smarty->assign('title','��֤���� - ���˻�Ա���� - '.$_CFG['site_name']);
	$_SESSION['send_key']=mt_rand(100000, 999999);
	$smarty->assign('send_key',$_SESSION['send_key']);
	/**
	 * ΢��ɨ���start
	 */
    if(intval($_CFG['weixin_apiopen'])==1 && intval($_CFG['weixin_scan_bind'])==1 && !$user['weixin_openid']){
	    $scene_id = mt_rand(20000001,30000000);
	    $_SESSION['scene_id'] = $scene_id;
		$dir = QISHI_ROOT_PATH.'data/weixin/'.($scene_id%10);
		make_dir($dir);
	    $fp = @fopen($dir.'/'.$scene_id.'.txt', 'wb+');
		$access_token = get_access_token();
	    $post_data = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$scene_id.'}}}';
	    $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
	    $result = https_request($url, $post_data);
	    $result_arr = json_decode($result,true);
	    $ticket = urlencode($result_arr["ticket"]);
	    $html = '<img width="240" height="240" src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket.'">';
		$smarty->assign('qrcode_img',$html);
	}else{
		$smarty->assign('qrcode_img','');
	}
    /**
     * ΢��ɨ���end
     */
	$smarty->display('member_personal/personal_authenticate.htm');
}
elseif ($act=='feedback')
{
	$smarty->assign('title','�û����� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('feedback',get_feedback($_SESSION['uid']));
	$smarty->display('member_personal/personal_feedback.htm');
}
//�����û�����
elseif ($act=='feedback_save')
{
	$get_feedback=get_feedback($_SESSION['uid']);
	if (count($get_feedback)>=5) 
	{
	showmsg('������Ϣ���ܳ���5����',1);
	exit();
	}
	$setsqlarr['infotype']=intval($_POST['infotype']);
	$setsqlarr['feedback']=trim($_POST['feedback'])?trim($_POST['feedback']):showmsg('����д���ݣ�',1);
	$setsqlarr['uid']=$_SESSION['uid'];
	$setsqlarr['usertype']=$_SESSION['utype'];
	$setsqlarr['username']=$_SESSION['username'];
	$setsqlarr['addtime']=$timestamp;
	write_memberslog($_SESSION['uid'],2,7001,$_SESSION['username'],"��ӷ�����Ϣ");
	!$db->inserttable(table('feedback'),$setsqlarr)?showmsg("���ʧ�ܣ�",0):showmsg("��ӳɹ�����ȴ�����Ա�ظ���",2);
}
//ɾ���û�����
elseif ($act=='del_feedback')
{
	$id=intval($_GET['id']);
	del_feedback($id,$_SESSION['uid'])?showmsg('ɾ���ɹ���',2):showmsg('ɾ��ʧ�ܣ�',1);
}
elseif ($act=='pm')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$uid=intval($_SESSION['uid']);
	$wheresql=" WHERE (p.msgfromuid='{$uid}' OR p.msgtouid='{$uid}') ";
	$joinsql=" LEFT JOIN  ".table('members')." AS i  ON  p.msgfromuid=i.uid ";
	$orderby=" order by p.pmid desc";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('pms').' AS p '.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$sql="SELECT p.* FROM ".table('pms').' AS p'.$joinsql.$wheresql.$orderby;
	//��ȡ���鿴��Ϣ��pmid , ���ҽ����޸�Ϊ�Ѷ�
	$pmid = update_pms_read($offset, $perpage,$sql);
	if(!empty($pmid))
	{
		$db->query("UPDATE ".table('pms')." SET `new`='2' WHERE new=1 AND msgtouid='{$uid}' and pmid in (".$pmid.")");	
	}
	else
	{
		$db->query("UPDATE ".table('pms')." SET `new`='2' WHERE new=1 AND msgtouid='{$uid}'");
	}	
	get_pms_no_num();
	$smarty->assign('pms',get_pms($offset,$perpage,$sql));
	$smarty->assign('total',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1'"));
	$smarty->assign('title','����Ϣ - ��Ա���� - '.$_CFG['site_name']);	
	$smarty->assign('page',$page->show(3));
	$smarty->assign('uid',$uid);  
	$smarty->display('member_personal/personal_user_pm.htm');
}
elseif ($act=='pm_del')
{
	$pmid=intval($_GET['pmid']);
	$uid=intval($_SESSION['uid']);
	$pms= $db->getone("select * from ".table('pms')." where pmid = '{$pmid}' AND (msgfromuid='{$uid}' OR msgtouid='{$uid}') LIMIT 1");
	if (!empty($pms))
	{
	$db->query("Delete from ".table('pms')." WHERE pmid='{$pms['pmid']}'");
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = "?act=pm";
	//ͳ����Ϣ
	$pmscount=$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$_SESSION['uid']}' OR msgtouid='{$_SESSION['uid']}') AND `new`='1' AND `replyuid`<>'{$_SESSION['uid']}'");
	setcookie('QS[pmscount]',$pmscount, $expire,$QS_cookiepath,$QS_cookiedomain);
	showmsg("�����ɹ���",2,$link);
}
elseif ($act=='del_qq_binding')
{
	$db->query("UPDATE ".table('members')." SET qq_openid = ''  WHERE uid='{$_SESSION[uid]}' LIMIT 1");
	exit('�����ѶQQ�󶨳ɹ���');
}
elseif ($act=='del_sina_binding')
{
	$db->query("UPDATE ".table('members')." SET sina_access_token = ''  WHERE uid='{$_SESSION[uid]}' LIMIT 1");
	exit('�������΢���󶨳ɹ���');
}
elseif ($act=='del_taobao_binding')
{
	$db->query("UPDATE ".table('members')." SET taobao_access_token = ''  WHERE uid='{$_SESSION[uid]}' LIMIT 1");
	exit('����Ա��˺Ű󶨳ɹ���');
}

//��Ա��¼��־
elseif ($act=='login_log')
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$uid = intval($_SESSION['uid']);
	$smarty->assign('total',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1'"));
	$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type='1001' ";
	$perpage=15;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_log').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('loginlog',get_user_loginlog($offset, $perpage,$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('title','��Ա��¼��־ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_user_loginlog.htm');
}
unset($smarty);
?>