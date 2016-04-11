<?php
 /*
 * 74cms ��Ʒ��
*/
define('IN_QISHI',true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_gifts_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
$smarty->assign('act',$act);
if($_CFG['subsite_id']>0){
	adminmsg('��û�й���Ȩ�ޣ�',0);
}
check_permissions($_SESSION['admin_purview'],"gifts");
$smarty->assign('pageheader',"��Ʒ��");
if($act == 'list')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY g.id  DESC";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE g.account like '%{$key}%'";
		$oederbysql="";
	}
	else
	{
		$usettime=isset($_GET['usettime'])?intval($_GET['usettime']):"";
		if ($usettime===0)
		{
		$wheresql=empty($wheresql)?" WHERE g.usettime= 0":$wheresql." AND g.usettime=0 ";
		}
		if ($usettime===1)
		{
		$wheresql=empty($wheresql)?" WHERE g.usettime> 0":$wheresql." AND g.usettime>0 ";
		$oederbysql=" order BY g.usettime  DESC";
		}
		
		$settr=intval($_GET['settr']);
		if ($settr>0)
		{
			$wheresql.=empty($wheresql)?" WHERE ":" AND  ";
			$days=intval($settr);
			$settr=strtotime("-{$days} day");
			$wheresql.=" g.addtime> {$settr} ";	
		}		
		if ($_GET['t_effective']<>"")
		{
		$t_effective=intval($_GET['t_effective']);
		$wheresql=empty($wheresql)?" WHERE t.t_effective= ".$t_effective:$wheresql." AND t.t_effective= ".$t_effective;
		}
		$t_id=isset($_GET['t_id'])?intval($_GET['t_id']):"";
		if ($t_id>0)
		{
		$wheresql=empty($wheresql)?" WHERE t.t_id= ".$t_id:$wheresql." AND t.t_id= ".$t_id;
		}
	}
	$joinsql=" LEFT JOIN  ".table('gifts_type')." AS t ON g.t_id=t.t_id ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('gifts')." AS g ".$joinsql.$wheresql;
	$total=$db->get_total($total_sql);
	$page = new page(array('total'=>$total, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_gifts($offset, $perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('category',get_gifts_category());
	$smarty->assign('list',$list);
	$smarty->assign('total',$total);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('navlabel',"list");
	$smarty->display('gifts/admin_gifts_list.htm');
}
elseif($act == 'generate')
{
	get_token();
	$category=get_gifts_category();
	if(empty($category))
	{
	$link[0]['text'] = "�鿴����";
	$link[0]['href'] = '?act=category';
	adminmsg("��Ʒ�����಻���ڣ�����������Ʒ������������Ʒ������",1,$link);
	}
	$smarty->assign('navlabel',"generate");
	$smarty->assign('category',get_gifts_category());
	$smarty->display('gifts/admin_gifts_generate.htm');
}
elseif($act == 'generate_save')
{
	check_token();
	$number=intval($_POST['number']);
	$pwd_pre=trim($_POST['pwd_pre']);
	$t_id=!empty($_POST['t_id'])?intval($_POST['t_id']):adminmsg('��ѡ����࣡',1);
	$category=get_gifts_category_one($t_id);
	$gifts_pre=$category['t_pre'];
	if ($number==0)
	{
		adminmsg("������������ȷ������д����0��������",0);
	}
	else
	{
		$addtime=time();
		$rand=mt_rand(1000,9999);
		for ($i = 1; $i <= $number; $i++)
		{
			$microtime = explode(" ", microtime());
			$account=$gifts_pre.$rand.str_pad($i,4,"0",STR_PAD_LEFT).mt_rand(1000,9999).mt_rand(1000,9999);
			$password=$pwd_pre.mt_rand(100000,999999);
			$db->query("INSERT INTO ".table('gifts')." ( `t_id` , `account` , `password` , `usettime` , `addtime` ) VALUES ( '{$t_id}', '{$account}', '{$password}', '0', '{$addtime}')");
		}
	$link[0]['text'] = "������Ʒ���б�";
	$link[0]['href'] = '?act=list';
	write_log("������Ʒ���������� {$number} ��", $_SESSION['admin_name'],3);
	adminmsg("���ɳɹ��������� {$number} ��",2,$link);
		
	}
}
elseif($act == 'gifts_act')
{
	check_token();
	if ($_POST['deletetid'])
	{
		$t_id=$_POST['d_tid'];
		if(empty($t_id))
		{
		adminmsg("��û��ѡ�����",1);
		}
		if ($num=del_gifts_tid($t_id))
		{
		write_log("ɾ������,��ɾ��".$num."��", $_SESSION['admin_name'],3);
		adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
		}
		else
		{
		adminmsg("ɾ��ʧ�ܣ�",0);
		}
	}
	if ($_POST['deleteid'])
	{
		$id=$_POST['id'];
		if(empty($id))
		{
		adminmsg("��û��ѡ����Ϣ",1);
		}
		if ($num=del_gifts($id))
		{
		write_log("ɾ����Ʒ��,��ɾ��".$num."��", $_SESSION['admin_name'],3);
		adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
		}
		else
		{
		adminmsg("ɾ��ʧ�ܣ�",0);
		}
	}
	if ($_POST['downtid'] || $_POST['downid'])
	{
		$id=$_POST['id'];	
		if (!empty($_POST['downid']))
		{
			if(empty($id))
			{
			adminmsg("��û��ѡ����Ϣ",1);
			}
			if(!is_array($id)) $id=array($id);
			$sqlin=implode(",",$id);
			if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
			{
				$sql="SELECT * FROM ".table('gifts')." AS a LEFT JOIN ".table('gifts_type')." AS t ON a.t_id=t.t_id WHERE a.id IN ({$sqlin})";
			}
		}
		elseif (!empty($_POST['downtid']))
		{
			$t_id=intval($_POST['t_id']);
			if(empty($t_id))
			{
			adminmsg("��û��ѡ�����",1);
			}
			else
			{
				$sql="SELECT * FROM ".table('gifts')." AS a LEFT JOIN ".table('gifts_type')." AS t ON a.t_id=t.t_id WHERE a.t_id='{$t_id}'";
			}
		}
		if (!empty($sql))
		{
				$result=$db->query($sql);
				while($row = $db->fetch_array($result))
				{
				$xls.=$row['t_name'].chr(9);
				$row['t_effective']=$row['t_effective']=="1"?"��Ч":"��Ч";
				$xls.=$row['t_effective'].chr(9);
				$xls.=$row['account'].chr(9);
				$xls.=$row['password'].chr(9);
				$xls.=$row['t_amount'].chr(9);
					if ($row['t_starttime']=="0" && $row['t_endtime']=="0")
					{
					$effectivedate="����";
					}
					else
					{
						$row['t_starttime']=$row['t_starttime']=="0"?"����":date("Y/m/d",$row['t_starttime']);
						$row['t_endtime']=$row['t_endtime']=="0"?"����":date("Y/m/d",$row['t_endtime']);
						$effectivedate=$row['t_starttime']."-".$row['t_endtime'];
					}
				$xls.=$effectivedate.chr(9);
				$row['usettime']=$row['usettime']=="0"?"δʹ��":"��ʹ�ã�ʹ��ʱ��".date("Y/m/d",$row['usettime']);
				$xls.=$row['usettime'].chr(9);
				$xls.=$row['t_repeat'].chr(9);
				$xls.=chr(13);
				}
				header("Content-type:application/vnd.ms-excel");
				header("Content-Disposition:attachment;filename=".date("Y-m-d-").uniqid().".xls");
				$xlstop='����'.chr(9);
				$xlstop.='����״̬'.chr(9);
				$xlstop.='�ʺ�'.chr(9);
				$xlstop.='����'.chr(9);
				$xlstop.='��«��'.chr(9);
				$xlstop.='��Ч��'.chr(9);
				$xlstop.='ʹ��״̬'.chr(9);
				$xlstop.='���Ӵ���'.chr(9);
				$xlstop.=chr(13);
				exit($xlstop.$xls);
		}		
	}
}
elseif($act == 'category')
{
	get_token();
	$smarty->assign('category',get_gifts_category());
	$smarty->assign('navlabel',"category");
	$smarty->display('gifts/admin_gifts_category.htm');
}
elseif($act == 'category_add')
{
	get_token();
	$smarty->assign('navlabel',"category");
	$smarty->display('gifts/admin_gifts_category_add.htm');
}
elseif($act == 'add_category_save')
{
	check_token();
	$setsqlarr['t_name']=!empty($_POST['t_name'])?trim($_POST['t_name']):adminmsg('����д�������ƣ�',1);
	$setsqlarr['t_starttime']=trim($_POST['t_starttime']);
	if ($setsqlarr['t_starttime']<>"0")
	{
		if (!preg_match("/^[0-9]{4}(\\-)[0-9]{1,2}(\\1)[0-9]{1,2}(|\s+[0-9]{1,2}(|:[0-9]{1,2}(|:[0-9]{1,2})))$/",$setsqlarr['t_starttime']))
		{
		adminmsg("��ʼʱ���ʽ����",0);
		}
		else
		{
		$setsqlarr['t_starttime']=intval(convert_datefm($_POST['t_starttime'],2));
		}
	}
	$setsqlarr['t_endtime']=trim($_POST['t_endtime']);
	if ($setsqlarr['t_endtime']<>"0")
	{
		if (!preg_match("/^[0-9]{4}(\\-)[0-9]{1,2}(\\1)[0-9]{1,2}(|\s+[0-9]{1,2}(|:[0-9]{1,2}(|:[0-9]{1,2})))$/",$setsqlarr['t_endtime']))
		{
		adminmsg("����ʱ���ʽ����",0);
		}
		else
		{
		$setsqlarr['t_endtime']=intval(convert_datefm($_POST['t_endtime'],2));
		}
	}
	$setsqlarr['t_repeat']=intval($_POST['t_repeat']);
	$setsqlarr['t_effective']=intval($_POST['t_effective']);
	$setsqlarr['t_amount']=intval($_POST['t_amount'])>0?intval($_POST['t_amount']):adminmsg('����ȷ��д��«�ң�',1);
	$setsqlarr['t_pre']=!empty($_POST['t_pre'])?trim($_POST['t_pre']):adminmsg('����д����ǰ׺��',1);
	$info=$db->getone("select * from ".table('gifts_type')." where t_pre='{$setsqlarr['t_pre']}' LIMIT 1");
	if (!empty($info))
	{
	adminmsg("����ǰ׺ {$setsqlarr['t_pre']} �Ѿ����ڣ�",1);
	}
	!$db->inserttable(table('gifts_type'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):"";
	$link[0]['text'] = "���ط������";
	$link[0]['href'] = '?act=category';
	$link[1]['text'] = "�������";
	$link[1]['href'] = "?act=category_add";
	adminmsg("��ӳɹ���",2,$link);
}
elseif($act == 'edit_category')
{
	get_token();
	$id=intval($_GET['id']);
	$category=get_gifts_category_one($id);
	if ($category['t_starttime']<>"0")
	{
	$category['t_starttime']=trim(convert_datefm($category['t_starttime'],1));
	}
	if ($category['t_endtime']<>"0")
	{
	$category['t_endtime']=trim(convert_datefm($category['t_endtime'],1));
	}
	$smarty->assign('category',$category);
	$smarty->assign('navlabel',"category");
	$smarty->display('gifts/admin_gifts_category_edit.htm');
}
elseif($act == 'edit_category_save')
{
	check_token();
	$id=intval($_POST['id']);
	$setsqlarr['t_name']=!empty($_POST['t_name'])?trim($_POST['t_name']):adminmsg('����д�������ƣ�',1);
	$setsqlarr['t_starttime']=trim($_POST['t_starttime']);
	if ($setsqlarr['t_starttime']<>"0")
	{
		if (!preg_match("/^[0-9]{4}(\\-)[0-9]{1,2}(\\1)[0-9]{1,2}(|\s+[0-9]{1,2}(|:[0-9]{1,2}(|:[0-9]{1,2})))$/",$setsqlarr['t_starttime']))
		{
		adminmsg("��ʼʱ���ʽ����",0);
		}
		else
		{
		$setsqlarr['t_starttime']=intval(convert_datefm($_POST['t_starttime'],2));
		}
	}
	$setsqlarr['t_endtime']=trim($_POST['t_endtime']);
	if ($setsqlarr['t_endtime']<>"0")
	{
		if (!preg_match("/^[0-9]{4}(\\-)[0-9]{1,2}(\\1)[0-9]{1,2}(|\s+[0-9]{1,2}(|:[0-9]{1,2}(|:[0-9]{1,2})))$/",$setsqlarr['t_endtime']))
		{
		adminmsg("����ʱ���ʽ����",0);
		}
		else
		{
		$setsqlarr['t_endtime']=intval(convert_datefm($_POST['t_endtime'],2));
		}
	}
	$setsqlarr['t_repeat']=intval($_POST['t_repeat']);
	$setsqlarr['t_effective']=intval($_POST['t_effective']);
	$setsqlarr['t_amount']=intval($_POST['t_amount'])>0?intval($_POST['t_amount']):adminmsg('����ȷ��д��«�ң�',1);
	$setsqlarr['t_pre']=!empty($_POST['t_pre'])?trim($_POST['t_pre']):adminmsg('����д����ǰ׺��',1);
	$info=$db->getone("select * from ".table('gifts_type')." where t_pre='{$setsqlarr['t_pre']}' LIMIT 1");
	if (!empty($info) && $info['t_id']<>$id)
	{
	adminmsg("����ǰ׺ {$setsqlarr['t_pre']} �Ѿ����ڣ�",1);
	}
	$link[0]['text'] = "�鿴�޸Ľ��";
	$link[0]['href'] = '?act=edit_category&id='.$id;
	$link[1]['text'] = "���ط������";
	$link[1]['href'] = '?act=category';
	!$db->updatetable(table('gifts_type'),$setsqlarr," t_id=".$id."")?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
}
elseif($act == 'del_category')
{
	check_token();
	$id=$_REQUEST['id'];
	$num=del_gifts_category($id);
	if ($num==-1)
	{
	adminmsg("ɾ��ʧ�ܣ���ѡ�����д�����Ʒ��������ɾ�����ɵ���Ʒ��",1);
	}
	if ($num>0)
	{
	write_log("ɾ����Ʒ������,��ɾ��".$num."��", $_SESSION['admin_name'],3);
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'use')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY g.usetime  DESC";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE g.account='{$key}'";
		if     ($key_type===2)$wheresql=" WHERE m.username='{$key}'";
		if     ($key_type===3)$wheresql=" WHERE g.uid='{$key}'";
		if     ($key_type===4)$wheresql=" WHERE m.email='{$key}'";
		if     ($key_type===5)$wheresql=" WHERE m.mobile='{$key}'";
		$oederbysql="";
	}
	else
	{

		$settr=intval($_GET['settr']);
		if ($settr>0)
		{
			$wheresql.=empty($wheresql)?" WHERE ":" AND  ";
			$days=intval($settr);
			$settr=strtotime("-{$days} day");
			$wheresql.=" g.usetime> {$settr} ";	
		}
		$t_id=isset($_GET['t_id'])?intval($_GET['t_id']):"";
		if ($t_id>0)
		{
		$wheresql=empty($wheresql)?" WHERE g.giftstid= ".$t_id:$wheresql." AND g.giftstid= ".$t_id;
		}
	}
	$joinsql=" LEFT JOIN  ".table('members')." AS m ON g.uid=m.uid ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_gifts')." AS g ".$joinsql.$wheresql;
	$total=$db->get_total($total_sql);
	$page = new page(array('total'=>$total, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_members_gifts($offset, $perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('category',get_gifts_category());
	$smarty->assign('list',$list);
	$smarty->assign('total',$total);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('navlabel',"use");
	$smarty->display('gifts/admin_members_gifts_list.htm');
}
?>