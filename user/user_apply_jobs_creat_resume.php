<?php
 /*
 * ����ְλ��δ��¼״̬�� ��ݷ�������
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
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):"δ��������";
	$setsqlarr['fullname']=trim($_POST['fullname'])?trim($_POST['fullname']):exit('����д����');
	$setsqlarr['sex']=intval($_POST['sex'])?intval($_POST['sex']):exit('��ѡ���Ա�');
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):exit('��ѡ��ѧ��');
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=$_POST['experience']?$_POST['experience']:exit('��ѡ��������');
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['intention_jobs']=trim($_POST['intention_jobs'])?trim($_POST['intention_jobs']):exit('��ѡ������ְλ');
	$setsqlarr['district_cn']=trim($_POST['district_cn'])?trim($_POST['district_cn']):exit('��ѡ����������');
	$setsqlarr['wage']=trim($_POST['wage'])?trim($_POST['wage']):exit('��ѡ������н��');
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):exit('����д�ֻ���');
	$setsqlarr['addtime']=$time;
	$setsqlarr['refreshtime']=$time;
	$setsqlarr['audit']=1;
	$setsqlarr['resume_from_pc']=4;
	if(get_user_inmobile($setsqlarr['telephone']))
	{
		exit("�ֻ����Ѿ���ע���,������д��");
	}
	$setsqlarr['current']=intval($_POST['current'])?intval($_POST['current']):exit('��ѡ��Ŀǰ״̬');
	$setsqlarr['current_cn']=trim($_POST['current_cn']);
	// ע���Ա
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
		//�����Ա��«�ұ��Ҳ�����«��
		$setarr['uid']=$insert_id;
		$db->inserttable(table("members_points"),$setarr);
		$points=get_cache('points_rule');
		if ($points['reg_per_points']['value']>0)
		{
			report_deal($insert_id,$points['reg_per_points']['type'],$points['reg_per_points']['value']);
			$operator=$points['reg_per_points']['type']=="1"?"+":"-";
			write_memberslog($insert_id,2,9001,$username,"��ע���Ա,({$operator}{$points['reg_per_points']['value']}),(ʣ��:{$points['reg_per_points']['value']})",2,1010,"ע���Աϵͳ�Զ����ͺ�«��","{$operator}{$points['reg_per_points']['value']}","{$points['reg_per_points']['value']}");
		}
		// ��¼
		$login =user_login($user_arr['username'],'123456');
		// ��ӻ�Ա��Ϣ
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
		// ���� ����
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
		// ֱ��Ͷ�ݼ���
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
			send_sms($setsqlarr['telephone'],"��ӭ��ע��".$_CFG['site_name'].",�û���:".$user_arr['username']."���룺123456,��Ҳ����ֱ���ֻ��ŵ�¼��");
			exit('��������Ͷ�ݳɹ���');
		}
	}
	else
	{
		exit("ע��ʧ�ܣ�");
	}
}
elseif($act == "check_mobile")
{
	$mobile = trim($_POST['mobile']);
	if (empty($mobile) || !preg_match("/^(13|15|14|17|18)\d{9}$/",$mobile))
	{
	exit("��������ȷ���ֻ��ţ�");
	}
	if(get_user_inmobile($mobile))
	{
		exit("�ֻ����Ѿ�����,�뻻һ�����룡");
	}
	else
	{
		exit('ok');
	}
}
elseif ($act =="send_code")
{
	$mobile = trim($_POST['mobile'])?trim($_POST['mobile']):exit("����д�ֻ���");
	if(get_user_inmobile($mobile))
	{
		exit("�ֻ����Ѿ�����,�뻻һ�����룡");
	}
	$SMSconfig=get_cache('sms_config');
	if ($SMSconfig['open']!="1")
	{
	exit("����ģ�鴦�ڹر�״̬");
	}
	if ($_SESSION['send_time'] && (time()-$_SESSION['send_time'])<100)
	{
	exit("��100����ٽ��в�����");
	}
	$rand=mt_rand(100000, 999999);	
	$r=send_sms($mobile,"������{$_CFG['site_name']}���п��ٴ�������,��֤��Ϊ:{$rand}");
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
	exit("SMS���ó�������ϵ��վ����Ա");
	}
}
elseif ($act =="check_code")
{
	$verifycode=trim($_POST['code']);
	$mobile =$_POST['mobile'];
	if (empty($verifycode) || empty($_SESSION['mobile_rand']) || $mobile<>$_SESSION['mobile'] || $verifycode<>$_SESSION['mobile_rand'])
	{
		exit("��֤�����");
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
