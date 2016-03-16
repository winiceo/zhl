<?php
/*
 * 74cms ��ѵ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/train_common.php');
$smarty->assign('leftmenu',"service");
if ($act=='account')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$i_type=trim($_GET['i_type']);
	$settr=intval($_GET['settr']);
	if($_CFG['operation_train_mode']=="1"){
		$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9101 ";
	}elseif($_CFG['operation_train_mode']=="2"){
		$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9102 ";
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
		$setmeal_endtime="������";
	}
	
	$smarty->assign('title','�ҵ��˻� - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->assign('setmeal',$setmeal);
	$smarty->assign('points_rule',get_points_rule());
	$smarty->assign('setmeal_rule',get_setmeal_one($setmeal['setmeal_id']));
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	if($_CFG['operation_train_mode']=="1"){
		$smarty->display('member_train/train_my_account.htm');
	}elseif($_CFG['operation_train_mode']=="2"){
		$smarty->display('member_train/train_my_account_package.htm');
	}
	
}
 elseif ($act=='order_add')
{
	$smarty->assign('title','���߳�ֵ - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('payment',get_payment());
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->display('member_train/train_order_add.htm');
}
 elseif ($act=='order_add_save')
{
		if (empty($train_profile['trainname']))
		{
		$link[0]['text'] = "��д��ҵ����";
		$link[0]['href'] = 'train_info.php?act=train_profile';
		showmsg("������д������ҵ���ϣ�",1,$link);
		}
	$myorder=get_user_order($_SESSION['uid'],1);
	if (count($myorder)>=5)
	{
	$link[0]['text'] = "�����鿴";
	$link[0]['href'] = '?act=order_list&is_paid=1';
	showmsg("δ����Ķ������ܳ��� 5 �������ȴ�����ٴ����룡",1,$link,true,8);
	}
	$amount=(trim($_POST['amount'])).(intval($_POST['amount']))?trim($_POST['amount']):showmsg('����д��ֵ��',1);
	($amount<$_CFG['train_payment_min'])?showmsg("���ʳ�ֵ�������� ".$_CFG['train_payment_min']." Ԫ��",1):'';
	$payment_name=empty($_POST['payment_name'])?showmsg("��ѡ�񸶿ʽ��",1):$_POST['payment_name'];
	$paymenttpye=get_payment_info($payment_name);
	if (empty($paymenttpye)) showmsg("֧����ʽ����",0);
	$fee=number_format(($amount/100)*$paymenttpye['fee'],1,'.','');//������
	$order['oid']= strtoupper(substr($paymenttpye['typename'],0,1))."-".date('ymd',time())."-".date('His',time());//������
	$order['v_url']=$_CFG['site_domain'].$_CFG['site_dir']."include/payment/respond_".$paymenttpye['typename'].".php";
	$order['v_amount']=$amount+$fee; 
	$points=$amount*$_CFG['train_payment_rate'];
	$order_id=add_order($_SESSION['uid'],$order['oid'],$amount,$payment_name,"��ֵ����:".$points,$timestamp,$points,'',4,4);
		if ($order_id)
			{
			header("location:?act=payment&order_id=".$order_id);
			}
			else
			{
			showmsg("��Ӷ���ʧ�ܣ�",0);
			}
}
 elseif ($act=='setmeal_list'  && $_CFG['operation_train_mode']=="2")
{
	$setmeal = get_user_setmeal($_SESSION['uid']);
	if ($setmeal['endtime']>0){
		$setmeal_endtime=sub_day($setmeal['endtime'],time());
	}else{
		$setmeal_endtime="������";
	}
	$smarty->assign('user_setmeal',$setmeal);
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	$smarty->assign('title','�����б� - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('setmeal',get_setmeal());
	$smarty->display('member_train/train_setmeal_list.htm');
}
 elseif ($act=='setmeal_order_add'  && $_CFG['operation_train_mode']=="2")
{
	$setmealid = intval($_GET['setmealid'])?intval($_GET['setmealid']):showmsg("��ѡ������ײͣ�",1);
	$setmeal = get_user_setmeal($_SESSION['uid']);
	if ($setmeal['endtime']>0){
		$setmeal_endtime=sub_day($setmeal['endtime'],time());
	}else{
		$setmeal_endtime="������";
	}
	$smarty->assign('user_setmeal',$setmeal);
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	$smarty->assign('setmeal',get_setmeal_one($setmealid));
	$smarty->assign('title','������� - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('payment',get_payment());
	$smarty->display('member_train/train_order_add_setmeal.htm');
}
 elseif ($act=='setmeal_order_add_save'  && $_CFG['operation_train_mode']=="2")
{
		if (empty($train_profile['trainname']))
		{
		$link[0]['text'] = "��д��ѵ��������";
		$link[0]['href'] = 'train_info.php?act=train_profile';
		showmsg("������д������ѵ�������ϣ�",1,$link);
		}
	$myorder=get_user_order($_SESSION['uid'],1);
	$order_num=count($myorder);
	if ($order_num>=5)
	{
	$link[0]['text'] = "�����鿴";
	$link[0]['href'] = '?act=order_list&is_paid=1';
	showmsg("δ����Ķ������ܳ��� 5 �������ȴ�����ٴ����룡",1,$link,true,8);
	}
	$setmeal=get_setmeal_one($_POST['setmealid']);
	if ($setmeal && $setmeal['apply']=="1")
	{
		$payment_name=empty($_POST['payment_name'])?showmsg("��ѡ�񸶿ʽ��",1):$_POST['payment_name'];
		$paymenttpye=get_payment_info($payment_name);
		if (empty($paymenttpye)) showmsg("֧����ʽ����",0);
		$fee=number_format(($setmeal['expense']/100)*$paymenttpye['fee'],1,'.','');//������
		$order['oid']= strtoupper(substr($paymenttpye['typename'],0,1))."-".date('ymd',time())."-".date('His',time());//������
		$order['v_url']=$_CFG['site_domain'].$_CFG['site_dir']."include/payment/respond_".$paymenttpye['typename'].".php";
		$order['v_amount']=$setmeal['expense']+$fee;//���
		$order_id=add_order($_SESSION['uid'],$order['oid'],$setmeal['expense'],$payment_name,"��ͨ����:".$setmeal['setmeal_name'],$timestamp,"",$setmeal['id'],4,1);
			if ($order_id)
			{
				if ($order['v_amount']==0)//0Ԫ�ײ�
				{
					if (order_paid($order['oid']))
					{
						$link[0]['text'] = "�鿴����";
						$link[0]['href'] = '?act=order_list';
						$link[1]['text'] = "��Ա������ҳ";
						$link[1]['href'] = 'train_index.php?act=';
						showmsg("�����ɹ���ϵͳ������ͨ�˷���",2,$link);	
					}
				}
				header("Location:?act=payment&order_id=".$order_id."");//����ҳ��
			}
			else
			{
			showmsg("��Ӷ���ʧ�ܣ�",0);
			}
	}
	else
	{
	showmsg("��Ӷ���ʧ�ܣ�",0);
	}
}
elseif ($act=='payment')
{
	$setmeal = get_user_setmeal($_SESSION['uid']);
	if ($setmeal['endtime']>0){
		$setmeal_endtime=sub_day($setmeal['endtime'],time());
	}else{
		$setmeal_endtime="������";
	}
	$smarty->assign('user_setmeal',$setmeal);
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	$smarty->assign('payment',get_payment());
	$order_id=intval($_GET['order_id']);
	$myorder=get_order_one($_SESSION['uid'],$order_id);
	$payment=get_payment_info($myorder['payment_name']);
	if (empty($payment)) showmsg("֧����ʽ����",0);
	$fee=number_format(($amount/100)*$payment['fee'],1,'.','');//������
	$order['oid']=$myorder['oid'];//������
	$order['v_url']=$_CFG['site_domain'].$_CFG['site_dir']."include/payment/respond_".$payment['typename'].".php";
	$order['v_amount']=$myorder['amount']+$fee;
	if ($myorder['payment_name']!='remittance' && $myorder['payment_name']!='weixinpay')//�����Ƿ�����֧����
	{
		require_once(QISHI_ROOT_PATH."include/payment/".$payment['typename'].".php");
		$payment_form=get_code($order,$payment);
		if (empty($payment_form)) showmsg("����֧����������",0);
	}
	//΢��֧��  ���ɶ�ά��ͼƬ
	elseif($myorder['payment_name']=='weixinpay')
	{
		require_once(QISHI_ROOT_PATH.'include/payment/native.php');
		$smarty->assign('w_url',urlencode($url2));
		fopen(QISHI_ROOT_PATH.'data/wxpay/'.$myorder['oid'].'.tmp', "w") or die("�޷��򿪻����ļ�!");
		$_SESSION['wxpay_no'] = $myorder['oid'];
	}
	$smarty->assign('title','���� - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('fee',$fee);
	$smarty->assign('amount',$myorder['amount']);
	$smarty->assign('oid',$order['oid']);
	$smarty->assign('byname',$payment);
	$smarty->assign('payment_form',$payment_form);
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->display('member_train/train_order_pay.htm');
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
	$smarty->assign('title','��ֵ��¼ - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('is_paid',$is_paid);
	$smarty->assign('payment',get_order_all($offset, $perpage,$wheresql));
	if ($total_val>$perpage)
	{
	$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_train/train_order_list.htm');
}
elseif ($act=='order_del')
{
	$link[0]['text'] = "������һҳ";
	$link[0]['href'] = '?act=order_list';
	$id=intval($_GET['id']);
	del_order($_SESSION['uid'],$id)?showmsg('ȡ���ɹ���',2,$link):showmsg('ȡ��ʧ�ܣ�',1);
}
 elseif ($act=='setmeal_report' && $_CFG['operation_train_mode']=="2")
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$i_type=trim($_GET['i_type']);
	$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9102 ";
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND log_addtime>".$settr_val;
	}
	$perpage=15;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_log').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title','����������ϸ - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('report',get_user_report($offset, $perpage,$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->display('member_train/train_setmeal_report.htm');
}
 elseif ($act=='points_report')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$i_type=trim($_GET['i_type']);
	$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9101 ";
	$settr=intval($_GET['settr']);
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
	$smarty->assign('title','����������ϸ - ��ѵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->assign('report',get_user_report($offset, $perpage,$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->display('member_train/train_points_report.htm');
}
unset($smarty);
?>