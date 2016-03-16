/* ��js �з�����Ҫ���� dialog-min.js*/

/*
  ���˲���
*/
function is_answer_dialog(className)
{
      $(''+className+'').on('click', function(){
            var url = $(this).data("url"),
            paperid = $(this).data("id"),
            ajax_url = "evaluation_ajax.php?act=is_answer&paperid="+paperid,
            msg = '';
      var myDialog = dialog();
      myDialog.title('������ʾ');
      myDialog.content("������...");
      myDialog.width('300');
      myDialog.showModal();
      jQuery.ajax({
        url: ajax_url,
        success: function (data) {
          msg = data.split("|")[1];
                  myDialog.content('<div class="del-dialog"><div class="tip-block"><span class="del-tips-text">'+msg+'</span></div></div><div class="center-btn-wrap"><input type="button" value="ȷ��" class="btn-65-30blue btn-big-font DialogSubmit" /><input type="button" value="ȡ��" class="btn-65-30grey btn-big-font DialogClose" /></div>');
                  /* �ر� */
                  $(".DialogClose").live('click',function() {
                    myDialog.close().remove();
                  });
                  /* ȷ������ */
                  $(".DialogSubmit").click(function() 
                  {
                        if(data.split("|")[0] == '1')
                        {
                              $.post("evaluation_ajax.php?act=is_answer_next",{paperid:paperid},
                              function(result)
                              {
                                    if(result == 'ok')
                                    {
                                          window.location.href=url;
                                    }
                                    else
                                    {
                                          myDialog.content(result);
                                    }
                              });
                        }
                        else if(data.split("|")[0] == '3')
                        {
                          window.location.href="my_evaluation.php";
                        }
                        else
                        {
                          window.location.reload();
                        }
                  });
              }
      });
  })
}