<?php
/*
 * 74cms ��ҵ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/company_common.php');
$smarty->assign('leftmenu',"index");
if ($act=='report')
{
	$smarty->assign('title','�ٱ���Ϣ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('url',$_SERVER['HTTP_REFERER']);
	$smarty->display('member_company/company_report.htm');
}
//����ٱ���Ϣ
elseif ($act=='report_save')
{
	$link[0]['text'] = "������һҳ��";
	$link[0]['href'] = $_POST['url'];
	if (check_resume_report($_SESSION['uid'],$_POST['resume_id']))
	{
	showmsg("���Ѿ��ٱ����˼�����",1,$link);
	}
	$setsqlarr['content']=trim($_POST['content'])?trim($_POST['content']):showmsg('���������������',1);
	$setsqlarr['resume_id']=$_POST['resume_id']?intval($_POST['resume_id']):showmsg('û�м���ID',1);
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):showmsg('û�м���',1);
	$setsqlarr['resume_addtime']=intval($_POST['resume_addtime']);
	$setsqlarr['uid']=$_SESSION['uid'];
	$setsqlarr['addtime']=time();
	write_memberslog($_SESSION['uid'],2,7003,$_SESSION['username'],"�ٱ�����({$_POST['resume_id']})");
	!$db->inserttable(table('report_resume'),$setsqlarr)?showmsg("�ٱ�ʧ�ܣ�",0,$link):showmsg("�ٱ��ɹ�������Ա�����洦��",2,$link);
}
unset($smarty);
?>