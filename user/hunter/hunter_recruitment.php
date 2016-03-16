<?php
/*
 * 74cms ��ͷ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/hunter_common.php');
$smarty->assign('leftmenu',"recruitment");
if ($act=='down_resume_list')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$joinsql=" LEFT JOIN  ".table('resume')." as r ON d.resume_id=r.id ";
	$wheresql=" WHERE  d.hunter_uid='{$_SESSION['uid']}' ";
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-{$settr} day");
	$wheresql.=" AND d.down_addtime>{$settr_val} ";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('hunter_down_resume')." as d".$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title','�����صļ��� - ��ͷ���ʻ�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('list',get_down_manager_resume($offset,$perpage,$joinsql.$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->display('member_hunter/hunter_down_resume.htm');
}
elseif ($act=='del_down')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ�����ؼ�¼��",1);
	if ($n=del_down_manager($yid,$_SESSION['uid']))
	{
	showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	showmsg("ʧ�ܣ�",0);
	}
}
//�յ��ļ���
elseif ($act=='apply_jobs')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$joinsql=" LEFT JOIN  ".table('resume')." AS r  ON  a.resume_id=r.id ";
	$wheresql=" WHERE a.huntet_uid='{$_SESSION['uid']}' ";
	$look=intval($_GET['look']);
	if($look>0)$wheresql.=" AND a.personal_look='{$look}'";
	$jobsid=intval($_GET['jobsid']);
	if($jobsid>0){
		$wheresql.=" AND a.jobs_id='{$jobsid}' ";
		$sql="select jobs_name from ".table("hunter_jobs")." where id=".intval($_GET['jobsid'])." ";
		$row=$db->getone($sql);
		$smarty->assign('jobs_name',$row["jobs_name"]);
	}
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_hunter_jobs_apply')." AS a  {$wheresql} ";
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('act',$act);
	$smarty->assign('title','�յ���ְλ���� - ��ͷ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('jobs_apply',get_apply_hunter_jobs($offset,$perpage,$joinsql.$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('count',count_jobs_apply($_SESSION['uid'],0,$jobsid));
	$smarty->assign('count1',count_jobs_apply($_SESSION['uid'],1,$jobsid));
	$smarty->assign('count2',count_jobs_apply($_SESSION['uid'],2,$jobsid));
	$smarty->assign('jobs',get_auditjobs($_SESSION['uid']));	
	$smarty->display('member_hunter/hunter_apply_jobs.htm');
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
//��������
elseif ($act=='interview')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$joinsql=" LEFT JOIN ".table('resume')." as r ON i.resume_id=r.id ";
	$wheresql=" WHERE i.hunter_uid='{$_SESSION['uid']}' ";
	$jobsid=intval($_GET['jobsid']);
	if($jobsid>0){
		$wheresql.=" AND i.jobs_id='{$jobsid}' ";
		$sql="select jobs_name from ".table("hunter_jobs")." where id=".intval($_GET['jobsid'])." ";
		$row=$db->getone($sql);
		$smarty->assign('jobs_name',$row["jobs_name"]);
	}
	$look=intval($_GET['look']);
	if($look>0)$wheresql.=" AND  i.personal_look='{$look}' ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('hunter_interview')." as i ".$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('act',$act);
	$smarty->assign('title','�ҷ������������ - ��ͷ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('resume',get_interview($offset, $perpage,$joinsql.$wheresql));
	$count1=count_interview($_SESSION['uid'],1,$jobsid);//δ�鿴
	$count2=count_interview($_SESSION['uid'],2,$jobsid);//�Բ鿴
	$count=$count1+$count2;
	$smarty->assign('count',$count);
	$smarty->assign('count1',$count1);
	$smarty->assign('count2',$count2);
	$smarty->assign('jobs',get_auditjobs($_SESSION['uid']));
	$smarty->assign('page',$page->show(3));
	$smarty->display('member_hunter/hunter_interview.htm');
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
unset($smarty);
?>