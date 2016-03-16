<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2016-03-16 23:15 CST */ ?>
<div class="navs link_bk">
    <a href="?act=upload" <?php if ($this->_vars['act'] == "upload"): ?>class="se"<?php endif; ?>>批量上传<span class="check"></span></a>

    <a href="?act=upload_list" <?php if ($this->_vars['act'] == "upload_list"): ?>class="se"<?php endif; ?>>历史记录<span class="check"> </span></a>
    <a href="?act=cheking_resume" <?php if ($this->_vars['act'] == "cheking_resume"): ?>class="se"<?php endif; ?>>审核简历<span
            class="no_check"> </span></a>

    <div class="clear"></div>
</div>