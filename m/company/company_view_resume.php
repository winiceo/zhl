<?php
 /*
 * ������������ļ����б�
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
if ($_SESSION['uid']=='' || $_SESSION['username']=='' || intval($_SESSION['utype'])==2)
{
	header("Location: ../login.php");
}
elseif ($act == 'index')
{
	$smarty->cache = false;
	$joinsql=" LEFT JOIN  ".table('resume')." AS r  ON  a.resumeid=r.id ";
	$wheresql=" WHERE a.uid='{$_SESSION['uid']}' ";
	//�鿴ʱ��ɸѡ
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND a.addtime>".$settr_val;
	}
	//ѧ��
	$education=intval($_GET['education']);
	if($education>0)
	{
	$wheresql.=" AND r.education=".$education;
	}
	//����
	$experience=intval($_GET['experience']);
	if($experience>0)
	{
	$wheresql.=" AND r.experience=".$experience;
	}
	//ְ��
	$category=intval($_GET['category']);
	$subclass=intval($_GET['subclass']);
	if($category>0)
	{
		$joinwheresql=" WHERE  category=".intval($category);
		if($subclass>0)
		{
			$joinwheresql.=" AND  subclass=".intval($subclass);
		}
		$joinsql.="  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_jobs')." {$joinwheresql} )AS j ON  a.resumeid=j.pid ";
	}
	$perpage = 100;
	$count  = 0;
	$page = empty($_GET['page'])?1:intval($_GET['page']);
	if($page<1) $page = 1;
	$start = ($page-1)*$perpage;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('view_resume')." AS a ".$joinsql." {$wheresql} ";
	$total_val=$db->get_total($total_sql);
	$view_resumes = wap_get_my_attention($start,$perpage,$joinsql.$wheresql);
	$smarty->assign('view_resumes',$view_resumes);
	$smarty->display("m/company/m-my-attention.html");	
}
?>