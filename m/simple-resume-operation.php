<?php
 /*
 * 74cms ������΢��������ģ��
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'add';
if ($act == 'add')
{
	$smarty->cache = false;
	$smarty->display('m/simple-resume-add.html');
}
elseif($act == 'add_save')
{
	$captcha=get_cache('captcha');
	$_POST=array_map("utf8_to_gbk", $_POST);
	$setsqlarr['audit']=intval($_CFG['simple_add_audit']);
	$setsqlarr['uname']=trim($_POST['uname'])?trim($_POST['uname']):exit('��û����д������');
	$setsqlarr['age']=intval($_POST['age'])?intval($_POST['age']):exit('��û����д���䣡');
	$setsqlarr['sex']=intval($_POST['sex']);
	switch($setsqlarr['sex']){
		case 1:$setsqlarr['sex_cn']="��";break;
		case 2:$setsqlarr['sex_cn']="Ů";break;
	}
	$setsqlarr['category']=trim($_POST['category'])?trim($_POST['category']):exit('��û����д����ְλ��');
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):exit('��û��ѡ�������飡');
	$experience = $db->getone("select c_name from ".table('category')." where c_id=".$setsqlarr['experience']);
	$setsqlarr['experience_cn']=$experience['c_name'];
	$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):exit('��û����д��ϵ�绰��');
	if(preg_match("/^\d*$/",$setsqlarr['tel']))
	{
		if ($captcha['simple_tel_repeat']=='0')
		{
			$sql = "select id from ".table('simple_resume')." where tel = '{$setsqlarr['tel']}' LIMIT 1";
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
	$setsqlarr['district']=intval($_POST['district'])?intval($_POST['district']):exit('��û��ѡ����������');
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['sdistrict_cn']=trim($_POST['sdistrict_cn']);
	$setsqlarr['detailed']=trim($_POST['detailed'])?trim($_POST['detailed']):exit('��û����д���˼�飡');
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
	$setsqlarr['likekey']=$setsqlarr['uname'].",".$setsqlarr['category'].",".$setsqlarr['address'].",".$setsqlarr['detailed'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']=$setsqlarr['uname'].$setsqlarr['address'].$setsqlarr['detailed'];
	$setsqlarr['key']="{$setsqlarr['uname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if($resumepid = $db->inserttable(table('simple_resume'),$setsqlarr,1))
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
	$row=simple_resume_one($_GET['id']);
	$smarty->assign('show',$row);
	$smarty->display('m/simple-resume-edit.html');
}
elseif($act == "edit_save")
{
	$captcha=get_cache('captcha');
	$_POST=array_map("utf8_to_gbk", $_POST);
	$id=intval($_POST['id']);
	$pwd=trim($_POST['pwd']);
	$info=$db->getone("select * from ".table('simple_resume')." where id = '{$id}' LIMIT 1");
	$thispwd=md5(md5($pwd).$info['pwd_hash'].$QS_pwdhash);
	if ($thispwd!=$info['pwd'])
	{
		exit("�����������");
	}
	if ($_CFG['simple_edit_audit']!="-1")
	{
	$setsqlarr['audit']=intval($_CFG['simple_edit_audit']);
	}
	$setsqlarr['uname']=trim($_POST['uname'])?trim($_POST['uname']):exit("��û����д������");
	$setsqlarr['age']=intval($_POST['age'])?intval($_POST['age']):exit("��û����д���䣡");
	$setsqlarr['sex']=intval($_POST['sex'])?intval($_POST['sex']):exit("��û��ѡ���Ա�");
	switch($setsqlarr['sex']){
		case 1:$setsqlarr['sex_cn']="��";break;
		case 2:$setsqlarr['sex_cn']="Ů";break;
	}
	$setsqlarr['category']=trim($_POST['category'])?trim($_POST['category']):exit("��û����д����ְλ��");
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):exit("��û��ѡ�������飡");
	$experience = $db->getone("select c_name from ".table('category')." where c_id=".$setsqlarr['experience']);
	$setsqlarr['experience_cn']=$experience['c_name'];
	if ($_CFG['simple_tel_edit']=="1")
	{
		$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):exit("��û����д��ϵ�绰��");
		if(preg_match("/^\d*$/",$setsqlarr['tel']))
		{
			if ($captcha['simple_tel_repeat']=='0')
			{
				$sql = "select id from ".table('simple_resume')." where tel = '{$setsqlarr['tel']}' AND id<>'{$id}' LIMIT 1";
				$info=$db->getone($sql);
				if (!empty($info))
				{
					exit("�绰�����Ѿ����ڣ�");
				}
			}
		}	
	}
	$setsqlarr['subsite_id']=intval($_POST['subsite_id'])?intval($_POST['subsite_id']):exit('��û��ѡ�������');
	$setsqlarr['district']=intval($_POST['district'])?intval($_POST['district']):exit("��û����д��ϵ�绰��");
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['sdistrict_cn']=trim($_POST['sdistrict_cn']);
	$setsqlarr['detailed']=trim($_POST['detailed'])!=""?trim($_POST['detailed']):exit("��û����д���˼�飡");
	$setsqlarr['refreshtime']=time();
	$days=intval($_POST['days']);
	if ($days>0)
	{
	$time=$info['deadline']>time()?$info['deadline']:time();
	$setsqlarr['deadline']=strtotime("{$days} day",$time);
	}
	$setsqlarr['likekey']=$setsqlarr['uname'].",".$setsqlarr['category'].",".$setsqlarr['address'].",".$setsqlarr['detailed'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']=$setsqlarr['uname'].$setsqlarr['address'].$setsqlarr['detailed'];
	$setsqlarr['key']="{$setsqlarr['uname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if($db->updatetable(table('simple_resume'),$setsqlarr," id='{$id}' "))
	{
		exit("ok");
	}
	else
	{
		exit("�޸�ʧ�ܣ�");
	}
}
// ˢ��΢����
elseif($act == "resume_refresh")
{
	$resumeid = intval($_GET['resumeid'])?intval($_GET['resumeid']):exit("��������");
	$pass = trim($_GET['pass'])?trim($_GET['pass']):exit("��������");
	$sql = "select * from ".table('simple_resume')." where id = '{$resumeid}' LIMIT 1";
	$info=$db->getone($sql);
	$thispwd=md5(md5($pass).$info['pwd_hash'].$QS_pwdhash);
	if ($thispwd==$info['pwd'])
	{
		$db->query("update ".table('simple_resume')."  SET refreshtime='".time()."' WHERE id = '{$resumeid}'");
		exit("ˢ�³ɹ���");
	}
	else
	{
		exit("�����������");
	}
}
// ɾ��΢����
elseif($act == "resume_delete")
{
	$resumeid = intval($_GET['resumeid'])?intval($_GET['resumeid']):exit("��������");
	$pass = trim($_GET['pass'])?trim($_GET['pass']):exit("��������");
	$sql = "select * from ".table('simple_resume')." where id = '{$resumeid}' LIMIT 1";
	$info=$db->getone($sql);
	$thispwd=md5(md5($pass).$info['pwd_hash'].$QS_pwdhash);
	if ($thispwd==$info['pwd'])
	{
		$db->query("Delete from ".table('simple_resume')." WHERE id = '{$resumeid}'");
		exit("ɾ���ɹ���");
	}
	else
	{
		exit("�����������");
	}
}

?>