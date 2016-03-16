<?php
function tpl_function_qishi_resume_list($params, &$smarty)
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
	case "Ӧ��������":
		$aset['campu_sresume'] = $a[1];
		break;
	case "ԺУ����":
		$aset['campusname'] = $a[1];
		break;
	case "����ʱ��":
		$aset['refreshtime'] = $a[1];
		break;
	case "��ʼλ��":
		$aset['start'] = $a[1];
		break;
	case "��������":
		$aset['namelen'] = $a[1];
		break;
	case "�س���������":
		$aset['specialtylen'] = $a[1];
		break;
	case "����ְλ����":
		$aset['jobslen'] = $a[1];
		break;
	case "רҵ����":
		$aset['majorlen'] = $a[1];
		break;
	case "��ַ�":
		$aset['dot'] = $a[1];
		break;
	case "���ڷ�Χ":
		$aset['settr'] = $a[1];
		break;
	case "ְλ����":
		$aset['jobcategory'] = trim($a[1]);
		break;
	case "ְλ����":
		$aset['category'] = trim($a[1]);
		break;
	case "ְλС��":
		$aset['subclass'] = trim($a[1]);
		break;
	case "��������":
		$aset['citycategory'] = trim($a[1]);
		break;
	case "��������":
		$aset['district'] = $a[1];
		break;
	case "����С��":
		$aset['sdistrict'] = $a[1];
		break;
	case "��ҵ":
		$aset['trade'] = trim($a[1]);
		break;
	case "רҵ":
		$aset['major'] = trim($a[1]);
		break;
	case "��ǩ":
		$aset['tag'] = $a[1];
		break;
	case "ѧ��":
		$aset['education'] = $a[1];
		break;
	case "��������":
		$aset['experience'] = $a[1];
		break;
	case "�ȼ�":
		$aset['talent'] = $a[1];
		break;
	case "�Ա�":
		$aset['sex'] = $a[1];  // �����������  �� Ů
		break;
	case "��Ƭ":
		$aset['photo'] = $a[1];
		break;
	case "�ؼ���":
		$aset['key'] = $a[1];
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
	case "������ļ���":
		$aset['readresume'] = $a[1];
		break;
	}
}
if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['row']=intval($aset['row'])>0?intval($aset['row']):20;
if ($aset['row']>20)$aset['row']=20;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['namelen']=isset($aset['namelen'])?intval($aset['namelen']):4;
$aset['specialtylen']=isset($aset['specialtylen'])?intval($aset['specialtylen']):0;
$aset['jobslen']=isset($aset['jobslen'])?intval($aset['jobslen']):0;
$aset['majorlen']=isset($aset['majorlen'])?intval($aset['majorlen']):50;
$aset['dot']=isset($aset['dot'])?$aset['dot']:null;
$aset['showname']=isset($aset['showname'])?$aset['showname']:'QS_resumeshow';
$aset['listpage']=isset($aset['listpage'])?$aset['listpage']:'QS_resumelist';
$resumetable=table('resume_search_rtime');
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
		$orderbysql=" ORDER BY r.refreshtime {$arr[1]},id desc ";
	}
}
else
{
	$orderbysql=" ORDER BY r.refreshtime desc,id desc ";
}
if (intval($_CFG['subsite_id'])>0)
{
	$wheresql.=" AND subsite_id=".intval($_CFG['subsite_id'])." ";
}
//Ӧ��������
if(isset($aset['campu_sresume']) && !empty($aset['campu_sresume']))
{
	$wheresql.=" AND r.experience='-1' ";
}
//ָ��ԺУ����
if(isset($aset['campusname']) && !empty($aset['campusname']))
{
	$joinwheresql_campusname.=" WHERE  school = '{$aset['campusname']}' ";
	$joinsql = "  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_education')." {$joinwheresql_campusname} )AS e ON  r.id=e.pid ";
}
//����ʱ��
if(isset($aset['refreshtime']) && !empty($aset['refreshtime']))
{
	$refreshtime_min = strtotime("-".$aset['refreshtime']."day");
	$wheresql.=" AND r.refreshtime > {$refreshtime_min} ";
}
if (!empty($aset['category']) || !empty($aset['subclass']) || !empty($aset['jobcategory']))
{
	if (!empty($aset['jobcategory']))
	{
					$dsql=$xsql="";
					$arr=explode("_",$aset['jobcategory']);
					$arr=array_unique($arr);
					foreach($arr as $sid)
					{
						$cat=explode(".",$sid);
						if (intval($cat[2])===0)
						{
						$dsql.= " OR category =".intval($cat[1]);
						}
						else
						{
						$xsql.= " OR subclass =".intval($cat[2]);
						}
					}
					$joinwheresql.=" WHERE ".ltrim(ltrim($dsql.$xsql),'OR');
	}
	else
	{
					if (!empty($aset['category']))
					{
						if (strpos($aset['category'],"-"))
						{
							$or=$orsql="";
							$arr=explode("-",$aset['category']);
							$sqlin=implode(",",$arr);
							if (count($arr)>10) exit();
							$sqlin=implode(",",$arr);
							if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
							{
								$joinwheresql.=" AND topclass IN  ({$sqlin}) ";
							}
						}
						else
						{
							$joinwheresql.=" AND  topclass=".intval($aset['category']);
						}
					}
					if (!empty($aset['subclass']))
					{
						if (strpos($aset['subclass'],"-"))
						{
							$or=$orsql="";
							$arr=explode("-",$aset['subclass']);
							if (count($arr)>10) exit();
							$sqlin=implode(",",$arr);
							if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
							{
								$joinwheresql.=" AND category IN  ({$sqlin}) ";
							}
						}
						else
						{
						$joinwheresql.=" AND category=".intval($aset['subclass']);
						}
					}
					if (!empty($joinwheresql))
					{
					$joinwheresql=" WHERE ".ltrim(ltrim($joinwheresql),'AND');
					}
					
	}
	$joinsql="  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_jobs')." {$joinwheresql} )AS j ON  r.id=j.pid ";
}
if (!empty($aset['trade']))
{
	
	if (strpos($aset['trade'],"_"))
	{
		$or=$orsql="";
		$arr=explode("_",$aset['trade']);
		if (count($arr)>10) exit();
		$sqlin=implode(",",$arr);
		if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
		{
			$joinwheresql_trade.=" AND trade IN  ({$sqlin}) ";
		}
	}
	else
	{
	$joinwheresql_trade.=" AND trade=".intval($aset['trade']);
	}
	
	if (!empty($joinwheresql_trade))
	{
	$joinwheresql_trade=" WHERE ".ltrim(ltrim($joinwheresql_trade),'AND');
	}
	$joinsql=$joinsql==""?"  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_trade')." {$joinwheresql_trade} )AS t ON  r.id=t.pid ":$joinsql."  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_trade')." {$joinwheresql_trade} )AS t ON  r.id=t.pid ";
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
// major רҵ����
if (!empty($aset['major']))
{
	
	if (strpos($aset['major'],"_"))
	{
		$or=$orsql="";
		$arr=explode("_",$aset['major']);
		if (count($arr)>10) exit();
		$sqlin=implode(",",$arr);
		if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
		{
			$wheresql.=" AND r.major IN  ({$sqlin}) ";
		}
	}
	else
	{
	$wheresql.=" AND r.major=".intval($aset['major']);
	}	
}
if (!empty($aset['district']) || !empty($aset['sdistrict']) || !empty($aset['citycategory']))
{
	if (!empty($aset['citycategory']))
	{
					$dsql=$xsql="";
					$arr=explode("_",$aset['citycategory']);
					$arr=array_unique($arr);
					foreach($arr as $sid)
					{
						$cat=explode(".",$sid);
						if (intval($cat[1])===0)
						{
						$dsql.= " OR r.district =".intval($cat[0]);
						}
						else
						{
						$xsql.= " OR r.sdistrict =".intval($cat[1]);
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
					if (count($arr)>10) exit();
					$sqlin=implode(",",$arr);
					if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
					{
						$wheresql.=" AND r.district IN  ({$sqlin}) ";
					}
				}
				else
				{
				$wheresql.=" AND r.district=".intval($aset['district'])." ";
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
						$wheresql.=" AND r.sdistrict IN  ({$sqlin}) ";
					}
				}
				else
				{
					$wheresql.=" AND r.sdistrict=".intval($aset['sdistrict'])." ";
				}
			}	
					
	}
}
if (isset($aset['experience']) && !empty($aset['experience']))
{
	$wheresql.=" AND r.experience=".intval($aset['experience'])." ";
}
if (isset($aset['education']) && !empty($aset['education']))
{
	$wheresql.=" AND r.education=".intval($aset['education'])." ";
}
if (isset($aset['talent']) && !empty($aset['talent']))
{
	$wheresql.=" AND r.talent=".intval($aset['talent'])." ";
}
if (isset($aset['sex']) && !empty($aset['sex']))
{
	$wheresql.=" AND r.sex=".intval($aset['sex'])." "; // �����������  �� Ů
}
if (isset($aset['photo']) && !empty($aset['photo']))
{
	$wheresql.=" AND r.photo='".intval($aset['photo'])."' ";
}
if (isset($aset['key']) && !empty($aset['key']))
{
	if ($_CFG['resumesearch_purview']=='2')
	{
		if ($_SESSION['username']=='')
		{
		header("Location: ".url_rewrite('QS_login')."?url=".urlencode($_SERVER["REQUEST_URI"]));
		}
	}
	$key=help::addslashes_deep(trim($aset['key']));
	if ($_CFG['resumesearch_type']=='1')
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
		$wheresql.=" AND  MATCH (r.`key`) AGAINST ('{$key}'{$mode}) ";
	}
	else
	{
		$wheresql.=" AND r.likekey LIKE '%{$key}%' ";
	}
	$resumetable=table('resume_search_key');
}
if (!empty($aset['tag']))
{
	if (strpos($aset['tag'],","))
	{
		$or=$orsql="";
		$arr=explode(",",$aset['tag']);
		if (count($arr)>10) exit();
		$sqlin=implode(",",$arr);
		if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
		{
			$joinwheresql_tag.=" AND tag IN  ({$sqlin}) ";
		}
	}
	else
	{
	$joinwheresql_tag.=" AND tag=".intval($aset['tag']);
	}
	
	if (!empty($joinwheresql_tag))
	{
	$joinwheresql_tag=" WHERE ".ltrim(ltrim($joinwheresql_tag),'AND');
	}
	$joinsql=$joinsql==""?"  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_tag')." {$joinwheresql_tag} )AS g ON  r.id=g.pid ":$joinsql."  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_tag')." {$joinwheresql_tag} )AS g ON  r.id=g.pid ";
}
/* ���� ʱ�䷶Χ */
$moth=intval($_CFG['search_time']);
if($moth>0)
{
	$moth_time=$moth*3600*24*30;
	$time=time()-$moth_time;
	$wheresql.=" AND r.refreshtime>$time ";
}
$wheresql.=" AND r.display='1' AND r.audit='1' ";
if (!empty($wheresql))
{
$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
}
		if (isset($aset['paged']))
		{
			require_once(QISHI_ROOT_PATH.'include/page.class.php');
			$total_sql="SELECT  COUNT(*) AS num  FROM  {$resumetable} AS r ".$joinsql.$wheresql;
			$total_count=$db->get_total($total_sql);
			if (intval($_CFG['resume_list_max'])>0)
			{
				$total_count>intval($_CFG['resume_list_max']) && $total_count=intval($_CFG['resume_list_max']);
			}
			//�����������ļ��� ����Ӧ����cookie�е�
			if ($aset['readresume']) 
			{
				$total_count = count($_COOKIE['QS']['view_resume_log']);
			}
			//echo $total_sql;
			//echo "SELECT  COUNT(DISTINCT r.id) AS num  FROM  ".table('resume')." AS r ".$wheresql;
			$page = new page(array('total'=>$total_count, 'perpage'=>$aset['row'],'alias'=>$aset['listpage'],'getarray'=>$_GET));
			$currenpage=$page->nowindex;
			$aset['start']=abs($currenpage-1)*$aset['row'];
			if ($total_count>$aset['row'])
			{
			$smarty->assign('page',$page->show(8));
			$smarty->assign('pagemin',$page->show(7));
			$smarty->assign('pagenow',$page->show(6));
			}
			$smarty->assign('total',$total_count);
		}
	$limit=" LIMIT {$aset['start']} , {$aset['row']}";
	$list = $id = array();
	$idresult = $db->query("SELECT id FROM {$resumetable}  AS r".$joinsql.$wheresql.$orderbysql.$limit);
	//echo "SELECT id FROM {$resumetable}  AS r".$joinsql.$wheresql.$orderbysql.$limit;

	// �����������ļ�������cookie�л�ȡ������id
	if ($aset['readresume']) {
		foreach ($_COOKIE['QS']['view_resume_log'] as $value) {
		   $id[]=$value;
		}
	} else {
		while($row = $db->fetch_array($idresult))
		{
			$id[]=$row['id'];
		}
	}
	
	if (!empty($id))
	{
	$wheresql=" WHERE id IN (".implode(',',$id).") and display!=3 ";
	$result = $db->query("SELECT id,subsite_id,uid,display,display_name,nature_cn,fullname,sex,major_cn,specialty,intention_jobs,trade_cn,photo,photo_img,photo_display,refreshtime,birthdate,tag_cn,talent,education_cn,sex_cn,wage_cn,experience_cn,district_cn,current_cn FROM ".table('resume')."  AS r ".$joinsql.$wheresql.$orderbysql);
		while($row = $db->fetch_array($result))
		{
			if ($row['display_name']=="2")
			{
				$row['fullname']="N".str_pad($row['id'],7,"0",STR_PAD_LEFT);
				$row['fullname_']=$row['fullname'];		
			}
			elseif($row['display_name']=="3")
			{ 
				if($row['sex']==1){
				$row['fullname']=cut_str($row['fullname'],1,0,"����");
				}elseif($row['sex'] == 2){
				$row['fullname']=cut_str($row['fullname'],1,0,"Ůʿ");
				}else{
				$row['fullname']=cut_str($row['fullname'],1,0,"**");
				}	
			}
			else
			{
				$row['fullname_']=$row['fullname'];
				$row['fullname']=cut_str($row['fullname'],$aset['namelen'],0,$aset['dot']);
			}
			if(in_array($row['id'],$_COOKIE['QS']['view_resume_log'])){
				$row['checked'] = true;
			}else{
				$row['checked'] = false;
			}
			$row['specialty_']=strip_tags($row['specialty']);
			if ($aset['specialtylen']>0)
			{
			$row['specialty']=cut_str(strip_tags($row['specialty']),$aset['specialtylen'],0,$aset['dot']);
			}
			$row['intention_jobs_'] = $row['intention_jobs'];
			if ($aset['jobslen']>0)
			{
			$row['intention_jobs']=cut_str(strip_tags($row['intention_jobs']),$aset['jobslen'],0,$aset['dot']);
			}
			if ($aset['majorlen']>0)
			{
			$row['major_cn']=cut_str(strip_tags($row['major_cn']),$aset['majorlen'],0,$aset['dot']);
			}
			$row['trade_cn_'] = $row['trade_cn'];
			$row['trade_cn'] = cut_str(strip_tags($row['trade_cn']),10,0,"..");
			$row['resume_url']=url_rewrite($aset['showname'],array('id'=>$row['id']),1,$row['subsite_id']);
			$row['refreshtime_cn']=daterange(time(),$row['refreshtime'],'Y-m-d',"#FF3300");
			$row['age']=date("Y")-$row['birthdate'];
			if ($row['tag_cn'])
			{
				$tag_cn=explode(',',$row['tag_cn']);
				$row['tag_cn']=$tag_cn;
			}
			else
			{
			$row['tag_cn']=array();
			}
			// ��Ƭ��ʾ��ʽ
			if ($row['photo']=="1")
			{
				if($row['photo_display']=="1")
				{
					$row['photosrc']=$_CFG['resume_photo_dir_thumb'].$row['photo_img'];
				}
				else
				{
					$row['photosrc']=$_CFG['resume_photo_dir_thumb']."no_photo_display.gif";
				}
				
			}
			else
			{
				$row['photosrc']=$_CFG['resume_photo_dir_thumb']."no_photo.gif";
			}
			//�ж��ֻ��Ƿ���֤
			$is_audit_phone = $db->getone("SELECT mobile_audit FROM ".table('members')." WHERE uid={$row['uid']}  LIMIT 1 ");
			$row['is_audit_mobile'] = $is_audit_phone['mobile_audit'];
			//��������
			$language = $db->getall("SELECT * FROM ".table('resume_language')." WHERE pid={$row['id']} ");
			$row['language'] = $language;
			$list[] = $row;
		}
	}
	else
	{
	$list=array();
	}
	$smarty->assign($aset['listname'], $list);
}
?>