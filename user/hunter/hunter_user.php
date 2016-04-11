<?php
/*
 * 74cms ��ͷ��Ա����
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/hunter_common.php');
$smarty->assign('leftmenu',"user");
if ($act=='binding')
{
	$smarty->assign('user',$user);
	$smarty->assign('title','�˺Ű� - ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_hunter/hunter_binding.htm');
}
elseif ($act=='pm')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$uid=intval($_SESSION['uid']);
	$new=intval($_GET['new']);
	$msgtype=intval($_GET['msgtype']);
	$wheresql=" WHERE (p.msgfromuid='{$uid}' OR p.msgtouid='{$uid}') ";
	$joinsql=" LEFT JOIN  ".table('members')." AS i  ON  p.msgfromuid=i.uid ";
	$orderby=" order by p.pmid desc";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('pms').' AS p '.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$sql="SELECT p.* FROM ".table('pms').' AS p'.$joinsql.$wheresql.$orderby; 
	//��ȡ���鿴��Ϣ��pmid , ���ҽ����޸�Ϊ�Ѷ�
	$pmid = update_pms_read($offset, $perpage,$sql);
	if(!empty($pmid))
	{
		$db->query("UPDATE ".table('pms')." SET `new`='2' WHERE new=1 AND msgtouid='{$uid}' and pmid in (".$pmid.")");	
	}
	else
	{
		$db->query("UPDATE ".table('pms')." SET `new`='2' WHERE new=1 AND msgtouid='{$uid}'");
	}	
	get_pms_no_num();
	$smarty->assign('pms',get_pms($offset,$perpage,$sql));
	$smarty->assign('title','����Ϣ - ��Ա���� - '.$_CFG['site_name']);	
	$smarty->assign('page',$page->show(3));
	$smarty->assign('uid',$uid); 
	
	$smarty->display('member_hunter/hunter_user_pm.htm');
}
elseif ($act=='pm_del')
{
	$pmid=intval($_GET['pmid']);
	$uid=intval($_SESSION['uid']);
	$pms= $db->getone("select * from ".table('pms')." where pmid = '{$pmid}' AND (msgfromuid='{$uid}' OR msgtouid='{$uid}') LIMIT 1");
	if (!empty($pms))
	{
	$db->query("Delete from ".table('pms')." WHERE pmid='{$pms['pmid']}'");
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = "?act=pm&msgtype={$_GET['msgtype']}&new={$_GET['new']}";
	//ͳ����Ϣ
	$pmscount=$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$_SESSION['uid']}' OR msgtouid='{$_SESSION['uid']}') AND `new`='1' ");
	setcookie('QS[pmscount]',$pmscount, $expire,$QS_cookiepath,$QS_cookiedomain);
	showmsg("�����ɹ���",2,$link);
}
elseif ($act=='authenticate')
{
	$uid = intval($_SESSION['uid']);
	$smarty->assign('user',$user);
	$smarty->assign('re_audit',$_GET['re_audit']);
	$smarty->assign('title','��֤���� - ���˻�Ա���� - '.$_CFG['site_name']);
	$_SESSION['send_key']=mt_rand(100000, 999999);
	$smarty->assign('send_key',$_SESSION['send_key']);
	/**
	 * ΢��ɨ���start
	 */
    if(intval($_CFG['weixin_apiopen'])==1 && intval($_CFG['weixin_scan_bind'])==1 && !$user['weixin_openid']){
	    $scene_id = mt_rand(20000001,30000000);
	    $_SESSION['scene_id'] = $scene_id;
		$dir = QISHI_ROOT_PATH.'data/weixin/'.($scene_id%10);
		make_dir($dir);
	    $fp = @fopen($dir.'/'.$scene_id.'.txt', 'wb+');
		$access_token = get_access_token();
	    $post_data = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$scene_id.'}}}';
	    $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
	    $result = https_request($url, $post_data);
	    $result_arr = json_decode($result,true);
	    $ticket = urlencode($result_arr["ticket"]);
	    $html = '<img width="240" height="240" src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket.'">';
		$smarty->assign('qrcode_img',$html);
	}else{
		$smarty->assign('qrcode_img','');
	}
    /**
     * ΢��ɨ���end
     */
	$smarty->display('member_hunter/hunter_authenticate.htm');
}
//�޸�����
elseif ($act=='password_edit')
{
	$smarty->assign('title','�޸����� - ��ͷ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_hunter/hunter_password.htm');
}
//�����޸�����
elseif ($act=='save_password')
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$arr['username']=$_SESSION['username'];
	$arr['oldpassword']=trim($_POST['oldpassword'])?trim($_POST['oldpassword']):showmsg('����������룡',1);
	$arr['password']=trim($_POST['password'])?trim($_POST['password']):showmsg('�����������룡',1);
	if ($arr['password']!=trim($_POST['password1'])) showmsg('�����������벻��ͬ�����������룡',1);
	$info=edit_password($arr);
	if ($info==-1) showmsg('����������������������룡',1);
	if ($info==$_SESSION['username']){
			//sendemail
			$mailconfig=get_cache('mailconfig');
			if ($mailconfig['set_editpwd']=="1" && $user['email_audit']=="1")
			{
			dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_mail.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_editpwd&newpassword={$arr['password']}");
			}
			//sendemail
			//sms
			$sms=get_cache('sms_config');
			if ($sms['open']=="1" && $sms['set_editpwd']=="1"  && $user['mobile_audit']=="1")
			{
			dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_sms.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_editpwd&newpassword={$arr['password']}");
			}
			//sms
			if(defined('UC_API'))
			{
			include_once(QISHI_ROOT_PATH.'uc_client/client.php');
			uc_user_edit($arr['username'],$arr['oldpassword'], $arr['password']);
			}
			showmsg('�����޸ĳɹ���',2);
	}
}
//�����޸��û���
elseif ($act=='save_username')
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$arr['uid']=$_SESSION['uid'];
	$_POST['newusername'] = utf8_to_gbk($_POST['newusername']);
	$arr['newusername']=trim($_POST['newusername'])?trim($_POST['newusername']):showmsg('���û�����',1);
	if(!preg_match("/^[".chr(0xa1)."-".chr(0xff)."a-zA-Z0-9_]{3,18}$/", $arr['newusername']))
	{
		exit("-4");
	}
	$row_newname = $db->getone("SELECT * FROM ".table('members')." WHERE username='{$arr['newusername']}' LIMIT 1");
	if($row_newname)
	{
		exit("-1");
	}
	$info=edit_username($arr);
	if ($info==-1) exit("-2");
	if (!$info) exit("-3");
	exit("1");
}
elseif ($act=='del_qq_binding')
{
	$db->query("UPDATE ".table('members')." SET qq_openid = ''  WHERE uid='{$_SESSION[uid]}' LIMIT 1");
	exit('�����ѶQQ�󶨳ɹ���');
}
elseif ($act=='del_sina_binding')
{
	$db->query("UPDATE ".table('members')." SET sina_access_token = ''  WHERE uid='{$_SESSION[uid]}' LIMIT 1");
	exit('�������΢���󶨳ɹ���');
}
elseif ($act=='del_taobao_binding')
{
	$db->query("UPDATE ".table('members')." SET taobao_access_token = ''  WHERE uid='{$_SESSION[uid]}' LIMIT 1");
	exit('����Ա��˺Ű󶨳ɹ���');
}
elseif ($act=='login_log')
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type='1001' ";
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND log_addtime >".$settr_val;
	}
	$perpage=15;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_log').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('loginlog',get_user_loginlog($offset, $perpage,$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('title','��Ա��¼��־ - ��ͷ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_hunter/hunter_user_loginlog.htm');
}
unset($smarty);
?>