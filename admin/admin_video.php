<?php
 /*
 * 74cms ����ҳ��
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.zhaohulu.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_article_fun.php');
require_once(ADMIN_ROOT_PATH.'include/upload.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'videolist';
$smarty->assign('act',$act);
if($act == 'videolist')
{
	check_permissions($_SESSION['admin_purview'],"video_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$video_sql="select * from ".table('video')." order by addtime desc";
	$video_res=$db->getall($video_sql);
	$smarty->assign('video',$video_res);
	$smarty->assign('pageheader',"��Ƶ�б�");
	get_token();
	$smarty->display('video/admin_video.htm');
}
elseif($act == 'video_add')
{
	check_permissions($_SESSION['admin_purview'],"video_add");
	$smarty->assign('subsite',get_subsite_list(intval($_CFG['subsite_id'])));
	$smarty->assign('author',$_SESSION['admin_name']);
	$smarty->assign('pageheader',"�����Ƶ");
	get_token();
	$smarty->display('video/admin_video_add.htm');
}
elseif($act == 'addsave')
{
	
	check_permissions($_SESSION['admin_purview'],"video_add");
	check_token();
	$setsqlarr['u_name']=!empty($_POST['u_name'])?trim($_POST['u_name']):adminmsg('��û����д�����ߣ�',1);
	!$_FILES['video_img']['name']?adminmsg('���ϴ�ͼƬ��',1):"";
	$datedir=date("Y/m/d/");
	$up_dir="../../data/companyimg/original/".$datedir;
	make_dir($up_dir);
	$setsqlarr['video_img']=_asUpFiles($up_dir,"video_img",800,'gif/jpg/bmp/png',true);
	if ($setsqlarr['video_img'])
	{
			$img_src=$up_dir.$setsqlarr['video_img'];
			$thumb_dir="../../data/companyimg/thumb/".$datedir;
			make_dir($thumb_dir);
			makethumb($img_src,$up_dir,325,325);
			makethumb($img_src,$thumb_dir,175,175);
			$setsqlarr['video_img']=$datedir.$setsqlarr['video_img'];
	}
	
	$setsqlarr['video_img']=!empty($setsqlarr['video_img'])?trim($setsqlarr['video_img']):adminmsg('��û���ϴ�����ͼƬ��',1);
	$setsqlarr['video_url']=!empty($_POST['video_url'])?trim($_POST['video_url']):adminmsg('��û����д��Ƶ���ӣ�',1);
	$setsqlarr['video_name']=!empty($_POST['video_name'])?trim($_POST['video_name']):adminmsg('��û����д��Ƶ���ƣ�',1);
	$setsqlarr['video_describe']=!empty($_POST['content'])?trim($_POST['content']):adminmsg('��û����д��Ƶ������',1);
	$setsqlarr['addtime']=time();
	$link[0]['text'] = "���������Ƶ";
	$link[0]['href'] = '?act=video_add';
	$link[1]['text'] = "������Ƶ�б�";
	$link[1]['href'] = '?act=videolist';
	write_log("�����Ƶ��".$setsqlarr['video_name'], $_SESSION['admin_name'],3);
	$insertid = $db->inserttable(table('video'),$setsqlarr,1);
	if(!$insertid){
		adminmsg("���ʧ�ܣ�",0);
	}else{
		adminmsg("��ӳɹ���",2,$link);
	}
}
elseif($act == 'video_edit')
{
	check_permissions($_SESSION['admin_purview'],"video_edit");
	$id=intval($_GET['id']);
	$sql = "select * from ".table('video')." where id=".intval($id)." LIMIT 1";
	$edit_video=$db->getone($sql);
	$smarty->assign('edit_video',$edit_video); 
	$smarty->assign('subsite',get_subsite_list(intval($_CFG['subsite_id'])));
	$smarty->assign('pageheader',"���»ع�");
	get_token();
	$smarty->display('video/admin_video_edit.htm');
}
elseif($act == 'editsave')
{
	check_permissions($_SESSION['admin_purview'],"video_edit");
	check_token();
	$id=$_POST['id'];
	$setsqlarr['u_name']=!empty($_POST['u_name'])?trim($_POST['u_name']):adminmsg('��û����д�����ߣ�',1);
	if($_FILES['video_img']['name'])
	{
		!$_FILES['video_img']['name']?adminmsg('���ϴ�ͼƬ��',1):"";
		$datedir=date("Y/m/d/");
		$up_dir="../../data/companyimg/original/".$datedir;
		make_dir($up_dir);
		$setsqlarr['video_img']=_asUpFiles($up_dir,"video_img",800,'gif/jpg/bmp/png',true);
		if ($setsqlarr['video_img'])
		{
				$img_src=$up_dir.$setsqlarr['video_img'];
				$thumb_dir="../../data/companyimg/thumb/".$datedir;
				make_dir($thumb_dir);
				makethumb($img_src,$up_dir,325,325);
				makethumb($img_src,$thumb_dir,175,175);
				$setsqlarr['video_img']=$datedir.$setsqlarr['video_img'];
		}
	}else
	{
		$setsqlarr['video_img']=$_POST['video_imgs'];
	}	
	$setsqlarr['video_img']=!empty($setsqlarr['video_img'])?trim($setsqlarr['video_img']):adminmsg('��û���ϴ�����ͼƬ��',1);
	$setsqlarr['video_url']=!empty($_POST['video_url'])?trim($_POST['video_url']):adminmsg('��û����д��Ƶ���ӣ�',1);
	$setsqlarr['video_name']=!empty($_POST['video_name'])?trim($_POST['video_name']):adminmsg('��û����д��Ƶ���ƣ�',1);
	$setsqlarr['video_describe']=!empty($_POST['content'])?trim($_POST['content']):adminmsg('��û����д��Ƶ������',1);
	$setsqlarr['addtime']=time();
	$link[0]['text'] = "���������б�";
	$link[0]['href'] = '?act=videolist';
	$link[1]['text'] = "�鿴���޸�����";
	$link[1]['href'] = "?act=video_edit&id=".$id;
	write_log("�޸�idΪ".$id."����Ƶ��Ϣ", $_SESSION['admin_name'],3);
	$pid=$db->updatetable(table('video'),$setsqlarr," id=".$id."");
	if(!$pid){
		adminmsg("�޸�ʧ�ܣ�",0);
	}else{
		adminmsg("�޸ĳɹ���",2,$link);
	}
}
elseif($act == 'video_del')
{
	check_token();
	$id=intval($_GET['id']);
	$sql="delete from ".table('video')." where id=".$id;
	$db->query($sql);
	write_log("ɾ��idΪ".$id."����Ƶ��Ϣ", $_SESSION['admin_name'],3);
	adminmsg("ɾ����Ƶ�ɹ���",2);
}
?>