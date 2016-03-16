<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.date_format.php'); $this->register_modifier("date_format", "tpl_modifier_date_format",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:34 CST */ ?>
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/ui-dialog.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript">
$(document).ready(function()
{
	//会员中心顶部搜索职位简历切换 
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
	//会员中心顶部搜索职位简历切换 点击选择触发事件
	$("#usertopselectbox .selmenu").click(function(){

			var txt=$(this).text();
			
			if (txt=="简历")
			{
			$("#topsotype").val('2');
			$("#seltxt").text('简历');
			$("#selmenu").text('职位');
			$("#selmenu").hide();
			}
			else
			{
			$("#topsotype").val('1');
			$("#seltxt").text('职位');
			$("#selmenu").text('简历');
			$("#selmenu").hide();
			}
	});
	//所有提交按钮效果
	$("input[type='submit'],input[type='button']").hover(
	  function () {
	    $(this).addClass("hover");
	  },
	  function () {
	    $(this).removeClass("hover");
	  }
	);
	//所有多选按钮效果
	$(".input_checkbox,.input_checkbox_add").hover(
	  function () {
	    $(this).addClass("h");
	  },
	  function () {
	    $(this).removeClass("h");
	  }
	);
	//多选
	$('#chk').click(function(){
		$(this).parents("form").find("input[type=checkbox]").attr('checked',$("#chk").is(':checked'))
	});
	$('#chk2').click(function(){
		$(this).parents("form").find("input[type=checkbox]").attr('checked',$("#chk2").is(':checked'))
	});
	//信息列表背景变色
	$(".userliststyle").hover(
	  function () {
	    $(this).css('background-color','#FCFCFC');
	  },
	  function () {
	    $(this).css('background-color','#FFFFFF');
	  }
	);
	// 预览简历
	$("#reade_resume").click(function () {
		dialog({
	        title: '选择预览简历',
	        content: $("#previewbox"),
	        width:380
	    }).showModal();
	});
	// 第一次登录 刷新简历
	var personal_login_first="<?php echo $this->_vars['personal_login_first']; ?>
";
	if(personal_login_first)
	{
		dialog({
	        title: '刷新简历',
	        content: $(".first_refresh_box"),
	        width:380
	    }).showModal();
	    $(".first_refresh").live('click',function() 
	    {
	    	var pid = $(this).attr("pid");
			$.get("personal_ajax.php?act=refresh_resume&id="+pid,function(result){
				if(result=="1"){
					dialog({
					  title: '温馨提示',
					  content: "刷新成功",
					  width:'300px'
					}).showModal();
				}else{
					dialog({
					  title: '温馨提示',
					  content: ""+result+"",
					  width:'300px'
					}).showModal();
				}
			});
	    });
	}
	// 没有简历时弹窗提示创建简历
	$(".no-resume").click(function(event) {
		var mynrDialog=dialog(),
		url = $(this).attr("url");
		mynrDialog.title('系统提示');
		mynrDialog.content('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text close-tips-text">您还没有创建简历，建议您立即创建。</span></div></div><div class="center-btn-wrap"><input type="button" value="创建" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="取消" class="btn-65-30grey btn-big-font DialogClose" /></div>');
	    mynrDialog.width('320');
	    mynrDialog.showModal();
	    /* 关闭 */
	    $(".DialogClose").live('click',function() {
	      mynrDialog.close().remove();
	    });
	    // 确定
	    $(".DialogSubmit").click(function() {
	    	if (url) {window.location.href=url};
	    });
	});
});
</script>

<div class="meun1">
  	<div class="meun2">
  		<div class="meunbox">
			<div class="menu_top"><a href="personal_index.php">中心首页</a>&nbsp;&nbsp;<span>|</span>&nbsp;&nbsp;<?php if ($this->_vars['auditresume']): ?><a href="javascript:void(0)" id="reade_resume">预览简历</a><?php else: ?><a class="no-resume" url="personal_resume.php?act=make1" href="javascript:;">预览简历</a><?php endif; ?>
			</div>
			<div class="previewbox" id="previewbox" style="display:none;">
				<table width="90%" border="0" cellspacing="0" cellpadding="5" style="margin: 0 auto;padding-bottom: 10px;">
				<tr>
				<td bgcolor="#F3F7FC"><strong>简历名称</strong></td>
				<td width="130" align="center" bgcolor="#F3F7FC"><strong>刷新时间</strong></td>
				<td width="50" bgcolor="#F3F7FC"><strong>点击</strong></td>
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
次</td>
				</tr>
				<?php endforeach; endif; ?>
				</table>
			</div>
		</div>
		<div class="meunbox">
		  <div class="tit"><div class="t i1">简历管理</div></div>
		  	<div class="linktxt">
				<ul>
				<li<?php if ($_GET['act'] == "make1"): ?> class="h"<?php endif; ?>><a href="personal_resume.php?act=make1">创建简历</a></li>
				<li<?php if ($_GET['act'] == "resume_list"): ?> class="h"<?php endif; ?>><a href="personal_resume.php?act=resume_list">我的简历</a></li>
				<li<?php if ($_GET['act'] == "resume_outward"): ?> class="h"<?php endif; ?>><a href="personal_resume.php?act=resume_outward">简历外发</a></li>
				</ul>
			</div>
		</div>
		<div class="meunbox">
		  <div class="tit"><div class="t i2">求职管理</div></div>
		  	<div class="linktxt">
				<ul>
				<li<?php if ($_GET['act'] == "interview"): ?> class="h"<?php endif; ?>><a href="personal_apply.php?act=interview">面试邀请</a></li>
				<li<?php if ($_GET['act'] == "apply_jobs"): ?> class="h"<?php endif; ?>><a href="personal_apply.php?act=apply_jobs">已申请职位</a></li>
				<li<?php if ($_GET['act'] == "favorites"): ?> class="h"<?php endif; ?>><a href="personal_apply.php?act=favorites">职位收藏夹</a></li>
				<li<?php if ($_GET['act'] == "attention_me"): ?> class="h"<?php endif; ?>><a href="personal_apply.php?act=attention_me">谁在关注我</a></li>
				<li<?php if ($_GET['act'] == "my_attention"): ?> class="h"<?php endif; ?>><a href="personal_apply.php?act=my_attention">浏览过的职位</a></li>
				<li<?php if ($_GET['act'] == "outward"): ?> class="h"<?php endif; ?>><a href="personal_apply.php?act=outward">简历外发记录</a></li>
				</ul>
			</div>
		</div>
		<div class="meunbox">
			<div class="tit"><div class="t c4">评测系统</div></div>
			<div class="linktxt">
				<ul>
					<li<?php if ($_GET['act'] == "fortune" || $_GET['act'] == "fotrune_list" || $_GET['act'] == "fotrune_show"): ?> class="h"<?php endif; ?>><a href="personal_resume.php?act=fortune">周易性格评测</a></li>
				</ul>
			</div>
		</div>
		<div class="meunbox">
		  <div class="tit"><div class="t c4">会员服务</div></div>
		  	<div class="linktxt">
				<ul>
				<li <?php if ($_GET['act'] == "j_account"): ?>class="h"<?php endif; ?>><a href="personal_service.php?act=j_account">我的账户</a></li>
				<li <?php if ($_GET['act'] == "order_add" || $_GET['act'] == "order_list" || $_GET['act'] == "payment"): ?>class="h"<?php endif; ?>><a href="personal_service.php?act=order_add">充值订单</a></li>
				</ul>
			</div>
		</div>
		<div class="meunbox last">
		  <div class="tit"><div class="t i3">账号管理</div></div>
		  	<div class="linktxt">
				<ul>
				<li<?php if ($_GET['act'] == "userprofile"): ?> class="h"<?php endif; ?>><a href="personal_user.php?act=userprofile">基本资料</a></li>
				<li<?php if ($_GET['act'] == "authenticate"): ?> class="h"<?php endif; ?>><a href="personal_user.php?act=authenticate">账号安全</a></li>
				<li<?php if ($_GET['act'] == "pm"): ?> class="h"<?php endif; ?>><a href="personal_user.php?act=pm">我的消息</a></li>
				<li><a href="<?php echo $this->_run_modifier("QS_login", 'qishi_url', 'plugin', 1); ?>
?act=logout">安全退出</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- 第一次登录 刷新简历  -->
<div class="first_refresh_box" style="display:none;">
	<table width="90%" border="0" cellspacing="0" cellpadding="5" style="margin: 0 auto;padding-bottom: 10px;">
	<tr>
	<td bgcolor="#F3F7FC"><strong>简历名称</strong></td>
	<td width="130" align="center" bgcolor="#F3F7FC"><strong>刷新时间</strong></td>
	<td width="50" bgcolor="#F3F7FC"><strong>操作</strong></td>
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
">刷新</a></td>
	</tr>
	<?php endforeach; endif; ?>
	</table>
</div>