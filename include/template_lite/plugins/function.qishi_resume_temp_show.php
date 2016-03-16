<?php
function tpl_function_qishi_resume_temp_show($params, &$smarty)
{
global $db,$_CFG,$QS_cookiepath,$QS_cookiedomain;
$arr=explode(',',$params['set']);
foreach($arr as $str)
{
$a=explode(':',$str);
	switch ($a[0])
	{
	case "����ID":
		$aset['id'] = $a[1];
		break;
	case "�б���":
		$aset['listname'] = $a[1];
		break;
	}
}
$aset=array_map("get_smarty_request",$aset);
$aset['id']=$aset['id']?intval($aset['id']):0;
$aset['listname']=$aset['listname']?$aset['listname']:"list";
$wheresql=" WHERE  id=".$aset['id']."";
$val=$db->getone("select id,uid,display,display_name,fullname,sex,sex_cn,major_cn,birthdate,photo,photo_img,photo_display,tag_cn,refreshtime,height,marriage_cn,education_cn,experience_cn,householdaddress,residence,talent,wage_cn,nature_cn,district_cn,trade_cn,intention_jobs,current_cn,specialty,title,telephone,email,addtime,resume_from_pc from ".table('resume_temp').$wheresql." LIMIT  1");
if(intval($_SESSION['utype'])==1){
	$company_profile = $db->getone("select companyname from ".table('company_profile')." where uid=".intval($_SESSION['uid']));
}
if ($val)
{
	setcookie('QS[view_resume_log]['.$val['id'].']',$val['id'],0,$QS_cookiepath,$QS_cookiedomain);
	if(intval($_SESSION['uid'])>0 && intval($_SESSION['utype'])==1)
	{
		//�����ҵ�Ƿ񱻸������ι�
		$company_profile = $db->getone("select companyname from ".table('company_profile')." where uid=".intval($_SESSION['uid']));

		//����Ƿ�鿴��
		$check = check_view_log(intval($_SESSION['uid']),$val['id']);
		if(!$check){
			add_view_log(intval($_SESSION['uid']),$val['id']);
			$sql="select did from ".table("personal_jobs_apply")." where resume_id=$val[id] and company_uid=".intval($_SESSION['uid'])." ";
			if($db->getone($sql)){
				$db->query("update ".table("personal_jobs_apply")." set personal_look=2 where  resume_id=$val[id] and company_uid=".intval($_SESSION['uid'])."");
			}
		}else{
			$db->query("update ".table("personal_jobs_apply")." set personal_look=2 where  resume_id=$val[id] and company_uid=".intval($_SESSION['uid'])."");
		}
		// ����������
		$resume_applyed = $db->getone("select count(*) num from ".table("personal_jobs_apply")." where  company_uid=".intval($_SESSION['uid'])." and resume_id=$val[id] ");
		if(!empty($resume_applyed))
		{
			$apply_see=$db->getone("select count(*) num from ".table("personal_jobs_apply")." where  company_uid=".intval($_SESSION['uid'])." and  personal_look=2 ");
			$apply_all=$db->getone("select count(*) num from ".table("personal_jobs_apply")." where  company_uid=".intval($_SESSION['uid'])." ");
			$company_info['resume_processing']=$apply_see['num']/$apply_all['num']*100;
			$db->updatetable(table("company_profile"),$company_info,array("uid"=>$_SESSION['uid']));
		}

		//�鿴�Ƿ��Ѿ����ع�����
		$download = $db->getone("select did from ".table("company_down_resume")." where resume_id=$val[id] and company_uid=".intval($_SESSION['uid'])." ");
		if($download){
			if ($val['display_name']=="2")
			{
				$val['fullname']="N".str_pad($val['id'],7,"0",STR_PAD_LEFT);
				$val['fullname_']=$val['fullname'];		
			}
			elseif($val['display_name']=="3")
			{
				if($val['sex']==1){
				$val['fullname']=cut_str($val['fullname'],1,0,"����");
				}elseif($val['sex'] == 2){
				$val['fullname']=cut_str($val['fullname'],1,0,"Ůʿ");
				}
			}
			else
			{
				$val['fullname_']=$val['fullname'];
				$val['fullname']=$val['fullname'];
			}
		}
		//��ʾ��Ϣ
		$mes_apply = $db->getone("select jobs_name,apply_addtime from ".table('personal_jobs_apply')." where `resume_id`=".$val['id']." and  `company_uid`=".intval($_SESSION['uid'])." limit 1 ");
		if($mes_apply)
		{
			$val['message'] = "ӦƸְλ��".$mes_apply['jobs_name']." Ͷ��ʱ�䣺".date('Y-m-d',$mes_apply['apply_addtime']);
		}
		else
		{
			$val['message'] = "";
		}
	}
	else
	{
		if ($val['display_name']=="2")
		{
			$val['fullname']="N".str_pad($val['id'],7,"0",STR_PAD_LEFT);
			$val['fullname_']=$val['fullname'];		
		}
		elseif($val['display_name']=="3")
		{
			if($val['sex']==1){
			$val['fullname']=cut_str($val['fullname'],1,0,"����");
			}elseif($val['sex'] == 2){
			$val['fullname']=cut_str($val['fullname'],1,0,"Ůʿ");
			}
		}
		else
		{
			$val['fullname_']=$val['fullname'];
			$val['fullname']=$val['fullname'];
		}
	}
	$val['education_list']=get_this_education($val['uid'],$val['id']);
	$val['work_list']=get_this_work($val['uid'],$val['id']);
	$val['training_list']=get_this_training($val['uid'],$val['id']);
	$val['language_list']=get_this_language($val['uid'],$val['id']);
	$val['credent_list']=get_this_credent($val['uid'],$val['id']);
	$val['img_list']=get_this_img($val['uid'],$val['id']);
	$val['age']=date("Y")-$val['birthdate'];

	if ($val['photo']=="1")
	{
		$download = $db->getone("select did from ".table("company_down_resume")." where resume_id=$val[id] and company_uid=".intval($_SESSION['uid'])." ");
		if(empty($download))
		{
			if($val['photo_display']=="1")
			{	
				$val['photosrc']=$_CFG['resume_photo_dir'].$val['photo_img'];
			}
			else
			{
				$val['photosrc']=$_CFG['resume_photo_dir_thumb']."no_photo_display.gif";
			}
		}
		else
		{
			$val['photosrc']=$_CFG['resume_photo_dir'].$val['photo_img'];
		}
	}
	else
	{
	$val['photosrc']=$_CFG['resume_photo_dir_thumb']."no_photo.gif";
	}

	if ($val['tag_cn'])
	{
		$tag_cn=explode(',',$val['tag_cn']);
		$val['tag_cn']=$tag_cn;
	}
	else
	{
	$val['tag_cn']=array();
	}
	$apply = $db->getone("select * from ".table('personal_jobs_apply')." where `resume_id`=".$val['id']);
	$val['jobs_name'] = $apply['jobs_name'];
	$val['apply_addtime'] = $apply['apply_addtime'];
	$val['jobs_url'] = url_rewrite('QS_jobsshow',array('id'=>$apply['jobs_id']));
	if($val['jobs_name']){
		$val['apply'] = 1;
	}else{
		$val['apply'] = 0;
	}
	/* ������Ծ��  ����ʱ�� ��������ְλ��  ���ְλ�� */
	$vitality=0;
	$val['refreshtime_cn']=daterange(time(),$val['refreshtime'],'Y-m-d',"#FF3300");
	$timestr=time()-$val['refreshtime'];
	$day= intval($timestr/86400);
	if($day<3)
	{
		$vitality+=2;
	}
	else
	{
		$vitality+=1;
	}
	$time=time()-15*86400;
	$val['apply_jobs']=$db->get_total("select count(*) num from ".table("personal_jobs_apply")." where resume_id=$val[id] and apply_addtime>$time and is_apply=1 ");
	if($val['apply_jobs']>0 && $val['apply_jobs']<10)
	{
		$vitality+=1;
	}
	elseif($val['apply_jobs']>=10)
	{
		$vitality+=2;
	}
	$val['view_jobs']=$db->get_total("select count(*) num from ".table("view_jobs")." where uid=$val[uid] and addtime>$time ");
	if($val['view_jobs']>=10)
	{
		$vitality+=1;
	}
	$val['vitality']=$vitality;
	/*��ҵ��ע�� start */
	$attention=0;
	$val['com_down']=$db->get_total("select count(*) num from ".table("company_down_resume")." where resume_id=$val[id] and down_addtime>$time ");
	
	if($val['com_down']>=0 && $val['com_down']<10)
	{
		$attention+=1;
	}
	elseif($val['com_down']>=10)
	{
		$attention+=2;
	}
	$val['com_invite']=$db->get_total("select count(*) num from ".table("company_interview")." where resume_id=$val[id] and interview_addtime>$time ");
	if($val['com_invite']>0 && $val['com_invite']<10)
	{
		$attention+=1;
	}
	elseif($val['com_invite']>=10)
	{
		$attention+=2;
	}
	$val['com_view']=$db->get_total("select count(*) num from ".table("view_resume")." where resumeid=$val[id] and addtime>$time ");
	if($val['com_view']>=10)
	{
		$attention+=1;
	}
	$val['attention']=$attention;
	/*��ҵ��ע�� end */
	//�ж��ֻ���΢�š������Ƿ���֤
	$is_audit_phone = $db->getone("SELECT mobile_audit,email_audit,weixin_openid FROM ".table('members')." WHERE uid={$val['uid']}  LIMIT 1 ");
	$val['is_audit_mobile'] = $is_audit_phone['mobile_audit'];
	$val['is_audit_email'] = $is_audit_phone['email_audit'];
	$val['is_audit_weixin'] = $is_audit_phone['weixin_openid'];
	//�����Լ�Ԥ��
	if($_SESSION['utype'] == '2' && $_SESSION['uid'] == $val['uid'] ){
		$val['isminesee'] = '1';
	}
}
else
{
	header("HTTP/1.1 404 Not Found"); 
	$smarty->display("404.htm");
	exit();
}
$smarty->assign($aset['listname'],$val);
}
function get_this_education($uid,$pid)
{
	global $db;
	$sql = "SELECT startyear,startmonth,endyear,endmonth,school,speciality,education_cn,todate FROM ".table('resume_education')." WHERE uid='".intval($uid)."' AND pid='".intval($pid)."' ";
	return $db->getall($sql);
}
function get_this_work($uid,$pid)
{
	global $db;
	$sql = "select startyear,startmonth,endyear,endmonth,jobs,companyname,achievements,todate from ".table('resume_work')." where uid=".intval($uid)." AND pid='".$pid."' " ;
	return $db->getall($sql);
}
function get_this_training($uid,$pid)
{
	global $db;
	$sql = "select startyear,startmonth,endyear,endmonth,agency,course,description,todate from ".table('resume_training')." where uid='".intval($uid)."' AND pid='".intval($pid)."'";
	return $db->getall($sql);
}
function get_this_language($uid,$pid)
{
	global $db;
	$sql = "select language_cn,level_cn from ".table('resume_language')." where uid='".intval($uid)."' AND pid='".intval($pid)."'";
	return $db->getall($sql);
}
function get_this_credent($uid,$pid)
{
	global $db;
	$sql = "select year,month,name,images from ".table('resume_credent')." where uid='".intval($uid)."' AND pid='".intval($pid)."'";
	return $db->getall($sql);
}
function get_this_img($uid,$pid)
{
	global $db;
	$sql = "select img from ".table('resume_img')." where uid='".intval($uid)."' AND resume_id='".intval($pid)."'";
	return $db->getall($sql);
}
function check_view_log($uid,$resumeid){
	global $db;
	$result = $db->getone("select id from ".table("view_resume")." where `uid`=".$uid." and `resumeid`=".$resumeid);
	return $result;
}
function add_view_log($uid,$resumeid){
	global $db;
	$setsqlarr['uid'] = $uid;
	$setsqlarr['resumeid'] = $resumeid;
	$setsqlarr['addtime'] = time();
	$db->inserttable(table("view_resume"),$setsqlarr);
}
?>