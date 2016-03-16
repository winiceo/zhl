<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:34 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php echo $this->_vars['title']; ?>
</title>
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<meta name="author" content="找葫芦" />
<meta name="copyright" content="zhaohulu.com" />
<meta http-equiv="Refresh" content="3; url=<?php echo $this->_vars['user']['url']; ?>
" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/reg.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<!-- 头部 -->
	<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("user/reg_header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
	<!-- main -->
	<div class="container">
		<div class="step_wrap">
			<div class="three-step-bar clearfix">
				<div class="step tstep1 f-left"><i class="step-icon">1</i>设置登录名</div>
				<div class="step tstep2 f-left"><i class="step-icon">2</i>填写账户信息</div>
				<div class="step tstep3 f-left active"><i class="step-icon">3</i>注册成功</div>
			</div>
		</div>
		<div class="common-status" style="padding:85px 0px">
			<div class="status-main">
				<span><img src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/icon-success-new.gif" alt="成功" /></span>恭喜您成功注册<?php if ($this->_vars['user']['utype'] == 1): ?>企业<?php elseif ($this->_vars['user']['utype'] == 2): ?>个人<?php elseif ($this->_vars['user']['utype'] == 3): ?>猎头<?php else: ?>培训<?php endif; ?>用户
			</div>
			<p class="automaticjump" id="automatic_jump">3秒后将自动跳转到会员中心</p>
			<div class="status-main">
				<a class="abtn index" href="<?php echo $this->_vars['user']['url']; ?>
">进入会员中心</a>
				<a class="abtn data" href="<?php echo $this->_vars['user']['index_url']; ?>
"><?php if ($this->_vars['user']['utype'] == 1): ?>完善资料<?php elseif ($this->_vars['user']['utype'] == 2): ?>创建简历<?php elseif ($this->_vars['user']['utype'] == 3): ?>完善资料<?php else: ?>完善资料<?php endif; ?></a>
			</div>
		</div>
	</div>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("user/footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>