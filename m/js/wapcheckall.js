function wapcheckall(saveurl) {
    // �󶨹����¼�
	$("#operateaction").click(function(event) {
		if ($(this).hasClass("c")) {
			$(this).text('����');
			$(".classifylist .title .name, .classifylist .txt .left, .classifylist .txt .right").removeClass('perc');
			$(".classifylist .chk input[type=checkbox]:checked").each(function(index, el) {
				$(this).trigger("click");
			});
		} else {
			$(this).text('ȡ��');
			$(".classifylist .title .name, .classifylist .txt .left, .classifylist .txt .right").addClass('perc');
		};
		$(this).toggleClass('c');
		$('.chkabaout').toggle();
	});
	// label�������Ӧ��input
	$(".classifylist .chk .check-box").each(function(index, el) {
		$(this).prev().attr("id", index+ "cbchk");
		$(this).attr("for",$(this).prev().attr("id"));
	});
	// ȫѡ
	$("#opecheckall").on('click', function(event) {
		var inputchkedlength = $(".classifylist .chk input[type=checkbox]:checked").length,
			listlength = $(".classifylist").length;
		if (inputchkedlength == listlength) {
			$(".classifylist .chk label").each(function(index, el) {
				$(this).trigger("click");
			});
		} else {
			$(".classifylist .chk input[type=checkbox]").not(':checked').each(function(index, el) {
				$(this).trigger("click");
			});
		};
	});
	// ɾ��
	$("#opecheckdelete").on('click', function(event) {
		var idArr = new Array();
		$(".classifylist .chk input[type=checkbox]:checked").each(function(index, el) {
			idArr[index] = $(this).attr("id[]");
		});
		$.post(saveurl, {id: idArr}, function(data) {
			alert(data);
		});
	});
};