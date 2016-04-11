<?php
/*
 * 74cms ��ѵ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/train_common.php');
$smarty->assign('leftmenu',"teachers");
$act=trim($_REQUEST['act']);
if ($act=='teachers')
{
	$wheresql=" WHERE uid='{$_SESSION['uid']}' AND train_id='{$train_profile['id']}' ";
	$audit=intval($_GET['audit']);
	if($audit){
	 $wheresql.=' AND audit='.$audit.' ';
	}
	$orderby=" order by refreshtime desc";
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('train_teachers').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$offset=($page->nowindex-1)*$perpage;
	$sql='select * from '.table('train_teachers').$wheresql.$orderby;
	$smarty->assign('title','��ʦ���� - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('total',$total);
	$smarty->assign('teachers',get_teachers($offset,$perpage,$sql,true));
	$smarty->assign('page',$page->show(3));
	$smarty->display('member_train/train_teachers.htm');
}
elseif ($act=='add_teachers')
{
	if ($train_profile['trainname'])
	{
		$pid=intval($_REQUEST['pid']);
		$uid=intval($_SESSION['uid']);
		$teachers=get_teachers_one($pid,$uid);
		$smarty->assign('teachers',$teachers);
		if($teachers){
			$smarty->assign('go_teachers_show',1);
		}
		$smarty->assign('act',$act);
		$smarty->assign('pid',$pid);
		$smarty->assign('title','��ʦ��Ϣ - ��ѵ��Ա���� - '.$_CFG['site_name']);
		if ($_CFG['operation_train_mode']=="2")
		{
			$setmeal=get_user_setmeal($_SESSION['uid']);
			$smarty->assign('setmeal',$setmeal);
			$smarty->assign('add_mode',2);
		}
		elseif ($_CFG['operation_train_mode']=="1")
		{
			$smarty->assign('points_total',get_user_points($_SESSION['uid']));
			$smarty->assign('points',get_cache('points_rule'));
			$smarty->assign('add_mode',1);
		}
		$captcha=get_cache('captcha');
		$smarty->assign('verify_addteachers',$captcha['verify_addteachers']);
		$smarty->display('member_train/train_addteachers.htm');
	}
	else
	{
	$link[0]['text'] = "���ƻ�������";
	$link[0]['href'] = 'train_info.php?act=train_profile';
	showmsg("Ϊ�˴ﵽ���õ�����Ч���������������Ļ������ϣ�",1,$link);
	}
}
elseif ($act=='make1_save')
{
	$captcha=get_cache('captcha');
	$postcaptcha = trim($_POST['postcaptcha']);
	if(intval($_REQUEST['pid'])===0){
		if($captcha['verify_addteachers']=='1' && empty($postcaptcha))
		{
			showmsg("����д��֤��",1);
		}
		if ($captcha['verify_addteachers']=='1' && strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
		{
			showmsg("��֤�����",1);
		}
	}
	$add_mode=trim($_POST['add_mode']);
	if ($_CFG['operation_train_mode']=='1')
	{
		$points_rule=get_cache('points_rule');
		$user_points=get_user_points($_SESSION['uid']);
		$total=0;
		if ($points_rule['teacher_add']['type']=="2" && $points_rule['teacher_add']['value']>0)
		{
		$total=$points_rule['teacher_add']['value'];
		}
		if ($total>$user_points)
		{
		$link[0]['text'] = "������ֵ";
		$link[0]['href'] = 'train_service.php?act=order_add';
		$link[1]['text'] = "��Ա������ҳ";
		$link[1]['href'] = 'train_index.php?act=';
		showmsg("���".$_CFG['train_points_byname']."���㣬���ֵ���ٷ�����",0,$link);
		}
	}
	elseif ($_CFG['operation_train_mode']=='2')
	{
		$link[0]['text'] = "������ͨ����";
		$link[0]['href'] = 'train_service.php?act=setmeal_list';
		$link[1]['text'] = "��Ա������ҳ";
		$link[1]['href'] = 'train_index.php?act=';
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{					
			showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
		}
		if ($setmeal['teachers_num']<=0)
		{
			showmsg("��ǰ��ӵĽ�ʦ�Ѿ�������������ƣ������������ײͣ�",1,$link);
		}
	}
	$setsqlarr['add_mode']=intval($add_mode);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['trainname']=$train_profile['trainname'];
	$setsqlarr['train_id']=$train_profile['id'];
	
	$setsqlarr['teachername']=!empty($_POST['teachername'])?trim($_POST['teachername']):showmsg('��û����д��ʦ������',1);
	check_word($_CFG['filter'],$_POST['teachername'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['sex']=trim($_POST['sex'])?intval($_POST['sex']):showmsg('��ѡ���Ա�',1);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['birthdate']=intval($_POST['birthdate'])>1945?intval($_POST['birthdate']):showmsg('����ȷ��д�������',1);
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):showmsg('��ѡ�����ڵ�����',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['education']=!empty($_POST['education'])?intval($_POST['education']):showmsg('��ѡ�����ѧ����',1);
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['speciality']=!empty($_POST['speciality'])?trim($_POST['speciality']):showmsg('����д�ó�רҵ��',1);
	check_word($_CFG['filter'],$_POST['speciality'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['positionaltitles']=!empty($_POST['positionaltitles'])?trim($_POST['positionaltitles']):showmsg('����д����ְ�ƣ�',1);
	check_word($_CFG['filter'],$_POST['positionaltitles'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['graduated_school']=trim($_POST['graduated_school'])?trim($_POST['graduated_school']):showmsg('����д��ҵԺУ��',1);
	check_word($_CFG['filter'],$_POST['graduated_school'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['work_unit']=trim($_POST['work_unit'])?trim($_POST['work_unit']):showmsg('����д������λ��',1);
	check_word($_CFG['filter'],$_POST['work_unit'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['work_position']=trim($_POST['work_position']);
	check_word($_CFG['filter'],$_POST['work_position'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('��û����д���˼�飡',1);
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['achievements']=!empty($_POST['achievements'])?trim($_POST['achievements']):showmsg('��û����д���˳ɾͣ�',1);
	check_word($_CFG['filter'],$_POST['achievements'])?showmsg($_CFG['filter_tips'],0):'';
	
	$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):showmsg('����д��ϵ�绰��',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['email']=trim($_POST['email']);
	check_word($_CFG['filter'],$_POST['email'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):showmsg('����дͨѶ��ַ��',1);
	check_word($_CFG['filter'],$_POST['address'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['website']=trim($_POST['website']);
	check_word($_CFG['filter'],$_POST['website'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['qq']=trim($_POST['qq']);
	check_word($_CFG['filter'],$_POST['qq'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['refreshtime']=$timestamp;

	
	$setsqlarr['email_show']=intval($_POST['email_show']);
	$setsqlarr['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr['address_show']=intval($_POST['address_show']);
	$setsqlarr['qq_show']=intval($_POST['qq_show']);

	if($_FILES['photo']['name']){
		require_once(QISHI_ROOT_PATH.'include/upload.php');
		$photo_dir=substr($_CFG['teacher_photo_dir'],strlen($_CFG['site_dir']));
		$photo_dir="../../".$photo_dir.date("Y/m/d/");
		make_dir($photo_dir);
		$setsqlarr['photo_img']=_asUpFiles($photo_dir, "photo",'1000','gif/jpg/bmp/png',true);
		$setsqlarr['photo_img']=date("Y/m/d/").$setsqlarr['photo_img'];
		$setsqlarr['photo']=1;
	}
	

	//�ײͻ��«�Ҳ���
	if(intval($_REQUEST['pid'])===0){
		if ($_CFG['operation_train_mode']=='1')
		{
			if ($points_rule['teacher_add']['value']>0)
			{
			report_deal($_SESSION['uid'],$points_rule['teacher_add']['type'],$points_rule['teacher_add']['value']);
			$user_points=get_user_points($_SESSION['uid']);
			$operator=$points_rule['teacher_add']['type']=="1"?"+":"-";
			write_memberslog($_SESSION['uid'],4,9101,$_SESSION['username'],"��ӽ�ʦ��<strong>{$setsqlarr['teachername']}</strong>��({$operator}{$points_rule['teacher_add']['value']})��(ʣ��:{$user_points})");
			}
		}
		elseif ($_CFG['operation_train_mode']=='2')
		{
			action_user_setmeal($_SESSION['uid'],'teachers_num');
			$setmeal=get_user_setmeal($_SESSION['uid']);
			write_memberslog($_SESSION['uid'],4,9102,$_SESSION['username'],"��ӽ�ʦ:<strong>{$_POST['teachername']}</strong>����������ӽ�ʦ:<strong>{$setmeal['teachers_num']}</strong>λ");
		}
	}
	if (intval($_REQUEST['pid'])===0)
	{	
			if ($train_profile['audit']=="1")
			{
			$setsqlarr['audit']=intval($_CFG['audit_verifytrain_addtea']);
			}
			else
			{
			$setsqlarr['audit']=intval($_CFG['audit_unexaminedtrain_addtea']);
			}
			$setsqlarr['addtime']=$timestamp;
			$pid=$db->inserttable(table('train_teachers'),$setsqlarr,1);
			write_memberslog($_SESSION['uid'],4,8107,$_SESSION['username'],"����˽�ʦ��{$setsqlarr['teachername']}");
			baidu_submiturl(url_rewrite('QS_train_lecturershow',array('id'=>$pid)),'addteacher');
			header("Location: train_teacher.php?act=teachers");
	}
	else
	{
		if ($train_profile['audit']=="1")
		{
			if(intval($_CFG['audit_verifytrain_edittea'])!="-1"){
				$setsqlarr['audit']=intval($_CFG['audit_verifytrain_edittea']);
			}
		}
		else
		{
			if(intval($_CFG['audit_verifytrain_edittea'])!="-1"){
				$setsqlarr['audit']=intval($_CFG['audit_unexaminedtrain_edittea']);
			}
		}
		$db->updatetable(table('train_teachers'),$setsqlarr," id='".intval($_REQUEST['pid'])."'  AND uid='{$setsqlarr['uid']}'");
		$teaarr['teacher_cn']=$setsqlarr['teachername'];
		$db->updatetable(table('course'),$teaarr," teacher_id='".intval($_REQUEST['pid'])."'  AND uid='{$setsqlarr['uid']}'");
		write_memberslog($_SESSION['uid'],4,8108,$_SESSION['username'],"�޸��˽�ʦ��{$setsqlarr['teachername']}����Ϣ");
		header("Location: train_teacher.php?act=teachers");
	}
}		
elseif ($act=='make2')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$link[0]['text'] = "���ؽ�ʦ�б�";
	$link[0]['href'] = '?act=teachers';
	if ($uid==0 || $pid==0) showmsg('��ʦ�����ڣ�',1,$link);
	$teachers=get_teachers_one($pid,$uid);
	$link[0]['text'] = "��д��ʦ������Ϣ";
	$link[0]['href'] = '?act=add_teachers';
	if (empty($teachers)) showmsg("������д��ʦ������Ϣ��",1,$link);
	
	if ($teachers['photo_img'] && empty($_GET['addphoto']))
	{
	header("Location: ?act=photo_cutting&pid=".$pid);
	}
	$smarty->assign('act',$act);
	$smarty->assign('teachers',$teachers);
	$smarty->assign('pid',$pid);
	$smarty->assign('title','��ʦ��Ϣ - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_train/train_teacher_make2.htm');
}
elseif ($act=='make2_save')
{
	!$_FILES['photo']['name']?showmsg('���ϴ�ͼƬ��',1):"";
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	if (intval($_REQUEST['pid'])==0) showmsg('��������',0);
	$photo_dir=substr($_CFG['teacher_photo_dir'],strlen($_CFG['site_dir']));
	$photo_dir="../../".$photo_dir.date("Y/m/d/");
	make_dir($photo_dir);
	$setsqlarr['photo_img']=_asUpFiles($photo_dir, "photo",'1000','gif/jpg/bmp/png',true);
	$setsqlarr['photo_img']=date("Y/m/d/").$setsqlarr['photo_img'];
	$setsqlarr['photo']=1;
	!$db->updatetable(table('train_teachers'),$setsqlarr," id='".intval($_REQUEST['pid'])."' AND uid='".intval($_SESSION['uid'])."'")?showmsg("����ʧ�ܣ�",0):'';
	header("Location: ?act=photo_cutting&pid=".intval($_REQUEST['pid']));
}
elseif ($act=='photo_cutting')
{
		$uid=intval($_SESSION['uid']);
		$pid=intval($_REQUEST['pid']);
		$link[0]['text'] = "��д��ʦ������Ϣ";
		$link[0]['href'] = '?act=add_teachers';
		$teachers=get_teachers_one($pid,$uid);
		if (empty($teachers)) showmsg("������д��ʦ������Ϣ��",1,$link);
		if (empty($teachers['photo_img']))
		{
		header('Location: ?act=make2&pid='.$_REQUEST['pid']);
		}
	$photo_thumb_dir=QISHI_ROOT_PATH.substr($_CFG['teacher_photo_dir_thumb'],strlen($_CFG['site_dir']));
	make_dir($photo_thumb_dir.dirname($teachers['photo_img']));
	if (file_exists($photo_thumb_dir.$teachers['photo_img']))
	{
		$smarty->assign('resume_thumb_photo',$teachers['photo_img']);
	}
	$smarty->assign('act',$act);
	$smarty->assign('teachers',$teachers);
	$smarty->assign('teachers_photo',$teachers['photo_img']);
	$smarty->assign('pid',$_REQUEST['pid']);
	$smarty->assign('title','������Ƭ - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_train/train_teachers_photo_cutting.htm');
}
//����-������Ƭ
elseif ($act=='save_teachers_photo_cutting')
{
	$teachers=get_teachers_one(intval($_REQUEST['pid']),intval($_SESSION['uid']));
	if (empty($teachers)) showmsg("������д��ʦ������Ϣ��",0);
	require_once(QISHI_ROOT_PATH.'include/imageresize.class.php');
	$imgresize = new ImageResize();
	$photo_dir=QISHI_ROOT_PATH.substr($_CFG['teacher_photo_dir'],strlen($_CFG['site_dir']));
	$photo_thumb_dir=QISHI_ROOT_PATH.substr($_CFG['teacher_photo_dir_thumb'],strlen($_CFG['site_dir']));
	$imgresize->load($photo_dir.$teachers['photo_img']);
	$posary=explode(',', $_POST['cut_pos']);
	foreach($posary as $k=>$v) $posary[$k]=intval($v); 
	if($posary[2]>0 && $posary[3]>0) $imgresize->resize($posary[2], $posary[3]);
	$imgresize->cut(120,150, intval($posary[0]), intval($posary[1]));
	$imgresize->save($photo_thumb_dir.$teachers['photo_img']);
	header('Location: ?act=photo_cutting&show=ok&pid='.$_REQUEST['pid']);
}
elseif ($act=='edit_photo_display')
{
	header('Location: ?act=teachers_show&id='.intval($_REQUEST['pid']));
}

elseif ($act=='teachers_show')
{
	$teachers=get_teachers_one(intval($_GET['id']),$_SESSION['uid']);
	if (empty($teachers)) showmsg("��������",1);
	$smarty->assign('title','�޸Ľ�ʦ���� - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('teachers',$teachers);
	$smarty->display('member_train/train_teachersshow.htm');
}
elseif($act=='del_teachers')//ȷ���Ƿ�ɾ���γ�
{
	$yid =!empty($_POST['y_id'])?$_POST['y_id']:$_GET['y_id'];
	if($n=del_teachers($yid,$_SESSION['uid'])){
	showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}else{
	showmsg("ɾ��ʧ�ܣ�",0);
	}
}
unset($smarty);
?>