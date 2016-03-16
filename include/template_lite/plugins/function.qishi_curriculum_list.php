<?php
/*********************************************
*��ʿ��ѵ�γ��б�
* *******************************************/
function tpl_function_qishi_curriculum_list($params, &$smarty)
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
	case "�γ�������":
		$aset['courselen'] = $a[1];
		break;
	case "����������":
		$aset['trainnamelen'] = $a[1];
		break;
	case "��������":
		$aset['brieflylen'] = $a[1];
		break;
	case "��ַ�":
		$aset['dot'] = $a[1];
		break;
	case "�γ����":
		$aset['coursecategory'] = $a[1];
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
	case "�Ͽΰ���":
		$aset['classtype'] = $a[1];
		break;
	case "�Ƽ�":
		$aset['recommend'] = $a[1];
		break;
	case "�ؼ���":
		$aset['key'] = $a[1];
		break;
	case "�ؼ�������":
		$aset['keytype'] = $a[1];
		break;
	case "���ڷ�Χ":
		$aset['refre'] = $a[1];
		break;
	case "����ʱ��":
		$aset['starttime'] = $a[1];
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
	case "����ҳ��":
		$aset['trainshow'] = $a[1];
		break;
	case "�γ�ҳ��":
		$aset['courseshow'] = $a[1];
		break;
	case "��ʦҳ��":
		$aset['teachershow'] = $a[1];
		break;
	case "�б�ҳ":
		$aset['listpage'] = $a[1];
		break;
	}
}
	$timenow=time();
$aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['listpage']=isset($aset['listpage'])?$aset['listpage']:"QS_courselist";
$aset['row']=intval($aset['row'])>0?intval($aset['row']):10;
if ($aset['row']>30)$aset['row']=30;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['courselen']=isset($aset['courselen'])?intval($aset['courselen']):8;
$aset['trainnamelen']=isset($aset['trainnamelen'])?intval($aset['trainnamelen']):15;
$aset['brieflylen']=isset($aset['brieflylen'])?intval($aset['brieflylen']):0;
$aset['recommend']=isset($aset['recommend'])?intval($aset['recommend']):'';
$aset['trainshow']=isset($aset['trainshow'])?$aset['trainshow']:'QS_train_agencyshow';
$aset['courseshow']=isset($aset['courseshow'])?$aset['courseshow']:'QS_train_curriculumshow';
$aset['teachershow']=isset($aset['teachershow'])?$aset['teachershow']:'QS_train_lecturershow';
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
		elseif ($arr[0]=="stime")
		{
		$orderbysql=" ORDER BY starttime {$arr[1]}";
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
if (intval($_CFG['subsite_id'])>0)
{
	$wheresql.=" AND subsite_id=".intval($_CFG['subsite_id'])." ";
}
if (isset($aset['refre']) && $aset['refre']<>'')
{
	$settr=intval($aset['refre']);
	if ($settr>0)
	{
	$settr_val=intval(strtotime("-".$aset['refre']." day"));
	$wheresql.=" AND refreshtime>".$settr_val;
	}
}
if (isset($aset['starttime']) && $aset['starttime']<>'')
{
	$trart=intval($aset['starttime']);
	if ($trart>0)
	{
	$trart_val=intval(strtotime("+".$aset['starttime']." day"));
	$wheresql.=" AND starttime>{$timenow} AND starttime<".$trart_val;
	}
}

if (isset($aset['uid'])  && $aset['uid']<>'')
{
	$wheresql.=" AND uid=".intval($aset['uid']);
}
if (isset($aset['recommend'])  && $aset['recommend']<>'')
{
	$wheresql.=" AND recommend=".intval($aset['recommend']);
}

if (isset($aset['classtype']) && $aset['classtype']<>'')
{
	$wheresql.=" AND classtype=".intval($aset['classtype'])." ";
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
//��֧�ֶ����������
if (!empty($aset['coursecategory']) && $aset['coursecategory']<>'')
{
			$wheresql.=" AND category =".intval($aset['coursecategory']);
}
if (isset($aset['key']) && !empty($aset['key']))
{
	if ($_CFG['courseearch_purview']=='2')
	{
		if ($_SESSION['username']=='')
		{
		header("Location: ".url_rewrite('QS_login')."?url=".urlencode($_SERVER["REQUEST_URI"]));
		}
	}
	$key=help::addslashes_deep(trim($aset['key']));
	if ($_CFG['courseearch_type']=='1')
	{
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
	else
	{
			$wheresql.=" AND likekey LIKE '%{$key}%' ";
	}
}
$wheresql.=$_CFG['outdated_course']=='1'?" AND deadline > {$timenow} ":' ';
if($_CFG['operation_train_mode']=='1'){
	$wheresql.="  AND audit=1  AND display=1 AND add_mode=1 ";
}elseif($_CFG['operation_train_mode']=='2'){
	$wheresql.="  AND audit=1  AND display=1 AND add_mode=2 AND setmeal_id>0 AND (setmeal_deadline>{$timenow} OR setmeal_deadline=0)";
}

if (!empty($wheresql))
{
$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
}
if (isset($aset['page']))
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('course')." {$wheresql}";
	//echo $total_sql;
	//echo "<br>";
	$total_count=$db->get_total($total_sql);	
	if ($_CFG['course_list_max']>0)
	{
		$total_count>intval($_CFG['course_list_max']) && $total_count=intval($_CFG['course_list_max']);
	}
	$page = new page(array('total'=>$total_count, 'perpage'=>$aset['row'],'alias'=>$aset['listpage'],'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$aset['start']=abs($currenpage-1)*$aset['row'];
	if ($total_count>$aset['row'])
	{
	$smarty->assign('page',$page->show(3));
	$smarty->assign('pagemin',$page->show(7));
	$smarty->assign('pagenow',$page->show(6));
	}
	$smarty->assign('total',$total_count);
}
	$limit=" LIMIT {$aset['start']} , {$aset['row']}";
	$list = $id = array();
	$result = $db->query("SELECT id,subsite_id,course_name,refreshtime,starttime,contents,trainname,train_id,teacher_id,teacher_cn,category_cn,classtype_cn,train_expenses,favour_expenses,district_cn,classhour FROM ".table('course').$wheresql.$orderbysql.$limit);
	while($row = $db->fetch_array($result))
	{
		$row['course_name_']=$row['course_name'];
		$row['refreshtime_cn']=daterange(time(),$row['refreshtime'],'Y-m-d',"#FF3300");
		$row['starttime_cn']=date("Y-m-d",$row['starttime']);
		$row['course_name']=cut_str($row['course_name'],$aset['courselen'],0,$aset['dot']);
			if ($aset['brieflylen']>0)
			{
				$row['briefly']=cut_str(strip_tags($row['contents']),$aset['brieflylen'],0,$aset['dot']);
			}
			else
			{
				$row['briefly']=strip_tags($row['contents']);
			}
		$row['briefly_']=strip_tags($row['contents']);
		$row['trainname_']=$row['trainname'];
		$row['trainname']=cut_str($row['trainname'],$aset['trainnamelen'],0,$aset['dot']);
		$row['course_url']=url_rewrite($aset['courseshow'],array('id'=>$row['id']),1,$row['subsite_id']);
		$row['train_url']=url_rewrite($aset['trainshow'],array('id'=>$row['train_id']));
		$row['teacher_url']=url_rewrite($aset['teachershow'],array('id'=>$row['teacher_id']));
		$list[] = $row;
	}
	$smarty->assign($aset['listname'],$list);
}
?>