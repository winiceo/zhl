<?php
 /*
 * 74cms �ٱ�
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if((empty($_SESSION['uid']) || empty($_SESSION['username']) || empty($_SESSION['utype'])) &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'] && $_COOKIE['QS']['uid'])
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	if(check_cookie($_COOKIE['QS']['uid'],$_COOKIE['QS']['username'],$_COOKIE['QS']['password']))
	{
	update_user_info($_COOKIE['QS']['uid'],false,false);
	header("Location:".get_member_url($_SESSION['utype']));
	}
	else
	{
	unset($_SESSION['uid'],$_SESSION['username'],$_SESSION['utype'],$_SESSION['uqqid'],$_SESSION['activate_username'],$_SESSION['activate_email'],$_SESSION["openid"]);
	setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[username]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[password]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	}
}
if ($_SESSION['uid']=='' || $_SESSION['username']=='')
{
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userlogin',$captcha['verify_userlogin']);
	$smarty->display('plus/ajax_login.htm');
	exit();
}
if ($_SESSION['utype']!='1')
{
	exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td>
					��������ҵ��Ա�ſ��ԶԼ������б��״̬��
				</td>
		    </tr>
		</table>');
}
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
$user=get_user_info($_SESSION['uid']);
if ($user['status']=="2") 
{
	exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td>
					�����˺Ŵ�����ͣ״̬������ϵ����Ա��Ϊ��������в�����
				</td>
		    </tr>
		</table>');
}
$resume_id=$_REQUEST['resume_id']?intval($_REQUEST['resume_id']):exit("����ID��ʧ��");
$setarr['resume_state']=$_REQUEST['resume_state']?intval($_REQUEST['resume_state']):exit("���״̬����");
$setarr['resume_state_cn']=$_REQUEST['resume_state_cn']?iconv('utf-8', 'gbk',trim($_REQUEST['resume_state_cn'])):exit("���״̬����");
$p_uid = $db->getone("SELECT uid FROM ".table('resume')." WHERE id={$resume_id} LIMIT 1 ");
$uid=intval($_SESSION['uid']);
$row=$db->getone("select resume_id from ".table("company_label_resume")." where uid=$uid and resume_id=$resume_id limit 1");
if(empty($row))
{
	$setarr['resume_id']=$resume_id;
	$setarr['uid']=$uid;
	$setarr['personal_uid']=$p_uid['uid'];
	$db->inserttable(table('company_label_resume'),$setarr);
	//���鿴״̬���³��Ѿ��鿴
	$db->updatetable(table('personal_jobs_apply'),array('personal_look'=>'2','is_reply'=>$setarr['resume_state']),array("company_uid"=>$uid,"resume_id"=>$resume_id));
}
else
{
	$db->updatetable(table('company_label_resume'),$setarr,array("uid"=>$uid,"resume_id"=>$resume_id));
	//���鿴״̬���³��Ѿ��鿴
	$db->updatetable(table('personal_jobs_apply'),array('personal_look'=>'2','is_reply'=>$setarr['resume_state']),array("company_uid"=>$uid,"resume_id"=>$resume_id));
}
exit("ok");
?>
