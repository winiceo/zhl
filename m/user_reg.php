<?php
 /*
 * 74cms ������ע��ģ��
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'reg';
$SMSconfig=get_cache('sms_config');
$smarty->assign('SMSconfig',$SMSconfig);
$smarty->caching = false;
if ($act == 'reg')
{
$smarty->display("m/reg-index.html");
}
elseif ($act=='reg_info')
{
	if ($_CFG['closereg']=='1')WapShowMsg("��վ��ͣ��Աע�ᣬ���Ժ��ٴγ��ԣ�",1);
	$utype = $_GET['utype'];
	if (intval($utype)==0)WapShowMsg("��ѡ��ע�����ͣ�",1);
	if(intval($_GET['type'])>2){
		WapShowMsg("��Ա���Ͳ���ȷ��������ѡ��",1);
	}
	$smarty->assign('utype',$utype);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userreg',$captcha['verify_userreg']);
	$smarty->display("m/reg.html");
}
//��ͨע��
elseif ($act == 'do_reg')
{
	require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	
	$_POST=array_map("utf8_to_gbk", $_POST);
	$username = isset($_POST['username'])?trim($_POST['username']):"";
	$password = isset($_POST['password'])?trim($_POST['password']):"";
	$member_type =intval($_POST['utype']);
	$email = isset($_POST['email'])?trim($_POST['email']):"";
	$agreement = isset($_POST['agreement'])?intval($_POST['agreement']):"";
	if (empty($username)||empty($password)||empty($member_type)||empty($email))
	{
	$err="��Ϣ������";
	}
	elseif (empty($agreement))
	{
	$err="��Ҫͬ��ע��Э��";
	}
	elseif (strlen($username)<6 || strlen($username)>18)
	{
	$err="�û�������Ϊ6-18���ַ�";
	}
	elseif (strlen($password)<6 || strlen($password)>18)
	{
	$err="���볤��Ϊ6-18���ַ�";
	}
	elseif ($password<>$_POST['password1'])
	{
	$err="������������벻ͬ";
	}
	elseif (empty($email) || !ereg("^[-a-zA-Z0-9_\.]+\@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$",$email))
	{
	$err="���������ʽ����";
	}
	if (get_user_inusername($username))
	{
	$err="�û����Ѿ�����";
	}
	if (get_user_inemail($email))
	{
	$err="���������Ѿ�����";
	}	
	if ($err)
	{
		exit($err);
	}	
	$register=user_register(3,$password,$member_type,$email,$mobile="",true,$username,"");
	if ($register>0)
	{
		$login_js=wap_user_login($username,$password);
		$mailconfig=get_cache('mailconfig');
		if ($mailconfig['set_reg']=="1")
		{
		dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&sendemail=".$email."&sendusername=".$username."&sendpassword=".$password."&act=reg");
		}
		if ($login_js)
		{
			exit($login_js['qs_login']);
		}
	}
	else
	{
		exit("user_reg.php");
	}
}
//�����ֻ���֤��
elseif ($act == 'reg_send_code')
{
	require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$_POST=array_map("utf8_to_gbk", $_POST);
	$mobile=trim($_POST['mobile']);
	if (empty($mobile) || !preg_match("/^(13|15|14|17|18)\d{9}$/",$mobile))
	{
		exit("�ֻ��Ŵ���");
	}
	$sql = "select * from ".table('members')." where mobile = '{$mobile}' LIMIT 1";
	$userinfo=$db->getone($sql);
	if ($userinfo)
	{
		exit("�ֻ����Ѵ��ڣ�����д�����ֻ�����");
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
//�ֻ���ע��
elseif ($act == 'phone_reg')
{
	$_POST=array_map("utf8_to_gbk", $_POST);
	require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$mobile = trim($_POST['mobile']);
	$password_mobile = isset($_POST['password_mobile'])?trim($_POST['password_mobile']):"";
	$member_type =intval($_POST['utype']);
	$agreement_mobile = isset($_POST['agreement_mobile'])?intval($_POST['agreement_mobile']):"";
	if(empty($mobile) || empty($password_mobile) || empty($member_type))
	{
		exit("��Ϣ��������");
	}
	elseif (empty($agreement_mobile))
	{
	 	exit("��Ҫͬ��ע��Э��");
	}
	//��֤��֤���Ƿ���ȷ
	$verifycode=trim($_POST['verifycode']);
	if (empty($verifycode) || empty($_SESSION['mobile_rand']) || $verifycode<>$_SESSION['mobile_rand'])
	{
		exit("��֤�����");
	}
	//�ֻ���ע��
	$register=user_register(1,$password_mobile,$member_type,$email="",$mobile,false,$username="","");
	if ($register>0)
	{
		$user_info=get_user_inmobile($mobile);
		$login_js=wap_user_login($user_info['username'],$password_mobile);
		if ($login_js)
		{
			exit($login_js['qs_login']);
		}
	}
	else
	{
		exit("user_reg.php");
	}
}
?>