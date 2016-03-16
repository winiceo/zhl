<?php
/**
 * 74cms 银行转账或者汇款
*/

 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
 //获取安装代码
function pay_info()
{
$arr['p_introduction']="转账/汇款简短描述：";
$arr['notes']="转账/汇款详细描述：";
$arr['fee']="转账/汇款交易手续费：";
return $arr;
}
?>