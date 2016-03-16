<?php
 /*
 * 74cms ����γ�
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
				<td class="ajax_app">
					�����Ǹ��˻�Ա�ſ�������γ̣�
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
				<td class="ajax_app">
					�����˺Ŵ�����ͣ״̬������ϵ����Ա��Ϊ��������в�����
				</td>
		    </tr>
		</table>');
}
if ($act=="app")
{		
		$id=isset($_GET['id'])?$_GET['id']:exit("id ��ʧ");
		$course=app_get_course($id);
		if (empty($course))
		{
			exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			    <tr>
					<td width="20" align="right"></td>
					<td class="ajax_app">
						����γ�ʧ�ܣ�
					</td>
			    </tr>
			</table>');
		}
?>
<script type="text/javascript">
$(".but80").hover(function(){$(this).addClass("but80_hover")},function(){$(this).removeClass("but80_hover")});
//���������������
var app_max="30";
var app_today="<?php echo get_now_applycour_num($_SESSION['uid']) ?>";
$(".ajax_app_tip > span:eq(0)").html(app_max);
$(".ajax_app_tip > span:eq(1)").html(app_today);
$(".ajax_app_tip > span:eq(2)").html(app_max-app_today);
//��֤
$("#ajax_app").click(function() {
	if (app_max-app_today==0 || app_max-app_today<0 )
	{
	alert("����������γ������Ѿ�����������ƣ�");
	}
	else if ($("#realname").val()=="")
	{
	alert("����д������");
	}
	else if ($("#mobile").val()=="")
	{
	alert("����д��ϵ�绰��");
	}
	else if ($("#email").val()=="")
	{
	alert("����д��ϵ���䣡");
	}
	else
	{
		$("#app").hide();
		$("#notice").hide();
		$("#waiting").show();
		var tsTimeStamp= new Date().getTime();
			 var courseid=$("#courseid").val();
			 var uid="<?php echo $_SESSION['uid'];?>;"
		$.post("<?php echo $_CFG['site_dir'] ?>user/user_apply_course.php", { "uid": uid,"courseid": courseid,"notes": $("#notes").val(),"realname": $("#realname").val(),"mobile": $("#mobile").val(),"email":$("#email").val(),"time":tsTimeStamp,"act":"app_save"},
	 	function (data,textStatus)
	 	 {
			if (data=="ok")
			{
				$("#app").hide();
				$("#notice").hide();
				$("#waiting").hide();
				$("#app_ok").show();
					$("#app_ok .closed").click(function(){
					});
			}
			else if(data=="repeat")
			{
				$("#app").show();
				$("#notice").show();
				$("#waiting").hide();
				$("#app_ok").hide();
				alert("��������˿γ̣������ظ�����");
			}
			else
			{
				$("#app").show();
				$("#notice").show();
				$("#waiting").hide();
				$("#app_ok").hide();
				alert("����ʧ�ܣ�"+data);
			}
	 	 })
	}
});
function DialogClose()
{
	$("#FloatBg").hide();
	$("#FloatBox").hide();
}
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall" id="app">
    <tr>
		<td width="120" align="right">Ҫ����Ŀγ̣�</td>
		<td class="ajax_app">
			<ul>
			 <li><label><input name="courseid" id="courseid" type="hidden" value="<?php echo $course['id']?>" /><?php echo $course['course_name']?></label>
			 </li>
			 </ul>
		</td>
    </tr>
    <tr>
		<td width="120" align="right">��ʵ������</td>
		<td>
			<input name="realname" id="realname"  class="input_text_150" maxlength="30"/>
		</td>
    </tr>
     <tr>
		<td width="120" align="right">��ϵ�绰��</td>
		<td>
			<input name="mobile" id="mobile"  class="input_text_150" maxlength="15"/>
		</td>
    </tr>
     <tr>
		<td width="120" align="right">���䣺</td>
		<td>
			<input name="email" id="email" class="input_text_150" maxlength="40"/>
		</td>
    </tr>
    <tr>
		<td align="right">����˵����</td>
		<td>
			<textarea name="notes" id="notes"  style="width:300px; height:60px; line-height:180%; font-size:12px;"></textarea>
		</td>
    </tr>
    <tr>
		<td></td>
		<td>
			<input type="button" name="Submit"  id="ajax_app" class="but130lan" value="����" />
		</td>
    </tr>
</table>
<table id="notice" width="100%" border="0" style="border-top:1px #CCCCCC dotted;background-color: #EEEEEE; line-height: 230%;padding: 15px; margin-top: 10px; ">
    <tbody><tr>
	    <td class="ajax_app_tip" style="padding-left: 10px;">��ÿ���������<span></span>�ſγ̣������Ѿ�������<span></span>�ţ�����������<span></span>��
	    </td>
    </tr>
</tbody>
</table>
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
			<strong style="font-size:14px ; color:#0066CC;margin-left:20px">����ɹ�!</strong>
			<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:50px;margin-left:20px"  class="dialog_closed">
			<a href="javascript:void(0)"  class="DialogClose underline" style="color:#0180cf;text-decoration:none;">�������</a>
			</div>
		</td>
    </tr>
</table>
<?php
}
elseif ($act=="app_save")
{
	$courseid=isset($_POST['courseid'])?$_POST['courseid']:exit("������");
	$uid=isset($_POST['uid'])?intval($_POST['uid']):exit("������");
	$realname=isset($_POST['realname'])?trim($_POST['realname']):exit("������");
	$mobile=isset($_POST['mobile'])?trim($_POST['mobile']):exit("������");
	$email=isset($_POST['email'])?trim($_POST['email']):exit("������");
	if (!preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$email)) exit('�����ʽ����ȷ');
	$notes=isset($_POST['notes'])?trim($_POST['notes']):"";
	$course=app_get_course($courseid);
	$course=array_map("addslashes",$course);
	if (empty($course))
	{
	exit("�γ̶�ʧ");
	}
 		if (check_course_apply($course['id'],$_SESSION['uid']))
			{
			exit('repeat');
			}
			$addarr['realname']=$realname;
			$addarr['email']=$email;
			$addarr['mobile']=$mobile;
			$addarr['personal_uid']=intval($_SESSION['uid']);
			$addarr['course_id']=$course['id'];
			$addarr['course_name']=$course['course_name'];
			$addarr['train_id']=$course['train_id'];
			$addarr['train_name']=$course['trainname'];
			$addarr['train_uid']=$course['uid'];
			$addarr['notes']= $notes;
			if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
			{
			$addarr['notes']=utf8_to_gbk($addarr['notes']);
			$addarr['realname']=utf8_to_gbk($addarr['realname']);
			$addarr['mobile']=utf8_to_gbk($addarr['mobile']);
			}
			$addarr['apply_addtime']=time();
			$addarr['personal_look']=1;
			if ($db->inserttable(table('personal_course_apply'),$addarr))
			{
					$mailconfig=get_cache('mailconfig');					
					$course['contact']=$db->getone("select notify  from ".table('course_contact')." where pid='{$course['id']}' LIMIT 1 ");
					$sms=get_cache('sms_config');	
					$trainuser=get_user_info($course['uid']);	
					if ($mailconfig['set_applycou']=='1'  && $trainuser['email_audit']=='1' && $course['contact']['notify']=='1')
					{	
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_applycou&course_id={$course['id']}&coursename={$course['course_name']}&personal_fullname={$realname}&email={$trainuser['email']}");
					}
					//sms	
					if ($sms['open']=="1"  && $sms['set_applycou']=="1"  && $trainuser['mobile_audit']=="1")
					{
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_applycou&course_id={$course['id']}&coursename={$course['course_name']}&personal_fullname={$realname}&mobile={$trainuser['mobile']}");
					}
					write_memberslog($_SESSION['uid'],2,s,$_SESSION['username'],"����γ̣��γ�:{$course['course_name']}");
			}
	 exit("ok");
}
?>
