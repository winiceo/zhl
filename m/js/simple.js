 /*
 * 74cms 触屏微商圈
*/
$(function(){
	$('.thisurl').click( function () {window.location.href =  $(this).attr('url');});
	/* 刷新 */
	$(".refresh").on('click', function(event) {
		var jobid = $(this).data("jobid");
		layer.prompt({
		    title: '请输口令，并确认',
		    formType: 1
		}, function(pass){
			$.get('simple-job-operation.php?act=refresh', {"pass":pass , "jobid":jobid}, function(data) {
				layer.msg(data);
			});
		});
	});

	/* 删除 */
	$(".delete").on('click', function(event) {
		var jobid = $(this).data("jobid");
		layer.prompt({
		    title: '请输口令，并确认',
		    formType: 1
		}, function(pass){
		   	$.get('simple-job-operation.php?act=delete', {"pass":pass , "jobid":jobid}, function(data) {
				layer.msg(data);
				window.history.go(-1);
			});
		});
	});
	/* 刷新 */
	$(".resume_refresh").on('click', function(event) {
		var resumeid = $(this).data("resumeid");
		layer.prompt({
		    title: '请输口令，并确认',
		    formType: 1
		}, function(pass){
			$.get('simple-resume-operation.php?act=resume_refresh', {"pass":pass , "resumeid":resumeid}, function(data) {
				layer.msg(data);
			});
		});
	});

	/* 删除 */
	$(".resume_delete").on('click', function(event) {
		var resumeid = $(this).data("resumeid");
		layer.prompt({
		    title: '请输口令，并确认',
		    formType: 1
		}, function(pass){
		   	$.get('simple-resume-operation.php?act=resume_delete', {"pass":pass , "resumeid":resumeid}, function(data) {
				layer.msg(data);
				window.history.go(-1);
			});
		});
	});
}); 