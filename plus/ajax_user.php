<?php
 /*
 * ajax����
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
if($act =='do_login')
{
	$username=isset($_POST['username'])?trim($_POST['username']):"";
	$password=isset($_POST['password'])?trim($_POST['password']):"";
	$expire=isset($_POST['expire'])?intval($_POST['expire']):"";
	$index_login=isset($_POST['index_login'])?intval($_POST['index_login']):0;//��ǵ�¼��Դ����ҳ��Ҫ��֤��
	$account_type=1;
	if (preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$username))
	{
	$account_type=2;
	}
	elseif (preg_match("/^(13|14|15|18|17)\d{9}$/",$username))
	{
	$account_type=3;
	}
	
	$url=isset($_POST['url'])?$_POST['url']:"";
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$username=utf8_to_gbk($username);
	$password=utf8_to_gbk($password);
	}
	if($account_type==1){
		if(!preg_match("/^[".chr(0xa1)."-".chr(0xff)."a-zA-Z0-9_]{3,18}$/", $username)){
			exit("err");
		}
	}
	$captcha=get_cache('captcha');
	if ($captcha['verify_userlogin']=="1" && $index_login!="1")
	{
		$postcaptcha=$_POST['postcaptcha'];
		if ($captcha['captcha_lang']=="cn" && strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
		{
		$postcaptcha=utf8_to_gbk($postcaptcha);
		}
		if (empty($postcaptcha) || empty($_SESSION['imageCaptcha_content']) || strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
		{
		unset($_SESSION['imageCaptcha_content']);
		exit("errcaptcha");
		}
	}
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	if ($username && $password)
	{
		$login=user_login($username,$password,$account_type,true,$expire);
		$url=$url?$url:$login['qs_login'];
		if ($login['qs_login'])
		{
			if($login['qs_login'] == 'false'){
				exit('status_err');
			}else{
				if(intval($_POST['binding']) == 1 && trim($_POST['openid'])<>'' && $_SESSION['uid']<>'' && trim($_POST['nickname'])<>''){
					$time = time();
					$nickname = utf8_to_gbk($_POST['nickname']);
					switch($_POST['bindtype']){
						case 'qq':
							$db->query("UPDATE ".table('members')." SET qq_openid = '{$_SESSION[openid]}', qq_nick = '{$nickname}', qq_binding_time = '{$time}' WHERE uid='".$_SESSION['uid']."' AND qq_openid='' LIMIT 1");
							break;
						case 'taobao':
							$db->query("UPDATE ".table('members')." SET taobao_access_token = '{$_SESSION[taobao_access_token]}', taobao_nick = '{$nickname}', taobao_binding_time = '{$time}' WHERE uid='".$_SESSION['uid']."' AND taobao_access_token='' LIMIT 1");
							break;
						case 'sina':
							$db->query("UPDATE ".table('members')." SET sina_access_token = '{$_SESSION[sina_access_token]}', sina_nick = '{$nickname}', sina_binding_time = '{$time}' WHERE uid='".$_SESSION['uid']."' AND sina_access_token='' LIMIT 1");
							break;
					}
					
				}
				exit($login['uc_login']."<script language=\"javascript\" type=\"text/javascript\">window.location.href=\"".$url."\";</script>");
			} 
		}
		else
		{
		exit("err");
		}
	}
	exit("err");
}
elseif ($act=='do_reg')
{
	$captcha=get_cache('captcha');
	if ($captcha['verify_userreg']=="1")
	{
		$postcaptcha=$_POST['postcaptcha'];
		if ($captcha['captcha_lang']=="cn" && strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
		{
		$postcaptcha=utf8_to_gbk($postcaptcha);
		}
		if (empty($postcaptcha) || empty($_SESSION['imageCaptcha_content']) || strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
		{
		exit("err");
		}
	}
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$username = isset($_POST['username'])?trim($_POST['username']):exit("err");
	$password = isset($_POST['password'])?trim($_POST['password']):exit("err");
	$member_type = isset($_POST['member_type'])?intval($_POST['member_type']):exit("err");
	$email = isset($_POST['email'])?trim($_POST['email']):exit("err");
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$username=utf8_to_gbk($username);
	$password=utf8_to_gbk($password);
	}
		if(defined('UC_API'))
		{
			include_once(QISHI_ROOT_PATH.'uc_client/client.php');
			if (uc_user_checkname($username)<0)
			{
			exit("err");
			}
			if (uc_user_checkemail($email)<0)
			{
			exit("err");
			}
		}
	$register=user_register($username,$password,$member_type,$email);
	if ($register>0)
	{	
		$login_js=user_login($username,$password);
		$mailconfig=get_cache('mailconfig');
		if ($mailconfig['set_reg']=="1" && !empty($email))
		{
		dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&sendemail=".$email."&sendusername=".$username."&sendpassword=".$password."&act=reg");
		}
		$ucjs=$login_js['uc_login'];
		$qsurl=$login_js['qs_login'];
		$qsjs="<script language=\"javascript\" type=\"text/javascript\">window.location.href=\"".$qsurl."\";</script>";
		 if ($ucjs || $qsurl)
			{
			exit($ucjs.$qsjs);
			}
			else
			{
			exit("err");
			}
	}
	else
	{
	exit("err");
	}
}
elseif($act =='check_usname')
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$usname=trim($_POST['usname']);
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$usname=utf8_to_gbk($usname);
	}
	if(!preg_match("/^[".chr(0xa1)."-".chr(0xff)."a-za-z0-9_]{3,18}$/", $usname)){
		exit("false");
	}
	$user=get_user_inusername($usname);
	if (defined('UC_API'))
	{
		include_once(QISHI_ROOT_PATH.'uc_client/client.php');
		if (uc_user_checkname($usname)===1 && empty($user))
		{
		exit("true");
		}
		else
		{
		exit("false");
		}
	}
	empty($user)?exit("true"):exit("false");
}
elseif($act == 'check_email')
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$email=trim($_POST['email']);
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$email=utf8_to_gbk($email);
	}
	if(!preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$email)){
		exit("false");
	}
	$user=get_user_inemail($email);
	if (defined('UC_API'))
	{
		include_once(QISHI_ROOT_PATH.'uc_client/client.php');
		if (uc_user_checkemail($email)===1 && empty($user))
		{
		exit("true");
		}
		else
		{
		exit("false");
		}
	}
	empty($user)?exit("true"):exit("false");
}
elseif($act == "check_mobile")
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$mobile=trim($_POST['mobile']);
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$mobile=utf8_to_gbk($mobile);
	}
	if (!preg_match("/^(13|14|15|18|17)\d{9}$/",$mobile)){
		exit("false");
	}
	$user=get_user_inmobile($mobile);
	empty($user)?exit("true"):exit("false");
}
elseif ($act=="top_loginform")
{
	$block = isset($_GET['block'])?iconv('utf-8',QISHI_CHARSET,$_GET['block']):'';
	$contents='';
	if ($_COOKIE['QS']['username'] && $_COOKIE['QS']['password'])
	{
		// $contents='��ӭ&nbsp;&nbsp;<a href="{#$user_url#}" style="color:#339900">{#$username#}</a> ��¼��&nbsp;&nbsp;{#$pmscount_a#}&nbsp;&nbsp;&nbsp;&nbsp;<a href="{#$user_url#}" style="color:#0066cc">[��Ա����]</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{#$logout_url#}" style="color:#0066cc">[�˳�]</a>';
		$contents='<span class="login-reg"><a href="{#$user_url#}" class="underline">{#$username#}</a>&nbsp;&nbsp;&nbsp;<a href="{#$logout_url#}" class="underline">[�˳�]</a></span>';
		
	}
	elseif ($_SESSION['activate_username'] && defined('UC_API'))
	{
		$contents='<span class="login-reg">�����ʺ�{#$activate_username#}�輤���ſ���ʹ�ã� &nbsp;&nbsp;&nbsp;<a href="{#$activate_url#}" class="underline">[��������]</a></span>';
	}
	else
	{	
		if (isset($_GET['block'])) {
			$contents='���ã���ӭ����{#$site_name#}'.$block.'��';
		} else {
			$contents='<span class="login-reg"><a href="{#$login_url#}" class="underline">��¼</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{#$reg_url#}" class="underline">ע��</a></span>';
		}
	}
		$contents=str_replace('{#$activate_username#}',$_SESSION['activate_username'],$contents);
		$contents=str_replace('{#$site_name#}',$_CFG['site_name'],$contents);
		$contents=str_replace('{#$username#}',$_COOKIE['QS']['username'],$contents);
		$contents=str_replace('{#$pmscount#}',$_COOKIE['QS']['pmscount'],$contents);
		$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
		if ($_COOKIE['QS']['utype']=='1')
		{
		$user_url=$_CFG['site_dir']."user/company/company_index.php";
			if($_COOKIE['QS']['pmscount']>0)
			 {
			 $pmscount_a='<a href="'.$_CFG['site_dir'].'user/company/company_user.php?act=pm&new=1" style="padding:1px 4px; background-color:#FF6600; color:#FFFFFF;text-decoration:none" title="����Ϣ">��Ϣ '.$_COOKIE['QS']['pmscount'].'</a>';
			 }
		}
		if ($_COOKIE['QS']['utype']=='2')
		{
			$user_url=$_CFG['site_dir']."user/personal/personal_index.php";
			if($_COOKIE['QS']['pmscount']>0)
			 {
			 $pmscount_a='<a href="'.$_CFG['site_dir'].'user/personal/personal_user.php?act=pm&new=1" style="padding:1px 4px; background-color:#FF6600; color:#FFFFFF;text-decoration:none" title="����Ϣ">��Ϣ '.$_COOKIE['QS']['pmscount'].'</a>';
			 }
		}
		if ($_COOKIE['QS']['utype']=='4')
		{
			$user_url=$_CFG['site_dir']."user/train/train_index.php";
			if($_COOKIE['QS']['pmscount']>0)
			 {
			 $pmscount_a='<a href="'.$_CFG['site_dir'].'user/train/train_user.php?act=pm&new=1" style="padding:1px 4px; background-color:#FF6600; color:#FFFFFF;text-decoration:none" title="����Ϣ">��Ϣ '.$_COOKIE['QS']['pmscount'].'</a>';
			 }
		}
		if ($_COOKIE['QS']['utype']=='3')
		{
			$user_url=$_CFG['site_dir']."user/hunter/hunter_index.php";
			if($_COOKIE['QS']['pmscount']>0)
			 {
			 $pmscount_a='<a href="'.$_CFG['site_dir'].'user/hunter/hunter_user.php?act=pm&new=1" style="padding:1px 4px; background-color:#FF6600; color:#FFFFFF;text-decoration:none" title="����Ϣ">��Ϣ '.$_COOKIE['QS']['pmscount'].'</a>';
			 }
		}
		$contents=str_replace('{#$pmscount_a#}',$pmscount_a,$contents);
		$contents=str_replace('{#$user_url#}',$user_url,$contents);
		$contents=str_replace('{#$login_url#}',url_rewrite('QS_login'),$contents);
		$contents=str_replace('{#$logout_url#}',url_rewrite('QS_login')."?act=logout",$contents);
		$contents=str_replace('{#$reg_url#}',$_CFG['site_dir']."user/user_reg.php",$contents);
		$contents=str_replace('{#$activate_url#}',$_CFG['site_dir']."user/user_reg.php?act=activate",$contents);
		exit($contents);
}
elseif ($act=="loginform")
{		
		$contents='';
		if ($_COOKIE['QS']['username'] && $_COOKIE['QS']['password'])
		{
			$tpl='../templates/'.$_CFG['template_dir']."plus/login_success.htm";
		}
		elseif ($_SESSION['activate_username'] && defined('UC_API'))
		{
			$tpl='../templates/'.$_CFG['template_dir']."plus/login_activate.htm";
		}
		else
		{
			$tpl='../templates/'.$_CFG['template_dir']."plus/login_form.htm";
		}
		$contents=file_get_contents($tpl);
		$contents=str_replace('{#$site_name#}',$_CFG['site_name'],$contents);
		if($_CFG['qq_apiopen']==1)
		{
			if($_CFG['qq_logintype']==1)
			{

				$contents=str_replace('{#$qq_apiopen#}','<a href="'.$_CFG['site_dir'].'user/connect_qq_client.php" class="third-icon qq f-left"></a>',$contents);
			}
			else
			{
				$contents=str_replace('{#$qq_apiopen#}','<a href="'.$_CFG['site_dir'].'user/connect_qq_server.php" class="third-icon qq f-left"></a>',$contents);
			}
		}
		else
		{
			$contents=str_replace('{#$qq_apiopen#}','',$contents);
		}
		if($_CFG['sina_apiopen']==1)
		{
			$contents=str_replace('{#$sina_apiopen#}','<a href="'.$_CFG['site_dir'].'user/connect_sina.php" class="third-icon sina f-left"></a>',$contents);
		}
		else
		{
			$contents=str_replace('{#$sina_apiopen#}','',$contents);
		}
		if($_CFG['taobao_apiopen']==1)
		{
			$contents=str_replace('{#$taobao_apiopen#}','<a href="'.$_CFG['site_dir'].'user/connect_taobao.php" class="third-icon taobao f-left"></a>',$contents);
		}
		else
		{
			$contents=str_replace('{#$taobao_apiopen#}','',$contents);
		}
		if ($_CFG['qq_apiopen']==1 || $_CFG['sina_apiopen']==1 || $_CFG['taobao_apiopen']==1)
		{
			
			$contents=str_replace('{#$third_tit#}',"�����˻���¼��",$contents);
		}
		else
		{
			$contents=str_replace('{#$third_tit#}',"",$contents);
		}
		$contents=str_replace('{#$site_name#}',$_CFG['site_name'],$contents);
		$contents=str_replace('{#$username#}',$_COOKIE['QS']['username'],$contents);
		$contents=str_replace('{#$pmscount#}',$_COOKIE['QS']['pmscount'],$contents);
		$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
		$contents=str_replace('{#$site_dir#}',$_CFG['site_dir'],$contents);
		$contents=str_replace('{#$site_tel#}',$_CFG['top_tel'],$contents);
		if($_CFG['weixin_apiopen']=='1' && $_CFG['weixin_scan_login']=='1'){
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
		    $img_html = '<img width="120" height="120" src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket.'">';
			$weixin_html = '<div id="codeLogin" style="display:none;"><div class="code-login" id="login_container">'.$img_html.'</div><p>��΢��ɨ���ά��</p></div>';
			$weixin_html_header='<div class="wechat-login"><a href="javascript:;" class="loginicon wx"></a></div>';
		}else{
			$weixin_html_header='';
			$weixin_html = '';
		}
		$contents=str_replace('{#$weixin_html_header#}',$weixin_html_header,$contents);
		$contents=str_replace('{#$weixin_html#}',$weixin_html,$contents);
		// ��һ�ε�¼ʱ��
		if($_COOKIE['QS']['username'])
		{
			if($_SESSION['no_self'])
			{
				$log=$db->getone("SELECT * FROM ".table('members_log')." WHERE log_uid=".$_COOKIE['QS']['uid']." AND log_type='1001' ORDER BY log_id DESC LIMIT 0,1");
				if(!empty($log))
				{
					$last_login_time='�ϴε�¼��'.date("Y-m-d H:i",$log['log_addtime']);
				}
				else
				{
					$log=$db->getone("SELECT reg_time from ".table("members")." where uid=".$_COOKIE['QS']['uid']."");
					$last_login_time='ע��ʱ�䣺'.date("Y-m-d H:i",$log['reg_time']);
				}
				
			}
			else
			{
				$log=$db->getone("SELECT * FROM ".table('members_log')." WHERE log_uid=".$_COOKIE['QS']['uid']." AND log_type='1001' ORDER BY log_id DESC LIMIT 1,1");
				if(!empty($log))
				{
					$last_login_time='�ϴε�¼��'.date("Y-m-d H:i",$log['log_addtime']);
				}
				else
				{
					$log=$db->getone("SELECT reg_time from ".table("members")." where uid=".$_COOKIE['QS']['uid']."");
					$last_login_time='ע��ʱ�䣺'.date("Y-m-d H:i",$log['reg_time']);
				}
			}
		}

		$contents=str_replace('{#$last_login_time#}',$last_login_time,$contents);
		if ($_COOKIE['QS']['utype']=='1' && $_COOKIE['QS']['utype']=='1')
		{
			$user_url=$_CFG['site_dir']."user/company/company_index.php";
			$interview=$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_interview')." as i  INNER JOIN ".table('resume')." as r ON i.resume_id=r.id   WHERE i.company_uid='{$_COOKIE['QS']['uid']}'");
			$count_receivedresume=$db->get_total("SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply')." AS a INNER JOIN  ".table('resume')." AS r  ON  a.resume_id=r.id  WHERE a.company_uid='{$_COOKIE['QS']['uid']}'");
			$count_favorites=$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_favorites')." AS f INNER JOIN  ".table('resume')." AS r ON  f.resume_id=r.id   WHERE f.company_uid='{$_COOKIE['QS']['uid']}'");

			$count_htm='<div code="'.$_CFG['site_dir'].'user/company/company_recruitment.php?act=interview_list" class="core-function f-left">
					<div><a href="'.$_CFG['site_dir'].'user/company/company_recruitment.php?act=interview_list">'.intval($interview).'</a></div>
					<p><a href="'.$_CFG['site_dir'].'user/company/company_recruitment.php?act=interview_list">��������</a></p>
				</div>
				<div code="'.$_CFG['site_dir'].'user/company/company_recruitment.php?act=apply_jobs" class="core-function f-left">
					<div><a href="'.$_CFG['site_dir'].'user/company/company_recruitment.php?act=apply_jobs">'.intval($count_receivedresume).'</a></div>
					<p><a href="'.$_CFG['site_dir'].'user/company/company_recruitment.php?act=apply_jobs">�յ��ļ���</a></p>
				</div>
				<div code="'.$_CFG['site_dir'].'user/company/company_recruitment.php?act=favorites_list" class="core-function last f-left">
					<div><a href="'.$_CFG['site_dir'].'user/company/company_recruitment.php?act=favorites_list">'.intval($count_favorites).'</a></div>
					<p><a href="'.$_CFG['site_dir'].'user/company/company_recruitment.php?act=favorites_list">�ղؼ�</a></p>
				</div>';
			$contents=str_replace('{#$count#}',$count_htm,$contents);

		}
		if ($_COOKIE['QS']['utype']=='2' && $_COOKIE['QS']['utype']=='2')
		{
			$user_url=$_CFG['site_dir']."user/personal/personal_index.php";
			$interview=$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_interview')." as i WHERE i.resume_uid='{$_COOKIE['QS']['uid']}'");

			$count_apply=$db->get_total("SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply')." AS a  WHERE a.personal_uid='{$_COOKIE['QS']['uid']}'");
			$fav_num = $db->get_total("SELECT COUNT(*) AS num FROM ".table('personal_favorites')." WHERE personal_uid='{$_COOKIE['QS']['uid']}' ");

			$count_htm='<div class="core-function f-left">
					<div><a href="'.$_CFG['site_dir'].'user/personal/personal_apply.php?act=interview">'.intval($interview).'</a></div>
					<p><a href="'.$_CFG['site_dir'].'user/personal/personal_apply.php?act=interview">��������</a></p>
				</div>
				<div class="core-function f-left">
					<div><a href="'.$_CFG['site_dir'].'user/personal/personal_apply.php?act=apply_jobs">'.intval($count_apply).'</a></div>
					<p><a href="'.$_CFG['site_dir'].'user/personal/personal_apply.php?act=apply_jobs">����ְλ</a></p>
				</div>
				<div class="core-function last f-left">
					<div><a href="'.$_CFG['site_dir'].'user/personal/personal_apply.php?act=favorites">'.intval($fav_num).'</a></div>
					<p><a href="'.$_CFG['site_dir'].'user/personal/personal_apply.php?act=favorites">�ղؼ�</a></p>
				</div>';
			$contents=str_replace('{#$count#}',$count_htm,$contents);
		}
		if ($_COOKIE['QS']['utype']=='4' && $_COOKIE['QS']['utype']=='4')
		{
			$user_url=$_CFG['site_dir']."user/train/train_index.php";
			$course=$db->get_total("SELECT COUNT(*) AS num FROM ".table('course'). " WHERE uid={$_COOKIE['QS']['uid']}");
			$apply_course=$db->get_total("SELECT COUNT(*) AS num FROM ".table('personal_course_apply')."  WHERE train_uid={$_COOKIE['QS']['uid']}");
			$teachers=$db->get_total("SELECT COUNT(*) AS num FROM ".table('train_teachers')." WHERE uid={$_COOKIE['QS']['uid']} ");
			$count_htm='<div class="core-function f-left">
					<div><a href="'.$_CFG['site_dir'].'user/train/train_course.php?act=course">'.intval($course).'</a></div>
					<p><a href="'.$_CFG['site_dir'].'user/train/train_course.php?act=course">�γ̹���</a></p>
				</div>
				<div class="core-function f-left">
					<div><a href="'.$_CFG['site_dir'].'user/train/train_recruitment.php?act=apply_course">'.intval($apply_course).'</a></div>
					<p><a href="'.$_CFG['site_dir'].'user/train/train_recruitment.php?act=apply_course">�γ�����</a></p>
				</div>
				<div class="core-function last f-left">
					<div><a href="'.$_CFG['site_dir'].'user/train/train_teacher.php?act=teachers">'.intval($teachers).'</a></div>
					<p><a href="'.$_CFG['site_dir'].'user/train/train_teacher.php?act=teachers">��ʦ����</a></p>
				</div>';
			$contents=str_replace('{#$count#}',$count_htm,$contents);
		}
		if ($_COOKIE['QS']['utype']=='3' && $_COOKIE['QS']['utype']=='3')
		{
			$user_url=$_CFG['site_dir']."user/hunter/hunter_index.php";
			$interview=$db->get_total("SELECT COUNT(*) AS num FROM  ".table('hunter_interview')."  as i  WHERE i.hunter_uid={$_COOKIE['QS']['uid']}");
			$count_apply=$db->get_total("SELECT COUNT(*) AS num FROM ".table('personal_hunter_jobs_apply')." AS a   WHERE a.huntet_uid='{$_COOKIE['QS']['uid']}'");
			$dow_num=$db->get_total("SELECT COUNT(*) AS num FROM ".table('hunter_down_resume')." as d WHERE  d.hunter_uid='{$_COOKIE['QS']['uid']}'");

			$count_htm='<div class="core-function f-left">
					<div><a href="'.$_CFG['site_dir'].'user/hunter/hunter_recruitment.php?act=interview">'.intval($interview).'</a></div>
					<p><a href="'.$_CFG['site_dir'].'user/hunter/hunter_recruitment.php?act=interview">��������</a></p>
				</div>
				<div class="core-function f-left">
					<div><a href="'.$_CFG['site_dir'].'user/hunter/hunter_recruitment.php?act=apply_jobs">'.intval($count_apply).'</a></div>
					<p><a href="'.$_CFG['site_dir'].'user/hunter/hunter_recruitment.php?act=apply_jobs">�յ�����</a></p>
				</div>
				<div class="core-function last f-left">
					<div><a href="'.$_CFG['site_dir'].'user/hunter/hunter_recruitment.php?act=down_resume_list">'.intval($dow_num).'</a></div>
					<p><a href="'.$_CFG['site_dir'].'user/hunter/hunter_recruitment.php?act=down_resume_list">���ؼ���</a></p>
				</div>';
			$contents=str_replace('{#$count#}',$count_htm,$contents);
		}
		$contents=str_replace('{#$pmscount_a#}',$pmscount_a,$contents);
		$contents=str_replace('{#$user_url#}',$user_url,$contents);
		$contents=str_replace('{#$login_url#}',url_rewrite('QS_login'),$contents);
		$contents=str_replace('{#$logout_url#}',url_rewrite('QS_login')."?act=logout",$contents);
		$contents=str_replace('{#$reg_url#}',$_CFG['site_dir']."user/user_reg.php",$contents);
		$contents=str_replace('{#$activate_url#}',$_CFG['site_dir']."user/user_reg.php?act=activate",$contents);
		exit($contents);
}
elseif ($act=="test_loginform")
{		
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$contents='';
	if ($_COOKIE['QS']['username'] && $_COOKIE['QS']['password'] && $_COOKIE['QS']['uid'] && $_COOKIE['QS']['utype']=='2')
	{
		$tpl='../templates/'.$_CFG['template_dir']."plus/login_success_eval.htm";
	}
	else
	{
		$tpl='../templates/'.$_CFG['template_dir']."plus/login_form_eval.htm";
	}
	$contents=file_get_contents($tpl);
	$contents=str_replace('{#$site_name#}',$_CFG['site_name'],$contents);
	if($_CFG['qq_apiopen']==1)
	{
		if($_CFG['qq_logintype']==1)
		{

			$contents=str_replace('{#$qq_apiopen#}','<a href="'.$_CFG['site_dir'].'user/connect_qq_client.php" class="other-icon qq f-left"></a>',$contents);
		}
		else
		{
			$contents=str_replace('{#$qq_apiopen#}','<a href="'.$_CFG['site_dir'].'user/connect_qq_server.php" class="other-icon qq f-left"></a>',$contents);
		}
	}
	else
	{
		$contents=str_replace('{#$qq_apiopen#}','',$contents);
	}
	if($_CFG['sina_apiopen']==1)
	{
		$contents=str_replace('{#$sina_apiopen#}','<a href="'.$_CFG['site_dir'].'user/connect_sina.php" class="other-icon sina f-left"></a>',$contents);
	}
	else
	{
		$contents=str_replace('{#$sina_apiopen#}','',$contents);
	}
	if ($_CFG['qq_apiopen']==1 || $_CFG['sina_apiopen']==1 || $_CFG['taobao_apiopen']==1)
	{
		
		$contents=str_replace('{#$third_tit#}',"�����˻���¼��",$contents);
	}
	else
	{
		$contents=str_replace('{#$third_tit#}',"",$contents);
	}
	$contents=str_replace('{#$site_name#}',$_CFG['site_name'],$contents);
	$contents=str_replace('{#$username#}',cut_str($_COOKIE['QS']['username'],8,0,"..."),$contents);
	$contents=str_replace('{#$username_#}',$_COOKIE['QS']['username'],$contents);
	$contents=str_replace('{#$pmscount#}',$_COOKIE['QS']['pmscount'],$contents);
	$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
	$contents=str_replace('{#$site_dir#}',$_CFG['site_dir'],$contents);
	$contents=str_replace('{#$site_tel#}',$_CFG['top_tel'],$contents);
	if ($_COOKIE['QS']['utype']=='2' && $_COOKIE['QS']['utype']=='2')
	{
		$user_url=$_CFG['site_dir']."user/personal/personal_index.php";
		//�û�Ա��Ϣ
		$user_info = get_user_inid($_COOKIE['QS']['uid']);
		if(!empty($user_info['avatars']))
		{
			$avatars = $_CFG['site_dir']."data/avatar/100/".$user_info['avatars'];
		}
		else
		{
			$avatars = $_CFG['site_template']."images/06.jpg";
		}
		$my_eval = url_rewrite('QS_my_evaluation');
		$contents=str_replace('{#$count#}',$my_eval,$contents);
		//�û�Ա��«��
		require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
		$user_points = get_user_points($_SESSION['uid']);
		$contents=str_replace('{#$user_points#}',$user_points,$contents);
		$contents=str_replace('{#$avatars#}',$avatars,$contents);
	}
	$contents=str_replace('{#$user_url#}',$user_url,$contents);
	$contents=str_replace('{#$login_url#}',url_rewrite('QS_login'),$contents);
	$contents=str_replace('{#$logout_url#}',url_rewrite('QS_login')."?act=logout",$contents);
	$contents=str_replace('{#$reg_url#}',$_CFG['site_dir']."user/user_reg.php",$contents);
	exit($contents);
}
// ע�ᷢ�Ͷ���/�һ����� ����
elseif($act == "reg_send_sms")
{
	$sms_type=$_POST['sms_type']?$_POST['sms_type']:exit('�Ƿ�������');
	if(!empty($sms_type) && sms_check_token())
	{
		require_once(QISHI_ROOT_PATH.'include/fun_user.php');
		$uid=intval($_POST['uid']);
		if($uid > 0)
		{
			$usinfo=get_user_inid($uid);
			if(!$usinfo)
			{
				exit("��������");  
			}
			$mobile=$usinfo['mobile'];
		}
		else
		{
			$mobile=trim($_POST['mobile']);
		}
		if (empty($mobile) || !preg_match("/^(13|15|14|17|18)\d{9}$/",$mobile))
		{
			exit("�ֻ��Ŵ���");
		}
		$rand=mt_rand(100000, 999999);	
		switch ($sms_type) {
			case 'reg':
				$sms_str="������ע��{$_CFG['site_name']}�Ļ�Ա,�ֻ���֤��Ϊ:{$rand},����֤����Ч��Ϊ10����";
				break;
			case 'getpass':
				$sms_str="�������һ�{$_CFG['site_name']}�Ļ�Ա����,�ֻ���֤��Ϊ:{$rand},����֤����Ч��Ϊ10����";
				break;
		}
		if($_SESSION['verify_mobile']==$mobile && time()<$_SESSION['send_time']+180)
		{
			exit("180���ڽ��ܻ�ȡһ�ζ�����֤��,���Ժ�����");
		}
		else
		{
			$r=send_sms($mobile,$sms_str);
		}
		if ($r=="success")
		{
			$_SESSION['mobile_rand']=substr(md5($rand), 8,16);
			$_SESSION['send_time']=time();
			$_SESSION['verify_mobile']=$mobile;
			exit("success");
		}
		else
		{
			exit("SMS���ó�������ϵ��վ����Ա");
		}
	}
	else
	{
		exit("�Ƿ�������");
	}
}
// ��֤ע�����
elseif($act == "check_reg_send_sms")
{
	$mobile=trim($_POST['mobile']);
	$time=time();
	if (empty($mobile) || !preg_match("/^(13|15|14|18|17)\d{9}$/",$mobile))
	{
	exit("false");//�ֻ��Ŵ���
	}
	if($mobile!=$_SESSION['verify_mobile'])
	{
	exit("false");//�ֻ��Ų�һ��
	}
	if($time>$_SESSION['send_time']+600)
	{
	exit("false");//��֤�����
	}
	$vcode_sms=intval($_POST['mobile_vcode']);
	$mobile_rand=substr(md5($vcode_sms), 8,16);
	if($mobile_rand!=$_SESSION['mobile_rand']){exit("false");}//��֤�����
	exit("true");
}
// �һ�������֤ �û������� �ֻ�
elseif($act == "get_pass_check")
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$username=$_POST['username']?iconv("utf-8", "gbk", trim($_POST['username'])):exit("false");
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
	!empty($usinfo)?exit("true"):exit("false");
}
// �һ����� �ж��Ƿ� ���ֻ� ���� ΢��
elseif($act == "get_pass_check_buding")
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$username=$_POST['username']?iconv("utf-8", "gbk", trim($_POST['username'])):exit("");
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
	if(($usinfo['mobile'] && $usinfo['mobile_audit']==1) || ($usinfo['email'] && $usinfo['email_audit']==1))
	{
		exit("true");
	}
	elseif($usinfo['weixin_openid'])
	{
		exit('buding_wx');
	}
	else
	{
		exit('false');
	}
}
// �һ����뷢���ʼ�
elseif($act == "getpass_sendemail")
{
	global $QS_pwdhash;
	$email=$_POST['email']?trim($_POST['email']):exit("�������");
	$username=$_POST['username']?iconv("utf-8", "gbk", trim($_POST['username'])):exit("û���û���");
	$uid=$_POST['uid']?intval($_POST['uid']):exit("û���û���");
	//��֤����������Ϣ
	$members_info = $db->getone("SELECT * FROM ".table('members')." WHERE uid=".$uid." AND username='".$username."' AND email='".$email."'");
	if (!preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$email) || !$members_info)
	{
		exit("�������");
	}
	$time=time();
	$key=substr(md5($username.$QS_pwdhash),8,16);
	$email_str.=$username."���ã�<br>";
	$email_str.="����24Сʱ�ڵ�������������������������룺<br>";
	$email_str.="<a href='".$_CFG['site_domain'].$_CFG['site_dir']."user/user_getpass.php?act=get_pass_step3_email&uid=$uid&key=$key&time=$time' target='_blank'>".$_CFG['site_domain'].$_CFG['site_dir']."user/user_getpass.php?act=get_pass_step3_email&uid=$uid&key=$key&time=$time</a><br>";
	$email_str.="��������޷����,�븴��ճ������������ʣ�<br>";
	$email_str.="���ʼ���ϵͳ����,����ظ���<br>";
	$email_str.="�����κ���������ϵ��վ�ٷ���".$_CFG['top_tel']."";

	if (smtp_mail($email,"{$_CFG['site_name']}-�һ�����",$email_str))
	{
		exit("success");
	}
	else
	{
		exit("�������ó�������ϵ��վ����Ա");
	}
}
//ע�ᷢ���ʼ�
elseif($act == "reg_sendemail")
{
	$email=$_POST['email']?trim($_POST['email']):exit("�������1");
	$utype=$_POST['utype']?intval($_POST['utype']):exit("�������2");
	if (!preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$email))
	{
		exit("�������3");
	}
	$time=time();
	$key=substr(md5($email.$time),8,16);
	$email_str.="����,����24Сʱ�ڵ�������������ע�᣺<br>";
	$email_str.="<a href='".$_CFG['site_domain'].$_CFG['site_dir']."user/user_reg.php?act=reg_step2_email&email=$email&utype=$utype&key=$key&time=$time' target='_blank'>".$_CFG['site_domain'].$_CFG['site_dir']."user/user_reg.php?act=reg_step2_email&email=$email&utype=$utype&key=$key&time=$time</a><br>";
	$email_str.="��������޷����,�븴��ճ������������ʣ�<br>";
	$email_str.="���ʼ���ϵͳ����,����ظ���<br>";
	$email_str.="�����κ���������ϵ��վ�ٷ���".$_CFG['top_tel']."";

	if (smtp_mail($email,"{$_CFG['site_name']} - ��Աע��",$email_str))
	{
		exit("success");
	}
	else
	{
		exit("�������ó�������ϵ��վ����Ա");
	}
}
?>