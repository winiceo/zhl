<?php
/*
 * 74cms ��ҵ��Ա����ajax������
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/hunter_common.php');
if($act=="user_email"){
	$tpl='../../templates/'.$_CFG['template_dir']."plus/ajax_authenticate_email_box.htm";
	$contents=file_get_contents($tpl);
	$_SESSION['send_email_key']=mt_rand(100000, 999999);
	$contents=str_replace('{#$email#}',$user["email"],$contents);
	$contents=str_replace('{#$send_email_key#}',$_SESSION['send_email_key'],$contents);
	$contents=str_replace('{#$notice#}','',$contents);
	exit($contents);
}	
elseif($act=="user_mobile"){
	$tpl='../../templates/'.$_CFG['template_dir']."plus/ajax_authenticate_mobile_box.htm";
	$contents=file_get_contents($tpl);
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$contents=str_replace('{#$mobile#}',$user["mobile"],$contents);
	$contents=str_replace('{#$send_mobile_key#}',$_SESSION['send_mobile_key'],$contents);
	$contents=str_replace('{#$notice#}','',$contents);
	exit($contents);
}
elseif($act=="old_mobile"){
	$tpl='../../templates/'.$_CFG['template_dir']."plus/ajax_authenticate_old_mobile_box.htm";
	$contents=file_get_contents($tpl);
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$user["hid_mobile"] = substr($user["mobile"],0,3)."*****".substr($user["mobile"],7,4);
	$contents=str_replace('{#$mobile#}',$user["mobile"],$contents);
	$contents=str_replace('{#$hid_mobile#}',$user["hid_mobile"],$contents);
	$contents=str_replace('{#$send_mobile_key#}',$_SESSION['send_mobile_key'],$contents);
	exit($contents);
}
elseif($act=="edit_mobile"){
	$tpl='../../templates/'.$_CFG['template_dir']."plus/ajax_authenticate_edit_mobile_box.htm";
	$contents=file_get_contents($tpl);
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$contents=str_replace('{#$send_mobile_key#}',$_SESSION['send_mobile_key'],$contents);
	$contents=str_replace('{#$notice#}','',$contents);
	exit($contents);
}
//�� �յ��ļ���
elseif($act=='reply')
{
	$resume_id = $_GET['resume_id']?$_GET['resume_id']:exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td class="ajax_app">
					�ü���id��ʧ��
				</td>
		    </tr>
		</table>');
	$jobs_id = $_GET['jobs_id']?$_GET['jobs_id']:exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td class="ajax_app">
					ְλid��ʧ��
				</td>
		    </tr>
		</table>');
	$tpl='../../templates/'.$_CFG['template_dir']."member_hunter/ajax_reply_resume.htm"; 
	$contents=file_get_contents($tpl);
	$contents=str_replace('{#$resume_id#}',$resume_id,$contents);   
	$contents=str_replace('{#$jobs_id#}',$jobs_id,$contents);   
	exit($contents);
}
elseif($act=='reply_save')
{ 
	$is_reply = intval($_POST['is_reply'])?$_POST['is_reply']:exit('-1'); 
	$yid = trim($_POST['resume_id'])?$_POST['resume_id']:exit('-1');  
	$jobs_id = trim($_POST['jobs_id'])?$_POST['jobs_id']:exit('-1');
	if(reply_resume($yid,$jobs_id,$is_reply)){
		exit('1');
	}else{
		exit('2');
	} 
}
elseif($act == 'check_weixinpay_notify'){
	if(file_exists(QISHI_ROOT_PATH.'data/wxpay/'.$_SESSION['wxpay_no'].'.tmp')){
		exit('1');
	}else{
		@unlink(QISHI_ROOT_PATH.'data/wxpay/'.$_SESSION['wxpay_no'].'.tmp');
		unset($_SESSION['wxpay_no']);
		exit($_CFG['site_dir'].'user/hunter/hunter_service.php?act=order_list');
	}
}
unset($smarty);
?>