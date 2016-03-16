<?php
 /*
 * WAP
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
if (intval($_SESSION['uid'])=='' || $_SESSION['username']==''||intval($_SESSION['utype'])==1)
{
	header("Location: ../login.php");
}
$user = get_user_info($_SESSION['uid']);
if($_CFG['login_per_audit_mobile'] && $user['mobile_audit']=="0" && $act != 'index')
{
	$str= "<script>";
	$str.="alert('������֤�ֻ���');";
	$str.="window.location.href='account_security.php';";
	$str.= "</script>";
	echo $str;
}
if ($act == 'index')
{
	$smarty->cache = false;
	$user=wap_get_user_info(intval($_SESSION['uid']));	
	$smarty->assign('user',$user);
	$resume_info=get_userprofile(intval($_SESSION['uid']));
	if(empty($resume_info))
	{
		header("Location: ?act=make_resume");
	}
	else
	{
		$resume_info['age']=date("Y")-$resume_info['birthday'];
		//ͳ�Ƽ�����
		$smarty->assign('count_resume',count_resume(intval($_SESSION['uid'])));
		//ͳ����������Է�δ�鿴��
		$smarty->assign('count_interview',count_interview($_SESSION['uid'],2,1));
		$smarty->assign('resume_info',$resume_info);
		$smarty->display("m/personal/m-user-personal-index.html");
	}
}
elseif ($act == 'favorites')
{
	$perpage = 7;
	$count  = 0;
	$page = empty($_GET['page'])?1:intval($_GET['page']);
	if($page<1) $page = 1;
	$theurl = "wap_user.php?act=favorites";
	$start = ($page-1)*$perpage;
	$wheresql=" WHERE f.personal_uid='{$_SESSION['uid']}' ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_favorites')." AS f {$wheresql} ";
	$count=$db->get_total($total_sql);
	$joinsql=" LEFT JOIN ".table('jobs')." as  j  ON f.jobs_id=j.id ";
	$smarty->assign('favorites',get_favorites($start,$perpage,$joinsql.$wheresql));
	$smarty->assign('pagehtml',wapmulti($count, $perpage, $page, $theurl));
	$smarty->display('m/personal/m-favorites.html');
}
elseif ($act == 'add_favorites')
{
	$id=isset($_POST['id'])?intval($_POST['id']):exit("err");
	if(intval($_SESSION['utype']!=2)){
		exit("���˻�Ա���¼���ղ�ְλ");
	}
	elseif(add_favorites($id,intval($_SESSION['uid']))==0)
	{
	exit("�ղؼ����Ѿ����ڴ�ְλ");
	}
	else
	{
	exit("ok");
	}
}
elseif ($act=='del_favorites')
{
	$yid =!empty($_POST['id'])?$_POST['id']:exit("��û��ѡ����Ŀ��");
	if($n=del_favorites($yid,$_SESSION['uid']))
	{
		exit("ok");
	}
	else
	{
		exit("err");
	}
}
// ��д����
elseif($act == "make_resume")
{
	$smarty->cache = false;
	$uid=intval($_SESSION['uid']);
	$user=wap_get_user_info(intval($_SESSION['uid']));
	$smarty->assign('user',$user);
	$smarty->assign('userprofile',get_userprofile($_SESSION['uid']));
	$smarty->display('m/personal/m-make-resume.html');
}
elseif($act == "make_resume_save")
{	
	$_POST=array_map("utf8_to_gbk",$_POST);
	$setsqlarr['uid']=$_SESSION['uid'];
	$setsqlarr['fullname']=trim($_POST['fullname'])?trim($_POST['fullname']):exit("����д��ʵ����");
	$setsqlarr['title']=trim($_POST['fullname'])."�ļ���";
	$setsqlarr['display_name']=1;
	$setsqlarr['sex']=trim($_POST['sex'])?trim($_POST['sex']):exit("��ѡ���Ա�");
	$setsqlarr['sex_cn']=trim($_POST['sex_cn'])?trim($_POST['sex_cn']):exit("��ѡ���Ա�");
	$setsqlarr['birthdate']=intval($_POST['birthdate'])?intval($_POST['birthdate']):exit("��ѡ��������");
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):exit("��ѡ����ѧ��");
	$setsqlarr['education_cn']=trim($_POST['education_cn'])?trim($_POST['education_cn']):exit("��ѡ����ѧ��");
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):exit("��ѡ��������");
	$setsqlarr['experience_cn']=trim($_POST['experience_cn'])?trim($_POST['experience_cn']):exit("��ѡ��������");
	$setsqlarr['email']=trim($_POST['email'])?trim($_POST['email']):exit("����д����");
	$setsqlarr['email_notify']=$_POST['email_notify']=="1"?1:0;
	$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):exit("����д�ֻ�");
	$setsqlarr['current']=intval($_POST['current'])?intval($_POST['current']):exit("��ѡ��Ŀǰ״̬");
	$setsqlarr['current_cn']=trim($_POST['current_cn'])?trim($_POST['current_cn']):exit("��ѡ��Ŀǰ״̬");
	$setsqlarr['intention_jobs']=trim($_POST['intention_jobs'])?trim($_POST['intention_jobs']):exit("��ѡ������ְλ");
	$_POST['intention_jobs_id']=trim($_POST['intention_jobs_id'])?trim($_POST['intention_jobs_id']):exit("��ѡ������ְλ");
	$setsqlarr['trade']=trim($_POST['trade'])?trim($_POST['trade']):exit("��ѡ��������ҵ");
	$setsqlarr['trade_cn']=trim($_POST['trade_cn'])?trim($_POST['trade_cn']):exit("��ѡ��������ҵ");
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$setsqlarr['district']=trim($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn'])?trim($_POST['district_cn']):exit("������������");
	/*$setsqlarr['nature']=intval($_POST['nature'])?intval($_POST['nature']):exit("��ѡ��������");
	$setsqlarr['nature_cn']=trim($_POST['nature_cn'])?trim($_POST['nature_cn']):exit("��ѡ��������");*/
	$setsqlarr['wage']=intval($_POST['wage'])?intval($_POST['wage']):exit("��ѡ������н��");
	$setsqlarr['wage_cn']=trim($_POST['wage_cn'])?trim($_POST['wage_cn']):exit("��ѡ������н��");
	// $setsqlarr['specialty']=trim($_POST['specialty'])?trim($_POST['specialty']):exit("����д��������");
	$auto_refresh=intval($_POST['auto_refresh']);
	$auto_apply=intval($_POST['auto_apply']);
	$setsqlarr['refreshtime']=time();
	$setsqlarr['audit']=intval($_CFG['audit_resume']);
	//1->PC  2->APP  3->wap
	$setsqlarr['resume_from_pc']=3;
	$total=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume')." WHERE uid='{$_SESSION['uid']}'");
	if ($total>=intval($_CFG['resume_max']))
	{
	exit("�������Դ���{$_CFG['resume_max']} �ݼ���,�Ѿ�������������ƣ�");
	}
	else
	{
	$setsqlarr['addtime']=time();
	$pid=$db->inserttable(table('resume'),$setsqlarr,1);
	$searchtab['id'] = $pid;
	$searchtab['uid'] = intval($_SESSION['uid']);
	
	$db->inserttable(table('resume_search_key'),$searchtab);
	$db->inserttable(table('resume_search_rtime'),$searchtab);
	if (empty($pid))exit("����ʧ�ܣ�");
	// 3�����Զ�ˢ��
	if($auto_refresh==1)
	{
		$time=time();
		$auto_refresh_row=$db->getone("select * from  ".table("resume_auto_refresh")." where  resume_id=".$pid." and  deadline_time>$time limit 1 ");
		$setarr['deadline_time']=$time+3600*24*3;
		if(empty($auto_refresh_row))
		{   
			$setarr['resume_id']=$pid;
			$db->inserttable(table("resume_auto_refresh"),$setarr);
		}
		else
		{
			$db->updatetable(table("resume_auto_refresh"),$setarr,array("resume_id"=>$pid));
		}
	}
	// 3���� �Զ�Ͷ��
	if($auto_apply==1)
	{
		$time=time();
		$auto_apply_row=$db->getone("select * from ".table("resume_entrust")." where id=$pid and uid=".$_SESSION['uid']." and entrust_end>$time ");
		if(empty($auto_apply_row))
		{
			$setarr_apply['id']=$pid;
			$setarr_apply['uid']=$_SESSION['uid'];
			$setarr_apply['fullname']=$setsqlarr['fullname'];
			$setarr_apply['entrust']=1;
			$setarr_apply['entrust_start']=time();
			$setarr_apply['entrust_end']=$time+3600*24*3;;
			$setarr_apply['isshield']=0;
			$setarr_apply['resume_addtime']=time();
			$db->inserttable(table("resume_entrust"),$setarr_apply);
			$db->query("update ".table("resume")." set entrust=1 where id=$pid and uid=".$_SESSION['uid']);
		}
	}

	if(!wap_add_resume_jobs($pid,intval($_SESSION['uid']),$_POST["intention_jobs_id"]))exit('err');
	check_resume($_SESSION['uid'],$pid);
	if(intval($_POST['entrust'])){
		set_resume_entrust($pid);
	}

	// �鿴������¼�� ͳ�ƴ���������������������  �ж��Ƿ񳬹�����   ��û�������� �������ӻ���
	$today=mktime(0, 0, 0,date('m'), date('d'), date('Y'));
	$info=$db->getone("SELECT sum(points) as num FROM ".table('members_handsel')." WHERE uid ='{$_SESSION['uid']}' AND htype='createresume' AND addtime>{$today} ");
	if(intval($info['num']) >= intval($_CFG['create_resume_points_max']))
	{
		write_memberslog($_SESSION['uid'],2,1101,$_SESSION['username'],"�����˼���");
	}
	else
	{
		$points_rule=get_cache('points_rule');
		$user_points=get_user_points($_SESSION['uid']);
		if ($points_rule['create_resume']['value']>0)
		{
			$time=time();
			$members_handsel_arr['uid']=$_SESSION['uid'];
			$members_handsel_arr['htype']="createresume";
			$members_handsel_arr['points']=$points_rule['create_resume']['value'];
			$members_handsel_arr['addtime']=$time;
			$db->inserttable(table("members_handsel"),$members_handsel_arr);
			report_deal($_SESSION['uid'],$points_rule['create_resume']['type'],$points_rule['create_resume']['value']);
			$user_points=get_user_points($_SESSION['uid']);
			$operator=$points_rule['create_resume']['type']=="1"?"+":"-";
			write_memberslog($_SESSION['uid'],2,9001,$_SESSION['username'],"�����˼�����<strong>{$setsqlarr['title']}</strong>��({$operator}{$points_rule['create_resume']['value']})��(ʣ��:{$user_points})",2,1101,"�����˼���","{$operator}{$points_rule['create_resume']['value']}","{$user_points}");
		}
		else
		{
			write_memberslog($_SESSION['uid'],2,1101,$_SESSION['username'],"�����˼���");
		}
	}
	
	if(!get_userprofile(intval($_SESSION['uid']))){
		$infoarr['realname']=$setsqlarr['fullname'];
		$infoarr['sex']=$setsqlarr['sex'];
		$infoarr['sex_cn']=$setsqlarr['sex_cn'];
		$infoarr['birthday']=$setsqlarr['birthdate'];
		$infoarr['residence']=$setsqlarr['residence'];
		$infoarr['residence']=$setsqlarr['residence'];
		$infoarr['education']=$setsqlarr['education'];
		$infoarr['education_cn']=$setsqlarr['education_cn'];
		$infoarr['experience']=$setsqlarr['experience'];
		$infoarr['experience_cn']=$setsqlarr['experience_cn'];
		$infoarr['phone']=$setsqlarr['telephone'];
		$infoarr['email']=$setsqlarr['email'];
		$infoarr['uid']=intval($_SESSION['uid']);
		$db->inserttable(table('members_info'),$infoarr);
	}
	baidu_submiturl(url_rewrite('QS_resumeshow',array('id'=>$pid)),'addresume');
	echo $pid;
	// header("Location: ?act=resume_success&pid=".$pid);
	}
}
// ��д�������
elseif($act == "resume_success")
{
	$smarty->cache = false;
	$id=intval($_GET['pid']);
	$sql="select j.* from ".table("jobs")." as j left join ".table("resume_jobs")." as r on r.category=j.category where r.pid=$id limit 5";
	$resume_jobs=$db->getall($sql);
	$smarty->assign('resume_jobs',$resume_jobs);
	$smarty->display('m/personal/create-resume-success.html');
}
// �����б�
elseif($act == "resume_list")
{	
	$smarty->cache = false;
	$wheresql=" WHERE uid='".intval($_SESSION['uid'])."' ";
	$sql="SELECT * FROM ".table('resume').$wheresql;
	$resume_list=get_resume_list($sql,12,true,true,true);
	$smarty->assign('resume_list',$resume_list);
	$smarty->display('m/personal/m-resumelist.html');
}
// ���Ƽ���
elseif($act == "resume_one")
{
	$smarty->cache = false;
	$id=intval($_GET['pid']);
	$resume_one=resume_one($id);
	$smarty->assign('resume_one',$resume_one);
	$smarty->assign('resume_basic',get_resume_basic(intval($_SESSION['uid']),$id));
	$smarty->assign('resume_jobs',get_resume_jobs($id));
	$smarty->assign('resume_education',get_resume_education(intval($_SESSION['uid']),$id));
	$smarty->assign('resume_work',get_resume_work(intval($_SESSION['uid']),$id));
	$smarty->assign('resume_training',get_resume_training(intval($_SESSION['uid']),$id));
	$smarty->display('m/personal/m-comlpete-resume.html');
}
elseif($act == "resume_basic")
{
	$smarty->cache = false;
	$id=intval($_GET['pid']);
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),$id);
	// var_dump($resume_basic);
	$smarty->assign('userprofile',get_userprofile(intval($_SESSION['uid'])));
	$smarty->assign('resume_basic',$resume_basic);
	$smarty->display('m/personal/m-personal-info.html');
}
elseif($act == "resume_basic_save")
{
	$smarty->cache = false;
	$_POST=array_map("utf8_to_gbk",$_POST);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['fullname']=trim($_POST['fullname'])?trim($_POST['fullname']):exit("����д��ʵ����");
	$setsqlarr['display_name']=intval($_POST['display_name']);
	$setsqlarr['sex']=trim($_POST['sex'])?trim($_POST['sex']):exit("��ѡ���Ա�");
	$setsqlarr['sex_cn']=trim($_POST['sex_cn'])?trim($_POST['sex_cn']):exit("��ѡ���Ա�");
	$setsqlarr['birthdate']=intval($_POST['birthdate'])?intval($_POST['birthdate']):exit("��ѡ��������");
	$setsqlarr['residence']=trim($_POST['residence'])?trim($_POST['residence']):exit("��ѡ���־�ס��");
	$setsqlarr['residence']=trim($_POST['residence'])?trim($_POST['residence']):exit("��ѡ���־�ס��");
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):exit("��ѡ����ѧ��");
	$setsqlarr['education_cn']=trim($_POST['education_cn'])?trim($_POST['education_cn']):exit("��ѡ����ѧ��");
	$setsqlarr['major']=intval($_POST['major'])?intval($_POST['major']):exit("��ѡ��רҵ");
	$setsqlarr['major_cn']=trim($_POST['major_cn'])?trim($_POST['major_cn']):exit("��ѡ��רҵ");
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):exit("��ѡ��������");
	$setsqlarr['experience_cn']=trim($_POST['experience_cn'])?trim($_POST['experience_cn']):exit("��ѡ��������");
	$setsqlarr['email']=trim($_POST['email'])?trim($_POST['email']):exit("����д����");
	if(!preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$setsqlarr['email']))
	{
		exit("�����ʽ����");
	}
	$setsqlarr['email_notify']=$_POST['email_notify']=="1"?1:0;
	$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):exit("����д�ֻ�");
	if(!preg_match("/^(13|15|14|17|18)\d{9}$/",$setsqlarr['telephone']))
	{
		exit("�ֻ���ʽ����");
	}
	$setsqlarr['height']=trim($_POST['height']);
	$setsqlarr['householdaddress']=trim($_POST['householdaddress']);
	$setsqlarr['marriage']=intval($_POST['marriage']);
	$setsqlarr['marriage_cn']=trim($_POST['marriage_cn']);
	$db->updatetable(table('resume'),$setsqlarr," id='".intval($_POST['pid'])."'  AND uid='{$setsqlarr['uid']}'");
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
	if($_CFG['audit_edit_resume']!="-1"){
		set_resume_entrust(intval($_REQUEST['pid']));
	}
	write_memberslog($_SESSION['uid'],2,1105,$_SESSION['username'],"�޸��˼���({$_POST['title']})");

	$infoarr['realname']=$setsqlarr['fullname'];
	$infoarr['sex']=$setsqlarr['sex'];
	$infoarr['sex_cn']=$setsqlarr['sex_cn'];
	$infoarr['birthday']=$setsqlarr['birthdate'];
	$infoarr['residence']=$setsqlarr['residence'];
	$infoarr['education']=$setsqlarr['education'];
	$infoarr['education_cn']=$setsqlarr['education_cn'];
	$infoarr['major']=$setsqlarr['major'];
	$infoarr['major_cn']=$setsqlarr['major_cn'];
	$infoarr['experience']=$setsqlarr['experience'];
	$infoarr['experience_cn']=$setsqlarr['experience_cn'];
	$infoarr['phone']=$setsqlarr['telephone'];
	$infoarr['email']=$setsqlarr['email'];
	$infoarr['height']=$setsqlarr['height'];
	$infoarr['householdaddress']=$setsqlarr['householdaddress'];
	$infoarr['marriage']=$setsqlarr['marriage'];
	$infoarr['marriage_cn']=$setsqlarr['marriage_cn'];
	$infoarr['uid']=intval($_SESSION['uid']);
	$db->updatetable(table('members_info'),$infoarr," uid={$infoarr['uid']} ");
	exit("ok");
}
// ��ְ���� ְλ
elseif($act == "resume_jobs")
{
	$smarty->cache = false;
	$id=$_GET['pid'];
	$resume_one=resume_one($id);
	$smarty->assign('resume_one',$resume_one);
	$resume_jobs = get_resume_jobs($id);
	if ($resume_jobs)
	{
		foreach($resume_jobs as $rjob)
		{
		$jobsid[]=$rjob['topclass'].".".$rjob['category'].".".$rjob['subclass'];
		}
		$resume_jobs_id=implode(",",$jobsid);
	}
	$smarty->assign('resume_jobs_id',$resume_jobs_id);
	$smarty->display('m/personal/m-want-job.html');
}
elseif($act == "resume_jobs_save")
{
	$smarty->cache = false;
	$_POST=array_map("utf8_to_gbk",$_POST);
	$setsqlarr['current']=trim($_POST['current'])?trim($_POST['current']):exit("��ѡ��Ŀǰ״̬");
	$setsqlarr['current_cn']=trim($_POST['current_cn'])?trim($_POST['current_cn']):exit("��ѡ��Ŀǰ״̬");
	$setsqlarr['intention_jobs']=trim($_POST['intention_jobs'])?trim($_POST['intention_jobs']):exit("��ѡ������ְλ");
	$_POST['intention_jobs_id']=trim($_POST['intention_jobs_id'])?trim($_POST['intention_jobs_id']):exit("��ѡ������ְλ");
	$setsqlarr['wage']=trim($_POST['wage'])?trim($_POST['wage']):exit("��ѡ������н��");
	$setsqlarr['wage_cn']=trim($_POST['wage_cn'])?trim($_POST['wage_cn']):exit("��ѡ������н��");
	$setsqlarr['nature']=trim($_POST['nature'])?trim($_POST['nature']):exit("��ѡ��������������");
	$setsqlarr['nature_cn']=trim($_POST['nature_cn'])?trim($_POST['nature_cn']):exit("��ѡ��������������");
	$setsqlarr['trade']=trim($_POST['trade'])?trim($_POST['trade']):exit("��ѡ��������ҵ");
	$setsqlarr['trade_cn']=trim($_POST['trade_cn'])?trim($_POST['trade_cn']):exit("��ѡ��������ҵ");
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$setsqlarr['district']=trim($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn'])?trim($_POST['district_cn']):exit("��ѡ��������������");
	if(!$db->updatetable(table('resume'),$setsqlarr,array("id"=>intval($_POST['pid']),"uid"=>intval($_SESSION['uid']))))exit("err");
	if(!wap_add_resume_jobs(intval($_POST['pid']),intval($_SESSION['uid']),$_POST['intention_jobs_id']))exit('err');
	if(!wap_add_resume_trade(intval($_POST['pid']),intval($_SESSION['uid']),$setsqlarr['trade']))exit('err');
	exit("ok");
}
//  ��������
elseif($act == "resume_work_list")
{
	$smarty->cache = false;
	$id=intval($_GET['pid']);
	$resume_work_list=get_resume_work(intval($_SESSION['uid']),$id);
	$smarty->assign('resume_work',$resume_work_list);
	$smarty->display('m/personal/m-work-experience.html');
}
// ��� �޸� ��������
elseif($act == "resume_work_add")
{	
	$smarty->cache = false;
	$id=intval($_GET['id']);
	$resume_work=get_this_work(intval($_SESSION['uid']),$id);
	if($resume_work){
		$smarty->assign('resume_work',$resume_work);
	}else{
		$smarty->assign('resume_work',false);
	}
	$smarty->display('m/personal/m-edit-work-experience.html');
}
elseif($act == "resume_work_save")
{
	$_POST=array_map("utf8_to_gbk",$_POST);
	// print_r($_POST);die;
	$id=intval($_POST['id']);
	$setsqlarr['uid'] = intval($_SESSION['uid']);
	$setsqlarr['pid'] = intval($_POST['pid']);

	if ($setsqlarr['uid']==0 || $setsqlarr['pid']==0 )exit('����������');

	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_POST['pid']));
	if (empty($resume_basic)) exit('������д����������Ϣ');
	$resume_work=get_resume_work($_SESSION['uid'],intval($_POST['pid']));
	if (count($resume_work)>=6)  exit('�����������ܳ���6��');
	$setsqlarr['companyname'] = trim($_POST['companyname'])?trim($_POST['companyname']):exit('����д��˾���ƣ�');
	$setsqlarr['jobs'] = trim($_POST['jobs'])?trim($_POST['jobs']):exit("����дְλ���ƣ�");
	if(trim($_POST['startyear'])==""||trim($_POST['startmonth'])==""||trim($_POST['endyear'])==""){
		exit("��ѡ����ְʱ��!");
	}
	$setsqlarr['startyear'] = intval($_POST['startyear']);
	$setsqlarr['startmonth'] = intval($_POST['startmonth']);
	//���� ���
	if(intval($_POST['endyear']) == -1)
	{
		$setsqlarr['endyear'] = 0;
		$setsqlarr['endmonth'] = 0;
		$setsqlarr['todate'] = 1;
	}
	else
	{
		$setsqlarr['endyear'] = intval($_POST['endyear']);
		$setsqlarr['endmonth'] = intval($_POST['endmonth']);
		$setsqlarr['todate'] = 0;
	}
	$setsqlarr['achievements'] = trim($_POST['achievements'])?trim($_POST['achievements']):exit("����д����ְ��");
	
	if($id){
		$db->updatetable(table("resume_work"),$setsqlarr,array("id"=>$id,"uid"=>intval($_SESSION['uid'])));
		exit("ok");
	}else{
		$insert_id = $db->inserttable(table("resume_work"),$setsqlarr,1);
		if($insert_id){
			check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
			perfect_resume($_SESSION['uid'],$_SESSION['username'],$_REQUEST['pid'],1);
			exit("ok");
		}else{
			exit("err");
		}
	}
}
elseif($act == "resume_work_del")
{
	$smarty->cache = false;
	$id=intval($_GET['work_id']);
	$uid=intval($_SESSION["uid"]);
	$sql="delete from ".table("resume_work")." where id=$id and uid=$uid ";
	if($db->query($sql)){
		perfect_resume($_SESSION['uid'],$_SESSION['username'],$id,2);
		exit("ok");
	}else{
		exit("err");
	}
}
// ��������
elseif($act == "resume_education")
{	
	$smarty->cache = false;
	$id=intval($_GET['pid']);
	$resume_education_list=get_resume_education(intval($_SESSION['uid']),$id);
	// var_dump($resume_education_list);
	$smarty->assign("resume_education_list",$resume_education_list);
	$smarty->display('m/personal/m-edu-experience.html');
}
// ��� �޸� ��������
elseif($act == "resume_education_add")
{	
	$smarty->cache = false;
	$id=intval($_GET["id"]);
	$resume_edu=get_this_education(intval($_SESSION['uid']),$id);
	if($resume_edu){
		$smarty->assign('resume_edu',$resume_edu);
	}else{
		$smarty->assign('resume_edu',false);
	}
	$smarty->display('m/personal/m-edit-edu-experience.html');
}
elseif($act == "resume_education_save")
{
	// print_r($_POST);die;
	$_POST=array_map("utf8_to_gbk",$_POST);
	$id=intval($_POST['id']);
	$setsqlarr['uid'] = intval($_SESSION['uid']);
	$setsqlarr['pid'] = intval($_POST['pid']);
	if ($setsqlarr['uid']==0 || $setsqlarr['pid']==0 )exit('����������');
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_POST['pid']));
	if (empty($resume_basic)) exit('������д����������Ϣ');
	$resume_education=get_resume_education($_SESSION['uid'],intval($_POST['pid']));
	if (count($resume_education)>=6)  exit('�����������ܳ���6��');
	$setsqlarr['school'] = trim($_POST['school'])?trim($_POST['school']):exit('����дѧУ���ƣ�');
	$setsqlarr['speciality'] = trim($_POST['speciality'])?trim($_POST['speciality']):exit("����дרҵ���ƣ�");
	if(trim($_POST['startyear'])==""||trim($_POST['startmonth'])==""||trim($_POST['endyear'])==""){
		exit("��ѡ��Ͷ�ʱ�䣡");
	}
	$setsqlarr['startyear'] = intval($_POST['startyear']);
	$setsqlarr['startmonth'] = intval($_POST['startmonth']);
	//���� ���
	if(intval($_POST['endyear']) == -1)
	{
		$setsqlarr['endyear'] = 0;
		$setsqlarr['endmonth'] = 0;
		$setsqlarr['todate'] = 1;
	}
	else
	{
		$setsqlarr['endyear'] = intval($_POST['endyear']);
		$setsqlarr['endmonth'] = intval($_POST['endmonth']);
		$setsqlarr['todate'] = 0;
	}
	$setsqlarr['education'] = trim($_POST['education'])?trim($_POST['education']):exit("��ѡ����ѧ��");
	$setsqlarr['education_cn'] = trim($_POST['education_cn'])?trim($_POST['education_cn']):exit("��ѡ����ѧ��");
	if($id){
		$db->updatetable(table("resume_education"),$setsqlarr,array("id"=>$id,"uid"=>intval($_SESSION['uid'])));
		exit("ok");
	}else{
		$insert_id = $db->inserttable(table("resume_education"),$setsqlarr,1);
		if($insert_id){
			check_resume(intval($_SESSION['uid']),intval($_REQUEST['pid']));
			perfect_resume($_SESSION['uid'],$_SESSION['username'],$_REQUEST['pid'],1);
			exit("ok");
		}else{
			exit("err");
		}
	}
}
// ɾ����������
elseif($act == "resume_education_del")
{
	$smarty->cache = false;
	$id=intval($_GET['education_id']);
	$uid=intval($_SESSION["uid"]);
	$sql="delete from ".table("resume_education")." where id=$id and uid=$uid ";
	if($db->query($sql)){
		perfect_resume($_SESSION['uid'],$_SESSION['username'],$id,2);
		exit("ok");
	}else{
		exit("err");
	}
}
// ��ѵ���� 
elseif($act == "resume_train")
{
	$smarty->cache = false;
	$id=intval($_GET['pid']);
	$resume_train_list=get_resume_training(intval($_SESSION['uid']),$id);
	$smarty->assign("resume_train_list",$resume_train_list);
	$smarty->display('m/personal/m-train-experience.html');
}
elseif($act == "resume_train_add")
{
	$smarty->cache = false;
	$id=intval($_GET["id"]);
	$resume_train=get_this_training(intval($_SESSION['uid']),$id);
	if($resume_train){
		$smarty->assign('resume_train',$resume_train);
	}else{
		$smarty->assign('resume_train',false);
	}
	$smarty->display('m/personal/m-edit-train-experience.html');
}
elseif($act == "resume_train_save")
{
	// print_r($_POST);die;
	$_POST=array_map("utf8_to_gbk",$_POST);
	$id=intval($_POST['id']);
	$setsqlarr['uid'] = intval($_SESSION['uid']);
	$setsqlarr['pid'] = intval($_POST['pid']);
	if ($setsqlarr['uid']==0 || $setsqlarr['pid']==0 )exit('����������');
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_POST['pid']));
	if (empty($resume_basic)) exit('������д����������Ϣ');
	$resume_training=get_resume_training($_SESSION['uid'],intval($_POST['pid']));
	if (count($resume_training)>=6)  exit('��ѵ�������ܳ���6��');
	$setsqlarr['agency'] = trim($_POST['agency'])?trim($_POST['agency']):exit('����д��ѵ�������ƣ�');
	$setsqlarr['course'] = trim($_POST['course'])?trim($_POST['course']):exit("����д��ѵרҵ���ƣ�");
	if(trim($_POST['startyear'])==""||trim($_POST['startmonth'])==""||trim($_POST['endyear'])==""){
		exit("��ѡ����ѵʱ�䣡");
	}
	$setsqlarr['startyear'] = intval($_POST['startyear']);
	$setsqlarr['startmonth'] = intval($_POST['startmonth']);
	//���� ���
	if(intval($_POST['endyear']) == -1)
	{
		$setsqlarr['endyear'] = 0;
		$setsqlarr['endmonth'] = 0;
		$setsqlarr['todate'] = 1;
	}
	else
	{
		$setsqlarr['endyear'] = intval($_POST['endyear']);
		$setsqlarr['endmonth'] = intval($_POST['endmonth']);
		$setsqlarr['todate'] = 0;
	}
	$setsqlarr['description'] = trim($_POST['description'])?trim($_POST['description']):exit("����д��ѵ���ݣ�");
	if($id){
		$db->updatetable(table("resume_training"),$setsqlarr,array("id"=>$id,"uid"=>intval($_SESSION['uid'])));
		exit("ok");
	}else{
		$insert_id = $db->inserttable(table("resume_training"),$setsqlarr,1);
		if($insert_id){
			check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
			perfect_resume($_SESSION['uid'],$_SESSION['username'],$_REQUEST['pid'],1);
			exit("ok");
		}else{
			exit("err");
		}
	}	
}
elseif($act == "resume_train_del")
{
	$smarty->cache = false;
	$id=intval($_GET['train_id']);
	$uid=intval($_SESSION["uid"]);
	$sql="delete from ".table("resume_training")." where id=$id and uid=$uid ";
	if($db->query($sql)){
		perfect_resume($_SESSION['uid'],$_SESSION['username'],$id,2);
		exit("ok");
	}else{
		exit("err");
	}
}
// ��������
elseif($act == "resume_specialty")
{
	$smarty->cache = false;
	$id=intval($_GET['pid']);
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),$id);
	$smarty->assign('resume_basic',$resume_basic);
	$smarty->display('m/personal/m-evaluation.html');
}
elseif($act == "resume_specialty_save")
{
	$_POST=array_map("utf8_to_gbk",$_POST);
	$smarty->cache = false;
	$id=intval($_POST['pid']);
	$uid=intval($_SESSION["uid"]);
	$specialty=$_POST['specialty']?$_POST['specialty']:exit("����д��������");
	$sql="update ".table("resume")." set specialty='$specialty' where id=$id and uid=$uid ";
	if($db->query($sql)){
		perfect_resume($_SESSION['uid'],$_SESSION['username'],$id,1);
		exit("ok");
	}else{
		exit("err");
	}
}
// ����ˢ��
elseif($act == "resume_refresh")
{
	$smarty->cache = false;

	$resumeid = intval($_GET['pid']);
	$refrestime=get_last_refresh_date(intval($_SESSION['uid']),"2001");
	$duringtime=time()-$refrestime['max(addtime)'];
	$space = $_CFG['per_refresh_resume_space']*60;
	$refresh_time = get_today_refresh_times($_SESSION['uid'],"2001");
	if($_CFG['per_refresh_resume_time']!=0&&($refresh_time['count(*)']>=$_CFG['per_refresh_resume_time']))
	{
	exit("ÿ�����ֻ��ˢ��".$_CFG['per_refresh_resume_time']."��,�������ѳ������ˢ�´������ƣ�");	
	}
	elseif($duringtime<=$space)
	{
	exit($_CFG['per_refresh_resume_space']."�����ڲ����ظ�ˢ�¼�����");
	}
	else 
	{
	refresh_resume($resumeid,intval($_SESSION['uid']))?exit('ok'):exit("err");
	}
}
// ������˽����
elseif($act == "resume_privacy")
{
	$smarty->cache = false;
	$pid=intval($_GET['pid']);
	//���ε���ҵ
	$uid=intval($_SESSION["uid"]);
	$shield_company=$db->getall("select * from ".table("personal_shield_company")." where uid=$uid and pid=$pid");
	$smarty->assign('shield_company',$shield_company);
	$smarty->assign('resume_one',resume_one($pid));
	$smarty->display('wap/personal/privacy-settings.html');
}
elseif($act == "resume_privacy_save")
{
	$smarty->cache = false;
	$uid=intval($_SESSION['uid']);
	$pid=intval($_POST['pid']);
	$setsqlarr['display']=intval($_POST['display']);
	$setsqlarr['display_name']=intval($_POST['display_name']);
	// $setsqlarr['photo_display']=intval($_POST['photo_display']);
	!$db->updatetable(table('resume'),$setsqlarr," uid='{$uid}' AND  id='{$pid}'");
	$setsqlarrdisplay['display']=intval($_POST['display']);
	!$db->updatetable(table('resume_search_key'),$setsqlarrdisplay," uid='{$uid}' AND  id='{$pid}'");
	!$db->updatetable(table('resume_search_rtime'),$setsqlarrdisplay," uid='{$uid}' AND  id='{$pid}'");
	$rst=write_memberslog($uid,2,1104,$_SESSION['username'],"���ü�����˽({$pid})");
	if($rst){
		exit("ok");
	}else{
		exit("err");
	}
}
elseif($act == "resume_del")
{
	$smarty->cache = false;
	$id=intval($_GET["pid"]);
	$uid=intval($_SESSION['uid']);
	del_resume($uid,$id)?exit('ok'):exit('err');
}
// �����߼�����
elseif($act == "resume_talent")
{
	$smarty->cache = false;
	$id=intval($_GET["pid"]);
	$uid=intval($_SESSION['uid']);
	$resume=get_resume_basic($uid,$id);
	if ($resume['complete_percent']<$_CFG['elite_resume_complete_percent'])
	{
		exit("��������ָ��С��{$_CFG['elite_resume_complete_percent']}%����ֹ���룡");
	}
	else
	{
		$setsqlarr["talent"]=3;
		$db->updatetable(table("resume"),$setsqlarr,array("id"=>$id,"uid"=>intval($_SESSION['uid'])))?exit("ok"):exit("err");
	}
}
// �޸ļ�����
elseif($act == 'resume_name_save')
{
	$smarty->cache = false;
	$_POST=array_map("utf8_to_gbk", $_POST);
	$resume_id=intval($_POST["resume_id"]);
	$uid=intval($_SESSION["uid"]);
	$title=trim($_POST['title'])?trim($_POST['title']):exit("�������������");
	$sql="update ".table("resume")." set title='$title' where id=$resume_id and uid=$uid ";
	if($db->query($sql)){
		exit("ok");
	}else{
		exit("err");
	}
	
}
// �������ͷ��
elseif ($act =='logo_save')
{
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	!$_FILES['logo_img']['name']?exit('���ϴ�ͼƬ��'):"";
	$logo_dir="../../data/photo/".date("Y/m/d/");
	$logo_thumb_dir="../../data/photo/thumb/".date("Y/m/d/");
	make_dir($logo_dir);
	make_dir($logo_thumb_dir);
	$setsqlarr['photo_img']=_asUpFiles($logo_dir, "logo_img",$_CFG['logo_max_size'],'gif/jpg/bmp/png',true);
	if ($setsqlarr['photo_img'])
	{
		copy($logo_dir.$setsqlarr['photo_img'], $logo_thumb_dir.$setsqlarr['photo_img']);
		$setsqlarr['photo_img']=date("Y/m/d/").$setsqlarr['photo_img'];
		$setsqlarr['photo_audit']=intval($_CFG['audit_resume_photo']);
		$setsqlarr['photo']=1;
		$pid=intval($_GET["pid"]);
		if($pid <= 0)
		{
			exit("-8");
		}
		$wheresql="uid='".$_SESSION['uid']."' and id=".$pid;
		if(!$db->updatetable(table('resume'),$setsqlarr,$wheresql))
		{
			exit("-6");
		}
		else
		{
			$data['isok'] = 1;
			$json_encode = json_encode($data);
			exit($json_encode);
		}
	}
	else
	{
	exit("-6");
	}
}
?>