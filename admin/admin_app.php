<?php
 /*
 * 74cms ϵͳ����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_app_fun.php');
require_once(ADMIN_ROOT_PATH.'include/upload.php');
$ads_updir="../data/appads/";
$ads_dir=$_CFG['site_dir']."data/appads/";
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set';
$smarty->assign('pageheader',"APP����");
$config = get_cache('config');
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
//��������
if($act == 'set')
{	
	get_token();
	check_permissions($_SESSION['admin_purview'],"set_app");
	$smarty->assign('config',$config);
	$smarty->assign('navlabel',"set");	
	$smarty->display('app/admin_set_app.htm');
}
elseif($act == 'set_save')
{
	check_token();
	foreach($_POST as $k => $v)
	{
		!$db->query("UPDATE ".table('config')." SET value='{$v}' WHERE name='{$k}'")?adminmsg('����APP����ʧ��', 1):"";
	}
	refresh_cache('config');
	//��д����Ա��־
	write_log("��̨�ɹ�����APP����", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2);
}
//������
elseif($act == 'ad_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"set_app_ad");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	//���½�����
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if($key_type===1)$wheresql=" WHERE a.title like '%{$key}%'";
	}
	else
	{
		$category_id=isset($_GET['category_id'])?intval($_GET['category_id']):"";
		if ($category_id>0)
		{
		$wheresql=empty($wheresql)?" WHERE a.category_id= ".$category_id:$wheresql." AND a.category_id= ".$category_id;
		}
		$settr=$_GET['settr'];
		if ($settr<>"")
		{
			$wheresql.=empty($wheresql)?" WHERE ":" AND  ";
			$days=intval($settr);
			$settr=strtotime($days." day");
			if ($days===0)
			{
			$wheresql.=" a.deadline< ".time()." AND a.deadline>0 ";
			}
			else
			{
			$wheresql.=" a.deadline< ".$settr." AND  a.deadline>".time()." ";
			}		
		}
		$is_display=isset($_GET['is_display'])?$_GET['is_display']:"";
		if ($is_display<>'')
		{
		$is_display=intval($is_display);
		$wheresql=empty($wheresql)?" WHERE a.is_display= ".$is_display:$wheresql." AND a.is_display= ".$is_display;
		}
	}
	$joinsql=" LEFT JOIN  ".table('ad_app_category')." AS c ON  a.category_id=c.id ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('ad_app')." AS a " .$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('list',get_ad_list($offset,$perpage,$joinsql.$wheresql));
	$smarty->assign('ad_app_category',get_ad_app_category());
	$smarty->assign('page',$page->show(3));
	$smarty->assign('total',$total_val);
	$smarty->assign('navlabel',"ad_list");
	$smarty->display('app/admin_ad_app_list.htm');
}
//��ӹ��
elseif($act == 'ad_add')
{
	$smarty->assign('datefm',convert_datefm(time(),1));
	$smarty->assign('ad_app_category',get_ad_app_category());
	$smarty->assign('navlabel',"ad_list");
	get_token();
	$smarty->display('app/admin_ad_app_add.htm');
}
//������ӹ��
elseif($act == 'ad_add_save')
{
	check_token();
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):adminmsg('��û����д���⣡',1);
	$setsqlarr['is_display']=trim($_POST['is_display'])?trim($_POST['is_display']):0;
	$setsqlarr['category_id']=trim($_POST['category_id'])?trim($_POST['category_id']):adminmsg('��û��ѡ������࣡',1);
	$setsqlarr['type_id']=trim($_POST['type_id'])?trim($_POST['type_id']):adminmsg('��û��ѡ�������ͣ�',1);
	$setsqlarr['alias']=trim($_POST['alias'])?trim($_POST['alias']):adminmsg('�������󣬵���ID�����ڣ�',1);
	$setsqlarr['show_order']=intval($_POST['show_order']);
	$setsqlarr['note']=trim($_POST['note']);	
	if ($_POST['starttime']=="")
	{
		$setsqlarr['starttime']=0;
	}
	else
	{
		$setsqlarr['starttime']=intval(convert_datefm($_POST['starttime'],2));
	}
	if($_POST['deadline']=="")
	{
		$setsqlarr['deadline']=0;
	}
	else
	{
		$setsqlarr['deadline']=intval(convert_datefm($_POST['deadline'],2));
	}
	//ͼƬ
	if($setsqlarr['type_id']=="2")
	{
		if (empty($_FILES['img_file']['name']) && empty($_POST['img_path']))
		{
			adminmsg('���ϴ�ͼƬ������дͼƬ·����',1);
		}
		if ($_FILES['img_file']['name'])
		{
			$ads_updir=$ads_updir.date("Y/m/d/");
			make_dir($ads_updir);
			$setsqlarr['img_path']=_asUpFiles($ads_updir,"img_file",1000,'gif/jpg/bmp/png',true);
			if (empty($setsqlarr['img_path']))
			{
				adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
			}
			$setsqlarr['img_path']=$ads_dir.date("Y/m/d/").$setsqlarr['img_path'];
		}
		else
		{
			$setsqlarr['img_path']=trim($_POST['img_path']);
		}
		$setsqlarr['img_url']=trim($_POST['img_url']);
		$setsqlarr['img_explain']=trim($_POST['img_explain']);
		$setsqlarr['img_uid']=intval($_POST['img_uid']);
	}
	else
	{
		adminmsg('������ʹ���',1);
	}
	$setsqlarr['addtime']=$timestamp;
	$link[0]['text'] = "�������";
	$link[0]['href'] ="?act=ad_add&category_id=".$_POST['category_id']."&type_id=".$_POST['type_id']."&alias=".$_POST['alias'];
	$link[1]['text'] = "���ع���б�";
	$link[1]['href'] ="?act=ad_list";
	$ad_id = $db->inserttable(table('ad_app'),$setsqlarr,true);
	if($ad_id < 0)
	{
		//��д����Ա��־
		write_log("��̨���APP���ʧ��", $_SESSION['admin_name'],3);
		adminmsg("���ʧ�ܣ�",0);
	}
	else
	{
		//����ӵĻ�ӭҳ���,���轫֮ǰ������Ϊ����
		if($setsqlarr['category_id']=='2')
		{
			$db->updatetable(table('ad_app'),array('is_display'=>0)," id!={$ad_id} and category_id=2 ");
		}
		//����ӵ�����ҳ�ַ����,����������ʾ�Ĵ�����5��,���轫�����ϴ�������Ϊ����
		if($setsqlarr['category_id']=='1')
		{
			//ͳ��������ʾ����ҳ�ַ����
			$num_add_index = $db->get_total("SELECT COUNT(*) AS num FROM ".table('ad_app')." WHERE is_display=1 AND category_id=1 ");
			if($num_add_index > 5)
			{
				$db->updatetable(table('ad_app'),array('is_display'=>0)," id!={$ad_id} and is_display=1 and category_id=1 order by show_order asc,id asc limit 1 ");
			}
		}
		//��д����Ա��־
		write_log("��̨�ɹ����APP���", $_SESSION['admin_name'],3);
		adminmsg("��ӳɹ���",2,$link);
	}
}
//�޸Ĺ��
elseif($act == 'edit_ad')
{
	get_token();
	$id=!empty($_GET['id'])?intval($_GET['id']):adminmsg('û�й��id��',1);
	$ad=get_ad_one($id);
	$smarty->assign('ad',$ad);
	$smarty->assign('ad_app_category',get_ad_app_category());//���λ�����б�
	$smarty->assign('url',$_SERVER['HTTP_REFERER']);
	$smarty->display('app/admin_ad_app_edit.htm');
}

//����:�޸Ĺ��
elseif($act == 'ad_edit_save')
{
	check_token();
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):adminmsg('��û����д���⣡',1);
	$setsqlarr['is_display']=trim($_POST['is_display'])?trim($_POST['is_display']):0;
	$setsqlarr['category_id']=trim($_POST['category_id'])?trim($_POST['category_id']):adminmsg('��û����д�����࣡',1);
	$setsqlarr['type_id']=trim($_POST['type_id'])?trim($_POST['type_id']):adminmsg('��û����д������ͣ�',1);
	$setsqlarr['alias']=trim($_POST['alias'])?trim($_POST['alias']):adminmsg('�������󣬵���ID�����ڣ�',1);
	$setsqlarr['show_order']=intval($_POST['show_order']);
	$setsqlarr['note']=trim($_POST['note']);	
	if ($_POST['starttime']=="")
	{
		$setsqlarr['starttime']=0;
	}
	else
	{
		$setsqlarr['starttime']=intval(convert_datefm($_POST['starttime'],2));
	}
	if ($_POST['deadline']=="")
	{
		$setsqlarr['deadline']=0;
	}
	else
	{
		$setsqlarr['deadline']=intval(convert_datefm($_POST['deadline'],2));
	}
	//ͼƬ
	if ($setsqlarr['type_id']=="2")
	{
		if (empty($_FILES['img_file']['name']) && empty($_POST['img_path']))
		{
		adminmsg('���ϴ�ͼƬ������дͼƬ·����',1);
		}
		if ($_FILES['img_file']['name'])
		{
			$ads_updir=$ads_updir.date("Y/m/d/");
			make_dir($ads_updir);
			$setsqlarr['img_path']=_asUpFiles($ads_updir,"img_file",1000,'gif/jpg/bmp/png',true);
			if (empty($setsqlarr['img_path']))
			{
				adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
			}
			$setsqlarr['img_path']=$ads_dir.date("Y/m/d/").$setsqlarr['img_path'];
		}
		else
		{
			$setsqlarr['img_path']=trim($_POST['img_path']);
		}
		$setsqlarr['img_url']=trim($_POST['img_url']);
		$setsqlarr['img_explain']=trim($_POST['img_explain']);
		$setsqlarr['img_uid']=intval($_POST['img_uid']);
	}
	else
	{
		adminmsg('������ʹ���',1);
	}
	$setsqlarr['addtime']=$timestamp;
	$link[0]['text'] = "�����б�";
	$link[0]['href'] =trim($_POST['url']);
	$wheresql=" id='".intval($_POST['id'])."' "; 
	if(!$db->updatetable(table('ad_app'),$setsqlarr,$wheresql))
	{
		//��д����Ա��־
		write_log("��̨�޸Ĺ��ʧ��", $_SESSION['admin_name'],3);
		adminmsg("�޸�ʧ�ܣ�",0);
	}
	else
	{
		//���޸ĵ��ǻ�ӭҳ���,�������޸�Ϊ����,���轫֮ǰ������Ϊ����
		if($setsqlarr['category_id']=='2' && $setsqlarr['is_display']=='1')
		{
			$db->updatetable(table('ad_app'),array('is_display'=>0)," id!={$_POST['id']} and category_id=2 ");
		}
		//���޸ĵ�����ҳ�ַ����,����������ʾ�Ĵ�����5��,���轫�����ϴ�������Ϊ����
		if($setsqlarr['category_id']=='1' && $setsqlarr['is_display']=='1')
		{
			//ͳ��������ʾ����ҳ�ַ����
			$num_add_index = $db->get_total("SELECT COUNT(*) AS num FROM ".table('ad_app')." WHERE is_display=1 AND category_id=1 ");
			if($num_add_index > 5)
			{
				$db->updatetable(table('ad_app'),array('is_display'=>0)," id!={$_POST['id']} and is_display=1 and category_id=1 order by show_order asc,id asc limit 1 ");
			}
		}
		//��д����Ա��־
		write_log("��̨�޸Ĺ��ɹ�", $_SESSION['admin_name'],3);
		adminmsg("�޸ĳɹ���",2,$link);
	}
}
//ɾ�����
elseif($act=='del_ad')
{
	$id=$_REQUEST['id'];
	check_token();
	if (empty($id)) adminmsg("��ѡ����Ŀ��",0);
	if ($num=del_ad($id))
	{
		adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
		adminmsg("ɾ��ʧ�ܣ�".$num,1);
	}
}

?>