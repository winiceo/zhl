<?php
 /*
 * 74cms �鿴�γ���������ϵ��ʽ
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'downtype';
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if((empty($_SESSION['uid']) || empty($_SESSION['username']) || empty($_SESSION['utype'])) &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'] && $_COOKIE['QS']['uid'])
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	if(check_cookie($_COOKIE['QS']['uid'],$_COOKIE['QS']['username'],$_COOKIE['QS']['password']))
	{
	update_user_info($_COOKIE['QS']['uid'],false,false);
	//header("Location:".get_member_url($_SESSION['utype']));
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
if ($_SESSION['utype']!='4')
{
	exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td class="ajax_app">
					��������ѵ��Ա�ſ��Բ鿴��ϵ��ʽ��
				</td>
		    </tr>
		</table>');
}
		require_once(QISHI_ROOT_PATH.'include/fun_train.php');
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
$id=!empty($_GET['id'])?intval($_GET['id']):exit("������");
$applyshow=get_apply_one($id);
if ($applyshow['download_type']=='1') 
{
	$contact='<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
				    <tr>
						<td width="80" align="right">������</td>
						<td>
							'.$applyshow['realname'].'
						</td>
				    </tr>
					<tr>
						<td width="80" align="right">�绰��</td>
						<td>
							'.$applyshow['mobile'].'
						</td>
				    </tr>
					<tr>
						<td width="80" align="right">���䣺</td>
						<td>
							'.$applyshow['email'].'
						</td>
				    </tr>
				</table>';
	
	exit($contact);
}
if ($_CFG['operation_train_mode']=="2")
{
	$setmeal=get_user_setmeal($_SESSION['uid']);
	if (empty($setmeal) || ($setmeal['endtime']<time() && $setmeal['endtime']<>'0'))
	{
		$str="<a href=\"".get_member_url(4,true)."train_service.php?act=setmeal_list\">[�������]</a>";
		exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
				    <tr>
						<td width="20" align="right"></td>
						<td class="ajax_app">
							���ķ����ѵ��ڡ������� '.$str.'
						</td>
				    </tr>
				</table>');
	}
	elseif ( $setmeal['down_apply']<=0)
	{
		$str="<a href=\"".get_member_url(4,true)."train_service.php?act=setmeal_list\">[�������]</a>";
		exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
				    <tr>
						<td width="20" align="right"></td>
						<td class="ajax_app">
							��Ĳ鿴�γ���������ϵ��ʽ�����Ѿ����������ơ������� '.$str.'
						</td>
				    </tr>
				</table>');
	}
}
elseif($_CFG['operation_train_mode']=="1")
{
	$points_rule=get_cache('points_rule');
	$points=$points_rule['down_apply']['value'];
	$mypoints=get_user_points($_SESSION['uid']);
	if  ($mypoints<$points)
	{
		$str="<a href=\"".get_member_url(4,true)."train_service.php?act=order_add\">[��ֵ{$_CFG['train_points_byname']}]</a>&nbsp;&nbsp;&nbsp;&nbsp;";
		exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
				    <tr>
						<td width="20" align="right"></td>
						<td class="ajax_app">
							���'.$_CFG['train_points_byname'].'���㣬���ֵ�����ء� '.$str.'
						</td>
				    </tr>
				</table>');			
	}
}
if ($act=="downtype")
{
	if ($_CFG['operation_train_mode']=="2")
	{
			$tip="��ʾ����Ĳ鿴�γ���������ϵ��ʽ����<span> {$setmeal['down_apply']}</span>��";
			
	}else{
			$tip="�鿴��ϵ��ʽ���۳�<span> {$points} </span>{$_CFG['train_points_quantifier']}{$_CFG['train_points_byname']}����Ŀǰ����<span> {$mypoints} </span>{$_CFG['train_points_quantifier']}{$_CFG['train_points_byname']}";
	}
?>
<script type="text/javascript">
$(".but100").hover(function(){$(this).addClass("but100_hover")},function(){$(this).removeClass("but100_hover")});

$("#ajax_download_r").click(function() {
		var id="<?php echo $id?>";
		var tsTimeStamp= new Date().getTime();
			$("#ajax_download_r").val("������...");
			$("#ajax_download_r").attr("disabled","disabled");
			$("#ajax_download_r").hide();
			$("#waiting").show();
		$.get("<?php echo $_CFG['site_dir'] ?>user/user_apply_contact.php", { "id":id,"time":tsTimeStamp,"act":"download_save"},
	 	function (data,textStatus)
	 	 {
			if (data=="ok")
			{
			$(".ajax_download_tip").hide();
			$("#ajax_download_table").hide();
			$("#notice").hide();
			$("#waiting").hide();
					//ˢ����ϵ��ַ
					$.get("<?php echo $_CFG['site_dir'] ?>user/user_apply_contact.php", { "id": id,"time":tsTimeStamp,"act":"downtype"},
					function (data,textStatus)
					 {	
						$("#download_ok").html(data);
					 }
					);
			}
			else
			{
			alert(data);
			}
			$("#ajax_download_r").val("���ؼ���");
			$("#ajax_download_r").attr("disabled","");
	 	 })
});
function DialogClose()
{
	$("#FloatBg").hide();
	$("#FloatBox").hide();
}
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall" id="ajax_download_table">
    <tr>
		<td></td>
		<td align="center">
			<input type="button" name="Submit"  id="ajax_download_r" class="but130lan" value="�鿴��ϵ��ʽ" />
		</td>
    </tr>
</table>
<table id="notice" width="100%" border="0" style="border-top:1px #CCCCCC dotted;background-color: #EEEEEE; line-height: 230%;padding: 15px; margin-top: 10px; ">
    <tbody><tr>
	    <td class="ajax_download_tip" style="padding-left: 10px;"><?php echo $tip?>
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
<div id="download_ok"></div>
<?php
}
elseif ($act=="download_save")
{
	$ruser=get_user_info($applyshow['personal_uid']);
	if ($_CFG['operation_train_mode']=='2')
	{	
		if ($setmeal['down_apply']>0 && add_down_apply($id,$_SESSION['uid']))
		{
		action_user_setmeal($_SESSION['uid'],"down_apply");
		$setmeal=get_user_setmeal($_SESSION['uid']);
		write_memberslog($_SESSION['uid'],4,9102,$_SESSION['username'],"�鿴�� {$ruser['username']} �Ŀγ�������ϵ��ʽ,�����Բ鿴 {$setmeal['down_apply']} ���γ��������ϵ��ʽ");
		write_memberslog($_SESSION['uid'],4,8206,$_SESSION['username'],"�鿴�� {$ruser['username']} �Ŀγ�������ϵ��ʽ");
		
			$mailconfig=get_cache('mailconfig');					
			$sms=get_cache('sms_config');	
			$personuser=get_user_info($applyshow['personal_uid']);	
			
			if ($mailconfig['set_downapp']=='1'  && $personuser['email_audit']=='1' )
			{	
				dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_mail.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_downapp&coursename={$applyshow['course_name']}&trainname={$applyshow['train_name']}&email={$personuser['email']}");
			}
			//sms	
			if ($sms['open']=='1'  && $sms['set_downapp']=='1'  && $personuser['mobile_audit']=="1")
			{
				dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_sms.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_downapp&coursename={$applyshow['course_name']}&trainname={$applyshow['train_name']}&mobile={$personuser['mobile']}");
			}
					
		exit("ok");
		}
	}
	elseif($_CFG['operation_train_mode']=="1")
	{
				$points_rule=get_cache('points_rule');
				$points=$points_rule['down_apply']['value'];
				$ptype=$points_rule['down_apply']['type'];
				$mypoints=get_user_points($_SESSION['uid']);
				if  ($mypoints<$points)
				{
					exit("err");
				}
				if (add_down_apply($id,$_SESSION['uid']))
				{
					if ($points>0)
					{
					report_deal($_SESSION['uid'],$ptype,$points);
					$user_points=get_user_points($_SESSION['uid']);
					$operator=$ptype=="1"?"+":"-";
					write_memberslog($_SESSION['uid'],4,9101,$_SESSION['username'],"�鿴�� {$ruser['username']} �Ŀγ�������ϵ��ʽ({$operator}{$points}),(ʣ��:{$user_points})");
					write_memberslog($_SESSION['uid'],4,8206,$_SESSION['username'],"�鿴�� {$ruser['username']} �Ŀγ�������ϵ��ʽ");
					}
					
						$mailconfig=get_cache('mailconfig');					
						$sms=get_cache('sms_config');	
						$personuser=get_user_info($applyshow['personal_uid']);	
						if ($mailconfig['set_downapp']=='1'  && $personuser['email_audit']=='1' )
						{	
							dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_mail.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_downapp&coursename={$applyshow['course_name']}&trainname={$applyshow['train_name']}&email={$personuser['email']}");
						}
						//sms	
						if ($sms['open']=='1'  && $sms['set_downapp']=='1'  && $personuser['mobile_audit']=='1')
						{
							dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_sms.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_downapp&coursename={$applyshow['course_name']}&trainname={$applyshow['train_name']}&mobile={$personuser['mobile']}");
						}
						
					exit("ok");
				}
	}
}
?>