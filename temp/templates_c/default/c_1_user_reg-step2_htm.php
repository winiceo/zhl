<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_get_classify.php'); $this->register_function("qishi_get_classify", "tpl_function_qishi_get_classify",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:33 CST */ ?>
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
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/reg.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/jobs.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.validate.min.js" type='text/javascript' language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
data/cache_classify.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.comreg.selectlayer.js" type='text/javascript' language="javascript"></script>
<script>
$(document).ready(function($) {
	// 手机号码验证   
	jQuery.validator.addMethod("isPhoneNumber", function(value, element) {   
	    var tel = /^((0\d{2,3}-[2-9][0-9]{6,7})|(0\d{2,3}[2-9][0-9]{6,7})|([2-9][0-9]{6,7})|(1[35847]\d{9}))$/;
	    var mobile = /^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|18[0-9]{9}|17[0-9]{9}$/;
	    return this.optional(element) || (tel.test(value));
	}, "请正确填写联系电话");
	jQuery.validator.addMethod("is_Email", function(value, element) {   
	    var email = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]w+)*$/;
	    return this.optional(element) || (email.test(value));
	}, "请正确填写邮箱");
	
	$("#Form1").validate({
		success: function(lable) {
				lable.text(" ").addClass("ver-success");
		},
		rules:{
			password:
			{
				required: true,
				minlength:6,
				maxlength:18
			},
			rpassword:
			{
				required: true,
				equalTo:"#password"
			}
			<?php if (in_array ( "companyname" , $this->_vars['config'] )): ?>
			,
			companyname:
			{
				required: true
			}
			<?php endif; ?>
			<?php if (in_array ( "nature" , $this->_vars['config'] )): ?>
			,
			nature:
			{
				required: true
			}
			<?php endif; ?>
			<?php if (in_array ( "trade" , $this->_vars['config'] )): ?>
			,
			trade:
			{
				required: true
			}
			<?php endif; ?>
			<?php if (in_array ( "scale" , $this->_vars['config'] )): ?>
			,
			scale:
			{
				required: true
			}
			<?php endif; ?>
			<?php if (in_array ( "district" , $this->_vars['config'] )): ?>
			,
			district:
			{
				required: true
			}
			<?php endif; ?>
			<?php if (in_array ( "contact" , $this->_vars['config'] )): ?>
			,
			contact:
			{
				required: true
			}
			<?php endif; ?>
			<?php if (in_array ( "telephone" , $this->_vars['config'] ) && $this->_vars['sqlarr']['reg_type'] == 2): ?>
			,
			telephone:
			{
				required: true,
				isPhoneNumber:true
			}
			<?php endif; ?>
			<?php if (in_array ( "email" , $this->_vars['config'] ) && $this->_vars['sqlarr']['reg_type'] == 1): ?>
			,
			reg_email:
			{
				required: true,
				is_Email:true
			}
			<?php endif; ?>
			<?php if (in_array ( "address" , $this->_vars['config'] )): ?>
			,
			address:
			{
				required: true
			}
			<?php endif; ?>
			<?php if (in_array ( "contents" , $this->_vars['config'] )): ?>
			,
			contents:
			{
				required: true
			}
			<?php endif; ?>
		},
		messages: {
			password: 
			{
				required: "请填写密码",
				minlength: jQuery.format("填写不能小于{0}个字符"),
				maxlength: jQuery.format("填写不能大于{0}个字符")
			},
			rpassword: {
				required: "请填写密码",
				equalTo: "两次输入的密码不同"
			}
			<?php if (in_array ( "companyname" , $this->_vars['config'] )): ?>
			,
			companyname:
			{
				required: "请输入企业名称"
			}
			<?php endif; ?>
						<?php if (in_array ( "nature" , $this->_vars['config'] )): ?>
			,
			nature:
			{
				required: "请选择企业性质"
			}
			<?php endif; ?>
			<?php if (in_array ( "trade" , $this->_vars['config'] )): ?>
			,
			trade:
			{
				required: "请选择企业所在行业"
			}
			<?php endif; ?>
			<?php if (in_array ( "scale" , $this->_vars['config'] )): ?>
			,
			scale:
			{
				required: "请选择企业规模"
			}
			<?php endif; ?>
			<?php if (in_array ( "district" , $this->_vars['config'] )): ?>
			,
			district:
			{
				required: "请选择企业所在地区"
			}
			<?php endif; ?>
			<?php if (in_array ( "contact" , $this->_vars['config'] )): ?>
			,
			contact:
			{
				required: "请输入企业联系人"
			}
			<?php endif; ?>
			<?php if (in_array ( "telephone" , $this->_vars['config'] ) && $this->_vars['sqlarr']['reg_type'] == 2): ?>
			,
			telephone:
			{
				required: "请输入企业联系电话",
				isPhoneNumber: jQuery.format("请正确填写联系电话")
			}
			<?php endif; ?>
			<?php if (in_array ( "email" , $this->_vars['config'] ) && $this->_vars['sqlarr']['reg_type'] == 1): ?>
			,
			reg_email:
			{
				required: "请输入企业联系邮箱",
				is_Email: jQuery.format("请正确填写邮箱")
			}
			<?php endif; ?>
			<?php if (in_array ( "address" , $this->_vars['config'] )): ?>
			,
			address:
			{
				required: "请输入企业详细地址"
			}
			<?php endif; ?>
			<?php if (in_array ( "contents" , $this->_vars['config'] )): ?>
			,
			contents:
			{
				required: "请输入企业介绍"
			}
			<?php endif; ?>
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
	// 下拉操作
	menuDown("#menu_nature","#nature","#nature_cn","#menu_nature_li","348px");
	menuDown("#menu_scale","#scale","#scale_cn","#menu_scale_li","348px");
	function menuDown(menuId,input,input_cn,menuList,width){
		$(menuId).click(function(){
			if ($(this).hasClass('current')) {
				$(this).removeClass('current');
				$(".menu_bg_layer").remove();
				$(menuList).slideUp("fast");
				$(menuId).parent("div").css("position","");
			} else {
				$(this).addClass('current');
				$(menuList).css("width",width);
				$(menuList).slideDown('fast');
				//生成背景
				$(menuId).before("<div class=\"menu_bg_layer\"></div>");
				$(".menu_bg_layer").height($(document).height());
				$(".menu_bg_layer").css({ width: $(document).width(), position: "absolute", left: "0", top: "0" , "z-index": "0", "background-color": "#ffffff"});
				$(".menu_bg_layer").css("opacity","0");
				$(".menu_bg_layer").click(function(){
					$(menuId).removeClass('current');
					$(".menu_bg_layer").remove();
					$(menuList).slideUp("fast");
					$(menuId).parent("div").css("position","");
				});
			};
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
	};
	allaround('<?php echo $this->_vars['QISHI']['site_dir']; ?>
');
	<?php if (in_array ( "trade" , $this->_vars['config'] )): ?>
	// 所属行业填充数据
	trade_filldata("#trad_list", QS_trade, "#aui_outer", "#result-list-trade", "#trade_result_show", "#trade", "<?php echo $this->_vars['QISHI']['site_dir']; ?>
");
	<?php endif; ?>
	<?php if (in_array ( "district" , $this->_vars['config'] )): ?>
	// 所在地区填充数据
	city_filldata("#city_list", QS_city_parent, QS_city, "#result-list-city", "#aui_outer_city", "#city_result_show", "#district", "<?php echo $this->_vars['QISHI']['site_dir']; ?>
");
	<?php endif; ?>
});	
<?php if ($this->_vars['QISHI']['weixin_apiopen'] == '1' && $this->_vars['QISHI']['weixin_scan_reg'] == '1'): ?>
window.setInterval(run, 5000);
function run(){
    $.get("<?php echo $this->_vars['QISHI']['site_dir']; ?>
m/login.php?act=waiting_weixin_login",function(data){
        if(data=="1"){
           window.location="<?php echo $this->_vars['QISHI']['site_dir']; ?>
";
        }
    });
}
<?php endif; ?>
</script>
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
				<div class="step tstep2 f-left active"><i class="step-icon">2</i>填写账户信息</div>
				<div class="step tstep3 f-left"><i class="step-icon">3</i>注册成功</div>
			</div>
		</div>
		<div class="reg-main clearfix">
			<form action="?act=reg_step3" method="post" id="Form1">
			<div class="reg-left-form f-left">
				<h2 class="reg-title">账户信息</h2>
				<div class="reg-form-item clearfix">
					<div class="reg-form-type f-left">密码</div>
					<div class="reg-form-content f-left">
						<input type="password" name="password" id="password" class="text text-lg span350" placeholder="请输入用户密码" />
					</div>
					<div class="verification f-left"></div>
				</div>
				<div class="reg-form-item clearfix">
					<div class="reg-form-type f-left">重复密码</div>
					<div class="reg-form-content f-left">
						<input type="password" name="rpassword" id="rpassword" class="text text-lg span350" placeholder="重复输入用户密码" />
					</div>
					<div class="verification f-left"></div>
				</div>
				<?php if ($this->_vars['config']): ?>
				<h2 class="reg-title">企业信息</h2>
				<?php endif; ?>
				<!-- 选填信息 start -->
				<?php if (in_array ( "companyname" , $this->_vars['config'] )): ?>
				<div class="reg-form-item clearfix">
					<div class="reg-form-type f-left">企业名称</div>
					<div class="reg-form-content f-left">
						<input type="text" name="companyname" id="companyname" class="text text-lg span350" placeholder="请输入企业名称" />
					</div>
					<div class="verification f-left"></div>
				</div>
				<?php endif; ?>

				<?php if (in_array ( "nature" , $this->_vars['config'] )): ?>
				<div class="reg-form-item clearfix">
					<div class="reg-form-type f-left">企业性质</div>
					<div class="drop-control_reg f-left">
						<div class="drop-box" id="menu_nature">
							<span>请选择</span>
							<i class="drop-icon"></i>
						</div>
						<div style="position: relative;">
							<ul class="drop-list" id="menu_nature_li">
								<?php echo tpl_function_qishi_get_classify(array('set' => "列表名:c_nature,类型:QS_company_type"), $this);?>
		              			<?php if (count((array)$this->_vars['c_nature'])): foreach ((array)$this->_vars['c_nature'] as $this->_vars['list']): ?>
		              			<li id="<?php echo $this->_vars['list']['id']; ?>
" title="<?php echo $this->_vars['list']['categoryname']; ?>
"><?php echo $this->_vars['list']['categoryname']; ?>
</li>
		              			<?php endforeach; endif; ?>
							</ul>
						</div>
						<input name="nature" type="hidden" id="nature" value="" />
             	 		<input name="nature_cn" type="hidden" id="nature_cn" value="" />
					</div>
					<div class="verification f-left"></div>
				</div>
				<?php endif; ?>

				<?php if (in_array ( "trade" , $this->_vars['config'] )): ?>
				<div class="reg-form-item clearfix" id="jobsTrad">
					<div class="reg-form-type f-left">所属行业</div>
					<div class="drop-control_reg f-left" style="position: relative;">
						<div class="drop-box ucc-default">
							<span id="tradText">请选择</span>
							<i class="drop-icon"></i>
						</div>
						<!-- 所属行业弹出框 -->
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
																		<div class="selector-header"><span class="selector-title">行业选择</span><div></div><span class="selector-close">X</span><div class="clear"></div></div>

																		<div class="item-table">
																			<table class="options-table options-table-7">
																				<tbody class="item-list"><tr><td class="bno"><table><tbody id="trad_list">
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
						<!-- 所属行业弹出框 End-->
						<input type="hidden" id="trade" name="trade" value="">
						<input type="hidden" id="trade_cn" name="trade_cn" value="">
					</div>
					<div class="verification f-left"></div>
				</div>
				<?php endif; ?>

				<?php if (in_array ( "scale" , $this->_vars['config'] )): ?>
				<div class="reg-form-item clearfix">
					<div class="reg-form-type f-left">企业规模</div>
					<div class="drop-control_reg f-left">
						<div class="drop-box " id="menu_scale">
							<span>请选择</span>
							<i class="drop-icon"></i>
						</div>
						<div style="position: relative;">
							<ul class="drop-list" id="menu_scale_li">
								<?php echo tpl_function_qishi_get_classify(array('set' => "列表名:c_scale,类型:QS_scale"), $this);?>
		              			<?php if (count((array)$this->_vars['c_scale'])): foreach ((array)$this->_vars['c_scale'] as $this->_vars['list']): ?>
		              			<li id="<?php echo $this->_vars['list']['id']; ?>
" title="<?php echo $this->_vars['list']['categoryname']; ?>
"><?php echo $this->_vars['list']['categoryname']; ?>
</li>
		              			<?php endforeach; endif; ?>
							</ul>
						</div>
						<input name="scale" type="hidden" id="scale" value="" />
             	 		<input name="scale_cn" type="hidden" id="scale_cn" value="" />
					</div>
					<div class="verification f-left"></div>
				</div>
				<?php endif; ?>
				
				<?php if (in_array ( "district" , $this->_vars['config'] )): ?>
				<div class="reg-form-item clearfix" id="jobsCity">
					<div class="reg-form-type f-left">所在地区</div>
					<div class="drop-control_reg f-left" style="position: relative;">
						<div class="drop-box ucc-default">
							<span id="cityText">请选择</span>
							<i class="drop-icon"></i>
						</div>
						<!-- 所在地区弹出框 -->
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
																		<div class="selector-header"><span class="selector-title">地区选择</span><div></div><span class="selector-close">X</span><div class="clear"></div></div>

																		<div class="data-row-list data-row-main" id="city_list">
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
						<!-- 所在地区弹出框 End-->
						<input id="district" type="hidden" value="" name="district">
			            <input id="sdistrict" type="hidden" value="" name="sdistrict">
			            <input id="districtID" type="hidden" value="">
			            <input name="district_cn" id="district_cn" type="hidden" value="" />
					</div>
					<div class="verification f-left"></div>
				</div>
				<?php endif; ?>

				<?php if (in_array ( "contact" , $this->_vars['config'] )): ?>
				<div class="reg-form-item clearfix">
					<div class="reg-form-type f-left">联系人</div>
					<div class="reg-form-content f-left">
						<input type="text" name="contact" id="contact" class="text text-lg span350" placeholder="请输入企业联系人" />
					</div>
					<div class="verification f-left"></div>
				</div>
				<?php endif; ?>

				<?php if (in_array ( "telephone" , $this->_vars['config'] ) && $this->_vars['sqlarr']['reg_type'] == 2): ?>
				<div class="reg-form-item clearfix">
					<div class="reg-form-type f-left">联系电话</div>
					<div class="reg-form-content f-left">
						<input type="text" name="telephone" id="telephone" class="text text-lg span350" value="<?php echo $this->_vars['sqlarr']['mobile']; ?>
" placeholder="请输入企业联系电话" />
					</div>
					<div class="verification f-left"></div>
				</div>
				<?php endif; ?>

				<?php if (in_array ( "email" , $this->_vars['config'] ) && $this->_vars['sqlarr']['reg_type'] == 1): ?>
				<div class="reg-form-item clearfix">
					<div class="reg-form-type f-left">联系邮箱</div>
					<div class="reg-form-content f-left">
						<input type="text" name="reg_email" id="reg_email" class="text text-lg span350" value="<?php echo $this->_vars['sqlarr']['email']; ?>
" placeholder="请输入企业联系邮箱" />
					</div>
					<div class="verification f-left"></div>
				</div>
				<?php endif; ?>
				<?php if (in_array ( "address" , $this->_vars['config'] )): ?>
				<div class="reg-form-item clearfix">
					<div class="reg-form-type f-left">详细地址</div>
					<div class="reg-form-content f-left">
						<input type="text" name="address" id="address" class="text text-lg span350" placeholder="请输入企业详细地址" />
					</div>
					<div class="verification f-left"></div>
				</div>
				<?php endif; ?>

				<?php if (in_array ( "contents" , $this->_vars['config'] )): ?>
				<div class="reg-form-item clearfix">
					<div class="reg-form-type f-left">企业介绍</div>
					<div class="reg-form-content f-left">
						<input type="text" name="contents" id="contents" class="text text-lg span350" placeholder="企业介绍" />
					</div>
					<div class="verification f-left"></div>
				</div>
				<?php endif; ?>
				<!-- 选填信息 end -->
				<div class="reg-form-item special clearfix">
					<div class="reg-form-type f-left">&nbsp;</div>
					<div class="reg-form-content f-left">

						<input type="hidden" name="utype" value="<?php echo $this->_vars['sqlarr']['utype']; ?>
"/>
						<input type="hidden" name="email" value="<?php echo $this->_vars['sqlarr']['email']; ?>
"/>
						<input type="hidden" name="reg_type" value="<?php echo $this->_vars['sqlarr']['reg_type']; ?>
"/>
						<input type="hidden" name="mobile" value="<?php echo $this->_vars['sqlarr']['mobile']; ?>
"/>
						<input type="hidden" name="token" value="<?php echo $this->_vars['token']; ?>
"/>
						<input type="submit" value="下一步" class="btn btn-lg blue span1" />
					</div>
				</div>
			</div>
			</form>
			<?php if ($this->_vars['QISHI']['weixin_apiopen'] == '1' && $this->_vars['QISHI']['weixin_scan_reg'] == '1'): ?>
			<div class="reg-right-box f-right">
				<div class="weixin-reg">
					<h4>微信注册</h4>
					<div class="weixin-img">
						<?php echo $this->_vars['qrcode_img']; ?>

					</div>
					<p>打开微信扫描二维码</p>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("user/footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>