
// ����ְλ
function apply_jobs(ajaxurl)
{
	//ȫѡ��ѡ
	$("#selectall").click(function(){
		$("#school-data :checkbox").not('#merge_com_btn').attr('checked',$("#selectall").is(':checked'))
	});
	$(".deliver").click(function()
	{
		var sltlength='';
		if(!$(".detail").length)
		{
			sltlength=$("#school-data .m_jobname_box input:checked").length;
		}
		else 
		{
			sltlength=$("#school-data .resume-item input:checked").length;
		}
		if (sltlength==0)
		{	
			dialog({
				title: 'ϵͳ��ʾ',
			    content: "��ѡ��ְλ",
			    width:300
			}).showModal();
		}
		else
		{
			var jidArr=new Array();
			if(!$(".detail").length)
			{
				$("#school-data .m_jobname_box input:checked").each(function(index){jidArr[index]=$(this).val();});
			}
			else 
			{
				$("#school-data .resume-item input:checked").each(function(index){jidArr[index]=$(this).val();});
			}
			var myDialog = dialog();
			jQuery.ajax({
			    url: ajaxurl+"user/user_apply_jobs.php?id="+jidArr.join("-")+"&act=app",
			    success: function (data) {
			        myDialog.content(data);
			        myDialog.title('����ְλ');
			        myDialog.width('500');
			    	myDialog.showModal();
			    	/* �ر� */
					$(".DialogClose").live('click',function() {
						myDialog.close().remove();
					});
			    }
			});
		}
	});
	//��������ְλ
	$(".invite-btn").unbind().click(function(){
		var myDialog = dialog();
		jQuery.ajax({
		    url: ""+ajaxurl+"user/user_apply_jobs.php?id="+$(this).attr("id")+"&act=app",
		    success: function (data) {
		        myDialog.content(data);
		        myDialog.title('����ְλ');
		        myDialog.width('500');
		    	myDialog.showModal();
		    	/* �ر� */
				$(".DialogClose").live('click',function() {
					myDialog.close().remove();
				});
		    }
		});
	});
}
// �ղ�ְλ
function favorites(ajaxurl)
{	
	$(".collecter").click(function()
	{
		var sltlength='';
		if(!$(".detail").length)
		{
			sltlength=$("#school-data .m_jobname_box input:checked").length;
		}
		else 
		{
			sltlength=$("#school-data .resume-item input:checked").length;
		}
		if (sltlength==0)
		{
			dialog({
				title: 'ϵͳ��ʾ',
			    content: "��ѡ��ְλ",
			    width:300
			}).showModal();
		}
		else
		{
			var jidArr=new Array();
			if(!$(".detail").length)
			{
				$("#school-data .m_jobname_box input:checked").each(function(index){jidArr[index]=$(this).val();});
			}
			else 
			{
				$("#school-data .resume-item input:checked").each(function(index){jidArr[index]=$(this).val();});
			}
			var myDialog = dialog();
			jQuery.ajax({
			    url: ajaxurl+"user/user_favorites_job.php?id="+jidArr.join("-")+"&act=add",
			    success: function (data) {
			        myDialog.content(data);
			        myDialog.title('�ղ�ְλ');
			        myDialog.width('500');
			    	myDialog.showModal();
			    	/* �ر� */
					$(".DialogClose").live('click',function() {
						myDialog.close().remove();
					});
			    }
			});
		}
	});
	// �����ղ�ְλ
	$(".collect").unbind().click(function(){
		var myDialog = dialog();
		jQuery.ajax({
		    url: ""+ajaxurl+"user/user_favorites_job.php?id="+$(this).attr("id")+"&act=add",
		    success: function (data) {
		        myDialog.content(data);
		        myDialog.title('�ղ�ְλ');
		        myDialog.width('500');
		    	myDialog.showModal();
		    	/* �ر� */
				$(".DialogClose").live('click',function() {
					myDialog.close().remove();
				});
		    }
		});
	});
}