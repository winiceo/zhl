<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-01-29 13:44 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<link href="templates/css/css.css" rel="stylesheet" type="text/css" />
<meta http-equiv="X-UA-Compatible" content="IE=7">
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<title>��װ�� - �Һ�«��</title>
<script src="templates/js/jquery.js" type="text/javascript" language="javascript"></script>
<script>
	$(function(){
		$(".step li:eq(3)").css("margin-right", 0);
		$(".setting div:last").css("border", 0);
	})
</script>
</head>
<body>
	<div class="install_box">
		<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
		<div class="step">
			<div class="step_show step_1"></div>
			<ul>
				<li class="complete">�������</li>
				<li>��������</li>
				<li>��ʼ��װ</li>
				<li>�ɹ���װ</li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="setting">
			<div class="setting_check">
				<h3>�������</h3>
				<table>
					<tbody>
						<tr class="title" height="30">
							<td width="229">��Ŀ</td>
							<td width="265">��ǰ������</td>
							<td width="102">��ʿcms��������</td>
						</tr>
						<tr height="30">
							<td width="229">����ϵͳ</td>
							<td width="265"><?php echo $this->_vars['system_info']['os']; ?>
</td>
							<td width="102">������</td>
						</tr>
						<tr height="30">
							<td width="229">�����ϴ�</td>
							<td width="265"><?php echo $this->_vars['system_info']['max_filesize']; ?>
</td>
							<td width="102">2M����</td>
						</tr>
						<tr height="30">
							<td width="229">PHP�汾</td>
							<td width="265"><?php echo $this->_vars['system_info']['php_ver']; ?>
</td>
							<td width="102">5.1����</td>
						</tr>
						<tr height="30">
							<td width="229">��������������</td>
							<td width="265"><?php echo $this->_vars['system_info']['web_server']; ?>
</td>
							<td width="102">Apache/IIS/Nginx</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="menu_check">
				<h3>Ŀ¼Ȩ�޼��</h3>
				<table>
					<tbody>
						<tr class="title" height="30">
							<td width="229">Ŀ¼�ļ�</td>
							<td width="265">��ȡȨ��</td>
							<td width="102">д��Ȩ��</td>
						</tr>
						<?php if (isset($this->_sections['dir'])) unset($this->_sections['dir']);
$this->_sections['dir']['name'] = 'dir';
$this->_sections['dir']['loop'] = is_array($this->_vars['dir_check']) ? count($this->_vars['dir_check']) : max(0, (int)$this->_vars['dir_check']);
$this->_sections['dir']['show'] = true;
$this->_sections['dir']['max'] = $this->_sections['dir']['loop'];
$this->_sections['dir']['step'] = 1;
$this->_sections['dir']['start'] = $this->_sections['dir']['step'] > 0 ? 0 : $this->_sections['dir']['loop']-1;
if ($this->_sections['dir']['show']) {
	$this->_sections['dir']['total'] = $this->_sections['dir']['loop'];
	if ($this->_sections['dir']['total'] == 0)
		$this->_sections['dir']['show'] = false;
} else
	$this->_sections['dir']['total'] = 0;
if ($this->_sections['dir']['show']):

		for ($this->_sections['dir']['index'] = $this->_sections['dir']['start'], $this->_sections['dir']['iteration'] = 1;
			 $this->_sections['dir']['iteration'] <= $this->_sections['dir']['total'];
			 $this->_sections['dir']['index'] += $this->_sections['dir']['step'], $this->_sections['dir']['iteration']++):
$this->_sections['dir']['rownum'] = $this->_sections['dir']['iteration'];
$this->_sections['dir']['index_prev'] = $this->_sections['dir']['index'] - $this->_sections['dir']['step'];
$this->_sections['dir']['index_next'] = $this->_sections['dir']['index'] + $this->_sections['dir']['step'];
$this->_sections['dir']['first']	  = ($this->_sections['dir']['iteration'] == 1);
$this->_sections['dir']['last']	   = ($this->_sections['dir']['iteration'] == $this->_sections['dir']['total']);
?>
						<tr height="30">
							<td width="229"><?php echo $this->_vars['dir_check'][$this->_sections['dir']['index']]['dir']; ?>
</td>
							<td width="265"><?php echo $this->_vars['dir_check'][$this->_sections['dir']['index']]['read']; ?>
</td>
							<td width="102"><?php echo $this->_vars['dir_check'][$this->_sections['dir']['index']]['write']; ?>
</td>
						</tr>
						<?php endfor; endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="next">
			<form action="index.php" method="get">
			<input name="act" type="hidden" value="3" />
			<input type="button" value="��һ��" class="up" onclick="window.location.href='index.php?act=1';"/>
			<input type="submit" value="��һ��" class="down" />
			</form>
		</div>
		<div class="copyright">
			Copyright @ 2015 74cms.com All Right Reserved
		</div>
	</div>
</body>
</html>