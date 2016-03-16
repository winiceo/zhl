<?php 
 /*
 * 74cms ֧����Ӧҳ��
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$funtype=array('1'=>'include/fun_company.php',2=>'include/fun_personal.php',4=>'include/fun_train.php',3=>'include/fun_hunter.php');
$timestamp = time();
$result = $_POST;
global $_CFG;
if(array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS")
{
	$order=$db->getone("select * from ".table('order')." WHERE oid ='".$result['out_trade_no']."'  LIMIT 1 ");
	require_once(QISHI_ROOT_PATH.$funtype[$order['utype']]);
	//�ж��Ƿ�֧�����
	if(intval($order['is_paid']) == 1)
	{
		$sql = "UPDATE ".table('order')." SET is_paid= '2',payment_time='{$timestamp}' WHERE oid='".$result['out_trade_no']."' LIMIT 1 ";
		if (!$db->query($sql)) return false;
	
		//�ײ͡�����֧��
		if($order['pay_type'] == '1' || $order['pay_type'] == '4')			
		{		 
			$order_name = "�ײͻ��ֶ���";
			$user=get_user_info($order['uid']);
			if($order['amount']=='0.00'){
				$ismoney=1;
			}else{
				$ismoney=2;
			}
			if ($order['points']>0)
			{
				report_deal($order['uid'],1,$order['points']);				
				$user_points=get_user_points($order['uid']);
				$notes=date('Y-m-d H:i',time())."ͨ����".get_payment_info($order['payment_name'],true)." �ɹ���ֵ ".$order['amount']."Ԫ��(+{$order['points']})��(ʣ��:{$user_points}),����:{$v_oid}";					
				write_memberslog($order['uid'],1,9001,$user['username'],$notes); 
				//��Ա�ײͱ����¼����Ա����ɹ���2��ʾ����Ա�Լ�����
				write_setmeallog($order['uid'],$user['username'],$notes,2,$order['amount'],$ismoney,1);
			}
			elseif ($order['setmeal']>0)
			{
				set_members_setmeal($order['uid'],$order['setmeal']);
				$setmeal=get_setmeal_one($order['setmeal']);
				$notes=date('Y-m-d H:i',time())."ͨ����".get_payment_info($order['payment_name'],true)." �ɹ���ֵ ".$order['amount']."Ԫ����ͨ{$setmeal['setmeal_name']}";
				write_memberslog($order['uid'],1,9002,$user['username'],$notes);
				//��Ա�ײͱ����¼����Ա����ɹ���2��ʾ����Ա�Լ�����
				write_setmeallog($order['uid'],$user['username'],$notes,2,$order['amount'],$ismoney,2,1);
			} 
		}
		//���λ֧��
		elseif($order['pay_type'] == '2')		
		{	 
			$order_name = "���λ����";
			write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"������λ��<strong>{$order['description']}</strong>��(���ѣ� {$order['amount']})��",1,1020,"������λ","-{$order['amount']}","{$user_points}"); 
		}
		//�����ײ�֧��
		elseif($order['pay_type'] == '3')		
		{	
			$order_name = "�����ײͶ���";
			$user=get_user_info($order['uid']);
			if($order['setmeal'] > 0){	//�鿴�����ײ�
				set_members_sms($order['uid'],intval($order['setmeal']));	//֧���ɹ������û����Ӷ�������
				$user_points = get_user_setmeal($order['uid']);
				write_memberslog($_SESSION['uid'],1,9003,$_SESSION['username'],"���ų�ֵ�ײͣ�<strong>{$order['description']}</strong>��(- {$order['amount']})��(ʣ��:{$user_points['set_sms']})",1,1020,"������λ","- {$order['amount']}","{$user_points['set_sms']}");
			}
		} 
		//sendemail
		$mailconfig=get_cache('mailconfig');
		if ($mailconfig['set_payment']=="1" && $user['email_audit']=="1" && $order['amount']>0)
		{
		dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_mail.php?uid={$order['uid']}&key=".asyn_userkey($order['uid'])."&act=set_payment");
		} 
		//sms
		$sms=get_cache('sms_config');
		if ($sms['open']=="1" && $sms['set_payment']=="1"  && $user['mobile_audit']=="1" && $order['amount']>0)
		{  
			dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_sms.php?uid={$order['uid']}&key=".asyn_userkey($order['uid'])."&act=set_payment"); 
		}
		//return true;
 	}
 	// else
 	// {
 	// 	//return true;
 	// }
 	
 	echo $order['oid'];
}

?>
