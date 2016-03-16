<?php
function tpl_function_qishi_shop_list($params, &$smarty)
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
		case "��ʾ��Ŀ":
			$aset['row'] = $a[1];
			break;
		case "���ⳤ��":
			$aset['titlelen'] = $a[1];
			break;
		case "��ʼλ��":
			$aset['start'] = $a[1];
			break;
		case "��ַ�":
			$aset['dot'] = $a[1];
			break;
		case "����ID":
			$aset['scategory'] = $a[1];
			break;
		case "�ؼ���":
			$aset['key'] = $a[1];
			break;
		case "�Ƽ�":
			$aset['recommend'] = $a[1];
			break;	
		case "����":
			$aset['displayorder'] = $a[1];
			break;
		case "��ҳ��ʾ":
			$aset['paged'] = $a[1];
			break;
		case "��������":
			$aset['points_interval'] = $a[1];//0|1000
			break;
		case "��Ա����":
			$aset['user_points'] = $a[1]; //���ܶһ���
			break;
		}
	}
	if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
	$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
	$aset['row']=isset($aset['row'])?intval($aset['row']):10;
	$aset['start']=isset($aset['start'])?intval($aset['start']):0;
	$aset['titlelen']=isset($aset['titlelen'])?intval($aset['titlelen']):15;
	$aset['dot']=isset($aset['dot'])?$aset['dot']:'';
	$aset['recommend']=isset($aset['recommend'])?intval($aset['recommend']):1;
	if (isset($aset['displayorder']))
	{
		$arr=explode('>',$aset['displayorder']);
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
			$arr[1]="desc";
		}
		if($arr[0]=="shop_points")
		{
			$orderbysql=" ORDER BY shop_points {$arr[1]},id desc ";
		}
		elseif($arr[0]=="click")
		{
			$orderbysql=" ORDER BY click {$arr[1]},id desc ";
		}
		else
		{
			if($aset['recommend']>0)
			{
				$orderbysql=" ORDER BY recommend desc,addtime {$arr[1]},id desc ";
			}
			else
			{
				$orderbysql=" ORDER BY addtime {$arr[1]},id desc ";
			}
		}

	}
	// ��������
	if(isset($aset['points_interval']) && !empty($aset['points_interval']))
	{
		$points_arr=explode('-', $aset['points_interval']);
		$points_min=$points_arr[0];
		$points_max=$points_arr[1];
		$wheresql.=" AND shop_points>$points_min AND shop_points<$points_max ";
	}
	// ���ܶһ��� user_points
	if(isset($aset['user_points']) && !empty($aset['user_points']))
	{
		$user_points=intval($aset['user_points']);
		$wheresql.=" AND shop_points<$user_points ";
	}
	if ($aset['scategory'])
	{
	$wheresql.=" AND scategory=".intval($aset['scategory'])." ";
	}
	if (isset($aset['key']) && !empty($aset['key']))
	{
		$key=help::addslashes_deep(trim($aset['key']));
		$wheresql.=" AND shop_title LIKE '%{$key}%' ";
	}

	if (!empty($wheresql))
	{
	$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
	}

	if (isset($aset['paged']))
	{
		require_once(QISHI_ROOT_PATH.'include/page.class.php');
		$total_sql="SELECT  COUNT(*) AS num  FROM ".table("shop_goods").$wheresql;
		$total_count=$db->get_total($total_sql);
		$page = new page(array('total'=>$total_count, 'perpage'=>$aset['row'],'getarray'=>$_GET));
		$currenpage=$page->nowindex;
		$aset['start']=abs($currenpage-1)*$aset['row'];
		if($total_count>$aset['row'])
		{
			$smarty->assign('page',$page->show(3));
			$smarty->assign('pagemin',$page->show(7));
		}
		$smarty->assign('total',$total_count);
		$smarty->assign('pagenow',$page->show(6));
	}
	$limit=" LIMIT {$aset['start']} , {$aset['row']}";
	$result = $db->query("SELECT * FROM ".table('shop_goods')." ".$wheresql.$orderbysql.$limit);
	$list = array();
	$config = get_cache('config');
	while($row = $db->fetch_array($result))
	{
		$row['shop_title_']=cut_str($row['shop_title'],$aset['titlelen'],0,$aset['dot']);
		$row['shop_url']=url_rewrite('QS_shop_show',array('id'=>$row['id']));
		$list[] = $row;
	}
	$smarty->assign($aset['listname'],$list);
}
?>