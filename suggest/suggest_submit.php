<?php
 /*
 * 74cms �������
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if($_SESSION['input_token']!="" && $_SESSION['input_token']==$_POST['input_token']){
	$setsqlarr['infotype']=intval($_POST['infotype'])>0?intval($_POST['infotype']):showmsg("��ѡ�����ͣ�",1);
	$setsqlarr['feedback']=trim($_POST['feedback'])?trim($_POST['feedback']):showmsg("����д���ݣ�",1);
	$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):showmsg("����д��ϵ��ʽ��",1);
	$setsqlarr['addtime']=time();
	!$db->inserttable(table('feedback'),$setsqlarr)?showmsg("���ʧ�ܣ�",0):showmsg("��ӳɹ�����л���Ա�վ��֧�֣�",2);
}else{
	showmsg("�Ƿ�������",1);
}
?>