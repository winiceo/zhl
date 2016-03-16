<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_pageinfo.php'); $this->register_function("qishi_pageinfo", "tpl_function_qishi_pageinfo",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:33 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<?php echo tpl_function_qishi_pageinfo(array('set' => "列表名:page,调用:QS_login"), $this);?>
<title><?php echo $this->_vars['page']['title']; ?>
</title>
<meta name="description" content="<?php echo $this->_vars['page']['description']; ?>
">
<meta name="keywords" content="<?php echo $this->_vars['page']['keywords']; ?>
">
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<meta name="author" content="骑士CMS" />
<meta name="copyright" content="74cms.com" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/reg.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.js" type='text/javascript' language="javascript"></script>
<script type="text/javascript">
$(document).ready(function()
{
	//验证码随机
	$("#getcode").live('click',function(){
		$(this).attr('src','<?php echo $this->_vars['QISHI']['site_dir']; ?>
include/imagecaptcha.php?t='+Math.random()+'');
	});
	// 背景图片
	var aDiv = $(".banner-list").find("div"),
		index = 0,
		timer = null;
	timer = setInterval(function(){
		startFocus();
	},10000);
	function startFocus(){
		index++;
		index = index > aDiv.size()-1 ? 0 :index;
		aDiv.eq(index)
			 .stop()
			 .animate({'opacity':1},300)
			 .css({'z-index':10})
			 .siblings()
			 .stop()
			 .animate({'opacity':0},300)
			 .css({'z-index':0});
	}
	function stopFoucs(){
		clearInterval(timer);
	};
	// 选择登录方式 
	var wxrun = '';
	$('.login-type').toggle(function(){
		$('#pcLogin').hide();
		$('#codeLogin').show();
		$('#login-box h1').html('微信登录');
		$(this).attr('title', '用户名登录');
		$(this).removeClass('wx').addClass('pc');
		<?php if ($this->_vars['QISHI']['weixin_apiopen'] == '1' && $this->_vars['QISHI']['weixin_scan_login'] == '1'): ?>
		wxrun = window.setInterval(run, 5000);
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
	}, function(){
		$('#pcLogin').show();
		$('#codeLogin').hide();
		$('#login-box h1').html('会员登录');
		$(this).attr('title', '微信登录');
		$(this).removeClass('pc').addClass('wx');
		<?php if ($this->_vars['QISHI']['weixin_apiopen'] == '1' && $this->_vars['QISHI']['weixin_scan_login'] == '1'): ?>
		window.clearInterval(wxrun);
		<?php endif; ?>
	})
	//表单验证
	$("form[id=pcLogin]").submit(function(e) {
	e.preventDefault();
		if ($("#username").val()=="" || $("#username").val()=="用户名/邮箱/手机号")
		{			
			$(".login_err").html("请填写：用户名 / 邮箱 / 手机号");	
			$(".login_err").show();
		}
		else if($("#password").val()=="")
		{	
		$(".login_err").html("请填写密码！");
		$(".login_err").show();
		}
		<?php if ($this->_vars['verify_userlogin'] == "1"): ?>
		else if($("#postcaptcha").val()=="")
		{	

			$(".login_err").html("请填写验证码！");
			$(".login_err").show();
		}
		<?php endif; ?>
		else
		{
			$("#login").hide();
			$("#waiting").show();
			 if($("#expire").attr("checked")==true)
			 {
			 var expire=$("#expire").val();
			 }
			 else
			 {
			 var expire="";
			 }
			$.post("<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/ajax_user.php", {"username": $("#username").val(),"password": $("#password").val(),"expire":expire,"url":"<?php echo $_GET['url']; ?>
","postcaptcha":$("#postcaptcha").val(),"time": new Date().getTime(),"act":"do_login"},
		 	function (data,textStatus)
		 	 {
				if (data=="err" || data=="errcaptcha" || data=='status_err')
				{			
					$("#login").show();
					$("#waiting").hide();
					$("#password").attr("value","");
					$(".login_err").show();	
					if (data=="err")
					{
					errinfo="帐号或者密码错误";
					}
					else if(data=="errcaptcha")
					{
					$("#imgdiv img").attr("src",$("#imgdiv img").attr("src")+"1");
					errinfo="验证码错误";
					}
					else if(data=="status_err")
					{
					errinfo="账号已经被暂停，请联系管理员！";
					}
					$(".login_err").html(errinfo);
				}
				else
				{
					$("body").append(data);
				}
		 	 })		
		}
		return false;
	});
});
</script> 
</head>
<body>
	<!-- 头部 -->
	<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("user/reg_header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
	<div class="login-banner-wrap">
		<div class="banner-list">
			<div style="background-image:url(<?php echo $this->_vars['QISHI']['site_template']; ?>
images/login-banner2.jpg);background-color:#22d5d5;"></div>
			<div style="background-image:url(<?php echo $this->_vars['QISHI']['site_template']; ?>
images/login-banner3.jpg);background-color:#fef166;"></div>
			<div style="background-image:url(<?php echo $this->_vars['QISHI']['site_template']; ?>
images/login-banner1.jpg);background-color:#e14644;"></div>
		</div>
	</div>
	<div id="login-box">
		<?php if ($this->_vars['QISHI']['weixin_apiopen'] == '1' && $this->_vars['QISHI']['weixin_scan_login'] == '1'): ?>
		<div class="weixin-login-box"><a href="javascript:void(0);" class="login-type wx" title="微信登录"></a><!-- pc登录class="pc" --></div> 
		<?php endif; ?>
		<h1>会员登录</h1>
		<form action="" id="pcLogin">
			<div class="login-ver-box tips">公共场所不建议自动登录，以防账号丢失</div>
			<div class="login-ver-box error login_err" style="display: none;"></div>
			<div class="login-input-item clearfix">
				<i class="login-icon l-icon-user f-left"></i>
				<div class="f-left"><input type="text" name="username" id="username" placeholder="用户名/手机/邮箱" class="login-input" /></div>
				<div class="input-clear"></div>
			</div>
			<div class="login-input-item clearfix">
				<i class="login-icon l-icon-password f-left"></i>
				<div class="f-left"><input type="password" name="password" id="password" placeholder="密码" class="login-input" /></div>
			</div>
			<?php if ($this->_vars['verify_userlogin'] == "1"): ?>
			<div class="login-input-item lver clearfix">
				<div class="f-left"><input type="text" class="text span190 text-lg" name="postcaptcha" id="postcaptcha" placeholder="输入右侧验证码" /></div>
				<div class="login-ver f-left"><img src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
include/imagecaptcha.php?t=<?php echo $this->_vars['random']; ?>
" id="getcode" align="absmiddle"  style="cursor:pointer;width: 100px;height: 38px;" title="看不请验证码？点击更换一张"  border="0" /></div>
			</div>
			<?php endif; ?>
			<div class="auto-login clearfix">
				<label class="f-left clearfix"><input type="checkbox" name="autologin" id="expire" name="expire" value="7" class="f-left" style="margin-top: 1px;*margin-top: -3px;margin-right: 5px;"/><span class="f-left">7日内自动登录</span></label>
				<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
user/user_getpass.php" class="f-right">忘记密码？</a>
			</div>
			<div class="login-btn-box">
				<input type="submit" name="submitlogin" id="login" value="登&nbsp;&nbsp;&nbsp;录" class="btn login-submit" />
				<input type="text"  id="waiting" value="正在登录..." class="btn login-submit"  style="display: none;" disabled/>
			</div>
			<?php if ($this->_vars['QISHI']['qq_apiopen'] == "1" || $this->_vars['QISHI']['sina_apiopen'] == "1" || $this->_vars['QISHI']['taobao_apiopen'] == "1"): ?>
			<div class="cooperation-account">
				<p>使用合作账号登录</p>
				<div class="coop-account">
					<?php if ($this->_vars['QISHI']['qq_apiopen'] == "1"): ?>
					<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
user/<?php if ($this->_vars['QISHI']['qq_logintype'] == '1'): ?>connect_qq_client.php<?php else: ?>connect_qq_server.php<?php endif; ?>"class="qq">QQ</a>
					<?php endif; ?>
					<?php if ($this->_vars['QISHI']['sina_apiopen'] == "1"): ?>
					&nbsp;|&nbsp;<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
user/connect_sina.php" class="weibo">新浪微博</a>
					<?php endif; ?>
					<?php if ($this->_vars['QISHI']['taobao_apiopen'] == "1"): ?>
					&nbsp;|&nbsp;<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
user/connect_taobao.php" class="taobao">淘宝</a>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		</form>
		<?php if ($this->_vars['QISHI']['weixin_apiopen'] == '1' && $this->_vars['QISHI']['weixin_scan_login'] == '1'): ?>
		<div id="codeLogin">
			<div class="code-login" id="login_container"><?php echo $this->_vars['qrcode_img']; ?>
</div>
			<p>打开微信扫描二维码</p>
		</div>
		<?php endif; ?>
	</div>
	<div id="footer">
	<p>联系地址：<?php echo $this->_vars['QISHI']['address']; ?>
 联系电话：<?php echo $this->_vars['QISHI']['bootom_tel']; ?>
 网站备案：<?php echo $this->_vars['QISHI']['icp'];  echo $this->_vars['QISHI']['statistics']; ?>
</p>
	<p><?php echo $this->_vars['QISHI']['site_name']; ?>
   Powered by   <a href="http://www.zhaohulu.com/" target="_blank">招葫芦网 <?php echo $this->_vars['QISHI']['version']; ?>
</a>   版权所有 | 仿冒必究</p>
	<p><?php echo $this->_vars['QISHI']['bottom_other']; ?>
</p>
</div>
</body>
</html>