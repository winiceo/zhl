<?php
 /*
 * ��Ա��¼
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
$alias="QS_login";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$SMSconfig=get_cache('sms_config');
$smarty->assign('SMSconfig',$SMSconfig);
$smarty->caching = false;
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'login';
if($act == 'logout')
{
	unset($_SESSION['uid']);
	unset($_SESSION['username']);
	unset($_SESSION['utype']);
	setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[username]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[password]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	unset($_SESSION['activate_username']);
	unset($_SESSION['activate_email']);
	header("location:index.php"); 
}
elseif($act == 'weixin_login'){
	$openid = trim($_GET['openid']);
	$uid = intval($_GET['uid']);
	$event_key = intval($_GET['event_key']);
	weixin_login($openid,$uid,$event_key);

	
	$smarty->display('m/scan/scan_success.html');
}
elseif(!$_SESSION['uid'] && !$_SESSION['username'] && !$_SESSION['utype'] &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'] )
{
	if(check_cookie($_COOKIE['QS']['username'],$_COOKIE['QS']['password']))
	{
	update_user_info($_COOKIE['QS']['username'],false,false);
			if($_SESSION['utype']==2)	header("location:personal/user.php");
			if($_SESSION['utype']==1)	header("location:company/user.php");
	}
	else
	{
	setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[username]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[password]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	header("location:index.php"); 
	}
}
elseif ($_SESSION['username'] && $_SESSION['utype'] )
{
			if($_SESSION['utype']==2)	header("location:personal/user.php");
			if($_SESSION['utype']==1)	header("location:company/user.php");
}
elseif ($act=='login')
{
	$_SESSION['url'] = $_SERVER['HTTP_REFERER'];
	$smarty->assign('url',$_SERVER['HTTP_REFERER']);
	$smarty->display('m/login.html');
}
elseif ($act == 'do_login')
{	
	require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
	$_POST=array_map("utf8_to_gbk", $_POST);
	if($_POST['username']=="�û���/����/�ֻ���" || $_POST['passWord']==""|| $_POST['username']=="" )
	{
		exit("1");
	}
	else
	{
		$username=isset($_POST['username'])?trim($_POST['username']):"";
		$password=isset($_POST['passWord'])?trim($_POST['passWord']):"";
		$expire=isset($_POST['expire'])?intval($_POST['expire']):"";
		if ($username && $password)
		{
			if (wap_user_login($username,$password))
			{	
				//��ͷ����ѵ��Ա��¼
				if($_SESSION['utype']!=1 && $_SESSION['utype']!=2)
				{
					//����session��cookie
					unset($_SESSION['uid']);
					unset($_SESSION['username']);
					unset($_SESSION['utype']);
					setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
					setcookie("QS[username]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
					setcookie("QS[password]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
					setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
					exit("3");
				}
				if(!empty($_SESSION['url']) && !strpos($_SESSION['url'],'user_get_pass')){
					$url  = $_SESSION['url'];
					unset($_SESSION['url']);
					exit($url);
				}
				if($_SESSION['utype']==2)	exit("personal/user.php");
				if($_SESSION['utype']==1)	exit("company/user.php");
			}
			else
			{
				exit("2");
			}		
		}
	}
}
//�����ֻ���֤��
elseif ($act == 'login_send_code')
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
//�ֻ��ŵ�¼
elseif ($act == 'phone_login')
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
		exit("1");
	}
	//��¼
	if(user_login_new($mobile,3))
	{
		if(!empty($_SESSION['url']) && !strpos($_SESSION['url'],'user_get_pass')){
			$url  = $_SESSION['url'];
			unset($_SESSION['url']);
			exit($url);
		}
		if($_SESSION['utype']==2)	exit("personal/user.php");
		if($_SESSION['utype']==1)	exit("company/user.php");
	}
	else
	{
		exit("2");
	}
}
elseif($act == 'waiting_weixin_login'){
	$event_key = $_SESSION['scene_id'];
	$content = "";
	if(file_exists(QISHI_ROOT_PATH."data/weixin/".($event_key%10).'/'.$event_key.".txt")){
		$content = file_get_contents(QISHI_ROOT_PATH."data/weixin/".($event_key%10).'/'.$event_key.".txt");
	}	
	$uid = intval($content);
	if($uid>0){
		global $QS_cookiepath,$QS_cookiedomain;
		$u=get_user_by_uid($uid);
		if (!empty($u))
		{
			unset($_SESSION['uid']);
			unset($_SESSION['username']);
			unset($_SESSION['utype']);
			unset($_SESSION['uqqid']);
			setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
			setcookie("QS[username]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
			setcookie("QS[password]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
			setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
			unset($_SESSION['activate_username']);
			unset($_SESSION['activate_email']);
			
			$_SESSION['uid']=$u['uid'];
			$_SESSION['username']=$u['username'];
			$_SESSION['utype']=$u['utype'];
			$_SESSION['uqqid']="1";
			setcookie('QS[uid]',$u['uid'],0,$QS_cookiepath,$QS_cookiedomain);
			setcookie('QS[username]',$u['username'],0,$QS_cookiepath,$QS_cookiedomain);
			setcookie('QS[password]',$u['password'],0,$QS_cookiepath,$QS_cookiedomain);
			setcookie('QS[utype]',$u['utype'], 0,$QS_cookiepath,$QS_cookiedomain);
			unlink(QISHI_ROOT_PATH."data/weixin/".($event_key%10).'/'.$event_key.".txt");
		}
		exit("1");
	}
}
elseif($act == 'weixin_reg'){
	$utype = intval($_GET['utype']);
	$weixin_openid = $_GET['openid'];
	$event_key = $_GET['event_key'];
	if(file_exists(QISHI_ROOT_PATH."data/weixin/".($event_key%10).'/'.$event_key.".txt")){
		$access_token = get_access_token();
		$w_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$weixin_openid."&lang=zh_CN";
		$w_result = https_request($w_url);
		$w_userinfo = json_decode($w_result,true);
		$w_userinfo = array_map('utf8_to_gbk', $w_userinfo);
		// ΢��ɨ��ע���û�������   ǰ׺+����ַ�
		$username = $_CFG['third_reg_prefix'].randusername();
		// ΢��ɨ��ע���û���������  1->���û�����ͬ       2->�������      3->ָ������   
		if($_CFG['reg_weixin_password_tpye'] == "1")
		{
			$password = $username;
		}
		elseif($_CFG['reg_weixin_password_tpye'] == "2")
		{
			$password=randusername();
		}
		else
		{
			$password=$_CFG['reg_weixin_password'];
		}
		$insert_id = user_register(3,$password,$utype,"","",false,$username,$weixin_openid,$w_userinfo['nickname']);
		if($insert_id>0){
			$smarty->assign('utype_cn',$utype==1?'��ҵ��Ա':'���˻�Ա');
			$smarty->assign('username',$username);
			$smarty->assign('password',$password);
			weixin_login($weixin_openid,$insert_id,$event_key);
			$smarty->display('m/scan/scan_reg_success.html');
		}
		else{
			exit('err');
		}
	}else{
		exit('err');
	}
}
function weixin_login($openid,$uid,$event_key){
	global $QS_cookiepath,$QS_cookiedomain,$_CFG;
	$u=get_user_by_weixinopenid($openid,$uid);
	if (!empty($u))
	{
		if(file_exists(QISHI_ROOT_PATH."data/weixin/".($event_key%10).'/'.$event_key.".txt")){
			ini_set('session.save_handler', 'files');
			session_save_path('/data/tmp/session/');
			session_start();
			$fp = @fopen(QISHI_ROOT_PATH . 'data/weixin/'.($event_key%10).'/'.$event_key.'.txt', 'wb+');
			@fwrite($fp, $uid);
			@fclose($fp);
			$find = array("http://","/wap");
			$replace = array("");
			$QS_cookiedomain = str_replace($find,$replace,$_CFG['wap_domain']);
			unset($_SESSION['uid']);
			unset($_SESSION['username']);
			unset($_SESSION['utype']);
			unset($_SESSION['uqqid']);
			setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
			setcookie("QS[username]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
			setcookie("QS[password]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
			setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
			unset($_SESSION['activate_username']);
			unset($_SESSION['activate_email']);
			
			$_SESSION['uid']=$u['uid'];
			$_SESSION['username']=$u['username'];
			$_SESSION['utype']=$u['utype'];
			$_SESSION['uqqid']="1";
			setcookie('QS[uid]',$u['uid'],0,$QS_cookiepath,$QS_cookiedomain);
			setcookie('QS[username]',$u['username'],0,$QS_cookiepath,$QS_cookiedomain);
			setcookie('QS[password]',$u['password'],0,$QS_cookiepath,$QS_cookiedomain);
			setcookie('QS[utype]',$u['utype'], 0,$QS_cookiepath,$QS_cookiedomain);
		}
	}
}
function get_user_by_weixinopenid($openid,$uid){
	global $db;
	$usinfo = $db->getone("select * from ".table('members')." where weixin_openid='".$openid."' and uid='".$uid."'");
	return $usinfo;
}
function get_user_by_uid($uid){
	global $db;
	$usinfo = $db->getone("select * from ".table('members')." where uid='".$uid."'");
	return $usinfo;
}
unset($smarty);
?>