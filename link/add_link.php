<?php
/*
 * 74cms ������������
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'add';
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if ($act=="add")
{
	$sql = "select * from ".table('link_category')."";
	$cat=$db->getall($sql);
	$smarty->assign('cat',$cat);
	$text=get_cache('text');
	$smarty->assign('link_application_txt',$text['link_application_txt']);
	$smarty->assign('random',mt_rand());
	$captcha=get_cache('captcha');
	$smarty->assign('verify_link',$captcha['verify_link']);
	$smarty->display('link/add.htm');
}
elseif ($act=="save")
{	
	$captcha=get_cache('captcha');
	$postcaptcha = trim($_POST['postcaptcha']);
	if($captcha['verify_link']=='1' && empty($postcaptcha))
	{
		showmsg("����д��֤��",1);
 	}
	if ($captcha['verify_link']=='1' &&  strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
	{
		showmsg("��֤�����",1);
	}
	if ($_CFG['app_link']<>"1")
	{
	showmsg('��ֹͣ�����������ӣ�����ϵ��վ����Ա��',1);
	}
	else
	{
	$setsqlarr['link_name']=trim($_POST['link_name'])?trim($_POST['link_name']):showmsg('��û����д�������ƣ�',1);
	$setsqlarr['link_url']=trim($_POST['link_url'])?trim($_POST['link_url']):showmsg('��û����д���ӵ�ַ��',1);
	$setsqlarr['link_logo']=trim($_POST['link_logo']);
	if(strpos($setsqlarr['link_logo'], "http") !== 0)
	{
		showmsg('LOGO��ַ������http��ͷ��',1);
	}
	$setsqlarr['app_notes']=trim($_POST['app_notes']);
	$setsqlarr['alias']=trim($_POST['alias']);
	$setsqlarr['display']=2;
	$setsqlarr['type_id']=2;
	$link[0]['text'] = "������վ��ҳ";
	$link[0]['href'] =$_CFG['site_dir'];
	!$db->inserttable(table('link'),$setsqlarr)?showmsg("���ʧ�ܣ�",0):showmsg("��ӳɹ�����ȴ�����Ա��ˣ�",2,$link);
	}
}
unset($smarty);
?>