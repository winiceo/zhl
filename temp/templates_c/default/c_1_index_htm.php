<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_link.php'); $this->register_function("qishi_link", "tpl_function_qishi_link",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_news_category.php'); $this->register_function("qishi_news_category", "tpl_function_qishi_news_category",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.cat.php'); $this->register_modifier("cat", "tpl_modifier_cat",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_get_classify.php'); $this->register_function("qishi_get_classify", "tpl_function_qishi_get_classify",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_categoryname.php'); $this->register_modifier("qishi_categoryname", "tpl_modifier_qishi_categoryname",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_resume_list.php'); $this->register_function("qishi_resume_list", "tpl_function_qishi_resume_list",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_companyjobs_list.php'); $this->register_function("qishi_companyjobs_list", "tpl_function_qishi_companyjobs_list",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_jobs_list.php'); $this->register_function("qishi_jobs_list", "tpl_function_qishi_jobs_list",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_ad.php'); $this->register_function("qishi_ad", "tpl_function_qishi_ad",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_help_list.php'); $this->register_function("qishi_help_list", "tpl_function_qishi_help_list",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_news_list.php'); $this->register_function("qishi_news_list", "tpl_function_qishi_news_list",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_notice_list.php'); $this->register_function("qishi_notice_list", "tpl_function_qishi_notice_list",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_pageinfo.php'); $this->register_function("qishi_pageinfo", "tpl_function_qishi_pageinfo",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:47 CST */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta property="qc:admins" content="25246775336201705456375" />
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" /><?php echo tpl_function_qishi_pageinfo(array('set' => "列表名:page,调用:QS_index"), $this);?>
<title><?php echo $this->_vars['page']['title']; ?>
按时</title>
<meta name="description" content="<?php echo $this->_vars['page']['description']; ?>
">
<meta name="keywords" content="<?php echo $this->_vars['page']['keywords']; ?>
">
<meta http-equiv="X-UA-Compatible" content="edge">
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<meta name="author" content="找葫芦" />
<meta name="copyright" content="zhaohulu.com" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/common.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/index.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/index_foucs.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.dropDownWidget.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.newindex.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.lazyload.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/jquery.autocomplete.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
data/cache_classify.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->_vars['QISHI']['site_template']; ?>
js/dialog-min-common.js" type="text/javascript" language="javascript"></script>
<link href="<?php echo $this->_vars['QISHI']['site_template']; ?>
css/ui-dialog.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
jQuery(document).ready(function($) {

  //选项卡切换
  $(".n-tab-control>a").each(function(){
    $(this).click(function(){
      $(this).addClass("select"); 
      $(this).siblings("a").removeClass("select");
      var bull_index = $(".n-tab-control>a").index(this);
      $(".news-tab-box>ul").eq(bull_index).show().siblings().hide();
    })
  });
  //登录
  $.get('<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/ajax_user.php?act=loginform', function(data) {
    $("#ajax_login").html(data);
    // 选择登录方式 
    var wxrun = '';
    $('.loginicon').toggle(function(){
      $('#pcLogin').hide();
      $('#codeLogin').show();
      $('#login-box h1').html('微信登录');
      $(this).attr('title', '用户名登录');
      $(this).removeClass('wx').addClass('pc');
      <?php if ($this->_vars['QISHI']['weixin_apiopen'] == '1' && $this->_vars['QISHI']['weixin_scan_login'] == '1' && $_SESSION['username'] == ''): ?>
      wxrun = window.setInterval(run, 5000);
      function run(){
          $.get("<?php echo $this->_vars['QISHI']['site_dir']; ?>
m/login.php?act=waiting_weixin_login",function(data){
              if(data=="1"){
                 window.location="<?php echo $this->_vars['QISHI']['site_dir']; ?>
";
              }
          });
      }
      <?php endif; ?>
    }, function(){
      $('#pcLogin').show();
      $('#codeLogin').hide();
      $('#login-box h1').html('会员登录');
      $(this).attr('title', '微信登录');
      $(this).removeClass('pc').addClass('wx');
      <?php if ($this->_vars['QISHI']['weixin_apiopen'] == '1' && $this->_vars['QISHI']['weixin_scan_login'] == '1' && $_SESSION['username'] == ''): ?>
      window.clearInterval(wxrun);
      <?php endif; ?>
    });
  });
  // 左侧下拉
  $.dropDownWidget(".job-sort-wrap");
  // 首页的一些js
  index("<?php echo $this->_vars['QISHI']['site_dir']; ?>
","<?php echo $this->_vars['QISHI']['site_template']; ?>
");
  //
  $('.floor-item:first').find('.floor-title').css({'margin-top':5});
  $(".core-function").live('click', function(event) {
    window.location.href = $(this).attr("code");
  });
  if("<?php echo $this->_vars['redirect_to_subsite']; ?>
"){
    dialog({
          title: '系统提示',
          content: '亲爱的用户您好：<br />我们检测到您所在地区为 <font color="#ff6600"><?php echo $this->_vars['redirect_disname']; ?>
</font>，建议您切换至<br /> <a href="<?php echo $this->_vars['redirect_url']; ?>
"><font color="#ff6600"><?php echo $this->_vars['redirect_sitename']; ?>
</font></a>，让我们为您提供更准确的职位信息。',
          okValue:'同意',
          ok: function () {
            window.location.href="<?php echo $this->_vars['redirect_url']; ?>
";
          },
          cancelValue:'取消',
          cancel: function () {
          }
      }).showModal();
  }
});
</script>
</head>
<body <?php if ($this->_vars['QISHI']['body_bgimg']): ?>style="background:url(<?php echo $this->_vars['QISHI']['site_domain'];  echo $this->_vars['QISHI']['site_dir']; ?>
data/<?php echo $this->_vars['QISHI']['updir_images']; ?>
/<?php echo $this->_vars['QISHI']['body_bgimg']; ?>
) repeat-x center 38px;"<?php endif; ?>>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<!-- 主体 -->
<div class="container-index">
  <div class="complex-main clearfix">
    <div class="complex-left f-left">
      <div class="job-sort-wrap">
        <div class="job-sort-control">全部职位分类<i class="sotr-icon"></i></div>
        <div class="job-sort-list"></div>
        <div class="leftmenu_box"></div>
      </div>
      <div class="bolck-nav clearfix">
        <a class="b-nav-item f-left" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
jobs" target="_blank">
          <i class="b-nav-icon icon1"></i>
          <p>找工作</p>
        </a>
        <a class="b-nav-item f-left" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
resume" target="_blank">
          <i class="b-nav-icon icon2"></i>
          <p>找人才</p>
        </a>
        <a class="b-nav-item f-left" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
user/company/company_jobs.php?act=addjobs" target="_blank">
          <i class="b-nav-icon icon9"></i>
          <p>发职位</p>
        </a>
        <a class="b-nav-item f-left" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
user/personal/personal_resume.php?act=make1" target="_blank">
          <i class="b-nav-icon icon4"></i>
          <p>创简历</p>
        </a>
        <a class="b-nav-item f-left" href="<?php echo $this->_run_modifier("QS_simplelist", 'qishi_url', 'plugin', 1); ?>
" target="_blank">
          <i class="b-nav-icon icon5"></i>
          <p>微商圈</p>
        </a>
        <a class="b-nav-item f-left" href="<?php echo $this->_run_modifier("QS_hrtoolsindex", 'qishi_url', 'plugin', 1); ?>
" target="_blank">
          <i class="b-nav-icon icon6"></i>
          <p>HR工具</p>
        </a>
        <a class="b-nav-item f-left" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
company" target="_blank">
          <i class="b-nav-icon icon3"></i>
          <p>找名企</p>
        </a>
        <a class="b-nav-item f-left" href="<?php echo $this->_run_modifier("QS_jobfairlist", 'qishi_url', 'plugin', 1); ?>
" target="_blank">
          <i class="b-nav-icon icon7"></i>
          <p>招聘会</p>
        </a>
      </div>
      <div class="news-tab">
        <div class="n-tab-control clearfix">
          <a href="javascript:;" class="f-left tab-ctrl select">公告</a>
          <a href="javascript:;" class="f-left tab-ctrl">资讯</a>
          <a href="javascript:;" class="f-left tab-ctrl">帮助</a>
        </div>
        <div class="news-tab-box">
          <!-- 公告 -->
          <ul>
            <?php echo tpl_function_qishi_notice_list(array('set' => "列表名:notice,显示数目:6,标题长度:12,分类:1,填补字符:..."), $this);?>
            <?php if (count((array)$this->_vars['notice'])): foreach ((array)$this->_vars['notice'] as $this->_vars['list']): ?>
            <li><i class="tab-icon"></i><a href="<?php echo $this->_vars['list']['url']; ?>
" target="_blank" title="<?php echo $this->_vars['list']['title_']; ?>
" class="underline"><?php echo $this->_vars['list']['title']; ?>
</a></li>
            <?php endforeach; endif; ?>
          </ul>
          <!-- 资讯 -->
          <ul style="display: none;">
            <?php echo tpl_function_qishi_news_list(array('set' => "列表名:news,显示数目:6,标题长度:12,分类:1,填补字符:...,排序:id>desc"), $this);?>
            <?php if (count((array)$this->_vars['news'])): foreach ((array)$this->_vars['news'] as $this->_vars['list']): ?>
            <li><i class="tab-icon"></i><a href="<?php echo $this->_vars['list']['url']; ?>
" target="_blank" title="<?php echo $this->_vars['list']['title_']; ?>
" class="underline"><?php echo $this->_vars['list']['title']; ?>
</a></li>
            <?php endforeach; endif; ?>
          </ul>
          <!-- 帮助 -->
          <ul style="display: none;">
            <?php echo tpl_function_qishi_help_list(array('set' => "列表名:help,显示数目:7,标题长度:12,填补字符:..."), $this);?>
            <?php if (count((array)$this->_vars['help'])): foreach ((array)$this->_vars['help'] as $this->_vars['list']): ?>
            <li><i class="tab-icon"></i><a href="<?php echo $this->_vars['list']['url']; ?>
" target="_blank" title="<?php echo $this->_vars['list']['title_']; ?>
" class="underline"><?php echo $this->_vars['list']['title']; ?>
</a></li>
            <?php endforeach; endif; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="complex-center f-left">
      <!-- 搜索 -->
      <div class="search-wrap clearfix">
        <div class="search-box f-left">
          <div class="search-type f-left">
            <div title="找工作" code="QS_jobslist" data="请输入职位名称或企业名称" class="search-type-show"><span>找工作</span><i class="search-icon"></i></div>
            <div title="找人才" code="QS_resumelist" data="请输入简历关键字" class="search-type-drop"><a href="javascript:;">找人才</a></div>
          </div>
          <div class="search-text f-left">
            <input type="text" name="keyForIndexSearch" id="keyForIndexSearch" placeholder="请输入职位名称或企业名称" />
          </div>
        </div>
        <div class="search-submit f-left"><input type="button" name="btnForIndexSearch" id="btnForIndexSearch" code="QS_jobslist" value="搜索" class="search-submit" /></div>
        <input type="hidden" name="citycategory" id="citycategory" value="">
        <!-- 工作地区弹出框 -->
        <div class="aui_outer" id="aui_outer_city">
          <table class="aui_border">
            <tbody>
              <tr>
                <td class="aui_c">
                  <div class="aui_inner">
                    <table class="aui_dialog">
                      <tbody>
                        <tr>
                          <td class="aui_main">
                            <div class="aui_content" style="padding: 0px;">
                              <div class="LocalDataMultiC" style="width:623px;">
                                <div class="selector-header"><span class="selector-title">选择地区</span><div></div><span id="ct-selector-save" class="selector-save">确定</span><span class="selector-close">X</span><div class="clear"></div></div>

                                <div class="data-row-head"><div class="data-row"><div class="data-row-side data-row-side-c">最多选 <strong class="text-warning">3</strong> 项&nbsp;&nbsp;已选 <strong id="arscity" class="text-warning">0</strong> 项</div><div id="result-list-city" class="result-list data-row-side-ra"></div></div><div class="cla"></div></div>
                                <div class="data-row-list data-row-main" id="city_list">
                                  <!-- 列表内容 -->
                                </div>
                              </div>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- 工作地区弹出框 End-->
      </div>
      <!-- 搜索结束 -->
      <div class="swipe-wrap">
        <?php echo tpl_function_qishi_ad(array('set' => "显示数目:6,调用名称:QS_indexfocus,列表名:ad"), $this);?>
        <div id="playBox">
          <div class="pre"></div>
          <div class="next"></div>
          <div class="smalltitle">
            <ul>
              <?php if (isset($this->_sections['list'])) unset($this->_sections['list']);
$this->_sections['list']['loop'] = is_array($this->_vars['ad']) ? count($this->_vars['ad']) : max(0, (int)$this->_vars['ad']);
$this->_sections['list']['name'] = 'list';
$this->_sections['list']['show'] = true;
$this->_sections['list']['max'] = $this->_sections['list']['loop'];
$this->_sections['list']['step'] = 1;
$this->_sections['list']['start'] = $this->_sections['list']['step'] > 0 ? 0 : $this->_sections['list']['loop']-1;
if ($this->_sections['list']['show']) {
	$this->_sections['list']['total'] = $this->_sections['list']['loop'];
	if ($this->_sections['list']['total'] == 0)
		$this->_sections['list']['show'] = false;
} else
	$this->_sections['list']['total'] = 0;
if ($this->_sections['list']['show']):

		for ($this->_sections['list']['index'] = $this->_sections['list']['start'], $this->_sections['list']['iteration'] = 1;
			 $this->_sections['list']['iteration'] <= $this->_sections['list']['total'];
			 $this->_sections['list']['index'] += $this->_sections['list']['step'], $this->_sections['list']['iteration']++):
$this->_sections['list']['rownum'] = $this->_sections['list']['iteration'];
$this->_sections['list']['index_prev'] = $this->_sections['list']['index'] - $this->_sections['list']['step'];
$this->_sections['list']['index_next'] = $this->_sections['list']['index'] + $this->_sections['list']['step'];
$this->_sections['list']['first']	  = ($this->_sections['list']['iteration'] == 1);
$this->_sections['list']['last']	   = ($this->_sections['list']['iteration'] == $this->_sections['list']['total']);
?>
              <li <?php if ($this->_sections['list']['first']): ?>class="thistitle"<?php endif; ?>></li>
              <?php endfor; endif; ?>
            </ul>
          </div>
          <ul class="oUlplay">
             <?php if (count((array)$this->_vars['ad'])): foreach ((array)$this->_vars['ad'] as $this->_vars['list']): ?>
             <li><a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img src="<?php echo $this->_vars['list']['img_path']; ?>
" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" border="0" width="610" height="270"/></a></li>
             <?php endforeach; endif; ?>
          </ul>
        </div>
      </div>
      <div class="block-ad-wrap clearfix lazyload">
        <?php echo tpl_function_qishi_ad(array('set' => "显示数目:6,调用名称:QS_indexrecommend,列表名:ad"), $this);?>
        <?php if ($this->_vars['ad']): ?>
          <?php if (count((array)$this->_vars['ad'])): foreach ((array)$this->_vars['ad'] as $this->_vars['list']): ?>
          <?php if ($this->_vars['list']['img_uid'] > 0): ?>
          <div class="block-ad-item f-left">
            <div class="block-ad-logo"><a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img original="<?php echo $this->_vars['list']['img_path']; ?>
" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/index/84.gif" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" width="202" height="81" border="0" /></a></div>
            <div class="block-ad-info">
              <h3><a href="<?php echo $this->_vars['list']['company_url']; ?>
" target="_blank"><?php echo $this->_vars['list']['companyname']; ?>
</a></h3>
              <p><a href="<?php echo $this->_vars['list']['jobs']['0']['jobs_url']; ?>
" target="_blank"><?php echo $this->_vars['list']['jobs']['0']['jobs_name']; ?>
</a></p>
            </div>
          </div>
          <?php else: ?>
          <div class="block-ad-item f-left">
            <a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank">
              <img original="<?php echo $this->_vars['list']['img_path']; ?>
" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/index/84.jpg" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" width="202" height="153" border="0" />
            </a>
          </div>
          <?php endif; ?>
          <?php endforeach; endif; ?>
        <?php endif; ?>
      </div>
    </div>
    <div class="complex-right f-left">
      <div class="login-block" id="ajax_login">
        <h4>会员登录</h4>
        <div class="login-wrap">
          <div class="login-item">
            <div class="login-text-box clearfix"><i class="login-icon user f-left"></i><div class="login-input f-left"><input type="text" name="" id="" placeholder="邮箱/手机号/用户名" /></div></div>
          </div>
          <div class="login-item">
            <div class="login-text-box clearfix"><i class="login-icon pass f-left"></i><div class="login-input f-left"><input type="password" name="" id="" placeholder="请输入密码" /></div></div>
          </div>
          <div class="login-item clearfix">
            <label class="auto-login f-left"><input type="checkbox" name="" id="" />自动登录</label>
            <a href="" class="forget underline f-right">忘记密码？</a>
          </div>
          <div class="login-item clearfix">
            <div class="login-btn-box f-left"><input type="button" value="立即登录" class="index-login-btn" /></div>
            <div class="f-left"><input type="button" value="免费注册" class="index-reg-btn" /></div>
          </div>
          <div class="third-login clearfix">
            <span class="f-left">其他账户登录：</span>
            <a href="" class="third-icon qq f-left"></a><a href="" class="third-icon sina f-left"></a><a href="" class="third-icon taobao f-left"></a>
          </div>
        </div>
      </div>
      <div class="urgent-block" id="emergencybox">
        <div class="urgent-title clearfix">
          <h4 class="f-left">紧急招聘</h4>
          <a href="<?php echo $this->_run_modifier("QS_jobs", 'qishi_url', 'plugin', 1); ?>
" class="underline f-right" target="_blank">更多>></a>
        </div>
        <ul class="urgent-list">
          <?php echo tpl_function_qishi_jobs_list(array('set' => "列表名:jobs,显示数目:10,职位名长度:12,企业名长度:12,紧急招聘:1,排序:refreshtime>desc"), $this);?>
          <?php if (count((array)$this->_vars['jobs'])): foreach ((array)$this->_vars['jobs'] as $this->_vars['list']): ?>
          <li class="clearfix"><a href="<?php echo $this->_vars['list']['company_url']; ?>
" class="u-com f-left underline" target="_blank"><?php echo $this->_vars['list']['companyname']; ?>
</a><a href="<?php echo $this->_vars['list']['jobs_url']; ?>
" class="u-job f-left underline" title="<?php echo $this->_vars['list']['jobs_name_']; ?>
" target="_blank"><?php echo $this->_vars['list']['jobs_name']; ?>
</a></li>
          <?php endforeach; endif; ?>
        </ul>
      </div>
    </div>
  </div>
  <!-- 广告位集中区域 -->
  <div class="ad-area">
    <!-- 1198*58 广告  -->
    <?php echo tpl_function_qishi_ad(array('set' => "显示数目:3,调用名称:QS_indextopimg,列表名:ad,文字长度:12"), $this);?>
    <?php if ($this->_vars['ad']): ?>
    <?php if (count((array)$this->_vars['ad'])): foreach ((array)$this->_vars['ad'] as $this->_vars['list']): ?>
    <div class="ad-row clearfix lazyload">
      <div class="ad-item ad-full f-left"><a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img original="<?php echo $this->_vars['list']['img_path']; ?>
" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/index/84.gif" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" width="1198" height="58" border="0" /></a></div>
    </div>
    <?php endforeach; endif; ?>
    <?php endif; ?>
    <!-- 392*58 广告  格子广告-->
    <?php echo tpl_function_qishi_ad(array('set' => "显示数目:6,调用名称:QS_indexcentreimg,列表名:ad,文字长度:12"), $this);?>
    <?php if ($this->_vars['ad']): ?>
    <div class="ad-row clearfix lazyload">
      <?php if (count((array)$this->_vars['ad'])): foreach ((array)$this->_vars['ad'] as $this->_vars['list']): ?>
      <div class="ad-item ad-31 f-left comimgtip">
        <a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img original="<?php echo $this->_vars['list']['img_path']; ?>
" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/index/84.gif" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" width="392" height="58" border="0" /></a>
        <?php if ($this->_vars['list']['jobs']): ?>
        <!-- 鼠标至上显示 -->
        <div class="ad-more-info info31" style="display: none;">
          <div class="ad-placeholder"></div>
          <ul class="ad-job-list">
            <?php if (count((array)$this->_vars['list']['jobs'])): foreach ((array)$this->_vars['list']['jobs'] as $this->_vars['jobs_li']): ?>
            <li class="clearfix"><div class="jobname f-left"><a href="<?php echo $this->_vars['jobs_li']['jobs_url']; ?>
" class="underline" target="_blank"><?php echo $this->_vars['jobs_li']['jobs_name']; ?>
</a></div><div class="jobpay f-left"><span><?php echo $this->_vars['jobs_li']['wage_cn']; ?>
</span></div><div class="jobnarea f-left"><?php echo $this->_vars['jobs_li']['district_cn']; ?>
</div></li>
            <?php endforeach; endif; ?>
          </ul>
          <div class="ad-com-info">
            <div class="companyname"><a href="<?php echo $this->_vars['list']['company_url']; ?>
" class="underline" target="_blank"><?php echo $this->_vars['list']['companyname']; ?>
</a></div>
            <p><?php echo $this->_vars['list']['briefly']; ?>
</p>
          </div>
          <a href="<?php echo $this->_vars['list']['company_url']; ?>
" class="ad-more" target="_blank">查看更多内容>></a>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; endif; ?>
    </div>
    <?php endif; ?>
    <!-- 230x58 广告  格子广告-->
    <?php echo tpl_function_qishi_ad(array('set' => "显示数目:10,调用名称:QS_indexcentreimg_230x58,列表名:ad,文字长度:12"), $this);?>
    <?php if ($this->_vars['ad']): ?>
    <div class="ad-row a23058d clearfix lazyload">
      <?php if (count((array)$this->_vars['ad'])): foreach ((array)$this->_vars['ad'] as $this->_vars['list']): ?>
      <div class="ad-item ad-51 f-left comimgtip">
        <a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img original="<?php echo $this->_vars['list']['img_path']; ?>
" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/index/84.gif" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" width="230" height="58" border="0" /></a>
        <?php if ($this->_vars['list']['jobs']): ?>
        <!-- 鼠标至上显示 -->
        <div class="ad-more-info info51" style="display: none;">
          <div class="ad-placeholder"></div>
          <ul class="ad-job-list">
            <?php if (count((array)$this->_vars['list']['jobs'])): foreach ((array)$this->_vars['list']['jobs'] as $this->_vars['jobs_li']): ?>
            <li class="clearfix"><div class="jobname f-left"><a href="<?php echo $this->_vars['jobs_li']['jobs_url']; ?>
" class="underline" target="_blank"><?php echo $this->_vars['jobs_li']['jobs_name']; ?>
</a></div><div class="jobpay f-left"><span><?php echo $this->_vars['jobs_li']['wage_cn']; ?>
</span></div></li>
            <?php endforeach; endif; ?>
          </ul>
          <div class="ad-com-info ad-com-info-w">
            <div class="companyname"><a href="<?php echo $this->_vars['list']['company_url']; ?>
" class="underline" target="_blank"><?php echo $this->_vars['list']['companyname']; ?>
</a></div>
            <p><?php echo $this->_vars['list']['briefly']; ?>
</p>
          </div>
          <a href="<?php echo $this->_vars['list']['company_url']; ?>
" class="ad-more" target="_blank">查看更多内容>></a>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <!-- 广告位集中区域结束 -->
    <!-- 列表-最新职位 -->
    <style>.index-data-container{border:1px solid #ededed;background-color: #fff;margin-top: 15px;}
    .index-data-container .index-data-title{padding:17px 13px;line-height: 22px;}
    .index-data-container .index-data-title.photo{padding:17px 20px;}
    .index-data-container .index-data-title h3{font-size: 16px;color:#333;font-weight: normal;}
    .index-data-container .index-data-title a{font-size: 14px;color:#999;}
    .new-job-list{padding:0 13px 10px;font-size: 14px;}
    .new-job-list .n-job-item{width: 390px;overflow: hidden;white-space: nowrap;text-overflow:ellipsis;margin-bottom: 12px;line-height: 18px;}
    .new-job-list .n-job-item .n-job-com{margin-right: 10px;color:#0178c8;vertical-align: middle;}
    .new-job-list .n-job-item .n-job-com img{vertical-align: middle;}
    .new-job-list .n-job-item .n-job{margin-right: 4px;color:#999;}
        </style>
    <div class="index-data-container">
        <div class="index-data-title clearfix">
            <h3 class="f-left">悬赏招聘</h3>
            <a href="<?php echo $this->_run_modifier("QS_jobslist", 'qishi_url', 'plugin', 1); ?>
" class="f-right underline" target="_blank">更多>></a>
         </div>
        <ul class="new-job-list clearfix">
            <?php echo tpl_function_qishi_jobs_list(array('set' => "列表名:jobs,显示数目:10,职位名长度:12,企业名长度:12,悬赏招聘:1,排序:rtime>desc"), $this);?>
            <?php if (count((array)$this->_vars['jobs'])): foreach ((array)$this->_vars['jobs'] as $this->_vars['list']): ?>
            <?php echo tpl_function_qishi_jobs_list(array('set' => "列表名:com_jobs_all,显示数目:3,会员UID:" . $this->_vars['list']['uid'] . ""), $this);?>
            <li class="n-job-item f-left"><a href="<?php echo $this->_vars['list']['company_url']; ?>
" target="_blank" class="n-job-com underline"><?php echo $this->_vars['list']['companyname']; ?>
</a>
                <a href="<?php echo $this->_vars['list']['jobs_url']; ?>
" class="n-job underline" target="_blank"><?php echo $this->_vars['list']['jobs_name_']; ?>
</a>悬赏金额<em style="color:red"><?php echo $this->_vars['list']['block_balance']; ?>
</em>
            </li>
             <?php endforeach; endif; ?>


        </ul>
    </div>

    <!-- 列表-最新职位结束 -->
  <!-- 列表-推荐职位 -->
  <div class="index-data-wrap index-data-wrap-i7">
    <div class="blue-line"></div>
    <div class="data-title-box clearfix">
      <h4 class="f-left">推荐职位<span>Recommended Job</span></h4>
      <a href="<?php echo $this->_run_modifier("QS_helplist,id:10", 'qishi_url', 'plugin', 1); ?>
" class="f-right underline" target="_blank">我是招聘单位，我想出现在这里</a>
    </div>
    <div class="famous-list clearfix">
      <?php echo tpl_function_qishi_companyjobs_list(array('set' => "列表名:comjob_recommend,显示数目:12,显示职位:3,推荐:1,统计职位:1"), $this);?>
      <?php if (count((array)$this->_vars['comjob_recommend'])): foreach ((array)$this->_vars['comjob_recommend'] as $this->_vars['list']): ?>
      <div class="famous-items f-left">
        <i class="fc-icon"></i>
        <div class="famous-com comtip">
          <a href="<?php echo $this->_vars['list']['company_url']; ?>
" class="underline" target="_balnk"><?php echo $this->_vars['list']['companyname'];  if ($this->_vars['QISHI']['operation_mode'] >= "2" && $this->_vars['list']['setmeal_id'] > 1): ?> <img src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
data/setmealimg/<?php echo $this->_vars['list']['setmeal_id']; ?>
.gif" border="0" title="<?php echo $this->_vars['list']['setmeal_name']; ?>
" class="vtip" width='14px' height='17px'/><?php endif; ?></a>
          <div class="famous-more-info" style="display:none;">
            <i class="fmi-icon"></i>
            <div class="fmi-title">招聘岗位</div>
            <ul class="fmi-list">
              <?php echo tpl_function_qishi_jobs_list(array('set' => "列表名:com_jobs_all,显示数目:3,会员UID:" . $this->_vars['list']['uid'] . ""), $this);?>
              <?php if (count((array)$this->_vars['com_jobs_all'])): foreach ((array)$this->_vars['com_jobs_all'] as $this->_vars['job_li']): ?>
              <li class="clearfix">
                <div class="fmi-jobname f-left"><a href="<?php echo $this->_vars['job_li']['jobs_url']; ?>
" class="underline" target="_balnk"><?php echo $this->_vars['job_li']['jobs_name']; ?>
</a></div><div class="fmi-time f-left"><span><?php echo $this->_vars['job_li']['refreshtime_cn']; ?>
</span></div>
              </li>
              <?php endforeach; endif; ?>
            </ul>
            <p>该企业共有<?php echo $this->_vars['list']['jobs_num']; ?>
个职位，<a href="<?php echo $this->_run_modifier("QS_companyjobs,id:" . $this->_vars['list']['company_id'] . "", 'qishi_url', 'plugin', 1); ?>
" target="_balnk" class="underline">查看全部</a></p>
          </div>
        </div>
        <div class="famous-job">
          <?php if (count((array)$this->_vars['list']['jobs'])): foreach ((array)$this->_vars['list']['jobs'] as $this->_vars['jobs_li']): ?>
          <span><a href="<?php echo $this->_vars['jobs_li']['jobs_url']; ?>
" class="underline" target="_balnk"><?php echo $this->_vars['jobs_li']['jobs_name']; ?>
</a></span>
          <?php endforeach; endif; ?>
        </div>
        
      </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
  <!-- 列表-名企招聘结束 -->
  <!-- 1198*58 广告  -->
  <?php echo tpl_function_qishi_ad(array('set' => "显示数目:3,调用名称:QS_indexcenter,列表名:ad,文字长度:12"), $this);?>
  <?php if ($this->_vars['ad']): ?>
  <div class="ad-area">
  <?php if (count((array)$this->_vars['ad'])): foreach ((array)$this->_vars['ad'] as $this->_vars['list']): ?>
  <div class="ad-row clearfix lazyload">
    <div class="ad-item ad-full f-left"><a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img original="<?php echo $this->_vars['list']['img_path']; ?>
" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/index/84.gif" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" width="1198" height="58" border="0" /></a></div>
  </div>
  <?php endforeach; endif; ?>
  </div>
  <?php endif; ?>
  <!-- 列表-最新职位 -->
  <div class="index-data-wrap">
    <div class="blue-line"></div>
    <div class="data-title-box clearfix">
      <h4 class="f-left">最新职位<span>Latest Job</span></h4>
      <a href="<?php echo $this->_run_modifier("QS_jobslist", 'qishi_url', 'plugin', 1); ?>
" class="f-right underline" target="_blank">更多>></a>
    </div>
    <div class="newest-list clearfix">
      <?php echo tpl_function_qishi_companyjobs_list(array('set' => "列表名:jobs,显示数目:40,职位名长度:12,显示职位:1,企业名长度:12,排序:rtime>desc"), $this);?>
      <?php if (count((array)$this->_vars['jobs'])): foreach ((array)$this->_vars['jobs'] as $this->_vars['list']): ?>
      <div class="newest-items f-left">
        <i class="nc-icon"></i>
        <a href="<?php echo $this->_vars['list']['company_url']; ?>
" class="newest-com underline" target="_blank"><?php echo $this->_vars['list']['companyname']; ?>
</a>
        <?php if (count((array)$this->_vars['list']['jobs'])): foreach ((array)$this->_vars['list']['jobs'] as $this->_vars['li']): ?>
        <a href="<?php echo $this->_vars['li']['jobs_url']; ?>
" class="newest-job underline" target="_blank"><?php echo $this->_vars['li']['jobs_name']; ?>
</a>
        <?php endforeach; endif; ?>
      </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
  <!-- 列表-最新职位结束 -->
  <!-- 1198*58 广告  -->
  <?php echo tpl_function_qishi_ad(array('set' => "显示数目:3,调用名称:QS_indexfootbanner,列表名:ad,文字长度:12"), $this);?>
  <?php if ($this->_vars['ad']): ?>
  <div class="ad-area">
  <?php if (count((array)$this->_vars['ad'])): foreach ((array)$this->_vars['ad'] as $this->_vars['list']): ?>
  <div class="ad-row clearfix lazyload">
    <div class="ad-item ad-full f-left"><a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img original="<?php echo $this->_vars['list']['img_path']; ?>
" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/index/84.gif" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" width="1198" height="58" border="0" /></a></div>
  </div>
  <?php endforeach; endif; ?>
  </div>
  <?php endif; ?>
  <!-- 列表-照片简历 -->
  <div class="index-data-wrap">
    <div class="blue-line"></div>
    <div class="data-title-box clearfix">
      <h4 class="f-left">照片简历<span>Photo Resume</span></h4>
      <a href="<?php echo $this->_run_modifier("QS_resumelist,photo:1", 'qishi_url', 'plugin', 1); ?>
" class="f-right underline" target="_blank">更多>></a>
    </div>
    <div class="photo-list clearfix">
      <?php echo tpl_function_qishi_resume_list(array('set' => "列表名:resume,显示数目:7,照片:1,意向职位长度:14,填补字符:...,排序:rtime>desc"), $this);?>
      <?php if (count((array)$this->_vars['resume'])): foreach ((array)$this->_vars['resume'] as $this->_vars['list']): ?>
        <div class="photo-items f-left">
          <div class="avater-box">
            <div class="avater"><a href="<?php echo $this->_vars['list']['resume_url']; ?>
" target="_blank"><img src="<?php echo $this->_vars['list']['photosrc']; ?>
"  width="70" height="70" border="0"/></a></div>
            <p><a href="<?php echo $this->_vars['list']['resume_url']; ?>
" target="_blank" class="underline"><?php echo $this->_vars['list']['fullname']; ?>
</a></p>
          </div>
          <div class="photo-info">
            <p><?php echo $this->_vars['list']['education_cn']; ?>
,<?php echo $this->_vars['list']['experience_cn']; ?>
</p>
            <p><?php echo $this->_vars['list']['intention_jobs']; ?>
</p>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
  <!-- 列表-照片简历结束 -->
  <!-- 列表-职位导航 -->
  <div class="index-data-wrap">
    <div class="blue-line"></div>
    <div class="data-title-box clearfix">
      <h4 class="f-left">职位导航<span>Jobs Navigation</span></h4>
    </div>
    <div class="job-build">
      <!-- 楼层1  -->
      <div class="floor-item">
        <div class="floor-title"><em>1F</em><span><?php echo $this->_run_modifier("QS_jobs,76", 'qishi_categoryname', 'plugin', 1); ?>
&nbsp;·&nbsp;<?php echo $this->_run_modifier("QS_jobs,77", 'qishi_categoryname', 'plugin', 1); ?>
</span></div>
        <div class="floor-box clearfix">
          <!-- 分类 -->
          <div class="floor-sort f-left">
            <?php echo tpl_function_qishi_get_classify(array('set' => "列表名:subcate,类型:QS_jobs_floor,显示数目:20,id:76_77"), $this);?>
            <?php if (count((array)$this->_vars['subcate'])): foreach ((array)$this->_vars['subcate'] as $this->_vars['list']): ?>
            <a href="<?php echo $this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier("QS_jobslist,jobcategory:", 'cat', 'plugin', 1, 74), 'cat', 'plugin', 1, "."), 'cat', 'plugin', 1, $this->_vars['list']['parentid']), 'cat', 'plugin', 1, "."), 'cat', 'plugin', 1, $this->_vars['list']['id']), 'qishi_url', 'plugin', 1); ?>
" class="f-sort-item f-left" target="_blank"><?php echo $this->_vars['list']['categoryname']; ?>
</a>
            <?php endforeach; endif; ?>
          </div>
          <!-- 职位 -->
          <div class="floor-jobs f-left">
            <?php echo tpl_function_qishi_companyjobs_list(array('set' => "列表名:comjobs,显示数目:10,显示职位:3,职位分类:76_77"), $this);?>
            <?php if (count((array)$this->_vars['comjobs'])): foreach ((array)$this->_vars['comjobs'] as $this->_vars['list']): ?>
            <div class="f-job-row">
              <a href="<?php echo $this->_vars['list']['company_url']; ?>
" class="f-job-com underline" target="_blank"><?php echo $this->_vars['list']['companyname']; ?>
</a>
              <?php if (count((array)$this->_vars['list']['jobs'])): foreach ((array)$this->_vars['list']['jobs'] as $this->_vars['li']): ?>
              <a href="<?php echo $this->_vars['li']['jobs_url']; ?>
" class="f-job-name underline" target="_blank"><?php echo $this->_vars['li']['jobs_name']; ?>
</a>
              <?php endforeach; endif; ?>
            </div>
            <?php endforeach; endif; ?>

          </div>
          <!-- 广告 楼层广告1 -->
          <div class="floor-ad-box f-left lazyload">
            <?php echo tpl_function_qishi_ad(array('set' => "显示数目:4,调用名称:QS_floor_img1,列表名:ad,文字长度:12"), $this);?>
            <?php if ($this->_vars['ad']): ?>
            <?php if (count((array)$this->_vars['ad'])): foreach ((array)$this->_vars['ad'] as $this->_vars['list']): ?>
            <div class="floor-ad"><a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img original="<?php echo $this->_vars['list']['img_path']; ?>
" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/index/84.gif" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" width="378" height="60" border="0" /></a></div>
            <?php endforeach; endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <!-- 楼层 2 -->
      <div class="floor-item">
        <div class="floor-title"><em>2F</em><span><?php echo $this->_run_modifier("QS_jobs,3", 'qishi_categoryname', 'plugin', 1); ?>
&nbsp;·&nbsp;<?php echo $this->_run_modifier("QS_jobs,5", 'qishi_categoryname', 'plugin', 1); ?>
&nbsp;·&nbsp;<?php echo $this->_run_modifier("QS_jobs,6", 'qishi_categoryname', 'plugin', 1); ?>
</span></div>
        <div class="floor-box clearfix">
          <!-- 分类 -->
          <div class="floor-sort f-left">
            <?php echo tpl_function_qishi_get_classify(array('set' => "列表名:subcate,类型:QS_jobs_floor,显示数目:20,id:3_5_6"), $this);?>
            <?php if (count((array)$this->_vars['subcate'])): foreach ((array)$this->_vars['subcate'] as $this->_vars['list']): ?>
            <a href="<?php echo $this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier("QS_jobslist,jobcategory:", 'cat', 'plugin', 1, 1), 'cat', 'plugin', 1, "."), 'cat', 'plugin', 1, $this->_vars['list']['parentid']), 'cat', 'plugin', 1, "."), 'cat', 'plugin', 1, $this->_vars['list']['id']), 'qishi_url', 'plugin', 1); ?>
" class="f-sort-item f-left" target="_blank"><?php echo $this->_vars['list']['categoryname']; ?>
</a>
            <?php endforeach; endif; ?>
          </div>
          <!-- 职位 -->
          <div class="floor-jobs f-left">
            <?php echo tpl_function_qishi_companyjobs_list(array('set' => "列表名:comjobs,显示数目:10,显示职位:3,职位分类:3_5_6"), $this);?>
            <?php if (count((array)$this->_vars['comjobs'])): foreach ((array)$this->_vars['comjobs'] as $this->_vars['list']): ?>
            <div class="f-job-row">
              <a href="<?php echo $this->_vars['list']['company_url']; ?>
" class="f-job-com underline" target="_blank"><?php echo $this->_vars['list']['companyname']; ?>
</a>
              <?php if (count((array)$this->_vars['list']['jobs'])): foreach ((array)$this->_vars['list']['jobs'] as $this->_vars['li']): ?>
              <a href="<?php echo $this->_vars['li']['jobs_url']; ?>
" class="f-job-name underline" target="_blank"><?php echo $this->_vars['li']['jobs_name']; ?>
</a>
              <?php endforeach; endif; ?>
            </div>
            <?php endforeach; endif; ?>

          </div>
          <!-- 广告 楼层广告1 -->
          <div class="floor-ad-box f-left lazyload">
            <?php echo tpl_function_qishi_ad(array('set' => "显示数目:4,调用名称:QS_floor_img2,列表名:ad,文字长度:12"), $this);?>
            <?php if ($this->_vars['ad']): ?>
            <?php if (count((array)$this->_vars['ad'])): foreach ((array)$this->_vars['ad'] as $this->_vars['list']): ?>
            <div class="floor-ad"><a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img original="<?php echo $this->_vars['list']['img_path']; ?>
" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/index/84.gif" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" width="378" height="60" border="0" /></a></div>
            <?php endforeach; endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <!-- 楼层 3 -->
      <div class="floor-item">
        <div class="floor-title"><em>3F</em><span><?php echo $this->_run_modifier("QS_jobs,117", 'qishi_categoryname', 'plugin', 1); ?>
&nbsp;·&nbsp;<?php echo $this->_run_modifier("QS_jobs,120", 'qishi_categoryname', 'plugin', 1); ?>
&nbsp;·&nbsp;<?php echo $this->_run_modifier("QS_jobs,121", 'qishi_categoryname', 'plugin', 1); ?>
</span></div>
        <div class="floor-box clearfix">
          <!-- 分类 -->
          <div class="floor-sort f-left">
            <?php echo tpl_function_qishi_get_classify(array('set' => "列表名:subcate,类型:QS_jobs_floor,显示数目:20,id:117_120_121"), $this);?>
            <?php if (count((array)$this->_vars['subcate'])): foreach ((array)$this->_vars['subcate'] as $this->_vars['list']): ?>
            <a href="<?php echo $this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier("QS_jobslist,jobcategory:", 'cat', 'plugin', 1, 116), 'cat', 'plugin', 1, "."), 'cat', 'plugin', 1, $this->_vars['list']['parentid']), 'cat', 'plugin', 1, "."), 'cat', 'plugin', 1, $this->_vars['list']['id']), 'qishi_url', 'plugin', 1); ?>
" class="f-sort-item f-left" target="_blank"><?php echo $this->_vars['list']['categoryname']; ?>
</a>
            <?php endforeach; endif; ?>
          </div>
          <!-- 职位 -->
          <div class="floor-jobs f-left">
            <?php echo tpl_function_qishi_companyjobs_list(array('set' => "列表名:comjobs,显示数目:10,显示职位:3,职位分类:117_120_121"), $this);?>
            <?php if (count((array)$this->_vars['comjobs'])): foreach ((array)$this->_vars['comjobs'] as $this->_vars['list']): ?>
            <div class="f-job-row">
              <a href="<?php echo $this->_vars['list']['company_url']; ?>
" class="f-job-com underline" target="_blank"><?php echo $this->_vars['list']['companyname']; ?>
</a>
              <?php if (count((array)$this->_vars['list']['jobs'])): foreach ((array)$this->_vars['list']['jobs'] as $this->_vars['li']): ?>
              <a href="<?php echo $this->_vars['li']['jobs_url']; ?>
" class="f-job-name underline" target="_blank"><?php echo $this->_vars['li']['jobs_name']; ?>
</a>
              <?php endforeach; endif; ?>
            </div>
            <?php endforeach; endif; ?>

          </div>
          <!-- 广告 楼层广告1 -->
          <div class="floor-ad-box f-left lazyload">
            <?php echo tpl_function_qishi_ad(array('set' => "显示数目:4,调用名称:QS_floor_img3,列表名:ad,文字长度:12"), $this);?>
            <?php if ($this->_vars['ad']): ?>
            <?php if (count((array)$this->_vars['ad'])): foreach ((array)$this->_vars['ad'] as $this->_vars['list']): ?>
            <div class="floor-ad"><a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img original="<?php echo $this->_vars['list']['img_path']; ?>
" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/index/84.gif" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" width="378" height="60" border="0" /></a></div>
            <?php endforeach; endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <!-- 楼层 4 -->
      <div class="floor-item">
        <div class="floor-title"><em>4F</em><span><?php echo $this->_run_modifier("QS_jobs,97", 'qishi_categoryname', 'plugin', 1); ?>
&nbsp;·&nbsp;<?php echo $this->_run_modifier("QS_jobs,98", 'qishi_categoryname', 'plugin', 1); ?>
&nbsp;·&nbsp;<?php echo $this->_run_modifier("QS_jobs,99", 'qishi_categoryname', 'plugin', 1); ?>
</span></div>
        <div class="floor-box clearfix">
          <!-- 分类 -->
          <div class="floor-sort f-left">
            <?php echo tpl_function_qishi_get_classify(array('set' => "列表名:subcate,类型:QS_jobs_floor,显示数目:20,id:97_98_99"), $this);?>
            <?php if (count((array)$this->_vars['subcate'])): foreach ((array)$this->_vars['subcate'] as $this->_vars['list']): ?>
            <a href="<?php echo $this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier("QS_jobslist,jobcategory:", 'cat', 'plugin', 1, 96), 'cat', 'plugin', 1, "."), 'cat', 'plugin', 1, $this->_vars['list']['parentid']), 'cat', 'plugin', 1, "."), 'cat', 'plugin', 1, $this->_vars['list']['id']), 'qishi_url', 'plugin', 1); ?>
" class="f-sort-item f-left" target="_blank"><?php echo $this->_vars['list']['categoryname']; ?>
</a>
            <?php endforeach; endif; ?>
          </div>
          <!-- 职位 -->
          <div class="floor-jobs f-left">
            <?php echo tpl_function_qishi_companyjobs_list(array('set' => "列表名:comjobs,显示数目:10,显示职位:3,职位分类:97_98_99"), $this);?>
            <?php if (count((array)$this->_vars['comjobs'])): foreach ((array)$this->_vars['comjobs'] as $this->_vars['list']): ?>
            <div class="f-job-row">
              <a href="<?php echo $this->_vars['list']['company_url']; ?>
" class="f-job-com underline" target="_blank"><?php echo $this->_vars['list']['companyname']; ?>
</a>
              <?php if (count((array)$this->_vars['list']['jobs'])): foreach ((array)$this->_vars['list']['jobs'] as $this->_vars['li']): ?>
              <a href="<?php echo $this->_vars['li']['jobs_url']; ?>
" class="f-job-name underline" target="_blank"><?php echo $this->_vars['li']['jobs_name']; ?>
</a>
              <?php endforeach; endif; ?>
            </div>
            <?php endforeach; endif; ?>

          </div>
          <!-- 广告 楼层广告1 -->
          <div class="floor-ad-box f-left lazyload">
            <?php echo tpl_function_qishi_ad(array('set' => "显示数目:4,调用名称:QS_floor_img4,列表名:ad,文字长度:12"), $this);?>
            <?php if ($this->_vars['ad']): ?>
            <?php if (count((array)$this->_vars['ad'])): foreach ((array)$this->_vars['ad'] as $this->_vars['list']): ?>
            <div class="floor-ad"><a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img original="<?php echo $this->_vars['list']['img_path']; ?>
" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/index/84.gif" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" width="378" height="60" border="0" /></a></div>
            <?php endforeach; endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <!-- 楼层 5 -->
      <div class="floor-item">
        <div class="floor-title"><em>5F</em><span><?php echo $this->_run_modifier("QS_jobs,50", 'qishi_categoryname', 'plugin', 1); ?>
&nbsp;·&nbsp;<?php echo $this->_run_modifier("QS_jobs,51", 'qishi_categoryname', 'plugin', 1); ?>
&nbsp;·&nbsp;<?php echo $this->_run_modifier("QS_jobs,52", 'qishi_categoryname', 'plugin', 1); ?>
</span></div>
        <div class="floor-box clearfix">
          <!-- 分类 -->
          <div class="floor-sort f-left">
            <?php echo tpl_function_qishi_get_classify(array('set' => "列表名:subcate,类型:QS_jobs_floor,显示数目:20,id:50_51_52"), $this);?>
            <?php if (count((array)$this->_vars['subcate'])): foreach ((array)$this->_vars['subcate'] as $this->_vars['list']): ?>
            <a href="<?php echo $this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_run_modifier("QS_jobslist,jobcategory:", 'cat', 'plugin', 1, 49), 'cat', 'plugin', 1, "."), 'cat', 'plugin', 1, $this->_vars['list']['parentid']), 'cat', 'plugin', 1, "."), 'cat', 'plugin', 1, $this->_vars['list']['id']), 'qishi_url', 'plugin', 1); ?>
" class="f-sort-item f-left" target="_blank"><?php echo $this->_vars['list']['categoryname']; ?>
</a>
            <?php endforeach; endif; ?>
          </div>
          <!-- 职位 -->
          <div class="floor-jobs f-left">
            <?php echo tpl_function_qishi_companyjobs_list(array('set' => "列表名:comjobs,显示数目:10,显示职位:3,职位分类:50_51_52"), $this);?>
            <?php if (count((array)$this->_vars['comjobs'])): foreach ((array)$this->_vars['comjobs'] as $this->_vars['list']): ?>
            <div class="f-job-row">
              <a href="<?php echo $this->_vars['list']['company_url']; ?>
" class="f-job-com underline" target="_blank"><?php echo $this->_vars['list']['companyname']; ?>
</a>
              <?php if (count((array)$this->_vars['list']['jobs'])): foreach ((array)$this->_vars['list']['jobs'] as $this->_vars['li']): ?>
              <a href="<?php echo $this->_vars['li']['jobs_url']; ?>
" class="f-job-name underline" target="_blank"><?php echo $this->_vars['li']['jobs_name']; ?>
</a>
              <?php endforeach; endif; ?>
            </div>
            <?php endforeach; endif; ?>

          </div>
          <!-- 广告 楼层广告1 -->
          <div class="floor-ad-box f-left lazyload">
            <?php echo tpl_function_qishi_ad(array('set' => "显示数目:4,调用名称:QS_floor_img5,列表名:ad,文字长度:12"), $this);?>
            <?php if ($this->_vars['ad']): ?>
            <?php if (count((array)$this->_vars['ad'])): foreach ((array)$this->_vars['ad'] as $this->_vars['list']): ?>
            <div class="floor-ad"><a href="<?php echo $this->_vars['list']['img_url']; ?>
" target="_blank"><img original="<?php echo $this->_vars['list']['img_path']; ?>
" src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/index/84.gif" alt="<?php echo $this->_vars['list']['img_explain']; ?>
" width="378" height="60" border="0" /></a></div>
            <?php endforeach; endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- 列表-职位导航结束 -->
  <!-- 列表-职场资讯 -->
  <div class="index-data-wrap">
    <div class="blue-line"></div>
    <div class="data-title-box clearfix">
      <h4 class="f-left">职场资讯<span>Workplace Information</span></h4>
      <a href="<?php echo $this->_run_modifier("QS_news", 'qishi_url', 'plugin', 1); ?>
" class="f-right underline" target="_blank">更多>></a>
    </div>
    <div class="job-news-block clearfix">
      <div class="jn-left f-left">
        <?php echo tpl_function_qishi_news_category(array('set' => "列表名:newscategory,资讯大类:1,显示数目:4"), $this);?>
        <?php if (isset($this->_sections['nclist'])) unset($this->_sections['nclist']);
$this->_sections['nclist']['loop'] = is_array($this->_vars['newscategory']) ? count($this->_vars['newscategory']) : max(0, (int)$this->_vars['newscategory']);
$this->_sections['nclist']['name'] = 'nclist';
$this->_sections['nclist']['show'] = true;
$this->_sections['nclist']['max'] = $this->_sections['nclist']['loop'];
$this->_sections['nclist']['step'] = 1;
$this->_sections['nclist']['start'] = $this->_sections['nclist']['step'] > 0 ? 0 : $this->_sections['nclist']['loop']-1;
if ($this->_sections['nclist']['show']) {
	$this->_sections['nclist']['total'] = $this->_sections['nclist']['loop'];
	if ($this->_sections['nclist']['total'] == 0)
		$this->_sections['nclist']['show'] = false;
} else
	$this->_sections['nclist']['total'] = 0;
if ($this->_sections['nclist']['show']):

		for ($this->_sections['nclist']['index'] = $this->_sections['nclist']['start'], $this->_sections['nclist']['iteration'] = 1;
			 $this->_sections['nclist']['iteration'] <= $this->_sections['nclist']['total'];
			 $this->_sections['nclist']['index'] += $this->_sections['nclist']['step'], $this->_sections['nclist']['iteration']++):
$this->_sections['nclist']['rownum'] = $this->_sections['nclist']['iteration'];
$this->_sections['nclist']['index_prev'] = $this->_sections['nclist']['index'] - $this->_sections['nclist']['step'];
$this->_sections['nclist']['index_next'] = $this->_sections['nclist']['index'] + $this->_sections['nclist']['step'];
$this->_sections['nclist']['first']	  = ($this->_sections['nclist']['iteration'] == 1);
$this->_sections['nclist']['last']	   = ($this->_sections['nclist']['iteration'] == $this->_sections['nclist']['total']);
?>
        <div class="jn-box f-left">
          <div class="jn-img f-left"><a href="<?php echo $this->_run_modifier("QS_newslist,id:" . $this->_vars['newscategory'][$this->_sections['nclist']['index']]['id'] . "", 'qishi_url', 'plugin', 1); ?>
" target="_blank"><img src="<?php echo $this->_vars['QISHI']['site_template']; ?>
images/news<?php echo $this->_sections['nclist']['index']; ?>
.jpg" width="163" height="98" border="0"></a></div>
          <ul class="jn-list f-left">
          <?php echo tpl_function_qishi_news_list(array('set' => "列表名:topnews,显示数目:4,标题长度:18,资讯小类:" . $this->_vars['newscategory'][$this->_sections['nclist']['index']]['id'] . ",摘要长度:36,填补字符:...,排序:id>desc"), $this);?>
          <?php if (count((array)$this->_vars['topnews'])): foreach ((array)$this->_vars['topnews'] as $this->_vars['toplist']): ?>
          <li><i class="jn-icon"></i><a target="_blank" href="<?php echo $this->_vars['toplist']['url']; ?>
" class="underline" title="<?php echo $this->_vars['toplist']['title_']; ?>
" target="_blank"><?php echo $this->_vars['toplist']['title']; ?>
</a></li>
          <?php endforeach; endif; ?>
          </ul>
        </div>
        <?php endfor; endif; ?>
      </div>
      <ol class="jn-right f-left">
        <?php echo tpl_function_qishi_news_list(array('set' => "列表名:news_list,显示数目:8,标题长度:12,填补字符:...,排序:click>desc"), $this);?>
        <?php if (isset($this->_sections['nclist'])) unset($this->_sections['nclist']);
$this->_sections['nclist']['loop'] = is_array($this->_vars['news_list']) ? count($this->_vars['news_list']) : max(0, (int)$this->_vars['news_list']);
$this->_sections['nclist']['name'] = 'nclist';
$this->_sections['nclist']['start'] = (int)1;
$this->_sections['nclist']['show'] = true;
$this->_sections['nclist']['max'] = $this->_sections['nclist']['loop'];
$this->_sections['nclist']['step'] = 1;
if ($this->_sections['nclist']['start'] < 0)
	$this->_sections['nclist']['start'] = max($this->_sections['nclist']['step'] > 0 ? 0 : -1, $this->_sections['nclist']['loop'] + $this->_sections['nclist']['start']);
else
	$this->_sections['nclist']['start'] = min($this->_sections['nclist']['start'], $this->_sections['nclist']['step'] > 0 ? $this->_sections['nclist']['loop'] : $this->_sections['nclist']['loop']-1);
if ($this->_sections['nclist']['show']) {
	$this->_sections['nclist']['total'] = min(ceil(($this->_sections['nclist']['step'] > 0 ? $this->_sections['nclist']['loop'] - $this->_sections['nclist']['start'] : $this->_sections['nclist']['start']+1)/abs($this->_sections['nclist']['step'])), $this->_sections['nclist']['max']);
	if ($this->_sections['nclist']['total'] == 0)
		$this->_sections['nclist']['show'] = false;
} else
	$this->_sections['nclist']['total'] = 0;
if ($this->_sections['nclist']['show']):

		for ($this->_sections['nclist']['index'] = $this->_sections['nclist']['start'], $this->_sections['nclist']['iteration'] = 1;
			 $this->_sections['nclist']['iteration'] <= $this->_sections['nclist']['total'];
			 $this->_sections['nclist']['index'] += $this->_sections['nclist']['step'], $this->_sections['nclist']['iteration']++):
$this->_sections['nclist']['rownum'] = $this->_sections['nclist']['iteration'];
$this->_sections['nclist']['index_prev'] = $this->_sections['nclist']['index'] - $this->_sections['nclist']['step'];
$this->_sections['nclist']['index_next'] = $this->_sections['nclist']['index'] + $this->_sections['nclist']['step'];
$this->_sections['nclist']['first']	  = ($this->_sections['nclist']['iteration'] == 1);
$this->_sections['nclist']['last']	   = ($this->_sections['nclist']['iteration'] == $this->_sections['nclist']['total']);
?>
        <li><span><?php echo $this->_sections['nclist']['index']; ?>
</span><a href="<?php echo $this->_vars['news_list'][$this->_sections['nclist']['index']]['url']; ?>
" class="underline" target="_blank"><?php echo $this->_vars['news_list'][$this->_sections['nclist']['index']]['title']; ?>
</a></li>
        <?php endfor; endif; ?>
      </ol>
    </div>
  </div>
  <!-- 列表-职场资讯结束 -->
  <!-- 列表-友情链接 -->
  <div class="index-data-wrap">
    <div class="blue-line"></div>
    <div class="data-title-box clearfix">
      <h4 class="f-left">友情链接<span>Friendly Link</span></h4>
      <a href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
link/add_link.php" target="_blank"  class="f-right underline">申请>></a>
    </div>
    <div class="friendly-link">
      <?php echo tpl_function_qishi_link(array('set' => "列表名:link,显示数目:100,调用名称:QS_index,类型:1"), $this);?>
      <?php if (count((array)$this->_vars['link'])): foreach ((array)$this->_vars['link'] as $this->_vars['list']): ?>
      <a href="<?php echo $this->_vars['list']['link_url']; ?>
" target="_blank" class="underline"><?php echo $this->_vars['list']['title']; ?>
</a>
      <?php endforeach; endif; ?>
    </div>
    <?php echo tpl_function_qishi_link(array('set' => "列表名:imglink,显示数目:14,调用名称:QS_index,类型:2"), $this);?>
    <?php if ($this->_vars['imglink']): ?>
    <div class="link_img">
      <?php if (count((array)$this->_vars['imglink'])): foreach ((array)$this->_vars['imglink'] as $this->_vars['list']): ?>
      <div class="l_img"><a href="<?php echo $this->_vars['list']['link_url']; ?>
" target="_blank"><img src="<?php echo $this->_vars['list']['link_logo']; ?>
" alt="<?php echo $this->_vars['list']['title']; ?>
" border="0"/></a> </div>
      <?php endforeach; endif; ?>
      <div class="clear"></div>
    </div>
    <?php endif; ?>
    </div>
  <!-- 列表-友情链接结束 -->
</div>
<!-- 主体结束 -->
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>