<?php
 /*
 * WAP
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
$user = get_user_info($_SESSION['uid']);
if($_CFG['login_com_audit_mobile'] && $user['mobile_audit']=="0" && $act != 'index')
{
	$str= "<script>";
	$str.="alert('������֤�ֻ���');";
	$str.="window.location.href='account_security.php';";
	$str.= "</script>";
	echo $str;
}
elseif ($act == 'index')
{
	$smarty->cache = false;
	$company_info=get_company(intval($_SESSION['uid']));
	if(empty($company_info)){
		header("Location: ?act=company_info");
	}else{
		//����
		$user_points  = get_user_points(intval($_SESSION['uid']));
		$smarty->assign('user_points',$user_points);
		//�ײ�
		$user_setmeal  = get_user_setmeal(intval($_SESSION['uid']));
		$smarty->assign('user_setmeal',$user_setmeal);
		$smarty->assign('company_info',$company_info);
		//������Ϣ
		$member_info  = get_user_info(intval($_SESSION['uid']));
		if(intval($member_info['consultant']) > 0)
		{
			$consultant = $db->getone("SELECT * FROM ".table('consultant')." WHERE id=".intval($member_info['consultant']));
			$smarty->assign('consultant',$consultant['qq']);
		}
		$smarty->display("m/company/m-user-company-index.html");
	}
}
// ��ҵ��Ϣ
elseif($act=="company_info")
{
	$smarty->cache = false;
	$company_info=get_company(intval($_SESSION['uid']));
	if($company_info){
		$company_info['contents'] = strip_tags(htmlspecialchars_decode($company_info['contents'],ENT_QUOTES));
		//���������зָ�
		$telarray = explode('-',$company_info['landline_tel']);
		if(intval($telarray[0]) > 0)
		{
			$company_info['landline_tel_first'] = $telarray[0];
		}
		if(intval($telarray[1]) > 0)
		{
			$company_info['landline_tel_next'] = $telarray[1];
		}
		if(intval($telarray[2]) > 0)
		{
			$company_info['landline_tel_last'] = $telarray[2];
		}
	}
	$smarty->assign('company_info',$company_info);
	$smarty->display("m/company/m-com-info.html");
	
}
elseif($act=="company_info_save")
{
	$smarty->cache = false;
	$company_info=get_company(intval($_SESSION['uid']));
	$_POST=array_map("utf8_to_gbk", $_POST);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['companyname']=trim($_POST['companyname'])?trim($_POST['companyname']):exit('��û��������ҵ���ƣ�');
	$setsqlarr['nature']=trim($_POST['nature'])?intval($_POST['nature']):exit('��ѡ����ҵ���ʣ�');
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['trade']=trim($_POST['trade'])?intval($_POST['trade']):exit('��ѡ��������ҵ��');
	$setsqlarr['trade_cn']=trim($_POST['trade_cn']);
	$setsqlarr['district']=intval($_POST['district'])>0?intval($_POST['district']):exit('��ѡ������������');
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['scale']=trim($_POST['scale'])?trim($_POST['scale']):exit('��ѡ��˾��ģ��');
	$setsqlarr['scale_cn']=trim($_POST['scale_cn']);
	$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):exit('����дͨѶ��ַ��');
	$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):exit('����д��ϵ�ˣ�');
	$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):'';
	if (!preg_match("/^(13|15|14|18|17)\d{9}$/",$setsqlarr['telephone']))
	{
		exit("�绰��ʽ����");//�ֻ��Ŵ���
	}
	$setsqlarr['email']=trim($_POST['email'])?trim($_POST['email']):exit('����д��ϵ���䣡');
	$setsqlarr['contents']=trim($_POST['contents'])?trim($_POST['contents']):exit('����д��˾��飡');
	
	//����
	$landline_tel[]=trim($_POST['landline_tel_first'])?trim($_POST['landline_tel_first']):"0";
	$landline_tel[]=trim($_POST['landline_tel_next'])?trim($_POST['landline_tel_next']):"0";
	$landline_tel[]=trim($_POST['landline_tel_last'])?trim($_POST['landline_tel_last']):"0";
	$setsqlarr['landline_tel']=implode('-', $landline_tel);
	//�������ֻ����ٶ�ѡһ
	if(empty($setsqlarr['telephone']) && $setsqlarr['landline_tel']=='0-0-0')
	{
		exit('����д�ֻ���̻�����ѡһ���ɣ�');
	}
	
	$setsqlarr['contact_show']=1;
	$setsqlarr['email_show']=1;
	$setsqlarr['telephone_show']=1;
	$setsqlarr['address_show']=1;
		
	if ($_CFG['company_repeat']=="0")
	{
		$info=$db->getone("SELECT uid FROM ".table('company_profile')." WHERE companyname ='{$setsqlarr['companyname']}' AND uid<>'{$_SESSION['uid']}' LIMIT 1");
		if(!empty($info))
		{
			exit("{$setsqlarr['companyname']}�Ѿ����ڣ�ͬ��˾��Ϣ�����ظ�ע��");
		}
	}
	if ($company_info)
	{
		$_CFG['audit_edit_com']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_edit_com']):'';
		if ($db->updatetable(table('company_profile'), $setsqlarr," uid=$_SESSION[uid]"))
		{
			$jobarr['companyname']=$setsqlarr['companyname'];
			$jobarr['trade']=$setsqlarr['trade'];
			$jobarr['trade_cn']=$setsqlarr['trade_cn'];
			$jobarr['scale']=$setsqlarr['scale'];
			$jobarr['scale_cn']=$setsqlarr['scale_cn'];
			$jobarr['street']=$setsqlarr['street'];
			$jobarr['street_cn']=$setsqlarr['street_cn'];			
			if (!$db->updatetable(table('jobs'),$jobarr," uid=".$setsqlarr['uid']."")) exit('�޸Ĺ�˾���Ƴ���');
			if (!$db->updatetable(table('jobs_tmp'),$jobarr," uid=".$setsqlarr['uid']."")) exit('�޸Ĺ�˾���Ƴ���');
			if (!$db->updatetable(table('jobfair_exhibitors'),array('companyname'=>$setsqlarr['companyname'])," uid=".$setsqlarr['uid']."")) exit('�޸Ĺ�˾���Ƴ���');
			$soarray['trade']=$jobarr['trade'];
			$soarray['scale']=$jobarr['scale'];
			$soarray['street']=$setsqlarr['street'];
			$db->updatetable(table('jobs_search_scale'),$soarray," uid=".$setsqlarr['uid']."");
			$db->updatetable(table('jobs_search_wage'),$soarray," uid=".$setsqlarr['uid']."");
			$db->updatetable(table('jobs_search_rtime'),$soarray," uid=".$setsqlarr['uid']."");
			$db->updatetable(table('jobs_search_stickrtime'),$soarray," uid=".$setsqlarr['uid']."");
			$db->updatetable(table('jobs_search_hot'),$soarray," uid=".$setsqlarr['uid']."");
			$db->updatetable(table('jobs_search_key'),$soarray," uid=".$setsqlarr['uid']."");
			unset($setsqlarr);
			write_memberslog($_SESSION['uid'],$_SESSION['utype'],8001,$_SESSION['username'],"�޸���ҵ����");
			exit("1");
		}
		else
		{
			exit("����ʧ�ܣ�");
		}
	}
	else
	{
		$setsqlarr['audit']=intval($_CFG['audit_add_com']);
		$setsqlarr['addtime']=$timestamp;
		$setsqlarr['refreshtime']=$timestamp;
		$insertid = $db->inserttable(table('company_profile'),$setsqlarr,1);
		if ($insertid)
		{
			baidu_submiturl(url_rewrite('QS_companyshow',array('id'=>$insertid)),'addcompany');
			write_memberslog($_SESSION['uid'],$_SESSION['utype'],8001,$_SESSION['username'],"������ҵ����");
			exit("1");
		}
		else
		{
			exit("����ʧ�ܣ�");
		}
	}
}
// ������ҵlogo
elseif ($act =='logo_save')
{
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	!$_FILES['logo_img']['name']?exit('���ϴ�ͼƬ��'):"";
	$logo_dir="../../data/logo/".date("Y/m/d/");
	make_dir($logo_dir);
	$setsqlarr['logo']=_asUpFiles($logo_dir, "logo_img",$_CFG['logo_max_size'],'gif/jpg/bmp/png',true);
	if ($setsqlarr['logo'])
	{
		$setsqlarr['logo']=date("Y/m/d/").$setsqlarr['logo'];
		$wheresql="uid='".$_SESSION['uid']."'";
		if(!$db->updatetable(table('company_profile'),$setsqlarr,$wheresql))
		{
			exit("-6");
		}
		else
		{
			$data['isok'] = 1;
			$json_encode = json_encode($data);
			exit($json_encode);
		}
	}
	else
	{
	exit("-6");
	}
}
elseif ($act == 'setmeal_product')
{
	$smarty->cache = false;
	//������Ϣ
	$member_info  = get_user_info(intval($_SESSION['uid']));
	if(intval($member_info['consultant']) > 0)
	{
		$consultant = $db->getone("SELECT * FROM ".table('consultant')." WHERE id=".intval($member_info['consultant']));
		$smarty->assign('consultant',$consultant);
	}
	$smarty->display("m/company/m-setmealproduct.html");
}
//�ײ�����
elseif ($act == 'setmeal_surplus')
{
	$smarty->cache = false;
	//�˻�״̬
	$user_setmeal  = get_user_setmeal(intval($_SESSION['uid']));
	$smarty->assign('user_setmeal',$user_setmeal);
	//ʣ�෢��ְλ��    ��������Ƹְλ = �����е� + ����˵�
	$jobs="SELECT COUNT(*) AS num FROM ".table('jobs')." where uid='{$_SESSION['uid']}'";
	$jobs_num=$db->get_total($jobs);
	$jobs_tmp="SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." where uid='{$_SESSION['uid']}' and  audit=2 ";
	$jobs_tmp_num=$db->get_total($jobs_tmp);
	$smarty->assign('jobs_num',intval($user_setmeal['jobs_ordinary']) - (intval($jobs_num)+intval($jobs_tmp_num)));
	//�˲ſ�����
	$favorites="SELECT COUNT(*) AS num FROM ".table('company_favorites')." where company_uid='{$_SESSION['uid']}'";
	$favorites_num=$db->get_total($favorites);
	$favorites_num=intval($user_setmeal['talent_pool']) - intval($favorites_num);
	$smarty->assign('favorites_num',intval($favorites_num));
	$smarty->display("m/company/m-setmeal-margin.html");
}
/* �˻�״̬*/
elseif($act == "company_auth")
{
	$company_profile=get_company($_SESSION['uid']);
	$smarty->assign('company_profile',$company_profile);
	$smarty->display("m/company/m-enterprise-authenticate.html");
}
elseif($act == "company_auth_save")
{
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	$setsqlarr['license']=trim($_POST['license']);
	$setsqlarr['audit']=2;
	
	!$_FILES['certificate_img']['name']?exit('���ϴ�ͼƬ��'):"";
	$certificate_dir="../../data/".$_CFG['updir_certificate']."/".date("Y/m/d/");
	make_dir($certificate_dir);
	$setsqlarr['certificate_img']=_asUpFiles($certificate_dir, "certificate_img",$_CFG['certificate_max_size'],'gif/jpg/bmp/png',true);
	if ($setsqlarr['certificate_img'])
	{
		/*
		3.5������ˮӡstart
		 */
		if(extension_loaded('gd')){
			include_once(QISHI_ROOT_PATH.'include/watermark.php');
			$font_dir=QISHI_ROOT_PATH."data/contactimgfont/cn.ttc";
			if(file_exists($font_dir)){
				$tpl=new watermark;
				$tpl->img($certificate_dir.$setsqlarr['certificate_img'],gbk_to_utf8($_CFG['site_name']),$font_dir,15,0);
			}
		}
		/*
		3.5����end
		 */
		$setsqlarr['certificate_img']=date("Y/m/d/").$setsqlarr['certificate_img'];
		$auth=$company_profile;
		@unlink("../../data/".$_CFG['updir_certificate']."/".$auth['certificate_img']);
		$wheresql="uid='".$_SESSION['uid']."'";
		write_memberslog($_SESSION['uid'],1,8002,$_SESSION['username'],"�ϴ���Ӫҵִ��");
		$db->updatetable(table('jobs'),array('company_audit'=>$setsqlarr['audit']),$wheresql);
		$db->updatetable(table('jobs_tmp'),array('company_audit'=>$setsqlarr['audit']),$wheresql);
		if(!$db->updatetable(table('company_profile'),$setsqlarr,$wheresql))
		{
			exit("-6");
		}
		else
		{
			$data['isok'] = 1;
			$json_encode = json_encode($data);
			exit($json_encode);
		}
	}
	else
	{
	exit("-6");
	}
}
?>