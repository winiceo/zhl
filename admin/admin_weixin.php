<?php
 /*
 * 74cms ΢�Ź���ƽ̨
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_weixin_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set_weixin';
$smarty->assign('act',$act);
$smarty->assign('navlabel',$act);
$smarty->assign('pageheader',"΢�Ź���ƽ̨");	
if($act == 'set_weixin')
{
	check_permissions($_SESSION['admin_purview'],"set_weixinconnect");	
	get_token();
	$smarty->assign('rand',rand(1,100));
	$smarty->assign('upfiles_dir',$upfiles_dir);	
	$smarty->assign('config',$_CFG);
	$smarty->display('weixin/admin_weixin.htm');
}
elseif($act == 'set_weixin_save')
{
	check_permissions($_SESSION['admin_purview'],"set_weixinconnect");	
	check_token();
	require_once(ADMIN_ROOT_PATH.'include/upload.php');
	if($_FILES['weixin_img']['name'])
	{
	$weixin_img=_asUpFiles($upfiles_dir, "weixin_img", 1024*2, 'jpg/gif/png',"weixin_img");
	!$db->query("UPDATE ".table('config')." SET value='$weixin_img' WHERE name='weixin_img'")?adminmsg('����վ������ʧ��', 1):"";
	}
	if($_FILES['weixin_first_pic']['name'])
	{
	$weixin_first_pic=_asUpFiles($upfiles_dir, "weixin_first_pic", 1024*2, 'jpg/gif/png',"weixin_first_pic");
	!$db->query("UPDATE ".table('config')." SET value='$weixin_first_pic' WHERE name='weixin_first_pic'")?adminmsg('����վ������ʧ��', 1):"";
	}
	if($_FILES['weixin_default_pic']['name'])
	{
	$weixin_default_pic=_asUpFiles($upfiles_dir, "weixin_default_pic", 1024*2, 'jpg/gif/png',"weixin_default_pic");
	!$db->query("UPDATE ".table('config')." SET value='$weixin_default_pic' WHERE name='weixin_default_pic'")?adminmsg('����վ������ʧ��', 1):"";
	}
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='{$v}' WHERE name='{$k}'")?adminmsg('����վ������ʧ��', 1):"";
	}
	refresh_cache('config');
	write_log("����΢��", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2);
}
elseif($act == 'set_menu')
{
	get_token();
	$smarty->assign('navlabel',"set_menu");
	$smarty->assign('menu',get_weixin_menu());
	$smarty->display('weixin/admin_weixin_menu.htm');
}
elseif($act == 'menu_all_save')
{
	check_token();
	if (is_array($_POST['save_id']) && count($_POST['save_id'])>0)
	{
		foreach($_POST['save_id'] as $k=>$v)
		{
		 
				$setsqlarr['menu_order']=intval($_POST['menu_order'][$k]);
				!$db->updatetable(table('weixin_menu'),$setsqlarr," id=".intval($_POST['save_id'][$k]))?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
 
		}
	}
	//���������
	if (is_array($_POST['add_pid']) && count($_POST['add_pid'])>0)
	{
		for ($i =0; $i <count($_POST['add_pid']);$i++){
			if (!empty($_POST['add_title'][$i]))
			{	
				$setsqlarr['menu_order']=intval($_POST['add_menu_order'][$i]);
				$setsqlarr['parentid']=intval($_POST['add_pid'][$i]);	
				!$db->inserttable(table('weixin_menu'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
	}
	write_log("����΢�Ų˵�", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2);
}
elseif($act == 'del_menu')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_menu($id))
	{
	write_log("ɾ��΢�Ų˵�,��ɾ��".$num."��", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'edit_menu')
{
	get_token();
	$smarty->assign('navlabel',"set_menu");
	$smarty->assign('parent_manu',get_parent_menu());
	$smarty->assign('menu',get_weixin_menu_one($_GET['id']));
	$smarty->display('weixin/admin_weixin_menu_edit.htm');
}
elseif($act == 'edit_menu_save')
{
	check_token();
	$setsqlarr['parentid']=intval($_POST['parentid']);
	$setsqlarr['title']=trim($_POST['title']);
	$setsqlarr['key']=trim($_POST['key']);
	$setsqlarr['type']=trim($_POST['type']);
	$setsqlarr['url']=trim($_POST['url']);
	$setsqlarr['status']=intval($_POST['status']);
	$setsqlarr['menu_order']=intval($_POST['menu_order']);	
	!$db->updatetable(table('weixin_menu'),$setsqlarr," id=".intval($_POST['id']))?adminmsg("�޸�ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=set_menu';
	write_log("�޸�΢�Ų˵�", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2,$link);
}
elseif($act == 'add_menu')
{
	get_token();
	$smarty->assign('navlabel',"set_menu");
	$smarty->assign('parent_manu',get_parent_menu());
	$smarty->display('weixin/admin_weixin_menu_add.htm');
}
elseif($act == 'add_menu_save')
{
	check_token();
	$setsqlarr['parentid']=intval($_POST['parentid']);
	$setsqlarr['title']=trim($_POST['title']);
	$setsqlarr['key']=trim($_POST['key']);
	$setsqlarr['type']=trim($_POST['type']);
	$setsqlarr['url']=trim($_POST['url']);
	$setsqlarr['status']=intval($_POST['status']);
	$setsqlarr['menu_order']=intval($_POST['menu_order']);
	!$db->inserttable(table('weixin_menu'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=set_menu';
	write_log("���΢�Ų˵�", $_SESSION['admin_name'],3);
	adminmsg("��ӳɹ���",2,$link);	
}
elseif($act == 'binding_list')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num from ".table('members')." WHERE weixin_openid!='' ";
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('userlist',get_binding_list($offset,$perpage));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('navlabel',"binding_list");
	$smarty->display('weixin/admin_binding_list.htm');
}
elseif($act == 'del_binding')
{
	check_token();
	$uid=$_REQUEST['uid'];
	if ($num=del_binding($uid))
	{
	write_log("���".$num."����Ա", $_SESSION['admin_name'],3);
	adminmsg("���ɹ��������".$num."����Ա",2);
	}
	else
	{
	adminmsg("���ʧ�ܣ�",1);
	}
}
elseif($act == 'send_weixin_msglist')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num from ".table('weixin_msg_list');
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('navlabel',"send_weixin_msglist");
	$smarty->assign('msglist',get_weixin_msg_list($offset,$perpage));
	$smarty->assign('page',$page->show(3));
	$smarty->display('weixin/admin_weixin_msg_list.htm');
}
elseif($act == 'send_weixin_msg')
{
	get_token();
	$uid = intval($_GET['uid'])==0?adminmsg("��ѡ���Ա��",1):intval($_GET['uid']);
	$user = $db->getone("select * from ".table('members')." where `uid`=".$uid);
	$smarty->assign('navlabel',"send_weixin_msg");
	$smarty->assign('userinfo',$user);
	$smarty->display('weixin/admin_send_weixin_msg.htm');
}
elseif($act == 'send_weixin_msg_save')
{
	check_token();
	$setsqlarr['uid'] = intval($_POST['uid'])==0?adminmsg("��ѡ���Ա��",1):intval($_POST['uid']);
	$setsqlarr['utype'] = intval($_POST['utype']);
	$setsqlarr['username'] = trim($_POST['username']);
	$setsqlarr['weixin_openid'] = trim($_POST['weixin_openid'])==''?adminmsg("��������",1):trim($_POST['weixin_openid']);
	$setsqlarr['content'] = trim($_POST['content'])==''?adminmsg("��������Ϣ���ݣ�",1):trim($_POST['content']);
	$setsqlarr['sendtime'] = time();
	$access_token = get_access_token();
	if(empty($access_token)){
		adminmsg("access_token��ȡʧ�ܣ�",1);
	}
	send_weixin_msg($setsqlarr['weixin_openid'],$setsqlarr['content'],$access_token);
	$insert_id = $db->inserttable(table("weixin_msg_list"),$setsqlarr,1);
	if($insert_id){
		adminmsg("���ͳɹ���",2);
	}else{
		adminmsg("����ʧ�ܣ�",1);
	}
}
elseif($act == 'send_weixin_msgqueue')
{
	get_token();
	$smarty->assign('navlabel',"send_weixin_msgqueue");
	$smarty->display('weixin/admin_send_weixin_msgqueue.htm');
}
elseif($act == 'send_weixin_msgqueue_save')
{
	check_token();
	$utype=intval($_POST['utype']);
	$content = trim($_POST['content'])==''?adminmsg("��������Ϣ���ݣ�",1):trim($_POST['content']);
	if($utype==0){
		$users = $db->getall("select * from ".table('members')." where `weixin_openid`!=''");
	}else{
		$users = $db->getall("select * from ".table('members')." where `weixin_openid`!='' and `utype`=".$utype);
	}
	$access_token = get_access_token();
	if(empty($access_token)){
		adminmsg("access_token��ȡʧ�ܣ�",1);
	}
	$setsqlarr['content'] = $content;
	$setsqlarr['sendtime'] = time();
	foreach ($users as $key => $value) {
		send_weixin_msg($value['weixin_openid'],$content,$access_token);
		$setsqlarr['uid'] = $value['uid'];
		$setsqlarr['utype'] = $value['utype'];
		$setsqlarr['username'] = addslashes($value['username']);
		$setsqlarr['weixin_openid'] = $value['weixin_openid'];
		$db->inserttable(table("weixin_msg_list"),$setsqlarr,1);
	}
	adminmsg("���ͳɹ���",2);
}
elseif($act == 'del_weixin_msg')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_weixin_msg($id))
	{
	write_log("ɾ����Ϣ,��ɾ��".$num."����¼", $_SESSION['admin_name'],3);		
	adminmsg("ɾ���ɹ�����ɾ��".$num."����¼",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'rule')
{
	get_token();
	$smarty->assign('navlabel','rule');
	$smarty->assign('weixin_config',get_cache('weixin_config'));
	$smarty->display('weixin/admin_weixin_rule.htm');
}
elseif($act == 'weixin_rule_save')
{
	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('weixin_config')." SET value='$v' WHERE name='$k'")?adminmsg('����վ������ʧ��', 1):"";
	}
	//��д����Ա��־
	write_log("��̨����΢��֪ͨ����", $_SESSION['admin_name'],3);
	refresh_cache('weixin_config');
	adminmsg("����ɹ���",2);
}
?>