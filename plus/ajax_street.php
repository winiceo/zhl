<?php
 /*
 * 74cms ajax ��·����
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : '';
$listtype=trim($_GET['listtype']);
if($act == 'alphabet')
{
	$alphabet=trim($_GET['x']);	
	if (!empty($alphabet))
	{
	$result = $db->query("select * from ".table('category')." where c_alias='QS_street' AND c_index='{$alphabet}' ");
	while($row = $db->fetch_array($result))
	{
		if ($listtype=="li")
		{
		$htm.="<div class=\"fl-content-li\" type=\"streetid\" code=\"{$row['c_id']}\">{$row['c_name']}</div>";
		}
		else
		{
		$_GET['streetid']=$row['c_id'];
		$url=url_rewrite('QS_street',$_GET);
		$htm.="<div class=\"fl-content-li\" type=\"streetid\" code=\"{$row['c_id']}\">{$row['c_name']}</div>";
		}
	}
	if (empty($htm))
	{
	$htm="<div class=\"fl-content-li-nostreet\">û���ҵ�����ĸΪ��<span class=\"le\">{$alphabet}</span> �ĵ�·��</div>";
	}
	exit($htm);
	}
}
elseif($act == 'key')
{
	$key=trim($_GET['key']);
	if (!empty($key))
	{
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0) $key=utf8_to_gbk($key);
	$result = $db->query("select * from ".table('category')." where c_alias='QS_street' AND c_name LIKE '%{$key}%' ");
	while($row = $db->fetch_array($result))
	{
		if ($listtype=="li")
		{
		$htm.="<div class=\"fl-content-li\" type=\"streetid\" code=\"{$row['c_id']}\">{$row['c_name']}</div>";
		}
		else
		{
		$_GET['streetid']=$row['c_id'];
		$url=url_rewrite('QS_street',$_GET);
		$htm.="<div class=\"fl-content-li\" type=\"streetid\" code=\"{$row['c_id']}\">{$row['c_name']}</div>";
		};
	}
	if (empty($htm))
	{
	$htm="<div class=\"fl-content-li-nostreet\">û���ҵ��ؼ���Ϊ��<span class=\"le\">{$key}</span> ��ص�·��</div>";
	}
	exit($htm);
	}
}
?>