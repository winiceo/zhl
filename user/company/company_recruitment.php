<?php
/*
 * 74cms 企业会员中心
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
	$smarty->assign('title','收到的职位申请 - 企业会员中心 - '.$_CFG['site_name']);
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
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("你没有选择任何项目！",1);
	set_apply($yid,$_SESSION['uid'],2)?showmsg("设置成功！",2):showmsg("设置失败！",0);
}
elseif ($act=='apply_jobs_del')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("你没有选择项目！",1);
	if ($n=del_apply_jobs($yid,$_SESSION['uid']))
	{
	showmsg("删除成功！共删除 {$n} 行",2);
	}
	else
	{
	showmsg("失败！",0);
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
	$state=intval($_GET['state']);//标记状态
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
	$smarty->assign('title','已下载的简历 - 企业会员中心 - '.$_CFG['site_name']);
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
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("你没有选择简历！",1);
	if ($n=del_down_resume($yid,$_SESSION['uid']))
	{
	showmsg("删除成功！共删除 {$n} 行",2);
	}
	else
	{
	showmsg("失败！",0);
	}
}
elseif ($act=='perform')
{
	$id =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("你没有选择简历！",1);
	if(!empty($_REQUEST['shift'])){
		$num=down_to_favorites($id,$_SESSION['uid']);
		if ($num==='full')
		{
		showmsg("人才库已满!",1);
		}
		elseif($num>0)
		{
		showmsg("添加成功，共添加 {$num} 条",2);
		}
		else
		{
		showmsg("添加失败,已经存在！",1);
		}
	}elseif(!empty($_REQUEST['attention_shift'])){
		$num=attention_to_favorites($id,$_SESSION['uid']);
		if ($num==='full')
		{
		showmsg("人才库已满!",1);
		}
		elseif($num>0)
		{
		showmsg("添加成功，共添加 {$num} 条",2);
		}
		else
		{
		showmsg("添加失败,已经存在！",1);
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
	$smarty->assign('title','企业人才库 - 企业会员中心 - '.$_CFG['site_name']);
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
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("你没有选择简历！",1);
	if ($n=del_favorites($yid,$_SESSION['uid']))
	{
	showmsg("删除成功！共删除 {$n} 行",2);
	}
	else
	{
	showmsg("失败！",0);
	}
}
//已邀请面试列表
elseif ($act=='interview_list')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$joinsql=" LEFT JOIN ".table('resume')." as r ON i.resume_id=r.id ";
	$wheresql=" WHERE i.company_uid='{$_SESSION['uid']}' ";
	//面试职位 筛选
	$jobsid=intval($_GET['jobsid']);
	if($jobsid>0)
	{
		$wheresql.=" AND i.jobs_id='{$jobsid}' ";
	}
	//对方查看状态 帅选
	$look=intval($_GET['look']);
	if($look>0)$wheresql.=" AND  i.personal_look='{$look}' ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('company_interview')." as i ".$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$resume = get_interview($offset, $perpage,$joinsql.$wheresql);
	$smarty->assign('act',$act);
	$smarty->assign('title','我发起的面试邀请 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('resume',$resume);
	$count1=count_interview($_SESSION['uid'],1,$jobsid);//未查看
	$count2=count_interview($_SESSION['uid'],2,$jobsid);//已查看
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
//删除面试邀请信息
elseif ($act=='interview_del')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("你没有选择简历！",1);
	if (del_interview($yid,$_SESSION['uid']))
	{
		showmsg("删除成功！",2);
	}
	else
	{
		showmsg("删除失败！",0);
	}
}
//浏览过的简历
elseif ($act=='my_attention')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$joinsql=" LEFT JOIN  ".table('resume')." AS r  ON  a.resumeid=r.id ";
	$wheresql=" WHERE a.uid='{$_SESSION['uid']}' ";
	//查看时间筛选
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
	$smarty->assign('title','浏览记录 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('resume_list',get_my_attention($offset,$perpage,$joinsql.$wheresql));
	if($total_val>$perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_company/company_my_attention.htm');
}
//删除 浏览过的简历
elseif($act == 'del_my_attention')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("你没有选择简历！",1);
	if ($n=del_my_attention($yid,$_SESSION['uid']))
	{
	showmsg("删除成功！共删除 {$n} 行",2);
	}
	else
	{
	showmsg("删除失败！",0);
	}
}
//收藏 简历
elseif($act == 'fav_att_resume')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("你没有选择简历！",1);
	$n = add_favorites($yid,$_SESSION['uid']);
	if(intval($n) > 0)
	{
	showmsg("收藏成功！共收藏 {$n} 行",2);
	}
	else
	{
	showmsg("收藏失败！",0);
	}
}
//查看 "谁看过我的职位" 信息
elseif ($act=='view_jobs_log')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	//筛选 职位
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
	//筛选 查看时间
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
	$smarty->assign('title','浏览记录 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('jobs',get_my_jobs($_SESSION['uid'],true));	
	$smarty->assign('user_list',get_view_users($offset,$perpage,$wheresql));
	if($total_val>$perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_company/company_view_jobs.htm');
}
//删除 "谁看过我的职位" 记录
elseif($act == 'del_view_jobs')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("你没有选择项目！",1);
	if(!del_view_jobs($yid))
	{
		showmsg("删除失败！",0);
	}
	else
	{
		showmsg("删除成功！",2);
	}
}
// 预约刷新
elseif($act == "refresh_appointment")
{
	/* 判断套餐 */
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
		exit("该模式下不能使用预约刷新！");
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
			exit("本次预约需要".$points."葫芦币,您的葫芦币为".$user_points.",葫芦币不足,请充值后再进行预约！");
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
				/* 操作葫芦币  */
			}
			report_deal($_SESSION['uid'],2,$points);
			exit("预约刷新成功！");
		}
	}
}

elseif ($act=='edit_resume')
{
	require_once(QISHI_ROOT_PATH.'include/fun_company_personal.php');

	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:showmsg("简历不存在！",1);


	$pid=intval($id);

	$sql = "select * from ".table('resume')."  WHERE id='{$pid}' LIMIT 1";
	$rs=$db->getone($sql);
	$uid=$rs["uid"];

	$smarty->assign('h_title',"查看简历");
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
	//地区二级
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
	$smarty->assign('title','我的简历 - 个人会员中心 - '.$_CFG['site_name']);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_resume',$captcha['verify_resume']);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->display('member_company/personal_resume_edit.htm');
}
unset($smarty);
?>