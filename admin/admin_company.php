<?php
 /*
 * 74cms ��ҵ�û����
*/
define('IN_QISHI', true);

require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');

require_once(ADMIN_ROOT_PATH.'include/admin_company_fun.php');
require_once(QISHI_ROOT_PATH . 'genv/func_company.php');


$act = !empty($_GET['act']) ? trim($_GET['act']) : 'jobs';
if($act == 'jobs')
{
	check_permissions($_SESSION['admin_purview'],"jobs_show");
	$audit=intval($_GET['audit']);
	$invalid=intval($_GET['invalid']);
	$deadline=intval($_GET['deadline']);
	$jobtype=intval($_GET['jobtype']);
	if (empty($jobtype))
	{
		$jobtype=1;
		$_GET['jobtype']=1;
	}
	if ($jobtype==1)
	{
	$tablename="jobs";
	$audit="";
	$deadline=$deadline>2?$deadline:'';
	}
	else
	{
	$tablename="jobs_tmp";
	}
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY id DESC";
	$wheresqlarr=array();
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if (!empty($key) && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE jobs_name like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE companyname like '%{$key}%'";
		elseif ($key_type===3 && intval($key)>0)$wheresql=" WHERE id =".intval($key);
		elseif ($key_type===4 && intval($key)>0)$wheresql=" WHERE company_id =".intval($key);
		elseif ($key_type===5 && intval($key)>0)$wheresql=" WHERE uid =".intval($key);
		$oederbysql="";
		$tablename="all";
	}
	else
	{
			if ($audit>0)
			{
			$wheresqlarr['audit']=$audit;
			}
			if(isset($_GET['emergency']) && $_GET['emergency']<>'')
			{
			$wheresqlarr['emergency']=intval($_GET['emergency']);
			$oederbysql=" order BY refreshtime DESC";
			}
			if(isset($_GET['recommend']) && $_GET['recommend']<>'')
			{
			$wheresqlarr['recommend']=intval($_GET['recommend']);
			$oederbysql=" order BY refreshtime DESC";
			}
			if (!empty($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
			if (!empty($_GET['settr']))
			{
				$settr=strtotime("-".intval($_GET['settr'])." day");
				$wheresql=empty($wheresql)?" WHERE refreshtime> ".$settr:$wheresql." AND refreshtime> ".$settr;
				$oederbysql=" order BY refreshtime DESC ";
			}
			if (!empty($_GET['addsettr']))
			{
				$settr=strtotime("-".intval($_GET['addsettr'])." day");
				$wheresql=empty($wheresql)?" WHERE addtime> ".$settr:$wheresql." AND addtime> ".$settr;
				$oederbysql=" order BY addtime DESC ";
			}
			//��Чԭ��(1->ְλ����  2->�ײ͵���  3->ְλ��ͣ  4->���δͨ��)
			if ($invalid==1)
			{
			$wheresql=empty($wheresql)?" WHERE deadline< ".time():$wheresql." AND deadline< ".time();
			$oederbysql=" order BY deadline DESC ";
			}
			elseif($invalid==2)
			{
			$wheresql=empty($wheresql)?" WHERE setmeal_deadline!=0 AND setmeal_deadline< ".time():$wheresql." AND  setmeal_deadline!=0 AND  setmeal_deadline< ".time();
			$oederbysql=" order BY setmeal_deadline DESC ";
			}
			elseif($invalid==3)
			{
			$wheresql=empty($wheresql)?" WHERE display=2 ":$wheresql." AND  display=2 ";
			$oederbysql=" order BY refreshtime DESC ";
			}
			elseif($invalid==4)
			{
			$wheresql=empty($wheresql)?" WHERE audit!=1 ":$wheresql." AND  audit!=1 ";
			$oederbysql=" order BY deadline DESC ";
			}

			if($deadline==1)
			{
			$wheresql=empty($wheresql)?" WHERE deadline< ".time():$wheresql." AND deadline< ".time();
			$oederbysql=" order BY deadline DESC ";
			}
			elseif($deadline==2)
			{			
			$wheresql=empty($wheresql)?" WHERE deadline>  ".time():$wheresql." AND deadline> ".time();
			$oederbysql=" order BY deadline DESC ";
			}
			elseif($deadline>2)
			{
			$settr=strtotime("+{$deadline} day");
			$wheresql=empty($wheresql)?" WHERE deadline< {$settr}":$wheresql." AND deadline<{$settr} ";
			$oederbysql=" order BY deadline DESC ";
			}
			
			if (!empty($_GET['promote']))
			{
				$promote=intval($_GET['promote']);
				if ($promote==-1)
				{
				$psql="recommend=0 AND emergency=0 AND stick=0 AND highlight=''";
				$wheresql=empty($wheresql)?" WHERE {$psql} ":"{$wheresql} AND {$psql} ";
				}
				elseif ($promote==1)
				{
				$psql="recommend=1";
				$wheresql=empty($wheresql)?" WHERE {$psql} ":"{$wheresql} AND {$psql} ";
				}
				elseif ($promote==2)
				{
				$psql="emergency=1";
				$wheresql=empty($wheresql)?" WHERE {$psql} ":"{$wheresql} AND {$psql} ";
				}
				elseif ($promote==3)
				{
				$psql="stick=1";
				$wheresql=empty($wheresql)?" WHERE {$psql} ":"{$wheresql} AND {$psql} ";
				}
				elseif ($promote==4)
				{
				$psql="highlight<>'' ";
				$wheresql=empty($wheresql)?" WHERE {$psql} ":"{$wheresql} AND {$psql} ";
				}
				$oederbysql="";
			}
		 
	}
	$subsite_sql = '';
	if (intval($_CFG['subsite_id'])>0)
	{
		$subsite_sql=" subsite_id=".intval($_CFG['subsite_id'])." ";
		$wheresql=empty($wheresql)?" WHERE {$subsite_sql} ":"{$wheresql} AND {$subsite_sql} ";
	}
	if ($tablename=="all")
	{
	$total_sql="SELECT COUNT(*) AS num FROM ".table('jobs').$wheresql." UNION ALL SELECT COUNT(*) AS num FROM ".table('jobs_tmp').$wheresql;
	}
	else
	{
	$total_sql="SELECT COUNT(*) AS num FROM ".table($tablename).$wheresql;
	}
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	if ($tablename=="all")
	{
	$getsql="SELECT * FROM ".table('jobs').$wheresql." UNION ALL SELECT * FROM ".table('jobs_tmp').$wheresql;
	}
	else
	{
	$getsql="SELECT * FROM ".table($tablename)." ".$wheresql.$oederbysql;
	}
	$total[0]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs').($subsite_sql?" where ".$subsite_sql:"")."");
	$total[1]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp').($subsite_sql?" where ".$subsite_sql:"")."");
	if ($jobtype==2)
	{
	$total[2]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE audit=1 ".($subsite_sql?" and ".$subsite_sql:"")."");
	$total[3]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE audit=2 ".($subsite_sql?" and ".$subsite_sql:"")."");
	$total[4]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE audit=3 ".($subsite_sql?" and ".$subsite_sql:"")."");
	}
	$jobs = get_jobs($offset,$perpage,$getsql);
	$smarty->assign('pageheader',"ְλ����");
	$smarty->assign('jobs',$jobs);
	$smarty->assign('now',time());
	$smarty->assign('total',$total);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('totaljob',$total_val);
	$smarty->assign('cat',get_promotion_cat(1));
	get_token();
	$smarty->display('company/admin_company_jobs.htm');
}
elseif($act == 'jobs_perform')
{
		check_token();
		$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:adminmsg("��û��ѡ��ְλ��",1);
		if (!empty($_REQUEST['delete']))
		{
			check_permissions($_SESSION['admin_purview'],"jobs_del");
			$num=del_jobs($yid);
			if ($num>0)
			{
			adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
			}
			else
			{
			adminmsg("ɾ��ʧ�ܣ�",0);
			}
		}
		elseif (!empty($_POST['set_audit']))
		{
			check_permissions($_SESSION['admin_purview'],"jobs_audit");
			$audit=intval($_POST['audit']);
			$pms_notice=intval($_POST['pms_notice']);
			$reason=trim($_POST['reason']);
			if ($n=edit_jobs_audit($yid,$audit,$reason,$pms_notice))
			{
			refresh_jobs($yid); 
			adminmsg("��˳ɹ�����Ӧ���� {$n}",2);			
			}
			else
			{
			adminmsg("���ʧ�ܣ���Ӧ���� 0",1);
			}
		}
		elseif (!empty($_GET['refresh']))
		{
			if($n=refresh_jobs($yid))
			{
			adminmsg("ˢ�³ɹ�����Ӧ���� {$n}",2);
			}
			else
			{
			adminmsg("ˢ��ʧ�ܣ�",0);
			}
		}
		elseif (!empty($_POST['set_delay']))
		{
			$days=intval($_POST['days']);
			if (empty($days))
			{
			adminmsg("����дҪ�ӳ���������",0);
			}
			$arr=delay_jobs($yid,$days);
			if(!empty($arr))
			{
				$job_arr = explode(',', $arr);
				if(intval($job_arr[1])==0)
				{
					$img_type = 0;
				}
				else
				{
					$img_type = 2;
				}
				distribution_jobs($yid);
				adminmsg("���ӳ�ְλ {$job_arr[0]} �����ɹ� {$job_arr[1]} ����ʧ�� {$job_arr[2]} ��",$img_type);
			}
			else
			{
			adminmsg("����ʧ�ܣ�",0);
			}
		}
		elseif (!empty($_REQUEST['export']))
		{
			check_permissions($_SESSION['admin_purview'],"jobs_export");
			if(!export_jobs($yid)){
				adminmsg("����ʧ�ܣ�",0);
			}
		}
}
elseif($act == 'edit_jobs')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"jobs_edit");
	$id =!empty($_REQUEST['id'])?intval($_REQUEST['id']):adminmsg("��û��ѡ��ְλ��",1);
	$smarty->assign('pageheader',"ְλ����");
	$jobs=get_jobs_one($id);
	//���������зָ�
	$telarray = explode('-',$jobs['contact']['landline_tel']);
	if(intval($telarray[0]) > 0)
	{
		$jobs['contact']['landline_tel_first'] = $telarray[0];
	}
	if(intval($telarray[1]) > 0)
	{
		$jobs['contact']['landline_tel_next'] = $telarray[1];
	}
	if(intval($telarray[2]) > 0)
	{
		$jobs['contact']['landline_tel_last'] = $telarray[2];
	}
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('jobs',$jobs);
	$smarty->assign('jobsaudit',get_jobsaudit_one($id));
	$smarty->display('company/admin_company_jobs_edit.htm');
}
elseif ($act=='editjobs_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"jobs_edit");
	$id=intval($_POST['id']);
	$company_id=intval($_POST['company_id']);
    $company_profile=get_company_one_id($company_id);
	$setsqlarr['jobs_name']=trim($_POST['jobs_name'])?trim($_POST['jobs_name']):adminmsg('��û����дְλ���ƣ�',1);
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);	
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=intval($_POST['category']);
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['wage']=intval($_POST['wage']);
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['display']=intval($_POST['display']);
	$setsqlarr['audit']=intval($_POST['audit']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['education']=intval($_POST['education']);
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience']);
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['contents']=trim($_POST['contents'])?trim($_POST['contents']):adminmsg('��û����дְλ������',1);	
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_profile['companyname'].$setsqlarr['category_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_profile['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$days=intval($_POST['days']);
	if ($days>0 && (intval($_POST['olddeadline'])-time())>0) $setsqlarr['deadline']=intval($_POST['olddeadline'])+($days*(60*60*24));
	if ($days>0 && (intval($_POST['olddeadline'])-time())<0) $setsqlarr['deadline']=strtotime("".$days." day");
	$setsqlarr_contact['contact']=trim($_POST['contact']);
	$setsqlarr_contact['qq']=trim($_POST['qq']);
	$setsqlarr_contact['telephone']=trim($_POST['telephone']);
	if(!preg_match("/1[3458]{1}\d{9}$/",$setsqlarr_contact['telephone'])){
		$setsqlarr_contact['notify_mobile'] = 0;
	}
	$setsqlarr_contact['address']=trim($_POST['address']);
	$setsqlarr_contact['email']=trim($_POST['email']);
	$setsqlarr_contact['notify']=trim($_POST['notify']);
		$setsqlarr_contact['contact_show']=intval($_POST['contact_show']);
	$setsqlarr_contact['email_show']=intval($_POST['email_show']);
	$setsqlarr_contact['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr_contact['address_show']=intval($_POST['address_show']);
	$setsqlarr_contact['qq_show']=intval($_POST['qq_show']);
	//����
	$landline_tel[]=trim($_POST['landline_tel_first'])?trim($_POST['landline_tel_first']):"0";
	$landline_tel[]=trim($_POST['landline_tel_next'])?trim($_POST['landline_tel_next']):"0";
	$landline_tel[]=trim($_POST['landline_tel_last'])?trim($_POST['landline_tel_last']):"0";
	$setsqlarr_contact['landline_tel']=implode('-', $landline_tel);
	
	$wheresql=" id='".$id."' ";
	$tb1=$db->getone("select * from ".table('jobs')." where id='{$id}' LIMIT 1");
	if (!empty($tb1))
	{
		if (!$db->updatetable(table('jobs'),$setsqlarr,$wheresql)) adminmsg("����ʧ�ܣ�",0);
	}
	else
	{
		if (!$db->updatetable(table('jobs_tmp'),$setsqlarr,$wheresql)) adminmsg("����ʧ�ܣ�",0);
	}
	$wheresql=" pid=".$id;
	if (!$db->updatetable(table('jobs_contact'),$setsqlarr_contact,$wheresql)) adminmsg("����ʧ�ܣ�",0);
	$searchtab['nature']=$setsqlarr['nature'];
	$searchtab['sex']=$setsqlarr['sex'];
	$searchtab['topclass']=$setsqlarr['topclass'];
	$searchtab['category']=$setsqlarr['category'];
	$searchtab['subclass']=$setsqlarr['subclass'];
	$searchtab['education']=$setsqlarr['education'];
	$searchtab['experience']=$setsqlarr['experience'];
	$searchtab['wage']=$setsqlarr['wage'];
	$searchtab['graduate']=$setsqlarr['graduate'];
	//
	$db->updatetable(table('jobs_search_wage'),$searchtab," id='{$id}'");
	$db->updatetable(table('jobs_search_rtime'),$searchtab," id='{$id}'");
	$db->updatetable(table('jobs_search_stickrtime'),$searchtab," id='{$id}'");
	$db->updatetable(table('jobs_search_hot'),$searchtab," id='{$id}'");
	$db->updatetable(table('jobs_search_scale'),$searchtab," id='{$id}'");
	$searchtab['key']=$setsqlarr['key'];
	$searchtab['likekey']=$setsqlarr['jobs_name'].','.$company_profile['companyname'];
	$db->updatetable(table('jobs_search_key'),$searchtab," id='{$id}' ");
	write_log("�޸�ְλidΪ".$id."��ְλ,", $_SESSION['admin_name'],3);
	unset($setsqlarr_contact,$setsqlarr);
	distribution_jobs($id);
	$link[0]['text'] = "����ְλ�б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg("�޸ĳɹ���",2,$link);
}
elseif($act == 'company_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"com_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY c.id DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE c.companyname like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE c.id ='".intval($key)."'";
		elseif ($key_type===3)$wheresql=" WHERE m.username like '{$key}%'";
		elseif ($key_type===4)$wheresql=" WHERE c.uid ='".intval($key)."'";
		elseif ($key_type===5)$wheresql=" WHERE c.address  like '%{$key}%'";
		elseif ($key_type===6)$wheresql=" WHERE c.telephone  like '{$key}%'";		
		$oederbysql="";
	}
	$_GET['audit']<>""? $wheresqlarr['c.audit']=intval($_GET['audit']):'';
	if (is_array($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
	if (!empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE addtime> ".$settr:$wheresql." AND addtime> ".$settr;
	}
	$operation_mode=$_CFG['operation_mode'];
	if($operation_mode=='1'){
		$joinsql=" LEFT JOIN ".table('members')." AS m ON c.uid=m.uid  LEFT JOIN ".table('members_points')." AS p ON c.uid=p.uid";
	}else{
		$joinsql=" LEFT JOIN ".table('members')." AS m ON c.uid=m.uid  LEFT JOIN ".table('members_setmeal')." AS p ON c.uid=p.uid";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('company_profile')." AS c".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$clist = get_company($offset,$perpage,$joinsql.$wheresql.$oederbysql,$operation_mode);
	$smarty->assign('pageheader',"��ҵ����");
	$smarty->assign('clist',$clist);
	$smarty->assign('certificate_dir',$certificate_dir);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_company_list.htm');
}

elseif($act == 'company_perform')
{
	check_token();
	$u_id =!empty($_POST['y_id'])?$_POST['y_id']:adminmsg("��û��ѡ����ҵ��",1);
	if ($_POST['delete'])
	{
		check_permissions($_SESSION['admin_purview'],"com_del");
		if ($_POST['delete_company']=='yes')
		{
		!del_company($u_id)?adminmsg("ɾ����ҵ����ʧ�ܣ�",0):"";
		}
		if ($_POST['delete_jobs']=='yes')
		{
		!del_company_alljobs($u_id)?adminmsg("ɾ��ְλʧ�ܣ�",0):"";
		}
		if ($_POST['delete_jobs']<>'yes' && $_POST['delete_company']<>'yes')
		{
		adminmsg("δѡ��ɾ�����ͣ�",1);
		}
		adminmsg("ɾ���ɹ���",2);
	}
	if (trim($_POST['set_audit']))
	{
		check_permissions($_SESSION['admin_purview'],"com_audit");
		$audit=$_POST['audit'];
		$pms_notice=intval($_POST['pms_notice']);
		$reason=trim($_POST['reason']);
		!edit_company_audit($u_id,intval($audit),$reason,$pms_notice)?adminmsg("����ʧ�ܣ�",0):adminmsg("���óɹ���",2);
	}
	elseif (!empty($_POST['set_refresh']))
	{
		if (empty($_POST['refresh_jobs']))
		{
		$refresjobs=false;
		}
		else
		{
		$refresjobs=true;
		}
		if($n=refresh_company($u_id,$refresjobs))
		{
		adminmsg("ˢ�³ɹ�����Ӧ���� {$n} ��",2);
		}
		else
		{
		adminmsg("ˢ��ʧ�ܣ�",0);
		}
	}
	elseif (!empty($_REQUEST['export']))
	{
		check_permissions($_SESSION['admin_purview'],"company_export");
		if(!export_company($u_id)){
			adminmsg("����ʧ�ܣ�",0);
		}
	}
}
elseif($act == 'edit_company_profile')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"com_edit");
	$yid =!empty($_REQUEST['id'])?intval($_REQUEST['id']):adminmsg("��û��ѡ����ҵ��",1);
	$smarty->assign('pageheader',"��ҵ����");
	$company_profile=get_company_one_id($yid);
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('comaudit',get_comaudit_one($yid));
	//���������зָ�
	$telarray = explode('-',$company_profile['landline_tel']);
	if(intval($telarray[0]) > 0)
	{
		$company_profile['landline_tel_first'] = $telarray[0];
	}
	if(intval($telarray[1]) > 0)
	{
		$company_profile['landline_tel_next'] = $telarray[1];
	}
	if(intval($telarray[2]) > 0)
	{
		$company_profile['landline_tel_last'] = $telarray[2];
	}
	$smarty->assign('company_profile',$company_profile);
	$smarty->assign('certificate_dir',$certificate_dir);//Ӫҵִ��·��
	$smarty->display('company/admin_company_profile_edit.htm');
}
elseif ($act=='company_profile_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_edit");
	$setsqlarr=array();
	$contents=array();
	$id=intval($_POST['id']);
	$setsqlarr['audit']=intval($_POST['audit']);
	$setsqlarr['companyname']=trim($_POST['companyname'])?trim($_POST['companyname']):adminmsg('��û��������ҵ���ƣ�',1);
	$setsqlarr['nature']=trim($_POST['nature'])?trim($_POST['nature']):adminmsg('��ѡ����ҵ���ʣ�',1);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn'])?trim($_POST['nature_cn']):adminmsg('��ѡ����ҵ���ʣ�',1);
	$setsqlarr['trade']=trim($_POST['trade'])?trim($_POST['trade']):adminmsg('��ѡ��������ҵ��',1);
	$setsqlarr['trade_cn']=trim($_POST['trade_cn'])?trim($_POST['trade_cn']):adminmsg('��ѡ��������ҵ��',1);
	$setsqlarr['district_cn']=trim($_POST['district_cn'])?trim($_POST['district_cn']):adminmsg('��ѡ������������',1);
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['street']=intval($_POST['street']);
	$setsqlarr['street_cn']=trim($_POST['street_cn']);
	$setsqlarr['scale']=trim($_POST['scale'])?trim($_POST['scale']):adminmsg('��ѡ��˾��ģ��',1);
	$setsqlarr['scale_cn']=trim($_POST['scale_cn'])?trim($_POST['scale_cn']):adminmsg('��ѡ��˾��ģ��',1);	
	$setsqlarr['registered']=trim($_POST['registered']);
	$setsqlarr['currency']=trim($_POST['currency']);
	$setsqlarr['address']=trim($_POST['address']);
	$setsqlarr['contact']=trim($_POST['contact']);
	$setsqlarr['telephone']=trim($_POST['telephone']);
	$setsqlarr['email']=trim($_POST['email']);
	$setsqlarr['website']=trim($_POST['website']);
	$setsqlarr['contents']=trim($_POST['contents'])?trim($_POST['contents']):adminmsg('����д��˾��飡',1);
		$setsqlarr['contact_show']=intval($_POST['contact_show']);
	$setsqlarr['email_show']=intval($_POST['email_show']);
	$setsqlarr['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr['address_show']=intval($_POST['address_show']);
	//����
	$landline_tel[]=trim($_POST['landline_tel_first'])?trim($_POST['landline_tel_first']):"0";
	$landline_tel[]=trim($_POST['landline_tel_next'])?trim($_POST['landline_tel_next']):"0";
	$landline_tel[]=trim($_POST['landline_tel_last'])?trim($_POST['landline_tel_last']):"0";
	$setsqlarr['landline_tel']=implode('-', $landline_tel);
		
	$wheresql=" id='{$id}' ";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
		if ($db->updatetable(table('company_profile'),$setsqlarr,$wheresql))
		{
				$jobarr['companyname']=$setsqlarr['companyname'];
				$jobarr['trade']=$setsqlarr['trade'];
				$jobarr['trade_cn']=$setsqlarr['trade_cn'];
				$jobarr['scale']=$setsqlarr['scale'];
				$jobarr['scale_cn']=$setsqlarr['scale_cn'];
				$jobarr['street']=$setsqlarr['street'];
				$jobarr['street_cn']=$setsqlarr['street_cn'];
				if (!$db->updatetable(table('jobs'),$jobarr," uid=".intval($_POST['cuid'])."")) adminmsg('�޸�ְλ���ֳ���',0);
				if (!$db->updatetable(table('jobs_tmp'),$jobarr," uid=".intval($_POST['cuid'])."")) adminmsg('�޸�ְλ���ֳ���',0);
				if (!$db->updatetable(table('jobfair_exhibitors'),array('companyname'=>$jobarr['companyname'])," uid=".intval($_POST['cuid'])."")) adminmsg('�޸Ĺ�˾���Ƴ���',0);
				$soarray['trade']=$jobarr['trade'];
				$soarray['scale']=$jobarr['scale'];
				$soarray['street']=$setsqlarr['street'];
				$db->updatetable(table('jobs_search_scale'),$soarray," uid=".intval($_POST['cuid'])."");
				$db->updatetable(table('jobs_search_wage'),$soarray," uid=".intval($_POST['cuid'])."");
				$db->updatetable(table('jobs_search_rtime'),$soarray," uid=".intval($_POST['cuid'])."");
				$db->updatetable(table('jobs_search_stickrtime'),$soarray," uid=".intval($_POST['cuid'])."");
				$db->updatetable(table('jobs_search_hot'),$soarray," uid=".intval($_POST['cuid'])."");
				$db->updatetable(table('jobs_search_key'),$soarray," uid=".intval($_POST['cuid'])."");
				//ͬ����ְλ��ϵ��ʽ
				if(intval($_POST['telephone_to_jobs'])==1)
				{
					$jobsid_arr=$db->getall("select id from ".table("jobs")." where uid=".intval($_POST['cuid']));
					if(!empty($jobsid_arr))
					{
						foreach ($jobsid_arr as $key => $value) 
						{
							$jobsid_arr_[]=$value['id'];
						}
						$jobsid_str=implode(',', $jobsid_arr_);
						$db->query("update ".table('jobs_contact')." set telephone='$setsqlarr[telephone]',email='$setsqlarr[email]',contact='$setsqlarr[contact]',landline_tel='$setsqlarr[landline_tel]' where pid in ($jobsid_str)");
					}
				}
				//�޸ĸ߼�ְλ����ҵ��Ϣ
				$hunterjobarr['companyname']=$setsqlarr['companyname'];
				$hunterjobarr['companyname_note']=$setsqlarr['companyname'];
				$hunterjobarr['trade']=$setsqlarr['trade'];
				$hunterjobarr['trade_cn']=$setsqlarr['trade_cn'];
				$hunterjobarr['scale']=$setsqlarr['scale'];
				$hunterjobarr['scale_cn']=$setsqlarr['scale_cn'];
				$hunterjobarr['nature']=$setsqlarr['nature'];
				$hunterjobarr['nature_cn']=$setsqlarr['nature_cn'];
				$hunterjobarr['company_desc']=$setsqlarr['contents'];
				$db->updatetable(table('hunter_jobs'),$hunterjobarr," uid=".intval($_POST['cuid'])."");
				write_log("�޸���ҵuidΪ".intval($_POST['cuid'])."����ҵ����", $_SESSION['admin_name'],3);
				unset($setsqlarr,$hunterjobarr);
				adminmsg("����ɹ���",2,$link);
		}
		else
		{
		unset($setsqlarr);
		adminmsg("����ʧ�ܣ�",0);
		}
}
elseif($act == 'order_list')
{	
	get_token();
	check_permissions($_SESSION['admin_purview'],"ord_show");
		require_once(QISHI_ROOT_PATH.'include/page.class.php');
		require_once(ADMIN_ROOT_PATH.'include/admin_pay_fun.php');
	$wheresql=" WHERE o.utype=1 ";
	$oederbysql=" order BY o.addtime DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql=" WHERE o.utype=1 AND c.companyname like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE o.utype=1 AND m.username = '{$key}'";
		elseif ($key_type===3)$wheresql=" WHERE o.utype=1 AND o.oid ='".trim($key)."'";
		$oederbysql="";
	}
	else
	{	
		$wheresqlarr['o.utype']='1';
		!empty($_GET['is_paid'])? $wheresqlarr['o.is_paid']=intval($_GET['is_paid']):'';
		!empty($_GET['typename'])?$wheresqlarr['o.payment_name']=trim($_GET['typename']):'';
		if (is_array($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
		
		if (!empty($_GET['settr']))
		{
			$settr=strtotime("-".intval($_GET['settr'])." day");
			$wheresql.=empty($wheresql)?" WHERE ": " AND ";
			$wheresql.="o.addtime> ".$settr;
		}
	}
	$joinsql=" left JOIN ".table('members')." as m ON o.uid=m.uid LEFT JOIN  ".table('company_profile')." as c ON o.uid=c.uid ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('order')." as o ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$orderlist = get_order_list($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��������");
	$smarty->assign('payment_list',get_payment(2));
	$smarty->assign('orderlist',$orderlist);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_order_list.htm');
}
elseif($act == 'show_order')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"ord_show");
	$smarty->assign('pageheader',"��������");
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('payment',get_order_one($_GET['id']));
	$smarty->display('company/admin_order_show.htm');
}
elseif($act == 'order_notes_save')
{
	check_token();
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	!$db->query("UPDATE ".table('order')." SET  notes='".$_POST['notes']."' WHERE id='".intval($_GET['id'])."'")?adminmsg('����ʧ��',1):adminmsg("�����ɹ���",2,$link);
}
//���ó�ֵ��¼���տͨ��
elseif($act == 'order_set')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"ord_set");
	$smarty->assign('pageheader',"��������");
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('payment',get_order_one($_GET['id']));
	$smarty->display('company/admin_order_set.htm');
}
elseif($act == 'order_set_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"ord_set");
		if (order_paid(trim($_POST['oid'])))
		{
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $_POST['url'];
		!$db->query("UPDATE ".table('order')." SET notes='".$_POST['notes']."' WHERE id=".intval($_GET['id'])."  LIMIT 1 ")?adminmsg('����ʧ��',1):adminmsg("�����ɹ���",2,$link);
		}
		else
		{
		adminmsg('����ʧ��',1);
		}
}
//ȡ����Ա��ֵ����
elseif($act == 'order_del')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"ord_del");
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ����Ŀ��",1);
	if (del_order($id))
	{
	adminmsg("ȡ���ɹ���",2,$link);
	}
	else
	{
	adminmsg("ȡ��ʧ�ܣ�",1);
	}
}
elseif($act == 'meal_members')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE a.effective=1 ";
	$oederbysql=" order BY a.uid DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql.=" AND b.username = '{$key}'";
		elseif ($key_type===2)$wheresql.=" AND b.uid = '".intval($key)."' ";
		elseif ($key_type===3)$wheresql.=" AND b.email = '{$key}'";
		elseif ($key_type===4)$wheresql.=" AND b.mobile like '{$key}%'";
		elseif ($key_type===5)$wheresql.=" AND c.companyname like '{$key}%'";
		$oederbysql="";
	}
	else
	{	
		if (!empty($_GET['setmeal_id']))
		{
			$setmeal_id=intval($_GET['setmeal_id']);
			$wheresql.=" AND a.setmeal_id=".$setmeal_id;
		}
		if (!empty($_GET['settr']))
		{
			$settr=intval($_GET['settr']);
			if ($settr==-1)
			{
			$wheresql.=" AND a.endtime<".time()." AND a.endtime>0 ";
			}
			else
			{
			$settr=strtotime("{$settr} day");
			$wheresql.="  AND a.endtime>".time()." AND a.endtime< {$settr}";
			}			
		}
	}
	$joinsql=" LEFT JOIN ".table('members')." as b ON a.uid=b.uid  LEFT JOIN ".table('company_profile')." as c ON a.uid=c.uid ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_setmeal')." as a ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$member = get_meal_members_list($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��ҵ����");
	$smarty->assign('navlabel','meal_members');
	$smarty->assign('member',$member);
	$smarty->assign('setmeal',get_setmeal());	
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_company_meal_members.htm');
}
elseif($act == 'meal_log')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY a.log_id DESC ";
	$key_uid=isset($_GET['key_uid'])?trim($_GET['key_uid']):"";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	$operation_mode=trim($_CFG['operation_mode']);
	//��«�ҡ��ײͺͻ������ģʽ�����¼�����ģʽ�º�«�Һ��ײͱ���ļ�¼����ʾ
	if($operation_mode=='1')
	{
		$wheresql=" WHERE a.log_mode=1 AND a.log_utype=1";
	}
	elseif($operation_mode=='2')
	{
		$wheresql=" WHERE a.log_mode=2 AND a.log_utype=1";
	}
	else
	{
		$wheresql=" WHERE (a.log_mode=1 OR a.log_mode=2) AND a.log_utype=1";
	}
	//������Ա(uid)�鿴�����¼
	if ($key_uid)
	{
		$wheresql.="  AND a.log_uid = '".intval($key_uid)."' ";
		//������ʶ�������ѯ������Ա�Ļ� ��ô���½ǵ���������û����
		$smarty->assign('sign','1');
	}
	//����������� : ����ĳ����Ա�ı����¼
	elseif ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql.="  AND a.log_username = '{$key}'";
		elseif ($key_type===2)$wheresql.="  AND a.log_uid = '".intval($key)."' ";
		elseif ($key_type===3)$wheresql.=" AND c.companyname like '{$key}%'";
		$oederbysql=" order BY a.log_id DESC ";
	}
	//��������ɸѡ��1->ϵͳ���͡�2->��Ա����3->����Ա�޸ġ�4->����Ա��ͨ����ɸѡ
	if (!empty($_GET['log_type']))
	{
		$log_type=intval($_GET['log_type']);
		$wheresql.=" AND  a.log_type=".$log_type;
	}
	if (!empty($_GET['settr']))
	{
		$settr=intval($_GET['settr']);
		$settr=strtotime("-{$settr} day");
		$wheresql.=" AND a.log_addtime> ".$settr;
	}
	if (!empty($_GET['is_money']))
	{
		$is_money=intval($_GET['is_money']);
		$wheresql.= " AND a.log_ismoney={$is_money}";
	}
	//����ģʽ ��������sql
	if($operation_mode=='1')
	{
		$joinsql=" LEFT JOIN ".table('members_points')." as b ON a.log_uid=b.uid  LEFT JOIN ".table('company_profile')." as c ON a.log_uid=c.uid ";
	}
	elseif($operation_mode=='2')
	{
		$joinsql=" LEFT JOIN ".table('members_setmeal')." as b ON a.log_uid=b.uid  LEFT JOIN ".table('company_profile')." as c ON a.log_uid=c.uid ";
	}
	else
	{
		$joinsql=" LEFT JOIN ".table('members_points')." as pb ON a.log_uid=pb.uid ";
		$joinsql.=" LEFT JOIN ".table('members_setmeal')." as sb ON a.log_uid=sb.uid  LEFT JOIN ".table('company_profile')." as c ON a.log_uid=c.uid ";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_charge_log')." as a ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$meallog = get_meal_members_log($offset,$perpage,$joinsql.$wheresql.$oederbysql,$operation_mode);
	$smarty->assign('pageheader','��ҵ����');
	$smarty->assign('navlabel','meal_log');
	$smarty->assign('meallog',$meallog);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_company_meal_log.htm');
}
elseif($act == 'meal_log_pie')
{
	require_once(ADMIN_ROOT_PATH.'include/admin_flash_statement_fun.php');
	$pie_type=!empty($_GET['pie_type'])?intval($_GET['pie_type']):1;
	meal_log_pie($pie_type,1);	
	$smarty->assign('pageheader',"��ҵ����");
	$smarty->assign('navlabel','meal_log_pie');
	$smarty->display('company/admin_company_meal_log_pie.htm');
}
elseif($act == 'meallog_del')
{
	check_permissions($_SESSION['admin_purview'],"meallog_del");
	check_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ���¼��",1);
	$num=del_meal_log($id);
	if ($num>0){adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);}else{adminmsg("ɾ��ʧ�ܣ�",0);}
}


elseif($act == 'meal_delay')
{
			$tuid =!empty($_REQUEST['tuid'])?$_REQUEST['tuid']:adminmsg("��û��ѡ���Ա��",1);
			$days=intval($_POST['days']);
			if (empty($days))
			{
			adminmsg("����дҪ�ӳ���������",0);
			}
			if($n=delay_meal($tuid,$days))
			{
			distribution_jobs_uid($tuid);
			adminmsg("�ӳ���Ч�ڳɹ�����Ӧ���� {$n}",2);
			}
			else
			{
			adminmsg("����ʧ�ܣ�",0);
			}
}
elseif($act == 'members_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"com_user_show");
		require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE m.utype=1 ";
	$oederbysql=" order BY m.uid DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql.=" AND m.username = '{$key}'";
		elseif ($key_type===2)$wheresql.=" AND m.uid = '".intval($key)."' ";
		elseif ($key_type===3)$wheresql.=" AND m.email = '{$key}'";
		elseif ($key_type===4)$wheresql.=" AND m.mobile like '{$key}%'";
		elseif ($key_type===5)$wheresql.=" AND c.companyname like '%{$key}%'";
		$oederbysql="";
	}
	else
	{	
		//ע��ʱ��
		if (!empty($_GET['settr']))
		{
			$settr=strtotime("-".intval($_GET['settr'])." day");
			$wheresql.=" AND m.reg_time> ".$settr;
		}
		//��֤����
		if (!empty($_GET['verification']))
		{
			if ($_GET['verification']=="1")
			{
			$wheresql.=" AND m.email_audit = 1";
			}
			elseif ($_GET['verification']=="2")
			{
			$wheresql.=" AND m.email_audit = 0";
			}
			elseif ($_GET['verification']=="3")
			{
			$wheresql.=" AND m.mobile_audit = 1";
			}
			elseif ($_GET['verification']=="4")
			{
			$wheresql.=" AND m.mobile_audit = 0";
			}
		}
		//���޹���
		if ($_GET['consultant']!="")
		{
			//δ����
			$consultant=intval($_GET['consultant']);
			if ($consultant=="0")
			{
			$wheresql.=" AND  m.consultant=0";
			}
			//�ѷ���
			elseif ($consultant=="1")
			{
			$wheresql.=" AND m.consultant != 0";
			}
		}
	}
	$joinsql=" LEFT JOIN ".table('company_profile')." as c ON m.uid=c.uid ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members')." as m ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$member = get_member_list($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��ҵ��Ա");
	$smarty->assign('member',$member);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_company_user_list.htm');
}elseif($act=="check_save"){
    get_token();
    check_permissions($_SESSION['admin_purview'], "com_user_show");



    $cid = !empty($_REQUEST['id']) ? $_REQUEST['id'] : adminmsg("��û��ѡ���Ա��", 1);
    $status=!empty($_REQUEST['status'])?intval($_REQUEST["status"]):0;
    $reason=($_REQUEST['reason']);
    $sql=vsprintf("update   %s set status=%d,reason='%s' where id=%d ",array(table("resume_check_apply"),$status,$reason,$cid));
	$data=array();
	$data["cid"]=$cid;
	$data["addtime"]=time();
	$data["name"]=$_SESSION["admin_name"];
	$data["reason"]=$reason;
	$data["status"]=$status;

	$db->inserttable(table("resume_check_apply_log"),$data);
    $db->query($sql);
    adminmsg("�����ɹ���", 1);

} elseif ($act == 'members_check_list') {
    //����б����
    get_token();
    check_permissions($_SESSION['admin_purview'], "members_check_list");
    require_once(QISHI_ROOT_PATH . 'include/page.class.php');
    $wheresql = " WHERE m.utype=1 ";
    $oederbysql = " order BY m.uid DESC ";


    //���޹���
    if ($_GET['status'] != "") {
        //δ����
        $consultant = intval($_GET['status']);
        if ($consultant == "0") {
            $wheresql .= " AND  c.status=0";
        } //�ѷ���
        elseif ($consultant == "1") {
            $wheresql .= " AND c.status = 1";
        }elseif ($consultant == "2") {
            $wheresql .= " AND c.status = 2";
        }
		$smarty->assign('status',$_GET['status']);
    }else{
		$smarty->assign('status', 0);

		$wheresql .= " AND  c.status=0";
    }

    $joinsql = " LEFT JOIN " . table('members') . " as m ON m.uid=c.uid LEFT JOIN " . table('company_profile') . " as p ON p.uid=c.uid";
    $total_sql = "SELECT COUNT(*) AS num FROM " . table('resume_check_apply') . " as c " . $joinsql . $wheresql;
    $total_val = $db->get_total($total_sql);
    $page = new page(array('total' => $total_val, 'perpage' => $perpage, 'getarray' => $_GET));
    $currenpage = $page->nowindex;
    $offset = ($currenpage - 1) * $perpage;
    $member = get_member_check_list($offset, $perpage, $joinsql . $wheresql . $oederbysql);
    $smarty->assign('pageheader', "��ҵ��Ա");
    $smarty->assign('member', $member);

    $smarty->assign('page', $page->show(3));
    $smarty->display('company/admin_company_user_check_list.htm');
} elseif ($act == 'applay_check_log') {
    //����б����
    get_token();
    check_permissions($_SESSION['admin_purview'], "members_check_list");
    require_once(QISHI_ROOT_PATH . 'include/page.class.php');




	$sql = "SELECT *  FROM " . table('resume_check_apply_log') . " where cid=".$_GET["cid"]." order by id desc";
   	global $db;
	$result=$db->query($sql);
	$row_arr=array();
	while ($row = $db->fetch_array($result)) {

		$row_arr[] = $row;
	}

	$smarty->assign('pageheader', "��ҵ��Ա");
    $smarty->assign('loglist', $row_arr);

     $smarty->display('company/admin_company_user_check_log.htm');
} elseif ($act == 'delete_user')
{	
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_del");
	$tuid =!empty($_REQUEST['tuid'])?$_REQUEST['tuid']:adminmsg("��û��ѡ���Ա��",1);
	if ($_POST['delete'])
	{
		if (!empty($_POST['delete_user']))
		{
		!delete_company_user($tuid)?adminmsg("ɾ����Աʧ�ܣ�",0):"";
		}
		if (!empty($_POST['delete_company']))
		{
		!del_company($tuid)?adminmsg("ɾ����ҵ����ʧ�ܣ�",0):"";
		}
		if (!empty($_POST['delete_jobs']))
		{
		!del_company_alljobs($tuid)?adminmsg("ɾ��ְλʧ�ܣ�",0):"";
		}
	adminmsg("ɾ���ɹ���",2);
	}
}
//��ӻ�Ա
elseif($act == 'members_add')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"com_user_add");
	$smarty->assign('pageheader',"��ҵ��Ա");
	$smarty->assign('givesetmeal',get_setmeal(false));
	$smarty->assign('points',get_cache('points_rule'));
	$smarty->display('company/admin_company_user_add.htm');
}
elseif($act == 'members_add_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_add");
	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');
	if (strlen(trim($_POST['username']))<3) adminmsg('�û�������Ϊ3λ���ϣ�',1);
	if (strlen(trim($_POST['password']))<6) adminmsg('�������Ϊ6λ���ϣ�',1);
	$sql['username'] = !empty($_POST['username']) ? trim($_POST['username']):adminmsg('����д�û�����',1);
	$sql['password'] = !empty($_POST['password']) ? trim($_POST['password']):adminmsg('����д���룡',1);	
	if ($sql['password']<>trim($_POST['password1']))
	{
	adminmsg('������������벻��ͬ��',1);
	}
	$sql['utype'] = !empty($_POST['member_type']) ? intval($_POST['member_type']):adminmsg('��û��ѡ��ע�����ͣ�',1);
	if (empty($_POST['email']) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$_POST['email']))
	{
	adminmsg('���������ʽ����',1);
	}
	$sql['email']= trim($_POST['email']);
	if (get_user_inusername($sql['username']))
	{
	adminmsg('���û����Ѿ���ʹ�ã�',1);
	}
	if (get_user_inemail($sql['email']))
	{
	adminmsg('�� Email �Ѿ���ע�ᣡ',1);
	}
	if(defined('UC_API'))
	{
		include_once(QISHI_ROOT_PATH.'uc_client/client.php');
		if (uc_user_checkname($sql['username'])<>"1")
		{
		adminmsg('���û����Ѿ���ʹ�û����û����Ƿ���',1);
		exit();
		}
		elseif (uc_user_checkemail($sql['email'])<>"1")
		{
			adminmsg('�� Email�Ѿ���ʹ�û��߷Ƿ���',1);
			exit();
		}
		else
		{
			uc_user_register($sql['username'],$sql['password'],$sql['email']);
		}
	}
	$sql['pwd_hash'] = randstr();
	$sql['password'] = md5(md5($sql['password']).$sql['pwd_hash'].$QS_pwdhash);
	$sql['reg_time']=time();
	$sql['reg_ip']=$online_ip;
	$insert_id=$db->inserttable(table('members'),$sql,true);
			if($sql['utype']=="1")
			{
			$db->query("INSERT INTO ".table('members_points')." (uid) VALUES ('{$insert_id}')");
			$db->query("INSERT INTO ".table('members_setmeal')." (uid) VALUES ('{$insert_id}')");
				if(intval($_POST['is_money']) && $_POST['log_amount']){
					$amount=round($_POST['log_amount'],2);
					$ismoney=2;
				}else{
					$amount='0.00';
					$ismoney=1;
				}
				$regpoints_num=intval($_POST['regpoints_num']);
				if ($_POST['regpoints']=="y")
				{
				write_memberslog($insert_id,1,9001,$sql['username'],"<span style=color:#FF6600>ע���Աϵͳ�Զ�����!(+{$regpoints_num})</span>",1,1010,"ע���Աϵͳ�Զ�����","+{$regpoints_num}","{$regpoints_num}");
						//��Ա��«�ұ����¼������Ա��̨�޸Ļ�Ա�ĺ�«�ҡ�3��ʾ������Ա��̨�޸�
				$notes="�����ˣ�{$_SESSION['admin_name']},˵������̨�����ҵ��Ա������(+{$regpoints_num})��«�ң���ȡ���ã�{$amount}Ԫ";
				write_setmeallog($insert_id,$sql['username'],$notes,4,$amount,$ismoney,1,1);
					
				report_deal($insert_id,1,$regpoints_num);
				}
				$reg_service=intval($_POST['reg_service']);
				if ($reg_service>0)
				{
				$service=get_setmeal_one($reg_service);
				write_memberslog($insert_id,1,9002,$sql['username'],"��ͨ����({$service['setmeal_name']})",2,1011,"��ͨ����","","");
				set_members_setmeal($insert_id,$reg_service);
						//��Ա��«�ұ����¼������Ա��̨�޸Ļ�Ա�ĺ�«�ҡ�3��ʾ������Ա��̨�޸�
				$notes="�����ˣ�{$_SESSION['admin_name']},˵������̨�����ҵ��Ա����ͨ����({$service['setmeal_name']})����ȡ���ã�{$amount}Ԫ";
				write_setmeallog($insert_id,$sql['username'],$notes,4,$amount,$ismoney,2,1);
					
				}
				if(intval($_POST['is_money']) && $_POST['log_amount'] && !$notes){
				$notes="�����ˣ�{$_SESSION['admin_name']},˵������̨�����ҵ��Ա��δ���ͺ�«�ң�δ��ͨ�ײͣ���ȡ���ã�{$amount}Ԫ";
				write_setmeallog($insert_id,$sql['username'],$notes,4,$amount,2,2,1);
				}			
			}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = "?act=members_list";
	$link[1]['text'] = "�������";
	$link[1]['href'] = "?act=members_add";
	write_log("��ӻ�Ա".$sql['username'], $_SESSION['admin_name'],3);
	adminmsg('��ӳɹ���',2,$link);
}
//���ù���
elseif($act == 'consultant_install')
{	
	//�õ�Ҫ���ù��ʵ���ҵ��Աuid 
	$tuid =!empty($_REQUEST['tuid'])?$_REQUEST['tuid']:adminmsg("��û��ѡ���Ա��",1);
	if(is_array($tuid)){
		$tuid=implode(",",$tuid);
	}
	//�õ�������Ϣ
	$consultants = $db->getall("select * from ".table('consultant'));
	//��ҳ
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('consultant').$oederbysql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$clist = get_consultant($offset,$perpage,$oederbysql);

	$smarty->assign('tuid',$tuid);
	$smarty->assign('pageheader',"���ù���");
	$smarty->assign('page',$page->show(3));
	$smarty->assign('consultants',$consultants);
	$smarty->display('company/admin_consultant_install.htm');
}
//����  ���ù���
elseif($act == 'consultant_install_save')
{
	//�õ� ���ʵ�id 
	$id = !empty($_GET['id'])?intval($_GET['id']):adminmsg("ѡ����ʷ�������",0);
	//�õ�Ҫ���ù��ʵ���ҵ��Աuid 
	$tuid =!empty($_REQUEST['tuid'])?$_REQUEST['tuid']:adminmsg("��û��ѡ���Ա��",1);
	$tuid=explode(",", $tuid);
	foreach ($tuid as $uid) {
		$db->updatetable(table('members'),array('consultant' => $id )," uid='{$uid}'");
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = "?act=members_list";
	write_log("Ϊ��ҵuidΪ".$tuid."����ҵ���ù���,����idΪ".$id, $_SESSION['admin_name'],3);
	adminmsg('���óɹ���',2,$link);
}
elseif($act == 'user_edit')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"com_user_edit");
	$company_user=get_user($_GET['tuid']);
	$smarty->assign('pageheader',"��ҵ��Ա");
	$company_profile=get_company_one_uid($company_user['uid']);
	$company_user['tpl']=$company_profile['tpl'];
	$smarty->assign('company_user',$company_user);
	$smarty->assign('userpoints',get_user_points($company_user['uid']));
	$smarty->assign('setmeal',get_user_setmeal($company_user['uid']));
	$smarty->assign('givesetmeal',get_setmeal(false));
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->display('company/admin_company_user_edit.htm');
}
elseif($act == 'set_account_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_edit");
	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');
	$setsqlarr['username']=trim($_POST['username']);
	$setsqlarr['email']=trim($_POST['email']);
	$setsqlarr['email_audit']=intval($_POST['email_audit']);
	$setsqlarr['mobile']=trim($_POST['mobile']);
	$setsqlarr['mobile_audit']=intval($_POST['mobile_audit']);
	if ($_POST['qq_openid']=="1")
	{
	$setsqlarr['qq_openid']='';
	}
	$thisuid=intval($_POST['company_uid']);	
	if (strlen($setsqlarr['username'])<3) adminmsg('�û�������Ϊ3λ���ϣ�',1);
	$getusername=get_user_inusername($setsqlarr['username']);
	if (!empty($getusername)  && $getusername['uid']<>$thisuid)
	{
	adminmsg("�û��� {$setsqlarr['username']}  �Ѿ����ڣ�",1);
	}
	if(!empty($setsqlarr['email']))
	{
		$getemail=get_user_inemail($setsqlarr['email']);
		if (!empty($getemail)  && $getemail['uid']<>$thisuid)
		{
		adminmsg("Email  {$setsqlarr['email']}  �Ѿ����ڣ�",1);
		}
	}
	if (!empty($setsqlarr['mobile']) && !preg_match("/^(13|15|14|17|18)\d{9}$/",$setsqlarr['mobile']))
	{
	adminmsg('�ֻ��������',1);
	}
	$getmobile=get_user_inmobile($setsqlarr['mobile']);
	if (!empty($setsqlarr['mobile']) && !empty($getmobile)  && $getmobile['uid']<>$thisuid)
	{
	adminmsg("�ֻ��� {$setsqlarr['mobile']}  �Ѿ����ڣ�",1);
	}
	if ($_POST['tpl'])
	{
		$tplarr['tpl']=trim($_POST['tpl']);
		$db->updatetable(table('company_profile'),$tplarr," uid='{$thisuid}'");
		$db->updatetable(table('jobs'),$tplarr," uid='{$thisuid}'");
		$db->updatetable(table('jobs_tmp'),$tplarr," uid='{$thisuid}'");
		unset($tplarr);
	}
	if ($db->updatetable(table('members'),$setsqlarr," uid=".$thisuid.""))
	{
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	write_log("�޸Ļ�ԱuidΪ".$thisuid."�Ļ�����Ϣ", $_SESSION['admin_name'],3);
	adminmsg('�޸ĳɹ���',2,$link);
	}
	else
	{
	adminmsg('�޸�ʧ�ܣ�',1);
	}
}
elseif($act == 'userpoints_edit')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_edit");
	if (intval($_POST['points'])<1) adminmsg('�������«�ң�',1);
	if (trim($_POST['points_notes'])=='') adminmsg('����д��«�Ҳ���˵����',1);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	$user=get_user($_POST['company_uid']);
	$points_type=intval($_POST['points_type']);	
	$t=$points_type==1?"+":"-";
	report_deal($user['uid'],$points_type,intval($_POST['points']));
	$points=get_user_points($user['uid']);
	write_memberslog(intval($_POST['company_uid']),1,9001,$user['username']," ����Ա������«��({$t}{$_POST['points']})��(ʣ��:{$points})����ע��".$_POST['points_notes'],1,1012,"����Ա������«��","{$t}{$_POST['points']}","{$points}");
		//��Ա��«�ұ����¼������Ա��̨�޸Ļ�Ա�ĺ�«�ҡ�3��ʾ������Ա��̨�޸�
		$user=get_user($_POST['company_uid']);
		if(intval($_POST['is_money']) && $_POST['log_amount']){
			$amount=round($_POST['log_amount'],2);
			$ismoney=2;
		}else{
			$amount='0.00';
			$ismoney=1;
		}
		$notes="�����ˣ�{$_SESSION['admin_name']},˵�����޸Ļ�Ա {$user['username']} ��«�� ({$t}{$_POST['points']})����ȡ��«�ҽ�{$amount} Ԫ����ע��{$_POST['points_notes']}";
		write_setmeallog($_POST['company_uid'],$user['username'],$notes,3,$amount,$ismoney,1,1);
	write_log("�޸Ļ�ԱuidΪ".$user['uid']."��«��", $_SESSION['admin_name'],3);
	adminmsg('����ɹ���',2);
}
elseif($act == 'set_setmeal_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_edit");
    if (intval($_POST['reg_service'])>0)
	{
		if (set_members_setmeal($_POST['company_uid'],$_POST['reg_service']))
		{
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $_POST['url'];
		//��Ա�ײͱ����¼������Ա��̨�޸Ļ�Ա�ײͣ����¿�ͨ�ײ͡�3��ʾ������Ա��̨�޸�
		$user=get_user($_POST['company_uid']);
		if(intval($_POST['is_money']) && $_POST['log_amount']){
			$amount=round($_POST['log_amount'],2);
			$ismoney=2;
		}else{
			$amount='0.00';
			$ismoney=1;
		}
		$notes="�����ˣ�{$_SESSION['admin_name']},˵����Ϊ��Ա {$user['username']} ���¿�ͨ������ȡ�����{$amount}Ԫ������ID��{$_POST['reg_service']}��";
		write_setmeallog($_POST['company_uid'],$user['username'],$notes,4,$amount,$ismoney,2,1);
		write_log("�޸Ļ�ԱuidΪ".$_POST['company_uid']."�ײ���Ϣ", $_SESSION['admin_name'],3);
		adminmsg('�����ɹ���',2,$link);
		}
		else
		{
		adminmsg('����ʧ�ܣ�',1);
		}
	}
	else
	{
	adminmsg('��ѡ������ײͣ�',1);
	}	
}
elseif($act == 'edit_setmeal_save')
{
	check_token();
    check_permissions($_SESSION['admin_purview'],"com_user_edit");
	$setsqlarr['jobs_ordinary']=$_POST['jobs_ordinary'];
	$setsqlarr['download_resume_ordinary']=$_POST['download_resume_ordinary'];
	$setsqlarr['download_resume_senior']=$_POST['download_resume_senior'];
	$setsqlarr['interview_ordinary']=$_POST['interview_ordinary'];
	$setsqlarr['interview_senior']=$_POST['interview_senior'];
	$setsqlarr['talent_pool']=$_POST['talent_pool'];
	$setsqlarr['recommend_num']=intval($_POST['recommend_num']);
	$setsqlarr['recommend_days']=intval($_POST['recommend_days']);
	$setsqlarr['stick_num']=intval($_POST['stick_num']);
	$setsqlarr['stick_days']=intval($_POST['stick_days']);
	$setsqlarr['emergency_num']=intval($_POST['emergency_num']);
	$setsqlarr['emergency_days']=intval($_POST['emergency_days']);
	$setsqlarr['highlight_num']=intval($_POST['highlight_num']);
	$setsqlarr['highlight_days']=intval($_POST['highlight_days']);
	$setsqlarr['change_templates']=intval($_POST['change_templates']);
	$setsqlarr['jobsfair_num']=intval($_POST['jobsfair_num']);
	$setsqlarr['map_open']=intval($_POST['map_open']);

	$setsqlarr['added']=$_POST['added'];
	if ($_POST['setendtime']<>"")
	{
		$setendtime=convert_datefm($_POST['setendtime'],2);
		if ($setendtime=='')
		{
		adminmsg('���ڸ�ʽ����',0);	
		}
		else
		{
		$setsqlarr['endtime']=$setendtime;
		}
	}
	else
	{
	$setsqlarr['endtime']=0;
	}
	if ($_POST['days']<>"")
	{
			if (intval($_POST['days'])<>0)
			{
				$oldendtime=intval($_POST['oldendtime']);
				$setsqlarr['endtime']=strtotime("".intval($_POST['days'])." days",$oldendtime==0?time():$oldendtime);
			}
			if (intval($_POST['days'])=="0")
			{
				$setsqlarr['endtime']=0;
			}
	}
	$setmealtime=$setsqlarr['endtime'];
	$company_uid=intval($_POST['company_uid']);
	if ($company_uid)
	{
			$setmeal=get_user_setmeal($company_uid);
			if (!$db->updatetable(table('members_setmeal'),$setsqlarr," uid=".$company_uid."")) adminmsg('�޸ĳ���',0);
		//��Ա�ײͱ����¼������Ա��̨�޸Ļ�Ա�ײͣ��޸Ļ�Ա��3��ʾ������Ա��̨�޸�
			$setmeal['endtime']=date('Y-m-d',$setmeal['endtime']);
			$setsqlarr['endtime']=date('Y-m-d',$setsqlarr['endtime']);
			$setsqlarr['log_amount']=round($_POST['log_amount']);
			$notes=edit_setmeal_notes($setsqlarr,$setmeal);
			if($notes){
				$user=get_user($_POST['company_uid']);
				$ismoney=round($_POST['log_amount'])?2:1;
				write_setmeallog($company_uid,$user['username'],$notes,3,$setsqlarr['log_amount'],$ismoney,2,1);
			}

			if ($setsqlarr['endtime']<>"")
			{
				$setmeal_deadline['setmeal_deadline']=$setmealtime;
				if (!$db->updatetable(table('jobs'),$setmeal_deadline," uid='{$company_uid}' AND add_mode='2' "))adminmsg('�޸ĳ���',0);
				if (!$db->updatetable(table('jobs_tmp'),$setmeal_deadline," uid='{$company_uid}' AND add_mode='2' "))adminmsg('�޸ĳ���',0);
				distribution_jobs_uid($company_uid);
			}
			//�޸ĸû�Ա������
			$setsmsqlarr['sms_num']=intval($_POST['sms_num']);
			$db->updatetable(table('members'),$setsmsqlarr," uid='{$company_uid}' ");
	}
	write_log("�༭��ԱuidΪ".$company_uid."�ײ���Ϣ", $_SESSION['admin_name'],3);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg('�����ɹ���',2,$link);
}
elseif($act == 'userpass_edit')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_edit");
	if (strlen(trim($_POST['password']))<6) adminmsg('���������Ϊ6λ���ϣ�',1);
	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');
	$user_info=get_user_inusername($_POST['username']);
	$pwd_hash=$user_info['pwd_hash'];
	$md5password=md5(md5(trim($_POST['password'])).$pwd_hash.$QS_pwdhash);	
	if ($db->query( "UPDATE ".table('members')." SET password = '$md5password'  WHERE uid='".$user_info['uid']."'"))
	{
			if(defined('UC_API'))
			{
			include_once(QISHI_ROOT_PATH.'uc_client/client.php');
			uc_user_edit($user_info['username'],trim($_POST['password']),trim($_POST['password']),"",1);
			}
	write_log("�޸Ļ�ԱuidΪ".$user_info['uid']."����", $_SESSION['admin_name'],3);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg('�����ɹ���',2,$link);
	}
	else
	{
	adminmsg('����ʧ�ܣ�',1);
	}
}
elseif($act == 'userstatus_edit')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_edit");
	if(set_user_status(intval($_POST['status']),intval($_POST['userstatus_uid'])))
	{
		write_log("�޸Ļ�ԱuidΪ".intval($_POST['userstatus_uid'])."��״̬", $_SESSION['admin_name'],3);
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $_POST['url'];
		adminmsg('�����ɹ���',2,$link);
	}
	else
	{
	adminmsg('����ʧ�ܣ�',1);
	}
}
elseif($act == 'comment_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"com_comment");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY c.id DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	$audit=isset($_GET['audit'])?intval($_GET['audit']):"";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql=" WHERE m.username  like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE c.content  like '%{$key}%'";
		elseif ($key_type===3)$wheresql=" WHERE c.uid ='".intval($key)."'";
		elseif ($key_type===5)$wheresql=" WHERE c.company_id ='".intval($key)."'";
		$oederbysql="";
	}else{
		if ($audit>0)
		{
			$wheresql=" WHERE c.audit ='".intval($audit)."'";
		}
	}

	$joinsql=" LEFT JOIN ".table('members')." AS m ON c.uid=m.uid LEFT JOIN ".table('company_profile')." AS j  ON c.company_id=j.id ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('comment')." AS c ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$clist = get_comment($offset,$perpage,"SELECT c.*,j.id as company_id,j.companyname,m.uid,m.username FROM ".table('comment')." AS c ".$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��ҵ����");
	$smarty->assign('clist',$clist);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_comment_list.htm');
}
elseif($act == 'comment_audit')
{
	check_permissions($_SESSION['admin_purview'],"comment_audit");
	check_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ��ְλ���ۣ�",1);
	$audit=intval($_POST['audit']);
	$num=comment_audit($id,$audit);
	if ($num>0){adminmsg("��˳ɹ��������".$num."��",2);}else{adminmsg("��˳ɹ�!��Ӱ��{$num}��",0);}
}
 elseif($act == 'comment_del')
{	
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_comment");
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û����Ϣ��",1);
	$n=comment_del($id);
	if ($n>0)
	{
	adminmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
 elseif($act == 'del_auditreason')
{	
	//check_token();
	check_permissions($_SESSION['admin_purview'],"jobs_audit");//�õ���ְλ��˵�Ȩ��
	$id =!empty($_REQUEST['a_id'])?$_REQUEST['a_id']:adminmsg("��û��ѡ����־��",1);
	$n=reasonaudit_del($id);
	if ($n>0)
	{
	adminmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'company_img')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"img_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY i.id DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if ($key_type===1)$wheresql=" WHERE c.companyname like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE c.id ='".intval($key)."'";
		elseif ($key_type===3)$wheresql=" WHERE i.id ='".intval($key)."'";
		elseif ($key_type===4)$wheresql=" WHERE i.title like '{$key}%'";
		$oederbysql="";
	}
	$_GET['audit']<>""? $wheresqlarr['i.audit']=intval($_GET['audit']):'';
	if (is_array($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
	if (!empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE i.addtime> ".$settr:$wheresql." AND i.addtime> ".$settr;
	}
	$joinsql=" LEFT JOIN ".table('company_profile')." AS c ON i.company_id=c.id  ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('company_img')." AS i".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$clist = get_company_img($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��ҵͼƬ");
	$smarty->assign('clist',$clist);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_company_img.htm');
}
elseif($act == 'del_company_img')
{
	check_permissions($_SESSION['admin_purview'],"img_del");
	check_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ��ͼƬ��",1);
	$num=del_company_img($id);
	if ($num>0){adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);}else{adminmsg("ɾ��ʧ�ܣ�",0);}
}
elseif($act == 'edit_img_audit')
{
	check_permissions($_SESSION['admin_purview'],"img_audit");
	check_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ��ͼƬ��",1);
	$audit=intval($_POST['audit']);
	$pms_notice=intval($_POST['pms_notice']);
	$reason=trim($_POST['reason']);
	$num=edit_img_audit($id,$audit,$reason,$pms_notice);
	if ($num>0){adminmsg("��˳ɹ��������".$num."��",2);}else{adminmsg("��˳ɹ�!��Ӱ��{$num}��",0);}
}
elseif($act == 'company_news')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"news_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY n.id DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if ($key_type===1)$wheresql=" WHERE c.companyname like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE c.id ='".intval($key)."'";
		elseif ($key_type===3)$wheresql=" WHERE n.id ='".intval($key)."'";
		elseif ($key_type===4)$wheresql=" WHERE n.title like '{$key}%'";
		$oederbysql="";
	}
	$_GET['audit']<>""? $wheresqlarr['n.audit']=intval($_GET['audit']):'';
	if (is_array($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
	if (!empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE n.addtime> ".$settr:$wheresql." AND n.addtime> ".$settr;
	}
	$joinsql=" LEFT JOIN ".table('company_profile')." AS c ON n.company_id=c.id  ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('company_news')." AS n".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$clist = get_company_news($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��ҵ����");
	$smarty->assign('clist',$clist);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_company_news.htm');
}
elseif($act == 'edit_news_audit')
{
	check_permissions($_SESSION['admin_purview'],"news_audit");
	check_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ�����ţ�",1);
	$audit=intval($_POST['audit']);
	$pms_notice=intval($_POST['pms_notice']);
	$reason=trim($_POST['reason']);
	$num=edit_news_audit($id,$audit,$reason,$pms_notice);
	if ($num>0){adminmsg("��˳ɹ��������".$num."��",2);}else{adminmsg("��˳ɹ�!��Ӱ��{$num}��",0);}
}
elseif($act == 'del_company_news')
{
	check_permissions($_SESSION['admin_purview'],"news_del");
	check_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ�����ţ�",1);
	$num=del_company_news($id);
	if ($num>0){adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);}else{adminmsg("ɾ��ʧ�ܣ�",0);}
}
elseif($act == 'edit_company_news')
{
	check_permissions($_SESSION['admin_purview'],"news_edit");
	get_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ�����ţ�",1);
	$news=get_news_one($id);
	$smarty->assign('news',$news);
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('pageheader',"��ҵ����");
	$smarty->display('company/admin_company_news_edit.htm');
}
elseif($act == 'company_news_save')
{
	check_token();
    check_permissions($_SESSION['admin_purview'],"news_edit");
	$id=intval($_POST['id']);
	$setsqlarr['title']=$_POST['title']?trim($_POST['title']):adminmsg("��û����д���ű��⣡",1);
	$setsqlarr['content']=$_POST['content']?trim($_POST['content']):adminmsg("��û����д�������ݣ�",1);
	$setsqlarr['click']=intval($_POST['click']);
	$setsqlarr['order']=intval($_POST['order']);
	$setsqlarr['audit']=intval($_POST['audit']);
	$setsqlarr['addtime']=time();
	$link[1]['text'] = "���������б�";
	$link[1]['href'] = '?act=company_news';
	$link[0]['text'] = '�鿴�޸Ľ��';
	$link[0]['href'] = '?act=edit_company_news&id='.$id;
	!$db->updatetable(table('company_news'),$setsqlarr,' id='.$id.' ')?adminmsg("�޸���ҵ����ʧ�ܣ�",1,$link):adminmsg("�޸���ҵ���ųɹ���",2,$link);

}
 elseif($act == 'management')
{

	$QS_cookiedomain = get_cookiedomain();
 	$id=intval($_GET['id']);
	$u=get_user($id);

	if (!empty($u))
	{
		unset($_SESSION['uid']);
		unset($_SESSION['username']);
		unset($_SESSION['utype']);
		unset($_SESSION['uqqid']);
		setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[username]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[password]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[subsite_id]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);

		unset($_SESSION['activate_username']);
		unset($_SESSION['activate_email']);


		
		$_SESSION['uid']=$u['uid'];
		$_SESSION['username']=$u['username'];
		$_SESSION['utype']=$u['utype'];
		$_SESSION['uqqid']="1";
		$_SESSION['no_self']="1";
		$_SESSION['subsite_id']=$u['subsite_id'];
		setcookie('QS[uid]',$u['uid'],0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[username]',$u['username'],0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[password]',$u['password'],0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[utype]',$u['utype'], 0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[subsite_id]',$u['subsite_id'], 0,$QS_cookiepath,$QS_cookiedomain);

		header("Location:".get_member_url($u['utype']));
	}else{
		adminmsg('�û�������',1);
	}
} 
elseif($act == 'consultant')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"consultant_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY id DESC ";
	
	$total_sql="SELECT COUNT(*) AS num FROM ".table('consultant').$oederbysql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$clist = get_consultant($offset,$perpage,$oederbysql);
	$smarty->assign('pageheader',"���ʹ���");
	$smarty->assign('clist',$clist);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_consultant_list.htm');
}
//����  ����
elseif($act == 'consultant_manage')
{
	//�õ�����id 
	$id = intval($_GET['id']);
	$sql = "select * from ".table('consultant')." where id = {$id}";
	$consultant = $db->getone($sql);
	if(empty($consultant)){
		adminmsg('���ʶ�ʧ',1);
	}
	//��ҳ
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql = " where consultant ={$id}";
	$total_sql="select count(*) as num from ".table('members')." where consultant ={$id}";
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$members = get_member_manage($offset,$perpage,$wheresql);
	$smarty->assign('pageheader',"���ù���");
	$smarty->assign('consultant',$consultant);
	$smarty->assign('members',$members);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_consultant_manage.htm');
}
//���� ����  ���� ��ť 
elseif($act == 'resetting')
{
	//�����ͷ������õ���ͬ�Ļ�Աuid�������õ���uid�Ǹ�����memberstuid���������õ�����һ��idֵmembersids��
	$membersid =$_GET['uid'];
	$memberstuid =$_REQUEST['tuid'];
	if(empty($membersid) && empty($memberstuid)){
		adminmsg("���÷�������",0);
	}
	$members_id = empty($membersid)?$memberstuid:$membersid;
	$member_del_id='';
	if(is_array($members_id)){
		foreach ($members_id as  $value) {
			if(empty($member_del_id)){
				$member_del_id = $value;
			}else{
				$member_del_id = $member_del_id.','.$value;
			}
		}
	}else{
		$member_del_id = $members_id;
	}
	//����Щ��Ա�������ù���
	if($db->updatetable(table('members'),array('consultant'=>0)," uid in ({$member_del_id}) ")){
		adminmsg('���óɹ�!',2);
	}else{
		adminmsg('���ù���ʧ��!',0);
	}
	

}
elseif($act == 'consultant_add')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"consultant_add");
	$smarty->assign('pageheader',"���ʹ���");
	$smarty->display('company/admin_consultant_add.htm');
}
elseif($act == 'consultant_add_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"consultant_add");
	$setsqlarr['name'] = !empty($_POST['name']) ? trim($_POST['name']):adminmsg('����д������',1);
	$setsqlarr['qq'] = !empty($_POST['qq']) ? trim($_POST['qq']):adminmsg('����дQQ��',1);	
	
	!$_FILES['pic']['name']?adminmsg('���ϴ���Ƭ��',1):"";
	$upload_image_dir="../data/".$_CFG['updir_images']."/".date("Y/m/d/");
	make_dir($upload_image_dir);
	require_once(dirname(__FILE__).'/include/upload.php');
	$setsqlarr['pic']=_asUpFiles($upload_image_dir, "pic","2048",'gif/jpg/bmp/png',true);
	$setsqlarr['pic']=date("Y/m/d/").$setsqlarr['pic'];

	$insert_id=$db->inserttable(table('consultant'),$setsqlarr,true);
	write_log("��ӹ���".$setsqlarr['name'], $_SESSION['admin_name'],3);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = "?act=consultant";
	$link[1]['text'] = "�������";
	$link[1]['href'] = "?act=consultant_add";
	adminmsg('��ӳɹ���',2,$link);
}
elseif($act == 'consultant_edit')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"consultant_edit");
	$id=intval($_GET['id']);
	if(!$id){
		adminmsg("��ѡ����ʣ�",1);
	}
	$consultant = get_consultant_one($id);
	$smarty->assign('consultant',$consultant);
	$smarty->assign('pageheader',"���ʹ���");
	$smarty->display('company/admin_consultant_edit.htm');
}
elseif($act == 'consultant_edit_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"consultant_edit");
	$id=intval($_POST['id']);
	if(!$id){
		adminmsg("��ѡ����ʣ�",1);
	}
	$consultant = get_consultant_one($id);
	$setsqlarr['name'] = !empty($_POST['name']) ? trim($_POST['name']):adminmsg('����д������',1);
	$setsqlarr['qq'] = !empty($_POST['qq']) ? trim($_POST['qq']):adminmsg('����дQQ��',1);	
	if($_FILES['pic']['name']){
		$upload_image_dir="../data/".$_CFG['updir_images']."/".date("Y/m/d/");
		make_dir($upload_image_dir);
		require_once(dirname(__FILE__).'/include/upload.php');
		$setsqlarr['pic']=_asUpFiles($upload_image_dir, "pic","2048",'gif/jpg/bmp/png',true);
		$setsqlarr['pic']=date("Y/m/d/").$setsqlarr['pic'];
		@unlink("../data/".$_CFG['updir_images']."/".$consultant['pic']);
	}
	
	$db->updatetable(table('consultant'),$setsqlarr," id={$id} ");
	write_log("�޸Ĺ���idΪ".$id."�Ĺ�����Ϣ", $_SESSION['admin_name'],3);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = "?act=consultant";
	$link[1]['text'] = "�鿴�޸Ľ��";
	$link[1]['href'] = "?act=consultant_edit&id={$id}";
	adminmsg('�޸ĳɹ���',2,$link);
}
elseif($act == "consultant_del"){
	check_permissions($_SESSION['admin_purview'],"consultant_del");
	$id=intval($_GET['id']);
	if(!$id){
		adminmsg("��ѡ����ʣ�",1);
	}
	del_consultant($id);
	adminmsg("ɾ���ɹ���",2);
}elseif ($act == 'reward_check_list') {
    //�˲���������б�;
    get_token();
    check_permissions($_SESSION['admin_purview'], "reward_check_list");
    require_once(QISHI_ROOT_PATH . 'include/page.class.php');
    $wheresql = " WHERE 1=1 ";
    $oederbysql = " order BY addtime DESC ";



    if ($_GET['status'] != "") {
        //δ����
        $consultant = intval($_GET['status']);
        $wheresql .= " AND  m.status= ".$consultant;
    }


	//����Ȩ��
	$assign=get_permissions($_SESSION['admin_purview'], "reward_check_list_assign");
	if(!$assign){
		$wheresql .= " AND m.admin_id = ".$_SESSION["admin_id"];
	}

    $total_sql = "SELECT COUNT(*) AS num FROM " . table('jobs_reward_clue') . " as m " .  $wheresql;
    $total_val = $db->get_total($total_sql);
    $page = new page(array('total' => $total_val, 'perpage' => $perpage, 'getarray' => $_GET));
    $currenpage = $page->nowindex;
    $offset = ($currenpage - 1) * $perpage;
    $member = get_clue_check_list($offset, $perpage,   $wheresql . $oederbysql);

	$admin=$db->getall("select * from ".table("admin"). " where admin_id!=1 order by admin_id");
	$smarty->assign('admin', $admin);

	$smarty->assign('pageheader', "�˲�����");
    $smarty->assign('member', $member);
    $smarty->assign('page', $page->show(3));
	$smarty->assign("assign",$assign);
    $smarty->display('company/admin_company_user_clue_list.htm');
} elseif ($act == 'clue_detail') {
    get_token();



	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');

	$id = !empty($_REQUEST['cid']) ? $_REQUEST['cid'] : adminmsg("��������", 1);
    $clue = get_clue_one($id);
    $company_profile = get_company_one_id($clue["company_id"]);
    $clue_log=get_clue_log_list($id);
    $promotion=get_promotion_info($clue["job_id"],5);
    if($promotion){
        $json=str_replace('&quot;', '"', trim($promotion["cp_json"]));
        $json=json_decode($json);

        $promotion=array_merge($promotion,(array)$json);
    }

    $member=get_member_info($clue["uid"]);

 	if($clue["member_id"]){
		$resume["uid"]=$clue["member_id"];
		$resume["list"]=get_resume_uid($clue["member_id"]);
		$smarty->assign('resume', $resume);

		//dump($resume);

	}

	$job_url=url_rewrite('QS_jobsshow', array('id' => $clue['job_id']));
    $smarty->assign('job_url', $job_url);
    $smarty->assign('clue', $clue);
    $smarty->assign('company_profile', $company_profile);
    $smarty->assign('promotion', $promotion);
    $smarty->assign('member', $member);

	$smarty->assign("genvhr",get_category_info("Genv_hr"));
	$smarty->assign("genvpro",get_category_info("Genv_prodive"));
	$smarty->assign("genvjob",get_category_info("Genv_job"));

    $smarty->assign('clue_log', $clue_log);
    $smarty->assign('url', $_SERVER["HTTP_REFERER"]);
    $smarty->assign('pageheader', "�˲ŷ��ʼ�¼");

	$clue_detail=get_clue_detail($promotion);



    $smarty->assign('clue_detail', $clue_detail);

	$subsite=get_all_subsite();
    $smarty->assign('subsite', $subsite);

    $smarty->display('company/admin_company_clue_detail.htm');


}elseif ($act == 'clue_log_save') {
    check_token();
    $id = intval($_POST['id']);
    if (!$id) {
        adminmsg("��������", 1);
    }
    $setsqlarr["cid"] = $id;
    $setsqlarr["notes"] = !empty($_POST['notes']) ? trim($_POST['notes']) : adminmsg('����д��־���ݣ�', 1);
    $setsqlarr['addtime'] = time();
    $setsqlarr['admin_name'] = $_SESSION["admin_name"];
    $setsqlarr['nexttime'] = $_POST["nexttime"];
    $setsqlarr['result'] = !empty($_POST['result']) ? trim($_POST['result']) : adminmsg('���ʽ�����Ͳ���Ϊ�գ�', 1);
	$db->inserttable(table('jobs_reward_clue_log'), $setsqlarr );


	write_log("����˲���ϵ��־" . $id . " ", $_SESSION['admin_name'], 3);


	if( $setsqlarr['result']=="������Ͷ֪ͨ�鿴"){
		Ggven::log("���Գɹ�,��ʼ����");
		success_act_view($id,1);
	}

    if( $setsqlarr['result']=="�ѳɹ�����(�۳�����Ӷ��)"){
		Ggven::log("���Գɹ�,��ʼ����");
		success_act($id,1);
	}

	if( $setsqlarr['result']=="�ѳɹ���ְ(�۳���ְӶ��)"){
		success_act($id,2);
	}

	update_clue_status($id);
    $link[0]['text'] = "�鿴�޸Ľ��";
    $link[0]['href'] = "?act=clue_detail&cid={$id}";
    adminmsg('��ӳɹ���', 2, $link);
}elseif ($act == 'reward_perform') {
    check_token();


    if (trim($_POST['reduce_money'])) {
        $amount = intval($_POST['amount']);
        $company_uid=$_POST["company_uid"];
        $reason=$_POST["reason"];
        $clue_id=$_POST["clue_id"];

        if(!balance_deal($company_uid,2,$amount)){
            adminmsg("����ʧ��", 2);
        }
        $notes="���۷�{$amount},�۷�ԭ��".$reason;


        $reason = trim($_POST['reason']);
        $order['oid'] = "KK-" . date('ymd', time()) . "-" . date('His', time());//������
        $order_id = admin_add_order($_SESSION['uid'], 8, $order['oid'], $amount, "���۷�", $notes, $timestamp, 0, '', 1);


        $setsqlarr["cid"] = $clue_id;
        $setsqlarr["notes"] = $notes;
        $setsqlarr['addtime'] = time();
        $setsqlarr['admin_name'] = $_SESSION["admin_name"];
        $setsqlarr['nexttime'] = "";
        $db->inserttable(table('jobs_reward_clue_log'), $setsqlarr );


        !$order_id? adminmsg("�۷�ʧ�ܣ�", 0) : adminmsg("�۷ѳɹ���", 2);
    }elseif (trim($_POST['add_money'])) {
        $amount = intval($_POST['amount1']);
        $member_uid=$_POST["member_uid"];
        $reason=$_POST["reason1"];
        $clue_id=$_POST["clue_id"];

        if(!balance_deal_person($member_uid,1,$amount)){
            adminmsg("����ʧ��", 2);
        }
        $notes="�������{$amount},����ԭ��".$reason;

        $reason = trim($_POST['reason']);
        $order['oid'] = "KK-" . date('ymd', time()) . "-" . date('His', time());//������
        $order_id = admin_add_order($_SESSION['uid'], 7, $order['oid'], $amount, "�������", $notes, $timestamp, 0, '', 1);

        $setsqlarr["cid"] = $clue_id;
        $setsqlarr["notes"] = "�û�id:".$member_uid.";".$notes;
        $setsqlarr['addtime'] = time();
        $setsqlarr['admin_name'] = $_SESSION["admin_name"];
        $setsqlarr['nexttime'] = "";
        $db->inserttable(table('jobs_reward_clue_log'), $setsqlarr );

        !$order_id? adminmsg("����ʧ�ܣ�", 0) : adminmsg("�����ɹ���", 2);
    }
}elseif ($act == 'company_points') {
    //����б����
    get_token();
    check_permissions($_SESSION['admin_purview'], "company_points");

    $list = get_points_plan();
    $smarty->assign('pageheader', "��«�����ͷ���");
    $smarty->assign('list', $list);


    $smarty->display('company/admin_company_points.htm');
}elseif ($act == 'points_plan_save') {
    //����б����
    get_token();
    check_permissions($_SESSION['admin_purview'], "company_points");

    $setsqlarr['name'] = !empty($_POST['name']) ? trim($_POST['name']):adminmsg('����д���ƣ�',1);
    $setsqlarr['money'] = !empty($_POST['money']) ? trim($_POST['money']):adminmsg('����Ϊ�գ�',1);
    $setsqlarr['free_points'] = !empty($_POST['free_points']) ? trim($_POST['free_points']):adminmsg('���ͺ�«�Ҳ���Ϊ�գ�',1);


    $insert_id=$db->inserttable(table('company_points'),$setsqlarr,true);
    write_log("��Ӻ�«�����ͷ���".$setsqlarr['name'], $_SESSION['admin_name'],3);
    $link[0]['text'] = "�����б�";
    $link[0]['href'] = "?act=company_points";

    adminmsg('��ӳɹ���',2,$link);

    $smarty->display('company/admin_company_points.htm');
}elseif ($act == 'points_plan_del') {
    //����б����
    get_token();
    check_permissions($_SESSION['admin_purview'], "company_points");
    $id = !empty($_REQUEST['id']) ? trim($_REQUEST['id']):adminmsg('id����Ϊ�գ�',1);

    if (!$db->query("Delete from ".table('company_points')." WHERE id IN (".$id.")")){
        adminmsg("ɾ��ʧ�ܣ�",0);
    } else{
        adminmsg("ɾ���ɹ���",0);

    }


}
elseif($act == 'clue_members_add_save')
{
	check_token();

	check_permissions($_SESSION['admin_purview'],"per_user_add");
	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');
 	if (strlen(trim($_POST['password']))<6) adminmsg('�������Ϊ6λ���ϣ�',1);
	$cid=$_POST['cid'];

	$sql['password'] = !empty($_POST['password']) ? trim($_POST['password']):adminmsg('����д���룡',1);
	$sql['mobile'] = !empty($_POST['mobile']) ? trim($_POST['mobile']):adminmsg('����д�ֻ��ţ�',1);
	if ($sql['password']<>trim($_POST['password1']))
	{
		adminmsg('������������벻��ͬ��',1);
	}
	$sql['utype'] = !empty($_POST['member_type']) ? intval($_POST['member_type']):adminmsg('��û��ѡ��ע�����ͣ�',1);
	if (empty($_POST['email']) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$_POST['email']))
	{
		adminmsg('���������ʽ����',1);
	}
	$sql['email']= trim($_POST['email']);
	$sql['username'] = $sql['email'];

	if (get_user_inusername($sql['username']))
	{
		adminmsg('���û����Ѿ���ʹ�ã�',1);
	}
	if (get_user_inemail($sql['email']))
	{
		adminmsg('�� Email �Ѿ���ע�ᣡ',1);
	}
	if (get_user_inmobile($sql['mobile']))
	{
		adminmsg('�� �ֻ��� �Ѿ���ע�ᣡ',1);
	}
	if(defined('UC_API'))
	{
		include_once(QISHI_ROOT_PATH.'uc_client/client.php');
		if (uc_user_checkname($sql['username'])<>"1")
		{
			adminmsg('���û����Ѿ���ʹ�û����û����Ƿ���',1);
			exit();
		}
		elseif (uc_user_checkemail($sql['email'])<>"1")
		{
			adminmsg('�� Email�Ѿ���ʹ�û��߷Ƿ���',1);
			exit();
		}
		else
		{
			uc_user_register($sql['username'],$sql['password'],$sql['email']);
		}
	}
	$sql['pwd_hash'] = randstr();
	$sql['password'] = md5(md5($sql['password']).$sql['pwd_hash'].$QS_pwdhash);
	$sql['reg_time']=time();
	$sql['subsite_id']=$_POST["subsite_id"];
	$sql['reg_ip']=$online_ip;
	$insert_id=$db->inserttable(table('members'),$sql,true);

 	$db->updatetable(table('jobs_reward_clue'),array("member_id"=>$insert_id,"link_telephone"=>$sql['mobile'])," id =".$cid);
	if ($insert_id)
	{
		$db->query("INSERT INTO ".table('members_points')." (uid) VALUES ('{$insert_id}')");
		//��д����Ա��־
		write_log("���idΪ".$insert_id."�ĸ��˻�Ա", $_SESSION['admin_name'],3);
		write_memberslog($insert_id,1,1000,$sql['username'],"����Ա�ں�̨������Ա");



		$setsqlarr["cid"] = $cid;
		$setsqlarr["notes"] = "����Ա�����û���Ϣ";
		$setsqlarr['addtime'] = time();
		$setsqlarr['admin_name'] = $_SESSION["admin_name"];


		$db->inserttable(table('jobs_reward_clue_log'), $setsqlarr );
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = "?act=clue_detail&cid=".$cid;
		adminmsg('��ӳɹ���',2,$link);
	}
}elseif ($act == 'clue_action') {
	check_token();

	check_permissions($_SESSION['admin_purview'], "reward_check_list_assign");
	$id = $_POST['tuid'];
	if (!is_array($id)){
		adminmsg('��ѡ��������',1);
	}
	$sqlin=implode(",",$id);
	if (trim($_POST['assign'])) {

		$setsqlarr['admin_id']= !empty($_POST['admin_id']||$_POST['admin_id']!=0) ? trim($_POST['admin_id']):adminmsg('��ѡ�����Ա��',1);

		$setsqlarr['admin_name'] =  $_POST['admin_name'] ;

		$db->updatetable(table('jobs_reward_clue'), $setsqlarr," id IN ({$sqlin})");

 		adminmsg("����ɹ���", 2);

	}elseif (trim($_POST['delete'])) {

		if ($db->query("Delete from ".table('jobs_reward_clue')." WHERE id IN ({$sqlin})")) {
			$num = $db->affected_rows();
			if ($num > 0) {
				adminmsg("ɾ���ɹ�����ɾ��" . $num . "��", 2);
			}
		}else
		{
			adminmsg("ɾ��ʧ�ܣ�",0);
		}
	}
}
elseif ($act == 're_money') {
	check_token();
	$id = intval($_REQUEST['id']);
	if (!$id) {
		adminmsg("��������", 1);
	}
	$log=$db->getone("select * from ".table("jobs_reward_clue_log")." where id=".$id." and is_success=0");
	if(!$log){
		adminmsg("��������", 1);
	}
	$cid=$log["cid"];

	$clue = get_clue_one($cid);
	$promotion = get_promotion_info($clue["job_id"], 5);
	if (!$promotion) {
		adminmsg("��Ƹ��Ϣ��ʧ", 1);
	}
	$json = json_array($promotion["cp_json"]);

	$type=$log["cate"];
	if($log["role"]==1){
		qiye_deal($clue, $json, $promotion, $type);
	}elseif($log["role"]==2){
		pro_deal($clue, $json, $promotion, $type);

	}elseif($log["role"]==3){
		jober_deal($clue, $json, $promotion, $type);
	}
	$arr=array();
	$arr["is_success"]=1;
	$wheresql = " id='" . $log["id"] . "'";
    $db->updatetable(table('jobs_reward_clue_log'), $arr, $wheresql);

	adminmsg('����ɹ���', 2);
}
elseif ($act == 'test') {
	success_act(2,1);
}





//��ȡѡ����Ϣ
function get_category_info($value ){

	$rs = \ORM::for_table(table('category'))->where_equal("c_alias", $value)->find_array();
	if($rs){
		return $rs;
	}
	return array();


}





?>