<?php
 /*
 * 74cms 分站
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/upload.php');
require_once(ADMIN_ROOT_PATH.'include/admin_category_fun.php');
require_once(ADMIN_ROOT_PATH.'include/admin_subsite_fun.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'set';
$smarty->assign('act',$act);
$smarty->assign('pageheader',"分站管理");
if($_CFG['subsite_id']>0){
	adminmsg('您没有管理权限！',0);
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
		!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k'")?adminmsg('更新站点设置失败', 1):"";
		}
		refresh_cache('config');
		adminmsg("保存成功！",2);
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
	//风格模板
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
	$setsqlarr['s_sitename']=!empty($_POST['s_sitename'])?trim($_POST['s_sitename']):adminmsg('请填写分站名称！',1);
	$setsqlarr['s_effective']=isset($_POST['s_effective'])?intval($_POST['s_effective']):1;
	$setsqlarr['s_district']=isset($_POST['s_district'])?intval($_POST['s_district']):adminmsg('请选择分站地区！',1);
	$setsqlarr['s_districtname']=!empty($_POST['s_districtname'])?trim($_POST['s_districtname']):adminmsg('请选择分站地区！',1);
	$setsqlarr['s_domain']=!empty($_POST['s_domain'])?trim($_POST['s_domain']):adminmsg('请填写域名！',1);
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
		adminmsg('上传图片出错！',1);
		}
	}
	$link[0]['text'] = "返回分站管理";
	$link[0]['href'] = '?act=list';
	if (!$db->inserttable(table('subsite'),$setsqlarr))
	{
	adminmsg("添加失败！",0);
	}
	else
	{
	refresh_subsite_cache();
	adminmsg("添加成功！",2,$link);	
	}
}
elseif ($act == 'edit')
{
	get_token();
	$id=intval($_GET['id']);	
	$subsite=get_subsite_one($id);
	$subsite['s_district']=explode('-',$subsite['s_district']);
	//风格模板
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
	$setsqlarr['s_sitename']=!empty($_POST['s_sitename'])?trim($_POST['s_sitename']):adminmsg('请填写分站名称！',1);
	$setsqlarr['s_effective']=isset($_POST['s_effective'])?intval($_POST['s_effective']):1;
	$setsqlarr['s_districtname']=!empty($_POST['s_districtname'])?trim($_POST['s_districtname']):adminmsg('请填写地区名称！',1);
	$setsqlarr['s_domain']=!empty($_POST['s_domain'])?trim($_POST['s_domain']):adminmsg('请填写域名！',1);
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
		adminmsg('上传图片出错！',1);
		}
	}
	$link[0]['text'] = "查看修改结果";
	$link[0]['href'] =  '?act=edit&id='.$s_id;
	$link[1]['text'] = "返回分站管理";
	$link[1]['href'] = '?act=list';
	if (!$db->updatetable(table('subsite'),$setsqlarr," s_id=".$s_id.""))
	{
	adminmsg("修改失败！",0);
	}
	else
	{
	refresh_subsite_cache();
	adminmsg("修改成功！",2,$link);
	}
}
elseif ($act == 'subsite_del')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_subsite($id))
	{
	refresh_subsite_cache();
	adminmsg("删除成功！共删除".$num."行",2);
	}
	else
	{
	adminmsg("删除失败！",0);
	}
}
?>