<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_get_classify.php'); $this->register_function("qishi_get_classify", "tpl_function_qishi_get_classify",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:41 CST */ ?>
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
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.validate.min.js" type='text/javascript' language="javascript"></script>
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
allaround('<?php echo $this->_vars['QISHI']['site_dir']; ?>
');
// ְλ���������� 
job_filldata("#job_list", QS_jobs_parent, QS_jobs, "#result-list-job", "#aui_outer_job", "#job_result_show", "#topclass", "<?php echo $this->_vars['QISHI']['site_dir']; ?>
");
// ְλ�����������
tag_filldata("#tag_list", QS_jobtag, "#aui_outer_tag", "#result-list-tag", "#trade_result_show", "#tag", "<?php echo $this->_vars['QISHI']['site_dir']; ?>
");
//�������ʵ�ѡ
var nature_obj = $("#nature_radio .input_radio").first();
$("#nature").val(nature_obj.attr("id"));
$("#nature_cn").val(nature_obj.text());
$("#nature_radio .input_radio").click(function(){
		$("#nature").val($(this).attr('id'));
		$("#nature_cn").val($(this).text());
		$("#nature_radio .input_radio").removeClass("select");
		$(this).addClass("select");
});
//�Ա�ѡ
$("#sex_radio .input_radio").click(function(){
		$("#sex").val($(this).attr('id'));
		$("#sex_cn").val($(this).text());
		$("#sex_radio .input_radio").removeClass("select");
		$(this).addClass("select");
});
menuDown("#education_menu","#education","#education_cn","#menu1","218px");
menuDown("#experience_menu","#experience","#experience_cn","#menu2","218px");
menuDown("#wage_menu","#wage","#wage_cn","#menu3","218px");
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
		
		var y=18;
		var ymax=65;
		htm="<div class=\"showyearbox yearlist\">";		
		htm+="<ul>";
		for (i=y;i<=ymax;i++)
		{
		htm+="<li title=\""+i+"\">"+i+"��</li>";
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
			$(inputname).val($(this).attr("title"));
			$(inputdiv).html($(this).attr("title"));
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
$("#Form1").validate({
 //debug: true,
// onsubmit:false,
//onfocusout :true,
   rules:{
   title:{
    required: true,
	minlength:2,
    maxlength:30
   },
   amount: {
	range:[0,9999]
   },
   category: "required",
   district: "required",
   wage: "required",
   education: "required",
   experience: "required",
   contents:{
	required: true,
	minlength:30,
    maxlength:1000
   }
	},
    messages: {
    title: {
    required: "������ְλģ������",
    minlength: jQuery.format("ְλģ�����Ʋ���С��{0}���ַ�"),
	maxlength: jQuery.format("ְλģ�����Ʋ��ܴ���{0}���ַ�")
   },
   amount: {
    range: jQuery.format("������һ������ {0} �� {1} ֮���ֵ")
   },
    category: {
    required: jQuery.format("��ѡ������ְλ����ȷѡ��ְλ��������Ч�����ƸЧ��")
   },
    district: {
    required: jQuery.format("��ѡ��������")
   },
   wage: {
    required: jQuery.format("��ѡ����н��Χ")
   },
   education: {
    required: jQuery.format("��ѡ��ѧ��Ҫ��")
   },
    experience: {
    required: jQuery.format("��ѡ��������")
   },
   contents: {
	required: jQuery.format("����дְλ����"),
	minlength: jQuery.format("ְλ�������ݲ���С��{0}���ַ�"),
	maxlength: jQuery.format("ְλ�������ݲ��ܴ���{0}���ַ�")
   } 
  },
  errorPlacement: function(error, element) {
    if ( element.is(":radio") )
        error.appendTo( element.parent().next().next() );
    else if ( element.is(":checkbox") )
        error.appendTo ( element.next());
    else
        error.appendTo(element.parent().next());
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
">��Ա����</a> > ���ְλģ��</div>

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
	      <div class="h1-title">���ְλģ��</div>
        </div>
	    <div class="titleH2"><span>ְλģ����Ϣ</span></div>
		<form id="Form1" name="Form1" method="post" action="?act=add_templates_save" >
		<input name="addrand" type="hidden"  id="addrand" value="<?php echo $this->_vars['addrand']; ?>
" />
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td width="125" align="right"><span class="nec">ģ������</span>��</td>
			<td width="230"><input name="title" type="text" class="input_text_200" id="title" maxlength="80" value=""/></td>
			<td>&nbsp;</td>
		  </tr>
		  <tr>
			<td align="right"><span class="nec">ְλ����</span>��</td>
			<td colspan="2">
			<div id="nature_radio">
			<input name="nature" id="nature" type="hidden" value="1" />
			<input name="nature_cn" id="nature_cn" type="hidden" value="ȫְ" />
			 <?php echo tpl_function_qishi_get_classify(array('set' => "����:QS_jobs_nature,�б���:c_nature"), $this); if (count((array)$this->_vars['c_nature'])): foreach ((array)$this->_vars['c_nature'] as $this->_vars['list']): ?>
			  <div class="input_radio <?php if ($this->_vars['list']['id'] == $this->_vars['c_nature']['0']['id']): ?>select<?php endif; ?>" id="<?php echo $this->_vars['list']['id']; ?>
"><?php echo $this->_vars['list']['categoryname']; ?>
</div>
			   <?php endforeach; endif; ?>			  
			  <div class="clear"></div>
			  </div>		    </td>
		  </tr>
		  <tr class="jobmain">
			<td align="right"><span class="nec">ְλ���</span>��</td>
			<td id="jobsSort" style="position:relative;">
              <div id="jobText" class="input_text_200_bg choose_cate ucc-default">��ѡ��</div>	
              <!-- ְλ��𵯳��� -->
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
																<div class="selector-header"><span class="selector-title">ְλѡ��</span><div></div><span class="selector-close">X</span><div class="clear"></div></div>

																<div class="data-row-list data-row-main" id="job_list">
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
				<!-- ְλ��𵯳��� End-->
              <input name="topclass" id="topclass" type="hidden" value="" />			
              <input name="category" id="category" type="hidden" value="" />		
              <input name="subclass" id="subclass" type="hidden" value="" />		
              <input name="category_cn" id="category_cn" type="hidden" value="" />
            </td>
			<td>&nbsp;</td>
		  </tr>
		   <tr>
			<td align="right"><span class="nec">��Ƹ����</span>��</td>
			<td><input name="amount" type="text" class="input_text_200" id="amount" maxlength="4" value="5"/></td>
			<td><div class="righttip">0��ʾ����!</div></td>
		  </tr>
		   <tr class="jobmain">
			<td align="right"><span class="nec">��������</span>��</td>
			<td>
				<div style="position:relateve;float: left;margin-right: 5px;" >
					<div id="subsite_menu" class="input_text_50_bg">��ѡ��</div>
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
				<input type="hidden" name="district" id="district"/><input type="hidden" name="sdistrict" id="sdistrict"/><input type="hidden" name="district_cn" id="district_cn"/></div>
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
							district_html='<div style="position:relateve;float: left;margin-right: 5px;"><div id="subsite_district_menu" class="input_text_50_bg">��ѡ��</div><div class="menu" id="menu_sub_district" style="border:#ccc solid 1px;margin-top: -1px;padding: 10px;min-height:100px">'+ul+'</div></div><input type="hidden" name="district" id="district"/><input type="hidden" name="sdistrict" id="sdistrict"/><input type="hidden" name="district_cn" id="district_cn"/>';
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
			<td align="right"><span class="nec">н�ʴ���</span>��</td>
			<td>
				<div style="position:relateve;">
             	 	<div id="wage_menu" class="input_text_200_bg">��ѡ��</div>	
             	 	<div class="menu" id="menu3">
	              		<ul>
	              			<?php echo tpl_function_qishi_get_classify(array('set' => "����:QS_wage,�б���:c_wage"), $this);?>
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
			<td><label><input name="negotiable" type="checkbox" value="1" />������</label></td>
		  </tr>
	    </table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td width="125" align="right">ְλ���㣺</td>
			<td style="position:relative;">
			  <div class="showchecktag"></div>
              <div class="input_checkbox_add ucc-default">���</div>	
              <!-- ְλ���㵯���� -->
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
																<div class="selector-header"><span class="selector-title">ְλ����ѡ��</span><div></div><span id="tag-selector-save" class="selector-save">ȷ��</span><span class="selector-close">X</span><div class="clear"></div></div>

																<div class="data-row-head"><div class="data-row"><div class="data-row-side">���ѡ <strong class="text-warning">5</strong> ��&nbsp;&nbsp;��ѡ <strong id="arstrade" class="text-warning">0</strong> ��</div><div id="result-list-tag" class="result-list data-row-side-ra"></div></div><div class="cla"></div></div>
																<div class="item-table">
																	<table class="options-table options-table-7">
																		<tbody class="item-list"><tr><td class="bno"><table><tbody id="tag_list">
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
				<!-- ְλ���㵯���� End-->
              <input name="tag" type="hidden" id="tag" value="" />
			  <input name="tag_cn" type="hidden" id="tag_cn" value="" />		
          </td>
		  </tr>
	    </table>

		
		
		<div class="titleH2"><span>ְλҪ��</span></div>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td width="125" align="right"><span class="nec">�Ա�Ҫ��</span>��</td>
			<td colspan="2">
			<div id="sex_radio">
			<input name="sex" id="sex" type="hidden" value="1" />
			<input name="sex_cn" id="sex_cn" type="hidden" value="��" />
			  <div class="input_radio select" id="1">��</div>
			  <div class="input_radio" id="2">Ů</div>			  
			  <div class="clear"></div>
			  </div>		    </td>
		  </tr>
		  <tr>
			<td align="right"><span class="nec">ѧ��Ҫ��</span>��</td>
			<td width="230">
			<div style="position:relateve;">
             	 	<div id="education_menu" class="input_text_200_bg">��ѡ��</div>	
             	 	<div class="menu" id="menu1">
	              		<ul>
	              			<?php echo tpl_function_qishi_get_classify(array('set' => "����:QS_education,�б���:c_education"), $this);?>
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
			<td>&nbsp;</td>
		  </tr>
		  <tr>
			<td align="right"><span class="nec">��������</span>��</td>
			<td>
				<div style="position:relateve;">
             	 	<div id="experience_menu" class="input_text_200_bg">��ѡ��</div>	
             	 	<div class="menu" id="menu2">
	              		<ul>
	              			<?php echo tpl_function_qishi_get_classify(array('set' => "����:QS_experience,�б���:c_experience"), $this);?>
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
			<td><label><input name="graduate" type="checkbox" value="1" />Ӧ�������</label></td>
		  </tr>
		    <tr>
			<td align="right">����Ҫ��</td>
			<td colspan="2">
			  <table border="0" cellpadding="0" cellspacing="0" >
                          <tr>
                            <td width="80" style="padding:0px">
                            	<div>
                            	<div class="input_text_50_bg date_input" id="minage_div">��ѡ��</div>
				             	 <input name="minage" id="minage" type="hidden" value="" />
				            	</div>
				        	</td>
                            <td width="20" style="padding:0px">��</td>
                            <td width="80"  style="padding:0px">
                            	<div>
                            	<div class="input_text_50_bg date_input" id="maxage_div">��ѡ��</div>
				             	 <input name="maxage" id="maxage" type="hidden" value="" />
				            	</div>
                            </td>
                          </tr>
                        </table>
				</td>
		  </tr>
	    </table>
		
		<div class="titleH2"><span>ְλ����</span></div>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		  <tr>
			<td width="125" align="right" valign="top"><span class="nec">ְλ����</span>��</td>
			<td width="500">
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
			<td>&nbsp;</td>
		  </tr>
	    </table>
		
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		   <tr>
			<td width="125" align="right"> </td>
			<td ><input type="submit" name="submitsave" id="submitsave" value="����"  class="but180lan"/>
			</td>
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
