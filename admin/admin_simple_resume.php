<?php
 /*
 * 74cms ΢����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_simple_resume_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
$smarty->assign('act',$act);
$smarty->assign('pageheader',"΢����");
if($act == 'list')
{
	check_permissions($_SESSION['admin_purview'],"simple_resume_list");	
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	$orderbysql=" order BY  `is_hot` DESC , `refreshtime` DESC";
	if ($key && $key_type>0)
	{
		
		if     ($key_type==1)$wheresql=" WHERE uname like '%{$key}%'";
		if     ($key_type==3)$wheresql=" WHERE tel ='{$key}'";
		$orderbysql="";
	}
	else
	{
		if (!empty($_GET['audit']))
		{
		$wheresql=" WHERE audit=".intval($_GET['audit']);
		}
		if (!empty($_GET['addtime']))
		{
			$settr=strtotime("-".intval($_GET['addtime'])." day");
			$wheresql=empty($wheresql)?" WHERE addtime> ".$settr:$wheresql." AND addtime> ".$settr;
		}
		if ($_GET['deadline']<>'')
		{
			$deadline=intval($_GET['deadline']);
			$time=time();			
			if ($deadline==0)
			{			
			$wheresql=empty($wheresql)?" WHERE deadline< {$time} AND deadline<>0 ":"{$wheresql} AND deadline< {$time} AND deadline<>0 ";
			}
			else
			{
			$settr=strtotime("+{$deadline} day");
			$wheresql=empty($wheresql)?" WHERE deadline<{$settr} AND deadline>{$time} ":"{$wheresql} AND  deadline<{$settr} AND deadline>{$time}";
			}			
		}
		if (!empty($_GET['refreshtime']))
		{
			$settr=strtotime("-".intval($_GET['refreshtime'])." day");
			$wheresql=empty($wheresql)?" WHERE refreshtime> ".$settr:$wheresql." AND refreshtime> ".$settr;
		}
		//�Ƿ��ö�
		if (!empty($_GET['is_hot']))
		{
		$wheresql=" WHERE is_hot=".intval($_GET['is_hot']);
		}
	}
	if (intval($_CFG['subsite_id'])>0)
	{
		$wheresql.=empty($wheresql)?" WHERE ":" AND ";
		$wheresql.=" subsite_id=".intval($_CFG['subsite_id'])." ";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('simple_resume').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_simple_resume_list($offset,$perpage,$wheresql.$orderbysql);
	$smarty->assign('key',$key);
	$smarty->assign('total',$total_val);
	$smarty->assign('list',$list);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('navlabel','list');
	$smarty->display('simple_resume/admin_simple.htm');
}
elseif($act == 'simple_del')
{
	check_permissions($_SESSION['admin_purview'],"simple_resume_del");
	check_token();
	$id=$_REQUEST['id'];
	if (empty($id))
	{
	adminmsg("��û��ѡ����Ŀ��",1);
	}
	if ($num=simple_del($id))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'simple_refresh')
{
	check_permissions($_SESSION['admin_purview'],"simple_resume_refresh");
	check_token();
	$id=$_REQUEST['id'];
	if (empty($id))
	{
	adminmsg("��û��ѡ����Ŀ��",1);
	}
	if ($num=simple_refresh($id))
	{
	adminmsg("ˢ�³ɹ�����ˢ�� {$num}�� ",2);
	}
	else
	{
	adminmsg("ˢ��ʧ�ܣ�",0);
	}
}
elseif($act == 'jobs_perform')
{
	//���
	if(!empty($_POST['set_audit'])){
		check_permissions($_SESSION['admin_purview'],"simple_resume_audit");
		check_token();
		$id=$_REQUEST['id'];
		$audit=intval($_POST['audit']);
		if (empty($id))
		{
		adminmsg("��û��ѡ����Ŀ��",1);
		}
		if ($num=simple_resume_audit($id,$audit))
		{
		adminmsg("���óɹ�����Ӱ�� {$num}�� ",2);
		}
		else
		{
		adminmsg("����ʧ�ܣ�",0);
		}
	}
	//�ö�
	if(!empty($_POST['set_hot'])){
		check_token();
		$id=$_REQUEST['id'];
		$is_hot=intval($_POST['is_hot']);
		if (empty($id))
		{
		adminmsg("��û��ѡ����Ŀ��",1);
		}
		if ($num=simple_resume_hot($id,$is_hot))
		{
		adminmsg("���óɹ�����Ӱ�� {$num}�� ",2);
		}
		else
		{
		adminmsg("����ʧ�ܣ�",0);
		}
	}
	
}
elseif($act == 'simple_add')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"simple_resume_add");
	$smarty->assign('navlabel','add');
	$smarty->assign('subsite',get_subsite_list(intval($_CFG['subsite_id'])));
	$smarty->display('simple_resume/admin_simple_add.htm');
}
elseif($act == 'simple_add_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"simple_resume_add");
	$setsqlarr['audit']=1;
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$setsqlarr['uname']=trim($_POST['uname'])?trim($_POST['uname']):adminmsg('��û����д������',1);
	$setsqlarr['age']=intval($_POST['age'])?intval($_POST['age']):adminmsg('��û����д���䣡',1);
	$setsqlarr['sex']=intval($_POST['sex']);
	switch($setsqlarr['sex']){
		case 1:$setsqlarr['sex_cn']="��";break;
		case 2:$setsqlarr['sex_cn']="Ů";break;
	}
	$setsqlarr['district']=intval($_POST['district'])?intval($_POST['district']):adminmsg('��û��ѡ�������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict'])?intval($_POST['sdistrict']):adminmsg('��û��ѡ�������',1);
	$district_cn = explode("/",trim($_POST['district_cn']));
	$setsqlarr['district_cn']=$district_cn[0];
	$setsqlarr['sdistrict_cn']=$district_cn[1];
	$setsqlarr['category']=trim($_POST['category'])?trim($_POST['category']):adminmsg('��û�����빤�֣�',1);
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):adminmsg('��û��ѡ�������飡',1);
	$experience = $db->getone("select c_name from ".table('category')." where c_id=".$setsqlarr['experience']);
	$setsqlarr['experience_cn']=$experience['c_name'];
	$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):adminmsg('��û����д��ϵ�绰��',1);
	$setsqlarr['detailed']=trim($_POST['detailed']);
	$setsqlarr['addtime']=time();
	$setsqlarr['refreshtime']=time();
	$setsqlarr['deadline']=0;
	$validity=intval($_POST['validity']);
	if ($validity>0)
	{
	$setsqlarr['deadline']=strtotime("{$validity} day");
	}
	$setsqlarr['pwd']=trim($_POST['pwd'])?trim($_POST['pwd']):adminmsg('��û����д�������룡',1);
	$setsqlarr['pwd_hash']=substr(md5(uniqid().mt_rand()),mt_rand(0,6),6);
	$setsqlarr['pwd']=md5(md5($setsqlarr['pwd']).$setsqlarr['pwd_hash'].$QS_pwdhash);
	$setsqlarr['addip']=$online_ip;
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']=$setsqlarr['uname'].$setsqlarr['address'].$setsqlarr['detailed'];
	$setsqlarr['key']="{$setsqlarr['uname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if($resumepid = $db->inserttable(table('simple_resume'),$setsqlarr,1))
	{
		//��д����Ա��־
		write_log("��̨���idΪ".$resumepid."��΢����", $_SESSION['admin_name'],3);
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = '?act=list';
		$link[1]['text'] = "�������";
		$link[1]['href'] = "?act=simple_add";
		adminmsg("��ӳɹ���",2,$link);
	}
	else
	{
		adminmsg("���ʧ�ܣ�",0);
	}	
}
elseif($act == 'simple_edit')
{
	get_token();
	$id=intval($_REQUEST['id']);
	if (empty($id))
	{
	adminmsg("��û��ѡ����Ŀ��",1);
	}
	check_permissions($_SESSION['admin_purview'],"simple_resume_edit");
	$sql = "select * from ".table('simple_resume')." where id = '{$id}' LIMIT 1";
	$show=$db->getone($sql);
	$show['district_cn'] = $show['district_cn']."/".$show['sdistrict_cn'];
	$smarty->assign('show',$show);
	$smarty->assign('subsite',get_subsite_list(intval($_CFG['subsite_id'])));
	$smarty->display('simple_resume/admin_simple_edit.htm');
}
elseif($act == 'simple_edit_save')
{
	$id=intval($_POST['id']);
	if (empty($id))
	{
	adminmsg("��û��ѡ����Ŀ��",1);
	}
	if ($_POST['pwd'])
	{
		$info=$db->getone("select * from ".table('simple_resume')." where id = '{$id}' LIMIT 1");
		$setsqlarr['pwd']=md5(md5($_POST['pwd']).$info['pwd_hash'].$QS_pwdhash);
	}
	$setsqlarr['uname']=trim($_POST['uname'])?trim($_POST['uname']):adminmsg('��û����д������',1);
	$setsqlarr['age']=intval($_POST['age'])?intval($_POST['age']):adminmsg('��û����д���䣡',1);
	$setsqlarr['sex']=intval($_POST['sex']);
	switch($setsqlarr['sex']){
		case 1:$setsqlarr['sex_cn']="��";break;
		case 2:$setsqlarr['sex_cn']="Ů";break;
	}
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$setsqlarr['district']=intval($_POST['district'])?intval($_POST['district']):adminmsg('��û��ѡ�������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict'])?intval($_POST['sdistrict']):adminmsg('��û��ѡ�������',1);
	$district_cn = explode("/",trim($_POST['district_cn']));
	$setsqlarr['district_cn']=$district_cn[0];
	$setsqlarr['sdistrict_cn']=$district_cn[1];
	$setsqlarr['category']=trim($_POST['category'])?trim($_POST['category']):adminmsg('��û����д���֣�',1);
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):adminmsg('��û��ѡ�������飡',1);
	$experience = $db->getone("select c_name from ".table('category')." where c_id=".$setsqlarr['experience']);
	$setsqlarr['experience_cn']=$experience['c_name'];
	
	$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):adminmsg('��û����д��ϵ�绰��',1);
	
	$setsqlarr['detailed']=trim($_POST['detailed']);
	$setsqlarr['refreshtime']=time();
	$days=intval($_POST['days']);
	if ($days>0)
	{
	$time=$_POST['olddeadline']>time()?$_POST['olddeadline']:time();
	$setsqlarr['deadline']=strtotime("{$days} day",$time);
	}
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']=$setsqlarr['uname'].$setsqlarr['address'].$setsqlarr['detailed'];
	$setsqlarr['key']="{$setsqlarr['uname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if($db->updatetable(table('simple_resume'),$setsqlarr," id='{$id}' "))
	{
		//��д����Ա��־
		write_log("��̨�޸�idΪ".$id."��΢����", $_SESSION['admin_name'],3);
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = '?act=list';
		adminmsg("�޸ĳɹ���",2,$link);
	}
	else
	{
	adminmsg("�޸�ʧ�ܣ�",0);
	}
}
?>