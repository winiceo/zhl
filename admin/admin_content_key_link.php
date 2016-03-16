<?php
 /*
 * 74cms ���Źؼ���
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
$smarty->assign('act',$act);
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"hotword");
$smarty->assign('pageheader',"���ݾۺϹؼ���");
if($act == 'list')
{	
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY id DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	if ($key)
	{
		$wheresql=" WHERE name like '%{$key}%'";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('content_key_link')." ".$wheresql;
	$page = new page(array('total'=>$db->get_total($total_sql),'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$hotword = get_content_key_link($offset, $perpage,$wheresql.$oederbysql);	
	$smarty->assign('hotword',$hotword);
	$smarty->assign('navlabel',"list");	
	$smarty->assign('page',$page->show(3));	
	$smarty->display('content_key_link/admin_content_key_link_list.htm');
}
elseif($act == 'add')
{
	get_token();
	$smarty->assign('navlabel',"add");	
	$smarty->display('content_key_link/admin_content_key_link_add.htm');
}
elseif($act == 'addsave')
{
	check_token();
	$setsqlarr['name']=trim($_POST['name'])?trim($_POST['name']):adminmsg('�ؼ��ʱ�����д��',1);
	$setsqlarr['url']=trim($_POST['url'])?trim($_POST['url']):adminmsg('�ؼ������ӱ�����д',1);
	$check=check_content_key_link($setsqlarr['name']);
	if (!empty($check))
	{
	adminmsg("�ؼ����Ѿ����ڣ�",0);
	}

	$link[0]['text'] = "�������";
	$link[0]['href'] = '?act=add';
	$link[1]['text'] = "�����б�";
	$link[1]['href'] = '?';
	write_log("������ݾۺϹؼ���", $_SESSION['admin_name'],3);
	!$db->inserttable(table('content_key_link'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):adminmsg("��ӳɹ���",2,$link);
}
elseif($act == 'edit')
{
	get_token();
	$smarty->assign('hotword',get_content_key_link_one($_GET['id']));
	$smarty->display('content_key_link/admin_content_key_link_edit.htm');
}
elseif($act == 'editsave')
{
	check_token();
	$id = !empty($_POST['id']) ? intval($_POST['id']) : adminmsg('��������',1);
	$setsqlarr['name']=trim($_POST['name'])?trim($_POST['name']):adminmsg('�ؼ��ʱ�����д��',1);
	$setsqlarr['url']=trim($_POST['url'])?trim($_POST['url']):adminmsg('�ؼ������ӱ�����д',1);
	$check=check_content_key_link($setsqlarr['name']);
	if (!empty($check))
	{
	adminmsg("�ؼ����Ѿ����ڣ�",0);
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?';
	write_log("�޸����ݾۺϹؼ���", $_SESSION['admin_name'],3);
 	!$db->updatetable(table('content_key_link'),$setsqlarr," id=".$id."")?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
}
elseif($act == 'content_key_link_del')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_content_key_link($id))
	{
	write_log("ɾ���������ݾۺϹؼ���,��ɾ�� {$num} ��", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�����ɾ�� {$num} ��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'set')
{
	get_token();
	$smarty->assign('pageheader',"���ݾۺϹؼ���");
	$smarty->assign('config',get_cache('config'));
	$smarty->assign('navlabel',"set");	
	$smarty->display('content_key_link/admin_content_key_link_set.htm');
}
elseif($act=='setsave')
{
	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='{$v}' WHERE name='{$k}'")?adminmsg('����վ������ʧ��', 1):"";
	}
	refresh_cache('config');
	//��д����Ա��־
	write_log("��̨�ɹ�������վ����", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2);
}	
?>