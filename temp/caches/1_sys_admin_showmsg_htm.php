a:2:{s:6:"config";a:0:{}s:8:"template";a:3:{i:0;s:21:"sys/admin_showmsg.htm";i:1;s:20:"sys/admin_header.htm";i:2;s:20:"sys/admin_footer.htm";}}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<meta http-equiv="X-UA-Compatible" content="IE=7">
<link rel="shortcut icon" href="/favicon.ico" />
<meta name="author" content="�Һ�«��" />
<meta name="copyright" content="zhaohulu.com" />
<title> Powered by ZHAOHULU</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.QSdialog.js"></script>
<script type="text/javascript" src="js/jquery.vtip-min.js"></script>
<script type="text/javascript" src="js/jquery.grid.rowSizing.pack.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/common.js"></script>
</head>
<body style="background-color:#E0F0FE"><div class="admin_main_nr_dbox">
<div class="pagetit">
	<div class="ptit">�Һ�«����������</div>
  <div class="clear"></div>
</div>
<div class="toptit">ϵͳ��ʾ</div>
<div class="showmsg">
	  <div class="left m2">
	  </div>
	   <div class="right">
	   <h2>���³ɹ���</h2>
	   <div id="redirectionMsg">�����������ѡ�񣬽��� <span id="spanSeconds">3</span> �����ת����һ�����ӵ�ַ��</div>
	   <ul style="margin:0;list-style:none" >
				<li><a href="javascript:history.go(-1)" >������һҳ</a></li>
				</ul>
	   </div>
	   <div class="clear"></div>
</div>
</div>
<script language="JavaScript">
<!--
var seconds = 3;
var defaultUrl = "javascript:history.go(-1)";


onload = function()
{
  if (defaultUrl == 'javascript:history.go(-1)' && window.history.length == 0)
  {
    document.getElementById('redirectionMsg').innerHTML = '';
    return;
  }

  window.setInterval(redirection, 1000);
}
function redirection()
{
  if (seconds <= 0)
  {
    window.clearInterval(redirection);
    return;
  }

  seconds --;
  document.getElementById('spanSeconds').innerHTML = seconds;

  if (seconds == 0)
  {
    window.clearInterval(redirection);
    location.href = defaultUrl;
  }
}
//-->
</script>

<div class="footer link_lan">
Powered by <a href="http://www.zhaohulu.com" target="_blank"><span style="color:#009900">zhaohulu.com</span></a> 3.7.city20160303beta
</div>
<div class="admin_frameset" >
  <div class="open_frame" title="ȫ��" id="open_frame"></div>
  <div class="close_frame" title="��ԭ����" id="close_frame"></div>
</div>
</body>
</html></body>
</html>