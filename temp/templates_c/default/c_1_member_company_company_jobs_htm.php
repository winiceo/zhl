<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.cat.php'); $this->register_modifier("cat", "tpl_modifier_cat",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.date_format.php'); $this->register_modifier("date_format", "tpl_modifier_date_format",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_parse_url.php'); $this->register_modifier("qishi_parse_url", "tpl_modifier_qishi_parse_url",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:41 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<title><?php echo $this->_vars['title']; ?>
</title>
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<meta name="author" content="找葫芦" />
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
	// 发布职位 成功提示弹出
	var addjobs_save_succeed="<?php echo $this->_vars['jobs_one']['id']; ?>
";
	if(addjobs_save_succeed>0)
	{
		
		var d=dialog({
	        title: '系统提示',
	        content: $(".addjob-success-dialog"),
	        cancelDisplay: false,
	        cancel: function () {
	        	window.location.href="?act=jobs";
	        }
	    }).showModal();
	    // 置顶
	    $(".set_promotion").live('click', function(event) {
	    	d.close().remove();
	    	set_promotion_dialog(".set_promotion");
	    });
	    // 预约
	    $(".appointmentSee").die().live('click', function(event) {
	    	d.close().remove();
			var appointDia =  dialog({
				title: '预约刷新',
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
				if (thepointAll < dayCount*thepointEvery) { // 本次消耗积分大于总积分不能预约
					$('input[name=toMakeAppointment]').removeClass('toMakeAppointment');
					$("#theNoEnouPoint").show();
				} else {
					$('input[name=toMakeAppointment]').addClass('toMakeAppointment');
					$("#theNoEnouPoint").hide();
				}
			});
			$(".toMakeAppointment").die().live('click', function(event) {
				if (!$("#aloneIntegralDays").val()) {
					alert("填写预约天数！");
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
					rightListHtm += '<div class="hasyue-item clearfix" reid="'+jobId+'"><div class="hasyue-job f-left">'+jobName+'</div><a href="javascript:;" class="notyue-btn f-left cancelAppoint" data="'+jobName+'">取消</a><div class="yue-time f-right"><input class="batchAppDays" type="text" /> 天</div></div>';
					$(".yue-right-block").append(rightListHtm);
					// 批量预约时计算消耗积分
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
					// 一键预约
					$(".aKeyAppoint").die().live('click', function(event) {
						if (!$(".yue-right-block .hasyue-item").length > 0) {
							alert("请先预约职位！");return false;
						};
						var isalertPoi = 0;
						$(".yue-right-block .batchAppDays").each(function(index, el) {
							isalertPoi += parseInt($(this).val());
						});
						if (!isalertPoi > 0) {
							alert("请填写预约天数！");return false;
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
						var oldYitObj = $(this).closest('.hasyue-item'), oldJobName = $(this).attr('data'), oldJobId = oldYitObj.attr('reid'), leftListHtm = '<div class="yue-title clearfix"><span class="f-left">可预约职位</span></div>';
						oldDataPool.push(oldJobId+','+oldJobName);
						oldYitObj.remove();
						$.each(oldDataPool, function(index, val) {
							var leftHtmArray = val.split(",");
							leftListHtm += '<div class="yue-item clearfix" reid="'+leftHtmArray[0]+'"><p class="yue-job f-left">'+leftHtmArray[1]+'</p><a href="javascript:;" class="yue-btn f-right appoint" data="'+leftHtmArray[1]+'">预约</a></div>';
						})
						$(".yue-left-block").html(leftListHtm);
					});
				});
			})
		});
	}
	// 单个关闭
	$(".ctrl-close").click(function(event) {
		var mycoDialog=dialog(),
		url = $(this).attr("url");
		mycoDialog.title('系统提示');
		mycoDialog.content('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text close-tips-text">关闭后将不对外展示招聘信息，您确定要关闭吗？</span></div></div><div class="center-btn-wrap"><input type="button" value="确定" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="取消" class="btn-65-30grey btn-big-font DialogClose" /></div>');
	    mycoDialog.width('300');
	    mycoDialog.showModal();
	    /* 关闭 */
	    $(".DialogClose").live('click',function() {
	      mycoDialog.close().remove();
	    });
	    // 确定
	    $(".DialogSubmit").click(function() {
	    	if (url) {window.location.href=url};
	    });
	});
	/*
		顶部筛选 36 
	*/
	$('.data-filter').on('click', function(e){
		$(this).find('.filter-down').toggle();
		// 动态设置下拉列表宽度
		var fWidth = $(this).find('.filter-span').outerWidth(true) - 2;
		$(this).find(".filter-down").width(fWidth);
		// 点击其他位置收起下拉
		$(document).one("click",function(){
			$('.filter-down').hide();
		});
		e.stopPropagation();
		//点击下拉时收起其他下拉
		$(".data-filter").not($(this)).find('.filter-down').hide();
	});
	vtip_reason("<?php echo $this->_vars['QISHI']['site_dir']; ?>
","jobs_reason");
	// 单个刷新
	$('.refresh').on('click', function()
	{
		var jobsid = $(this).attr("jobsid"),
		ajax_url = "company_ajax.php?act=jobs_refresh_ajax&jobsid="+jobsid,
		msg = '';
		var myDialog = dialog();
		myDialog.title('刷新提示');
		myDialog.content("加载中...");
		myDialog.width('300');
		myDialog.showModal();
		jQuery.ajax({
			url: ajax_url,
			success: function (data) {
				myDialog.content(data);
				/* 关闭 */
				$(".DialogClose").live('click',function() {
				myDialog.close().remove();
				});
				/* 确定操作 */
				$(".DialogSubmit").click(function() 
				{
					window.location.href="company_jobs.php?act=jobs_perform&y_id="+jobsid+"&refresh=1";
				});
			}
		});
	});
	// 批量刷新
	$("#refresh_all").on('click', function(){
		var length=$("#form1 :checkbox[name='y_id[]'][checked]").length;
		$.get("company_ajax.php?act=jobs_all_refresh_ajax",{length:length},
		function(result)
		{
			var myDialog=dialog();
				myDialog.title('刷新提示');
				myDialog.content(result);
				myDialog.width('300');
				myDialog.showModal();
				/* 关闭 */
				$(".DialogClose").live('click',function() {
					myDialog.close().remove();
					return false;
				});
				// 确定
				$(".DialogSubmit").click(function() 
				{
					$("#form1").submit();
				});
		});
	});
	// 职位推广
	set_promotion_dialog(".set_promotion");
    set_reward_dialog(".set_reward");
    set_date_dialog(".set_date");


	// 推广下拉
	$(".spread").toggle(function() {
		$(this).find(".spread_but_group").slideDown("fast");
		$(this).find("img").attr("src","<?php echo $this->_vars['QISHI']['site_template']; ?>
/images/spread_icon_up.gif");
	}, function() {
		$(this).find(".spread_but_group").slideUp("fast");
		$(this).find("img").attr("src","<?php echo $this->_vars['QISHI']['site_template']; ?>
/images/spread_icon.gif");
	});
	/*批量 关闭职位 */
	$("#display2").click(function(){
		var length=$("#form1 .check-control :checkbox[checked]").length;
		if(length>0)
		{
			var cof = confirm("您是否要关闭您选中的职位，您共选中"+length+"个职位,确定关闭吗？");
			if(cof) {
				return true;
			} else {
				return false;
			}
		}
		else
		{
			alert("您没有选中职位！");
			return false;
		}
		
	});
	// 删除弹出
	delete_dialog('.ctrl-del','#form1');
	// 分享到朋友圈
	$(".pengyouquan").on('click', function() {
		id=$(this).attr('id');
		var pengyouquan = dialog({
	    content: '<img src="<?php echo $this->_vars['QISHI']['site_domain'];  echo $this->_vars['QISHI']['site_dir']; ?>
plus/url_qrcode.php?url=<?php echo $this->_vars['w_url']; ?>
" alt="扫描二维码" width="80" height="80" />',
	    quickClose: true// 点击空白处快速关闭
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

<div class="page_location link_bk">当前位置：<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
">首页</a> > <a href="<?php echo $this->_vars['userindexurl']; ?>
">会员中心</a> > 职位管理</div>
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
				<div class="titleH1"><div class="h1-title">管理职位</div></div>
				<div class="navs link_bk">
					<a href="?act=<?php echo $this->_vars['act']; ?>
&jobtype=" <?php if ($_GET['jobtype'] == ""): ?>class="se"<?php endif; ?>>
					发布中职位<span >(<?php echo $this->_vars['jobs_total'][0]; ?>
)</span></a>
					<a href="?act=<?php echo $this->_vars['act']; ?>
&jobtype=2" <?php if ($_GET['jobtype'] == "2"): ?>class="se"<?php endif; ?>>
					审核中职位<span >(<?php echo $this->_vars['jobs_total'][2]; ?>
)</span></a>
					<a href="?act=<?php echo $this->_vars['act']; ?>
&jobtype=3" <?php if ($_GET['jobtype'] == "3"): ?>class="se"<?php endif; ?>>未显示职位<span class="check">(<?php echo $this->_vars['jobs_total'][3]; ?>
)</span></a>
					<a href="?act=<?php echo $this->_vars['act']; ?>
&jobtype=1" <?php if ($_GET['jobtype'] == "1"): ?>class="se"<?php endif; ?>>
					全部职位<span >(<?php echo $this->_vars['jobs_total'][1]; ?>
)</span></a>
					<div class="clear"></div>
				</div>
			</div>
			<?php if ($this->_vars['QISHI']['operation_mode'] == "2" || $this->_vars['QISHI']['operation_mode'] == "3"): ?>
			<div class="addjob-number">您的账户可以发布<span><?php echo $this->_vars['setmeal']['jobs_ordinary']; ?>
</span>个职位，现发布中<span><?php echo $this->_vars['total'][2]; ?>
</span>个职位。</div>
			<?php endif; ?>
		</div>
		<form id="form1" name="form1" method="post" action="?act=jobs_perform">
		<!-- 新的发布中职位 -->
		<?php if ($_GET['jobtype'] == ""): ?>
		<div class="company-data-list">
			<div class="c-data-top com-job-ma clearfix">
				<div class="item f-left check-item"><input type="checkbox" name="chkAll"   id="chk" title="全选/反选" /></div>
				<div class="item f-left top-item1">职位名称</div>
				<div class="item f-left top-item2">
					<div class="data-filter span4">
						<span class="filter-span"><?php if ($_GET['auto_refresh_cn'] == ''): ?>预约刷新<?php else:  echo $_GET['auto_refresh_cn'];  endif; ?><i class="filter-icon"></i></span>
						<ul class="filter-down">
							<li><a href="<?php echo $this->_run_modifier("auto_refresh:3,auto_refresh_cn:全部", 'qishi_parse_url', 'plugin', 1); ?>
">全部</a></li>
							<li><a href="<?php echo $this->_run_modifier("auto_refresh:1,auto_refresh_cn:已预约", 'qishi_parse_url', 'plugin', 1); ?>
">已预约</a></li>
							<li><a href="<?php echo $this->_run_modifier("auto_refresh:2,auto_refresh_cn:未预约", 'qishi_parse_url', 'plugin', 1); ?>
">未预约</a></li>
						</ul>
					</div>
				</div>
				<div class="item f-left top-item3">
					<div class="data-filter span4">
						<span class="filter-span"><?php if ($_GET['generalize_cn'] == ''): ?>推广状态<?php else:  echo $_GET['generalize_cn'];  endif; ?><i class="filter-icon"></i></span>
						<ul class="filter-down">  
							<li><a href="<?php echo $this->_run_modifier("generalize:,generalize_cn:全部", 'qishi_parse_url', 'plugin', 1); ?>
">全部</a></li>
							<li><a href="<?php echo $this->_run_modifier("generalize:stick,generalize_cn:置顶", 'qishi_parse_url', 'plugin', 1); ?>
">置顶</a></li>
							<li><a href="<?php echo $this->_run_modifier("generalize:highlight,generalize_cn:套色", 'qishi_parse_url', 'plugin', 1); ?>
">套色</a></li>
							<li><a href="<?php echo $this->_run_modifier("generalize:emergency,generalize_cn:紧急", 'qishi_parse_url', 'plugin', 1); ?>
">紧急</a></li>
							<li><a href="<?php echo $this->_run_modifier("generalize:recommend,generalize_cn:推荐", 'qishi_parse_url', 'plugin', 1); ?>
">推荐</a></li>
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
							<p>应聘简历：<?php echo $this->_vars['li']['countresume']; ?>
 | 更新时间：<?php echo $this->_run_modifier($this->_vars['li']['refreshtime'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M"); ?>
 <a  href="<?php if ($this->_vars['QISHI']['operation_mode'] == "1"): ?>?act=jobs_perform&refresh=1&y_id=<?php echo $this->_vars['li']['id'];  else: ?>javascript:;<?php endif; ?>" jobsid="<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline refresh">[刷新]</a></p>
							<div class="job-ma-ctrl">
								<a href="?act=editjobs&id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">修改</a>&nbsp;<a href="<?php echo $this->_run_modifier($this->_run_modifier("QS_resumelist,jobcategory:", 'cat', 'plugin', 1, $this->_vars['li']['jobcategory']), 'qishi_url', 'plugin', 1); ?>
" class="data-ctrl underline" target="_blank">匹配</a>&nbsp;<a url="?act=jobs_perform&display2=1&y_id=<?php echo $this->_vars['li']['id']; ?>
" href="javascript:;" class="data-ctrl underline ctrl-close">关闭</a>&nbsp;<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">删除</a>
								<a href="javascript:;" class="pengyouquan" id="jobs_<?php echo $this->_vars['li']['id']; ?>
">分享到朋友圈</a>
							</div>
						</div>
					</div>
					<?php if ($this->_vars['li']['auto_refresh'] == 0): ?>
					<div class="c-item f-left content2"><span class="hasyuyue">未预约</span><a href="javascript:;" class="check-yuyue data-ctrl underline appointmentSee" data="<?php echo $this->_vars['li']['jobs_name_']; ?>
,<?php echo $this->_vars['li']['id']; ?>
">[预约]</a></div>
					<?php else: ?>
					<div class="c-item f-left content2"><span class="hasyuyue">已预约</span><a href="javascript:;" class="check-yuyue data-ctrl underline appointmentShow" data="<?php echo $this->_vars['li']['id']; ?>
">[查看]</a></div>
					<?php endif; ?>
					<div class="c-item f-left content3">
						<?php if ($this->_vars['li']['stick'] != 1): ?>
							<a  href="javascript:void(0);" class="data-ctrl set_promotion" catid="3" jobid="<?php echo $this->_vars['li']['id']; ?>
">置顶</a>
						<?php else: ?>
							<a  href="javascript:void(0);" class="data-ctrl underline" style="color:#999" title="该职位已置顶">置顶</a>
						<?php endif; ?>
						<?php if ($this->_vars['li']['highlight'] == ""): ?>
						<a  href="javascript:void(0);" class="data-ctrl set_promotion" catid="4" jobid="<?php echo $this->_vars['li']['id']; ?>
">套色</a><br />
						<?php else: ?>
						<a  href="javascript:void(0);" class="data-ctrl underline" style="color:#999" title="该职位已套色">套色</a><br />
						<?php endif; ?>
						<?php if ($this->_vars['li']['emergency'] == "0"): ?>
						<a href="javascript:void(0);" class="data-ctrl set_promotion" catid="2" jobid="<?php echo $this->_vars['li']['id']; ?>
">紧急</a>
						<?php else: ?>
						<a  href="javascript:void(0);" class="data-ctrl underline" style="color:#999" title="该职位已紧急推广">紧急</a>
						<?php endif; ?>
						<?php if ($this->_vars['li']['recommend'] == "0"): ?>
						<a href="javascript:void(0);" class="data-ctrl set_promotion" catid="1" jobid="<?php echo $this->_vars['li']['id']; ?>
">推荐</a>
						<?php else: ?>
						<a  href="javascript:void(0);" class="data-ctrl underline" style="color:#999" title="该职位已紧急推荐">推荐</a>
 						<?php endif; ?><br>
                        <?php if ($this->_vars['li']['reward'] == "0"): ?>
                        <a href="javascript:void(0);" class="data-ctrl set_reward" catid="5" jobid="<?php echo $this->_vars['li']['id']; ?>
">悬赏</a>
                        <?php else: ?>
                        <a  href="javascript:void(0);" class="data-ctrl underline set_date" catid="5" jobid="<?php echo $this->_vars['li']['id']; ?>
" style="color:#999" title="该职位已悬赏">悬赏</a>
                    
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; endif; ?>
			<script type="text/javascript">
				/*查看预约*/
				$(".appointmentShow").live('click', function()
				{
					var id =$(this).attr('data');
					var myDialog = dialog();
				    jQuery.ajax({
				        url: 'company_ajax.php?act=auto_refresh&id='+id,
				        success: function (data) {
				            myDialog.content(data);
				            myDialog.title('预约详情');
				            myDialog.width('500');
				            myDialog.showModal();
				        }
				    });
				});
				$(".appointmentSee").die().live('click', function(event) {
					var appointDia =  dialog({
						title: '预约刷新',
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
						if (thepointAll < dayCount*thepointEvery) { // 本次消耗积分大于总积分不能预约
							$('input[name=toMakeAppointment]').removeClass('toMakeAppointment');
							$("#theNoEnouPoint").show();
						} else {
							$('input[name=toMakeAppointment]').addClass('toMakeAppointment');
							$("#theNoEnouPoint").hide();
						}
					});
					$(".toMakeAppointment").die().live('click', function(event) {
						if (!$("#aloneIntegralDays").val()) {
							alert("填写预约天数！");
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
							rightListHtm += '<div class="hasyue-item clearfix" reid="'+jobId+'"><div class="hasyue-job f-left">'+jobName+'</div><a href="javascript:;" class="notyue-btn f-left cancelAppoint" data="'+jobName+'">取消</a><div class="yue-time f-right"><input class="batchAppDays" type="text" /> 天</div></div>';
							$(".yue-right-block").append(rightListHtm);
							// 批量预约时计算消耗积分
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
							// 一键预约
							$(".aKeyAppoint").die().live('click', function(event) {
								if (!$(".yue-right-block .hasyue-item").length > 0) {
									alert("请先预约职位！");return false;
								};
								var isalertPoi = 0;
								$(".yue-right-block .batchAppDays").each(function(index, el) {
									isalertPoi += parseInt($(this).val());
								});
								if (!isalertPoi > 0) {
									alert("请填写预约天数！");return false;
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
								var oldYitObj = $(this).closest('.hasyue-item'), oldJobName = $(this).attr('data'), oldJobId = oldYitObj.attr('reid'), leftListHtm = '<div class="yue-title clearfix"><span class="f-left">可预约职位</span></div>';
								oldDataPool.push(oldJobId+','+oldJobName);
								oldYitObj.remove();
								$.each(oldDataPool, function(index, val) {
									var leftHtmArray = val.split(",");
									leftListHtm += '<div class="yue-item clearfix" reid="'+leftHtmArray[0]+'"><p class="yue-job f-left">'+leftHtmArray[1]+'</p><a href="javascript:;" class="yue-btn f-right appoint" data="'+leftHtmArray[1]+'">预约</a></div>';
								})
								$(".yue-left-block").html(leftListHtm);
							});
						});
					})
				});
			</script>
			<!-- 单个预约弹出 -->
			<div class="yuyue-one-dialog yuyue-dialog" style="display:none">
				<?php if ($this->_vars['add_mode'] == 1): ?>
				<div class="short-text-tip" style="margin-left:0">您的账户剩余 <span id="consumptionPoint"><?php echo $this->_vars['user_points']; ?>
</span> 分，本次预约需消耗 <span id="theConsumptionPoint">0</span> 分！</div>
				<div class="yo-block">
					<div class="yue-one-item">
						<span class="yo-type">预约职位：</span><font id="appointJobName" jobid=""></font><a href="javascript:;" class="underline batchAppoint">批量预约职位</a>
					</div>
					<div class="yue-one-item">
						<span class="yo-type">刷新次数：</span>1次/天
					</div>
					<div class="yue-one-item">
						<span class="yo-type">预约天数：</span><input id="aloneIntegralDays" type="text" /> 天
					</div>
					<div class="yue-one-item">
						<span class="yo-type">消耗积分：</span><em id="aloneIntegral">0</em> 分<span class="use-fen">（每天消耗<em id="everyDayConsumptionPoint"><?php echo $this->_vars['points_rule']['job_auto_refresh']['value']; ?>
</em>积分）</span>
					</div>
				</div>
				<div class="center-btn-wrap">
					<input type="button" value="预约" name="toMakeAppointment" class="btn-65-30blue btn-big-font toMakeAppointment" /><input type="button" value="取消" class="btn-65-30grey btn-big-font DialogClose" />
				</div>
				<?php else: ?>
				<div>套餐模式下不能使用预约刷新！</div>
				<?php endif; ?>
			</div>
			<!-- 单个预约弹出 end-->
			<!-- 批量预约弹出 -->
			<div class="yuyue-all-dialog" style="display:none">
				<?php if ($this->_vars['add_mode'] == 1): ?>
				<div class="short-text-tip" style="margin-left:0">您的账户剩余 <span id="bAlPiontLast"><?php echo $this->_vars['user_points']; ?>
</span> 分，本次预约需消耗 <span id="bAlPiont">0</span> 分！<span style="display:none" id="noEnouPoint">积分不足，</span><a href="company_service.php?act=order_add" class="underline">立即充值</a></div>
				<div class="yuyue-d clearfix">
					<div class="yuyue-left f-left">
						<div class="yue-left-block">
							<div class="yue-title clearfix"><span class="f-left">可预约职位</span></div>
							<?php if (count((array)$this->_vars['jobs_refresh'])): foreach ((array)$this->_vars['jobs_refresh'] as $this->_vars['yy_li']): ?>
							<div class="yue-item clearfix" reid="<?php echo $this->_vars['yy_li']['id']; ?>
">
								<p class="yue-job f-left"><?php echo $this->_vars['yy_li']['jobs_name']; ?>
</p>
								<a href="javascript:;" class="yue-btn f-right appoint" data="<?php echo $this->_vars['yy_li']['jobs_name']; ?>
">预约</a>
							</div>
							<?php endforeach; endif; ?>
						</div>
					</div>
					<div class="yuyue-right f-left">
						<div class="yue-right-block">
							<div class="yue-title clearfix"><span class="f-left">已选预约职位</span><span class="f-right" style="width:60px">预约天数</span></div>
						</div>
					</div>
				</div>
				<div class="yuyue-d-tip">每个职位每天刷新 <span>1</span> 次，消耗 <span id="bacMoreTime"><?php echo $this->_vars['points_rule']['job_auto_refresh']['value']; ?>
</span> 积分。</div>
				<div class="center-btn-wrap">
					<input type="button" name="aKeyAppoint" value="一键预约" class="btn-75-30blue btn-big-font aKeyAppoint" /><input type="button" value="取消" class="btn-65-30grey btn-big-font DialogClose" />
				</div>
				<?php else: ?>
				<div>套餐模式下不能使用预约刷新！</div>
				<?php endif; ?>
			</div>
			<!-- 批量预约弹出 end-->
			<div class="c-data-row last">
				<div class="c-data-content apply_jobs clearfix">
					<div class="c-item f-left check-item"><input type="checkbox" name="chkAll"   id="chk2" title="全选/反选" /></div>
					<div class="data-last-btn f-left">
						<?php if ($this->_vars['QISHI']['operation_mode'] == "3"): ?>
						<input type="hidden" name="refresh" id="refresh" value="1" />
						<input type="button" name="refresh" class="btn-65-30blue"  value="刷新职位"  id="refresh_all"/>
						<?php else: ?>
						<input type="hidden" name="refresh" id="refresh" value="1" />
						<input type="button" name="refresh" class="btn-65-30blue"  value="刷新职位"  id="refresh_all"/>
						<?php endif; ?>
						<input type="submit" value="关闭" name="display2" class="btn-65-30blue" id="display2"/>
						<input type="button" name="delete" class="btn-65-30blue ctrl-del" value="删除" id="delete" act="?act=jobs_perform&delete=1"/>
					</div>
				</div>
			</div>
            <div style="color:red;font-size:15px; padding-left: 10px;"><span>在推广期的简历，不可以修改及关闭，删除操作</span></div>
			<?php else: ?>
			<div class="c-data-row emptytip">没有找到对应的职位信息</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<!-- 审核中的 职位 -->
		<?php if ($_GET['jobtype'] == "2"): ?>
		<div class="company-data-list">
			<div class="c-data-top com-job-ma clearfix">
				<div class="item f-left check-item"><input type="checkbox" name="chkAll"   id="chk"/></div>
				<div class="item f-left top-item1">职位名称</div>
				<div class="item f-left top-item2">工作地区</div>
				<div class="item f-left top-item3" style="text-align: center;">操作</div>
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
							<p>应聘简历：<?php echo $this->_vars['li']['countresume']; ?>
 | 更新时间：<?php echo $this->_run_modifier($this->_vars['li']['refreshtime'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M"); ?>
</p>
						</div>
					</div>
					<div class="c-item f-left content2" style="width:218px;"><?php echo $this->_vars['li']['district_cn']; ?>
</div>
					<div class="c-item f-left content3">
						<a href="?act=editjobs&id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">修改</a>&nbsp;<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">删除</a>
					</div>
				</div>
			</div>
			<?php endforeach; endif; ?>
			<div class="c-data-row last">
				<div class="c-data-content apply_jobs clearfix">
					<div class="c-item f-left check-item"><input type="checkbox" name="chkAll"   id="chk2"/></div>
					<div class="data-last-btn f-left">
						<input type="button" name="delete" class="btn-65-30blue ctrl-del" value="删除" id="delete" act="?act=jobs_perform&delete=1"/>
					</div>
				</div>
			</div>
			<?php else: ?>
			<div class="c-data-row emptytip">没有找到对应的职位信息</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<!-- 未显示 职位 -->
		<?php if ($_GET['jobtype'] == "3"): ?>
		<div class="company-data-list">
			<div class="c-data-top com-job-ma clearfix">
				<div class="item f-left check-item"><input type="checkbox" name="chkAll"   id="chk"/></div>
				<div class="item f-left top-item1">职位名称</div>
				<div class="item f-left top-item2">
					<div class="data-filter span4">
						<span class="filter-span">职位状态<i class="filter-icon"></i></span>
						<ul class="filter-down">
							<li><a href="<?php echo $this->_run_modifier("state:0", 'qishi_parse_url', 'plugin', 1); ?>
">全部</a></li>
							<li><a href="<?php echo $this->_run_modifier("state:1", 'qishi_parse_url', 'plugin', 1); ?>
">未通过</a></li>
							<li><a href="<?php echo $this->_run_modifier("state:2", 'qishi_parse_url', 'plugin', 1); ?>
">已关闭</a></li>
						</ul>
					</div>
				</div>
				<div class="item f-left top-item3" style="text-align: center;">操作</div>
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
							<p>应聘简历：<?php echo $this->_vars['li']['countresume']; ?>
 | 更新时间：<?php echo $this->_run_modifier($this->_vars['li']['refreshtime'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M"); ?>
</p>
						</div>
					</div>
					<div class="c-item f-left content2" style="width:218px;"><?php echo $this->_vars['li']['status_cn']; ?>
</div>
					<div class="c-item f-left content3">
						<?php if ($this->_vars['li']['status'] == 2): ?>
						<a href="?act=editjobs&id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">修改</a>&nbsp;<a href="?act=jobs_perform&display1=1&y_id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">恢复</a>&nbsp;<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">删除</a>
						<?php else: ?>
						<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">删除</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; endif; ?>
			<div class="c-data-row last">
				<div class="c-data-content apply_jobs clearfix">
					<div class="c-item f-left check-item"><input type="checkbox" name="chkAll"   id="chk2"/></div>
					<div class="data-last-btn f-left">
						<input type="button" name="delete" class="btn-65-30blue ctrl-del" value="删除" id="delete" act="?act=jobs_perform&delete=1"/>
					</div>
				</div>
			</div>
			<?php else: ?>
			<div class="c-data-row emptytip">没有找到对应的职位信息</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<!-- 全部职位 职位 -->
		<?php if ($_GET['jobtype'] == "1"): ?>
		<div class="company-data-list">
			<div class="c-data-top com-job-ma clearfix">
				<div class="item f-left check-item"><input type="checkbox" id="chk" name="chkAll"></div>
				<div class="item f-left top-item1">职位名称</div>
				<div class="item f-left top-item2">
					<div class="data-filter span4">
						<span class="filter-span">职位状态<i class="filter-icon"></i></span>
						<ul class="filter-down">
							<li><a href="<?php echo $this->_run_modifier("state:0", 'qishi_parse_url', 'plugin', 1); ?>
">全部</a></li>
							<li><a href="<?php echo $this->_run_modifier("state:1", 'qishi_parse_url', 'plugin', 1); ?>
">发布中</a></li>
							<li><a href="<?php echo $this->_run_modifier("state:2", 'qishi_parse_url', 'plugin', 1); ?>
">审核中</a></li>
							<li><a href="<?php echo $this->_run_modifier("state:3", 'qishi_parse_url', 'plugin', 1); ?>
">未通过</a></li>
							<li><a href="<?php echo $this->_run_modifier("state:4", 'qishi_parse_url', 'plugin', 1); ?>
">已关闭</a></li>
						</ul>
					</div>
				</div>
				<div class="item f-left top-item3" style="text-align: center;">操作</div>
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
							<p>应聘简历：<?php echo $this->_vars['li']['countresume']; ?>
 | 更新时间：<?php echo $this->_run_modifier($this->_vars['li']['refreshtime'], 'date_format', 'plugin', 1, "%Y-%m-%d %H:%M");  if ($this->_vars['li']['status'] == 1): ?>  <a  href="<?php if ($this->_vars['QISHI']['operation_mode'] == "1"): ?>?act=jobs_perform&refresh=1&y_id=<?php echo $this->_vars['li']['id'];  else: ?>javascript:;<?php endif; ?>" jobsid="<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline refresh">[刷新]</a><?php endif; ?></p>
						</div>
					</div>
					<div class="c-item f-left content2" style="width:218px;"><span style="<?php if ($this->_vars['li']['status'] == 2 || $this->_vars['li']['status'] == 4): ?>color:red;<?php elseif ($this->_vars['li']['status'] == 3): ?>color:#FFB443;<?php else:  endif; ?>"><?php echo $this->_vars['li']['status_cn']; ?>
</span></div>
					<div class="c-item f-left content3">
						<?php if ($this->_vars['li']['status'] == 1): ?>
						<a href="?act=editjobs&id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">修改</a>&nbsp;<a href="?act=jobs_perform&display2=1&y_id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">关闭</a>&nbsp;<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">删除</a>
						<?php elseif ($this->_vars['li']['status'] == 2): ?>
						<a href="?act=editjobs&id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">修改</a>&nbsp;<a href="?act=jobs_perform&display1=1&y_id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">恢复</a>&nbsp;<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">删除</a>
						<?php elseif ($this->_vars['li']['status'] == 3): ?>
						<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">删除</a>
						<?php else: ?>
						<a href="?act=editjobs&id=<?php echo $this->_vars['li']['id']; ?>
" class="data-ctrl underline">修改</a>&nbsp;<a href="javascript:;" class="data-ctrl underline ctrl-del" url="?act=jobs_perform&delete=1&y_id=<?php echo $this->_vars['li']['id']; ?>
">删除</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; endif; ?>
			<div class="c-data-row last">
				<div class="c-data-content apply_jobs clearfix">
					<div class="c-item f-left check-item"><input type="checkbox" name="chkAll"   id="chk2" title="全选/反选" /></div>
					<div class="data-last-btn f-left">
						<input type="button" name="delete" class="btn-65-30blue ctrl-del" value="删除" id="delete" act="?act=jobs_perform&delete=1"/>
					</div>
				</div>
			</div>
			<?php else: ?>
			<div class="c-data-row emptytip">没有找到对应的职位信息</div>
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
<!-- 发布职位成功弹出 -->
<div class="addjob-success-dialog" style="display:none">
	<div class="success-tip">职 位 发 布 成 功<a href="?act=addjobs" class="underline">继续发布职位</a></div>
	<p class="sns-tips">让企业品牌更红，让招聘效率更高，<span>立即分享转发招聘职位吧！</span></p>
	<style>.bdshare-button-style1-16 a, .bdshare-button-style1-16 .bds_more{padding-left: 0;}</style>
	<div class="bdsharebuttonbox bdshare sns-block share clearfix">
		<a href="#" class="sns-icon sina f-left " title="分享到新浪微博" data-cmd="tsina"></a>
		<a href="#" class="sns-icon renren f-left" title="分享到人人网" data-cmd="renren"></a>
		<a href="#" class="sns-icon qzone f-left" title="分享到QQ空间" data-cmd="qzone"></a>
		<a href="#" class="sns-icon t-weibo f-left" title="分享到腾讯微博"data-cmd="tqq" ></a>
		<a href="#" class="sns-icon qq f-left" title="分享到QQ好友" data-cmd="sqq"></a>
		<a href="javascript:;" class="sns-icon weixin f-left" title="分享到微信"></a>
		<div class="weixin-code-box f-left">
			<div class="weixin-border">
				<img src="<?php echo $this->_vars['QISHI']['site_domain'];  echo $this->_vars['QISHI']['site_dir']; ?>
plus/url_qrcode.php?url=<?php echo $this->_vars['w_url']; ?>
" alt="二维码" width="70" height="70" />
			</div>
				<span>微信扫一扫</span>
			</div>
		<script>window._bd_share_config={"common":{"bdText":"<?php echo $this->_vars['jobs_one']['companyname']; ?>
在@<?php echo $this->_vars['QISHI']['site_name']; ?>
  发布了最新职位 : <?php echo $this->_vars['jobs_one']['jobs_name']; ?>
(月薪 : <?php echo $this->_vars['jobs_one']['wage_cn']; ?>
)","bdDesc":"想了解更多最新职位简历资讯请关注<?php echo $this->_vars['QISHI']['site_name']; ?>
","bdUrl":"<?php echo $this->_vars['jobs_one']['jobs_url']; ?>
","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"1","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];
		</script>
	</div>
	<p class="succsee-more">为了吸引更多求职者的关注，建议您：<a href="javascript:;" class="underline set_promotion" catid="3" jobid="<?php echo $this->_vars['jobs_one']['id']; ?>
">置顶职位</a>&nbsp;<a href="javascript:;" data="<?php echo $this->_vars['jobs_one']['jobs_name']; ?>
,<?php echo $this->_vars['jobs_one']['id']; ?>
" class="underline appointmentSee">职位预约刷新</a></p>
</div>
</body>
</html>