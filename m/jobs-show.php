<?php
 /*
 * 74cms WAP
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
require_once(dirname(__FILE__).'/weixin_share.php');
wap_weixin_logon($_GET['from']);
$row=jobs_one($_GET['id']);
check_m_url($row['subsite_id'],$smarty,$_CFG['m_job_url']);
//����Ȥ��ְλ
$interest_show=interest_jobs($row['topclass'],$row['category'],$row['subclass'],$_GET['id']);
if($_SESSION["uid"] && $_SESSION["utype"]==2){
	$sql="select * from ".table("resume")." where uid=$_SESSION[uid] ";
	$resume_list = $db->getall($sql);
	$smarty->assign('resume_list',$resume_list);
}
$show=false;
if($_CFG['showjobcontact_wap']=='0')
{
	$show=true;
}
elseif($_CFG['showjobcontact_wap']=='1')
{
	if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
	{
		$show=true;
	}
	else
	{
		$show=false;
	}
}
elseif($_CFG['showjobcontact_wap']=='2')
{
	if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
	{
		$val=$db->getone("select uid from ".table('resume')." where uid='{$_SESSION['uid']}' LIMIT 1");
	 	if (!empty($val))
		{
			$show=true;
		}
		else
		{
			$show=false;
		}
	}
	else
	{
		$show=false;
	}
}
if($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1' && $show==false)
{
	if($row['uid']==$_SESSION['uid'])
	{
		$show=true;
	}
	else
	{
		$show=false;
	}
}
$smarty->assign('is_show_tel',$show);
$smarty->assign('show',$row);
$smarty->assign('interest_show',$interest_show);
$smarty->assign('goback',$_SERVER["HTTP_REFERER"]);
$smarty->display("m/m-jobs-show.html");
?>