 /*
 * 74cms 触屏新闻导航
*/
$(function(){
	var itemArr = $(".hitem"),
		moreMenuHtm = '',
		lessMenuHtm = '',
		className = '';
	$("#selectedTag").val() ? className = 'selected' : className = 'noselected';
	lessMenuHtm += '<div class="' + className + ' items" data-url="news-list.php?type_id=' + $(".hitemFirst").data("code") + '">';
	lessMenuHtm += '<a class="loginTitle" href="javascript:;">' + $(".hitemFirst").data("name") + '</a>';
	lessMenuHtm += '</div>';
	moreMenuHtm += '<div class="' + className + ' items" data-url="news-list.php?type_id=' + $(".hitemFirst").data("code") + '">';
	moreMenuHtm += '<a class="loginTitle" href="javascript:;">' + $(".hitemFirst").data("name") + '</a>';
	moreMenuHtm += '</div>';
	$.each(itemArr, function(index, val) {
		if (index < 1) {
			lessMenuHtm += '<div class="noselected items" data-url="news-list.php?type_id=' + $(this).data("code") + '">';
			lessMenuHtm += '<a class="loginTitle" href="javascript:;">' + $(this).data("name") + '</a>';
			lessMenuHtm += '</div>';
		};
		if (index == 1) {
			moreMenuHtm += '<div class="noselected" id="showLess"><a class="loginTitle" href="javascript:;">收起<i class="w-icon w-icon-up"></i></a><div class="verticalbar"></div></div>';
		};
		moreMenuHtm += '<div class="noselected items" data-url="news-list.php?type_id=' + $(this).data("code") + '">';
		moreMenuHtm += '<a class="loginTitle" href="javascript:;">' + $(this).data("name") + '</a>';
		moreMenuHtm += '</div>';
		$("#lessItemBox").html(lessMenuHtm);
		$("#moreItemBox").html(moreMenuHtm);
	});
	$(".items").on('click', function(event) {
		var key = $("#key").val(),
			url = $(this).data("url");
		window.location.href = url + "&key=" + key;
	});
	$(".itemsHot").on('click', function(event) {
		var url = $(this).data("url");
		window.location.href = url;
	});
	/* 显示更多导航 */
	$("#showMore").on('click', function(event) {
		$("#less_menu").hide();
	    $("#more_menu").show();
	    $(".itemsHot").css('margin-bottom','0');
	});
	/* 收起更多导航 */
	$("#showLess").on('click', function(event) {
		$("#less_menu").show();
	    $("#more_menu").hide();
	    $(".itemsHot").css('margin-bottom','3%');
	});
}); 