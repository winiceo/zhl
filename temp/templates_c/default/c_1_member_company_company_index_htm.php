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
<meta name="author" content="找葫芦" />
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
		// 第一次注册进入会员中心，显示引导
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
					alert('公司名称不能为空');
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
		// 顶部提醒显示第一条提醒，其余的先隐藏
		$(".company-index-tip .index-tip").eq(0).show().siblings('.index-tip').hide();
		// 操作顶部提醒代码
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
	<div class="page_location link_bk">当前位置：<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
">首页</a> > <a href="<?php echo $this->_vars['userindexurl']; ?>
">会员中心</a></div>
	<!-- 引导 -->
	<div id="mask"></div>
	<div id="searchTip">
		<div class="stepA"><a>下一步</a><span title="关闭">关闭</span></div>
	    <div class="stepB"><a class="p">上一步</a><a>下一步</a><span title="关闭">关闭</span></div>
	    <div class="stepC"><a class="p">上一步</a><a>下一步</a><span title="关闭">关闭</span></div>
	</div>
	<!-- 引导 end-->
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
						<h1 class="f-left">欢迎您，<?php echo $this->_vars['user']['username']; ?>
</h1>
						<div class="f-right short-message">消息提醒：<a href="company_user.php?act=pm" class="underline">已读(<?php echo $this->_vars['msg_total1']; ?>
)</a>&nbsp;<a href="company_user.php?act=pm" class="underline">未读<span>(<?php echo $this->_vars['msg_total2']; ?>
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
								<h2 class="f-left"><?php if ($this->_vars['cominfo_flge']):  echo $this->_vars['company']['companyname'];  else: ?>未完善企业资料<?php endif; ?></h2>
								<div class="c-name-edit-text f-left"><input type="text" /></div>
								<a href="company_info.php?act=company_profile" class="name-edit f-left">编辑</a>
							</div>
							<div class="last-login"><?php if ($this->_vars['loginlog']): ?>上次登录：<?php echo $this->_run_modifier($this->_vars['loginlog']['log_addtime'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M");  else: ?>首次登录： <?php echo $this->_run_modifier($this->_vars['user']['reg_time'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M");  endif; ?> <a href="company_user.php?act=login_log" class="underline">[登录日志]</a></div>
							<div class="auth-wrap clearfix">
								<div class="i-auth-item f-left">
									<a href="company_info.php?act=company_auth" class="auth-block com <?php if ($this->_vars['company']['audit'] == "1"): ?>done<?php endif; ?>">企业<?php if ($this->_vars['company']['audit'] == "1"): ?>已<?php else: ?>未<?php endif; ?>认证</a>
									<?php if ($this->_vars['company']['audit'] != '1'): ?>
									<div class="not-auth-tip"><i class="ia-tip-arrow"></i>企业认证后可以提升企业形象和品牌，使企业招聘更真实，让求职者更信任。<a href="company_info.php?act=company_auth" class="underline">立即上传营业执照</a></div>
									<?php endif; ?>
								</div>
								<?php if ($this->_vars['QISHI']['weixin_scan_bind'] == '1' && $this->_vars['QISHI']['weixin_apiopen'] == '1'): ?>
								<div class="i-auth-item f-left">
									<a href="company_user.php?act=authenticate" class="auth-block weixin <?php if ($this->_vars['user']['weixin_openid']): ?>done<?php endif; ?>">微信<?php if ($this->_vars['user']['weixin_openid']): ?>已<?php else: ?>未<?php endif; ?>认证</a>
									<?php if (! $this->_vars['user']['weixin_openid']): ?>
									<div class="not-auth-tip"><i class="ia-tip-arrow"></i>微信认证后可以随时随地了解职位最新动态，提高招聘效率。<a href="company_user.php?act=authenticate" class="underline">立即认证</a></div>
									<?php endif; ?>
								</div>
								<?php endif; ?>
								<div class="i-auth-item f-left">
									<a href="company_user.php?act=authenticate" class="auth-block mail <?php if ($this->_vars['user']['email_audit'] == "1"): ?>done<?php endif; ?>">邮箱<?php if ($this->_vars['user']['email_audit'] == "1"): ?>已<?php else: ?>未<?php endif; ?>认证</a>
									<?php if ($this->_vars['user']['email_audit'] != "1"): ?>
									<div class="not-auth-tip"><i class="ia-tip-arrow"></i>邮箱认证后，可以通过邮箱取回账户密码，且接收简历。<a href="company_user.php?act=authenticate" class="underline">立即认证</a></div>
									<?php endif; ?>
								</div>
								<?php if ($this->_vars['sms']['open'] == "1"): ?>
								<div class="i-auth-item f-left">
									<a href="company_user.php?act=authenticate" class="auth-block phone <?php if ($this->_vars['user']['mobile_audit'] == "1"): ?>done<?php endif; ?>">手机<?php if ($this->_vars['user']['mobile_audit'] == "1"): ?>已<?php else: ?>未<?php endif; ?>认证</a>
									<?php if ($this->_vars['user']['mobile_audit'] != "1"): ?>
									<div class="not-auth-tip"><i class="ia-tip-arrow"></i>手机认证后，可以通过手机随时取回账户密码，向个人发送面试邀请短信。<a href="company_user.php?act=authenticate" class="underline">立即认证</a></div>
									<?php endif; ?>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="index-account-info">
						<!--套餐模式 -->
						<?php if ($this->_vars['QISHI']['operation_mode'] == "2" || $this->_vars['QISHI']['operation_mode'] == "3"): ?>
						<span class="account-type">
							<span>我的套餐：</span><?php echo $this->_vars['setmeal']['setmeal_name']; ?>
<em> (<?php echo $this->_run_modifier($this->_vars['setmeal']['starttime'], 'date_format', 'plugin', 1, "%Y-%m-%d"); ?>
 至 <?php if ($this->_vars['setmeal']['endtime'] == '0'): ?>--<?php else:  echo $this->_run_modifier($this->_vars['setmeal']['endtime'], 'date_format', 'plugin', 1, "%Y-%m-%d");  endif; ?>)</em>
							<a href="company_service.php?act=setmeal_list" class="underline">升级VIP套餐</a>
						</span>
						<?php endif; ?>
						<!--套餐模式结束 -->
						<!--积分模式  -->
						<?php if ($this->_vars['QISHI']['operation_mode'] == "1" || $this->_vars['QISHI']['operation_mode'] == "3"): ?>
						<span class="account-type last">
							<span>我的<?php echo $this->_vars['QISHI']['points_byname']; ?>
：</span>
							<span class="account-fen"><?php echo $this->_vars['points']; ?>
</span><?php echo $this->_vars['QISHI']['points_quantifier']; ?>

							<a href="company_service.php?act=order_add" class="underline">积分充值</a>
							<a href="<?php echo $this->_run_modifier("QS_shop_index", 'qishi_url', 'plugin', 1); ?>
" class="underline">积分商城</a>
						</span>
						<?php endif; ?>
						<!--积分模式结束 -->
					</div>
				</div>
				<div class="commanage clearfix">
					<div class="list">
		  				<div class="t">职位管理</div>
						<div class="p">
							<a href="company_jobs.php?act=jobs&jobtype=">
								<span class="s_a">发布中的职位：</span><?php echo $this->_vars['total_audit_jobs']; ?>

							</a>
			    			<a href="company_jobs.php?act=jobs&jobtype=2">
			    				<span class="s_a">审核中的职位：</span><?php echo $this->_vars['total_noaudit_jobs']; ?>

			    			</a>
						</div>
		    				<div class="but"><input name="" type="button" value="发布职位" class="but180lan" onclick="javascript:location.href='company_jobs.php?act=addjobs'"></div>
		    			</div>
		    			<div class="list" style="width:300px">
		  				<div class="t">简历管理</div>
						<div class="p">
							<a href="company_recruitment.php?act=apply_jobs&look=1">
								<span class="s_a">未查看的简历：</span><?php echo $this->_vars['total_nolook_resume']; ?>

							</a>
			    			<a href="company_recruitment.php?act=down_resume_list">
			    				<span class="s_a">已下载的简历：</span><?php echo $this->_vars['total_down_resume']; ?>

			    			</a>
						</div>
		    				<div class="but"><input name="" type="button" value="管理简历" class="but180lan" onclick="javascript:location.href='company_recruitment.php?act=apply_jobs'"></div>
		    			</div>
					<div class="list last">
		  				<div class="t">简历搜索</div>
						<div class="p">
							<a href="company_recruitment.php?act=my_attention">
								<span class="s_a">浏览过的简历：</span><?php echo $this->_vars['total_view_resume']; ?>

							</a>
			    			<a href="company_recruitment.php?act=favorites_list">
			    				<span class="s_a">已收藏的简历：</span><?php echo $this->_vars['total_favorites_resume']; ?>

			    			</a>
						</div>
		    				<div class="but"><input name="" type="button" value="搜索简历" class="but180lan" onclick="javascript:window.open('<?php echo $this->_run_modifier("QS_resumelist", 'qishi_url', 'plugin', 1); ?>
')"></div>
		    			</div>
		    			<div class="clear"></div>
				</div>
				<div class="rec-resumes">
					<!-- 企业会员中心中部广告 -->
					<?php echo tpl_function_qishi_ad(array('set' => "显示数目:1,调用名称:QS_company_index,列表名:list,文字长度:12"), $this);?>
					<?php if ($this->_vars['list']['0']): ?>
					<div class="user-company-ad">
						<a href="<?php echo $this->_vars['list']['0']['img_url']; ?>
" target="_blank"><img src="<?php echo $this->_vars['list']['0']['img_path']; ?>
" alt="<?php echo $this->_vars['list']['0']['img_explain']; ?>
" width="950" height="120" /></a>
					</div>
					<?php endif; ?>
					<!-- 企业会员中心中部广告 end-->
					<div class="rec-re-top clearfix">
						<h4 class="f-left">推荐简历</h4>
						<a href="<?php echo $this->_run_modifier("QS_resumelist", 'qishi_url', 'plugin', 1); ?>
" class="f-right underline">查看更多>></a>
					</div>
					<?php echo tpl_function_qishi_resume_list(array('set' => $this->_run_modifier("列表名:resumelist,显示数目:6,意向职位长度:30,填补字符:...,排序:rtime>desc,职位大类:", 'cat', 'plugin', 1, $this->_vars['concern_id'])), $this);?>
					<?php if (! $this->_vars['resumelist']): ?>
					<?php echo tpl_function_qishi_resume_list(array('set' => "列表名:resumelist,显示数目:6,意向职位长度:30,填补字符:...,排序:rtime>desc"), $this);?>
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
岁</td>
									<td width="78"><?php echo $this->_vars['list']['education_cn']; ?>
</td>
									<td width="87"><?php echo $this->_vars['list']['experience_cn']; ?>
</td>
									<td width="325"><div title="<?php echo $this->_vars['list']['intention_jobs']; ?>
">意向职位：<?php echo $this->_vars['list']['intention_jobs']; ?>
</div></td>
									<td width="73"><?php echo $this->_run_modifier($this->_vars['list']['refreshtime'], 'date_format', 'plugin', 1, "%Y-%m-%d"); ?>
</td>
								</tr>
								<?php endforeach; endif; ?>
							</tbody>
							<?php else: ?>
							<tbody>
								<tr>
									<td class="emptytip">对不起！没有找到您要的信息！</td>
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