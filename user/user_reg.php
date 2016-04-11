<?php
 /*
 * ��Աע��
*/
define('IN_QISHI', true);
$alias="QS_login";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$smarty->cache = false;
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'reg';
$smarty->assign('header_nav',"reg");
$SMSconfig=get_cache('sms_config');
$smarty->assign('SMSconfig',$SMSconfig);
if(!$_SESSION['uid'] && !$_SESSION['username'] && !$_SESSION['utype'] &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'] )
{
	if(check_cookie($_COOKIE['QS']['uid'],$_COOKIE['QS']['username'],$_COOKIE['QS']['password']))
	{
	update_user_info($_COOKIE['QS']['uid'],false,false);
	header("Location:".get_member_url($_SESSION['utype']));
	}
	else
	{
	setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[username]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[password]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	header("Location:".url_rewrite('QS_login'));
	}
}
//�����˻�
elseif ($act=='activate')
{
	if (defined('UC_API')){
				include_once(QISHI_ROOT_PATH.'uc_client/client.php');
				if($data = uc_get_user($_SESSION['activate_username']))
				{
				unset($_SESSION['uid']);
				unset($_SESSION['username']);
				unset($_SESSION['utype']);
				unset($_SESSION['uqqid']);
				setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
				setcookie("QS[username]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
				setcookie("QS[password]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
				setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);		
				$smarty->assign('activate_email',$data[2]);
				$smarty->assign('activate_username',$_SESSION['activate_username']);
				}
				else
				{
				showmsg('����ʧ�ܣ��û�������',0);
				}
				$smarty->display('user/activate.htm');
	}
}
elseif ($act=='activate_save')
{
		$activateinfo=activate_user($_SESSION['activate_username'],$_POST['pwd'],$_POST['act_email'],$_POST['member_type'],$_POST['mobile']);
		if($activateinfo>0)
		{
			$login_url=user_login($_SESSION['activate_username'],$_POST['pwd'],1,false);
			$link[0]['text'] = "�����Ա����";
			$link[0]['href'] = $login_url['qs_login'];
			$link[1]['text'] = "��վ��ҳ";
			$link[1]['href'] = $_CFG['site_dir'];
			$_SESSION['activate_username']="";
			showmsg('����ɹ������������Ա���ģ�',2,$link);
			exit(); 
		}
		else
		{
			if ($activateinfo==-10)
			{
			$html="�����������";
			}
			elseif($activateinfo==-1)
			{
			$html="�����Ա���Ͷ�ʧ";
			}
			elseif($activateinfo==-2)
			{
			$html="�����������ظ�";
			}
			elseif($activateinfo==-3)
			{
			$html="�ֻ����ظ�";
			}
			elseif($activateinfo==-4)
			{
			$html="�û������ظ�";
			}
			else
			{
			$html="ԭ��δ֪";
			}
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
			unset($_SESSION["openid"]);
			$link[0]['text'] = "���µ�¼";
			$link[0]['href'] = url_rewrite('QS_login');
			showmsg("����ʧ�ܣ�ԭ��{$html}",0,$link);
			exit();
		}
}
elseif ($_SESSION['username'] && $_SESSION['utype'] &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'])
{
	header("Location:".get_member_url($_SESSION['utype']));
}
// ע���һ��
elseif ($act=='reg')
{
	if($_CFG['subsite_id']==0)showmsg("��վ������ע��,�뵽�����ڳ��еķ�վע��",1);
	if ($_CFG['closereg']=='1')showmsg("��վ��ͣ��Աע�ᣬ���Ժ��ٴγ��ԣ�",1);
	if(intval($_GET['type'])==3 && $_PLUG['hunter']['p_install']==1){
		showmsg("����Ա�ѹر���ͷģ��,��ֹע�ᣡ",1);
	}
	if(intval($_GET['type'])==4 && $_PLUG['train']['p_install']==1){
		showmsg("����Ա�ѹر���ѵģ��,��ֹע�ᣡ",1);
	}
	/**
	 * ΢��ɨ��ע��start
	 */
    if(intval($_CFG['weixin_apiopen'])==1 && intval($_CFG['weixin_scan_reg'])==1){
		$access_token = get_access_token();
	    $scene_id = rand(10000001,20000000);
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
     * ΢��ɨ��ע��end
     */
	$smarty->assign('title','��Աע�� - '.$_CFG['site_name']);
	$token=substr(md5(mt_rand(100000, 999999)), 8,16);
	$_SESSION['reg_token']=$token;
	$smarty->assign('token',$token);
	$captcha=get_cache('captcha');
	sms_get_token();
	$smarty->assign('verify_userreg',$captcha['verify_userreg']);
	$smarty->display('user/reg-step1.htm');
}
// ע��ڶ��� ͨ���ֻ�
elseif($act =="reg_step2")
{
	global $_CFG;
	if(empty($_POST['token']) || $_POST['token']!=$_SESSION['reg_token'])
	{
		$link[0]['text'] = "ע��ʧ��,����ע��";
		$link[0]['href'] = "?act=reg";
		showmsg("ע��ʧ�ܣ�����������",0,$link);
	}
	$sqlarr['utype']=$_POST['utype']?intval($_POST['utype']):showmsg('��ѡ���Ա����');
	$sqlarr['mobile']=$_POST['mobile']?trim($_POST['mobile']):showmsg('�������ֻ���');
	if($sqlarr['mobile'] != trim($_SESSION['verify_mobile']))
	{
		$link[0]['text'] = "ע��ʧ��,����ע��";
		$link[0]['href'] = "?act=reg";
		showmsg("ע��ʧ�ܣ��ֻ��������",0,$link);
	}
	$sqlarr['reg_type']=1;
	$token=substr(md5(mt_rand(100000, 999999)), 8,16);
	$_SESSION['reg_token']=$token;
	$smarty->assign('token',$token);
	$smarty->assign('title','��Աע�� - '.$_CFG['site_name']);
	$smarty->assign('sqlarr',$sqlarr);
	// ��ҵע��ѡ����Ϣ
	if($sqlarr['utype']==1 && $_CFG['reg_com_set']!='')
	{
		$reg_com_config=$_CFG['reg_com_set']==''?array():explode(',', $_CFG['reg_com_set']);
		$smarty->assign('config',$reg_com_config);
	}
	/**
	 * ΢��ɨ��ע��start
	 */
    if(intval($_CFG['weixin_apiopen'])==1 && intval($_CFG['weixin_scan_reg'])==1){
		$access_token = get_access_token();
	    $scene_id = rand(10000001,20000000);
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
     * ΢��ɨ��ע��end
     */
	$smarty->display('user/reg-step2.htm');
}
// ͨ������
elseif($act =="reg_step2_email")
{
	global $_CFG;
	if($_CFG['check_reg_email']=="1")
	{
		$email=$_GET['email']?trim($_GET['email']):"";
		$key=$_GET['key']?trim($_GET['key']):"";
		$time=$_GET['time']?trim($_GET['time']):"";

		$end_time=$time+24*3600;
		if($end_time<time())
		{
			$link[0]['text'] = "����ע��";
			$link[0]['href'] = "?act=reg";
			showmsg("ע��ʧ��,���ӹ���",0,$link);
		}
		$key_str=substr(md5($email.$time),8,16);
		if($key_str!=$key)
		{
			$link[0]['text'] = "����ע��";
			$link[0]['href'] = "?act=reg";
			showmsg("ע��ʧ��,key����",0,$link);
		}
		$token=substr(md5(mt_rand(100000, 999999)), 8,16);
		$_SESSION['reg_token']=$token;
		$smarty->assign('token',$token);
		$sqlarr['utype']=$_GET['utype']?intval($_GET['utype']):showmsg('��ѡ���Ա����');
		$sqlarr['email']=$_GET['email']?trim($_GET['email']):showmsg('����������');
	}
	else
	{
		if(empty($_POST['token']) || $_POST['token']!=$_SESSION['reg_token'])
		{
			$link[0]['text'] = "ע��ʧ��,����ע��";
			$link[0]['href'] = "?act=reg";
			showmsg("ע��ʧ�ܣ�����������",0,$link);
		}
		$sqlarr['utype']=$_POST['utype']?intval($_POST['utype']):showmsg('��ѡ���Ա����');
		$sqlarr['email']=$_POST['email']?trim($_POST['email']):showmsg('����������');
		$token=substr(md5(mt_rand(100000, 999999)), 8,16);
		$_SESSION['reg_token']=$token;
		$smarty->assign('token',$token);
	}
	// ��ҵע��ѡ����Ϣ
	if($sqlarr['utype']==1)
	{
		$reg_com_config=$_CFG['reg_com_set']==''?array():explode(',', $_CFG['reg_com_set']);
		$smarty->assign('config',$reg_com_config);
	}
	$sqlarr['reg_type']=2;
	$smarty->assign('sqlarr',$sqlarr);
	$smarty->assign('title','��Աע�� - '.$_CFG['site_name']);
	/**
	 * ΢��ɨ��ע��start
	 */
    if(intval($_CFG['weixin_apiopen'])==1 && intval($_CFG['weixin_scan_reg'])==1){
		$access_token = get_access_token();
	    $scene_id = rand(10000001,20000000);
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
     * ΢��ɨ��ע��end
     */
	$smarty->display('user/reg-step2.htm');
}
// ����ע����Ϣ
elseif($act =="reg_step3")
{
	global $db,$QS_pwdhash,$_CFG,$timestamp;
	if(empty($_POST['token']) || $_POST['token']!=$_SESSION['reg_token'])
	{
		$link[0]['text'] = "ע��ʧ��,����ע��";
		$link[0]['href'] = "?act=reg";
		showmsg("ע��ʧ�ܣ�����������",0,$link);
	}
	unset($_SESSION['reg_token']);
	// ע����Ϣ
	$reg_type=$_POST['reg_type']?intval($_POST['reg_type']):showmsg("ע�᷽ʽ����");
	$member_type=$_POST['utype']?intval($_POST['utype']):showmsg("ѡ��ע���Ա");
	$password=$_POST['password']?trim($_POST['password']):showmsg("����������");

	if($member_type==1)
	{
		$reg_com_config=explode(',', $_CFG['reg_com_set']);
		if(in_array("companyname", $reg_com_config))
		{
			$com_setarr['companyname']=$_POST['companyname']?trim($_POST['companyname']):showmsg("��������ҵ����");
		}
		if(in_array("nature", $reg_com_config))
		{
			$com_setarr['nature']=trim($_POST['nature'])?intval($_POST['nature']):showmsg("��ѡ����ҵ����");
			$com_setarr['nature_cn']=trim($_POST['nature_cn']);
		}
		if(in_array("trade", $reg_com_config))
		{
			$com_setarr['trade']=$_POST['trade']?intval($_POST['trade']):showmsg("��ѡ����ҵ������ҵ");
			$com_setarr['trade_cn']=trim($_POST['trade_cn']);
		}
		if(in_array("scale", $reg_com_config))
		{
			$com_setarr['scale']=$_POST['scale']?intval($_POST['scale']):showmsg("��ѡ����ҵ��ģ");
			$com_setarr['scale_cn']=trim($_POST['scale_cn']);
		}
		if(in_array("district", $reg_com_config))
		{
			$com_setarr['district']=intval($_POST['district'])>0?intval($_POST['district']):showmsg("��ѡ����ҵ���ڵ�");
			$com_setarr['sdistrict']=intval($_POST['sdistrict']);
			$com_setarr['district_cn']=trim($_POST['district_cn']);
		}
		if(in_array("contact", $reg_com_config))
		{
			$com_setarr['contact']=$_POST['contact']?trim($_POST['contact']):showmsg("��������ҵ��ϵ��");
		}
		if(in_array("telephone", $reg_com_config) && $reg_type==2)
		{
			$com_setarr['telephone']=$_POST['telephone']?trim($_POST['telephone']):'';
			//����
			$landline_tel[]=trim($_POST['landline_tel_first'])?trim($_POST['landline_tel_first']):"0";
			$landline_tel[]=trim($_POST['landline_tel_next'])?trim($_POST['landline_tel_next']):"0";
			$landline_tel[]=trim($_POST['landline_tel_last'])?trim($_POST['landline_tel_last']):"0";
			$com_setarr['landline_tel']=implode('-', $landline_tel);
		}
		if(in_array("email", $reg_com_config) && $reg_type==1)
		{
			$com_setarr['email']=$_POST['reg_email']?trim($_POST['reg_email']):showmsg("��������ҵ��ϵ����");
		}
		if(in_array("address", $reg_com_config))
		{
			$com_setarr['address']=$_POST['address']?trim($_POST['address']):showmsg("��������ҵ��ϸ��ַ");
		}
		if(in_array("contents", $reg_com_config))
		{
			$com_setarr['contents']=$_POST['contents']?trim($_POST['contents']):showmsg("��������ҵ����");
		}
	}
	if($reg_type==1)
	{
		$mobile=$_POST['mobile']?trim($_POST['mobile']):showmsg("ע���ֻ��Ŷ�ʧ");
		$rst=user_register($reg_type,$password,$member_type,"",$mobile,false);
	}
	else
	{
		$email=$_POST['email']?trim($_POST['email']):showmsg("ע������Ŷ�ʧ");
		$rst=user_register($reg_type,$password,$member_type,$email,"",$uc_reg=true);
	}
	if($rst>0)
	{
		$user=get_user_inid($rst);

		// ��ҵ��Ϣ
		if($member_type==1 && !empty($com_setarr))
		{
			$com_setarr['uid']=intval($rst);
			$com_setarr['audit']=intval($_CFG['audit_add_com']);
			$com_setarr['addtime']=$timestamp;
			$com_setarr['refreshtime']=$timestamp;
			$db->inserttable(table('company_profile'),$com_setarr);
		}	
		$login_js=user_login($user['username'],$password);
		$mailconfig=get_cache('mailconfig');
		if ($mailconfig['set_reg']=="1")
		{
		switch ($user['utype']) {
			case '1':
				$utype_cn='��ҵ'; 
				break;
			case '2':
				$utype_cn='����'; 
				break;
			case '3':
				$utype_cn='��ͷ'; 
				break;
			case '4':
				$utype_cn='��ѵ'; 
				break;
		}
		dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$user['uid']."&key=".asyn_userkey($user['uid'])."&sendemail=".$email."&sendusername=".$user['username']."&sendpassword=".$password."&utype=".$utype_cn."&act=reg");
		}
		$user['uc_url']=$login_js['uc_login'];
		$user['url']=$login_js['qs_login'];
		if($user['utype']=='1')
		{
			$user['index_url']=$_CFG['site_domain'].$_CFG['site_dir']."user/company/company_info.php?act=company_profile";
		}
		elseif($user['utype']=='2')
		{
			$user['index_url']=$_CFG['site_domain'].$_CFG['site_dir']."user/personal/personal_resume.php?act=make1";
		}
		elseif($user['utype']=='3')
		{
			$user['index_url']=$_CFG['site_domain'].$_CFG['site_dir']."user/hunter/hunter_info.php?act=hunter_profile";
		}
		else
		{
			$user['index_url']=$_CFG['site_domain'].$_CFG['site_dir']."user/train/train_info.php?act=train_profile";
		}
		$smarty->assign('title','��Աע�� - '.$_CFG['site_name']);
		$smarty->assign('user',$user);
		setcookie("isFirstReg",1, time()+3600*24);
		$smarty->display('user/reg-step3.htm');
	}
	else
	{
		$link[0]['text'] = "ע��ʧ��,����ע��";
		$link[0]['href'] = "?act=reg";
		showmsg("ע��ʧ��",0,$link);
	}
}
unset($smarty);
?>