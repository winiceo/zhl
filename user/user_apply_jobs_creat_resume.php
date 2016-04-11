<?php
 /*
 * 申请职位，未登录状态下 快捷发布简历
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(dirname(__FILE__).'/../include/fun_user.php');
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'create_resume';
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
require_once(QISHI_ROOT_PATH.'include/fun_weixin.php');
?>
<?php
if($act=="create_resume")
{
	$time =time();
	foreach ($_POST as $key => $value) {
		$value=utf8_to_gbk($value);
		$_POST[$key]=$value;
	}
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):"未命名简历";
	$setsqlarr['fullname']=trim($_POST['fullname'])?trim($_POST['fullname']):exit('请填写姓名');
	$setsqlarr['sex']=intval($_POST['sex'])?intval($_POST['sex']):exit('请选择性别');
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):exit('请选择学历');
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=$_POST['experience']?$_POST['experience']:exit('请选择工作经验');
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['intention_jobs']=trim($_POST['intention_jobs'])?trim($_POST['intention_jobs']):exit('请选择期望职位');
	$setsqlarr['district_cn']=trim($_POST['district_cn'])?trim($_POST['district_cn']):exit('请选择期望地区');
	$setsqlarr['wage']=trim($_POST['wage'])?trim($_POST['wage']):exit('请选择期望薪资');
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):exit('请填写手机号');
	$setsqlarr['addtime']=$time;
	$setsqlarr['refreshtime']=$time;
	$setsqlarr['audit']=1;
	$setsqlarr['resume_from_pc']=4;
	if(get_user_inmobile($setsqlarr['telephone']))
	{
		exit("手机号已经被注册过,重新填写！");
	}
	$setsqlarr['current']=intval($_POST['current'])?intval($_POST['current']):exit('请选择目前状态');
	$setsqlarr['current_cn']=trim($_POST['current_cn']);
	// 注册会员
	$user_arr['username']="sj_".randusername();
	$user_arr['utype']=2;
	$user_arr['mobile']=$setsqlarr['telephone'];
	$user_arr['mobile_audit']=1;
	$user_arr['pwd_hash']=randstr();
	$user_arr['password']=md5(md5('123456').$user_arr['pwd_hash'].$QS_pwdhash);
	$user_arr['reg_time']=$timestamp;
	$user_arr['reg_ip']=$online_ip;
	$insert_id=$db->inserttable(table('members'),$user_arr,true);
	if($insert_id)
	{
		//插入会员葫芦币表并且操作葫芦币
		$setarr['uid']=$insert_id;
		$db->inserttable(table("members_points"),$setarr);
		$points=get_cache('points_rule');
		if ($points['reg_per_points']['value']>0)
		{
			report_deal($insert_id,$points['reg_per_points']['type'],$points['reg_per_points']['value']);
			$operator=$points['reg_per_points']['type']=="1"?"+":"-";
			write_memberslog($insert_id,2,9001,$username,"新注册会员,({$operator}{$points['reg_per_points']['value']}),(剩余:{$points['reg_per_points']['value']})",2,1010,"注册会员系统自动赠送葫芦币","{$operator}{$points['reg_per_points']['value']}","{$points['reg_per_points']['value']}");
		}
		// 登录
		$login =user_login($user_arr['username'],'123456');
		// 添加会员信息
		$user_info['uid']=$insert_id;
		$user_info['realname']=$setsqlarr['fullname'];
		$user_info['sex']=$setsqlarr['sex'];
		$user_info['sex_cn']=$setsqlarr['sex_cn'];
		$user_info['education']=$setsqlarr['education'];
		$user_info['education_cn']=$setsqlarr['education_cn'];
		$user_info['experience']=$setsqlarr['experience'];
		$user_info['experience_cn']=$setsqlarr['experience_cn'];
		$user_info['phone']=$setsqlarr['telephone'];
		$db->inserttable(table('members_info'),$user_info);
		// 创建 简历
		$setsqlarr['uid']=$insert_id;
		$pid=$db->inserttable(table('resume'),$setsqlarr,1);
		$searchtab['id'] = $pid;
		$searchtab['uid'] = $insert_id;
		$db->inserttable(table('resume_search_key'),$searchtab);
		$db->inserttable(table('resume_search_rtime'),$searchtab);

		add_resume_jobs($pid,$insert_id,$_POST['intention_jobs_id']);
		//add_resume_district($pid,$_SESSION['uid'],$_POST['district']);
		add_resume_trade($pid,$_SESSION['uid'],$_POST['trade']);
		check_resume($_SESSION['uid'],$pid);
		// 直接投递简历
		$jobsarr=app_get_jobs($_POST['jobsid']);
		foreach($jobsarr as $jobs)
	 	{
	 		$jobs = array_map("addslashes",$jobs);
	 		$addarr['resume_id']=$pid;
			$addarr['resume_name']=$setsqlarr['fullname'];
			$addarr['personal_uid']=$insert_id;
			$addarr['jobs_id']=$jobs['id'];
			$addarr['jobs_name']=$jobs['jobs_name'];
			$addarr['company_id']=$jobs['company_id'];
			$addarr['company_name']=$jobs['companyname'];
			$addarr['company_uid']=$jobs['uid'];
			$addarr['apply_addtime']=time();
			$addarr['personal_look']=1;
			$addarr['is_apply']=1;
	 	}
		if ($db->inserttable(table('personal_jobs_apply'),$addarr))
		{
			send_sms($setsqlarr['telephone'],"欢迎您注册".$_CFG['site_name'].",用户名:".$user_arr['username']."密码：123456,您也可以直接手机号登录。");
			exit('创建并且投递成功！');
		}
	}
	else
	{
		exit("注册失败！");
	}
}
elseif($act == "check_mobile")
{
	$mobile = trim($_POST['mobile']);
	if (empty($mobile) || !preg_match("/^(13|15|14|17|18)\d{9}$/",$mobile))
	{
	exit("请输入正确的手机号！");
	}
	if(get_user_inmobile($mobile))
	{
		exit("手机号已经存在,请换一个号码！");
	}
	else
	{
		exit('ok');
	}
}
elseif ($act =="send_code")
{
	$mobile = trim($_POST['mobile'])?trim($_POST['mobile']):exit("请填写手机号");
	if(get_user_inmobile($mobile))
	{
		exit("手机号已经存在,请换一个号码！");
	}
	$SMSconfig=get_cache('sms_config');
	if ($SMSconfig['open']!="1")
	{
	exit("短信模块处于关闭状态");
	}
	if ($_SESSION['send_time'] && (time()-$_SESSION['send_time'])<100)
	{
	exit("请100秒后再进行操作！");
	}
	$rand=mt_rand(100000, 999999);	
	$r=send_sms($mobile,"您正在{$_CFG['site_name']}经行快速创建简历,验证码为:{$rand}");
	if ($r=="success")
	{
	$_SESSION['mobile']=$mobile;
	$_SESSION['mobile_rand']=$rand;
	$_SESSION['send_time']=time();
	$_SESSION['verify_mobile']=$mobile;
	exit("ok");
	}
	else
	{
	exit("SMS配置出错，请联系网站管理员");
	}
}
elseif ($act =="check_code")
{
	$verifycode=trim($_POST['code']);
	$mobile =$_POST['mobile'];
	if (empty($verifycode) || empty($_SESSION['mobile_rand']) || $mobile<>$_SESSION['mobile'] || $verifycode<>$_SESSION['mobile_rand'])
	{
		exit("验证码错误");
	}
	else
	{
		exit('ok');
	}
}
function reduce_user_sms($uid){	
	global $db;
	$db->query("UPDATE ".table('members')." SET `sms_num` = sms_num - 1 WHERE `uid` = ".$uid." LIMIT 1 ;"); 
}
?>
