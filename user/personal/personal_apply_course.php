<?php
/*
 * 74cms ���˻�Ա����(�γ�����)
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');
$smarty->assign('leftmenu',"apply");
if ($act=='apply_course')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE personal_uid='{$_SESSION['uid']}' ";
	$aetlook=intval($_GET['aetlook']);
	if($aetlook>0)
	{
	$wheresql.=" AND personal_look='{$aetlook}'";
	}	
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_course_apply')." AS a {$wheresql} ";
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title','������Ŀγ� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('course_apply',get_apply_course($offset,$perpage,$wheresql));
	$smarty->assign('act',$act);
	$applycour_num=get_now_applycour_num($_SESSION['uid']);
	$smarty->assign('count_apply',array(30,$applycour_num,30-$applycour_num));
	$smarty->assign('page',$page->show(3));
	$count[0]=count_personal_cour_apply($_SESSION['uid'],1);
	$count[1]=count_personal_cour_apply($_SESSION['uid'],2);
	$count[2]=$count[0]+$count[1];
	$smarty->assign('count',$count);
	$smarty->display('member_personal/personal_apply_course.htm');
}
elseif ($act=='del_course_apply')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ����Ŀ��",1);
	$delete =trim($_POST['delete']);
	$delete?(!del_apply($yid,$_SESSION['uid'])?showmsg("ɾ��ʧ�ܣ�",0):showmsg("ɾ���ɹ���",2)):'';
}
unset($smarty);
?>