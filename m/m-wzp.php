<?php
 /*
 * WAP
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

if((empty($_SESSION['uid']) || empty($_SESSION['username']) || empty($_SESSION['utype'])) &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'] && $_COOKIE['QS']['uid'])
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	if(check_cookie($_COOKIE['QS']['uid'],$_COOKIE['QS']['username'],$_COOKIE['QS']['password']))
	{
	update_user_info($_COOKIE['QS']['uid'],false,false);
	header("Location:".get_member_url($_SESSION['utype']));
	}
	else
	{
	unset($_SESSION['uid'],$_SESSION['username'],$_SESSION['utype'],$_SESSION['uqqid'],$_SESSION['activate_username'],$_SESSION['activate_email'],$_SESSION["openid"]);
	setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[username]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[password]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	}	
}

	
	// 企业信息
	if($company_id>0)
	{	
		//插入访问记录(1->访问  2->点赞  3->分享)
		$insetarr['company_id']=$company_id;
		$insetarr['uid']=$_SESSION['uid'];
		$insetarr['click_type']=1;
		$insetarr['addtime']=strtotime(date("Y-m-d"));
		$insetarr['ip']=getip();
		$db->inserttable(table('company_praise'),$insetarr);
		
		$company_info=$db->getone("SELECT * from ".table("company_profile")." where id=$company_id limit 1");
		// 0 游客     1 已登录会员     2 已登录且发布了有效简历
		
		
		if($company_info['telephone_show'] ==1 ){
			$phone=get_cache('config');
			if($phone['showjobcontact'] == 0){
				$phone_error='';
				$phone_error_tet='';
				$phone_url= $phone['site_domain'].'/m/login.php';
				$phone_code = 1;
			}elseif ($phone['showjobcontact'] == 1) {
				
				if ($_SESSION['uid']=='' || $_SESSION['username']=='' || intval($_SESSION['uid'])===0)
				{
					$phone_error='对不起，请登录后继续查看企业联系方式';
					$phone_error_tet='登录';
					$phone_url= $phone['site_domain'].'/m/login.php';
					$phone_code = 0;
				}else{
					$phone_code = 1;
				}
				
			}elseif ($phone['showjobcontact'] == 2) {
				if ($_SESSION['uid']=='' || $_SESSION['username']=='' || intval($_SESSION['uid'])===0)
				{
					$phone_error='对不起，请登录后继续查看企业联系方式';
					$phone_error_tet='登录';
					$phone_url= $phone['site_domain'].'/m/login.php';
					$phone_code = 0;
				}else{
						if($_SESSION['uid']){
						$resume=$db->getone("SELECT uid from ".table("resume")." where uid={$_SESSION['uid']} limit 1");
						}
						if(!$resume){
							$phone_error='对不起，您还没有有效简历，请先创建一份求职简历';
							$phone_error_tet='创建';
							$phone_url= $phone['site_domain'].'/m/personal/user.php?act=make_resume';
							$phone_code = 0;
						}
						else
						{
							$phone_code = 1;
						}
				}
			}
		}else{
				$phone_error='该企业不接受电话咨询，请直接投递简历';
				$phone_error_tet='投递';
				$phone_url= $phone['site_domain'].'/m/company-show.php?id='.$company_info['id'];
				$phone_code = 0;
		}
		
		$smarty->assign('phone_code',$phone_code);
		$smarty->assign('phone_url',$phone_url);
		$smarty->assign('phone_error',$phone_error);
		$smarty->assign('phone_error_tet',$phone_error_tet);


		$company_info['contents'] = htmlspecialchars_decode($company_info['contents'],ENT_QUOTES);
		//统计点赞数
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
			// 企业自己访问出现菜单
			if($vip_menu=="1")
			{
				$smarty->assign('show_menue',1);
			}
			// 企业职位
			$company_jobs=$db->getall("SELECT j.*,c.telephone from ".table("jobs")." as j left join ".table("jobs_contact")." as c on j.id=c.pid where j.uid=$company_info[uid] ");
			$smarty->assign('company_jobs',$company_jobs);
			// 企业标签
			$company_tag=explode(",", $company_info['tag']);
			foreach ($company_tag as $key => $value)
			{
				$val=explode("|", $value);
				$company_tagarr['id'][]=$val[0];
				$company_tagarr['tag_cn'][]=$val[1];
			}
			// 默认标签
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
			// 合并企业 选中标签 和 默认标签 显示6个 标签
			$tag=array_slice(array_merge($company_tagarr['tag_cn'],$list),1,6);
			$smarty->assign('tag',$tag);
			$smarty->assign('title',$company_info['companyname']." - 招聘信息 ");
			
			// 个人登录的时候
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
// 公司福利
elseif($act == "company_welfare")
{
	// 企业信息
	if($company_id>0)
	{	$company_info=$db->getone("SELECT * from ".table("company_profile")." where id=$company_id limit 1");
		$smarty->assign('company_info',$company_info);
		if(empty($company_info) || $company_info['uid']!=$_SESSION['uid'])
		{
			header("location:login.php");
		}
		else
		{
			// 企业标签
			$company_tag=explode(",", $company_info['tag']);
			foreach ($company_tag as $key => $value)
			{
				$val=explode("|", $value);
				$company_tagarr['id'][]=$val[0];
				$company_tagarr['tag_cn'][]=$val[1];
			}
			// var_dump($company_tag);die;
			$smarty->assign('company_tag',$company_tagarr['id']);
			$smarty->assign('title',$company_info['companyname']." - 企业福利 ");
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
// 修改 模版
elseif($act == "company_tpl")
{
	// 企业信息
	if($company_id>0)
	{	$company_info=$db->getone("SELECT * from ".table("company_profile")." where id=$company_id limit 1");
		$smarty->assign('company_info',$company_info);
		if(empty($company_info) || $company_info['uid']!=$_SESSION['uid'])
		{
			header("location:login.php");
		}
		else
		{
			$smarty->assign('title',$company_info['companyname']." - 模版 ");
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
// 点赞
elseif($act=="praise_click")
{

	if($company_id>0)
	{	
		//插入访问记录(1->访问  2->点赞  3->分享)
		$insetarr['company_id']=$company_id;
		$insetarr['uid']=$_SESSION['uid'];
		$insetarr['click_type']=2;
		$insetarr['addtime']=strtotime(date("Y-m-d"));
		$insetarr['ip']=getip();
		if($db->inserttable(table('company_praise'),$insetarr))
		{
			//统计点赞数
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
// 分享
elseif($act=="share")
{
	if($company_id>0)
	{	
		//插入访问记录(1->访问  2->点赞  3->分享)
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