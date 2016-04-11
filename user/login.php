<?php
 /*
 * 74cms ��Ա��¼
*/
define('IN_QISHI', true);
$alias="QS_login";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$smarty->cache = false;
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'login';
$smarty->assign('header_nav',"login");
//error_reporting(-1);
if($act == 'logout')
{

	//echo $QS_cookiepath;
	//require_once(QISHI_ROOT_PATH.'genv/lib.php');

	setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[username]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[password]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[subsite_id]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);


	unset($_SESSION['uid'],$_SESSION['username'],$_SESSION['utype'],$_SESSION['uqqid'],$_SESSION['activate_username'],$_SESSION['activate_email'],$_SESSION["openid"]);
	
	//ɾ���������ʱ���ɵ�session����ʱcookie�ļ�
	if($_SESSION['cookie_name']){
		@unlink(QISHI_ROOT_PATH.'/data/tmp/'.$_SESSION['cookie_name']);
		unset($_SESSION['cookie_name']);
	}
	if(defined('UC_API'))
	{
		include_once(QISHI_ROOT_PATH.'uc_client/client.php');	
		$logoutjs=uc_user_synlogout();
	}




	$logoutjs.="<script language=\"javascript\" type=\"text/javascript\">window.location.href=\"".url_rewrite('QS_login')."\";</script>";
	exit($logoutjs); 
}
elseif((empty($_SESSION['uid']) || empty($_SESSION['username']) || empty($_SESSION['utype'])) &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'] && $_COOKIE['QS']['uid'])
{
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
	
	header("Location:".url_rewrite('QS_login'));
	}
}
elseif ($_SESSION['username'] && $_SESSION['utype'] &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'])
{
	header("Location:".get_member_url($_SESSION['utype']));
}
elseif ($act=='login')
{
	/**
	 * ΢��ɨ���¼start
	 */
    if(intval($_CFG['weixin_apiopen'])==1){
		$access_token = get_access_token();
	    $scene_id = rand(1,10000000);
	    $_SESSION['scene_id'] = $scene_id;
		$dir = QISHI_ROOT_PATH.'data/weixin/'.($scene_id%10);
		make_dir($dir);
	    $fp = @fopen($dir.'/'.$scene_id.'.txt', 'wb+');
	    $post_data = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$scene_id.'}}}';
	    $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
	    $result = https_request($url, $post_data);
	    $result_arr = json_decode($result,true);
	    $ticket = urlencode($result_arr["ticket"]);
	    $html = '<img width="120" height="120" src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket.'">';
		$smarty->assign('qrcode_img',$html);
	}
    /**
     * ΢��ɨ���¼end
     */
	$smarty->assign('title','��Ա��¼ - '.$_CFG['site_name']);
	$smarty->assign('error',$_GET['error']);
	$smarty->assign('url',$_GET['url']);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userlogin',$captcha['verify_userlogin']);
	$smarty->display($mypage['tpl']);
}
unset($smarty);
?>