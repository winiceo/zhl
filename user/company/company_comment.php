<?php
/*
 * 74cms ְλ����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
if ($act=='comment_list')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$id=intval($_GET['jobsid']);
	$wheresql=" WHERE c.jobs_id='{$id}'";
	$joinsql=" LEFT JOIN   ".table('members')." AS m ON c.uid=m.uid LEFT JOIN  ".table('jobs')." AS j ON c.jobs_id=j.id ";
	$perpage=15;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('comment')." AS c ".$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('title','ְλ���� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('list',get_comment_list($offset, $perpage,$joinsql.$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->display('member_company/company_comment.htm');
}
elseif ($act=='comment_del')
{
	$id =!empty($_POST['id'])?$_POST['id']:$_GET['id'];
	$jobs_id=intval($_POST['jobs_id']);
	if (empty($id))
	{
	showmsg("��û��ѡ����Ŀ��",1);
	}
	if($n=del_company_comment($id,$jobs_id,$company_profile['id']))
	{
	showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	showmsg("ɾ��ʧ�ܣ�",0);
	}
}

elseif($act == 'comment_audit')
{
	if ($_CFG['open_commentaudit']!="1" ||  $_CFG['com_commentaudit']!="1") exit('error'); 
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:showmsg("��û��ѡ��ְλ���ۣ�",1);
	$audit=intval($_REQUEST['audit']);
	$num=comment_audit($id,$audit);
	if ($num>0){showmsg("��˳ɹ��������".$num."��",2);}else{showmsg("��˳ɹ�!��Ӱ��{$num}��",0);}
}

unset($smarty);
?>