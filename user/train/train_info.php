<?php
/*
 * 74cms ��ѵ������Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/train_common.php');
$smarty->assign('leftmenu',"info");
if ($act=='train_profile')
{
	$smarty->assign('title','�������Ϲ��� - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('train_profile',$train_profile);
	// ��ע���Ա �����ȡע������
	$smarty->assign('user',$user);
	$smarty->display('member_train/train_profile.htm');
}
elseif ($act=='train_profile_save')
{
	$uid=intval($_SESSION['uid']);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['trainname']=trim($_POST['trainname'])?trim($_POST['trainname']):showmsg('��û������������ƣ�',1);
	check_word($_CFG['filter'],$_POST['trainname'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['nature']=trim($_POST['nature'])?intval($_POST['nature']):showmsg('��ѡ��������ʣ�',1);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['founddate']=intval(convert_datefm($_POST['founddate'],2));
	if (empty($setsqlarr['founddate']))
	{
	showmsg('����д����ʱ�䣡ʱ���ʽ��YYYY-MM-DD',1);
	}	
	if ($setsqlarr['founddate']>=time())
	{
	showmsg('����ʱ�䲻�ܴ��ڽ���',1);
	}	
	$setsqlarr['district']=intval($_POST['district'])>0?intval($_POST['district']):showmsg('��ѡ������������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):showmsg('����дͨѶ��ַ��',1);
	check_word($_CFG['filter'],$_POST['address'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):showmsg('����д��ϵ�ˣ�',1);
	check_word($_CFG['filter'],$_POST['contact'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):showmsg('����д��ϵ�绰��',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['email']=trim($_POST['email'])?trim($_POST['email']):showmsg('����д��ϵ���䣡',1);
	check_word($_CFG['filter'],$_POST['email'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['website']=trim($_POST['website']);
	check_word($_CFG['filter'],$_POST['website'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['contents']=trim($_POST['contents'])?trim($_POST['contents']):showmsg('����д������飡',1);
		check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['teacherpower']=trim($_POST['teacherpower'])?trim($_POST['teacherpower']):showmsg('����дʦ��������',1);
		check_word($_CFG['filter'],$_POST['teacherpower'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['achievement']=trim($_POST['achievement'])?trim($_POST['achievement']):showmsg('����д��Ҫҵ����',1);
		check_word($_CFG['filter'],$_POST['achievement'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['yellowpages']=intval($_POST['yellowpages']);
	
	$setsqlarr['contact_show']=intval($_POST['contact_show']);
	$setsqlarr['email_show']=intval($_POST['email_show']);
	$setsqlarr['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr['address_show']=intval($_POST['address_show']);
	
	$link[0]['text'] = "�鿴�޸Ľ��";
	$link[0]['href'] = '?act=train_profile';
	$link[1]['text'] = "������ѵ�γ�";
	$link[1]['href'] = "train_course.php?act=addcourse";
	$link[2]['text'] = "��Ա������ҳ";
	$link[2]['href'] = "train_index.php?";
	if ($_CFG['train_repeat']=='0')
	{
		$info=$db->getone("SELECT uid FROM ".table('train_profile')." WHERE trainname ='{$setsqlarr['trainname']}' AND uid<>'{$_SESSION['uid']}' LIMIT 1");
		if(!empty($info))
		{
			showmsg("{$setsqlarr['trainname']}�Ѿ����ڣ�ͬ������Ϣ�����ظ�ע��",1);
		}
	}
	if ($train_profile)
	{
			$_CFG['audit_edit_train']<>'-1'?$setsqlarr['audit']=intval($_CFG['audit_edit_train']):$train_profile['audit'];
			if ($db->updatetable(table('train_profile'), $setsqlarr," uid='{$uid}'"))
			{
				$coursearr['trainname']=$setsqlarr['trainname'];
				if (!$db->updatetable(table('course'),$coursearr," uid=".$setsqlarr['uid']."")) showmsg('�޸���ѵ�������Ƴ���',0);
				if (!$db->updatetable(table('train_teachers'),$coursearr," uid=".$setsqlarr['uid']."")) showmsg('�޸���ѵ�������Ƴ���',0);
				write_memberslog($_SESSION['uid'],$_SESSION['utype'],8101,$_SESSION['username'],"�޸���ѵ��������");
		     	showmsg("����ɹ���",2,$link);
			}
			else
			{
				showmsg("����ʧ�ܣ�",0);
			}
	}
	else
	{
			$setsqlarr['audit']=intval($_CFG['audit_add_train']);
			$setsqlarr['addtime']=$timestamp;
			$setsqlarr['refreshtime']=$timestamp;
			$insertid = $db->inserttable(table('train_profile'),$setsqlarr,1);
			if ($insertid)
			{
				write_memberslog($_SESSION['uid'],$_SESSION['utype'],8100,$_SESSION['username'],"������ѵ��������");
				baidu_submiturl(url_rewrite('QS_train_agencyshow',array('id'=>$insertid)),'addagency');
				showmsg("����ɹ���",2,$link);
			}
			else
			{
				showmsg("����ʧ�ܣ�",0);
			}
	}
}
elseif ($act=='train_auth')
{
	$link[0]['text'] = "���ƻ�������";
	$link[0]['href'] = '?act=train_profile';
	$link[1]['text'] = "������ҳ";
	$link[1]['href'] = 'train_index.php';
	if (empty($train_profile['trainname'])) showmsg("���������Ļ����������ϴ�Ӫҵִ�գ�",1,$link);
	$smarty->assign('title','Ӫҵִ�� - ��ѵ������Ա���� - '.$_CFG['site_name']);
	$smarty->assign('points',get_cache('points_rule'));
	$smarty->assign('train_profile',$train_profile);
	$smarty->display('member_train/train_auth.htm');
}
elseif ($act=='train_auth_save')
{
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	$setsqlarr['license']=trim($_POST['license'])?trim($_POST['license']):showmsg('��û������Ӫҵִ��ע��ţ�',1);
	$setsqlarr['audit']=2;//���Ĭ�������..
	!$_FILES['certificate_img']['name']?showmsg('���ϴ�ͼƬ��',1):"";
	$certificate_dir="../../data/".$_CFG['updir_train_certificate']."/".date("Y/m/d/");
	make_dir($certificate_dir);
	$setsqlarr['certificate_img']=_asUpFiles($certificate_dir, "certificate_img",$_CFG['certificate_train_max_size'],'gif/jpg/bmp/png',true);
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
	$auth=$train_profile;
	@unlink("../../data/".$_CFG['updir_train_certificate']."/".$auth['certificate_img']);
	$wheresql="uid='".$_SESSION['uid']."'";
	write_memberslog($_SESSION['uid'],4,8102,$_SESSION['username'],"�ϴ�����ѵ����Ӫҵִ��");
	!$db->updatetable(table('train_profile'),$setsqlarr,$wheresql)?showmsg('����ʧ�ܣ�',1):showmsg('����ɹ��������ĵȴ�����Ա��ˣ�',2);
	}
	else
	{
	showmsg('����ʧ�ܣ�',1);
	}
}
elseif ($act=='train_logo')
{
	$link[0]['text'] = "���ƻ�������";
	$link[0]['href'] = '?act=train_profile';
	$link[1]['text'] = "��Ա������ҳ";
	$link[1]['href'] = 'train_index.php';
	if (empty($train_profile['trainname'])) showmsg("���������Ļ����������ϴ�����LOGO��",1,$link);
	$smarty->assign('title','����LOGO - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('train_profile',$train_profile);
	$smarty->assign('rand',rand(1,100));
	$smarty->display('member_train/train_logo.htm');
}
elseif ($act=='train_logo_save')
{
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	!$_FILES['logo']['name']?showmsg('���ϴ�ͼƬ��',1):"";
	$uplogo_dir="../../data/train_logo/".date("Y/m/d/");
	make_dir($uplogo_dir);
	$setsqlarr['logo']=_asUpFiles($uplogo_dir, "logo",$_CFG['logo_train_max_size'],'gif/jpg/bmp/png',$_SESSION['uid']);
	if ($setsqlarr['logo'])
	{
	$setsqlarr['logo']=date("Y/m/d/").$setsqlarr['logo'];
	$logo_src="../../data/train_logo/".$setsqlarr['logo'];
	$thumb_dir=$uplogo_dir;
	makethumb($logo_src,$thumb_dir,300,110);//��������ͼ
	$wheresql="uid='".$_SESSION['uid']."'";
			if ($db->updatetable(table('train_profile'),$setsqlarr,$wheresql))
			{
			$link[0]['text'] = "�鿴LOGO";
			$link[0]['href'] = '?act=train_logo';
			write_memberslog($_SESSION['uid'],4,8103,$_SESSION['username'],"�ϴ�����ѵ����LOGO");
			showmsg('�ϴ��ɹ���',2,$link);
			}
			else
			{
			showmsg('����ʧ�ܣ�',1);
			}
	}
	else
	{
	showmsg('����ʧ�ܣ�',1);
	}
}
elseif ($act=='train_logo_del')
{
	$uplogo_dir="../../data/train_logo/";
	$auth=$train_profile;//��ȡԭʼͼƬ
	@unlink($uplogo_dir.$auth['logo']);//��ɾ��ԭʼͼƬ
	$setsqlarr['logo']="";
	$wheresql="uid='".$_SESSION['uid']."'";
		if ($db->updatetable(table('train_profile'),$setsqlarr,$wheresql))
		{
		write_memberslog($_SESSION['uid'],4,8104,$_SESSION['username'],"ɾ���˻���LOGO");
		showmsg('ɾ���ɹ���',2);
		}
		else
		{
		showmsg('ɾ��ʧ�ܣ�',1);
		}
}
elseif ($act=='train_map')
{
	$link[0]['text'] = "��д��������";
	$link[0]['href'] = '?act=train_profile';
	if (empty($train_profile['trainname'])) showmsg("������������ѵ�������������õ��ӵ�ͼ��",1,$link);
	if ($train_profile['map_open']=="1")//�����Ѿ���ͨ
	{
	header("Location: ?act=train_map_set");
	}
	else
	{
		if($_CFG['operation_train_mode']=='1'){
			$points=get_cache('points_rule');//��ȡ�������ѹ���
			$smarty->assign('points',$points['train_map']['value']);
		}elseif($_CFG['operation_train_mode']=='2'){
			$setmeal=get_user_setmeal($_SESSION['uid']);
			$smarty->assign('map_open',$setmeal['map_open']);
		}
		$smarty->assign('title','��ͨ���ӵ�ͼ - ��ѵ��Ա���� - '.$_CFG['site_name']);
		$smarty->display('member_train/train_map_open.htm');
	}
}
elseif ($act=='train_map_open')
{
	$link[0]['text'] = "��д��ѵ��������";
	$link[0]['href'] = '?act=train_profile';
	if (empty($train_profile['trainname'])) showmsg("������������ѵ�������������õ��ӵ�ͼ��",1);
	if($_CFG['operation_train_mode']=='1'){
		$points=get_cache('points_rule');
		$user_points=get_user_points($_SESSION['uid']);
		if ($points['train_map']['type']=='2' && $points['train_map']['value']>$user_points)
		{
		showmsg("���".$_CFG['train_points_byname']."���㣬���ֵ���ٽ�����ز�����",0);
		}
	}elseif($_CFG['operation_train_mode']=='2'){
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if ($setmeal['endtime']<time() &&  $setmeal['endtime']<>'0'){
			showmsg("��ķ����ײ��ѵ��ڣ������¿�ͨ����",0);
		}elseif($setmeal['map_open']=='0'){
			showmsg("������ײͣ�{$setmeal['setmeal_name']} û�п�ͨ���ӵ�ͼ��Ȩ�ޣ������������ײͣ�",0);
		}
	}
	$wheresql="uid='".$_SESSION['uid']."'";
	$setsqlarr['map_open']=1;
		if ($db->updatetable(table('train_profile'),$setsqlarr,$wheresql))
		{
			//�����ʼ�
			$mailconfig=get_cache('mailconfig');
			if ($mailconfig['set_addmap']=="1" && $user['email_audit']=="1")
			{
			dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&act=set_addmap");
			}
			//sms
			$sms=get_cache('sms_config');
			if ($sms['open']=="1" && $sms['set_addmap']=="1"  && $user['mobile_audit']=="1")
			{
			dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&act=set_addmap");
			}
			//sms
			$link[0]['text'] = "���õ��ӵ�ͼ";
			$link[0]['href'] = '?act=train_map_set';
			$link[1]['text'] = "���ػ�Ա������ҳ";
			$link[1]['href'] = 'train_index.php?act=';			
			write_memberslog($_SESSION['uid'],4,8105,$_SESSION['username'],"��ͨ�˵��ӵ�ͼ");
			if($_CFG['operation_train_mode']=='1'){
				if ($points['train_map']['value']>0)
				{
				report_deal($_SESSION['uid'],$points['train_map']['type'],$points['train_map']['value']);
				$user_points=get_user_points($_SESSION['uid']);
				$operator=$points['train_map']['type']=="1"?"+":"-";
				write_memberslog($_SESSION['uid'],4,9101,$_SESSION['username'],"��ͨ�˵��ӵ�ͼ({$operator}{$points['train_map']['value']})��(ʣ��:{$user_points})");
				}
			}elseif($_CFG['operation_train_mode']=='2'){
				write_memberslog($_SESSION['uid'],4,9102,$_SESSION['username'],"ʹ�÷����ײͿ�ͨ�˵��ӵ�ͼ");
			}
			showmsg('�ɹ���ͨ��',2,$link);
		}
		else
		{
		showmsg('��ͨʧ�ܣ�',1);
		}
}
elseif ($act=='train_map_set')
{
	$smarty->assign('title','���õ��ӵ�ͼ - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('train_profile',$train_profile);
	$smarty->display('member_train/train_map_set.htm');
}
elseif ($act=='train_map_set_save')
{
	$setsqlarr['map_x']=trim($_POST['x'])?trim($_POST['x']):showmsg('���ȵ�����ڵ�ͼ�ϱ���ҵ�λ�á���ť��Ȼ���ٵ�������ҵ�λ�ý��б��棡',1);
	$setsqlarr['map_y']=trim($_POST['y'])?trim($_POST['y']):showmsg('���ȵ�����ڵ�ͼ�ϱ���ҵ�λ�á���ť��Ȼ���ٵ�������ҵ�λ�ý��б��棡',1);
	$setsqlarr['map_zoom']=trim($_POST['zoom']);
	$wheresql=" uid='{$_SESSION['uid']}'";
	write_memberslog($_SESSION['uid'],4,8106,$_SESSION['username'],"�����˵��ӵ�ͼ����");
	if ($db->updatetable(table('train_profile'),$setsqlarr,$wheresql))
	{
		$coursesql['map_x']=$setsqlarr['map_x'];
		$coursesql['map_y']=$setsqlarr['map_y'];
		$db->updatetable(table('course'),$coursesql,$wheresql);
		unset($setsqlarr['map_zoom']);
     	showmsg('����ɹ�',2);
	}
	else
	{
	showmsg('����ʧ��',1);
	}
}
elseif ($act=='train_news')
{
	$smarty->assign('news',get_train_news(0,60,$_SESSION['uid']));
	$smarty->assign('title','�������� - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_train/train_news_list.htm');
}
if ($act=='train_news_add')
{
	$link[0]['text'] = "���ƻ�������";
	$link[0]['href'] = '?act=train_profile';
	$link[1]['text'] = "��Ա������ҳ";
	$link[1]['href'] = 'train_index.php';
	if (empty($train_profile['trainname'])) showmsg("���������Ļ������ϣ�",1,$link);
	$smarty->assign('title','��ӻ������� - ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_train/train_news_add.htm');
}
elseif ($act=='train_news_add_save')
{
	$n=$db->get_total("SELECT COUNT(*) AS num FROM ".table('train_news')." WHERE uid='".intval($_SESSION['uid'])."'");
	if($n>=60)
	{
	showmsg('����������෢��60����',1);
	}
	if ($train_profile['audit']=='1')
	{
	$setsqlarr['audit']=intval($_CFG['audit_verifytrain_addnews']);
	}
	else
	{
	$setsqlarr['audit']=intval($_CFG['audit_unexaminedtrain_addnews']);
	}
	$setsqlarr['title']=!empty($_POST['title'])?trim($_POST['title']):showmsg('����д���⣡',1);
	check_word($_CFG['filter'],$_POST['title'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['order']=intval($_POST['order']);
	$setsqlarr['content']=!empty($_POST['content'])?trim($_POST['content']):showmsg('����д����',1);
	check_word($_CFG['filter'],$_POST['content'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['addtime']=time();
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['train_id']=$train_profile['id'];
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=train_news';
	$link[1]['text'] = "�������";
	$link[1]['href'] = '?act=train_news_add';
	!$db->inserttable(table('train_news'),$setsqlarr)?showmsg("���ʧ�ܣ�",0):showmsg("��ӳɹ���",2,$link);
}
if ($act=='train_news_edit')
{
	$uid=intval($_SESSION['uid']);
	$id=intval($_GET['id']);
	$smarty->assign('news',$db->getone("select * from ".table('train_news')." where uid='{$uid}' AND id ='{$id}' LIMIT 1"));
	$smarty->assign('title','�޸Ļ������� - ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_train/train_news_edit.htm');
}
elseif ($act=='train_news_edit_save')
{
	if ($train_profile['audit']=='1')
	{
	$_CFG['audit_verifytrain_editnews']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_verifytrain_editnews']):'';
	}
	else
	{
	$_CFG['audit_unexaminedtrain_editnews']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_unexaminedtrain_editnews']):'';
	}
	$setsqlarr['title']=!empty($_POST['title'])?trim($_POST['title']):showmsg('����д���⣡',1);
	check_word($_CFG['filter'],$_POST['title'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['order']=intval($_POST['order']);
	$setsqlarr['content']=!empty($_POST['content'])?trim($_POST['content']):showmsg('����д����',1);
	check_word($_CFG['filter'],$_POST['content'])?showmsg($_CFG['filter_tips'],0):'';
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=train_news';
	$uid=intval($_SESSION['uid']);
	$id=intval($_POST['id']);
	!$db->updatetable(table('train_news'),$setsqlarr," uid='{$uid}' AND id='{$id}' ")?showmsg("�޸�ʧ�ܣ�",0):showmsg("�޸ĳɹ���",2,$link);
}
elseif ($act=='train_news_del')
{
	$id =!empty($_POST['id'])?$_POST['id']:$_GET['id'];
	if (empty($id))
	{
	showmsg("��û��ѡ�����ţ�",1);
	}
	if($n=del_train_news($id,$_SESSION['uid']))
	{
	showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	showmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif ($act=='train_img')
{
	$link[0]['text'] = "���ƻ�������";
	$link[0]['href'] = '?act=train_profile';
	$link[1]['text'] = "��Ա������ҳ";
	$link[1]['href'] = 'train_index.php';
	if (empty($train_profile['trainname'])) showmsg("���������Ļ����������ϴ�����ͼƬ��",1,$link);
	$smarty->assign('title','����ͼƬ - ������Ա���� - '.$_CFG['site_name']);
	$smarty->assign('img',get_train_img(0,60,$_SESSION['uid']));	
	$smarty->display('member_train/train_img.htm');
}
elseif ($act=='train_img_save')
{
	$n=$db->get_total("SELECT COUNT(*) AS num FROM ".table('train_img')." WHERE uid='".intval($_SESSION['uid'])."'");
	if($n>=8)
	{
	showmsg('����ͼƬ��෢��8�ţ�',1);
	}
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	!$_FILES['img']['name']?showmsg('���ϴ�ͼƬ��',1):"";
	$datedir=date("Y/m/d/");
	$up_dir="../../data/train_img/original/".$datedir;
	make_dir($up_dir);
	$setsqlarr['img']=_asUpFiles($up_dir,"img",800,'gif/jpg/bmp/png',true);
	if ($setsqlarr['img'])
	{
			$img_src=$up_dir.$setsqlarr['img'];
			$thumb_dir="../../data/train_img/thumb/".$datedir;
			make_dir($thumb_dir);
			makethumb($img_src,$up_dir,600,600);
			makethumb($img_src,$thumb_dir,295,165);
			$setsqlarr['uid']=intval($_SESSION['uid']);
			$setsqlarr['train_id']=$train_profile['id'];
			$setsqlarr['addtime']=time();
			$setsqlarr['title']=trim($_POST['title']);
			$setsqlarr['img']=$datedir.$setsqlarr['img'];
			if ($train_profile['audit']=='1')
			{
			$setsqlarr['audit']=intval($_CFG['audit_verifytrain_addimg']);
			}
			else
			{
			$setsqlarr['audit']=intval($_CFG['audit_unexaminedtrain_addimg']);
			}
			if ($db->inserttable(table('train_img'),$setsqlarr))
			{
			$link[0]['text'] = "������һҳ";
			$link[0]['href'] = '?act=train_img';
			showmsg('�ϴ��ɹ���',2,$link);
			}
			else
			{
			showmsg('����ʧ�ܣ�',1);
			}
	}
	else
	{
	showmsg('����ʧ�ܣ�',1);
	}
}
elseif ($act=='train_img_del')
{
	$uid=intval($_SESSION['uid']);
	$id=intval($_GET['id']);
	$img=$db->getone("select * from ".table('train_img')." WHERE uid='{$uid}' AND id='{$id}' LIMIT 1");
	if (empty($img))
	{
	showmsg('ɾ��ʧ�ܣ�',1);
	}
	@unlink("../../data/train_img/original/".$img['img']);
	@unlink("../../data/train_img/thumb/".$img['img']);
	$db->query("Delete from ".table('train_img')." WHERE  uid='{$uid}' AND id='{$id}'");
	showmsg('ɾ���ɹ���',2);
}
unset($smarty);
?>