	<?php
/*
 * 74cms ��ͷ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/hunter_common.php');
$smarty->assign('leftmenu',"info");
if ($act=='hunter_profile')
{
	$smarty->assign('title','��ͷ���Ϲ��� - ��ͷ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('hunter_profile',$hunter_profile);
	// ��ע���Ա �����ȡע������
	$smarty->assign('user',$user);
	$smarty->display('member_hunter/hunter_profile.htm');
}
elseif ($act=='hunter_profile_save')
{
	
	$uid=intval($_SESSION['uid']);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['huntername']=trim($_POST['huntername'])?trim($_POST['huntername']):showmsg('��û��������ͷ���ƣ�',1);
	check_word($_CFG['filter'],$_POST['huntername'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['companyname']=trim($_POST['companyname'])?trim($_POST['companyname']):showmsg('��û��������ͷ���ڹ�˾��',1);
	check_word($_CFG['filter'],$_POST['companyname'])?showmsg($_CFG['filter_tips'],0):'';
	$code=trim($_POST['code'])?trim($_POST['code']):showmsg('��û����д�������ţ�',1);
	$telephone=trim($_POST['companytelephone'])?trim($_POST['companytelephone']):showmsg('��û����д�������룡',1);
	$setsqlarr['companytelephone']=$code.'-'.$telephone;
	$setsqlarr['district']=intval($_POST['district'])>0?intval($_POST['district']):showmsg('��û��ѡ������������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['worktime_start']=intval($_POST['worktime_start'])>1970?intval($_POST['worktime_start']):showmsg('��û����д��ҵ��ʼʱ�䣡',1);
	$setsqlarr['rank']=trim($_POST['rank'])?intval($_POST['rank']):showmsg('��û��ѡ����ͷͷ�Σ�',1);
	$setsqlarr['rank_cn']=trim($_POST['rank_cn']);
	$setsqlarr['goodtrade']=trim($_POST['goodtrade'])?trim($_POST['goodtrade']):showmsg('��û��ѡ���ó���ҵ��',1);
	$setsqlarr['goodtrade_cn']=trim($_POST['goodtrade_cn']);
	$setsqlarr['goodcategory']=trim($_POST['goodcategory'])?trim($_POST['goodcategory']):showmsg('��û��ѡ���ó�ְ�ܣ�',1);
	$setsqlarr['goodcategory_cn']=trim($_POST['goodcategory_cn']);
	$setsqlarr['contents']=trim($_POST['contents'])?trim($_POST['contents']):showmsg('����д��ͷ��飡',1);
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['cooperate_company']=trim($_POST['cooperate_company']);
	check_word($_CFG['filter'],$_POST['cooperate_company'])?showmsg($_CFG['filter_tips'],0):'';
	
	
	$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):showmsg('����дͨѶ��ַ��',1);
	check_word($_CFG['filter'],$_POST['address'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):showmsg('����д��ϵ�绰��',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['email']=trim($_POST['email'])?trim($_POST['email']):showmsg('����д��ϵ���䣡',1);
	check_word($_CFG['filter'],$_POST['email'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['yellowpages']=intval($_POST['yellowpages']);
	
	$setsqlarr['email_show']=intval($_POST['email_show']);
	$setsqlarr['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr['address_show']=intval($_POST['address_show']);
	
	$setsqlarr['key']=$setsqlarr['huntername'].$setsqlarr['companyname'].$setsqlarr['goodtrade_cn'].$setsqlarr['goodcategory_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['huntername']} {$setsqlarr['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	
	$link[0]['text'] = "�鿴�޸Ľ��";
	$link[0]['href'] = '?act=hunter_profile';
	$link[1]['text'] = "������Ƹ��Ϣ";
	$link[1]['href'] = "hunter_jobs.php?act=addjobs";
	$link[2]['text'] = "��Ա������ҳ";
	$link[2]['href'] = "hunter_index.php?";
	if ($_CFG['hunter_repeat']=="0")
	{
		$info=$db->getone("SELECT uid FROM ".table('hunter_profile')." WHERE huntername ='{$setsqlarr['huntername']}' AND companyname ='{$setsqlarr['companyname']}' AND uid<>'{$_SESSION['uid']}' LIMIT 1");
		if(!empty($info))
		{
			showmsg("{$setsqlarr['huntername']}�Ѿ����ڣ�ͬ��ͷ��Ϣ�����ظ�ע��",1);
		}
	}
	if ($hunter_profile)
	{
			$setsqlarr['refreshtime']=$timestamp;
			$_CFG['audit_edit_hunter']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_edit_hunter']):'';
			if ($db->updatetable(table('hunter_profile'), $setsqlarr," uid='{$uid}'"))
			{
				unset($setsqlarr);
				write_memberslog($_SESSION['uid'],$_SESSION['utype'],8501,$_SESSION['username'],"�޸���ͷ����");
				showmsg("����ɹ���",2,$link);
			}
			else
			{
				showmsg("����ʧ�ܣ�",0);
			}
	}
	else
	{
			$setsqlarr['audit']=intval($_CFG['audit_add_hunter']);
			$setsqlarr['addtime']=$timestamp;
			$setsqlarr['refreshtime']=$timestamp;
			$insertid = $db->inserttable(table('hunter_profile'),$setsqlarr,1);
			if ($insertid)
			{
				write_memberslog($_SESSION['uid'],$_SESSION['utype'],8500,$_SESSION['username'],"������ͷ����");
				baidu_submiturl(url_rewrite('QS_hunter_show',array('id'=>$insertid)),'addhunter');
				showmsg("����ɹ���",2,$link);
			}
			else
			{
				showmsg("����ʧ�ܣ�",0);
			}
	}
}

elseif ($act=='photo')
{
	if (empty($hunter_profile)) showmsg('����������ͷ�������ϣ�',0);
	$smarty->assign('title','��Ƭ��֤ - ��ͷ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('points',get_cache('points_rule'));
	$smarty->assign('hunter_profile',$hunter_profile);
	$smarty->display('member_hunter/hunter_photo.htm');
}
elseif ($act=='photo_save')
{
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	$setsqlarr['audit']=2;//���Ĭ�������..
	!$_FILES['photo_img']['name']?showmsg('���ϴ���Ƭ��',1):"";
	$photo_dir="../../data/hunter/".date("Y/m/d/");
	make_dir($photo_dir);
	$setsqlarr['photo_img']=_asUpFiles($photo_dir, "photo_img",$_CFG['resume_photo_max'],'gif/jpg/bmp/png',true);
	if ($setsqlarr['photo_img'])
	{
	/*
	3.5������ˮӡstart
	 */
	if(extension_loaded('gd')){
		include_once(QISHI_ROOT_PATH.'include/watermark.php');
		$font_dir=QISHI_ROOT_PATH."data/contactimgfont/cn.ttc";
		if(file_exists($font_dir)){
			$tpl=new watermark;
			$tpl->img($photo_dir.$setsqlarr['photo_img'],gbk_to_utf8($_CFG['site_name']),$font_dir,15,0);
		}
	}
	/*
	3.5����end
	 */
	$setsqlarr['photo_img']=date("Y/m/d/").$setsqlarr['photo_img'];
	$auth=$hunter_profile;
	@unlink("../../data/hunter/".$auth['photo_img']);
	!$db->updatetable(table('hunter_profile'),$setsqlarr," id='".intval($hunter_profile['id'])."' AND uid='".intval($_SESSION['uid'])."'")?showmsg("����ʧ�ܣ�",0):showmsg("����ɹ���",2);
	}
	else
	{
	showmsg('����ʧ�ܣ�',1);
	}
}

unset($smarty);
?>