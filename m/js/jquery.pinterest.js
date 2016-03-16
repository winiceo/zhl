 /*
 * 无限加载插件
*/
$(function(){
	fnLoad();
	$('.thisurl').on('click', function(event) {
		fnUnload();
		window.location.href =  $(this).attr('url');
	});
}); 
function pinterest(url) {
	/* 滚动的时候绑定事件 */
	var isGet = true;
	window.onscroll = function() {
		/* 判断有无管理选项 */
		if ($("#operateaction").length > 0) {
			if (!$("#operateaction").hasClass("c")) {
				/* 当滚动的高度大于临界点的高度的时候开始加载更多数据 */
				if (scrollside()) {
					$('.loadinglist').show(); /* 正在加载显示 */
					$('.remindnoinfo').hide(); /*  没有更多职位的提示框消失 */
					/* 获得目前容器中列表的个数，从这个位置开始再获取N个 */
					var offset = $("#container .box").size(),
						rows = 5;
					if (isGet) {
						isGet = false;
						$.get(url, {"offset":offset, "rows":rows}, function(data) {
							if(data == "-1"){ /* -1代表没有数据 */
								$('.loadinglist').hide(); /* 正在加载隐藏 */
						    	$('.remindnoinfo').show(); /* 没有更多职位的提示框显示 */
						    } else {
						    	$(data).appendTo($('#container')); /* 返回的数据添加到容器中 */
								$('.loadinglist').hide(); /* 如果返回数据正在加载隐藏 */
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
			/* 当滚动的高度大于临界点的高度的时候开始加载更多数据 */
			if (scrollside()) {
				$('.loadinglist').show(); /* 正在加载显示 */
				$('.remindnoinfo').hide(); /*  没有更多职位的提示框消失 */
				/* 获得目前容器中列表的个数，从这个位置开始再获取N个 */
				var offset = $("#container .box").size(),
					rows = 5;
				if (isGet) {
					isGet = false;
					$.get(url, {"offset":offset, "rows":rows}, function(data) {
						if(data == "-1"){ /* -1代表没有数据 */
							$('.loadinglist').hide(); /* 正在加载隐藏 */
					    	$('.remindnoinfo').show(); /* 没有更多职位的提示框显示 */
					    } else {
					    	$(data).appendTo($('#container')); /* 返回的数据添加到容器中 */
							$('.loadinglist').hide(); /* 如果返回数据正在加载隐藏 */
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

/* 判断是否滚动到临界点 */
function scrollside() {
	var box = $(".box");
	/* 定位临界点为最后一个box一半的位置 */
	var lastboxHeight = box.last().get(0).offsetTop + Math.floor(box.last().height()/2); 
	var documentHeight = $(window).height();
	var scrollHeight = $(window).scrollTop();
	return (lastboxHeight < documentHeight+scrollHeight) ? true : false;
}

/* 关键字搜索 */
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