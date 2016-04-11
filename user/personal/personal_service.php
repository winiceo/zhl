<?php
/*
 * 74cms ���˻�Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/personal_common.php');
$smarty->assign('leftmenu',"service");

//�ҵ��˻� -> ��«�Ҳ���
if ($act=='j_account')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$smarty->assign('operation_mode',intval($_CFG['operation_mode']));
	//��֧״̬(����->1 ����->2)/����ʱ��
	$cid=trim($_GET['cid']);
	$settr=intval($_GET['settr']);
	//��«��
	$my_points = get_user_points(intval($_SESSION['uid']));
	$smarty->assign('points',$my_points);
	$smarty->assign('act','j_account');
	$smarty->assign('title','�ҵ��˻� - ���˻�Ա���� - '.$_CFG['site_name']);
	//��«��������ϸ
	if(trim($_GET['detail']) == '1')
	{
		$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9001 AND log_mode=2";
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
		$smarty->display('member_personal/personal_my_account_detail.htm');
	}
	//��«�ҹ���
	else
	{
		$smarty->assign('points_rule',get_points_rule());
		$smarty->display('member_personal/personal_my_account.htm');
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
	$smarty->assign('title','��ֵ��¼ - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('is_paid',$is_paid);
	$smarty->assign('payment',get_order_all($offset, $perpage,$wheresql));
	if ($total_val>$perpage)
	{
	$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_personal/personal_order_list.htm');
}
elseif ($act=='order_add')
{
	$smarty->assign('title','���߳�ֵ - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('payment',get_payment());
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->display('member_personal/personal_order_add.htm');
}
elseif ($act=='order_add_save')
{
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
	$order_id=add_order($_SESSION['uid'],4,$order['oid'],$amount,$payment_name,"��ֵ��«��:".$points,$timestamp,$points,'',2);
	if ($order_id)
	{
		header("location:?act=payment&order_id=".$order_id);
	}
	else
	{
		showmsg("��Ӷ���ʧ�ܣ�",0);
	}
}
elseif ($act=='payment')
{
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
	$smarty->assign('title','���� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('fee',$fee);
	$smarty->assign('amount',$myorder['amount']);
	$smarty->assign('oid',$order['oid']);
	$smarty->assign('byname',$payment);
	$smarty->assign('payment_form',$payment_form);
	$smarty->display('member_personal/personal_order_pay.htm');
}
elseif ($act=='order_del')
{
	$link[0]['text'] = "������һҳ";
	$link[0]['href'] = '?act=order_list';
	$id=intval($_GET['id']);
	del_order($_SESSION['uid'],$id)?showmsg('ȡ���ɹ���',2,$link):showmsg('ȡ��ʧ�ܣ�',1);
}
elseif ($act == 'reward_check_list') {
	//�˲���������б�;



	require_once(QISHI_ROOT_PATH . 'include/page.class.php');

	$cate=$_GET["cate"];
	$wheresql = " WHERE 1=1 ";
	$oederbysql = " order BY addtime DESC ";

	if($cate==1){
		$wheresql .= " AND uid = ".$_SESSION["uid"];
	}elseif($cate==2){
		$wheresql .= " AND member_id = ".$_SESSION["uid"];
	}



	 // $wheresql .= " AND uid = ".$_SESSION["uid"];




	$perpage = 10;
	$total_sql = "SELECT COUNT(*) AS num FROM " . table('jobs_reward_clue') . "   {$wheresql} ";
	$total = $db->get_total($total_sql);
	$page = new page(array('total' => $total, 'perpage' => $perpage));
	$offset = ($page->nowindex - 1) * $perpage;
	$smarty->assign('act', $act);
	$smarty->assign('title', '�����ϴ����� - ��ҵ��Ա���� - ' . $_CFG['site_name']);

	if ($total > $perpage) {
		$smarty->assign('page', $page->show(3));
	}

	$member=get_clue_check_list($offset, $perpage, $wheresql);



	$smarty->assign('member',$member );
	$smarty->assign('page', $page->show(3));
	$smarty->assign("cate",$cate);



	if($cate==1){
		$smarty->display('member_personal/company_reward_list.htm');
	}elseif($cate==2){
		$smarty->display('member_personal/company_reward_list1.htm');

	}
}
unset($smarty);



function get_clue_check_list($offset, $perpage, $get_sql = '')
{
	global $db;
	$row_arr = array();
	$limit = " LIMIT " . $offset . ',' . $perpage;
	$result = $db->query("SELECT * FROM " . table('jobs_reward_clue') . " as m " . $get_sql . $limit);
	while ($row = $db->fetch_array($result)) {
		$row['company_url'] = url_rewrite('QS_companyshow', array('id' => $row['company_id']));
		$row['jobs_url'] = url_rewrite('QS_jobsshow', array('id' => $row['job_id']));
		$sql = "select * from ".table('members')." where uid = '{$row["uid"]}' LIMIT 1";

		$row["user"]= $db->getone($sql);
		$row_arr[] = $row;
	}
	return $row_arr;
}
?>