<?php
 /*
 * 74cms �������� �������
*/
 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
//��ȡ����б�
function get_plug($type="")
{
	global $db;
	if (!empty($type)) $wheresql="  WHERE p_install=".intval($type)."  ";
	$sql = "select * from ".table('plug')." ".$wheresql;
	$list=$db->getall($sql);
	return $list;
}
//ж�ز��
function uninstall_plug($id)
{
global $db;
if (!intval($id)) return false;
$sql= "UPDATE ".table('plug')." SET p_install='1' WHERE id='$id'";
if (!$db->query($sql))return false;
return true;
}
//��װ���
function install_plug($id)
{
global $db;
if (!intval($id)) return false;
$sql= "UPDATE ".table('plug')." SET p_install='2' WHERE id='$id'";
if (!$db->query($sql))return false;
return true;
}
?>