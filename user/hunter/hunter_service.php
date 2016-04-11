<?php
/*
 * 74cms 猎头会员中心
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/hunter_common.php');
$smarty->assign('leftmenu',"service");
if ($act=='account')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$i_type=trim($_GET['i_type']);
	$settr=intval($_GET['settr']);
	if($_CFG['operation_hunter_mode']=="1"){
		$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9201 ";
	}elseif($_CFG['operation_hunter_mode']=="2"){
		$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9202 ";
	}
	
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND log_addtime>".$settr_val;
	}
	$perpage=15;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_log').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('report',get_user_report($offset, $perpage,$wheresql));
	$smarty->assign('page',$page->show(3));
	
	
	$setmeal = get_user_setmeal($_SESSION['uid']);
	if ($setmeal['endtime']>0){
		$setmeal_endtime=sub_day($setmeal['endtime'],time());
	}else{
		$setmeal_endtime="无限期";
	}
	
	$smarty->assign('title','我的账户 - 猎头会员中心 - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->assign('setmeal',$setmeal);
	$smarty->assign('points_rule',get_points_rule());
	$smarty->assign('setmeal_rule',get_setmeal_one($setmeal['setmeal_id']));
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	if($_CFG['operation_hunter_mode']=="1"){
		$smarty->display('member_hunter/hunter_my_account.htm');
	}elseif($_CFG['operation_hunter_mode']=="2"){
		$smarty->display('member_hunter/hunter_my_account_package.htm');
	}
	
}
elseif ($act=='order_add')
{
	$smarty->assign('title','在线充值 - 猎头会员中心 - '.$_CFG['site_name']);
	$smarty->assign('payment',get_payment());
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->display('member_hunter/hunter_order_add.htm');
}
elseif ($act=='order_add_save')
{
		if (empty($hunter_profile['huntername']))
		{
		$link[0]['text'] = "填写猎头资料";
		$link[0]['href'] = 'hunter_info.php?act=hunter_profile';
		showmsg("请先填写您的猎头资料！",1,$link);
		}
	$myorder=get_user_order($_SESSION['uid'],1);
	$order_num=count($myorder);
	if ($order_num>=5)
	{
	$link[0]['text'] = "立即查看";
	$link[0]['href'] = '?act=order_list&is_paid=1';
	showmsg("未处理的订单不能超过 5 条，请先处理后再次申请！",1,$link,true,8);
	}
	$amount=(trim($_POST['amount'])).(intval($_POST['amount']))?trim($_POST['amount']):showmsg('请填写充值金额！',1);
	($amount<$_CFG['hunter_payment_min'])?showmsg("单笔充值金额不能少于 ".$_CFG['hunter_payment_min']." 元！",1):'';
	$payment_name=empty($_POST['payment_name'])?showmsg("请选择付款方式！",1):$_POST['payment_name'];
	$paymenttpye=get_payment_info($payment_name);
	if (empty($paymenttpye)) showmsg("支付方式错误！",0);
	$fee=number_format(($amount/100)*$paymenttpye['fee'],1,'.','');//手续费
	$order['oid']= strtoupper(substr($paymenttpye['typename'],0,1))."-".date('ymd',time())."-".date('His',time());//订单号
	$order['v_url']=$_CFG['site_domain'].$_CFG['site_dir']."include/payment/respond_".$paymenttpye['typename'].".php";
	$order['v_amount']=$amount+$fee; 
	$points=$amount*$_CFG['hunter_payment_rate'];
	$order_id=add_order($_SESSION['uid'],$order['oid'],$amount,$payment_name,"充值葫芦币:".$points,$timestamp,$points,'',3,4);
		if ($order_id)
			{
			header("location:?act=payment&order_id=".$order_id);
			}
			else
			{
			showmsg("添加订单失败！",0);
			}
}
elseif ($act=='payment')
{
	$setmeal = get_user_setmeal($_SESSION['uid']);
	if ($setmeal['endtime']>0){
		$setmeal_endtime=sub_day($setmeal['endtime'],time());
	}else{
		$setmeal_endtime="无限期";
	}
	$smarty->assign('user_setmeal',$setmeal);
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	$smarty->assign('payment',get_payment());
	$order_id=intval($_GET['order_id']);
	$myorder=get_order_one($_SESSION['uid'],$order_id);
	$payment=get_payment_info($myorder['payment_name']);
	if (empty($payment)) showmsg("支付方式错误！",0);
	$fee=number_format(($amount/100)*$payment['fee'],1,'.','');//手续费
	$order['oid']=$myorder['oid'];//订单号
	$order['v_url']=$_CFG['site_domain'].$_CFG['site_dir']."include/payment/respond_".$payment['typename'].".php";
	$order['v_amount']=$myorder['amount']+$fee;
	if ($myorder['payment_name']!='remittance' && $myorder['payment_name']!='weixinpay')//假如是非线下支付，
	{
		require_once(QISHI_ROOT_PATH."include/payment/".$payment['typename'].".php");
		$payment_form=get_code($order,$payment);
		if (empty($payment_form)) showmsg("在线支付参数错误！",0);
	}	//微信支付  生成二维码图片
	elseif($myorder['payment_name']=='weixinpay')
	{
		require_once(QISHI_ROOT_PATH.'include/payment/native.php');
		$smarty->assign('w_url',urlencode($url2));
		fopen(QISHI_ROOT_PATH.'data/wxpay/'.$myorder['oid'].'.tmp', "w") or die("无法打开缓存文件!");
		$_SESSION['wxpay_no'] = $myorder['oid'];
	}
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->assign('title','付款 - 猎头会员中心 - '.$_CFG['site_name']);
	$smarty->assign('fee',$fee);
	$smarty->assign('amount',$myorder['amount']);
	$smarty->assign('oid',$order['oid']);
	$smarty->assign('byname',$payment);
	$smarty->assign('payment_form',$payment_form);
	$smarty->display('member_hunter/hunter_order_pay.htm');
}
elseif ($act=='order_list')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$is_paid=trim($_GET['is_paid']);
	$wheresql=" WHERE uid='".$_SESSION['uid']."' ";
	if($is_paid<>'' && is_numeric($is_paid))
	{
	$wheresql.=" AND is_paid='".intval($is_paid)."' ";
	}
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('order').$wheresql;
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title','充值记录 - 猎头会员中心 - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('is_paid',$is_paid);
	$smarty->assign('payment',get_order_all($offset, $perpage,$wheresql));
	if ($total_val>$perpage)
	{
	$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_hunter/hunter_order_list.htm');
}
elseif ($act=='order_del')
{
	$link[0]['text'] = "返回上一页";
	$link[0]['href'] = '?act=order_list';
	$id=intval($_GET['id']);
	del_order($_SESSION['uid'],$id)?showmsg('取消成功！',2,$link):showmsg('取消失败！',1);
}
elseif ($act=='setmeal_list'  && $_CFG['operation_hunter_mode']=="2")
{
	$setmeal = get_user_setmeal($_SESSION['uid']);
	if ($setmeal['endtime']>0){
		$setmeal_endtime=sub_day($setmeal['endtime'],time());
	}else{
		$setmeal_endtime="无限期";
	}
	$smarty->assign('user_setmeal',$setmeal);
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	$smarty->assign('title','服务列表 - 猎头会员中心 - '.$_CFG['site_name']);
	$smarty->assign('setmeal',get_setmeal());
	$smarty->display('member_hunter/hunter_setmeal_list.htm');
}
elseif ($act=='setmeal_order_add'  && $_CFG['operation_hunter_mode']=="2")
{
	$setmealid = intval($_GET['setmealid'])?intval($_GET['setmealid']):showmsg("请选择服务套餐！",1);
	$setmeal = get_user_setmeal($_SESSION['uid']);
	if ($setmeal['endtime']>0){
		$setmeal_endtime=sub_day($setmeal['endtime'],time());
	}else{
		$setmeal_endtime="无限期";
	}
	$smarty->assign('user_setmeal',$setmeal);
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	$smarty->assign('setmeal',get_setmeal_one($setmealid));
	$smarty->assign('title','申请服务 - 猎头会员中心 - '.$_CFG['site_name']);
	$smarty->assign('payment',get_payment());
	$smarty->display('member_hunter/hunter_order_add_setmeal.htm');
}
elseif ($act=='setmeal_order_add_save'  && $_CFG['operation_hunter_mode']=="2")
{
		if (empty($hunter_profile['huntername']))
		{
		$link[0]['text'] = "填写猎头资料";
		$link[0]['href'] = 'hunter_info.php?act=hunter_profile';
		showmsg("请先填写您的猎头资料！",1,$link);
		}
	$myorder=get_user_order($_SESSION['uid'],1);
	$myorder=count($myorder);
	if ($myorder>=5)
	{
	$link[0]['text'] = "立即查看";
	$link[0]['href'] = '?act=order_list&is_paid=1';
	showmsg("未处理的订单不能超过 5 条，请先处理后再次申请！",1,$link,true,8);
	}
	$setmeal=get_setmeal_one($_POST['setmealid']);
	if ($setmeal && $setmeal['apply']=="1")
	{
		$payment_name=empty($_POST['payment_name'])?showmsg("请选择付款方式！",1):$_POST['payment_name'];
		$paymenttpye=get_payment_info($payment_name);
		if (empty($paymenttpye)) showmsg("支付方式错误！",0);
		$fee=number_format(($setmeal['expense']/100)*$paymenttpye['fee'],1,'.','');//手续费
		$order['oid']= strtoupper(substr($paymenttpye['typename'],0,1))."-".date('ymd',time())."-".date('His',time());//订单号
		$order['v_url']=$_CFG['site_domain'].$_CFG['site_dir']."include/payment/respond_".$paymenttpye['typename'].".php";
		$order['v_amount']=$setmeal['expense']+$fee;//金额
		$order_id=add_order($_SESSION['uid'],$order['oid'],$setmeal['expense'],$payment_name,"开通服务:".$setmeal['setmeal_name'],$timestamp,"",$setmeal['id'],3,1);
			if ($order_id)
			{
				if ($order['v_amount']==0)//0元套餐
				{
					if (order_paid($order['oid']))
					{
						$link[0]['text'] = "查看订单";
						$link[0]['href'] = '?act=order_list';
						$link[1]['text'] = "会员中心首页";
						$link[1]['href'] = 'hunter_index.php?act=';
						showmsg("操作成功，系统已您开通了服务！",2,$link);	
					}
				}
				header("Location:?act=payment&order_id=".$order_id."");//付款页面
			}
			else
			{
			showmsg("添加订单失败！",0);
			}
	}
	else
	{
	showmsg("添加订单失败！",0);
	}
}
unset($smarty);
?>