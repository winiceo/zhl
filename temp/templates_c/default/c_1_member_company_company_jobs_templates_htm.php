<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.date_format.php'); $this->register_modifier("date_format", "tpl_modifier_date_format",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:41 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<title><?php echo $this->_vars['title']; ?>
</title>
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<meta name="author" content="找葫芦" />
<meta name="copyright" content="zhaohulu.com" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/user_common.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/user_company.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/ui-dialog.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min-common.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){
		// 删除弹出
		delete_dialog('.ctrl-del','#form1');
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
">会员中心</a> > 职位模板管理</div>
<div class="usermain">
  <div class="leftmenu  com link_bk">
  	 <?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("member_company/left.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
  </div>
  <div class="rightmain">
  
	<div class="bbox1 my_account link_bk jobs">
  		<div class="title_h1 intrgration">职位模板管理</div>


  		<div class="data resume">
  			<?php if ($this->_vars['jobs_templates']): ?>
  			<form id="form1" name="form1" method="post" action="?act=del_jobs_templates">
		  	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  		<tbody>
		  			<tr bgcolor="#F5F5F5" align="left" height="44">
		  				<th width="175" class="left"><input type="checkbox" name="chkAll"  id="chk" title="全选/反选" /> 模板名称</th>
		  				<th width="138" align="left">学历要求</th>
		  				<th width="128" align="left">工作经验</th>
		  				<th width="101" align="left">修改日期</th>
		  				<th width="106" align="center">操作</th>
		  			</tr>
		  			<?php if (count((array)$this->_vars['jobs_templates'])): foreach ((array)$this->_vars['jobs_templates'] as $this->_vars['list']): ?>
		  			<tr class="data_content" height="54">
		  				<td width="175" class="left"><input name="y_id[]" type="checkbox" id="y_id"   value="<?php echo $this->_vars['list']['id']; ?>
"/> <?php echo $this->_vars['list']['title']; ?>
</td>
		  				<td width="138" align="left"><?php echo $this->_vars['list']['education_cn']; ?>
</td>
		  				<td width="128" align="left"><?php echo $this->_vars['list']['experience_cn']; ?>
</td>
		  				<td width="101" align="left"><?php echo $this->_run_modifier($this->_vars['list']['addtime'], 'date_format', 'plugin', 1, "%Y-%m-%d"); ?>
</td>
		  				<td width="106" align="center"><a class="bg01" href="?act=edit_templates&id=<?php echo $this->_vars['list']['id']; ?>
">修改</a>&nbsp;<a class="bg01 ctrl-del" href="javascript:;" url="?act=del_jobs_templates&y_id=<?php echo $this->_vars['list']['id']; ?>
">删除</a></td>
		  			</tr>
		  			<?php endforeach; endif; ?>
		  		</tbody>
		  	</table>
		  	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  		<tbody>
		  			<tr>
		  				<td class="left">
		  					<input type="button" id="add" name="add" onclick="javascript:window.location.href='?act=add_templates'" value="创建模板" class="but95_35lan"/>
		  					<input type="button" name="delete" value="删除" act="?act=del_jobs_templates" class="but95_35lan ctrl-del"/>
		  				</td>
		  			</tr>
		  		</tbody>
		  	</table>
		  </form>
			<?php if ($this->_vars['page']): ?>
				<table border="0" align="center" cellpadding="0" cellspacing="0" class="link_bk">
		          <tr>
		            <td height="50" align="center"> <div class="page link_bk"><?php echo $this->_vars['page']; ?>
</div></td>
		          </tr>
		      </table>
		      <?php endif; ?>
			<?php else: ?>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  		<tbody>
		  			<tr bgcolor="#F5F5F5" align="left" height="44">
		  				<th width="175" class="left"><input type="checkbox" name="chkAll"  id="chk" title="全选/反选" /> 模板名称</th>
		  				<th width="138">学历要求</th>
		  				<th width="128">工作经验</th>
		  				<th width="101">修改日期</th>
		  				<th width="106">操作</th>
		  			</tr>
		  		</tbody>
		  	</table>
			<div class="emptytip">没有找到对应的信息！</div>
		  	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  		<tbody>
		  			<tr>
		  				<td width="100" class="left"><input type="button" id="add" name="add" onclick="javascript:window.location.href='?act=add_templates'" value="创建模板" class="but95_35lan"/></td>
		  			</tr>
		  		</tbody>
		  	</table>
			<?php endif; ?>
		  </div>
		<!-- 小贴士 -->
		  <div class="bottomtip">
			<div class="tp h2-title">小贴士</div>
			把常用的职位保存为模版，发布新职位时会减少大量重复输入工作
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