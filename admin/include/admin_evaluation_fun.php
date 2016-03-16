<?php
 /*
 * 74cms �������� ���÷��� ���ݵ��ú���
*/
if(!defined('IN_QISHI'))
{
	die('Access Denied!');
}
// ��ȡ��������
function get_evaluation_category()
{
	global $db;
	$sql = "select * from ".table('evaluation_type')."  order BY id  asc";
	return   $db->getall($sql);
}
// ��ȡ��һ��������
function get_evaluation_category_one($id)
{
	global $db;
	$id=intval($id);
	$sql = "select * from ".table('evaluation_type')."  where id=$id limit 1 ";
	return   $db->getone($sql);
}
// ��ȡ�����Ծ�
function get_evaluation_paper($offset, $perpage, $sql= '')
{
	global $db,$_CFG;
	$limit=" LIMIT ".$offset.','.$perpage;
	$rows = $db->getall("SELECT * FROM ".table('evaluation_paper').$sql.$limit);
	foreach ($rows as $key => $value) 
	{
		$value['url']=url_rewrite('QS_paper_answer',array('id'=>$value['id']));
		$list[$key] = $value;
	}
	return $list;
}
// ��ȡ��һ�Ծ���Ϣ
function get_evaluation_one($id)
{
	global $db,$_CFG;
	$id=intval($id);
	return $db->getone("SELECT * FROM ".table('evaluation_paper')." where id=$id limit 1 ");
}
// ɾ���Ծ�
function del_paper($id)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		//ѭ���Ծ� �����Ծ���Ӧ���͵��Ծ���
		$sql = "SELECT *  FROM ".table('evaluation_paper')." WHERE id IN ({$sqlin})";
		$paper_info = $db->getall($sql);
		foreach ($paper_info as $key => $value) 
		{
			//�����Ծ���  1=>��    2=>��
			set_paper_num('2',$value['type_id'],1);
		}
		if (!$db->query("Delete from ".table('evaluation_paper')." WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		return $return;
	}
	else
	{
	return false;
	}
}
// ��ȡ�Ծ������б�
function get_paper_question($offset, $perpage, $sql= '')
{
	global $db,$_CFG;
	$limit=" LIMIT ".$offset.','.$perpage;
	$rows = $db->getall("SELECT * FROM ".table('evaluation_question').$sql.$limit);
	return $rows;
}
// ��ȡ�Ծ�һ����
function get_question_one($id)
{
	global $db,$_CFG;
	$id=intval($id);
	return $db->getone("SELECT * FROM ".table('evaluation_question')." where id=$id limit 1 ");
}
// �����Ծ�������
function set_paper_question_num($type, $paper_id, $num)
{
	global $db,$_CFG;
	$paper_id=intval($paper_id);
	if(empty($paper_id))
	{
		return false;
	}
	$num=intval($num);
	$paper_info = get_evaluation_one($paper_id);
	if(intval($type) == 1)
	{
		$num = $num + intval($paper_info['question_num']);
	}
	else
	{
		$num = intval($paper_info['question_num']) - $num;
	}
	$paperarr['question_num'] = $num;
	if(!$db->updatetable(table("evaluation_paper"),$paperarr,array("id"=>$paper_id)))
	{
		return false;
	}
	return true;
}
// ɾ������
function del_question($id)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('evaluation_question')." WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		return $return;
	}
	else
	{
	return false;
	}
}
// ��ȡ����ѡ���б�
function get_question_option($offset, $perpage, $sql= '')
{
	global $db,$_CFG;
	$limit=" LIMIT ".$offset.','.$perpage;
	$rows = $db->getall("SELECT * FROM ".table('evaluation_option').$sql.$limit);
	return $rows;
}
// ��ȡ�Ծ�һ����
function get_option_one($id)
{
	global $db,$_CFG;
	$id=intval($id);
	return $db->getone("SELECT * FROM ".table('evaluation_option')." where id=$id limit 1 ");
}
// ɾ��ѡ��
function del_option($id)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('evaluation_option')." WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		return $return;
	}
	else
	{
	return false;
	}
}
// �����Ծ���
function set_paper_num($type, $type_id, $num)
{
	global $db,$_CFG;
	$type_id=intval($type_id);
	if(empty($type_id))
	{
		return false;
	}
	$num=intval($num);
	$sql = "select * from ".table('evaluation_type')." WHERE id=".$type_id." LIMIT 1 ";
	$eval_type = $db->getone($sql);
	if(intval($type) == 1)
	{
		$num = $num + intval($eval_type['num']);
	}
	else
	{
		$num = intval($eval_type['num']) - $num;
	}
	$paperarr['num'] = $num;
	if(!$db->updatetable(table("evaluation_type"),$paperarr,array("id"=>$type_id)))
	{
		return false;
	}
	return true;
}
// ɾ��������¼
function del_record($id)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('evaluation_record')." WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		return $return;
	}
	else
	{
	return false;
	}
}

// ��ȡ�Ծ������б�
function get_paper_result($offset, $perpage, $sql= '')
{
	global $db,$_CFG;
	$limit=" LIMIT ".$offset.','.$perpage;
	$rows = $db->getall("SELECT * FROM ".table('evaluation_result').$sql.$limit);
	return $rows;
}
// ��ȡ��
function get_result_one($id)
{
	global $db,$_CFG;
	$id=intval($id);
	return $db->getone("SELECT * FROM ".table('evaluation_result')." where id=$id limit 1 ");
}
// ɾ����
function del_result($id)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('evaluation_result')." WHERE id IN ({$sqlin})")) return false;
		$return=$return+$db->affected_rows();
		return $return;
	}
	else
	{
		return false;
	}
}
?>