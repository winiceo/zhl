<?php
/*********************************************
*΢��Ƹ
********************************************/
function tpl_function_qishi_simple_resume_list($params, &$smarty)
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
	case "��ʼλ��":
		$aset['start'] = $a[1];
		break;
	case "��������":
		$aset['unamelen'] = $a[1];
		break;
	case "��������":
		$aset['brieflylen'] = $a[1];
		break;
	case "��ַ�":
		$aset['dot'] = $a[1];
		break;	 
	case "�ؼ���":
		$aset['key'] = $a[1];
		break;
	case "�ؼ�������":
		$aset['keytype'] = $a[1];
		break;
	case "���ڷ�Χ":
		$aset['settr'] = $a[1];
		break;
	case "����":
		$aset['displayorder'] = $a[1];
		break;
	case "��ҳ��ʾ":
		$aset['page'] = $a[1];
		break;
	case "ҳ��":
		$aset['simpleresumeshow'] = $a[1];
		break;	
	case "��������":
		$aset['citycategory'] = $a[1];
		break;	
	}
}
$aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['row']=isset($aset['row'])?intval($aset['row']):10;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['unamelen']=isset($aset['unamelen'])?intval($aset['unamelen']):8;
$aset['brieflylen']=isset($aset['brieflylen'])?intval($aset['brieflylen']):0;
$aset['simpleresumeshow']=isset($aset['simpleresumeshow'])?$aset['simpleresumeshow']:'QS_simpleresumeshow';
if (isset($aset['displayorder']))
{
	if (strpos($aset['displayorder'],'>'))
	{
		$arr=explode('>',$aset['displayorder']);
		// �����ֶ�
		if($arr[0]=='refreshtime'){
			$arr[0]="refreshtime";
		}
		elseif($arr[0]=="id")
		{
			$arr[0]="id";
		}
		elseif($arr[0]=="click")
		{
			$arr[0]=="click";
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
		$orderbysql=" ORDER BY  `is_hot`  {$arr[1]} , {$arr[0]} {$arr[1]}";
		}
	}
}else{
	$orderbysql=" ORDER BY   `is_hot`  DESC";
}
$wheresql=" AND audit=1 ";
if (intval($_CFG['subsite_id'])>0)
{
	$wheresql.=" AND subsite_id=".intval($_CFG['subsite_id'])." ";
}
if (isset($aset['settr']) && $aset['settr']<>'')
{
	$settr=intval($aset['settr']);
	if ($settr>0)
	{
	$settr_val=intval(strtotime("-".$aset['settr']." day"));
	$wheresql.=" AND refreshtime>".$settr_val;
	}
}

if (isset($aset['key']) && !empty($aset['key']))
{
	$aset['key']=help::addslashes_deep(trim($aset['key']));
	if ($aset['keytype']=="1" || $aset['keytype']=="")
	{
		$wheresql.=" AND  likekey like '%{$aset['key']}%'";
		$orderbysql="";
	}
	elseif ($aset['keytype']=="2")
	{
		$wheresql.=" AND  MATCH (`key`) AGAINST ('".fulltextpad($aset['key'])."') ";
		$orderbysql="";
	}
}
if (!empty($aset['citycategory']))
{
		$dsql=$xsql="";
		$arr=explode("_",$aset['citycategory']);
		$arr=array_unique($arr);
		if (count($arr)>10) exit();
		foreach($arr as $sid)
		{
				$cat=explode(".",$sid);
				if (intval($cat[1])===0)
				{
				$dsql.= " OR district =".intval($cat[0]);
				}
				else
				{
				$xsql.= " OR sdistrict =".intval($cat[1]);
				}
				
				
		}
		$wheresql.=" AND  (".ltrim(ltrim($dsql.$xsql),'OR').") ";
}
else
{
	if (isset($aset['district'])  && $aset['district']<>'')
	{
		if (strpos($aset['district'],"-"))
		{
			$or=$orsql="";
			$arr=explode("-",$aset['district']);
			$arr=array_unique($arr);
			if (count($arr)>20) exit();
			$sqlin=implode(",",$arr);
			if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
			{
				$wheresql.=" AND district IN  ({$sqlin}) ";
			}
		}
		else
		{
			$wheresql.=" AND district =".intval($aset['district']);
		}
	}
	if (isset($aset['sdistrict'])  && $aset['sdistrict']<>'')
	{
		if (strpos($aset['sdistrict'],"-"))
		{
			$or=$orsql="";
			$arr=explode("-",$aset['sdistrict']);
			$arr=array_unique($arr);
			if (count($arr)>10) exit();
			$sqlin=implode(",",$arr);
			if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
			{
				$wheresql.=" AND sdistrict IN  ({$sqlin}) ";
			}
		}
		else
		{
			$wheresql.=" AND sdistrict =".intval($aset['sdistrict']);
		}
	}	
}
if (!empty($wheresql))
{
$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
}
if (isset($aset['page']))
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('simple_resume').$wheresql;
	//echo $total_sql;
	$total_count=$db->get_total($total_sql);	
	$page = new page(array('total'=>$total_count, 'perpage'=>$aset['row'],'alias'=>'QS_simpleresumelist','getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$aset['start']=($currenpage-1)*$aset['row'];
	$smarty->assign('page',$page->show(3));
	$smarty->assign('total',$total_count);
}
	$limit=" LIMIT ".abs($aset['start']).','.$aset['row'];
	$result = $db->query("SELECT id,subsite_id,uname,detailed,refreshtime,category,is_hot,sex,age,experience_cn,deadline,district_cn,sdistrict_cn FROM ".table('simple_resume')." ".$wheresql.$orderbysql.$limit);
	$list   = array();
	//echo "SELECT * FROM ".table('jobs')." ".$wheresql.$orderbysql.$limit;
		while($row = $db->fetch_array($result))
		{
		$row['uname_']=$row['uname'];
		$row['uname']=cut_str($row['uname'],$aset['unamelen'],0,$aset['dot']);
		$row['detailed_']=strip_tags($row['detailed']);
		if ($aset['brieflylen']>0)
			{
				$row['detailed']=cut_str($row['detailed_'],$aset['brieflylen'],0,$aset['dot']);
			}
			else
			{
				$row['detailed']=$row['detailed_'];
			}
			if(strlen($row['detailed_']) > $aset['brieflylen']*2) {
				$row['show_detail'] = true;
			}else{
				$row['show_detail'] = false;
			}
		// $row['comname_']=$row['comname'];
		$row['refreshtime_cn']=daterange(time(),$row['refreshtime'],'Y-m-d',"#FF3300");
		// $row['comname']=cut_str($row['comname'],$aset['companynamelen'],0,$aset['dot']);
		$row['simple_url']=url_rewrite($aset['simpleresumeshow'],array('id'=>$row['id']),1,$row['subsite_id']);
		$row['intention_jobs']=$row['category'];
		$list[] = $row;
		}
		$smarty->assign($aset['listname'],$list);
}
?>