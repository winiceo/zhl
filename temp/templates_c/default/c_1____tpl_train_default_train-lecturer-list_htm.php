<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_train_lecturer_list.php'); $this->register_function("qishi_train_lecturer_list", "tpl_function_qishi_train_lecturer_list",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_get_classify.php'); $this->register_function("qishi_get_classify", "tpl_function_qishi_get_classify",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.default.php'); $this->register_modifier("default", "tpl_modifier_default",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_categoryname.php'); $this->register_modifier("qishi_categoryname", "tpl_modifier_qishi_categoryname",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.cat.php'); $this->register_modifier("cat", "tpl_modifier_cat",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:55 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php if ($_GET['key']):  echo $_GET['key']; ?>
 - <?php endif;  if ($_GET['citycategory']):  echo $this->_run_modifier($this->_run_modifier("QS_district,", 'cat', 'plugin', 1, $_GET['citycategory']), 'qishi_categoryname', 'plugin', 1); ?>
 - <?php endif;  if ($_GET['education']):  echo $this->_run_modifier($this->_run_modifier("QS_education,", 'cat', 'plugin', 1, $_GET['education']), 'qishi_categoryname', 'plugin', 1); ?>
 - <?php endif; ?>��ʦ�б�  - <?php echo $this->_vars['QISHI']['site_name']; ?>
</title>
<meta name="description" content="<?php if ($_GET['citycategory']):  echo $this->_run_modifier($this->_run_modifier("QS_district,", 'cat', 'plugin', 1, $_GET['citycategory']), 'qishi_categoryname', 'plugin', 1); ?>
��<?php endif;  if ($_GET['education']):  echo $this->_run_modifier($this->_run_modifier("QS_education,", 'cat', 'plugin', 1, $_GET['education']), 'qishi_categoryname', 'plugin', 1); ?>
��<?php endif;  echo $this->_vars['QISHI']['site_name']; ?>
 - ������ѵ">
<meta name="keywords" content="<?php echo $this->_vars['QISHI']['site_name']; ?>
,�����б�������ѵ">
<meta name="author" content="�Һ�«��" />
<meta name="copyright" content="zhaohulu.com" />
<meta name="renderer" content="webkit"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/common.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_vars['user_tpl']; ?>
css/train.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.js" type='text/javascript' ></script>
<script src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
data/cache_classify.js" type='text/javascript' charset="utf-8"></script>
<script src="<?php echo $this->_vars['user_tpl']; ?>
js/jquery.train-search.js" type='text/javascript' ></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.autocomplete.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function() {
		allaround("<?php echo $this->_vars['QISHI']['site_dir']; ?>
");
		// �����������
		city_filldata("#city_list", QS_city_parent, QS_city, "#result-list-city", "#aui_outer_city", "#city_result_show", "#citycategory", "<?php echo $this->_vars['QISHI']['site_dir']; ?>
");
		$(".recommend_lecturer_list .h .s a").click(function(event) {
			generateBackground();
		});
		// ���ڼ���
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
		};
		// ȥ���б����һ�����±߿�
		$(".recommend_lecturer_list .l").last().css("border-bottom","none");
	});
</script>
</head>
<body class="bgf5">
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("../tpl_train/default/header-train.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<!-- ������ -->
<div class="container" style="margin-bottom:10px;">
	<div id="filterSearch" style="border:none;">
		<div class="top-search clearfix" style="background:#ffffff">
			<div class="filter-item f-left">
					<div class="choose-item">
						<div class="choose-control choose-control-s">
							<span class="cc-default cc-default-s" id="city_result_show">ѡ��������</span><i class="choose-icon"></i>
							<!-- �������������� -->
							<div class="aui_outer aui_outer_st8" id="aui_outer_city">
								<table class="aui_border">
									<tbody>
										<tr>
											<td class="aui_c">
												<div class="aui_inner">
													<table class="aui_dialog">
														<tbody>
															<tr>
																<td class="aui_main">
																	<div class="aui_content" style="padding: 0px;">
																		<div class="LocalDataMultiC" style="width:623px;">
																			<div class="selector-header"><span class="selector-title">����ѡ��</span><div></div><span id="ct-selector-save" class="selector-save">ȷ��</span><span class="selector-close">X</span><div class="clear"></div></div>

																			<div class="data-row-head"><div class="data-row"><div class="data-row-side data-row-side-c">���ѡ <strong class="text-warning">3</strong> ��&nbsp;&nbsp;��ѡ <strong id="arscity" class="text-warning">0</strong> ��</div><div id="result-list-city" class="result-list data-row-side-ra"></div></div><div class="cla"></div></div>
																			<div class="data-row-list data-row-main" id="city_list">
																				<!-- �б����� -->
																			</div>
																		</div>
																	</div>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<!-- �������������� End-->
						</div>
					</div>
			</div>
			<div class="t-search-box f-left t-search-box-s">
				<div class="type-input-box f-left" id="hidden_input_box">
					<div class="key"><?php echo $this->_run_modifier($_GET['key'], 'default', 'plugin', 1, "�����뽲ʦ����"); ?>
</div>
					<input class="s" type="text" name="key" id="key" value="<?php echo $_GET['key']; ?>
" style="display:none;"/>
					<input name="citycategory" id="citycategory" type="hidden" value="<?php echo $_GET['citycategory']; ?>
"/>
					<input name="education" id="education" type="hidden" value="<?php echo $_GET['education']; ?>
" />
					<input name="sort" id="sort" type="hidden" value="<?php echo $_GET['sort']; ?>
" />
					<input type="hidden" name="page" id="page" value="<?php echo $_GET['page']; ?>
">
				</div>
			</div>
			<div class="t-search-btn f-left"><input type="button" detype="QS_train_lecturer" id="searcnbtn" value="��&nbsp;��" /></div>
		</div>
		<div class="fliter-wrap">
			<div class="filter-list-wrap clearfix">
				<div class="fl-type f-left fl-type-s">��ʦѧ����</div>
				<div class="fl-content r-choice f-left">
					<?php echo tpl_function_qishi_get_classify(array('set' => "�б���:c_education,����:QS_education,��ʾ��Ŀ:100"), $this);?>
					<?php if (count((array)$this->_vars['c_education'])): foreach ((array)$this->_vars['c_education'] as $this->_vars['list']): ?>
					<div class="fl-content-li<?php if ($this->_vars['list']['id'] == $_GET['education']): ?> select<?php endif; ?> fl-content-li-s" type="education" code="<?php echo $this->_vars['list']['id']; ?>
"><?php echo $this->_vars['list']['categoryname']; ?>
</div>
					<?php endforeach; endif; ?>
				</div>
			</div>
		</div>
		<div class="fliter-result clearfix" id="has_result">
			<div class="fr-type f-left">��ѡ������</div>
			<div class="fr-content f-left rl">
			<?php if ($_GET['key']): ?>
				<div class="has-select-item f-left" type="key"><?php echo $_GET['key']; ?>
<i class="fliter-clear-icon"></i></div>
			<?php endif; ?>
			<?php if ($_GET['citycategory']): ?>
				<div class="has-select-item f-left" type="citycategory">�������<i class="fliter-clear-icon"></i></div>
			<?php endif; ?>
			<?php if ($_GET['education']): ?>
				<?php echo tpl_function_qishi_get_classify(array('set' => "�б���:education,����:QS_education,��ʾ��Ŀ:100"), $this);?>
				<?php if (count((array)$this->_vars['education'])): foreach ((array)$this->_vars['education'] as $this->_vars['list']): ?>
				<?php if ($this->_vars['list']['id'] == $_GET['education']): ?>
					<div class="has-select-item f-left" type="education"><?php echo $this->_vars['list']['categoryname']; ?>
<i class="fliter-clear-icon"></i></div>
				<?php endif; ?>
				<?php endforeach; endif; ?>
			<?php endif; ?>
			</div>
			<div class="fliter-clear f-right" id="clear_all_selected"><a href="javascript:;" class="clear-link">�����ѡ</a></div>
		</div>
	</div>
</div>
<!-- ������ End-->
<!-- ��ʦ�б� -->
<?php echo tpl_function_qishi_train_lecturer_list(array('set' => "��ҳ��ʾ:1,�б���:teacher,��ʾ��Ŀ:GET[inforow],��ַ�:...,����������:20,�ó�רҵ����:25,��������:67,��ʦѧ��:GET[education],�ؼ���:GET[key],��������:GET[citycategory],����:GET[sort],�б�ҳ:QS_train_lecturer"), $this);?>
<div class="recommend_lecturer_list">
	<div class="h h-nb">
		<div class="t t-nl">����</div>
		<div class="s">
			<a href="<?php echo $this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier("QS_train_lecturer,education:", 'cat', 'plugin', 1, $_GET['education']), 'cat', 'plugin', 1, "-citycategory:"), 'cat', 'plugin', 1, $_GET['citycategory']), 'cat', 'plugin', 1, "-sort:rtime"), 'cat', 'plugin', 1, "-inforow:" . $_GET['inforow'] . ""), 'qishi_url', 'plugin', 1); ?>
"  <?php if ($_GET['sort'] == "rtime" || $_GET['sort'] == ""): ?>class="select"<?php endif; ?>>����ʱ��</a>
		    <a href="<?php echo $this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier("QS_train_lecturer,education:", 'cat', 'plugin', 1, $_GET['education']), 'cat', 'plugin', 1, "-citycategory:"), 'cat', 'plugin', 1, $_GET['citycategory']), 'cat', 'plugin', 1, "-sort:hot"), 'cat', 'plugin', 1, "-inforow:" . $_GET['inforow'] . ""), 'qishi_url', 'plugin', 1); ?>
"  <?php if ($_GET['sort'] == "hot"): ?>class="select"<?php endif; ?>>�ȶ�</a>
		</div>
		<div class="s siri"><div class="pageminnav"><?php echo $this->_vars['pagemin']; ?>
</div></div>
	</div>
	<?php if ($this->_vars['teacher']): ?>
	<?php if (count((array)$this->_vars['teacher'])): foreach ((array)$this->_vars['teacher'] as $this->_vars['list']): ?>
	<div class="l">
		<div class="p"><img src="<?php if ($this->_vars['list']['photosrc']):  echo $this->_vars['list']['photosrc'];  else:  echo $this->_vars['QISHI']['site_template']; ?>
images/lecturer_no_photo.gif<?php endif; ?>" width="82" height="103" border="0"></div>
		<div class="t">
			<div class='name'><div class="pname"><a href="<?php echo $this->_vars['list']['teacher_url']; ?>
"><?php echo $this->_vars['list']['teachername']; ?>
</a></div><div class="cname"><a href="<?php echo $this->_vars['list']['train_url']; ?>
"><?php echo $this->_vars['list']['trainname']; ?>
</a></div></div>
			<div class="c">���ѧ����<?php echo $this->_vars['list']['education_cn']; ?>
 �� ���䣺<?php echo $this->_vars['list']['age']; ?>
 �� ���ڵ�����<?php echo $this->_vars['list']['district_cn']; ?>
 �� �ó�רҵ��<?php echo $this->_vars['list']['speciality']; ?>
 �� �������ڣ�<?php echo $this->_vars['list']['refreshtime_cn']; ?>
</div>
			<div class="c">���˼�飺<?php echo $this->_vars['list']['briefly']; ?>
</div>
			<div class="c">��Ҫ�ɹ���<?php echo $this->_vars['list']['achievements']; ?>
</div>
		</div>
	</div>
	<?php endforeach; endif; ?>
		<?php if ($this->_vars['page']): ?>
		<table border="0" align="center" cellpadding="0" cellspacing="0" class="link_bk">
		        	<tr>
		          		<td height="50" align="center"> <div class="page link_bk"><?php echo $this->_vars['page']; ?>
</div></td>
		          	</tr>
		</table>
		<?php endif; ?>
	<?php else: ?>
		<div class="emptytip">��Ǹ��û�з��ϴ���������Ϣ��</div>
	<?php endif; ?>
</div>
<!-- ��ʦ�б� End-->
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>