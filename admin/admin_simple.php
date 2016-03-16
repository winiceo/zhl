<?php
 /*
 * 74cms ΢��Ƹ
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_simple_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
$smarty->assign('act',$act);
$smarty->assign('pageheader',"΢��Ƹ");
if($act == 'list')
{
	check_permissions($_SESSION['admin_purview'],"simple_list");	
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	$orderbysql=" order BY `is_hot` DESC , `refreshtime` DESC";
	if ($key && $key_type>0)
	{
		
		if     ($key_type==1)$wheresql=" WHERE jobname like '%{$key}%'";
		if     ($key_type==2)$wheresql=" WHERE comname like '%{$key}%'";
		if     ($key_type==3)$wheresql=" WHERE tel ='{$key}'";
		if     ($key_type==4)$wheresql=" WHERE contact like '%{$key}%'";
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
	$total_sql="SELECT COUNT(*) AS num FROM ".table('simple').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_simple_list($offset,$perpage,$wheresql.$orderbysql);
	$smarty->assign('key',$key);
	$smarty->assign('total',$total_val);
	$smarty->assign('list',$list);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('navlabel','list');
	$smarty->display('simple/admin_simple.htm');
}
elseif($act == 'simple_del')
{
	check_permissions($_SESSION['admin_purview'],"simple_del");
	check_token();
	$id=$_REQUEST['id'];
	if (empty($id))
	{
	adminmsg("��û��ѡ����Ŀ��",1);
	}
	if ($num=simple_del($id))
	{
	write_log("ɾ��΢��Ȧ��ɾ��".$num."��", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'simple_refresh')
{
	check_permissions($_SESSION['admin_purview'],"simple_refresh");
	check_token();
	$id=$_REQUEST['id'];
	if (empty($id))
	{
	adminmsg("��û��ѡ����Ŀ��",1);
	}
	if ($num=simple_refresh($id))
	{
	write_log("ˢ��΢��Ȧ��ɾ��".$num."��", $_SESSION['admin_name'],3);
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
		check_permissions($_SESSION['admin_purview'],"simple_audit");
		check_token();
		$id=$_REQUEST['id'];
		$audit=intval($_POST['audit']);
		if (empty($id))
		{
		adminmsg("��û��ѡ����Ŀ��",1);
		}
		if ($num=simple_audit($id,$audit))
		{
		write_log("����΢��Ƹ���״̬Ϊ".$audit."��Ӱ�� {$num}��", $_SESSION['admin_name'],3);
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
		if ($num=simple_hot($id,$is_hot))
		{
		write_log("����΢��Ƹ�ö�״̬Ϊ".$is_hot."��Ӱ�� {$num}��", $_SESSION['admin_name'],3);
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
	check_permissions($_SESSION['admin_purview'],"simple_add");
	$smarty->assign('navlabel','add');
	$smarty->assign('subsite',get_subsite_list(intval($_CFG['subsite_id'])));
	$smarty->display('simple/admin_simple_add.htm');
}
elseif($act == 'simple_add_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"simple_add");
	$setsqlarr['audit']=1;
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$setsqlarr['jobname']=trim($_POST['jobname'])?trim($_POST['jobname']):adminmsg('��û����дְλ���ƣ�',1);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['comname']=trim($_POST['comname'])?trim($_POST['comname']):adminmsg('��û����д��λ���ƣ�',1);
	$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):adminmsg('��û����д��ϵ�ˣ�',1);
	$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):adminmsg('��û����д��ϵ�绰��',1);
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$district_cn = explode("/",trim($_POST['district_cn']));
	$setsqlarr['district_cn']=$district_cn[0];
	$setsqlarr['sdistrict_cn']=$district_cn[1];
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
	$setsqlarr['key']=$setsqlarr['jobname'].$setsqlarr['comname'].$setsqlarr['address'].$setsqlarr['detailed'];
	$setsqlarr['key']="{$setsqlarr['jobname']} {$setsqlarr['comname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if($db->inserttable(table('simple'),$setsqlarr))
	{
		//��д����Ա��־
		write_log("��̨���ְλ����Ϊ : ".$setsqlarr['jobname']."��΢��Ƹ ", $_SESSION['admin_name'],3);
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = '?act=list';
		$link[1]['text'] = "�������";
		$link[1]['href'] = "?act=simple_add";
		write_log("���΢��Ƹ��".$setsqlarr['jobname'], $_SESSION['admin_name'],3);
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
	check_permissions($_SESSION['admin_purview'],"simple_edit");
	$sql = "select * from ".table('simple')." where id = '{$id}' LIMIT 1";
	$show=$db->getone($sql);
	$show['district_cn'] = $show['district_cn']."/".$show['sdistrict_cn'];
	$smarty->assign('show',$show);
	$smarty->assign('subsite',get_subsite_list(intval($_CFG['subsite_id'])));
	$smarty->display('simple/admin_simple_edit.htm');
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
		$info=$db->getone("select * from ".table('simple')." where id = '{$id}' LIMIT 1");
		$setsqlarr['pwd']=md5(md5($_POST['pwd']).$info['pwd_hash'].$QS_pwdhash);
	}
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$setsqlarr['jobname']=trim($_POST['jobname'])?trim($_POST['jobname']):adminmsg('��û����дְλ���ƣ�',1);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['comname']=trim($_POST['comname'])?trim($_POST['comname']):adminmsg('��û����д��λ���ƣ�',1);
	$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):adminmsg('��û����д��ϵ�ˣ�',1);
	$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):adminmsg('��û����д��ϵ�绰��',1);
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$district_cn = explode("/",trim($_POST['district_cn']));
	$setsqlarr['district_cn']=$district_cn[0];
	$setsqlarr['sdistrict_cn']=$district_cn[1];
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
	$setsqlarr['key']=$setsqlarr['jobname'].$setsqlarr['comname'].$setsqlarr['address'].$setsqlarr['detailed'];
	$setsqlarr['key']="{$setsqlarr['jobname']} {$setsqlarr['comname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if($db->updatetable(table('simple'),$setsqlarr," id='{$id}' "))
	{
		//��д����Ա��־
		write_log("��̨�޸�idΪ".$id."��΢��Ƹ ", $_SESSION['admin_name'],3);
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = '?act=list';
		write_log("�޸�idΪ��".$id."΢��Ƹ��Ϣ", $_SESSION['admin_name'],3);
		adminmsg("�޸ĳɹ���",2,$link);
	}
	else
	{
	adminmsg("�޸�ʧ�ܣ�",0);
	}
}
?>