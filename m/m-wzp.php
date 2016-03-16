<?php
 /*
 * 74cms WAP
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
require_once(dirname(__FILE__).'/weixin_share.php');
$smarty->cache = false;
$act=$_GET['act']?trim($_GET['act']):"index";
$company_id=$_GET['company_id']?intval($_GET['company_id']):"";
$vip_menu=$_GET['vip_menu']?intval($_GET['vip_menu']):"";
if($act == "index")
{
	// ��ҵ��Ϣ
	if($company_id>0)
	{	
		//������ʼ�¼(1->����  2->����  3->����)
		$insetarr['company_id']=$company_id;
		$insetarr['uid']=$_SESSION['uid'];
		$insetarr['click_type']=1;
		$insetarr['addtime']=strtotime(date("Y-m-d"));
		$insetarr['ip']=getip();
		$db->inserttable(table('company_praise'),$insetarr);
		
		$company_info=$db->getone("SELECT * from ".table("company_profile")." where id=$company_id limit 1");
		$company_info['contents'] = htmlspecialchars_decode($company_info['contents'],ENT_QUOTES);
		//ͳ�Ƶ�����
		$praise = $db->get_total("SELECT COUNT(*) AS num FROM ".table('company_praise')." WHERE company_id={$company_id} AND click_type=2 ");
		$company_info['wzp_click']=$praise;
		if($company_info['logo'])
		{
			$company_info['logo']=$_CFG['site_domain'].$_CFG['site_dir'].'data/logo/'.$company_info['logo'];
		}
		else
		{
			$company_info['logo']=$_CFG['site_domain'].$_CFG['site_dir'].'data/logo/no_logo.gif';
		}
		$smarty->assign('company_info',$company_info);
		if(empty($company_info))
		{
			$smarty->display("m/m-wzp_error.html");
		}
		else
		{
			// ��ҵ�Լ����ʳ��ֲ˵�
			if($vip_menu=="1")
			{
				$smarty->assign('show_menue',1);
			}
			// ��ҵְλ
			$company_jobs=$db->getall("SELECT j.*,c.telephone from ".table("jobs")." as j left join ".table("jobs_contact")." as c on j.id=c.pid where j.uid=$company_info[uid] ");
			$smarty->assign('company_jobs',$company_jobs);
			// ��ҵ��ǩ
			$company_tag=explode(",", $company_info['tag']);
			foreach ($company_tag as $key => $value)
			{
				$val=explode("|", $value);
				$company_tagarr['id'][]=$val[0];
				$company_tagarr['tag_cn'][]=$val[1];
			}
			// Ĭ�ϱ�ǩ
			$_CAT=get_cache('category');
			if (!empty($_CAT['QS_jobtag']))
			{
				foreach ($_CAT["QS_jobtag"] as $cat)
				{
					$list[]=$cat['categoryname'];
				}
				$list=array_slice($list,0,6);
			}
			$company_tagarr['tag_cn'];
			// �ϲ���ҵ ѡ�б�ǩ �� Ĭ�ϱ�ǩ ��ʾ6�� ��ǩ
			$tag=array_slice(array_merge($company_tagarr['tag_cn'],$list),1,6);
			$smarty->assign('tag',$tag);
			$smarty->assign('title',$company_info['companyname']." - ��Ƹ��Ϣ ");
			
			// ���˵�¼��ʱ��
			if($_SESSION["uid"] && $_SESSION["utype"]==2)
			{
				$sql="select * from ".table("resume")." where uid=$_SESSION[uid] ";
				$resume_list = $db->getall($sql);
				$smarty->assign('resume_list',$resume_list);
			}


			if($company_info['wzp_tpl']>0)
			{
				$smarty->display("m/m-wzp_selected.html");
			}
			else
			{
				$smarty->display("m/m-wzp.html");
			}
		}
	}
	else
	{
		$smarty->display("m/m-wzp_error.html");
	}
}
// ��˾����
elseif($act == "company_welfare")
{
	// ��ҵ��Ϣ
	if($company_id>0)
	{	$company_info=$db->getone("SELECT * from ".table("company_profile")." where id=$company_id limit 1");
		$smarty->assign('company_info',$company_info);
		if(empty($company_info) || $company_info['uid']!=$_SESSION['uid'])
		{
			header("location:login.php");
		}
		else
		{
			// ��ҵ��ǩ
			$company_tag=explode(",", $company_info['tag']);
			foreach ($company_tag as $key => $value)
			{
				$val=explode("|", $value);
				$company_tagarr['id'][]=$val[0];
				$company_tagarr['tag_cn'][]=$val[1];
			}
			// var_dump($company_tag);die;
			$smarty->assign('company_tag',$company_tagarr['id']);
			$smarty->assign('title',$company_info['companyname']." - ��ҵ���� ");
			$smarty->display("m/m-wzp_welfare.html");
		}
	}
	else
	{
		$smarty->display("m/m-wzp_error.html");
	}
}
elseif($act == "company_welfare_add")
{
	$setarr['tag']=ltrim($_GET['tag'],",");
	if($company_id>0)
	{	
		$company_info=$db->getone("SELECT * from ".table("company_profile")." where id=$company_id limit 1");
		if($company_info["uid"]!=$_SESSION['uid'])
		{
			exit("err");
		}
		$db->updatetable(table('company_profile'),$setarr,array('id'=>$company_id,'uid'=>$_SESSION['uid']))?exit("ok"):exit("err");
	}
	else
	{
		exit('err');
	}
}
// �޸� ģ��
elseif($act == "company_tpl")
{
	// ��ҵ��Ϣ
	if($company_id>0)
	{	$company_info=$db->getone("SELECT * from ".table("company_profile")." where id=$company_id limit 1");
		$smarty->assign('company_info',$company_info);
		if(empty($company_info) || $company_info['uid']!=$_SESSION['uid'])
		{
			header("location:login.php");
		}
		else
		{
			$smarty->assign('title',$company_info['companyname']." - ģ�� ");
			$smarty->display("m/m-wzp_tpl.html");
		}
	}
	else
	{
		$smarty->display("m/m-wzp_error.html");
	}
}
elseif($act == "company_tpl_add")
{
	$setarr['wzp_tpl']=intval($_GET['tpl']);
	if($company_id>0)
	{	
		$company_info=$db->getone("SELECT * from ".table("company_profile")." where id=$company_id limit 1");
		if($company_info["uid"]!=$_SESSION['uid'])
		{
			exit("err");
		}
		$db->updatetable(table('company_profile'),$setarr,array('id'=>$company_id,'uid'=>$_SESSION['uid']))?exit("ok"):exit("err");
	}
	else
	{
		exit('err');
	}
}
// ����
elseif($act=="praise_click")
{

	if($company_id>0)
	{	
		//������ʼ�¼(1->����  2->����  3->����)
		$insetarr['company_id']=$company_id;
		$insetarr['uid']=$_SESSION['uid'];
		$insetarr['click_type']=2;
		$insetarr['addtime']=strtotime(date("Y-m-d"));
		$insetarr['ip']=getip();
		if($db->inserttable(table('company_praise'),$insetarr))
		{
			//ͳ�Ƶ�����
			$praise = $db->get_total("SELECT COUNT(*) AS num FROM ".table('company_praise')." WHERE company_id={$company_id} AND click_type=2 ");
			exit("".$praise."");
		}
		else
		{
			exit("-1");
		}
	}
	else
	{
		exit("-1");
	}
}
// ����
elseif($act=="share")
{
	if($company_id>0)
	{	
		//������ʼ�¼(1->����  2->����  3->����)
		$insetarr['company_id']=$company_id;
		$insetarr['uid']=$_SESSION['uid'];
		$insetarr['click_type']=3;
		$insetarr['addtime']=strtotime(date("Y-m-d"));
		$insetarr['ip']=getip();
		if($db->inserttable(table('company_praise'),$insetarr))
		{
			exit("1");
		}
		else
		{
			exit("-1");
		}
	}
	else
	{
		exit("-1");
	}
}
?>