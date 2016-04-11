<?php
/*
 * 74cms �������� 
 */
 if(!defined('IN_QISHI'))
 {
	die('Access Denied!');
 }
//������±����Ƿ��ظ�
function ck_article_title($val)
{
	global $db;
	$sql = "select * from ".table('article')." where title='{$val}' LIMIT 1";
	$alist=$db->getone($sql);
	return $alist;
}
///--------------------------------
//��ȡ��˾��Ϣ
function get_companyinfo($val)
{
	global $db;
	$sql = "select * from ".table('company_profile')." where companyname='{$val}' AND  robot=1 LIMIT 1";
	return $db->getone($sql);
}
//���ְλ�����Ƿ����ظ�
function ck_jobs_name($val,$uid)
{
	global $db;
	$uid=intval($uid);
	$sql = "select id from ".table('jobs')." where jobs_name='{$val}' AND uid='{$uid}' LIMIT 1";
	$alist=$db->getone($sql);
	return $alist;
}
//ƥ����ҵ����
function locoyspider_company_nature($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select c_id,c_name from ".table('category')." where c_alias='QS_company_type' AND  c_id=".intval($locoyspider['company_nature'])." LIMIT 1";
	$nature=$db->getone($sql);
	$default=array("id"=>$nature['c_id'],"cn"=>$nature['c_name']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		if($str=='���ʣ�ŷ����' || $str=='���ʣ���ŷ����'){
			$default=array("id"=>49,"cn"=>'���̶���');
		}elseif($str=='����'){
			$default=array("id"=>48,"cn"=>'����');
		}elseif($str=='����'){
			$default=array("id"=>46,"cn"=>'����');
		}elseif($str=='��Ӫ��˾'){
			$default=array("id"=>47,"cn"=>'��Ӫ');
		}elseif($str=='�������й�˾'){
			$default=array("id"=>51,"cn"=>'���й�˾');
		}elseif($str=='��ҵ��λ'){
			$default=array("id"=>53,"cn"=>'��ҵ��λ');
		}else{
			$default=array("id"=>54,"cn"=>'����');
		}
		return $default;

	}
}
//ƥ����ҵ��ҵ
function locoyspider_company_trade($str=NULL)
{	
	global $db,$locoyspider;
	$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_trade' AND  c_id=".intval($locoyspider['company_trade'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_trade'";
		$info=$db->getall($sql);
		$return=locoyspider_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return $default;
		}
	}
}
//ƥ��ְλ����
function locoyspider_jobs_category($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select id,parentid,categoryname from ".table('category_jobs')." where id=".intval($locoyspider['jobs_subclass'])." LIMIT 1";
	$info=$db->getone($sql);
	$sql2 = "select id,parentid,categoryname from ".table('category_jobs')." where id=".intval($info['parentid'])." LIMIT 1";
	$info2=$db->getone($sql2);
	$default=array("topclass"=>$info2['id'],"category"=>$info['id'],"subclass"=>0,"category_cn"=>$info['categoryname']);
	//var_dump($default);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$sql = "select id,parentid,categoryname from ".table('category_jobs');
		$info=$db->getall($sql);
		$return=locoyspider_search_str($info,$str,"categoryname");
		//ƥ�䵽��
		if(!empty($return)){
			//����һ�������
			if($return['parentid']==0){
				return array("topclass"=>$return['id'],"category"=>0,"subclass"=>0,"category_cn"=>$return['categoryname']);
			}else{
				$sql2 = "select id,parentid,categoryname from ".table('category_jobs')." where id=".intval($return['parentid']);
				$info2=$db->getone($sql2);
				//���������
				if($info2['parentid']==0){
					return array("topclass"=>$info2['id'],"category"=>$return['id'],"subclass"=>0,"category_cn"=>$return['categoryname']);
				}
				//���������
				else{
					$sql3 = "select id,parentid,categoryname from ".table('category_jobs')." where id=".intval($info2['parentid']);
					$info3=$db->getone($sql3);
					return array("topclass"=>$info3['id'],"category"=>$info2['id'],"subclass"=>$return['id'],"category_cn"=>$return['categoryname']);
				}
				
			}

		}
		//û��ƥ�䵽
		else{
			return $default;
		}
		
	}
}
//ƥ����ҵ����
function locoyspider_company_district($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select id,parentid,categoryname from ".table('category_district')." where id=".intval($locoyspider['company_district'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("district"=>$info['parentid'],"sdistrict"=>$info['id'],"district_cn"=>$info['categoryname']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$sql = "select id,parentid,categoryname from ".table('category_district')." ";
		$info=$db->getall($sql);
		$return=locoyspider_search_str($info,$str,"categoryname");
		if ($return)
		{
		return array("district"=>$return['parentid'],"sdistrict"=>$return['id'],"district_cn"=>$return['categoryname']);	
		}
		else
		{
		return $default;
		}
	}
}
//ƥ�乤������
function locoyspider_jobs_district($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select id,parentid,categoryname from ".table('category_district')." where id=".intval($locoyspider['jobs_district'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("district"=>$info['parentid'],"sdistrict"=>$info['id'],"district_cn"=>$info['categoryname']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$sql = "select id,parentid,categoryname from ".table('category_district')." ";
		$info=$db->getall($sql);
		$return=locoyspider_search_str($info,$str,"categoryname");
		if ($return)
		{
		return array("district"=>$return['parentid'],"sdistrict"=>$return['id'],"district_cn"=>$return['categoryname']);
		}
		else
		{
		return $default;
		}
	}
}
//ƥ����ҵ��ģ
function locoyspider_company_scale($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_scale' and c_id=".intval($locoyspider['company_scale'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
	if (empty($str))
	{
		return $default;
	}
	elseif(trim($str)=='����50��'){
		return array("id"=>'80',"cn"=>'20������');
	}
	elseif(trim($str)=='50-150��'){
		return array("id"=>'81',"cn"=>'20-99��');
	}
	elseif(trim($str)=='150-500��'){
		return array("id"=>'82',"cn"=>'100-499��');
	}
	elseif(trim($str)=='500-1000��'){
		return array("id"=>'83',"cn"=>'500-999��');
	}
	elseif(trim($str)=='1000-5000��' || trim($str)=='5000-10000��'){
		return array("id"=>'84',"cn"=>'1000-9999��');
	}
	elseif(trim($str)=='10000������'){
		return array("id"=>'85',"cn"=>'10000������');
	}
	else
	{
		return $default;		
	}
}
//ƥ����ҵע���ʽ�
function locoyspider_company_registered($str=NULL)
{
	global $locoyspider;
	if (empty($str))
	{
		return array("registered"=>$locoyspider['company_registered'],"currency"=>$locoyspider['company_currency']);
	}
	else
	{
		return array("registered"=>$str,"currency"=>"");
	}
}
//ƥ��ְλ����
function locoyspider_jobs_nature($str=NULL)
{
	global $db,$locoyspider;
	
	$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_jobs_nature' AND c_id=".intval($locoyspider['jobs_nature'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_jobs_nature' ";
		$info=$db->getall($sql);
		$return=locoyspider_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return $default;
		}
	}
}
//ƥ��ְλ �Ա�
function locoyspider_jobs_sex($str=NULL)
{	
	return get_locoyspider_jobs_sex($str);
}
function get_locoyspider_jobs_sex($sex_cn=NULL,$sex=NULL)
{
		global $locoyspider;
		if ($sex_cn=="��" || $sex=="1")
		{
		return array("id"=>1,"cn"=>"��");
		}
		elseif ($sex_cn=="Ů" ||  $sex=="2")
		{
		return array("id"=>2,"cn"=>"Ů");
		}
		elseif ($sex_cn=="����"  ||  $sex=="3")
		{
		return array("id"=>3,"cn"=>"����");
		}
		else
		{
			if ($locoyspider['jobs_sex']=="0")
			{
			return get_locoyspider_jobs_sex("",3);
			}
			else
			{
			return get_locoyspider_jobs_sex("",intval($locoyspider['jobs_sex']));
			}
		}
}
//ƥ��ְλ ��Ƹ����
function locoyspider_jobs_amount($str=NULL)
{
	global $locoyspider;
	$str=intval($str);
	if ($str>0)
	{
		return $str;
	}
	else
	{
		return mt_rand(intval($locoyspider['jobs_amount_min']),intval($locoyspider['jobs_amount_max']));
	}
}
//ƥ��Ҫ��ѧ��
function locoyspider_jobs_education($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select c_id,c_name from ".table('category')." where c_alias='QS_education'  and c_id=".intval($locoyspider['jobs_education'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')."  where c_alias='QS_education'";
		$info=$db->getall($sql);
		$return=locoyspider_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return $default;
		}
	}
}
//ƥ��Ҫ��������1(ģ��ƥ�䲻�ʺ�51job�ɼ�)
function locoyspider_jobs_experience($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_experience' AND c_id=".intval($locoyspider['jobs_experience'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_experience'";
		$info=$db->getall($sql);
		$return=locoyspider_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return $default;
		}
	}
}
//ƥ��Ҫ��������3(�ʺ�51job�ɼ�)
function get_experience($str=NULL){
	$str=intval($str);
	$arr=array();
	if($str=="1��" || $str=="2��"){
		$arr=array('id'=>76,'cn'=>'1-3��');
	}elseif($str=="3-4��"){
		$arr=array('id'=>77,'cn'=>'3-5��');
	}elseif($str=="5-7��" || $str=="8-9��"){
		$arr=array('id'=>78,'cn'=>'5-10��');
	}elseif($str=="10������"){
		$arr=array('id'=>79,'cn'=>'10������');
	}else{
		$arr=array('id'=>74,'cn'=>'�޾���');
	}
	return $arr;
}
//ƥ��н�ʴ���
/*function locoyspider_jobs_wage($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select  c_id,c_name from ".table('category')." where  c_alias='QS_wage' and c_id=".intval($locoyspider['jobs_wage'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$sql = "select c_id,c_name from ".table('category')." where  c_alias='QS_wage'";
		$info=$db->getall($sql);
		$return=locoyspider_search_str($info,$str,"c_name");
		if ($return)
		{
		return array("id"=>$return['c_id'],"cn"=>$return['c_name']);
		}
		else
		{
		return $default;
		}
	}
}*/
//�޸ĺ��ƥ��н�ʴ���
function locoyspider_jobs_wage($str=NULL)
{
	global $db,$locoyspider;
	$sql = "select  c_id,c_name from ".table('category')." where  c_alias='QS_wage' and c_id=".intval($locoyspider['jobs_wage'])." LIMIT 1";
	$info=$db->getone($sql);
	$default=array("id"=>$info['c_id'],"cn"=>$info['c_name']);
	if (empty($str))
	{
		return $default;
	}
	else
	{
		$str = trim($str);
		if($str=='����'){
			return array('id'=>55,'cn'=>'����');
		}elseif($str=="1500����"){
			return array('id'=>56,'cn'=>'1000~1500Ԫ/��');
		}elseif($str=="1500-1999"){
			return array('id'=>57,'cn'=>'1500~2000Ԫ/��');
		}elseif($str=="2000-2999"){
			return array('id'=>58,'cn'=>'2000~3000Ԫ/��');
		}elseif($str=="3000-4499" || $str=="3500+" || $str=="3500-6000"){
			return array('id'=>59,'cn'=>'3000~5000Ԫ/��');
		}elseif($str=="4500-5999" || $str=="5000-8000" || $str=="6000-7999" || $str=="8000-9999"){
			return array('id'=>60,'cn'=>'5000~10000Ԫ/��');
		}elseif($str=="10000-14999" || $str=="15000-19999"){
			return array('id'=>61,'cn'=>'һ������/��');
		}else{
			return array('id'=>$info['c_id'],'cn'=>$info['c_name']);
		}
	}
}
//���ɵ���ʱ��
function locoyspider_jobs_deadline()
{
	global $locoyspider;
	$jobs_days_min=intval($locoyspider['jobs_days_min']);
	$jobs_days_max=intval($locoyspider['jobs_days_max']);
	if ($jobs_days_min==0 && $jobs_days_max==0)
	{
	return strtotime("30 day");
	}
	else
	{
	return strtotime("".mt_rand($jobs_days_min,$jobs_days_max)." day");
	}
}
//�ɼ�ע���Ա
function locoyspider_user_register($email=NULL,$utype='1')
{
	global $db,$locoyspider,$QS_pwdhash,$_CFG;
	$setsqlarr['username']=$locoyspider['reg_usname'].uniqid().time();
	$setsqlarr['pwd_hash']=res_randstr();
		//reg_password
		if ($locoyspider['reg_password_tpye']=="1")//����=�û���
		{
			$pwd=$setsqlarr['username'];
		}
		elseif ($locoyspider['reg_password_tpye']=="3")//����=�̶�����ֵ
		{
			$pwd=$locoyspider['reg_password'];
		}
		else
		{
			$pwd=res_randstr(7);//����Ϊ7������ַ���
		}
		//email
		if (empty($email) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$email))//����У��email
		{
			$email=time().uniqid().$locoyspider['reg_email'];
		}
	$setsqlarr['password']=md5(md5($pwd).$setsqlarr['pwd_hash'].$QS_pwdhash);
	$setsqlarr['email']=$email;
	$setsqlarr['utype']=$utype;
	$setsqlarr['reg_time']=time();
	$setsqlarr['robot']=1;//���Ϊ�ɼ�
	$reg_id=$db->inserttable(table('members'),$setsqlarr,true);
	if (!$reg_id) return false;
	if($utype=='1'){
		if(!$db->query("INSERT INTO ".table('members_points')." (uid) VALUES ('{$reg_id}')"))  return false;
		if(!$db->query("INSERT INTO ".table('members_setmeal')." (uid) VALUES ('{$reg_id}')"))  return false;

		$points=get_cache('points_rule');
		include_once(QISHI_ROOT_PATH.'include/fun_company.php');
		set_consultant($reg_id);
		if ($points['reg_points']['value']>0)
		{
			report_deal($reg_id,$points['reg_points']['type'],$points['reg_points']['value']);
			$operator=$points['reg_points']['type']=="1"?"+":"-";
			write_memberslog($reg_id,1,9001,$username,"��ע���Ա,({$operator}{$points['reg_points']['value']}),(ʣ��:{$points['reg_points']['value']})",1,1010,"ע���Աϵͳ�Զ����ͺ�«��","{$operator}{$points['reg_points']['value']}","{$points['reg_points']['value']}");
			//��«�ұ����¼
			write_setmeallog($reg_id,$username,"ע���Աϵͳ�Զ����ͣ�({$operator}{$points['reg_points']['value']}),(ʣ��:{$points['reg_points']['value']})",1,'0.00','1',1,1);
		
		}
		if ($_CFG['reg_service']>0){
			set_members_setmeal($reg_id,$_CFG['reg_service']);
			$setmeal=get_setmeal_one($_CFG['reg_service']);
			write_memberslog($reg_id,1,9002,$username,"ע���Աϵͳ�Զ����ͣ�{$setmeal['setmeal_name']}",2,1011,"��ͨ����(ϵͳ����)","-","-");
			//�ײͱ����¼
			write_setmeallog($reg_id,$username,"ע���Աϵͳ�Զ����ͣ�{$setmeal['setmeal_name']}",1,'0.00','1',2,1);
		}
	}
	return $reg_id;
}
//���ְλ
function locoyspider_addjobs($companyinfo)
{
		global $locoyspider,$db;
		$jobssetsqlarr['uid']=$companyinfo['uid'];		
		$jobssetsqlarr['companyname']=$companyinfo['companyname'];
		$jobssetsqlarr['company_id']=$companyinfo['id'];		
		$jobssetsqlarr['company_addtime']=$companyinfo['addtime'];
		$jobssetsqlarr['jobs_name']=trim($_POST['jobs_name']);
		if (empty($jobssetsqlarr['jobs_name']))  exit("ְλ���ƶ�ʧ");
		if (ck_jobs_name($jobssetsqlarr['jobs_name'],$jobssetsqlarr['uid'])) exit("ְλ�������ظ�");
		$jobssetsqlarr['contents']=html2text($_POST['jobs_contents']);
			$nature=locoyspider_jobs_nature(trim($_POST['jobs_nature']));
		$jobssetsqlarr['nature']=$nature['id'];
		$jobssetsqlarr['nature_cn']=$nature['cn'];
			$sex=locoyspider_jobs_sex(trim($_POST['jobs_sex']));
		$jobssetsqlarr['sex']=$sex['id'];
		$jobssetsqlarr['sex_cn']=$sex['cn'];
		//����Ҫ��
		$jobssetsqlarr['age']=trim($_POST['jobs_age']);
		$jobssetsqlarr['amount']=locoyspider_jobs_amount(trim($_POST['jobs_amount']));
		$jobs_category=trim($_POST['jobs_category'])?trim($_POST['jobs_category']):$jobssetsqlarr['jobs_name'];
			$category=locoyspider_jobs_category($jobs_category);//$_POST['jobs_category']
		//һ��
		$jobssetsqlarr['topclass']=$category['topclass'];
		$jobssetsqlarr['category']=$category['category'];
		$jobssetsqlarr['subclass']=$category['subclass'];
		$jobssetsqlarr['category_cn']=$category['category_cn'];
		$jobssetsqlarr['trade']=$companyinfo['trade'];
		$jobssetsqlarr['trade_cn']=$companyinfo['trade_cn'];
			$district=locoyspider_jobs_district(trim($_POST['jobs_district']));
		$jobssetsqlarr['scale']=$companyinfo['scale'];
		$jobssetsqlarr['scale_cn']=$companyinfo['scale_cn'];
		$jobssetsqlarr['district']=$district['district'];
		$jobssetsqlarr['sdistrict']=$district['sdistrict'];
		$jobssetsqlarr['district_cn']=$district['district_cn'];
		//�ֵ�id �� �ֵ�
		$jobssetsqlarr['street']=$companyinfo['street'];		
		$jobssetsqlarr['street_cn']=$companyinfo['street_cn'];
			$education=locoyspider_jobs_education(trim($_POST['jobs_education']));
		$jobssetsqlarr['education']=$education['id'];
		$jobssetsqlarr['education_cn']=$education['cn'];
			$experience=get_experience(trim($_POST['jobs_experience']));
		$jobssetsqlarr['experience']=$experience['id'];	
		$jobssetsqlarr['experience_cn']=$experience['cn'];
			$wage=locoyspider_jobs_wage(trim($_POST['jobs_wage']));
		$jobssetsqlarr['wage']=$wage['id'];
		$jobssetsqlarr['wage_cn']=$wage['cn'];
		$jobssetsqlarr['addtime']=time();
		$jobssetsqlarr['deadline']=locoyspider_jobs_deadline();
		$jobssetsqlarr['refreshtime']=time();
		$jobssetsqlarr['key']=$jobssetsqlarr['jobs_name'].$companyinfo['companyname'].$jobssetsqlarr['category_cn'].$jobssetsqlarr['district_cn'].$jobssetsqlarr['contents'];
		require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
		$sp = new SPWord();
		$jobssetsqlarr['key']="{$jobssetsqlarr['jobs_name']} {$companyinfo['companyname']} ".$sp->extracttag($jobssetsqlarr['key']);
		$jobssetsqlarr['key']=$sp->pad($jobssetsqlarr['key']);
		$jobssetsqlarr['audit']=$locoyspider['jobs_audit'];
		$jobssetsqlarr['display']=$locoyspider['jobs_display'];
		$jobssetsqlarr['robot']=1;
		$pid=$db->inserttable(table('jobs'),$jobssetsqlarr,true);
		if (!$pid) exit("�����Ƹ��Ϣʧ��");
		//ְλ��ϵ��ʽ
		$setsqlarr_contact['contact']=trim($_POST['contact']);
		//QQ
		$setsqlarr_contact['qq']=trim($_POST['qq']);
		$setsqlarr_contact['telephone']=trim($_POST['telephone']);
		$setsqlarr_contact['address']=trim($_POST['address']);
		$setsqlarr_contact['email']=check_email(trim($_POST['email']));
			//3.4�����ֶ�,3.5Ҳ��
		$setsqlarr_contact['contact_show']=1;
		$setsqlarr_contact['telephone_show']=1;
		$setsqlarr_contact['email_show']=1;
		$setsqlarr_contact['address_show']=1;
		$setsqlarr_contact['qq_show']=1;

		$setsqlarr_contact['notify']=$locoyspider['jobs_notify'];
		$setsqlarr_contact['pid']=$pid;
		if (!$db->inserttable(table('jobs_contact'),$setsqlarr_contact)) exit("�����Ƹ��ϵ��ʽʧ��");
		//------
		$searchtab['id']=$pid;
		$searchtab['uid']=$jobssetsqlarr['uid'];
		$searchtab['recommend']=$jobssetsqlarr['recommend'];
		$searchtab['emergency']=$jobssetsqlarr['emergency'];
		$searchtab['nature']=$jobssetsqlarr['nature'];
		$searchtab['sex']=$jobssetsqlarr['sex'];
		$searchtab['topclass']=$jobssetsqlarr['topclass'];
		$searchtab['category']=$jobssetsqlarr['category'];
		$searchtab['subclass']=$jobssetsqlarr['subclass'];
		$searchtab['trade']=$jobssetsqlarr['trade'];
		$searchtab['district']=$jobssetsqlarr['district'];
		$searchtab['sdistrict']=$jobssetsqlarr['sdistrict'];	
		$searchtab['street']=$companyinfo['street'];	
		$searchtab['education']=$jobssetsqlarr['education'];
		$searchtab['experience']=$jobssetsqlarr['experience'];
		$searchtab['wage']=$jobssetsqlarr['wage'];
		$searchtab['refreshtime']=$jobssetsqlarr['refreshtime'];
		$searchtab['scale']=$jobssetsqlarr['scale'];	
		//
		$db->inserttable(table('jobs_search_wage'),$searchtab);
		$db->inserttable(table('jobs_search_scale'),$searchtab);
		$db->inserttable(table('jobs_search_rtime'),$searchtab);
		//
		$searchtab['stick']=$jobssetsqlarr['stick'];
		$db->inserttable(table('jobs_search_stickrtime'),$searchtab);
		unset($searchtab['stick']);
		//
		$searchtab['click']=$jobssetsqlarr['click'];
		$db->inserttable(table('jobs_search_hot'),$searchtab);
		unset($searchtab['click']);
		//
		$searchtab['likekey']=$jobssetsqlarr['jobs_name'].','.$jobssetsqlarr['companyname'];
		$searchtab['key']=$jobssetsqlarr['key'];
		$db->inserttable(table('jobs_search_key'),$searchtab);
		require_once(ADMIN_ROOT_PATH.'include/admin_company_fun.php');
		distribution_jobs($pid);
		exit("��ӳɹ�");	
}
//�����ҵ
function locoyspider_addcompany($companyname)
{
	global $locoyspider,$db;
		$setsqlarr['uid']=locoyspider_user_register(check_email(trim($_POST['email'])));
		if ($setsqlarr['uid']=="") exit("��ӻ�Ա����");
		$setsqlarr['companyname']=$companyname;
			$nature=locoyspider_company_nature(trim($_POST['nature']));
		$setsqlarr['nature']=$nature['id'];
		$setsqlarr['nature_cn']=$nature['cn'];
			$trade=locoyspider_company_trade(trim($_POST['trade']));
		$setsqlarr['trade']=$trade['id'];
		$setsqlarr['trade_cn']=$trade['cn'];
			$district=locoyspider_company_district(trim($_POST['district']));
		$setsqlarr['district']=$district['district'];
		$setsqlarr['sdistrict']=$district['sdistrict'];
		$setsqlarr['district_cn']=$district['district_cn'];
			$scale=locoyspider_company_scale(trim($_POST['scale']));
		$setsqlarr['scale']=$scale['id'];
		$setsqlarr['scale_cn']=$scale['cn'];
	 		$registered=locoyspider_company_registered(trim($_POST['registered']));
		$setsqlarr['registered']=$registered['registered'];//ע���ʽ�
		$setsqlarr['currency']=$registered['currency'];//ע���ʽ�λ������� or ��Ԫ��
		$setsqlarr['address']=trim($_POST['address']);
		$setsqlarr['contact']=trim($_POST['contact']);
		$setsqlarr['telephone']=trim($_POST['telephone']);
		$setsqlarr['email']=trim($_POST['email']);
		$setsqlarr['website']=trim($_POST['website']);
		$setsqlarr['contents']=html2text($_POST['contents']);
		$setsqlarr['audit']=intval($locoyspider['company_audit']);
		$setsqlarr['addtime']=time();
		$setsqlarr['refreshtime']=time();
		$setsqlarr['robot']=1;
			//3.4�����ֶ�,3.5Ҳ��
		$setsqlarr['contact_show']=1;
		$setsqlarr['telephone_show']=1;
		$setsqlarr['email_show']=1;
		$setsqlarr['address_show']=1;
		if (!$db->inserttable(table('company_profile'),$setsqlarr)) exit("�����ҵ����");
		return true;
}
//��ȡ����ַ���
function res_randstr($length=6)
{
	$hash='';
	$chars= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz@#!~?:-=';   
	$max=strlen($chars)-1;   
	mt_srand((double)microtime()*1000000);   
	for($i=0;$i<$length;$i++)   {   
	$hash.=$chars[mt_rand(0,$max)];   
	}   
	return $hash;   
}
//ģ������
function locoyspider_search_str($arr,$str,$arrinname)
{
		global $locoyspider;
 
		foreach ($arr as $key =>$list)
		{
			similar_text($list[$arrinname],$str,$percent);
			$od[$percent]=$key;
		}
			krsort($od);
			foreach ($od as $key =>$li)
			{
				if ($key>=$locoyspider['search_threshold'])
				{
				return $arr[$li];
				}
				else
				{
				return false;
				}
			}	
}
/*
 * ���ܣ������󹤲ɼ���ͼƬ���ɵ�email
 * params����������email
 * return���������email
 */
function check_email($val)
{
	$str_email = strtolower($val);
	$str_email=str_replace(' ','',$str_email);//ɾ������ǿո�
	$str_email=str_replace('��','',$str_email);//ɾ����ȫ�ǿո�
	$str_email=str_replace('c0m','com',$str_email);
	$str_email=str_replace('-com','.com',$str_email);
	$str_email=stripslashes($str_email);
	$str_email=str_replace("co|\\",'com',$str_email);
	return $str_email;
}
?>