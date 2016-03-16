<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:33 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php echo $this->_vars['title']; ?>
</title>
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<meta name="author" content="�Һ�«" />
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
	//��֤�����
	$("#getcode").live('click',function(){
		$(this).attr('src','<?php echo $this->_vars['QISHI']['site_dir']; ?>
include/imagecaptcha.php?t='+Math.random()+'');
	});
	// �ֻ�ע�� ����֤ 
	$("#reg_by_mobile").validate({
		submitHandler:function(form){
			if($("#agreement_mobile").attr("checked")==false)
			{
				alert("������ͬ��[ע��Э��]���ܼ�����һ��");
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
				required: "����д�ֻ���",
				remote: jQuery.format("�ֻ����Ѵ��ڻ򲻺Ϸ�")
			},
			mobile_vcode:
			{
				required: "�������ֻ���֤��",
				remote: jQuery.format("��֤�����")	
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
	// �ֻ�
	jQuery.validator.addMethod("ismobile", function(value, element) { 
	var tel = /^(13|15|18|17)\d{9}$/;
	var $cstr= false;
	if (tel.test(value)) $cstr= true;
	return $cstr || this.optional(element); 
	}, "��������ȷ���ֻ���");

	// ����ע�� ����֤ 
	$("#reg_by_email").validate({
		submitHandler:function(form){
			if($("#agreement_email").attr("checked")==false)
			{
				alert("������ͬ��[ע��Э��]���ܼ�����һ��");
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
						// ������������
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
				required: "����������",
				email: "��������ȷ��ʽ������",
				remote: jQuery.format("�������Ѵ��ڻ򲻺Ϸ���")	
			},
			postcaptcha:
			{
				required: "������֤��",
				remote: jQuery.format("��֤�����")	
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
	// ������͵��ֻ���֤��
	$("#sms_send").click(function() {
		var checkText = $("#mobile").parent().next().text(),
			SysSecond = 180;
		if ($("#mobile").val().length <= 0) {
			$("#sms_send").attr("disabled",true);
			$(this).addClass('error');
			$("#mobile").parent().next().html('<label for="mobile" generated="true" class="error" style="display: inline;">�������ֻ���</label>');
			return false;
		} else if (checkText.indexOf('�ֻ����ѱ�ע��') > -1) {
			$("#sms_send").attr("disabled",true);
			return false;
		} else {
			$.post('<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/ajax_user.php', {'act':'reg_send_sms','mobile':$('#mobile').val()}, function(data) {
				if($.trim(data)=='success')
				{
					$("#sms_send").hide();
					$("#send_ok").css('display', 'inline-block');
					$("#send_ok").html( SysSecond +" �������»�ȡ");
					function SetRemainTime()
					{
						if (SysSecond > 0)
						{
							SysSecond --;
							$("#sms_send").closest('.reg-form-item').addClass('reg-form-item-sms');
							$("#sms_send").hide();
							$("#send_ok").css('display', 'inline-block');
							$("#send_ok").html( SysSecond +" �������»�ȡ");
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
	//�޸��ֻ��� �ֻ���֤��仯
	$("#mobile").change(function() {
		$("#sms_send").show();
		$("#send_ok").hide();
	});
	// �ı�ע�᷽ʽ �ֻ�/����
	$("#change_reg_type").click(function() {
		var reg_type =$(this).attr("reg_type");
		if(reg_type==1)
		{
			$(this).attr("reg_type","2");
			$(this).html("ʹ���ֻ�ע��>>");
			$("#reg_type").val("2");
			$("#reg_by_email").show();
			$("#reg_by_mobile").hide();
		}
		else
		{
			$(this).attr("reg_type","1");
			$(this).html("ʹ������ע��>>");
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
	<!-- ͷ�� -->
	<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("user/reg_header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
	<!-- main -->
	<div class="container">
		<div class="step_wrap">
			<div class="three-step-bar clearfix">
				<div class="step tstep1 f-left active"><i class="step-icon">1</i>���õ�¼��</div>
				<div class="step tstep2 f-left"><i class="step-icon">2</i>��д�˻���Ϣ</div>
				<div class="step tstep3 f-left"><i class="step-icon">3</i>ע��ɹ�</div>
			</div>
		</div>
		<!-- ���ֻ�ע�� -->
		
		<div class="reg-main clearfix">
			<div class="reg-left-form f-left">
				<div class="reg-form-item reg-form-item-w clearfix">
					<div class="reg-form-type f-left">��Ա����</div>
					<div class="reg-form-content f-left">
						<div class="member-type-wrap clearfix">
							<div class="member-type select f-left" member-type="2"><i class="member-icon m-icon1"></i>����</div>
							<div class="member-type f-left" member-type="1"><i class="member-icon m-icon2"></i>��ҵ</div>
							<div class="member-type f-left" member-type="3"><i class="member-icon m-icon3"></i>��ͷ</div>
							<div class="member-type f-left" member-type="4"><i class="member-icon m-icon4"></i>��ѵ</div>
						</div>
					</div>
				</div>
				<!-- ע����ʾ��Ϣ -->
				<!-- ���� -->
				<div class="reg-warning">
					<i class="regw-arrow"><em></em></i>
					<div class="warlist"><span class="colorfe">1.��Ѵ�����</span> �� ע����˻�Ա����Ѵ�����ݼ���</div>
					<div class="warlist"><span class="colorfe">2.ְλ������</span> �� ��վÿ����������ְλ����н������</div>
					<div class="warlist warlist-last"><span class="colorfe">3.��������Ͷ</span> �� ����Ͷ�����������ǽ����ܻᷢ��</div>
				</div>
				<!-- ��ҵ -->
				<div class="reg-warning">
					<i class="regw-arrow regw-arrow-com"><em></em></i>
					<div class="warlist"><span class="colorfe">1.������Ƹ��Ϣ</span> �� ע����ҵ��Ա������������Ƹ�˲�</div>
					<div class="warlist"><span class="colorfe">2.��ȡ����Ͷ��</span> �� ��������ȡ��ְ��Ͷ�ݵļ���</div>
					<div class="warlist warlist-last"><span class="colorfe">3.����ְλ����</span> �� ΢����Ƹ����ҵ����ɫ�����˸����</div>
				</div>
				<!-- ��ͷ -->
				<div class="reg-warning">
					<i class="regw-arrow regw-arrow-hunter"><em></em></i>
					<div class="warlist"><span class="colorfe">1.������Ƹ��Ϣ</span> �� ע����ͷ��Ա��������˲ſ�</div>
					<div class="warlist"><span class="colorfe">2.��ȡ����Ͷ��</span> �� ��������ȡ��ְ��Ͷ�ݵļ���</div>
					<div class="warlist warlist-last"><span class="colorfe">3.�����˲�ѡ��</span> �� ��վʵʱ���´�����������������Ҫ��</div>
				</div>
				<!-- ��ѵ -->
				<div class="reg-warning">
					<i class="regw-arrow regw-arrow-train"><em></em></i>
					<div class="warlist"><span class="colorfe">1.������ѵ��Ϣ</span> �� ע����ѵ��Ա��������������ѧԱ</div>
					<div class="warlist"><span class="colorfe">2.��ȡ��ѵ����</span> �� ������������ȡѧԱ�Ŀγ�������Ϣ</div>
					<div class="warlist warlist-last"><span class="colorfe">3.��߻���Ӱ����</span> �� �����ѵ�����ĵ���Ӱ����</div>
				</div>
				<!-- ע����ʾ��Ϣ end-->
				<!-- �ֻ�ע�Ὺʼ  -->
				<?php if ($this->_vars['SMSconfig']['open'] == 1): ?>
				<form action="?act=reg_step2" id="reg_by_mobile" method="post">
					<div class="reg-form-item clearfix">
						<div class="reg-form-type f-left">�ֻ�</div>
						<div class="reg-form-content f-left">
							<input type="text" name="mobile" id="mobile" class="text text-lg span350" placeholder="�����������ֻ�����" />
						</div>
						<div class="verification f-left"></div>
					</div>
					<div class="reg-form-item clearfix">
						<div class="reg-form-type f-left">��֤��</div>
						<div class="reg-form-content f-left">
							<input type="text" name="mobile_vcode" id="mobile_vcode" class="text text-lg span180" placeholder="��֤��" />
						</div>
						<div class="reg-form-other verification f-left">
							<input type="button" href="javascript:void(0)" class="btn short-text-btn" value="��ȡ������֤��" id="sms_send">
							<a id="send_ok" class="btn short-text-btn" style="display: none;"><span id="remainTime"></span>�������»�ȡ</a>
						</div>
					</div>
					<div class="sms_send_succeed"><label class="ver-success" style="display: inline;">��֤���ѷ����������ֻ��������</label></div>
					<div class="reg-form-item special clearfix">
						<div class="reg-form-type f-left">&nbsp;</div>
						<div class="reg-form-content f-left">
							<div class="agree-confirm">
								<label><input class="argeeinput" type="checkbox" name="agreement_mobile" id="agreement_mobile" value="1" checked="checked"/>�����Ķ���ͬ��</label><a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
agreement/">��<?php echo $this->_vars['QISHI']['site_name']; ?>
�û�����Э�顷</a>
							</div>
						</div>
					</div>
					<div class="reg-form-item special clearfix">
						<div class="reg-form-type f-left">&nbsp;</div>
						<div class="reg-form-content f-left">
							<input type="hidden" name="utype" class="utype" value="2"/>
							<input type="hidden" name="token" value="<?php echo $this->_vars['token']; ?>
" />
							<input type="submit" value="��һ��" class="btn btn-lg blue span1"/>
						</div>
					</div>
				</form>
				<?php endif; ?>
				<!-- �ֻ�ע�����  -->
				<!-- ����ע�Ὺʼ  -->
				<form action="?act=reg_step2_email" id="reg_by_email" method="post" <?php if ($this->_vars['SMSconfig']['open'] == 1): ?>style="display: none;"<?php endif; ?>>
					<div class="reg-form-item clearfix">
						<div class="reg-form-type f-left">����</div>
						<div class="reg-form-content f-left">
							<input type="text" name="email" id="email" class="text text-lg span350 inputElem" placeholder="��������������" />
						</div>
						<div class="verification f-left"></div>
					</div>
					<?php if ($this->_vars['verify_userreg'] == "1"): ?>
					<div class="reg-form-item clearfix">
						<div class="reg-form-type f-left">��֤��</div>
						<div class="reg-form-content f-left">
							<input type="text" name="postcaptcha" id="postcaptcha" class="text text-lg span180" placeholder="��֤��" />
						</div>
						<div class="reg-form-other f-left verification" style="padding-left: 20px;">
							<div class="ver-box f-left"><img src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
include/imagecaptcha.php?t=<?php echo $this->_vars['random']; ?>
" id="getcode" align="absmiddle"  style="cursor:pointer;width: 148px;height: 38px;" title="��������֤�룿�������һ��"  border="0" /></div>
						</div>
					</div>
					<?php endif; ?>
					<div class="reg-form-item special clearfix">
						<div class="reg-form-type f-left">&nbsp;</div>
						<div class="reg-form-content f-left">
							<div class="agree-confirm">
								<label><input type="checkbox" name="agreement_email" id="agreement_email" value="1" checked="checked"/>�����Ķ���ͬ��</label><a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
agreement/">��<?php echo $this->_vars['QISHI']['site_name']; ?>
�û�����Э�顷</a>
							</div>
						</div>
					</div>
					<div class="reg-form-item special clearfix">
						<div class="reg-form-type f-left">&nbsp;</div>
						<div class="reg-form-content f-left">
							<input type="hidden" name="utype" class="utype" value="2" id="utype"/>
							<input type="hidden" name="token" value="<?php echo $this->_vars['token']; ?>
" />
							<input type="submit" value="��һ��" class="btn btn-lg blue span1" id="reg_email_submit"/>
							<input type="text" value="���ڷ����ʼ�..." class="btn btn-lg blue span1" id="reg_email_submit_"  style="display: none;"disabled/>
						</div>
					</div>
				</form>
				<!-- ����ע�����  -->
				<?php if ($this->_vars['SMSconfig']['open'] == 1): ?>
				<div class="reg-form-item special clearfix">
					<div class="reg-form-type f-left">&nbsp;</div>
					<div class="reg-form-content f-left">
						<div class="agree-confirm">��Ҳ����&nbsp;<a class="c" href="javascript:void(0)" id="change_reg_type" reg_type="1">ʹ������ע��>></a></div>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<?php if ($this->_vars['QISHI']['weixin_apiopen'] == '1' && $this->_vars['QISHI']['weixin_scan_reg'] == '1'): ?>
			<div class="reg-right-box f-right">
				<div class="weixin-reg">
					<h4>΢��ע��</h4>
					<div class="weixin-img">
						<?php echo $this->_vars['qrcode_img']; ?>

					</div>
					<p>��΢��ɨ���ά��</p>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<div class="common-status" style="display: none;padding: 70px 0 50px 0;">
			<div class="status-main">
				<span><img src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/icon-success.png" alt="�ɹ�" /></span>���������������ע��
			</div>
			<p>�����Ѿ�����������<span id="send_email"></span>������һ�⼤���ʼ��������ʼ��е��������ע�ᣡ</p>
			<div class="status-btn"><a href="#" target="_blank" id="goto_email" email="" class="btn btn-lg blue span2">������������</a></div>
		</div>
</div>

	

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("user/footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>