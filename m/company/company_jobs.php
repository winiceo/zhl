<?php
 /*
 * ������ְλ����ģ��
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
if ($_SESSION['uid']=='' || $_SESSION['username']==''||intval($_SESSION['utype'])==2)
{
	header("Location: ../login.php");
}
$user = get_user_info($_SESSION['uid']);
if($_CFG['login_com_audit_mobile'] && $user['mobile_audit']=="0")
{
	$str= "<script>";
	$str.="alert('������֤�ֻ���');";
	$str.="window.location.href='account_security.php';";
	$str.= "</script>";
	echo $str;
}
elseif ($act == 'index')
{
	$smarty->cache = false;
	$uid=intval($_SESSION["uid"]);
	$wheresql=" select * from ".table("jobs")." where uid=$_SESSION[uid] union all select * from ".table("jobs_tmp")." where uid=$uid ";
	$row=get_jobs($offset,$perpage,$wheresql,true);
	$smarty->assign('row',$row);
	$smarty->display("m/company/m-job-index.html");	
}
// ����ְλajax
elseif($act=="ajax_jobs_add")
{
	$company_info=get_company(intval($_SESSION['uid']));
	if($company_info['companyname'])
	{
		if ($_CFG['operation_mode']=="3")
		{
			$setmeal=get_user_setmeal($_SESSION['uid']);
			if (($setmeal['endtime']>time() || $setmeal['endtime']=="0") &&  $setmeal['jobs_ordinary']>0)
			{
				$add_mode = 2;
			}
			elseif($_CFG['setmeal_to_points']=="1")
			{
				$add_mode = 1;
			}
			else
			{
				$add_mode = 2;
			}
		}
		elseif ($_CFG['operation_mode']=="2")
		{
			$add_mode = 2;
		}
		elseif ($_CFG['operation_mode']=="1")
		{
			$add_mode = 1;
		}
		if ($add_mode=='1')
		{
			$points_rule=get_cache('points_rule');
			$user_points=get_user_points($_SESSION['uid']);
			if ($points_rule['jobs_add']['type']=="2" && $points_rule['jobs_add']['value']>0)
			{
				$total=$points_rule['jobs_add']['value'];
				if ($total>$user_points)
				{
					exit("���".$_CFG['points_byname']."���㣬���ֵ���ٷ�����");
				}
			}
		}
		elseif ($add_mode=='2')
		{
			$setmeal=get_user_setmeal($_SESSION['uid']);
			if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
			{					
				exit("���ķ����Ѿ����ڣ������¿�ͨ");
			}
			/*
				��ʾ�е�ְλ(���ͨ��,�����,δ��ͣ)
			*/
			$jobs_num= $db->get_total("select count(*) num from ".table("jobs")." where uid=$_SESSION[uid] and audit=1 and display=1 ");
			$jobs_tmp_num= $db->get_total("select count(*) num from ".table("jobs_tmp")." where uid=$_SESSION[uid] and audit=2 and display=1 ");
			$com_jobs_num=$jobs_num+$jobs_tmp_num;
			if ($com_jobs_num>=$setmeal['jobs_ordinary'])
			{
				exit("��ǰ��ʾ��ְλ�Ѿ�������������ƣ������������ײͣ�");
			}
		}
		exit("1");
	}else{
		exit("2");
		//header("Location: user.php?act=company_info_add");
	}
}
// ����ְλ
elseif($act=="jobs_add")
{
	$smarty->cache = false;
	$company_info=get_company(intval($_SESSION['uid']));
	if($company_info['companyname'])
	{
		$smarty->assign('company_info',$company_info);
		if ($_CFG['operation_mode']=="3")
		{
			$setmeal=get_user_setmeal($_SESSION['uid']);
			if (($setmeal['endtime']>time() || $setmeal['endtime']=="0") &&  $setmeal['jobs_ordinary']>0)
			{
			$smarty->assign('setmeal',$setmeal);
			$add_mode = 2;
			$smarty->assign('add_mode',2);
			}
			elseif($_CFG['setmeal_to_points']=="1")
			{
			$smarty->assign('points_total',get_user_points($_SESSION['uid']));
			$smarty->assign('points',get_cache('points_rule'));
			$add_mode = 1;
			$smarty->assign('add_mode',1);
			}
			else
			{
			$smarty->assign('setmeal',$setmeal);
			$add_mode = 2;
			$smarty->assign('add_mode',2);
			}
			
		}
		elseif ($_CFG['operation_mode']=="2")
		{
			$setmeal=get_user_setmeal($_SESSION['uid']);
			$smarty->assign('setmeal',$setmeal);
			$add_mode = 2;
			$smarty->assign('add_mode',2);
		}
		elseif ($_CFG['operation_mode']=="1")
		{
			$smarty->assign('points_total',get_user_points($_SESSION['uid']));
			$smarty->assign('points',get_cache('points_rule'));
			$add_mode = 1;
			$smarty->assign('add_mode',1);
		}
		if ($add_mode=='1')
		{
			$points_rule=get_cache('points_rule');
			$user_points=get_user_points($_SESSION['uid']);
			if ($points_rule['jobs_add']['type']=="2" && $points_rule['jobs_add']['value']>0)
			{
				$total=$points_rule['jobs_add']['value'];
				if ($total>$user_points)
				{
					exit("���".$_CFG['points_byname']."���㣬���ֵ���ٷ�����");
				}
			}
		}
		elseif ($add_mode=='2')
		{
			$setmeal=get_user_setmeal($_SESSION['uid']);
			if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
			{					
				exit("���ķ����Ѿ����ڣ������¿�ͨ");
			}
			/*
				��ʾ�е�ְλ(���ͨ��,�����,δ��ͣ)
			*/
			$jobs_num= $db->get_total("select count(*) num from ".table("jobs")." where uid=$_SESSION[uid] and audit=1 and display=1 ");
			$jobs_tmp_num= $db->get_total("select count(*) num from ".table("jobs_tmp")." where uid=$_SESSION[uid] and audit=2 and display=1 ");
			$com_jobs_num=$jobs_num+$jobs_tmp_num;
			if ($com_jobs_num>=$setmeal['jobs_ordinary'])
			{
				exit("��ǰ��ʾ��ְλ�Ѿ�������������ƣ������������ײͣ�");
			}
		}
		$smarty->display("m/company/m-create-job.html");
	}else{
		header("Location: user.php?act=company_info_add");
	}
}
// ����ְλ
elseif($act=="jobs_add_save")
{
	$smarty->cache = false;
	$company_info=get_company(intval($_SESSION['uid']));
	$company_info = array_map("addslashes", $company_info);
	$_POST=array_map("utf8_to_gbk", $_POST);
	$add_mode=trim($_POST['add_mode']);
	if ($add_mode=='1')
	{
		$points_rule=get_cache('points_rule');
		$user_points=get_user_points($_SESSION['uid']);
		$total=0;
		if ($points_rule['jobs_add']['type']=="2" && $points_rule['jobs_add']['value']>0)
		{
			$total=$points_rule['jobs_add']['value'];
			if ($total>$user_points)
			{
			exit("���".$_CFG['points_byname']."���㣬���ֵ���ٷ�����");
			}
		}
		if($_CFG['operation_mode']=="1")
		{
			$setsqlarr['setmeal_deadline']=0;
		}
		elseif($_CFG['operation_mode']=="3")
		{
			$setmeal=get_user_setmeal($_SESSION['uid']);
			if(empty($setmeal))
			{
				$setsqlarr['setmeal_deadline']=0;
			}
			else
			{
				$setsqlarr['setmeal_deadline']=0;
				$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
				$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
			}
		}
	}
	elseif ($add_mode=='2')
	{
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{					
			exit("���ķ����Ѿ����ڣ������¿�ͨ");
		}
		if ($setmeal['jobs_ordinary']<=0)
		{
			exit("��ǰ������ְλ�Ѿ�������������ƣ������������ײͣ�");
		}
		/*
			��ʾ�е�ְλ(���ͨ��,�����,δ��ͣ)
		*/
		$jobs_num= $db->get_total("select count(*) num from ".table("jobs")." where uid=$_SESSION[uid] and audit=1 and display=1 ");
		$jobs_tmp_num= $db->get_total("select count(*) num from ".table("jobs_tmp")." where uid=$_SESSION[uid] and audit=2 and display=1 ");
		$com_jobs_num=$jobs_num+$jobs_tmp_num;
		if ($com_jobs_num>=$setmeal['jobs_ordinary'])
		{
			exit("��ǰ��ʾ��ְλ�Ѿ�������������ƣ������������ײͣ�");
		}
		$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
		$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
		$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
	}
	$setsqlarr['add_mode']=intval($add_mode);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['companyname']=$company_info['companyname'];
	$setsqlarr['company_id']=$company_info['id'];
	$setsqlarr['company_addtime']=$company_info['addtime'];
	$setsqlarr['company_audit']=$company_info['audit'];
	$setsqlarr['jobs_name']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name']):exit('����дְλ���ƣ�');
	$setsqlarr['nature']=62;
	$setsqlarr['nature_cn']="ȫְ";
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):exit('��ѡ��ְλ���');
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['amount']=0;
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):exit('��û����дְλ������');
	$setsqlarr['wage']=intval($_POST['wage'])?intval($_POST['wage']):exit('��ѡ��н�ʴ�����');		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['subsite_id']=!empty($_POST['subsite_id'])?intval($_POST['subsite_id']):exit('��ѡ����������');
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):exit('��ѡ����������');
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['tag']=trim($_POST['tag']);		
	$setsqlarr['tag_cn']=trim($_POST['tag_cn']);
	$setsqlarr['sex']=3;
	$setsqlarr['sex_cn']="����";
	check_word($_CFG['filter'],$_POST['contents'])?exit($_CFG['filter_tips']):'';
	$setsqlarr['trade']=$company_info['trade'];
	$setsqlarr['trade_cn']=$company_info['trade_cn'];
	$setsqlarr['scale']=$company_info['scale'];
	$setsqlarr['scale_cn']=$company_info['scale_cn'];
	$setsqlarr['street']=$company_info['street'];
	$setsqlarr['street_cn']=$company_info['street_cn'];
	$setsqlarr['addtime']=time();
	$setsqlarr['deadline']=strtotime("".intval($_CFG['company_add_days'])." day");
	$setsqlarr['refreshtime']=time();
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_info['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_info['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$setsqlarr['tpl']=$company_info['tpl'];
	$setsqlarr['map_x']=$company_info['map_x'];
	$setsqlarr['map_y']=$company_info['map_y'];
	if ($company_info['audit']=="1")
	{
	$setsqlarr['audit']=intval($_CFG['audit_verifycom_addjob']);
	}
	else
	{
	$setsqlarr['audit']=intval($_CFG['audit_unexaminedcom_addjob']);
	}
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):exit('��û����д��ϵ�ˣ�');
	$setsqlarr_contact['address']=!empty($_POST['address'])?trim($_POST['address']):exit('��û����д��ϸ��ַ��');
	$setsqlarr_contact['email']=!empty($_POST['email'])?trim($_POST['email']):exit('��û����д���䣡');
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):'';
	check_word($_CFG['filter'],$_POST['telephone'])?exit($_CFG['filter_tips']):'';

	$setsqlarr_contact['contact_show']=1;
	$setsqlarr_contact['email_show']=1;
	$setsqlarr_contact['telephone_show']=1;
	$setsqlarr_contact['address_show']=1;
	//����
	$landline_tel[]=trim($_POST['landline_tel_first'])?trim($_POST['landline_tel_first']):"0";
	$landline_tel[]=trim($_POST['landline_tel_next'])?trim($_POST['landline_tel_next']):"0";
	$landline_tel[]=trim($_POST['landline_tel_last'])?trim($_POST['landline_tel_last']):"0";
	$setsqlarr_contact['landline_tel']=implode('-', $landline_tel);
	//�������ֻ����ٶ�ѡһ
	if(empty($setsqlarr_contact['telephone']) && $setsqlarr_contact['landline_tel']=='0-0-0')
	{
		exit('����д�ֻ���̻�����ѡһ���ɣ�');
	}

	//���ְλ��Ϣ
	$pid=$db->inserttable(table('jobs'),$setsqlarr,1);
	if(empty($pid)){
		exit("err");
	}
	//�����ϵ��ʽ
	$setsqlarr_contact['pid']=$pid;
	if(!$db->inserttable(table('jobs_contact'),$setsqlarr_contact))exit("��ϵ��ʽ����");
	if ($add_mode=='1')
	{
		if ($points_rule['jobs_add']['value']>0)
		{
		report_deal($_SESSION['uid'],$points_rule['jobs_add']['type'],$points_rule['jobs_add']['value']);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_add']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"������ְλ��<strong>{$setsqlarr['jobs_name']}</strong>��({$operator}{$points_rule['jobs_add']['value']})��(ʣ��:{$user_points})",1,1001,"����ְλ","{$operator}{$points_rule['jobs_add']['value']}","{$user_points}");
		}
	}	
	elseif ($add_mode=='2')
	{
		//action_user_setmeal($_SESSION['uid'],"jobs_ordinary");
		$setmeal=get_user_setmeal($_SESSION['uid']);
		write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"������ְͨλ:<strong>{$_POST['jobs_name']}</strong>�������Է�����ְͨλ:<strong>{$setmeal['jobs_ordinary']}</strong>��",2,1001,"����ְλ","1","{$setmeal['jobs_ordinary']}");
	}
	$searchtab['id']=$pid;
	$searchtab['uid']=$setsqlarr['uid'];
	$searchtab['subsite_id']=$setsqlarr['subsite_id'];
	$searchtab['recommend']=$setsqlarr['recommend'];
	$searchtab['emergency']=$setsqlarr['emergency'];
	$searchtab['nature']=$setsqlarr['nature'];
	$searchtab['sex']=$setsqlarr['sex'];
	$searchtab['topclass']=$setsqlarr['topclass'];
	$searchtab['category']=$setsqlarr['category'];
	$searchtab['subclass']=$setsqlarr['subclass'];
	$searchtab['trade']=$setsqlarr['trade'];
	$searchtab['district']=$setsqlarr['district'];
	$searchtab['sdistrict']=$setsqlarr['sdistrict'];	
	$searchtab['street']=$company_info['street'];
	$searchtab['education']=$setsqlarr['education'];
	$searchtab['experience']=$setsqlarr['experience'];
	$searchtab['wage']=$setsqlarr['wage'];
	$searchtab['refreshtime']=$setsqlarr['refreshtime'];
	$searchtab['scale']=$setsqlarr['scale'];	
	$searchtab['setmeal_id']=$setsqlarr['setmeal_id'];
	//
	$db->inserttable(table('jobs_search_wage'),$searchtab);
	$db->inserttable(table('jobs_search_scale'),$searchtab);
	//
	$searchtab['map_x']=$setsqlarr['map_x'];
	$searchtab['map_y']=$setsqlarr['map_y'];
	$db->inserttable(table('jobs_search_rtime'),$searchtab);

	unset($searchtab['map_x'],$searchtab['map_y']);
	//
	$searchtab['stick']=$setsqlarr['stick'];
	$db->inserttable(table('jobs_search_stickrtime'),$searchtab);

	unset($searchtab['stick']);
	//
	$searchtab['click']=$setsqlarr['click'];
	$db->inserttable(table('jobs_search_hot'),$searchtab);

	unset($searchtab['click']);
	//
	$searchtab['key']=$setsqlarr['key'];
	$searchtab['likekey']=$setsqlarr['jobs_name'].','.$setsqlarr['companyname'];
	$searchtab['map_x']=$setsqlarr['map_x'];
	$searchtab['map_y']=$setsqlarr['map_y'];
	$db->inserttable(table('jobs_search_key'),$searchtab);
	unset($searchtab);
	add_jobs_tag($pid,$_SESSION['uid'],$_POST['tag'])?"":exit('err');
	distribution_jobs($pid,$_SESSION['uid']);
	write_memberslog($_SESSION['uid'],1,2001,$_SESSION['username'],"������ְλ��{$setsqlarr['jobs_name']}");
	baidu_submiturl(url_rewrite('QS_jobsshow',array('id'=>$pid)),'addjob');
	echo $pid;
}
// ְλ�����ɹ�ҳ��
elseif($act=="addjobs_save_succeed")
{
	$smarty->cache = false;
	$jobs_id=intval($_GET["id"]);
	$jobs=jobs_one($jobs_id);
	$sql="select * from ".table("resume")." as r left join ".table("resume_jobs")." as rj on rj.pid=r.id where rj.category=$jobs[category] limit 5 ";
	$resume_list=$db->getall($sql);
	foreach ($resume_list as $key => $val) {
		$val['age']=date("Y")-$val['birthdate'];
		$resume_list[$key]=$val;
	}
	$smarty->assign('resume_list',$resume_list);
	$smarty->display("m/company/m-job-index.html");
}
// ְλ�޸� ҳ��
elseif($act=="jobs_edit")
{
	$smarty->cache = false;
	$jobs=get_jobs_one($_GET['id'],$_SESSION['uid']);
	if($jobs){
		$jobs['contents'] = strip_tags(htmlspecialchars_decode($jobs['contents'],ENT_QUOTES));
		//���������зָ�
		$telarray = explode('-',$jobs['contact']['landline_tel']);
		if(intval($telarray[0]) > 0)
		{
			$jobs['contact']['landline_tel_first'] = $telarray[0];
		}
		if(intval($telarray[1]) > 0)
		{
			$jobs['contact']['landline_tel_next'] = $telarray[1];
		}
		if(intval($telarray[2]) > 0)
		{
			$jobs['contact']['landline_tel_last'] = $telarray[2];
		}
	}
	$company_info=get_company($_SESSION['uid']);
	$smarty->assign('company_info',$company_info);
	$smarty->assign('jobs',$jobs);
	$smarty->display("m/company/m-jobs-edit.html");
}
elseif($act=="jobs_edit_save")
{
	$smarty->cache = false;
	$company_info=get_company($_SESSION['uid']);
	$company_info = array_map("addslashes", $company_info);
	$id=intval($_POST['id']);
	$_POST=array_map("utf8_to_gbk", $_POST);
	// var_dump($_POST);die;
	$add_mode=trim($_POST['add_mode']);
	if ($add_mode=='1')
	{
		$points_rule=get_cache('points_rule');
		$user_points=get_user_points($_SESSION['uid']);
		$total=0;
		if($points_rule['jobs_edit']['type']=="2" && $points_rule['jobs_edit']['value']>0)
		{
			$total=$points_rule['jobs_edit']['value'];
			if ($total>$user_points)
			{
			exit("���".$_CFG['points_byname']."���㣬���ֵ���ٷ�����");
			}
		}
	}
	elseif ($add_mode=='2')
	{
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{					
			exit("�����ײ��Ѿ����ڣ������¿�ͨ");
		}
	}
	$setsqlarr['jobs_name']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name']):exit('����дְλ���ƣ�');
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):exit('��ѡ��ְλ���');
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['amount']=0;
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):exit('��û����дְλ������');
	$setsqlarr['wage']=intval($_POST['wage'])?intval($_POST['wage']):exit('��ѡ��н�ʴ�����');		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['tag']=trim($_POST['tag']);		
	$setsqlarr['tag_cn']=trim($_POST['tag_cn']);
	$setsqlarr['subsite_id']=!empty($_POST['subsite_id'])?intval($_POST['subsite_id']):exit('��ѡ����������');
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):exit('��ѡ����������');
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	check_word($_CFG['filter'],$_POST['contents'])?exit($_CFG['filter_tips']):'';
	if ($add_mode=='1'){
		$setsqlarr['setmeal_deadline']=0;
		$setsqlarr['add_mode']=1;
	}elseif($add_mode=='2'){
		$setmeal=get_user_setmeal($_SESSION['uid']);
		$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
		$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
		$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
		$setsqlarr['add_mode']=2;
	}
	$setsqlarr['deadline']=strtotime("".intval($_CFG['company_add_days'])." day");
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_info['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_info['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if ($company_info['audit']=="1")
	{
	$_CFG['audit_verifycom_editjob']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_verifycom_editjob']):'';
	}
	else
	{
	$_CFG['audit_unexaminedcom_editjob']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_unexaminedcom_editjob']):'';
	}
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):exit('��û����д��ϵ�ˣ�');
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):'';
	$setsqlarr_contact['email']=!empty($_POST['email'])?trim($_POST['email']):exit('��û����д��ϵ���䣡');
	$setsqlarr_contact['address']=!empty($_POST['address'])?trim($_POST['address']):exit('��û����д��ϸ��ַ��');
	check_word($_CFG['filter'],$_POST['telephone'])?exit($_CFG['filter_tips']):'';

	$setsqlarr_contact['contact_show']=1;
	$setsqlarr_contact['email_show']=1;
	$setsqlarr_contact['telephone_show']=1;
	$setsqlarr_contact['address_show']=1;
	//����
	$landline_tel[]=trim($_POST['landline_tel_first'])?trim($_POST['landline_tel_first']):"0";
	$landline_tel[]=trim($_POST['landline_tel_next'])?trim($_POST['landline_tel_next']):"0";
	$landline_tel[]=trim($_POST['landline_tel_last'])?trim($_POST['landline_tel_last']):"0";
	$setsqlarr_contact['landline_tel']=implode('-', $landline_tel);
	//�������ֻ����ٶ�ѡһ
	if(empty($setsqlarr_contact['telephone']) && $setsqlarr_contact['landline_tel']=='0-0-0')
	{
		exit('����д�ֻ���̻�����ѡһ���ɣ�');
	}

	if (!$db->updatetable(table('jobs'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) exit("err");
	if (!$db->updatetable(table('jobs_tmp'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) exit("err");
	if (!$db->updatetable(table('jobs_contact'), $setsqlarr_contact," pid='{$id}' ")){
		exit("err");
	}
	if ($add_mode=='1')
	{
		if ($points_rule['jobs_edit']['value']>0)
		{
		report_deal($_SESSION['uid'],$points_rule['jobs_edit']['type'],$points_rule['jobs_edit']['value']);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_edit']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"�޸�ְλ��<strong>{$setsqlarr['jobs_name']}</strong>��({$operator}{$points_rule['jobs_edit']['value']})��(ʣ��:{$user_points})",1,1002,"�޸���Ƹ��Ϣ","{$operator}{$points_rule['jobs_edit']['value']}","{$user_points}");
		}
	}
	//��ǩ
	add_jobs_tag(intval($_POST['id']),$_SESSION['uid'],$_POST['tag'])?"":exit("err");
	$searchtab['topclass']=$setsqlarr['topclass'];
	$searchtab['category']=$setsqlarr['category'];
	$searchtab['subclass']=$setsqlarr['subclass'];
	$searchtab['subsite_id']=$setsqlarr['subsite_id'];
	$searchtab['district']=$setsqlarr['district'];
	$searchtab['sdistrict']=$setsqlarr['sdistrict'];
	$searchtab['wage']=$setsqlarr['wage'];
	//
	$db->updatetable(table('jobs_search_wage'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	$db->updatetable(table('jobs_search_rtime'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	$db->updatetable(table('jobs_search_stickrtime'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	$db->updatetable(table('jobs_search_hot'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	$db->updatetable(table('jobs_search_scale'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}'");
	$searchtab['key']=$setsqlarr['key'];
	$searchtab['likekey']=$setsqlarr['jobs_name'].','.$company_profile['companyname'];
	$db->updatetable(table('jobs_search_key'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	unset($searchtab);
	// distribution_jobs($id,$_SESSION['uid']);
	write_memberslog($_SESSION['uid'],$_SESSION['utype'],2002,$_SESSION['username'],"�޸���ְλ��{$setsqlarr['jobs_name']}��ְλID��{$id}");
	exit("ok");
}
// ְλˢ��
elseif($act=="jobs_refresh")
{
	$smarty->cache = false;
	$id=intval($_POST['id']);
	$jobs_num = 1;
	//����ģʽ
	if($_CFG['operation_mode']=='1'){
		//����ˢ��ʱ��
		//�һ�ε�ˢ��ʱ��
		$refrestime=get_last_refresh_date($_SESSION['uid'],"1001");
		$duringtime=time()-$refrestime['max(addtime)'];
		$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001");
		if($_CFG['com_pointsmode_refresh_time']!=0&&($refresh_time['count(*)']>=$_CFG['com_pointsmode_refresh_time']))
		{
		exit("ÿ�����ֻ��ˢ��".$_CFG['com_pointsmode_refresh_time']."��,�������ѳ������ˢ�´������ƣ�");	
		}
		elseif($duringtime<=$space){
		exit($_CFG['com_pointsmode_refresh_space']."�����ڲ����ظ�ˢ��ְλ��");
		}
		else 
		{	
			$points_rule=get_cache('points_rule');
			if($points_rule['jobs_refresh']['value']>0)
			{
				$user_points=get_user_points($_SESSION['uid']);
				$total_point=$jobs_num*$points_rule['jobs_refresh']['value'];
				if ($total_point>$user_points && $points_rule['jobs_refresh']['type']=="2")
				{
						$link[0]['text'] = "������һҳ";
						$link[0]['href'] = 'javascript:history.go(-1)';
						$link[1]['text'] = "������ֵ";
						$link[1]['href'] = 'company_service.php?act=order_add';
				showmsg("����".$_CFG['points_byname']."���㣬���ȳ�ֵ��",0,$link);
				}
				report_deal($_SESSION['uid'],$points_rule['jobs_refresh']['type'],$total_point);
				$user_points=get_user_points($_SESSION['uid']);
				$operator=$points_rule['jobs_refresh']['type']=="1"?"+":"-";
				write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"ˢ����{$jobs_num}��ְλ��({$operator}{$total_point})��(ʣ��:{$user_points})",1,1003,"ˢ��ְλ","{$operator}{$total_point}","{$user_points}");
				write_refresh_log($_SESSION['uid'],1001);	
				refresh_jobs($id,intval($_SESSION['uid']))?exit("ok"):exit("err");
			}
			write_refresh_log($_SESSION['uid'],1001);
			refresh_jobs($id,intval($_SESSION['uid']))?exit("ok"):exit("err");
		}
	}	
	//�ײ�ģʽ
	elseif($_CFG['operation_mode']=='2') 
	{
		//����ˢ��ʱ��
		//�һ�ε�ˢ��ʱ��
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if (empty($setmeal))
		{					
			exit("����û�п�ͨ�����뿪ͨ");
		}
		elseif ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{					
			exit("���ķ����Ѿ����ڣ������¿�ͨ");
		}
		else
		{
			$refrestime=get_last_refresh_date($_SESSION['uid'],"1001");
			$duringtime=time()-$refrestime['max(addtime)'];
			$space = $setmeal['refresh_jobs_space']*60;
			$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001");
			if($setmeal['refresh_jobs_time']!=0&&($refresh_time['count(*)']>=$setmeal['refresh_jobs_time']))
			{
			exit("ÿ�����ֻ��ˢ��".$setmeal['refresh_jobs_time']."��,�������ѳ������ˢ�´������ƣ�");
			}
			elseif($duringtime<=$space){
			exit($setmeal['refresh_jobs_space']."�����ڲ����ظ�ˢ��ְλ��");	
			}
			write_refresh_log($_SESSION['uid'],1001);
			refresh_jobs($id,intval($_SESSION['uid']))?exit("ok"):exit("err");
		}
	}
	//���ģʽ
	elseif($_CFG['operation_mode']=='3') 
	{
		//����ˢ��ʱ��
		//�һ�ε�ˢ��ʱ��
		$setmeal=get_user_setmeal($_SESSION['uid']);
		$refrestime=get_last_refresh_date($_SESSION['uid'],"1001");
		$duringtime=time()-$refrestime['max(addtime)'];
		$space = $setmeal['refresh_jobs_space']*60;
		$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001");
		if($setmeal['refresh_jobs_time']!=0&&($refresh_time['count(*)']>=$setmeal['refresh_jobs_time']))
		{
		exit("ÿ�����ֻ��ˢ��".$setmeal['refresh_jobs_time']."��,�������ѳ������ˢ�´������ƣ�");
		}
		elseif($duringtime<=$space){
		exit($setmeal['refresh_jobs_space']."�����ڲ����ظ�ˢ��ְλ��");	
		}
		else
		{
			$points_rule=get_cache('points_rule');
			if($points_rule['jobs_refresh']['value']>0)
			{
				$user_points=get_user_points($_SESSION['uid']);
				$total_point=$jobs_num*$points_rule['jobs_refresh']['value'];
				if ($total_point>$user_points && $points_rule['jobs_refresh']['type']=="2")
				{
				exit("����".$_CFG['points_byname']."���㣬���ȳ�ֵ��");
				}
				report_deal($_SESSION['uid'],$points_rule['jobs_refresh']['type'],$total_point);
				$user_points=get_user_points($_SESSION['uid']);
				$operator=$points_rule['jobs_refresh']['type']=="1"?"+":"-";
				write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"ˢ����{$jobs_num}��ְλ��({$operator}{$total_point})��(ʣ��:{$user_points})",1,1003,"ˢ��ְλ","{$operator}{$total_point}","{$user_points}");
				write_refresh_log($_SESSION['uid'],1001);
				refresh_jobs($id,intval($_SESSION['uid']))?exit("ok"):exit("err");
			}
			else
			{
				write_refresh_log($_SESSION['uid'],1001);
				refresh_jobs($id,intval($_SESSION['uid']))?exit("ok"):exit("err");
			}
		}
	}
}
// ��ͣ ְλ 
elseif($act=="jobs_pause")
{
	$smarty->cache = false;
	$id=intval($_POST['id']);
	$uid=intval($_SESSION["uid"]);
	activate_jobs($id,2,$_SESSION['uid']);
	exit("ok");
}
// ��ְͣλ �ָ�
elseif($act=="jobs_regain")
{
	$smarty->cache = false;
	$id=intval($_POST['id']);
	$uid=intval($_SESSION["uid"]);
	$jobs_num= $db->get_total("select count(*) num from ".table("jobs")." where uid=$_SESSION[uid] and audit=1 and display=1 ");
	$jobs_tmp_num= $db->get_total("select count(*) num from ".table("jobs_tmp")." where uid=$_SESSION[uid] and audit<>3 and display=1 ");
	$com_jobs_num=$jobs_num+$jobs_tmp_num;
	$setmeal= get_user_setmeal($_SESSION['uid']);
	if ($com_jobs_num>=$setmeal['jobs_ordinary'])
	{
		exit("��ǰ��ʾ��ְλ�Ѿ�������������ƣ������������ײͣ��򽫲���Ƹ��ְλ��Ϊ�رգ�");
	}else
	{
		activate_jobs($id,1,$_SESSION['uid']);
		exit("ok");
	}
}
// ɾ��ְλ 
elseif($act=="jobs_del")
{
	$smarty->cache = false;
	$id=intval($_POST['id']);
	del_jobs($id,intval($_SESSION['uid']))?exit("ok"):exit("err");
}
// ְλ�ƹ�
elseif($act=="set_promotion")
{
	$catid = intval($_GET['catid'])?intval($_GET['catid']):1;
	$jobid = intval($_GET['jobid'])?intval($_GET['jobid']):exit("��������");
	$uid = intval($_SESSION['uid'])?intval($_SESSION['uid']):exit("��������");
	$jobinfo = get_jobs_one($jobid);
	$promotion = get_promotion_category_one($catid);
	if ($_CFG['operation_mode']=='2')
	{
		$setmeal=get_user_setmeal($uid);//��ȡ��Ա�ײ�
		if($setmeal['endtime']<time() && $setmeal['endtime']<>'0')
		{
			$end=1;//�ж��ײ��Ƿ���
			exit("�ײ��ѵ��ڣ�");
		}
		else
		{
			$data=get_setmeal_promotion($uid,$catid);//��ȡ��Աĳ���ƹ��ʣ�����������������ƣ�������
			exit("2"."|".$data['days']);
		}
	}
	elseif($_CFG['operation_mode']=='1')
	{
		$points = get_user_points($uid);
		exit("1"."|".$promotion['cat_points']."|".$promotion['cat_minday']."|".$promotion['cat_maxday']);
	}
	elseif($_CFG['operation_mode']=='3')
	{
		$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
		if($setmeal['endtime']<time() && $setmeal['endtime']<>'0')
		{
			if($_CFG['setmeal_to_points']!=1)
			{
				exit("�ײ��ѵ��ڣ�");
			}
			else
			{
				$points = get_user_points($uid);
				exit("1"."|".$promotion['cat_points']."|".$promotion['cat_minday']."|".$promotion['cat_maxday']);
			}
		}
		else
		{
			$data=get_setmeal_promotion($uid,$catid);//��ȡ��Աĳ���ƹ��ʣ�����������������ƣ�������
			exit("2"."|".$data['days']);
		}
	}
}
// ְλ�ƹ���ģʽ���жϸ��ƹ������Ƿ�ʹ�û���
elseif($act=="set_promotion_operation")
{
	$catid = intval($_GET['catid'])?intval($_GET['catid']):exit("��������");
	$uid = intval($_SESSION['uid'])?intval($_SESSION['uid']):exit("��������");
	if($_CFG['operation_mode'] != 3)
	{
		exit('ģʽ����');
	}
	$data=get_setmeal_promotion($uid,$catid);//��ȡ��Աĳ���ƹ��ʣ�����������������ƣ�������
	if($data['num'] >= 1)
	{
		exit("2"."|".$data['days']);
	}
	else
	{
		$promotion = get_promotion_category_one($catid);
		exit("1"."|".$promotion['cat_points']."|".$promotion['cat_minday']."|".$promotion['cat_maxday']);
	}
}
// ����ģʽ���жϷ��ظ��ƹ����͵� ÿ�����Ļ��֡��ƹ���С�������������
elseif($act=="set_promotion_points")
{
	$catid = intval($_GET['catid'])?intval($_GET['catid']):exit("��������");
	$uid = intval($_SESSION['uid'])?intval($_SESSION['uid']):exit("��������");
	$promotion = get_promotion_category_one($catid);
	if($promotion)
	{
		exit("1"."|".$promotion['cat_points']."|".$promotion['cat_minday']."|".$promotion['cat_maxday']);
	}
	else
	{
		exit("��������");
	}
}
// �ײ�ģʽ���жϷ��ظ��ƹ����͵� �ƹ�����
elseif($act=="set_promotion_setmeal")
{
	$catid = intval($_GET['catid'])?intval($_GET['catid']):exit("��������");
	$uid = intval($_SESSION['uid'])?intval($_SESSION['uid']):exit("��������");
	$data=get_setmeal_promotion($uid,$catid);//��ȡ��Աĳ���ƹ��ʣ�����������������ƣ�������
	if($data)
	{
		exit("2"."|".$data['days']);
	}
	else
	{
		exit("��������");
	}
}
// �����ƹ�
elseif($act=="set_promotion_save")
{
	// typeid : ��Ӫģʽ
	$uid = intval($_SESSION['uid'])?intval($_SESSION['uid']):exit("��������");
	$catid = intval($_GET['catid'])?intval($_GET['catid']):exit("��������");
	$typeid = intval($_GET['typeid'])?intval($_GET['typeid']):exit("��������");
	$jobid = intval($_GET['jobid'])?intval($_GET['jobid']):exit("��������");
	$day = intval($_GET['day'])?intval($_GET['day']):exit("��������");
	$jobs=get_jobs_one($jobid,$uid);
	$jobs = array_map("addslashes", $jobs);
	if ($jobid>0 && $day>0)
	{
		$pro_cat=get_promotion_category_one($catid);
		if($typeid == 1)
		{
			if ($pro_cat['cat_points']>0)
			{
				$points=$pro_cat['cat_points']*$day;
				$user_points=get_user_points($uid);
				if ($points>$user_points)
				{
					exit("���".$_CFG['points_byname']."�������д˴β��������ȳ�ֵ��");
				}
			}
		}
		elseif($typeid == 2)
		{
			$setmeal=get_setmeal_promotion($uid,$catid);//��ȡ��Ա�ײ�
			$num=$setmeal['num'];
			if(($setmeal['endtime']<time() && $setmeal['endtime']<>'0') || $num<=0)
			{
				exit("����ײ��ѵ��ڻ��ײ���ʣ��{$pro_cat['cat_name']}�������뾡�쿪ͨ���ײ�");
			}
		}
		$info=get_promotion_one($jobid,$uid,$catid);
		if (!empty($info))
		{
			exit("��ְλ�����ƹ��У���ѡ������ְλ����������");
		}
		$setsqlarr['cp_available']=1;
		$setsqlarr['cp_promotionid']=$catid;
		$setsqlarr['cp_uid']=$uid;
		$setsqlarr['cp_jobid']=$jobid;
		$setsqlarr['cp_days']=$day;
		$setsqlarr['cp_starttime']=time();
		$setsqlarr['cp_endtime']=strtotime("{$day} day");
		$color = get_color();
		$val_code = $color[0]['value'];
		$setsqlarr['cp_val']=$val_code;
		if ($db->inserttable(table('promotion'),$setsqlarr))
		{
			set_job_promotion($jobid,$setsqlarr['cp_promotionid'],$val_code);
			if($typeid == 1 && $pro_cat['cat_points']>0)
			{
				report_deal($uid,2,$points);
				$user_points=get_user_points($uid);
				write_memberslog($uid,1,9001,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$day} �죬(-{$points})��(ʣ��:{$user_points})",1,1018,"{$pro_cat['cat_name']}","-{$points}","{$user_points}");
			}
			elseif($typeid == 2)
			{
				$user_pname=trim($_POST['pro_name']);
				action_user_setmeal($uid,$setmeal['name']); //�����ײ�����Ӧ�ƹ㷽ʽ������
				$setmeal=get_user_setmeal($uid);//��ȡ��Ա�ײ�
				write_memberslog($uid,1,9002,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$day} �죬�ײ���ʣ��{$pro_cat['cat_name']}������{$setmeal[$user_pname]}����",2,1018,"{$pro_cat['cat_name']}","-{$day}","{$setmeal[$user_pname]}");//9002���ײͲ���
			}
			write_memberslog($uid,1,3004,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$day} �졣");
			exit('ok');
		}
		else
		{
			exit("�ƹ�ʧ�ܣ�");
		}
	}
	else
	{
		exit("ְλ�����ƹ�������������");
	}
}

?>