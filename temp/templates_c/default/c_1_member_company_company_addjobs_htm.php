<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_get_classify.php'); $this->register_function("qishi_get_classify", "tpl_function_qishi_get_classify",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:36 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php echo $this->_vars['title']; ?>
</title>
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<meta name="author" content="找葫芦" />
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
js/jquery.validate.min.js" type='text/javascript' language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min-common.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
data/cache_classify.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.company.selectlayer.js" type='text/javascript' language="javascript"></script>
<link rel="stylesheet" href="<?php echo $this->_vars['QISHI']['site_template']; ?>
kindeditor/themes/default/default.css" />
<script charset="utf-8" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
kindeditor/lang/zh_CN.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
$("#Form1 input, #Form1 textarea, #Form1 select").each(function(index, el) {
	$(this).attr('_value', jQuery(this).val());
});
//绑定beforeunload事件
function is_form_changed() {
	//检测页面是否有保存按钮
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
        return "您正在编辑的内容尚未保存，确定要离开此页吗？";
    }
}
else{
    window.onbeforeunload = function(){
        if (is_form_changed()) {
			return '您正在编辑的内容尚未保存，确定要离开此页吗？';
		}
    }
}
allaround('<?php echo $this->_vars['QISHI']['site_dir']; ?>
');
// 职位类别填充数据 
job_filldata("#job_list", QS_jobs_parent, QS_jobs, "#result-list-job", "#aui_outer_job", "#job_result_show", "#topclass", "<?php echo $this->_vars['QISHI']['site_dir']; ?>
");
// 职位亮点填充数据
tag_filldata("#tag_list", QS_jobtag, "#aui_outer_tag", "#result-list-tag", "#trade_result_show", "#tag", "<?php echo $this->_vars['QISHI']['site_dir']; ?>
");
//工作性质单选
var nature_obj = $("#nature_radio .input_radio").first();
$("#nature").val(nature_obj.attr("id"));
$("#nature_cn").val(nature_obj.text());
$("#nature_radio .input_radio").click(function(){
		$("#nature").val($(this).attr('id'));
		$("#nature_cn").val($(this).text());
		$("#nature_radio .input_radio").removeClass("select");
		$(this).addClass("select");
});
//性别单选
$("#sex_radio .input_radio").click(function(){
		$("#sex").val($(this).attr('id'));
		$("#sex_cn").val($(this).text());
		$("#sex_radio .input_radio").removeClass("select");
		$(this).addClass("select");
});
menuDown("#education_menu","#education","#education_cn","#menu1","218px");
menuDown("#experience_menu","#experience","#experience_cn","#menu2","218px");
menuDown("#wage_menu","#wage","#wage_cn","#menu3","218px");
menuDown("#tpl_menu","","","#menu4","218px");
function menuDown(menuId,input,input_cn,menuList,width){
	$(menuId).click(function(){
		$(menuList).css("width",width);
		$(menuList).slideDown('fast');
		//生成背景
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
showagebox("#minage_div","#minage");
showagebox("#maxage_div","#maxage");
function showagebox(divname,inputname)
{
	$(divname).click(function(){
		var inputdiv=$(this);
		$(inputdiv).parent("div").before("<div class=\"menu_bg_layer\"></div>");
		$(".menu_bg_layer").height($(document).height());
		$(".menu_bg_layer").css({ width: $(document).width(), position: "absolute", left: "0", top: "0" , "z-index": "0"});
		$(inputdiv).parent("div").css("position","relative");
		
		var y=16;
		var ymax=60;
		htm="<div class=\"showyearbox yearlist\">";		
		htm+="<ul>";
		for (i=y;i<=ymax;i++)
		{
		htm+="<li title=\""+i+"\">"+i+"岁</li>";
		}
		htm+="<div class=\"clear\"></div>";
		htm+="</ul>";
		htm+="</div>";
		$(inputdiv).blur();
		if ($(inputdiv).parent("div").find(".showyearbox").html())
		{
			$(inputdiv).parent("div").children(".showyearbox.yearlist").slideToggle("fast");
		}
		else
		{
			$(inputdiv).parent("div").append(htm);
			$(inputdiv).parent("div").children(".showyearbox.yearlist").slideToggle("fast");
		}
		//
		$(inputdiv).parent("div").children(".yearlist").find("li").unbind("click").click(function()
		{
			var tt = $(this).attr("title");
			$(inputname).val(tt);
			$(inputdiv).html(tt);
			if (inputname == "#minage") {
				var maxval = $("#maxage").val();
				if(maxval) {
					tt >= maxval ? $("#ageerr").show() : $("#ageerr").hide();
				}
			}
			if (inputname == "#maxage") {
				var minval = $("#minage").val();
				if(minval) {
					tt <= minval ? $("#ageerr").show() : $("#ageerr").hide();
				}
			}
			$(inputdiv).parent("div").children(".yearlist").hide();
			$(".menu_bg_layer").remove();
		});	
		//
		$(".showyearbox>ul>li").hover(
		function()
		{
		$(this).css("background-color","#DAECF5");
		$(this).css("color","#ff0000");
		},
		function()
		{
		$(this).css("background-color","");
		$(this).css("color","");
		}
		);
		//
		$(".menu_bg_layer").click(function(){
			$(".menu_bg_layer").hide();
			$(inputdiv).parent("div").css("position","");
			$(inputdiv).parent("div").find(".showyearbox").hide();	
		});
	});
}
// 手机号码验证   
var mobile = /^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|18[0-9]{9}|17[0-9]{9}$/;
var current_tel = "<?php echo $this->_vars['company_profile']['telephone']; ?>
";
if(!mobile.test(current_tel)){
	$("#receiveit").attr('disabled','disabled');
    $("#receiveit").attr('checked',false);
}
// 手机号码验证   
jQuery.validator.addMethod("isPhoneNumber", function(value, element) {   
    var mobile = /^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|18[0-9]{9}|17[0-9]{9}$/;
    if(this.optional(element) || (mobile.test(value))){
    	$("#receiveit").removeAttr('disabled');
    }else{
    	$("#receiveit").attr('disabled','disabled');
    	$("#receiveit").attr('checked',false);
    }
    return this.optional(element) || (mobile.test(value));
}, "请正确填写联系手机");
// 区号验证   
jQuery.validator.addMethod("isareacode", function(value, element) {   
    var tel = /^0\d{2,3}$/;
    return this.optional(element) || (tel.test(value));
}, "请正确填写电话号码");
// 电话号验证   
jQuery.validator.addMethod("isphone", function(value, element) {   
    var tel = /^\d{6,11}$/;
    return this.optional(element) || (tel.test(value));
}, "请正确填写电话号码");
// 分机号验证   
jQuery.validator.addMethod("isextensioncode", function(value, element) {   
    var tel = /^\d{0,6}$/;
    return this.optional(element) || (tel.test(value));
}, "请正确填写电话号码");
// 手机号和固定电话二选一   
jQuery.validator.addMethod("isHaveLandlin", function(value, element) {   
    var landval = $.trim($('#telephone').val());
    return value.length > 0 || landval.length > 0;
}, "请填写手机或固话，二选一即可");
$("#Form1").validate({
 //debug: true,
// onsubmit:false,
//onfocusout :true,
   rules:{
   jobs_name:{
    required: true,
	minlength:2,
    maxlength:30
   },
   amount: {
   	required: true,
	range:[0,9999]
   },
   category: "required",
   subsite_id: "required",
   wage: "required",
   education: "required",
   experience: "required",
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
	   <?php if ($this->_vars['verify_addjob'] == "1"): ?>
		postcaptcha:{
		IScaptchastr: true,
		required: true,
		remote:{     
		url:"<?php echo $this->_vars['QISHI']['site_dir']; ?>
include/imagecaptcha.php",     
		type:"post",    
		data:{"postcaptcha":function (){return $("#postcaptcha").val()},"act":"verify","time":function (){return new Date().getTime()}}     
		}
	   },
	   <?php endif; ?>  
	   email: {
	   required:true,
	   email:true
	   }
	},
    messages: {
    jobs_name: {
    required: "请输入职位名称",
    minlength: jQuery.format("职位名称不能小于{0}个字符"),
	maxlength: jQuery.format("职位名称不能大于{0}个字符")
   },
   amount: {
   	required: "请输入招聘人数",
    range: jQuery.format("请输入一个介于 {0} 和 {1} 之间的值")
   },
    category: {
    required: jQuery.format("请选择所属职位，精确选择职位类别可以有效提高招聘效果")
   },
    subsite_id: {
    required: jQuery.format("请选择工作地区")
   },
   wage: {
    required: jQuery.format("请选择月薪范围")
   },
   education: {
    required: jQuery.format("请选择学历要求")
   },
    experience: {
    required: jQuery.format("请选择工作经验")
   },
   contents: {
	required: jQuery.format("请填写职位描述"),
	minlength: jQuery.format("职位描述内容不能小于{0}个字符")
   },
   contact: {
    required: jQuery.format("请填写联系人")
   },
    telephone: {
	isPhoneNumber: jQuery.format("请正确填写联系手机")
   },
   landline_tel_first: {
	isareacode: jQuery.format("请正确填写区号")
   },
    landline_tel_next: {
    	isHaveLandlin: jQuery.format("请填写手机或固话，二选一即可"),
	isphone: jQuery.format("请正确填写电话号码")
   },
    landline_tel_last: {
	isextensioncode: jQuery.format("请正确填写分机号")
   },
   address: {
    required: jQuery.format("请填写联系地址")
   },
   <?php if ($this->_vars['verify_addjob'] == "1"): ?>
    postcaptcha: {
    required: "请填写验证码",
	remote: jQuery.format("验证码错误")	
   },
    <?php endif; ?>
   email: {
    required: jQuery.format("请填写电子邮箱"),
	email: jQuery.format("请正确填写电子邮箱")
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
    jQuery.validator.addMethod("IScaptchastr", function(value, element) {
	var str="点击获取验证码";
	var flag=true;
	if (str==value)
	{
	flag=false;
	}
	return  flag || this.optional(element) ;
	}, "请填写验证码");
		/////验证码部分
<?php if ($this->_vars['verify_addjob'] == "1"): ?>
function imgcaptcha(inputID,imgdiv)
{
	$(inputID).focus(function(){
		if ($(inputID).val()=="点击获取验证码")
		{
		$(inputID).val("");
		$(inputID).css("color","#333333");
		}
		$(inputID).parent("div").css("position","relative");
		//设置验证码DIV
		$(imgdiv).css({position: "absolute", left:  $(inputID).width(), "bottom": "0px" , "z-index": "10", "background-color": "#FFFFFF", "border": "1px #A3C8DC solid","display": "none","margin-left": "25px"});
		$(imgdiv).show();
		if ($(imgdiv).html()=='')
		{
		$(imgdiv).append("<img src=\"<?php echo $this->_vars['QISHI']['site_dir']; ?>
include/imagecaptcha.php?t=<?php echo $this->_vars['random']; ?>
\" id=\"getcode\" align=\"absmiddle\"  style=\"cursor:pointer; margin:3px; height:28px\" title=\"看不请验证码？点击更换一张\"  border=\"0\"/>");
		}
		$(imgdiv+" img").click(function()
		{
			$(imgdiv+" img").attr("src",$(imgdiv+" img").attr("src")+"1");
			$(inputID).val("");
			$("#Form1").validate().element("#postcaptcha");	
		});
		$(document).click(function(event)
		{
			var clickid=$(event.target).attr("id");
			if (clickid!="getcode" &&  clickid!="postcaptcha")
			{
			$(imgdiv).hide();
			$(inputID).parent("div").css("position","");
			}			
		});
	});
}
imgcaptcha("#postcaptcha","#imgdiv");
//验证码结束
<?php endif; ?>
$("#save_templates").click(function(){
	  var jobs_name = $('input[name=jobs_name]').val();
	  var nature=$('input[name=nature]').val();
	  var nature_cn=$('input[name=nature_cn]').val();
	  var category=$('input[name=category]').val();
	  var subclass=$('input[name=subclass]').val();
	  var topclass=$('input[name=topclass]').val();
	  var category_cn=$('input[name=category_cn]').val();
	  var amount=$('input[name=amount]').val();
	  var subsite_id=$('input[name=subsite_id]').val();
	  var district=$('input[name=district]').val();
	  var sdistrict=$('input[name=sdistrict]').val();
	  var district_cn=$('input[name=district_cn]').val();
	  var wage=$('input[name=wage]').val();
	  var wage_cn=$('input[name=wage_cn]').val();
	  var negotiable=$('input:checkbox[name=negotiable][checked]').val();
	  var tag=$('input[name=tag]').val();
	  var sex=$('input[name=sex]').val();
	  var sex_cn=$('input[name=sex_cn]').val();
	  var education=$('input[name=education]').val();
	  var education_cn=$('input[name=education_cn]').val();
	  var experience=$('input[name=experience]').val();
	  var experience_cn=$('input[name=experience_cn]').val();
	  var graduate=$('input:checkbox[name=graduate][checked]').val();
	  var minage=$('input[name=minage]').val();
	  var maxage=$('input[name=maxage]').val();
	  var contents=$('#contents').val();
	  $.post("?act=ajax_save_jobs_templates",{subsite_id:subsite_id,jobs_name:jobs_name,category_cn:category_cn,category:category,subclass:subclass,topclass:topclass,amount:amount,nature:nature,nature_cn:nature_cn,district_cn:district_cn,district:district,sdistrict:sdistrict,graduate:graduate,education_cn:education_cn,education:education,experience_cn:experience_cn,experience:experience,sex_cn:sex_cn,sex:sex,contents:contents,wage:wage,wage_cn:wage_cn,tag:tag,negotiable:negotiable,minage:minage,maxage:maxage},function(result){
		    if(result=="1"){
		    	$("#save_templates").attr("disabled","disabled");
		    	$("#save_templates").val("已保存");
		    	dialog({
				  title: '系统提示',
				  content: "名称为"+jobs_name+"的模板已经保存到职位模板中",
				  width:'300px'
				}).showModal();
		    	return false;
		    }else if(result=="-1"){
		    	dialog({
				  title: '系统提示',
				  content: "您有未完善的内容，请检查！",
				  width:'300px'
				}).showModal();
		    	return false;
		    }else{
		    	dialog({
				  title: '系统提示',
				  content: "保存失败！",
				  width:'300px'
				}).showModal();
		    	return false;
		    }
	  });
});
$(".choose_templates").click(function(){
	$.get("?act=copy_templates&id="+$(this).attr("id"),function(result){
		    if(result=="-1"){
		    	return false;
		    }else{
				$("#nature_radio").children(".input_radio").removeClass("select");
				$("#nature_radio").children("#"+result.nature).addClass("select");
				$('input[name=nature]').val(result.nature);
				$('input[name=nature_cn]').val(result.nature_cn);
				$('input[name=topclass]').val(result.topclass);
				$('input[name=category]').val(result.category);
				$('input[name=subclass]').val(result.subclass);
				$('input[name=category_cn]').val(result.category_cn);
				$('.choose_cate').html(result.category_cn);
				$('input[name=amount]').val(result.amount);
				$('input[name=district]').val(result.district);
				$('input[name=sdistrict]').val(result.sdistrict);
				$('input[name=district_cn]').val(result.district_cn);
				$('.choose_district').html(result.district_cn);
				$('input[name=wage]').val(result.wage);
				$('input[name=wage_cn]').val(result.wage_cn);
				$('#wage_menu').html(result.wage_cn);
				$("input[type=checkbox][name=negotiable][value="+result.negotiable+"]").attr("checked",'checked');
				$("#sex_radio").children(".input_radio").removeClass("select");
				$("#sex_radio").children("#"+result.sex).addClass("select");
				$('input[name=sex]').val(result.sex);
				$('input[name=sex_cn]').val(result.sex_cn);
				$('input[name=education]').val(result.education);
				$('input[name=education_cn]').val(result.education_cn);
				$('#education_menu').html(result.education_cn);
				$('input[name=experience]').val(result.experience);
				$('input[name=experience_cn]').val(result.experience_cn);
				$('#experience_menu').html(result.experience_cn);
				$('input[name=minage]').val(result.minage);
				editor.html(result.contents);
				editor.sync();
				if(result.minage==""){
					$('#minage_div').html("请选择");
				}else{
					$('#minage_div').html(result.minage);
				}
				if(result.maxage==""){
					$('#maxage_div').html("请选择");
				}else{
					$('#maxage_div').html(result.maxage);
				}
				$('input[name=maxage]').val(result.maxage);
				$("input[type=checkbox][name=graduate][value="+result.graduate+"]").attr("checked",'checked');
				$('input[name=tag]').val(result.tag);
				$('input[name=tag_cn]').val(result.tag_cn);
				// 职位类别填充数据 
				job_filldata("#job_list", QS_jobs_parent, QS_jobs, "#result-list-job", "#aui_outer_job", "#job_result_show", "#topclass", "<?php echo $this->_vars['QISHI']['site_dir']; ?>
");
				// 职位亮点填充数据
				tag_filldata("#tag_list", QS_jobtag, "#aui_outer_tag", "#result-list-tag", "#trade_result_show", "#tag", "<?php echo $this->_vars['QISHI']['site_dir']; ?>
");
				// 地区填充数据
				var district_cn_ = result.district_cn.split("/");
				$("#subsite_menu").html(district_cn_[0]);
				$("#subsite_id").val(result.subsite_id);
				$("#subsite_name").val(district_cn_[0]);
				if(result.district && district_cn_[1])
				{
					if (QS_city[result.district]) {
						var district= new Array();
						district = QS_city[result.district].split("|");
						var ul='<ul>';
						for (i=0;i<district.length ;i++ )
						{
							var district_=district[i].split(",");

							ul+='<li id="'+district_[0]+'" sid="'+district_[0]+'" pid="'+result.district+'" title="'+district_[1]+'" style="float: left;padding: 0 5px;">'+district_[1]+'</li>';
						}
						ul+='<div class="clear:both"></div>';
						ul+='</ul>';
						var district_html='';
						district_html='<div style="position:relateve;float: left;margin-right: 5px;"><div id="subsite_district_menu" class="input_text_50_bg">'+result.district_cn.split("/")[1]+'</div><div class="menu" id="menu_sub_district" style="border:#ccc solid 1px;margin-top: -1px;padding: 10px;min-height:100px">'+ul+'</div></div><input type="hidden" name="district" id="district" value="'+result.district+'"/><input type="hidden" name="sdistrict" id="sdistrict" value="'+result.sdistrict+'"/><input type="hidden" name="district_cn" id="district_cn" value="'+result.district_cn+'"/>';
						$("#top_dis").html(district_html);
					} else {
						if ($("#subsite_district_menu").length > 0) {
							$("#subsite_district_menu").hide();
						};
					};
				}
		    }
	  },dataType="json");
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
<div class="page_location link_bk">当前位置：<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
">首页</a> > <a href="<?php echo $this->_vars['userindexurl']; ?>
">会员中心</a> > 添加职位</div>
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
	  <div class="addjob">
	    <div class="titleH1">
	      <div class="h1-title">发布职位</div>
        </div>
	    <div class="titleH2"><span>职位信息</span></div>
		<form id="Form1" name="Form1" method="post" action="?act=addjobs_save" >
		<input name="addrand" type="hidden"  id="addrand" value="<?php echo $this->_vars['addrand']; ?>
" />
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td width="125" align="right"><span class="nec">职位名称</span>：</td>
			<td width="230"><input name="jobs_name" type="text" class="input_text_200" id="jobs_name" maxlength="80" value=""/></td>
			<td></td>
		  </tr>
		  <tr>
			<td align="right">选择模板：</td>
			<td>
				<div style="position:relateve;">
             	 	<div id="tpl_menu" class="input_text_200_bg">请选择</div>	
             	 	<div class="menu" id="menu4">
	              		<ul>
	              			<?php if ($this->_vars['jobs_templates']): ?>
	              			<?php if (count((array)$this->_vars['jobs_templates'])): foreach ((array)$this->_vars['jobs_templates'] as $this->_vars['ex']): ?>
	              			<li id="<?php echo $this->_vars['ex']['id']; ?>
" title="<?php echo $this->_vars['ex']['title']; ?>
" class="choose_templates"><?php echo $this->_vars['ex']['title']; ?>
</li>
	              			<?php endforeach; endif; ?>
	              			<?php else: ?>
							<li>您还没有创建模板 <a href="company_jobs.php?act=add_templates" style="color:#0180cf">[新建模板]</a></li>
	              			<?php endif; ?>
	              		</ul>
	              	</div>
	            </div>				
	        </td>
	        <td><a style="text-decoration: none;color:#0180CF" href="?act=jobs_templates">[管理模板]</a></td>
		  </tr>
		  <tr>
			<td align="right"><span class="nec">职位性质</span>：</td>
			<td colspan="2">
			<div id="nature_radio">
			<input name="nature" id="nature" type="hidden" value="1" />
			<input name="nature_cn" id="nature_cn" type="hidden" value="全职" />
			 <?php echo tpl_function_qishi_get_classify(array('set' => "类型:QS_jobs_nature,列表名:c_nature"), $this); if (count((array)$this->_vars['c_nature'])): foreach ((array)$this->_vars['c_nature'] as $this->_vars['list']): ?>
			  <div class="input_radio <?php if ($this->_vars['list']['id'] == $this->_vars['c_nature']['0']['id']): ?>select<?php endif; ?>" id="<?php echo $this->_vars['list']['id']; ?>
"><?php echo $this->_vars['list']['categoryname']; ?>
</div>
			   <?php endforeach; endif; ?>			  
			  <div class="clear"></div>
			  </div>		    </td>
		  </tr>
		  <tr class="jobmain">
			<td align="right"><span class="nec">职位类别</span>：</td>
			<td id="jobsSort" style="position:relative;">
            	<div id="jobText" class="input_text_200_bg choose_cate ucc-default">请选择</div>	
				<!-- 职位类别弹出框 -->
				<div class="aui_outer" id="aui_outer_job">
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
															<div class="LocalDataMultiC">
																<div class="selector-header"><span class="selector-title">职位选择</span><div></div><span class="selector-close">X</span><div class="clear"></div></div>

																<div class="data-row-list data-row-main" id="job_list">
																	<!-- 列表内容 -->
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
				<!-- 职位类别弹出框 End-->
              <input name="topclass" id="topclass" type="hidden" value="" />			
              <input name="category" id="category" type="hidden" value="" />		
              <input name="subclass" id="subclass" type="hidden" value="" />		
              <input name="category_cn" id="category_cn" type="hidden" value="" />
            </td>
			<td>&nbsp;</td>
		  </tr>
		   <tr>
			<td align="right"><span class="nec">招聘人数</span>：</td>
			<td><input name="amount" type="text" class="input_text_200" id="amount" maxlength="4" value="5"/></td>
			<td><div class="righttip" style="float:left;">0表示若干!</div></td>
		  </tr>
		  <tr class="jobmain">
			<td align="right"><span class="nec">工作地区</span>：</td>
			<td>
				<div style="position:relateve;float: left;margin-right: 5px;" >
					<div id="subsite_menu" class="input_text_50_bg">请选择</div>
					<input type="hidden" name="subsite_id" id="subsite_id"/>
					<input type="hidden" name="subsite_name" id="subsite_name"/>
             	 	<div class="menu" id="menu_sub" style="border:#ccc solid 1px;margin-top: -1px;padding:10px;min-height:100px">
	              		<ul>
	              			<?php if (count((array)$this->_vars['subsite'])): foreach ((array)$this->_vars['subsite'] as $this->_vars['list']): ?>
	              			<li id="<?php echo $this->_vars['list']['s_id']; ?>
" sid="<?php echo $this->_vars['list']['s_district']; ?>
" title="<?php echo $this->_vars['list']['s_districtname']; ?>
" pid="0" style="float: left;padding: 0 5px;"><?php echo $this->_vars['list']['s_districtname']; ?>
</li>
	              			<?php endforeach; endif; ?>
	              			<div style="clear: both;"></div>
	              		</ul>
	              	</div>
	            </div>
				<div id="top_dis">
					<input type="hidden" id="district" name="district" value="">
					<input type="hidden" id="sdistrict" name="sdistrict" value="">
					<input type="hidden" id="district_cn" name="district_cn" value="">
				</div>
			</td>
			<td></td>
			<script>
			$(document).ready(function()
			{
				subMenuDown("#subsite_menu","#subsite_id","#district","#subsite_name","#menu_sub","218px","1");
				subMenuDown("#subsite_district_menu","#sdistrict","#district","#district_cn","#menu_sub_district","218px","2");
				function subMenuDown(menuId,input,input2,input_cn,menuList,width,next_index){
					$(menuId).live('click',function(){
						$(menuList).css("width",width);
						$(menuList).slideDown('fast');
						//生成背景
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

					$(menuList+" li").live('click',function(){
						var id = $(this).attr("id");
						var title = $(this).attr("title");
						var sid=$(this).attr("sid");
						var pid =$(this).attr('pid');
						if (QS_city[sid]) {
							var district= new Array();
							district = QS_city[sid].split("|");
							var ul='<ul>';
							for (i=0;i<district.length ;i++ )
							{
								var district_=district[i].split(",");

								ul+='<li id="'+district_[0]+'" sid="'+district_[0]+'" pid="'+sid+'" title="'+district_[1]+'" style="float: left;padding: 0 5px;">'+district_[1]+'</li>';
							}
							ul+='<div class="clear:both"></div>';
							ul+='</ul>';
							var district_html='';
							district_html='<div style="position:relateve;float: left;margin-right: 5px;"><div id="subsite_district_menu" class="input_text_50_bg">请选择</div><div class="menu" id="menu_sub_district" style="border:#ccc solid 1px;margin-top: -1px;padding: 10px;min-height:100px">'+ul+'</div></div><input type="hidden" name="district" id="district"/><input type="hidden" name="sdistrict" id="sdistrict"/><input type="hidden" name="district_cn" id="district_cn"/>';
							$("#top_dis").html(district_html);
							$("#menu_sub_district").show();

							$(input_cn).val(title);
							$(input).val(id);
							$(input2).val(sid);
							$(menuId).html(title);
							$("#district_cn").val(title);
							$("#sdistrict").val('0');
						} else {
							if(next_index==1) {
								$(input).val(id);
								$(input_cn).val(title);
								$("#district_cn").val(title);
								$(input2).val(sid);
								$("#sdistrict").val('0');
								$("#subsite_district_menu").hide();
							} else {
								$("#sdistrict").val(sid);
								$("#district_cn").val(title);
							}
							$(menuId).html(title);
						};
						$(menuList).slideUp('fast');
						$(".menu_bg_layer").remove();
					});
				}
			})
			</script>
		  </tr>
		  <tr>
			<td align="right"><span class="nec">薪资待遇</span>：</td>
			<td>
				<div style="position:relateve;">
             	 	<div id="wage_menu" class="input_text_200_bg">请选择</div>	
             	 	<div class="menu" id="menu3">
	              		<ul>
	              			<?php echo tpl_function_qishi_get_classify(array('set' => "类型:QS_wage,列表名:c_wage"), $this);?>
	              			<?php if (count((array)$this->_vars['c_wage'])): foreach ((array)$this->_vars['c_wage'] as $this->_vars['list']): ?>
	              			<li id="<?php echo $this->_vars['list']['id']; ?>
" title="<?php echo $this->_vars['list']['categoryname']; ?>
"><?php echo $this->_vars['list']['categoryname']; ?>
</li>
	              			<?php endforeach; endif; ?>
	              		</ul>
	              	</div>
	            </div>				
             	 <input name="wage" type="hidden" id="wage" value="" />
             	 <input name="wage_cn" type="hidden" id="wage_cn" value="" /></td>
			<td><label><input name="negotiable" type="checkbox" value="1" />可面议</label></td>
		  </tr>
	    </table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td width="125" align="right">职位亮点：</td>
			<td style="position:relative;">
			  <div class="showchecktag"></div>
              <div class="input_checkbox_add ucc-default">添加</div>	
              <!-- 职位亮点弹出框 -->
				<div class="aui_outer" id="aui_outer_tag">
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
																<div class="selector-header"><span class="selector-title">职位亮点选择</span><div></div><span id="tag-selector-save" class="selector-save">确定</span><span class="selector-close">X</span><div class="clear"></div></div>

																<div class="data-row-head"><div class="data-row"><div class="data-row-side">最多选 <strong class="text-warning">5</strong> 项&nbsp;&nbsp;已选 <strong id="arstrade" class="text-warning">0</strong> 项</div><div id="result-list-tag" class="result-list data-row-side-ra"></div></div><div class="cla"></div></div>
																<div class="item-table">
																	<table class="options-table options-table-7">
																		<tbody class="item-list"><tr><td class="bno"><table><tbody id="tag_list">
																			<!-- 列表内容 -->
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
				<!-- 职位亮点弹出框 End-->
              <input name="tag" type="hidden" id="tag" value="" />
			  <input name="tag_cn" type="hidden" id="tag_cn" value="" />		
          </td>
		  </tr>
	    </table>
		<div class="titleH2"><span>职位要求</span></div>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td width="125" align="right"><span class="nec">性别要求</span>：</td>
			<td colspan="2">
			<div id="sex_radio">
			<input name="sex" id="sex" type="hidden" value="3" />
			<input name="sex_cn" id="sex_cn" type="hidden" value="不限" />
			  <div class="input_radio select" id="3">不限</div>			
			  <div class="input_radio" id="1">男</div>
			  <div class="input_radio" id="2">女</div>			  
			  <div class="clear"></div>
			  </div>		    </td>
		  </tr>
		  <tr>
			<td align="right"><span class="nec">学历要求</span>：</td>
			<td width="230">
			<div style="position:relateve;">
             	 	<div id="education_menu" class="input_text_200_bg">请选择</div>	
             	 	<div class="menu" id="menu1">
	              		<ul>
	              			<?php echo tpl_function_qishi_get_classify(array('set' => "类型:QS_education,列表名:c_education"), $this);?>
	              			<?php if (count((array)$this->_vars['c_education'])): foreach ((array)$this->_vars['c_education'] as $this->_vars['list']): ?>
	              			<li id="<?php echo $this->_vars['list']['id']; ?>
" title="<?php echo $this->_vars['list']['categoryname']; ?>
"><?php echo $this->_vars['list']['categoryname']; ?>
</li>
	              			<?php endforeach; endif; ?>
	              		</ul>
	              	</div>
	            </div>				
             	 <input name="education" type="hidden" id="education" value="" />
             	 <input name="education_cn" type="hidden" id="education_cn" value="" /></td>
		<td><label> 及以上</label></td>
		  </tr>
		  <tr>
			<td align="right"><span class="nec">工作经验</span>：</td>
			<td>
				<div style="position:relateve;">
             	 	<div id="experience_menu" class="input_text_200_bg">请选择</div>	
             	 	<div class="menu" id="menu2">
	              		<ul>
	              			<?php echo tpl_function_qishi_get_classify(array('set' => "类型:QS_experience,列表名:c_experience"), $this);?>
	              			<?php if (count((array)$this->_vars['c_experience'])): foreach ((array)$this->_vars['c_experience'] as $this->_vars['list']): ?>
	              			<li id="<?php echo $this->_vars['list']['id']; ?>
" title="<?php echo $this->_vars['list']['categoryname']; ?>
"><?php echo $this->_vars['list']['categoryname']; ?>
</li>
	              			<?php endforeach; endif; ?>
	              		</ul>
	              	</div>
	            </div>				
             	 <input name="experience" type="hidden" id="experience" value="" />
             	 <input name="experience_cn" type="hidden" id="experience_cn" value="" /></td>
			<td><label><input name="graduate" type="checkbox" value="1" />应届生亦可</label></td>
		  </tr>
		    <tr>
			<td align="right">年龄要求：</td>
			<td colspan="2">
			  <table border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td width="80" style="padding:0px">
                    	<div>
                    	<div class="input_text_50_bg date_input wid54" id="minage_div">请选择</div>
		             	 <input name="minage" id="minage" type="hidden" value="" />
		            	</div>
		        	</td>
                    <td width="20" style="padding:0px">至</td>
                    <td width="80"  style="padding:0px">
                    	<div>
                    	<div class="input_text_50_bg date_input wid54" id="maxage_div">请选择</div>
		             	 <input name="maxage" id="maxage" type="hidden" value="" />
		            	</div>
                    </td>
                  </tr>
                </table>
				<label id="ageerr" class="error" style="display:none">请选择正确的年龄范围</label>
				</td>
		  </tr>
	    </table>
		<div class="titleH2"><span>职位描述</span></div>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td width="125" align="right" valign="top"><span class="nec">职位描述</span>：</td>
			<td width="500">
			<div style="display:none;" id="template">
				<span class="tdTemplateTitle">&nbsp;</span><font style="color:#FF6600">（点击职位模板后会自动填充职位描述）</font>
				<span id="JobRequInfoTemplate"><a href="javascript:void(0);"></a></span>
			</div>
			<textarea id="contents" name="contents" style="width:700px;height:300px;visibility:hidden;"></textarea>
				<script type="text/javascript">
					var editor;
					KindEditor.ready(function(K) {
						editor = K.create('textarea[name="contents"]', {
							allowFileManager : false,
							width:600,
							height:250,
							afterBlur: function(){this.sync();}
						});
					});
				</script>
			</td>
			<td></td>
		  </tr>
	    </table>
	<div class="titleH2"><span>联系方式</span></div>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td width="125" align="right"><span class="nec">联系人</span>：</td>
			<td width="230"><input name="contact" type="text" class="input_text_200" id="contact" maxlength="20"   value="<?php echo $this->_vars['company_profile']['contact']; ?>
"/></td>
			<td><label><input name="contact_show" type="checkbox" value="1" checked="checked" />对外公开联系人</label></td>
		  </tr>
		   </table>
		  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			  <tr>
				<td width="125" align="right"><span class="nec">固定电话</span>：</td>
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
			<td width="125" align="right"><span>联系手机</span>：</td>
			<td width="230">
			
            <input name="telephone" oninput="OnInput (event,'#telephone_two')" onpropertychange="OnPropChanged (event,'#telephone_two')" type="text" class="input_text_200" id="telephone" maxlength="35"   value="<?php echo $this->_vars['company_profile']['telephone']; ?>
"/></td>
			<td><label><input name="telephone_show" type="checkbox" value="1" checked="checked" />对外公开联系手机</label></td>
		  </tr>
		  </table>
		  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td width="125" align="right"><span class="nec">联系邮箱</span>：</td>
			<td width="230">
			
             <input name="email" oninput="OnInput (event,'#email_two')" onpropertychange="OnPropChanged (event,'#email_two')" type="text" class="input_text_200" id="email" maxlength="80" value="<?php echo $this->_vars['company_profile']['email']; ?>
"/>            	</td>
			<td><label><input name="email_show" type="checkbox" value="1" checked="checked" />对外公开联系邮箱</label></td>
		  </tr>
	    </table>
	    <script type="text/javascript">
	    	// Firefox, Google Chrome, Opera, Safari, Internet Explorer from version 9
			function OnInput (event,id) {
				$(id).val(event.target.value);
			    // alert ("The new content: " + event.target.value);
			}
			// Internet Explorer
			function OnPropChanged (event,id) {
			    if (event.propertyName.toLowerCase () == "value") {
			    	$(id).val(event.srcElement.value);
			    }
			}
	    </script>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			<tbody>
				<tr>
					<td width="125" align="right"><span class="nec">联系地址</span>：</td>
					<td ><input name="address" id="address" type="text" class="input_text_500" maxlength="100" value="<?php echo $this->_vars['company_profile']['address']; ?>
"/></td>
				 </tr>
			</tbody>
		</table>
		<div class="titleH2"><span style="background:none;">高级设置</span></div>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			<tr>
			<td width="125" align="right">接收委托简历：</td>
			<td ><label><input name="is_entrust" type="checkbox" value="0" checked="checked"/>接收</label>
		     </td>
		  </tr>
			<tr>
				<td width="125" align="right"><span class="">简历接收邮箱</span>：</td>
				<td width="230"><input name="email_two" id="email_two" type="text" class="input_text_200" maxlength="80" value="<?php echo $this->_vars['company_profile']['email']; ?>
" disabled="disabled"/></td>
				<td><label><input name="notify" type="checkbox" value="1" checked="checked"/>接收</label></td>
		  	</tr>
			<tr>
				<td width="125" align="right"><span class="">简历短信通知</span>：</td>
				<td width="230"><input name="telephone_two" id="telephone_two" type="text" class="input_text_200" maxlength="80" value="<?php echo $this->_vars['company_profile']['telephone']; ?>
" disabled="disabled"/></td>
				<td><label><input name="notify_mobile" type="checkbox" value="1" id="receiveit" <?php if ($this->_vars['QISHI']['company_sms'] == 1 && $this->_vars['user']['sms_num'] <= 0): ?>disabled="disabled"<?php endif; ?>/>接收</label></td>
		  	</tr>
			<?php if ($this->_vars['QISHI']['company_sms'] == 1): ?>
			<tr height="10" style="display:;">
				<td width="125" align="right" style="padding:0">&nbsp;</td>
				<td width="230" colspan="2" style="padding:0">
					<div class="short-text-tip f-left" style="margin-left:0">您的可用短信条数为 <span style="color:#ff9900;"><?php echo $this->_vars['user']['sms_num']; ?>
</span> 条<?php if ($this->_vars['user']['sms_num'] <= 0): ?>，<a target="_blank" href="company_sms.php?act=sms_order" class="underline">立即购买</a><?php endif; ?></div>
				</td>
		  	</tr>
			<?php endif; ?>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <?php if ($this->_vars['verify_addjob'] == "1"): ?>
		  <tr>
			<td align="right"><span class="nec">验证码</span>：</td>
			<td>
				<div>
				<div id="imgdiv"></div>
				<input  class="input_text_200" name="postcaptcha" id="postcaptcha" type="text" value="点击获取验证码" style="color:#999999"/>
			</div>
			</td>
		  </tr>
		  <?php endif; ?>
		   <tr>
			<td width="125" align="right"> </td>
			<td ><input type="submit" name="submitsave" id="submitsave" value="发布"  class="but180lan" onclick="$(window).unbind('beforeunload');"/>
				<input type="button" name="save_templates" id="save_templates" value="存为模板"  class="but180hui"/>
		     </td>
		  </tr>
	    </table>
	    <input name="add_mode" type="hidden" id="add_mode" value="<?php echo $this->_vars['add_mode']; ?>
" />

<?php if ($this->_vars['add_mode'] == "1"): ?>
<script type="text/javascript">
$(document).ready(function()
{
	//积分默认
	total();
	//计算总计积分
	function total()
	{
			var points = <?php echo $this->_vars['points_total']; ?>
;
			var jobs_add = <?php echo $this->_vars['points']['jobs_add']['value']; ?>
;
			var totals=jobs_add;
			if (jobs_add>0)
			{			
			$("#total").html("发布本条信息总计扣除&nbsp;<span style=\"font-size:20px; color: #FF0000\" >"+totals+"</span>&nbsp;<?php echo $this->_vars['QISHI']['points_quantifier'];  echo $this->_vars['QISHI']['points_byname']; ?>
，你目前共有<strong style=\"color:#0033FF\"> <?php echo $this->_vars['points_total']; ?>
  </strong><?php echo $this->_vars['QISHI']['points_quantifier'];  echo $this->_vars['QISHI']['points_byname']; ?>
；<br />");
			$("#totals").val(<?php echo $this->_vars['points_total']; ?>
-totals);	
			}
	}				
	$("#addsubmit").click(function()
	{
		if ($("#totals").val()<0)
		{
			<?php if ($this->_vars['QISHI']['operation_mode'] == "2"): ?>
			dialog({
			  title: '系统提示',
			  content: "您的积分不足，请申请新的服务或者充值积分！",
			  width:'300px'
			}).showModal();
			<?php else: ?>
			dialog({
			  title: '系统提示',
			  content: "您的积分不足，请充值后再发布！",
			  width:'300px'
			}).showModal();
			<?php endif; ?>
		}
		else
		{
		$("form[name=Form1]").submit();
		}
	});	
});
</script>
<div style="position:relative;padding-left:125px;">
	  <table width="100%" border="0" cellpadding="20" cellspacing="0" class="link_lan" bgcolor="F9F9F9" >
            <tr>               
              <td align="center" >
              	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall"  bgcolor="F9F9F9">
		   <tr>
			<td width="360" align="left">
				<div id="total"></div>
			</td>
		  </tr>
	    </table>
              	</td>
            </tr>
      </table>
</div>
<?php endif; ?>
</form> 
	  </div>
	</div>
  </div>
  <div class="clear"></div>
</div>
<!-- 职位亮点选择框 -->
<div id="tagBox" class="alert-window" style="display:none;width: 810px; height: 300px; left: 50%; top: 600px; margin-left: -405px; position: absolute; z-index: 100001;">
	<div style="width: 810px;" class="resume-tc">
        <div class="resume-tc-head">
            <h3><i class="icon-loc"></i> 请选职位两点（最多选择5个）</h3>
            <a class="close wd_close cm_closeMsg" href="javascript:;"></a> 
        </div>
        <div class="sx-yx">
        	<span class="clr-grn fnt-b">已选：</span>
        	<span id="box_checkedTag"></span>
        </div>
        <div id="showTag" style="width:810px; height: 200px; overflow: auto; overflow-x: hidden; overflow-y: auto; position: relative;">
	        
		</div>
        <div class="sx-action">
        	<button id="btn_tagsave" class="grn" type="button">确定</button> 
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
