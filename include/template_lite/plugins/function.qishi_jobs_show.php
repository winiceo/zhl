<?php
function tpl_function_qishi_jobs_show($params, &$smarty)
{
	global $db,$timestamp,$_CFG;
	$arr=explode(',',$params['set']);

	foreach($arr as $str)
	{
	$a=explode(':',$str);
		switch ($a[0])
		{
		case "ְλID":
			$aset['id'] = $a[1];
			break;
		case "�б���":
			$aset['listname'] = $a[1];
			break;
		case "��������":
			$aset['brieflylen'] = $a[1];
			break;
		case "��ַ�":
			$aset['dot'] = $a[1];
			break;
		}
	}
	$aset=array_map("get_smarty_request",$aset);
	$aset['id']=$aset['id']?intval($aset['id']):0;

	$aset['brieflylen']=isset($aset['brieflylen'])?intval($aset['brieflylen']):0;
	$aset['listname']=$aset['listname']?$aset['listname']:"list";
 	$smarty->assign("is_reward",get_jobs_is_reward($aset['id']));

	$wheresql=" WHERE id={$aset['id']} ";
	$sql = "select id,subsite_id,uid,audit,display,setmeal_deadline,add_mode,amount,company_id,district_cn,contents,refreshtime,tag_cn,category,subclass,sdistrict,jobs_name,companyname,wage_cn,nature_cn,category_cn,sex_cn,age,education_cn,experience_cn,deadline,graduate from ".table('jobs').$wheresql." LIMIT 1";
	$val=$db->getone($sql);
	if(empty($val))
	{
		$sql_tmp = "select id,subsite_id,uid,audit,display,setmeal_deadline,add_mode,amount,company_id,district_cn,contents,refreshtime,tag_cn,category,subclass,sdistrict,jobs_name,companyname,wage_cn,nature_cn,category_cn,sex_cn,age,education_cn,deadline,experience_cn from ".table('jobs_tmp').$wheresql." LIMIT 1";
		$val=$db->getone($sql_tmp);
	}
	if (empty($val))
	{
		header("HTTP/1.1 404 Not Found");
		$smarty->display("404.htm");
		exit();
	}
	check_url($val['subsite_id'],$smarty,$_CFG['job_url']);
	if($val['deadline']<time())
	{
		$val['jobs_gq']=1;
	}
	elseif($val['audit']<>'1' || $val['display']<>'1' || ($val['setmeal_deadline']<>'0' && $val['setmeal_deadline']<time()))
	{
		$val['jobs_gq']=2;
	}
	
	setcookie('QS[view_jobs_log]['.$val['id'].']',$val['id'],0,$QS_cookiepath,$QS_cookiedomain);
	if(intval($_SESSION['uid'])>0 && intval($_SESSION['utype'])==2){
		//����Ƿ񿴹���ְλ
		$check = check_view_log(intval($_SESSION['uid']),$val['id']);
		if(!$check){
			add_view_log(intval($_SESSION['uid']),$val['id']);
		}
		//����ְλ�Ƿ�Դ˻�Ա������������,���Ҵ˻�Աû��
		$check_int = check_interview(intval($_SESSION['uid']),$val['id']);
		if($check_int){
			update_interview(intval($_SESSION['uid']),$val['id']);
		}
		//����ְλ�Ƿ񱻴˻�Ա�ղع�
		$check_fav = check_favorites(intval($_SESSION['uid']),$val['id']);
		if($check_fav)
		{
			$val['check_fav']=1;
		}
	}
	if ($val['setmeal_deadline']<time() && $val['setmeal_deadline']<>"0" && $val['add_mode']=="2")
	{
	$val['deadline']=$val['setmeal_deadline'];
	}
	$val['amount']=$val['amount']=="0"?'����':$val['amount'];
	$val['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$val['id']),1,$val['subsite_id']);
	$profile=GetJobsCompanyProfile($val['company_id']);
	$val['company']=$profile;
	$val['contact']=GetJobsContact($val['id']);
	$district_cn = $val['district_cn'];
	$d_arr = explode("/", $district_cn);
	$val['district_ch'] = $d_arr[0];
	$val['sdistrict_ch'] = $d_arr[1];
	$val['expire']=sub_day($val['deadline'],time());	
	$val['countresume']=$db->get_total("SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply')." WHERE jobs_id= '{$val['id']}'");
	if ($aset['brieflylen']>0)
	{
		$val['briefly']=cut_str(strip_tags($val['contents']),$aset['brieflylen'],0,$aset['dot']);
	}
	else
	{
		$val['briefly']=strip_tags($val['contents']);
	}
	$val['contents'] = htmlspecialchars_decode($val['contents'],ENT_QUOTES);
	$val['refreshtime_cn']=daterange(time(),$val['refreshtime'],'Y-m-d',"#FF3300");
	$val['company_url']=url_rewrite('QS_companyshow',array('id'=>$val['company_id']));
	if ($val['company']['logo'])
	{
	$val['company']['logo']=$_CFG['site_dir']."data/logo/".$val['company']['logo'];
	}
	else
	{
	$val['company']['logo']=$_CFG['site_dir']."data/logo/no_logo.gif";
	}
	if($val['company']['website']){
		if(strstr($val['company']['website'],"http://")===false){
			$val['company']['website'] = "http://".$val['company']['website'];
		}
	}
	if(intval($_SESSION['utype'])==2){
		$view_log = get_view_log(intval($_SESSION['uid']));
		foreach ($view_log as $key => $value) {
			$jobs_info = $db->getone("select id,subsite_id,company_id,jobs_name,companyname from ".table('jobs')." where id=".$value['jobsid']);
			$val['view_log'][$key]['jobsid'] = $jobs_info['id'];
			$val['view_log'][$key]['jobs_name'] = $jobs_info['jobs_name'];
			$val['view_log'][$key]['jobs_url'] = url_rewrite('QS_jobsshow',array('id'=>$jobs_info['id']),1,$jobs_info['subsite_id']);
			$val['view_log'][$key]['companyname'] = $jobs_info['companyname'];
		}
		$interest_id = get_interest_jobs_id(intval($_SESSION['uid']));
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
	/*
		н��ͳ��
		���ְλ���������� �����ְλ�� subclass
		���ְλû������������ �����ְλ�� category
	 */
	if($val['subclass'])
	{
		$salary_data= $db->getall("select c.c_name from ".table('jobs_search_wage')." as j left join ".table('category')." as c on c.c_id=j.wage where j.subclass=".$val['subclass']." and j.sdistrict=".$val['sdistrict']);;
	}
	else
	{
		$salary_data = $db->getall("select c.c_name from ".table('jobs_search_wage')." as j left join ".table('category')." as c on c.c_id=j.wage where j.category=".$val['category']." and j.sdistrict=".$val['sdistrict']);
	}
	if($salary_data)
	{
		$total = 0;
		$total_salary = 0;
		foreach ($salary_data as $key => $value) 
		{
			$total_salary += intval($value['c_name'])+500;
			$total++;
		}
		$val['salary']['value'] = intval($total_salary/$total);
		$val['salary']['px'] = ($val['salary']['value']/12000*446)."px";
	}
	/*
		н��ͳ��
	*/ 
	$user=get_jobs_username($val['uid']);
	$hashstr=substr(md5($user['username']),8,16);
	$smarty->assign('hashstr',$hashstr);
	$smarty->assign($aset['listname'],$val);
}
function GetJobsCompanyProfile($id)
{
	global $db;
	$sql = "select * from ".table('company_profile')." where id=".intval($id)." LIMIT 1 ";
	return $db->getone($sql);
}
function GetJobsContact($id)
{
	global $db;
	$sql = "select address from ".table('jobs_contact')." where pid=".intval($id)." LIMIT 1 ";
	return $db->getone($sql);
}
function check_view_log($uid,$jobsid){
	global $db;
	$result = $db->getone("select id from ".table("view_jobs")." where `uid`=".$uid." and `jobsid`=".$jobsid);
	return $result;
}
function check_interview($uid,$jobsid){
	global $db;
	$result = $db->getone("select did from ".table("company_interview")." where `personal_look`=1 and  `resume_uid`=".$uid." and `jobs_id`=".$jobsid);
	return $result;
}
function update_interview($uid,$jobsid){
	global $db;
	$setsqlarr['personal_look'] = 2;
	$db->updatetable(table("company_interview"),$setsqlarr," `resume_uid`=".$uid." and `jobs_id`=".$jobsid );
}
function check_favorites($uid,$jobsid){
	global $db;
	$result = $db->getone("select did from ".table("personal_favorites")." where `personal_uid`=".$uid." and `jobs_id`=".$jobsid);
	return $result;
}
function add_view_log($uid,$jobsid){
	global $db;
	$setsqlarr['uid'] = $uid;
	$setsqlarr['jobsid'] = $jobsid;
	$setsqlarr['addtime'] = time();
	$db->inserttable(table("view_jobs"),$setsqlarr);
}
function get_view_log($uid){
	global $db;
	$result = $db->getall("select * from ".table("view_jobs")." where `uid`=".$uid." order by id desc limit 5");
	return $result;
}
function get_interest_jobs_id($uid)
{
	global $db;
	$uid=intval($uid);
	$sql = "select id from ".table('resume')." where   uid='{$uid}' LIMIT 3 ";
	$info=$db->getall($sql);
	if (is_array($info))
	{
		foreach($info as $s)
		{
			$jobsid=get_resume_jobs($s['id']);
			if(is_array($jobsid))
			{
			foreach($jobsid as $cid)
			 {
			 $interest_id[]=$cid['category'];
			 }
			}
		}
		if (is_array($interest_id)) return implode("-",array_unique($interest_id));
	}
	return "";	
}
//��ȡ����ְλ
function get_resume_jobs($pid)
{
	global $db;
	$pid=intval($pid);
	$sql = "select * from ".table('resume_jobs')." where pid='{$pid}'  LIMIT 20" ;
	return $db->getall($sql);
}
//ģ��ƥ��
function search_strs($arr,$str)
{
	foreach ($arr as $key =>$list)
	{
		similar_text($list,$str,$percent);
		$od[$percent]=$key;
	}
	krsort($od);
	foreach ($od as $key =>$li)
	{
		if ($key>=60)
		{
			return $li;
		}
		else
		{
			return false;
		}
	}
} 
function get_jobs_username($uid)
{
	global $db;
	$uid=intval($uid);
	return $db->getone("select username from ".table('members')." where uid=$uid");
}


//�Ƿ������ͼ���
function get_jobs_is_reward($jobsid)
{
    global $db;

    $rs= $db->getone("select * from ".table('promotion')."   where    cp_jobid={$jobsid} and cp_promotionid=5 LIMIT 1");

    return $rs;
 }
?>