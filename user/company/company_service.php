<?php
/*
 * 74cms ��ҵ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
require_once(QISHI_ROOT_PATH . '/genv/func_company.php');
$smarty->assign('leftmenu',"service");
$smarty->assign('act',$act);

//�ҵ��˻� -> ��«�Ҳ���
if ($act=='j_account')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$smarty->assign('operation_mode',intval($_CFG['operation_mode']));
	//��֧״̬(����->1 ����->2)/����ʱ��
	$cid=trim($_GET['cid']);
	$settr=intval($_GET['settr']);
	//�ײ�
	$my_setmeal = get_user_setmeal($_SESSION['uid']);
	$smarty->assign('setmeal',$my_setmeal);
	//��«��
	$my_points = get_user_points(intval($_SESSION['uid']));
    $my_balance = get_user_balance(intval($_SESSION['uid']));
	$smarty->assign('points',$my_points);
    $smarty->assign('balance', $my_balance);
	$smarty->assign('act','j_account');
	$smarty->assign('title','�ҵ��˻� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	//��«��������ϸ
	if(trim($_GET['detail']) == '1')
	{
		$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9001 AND log_mode=1";
		if($settr>0)
		{
			$settr_val=strtotime("-".$settr." day");
			$wheresql.=" AND log_addtime>".$settr_val;
			$smarty->assign('settr',$_GET['settr']);
		}
		if($cid == '1')
		{
			$smarty->assign('c_type',"����");
			$smarty->assign('cid',$_GET['cid']);
			$wheresql.=" AND log_op_used < 0 ";
		}
		elseif($cid == '2')
		{
			$smarty->assign('c_type',"����");
			$smarty->assign('cid',$_GET['cid']);
			$wheresql.=" AND log_op_used > 0 ";
		}
		$perpage=10;
		$total_sql="SELECT COUNT(*) AS num FROM ".table('members_log').$wheresql;
		$total_val=$db->get_total($total_sql);
		$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
		$offset=($page->nowindex-1)*$perpage;
		$smarty->assign('report',get_user_report($offset, $perpage,$wheresql));
		$smarty->assign('page',$page->show(3));
		$smarty->display('member_company/company_my_account_detail.htm');
	}
	//��«�ҹ���
	else
	{
		$smarty->assign('points_rule',get_points_rule());
		$smarty->display('member_company/company_my_account.htm');
	}
}
//�ҵ��˻� -> �ײͲ��� 
elseif ($act=='t_account')
{
	$settr=intval($_GET['settr']);
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$smarty->assign('operation_mode',intval($_CFG['operation_mode']));
	//��«��
	$my_points = get_user_points(intval($_SESSION['uid']));
	$smarty->assign('points',$my_points);
	//�ײ�
	$my_setmeal = get_user_setmeal($_SESSION['uid']);
	$smarty->assign('setmeal',$my_setmeal);
	$smarty->assign('act','t_account');
	$smarty->assign('title','�ҵ��˻� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	//�ײ�������ϸ
	if(trim($_GET['detail']) == '1')
	{
		$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9002 AND log_mode=2 ";
		if($settr>0)
		{
			$settr_val=strtotime("-".$settr." day");
			$wheresql.=" AND log_addtime>".$settr_val;
			$smarty->assign('settr',$_GET['settr']);
		}
		$perpage=10;
		$total_sql="SELECT COUNT(*) AS num FROM ".table('members_log').$wheresql;
		$total_val=$db->get_total($total_sql);
		$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
		$offset=($page->nowindex-1)*$perpage;
		$smarty->assign('report',get_user_report($offset, $perpage,$wheresql));
		$smarty->assign('page',$page->show(3));
		$smarty->display('member_company/company_my_account_package_detail.htm');
	}
	//�ײ͹���
	else
	{
		//������Ƹְλ �� �˲ſ����� Ҫ��������һ�� ��Ϊ���ǵõ���ͳ��
		//��������Ƹְλ = �����е� + ����˵�
		$jobs="SELECT COUNT(*) AS num FROM ".table('jobs')." where uid='{$_SESSION['uid']}'";
		$jobs_num=$db->get_total($jobs);
		$jobs_tmp="SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." where uid='{$_SESSION['uid']}' and  audit=2 ";
		$jobs_tmp_num=$db->get_total($jobs_tmp);
		$smarty->assign('jobs_num',intval($jobs_num)+intval($jobs_tmp_num));
	   	//�˲ſ�����
		$favorites="SELECT COUNT(*) AS num FROM ".table('company_favorites')." where company_uid='{$_SESSION['uid']}'";
		$favorites_num=$db->get_total($favorites);
		$smarty->assign('favorites_num',intval($favorites_num));
		$smarty->assign('setmeal_rule',get_members_setmeal_rule($my_setmeal['setmeal_id']));
		$smarty->display('member_company/company_my_account_package.htm');
	}

}
elseif ($act=='order_list')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$is_paid=trim($_GET['is_paid']);
	$pay_type=intval($_GET['pay_type']);
	$wheresql=" WHERE uid='".$_SESSION['uid']."' ";
	//����״̬
	if($is_paid<>'' && is_numeric($is_paid))
	{
		$smarty->assign('is_paid',$is_paid);
		$wheresql.=" AND is_paid='".intval($is_paid)."' ";
	}
	//�������
	if($pay_type > 0)
	{
		$smarty->assign('pay_type',$pay_type);
		$wheresql.=" AND pay_type='".intval($pay_type)."' ";
	}
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('order').$wheresql;
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title','��ֵ��¼ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('is_paid',$is_paid);
	$smarty->assign('payment',get_order_all($offset, $perpage,$wheresql));
	if ($total_val>$perpage)
	{
	$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_company/company_order_list.htm');
}
elseif ($act=='order_add')
{
	$smarty->assign('title','���߳�ֵ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('payment',get_payment());
	$smarty->assign('points',get_user_points($_SESSION['uid']));
    $smarty->assign('balance', get_user_balance($_SESSION['uid']));
    $smarty->assign('balance_can', get_user_can_balance($_SESSION['uid']));
    $plants=get_points_plan("money desc");
    if($plants){

        $smarty->assign('points_plan',get_points_plan());
        $testJSON=array();
        foreach ( $plants as $key => $value ) {
            foreach($value as $p=>$m){
                $testJSON[$key][$p] = urlencode(  $m );
            }
        }
         $json_str=  urldecode( json_encode ( $testJSON,JSON_UNESCAPED_UNICODE ) );

        $smarty->assign('points_plan_js',$json_str);

    }
    $smarty->display('member_company/company_order_add.htm');
}
elseif ($act=='order_add_save')
{
		if (!$cominfo_flge)
		{
		$link[0]['text'] = "��д��ҵ����";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("������д������ҵ���ϣ�",1,$link);
		}
	$myorder=get_user_order($_SESSION['uid'],1);
	$order_num=count($myorder);
	if ($order_num>=5)
	{
	$link[0]['text'] = "�����鿴";
	$link[0]['href'] = '?act=order_list&is_paid=1';
	showmsg("δ����Ķ������ܳ��� 5 �������ȴ�����ٴ����룡",1,$link,true,8);
	}
	$amount=(trim($_POST['amount'])).(intval($_POST['amount']))?trim($_POST['amount']):showmsg('����д��ֵ��',1);
	($amount<$_CFG['payment_min'])?showmsg("���ʳ�ֵ�������� ".$_CFG['payment_min']." Ԫ��",1):'';
	$payment_name=empty($_POST['payment_name'])?showmsg("��ѡ�񸶿ʽ��",1):$_POST['payment_name'];
	$paymenttpye=get_payment_info($payment_name);
	if (empty($paymenttpye)) showmsg("֧����ʽ����",0);
	$fee=number_format(($amount/100)*$paymenttpye['fee'],1,'.','');//������
	$order['oid']= strtoupper(substr($paymenttpye['typename'],0,1))."-".date('ymd',time())."-".date('His',time());//������
	$order['v_url']=$_CFG['site_domain'].$_CFG['site_dir']."include/payment/respond_".$paymenttpye['typename'].".php";
	$order['v_amount']=$amount+$fee; 
	$points=$amount*$_CFG['payment_rate'];
	$order_id=add_order($_SESSION['uid'],4,$order['oid'],$amount,$payment_name,"��ֵ��«��:".$points,$timestamp,$points,'',1);
		if ($order_id)
			{
			header("location:?act=payment&order_id=".$order_id);
			}
			else
			{
			showmsg("��Ӷ���ʧ�ܣ�",0);
			}
} elseif ($act == 'pay_add') {
    $smarty->assign('title', '���߳�ֵ - ��ҵ��Ա���� - ' . $_CFG['site_name']);
    $smarty->assign('payment', get_payment());
    $smarty->assign('points', get_user_points($_SESSION['uid']));
    $smarty->assign('balance', get_user_balance($_SESSION['uid']));
    $smarty->assign('balance_can', get_user_can_balance($_SESSION['uid']));


    $smarty->display('member_company/company_pay_add.htm');
}elseif ($act == 'pay_reduce') {
    $smarty->assign('title', '���߳�ֵ - ��ҵ��Ա���� - ' . $_CFG['site_name']);
    $smarty->assign('payment', get_payment());
    $smarty->assign('points', get_user_points($_SESSION['uid']));
    $smarty->assign('balance', get_user_balance($_SESSION['uid']));
    $smarty->assign('balance_can', get_user_can_balance($_SESSION['uid']));

    $smarty->display('member_company/company_pay_reduce.htm');
} elseif ($act == 'pay_add_save') {
    if (!$cominfo_flge) {
        $link[0]['text'] = "��д��ҵ����";
        $link[0]['href'] = 'company_info.php?act=company_profile';
        showmsg("������д������ҵ���ϣ�", 1, $link);
    }
    $myorder = get_user_order($_SESSION['uid'], 1);
    $order_num = count($myorder);
    if ($order_num >= 5) {
        $link[0]['text'] = "�����鿴";
        $link[0]['href'] = '?act=order_list&is_paid=1';
        showmsg("δ����Ķ������ܳ��� 5 �������ȴ�����ٴ����룡", 1, $link, true, 8);
    }
    $amount = (trim($_POST['amount'])) . (intval($_POST['amount'])) ? trim($_POST['amount']) : showmsg('����д��ֵ��', 1);
    ($amount < $_CFG['payment_min']) ? showmsg("���ʳ�ֵ�������� " . $_CFG['payment_min'] . " Ԫ��", 1) : '';
    $payment_name = empty($_POST['payment_name']) ? showmsg("��ѡ�񸶿ʽ��", 1) : $_POST['payment_name'];
    $paymenttpye = get_payment_info($payment_name);
    if (empty($paymenttpye)) showmsg("֧����ʽ����", 0);
    $fee = number_format(($amount / 100) * $paymenttpye['fee'], 1, '.', '');//������
    $order['oid'] = strtoupper(substr($paymenttpye['typename'], 0, 1)) . "-" . date('ymd', time()) . "-" . date('His', time());//������
    $order['v_url'] = $_CFG['site_domain'] . $_CFG['site_dir'] . "include/payment/respond_" . $paymenttpye['typename'] . ".php";
    $order['v_amount'] = $amount + $fee;
    $points = $amount * $_CFG['payment_rate'];
    $order_id = add_order($_SESSION['uid'], 7, $order['oid'], $amount, $payment_name, "�ֽ��ֵ:" . $points, $timestamp, $points, '', 1);
    if ($order_id) {

        header("location:?act=payment&order_id=" . $order_id);
    } else {
        showmsg("��Ӷ���ʧ�ܣ�", 0);
    }
}elseif ($act == 'pay_add_points_save') {
    //�����«�ң�
    if (!$cominfo_flge) {
        $link[0]['text'] = "��д��ҵ����";
        $link[0]['href'] = 'company_info.php?act=company_profile';
        showmsg("������д������ҵ���ϣ�", 1, $link);
    }
    $myorder = get_user_order($_SESSION['uid'], 1);
    $order_num = count($myorder);
    if ($order_num >= 5) {
        $link[0]['text'] = "�����鿴";
        $link[0]['href'] = '?act=order_list&is_paid=1';
        showmsg("δ����Ķ������ܳ��� 5 �������ȴ�����ٴ����룡", 1, $link, true, 8);
    }
    $amount = (trim($_POST['amount'])) . (intval($_POST['amount'])) ? trim($_POST['amount']) : showmsg('����д��ֵ��', 1);
    $description = trim($_POST['description'])  ;
    $points = 0;
    if($amount>get_user_can_balance($_SESSION["uid"])){
        showmsg("����������", 1);
    }
    $points = $amount * $_CFG['payment_rate'];

    $plants=get_points_plan("money desc");
    $free_points=0;
    if($plants){
        foreach($plants as $key=>$v){
            if($amount>=$v["money"]){
                $free_points=$v["free_points"];
                break;
            }
        }
    }
    $points=$points+$free_points;
	balance_deal($_SESSION["uid"],2,$amount);

    $order_id = add_order($_SESSION['uid'], 4, $order['oid'], $amount, $payment_name, "�����«��:" . $points, $timestamp, $points, '', 1);
    if ($order_id) {
        $sql = "UPDATE ".table('order')." SET is_paid=2,payment_time='{$timestamp}'  WHERE id='{$order_id}' LIMIT 1";

        $db->query($sql);

        report_deal($_SESSION['uid'],1,$points);
        $user_points=get_user_points($_SESSION['uid']);
       // $notes="�����ˣ�{$_SESSION['admin_name']},˵����ȷ���տ�տ��{$order['amount']} ��".date('Y-m-d H:i',time())."ͨ����".get_payment_info($order['payment_name'],true)." �ɹ���ֵ ".$order['amount']."Ԫ��(+{$order['points']})��(ʣ��:{$user_points}),����:{$v_oid}";
       // write_memberslog($order['uid'],1,9001,$user['username'],$notes);

        header("location:?act=order_list");
    } else {
        showmsg("��Ӷ���ʧ�ܣ�", 0);
    }
}elseif ($act == 'pay_reduce_save') {

    //���ֶ�������
    if (!$cominfo_flge) {
        $link[0]['text'] = "��д��ҵ����";
        $link[0]['href'] = 'company_info.php?act=company_profile';
        showmsg("������д������ҵ���ϣ�", 1, $link);
    }
    $myorder = get_user_order($_SESSION['uid'], 1);
    $order_num = count($myorder);
    if ($order_num >= 5) {
        $link[0]['text'] = "�����鿴";
        $link[0]['href'] = '?act=order_list&is_paid=1';
        showmsg("δ����Ķ������ܳ��� 5 �������ȴ�����ٴ����룡", 1, $link, true, 8);
    }
    $amount = (trim($_POST['amount'])) . (intval($_POST['amount'])) ? trim($_POST['amount']) : showmsg('����д��ֵ��', 1);
    $description = empty($_POST['description']) ? showmsg("��ѡ���տ���Ϣ��", 1) : $_POST['description'];

    $order['oid'] = "TX-" . date('ymd', time()) . "-" . date('His', time());//������

    $points = 0;
    if($amount>get_user_can_balance($_SESSION["uid"])){
        showmsg("���ֽ���������", 1);
    }

    $order_id = add_order($_SESSION['uid'], 9, $order['oid'], $amount, "moneyreduce", "�������:" . $amount.";".$description, $timestamp, $points, '', 1);

    if ($order_id) {
        header("location:?act=order_list");
    } else {
        showmsg("��Ӷ���ʧ�ܣ�", 0);
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
	$fee=number_format(($myorder['amount']/100)*$payment['fee'],1,'.','');//������
	$order['oid']=$myorder['oid'];//������
	//�ж��Ƿ���֧����  ����֧�����ṩ�첽֪ͨ����
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
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->assign('title','���� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('fee',$fee);
	$smarty->assign('amount',$myorder['amount']);
	$smarty->assign('oid',$order['oid']);
	$smarty->assign('byname',$payment);
	$smarty->assign('payment_form',$payment_form);
	$smarty->display('member_company/company_order_pay.htm');
}
elseif ($act=='order_del')
{
	$link[0]['text'] = "������һҳ";
	$link[0]['href'] = '?act=order_list';
	$id=intval($_GET['id']);
	del_order($_SESSION['uid'],$id)?showmsg('ȡ���ɹ���',2,$link):showmsg('ȡ��ʧ�ܣ�',1);
}
elseif ($act=='setmeal_list')
{
	$setmeal = get_user_setmeal($_SESSION['uid']);
	if ($setmeal['endtime']>0){
		$setmeal_endtime=sub_day($setmeal['endtime'],time());
	}else{
		$setmeal_endtime="������";
	}
	$smarty->assign('user_setmeal',$setmeal);
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	$smarty->assign('title','�����б� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('setmeal',get_setmeal());
	$smarty->display('member_company/company_setmeal_list.htm');
}
elseif ($act=='setmeal_order_add')
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
	$smarty->assign('title','������� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('setmeal',get_setmeal_one($setmealid));
	$smarty->assign('payment',get_payment());
	$smarty->display('member_company/company_order_add_setmeal.htm');
}
elseif ($act=='setmeal_order_add_save')
{
		if (!$cominfo_flge)
		{
		$link[0]['text'] = "��д��ҵ����";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("������д������ҵ���ϣ�",1,$link);
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
		$order_id=add_order($_SESSION['uid'],1,$order['oid'],$setmeal['expense'],$payment_name,"��ͨ����:".$setmeal['setmeal_name'],$timestamp,"",$setmeal['id'],1);
			if ($order_id)
			{
				if ($order['v_amount']==0)//0Ԫ�ײ�
				{
					if (order_paid($order['oid']))
					{
						$link[0]['text'] = "�鿴����";
						$link[0]['href'] = 'company_service.php?act=order_list';
						$link[1]['text'] = "��Ա������ҳ";
						$link[1]['href'] = 'company_index.php?act=';
						showmsg("�����ɹ���ϵͳ��Ϊ����ͨ�˷���",2,$link);	
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
elseif ($act=='feedback')
{
	$smarty->assign('title','�û����� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('feedback',get_feedback($_SESSION['uid']));
	$smarty->display('member_company/company_feedback.htm');
}
elseif ($act=='gifts')
{
	$smarty->assign('title','��Ʒ�� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('gifts',get_gifts($_SESSION['uid']));
	$captcha=get_cache('captcha');
	$smarty->assign('verify_gifts',$captcha['verify_gifts']);
	$smarty->display('member_company/company_gifts.htm');
}
elseif ($act=='gifts_apy')
{
	$account=trim($_POST['account'])?trim($_POST['account']):showmsg("����д���ţ�",1);
	$pwd=trim($_POST['pwd'])?trim($_POST['pwd']):showmsg("����д���룡",1);
	$captcha=get_cache('captcha');
	$postcaptcha = trim($_POST['postcaptcha']);
	if($captcha['verify_gifts']=='1' && empty($postcaptcha))
	{
		showmsg("����д��֤��",1);
 	}
	if ($captcha['verify_gifts']=='1' &&  strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
	{
		showmsg("��֤�����",1);
	}
	$info=$db->getone("select * from ".table('gifts')." where account='{$account}'  AND password='{$pwd}' LIMIT 1 ");
	if (empty($info))
	{
		showmsg("���Ż��������",0);
	}
	else
	{
		if ($info['usettime']>0)
		{
		showmsg("���ſ��ѱ�ʹ�ã������ظ�ʹ��",1);
		}
		else
		{
			$gifts_type=$db->getone("select * from ".table('gifts_type')." where t_id='{$info['t_id']}' LIMIT 1 ");
			if($gifts_type['t_endtime']!=0&&$gifts_type['t_endtime']<strtotime(date("Y-m-d"))){
				showmsg("���ſ��ѳ�����Ч�ڣ�����ʹ��",1);
			}
			if($gifts_type['t_effective']==0){
				showmsg("���ſ��ѱ�����Ա����Ϊ�����ã�����ϵ��վ����Ա",1);
			}
			if ($gifts_type['t_repeat']>0)
			{
				$total=$db->get_total("SELECT COUNT(*) AS num FROM ".table('members_gifts')." where uid='{$_SESSION['uid']}'");
				if ($total>=$gifts_type['t_repeat'])
				{
				showmsg("{$gifts_type['t_name']} ÿ����Ա������ʹ�� {$gifts_type['t_repeat']} �Ρ�",1);
				}
			}
			$db->query( "UPDATE ".table('gifts')." SET usettime = '".time()."',useuid= '{$_SESSION['uid']}'  where account='{$account}'");
			$setsqlarr['uid']=$_SESSION['uid'];
			$setsqlarr['usetime']=time();
			$setsqlarr['account']=$account;
			$setsqlarr['giftsname']=$gifts_type['t_name'];
			$setsqlarr['giftsamount']=$gifts_type['t_amount'];
			$setsqlarr['giftstid']=$gifts_type['t_id'];
			$db->inserttable(table('members_gifts'),$setsqlarr);
			report_deal($_SESSION['uid'],1,$setsqlarr['giftsamount']);
			$user_points=get_user_points($_SESSION['uid']);
			$operator="+";
			write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"ʹ����Ʒ��({$account})��ֵ({$operator}{$setsqlarr['giftsamount']})��(ʣ��:{$user_points})",1,1021,"��Ʒ����ֵ","{$operator}{$setsqlarr['giftsamount']}","{$user_points}");
			showmsg("��ֵ�ɹ���",2);	
					
		}
	}
}
elseif ($act=='feedback_save')
{
	$get_feedback=get_feedback($_SESSION['uid']);
	if (count($get_feedback)>=5) 
	{
	showmsg('������Ϣ���ܳ���5����',1);
	exit();
	}
	$setsqlarr['infotype']=intval($_POST['infotype']);
	$setsqlarr['feedback']=trim($_POST['feedback'])?trim($_POST['feedback']):showmsg("����д���ݣ�",1);
	$setsqlarr['uid']=$_SESSION['uid'];
	$setsqlarr['usertype']=$_SESSION['utype'];
	$setsqlarr['username']=$_SESSION['username'];
	$setsqlarr['addtime']=$timestamp;
	write_memberslog($_SESSION['uid'],1,7001,$_SESSION['username'],"����˷�����Ϣ");
	!$db->inserttable(table('feedback'),$setsqlarr)?showmsg("���ʧ�ܣ�",0):showmsg("��ӳɹ�����ȴ�����Ա�ظ���",2);
}
elseif ($act=='del_feedback')
{
	$id=intval($_GET['id']);
	del_feedback($id,$_SESSION['uid'])?showmsg('ɾ���ɹ���',2):showmsg('ɾ��ʧ�ܣ�',1);
}
elseif ($act=='adv_list')
{
	$smarty->assign('title','������λ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('adv_list',get_adv_list());
	$smarty->display('member_company/company_adv_list.htm');
}
elseif ($act=='adv_order_add')
{
	$advid = intval($_GET['advid'])?intval($_GET['advid']):showmsg("��ѡ����λ��",1);
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->assign('title','������λ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('advinfo',get_adv_one($advid));
	$smarty->assign('payment',get_payment());
	$smarty->display('member_company/company_order_add_adv.htm');
}
elseif ($act=='order_adv_add_save')
{
	if (!$cominfo_flge)
	{
	$link[0]['text'] = "��д��ҵ����";
	$link[0]['href'] = 'company_info.php?act=company_profile';
	showmsg("������д������ҵ���ϣ�",1,$link);
	}
	$advinfo=get_adv_one($_POST['advid']);
	$week=intval($_POST['week']);
	$payment_name=empty($_POST['payment_name'])?showmsg("��ѡ�񸶿ʽ��",1):$_POST['payment_name'];
	if($payment_name=="points")
	{
		$p = get_user_points($_SESSION['uid']);
		$expense = intval($_POST['points_expense_input']);
		if($p<$expense){
			showmsg("���ĺ�«�Ҳ�����֧����",1);
		}
		$order['oid']= "P-".date('ymd',time())."-".date('His',time());//������
		$order['v_amount']=$expense;			//���
		$order_id=add_adv_order($_SESSION['uid'],$order['oid'],$order['v_amount'],$week,$payment_name,$advinfo['categoryname'],$timestamp,$expense); 
	}
	else
	{
		$paymenttpye=get_payment_info($payment_name);
		if (empty($paymenttpye)) showmsg("֧����ʽ����",0);
		$expense = intval($_POST['expense_input']);
		$fee=number_format(($expense/100)*$paymenttpye['fee'],1,'.','');//������
		$order['oid']= strtoupper(substr($paymenttpye['typename'],0,1))."-".date('ymd',time())."-".date('His',time());//������
		$order['v_url']=$_CFG['site_domain'].$_CFG['site_dir']."include/payment/respond_".$paymenttpye['typename'].".php";
		$order['v_amount']=$expense+$fee;//���
		$order_id=add_adv_order($_SESSION['uid'],$order['oid'],$expense,$week,$payment_name,$advinfo['categoryname'],$timestamp,0);
	}
	if ($order_id)
	{
		if ($order['v_amount']==0)	//0Ԫ���λ
		{
			if (adv_order_paid($order['oid']))
			{
				$link[0]['text'] = "�鿴����";
				$link[0]['href'] = 'company_service.php?act=order_list';
				$link[1]['text'] = "��Ա������ҳ";
				$link[1]['href'] = 'company_index.php?act=';
				showmsg("�����ɹ�����ȴ�����Ա��ˣ�",2,$link);	
			}
		}
		header("Location:?act=adv_payment&order_id=".$order_id."");//����ҳ��
	}
	else
	{
	showmsg("��Ӷ���ʧ�ܣ�",0);
	}
}
elseif ($act=='adv_payment')
{
	$_SESSION['adv_pay'] = 1;
	$smarty->assign('payment',get_payment());
	$order_id=intval($_GET['order_id']);
	$myorder=get_adv_order_one($_SESSION['uid'],$order_id);
	if($myorder['payment_name']!="points")
	{
		$payment=get_payment_info($myorder['payment_name']);
		if (empty($payment)) showmsg("֧����ʽ����",0);
		$fee=number_format(($myorder['amount']/100)*$payment['fee'],1,'.','');//������
		$order['oid']=$myorder['oid'];//������
		//�ж��Ƿ���֧����  ����֧�����ṩ�첽֪ͨ����
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
	if($myorder['payment_name']=="points"){
		$myorder['amount'] = intval($myorder['amount']);
	} 
	$myorder['payment_name_'] = get_payment_info($myorder['payment_name'],true);
	$smarty->assign('myorder',$myorder);
	$smarty->assign('fee',$fee);
	$smarty->assign('title','���� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('byname',$payment); 
	$smarty->assign('payment_form',$payment_form);
	$smarty->display('member_company/company_adv_order_pay.htm');
}
elseif($act == "adv_order_pay"){
	$orderid = intval($_GET['order_id'])?intval($_GET['order_id']):showmsg("��û��ѡ�񶩵���",1);
	$myorder=get_adv_order_one($_SESSION['uid'],$orderid);
	if (adv_order_paid($myorder['oid']))
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