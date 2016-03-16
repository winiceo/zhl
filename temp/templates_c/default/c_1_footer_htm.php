<?php require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/modifier.qishi_url.php'); $this->register_modifier("qishi_url", "tpl_modifier_qishi_url",false);  require_once('/genv/app/zhaohulu.com/include/template_lite/plugins/function.qishi_explain_list.php'); $this->register_function("qishi_explain_list", "tpl_function_qishi_explain_list",false);  /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 22:47 CST */ ?>
<div class="foot">
  <div class="footer_box">
    <div class="box link_bk">
        <div class="list">
          <h4>关于我们</h4>
          <div class="foot_list">
            <ul>
            <?php echo tpl_function_qishi_explain_list(array('set' => "列表名:explain,显示数目:3,标题长度:6,分类ID:1"), $this);?>
            <?php if (count((array)$this->_vars['explain'])): foreach ((array)$this->_vars['explain'] as $this->_vars['list']): ?>
              <li><a target="_blank" href="<?php echo $this->_vars['list']['url']; ?>
"><?php echo $this->_vars['list']['title']; ?>
</a></li>
            <?php endforeach; endif; ?>
              <li><a target="_blank" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
suggest">意见反馈</a></li>
            </ul>
            <ul>
              <?php echo tpl_function_qishi_explain_list(array('set' => "列表名:explain,显示数目:3,开始位置:3,标题长度:6,分类ID:1"), $this);?>
              <?php if (count((array)$this->_vars['explain'])): foreach ((array)$this->_vars['explain'] as $this->_vars['list']): ?>
                <li><a target="_blank" href="<?php echo $this->_vars['list']['url']; ?>
"><?php echo $this->_vars['list']['title']; ?>
</a></li>
              <?php endforeach; endif; ?>
            </ul>
          </div>
        </div>
        <div class="list">
          <h4>帮助中心</h4>
          <div class="foot_list">
            <ul>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_helplist,id:4", 'qishi_url', 'plugin', 1); ?>
">注册与登录</a></li>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_helplist,id:5", 'qishi_url', 'plugin', 1); ?>
">密码找回</a></li>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_helplist,id:8", 'qishi_url', 'plugin', 1); ?>
">认证管理</a></li>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_helplist,id:10", 'qishi_url', 'plugin', 1); ?>
">招聘管理 </a></li>
            </ul>
            <ul>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_helplist,id:17", 'qishi_url', 'plugin', 1); ?>
">求职管理</a></li>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_helplist,id:13", 'qishi_url', 'plugin', 1); ?>
">充值消费 </a></li>
            </ul>
          </div>
        </div>
        <div class="list">
          <h4>个人求职</h4>
          <div class="foot_list">
            <ul>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_jobslist", 'qishi_url', 'plugin', 1); ?>
">职位搜索</a></li>
              <li><a target="_blank" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
salary">薪酬统计</a></li>
              <li><a target="_blank" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
subscribe">职位订阅</a></li>
              <li><a target="_blank" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
explain/explain-show.php?id=6">职场指南</a></li>
            </ul>
            <ul>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_hunter_jobslist", 'qishi_url', 'plugin', 1); ?>
">猎头职位</a></li>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_jobslist,nature:63", 'qishi_url', 'plugin', 1); ?>
">兼职招聘</a></li>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_train_curriculum", 'qishi_url', 'plugin', 1); ?>
">培训课程</a></li>
            </ul>
          </div>
        </div>
        <div class="list">
          <h4>企业服务</h4>
          <div class="foot_list">
            <ul>
              <li><a target="_blank" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
user/company/company_jobs.php?act=addjobs">发布职位</a></li>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_resumelist", 'qishi_url', 'plugin', 1); ?>
">简历搜索</a></li>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_simplelist", 'qishi_url', 'plugin', 1); ?>
">招聘普工</a></li>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_hrtoolslist", 'qishi_url', 'plugin', 1); ?>
">HR工具箱</a></li>
            </ul>
            <ul>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_login", 'qishi_url', 'plugin', 1); ?>
">企业注册</a></li>
              <li><a target="_blank" href="<?php echo $this->_run_modifier("QS_jobfairlist", 'qishi_url', 'plugin', 1); ?>
">现场招聘会</a></li>
            </ul>
          </div>
        </div>
        <div class="clear"></div>
        
        
            <div class="weixin">
              <div class="weixin_con">
                <div class="weixin_img">
                  <span>官方微信</span><br />
                  <img src="<?php echo $this->_vars['QISHI']['upfiles_dir'];  echo $this->_vars['QISHI']['weixin_img']; ?>
" width="80" height="80">
                </div>
                <div class="weixin_img">
                  <span>手机APP</span><br />
                  <img src="<?php echo $this->_vars['QISHI']['site_dir']; ?>
plus/url_qrcode.php?url=<?php echo $this->_vars['QISHI']['site_domain'];  echo $this->_vars['QISHI']['site_dir']; ?>
m/download.php?downtype=android" width="80" height="80">
                </div>
                <div class="clear"></div>
              </div>
              <div class="weixin_cons" style="display:none;">
                <div class="w_txt">官方微信</div>
                <img src="<?php echo $this->_vars['QISHI']['upfiles_dir'];  echo $this->_vars['QISHI']['weixin_img']; ?>
" alt="" width="80" height="80">
                <div class="clear"></div>
              </div>
              <div class="comment">
                <p>服务热线</p>
                <p class="phone_number"><?php echo $this->_vars['QISHI']['bootom_tel']; ?>
</p>
              </div>
      </div>
    </div>
  </div>
    
  
  <div class="copyright">
联系地址：<?php echo $this->_vars['QISHI']['address']; ?>
 联系电话：<?php echo $this->_vars['QISHI']['bootom_tel']; ?>
 网站备案：<?php echo $this->_vars['QISHI']['icp'];  echo $this->_vars['QISHI']['statistics']; ?>
<br />
<?php echo $this->_vars['QISHI']['bottom_other']; ?>
<br />
Powered by <a href="http://www.zhaohulu.com/" target="_blank" style="color:#009900"><em> 找葫芦网 版权所有</em></a><br />
</div>
</div>
<!-- 回到顶部组件 -->
<div class="back_to_top" id="back_to_top">
  <div class="back" style="display:none;">
    <div>回到顶部</div>
  </div>
  <div class="steer">
    <div onclick="javascript:location.href='<?php echo $this->_vars['QISHI']['site_dir']; ?>
suggest'">我要建议</div>
  </div>
  <div class="sub">
    <div onclick="javascript:location.href='<?php echo $this->_vars['QISHI']['site_dir']; ?>
subscribe'">我要订阅</div>
  </div>
</div>
<script>
  $(function(){
    //回到顶部组件出现设置
    $(window).scroll(function(){
      if($(window).scrollTop()>200){
        $(".back").fadeIn(400);
      }else{
        $(".back").fadeOut(400);
      }
    })

    //回到顶部hover效果
    $(".back_to_top .back, .steer, .sub").hover(function(){
      $(this).find("div").css("display","block");
    },function(){
      $(this).find("div").css("display","none");
    })

    //设置滚回顶部方法
    $(".back").click(function(){
      $("body,html").animate({scrollTop:0}, 500);
      return false;
    })
  });
  $(function(){
    $(".foot_list ul:odd li").css("width", 62);
    $(".weixin_img:last").css("margin-right", 0);
  })
</script>
