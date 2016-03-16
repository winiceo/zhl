<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.date_format.php'); $this->register_modifier("date_format", "tpl_modifier_date_format",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:34 CST */ ?>
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
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/user_personal.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/user_common.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/ui-dialog.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min-common.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.cookie.js" type='text/javascript'></script>
<script type="text/javascript">
$(document).ready(function()
{	
	// ��һ��ע������Ա���ģ���ʾ����
	var mode = $.cookie('isFirstReg');
	if (mode == 1) {
		$("#mask").height($(document).height());
		$('#mask, #searchTip, #searchTip div:eq(0)').show();
		var topL0 = $("#searchTip div:eq(0)").offset().top;
		$(document).scrollTop(topL0);
		$('#searchTip div a').click(function(){
			var currentStep=$(this).parent();
			currentStep.hide();
			currentStep.next().show();
			var topLn = currentStep.next().offset().top;
			$("body,html").animate({scrollTop:topLn - 50}, 500);
		});
		$('#searchTip div a.p').unbind().click(function(){
			$('#searchTip div').hide();
			var currentStep=$(this).parent();
			currentStep.hide();
			currentStep.prev().show();
			var topLp = currentStep.prev().offset().top;
			$("body,html").animate({scrollTop:topLp - 50}, 500);
		});
		$('#searchTip div a:last, #searchTip div span').unbind().click(function(){
			$('#mask, #searchTip').hide();
			$("body,html").animate({scrollTop:0}, 500);
			return false;
		});
		$.cookie('isFirstReg',0);
	};
	// ��ʾ��һ�����ѣ������������
	$(".company-index-tip .index-tip").eq(0).show().siblings('.index-tip').hide();
	// �����������Ѵ���
	$(".ctip_close").die().live('click', function(event) {
		$(this).closest('.index-tip').addClass('istip').slideUp(600);
		var leng = $(".company-index-tip .index-tip").not(".istip").length;
		if (leng > 0) {
			setTimeout(function () { 
				$(".company-index-tip .index-tip").not(".istip").eq(0).slideDown(600);
		    }, 600);
		} else if (leng == 0) {
			setTimeout(function () { 
				$(".company-index-tip").slideUp(600);
				$(".bbox1 .lin").hide();
		    }, 600);
		}
	});
//��������������ְλ����Ӧ
$(".mli .imgbox").hover(
	function () {
		$(this).addClass("h");
	},
	function () {
		$(this).removeClass("h");
	}
	);
$(".imgbox").click(function(){
	window.location.href="personal_apply.php?act="+$(this).attr("mark");
});
$.get("?act=ajax_get_interest_jobs&pid="+$(".myresume").first().attr("pid"), function(result){
	$("#interest_jobs_list").html(result);
});

//ί�м���
$("#entrudt_id").click(function(){
	var pid = $(this).attr("pid");
	var url = "personal_ajax.php?act=entrust&pid="+pid;
	var myDialog = dialog();
	myDialog.content("������...");
    myDialog.title('ί�м���');
    myDialog.width('440');
    myDialog.showModal();
    $.get(url, function(data){
        myDialog.content(data);
        /* �ر� */
        $(".DialogClose").live('click',function() {
          myDialog.close().remove();
        });
    });
});
//��˽����
$(".resume_privacy").click(function(){
	var pid = $(this).attr("pid");
	var url = "personal_ajax.php?act=privacy&pid="+pid;
	var myDialog = dialog();
	myDialog.content("������...");
    myDialog.title('��˽����');
    myDialog.width('550');
    myDialog.showModal();
    $.get(url, function(data){
        myDialog.content(data);
        /* �ر� */
        $(".DialogClose").live('click',function() {
          myDialog.close().remove();
        });
    });
});
$(".del_resume").click(function(){
	var pid = $(this).attr("pid");
	var url = "personal_ajax.php?act=del_resume&pid="+pid;
	var myDialog = dialog();
	myDialog.content("������...");
    myDialog.title('ɾ������');
    myDialog.width('350');
    myDialog.showModal();
    $.get(url, function(data){
        myDialog.content(data);
        /* �ر� */
        $(".DialogClose").live('click',function() {
          myDialog.close().remove();
        });
    });
});
$(".refresh_resume").live("click",function(){
	var pid = $(this).attr("pid");
	var url = "personal_ajax.php?act=ajax_refresh_resume&pid="+pid;
	var url_ = "personal_ajax.php?act=ajax_refresh_resume_save&id="+pid;
	var myDialog = dialog();
		myDialog.content("������...");
		myDialog.title('ˢ�¼���');
		myDialog.showModal();
		$.get(url, function(data){
			myDialog.content(data);
			/* �ر� */
			$(".DialogClose").live('click',function() {
			myDialog.close().remove();
			});
			//���ˢ�¼���
			$(".refresh-btn").live('click',function() {
				var current = $("#current").val();
				var current_cn = $("#current_cn").val();
				$.get(url_+"&current="+current+"&current_cn="+current_cn, function(data){
					if(data =='ok')
					{
						myDialog.content("ˢ�³ɹ���");
						window.location.reload();
					}
					else
					{
						myDialog.content(data);
						/* �ر� */
						$(".DialogClose").live('click',function() {
						myDialog.close().remove();
						});
					}
				});
			});
		});
});
	// ����
	$('.resume-choose').on('click', function(e){
		$(this).find('.resume-choose-list').stop().slideToggle('fast');
		$(this).find('.choose-span').toggleClass('beselect');
		$(document).one("click",function(){
			$('.choose-span').removeClass('beselect');
			$('.resume-choose-list').slideUp('fast');
		});
		e.stopPropagation();
	});
	$(".resume-choose-list li").click(function(){
		var id = $(this).attr("id");
		var title = $(this).attr("title");
		$(".choose-span").html(title);
		$(this).find('.resume-choose-list').stop().slideToggle('fast');
		$(".myresume").css("display","none");
		$("#resume_box_"+$(this).attr("id")).css("display","block");
		$.get("?act=ajax_get_interest_jobs&pid="+id, function(result){
			$("#interest_jobs_list").html(result);
		});
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
<!-- ���� -->
<div id="mask"></div>
<div id="searchTip">
	<div class="stepD"><a>��һ��</a><span title="�ر�">�ر�</span></div>
    <div class="stepE"><a class="p">��һ��</a><a>��һ��</a><span title="�ر�">�ر�</span></div>
    <div class="stepF"><a class="p">��һ��</a><a>��һ��</a><span title="�ر�">�ر�</span></div>
</div>
<!-- ���� end-->
<div class="page_location link_bk">��ǰλ�ã�<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
">��ҳ</a> > <a href="<?php echo $this->_vars['userindexurl']; ?>
">��Ա����</a></div>
<div class="usermain">
  <div class="leftmenu link_bk">
  	 <?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("member_personal/left.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
  </div>
  <div class="rightmain">
  	<div class="bbox1">	
  		<?php if ($this->_vars['message']): ?>
  		<div class="company-index-tip">
  			<?php if (count((array)$this->_vars['message'])): foreach ((array)$this->_vars['message'] as $this->_vars['list']): ?>
			<div class="index-tip">
				<?php echo $this->_vars['list']; ?>
<i class="close-icon ctip_close"></i>
			</div>
			<?php endforeach; endif; ?>
		</div>
		<div class="lin"></div>
		<?php endif; ?>
		  <div class="pindex-info">
		  	<div class="index-account-info">
		  		<span class="account-type">
					<span>�ҵ�<?php echo $this->_vars['QISHI']['points_byname']; ?>
��</span ><span class="account-fen"><?php echo $this->_vars['points']; ?>
</span><?php echo $this->_vars['QISHI']['points_quantifier']; ?>

					<a href="personal_service.php?act=order_add" class="underline">���ֳ�ֵ</a>
					<a href="<?php echo $this->_run_modifier("QS_shop_index", 'qishi_url', 'plugin', 1); ?>
" class="underline">�����̳�</a>
				</span>
			</div>
		  		<div class="leftbox">
				  <div class="">
				  	<?php if ($this->_vars['user']['avatars'] == ""): ?>
				  	<img name="" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/06.jpg" width="100" height="100" alt="" />
				  	<?php else: ?>
				  	<img name="" src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
data/avatar/100/<?php echo $this->_vars['user']['avatars']; ?>
?rand=<?php echo $this->_vars['rand']; ?>
" width="100" height="100" alt="" />
				  	<?php endif; ?>
				  </div>
				  <a href="personal_user.php?act=avatars"  class="edit">�޸�ͷ��</a>
		  		</div>
				<div class="cbox">
				  	<div class="name  h1-title"><?php echo $this->_vars['user']['username']; ?>
<span>(uid:<?php echo $this->_vars['user']['uid']; ?>
)</span><a href="personal_user.php?act=userprofile" class="personal-edit">�༭</a></div>
					<div class="txt">
					<?php if ($this->_vars['sms']['open'] == "1"): ?>
						<?php if ($this->_vars['user']['mobile_audit'] == "1"): ?>
						<span class="m">�ֻ���֤��</span>&nbsp;<span style="color:#009900">����֤</span>&nbsp;<?php else: ?><span class="m n">�ֻ���֤��</span>&nbsp;<a href="personal_user.php?act=authenticate"><span style="color:#FF0000">δ��֤</span></a>&nbsp;
						<?php endif;  echo $this->_vars['user']['mobile']; ?>
<br />
					<?php endif; ?>
					<?php if ($this->_vars['user']['email_audit'] == "1"): ?>
					<span class="e">������֤��</span>&nbsp;<span style="color:#009900">����֤</span>&nbsp;<?php else: ?><span class="e n">������֤��</span>&nbsp;<a href="personal_user.php?act=authenticate"><span style="color:#FF0000">δ��֤</span></a>&nbsp;<?php endif;  echo $this->_vars['user']['email']; ?>
<br />
					
					<?php if ($this->_vars['user']['weixin_openid']): ?>
					<span class="w">΢�Ű��</span>&nbsp;<span style="color:#009900">�Ѱ�</span>&nbsp;<?php else: ?><span class="w n">΢�Ű󶨣�</span>&nbsp;<a href="personal_user.php?act=authenticate"><span style="color:#FF0000">δ��</span></a>&nbsp;<?php endif; ?><br />

 					ϵͳ��Ϣ��δ��<?php if ($this->_vars['msg_total1'] > 0): ?><span><a style="color:#FF0000" href="personal_user.php?act=pm&msgtype=1">(<?php echo $this->_vars['msg_total1']; ?>
)</a></span><?php else: ?><a style="color:#FF0000" href="personal_user.php?act=pm&msgtype=1">(0)</a><?php endif; ?>&nbsp;�Ѷ�<?php if ($this->_vars['msg_total2'] > 0): ?><span><a style="color:#666666" href="personal_user.php?act=pm&msgtype=1">(<?php echo $this->_vars['msg_total2']; ?>
)</a></span><?php else: ?><a style="color:#666666" href="personal_user.php?act=pm&msgtype=1">(0)</a><?php endif; ?><br />
					</div>
				</div>
				<div class="rbox">
				  <div class="mli">
				  	<div class="imgbox" mark="apply_jobs">
					  <div class="count"><?php echo $this->_vars['count_apply']; ?>
</div>
					  <div class="txt">������ְλ</div>
					</div>
				  </div>
				  <div class="mli">
				  	<div class="imgbox" mark="attention_me">
					  <div class="count"><?php echo $this->_vars['count_attention_me']; ?>
</div>
					  <div class="txt">˭�ڹ�ע��</div>
					</div>
				  </div>
				  <div class="mli">
				  	<div class="imgbox" mark="interview">
					  <div class="count"><?php echo $this->_vars['count_interview']; ?>
</div>
					  <div class="txt">��������</div>
					</div>
				  </div>
				  <div class="clear"></div>
				</div>
				<div class="clear"></div>
	</div>
	<?php if ($this->_vars['my_resume']): ?>
	<?php if (isset($this->_sections['list'])) unset($this->_sections['list']);
$this->_sections['list']['name'] = 'list';
$this->_sections['list']['loop'] = is_array($this->_vars['my_resume']) ? count($this->_vars['my_resume']) : max(0, (int)$this->_vars['my_resume']);
$this->_sections['list']['show'] = true;
$this->_sections['list']['max'] = $this->_sections['list']['loop'];
$this->_sections['list']['step'] = 1;
$this->_sections['list']['start'] = $this->_sections['list']['step'] > 0 ? 0 : $this->_sections['list']['loop']-1;
if ($this->_sections['list']['show']) {
	$this->_sections['list']['total'] = $this->_sections['list']['loop'];
	if ($this->_sections['list']['total'] == 0)
		$this->_sections['list']['show'] = false;
} else
	$this->_sections['list']['total'] = 0;
if ($this->_sections['list']['show']):

		for ($this->_sections['list']['index'] = $this->_sections['list']['start'], $this->_sections['list']['iteration'] = 1;
			 $this->_sections['list']['iteration'] <= $this->_sections['list']['total'];
			 $this->_sections['list']['index'] += $this->_sections['list']['step'], $this->_sections['list']['iteration']++):
$this->_sections['list']['rownum'] = $this->_sections['list']['iteration'];
$this->_sections['list']['index_prev'] = $this->_sections['list']['index'] - $this->_sections['list']['step'];
$this->_sections['list']['index_next'] = $this->_sections['list']['index'] + $this->_sections['list']['step'];
$this->_sections['list']['first']	  = ($this->_sections['list']['iteration'] == 1);
$this->_sections['list']['last']	   = ($this->_sections['list']['iteration'] == $this->_sections['list']['total']);
?>
  <div class="myresume" pid="<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['id']; ?>
" id="resume_box_<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['id']; ?>
" <?php if ($this->_sections['list']['index'] > 0): ?>style="display:none"<?php endif; ?>>
  	<div class="lbox">
        <div class="clearfix">
        	<div class="resume-choose f-left">
        		<span class="choose-span"><?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['title']; ?>
</span>
        		<ul class="resume-choose-list">
        			<?php if (count((array)$this->_vars['my_resume'])): foreach ((array)$this->_vars['my_resume'] as $this->_vars['li']): ?>
          			<li id="<?php echo $this->_vars['li']['id']; ?>
" title="<?php echo $this->_vars['li']['title']; ?>
"><?php echo $this->_vars['li']['title']; ?>
</li>
          			<?php endforeach; endif; ?>
        		</ul>
        	</div>
        </div>
	<div class="txt">
		����״̬��<?php if ($this->_vars['my_resume'][$this->_sections['list']['index']]['display'] == "1"): ?>����<?php elseif ($this->_vars['my_resume'][$this->_sections['list']['index']]['display'] == "2"): ?>����<?php else: ?>�ر�<?php endif; ?><br>
		��ʵ������<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['fullname']; ?>
<br />
		����ʱ�䣺<?php echo $this->_run_modifier($this->_vars['my_resume'][$this->_sections['list']['index']]['refreshtime'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M"); ?>
<br />
	  	���״̬��<?php if ($this->_vars['my_resume'][$this->_sections['list']['index']]['audit'] == "1"): ?><span style="color:#009900">���ͨ��</span><?php elseif ($this->_vars['my_resume'][$this->_sections['list']['index']]['audit'] == "2"): ?><span style="color:#FF0000">�����</span><?php else: ?><span style="color:#FF0000">���δͨ��</span><?php endif; ?><br />
	  </div>
	</div>
	<div class="cbox">
		<div class="imgbox1-<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['complete_percent']; ?>
"></div><!--imgbox1-40 ��40%   imgbox1-55��55%-->	
	    <div class="imgboxtit">���������ȣ�<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['complete_percent']; ?>
%</div>
	</div>
	<div class="cbox">
		<div class="imgbox2-<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['level']; ?>
"></div><!--imgbox2-1 �ǲ�   imgbox2-2 ��  imgbox2-3 ��-->		
	    <div class="imgboxtit">��������</div>
	</div>
	<div class="rbox">
		<div class="but">
			<a class="refresh_resume" pid="<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['id']; ?>
" href="javascript:void(0);">ˢ�¼���</a>
			<a href="personal_resume.php?act=edit_resume&pid=<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['id']; ?>
">�޸ļ���</a>
			<a target="_blank" href="<?php echo $this->_run_modifier("QS_jobslist,jobcategory:" . $this->_vars['my_resume'][$this->_sections['list']['index']]['interestjobs'] . "", 'qishi_url', 'plugin', 1); ?>
" class="o">ƥ��ְλ</a>
		</div>	
	    <div class="bottomlink link_lan"><a target="_blank" href="<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['resume_url']; ?>
">Ԥ������</a><a class="resume_privacy" pid="<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['id']; ?>
" href="javascript:void(0);">��˽����</a><?php if ($this->_vars['my_resume'][$this->_sections['list']['index']]['entrust'] == 0): ?><a id="entrudt_id" pid="<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['id']; ?>
" href="javascript:void(0);">ί�м���<img src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/73.gif" alt="" /></a><?php else: ?><a  href="personal_resume.php?act=set_entrust_del&pid=<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['id']; ?>
">ȡ��ί��</a><?php endif; ?><a url="personal_ajax.php?act=del_resume&pid=<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['id']; ?>
" href="javascript:void(0);" class="del_resume" pid="<?php echo $this->_vars['my_resume'][$this->_sections['list']['index']]['id']; ?>
">ɾ������</a></div>
	</div>
	<div class="clear"></div>
</div>
<?php endfor; endif; ?>
<?php else: ?>
<div class="no-rec-box">
	<p>
		��������ְ����������д�������ܸ����ҵ��ù�����<br />
		ȥ��дһ�����ʵļ����ɣ�������ˣ��������������ҵ�����㣡
	</p>
	<input type="button" value="��������" class="creat-resume" onclick="window.location.href='personal_resume.php?act=make1'"/>
</div>
<?php endif; ?>
	<div class="personal-rec-jobs">
		<div class="p-rec-job-top clearfix">
			<h2 class="f-left">�Ƽ�ְλ</h2>
			<a target="_blank" href="<?php echo $this->_run_modifier("QS_jobslist", 'qishi_url', 'plugin', 1); ?>
" class="underline f-right">�鿴����>></a>
		</div>
		<div class="p-rec-data">
			<div id="interest_jobs_list"></div>
		</div>
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
