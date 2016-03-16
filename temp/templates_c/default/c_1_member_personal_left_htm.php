<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.date_format.php'); $this->register_modifier("date_format", "tpl_modifier_date_format",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:34 CST */ ?>
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/ui-dialog.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript">
$(document).ready(function()
{
	//��Ա���Ķ�������ְλ�����л� 
	$("#usertopselectbox").hover(
	  function () {
	  $(this).find(".#selmenu").show();
	  },
	  function () {
	    $(this).find("#selmenu").hide();
	  }
	);
	$(".leftmenu .meunbox li[class!='h']").hover(
	  function () {
	      $(this).css("background-color","#F5F5F5");
	  },
	  function () {
	     $(this).css("background-color","#FFFFFF");
	  }
	);
	//��Ա���Ķ�������ְλ�����л� ���ѡ�񴥷��¼�
	$("#usertopselectbox .selmenu").click(function(){

			var txt=$(this).text();
			
			if (txt=="����")
			{
			$("#topsotype").val('2');
			$("#seltxt").text('����');
			$("#selmenu").text('ְλ');
			$("#selmenu").hide();
			}
			else
			{
			$("#topsotype").val('1');
			$("#seltxt").text('ְλ');
			$("#selmenu").text('����');
			$("#selmenu").hide();
			}
	});
	//�����ύ��ťЧ��
	$("input[type='submit'],input[type='button']").hover(
	  function () {
	    $(this).addClass("hover");
	  },
	  function () {
	    $(this).removeClass("hover");
	  }
	);
	//���ж�ѡ��ťЧ��
	$(".input_checkbox,.input_checkbox_add").hover(
	  function () {
	    $(this).addClass("h");
	  },
	  function () {
	    $(this).removeClass("h");
	  }
	);
	//��ѡ
	$('#chk').click(function(){
		$(this).parents("form").find("input[type=checkbox]").attr('checked',$("#chk").is(':checked'))
	});
	$('#chk2').click(function(){
		$(this).parents("form").find("input[type=checkbox]").attr('checked',$("#chk2").is(':checked'))
	});
	//��Ϣ�б�����ɫ
	$(".userliststyle").hover(
	  function () {
	    $(this).css('background-color','#FCFCFC');
	  },
	  function () {
	    $(this).css('background-color','#FFFFFF');
	  }
	);
	// Ԥ������
	$("#reade_resume").click(function () {
		dialog({
	        title: 'ѡ��Ԥ������',
	        content: $("#previewbox"),
	        width:380
	    }).showModal();
	});
	// ��һ�ε�¼ ˢ�¼���
	var personal_login_first="<?php echo $this->_vars['personal_login_first']; ?>
";
	if(personal_login_first)
	{
		dialog({
	        title: 'ˢ�¼���',
	        content: $(".first_refresh_box"),
	        width:380
	    }).showModal();
	    $(".first_refresh").live('click',function() 
	    {
	    	var pid = $(this).attr("pid");
			$.get("personal_ajax.php?act=refresh_resume&id="+pid,function(result){
				if(result=="1"){
					dialog({
					  title: '��ܰ��ʾ',
					  content: "ˢ�³ɹ�",
					  width:'300px'
					}).showModal();
				}else{
					dialog({
					  title: '��ܰ��ʾ',
					  content: ""+result+"",
					  width:'300px'
					}).showModal();
				}
			});
	    });
	}
	// û�м���ʱ������ʾ��������
	$(".no-resume").click(function(event) {
		var mynrDialog=dialog(),
		url = $(this).attr("url");
		mynrDialog.title('ϵͳ��ʾ');
		mynrDialog.content('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text close-tips-text">����û�д�������������������������</span></div></div><div class="center-btn-wrap"><input type="button" value="����" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
	    mynrDialog.width('320');
	    mynrDialog.showModal();
	    /* �ر� */
	    $(".DialogClose").live('click',function() {
	      mynrDialog.close().remove();
	    });
	    // ȷ��
	    $(".DialogSubmit").click(function() {
	    	if (url) {window.location.href=url};
	    });
	});
});
</script>

<div class="meun1">
  	<div class="meun2">
  		<div class="meunbox">
			<div class="menu_top"><a href="personal_index.php">������ҳ</a>&nbsp;&nbsp;<span>|</span>&nbsp;&nbsp;<?php if ($this->_vars['auditresume']): ?><a href="javascript:void(0)" id="reade_resume">Ԥ������</a><?php else: ?><a class="no-resume" url="personal_resume.php?act=make1" href="javascript:;">Ԥ������</a><?php endif; ?>
			</div>
			<div class="previewbox" id="previewbox" style="display:none;">
				<table width="90%" border="0" cellspacing="0" cellpadding="5" style="margin: 0 auto;padding-bottom: 10px;">
				<tr>
				<td bgcolor="#F3F7FC"><strong>��������</strong></td>
				<td width="130" align="center" bgcolor="#F3F7FC"><strong>ˢ��ʱ��</strong></td>
				<td width="50" bgcolor="#F3F7FC"><strong>���</strong></td>
				</tr>
				<?php if (count((array)$this->_vars['auditresume'])): foreach ((array)$this->_vars['auditresume'] as $this->_vars['list']): ?>
				<tr>
				<td class="us_list" title="<?php echo $this->_vars['list']['title_']; ?>
"><a class="co01" href="<?php echo $this->_vars['list']['resume_url']; ?>
" target="_blank"><?php echo $this->_vars['list']['title_']; ?>
</a></td>
				<td align="center" class="us_list"><?php echo $this->_run_modifier($this->_vars['list']['refreshtime'], 'date_format', 'plugin', 1, "%m/%d %H:%M"); ?>
</td>
				<td class="us_list"><?php echo $this->_vars['list']['click']; ?>
��</td>
				</tr>
				<?php endforeach; endif; ?>
				</table>
			</div>
		</div>
		<div class="meunbox">
		  <div class="tit"><div class="t i1">��������</div></div>
		  	<div class="linktxt">
				<ul>
				<li<?php if ($_GET['act'] == "make1"): ?> class="h"<?php endif; ?>><a href="personal_resume.php?act=make1">��������</a></li>
				<li<?php if ($_GET['act'] == "resume_list"): ?> class="h"<?php endif; ?>><a href="personal_resume.php?act=resume_list">�ҵļ���</a></li>
				<li<?php if ($_GET['act'] == "resume_outward"): ?> class="h"<?php endif; ?>><a href="personal_resume.php?act=resume_outward">�����ⷢ</a></li>
				</ul>
			</div>
		</div>
		<div class="meunbox">
		  <div class="tit"><div class="t i2">��ְ����</div></div>
		  	<div class="linktxt">
				<ul>
				<li<?php if ($_GET['act'] == "interview"): ?> class="h"<?php endif; ?>><a href="personal_apply.php?act=interview">��������</a></li>
				<li<?php if ($_GET['act'] == "apply_jobs"): ?> class="h"<?php endif; ?>><a href="personal_apply.php?act=apply_jobs">������ְλ</a></li>
				<li<?php if ($_GET['act'] == "favorites"): ?> class="h"<?php endif; ?>><a href="personal_apply.php?act=favorites">ְλ�ղؼ�</a></li>
				<li<?php if ($_GET['act'] == "attention_me"): ?> class="h"<?php endif; ?>><a href="personal_apply.php?act=attention_me">˭�ڹ�ע��</a></li>
				<li<?php if ($_GET['act'] == "my_attention"): ?> class="h"<?php endif; ?>><a href="personal_apply.php?act=my_attention">�������ְλ</a></li>
				<li<?php if ($_GET['act'] == "outward"): ?> class="h"<?php endif; ?>><a href="personal_apply.php?act=outward">�����ⷢ��¼</a></li>
				</ul>
			</div>
		</div>
		<div class="meunbox">
			<div class="tit"><div class="t c4">����ϵͳ</div></div>
			<div class="linktxt">
				<ul>
					<li<?php if ($_GET['act'] == "fortune" || $_GET['act'] == "fotrune_list" || $_GET['act'] == "fotrune_show"): ?> class="h"<?php endif; ?>><a href="personal_resume.php?act=fortune">�����Ը�����</a></li>
				</ul>
			</div>
		</div>
		<div class="meunbox">
		  <div class="tit"><div class="t c4">��Ա����</div></div>
		  	<div class="linktxt">
				<ul>
				<li <?php if ($_GET['act'] == "j_account"): ?>class="h"<?php endif; ?>><a href="personal_service.php?act=j_account">�ҵ��˻�</a></li>
				<li <?php if ($_GET['act'] == "order_add" || $_GET['act'] == "order_list" || $_GET['act'] == "payment"): ?>class="h"<?php endif; ?>><a href="personal_service.php?act=order_add">��ֵ����</a></li>
				</ul>
			</div>
		</div>
		<div class="meunbox last">
		  <div class="tit"><div class="t i3">�˺Ź���</div></div>
		  	<div class="linktxt">
				<ul>
				<li<?php if ($_GET['act'] == "userprofile"): ?> class="h"<?php endif; ?>><a href="personal_user.php?act=userprofile">��������</a></li>
				<li<?php if ($_GET['act'] == "authenticate"): ?> class="h"<?php endif; ?>><a href="personal_user.php?act=authenticate">�˺Ű�ȫ</a></li>
				<li<?php if ($_GET['act'] == "pm"): ?> class="h"<?php endif; ?>><a href="personal_user.php?act=pm">�ҵ���Ϣ</a></li>
				<li><a href="<?php echo $this->_run_modifier("QS_login", 'qishi_url', 'plugin', 1); ?>
?act=logout">��ȫ�˳�</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- ��һ�ε�¼ ˢ�¼���  -->
<div class="first_refresh_box" style="display:none;">
	<table width="90%" border="0" cellspacing="0" cellpadding="5" style="margin: 0 auto;padding-bottom: 10px;">
	<tr>
	<td bgcolor="#F3F7FC"><strong>��������</strong></td>
	<td width="130" align="center" bgcolor="#F3F7FC"><strong>ˢ��ʱ��</strong></td>
	<td width="50" bgcolor="#F3F7FC"><strong>����</strong></td>
	</tr>
	<?php if (count((array)$this->_vars['auditresume'])): foreach ((array)$this->_vars['auditresume'] as $this->_vars['list']): ?>
	<tr>
	<td class="us_list" title="<?php echo $this->_vars['list']['title_']; ?>
"><a class="co01" href="<?php echo $this->_vars['list']['resume_url']; ?>
" target="_blank"><?php echo $this->_vars['list']['title_']; ?>
</a></td>
	<td align="center" class="us_list"><?php echo $this->_run_modifier($this->_vars['list']['refreshtime'], 'date_format', 'plugin', 1, "%m/%d %H:%M"); ?>
</td>
	<td class="us_list"><a class="co01 first_refresh" href="javascript:;" pid="<?php echo $this->_vars['list']['id']; ?>
">ˢ��</a></td>
	</tr>
	<?php endforeach; endif; ?>
	</table>
</div>