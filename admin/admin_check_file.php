<?php
 /*
 * 74cms ���ݿ�
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'check_file';
$smarty->assign('act',$act);
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
if ($act=="check_file")
{
	$smarty->display('sys/admin_check_file.htm');
}
elseif($act=="do_check") 
{
	$sql_data = array();
	$local_detail = array();
	//��֤ ��Ȩ��
	$rst=https_request("http://www.zhaohulu.com/plus/check_webkey.php?web_key=$_POST[web_key]&web=$_CFG[site_domain]&check_type=file");
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

	//��ȡ��ǰ��վ �ļ�Ŀ¼
	function get_filetree($path)
	{
		$tree = array();
		$single="";
		foreach(glob($path.'/*') as $single){
		if(is_dir($single)){
		$tree = array_merge($tree,get_filetree($single));
		}
		else{
		$tree[] = $single.",".filesize($single).",".date("Y-m-d H:i:s",filemtime($single));
		}
		}
		return $tree;
	}
	$dir=dirname(__FILE__);
	$num=strripos($dir,"\\");
	$path=substr($dir,0,$num);
	$tree=get_filetree($path);
	foreach($tree as $key => $value)
	{
		$num=stripos($value,"/");
		$value=substr($value,$num+1);
		if(preg_match('/data\/|temp|templates\/|statement\/|install\//', $value))
		{
			continue;
		}
		else
		{
			$value_arr=explode(",", $value);
			$num=strripos($value_arr[0],"/");
			$name=substr($value_arr[0],$num+1);
			$tree_arr[$name]=$value_arr;
		}
	}
	$local_detail=$tree_arr;
	$server_detail = $sql_data;
	//У��(˫��У��)
	$diff_field = $diff_table = array();
	foreach($server_detail as $k=>$v)
	{
		//�жϱ��� ���д��ļ�
		if(!is_array($local_detail[$k]))
		{
			//����ȱ���ļ�
			$diff_table['less'][] = $v[0];
		}
		else
		{
			//�ļ��Ƿ��޸�
			if(!($local_detail[$k] === $v))
			{
				//�Ѳ�һ�����ֶ� �ŵ�diff_field������
				if($local_detail[$k][1]!==$v[1] || $local_detail[$k][2]!==$v[2])
				{
					$file['local']=$local_detail[$k];
					$file['web']=$v;
					$diff_field[$k]=$file;
				}
			}
		}
	}
	foreach($local_detail as $k=>$v)
	{
		//�ж� ���������Ƿ��д��ļ�
		if(!is_array($server_detail[$k]))
		{
			//���ض���ļ�
			$diff_table['many'][] = $v[0];
		}
	}
	$smarty->assign('less',$diff_table['less']); 	//�������ݿ�ȱ�ٵı�
	$smarty->assign('less_num',count($diff_table['less']));
	$smarty->assign('many',$diff_table['many']);	//�������ݿ����ı�
	$smarty->assign('many_num',count($diff_table['many']));
	$smarty->assign('different',$diff_field);	//�������ݿⲻͬ�ڷ������ı�
	$smarty->assign('diff_num',count($diff_field));
	$smarty->display('sys/admin_check_file.htm');
}
?>