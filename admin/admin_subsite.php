<?php
 /*
 * 74cms ��վ
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/upload.php');
require_once(ADMIN_ROOT_PATH.'include/admin_category_fun.php');
require_once(ADMIN_ROOT_PATH.'include/admin_subsite_fun.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'set';
$smarty->assign('act',$act);
$smarty->assign('pageheader',"��վ����");
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"subsite");
if($act == 'set')
{	
	get_token();
	$smarty->assign('config',$_CFG);
	$smarty->assign('navlabel',"set");
	$smarty->display('subsite/admin_subsite_set.htm');
}
elseif($act == 'setsave')
{
		check_token();
		foreach($_POST as $k => $v)
		{
		!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k'")?adminmsg('����վ������ʧ��', 1):"";
		}
		refresh_cache('config');
		adminmsg("����ɹ���",2);
}
elseif($act == 'list')
{	

	get_token();
	$smarty->assign('list',get_subsite_list());
	$smarty->assign('navlabel',"list");
	$smarty->display('subsite/admin_subsite_list.htm');
}
elseif($act == 'add')
{		
	get_token();
	//���ģ��
	$dirs = getsubdirs('../templates');
	unset($dirs[array_search(".svn",$dirs)]);
	unset($dirs[array_search("tpl_company",$dirs)]);
	unset($dirs[array_search("tpl_resume",$dirs)]);
	unset($dirs[array_search("tpl_hunter",$dirs)]);
	unset($dirs[array_search("tpl_train",$dirs)]);
	unset($dirs[array_search("tpl_shop",$dirs)]);
	unset($dirs[array_search("tpl_campus",$dirs)]);
	unset($dirs[array_search("tpl_evaluation",$dirs)]);
	$smarty->assign('dirs',$dirs);
	$smarty->assign('district',get_category_district());
	$smarty->assign('navlabel',"add");
	$smarty->display('subsite/admin_subsite_add.htm');
}
elseif($act == 'add_save')
{	
	check_token();
	$setsqlarr['s_sitename']=!empty($_POST['s_sitename'])?trim($_POST['s_sitename']):adminmsg('����д��վ���ƣ�',1);
	$setsqlarr['s_effective']=isset($_POST['s_effective'])?intval($_POST['s_effective']):1;
	$setsqlarr['s_district']=isset($_POST['s_district'])?intval($_POST['s_district']):adminmsg('��ѡ���վ������',1);
	$setsqlarr['s_districtname']=!empty($_POST['s_districtname'])?trim($_POST['s_districtname']):adminmsg('��ѡ���վ������',1);
	$setsqlarr['s_domain']=!empty($_POST['s_domain'])?trim($_POST['s_domain']):adminmsg('����д������',1);
	$setsqlarr['s_m_domain']=trim($_POST['s_m_domain']);
	$setsqlarr['s_tpl']=!empty($_POST['s_tpl'])?trim($_POST['s_tpl']):'';
	$setsqlarr['s_order']=intval($_POST['s_order']);
	$setsqlarr['s_index']=getfirstchar($setsqlarr['s_sitename']);
	$setsqlarr['s_title']=trim($_POST['s_title']);
	$setsqlarr['s_keywords']=trim($_POST['s_keywords']);
	$setsqlarr['s_description']=trim($_POST['s_description']);
	if ( $_FILES['s_logo']['name'])
	{
		$setsqlarr['s_logo']=_asUpFiles($upfiles_dir, "s_logo", 1024*2, 'jpg/gif/png',"logo".md5($setsqlarr['s_domain']));
		if (empty($setsqlarr['s_logo']))
		{
		adminmsg('�ϴ�ͼƬ����',1);
		}
	}
	$link[0]['text'] = "���ط�վ����";
	$link[0]['href'] = '?act=list';
	if (!$db->inserttable(table('subsite'),$setsqlarr))
	{
	adminmsg("���ʧ�ܣ�",0);
	}
	else
	{
	refresh_subsite_cache();
	adminmsg("��ӳɹ���",2,$link);	
	}
}
elseif ($act == 'edit')
{
	get_token();
	$id=intval($_GET['id']);	
	$subsite=get_subsite_one($id);
	$subsite['s_district']=explode('-',$subsite['s_district']);
	//���ģ��
	$dirs = getsubdirs('../templates');
	unset($dirs[array_search(".svn",$dirs)]);
	unset($dirs[array_search("tpl_company",$dirs)]);
	unset($dirs[array_search("tpl_resume",$dirs)]);
	unset($dirs[array_search("tpl_hunter",$dirs)]);
	unset($dirs[array_search("tpl_train",$dirs)]);
	unset($dirs[array_search("tpl_shop",$dirs)]);
	unset($dirs[array_search("tpl_campus",$dirs)]);
	unset($dirs[array_search("tpl_evaluation",$dirs)]);
	$smarty->assign('dirs',$dirs);
	$smarty->assign('district',get_category_district());
	$smarty->assign('subsite',$subsite);
	$smarty->assign('rand',rand(1,100));
	$smarty->assign('upfiles_dir',$upfiles_dir);
	$smarty->display('subsite/admin_subsite_edit.htm');
}
elseif ($act == 'edit_save')
{
	check_token();
	$s_id=intval($_POST['s_id']);
	$setsqlarr['s_sitename']=!empty($_POST['s_sitename'])?trim($_POST['s_sitename']):adminmsg('����д��վ���ƣ�',1);
	$setsqlarr['s_effective']=isset($_POST['s_effective'])?intval($_POST['s_effective']):1;
	$setsqlarr['s_districtname']=!empty($_POST['s_districtname'])?trim($_POST['s_districtname']):adminmsg('����д�������ƣ�',1);
	$setsqlarr['s_domain']=!empty($_POST['s_domain'])?trim($_POST['s_domain']):adminmsg('����д������',1);
	$setsqlarr['s_m_domain']=trim($_POST['s_m_domain']);
	$setsqlarr['s_tpl']=!empty($_POST['s_tpl'])?trim($_POST['s_tpl']):'';
	$setsqlarr['s_order']=intval($_POST['s_order']);
	$setsqlarr['s_index']=getfirstchar($setsqlarr['s_sitename']);
	$setsqlarr['s_title']=trim($_POST['s_title']);
	$setsqlarr['s_keywords']=trim($_POST['s_keywords']);
	$setsqlarr['s_description']=trim($_POST['s_description']);

	if ( $_FILES['s_logo']['name'])
	{
		$setsqlarr['s_logo']=_asUpFiles($upfiles_dir, "s_logo", 1024*2, 'jpg/gif/png',"logo".md5($setsqlarr['s_domain']));
		if (empty($setsqlarr['s_logo']))
		{
		adminmsg('�ϴ�ͼƬ����',1);
		}
	}
	$link[0]['text'] = "�鿴�޸Ľ��";
	$link[0]['href'] =  '?act=edit&id='.$s_id;
	$link[1]['text'] = "���ط�վ����";
	$link[1]['href'] = '?act=list';
	if (!$db->updatetable(table('subsite'),$setsqlarr," s_id=".$s_id.""))
	{
	adminmsg("�޸�ʧ�ܣ�",0);
	}
	else
	{
	refresh_subsite_cache();
	adminmsg("�޸ĳɹ���",2,$link);
	}
}
elseif ($act == 'subsite_del')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_subsite($id))
	{
	refresh_subsite_cache();
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
?>