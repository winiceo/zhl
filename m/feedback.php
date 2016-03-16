<?php
 /*
 * 74cms 触屏版用户反馈
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
if($act == 'index')
{
	//网站简介（说明页） 
	$site_detail = $db->getone("SELECT * FROM ".table('explain')." WHERE id=2 ");
	$smarty->assign('site_detail',$site_detail["content"]);
	$smarty->assign('goback',$_SERVER["HTTP_REFERER"]);
	$smarty->display("m/feedback.html");
}
elseif ($act == 'save')
{
	$setsqlarr['infotype']=intval($_POST['type']);
	$setsqlarr['feedback']=trim($_POST['feedback']);
	$setsqlarr['tel']=trim($_POST['tel']);
	$setsqlarr['addtime']=time();
	$db->inserttable(table('feedback'),$setsqlarr);
	header("Location: index.php");
}

?>