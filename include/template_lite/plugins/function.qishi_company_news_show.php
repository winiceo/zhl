<?php
function tpl_function_qishi_company_news_show($params, &$smarty)
{
global $db;
$arr=explode(',',$params['set']);
foreach($arr as $str)
{
$a=explode(':',$str);
	switch ($a[0])
	{
	case "����ID":
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
$sql = "select id,title,content,company_id,addtime from ".table('company_news')." WHERE  id=".intval($aset['id'])." LIMIT 1";
$val=$db->getone($sql);
if (empty($val))
{
	header("HTTP/1.1 404 Not Found"); 
	$smarty->display("404.htm");
	exit();
}

$val['keywords']=$val['title'];
$val['description']=cut_str(strip_tags($val['content']),60,0,"");

$prev = $db->getone("select id,title from ".table('company_news')." where id<".$val['id']." and company_id=".$val['company_id']." and audit=1 order by id desc limit 1");
if(!$prev){
	$val['prev'] = 0;
}else{
	$val['prev'] = 1;
	$val['prev_title'] = $prev['title'];
	$val['prev_url'] = url_rewrite("QS_companynewsshow",array('id'=>$prev['id']));
}
$next = $db->getone("select id,title from ".table('company_news')." where id>".$val['id']." and company_id=".$val['company_id']." and audit=1 order by id desc limit 1");
if(!$next){
	$val['next'] = "û����";
}else{
	$val['next'] = 1;
	$val['next_title'] = $next['title'];
	$val['next_url'] = url_rewrite("QS_companynewsshow",array('id'=>$next['id']));
}

$smarty->assign($aset['listname'],$val);
}
?>