<?php
 /*
 * 74cms ���ݿ�
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'check_database';
$smarty->assign('act',$act);
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
if ($act=="check_database")
{
	$smarty->display('sys/admin_check_database.htm');
}
elseif($act=="do_check") 
{
	$sql_data = array();
	$local_detail = array();
	//��֤ ��Ȩ��
	$rst=https_request("http://www.74cms.com/plus/check_webkey.php?web_key=$_POST[web_key]&web=$_CFG[site_domain]");
	if($rst===false)
	{
		adminmsg("���ȿ�����php��curlģ��!");
	}
	elseif($rst=="err")
	{
		adminmsg("���������Ȩ������!");
	}
	else
	{
		$sql_data=json_decode($rst,1);
	}
	//�õ����ݿ��б��Լ����е��ֶ�ֵ
	$local_table = mysql_query("show tables");
	while($local = mysql_fetch_array($local_table))
	{
		//$name-> ����
		$name = $local[0];
		$name = substr($local[0],strpos($local[0],'_')+1);
		//desc table_name -> �鿴������(�ֶ�  ����  �Ƿ�Ϊ��  �����  Ĭ��ֵ  ����/����)
		//MYSQL_ASSOC -> ֻ�õ���������
		$table_info = mysql_query("desc ".$pre."$name");
		while ($info = mysql_fetch_assoc($table_info)) 
		{
			$local_detail[$name][] =$info;
		}
	}
	$server_detail = $sql_data;
	//У��(˫��У��)
	$diff_field = $diff_table = array();
	foreach($server_detail as $k=>$v)
	{
		//�жϷ��������ݿ����Ƿ��д˱����Ϣ()
		if(!is_array($local_detail[$k]))
		{
			//�Ѳ�һ���ı� �ŵ�diff_table������
			$diff_table['less'][] = $k;
		}
		else
		{
			//�ж��ֶ���(ֵ  ˳��  ����)�Ƿ�һ��
			if(!($local_detail[$k] === $v))
			{
				//�Ѳ�һ�����ֶ� �ŵ�diff_field������
				foreach($v as $k1=>$v1)
				{
					if(!in_array($v1,$local_detail[$k]))
					{
						$diff_field[$k][] = $v1;
					}
				}
			}
		}
	}
	foreach($local_detail as $k=>$v)
	{
		//�жϷ��������ݿ����Ƿ��д˱����Ϣ()
		if(!is_array($server_detail[$k]))
		{
			//�Ѳ�һ���ı� �ŵ�diff_table������
			$diff_table['many'][] = $k;
		}
	}
	$smarty->assign('less',$diff_table['less']); 	//�������ݿ�ȱ�ٵı�
	$smarty->assign('less_num',count($diff_table['less']));
	$smarty->assign('many',$diff_table['many']);	//�������ݿ����ı�
	$smarty->assign('many_num',count($diff_table['many']));
	$smarty->assign('different',$diff_field);		//�������ݿⲻͬ�ڷ������ı�
	$smarty->assign('diff_num',count($diff_field));
	$smarty->display('sys/admin_check_database.htm');
}
?>