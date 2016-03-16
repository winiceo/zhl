 /*
 * 74cms �༭����
*/
$(function(){
    editCv.initMb();
    if(window.location.href.indexOf("page=changePhone")!=-1){
        var formParam = storage.get("formParam");
        var formParamJson = eval("(" + formParam + ")");
        for(var key in formParamJson){
            if(key=="brithdayTxt" || key=="startDateTxt" || key=="endDateTxt"|| key=="beginworkTxt"){
                $("#"+key).text(formParamJson[key]);
            }else{
                $("#"+key).val(formParamJson[key]);
                if(key=="sex_dummy"){
                    $("#"+key).siblings(".sex[data-val='"+formParamJson[key]+"']").trigger("click");
                }else if(key=="marry_dummy"){
                    $("#"+key).siblings(".marry[data-val='"+formParamJson[key]+"']").trigger("click");
                }else if(key=="abroad_dummy"){
                    $("#"+key).siblings(".abroad[data-val='"+formParamJson[key]+"']").trigger("click");
                }
            }
        }
        storage.del("formParam");
        storage.del("prevUrl");
    }
    if(window.location.href.indexOf("page=changeEmail")!=-1){
        var formEmail = storage.get("formEmail");
        var formEmailJson = eval("(" + formEmail + ")");
        for(var key in formEmailJson){
            if(key=="brithdayTxt" || key=="startDateTxt" || key=="endDateTxt"|| key=="beginworkTxt"){
                $("#"+key).text(formEmailJson[key]);
            }else{
                $("#"+key).val(formEmailJson[key]);
                if(key=="sex_dummy"){
                    $("#"+key).siblings(".sex[data-val='"+formEmailJson[key]+"']").trigger("click");
                }else if(key=="marry_dummy"){
                    $("#"+key).siblings(".marry[data-val='"+formEmailJson[key]+"']").trigger("click");
                }else if(key=="abroad_dummy"){
                    $("#"+key).siblings(".abroad[data-val='"+formEmailJson[key]+"']").trigger("click");
                }
            }
        }
        storage.del("formEmail");
        storage.del("prevUrl");
    }
})
var editCv = {
	initMb : function(){
        var curr = new Date().getFullYear(),
            cumm = new Date().getMonth(),
            that = this,
            opt = {
            'swdate': {
                theme: 'mct light',
                preset: 'date',
                dateOrder: 'yym��',
                display: 'bottom',
                headerText: false,
                startYear: curr - 50,
                endYear: curr,
                dateFormat: 'yy.m',
            },
            'ewdate': {
                theme: 'mct light',
                preset: 'date',
                dateOrder: 'yym��',
                display: 'bottom',
                headerText: false,
                startYear: curr - 50,
                endYear: curr,
                dateFormat: 'yy.m',
            },
            'birth': {
                theme: 'mct light',
                preset: 'date',
                dateOrder: 'yym��',
                display: 'bottom',
                headerText: false,
                defaultValue: new Date(new Date().setFullYear(curr-23)),
                maxDate: new Date(),
                minDate: new Date(new Date().setFullYear(1945)),
                dateFormat: 'yy.m',
            },
            'year': {
                theme: 'mct light',
                preset: 'date',
                dateOrder: 'yym��',
                display: 'bottom',
                startYear: curr - 50,
                endYear: curr,
                buttons: false,
                dateFormat: 'yy.m',
            },
            'yearStart': {
                theme: 'mct light',
                preset: 'date',
                dateOrder: 'yym��',
                display: 'bottom',
                defaultValue: new Date(new Date().setFullYear(curr-2)),
                maxDate: new Date(),
                minDate: new Date(new Date().setFullYear(curr - 60)),
                buttons: false,
                dateFormat: 'yy.m',
            },
            'yearEnd': {
                theme: 'mct light',
                preset: 'date',
                dateOrder: 'yym��',
                display: 'bottom',
                defaultValue: new Date(new Date().setFullYear(curr)),
                maxDate: new Date(),
                minDate: new Date(new Date().setFullYear(curr - 60)),
                buttons: false,
                dateFormat: 'yy.m',
            },
            'select': {
                theme: 'mct light',
                preset: 'select',
                rows: 7,
                inputClass: 'hide',
                display: 'bottom',
            }
        }
        $(".mbs").each(function(e,i){
            var type = $(this).data("type");
            var head = ($(this).data("text"))? $(this).data("text"):"";
            var thisid = ($(this).attr("id"))? $(this).attr("id"):"";
            switch(type){
                case "select":
                    $(this).scroller('destroy').scroller($.extend(opt['select'], {
                        buttons: false,
                        headerText: "ѡ��ѧ��",
                        onValueTap: function (item, inst) {
                            var text = item.find('.dw-i').text();
                            if(text){
                                $('#degreeTxt').text(text)
                                var majorDom = $("input[name$='major']");
                                if(text == "����" ||text == "����"){
                                    majorDom.attr("readonly","readonly").val("");
                                    majorDom.addClass("empty")
                                    majorDom.hide();
                                    majorDom.parent().prev().css("color",'#cfcfcf');
                                }else{
                                    majorDom.removeAttr("readonly").show()
                                    majorDom.removeClass("empty")
                                    majorDom.show();
                                    majorDom.parent().prev().removeAttr('style');
                                }
                                $('#degree').mobiscroll('select')
                            }
                        },
                        onSelect:function(v,i){
                            setTimeout(function(){
                                
                            },300)
                        }
                    }))
                    break;
                case "year":
                    that.formatDate($(this).next().val(),$(this))
                    if(head){
                        var self = $(this);
                        if(thisid == "beginworkTxt"){
                            $(this).scroller('destroy').scroller($.extend(opt['year'], {
                                headerText: head,
                                buttons: ["cancel","set",{text:"����",handler:function(e,i){
                                    $('#beginworkTxt').text("����");
                                    $('#beginwork_dummy').val(0);
                                    self.mobiscroll('cancel')
                                }}],
                                onSelect: function (v, i) {
                                    $('#beginworkTxt').text(i.values[0]+"��"+(parseInt(i.values[1])+1)+"��");
                                    $('#beginworkTxt').next().val(v);
                                }
                            }))
                        }else if(thisid == "graduateTxt"){
                            $(this).scroller('destroy').scroller($.extend(opt['yearEnd'], {
                                headerText: head,
                                onSelect: function (v, inst) {
                                    $('#graduate_dummy').val(inst.values[0]);
                                },
                                onValueTap: function (item, inst) {
                                    var text = item.find('.dw-i').text();
                                    if(text){
                                        $('#graduateTxt').text(text);
                                        $('#graduateTxt').mobiscroll('select');
                                    }
                                }
                            }))
                        }
                    }else{

                    }
                    break;
                case "yearStart":
                    var sTxt = $(this);
                    that.formatDate($(this).next().val(),$(this))
                    sTxt.scroller('destroy').scroller($.extend(opt['yearStart'], {
                        headerText: head,
                        buttons: ["cancel","set"],
                        onSelect: function (v, i) {
                            sTxt.next().val(v);
                            sTxt.text(i.values[0]+"��"+(parseInt(i.values[1])+1)+"��");
                        }
                    }))
                    break;
                case "yearEnd":
                    var eTxt = $(this);
                    that.formatDate($(this).next().val(),$(this))
                    eTxt.scroller('destroy').scroller($.extend(opt['yearEnd'], {
                        headerText: head,
                        buttons: ["cancel","set",{text:"����",handler:function(e,i){
                            eTxt.next().val(-1);
                            eTxt.text("����");
                            
                            eTxt.scroller("cancel")
                        }}],
                        onSelect: function (v, i) {
                            eTxt.next().val(v);
                            eTxt.text(i.values[0]+"��"+(parseInt(i.values[1])+1)+"��");
                            
                        }
                    }))
                    break;
                case "date":
                    that.formatDate($(this).next().val(),$(this))
                    if(head){
                        var thisdom = $(this);
                        $(this).scroller('destroy').scroller($.extend(opt['ewdate'], {
                            buttons: ["cancel","set",{text:"����",handler:function(e,i){
                                $('#endWork_dummy').val(-1);
                                $('#endWorkTxt').text("����");
                                thisdom.scroller("cancel")
                            }}],
                            onSelect: function (v, i) {
                                $('#endWork_dummy').val(i.val);
                                $('#endWorkTxt').text(i.values[0]+"��"+(parseInt(i.values[1])+1)+"��");
                            }
                        }))
                    }else{
                        $(this).scroller('destroy').scroller($.extend(opt['swdate'], {
                            buttons: ["cancel","set"],
                            onSelect: function (v, i) {
                                $('#startWork_dummy').val(i.val);
                                $('#startWorkTxt').text(i.values[0]+"��"+(parseInt(i.values[1])+1)+"��");
                            }
                        }))
                    }
                    break;
                case "birthday" :
                    that.formatDate($(this).next().val(),$(this))
                    $(this).scroller('destroy').scroller($.extend(opt['birth'], {
                        headerText: head,
                        buttons: ["cancel","set"],
                        onSelect: function (v, i) {
                            $('#birthdate').val(i.val);
                            $('#brithdayTxt').text(i.values[0]+"��"+(parseInt(i.values[1])+1)+"��");
                        }
                    }))
                    break;
                default:
                    break;
            }
        })
    },
    formatDate:function(date,$this){
        var dArr = date.split("."),
            str = "";
        if(!dArr[0]) return false;
        dArr[0] += "";
        switch(dArr[0]){
            case "0":
                str += "����"
                break;
            case "-1":
                str += "����"
                break;
            default:
                str += dArr[0]+"��";
                if(dArr[1]) str += dArr[1]+"��";
                break;
        }
        if($this && $this.length){
            $this.text(str)
        }
        return str;
    }
}
$(document).ready(function() {
	/* ���div��ת */
	$('.thisurl').click( function () {window.location.href =  $(this).attr('url');});

	/* ����󱳾� */
 	var lockWin = {
		on: function(a) {
			switch (a) {
				case 1:
					if ($(".hide_win").length < 1) {
						$("body").append('<div class="hide_win"></div>')
					}
					$(".hide_win").show();
					break;
				default:
					if ($(".lock_win").length < 1) {
						$("body").append('<div class="lock_win"></div>')
					}
					$(".lock_win").show();
					break
			}
		},
		off: function(a) {
			switch (a) {
				case 1:
					$(".hide_win").hide();
					break;
				default:
					$(".lock_win").hide();
					break
			}
		}
	};

	
	/* ����޸ļ������� */
	$("#resumenameEdit").on('click', function(event) {
		layer.open({
			title: [
		        '�޸ļ�������',
		        'color:#333333;'
		    ],
		    content: '<span class="input input--minoru"><input class="input__field input__field--minoru" type="text" id="modfiyResumenameInputbox" placeholder="'+$("#resumenameEdit").text()+'" /><label class="input__label input__label--minoru" for="modfiyResumebameInputbox"></label></span>',
		    btn: ['ȷ��', 'ȡ��'],
		    shadeClose: false,
		    yes: function(){ 
		    	var modifyresvalue = $("#modfiyResumenameInputbox").val();
				var resume_id = $("#resume_id").val();
		        $.post('?act=resume_name_save', {"resume_id": resume_id,"title":modifyresvalue}, function(data) {
		        	layer.open({
					    content: '�����ύ���ݣ�',
					    style: 'background-color:#FFFFFF; color:#666666; border:none;'
					});
		        	if (data == "ok") {
		        		$("#resumenameEdit .txt").text(modifyresvalue);
		        		layer.open({
						    content: '�޸ĳɹ���',
						    style: 'background-color:#FFFFFF; color:#666666; border:none;',
						    time: 1
						});
                        $("#resumenameEdit").css("background-position-x",($("#resumenameEdit").text().length*16+10)+"px");
		        	} else if(data == "err") {
						layer.open({
						    content: '�޸�ʧ�ܣ�',
						    style: 'background-color:#FFFFFF; color:#666666; border:none;',
						    time: 1
						});
						window.location.reload();
					} else {
						layer.open({
						    content: data,
						    style: 'background-color:#FFFFFF; color:#666666; border:none;',
						    time: 1
						});
						window.location.reload();
					}
		        });
		    }, no: function(){
		        layer.closeAll();
		    }
		});
	});

    /* ��˽���� */
    $(".setPrivacy").on('click', function(event) {
        var QS_privacy = new Array("1,����","2,����","3,�ر�"),
            pid = $(this).data("pid");
        layer.open({
            title: '��ѡ����������̶�',
            content: getOptionsContent(QS_privacy, ''),
            btn: ['ȷ��', 'ȡ��'],
            shadeClose: true,
            yes: function(){
                yesPrivacyOptionsClick(pid);
            }, no: function(){
                layer.closeAll();
            },
            success: function(elem){
                privacyOptionsClick();
            }   
        });
    });

 	/* ���ѧ�� */
 	$("#educationTxt").on('click', function(event) {
 		layer.open({
            title: '��ѡ�����ѧ��',
 			content: getOptionsContent(QS_education, ''),
		    btn: ['ȡ��'],
		    shadeClose: true,
		    yes: function(){
				layer.closeAll();
		    }, no: function(){
		        layer.closeAll();
		    },
		    success: function(elem){
			    optionsClick("#education", "#education_cn", "#educationTxt");
			}   
		});
 	});

 	/* �������� */
 	$("#experienceTxt").on('click', function(event) {
 		layer.open({
            title: '��ѡ��������',
 			content: getOptionsContent(QS_experience, ''),
		    btn: ['ȡ��'],
		    shadeClose: true,
		    yes: function(){
				layer.closeAll();
		    }, no: function(){
		        layer.closeAll();
		    },
		    success: function(elem){
			    optionsClick("#experience", "#experience_cn", "#experienceTxt");
			}   
		});
 	});

 	/* Ŀǰ״̬ */
 	$("#currentTxt").on('click', function(event) {
 		layer.open({
            title: '��ѡ��Ŀǰ״̬',
 			content: getOptionsContent(QS_current, ''),
		    btn: ['ȡ��'],
		    shadeClose: true,
		    yes: function(){
				layer.closeAll();
		    }, no: function(){
		        layer.closeAll();
		    },
		    success: function(elem){
			    optionsClick("#current", "#current_cn", "#currentTxt");
			}   
		});
 	});

 	/* �������� */
 	$("#natureTxt").on('click', function(event) {
 		layer.open({
            title: '��ѡ��������',
 			content: getOptionsContent(QS_jobsnature, ''),
		    btn: ['ȷ��', 'ȡ��'],
		    shadeClose: true,
		    yes: function(){
				layer.closeAll();
		    }, no: function(){
		        layer.closeAll();
		    },
		    success: function(elem){
			    optionsClick("#nature", "#nature_cn", "#natureTxt");
			}   
		});
 	});

 	/* ����н�� */
 	$("#wageTxt").on('click', function(event) {
 		layer.open({
            title: '��ѡ������н��',
 			content: getOptionsContent(QS_wage, ''),
		    btn: ['ȡ��'],
		    shadeClose: true,
		    yes: function(){
				layer.closeAll();
		    }, no: function(){
		        layer.closeAll();
		    },
		    success: function(elem){
			    optionsClick("#wage", "#wage_cn", "#wageTxt");
			}   
		});
 	});

 	/* ������ҵ */
 	$("#tradeTxt").on('click', function(event) {
 		layer.open({
 			title: [
		        '��ѡ��������ҵ'
		    ],
 			content: getOptionsContent(QS_trade, ''),
		    btn: ['ȡ��'],
		    shadeClose: true,
		    yes: function(){
				layer.closeAll();
		    }, no: function(){
		        layer.closeAll();
		    },
		    success: function(elem){
			    optionsClick("#trade", "#trade_cn", "#tradeTxt");
			}   
		});
 	});

 	/* ����״�� */
 	$("#marriageTxt").on('click', function(event) {
 		layer.open({
            title: '��ѡ�����״��',
		    content: '<div class="formDiv formDivlayer"><div class="formChild option" data-val="2" data-content="�ѻ�"><div class="lt">�ѻ�</div></div><div class="formChild option" data-val="1" data-content="δ��"><div class="lt">δ��</div></div></div>',
		    btn: ['ȷ��', 'ȡ��'],
		    shadeClose: true,
		    yes: function(){
				layer.closeAll();
		    }, no: function(){
		        layer.closeAll();
		    },
		    success: function(elem){
			    optionsClick("#marriage", "#marriage_cn", "#marriageTxt");
			}   
		});
 	});
 	
    /* ����ְ�� */
    var subTemp = '<div class="cline"></div><div class="formDiv">'+
                        '<textarea id="subTxtArea" rows="10" placeholder="��༭"></textarea>'+
                    '</div>'+
                    '<div class="btn_bar">'+
                        '<div id="saveSub" class="blue_btn">����</div>'+
                    '</div>';
    $("#achievementTxt").click(function(){
        var $this = $(this),
            baseTxt = $this.text();
        popWin.init({
            title:"�༭����ְ��",
            from:"right",
            html:subTemp,
            handle:function(){
                $("#subTxtArea").val(baseTxt);
                $("#saveSub").click(function(){
                    $this.text($("#subTxtArea").val());
                    $this.next().val($("#subTxtArea").val());
                    popWin.close("right");
                })
            }
        })
    })

    /* ��ѵ���� */
    $("#descriptionTxt").click(function(){
        var $this = $(this),
            baseTxt = $this.text();
        popWin.init({
            title:"�༭��ѵ����",
            from:"right",
            html:subTemp,
            handle:function(){
                $("#subTxtArea").val(baseTxt);
                $("#saveSub").click(function(){
                    $this.text($("#subTxtArea").val());
                    $this.next().val($("#subTxtArea").val());
                    popWin.close("right");
                })
            }
        })
    })

 	var m_selector = {
	jobsArr: "",
	jobs: function(a, i, d) {
		var h = $("#" + a);
		var e = "",
			f = this;
		if (!h) {
			clog("nodiv");
			return
		}
		e += '<div id="loc_level1_' + a + '" class="sl_level1"><div><ul>';
		$.each(QS_jobs_parent, function(indexParent, valParent) {
			var i = valParent.split(",")[0],
				cArrParent = QS_jobs[i].split("|");
			$.each(cArrParent, function(index, val) {
				var cArr = val.split(",");
				e += '<li data-key="' + cArr[1] + '" data-val="' + i + ',' + cArr[0] + '">' + cArr[1] + "</li>";
			});
		});
		e += '		</ul></div></div><div id="loc_level2_' + a + '" class="sl_level2"><div><ul>';
		e += "		</ul></div></div>";
		this.set(h, e);
		/* �ָ�ѡ�� */
		if (!$("#intention_jobs_id").val()) {
			var restoreId = $("#category").val(),
                topId = $("#topclass").val(),
                restoreArr = $("#loc_level1_" + a + " li"),
                firstKey = '';
            $.each(restoreArr, function(index, val) {
                if ($(this).data("val").split(",")[1] == restoreId) {
                    $(this).addClass('on');
                    firstKey = $(this).data("key");
                };
            });
            var gArr = QS_jobs[restoreId].split("|"),
                g = this.getFistJob(gArr, topId, restoreId, firstKey);
			$("#loc_level2_" + a + " ul").empty().html(g);
		} else {
			$("#loc_level1_" + a + " li").first().addClass("on");
            var gidp = $("#loc_level1_" + a + " li").first().data("val").split(",")[0],
                gid = $("#loc_level1_" + a + " li").first().data("val").split(",")[1];
            if (QS_jobs[gid]) {
                var gArr = QS_jobs[gid].split("|"),
                    firstKey = $("#loc_level1_" + a + " li").first().data("key"),
                    g = this.getFistJob(gArr, gidp, gid, firstKey);
            } else {
                var gArr = new Array(),
                    firstKey = $("#loc_level1_" + a + " li").first().data("key"),
                    g = this.getFistJob(gArr, gidp, gid, firstKey);
            };
            $("#loc_level2_" + a + " ul").empty().html(g);
		};
		if (document.documentElement.scrollTop + document.body.scrollTop > 108) {
			$(".sl_level1,.sl_level2").css("height", window.innerHeight - 45 - 108)
		} else {
			$(".sl_level1,.sl_level2").css("height", window.innerHeight - h.offset().top)
		}
		if (!i) {
			$("#loc_level2_" + a).delegate("li:not(.fail)", "click", function() {
				storage.setJson("search", "soCity", $(this).data("val"));
				if ($(this).hasClass("locCity")) {
					storage.setJson("search", "soCity_cn", $(this).data("txt"))
				} else {
					storage.setJson("search", "soCity_cn", $(this).text())
				}
			});
		}
		$("#loc_level1_" + a + " li").on("click", function() {
			$(this).parent().find("li").removeClass("on");
            $(this).addClass("on");
            var mp = $(this).data("val").split(",")[0],
                m = $(this).data("val").split(",")[1],
                n = '<li data-val="' + mp + ',' + m + ',0" data-txt="' + $(this).data("key") + '">����</li>';
            if (QS_jobs[m]) {
                var mArr = QS_jobs[m].split("|");
                $.each(mArr, function(indexlevel2, vallevel2) {
                    var mArrLeval2 = vallevel2.split(",");
                    n += '<li data-val="' + mp + "," + m + "," + mArrLeval2[0] + '" data-txt="' + mArrLeval2[1] + '">' + mArrLeval2[1] + '</li>';
                });
            };
            $("#loc_level2_" + a + " ul").empty().html(n);
            j.refresh();
		});
		var b = new iScroll("loc_level1_" + a);
		var j = new iScroll("loc_level2_" + a);
		return b
	},
	nearCityCatch: "",
	loadingNear: false,
	soCity: function(a, i, d) {
		var h = $("#" + a);
        var e = "",
            f = this;
        if (!h) {
            clog("nodiv");
            return
        }
        e += '<div id="loc_level1_' + a + '" class="sl_level1"><div><ul>';
        $.each(QS_subsite, function(index, val) {
            var cArr = val.split(","),
                cArr0Arr = new Array();
            if (cArr[0]) {
                cArr0Arr = cArr[0].split('-');
            };
            e += '<li data-key="' + cArr[1] + '" data-val="' + cArr0Arr[1] + '" data-code="' + cArr0Arr[0] + '">' + cArr[1] + "</li>";
            
        });
        e += '      </ul></div></div><div id="loc_level2_' + a + '" class="sl_level2"><div><ul>';
        e += "      </ul></div></div>";
        this.set(h, e);
        /* �ָ�ѡ�� */
        if ($("#district").val()) {
            var restoreId = $("#district").val();
            var restoreArr = $("#loc_level1_" + a + " li");
            $.each(restoreArr, function(index, val) {
                if ($(this).data("val") == restoreId) {
                    $(this).addClass('on');
                };
            });
            if (QS_city[restoreId]) {
                var gArr = QS_city[restoreId].split("|"),
                    firstKey = $("#district_cn").val(),
                g = this.getFistCity(gArr, restoreId, firstKey);
            } else {
                var gArr = new Array(),
                    firstKey = $("#loc_level1_" + a + " li").first().data("key"),
                g = this.getFistCity(gArr, restoreId, firstKey);
            };
            $("#loc_level2_" + a + " ul").empty().html(g);
        } else {
            $("#loc_level1_" + a + " li").first().addClass("on");
            var gid = $("#loc_level1_" + a + " li").first().data("val");
            if (QS_city[gid]) {
                var gArr = QS_city[gid].split("|"),
                    firstKey = $("#loc_level1_" + a + " li").first().data("key"),
                g = this.getFistCity(gArr, gid, firstKey);
            } else {
                var gArr = new Array(),
                    firstKey = $("#loc_level1_" + a + " li").first().data("key"),
                g = this.getFistCity(gArr, gid, firstKey);
            };
            $("#loc_level2_" + a + " ul").empty().html(g);
        };
        if (document.documentElement.scrollTop + document.body.scrollTop > 108) {
            $(".sl_level1,.sl_level2").css("height", window.innerHeight - 45 - 108)
        } else {
            $(".sl_level1,.sl_level2").css("height", window.innerHeight - h.offset().top)
        }
        $("#loc_level1_" + a + " li").on("click", function() {
            $('#subsite_id').val($(this).data("code"));
            $('#district').val($(this).data("val"));
            $('#sdistrict').val(0);
            $('#district_cn').val($(this).data("key"));
            $(this).parent().find("li").removeClass("on");
            $(this).addClass("on");
            var m = $(this).data("val"),
                n = '<li data-val="' + m + ',0" data-txt="' + $(this).data("key") + '">����</li>';
            if (QS_city[m]) {
                var mArr = QS_city[m].split("|");
                $.each(mArr, function(indexlevel2, vallevel2) {
                    var mArrLeval2 = vallevel2.split(",");
                    n += '<li data-val="' + m + "," + mArrLeval2[0] + '" data-txt="' + mArrLeval2[1] + '">' + mArrLeval2[1] + "</li>";
                });
            };
            $("#loc_level2_" + a + " ul").empty().html(n);
        });
        var b = new iScroll("loc_level1_" + a);
        var j = new iScroll("loc_level2_" + a);
        return b
	},
    majors: function(a, i, d) {
        var h = $("#" + a);
        var e = "",
            f = this;
        if (!h) {
            clog("nodiv");
            return
        }
        e += '<div id="loc_level1_' + a + '" class="sl_level1"><div><ul>';
        $.each(QS_major_parent, function(index, val) {
             var cArr = val.split(",");
            e += '<li data-key="' + cArr[1] + '" data-val="' + cArr[0] + '">' + cArr[1] + "</li>"
        });
        e += '      </ul></div></div><div id="loc_level2_' + a + '" class="sl_level2"><div><ul>';
        e += "      </ul></div></div>";
        this.set(h, e);
        /* �ָ�ѡ�� */
        if ($("#major_cn").val().length > 0) {
            var restoreArr = $("#loc_level1_" + a + " li"),
                restoreId = this.getMajorRestoreId(a),
                firstKey = '';
            $.each(restoreArr, function(index, val) {
                if ($(this).data("val") == restoreId) {
                    $(this).addClass('on');
                    firstKey = $(this).data("key");
                };
            });
            var gArr = QS_major[restoreId].split("|"),
                g = this.getFistCity(gArr, restoreId, firstKey);
            $("#loc_level2_" + a + " ul").empty().html(g);
        } else {
            $("#loc_level1_" + a + " li").first().addClass("on");
            var gid = this.getMajorRestoreId(a),
                gArr = QS_major[gid].split("|"),
                firstKey = $("#loc_level1_" + a + " li").first().data("key"),
                g = this.getFistCity(gArr, gid, firstKey);
            $("#loc_level2_" + a + " ul").empty().html(g);
        };
        if (document.documentElement.scrollTop + document.body.scrollTop > 108) {
            $(".sl_level1,.sl_level2").css("height", window.innerHeight - 45 - 108)
        } else {
            $(".sl_level1,.sl_level2").css("height", window.innerHeight - h.offset().top)
        }
        if (!i) {
            $("#loc_level2_" + a).delegate("li:not(.fail)", "click", function() {
                storage.setJson("search", "soCity", $(this).data("val"));
                if ($(this).hasClass("locCity")) {
                    storage.setJson("search", "soCity_cn", $(this).data("txt"))
                } else {
                    storage.setJson("search", "soCity_cn", $(this).text())
                }
            });
        }
        $("#loc_level1_" + a + " li").on("click", function() {
            $(this).parent().find("li").removeClass("on");
            $(this).addClass("on");
            var m = $(this).data("val"),
                n = '<li data-val="' + m + ',0" data-txt="' + $(this).data("key") + '">����</li>',
                mArr = QS_major[m].split("|");
            $.each(mArr, function(indexlevel2, vallevel2) {
                var mArrLeval2 = vallevel2.split(",");
                n += '<li data-val="' + m + "," + mArrLeval2[0] + '" data-txt="' + mArrLeval2[1] + '">' + mArrLeval2[1] + "</li>";
            });
            $("#loc_level2_" + a + " ul").empty().html(n);
            j.refresh();
        });
        var b = new iScroll("loc_level1_" + a);
        var j = new iScroll("loc_level2_" + a);
        return b
    },
	getFistCity: function(arr, pid, firstKey) {
        var b = '<li data-val="' + pid + ',0" data-txt="' + firstKey + '">����</li>';
        $.each(arr, function(index, val) {
            var sArr = val.split(",");
            b += '<li data-val="' + pid + "," + sArr[0] + '" data-txt="' + sArr[1] + '">' + sArr[1] + "</li>";
        });
        return b;
    },
	getFistJob: function(arr, pid, sid, firstKey) {
		var b = '<li data-val="' + pid + ',' + sid + ',0" data-txt="' + firstKey + '">����</li>';
        $.each(arr, function(index, val) {
            var sArr = val.split(",");
            b += '<li data-val="' + pid + "," + sid + "," + sArr[0] + '" data-txt="' + sArr[1] + '">' + sArr[1] + "</li>";
        });
        return b;
	},
    getMajorRestoreId: function(a) {
        var restoreCn = $("#major_cn").val(),
            restoreArr = $("#loc_level1_" + a + " li"),
            restoreId = '';
        if ($("#major_cn").val().length > 0) {
            $.each(QS_major_parent, function(index, val) {
                var confirmLevel1Arr = QS_major[index+1].split("|");
                $.each(confirmLevel1Arr, function(indexLenvl1, valLenvl1) {
                    var confirmLevel1Cn = valLenvl1.split(",")[1];
                    if (restoreCn === confirmLevel1Cn) {
                        restoreId = index + 1;
                    };
                });
            });
        } else {
            restoreId = 1;
        }
        return restoreId;
    },
	set: function(b, a, c) {
		if (b.find(".sl_list>div").length) {
			b.find(".sl_list>div").empty().html(a)
		} else {
			if (b.find(".sl-main>div").length) {
				b.find(".sl-main>div").empty().html(a)
			} else {
				b.empty().html(a);
			}
		}
		if (c) {
			$("#more_level").css("height", window.innerHeight - 50)
		} else {
			if (document.documentElement.scrollTop + document.body.scrollTop > 108) {
				b.css("height", window.innerHeight - 45 - 108)
			} else {
				b.css("height", window.innerHeight - b.offset().top)
			}
		}
	}
};
 	/* �������� */
 	$("#cityTxt").on("click",function(){
 		var elem = $(".select_bar_div"),
            that = this;
        elem.show().css("height",300)
        lockWin.on();
        if($("#loc_level1_cityList").length){
            elem.show().addClass("on");
        }else{
            elem.empty();
            elem.append('<div class="select_bar_head">����</div><div id="cityList" class="sl_list"><div><ul></ul></div></div>');
            elem.addClass("on");
            var select_city = m_selector.soCity($(this).data("go")+"List",true,true)
            setTimeout(function(){
                $(".sl_level1,.sl_level2").css("height",255)
                select_city.refresh();
            },500)
        }
        elem.find(".sl_level2").off("click.checkcity").delegate("li:not(.fail)","click.checkcity",function(){
            if($(this).hasClass('locCity')){
                $("#cityTxt").text($(this).data("txt"));
            }else{
                $("#cityTxt").text($(this).data("txt"));
            }
            var thisValArr = $(this).data("val").split(",");
            $("#district").val(thisValArr[0]);
            $("#sdistrict").val(thisValArr[1]);
            $("#district_cn").val($(this).data("txt"));
            hideBar(elem);
        })
        $(".lock_win").on("click",function(){
            hideBar(elem);
        })
    });

 	/* ����ְλ */
 	$("#jobTxt").on("click",function(){
 		var elem = $(".select_bar_div"),
            that = this;
        elem.show().css("height",300)
        lockWin.on();
        if($("#loc_level1_jobList").length){
            elem.show().addClass("on");
        }else{
            elem.empty();
            elem.append('<div class="select_bar_head">ְλ</div><div id="jobList" class="sl_list"><div><ul></ul></div></div>');
            elem.addClass("on");
            var select_city = m_selector.jobs($(this).data("go")+"List",true,true)
            setTimeout(function(){
                $(".sl_level1,.sl_level2").css("height",255)
                select_city.refresh();
            },500)
        }
        elem.find(".sl_level2").off("click.checkcity").delegate("li:not(.fail)","click.checkcity",function(){
            if($(this).hasClass('locCity')){
                $("#jobTxt").text($(this).data("txt"));
            }else{
                $("#jobTxt").text($(this).data("txt"));
            }
            $("#intention_jobs_id").val($(this).data("val"));
            $("#intention_jobs").val($(this).data("txt"));
            hideBar(elem);
        })
        $(".lock_win").on("click",function(){
            hideBar(elem);
        })
    });

    /* �����б���ఴť */
    $(".setMore").on("click",function(){
        var elem = $(".select_bar_div"),
            that = this,
            pid = $(this).data("pid");
        elem.show();
        lockWin.on();
        if($("#loc_level1_cityList").length){
            elem.show().addClass("on");
        }else{
            elem.empty();
            elem.append('<div class="select_bar_head updateresume center">��������</div><div class="select_bar_head delresume center"><span class="del">ɾ��</span></div><div class="select_bar_head cancle center">ȡ��</div>');
            elem.addClass("on");
        }
        $(".lock_win,.cancle").on("click",function(){
            hideBar(elem);
        });
        /* �������� */
        $(".updateresume").on('click', function(event) {
            layer.confirm('ȷ��Ҫ�����ü�����', {
                btn: ['ȷ��'], //��ť
                title: '��������'
            }, function(){
                $.get("?act=resume_talent&pid="+pid+"", function(data){
                    if(data=="ok"){
                        layer.msg('����߼�����,��ȴ�����Ա��ˣ�');
                        setTimeout(function () {
                            window.location.reload();
                        }, 2000);
                    }else if(data="err"){
                        layer.msg('����ʧ�ܣ�', {icon: 2});
                    }
                });
            }, function(){
                layer.closeAll();
                hideBar(elem);
            });
        });
        /* ɾ������ */
        $(".delresume").on('click', function(event) {
            layer.confirm('ȷ��Ҫɾ���ü�����', {
                btn: ['ȷ��','ȡ��'], //��ť
                title: 'ɾ������'
            }, function(){
                $.get("?act=resume_del&pid="+pid+"", function(data){
                    if(data=="ok"){
                        layer.msg('ɾ�������ɹ���', {icon: 1});
                        setTimeout(function () {
                            window.location.reload();
                        }, 2000);
                    }else if(data="err"){
                        layer.msg('ɾ������ʧ�ܣ�', {icon: 2});
                    }else{
                        layer.msg(data);
                    }
                });
            }, function(){
                layer.closeAll();
                hideBar(elem);
            });
        });
    });

 	/* רҵ */
 	$("#majorTxt").on("click",function(){
 		var elem = $(".select_bar_div"),
            that = this;
        elem.show().css("height",300)
        lockWin.on();
        if($("#loc_level1_cityList").length){
            elem.show().addClass("on");
        }else{
            elem.empty();
            elem.append('<div class="select_bar_head">רҵ</div><div id="majorList" class="sl_list"><div><ul></ul></div></div>');
            elem.addClass("on");
            var select_city = m_selector.majors($(this).data("go")+"List",true,true);
            setTimeout(function(){
                $(".sl_level1,.sl_level2").css("height",255)
                select_city.refresh();
            },500);
        }
        elem.find(".sl_level2").off("click.checkcity").delegate("li:not(.fail)","click.checkcity",function(){
            if($(this).hasClass('locCity')){
                $("#majorTxt").text($(this).data("txt"));
            }else{
                $("#majorTxt").text($(this).data("txt"));
            }
            $("#major").val($(this).data("val").split(",")[1]);
            $("#major_cn").val($(this).data("txt"));
            hideBar(elem);
        })
        $(".lock_win").on("click",function(){
            hideBar(elem);
        })
    });

 	/* ѡ��֮�����ش󱳾� */
    function hideBar(elem) {
        lockWin.off();
        elem.removeClass("on")
    }

    function clog(a) {
		console.log(a)
	}

	function checkVals(val){
        var mark = true;
        $(".sValDiv>div>div").each(function(){
            if($(this).data("val") == val){
                mark = false;
                return;
            }
        })
        return mark;
    }

    function setVal($this,fun,length,lenFail){
        var addFlag = true,
            editDom = "",
            txt = ($this.data("key"))?$this.data("key"):$this.data("val");
        $("#sValDiv>div>div").each(function(){
            var a = $(this).data("val");
            var b = txt;
            a = a.toString();
            b = b.toString();
            var aArr = a.split(",");
            var bArr = b.split(",");
            if(a == b){
                return false;
            }
            if(aArr.length == 1 && bArr.length == 2){
                if(aArr[0] == bArr[0]){
                    editDom = $(this);
                    addFlag = false;
                }
            }
            if(aArr.length == 2 && bArr.length == 3){
                if(aArr[0] == bArr[0] && aArr[1] == bArr[1]){
                    editDom = $(this);
                    addFlag = false;
                }
            }
        })
        if(addFlag){
            if($("#sValDiv>div>div").length>=length){
                return lenFail()
            }
            $("#sValDiv>div").append('<div data-val="'+txt+'">'+$this.text()+'<em class="close"></em></div>')
            fun();
        }else{
            editDom.data("val",txt)
            editDom.html($this.text()+'<em class="close"></em>')
            fun();
        }
    }
 	/* չ������ѡ���� */
 	$(".notRformChild").hide();
 	$("#showNotrequired").on('click', function(event) {
        $(this).hide();
 		$(".notRformChild").toggle();
 	});
});

/* ��ȡ������������� */
function getOptionsContent(dataSource, title) {
	var content = '<div class="formDiv formDivlayer">';
	if (title.length > 0) {
		content += '<div class="formChild"><div class="lt">' + title + '</div></div>';
	};
	$.each(dataSource, function(index, val) {
		var dataSourceContentArr = val.split(","), id = dataSourceContentArr[0], text = dataSourceContentArr[1];
		content += '<div class="formChild option" data-val="' + id + '" data-content="' + text + '"><div class="lt">' + text + '</div></div>';
	});
	content += '</div>';
	return content;
}

/* ������ѡ���б������¼� */
function optionsClick(optionId, optionCn, showcontentId) {
	$(".formDivlayer .option").on('click', function(event) {
		$(optionId).val($(this).data('val'));
		$(optionCn).val($(this).data('content'));
		$(showcontentId).text($(this).data('content'));
 		layer.closeAll();
 	});
}
/* ������˽����¼� */
function privacyOptionsClick() {
    $(".formDivlayer .option").on('click', function(event) {
        $(".formDivlayer .option").each(function(index, el) {
            $(this).removeClass('on');
        });
        $(this).addClass('on');
    });
}
/* ������˽ȷ���¼� */
function yesPrivacyOptionsClick(pid) {
    var optionId = $(".formDivlayer .on").data('val');
    $.post('?act=resume_privacy_save', {pid: pid,display: optionId}, function(data) {
        if(data=="ok"){
            layer.msg('��˽���óɹ���', {icon: 1});
            setTimeout(function () {
                window.location.reload();
            }, 2000);
        }else if(data=="err"){
            layer.msg('��˽����ʧ�ܣ�', {icon: 2});
        }
    });
    layer.closeAll();
}