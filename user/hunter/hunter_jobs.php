<?php
/*
 * 74cms ��ѵ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/hunter_common.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'jobs';
$smarty->assign('leftmenu',"jobs");
if ($act=='jobs')
{
	$wheresql=" WHERE uid='{$_SESSION['uid']}' AND utype={$_SESSION['utype']} ";
	$orderby=" order by refreshtime desc";
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('hunter_jobs').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('title','ְλ���� - ��ͷ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$sql="SELECT * FROM ".table('hunter_jobs').$wheresql.$orderby;
	$smarty->assign('hunterjobs',get_hunterjobs($offset,$perpage,$sql,true));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('points_rule',get_cache('points_rule'));
	
	$setmeal=get_user_setmeal($_SESSION['uid']); 
	$smarty->assign('setmeal',$setmeal);
	$total=$db->get_total("SELECT COUNT(*) AS num FROM ".table('hunter_jobs')." WHERE uid='{$_SESSION['uid']}'"); 
	$smarty->assign('total',$total); 
	$smarty->display('member_hunter/hunter_jobs.htm');
}
elseif ($act=='addjobs')
{
		$smarty->assign('user',$user);
		if ($hunter_profile['huntername'])
		{
			$_SESSION['addrand']=rand(1000,5000);
			$smarty->assign('addrand',$_SESSION['addrand']);
			$smarty->assign('title','����ְλ - ��ͷ��Ա���� - '.$_CFG['site_name']);
			$smarty->assign('hunter_profile',$hunter_profile);
			if ($_CFG['operation_hunter_mode']=="2")
			{
				$link[0]['text'] = "������ͨ����";
				$link[0]['href'] = 'hunter_service.php?act=setmeal_list';
				$link[1]['text'] = "��Ա������ҳ";
				$link[1]['href'] = 'hunter_index.php?act=';
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if ($setmeal['endtime']<time() && $setmeal['endtime']<>'0')
				{					
					showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
				}
				if ($setmeal['jobs_add']<=0)
				{
					showmsg("��ǰ������ְλ�Ѿ�������������ƣ������������ײͣ�",1,$link);
				}
				$smarty->assign('setmeal',$setmeal);
				$smarty->assign('add_mode',2);
			}
			elseif ($_CFG['operation_hunter_mode']=="1")
			{
				$points_rule=get_cache('points_rule');
				$user_points=get_user_points($_SESSION['uid']);
				$total=0;
				if ($points_rule['hunter_hunterjobs_add']['type']=="2" && $points_rule['hunter_hunterjobs_add']['value']>0)
				{
				$total=$points_rule['hunter_hunterjobs_add']['value'];
				}
				if ($points_rule['hunter_hunterjobs_daily']['type']=="2" && $points_rule['hunter_hunterjobs_daily']['value']>0)
				{
				$total=$total+($_CFG['hunter_add_days_min']*$points_rule['hunter_hunterjobs_daily']['value']);
				}
				if ($total>$user_points)
				{
				$link[0]['text'] = "������ֵ";
				$link[0]['href'] = 'hunter_service.php?act=order_add';
				$link[1]['text'] = "��Ա������ҳ";
				$link[1]['href'] = 'hunter_index.php?act=';
				showmsg("���".$_CFG['hunter_points_byname']."���㣬���ֵ���ٷ�����",0,$link);
				}
				$smarty->assign('points_total',$user_points);
				$smarty->assign('points',$points_rule);
				$smarty->assign('add_mode',1);
			}
			
			$captcha=get_cache('captcha');
			$smarty->assign('verify_addjob',$captcha['verify_addjob']);
			$smarty->assign('subsite',get_all_subsite());
			$smarty->display('member_hunter/hunter_addjobs.htm');
		}
		else
		{
		$link[0]['text'] = "������ͷ����";
		$link[0]['href'] = 'hunter_info.php?act=hunter_profile';
		showmsg("Ϊ�˴ﵽ���õ���ƸЧ������������������ͷ���ϣ�",1,$link);
		}
}
elseif ($act=='addjobs_save')
{
	$captcha=get_cache('captcha');
	$postcaptcha = trim($_POST['postcaptcha']);
	if($captcha['verify_addjob']=='1' && empty($postcaptcha))
	{
		showmsg("����д��֤��",1);
 	}
	if ($captcha['verify_addjob']=='1' && strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
	{
		showmsg("��֤�����",1);
	}
	$add_mode=trim($_POST['add_mode']);
	$days=intval($_POST['days']);
				if ($days<$_CFG['hunter_add_days_min'])
				{
				showmsg("��Чʱ������Ϊ ".$_CFG['hunter_add_days_min']." �죡",1);
				}
	if ($_CFG['operation_hunter_mode']=='1')
	{
					$points_rule=get_cache('points_rule');
					$user_points=get_user_points($_SESSION['uid']);
					$total=0;
					if ($points_rule['hunter_hunterjobs_add']['type']=="2" && $points_rule['hunter_hunterjobs_add']['value']>0)
					{
					$total=$points_rule['hunter_hunterjobs_add']['value'];
					}
					if ($points_rule['hunter_hunterjobs_daily']['type']=="2" && $points_rule['hunter_hunterjobs_daily']['value']>0)
					{
					$total=$total+($days*$points_rule['hunter_hunterjobs_daily']['value']);
					}
					if ($total>$user_points)
					{
					$link[0]['text'] = "������ֵ";
					$link[0]['href'] = 'hunter_service.php?act=order_add';
					$link[1]['text'] = "��Ա������ҳ";
					$link[1]['href'] = 'hunter_index.php?act=';
					showmsg("���".$_CFG['hunter_points_byname']."���㣬���ֵ���ٷ�����",0,$link);
					}
					$setsqlarr['setmeal_deadline']=0;
	}
	elseif ($_CFG['operation_hunter_mode']=='2')
	{
					$link[0]['text'] = "������ͨ����";
					$link[0]['href'] = 'hunter_service.php?act=setmeal_list';
					$link[1]['text'] = "��Ա������ҳ";
					$link[1]['href'] = 'hunter_index.php?act=';
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if ($setmeal['endtime']<time() && $setmeal['endtime']<>'0')
				{					
					showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
				}
				if ($setmeal['jobs_add']<=0)
				{
					showmsg("��ǰ������ְλ�Ѿ�������������ƣ������������ײͣ�",1,$link);
				}
				$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
				$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
				$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
	}
	$addrand=intval($_POST['addrand']);
	if($_SESSION['addrand']==$addrand){
	unset($_SESSION['addrand']);
	$setsqlarr['add_mode']=intval($add_mode);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['utype']=intval($_SESSION['utype']);
	
	$setsqlarr['companyname']=!empty($_POST['companyname'])?trim($_POST['companyname']):showmsg('��û����д��Ƹ����˾����ʾ���ƣ�',1);
	check_word($_CFG['filter'],$_POST['companyname'])?showmsg($_CFG['filter_tips'],0):'';
	//$setsqlarr['companyname_note']=!empty($_POST['companyname_note'])?trim($_POST['companyname_note']):showmsg('��û����д��Ƹ����˾��ע���ƣ�',1);
	$setsqlarr['companyname_note']=trim($_POST['companyname_note']);
	check_word($_CFG['filter'],$_POST['companyname_note'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['nature']=!empty($_POST['nature'])?intval($_POST['nature']):showmsg('��ѡ����Ƹ����˾���ʣ�',1);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['scale']=!empty($_POST['scale'])?intval($_POST['scale']):showmsg('��ѡ����Ƹ����˾��ģ��',1);
	$setsqlarr['scale_cn']=trim($_POST['scale_cn']);
	$setsqlarr['trade']=!empty($_POST['trade'])?intval($_POST['trade']):showmsg('��ѡ����Ƹ����˾��ҵ��',1);
	$setsqlarr['trade_cn']=trim($_POST['trade_cn']);
	$setsqlarr['company_desc']=!empty($_POST['company_desc'])?trim($_POST['company_desc']):showmsg('��û����д��Ƹ����˾�ļ�飡',1);
	check_word($_CFG['filter'],$_POST['company_desc'])?showmsg($_CFG['filter_tips'],0):'';
	
		
	$setsqlarr['jobs_name']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name']):showmsg('��û����дְλ���ƣ�',1);
	check_word($_CFG['filter'],$_POST['jobs_name'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('��ѡ��ְλ���',1);
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	
	$setsqlarr['department']=!empty($_POST['department'])?trim($_POST['department']):showmsg('��û����д�������ţ�',1);
	check_word($_CFG['filter'],$_POST['department'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['reporter']=!empty($_POST['reporter'])?trim($_POST['reporter']):showmsg('��û����д�㱨����',1);
	check_word($_CFG['filter'],$_POST['reporter'])?showmsg($_CFG['filter_tips'],0):'';
	
	$setsqlarr['subsite_id']=!empty($_POST['subsite_id'])?intval($_POST['subsite_id']):showmsg('��ѡ����������',1);
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=empty($_POST['district_cn'])?trim($_POST['subsite_name']):(trim($_POST['subsite_name']).'/'.trim($_POST['district_cn']));

	$setsqlarr['wage']=!empty($_POST['wage'])?intval($_POST['wage']):showmsg('��ѡ����н��Χ��',1);
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['wage_structure']=!empty($_POST['wage_structure'])?$_POST['wage_structure']:showmsg('��ѡ��н�ʹ��ɣ�',1);
	$setsqlarr['socialbenefits']=trim($_POST['socialbenefits']);
	$setsqlarr['livebenefits']=trim($_POST['livebenefits']);
	$setsqlarr['annualleave']=trim($_POST['annualleave']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('��û����дְλ������',1);
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	
	
	$setsqlarr['age']=!empty($_POST['age'])?intval($_POST['age']):showmsg('��ѡ������Ҫ��',1);
	$setsqlarr['age_cn']=trim($_POST['age_cn']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['experience']=!empty($_POST['experience'])?intval($_POST['experience']):showmsg('��ѡ��������Ҫ��',1);
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['education']=!empty($_POST['education'])?intval($_POST['education']):showmsg('��ѡ��ѧ��Ҫ��',1);
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['tongzhao']=intval($_POST['tongzhao']);
	$setsqlarr['tongzhao_cn']=trim($_POST['tongzhao_cn']);
	$setsqlarr['language']=trim($_POST['language']);
	$setsqlarr['jobs_qualified']=!empty($_POST['jobs_qualified'])?trim($_POST['jobs_qualified']):showmsg('��û����д��ְ�ʸ�',1);
	check_word($_CFG['filter'],$_POST['jobs_qualified'])?showmsg($_CFG['filter_tips'],0):'';
	
	$setsqlarr['hopetrade']=!empty($_POST['hopetrade'])?trim($_POST['hopetrade']):showmsg('��ѡ�������˲���Դ��ҵ��',1);
	$setsqlarr['hopetrade_cn']=trim($_POST['hopetrade_cn']);
	$setsqlarr['extra_message']=trim($_POST['extra_message']);
	check_word($_CFG['filter'],$_POST['extra_message'])?showmsg($_CFG['filter_tips'],0):'';

	$setsqlarr['addtime']=$timestamp;
	$setsqlarr['deadline']=strtotime("".intval($_POST['days'])." day");
	$setsqlarr['refreshtime']=$timestamp;
	$setsqlarr['key']=$setsqlarr['jobs_name'].$setsqlarr['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'].$setsqlarr['jobs_qualified'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$setsqlarr['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$setsqlarr['likekey']=$setsqlarr['jobs_name'].','.$setsqlarr['companyname'].','.$setsqlarr['category_cn'].','.$setsqlarr['district_cn'];
	if ($hunter_profile['audit']=='1')
	{
		$setsqlarr['audit']=intval($_CFG['audit_verifyhunter_addjob']);
	}
	else
	{
		$setsqlarr['audit']=intval($_CFG['audit_unexaminedhunter_addjob']);
	}
	$setsqlarr['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('��û����д��ϵ�ˣ�',1);
	check_word($_CFG['filter'],$_POST['contact'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['qq']=trim($_POST['qq']);
	check_word($_CFG['filter'],$_POST['qq'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):showmsg('��û����д��ϵ�绰��',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['address']=!empty($_POST['address'])?trim($_POST['address']):showmsg('��û����д��ϵ��ַ��',1);
	check_word($_CFG['filter'],$_POST['address'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['email']=!empty($_POST['email'])?trim($_POST['email']):showmsg('��û����д��ϵ���䣡',1);
	check_word($_CFG['filter'],$_POST['email'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['notify']=intval($_POST['notify']);
	
	$setsqlarr['contact_show']=intval($_POST['contact_show']);
	$setsqlarr['email_show']=intval($_POST['email_show']);
	$setsqlarr['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr['address_show']=intval($_POST['address_show']);
	$setsqlarr['qq_show']=intval($_POST['qq_show']);
	
	//���ְλ��Ϣ
	$pid=$db->inserttable(table('hunter_jobs'),$setsqlarr,true);
	empty($pid)?showmsg("���ʧ�ܣ�",0):'';
	//�����ϵ��ʽ
	if ($_CFG['operation_hunter_mode']=='1')
	{
		if ($points_rule['hunter_hunterjobs_add']['value']>0)
		{
		report_deal($_SESSION['uid'],$points_rule['hunter_hunterjobs_add']['type'],$points_rule['hunter_hunterjobs_add']['value']);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['hunter_hunterjobs_add']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],3,9201,$_SESSION['username'],"������ְλ��<strong>{$setsqlarr['jobs_name']}</strong>��({$operator}{$points_rule['hunter_hunterjobs_add']['value']})��(ʣ��:{$user_points})");
		}
		if (intval($_POST['days'])>0 && $points_rule['hunter_hunterjobs_daily']['value']>0)
		{
		$points_day=intval($_POST['days'])*$points_rule['hunter_hunterjobs_daily']['value'];
		report_deal($_SESSION['uid'],$points_rule['hunter_hunterjobs_daily']['type'],$points_day);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['hunter_hunterjobs_daily']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],3,9201,$_SESSION['username'],"������ְͨλ:<strong>{$_POST['jobs_name']}</strong>����Ч��Ϊ{$_POST['days']}�죬({$operator}{$points_day})��(ʣ��:{$user_points})");
		}
	}
	elseif ($_CFG['operation_hunter_mode']=='2')
	{
		action_user_setmeal($_SESSION['uid'],"jobs_add");
		$setmeal=get_user_setmeal($_SESSION['uid']);
		write_memberslog($_SESSION['uid'],3,9202,$_SESSION['username'],"����ְλ:<strong>{$_POST['jobs_name']}</strong>�������Է���ְλ:<strong>{$setmeal['jobs_add']}</strong>��");
	}
	write_memberslog($_SESSION['uid'],3,8502,$_SESSION['username'],"������ְλ��{$setsqlarr['jobs_name']}");
	baidu_submiturl(url_rewrite('QS_hunter_jobsshow',array('id'=>$pid)),'addhunterjob');
	}
	header("location:?act=addjobs_save_succeed");
}
elseif($act=='addjobs_save_succeed'){
	$uid = intval($_SESSION['uid']);
	$smarty->assign('concern_id',get_concern_id($uid));
	$smarty->assign('title','����ְλ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_hunter/hunter_addjobs_succeed.htm');
}
elseif ($act=='jobs_perform')
{
	$yid =!empty($_POST['y_id'])?$_POST['y_id']:$_GET['y_id'];
    $jobs_num=count($yid);
 	if (empty($yid))
	{
	showmsg("��û��ѡ��ְλ��",1);
	}
	
	$refresh=!empty($_POST['refresh'])?$_POST['refresh']:$_GET['refresh'];
	$delete=!empty($_POST['delete'])?$_POST['delete']:$_GET['delete'];
    if ($refresh)
	{
		if($jobs_num==1){
			if(is_array($yid)){
				$yid = $yid[0];
			}
			$jobs_info = get_jobs_one($yid,$_SESSION['uid']);
			if($jobs_info['deadline']<time()){
				showmsg("��ְλ�ѵ��ڣ��������ڣ�",1);
			}
		}
		if($_CFG['operation_hunter_mode']=='1'){
			//����ˢ��ʱ��
			//�һ�ε�ˢ��ʱ��
			$refrestime=get_last_refresh_date($_SESSION['uid'],"3001");
			$duringtime=time()-$refrestime['max(addtime)'];
			$space = $_CFG['hunter_pointsmode_refresh_space']*60;
			$refresh_time = get_today_refresh_times($_SESSION['uid'],"3001");
			if($_CFG['hunter_pointsmode_refresh_time']!=0&&($refresh_time['count(*)']>=$_CFG['hunter_pointsmode_refresh_time']))
			{
			showmsg("ÿ�����ֻ��ˢ��".$_CFG['hunter_pointsmode_refresh_time']."��,�������ѳ������ˢ�´������ƣ�",2);	
			}
			elseif($duringtime<=$space){
			showmsg($_CFG['hunter_pointsmode_refresh_space']."�����ڲ����ظ�ˢ��ְλ��",2);
			}
			else 
			{
				$points_rule=get_cache('points_rule');
				if($points_rule['hunter_hunterjobs_refresh']['value']>0)
				{
					$user_points=get_user_points($_SESSION['uid']);
					$total_point=$jobs_num*$points_rule['hunter_hunterjobs_refresh']['value'];
					if ($total_point>$user_points && $points_rule['hunter_hunterjobs_refresh']['type']=="2")
					{
							$link[0]['text'] = "������һҳ";
							$link[0]['href'] = 'javascript:history.go(-1)';
							$link[1]['text'] = "������ֵ";
							$link[1]['href'] = 'hunter_service.php?act=order_add';
					showmsg("����".$_CFG['hunter_points_byname']."���㣬���ȳ�ֵ��",0,$link);
					}
					report_deal($_SESSION['uid'],$points_rule['hunter_hunterjobs_refresh']['type'],$total_point);
					$user_points=get_user_points($_SESSION['uid']);
					$operator=$points_rule['hunter_hunterjobs_refresh']['type']=="1"?"+":"-";
					write_memberslog($_SESSION['uid'],3,9201,$_SESSION['username'],"ˢ����{$jobs_num}��ְλ��({$operator}{$total_point})��(ʣ��:{$user_points})");
				}
			}
		}
		elseif($_CFG['operation_hunter_mode']=='2'){
			$link[0]['text'] = "������ͨ����";
			$link[0]['href'] = 'hunter_service.php?act=setmeal_list';
			$link[1]['text'] = "��Ա������ҳ";
			$link[1]['href'] = 'hunter_index.php?act=';
			$setmeal=get_user_setmeal($_SESSION['uid']);
			if (empty($setmeal))
			{					
				showmsg("����û�п�ͨ�����뿪ͨ",1,$link);
			}
			elseif ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
			{					
				showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
			}
			else
			{
				$refrestime=get_last_refresh_date($_SESSION['uid'],"3001");
				$duringtime=time()-$refrestime['max(addtime)'];
				$space = $setmeal['hunter_refresh_jobs_space']*60;
				$refresh_time = get_today_refresh_times($_SESSION['uid'],"3001");
				if($setmeal['hunter_refresh_jobs_time']!=0&&($refresh_time['count(*)']>=$setmeal['hunter_refresh_jobs_time']))
				{
				showmsg("ÿ�����ֻ��ˢ��".$setmeal['hunter_refresh_jobs_time']."��,�������ѳ������ˢ�´������ƣ�",2);	
				}
				elseif($duringtime<=$space){
				showmsg($setmeal['hunter_refresh_jobs_space']."�����ڲ����ظ�ˢ��ְλ��",2);
				}
			}
		}
		refresh_jobs($yid,$_SESSION['uid']);
		write_memberslog($_SESSION['uid'],3,8504,$_SESSION['username'],"ˢ��ְλ");
		write_refresh_log($_SESSION['uid'],3001);					
		showmsg("ˢ��ְλ�ɹ���",2);
	}
	elseif ($delete)
	{
		if($n=del_jobs($yid,$_SESSION['uid']))
		{
			showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
		}
		else
		{
		showmsg("ɾ��ʧ�ܣ�",0);
		}
	}
	elseif (!empty($_REQUEST['display1']))
	{
	activate_jobs($yid,1,$_SESSION['uid']);
	showmsg("���óɹ���",2);
	}
	elseif (!empty($_REQUEST['display2']))
	{
	activate_jobs($yid,2,$_SESSION['uid']);
	showmsg("���óɹ���",2);
	}
}
elseif ($act=='edit_jobs')
{
	$jobs=get_jobs_one(intval($_GET['id']),$_SESSION['uid']);
	if (empty($jobs)) showmsg("��������",1);
	$smarty->assign('user',$user);
	$smarty->assign('title','�޸�ְλ - ��ͷ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('points_total',get_user_points($_SESSION['uid']));
	$smarty->assign('points',get_cache('points_rule'));
	$smarty->assign('jobs',$jobs);
	$smarty->assign('subsite',get_all_subsite());
	$subsite_cn = explode('/',$jobs['district_cn']);
	$smarty->assign('subsite_cn',$subsite_cn[0]);
	$smarty->assign('district_cn',$subsite_cn[1]);
	//��������
	$smarty->assign('district',get_subsite_district($jobs['district']));
	$smarty->display('member_hunter/hunter_editjobs.htm');
}
elseif ($act=='editjobs_save')
{
	$id=intval($_POST['id']);
	$add_mode=trim($_POST['add_mode']);
	$days=intval($_POST['days']);
	if ($_CFG['operation_hunter_mode']=='1')
	{
					$points_rule=get_cache('points_rule');
					$user_points=get_user_points($_SESSION['uid']);
					$total=0;
					if($points_rule['hunter_hunterjobs_edit']['type']=="2" && $points_rule['hunter_hunterjobs_edit']['value']>0)
					{
					$total=$points_rule['hunter_hunterjobs_edit']['value'];
					}
					if($points_rule['hunter_hunterjobs_daily']['type']=="2" && $points_rule['hunter_hunterjobs_daily']['value']>0)
					{
					$total=$total+($days*$points_rule['hunter_hunterjobs_daily']['value']);
					}
					if ($total>$user_points)
					{
					$link[0]['text'] = "������һҳ";
					$link[0]['href'] = 'javascript:history.go(-1)';
					$link[1]['text'] = "������ֵ";
					$link[1]['href'] = 'hunter_service.php?act=order_add';
					showmsg("���".$_CFG['hunter_points_byname']."���㣬���ֵ���ٷ�����",0,$link);
					}
	}
	elseif ($_CFG['operation_hunter_mode']=='2')
	{
					$link[0]['text'] = "������ͨ����";
					$link[0]['href'] = 'hunter_service.php?act=setmeal_list';
					$link[1]['text'] = "��Ա������ҳ";
					$link[1]['href'] = 'hunter_index.php?act=';
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
				{					
					showmsg("����Ϣͨ�������ײͷ����������ײ��Ѿ����ڣ������¿�ͨ",1,$link);
				}
			$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
			$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
			$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
	}
	$setsqlarr['add_mode']=intval($add_mode);
	
	$setsqlarr['companyname']=!empty($_POST['companyname'])?trim($_POST['companyname']):showmsg('��û����д��Ƹ����˾����ʾ���ƣ�',1);
	check_word($_CFG['filter'],$_POST['companyname'])?showmsg($_CFG['filter_tips'],0):'';
	//$setsqlarr['companyname_note']=!empty($_POST['companyname_note'])?trim($_POST['companyname_note']):showmsg('��û����д��Ƹ����˾��ע���ƣ�',1);
	$setsqlarr['companyname_note']=trim($_POST['companyname_note']);
	check_word($_CFG['filter'],$_POST['companyname_note'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['nature']=!empty($_POST['nature'])?intval($_POST['nature']):showmsg('��ѡ����Ƹ����˾���ʣ�',1);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['scale']=!empty($_POST['scale'])?intval($_POST['scale']):showmsg('��ѡ����Ƹ����˾��ģ��',1);
	$setsqlarr['scale_cn']=trim($_POST['scale_cn']);
	$setsqlarr['trade']=!empty($_POST['trade'])?intval($_POST['trade']):showmsg('��ѡ����Ƹ����˾��ҵ��',1);
	$setsqlarr['trade_cn']=trim($_POST['trade_cn']);
	$setsqlarr['company_desc']=!empty($_POST['company_desc'])?trim($_POST['company_desc']):showmsg('��û����д��Ƹ����˾�ļ�飡',1);
	check_word($_CFG['filter'],$_POST['company_desc'])?showmsg($_CFG['filter_tips'],0):'';
	
		
	$setsqlarr['jobs_name']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name']):showmsg('��û����дְλ���ƣ�',1);
	check_word($_CFG['filter'],$_POST['jobs_name'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('��ѡ��ְλ���',1);
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	
	$setsqlarr['department']=!empty($_POST['department'])?trim($_POST['department']):showmsg('��û����д�������ţ�',1);
	check_word($_CFG['filter'],$_POST['department'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['reporter']=!empty($_POST['reporter'])?trim($_POST['reporter']):showmsg('��û����д�㱨����',1);
	check_word($_CFG['filter'],$_POST['reporter'])?showmsg($_CFG['filter_tips'],0):'';
	
	$setsqlarr['subsite_id']=!empty($_POST['subsite_id'])?intval($_POST['subsite_id']):showmsg('��ѡ����������',1);
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=empty($_POST['district_cn'])?trim($_POST['subsite_name']):(trim($_POST['subsite_name']).'/'.trim($_POST['district_cn']));

	$setsqlarr['wage']=!empty($_POST['wage'])?intval($_POST['wage']):showmsg('��ѡ����н��Χ��',1);
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['wage_structure']=!empty($_POST['wage_structure'])?$_POST['wage_structure']:showmsg('��ѡ��н�ʹ��ɣ�',1);
	$setsqlarr['socialbenefits']=trim($_POST['socialbenefits']);
	$setsqlarr['livebenefits']=trim($_POST['livebenefits']);
	$setsqlarr['annualleave']=trim($_POST['annualleave']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('��û����дְλ������',1);
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	
	
	$setsqlarr['age']=!empty($_POST['age'])?intval($_POST['age']):showmsg('��ѡ������Ҫ��',1);
	$setsqlarr['age_cn']=trim($_POST['age_cn']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['experience']=!empty($_POST['experience'])?intval($_POST['experience']):showmsg('��ѡ��������Ҫ��',1);
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['education']=!empty($_POST['education'])?intval($_POST['education']):showmsg('��ѡ��ѧ��Ҫ��',1);
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['tongzhao']=intval($_POST['tongzhao']);
	$setsqlarr['tongzhao_cn']=trim($_POST['tongzhao_cn']);
	$setsqlarr['language']=trim($_POST['language']);
	$setsqlarr['jobs_qualified']=!empty($_POST['jobs_qualified'])?trim($_POST['jobs_qualified']):showmsg('��û����д��ְ�ʸ�',1);
	check_word($_CFG['filter'],$_POST['jobs_qualified'])?showmsg($_CFG['filter_tips'],0):'';
	
	$setsqlarr['hopetrade']=!empty($_POST['hopetrade'])?trim($_POST['hopetrade']):showmsg('��ѡ�������˲���Դ��ҵ��',1);
	$setsqlarr['hopetrade_cn']=trim($_POST['hopetrade_cn']);
	$setsqlarr['extra_message']=trim($_POST['extra_message']);
	check_word($_CFG['filter'],$_POST['extra_message'])?showmsg($_CFG['filter_tips'],0):'';

	$setsqlarr['refreshtime']=$timestamp;
	$setsqlarr['key']=$setsqlarr['jobs_name'].$setsqlarr['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'].$setsqlarr['jobs_qualified'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$setsqlarr['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$setsqlarr['likekey']=$setsqlarr['jobs_name'].','.$setsqlarr['companyname'].','.$setsqlarr['category_cn'].','.$setsqlarr['district_cn'];
	

	if ($_CFG['operation_hunter_mode']=='1'){
		$setsqlarr['setmeal_deadline']=0;
		$setsqlarr['add_mode']=1;
	}elseif($_CFG['operation_hunter_mode']=='2'){
		$setmeal=get_user_setmeal($_SESSION['uid']);
		$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
		$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
		$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
		$setsqlarr['add_mode']=2;
	}
	if ($days>0)
	{
		if (intval($_POST['olddeadline'])>=time())
		{
			 $setsqlarr['deadline']=intval($_POST['olddeadline'])+($days*(60*60*24));
		}
		else
		{
			 $setsqlarr['deadline']=strtotime("{$days} day");
		}
	}else{
			 $setsqlarr['deadline']=intval($_POST['olddeadline']);
	}

	if ($hunter_profile['audit']=="1")
	{
	$_CFG['audit_verifyhunter_editjob']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_verifyhunter_editjob']):'';
	}
	else
	{
	$_CFG['audit_unexaminedhunter_editjob']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_unexaminedhunter_editjob']):'';
	}

	$setsqlarr['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('��û����д��ϵ�ˣ�',1);
	check_word($_CFG['filter'],$_POST['contact'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['qq']=trim($_POST['qq']);
	check_word($_CFG['filter'],$_POST['qq'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):showmsg('��û����д��ϵ�绰��',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['address']=!empty($_POST['address'])?trim($_POST['address']):showmsg('��û����д��ϵ��ַ��',1);
	check_word($_CFG['filter'],$_POST['address'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['email']=!empty($_POST['email'])?trim($_POST['email']):showmsg('��û����д��ϵ���䣡',1);
	check_word($_CFG['filter'],$_POST['email'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['notify']=intval($_POST['notify']);
	
	$setsqlarr['contact_show']=intval($_POST['contact_show']);
	$setsqlarr['email_show']=intval($_POST['email_show']);
	$setsqlarr['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr['address_show']=intval($_POST['address_show']);
	$setsqlarr['qq_show']=intval($_POST['qq_show']);
		
	if (!$db->updatetable(table('hunter_jobs'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) showmsg("����ʧ�ܣ�",0);
	if ($_CFG['operation_hunter_mode']=='1')
	{
		if ($points_rule['hunter_hunterjobs_edit']['value']>0)
		{
		report_deal($_SESSION['uid'],$points_rule['hunter_hunterjobs_edit']['type'],$points_rule['hunter_hunterjobs_edit']['value']);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['hunter_hunterjobs_edit']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],3,9201,$_SESSION['username'],"�޸�ְλ��<strong>{$setsqlarr['jobs_name']}</strong>��({$operator}{$points_rule['hunter_hunterjobs_edit']['value']})��(ʣ��:{$user_points})");
		}
		if ($days>0 && $points_rule['hunter_hunterjobs_daily']['value']>0)
		{
		$points_day=intval($_POST['days'])*$points_rule['hunter_hunterjobs_daily']['value'];
		report_deal($_SESSION['uid'],$points_rule['hunter_hunterjobs_daily']['type'],$points_day);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['hunter_hunterjobs_daily']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],3,9201,$_SESSION['username'],"�ӳ�ְλ({$_POST['jobs_name']})��Ч��Ϊ{$_POST['days']}�죬({$operator}{$points_day})��(ʣ��:{$user_points})");
		}
	}	 
	$link[0]['text'] = "ְλ�б�";
	$link[0]['href'] = '?act=jobs';
	$link[1]['text'] = "�鿴�޸Ľ��";
	$link[1]['href'] = "?act=edit_jobs&id={$id}";
	$link[2]['text'] = "��Ա������ҳ";
	$link[2]['href'] = "hunter_index.php";
	//
 	write_memberslog($_SESSION['uid'],$_SESSION['utype'],8503,$_SESSION['username'],"�޸���ְλ��{$setsqlarr['jobs_name']}��ְλID��{$id}");
	showmsg("�޸ĳɹ���",2,$link);
}
unset($smarty);
?>