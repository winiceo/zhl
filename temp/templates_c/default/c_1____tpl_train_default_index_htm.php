<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_train_lecturer_list.php'); $this->register_function("qishi_train_lecturer_list", "tpl_function_qishi_train_lecturer_list",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_train_list.php'); $this->register_function("qishi_train_list", "tpl_function_qishi_train_list",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_ad.php'); $this->register_function("qishi_ad", "tpl_function_qishi_ad",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_notice_list.php'); $this->register_function("qishi_notice_list", "tpl_function_qishi_notice_list",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_curriculum_list.php'); $this->register_function("qishi_curriculum_list", "tpl_function_qishi_curriculum_list",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_get_classify.php'); $this->register_function("qishi_get_classify", "tpl_function_qishi_get_classify",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_pageinfo.php'); $this->register_function("qishi_pageinfo", "tpl_function_qishi_pageinfo",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:55 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<?php echo tpl_function_qishi_pageinfo(array('set' => "�б���:page,����:QS_train"), $this);?>
<title><?php echo $this->_vars['page']['title']; ?>
</title>
<meta name="description" content="<?php echo $this->_vars['page']['description']; ?>
">
<meta name="keywords" content="<?php echo $this->_vars['page']['keywords']; ?>
">
<meta name="author" content="��ʿCMS" />
<meta name="copyright" content="74cms.com" />
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

<div class="hunter_top_banner" style="background:url(<?php echo $this->_vars['user_tpl']; ?>
images/train_top_banner.gif) no-repeat top center #45c1c3"></div>
<!-- ������ End-->
<!-- ������ -->
<div class="container" style="padding-top:0px;">
	<div id="filterSearch" style="border:none;">
		<div class="fliter-wrap">
			<div class="filter-drop clearfix" style="margin-bottom:0px;">
				<div class="filter-item f-left">
					<div class="choose-item" id="select_train_category">
						<div class="choose-control">
							<span class="cc-default">ѡ��γ�</span><i class="choose-icon"></i>
							<div class="trainshowdiv" id="trainshowdiv">
								<ul>
									<?php echo tpl_function_qishi_get_classify(array('set' => "�б���:c_nature,����:QS_train_category"), $this);?>
									<?php if (count((array)$this->_vars['c_nature'])): foreach ((array)$this->_vars['c_nature'] as $this->_vars['list']): ?>
									 <li id="<?php echo $this->_vars['list']['id']; ?>
" title="<?php echo $this->_vars['list']['categoryname']; ?>
"><?php echo $this->_vars['list']['categoryname']; ?>
</li>
									<?php endforeach; endif; ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<script type="text/javascript">
					showmenu('#select_train_category','#trainshowdiv',"#category");
					//ģ��select
					function showmenu(menuID,showID,inputname)
					{
						$(menuID).click(function(){
							$(menuID).blur();
							$(menuID).css("position","relative");
							$(showID).slideToggle("fast");
							//���ɱ���
							$(menuID).parent("div").before("<div class=\"menu_bg_layer\"></div>");
							$(".menu_bg_layer").height($(document).height());
							$(".menu_bg_layer").css({ width: $(document).width(), position: "absolute", left: "0", top: "0" , "z-index": "0", "background-color": "#ffffff"});
							$(".menu_bg_layer").css("opacity","0");
							//���ɱ�������
							$(showID+" li").live("click",function(){
								$(menuID).find(".cc-default").html($(this).attr("title"));
								$(inputname).val($(this).attr("id"));
								$(".menu_bg_layer").remove();
								$(showID).hide();
								$(menuID).parent("div").css("position","");	
								$(this).css("background-color","");
							});
							$(".menu_bg_layer").click(function(){
								$(".menu_bg_layer").remove();
								$(showID).hide();
								$(menuID).parent("div").css("position","");
							});
							$(showID+" li").unbind("hover").hover(function() {
									$(this).css("background-color","#F5F5F5");
								}, function() {
									$(this).css("background-color","");
								}
							);
						});
					}
				</script>
				<div class="keybox">
					<div class="key">������ؼ���</div>
			 		<input type="text" name="key" id="key" style="display:none;"/>
					<input name="citycategory" id="citycategory" type="hidden" value=""/>
			 		<input name="category" id="category" type="hidden" value="" />
					<input name="classtype" id="classtype" type="hidden" value="" />
					<input type="hidden" name="start" id="start" value="">
					<input type="hidden" name="refre" id="refre" value="">
					<input type="hidden" name="sort" id="sort" value="">
			 	</div>
		 		<div class="btnsearch" detype="QS_train_curriculum" id="searcnbtn">��ѵ�γ�</div>
			</div>
		</div>
	</div>
</div>
<!-- ������ End-->
<div class="talents_information">
	<!-- �Ƽ��γ� -->
	<div class="hunterbox_container senior_talents">
		<div class="h">
			<div class="t">�Ƽ��γ�</div>
			<div class="m"><a href="<?php echo $this->_run_modifier("QS_train_curriculum", 'qishi_url', 'plugin', 1); ?>
">����>></a></div>
			<div class="clear"></div>
		</div>
		<div class="box_content">
			<?php echo tpl_function_qishi_curriculum_list(array('set' => "�б���:course,��ַ�:...,�γ�������:7,��ʾ��Ŀ:12,�б�ҳ:QS_train_agency_curriculum,�Ƽ�:1,����������:16"), $this);?>
			<?php if (count((array)$this->_vars['course'])): foreach ((array)$this->_vars['course'] as $this->_vars['list']): ?>
			<div class="cell">
				<div class="name"><a href="<?php echo $this->_vars['list']['course_url']; ?>
" target="_blank"><?php echo $this->_vars['list']['course_name']; ?>
</a></div>
				<span class="mr10"><?php echo $this->_vars['list']['district_cn']; ?>
</span>
				<span><?php echo $this->_vars['list']['starttime_cn']; ?>
</span><br />
				<div class="com_name_wage">
					<a class="com_name"><?php echo $this->_vars['list']['trainname']; ?>
</a><span class="wage"><?php echo $this->_vars['list']['train_expenses']; ?>
Ԫ</span>
				</div>
			</div>
			<?php endforeach; endif; ?>
		</div>
	</div>
	<!-- �Ƽ��γ� End-->
	<!-- ��ѵ���� -->
	<div class="hunterbox_container search_information ">
		<div class="h">
			<div class="t">��ѵ����</div>
			<div class="m"><a href="<?php echo $this->_run_modifier("QS_noticelist", 'qishi_url', 'plugin', 1); ?>
?id=2">����>></a></div>
			<div class="clear"></div>
		</div>
		<div class="box_content">
			<?php echo tpl_function_qishi_notice_list(array('set' => "�б���:notice,��ʾ��Ŀ:9,���ⳤ��:24,����:2,��ַ�:..."), $this);?>
			<?php if (count((array)$this->_vars['notice'])): foreach ((array)$this->_vars['notice'] as $this->_vars['list']): ?>
			<div class="c"><a href="<?php echo $this->_vars['list']['url']; ?>
" target="_blank" title="<?php echo $this->_vars['list']['title_']; ?>
"><?php echo $this->_vars['list']['title']; ?>
</a></div>
			<?php endforeach; endif; ?>
		</div>
		<!-- ��ѵ������� -->
		<div class="school-ad-block">
		<?php echo tpl_function_qishi_ad(array('set' => "��ʾ��Ŀ:1,��������:QS_trainindexright,�б���:ad_r,���ֳ���:12"), $this);?>
		<?php if ($this->_vars['ad_r']): ?>
		<div class="banner">
			<?php if (count((array)$this->_vars['ad_r'])): foreach ((array)$this->_vars['ad_r'] as $this->_vars['list']): ?>
				<a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img src="<?php echo $this->_vars['list']['img_path']; ?>
" alt="<?php echo $this->_vars['list']['img_explain_']; ?>
" width="320" height="151" border="0" /></a>
			<?php endforeach; endif; ?>
		</div>
		<?php endif; ?>
		</div>
		<!-- ��ѵ������� end-->
	</div>
	<!-- ��ѵ���� End-->
	<div class="clear"></div>
</div>
<!-- �Ƽ����� -->
<div class="hunterbox_container recommend_headhunter">
	<div class="h">
		<div class="t">�Ƽ�����</div>
		<div class="m"><a href="<?php echo $this->_run_modifier("QS_train_agency", 'qishi_url', 'plugin', 1); ?>
">����>></a></div>
		<div class="clear"></div>
	</div>
	<div class="box_content">
		<?php echo tpl_function_qishi_train_list(array('set' => "�б���:train,��ַ�:...,�Ƽ�:1,��ʾ��Ŀ:6,����������:15,����:rtime>desc"), $this);?>
		<?php if (count((array)$this->_vars['train'])): foreach ((array)$this->_vars['train'] as $this->_vars['list']): ?>
		<div class="cell">
			<div class='info'>
				<div class="photo"><img src="<?php if ($this->_vars['list']['photosrc']):  echo $this->_vars['list']['photosrc'];  else:  echo $this->_vars['QISHI']['site_template']; ?>
images/train_no_logo.gif<?php endif; ?>" width="214" height="73" border="0"></div>
			</div>
			<div class="open_jobs">
				<div class="c"><a href="<?php echo $this->_vars['list']['train_url']; ?>
"><?php echo $this->_vars['list']['trainname_']; ?>
</a></div>
				<div class="c">���ڵ�����<?php echo $this->_vars['list']['district_cn']; ?>
</div>
				<div class="c">�������ʣ�<?php echo $this->_vars['list']['nature_cn']; ?>
</div>
				<div class="c c801">���ڷ����Ŀγ̣�<?php echo $this->_vars['list']['countresume']; ?>
</div>
			</div>
		</div>
		<?php endforeach; endif; ?>
	</div>
</div>
<!-- �Ƽ����� End-->
<div class="talents_information">
	<!-- �������� -->
	<div class="hunterbox_container senior_talents">
		<div class="h">
			<div class="t">��������</div>
			<div class="m"><a href="<?php echo $this->_run_modifier("QS_train_curriculum", 'qishi_url', 'plugin', 1); ?>
">����>></a></div>
			<div class="clear"></div>
		</div>
		<div class="box_content h-440">
			<?php echo tpl_function_qishi_curriculum_list(array('set' => "�б���:course,��ַ�:...,�γ�������:7,��ʾ��Ŀ:12,�б�ҳ:QS_train_agency_curriculum,����ʱ��:60,�γ�������:7,����������:10"), $this);?>
			<?php if (count((array)$this->_vars['course'])): foreach ((array)$this->_vars['course'] as $this->_vars['list']): ?>
			<div class="cell">
				<div class="name"><a href="<?php echo $this->_vars['list']['course_url']; ?>
" target="_blank"><?php echo $this->_vars['list']['course_name']; ?>
</a></div>
				<span class="mr10"><?php echo $this->_vars['list']['district_cn']; ?>
</span>
				<span><?php echo $this->_vars['list']['starttime_cn']; ?>
</span><br />
				<div class="com_name_wage">
					<a class="com_name"><?php echo $this->_vars['list']['trainname']; ?>
</a><span class="wage"><?php echo $this->_vars['list']['train_expenses']; ?>
Ԫ</span>
				</div>
			</div>
			<?php endforeach; endif; ?>
		</div>
	</div>
	<!-- �������� End-->
	<!-- �Ƽ���ʦ -->
	<div class="hunterbox_container search_information ">
		<div class="h">
			<div class="t">�Ƽ���ʦ</div>
			<div class="m"><a href="<?php echo $this->_run_modifier("QS_train_lecturer", 'qishi_url', 'plugin', 1); ?>
">����>></a></div>
			<div class="clear"></div>
		</div>
		<div class="box_content h-440">
			<?php echo tpl_function_qishi_train_lecturer_list(array('set' => "�б���:teacher,��ַ�:...,�Ƽ�:1,��ʾ��Ŀ:4,��ʦ������:6,�б�ҳ:QS_train_lecturershow,����:rtime>desc"), $this);?>
			<?php if (count((array)$this->_vars['teacher'])): foreach ((array)$this->_vars['teacher'] as $this->_vars['list']): ?>
			<div class="cell">
				<div class='info'>
					<div class="photo"><img src="<?php if ($this->_vars['list']['photosrc']):  echo $this->_vars['list']['photosrc'];  else:  echo $this->_vars['QISHI']['site_template']; ?>
images/lecturer_no_photo.gif<?php endif; ?>" width="59" height="80" border="0"></div>
					<div class="text">
						<div class="c"><a href="<?php echo $this->_vars['list']['teacher_url']; ?>
"><?php echo $this->_vars['list']['teachername']; ?>
</a><?php echo $this->_vars['list']['refreshtime_cn']; ?>
</div>
						<div class="c">��ҵ���ޣ�<?php echo $this->_vars['list']['age']; ?>
 ��</div>
						<div class="c">���ڵ�����<?php echo $this->_vars['list']['district_cn']; ?>
</div>
					</div>
				</div>
			</div>
			<?php endforeach; endif; ?>
		</div>
	</div>
	<!-- �Ƽ���ʦ End-->
	<div class="clear"></div>
</div>
	<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
	<script type="text/javascript">
				showmenu('#typeid_cn','#trainshowdiv',"#typeid");
				$("#search_go").click(function() {
					 var patrn1=/^(������ؼ���)/i;	 
					 var key1=$("#keyinput").val();
					if (patrn1.exec(key1))
					{
						 $(this).css('color','#000000').val('');
						 $("#keyinput").val('');
						 key1='';
					}
				$.get("<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/ajax_search_location.php", {"act":"QS_train_curriculum","key":key1,"category":$("#typeid").val(),"page":1},
						function (data,textStatus)
						 {
							 window.location.href=data;
						 }
					);
				});
				//ģ��select
				function showmenu(menuID,showID,inputname)
				{
					$(menuID).click(function(){
						$(menuID).blur();
						$(menuID).parent("div").css("position","relative");
						$(showID).slideToggle("fast");
						//���ɱ���
						$(menuID).parent("div").before("<div class=\"menu_bg_layer\"></div>");
						$(".menu_bg_layer").height($(document).height());
						$(".menu_bg_layer").css({ width: $(document).width(), position: "absolute", left: "0", top: "0" , "z-index": "0", "background-color": "#ffffff"});
						$(".menu_bg_layer").css("opacity","0");
						//���ɱ�������
						$(showID+" li").live("click",function(){
							$(menuID).val($(this).attr("title"));
							$(inputname).val($(this).attr("id"));
							$(".menu_bg_layer").hide();
							$(showID).hide();
							$(menuID).parent("div").css("position","");	
							$(this).css("background-color","");
						});

								$(".menu_bg_layer").click(function(){
									$(".menu_bg_layer").hide();
									$(showID).hide();
									$(menuID).parent("div").css("position","");
								});
						$(showID+" li").unbind("hover").hover(
						function()
						{
						$(this).css("background-color","#F5F5F5");
						},
						function()
						{
						$(this).css("background-color","");

						}
						);
					});
				}
			</script>
</body>
</html>