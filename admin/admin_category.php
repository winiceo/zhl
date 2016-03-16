<?php
 /*
 * 74cms ����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_category_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'district';
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"site_category");
$smarty->assign('pageheader',"�������");
if($act == 'grouplist')
{
	get_token();
	$smarty->assign('navlabel',"group");
	$smarty->assign('group',get_category_group());
	$smarty->display('category/admin_category_group.htm');
}
elseif($act == 'add_group')
{
	get_token();
	$smarty->assign('navlabel',"group");
	$smarty->display('category/admin_category_group_add.htm');
}
elseif($act == 'add_group_save')
{
	check_token();
	$setsqlarr['g_name']=!empty($_POST['g_name']) ?trim($_POST['g_name']) : adminmsg("����д������",1);
	$setsqlarr['g_alias']=!empty($_POST['g_alias']) ?trim($_POST['g_alias']) : adminmsg("����д������",1);
	$info=get_category_group_one($setsqlarr['g_alias']);
	if (empty($info))
	{
		if (stripos($setsqlarr['g_alias'],"qs_")===0)
		{
			adminmsg("�����������á�qs_����ͨ",0);
		}
		else
		{
			$link[0]['text'] = "�������б�";
			$link[0]['href'] = '?act=grouplist';
			$link[1]['text'] = "������ӷ�����";
			$link[1]['href'] = "?act=add_group";
			//��д����Ա��־
			write_log("��̨��ӷ��࣡", $_SESSION['admin_name'],3);
			$db->inserttable(table('category_group'),$setsqlarr)?adminmsg("��ӳɹ���",2,$link):adminmsg("���ʧ�ܣ�",0);			
		}
	}
	else
	{
	 adminmsg("���ʧ��,���������ظ�",0);
	}
}
elseif($act == 'edit_group')
{
	get_token();
	$smarty->assign('navlabel',"group");
	$smarty->assign('group',get_category_group_one($_GET['alias']));
	$smarty->display('category/admin_category_group_edit.htm');
}
elseif($act == 'edit_group_save')
{
	check_token();
	$setsqlarr['g_name']=!empty($_POST['g_name']) ?trim($_POST['g_name']) : adminmsg("����д������",1);
	$setsqlarr['g_alias']=!empty($_POST['g_alias']) ?trim($_POST['g_alias']) : adminmsg("����д������",1);
	$info=get_category_group_one($setsqlarr['g_alias']);
	if (empty($info) || $info['g_id']==intval($_POST['g_id']))
	{
		if (stripos($setsqlarr['g_alias'],"qs_")===0)
		{
			adminmsg("�����������á�qs_����ͨ",0);
		}
		else
		{
			$link[0]['text'] = "�������б�";
			$link[0]['href'] = '?act=grouplist';
			$link[1]['text'] = "�鿴�޸Ľ��";
			$link[1]['href'] = "?act=edit_group&alias=".$setsqlarr['g_alias'];
			$db->updatetable(table('category_group'),$setsqlarr," g_id=".intval($_POST['g_id']))?'':adminmsg("�޸�ʧ�ܣ�",0);
			//ͬʱ�޸ķ������µķ������
			$catarr['c_alias']=$setsqlarr['g_alias'];
			$db->updatetable(table('category'),$catarr," c_alias='".$_POST['old_g_alias']."'")?'':adminmsg("�޸�ʧ�ܣ�",0);
			//��д����Ա��־
			write_log("��̨�ɹ��޸ķ��࣡", $_SESSION['admin_name'],3);
			adminmsg("�޸ĳɹ���",2,$link);						
		}
	}
	else
	{
	 adminmsg("���ʧ��,���������ظ�",0);
	}
}
elseif($act == 'del_group')
{
	check_token();
	$alias=$_REQUEST['alias'];
	if ($num=del_group($alias))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'show_category')
{
	get_token();
	$smarty->assign('navlabel',"group");
	$smarty->assign('group',get_category_group_one($_GET['alias']));
	$smarty->assign('category',get_category($_GET['alias']));	
	$smarty->display('category/admin_category_list.htm');
}
elseif($act == 'category_save')
{
	check_token();
	if (is_array($_POST['c_id']) && count($_POST['c_id'])>0)
	{
		for ($i =0; $i <count($_POST['c_id']);$i++){
			if (!empty($_POST['c_name'][$i]))
			{	
				$setsqlarr['c_name']=trim($_POST['c_name'][$i]);
				$setsqlarr['c_order']=intval($_POST['c_order'][$i]);
				$setsqlarr['c_index']=getfirstchar($setsqlarr['c_name']);
				!$db->updatetable(table('category'),$setsqlarr," c_id=".intval($_POST['c_id'][$i]))?adminmsg("���ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}

	}
	//��д����Ա��־
	write_log("��̨�ɹ��޸ķ��࣡", $_SESSION['admin_name'],3);
	refresh_category_cache();
	makejs_classify();
	makejs_train_classify();
	adminmsg("�޸���ɣ�",2);
}
elseif($act == 'add_category')
{
	get_token();
	$smarty->assign('navlabel',"group");
	$smarty->assign('group',get_category_group_one($_GET['alias']));
	$smarty->display('category/admin_category_add.htm');
}
elseif($act == 'add_category_save')
{
	check_token();
	$num=0;
	if (is_array($_POST['c_name']) && count($_POST['c_name'])>0)
	{
		for ($i =0; $i <count($_POST['c_name']);$i++){
			if (!empty($_POST['c_name'][$i]))
			{		
				$setsqlarr['c_name']=trim($_POST['c_name'][$i]);
				$setsqlarr['c_alias']=trim($_POST['c_alias'][$i]);
				$setsqlarr['c_order']=intval($_POST['c_order'][$i]);
				$setsqlarr['c_index']=getfirstchar($setsqlarr['c_name']);
				$setsqlarr['c_note']=trim($_POST['c_note'][$i]);				
				!$db->inserttable(table('category'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}

	}
	if ($num==0)
	{
	adminmsg("���ʧ��,���ݲ�����",1);
	}
	else
	{
	$link[0]['text'] = "���ط����б�";
	$link[0]['href'] = "?act=show_category&alias=".$setsqlarr['c_alias'];
	$link[1]['text'] = "������ӷ���";
	$link[1]['href'] = "?act=add_category&alias=".$setsqlarr['c_alias'];
	refresh_category_cache();
	makejs_classify();
	makejs_train_classify();
	//��д����Ա��־
	write_log("��̨�ɹ���ӷ��� , �����".$num."����", $_SESSION['admin_name'],3);
	adminmsg("��ӳɹ��������".$num."������",2,$link);
	}
}
elseif($act == 'edit_category')
{	
	get_token();
	$smarty->assign('navlabel',"group");
	$smarty->assign('category',get_category_one($_GET['id']));
	$smarty->display('category/admin_category_edit.htm');
}
elseif($act == 'edit_category_save')
{
	check_token();
	$setsqlarr['c_name']=!empty($_POST['c_name']) ?trim($_POST['c_name']) : adminmsg("����д����",1);
	$setsqlarr['c_order']=intval($_POST['c_order']);
	$setsqlarr['c_parentid']=intval($_POST['c_parentid']);
	$setsqlarr['c_index']=getfirstchar($setsqlarr['c_name']);
	$setsqlarr['c_note']=trim($_POST['c_note']);				
	!$db->updatetable(table('category'),$setsqlarr," c_id=".intval($_POST['c_id']))?adminmsg("����ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=show_category&alias='.$_POST['c_alias'];
	$link[1]['text'] = "�鿴�޸Ľ��";
	$link[1]['href'] = "?act=edit_category&id=".intval($_POST['c_id']);
	refresh_category_cache();
	makejs_classify();
	makejs_train_classify();
	//��д����Ա��־
	write_log("��̨�ɹ��޸ķ���", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2,$link);
}
elseif($act == 'del_category')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_category($id))
	{
	refresh_category_cache();
	makejs_classify();
	makejs_train_classify();
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
//����--------------
elseif($act == 'district')
{
	get_token();
	$smarty->assign('navlabel',"district");
	$smarty->assign('district',get_category_district());
	$smarty->display('category/admin_category_district.htm');
}
elseif($act == 'district_all_save')
{
	check_token();
	if (is_array($_POST['save_id']) && count($_POST['save_id'])>0)
	{
		foreach($_POST['save_id'] as $k=>$v)
		{
		 
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$k]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$k]);
				!$db->updatetable(table('category_district'),$setsqlarr," id=".intval($_POST['save_id'][$k]))?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
 
		}
		//��д����Ա��־
		write_log("��̨�ɹ����µ������� , ������".$num."��", $_SESSION['admin_name'],3);
	}
	//���������
	if (is_array($_POST['add_pid']) && count($_POST['add_pid'])>0)
	{
		for ($i =0; $i <count($_POST['add_pid']);$i++){
			if (!empty($_POST['add_categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['add_categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['add_category_order'][$i]);
				$setsqlarr['parentid']=intval($_POST['add_pid'][$i]);	
				!$db->inserttable(table('category_district'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
		//��д����Ա��־
		write_log("��̨�ɹ���ӵ������� , �����".$num."��", $_SESSION['admin_name'],3);
	}
	makejs_classify();
	makejs_train_classify();
	adminmsg("����ɹ���",2);
}
elseif($act == 'del_district')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_district($id))
	{
	makejs_classify();
	//��д����Ա��־
	write_log("��̨�ɹ�ɾ���������࣡��ɾ��".$num."��", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'edit_district')
{
	get_token();
	$smarty->assign('navlabel',"district");
	$smarty->assign('district',get_category_district_one($_GET['id']));
	$smarty->display('category/admin_category_district_edit.htm');
}
elseif($act == 'edit_district_save')
{
	check_token();
	$setsqlarr['categoryname']=!empty($_POST['categoryname']) ?trim($_POST['categoryname']) : adminmsg("����д����",1);
	$setsqlarr['category_order']=intval($_POST['category_order']);
	$setsqlarr['parentid']=intval($_POST['parentid']);				
	!$db->updatetable(table('category_district'),$setsqlarr," id=".intval($_POST['id']))?adminmsg("�޸�ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=district';
	makejs_classify();
	makejs_train_classify();
	//��д����Ա��־
	write_log("��̨�ɹ��޸ĵ������࣡", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2,$link);
}
elseif($act == 'add_district')
{
	get_token();
	$smarty->assign('navlabel',"district");
	$smarty->display('category/admin_category_district_add.htm');
}
elseif($act == 'add_district_save')
{
	check_token();
	//���������
	if (is_array($_POST['categoryname']) && count($_POST['categoryname'])>0)
	{
		for ($i =0; $i <count($_POST['categoryname']);$i++){
			if (!empty($_POST['categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$i]);
				$setsqlarr['parentid']=intval($_POST['parentid'][$i]);	
				!$db->inserttable(table('category_district'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=district';
	makejs_classify();
	makejs_train_classify();
	//��д����Ա��־
	write_log("��̨�ɹ���ӵ������࣡���������{$num}������", $_SESSION['admin_name'],3);
	adminmsg("��ӳɹ������������{$num}������",2,$link);	
}
///////---------------ְλ����
elseif($act == 'jobs')
{
	get_token();
	$smarty->assign('navlabel',"jobs");
	$smarty->assign('district',get_category_jobs());
	$smarty->display('category/admin_category_jobs.htm');
}
elseif($act == 'jobs_all_save')
{
	check_token();
	if (is_array($_POST['save_id']) && count($_POST['save_id'])>0)
	{
		for ($i =0; $i <count($_POST['save_id']);$i++){
			if (!empty($_POST['categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$i]);
				$setsqlarr['content']=trim($_POST['content'][$i]);				
				!$db->updatetable(table('category_jobs'),$setsqlarr," id=".intval($_POST['save_id'][$i]))?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}
		}
		//��д����Ա��־
		write_log("��̨�ɹ�����ְλ���࣡���θ�����{$num}������", $_SESSION['admin_name'],3);
	}
	//���������
	if (is_array($_POST['add_pid']) && count($_POST['add_pid'])>0)
	{
		for ($i =0; $i <count($_POST['add_pid']);$i++){
			if (!empty($_POST['add_categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['add_categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['add_category_order'][$i]);
				$setsqlarr['content']=trim($_POST['content'][$i]);	
				$setsqlarr['parentid']=intval($_POST['add_pid'][$i]);	
				!$db->inserttable(table('category_jobs'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
		//��д����Ա��־
		write_log("��̨�ɹ����ְλ���࣡���������{$num}������", $_SESSION['admin_name'],3);
	}
	makejs_classify();
	makejs_train_classify();
	adminmsg("����ɹ���",2);
}
elseif($act == 'del_jobs_category')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_jobs_category($id))
	{
		//��д����Ա��־
		write_log("��̨�ɹ�ɾ��ְλ���࣡��ɾ��".$num."��", $_SESSION['admin_name'],3);
		adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'edit_jobs_category')
{
	get_token();
	$smarty->assign('navlabel',"jobs");
	$smarty->assign('category',get_category_jobs_one($_GET['id']));
	$smarty->display('category/admin_category_jobs_edit.htm');
}
elseif($act == 'edit_jobs_category_save')
{
	check_token();
	$setsqlarr['categoryname']=!empty($_POST['categoryname']) ?trim($_POST['categoryname']) : adminmsg("����д����",1);
	$setsqlarr['category_order']=intval($_POST['category_order']);
	$setsqlarr['content']=trim($_POST['content']);
	$setsqlarr['parentid']=intval($_POST['parentid']);				
	!$db->updatetable(table('category_jobs'),$setsqlarr," id=".intval($_POST['id']))?adminmsg("�޸�ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=jobs';
	makejs_classify();
	makejs_train_classify();
	//��д����Ա��־
	write_log("��̨�ɹ��޸�ְλ���࣡", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2,$link);
}
elseif($act == 'add_category_jobs')
{
	get_token();
	$smarty->assign('navlabel',"jobs");
	$smarty->display('category/admin_category_jobs_add.htm');
}
elseif($act == 'add_category_jobs_save')
{
	check_token();
	//���������
	if (is_array($_POST['categoryname']) && count($_POST['categoryname'])>0)
	{
		for ($i =0; $i <count($_POST['categoryname']);$i++){
			if (!empty($_POST['categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$i]);
				$setsqlarr['content']=trim($_POST['content'][$i]);	
				$setsqlarr['parentid']=intval($_POST['parentid'][$i]);	
				!$db->inserttable(table('category_jobs'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=jobs';
	makejs_classify();
	makejs_train_classify();
	//��д����Ա��־
	write_log("��̨�ɹ����ְλ���࣡���������".$num."������", $_SESSION['admin_name'],3);
	adminmsg("��ӳɹ������������".$num."������",2,$link);	
}

//�߼�ְλ����
elseif($act == 'hunter_jobs')
{
	get_token();
	$smarty->assign('navlabel',"hunter_jobs");
	$smarty->assign('category_hunterjobs',get_category_hunterjobs());
	$smarty->display('category/admin_category_hunterjobs.htm');
}
elseif($act == 'hunterjobs_all_save')
{
	check_token();
	if (is_array($_POST['save_id']) && count($_POST['save_id'])>0)
	{
		for ($i =0; $i <count($_POST['save_id']);$i++){
			if (!empty($_POST['categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$i]);				
				!$db->updatetable(table('category_hunterjobs'),$setsqlarr," id=".intval($_POST['save_id'][$i]))?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}
		}
		//��д����Ա��־
		write_log("��̨�ɹ�������ͷְλ���࣡���θ�����".$num."������", $_SESSION['admin_name'],3);
	}
	//���������
	if (is_array($_POST['add_pid']) && count($_POST['add_pid'])>0)
	{
		for ($i =0; $i <count($_POST['add_pid']);$i++){
			if (!empty($_POST['add_categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['add_categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['add_category_order'][$i]);
				$setsqlarr['parentid']=intval($_POST['add_pid'][$i]);	
				!$db->inserttable(table('category_hunterjobs'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
		//��д����Ա��־
		write_log("��̨�ɹ������ͷְλ���࣡���������".$num."������", $_SESSION['admin_name'],3);
	}
	makejs_classify();
	makejs_train_classify();
	adminmsg("����ɹ���",2);
}
elseif($act == 'del_hunterjobs_category')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_hunterjobs_category($id))
	{
		//��д����Ա��־
		write_log("��̨�ɹ�ɾ����ͷְλ���࣡����ɾ����".$num."������", $_SESSION['admin_name'],3);
		adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'edit_hunterjobs_category')
{
	get_token();
	$smarty->assign('navlabel',"hunter_jobs");
	$smarty->assign('category',get_category_hunterjobs_one($_GET['id']));
	$smarty->display('category/admin_category_hunterjobs_edit.htm');
}
elseif($act == 'edit_hunterjobs_category_save')
{
	check_token();
	$setsqlarr['categoryname']=!empty($_POST['categoryname']) ?trim($_POST['categoryname']) : adminmsg("����д����",1);
	$setsqlarr['category_order']=intval($_POST['category_order']);
	$setsqlarr['parentid']=intval($_POST['parentid']);				
	!$db->updatetable(table('category_hunterjobs'),$setsqlarr," id=".intval($_POST['id']))?adminmsg("�޸�ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=hunter_jobs';
	makejs_classify();
	makejs_train_classify();
	//��д����Ա��־
	write_log("��̨�ɹ��޸���ͷְλ���࣡", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2,$link);
}
elseif($act == 'add_category_hunterjobs')
{
	get_token();
	$smarty->assign('navlabel',"hunter_jobs");
	$smarty->display('category/admin_category_hunterjobs_add.htm');
}
elseif($act == 'add_category_hunterjobs_save')
{
	check_token();
	//���������
	if (is_array($_POST['categoryname']) && count($_POST['categoryname'])>0)
	{
		for ($i =0; $i <count($_POST['categoryname']);$i++){
			if (!empty($_POST['categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$i]);
				$setsqlarr['parentid']=intval($_POST['parentid'][$i]);	
				!$db->inserttable(table('category_hunterjobs'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
		//��д����Ա��־
		write_log("��̨�ɹ������ͷְλ���࣡���������".$num."������", $_SESSION['admin_name'],3);
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=hunter_jobs';
	makejs_classify();
	makejs_train_classify();
	adminmsg("��ӳɹ������������".$num."������",2,$link);	
}
elseif($act == 'colorlist')
{
	get_token();
	$smarty->assign('navlabel',"color");
	$smarty->assign('color',get_color());
	$smarty->display('category/admin_color.htm');
}
elseif($act == 'add_color')
{
	get_token();
	$smarty->assign('navlabel',"color");
	$smarty->display('category/admin_color_add.htm');
}
elseif($act == 'add_color_save')
{
	check_token();
	$setsqlarr['value']=!empty($_POST['val']) ?trim($_POST['val']) : adminmsg("��ѡ����ɫ",1);
	$link[0]['text'] = "��ɫ�б�";
	$link[0]['href'] = '?act=colorlist';
	$link[1]['text'] = "���������ɫ";
	$link[1]['href'] = "?act=add_color";
	//��д����Ա��־
	write_log("��̨�����ɫ���࣡", $_SESSION['admin_name'],3);
	$db->inserttable(table('color'),$setsqlarr)?adminmsg("��ӳɹ���",2,$link):adminmsg("���ʧ�ܣ�",0);			

}
elseif($act == 'edit_color')
{
	get_token();
	$smarty->assign('navlabel',"color");
	$smarty->assign('color',get_color_one($_GET['id']));
	$smarty->display('category/admin_color_edit.htm');
}
elseif($act == 'edit_color_save')
{
	check_token();
	$setsqlarr['value']=!empty($_POST['val']) ?trim($_POST['val']) : adminmsg("��ѡ����ɫ",1);
	$info=get_color_one($_POST['id']);
	
	$link[0]['text'] = "��ɫ�б�";
	$link[0]['href'] = '?act=colorlist';
	$link[1]['text'] = "�鿴�޸Ľ��";
	$link[1]['href'] = "?act=edit_color&id=".$_POST['id'];
	$db->updatetable(table('color'),$setsqlarr," id=".intval($_POST['id']))?'':adminmsg("�޸�ʧ�ܣ�",0);
	//��д����Ա��־
	write_log("��̨�ɹ��޸���ɫ���࣡", $_SESSION['admin_name'],3);
	adminmsg("�޸ĳɹ���",2,$link);						
	
}
elseif($act == 'del_color')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_color($id))
	{
		//��д����Ա��־
		write_log("��̨�ɹ�ɾ����ɫ���࣡��ɾ��".$num."��", $_SESSION['admin_name'],3);
		adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
///////---------------רҵ����
elseif($act == 'major')
{
	get_token();
	$smarty->assign('navlabel',"major");
	$smarty->assign('district',get_category_major());
	$smarty->display('category/admin_category_major.htm');
}
elseif($act == 'major_all_save')
{
	check_token();
	if (is_array($_POST['save_id']) && count($_POST['save_id'])>0)
	{
		for ($i =0; $i <count($_POST['save_id']);$i++){
			if (!empty($_POST['categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$i]);			
				!$db->updatetable(table('category_major'),$setsqlarr," id=".intval($_POST['save_id'][$i]))?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}
		}
		//��д����Ա��־
		write_log("��̨�ɹ�����רҵ���࣡���θ�����{$num}������", $_SESSION['admin_name'],3);
	}
	//���������
	if (is_array($_POST['add_pid']) && count($_POST['add_pid'])>0)
	{
		for ($i =0; $i <count($_POST['add_pid']);$i++){
			if (!empty($_POST['add_categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['add_categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['add_category_order'][$i]);
				$setsqlarr['parentid']=intval($_POST['add_pid'][$i]);	
				!$db->inserttable(table('category_major'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
		//��д����Ա��־
		write_log("��̨�ɹ����רҵ���࣡���������{$num}������", $_SESSION['admin_name'],3);
	}
	makejs_classify();
	makejs_train_classify();
	adminmsg("����ɹ���",2);
}
elseif($act == 'del_major_category')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_major_category($id))
	{
		//��д����Ա��־
		write_log("��̨�ɹ�ɾ��רҵ���࣡��ɾ��".$num."��", $_SESSION['admin_name'],3);
		adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'edit_major_category')
{
	get_token();
	$smarty->assign('navlabel',"major");
	$smarty->assign('category',get_category_major_one($_GET['id']));
	$smarty->display('category/admin_category_major_edit.htm');
}
elseif($act == 'edit_major_category_save')
{
	check_token();
	$setsqlarr['categoryname']=!empty($_POST['categoryname']) ?trim($_POST['categoryname']) : adminmsg("����д����",1);
	$setsqlarr['category_order']=intval($_POST['category_order']);
	$setsqlarr['parentid']=intval($_POST['parentid']);				
	!$db->updatetable(table('category_major'),$setsqlarr," id=".intval($_POST['id']))?adminmsg("�޸�ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=major';
	makejs_classify();
	makejs_train_classify();
	//��д����Ա��־
	write_log("��̨�ɹ��޸�רҵ���࣡", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2,$link);
}
elseif($act == 'add_category_major')
{
	get_token();
	$smarty->assign('navlabel',"major");
	$smarty->display('category/admin_category_major_add.htm');
}
elseif($act == 'add_category_major_save')
{
	check_token();
	//���������
	if (is_array($_POST['categoryname']) && count($_POST['categoryname'])>0)
	{
		for ($i =0; $i <count($_POST['categoryname']);$i++){
			if (!empty($_POST['categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$i]);
				$setsqlarr['parentid']=intval($_POST['parentid'][$i]);	
				!$db->inserttable(table('category_major'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=major';
	makejs_classify();
	makejs_train_classify();
	//��д����Ա��־
	write_log("��̨�ɹ����רҵ���࣡���������".$num."������", $_SESSION['admin_name'],3);
	adminmsg("��ӳɹ������������".$num."������",2,$link);	
}
?>