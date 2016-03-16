<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:47 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<meta http-equiv="X-UA-Compatible" content="IE=7">
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<title> Powered by ZHAOHULU</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
$(".admin_top_nav>a").click(function(){
	$(".admin_top_nav>a").removeClass("select");
	$(this).addClass("select");
	$(this).blur();
	window.parent.frames["leftFrame"].location.href =  "admin_left.php?act="+$(this).attr("id");
	})
})
</script>
</head>
<body>
<div class="admin_top_bg">
    <table width="1400" height="70" border="0" cellpadding="0" cellspacing="0">
        <tr><td width="200" rowspan="2" align="right" valign="middle" ><div align="center"><img src="images/logo_in.png" width="160" height="25" /></div>
		</td>
          <td height="39" align="right" valign="middle" class="link_w">
		 <?php if ($this->_vars['QISHI']['subsite_id'] != "0"): ?>
		  <span style="color:#FFFF00">[<?php echo $this->_vars['QISHI']['subsite_districtname']; ?>
վ����ƽ̨]</span>&nbsp;&nbsp;&nbsp;&nbsp;  
		  <?php endif; ?>
		  ��ӭ<?php echo $this->_vars['admin_rank']; ?>
��<strong style="color: #99FF00"><?php echo $this->_vars['admin_name']; ?>
</strong>&nbsp; ��¼&nbsp;&nbsp;&nbsp;&nbsp;  <a href="admin_login.php?act=logout" target="_top">[�˳�]</a>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; <a href="../" target="_blank">��վ��ҳ</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp; </td>
        </tr>
        <tr>
          <td height="31" valign="bottom" class="admin_top_nav">
		  <a href="admin_index.php?act=main" class="select" target="mainFrame" id="index">��ҳ</a>
		  <a href="admin_company.php" target="mainFrame" id="company">��ҵ</a>
		  <a href="admin_personal.php?act=list" target="mainFrame" id="personal">����</a>
		  <?php if ($this->_vars['_PLUG']['hunter']['p_install'] == 2): ?>
		  <a href="admin_hunter.php" target="mainFrame" id="hunter">��ͷ</a>
		  <?php endif; ?>
		  <?php if ($this->_vars['_PLUG']['train']['p_install'] == 2): ?>
		  <a href="admin_train.php?act=list" target="mainFrame" id="train">��ѵ</a>
		  <?php endif; ?>
		  <?php if ($this->_vars['_PLUG']['simple']['p_install'] == 2): ?>
		  <a href="admin_simple.php" target="mainFrame" id="simple">΢��Ȧ</a>
		  <?php endif; ?>
		  <?php if ($this->_vars['_PLUG']['jobfair']['p_install'] == 2): ?>
		  <a href="admin_jobfair.php" target="mainFrame" id="jobfair">��Ƹ��</a>
		  <?php endif; ?>
		  <?php if ($this->_vars['QISHI']['subsite_id'] == 0): ?>
		  <a href="admin_campus.php" target="mainFrame" id="campus">У԰��Ƹ</a>
		  <a href="admin_evaluation.php" target="mainFrame" id="evaluation">�˲Ų���</a>
		  <a href="admin_shop.php" target="mainFrame" id="shop">�����̳�</a>
		  <a href="admin_app.php" target="mainFrame" id="mobile">�ƶ���</a>
		  <?php endif; ?>
		  <a href="admin_article.php" target="mainFrame" id="article">����</a>
		  <a href="admin_ad.php?act=list" target="mainFrame" id="ad">���</a>
		  <a href="admin_clearcache.php" target="mainFrame" id="tools">����</a>
		  <?php if ($this->_vars['QISHI']['subsite_id'] == 0): ?>
		  <a href="admin_set.php" target="mainFrame" id="set">ϵͳ</a>
		  <?php endif; ?>
		  <div class="clear"></div>
		   </td>
        </tr>
      </table>
	  </div>
</body>
</html>
