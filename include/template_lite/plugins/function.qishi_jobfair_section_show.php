<?php
function tpl_function_qishi_jobfair_section_show($params, &$smarty)
{
global $db,$_CFG;
$arr=explode(',',$params['set']);
foreach($arr as $str)
{
$a=explode(':',$str);
	switch ($a[0])
	{
	case "��Ƹ��ID":
		$aset['id'] = $a[1];
		break;
	case "�б���":
		$aset['listname'] = $a[1];
		break;
	}
}
$aset=array_map("get_smarty_request",$aset);
$aset['id']=$aset['id']?intval($aset['id']):0;
$aset['listname']=$aset['listname']?$aset['listname']:"list";
unset($arr,$str,$a,$params);
$sql = "select * from ".table('jobfair_section')." WHERE  id=".intval($aset['id'])." LIMIT 1";
$val=$db->getone($sql);
if (empty($val))
{
	header("HTTP/1.1 404 Not Found"); 
	$smarty->display("404.htm");
	exit();
}
check_url($val['subsite_id'],$smarty,$_CFG['jobfair_url']);
if($val['is_singleness']==1)
{
	$company_list=$db->getall("select c.*,i.companyname,i.logo from ".table('jobfair_section_company')." as c left join ".table('company_profile')." as i on c.company_id=i.id where c.trade in ($val[trade]) and c.jobfair_id=$val[id] ");
	foreach ($company_list as $key => $value) {
		$jobs = $db->getall("select id,subsite_id,jobs_name,wage_cn from ".table('jobs')." where company_id=$value[company_id] order by id desc limit 4 ");
		foreach ($jobs as $k => $v) {
			$v['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$v['id']),1,$v['subsite_id']);
			$jobs[$k]=$v;
		}
		$value['jobs']=$jobs;
		$value['company_url']= url_rewrite('QS_companyshow',array('id'=>$value['company_id']));
		$val['company_list'][$key]=$value;
	}
}
// ����ҵ
else
{
	$trade_arr =explode(",", $val['trade_cn']);
	foreach ($trade_arr as $key => $value) {
		$trade_arr_[$value]='';
	}

	$company_list=$db->getall("select c.*,i.companyname,i.logo,i.trade_cn,i.district_cn from ".table('jobfair_section_company')." as c left join ".table('company_profile')." as i on c.company_id=i.id where c.trade in ($val[trade]) and c.jobfair_id=$val[id] ");
	foreach ($company_list as $key => $value) {
		$jobs = $db->getall("select id,subsite_id,jobs_name,wage_cn from ".table('jobs')." where company_id=$value[company_id] order by id desc limit 2 ");
		foreach ($jobs as $k => $v) {
			$v['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$v['id']),1,$v['subsite_id']);
			$jobs[$k]=$v;
		}
		$value['jobs']=$jobs;
		$value['company_url']= url_rewrite('QS_companyshow',array('id'=>$value['company_id']));
		$company_list[$key]=$value;
	}
	foreach ($company_list as $key => $value) {
		$trade_arr_[$value['trade_cn']][]= $value;

	}
	$trade_arr_ =array_values($trade_arr_);
	$smarty->assign('trade_arr',$trade_arr);
	$smarty->assign('trade_arr_com',$trade_arr_);
}
$smarty->assign($aset['listname'],$val);
}
?>