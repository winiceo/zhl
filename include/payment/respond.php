<?php 
 /*
 * ֧���첽����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
require_once(QISHI_ROOT_PATH."include/payment/alipay.php");

$dingdan           = $_POST['out_trade_no'];		//��ȡ������
$total_fee         = $_POST['total_fee'];		//��ȡ�ܼ۸�
$order=$db->getone("select * from ".table('order')." WHERE oid ='{$dingdan}'  LIMIT 1 ");
$funtype=array(1=>'include/fun_company.php',4=>'include/fun_train.php',3=>'include/fun_hunter.php');
require_once(QISHI_ROOT_PATH.$funtype[$order['utype']]);

$time=date('Y-m-d H:i:s',time());
$log_file = QISHI_ROOT_PATH.'/data/'.'allpay_safe.txt'; 
fputs(fopen($log_file,'a+'),$time.'/'.$dingdan.'/'.$order['is_paid']."\r\n");

	
$payment= get_payment_info('alipay');	
$partner		= trim($payment['partnerid']);
$key			= trim($payment['ytauthkey']);
$sign_type		= "MD5";
$_input_charset	= "GBK";
$transport		= "http";
$alipay = new alipay_notify($partner,$key,$sign_type,$_input_charset,$transport);
$verify_result = $alipay->notify_verify();
if($verify_result) {//��֤�ɹ�
	order_paid($dingdan);
	return 'success';
}
else {
	return 'err';
}
?>
