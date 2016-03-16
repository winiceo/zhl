<?php
 /*
 * 74cms ��������˻�Ա�����˺Ű�ȫ
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
require_once(QISHI_ROOT_PATH.'include/fun_weixin.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'verify_mobile';
if ($_SESSION['uid']=='' || $_SESSION['username']==''||intval($_SESSION['utype'])==1)
{
	header("Location: ../login.php");
}
elseif ($act == 'verify_mobile')
{
	$uid = intval($_SESSION['uid']);
	$sql = "select * from ".table('members')." where uid = '{$uid}' LIMIT 1";
	$userinfo=$db->getone($sql);
	$smarty->assign('userinfo',$userinfo);
	$smarty->assign('mobile',$userinfo['mobile']);
	$smarty->assign('email',$userinfo['email']);
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$smarty->assign('send_mobile_key',$_SESSION['send_mobile_key']);
	$smarty->display("m/personal/m-securityaccount.html");
}
elseif ($act=="send_code")
{
	$send_key=trim($_POST['send_key']);
	if (empty($send_key) || $send_key<>$_SESSION['send_mobile_key'])
	{
		exit("���Ӵ���");
	}
	$mobile=trim($_POST['mobile']);
	if (empty($mobile) || !preg_match("/^(13|15|14|17|18)\d{9}$/",$mobile))
	{
		exit("�ֻ��Ŵ���");
	}
	$sql = "select * from ".table('members')." where mobile = '{$mobile}' LIMIT 1";
	$userinfo=$db->getone($sql);
	if ($userinfo && $userinfo['uid']<>$_SESSION['uid'])
	{
		exit("�ֻ����Ѿ����ڣ�����д�����ֻ�����");
	}
	elseif(!empty($userinfo['mobile']) && $userinfo['mobile_audit']=="1" && $userinfo['mobile']==$mobile)
	{
		exit("����ֻ��� {$mobile} �Ѿ�ͨ����֤��");
	}
	else
	{
		if ($_SESSION['send_time'] && (time()-$_SESSION['send_time'])<180)
		{
			exit("��180����ٽ�����֤��");
		}
		$rand=mt_rand(100000, 999999);	
		$r=captcha_send_sms($mobile,"��л��ʹ��{$_CFG['site_name']}�ֻ���֤,��֤��Ϊ:{$rand}");
		if ($r=="success")
		{
			$_SESSION['mobile_rand']=$rand;
			$_SESSION['send_time']=time();
			$_SESSION['verify_mobile']=$mobile;
			exit("success");
		}
		else
		{
			exit("SMS���ó�������ϵ��վ����Ա");
		}
	} 
}
elseif ($act=="verify_code")
{
	$send_key=trim($_POST['send_key']);
	if (empty($send_key) || $send_key<>$_SESSION['send_mobile_key'])
	{
		exit("���Ӵ���");
	}
	$verifycode=trim($_POST['verifycode']);
	if (empty($verifycode) || empty($_SESSION['mobile_rand']) || $verifycode<>$_SESSION['mobile_rand'])
	{
		exit("��֤�����");
	}
	else
	{
		$uid=intval($_SESSION['uid']);
		if (empty($uid))
		{
			exit("ϵͳ����UID��ʧ��");
		}
		else
		{
			$setsqlarr['mobile']=$_SESSION['verify_mobile'];
			$setsqlarr['mobile_audit']=1;
			$db->updatetable(table('members'),$setsqlarr," uid='{$uid}'");
			$infoarr['phone']=$setsqlarr['mobile'];
			$db->updatetable(table('members_info'),$infoarr," uid='{$uid}'");
			$u['telephone']=$setsqlarr['mobile'];
			$db->updatetable(table('resume'),$u," uid='{$uid}'");
			// ���ֲ���
			$rule=get_cache('points_rule');
			if ($rule['per_verifymobile']['value']>0)
			{
				$info=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$_SESSION['uid']}' AND htype='per_verifymobile'   LIMIT 1");
				if(empty($info))
				{
				$time=time();			
				$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$_SESSION['uid']}', 'per_verifymobile','{$time}')");
				report_deal($_SESSION['uid'],$rule['per_verifymobile']['type'],$rule['per_verifymobile']['value']);
				$user_points=get_user_points($_SESSION['uid']);
				$operator=$rule['per_verifymobile']['type']=="1"?"+":"-";
				$_SESSION['handsel_per_verifymobile']=$_CFG['points_byname'].$operator.$rule['per_verifymobile']['value'];
				write_memberslog($_SESSION['uid'],2,9001,$_SESSION['username']," �ֻ��ڴ�����ͨ����֤��{$_CFG['points_byname']}({$operator}{$rule['per_verifymobile']['value']})��(ʣ��:{$user_points})",2,1015,"�ֻ��ڴ�������֤ͨ��","{$operator}{$rule['per_verifymobile']['value']}","{$user_points}");
				}
			}
			exit("success");
		}
	}
}
elseif ($act == 'edit_email')
{
	global $QS_pwdhash;
	$uid=intval($_SESSION['uid']);
	$send_key=trim($_POST['send_key']);
	if (empty($send_key) || $send_key<>$_SESSION['send_mobile_key'])
	{
		exit("���Ӵ���");
	}
	$email=trim($_POST['email']);
	$password=trim($_POST['password']);
	if (empty($email) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]w+)*$/",$email))
	{
		exit("�����ʽ����");
	}
	$sql = "select * from ".table('members')." where email = '{$email}' LIMIT 1";
	$userinfo=$db->getone($sql);
	if ($userinfo && $userinfo['uid']<>$_SESSION['uid'])
	{
		exit("�����Ѿ����ڣ�����д��������");
	}
	else
	{
		//��֤�����Ƿ���ȷ
		$sql = "select * from ".table('members')." where uid = '{$uid}' LIMIT 1";
		$member_info=$db->getone($sql);
		$pwd=md5(md5($password).$member_info['pwd_hash'].$QS_pwdhash);
		if ($member_info['password']!=$pwd)
		{
			exit("�������");
		}
		$setsqlarr['email']=$email;
		$setsqlarr['email_audit']=0;
		$db->updatetable(table('members'),$setsqlarr," uid='{$uid}'");
		$infoarr['email']=$setsqlarr['email'];
		$db->updatetable(table('members_info'),$infoarr," uid='{$uid}'");
		$u['email']=$setsqlarr['email'];
		$db->updatetable(table('resume'),$u," uid='{$uid}'");
		exit("success");
	}
}
elseif ($act == 'save_password')
{	
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$arr['username']=$_SESSION['username'];
	$arr['oldpassword']=trim($_POST['oldpassword'])?trim($_POST['oldpassword']):exit('����������룡');
	$arr['password']=trim($_POST['password'])?trim($_POST['password']):exit('�����������룡');
	if ($arr['password']!=trim($_POST['password1'])) exit('�����������벻��ͬ�����������룡');
	//edit_password()�޸�����ķ���
	$info=edit_password($arr);
	if ($info==-1) exit('����������������������룡');
	if ($info==$_SESSION['username'])
	{
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
		//����Ա��־�����¼
		write_memberslog($_SESSION['uid'],2,1004 ,$_SESSION['username'],"�޸�����");
		exit('�����޸ĳɹ���');
	 }
}

?>