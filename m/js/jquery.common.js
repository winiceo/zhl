//����
function OpenCity(objid,input_cn,input,inputs,titstr)
{
	$(objid).unbind().click(function()
	{
			$(this).blur();
			html="";
			html+="<div class=\"selectbox\" id=\"selectbox\">";
			html+="<div class=\"titbox\">";
			html+="<div class=\"lefttit\">"+titstr+"</div>";
			html+="<div class=\"unrestricted\">������</div>";
			html+="<div class=\"closs\"></div>";
			html+="</div>  ";
			html+="<div class=\"listbox\" id=\"listboxb\">";
			html+="</div>";
			html+="<div class=\"listbox\" id=\"listboxs\">";
			html+="</div>";
			html+="</div>";
			if ($("#selectbox").html()==null)
			{
				$("body").append(html);
				$("#selectbox #listboxb").html(MakeLiB(QS_city_parent));
				$("#selectbox  .unrestricted").click( function () {
								$(input_cn).html('������');
								$(input).val('');
								$(inputs).val('');
								$("#selectbox #listboxb .li .t2").removeClass("h");
								$("#selectbox #listboxb").show();
								$("#selectbox #listboxs").hide();
								$("#selectbox").hide();
					});	
				//�󶨹ر�
				$("#selectbox .closs").click( function () { 
					$("#selectbox").hide();
				});
				$("#selectbox #listboxb .li").click( function () {
					$("#selectbox #listboxb .li .t2").removeClass("h");
					$(this).find(".t2").addClass("h");		
					$("#selectbox #listboxb").hide();
					$("#selectbox #listboxs").show();
					$(input).val($(this).attr('id'));
					if(QS_city[$(this).attr('id')]) {
						$("#selectbox #listboxs").html(MakeLi(QS_city[$(this).attr('id')]));
						$("#selectbox #listboxs .goback").click( function () {
							$("#selectbox #listboxb").show();
							$("#selectbox #listboxs").hide();	
						});								   
						$("#selectbox #listboxs .li").click( function () {
							$("#selectbox #listboxs .li .t2").removeClass("h");
							$(this).find(".t2").addClass("h");		
							$(input_cn).html($(this).attr('title'));
							$(inputs).val($(this).attr('id'));
							$("#selectbox").hide();
						});
					} else {
						$(input_cn).html($(this).attr('title'));
						$(inputs).val("0");
						$("#selectbox").hide();
					}
				});
			}
			else
			{
				$("#selectbox").show();
			}

			
	});
}
//���ɴ���
function MakeLiB(arr)
{
			if (arr=="")return false;
			var htmlstr='';
				for (x in arr)
				{
				 var v=arr[x].split(",");
				htmlstr+="<div class=\"li\"  title=\""+v[1]+"\" id=\""+v[0]+"\"><div class=\"t1\">"+v[1]+"</div><div class=\"t2\"></div><div class=\"clear\"></div></div>";
				}
			return htmlstr; 
}
//����С��
function MakeLi(val)
{
			if (val=="")return false;
			arr=val.split("|");
			var htmlstr='<div class=\"goback\">�����ϼ�����</div>';
				for (x in arr)
				{
				 var v=arr[x].split(",");
				htmlstr+="<div class=\"li\"  title=\""+v[1]+"\" id=\""+v[0]+"\"><div class=\"t1\">"+v[1]+"</div><div class=\"t2\"></div><div class=\"clear\"></div></div>";
				}
			return htmlstr; 
}
//������һҳ
$(document).ready(function()
{
	$("#pageback").click( function () { 
	window.history.go(-1);
	});

	$(window).scroll(function() {
		if ($(window).scrollTop() > 100) {
			$('.back-to-top').stop().fadeIn(400);
		}else{
			$('.back-to-top').stop().fadeOut(400);
		}
	});
	$(document).on('click', '.back-to-top', function() {
		$('body, html').animate({
			scrollTop: 0
		}, 500);
		return false;
	})
});

