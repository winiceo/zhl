<?php
 /*
 * 74cms ������΢��Ƹ����ģ��
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
require_once(dirname(__FILE__).'/weixin_share.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'add';
if ($act == 'add')
{
	$smarty->cache = false;
	$smarty->display('m/simple-add.html');
}
elseif($act == 'add_save')
{
	$captcha=get_cache('captcha');
	$_POST=array_map("utf8_to_gbk", $_POST);
	$setsqlarr['audit']=intval($_CFG['simple_add_audit']);
	$setsqlarr['jobname']=trim($_POST['jobname'])?trim($_POST['jobname']):exit('��û����дְλ���ƣ�');
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['comname']=trim($_POST['comname'])?trim($_POST['comname']):exit('��û����д��˾���ƣ�');
	$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):exit('��û����д��ϵ�ˣ�');
	$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):exit('��û����д��ϵ�绰��');
	if(preg_match("/^\d*$/",$setsqlarr['tel']))
	{
		if ($captcha['simple_tel_repeat']=='0')
		{
			$sql = "select id from ".table('simple')." where tel = '{$setsqlarr['tel']}' LIMIT 1";
			$info=$db->getone($sql);
			if (!empty($info))
			{
				exit('�绰�����Ѿ����ڣ�');
			}
		}
	}
	else
	{
		exit('�绰�����ʽ����');
	}
	$setsqlarr['subsite_id']=intval($_POST['subsite_id'])?intval($_POST['subsite_id']):exit('��û��ѡ�������');
	$setsqlarr['district']=intval($_POST['district'])?intval($_POST['district']):exit('��û��ѡ�������');
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn'])?trim($_POST['district_cn']):exit('��û��ѡ�������');
	$setsqlarr['sdistrict_cn']=trim($_POST['sdistrict_cn']);
	$setsqlarr['detailed']=trim($_POST['detailed'])?trim($_POST['detailed']):exit('��û����д����Ҫ��');
	$setsqlarr['addtime']=time();
	$setsqlarr['refreshtime']=time();
	$setsqlarr['deadline']=0;
	$validity=intval($_POST['validity']);
	if ($validity>0)
	{
	$setsqlarr['deadline']=strtotime("{$validity} day");
	}
	$setsqlarr['pwd']=trim($_POST['pwd'])?trim($_POST['pwd']):exit('��û����д�������룡');
	$setsqlarr['pwd_hash']=substr(md5(uniqid().mt_rand()),mt_rand(0,6),6);
	$setsqlarr['pwd']=md5(md5($setsqlarr['pwd']).$setsqlarr['pwd_hash'].$QS_pwdhash);
	$setsqlarr['addip']=$online_ip;
	$setsqlarr['likekey']=$setsqlarr['jobname'].",".$setsqlarr['comname'].",".$setsqlarr['address'].",".$setsqlarr['detailed'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']=$setsqlarr['jobname'].$setsqlarr['comname'].$setsqlarr['address'].$setsqlarr['detailed'];
	$setsqlarr['key']="{$setsqlarr['jobname']} {$setsqlarr['comname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if($db->inserttable(table('simple'),$setsqlarr))
	{
		exit("ok");
	}
	else
	{
		exit("���ʧ�ܣ�");
	}
}
elseif($act == 'edit')
{
	$smarty->cache = false;
	$row=simple_jobs_one($_GET['id']);
	$smarty->assign('show',$row);
	$smarty->display('m/simple-edit.html');
}
elseif($act == "edit_save")
{
	$captcha=get_cache('captcha');
	$_POST=array_map("utf8_to_gbk", $_POST);
	$id=intval($_POST['id']);
	$pwd=trim($_POST['pwd']);
	$info=$db->getone("select * from ".table('simple')." where id = '{$id}' LIMIT 1");
	$thispwd=md5(md5($pwd).$info['pwd_hash'].$QS_pwdhash);
	if ($thispwd!=$info['pwd'])
	{
		exit("�����������");
	}
	if ($_CFG['simple_edit_audit']!="-1")
	{
	$setsqlarr['audit']=intval($_CFG['simple_edit_audit']);
	}
	$setsqlarr['jobname']=trim($_POST['jobname'])?trim($_POST['jobname']):exit('��û����дְλ���ƣ�');
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['comname']=trim($_POST['comname'])?trim($_POST['comname']):exit('��û����д��˾���ƣ�');
	$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):exit('��û����д��ϵ�ˣ�');
	if ($_CFG['simple_tel_edit']=="1")
	{
		$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):exit('��û����д��ϵ�绰��');
		if(preg_match("/^\d*$/",$setsqlarr['tel']))
		{
			if ($captcha['simple_tel_repeat']=='0')
			{
				$sql = "select id from ".table('simple')." where tel = '{$setsqlarr['tel']}' AND id<>'{$id}' LIMIT 1";
				$info=$db->getone($sql);
				if (!empty($info))
				{
					exit('�绰�����Ѿ����ڣ�');
				}
			}
		}	
	}
	$setsqlarr['subsite_id']=intval($_POST['subsite_id'])?intval($_POST['subsite_id']):exit('��û��ѡ�������');
	$setsqlarr['district']=intval($_POST['district'])?intval($_POST['district']):exit('��û��ѡ�������');
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['sdistrict_cn']=trim($_POST['sdistrict_cn']);
	$setsqlarr['detailed']=trim($_POST['detailed'])?trim($_POST['detailed']):exit('��û����д����Ҫ��');
	$setsqlarr['refreshtime']=time();
	$days=intval($_POST['days']);
	if ($days>0)
	{
	$time=$info['deadline']>time()?$info['deadline']:time();
	$setsqlarr['deadline']=strtotime("{$days} day",$time);
	}
	$setsqlarr['likekey']=$setsqlarr['jobname'].",".$setsqlarr['comname'].",".$setsqlarr['address'].",".$setsqlarr['detailed'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']=$setsqlarr['jobname'].$setsqlarr['comname'].$setsqlarr['address'].$setsqlarr['detailed'];
	$setsqlarr['key']="{$setsqlarr['jobname']} {$setsqlarr['comname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if($db->updatetable(table('simple'),$setsqlarr," id='{$id}' "))
	{
		exit("ok");
	}
	else
	{
		exit("�޸�ʧ�ܣ�");
	}
}
// ˢ��΢��Ƹ
elseif($act == "refresh")
{
	$jobid = intval($_GET['jobid'])?intval($_GET['jobid']):exit("��������");
	$pass = trim($_GET['pass'])?trim($_GET['pass']):exit("��������");
	$sql = "select * from ".table('simple')." where id = '{$jobid}' LIMIT 1";
	$info=$db->getone($sql);
	$thispwd=md5(md5($pass).$info['pwd_hash'].$QS_pwdhash);
	if ($thispwd==$info['pwd'])
	{
		$db->query("update ".table('simple')."  SET refreshtime='".time()."' WHERE id = '{$jobid}'");
		exit("ˢ�³ɹ���");
	}
	else
	{
		exit("�����������");
	}
}
// ɾ��΢��Ƹ
elseif($act == "delete")
{
	$jobid = intval($_GET['jobid'])?intval($_GET['jobid']):exit("��������");
	$pass = trim($_GET['pass'])?trim($_GET['pass']):exit("��������");
	$sql = "select * from ".table('simple')." where id = '{$jobid}' LIMIT 1";
	$info=$db->getone($sql);
	$thispwd=md5(md5($pass).$info['pwd_hash'].$QS_pwdhash);
	if ($thispwd==$info['pwd'])
	{
		$db->query("Delete from ".table('simple')." WHERE id = '{$jobid}'");
		exit("ɾ���ɹ���");
	}
	else
	{
		exit("�����������");
	}
}

?>