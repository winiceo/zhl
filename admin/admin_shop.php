<?php
 /*
 * 74cms ��Ʒ��
*/
define('IN_QISHI',true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_shop_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
$smarty->assign('act',$act);
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"shop");
if($act == 'list')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY id  DESC";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if($key_type===1)$wheresql=" WHERE shop_title like '%{$key}%'";
		$oederbysql="";
	}
	else
	{
		$settr=intval($_GET['settr']);
		if ($settr>0)
		{
			$wheresql.=empty($wheresql)?" WHERE ":" AND  ";
			$days=intval($settr);
			$settr=strtotime("-{$days} day");
			$wheresql.=" addtime> {$settr} ";	
		}
	}

	$total_sql="SELECT COUNT(*) AS num FROM ".table('shop_goods').$wheresql;
	$total=$db->get_total($total_sql);
	$page = new page(array('total'=>$total, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_shop($offset, $perpage,$wheresql.$oederbysql);
	$smarty->assign('list',$list);
	$smarty->assign('total',$total);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('pageheader',"�����̳�");
	$smarty->display('shop/admin_shop_list.htm');
}
elseif($act == 'shop_add')
{
	get_token();
	$smarty->assign('pageheader',"�����̳� ");
	$smarty->display('shop/admin_shop_add.htm');
}
elseif($act == 'shop_edit')
{
	get_token();
	$id=intval($_GET['id']);
	$smarty->assign('pageheader',"�����̳� ");
	$smarty->assign('show',get_shop_one($id));
	$smarty->display('shop/admin_shop_add.htm');
}
elseif($act == 'shop_save')
{
	check_token();
	$id=intval($_POST['id']);
	$setarr["shop_title"]=$_POST["shop_title"]?trim($_POST["shop_title"]):adminmsg("��������Ʒ����");
	$setarr["shop_brand"]=$_POST["shop_brand"]?trim($_POST["shop_brand"]):adminmsg("������Ʒ��");
	$setarr["scategory"]=$_POST["scategory"]?trim($_POST["scategory"]):adminmsg("��ѡ����������");
	$scategory_arr=explode(",", $setarr["scategory"]);
	$setarr["category"]=$scategory_arr[0];
	$setarr["scategory"]=$scategory_arr[1];
	$setarr["category_cn"]=$scategory_arr[2];

	$setarr["shop_stock"]=$_POST["shop_stock"]?intval($_POST["shop_stock"]):adminmsg("��������Ʒ���");
	$setarr["shop_customer"]=intval($_POST["shop_customer"]);
	$setarr["shop_points"]=$_POST["shop_points"]?intval($_POST["shop_points"]):adminmsg("��������Ʒ�һ��������");
	$setarr["content"]=$_POST["content"]?trim($_POST["content"]):adminmsg("��������Ʒ����");
	$setarr["recommend"]=intval($_POST["recommend"]);
	if($_FILES['shop_img']['name'])
	{
		require_once(QISHI_ROOT_PATH.'include/upload.php');
		$upfiles_dir="../data/shop/".date("Y/m/d/");
		make_dir($upfiles_dir);
		$shop_img=_asUpFiles($upfiles_dir, "shop_img", 1024*2, 'jpg/gif/png',true);
		$makefile=$upfiles_dir.$shop_img;
		$thumb_dir="../data/shop/thumb/".date("Y/m/d/");
		make_dir($thumb_dir);
		makethumb($makefile,$thumb_dir,288,288,1);
		$setarr['shop_img']=date("Y/m/d/").$shop_img;
	}
	if($id>0)
	{
		write_log("��̨�޸���Ʒ��Ϣ", $_SESSION['admin_name'],3);
		!$db->updatetable(table("shop_goods"),$setarr,array("id"=>$id))?adminmsg("�޸�ʧ�ܣ�"):adminmsg("�޸ĳɹ���",2);
	}
	else
	{
		$setarr['addtime']=time();
		$setarr['shop_number']=time().rand(1000,9999);
		write_log("��̨�����Ʒ��Ϣ", $_SESSION['admin_name'],3);
		!$db->inserttable(table("shop_goods"),$setarr)?adminmsg("���ʧ�ܣ�"):adminmsg("��ӳɹ���",2);	
	}
}
elseif($act=="shop_del")
{
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ����Ʒ��",1);
	$num=del_shop($id);
	if ($num>0)
	{
	write_log("��̨ɾ����Ʒ,��ɾ��".$return."��", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
/*
	��Ʒ����
*/
elseif($act == 'category')
{
	get_token();
	$smarty->assign('pageheader',"�����̳�-��Ʒ����");
	$smarty->assign('category',get_shop_category());
	$smarty->display('shop/admin_shop_category.htm');
}
elseif($act == 'category_all_save')
{
	check_token();
	if (is_array($_POST['save_id']) && count($_POST['save_id'])>0)
	{
		foreach($_POST['save_id'] as $k=>$v)
		{
		 
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$k]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$k]);
				!$db->updatetable(table('shop_category'),$setsqlarr," id=".intval($_POST['save_id'][$k]))?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
 
		}
		//��д����Ա��־
		write_log("��̨�ɹ�������Ʒ���� , ������".$num."��", $_SESSION['admin_name'],3);
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
				!$db->inserttable(table('shop_category'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
		//��д����Ա��־
		write_log("��̨�ɹ������Ʒ���� , �����".$num."��", $_SESSION['admin_name'],3);
	}
	makejs_classify();
	makejs_train_classify();
	adminmsg("����ɹ���",2);
}
elseif($act == 'del_category')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_shop_category($id))
	{
	makejs_classify();
	//��д����Ա��־
	write_log("��̨�ɹ�ɾ����Ʒ���࣡��ɾ��".$num."��", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'edit_category')
{
	get_token();
	$smarty->assign('pageheader',"�����̳�-��Ʒ����");
	$smarty->assign('category',get_shop_category_one($_GET['id']));
	$smarty->display('shop/admin_shop_category_edit.htm');
}
elseif($act == 'edit_category_save')
{
	check_token();
	$setsqlarr['categoryname']=!empty($_POST['categoryname']) ?trim($_POST['categoryname']) : adminmsg("����д����",1);
	$setsqlarr['category_order']=intval($_POST['category_order']);
	$setsqlarr['parentid']=intval($_POST['parentid']);				
	!$db->updatetable(table('shop_category'),$setsqlarr," id=".intval($_POST['id']))?adminmsg("�޸�ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=category';
	makejs_classify();
	makejs_train_classify();
	//��д����Ա��־
	write_log("��̨�ɹ��޸���Ʒ���࣡", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2,$link);
}
elseif($act == 'add_category')
{
	get_token();
	$smarty->assign('pageheader',"�����̳�-��Ʒ����");
	$smarty->display('shop/admin_shop_category_add.htm');
}
elseif($act == 'add_category_save')
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
				!$db->inserttable(table('shop_category'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=category';
	makejs_classify();
	makejs_train_classify();
	//��д����Ա��־
	write_log("��̨�ɹ������Ʒ���࣡���������{$num}������", $_SESSION['admin_name'],3);
	adminmsg("��ӳɹ������������{$num}������",2,$link);	
}
elseif($act == 'order')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY id  DESC";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if($key_type===1)$wheresql=" WHERE shop_title like '%{$key}%'";
		$oederbysql="";
	}
	else
	{
		$settr=intval($_GET['settr']);
		if ($settr>0)
		{
			$wheresql.=empty($wheresql)?" WHERE ":" AND  ";
			$days=intval($settr);
			$settr=strtotime("-{$days} day");
			$wheresql.=" addtime> {$settr} ";	
		}
		$state=intval($_GET['state']);
		if($state>0)
		{
			$state=$state-1;
			$wheresql.=empty($wheresql)?" WHERE ":" AND  ";
			$wheresql.=" state={$state} ";	
		}
		
	}

	$total_sql="SELECT COUNT(*) AS num FROM ".table('shop_order').$wheresql;
	$total=$db->get_total($total_sql);
	$page = new page(array('total'=>$total, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_order($offset, $perpage,$wheresql.$oederbysql);
	$smarty->assign('list',$list);
	$smarty->assign('total',$total);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('pageheader',"�����̳�");
	$smarty->display('shop/admin_shop_order.htm');
}
elseif($act =='order_show')
{
	$id=intval($_GET['id']);
	$show=get_order_one($id);
	$smarty->assign('show',$show);
	$smarty->assign('pageheader',"�����̳�");
	$smarty->display('shop/admin_shop_order_show.htm');
}
elseif($act == "order_del")
{
	$id=$_REQUEST['id']?$_REQUEST['id']:adminmsg("��ѡ�񶩵�",1);
	$num= order_del($id);
	if($num>0)
	{
		adminmsg("ɾ�������ɹ�!",2);
	}else
	{
		adminmsg("ɾ������ʧ��",1);
	}
}	
// ���ö���״̬
elseif($act=='set_order')
{
	$id=$_REQUEST['id']?$_REQUEST['id']:adminmsg("��ѡ�񶩵�",1);
	$state=$_GET['state']?intval($_GET['state']):intval($_POST['state']);
	$num = set_order($id,$state);
	if($num>0)
	{
		adminmsg("�����ɹ�!",2);
	}else
	{
		adminmsg("����ʧ�ܣ�",1);
	}
}
// ���Źؼ���
elseif($act == "hotword")
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY w_hot DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	if ($key)
	{
		$wheresql=" WHERE w_word like '%{$key}%'";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('shop_hotword')." ".$wheresql;
	$page = new page(array('total'=>$db->get_total($total_sql),'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$hotword = get_hotword($offset, $perpage,$wheresql.$oederbysql);	
	$smarty->assign('hotword',$hotword);
	$smarty->assign('navlabel',"list");	
	$smarty->assign('page',$page->show(3));	
	$smarty->assign('pageheader',"�����̳�");
	$smarty->display('shop/admin_hotword_list.htm');
}
elseif($act == 'add')
{
	get_token();
	$smarty->assign('navlabel',"add");	
	$smarty->assign('pageheader',"�����̳�");
	$smarty->display('shop/admin_hotword_add.htm');
}
elseif($act == 'addsave')
{
	check_token();
	$setsqlarr['w_word']=trim($_POST['w_word'])?trim($_POST['w_word']):adminmsg('�ؼ��ʱ�����д��',1);
	$setsqlarr['w_hot']=intval($_POST['w_hot']);
	if (get_hotword_obtainword($setsqlarr['w_word']))
	{
	adminmsg("�ؼ����Ѿ����ڣ�",0);
	}
	$link[0]['text'] = "�������";
	$link[0]['href'] = '?act=add&w_type='.$setsqlarr['w_type'];
	$link[1]['text'] = "�����б�";
	$link[1]['href'] = '?act=hotword';
	write_log("������Źؼ���", $_SESSION['admin_name'],3);
	!$db->inserttable(table('shop_hotword'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):adminmsg("��ӳɹ���",2,$link);
}
elseif($act == 'edit')
{
	get_token();
	$smarty->assign('hotword',get_hotword_one($_GET['id']));
	$smarty->assign('pageheader',"�����̳�");
	$smarty->display('shop/admin_hotword_edit.htm');
}
elseif($act == 'editsave')
{
	check_token();
	$id = !empty($_POST['id']) ? intval($_POST['id']) : adminmsg('��������',1);
	$setsqlarr['w_word']=trim($_POST['w_word'])?trim($_POST['w_word']):adminmsg('�ؼ��ʱ�����д��',1);
	$setsqlarr['w_hot']=intval($_POST['w_hot']);
	$word=get_hotword_obtainword($setsqlarr['w_word']);
	if ($word['id'] && $word['id']<>$id)
	{
	adminmsg("�ؼ����Ѿ����ڣ�",0);
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=hotword';
	write_log("�޸����Źؼ���", $_SESSION['admin_name'],3);
 	!$db->updatetable(table('shop_hotword'),$setsqlarr," id=".$id."")?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
}
elseif($act == 'hottype_del')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_hottype($id))
	{
	write_log("ɾ�����Źؼ���,��ɾ�� {$num} ��", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�����ɾ�� {$num} ��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
?>