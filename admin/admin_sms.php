<?php
 /*
 * 74cms �ʼ�����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set_sms';
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"set_sms");
$smarty->assign('pageheader',"��������");
if($act == 'set_sms')
{
	get_token();
	$smarty->assign('sms',get_cache('sms_config'));
	$smarty->assign('navlabel','set');
	$smarty->display('sms/admin_sms_set.htm');
}
elseif($act == 'set_save')
{
	check_token();
	header("Cache-control: private");
	foreach($_POST as $k => $v){
	!$db->query("UPDATE ".table('sms_config')." SET value='$v' WHERE name='$k'")?adminmsg('����վ������ʧ��', 1):"";
	}
	//��д����Ա��־
	write_log("��̨����վ������", $_SESSION['admin_name'],3);
	refresh_cache('sms_config');
	adminmsg("����ɹ���",2);
}
if($act == 'testing')
{
	get_token();
	$smarty->assign('navlabel','testing');
	$smarty->display('sms/admin_sms_testing.htm');
}
elseif($act == 'sms_testing')
{
	check_token();
	$txt="���ã�����һ��������ģ�����õĶ��š��յ��˶��ţ���ζ�����Ķ���ģ��������ȷ�������Խ������������ˣ�";
	$mobile=$_POST['mobile'];
	if (!preg_match("/^(13|15|14|17|18)\d{9}$/",$mobile))
	{
	adminmsg("�ֻ�����д������������д!",0);
	}
	if($_POST['type']==1){
		$r=captcha_send_sms($mobile,$txt);
		if ($r=="success")
		{
			//��д����Ա��־
		write_log("��̨���ŷ��ͳɹ���", $_SESSION['admin_name'],3);
		adminmsg('���ŷ��ͳɹ���',2);
		}
		else
		{
		adminmsg("���ŷ���ʧ�ܣ�$r",1);
		}
	}elseif($_POST['type']==2){
		$r=send_sms($mobile,$txt);
		if ($r=="success")
		{
			//��д����Ա��־
		write_log("��̨���ŷ��ͳɹ���", $_SESSION['admin_name'],3);
		adminmsg('���ŷ��ͳɹ���',2);
		}
		else
		{
		adminmsg("���ŷ���ʧ�ܣ�$r",1);
		}
	}elseif($_POST['type']==3){
		$r=free_send_sms($mobile,$txt);
		if ($r=="success")
		{
			//��д����Ա��־
		write_log("��̨���ŷ��ͳɹ���", $_SESSION['admin_name'],3);
		adminmsg('���ŷ��ͳɹ���',2);
		}
		else
		{
		adminmsg("���ŷ���ʧ�ܣ�$r",1);
		}
	}
	
}
elseif($act == 'set_tpl')
{
	get_token();
	$smarty->assign('navlabel','templates');
	$smarty->assign('mailconfig',get_cache('mailconfig'));
	$smarty->display('sms/admin_sms_templates.htm');
}
elseif($act == 'rule')
{
	get_token();
	$smarty->assign('navlabel','rule');
	$smarty->assign('sms_config',get_cache('sms_config'));
	$smarty->display('sms/admin_sms_rule.htm');
}
elseif($act == 'sms_rule_save')
{
	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('sms_config')." SET value='$v' WHERE name='$k'")?adminmsg('����վ������ʧ��', 1):"";
	}
	//��д����Ա��־
	write_log("��̨���ö������ã�", $_SESSION['admin_name'],3);
	refresh_cache('sms_config');
	adminmsg("����ɹ���",2);
}
elseif($act == 'edit_tpl')
{
	get_token();
	$templates_name=trim($_GET['templates_name']);
	$label=array();
	$label[]=array('{sitename}','��վ����');
	$label[]=array('{sitedomain}','��վ����');
	//���ɱ�ǩ
	if ($templates_name=='set_reg')
	{
	$label[]=array('{username}','�û���');
	$label[]=array('{password}','����');
	}
	elseif ($templates_name=='set_applyjobs')
	{
	$label[]=array('{personalfullname}','������');
	$label[]=array('{jobsname}','����ְλ����');
	}
	elseif ($templates_name=='set_invite')
	{
	$label[]=array('{companyname}','���뷽(��˾����)');
	}
	elseif ($templates_name=='set_order')
	{
	$label[]=array('{paymenttpye}','���ʽ');
	$label[]=array('{oid}','������');
	$label[]=array('{amount}','���');
	}
	elseif ($templates_name=='set_editpwd')
	{
	$label[]=array('{newpassword}','������');
	}
	elseif ($templates_name=='set_jobsallow' || $templates_name=='set_jobsnotallow')
	{
	$label[]=array('{jobsname}','ְλ����');
	}
	//-end
	if ($templates_name)
	{
		$sql = "select * from ".table('sms_templates')." where name='".$templates_name."'";
		$info=$db->getone($sql);
	}
	$info['thisname']=trim($_GET['thisname']);
	$smarty->assign('info',$info);
	$smarty->assign('label',$label);
	$smarty->assign('navlabel','templates');
	$smarty->display('sms/admin_sms_templates_edit.htm');
}
elseif($act == 'templates_save')
{
	check_token();
	$templates_value=trim($_POST['templates_value']);
	$templates_name=trim($_POST['templates_name']);
	!$db->query("UPDATE ".table('sms_templates')." SET value='{$templates_value}' WHERE name='{$templates_name}'")?adminmsg('����ʧ��', 1):"";
	$link[0]['text'] = "������һҳ";
	$link[0]['href'] ="?act=set_tpl";
	refresh_cache('sms_templates');
	//��д����Ա��־
	write_log("��̨�ɹ�����ģ�壡", $_SESSION['admin_name'],3);
	adminmsg("����ɹ���",2,$link);
}
elseif($act == 'send')
{
	get_token();
	$smarty->assign('pageheader',"����Ӫ��");
	
	require_once(dirname(__FILE__).'/include/admin_smsqueue_fun.php');
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$uid=intval($_GET['uid']);
	$mobile=trim($_GET['mobile']);
	
	$wheresql=' WHERE s_uid='.$uid.' ORDER BY s_id DESC ';
	$total_sql="SELECT COUNT(*) AS num FROM ".table('smsqueue').$wheresql;
	$perpage=10;
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$sms_log = get_smsqueue($offset,$perpage,$wheresql);
	
	$url=trim($_REQUEST['url']);
	if (empty($url))
	{
	$url="?act=send&mobile={$mobile}&uid={$uid}";
	}
	$smarty->assign('url',$url);
	$smarty->assign('smslog',$sms_log);
	$smarty->assign('page',$page->show(3));
	$smarty->display('sms/admin_sms_send.htm');
}
elseif($act == 'sms_send')
{
	check_token();
	$txt=trim($_POST['txt']);
	$mobile=trim($_POST['mobile']);
	$uid=intval($_POST['uid']);
	$url=trim($_REQUEST['url']);
	if (!$uid)
	{
	adminmsg('�û�UID����',0);
	}
	if (empty($txt))
	{
	adminmsg('�������ݲ���Ϊ�գ�',0);
	}
	if (empty($mobile))
	{
	adminmsg('�ֻ�����Ϊ�գ�',0);
	}
	if (!preg_match("/^(13|15|14|17|18)\d{9}$/",$mobile))
	{
		$link[0]['text'] = "������һҳ";
		$link[0]['href'] = "{$url}";
		adminmsg("����ʧ�ܣ�<strong>{$mobile}</strong> ���Ǳ�׼���ֻ��Ÿ�ʽ",1,$link);
		
	}
	else
	{
			$setsqlarr['s_uid']=$uid;
			$setsqlarr['s_mobile']=$mobile;
			$setsqlarr['s_body']=$txt;
			$setsqlarr['s_addtime']=time();
			$r=free_send_sms($mobile,$txt);
			if ($r=="success")
			{
				$setsqlarr['s_sendtime']=time();
				$setsqlarr['s_type']=1;//���ͳɹ�
				$db->inserttable(table('smsqueue'),$setsqlarr);
				unset($setsqlarr);
				//��д����Ա��־
				write_log("��̨�ɹ����Ͷ��ţ�", $_SESSION['admin_name'],3);
				$link[0]['text'] = "������һҳ";
				$link[0]['href'] = "{$url}";
				adminmsg("���ͳɹ���",2,$link);
			}
			else
			{
				$setsqlarr['s_sendtime']=time();
				$setsqlarr['s_type']=2;//����ʧ��
				$db->inserttable(table('smsqueue'),$setsqlarr);
				unset($setsqlarr);
				$link[0]['text'] = "������һҳ";
				$link[0]['href'] = "{$url}";
				adminmsg("����ʧ�ܣ�����δ֪��",0,$link);
			}
	}
}
elseif ($act=='again_send')
{
	$id=intval($_GET['id']);
	if (empty($id))
	{
	adminmsg("��ѡ��Ҫ���͵���Ŀ��",1);
	}
	$result = $db->getone("SELECT * FROM ".table('smsqueue')." WHERE  s_id = {$id} limit 1");
	$wheresql=" s_id={$id} ";
	$r=free_send_sms($result['s_mobile'],$result['s_body']);
	if ($r=='success')
	{
		$setsqlarr['s_sendtime']=time();
		$setsqlarr['s_type']=1;//���ͳɹ�
		!$db->updatetable(table('smsqueue'),$setsqlarr,$wheresql);
		//��д����Ա��־
		write_log("��̨�ɹ�������Ŀ��", $_SESSION['admin_name'],3);
		adminmsg('���ͳɹ�',2);
	}else{
		$setsqlarr['s_sendtime']=time();
		$setsqlarr['s_type']=2;
		!$db->updatetable(table('smsqueue'),$setsqlarr,$wheresql);
		adminmsg('����ʧ��',0);
	}
		
}
elseif ($act=='del')
{
	$id=$_POST['id'];
	if (empty($id))
	{
	adminmsg("��ѡ����Ŀ��",1);
	}
	if(!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	$db->query("Delete from ".table('smsqueue')." WHERE s_id IN ({$sqlin}) ");
	//��д����Ա��־
	write_log("��̨�ɹ�ɾ����Ŀ��", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�",2);
	}
}
//�����ײ�
elseif($act == 'meal')
{
    $sql = "select * from ".table('sms_setmeal');
	$setmeal =  $db->getall($sql);
	$smarty->assign('setmeal',$setmeal);	
	$smarty->assign('navlabel','meal'); 
	$smarty->display("sms/admin_sms_meal.htm");
}
elseif($act == 'set_meal_add')
{
	$smarty->assign('navlabel','meal');
	$smarty->display("sms/admin_sms_meal_add.htm");
}
elseif($act == 'set_meal_add_save')
{
	$setsqlarr['setmeal_name'] = trim($_POST['setmeal_name']);
	$setsqlarr['num'] = intval($_POST['num']);
	$setsqlarr['show_order'] = intval($_POST['show_order']);
	$setsqlarr['display'] = intval($_POST['display']);
	$setsqlarr['expense'] = trim($_POST['expense']);
	if($db->inserttable(table('sms_setmeal'),$setsqlarr)){
		//��д����Ա��־
		write_log("��̨�ɹ���Ӷ����ײͣ�", $_SESSION['admin_name'],3);
		adminmsg('��ӳɹ���',2);
	}else{
		adminmsg('���ʧ��',1);
	}  
}
elseif($act == 'set_meal_edit')
{
	$id = intval($_GET['id'])?intval($_GET['id']):exit('��������ȷ��');
	$sql = "select * from ".table('sms_setmeal')." where id = $id";
	$show =  $db->getone($sql);
	$smarty->assign('show',$show);
	$smarty->assign('navlabel','meal');
	$smarty->display("sms/admin_sms_meal_edit.htm");
}
elseif($act == 'set_meal_edit_save')
{	 
	$setsqlarr['setmeal_name'] = trim($_POST['setmeal_name']);
	$setsqlarr['num'] = intval($_POST['num']);
	$setsqlarr['show_order'] = intval($_POST['show_order']);
	$setsqlarr['display'] = intval($_POST['display']);
	$setsqlarr['expense'] = trim($_POST['expense']);
	if($db->updatetable(table('sms_setmeal'),$setsqlarr,' id = '.intval($_POST['id']))){
		//��д����Ա��־
		write_log("��̨�ɹ��޸Ķ����ײͣ�", $_SESSION['admin_name'],3);
		adminmsg('�޸ĳɹ���',2);
	}else{
		adminmsg('�޸�ʧ��',1);
	}  
}
elseif($act == 'set_meal_del')
{
	$id=intval($_GET['id']);

	if($db->query("delete from ".table("sms_setmeal")." where id=$id "))
	{
		//��д����Ա��־
		write_log("ɾ�������ײͳɹ���", $_SESSION['admin_name'],3);
		adminmsg('ɾ�������ײͳɹ�',2);
	}
	else
	{
		adminmsg('ɾ�������ײ�ʧ��',1);
	}
}
?>