<?php
 /*
 * 74cms ���������ļ�
*/
if(!defined('IN_QISHI'))
{
die('Access Denied!');
}
error_reporting(E_ERROR );
define('QISHI_ROOT_PATH', str_replace('install/include/common.inc.php', '', str_replace('\\', '/', __FILE__)));
session_cache_limiter('private, must-revalidate');
ini_set('session.save_handler', 'files');
session_save_path('/data/tmp/session/');
session_start();
require_once (QISHI_ROOT_PATH.'install/include/common.fun.php');
if (!empty($_GET))
{
$_GET  = install_addslashes_deep($_GET);
}
if (!empty($_POST))
{
$_POST = install_addslashes_deep($_POST);
}
$_COOKIE   = install_addslashes_deep($_COOKIE);
$_REQUEST  = install_addslashes_deep($_REQUEST);
PHP_VERSION > '5.1'?date_default_timezone_set("PRC"):'';
 $timestamp = time();
 header("Content-Type:text/html;charset=".QISHI_CHARSET);
 $php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
 $url = $php_self."?".$_SERVER['QUERY_STRING'];
if (!file_exists(QISHI_ROOT_PATH.'install/'))
{
echo "��install��Ŀ¼�����ڣ�";
exit();
}
if (!is_readable(QISHI_ROOT_PATH.'install/') || !is_writable(QISHI_ROOT_PATH.'install/') || !is_readable(QISHI_ROOT_PATH.'install/compile/') || !is_writable(QISHI_ROOT_PATH.'install/compile/'))
{
exit("���Ƚ���install��Ŀ¼�Լ���Ŀ¼�µ���Ŀ¼����Ϊ�ɶ�д״̬��777��<br />���������Ķ�����װ˵����������������");
} 
 require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
 require_once(QISHI_ROOT_PATH.'include/template_lite/class.template.php');
 $install_smarty = new Template_Lite();
 $install_smarty -> reserved_template_varname = "smarty";
 $install_smarty->cache = false;
 $install_smarty->template_dir = QISHI_ROOT_PATH.'install/templates';
 $install_smarty->compile_dir = QISHI_ROOT_PATH.'install/compile';
 $install_smarty->left_delimiter = "{#";
 $install_smarty->right_delimiter = "#}";
 $need_check_dirs = array(
                    'data',
                     'data/appads',
                    'data/avatar',
		    		'data/backup',
                    'data/campus',
		    		'data/certificate',
                    'data/comads',
                    'data/companyimg',
		    		'data/contactimgfont',
                    'data/credent_photo',
                    'data/eval',
                    'data/font',
                    'data/hrtools',
                    'data/hunter',
				    'data/images',
				    'data/images/thumb',
				    'data/link',
				    'data/logo',					
                    'data/photo',
				    'data/photo/thumb',
				    'data/salary',
				    'data/sessions',
				    'data/setmealimg',
				    'data/shop',
				    'data/tmp',
				    'data/train_certificate',
				    'data/train_img',
				    'data/train_logo',
				    'data/train_teachers',
				    'data/weixin',
				    'data/word',
				    'data/wxpay',
				    'data/wzp',
					'temp/caches',
					'temp/templates_c',		
					'temp/backup_templates',			
					'templates',	
					'admin/statement',			
					'install'
                    );
?>
