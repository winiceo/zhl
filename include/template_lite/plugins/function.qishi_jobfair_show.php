<?php
function tpl_function_qishi_jobfair_show($params, &$smarty)
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
	case "�λ���ҵҳ":
		$aset['exhibitorspage'] = $a[1];
		break;
	}
}
$aset=array_map("get_smarty_request",$aset);
$aset['id']=$aset['id']?intval($aset['id']):0;
$aset['listname']=$aset['listname']?$aset['listname']:"list";
$aset['exhibitorspage']=isset($aset['exhibitorspage'])?$aset['exhibitorspage']:'QS_jobfairexhibitors';
unset($arr,$str,$a,$params);
$sql = "select id,subsite_id,title,holddate_start,holddate_end,predetermined_status,predetermined_start,predetermined_end,trade_cn,bus,introduction,predetermined_web,contact,phone,address,service_setmeal,price,map_x,map_y,map_zoom from ".table('jobfair')." WHERE  id=".intval($aset['id'])." AND  display=1 LIMIT 1";
$val=$db->getone($sql);
if (empty($val))
{
	header("HTTP/1.1 404 Not Found"); 
	$smarty->display("404.htm");
	exit();
}
check_url($val['subsite_id'],$smarty,$_CFG['jobfair_url']);
$week=array('������','����һ','���ڶ�','������','������','������','������');
$val['title']=cut_str($val['title'],15,0,"");
$val['holddates_week']=$week[date("w",$val['holddate_start'])];
$val['url'] = url_rewrite("QS_jobfairshow",array('id'=>$val['id']),1,$val['subsite_id']);
$val['exhibitorsurl'] = url_rewrite($aset['exhibitorspage'],array('id'=>$val['id']));
$val['introduction'] = htmlspecialchars_decode($val['introduction'],ENT_QUOTES);
$time=time();
	if ($val['predetermined_status']=="1" && $val['predetermined_start']>$time) {
		$val['predetermined_ok']=1; // δ��ʼ
	}
	else if ($val['predetermined_status']=="1" && $val['holddate_start']>$time && $time>$val['predetermined_start'] && ($val['predetermined_end']=="0" || $val['predetermined_end']>$time))
	{
		$val['predetermined_ok']=2; // Ԥ����
	}
	else
	{
		$val['predetermined_ok']=0; // �ѽ���
	}
if($val['trade_cn']){
	$trade_cn = explode(",", $val['trade_cn']);
	foreach ($trade_cn as $key => $value) {
		$val['trade_arr'][]['name'] = $value;
	}
}
$val['keywords']=$val['title'];
//$val['bus']=cut_str($val['bus'],18,0,"");
$val['description']=str_replace('&nbsp;','',$val['introduction']);
$val['description']=cut_str(strip_tags($val['description']),60,0,"");
$smarty->assign($aset['listname'],$val);
}
?>