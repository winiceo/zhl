<?php
 /*
 * WAP
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
require_once(dirname(__FILE__).'/weixin_share.php');
$page = empty($_GET['page'])?1:intval($_GET['page']);
$district = intval($_GET['district'])==0?"":intval($_GET['district']);
$sdistrict = intval($_GET['sdistrict'])==0?"":intval($_GET['sdistrict']);
$experience = intval($_GET['experience'])==0?"":intval($_GET['experience']);
$education = intval($_GET['education'])==0?"":intval($_GET['education']);
$topclass = intval($_GET['topclass'])==0?"":intval($_GET['topclass']);
$category = intval($_GET['category'])==0?"":intval($_GET['category']);
$subclass = intval($_GET['subclass'])==0?"":intval($_GET['subclass']);
$talent = intval($_GET['talent'])==0?"":intval($_GET['talent']);
$sex = intval($_GET['sex'])==0?"":intval($_GET['sex']);
$settr = intval($_GET['settr'])==0?"":intval($_GET['settr']);
$photo = intval($_GET['photo'])==0?"":intval($_GET['photo']);

$key = empty($_GET['key'])?"":$_GET['key'];
$jobstable=table('resume_search_rtime');
if ($_CFG['subsite_id']>0)
{
	$wheresql.=" AND `subsite_id` = ".intval($_CFG['subsite_id']);
}
if($talent<>'')
{
	$wheresql.=" AND `talent`=".$talent." ";
}
if($settr<>'')
{
	$settr_val=strtotime("-{$settr} day");
	$wheresql.=" AND refreshtime>{$settr_val} ";
}
if($sex<>'')
{
	$wheresql.=" AND sex={$sex} ";
}
if($photo<>'')
{
	$wheresql.=" AND photo!=0 ";
}
if ($district<>'' || $sdistrict<>'')
{
	if($district<>'')
	{
		$wheresql.=" AND  district=".$district;
	}
	if ($sdistrict<>'')
	{
		$wheresql.=" AND `sdistrict` = ".$sdistrict;
	}
	/*if (!empty($d_joinwheresql))
	{
	$d_joinwheresql=" WHERE ".ltrim(ltrim($d_joinwheresql),'AND');
	}
	$joinsql="  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_district')." {$d_joinwheresql} )AS d ON  r.id=d.pid ";*/
}
if ($experience<>'')
{
	$wheresql.=" AND `experience`=".$experience." ";
}
if ($education<>'')
{
	$wheresql.=" AND `education`=".$education." ";
}
if ($topclass<>'' || $category<>'' || $subclass<>'')
{
	if ($topclass<>'')
	{
		$joinwheresql.=" AND  topclass=".$topclass;
	}
	if ($category<>'')
	{
		$joinwheresql.=" AND  category=".$category;
	}
	if ($subclass<>'')
	{
		$joinwheresql.=" AND  subclass=".$subclass;
	}
	if (!empty($joinwheresql))
	{
	$joinwheresql=" WHERE ".ltrim(ltrim($joinwheresql),'AND');
	}
	$joinsql=$joinsql==""?"  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_jobs')." {$joinwheresql} )AS j ON  r.id=j.pid ":$joinsql."  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_jobs')." {$joinwheresql} )AS j ON  r.id=j.pid ";
}

if (!empty($key))
{
	$key=trim($key);
	$akey=explode(' ',$key);
	if (count($akey)>1)
	{
	$akey=array_filter($akey);
	$akey=array_slice($akey,0,2);
	$akey=array_map("fulltextpad",$akey);
	$ykey='+'.implode(' +',$akey);
	$mode=' IN BOOLEAN MODE';
	}
	else
	{
	$ykey=fulltextpad($key);
	$mode=' ';
	}
	$wheresql.=" AND  MATCH (`key`) AGAINST ('{$ykey}'{$mode}) ";
	$jobstable=table('resume_search_key');
}
$wheresql.=" AND display=1 AND audit=1 ";
$orderbysql=" ORDER BY `refreshtime` desc,`id` desc ";
if (!empty($wheresql))
{
$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
}

	$perpage = 5;
	$count  = 0;
	$page = empty($_GET['page'])?1:intval($_GET['page']);
	if($page<1) $page = 1;
	
	$theurl = "resume-list.php?sdistrict=".$sdistrict."&amp;subclass=".$subclass."&amp;key=".$key;
	$start = ($page-1)*$perpage;
	$total_sql="SELECT COUNT(*) AS num FROM {$jobstable} as r {$joinsql} {$wheresql}";
	$count=$db->get_total($total_sql);
	$limit=" LIMIT {$start},{$perpage}";
	$idresult = $db->query("SELECT id FROM {$jobstable} as r ".$joinsql.$wheresql.$orderbysql.$limit);
	while($row = $db->fetch_array($idresult))
	{
	$id[]=$row['id'];
	}
	if (!empty($id))
	{
		$wheresql=" WHERE id IN (".implode(',',$id).") AND display=1 AND audit=1 ";
		$resume = $db->getall("SELECT * FROM ".table('resume').$wheresql.$orderbysql);	
		foreach ($resume as $key => $value) {
			if ($value['display_name']=="2")
			{
				$value['fullname']="N".str_pad($value['id'],7,"0",STR_PAD_LEFT);
				$value['fullname_']=$value['fullname'];		
			}
			elseif($value['display_name']=="3")
			{
				if($value['sex']==1)
				{
					$value['fullname']=cut_str($value['fullname'],1,0,"����");
				}
				elseif($value['sex']==2)
				{
					$value['fullname']=cut_str($value['fullname'],1,0,"Ůʿ");
				}
				$value['fullname_']=$value['fullname'];	
			}
			else
			{
				$value['fullname_']=$value['fullname'];
				$value['fullname']=$value['fullname'];
			}
			$resume[$key]['url'] = wap_url_rewrite("resume-show",array("id"=>$value["id"]),1,$value['subsite_id']);
			$resume[$key]['fullname_']=$value['fullname_'];
			$resume[$key]['fullname']=$value['fullname'];
			$resume[$key]['refreshtime_cn']=daterange(time(),$value['refreshtime'],'Y-m-d',"#FF3300");
			//���û�Ա���ֻ���֤״̬
			$user_info = $db->getone("SELECT mobile_audit FROM ".table('members')." WHERE uid=".$value['uid']);
			$resume[$key]['mobile_audit']=$user_info['mobile_audit'];
		}
		
	}
	else
	{
		$resume=array();
	}
	$smarty->assign('resume',$resume);
	$smarty->assign('pagehtml',wapmulti($count, $perpage, $page, $theurl));
	$smarty->display("m/m-resume-list.html");
?>