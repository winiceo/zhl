<?php
 /*
 * 74cms 初始化模版引擎
*/
if(!defined('IN_QISHI'))
{
die('Access Denied!');
}
include_once(QISHI_ROOT_PATH.'include/template_lite/class.template.php');
$smarty = new Template_Lite; 
$smarty -> cache_dir = QISHI_ROOT_PATH.'temp/caches/'.$_CFG['template_dir'];
$smarty -> compile_dir =  QISHI_ROOT_PATH.'temp/templates_c/'.$_CFG['template_dir'];
$smarty -> template_dir = QISHI_ROOT_PATH.'templates/'.$_CFG['template_dir'];
$smarty -> reserved_template_varname = "smarty";
$smarty -> left_delimiter = "{#";
$smarty -> right_delimiter = "#}";
$smarty -> force_compile = false;
$smarty -> assign('_PLUG', $_PLUG);
$smarty -> assign('QISHI', $_CFG);
$smarty -> assign('page_select',$page_select);
?>