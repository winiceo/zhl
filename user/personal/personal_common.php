<?php
/*
 * 74cms ���˻�Ա����
*/
if(!defined('IN_QISHI')) die('Access Denied!');
$page_select="user";
require_once(dirname(dirname(dirname(__FILE__))).'/include/common.inc.php');
$smarty->cache = false;
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
require_once(QISHI_ROOT_PATH.'include/fun_weixin.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if((empty($_SESSION['uid']) || empty($_SESSION['username']) || empty($_SESSION['utype'])) &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'] && $_COOKIE['QS']['uid'])
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	if(check_cookie($_COOKIE['QS']['uid'],$_COOKIE['QS']['username'],$_COOKIE['QS']['password']))
	{
	update_user_info($_COOKIE['QS']['uid'],false,false);
	header("Location:".get_member_url($_SESSION['utype']));
	}
	else
	{
	unset($_SESSION['uid'],$_SESSION['username'],$_SESSION['utype'],$_SESSION['uqqid'],$_SESSION['activate_username'],$_SESSION['activate_email'],$_SESSION["openid"]);
	setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[username]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[password]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	}	
}
if ($_SESSION['uid']=='' || $_SESSION['username']=='' || intval($_SESSION['uid'])===0 || (empty($_COOKIE['QS']['username']) && empty($_COOKIE['QS']['password']) && empty($_COOKIE['QS']['uid'])))
{
	header("Location: ".url_rewrite('QS_login')."?act=logout");
	exit();
}
elseif ($_SESSION['utype']!='2')
{
	$link[0]['text'] = "��Ա����";
	$link[0]['href'] = url_rewrite('QS_login');
	showmsg('�����ʵ�ҳ����Ҫ ���˻�Ա ��¼��',1,$link);
}
	$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
	$user=get_user_info($_SESSION['uid']);	
	if (empty($user))
	{
	unset($_SESSION['utype'],$_SESSION['uid'],$_SESSION['username']);
	header("Location: ".url_rewrite('QS_login')."?url=".$_SERVER["REQUEST_URI"]);
	exit();
	}
	elseif ($user['status']=="2" && $act!='index' && $act!='user_status'  && $act!='user_status_save') 
	{
		$link[0]['text'] = "���ػ�Ա������ҳ";
		$link[0]['href'] = 'personal_index.php?act=';
		exit(showmsg('�����˺Ŵ�����ͣ״̬������ϵ����Ա��Ϊ��������в�����',1,$link));	
	}
	if ($_CFG['login_per_audit_email'] && $user['email_audit']=="0" && $act!='authenticate' && $act!='user_email' && $act!='user_mobile')
	{
		$link[0]['text'] = "��֤����";
		$link[0]['href'] = 'personal_user.php?act=authenticate';
		$link[1]['text'] = "��վ��ҳ";
		$link[1]['href'] = $_CFG['site_dir'];
		showmsg('��������δ��֤����֤����ܽ�������������',1,$link,true,6);
		exit();
	}
	$sms=get_cache('sms_config');
	if ($_CFG['login_per_audit_mobile'] && $user['mobile_audit']=="0" && $act!='authenticate' && $act!='user_mobile' && $act!='user_email' && $sms['open']=="1")
	{
		$link[0]['text'] = "��֤�ֻ�";
		$link[0]['href'] = 'personal_user.php?act=authenticate';
		$link[1]['text'] = "��վ��ҳ";
		$link[1]['href'] = $_CFG['site_dir'];
		showmsg('�����ֻ�δ��֤����֤����ܽ�������������',1,$link,true,6);
		exit();
	}
	$smarty->assign('user',$user);
	$smarty->assign('userindexurl','personal_index.php');
	$auditresume = get_auditresume_list($_SESSION['uid'],2);
	$smarty->assign('auditresume',$auditresume);
	$smarty->assign('sms',$sms);
	// ����Ƿ� �����һ�ε�¼
	if($_SESSION['personal_login_first'] && $auditresume && $act!="edit_resume")
	{
		$smarty->assign('personal_login_first',$_SESSION['personal_login_first']);
		unset($_SESSION['personal_login_first']);
	}
?>