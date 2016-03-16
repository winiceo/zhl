<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_allsite.php'); $this->register_function("qishi_allsite", "tpl_function_qishi_allsite",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_nav.php'); $this->register_function("qishi_nav", "tpl_function_qishi_nav",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:47 CST */ ?>
<!-- 头部 -->
<div id="header">
	<div class="top-nav-wrap">
		<div class="top-nav clearfix">
			<ul class="f-left">
				<?php echo tpl_function_qishi_nav(array('set' => "调用名称:QS_top,列表名:list,分割:5"), $this);?>
				<?php if (count((array)$this->_vars['list'])): foreach ((array)$this->_vars['list'] as $this->_vars['list']): ?>
				<li><a href="<?php echo $this->_vars['list']['url']; ?>
" target="<?php echo $this->_vars['list']['target']; ?>
" class="nav-li <?php if ($this->_vars['list']['tag'] == $this->_vars['page_select'] && $this->_vars['list']['tag'] != ""): ?>active<?php endif; ?>"><?php echo $this->_vars['list']['title']; ?>
</a></li>
			    <?php endforeach; endif; ?>
				<li class="nav-list">
					<a href="javascript:;" class="nav-li">更多<i class="nav-more"></i></a>
					<div class="nav-more-drop">
						<ul class="nav-detail clearfix">
							<?php if (count((array)$this->_vars['list_more'])): foreach ((array)$this->_vars['list_more'] as $this->_vars['li']): ?>
							<li><a href="<?php echo $this->_vars['li']['url']; ?>
" target="<?php echo $this->_vars['li']['target']; ?>
" class="underline f-left"><?php echo $this->_vars['li']['title']; ?>
</a></li>
						    <?php endforeach; endif; ?>
							<li><a href="<?php echo $this->_run_modifier("QS_map,id:1", 'qishi_url', 'plugin', 1); ?>
" class="underline f-left" target="_blank">地图搜索</a></li>
							<li><a href="<?php echo $this->_run_modifier("QS_explainshow,id:7", 'qishi_url', 'plugin', 1); ?>
" class="underline f-left" target="_blank">VIP服务</a></li>
							<li><a href="<?php echo $this->_run_modifier("QS_suggest", 'qishi_url', 'plugin', 1); ?>
" class="underline f-left" target="_blank">意见建议</a></li>
							<li><a href="<?php echo $this->_run_modifier("QS_simplelist", 'qishi_url', 'plugin', 1); ?>
" class="underline f-left" target="_blank">微招聘</a></li>
							<li><a href="<?php echo $this->_run_modifier("QS_helplist", 'qishi_url', 'plugin', 1); ?>
" class="underline f-left">使用帮助</a></li>
						</ul>
						<div class="mob-list">
							<h4>移动互联</h4>
							<div class="mob-list-box clearfix">
								<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
mobile/?type=touch" class="mob-link wap-link f-left" target="_balnk">触屏版</a>
								<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
mobile/?type=android" class="mob-link android-link f-left" target="_balnk">安卓版</a>
								<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
mobile/?type=ios" class="mob-link ios-link f-left" target="_balnk">苹果版</a>
								<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
mobile/?type=weixin" class="mob-link wechat-link f-left" target="_balnk">微信版</a>
							</div>
						</div>
					</div>
				</li>
			</ul>
			<div class="nav-right f-right">
				<span id="top_loginform"></span>
				<span class="nav-items"><a href="<?php echo $this->_run_modifier("QS_shop_index", 'qishi_url', 'plugin', 1); ?>
" class="underline">积分商城</a></span>
				<span class="nav-items"><a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
salary" class="underline">薪酬统计</a></span>
				<span class="nav-items"><a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/shortcut.php" class="underline">保存到桌面</a></span>
			</div>
		</div>
	</div>
	<div class="container-index header-main clearfix">
		<div class="logo-box f-left"><a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
"><img src="<?php echo $this->_vars['QISHI']['upfiles_dir'];  echo $this->_vars['QISHI']['web_logo']; ?>
" alt="<?php echo $this->_vars['QISHI']['site_name']; ?>
" border="0" align="absmiddle" width="260" height="70"/></a></div>
		<!-- 分站 -->
		<div class="station-choose f-left">
			<p class="station-name"><?php echo $this->_vars['QISHI']['site_name']; ?>
</p>
			<div class="station-control">
				<div class="station-item"><?php if ($this->_vars['QISHI']['subsite_districtname']):  echo $this->_vars['QISHI']['subsite_districtname'];  else: ?>总站<?php endif; ?><i class="station-arrow"></i></div>
				<div class="station-drop-wrapper">
					<div class="drop-title clearfix">
						<h4 class="f-left">地区分站 · <a href="<?php echo $this->_vars['QISHI']['main_domain']; ?>
" class="underline">[总站]</a></h4>
						<span class="f-right">一站注册，多站发布</span>
					</div>
					<ul class="station-list clearfix">
						<?php echo tpl_function_qishi_allsite(array('set' => "列表名:city"), $this);?>
						<?php if (count((array)$this->_vars['city'])): foreach ((array)$this->_vars['city'] as $this->_vars['li']): ?>
						<li class="f-left"><a href="<?php echo $this->_vars['li']['s_domain']; ?>
" class="underline"><?php echo $this->_vars['li']['s_districtname']; ?>
</a></li>
						<?php endforeach; endif; ?>
					</ul>
				</div>
			</div>
		</div>
		<!-- 分站结束 -->
		<div class="mobile-block f-right">
			<div class="mob-btn-box f-left">
				<a href="javascript:;" class="mob-btn phone webApp">手机招聘</a>
				<div class="mob-dialog-box weMinBoxApp">
					<div class="mob-dialog">
						<i class="mob-arrow"><em></em></i>
						<div class="mob-d-wrap clearfix">
							<div class="mob-d-block f-left">
								<p>安卓版APP</p>
								<div><img src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/url_qrcode.php?url=<?php echo $this->_vars['QISHI']['site_domain'];  echo $this->_vars['QISHI']['site_dir']; ?>
m/download.php?downtype=android" alt="二维码" width="145" height="145" border="0" /></div>
							</div>
							<div class="mob-d-block f-left nm">
								<p>iOS版APP</p>
								<div><img src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/url_qrcode.php?url=<?php echo $this->_vars['QISHI']['site_domain'];  echo $this->_vars['QISHI']['site_dir']; ?>
m/download.php?downtype=ios" alt="二维码" width="145" height="145" border="0" /></div>
							</div>
						</div>
						<div class="mob-d-wap">触屏版：<span><a href="<?php echo $this->_vars['QISHI']['wap_domain']; ?>
" target="_blank"><?php echo $this->_vars['QISHI']['wap_domain']; ?>
</a></span></div>
					</div>
				</div>
			</div>
			<div class="mob-btn-box f-left">
				<a href="javascript:;" class="mob-btn wechat weChat">微信招聘</a>
				<div class="mob-dialog-box-w weMinBoxWx">
					<div class="mob-dialog">
						<i class="mob-arrow"><em></em></i>
						<div class="mob-d-block f-left nm">
							<p>官方微信</p>
							<div><img src="<?php echo $this->_vars['QISHI']['upfiles_dir'];  echo $this->_vars['QISHI']['weixin_img']; ?>
" alt="" width="145" height="145" border="0" /></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- 头部结束 -->
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.header.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript">
	$(document).ready(function() {
		header();
		//顶部部登录
		$.get("<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/ajax_user.php", {"act":"top_loginform"},
		function (data,textStatus)
		{			
		$("#top_loginform").html(data);
		}
		);
		$('.station-control').on('click', function(e) {
			if("<?php echo $this->_vars['QISHI']['sel_subsite_method']; ?>
"==0){
				$(this).find('.station-drop-wrapper').toggle();
				$('.station-item').toggleClass('open');
				e.stopPropagation();
				$(document).one('click', function(e) {
					$('.station-drop-wrapper').hide();
					$('.station-item').removeClass('open');
				});
			}else{
				location.href='<?php echo $this->_vars['QISHI']['site_dir']; ?>
substation';
			}
		});
		$('.station-drop-wrapper').on('click', function(e) {
			e.stopPropagation();
		});
	});
</script>