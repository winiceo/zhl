<?php
function tpl_function_qishi_video($params, &$smarty)
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
		case "显示数目":
			$aset['num'] = $a[1];
			break;
		case "排序":
			$aset['orderby'] = $a[1];
			break;
	}
}

$utype = $aset['utype'];
$num = $aset['num'];
$order = $aset['orderby'];

$wheresql = "";
$ordersql = "";
$limitsql = "";
if(!empty($num))
{
	$limitsql = " limit ".$num;
}
if(!empty($order))
{
	
	$arr=explode('>',$aset['orderby']);
	// 排序方式
	if($arr[1]=='desc'){
		$arr[1]="desc";
	}
	elseif($arr[1]=="asc")
	{
		$arr[1]="asc";
	}
	$ordersql = " order by ".$arr[0]." ".$arr[1];
}

$sql = "select * from ".table('video').$wheresql.$ordersql.$limitsql;
//echo $sql;
$res = $db->query($sql);
while($row = $db->fetch_array($res))
{
	$list[]=$row;
}
	//var_dump($list);
	$smarty->assign($aset['listname'], $list);
}
?>