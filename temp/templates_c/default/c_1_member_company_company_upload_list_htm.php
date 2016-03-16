<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 23:15 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=gb2312">
    <title><?php echo $this->_vars['title']; ?>
</title>
    <link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico"/>
<meta name="author" content="找葫芦" />
<meta name="copyright" content="zhaohulu.com" />
    <link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/user_common.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/user_company.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/ui-dialog.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/datepicker.css"/>
    <script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min-common.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.vtip-min.js" type="text/javascript" language="javascript"></script>
    <script type="text/javascript" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.datepicker.js"></script>
    <script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.validate.min.js" type='text/javascript'
            language="javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {


            $('.name-link').toggle(function () {
                $(this).parents('.c-data-content').removeClass('data-no-read').next().show();
                $(this).parents('.c-data-row').css({'border-color': '#fff'});
            }, function () {
                $(this).parents('.c-data-content').next().hide();
                $(this).parents('.c-data-row').css({'border-color': '#ccc'});
            });
            // 顶部筛选
            $('.data-filter').on('click', function (e) {
                $(this).find('.filter-down').toggle();
                var fWidth = $(this).find('.filter-span').outerWidth(true) - 2;
                $(this).find(".filter-down").width(fWidth);
                $(document).one("click", function () {
                    $('.filter-down').hide();
                });
                e.stopPropagation();
                $(".data-filter").not($(this)).find('.filter-down').hide();
            })
            // 状态
            $('.state-icon').on('click', function (e) {
                $(this).next().toggle();
                $(document).one('click', function () {
                    $('.state-list').hide();
                });
                e.stopPropagation();
                $('.state-icon').not($(this)).next().hide();
            });
            $('.state-list .state').on('click', function (e) {
                var resume_state = $(this).attr('state');
                var resume_state_cn = $(this).text();
                var resume_id = $(this).attr('resume_id');
                $.post('<?php echo $this->_vars['QISHI']['site_dir']; ?>
user/user_label_resume.php', {
                    "resume_state": resume_state,
                    "resume_state_cn": resume_state_cn,
                    "resume_id": resume_id
                }, function (data) {
                    if (data == "ok") {
                        window.location.replace(location);
                    }
                    else {
                        alert(data);
                        $('.state-list').hide();
                    }
                });
                e.stopPropagation();
            });
            // 删除弹出
            delete_dialog('.ctrl-del', '#form1');
            //邀请面试弹出
            inviteJob_dialog('.inter-job', "<?php echo $this->_vars['QISHI']['site_dir']; ?>
user/user_invited.php", '<?php echo $_SESSION['utype']; ?>
');
            // 发送到邮箱弹出
            companySendToEmail_dialog('.db-btn.btn3', './company_ajax.php');
        })


        //验证
        $(document).ready(function () {
            $("#Form_upload").validate({
// debug: true,
// onsubmit:false,
//onfocusout :true,
                rules: {
                    logo: {
                        required: true,
                        accept: "xls"
                    }
                },
                messages: {
                    logo: {
                        required: jQuery.format("请上传简历"),
                        accept: jQuery.format("文件格式支持xls")
                    }
                },
                errorPlacement: function (error, element) {
                    if (element.is(":radio"))
                        error.appendTo(element.parent().next().next());
                    else if (element.is(":checkbox"))
                        error.appendTo(element.next());
                    else
                        error.appendTo(element.parent());
                }
            });

            $("#up").click(function () {
                if ($("#Form_upload").valid()) {
                    $("#upform").hide();
                    $("#upform_wait").show();
                }
            });

        });
    </script>
</head>
<body <?php if ($this->_vars['QISHI']['body_bgimg']): ?>style="background:url(<?php echo $this->_vars['QISHI']['site_domain'];  echo $this->_vars['QISHI']['site_dir']; ?>
data/<?php echo $this->_vars['QISHI']['updir_images']; ?>
/<?php echo $this->_vars['QISHI']['body_bgimg']; ?>
) repeat-x center 38px;"
      <?php endif; ?>>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("user/header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<div class="page_location link_bk">当前位置：<a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
">首页</a> > <a href="<?php echo $this->_vars['userindexurl']; ?>
">会员中心</a> >
    收到的简历
</div>
<div class="usermain">
    <div class="leftmenu  com link_bk">
        <?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("member_company/left.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
    </div>
    <div class="rightmain">
        <div class="bbox1">
            <div class="topnav get_resume">
                <div class="titleH1">
                    <div class="h1-title">上传简历</div>
                </div>
                <?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("member_company/upload_nav.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
            </div>

            <form id="form1" name="form1" method="post" action="?act=set_apply_jobs">
                <div class="company-data-list">
                    <div class="c-data-top apply_jobs clearfix">
                        <div class="item f-left check-item"><input type="checkbox" name="chkAll" id="chk"
                                                                   title="全选/反选"/></div>

                        <div class="item f-left top-item2">文件名称</div>
                        <div class="item f-left top-item3">上传时间</div>

                    </div>
                    <?php if ($this->_vars['uploads']): ?>
                    <?php if (count((array)$this->_vars['uploads'])): foreach ((array)$this->_vars['uploads'] as $this->_vars['list']): ?>

                    <div class="c-data-row">
                        <div class='c-data-content apply_jobs clearfix <?php if ($this->_vars['list']['personal_look'] == "1" && $_GET['look'] == ""): ?>data-no-read<?php endif; ?>'>
                        <div class="c-item f-left check-item"><input name="y_id[]" type="checkbox" id="y_id"
                                                                     value="<?php echo $this->_vars['list']['did']; ?>
"/></div>

                        <div class="c-item f-left content2"> <?php echo $this->_vars['list']['name']; ?>

                        </div>
                        <div class="c-item f-left content3"><?php echo $this->_vars['list']['addtime']; ?>
</div>
                            </div>
                    </div>


                    <?php endforeach; endif; ?>

                    <?php else: ?>
                    <div class="emptytip">没有找到对应的信息！</div>
                    <?php endif; ?>
                </div>
            </form>
            <?php if ($this->_vars['page']): ?>
            <table border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td height="50" align="center">
                        <div class="page link_bk"><?php echo $this->_vars['page']; ?>
</div>
                    </td>
                </tr>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("user/footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>