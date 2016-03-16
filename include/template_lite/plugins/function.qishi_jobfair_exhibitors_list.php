<?php
function tpl_function_qishi_jobfair_exhibitors_list($params, &$smarty)
{
global $db,$_CFG;
$arr=explode(',',$params['set']);
foreach($arr as $str)
{
$a=explode(':',$str);
	switch ($a[0])
	{
	case "�б���":
		$aset['listname'] = $a[1];
		break;
	case "��Ƹ��ID":
		$aset['jobfairid'] = $a[1];
		break;
	case "��ʾ��Ŀ":
		$aset['row'] = $a[1];
		break;
	case "��˾���Ƴ���":
		$aset['titlelen'] = $a[1];
		break;	
	case "��ʼλ��":
		$aset['start'] = $a[1];
		break;
	case "��ַ�":
		$aset['dot'] = $a[1];
		break;
	case "���ڷ�Χ":
		$aset['settr'] = $a[1];
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
	case "�λ���ҵҳ":
		$aset['exhibitorspage'] = $a[1];
		break;
	}
}
if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['row']=isset($aset['row'])?intval($aset['row']):10;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['titlelen']=isset($aset['titlelen'])?intval($aset['titlelen']):25;
$aset['showname']=isset($aset['showname'])?$aset['showname']:'QS_companyshow';
$aset['listpage']=isset($aset['listpage'])?$aset['listpage']:'QS_jobfairexhibitors';
$orderbysql=" order BY id DESC ";
$wheresql=" WHERE e.jobfairid=".intval($aset['jobfairid'])." AND e.audit=1 ";
if (isset($aset['settr']))
{
$settr_val=strtotime("-".intval($aset['settr'])." day");
$wheresql.=" AND e.eaddtime > ".$settr_val;
}
if (isset($aset['paged']))
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('jobfair_exhibitors')." as e ".$wheresql;
	$total_count=$db->get_total($total_sql);
	$pagelist = new page(array('total'=>$total_count, 'perpage'=>$aset['row'],'alias'=>$aset['listpage'],'getarray'=>$_GET));
	$currenpage=$pagelist->nowindex;
	$aset['start']=($currenpage-1)*$aset['row'];
		if ($total_count>$aset['row'])
		{
		$smarty->assign('page',$pagelist->show(3));
		}
		else
		{
		$smarty->assign('page','');
		}
		$smarty->assign('total',$total_count);
}
$limit=" LIMIT ".abs($aset['start']).','.$aset['row'];
$result = $db->query("SELECT e.id,e.uid,e.company_id,e.companyname,c.nature_cn FROM ".table('jobfair_exhibitors')." as e left join ".table('company_profile')." as c on e.company_id=c.id ".$wheresql.$orderbysql.$limit);
$list= array();
//echo "SELECT * FROM ".table('jobfair_exhibitors')." ".$wheresql.$orderbysql.$limit;
while($row = $db->fetch_array($result))
{
	$row['companyname_']=$row['companyname'];
	$row['companyname']=cut_str($row['companyname'],$aset['titlelen'],0,$aset['dot']);
	if ($row['uid']>0)
	{
	$row['url'] =url_rewrite($aset['showname'],array('id'=>$row['company_id']));
	}
	else
	{
	$row['url']="";
	}
	$jobslist = $db->getall("select jobs_name,amount from ".table('jobs')." where company_id=".$row['company_id']);
	foreach ($jobslist as $key => $value) {
		$row['jobslist'] .= $value['jobs_name']."(".$value['amount'].") , ";
	}
	$row['jobslist'] = rtrim($row['jobslist']," , ");
	$list[] = $row;
}
$smarty->assign($aset['listname'],$list);
}
?>