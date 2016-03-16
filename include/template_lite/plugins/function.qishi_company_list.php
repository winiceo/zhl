<?php
function tpl_function_qishi_company_list($params, &$smarty)
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
	case "��ҵ������":
		$aset['companynamelen'] = $a[1];
		break;
	case "��������":
		$aset['brieflylen'] = $a[1];
		break;
	case "��ַ�":
		$aset['dot'] = $a[1];
		break;
	case "��ҵ":
		$aset['trade'] = $a[1];
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
	case "��ҵ����":
		$aset['nature'] = $a[1];
		break;
	case "��ҳ":
		$aset['yellowpages'] = $a[1];
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
	case "��˾ҳ��":
		$aset['companyshow'] = $a[1];
		break;
	case "�б�ҳ":
		$aset['listpage'] = $a[1];
		break;
	case "��ģ":
		$aset['scale'] = $a[1];
		break;
	}
}
if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['row']=!empty($aset['row'])?intval($aset['row']):10;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['companynamelen']=isset($aset['companynamelen'])?intval($aset['companynamelen']):16;
$aset['dot']=isset($aset['dot'])?$aset['dot']:'';
$aset['companyshow']=isset($aset['companyshow'])?$aset['companyshow']:'QS_companyshow';
$aset['listpage']=!empty($aset['listpage'])?$aset['listpage']:'QS_companylist';
if ($aset['displayorder'])
{
	if (strpos($aset['displayorder'],'>'))
	{
		$arr=explode('>',$aset['displayorder']);
		// �����ֶ�
		if($arr[0]=='click'){
			$arr[0]="click";
		}
		elseif($arr[0]=="id")
		{
			$arr[0]="id";
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
	}
}
if (isset($aset['trade']) && intval($aset['trade'])>0)
{
	$wheresql.=" AND trade=".intval($aset['trade']);
}
if (isset($aset['nature']) && intval($aset['nature'])>0)
{
	$wheresql.=" AND nature=".intval($aset['nature']);
}
if (isset($aset['scale']) && intval($aset['scale'])>0)
{
	$wheresql.=" AND scale=".intval($aset['scale']);
}
if (!empty($aset['citycategory']))
{
		$dsql=$xsql="";
		$arr=explode(",",$aset['citycategory']);
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
if (!empty($aset['key']))
{
$key=help::addslashes_deep(trim($aset['key']));
$wheresql.=" AND companyname like '%{$key}%'";
}
if (!empty($wheresql))
{
$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
}
if (isset($aset['paged']))
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('company_profile').$wheresql;
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
$result = $db->query("SELECT id,companyname,contents,logo,audit,trade_cn,district_cn,nature_cn FROM ".table('company_profile')." ".$wheresql.$orderbysql.$limit);
$list= array();
if(intval($_CFG['subsite_id'])>0){
	$countwheresql = ' and subsite_id='.intval($_CFG['subsite_id']);
}else{
	$countwheresql = '';
}		
while($row = $db->fetch_array($result))
{
	$row['companyname_']=$row['companyname'];
	$row['companyname']=cut_str($row['companyname'],$aset['companynamelen'],0,$aset['dot']);
	$row['url'] = url_rewrite($aset['companyshow'],array('id'=>$row['id']));
	$row['contents']=str_replace('&nbsp;','',$row['contents']);
	$row['briefly_']=strip_tags($row['contents']);
	$row['briefly']=strip_tags($row['briefly_']);
		if ($aset['brieflylen']>0)
		{
		$row['briefly']=cut_str(strip_tags($row['contents']),$aset['brieflylen'],0,$aset['dot']);
		}
		if ($row['logo'])
		{
			$row['logo']=$_CFG['site_dir']."data/logo/".$row['logo'];
		}
		else
		{
			$row['logo']=$_CFG['site_dir']."data/logo/no_logo.gif";
		}
	$row['jobs_num'] = $db->get_total("select count(*) as num from ".table('jobs')." where company_id=".$row['id'].$countwheresql);
	$row['comment_num'] = $db->get_total("select count(*) as num from ".table('comment')." where company_id=".$row['id']." and audit=1");
	$list[] = $row;
}
$smarty->assign($aset['listname'],$list);
}
?>