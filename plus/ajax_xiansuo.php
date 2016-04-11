<?php
 /*
 * 申请职位
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'app';
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
require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
$user=get_user_info($_SESSION['uid']);
	
?>
<?php

if ($act=="rencai_save")
{
	$jobsid=isset($_POST['jobs_id'])?$_POST['jobs_id']:exit("出错了");
	$pms_notice=intval($_POST['pms_notice']);
	$jobsql="select * from ".table('hunter_jobs')." WHERE id=".$jobsid;	
	$jobres=$db->getone($jobsql);
	$jobs['uid']=$jobres['uid'];
	$now_uid=$_SESSION['uid'];
	$now_user=$db->getone("select username from ".table('members')." where uid ={$now_uid} limit 1");
	$now_username=$now_user['username'];
	//站内信
	if($pms_notice=='1'){
		$user=$db->getone("select username from ".table('members')." where uid ={$jobs['uid']} limit 1");
		$user = array_map("addslashes", $user);
		$message=$now_username."向您推荐了人才：人才姓名（{$_POST['rencainame']}）;人才电话（{$_POST['rencaiphone']}）;人才邮箱（{$_POST['rencaiemail']}）";
		write_pmsnotice($jobs['uid'],$user['username'],$message);
	}
	$pid=write_memberslog($_SESSION['uid'],2,1301,$_SESSION['username'],"提供了人才信息");
	if(empty($pid))
	{
		exit('fail');
	}else
	{
		exit('ok');
	}
}

?>
<script type="text/javascript">
//提交保存
$("#ajax_ok").click(function() {
	var jobs_id=<?php echo intval($_GET['jobs_id']) ?>;
	var tsTimeStamp= new Date().getTime();
	var pms_notice=$("#pms_notice").val();
	var rencainame=$("#rencainame").val();
	var rencaiphone=$("#rencaiphone").val();
	var rencaiemail=$("#rencaiemail").val();
	var notes=$("#notes").val();
	$.post("<?php echo $_CFG['site_dir'] ?>plus/ajax_xiansuo.php", { "rencainame": rencainame, "rencaiphone":rencaiphone , "rencaiemail":rencaiemail ,"notes":notes ,"pms_notice":pms_notice,"time":tsTimeStamp,"act":"rencai_save","jobs_id":jobs_id},function(data){
	//alert(data);
		if (data=="ok")
		{
			$("#app").hide();
			$("#app_ok").show();
		}
	})
});
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall" id="app">
   	
    <tr>
		<td width="120" align="right">人才姓名：</td>
		<td>
			<input type="text" name="rencainame"  id="rencainame" class="input_text_login_user" value="" />
		</td>
   	</tr>
    <tr>
		<td width="120" align="right">人才电话：</td>
		<td>
			<input type="text" name="rencaiphone"  id="rencaiphone" class="input_text_login_user" value="" />
		</td>
   	</tr>
    <tr>
		<td width="120" align="right">人才邮箱：</td>
		<td>
			<input type="text" name="rencaiemail"  id="rencaiemail" class="input_text_login_user" value="" />
		</td>
   	</tr>
    <tr>
		<td align="right">其他说明：</td>
		<td>
			<textarea name="notes" id="notes"  style="width:300px; height:60px; line-height:180%; font-size:12px;"></textarea>
		</td>
    </tr>
    <tr>
		<td align="right">站内信通知对方：</td>
		<td>
			 <label><input type="checkbox" name="pms_notice" id="pms_notice" value="1"  checked="checked"/>
		  站内信通知
		   </label>
		</td>
    </tr>
    <tr>
		<td></td>
		<td>
			<input type="button" name="Submit"  id="ajax_ok" class="but130lan" value="确定" />
		</td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="5" cellpadding="0" id="waiting"  style="display:none">
  <tr>
    <td align="center" height="60"><img src="<?php echo  $_CFG['site_template']?>images/30.gif"  border="0"/></td>
  </tr>
  <tr>
    <td align="center" >请稍后...</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall" id="app_ok" style="display:none">
    <tr>
		<td width="140" align="right"><img height="100" src="<?php echo  $_CFG['site_template']?>images/big-yes.png" /></td>
		<td>
			<strong style="font-size:14px ; color:#0066CC;margin-left:20px">发送成功!</strong>
			<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:50px;margin-left:20px"  class="dialog_closed">
			<a href="javascript:void(0)"  class="DialogClose underline" style="color:#0180cf;text-decoration:none;">发送完成</a>
			</div>
		</td>
    </tr>
</table>

