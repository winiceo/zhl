<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:33 CST */ ?>
	<div id="reg-head"<?php if ($this->_vars['header_nav'] == "login"): ?>style="margin:0"<?php endif; ?>>
		<div class="container">
			<div class="logo-box f-left">
				<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
" class="logo f-left"><img src="<?php echo $this->_vars['QISHI']['upfiles_dir'];  echo $this->_vars['QISHI']['web_logo']; ?>
" alt="<?php echo $this->_vars['QISHI']['site_name']; ?>
" width="261" height="70" border="0"/></a>
				<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
" class="back-index f-left"></a>
			</div>
			<div class="reg-tips f-left"><?php if ($this->_vars['header_nav'] == "getpass"): ?>�����һ�<?php elseif ($this->_vars['header_nav'] == "login"): ?>��ӭ��¼<?php else: ?>���ע��<?php endif; ?></div>
			<div class="top-right f-right">
				<?php if ($this->_vars['header_nav'] == "login"): ?>
				<p class="f-left">��û���ʺ�?</p>
				<div class="f-left"><a href="user_reg.php" class="btn login-btn blue" style="color:#FFFFFF">����ע��</a></div>
				<?php else: ?>
				<p class="f-left">����ע�ᣬ���ھ�</p>
				<div class="f-left"><a href="login.php?act=login" class="btn login-btn blue" style="color:#FFFFFF">��¼</a></div>
				<?php endif; ?>
			</div>
		</div>
	</div>