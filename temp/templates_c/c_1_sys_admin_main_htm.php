<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:33 CST */  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("sys/admin_header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<div class="admin_main_nr_dbox">
<div class="pagetit">
	<div class="ptit">��ӭ��¼ <?php echo $this->_vars['QISHI']['site_name']; ?>
 �������ģ�</div>
  <div class="clear"></div>
</div>
<span id="version"></span>
<?php if ($this->_vars['admin_register_globals']): ?>
<table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#990000"  style=" margin-bottom:6px; color:#FFFFFF">
  <tr>
    <td bgcolor="#CC0000">&nbsp;<?php echo $this->_vars['admin_register_globals']; ?>
</td>
  </tr>
</table>
<?php endif; ?>
<?php if ($this->_vars['install_warning']): ?>
<table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#FF9900"  style=" margin-bottom:6px;">
  <tr>
    <td bgcolor="#FFFFCC">&nbsp;<?php echo $this->_vars['install_warning']; ?>
</td>
  </tr>
</table>
<?php endif; ?>
<?php if ($this->_vars['update_warning']): ?>
<table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#FF9900"  style=" margin-bottom:6px;">
  <tr>
    <td bgcolor="#FFFFCC">&nbsp;<?php echo $this->_vars['update_warning']; ?>
</td>
  </tr>
</table>
<?php endif; ?>
<?php if ($this->_vars['admindir_warning']): ?>
<table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#FF9900"  style=" margin-bottom:6px;">
  <tr>
    <td bgcolor="#FFFFCC">&nbsp;<?php echo $this->_vars['admindir_warning']; ?>
</td>
  </tr>
</table>
<?php endif; ?>
<?php if ($this->_vars['no_subsite_warning']): ?>
<table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#990000"  style=" margin-bottom:6px; color:#FFFFFF">
  <tr>
    <td bgcolor="#CC0000">&nbsp;<?php echo $this->_vars['no_subsite_warning']; ?>
</td>
  </tr>
</table>
<?php endif; ?>
<div class="toptit">����������</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="link_lan" style="padding-left:15px; line-height:220%; margin-bottom:10px; color:#666666">
      <tr>
        <td  >�����ְλ��&nbsp;<a href="admin_company.php?jobtype=2&audit=2" id="t1">...</a></td>
        <td  >����֤��ҵ��&nbsp;<a href="admin_company.php?act=company_list&audit=2" id="t3">...</a></td>
        <td  >&nbsp;</td>
      </tr>
      <tr>
        <td  >����˼�����&nbsp;<a href="admin_personal.php?audit=2&tabletype=2" id="t2">...</a></td>
        <td  >����ͨ�߼��˲ţ�&nbsp;<a href="admin_personal.php?talent=3" id="t4">...</a></td>
        <td  >�������Ƭ ��&nbsp;<a href="admin_personal.php?act=list&photo_audit=2" id="t6">...</a></td>
      </tr>
      <tr>
        <td  >����˿γ̣�&nbsp;<a href="admin_train.php?act=list&audit=2" id="t11">...</a></td>
        <td  >����˸߼�ְλ��&nbsp;<a href="admin_hunter.php?audit=2" id="t12">...</a></td>
        <td  ></td>
      </tr>
      <tr>
        <td>��������ҵ������&nbsp;<a href="admin_company.php?act=order_list&is_paid=1" id="t5">...</a>		  </td>
        <td>��������ʶ�����&nbsp;<a href="admin_hunter.php?act=order_list&is_paid=1" id="t9">...</a>		  </td>
        <td>���������������&nbsp;<a href="admin_train.php?act=order_list&is_paid=1" id="t10">...</a>		  </td>
      </tr>
      <tr>
        <td  >�ٱ���Ϣ��&nbsp;<a href="admin_feedback.php?act=report_list" id="t7">...</a>		  </td>
        <td  >���/���飺&nbsp; <a href="admin_feedback.php?act=suggest_list&replyinfo=1" id="t8">...</a> </td>
      </tr>
  </table>


<div class="toptit">���30���Աע������</div>

<script language="JavaScript" src="js/FusionCharts.js"></script>
<div id="chartdiv"  > 
        FusionCharts. 
		</div>
<script type="text/javascript">
		   var chart = new FusionCharts("statement/area2D.swf", "ChartId", "800", "150");
		   chart.setDataURL("statement/userreg_30_days.xml");		   
		   chart.render("chartdiv");
		</script>
<script type="text/javascript">
		var tsTimeStamp= new Date().getTime();
		$.get("admin_ajax.php", {"act":"total"},
			function (data,textStatus)
			 {		
				 var str=data.split(",");
				 var i=1;
				 for (x in str)
					{
					$("#t"+i).html(str[x]);
					i++;
					}
			 }
		);
</script>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("sys/admin_footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>