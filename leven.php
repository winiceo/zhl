<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

define('IN_QISHI', true);

define('ROOT', dirname(__FILE__) . "/");
define('QISHI_ROOT_PATH', dirname(__FILE__) . "/");


require ROOT . 'vendor/autoload.php';
require ROOT . 'leven/lib.php';

error_reporting(E_ALL);

//QISHI_ROOT_PATH=ROOT;
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);


$app->get('/genv/clear',function($request, $response){
    global  $dbname;
    $table=array();
    // $table[]="qs_resume_field";
    $table[]="qs_link";
    $table[]="qs_gifts_type";
    $table[]="qs_admin";
    $table[]="qs_explain_category";
    $table[]="qs_pms_sys";
    $table[]="qs_notice_category";
    $table[]="qs_ad_app_category";
    $table[]="qs_shop_hotword";
    $table[]="qs_text";
    $table[]="qs_sms_setmeal";
    $table[]="qs_link_category";
    $table[]="qs_article_property";
    $table[]="qs_color";
    $table[]="qs_evaluation_type";
    $table[]="qs_train_setmeal";
    $table[]="qs_payment";
    $table[]="qs_hunter_setmeal";
    $table[]="qs_promotion_category";
    $table[]="qs_setmeal";
    $table[]="qs_explain";
    $table[]="qs_navigation_category";
    $table[]="qs_baiduxml";
    $table[]="qs_tpl";
    $table[]="qs_plug";
    $table[]="qs_article_category";
    $table[]="qs_hrtools_category";
    $table[]="qs_shop_category";
    $table[]="qs_weixin_config";
    $table[]="qs_uc_config";
    $table[]="qs_crons";
    $table[]="qs_baidu_submiturl";
    $table[]="qs_weixin_menu";
    $table[]="qs_help_category";
    $table[]="qs_sms_templates";
    $table[]="qs_captcha";
    $table[]="qs_category_group";
    $table[]="qs_mailconfig";
    $table[]="qs_sms_config";
    $table[]="qs_locoyspider";
    $table[]="qs_ad_category";
    $table[]="qs_navigation";
    $table[]="qs_mail_templates";
    $table[]="qs_members_points_rule";
    $table[]="qs_category_major";
    $table[]="qs_page";
    $table[]="qs_config";
    $table[]="qs_category";
    $table[]="qs_category_hunterjobs";
    $table[]="qs_category_district";
    $table[]="qs_category_jobs";
    $table[]="qs_hrtools";
    $table[]="qs_hotword";
    $table[]="qs_relation";
    $table[]="qs_subsite";

    $sql=" select TABLE_NAME from information_schema.tables where table_schema='{$dbname}' and table_type='base table'   order by table_rows;";

    $rs=\ORM::for_table("qs_resume_temp")->raw_query($sql)->find_array();

    foreach($rs as $key=>$value){
        if(!in_array($value["TABLE_NAME"],$table)){

            dump($value["TABLE_NAME"]);
            $rs=ORM::raw_execute("truncate   table `".$value['TABLE_NAME']."`");
            dump($rs);
        }
     }

});

$app->get('/genv/test',function($request, $response){
//    $rs=\ORM::for_table("qs_members")->find_array();
//    for)
//    for($i=1;$i<8;$i++){
//        $rs=\ORM::for_table("qs_fotrune")->create();
//        $rs->name="李四".$i;
//        $rs->result="李四".time()*$i;
//        $rs->uid=7;
//        $rs->category=$i;
//        $rs->save();
//
//    }

    echo 333;

});

$app->run();
