//ȫѡ��ѡ
$("input[name='selectall']").unbind().click(function(){$("#infolists :checkbox").attr('checked',$(this).attr('checked'))});
// ����ְλ
function apply_hunter_jobs(ajaxurl)
{
	$(".deliver").click(function()
	{
		var sltlength='';
		if($(".detail").is(":hidden")) 
		{
			sltlength=$("#infolists .merger_com_box input:checked").length;
		}
		else 
		{
			sltlength=$("#infolists .detail input:checked").length;
		}
		if (sltlength==0)
		{
			dialog("ϵͳ��ʾ","text:��ѡ��ְλ","300px","auto");
		}
		else
		{
			var jidArr=new Array();
			 $("#infolists .list :checkbox[checked]").each(function(index){jidArr[index]=$(this).val();});
			dialog("����ְλ","url:"+ajaxurl+"user/user_apply_hunter_jobs.php?id="+jidArr.join("-")+"&act=app","500px","auto","");
		}
	});
	//��������ְλ
	$(".app_jobs").unbind().click(function(){
	dialog("����ְλ","url:"+ajaxurl+"user/user_apply_hunter_jobs.php?id="+$(this).attr("id")+"&act=app","500px","auto","");
	});
}
// �ղ�ְλ
function favorites_hunter_jobs(ajaxurl)
{	
	$(".collecter").click(function()
	{
		var sltlength=$("#infolists input:checked").length;
		if ($("#infolists .list input:checked").length==0)
			{
				dialog("ϵͳ��ʾ","text:��ѡ��ְλ","300px","auto");
			}
			else
			{
				var jidArr=new Array();
				 $("#infolists .list :checkbox[checked]").each(function(index){jidArr[index]=$(this).val();});
				dialog("�ղ�ְλ","url:"+ajaxurl+"user/user_favorites_hunter_job.php?id="+jidArr.join("-")+"&act=add","500px","auto","");
			}
	});
	// �����ղ�ְλ
	$(".add_favorites").unbind().click(function(){
	dialog("�ղ�ְλ","url:"+ajaxurl+"user/user_favorites_hunter_job.php?id="+$(this).attr("id")+"&act=add","500px","auto","");
	});
}
// �����˲ſ�
/*function allfavorites(ajaxurl)
{
	$(".add_favoritesr").unbind().click(function(){	
	var url=ajaxurl+"user/user_favorites_resume.php?id="+$(this).attr("id")+"&act=add";
	dialog("�ղص��˲ſ�","url:"+url,"500px","auto","");	
	});	
	
	$(".allfavorites").click(function()
	{
		var sltlength=$("#infolists input:checked").length;
		if ($("#infolists .list input:checked").length==0)
			{
			dialog("ϵͳ��ʾ","text:��ѡ�����","300px","auto");
			}
			else
			{
				var jidArr=new Array();
				 $("#infolists .list :checkbox[checked]").each(function(index){jidArr[index]=$(this).val();});
				dialog("�����˲ſ�","url:"+ajaxurl+"user/user_favorites_resume.php?id="+jidArr.join("-")+"&act=add","500px","auto","");
			}
	});
}*/