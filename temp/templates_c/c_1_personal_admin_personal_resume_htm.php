<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.default.php'); $this->register_modifier("default", "tpl_modifier_default",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.date_format.php'); $this->register_modifier("date_format", "tpl_modifier_date_format",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_parse_url.php'); $this->register_modifier("qishi_parse_url", "tpl_modifier_qishi_parse_url",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:47 CST */  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("sys/admin_header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<script type="text/javascript" src="js/jquery.userinfotip-min.js"></script>
<script type="text/javascript" src="js/jquery.entrustinfotip-min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
	$("#ButAudit").QSdialog({
	DialogTitle:"��ѡ��",
	DialogContent:"#AuditSet",
	DialogContentType:"id",
	DialogAddObj:"#OpAudit",	
	DialogAddType:"html"	
	});
	$("#ButTalent").QSdialog({
	DialogTitle:"��ѡ��",
	DialogContent:"#TalentSet",
	DialogContentType:"id",
	DialogAddObj:"#OpTalent",	
	DialogAddType:"html"	
	});	
	$("#ButPhotoresume").QSdialog({
	DialogTitle:"��ѡ��",
	DialogContent:"#PhotoresumeSet",
	DialogContentType:"id",
	DialogAddObj:"#OpPhotoresume",	
	DialogAddType:"html"	
	});
	$("#ButImportresume").QSdialog({
	DialogTitle:"��ѡ��",
	DialogContent:"#ImportSet",
	DialogContentType:"id",
	DialogAddObj:"#OpImportresume",	
	DialogAddType:"html"	
	});
	//�������ɾ��	
	$("#ButDel").click(function(){
		if (confirm('��ȷ��Ҫɾ����'))
		{
			$("form[name=form1]").attr("action",$("form[name=form1]").attr("action")+"&delete=true");
			$("form[name=form1]").submit()
		}
	});
	$("#Butrefresh").click(function(){
			$("form[name=form1]").attr("action",$("form[name=form1]").attr("action")+"&refresh=ok");
			$("form[name=form1]").submit()
	});
	$("#ButExport").click(function(){
			$("form[name=form1]").attr("action",$("form[name=form1]").attr("action")+"&export=true");
			$("form[name=form1]").submit()
	});
	//�������
	$("#set_import").click(function(){
			$("form[name=form1]").attr("action","?act=import");
			$("form[name=form1]").submit()
	});
	//�����б�����
	$(".listod .txt").each(function(i){
	var li=$(this).children(".select");
	var htm="<a href=\""+li.attr("href")+"\" class=\""+li.attr("class")+"\">"+li.text()+"</a>";
	li.detach();
	$(this).prepend(htm);
	});
			
	$("#fail").click(function(){
		$("#reason").show();
	});
	$("#success").click(function(){
		$("#reason").hide();
	});

	$("#not_audit").click(function(){
		$("#is_del_img").show();
	});
	$("#yes_audit").click(function(){
		$("#is_del_img").hide();
	});
	
	
		
});
</script>
<div class="admin_main_nr_dbox">
 <div class="pagetit">
	<div class="ptit"> <?php echo $this->_vars['pageheader']; ?>
</div>
  <div class="clear"></div>
</div>

<div class="toptip">
	<h2>��ʾ��</h2>
	<p>
�ɼ�������ָ�����ͨ��,ȫ�����ļ�������֮Ϊ�ǿɼ�����<br />
</p>
</div>


<div class="seltpye_y">

	<div class="tit link_lan">
	<strong>�����б�</strong><span>(���ҵ�<?php echo $this->_vars['total_val']; ?>
��)</span>
	<a href="?act=list">[�ָ�Ĭ��]</a>
	<div class="pli link_bk"><u>ÿҳ��ʾ��</u>
	<a href="<?php echo $this->_run_modifier("perpage:10", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['perpage'] == "10"): ?>class="select"<?php endif; ?>>10</a>
	<a href="<?php echo $this->_run_modifier("perpage:20", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['perpage'] == "20"): ?>class="select"<?php endif; ?>>20</a>
	<a href="<?php echo $this->_run_modifier("perpage:30", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['perpage'] == "30"): ?>class="select"<?php endif; ?>>30</a>
	<a href="<?php echo $this->_run_modifier("perpage:60", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['perpage'] == "60"): ?>class="select"<?php endif; ?>>60</a>
	<div class="clear"></div>
	</div>
	</div>	
    <div class="list">
	  <div class="t">�ɼ�״̬</div>	  
	  <div class="txt link_lan">
	 	<a href="<?php echo $this->_run_modifier("tabletype:1", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['tabletype'] == "1"): ?>class="select"<?php endif; ?>>�ɼ�����<span>(<?php echo $this->_vars['total']['0']; ?>
)</span></a>
		<a href="<?php echo $this->_run_modifier("tabletype:2", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['tabletype'] == "2"): ?>class="select"<?php endif; ?>>�ǿɼ�����<span>(<?php echo $this->_vars['total']['1']; ?>
)</span></a>
	  </div>
    </div>
	<?php if ($_GET['tabletype'] == "2"): ?>
	<div class="list">
	  <div class="t">���״̬</div>	  
	  <div class="txt link_lan">
	 	<a href="<?php echo $this->_run_modifier("audit:", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['audit'] == ""): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("audit:1", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['audit'] == "1"): ?>class="select"<?php endif; ?>>���ͨ��<span>(<?php echo $this->_vars['total']['2']; ?>
)</span></a>
		<a href="<?php echo $this->_run_modifier("audit:2", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['audit'] == "2"): ?>class="select"<?php endif; ?>>�ȴ����<span>(<?php echo $this->_vars['total']['3']; ?>
)</span></a>
		<a href="<?php echo $this->_run_modifier("audit:3", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['audit'] == "3"): ?>class="select"<?php endif; ?>>���δͨ��<span>(<?php echo $this->_vars['total']['4']; ?>
)</span></a>
	  </div>
    </div>
	  <?php endif; ?>
	
	

	<div class="list" >
	  <div class="t">�����ȼ�</div>	  
	  <div class="txt link_lan">
	 	<a href="<?php echo $this->_run_modifier("talent:", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['talent'] == ""): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("talent:1", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['talent'] == "1"): ?>class="select"<?php endif; ?>>��ͨ�˲�</a>
		<a href="<?php echo $this->_run_modifier("talent:2", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['talent'] == "2"): ?>class="select"<?php endif; ?>>�߼��˲�</a>
		<a href="<?php echo $this->_run_modifier("talent:3", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['talent'] == "3"): ?>class="select"<?php endif; ?>>����ͨ�߼��˲�</a>
	  </div>
    </div>
	
	
	<div class="list" >
	  <div class="t">��Ƭ�ɼ�</div>	  
	  <div class="txt link_lan">
	 	<a href="<?php echo $this->_run_modifier("photo_display:", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['photo_display'] == ""): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("photo_display:1", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['photo_display'] == "1"): ?>class="select"<?php endif; ?>>�ɼ�</a>
		<a href="<?php echo $this->_run_modifier("photo_display:2", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['photo_display'] == "2"): ?>class="select"<?php endif; ?>>���ɼ�</a>
	  </div>
    </div>
	
	
	
	<div class="list" >
	  <div class="t">��Ƭ���</div>	  
	  <div class="txt link_lan">
	 	<a href="<?php echo $this->_run_modifier("photo_audit:", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['photo_audit'] == ""): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("photo_audit:1", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['photo_audit'] == "1"): ?>class="select"<?php endif; ?>>��Ƭ���ͨ��</a>
		<a href="<?php echo $this->_run_modifier("photo_audit:2", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['photo_audit'] == "2"): ?>class="select"<?php endif; ?>>��Ƭ�����</a>
		<a href="<?php echo $this->_run_modifier("photo_audit:3", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['photo_audit'] == "3"): ?>class="select"<?php endif; ?>>��Ƭ���δͨ��</a>
	  </div>
    </div>
	
	 
	
	<div class="list" >
	  <div class="t">���ʱ��</div>	  
	  <div class="txt link_lan">
	 	<a href="<?php echo $this->_run_modifier("addtimesettr:", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['addtimesettr'] == ""): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("addtimesettr:3", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['addtimesettr'] == "3"): ?>class="select"<?php endif; ?>>������</a>
		<a href="<?php echo $this->_run_modifier("addtimesettr:7", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['addtimesettr'] == "7"): ?>class="select"<?php endif; ?>>һ����</a>
		<a href="<?php echo $this->_run_modifier("addtimesettr:30", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['addtimesettr'] == "30"): ?>class="select"<?php endif; ?>>һ����</a>

	  </div>
    </div>
	
	<div class="list" >
	  <div class="t">ˢ��ʱ��</div>	  
	  <div class="txt link_lan">
	 	<a href="<?php echo $this->_run_modifier("settr:", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['settr'] == ""): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("settr:3", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['settr'] == "3"): ?>class="select"<?php endif; ?>>������</a>
		<a href="<?php echo $this->_run_modifier("settr:7", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['settr'] == "7"): ?>class="select"<?php endif; ?>>һ����</a>
		<a href="<?php echo $this->_run_modifier("settr:30", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['settr'] == "30"): ?>class="select"<?php endif; ?>>һ����</a>

		
	  </div>
    </div>


	<div class="list" >
	  <div class="t">�Ƿ�ί��</div>  
	  <div class="txt link_lan">
	 	<a href="<?php echo $this->_run_modifier("entrust:", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['entrust'] == ""): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("entrust:1", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['entrust'] == "1"): ?>class="select"<?php endif; ?>>һ��</a>
		<a href="<?php echo $this->_run_modifier("entrust:2", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['entrust'] == "2"): ?>class="select"<?php endif; ?>>����</a>
		<a href="<?php echo $this->_run_modifier("entrust:3", 'qishi_parse_url', 'plugin', 1); ?>
" <?php if ($_GET['entrust'] == "3"): ?>class="select"<?php endif; ?>>һ����</a>
	  </div>
    </div>
	
	
	<div class="clear"></div>
</div>
 
  <form id="form1" name="form1" enctype="multipart/form-data" method="post" action="?act=perform">
  <?php echo $this->_vars['inputtoken']; ?>

  <table width="100%" border="0" cellpadding="0" cellspacing="0"   class="link_lan">
    <tr>
      <td    class="admin_list_tit admin_list_first" >
     <label id="chkAll"><input type="checkbox" name=" " title="ȫѡ/��ѡ" id="chk"/>����</label>	 </td>
     
	   <td   align="center"  width="80" class="admin_list_tit">����ָ�� </td>
	  <td  align="center"  width="6%" class="admin_list_tit">�ȼ�</td>
	   <td align="center"  width="10%"  class="admin_list_tit">���״̬</td> 
      <td   align="center" width="8%" class="admin_list_tit">����</td>
	  <td align="center" width="8%" class="admin_list_tit">��Ƭ</td>
      <td align="center" width="12%"  class="admin_list_tit">���ʱ��</td>
	  <td align="center"  width="12%"  class="admin_list_tit">ˢ��ʱ��</td>	
	   <td align="center"  width="6%"  class="admin_list_tit">ί��״̬</td>	
      <td align="center"  width="15%" class="admin_list_tit">����</td>
    </tr>
	<?php if (count((array)$this->_vars['resumelist'])): foreach ((array)$this->_vars['resumelist'] as $this->_vars['list']): ?>
	<tr>
      <td  class="admin_list admin_list_first" >
	  <input name="id[]" type="checkbox" id="id" value="<?php echo $this->_vars['list']['id']; ?>
"  />
		<a href="<?php echo $this->_vars['list']['resume_url']; ?>
" target="_blank"><?php echo $this->_vars['list']['fullname']; ?>
</a>
		<?php if ($this->_vars['list']['talent'] == "3"): ?>
		<span style="color: #FF0000">[����ͨ�߼��˲�]</span>
		<?php endif; ?>
		<?php if ($this->_vars['list']['photo_img'] <> ""): ?>
		<span style="color: #009900"  class="vtip" title="<img src=<?php echo $this->_vars['QISHI']['resume_photo_dir_thumb'];  echo $this->_vars['list']['photo_img']; ?>
  border=0  align=absmiddle>">[��Ƭ]</span>
		<?php endif; ?>	
		<a href="admin_mail.php?act=send&email=<?php echo $this->_vars['list']['email']; ?>
&uid=<?php echo $this->_vars['list']['uid']; ?>
"> <img src="images/email.gif" border="0" title="�����ʼ�" /></a>	
		 <?php if ($this->_vars['list']['telephone']): ?><a href="admin_sms.php?act=send&mobile=<?php echo $this->_vars['list']['telephone']; ?>
&uid=<?php echo $this->_vars['list']['uid']; ?>
"><img src="images/sms.gif" border="0" title="���Ͷ���" /></a><?php endif; ?>	
		  </td>
	 <td align="center"  class="admin_list">
	 <div style="width:100%; border:1px #CCCCCC solid; padding:1px; text-align:left; position:relative; font-size:0px">
	 	<div style=" background-color: #99CC00; height:10px; color:#990000;width:<?php echo $this->_vars['list']['complete_percent']; ?>
%;font-size:0px"></div>
		<div style="position:absolute; top:0px; left:40%; font-size:10px;"><?php echo $this->_vars['list']['complete_percent']; ?>
%</div>
	 </div>	</td>
	
      <td align="center"  class="admin_list">
	  <?php if ($this->_vars['list']['talent'] == "1"): ?>��ͨ<?php endif; ?>
	  <?php if ($this->_vars['list']['talent'] == "2"): ?><span style="color: #009900">�߼�</span><?php endif; ?>
	  <?php if ($this->_vars['list']['talent'] == "3"): ?><span style="color: #FF0000">����ͨ</span><?php endif; ?>	  </td>
	   <td align="center"  class="admin_list">
	   <?php if ($this->_vars['list']['audit'] == "1"): ?>���ͨ��<?php endif; ?>
	   <?php if ($this->_vars['list']['audit'] == "2"): ?><span style="color: #FF6600">�ȴ����</span><?php endif; ?>
	   <?php if ($this->_vars['list']['audit'] == "3"): ?><span style="color: #666666">���δͨ��</span><?php endif; ?>	   </td>   
      <td align="center"  class="admin_list">
	  <?php if ($this->_vars['list']['display'] == "1"): ?>
	  		����
	  <?php else: ?>
	  		�빫��
	  <?php endif; ?>	  </td>
      <td align="center"  class="admin_list">
	   <?php if ($this->_vars['list']['photo'] == ""): ?>
	  ����Ƭ
	  <?php else: ?>
	  	 <?php if ($this->_vars['list']['photo_audit'] == "1"): ?>
		 ���ͨ��<?php if ($this->_vars['list']['photo_display'] <> "1"): ?>[���ɼ�]<?php endif; ?>
		 <?php endif; ?>
		 <?php if ($this->_vars['list']['photo_audit'] == "2"): ?><span style="color:#FF0000">�ȴ����</span><?php endif; ?>
		 <?php if ($this->_vars['list']['photo_audit'] == "3"): ?>
		 ���δͨ�� 
		 <?php endif; ?>
	  <?php endif; ?>	  </td>
      <td align="center"  class="admin_list"><?php echo $this->_run_modifier($this->_vars['list']['addtime'], 'date_format', 'plugin', 1, "%Y-%m-%d"); ?>
</td>
	  <td align="center"  class="admin_list"><?php echo $this->_run_modifier($this->_vars['list']['refreshtime'], 'date_format', 'plugin', 1, "%Y-%m-%d"); ?>
</td>
	    <?php if ($this->_vars['list']['entrust'] == 0): ?>
	  <td align="center"  class="admin_list">- -</td>
	  <?php elseif ($this->_vars['list']['entrust'] == 1): ?>
	  <td align="center"  class="admin_list"><span class="entrustinfo" uid="<?php echo $this->_vars['list']['uid']; ?>
" rid="<?php echo $this->_vars['list']['id']; ?>
">һ��</span></td>
	  <?php elseif ($this->_vars['list']['entrust'] == 2): ?>
	<td align="center"  class="admin_list"><span class="entrustinfo" uid="<?php echo $this->_vars['list']['uid']; ?>
" rid="<?php echo $this->_vars['list']['id']; ?>
">����</span></td>
	<?php else: ?>
	<td align="center"  class="admin_list"><span class="entrustinfo" uid="<?php echo $this->_vars['list']['uid']; ?>
" rid="<?php echo $this->_vars['list']['id']; ?>
">һ����</span></td>
	<?php endif; ?>
      <td align="center"  class="admin_list">
		<a href="admin_memberslog.php?uid=<?php echo $this->_vars['list']['uid']; ?>
">��־</a>
		&nbsp;
		<a href="?act=resume_show&id=<?php echo $this->_vars['list']['id']; ?>
&uid=<?php echo $this->_vars['list']['uid']; ?>
" >�鿴</a>
		&nbsp;
		<a href="?act=management&id=<?php echo $this->_vars['list']['uid']; ?>
"  target="_blank" class="userinfo"  id="<?php echo $this->_vars['list']['uid']; ?>
">����</a>	 
	&nbsp;
		<?php if ($this->_vars['list']['entrust'] != 0): ?>
		<a href="?act=matching&id=<?php echo $this->_vars['list']['id']; ?>
&uid=<?php echo $this->_vars['list']['uid']; ?>
">ƥ��</a>  
		<?php else: ?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<?php endif; ?>
	  </td>
    </tr>
	 <?php endforeach; endif; ?>
  </table>
  <span id="OpAudit"></span>
  <span id="OpTalent"></span>
  <span id="OpPhotoresume"></span>
  <span id="OpImportresume"></span>
 </form>
<?php if (! $this->_vars['resumelist']): ?>
<div class="admin_list_no_info">û���κ���Ϣ��</div>
<?php endif; ?>
<table width="100%" border="0" cellspacing="10" cellpadding="0" class="admin_list_btm">
      <tr>
        <td>
<input type="button" class="admin_submit" id="ButAudit" value="��˼���" />
<input type="button" class="admin_submit" id="ButTalent" value="�˲ŵȼ�" />  
<input type="button" class="admin_submit" id="ButPhotoresume" value="�����Ƭ" />
<input type="button" class="admin_submit" id="Butrefresh" value="ˢ��"/>
<input type="button" class="admin_submit" id="ButDel" value="ɾ��"/>
<input type="button" name="ButExport"  id="ButExport" value="����" class="admin_submit"/>
<input type="button" name="ButImportresume"  id="ButImportresume" value="����" class="admin_submit"/>
<input type="button" name="excelmodel"  id="excelmodel" value="���ؼ���ģ��" class="admin_submit" onclick="javascript:location.href='./excelmodel.xls'"/>
		</td>
        <td width="305" align="right">
		<form id="formseh" name="formseh" method="get" action="?">	
			<div class="seh">
			    <div class="keybox"><input name="key" type="text"   value="<?php echo $_GET['key']; ?>
" /></div>
			    <div class="selbox">
					<input name="key_type_cn"  id="key_type_cn" type="text" value="<?php echo $this->_run_modifier($_GET['key_type_cn'], 'default', 'plugin', 1, "����"); ?>
" readonly="true"/>
						<div>
								<input name="key_type" id="key_type" type="hidden" value="<?php echo $this->_run_modifier($_GET['key_type'], 'default', 'plugin', 1, "1"); ?>
" />
												<div id="sehmenu" class="seh_menu">
														<ul>
														<li id="1" title="����">����</li>
														<li id="2" title="ID">ID</li>
														<li id="3" title="UID">UID</li>
														<li id="4" title="�绰">�绰</li>
														<li id="6" title="��ַ">��ַ</li>
														</ul>
												</div>
						</div>				
				</div>
				<div class="sbtbox">
				<input name="act" type="hidden" value="list" />
				<input type="submit" name="" class="sbt" id="sbt" value="����"/>
				</div>
				<div class="clear"></div>
		  </div>
		  </form>
		  <script type="text/javascript">$(document).ready(function(){showmenu("#key_type_cn","#sehmenu","#key_type");});	</script>
	    </td>
      </tr>
  </table>
<div class="page link_bk"><?php echo $this->_vars['page']; ?>
</div>
</div>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("sys/admin_footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<div id="AuditSet" style="display: none" >
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="6" >
    <tr>
      <td height="30">&nbsp;</td>
      <td height="30"><strong  style="color:#0066CC; font-size:14px;">����ѡ��������Ϊ��</strong></td>
    </tr>
	      <tr>
      <td width="40" height="25">&nbsp;</td>
      <td>
	  <label >
                      <input name="audit" type="radio" value="1" checked="checked" id="success" />
                      ���ͨ�� </label>	  </td>
    </tr>
    <tr>
      <td width="40" height="25">&nbsp;</td>
      <td width="400"><label >
                      <input type="radio" name="audit" value="3"  id="fail"/>
                       ���δͨ��</label></td>
    </tr>
    <tr>
      <td width="50" height="25">&nbsp;</td>
      <td>
                      <label><input type="checkbox" name="pms_notice" value="1"  checked="checked"/>
					  վ����֪ͨ
                       </label></td>
    </tr>
	<tr style="display:none" id="reason">
      <td width="40" height="25" >���ɣ�</td>
      <td>
         <textarea name="reason" id="reason" cols="40" style="font-size:12px"></textarea>            
      </td>
    </tr>
    <tr>
      <td height="25">&nbsp;</td>
      <td><span style="border-top: 1px #A3C7DA solid;">
        <input type="submit" name="set_audit" value="ȷ��" class="admin_submit">
      </span></td>
    </tr>		  
  </table>
  </div>
  <div id="ImportSet" style="display: none" >
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="6" >
    <tr>
    <input type="file" name="file"  style="height:21px; width:210px; border:1px #999999 solid" />
    </tr>
    <tr>
      <td height="25">&nbsp;</td>
      <td><span style="border-top: 1px #A3C7DA solid;">
        <input type="button" name="set_import" id="set_import" value="ȷ��" class="admin_submit">
      </span></td>
    </tr>		  
  </table>
  </div>
<div id="TalentSet" style="display:none" >
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="6" >
    <tr>
      <td height="30">&nbsp;</td>
      <td height="30"><strong  style="color:#0066CC; font-size:14px;">����ѡ��������Ϊ��</strong></td>
    </tr>
	      <tr>
      <td width="27" height="25">&nbsp;</td>
      <td>
	  <label >
                      <input name="talent" type="radio" value="1" checked="checked"  />
                      ��ͨ�˲� </label>	  </td>
    </tr>
    <tr>
      <td width="27" height="25">&nbsp;</td>
      <td width="425"><label ><input type="radio" name="talent" value="2"  />�߼��˲�</label>	  </td>
    </tr>
    <tr>
      <td height="25">&nbsp;</td>
      <td><span style="border-top: 1px #A3C7DA solid;">
        <input type="submit" name="set_talent" value="ȷ��" class="admin_submit">
      </span></td>
    </tr>
  </table>
</div>
<div id="PhotoresumeSet" style="display: none" >
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="6" >
    <tr>
      <td height="30">&nbsp;</td>
      <td height="30"><strong  style="color:#0066CC; font-size:14px;">����ѡ��������Ϊ��</strong></td>
    </tr>
	      <tr>
      <td width="27" height="25">&nbsp;</td>
      <td>
	  <label >
                      <input name="photoaudit" type="radio" value="1" checked="checked" id="yes_audit"/>
                      ��Ƭ���ͨ�� </label>	  </td>
    </tr>
    <tr>
      <td width="27" height="25">&nbsp;</td>
      <td width="425"><label >
                      <input type="radio" name="photoaudit" value="3" id="not_audit"/>
                       ��Ƭ���δͨ��</label></td>
    </tr>
    <tr style="display:none" id="is_del_img">
      <td width="27" height="25">&nbsp;</td>
      <td>
                      <label><input type="checkbox" name="is_del_img" value="1"/>
					  �Ƿ�ɾ����Ӧ��Ƭ
                       </label></td>
    </tr>
    <tr>
      <td height="25">&nbsp;</td>
      <td><span style="border-top: 1px #A3C7DA solid;">
        <input type="submit" name="set_photoaudit" value="ȷ��" class="admin_submit">
      </span></td>
    </tr>		  
  </table>
</div>
</body>
</html>