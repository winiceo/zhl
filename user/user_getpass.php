<?php
 /*
 * 74cms ��Աע��
*/
define('IN_QISHI', true);
$alias="QS_login";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$smarty->cache = false;
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'enter';
$smarty->assign('header_nav',"getpass");
if ($act=='enter')
{
	$smarty->assign('title','�һ����� - '.$_CFG['site_name']);
	$token=substr(md5(mt_rand(100000, 999999)), 8,16);
	$_SESSION['getpass_token']=$token;
	$smarty->assign('token',$token);
	$smarty->display('user/get-pass.htm');
}
//�һ������2��
elseif ($act=='get_pass_step2')
{
	if(empty($_POST['token']) || $_POST['token']!=$_SESSION['getpass_token'])
	{
		$link[0]['text'] = "�һ�����ʧ��";
		$link[0]['href'] = "?act=enter";
		showmsg("�һ�����ʧ�ܣ�����������",0,$link);
	}
	$username=$_POST['username']?trim($_POST['username']):showmsg("�������û���/����/����֤�ֻ�");
	if (preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$username))
	{
		$usinfo=get_user_inemail($username);
	}
	elseif (preg_match("/^(13|14|15|18|17)\d{9}$/",$username))
	{
		$usinfo=get_user_inmobile($username);
	}
	else
	{
		$usinfo=get_user_inusername($username);
	}
	if($usinfo['mobile'])
	{
		$usinfo['mobile_']=preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$usinfo['mobile']);
	}
	if($usinfo['email'])
	{
		$usinfo['email_']=preg_replace('/([A-Za-z0-9_])[A-Za-z0-9_]*([A-Za-z0-9_])/','$1****$2',$usinfo['email'],1);
	}
	$token=substr(md5(mt_rand(100000, 999999)), 8,16);
	$_SESSION['getpass_token']=$token;
	$smarty->assign('token',$token);
	$smarty->assign('usinfo',$usinfo);
	$smarty->assign('title','�һ����� - ��֤���-'.$_CFG['site_name']);
	sms_get_token();
	$smarty->display('user/get-pass-step2.htm');
}
// �һ����� ������
elseif($act == 'get_pass_step3')
{
	if(empty($_POST['token']) || $_POST['token']!=$_SESSION['getpass_token'])
	{
		$link[0]['text'] = "�һ�����ʧ��";
		$link[0]['href'] = "?act=enter";
		showmsg("�һ�����ʧ�ܣ�����������",0,$link);
	}
	$uid=intval($_POST['uid']);
	$userinfo=get_user_inid($uid);
	$token=substr(md5(mt_rand(100000, 999999)), 8,16);
	$_SESSION['getpass_token']=$token;
	$smarty->assign('token',$token);
	$smarty->assign('userinfo',$userinfo);
	$smarty->assign('title','�һ����� - ����������-'.$_CFG['site_name']);
	$smarty->display('user/get-pass-step3.htm');
}
elseif($act=="get_pass_step3_email")
{	
	global $QS_pwdhash;
	$uid=$_GET['uid']?intval($_GET['uid']):"";
	$key=$_GET['key']?trim($_GET['key']):"";
	$time=$_GET['time']?trim($_GET['time']):"";
	$userinfo=get_user_inid($uid);
	if(empty($userinfo))
	{
		$link[0]['text'] = "�����һ�����";
		$link[0]['href'] = "?act=enter";
		showmsg("�һ�����ʧ��,�û���Ϣ����",0,$link);
	}
	$end_time=$time+24*3600;
	if($end_time<time())
	{
		$link[0]['text'] = "�����һ�����";
		$link[0]['href'] = "?act=enter";
		showmsg("�һ�����ʧ��,���ӹ���",0,$link);
	}
	$key_str=substr(md5($userinfo['username'].$QS_pwdhash),8,16);
	if($key_str!=$key)
	{
		$link[0]['text'] = "�����һ�����";
		$link[0]['href'] = "?act=enter";
		showmsg("�һ�����ʧ��,key����",0,$link);
	}
	$token=substr(md5(mt_rand(100000, 999999)), 8,16);
	$_SESSION['getpass_token']=$token;
	$smarty->assign('token',$token);
	$smarty->assign('userinfo',$userinfo);
	$smarty->assign('title','�һ����� - ����������-'.$_CFG['site_name']);
	$smarty->display('user/get-pass-step3.htm');
}
// ���� ����
elseif($act == "get_pass_save")
{
	global $QS_pwdhash;
	if(empty($_POST['token']) || $_POST['token']!=$_SESSION['getpass_token'])
	{
		$link[0]['text'] = "�����һ�����";
		$link[0]['href'] = "?act=enter";
		showmsg("�һ�����ʧ�ܣ�����������",0,$link);
	}
	$uid=intval($_POST['uid']);
	$password=$_POST['password']?trim($_POST['password']):showmsg("���������룡",1);
	$userinfo=get_user_inid($uid);
	if(empty($userinfo))
	{
		$link[0]['text'] = "�����һ�����";
		$link[0]['href'] = "?act=enter";
		showmsg("�޸�����ʧ��",0,$link);
	}
	$password_hash=md5(md5($password).$userinfo['pwd_hash'].$QS_pwdhash);
	$setsqlarr['password']=$password_hash;
	$rst=$db->updatetable(table('members'),$setsqlarr,array("uid"=>$userinfo['uid']));
	if($rst)
	{
		header("Location: ?act=get_pass_sucess"); 
	}	
	else
	{
		showmsg("����������ʧ�ܣ�",1);
	}
}
// �һ����� ���Ĳ�
elseif($act == "get_pass_sucess")
{
	$smarty->assign('title','�һ����� - �һسɹ� - '.$_CFG['site_name']);
	$smarty->display('user/get-pass-step4.htm');
}
unset($smarty);
?>