<?php
/*********************************************
*��ʿ��ѵ��ʦ�б�
* *******************************************/
function tpl_function_qishi_train_lecturer_list($params, &$smarty)
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
	case "��ʦ������":
		$aset['teacherlen'] = $a[1];
		break;
	case "����������":
		$aset['trainnamelen'] = $a[1];
		break;
	case "�ó�רҵ����":
		$aset['specialitylen'] = $a[1];
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
	case "��ʦѧ��":
		$aset['education'] = $a[1];
		break;
	case "�Ƽ�":
		$aset['recommend'] = $a[1];
		break;
	case "�ؼ���":
		$aset['key'] = $a[1];
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
$aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['listpage']=isset($aset['listpage'])?$aset['listpage']:"QS_courselist";
$aset['row']=intval($aset['row'])>0?intval($aset['row']):10;
if ($aset['row']>30)$aset['row']=30;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['teacherlen']=isset($aset['teacherlen'])?intval($aset['teacherlen']):8;
$aset['trainnamelen']=isset($aset['trainnamelen'])?intval($aset['trainnamelen']):15;
$aset['specialitylen']=isset($aset['specialitylen'])?intval($aset['specialitylen']):13;
$aset['education']=isset($aset['education'])?intval($aset['education']):0;
$aset['brieflylen']=isset($aset['brieflylen'])?intval($aset['brieflylen']):0;
$aset['recommend']=isset($aset['recommend'])?intval($aset['recommend']):'';
$aset['trainshow']=isset($aset['trainshow'])?$aset['trainshow']:'QS_train_agencyshow';
$aset['teachershow']=isset($aset['teachershow'])?$aset['teachershow']:'QS_train_lecturershow';

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
if (isset($aset['key']) && !empty($aset['key']))
{
	$key=trim($aset['key']);
	$wheresql.=" AND teachername LIKE '%{$key}%' ";
}
if (isset($aset['uid'])  && $aset['uid']<>'')
{
	$wheresql.=" AND uid=".intval($aset['uid']);
}
if (isset($aset['recommend'])  && $aset['recommend']<>'')
{
	$wheresql.=" AND recommend=".intval($aset['recommend']);
}

if (isset($aset['education']) && $aset['education']<>'')
{
	$wheresql.=" AND education=".intval($aset['education'])." ";
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
	$total_sql="SELECT COUNT(*) AS num FROM ".table('train_teachers')." {$wheresql}";
	$total_count=$db->get_total($total_sql);	
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

	$result = $db->query("SELECT id,uid,refreshtime,teachername,contents,achievements,speciality,trainname,train_id,birthdate,photo,photo_img,education_cn,district_cn FROM ".table('train_teachers').$wheresql.$orderbysql.$limit);	
 	while($row = $db->fetch_array($result))
	{
		$row['refreshtime_cn']=daterange(time(),$row['refreshtime'],'Y-m-d',"#FF3300");
		$row['teachername']=cut_str($row['teachername'],$aset['teacherlen'],0,$aset['dot']);
		$row['trainname']=cut_str($row['trainname'],$aset['trainnamelen'],0,$aset['dot']);
		
			if ($aset['brieflylen']>0)
			{
				$row['briefly']=cut_str(strip_tags($row['contents']),$aset['brieflylen'],0,$aset['dot']);
				$row['achievements']=cut_str(strip_tags($row['achievements']),$aset['brieflylen'],0,$aset['dot']);
			}
			else
			{
				$row['briefly']=strip_tags($row['contents']);
				$row['achievements']=strip_tags($row['achievements']);
			}
			
			if ($aset['specialitylen']>0)
			{
				$row['speciality']=cut_str(strip_tags($row['speciality']),$aset['specialitylen'],0,$aset['dot']);
			}
			else
			{
				$row['speciality']=strip_tags($row['speciality']);
			}
			
		$row['briefly_']=$row['contents'];
		$row['achievements_']=$row['achievements'];
		$row['speciality_']=$row['speciality'];
		$row['trainname_']=$row['trainname'];
		$row['train_url']=url_rewrite($aset['trainshow'],array('id'=>$row['train_id']));
		$row['teacher_url']=url_rewrite($aset['teachershow'],array('id'=>$row['id']));
		$row['age']=date('Y')+1-$row['birthdate'];
			if ($row['photo']=="1")
			{
			$row['photosrc']=$_CFG['teacher_photo_dir'].$row['photo_img'];
			}
			else
			{
			$row['photosrc']=$_CFG['teacher_photo_dir']."no_photo.gif";
			}
		
		$list[] = $row;
		unset($row);
	}
	$smarty->assign($aset['listname'],$list);
}
?>