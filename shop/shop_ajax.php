<?php
 /*
 * 74cms ��«���̳���ҳ
*/
define('IN_QISHI', true);
require_once('shop_common.php');
$act =$_GET['act']?trim($_GET['act']):"check";
if($act == 'check')
{
	$id=intval($_GET['id']); //��Ʒid
	$num=$_GET['num']?intval($_GET['num']):1;//�һ���Ŀ
	$uid=intval($_SESSION['uid']);
	// ��ȡ��ҵ��«��
	$uesr_points=get_user_points($uid);
	// ��ȡ��Ʒ��Ϣ
	$shop_one=get_shop_one($id);
	if(empty($shop_one))
	{
		exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td style="padding-bottom:10px">
					��Ʒid��ʧ��
				</td>
		    </tr>
		</table>');
	}
	if($_SESSION['username']=='')
	{
		$captcha=get_cache('captcha');
		$smarty->assign('verify_userlogin',$captcha['verify_userlogin']);
		$smarty->display('plus/ajax_login.htm');
		exit();
	}
	if($_SESSION['utype']!='1' && $_SESSION['utype']!='2')
	{
		exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td style="padding-bottom:10px">
					��Ʒ�һ�������ҵ�͸��˻�Ա���ţ�
				</td>
		    </tr>
		</table>');
	}
	if($uesr_points>$shop_one['shop_points']*$num)
	{
		if($shop_one['shop_stock']>$num)
		{
			if($shop_one['shop_customer']>0)
			{
				$com_num=count_exchange($uid,$id);
				$shop_customer=$shop_one['shop_customer']-$com_num;
				if($shop_customer>=$num)
				{
					$key=substr(md5($id.$_SESSION['username'].$num),8,16);
					exit("".$key.",1");
				}
				else
				{
					exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
					    <tr>
							<td width="20" align="right"></td>
							<td style="padding-bottom:10px">
								����Ʒ����ÿ�˶һ�'.$shop_one['shop_customer'].'��,���Ѿ��һ�'.$com_num.'��
							</td>
					    </tr>
					</table>');
				}
			}
			else
			{
				$key=substr(md5($id.$_SESSION['username'].$num),8,16);
				exit("".$key.",1");
			}
		}
		else
		{
			exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
					    <tr>
							<td width="20" align="right"></td>
							<td style="padding-bottom:10px">
								��治��
							</td>
					    </tr>
					</table>');
		}
	}
	else
	{
		exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
					    <tr>
							<td width="20" align="right"></td>
							<td style="text-align:center;">
								���ĺ�«�Ҳ��㣬������ѡ����Ʒ�һ���<br>
								<a class="prevListIndex" href="shop_list.php">������ҳ</a>
							</td>
					    </tr>
					</table>');
	}
}
elseif($act== "order_show")
{
	$id=intval($_GET['id']);
	$order_show=get_order_one($id);

	if(!empty($order_show))
	{
		if($order_show['state']==0)
		{
			$order_show['state_cn']="<span class=\"coff9125\">�����</span>";
		}
		elseif($order_show['state']==1)
		{
			$order_show['state_cn']="<span class=\"co5dbc47\">���ͨ��</span>";
		}
		else
		{
			$order_show['state_cn']="<span class=\"cofe4848\">���δͨ��</span>";
		}
		exit('<div class="order-d-wrap">
		<div class="order-d-item clearfix">
			<div class="od-type f-left">�������ݣ�</div>
			<div class="od-content f-left">'.$order_show['shop_title'].'  * '.$order_show['shop_num'].'</div>
		</div>
		<div class="order-d-item clearfix">
			<div class="od-type f-left">����״̬��</div>
			<div class="od-content f-left">'.$order_show['state_cn'].'</div>
		</div>
		<div class="order-d-item clearfix">
			<div class="od-type f-left">����ʱ�䣺</div>
			<div class="od-content f-left">'.date('Y-m-d H:i:s',$order_show['addtime']).'</div>
		</div>
		<div class="order-d-item clearfix">
			<div class="od-type f-left">�� ϵ �ˣ�</div>
			<div class="od-content f-left">'.$order_show['contact'].'  '.$order_show['mobile'].'</div>
		</div>
		<div class="order-d-item clearfix">
			<div class="od-type f-left">��ϵ��ַ��</div>
			<div class="od-content f-left">'.$order_show['address'].'</div>
		</div>
	</div>');
	}
}
?>