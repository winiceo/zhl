function allaround(dir){
	if($("#divCityCate").length > 0) {
		fillCity("#divCityCate"); // �������
		// �ָ�����ѡ������
		if($("#sdistrict").val()) {
			var scityid = $("#sdistrict").val();
			if(scityid == 0) {
				var dcityid = $("#district").val();
				$("#divCityCate .citycatebox p a").each(function() {
					if(dcityid == $(this).attr("rcoid")) {
						$(this).addClass('selectedcolor');
					}
				});
			} else {
				$("#divCityCate .citycatebox .subcate a").each(function() {
					if(scityid == $(this).attr("rcoid")) {
						$(this).parent().prev().find('font a').addClass('selectedcolor');
						$(this).addClass('selectedcolor');
					}
				});
			}
		}
		/* ���������ʾ����ѡ */
		$("#divCityCate li p a").unbind().live('click', function(){
			$("#divCityCate li p a").each(function() {
				$(this).removeClass('selectedcolor');
			});
			$(this).addClass('selectedcolor');
			var checkID = $(this).attr('pid').split(".");
			var checkText = $(this).attr('title');
			$("#cityText").html(checkText);
			$("#district_cn").val(checkText);
			$("#district").val(checkID[0]);
			$("#sdistrict").val(checkID[1]);
			$("#divCityCate").hide();
		});
		$("#divCityCate .subcate a").unbind().live('click', function() {		
			$("#divCityCate .subcate a").each(function() {
				$(this).parent().prev().find('font a').removeClass('selectedcolor');
				$(this).removeClass('selectedcolor');
			});
			$(this).parent().prev().find('font a').addClass('selectedcolor');
			$(this).addClass('selectedcolor');
			var checkID = $(this).attr('pid').split(".");
			var checkText = $(this).attr('title');
			$("#cityText").html(checkText);
			$("#district_cn").val(checkText);
			$("#district").val(checkID[0]);
			$("#sdistrict").val(checkID[1]);
			$("#divCityCate").hide();
		});
	}
	if($("#divCityCateJob").length > 0) {
		fillCityJob("#divCityCateJob"); // �ó�ְ�����
		// �ָ��ó�ְ��ѡ������
		if($("#intention_jobs_id").val()) {
				var recoverCityArray = $("#intention_jobs_id").val().split(",");
				$.each(recoverCityArray, function(index, val) {
					 var democityArray = val.split(".");
					 if(democityArray[1] == 0) { // ����ڶ�������Ϊ 0 ˵��ѡ�����һ��
					 	$(".citycatebox p a").each(function() {
					 		if(democityArray[0] == $(this).attr("rcoid")) {
					 			$(this).addClass('selectedcolor');
					 		}
					 	});
					 } else { // ѡ����Ƕ���
					 	$(".citycatebox .subcate a").each(function() {
					 		if(democityArray[1] == $(this).attr("rcoid")) {
					 			$(this).addClass('selectedcolor');
					 		}
					 	});
					 }
				});
				copyCityJobItem();
		}
		/* �ó�ְ�ܵ����ʾ����ѡ */
		$("#divCityCateJob p a").unbind().live('click', function() {
			// �ж�ѡ��������Ƿ񳬳�
			if($("#divCityCateJob .selectedcolor").length >= 5) {
				$("#citydropcontent").show(0).delay(3000).fadeOut("slow");
			} else {
				$(this).addClass('selectedcolor');
				copyCityJobItem(); // ���ó�ְ����ѡ�Ŀ���
			}
		});
		$("#divCityCateJob .subcate a").unbind().live('click', function() {
			// �ж�ѡ��������Ƿ񳬳�
			if($("#divCityCateJob .selectedcolor").length >= 5) {
				$("#citydropcontent").show(0).delay(3000).fadeOut("slow");
			} else {
				if($(this).attr("p") == "qb") {
					$(this).parent().prev().find('font a').addClass('selectedcolor');
					$(this).parent().find('a').removeClass('selectedcolor');
				} else {
					$(this).parent().prev().find('font a').removeClass('selectedcolor');
					$(this).addClass('selectedcolor');
				}
				copyCityJobItem(); // ���ó�ְ����ѡ�Ŀ���
			}
		});
		// �ó�ְ��ȷ��ѡ��
		$("#citySure").unbind().click(function() {
			var a_cn=new Array();
			var a_id=new Array();
			$("#cityAcq a").each(function(index) {
				// ���ѡ�����һ�����ڶ��������� 0
				var chid = new Array();
				if($(this).attr('pid')) {
					chid = $(this).attr('pid').split(".");
					if(chid.length < 2) {
						chid.push(0);
					}
				}
				var checkID = chid.join(".");
				var checkText = $(this).attr('title');
				a_id[index]=checkID;
				a_cn[index]=checkText;
			});
			if (a_cn.length > 0) {
				$("#cityTextJob").html(a_cn.join(","));
				$("#intention_jobs").val(a_cn.join(","));
				$("#intention_jobs_id").val(a_id.join(","));
			} else {
				$("#cityTextJob").html("��ѡ���������");
				$("#intention_jobs").val("");
				$("#intention_jobs_id").val("");
			}
			$("#divCityCateJob").hide();
		});
	}
	if($("#divTradCate").length > 0) {
		fillTrad("#divTradCate"); // ��ҵ�������
		// �ָ���ҵѡ������
		if($("#trade").val()) {
			var recoverTradArray = $("#trade").val().split(",");
			$.each(recoverTradArray, function(index, val) {
				 $("#tradList a").each(function() {
					if(val == $(this).attr('cln')) {
						$(this).addClass('selectedcolor');
					}
				});
			});
			copyTradItem();
			var a_cn = new Array();
			$("#tradAcq a").each(function(index) {
				var checkText = $(this).attr('title');
				a_cn[index]=checkText;
			});
			$("#tradText").html(a_cn.join(","));
		}
		/* ��ҵ�б�����ʾ����ѡ */
		$("#tradList li a").unbind().live('click', function() {
			// �ж�ѡ��������Ƿ񳬳�
			if($("#tradList .selectedcolor").length >= 5) {
				$("#tradropcontent").show(0).delay(3000).fadeOut("slow");
			} else {
				$(this).addClass('selectedcolor');
				copyTradItem(); // ����ҵ��ѡ�Ŀ���
			}
		});
		// ��ҵȷ��ѡ��
		$("#tradSure").unbind().click(function() {
			var a_cn=new Array();
			var a_id=new Array();
			$("#tradAcq a").each(function(index) {
				var checkID = $(this).attr('rel');
				var checkText = $(this).attr('title');
				a_id[index]=checkID;
				a_cn[index]=checkText;
			});
			if (a_cn.length > 0) {
				$("#tradText").html(a_cn.join(","));
				$("#trade_cn").val(a_cn.join(","));
				$("#trade").val(a_id.join(","));
			} else {
				$("#tradText").html("��ѡ����ҵ���");
				$("#trade_cn").val("");
				$("#trade").val("");
			}
			$("#divTradCate").hide();
		});
	}
	if($("#divTradCateD").length > 0) {
		fillTrad("#divTradCateD"); // ������ҵ���
		// �ָ���˾������ҵ
		if($("#tradeD").val()) {
			var tradid = $("#tradeD").val();
			 $("#tradListD a").each(function() {
				if(tradid == $(this).attr('cln')) {
					$(this).addClass('selectedcolor');
				}
			});
		}
		/* ������ҵ�б�����ʾ����ѡ */
		$("#divTradCateD li a").unbind().live('click', function() {
			$("#tradListD a").each(function() {
				$(this).removeClass('selectedcolor');
			});
			$(this).addClass('selectedcolor');
			var checkID = $(this).attr('cln');
			var checkText = $(this).attr('title');
			$("#tradTextD").html(checkText);
			$("#tradeD_cn").val(checkText);
			$("#tradeD").val(checkID);
			$("#divTradCateD").hide();
		});
	}
	if($("#divCityCateAD").length > 0) {
		fillCityJob("#divCityCateAD"); // ְλ������
		// �ָ�ְλ���ѡ������
		if($("#subclass").val()) {
			var scityid = $("#subclass").val();
			if(scityid == 0) {
				var ccityid = $("#category").val();
				$("#divCityCateAD .citycatebox p a").each(function() {
					if(ccityid == $(this).attr("rcoid")) {
						$(this).addClass('selectedcolor');
					}
				});
			} else {
				$("#divCityCateAD .citycatebox .subcate a").each(function() {
					if(scityid == $(this).attr("rcoid")) {
						$(this).parent().prev().find('font a').addClass('selectedcolor');
						$(this).addClass('selectedcolor');
					}
				});
			}
		}
		/* ְλ�������ʾ����ѡ */
		$("#divCityCateAD p a").unbind().live('click', function() {		
			$("#divCityCateAD p a").each(function() {
				$(this).removeClass('selectedcolor');
			});
			$(this).addClass('selectedcolor');
			var checkID = $(this).attr('pid').split(".");
			var checkText = $(this).attr('title');
			$("#cityTextAD").html(checkText);
			$("#category_cn").val(checkText);
			$("#category").val(checkID[0]);
			$("#subclass").val(checkID[1]);
			$("#divCityCateAD").hide();
		});
		$("#divCityCateAD .subcate a").unbind().live('click', function() {		
			$("#divCityCateAD .subcate a").each(function() {
				$(this).parent().prev().find('font a').removeClass('selectedcolor');
				$(this).removeClass('selectedcolor');
			});
			$(this).parent().prev().find('font a').addClass('selectedcolor');
			$(this).addClass('selectedcolor');
			var checkID = $(this).attr('pid').split(".");
			var checkText = $(this).attr('title');
			$("#cityTextAD").html(checkText);
			$("#category_cn").val(checkText);
			$("#category").val(checkID[0]);
			$("#subclass").val(checkID[1]);
			$("#divCityCateAD").hide();
		});
	}
}
function copyCityJobItem() {
	var cityacqhtm = '';
	$("#divCityCateJob .selectedcolor").each(function() {
		cityacqhtm += '<a pid="'+$(this).attr('pid')+'" href="javascript:;" title="'+$(this).attr('title')+'"><div class="text">'+$(this).attr('title')+'</div><div class="close"></div></a>';
	});
	$("#cityAcq").html(cityacqhtm);
	// ��ѡ��Ŀ�󶨵���¼�
	$("#cityAcq a").unbind().click(function() {
		var selval = $(this).attr('title');
		$("#divCityCateJob .selectedcolor").each(function() {
			if ($(this).attr('title') == selval) {
				$(this).removeClass('selectedcolor');
				copyCityJobItem();
			}
		});
	});
	// ���
	$("#cityEmpty").unbind().click(function() {
		$("#cityAcq").html("");
		$("#divCityCateJob .selectedcolor").each(function() {
			$(this).removeClass('selectedcolor');
		});
	});
}
function copyTradItem() {
	var tradacqhtm = '';
	$("#tradList .selectedcolor").each(function() {
		tradacqhtm += '<a href="javascript:;" rel="'+$(this).attr('cln')+'" title="'+$(this).attr('title')+'"><div class="text">'+$(this).attr('title')+'</div><div class="close" id="c-'+$(this).attr('cln')+'"></div></a>';
	});
	$("#tradAcq").html(tradacqhtm);
	// ��ѡ��Ŀ�󶨵���¼�
	$("#tradAcq a").unbind().click(function() {
		var selval = $(this).attr('title');
		$("#tradList .selectedcolor").each(function() {
			if ($(this).attr('title') == selval) {
				$(this).removeClass('selectedcolor');
				copyTradItem();
			}
		});
	});
	// ���
	$("#tradEmpty").unbind().click(function() {
		$("#tradAcq").html("");
		$("#tradList .selectedcolor").each(function() {
			$(this).removeClass('selectedcolor');
		});
	});
}
function fillTrad(fillID){
	var tradli = '';
	$.each(QS_trade, function(index, val) {
		if(val) {
			var trads = val.split(",");
		 	tradli += '<li><a title="'+trads[1]+'" cln="'+trads[0]+'" href="javascript:;">'+trads[1]+'</a></li>';
		}
	});
	$(fillID+" ul").html(tradli);
}
function fillCityJob(fillID){
	var citystr = '';
	citystr += '<tr>';
	citystr += '<td><ul class="jobcatelist">';
	$.each(QS_hunter_jobs_parent, function(pindex, pval) {
		if(pval) {
			var citys = pval.split(",");
	 		citystr += '<li>';
	 		citystr += '<p><font><a rcoid="'+citys[0]+'" pid="'+citys[0]+'.0" title="'+citys[1]+'" href="javascript:;">'+citys[1]+'</a></font></p>';
	 		if(QS_hunter_jobs[citys[0]]) {
	 			citystr += '<div class="subcate" style="display:none;">';
	 			var ccitysArray = QS_hunter_jobs[citys[0]].split("|");
		 		$.each(ccitysArray, function(cindex, cval) {
		 			if(cval) {
			 			var ccitys = cval.split(",");
			 			citystr += '<a rcoid="'+ccitys[0]+'" title="'+citys[1]+'/'+ccitys[1]+'" pid="'+citys[0]+'.'+ccitys[0]+'" href="javascript:;">'+ccitys[1]+'</a>';
		 			}
		 		});
	 			citystr += '</div>';
	 		}
	 		citystr += '</li>';
		}
	});
	citystr += '</ul></td>';
	citystr += '</tr>';
	$(fillID+" tbody").html(citystr);
	$(".jobcatelist li").each(function() {
		if($(this).find('.subcate').length <= 0) {
			$(this).find('font').css("background","none");
		}
	});
}
function fillCity(fillID){
	var citystr = '';
	citystr += '<tr>';
	citystr += '<td><ul class="jobcatelist">';
	$.each(QS_city_parent, function(pindex, pval) {
		if(pval) {
			var citys = pval.split(",");
	 		citystr += '<li>';
	 		citystr += '<p><font><a rcoid="'+citys[0]+'" pid="'+citys[0]+'.0" title="'+citys[1]+'" href="javascript:;">'+citys[1]+'</a></font></p>';
	 		if(QS_city[citys[0]]) {
	 			citystr += '<div class="subcate" style="display:none;">';
	 			var ccitysArray = QS_city[citys[0]].split("|");
		 		$.each(ccitysArray, function(cindex, cval) {
		 			if(cval) {
			 			var ccitys = cval.split(",");
			 			citystr += '<a rcoid="'+ccitys[0]+'" title="'+citys[1]+'/'+ccitys[1]+'" pid="'+citys[0]+'.'+ccitys[0]+'" href="javascript:;">'+ccitys[1]+'</a>';
		 			}
		 		});
	 			citystr += '</div>';
	 		}
	 		citystr += '</li>';
		}
	});
	citystr += '</ul></td>';
	citystr += '</tr>';
	$(fillID+" tbody").html(citystr);
	$(".jobcatelist li").each(function() {
		if($(this).find('.subcate').length <= 0) {
			$(this).find('font').css("background","none");
		}
	});
}