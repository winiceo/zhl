<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_nav.php'); $this->register_function("qishi_nav", "tpl_function_qishi_nav",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:34 CST */ ?>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.autocomplete.js" type="text/javascript" language="javascript"></script>
<script>
	$(function() {
		// 顶部nav展开
		$('.nav-list').hover(function(){
			$(this).find('.nav-li').css({'background-color':'#005eac'});
			$(this).find('.nav-more-drop').show();
		}, function(){
			$(this).find('.nav-li').attr('style', '');
			$(this).find('.nav-more-drop').hide();
		});
		// 搜索类型展开
		$('.search-type').hover(function(){
			$(this).find('.search-type-drop').show();
		}, function(){
			$(this).find('.search-type-drop').hide();
		});
		// 选择找工作还是找人才
		$('.search-type-drop').bind('click', function() {
			var stype = $(this).attr("code"), tit = $(this).attr("title"), data = $(this).attr("data"),
				hstype = $(".search-type-show").attr("code"), htit = $(".search-type-show").attr("title"), hdata = $(".search-type-show").attr("data");
			$("#btnForIndexSearch").attr("code",stype);
			$("#keyForIndexSearch").attr("placeholder",data);
			$(this).attr("code",hstype).attr("title",htit).attr("data",hdata).find("a").text(htit);
			$(".search-type-show").attr("code",stype).attr("title",tit).attr("data",data).find("span").text(tit);
			$('.search-type-drop').hide();
		});
		var dir = "<?php echo $this->_vars['QISHI']['site_dir']; ?>
";
		// 回车搜索
		$('#keyForIndexSearch').keydown(function(e) {
			if (e.keyCode==13) {
				search_location(dir);
			}
		});
		// 搜索按钮点击
		$("#btnForIndexSearch").click(function() {
			search_location(dir);
		});
		// 搜索跳转
		function search_location(dir) {
			generateBackground();
			var listype = $("#btnForIndexSearch").attr('code');
			var key=$("input[name=keyForIndexSearch]").val();
			var page=1;
			$.get(dir+"plus/ajax_search_location.php", {"act":listype,"key":key,"page":page},
				function (data,textStatus)
				 {	
					 window.location.href=data;
				 },"text"
			);
		}
		// 正在加载
		function generateBackground() {
			var backgroundHtm = '<div id="bonfire-pageloader"><div class="bonfire-pageloader-icon"></div></div>';
			var html = jQuery('body');
			html.append(backgroundHtm);
			jQuery(window).resize(function(){
				 resizenow();
			});
			function resizenow() {
				var browserwidth = jQuery(window).width();
				var browserheight = jQuery(window).height();
				jQuery('.bonfire-pageloader-icon').css('right', ((browserwidth - jQuery(".bonfire-pageloader-icon").width())/2)).css('top', ((browserheight - jQuery(".bonfire-pageloader-icon").height())/2 + $(document).scrollTop() - 109));
			};
			resizenow();
		}
		// 搜索提示
		var hotKey = $('#keyForIndexSearch').autocomplete({
			serviceUrl:dir+"plus/ajax_common.php?act=hotword",
			minChars:1, 
			maxHeight:400,
			width:278,
			zIndex: 1,
			deferRequestBy: 0 
		});
		// 名企列表
		$('.famous-items:nth-child(4n)').css({'margin-right':0});
		// 照片简历
		$('.photo-items:nth-child(7n)').css({'margin-right':0});
		// 广告位
		var adBlock = $('.ad-row');
		adBlock.each(function(){
			if ($(this).find('.ad-item').length == 3) {
				$(this).find('.ad-item:nth-child(3n)').css({'margin-right':0});
			}else if($(this).find('.ad-item').length == 5) {
				$(this).find('.ad-item:nth-child(5n)').css({'margin-right':0});
			};
		});
	});
</script>
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
							<li><a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
jobs/map-search.php" class="underline f-left" target="_blank">地图搜索</a></li>
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
				<?php if ($_SESSION['username']): ?>
				<span class="login-reg"><a href="<?php echo $this->_run_modifier("QS_login", 'qishi_url', 'plugin', 1); ?>
" class="underline"><?php echo $_SESSION['username']; ?>
</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo $this->_run_modifier("QS_login,act:logout", 'qishi_url', 'plugin', 1); ?>
" class="underline">[退出]</a></span>
				<?php else: ?>
				<span class="login-reg"><a href="<?php echo $this->_run_modifier("QS_login", 'qishi_url', 'plugin', 1); ?>
" class="underline">登录</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
user/user_reg.php" class="underline">注册</a></span>
				<?php endif; ?>
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
		<div class="mobile-block f-right">
			<div class="complex-center f-left">
				<!-- 搜索 -->
				<div class="search-wrap clearfix">
					<div class="search-box f-left">
						<div class="search-type f-left">
							<?php if ($_SESSION['utype'] == 2): ?>
							<div title="找工作" code="QS_jobslist" data="请输入职位名称或企业名称" class="search-type-show"><span>找工作</span><i class="search-icon"></i></div>
							<div title="找人才" code="QS_resumelist" data="请输入简历关键字" class="search-type-drop"><a href="javascript:;">找人才</a></div>
							<?php else: ?>
							<div title="找人才" code="QS_resumelist" data="请输入简历关键字" class="search-type-show"><span>找人才</span><i class="search-icon"></i></div>
							<div title="找工作" code="QS_jobslist" data="请输入职位名称或企业名称" class="search-type-drop"><a href="javascript:;">找工作</a></div>
							<?php endif; ?>
						</div>
						<div class="search-text f-left">
							<input type="text" name="keyForIndexSearch" id="keyForIndexSearch" placeholder="<?php if ($_SESSION['utype'] == 2): ?>请输入职位名称或企业名称<?php else: ?>请输入简历关键字<?php endif; ?>" />
						</div>
					</div>
					<div class="search-submit f-left"><input type="button" name="btnForIndexSearch" id="btnForIndexSearch" value="搜索" class="search-submit" <?php if ($_SESSION['utype'] == 2): ?>code="QS_jobslist"<?php else: ?>code="QS_resumelist"<?php endif; ?>/></div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- 头部结束 -->