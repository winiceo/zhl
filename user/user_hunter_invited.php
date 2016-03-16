<?php
 /*
 * 74cms ��������
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'invited';
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
if ($_SESSION['utype']!='3')
{
	exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td class="ajax_app">
					��������ͷ��Ա�ſ����������ԣ�
				</td>
		    </tr>
		</table>');
}
require_once(QISHI_ROOT_PATH.'include/fun_hunter.php');
require_once(QISHI_ROOT_PATH.'include/fun_weixin.php');
$user=get_user_info($_SESSION['uid']);
$downresumeurl="<a href=\"".get_member_url(3,true)."hunter_recruitment.php?act=down_resume_list\">[�鿴�����صĸ߼�����]</a>";
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
$id=isset($_GET['id'])?intval($_GET['id']):exit("err");
$user_jobs=get_auditjobs($_SESSION['uid']);
if (count($user_jobs)==0)
{
	exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td class="ajax_app">
					����ʧ�ܣ���û�з�����Ƹ��Ϣ������Ϣû�����ͨ����
				</td>
		    </tr>
		</table>');
}
$setmeal=get_user_setmeal($_SESSION['uid']);
$resume=$db->getone("select * from ".table('resume')." WHERE id ='{$id}'  LIMIT 1");
$resume = array_map("addslashes", $resume);
if ($act=="invited")
{			
	if ($_CFG['operation_hunter_mode']=="2")
	{
		if (empty($setmeal) || ($setmeal['endtime']<time() && $setmeal['endtime']<>"0"))
		{
			$str="<a href=\"".get_member_url(3,true)."hunter_service.php?act=setmeal_list\">[�������]</a>";
			exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			    <tr>
					<td width="20" align="right"></td>
					<td class="ajax_app">
						���ķ����ѵ��ڡ������� '.$str.'
					</td>
			    </tr>
			</table>');
		}
		elseif ($resume['talent']=='2' && $setmeal['interview_senior']<=0)
		{
			$str="<a href=\"".get_member_url(3,true)."hunter_service.php?act=setmeal_list\">[�������]</a>";
			exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			    <tr>
					<td width="20" align="right"></td>
					<td class="ajax_app">
						������߼��˲����Դ����Ѿ����������ơ������� '.$str.'
					</td>
			    </tr>
			</table>');
		}
		elseif (($resume['talent']=='1' || $resume['talent']=='3') && $setmeal['interview_ordinary']<=0)
		{
			$str="<a href=\"".get_member_url(3,true)."hunter_service.php?act=setmeal_list\">[�������]</a>";
			exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			    <tr>
					<td width="20" align="right"></td>
					<td class="ajax_app">
						���������Դ����Ѿ����������ơ������� '.$str.'
					</td>
			    </tr>
			</table>');
		}
		if ($resume['talent']=='2')
		{	
			$tip="��ʾ��������������<span> {$setmeal['interview_senior']}</span> �θ߼��˲�����";
		}
		else
		{	
			$tip="��ʾ��������������<span> {$setmeal['interview_ordinary']}</span> ����ͨ�˲�����";
		}
	}
	elseif($_CFG['operation_hunter_mode']=="1")
	{
		$mypoints=get_user_points($_SESSION['uid']);
		$points_rule=get_cache('points_rule');
		$points=$resume['talent']=='2'?$points_rule['hunter_interview_invite_advanced']['value']:$points_rule['hunter_interview_invite']['value'];
		if  ($mypoints<$points)
		{
			$str="<a href=\"".get_member_url(3,true)."hunter_service.php?act=order_add\">[��ֵ{$_CFG['points_byname']}]</a>&nbsp;&nbsp;&nbsp;&nbsp;";
			exit("<table width='100%' border='0' cellspacing='0' cellpadding='0' class='tableall'>
			    <tr>
					<td width='20' align='right'></td>
					<td class='ajax_app'>
						���{$_CFG['points_byname']}���㣬���ֵ�����ء�".$str."
					</td>
			    </tr>
			</table>");
		}
		$tip="�������Խ��۳�<span> {$points} </span>{$_CFG['points_quantifier']}{$_CFG['points_byname']}����Ŀǰ����<span> {$mypoints}</span>{$_CFG['points_quantifier']}{$_CFG['points_byname']}";
	}
	//�鿴�Ƿ����ع���ݼ���
	$row = $db->getone("select * from ".table('hunter_down_resume')." where hunter_uid={$_SESSION['uid']} and resume_id = ".intval($_GET['id'])." limit 1");
	if(!$row)
	{
		if ($_CFG['operation_hunter_mode']=="2")
		{
			if ($resume['talent']=='2')
			{	
				$tip="��ʾ��������������<span> {$setmeal['download_resume_senior']}</span>�ݸ߼��˲ż���";
			}
			else
			{
				$tip="��ʾ��������������<span> {$setmeal['download_resume_ordinary']}</span>����ͨ�˲ż���";
			}
		}
		elseif($_CFG['operation_hunter_mode']=="1")
		{
			$points_rule=get_cache('points_rule');
			if ($resume['talent']=='2')
			{
				$points=$points_rule['hunter_resume_download_advanced']['value'];
			}
			else
			{
				$points=$points_rule['hunter_resume_download']['value'];
			}
			$mypoints=get_user_points($_SESSION['uid']);
			if  ($mypoints<$points)
			{
				$str="<a href=\"".get_member_url(3,true)."hunter_service.php?act=order_add\">[��ֵ{$_CFG['hunter_points_byname']}]</a>&nbsp;&nbsp;&nbsp;&nbsp;";
				exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
					    <tr>
						<td class="ajax_app" align="center">
							<b>������������˼����� �ſ��Զ����������� </b>
						</td>
					    </tr>
					    <tr>
						<td class="ajax_app" align="center">
							�������'.$_CFG['hunter_points_byname'].'���㣬���ֵ�����ء�'.$str.'
						</td>
					    </tr>
					     <tr>
				     		<td class="ajax_app" align="center">
						</td>
					    </tr>
					</table>');			
			}
			$tip="���ش˷ݼ������۳�<span> {$points}</span>{$_CFG['hunter_points_quantifier']}{$_CFG['hunter_points_byname']}����Ŀǰ����<span> {$mypoints}</span>{$_CFG['hunter_points_quantifier']}{$_CFG['hunter_points_byname']}";
		}
	
?>
<script type="text/javascript">
$(".but100").hover(function(){$(this).addClass("but100_hover")},function(){$(this).removeClass("but100_hover")});

$("#ajax_download_r").click(function() {
		var id="<?php echo $id?>";
		var tsTimeStamp= new Date().getTime();
			$("#ajax_download_r").val("������...");
			$("#ajax_download_r").attr("disabled","disabled");
 			 var pms_notice=$("#pms_notice").attr("checked");
			 if(pms_notice) pms_notice=1;else pms_notice=0;
		$.get("<?php echo $_CFG['site_dir'] ?>user/user_hunter_download_resume.php", { "id":id,"pms_notice":pms_notice,"time":tsTimeStamp,"act":"download_save"},
 	 	function (data,textStatus)
	 	 {	
			if (data=="ok")
			{
			$(".ajax_download_tip").hide();
			$("#ajax_download_table").hide();
			$("#notice").hide();
			$("#download_ok").show();			 
					$("#download_ok .closed").click(function(){
						DialogClose();
					});
					//ˢ����ϵ��ַ
					$.get("<?php echo $_CFG['site_dir'] ?>plus/ajax_contact.php", { "id": id,"time":tsTimeStamp,"act":"resume_contact"},
					function (data,textStatus)
					 {	
						$("#resume_contact").html(data);
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
	<tr style='width:100%; margin:0px auto;'>
			<div style='margin:0px auto;'><h3>����������ɼ����� �ſ�����������</h3></div>
    </tr>

    <tr>
		<td width="120" align="right">վ����֪ͨ�Է���</td>
		<td class="ajax_app">
			
			<label><input type="checkbox" name="pms_notice" id="pms_notice" value="1"  checked="checked"/>
		  վ����֪ͨ
		   </label>
		</td>
    </tr>
    <tr>
		<td></td>
		<td>
			<input type="button" name="Submit"  id="ajax_download_r" class="but130lan" value="���ؼ���" />
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
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall" id="download_ok" style="display:none">
    <tr>
		<td width="140" align="right"><img height="100" src="<?php echo  $_CFG['site_template']?>images/big-yes.png" /></td>
		<td>
			<strong style="font-size:14px ; color:#0066CC;margin-left:20px">���سɹ�!</strong>
			<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:50px;margin-left:20px"  class="dialog_closed">
			<?php echo $downresumeurl;?><br/>
			<a href="javascript:void(0)"  class="DialogClose underline" style="color:#0180cf;text-decoration:none;">�������</a>
			</div>
		</td>
    </tr>
</table>
		
			
			
<?php		
			//---------------------//
			die;
	}
?>
<!-- �������Ե��� -->
<div class="interview-dialog dialog-block">
	<div class="dialog-item clearfix">
		<div class="d-type f-left">���������</div>
		<div class="d-content f-left"><?php echo $resume['fullname']; ?></div>
	</div>
	<div class="dialog-item clearfix">
		<div class="d-type f-left">����ְλ��</div>
		<div class="data-filter d-content f-left">
			<div class="dropdown">
				<span class="dropdown-ctrl"><span class="jobtit">ѡ��ְλ</span><i class="drop-icon"></i></span>
				<ul class="dropdown-list">
					<?php 
					foreach ($user_jobs as $list)
					{
					?>
					<li><a href="javascript:;"id="<?php echo $list['id']?>" title="<?php echo $list['jobs_name']; ?>" class='dropdown-list-a'><?php echo $list['jobs_name']?></a></li>
					<?php
					}
					?>
				</ul>
			</div>
            <input type="hidden" name="jobsid" value="" id="jobsid">
		</div>
	</div>
	<div class="dialog-item clearfix">
		<div class="d-type f-left">�������ڣ�</div>
		<div class="d-content f-left">
			<span class="datepicker" style="padding: 0;"><input type="text" name="interview_time" id="interview_time" class="date-picker"/></span>
		</div>
	</div>
	<div class="dialog-item clearfix">
		<div class="d-type f-left">֪ͨ���ݣ�</div>
		<div class="d-content f-left">
			<textarea name="notes" id="notes" cols="30" rows="10" class="dialog-textarea"></textarea>
		</div>
	</div>
	<div class="dialog-item clearfix">
		<div class="d-type f-left">վ��֪ͨ��</div>
		<div class="d-content f-left">
			<div class="f-left"><label><input type="checkbox" name="pms_notice" id="pms_notice" value="1"  checked="checked"/>
		  վ����֪ͨ</label></div>
		</div>
	</div>
	<div class="dialog-item clearfix">
		<div class="d-type f-left">����֪ͨ��</div>
		<div class="d-content f-left">
			<div class="f-left"><label><input type="checkbox" name="sms_notice" id="sms_notice" class="checkbox" />��</label></div>
			<div class="short-text-tip f-left" >������<?php echo $user['sms_num'];?>������.</div>
		</div>
	</div>

	<div class="dialog-item clearfix">
		<div class="d-type f-left">&nbsp;</div>
		<div class="d-content f-left">
			<input type="button" value="����" class="btn-65-30blue btn-big-font DialogSubmit"/><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" />
		</div>
	</div>
</div>
<script type="text/javascript">
$('input.date-picker').datePicker();
/* ְλѡ�� */
$(".dropdown-ctrl").on('click',function() {
	$('.dropdown-list').slideDown('fast');
});
$('.dropdown-list a').on('click',function() {
	var id = $(this).attr("id");
	var title = $(this).attr("title");
	$("#jobsid").val(id);
	$(".jobtit").html(title);
	$('.dropdown-list').slideUp('fast');
});
</script>
<?php
}
 elseif ($act=="invited_save")
{
	$jobs_id=isset($_GET['jobs_id'])?intval($_GET['jobs_id']):exit("err");
	$notes=isset($_GET['notes'])?trim($_GET['notes']):"";
	$pms_notice=intval($_GET['pms_notice']);
	$interview_time=trim($_GET['interview_time']);
	if (check_interview($id,$jobs_id,$_SESSION['uid']))
	{
	exit("���ѶԸü������й���������,�����ظ����룡");
	}
	$hunter_profile = get_hunter(intval($_SESSION['uid']));
	$jobs=get_jobs_one($jobs_id);
	$jobs = array_map("addslashes", $jobs);
	$addarr['resume_id']=$resume['id'];
	$addarr['resume_addtime']=$resume['addtime'];
	if ($resume['display_name']=="2")
	{
	$addarr['resume_name']="N".str_pad($resume['id'],7,"0",STR_PAD_LEFT);	
	}
	elseif ($resume['display_name']=="3")
	{
		if($resume['sex']==1)
		{
			$addarr['resume_name']=cut_str($resume['fullname'],1,0,"����");	
		}
		elseif($resume['sex']==2)
		{
			$addarr['resume_name']=cut_str($resume['fullname'],1,0,"Ůʿ");
		}
	}
	else
	{
	$addarr['resume_name']=$resume['fullname'];
	}
	$addarr['resume_uid']=$resume['uid'];
	$addarr['hunter_id']=$hunter_profile['id'];
	$addarr['hunter_addtime']=$hunter_profile['addtime'];
	$addarr['hunter_name']=$hunter_profile['huntername'];
	$addarr['hunter_uid']=$_SESSION['uid'];
	$addarr['jobs_id']=$jobs['id'];
	$addarr['jobs_name']=$jobs['jobs_name'];
	$addarr['jobs_addtime']=$jobs['addtime'];	
	$addarr['notes']= $notes;
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
		$addarr['notes']=utf8_to_gbk($addarr['notes']);
	}
	$addarr['personal_look']= 1;
	$addarr['interview_addtime']=time();
	$addarr['interview_time']= $interview_time;
	$resume_user=get_user_info($resume['uid']);
	$resume_user = array_map("addslashes", $resume_user);
	if ($_CFG['operation_hunter_mode']=="2")
	{
		$db->inserttable(table('hunter_interview'),$addarr);
		if ($resume['talent']=='2')
		{
			action_user_setmeal($_SESSION['uid'],"interview_senior");
			$setmeal=get_user_setmeal($_SESSION['uid']);
			write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"������ {$resume_user['username']} ���ԣ�����������߼��˲� {$setmeal['interview_senior']} ��",2,1007,"����߼��˲�����","1","{$setmeal['interview_senior']}");
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"������ {$resume_user['username']} ����");
		}
		else
		{				 
			action_user_setmeal($_SESSION['uid'],"interview_ordinary");
			$setmeal=get_user_setmeal($_SESSION['uid']);
			write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"������ {$resume_user['username']} ���ԣ�������������ͨ�˲� {$setmeal['interview_ordinary']} ��",2,1006,"������ͨ�˲�����","1","{$setmeal['interview_ordinary']}");
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"������ {$resume_user['username']} ����");				
		}			
	}		 
	elseif($_CFG['operation_hunter_mode']=="1")
	{
		$mypoints=get_user_points($_SESSION['uid']);
		$points_rule=get_cache('points_rule');
		$points=$resume['talent']=='2'?$points_rule['interview_invite_advanced']['value']:$points_rule['interview_invite']['value'];
		$ptype=$resume['talent']=='2'?$points_rule['interview_invite_advanced']['type']:$points_rule['interview_invite']['type'];
		if  ($mypoints<$points)
		{
			exit("���Ļ��ֲ���,���ܾ����������ԣ�");
		}
		$db->inserttable(table('hunter_interview'),$addarr);
		if ($points>0)
		{
			report_deal($_SESSION['uid'],$ptype,$points);
			$user_points=get_user_points($_SESSION['uid']);
			$operator=$ptype=="1"?"+":"-";
			if($resume['talent']=='2'){
				write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"���� {$resume_user['username']} ����({$operator}{$points}),(ʣ��:{$user_points})",1,1007,"����߼��˲�����","{$operator}{$points}","{$user_points}");
			}else{
				write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"���� {$resume_user['username']} ����({$operator}{$points}),(ʣ��:{$user_points})",1,1006,"������ͨ�˲�����","{$operator}{$points}","{$user_points}");
			}
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"���� {$resume_user['username']} ����");
		}		
	}
	$mailconfig=get_cache('mailconfig');
	$weixinconfig=get_cache('weixin_config');
	$sms=get_cache('sms_config');
	if ($mailconfig['set_invite']=="1" && $resume['email_notify']=='1' && $resume_user['email_audit']=="1")
	{
		dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_invite&companyname={$jobs['companyname']}&email={$resume_user['email']}");				
	}
	//sms
	if ($sms['open']=="1"  && $sms['set_invite']=="1"  && $resume_user['mobile_audit']=="1")
	{
		$success = dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_invite&companyname={$jobs['companyname']}&mobile={$resume_user['mobile']}");	
		if($success == 'success'){
			reduce_user_sms($_SESSION['uid']);
		}
	}
	//վ����
	if($pms_notice=='1'){
		$jobs_url=url_rewrite('QS_jobsshow',array('id'=>$jobs['id']));
		$hunter_url=url_rewrite('QS_hunter_show',array('id'=>$jobs['hunter_id']),false);
		$message=$jobs['companyname']."�������μӹ�˾���ԣ�����ְλ��<a href=\"{$jobs_url}\" target=\"_blank\"> {$jobs['jobs_name']} </a>��<a href=\"{$hunter_url}\" target=\"_blank\">����鿴��˾����</a>";
		write_pmsnotice($resume['uid'],$resume_user['username'],$message);
	}
	//΢��
	set_invite($resume['uid'],$jobs['id'],$jobs['companyname'],$jobs['jobs_name'],$jobs['contact']['address'],$jobs['contact']['contact'],$jobs['contact']['telephone'],$notes);
	
	$html='<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall" id="invited_ok">
	    <tr>
			<td width="140" align="right"><img height="100" src="'.$_CFG['site_template'].'images/big-yes.png" /></td>
			<td>
				<strong style="font-size:14px ; color:#0066CC;margin-left:20px">����ɹ�!</strong>
				<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:50px;margin-left:20px"  class="dialog_closed">
				<a href="'.get_member_url(3,true).'hunter_recruitment.php?act=interview" style="color:#0180cf;text-decoration:none;" class="underline">�鿴�ҷ������������</a><br />
				<a href="javascript:void(0)"  class="DialogClose underline" style="color:#0180cf;text-decoration:none;">�������</a>
				</div>
			</td>
	    </tr>
	</table>';
	exit($html);
}
 
?>