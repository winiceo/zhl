<?php
/*
 * ��ҵ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"jobs");
if ($act=='jobs')
{
	$addjobs_save_succeed=intval($_GET['addjobs_save_succeed']);
	$jobtype=intval($_GET['jobtype']);
	$wheresql=" WHERE uid='{$_SESSION['uid']}' ";
	$orderby=" order by refreshtime desc";
	switch($jobtype){
		case 1:
			$tabletype="all";
			/* ȫ��ְλ ״̬ ɸѡ*/
			$state=intval($_GET["state"]);
			if($state==1)
			{	
				$tabletype="jobs";
			}
			elseif($state==2)
			{
				$tabletype="jobs_tmp";
				$wheresql.=" AND audit=2 ";
			}
			elseif($state==3)
			{
				$tabletype="jobs_tmp";
				$wheresql.=" AND audit=3 ";
			}
			elseif($state==4)
			{
				$tabletype="jobs_tmp";
				$wheresql.=" AND (display=2 or deadline<".time()." or (setmeal_deadline != 0 and setmeal_deadline< ".time().")) ";
			}
			$orderby=" order by display asc,audit asc";
			break;
			
		case 2:
			$tabletype="jobs_tmp";
			$wheresql.=" AND audit=2 ";
			break;
		case 3:
			$tabletype="jobs_tmp";
			/* δ��ʾ ״̬ ɸѡ*/
			$state=intval($_GET["state"]);
			if($state==0)
			{
				$wheresql.=" AND (audit=3 or display=2 or deadline<".time()." or (setmeal_deadline != 0 and setmeal_deadline< ".time()."))";
			}
			elseif($state==1)
			{
				$wheresql.=" AND audit=3 ";
			}
			else
			{
				$wheresql.=" AND (display=2 or deadline<".time()." or (setmeal_deadline != 0 and setmeal_deadline< ".time().")) ";
			}
			break;
		default:
			$tabletype="jobs";
			break;
	}
	/*
		3.6 �ƹ�״̬
	*/
	$generalize=trim($_GET['generalize']);
	$generalize_arr = array("stick","highlight","emergency","recommend");
	if(in_array($generalize,$generalize_arr))
	{
		$wheresql.=" AND $generalize<>'' ";
	}
	/*
		ԤԼˢ��
	*/
	$auto_refresh=intval($_GET['auto_refresh']);
	switch($auto_refresh)
	{
		case 1:
			$wheresql.=" AND auto_refresh=1 ";
			break;
		case 2:
			$wheresql.=" AND auto_refresh=0 ";
			break;
	}
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	if ($tabletype=="all")
	{
	$total_sql="SELECT COUNT(*) AS num FROM ".table('jobs').$wheresql." UNION ALL  SELECT COUNT(*) AS num FROM ".table('jobs_tmp').$wheresql;
	}
	else
	{
	$total_sql="SELECT COUNT(*) AS num FROM ".table($tabletype).$wheresql;
	}
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('title','ְλ���� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('audit',$audit);
	if ($tabletype=="all")
	{
	$sql="SELECT * FROM ".table('jobs').$wheresql." UNION ALL SELECT * FROM ".table('jobs_tmp').$wheresql.$orderby;
	}
	else
	{
	$sql="SELECT * FROM ".table($tabletype).$wheresql.$orderby;
	}
	$total[0]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs')." WHERE uid='{$_SESSION['uid']}' and audit=1 and display=1 ");
	$total[1]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE uid='{$_SESSION['uid']}' AND audit=2 ");
	$total[2]=$total[0]+$total[1];
	//ͳ��ÿ�������е�ְλ��
	$jobs_total[0]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs')." WHERE uid='{$_SESSION['uid']}' ");
	$jobs_total[1]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs')." WHERE uid='{$_SESSION['uid']}'  UNION ALL  SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE uid='{$_SESSION['uid']}' ");
	$jobs_total[2]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE uid='{$_SESSION['uid']}'  AND audit=2 ");
	$jobs_total[3]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE uid='{$_SESSION['uid']}'  AND (audit=3 or display=2 or deadline<".time()." or (setmeal_deadline != 0 and setmeal_deadline< ".time()."))");
	$smarty->assign('total',$total);
	$smarty->assign('jobs_total',$jobs_total);
	$setmeal=get_user_setmeal($_SESSION['uid']);
	$smarty->assign('setmeal',$setmeal);
	$smarty->assign('jobs',get_jobs($offset,$perpage,$sql,true));
	if($total_val>$perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	// �����ɹ����
	$addjobs_save_succeed=intval($_GET['addjobs_save_succeed']);
	$jobs_one=get_jobs_one($addjobs_save_succeed);
	$smarty->assign('jobs_one',$jobs_one);
	$smarty->assign('points_rule',get_cache('points_rule'));
	/*
		���ҿ�ԤԼ��ְλ,�ж���Ӫģʽ
	*/
	if ($_CFG['operation_mode']=="3")
	{
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if($_CFG['setmeal_to_points']=="1")
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
	$smarty->assign('add_mode',$add_mode);
	$jobs_refresh=$db->getall("SELECT j.id,j.uid,j.jobs_name FROM ".table('jobs')." as j WHERE j.uid=$_SESSION[uid] and auto_refresh=0 ");
	$smarty->assign('jobs_refresh',$jobs_refresh);
	// ΢��Ƹ url
	$w_url=$_CFG['site_domain'].$_CFG['site_dir']."m/m-wzp.php?company_id=".$company_profile['id'];
	$smarty->assign('w_url',$w_url);
	$smarty->assign('user_points',get_user_points($_SESSION['uid']));
	$smarty->display('member_company/company_jobs.htm');
}
elseif ($act=='jobs_templates')
{
	$smarty->assign('title','ְλģ����� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('jobs_templates',get_jobs_templates());
	$smarty->display('member_company/company_jobs_templates.htm');
}
elseif ($act=='addjobs')
{




		$smarty->assign('user',$user);
		$smarty->assign('jobs_templates',get_jobs_templates());
		$smarty->assign('subsite',get_me_subsite());
		if ($cominfo_flge)
		{
			//���������зָ�
			$telarray = explode('-',$company_profile['landline_tel']);
			if(intval($telarray[0]) > 0)
			{
				$company_profile['landline_tel_first'] = $telarray[0];
			}
			if(intval($telarray[1]) > 0)
			{
				$company_profile['landline_tel_next'] = $telarray[1];
			}
			if(intval($telarray[2]) > 0)
			{
				$company_profile['landline_tel_last'] = $telarray[2];
			}
			$_SESSION['addrand']=rand(1000,5000);
			$smarty->assign('addrand',$_SESSION['addrand']);
			$smarty->assign('title','����ְλ - ��ҵ��Ա���� - '.$_CFG['site_name']);
			$smarty->assign('company_profile',$company_profile);
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
			/**
			 * 3.6�Ż�start
			 */
			if ($add_mode=='1')
			{
				$points_rule=get_cache('points_rule');
				$user_points=get_user_points($_SESSION['uid']);
				if ($points_rule['jobs_add']['type']=="2" && $points_rule['jobs_add']['value']>0)
				{
					$total=$points_rule['jobs_add']['value'];
					if ($total>$user_points)
					{
						$link[0]['text'] = "������ֵ";
						$link[0]['href'] = 'company_service.php?act=order_add';
						$link[1]['text'] = "��Ա������ҳ";
						$link[1]['href'] = 'company_index.php?act=';
						showmsg("���".$_CFG['points_byname']."���㣬���ֵ���ٷ�����",0,$link);
					}
				}
			}
			elseif ($add_mode=='2')
			{
				$link[0]['text'] = "������ͨ����";
				$link[0]['href'] = 'company_service.php?act=setmeal_list';
				$link[1]['text'] = "��Ա������ҳ";
				$link[1]['href'] = 'company_index.php?act=';
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
				{					
					showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
				}
				/*
					��ʾ�е�ְλ(���ͨ��,�����,δ��ͣ)
				*/
				$jobs_num= $db->get_total("select count(*) num from ".table("jobs")." where uid=$_SESSION[uid] and audit=1 and display=1 ");
				$jobs_tmp_num= $db->get_total("select count(*) num from ".table("jobs_tmp")." where uid=$_SESSION[uid] and audit=2 and display=1 ");
				$com_jobs_num=$jobs_num+$jobs_tmp_num;
				if ($com_jobs_num>=$setmeal['jobs_ordinary'])
				{
					showmsg("��ǰ��ʾ��ְλ�Ѿ�������������ƣ������������ײͣ�",1,$link);
				}
			}
			/**
			 * 3.6�Ż�end
			 */

			$captcha=get_cache('captcha');
			$smarty->assign('verify_addjob',$captcha['verify_addjob']);
			$smarty->display('member_company/company_addjobs.htm');
		}
		else
		{
		$link[0]['text'] = "������ҵ����";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("Ϊ�˴ﵽ���õ���ƸЧ������������������ҵ���ϣ�",1,$link);
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
	if ($add_mode=='1')
	{
		$points_rule=get_cache('points_rule');
		$user_points=get_user_points($_SESSION['uid']);
		if ($points_rule['jobs_add']['type']=="2" && $points_rule['jobs_add']['value']>0)
		{
			$total=$points_rule['jobs_add']['value'];
			if ($total>$user_points)
			{
				$link[0]['text'] = "������ֵ";
				$link[0]['href'] = 'company_service.php?act=order_add';
				$link[1]['text'] = "��Ա������ҳ";
				$link[1]['href'] = 'company_index.php?act=';
				showmsg("���".$_CFG['points_byname']."���㣬���ֵ���ٷ�����",0,$link);
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
		$link[0]['text'] = "������ͨ����";
		$link[0]['href'] = 'company_service.php?act=setmeal_list';
		$link[1]['text'] = "��Ա������ҳ";
		$link[1]['href'] = 'company_index.php?act=';
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{					
			showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
		}
		/*
			��ʾ�е�ְλ(���ͨ��,�����,δ��ͣ)
		*/
		$jobs_num= $db->get_total("select count(*) num from ".table("jobs")." where uid=$_SESSION[uid] and audit=1 and display=1 ");
		$jobs_tmp_num= $db->get_total("select count(*) num from ".table("jobs_tmp")." where uid=$_SESSION[uid] and audit=2 and display=1 ");
		$com_jobs_num=$jobs_num+$jobs_tmp_num;
		if ($com_jobs_num>=$setmeal['jobs_ordinary'])
		{
			showmsg("��ǰ��ʾ��ְλ�Ѿ�������������ƣ������������ײͣ�",1,$link);
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
	$setsqlarr['companyname']=$company_profile['companyname'];
	$setsqlarr['company_id']=$company_profile['id'];
	$setsqlarr['company_addtime']=$company_profile['addtime'];
	$setsqlarr['company_audit']=$company_profile['audit'];
	$setsqlarr['jobs_name']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name']):showmsg('��û����дְλ���ƣ�',1);
	check_word($_CFG['filter'],$_POST['jobs_name'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('��ѡ��ְλ���',1);
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['subsite_id']=!empty($_POST['subsite_id'])?intval($_POST['subsite_id']):showmsg('��ѡ����������',1);
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=empty($_POST['district_cn'])?trim($_POST['subsite_name']):(trim($_POST['subsite_name']).'/'.trim($_POST['district_cn']));
	$setsqlarr['wage']=intval($_POST['wage'])?intval($_POST['wage']):showmsg('��ѡ��н�ʴ�����',1);		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['negotiable']=intval($_POST['negotiable']);
	$setsqlarr['tag']=trim($_POST['tag']);
	$setsqlarr['tag_cn']=trim($_POST['tag_cn']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):showmsg('��ѡ��ѧ��Ҫ��',1);		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):showmsg('��ѡ�������飡',1);		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['age']=trim($_POST['minage'])."-".trim($_POST['maxage']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('��û����дְλ������',1);
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['trade']=$company_profile['trade'];
	$setsqlarr['trade_cn']=$company_profile['trade_cn'];
	$setsqlarr['scale']=$company_profile['scale'];
	$setsqlarr['scale_cn']=$company_profile['scale_cn'];
	$setsqlarr['street']=$company_profile['street'];
	$setsqlarr['street_cn']=$company_profile['street_cn'];
	$setsqlarr['addtime']=$timestamp;
	$setsqlarr['deadline']=strtotime("".intval($_CFG['company_add_days'])." day");
	$setsqlarr['refreshtime']=$timestamp;
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_profile['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_profile['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$setsqlarr['tpl']=$company_profile['tpl'];
	$setsqlarr['map_x']=$company_profile['map_x'];
	$setsqlarr['map_y']=$company_profile['map_y'];
	if ($company_profile['audit']=="1")
	{
	$setsqlarr['audit']=intval($_CFG['audit_verifycom_addjob']);
	}
	else
	{
	$setsqlarr['audit']=intval($_CFG['audit_unexaminedcom_addjob']);
	} 
	$setsqlarr['is_entrust']=isset($_POST['is_entrust']) && intval($_POST['is_entrust'])=="0"?"0":"1";
	
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('��û����д��ϵ�ˣ�',1);
	check_word($_CFG['filter'],$_POST['contact'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):'';
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['address']=!empty($_POST['address'])?trim($_POST['address']):showmsg('��û����д��ϵ��ַ��',1);
	check_word($_CFG['filter'],$_POST['address'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['email']=!empty($_POST['email'])?trim($_POST['email']):showmsg('��û����д��ϵ���䣡',1);
	check_word($_CFG['filter'],$_POST['email'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['notify']=intval($_POST['notify']);//�ʼ�����
	$setsqlarr_contact['notify_mobile']=intval($_POST['notify_mobile']);//�ֻ�����
	$setsqlarr_contact['contact_show']=intval($_POST['contact_show']);
	$setsqlarr_contact['email_show']=intval($_POST['email_show']);
	$setsqlarr_contact['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr_contact['address_show']=intval($_POST['address_show']);
	//����
	$landline_tel[]=trim($_POST['landline_tel_first'])?trim($_POST['landline_tel_first']):"0";
	$landline_tel[]=trim($_POST['landline_tel_next'])?trim($_POST['landline_tel_next']):"0";
	$landline_tel[]=trim($_POST['landline_tel_last'])?trim($_POST['landline_tel_last']):"0";
	$setsqlarr_contact['landline_tel']=implode('-', $landline_tel);
	//�������ֻ����ٶ�ѡһ
	if(empty($setsqlarr_contact['telephone']) && $setsqlarr_contact['landline_tel']=='0-0-0')
	{
		showmsg('����д�ֻ���̻�����ѡһ���ɣ�',1);
	}
	
	//���ְλ��Ϣ
	$pid=$db->inserttable(table('jobs'),$setsqlarr,true);
	if(empty($pid)){
		showmsg("���ʧ�ܣ�",0);
	}
	//�����ϵ��ʽ
	$setsqlarr_contact['pid']=$pid;
	!$db->inserttable(table('jobs_contact'),$setsqlarr_contact)?showmsg("���ʧ�ܣ�",0):'';
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
		if($setsqlarr['audit']!="3")
		{
			// action_user_setmeal($_SESSION['uid'],"jobs_ordinary");
			$setmeal=get_user_setmeal($_SESSION['uid']);
			$com_jobs= $db->getone("select count(*) num from ".table("jobs")." where uid=$_SESSION[uid] and (audit=1 or audit=2) and display=1 ");
			$num=$setmeal['jobs_ordinary']-$com_jobs['num'];
			write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"������ְͨλ:<strong>{$_POST['jobs_name']}</strong>�������Է�����ְͨλ:<strong>{$num}</strong>��",2,1001,"����ְλ","1","{$setmeal['jobs_ordinary']}");
		}
		
	}
	$searchtab['id']=$pid;
	$searchtab['subsite_id']=$setsqlarr['subsite_id'];
	$searchtab['uid']=$setsqlarr['uid'];
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
	$searchtab['street']=$company_profile['street'];
	$searchtab['education']=$setsqlarr['education'];
	$searchtab['experience']=$setsqlarr['experience'];
	$searchtab['wage']=$setsqlarr['wage'];
	$searchtab['refreshtime']=$setsqlarr['refreshtime'];
	$searchtab['scale']=$setsqlarr['scale'];
	$searchtab['graduate']=$setsqlarr['graduate'];
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
	add_jobs_tag($pid,$_SESSION['uid'],$_POST['tag'])?"":showmsg('����ʧ�ܣ�',0);
	distribution_jobs($pid,$_SESSION['uid']);
	write_memberslog($_SESSION['uid'],1,2001,$_SESSION['username'],"������ְλ��{$setsqlarr['jobs_name']}");
	baidu_submiturl(url_rewrite('QS_jobsshow',array('id'=>$pid)),'addjob');
	}
	header("location:?act=jobs&addjobs_save_succeed=".$pid);
}
// elseif($act=='addjobs_save_succeed'){
// 	$uid = intval($_SESSION['uid']);
// 	$jobs=get_jobs_one(intval($_GET['jobsid']),$uid);
// 	$jobs['jobs_url'] = url_rewrite("QS_jobsshow",array('id'=>$jobs['id']));
// 	$smarty->assign('jobs',$jobs);
// 	$smarty->assign('concern_id',get_concern_id($uid));
// 	$smarty->assign('title','����ְλ - ��ҵ��Ա���� - '.$_CFG['site_name']);
// 	$smarty->display('member_company/company_addjobs_succeed.htm');
// }
elseif ($act=='del_jobs_templates')
{
	$yid =!empty($_POST['y_id'])?$_POST['y_id']:$_GET['y_id'];
	if (empty($yid))
	{
	showmsg("��û��ѡ��ģ�壡",1);
	}
	if($n=del_templates($yid,$_SESSION['uid']))
	{
		showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	showmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif ($act=='jobs_perform')
{
	global $_CFG;
	$yid =!empty($_POST['y_id'])?$_POST['y_id']:$_GET['y_id'];
    $jobs_num=count($yid);
	if (empty($yid))
	{
	showmsg("��û��ѡ��ְλ��",1);
	}
	
	$refresh=!empty($_POST['refresh'])?$_POST['refresh']:$_GET['refresh'];
	$delete=!empty($_POST['delete'])?$_POST['delete']:$_GET['delete']; 
    
	if (!empty($_REQUEST['display2']))
	{
	activate_jobs($yid,2,$_SESSION['uid']);
	showmsg("���óɹ���",2);
	}
	elseif ($delete)
	{
		if($n=del_jobs($yid,$_SESSION['uid']))
		{
			showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
		}
		else
		{
			showmsg("ɾ��ʧ�ܣ�",2);
		}
	} 
      elseif ($refresh)
	{
		$mode = 0;
		if($jobs_num==1){
			if(is_array($yid)){
				$yid = $yid[0];
			}
			$jobs_info = get_jobs_one($yid,$_SESSION['uid']);
			if($jobs_info['deadline']<time()){
				showmsg("��ְλ�ѵ��ڣ�",1);
			}
		}
		//����ģʽ
		if($_CFG['operation_mode']=='1')
		{
			$mode = 1;
			//����ˢ��ʱ��
			//���һ�ε�ˢ��ʱ��
			$refrestime=get_last_refresh_date($_SESSION['uid'],"1001",1);
			$duringtime=time()-$refrestime['max(addtime)'];
			$space = $_CFG['com_pointsmode_refresh_space']*60;
			$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",1);
			if($_CFG['com_pointsmode_refresh_time']!=0&&($refresh_time['count(*)']>=$_CFG['com_pointsmode_refresh_time']))
			{
			showmsg("ÿ�����ֻ��ˢ��".$_CFG['com_pointsmode_refresh_time']."��,�������ѳ������ˢ�´������ƣ�",2);	
			}
			elseif($duringtime<=$space){
			showmsg($_CFG['com_pointsmode_refresh_space']."�����ڲ����ظ�ˢ��ְλ��",2);
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
					//��/�� ����
					report_deal($_SESSION['uid'],$points_rule['jobs_refresh']['type'],$total_point);
					$user_points=get_user_points($_SESSION['uid']);
					$operator=$points_rule['jobs_refresh']['type']=="1"?"+":"-";
					write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"ˢ����{$jobs_num}��ְλ��({$operator}{$total_point})��(ʣ��:{$user_points})",1,1003,"ˢ��ְλ","{$operator}{$total_point}","{$user_points}");
				}
			}
		}	
		//�ײ�ģʽ
		elseif($_CFG['operation_mode']=='2') 
		{
			$mode = 2;
			//����ˢ��ʱ��
			$link[0]['text'] = "������ͨ����";
			$link[0]['href'] = 'company_service.php?act=setmeal_list';
			$link[1]['text'] = "��Ա������ҳ";
			$link[1]['href'] = 'company_index.php?act=';
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
				//���һ�ε�ˢ��ʱ��
				$refrestime=get_last_refresh_date($_SESSION['uid'],"1001",2);
				$duringtime=time()-$refrestime['max(addtime)'];
				$space = $setmeal['refresh_jobs_space']*60;
				$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",2);
				if($setmeal['refresh_jobs_time']!=0&&($refresh_time['count(*)']>=$setmeal['refresh_jobs_time']))
				{
				showmsg("ÿ�����ֻ��ˢ��".$setmeal['refresh_jobs_time']."��,�������ѳ������ˢ�´������ƣ�",2);
				}
				elseif($duringtime<=$space){
				showmsg($setmeal['refresh_jobs_space']."�����ڲ����ظ�ˢ��ְλ��",2);	
				}
			}
		}
		//���ģʽ
		elseif($_CFG['operation_mode']=='3') 
		{
			$setmeal=get_user_setmeal($_SESSION['uid']);
			//�û�Ա�ײ͹��� (�ײ͹��ں���û�����ˢ)
			if($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
			{
				//��̨��ͨ  ������ʱ���û�������
				if($_CFG['setmeal_to_points']=='1')
				{
					$mode = 1;
					//�û�����ˢ��ְλ�Ļ�->���ջ���ģʽ����->�ȿ����Ƿ񳬹��������ƺ�ʱ����
					$refrestime=get_last_refresh_date($_SESSION['uid'],"1001",1);
					$duringtime=time()-$refrestime['max(addtime)'];
					$space = $setmeal['refresh_jobs_space']*60;
					$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",1);
					if($_CFG['com_pointsmode_refresh_time']!=0&&($refresh_time['count(*)']>=$_CFG['com_pointsmode_refresh_time']))
					{
						$link[0]['text'] = "������һҳ";
						$link[0]['href'] = 'javascript:history.go(-1)';
						$link[1]['text'] = "��������";
						$link[1]['href'] = 'company_service.php?act=setmeal_list';
						showmsg("�����ײ��Ѿ����ڣ�ˢ��ְλ�����Ļ��֣������û���ˢ��ְλÿ�����ֻ��ˢ��".$_CFG['com_pointsmode_refresh_time']."��,�������ѳ������ˢ�´������ƣ������������ײͣ�",2,$link);	
					}
					elseif($duringtime<=$space)
					{
						$link[0]['text'] = "������һҳ";
						$link[0]['href'] = 'javascript:history.go(-1)';
						$link[1]['text'] = "��������";
						$link[1]['href'] = 'company_service.php?act=setmeal_list';
						showmsg("�����ײ��Ѿ����ڣ�ˢ��ְλ�����Ļ��֣������û���ˢ��ְλ".$_CFG['com_pointsmode_refresh_space']."�����ڲ����ظ�ˢ�£�",2,$link);
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
								$link[1]['text'] = "��������";
								$link[1]['href'] = 'company_service.php?act=setmeal_list';
								$link[2]['text'] = "������ֵ";
								$link[2]['href'] = 'company_service.php?act=order_add';
								showmsg("�����ײ��ѹ��ڣ������Ļ�����ˢ��ְλ����Ŀǰ����".$_CFG['points_byname']."���㣬���ȳ�ֵ���ֻ����������ײͣ�",0,$link);
							}
							//��/�� ����
							report_deal($_SESSION['uid'],$points_rule['jobs_refresh']['type'],$total_point);
							$user_points=get_user_points($_SESSION['uid']);
							$operator=$points_rule['jobs_refresh']['type']=="1"?"+":"-";
							write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"ˢ����{$jobs_num}��ְλ��({$operator}{$total_point})��(ʣ��:{$user_points})",1,1003,"ˢ��ְλ","{$operator}{$total_point}","{$user_points}");
						}
					}
				}
				//��̨û�п�ͨ  ������ʱ���û�������
				else
				{
					$link[0]['text'] = "������ͨ����";
					$link[0]['href'] = 'company_service.php?act=setmeal_list';
					$link[1]['text'] = "��Ա������ҳ";
					$link[1]['href'] = 'company_index.php?act=';
					showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
				}
			}
			//�û�Ա�ײ�δ���� 
			else
			{
				$mode = 2;
				$points_rule=get_cache('points_rule');
				$user_points=get_user_points($_SESSION['uid']);
				//��ȡ����ˢ�µ�ְλ��(���ײ�ģʽ��ˢ�µ�)
				$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",2);
				//����ʣ��ˢ��ְλ��(���ײ�ģʽ��ˢ�µ�)
				$surplus_time =  $setmeal['refresh_jobs_time'] - $refresh_time['count(*)'];
				//ˢ��ְλ�� ���� ʣ��ˢ��ְλ�� (����)
				if($setmeal['refresh_jobs_time']!=0&&($jobs_num>$surplus_time))
				{
					//��̨��ͨ  ������ʱ���û�������
					if($_CFG['setmeal_to_points']=='1')
					{
						//�жϵ������ˢ��ְλ�� �Ƿ� ���������ͼ������
						$refrestime=get_last_refresh_date($_SESSION['uid'],"1001",1);
						$duringtime=time()-$refrestime['max(addtime)'];
						$space = $_CFG['com_pointsmode_refresh_space']*60;
						$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",1);
						if($_CFG['com_pointsmode_refresh_time']!=0&&($refresh_time['count(*)']>=$_CFG['com_pointsmode_refresh_time']))
						{
						showmsg("ˢ��ְλ���������ײʹ������ƣ�ˢ��ְλ�����Ļ��֣�ÿ���û���ˢ�����ֻ��ˢ��".$_CFG['com_pointsmode_refresh_time']."��,�������ѳ������ˢ�´������ƣ�",2);	
						}
						elseif($duringtime<=$space)
						{
						showmsg("ˢ��ְλ���������ײʹ������ƣ�ˢ��ְλ�����Ļ��֣�����".$_CFG['com_pointsmode_refresh_space']."�����ڲ����ظ�ˢ��ְλ��",2);
						}
						else
						{
							if($points_rule['jobs_refresh']['value']>0)
							{
								//������ְλ����ˢ�� ����Ļ���
								$beyond = $jobs_num - $surplus_time;
								$surplus_total_point=$beyond*$points_rule['jobs_refresh']['value'];
								//��Ա���ֲ��������� �������
								if ($surplus_total_point>$user_points && $points_rule['jobs_refresh']['type']=="2")
								{
									$link[0]['text'] = "������һҳ";
									$link[0]['href'] = 'javascript:history.go(-1)';
									$link[1]['text'] = "��������";
									$link[1]['href'] = 'company_service.php?act=setmeal_list';
									$link[2]['text'] = "������ֵ";
									$link[2]['href'] = 'company_service.php?act=order_add';
									showmsg("��ˢ��ְλ���������ײʹ������ƣ������Ĵ������������Ļ��֣���������".$_CFG['points_byname']."���㣬���ȳ�ֵ��",0,$link);
								}
								//�жϳ�����ְλ���Ƿ� ���� �������ƴ���
								if($beyond > $_CFG['com_pointsmode_refresh_time'] && $_CFG['com_pointsmode_refresh_time']!=0)
								{
									showmsg("��ˢ��ְλ���������ײʹ������ƣ�������ְλ�����������Ļ��֣�����Ҳ������".$_CFG['points_byname']."���ƴ�����������ѡ��ְλ��",0);
								}
								if(is_array($yid)){
									for ($i=0; $i < $surplus_time; $i++) 
									{ 
										refresh_jobs($yid[$i],$_SESSION['uid']);
										write_memberslog($_SESSION['uid'],1,2004,$_SESSION['username'],"ˢ��ְλ");
										write_refresh_log($_SESSION['uid'],1001,2);
									}
									for ($i=$surplus_time; $i < $jobs_num; $i++) 
									{ 
										refresh_jobs($yid[$i],$_SESSION['uid']);
										write_memberslog($_SESSION['uid'],1,2004,$_SESSION['username'],"ˢ��ְλ");
										write_refresh_log($_SESSION['uid'],1001,1);
									}
								}
								else
								{
									refresh_jobs($yid,$_SESSION['uid']);
									write_memberslog($_SESSION['uid'],1,2004,$_SESSION['username'],"ˢ��ְλ");
									write_refresh_log($_SESSION['uid'],1001,1);
								}
								//���»�Ա����
								//��/�� ����
								report_deal($_SESSION['uid'],$points_rule['jobs_refresh']['type'],$surplus_total_point);
								$user_points=get_user_points($_SESSION['uid']);
								$operator=$points_rule['jobs_refresh']['type']=="1"?"+":"-";
								write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"ˢ����{$jobs_num}��ְλ��({$operator}{$total_point})��(ʣ��:{$user_points})",1,1003,"ˢ��ְλ","{$operator}{$total_point}","{$user_points}");
								showmsg("ˢ��ְλ�ɹ���",2);
							}
						}
					}
					//��̨û�п�ͨ  ������ʱ���û�������
					else
					{
						$link[0]['text'] = "������һҳ";
						$link[0]['href'] = 'javascript:history.go(-1)';
						$link[1]['text'] = "��������";
						$link[1]['href'] = 'company_service.php?act=setmeal_list';
						showmsg("��ˢ��ְλ���������ײʹ������� ! ",1,$link);
					}
				}
				//ˢ��ְλ�� С�� ʣ��ˢ��ְλ�� (û��)
				else
				{
					//���һ�ε�ˢ��ʱ��
					$refrestime=get_last_refresh_date($_SESSION['uid'],"1001",2);
					$duringtime=time()-$refrestime['max(addtime)'];
					$space = $setmeal['refresh_jobs_space']*60;
					$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",2);
					if($setmeal['refresh_jobs_time']!=0&&($refresh_time['count(*)']>=$setmeal['refresh_jobs_time']))
					{
					showmsg("ÿ�����ֻ��ˢ��".$setmeal['refresh_jobs_time']."��,�������ѳ������ˢ�´������ƣ�",2);
					}
					elseif($duringtime<=$space)
					{
					showmsg($setmeal['refresh_jobs_space']."�����ڲ����ظ�ˢ��ְλ��",2);	
					}
				}
			}
		}
		
		refresh_jobs($yid,$_SESSION['uid']);
		write_memberslog($_SESSION['uid'],1,2004,$_SESSION['username'],"ˢ��ְλ");
		for ($i=0; $i < $jobs_num; $i++) { 
			write_refresh_log($_SESSION['uid'],1001,$mode);
		}
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
			showmsg("ɾ��ʧ�ܣ�",2);
		}
	} 
	elseif (!empty($_REQUEST['display1']))
	{
		/*
			��ʾ�е�ְλ(���ͨ��,�����,δ��ͣ)
		*/
		if($_CFG['operation_mode']=='1'){
			activate_jobs($yid,1,$_SESSION['uid']);
			showmsg("���óɹ���",2);
		}else{
			$jobs_num= $db->get_total("select count(*) num from ".table("jobs")." where uid=$_SESSION[uid] and audit=1 and display=1 ");
			$jobs_tmp_num= $db->get_total("select count(*) num from ".table("jobs_tmp")." where uid=$_SESSION[uid] and audit=2 and display=1 ");
			$com_jobs_num=$jobs_num+$jobs_tmp_num;
			$setmeal= get_user_setmeal($_SESSION['uid']);
			if ($com_jobs_num>=$setmeal['jobs_ordinary'])
			{
				showmsg("��ǰ��ʾ��ְλ�Ѿ�������������ƣ������������ײͣ��򽫲���Ƹ��ְλ��Ϊ�رգ�",1);
			}else
			{
				activate_jobs($yid,1,$_SESSION['uid']);
				showmsg("���óɹ���",2);
			}
		}
	}
}
//���ģʽ��  :  �ж�ˢ��ְλ�Ƿ���Ҫ���Ļ���
elseif ($act=='ajax_mode_points')
{
	//Ҫˢ�µ�ְλ��
	$length = intval($_GET['length']);
	$points_rule=get_cache('points_rule');
	$setmeal=get_user_setmeal($_SESSION['uid']);
	//�û�Ա�ײ͹��� (�ײ͹��ں���û���������)
	if($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
	{
		if($_CFG['setmeal_to_points']=='1' && $points_rule['jobs_refresh']['value']>"0")
		{
			exit('ok');
		}
	}
	//��ȡ����ˢ�µ�ְλ��(���ײ�ģʽ��ˢ�µ�)
	$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001",2);
	//����ʣ��ˢ��ְλ��(���ײ�ģʽ��ˢ�µ�)
	$surplus_time =  $setmeal['refresh_jobs_time'] - $refresh_time['count(*)'];
	//ˢ��ְλ�� ���� ʣ��ˢ��ְλ�� (����)
	if($setmeal['refresh_jobs_time']!=0 && ($length>$surplus_time))
	{
		if($_CFG['setmeal_to_points']=='1' && $points_rule['jobs_refresh']['value']>"0")
		{
			exit('ok');
		}
	}
	exit('no');
}
elseif ($act=='editjobs')
{
	$jobs=get_jobs_one(intval($_GET['id']),$_SESSION['uid']);
	if (empty($jobs)) showmsg("��������",1);
	$jobs['contents'] = htmlspecialchars_decode($jobs['contents'],ENT_QUOTES);
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
	if($jobs['age']){
		$jobs_age = explode("-", $jobs['age']);
		$jobs['minage'] = $jobs_age[0];
		$jobs['maxage'] = $jobs_age[1];
	}
	$smarty->assign('user',$user);
	$smarty->assign('title','�޸�ְλ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('points_total',get_user_points($_SESSION['uid']));
	$smarty->assign('points',get_cache('points_rule'));
	$smarty->assign('subsite',get_me_subsite());
	$subsite_cn = explode('/',$jobs['district_cn']);
	$smarty->assign('subsite_cn',$subsite_cn[0]);
	$smarty->assign('district_cn',$subsite_cn[1]);
	//��������
	$smarty->assign('district',get_subsite_district($jobs['district']));
	$smarty->assign('jobs',$jobs);
	$smarty->display('member_company/company_editjobs.htm');
}
elseif ($act=='editjobs_save')
{
	$id=intval($_POST['id']);
	$add_mode=trim($_POST['add_mode']);
	if ($add_mode=='1')
	{
					$points_rule=get_cache('points_rule');
					$user_points=get_user_points($_SESSION['uid']);
					if($points_rule['jobs_edit']['type']=="2" && $points_rule['jobs_edit']['value']>0)
					{
						$total=$points_rule['jobs_edit']['value'];
						if ($total>$user_points)
						{
						$link[0]['text'] = "������һҳ";
						$link[0]['href'] = 'javascript:history.go(-1)';
						$link[1]['text'] = "������ֵ";
						$link[1]['href'] = 'company_service.php?act=order_add';
						showmsg("���".$_CFG['points_byname']."���㣬���ֵ���ٷ�����",0,$link);
						}
					}
					
	}
	elseif ($add_mode=='2')
	{
					$link[0]['text'] = "������ͨ����";
					$link[0]['href'] = 'company_service.php?act=setmeal_list';
					$link[1]['text'] = "��Ա������ҳ";
					$link[1]['href'] = 'company_index.php?act=';
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
				{					
					showmsg("�����ײ��Ѿ����ڣ������¿�ͨ",1,$link);
				}
	}

	$setsqlarr['jobs_name']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name']):showmsg('��û����дְλ���ƣ�',1);
	check_word($_CFG['filter'],$_POST['jobs_name'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['topclass']=trim($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('��ѡ��ְλ���',1);
	$setsqlarr['subclass']=trim($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['subsite_id']=!empty($_POST['subsite_id'])?intval($_POST['subsite_id']):showmsg('��ѡ����������',1);
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=empty($_POST['district_cn'])?trim($_POST['subsite_name']):(trim($_POST['subsite_name']).'/'.trim($_POST['district_cn']));
	$setsqlarr['wage']=intval($_POST['wage'])?intval($_POST['wage']):showmsg('��ѡ��н�ʴ�����',1);		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['negotiable']=intval($_POST['negotiable']);
	$setsqlarr['tag']=trim($_POST['tag']);
	$setsqlarr['tag_cn']=trim($_POST['tag_cn']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):showmsg('��ѡ��ѧ��Ҫ��',1);		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):showmsg('��ѡ�������飡',1);		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['age']=trim($_POST['minage'])."-".trim($_POST['maxage']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('��û����дְλ������',1); 
	$setsqlarr['is_entrust']=isset($_POST['is_entrust']) && intval($_POST['is_entrust'])=="0"?"0":"1";
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
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
	// �޸�ְλ ����ʱ��Ϊ
	$setsqlarr['deadline']=strtotime("".intval($_CFG['company_add_days'])." day");
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_profile['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_profile['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if ($company_profile['audit']=="1")
	{
	$_CFG['audit_verifycom_editjob']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_verifycom_editjob']):'';
	}
	else
	{
	$_CFG['audit_unexaminedcom_editjob']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_unexaminedcom_editjob']):'';
	}
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('��û����д��ϵ�ˣ�',1);
	check_word($_CFG['filter'],$_POST['contact'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):'';
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['address']=!empty($_POST['address'])?trim($_POST['address']):showmsg('��û����д��ϵ��ַ��',1);
	check_word($_CFG['filter'],$_POST['address'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['email']=!empty($_POST['email'])?trim($_POST['email']):showmsg('��û����д��ϵ���䣡',1);
	check_word($_CFG['filter'],$_POST['email'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['notify']=intval($_POST['notify']);//�ʼ�����
	$setsqlarr_contact['notify_mobile']=intval($_POST['notify_mobile']);//�ֻ�����
	
  	$setsqlarr_contact['contact_show']=intval($_POST['contact_show']);
	$setsqlarr_contact['email_show']=intval($_POST['email_show']);
	$setsqlarr_contact['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr_contact['address_show']=intval($_POST['address_show']);
	//����
	$landline_tel[]=trim($_POST['landline_tel_first'])?trim($_POST['landline_tel_first']):"0";
	$landline_tel[]=trim($_POST['landline_tel_next'])?trim($_POST['landline_tel_next']):"0";
	$landline_tel[]=trim($_POST['landline_tel_last'])?trim($_POST['landline_tel_last']):"0";
	$setsqlarr_contact['landline_tel']=implode('-', $landline_tel);
	//�������ֻ����ٶ�ѡһ
	if(empty($setsqlarr_contact['telephone']) && $setsqlarr_contact['landline_tel']=='0-0-0')
	{
		showmsg('����д�ֻ���̻�����ѡһ���ɣ�',1);
	}
 
	if (!$db->updatetable(table('jobs'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) showmsg("����ʧ�ܣ�",0);
	if (!$db->updatetable(table('jobs_tmp'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) showmsg("����ʧ�ܣ�",0);
	if (!$db->updatetable(table('jobs_contact'), $setsqlarr_contact," pid='{$id}' ")){
		showmsg("����ʧ�ܣ�",0);
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
	$link[0]['text'] = "ְλ�б�";
	$link[0]['href'] = '?act=jobs';
	$link[1]['text'] = "�鿴�޸Ľ��";
	$link[1]['href'] = "?act=editjobs&id={$id}";
	$link[2]['text'] = "��Ա������ҳ";
	$link[2]['href'] = "company_index.php";
	//
	$searchtab['nature']=$setsqlarr['nature'];
	$searchtab['subsite_id']=$setsqlarr['subsite_id'];
	$searchtab['sex']=$setsqlarr['sex'];
	$searchtab['topclass']=$setsqlarr['topclass'];
	$searchtab['category']=$setsqlarr['category'];
	$searchtab['subclass']=$setsqlarr['subclass'];
	$searchtab['sdistrict']=$setsqlarr['sdistrict'];
	$searchtab['district']=$setsqlarr['district'];
	$searchtab['education']=$setsqlarr['education'];
	$searchtab['experience']=$setsqlarr['experience'];
	$searchtab['wage']=$setsqlarr['wage'];
	$searchtab['graduate']=$setsqlarr['graduate'];	
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
	add_jobs_tag(intval($_POST['id']),$_SESSION['uid'],$_POST['tag'])?"":showmsg('����ʧ�ܣ�',0);
	distribution_jobs($id,$_SESSION['uid']);
	write_memberslog($_SESSION['uid'],$_SESSION['utype'],2002,$_SESSION['username'],"�޸���ְλ��{$setsqlarr['jobs_name']}��ְλID��{$id}");
	showmsg("�޸ĳɹ���",2,$link);
}
elseif($act == "ajax_save_jobs_templates"){
	foreach ($_POST as $key => $value) {
		$_POST[$key] = utf8_to_gbk($value);
	}
	$setsqlarr['title']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name'])."��ģ��":exit('-1');
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):exit('-1');
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):exit('-1');
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['subsite_id']=!empty($_POST['subsite_id'])?intval($_POST['subsite_id']):showmsg('��ѡ����������',1);
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):exit('-1');
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=empty($_POST['district_cn'])?trim($_POST['subsite_name']):(trim($_POST['subsite_name']).'/'.trim($_POST['district_cn']));
	$setsqlarr['education']=intval($_POST['education']);		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience']);		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['wage']=intval($_POST['wage']);		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['tag']=trim($_POST['tag']);		
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['negotiable']=intval($_POST['negotiable']);
	$setsqlarr['minage']=intval($_POST['minage']);
	$setsqlarr['maxage']=intval($_POST['maxage']);
	$setsqlarr['addtime']=time();
	$pid=$db->inserttable(table('jobs_templates'),$setsqlarr,true);
	if($pid>0){
		exit("1");
	}else{
		exit("0");
	}
}
elseif($act == 'copy_templates'){
	$id = intval($_GET['id']);
	if($id<1){
		exit("-1");
	}
	$templates = get_jobs_templates_one($id);
	$templates['contents'] = htmlspecialchars_decode($templates['contents'],ENT_QUOTES);
	if(!empty($templates)){
		foreach ($templates as $key => $value) {
			$templates[$key] = gbk_to_utf8($value);
		}
		exit(json_encode($templates));
	}else{
		exit("-1");
	}
}
elseif($act == "get_content_by_jobs_cat"){
	$id = intval($_GET['id']);
	if($id>0){
		$content = get_content_by_jobs_cat($id);
		if(!empty($content)){
			exit($content);
		}else{
			exit("-1");
		}
	}else{
		exit("-1");
	}
}
elseif ($act=='add_templates')
{
	$_SESSION['addrand']=rand(1000,5000);
	$smarty->assign('addrand',$_SESSION['addrand']);
	$smarty->assign('subsite',get_me_subsite());
	$smarty->assign('title','����ְλģ�� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_company/company_add_templates.htm');	
}
elseif($act == "add_templates_save"){
	$addrand=intval($_POST['addrand']);
	if($_SESSION['addrand']==$addrand){
	unset($_SESSION['addrand']);
	$setsqlarr['title']=!empty($_POST['title'])?trim($_POST['title']):showmsg('����дģ�����ƣ�',1);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('����дְλ������',1);
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('��ѡ��ְλ���',1);
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['subsite_id']=!empty($_POST['subsite_id'])?intval($_POST['subsite_id']):showmsg('��ѡ����������',1);
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):showmsg('��ѡ����������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=empty($_POST['district_cn'])?trim($_POST['subsite_name']):(trim($_POST['subsite_name']).'/'.trim($_POST['district_cn']));
	$setsqlarr['education']=intval($_POST['education']);		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience']);		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['wage']=intval($_POST['wage']);		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['tag']=trim($_POST['tag']);		
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['negotiable']=intval($_POST['negotiable']);
	$setsqlarr['minage']=intval($_POST['minage']);
	$setsqlarr['maxage']=intval($_POST['maxage']);
	$setsqlarr['addtime']=time();
	$pid=$db->inserttable(table('jobs_templates'),$setsqlarr,true);
	$link[0]['text'] = "ְλģ���б�";
	$link[0]['href'] = 'company_jobs.php?act=jobs_templates';
	$link[1]['text'] = "��������ְλģ��";
	$link[1]['href'] = 'company_jobs.php?act=add_templates';
	empty($pid)?showmsg("���ʧ�ܣ�",0):showmsg("��ӳɹ���",2,$link);
	}
}
elseif ($act=='edit_templates')
{
	$id = intval($_GET['id']);
	if($id<1){
		showmsg("��ѡ��ְλģ�壡",1);
	}
	$templates = get_jobs_templates_one($id);
	$_SESSION['addrand']=rand(1000,5000);
	$smarty->assign('addrand',$_SESSION['addrand']);
	$smarty->assign('templates',$templates);
	$smarty->assign('subsite',get_me_subsite());
	$subsite_cn = explode('/',$templates['district_cn']);
	$smarty->assign('subsite_cn',$subsite_cn[0]);
	$smarty->assign('district_cn',$subsite_cn[1]);
	//��������
	$smarty->assign('district',get_subsite_district($templates['district']));
	$smarty->assign('title','�޸�ְλģ�� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_company/company_edit_templates.htm');	
}
elseif($act == "edit_templates_save"){
	$id = intval($_POST['id']);
	if($id<1){
		showmsg("��ѡ��ְλģ�壡",1);
	}
	$addrand=intval($_POST['addrand']);
	if($_SESSION['addrand']==$addrand){
	unset($_SESSION['addrand']);
	$setsqlarr['title']=!empty($_POST['title'])?trim($_POST['title']):showmsg('����дģ�����ƣ�',1);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('����дְλ������',1);
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('��ѡ��ְλ���',1);
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['subsite_id']=!empty($_POST['subsite_id'])?intval($_POST['subsite_id']):showmsg('��ѡ����������',1);
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):showmsg('��ѡ����������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=empty($_POST['district_cn'])?trim($_POST['subsite_name']):(trim($_POST['subsite_name']).'/'.trim($_POST['district_cn']));
	$setsqlarr['education']=intval($_POST['education']);		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience']);		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['wage']=intval($_POST['wage']);		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['tag']=trim($_POST['tag']);	
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['negotiable']=intval($_POST['negotiable']);
	$setsqlarr['minage']=intval($_POST['minage']);
	$setsqlarr['maxage']=intval($_POST['maxage']);
	$setsqlarr['addtime']=time();
	if (!$db->updatetable(table('jobs_templates'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) showmsg("����ʧ�ܣ�",0);
	$link[0]['text'] = "ְλģ���б�";
	$link[0]['href'] = 'company_jobs.php?act=jobs_templates';
	$link[1]['text'] = "�鿴�޸Ľ��";
	$link[1]['href'] = 'company_jobs.php?act=edit_templates&id='.$id;
	showmsg("�޸ĳɹ���",2,$link);
	}
}
//΢����Ƹ
elseif($act == 'simple_jobs')
{
	if ($cominfo_flge)
	{
		$day = intval(strtotime(date("Y-m-d")))-86400;
		//ͳ�����շ�����
		$click = $db->get_total("SELECT COUNT(*) AS num FROM ".table('company_praise')." WHERE company_id={$company_profile['id']} AND click_type=1 AND addtime={$day} ");
		//ͳ�����յ�����
		$praise = $db->get_total("SELECT COUNT(*) AS num FROM ".table('company_praise')." WHERE uid={$_SESSION['uid']} AND company_id={$company_profile['id']} AND click_type=2 AND addtime={$day} ");
		//ɨ��url
		$w_url=$_CFG['site_domain'].$_CFG['site_dir']."m/m-wzp.php?company_id=".$company_profile['id']."&vip_menu=1";
		$smarty->assign('click',$click);
		$smarty->assign('praise',$praise);
		$smarty->assign('w_url',urlencode($w_url));
	    $smarty->assign('title','΢����Ƹ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	    $smarty->display('member_company/company_simple_jobs.htm');
	}
	else
	{
		$link[0]['text'] = "������ҵ����";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("Ϊ�˸��õ���ʾ΢����ƸЧ������������������ҵ���ϣ�",1,$link);
	}
}
//΢����Ƹ  ����ͳ��
elseif($act == 'data_statistics')
{
	if ($cominfo_flge)
	{
		$check_table_cache = check_cache('u'.$_SESSION['uid'].'_wzp_tabledata.cache','wzp');

		if($check_table_cache){
			$arr = json_decode($check_table_cache,1);
		}else{
			$arr = array(array());
			//����ʱ��
			$yesterday = intval(strtotime(date("Y-m-d")))-86400;
			//����ʱ��
			$week = mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"));
			$today_end = strtotime(date("Y-m-d"));
			//����ʱ��
			$last_week_day_begin = mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7,date("Y"));
			$last_week_day_end = mktime(0, 0 , 0,date("m"),date("d")-date("w"),date("Y"));
			//����ʱ��
			$month_day = strtotime(date("Y-m")."-1");
			//����ʱ��
			$month_day_begin = strtotime(date("Y-").(date('m')-1)."-1");
			$month_day_end = strtotime(date("Y-m")."-1")-86400;
			//ѭ������
			$data = $db->getall('SELECT id,company_id,uid,click_type,addtime,ip FROM '.table('company_praise')." WHERE  company_id={$company_profile['id']} ");
			foreach ($data as $key => $value) 
			{
				if($value['addtime']==$yesterday)
				{
					$arr['yesterday'][$value['click_type']] += 1;
				}
				if($value['addtime']>=$week && $value['addtime']<$today_end)
				{
					$arr['week'][$value['click_type']] += 1;
				}
				if($value['addtime']>=$last_week_day_begin && $value['addtime']<=$last_week_day_end)
				{
					$arr['last_week'][$value['click_type']] += 1;
				}
				if($value['addtime']>=$month_day  && $value['addtime']<$today_end )
				{
					$arr['month'][$value['click_type']] += 1;
				}
				if($value['addtime']>=$month_day_begin && $value['addtime']<=$month_day_end)
				{
					$arr['last_month'][$value['click_type']] += 1;
				}
				if($value['addtime']<$today_end)
				{
					$arr['total'][$value['click_type']] += 1;
				}
			}
			//����ip���ݵ���ͳ��
			$arr['yesterday'][4] = $db->get_total("SELECT COUNT(distinct ip) AS num FROM ".table('company_praise')." WHERE company_id={$company_profile['id']} AND addtime={$yesterday} ");
			$arr['week'][4] = $db->get_total("SELECT COUNT(distinct ip) AS num FROM ".table('company_praise')." WHERE company_id={$company_profile['id']} AND addtime={$week} ");
			$arr['last_week'][4] = $db->get_total("SELECT COUNT(distinct ip) AS num FROM ".table('company_praise')." WHERE company_id={$company_profile['id']} AND addtime>={$last_week_day_begin} AND addtime<={$last_week_day_end} ");
			$arr['month'][4] = $db->get_total("SELECT COUNT(distinct ip) AS num FROM ".table('company_praise')." WHERE company_id={$company_profile['id']} AND addtime={$month_day} ");
			$arr['last_month'][4] = $db->get_total("SELECT COUNT(distinct ip) AS num FROM ".table('company_praise')." WHERE company_id={$company_profile['id']} AND addtime>={$month_day_begin} AND addtime<={$month_day_end} ");
			$arr['total'][4] = $db->get_total("SELECT COUNT(distinct ip) AS num FROM ".table('company_praise')." WHERE company_id={$company_profile['id']} ");
			write_cache('u'.$_SESSION['uid'].'_wzp_tabledata.cache',json_encode($arr),'wzp');
		}
		


		/**
		* ͼ��ͳ��start
		**/
		$filter = intval($_GET['settr'])>0?intval($_GET['settr']):7;

		$check_categories_cache = check_cache('u'.$_SESSION['uid'].'_wzp_categories_'.$filter.'.cache','wzp');
		$check_dataset_cache = check_cache('u'.$_SESSION['uid'].'_wzp_dataset_'.$filter.'.cache','wzp');
		if($check_categories_cache && $check_dataset_cache){
			$categories = $check_categories_cache;
			$dataset = $check_dataset_cache;
		}else{
			for ($i=$filter; $i >0 ; $i--) { 
				$labelArr[] = strtotime(date('Y-m-d',time()-$i*86400));
			}

			$line_data = $db->getall("select * from ".table('company_praise')." where  company_id = {$company_profile['id']} AND  addtime>".strtotime(date('Y-m-d',time()-$filter*86400))." order by addtime asc");
			foreach ($line_data as $key => $value) {
				$line[$value['click_type']][$value['addtime']] += 1;
			}
			$item = 0;
			foreach ($labelArr as $key => $value) {
				$label[$item]['label'] = date('m-d',$value);
				$lineData[0][$item]['value'] = intval($line[1][$value]);
				$lineData[1][$item]['value'] = intval($line[2][$value]);
				$lineData[2][$item]['value'] = intval($line[3][$value]);
				$item++;
			}
			$categories = array(
		    	'category'=>array(
		    		$label
		    		)
		    	);
		    $categories = json_encode($categories);
			$dataset = array(
		    	array(
		    		'seriesname'=>iconv('gbk','utf-8','�����'),
			    	'data'=>array(
			    		$lineData[0]
			    		)
		    		),
		    	array(
		    		'seriesname'=>iconv('gbk','utf-8','������'),
			    	'data'=>array(
			    		$lineData[1]
			    		)
		    		),
		    	array(
		    		'seriesname'=>iconv('gbk','utf-8','������'),
			    	'data'=>array(
			    		$lineData[2]
			    		)
		    		)
		    	);
		    $dataset = json_encode($dataset);
		    write_cache('u'.$_SESSION['uid'].'_wzp_categories_'.$filter.'.cache',$categories,'wzp');
	   		write_cache('u'.$_SESSION['uid'].'_wzp_dataset_'.$filter.'.cache',$dataset,'wzp');
		}

	    $smarty->assign('categories',$categories);
		$smarty->assign('dataset',$dataset);
		/**
		* ͼ��ͳ��end
		**/
		$smarty->assign('data',$arr);
	    $smarty->assign('title','΢����Ƹ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	    $smarty->display('member_company/company_data_statistics.htm');
	}
	else
	{
		$link[0]['text'] = "������ҵ����";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("Ϊ�˸��õ���ʾ΢����ƸЧ������������������ҵ���ϣ�",1,$link);
	}
}
unset($smarty);
?>