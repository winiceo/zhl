<?php
 /*
 * 74cms ��֤����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : ''; 
$email=trim($_POST['email']);
$send_key=trim($_POST['send_key']);
if (empty($send_key) || $send_key<>$_SESSION['send_email_key'])
{
exit("Ч�������");
}
if ($act=="send_code")
{
		if (empty($email) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]w+)*$/",$email))
		{
		exit("�����ʽ����");
		}
		$sql = "select * from ".table('members')." where email = '{$email}' LIMIT 1";
		$userinfo=$db->getone($sql);
		if ($userinfo && $userinfo['uid']<>$_SESSION['uid'])
		{
		exit("�����Ѿ����ڣ�����д��������");
		}
		elseif(!empty($userinfo['email']) && $userinfo['email_audit']=="1" && $userinfo['email']==$email)
		{
		exit("������� {$email} �Ѿ�ͨ����֤��");
		}
		else
		{
			if ($_SESSION['sendemail_time'] && (time()-$_SESSION['sendemail_time'])<10)
			{
			exit("��60����ٽ�����֤��");
			}
			$rand=mt_rand(100000, 999999);
			if (smtp_mail($email,"{$_CFG['site_name']}�ʼ���֤","{$QISHI['site_name']}��������<br>�����ڽ���������֤����֤��Ϊ:<strong>{$rand}</strong>"))
			{
			$_SESSION['verify_email']=$email;
			$_SESSION['email_rand']=$rand;
			$_SESSION['sendemail_time']=time();
			exit("success");
			}
			else
			{
			exit("�������ó�������ϵ��վ����Ա");
			}
		} 
}
elseif ($act=="verify_code")
{
	$verifycode=trim($_POST['verifycode']);
	if (empty($verifycode) || empty($_SESSION['email_rand']) || $verifycode<>$_SESSION['email_rand'])
	{
		exit("��֤�����");
	}
	else
	{
			$uid=intval($_SESSION['uid']);
			if (empty($uid))
			{
				exit("ϵͳ����UID��ʧ��");
			}
			else
			{
					$setsqlarr['email']=$_SESSION['verify_email'];
					$setsqlarr['email_audit']=1;
					$db->updatetable(table('members'),$setsqlarr," uid='{$uid}'");
					if ($_SESSION['utype']=="2")
					{
						$infoarr['email']=$setsqlarr['email'];
						$db->updatetable(table('members_info'),$infoarr," uid='{$uid}'");
						$u['email']=$setsqlarr['email'];
						$db->updatetable(table('resume'),$u," uid='{$uid}'");
						// ���ֲ���
						$rule=get_cache('points_rule');
						if ($rule['per_verifyemail']['value']>0)
						{
							$info=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$_SESSION['uid']}' AND htype='per_verifyemail'   LIMIT 1");
							if(empty($info))
							{
							$time=time();			
							$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$_SESSION['uid']}', 'per_verifyemail','{$time}')");
							require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
							report_deal($_SESSION['uid'],$rule['per_verifyemail']['type'],$rule['per_verifyemail']['value']);
							$user_points=get_user_points($_SESSION['uid']);
							$operator=$rule['per_verifyemail']['type']=="1"?"+":"-";
							$_SESSION['handsel_per_verifyemail']=$_CFG['points_byname'].$operator.$rule['per_verifyemail']['value'];
							write_memberslog($_SESSION['uid'],2,9001,$_SESSION['username']," ����ͨ����֤��{$_CFG['points_byname']}({$operator}{$rule['per_verifyemail']['value']})��(ʣ��:{$user_points})",2,1015,"������֤ͨ��","{$operator}{$rule['per_verifyemail']['value']}","{$user_points}");
							}
						}
					}
					unset($setsqlarr,$_SESSION['verify_email'],$_SESSION['email_rand'],$u,$infoarr);
					if (($_CFG['operation_mode']=='1' || $_CFG['operation_mode']=='3') && $_SESSION['utype']=='1')
					{
						$rule=get_cache('points_rule');
						if ($rule['verifyemail']['value']>0)
						{
							$info=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$_SESSION['uid']}' AND htype='verifyemail'   LIMIT 1");
							if(empty($info))
							{
							$time=time();			
							$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$_SESSION['uid']}', 'verifyemail','{$time}')");
							require_once(QISHI_ROOT_PATH.'include/fun_company.php');
							report_deal($_SESSION['uid'],$rule['verifyemail']['type'],$rule['verifyemail']['value']);
							$user_points=get_user_points($_SESSION['uid']);
							$operator=$rule['verifyemail']['type']=="1"?"+":"-";
							$_SESSION['handsel_verifyemail']=$_CFG['points_byname'].$operator.$rule['verifyemail']['value'];
							write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username']," ����ͨ����֤��{$_CFG['points_byname']}({$operator}{$rule['verifyemail']['value']})��(ʣ��:{$user_points})",1,1015,"������֤ͨ��","{$operator}{$rule['verifyemail']['value']}","{$user_points}");
							}
						}
					}elseif ($_CFG['operation_train_mode']=='1' && $_SESSION['utype']=='4')
					{
						$rule=get_cache('points_rule');
						if ($rule['train_verifyemail']['value']>0)
						{
							$info=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$_SESSION['uid']}' AND htype='verifyemail'   LIMIT 1");
							if(empty($info))
							{
							$time=time();			
							$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$_SESSION['uid']}', 'verifyemail','{$time}')");
							require_once(QISHI_ROOT_PATH.'include/fun_train.php');
							report_deal($_SESSION['uid'],$rule['train_verifyemail']['type'],$rule['train_verifyemail']['value']);
							$user_points=get_user_points($_SESSION['uid']);
							$operator=$rule['train_verifyemail']['type']=="1"?"+":"-";
							$_SESSION['handsel_verifyemail']=$_CFG['train_points_byname'].$operator.$rule['train_verifyemail']['value'];
							write_memberslog($_SESSION['uid'],4,9101,$_SESSION['username']," ����ͨ����֤��{$_CFG['train_points_byname']}({$operator}{$rule['train_verifyemail']['value']})��(ʣ��:{$user_points})");
							}
						}
					}elseif ($_CFG['operation_hunter_mode']=='1' && $_SESSION['utype']=='3')
					{
						$rule=get_cache('points_rule');
						if ($rule['hunter_verifyemail']['value']>0)
						{
							$info=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$_SESSION['uid']}' AND htype='verifyemail'   LIMIT 1");
							if(empty($info))
							{
							$time=time();			
							$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$_SESSION['uid']}', 'verifyemail','{$time}')");
							require_once(QISHI_ROOT_PATH.'include/fun_hunter.php');
							report_deal($_SESSION['uid'],$rule['hunter_verifyemail']['type'],$rule['hunter_verifyemail']['value']);
							$user_points=get_user_points($_SESSION['uid']);
							$operator=$rule['hunter_verifyemail']['type']=="1"?"+":"-";
							$_SESSION['handsel_verifyemail']=$_CFG['hunter_points_byname'].$operator.$rule['hunter_verifyemail']['value'];
							write_memberslog($_SESSION['uid'],3,9201,$_SESSION['username']," ����ͨ����֤��{$_CFG['hunter_points_byname']}({$operator}{$rule['hunter_verifyemail']['value']})��(ʣ��:{$user_points})");
							}
						}
					}

					exit("success");
			}
	}
}
?>