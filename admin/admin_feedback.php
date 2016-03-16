<?php
 /*
 * 74cms Ͷ���뽨��
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_feedback_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'suggest_list';
if($act == 'suggest_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"suggest_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	!empty($_GET['infotype'])? $wheresqlarr['infotype']=intval($_GET['infotype']):'';
		if (is_array($wheresqlarr))
		{
		$where_set=' WHERE';
			foreach ($wheresqlarr as $key => $value)
			{
			$wheresql .=$where_set. $comma.'`'.$key.'`'.'=\''.$value.'\'';
			$comma = ' AND ';
			$where_set='';
			}
		}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('feedback').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_feedback_list($offset,$perpage,$wheresql);
	$smarty->assign('pageheader',"����ͽ���");
	$smarty->assign('infotype',$_GET['infotype']);
	$smarty->assign('perpage',$perpage);
	$smarty->assign('list',$list);//�б�
	if ($total_val>$perpage)
	{
	$smarty->assign('page',$page->show(3));//��ҳ��
	}
	$smarty->display('feedback/admin_feedback_suggest_list.htm');
}
elseif($act == 'del_feedback')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"suggest_del");
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ����Ŀ��",1);
	if ($num=del_feedback($id))
	{
	write_log("ɾ���������,��ɾ��".$num."��", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'report_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"report_show");
	$type=intval($_GET['type'])==0?1:intval($_GET['type']);
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY r.id DESC ";
	if (!empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE r.addtime> ".$settr:$wheresql." AND r.addtime> ".$settr;
	}
	$joinsql=" LEFT JOIN ".table('members')." AS m ON r.uid=m.uid  ";
	if($type==1){
		$total_sql="SELECT COUNT(*) AS num FROM ".table('report')." AS r ".$joinsql.$wheresql;
	}else{
		$total_sql="SELECT COUNT(*) AS num FROM ".table('report_resume')." AS r ".$joinsql.$wheresql;
	}
	if (!empty($_GET['reporttype']))
	{
		$wheresql=empty($wheresql)?" WHERE r.report_type=".$_GET['reporttype']:$wheresql." AND r.report_type=".$_GET['reporttype'];
	}
	if (!empty($_GET['audit']))
	{
		$wheresql=empty($wheresql)?" WHERE r.audit=".$_GET['audit']:$wheresql." AND r.audit=".$_GET['audit'];
	}
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_report_list($offset,$perpage,$joinsql.$wheresql.$oederbysql,$type);
	$smarty->assign('pageheader',"�ٱ���Ϣ");
	$smarty->assign('list',$list);
	$smarty->assign('page',$page->show(3));
	if($type==1){
		$smarty->display('feedback/admin_report_list.htm');
	}else{
		$smarty->display('feedback/admin_report_resume_list.htm');
	}
}
elseif($act == 'report_perform')
{
	$type=intval($_POST['type'])==0?1:intval($_POST['type']);
	//���
	if(!empty($_POST['set_audit'])){
		check_permissions($_SESSION['admin_purview'],"report_audit");
		check_token();
		$id=$_REQUEST['id'];
		if ($type==1) {
			$rid=$_REQUEST['jobs_id'];
		} else {
			$rid=$_REQUEST['resume_id'];
		}
		$audit=intval($_POST['audit']);
		if (empty($id))
		{
		adminmsg("��û��ѡ����Ŀ��",1);
		}
		if ($num=report_audit($id,$audit,$type,$rid))
		{
		write_log("���þٱ���Ϣ���״̬����Ӱ��{$num}�� ", $_SESSION['admin_name'],3);
		adminmsg("���óɹ�����Ӱ�� {$num}�� ",2);
		}
		else
		{
		adminmsg("����ʧ�ܣ�",0);
		}
	}
}
elseif($act == 'del_report')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"report_del");
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ����Ŀ��",1);
	$id=$_REQUEST['id'];
	if ($num=del_report($id))
	{
	write_log("ɾ���ٱ���Ϣ����ɾ��{$num}�� ", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'del_report_resume')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"report_del");
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ����Ŀ��",1);
	$id=$_REQUEST['id'];
	if ($num=del_report_resume($id))
	{
	write_log("ɾ���ٱ�������Ϣ����ɾ��{$num}�� ", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
?>