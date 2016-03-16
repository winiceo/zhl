<?php
 /*
 * 74cms SMS
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : ''; 
$mobile=trim($_POST['mobile']);
$send_key=trim($_POST['send_key']);
if (empty($send_key) || $send_key<>$_SESSION['send_mobile_key'])
{
exit("Ч�������");
}
$SMSconfig=get_cache('sms_config');
if ($SMSconfig['open']!="1")
{
exit("����ģ�鴦�ڹر�״̬");
}
if ($act=="send_code")
{
		if (empty($mobile) || !preg_match("/^(13|15|14|17|18)\d{9}$/",$mobile))
		{
		exit("�ֻ��Ŵ���");
		}
		$sql = "select * from ".table('members')." where mobile = '{$mobile}' LIMIT 1";
		$userinfo=$db->getone($sql);
		if ($userinfo && $userinfo['uid']<>$_SESSION['uid'])
		{
		exit("�ֻ����Ѿ����ڣ�����д�����ֻ�����");
		}
		elseif(!empty($userinfo['mobile']) && $userinfo['mobile_audit']=="1" && $userinfo['mobile']==$mobile)
		{
		exit("����ֻ��� {$mobile} �Ѿ�ͨ����֤��");
		}
		else
		{
			if ($_SESSION['send_time'] && (time()-$_SESSION['send_time'])<180)
			{
			exit("��180����ٽ�����֤��");
			}
			$rand=mt_rand(100000, 999999);	
			$r=captcha_send_sms($mobile,"��л��ʹ��{$_CFG['site_name']}�ֻ���֤,��֤��Ϊ:{$rand}");
			if ($r=="success")
			{
			$_SESSION['mobile_rand']=$rand;
			$_SESSION['send_time']=time();
			$_SESSION['verify_mobile']=$mobile;
			exit("success");
			}
			else
			{
			exit("SMS���ó���������ϵ��վ����Ա");
			}
		} 
}
elseif ($act=="verify_code")
{
	$verifycode=trim($_POST['verifycode']);
	if (empty($verifycode) || empty($_SESSION['mobile_rand']) || $verifycode<>$_SESSION['mobile_rand'])
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
					$setsqlarr['mobile']=$_SESSION['verify_mobile'];
					$setsqlarr['mobile_audit']=1;
					$db->updatetable(table('members'),$setsqlarr," uid='{$uid}'");
					if ($_SESSION['utype']=="2")
					{
						$infoarr['phone']=$setsqlarr['mobile'];
						$db->updatetable(table('members_info'),$infoarr," uid='{$uid}'");
						
						$u['telephone']=$setsqlarr['mobile'];
						$db->updatetable(table('resume'),$u," uid='{$uid}'");
						// ���ֲ���
						$rule=get_cache('points_rule');
						if ($rule['per_verifymobile']['value']>0)
						{
							$info=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$_SESSION['uid']}' AND htype='per_verifymobile'   LIMIT 1");
							if(empty($info))
							{
							$time=time();			
							$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$_SESSION['uid']}', 'per_verifymobile','{$time}')");
							require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
							report_deal($_SESSION['uid'],$rule['per_verifymobile']['type'],$rule['per_verifymobile']['value']);
							$user_points=get_user_points($_SESSION['uid']);
							$operator=$rule['per_verifymobile']['type']=="1"?"+":"-";
							$_SESSION['handsel_per_verifymobile']=$_CFG['points_byname'].$operator.$rule['per_verifymobile']['value'];
							write_memberslog($_SESSION['uid'],2,9001,$_SESSION['username']," �ֻ�ͨ����֤��{$_CFG['points_byname']}({$operator}{$rule['per_verifymobile']['value']})��(ʣ��:{$user_points})",2,1015,"�ֻ���֤ͨ��","{$operator}{$rule['per_verifymobile']['value']}","{$user_points}");
							}
						}
					}
					unset($setsqlarr,$infoarr,$_SESSION['verify_mobile'],$_SESSION['mobile_rand']);
					if ($_SESSION['utype']=="1" && ($_CFG['operation_mode']=='1' || $_CFG['operation_mode']=='3'))
					{
						$rule=get_cache('points_rule');
						if ($rule['verifymobile']['value']>0)
						{
							$info=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$_SESSION['uid']}' AND htype='verifymobile' LIMIT 1");
							if(empty($info))
							{
							$time=time();			
							$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$_SESSION['uid']}', 'verifymobile','{$time}')");
							require_once(QISHI_ROOT_PATH.'include/fun_comapny.php');
							report_deal($_SESSION['uid'],$rule['verifymobile']['type'],$rule['verifymobile']['value']);
							$user_points=get_user_points($_SESSION['uid']);
							$operator=$rule['verifymobile']['type']=="1"?"+":"-";
							$_SESSION['handsel_verifymobile']=$_CFG['points_byname'].$operator.$rule['verifymobile']['value'];
							write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username']," �ֻ�ͨ����֤��{$_CFG['points_byname']}({$operator}{$rule['verifymobile']['value']})��(ʣ��:{$user_points})",1,1016,"�ֻ���֤ͨ��","{$operator}{$rule['verifymobile']['value']}","{$user_points}");
							}
						}
					}elseif ($_SESSION['utype']=='4' && $_CFG['operation_train_mode']=='1')
					{
						$rule=get_cache('points_rule');
						if ($rule['train_verifymobile']['value']>0)
						{
							$info=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$_SESSION['uid']}' AND htype='verifymobile' LIMIT 1");
							if(empty($info))
							{
							$time=time();			
							$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$_SESSION['uid']}', 'verifymobile','{$time}')");
							require_once(QISHI_ROOT_PATH.'include/fun_train.php');
							report_deal($_SESSION['uid'],$rule['train_verifymobile']['type'],$rule['train_verifymobile']['value']);
							$user_points=get_user_points($_SESSION['uid']);
							$operator=$rule['train_verifymobile']['type']=="1"?"+":"-";
							$_SESSION['handsel_verifymobile']=$_CFG['train_points_byname'].$operator.$rule['train_verifymobile']['value'];
							write_memberslog($_SESSION['uid'],4,9101,$_SESSION['username']," �ֻ�ͨ����֤��{$_CFG['train_points_byname']}({$operator}{$rule['train_verifymobile']['value']})��(ʣ��:{$user_points})");
							}
						}
					}elseif ($_SESSION['utype']=='3' && $_CFG['operation_hunter_mode']=='1')
					{
						$rule=get_cache('points_rule');
						if ($rule['hunter_verifymobile']['value']>0)
						{
							$info=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$_SESSION['uid']}' AND htype='verifymobile' LIMIT 1");
							if(empty($info))
							{
							$time=time();			
							$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$_SESSION['uid']}', 'verifymobile','{$time}')");
							require_once(QISHI_ROOT_PATH.'include/fun_hunter.php');
							report_deal($_SESSION['uid'],$rule['hunter_verifymobile']['type'],$rule['hunter_verifymobile']['value']);
							$user_points=get_user_points($_SESSION['uid']);
							$operator=$rule['hunter_verifymobile']['type']=="1"?"+":"-";
							$_SESSION['handsel_verifymobile']=$_CFG['hunter_points_byname'].$operator.$rule['hunter_verifymobile']['value'];
							write_memberslog($_SESSION['uid'],3,9201,$_SESSION['username']," �ֻ�ͨ����֤��{$_CFG['hunter_points_byname']}({$operator}{$rule['hunter_verifymobile']['value']})��(ʣ��:{$user_points})");
							}
						}
					}

					exit("success");
			}
	}
}
// ���˷������� �޸ļ�����ʱ�� 
elseif($act == "mobile_code")
{
	$verifycode=trim($_POST['mobile_code']);
	if (empty($verifycode) || empty($_SESSION['mobile_rand']) || $verifycode<>$_SESSION['mobile_rand'])
	{
		exit("false");
	}
	else
	{
		$uid=intval($_SESSION['uid']);
		if (empty($uid))
		{
			exit("false");
		}
		else
		{
			unset($_SESSION['verify_mobile'],$_SESSION['mobile_rand']);
			exit("true");

		}
	}
}
?>