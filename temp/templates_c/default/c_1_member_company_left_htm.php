<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:35 CST */ ?>
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
//ȫѡ
$('#chk').click(function(){$("#form1 :checkbox").attr('checked',$("#chk").is(':checked'))});
$('#chk2').click(function(){$("#form1 :checkbox").attr('checked',$("#chk2").is(':checked'))});
});
// �Ҳ�Ư��
$(function(){
	$(".guwen").click(function(){
		$(this).hide();
		$(".guwen_open").show();
	});
	$(".guwen_open .close").click(function(){
		$(".guwen_open").hide();
		$(".guwen").show();
	})
})
$(document).ready(function() {
	$(".guwen").click(function(){
		$(this).hide();
		$(".guwen_open").show();
	});
	$(".guwen_close").click(function(){
		$(".guwen_open").hide();
		$(".guwen").show();
	});
});
</script>
<div class="meun1">
  		<div class="meun2">
  		<div class="meunbox">
			<div class="menu_top"><a href="company_index.php">������ҳ</a>&nbsp;&nbsp;<span>|</span>&nbsp;&nbsp;<?php if ($this->_vars['company_url']): ?><a target="_black" href="<?php echo $this->_vars['company_url']; ?>
">Ԥ����ҵ</a><?php else: ?><a href="company_info.php?act=company_profile" onClick="return confirm('�޷�Ԥ��,��û��������ҵ����,����ȥ������ҵ���ϣ�')">Ԥ����ҵ</a><?php endif; ?>
			</div>
		</div>
		<div class="meunbox">
		  <div class="tit"><div class="t c1">ְλ����</div></div>
		  	<div class="linktxt">
				<ul>
				<li <?php if ($_GET['act'] == "addjobs"): ?>class="h"<?php endif; ?>><a href="company_jobs.php?act=addjobs">����ְλ</a></li>
				<li <?php if ($_GET['act'] == "jobs" || $_GET['act'] == "editjobs"): ?>class="h"<?php endif; ?>><a href="company_jobs.php?act=jobs">����ְλ</a></li>
				<li <?php if ($_GET['act'] == "simple_jobs" || $_GET['act'] == "data_statistics"): ?>class="h"<?php endif; ?>><a href="company_jobs.php?act=simple_jobs">΢����Ƹ</a></li>
				</ul>
			</div>
		</div>
		
		<div class="meunbox">
		  <div class="tit"><div class="t c2">��������</div></div>
		  	<div class="linktxt">
				<ul>
				<li <?php if ($_GET['act'] == "interview_list"): ?>class="h"<?php endif; ?>><a href="company_recruitment.php?act=interview_list">��������</a></li>
				<li <?php if ($_GET['act'] == "apply_jobs"): ?>class="h"<?php endif; ?>><a href="company_recruitment.php?act=apply_jobs">�յ��ļ���</a></li>
				<li <?php if ($_GET['act'] == "down_resume_list"): ?>class="h"<?php endif; ?>><a href="company_recruitment.php?act=down_resume_list">�����صļ���</a></li>
				<li <?php if ($_GET['act'] == "favorites_list"): ?>class="h"<?php endif; ?>><a href="company_recruitment.php?act=favorites_list">�ղصļ���</a></li>
				<li <?php if ($_GET['act'] == "my_attention"): ?>class="h"<?php endif; ?>><a href="company_recruitment.php?act=my_attention">������ļ���</a></li>
				<li <?php if ($_GET['act'] == "view_jobs_log"): ?>class="h"<?php endif; ?>><a href="company_recruitment.php?act=view_jobs_log">˭�����ҵ�ְλ</a></li>
                <li <?php if ($_GET['act'] == "upload" || $_GET['act'] == "upload_list" || $_GET['act'] == "cheking_resume" || $_GET['act'] == "check_resume_detail"): ?>class="h"<?php endif; ?>><a href="company_upload.php?act=upload">�����û�</a></li>

                </ul>
			</div>
		</div>
			<div class="meunbox">
				<div class="tit"><div class="t c4">����ϵͳ</div></div>
				<div class="linktxt">
					<ul>
						<li<?php if ($_GET['act'] == "fortune" || $_GET['act'] == "fotrune_list" || $_GET['act'] == "fotrune_show"): ?> class="h"<?php endif; ?>><a href="company_fortune.php?act=fortune">�����Ը�����</a></li>

					</ul>
				</div>
			</div>
		<div class="meunbox">
		  <div class="tit"><div class="t c4">��Ա����</div></div>
		  	<div class="linktxt">
				<ul>
				<?php if ($this->_vars['QISHI']['operation_mode'] == "1"): ?>
				<li <?php if ($_GET['act'] == "j_account"): ?>class="h"<?php endif; ?>><a href="company_service.php?act=j_account">�ҵ��˻�</a></li>
				<?php elseif ($this->_vars['QISHI']['operation_mode'] == "2"): ?>
				<li <?php if ($_GET['act'] == "t_account"): ?>class="h"<?php endif; ?>><a href="company_service.php?act=t_account">�ҵ��˻�</a></li>
				<?php elseif ($this->_vars['QISHI']['operation_mode'] == "3"): ?>
				<li <?php if ($_GET['act'] == "j_account" || $_GET['act'] == "t_account"): ?>class="h"<?php endif; ?>><a href="company_service.php?act=j_account">�ҵ��˻�</a></li>
				<?php endif; ?>
				<?php if ($this->_vars['QISHI']['operation_mode'] == "1" || $this->_vars['QISHI']['operation_mode'] == "3"): ?>
				<li <?php if ($_GET['act'] == "order_add" || $_GET['act'] == "setmeal_list" || $_GET['act'] == "order_list" || $_GET['act'] == "payment" || $_GET['act'] == "setmeal_order_add" || $_GET['act'] == "gifts" || $_GET['act'] == "pay_reduce" || $_GET['act'] == "pay_add"): ?>class="h"<?php endif; ?>><a href="company_service.php?act=order_add">��ֵ����</a></li>
				<?php elseif ($this->_vars['QISHI']['operation_mode'] == "2"): ?>
				<li <?php if ($_GET['act'] == "setmeal_list" || $_GET['act'] == "order_list" || $_GET['act'] == "payment" || $_GET['act'] == "setmeal_order_add" || $_GET['act'] == "pay_add" || $_GET['act'] == "pay_reduce"): ?>class="h"<?php endif; ?>><a href="company_service.php?act=setmeal_list">��ֵ����</a></li>
				<?php endif; ?>
				<li <?php if ($_GET['act'] == "adv_list" || $_GET['act'] == "adv_order_add" || $_GET['act'] == "sms_order" || $_GET['act'] == "adv_payment" || $_GET['act'] == "sms_order_add" || $_GET['act'] == "sms_payment"): ?>class="h"<?php endif; ?>><a href="company_service.php?act=adv_list">��ֵ����</a></li>
				<li <?php if ($_GET['act'] == "tpl"): ?>class="h"<?php endif; ?>><a href="company_promotion.php?act=tpl">��ҵģ��</a></li>
				</ul>
			</div>
		</div>
		<?php if ($this->_vars['_PLUG']['jobfair']['p_install'] == "2"): ?>
		<div class="meunbox">
		  <div class="tit"><div class="t c3">�ֳ���Ƹ��</div></div>
		  	<div class="linktxt">
				<ul>
				<li><a target="_blank" href="<?php echo $this->_run_modifier("QS_jobfairlist", 'qishi_url', 'plugin', 1); ?>
">Ԥ��չλ</a></li>
				<li <?php if ($_GET['act'] == "mybooth"): ?>class="h"<?php endif; ?>><a href="company_jobfair.php?act=mybooth">��չ��¼</a></li>
				</ul>
			</div>
		</div>
		<?php endif; ?>
		
			<div class="meunbox last">
		  <div class="tit"><div class="t c5">�˺Ź���</div></div>
		  	<div class="linktxt">
				<ul>
				<li <?php if ($_GET['act'] == "company_profile" || $_GET['act'] == "company_logo" || $_GET['act'] == "company_news" || $_GET['act'] == "company_img" || $_GET['act'] == "company_map_set" || $_GET['act'] == "company_news_add" || $_GET['act'] == "company_news_edit"): ?>class="h"<?php endif; ?>><a href="company_info.php?act=company_profile">��ҵ����</a></li>
				<li<?php if ($_GET['act'] == "authenticate" || $_GET['act'] == "company_auth" || $_GET['act'] == "login_log"): ?> class="h"<?php endif; ?>><a href="company_user.php?act=authenticate">��ȫ��֤</a></li>
				<li <?php if ($_GET['act'] == "pm"): ?>class="h"<?php endif; ?>><a href="company_user.php?act=pm">�ҵ���Ϣ</a></li>
				<li><a href="<?php echo $this->_run_modifier("QS_login", 'qishi_url', 'plugin', 1); ?>
?act=logout">�˳���¼</a></li>
				</ul>
			</div>
		</div>
		
		</div>
	</div>

	<!-- �Ҳ�Ư����� -->
<?php if ($this->_vars['consultant']): ?>
<div class="guwen_bbox">
		<div class="guwen">��ҵ����</div>
		<div class="guwen_open" style="display:none;">
			<div class="open_box">
				<span class="guwen_close"></span>
				<div class="guwen_blc">��ҵ����</div>
				<div class="guwen_top">
					<div class="guwen_avater"><img src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
data/images/<?php echo $this->_vars['consultant']['pic']; ?>
" alt="" width="55" height="70"></div>
					<p>�ͷ���<?php echo $this->_vars['consultant']['name']; ?>
</p>
				</div>
				<div class="guwen_btn_box">
					<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $this->_vars['consultant']['qq']; ?>
&site=qq&menu=yes" class="guwen_btn">������ѯ</a>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>