<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:33 CST */ ?>
<div class="topnav">
<a href="?act=" <?php if ($this->_vars['navlabel'] == 'set'): ?>class="select"<?php endif; ?>><u>��վ����</u></a>
<a href="?act=agreement" <?php if ($this->_vars['navlabel'] == 'agreement'): ?>class="select"<?php endif; ?>><u>ע������</u></a>
<a href="?act=map" <?php if ($this->_vars['navlabel'] == 'map'): ?>class="select"<?php endif; ?>><u>���ӵ�ͼ</u></a>
<div class="clear"></div>
</div>
<script type="text/javascript">
$(document).ready(function()
{
	//ָ����������
	var password_tpye=$("#reg_weixin_password_tpye  :radio[checked]").val();
	password_tpye=="3"?$("#config_reg_password").show():$("#config_reg_password").hide();
	$("#reg_weixin_password_tpye :radio").click(function(){
	var password_tpye=$("#reg_weixin_password_tpye  :radio[checked]").val();
	password_tpye=="3"?$("#config_reg_password").show():$("#config_reg_password").hide();
	});	 
});
</script>