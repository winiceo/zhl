<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.cat.php'); $this->register_modifier("cat", "tpl_modifier_cat",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_resume_list.php'); $this->register_function("qishi_resume_list", "tpl_function_qishi_resume_list",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_ad.php'); $this->register_function("qishi_ad", "tpl_function_qishi_ad",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.date_format.php'); $this->register_modifier("date_format", "tpl_modifier_date_format",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:35 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<meta name="renderer" content="webkit"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge"/> 
<title><?php echo $this->_vars['title']; ?>
</title>
<meta name="renderer" content="webkit"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<meta name="author" content="�Һ�«" />
<meta name="copyright" content="zhaohulu.com" />
<link rel="stylesheet" href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/user_common.css" />
<link rel="stylesheet" href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/user_company.css" />
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.js"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.cookie.js" type='text/javascript'></script>
<script>
	$(document).ready(function() {
		// ��һ��ע������Ա���ģ���ʾ����
		var mode = $.cookie('isFirstReg');
		if (mode == 1) {
			$("#mask").height($(document).height());
			$('#mask, #searchTip, #searchTip div:eq(0)').show();
			var topL0 = $("#searchTip div:eq(0)").offset().top;
			$(document).scrollTop(topL0);
			$('#searchTip div a').click(function(){
				var currentStep=$(this).parent();
				currentStep.hide();
				currentStep.next().show();
				var topLn = currentStep.next().offset().top;
				$("body,html").animate({scrollTop:topLn - 50}, 500);
			});
			$('#searchTip div a.p').unbind().click(function(){
				$('#searchTip div').hide();
				var currentStep=$(this).parent();
				currentStep.hide();
				currentStep.prev().show();
				var topLp = currentStep.prev().offset().top;
				$("body,html").animate({scrollTop:topLp - 50}, 500);
			});
			$('#searchTip div a:last, #searchTip div span').unbind().click(function(){
				$('#mask, #searchTip').hide();
				$("body,html").animate({scrollTop:0}, 500);
				return false;
			});
			$.cookie('isFirstReg',0);
		};
		function blurAction(){
			$('.c-name-edit-text input').on('blur', function(){
				var changeName = $(this).val();
				if (changeName != '') {
					$(this).parent().hide().siblings('h2').text(changeName).show().siblings('.name-edit').show();
				}else{
					alert('��˾���Ʋ���Ϊ��');
					$(this).focus();
				}
			})
		}
		$('.i-auth-item').hover(function(){
			$(this).find('.not-auth-tip').show();
		}, function(){
			$(this).find('.not-auth-tip').hide();
		});
		$('.rec-data table tr:last').find('td').css({'border-bottom':0});
		// ����������ʾ��һ�����ѣ������������
		$(".company-index-tip .index-tip").eq(0).show().siblings('.index-tip').hide();
		// �����������Ѵ���
		$(".ctip_close").die().live('click', function(event) {
			$(this).closest('.index-tip').addClass('istip').slideUp(600);
			var leng = $(".company-index-tip .index-tip").not(".istip").length;
			if (leng > 0) {
				setTimeout(function () { 
					$(".company-index-tip .index-tip").not(".istip").eq(0).slideDown(600);
			    }, 600);
			} else if (leng == 0) {
				setTimeout(function () { 
					$(".company-index-tip").slideUp(600);
					$(".bbox1 .lin").hide();
			    }, 600);
			}
		});
	});
</script>
</head>
<body <?php if ($this->_vars['QISHI']['body_bgimg']): ?>style="background:url(<?php echo $this->_vars['QISHI']['site_domain'];  echo $this->_vars['QISHI']['site_dir']; ?>
data/<?php echo $this->_vars['QISHI']['updir_images']; ?>
/<?php echo $this->_vars['QISHI']['body_bgimg']; ?>
) repeat-x center 38px;"<?php endif; ?>>
	<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("user/header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
	<div class="page_location link_bk">��ǰλ�ã�<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
">��ҳ</a> > <a href="<?php echo $this->_vars['userindexurl']; ?>
">��Ա����</a></div>
	<!-- ���� -->
	<div id="mask"></div>
	<div id="searchTip">
		<div class="stepA"><a>��һ��</a><span title="�ر�">�ر�</span></div>
	    <div class="stepB"><a class="p">��һ��</a><a>��һ��</a><span title="�ر�">�ر�</span></div>
	    <div class="stepC"><a class="p">��һ��</a><a>��һ��</a><span title="�ر�">�ر�</span></div>
	</div>
	<!-- ���� end-->
	<div class="usermain">
		<div class="leftmenu com link_bk">
			<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("member_company/left.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
		</div>
		<div class="rightmain">
			<div class="bbox1">
				<?php if ($this->_vars['message']): ?>
				<div class="company-index-tip">
					<?php if (count((array)$this->_vars['message'])): foreach ((array)$this->_vars['message'] as $this->_vars['list']): ?>
					<div class="index-tip">
						<?php echo $this->_vars['list']; ?>

						<i class="close-icon ctip_close"></i>
					</div>
					<?php endforeach; endif; ?>
				</div>
				<div class="lin"></div>
				<?php endif; ?>
				<div class="index-company-wrap">
					<div class="top-welcome clearfix top-welcome-mt5">
						<h1 class="f-left">��ӭ����<?php echo $this->_vars['user']['username']; ?>
</h1>
						<div class="f-right short-message">��Ϣ���ѣ�<a href="company_user.php?act=pm" class="underline">�Ѷ�(<?php echo $this->_vars['msg_total1']; ?>
)</a>&nbsp;<a href="company_user.php?act=pm" class="underline">δ��<span>(<?php echo $this->_vars['msg_total2']; ?>
)</span></a></div>
					</div>
					<div class="company-log-auth clearfix">
						<div class="index-logo f-left">
							<?php if ($this->_vars['company']['logo'] == ""): ?>
							<img src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/no_logo.gif" alt="" width="243" height="78" onclick="javascript:location.href='company_info.php?act=company_logo'"/>
							<?php else: ?>
							<img src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
data/logo/<?php echo $this->_vars['company']['logo']; ?>
?rand=<?php echo $this->_vars['rand']; ?>
" alt="" width="243" height="78" onclick="javascript:location.href='company_info.php?act=company_logo'"/>
							<?php endif; ?>
						</div>
						<div class="name-auth-info f-left">
							<div class="company-name clearfix">
								<h2 class="f-left"><?php if ($this->_vars['cominfo_flge']):  echo $this->_vars['company']['companyname'];  else: ?>δ������ҵ����<?php endif; ?></h2>
								<div class="c-name-edit-text f-left"><input type="text" /></div>
								<a href="company_info.php?act=company_profile" class="name-edit f-left">�༭</a>
							</div>
							<div class="last-login"><?php if ($this->_vars['loginlog']): ?>�ϴε�¼��<?php echo $this->_run_modifier($this->_vars['loginlog']['log_addtime'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M");  else: ?>�״ε�¼�� <?php echo $this->_run_modifier($this->_vars['user']['reg_time'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M");  endif; ?> <a href="company_user.php?act=login_log" class="underline">[��¼��־]</a></div>
							<div class="auth-wrap clearfix">
								<div class="i-auth-item f-left">
									<a href="company_info.php?act=company_auth" class="auth-block com <?php if ($this->_vars['company']['audit'] == "1"): ?>done<?php endif; ?>">��ҵ<?php if ($this->_vars['company']['audit'] == "1"): ?>��<?php else: ?>δ<?php endif; ?>��֤</a>
									<?php if ($this->_vars['company']['audit'] != '1'): ?>
									<div class="not-auth-tip"><i class="ia-tip-arrow"></i>��ҵ��֤�����������ҵ�����Ʒ�ƣ�ʹ��ҵ��Ƹ����ʵ������ְ�߸����Ρ�<a href="company_info.php?act=company_auth" class="underline">�����ϴ�Ӫҵִ��</a></div>
									<?php endif; ?>
								</div>
								<?php if ($this->_vars['QISHI']['weixin_scan_bind'] == '1' && $this->_vars['QISHI']['weixin_apiopen'] == '1'): ?>
								<div class="i-auth-item f-left">
									<a href="company_user.php?act=authenticate" class="auth-block weixin <?php if ($this->_vars['user']['weixin_openid']): ?>done<?php endif; ?>">΢��<?php if ($this->_vars['user']['weixin_openid']): ?>��<?php else: ?>δ<?php endif; ?>��֤</a>
									<?php if (! $this->_vars['user']['weixin_openid']): ?>
									<div class="not-auth-tip"><i class="ia-tip-arrow"></i>΢����֤�������ʱ����˽�ְλ���¶�̬�������ƸЧ�ʡ�<a href="company_user.php?act=authenticate" class="underline">������֤</a></div>
									<?php endif; ?>
								</div>
								<?php endif; ?>
								<div class="i-auth-item f-left">
									<a href="company_user.php?act=authenticate" class="auth-block mail <?php if ($this->_vars['user']['email_audit'] == "1"): ?>done<?php endif; ?>">����<?php if ($this->_vars['user']['email_audit'] == "1"): ?>��<?php else: ?>δ<?php endif; ?>��֤</a>
									<?php if ($this->_vars['user']['email_audit'] != "1"): ?>
									<div class="not-auth-tip"><i class="ia-tip-arrow"></i>������֤�󣬿���ͨ������ȡ���˻����룬�ҽ��ռ�����<a href="company_user.php?act=authenticate" class="underline">������֤</a></div>
									<?php endif; ?>
								</div>
								<?php if ($this->_vars['sms']['open'] == "1"): ?>
								<div class="i-auth-item f-left">
									<a href="company_user.php?act=authenticate" class="auth-block phone <?php if ($this->_vars['user']['mobile_audit'] == "1"): ?>done<?php endif; ?>">�ֻ�<?php if ($this->_vars['user']['mobile_audit'] == "1"): ?>��<?php else: ?>δ<?php endif; ?>��֤</a>
									<?php if ($this->_vars['user']['mobile_audit'] != "1"): ?>
									<div class="not-auth-tip"><i class="ia-tip-arrow"></i>�ֻ���֤�󣬿���ͨ���ֻ���ʱȡ���˻����룬����˷�������������š�<a href="company_user.php?act=authenticate" class="underline">������֤</a></div>
									<?php endif; ?>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="index-account-info">
						<!--�ײ�ģʽ -->
						<?php if ($this->_vars['QISHI']['operation_mode'] == "2" || $this->_vars['QISHI']['operation_mode'] == "3"): ?>
						<span class="account-type">
							<span>�ҵ��ײͣ�</span><?php echo $this->_vars['setmeal']['setmeal_name']; ?>
<em> (<?php echo $this->_run_modifier($this->_vars['setmeal']['starttime'], 'date_format', 'plugin', 1, "%Y-%m-%d"); ?>
 �� <?php if ($this->_vars['setmeal']['endtime'] == '0'): ?>--<?php else:  echo $this->_run_modifier($this->_vars['setmeal']['endtime'], 'date_format', 'plugin', 1, "%Y-%m-%d");  endif; ?>)</em>
							<a href="company_service.php?act=setmeal_list" class="underline">����VIP�ײ�</a>
						</span>
						<?php endif; ?>
						<!--�ײ�ģʽ���� -->
						<!--����ģʽ  -->
						<?php if ($this->_vars['QISHI']['operation_mode'] == "1" || $this->_vars['QISHI']['operation_mode'] == "3"): ?>
						<span class="account-type last">
							<span>�ҵ�<?php echo $this->_vars['QISHI']['points_byname']; ?>
��</span>
							<span class="account-fen"><?php echo $this->_vars['points']; ?>
</span><?php echo $this->_vars['QISHI']['points_quantifier']; ?>

							<a href="company_service.php?act=order_add" class="underline">���ֳ�ֵ</a>
							<a href="<?php echo $this->_run_modifier("QS_shop_index", 'qishi_url', 'plugin', 1); ?>
" class="underline">�����̳�</a>
						</span>
						<?php endif; ?>
						<!--����ģʽ���� -->
					</div>
				</div>
				<div class="commanage clearfix">
					<div class="list">
		  				<div class="t">ְλ����</div>
						<div class="p">
							<a href="company_jobs.php?act=jobs&jobtype=">
								<span class="s_a">�����е�ְλ��</span><?php echo $this->_vars['total_audit_jobs']; ?>

							</a>
			    			<a href="company_jobs.php?act=jobs&jobtype=2">
			    				<span class="s_a">����е�ְλ��</span><?php echo $this->_vars['total_noaudit_jobs']; ?>

			    			</a>
						</div>
		    				<div class="but"><input name="" type="button" value="����ְλ" class="but180lan" onclick="javascript:location.href='company_jobs.php?act=addjobs'"></div>
		    			</div>
		    			<div class="list" style="width:300px">
		  				<div class="t">��������</div>
						<div class="p">
							<a href="company_recruitment.php?act=apply_jobs&look=1">
								<span class="s_a">δ�鿴�ļ�����</span><?php echo $this->_vars['total_nolook_resume']; ?>

							</a>
			    			<a href="company_recruitment.php?act=down_resume_list">
			    				<span class="s_a">�����صļ�����</span><?php echo $this->_vars['total_down_resume']; ?>

			    			</a>
						</div>
		    				<div class="but"><input name="" type="button" value="�������" class="but180lan" onclick="javascript:location.href='company_recruitment.php?act=apply_jobs'"></div>
		    			</div>
					<div class="list last">
		  				<div class="t">��������</div>
						<div class="p">
							<a href="company_recruitment.php?act=my_attention">
								<span class="s_a">������ļ�����</span><?php echo $this->_vars['total_view_resume']; ?>

							</a>
			    			<a href="company_recruitment.php?act=favorites_list">
			    				<span class="s_a">���ղصļ�����</span><?php echo $this->_vars['total_favorites_resume']; ?>

			    			</a>
						</div>
		    				<div class="but"><input name="" type="button" value="��������" class="but180lan" onclick="javascript:window.open('<?php echo $this->_run_modifier("QS_resumelist", 'qishi_url', 'plugin', 1); ?>
')"></div>
		    			</div>
		    			<div class="clear"></div>
				</div>
				<div class="rec-resumes">
					<!-- ��ҵ��Ա�����в���� -->
					<?php echo tpl_function_qishi_ad(array('set' => "��ʾ��Ŀ:1,��������:QS_company_index,�б���:list,���ֳ���:12"), $this);?>
					<?php if ($this->_vars['list']['0']): ?>
					<div class="user-company-ad">
						<a href="<?php echo $this->_vars['list']['0']['img_url']; ?>
" target="_blank"><img src="<?php echo $this->_vars['list']['0']['img_path']; ?>
" alt="<?php echo $this->_vars['list']['0']['img_explain']; ?>
" width="950" height="120" /></a>
					</div>
					<?php endif; ?>
					<!-- ��ҵ��Ա�����в���� end-->
					<div class="rec-re-top clearfix">
						<h4 class="f-left">�Ƽ�����</h4>
						<a href="<?php echo $this->_run_modifier("QS_resumelist", 'qishi_url', 'plugin', 1); ?>
" class="f-right underline">�鿴����>></a>
					</div>
					<?php echo tpl_function_qishi_resume_list(array('set' => $this->_run_modifier("�б���:resumelist,��ʾ��Ŀ:6,����ְλ����:30,��ַ�:...,����:rtime>desc,ְλ����:", 'cat', 'plugin', 1, $this->_vars['concern_id'])), $this);?>
					<?php if (! $this->_vars['resumelist']): ?>
					<?php echo tpl_function_qishi_resume_list(array('set' => "�б���:resumelist,��ʾ��Ŀ:6,����ְλ����:30,��ַ�:...,����:rtime>desc"), $this);?>
					<?php endif; ?>
					<div class="rec-data">
						<table>
							<?php if ($this->_vars['resumelist']): ?>
							<tbody>
								<?php if (count((array)$this->_vars['resumelist'])): foreach ((array)$this->_vars['resumelist'] as $this->_vars['list']): ?>
								<tr>
									<td width="117" class="first"><a href="<?php echo $this->_vars['list']['resume_url']; ?>
" target="_blank" class="underline"><?php echo $this->_vars['list']['fullname']; ?>
</a></td>
									<td width="74"><?php echo $this->_vars['list']['age']; ?>
��</td>
									<td width="78"><?php echo $this->_vars['list']['education_cn']; ?>
</td>
									<td width="87"><?php echo $this->_vars['list']['experience_cn']; ?>
</td>
									<td width="325"><div title="<?php echo $this->_vars['list']['intention_jobs']; ?>
">����ְλ��<?php echo $this->_vars['list']['intention_jobs']; ?>
</div></td>
									<td width="73"><?php echo $this->_run_modifier($this->_vars['list']['refreshtime'], 'date_format', 'plugin', 1, "%Y-%m-%d"); ?>
</td>
								</tr>
								<?php endforeach; endif; ?>
							</tbody>
							<?php else: ?>
							<tbody>
								<tr>
									<td class="emptytip">�Բ���û���ҵ���Ҫ����Ϣ��</td>
								</tr>
							</tbody>
							<?php endif; ?>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("user/footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>