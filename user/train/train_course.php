<?php
/*
 * 74cms ��ѵ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/train_common.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'course';
$smarty->assign('leftmenu',"course");
if ($act=='course')
{
	$wheresql=" WHERE uid='{$_SESSION['uid']}' ";
	$orderby=" order by refreshtime desc";
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('course').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('title','�γ̹��� - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$sql="SELECT * FROM ".table('course').$wheresql.$orderby;
	$smarty->assign('courses',get_course($offset,$perpage,$sql,true));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('points_rule',get_cache('points_rule'));
	$smarty->display('member_train/train_courses.htm');
}
elseif ($act=='addcourse')
{
		if ($train_profile['trainname'])
		{
			$_SESSION['addrand']=rand(1000,5000);
			$smarty->assign('addrand',$_SESSION['addrand']);
			$teachers=get_audit_teachers($_SESSION['uid'],$train_profile['id']);
			$link[0]['text'] = '��ӽ�ʦ';
			$link[0]['href'] = 'train_teacher.php?act=add_teachers';
			if(empty($teachers)){
				showmsg('�����γ�ǰ��������ӽ�ʦ��ȷ�����ͨ����',1,$link);
			}
			$smarty->assign('title','�����γ� - ��ѵ��Ա���� - '.$_CFG['site_name']);
			$smarty->assign('train_profile',$train_profile);
     		if ($_CFG['operation_train_mode']=="2")
			{
				$link[0]['text'] = "������ͨ����";
				$link[0]['href'] = 'train_service.php?act=setmeal_list';
				$link[1]['text'] = "��Ա������ҳ";
				$link[1]['href'] = 'train_index.php?act=';
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
				{					
					showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
				}
				if ($setmeal['course_num']<=0)
				{
					showmsg("��ǰ�����Ŀγ��Ѿ�������������ƣ������������ײͣ�",1,$link);
				}
				$smarty->assign('setmeal',$setmeal);
				$smarty->assign('add_mode',2);
			}
			elseif ($_CFG['operation_train_mode']=="1")
			{
				$points_rule=get_cache('points_rule');
				$user_points=get_user_points($_SESSION['uid']);
				$total=0;
				if ($points_rule['course_add']['type']=="2" && $points_rule['course_add']['value']>0)
				{
				$total=$points_rule['course_add']['value'];
				}
				if ($points_rule['course_daily']['type']=="2" && $points_rule['course_daily']['value']>0)
				{
				$total=$total+($_CFG['course_add_days_min']*$points_rule['course_daily']['value']);
				}
				if ($total>$user_points)
				{
				$link[0]['text'] = "������ֵ";
				$link[0]['href'] = 'train_service.php?act=order_add';
				$link[1]['text'] = "��Ա������ҳ";
				$link[1]['href'] = 'train_index.php?act=';
				showmsg("���".$_CFG['train_points_byname']."���㣬���ֵ���ٷ�����",0,$link);
				}
				$smarty->assign('points_total',$user_points);
				$smarty->assign('points',$points_rule);
				$smarty->assign('add_mode',1);
			}
		
			$smarty->assign('user',$user);
			$smarty->assign('teachers',$teachers);
			$captcha=get_cache('captcha');
			$smarty->assign('verify_addcourse',$captcha['verify_addcourse']);
			$smarty->assign('subsite',get_all_subsite());
			$smarty->display('member_train/train_addcourse.htm');
		}
		else
		{
		$link[0]['text'] = "���ƻ�������";
		$link[0]['href'] = 'train_info.php?act=train_profile';
		showmsg("Ϊ�˴ﵽ���õ�����Ч���������������Ļ������ϣ�",1,$link);
		}
}
elseif ($act=='addcourse_save')
{
	$captcha=get_cache('captcha');
	$postcaptcha = trim($_POST['postcaptcha']);
	if($captcha['verify_addcourse']=='1' && empty($postcaptcha))
	{
		showmsg("����д��֤��",1);
 	}
	if ($captcha['verify_addcourse']=='1' && strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
	{
		showmsg("��֤�����",1);
	}
	$add_mode=trim($_POST['add_mode']);
	$days=intval($_POST['days']);
	if ($days<$_CFG['course_add_days_min'])
	{
	showmsg("��Чʱ������Ϊ ".$_CFG['course_add_days_min']." �죡",1);
	}
	if ($_CFG['operation_train_mode']=='1')
	{
		$points_rule=get_cache('points_rule');
		$user_points=get_user_points($_SESSION['uid']);
		$total=0;
		if ($points_rule['course_add']['type']=="2" && $points_rule['course_add']['value']>0)
		{
		$total=$points_rule['course_add']['value'];
		}
		if ($points_rule['course_daily']['type']=="2" && $points_rule['course_daily']['value']>0)
		{
		$total=$total+($days*$points_rule['course_daily']['value']);
		}
		if ($total>$user_points)
		{
		$link[0]['text'] = "������ֵ";
		$link[0]['href'] = 'train_service.php?act=order_add';
		$link[1]['text'] = "��Ա������ҳ";
		$link[1]['href'] = 'train_index.php?act=';
		showmsg("���".$_CFG['train_points_byname']."���㣬���ֵ���ٷ�����",0,$link);
		}
		$setsqlarr['setmeal_deadline']=0;
	}
	elseif ($_CFG['operation_train_mode']=='2')
	{
		$link[0]['text'] = "������ͨ����";
		$link[0]['href'] = 'train_service.php?act=setmeal_list';
		$link[1]['text'] = "��Ա������ҳ";
		$link[1]['href'] = 'train_index.php?act=';
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{					
			showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
		}
		if ($setmeal['course_num']<=0)
		{
			showmsg("��ǰ�����Ŀγ��Ѿ�������������ƣ������������ײͣ�",1,$link);
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
	$setsqlarr['trainname']=$train_profile['trainname'];
	$setsqlarr['train_id']=$train_profile['id'];
	
	$setsqlarr['course_name']=!empty($_POST['course_name'])?trim($_POST['course_name']):showmsg('��û����д�γ����ƣ�',1);
	check_word($_CFG['filter'],$_POST['course_name'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('��ѡ��γ����',1);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['subsite_id']=!empty($_POST['subsite_id'])?intval($_POST['subsite_id']):showmsg('��ѡ����������',1);
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=empty($_POST['district_cn'])?trim($_POST['subsite_name']):(trim($_POST['subsite_name']).'/'.trim($_POST['district_cn']));
	$setsqlarr['classtype']=!empty($_POST['classtype'])?intval($_POST['classtype']):showmsg('��ѡ���Ͽΰ��ƣ�',1);
	$setsqlarr['classtype_cn']=trim($_POST['classtype_cn']);
	$setsqlarr['teacher_id']=!empty($_POST['teacher_id'])?intval($_POST['teacher_id']):showmsg('��ѡ�������ˣ�',1);
	$setsqlarr['teacher_cn']=trim($_POST['teacher_cn']);
	$setsqlarr['starttime']=intval(convert_datefm($_POST['starttime'],2));
	if (empty($setsqlarr['starttime']))
	{
	showmsg('����д����ʱ�䣡ʱ���ʽ��YYYY-MM-DD',1);
	}	
	$setsqlarr['train_object']=!empty($_POST['train_object'])?trim($_POST['train_object']):showmsg('��û����д�ڿζ���',1);
	check_word($_CFG['filter'],$_POST['train_object'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['train_certificate']=!empty($_POST['train_certificate'])?trim($_POST['train_certificate']):'';
	check_word($_CFG['filter'],$_POST['train_certificate'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['classhour']=!empty($_POST['classhour'])?intval($_POST['classhour']):showmsg('��û����д�ڿ�ѧʱ��',1);
	$setsqlarr['train_expenses']=!empty($_POST['train_expenses'])?intval($_POST['train_expenses']):showmsg('��û����д��ѵ���ã�',1);
	$setsqlarr['favour_expenses']=!empty($_POST['favour_expenses'])?intval($_POST['favour_expenses']):showmsg('��û����д�Żݼ۸�',1);
	
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('��û����д�γ�������',1);
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['addtime']=$timestamp;
	$setsqlarr['deadline']=strtotime("".intval($_POST['days'])." day");
	$setsqlarr['refreshtime']=$timestamp;
	$setsqlarr['key']=$setsqlarr['course_name'].$train_profile['trainname'].$setsqlarr['teacher_cn'].$setsqlarr['train_certificate'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['course_name']} {$train_profile['trainname']} {$setsqlarr['teacher_cn']} {$setsqlarr['train_certificate']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$setsqlarr['likekey']="{$setsqlarr['course_name']},{$train_profile['trainname']},{$setsqlarr['teacher_cn']},{$setsqlarr['train_certificate']}";
	$setsqlarr['tpl']=$train_profile['tpl'];
	$setsqlarr['map_x']=$train_profile['map_x'];
	$setsqlarr['map_y']=$train_profile['map_y'];
	if ($train_profile['audit']=="1")
	{
	$setsqlarr['audit']=intval($_CFG['audit_verifytrain_addcourse']);
	}
	else
	{
	$setsqlarr['audit']=intval($_CFG['audit_unexaminedtrain_addcourse']);
	}
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('��û����д��ϵ�ˣ�',1);
	check_word($_CFG['filter'],$_POST['contact'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['qq']=trim($_POST['qq']);
	check_word($_CFG['filter'],$_POST['qq'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):showmsg('��û����д��ϵ�绰��',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['address']=!empty($_POST['address'])?trim($_POST['address']):showmsg('��û����д��ϵ��ַ��',1);
	check_word($_CFG['filter'],$_POST['address'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['email']=!empty($_POST['email'])?trim($_POST['email']):showmsg('��û����д��ϵ���䣡',1);
	check_word($_CFG['filter'],$_POST['email'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['notify']=intval($_POST['notify']);
	
	$setsqlarr_contact['contact_show']=intval($_POST['contact_show']);
	$setsqlarr_contact['email_show']=intval($_POST['email_show']);
	$setsqlarr_contact['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr_contact['address_show']=intval($_POST['address_show']);
	$setsqlarr_contact['qq_show']=intval($_POST['qq_show']);
	
	//��ӿγ���Ϣ
	$pid=$db->inserttable(table('course'),$setsqlarr,true);
	empty($pid)?showmsg("���ʧ�ܣ�",0):'';

	baidu_submiturl(url_rewrite('QS_train_curriculumshow',array('id'=>$pid)),'addcourse');
	//�����ϵ��ʽ
	$setsqlarr_contact['pid']=$pid;
	!$db->inserttable(table('course_contact'),$setsqlarr_contact)?showmsg("���ʧ�ܣ�",0):'';
	

	if ($_CFG['operation_train_mode']=='1')
	{
		if ($points_rule['course_add']['value']>0)
		{
		report_deal($_SESSION['uid'],$points_rule['course_add']['type'],$points_rule['course_add']['value']);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['course_add']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],4,9101,$_SESSION['username'],"�����˿γ̣�<strong>{$setsqlarr['course_name']}</strong>��({$operator}{$points_rule['course_add']['value']})��(ʣ��:{$user_points})");
		}
		if (intval($_POST['days'])>0 && $points_rule['course_daily']['value']>0)
		{
		$points_day=intval($_POST['days'])*$points_rule['course_daily']['value'];
		report_deal($_SESSION['uid'],$points_rule['course_daily']['type'],$points_day);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['course_daily']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],4,9101,$_SESSION['username'],"������ͨ�γ�:<strong>{$_POST['course_name']}</strong>����Ч��Ϊ{$_POST['days']}�죬({$operator}{$points_day})��(ʣ��:{$user_points})");
		}
	}
	elseif ($_CFG['operation_train_mode']=='2')
	{
		action_user_setmeal($_SESSION['uid'],"course_num");
		$setmeal=get_user_setmeal($_SESSION['uid']);
		write_memberslog($_SESSION['uid'],4,9102,$_SESSION['username'],"������ͨ�γ�:<strong>{$_POST['course_name']}</strong>�������Է�����ͨ�γ�:<strong>{$setmeal['course_num']}</strong>��");
	}
	}

	$link[0]['text'] = '������ӿγ�';
	$link[0]['href'] = 'train_course.php?act=addcourse';
	$link[1]['text'] = "��Ա������ҳ";
	$link[1]['href'] = 'train_index.php?act=';
	showmsg("�����ɹ���",2,$link);
}
elseif ($act=='course_perform')
{
	$yid =!empty($_POST['y_id'])?$_POST['y_id']:$_GET['y_id'];
    $course_num=count($yid);
	if (empty($yid))
	{
	showmsg("��û��ѡ��γ̣�",1);
	}
	$refresh=!empty($_POST['refresh'])?$_POST['refresh']:$_GET['refresh'];
	$delete=!empty($_POST['delete'])?$_POST['delete']:$_GET['delete'];
    if ($refresh)
	{
		
		if($_CFG['operation_train_mode']=='1'){
			$refrestime=get_last_refresh_date($_SESSION['uid'],"4001");
			$duringtime=time()-$refrestime['max(addtime)'];
			$space = $_CFG['train_pointsmode_refresh_space']*60;
			$refresh_time = get_today_refresh_times($_SESSION['uid'],"4001");
			if($_CFG['train_pointsmode_refresh_time']!=0&&($refresh_time['count(*)']>=$_CFG['train_pointsmode_refresh_time']))
			{
			showmsg("ÿ�����ֻ��ˢ��".$_CFG['train_pointsmode_refresh_time']."��,�������ѳ������ˢ�´������ƣ�",2);	
			}
			elseif($duringtime<=$space){
			showmsg($_CFG['train_pointsmode_refresh_space']."�����ڲ����ظ�ˢ�¿γ̣�",2);
			}
			else 
			{
				$points_rule=get_cache('points_rule');
				if($points_rule['course_refresh']['value']>0)
				{
					$user_points=get_user_points($_SESSION['uid']);
					$total_point=$course_num*$points_rule['course_refresh']['value'];
					if ($total_point>$user_points && $points_rule['course_refresh']['type']=="2")
					{
							$link[0]['text'] = "������һҳ";
							$link[0]['href'] = 'javascript:history.go(-1)';
							$link[1]['text'] = "������ֵ";
							$link[1]['href'] = 'train_service.php?act=order_add';
					showmsg("����".$_CFG['train_points_byname']."���㣬���ȳ�ֵ��",0,$link);
					}
					report_deal($_SESSION['uid'],$points_rule['course_refresh']['type'],$total_point);
					$user_points=get_user_points($_SESSION['uid']);
					$operator=$points_rule['course_refresh']['type']=="1"?"+":"-";
					write_memberslog($_SESSION['uid'],4,9101,$_SESSION['username'],"ˢ�¿γ���{$course_num}���γ̣�({$operator}{$total_point})��(ʣ��:{$user_points})");
				}
			}
		}
		elseif($_CFG['operation_train_mode']=='2') 
		{
			//����ˢ��ʱ��
			//�һ�ε�ˢ��ʱ��
			$link[0]['text'] = "������ͨ����";
			$link[0]['href'] = 'train_service.php?act=setmeal_list';
			$link[1]['text'] = "��Ա������ҳ";
			$link[1]['href'] = 'train_index.php?act=';
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
				$refrestime=get_last_refresh_date($_SESSION['uid'],"4001");
				$duringtime=time()-$refrestime['max(addtime)'];
				$space = $setmeal['refresh_course_space']*60;
				$refresh_time = get_today_refresh_times($_SESSION['uid'],"4001");
				if($setmeal['refresh_course_time']!=0&&($refresh_time['count(*)']>=$setmeal['refresh_course_time']))
				{
				showmsg("ÿ�����ֻ��ˢ��".$setmeal['refresh_course_time']."��,�������ѳ������ˢ�´������ƣ�",2);
				}
				elseif($duringtime<=$space){	
				showmsg($setmeal['refresh_course_space']."�����ڲ����ظ�ˢ�¿γ̣�",2);
				}
			}
		}
		refresh_course($yid,$_SESSION['uid']);
		write_memberslog($_SESSION['uid'],4,8203,$_SESSION['username'],"ˢ�¿γ�");	
		write_refresh_log($_SESSION['uid'],4001);		
		showmsg("ˢ�¿γ̳ɹ���",2);
		
	}
	elseif ($delete)
	{
		if($n=del_course($yid,$_SESSION['uid']))
		{
			showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
		}
		else
		{
		showmsg("ɾ��ʧ�ܣ�",0);
		}
	}
	elseif (!empty($_POST['display1']))
	{
	activate_course($yid,1,$_SESSION['uid']);
	showmsg("���óɹ���",2);
	}
	elseif (!empty($_POST['display2']))
	{
	activate_course($yid,2,$_SESSION['uid']);
	showmsg("���óɹ���",2);
	}
}
elseif ($act=='editcourse')
{
	$course=get_course_one(intval($_GET['id']),$_SESSION['uid']);
	if (empty($course)) showmsg("��������",1);
	$teachers=get_audit_teachers($_SESSION['uid'],$train_profile['id']);
	$smarty->assign('user',$user);
	$smarty->assign('title','�޸Ŀγ� - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('points_total',get_user_points($_SESSION['uid']));
	$smarty->assign('points',get_cache('points_rule'));
	$smarty->assign('course',$course);
	$smarty->assign('teachers',$teachers);
	$smarty->assign('subsite',get_all_subsite());
	$subsite_cn = explode('/',$course['district_cn']);
	$smarty->assign('subsite_cn',$subsite_cn[0]);
	$smarty->assign('district_cn',$subsite_cn[1]);
	//��������
	$smarty->assign('district',get_subsite_district($course['district']));
	$smarty->display('member_train/train_editcourse.htm');
}
elseif ($act=='editcourse_save')
{
	$id=intval($_POST['id']);
	$days=intval($_POST['days']);
	if ($_CFG['operation_train_mode']=='1')
	{
		$add_mode=1;
		$points_rule=get_cache('points_rule');
		$user_points=get_user_points($_SESSION['uid']);
		$total=0;
		if($points_rule['course_edit']['type']=="2" && $points_rule['course_edit']['value']>0)
		{
		$total=$points_rule['course_edit']['value'];
		}
		if($points_rule['course_daily']['type']=="2" && $points_rule['course_daily']['value']>0)
		{
		$total=$total+($days*$points_rule['course_daily']['value']);
		}
		if ($total>$user_points)
		{
		$link[0]['text'] = "������һҳ";
		$link[0]['href'] = 'javascript:history.go(-1)';
		$link[1]['text'] = "������ֵ";
		$link[1]['href'] = 'train_service.php?act=order_add';
		showmsg("���".$_CFG['train_points_byname']."���㣬���ֵ���ٷ�����",0,$link);
		}
	}
	elseif ($_CFG['operation_train_mode']=='2')
	{
		$add_mode=2;
		$link[0]['text'] = "������ͨ����";
		$link[0]['href'] = 'train_service.php?act=setmeal_list';
		$link[1]['text'] = "��Ա������ҳ";
		$link[1]['href'] = 'train_index.php?act=';
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if ($setmeal['endtime']<time() && $setmeal['endtime']<>'0')
		{					
			showmsg("�˿γ�ͨ�������ײͷ����������ײ��Ѿ����ڣ������¿�ͨ",1,$link);
		}
		$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
		$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
		$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
	}

	$setsqlarr['add_mode']=intval($add_mode);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['trainname']=$train_profile['trainname'];
	$setsqlarr['train_id']=$train_profile['id'];
	
	$setsqlarr['course_name']=!empty($_POST['course_name'])?trim($_POST['course_name']):showmsg('��û����д�γ����ƣ�',1);
	check_word($_CFG['filter'],$_POST['course_name'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('��ѡ��γ����',1);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['subsite_id']=!empty($_POST['subsite_id'])?intval($_POST['subsite_id']):showmsg('��ѡ����������',1);
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=empty($_POST['district_cn'])?trim($_POST['subsite_name']):(trim($_POST['subsite_name']).'/'.trim($_POST['district_cn']));
	$setsqlarr['classtype']=!empty($_POST['classtype'])?intval($_POST['classtype']):showmsg('��ѡ���Ͽΰ��ƣ�',1);
	$setsqlarr['classtype_cn']=trim($_POST['classtype_cn']);
	$setsqlarr['teacher_id']=!empty($_POST['teacher_id'])?intval($_POST['teacher_id']):showmsg('��ѡ�������ˣ�',1);
	$setsqlarr['teacher_cn']=trim($_POST['teacher_cn']);
	$setsqlarr['starttime']=intval(convert_datefm($_POST['starttime'],2));
	if (empty($setsqlarr['starttime']))
	{
	showmsg('����д����ʱ�䣡ʱ���ʽ��YYYY-MM-DD',1);
	}	
	$setsqlarr['train_object']=!empty($_POST['train_object'])?trim($_POST['train_object']):showmsg('��û����д�ڿζ���',1);
	check_word($_CFG['filter'],$_POST['train_object'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['train_certificate']=!empty($_POST['train_certificate'])?trim($_POST['train_certificate']):'';
	check_word($_CFG['filter'],$_POST['train_certificate'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['classhour']=!empty($_POST['classhour'])?intval($_POST['classhour']):showmsg('��û����д�ڿ�ѧʱ��',1);
	$setsqlarr['train_expenses']=!empty($_POST['train_expenses'])?intval($_POST['train_expenses']):showmsg('��û����д��ѵ���ã�',1);
	$setsqlarr['favour_expenses']=!empty($_POST['favour_expenses'])?intval($_POST['favour_expenses']):showmsg('��û����д�Żݼ۸�',1);
	
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('��û����д�γ�������',1);
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
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
	}

	$setsqlarr['refreshtime']=$timestamp;
	$setsqlarr['key']=$setsqlarr['course_name'].$train_profile['trainname'].$setsqlarr['teacher_cn'].$setsqlarr['train_certificate'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['course_name']} {$train_profile['trainname']} {$setsqlarr['teacher_cn']} {$setsqlarr['train_certificate']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$setsqlarr['likekey']="{$setsqlarr['course_name']},{$train_profile['trainname']},{$setsqlarr['teacher_cn']},{$setsqlarr['train_certificate']}";
	$setsqlarr['tpl']=$train_profile['tpl'];
	$setsqlarr['map_x']=$train_profile['map_x'];
	$setsqlarr['map_y']=$train_profile['map_y'];
	if ($train_profile['audit']=="1")
	{
	$setsqlarr['audit']=intval($_CFG['audit_verifytrain_editcourse']);
	}
	else
	{
	$setsqlarr['audit']=intval($_CFG['audit_unexaminedtrain_editcourse']);
	}
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('��û����д��ϵ�ˣ�',1);
	check_word($_CFG['filter'],$_POST['contact'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['qq']=trim($_POST['qq']);
	check_word($_CFG['filter'],$_POST['qq'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):showmsg('��û����д��ϵ�绰��',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['address']=!empty($_POST['address'])?trim($_POST['address']):showmsg('��û����д��ϵ��ַ��',1);
	check_word($_CFG['filter'],$_POST['address'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['email']=!empty($_POST['email'])?trim($_POST['email']):showmsg('��û����д��ϵ���䣡',1);
	check_word($_CFG['filter'],$_POST['email'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['notify']=intval($_POST['notify']);
	
	$setsqlarr_contact['contact_show']=intval($_POST['contact_show']);
	$setsqlarr_contact['email_show']=intval($_POST['email_show']);
	$setsqlarr_contact['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr_contact['address_show']=intval($_POST['address_show']);
	$setsqlarr_contact['qq_show']=intval($_POST['qq_show']);		
	if (!$db->updatetable(table('course'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) showmsg("����ʧ�ܣ�",0);
	if (!$db->updatetable(table('course_contact'), $setsqlarr_contact," pid='{$id}' ")) showmsg("����ʧ�ܣ�",0);
	if ($_CFG['operation_train_mode']=='1')
	{
		if ($points_rule['course_edit']['value']>0)
		{
		report_deal($_SESSION['uid'],$points_rule['course_edit']['type'],$points_rule['course_edit']['value']);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['course_edit']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],4,9101,$_SESSION['username'],"�޸Ŀγ̣�<strong>{$setsqlarr['course_name']}</strong>��({$operator}{$points_rule['course_edit']['value']})��(ʣ��:{$user_points})");
		}
		if ($days>0 && $points_rule['course_daily']['value']>0)
		{
		$points_day=intval($_POST['days'])*$points_rule['course_daily']['value'];
		report_deal($_SESSION['uid'],$points_rule['course_daily']['type'],$points_day);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['course_daily']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],4,9101,$_SESSION['username'],"�ӳ��γ�({$_POST['course_name']})��Ч��Ϊ{$_POST['days']}�죬({$operator}{$points_day})��(ʣ��:{$user_points})");
		}
	}	 
	$link[0]['text'] = "�γ��б�";
	$link[0]['href'] = '?act=course';
	$link[1]['text'] = "�鿴�޸Ľ��";
	$link[1]['href'] = "?act=editcourse&id={$id}";
	$link[2]['text'] = "��Ա������ҳ";
	$link[2]['href'] = "train_index.php";
	write_memberslog($_SESSION['uid'],$_SESSION['utype'],8202,$_SESSION['username'],"�޸��˿γ̣�{$setsqlarr['course_name']}���γ�ID��{$id}");
	showmsg("�޸ĳɹ���",2,$link);
}
unset($smarty);
?>