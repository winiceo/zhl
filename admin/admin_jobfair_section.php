<?php
 /*
 * 74cms ��Ƹ��
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_jobfair_fun.php');
require_once(ADMIN_ROOT_PATH.'include/upload.php');
$ads_updir="../data/comads/";
$ads_dir=$_CFG['site_dir']."data/comads/";
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';
$smarty->assign('act',$act);
if($act == 'list')
{	
	check_permissions($_SESSION['admin_purview'],"jobfair");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	$oederbysql=" order BY id DESC";
	if ($key && $key_type>0)
	{
		
		if     ($key_type==1)$wheresql=" WHERE title like '%{$key}%'";
	}
	else
	{
		if (!empty($_GET['settr']))
		{
			$settr=strtotime("-".intval($_GET['settr'])." day");
			$wheresql=empty($wheresql)?" WHERE addtime> ".$settr:$wheresql." AND addtime> ".$settr;
		}
	}
	if (intval($_CFG['subsite_id'])>0)
	{
		$wheresql.=empty($wheresql)?" WHERE ":" AND ";
		$wheresql.=" subsite_id=".intval($_CFG['subsite_id'])." ";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('jobfair_section')." ".$wheresql;
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('list',get_jobfair_section($offset, $perpage,$wheresql.$oederbysql));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('time',time());
	$smarty->assign('pageheader',"ר����Ƹ��");
	get_token();
	$smarty->display('jobfair/admin_jobfair_section.htm');
}
elseif($act =='jobfair_section_del')
{
	$id=$_REQUEST['id'];
	if (empty($id)) adminmsg("��ѡ����Ŀ��",1);
	if ($n=del_jobfair_section($id))
	{
	adminmsg("ɾ���ɹ���Ӱ������ {$n}",2);
	}
}
elseif($act =='jobfair_add')
{
	check_permissions($_SESSION['admin_purview'],"jobfair");
	$smarty->assign('pageheader',"���ר����Ƹ��");
	$smarty->assign('subsite',get_subsite_list(intval($_CFG['subsite_id'])));
	get_token();
	$smarty->display('jobfair/admin_jobfair_section_add.htm');
 
}
elseif($act == 'addsave')
{
	check_permissions($_SESSION['admin_purview'],"jobfair");
	check_token();
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);	
	$setsqlarr['title']=!empty($_POST['title'])?trim($_POST['title']):adminmsg('��û����д���⣡',1);
	$setsqlarr['trade']=trim($_POST['trade'])?trim($_POST['trade']):adminmsg('��ѡ����Ƹ����ҵ��',1);
	$setsqlarr['trade_cn']=trim($_POST['trade_cn']);
	
	$tradearr = explode(',', $setsqlarr['trade']);
	if(count($tradearr)>1)
	{
		$setsqlarr['is_singleness']=2;
	}else
	{
		$setsqlarr['is_singleness']=1;
	}
	$setsqlarr['addtime']=time();
	$insertid = $db->inserttable(table('jobfair_section'),$setsqlarr,1);
	if ($insertid)
	{
	write_log("���ר����Ƹ�᣺".$setsqlarr['title'], $_SESSION['admin_name'],3);
	$link[0]['text'] = "���ר����Ƹ�����ҵ";
	$link[0]['href'] = '?act=jobfair_section_show&id='.$insertid;
	adminmsg("��ӳɹ���",2,$link);	
	}
	else
	{
	adminmsg("���ʧ�ܣ�",0);
	}	
}
elseif($act == "editsave")
{
	$jobfair_id=intval($_POST['jobfair_id'])>0?intval($_POST['jobfair_id']):adminmsg("��Ƹ��ID��ʧ��",1);

	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);	
	$setsqlarr['title']=!empty($_POST['title'])?trim($_POST['title']):adminmsg('��û����д���⣡',1);
	$setsqlarr['trade']=trim($_POST['trade'])?trim($_POST['trade']):adminmsg('��ѡ����Ƹ����ҵ��',1);
	$setsqlarr['trade_cn']=trim($_POST['trade_cn']);
	$tradearr = explode(',', $setsqlarr['trade']);
	if(count($tradearr)>1)
	{
		$setsqlarr['is_singleness']=2;
	}else
	{
		$setsqlarr['is_singleness']=1;
	}
	$setsqlarr['addtime']=time();
	if ($db->updatetable(table('jobfair_section'),$setsqlarr,array('id'=>$jobfair_id)))
	{
	$db->query("delete from ".table('jobfair_section_company')." where trade not in({$setsqlarr['trade']}) and jobfair_id=$jobfair_id ");
	adminmsg("�޸ĳɹ���",2);	
	}
	else
	{
	adminmsg("�޸�ʧ�ܣ�",0);
	}	
}
// ר����Ƹ����ϸ
elseif($act == 'jobfair_section_show')
{
	$id=intval($_GET['id'])>0?intval($_GET['id']):adminmsg('id��ʧ��',0);
	$show = get_jobfair_section_one($id);
	// �λ���ҵ�б�
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$joinsql =" left join ".table('company_profile')." as c on j.company_id=c.id ";
	$wheresql=" where j.jobfair_id=$id ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('jobfair_section_company')." as j ".$wheresql;
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$company_list = get_jobfair_section_company($offset, $perpage,$joinsql.$wheresql);
	$smarty->assign('company_list',$company_list);

	foreach ($company_list as $key => $value) {
		$company_id_str .=','.$value['company_id'];
	}
	$company_id_str =ltrim($company_id_str,',');
	$smarty->assign('company_id_str',$company_id_str);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('show',$show);
	$smarty->assign('subsite',get_subsite_list(intval($_CFG['subsite_id'])));
	
	// �λ���ҵ�� ��ҵ
	$company_select = $db->getall("select companyname,id,trade,trade_cn from ".table('company_profile')." where trade in ($show[trade])");
	$trade_arr = explode(',', $show['trade_cn']);
	foreach ($trade_arr as $key => $value) {
		$trade_arr_[$value]='';
	}
	foreach ($company_select as $key => $value) {
		$value['companyname'] = cut_str($value['companyname'],10,0,"...");
		$trade_arr_[$value['trade_cn']][]=$value;

	}
	$trade_arr_title= array_keys($trade_arr_);
	$trade_arr_com= array_values($trade_arr_);
	$smarty->assign('trade_arr_title',$trade_arr_title);
	$smarty->assign('trade_arr_com',$trade_arr_com);


	$smarty->assign('pageheader',"ר����Ƹ����ϸ");
	$smarty->display('jobfair/admin_jobfair_section_show.htm');
}
elseif($act == 'jobfair_section_company_add')
{
	global $db;
	$jobfair_id= intval($_POST['jobfair_id'])>0?intval($_POST['jobfair_id']):exit("err1");
	$company_id=trim($_POST['company_id'])?trim($_POST['company_id']):exit('err2');
	$trade_id=trim($_POST['trade_id'])?trim($_POST['trade_id']):exit('err2');
	$company_arr =explode(',', $company_id);
	$trade_arr =explode(',', $trade_id);
	// ��ɾ�� 
	$db->query("delete from ".table('jobfair_section_company')." where jobfair_id=$jobfair_id and company_id not in($company_id) ");
	foreach ($company_arr as $key => $value) {
		$company = $db->getone("select id from ".table('jobfair_section_company')." where company_id=$value and jobfair_id=$jobfair_id limit 1 ");
		if(!empty($company))
		{
			continue;
		}
		$db->inserttable(table('jobfair_section_company'),array('jobfair_id'=>$jobfair_id,'company_id'=>$value,'trade'=>$trade_arr[$key],'addtime'=>time()));
	}
	exit("ok");
}
// �޸Ĳλ���ҵ ǩ��
elseif($act == 'jobfair_section_company_edit')
{
	global $db;
	$jobfair_id=intval($_POST['jobfair_id'])>0?intval($_POST['jobfair_id']):adminmsg("��Ƹ��ID��ʧ��",1);
	foreach ($_POST['company_signature'] as $key => $value) 
	{
		$settr['company_signature']=$value;
		$db->updatetable(table('jobfair_section_company'),$settr,array('jobfair_id'=>$jobfair_id,'id'=>$key));
	}
	adminmsg("����ɹ���",2);
}
// ɾ���λ���ҵ
elseif($act == 'jobfair_section_company_del')
{
	$id=$_REQUEST['id'];
	if (empty($id)) adminmsg("��ѡ����Ŀ��",1);
	if ($n=del_jobfair_section_company($id))
	{
	adminmsg("ɾ���ɹ���Ӱ������ {$n}",2);
	}
}

elseif($act == 'exhibitors')
{	
	check_permissions($_SESSION['admin_purview'],"jobfair_exhibitors");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	$oederbysql=" order BY id DESC";
	if ($key && $key_type>0)
	{
		
		if     ($key_type==1)$wheresql=" WHERE companyname like '%{$key}%'";
		if     ($key_type==2)$wheresql=" WHERE jobfair_title like '%{$key}%'";
	}
	else
	{
		if ($_GET['predetermined_status']<>'')
		{
		$wheresql=" WHERE audit=".intval($_GET['audit']);
		}
		if (!empty($_GET['etypr']))
		{
			$wheresql=empty($wheresql)?" WHERE etypr=".intval($_GET['etypr']):$wheresql." AND etypr> ".intval($_GET['etypr']);
		}
		if (!empty($_GET['settr']))
		{
			$settr=strtotime("-".intval($_GET['settr'])." day");
			$wheresql=empty($wheresql)?" WHERE eaddtime> ".$settr:$wheresql." AND eaddtime> ".$settr;
		}
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('jobfair_exhibitors')." ".$wheresql;
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('list',get_jobfair_exhibitors($offset, $perpage,$wheresql.$oederbysql));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('time',time());
	$smarty->assign('pageheader',"�λ���ҵ");
	get_token();
	$smarty->display('jobfair/admin_jobfair_exhibitors.htm');
}
elseif($act =='exhibitors_del')
{
	check_permissions($_SESSION['admin_purview'],"jobfair_exhibitors");
	$id=$_REQUEST['id'];
	if (empty($id)) adminmsg("��ѡ����Ŀ��",1);
	check_token();
	if ($n=del_exhibitors($id))
	{
	adminmsg("ɾ���ɹ���Ӱ������ {$n}",2);
	}
}
elseif($act =='exhibitors_audit')
{
	check_permissions($_SESSION['admin_purview'],"jobfair_exhibitors");
	$id=$_REQUEST['id'];
	if (empty($id)) adminmsg("��ѡ����Ŀ��",1);
	if ($n=edit_exhibitors_audit($id,$_POST['audit']))
	{
	adminmsg("���óɹ���Ӱ������ {$n}",2);
	}
}
elseif($act =='exhibitors_add')
{
	check_permissions($_SESSION['admin_purview'],"jobfair_exhibitors");
	get_token();
	$smarty->assign('pageheader',"�λ���ҵ");	
	$smarty->assign('jobfair',get_jobfair_audit());
	$smarty->display('jobfair/admin_jobfair_exhibitors_add.htm'); 
}
elseif($act == 'exhibitors_add_save')
{
	check_permissions($_SESSION['admin_purview'],"jobfair_exhibitors");
	check_token();	
	//�жϸ���ҵ�Ƿ��˲����û������˲����û�
	$t=$_POST['t'];
	if ($t=="2")
	{
		$setsqlarr['companyname']=!empty($_POST['companyname'])?trim($_POST['companyname']):adminmsg('��û����ҵ���ƣ�',1);
	}
	elseif ($t=="1")
	{
		$comid=intval($_POST['comid']);
		if ($comid==0)
		{
			adminmsg('��ѡ����ҵ��',1);
		}
		else
		{
			$sql = "select * from ".table('company_profile')." where id={$comid} LIMIT 1";
			$info=$db->getone($sql);
			if (empty($info))
			{
			adminmsg('��ҵ�����ڣ�',1);
			}
			$setsqlarr['uid']=$info['uid'];
			$setsqlarr['companyname']=$info['companyname'];
			$setsqlarr['company_id']=$info['id'];
			$setsqlarr['company_addtime']=$info['addtime'];
		}
	}
	$setsqlarr['audit']=intval($_POST['audit']);
	$setsqlarr['etypr']=intval($_POST['etypr']);	
	$setsqlarr['eaddtime']=time();
	$setsqlarr['note']=trim($_POST['note']);
	//�鿴ѡ�����Ƹ���Ƿ����
	$setsqlarr['jobfairid']=intval($_POST['jobfairid']);
	$sql = "select * from ".table('jobfair')." where id={$setsqlarr['jobfairid']} LIMIT 1";
	$jobfair=$db->getone($sql);
	if (empty($jobfair))
	{
	adminmsg('��Ƹ�᲻����',1);
	}
	$setsqlarr['jobfair_title']=$jobfair['title'];
	$setsqlarr['jobfair_addtime']=$jobfair['addtime'];
	//�жϸ���ҵ�Ƿ��Ѿ�Ԥ��������Ƹ��
	$is_join = $db->getone("select * from ".table('jobfair_exhibitors')." where companyname='".trim($setsqlarr['companyname'])."' and jobfairid=".$setsqlarr['jobfairid']." limit 1 ");
	if(!empty($is_join))
	{
		adminmsg('����ҵ�Ѿ�Ԥ��������Ƹ��',1);
	}
	if ($db->inserttable(table('jobfair_exhibitors'),$setsqlarr))
	{
	write_log("��Ӳλ���ҵ��".$setsqlarr['companyname'], $_SESSION['admin_name'],3);
	$link[0]['text'] = "�������";
	$link[0]['href'] = '?act=exhibitors_add';
	$link[1]['text'] = "�����б�";
	$link[1]['href'] = '?act=exhibitors';
	adminmsg("��ӳɹ���",2,$link);	
	}
	else
	{
	adminmsg("���ʧ�ܣ�",0);
	}	
}
elseif($act == 'exhibitors_edit')
{
	check_permissions($_SESSION['admin_purview'],"jobfair_exhibitors");
	get_token();
	$id=intval($_GET['id']);
	$sql = "select * from ".table('jobfair_exhibitors')." where id='{$id}' LIMIT 1";
	$info=$db->getone($sql);
	if ($info['uid']>0)
	{
	$info['company_url']=url_rewrite('QS_companyshow',array('id'=>$info['company_id']));
	}
	$smarty->assign('info',$info);
	$smarty->assign('jobfair',get_jobfair_audit());
	$smarty->assign('pageheader',"��Ƹ��");	
	$smarty->display('jobfair/admin_jobfair_exhibitors_edit.htm');
}
elseif($act == 'exhibitors_edit_save')
{
	check_permissions($_SESSION['admin_purview'],"jobfair_exhibitors");
	check_token();
	$id=intval($_POST['id']);
	$setsqlarr['companyname']=!empty($_POST['companyname'])?trim($_POST['companyname']):adminmsg('��û����ҵ���ƣ�',1);	 
	$setsqlarr['audit']=intval($_POST['audit']);
	$setsqlarr['etypr']=intval($_POST['etypr']);	
	$setsqlarr['note']=trim($_POST['note']);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=exhibitors';
	write_log("�޸�idΪ��".$id."�Ĳλ���ҵ��Ϣ", $_SESSION['admin_name'],3);
	!$db->updatetable(table('jobfair_exhibitors'),$setsqlarr," id=".$id."")?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
}
?>