<?php
 /*
 * 74cms 邮件设置
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set_sms';
if($_CFG['subsite_id']>0){
	adminmsg('您没有管理权限！',0);
}
check_permissions($_SESSION['admin_purview'],"set_sms");
$smarty->assign('pageheader',"短信设置");
if($act == 'set_sms')
{
	get_token();
	$smarty->assign('sms',get_cache('sms_config'));
	$smarty->assign('navlabel','set');
	$smarty->display('sms/admin_sms_set.htm');
}
elseif($act == 'set_save')
{
	check_token();
	header("Cache-control: private");
	foreach($_POST as $k => $v){
	!$db->query("UPDATE ".table('sms_config')." SET value='$v' WHERE name='$k'")?adminmsg('更新站点设置失败', 1):"";
	}
	//填写管理员日志
	write_log("后台更新站点设置", $_SESSION['admin_name'],3);
	refresh_cache('sms_config');
	adminmsg("保存成功！",2);
}
if($act == 'testing')
{
	get_token();
	$smarty->assign('navlabel','testing');
	$smarty->display('sms/admin_sms_testing.htm');
}
elseif($act == 'sms_testing')
{
	check_token();
	$txt="您好！这是一条检测短信模块配置的短信。收到此短信，意味着您的短信模块设置正确！您可以进行其它操作了！";
	$mobile=$_POST['mobile'];
	if (!preg_match("/^(13|15|14|17|18)\d{9}$/",$mobile))
	{
	adminmsg("手机号填写错误，请重新填写!",0);
	}
	if($_POST['type']==1){
		$r=captcha_send_sms($mobile,$txt);
		if ($r=="success")
		{
			//填写管理员日志
		write_log("后台短信发送成功！", $_SESSION['admin_name'],3);
		adminmsg('短信发送成功！',2);
		}
		else
		{
		adminmsg("短信发送失败！$r",1);
		}
	}elseif($_POST['type']==2){
		$r=send_sms($mobile,$txt);
		if ($r=="success")
		{
			//填写管理员日志
		write_log("后台短信发送成功！", $_SESSION['admin_name'],3);
		adminmsg('短信发送成功！',2);
		}
		else
		{
		adminmsg("短信发送失败！$r",1);
		}
	}elseif($_POST['type']==3){
		$r=free_send_sms($mobile,$txt);
		if ($r=="success")
		{
			//填写管理员日志
		write_log("后台短信发送成功！", $_SESSION['admin_name'],3);
		adminmsg('短信发送成功！',2);
		}
		else
		{
		adminmsg("短信发送失败！$r",1);
		}
	}
	
}
elseif($act == 'set_tpl')
{
	get_token();
	$smarty->assign('navlabel','templates');
	$smarty->assign('mailconfig',get_cache('mailconfig'));
	$smarty->display('sms/admin_sms_templates.htm');
}
elseif($act == 'rule')
{
	get_token();
	$smarty->assign('navlabel','rule');
	$smarty->assign('sms_config',get_cache('sms_config'));
	$smarty->display('sms/admin_sms_rule.htm');
}
elseif($act == 'sms_rule_save')
{
	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('sms_config')." SET value='$v' WHERE name='$k'")?adminmsg('更新站点设置失败', 1):"";
	}
	//填写管理员日志
	write_log("后台设置短信配置！", $_SESSION['admin_name'],3);
	refresh_cache('sms_config');
	adminmsg("保存成功！",2);
}
elseif($act == 'edit_tpl')
{
	get_token();
	$templates_name=trim($_GET['templates_name']);
	$label=array();
	$label[]=array('{sitename}','网站名称');
	$label[]=array('{sitedomain}','网站域名');
	//生成标签
	if ($templates_name=='set_reg')
	{
	$label[]=array('{username}','用户名');
	$label[]=array('{password}','密码');
	}
	elseif ($templates_name=='set_applyjobs')
	{
	$label[]=array('{personalfullname}','申请人');
	$label[]=array('{jobsname}','申请职位名称');
	}
	elseif ($templates_name=='set_invite')
	{
	$label[]=array('{companyname}','邀请方(公司名称)');
	}
	elseif ($templates_name=='set_order')
	{
	$label[]=array('{paymenttpye}','付款方式');
	$label[]=array('{oid}','订单号');
	$label[]=array('{amount}','金额');
	}
	elseif ($templates_name=='set_editpwd')
	{
	$label[]=array('{newpassword}','新密码');
	}
	elseif ($templates_name=='set_jobsallow' || $templates_name=='set_jobsnotallow')
	{
	$label[]=array('{jobsname}','职位名称');
	}
	//-end
	if ($templates_name)
	{
		$sql = "select * from ".table('sms_templates')." where name='".$templates_name."'";
		$info=$db->getone($sql);
	}
	$info['thisname']=trim($_GET['thisname']);
	$smarty->assign('info',$info);
	$smarty->assign('label',$label);
	$smarty->assign('navlabel','templates');
	$smarty->display('sms/admin_sms_templates_edit.htm');
}
elseif($act == 'templates_save')
{
	check_token();
	$templates_value=trim($_POST['templates_value']);
	$templates_name=trim($_POST['templates_name']);
	!$db->query("UPDATE ".table('sms_templates')." SET value='{$templates_value}' WHERE name='{$templates_name}'")?adminmsg('设置失败', 1):"";
	$link[0]['text'] = "返回上一页";
	$link[0]['href'] ="?act=set_tpl";
	refresh_cache('sms_templates');
	//填写管理员日志
	write_log("后台成功保存模板！", $_SESSION['admin_name'],3);
	adminmsg("保存成功！",2,$link);
}
elseif($act == 'send')
{
	get_token();
	$smarty->assign('pageheader',"短信营销");
	
	require_once(dirname(__FILE__).'/include/admin_smsqueue_fun.php');
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$uid=intval($_GET['uid']);
	$mobile=trim($_GET['mobile']);
	
	$wheresql=' WHERE s_uid='.$uid.' ORDER BY s_id DESC ';
	$total_sql="SELECT COUNT(*) AS num FROM ".table('smsqueue').$wheresql;
	$perpage=10;
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$sms_log = get_smsqueue($offset,$perpage,$wheresql);
	
	$url=trim($_REQUEST['url']);
	if (empty($url))
	{
	$url="?act=send&mobile={$mobile}&uid={$uid}";
	}
	$smarty->assign('url',$url);
	$smarty->assign('smslog',$sms_log);
	$smarty->assign('page',$page->show(3));
	$smarty->display('sms/admin_sms_send.htm');
}
elseif($act == 'sms_send')
{
	check_token();
	$txt=trim($_POST['txt']);
	$mobile=trim($_POST['mobile']);
	$uid=intval($_POST['uid']);
	$url=trim($_REQUEST['url']);
	if (!$uid)
	{
	adminmsg('用户UID错误！',0);
	}
	if (empty($txt))
	{
	adminmsg('短信内容不能为空！',0);
	}
	if (empty($mobile))
	{
	adminmsg('手机不能为空！',0);
	}
	if (!preg_match("/^(13|15|14|17|18)\d{9}$/",$mobile))
	{
		$link[0]['text'] = "返回上一页";
		$link[0]['href'] = "{$url}";
		adminmsg("发送失败！<strong>{$mobile}</strong> 不是标准的手机号格式",1,$link);
		
	}
	else
	{
			$setsqlarr['s_uid']=$uid;
			$setsqlarr['s_mobile']=$mobile;
			$setsqlarr['s_body']=$txt;
			$setsqlarr['s_addtime']=time();
			$r=free_send_sms($mobile,$txt);
			if ($r=="success")
			{
				$setsqlarr['s_sendtime']=time();
				$setsqlarr['s_type']=1;//发送成功
				$db->inserttable(table('smsqueue'),$setsqlarr);
				unset($setsqlarr);
				//填写管理员日志
				write_log("后台成功发送短信！", $_SESSION['admin_name'],3);
				$link[0]['text'] = "返回上一页";
				$link[0]['href'] = "{$url}";
				adminmsg("发送成功！",2,$link);
			}
			else
			{
				$setsqlarr['s_sendtime']=time();
				$setsqlarr['s_type']=2;//发送失败
				$db->inserttable(table('smsqueue'),$setsqlarr);
				unset($setsqlarr);
				$link[0]['text'] = "返回上一页";
				$link[0]['href'] = "{$url}";
				adminmsg("发送失败，错误未知！",0,$link);
			}
	}
}
elseif ($act=='again_send')
{
	$id=intval($_GET['id']);
	if (empty($id))
	{
	adminmsg("请选择要发送的项目！",1);
	}
	$result = $db->getone("SELECT * FROM ".table('smsqueue')." WHERE  s_id = {$id} limit 1");
	$wheresql=" s_id={$id} ";
	$r=free_send_sms($result['s_mobile'],$result['s_body']);
	if ($r=='success')
	{
		$setsqlarr['s_sendtime']=time();
		$setsqlarr['s_type']=1;//发送成功
		!$db->updatetable(table('smsqueue'),$setsqlarr,$wheresql);
		//填写管理员日志
		write_log("后台成功发送项目！", $_SESSION['admin_name'],3);
		adminmsg('发送成功',2);
	}else{
		$setsqlarr['s_sendtime']=time();
		$setsqlarr['s_type']=2;
		!$db->updatetable(table('smsqueue'),$setsqlarr,$wheresql);
		adminmsg('发送失败',0);
	}
		
}
elseif ($act=='del')
{
	$id=$_POST['id'];
	if (empty($id))
	{
	adminmsg("请选择项目！",1);
	}
	if(!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	$db->query("Delete from ".table('smsqueue')." WHERE s_id IN ({$sqlin}) ");
	//填写管理员日志
	write_log("后台成功删除项目！", $_SESSION['admin_name'],3);
	adminmsg("删除成功",2);
	}
}
//短信套餐
elseif($act == 'meal')
{
    $sql = "select * from ".table('sms_setmeal');
	$setmeal =  $db->getall($sql);
	$smarty->assign('setmeal',$setmeal);	
	$smarty->assign('navlabel','meal'); 
	$smarty->display("sms/admin_sms_meal.htm");
}
elseif($act == 'set_meal_add')
{
	$smarty->assign('navlabel','meal');
	$smarty->display("sms/admin_sms_meal_add.htm");
}
elseif($act == 'set_meal_add_save')
{
	$setsqlarr['setmeal_name'] = trim($_POST['setmeal_name']);
	$setsqlarr['num'] = intval($_POST['num']);
	$setsqlarr['show_order'] = intval($_POST['show_order']);
	$setsqlarr['display'] = intval($_POST['display']);
	$setsqlarr['expense'] = trim($_POST['expense']);
	if($db->inserttable(table('sms_setmeal'),$setsqlarr)){
		//填写管理员日志
		write_log("后台成功添加短信套餐！", $_SESSION['admin_name'],3);
		adminmsg('添加成功！',2);
	}else{
		adminmsg('添加失败',1);
	}  
}
elseif($act == 'set_meal_edit')
{
	$id = intval($_GET['id'])?intval($_GET['id']):exit('参数不正确！');
	$sql = "select * from ".table('sms_setmeal')." where id = $id";
	$show =  $db->getone($sql);
	$smarty->assign('show',$show);
	$smarty->assign('navlabel','meal');
	$smarty->display("sms/admin_sms_meal_edit.htm");
}
elseif($act == 'set_meal_edit_save')
{	 
	$setsqlarr['setmeal_name'] = trim($_POST['setmeal_name']);
	$setsqlarr['num'] = intval($_POST['num']);
	$setsqlarr['show_order'] = intval($_POST['show_order']);
	$setsqlarr['display'] = intval($_POST['display']);
	$setsqlarr['expense'] = trim($_POST['expense']);
	if($db->updatetable(table('sms_setmeal'),$setsqlarr,' id = '.intval($_POST['id']))){
		//填写管理员日志
		write_log("后台成功修改短信套餐！", $_SESSION['admin_name'],3);
		adminmsg('修改成功！',2);
	}else{
		adminmsg('修改失败',1);
	}  
}
elseif($act == 'set_meal_del')
{
	$id=intval($_GET['id']);

	if($db->query("delete from ".table("sms_setmeal")." where id=$id "))
	{
		//填写管理员日志
		write_log("删除短信套餐成功！", $_SESSION['admin_name'],3);
		adminmsg('删除短信套餐成功',2);
	}
	else
	{
		adminmsg('删除短信套餐失败',1);
	}
}
?>