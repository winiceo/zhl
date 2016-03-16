<?php
 /*
 * ����������ղؼ�ģ��
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
if ($_SESSION['uid']=='' || $_SESSION['username']==''||intval($_SESSION['utype'])==2)
{
	header("Location: ../login.php");
}
elseif ($act == 'index')
{
	$smarty->cache = false;
	$joinsql=" LEFT JOIN  ".table('resume')." AS r ON  f.resume_id=r.id ";
	$wheresql= " WHERE f.company_uid='{$_SESSION['uid']}' ";
	//�ղ�ʱ��
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND f.favoritesa_ddtime>".$settr_val;
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
		$joinsql.="  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_jobs')." {$joinwheresql} )AS j ON  f.resume_id=j.pid ";
	}
	$perpage = 100;
	$count  = 0;
	$page = empty($_GET['page'])?1:intval($_GET['page']);
	if($page<1) $page = 1;
	$start = ($page-1)*$perpage;
	$total_sql="SELECT COUNT(*) AS num FROM  ".table('company_favorites')." as f ".$joinsql." {$wheresql}";
	$count=$db->get_total($total_sql);
	$smarty->assign('favorites',wap_get_favorites($start, $perpage,$joinsql.$wheresql));
	$smarty->display("m/company/m-collect-resumes.html");	
}
elseif($act=="ajax_collect_resume")
{
	$favoriteshtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']);
	$wheresql=" WHERE a.company_uid='{$_SESSION['uid']}' ";
	$row=$db->getall("select a.*,r.title,r.fullname,r.sex,r.display_name,r.education_cn,r.birthdate,r.experience_cn,r.residence from ".table("company_favorites")." as a left join ".table("resume")." as r on a.resume_id=r.id  $wheresql order by a.favoritesa_ddtime desc limit $offset,$rows");
	if (!empty($row) && $offset<=100)
	{
		foreach($row as $list)
		{
			$list["birthdate_"]=date('Y',time())-$list["birthdate"];
			if ($list['display_name']=="2")
			{
			$list['fullname']="N".str_pad($list['resume_id'],7,"0",STR_PAD_LEFT);
			}
			elseif ($list['display_name']=="3")
			{
				if($list['sex']==1)
				{
					$list['fullname']=cut_str($list['fullname'],1,0,"����");
				}
				elseif($list['sex']==2)
				{
					$list['fullname']=cut_str($list['fullname'],1,0,"Ůʿ");
				}
			}
			$favoriteshtml.='<div class="get_resume_box" onclick="window.location.href="../resume-show.php?id='.$list["resume_id"].'
""><div class="get_resume_left"><div class="name_box"><div class="name_box_l">'.$list["fullname"].'</div><div class="name_box_r">'.date("Y-m-d",$list["apply_addtime"]).'</div><div class="clear"></div></div><div class="person_detail">'.$list["education_cn"].'|'.$list["birthdate_"].'|'.$list["experience_cn"].'|'.$list["residence"].'</div></div><div class="get_resume_right"><img src="../images/34.gif" alt="" /></div><div class="clear"></div></div>';
		}
		exit($favoriteshtml);
	}
	else
	{
		exit('-1');
	}
}
elseif($act=="ajax_collect_resume_add")
{
	$resume_id=intval($_POST["resume_id"]);
	$uid=intval($_SESSION["uid"]);
	$sql="select * from ".table("company_favorites")." where resume_id=$resume_id and company_uid=$uid ";
	$collect_resume=$db->getone($sql);
	if($_SESSION["utype"]!=1){
		exit("��ҵ��Ա��¼���ղؼ���");
	}
	else if($collect_resume)
	{
		exit("���Ѿ��ղع��˼���");
	}
	else
	{
		$setsqlarr["resume_id"]=$resume_id;
		$setsqlarr["company_uid"]=$uid;
		$setsqlarr["favoritesa_ddtime"]=time();
		$db->inserttable(table('company_favorites'),$setsqlarr)?exit("ok"):exit("err");
	}
}
?>