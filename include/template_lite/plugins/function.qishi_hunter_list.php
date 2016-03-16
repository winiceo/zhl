<?php
/*********************************************
*��ʿ��ͷ�����б�
* *******************************************/
function tpl_function_qishi_hunter_list($params, &$smarty)
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
	case "��ͷ������":
		$aset['hunterlen'] = $a[1];
		break;
	case "��˾������":
		$aset['companynamelen'] = $a[1];
		break;
	case "�ó���ҵ����":
		$aset['goodtradelen'] = $a[1];
		break;
	case "�ó�ְ�ܳ���":
		$aset['goodcategorylen'] = $a[1];
		break;
	case "��������":
		$aset['brieflylen'] = $a[1];
		break;
	case "��ַ�":
		$aset['dot'] = $a[1];
		break;
	case "��������":
		$aset['citycategory'] = $a[1];
		break;
	case "��������":
		$aset['district'] = $a[1];
		break;
	case "����С��":
		$aset['sdistrict'] = $a[1];
		break;
	case "��ͷͷ��":
		$aset['rank'] = $a[1];
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
	case "��ԱUID":
		$aset['uid'] = $a[1];
		break;
	case "��ͷҳ��":
		$aset['huntershow'] = $a[1];
		break;
	case "�б�ҳ":
		$aset['listpage'] = $a[1];
		break;
	case "�ؼ���":
		$aset['key'] = $a[1];
		break;
	}
}
$aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['listpage']=isset($aset['listpage'])?$aset['listpage']:"QS_hunterlist";
$aset['row']=intval($aset['row'])>0?intval($aset['row']):10;
if ($aset['row']>30)$aset['row']=30;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['hunterlen']=isset($aset['hunterlen'])?intval($aset['hunterlen']):8;
$aset['companynamelen']=isset($aset['companynamelen'])?intval($aset['companynamelen']):15;
$aset['goodtradelen']=isset($aset['goodtradelen'])?intval($aset['goodtradelen']):13;
$aset['goodcategorylen']=isset($aset['goodcategorylen'])?intval($aset['goodcategorylen']):13;
$aset['rank']=isset($aset['rank'])?intval($aset['rank']):0;
$aset['brieflylen']=isset($aset['brieflylen'])?intval($aset['brieflylen']):0;

$orderbysql=" ORDER BY refreshtime desc ";

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
		if ($arr[0]=="rtime")
		{
		$orderbysql=" ORDER BY refreshtime {$arr[1]}";
		}
		elseif ($arr[0]=="atime")
		{
		$orderbysql=" ORDER BY addtime {$arr[1]}";
		}
		elseif ($arr[0]=="hot")
		{
		$orderbysql=" ORDER BY click {$arr[1]}";
		}
		else
		{
		$orderbysql=" ORDER BY refreshtime {$arr[1]}";
		}
}
$wheresql.=" AND audit=1 ";
if (isset($aset['settr']) && $aset['settr']<>'')
{
	$settr=intval($aset['settr']);
	if ($settr>0)
	{
	$settr_val=intval(strtotime("-".$aset['settr']." day"));
	$wheresql.=" AND refreshtime>".$settr_val;
	}
}

if (isset($aset['uid'])  && $aset['uid']<>'')
{
	$wheresql.=" AND uid=".intval($aset['uid']);
}
if (isset($aset['rank']) && $aset['rank']<>'')
{
	$wheresql.=" AND rank=".intval($aset['rank'])." ";
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
if (isset($aset['key']) && !empty($aset['key']))
{
	$key=help::addslashes_deep(trim($aset['key']));
	
	$akey=explode(' ',$key);
	if (count($akey)>1)
	{
	$akey=array_filter($akey);
	$akey=array_slice($akey,0,2);
	$akey=array_map("fulltextpad",$akey);
	$key='+'.implode(' +',$akey);
	$mode=' IN BOOLEAN MODE';
	}
	else
	{
	$key=fulltextpad($key);
	$mode=' ';
	}
	$wheresql.=" AND  MATCH (`key`) AGAINST ('{$key}'{$mode}) ";
}
if (!empty($wheresql))
{
$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
}
if (isset($aset['page']))
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('hunter_profile')." {$wheresql}";
	$total_count=$db->get_total($total_sql);	
	$page = new page(array('total'=>$total_count, 'perpage'=>$aset['row'],'alias'=>$aset['listpage'],'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$aset['start']=abs($currenpage-1)*$aset['row'];
	$countpage = $total_count%$aset['row']==0?$total_count/$aset['row']:intval($total_count/$aset['row'])+1;
	$smarty->assign('countpage',$countpage);
	
	$smarty->assign('page',$page->show(3));
	$smarty->assign('pagemin',$page->show(4));
	
	$smarty->assign('currenpage',$currenpage);
	$smarty->assign('total',$total_count);
}
	$limit=" LIMIT {$aset['start']} , {$aset['row']}";
	$list = $id = array();
	$result = $db->query("SELECT id,uid,refreshtime,huntername,companyname,contents,goodtrade_cn,goodcategory_cn,worktime_start,photo_img,district_cn FROM ".table('hunter_profile').$wheresql.$orderbysql.$limit);	
 	while($row = $db->fetch_array($result))
	{
		$row['refreshtime_cn']=daterange(time(),$row['refreshtime'],'Y-m-d',"#FF3300");
		$row['huntername']=cut_str($row['huntername'],$aset['hunterlen'],0,$aset['dot']);
		$row['companyname']=cut_str($row['companyname'],$aset['companynamelen'],0,$aset['dot']);
		
			if ($aset['brieflylen']>0)
			{
				$row['contents']=cut_str(strip_tags($row['contents']),$aset['brieflylen'],0,$aset['dot']);
			}
			else
			{
				$row['contents']=strip_tags($row['contents']);
			}
			if ($aset['goodtradelen']>0)
			{
				$row['goodtrade_cn']=cut_str(strip_tags($row['goodtrade_cn']),$aset['goodtradelen'],0,$aset['dot']);
			}
			else
			{
				$row['goodtrade_cn']=strip_tags($row['goodtrade_cn']);
			}
 			if ($aset['goodcategorylen']>0)
			{
				$row['goodcategory_cn']=cut_str(strip_tags($row['goodcategory_cn']),$aset['goodcategorylen'],0,$aset['dot']);
			}
			else
			{
				$row['goodcategory_cn']=strip_tags($row['goodcategory_cn']);
			}
			
		$row['companyname_']=$row['companyname'];
		$row['huntername_']=$row['huntername'];
		
		$row['years']=date('Y')+1-$row['worktime_start'];
			if ($row['photo_img'])
			{
			$row['photosrc']=$_CFG['hunter_photo_dir'].$row['photo_img'];
			}
			else
			{
			$row['photosrc']=$_CFG['hunter_photo_dir']."no_photo.gif";
			}
		$timenow=time();
		if($_CFG['operation_mode']=='1'){
			$wheresql1.="  AND audit=1  AND display=1 ";
		}elseif($_CFG['operation_train_mode']=='2'){
			$wheresql1.="  AND audit=1 AND display=1 AND setmeal_id>0 AND (setmeal_deadline>{$timenow} OR setmeal_deadline=0)";
		}
		$row['countjobs'] = $db->get_total("select count(*) as num from ".table('hunter_jobs')." where uid=".$row['uid']." ".$wheresql1);
		$jobs = $db->getall("select id,subsite_id,jobs_name,refreshtime from ".table('hunter_jobs')." where uid=".$row['uid']." ".$wheresql1." limit 4");
		foreach ($jobs as $key => $value) {
			$row['jobslist'][$key]['jobs_name'] = cut_str($value['jobs_name'],10,0,"..");
			$row['jobslist'][$key]['refreshtime'] = date("Y-m-d",$value['refreshtime']);
			$row['jobslist'][$key]['jobs_url'] = url_rewrite("QS_hunter_jobsshow",array("id"=>$value['id']),1,$value['subsite_id']);
		}
		$timenow=time();
		if($_CFG['operation_mode']=='1'){
			$wheresql1.="  AND audit=1  AND display=1 ";
		}elseif($_CFG['operation_train_mode']=='2'){
			$wheresql1.="  AND audit=1 AND display=1 AND setmeal_id>0 AND (setmeal_deadline>{$timenow} OR setmeal_deadline=0)";
		}
		$row['countjobs'] = $db->get_total("select count(*) as num from ".table('hunter_jobs')." where uid=".$row['uid']." ".$wheresql1);
		$list[] = $row;
		unset($row);
	}
	$smarty->assign($aset['listname'],$list);
}
?>