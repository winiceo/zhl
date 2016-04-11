<?php
function tpl_function_qishi_xs($params, &$smarty)
{
global $db,$_CFG;
$arrset=explode(',',$params['set']);
foreach($arrset as $str)
{
	$a=explode(':',$str);
	switch ($a[0])
	{
		case "列表名":
			$aset['listname'] = $a[1];
			break;
		case "日期范围":
			$aset['settr'] = $a[1];
			break;
	}
}
$time = $aset['settr'];
if ($time == 'all')
{
	$company_sql = "select count(DISTINCT company_id) as company_num from ".table('xs_points');
	$company_res = $db->query($company_sql);
	while($company_row = $db->fetch_array($company_res))
	{
		$company = $company_row;
	}
	$company_num = $company['company_num'];
	
	$job_sql = "select count(DISTINCT job_id ) as job_num from ".table('xs_points');
	$job_res = $db->query($job_sql);
	while($job_row = $db->fetch_array($job_res))
	{
		$job = $job_row;
	}
	$job_num = $job['job_num'];
	
	$money_sql = "select SUM(residue) as money_num from ".table('xs_points');
	$money_res = $db->query($money_sql);
	while($money_row = $db->fetch_array($money_res))
	{
		$money = $money_row;
	}
	$money_num = $money['money_num'];
	
}elseif ($time == 'year')
{
	$time_start = strtotime("-1 year");

	$company_sql = "select count(DISTINCT company_id) as company_num from ".table('xs_points')." where addtime>".$time_start;
	$company_res = $db->query($company_sql);
	while($company_row = $db->fetch_array($company_res))
	{
		$company = $company_row;
	}
	$company_num = $company['company_num'];
	
	$job_sql = "select count(DISTINCT job_id ) as job_num from ".table('xs_points')." where addtime>".$time_start;
	$job_res = $db->query($job_sql);
	while($job_row = $db->fetch_array($job_res))
	{
		$job = $job_row;
	}
	$job_num = $job['job_num'];
	
	$money_sql = "select SUM(residue) as money_num from ".table('xs_points')." where addtime>".$time_start;
	$money_res = $db->query($money_sql);
	while($money_row = $db->fetch_array($money_res))
	{
		$money = $money_row;
	}
	$money_num = $money['money_num'];
}
elseif ($time == 'month')
{
	$time_start = strtotime("-1 month");
	//echo $time_start;
	$company_sql = "select count(DISTINCT company_id) as company_num from ".table('xs_points')." where addtime>".$time_start;
	$company_res = $db->query($company_sql);
	while($company_row = $db->fetch_array($company_res))
	{
		$company = $company_row;
	}
	$company_num = $company['company_num'];
	
	$job_sql = "select count(DISTINCT job_id ) as job_num from ".table('xs_points')." where addtime>".$time_start;
	$job_res = $db->query($job_sql);
	while($job_row = $db->fetch_array($job_res))
	{
		$job = $job_row;
	}
	$job_num = $job['job_num'];
	
	$money_sql = "select SUM(residue) as money_num from ".table('xs_points')." where addtime>".$time_start;
	$money_res = $db->query($money_sql);
	while($money_row = $db->fetch_array($money_res))
	{
		$money = $money_row;
	}
	$money_num = $money['money_num'];
}
elseif ($time == 'yestoday')
{
	$time_start = strtotime("-1 day");
	$company_sql = "select count(DISTINCT company_id) as company_num from ".table('xs_points')." where addtime>".$time_start;
	$company_res = $db->query($company_sql);
	while($company_row = $db->fetch_array($company_res))
	{
		$company = $company_row;
	}
	$company_num = $company['company_num'];
	
	$job_sql = "select count(DISTINCT job_id ) as job_num from ".table('xs_points')." where addtime>".$time_start;
	$job_res = $db->query($job_sql);
	while($job_row = $db->fetch_array($job_res))
	{
		$job = $job_row;
	}
	$job_num = $job['job_num'];
	
	$money_sql = "select SUM(residue) as money_num from ".table('xs_points')." where addtime>".$time_start;
	$money_res = $db->query($money_sql);
	while($money_row = $db->fetch_array($money_res))
	{
		$money = $money_row;
	}
	$money_num = $money['money_num'];
}
		
	$list['company_num']=$company_num;
	$list['job_num']=$job_num;
	$list['money_num']=empty($money_num)?0:$money_num;
	//var_dump($list);die;
	$smarty->assign($aset['listname'], $list);
}
?>