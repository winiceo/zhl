<?php
 /*
 * 74cms ����word
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"recruitment");
$id =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ�������",1);
if (is_array($id))
{
	// �����صļ��� ��������Ϊword  �Ȳ�ѯ����id  
	$sqlin=implode(",",$id);
	if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
	$idarr = $db->getall("select resume_id from ".table('company_down_resume')." where did IN ({$sqlin})");
	foreach ($idarr as $key=>$value) {
		$idarr[$key]=$value['resume_id'];
	}
	$id=$idarr;
}
else
{
	$id=array($id);
}
$sqlin=implode(",",$id);
if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
$rsume_sql = "select * from ".table('resume')." where id IN ({$sqlin}) ";
$result=$db->getall($rsume_sql);
$htm='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv=Content-Type  content="text/html; charset=gb2312" >
	<title></title>
	<style>

	.table_tit{border-bottom: 2px solid #000;font-size: 14px;font-weight: bold;padding:10px 0;}
	.fl{float: left;}
	.fr{float: right;}
	</style>
</head>
<body>';
foreach ($result as $val)
{

	$val['education_list']=get_this_education($val['uid'],$val['id']);
	$val['work_list']=get_this_work($val['uid'],$val['id']);
	$val['training_list']=get_this_training($val['uid'],$val['id']);
	$val['age']=date("Y")-$val['birthdate'];
	$val['tagcn']=preg_replace("/\d+/", '',$val['tag']);
	$val['tagcn']=preg_replace('/\,/','',$val['tagcn']);
	$val['tagcn']=preg_replace('/\|/','&nbsp;&nbsp;&nbsp;',$val['tagcn']);
 	
	// if(intval($_GET['apply'])==1){
	// 	$set_apply = 1;
	// 	$apply = $db->getone("select * from ".table('personal_jobs_apply')." where `resume_id`=".$val['id']);
	// 	$val['jobs_name'] = $apply['jobs_name'];
	// }else{
	// 	$set_apply = 0;
	// }
	// �����¼ʱ��
	$userinfo=$db->getone("select last_login_time from ".table('members')." where uid=$val[uid]");
	$last_login_time=date('Y-m-d',$userinfo['last_login_time']);
$htm.='
<table width="700" border="0" align="center" cellpadding="10" cellspacing="0" style="background-color: #FAF7C1;padding:10px 0">
		<tr>
			<td align="center">��������</td>
			<td align="center">'.$val['title'].'</td>
			<td align="center">�����¼</td>
			<td align="center">'.$last_login_time.'</td>
			<td align="center"><img width="130" height="40" src="'.$_CFG["site_domain"].$_CFG["upfiles_dir"].$_CFG["web_logo"].'" alt="'.$_CFG["site_name"].'" border="0" align="absmiddle"></td>
		</tr>
	</table>';
// if($set_apply==1){
// 	if($val['jobs_name']!=""){
// 		$htm .= '<tr>
// 	    <td colspan="1">����ְλ��'.$val["jobs_name"].'</td>
// 	  </tr>';
// 	}
// }
	//��ҵ�鿴�յ��ļ�����������  0->�ر�  1->����
	$show=false;
	if($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1' && $_CFG['showapplycontact']=='1'){
		$has = $db->getone("select 1 from ".table('personal_jobs_apply')." where company_uid=".intval($_SESSION['uid'])." and resume_id=".$val['id']);
		if($has){
			$show = true;
		}
	}
	if($show==false)
	{
		if ($val['display_name']=="2")
		{
			$val['fullname_']=$val['fullname'];	
			$val['fullname']="N".str_pad($val['id'],7,"0",STR_PAD_LEFT);	
		}
		elseif($val['display_name']=="3")
		{
			$val['fullname_']=$val['fullname'];	
			if($val['sex']==1)
			{
				$val['fullname']=cut_str($val['fullname'],1,0,"����");
			}
			elseif($val['sex']==2)
			{
				$val['fullname']=cut_str($val['fullname'],1,0,"Ůʿ");
			}
		}
		else
		{
			$val['fullname_']=$val['fullname'];
			$val['fullname']=$val['fullname'];
		}
		// ������Ϣ ��ϵ��ʽ
		if($_CFG['showresumecontact']=='1' || $_CFG['showresumecontact']=='0')
		{
			$val['fullname']=$val['fullname'];
			$val['telephone']=$val['telephone'];
			$val['email']=$val['email'];
		}
		elseif($_CFG['showresumecontact']=='2')//��ϵ��ʽ����Ա���غ�ɼ�
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1')
			{
				$sql = "select did from ".table('company_down_resume')." WHERE company_uid = {$_SESSION['uid']} AND resume_id='{$val[id]}' LIMIT 1";
				$info=$db->getone($sql);
				if (!empty($info))
				{
					$val['fullname']=$val['fullname_'];
					$val['telephone']=$val['telephone'];
					$val['email']=$val['email'];
				}
				else
				{
					$val['fullname']=$val['fullname'];
					$val['telephone']="���غ�ɼ�";
					$val['email']="���غ�ɼ�";
				}
			}elseif($_SESSION['utype']=='2' && $_SESSION['uid']==$val['uid'])
			{
				$val['fullname']=$val['fullname_'];
				$val['telephone']=$val['telephone'];
				$val['email']=$val['email'];
			}else{
					$val['fullname']=$val['fullname'];
					$val['telephone']="���غ�ɼ�";
					$val['email']="���غ�ɼ�";
			}
		}
	}
	if ($val['photo']=="1")
	{
	$val['photosrc']=$_CFG["site_domain"].$_CFG['resume_photo_dir_thumb'].$val['photo_img'];
	}
	else
	{
	$val['photosrc']=$_CFG["site_domain"].$_CFG['resume_photo_dir_thumb']."no_photo.gif";
	}
	$htm.='<table width="700" border="0" align="center" cellpadding="10" cellspacing="0">
			<tr>
				<td class="table_tit">������Ϣ</td>
			</tr>
		</table>
		<table width="700" border="0" align="center" cellpadding="6" cellspacing="0" style="font-size: 12px;">
			<tr>
				<td align="right" width="100">������</td>
				<td align="left" width="200">'.$val['fullname'].'</td>
				<td align="right" width="100">�Ա�</td>
				<td align="left" width="200">'.$val['sex_cn'].'</td>
				<td rowspan=5 align="center">
					<img width="100" height="100" src="'.$val['photosrc'].'">
				</td>
			</tr>
			<tr>
				<td align="right" width="100" >�ֻ����룺</td>
				<td align="left" width="200">'.$val['telephone'].'</td>
				<td align="right" width="100">���䣺</td>
				<td align="left" width="200">'.$val['age'].'��</td>
			</tr>
			<tr>
				<td align="right" width="100">�����ʼ���</td>
				<td align="left" width="200">'.$val['email'].'</td>
				<td align="right" width="100">�����̶ȣ�</td>
				<td align="left" width="200">'.$val['education_cn'].'</td>
			</tr>
			<tr>
				<td align="right" width="100">�������ޣ�</td>
				<td align="left" width="200">'.$val['experience_cn'].'</td>
				<td align="right" width="100">����״����</td>
				<td align="left" width="200">'.$val['marriage_cn'].'</td>
			</tr>
			<tr>
				<td align="right" width="100">���᣺</td>
				<td align="left" width="200">'.$val['householdaddress'].'</td>
				<td align="right" width="100">Ŀǰ���ڵأ�</td>
				<td align="left" width="200">'.$val['residence'].'</td>
			</tr>
		</table>';
	// ��ְ����

	$htm.='<table width="700" border="0" align="center" cellpadding="10" cellspacing="0">
			<tr>
				<td class="table_tit">��ְ����</td>
			</tr>
		</table>
		<table width="700" border="0" align="center" cellpadding="6" cellspacing="0" style="font-size: 12px;">
				<tr>
					<td align="right" width="100">������ҵ��</td>
					<td  align="left">'.$val['trade_cn'].'</td>
				</tr>
				<tr>
					<td align="right" width="100">����ְλ��</td>
					<td align="left">'.$val['intention_jobs'].'</td>
				</tr>
				<tr>
					<td align="right" width="100">�����ص㣺</td>
					<td align="left">'.$val['district_cn'].'</td>
				</tr>
				<tr>
					<td align="right" width="100">����н�ʣ�</td>
					<td align="left">'.$val['wage_cn'].'</td>
				</tr>
			</table>';
	// ��������
	$htm.='<table width="700" border="0" align="center" cellpadding="10" cellspacing="0">
			<tr>
				<td class="table_tit">��������</td>
			</tr>
		</table>';
	if($val['work_list'])
	{
		foreach ($val['work_list'] as $wli)
		{  
			$htm.='<table width="700" border="0" align="center" cellpadding="10" cellspacing="0" style="font-size: 12px;padding-top: 20px;">
			<tr>
				<td align="right" width="100">'.$wli['startyear'].'.'.$wli['startmonth'].'-'.$wli['endyear'].'.'.$wli['endmonth'].'</td>
				<td align="left">'.$wli['companyname'].'</td>
			</tr>
			<tr>
				<td width="100">&nbsp;</td>
				<td >
					<span class="fl w100">ְλ���ƣ�</span>
					<span class="fl">'.$wli['jobs'].'</span>
				</td>
			</tr>
			<tr>
				<td width="100">&nbsp;</td>
				<td >
					<span class="fl w100">����ְ��</span>
					<span class="fl">
					'.$wli['achievements'].'
					</span>
				</td>
			</tr>
		</table>';
		}
	}
	else
	{
	 $htm.='<table width="700" border="0" align="center" cellpadding="10" cellspacing="0" style="font-size: 12px;padding-top: 20px;"><tr>û����д��������</tr></table>';
	}
	// ��ѵ����
	$htm.='<table width="700" border="0" align="center" cellpadding="10" cellspacing="0">
			<tr>
				<td class="table_tit">��ѵ����</td>
			</tr>
		</table>
	';
	if($val['training_list'])
	{
		foreach ($val['training_list'] as $tli)
		{
			$htm.='<table width="700" border="0" align="center" cellpadding="10" cellspacing="0" style="font-size: 12px;padding-top: 20px;">
			<tr>
				<td class="w100 td_left td_bgc">'.$tli['startyear'].'.'.$tli['startmonth'].'-'.$tli['endyear'].'.'.$tli['endmonth'].'</td>
				<td class="td_left td_bgc">'.$tli['agency'].'</td>
			</tr>
			<tr>
				<td width="100">&nbsp;</td>
				<td >
					��ѵ�γ̣�'.$tli['course'].'
				</td>
			</tr>
			<tr>
				<td width="100">&nbsp;</td>
				<td >
					<span class="fl w100">��ѵ���ܣ�</span>
					<span class="fl">
						'.$tli['description'].'
					</span>
				</td>
			</tr>
		</table>';
		}
	}
	else
	{
		$htm.='<table width="700" border="0" align="center" cellpadding="10" cellspacing="0" style="font-size: 12px;padding-top: 20px;">
			<tr>û����д��ѵ����</tr></table>';
	}
	// ��������
	$htm.='<table width="700" border="0" align="center" cellpadding="10" cellspacing="0">
			<tr>
				<td class="table_tit">��������</td>
			</tr>
		</table>';
	if($val['education_list']){
		foreach ($val['education_list'] as $eli)
		{
			$htm.='<table width="700" border="0" align="center" cellpadding="10" cellspacing="0" style="font-size: 12px;padding-top: 20px;">
			<tr>
				<td style="font-weight: bold;">'.$eli['school'].'</td>
				<td>'.$eli['startyear'].'.'.$eli['startmonth'].'-'.$eli['endyear'].'.'.$eli['endmonth'].'</td>
				<td>'.$eli['speciality'].'</td>
				<td>'.$eli['education_cn'].'</td>
			</tr>
		</table>';
		}
	}
	else
	{
		$htm.='<table width="700" border="0" align="center" cellpadding="10" cellspacing="0" style="font-size: 12px;padding-top: 20px;">
			<tr>û����д��������</tr></table>';
	}
	// ��������
	$htm.='<table width="700" border="0" align="center" cellpadding="10" cellspacing="0">
			<tr>
				<td class="table_tit">��������</td>
			</tr>
		</table>
		<table width="700" border="0" align="center" cellpadding="10" cellspacing="0" style="font-size: 12px;padding-top: 20px;">
			<tr>
				<td>'.nl2br($val['specialty']).'</td>
			</tr>
		</table><br><br><br><br><br><br><br><br>';
}
$htm.="<div align=\"center\"><br />
	<a title=\"{$_CFG['site_name']}\" href=\"{$_CFG['site_domain']}{$_CFG['site_dir']}\">{$_CFG['site_name']}</a>
</div>
</body>
</html>";

header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");   
header("Content-Type: application/doc"); 
header("Content-Disposition:attachment; filename=�������˼���.doc"); 
echo $htm;
function get_this_education($uid,$pid)
{
	global $db;
	$sql = "SELECT * FROM ".table('resume_education')." WHERE uid='".intval($uid)."' AND pid='".intval($pid)."' ";
	return $db->getall($sql);
}
function get_this_work($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_work')." where uid=".intval($uid)." AND pid='".$pid."' " ;
	return $db->getall($sql);
}
function get_this_training($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_training')." where uid='".intval($uid)."' AND pid='".intval($pid)."'";
	return $db->getall($sql);
}
function get_user_setmealt($uid)
{
	global $db;
	$sql = "select * from ".table('members_setmeal')."  WHERE uid=".intval($uid)." AND  effective=1 LIMIT 1";
	return $db->getone($sql);
}
?>