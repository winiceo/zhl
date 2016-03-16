<?php
 /*
 * 74cms WAP
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
$alias="QS_login";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$smarty->cache = false;
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'enter';
if ($act=='enter')
{
	$smarty->assign('title','�һ����� - '.$_CFG['site_name']);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_getpwd',$captcha['verify_getpwd']);
	$smarty->assign('sms',get_cache('sms_config'));
	$smarty->assign('step',"1");
	$_SESSION['send_key']=mt_rand(100000, 999999);
	$smarty->assign('send_key',$_SESSION['send_key']);
	$smarty->display('m/password.html');
}
//�����ֻ���֤��
elseif($act=='pw_send_code')
{
	$mobile=trim($_POST['mobile']);
	$_POST=array_map("utf8_to_gbk", $_POST);
	if (empty($mobile) || !preg_match("/^(13|15|14|17|18)\d{9}$/",$mobile))
	{
		exit("�ֻ��Ŵ���");
	}
	$sql = "select * from ".table('members')." where mobile = '{$mobile}' LIMIT 1";
	$userinfo=$db->getone($sql);
	if (!$userinfo)
	{
		exit("�ֻ��Ų����ڣ�����д�����ֻ�����");
	}
	if ($_SESSION['send_time'] && (time()-$_SESSION['send_time'])<60)
	{
		exit("��60���������֤��");
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
//��֤�ֻ���֤��
elseif($act=='pw_validate_code')
{
	$_POST=array_map("utf8_to_gbk", $_POST);
	require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
	$mobile = trim($_POST['mobile']);
	if(empty($mobile))
	{
		exit("3");
	}
	//��֤��֤���Ƿ���ȷ
	$verifycode=trim($_POST['verifycode']);
	if (empty($verifycode) || empty($_SESSION['mobile_rand']) || $verifycode<>$_SESSION['mobile_rand'])
	{
		exit("2");
	}
	else
	{
		//��ϼ����ֶ�
		$userinfo=get_user_inmobile($mobile);
		$token=md5($userinfo['uid'].$userinfo['username'].$userinfo['reg_time']);
		exit("?act=set_pass&type=1&username=".$mobile."&token=".$token);
	}
}
//����������֤��
elseif($act=='pw_send_email_code')
{
	$email=trim($_POST['email']);
	$_POST=array_map("utf8_to_gbk", $_POST);
	if (empty($email) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]w+)*$/",$email))
	{
		exit("�����ʽ����");
	}
	$sql = "select * from ".table('members')." where email = '{$email}' LIMIT 1";
	$userinfo=$db->getone($sql);
	if (!$userinfo)
	{
		exit("���䲻���ڣ�����д���������˺�");
	}
	if ($_SESSION['send_email_time'] && (time()-$_SESSION['send_email_time'])<60)
	{
		exit("��60���������֤��");
	}
	$rand=mt_rand(100000, 999999);
	if (smtp_mail($email,"{$_CFG['site_name']}�ʼ���֤","{$QISHI['site_name']}��������<br>�����ڽ���������֤����֤��Ϊ:<strong>{$rand}</strong>"))
	{
		$_SESSION['email_rand']=$rand;
		$_SESSION['send_email_time']=time();
		$_SESSION['verify_email']=$email;
		exit("success");
	}
	else
	{
		exit("�������ó�������ϵ��վ����Ա");
	}
}
//��֤������
elseif($act=='pw_email_validate_code')
{
	$_POST=array_map("utf8_to_gbk", $_POST);
	require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
	$email = trim($_POST['email']);
	if(empty($email))
	{
		exit("3");
	}
	//��֤��֤���Ƿ���ȷ
	$emverifycode=trim($_POST['emverifycode']);
	if (empty($emverifycode) || empty($_SESSION['email_rand']) || $emverifycode<>$_SESSION['email_rand'])
	{
		exit("2");
	}
	else
	{
		//��ϼ����ֶ�
		$userinfo=get_user_inemail($email);
		$token=md5($userinfo['uid'].$userinfo['username'].$userinfo['reg_time']);
		exit("?act=set_pass&type=2&username=".$email."&token=".$token);
	}
}
//�һ������2�� -> ��������
elseif ($act=='set_pass')
{
	$send_key = trim($_GET['send_key']);
	$token = trim($_GET['token']);
	$username = trim($_GET['username']);
	if (preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$username))
	{
		$userinfo=get_user_inemail($username);
	}
	elseif (preg_match("/^(13|14|15|17|18)\d{9}$/",$username))
	{
		$userinfo=get_user_inmobile($username);
	}
	$token_two=md5($userinfo['uid'].$userinfo['username'].$userinfo['reg_time']);
	if (empty($send_key) || $send_key<>$_SESSION['send_key'] || $token_two<>$token)
	{
		$smarty->display('m/password-err.html');
	}
	else
	{
		$type = intval($_GET['type']);
		$smarty->assign('type',$type);
		$smarty->assign('username',$username);
		$smarty->display('m/password-set-new.html');
	}
}
elseif ($act=='set_pass_save')
{
	global $QS_pwdhash;
	$_POST=array_map("utf8_to_gbk", $_POST);
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$password_two = trim($_POST['password_two']);
	if(empty($username) || empty($password) || empty($password_two))
	{
		exit('��Ϣ��ʧ��');
	}
	if($password <>$password_two )
	{
		exit('������������벻ͬ��');
	}
	if (preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$username))
	{
		$userinfo=get_user_inemail($username);
	}
	elseif (preg_match("/^(13|14|15|17|18)\d{9}$/",$username))
	{
		$userinfo=get_user_inmobile($username);
	}
	$password_hash=md5(md5($password).$userinfo['pwd_hash'].$QS_pwdhash);
	$setsqlarr['password']=$password_hash;
	$rst=$db->updatetable(table('members'),$setsqlarr,array("uid"=>$userinfo['uid']));
	if($rst)
	{
		exit('ok');
	}	
	else
	{
		exit('����������ʧ�ܣ�');
	}
}
unset($smarty);
?>