<?php
function tpl_function_qishi_train_news_list($params, &$smarty)
{
global $db,$_CFG;
$arrset=explode(',',$params['set']);
foreach($arrset as $str)
{
$a=explode(':',$str);
	switch ($a[0])
	{
	case "�б���":
		$aset['listname'] = $a[1];
		break;
	case "����ID":
		$aset['train_id'] = $a[1];
		break;
	case "��ʾ��Ŀ":
		$aset['row'] = $a[1];
		break;
	case "���ⳤ��":
		$aset['titlelen'] = $a[1];
		break;
	case "ժҪ����":
		$aset['infolen'] = $a[1];
		break;		
	case "��ʼλ��":
		$aset['start'] = $a[1];
		break;
	case "��ַ�":
		$aset['dot'] = $a[1];
		break;
	case "����":
		$aset['displayorder'] = $a[1];
		break;
	case "��ҳ��ʾ":
		$aset['paged'] = $a[1];
		break;
	case "ҳ��":
		$aset['showname'] = $a[1];
		break;
	case "�б�ҳ":
		$aset['listpage'] = $a[1];
		break;
	}
}
if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['listpage']=isset($aset['listpage'])?$aset['listpage']:"QS_train_agency_news";
$aset['row']=isset($aset['row'])?intval($aset['row']):30;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['titlelen']=isset($aset['titlelen'])?intval($aset['titlelen']):15;
$aset['infolen']=isset($aset['infolen'])?intval($aset['infolen']):25;
$aset['showname']=isset($aset['showname'])?$aset['showname']:'QS_train_newsshow';
if ($aset['displayorder'])
{
	if (strpos($aset['displayorder'],'>'))
	{
		$arr=explode('>',$aset['displayorder']);
		// �����ֶ�
		if($arr[0]=='order'){
			$arr[0]="order";
		}
		elseif($arr[0]=="id")
		{
			$arr[0]="id";
		}
		elseif($arr[0]=="click")
		{
			$arr[0]="click";
		}
		else
		{
			$arr[0]="";
		}
		// ����ʽ
		if($arr[1]=='desc'){
			$arr[1]="desc";
		}
		elseif($arr[1]=="asc")
		{
			$arr[1]="asc";
		}
		else
		{
			$arr[1]="";
		}

		if ($arr[0] && $arr[1])
		{
		$orderbysql=" ORDER BY `".$arr[0]."` ".$arr[1];
		}
		if ($arr[0]=="order")
		{
		$orderbysql.=" ,id DESC ";
		}
	}
}
if (isset($aset['train_id'])  && $aset['train_id']<>'')
{
	$wheresql=" WHERE train_id='".intval($aset['train_id'])."' AND audit=1";
}else{
	$wheresql=" WHERE audit=1";
}
if (isset($aset['paged']))
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('train_news').$wheresql;
 	$total_count=$db->get_total($total_sql);
 	$pagelist = new page(array('total'=>$total_count, 'perpage'=>$aset['row'],'alias'=>$aset['listpage'],'getarray'=>$_GET));
	$currenpage=$pagelist->nowindex;
	$aset['start']=($currenpage-1)*$aset['row'];
		if ($total_count>$aset['row'])
		{
		$smarty->assign('page',$pagelist->show(3));
		}
		$smarty->assign('total',$total_count);
}
$limit=" LIMIT ".abs($aset['start']).','.$aset['row'];
$result = $db->query("SELECT id,train_id,title,content,click,addtime FROM ".table('train_news')." ".$wheresql.$orderbysql.$limit);
 $list= array();
while($row = $db->fetch_array($result))
{
	$profile=GetTainProfile($row['train_id']);
	if(empty($profile)) continue;
	$row['train']=$profile;
	$row['title_']=$row['title'];
	$row['title']=cut_str($row['title'],$aset['titlelen'],0,$aset['dot']);
	$row['url'] = url_rewrite($aset['showname'],array('id'=>$row['id']));
	$row['train_url'] = url_rewrite('QS_train_agencyshow',array('id'=>$row['train']['id']));
	$row['content']=str_replace('&nbsp;','',$row['content']);
	$row['briefly_']=strip_tags($row['content']);
		if ($aset['infolen']>0)
		{
		$row['briefly']=cut_str(strip_tags($row['content']),$aset['infolen'],0,$aset['dot']);
		}
	$list[] = $row;
}
$smarty->assign($aset['listname'],$list);
}
function GetTainProfile($id)
{
	global $db;
	$sql = "select * from ".table('train_profile')." where id=".intval($id)." LIMIT 1 ";
	return $db->getone($sql);
}

?>