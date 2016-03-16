<?php
/**
 * 74cms 微信支付
*/

 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
 //获取安装代码
function pay_info()
{
$arr['p_introduction']="微信支付简短描述：";
$arr['notes']="微信支付详细描述：";
$arr['fee']="微信支付交易手续费：";
return $arr;
}
?>