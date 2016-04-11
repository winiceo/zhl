<?php
 /*
 * 74cms ��«���̳���ҳ
*/
define('IN_QISHI', true);
require_once('shop_common.php');
$mypage['tpl']="../tpl_shop/default/";
$act =$_GET['act']?trim($_GET['act']):"list";
if($act== 'list')
{
	$wheresql=" where uid=".intval($_SESSION['uid'])." ";
	$state=intval($_GET['state']);
	if($state>0)
	{
		$state=$state-1;
		$wheresql.=" AND state=$state ";
	}
	$settr=intval($_GET['settr']);
	if ($settr>0)
	{
		$days=intval($settr);
		$settr=strtotime("-{$days} day");
		$wheresql.=" AND addtime> {$settr} ";	
	}
	$key=trim($_GET['key']);
	if(!empty($key))
	{
		$wheresql.=" AND shop_title like '%{$key}%'";
	}
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('shop_order').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val,'getarray'=>$_GET));
	$perpage=10;
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('list',get_order($offset,$perpage,$wheresql));
	if($total_val>$perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	
	$smarty->display($mypage['tpl'].'shop_order_list.htm');
}
elseif($act== 'order_add')
{
	$key=trim($_GET["key"]);
	$id=intval($_GET['id']);
	$num=intval($_GET['num']);//�һ���Ŀ
	$keystr=substr(md5($id.$_SESSION['username'].$num),8,16);
	if($key!=$keystr)
	{
		showmsg('�Ƿ����ӣ�',1);
	}
	$shop_show=get_shop_one($id);
	$smarty->assign("show",$shop_show);
	$need_points=$num*$shop_show['shop_points'];   //�����«��
	$smarty->assign("num",$num);
	$smarty->assign("need_points",$need_points);
	if($_SESSION['utype']=='1')
	{
		$smarty->assign("com_profile",get_company($_SESSION['uid']));
	}
	elseif($_SESSION['utype']=='2')
	{
		$smarty->assign("per_profile",get_personal($_SESSION['uid']));
	}
	$smarty->display($mypage['tpl'].'shop_order_add.htm');
}
elseif($act== 'order_save')
{
	$setarr['shop_id']=$_POST['shop_id']?intval($_POST['shop_id']):showmsg("ID��ʧ",1);
	$setarr['shop_num']=$_POST['num']?intval($_POST['num']):showmsg("�һ�������ʧ",1);
	$shop_one=get_shop_one($setarr['shop_id']);
	$setarr['shop_title']=$shop_one['shop_title'];
	$setarr['shop_points']=$shop_one['shop_points'];
	$setarr['order_points']=$shop_one['shop_points']*$setarr['shop_num'];
	$setarr['contact']=$_POST['contact']?trim($_POST['contact']):showmsg("��������ϵ�ˣ�",1);
	$setarr['mobile']=$_POST['mobile']?trim($_POST['mobile']):showmsg("��������ϵ�绰��",1);
	$setarr['address']=$_POST['address']?trim($_POST['address']):showmsg("�������ַ��",1);
	$setarr['addtime']=time();
	$setarr['uid']=intval($_SESSION['uid']);
	if($_SESSION['utype']=='1')
	{
		$com_info=get_company($_SESSION['uid']);
		$setarr['company_name']=$com_info['companyname'];
	}
	elseif($_SESSION['utype']=='2')
	{
		$per_info=get_personal($_SESSION['uid']);
		$setarr['company_name']=$per_info['realname'];
	}
	$uesr_points=get_user_points($_SESSION['uid']);
	if($uesr_points>$setarr['order_points'])
	{
		if($shop_one['shop_stock']>$setarr['shop_num'])
		{
			if($shop_one['shop_customer']>0)
			{
				$com_num=count_exchange($uid,$id);
				$shop_customer=$shop_one['shop_stock']-$com_num;
				if($shop_customer>$setarr['shop_num'])
				{
					$link[0]['text'] = "�鿴����";
					$link[0]['href'] = "?act=list";
					$order_id=$db->inserttable(table("shop_order"),$setarr,1);
					if($order_id)
					{
						//�۳���ҵ��«��
						report_deal($setarr['uid'],2,$setarr['order_points']);
						//д����־
						$user_points=get_user_points($_SESSION['uid']);
						write_memberslog($_SESSION['uid'],$_SESSION['utype'],9001,$_SESSION['username'],$_SESSION['username']."��«�Ҷһ���Ʒ��<strong>{$setarr['shop_title']}</strong>������Ϊ��({$setarr['shop_num']})�����۳� {$setarr['order_points']} ��«�ҡ�",$_SESSION['utype'],2007,"�һ���Ʒ","-{$setarr['order_points']}","{$user_points}");
						// д�� �һ���¼
						write_exchange($order_id,$setarr['shop_id'],$setarr['shop_title'],$setarr['order_points'],$setarr['shop_num'],$setarr['uid'],$setarr['company_name'],$setarr['addtime']);
						showmsg("�����ɹ���",2,$link);
					}
					else
					{
						showmsg("����ʧ�ܣ�",1);
					}
				}
				else
				{
					showmsg('����Ʒ����ÿ�˶һ�'.$shop_one['shop_customer'].'��,���Ѿ��һ�'.$com_num.'��',1);
				}
			}
			else
			{
				$link[0]['text'] = "�鿴����";
				$link[0]['href'] = "?act=list";
				$order_id=$db->inserttable(table("shop_order"),$setarr,1);
				if($order_id)
				{
					//д��һ���¼
					write_exchange($order_id,$setarr['shop_id'],$setarr['shop_title'],$setarr['order_points'],$setarr['uid'],$setarr['company_name'],$setarr['addtime']);
					//�۳���ҵ��«��
					report_deal($setarr['uid'],2,$setarr['order_points']);
					//д����־
					$user_points=get_user_points($_SESSION['uid']);
					write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],$_SESSION['username']."��«�Ҷһ���Ʒ��<strong>{$setarr['shop_title']}</strong>������Ϊ��({$setarr['shop_num']})�����۳� {$setarr['order_points']} ��«�ҡ�",1,2007,"�һ���Ʒ","-{$setarr['order_points']}","{$user_points}");
					showmsg("�����ɹ���",2,$link);
				}
				else
				{
					showmsg("����ʧ�ܣ�",1);
				}
			}
		}
		else
		{
			showmsg("��治����",1);
		}
	}
	else
	{
		showmsg("���ĺ�«�Ҳ�����",1);
	}
}
unset($smarty);
?>