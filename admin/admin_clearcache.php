<?php
 /*
 * 74cms �������
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'select_cache';
$smarty->assign('pageheader',"���»���");
if ($act=="select_cache")
{
	
	$smarty->display('sys/admin_clear_cache.htm');
}
elseif ($act=="clear_cache")
{
		if ($_POST['type'])
		{
			$smarty->cache = true;
			if (in_array("tplcache",$_POST['type']))
			{			
				$smarty->clear_compiled_tpl();
			}
			if (in_array("datacache",$_POST['type']))
			{
			$smarty->clear_all_cache();
			refresh_cache('config');
			refresh_cache('text');
			refresh_cache('mailconfig');
			refresh_cache('mail_templates');
			refresh_cache('locoyspider');
			refresh_cache('sms_config');
			refresh_cache('sms_templates');
			refresh_cache('captcha');
			refresh_cache('baiduxml');
			refresh_cache('weixin_config');
			refresh_plug_cache();
			refresh_category_cache();
			refresh_page_cache();
			refresh_nav_cache();
			refresh_points_rule_cache();
			makejs_classify();
			makejs_train_classify();
			refresh_uc_cache('uc_config');
			refresh_content_key_link_cache('content_key_link');
			get_access_token(true);
			refresh_subsite_cache();
			}
				$dirs = getsubdirs('../templates');
				foreach ($dirs as $k=> $val)
				{
					$dir="../temp/templates_c/".$val;
					if (!file_exists($dir)) mkdir($dir);
					$dir="../temp/caches/".$val;
					if (!file_exists($dir)) mkdir($dir);
				}
		}
		else
		{
		adminmsg('��ѡ����Ŀ��',1);
		}
	adminmsg('���³ɹ���',2);
}
?>