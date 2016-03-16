<?php
/*
 * 74cms ��ҵ���Ź�������
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"sms");
	 
if(check_sms_open() == true){ 
	$link[0]['text'] = "��Ա������ҳ";
	$link[0]['href'] = 'company_index.php?act=';
	showmsg("��û��Ȩ�޲�����",2,$link);	 
}
if ($act=='sms_order')
{ 
	$sms_setmeal = get_sms_setmeal($_SESSION['uid']);
	$smarty->assign('sms_setmeal',$sms_setmeal); 
	$smarty->assign('title','���ų�ֵ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_company/company_sms_order.htm');
} 
elseif ($act=='sms_order_add')
{ 
	$setmealid = intval($_GET['setmealid'])?intval($_GET['setmealid']):showmsg("��ѡ������ײͣ�",1); 
	$smarty->assign('title','��������ײ� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('meal_sms',get_sms_setmeal_one($setmealid));
	$smarty->assign('payment',get_payment());
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->display('member_company/company_sms_order_add.htm');
}
elseif ($act=='order_sms_add_save')
{
	if (!$cominfo_flge)
	{
		$link[0]['text'] = "��д��ҵ����";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("������д������ҵ���ϣ�",1,$link);
	}
	 
	$sms_meal=get_sms_setmeal_one($_POST['meal_id']); 
	$payment_name=empty($_POST['payment_name'])?showmsg("��ѡ�񸶿ʽ��",1):$_POST['payment_name']; 
	if($payment_name=="points"){
		//ѡ�����ģʽ
		$p = get_user_points($_SESSION['uid']);
		$expense = intval($_POST['points_expense_input']);
		if($p<$expense){
			showmsg("���Ļ��ֲ�����֧����",1);
		}
		$order['oid']= "P-".date('ymd',time())."-".date('His',time());//������
		$order['v_amount']=$expense;	//֧���Ļ�������
		$order_id=add_sms_order($_SESSION['uid'],$order['oid'],$expense, $payment_name,$sms_meal['setmeal_name'],$timestamp,$expense,intval($_POST['meal_id']));
	}
	else
	{	//��Ǯģʽ 
		$paymenttpye=get_payment_info($payment_name);
		if (empty($paymenttpye)) showmsg("֧����ʽ����",0);
		$expense = intval($_POST['expense_input']);
		$fee=number_format(($expense/100)*$paymenttpye['fee'],1,'.','');//������
		$order['oid']= strtoupper(substr($paymenttpye['typename'],0,1))."-".date('ymd',time())."-".date('His',time());//������
		$order['v_url']=$_CFG['site_domain'].$_CFG['site_dir']."include/payment/respond_".$paymenttpye['typename'].".php";
		$order['v_amount']=$expense+$fee;//���
		$order_id=add_sms_order($_SESSION['uid'],$order['oid'],$expense, $payment_name,$sms_meal['setmeal_name'],$timestamp,0,intval($_POST['meal_id']));
	}
	if($order_id)
	{
		if ($order['v_amount']==0)	//0Ԫ�����ײ�
		{
			if (sms_order_paid($order['oid']))
			{
				$link[0]['text'] = "�鿴����";
				$link[0]['href'] = 'company_service.php?act=order_list';
				$link[1]['text'] = "��Ա������ҳ";
				$link[1]['href'] = 'company_index.php?act=';
				showmsg("�����ɹ�����ȴ�����Ա��ˣ�",2,$link);	
			}
		}
		header("Location:?act=sms_payment&order_id=".$order_id."");//����ҳ��
	}
	else
	{
	showmsg("��Ӷ���ʧ�ܣ�",0);
	}
}
elseif ($act=='sms_payment')
{
	$_SESSION['sms_pay'] = 1;
	$smarty->assign('payment',get_payment());
	$order_id=intval($_GET['order_id']);
	$myorder=get_sms_order_one($_SESSION['uid'],$order_id);
	if($myorder['payment_name']!="points")
	{ 
		$payment=get_payment_info($myorder['payment_name']);
		if (empty($payment)) showmsg("֧����ʽ����",0); 
		$fee=number_format(($myorder['amount']/100)*$payment['fee'],1,'.','');//������
		$order['oid']=$myorder['oid'];//������
		//������//�ж��Ƿ���֧����  ����֧�����ṩ�첽֪ͨ����
		if($payment['typename']=='alipay')
		{
			$order['n_url']=$_CFG['site_domain'].$_CFG['site_dir']."include/payment/respond.php";
		}
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
	}
	if($myorder['payment_name']=="points")
	{
		$myorder['amount'] = intval($myorder['amount']);
	} 
	$myorder['payment_name_'] = get_payment_info($myorder['payment_name'],true);
	$smarty->assign('myorder',$myorder);
	$smarty->assign('fee',$fee);
	$smarty->assign('title','���� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('byname',$payment);
	$smarty->assign('payment_form',$payment_form);
	$smarty->display('member_company/company_sms_order_pay.htm');
}
elseif($act == "sms_order_pay")
{
	$orderid = intval($_GET['order_id'])?intval($_GET['order_id']):showmsg("��û��ѡ�񶩵���",1);
	$myorder=get_adv_order_one($_SESSION['uid'],$orderid);
	if (sms_order_paid($myorder['oid']))
	{
		$link[0]['text'] = "�鿴����";
		$link[0]['href'] = 'company_service.php?act=order_list';
		$link[1]['text'] = "��Ա������ҳ";
		$link[1]['href'] = 'company_index.php?act=';
		showmsg("�����ɹ�����ȴ�����Ա��ˣ�",2,$link);	
	}
}
unset($smarty);
?>