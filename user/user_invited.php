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
if ($_SESSION['utype']!='1')
{
	exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td class="ajax_app">
					��������ҵ��Ա�ſ����������ԣ�
				</td>
		    </tr>
		</table>');
}
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
require_once(QISHI_ROOT_PATH.'include/fun_weixin.php');
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

if ($_CFG['operation_mode']=="3")
{
 	if ($_CFG['setmeal_to_points']=="1")
	{
		if (empty($setmeal) || ($setmeal['endtime']<time() && $setmeal['endtime']<>"0"))
		{
		$_CFG['operation_mode']="1";
		}
		else
		{
		$_CFG['operation_mode']="2";
		}
	}else{
		$_CFG['operation_mode']="2";
	}
}
if ($_CFG['operation_mode']=="2")
{
 			if (empty($setmeal) || ($setmeal['endtime']<time() && $setmeal['endtime']<>"0"))
			{
				$str="<a href=\"".get_member_url(1,true)."company_service.php?act=setmeal_list\">[�������]</a>";
				exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
				    <tr>
						<td width="20" align="right"></td>
						<td class="ajax_app">
							���ķ����ѵ��ڡ������� '.$str.'
						</td>
				    </tr>
				</table>');
			}
}
if ($act=="invited")
{			
	if ($_CFG['operation_mode']=="2")
	{
	}
	elseif($_CFG['operation_mode']=="1")
	{
				$mypoints=get_user_points($_SESSION['uid']);
				$points_rule=get_cache('points_rule');
				$points=$resume['talent']=='2'?$points_rule['interview_invite_advanced']['value']:$points_rule['interview_invite']['value'];
				if  ($mypoints<$points)
				{
					$str="<a href=\"".get_member_url(1,true)."company_service.php?act=order_add\">[��ֵ{$_CFG['points_byname']}]</a>&nbsp;&nbsp;&nbsp;&nbsp;";
					$str1="<a href=\"".get_member_url(1,true)."company_service.php?act=setmeal_list\">[�������]</a>";
					if (!empty($setmeal) && $_CFG['setmeal_to_points']=="1")
					{
						exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
						    <tr>
								<td width="20" align="right"></td>
								<td class="ajax_app">
									��ķ����ѵ��ڻ򳬳����������������� '.$str.$str1.'
								</td>
						    </tr>
						</table>');
					}
					else
					{
						exit("<table width='100%' border='0' cellspacing='0' cellpadding='0' class='tableall'>
						    <tr>
								<td width='20' align='right'></td>
								<td class='ajax_app'>
									���{$_CFG['points_byname']}���㣬���ֵ�����ء�".$str."
								</td>
						    </tr>
						</table>");
					}
				}
				$tip="�������Խ��۳�<span> {$points} </span>{$_CFG['points_quantifier']}{$_CFG['points_byname']}����Ŀǰ����<span> {$mypoints}</span>{$_CFG['points_quantifier']}{$_CFG['points_byname']}";
	}
	/* ����Ƿ����ع��ļ��� ����������� ְλ */
	$row = $db->getone("select * from ".table('company_down_resume')." where company_uid={$_SESSION['uid']} and resume_id = ".intval($_GET['id'])." limit 1");
	$row_apply=$db->getone("select * from ".table('personal_jobs_apply')." where company_uid={$_SESSION['uid']} and resume_id = ".intval($_GET['id'])." limit 1");
	if(!$row && !$row_apply)
	{	
		if ($_CFG['operation_mode']=="2")
		{
			if ($resumeshow['talent']=='2')
			{	
				$tip="��ʾ��������������<span> {$setmeal['download_resume_senior']}</span>�ݸ߼��˲ż���";
			}
			else
			{	
				$tip="��ʾ��������������<span> {$setmeal['download_resume_ordinary']}</span>����ͨ�˲ż���";
			}
		}	
		elseif($_CFG['operation_mode']=="1")
		{
			$points_rule=get_cache('points_rule');
			$points=$resumeshow['talent']=='2'?$points_rule['resume_download_advanced']['value']:$points_rule['resume_download']['value'];
			$mypoints=get_user_points($_SESSION['uid']);
			if  ($mypoints<$points)
			{
				$str="<a href=\"".get_member_url(1,true)."company_service.php?act=order_add\">[��ֵ{$_CFG['points_byname']}]</a>&nbsp;&nbsp;&nbsp;&nbsp;";
				$str1="<a href=\"".get_member_url(1,true)."company_service.php?act=setmeal_list\">[�������]</a>";
				if (!empty($setmeal) && $_CFG['setmeal_to_points']=="1")
				{
					exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
					    <tr>
							<td width="20" align="right"></td>
							<td class="ajax_app">
								��ķ����ѵ��ڻ򳬳����������������� '.$str.$str1.'
							</td>
					    </tr>
					</table>');
				}
				else
				{
					exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
					    <tr>
							<td width="20" align="right"></td>
							<td class="ajax_app">
								���'.$_CFG['points_byname'].' ���㣬���ֵ�����ء�'.$str.'
							</td>
					    </tr>
					</table>');
				}			
			}
			$tip="���ش˷ݼ������۳�<span> {$points}</span>{$_CFG['points_quantifier']}{$_CFG['points_byname']}����Ŀǰ����<span> {$mypoints}</span>{$_CFG['points_quantifier']}{$_CFG['points_byname']}";
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
		$.get("<?php echo $_CFG['site_dir'] ?>user/user_download_resume.php", { "id":id,"pms_notice":pms_notice,"time":tsTimeStamp,"act":"download_save"},
 	 	function (data,textStatus)
	 	 {	
			if (data=="ok")
			{
				$(".ajax_download_tip").hide();
				$("#ajax_download_table").hide();
				$("#notice").hide();
				$("#download_ok").show();
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
			<?php
				if($_SESSION['utype']==1){
			?>
			<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:50px;margin-left:20px"  class="dialog_closed">
			<a href="<?php echo get_member_url(1,true)?>company_recruitment.php?act=down_resume_list" style="color:#0180cf;text-decoration:none;" class="underline">�鿴�����ؼ���</a><br />
			<?php
				}else{
			?>
			<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:50px;margin-left:20px"  class="dialog_closed">
			<?php echo $downresumeurl;?><br />
			<?php
				}
			?>
			<a href="javascript:void(0)"  class="DialogClose underline" style="color:#0180cf;text-decoration:none;">�������</a>
			</div>
		</td>
    </tr>
</table>	
<?php		
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
			<div class="f-left"><label><input <?php if(intval($user['sms_num']) == 0) echo "disabled";?> type="checkbox" name="sms_notice" id="sms_notice" class="checkbox" />��</label></div>
			<?php if($_CFG['company_sms']==1) {?><div class="short-text-tip f-left" >���Ŀ��ö�������Ϊ <span style="color:#ff9900;"><?php echo $user['sms_num'];?></span> �� <a target="_blank" href="../user/company/company_sms.php?act=sms_order" class="underline">��������</a></div><?php } ?>
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
	$('.dropdown-list').slideToggle('fast');
});
// ����հ״�ְλ�б���������
$(document).bind('click', function(e) {
	var target = $(e.target);
	if (target.closest('.dropdown-ctrl').length == 0) {
		$(".dropdown-list").slideUp('fast');
	}
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
	$sms_notice=intval($_GET['sms_notice']);
	$interview_time=trim($_GET['interview_time']);
	if (check_interview($id,$jobs_id,$_SESSION['uid']))
	{
	exit("���ѶԸü������й���������,�����ظ����룡");
	}
	$jobs=get_jobs_one($jobs_id);
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
	$addarr['company_id']=$jobs['company_id'];
	$addarr['company_addtime']=$jobs['company_addtime'];
	$addarr['company_name']=$jobs['companyname'];
	$addarr['company_uid']=$_SESSION['uid'];
	$addarr['jobs_id']=$jobs['id'];
	$addarr['jobs_name']=$jobs['jobs_name'];
	$addarr['jobs_addtime']=$jobs['addtime'];	
	$addarr['notes']= $notes;
	$addarr['interview_addtime']= time();
	$addarr['interview_time']= $interview_time;
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
		$addarr['notes']=iconv("utf-8", "gbk",$addarr['notes']);
	}
	$addarr['personal_look']= 1;
	$resume_user=get_user_info($resume['uid']);
	if ($_CFG['operation_mode']=="2")
	{
		//�ж��ײ��Ƿ���������
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if ($resume['talent']=='2')
		{
			if(intval($setmeal['interview_senior']) > 0)
			{
				$db->inserttable(table('company_interview'),$addarr);
				action_user_setmeal($_SESSION['uid'],"interview_senior");
				$setmeal=get_user_setmeal($_SESSION['uid']);
				write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"������ {$resume_user['username']} ���ԣ�����������߼��˲� {$setmeal['interview_senior']} ��",2,1007,"����߼��˲�����","1","{$setmeal['interview_senior']}");
				write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"������ {$resume_user['username']} ����");
			}
			else
			{
				exit("�����ײͳ�����������,���ܽ����������ԣ�");
			}
		}
		else
		{		
			if(intval($setmeal['interview_ordinary']) > 0)
			{		 
				$db->inserttable(table('company_interview'),$addarr);
				action_user_setmeal($_SESSION['uid'],"interview_ordinary");
				$setmeal=get_user_setmeal($_SESSION['uid']);
				write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"������ {$resume_user['username']} ���ԣ�������������ͨ�˲� {$setmeal['interview_ordinary']} ��",2,1006,"������ͨ�˲�����","1","{$setmeal['interview_ordinary']}");
				write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"������ {$resume_user['username']} ����");	
			}
			else
			{
				exit("�����ײͳ�����������,���ܽ����������ԣ�");
			}			
		}
	}		 
	elseif($_CFG['operation_mode']=="1")
	{
		$mypoints=get_user_points($_SESSION['uid']);
		$points_rule=get_cache('points_rule');
		$points=$resume['talent']=='2'?$points_rule['interview_invite_advanced']['value']:$points_rule['interview_invite']['value'];
		$ptype=$resumeshow['talent']=='2'?$points_rule['interview_invite_advanced']['type']:$points_rule['interview_invite']['type'];
		if  ($mypoints<$points)
		{
			exit("���ĺ�«�Ҳ���,���ܽ����������ԣ�");
		}
		$db->inserttable(table('company_interview'),$addarr);
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
	/*
		���Ͷ�����ʾ ���� 
	*/
	$sms=get_cache('sms_config');
	if($sms['open']=="1" && $sms['set_invite']=="1" && $sms_notice=="1")
	{
		$user=get_user_info($_SESSION['uid']);
		if($_CFG['company_sms']==1 && $user['sms_num']>0)
		{
			$success = dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_invite&companyname={$jobs['companyname']}&mobile={$resume['telephone']}");	
			if($success=="success")
			{
				reduce_user_sms($_SESSION['uid']);
			}
		}
		else
		{
			dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_invite&companyname={$jobs['companyname']}&mobile={$resume['telephone']}");	
		}
	}
	//վ����
	if($pms_notice=='1'){
		$jobs_url=url_rewrite('QS_jobsshow',array('id'=>$jobs['id']));
		$company_url=url_rewrite('QS_companyshow',array('id'=>$jobs['company_id']),false);
		$message=$jobs['companyname']."�������μӹ�˾���ԣ�����ְλ��<a href=\"{$jobs_url}\" target=\"_blank\"> {$jobs['jobs_name']} </a>��<a href=\"{$company_url}\" target=\"_blank\">����鿴��˾����</a>";
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
				<a href="'.get_member_url(1,true).'company_recruitment.php?act=interview_list" style="color:#0180cf;text-decoration:none;" class="underline">�鿴�ҷ������������</a><br />
				<a href="javascript:void(0)"  class="DialogClose underline" style="color:#0180cf;text-decoration:none;">�������</a>
				</div>
			</td>
	    </tr>
	</table>';
	exit($html);
}
?>