<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_nav.php'); $this->register_function("qishi_nav", "tpl_function_qishi_nav",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:55 CST */ ?>
<div id="trainew-header" class="<?php if ($this->_vars['campus_nav'] == 'index'): ?>index<?php endif; ?>">
	<div class="header-top claerfix">
		<div class="container clearfix containernp">
			<div class="header-title f-left">您好<?php if ($_SESSION['username']): ?> <a href="<?php echo $this->_run_modifier("QS_login", 'qishi_url', 'plugin', 1); ?>
"><?php echo $_SESSION['username']; ?>
</a><?php endif; ?>，欢迎访问<?php echo $this->_vars['QISHI']['site_name']; ?>
培训专区！<?php if (! $_SESSION['username']): ?><a href="<?php echo $this->_run_modifier("QS_login", 'qishi_url', 'plugin', 1); ?>
">[登录]</a><?php else: ?><a href="<?php echo $this->_run_modifier("QS_login,act:logout", 'qishi_url', 'plugin', 1); ?>
" >[退出]</a><?php endif; ?></div>
			<div class="top-contact f-right"><span>服务热线：<?php echo $this->_vars['QISHI']['top_tel']; ?>
</span> | <a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
">返回找葫芦网</a></div>
		</div>
	</div>
	<div class="header-logo-nav">
		<div class="container clearfix containernp">
			<div class="school-logo f-left"><a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
"><img src="<?php echo $this->_vars['QISHI']['upfiles_dir'];  echo $this->_vars['QISHI']['web_logo']; ?>
" alt="logo" height="50" /></a></div>
			<div class="logo-side-title f-left">培训专区</div>
			<div class="school-nav f-right">
				<?php echo tpl_function_qishi_nav(array('set' => "调用名称:QS_train_top,列表名:list"), $this);?>
				<?php if (count((array)$this->_vars['list'])): foreach ((array)$this->_vars['list'] as $this->_vars['list']): ?>
			        <a href="<?php echo $this->_vars['list']['url']; ?>
" target="<?php echo $this->_vars['list']['target']; ?>
" class="s-nav-item f-left<?php if ($this->_vars['list']['tag'] == $this->_vars['page_select'] && $this->_vars['list']['tag'] != ""): ?> active<?php endif; ?>"><?php echo $this->_vars['list']['title']; ?>
</a>
			    <?php endforeach; endif; ?>
			</div>
		</div>
	</div>
</div>
