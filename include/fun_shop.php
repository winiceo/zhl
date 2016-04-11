<?php
 /*
 * 74cms �������� ���÷��� ���ݵ��ú���
*/
if(!defined('IN_QISHI'))
{
	die('Access Denied!');
}
function get_shop_category($pid='0')
{
	global $db;
	$pid=intval($pid);
	$sql = "select * from ".table('shop_category')." where parentid={$pid}  order BY category_order desc,id asc";
	return   $db->getall($sql);
}
function get_shop_category_one($id)
{
	global $db;
	$sql = "select * from ".table('shop_category')." WHERE id=".intval($id)." LIMIT 1";
	return $db->getone($sql);
}
function del_shop_category($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('shop_category')." WHERE id IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
		if (!$db->query("Delete from ".table('shop_category')." WHERE parentid IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
// ��ȡ��Ʒ
function get_shop($offset, $perpage, $sql= '')
{
	global $db,$_CFG;
	$limit=" LIMIT ".$offset.','.$perpage;
	$rows = $db->getall("SELECT * FROM ".table('shop_goods').$sql.$limit);
	return $rows;
}
function get_shop_one($id)
{
	global $db,$_CFG;
	$id=intval($id);
	return $db->getone("SELECT * FROM ".table('shop_goods')." where id=$id limit 1 ");
}
function del_shop($id)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('shop_goods')." WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		return $return;
	}
	else
	{
	return false;
	}
}
//��ȡ��ҵ����
function get_company($uid)
{
	global $db;
	$sql = "select * from ".table('company_profile')." where uid=".intval($uid)." LIMIT 1 ";
	$result = $db->getone($sql);
	return $result;
}
//��ȡ��������
function get_personal($uid)
{
	global $db;
	$sql = "select * from ".table('members_info')." where uid=".intval($uid)." LIMIT 1 ";
	$result = $db->getone($sql);
	return $result;
}
// ��ȡ�û��ĺ�«��
function get_user_points($uid)
{
	global $db;
	$uid=intval($uid);
	$points=$db->getone("select points from ".table('members_points')." where uid ='{$uid}' LIMIT 1");
	return $points['points'];
}
// �����û��ĺ�«��
function report_deal($uid,$i_type=1,$points=0)
{
	global $db,$timestamp;
	$points=intval($points);
	$uid=intval($uid);
	$points_val=get_user_points($uid);
	if ($i_type==1)
	{
	$points_val=$points_val+$points;
	}
	if ($i_type==2)
	{
	$points_val=$points_val-$points;
	$points_val=$points_val<0?0:$points_val;
	}
	$sql = "UPDATE ".table('members_points')." SET points= '{$points_val}' WHERE uid='{$uid}' LIMIT 1";
	if (!$db->query($sql))return false;
	return true;
}
// ��ȡĳ����ҵ �һ�ĳ����Ʒ��¼��
function count_exchange($uid,$id)
{
	global $db;
	$uid=intval($uid);
	$id=intval($id);
	$sql="select count(*) num from ".table("shop_exchange")." where company_uid=$uid and shop_id=$id and state!=2 ";
	$row=$db->getone($sql);
	return $row['num'];
}
// ��ȡ�Ķ���
function get_order($offset, $perpage, $sql= '')
{
	global $db,$_CFG;
	$limit=" LIMIT ".$offset.','.$perpage;
	$rows = $db->getall("SELECT * FROM ".table('shop_order').$sql.$limit);
	return $rows;
}
function get_order_one($id)
{
	global $db,$_CFG;
	$id=intval($id);
	return $db->getone("SELECT * FROM ".table('shop_order')." where id=$id limit 1");
}
/*��ȡ���� �һ���¼*/
function get_exchange_index($num)
{
	global $db;
	$num=intval($num);
	$id=intval($id);
	$sql="select * from ".table('shop_exchange')." where state=1 order by addtime desc limit $num ";
	$row=$db->getall($sql);
	foreach ($row as $key => $value)
	{
		$value['addtime_cn']=daterange(time(),$value['addtime'],'Y-m-d',"#FF3300");
		$company_profile=$db->getone("select id from ".table("company_profile")." where uid=$value[company_uid]");
		$value['company_id']=$company_profile['id'];
		$row[$key]=$value;

	}
	return $row;
}
/*��ȡ���� �һ���¼*/
function get_exchange($num,$id)
{
	global $db;
	$num=intval($num);
	$id=intval($id);
	$sql="select * from ".table('shop_exchange')." where state=1 and shop_id={$id} order by addtime desc limit $num ";
	$row=$db->getall($sql);
	foreach ($row as $key => $value)
	{
		$value['addtime_cn']=daterange(time(),$value['addtime'],'Y-m-d',"#FF3300");
		$row[$key]=$value;
	}
	return $row;
}
/*д��һ���¼ */
function write_exchange($order_id,$shop_id,$shop_title,$points,$num,$company_uid,$company_name,$time,$state=0)
{
	global $db;
	$setarr["order_id"]=intval($order_id);
	$setarr["shop_id"]=intval($shop_id);
	$setarr["shop_title"]=trim($shop_title);
	$setarr["points"]=intval($points);
	$setarr["num"]=intval($num);
	$setarr["company_uid"]=intval($company_uid);
	$setarr["company_name"]=trim($company_name);
	$setarr["addtime"]=$time;
	$setarr["state"]=0;
	$db->inserttable(table('shop_exchange'),$setarr);
}
// ��ȡ���Źؼ���
function get_shop_hotword($num)
{
	global $db;
	$num=intval($num);
	$sql="select * from ".table('shop_hotword')." order by w_hot desc,id desc limit $num ";
	return $db->getall($sql);
}
?>