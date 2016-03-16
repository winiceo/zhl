<?php
 /*
 * 74cms �����ʼ�
*/
ignore_user_abort(true);
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_GET['act']) ? trim($_GET['act']) : '';
$uid=intval($_GET['uid']);
$key=trim($_GET['key']);
if (empty($uid) || empty($key))
{
 exit("error");
}
$asyn_userkey=asyn_userkey($uid);
if ($asyn_userkey<>$key)exit("error");
$mailconfig=get_cache('mailconfig');
$mail_templates=get_cache('mail_templates');
//����ע���ʼ�
if($act == 'reg'){
	if ($_GET['sendemail'] && $_GET['sendusername'] && $_GET['sendpassword'] && $mailconfig['set_reg']=="1")
	{
			$userinfo=get_user_inid($uid);
			if ($userinfo['username']==$_GET['sendusername'] && $userinfo['email']==$_GET['sendemail'])
			{ 
			$templates=label_replace($mail_templates['set_reg']);
			$templates_title=label_replace($mail_templates['set_reg_title']);
			smtp_mail($_GET['sendemail'],$templates_title,$templates);
			}
	}
}
//����ְλ�����ʼ�
elseif($act == 'jobs_apply')
{   
	global $_CFG;
	$templates=label_replace($mail_templates['set_applyjobs']);
	$templates_title=label_replace($mail_templates['set_applyjobs_title']);
	// ����ְλ�����ʼ� ������Ϣ
	require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
	$resume_id=intval($_GET['resume_id']);
	$resume_basic=get_resume_basic($uid,$resume_id);
	if($resume_basic['tag_cn'])
	{
		$resume_tag=explode(',',$resume_basic['tag_cn']);
		$tag_str='<p>';
		foreach ($resume_tag as $value)
		{
			$tag_str.='<span style="color: #656565;display:inline-block;background-color: #f2f4f7; border: 1px solid #d6d6d7;text-align: center;height:30px;line-height: 30px;margin-right:10px;padding:0 10px">'.$value.'</span>';
		}
		$tag_str.='</p>';
	}
	$resume_work=get_resume_work($uid,$resume_id);
	$show_contact = false;
	if($_CFG['showapplycontact']=='1' || $_CFG['showresumecontact']=='0')
	{
		$show_contact = '<p>�ֻ����룺'.$resume_basic["telephone"].' �������䣺'.$resume_basic["email"].'</p>';
	}
	else
	{
		$show_contact = '<p>��ϵ��ʽ��<a href='.url_rewrite('QS_resumeshow',array('id'=>$resume_id)).'>����鿴</a></p>';
	}	
	$htm='<div style="width: 900px;margin: 0 auto;font-size: 14px;">
		<div style="margin-bottom:10px">
			<div style="float: left;"><a href="'.$_CFG['site_domain'].$_CFG['site_dir'].'"><img src="'.$_CFG['site_domain'].$_CFG['upfiles_dir'].$_CFG['web_logo'].'" alt="'.$_CFG['site_name'].'" border="0" align="absmiddle" width=180 height=50 /></a></div>
			<div style="float: right;padding-top:10px;">'.$templates.'����ʱ�䣺'.date("Y-m-d",$resume_basic["refreshtime"]).'</div>
			<div style="clear:both"></div>
		</div>
		<div style="padding-bottom: 10px;">
			<span style="font-size: 18px;font-weight: 700;">'.$resume_basic["fullname"].'</span><span>��'.$resume_basic["sex_cn"].'��'.$resume_basic["age"].'��</span>
			<p>ѧ����'.$resume_basic["education_cn"].' | רҵ��'.$resume_basic["major_cn"].' | �������飺'.$resume_basic["experience_cn"].'�� | �־�ס�أ�'.$resume_basic["residence"].'</p>

			'.$show_contact.$tag_str.'

		</div>
		<div style="padding-bottom: 10px;">
			<p style="font-size: 16px;font-weight: 700;">��ְ����</p>
			<p>����ְλ��'.$resume_basic["intention_jobs"].'</p>
			<p>����н�ʣ�'.$resume_basic["wage_cn"].'</p>
			<p>����������'.$resume_basic["district_cn"].'</p>
		</div>
		<div style="padding-bottom: 10px;">
			<p style="font-size: 16px;font-weight: 700;">��������</p>';
				if(!empty($resume_work))
				{
					foreach ($resume_work as $value)
					{
						$htm.='<div>
								<p style="font-size: 14px;font-weight: 700;">'.$value["companyname"].'</p>
								<p>'.$value["startyear"].'��'.$value["startmonth"].'��-'.$value["endyear"].'��'.$value["endmonth"].'�� '.$value["jobs"].'</p>
								<div style="float: left;width: 100px;">�������ݣ�</div>
								<div style="float: right;width: 800px;">'.$value["achievements"].'</div>
								<div style="clear:both"></div>
							</div>'	;
					}
				}
				else
				{
					$htm.='<div>
								û����д��������
							</div>'	;
				}
				
		$htm.='</div>';
		if($resume_basic["specialty"])
		{
			$htm.='<div style="padding-bottom: 10px;">
				<p style="font-size: 16px;font-weight: 700;">��������</p>
				<p>'.$resume_basic["specialty"].'</p>
			</div>';
		}
		$htm.='<div style="text-align: center;margin-top:20px">
				�ü�������<a href="'.$_CFG["site_domain"].$_CFG["site_dir"].'">'.$_CFG["site_name"].'</a>
			</div>
		</div>';
	smtp_mail($_GET['email'],$templates_title,$htm);
}
//�������Է����ʼ�
elseif($act == 'set_invite')
{
			$templates=label_replace($mail_templates['set_invite']);
			$templates_title=label_replace($mail_templates['set_invite_title']);
			smtp_mail($_GET['email'],$templates_title,$templates);
}
//�����ֵ�������ʼ�
elseif($act == 'set_order'){
			$templates=label_replace($mail_templates['set_order']);
			$templates_title=label_replace($mail_templates['set_order_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//��ֵ�ɹ��������ʼ�
elseif($act == 'set_payment'){
			$templates=label_replace($mail_templates['set_payment']);
			$templates_title=label_replace($mail_templates['set_payment_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//�޸����룬�����ʼ�
elseif($act == 'set_editpwd'){
			$templates=label_replace($mail_templates['set_editpwd']);
			$templates_title=label_replace($mail_templates['set_editpwd_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//ְλ���ͨ���������ʼ�
elseif($act == 'set_jobsallow'){
			$templates=label_replace($mail_templates['set_jobsallow']);
			$templates_title=label_replace($mail_templates['set_jobsallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//ְλδ���ͨ���������ʼ�
elseif($act == 'set_jobsnotallow'){
			$templates=label_replace($mail_templates['set_jobsnotallow']);
			$templates_title=label_replace($mail_templates['set_jobsnotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//��ҵ��֤ͨ���������ʼ�
elseif($act == 'set_licenseallow'){
			$templates=label_replace($mail_templates['set_licenseallow']);
			$templates_title=label_replace($mail_templates['set_licenseallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//��ҵ��֤δͨ���������ʼ�
elseif($act == 'set_licensenotallow'){
			$templates=label_replace($mail_templates['set_licensenotallow']);
			$templates_title=label_replace($mail_templates['set_licensenotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//��ҵ�����ر��Ƽ��������ʼ�
elseif($act == 'set_addmap'){
			$templates=label_replace($mail_templates['set_addmap']);
			$templates_title=label_replace($mail_templates['set_addmap_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//����ͨ����ˣ������ʼ�
elseif($act == 'set_resumeallow'){
			$templates=label_replace($mail_templates['set_resumeallow']);
			$templates_title=label_replace($mail_templates['set_resumeallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//����δͨ����ˣ������ʼ�
elseif($act == 'set_resumenotallow'){
			$templates=label_replace($mail_templates['set_resumenotallow']);
			$templates_title=label_replace($mail_templates['set_resumenotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//��ʦͨ����ˣ������ʼ�
elseif($act == 'set_teaallow'){
			$templates=label_replace($mail_templates['set_teaallow']);
			$templates_title=label_replace($mail_templates['set_teaallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//��ʦδͨ����ˣ������ʼ�
elseif($act == 'set_teanotallow'){
			$templates=label_replace($mail_templates['set_teanotallow']);
			$templates_title=label_replace($mail_templates['set_teanotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//�γ�ͨ����ˣ������ʼ�
elseif($act == 'set_couallow'){
			$templates=label_replace($mail_templates['set_couallow']);
			$templates_title=label_replace($mail_templates['set_couallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//�γ�δͨ����ˣ������ʼ�
elseif($act == 'set_counotallow'){
			$templates=label_replace($mail_templates['set_counotallow']);
			$templates_title=label_replace($mail_templates['set_counotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//������������γ̣������ʼ�
elseif($act == 'set_applycou'){
			$templates=label_replace($mail_templates['set_applycou']);
			$templates_title=label_replace($mail_templates['set_applycou_title']);
			smtp_mail($_GET['email'],$templates_title,$templates);
}
//�����������룬�����ʼ�
elseif($act == 'set_downapp'){
			$templates=label_replace($mail_templates['set_downapp']);
			$templates_title=label_replace($mail_templates['set_downapp_title']);
			smtp_mail($_GET['email'],$templates_title,$templates);
}
//��ͷͨ����ˣ������ʼ�
elseif($act == 'set_hunallow'){
			$templates=label_replace($mail_templates['set_hunallow']);
			$templates_title=label_replace($mail_templates['set_hunallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//��ͷδͨ����ˣ������ʼ�
elseif($act == 'set_hunnotallow'){
			$templates=label_replace($mail_templates['set_hunnotallow']);
			$templates_title=label_replace($mail_templates['set_hunnotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//�߼�ְλͨ����ˣ������ʼ�
elseif($act == 'set_hunjobsallow'){
			$templates=label_replace($mail_templates['set_hunjobsallow']);
			$templates_title=label_replace($mail_templates['set_hunjobsallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//�߼�ְλδͨ����ˣ������ʼ�
elseif($act == 'set_hunjobsnotallow'){
			$templates=label_replace($mail_templates['set_hunjobsnotallow']);
			$templates_title=label_replace($mail_templates['set_hunjobsnotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}

?>