<?php
 /*
 * 74cms SMS
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : ''; 
$mobile=trim($_POST['mobile']);
$send_key=trim($_POST['send_key']);
if (empty($send_key) || $send_key<>$_SESSION['send_mobile_key'])
{
exit("Ч�������");
}
$SMSconfig=get_cache('sms_config');
if ($SMSconfig['open']!="1")
{
exit("����ģ�鴦�ڹر�״̬");
}
if ($act=="send_code")
{
		if (empty($mobile) || !preg_match("/^(13|14|15|17|18)\d{9}$/",$mobile))
		{
		exit("�ֻ��Ŵ���");
		}
		
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
elseif ($act=="verify_code")
{
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
				exit("success");
			}
	}
}
?>