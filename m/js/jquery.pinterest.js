 /*
 * ���޼��ز��
*/
$(function(){
	fnLoad();
	$('.thisurl').on('click', function(event) {
		fnUnload();
		window.location.href =  $(this).attr('url');
	});
}); 
function pinterest(url) {
	/* ������ʱ����¼� */
	var isGet = true;
	window.onscroll = function() {
		/* �ж����޹���ѡ�� */
		if ($("#operateaction").length > 0) {
			if (!$("#operateaction").hasClass("c")) {
				/* �������ĸ߶ȴ����ٽ��ĸ߶ȵ�ʱ��ʼ���ظ������� */
				if (scrollside()) {
					$('.loadinglist').show(); /* ���ڼ�����ʾ */
					$('.remindnoinfo').hide(); /*  û�и���ְλ����ʾ����ʧ */
					/* ���Ŀǰ�������б�ĸ����������λ�ÿ�ʼ�ٻ�ȡN�� */
					var offset = $("#container .box").size(),
						rows = 5;
					if (isGet) {
						isGet = false;
						$.get(url, {"offset":offset, "rows":rows}, function(data) {
							if(data == "-1"){ /* -1����û������ */
								$('.loadinglist').hide(); /* ���ڼ������� */
						    	$('.remindnoinfo').show(); /* û�и���ְλ����ʾ����ʾ */
						    } else {
						    	$(data).appendTo($('#container')); /* ���ص�������ӵ������� */
								$('.loadinglist').hide(); /* ��������������ڼ������� */
								$('.thisurl').on('click', function(event) {
									window.location.href =  $(this).attr('url');
								});
						    }
						    isGet = true;
						});
					};
				};
			};
		} else {
			/* �������ĸ߶ȴ����ٽ��ĸ߶ȵ�ʱ��ʼ���ظ������� */
			if (scrollside()) {
				$('.loadinglist').show(); /* ���ڼ�����ʾ */
				$('.remindnoinfo').hide(); /*  û�и���ְλ����ʾ����ʧ */
				/* ���Ŀǰ�������б�ĸ����������λ�ÿ�ʼ�ٻ�ȡN�� */
				var offset = $("#container .box").size(),
					rows = 5;
				if (isGet) {
					isGet = false;
					$.get(url, {"offset":offset, "rows":rows}, function(data) {
						if(data == "-1"){ /* -1����û������ */
							$('.loadinglist').hide(); /* ���ڼ������� */
					    	$('.remindnoinfo').show(); /* û�и���ְλ����ʾ����ʾ */
					    } else {
					    	$(data).appendTo($('#container')); /* ���ص�������ӵ������� */
							$('.loadinglist').hide(); /* ��������������ڼ������� */
							$('.thisurl').on('click', function(event) {
								fnUnload();
								window.location.href =  $(this).attr('url');
							});
					    }
					    isGet = true;
					});
				};
			};
		};
	}
}

/* �ж��Ƿ�������ٽ�� */
function scrollside() {
	var box = $(".box");
	/* ��λ�ٽ��Ϊ���һ��boxһ���λ�� */
	var lastboxHeight = box.last().get(0).offsetTop + Math.floor(box.last().height()/2); 
	var documentHeight = $(window).height();
	var scrollHeight = $(window).scrollTop();
	return (lastboxHeight < documentHeight+scrollHeight) ? true : false;
}

/* �ؼ������� */
$(function(){
	$(".w-icon-search-news").on('click', function(event) {
		window.location.href = "?key=" + $("#key").val() +"&type_id=" + $("#selectedTag").val();
	});
});

function getCookieName(name)
{
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
 
    if(arr=document.cookie.match(reg))
 
        return (arr[2]);
    else
        return null;
}
function ClearCookie(name) {  
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookieName(name);
    if(cval!=null){
    	document.cookie= name + "="+cval+"; expires="+exp.toGMTString();  
    }
} 
function SetCookie(cname, cvalue, exdays)
{
	var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
function GetCookie(sName)
{
	var aCookie = document.cookie.split(";");
	for (var i=0; i < aCookie.length; i++){
		var aCrumb = aCookie[i].split("=");
		if ($.trim(sName) == $.trim(aCrumb[0])) {
			return unescape(aCrumb[1]);
		};
	}
	return null;
}

function fnLoad()
{	
	document.body.scrollTop = GetCookie("scrollTop"+location.pathname);
	ClearCookie("scrollTop"+location.pathname);
}

function fnUnload()
{
	SetCookie("scrollTop"+location.pathname, document.body.scrollTop, 1);
}