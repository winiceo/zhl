<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:33 CST */ ?>
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
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.validate.min.js" type='text/javascript' language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.gotoemail.js" type='text/javascript' language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/emailAutoComplete.js" type="text/javascript"></script>
<script>
$(document).ready(function($) {
	$(".reg-warning").eq(0).show();
	$(function() {
		$('.member-type').live('click', function(){
			$(this).addClass('select').siblings('.member-type').removeClass('select');
			var utype=$(this).attr("member-type"), index = $('.member-type').index(this);
			$(".utype").val(utype);
			$(".reg-warning").eq(index).show().siblings('.reg-warning').hide();
		})
	});
	//验证码随机
	$("#getcode").live('click',function(){
		$(this).attr('src','<?php echo $this->_vars['QISHI']['site_dir']; ?>
include/imagecaptcha.php?t='+Math.random()+'');
	});
	// 手机注册 表单验证 
	$("#reg_by_mobile").validate({
		submitHandler:function(form){
			if($("#agreement_mobile").attr("checked")==false)
			{
				alert("您必须同意[注册协议]才能继续下一步");
				return false;
			}
        	form.submit();  
		},
		success: function(lable) {
				lable.text(" ").addClass("ver-success");
				$("#sms_send").attr("disabled",false);
			},
		rules:{	
			mobile:
			{
				required: true,
				ismobile : true,
				remote:{     
					url:"<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/ajax_user.php",     
					type:"post",    
					data:{"mobile":function (){return $("#mobile").val()},"act":"check_mobile","time":function (){return new Date().getTime()}}     
				}
			},
			mobile_vcode:
			{
				required: true,
				remote:{     
					url:"<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/ajax_user.php",     
					type:"post",    
					data:{"mobile":function (){return $("#mobile").val()},"mobile_vcode":function (){return $("#mobile_vcode").val()},"act":"check_reg_send_sms","time":function (){return new Date().getTime()}}     
				}
			}
		},
		messages: {
			mobile: {
				required: "请填写手机号",
				remote: jQuery.format("手机号已存在或不合法")
			},
			mobile_vcode:
			{
				required: "请输入手机验证码",
				remote: jQuery.format("验证码错误")	
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
	// 手机
	jQuery.validator.addMethod("ismobile", function(value, element) { 
	var tel = /^(13|15|18|17)\d{9}$/;
	var $cstr= false;
	if (tel.test(value)) $cstr= true;
	return $cstr || this.optional(element); 
	}, "请输入正确的手机号");

	// 邮箱注册 表单验证 
	$("#reg_by_email").validate({
		submitHandler:function(form){
			if($("#agreement_email").attr("checked")==false)
			{
				alert("您必须同意[注册协议]才能继续下一步");
				return false;
			}
			var check_reg_email="<?php echo $this->_vars['QISHI']['check_reg_email']; ?>
";
			if(check_reg_email=="1")
			{	
				$("#reg_email_submit").hide();
				$("#reg_email_submit_").show();
				$.post('<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/ajax_user.php', {"act":"reg_sendemail","email":$("#email").val(),"utype":$("#utype").val()}, function(data) {
					if ($.trim(data)=='success') 
					{
						$(".reg-main").hide();
						$("#send_email").html($("#email").val());
						$("#goto_email").attr('email', $("#email").val());
						$(".common-status").show();
						// 立即进入邮箱
						gotoemail("#goto_email");
					}
					else
					{
						$("#reg_email_submit").show();
						$("#reg_email_submit_").hide();
						alert(data)
					}
				});
				return false;
			}

			else
			{
				form.submit(); 
			}
		},
		success: function(lable) {
				lable.text(" ").addClass("ver-success");
			},
		rules:{	
			email:
			{
				required: true,
				email:true,
				remote:{     
					url:"<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/ajax_user.php",     
					type:"post",    
					data:{"email":function (){return $("#email").val()},"act":"check_email"}     
				}
			},
			postcaptcha:
			{
				required: true,
				remote:{     
					url:"<?php echo $this->_vars['QISHI']['site_dir']; ?>
include/imagecaptcha.php",     
					type:"post",    
					data:{"postcaptcha":function (){return $("#postcaptcha").val()},"act":"verify"}     
				}
			}
		},
		messages: {
			email:
			{
				required: "请输入邮箱",
				email: "请输入正确格式的邮箱",
				remote: jQuery.format("该邮箱已存在或不合法！")	
			},
			postcaptcha:
			{
				required: "请输验证码",
				remote: jQuery.format("验证码错误")	
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
	// 点击发送到手机验证码
	$("#sms_send").click(function() {
		var checkText = $("#mobile").parent().next().text(),
			SysSecond = 180;
		if ($("#mobile").val().length <= 0) {
			$("#sms_send").attr("disabled",true);
			$(this).addClass('error');
			$("#mobile").parent().next().html('<label for="mobile" generated="true" class="error" style="display: inline;">请输入手机号</label>');
			return false;
		} else if (checkText.indexOf('手机号已被注册') > -1) {
			$("#sms_send").attr("disabled",true);
			return false;
		} else {
			$.post('<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/ajax_user.php', {'act':'reg_send_sms','mobile':$('#mobile').val()}, function(data) {
				if($.trim(data)=='success')
				{
					$("#sms_send").hide();
					$("#send_ok").css('display', 'inline-block');
					$("#send_ok").html( SysSecond +" 秒后可重新获取");
					function SetRemainTime()
					{
						if (SysSecond > 0)
						{
							SysSecond --;
							$("#sms_send").closest('.reg-form-item').addClass('reg-form-item-sms');
							$("#sms_send").hide();
							$("#send_ok").css('display', 'inline-block');
							$("#send_ok").html( SysSecond +" 秒后可重新获取");
							$(".sms_send_succeed").show();
						} 
						else 
						{
							window.clearInterval(InterValObj);
							$("#sms_send").show();
							$("#sms_send").closest('.reg-form-item').removeClass('reg-form-item-sms');
							$("#send_ok").hide();
							$(".sms_send_succeed").hide();
						}
					}
					InterValObj = window.setInterval(SetRemainTime, 1000);
				}
				else
				{
					alert(data);
				}
			});
		};
	});
	//修改手机号 手机验证码变化
	$("#mobile").change(function() {
		$("#sms_send").show();
		$("#send_ok").hide();
	});
	// 改变注册方式 手机/邮箱
	$("#change_reg_type").click(function() {
		var reg_type =$(this).attr("reg_type");
		if(reg_type==1)
		{
			$(this).attr("reg_type","2");
			$(this).html("使用手机注册>>");
			$("#reg_type").val("2");
			$("#reg_by_email").show();
			$("#reg_by_mobile").hide();
		}
		else
		{
			$(this).attr("reg_type","1");
			$(this).html("使用邮箱注册>>");
			$("#reg_type").val("1");
			$("#reg_by_mobile").show();
			$("#reg_by_email").hide();
		}
	});
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
				<div class="step tstep1 f-left active"><i class="step-icon">1</i>设置登录名</div>
				<div class="step tstep2 f-left"><i class="step-icon">2</i>填写账户信息</div>
				<div class="step tstep3 f-left"><i class="step-icon">3</i>注册成功</div>
			</div>
		</div>
		<!-- 用手机注册 -->
		
		<div class="reg-main clearfix">
			<div class="reg-left-form f-left">
				<div class="reg-form-item reg-form-item-w clearfix">
					<div class="reg-form-type f-left">会员类型</div>
					<div class="reg-form-content f-left">
						<div class="member-type-wrap clearfix">
							<div class="member-type select f-left" member-type="2"><i class="member-icon m-icon1"></i>个人</div>
							<div class="member-type f-left" member-type="1"><i class="member-icon m-icon2"></i>企业</div>
							<div class="member-type f-left" member-type="3"><i class="member-icon m-icon3"></i>猎头</div>
							<div class="member-type f-left" member-type="4"><i class="member-icon m-icon4"></i>培训</div>
						</div>
					</div>
				</div>
				<!-- 注册提示信息 -->
				<!-- 个人 -->
				<div class="reg-warning">
					<i class="regw-arrow"><em></em></i>
					<div class="warlist"><span class="colorfe">1.免费创简历</span> — 注册个人会员，免费创建多份简历</div>
					<div class="warlist"><span class="colorfe">2.职位海量淘</span> — 网站每天新增大量职位，高薪等你挑</div>
					<div class="warlist warlist-last"><span class="colorfe">3.简历轻松投</span> — 简历投递无限量，是金子总会发光</div>
				</div>
				<!-- 企业 -->
				<div class="reg-warning">
					<i class="regw-arrow regw-arrow-com"><em></em></i>
					<div class="warlist"><span class="colorfe">1.发布招聘信息</span> — 注册企业会员，轻轻松松招聘人才</div>
					<div class="warlist"><span class="colorfe">2.收取简历投递</span> — 无限量收取求职者投递的简历</div>
					<div class="warlist warlist-last"><span class="colorfe">3.多种职位分享</span> — 微信招聘让企业更出色，招人更便捷</div>
				</div>
				<!-- 猎头 -->
				<div class="reg-warning">
					<i class="regw-arrow regw-arrow-hunter"><em></em></i>
					<div class="warlist"><span class="colorfe">1.发布招聘信息</span> — 注册猎头会员，扩充后备人才库</div>
					<div class="warlist"><span class="colorfe">2.收取简历投递</span> — 无限量收取求职者投递的简历</div>
					<div class="warlist warlist-last"><span class="colorfe">3.多种人才选择</span> — 网站实时更新大量简历，总有你需要的</div>
				</div>
				<!-- 培训 -->
				<div class="reg-warning">
					<i class="regw-arrow regw-arrow-train"><em></em></i>
					<div class="warlist"><span class="colorfe">1.发布培训信息</span> — 注册培训会员，轻轻松松招收学员</div>
					<div class="warlist"><span class="colorfe">2.收取培训申请</span> — 无限量在线收取学员的课程申请信息</div>
					<div class="warlist warlist-last"><span class="colorfe">3.提高机构影响力</span> — 提高培训机构的地区影响力</div>
				</div>
				<!-- 注册提示信息 end-->
				<!-- 手机注册开始  -->
				<?php if ($this->_vars['SMSconfig']['open'] == 1): ?>
				<form action="?act=reg_step2" id="reg_by_mobile" method="post">
					<div class="reg-form-item clearfix">
						<div class="reg-form-type f-left">手机</div>
						<div class="reg-form-content f-left">
							<input type="text" name="mobile" id="mobile" class="text text-lg span350" placeholder="请输入您的手机号码" />
						</div>
						<div class="verification f-left"></div>
					</div>
					<div class="reg-form-item clearfix">
						<div class="reg-form-type f-left">验证码</div>
						<div class="reg-form-content f-left">
							<input type="text" name="mobile_vcode" id="mobile_vcode" class="text text-lg span180" placeholder="验证码" />
						</div>
						<div class="reg-form-other verification f-left">
							<input type="button" href="javascript:void(0)" class="btn short-text-btn" value="获取短信验证码" id="sms_send">
							<a id="send_ok" class="btn short-text-btn" style="display: none;"><span id="remainTime"></span>秒后可重新获取</a>
						</div>
					</div>
					<div class="sms_send_succeed"><label class="ver-success" style="display: inline;">验证码已发送至您的手机，请查收</label></div>
					<div class="reg-form-item special clearfix">
						<div class="reg-form-type f-left">&nbsp;</div>
						<div class="reg-form-content f-left">
							<div class="agree-confirm">
								<label><input class="argeeinput" type="checkbox" name="agreement_mobile" id="agreement_mobile" value="1" checked="checked"/>我已阅读并同意</label><a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
agreement/">《<?php echo $this->_vars['QISHI']['site_name']; ?>
用户服务协议》</a>
							</div>
						</div>
					</div>
					<div class="reg-form-item special clearfix">
						<div class="reg-form-type f-left">&nbsp;</div>
						<div class="reg-form-content f-left">
							<input type="hidden" name="utype" class="utype" value="2"/>
							<input type="hidden" name="token" value="<?php echo $this->_vars['token']; ?>
" />
							<input type="submit" value="下一步" class="btn btn-lg blue span1"/>
						</div>
					</div>
				</form>
				<?php endif; ?>
				<!-- 手机注册结束  -->
				<!-- 邮箱注册开始  -->
				<form action="?act=reg_step2_email" id="reg_by_email" method="post" <?php if ($this->_vars['SMSconfig']['open'] == 1): ?>style="display: none;"<?php endif; ?>>
					<div class="reg-form-item clearfix">
						<div class="reg-form-type f-left">邮箱</div>
						<div class="reg-form-content f-left">
							<input type="text" name="email" id="email" class="text text-lg span350 inputElem" placeholder="请输入您的邮箱" />
						</div>
						<div class="verification f-left"></div>
					</div>
					<?php if ($this->_vars['verify_userreg'] == "1"): ?>
					<div class="reg-form-item clearfix">
						<div class="reg-form-type f-left">验证码</div>
						<div class="reg-form-content f-left">
							<input type="text" name="postcaptcha" id="postcaptcha" class="text text-lg span180" placeholder="验证码" />
						</div>
						<div class="reg-form-other f-left verification" style="padding-left: 20px;">
							<div class="ver-box f-left"><img src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
include/imagecaptcha.php?t=<?php echo $this->_vars['random']; ?>
" id="getcode" align="absmiddle"  style="cursor:pointer;width: 148px;height: 38px;" title="看不请验证码？点击更换一张"  border="0" /></div>
						</div>
					</div>
					<?php endif; ?>
					<div class="reg-form-item special clearfix">
						<div class="reg-form-type f-left">&nbsp;</div>
						<div class="reg-form-content f-left">
							<div class="agree-confirm">
								<label><input type="checkbox" name="agreement_email" id="agreement_email" value="1" checked="checked"/>我已阅读并同意</label><a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
agreement/">《<?php echo $this->_vars['QISHI']['site_name']; ?>
用户服务协议》</a>
							</div>
						</div>
					</div>
					<div class="reg-form-item special clearfix">
						<div class="reg-form-type f-left">&nbsp;</div>
						<div class="reg-form-content f-left">
							<input type="hidden" name="utype" class="utype" value="2" id="utype"/>
							<input type="hidden" name="token" value="<?php echo $this->_vars['token']; ?>
" />
							<input type="submit" value="下一步" class="btn btn-lg blue span1" id="reg_email_submit"/>
							<input type="text" value="正在发送邮件..." class="btn btn-lg blue span1" id="reg_email_submit_"  style="display: none;"disabled/>
						</div>
					</div>
				</form>
				<!-- 邮箱注册结束  -->
				<?php if ($this->_vars['SMSconfig']['open'] == 1): ?>
				<div class="reg-form-item special clearfix">
					<div class="reg-form-type f-left">&nbsp;</div>
					<div class="reg-form-content f-left">
						<div class="agree-confirm">您也可以&nbsp;<a class="c" href="javascript:void(0)" id="change_reg_type" reg_type="1">使用邮箱注册>></a></div>
					</div>
				</div>
				<?php endif; ?>
			</div>
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
		<div class="common-status" style="display: none;padding: 70px 0 50px 0;">
			<div class="status-main">
				<span><img src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/icon-success.png" alt="成功" /></span>还差两步即可完成注册
			</div>
			<p>我们已经向您的邮箱<span id="send_email"></span>发送了一封激活邮件，请点击邮件中的链接完成注册！</p>
			<div class="status-btn"><a href="#" target="_blank" id="goto_email" email="" class="btn btn-lg blue span2">立即进入邮箱</a></div>
		</div>
</div>

	

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("user/footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>