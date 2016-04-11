<?php
/*
 * 74cms ��ҵ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"recruitment");
if ($act=='apply_jobs')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$joinsql=" LEFT JOIN  ".table('resume')." AS r  ON  a.resume_id=r.id ";
	$wheresql=" WHERE a.company_uid='{$_SESSION['uid']}' ";
	$look=intval($_GET['look']);
	if($look>0)$wheresql.=" AND a.personal_look='{$look}'";
	$state=intval($_GET['state']);
	if($state>0)
	{
		$joinsql.=" left join ".table('company_label_resume')." as l on l.resume_id=a.resume_id ";
		$wheresql.=" AND l.resume_state=$state AND l.uid={$_SESSION['uid']} ";
	}
	$jobsid=intval($_GET['jobsid']);
	if($jobsid>0){
		$wheresql.=" AND a.jobs_id='{$jobsid}' ";
		$sql="select jobs_name from ".table("jobs")." where id=".intval($_GET['jobsid'])." ";
		$row=$db->getone($sql);
		$smarty->assign('jobs_name',$row["jobs_name"]);
	}
	$is_apply=intval($_GET['is_apply']);
	if($is_apply>0)
	{
		if($is_apply==1)
		{
			$wheresql.=" AND a.is_apply=1 ";
		}elseif($is_apply==5)
		{
			$wheresql.=" AND a.is_apply=5 ";
		}
		else
		{
			$wheresql.=" AND a.is_apply=0 ";
		}
		
	}
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply')." AS a  ".$joinsql." {$wheresql} ";
	$total=$db->get_total($total_sql);
	$page = new page(array('total'=>$total, 'perpage'=>$perpage));
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('act',$act);
	$smarty->assign('title','�յ���ְλ���� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('jobs_apply',get_apply_jobs($offset,$perpage,$joinsql.$wheresql));
	if($total>$perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	$smarty->assign('count',count_jobs_apply($_SESSION['uid'],0,$jobsid));
	$smarty->assign('count1',count_jobs_apply($_SESSION['uid'],1,$jobsid));
	$smarty->assign('count2',count_jobs_apply($_SESSION['uid'],2,$jobsid));
	$smarty->assign('jobs',get_auditjobs($_SESSION['uid']));
	$smarty->display('member_company/company_apply_jobs.htm');
}
elseif ($act=='set_apply_jobs')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ���κ���Ŀ��",1);
	set_apply($yid,$_SESSION['uid'],2)?showmsg("���óɹ���",2):showmsg("����ʧ�ܣ�",0);
}
elseif ($act=='apply_jobs_del')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ����Ŀ��",1);
	if ($n=del_apply_jobs($yid,$_SESSION['uid']))
	{
	showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	showmsg("ʧ�ܣ�",0);
	}
}
elseif ($act=='down_resume_list')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$joinsql=" LEFT JOIN  ".table('resume')." as r ON d.resume_id=r.id ";
	$wheresql=" WHERE  d.company_uid='{$_SESSION['uid']}' ";
	$settr=intval($_GET['settr']);
	$talent=intval($_GET['talent']);
	$state=intval($_GET['state']);//���״̬
	if($settr>0)
	{
	$settr_val=strtotime("-{$settr} day");
	$wheresql.=" AND d.down_addtime>{$settr_val} ";
	}
	if($talent){
		$wheresql.=" AND r.talent=1 ";
	}
	if($state>0)
	{
		$joinsql.=" left join ".table('company_label_resume')." as l on l.resume_id=d.resume_id ";
		$wheresql.=" AND l.resume_state=$state ";
	}

	$total_sql="SELECT COUNT(*) AS num FROM ".table('company_down_resume')." as d".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title','�����صļ��� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('list',get_down_resume($offset,$perpage,$joinsql.$wheresql));
	if($total_val>$perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_company/company_down_resume.htm');
}
elseif ($act=='down_resume_del')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ�������",1);
	if ($n=del_down_resume($yid,$_SESSION['uid']))
	{
	showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	showmsg("ʧ�ܣ�",0);
	}
}
elseif ($act=='perform')
{
	$id =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ�������",1);
	if(!empty($_REQUEST['shift'])){
		$num=down_to_favorites($id,$_SESSION['uid']);
		if ($num==='full')
		{
		showmsg("�˲ſ�����!",1);
		}
		elseif($num>0)
		{
		showmsg("��ӳɹ�������� {$num} ��",2);
		}
		else
		{
		showmsg("���ʧ��,�Ѿ����ڣ�",1);
		}
	}elseif(!empty($_REQUEST['attention_shift'])){
		$num=attention_to_favorites($id,$_SESSION['uid']);
		if ($num==='full')
		{
		showmsg("�˲ſ�����!",1);
		}
		elseif($num>0)
		{
		showmsg("��ӳɹ�������� {$num} ��",2);
		}
		else
		{
		showmsg("���ʧ��,�Ѿ����ڣ�",1);
		}
	}
	
}
elseif ($act=='favorites_list')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$joinsql=" LEFT JOIN  ".table('resume')." AS r ON  f.resume_id=r.id ";
	$wheresql= " WHERE f.company_uid='{$_SESSION['uid']}' ";
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND f.favoritesa_ddtime>".$settr_val;
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('company_favorites')." AS f ".$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('title','��ҵ�˲ſ� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('favorites',get_favorites($offset, $perpage,$joinsql.$wheresql));
	if($total_val>$perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_company/company_favorites.htm');
}
elseif ($act=='favorites_del')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ�������",1);
	if ($n=del_favorites($yid,$_SESSION['uid']))
	{
	showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	showmsg("ʧ�ܣ�",0);
	}
}
//�����������б�
elseif ($act=='interview_list')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$joinsql=" LEFT JOIN ".table('resume')." as r ON i.resume_id=r.id ";
	$wheresql=" WHERE i.company_uid='{$_SESSION['uid']}' ";
	//����ְλ ɸѡ
	$jobsid=intval($_GET['jobsid']);
	if($jobsid>0)
	{
		$wheresql.=" AND i.jobs_id='{$jobsid}' ";
	}
	//�Է��鿴״̬ ˧ѡ
	$look=intval($_GET['look']);
	if($look>0)$wheresql.=" AND  i.personal_look='{$look}' ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('company_interview')." as i ".$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$resume = get_interview($offset, $perpage,$joinsql.$wheresql);
	$smarty->assign('act',$act);
	$smarty->assign('title','�ҷ������������ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('resume',$resume);
	$count1=count_interview($_SESSION['uid'],1,$jobsid);//δ�鿴
	$count2=count_interview($_SESSION['uid'],2,$jobsid);//�Ѳ鿴
	$count=$count1+$count2;
	$smarty->assign('count',$count);
	$smarty->assign('count1',$count1);
	$smarty->assign('count2',$count2);
	$smarty->assign('filter_jobs',get_interview_jobs($_SESSION['uid']));
	if($total_val>$perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_company/company_interview.htm');
}
//ɾ������������Ϣ
elseif ($act=='interview_del')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ�������",1);
	if (del_interview($yid,$_SESSION['uid']))
	{
		showmsg("ɾ���ɹ���",2);
	}
	else
	{
		showmsg("ɾ��ʧ�ܣ�",0);
	}
}
//������ļ���
elseif ($act=='my_attention')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$joinsql=" LEFT JOIN  ".table('resume')." AS r  ON  a.resumeid=r.id ";
	$wheresql=" WHERE a.uid='{$_SESSION['uid']}' ";
	//�鿴ʱ��ɸѡ
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND a.addtime>".$settr_val;
	}
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('view_resume')." AS a  {$wheresql} ";
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('title','�����¼ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('resume_list',get_my_attention($offset,$perpage,$joinsql.$wheresql));
	if($total_val>$perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_company/company_my_attention.htm');
}
//ɾ�� ������ļ���
elseif($act == 'del_my_attention')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ�������",1);
	if ($n=del_my_attention($yid,$_SESSION['uid']))
	{
	showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	showmsg("ɾ��ʧ�ܣ�",0);
	}
}
//�ղ� ����
elseif($act == 'fav_att_resume')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ�������",1);
	$n = add_favorites($yid,$_SESSION['uid']);
	if(intval($n) > 0)
	{
	showmsg("�ղسɹ������ղ� {$n} ��",2);
	}
	else
	{
	showmsg("�ղ�ʧ�ܣ�",0);
	}
}
//�鿴 "˭�����ҵ�ְλ" ��Ϣ
elseif ($act=='view_jobs_log')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	//ɸѡ ְλ
	if(intval($_GET['jobsid'])>0)
	{
		$wheresql=" WHERE `jobsid`=".intval($_GET['jobsid'])." ";
	}
	else
	{
		$my_jobs = get_my_jobs(intval($_SESSION['uid']));
		if(empty($my_jobs)){
			$wheresql=" WHERE 0";
		}
		else{
			$wheresql=" WHERE `jobsid` in(".$my_jobs.") ";
		}
		
	}
	//ɸѡ �鿴ʱ��
	$settr = intval($_GET['settr']);
	if($settr>0)
	{
		if(empty($wheresql))
		{
			$settr_val=strtotime("-".$settr." day");
			$wheresql="WHERE addtime>".$settr_val;
		}
		else
		{
			$settr_val=strtotime("-".$settr." day");
			$wheresql.=" AND addtime>".$settr_val;
		}
	}
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('view_jobs')." {$wheresql} ";
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('title','�����¼ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('jobs',get_my_jobs($_SESSION['uid'],true));	
	$smarty->assign('user_list',get_view_users($offset,$perpage,$wheresql));
	if($total_val>$perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_company/company_view_jobs.htm');
}
//ɾ�� "˭�����ҵ�ְλ" ��¼
elseif($act == 'del_view_jobs')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ����Ŀ��",1);
	if(!del_view_jobs($yid))
	{
		showmsg("ɾ��ʧ�ܣ�",0);
	}
	else
	{
		showmsg("ɾ���ɹ���",2);
	}
}
// ԤԼˢ��
elseif($act == "refresh_appointment")
{
	/* �ж��ײ� */
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

	if($add_mode==2)
	{
		exit("��ģʽ�²���ʹ��ԤԼˢ�£�");
	}
	else
	{
		$get_data=$_GET['data_arr'];
		if (is_string($get_data))
		{
			$array=explode(",",$get_data);
			$data_arr[]=$array;
		}
		else
		{
			foreach ($get_data as $key => $value)
			{
				$array=explode(",",$value);
				$data_arr[$key]=$array;
			}
		}
		foreach ($data_arr as $key => $value)
		{
			$points+=$value[2];
		}
		$user_points=get_user_points($_SESSION['uid']);
		if($points>$user_points)
		{
			exit("����ԤԼ��Ҫ".$points."��«��,���ĺ�«��Ϊ".$user_points.",��«�Ҳ���,���ֵ���ٽ���ԤԼ��");
		}
		else
		{
			foreach ($data_arr as $key => $value)
			{
				$setarr['uid']=$_SESSION['uid'];
				$setarr['jobs_id']=$value[0];
				$setarr['appointment_time']=$value[1];
				$setarr['appointment_time_available']=$value[1];
				$setarr['points']=$value[2];
				$db->inserttable(table('jobs_appointment_refresh'),$setarr);
				$jobarr['auto_refresh']=1;
				$db->updatetable(table('jobs'),$jobarr,array("id"=>$setarr['jobs_id'],"uid"=>$setarr['uid']));
				/* ������«��  */
			}
			report_deal($_SESSION['uid'],2,$points);
			exit("ԤԼˢ�³ɹ���");
		}
	}
}

elseif ($act=='edit_resume')
{
	require_once(QISHI_ROOT_PATH.'include/fun_company_personal.php');

	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:showmsg("���������ڣ�",1);


	$pid=intval($id);

	$sql = "select * from ".table('resume')."  WHERE id='{$pid}' LIMIT 1";
	$rs=$db->getone($sql);
	$uid=$rs["uid"];

	$smarty->assign('h_title',"�鿴����");
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$smarty->assign('send_key',$_SESSION['send_mobile_key']);
	$resume_basic = get_resume_basic($uid,$pid);
	$smarty->assign('resume_basic',$resume_basic);
	$smarty->assign('resume_education',get_resume_education($uid,$pid));
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$smarty->assign('resume_language',get_resume_language($uid,$pid));
	$smarty->assign('resume_credent',get_resume_credent($uid,$pid));
	$smarty->assign('resume_img',get_resume_img($uid,$pid));
	$smarty->assign('subsite',get_all_subsite());
	$subsite_cn = explode('/',$resume_basic['district_cn']);
	$smarty->assign('subsite_cn',$subsite_cn[0]);
	$smarty->assign('district_cn',$subsite_cn[1]);
	//��������
	$smarty->assign('district',get_subsite_district($resume_basic['district']));

	$resume_jobs=get_resume_jobs($pid);
	if ($resume_jobs)
	{
		foreach($resume_jobs as $rjob)
		{
			$jobsid[]=$rjob['topclass'].".".$rjob['category'].".".$rjob['subclass'];
		}
		$resume_jobs_id=implode(",",$jobsid);
	}
	$smarty->assign('resume_jobs_id',$resume_jobs_id);
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('user',$user);
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_resume',$captcha['verify_resume']);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->display('member_company/personal_resume_edit.htm');
}
unset($smarty);
?>