<?php
 /*
 * ajax Î¢ÕÐÆ¸
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'add';
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$video_id=intval($_GET['id'])?intval($_GET['id']):exit('ID¶ªÊ§£¡');
$video=$db->getone("select * from ".table('video')." where id={$video_id}");
if($video){
/*$html='<embed src="'.$video['video_url'].'" mode="transparent"  quality="high"  type="application/x-shockwave-flash" width="850" height="600" allowfullscreen="always" allownetworking="always" allowscriptaccess="always"></embed>';*/
$html='<object width=850 height=600><param name="movie" value="'.$video['video_url'].'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><param name="wmode" value="Transparent"></param><embed width=850 height=600 wmode="Transparent" allowfullscreen="true" allowscriptaccess="always" quality="high" src="'.$video['video_url'].'" type="application/x-shockwave-flash"/></embed></object>';
}
else{
exit('ID¶ªÊ§£¡');
}
exit($html);
?>