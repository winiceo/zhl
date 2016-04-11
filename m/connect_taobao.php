<?php
 /*
 * 74cms �Ա����ʺŵ�¼
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/plus.common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'login';
$top_parameters=trim($_REQUEST['top_parameters']);
$top_sign=trim($_REQUEST['top_sign']);
if($act == 'login' && empty($top_parameters))
{
	$url="https://oauth.taobao.com/authorize?response_type=user&client_id={$_CFG['taobao_appkey']}&redirect_uri=";
	$url.=urlencode("{$_CFG['wap_domain']}/connect_taobao.php");
	header("Location:{$url}");	
}
elseif($act == 'login' && !empty($top_parameters))
{
	require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	unset($dbhost,$dbuser,$dbpass,$dbname);
	require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
	if (empty($top_sign))
	{
	exit('��������');
	}
	$base64str=base64_encode(md5($top_parameters.$_CFG['taobao_appsecret'],TRUE ));
	if ($base64str<>$top_sign)
	{
	exit('�����Ƿ���');
	}
	else
	{
	$code=base64_decode($top_parameters);
	parse_str($code,$code);
	$token=md5($code['nick'].$code['user_id']);
	}
	if (empty($token))
	{
	exit('��¼ʧ�ܣ�token��ȡʧ��');
	}
	else
	{
				require_once(QISHI_ROOT_PATH.'include/fun_user.php');
				$uinfo=get_user_intaobao_access_token($token);
				if (!empty($uinfo))
				{
					update_user_info($uinfo['uid']);
					if($uinfo['utype']==1){
						$userurl="company/user.php";
					}elseif($uinfo['utype']==2){
						$userurl="personal/user.php";
					}
					header("Location: {$userurl}");
				}
				else
				{
					if (!empty($_SESSION['uid']) && !empty($_SESSION['utype']))
					{
					$db->query("UPDATE ".table('members')." SET taobao_access_token = '{$token}'  WHERE uid='{$_SESSION[uid]}' AND taobao_access_token='' LIMIT 1");
					exit('���ʺųɹ���');
					}
					else
					{
					$_SESSION['taobao_access_token']=$token;
					header("Location:?act=reg");
					}
				}
	}
	
}
elseif ($act=='reg')
{
	if (empty($_SESSION["taobao_access_token"]))
	{
		exit("access_token is empty");
	}
	else
	{
		require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
		$smarty->assign('title','������Ϣ - '.$_CFG['site_name']);
		$smarty->assign('t_url',"?act=");
		$smarty->display('m/bind-taobao.html');
	}
}
elseif ($act=='reg_save')
{
	if (empty($_SESSION["taobao_access_token"]))
	{
		exit("access_token is empty");
	}
	$val['username']=!empty($_POST['username'])?trim(utf8_to_gbk($_POST['username'])):exit("�����û���");
	$val['email']=!empty($_POST['email'])?trim($_POST['email']):exit("��������");
	$val['member_type']=intval($_POST['member_type']);
	$val['password']=!empty($_POST['password'])?trim($_POST['password']):exit("��������");
	if($val['password']!=trim($_POST['rpassword'])){
		exit("���벻һ��");
	}	
	require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	unset($dbhost,$dbuser,$dbpass,$dbname);
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$sql="select * from ".table("members")." where username='$val[username]' or email='$val[email]'";
	$row = $db->getall($sql);
	if(!empty($row)){
		exit("�û����������Ѿ����ڣ�");
	}
	$userid=user_register(2,$val['password'],$val['member_type'],$val['email']);
	if ($userid)
	{
		$db->query("UPDATE ".table('members')." SET taobao_access_token = '{$_SESSION['taobao_access_token']}'  WHERE uid='{$userid}' AND taobao_access_token='' LIMIT 1");
		unset($_SESSION["taobao_access_token"]);
		update_user_info($userid);
		exit("ok");
	}
	else
	{
		unset($_SESSION["taobao_access_token"]);
		require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
		exit('ע��ʧ�ܣ�');
	}
	
}