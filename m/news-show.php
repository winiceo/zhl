<?php
 /*
 * 74cms WAP
*/
define('IN_QISHI', true);
define('REQUEST_MOBILE',true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
require_once(dirname(__FILE__).'/weixin_share.php');
$show = news_one($_GET['id']);
check_m_url($show['subsite_id'],$smarty,$_CFG['m_news_url']);
if($show)
{
	$show['addtime'] = daterange(time(),$show['addtime'],'Y-m-d',"#FF3300");
	$show['bimg']=$_CFG['site_domain'].$_CFG['upfiles_dir'].$show['Small_img'];
	$type_cn = $db->getone(" SELECT * FROM ".table('article_category')." WHERE id=".$show['type_id']);
	$show['type_cn'] = $type_cn['categoryname'];
	$smarty->assign('show',$show);
}
$smarty->assign('goback',$_SERVER["HTTP_REFERER"]);
$smarty->display("m/news-show.html");
?>