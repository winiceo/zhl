 /*
 * 74cms ����΢��Ȧ
*/
$(function(){
	$('.thisurl').click( function () {window.location.href =  $(this).attr('url');});
	/* ˢ�� */
	$(".refresh").on('click', function(event) {
		var jobid = $(this).data("jobid");
		layer.prompt({
		    title: '��������ȷ��',
		    formType: 1
		}, function(pass){
			$.get('simple-job-operation.php?act=refresh', {"pass":pass , "jobid":jobid}, function(data) {
				layer.msg(data);
			});
		});
	});

	/* ɾ�� */
	$(".delete").on('click', function(event) {
		var jobid = $(this).data("jobid");
		layer.prompt({
		    title: '��������ȷ��',
		    formType: 1
		}, function(pass){
		   	$.get('simple-job-operation.php?act=delete', {"pass":pass , "jobid":jobid}, function(data) {
				layer.msg(data);
				window.history.go(-1);
			});
		});
	});
	/* ˢ�� */
	$(".resume_refresh").on('click', function(event) {
		var resumeid = $(this).data("resumeid");
		layer.prompt({
		    title: '��������ȷ��',
		    formType: 1
		}, function(pass){
			$.get('simple-resume-operation.php?act=resume_refresh', {"pass":pass , "resumeid":resumeid}, function(data) {
				layer.msg(data);
			});
		});
	});

	/* ɾ�� */
	$(".resume_delete").on('click', function(event) {
		var resumeid = $(this).data("resumeid");
		layer.prompt({
		    title: '��������ȷ��',
		    formType: 1
		}, function(pass){
		   	$.get('simple-resume-operation.php?act=resume_delete', {"pass":pass , "resumeid":resumeid}, function(data) {
				layer.msg(data);
				window.history.go(-1);
			});
		});
	});
}); 