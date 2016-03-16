<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.cat.php'); $this->register_modifier("cat", "tpl_modifier_cat",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.date_format.php'); $this->register_modifier("date_format", "tpl_modifier_date_format",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_parse_url.php'); $this->register_modifier("qishi_parse_url", "tpl_modifier_qishi_parse_url",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:41 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<title><?php echo $this->_vars['title']; ?>
</title>
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<meta name="author" content="�Һ�«" />
<meta name="copyright" content="zhaohulu.com" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/user_common.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/user_company.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/ui-dialog.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min-common.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.vtip-min.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.reasontip-min.js" type='text/javascript' language="javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	// ����ְλ �ɹ���ʾ����
	var addjobs_save_succeed="<?php echo $this->_vars['jobs_one']['id']; ?>
";
	if(addjobs_save_succeed>0)
	{
		
		var d=dialog({
	        title: 'ϵͳ��ʾ',
	        content: $(".addjob-success-dialog"),
	        cancelDisplay: false,
	        cancel: function () {
	        	window.location.href="?act=jobs";
	        }
	    }).showModal();
	    // �ö�
	    $(".set_promotion").live('click', function(event) {
	    	d.close().remove();
	    	set_promotion_dialog(".set_promotion");
	    });
	    // ԤԼ
	    $(".appointmentSee").die().live('click', function(event) {
	    	d.close().remove();
			var appointDia =  dialog({
				title: 'ԤԼˢ��',
				content: $('.yuyue-dialog'),
				width: '420px'
			});
			appointDia.showModal();
			$(".DialogClose").live('click',function() {
		      appointDia.close().remove();
		    });
			var jobNameIdArray = $(this).attr('data').split(",");
			$("#appointJobName").html(jobNameIdArray[0]).attr("jobid",jobNameIdArray[1]);
			$("#aloneIntegralDays").keyup(function() {
				var dayCount = parseInt($(this).val()), thepointAll = parseInt($("#consumptionPoint").html()), thepointEvery = parseInt($("#everyDayConsumptionPoint").html());
				dayCount ? dayCount = dayCount : dayCount = 0;
				$("#aloneIntegral").html(dayCount*thepointEvery);
				$("#theConsumptionPoint").html(dayCount*thepointEvery);
				if (thepointAll < dayCount*thepointEvery) { // �������Ļ��ִ����ܻ��ֲ���ԤԼ
					$('input[name=toMakeAppointment]').removeClass('toMakeAppointment');
					$("#theNoEnouPoint").show();
				} else {
					$('input[name=toMakeAppointment]').addClass('toMakeAppointment');
					$("#theNoEnouPoint").hide();
				}
			});
			$(".toMakeAppointment").die().live('click', function(event) {
				if (!$("#aloneIntegralDays").val()) {
					alert("��дԤԼ������");
					$("#aloneIntegralDays").focus();
					return false;
				}
				var dataCode = $("#appointJobName").attr('jobid')+","+$("#aloneIntegralDays").val()+","+$("#theConsumptionPoint").html();
				$.get('company_recruitment.php?act=refresh_appointment', {data_arr:dataCode}, function(data){
					appointDia.content(data);
				});
			});
			$(".batchAppoint").click(function(e) {
				appointDia.content($('.yuyue-all-dialog'));
				appointDia.width("540px");
				var oldDataPool = new Array(), oldListHtmArray = $(".yue-left-block .yue-item");
				$.each(oldListHtmArray, function(index, val) {
					var oldHtmId = $(this).attr('reid'), oldHtmCn = $(this).find(".appoint").attr('data');
					oldDataPool.push(oldHtmId+','+oldHtmCn);
				});
				$(".appoint").die().live('click', function() {
					var yitObj = $(this).closest('.yue-item'), jobName = $(this).attr('data'), jobId = yitObj.attr('reid'), rightListHtm = '';
					oldDataPool.splice(jQuery.inArray(jobId+','+jobName,oldDataPool),1);
					yitObj.remove();
					rightListHtm += '<div class="hasyue-item clearfix" reid="'+jobId+'"><div class="hasyue-job f-left">'+jobName+'</div><a href="javascript:;" class="notyue-btn f-left cancelAppoint" data="'+jobName+'">ȡ��</a><div class="yue-time f-right"><input class="batchAppDays" type="text" /> ��</div></div>';
					$(".yue-right-block").append(rightListHtm);
					// ����ԤԼʱ�������Ļ���
					$(".batchAppDays").die().live('keyup', function(event) {
						var pvdysa= parseInt($("#bacMoreTime").html()), evallPointVal = 0;
						$(".yue-right-block .batchAppDays").each(function(index, el) {
							var evdysa = parseInt($(this).val());
							evdysa ? evdysa = evdysa : evdysa = 0;
							evallPointVal += pvdysa*evdysa;
						});
						$("#bAlPiont").html(evallPointVal);
						if (parseInt($("#bAlPiontLast").html()) < evallPointVal) {
							$("#noEnouPoint").show();
							$('input[name=aKeyAppoint]').removeClass('aKeyAppoint');
						} else {
							$("#noEnouPoint").hide();
							$('input[name=aKeyAppoint]').addClass('aKeyAppoint');
						}
					});
					// һ��ԤԼ
					$(".aKeyAppoint").die().live('click', function(event) {
						if (!$(".yue-right-block .hasyue-item").length > 0) {
							alert("����ԤԼְλ��");return false;
						};
						var isalertPoi = 0;
						$(".yue-right-block .batchAppDays").each(function(index, el) {
							isalertPoi += parseInt($(this).val());
						});
						if (!isalertPoi > 0) {
							alert("����дԤԼ������");return false;
						};
						var aPArray = new Array();
						$(".yue-right-block .hasyue-item").each(function(ind, eldd) {
							var ajid = $(this).attr('reid'), ajname = $(this).find(".batchAppDays").val(),
								ajpoint = parseInt($(this).find('.batchAppDays').val());
							ajpoint ? ajpoint = ajpoint : ajpoint = 0;
							var ajpointAll = ajpoint*parseInt($("#bacMoreTime").html())
							aPArray[ind] = ajid+","+ajname+","+ajpointAll;
						});
						$.get('company_recruitment.php?act=refresh_appointment', {data_arr:aPArray}, function(data) {
							appointDia.content(data);
						});
					});
					$(".cancelAppoint").die().live('click', function() {
						var oldYitObj = $(this).closest('.hasyue-item'), oldJobName = $(this).attr('data'), oldJobId = oldYitObj.attr('reid'), leftListHtm = '<div class="yue-title clearfix"><span class="f-left">��ԤԼְλ</span></div>';
						oldDataPool.push(oldJobId+','+oldJobName);
						oldYitObj.remove();
						$.each(oldDataPool, function(index, val) {
							var leftHtmArray = val.split(",");
							leftListHtm += '<div class="yue-item clearfix" reid="'+leftHtmArray[0]+'"><p class="yue-job f-left">'+leftHtmArray[1]+'</p><a href="javascript:;" class="yue-btn f-right appoint" data="'+leftHtmArray[1]+'">ԤԼ</a></div>';
						})
						$(".yue-left-block").html(leftListHtm);
					});
				});
			})
		});
	}
	// �����ر�
	$(".ctrl-close").click(function(event) {
		var mycoDialog=dialog(),
		url = $(this).attr("url");
		mycoDialog.title('ϵͳ��ʾ');
		mycoDialog.content('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text close-tips-text">�رպ󽫲�����չʾ��Ƹ��Ϣ����ȷ��Ҫ�ر���</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
	    mycoDialog.width('300');
	    mycoDialog.showModal();
	    /* �ر� */
	    $(".DialogClose").live('click',function() {
	      mycoDialog.close().remove();
	    });
	    // ȷ��
	    $(".DialogSubmit").click(function() {
	    	if (url) {window.location.href=url};
	    });
	});
	/*
		����ɸѡ 36 
	*/
	$('.data-filter').on('click', function(e){
		$(this).find('.filter-down').toggle();
		// ��̬���������б���
		var fWidth = $(this).find('.filter-span').outerWidth(true) - 2;
		$(this).find(".filter-down").width(fWidth);
		// �������λ����������
		$(document).one("click",function(){
			$('.filter-down').hide();
		});
		e.stopPropagation();
		//�������ʱ������������
		$(".data-filter").not($(this)).find('.filter-down').hide();
	});
	vtip_reason("<?php echo $this->_vars['QISHI']['site_dir']; ?>
","jobs_reason");
	// ����ˢ��
	$('.refresh').on('click', function()
	{
		var jobsid = $(this).attr("jobsid"),
		ajax_url = "company_ajax.php?act=jobs_refresh_ajax&jobsid="+jobsid,
		msg = '';
		var myDialog = dialog();
		myDialog.title('ˢ����ʾ');
		myDialog.content("������...");
		myDialog.width('300');
		myDialog.showModal();
		jQuery.ajax({
			url: ajax_url,
			success: function (data) {
				myDialog.content(data);
				/* �ر� */
				$(".DialogClose").live('click',function() {
				myDialog.close().remove();
				});
				/* ȷ������ */
				$(".DialogSubmit").click(function() 
				{
					window.location.href="company_jobs.php?act=jobs_perform&y_id="+jobsid+"&refresh=1";
				});
			}
		});
	});
	// ����ˢ��
	$("#refresh_all").on('click', function(){
		var length=$("#form1 :checkbox[name='y_id[]'][checked]").length;
		$.get("company_ajax.php?act=jobs_all_refresh_ajax",{length:length},
		function(result)
		{
			var myDialog=dialog();
				myDialog.title('ˢ����ʾ');
				myDialog.content(result);
				myDialog.width('300');
				myDialog.showModal();
				/* �ر� */
				$(".DialogClose").live('click',function() {
					myDialog.close().remove();
					return false;
				});
				// ȷ��
				$(".DialogSubmit").click(function() 
				{
					$("#form1").submit();
				});
		});
	});
	// ְλ�ƹ�
	set_promotion_dialog(".set_promotion");
    set_reward_dialog(".set_reward");
    set_date_dialog(".set_date");


	// �ƹ�����
	$(".spread").toggle(function() {
		$(this).find(".spread_but_group").slideDown("fast");
		$(this).find("img").attr("src","<?php echo $this->_vars['QISHI']['site_template']; ?>
/images/spread_icon_up.gif");
	}, function() {
		$(this).find(".spread_but_group").slideUp("fast");
		$(this).find("img").attr("src","<?php echo $this->_vars['QISHI']['site_template']; ?>
/images/spread_icon.gif");
	});
	/*���� �ر�ְλ */
	$("#display2").click(function(){
		var length=$("#form1 .check-control :checkbox[checked]").length;
		if(length>0)
		{
			var cof = confirm("���Ƿ�Ҫ�ر���ѡ�е�ְλ������ѡ��"+length+"��ְλ,ȷ���ر���");
			if(cof) {
				return true;
			} else {
				return false;
			}
		}
		else
		{
			alert("��û��ѡ��ְλ��");
			return false;
		}
		
	});
	// ɾ������
	delete_dialog('.ctrl-del','#form1');
	// ��������Ȧ
	$(".pengyouquan").on('click', function() {
		id=$(this).attr('id');
		var pengyouquan = dialog({
	    content: '<img src="<?php echo $this->_vars['QISHI']['site_domain'];  echo $this->_vars['QISHI']['site_dir']; ?>
plus/url_qrcode.php?url=<?php echo $this->_vars['w_url']; ?>
" alt="ɨ���ά��" width="80" height="80" />',
	    quickClose: true// ����հ״����ٹر�
		});
		pengyouquan.showModal(document.getElementById(''+id+''));
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

<div class="page_location link_bk">��ǰλ�ã�<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
">��ҳ</a> > <a href="<?php echo $this->_vars['userindexurl']; ?>
">��Ա����</a> > ְλ����</div>
<div class="usermain">
  <div class="leftmenu  com link_bk">
  	 <?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("member_company/left.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
  </div>
  <div class="rightmain">
 	<div class="bbox1 link_bk my_account">
		<div class="topnav">
			<div class="topnav get_resume">
				<div class="titleH1"><div class="h1-title">����ְλ</div></div>
				<div class="navs link_bk">
					<a href="?act=<?php echo $this->_vars['act']; ?>
&jobtype=" <?php if ($_GET['jobtype'] == ""): ?>class="se"<?php endif; ?>>
					������ְλ<span >(<?php echo $this->_vars['jobs_total'][0]; ?>
)</span></a>
					<a href="?act=<?php echo $this->_vars['act']; ?>
&jobtype=2" <?php if ($_GET['jobtype'] == "2"): ?>class="se"<?php endif; ?>>
					�����ְλ<span >(<?php echo $this->_vars['jobs_total'][2]; ?>
)</span></a>
					<a href="?act=<?php echo $this->_vars['act']; ?>
&jobtype=3" <?php if ($_GET['jobtype'] == "3"): ?>class="se"<?php endif; ?>>δ��ʾְλ<span class="check">(<?php echo $this->_vars['jobs_total'][3]; ?>
)</span></a>
					<a href="?act=<?php echo $this->_vars['act']; ?>
&jobtype=1" <?php if ($_GET['jobtype'] == "1"): ?>class="se"<?php endif; ?>>
					ȫ��ְλ<span >(<?php echo $this->_vars['jobs_total'][1]; ?>
)</span></a>
					<div class="clear"></div>
				</div>
			</div>
			<?php if ($this->_vars['QISHI']['operation_mode'] == "2" || $this->_vars['QISHI']['operation_mode'] == "3"): ?>
			<div class="addjob-number">�����˻����Է���<span><?php echo $this->_vars['setmeal']['jobs_ordinary']; ?>
</span>��ְλ���ַ�����<span><?php echo $this->_vars['total'][2]; ?>
</span>��ְλ��</div>
			<?php endif; ?>
		</div>
		<form id="form1" name="form1" method="post" action="?act=jobs_perform">
		<!-- �µķ�����ְλ -->
		<?php if ($_GET['jobtype'] == ""): ?>
		<div class="company-data-list">
			<div class="c-data-top com-job-ma clearfix">
				<div class="item f-left check-item"><input type="checkbox" name="chkAll"   id="chk" title="ȫѡ/��ѡ" /></div>
				<div class="item f-left top-item1">ְλ����</div>
				<div class="item f-left top-item2">
					<div class="data-filter span4">
						<span class="filter-span"><?php if ($_GET['auto_refresh_cn'] == ''): ?>ԤԼˢ��<?php else:  echo $_GET['auto_refresh_cn'];  endif; ?><i class="filter-icon"></i></span>
						<ul class="filter-down">
							<li><a href="<?php echo $this->_run_modifier("auto_refresh:3,auto_refresh_cn:ȫ��", 'qishi_parse_url', 'plugin', 1); ?>
">ȫ��</a></li>
							<li><a href="<?php echo $this->_run_modifier("auto_refresh:1,auto_refresh_cn:��ԤԼ", 'qishi_parse_url', 'plugin', 1); ?>
">��ԤԼ</a></li>
							<li><a href="<?php echo $this->_run_modifier("auto_refresh:2,auto_refresh_cn:δԤԼ", 'qishi_parse_url', 'plugin', 1); ?>
">δԤԼ</a></li>
						</ul>
					</div>
				</div>
				<div class="item f-left top-item3">
					<div class="data-filter span4">
						<span class="filter-span"><?php if ($_GET['generalize_cn'] == ''): ?>�ƹ�״̬<?php else:  echo $_GET['generalize_cn'];  endif; ?><i class="filter-icon"></i></span>
						<ul class="filter-down">  
							<li><a href="<?php echo $this->_run_modifier("generalize:,generalize_cn:ȫ��", 'qishi_parse_url', 'plugin', 1); ?>
">ȫ��</a></li>
							<li><a href="<?php echo $this->_run_modifier("generalize:stick,generalize_cn:�ö�", 'qishi_parse_url', 'plugin', 1); ?>
">�ö�</a></li>
							<li><a href="<?php echo $this->_run_modifier("generalize:highlight,generalize_cn:��ɫ", 'qishi_parse_url', 'plugin', 1); ?>
">��ɫ</a></li>
							<li><a href="<?php echo $this->_run_modifier("generalize:emergency,generalize_cn:����", 'qishi_parse_url', 'plugin', 1); ?>
">����</a></li>
							<li><a href="<?php echo $this->_run_modifier("generalize:recommend,generalize_cn:�Ƽ�", 'qishi_parse_url', 'plugin', 1); ?>
">�Ƽ�</a></li>
						</ul>
					</div>
				</div>
			</div>
			<?php if ($this->_vars['jobs']): ?>
			<?php if (count((array)$this->_vars['jobs'])): foreach ((array)$this->_vars['jobs'] as $this->_vars['li']): ?>
			<div class="c-data-row check-control">
				<div class="c-data-content com-job-ma clearfix">
					<div class="c-item f-left check-item"><input name="y_id[]" type="checkbox" id="y_id"  value="<?php echo $this->_vars['li']['id']; ?>
"/></div>
					<div class="c-item f-left content1">
						<div class="job-ma-block">
							<div><a href="<?php echo $this->_vars['li']['jobs_url']; ?>
" target="_blank" class="name-link underline" title="<?php echo $this->_vars['li']['jobs_name_']; ?>
"><?php echo $this->_vars['li']['jobs_name']; ?>
</a></div>
							<p>ӦƸ������<?php echo $this->_vars['li']['countresume']; ?>
 | ����ʱ�䣺<?php echo $this->_run_modifier($this->_vars['li']['refreshtime'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M"); ?>
 <a  href="<?php if ($this->_vars['QISHI']['operation_mode'] == "1"): ?>?act=jobs_perform&refresh=1&y_id=<?php echo $this->_vars['li']['id'];  else: ?>javascript:;<?php endif; ?>" jobsid="<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline refresh">[ˢ��]</a></p>
							<div class="job-ma-ctrl">
								<a href="?act=editjobs&id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">�޸�</a>&nbsp;<a href="<?php echo $this->_run_modifier($this->_run_modifier("QS_resumelist,jobcategory:", 'cat', 'plugin', 1, $this->_vars['li']['jobcategory']), 'qishi_url', 'plugin', 1); ?>
" class="data-ctrl underline" target="_blank">ƥ��</a>&nbsp;<a url="?act=jobs_perform&display2=1&y_id=<?php echo $this->_vars['li']['id']; ?>
" href="javascript:;" class="data-ctrl underline ctrl-close">�ر�</a>&nbsp;<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">ɾ��</a>
								<a href="javascript:;" class="pengyouquan" id="jobs_<?php echo $this->_vars['li']['id']; ?>
">��������Ȧ</a>
							</div>
						</div>
					</div>
					<?php if ($this->_vars['li']['auto_refresh'] == 0): ?>
					<div class="c-item f-left content2"><span class="hasyuyue">δԤԼ</span><a href="javascript:;" class="check-yuyue data-ctrl underline appointmentSee" data="<?php echo $this->_vars['li']['jobs_name_']; ?>
,<?php echo $this->_vars['li']['id']; ?>
">[ԤԼ]</a></div>
					<?php else: ?>
					<div class="c-item f-left content2"><span class="hasyuyue">��ԤԼ</span><a href="javascript:;" class="check-yuyue data-ctrl underline appointmentShow" data="<?php echo $this->_vars['li']['id']; ?>
">[�鿴]</a></div>
					<?php endif; ?>
					<div class="c-item f-left content3">
						<?php if ($this->_vars['li']['stick'] != 1): ?>
							<a  href="javascript:void(0);" class="data-ctrl set_promotion" catid="3" jobid="<?php echo $this->_vars['li']['id']; ?>
">�ö�</a>
						<?php else: ?>
							<a  href="javascript:void(0);" class="data-ctrl underline" style="color:#999" title="��ְλ���ö�">�ö�</a>
						<?php endif; ?>
						<?php if ($this->_vars['li']['highlight'] == ""): ?>
						<a  href="javascript:void(0);" class="data-ctrl set_promotion" catid="4" jobid="<?php echo $this->_vars['li']['id']; ?>
">��ɫ</a><br />
						<?php else: ?>
						<a  href="javascript:void(0);" class="data-ctrl underline" style="color:#999" title="��ְλ����ɫ">��ɫ</a><br />
						<?php endif; ?>
						<?php if ($this->_vars['li']['emergency'] == "0"): ?>
						<a href="javascript:void(0);" class="data-ctrl set_promotion" catid="2" jobid="<?php echo $this->_vars['li']['id']; ?>
">����</a>
						<?php else: ?>
						<a  href="javascript:void(0);" class="data-ctrl underline" style="color:#999" title="��ְλ�ѽ����ƹ�">����</a>
						<?php endif; ?>
						<?php if ($this->_vars['li']['recommend'] == "0"): ?>
						<a href="javascript:void(0);" class="data-ctrl set_promotion" catid="1" jobid="<?php echo $this->_vars['li']['id']; ?>
">�Ƽ�</a>
						<?php else: ?>
						<a  href="javascript:void(0);" class="data-ctrl underline" style="color:#999" title="��ְλ�ѽ����Ƽ�">�Ƽ�</a>
 						<?php endif; ?><br>
                        <?php if ($this->_vars['li']['reward'] == "0"): ?>
                        <a href="javascript:void(0);" class="data-ctrl set_reward" catid="5" jobid="<?php echo $this->_vars['li']['id']; ?>
">����</a>
                        <?php else: ?>
                        <a  href="javascript:void(0);" class="data-ctrl underline set_date" catid="5" jobid="<?php echo $this->_vars['li']['id']; ?>
" style="color:#999" title="��ְλ������">����</a>
                    
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; endif; ?>
			<script type="text/javascript">
				/*�鿴ԤԼ*/
				$(".appointmentShow").live('click', function()
				{
					var id =$(this).attr('data');
					var myDialog = dialog();
				    jQuery.ajax({
				        url: 'company_ajax.php?act=auto_refresh&id='+id,
				        success: function (data) {
				            myDialog.content(data);
				            myDialog.title('ԤԼ����');
				            myDialog.width('500');
				            myDialog.showModal();
				        }
				    });
				});
				$(".appointmentSee").die().live('click', function(event) {
					var appointDia =  dialog({
						title: 'ԤԼˢ��',
						content: $('.yuyue-dialog'),
						width: '420px'
					});
					appointDia.showModal();
					$(".DialogClose").live('click',function() {
				      appointDia.close().remove();
				    });
					var jobNameIdArray = $(this).attr('data').split(",");
					$("#appointJobName").html(jobNameIdArray[0]).attr("jobid",jobNameIdArray[1]);
					$("#aloneIntegralDays").keyup(function() {
						var dayCount = parseInt($(this).val()), thepointAll = parseInt($("#consumptionPoint").html()), thepointEvery = parseInt($("#everyDayConsumptionPoint").html());
						dayCount ? dayCount = dayCount : dayCount = 0;
						$("#aloneIntegral").html(dayCount*thepointEvery);
						$("#theConsumptionPoint").html(dayCount*thepointEvery);
						if (thepointAll < dayCount*thepointEvery) { // �������Ļ��ִ����ܻ��ֲ���ԤԼ
							$('input[name=toMakeAppointment]').removeClass('toMakeAppointment');
							$("#theNoEnouPoint").show();
						} else {
							$('input[name=toMakeAppointment]').addClass('toMakeAppointment');
							$("#theNoEnouPoint").hide();
						}
					});
					$(".toMakeAppointment").die().live('click', function(event) {
						if (!$("#aloneIntegralDays").val()) {
							alert("��дԤԼ������");
							$("#aloneIntegralDays").focus();
							return false;
						}
						var dataCode = $("#appointJobName").attr('jobid')+","+$("#aloneIntegralDays").val()+","+$("#theConsumptionPoint").html();
						$.get('company_recruitment.php?act=refresh_appointment', {data_arr:dataCode}, function(data){
							appointDia.content(data);
						});
					});
					$(".batchAppoint").click(function(e) {
						appointDia.content($('.yuyue-all-dialog'));
						appointDia.width("540px");
						var oldDataPool = new Array(), oldListHtmArray = $(".yue-left-block .yue-item");
						$.each(oldListHtmArray, function(index, val) {
							var oldHtmId = $(this).attr('reid'), oldHtmCn = $(this).find(".appoint").attr('data');
							oldDataPool.push(oldHtmId+','+oldHtmCn);
						});
						$(".appoint").die().live('click', function() {
							var yitObj = $(this).closest('.yue-item'), jobName = $(this).attr('data'), jobId = yitObj.attr('reid'), rightListHtm = '';
							oldDataPool.splice(jQuery.inArray(jobId+','+jobName,oldDataPool),1);
							yitObj.remove();
							rightListHtm += '<div class="hasyue-item clearfix" reid="'+jobId+'"><div class="hasyue-job f-left">'+jobName+'</div><a href="javascript:;" class="notyue-btn f-left cancelAppoint" data="'+jobName+'">ȡ��</a><div class="yue-time f-right"><input class="batchAppDays" type="text" /> ��</div></div>';
							$(".yue-right-block").append(rightListHtm);
							// ����ԤԼʱ�������Ļ���
							$(".batchAppDays").die().live('keyup', function(event) {
								var pvdysa= parseInt($("#bacMoreTime").html()), evallPointVal = 0;
								$(".yue-right-block .batchAppDays").each(function(index, el) {
									var evdysa = parseInt($(this).val());
									evdysa ? evdysa = evdysa : evdysa = 0;
									evallPointVal += pvdysa*evdysa;
								});
								$("#bAlPiont").html(evallPointVal);
								if (parseInt($("#bAlPiontLast").html()) < evallPointVal) {
									$("#noEnouPoint").show();
									$('input[name=aKeyAppoint]').removeClass('aKeyAppoint');
								} else {
									$("#noEnouPoint").hide();
									$('input[name=aKeyAppoint]').addClass('aKeyAppoint');
								}
							});
							// һ��ԤԼ
							$(".aKeyAppoint").die().live('click', function(event) {
								if (!$(".yue-right-block .hasyue-item").length > 0) {
									alert("����ԤԼְλ��");return false;
								};
								var isalertPoi = 0;
								$(".yue-right-block .batchAppDays").each(function(index, el) {
									isalertPoi += parseInt($(this).val());
								});
								if (!isalertPoi > 0) {
									alert("����дԤԼ������");return false;
								};
								var aPArray = new Array();
								$(".yue-right-block .hasyue-item").each(function(ind, eldd) {
									var ajid = $(this).attr('reid'), ajname = $(this).find(".batchAppDays").val(),
										ajpoint = parseInt($(this).find('.batchAppDays').val());
									ajpoint ? ajpoint = ajpoint : ajpoint = 0;
									var ajpointAll = ajpoint*parseInt($("#bacMoreTime").html())
									aPArray[ind] = ajid+","+ajname+","+ajpointAll;
								});
								$.get('company_recruitment.php?act=refresh_appointment', {data_arr:aPArray}, function(data) {
									appointDia.content(data);
								});
							});
							$(".cancelAppoint").die().live('click', function() {
								var oldYitObj = $(this).closest('.hasyue-item'), oldJobName = $(this).attr('data'), oldJobId = oldYitObj.attr('reid'), leftListHtm = '<div class="yue-title clearfix"><span class="f-left">��ԤԼְλ</span></div>';
								oldDataPool.push(oldJobId+','+oldJobName);
								oldYitObj.remove();
								$.each(oldDataPool, function(index, val) {
									var leftHtmArray = val.split(",");
									leftListHtm += '<div class="yue-item clearfix" reid="'+leftHtmArray[0]+'"><p class="yue-job f-left">'+leftHtmArray[1]+'</p><a href="javascript:;" class="yue-btn f-right appoint" data="'+leftHtmArray[1]+'">ԤԼ</a></div>';
								})
								$(".yue-left-block").html(leftListHtm);
							});
						});
					})
				});
			</script>
			<!-- ����ԤԼ���� -->
			<div class="yuyue-one-dialog yuyue-dialog" style="display:none">
				<?php if ($this->_vars['add_mode'] == 1): ?>
				<div class="short-text-tip" style="margin-left:0">�����˻�ʣ�� <span id="consumptionPoint"><?php echo $this->_vars['user_points']; ?>
</span> �֣�����ԤԼ������ <span id="theConsumptionPoint">0</span> �֣�</div>
				<div class="yo-block">
					<div class="yue-one-item">
						<span class="yo-type">ԤԼְλ��</span><font id="appointJobName" jobid=""></font><a href="javascript:;" class="underline batchAppoint">����ԤԼְλ</a>
					</div>
					<div class="yue-one-item">
						<span class="yo-type">ˢ�´�����</span>1��/��
					</div>
					<div class="yue-one-item">
						<span class="yo-type">ԤԼ������</span><input id="aloneIntegralDays" type="text" /> ��
					</div>
					<div class="yue-one-item">
						<span class="yo-type">���Ļ��֣�</span><em id="aloneIntegral">0</em> ��<span class="use-fen">��ÿ������<em id="everyDayConsumptionPoint"><?php echo $this->_vars['points_rule']['job_auto_refresh']['value']; ?>
</em>���֣�</span>
					</div>
				</div>
				<div class="center-btn-wrap">
					<input type="button" value="ԤԼ" name="toMakeAppointment" class="btn-65-30blue btn-big-font toMakeAppointment" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" />
				</div>
				<?php else: ?>
				<div>�ײ�ģʽ�²���ʹ��ԤԼˢ�£�</div>
				<?php endif; ?>
			</div>
			<!-- ����ԤԼ���� end-->
			<!-- ����ԤԼ���� -->
			<div class="yuyue-all-dialog" style="display:none">
				<?php if ($this->_vars['add_mode'] == 1): ?>
				<div class="short-text-tip" style="margin-left:0">�����˻�ʣ�� <span id="bAlPiontLast"><?php echo $this->_vars['user_points']; ?>
</span> �֣�����ԤԼ������ <span id="bAlPiont">0</span> �֣�<span style="display:none" id="noEnouPoint">���ֲ��㣬</span><a href="company_service.php?act=order_add" class="underline">������ֵ</a></div>
				<div class="yuyue-d clearfix">
					<div class="yuyue-left f-left">
						<div class="yue-left-block">
							<div class="yue-title clearfix"><span class="f-left">��ԤԼְλ</span></div>
							<?php if (count((array)$this->_vars['jobs_refresh'])): foreach ((array)$this->_vars['jobs_refresh'] as $this->_vars['yy_li']): ?>
							<div class="yue-item clearfix" reid="<?php echo $this->_vars['yy_li']['id']; ?>
">
								<p class="yue-job f-left"><?php echo $this->_vars['yy_li']['jobs_name']; ?>
</p>
								<a href="javascript:;" class="yue-btn f-right appoint" data="<?php echo $this->_vars['yy_li']['jobs_name']; ?>
">ԤԼ</a>
							</div>
							<?php endforeach; endif; ?>
						</div>
					</div>
					<div class="yuyue-right f-left">
						<div class="yue-right-block">
							<div class="yue-title clearfix"><span class="f-left">��ѡԤԼְλ</span><span class="f-right" style="width:60px">ԤԼ����</span></div>
						</div>
					</div>
				</div>
				<div class="yuyue-d-tip">ÿ��ְλÿ��ˢ�� <span>1</span> �Σ����� <span id="bacMoreTime"><?php echo $this->_vars['points_rule']['job_auto_refresh']['value']; ?>
</span> ���֡�</div>
				<div class="center-btn-wrap">
					<input type="button" name="aKeyAppoint" value="һ��ԤԼ" class="btn-75-30blue btn-big-font aKeyAppoint" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" />
				</div>
				<?php else: ?>
				<div>�ײ�ģʽ�²���ʹ��ԤԼˢ�£�</div>
				<?php endif; ?>
			</div>
			<!-- ����ԤԼ���� end-->
			<div class="c-data-row last">
				<div class="c-data-content apply_jobs clearfix">
					<div class="c-item f-left check-item"><input type="checkbox" name="chkAll"   id="chk2" title="ȫѡ/��ѡ" /></div>
					<div class="data-last-btn f-left">
						<?php if ($this->_vars['QISHI']['operation_mode'] == "3"): ?>
						<input type="hidden" name="refresh" id="refresh" value="1" />
						<input type="button" name="refresh" class="btn-65-30blue"  value="ˢ��ְλ"  id="refresh_all"/>
						<?php else: ?>
						<input type="hidden" name="refresh" id="refresh" value="1" />
						<input type="button" name="refresh" class="btn-65-30blue"  value="ˢ��ְλ"  id="refresh_all"/>
						<?php endif; ?>
						<input type="submit" value="�ر�" name="display2" class="btn-65-30blue" id="display2"/>
						<input type="button" name="delete" class="btn-65-30blue ctrl-del" value="ɾ��" id="delete" act="?act=jobs_perform&delete=1"/>
					</div>
				</div>
			</div>
            <div style="color:red;font-size:15px; padding-left: 10px;"><span>���ƹ��ڵļ������������޸ļ��رգ�ɾ������</span></div>
			<?php else: ?>
			<div class="c-data-row emptytip">û���ҵ���Ӧ��ְλ��Ϣ</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<!-- ����е� ְλ -->
		<?php if ($_GET['jobtype'] == "2"): ?>
		<div class="company-data-list">
			<div class="c-data-top com-job-ma clearfix">
				<div class="item f-left check-item"><input type="checkbox" name="chkAll"   id="chk"/></div>
				<div class="item f-left top-item1">ְλ����</div>
				<div class="item f-left top-item2">��������</div>
				<div class="item f-left top-item3" style="text-align: center;">����</div>
			</div>
			<?php if ($this->_vars['jobs']): ?>
			<?php if (count((array)$this->_vars['jobs'])): foreach ((array)$this->_vars['jobs'] as $this->_vars['li']): ?>
			<div class="c-data-row">
				<div class="c-data-content com-job-ma3 clearfix">
					<div class="c-item f-left check-item"><input type="checkbox" name="y_id[]" id="y_id"  value="<?php echo $this->_vars['li']['id']; ?>
" class="checkbox" /></div>
					<div class="c-item f-left content1" style="width:535px;">
						<div class="job-ma-block">
							<div><a href="<?php echo $this->_vars['li']['jobs_url']; ?>
" target="_blank" class="name-link underline" title="<?php echo $this->_vars['li']['jobs_name_']; ?>
"><?php echo $this->_vars['li']['jobs_name']; ?>
</a></div>
							<p>ӦƸ������<?php echo $this->_vars['li']['countresume']; ?>
 | ����ʱ�䣺<?php echo $this->_run_modifier($this->_vars['li']['refreshtime'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M"); ?>
</p>
						</div>
					</div>
					<div class="c-item f-left content2" style="width:218px;"><?php echo $this->_vars['li']['district_cn']; ?>
</div>
					<div class="c-item f-left content3">
						<a href="?act=editjobs&id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">�޸�</a>&nbsp;<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">ɾ��</a>
					</div>
				</div>
			</div>
			<?php endforeach; endif; ?>
			<div class="c-data-row last">
				<div class="c-data-content apply_jobs clearfix">
					<div class="c-item f-left check-item"><input type="checkbox" name="chkAll"   id="chk2"/></div>
					<div class="data-last-btn f-left">
						<input type="button" name="delete" class="btn-65-30blue ctrl-del" value="ɾ��" id="delete" act="?act=jobs_perform&delete=1"/>
					</div>
				</div>
			</div>
			<?php else: ?>
			<div class="c-data-row emptytip">û���ҵ���Ӧ��ְλ��Ϣ</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<!-- δ��ʾ ְλ -->
		<?php if ($_GET['jobtype'] == "3"): ?>
		<div class="company-data-list">
			<div class="c-data-top com-job-ma clearfix">
				<div class="item f-left check-item"><input type="checkbox" name="chkAll"   id="chk"/></div>
				<div class="item f-left top-item1">ְλ����</div>
				<div class="item f-left top-item2">
					<div class="data-filter span4">
						<span class="filter-span">ְλ״̬<i class="filter-icon"></i></span>
						<ul class="filter-down">
							<li><a href="<?php echo $this->_run_modifier("state:0", 'qishi_parse_url', 'plugin', 1); ?>
">ȫ��</a></li>
							<li><a href="<?php echo $this->_run_modifier("state:1", 'qishi_parse_url', 'plugin', 1); ?>
">δͨ��</a></li>
							<li><a href="<?php echo $this->_run_modifier("state:2", 'qishi_parse_url', 'plugin', 1); ?>
">�ѹر�</a></li>
						</ul>
					</div>
				</div>
				<div class="item f-left top-item3" style="text-align: center;">����</div>
			</div>
			<?php if ($this->_vars['jobs']): ?>
			<?php if (count((array)$this->_vars['jobs'])): foreach ((array)$this->_vars['jobs'] as $this->_vars['li']): ?>
			<div class="c-data-row">
				<div class="c-data-content com-job-ma3 clearfix">
					<div class="c-item f-left check-item"><input type="checkbox" name="y_id[]" id="y_id"  value="<?php echo $this->_vars['li']['id']; ?>
" class="checkbox" /></div>
					<div class="c-item f-left content1" style="width:535px;">
						<div class="job-ma-block">
							<div><a href="<?php echo $this->_vars['li']['jobs_url']; ?>
" target="_blank" class="name-link underline" title="<?php echo $this->_vars['li']['jobs_name_']; ?>
"><?php echo $this->_vars['li']['jobs_name']; ?>
</a></div>
							<p>ӦƸ������<?php echo $this->_vars['li']['countresume']; ?>
 | ����ʱ�䣺<?php echo $this->_run_modifier($this->_vars['li']['refreshtime'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M"); ?>
</p>
						</div>
					</div>
					<div class="c-item f-left content2" style="width:218px;"><?php echo $this->_vars['li']['status_cn']; ?>
</div>
					<div class="c-item f-left content3">
						<?php if ($this->_vars['li']['status'] == 2): ?>
						<a href="?act=editjobs&id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">�޸�</a>&nbsp;<a href="?act=jobs_perform&display1=1&y_id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">�ָ�</a>&nbsp;<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">ɾ��</a>
						<?php else: ?>
						<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">ɾ��</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; endif; ?>
			<div class="c-data-row last">
				<div class="c-data-content apply_jobs clearfix">
					<div class="c-item f-left check-item"><input type="checkbox" name="chkAll"   id="chk2"/></div>
					<div class="data-last-btn f-left">
						<input type="button" name="delete" class="btn-65-30blue ctrl-del" value="ɾ��" id="delete" act="?act=jobs_perform&delete=1"/>
					</div>
				</div>
			</div>
			<?php else: ?>
			<div class="c-data-row emptytip">û���ҵ���Ӧ��ְλ��Ϣ</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<!-- ȫ��ְλ ְλ -->
		<?php if ($_GET['jobtype'] == "1"): ?>
		<div class="company-data-list">
			<div class="c-data-top com-job-ma clearfix">
				<div class="item f-left check-item"><input type="checkbox" id="chk" name="chkAll"></div>
				<div class="item f-left top-item1">ְλ����</div>
				<div class="item f-left top-item2">
					<div class="data-filter span4">
						<span class="filter-span">ְλ״̬<i class="filter-icon"></i></span>
						<ul class="filter-down">
							<li><a href="<?php echo $this->_run_modifier("state:0", 'qishi_parse_url', 'plugin', 1); ?>
">ȫ��</a></li>
							<li><a href="<?php echo $this->_run_modifier("state:1", 'qishi_parse_url', 'plugin', 1); ?>
">������</a></li>
							<li><a href="<?php echo $this->_run_modifier("state:2", 'qishi_parse_url', 'plugin', 1); ?>
">�����</a></li>
							<li><a href="<?php echo $this->_run_modifier("state:3", 'qishi_parse_url', 'plugin', 1); ?>
">δͨ��</a></li>
							<li><a href="<?php echo $this->_run_modifier("state:4", 'qishi_parse_url', 'plugin', 1); ?>
">�ѹر�</a></li>
						</ul>
					</div>
				</div>
				<div class="item f-left top-item3" style="text-align: center;">����</div>
			</div>
			<?php if ($this->_vars['jobs']): ?>
			<?php if (count((array)$this->_vars['jobs'])): foreach ((array)$this->_vars['jobs'] as $this->_vars['li']): ?>
			<div class="c-data-row">
				<div class="c-data-content com-job-ma3 clearfix">
					<div class="c-item f-left check-item"><input type="checkbox" name="y_id[]" id="y_id"  value="<?php echo $this->_vars['li']['id']; ?>
" class="checkbox" /></div>
					<div class="c-item f-left content1" style="width:535px;">
						<div class="job-ma-block">
							<div><a href="<?php echo $this->_vars['li']['jobs_url']; ?>
" target="_blank" class="name-link underline" title="<?php echo $this->_vars['li']['jobs_name_']; ?>
"><?php echo $this->_vars['li']['jobs_name']; ?>
</a></div>
							<p>ӦƸ������<?php echo $this->_vars['li']['countresume']; ?>
 | ����ʱ�䣺<?php echo $this->_run_modifier($this->_vars['li']['refreshtime'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M");  if ($this->_vars['li']['status'] == 1): ?>  <a  href="<?php if ($this->_vars['QISHI']['operation_mode'] == "1"): ?>?act=jobs_perform&refresh=1&y_id=<?php echo $this->_vars['li']['id'];  else: ?>javascript:;<?php endif; ?>" jobsid="<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline refresh">[ˢ��]</a><?php endif; ?></p>
						</div>
					</div>
					<div class="c-item f-left content2" style="width:218px;"><span style="<?php if ($this->_vars['li']['status'] == 2 || $this->_vars['li']['status'] == 4): ?>color:red;<?php elseif ($this->_vars['li']['status'] == 3): ?>color:#FFB443;<?php else:  endif; ?>"><?php echo $this->_vars['li']['status_cn']; ?>
</span></div>
					<div class="c-item f-left content3">
						<?php if ($this->_vars['li']['status'] == 1): ?>
						<a href="?act=editjobs&id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">�޸�</a>&nbsp;<a href="?act=jobs_perform&display2=1&y_id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">�ر�</a>&nbsp;<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">ɾ��</a>
						<?php elseif ($this->_vars['li']['status'] == 2): ?>
						<a href="?act=editjobs&id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">�޸�</a>&nbsp;<a href="?act=jobs_perform&display1=1&y_id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">�ָ�</a>&nbsp;<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">ɾ��</a>
						<?php elseif ($this->_vars['li']['status'] == 3): ?>
						<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">ɾ��</a>
						<?php else: ?>
						<a href="?act=editjobs&id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">�޸�</a>&nbsp;<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">ɾ��</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; endif; ?>
			<div class="c-data-row last">
				<div class="c-data-content apply_jobs clearfix">
					<div class="c-item f-left check-item"><input type="checkbox" name="chkAll"   id="chk2" title="ȫѡ/��ѡ" /></div>
					<div class="data-last-btn f-left">
						<input type="button" name="delete" class="btn-65-30blue ctrl-del" value="ɾ��" id="delete" act="?act=jobs_perform&delete=1"/>
					</div>
				</div>
			</div>
			<?php else: ?>
			<div class="c-data-row emptytip">û���ҵ���Ӧ��ְλ��Ϣ</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		</form>
		<?php if ($this->_vars['page']): ?>
		<table border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td height="50" align="center"> <div class="page link_bk"><?php echo $this->_vars['page']; ?>
</div></td>
          </tr>
      	</table>
		<?php endif; ?>
  	</div>
  </div>

  <div class="clear"></div>
</div>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("user/footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<!-- ����ְλ�ɹ����� -->
<div class="addjob-success-dialog" style="display:none">
	<div class="success-tip">ְ λ �� �� �� ��<a href="?act=addjobs" class="underline">��������ְλ</a></div>
	<p class="sns-tips">����ҵƷ�Ƹ��죬����ƸЧ�ʸ��ߣ�<span>��������ת����Ƹְλ�ɣ�</span></p>
	<style>.bdshare-button-style1-16 a, .bdshare-button-style1-16 .bds_more{padding-left: 0;}</style>
	<div class="bdsharebuttonbox bdshare sns-block share clearfix">
		<a href="#" class="sns-icon sina f-left " title="��������΢��" data-cmd="tsina"></a>
		<a href="#" class="sns-icon renren f-left" title="����������" data-cmd="renren"></a>
		<a href="#" class="sns-icon qzone f-left" title="����QQ�ռ�" data-cmd="qzone"></a>
		<a href="#" class="sns-icon t-weibo f-left" title="������Ѷ΢��"data-cmd="tqq" ></a>
		<a href="#" class="sns-icon qq f-left" title="����QQ����" data-cmd="sqq"></a>
		<a href="javascript:;" class="sns-icon weixin f-left" title="����΢��"></a>
		<div class="weixin-code-box f-left">
			<div class="weixin-border">
				<img src="<?php echo $this->_vars['QISHI']['site_domain'];  echo $this->_vars['QISHI']['site_dir']; ?>
plus/url_qrcode.php?url=<?php echo $this->_vars['w_url']; ?>
" alt="��ά��" width="70" height="70" />
			</div>
				<span>΢��ɨһɨ</span>
			</div>
		<script>window._bd_share_config={"common":{"bdText":"<?php echo $this->_vars['jobs_one']['companyname']; ?>
��@<?php echo $this->_vars['QISHI']['site_name']; ?>
  ����������ְλ : <?php echo $this->_vars['jobs_one']['jobs_name']; ?>
(��н : <?php echo $this->_vars['jobs_one']['wage_cn']; ?>
)","bdDesc":"���˽��������ְλ������Ѷ���ע<?php echo $this->_vars['QISHI']['site_name']; ?>
","bdUrl":"<?php echo $this->_vars['jobs_one']['jobs_url']; ?>
","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"1","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];
		</script>
	</div>
	<p class="succsee-more">Ϊ������������ְ�ߵĹ�ע����������<a href="javascript:;" class="underline set_promotion" catid="3" jobid="<?php echo $this->_vars['jobs_one']['id']; ?>
">�ö�ְλ</a>&nbsp;<a href="javascript:;" data="<?php echo $this->_vars['jobs_one']['jobs_name']; ?>
,<?php echo $this->_vars['jobs_one']['id']; ?>
" class="underline appointmentSee">ְλԤԼˢ��</a></p>
</div>
</body>
</html>