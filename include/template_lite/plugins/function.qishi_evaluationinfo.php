<?php
function tpl_function_qishi_evaluationinfo($params, &$smarty)
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
	case "����ID":
		$aset['id'] = $a[1];
		break;		
	}
}
if (is_array($aset))$aset=array_map("get_smarty_request",$aset);
$aset['listname']=$aset['listname']?$aset['listname']:"list";
$aset['id']=$aset['id']?$aset['id']:1;
//����������Ϣ
$sql = "select * from ".table('evaluation_type')." where id = ".intval($aset['id'])." LIMIT  1";
$info=$db->getone($sql);
//�ò������͵��Ծ���Ϣ
$paper_sql = "select * from ".table('evaluation_paper')." where type_id = ".intval($info['id'])." ";
$paper_info=$db->getall($paper_sql);
$info['paper'] = $paper_info;
$smarty->assign($aset['listname'],$info);
}
?>