<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-01-29 13:47 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<title>��װ��-�Һ�«��</title>
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<link rel="stylesheet" href="templates/css/css.css" type="text/css" />
<script src="templates/js/jquery.js" type="text/javascript" language="javascript"></script>
<script src="http://www.74cms.com/plus/getinstall.php?domaindir=<?php echo $this->_vars['domaindir']; ?>
&domain=<?php echo $this->_vars['domain']; ?>
&email=<?php echo $this->_vars['email']; ?>
&v=<?php echo $this->_vars['v']; ?>
&t5<?php echo $this->_vars['t']; ?>
&i=1099" language="javascript"></script>
<script>
	$(function(){
		$(".step li:eq(3)").css("margin-right", 0);
	})
</script>
</head>
<body>
	<div class="install_box">
		<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
		<div class="step">
			<div class="step_show step_4"></div>
			<ul>
				<li class="complete">�������</li>
				<li class="complete">��������</li>
				<li class="complete">��ʼ��װ</li>
				<li class="complete">�ɹ���װ</li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="install_complete">
			<div class="sccueed">��ϲ�������ѳɹ���װ��ʿcms��</div>
			<div class="sccueed_but">
				<a href="../">��վ��ҳ</a><a href="../admin/" class="backstage">��վ��̨</a>
			</div>
		</div>

		
		<div class="copyright">
			Copyright @ 2015 74cms.com All Right Reserved
		</div>
	</div>
</body>
</html>