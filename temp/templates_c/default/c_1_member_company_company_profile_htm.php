<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_get_classify.php'); $this->register_function("qishi_get_classify", "tpl_function_qishi_get_classify",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.default.php'); $this->register_modifier("default", "tpl_modifier_default",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:35 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php echo $this->_vars['title']; ?>
</title>
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<meta name="author" content="�Һ�«" />
<meta name="copyright" content="zhaohulu.com" />
<meta name="renderer" content="webkit"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/user_common.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/user_company.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/jobs.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/ui-dialog.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min-common.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.validate.min.js" type='text/javascript' language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
data/cache_classify.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.company.selectlayer.js" type='text/javascript' language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.vtip-min.js" type="text/javascript" language="javascript"></script>
<link rel="stylesheet" href="<?php echo $this->_vars['QISHI']['site_template']; ?>
kindeditor/themes/default/default.css" />
<script charset="utf-8" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
kindeditor/lang/zh_CN.js"></script>
<style>
	{width: 100%;}
</style>
<script type="text/javascript">
$(document).ready(function()
{	
	$("#Form1 input, #Form1 textarea, #Form1 select").each(function(index, el) {
		$(this).attr('_value', jQuery(this).val());
	});
	//��beforeunload�¼�
	function is_form_changed() {
		//���ҳ���Ƿ��б��水ť
		var t_save = $("#submitsave");
		if(t_save.length>0) {
			var is_changed = false;
			$("#Form1 input, #Form1 textarea, #Form1 select").each(function(index, el) {
				var _v = $(this).attr('_value');
				if(typeof(_v) == 'undefined') {
					_v = '';
				}
				if(_v != jQuery(this).val()) {
					is_changed = true;
				}
			});
			return is_changed;
		}
		return false;
	}
	if ($.browser.msie){
	    window.onunload = function(){
	        return "�����ڱ༭��������δ���棬ȷ��Ҫ�뿪��ҳ��";
	    }
	}
	else{
	    window.onbeforeunload = function(){
	        if (is_form_changed()) {
				return '�����ڱ༭��������δ���棬ȷ��Ҫ�뿪��ҳ��';
			}
	    }
	}
	allaround('<?php echo $this->_vars['QISHI']['site_dir']; ?>
');
	// ������ҵ�������
	trade_filldata("#trad_list", QS_trade, "#aui_outer", "#result-list-trade", "#trade_result_show", "#trade", "<?php echo $this->_vars['QISHI']['site_dir']; ?>
");
	// ���ڵ����������
	city_filldata("#city_list", QS_city_parent, QS_city, "#result-list-city", "#aui_outer_city", "#city_result_show", "#district", "<?php echo $this->_vars['QISHI']['site_dir']; ?>
");
	// ���ڽֵ�ѡ��
	street_filldata("#street_list", "#street");
	//����չ��
	$("#model").click(function(){	$("#cp").toggle()});
	menuDown("#nature_menu","#nature","#nature_cn","#menu1","218px");
	menuDown("#scale_menu","#scale","#scale_cn","#menu2","218px");
	menuDown("#currency_menu","","#currency","#menu3","78px");
	function menuDown(menuId,input,input_cn,menuList,width){
		$(menuId).click(function(){
			$(menuList).css("width",width);
			$(menuList).slideDown('fast');
			//���ɱ���
			$(menuId).parent("div").before("<div class=\"menu_bg_layer\"></div>");
			$(".menu_bg_layer").height($(document).height());
			$(".menu_bg_layer").css({ width: $(document).width(), position: "absolute", left: "0", top: "0" , "z-index": "0", "background-color": "#ffffff"});
			$(".menu_bg_layer").css("opacity","0");
			$(".menu_bg_layer").click(function(){
				$(".menu_bg_layer").remove();
				$(menuList).slideUp("fast");
				$(menuId).parent("div").css("position","");
			});
		});

		$(menuList+" li").click(function(){
			var id = $(this).attr("id");
			var title = $(this).attr("title");
			$(input).val(id);
			$(input_cn).val(title);
			$(menuId).html(title);
			$(input).parent().find('.input_text_200_bg').removeClass('error');
			$(input).parent().next().find('.error').hide();
			$(menuList).slideUp('fast');
			$(".menu_bg_layer").remove();
		});
	}
	// �ֻ�������֤   
	jQuery.validator.addMethod("isPhoneNumber", function(value, element) {   
	    var tel = /^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|18[0-9]{9}|17[0-9]{9}$/;
	    return this.optional(element) || (tel.test(value));
	}, "����ȷ��д�ֻ�����");
	// ������֤   
	jQuery.validator.addMethod("isareacode", function(value, element) {   
	    var tel = /^0\d{2,3}$/;
	    return this.optional(element) || (tel.test(value));
	}, "����ȷ��д�绰����");
	// �绰����֤   
	jQuery.validator.addMethod("isphone", function(value, element) {   
	    var tel = /^\d{6,11}$/;
	    return this.optional(element) || (tel.test(value));
	}, "����ȷ��д�绰����");
	// �ֻ�����֤   
	jQuery.validator.addMethod("isextensioncode", function(value, element) {   
	    var tel = /^\d{0,6}$/;
	    return this.optional(element) || (tel.test(value));
	}, "����ȷ��д�绰����");
	// �ֻ��ź͹̶��绰��ѡһ   
	jQuery.validator.addMethod("isHaveLandlin", function(value, element) {   
	    var landval = $.trim($('#telephone').val());
	    return value.length > 0 || landval.length > 0;
	}, "����д�ֻ���̻�����ѡһ����");
	$("#Form1").validate({
 //debug: true,
// onsubmit:false,
//onfocusout :true,
   rules:{
   companyname:{
    required: true,
	minlength:2,
    maxlength:30
   },
   nature: "required",
   trade: "required",
   scale: "required",
   district: "required",
   contents:{
	required: true,
	minlength:30
   },
   contact:{
   required: true
   },
	  telephone: {
	   isPhoneNumber:true
	},
	  landline_tel_first: {
	   isareacode:true
	},
	  landline_tel_next: {
	   isHaveLandlin:true,
	   isphone:true
	},
	  landline_tel_last: {
	   isextensioncode:true
	},
	  address: "required", 
	   email: {
	   required:true,
	   email:true
	   }
	},
    messages: {
    companyname: {
    required: "��������ҵ����",
    minlength: jQuery.format("��ҵ���Ʋ���С��{0}���ַ�"),
	maxlength: jQuery.format("��ҵ���Ʋ��ܴ���{0}���ַ�")
   },
   nature: {
    required: jQuery.format("��ѡ����ҵ����")
   },
    trade: {
    required: jQuery.format("��ѡ��������ҵ")
   },
    district: {
    required: jQuery.format("��ѡ�����ڵ���")
   },
   scale: {
    required: jQuery.format("��ѡ����ҵ��ģ")
   },
   contents: {
	required: jQuery.format("����д��˾���"),
	minlength: jQuery.format("��˾������ݲ���С��{0}���ַ�")
   },
   contact: {
    required: jQuery.format("����д��ϵ��")
   },
    telephone: {
	isPhoneNumber: jQuery.format("����ȷ��д�ֻ�����")
   },
    landline_tel_first: {
	isareacode: jQuery.format("����ȷ��д����")
   },
    landline_tel_next: {
    	isHaveLandlin: jQuery.format("����д�ֻ���̻�����ѡһ����"),
	isphone: jQuery.format("����ȷ��д�绰����")
   },
    landline_tel_last: {
	isextensioncode: jQuery.format("����ȷ��д�ֻ���")
   },
   address: {
    required: jQuery.format("����д��ϵ��ַ")
   },
   email: {
    required: jQuery.format("����д��������"),
	email: jQuery.format("����ȷ��д��������")
   } 
  },
  errorPlacement: function(error, element) {
    if ( element.is(":radio") )
        error.appendTo( element.parent().next().next() );
    else if ( element.is(":checkbox") )
        error.appendTo ( element.next());
    else
        error.appendTo(element.parent().next());
    	element.parent().find('.input_text_200_bg').addClass('error');
	},
	success: function (label) {
        label.parent().prev().find('.input_text_200_bg').removeClass('error');
    },
	submitHandler: function(form) {
		window.onbeforeunload = null;
		form.submit();
	}
    });
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

<div class="page_location link_bk">��ǰλ�ã�<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
">��ҳ</a> > <a href="<?php echo $this->_vars['userindexurl']; ?>
">��Ա����</a> > ��ҵ����</div>

<div class="usermain">
  <div class="leftmenu link_bk">
   <?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("member_company/left.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>	
  </div>
  <div class="rightmain">
	<div class="bbox1">	
	  <div class="addjob link_bk">
	    <div class="topnav">
			<div class="titleH1" style="+padding-top:0;margin-top:0;">
				<div class="h1-title">��ҵ����</div>
			</div>
			<div class="navs">
				<a href="?act=company_profile" class="se">��������</a>
				<a href="?act=company_logo">��ҵLogo</a>
				<a href="?act=company_news">��ҵ��̬</a>
				<a href="?act=company_img">��ҵ���</a>
				<a href="?act=company_map_open">��ͼ��ע</a>
				<div class="clear"></div>
			</div>
		</div>
		<form id="Form1" name="Form1" method="post" action="?act=company_profile_save" >
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall" style="margin-top:20px;">
		  <tr>
			<td width="125" align="right"><span class="nec">��ҵ����</span>��</td>
			<td width="230"><?php if ($this->_vars['company_profile']['audit'] == 1):  echo $this->_vars['company_profile']['companyname']; ?>
<span class="vtip c" title="���Ѿ�ͨ��Ӫҵִ����֤��Ҫ�޸���ҵ������ϵ����Ա��"></span><?php else: ?><input name="companyname" type="text" class="input_text_200" id="companyname" maxlength="80"   value="<?php echo $this->_vars['company_profile']['companyname']; ?>
"/><?php endif; ?></td>
			<td>&nbsp;</td>
		  </tr>
		  <tr>
			<td width="125" align="right"><span class="nec">��ҵ����</span>��</td>
			<td width="230">
				<div style="position:relateve;">
             	 	<div id="nature_menu" class="input_text_200_bg"><?php echo $this->_run_modifier($this->_vars['company_profile']['nature_cn'], 'default', 'plugin', 1, "��ѡ��"); ?>
</div>	
             	 	<div class="menu" id="menu1">
	              		<ul>
	              			<?php echo tpl_function_qishi_get_classify(array('set' => "�б���:c_nature,����:QS_company_type"), $this);?>
	              			<?php if (count((array)$this->_vars['c_nature'])): foreach ((array)$this->_vars['c_nature'] as $this->_vars['list']): ?>
	              			<li id="<?php echo $this->_vars['list']['id']; ?>
" title="<?php echo $this->_vars['list']['categoryname']; ?>
"><?php echo $this->_vars['list']['categoryname']; ?>
</li>
	              			<?php endforeach; endif; ?>
	              		</ul>
	              	</div>
	            </div>				
             	<input name="nature" type="hidden" id="nature" value="<?php echo $this->_vars['company_profile']['nature']; ?>
" />
             	<input name="nature_cn" type="hidden" id="nature_cn" value="<?php echo $this->_vars['company_profile']['nature_cn']; ?>
" />
			</td>
			<td>&nbsp;</td>
		  </tr>
		  <tr class="jobmain">
			<td align="right"><span class="nec">������ҵ</span>��</td>
			<td id="jobsTrad" style="position:relative;z-index:1;">
				<div class="input_text_200_bg ucc-default" id="tradText"><?php echo $this->_run_modifier($this->_vars['company_profile']['trade_cn'], 'default', 'plugin', 1, "��ѡ��"); ?>
</div>
				<!-- ������ҵ������ -->
				<div class="aui_outer" id="aui_outer">
					<table class="aui_border">
						<tbody>
							<tr>
								<td class="aui_c">
									<div class="aui_inner">
										<table class="aui_dialog">
											<tbody>
												<tr>
													<td class="aui_main">
														<div class="aui_content">
															<div class="items jquery-localdata">
																<div class="selector-header"><span class="selector-title">��ҵѡ��</span><div></div><span class="selector-close">X</span><div class="clear"></div></div>

																<div class="item-table">
																	<table class="options-table options-table-7">
																		<tbody class="item-list"><tr><td class="bno"><table><tbody id="trad_list">
																			<!-- �б����� -->
																		</tbody></table></td></tr>
																		</tbody>
																	</table>
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
				<!-- ������ҵ������ End-->
				<input type="hidden" id="trade" name="trade" value="<?php echo $this->_vars['company_profile']['trade']; ?>
">
				<input type="hidden" id="trade_cn" name="trade_cn" value="<?php echo $this->_vars['company_profile']['trade_cn']; ?>
">
			</td>
			<td>&nbsp;</td>
		  </tr>
		   <tr>
			<td align="right"><span class="nec">��ҵ��ģ</span>��</td>
			<td>
				<div style="position:relateve;">
             	 	<div id="scale_menu" class="input_text_200_bg"><?php echo $this->_run_modifier($this->_vars['company_profile']['scale_cn'], 'default', 'plugin', 1, "��ѡ��"); ?>
</div>	
             	 	<div class="menu" id="menu2">
	              		<ul>
	              			<?php echo tpl_function_qishi_get_classify(array('set' => "�б���:c_scale,����:QS_scale"), $this);?>
	              			<?php if (count((array)$this->_vars['c_scale'])): foreach ((array)$this->_vars['c_scale'] as $this->_vars['list']): ?>
	              			<li id="<?php echo $this->_vars['list']['id']; ?>
" title="<?php echo $this->_vars['list']['categoryname']; ?>
"><?php echo $this->_vars['list']['categoryname']; ?>
</li>
	              			<?php endforeach; endif; ?>
	              		</ul>
	              	</div>
	            </div>				
             	 <input name="scale" type="hidden" id="scale" value="<?php echo $this->_vars['company_profile']['scale']; ?>
" />
             	 <input name="scale_cn" type="hidden" id="scale_cn" value="<?php echo $this->_vars['company_profile']['scale_cn']; ?>
" />
			</td>
			<td>&nbsp;</td>
		  </tr>
		   <tr>
			<td align="right">ע���ʽ�</td>
			<td colspan="2">
			  <table border="0" cellpadding="0" cellspacing="0" >
	              <tr>
	                <td width="80" style="padding:0px"><input name="registered" type="text" class="input_text_100" id="registered" maxlength="20"   value="<?php echo $this->_vars['company_profile']['registered']; ?>
"/></td>
	                <td width="20" align="center" style="padding:0px">��</td>
	                <td width="80"  style="padding:0px">
	                	<div style="position:relateve;">
		             	 	<div id="currency_menu" class="input_text_60_bg"><?php echo $this->_run_modifier($this->_vars['company_profile']['currency'], 'default', 'plugin', 1, "�����"); ?>
</div>	
		             	 	<div class="menu" id="menu3">
			              		<ul>
			              			<li title="�����">�����</li>
			              			<li title="��Ԫ">��Ԫ</li>
			              		</ul>
			              	</div>
			            </div>				
		             	 <input name="currency" type="hidden" id="currency" value="<?php echo $this->_run_modifier($this->_vars['company_profile']['currency'], 'default', 'plugin', 1, "�����"); ?>
" />
	                </td>
	              </tr>
	            </table>
			</td>
		  </tr>
		  <tr class="jobmain">
			<td width="125" align="right"><span class="nec">���ڵ���</span>��</td>
			<td width="230" id="jobsCity" style="position:relative;">
				<div class="input_text_200_bg ucc-default" id="cityText"><?php echo $this->_vars['company_profile']['district_cn']; ?>
</div>
				<!-- ���ڵ��������� -->
				<div class="aui_outer" id="aui_outer_city">
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
																<div class="selector-header"><span class="selector-title">����ѡ��</span><div></div><span class="selector-close">X</span><div class="clear"></div></div>

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
				<!-- ���ڵ��������� End-->
				<input id="district" type="hidden" value="<?php echo $this->_vars['company_profile']['district']; ?>
" name="district">
	            <input id="sdistrict" type="hidden" value="<?php echo $this->_vars['company_profile']['sdistrict']; ?>
" name="sdistrict">
	            <input id="districtID" type="hidden" value="">
	            <input name="district_cn" id="district_cn" type="hidden" value="<?php echo $this->_vars['company_profile']['district_cn']; ?>
" />
			</td>
			<td>&nbsp;</td>
		  </tr>
		  <tr>
			<td width="125" align="right">���ڽֵ���</td>
			<td width="230" style="position:relative;">
				<div class="input_text_200_bg ucc-default" id="streetText"><?php echo $this->_run_modifier($this->_vars['company_profile']['street_cn'], 'default', 'plugin', 1, "δ��ע�ֵ�"); ?>
</div>
				<!-- ���ڽֵ������� -->
				<div class="aui_outer" id="aui_outer_street">
					<table class="aui_border">
						<tbody>
							<tr>
								<td class="aui_c">
									<div class="aui_inner">
										<table class="aui_dialog">
											<tbody>
												<tr>
													<td class="aui_main">
														<div class="aui_content">
															<div class="items jquery-localdata">
																<div class="selector-header"><span class="selector-title">���ڽֵ�</span><div></div><span class="selector-close">X</span><div class="clear"></div></div>

																<div class="item-table">
																	<table class="options-table options-table-7">
																		<tbody class="item-list"><tr><td class="bno"><table><tbody id="street_list"><tr>
																			<!-- �б����� -->
																			<?php echo tpl_function_qishi_get_classify(array('set' => "����:QS_street,�б���:list"), $this);?>
																			<?php if (isset($this->_sections['lis'])) unset($this->_sections['lis']);
$this->_sections['lis']['name'] = 'lis';
$this->_sections['lis']['loop'] = is_array($this->_vars['list']) ? count($this->_vars['list']) : max(0, (int)$this->_vars['list']);
$this->_sections['lis']['show'] = true;
$this->_sections['lis']['max'] = $this->_sections['lis']['loop'];
$this->_sections['lis']['step'] = 1;
$this->_sections['lis']['start'] = $this->_sections['lis']['step'] > 0 ? 0 : $this->_sections['lis']['loop']-1;
if ($this->_sections['lis']['show']) {
	$this->_sections['lis']['total'] = $this->_sections['lis']['loop'];
	if ($this->_sections['lis']['total'] == 0)
		$this->_sections['lis']['show'] = false;
} else
	$this->_sections['lis']['total'] = 0;
if ($this->_sections['lis']['show']):

		for ($this->_sections['lis']['index'] = $this->_sections['lis']['start'], $this->_sections['lis']['iteration'] = 1;
			 $this->_sections['lis']['iteration'] <= $this->_sections['lis']['total'];
			 $this->_sections['lis']['index'] += $this->_sections['lis']['step'], $this->_sections['lis']['iteration']++):
$this->_sections['lis']['rownum'] = $this->_sections['lis']['iteration'];
$this->_sections['lis']['index_prev'] = $this->_sections['lis']['index'] - $this->_sections['lis']['step'];
$this->_sections['lis']['index_next'] = $this->_sections['lis']['index'] + $this->_sections['lis']['step'];
$this->_sections['lis']['first']	  = ($this->_sections['lis']['iteration'] == 1);
$this->_sections['lis']['last']	   = ($this->_sections['lis']['iteration'] == $this->_sections['lis']['total']);
?>
																			<?php if ($this->_sections['lis']['index'] % 6 == 0 && $this->_sections['lis']['index'] != 0): ?>
																				</tr><tr><td><label class="selectra" data="<?php echo $this->_vars['list'][$this->_sections['lis']['index']]['c_id']; ?>
,<?php echo $this->_vars['list'][$this->_sections['lis']['index']]['categoryname']; ?>
"><?php echo $this->_vars['list'][$this->_sections['lis']['index']]['categoryname']; ?>
</label></td>
																			<?php else: ?>
																				<td><label class="selectra" data="<?php echo $this->_vars['list'][$this->_sections['lis']['index']]['c_id']; ?>
,<?php echo $this->_vars['list'][$this->_sections['lis']['index']]['categoryname']; ?>
"><?php echo $this->_vars['list'][$this->_sections['lis']['index']]['categoryname']; ?>
</label></td>
																			<?php endif; ?>
																			<?php endfor; endif; ?>
																		</tr></tbody></table></td></tr>
																		</tbody>
																	</table>
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
				<!-- ���ڽֵ������� End-->
				<input type="hidden" id="street" name="street" value="<?php echo $this->_vars['company_profile']['street']; ?>
">
				<input type="hidden" id="street_cn" name="street_cn" value="<?php echo $this->_vars['company_profile']['street_cn']; ?>
">
			</td>
			<td>&nbsp;</td> 
		  </tr>
		  <tr>
			<td width="125" align="right"><span class="nec">��ϵ��</span>��</td>
			<td width="230"><input name="contact" type="text" class="input_text_200" id="contact" maxlength="80"   value="<?php echo $this->_vars['company_profile']['contact']; ?>
"/></td>
			<td><label><input name="contact_show" type="checkbox" value="1" <?php if ($this->_vars['company_profile']['contact_show'] <> "0"): ?>checked="checked"<?php endif; ?>/>&nbsp;��ϵ������ҵ����ҳ��ʾ</label></td>
		  </tr>
		  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			  <tr>
				<td width="125" align="right"><span class="nec">�̶��绰</span>��</td>
				<td width="280">
					<input type="text" id="landline_tel_first" name="landline_tel_first" class="input_text_33 input_text" id="landline_tel_first" maxlength="4"   value="<?php if ($this->_vars['company_profile']['landline_tel_first']):  echo $this->_vars['company_profile']['landline_tel_first'];  endif; ?>"/>&nbsp;-
					<input id="landline_tel_next" name="landline_tel_next" type="text" class="input_text_90 input_text" maxlength="11"   value="<?php if ($this->_vars['company_profile']['landline_tel_next']):  echo $this->_vars['company_profile']['landline_tel_next'];  endif; ?>"/>&nbsp;-
					<input id="landline_tel_last" name="landline_tel_last" type="text" class="input_text_49 input_text" maxlength="6"   value="<?php if ($this->_vars['company_profile']['landline_tel_last']):  echo $this->_vars['company_profile']['landline_tel_last'];  endif; ?>"/>
				</td>
				<td>&nbsp;</td>
			  </tr>
		  </table>
		  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			  <tr>
				<td width="125" align="right"><span>��ϵ�ֻ�</span>��</td>
				<td width="230"><input name="telephone" type="text" class="input_text_200" id="telephone" maxlength="80"   value="<?php if ($this->_vars['company_profile']['telephone']):  echo $this->_vars['company_profile']['telephone'];  else:  echo $this->_vars['user']['mobile'];  endif; ?>"/></td>
				<td><label><input name="telephone_show" type="checkbox" value="1" <?php if ($this->_vars['company_profile']['telephone_show'] <> "0"): ?>checked="checked"<?php endif; ?>/>&nbsp;��ϵ�绰����ҵ��������ʾ</label>&nbsp;</td>
			  </tr>
		  </table>
		  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td width="125" align="right"><span class="nec">��ϵ����</span>��</td>
			<td width="230"><input name="email" type="text" class="input_text_200" id="email" maxlength="80"   value="<?php if ($this->_vars['company_profile']['email']):  echo $this->_vars['company_profile']['email'];  else:  echo $this->_vars['user']['email'];  endif; ?>"/></td>
			<td><label><input name="email_show" type="checkbox" value="1" <?php if ($this->_vars['company_profile']['email_show'] <> "0"): ?>checked="checked"<?php endif; ?>/>&nbsp;��ϵ��������ҵ��������ʾ</label></td>
		  </tr>
		   <tr>
			<td width="125" align="right">��˾��ַ��</td>
			<td width="230"><input name="website" type="text" class="input_text_200" id="website" maxlength="80" value="<?php echo $this->_vars['company_profile']['website']; ?>
"/></td>
			<td>&nbsp;</td>
		  </tr>
	    </table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td width="125" align="right"><span class="nec">��ϸ��ַ</span>��</td>
			<td width="500"><input name="address" type="text" class="input_text_500" id="address" maxlength="80"   value="<?php echo $this->_vars['company_profile']['address']; ?>
"/></td>
			<td>&nbsp;</td>
		  </tr>
		  <tr>
			<td width="125" align="right" valign="top"><span class="nec">��˾���</span>��</td>
			<td width="500">
				<textarea id="contents" name="contents" style="width:600px;height:300px;visibility:hidden;"><?php echo $this->_vars['company_profile']['contents']; ?>
</textarea>
				<script type="text/javascript">
					var editor;
					KindEditor.ready(function(K) {
						editor = K.create('textarea[name="contents"]', {
							allowFileManager : false,
							width:520,
							height:250,
							afterBlur: function(){this.sync();}
						});
					});
				</script>
			</td>
			<td>&nbsp;</td>
		  </tr>
		  <tr>
			<td width="125" align="right" valign="top">&nbsp;</td>
			<td width="500" align="right"><a href="javascript:void(0);" id="model" style="display:inline-block;margin-top:-18px;_margin-top:-15px;color:#0180CF;*margin-top:5px;">[����]</a></td>
			<td>&nbsp;</td>
		  </tr>
	    </table>
		  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall s" style="margin-top:5px;display:none" id="cp">
		  <tr>
			<td width="125" align="right" valign="top">��˾��鷶����</td>
			<td width="500" bgcolor="#F5F5F5" style="line-height:22px;padding-left:10px;padding-right:10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;XXX�������޹�˾������2000��,��XXXͶ�����޹�˾������XXX��˾�����˽������������ҵ,����������XXXר������,רҵ����XX�о�����������Ӧ��.<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��˾2002�걻XX�϶�Ϊ���¼�����ҵ,��Ŀǰ����Ψһ��һ�Ҽ�����XXX������X����������,��Ҫ��Ʒ�и�XX,XX�в�ƷXXX��.<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ŀǰ,XX��˾ӵ�����ʲ�X��Ԫ,�����ӹ�����ռ��XX��Ķ,����һ����XX����ֵ�XX��������,������Ӫ������Ȩ,��Ŀǰ�������ʵ��XX������Ӧ��֮һ.<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;���Ƚ�,����,���õļ�������XX��ҵ ,��X��˾����ҵ��ּ,��˾�������¼�����ͬʱ,���ϸ���XXXʵ�������Ӧ�÷�������о����ƹ�����XXX,����ȫ��,����������XX��˾�ķ�չĿ��,ĿǰXXX��˾�Ĳ�Ʒ����X����ʹ����,�Ѿ������˽���,�㽭,����,����,����,����,�Ĵ�,����,�㶫��ʮ���ʡ��,ͬʱ��˾������XXX�Ѿ���������,���ڶ���˹,������˹̹��ʼӦ��,�ѳɹ���������г�.</td>
			<td>&nbsp;</td>
		  </tr>
		  </table>
		  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall stab">
		  <?php if ($this->_vars['company_jobs']): ?>
		  <tr>
			<td align="right" width="125">&nbsp;</td>
			<td width="230"><label style="color:#FF9900;"><input name="telephone_to_jobs" type="checkbox" value="1"/> &nbsp;�޸���ϵ��ʽͬ����ְλ</label></td>
			<td>&nbsp;</td>
		  </tr>
		  <?php endif; ?>
		  </table>
		  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td align="right" width="125">&nbsp;</td>
			<td width="130"><input type="submit" value="����" id="save_profile" class="but100lan" onclick="$(window).unbind('beforeunload');"/></td>
			<td>&nbsp;</td>
		  </tr>
		  </table>
		</form> 
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