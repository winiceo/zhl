<?php
 /*
 * 74cms �ٱ�
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
if ($_SESSION['utype']!='2')
{
	exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td>
					�����Ǹ��˻�Ա�ſ��Ծٱ�ְλ��Ϣ��
				</td>
		    </tr>
		</table>');
}
require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
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
if ($act=="report")
{		
		$id=isset($_GET['jobs_id'])?$_GET['jobs_id']:exit("id ��ʧ");
		$jobs=app_get_jobs($id);
		if (empty($jobs))
		{
			exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			    <tr>
					<td width="20" align="right"></td>
					<td>
						�ٱ���Ϣʧ�ܣ�
					</td>
			    </tr>
			</table>');
		}
		if (check_jobs_report($_SESSION['uid'],intval($_GET['jobs_id'])))
		{
			exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			    <tr>
					<td width="20" align="right"></td>
					<td>
						���Ѿ��ٱ�����ְλ��
					</td>
			    </tr>
			</table>');
		}
?>
<script type="text/javascript">
$(".but80").hover(function(){$(this).addClass("but80_hover")},function(){$(this).removeClass("but80_hover")});
//���������������

//��֤
$("#ajax_report").click(function() {
	var content=$("#content").val();
	if (content=="")
	{
	alert("����������");
	}
	else
	{
		$("#report").hide();
		$("#waiting").show();
		
		$.post("<?php echo $_CFG['site_dir'] ?>user/user_report.php", { "jobs_id": $("#jobs_id").val(),"jobs_name": $("#jobs_name").val(),"content": $("#content").val(),"report_type":$('input[name="report_type"]:checked').val(),"jobs_addtime":$("#jobs_addtime").val(),"act":"app_save"},

	 	function (data,textStatus)
	 	 {
			if (data=="ok")
			{
				$("#report").hide();
				$("#waiting").hide();
				$("#app_ok").show();
			}
			else
			{
				$("#report").hide();
				$("#waiting").hide();
				$("#app_ok").hide();
				$("#error_msg").html("�ٱ�ʧ�ܣ�"+data);
				$("#error").show();
			}
	 	 });
	}
});
</script>
<div class="report-dialog" id="report">
	<input type="hidden" id="jobs_id" value="<?php echo intval($_GET['jobs_id']);?>">
	<input type="hidden" id="jobs_name" value="<?php echo trim($_GET['jobs_name']);?>">
	<input type="hidden" id="jobs_addtime" value="<?php echo trim($_GET['jobs_addtime']);?>">
	<div class="report-item clearfix">
		<div class="report-type f-left">�ٱ�ԭ��</div>
		<div class="report-content f-left">
			<label><input type="radio" name="report_type"  class="radio" value="1" checked="checked"/>��Ϣ���<span>����д����������������ݣ�</span></label>
			<label><input type="radio" name="report_type"  class="radio" value="2" />�绰��ͨ<span>���绰���δͨ��</span></label>
			<label><input type="radio" name="report_type"  class="radio" value="3" />����ԭ��<span>�����н�ȣ�</span></label>
		</div>
	</div>
	<div class="report-item clearfix">
		<div class="report-type f-left">���������</div>
		<div class="report-content f-left">
			<textarea name="content" id="content" cols="30" rows="10"></textarea>
		</div>
	</div>
	<span class="r-all-row">һ����ʵ�����ǻ�����... </span>
	<div class="report-item clearfix">
		<div class="report-type f-left">&nbsp;</div>
		<div class="report-content f-left">
			<p class="del-info">ɾ����Ϣ��Ϊ����� </p>
			<p class="del-info">վ����֪ͨ�� </p>
		</div>
	</div>
	<div class="center-btn-box">
		<input type="button" value="�ٱ�" class="btn-65-30blue btn-big-font " id="ajax_report"/><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" />
	</div>
	<p class="jubao-tip" style="padding-left: 10px;">��ܰ��ʾ���ҷݹ��������ף�������ʵ�ٱ�Ŷ��</p>
</div>


<table width="100%" border="0" cellspacing="5" cellpadding="0" id="waiting"  style="display:none">
  <tr>
    <td align="center" height="60"><img src="<?php echo  $_CFG['site_template']?>images/30.gif"  border="0"/></td>
  </tr>
  <tr>
    <td align="center" >���Ժ�...</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall" id="app_ok" style="display:none">
    <tr>
		<td width="140" align="right"><img height="100" src="<?php echo  $_CFG['site_template']?>images/big-yes.png" /></td>
		<td>
			<strong style="font-size:14px ; color:#0066CC;margin-left:20px">�ٱ��ɹ�������Ա�����洦��!</strong>
		</td>
    </tr>
</table>

<table width="100%" border="0" cellspacing="5" cellpadding="0" id="error"  style="display:none">
  <tr>
    <td align="center" id="error_msg"></td>
  </tr>
</table>

<?php
}
elseif ($act=="app_save")
{
	$setsqlarr['content']=trim($_POST['content'])?trim($_POST['content']):exit("������");
	$setsqlarr['jobs_id']=$_POST['jobs_id']?intval($_POST['jobs_id']):exit("������");
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['addtime']=time();
	$setsqlarr['report_type']=intval($_POST['report_type']); // Ͷ������
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$setsqlarr['content']=utf8_to_gbk($setsqlarr['content']);
	}
	$jobsarr=app_get_jobs($setsqlarr['jobs_id']);
	if (empty($jobsarr))
	{
	exit("ְλ��ʧ");
	}
	else
	{
		$setsqlarr['jobs_name']=$jobsarr[0]['jobs_name'];
		$setsqlarr['jobs_addtime']=$jobsarr[0]['addtime'];
		$insert_id = $db->inserttable(table('report'),$setsqlarr,1);
	}
	if($insert_id)
	 {
	 exit("ok");
	 }
}

?>
