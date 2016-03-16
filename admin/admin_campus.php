<?php
 /*
 * 74cms ϵͳ����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_campus_fun.php');
require_once(ADMIN_ROOT_PATH.'include/upload.php');
$campus_updir="../data/campus/logo/";
$campus_dir=$_CFG['site_dir']."data/campus/logo/";
$campus_img_updir="../data/campus/img/";
$campus_img_dir=$_CFG['site_dir']."data/campus/img/";
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'campus_list';
$config = get_cache('config');
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"set_campus");
//����ԺУ
if($act == 'campus_list')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	//���½�����
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if($key_type===1)$wheresql=" WHERE campusname like '%{$key}%'";
	}
	else
	{
		//���ʱ��
		$settr=$_GET['settr'];
		if ($settr<>"")
		{
			$settr=strtotime("-".intval($_GET['settr'])." day");
			$wheresql.=" WHERE addtime> ".$settr;
		}
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('cooperate_campus').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('list',get_cooperate_campus_list($offset,$perpage,$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('total',$total_val);
	$smarty->assign('navlabel',"campus_list");
	$smarty->assign('campus_dir',$campus_dir);
	$smarty->assign('pageheader',"����ԺУ");
	$smarty->display('campus/admin_cooperate_campus_list.htm');
}
//���ԺУ
elseif($act == 'campus_add')
{
	get_token();
	$smarty->assign('navlabel',"campus_add");
	$smarty->assign('pageheader',"����ԺУ");
	$smarty->display('campus/admin_cooperate_campus_add.htm');
}
//�������ԺУ
elseif($act == 'campus_add_save')
{
	check_token();
	$setsqlarr['campusname']=trim($_POST['campusname'])?trim($_POST['campusname']):adminmsg('��û����дԺУ���ƣ�',1);
	$setsqlarr['website']=trim($_POST['website'])?trim($_POST['website']):adminmsg('��û����дԺУ��ҳ��',1);
	$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):adminmsg('��û����дԺУ��ַ��',1);
	$setsqlarr['contents']=trim($_POST['contents'])?trim($_POST['contents']):adminmsg('��û����дԺУ��飡',1);
	//LOGO
	if (empty($_FILES['logo']['name']))
	{
		adminmsg('���ϴ�ͼƬ��',1);
	}
	else
	{
		$campus_updir=$campus_updir.date("Y/m/d/");
		make_dir($campus_updir);
		$setsqlarr['logo']=_asUpFiles($campus_updir,"logo",1000,'gif/jpg/bmp/png',true);
		if (empty($setsqlarr['logo']))
		{
			adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
		}
		$setsqlarr['logo']=date("Y/m/d/").$setsqlarr['logo'];
	}
	$setsqlarr['addtime']=$timestamp;
	$link[0]['text'] = "�������";
	$link[0]['href'] ="?act=campus_add";
	$link[1]['text'] = "����ԺУ�б�";
	$link[1]['href'] ="?act=campus_list";
	$ad_id = $db->inserttable(table('cooperate_campus'),$setsqlarr,true);
	if($ad_id < 0)
	{
		//��д����Ա��־
		write_log("��̨��Ӻ���ԺУʧ��", $_SESSION['admin_name'],3);
		adminmsg("���ʧ�ܣ�",0);
	}
	else
	{
		//����ԺУͼƬ
		if (!empty($_FILES['image']))
		{
			foreach ($_FILES['image']['name'] as $key => $value) 
			{
				$img_path='';
				$name=explode('.',$value);//���ļ�����'.'�ָ�õ���׺��,�õ�һ������
				$img_path=$campus_img_updir.date("Y/m/d/");
				make_dir($img_path);
				$qid=uniqid();
				$img_path = $img_path.$qid.".".$name[1];
				if(move_uploaded_file($_FILES['image']['tmp_name'][$key], $img_path))
				{
					$imgarr=array();
					$imgarr['img']=date("Y/m/d/").$qid.".".$name[1];
					$imgarr['addtime'] = $timestamp;
					$imgarr['campus_id'] = $ad_id;
					$db->inserttable(table('cooperate_campus_img'),$imgarr);
				}
			}
		}
		else
		{
			adminmsg('���ϴ����ͼƬ��',1);
		}
		//��д����Ա��־
		write_log("��̨�ɹ���Ӻ���ԺУ", $_SESSION['admin_name'],3);
		baidu_submiturl(url_rewrite('QS_campusshow',array('id'=>$ad_id)),'addcampus');
		adminmsg("��ӳɹ���",2,$link);
	}
}
//ɾ�����ͼƬ
elseif($act == 'del_img')
{
	$id = !empty($_GET['id'])?intval($_GET['id']):adminmsg("���ͼƬid��ʧ��",0);
	$campus_id = !empty($_GET['c_id'])?intval($_GET['c_id']):adminmsg("ԺУid��ʧ��",0);
	$link[0]['text'] = "����ɾ��";
	$link[0]['href'] ="?act=edit_campus&id={$campus_id}";
	$link[1]['text'] = "����ԺУ�б�";
	$link[1]['href'] ="?act=campus_list";
	if(del_campus_img($id,$campus_id))
	{
		adminmsg("ɾ���ɹ���",2,$link);
	}
	else
	{
		adminmsg("ɾ��ʧ�ܣ�",0,$link);
	}
}
//�޸�ԺУ
elseif($act == 'edit_campus')
{
	get_token();
	$id=!empty($_GET['id'])?intval($_GET['id']):adminmsg('ԺУid��ʧ��',1);
	$campus = get_campus_one($id);
	//���ͼƬ
	$campus_img = get_campus_img($id);
	$smarty->assign('rand',rand(1,100));
	$smarty->assign('campus_img',$campus_img);
	$smarty->assign('campus',$campus);
	$smarty->assign('campus_img_dir',$campus_img_dir);
	$smarty->assign('campus_dir',$campus_dir);
	$smarty->assign('pageheader',"����ԺУ");
	$smarty->display('campus/admin_cooperate_campus_edit.htm');
}
//����:�޸�ԺУ
elseif($act == 'edit_campus_save')
{
	check_token();
	$id=intval($_POST['id']);
	$setsqlarr['campusname']=trim($_POST['campusname'])?trim($_POST['campusname']):adminmsg('��û����дԺУ���ƣ�',1);
	$setsqlarr['website']=trim($_POST['website'])?trim($_POST['website']):adminmsg('��û����дԺУ��ҳ��',1);
	$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):adminmsg('��û����дԺУ��ַ��',1);
	$setsqlarr['contents']=trim($_POST['contents'])?trim($_POST['contents']):adminmsg('��û����дԺУ��飡',1);
	//LOGO
	if (!empty($_FILES['logo']['name']))
	{
		$campus_updir=$campus_updir.date("Y/m/d/");
		make_dir($campus_updir);
		$setsqlarr['logo']=_asUpFiles($campus_updir,"logo",1000,'gif/jpg/bmp/png',true);
		if (empty($setsqlarr['logo']))
		{
			adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
		}
		$setsqlarr['logo']=date("Y/m/d/").$setsqlarr['logo'];
	}
	//����ԺУͼƬ
	foreach ($_FILES['image']['name'] as $key => $value) 
	{
		if(empty($value))
		{
			continue;
		}
		$img_path='';
		$name=explode('.',$value);//���ļ�����'.'�ָ�õ���׺��,�õ�һ������
		$img_path=$campus_img_updir.date("Y/m/d/");
		make_dir($img_path);
		$qid=uniqid();
		$img_path = $img_path.$qid.".".$name[1];
		if(move_uploaded_file($_FILES['image']['tmp_name'][$key], $img_path))
		{
			$imgarr=array();
			$imgarr['img']=date("Y/m/d/").$qid.".".$name[1];
			$imgarr['addtime'] = $timestamp;
			$imgarr['campus_id'] = $id;
			$db->inserttable(table('cooperate_campus_img'),$imgarr);
		}
	}

	$wheresql=" id='".$id."' ";
	if(!$db->updatetable(table('cooperate_campus'),$setsqlarr,$wheresql))
	{
		//��д����Ա��־
		write_log("��̨�޸ĺ���ԺУʧ��", $_SESSION['admin_name'],3);
		adminmsg("�޸�ʧ�ܣ�",0);
	}
	else
	{
		$link[0]['text'] = "����ԺУ�б�";
		$link[0]['href'] = "?act=campus_list";
		//��д����Ա��־
		write_log("��̨�ɹ��޸ĺ���ԺУ", $_SESSION['admin_name'],3);
		adminmsg("�޸ĳɹ���",2,$link);
	}
}
//ɾ������ԺУ
elseif($act=='del_campus')
{
	$id=$_REQUEST['id'];
	check_token();
	if (empty($id)) adminmsg("��ѡ����Ŀ��",0);
	if ($num=del_campus($id))
	{
		adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
		adminmsg("ɾ��ʧ�ܣ�".$num,1);
	}
}
?>